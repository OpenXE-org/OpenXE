<?php

declare(strict_types=1);

namespace Xentral\Components\ScanbotApi\Client;

use Xentral\Components\ScanbotApi\Exception\RuntimeException;

class ScanbotApiOcrClient
{
    /** @var string $apiUrl */
    private $apiUrl;

    /** @var string $apiKey */
    private $apiKey;

    /** @var array $result */
    private $result;

    /**
     * @var string $resultHandle
     *  Referenz-ID zum Invoice-Recognition-Task
     *  Unter dem Handle lässt sich das Ergebnis der OCR-Erkennung abrufen.
     *  Das Handle wird auch benötigt um die korrigierten Daten zurückzumelden.
     */
    private $resultHandle;

    /**
     * @param string $apiUrl
     * @param string $apiKey
     */
    public function __construct(string $apiUrl, string $apiKey)
    {
        if (empty($apiUrl)) {
            throw new RuntimeException('Api-URL can not be empty.');
        }
        if (empty($apiKey)) {
            throw new RuntimeException('Api-Key can not be empty.');
        }
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $filePath     Absoluter Dateipfad
     * @param string $fileMimeType [image/jpeg|application/pdf]
     *
     * @throws RuntimeException
     *
     * @return void
     */
    public function fetchApi(string $filePath, string $fileMimeType): void
    {
        // Datei hochladen + FileHandle abholen
        $fileHandle = $this->fetchFileHandle($filePath, $fileMimeType);

        // Handle zum Abfragen es Ergebnisses abholen
        $this->resultHandle = $this->fetchResultHandle($fileHandle);

        // Anhand des ResultHandles das Ergebnis abholen
        $this->pollResult($this->resultHandle);
    }

    /**
     * API-Ergebnis als Array
     *
     * Das Ergebnis hat folgende Struktur:
     * [
     *    'IBAN' => array|null,
     *    'invoiceDate' => array|null,
     *    'invoiceNumber' => array|null,
     *    'orderId' => array|null,
     *    'totalAmount' => array|null,
     *    'totalTax' => array|null,
     *    'hocrOutput' => string,
     * ]
     *
     * @throws RuntimeException
     *
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @return string
     */
    public function getResultHandle(): string
    {
        return $this->resultHandle;
    }

    /**
     * @param string $filePath
     * @param string $fileMimeType
     *
     * @return string
     */
    private function fetchFileHandle(string $filePath, string $fileMimeType): string
    {
        if (!is_file($filePath)) {
            throw new RuntimeException(sprintf('Datei "%s" nicht gefunden.', $filePath));
        }

        $url = $this->apiUrl . '/file';
        $header = [
            'Content-Type: ' . $fileMimeType,
            'x-api-key: ' . $this->apiKey,
        ];
        $curlFile = curl_file_create($filePath, $fileMimeType);

        $client = new CurlHttpClient('PUT', $url, $header, [$curlFile]);

        if ($client->HasError()) {
            throw new RuntimeException(sprintf('Curl-Fehler: %s', $client->GetErrorMessage()));
        }

        $result = $client->GetContent();
        $arrayResult = json_decode($result, true);

        if (json_last_error() > 0) {
            throw new RuntimeException(sprintf('JSON-Fehler: %s', json_last_error_msg()));
        }

        if (!empty($arrayResult['message'])) {
            throw new RuntimeException(sprintf('API-Fehler: %s', $arrayResult['message']));
        }

        return $arrayResult['handle'];
    }

    /**
     * @param string $fileHandle
     *
     * @return string
     */
    private function fetchResultHandle(string $fileHandle): string
    {
        $url = $this->apiUrl . '/invoice/' . $fileHandle;
        $header = [
            'Accept: */*',
            'x-api-key: ' . $this->apiKey,
        ];

        $client = new CurlHttpClient('POST', $url, $header);

        if ($client->HasError()) {
            throw new RuntimeException(sprintf('Curl-Fehler: %s', $client->GetErrorMessage()));
        }

        $result = $client->GetContent();
        $arrayResult = json_decode($result, true);

        if (json_last_error() > 0) {
            throw new RuntimeException(sprintf('JSON-Fehler: %s', json_last_error_msg()));
        }

        if (!empty($arrayResult['message'])) {
            throw new RuntimeException(sprintf('API-Fehler: %s', $arrayResult['message']));
        }

        return $arrayResult['handle'];
    }

    /**
     * @param string $resultHandle
     *
     * @throws RuntimeException
     *
     * @return string
     */
    private function pollResult(string $resultHandle)
    {
        for ($try = 1; $try < 7; $try++) {
            $statusCode = $this->sendPollRequest($resultHandle);
            if ($statusCode === 200) {
                // Beim HTTP-Status 200 ist entweder ein Ergebnis zurückgekommen, oder ein Fehler > Schleife beenden
                break;
            }

            // Beim HTTP-Staus 404 ist noch kein Ergebnis verfügbar > Kurze Pause und weiter pollen...
            sleep(5);
        }

        // Schleife ist ergebnislos durchgelaufen
        if ($this->result === null) {
            throw new RuntimeException('Timeout: Kein Ergebnis von der API.');
        }

        // Ergebnis ist da > Versuchen JSON zu lesen
        $arrayResult = json_decode($this->result, true);
        if (json_last_error() > 0) {
            throw new RuntimeException(sprintf('JSON-Fehler: %s', json_last_error_msg()));
        }

        if (isset($arrayResult['message'])) {
            throw new RuntimeException(sprintf('API-Meldung: %s', $arrayResult['message']));
        }
        if (isset($arrayResult['errorCode']) && isset($arrayResult['error'])) {
            throw new RuntimeException(
                sprintf('API-Fehler: Code #%s %s', $arrayResult['errorCode'], $arrayResult['error'])
            );
        }

        // Wenn Programm bis hierhin durchgelaufen ist,
        // dann ist $this->result mit API-Ergebnis gefüllt
        $this->result = $arrayResult;
    }

    /**
     * @param string $resultHandle
     *
     * @return int HTTP-Statuscode
     */
    private function sendPollRequest(string $resultHandle): int
    {
        $url = $this->apiUrl . '/file/' . $resultHandle;
        $header = [
            'Accept: */*',
            'x-api-key: ' . $this->apiKey,
        ];

        $client = new CurlHttpClient('GET', $url, $header);
        $content = $client->GetContent();
        $httpCode = $client->GetStatusCode();

        if ($client->HasError()) {
            throw new RuntimeException(sprintf('Curl-Fehler: %s', $client->GetErrorMessage()));
        }

        // Beim HTTP-Status 200 ist entweder ein Ergebnis zurückgekommen, oder ein Fehler; aber immer als JSON
        // Beim HTTP-Staus 404 ist noch kein Ergebnis verfügbar > nochmal pollen...
        if ($httpCode === 200) {
            $this->result = $content;
        }

        return $httpCode;
    }
}
