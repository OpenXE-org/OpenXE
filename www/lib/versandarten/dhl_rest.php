<?php

/*
 * SPDX-FileCopyrightText: 2025 Andreas Palm
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

use Xentral\Carrier\DhlRest\DhlRestApi;
use Xentral\Carrier\DhlRest\DhlRestApiException;
use Xentral\Modules\ShippingMethod\Model\AddressType;
use Xentral\Modules\ShippingMethod\Model\CreateShipmentResult;
use Xentral\Modules\ShippingMethod\Model\Product;
use Xentral\Modules\ShippingMethod\Model\Service;
use Xentral\Modules\ShippingMethod\Model\ShipmentStatus;
use Xentral\Modules\ShippingMethod\Model\ShipmentType;

require_once dirname(__DIR__) . '/class.versanddienstleister.php';

/**
 * DHL Paket DE Versenden REST API v2 shipping module.
 *
 * Drop-in replacement for the SOAP-based dhl.php module.
 * Uses Basic Auth + dhl-api-key header (supported until further notice).
 */
class Versandart_dhl_rest extends Versanddienstleister
{
    public function GetName(): string
    {
        return 'DHL REST API';
    }

    public function AdditionalSettings(): array
    {
        return [
            'api_user'     => ['typ' => 'text', 'bezeichnung' => 'GK-Benutzername (E-Mail):',
                               'info' => 'Benutzername (E-Mail-Adresse) des DHL Geschäftskunden-Kontos'],
            'api_password' => ['typ' => 'text', 'bezeichnung' => 'GK-Passwort:',
                               'info' => 'Passwort des DHL Geschäftskunden-Kontos'],
            'api_key'      => ['typ' => 'text', 'bezeichnung' => 'API Key:',
                               'info' => 'API Key aus dem DHL Developer Portal – anlegen unter <a href="https://developer.dhl.com/" target="_blank">https://developer.dhl.com/</a>'],
            'sandbox'      => ['typ' => 'checkbox', 'bezeichnung' => 'Testumgebung (Sandbox):'],

            'ekp'                  => ['typ' => 'text', 'bezeichnung' => 'EKP',
                                       'info' => '10-stellige DHL Kundennummer'],
            'accountnumber'        => ['typ' => 'text', 'bezeichnung' => 'Abrechnungsnummer Paket:',
                                       'info' => '14-stellig (EKP+Verfahren+Teilnahme, z.B. 1234567890 0101)'],
            'accountnumber_int'    => ['typ' => 'text', 'bezeichnung' => 'Abrechnungsnummer Paket International:'],
            'accountnumber_euro'   => ['typ' => 'text', 'bezeichnung' => 'Abrechnungsnummer Europaket:'],
            'accountnumber_connect'=> ['typ' => 'text', 'bezeichnung' => 'Abrechnungsnummer Paket Connect:'],
            'accountnumber_wp'     => ['typ' => 'text', 'bezeichnung' => 'Abrechnungsnummer Warenpost:'],
            'accountnumber_wpint'  => ['typ' => 'text', 'bezeichnung' => 'Abrechnungsnummer Warenpost International:'],

            'sender_name1'          => ['typ' => 'text', 'bezeichnung' => 'Versender Firma:'],
            'sender_street'         => ['typ' => 'text', 'bezeichnung' => 'Versender Strasse:'],
            'sender_streetnumber'   => ['typ' => 'text', 'bezeichnung' => 'Versender Strasse Nr.:'],
            'sender_zip'            => ['typ' => 'text', 'bezeichnung' => 'Versender PLZ:'],
            'sender_city'           => ['typ' => 'text', 'bezeichnung' => 'Versender Stadt:'],
            'sender_country'        => ['typ' => 'text', 'bezeichnung' => 'Versender ISO Code:', 'info' => 'DE'],
            'sender_email'          => ['typ' => 'text', 'bezeichnung' => 'Versender E-Mail:'],
            'sender_phone'          => ['typ' => 'text', 'bezeichnung' => 'Versender Telefon:'],
            'sender_contact_person' => ['typ' => 'text', 'bezeichnung' => 'Versender Ansprechpartner:'],

            'cod_account_owner' => ['typ' => 'text', 'bezeichnung' => 'Nachnahme Kontoinhaber:'],
            'cod_bank_name'     => ['typ' => 'text', 'bezeichnung' => 'Nachnahme Bank Name:'],
            'cod_account_iban'  => ['typ' => 'text', 'bezeichnung' => 'Nachnahme IBAN:'],
            'cod_account_bic'   => ['typ' => 'text', 'bezeichnung' => 'Nachnahme BIC:'],
            'cod_extra_fee'     => ['typ' => 'text', 'bezeichnung' => 'Nachnahme Gebühr:',
                                    'info' => 'z.B. 2,00 — wird auf Rechnungsbetrag addiert'],

            'weight'  => ['typ' => 'text', 'bezeichnung' => 'Standard Gewicht:', 'info' => 'in KG'],
            'length'  => ['typ' => 'text', 'bezeichnung' => 'Standard Länge:',   'info' => 'in cm'],
            'width'   => ['typ' => 'text', 'bezeichnung' => 'Standard Breite:',  'info' => 'in cm'],
            'height'  => ['typ' => 'text', 'bezeichnung' => 'Standard Höhe:',    'info' => 'in cm'],
            'product' => ['typ' => 'text', 'bezeichnung' => 'Standard Produkt:', 'info' => 'z.B. V01PAK'],
        ];
    }

    protected function CreateShipment(object $json): CreateShipmentResult
    {
        $ret = new CreateShipmentResult();
        try {
            $api      = $this->buildApi();
            $payload  = $this->buildShipmentPayload($json);
            $response = $api->createShipment($payload);

            $item = $response['items'][0] ?? null;
            if ($item === null) {
                $ret->Errors[] = 'Ungültige API-Antwort (kein items-Eintrag)';
                return $ret;
            }

            $statusCode = $item['sstatus']['statusCode'] ?? $response['status']['statusCode'] ?? 0;

            if ($statusCode === 200) {
                $ret->Success        = true;
                $ret->TrackingNumber = $item['shipmentNo'];
                $ret->TrackingUrl    = sprintf(
                    'https://www.dhl.de/de/privatkunden/pakete-empfangen/verfolgen.html?piececode=%s',
                    $ret->TrackingNumber
                );

                $ret->Label = $this->extractPdf($api, $item['label'] ?? []);

                if (!empty($item['customsDoc'])) {
                    try {
                        $ret->ExportDocuments = $this->extractPdf($api, $item['customsDoc']);
                    } catch (DhlRestApiException $e) {
                        $ret->AdditionalInfo = 'Exportdokument nicht abrufbar: ' . $e->getMessage();
                    }
                }

                $warnings = $item['validationMessages'] ?? [];
                if (!empty($warnings)) {
                    $texts = array_column($warnings, 'validationMessage');
                    $ret->AdditionalInfo = implode('; ', array_filter($texts));
                }
            } else {
                foreach ($item['validationMessages'] ?? [] as $msg) {
                    $ret->Errors[] = $msg['validationMessage'] ?? 'Unbekannter Fehler';
                }
                if (empty($ret->Errors)) {
                    $detail = $response['status']['detail'] ?? ($item['sstatus']['title'] ?? '');
                    $ret->Errors[] = "API-Fehler $statusCode" . ($detail ? ": $detail" : '');
                }
            }
        } catch (DhlRestApiException $e) {
            $ret->Errors[] = $e->getMessage();
        }

        return $ret;
    }

    protected function GetShippingProducts(): array
    {
        $result = [];
        if (!empty($this->settings->accountnumber)) {
            $result[] = Product::Create('V01PAK', 'DHL Paket')
                ->WithLength(15, 120)->WithWidth(11, 60)->WithHeight(1, 60)->WithWeight(0.01, 31.5);
        }
        if (!empty($this->settings->accountnumber_int)) {
            $result[] = Product::Create('V53WPAK', 'DHL Paket International')
                ->WithLength(15, 120)->WithWidth(11, 60)->WithHeight(1, 60)->WithWeight(0.01, 31.5)
                ->WithServices([Service::SERVICE_PREMIUM]);
        }
        if (!empty($this->settings->accountnumber_euro)) {
            $result[] = Product::Create('V54EPAK', 'DHL Europaket')
                ->WithLength(15, 120)->WithWidth(11, 60)->WithHeight(3.5, 60)->WithWeight(0.01, 31.5);
        }
        if (!empty($this->settings->accountnumber_connect)) {
            $result[] = Product::Create('V55PAK', 'DHL Paket Connect')
                ->WithLength(15, 120)->WithWidth(11, 60)->WithHeight(3.5, 60)->WithWeight(0.01, 31.5);
        }
        if (!empty($this->settings->accountnumber_wp)) {
            $result[] = Product::Create('V62WP', 'DHL Warenpost')
                ->WithLength(10, 35)->WithWidth(7, 25)->WithHeight(0.1, 5)->WithWeight(0.01, 1);
        }
        if (!empty($this->settings->accountnumber_wpint)) {
            $result[] = Product::Create('V66WPI', 'DHL Warenpost International')
                ->WithLength(10, 35)->WithWidth(7, 25)->WithHeight(0.1, 10)->WithWeight(0.01, 1)
                ->WithServices([Service::SERVICE_PREMIUM]);
        }
        return $result;
    }

    public function GetShipmentStatus(string $tracking): ShipmentStatus|null
    {
        return null;
    }

    // ── private helpers ──────────────────────────────────────────────────────

    private function buildApi(): DhlRestApi
    {
        return new DhlRestApi(
            $this->settings->api_user     ?? '',
            $this->settings->api_password ?? '',
            $this->settings->api_key      ?? '',
            !empty($this->settings->sandbox)
        );
    }

    private function buildShipmentPayload(object $json): array
    {
        $payload = [
            'product'       => $json->productId,
            'billingNumber' => $this->getBillingNumber($json->productId) ?? '',
            'shipDate'      => date('Y-m-d'),
            'refNo'         => substr($json->reference ?? '', 0, 35) ?: null,
            'shipper'       => $this->buildShipper(),
            'consignee'     => $this->buildConsignee($json),
            'details'       => $this->buildDetails($json),
        ];

        $customs = $this->buildCustoms($json->customsDeclaration ?? null);
        if ($customs !== null) {
            $payload['customs'] = $customs;
        }

        // Remove null values at top level
        return array_filter($payload, fn($v) => $v !== null && $v !== '');
    }

    private function buildShipper(): array
    {
        return self::compactArray([
            'name1'         => $this->settings->sender_name1       ?? '',
            'addressStreet' => $this->settings->sender_street      ?? '',
            'addressHouse'  => $this->settings->sender_streetnumber ?? null,
            'postalCode'    => $this->settings->sender_zip         ?? '',
            'city'          => $this->settings->sender_city        ?? '',
            'country'       => self::toIso3($this->settings->sender_country ?? 'DE'),
            'email'         => $this->settings->sender_email       ?? null,
            'phone'         => $this->settings->sender_phone       ?? null,
            'contactName'   => $this->settings->sender_contact_person ?? null,
        ]);
    }

    private function buildConsignee(object $json): array
    {
        $a = $json->address;
        $addresstype = $a->addresstype ?? AddressType::COMPANY->value;

        // Packstation
        if ($addresstype === AddressType::PARCELSTATION->value) {
            return self::compactArray([
                'name'       => $a->name,
                'lockerID'   => $a->parcelstationNumber ?? '',
                'postNumber' => $a->postnumber          ?? null,
                'postalCode' => $a->zip                 ?? '',
                'city'       => $a->city                ?? '',
                'country'    => self::toIso3($a->country ?? 'DE'),
                'email'      => $a->email               ?? null,
                'phone'      => $a->phone               ?? null,
            ]);
        }

        // Postfiliale
        if ($addresstype === AddressType::SHOP->value) {
            return self::compactArray([
                'name'       => $a->name,
                'retailID'   => $a->postofficeNumber    ?? '',
                'postNumber' => $a->postnumber          ?? null,
                'postalCode' => $a->zip                 ?? '',
                'city'       => $a->city                ?? '',
                'country'    => self::toIso3($a->country ?? 'DE'),
                'email'      => $a->email               ?? null,
                'phone'      => $a->phone               ?? null,
            ]);
        }

        // Company (0) or Private (3)
        if ($addresstype === AddressType::COMPANY->value) {
            $name1 = $a->companyName ?? $a->name ?? '';
            $name2Parts = array_filter(
                [$a->contactName ?? '', $a->companyDivision ?? ''],
                fn(string $s) => trim($s) !== ''
            );
            $name2 = implode('; ', $name2Parts) ?: null;
        } else {
            $name1 = $a->name         ?? '';
            $name2 = $a->contactName  ?? null;
        }

        return self::compactArray([
            'name1'                        => $name1,
            'name2'                        => $name2,
            'additionalAddressInformation1'=> $a->address2 ?? null,
            'addressStreet'                => $a->street       ?? '',
            'addressHouse'                 => $a->streetnumber ?? null,
            'postalCode'                   => $a->zip          ?? '',
            'city'                         => $a->city         ?? '',
            'country'                      => self::toIso3($a->country ?? 'DE'),
            'state'                        => !empty($a->state) ? $a->state : null,
            'email'                        => $a->email        ?? null,
            'phone'                        => $a->phone        ?? null,
        ]);
    }

    private function buildDetails(object $json): array
    {
        $details = [
            'weight' => [
                'uom'   => 'kg',
                'value' => (float)($json->package->weight ?? 0),
            ],
        ];

        $l = $json->package->length ?? null;
        $w = $json->package->width  ?? null;
        $h = $json->package->height ?? null;
        if ($l !== null && $w !== null && $h !== null) {
            $details['dim'] = [
                'uom'    => 'cm',
                'length' => (int)$l,
                'width'  => (int)$w,
                'height' => (int)$h,
            ];
        }

        return $details;
    }

    private function buildCustoms(?object $decl): ?array
    {
        if ($decl === null || empty($decl->positions)) {
            return null;
        }

        $exportTypeMap = [
            ShipmentType::GOODS->value     => 'PERMANENT',
            ShipmentType::DOCUMENTS->value => 'DOCUMENTS',
            ShipmentType::GIFT->value      => 'PRESENT',
            ShipmentType::SAMPLE->value    => 'SAMPLE',
            ShipmentType::RETURN->value    => 'RETURN',
        ];
        $exportType = $exportTypeMap[(int)($decl->shipmentType ?? ShipmentType::GOODS->value)] ?? 'PERMANENT';

        $items = [];
        foreach ($decl->positions as $pos) {
            $item = [
                'itemDescription'  => $pos->description    ?? '',
                'packagedQuantity' => (int)($pos->quantity ?? 1),
                'itemValue'        => [
                    'currency' => 'EUR',
                    'value'    => round((float)($pos->itemValue ?? 0), 2),
                ],
                'itemWeight'       => [
                    'uom'   => 'kg',
                    'value' => round((float)($pos->itemWeight ?? 0), 3),
                ],
            ];
            if (!empty($pos->originCountryCode)) {
                $item['countryOfOrigin'] = self::toIso3($pos->originCountryCode);
            }
            if (!empty($pos->hsCode)) {
                $item['hsCode'] = $pos->hsCode;
            }
            $items[] = $item;
        }

        $customs = [
            'exportType' => $exportType,
            'items'      => $items,
        ];
        if (!empty($decl->invoiceNumber)) {
            $customs['invoiceNo'] = $decl->invoiceNumber;
        }

        return $customs;
    }

    /**
     * Extract PDF bytes from a label/customsDoc response object.
     * Prefers base64 inline, falls back to URL download.
     *
     * @throws DhlRestApiException
     */
    private function extractPdf(DhlRestApi $api, array $labelObj): string
    {
        if (!empty($labelObj['b64'])) {
            return base64_decode($labelObj['b64']);
        }
        if (!empty($labelObj['url'])) {
            return $api->fetchLabel($labelObj['url']);
        }
        throw new DhlRestApiException('Kein Label in der API-Antwort (weder b64 noch url)');
    }

    private function getBillingNumber(string $product): ?string
    {
        return match ($product) {
            'V01PAK'  => $this->settings->accountnumber         ?? null,
            'V53WPAK' => $this->settings->accountnumber_int     ?? null,
            'V54EPAK' => $this->settings->accountnumber_euro    ?? null,
            'V55PAK'  => $this->settings->accountnumber_connect ?? null,
            'V62WP'   => $this->settings->accountnumber_wp      ?? null,
            'V66WPI'  => $this->settings->accountnumber_wpint   ?? null,
            default   => null,
        };
    }

    /** Remove null and empty-string values recursively at top level. */
    private static function compactArray(array $arr): array
    {
        return array_filter($arr, fn($v) => $v !== null && $v !== '');
    }

    /**
     * Convert ISO 3166-1 alpha-2 → alpha-3.
     * Falls back to the input value unchanged if not found (allows callers
     * who already pass alpha-3 to work transparently).
     */
    private static function toIso3(string $iso2): string
    {
        static $map = [
            'AF' => 'AFG', 'AL' => 'ALB', 'DZ' => 'DZA', 'AD' => 'AND', 'AO' => 'AGO',
            'AG' => 'ATG', 'AR' => 'ARG', 'AM' => 'ARM', 'AU' => 'AUS', 'AT' => 'AUT',
            'AZ' => 'AZE', 'BS' => 'BHS', 'BH' => 'BHR', 'BD' => 'BGD', 'BB' => 'BRB',
            'BY' => 'BLR', 'BE' => 'BEL', 'BZ' => 'BLZ', 'BJ' => 'BEN', 'BT' => 'BTN',
            'BO' => 'BOL', 'BA' => 'BIH', 'BW' => 'BWA', 'BR' => 'BRA', 'BN' => 'BRN',
            'BG' => 'BGR', 'BF' => 'BFA', 'BI' => 'BDI', 'CV' => 'CPV', 'KH' => 'KHM',
            'CM' => 'CMR', 'CA' => 'CAN', 'CF' => 'CAF', 'TD' => 'TCD', 'CL' => 'CHL',
            'CN' => 'CHN', 'CO' => 'COL', 'KM' => 'COM', 'CG' => 'COG', 'CD' => 'COD',
            'CR' => 'CRI', 'HR' => 'HRV', 'CU' => 'CUB', 'CY' => 'CYP', 'CZ' => 'CZE',
            'DK' => 'DNK', 'DJ' => 'DJI', 'DO' => 'DOM', 'EC' => 'ECU', 'EG' => 'EGY',
            'SV' => 'SLV', 'GQ' => 'GNQ', 'ER' => 'ERI', 'EE' => 'EST', 'SZ' => 'SWZ',
            'ET' => 'ETH', 'FJ' => 'FJI', 'FI' => 'FIN', 'FR' => 'FRA', 'GA' => 'GAB',
            'GM' => 'GMB', 'GE' => 'GEO', 'DE' => 'DEU', 'GH' => 'GHA', 'GR' => 'GRC',
            'GT' => 'GTM', 'GN' => 'GIN', 'GW' => 'GNB', 'GY' => 'GUY', 'HT' => 'HTI',
            'HN' => 'HND', 'HU' => 'HUN', 'IS' => 'ISL', 'IN' => 'IND', 'ID' => 'IDN',
            'IR' => 'IRN', 'IQ' => 'IRQ', 'IE' => 'IRL', 'IL' => 'ISR', 'IT' => 'ITA',
            'JM' => 'JAM', 'JP' => 'JPN', 'JO' => 'JOR', 'KZ' => 'KAZ', 'KE' => 'KEN',
            'KI' => 'KIR', 'KP' => 'PRK', 'KR' => 'KOR', 'KW' => 'KWT', 'KG' => 'KGZ',
            'LA' => 'LAO', 'LV' => 'LVA', 'LB' => 'LBN', 'LS' => 'LSO', 'LR' => 'LBR',
            'LY' => 'LBY', 'LI' => 'LIE', 'LT' => 'LTU', 'LU' => 'LUX', 'MG' => 'MDG',
            'MW' => 'MWI', 'MY' => 'MYS', 'MV' => 'MDV', 'ML' => 'MLI', 'MT' => 'MLT',
            'MH' => 'MHL', 'MR' => 'MRT', 'MU' => 'MUS', 'MX' => 'MEX', 'FM' => 'FSM',
            'MD' => 'MDA', 'MC' => 'MCO', 'MN' => 'MNG', 'ME' => 'MNE', 'MA' => 'MAR',
            'MZ' => 'MOZ', 'MM' => 'MMR', 'NA' => 'NAM', 'NR' => 'NRU', 'NP' => 'NPL',
            'NL' => 'NLD', 'NZ' => 'NZL', 'NI' => 'NIC', 'NE' => 'NER', 'NG' => 'NGA',
            'MK' => 'MKD', 'NO' => 'NOR', 'OM' => 'OMN', 'PK' => 'PAK', 'PW' => 'PLW',
            'PA' => 'PAN', 'PG' => 'PNG', 'PY' => 'PRY', 'PE' => 'PER', 'PH' => 'PHL',
            'PL' => 'POL', 'PT' => 'PRT', 'QA' => 'QAT', 'RO' => 'ROU', 'RU' => 'RUS',
            'RW' => 'RWA', 'WS' => 'WSM', 'SM' => 'SMR', 'ST' => 'STP', 'SA' => 'SAU',
            'SN' => 'SEN', 'RS' => 'SRB', 'SC' => 'SYC', 'SL' => 'SLE', 'SG' => 'SGP',
            'SK' => 'SVK', 'SI' => 'SVN', 'SB' => 'SLB', 'SO' => 'SOM', 'ZA' => 'ZAF',
            'SS' => 'SSD', 'ES' => 'ESP', 'LK' => 'LKA', 'SD' => 'SDN', 'SR' => 'SUR',
            'SE' => 'SWE', 'CH' => 'CHE', 'SY' => 'SYR', 'TW' => 'TWN', 'TJ' => 'TJK',
            'TZ' => 'TZA', 'TH' => 'THA', 'TL' => 'TLS', 'TG' => 'TGO', 'TO' => 'TON',
            'TT' => 'TTO', 'TN' => 'TUN', 'TR' => 'TUR', 'TM' => 'TKM', 'TV' => 'TUV',
            'UG' => 'UGA', 'UA' => 'UKR', 'AE' => 'ARE', 'GB' => 'GBR', 'US' => 'USA',
            'UY' => 'URY', 'UZ' => 'UZB', 'VU' => 'VUT', 'VE' => 'VEN', 'VN' => 'VNM',
            'YE' => 'YEM', 'ZM' => 'ZMB', 'ZW' => 'ZWE',
        ];

        $upper = strtoupper($iso2);
        return $map[$upper] ?? $iso2;
    }
}
