<?php

namespace Xentral\Modules\Report;

use Exception;
use Xentral\Modules\Report\Data\ReportData;
use Xentral\Modules\Report\Exception\JsonExportException;

final class ReportJsonExportService extends AbstractReportExportService
{
    /** @var string $fileExtension */
    protected $fileExtension = 'json';

    /**
     * @param ReportData $report
     * @param string     $filename
     *
     * @return string
     */
    public function createJsonFileFromReport(ReportData $report, $filename = '')
    {
        $filename = $this->generateFileName($report, $filename);
        $filePath = $this->genereateFilePath($filename);
        $data = $this->generateJsonStructure($report);
        $jsonString = json_encode($data, JSON_PRETTY_PRINT);
        try {
            $resource = fopen($filePath, 'x+b');
            fwrite($resource, $jsonString);
            fclose($resource);
        } catch (Exception $e) {
            throw new JsonExportException(sprintf('JSON export failed: %s', $e->getMessage()), $e->getCode(), $e);
        }

        return $filePath;
    }

    /**
     * @param ReportData $report
     *
     * @return array
     */
    public function generateJsonStructure(ReportData $report)
    {
        $id = $report->getId();
        $data = $report->jsonSerialize();
        $share = $this->gateway->findShareArrayByReportId($id);
        $data['share'] = null;
        if ($share === null || count($share) === 0) {
            return $data;
        }
        $share['chart_public'] = ($share['chart_public'] === 1);
        $share['file_public'] = ($share['file_public'] === 1);
        $share['file_pdf_enabled'] = ($share['file_pdf_enabled'] === 1);
        $share['file_csv_enabled'] = ($share['file_csv_enabled'] === 1);
        $share['file_xls_enabled'] = ($share['file_xls_enabled'] === 1);
        $share['menu_public'] = ($share['menu_public'] === 1);
        $share['tab_public'] = ($share['tab_public'] === 1);
        unset($share['id'], $share['report_id']);

        $data['share'] = $share;

        return $data;
    }
}
