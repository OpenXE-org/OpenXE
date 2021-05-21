<?php

namespace Xentral\Modules\Report;

use Xentral\Components\Database\Database;
use Xentral\Components\Util\StringUtil;
use Xentral\Modules\Report\Data\ReportData;

abstract class AbstractReportExportService
{
    /** @var string $fileExtension */
    protected $fileExtension = '';

    /** @var Database $db */
    protected $db;

    /** @var ReportService $service */
    protected $service;

    /** @var ReportGateway $gateway */
    protected $gateway;

    /** @var string */
    protected $tempDir;

    /**
     * @param Database      $db
     * @param ReportGateway $gateway
     * @param ReportService $service
     * @param string        $tempDir
     */
    public function __construct(Database $db, ReportGateway $gateway, ReportService $service, $tempDir)
    {
        $this->db = $db;
        $this->gateway = $gateway;
        $this->service = $service;
        $this->tempDir = $tempDir;
    }

    /**
     * @param ReportData $report
     * @param string     $filename
     *
     * @return string
     */
    public function generateFileName(ReportData $report, $filename = '')
    {
        if($filename === ''){
            $filename = sprintf('Report_{BERICHTNAME}_{TIMESTAMP}.%s', $this->fileExtension);
        }
        $variables = ['{TIMESTAMP}', '{DATUM}', '{BERICHTNAME}'];
        $values = [time(), date('Y-m-d_H-i-s'), $report->getName()];
        $filename = str_replace($variables, $values, $filename);
        if (substr(strtolower($filename), -strlen($this->fileExtension)) !== strtolower($this->fileExtension)) {
            $filename = "{$filename}.{$this->fileExtension}";
        }

        return StringUtil::toFilename($filename);
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public function genereateFilePath($fileName)
    {
        return sprintf('%s%s', $this->tempDir, $fileName);
    }
}
