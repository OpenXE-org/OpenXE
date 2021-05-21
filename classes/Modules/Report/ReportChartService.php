<?php

namespace Xentral\Modules\Report;

use Xentral\Components\Database\Database;
use Xentral\Modules\Report\Data\ReportData;
use Xentral\Modules\Report\Exception\InvalidArgumentException;
use Xentral\Modules\Report\Exception\EmptyQueryException;
use Xentral\Widgets\Chart\Chart;
use Xentral\Widgets\Chart\Dataset;
use Xentral\Widgets\Chart\HtmlRenderer;

class ReportChartService
{
    /** @var Database $db */
    private $db;

    /** @var ReportService $service */
    protected $service;

    /** @var ReportGateway $gateway */
    protected $gateway;

    /**
     * @param Database $db
     */
    public function __construct(Database $db, ReportService $service, ReportGateway $gateway)
    {
        $this->db = $db;
        $this->service = $service;
        $this->gateway = $gateway;
    }

    /**
     * @param array  $data
     * @param string $groupColumn
     * @param string $valueCol
     *
     * @return array[]
     */
    private function groupDataByColumn($data, $groupColumn, $axesCol, $valueCol)
    {
        $groupedData = [];
        $groupValues = [];
        foreach($data as $row) {
            if(!isset($row[$groupColumn])) {
                continue;
            }
            if(isset($row[$axesCol]) && isset($row[$valueCol])) {
                $groupedData[$row[$axesCol]][$row[$groupColumn]] = $row;
            }
            if(!in_array($row[$groupColumn], $groupValues)) {
                $groupValues[] = $row[$groupColumn];
            }
        }
        if(empty($groupValues) || empty($groupedData)) {
            return [$data, $groupValues];
        }
        sort($groupValues);
        $data = [];
        foreach($groupedData as $axeValue => $groupedRows) {
            $firstRow = reset($groupedRows);
            unset($firstRow[$valueCol]);
            unset($firstRow[$groupColumn]);

            foreach($groupValues as $groupValue) {
                if(isset($groupedRows[$groupValue])) {
                    $firstRow[$groupValue] = $groupedRows[$groupValue][$valueCol];
                }
                else {
                    $firstRow[$groupValue] = 0;
                }
            }
            $data[] = $firstRow;
        }

        return [$data, $groupValues];
    }

    /**
     * @param ReportData $report
     *
     * @return Chart
     */
    private function getChartFromReport(ReportData $report, $parameterValues = [])
    {
        $struktur = $this->service->resolveParameters($report, $parameterValues);
        if(!$this->service->isSqlStatementAllowed($struktur))
        {
            throw new InvalidArgumentException('Resolved Query not executable.');
        }

        $arr = $this->db->fetchAll($struktur);
        if(empty($arr)) {
            throw new EmptyQueryException('Result empty');
        }

        $share = $this->gateway->findShareArrayByReportId($report->getId());
        if(empty($share)) {
            throw new EmptyQueryException('Share Object empty');
        }

        $chartType = $share['chart_type'];
        if(empty($chartType)) {
            $chartType = 'line';
        }
        $yAxis = $share['chart_axislabel'];
        $groupColumn = $share['chart_group_column'];
        $firstColumn = $share['chart_x_column'];
        $dataColumns = trim($share['data_columns'],';');
        $dataColumnsArr = explode(';', $dataColumns);
        $dataColumnsArr = array_map('trim', $dataColumnsArr);
        $firstName = $yAxis;
        $secondColumn = $dataColumns;
        $groupValues = [];
        $isLineOrBar = in_array($chartType, ['line','bar']);
        if(!empty($groupColumn) && !empty($firstColumn) && !empty($dataColumns) && $isLineOrBar) {
            list($arr, $groupValues) = $this->groupDataByColumn($arr, $groupColumn, $firstColumn, $dataColumns);
            if(!empty($groupValues)) {
                $secondColumn = reset($groupValues);
                $firstName = $secondColumn;
            }
        }
        elseif($isLineOrBar && count($dataColumnsArr) > 1) {
            $groupValues = $dataColumnsArr;
        }

        foreach ($report->getColumns() as $column) {
            if(empty($firstColumn)) {
                $firstColumn = $column->getKey();
                if(empty($firstName)) {
                    $firstName = $column->getTitle();
                }
                continue;
            }
            if($column->getKey() === $firstColumn) {
                continue;
            }
            if(empty($secondColumn)) {
                $secondColumn = $column->getKey();
                break;
            }
        }

        $data = array_column($arr, $secondColumn);
        $data = array_map('floatVal', str_replace(',','.', $data));
        $labels = array_column($arr, $firstColumn);
        $dataset = new Dataset($firstName, $data);

        $chart = new Chart($chartType, $labels, [$dataset]);
        if(!empty($groupValues) && count($groupValues) > 1) {
            foreach($groupValues as $groupValue) {
                if($groupValue === $secondColumn) {
                    continue;
                }
                $chart->addDataset(
                    new Dataset(
                        $groupValue,
                        array_map(
                            'floatVal',
                            str_replace(',','.',
                                array_column(
                                    $arr,
                                    $groupValue
                                )
                            )
                        )
                    )
                );
            }
        }

        return $chart;
    }

    /**
     * @param Chart  $chart
     * @param string $title
     * @param int    $width
     * @param int    $height
     *
     * @return HtmlRenderer
     */
    private function getChartRenderer(Chart $chart, $title, $width = 400, $height = 150)
    {
        return new HtmlRenderer($chart, $title, $width, $height);
    }

    /**
     * @param HtmlRenderer $render
     *
     * @return string
     */
    private function getChartString(HtmlRenderer $render)
    {
        return $render->render();
    }

    /**
     * @param ReportData $report
     * @param int        $width
     * @param int        $height
     *
     * @return string
     */
    public function renderChartByReport(ReportData $report, $width = 400, $height = 150)
    {
        $chart = $this->getChartFromReport($report);
        $renderer = $this->getChartRenderer($chart, $report->getName(), $width, $height);

        return $this->getChartString($renderer);
    }
}
