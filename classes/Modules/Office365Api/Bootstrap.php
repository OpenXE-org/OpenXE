<?php

declare(strict_types=1);

namespace Xentral\Modules\Office365Api;

use ApplicationCore;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Office365Api\Service\Office365AccountGateway;
use Xentral\Modules\Office365Api\Service\Office365AuthorizationService;
use Xentral\Modules\Office365Api\Service\Office365CredentialsService;
use Xentral\Modules\Office365Api\Wrapper\CompanyConfigWrapper;

final class Bootstrap
{
    public static function registerServices(): array
    {
        return [
            'Office365CredentialsService' => 'onInitOffice365CredentialsService',
            'Office365AccountGateway' => 'onInitOffice365AccountGateway',
            'Office365AuthorizationService' => 'onInitOffice365AuthorizationService',
        ];
    }

    public static function onInitOffice365CredentialsService(ContainerInterface $container): Office365CredentialsService
    {
        return new Office365CredentialsService(
            self::onInitCompanyConfigWrapper($container)
        );
    }

    public static function onInitOffice365AccountGateway(ContainerInterface $container): Office365AccountGateway
    {
        return new Office365AccountGateway($container->get('Database'));
    }

    public static function onInitOffice365AuthorizationService(ContainerInterface $container): Office365AuthorizationService
    {
        return new Office365AuthorizationService(
            $container->get('Office365AccountGateway'),
            $container->get('Office365CredentialsService')
        );
    }

    private static function onInitCompanyConfigWrapper(ContainerInterface $container): CompanyConfigWrapper
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new CompanyConfigWrapper($app->erp);
    }
}
