<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemMailClient;

use Xentral\Core\DependencyInjection\ServiceContainer;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'MailClientConfigProvider' => 'onInitMailClientConfigProvider',
            'MailClientProvider'       => 'onInitMailClientProvider',
        ];
    }

    /**
     * @param ServiceContainer $container
     *
     * @return MailClientConfigProvider
     */
    public static function onInitMailClientConfigProvider(ServiceContainer $container): MailClientConfigProvider
    {
        return new MailClientConfigProvider(
            $container->get('EmailAccountGateway'),
            $container->get('GoogleAccountGateway'),
            $container->get('GoogleApiClientFactory')
        );
    }

    /**
     * @param ServiceContainer $container
     *
     * @return MailClientProvider
     */
    public static function onInitMailClientProvider(ServiceContainer $container): MailClientProvider
    {
        return new MailClientProvider(
            $container->get('MailClientFactory'),
            $container->get('MailClientConfigProvider'),
            $container->get('EmailAccountGateway')
        );
    }
}
