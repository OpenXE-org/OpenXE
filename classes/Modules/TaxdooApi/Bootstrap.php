<?php

namespace Xentral\Modules\TaxdooApi;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\TaxdooApi\Exception\ConfigurationMissingException;
use Xentral\Modules\TaxdooApi\Exception\KeyNotFoundException;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'TaxdooApiService' => 'onInitTaxdooApiService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @throws KeyNotFoundException|ConfigurationMissingException
     *
     * @return TaxdooApiService
     */
    public static function onInitTaxdooApiService(ContainerInterface $container)
    {
        $app = $container->get('LegacyApplication');

        $token = $app->erp->GetKonfiguration('taxdoo_token');
        if ($token === '') {
            throw new KeyNotFoundException();
        }

        $land = $app->erp->GetKonfiguration('taxdoo_land');
        if (strlen($land) !== 2) {
            throw new ConfigurationMissingException('Land muss ISO-2 sein');
        }

        return new TaxdooApiService($token);
    }
}
