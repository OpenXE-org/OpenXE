<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemMailer;

use Xentral\Core\DependencyInjection\ServiceContainer;
use Xentral\Modules\SystemMailer\Service\EmailAccountGateway;
use Xentral\Modules\SystemMailer\Service\MailBodyCleaner;
use Xentral\Modules\SystemMailer\Service\MailerTransportFactory;
use Xentral\Modules\SystemMailer\Service\MailLogService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'SystemMailer'           => 'onInitMailer',
            'MailLogService'         => 'onInitMailLogService',
            'MailerTransportFactory' => 'onInitMailerTransportFactory',
            'EmailAccountGateway'    => 'onInitEmailAccountGateway',
        ];
    }

    /**
     * @param ServiceContainer $container
     *
     * @return SystemMailer
     */
    public static function onInitMailer(ServiceContainer $container): SystemMailer
    {
        return new SystemMailer(
            $container->get('MailerTransportFactory'),
            $container->get('EmailAccountGateway'),
            $container->get('MailLogService'),
            new MailBodyCleaner()
        );
    }

    /**
     * @param ServiceContainer $container
     *
     * @return MailLogService
     */
    public static function onInitMailLogService(ServiceContainer $container): MailLogService
    {
        return new MailLogService($container->get('Database'));
    }

    /**
     * @param ServiceContainer $container
     *
     * @return EmailAccountGateway
     */
    public static function onInitEmailAccountGateway(ServiceContainer $container): EmailAccountGateway
    {
        return new EmailAccountGateway($container->get('Database'));
    }

    /**
     * @param ServiceContainer $container
     *
     * @return MailerTransportFactory
     */
    public static function onInitMailerTransportFactory(ServiceContainer $container): MailerTransportFactory
    {
        return new MailerTransportFactory($container);
    }
}
