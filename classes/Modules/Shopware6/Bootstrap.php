<?php

namespace Xentral\Modules\Shopware6;

use Xentral\Components\HttpClient\HttpClient;
use Xentral\Modules\Shopware6\Client\Shopware6Client;

final class Bootstrap{

    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'Shopware6Client' => 'onInitShopware6Client',
        ];
    }

    /**
     * @return Shopware6Client
     */
    public static function onInitShopware6Client(): Shopware6Client
    {
        return new Shopware6Client(new HttpClient());
    }
}
