<?php

declare(strict_types=1);

namespace Xentral\Components\ScanbotApi;

use Xentral\Components\ScanbotApi\Client\ScanbotApiOcrClient;
use Xentral\Components\ScanbotApi\Client\ScanbotApiRegistrationClient;

class ScanbotApiClientFactory
{
    /**
     * @param string $url
     * @param string $clientId
     *
     * @return ScanbotApiRegistrationClient
     */
    public function createRegistrationClient(string $url, string $clientId): ScanbotApiRegistrationClient
    {
        return new ScanbotApiRegistrationClient($url, $clientId);
    }

    /**
     * @param string $url
     * @param string $apikey
     *
     * @return ScanbotApiOcrClient
     */
    public function createOcrClient(string $url, string $apikey): ScanbotApiOcrClient
    {
        return new ScanbotApiOcrClient($url, $apikey);
    }
}
