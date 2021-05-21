<?php

declare(strict_types=1);

namespace Xentral\Components\HttpClient;

final class HttpClientFactory
{
    /**
     * @param RequestOptions|null $options
     *
     * @return HttpClientInterface
     */
    public function createClient(RequestOptions $options = null): HttpClientInterface
    {
        if ($options === null) {
            $options = new RequestOptions();
        }

        return new HttpClient($options);
    }
}
