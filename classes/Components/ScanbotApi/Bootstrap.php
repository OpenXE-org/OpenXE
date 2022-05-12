<?php

declare(strict_types=1);

namespace Xentral\Components\ScanbotApi;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'ScanbotApiClientFactory' => 'onInitScanbotApiClientFactory',
        ];
    }

    /**
     * @return ScanBotApiClientFactory
     */
    public static function onInitScanbotApiClientFactory()
    {
        return new ScanbotApiClientFactory();
    }
}
