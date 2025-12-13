<?php

declare(strict_types=1);

namespace Xentral\Modules\LexwareOffice\Service;

use DateTimeImmutable;
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
     * @param int $invoiceId
     *
     * @return array
     */
    public function pushInvoice(int $invoiceId): array
    {
        $apiKey = $this->config->getApiKey();
        if (empty($apiKey)) {
            throw new LexwareOfficeException('Es ist kein Lexware Office API-Schlüssel hinterlegt.');
        }

        $invoice = $this->fetchInvoice($invoiceId);
        if (empty($invoice)) {
            throw new LexwareOfficeException('Rechnung wurde nicht gefunden.');
        }

        $positions = $this->fetchPositions($invoiceId);
        if (empty($positions)) {
            throw new LexwareOfficeException('Die Rechnung enthält keine Positionen.');
        }

        $contactId = $this->resolveContact($apiKey, $invoice);
        $payload = $this->mapInvoicePayload($invoice, $positions, $contactId);
        $invoiceResponse = $this->client->createInvoice($apiKey, $payload, true);

        $lexwareInvoiceId = $this->extractInvoiceId($invoiceResponse);
        if ($lexwareInvoiceId === null || $lexwareInvoiceId === '') {
            $message = $invoiceResponse['message'] ?? $invoiceResponse['error'] ?? '';
            if ($message === '' && !empty($invoiceResponse)) {
                $message = json_encode($invoiceResponse, JSON_UNESCAPED_UNICODE);
            }
            throw new LexwareOfficeException(
                sprintf(
                    'Rechnung wurde nicht in Lexware Office angelegt. %s',
                    $message !== '' ? $message : 'Keine Beleg-ID erhalten.'
                )
            );
        }

        $this->logger->notice(
            'Rechnung an Lexware Office gesendet',
            [
                'invoice_id' => $invoiceId,
                'lexware_invoice_id' => $lexwareInvoiceId,
                'contact_id' => $contactId,
                'lexware_response' => $invoiceResponse,
                'lexware_payload' => $payload,
            ]
        );

        return [
            'invoiceId' => $lexwareInvoiceId,
            'contactId' => $contactId,
            'response' => $invoiceResponse,
        ];
    }

    private function extractInvoiceId(array $invoiceResponse): ?string
    {
        $ids = [
            $invoiceResponse['id'] ?? null,
            $invoiceResponse['voucherId'] ?? null,
        ];

        if (isset($invoiceResponse['content']) && is_array($invoiceResponse['content'])) {
            $first = reset($invoiceResponse['content']);
            if (is_array($first)) {
                $ids[] = $first['id'] ?? null;
                $ids[] = $first['voucherId'] ?? null;
            }
        }

        foreach ($ids as $id) {
            if ($id !== null && $id !== '') {
                return (string)$id;
            }
        }

        return null;
    }

    /**
     * @param string $apiKey
     * @param array  $invoice
     *
     * @return string
     */
    private function resolveContact(string $apiKey, array $invoice): string
    {
        $searchEmail = $invoice['email'] ?? $invoice['adresse_email'] ?? null;
        $search = array_filter([
            'email' => $searchEmail,
            'name' => $invoice['name'] ?? null,
        ]);

        if (!empty($search)) {
            $found = $this->client->searchContacts($apiKey, $search);
            $content = $found['content'] ?? $found['items'] ?? [];
            if (!empty($content)) {
                $first = reset($content);
                if (!empty($first['id'])) {
                    return $first['id'];
                }
            }
        }

        $contactPayload = $this->mapContactPayload($invoice);
        $created = $this->client->createContact($apiKey, $contactPayload);
        if (empty($created['id'])) {
            throw new LexwareOfficeException('Kontakt konnte in Lexware Office nicht angelegt werden.');
        }

        return $created['id'];
    }

    /**
     * @param array $invoice
     *
     * @return array
     */
    private function mapContactPayload(array $invoice): array
    {
        $email = $invoice['email'] ?? $invoice['adresse_email'] ?? '';
        $phone = $invoice['telefon'] ?? $invoice['adresse_telefon'] ?? '';
        $countryCode = $this->normalizeCountry($invoice['land'] ?? '');
        $address = array_filter([
            'name' => $invoice['name'] ?? '',
            'supplement' => $invoice['adresszusatz'] ?? '',
            'street' => $invoice['strasse'] ?? '',
            'zip' => $invoice['plz'] ?? '',
            'city' => $invoice['ort'] ?? '',
            'countryCode' => $countryCode,
        ], static fn($value) => $value !== null && $value !== '');

        $payload = [
            'version' => 0,
            'roles' => [
                'customer' => new \stdClass(),
            ],
            'company' => [
                'name' => $invoice['name'] ?? '',
            ],
            'addresses' => [
                'billing' => [$address],
            ],
        ];

        if (!empty($email)) {
            $payload['emailAddresses']['business'][] = $email;
        }
        if (!empty($phone)) {
            $payload['phoneNumbers']['business'][] = $phone;
        }

        return $payload;
    }

    /**
     * @param array  $invoice
     * @param array  $positions
     * @param string $contactId
     *
     * @return array
     */
    private function mapInvoicePayload(array $invoice, array $positions, string $contactId): array
    {
        $voucherDate = $invoice['datum'] ?? date('Y-m-d');
        $voucherDateTime = new DateTimeImmutable($voucherDate . ' 00:00:00');
        $countryCode = $this->normalizeCountry($invoice['land'] ?? '');
        $paymentTerm = (int)($invoice['zahlungszieltage'] ?? 0);
        $discountDays = (int)($invoice['zahlungszieltageskonto'] ?? 0);
        $discountPercent = (float)($invoice['zahlungszielskonto'] ?? 0);
        $title = !empty($invoice['belegnr']) ? sprintf('Rechnung %s', $invoice['belegnr']) : 'Rechnung';

        $payload = [
            'contactId' => $contactId,
            // Lexware expects a timestamp without timezone; use ISO date+time.
            'voucherDate' => $voucherDateTime->format('Y-m-d\TH:i:s'),
            'title' => $title,
            'remark' => $invoice['freitext'] ?? '',
            'useContactAddress' => false,
            'address' => [
                'name' => $invoice['name'] ?? '',
                'supplement' => $invoice['adresszusatz'] ?? '',
                'street' => $invoice['strasse'] ?? '',
                'zip' => $invoice['plz'] ?? '',
                'city' => $invoice['ort'] ?? '',
                'countryCode' => $countryCode,
            ],
            'lineItems' => $this->mapLineItems($positions, $invoice),
            'taxConditions' => [
                'taxType' => 'net',
            ],
            'paymentConditions' => [
                'paymentTermDuration' => $paymentTerm,
            ],
            'electronicDocumentProfile' => 'NONE',
        ];

        if ($discountDays > 0 && $discountPercent > 0) {
            $payload['paymentConditions']['paymentDiscountConditions'] = [
                'discountPercentage' => $discountPercent,
                'discountRange' => $discountDays,
            ];
            $payload['paymentConditions']['paymentTermLabel'] = sprintf(
                '%d Tage - %s%%, %d Tage netto',
                $discountDays,
                $this->formatNumber($discountPercent),
                $paymentTerm
            );
        }

        return $payload;
    }

    /**
     * @param array $positions
     * @param array $invoice
     *
     * @return array
     */
    private function mapLineItems(array $positions, array $invoice): array
    {
        $items = [];
        $defaultCurrency = $invoice['waehrung'] ?? 'EUR';
        $defaultTax = (float)($invoice['steuersatz_normal'] ?? 19);

        foreach ($positions as $position) {
            $tax = $position['steuersatz'] ?? $defaultTax;
            $items[] = array_filter([
                'type' => 'custom',
                'name' => $position['bezeichnung'] ?? $position['nummer'] ?? 'Position',
                'description' => $position['beschreibung'] ?? '',
                'quantity' => (float)$position['menge'],
                'unitName' => $position['einheit'] ?: 'Stück',
                'unitPrice' => [
                    'currency' => $position['waehrung'] ?: $defaultCurrency,
                    'netAmount' => (float)$position['preis'],
                ],
                'discountPercentage' => $this->getDiscount($position),
                'taxRatePercentage' => (float)$tax,
            ], static fn($value) => $value !== null && $value !== '');
        }

        return $items;
    }

    /**
     * @param array $position
     *
     * @return float|null
     */
    private function getDiscount(array $position): ?float
    {
        $discount = $position['rabatt'] ?? 0.0;
        if ((float)$discount <= 0.0) {
            return null;
        }

        return (float)$discount;
    }

    /**
     * @param string $country
     *
     * @return string
     */
    private function normalizeCountry(string $country): string
    {
        $country = trim($country);
        if (strlen($country) === 2) {
            return strtoupper($country);
        }

        if ($this->erp !== null) {
            $iso = $this->erp->FindISOCountry($country);
            if (!empty($iso) && $iso !== -1) {
                return strtoupper($iso);
            }
        }

        return 'DE';
    }

    /**
     * @param int $invoiceId
     *
     * @return array|null
     */
    private function fetchInvoice(int $invoiceId): ?array
    {
        return $this->db->fetchRow(
            'SELECT 
                r.*, 
                COALESCE(r.kundennummer, adr.kundennummer) AS kundennummer_lexware,
                adr.email AS adresse_email,
                adr.telefon AS adresse_telefon
            FROM `rechnung` AS `r`
            LEFT JOIN `adresse` AS `adr` ON adr.id = r.adresse
            WHERE r.id = :id',
            ['id' => $invoiceId]
        );
    }

    /**
     * @param int $invoiceId
     *
     * @return array
     */
    private function fetchPositions(int $invoiceId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM `rechnung_position` WHERE `rechnung` = :id ORDER BY `sort`',
            ['id' => $invoiceId]
        );
    }

    private function formatNumber(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }
}
