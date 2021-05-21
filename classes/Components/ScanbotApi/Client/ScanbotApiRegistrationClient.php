<?php

declare(strict_types=1);

namespace Xentral\Components\ScanbotApi\Client;

use Xentral\Components\ScanbotApi\Exception\RuntimeException;

class ScanbotApiRegistrationClient
{
    /** @var string $apiUrl */
    private $apiUrl;

    /** @var string $clientId ClientID von Xentral bei freigeist (ist gleich für alle Xentral-Kunden) */
    private $clientId;

    /**
     * @param string $apiUrl
     * @param string $clientId
     */
    public function __construct(string $apiUrl, string $clientId)
    {
        if (empty($apiUrl)) {
            throw new RuntimeException('ApiURL can not be empty.');
        }
        if (empty($clientId)) {
            throw new RuntimeException('ClientID can not be empty.');
        }
        $this->apiUrl = $apiUrl;
        $this->clientId = $clientId;
    }

    /**
     * @param string $companyMail Pro Mailadresse kann nur ein ApiKey registriert werden
     * @param string $companyName
     *
     * @throws RuntimeException
     *
     * @return array
     */
    public function register(string $companyMail, string $companyName): array
    {
        if (empty($companyMail)) {
            throw new RuntimeException('Mail parameter can not be empty.');
        }
        if (empty($companyName)) {
            throw new RuntimeException('Name parameter can not be empty.');
        }

        $url = $this->apiUrl . '/createApiKey?' . sprintf(
                'email=%s&company=%s&client_id=%s',
                rawurlencode($companyMail),
                rawurlencode($companyName),
                $this->clientId
            );

        $client = new CurlHttpClient('POST', $url);

        if ($client->HasError()) {
            throw new RuntimeException(sprintf('Curl-Fehler: %s', $client->GetErrorMessage()));
        }

        $result = $client->GetContent();
        $arrayResult = json_decode($result, true);

        if (json_last_error() > 0) {
            throw new RuntimeException(sprintf('JSON-Fehler: %s', json_last_error_msg()));
        }

        if (isset($arrayResult['error'])) {
            throw new RuntimeException(sprintf('API-Fehler: %s', $arrayResult['error']));
        }

        return $arrayResult;
    }
}
