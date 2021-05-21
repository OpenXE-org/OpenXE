<?php

declare(strict_types=1);

namespace Xentral\Modules\AmaInvoice\Scheduler;

use Exception;
use Xentral\Modules\AmaInvoice\Exception\ThrottlingException;
use Xentral\Modules\AmaInvoice\Service\AmaInvoiceService;
use Xentral\Modules\AmaInvoice\Exception\SchedulerTaskAlreadyRunningException;
use Xentral\Modules\SuperSearch\Wrapper\CompanyConfigWrapper;

final class AmaInvoiceTask
{
    /** @var AmaInvoiceService $service */
    private $service;

    /** @var CompanyConfigWrapper $config */
    private $config;

    /** @var bool */
    private $useFtp = false;


    /**
     * AmaInvoiceService constructor.
     *
     * @param AmaInvoiceService    $service
     * @param CompanyConfigWrapper $config
     */
    public function __construct($service, $config)
    {
        $this->service = $service;
        $this->config = $config;
    }

    /**
     * @throws Exception
     *
     * @return void
     */
    public function execute(): void
    {
        $taskActive = (int)$this->config->get('amainvoice_task_mutex');
        if ($taskActive > 0) {
            throw new SchedulerTaskAlreadyRunningException(
                'Amainvoice task is already running. Task can only run once at a time.'
            );
        }

        $this->config->set('amainvoice_task_mutex', '1');

        $this->syncNewFiles();
        $this->config->set('amainvoice_task_mutex', '1');
        $this->service->executeImportDateDbEntries(false, false);
        $this->config->set('amainvoice_task_mutex', '1');
        $this->service->executeImportDateDbEntries(false, true);
        $this->config->set('amainvoice_task_mutex', '1');
        $this->service->executeImportDateDbEntries(true, false);
    }

    /**
     * @throws Exception
     */
    private function syncNewFiles(): void
    {
        $files = $this->service->getNewFiles();
        $csvFiles = [];
        $datevFiles = [];
        $positions = [];
        $datevs = [];
        $pdfFiles = [];
        $dateFiles = $this->getFirstApiFiles($files);
        if ($this->useFtp) {
            $csvFiles = $this->getExportCsvs($files);
            $datevFiles = $this->getDatevFiles($files);
            [$invoicePdfFiles, $returnOrderPdfFiles] = $this->getPdfFiles($files);
            $pdfFiles = array_merge($invoicePdfFiles, $returnOrderPdfFiles);
            foreach ($csvFiles as $csvFile) {
                $position = $this->syncExportCsv($csvFile);
                if (!empty($position)) {
                    foreach ($position as $pos) {
                        $positions[] = $pos;
                    }
                }
            }
            foreach ($datevFiles as $datevFile) {
                $datev = $this->syncDatevCsv($datevFile);
                if (!empty($datev)) {
                    foreach ($datev as $pos) {
                        $datevs[] = $pos;
                    }
                }
            }
        }

        foreach (['invoice', 'returnorder'] as $type) {
            foreach ($dateFiles[$type] as $file) {
                try {
                    $this->service->executeImportDateFile($file);
                }
                catch(ThrottlingException $e) {
                    break 2;
                }
            }
            if ($this->useFtp) {
                $datev = empty($datevs[$type]) ? [] : $datevs[$type];
                if (empty($positions[$type])) {
                    continue;
                }
                foreach ($datev as $amazonOrderId => $documents) {
                    if (empty($positions[$type][$amazonOrderId])) {
                        continue;
                    }
                    foreach ($documents as $number => $document) {
                        $numberInvoice = $number;
                        if (empty($positions[$type][$amazonOrderId][$number])) {
                            $numberInvoice = substr($number, 3);
                            if (empty($positions[$type][$amazonOrderId][$numberInvoice])) {
                                continue;
                            }
                        }
                        try {
                            $pdfFile = in_array($number . '.pdf', $pdfFiles, true) ? $number . '.pdf' : null;
                            if (
                            $this->service->createDocument(
                                $type,
                                $amazonOrderId,
                                $number,
                                $document,
                                $positions[$type][$amazonOrderId][$numberInvoice],
                                $pdfFile
                            )
                            ) {
                                if ($pdfFile !== null) {
                                    $this->service->markFile($pdfFile, 'pdf', 'imported');
                                }
                            } elseif ($pdfFile !== null) {
                                $this->service->markFile($pdfFile, 'pdf', 'error');
                            }
                        } catch (Exception $e) {
                        }
                    }
                }
            }
        }

        if ($this->useFtp) {
            foreach ($datevFiles as $datevFile) {
                $this->service->markFile($datevFile, 'datev', 'imported');
                $this->service->cleanFile($datevFile);
            }

            foreach ($csvFiles as $csvFile) {
                $this->service->markFile($csvFile, 'csv', 'imported');
                $this->service->cleanFile($csvFile);
            }
            foreach ($pdfFiles as $pdfFile) {
                $this->service->cleanFile($pdfFile);
            }
        }
    }

    /**
     * @param string $file
     *
     * @throws Exception
     *
     * @return array
     */
    private function syncDatevCsv($file): array
    {
        $csvFile = $this->service->getFile($file);

        return $this->service->getPositionFromDatevCsv($csvFile);
    }

    /**
     * @param string $file
     *
     * @throws Exception
     * @return array
     */
    private function syncExportCsv($file): array
    {
        $csvFile = $this->service->getFile($file);

        return $this->service->getPositionFromExportCsv($csvFile);
    }

    /**
     * @param array $files
     *
     * @return array[]
     */
    private function getPdfFiles($files): array
    {
        if (empty($files)) {
            return [[], []];
        }

        $ret = [[], []];
        foreach ($files as $file) {
            if (substr($file, -4) === '.pdf') {
                if (strpos($file, 'GS') === 0) {
                    $ret[1][] = $file;
                }
                $ret[0][] = $file;
            }
        }

        return $ret;
    }

    /**
     * @param array $files
     *
     * @return array
     */
    private function getDatevFiles($files): array
    {
        if (empty($files)) {
            return [];
        }

        $ret = [];
        foreach ($files as $file) {
            if (strpos($file, 'EXTF_ERLOESE') === 0 && substr($file, -4) === '.csv') {
                $ret[] = $file;
            }
        }

        return $ret;
    }

    /**
     * @param array $files
     *
     * @return array|array[]
     */
    private function getFirstApiFiles($files): array
    {
        $ret = [
            'invoice'     => [],
            'returnorder' => [],
        ];
        if (empty($files)) {
            return $ret;
        }


        foreach ($files as $file) {
            if (substr($file, -4) === '.inv') {
                $ret['invoice'][] = $file;

                return $ret;
            }
            if (substr($file, -4) === '.rem') {
                $ret['returnorder'][] = $file;

                return $ret;
            }
        }

        return $ret;
    }

    /**
     * @param array $files
     *
     * @return array
     */
    private function getExportCsvs($files): array
    {
        if (empty($files)) {
            return [];
        }

        $ret = [];
        foreach ($files as $file) {
            if (strpos($file, 'export_') === 0 && substr($file, -4) === '.csv') {
                $ret[] = $file;
            }
        }

        return $ret;
    }

    /**
     * @return void
     */
    public function cleanup(): void
    {
        $this->config->set('amainvoice_task_mutex', '0');
    }

}
