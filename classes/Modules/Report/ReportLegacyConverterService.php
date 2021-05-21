<?php

declare(strict_types=1);

namespace Xentral\Modules\Report;

use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\DatabaseExceptionInterface;
use Xentral\Components\Util\StringUtil;
use Xentral\Modules\Report\Data\ReportColumn;
use Xentral\Modules\Report\Data\ReportColumnCollection;
use Xentral\Modules\Report\Data\ReportData;
use Xentral\Modules\Report\Data\ReportParameter;
use Xentral\Modules\Report\Data\ReportParameterCollection;

class ReportLegacyConverterService
{
    /** @var Database $db */
    private $db;

    /** @var ReportService $service */
    private $service;

    /**
     * @param Database      $db
     * @param ReportService $service
     */
    public function __construct(Database $db, ReportService $service)
    {
        $this->db = $db;
        $this->service = $service;
    }

    /**
     * @param int $legacyReportId
     *
     * @return int id of the new report
     */
    public function convertLegacyReport(int $legacyReportId): int
    {
        $sql = 'SELECT * FROM `berichte` AS `b` WHERE id=:idValue';
        $legacyData = $this->db->fetchRow($sql, ['idValue' => $legacyReportId]);
        if ($legacyData === null || empty($legacyData)) {
            return 0;
        }

        $reportArray = [
            'name'          => $this->service->generateIncrementedReportName($legacyData['name']),
            'description'   => $legacyData['beschreibung'],
            'remark'        => $legacyData['internebemerkung'],
            'sql_query'     => $legacyData['struktur'],
            'project'       => $legacyData['project'],
            'csv_delimiter' => ';',
            'csv_enclosure' => '"',
        ];
        $newReport = ReportData::fromFormData($reportArray);

        $parameters = $this->parseParameters($legacyData['variablen']);
        $newReport->setParameters($parameters);

        $preQuery = $this->service->resolveParameters($newReport);
        try {
            $queryResult = $this->db->fetchRow($preQuery);
        } catch (DatabaseExceptionInterface $e) {
            $queryResult = [];
        }

        $columns = $this->parseColumns(
            $legacyData['spaltennamen'],
            $legacyData['spaltenbreite'],
            $legacyData['spaltenausrichtung'],
            $legacyData['sumcols'],
            $queryResult
        );
        $newReport->setColumns($columns);

        $newReportId = $this->service->saveReport($newReport);

        $transferArray = $this->getTransferArray($legacyData, $newReportId);
        $this->service->saveTransferArray($transferArray);

        $shareArray = $this->getShareArray($legacyData, $newReportId);
        $this->service->saveShareArray($shareArray);

        return $newReportId;
    }

    /**
     * @param string $colNameString
     * @param string $colWidthString
     * @param string $colAlignString
     * @param string $sumColString
     * @param array  $sampleRow
     *
     * @return null|ReportColumnCollection
     */
    private function parseColumns(
        ?string $colNameString,
        ?string $colWidthString,
        ?string $colAlignString,
        ?string $sumColString,
        array $sampleRow
    ): ?ReportColumnCollection
    {
        $colNames = [];
        if ($colNameString !== null) {
            $colNames = explode(';',$colNameString);
        }
        $colWidths = [];
        if ($colWidthString !== null) {
            $colWidths = explode(';', $colWidthString);
        }
        $colAligns = [];
        if ($colAlignString !== null) {
            $colAligns = explode(';', $colAlignString);
        }
        $sumCols = [];
        if ($sumColString !== null) {
            $sumCols = explode(';', $sumColString);
        }

        $refcount = count($colNames);
        if (count($colWidths) !== $refcount || count($colAligns) !== $refcount) {
            return null;
        }

        $sortedColumns = [];
        if (empty($sampleRow)) {
            foreach ($colNames as $name) {
                $key = strtolower(StringUtil::toAscii(trim($name)));
                $sortedColumns[$key] = trim($name);
            }
        } else {
            $i = 0;
            foreach ($sampleRow as $key => $value) {
                if (array_key_exists($i, $colNames)) {
                    $sortedColumns[$key] = $colNames[$i];
                }
                $i++;
            }
        }

        $newColumnsArray = [];
        $i = 0;
        foreach ($sortedColumns as $key => $name) {
            $title = trim($name);
            $width = trim($colWidths[$i]);
            $align = strtoupper(trim($colAligns[$i]));
            switch ($align) {
                case 'L':
                    $align = 'left';
                    break;

                case 'R':
                    $align = 'right';
                    break;

                default:
                    $align = 'center';
            }
            $isSumCol = (in_array(($i + 1), $sumCols, false));
            $columnObject = new ReportColumn($key, $title, $width, $align, $isSumCol);
            $newColumnsArray[] = $columnObject;
            $i++;
        }

        if (count($newColumnsArray) === 0) {
            return null;
        }

        return new ReportColumnCollection($newColumnsArray);
    }

    /**
     * @param $parametersString
     *
     * @return ReportParameterCollection|null
     */
    public function parseParameters(?string $parametersString): ?ReportParameterCollection
    {
        if ($parametersString === null){
            return null;
        }
        $singleParamStrings = explode(';', $parametersString);
        if (count($singleParamStrings) === 0) {
            return null;
        }
        $newParamArray = [];
        foreach ($singleParamStrings as $param) {
            $parts = [];
            if (
                preg_match('/.*{(\w+)}\s*=\s*(\S+)/', $param, $parts)
                && count($parts) === 3
            ) {
                $paramObject = new ReportParameter($parts[1], $parts[2], $parts[1], [], '', false);
                $newParamArray[] = $paramObject;
            }
        }

        if (count($newParamArray) === 0) {
            return null;
        }

        return new ReportParameterCollection($newParamArray);
    }

    /**
     * @param array $legacyData
     * @param int   $repotId
     *
     * @return array
     */
    private function getTransferArray(array $legacyData, int $repotId): array
    {
        return [
            'id' => 0,
            'report_id' => $repotId,
            'ftp_active' => $legacyData['ftpuebertragung'],
            'ftp_type' => $legacyData['typ'],
            'ftp_host' => $this->getArrayValue('ftphost', $legacyData, ''),
            'ftp_port' => $this->getArrayValue('ftpport', $legacyData, 0),
            'ftp_user' => $this->getArrayValue('ftpuser', $legacyData, ''),
            'ftp_password' => $this->getArrayValue('ftppassword', $legacyData, ''),
            'ftp_interval_mode' => 'day',
            'ftp_interval_value' => 1,
            'ftp_daytime' => $legacyData['ftpuhrzeit'],
            'ftp_format' => 'csv',
            'ftp_filename' => $legacyData['ftpnamealternativ'],
            'email_active' => $legacyData['emailuebertragung'],
            'email_recipient' => $this->getArrayValue('emailempfaenger', $legacyData, ''),
            'email_subject' => $this->getArrayValue('emailbetreff', $legacyData, ''),
            'email_interval_mode' => 'day',
            'email_interval_value' => 1,
            'email_daytime'        => $legacyData['emailuhrzeit'],
            'email_format'         => 'csv',
            'email_filename'       => 'emailnamealternativ',
            'url_format'           => 'csv',
            'url_begin'            => null,
            'url_end'              => null,
            'url_address'          => '',
            'api_active'           => 0,
            'api_account_id'       => 0,
            'api_format'           => 'csv',
        ];
    }

    /**
     * @param array $legacyData
     * @param int   $reportId
     *
     * @return array
     */
    private function getShareArray(array $legacyData, int $reportId): array
    {
        return [
            'id' => 0,
            'report_id' => $reportId,
            'chart_public' => 0,
            'chart_axislabel' => '',
            'chart_type' => 'line',
            'chart_dateformat' => 'Y-m-d H:i:s',
            'chart_interval_value' => 0,
            'chart_interval_mode'  => '',
            'chart_x_column'       => '',
            'data_columns'         => '',
            'chart_group_column'   => '',
            'file_public'          => 0,
            'file_pdf_enabled'     => 0,
            'file_csv_enabled'     => 0,
            'file_xls_enabled'     => 0,
            'menu_public'          => $this->getArrayValue('doctype_actionmenu', $legacyData, 0),
            'menu_doctype'         => $this->getArrayValue('doctype', $legacyData, ''),
            'menu_label'           => $legacyData['doctype_actionmenuname'],
            'menu_format'          => $legacyData['doctype_actionmenufiletype'],
            'tab_public'           => 0,
            'tab_action'           => '',
            'tab_module'           => '',
            'tab_label'            => '',
            'tab_position'         => '',
        ];
    }

    /**
     * @param string $key
     * @param        $data
     * @param mixed  $default
     *
     * @return mixed|null
     */
    private function getArrayValue(string $key, array &$data, $default = null)
    {
        if (!isset($data[$key]) || $data[$key] === null) {
            return $default;
        }

        return $data[$key];
    }
}
