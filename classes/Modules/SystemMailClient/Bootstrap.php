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
        $office365Gateway = $container->has('Office365AccountGateway')
            ? $container->get('Office365AccountGateway')
            : null;
        $office365AuthService = $container->has('Office365AuthorizationService')
            ? $container->get('Office365AuthorizationService')
            : null;

        return new MailClientConfigProvider(
            $container->get('EmailAccountGateway'),
            $container->get('GoogleAccountGateway'),
            $container->get('GoogleApiClientFactory'),
            $office365Gateway,
            $office365AuthService
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
