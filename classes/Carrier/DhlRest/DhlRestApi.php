<?php

/*
 * SPDX-FileCopyrightText: 2025 Andreas Palm
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

namespace Xentral\Carrier\DhlRest;

class DhlRestApiException extends \RuntimeException {}

class DhlRestApi
{
    private const SANDBOX_URL = 'https://api-sandbox.dhl.com/parcel/de/shipping/v2';
    private const PRODUCTION_URL = 'https://api-eu.dhl.com/parcel/de/shipping/v2';

    private string $baseUrl;

    public function __construct(
        private readonly string $user,
        private readonly string $password,
        private readonly string $apiKey,
        bool $sandbox = false
    ) {
        $this->baseUrl = $sandbox ? self::SANDBOX_URL : self::PRODUCTION_URL;
    }

    /**
     * POST /orders — create one shipment and return the raw decoded response array.
     *
     * @throws DhlRestApiException
     */
    public function createShipment(array $shipmentData): array
    {
        return $this->request('POST', '/orders', ['shipments' => [$shipmentData]]);
    }

    /**
     * Download a label (or export document) from a DHL URL and return raw PDF bytes.
     *
     * @throws DhlRestApiException
     */
    public function fetchLabel(string $url): string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => $this->user . ':' . $this->password,
            CURLOPT_HTTPHEADER     => ['dhl-api-key: ' . $this->apiKey],
            CURLOPT_TIMEOUT        => 30,
        ]);
        $body  = curl_exec($ch);
        $code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            throw new DhlRestApiException('cURL error: ' . $error);
        }
        if ($code !== 200) {
            throw new DhlRestApiException("Label-Download schlug fehl (HTTP $code)");
        }
        return $body;
    }

    /**
     * @throws DhlRestApiException
     */
    private function request(string $method, string $path, ?array $data = null): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $this->baseUrl . $path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => $this->user . ':' . $this->password,
            CURLOPT_HTTPHEADER     => [
                'dhl-api-key: ' . $this->apiKey,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT        => 30,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        }

        $body  = curl_exec($ch);
        $code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            throw new DhlRestApiException('cURL error: ' . $error);
        }

        $decoded = json_decode($body, true);
        if ($decoded === null) {
            throw new DhlRestApiException(
                "Ungültige API-Antwort (HTTP $code): " . substr($body, 0, 500)
            );
        }

        return $decoded;
    }
}
