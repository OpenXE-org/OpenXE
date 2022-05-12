<?php

namespace Xentral\Modules\DocuvitaSolutionsApi;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Core\DependencyInjection\ServiceContainer;
use Xentral\Modules\DocuvitaApi\Exception\ConfigurationMissingException;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'DocuvitaSolutionsApiService' => 'onInitDocuvitaSolutionsApiService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @throws ConfigurationMissingException
     *
     * @return DocuvitaSolutionsApiService
     */
    public static function onInitDocuvitaSolutionsApiService(ContainerInterface $container)
    {
        /** @var \Application $app */
        $app = $container->get('LegacyApplication');

        return new DocuvitaSolutionsApiService(
            $app,
            $app->erp->GetKonfiguration('docuvitasolutions_import_user'),
            $app->erp->GetKonfiguration('docuvitasolutions_import_password'),
            $app->erp->GetKonfiguration('docuvitasolutions_url'),
            $app->erp->GetKonfiguration('docuvitasolutions_start_date'),
            $app->erp->GetKonfiguration('docuvitasolutions_mandant'),
            $app->erp->GetKonfiguration('docuvitasolutions_template_address'),
            $app->erp->GetKonfiguration('docuvitasolutions_template_project'),
            $app->erp->GetKonfiguration('docuvitasolutions_template_offer'),
            $app->erp->GetKonfiguration('docuvitasolutions_template_invoice'),
            $app->erp->GetKonfiguration('docuvitasolutions_template_credit'),
            $app->erp->GetKonfiguration('docuvitasolutions_template_delivery_note'),
            $app->erp->GetKonfiguration('docuvitasolutions_template_order'),
            $app->erp->GetKonfiguration('docuvitasolutions_template_order_in'),
            $app->erp->GetKonfiguration('docuvitasolutions_template_verbindlichkeit')
        );
    }
}