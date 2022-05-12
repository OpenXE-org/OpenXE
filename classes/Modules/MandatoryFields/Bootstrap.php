<?php

declare(strict_types=1);

namespace Xentral\Modules\MandatoryFields;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\MandatoryFields\Service\MandatoryFieldsGateway;
use Xentral\Modules\MandatoryFields\Service\MandatoryFieldsService;
use Xentral\Modules\MandatoryFields\Service\MandatoryFieldsValidator;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'MandatoryFieldsModule' => 'onInitMandatoryFieldsModule',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return MandatoryFieldsModule
     */
    public static function onInitMandatoryFieldsModule(ContainerInterface $container): MandatoryFieldsModule
    {
        return new MandatoryFieldsModule(
            self::onInitMandatoryFieldsGateway($container),
            self::onInitMandatoryFieldsService($container),
            self::onInitMandatoryFieldsValidator($container)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return MandatoryFieldsGateway
     */
    private static function onInitMandatoryFieldsGateway(ContainerInterface $container): MandatoryFieldsGateway
    {
        return new MandatoryFieldsGateway($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return MandatoryFieldsService
     */
    private static function onInitMandatoryFieldsService(ContainerInterface $container): MandatoryFieldsService
    {
        return new MandatoryFieldsService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return MandatoryFieldsValidator
     */
    private static function onInitMandatoryFieldsValidator(ContainerInterface $container): MandatoryFieldsValidator
    {
        return new MandatoryFieldsValidator(
            self::onInitMandatoryFieldsGateway($container)
        );
    }
}
