<?php

declare(strict_types=1);

namespace Xentral\Components\HttpClient;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'HttpClientFactory' => 'onInitHttpClientFactory',
        ];
    }

    /**
     * @return HttpClientFactory
     */
    public static function onInitHttpClientFactory(): HttpClientFactory
    {
        return new HttpClientFactory();
    }
}
