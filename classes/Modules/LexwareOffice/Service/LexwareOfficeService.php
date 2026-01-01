<?php

declare(strict_types=1);

namespace Xentral\Modules\LexwareOffice\Service;

use erpAPI;
use Xentral\Components\Database\Database;
use Xentral\Components\Logger\Logger;
use Xentral\Modules\LexwareOffice\Exception\LexwareOfficeException;

final class LexwareOfficeService
{
    public function __construct(
        private Database $db,
        private LexwareOfficeConfigService $config,
        private LexwareOfficeApiClient $client,
        private Logger $logger,
        private ?erpAPI $erp = null
    ) {
    }

    public function hasApiKey(): bool
    {
        return $this->config->hasApiKey();
    }

    public function saveApiKey(string $apiKey): void
    {
        $this->config->saveApiKey($apiKey);
    }

    public function deleteApiKey(): void
    {
        $this->config->deleteApiKey();
    }

    /**
     * Laedt nur das Rechnungs-PDF nach Lexware (POST /files), ohne Beleganlage.
     *
     * @param int $invoiceId
     *
     * @return array{id:string,fileUpload:array}
     */
    public function pushInvoice(int $invoiceId): array
    {
        $apiKey = $this->config->getApiKey();
        if (empty($apiKey)) {
            throw new LexwareOfficeException('Es ist kein Lexware Office API-Schluessel hinterlegt.');
        }

        $invoice = $this->fetchInvoice($invoiceId);
        if (empty($invoice)) {
            throw new LexwareOfficeException('Rechnung wurde nicht gefunden.');
        }

        $pdfPath = $this->createInvoicePdf($invoiceId);

        $fileResponse = [];
        $fileId = null;
        try {
            $fileName = $this->buildInvoiceFileName($invoice);
            $fileResponse = $this->client->uploadFile($apiKey, $pdfPath, $fileName, 'voucher');
            $fileId = $fileResponse['id'] ?? null;
            if ($fileId === null || $fileId === '') {
                throw new LexwareOfficeException('Lexware Office File-ID wurde nicht zurueckgegeben.');
            }
        } finally {
            if (is_file($pdfPath)) {
                @unlink($pdfPath);
            }
        }

        $this->logger->notice(
            'Rechnungs-PDF an Lexware Office hochgeladen',
            [
                'invoice_id' => $invoiceId,
                'lexware_file_id' => $fileId,
                'lexware_file_response' => $fileResponse,
            ]
        );

        return [
            'fileId' => $fileId,
            'fileUpload' => $fileResponse,
        ];
    }

    private function fetchInvoice(int $invoiceId): ?array
    {
        return $this->db->fetchRow(
            'SELECT r.*, adr.email AS adresse_email, adr.telefon AS adresse_telefon
             FROM `rechnung` AS `r`
             LEFT JOIN `adresse` AS `adr` ON adr.id = r.adresse
             WHERE r.id = :id',
            ['id' => $invoiceId]
        );
    }

    private function createInvoicePdf(int $invoiceId): string
    {
        if ($this->erp === null) {
            throw new LexwareOfficeException('ERP-API fuer PDF-Erstellung nicht verfuegbar.');
        }

        $projectId = (int)$this->db->fetchValue(
            'SELECT projekt FROM `rechnung` WHERE id = :id',
            ['id' => $invoiceId]
        );

        $previousBackgroundSetting = $this->erp->BriefpapierHintergrunddisable ?? false;
        $this->erp->BriefpapierHintergrunddisable = true;

        try {
            $className = class_exists('\RechnungPDFCustom') ? '\RechnungPDFCustom' : '\RechnungPDF';
            $brief = new $className($this->erp->app, $projectId);
            $brief->GetRechnung($invoiceId);
            $pdfPath = $brief->displayTMP(true);
        } finally {
            $this->erp->BriefpapierHintergrunddisable = $previousBackgroundSetting;
        }

        if (empty($pdfPath) || !is_file($pdfPath)) {
            throw new LexwareOfficeException('Rechnungs-PDF konnte nicht erzeugt werden.');
        }

        return $pdfPath;
    }

    private function buildInvoiceFileName(array $invoice): string
    {
        $raw = $invoice['belegnr'] ?? (string)($invoice['id'] ?? $invoice['rechnung'] ?? '');
        $raw = $raw !== '' ? $raw : (string)$invoice['id'];
        $safe = preg_replace('/[^A-Za-z0-9._-]+/', '_', (string)$raw);
        if ($safe === '' || $safe === '_') {
            $safe = 'rechnung';
        }

        return sprintf('Rechnung_%s.pdf', $safe);
    }
}
