<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient;

use Xentral\Components\MailClient\Client\MimeMessageFormatter;
use Xentral\Components\MailClient\Client\MimeMessageFormatterInterface;
use Xentral\Core\DependencyInjection\ServiceContainer;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'MailClientFactory'              => 'onInitMailClientFactory',
            'MailClientMimeMessageFormatter' => 'onInitMimeMessageFormatter',
        ];
    }

    /**
     * @param ServiceContainer $container
     *
     * @return MailClientFactory
     */
    public static function onInitMailClientFactory(ServiceContainer $container): MailClientFactory
    {
        return new MailClientFactory();
    }

    /**
     * @param ServiceContainer $container
     *
     * @return MimeMessageFormatterInterface
     */
    public static function onInitMimeMessageFormatter(ServiceContainer $container): MimeMessageFormatterInterface
    {
        return new MimeMessageFormatter();
    }
}
