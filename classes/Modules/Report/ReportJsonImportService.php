<?php

namespace Xentral\Modules\Report;

use Exception;
use Xentral\Components\Http\File\FileUpload;
use Xentral\Modules\Report\Data\ReportColumn;
use Xentral\Modules\Report\Data\ReportColumnCollection;
use Xentral\Modules\Report\Data\ReportData;
use Xentral\Modules\Report\Data\ReportParameter;
use Xentral\Modules\Report\Data\ReportParameterCollection;
use Xentral\Modules\Report\Exception\InvalidArgumentException;
use Xentral\Modules\Report\Exception\JsonParseException;
use Xentral\Modules\Report\Exception\ReportReadonlyException;

final class ReportJsonImportService
{
    /** @var ReportGateway $gateway */
    private $gateway;

    /** @var ReportService $service */
    private $service;

    /**
     * @param ReportGateway $gateway
     * @param ReportService $service
     */
    public function __construct(ReportGateway $gateway, ReportService $service)
    {
        $this->gateway = $gateway;
        $this->service = $service;
    }

    /**
     * @param array $jsonData
     *
     * @return int database entry id
     */
    public function importReport($jsonData)
    {
        if (!is_array($jsonData)) {
            throw new JsonParseException('Json data must be associative array.');
        }
        if (!isset($jsonData['name']) || $jsonData['name'] === '') {
            throw new JsonParseException('Name of the report is required.');
        }

        $existing = $this->gateway->findReportByName($jsonData['name']);
        $existingShare = [];
        if ($existing !== null) {
            $jsonData['id'] = $existing->getId();
            $report = $this->parseReport($jsonData);
            $columns = $this->gateway->getColumnsByReportId($existing->getId());
            $params = $this->gateway->getParametersByReportId($existing->getId());
            $areColumnsEqual = $this->areArraysEqual(
                $columns->toArray(),
                $report->getColumns() === null ? [] : $report->getColumns()
                    ->toArray()
            );
            $areParamsEqual = $this->areArraysEqual(
                $params->toArray(),
                $report->getParameters() === null ? [] : $report->getParameters()
                    ->toArray()
            );
            if ($areColumnsEqual && $areParamsEqual) {
                $report->setColumns(new ReportColumnCollection());
                $report->setParameters(new ReportParameterCollection());
            } else {
                foreach ($columns as $column) {
                    $this->service->deleteColumnById($column->getId());
                }
                foreach ($params as $param) {
                    $this->service->deleteParamById($param->getId());
                }
            }
            $existingShare = $this->gateway->findShareArrayByReportId($existing->getId());
        } else {
            $report = $this->parseReport($jsonData);
        }
        $insertReportId = $this->service->saveReport($report);

        if (array_key_exists('share', $jsonData) && $jsonData['share'] !== null) {
            $share = $this->parseShare($jsonData['share'], $existingShare);
            $share['id'] = 0;
            $share['report_id'] = $insertReportId;
            if ($existingShare !== null && count($existingShare) > 0) {
                $share['id'] = $existingShare['id'];
            }
            $this->service->saveShareArray($share);
        }

        return $insertReportId;
    }

    /**
     * @param array $jsonArray
     *
     * @return array errors
     */
    public function findJsonStructureErrors($jsonArray)
    {
        $errors = [];
        $required = ['name', 'sql_query', 'columns'];
        $requiredInColumn = ['key_name', 'title'];
        $requiredInParam = ['varname', 'default_value'];
        foreach ($required as $key) {
            if (!isset($jsonArray[$key]) || empty($jsonArray[$key])) {
                $errors[] = sprintf('Field %s is required.', $key);
            }
        }
        if (isset($jsonArray['columns']) && is_array($jsonArray['columns'])) {
            foreach ($jsonArray['columns'] as $column) {
                foreach ($requiredInColumn as $key) {
                    if (!isset($column[$key]) || $column[$key] === '') {
                        $errors[] = sprintf('Field column>%s is required.', $key);
                    }
                }
            }
        }
        if (isset($jsonArray['parameters']) && is_array($jsonArray['parameters'])) {
            foreach ($jsonArray['parameters'] as $param) {
                foreach ($requiredInParam as $key) {
                    if (!isset($param[$key]) || $param[$key] === '') {
                        $errors[] = sprintf('Field column>%s is required.', $key);
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * @param FileUpload $upload
     * @param int        $currentId
     *
     * @return int
     */
    public function importJsonUpload(FileUpload $upload, $currentId = 0)
    {
        if ($currentId > 0 && $this->gateway->isReportReadonly($currentId)) {
            throw new ReportReadonlyException('Cannot overwrite report by JSON import');
        }
        if ($upload === null || !$upload->isValid() || $upload->getClientMimeType() !== 'application/json') {
            throw new InvalidArgumentException('uploaded file is invalid');
        }
        if ($upload->hasError()) {
            throw new InvalidArgumentException($upload->getErrorMessage(), $upload->getErrorCode());
        }
        $stringContent = $upload->getContent();
        $dataArray = json_decode($stringContent, true);
        if (empty($dataArray)) {
            throw new JsonParseException('Error parsing JSON structure');
        }

        if (isset($dataArray['name'])) {
            $dataArray['name'] = $this->service->generateIncrementedReportName($dataArray['name'], $currentId);
        }

        return $this->importReport($dataArray);
    }

    /**
     * @param array $reportData
     *
     * @throws JsonParseException
     *
     * @return ReportData
     */
    private function parseReport($reportData)
    {
        try {
            $report = ReportData::fromFormData($reportData);
        } catch (Exception $e) {
            throw new JsonParseException($e->getMessage(), $e->getCode(), $e);
        }
        if (isset($reportData['readonly']) && $reportData['readonly'] === true) {
            $report->setReadonly(true);
        }

        $columnObjects = [];
        if (isset($reportData['columns']) && is_array($reportData['columns'])) {

            $sequence = 1;
            foreach ($reportData['columns'] as $columnData) {
                $parsedCol = $this->parseColumn($columnData);
                if ($parsedCol->getSequence() === 0) {
                    $parsedCol = new ReportColumn(
                        $parsedCol->getKey(),
                        $parsedCol->getTitle(),
                        $parsedCol->getWidth(),
                        $parsedCol->getAlignment(),
                        $parsedCol->isSumColumn(),
                        $parsedCol->getId(),
                        $sequence,
                        $parsedCol->getSorting(),
                        $parsedCol->getFormatType(),
                        $parsedCol->getFormatStatement()
                    );
                }
                $columnObjects[] = $parsedCol;
                $sequence++;
            }
        }
        $colCollection = new ReportColumnCollection($columnObjects);
        $report->setColumns($colCollection);

        $paramObjects = [];
        if (isset($reportData['parameters']) && is_array($reportData['parameters'])) {
            foreach ($reportData['parameters'] as $paramData) {
                $paramObjects[] = $this->parseParam($paramData);
            }
        }
        $paramCollection = new ReportParameterCollection($paramObjects);
        $report->setParameters($paramCollection);

        return $report;
    }

    /**
     * @param array $colData
     *
     * @throws JsonParseException
     *
     * @return ReportColumn
     */
    private function parseColumn($colData)
    {
        try {
            $column = ReportColumn::fromDbState($colData);
        } catch (Exception $e) {
            throw new JsonParseException($e->getMessage(), $e->getCode(), $e);
        }
        if ($column === null) {
            throw new JsonParseException('Error while parsing Column.');
        }

        return $column;
    }

    /**
     * @param array $paramData
     *
     * @throws JsonParseException
     *
     * @return ReportParameter
     */
    private function parseParam($paramData)
    {
        try {
            $parameter = ReportParameter::fromDbState($paramData);
        } catch (Exception $e) {
            throw new JsonParseException($e->getMessage(), $e->getCode(), $e);
        }
        if ($parameter === null) {
            throw new JsonParseException('Error while parsing Column.');
        }

        return $parameter;
    }

    /**
     * @param array $share
     * @param array $existingShare
     *
     * @return array
     */
    private function parseShare($share, $existingShare = [])
    {
        $data = [
            'chart_public'         => 0,
            'chart_axislabel'      => '',
            'chart_dateformat'     => 'Y-m-d H:i:s',
            'chart_type'           => 'line',
            'chart_x_column'       => '',
            'data_columns'         => '',
            'chart_group_column'   => '',
            'chart_interval_value' => 0,
            'chart_interval_mode'  => 'day',
            'file_public'          => 0,
            'file_pdf_enabled'     => 0,
            'file_csv_enabled'     => 0,
            'file_xls_enabled'     => 0,
            'menu_public'          => 0,
            'menu_doctype'         => '',
            'menu_label'           => '',
            'menu_format'          => 'csv',
            'tab_public'           => 0,
            'tab_module'           => '',
            'tab_action'           => '',
            'tab_label'            => '',
            'tab_position'         => 'nach_freifeld',
        ];

        $data = array_merge($data, $existingShare);
        $data = array_merge($data, $share);

        $boolkeys = [
            'chart_public',
            'file_public',
            'file_pdf_enabled',
            'file_csv_enabled',
            'file_xls_enabled',
            'menu_public',
            'tab_public',
        ];

        foreach ($boolkeys as $booleanKey) {
            if (array_key_exists($booleanKey, $share) && $share[$booleanKey] === true) {
                $data[$booleanKey] = 1;
            } else {
                $data[$booleanKey] = 0;
            }
        }

        return $data;
    }

    /**
     * @param array $collection
     * @param array $collectionToTest
     *
     * @return bool
     */
    private function areArraysEqual(array $collection, array $collectionToTest): bool
    {
        $collectionWithOutIds = array_map(
            static function (array $array) {
                unset($array['id']);
                if(isset($array['sum'])) {
                    $array['sum'] = (int)$array['sum'];
                }
                return $array;
            },
            $collection
        );
        $collectionToTestWithOutIds = array_map(
            static function (array $array) {
                unset($array['id']);
                if(isset($array['sum'])) {
                    $array['sum'] = (int)$array['sum'];
                }

                return $array;
            },
            $collectionToTest
        );

        return json_encode($collectionToTestWithOutIds) === json_encode($collectionWithOutIds);
    }
}
