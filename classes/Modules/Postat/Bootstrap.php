<?php

declare(strict_types=1);

namespace Xentral\Modules\Postat;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Postat\SOAP\SoapServiceFactory;

final class Bootstrap
{
    public static function registerServices(): array
    {
        return [
            'PostAtSoapClientFactory' => 'onInitPostAtSoapClientFactory',
        ];
    }

    public static function onInitPostAtSoapClientFactory(ContainerInterface $container): SoapServiceFactory
    {
        $logger = $container->get('Logger');

        return new SoapServiceFactory($logger);
    }
}
