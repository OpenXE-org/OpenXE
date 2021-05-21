<?php
declare(strict_types=1);

namespace Xentral\Modules\Report;

use Xentral\Components\Exporter\Csv\CsvConfig;
use Xentral\Components\Exporter\Csv\CsvExporter;
use Xentral\Components\Exporter\Csv\CsvWriter;
use Xentral\Modules\Report\Data\ReportData;

class ReportCsvExportService extends AbstractReportExportService
{
    /** @var string $fileExtension */
    protected $fileExtension = 'csv';

    /**
     * @param ReportData $report
     * @param array      $parameterValues
     * @param string     $filename
     *
     * @return string
     */
    public function createCsvFileFromReport(ReportData $report, $parameterValues = [], $filename = '')
    {
        $filename = $this->generateFileName($report, $filename);
        $defaultHeaders = [];
        $headersKeyMap = [];
        foreach ($report->getColumns() as $column) {
            $defaultHeaders[] = $column->getTitle();
            $headersKeyMap[$column->getKey()] = $column->getTitle();
        }
        $delimiter = $report->getCsvDelimiter();
        if ($delimiter === '') {
            $delimiter = null;
        }
        $enclosure = $report->getCsvEnclosure();
        if ($enclosure === '') {
            $enclosure = null;
        }
        $csvConfig = new CsvConfig(
            $delimiter,
            $enclosure,
            null,
            null,
            null,
            true
        );
        $filePath = $this->genereateFilePath($filename);
        $resource = @fopen($filePath, 'x+b');
        $writer = new CsvWriter($resource,$csvConfig);
        $prepareHeaderLine = true;
        foreach ($this->db->yieldAll($this->service->resolveParameters($report, $parameterValues), []) as $row) {
            if($prepareHeaderLine){
                $this->writeHeaderLine($writer, $row, $defaultHeaders ,$headersKeyMap);
                $prepareHeaderLine = false;
            }
            $writer->writeLine($row);
        }
        fclose($resource);

        return $filePath;
    }

    /**
     * @param CsvWriter $writer
     * @param array     $row
     * @param array     $headers
     * @param array     $headersKeyMap
     */
    protected function writeHeaderLine(CsvWriter $writer, array $row, array $headers, array $headersKeyMap): void
    {
        if (count($row) > 0) {
            $headers = [];
            foreach ($row as $columnName => $value) {
                $headers[] = $headersKeyMap[$columnName];
            }
        }
        $writer->writeLine($headers);
    }
}
