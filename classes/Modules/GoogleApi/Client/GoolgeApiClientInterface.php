<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleApi\Client;

use Xentral\Modules\GoogleApi\Data\GoogleAccountData;

interface GoolgeApiClientInterface
{
    /**
     * @return GoogleAccountData
     */
    public function getAccount(): GoogleAccountData;

    /**
     * @param string     $method
     * @param string     $uri
     * @param array|null $data
     * @param array|null $headers
     *
     * @return array
     */
    public function sendRequest(string $method, string $uri, array $data = null, array $headers = []): array;
}
