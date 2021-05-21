<?php

namespace Xentral\Modules\SystemTemplates;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\SystemTemplates\Validator\MetaDataValidation;
use Xentral\Modules\SystemTemplates\Validator\Ruleset;
use Xentral\Modules\SystemTemplates\Validator\SystemTemplateValidator;

final class Bootstrap
{

    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'SystemTemplatesService' => 'onInitSystemTemplatesService',
            'SystemTemplatesGateway' => 'onInitSystemTemplatesGateway',
            'MetaDataValidation'     => 'onInitMetaDataValidation',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SystemTemplatesService
     */
    public static function onInitSystemTemplatesService(ContainerInterface $container)
    {
        /** @var string $templateFilePath */
        $templateFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;

        return new SystemTemplatesService(
            $container->get('SystemTemplatesGateway'),
            $container->get('DatabaseBackup'),
            $container->get('FileBackup'),
            $container->get('BackupService'),
            $container->get('Database'),
            $container->get('MetaDataValidation'),
            $container->get('BackupLog'),
            $templateFilePath
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SystemTemplatesGateway
     */
    public static function onInitSystemTemplatesGateway(ContainerInterface $container)
    {
        return new SystemTemplatesGateway($container->get('Database'), $container->get('BackupGateway'));
    }

    /**
     * @return SystemTemplateValidator
     */
    public static function onInitMetaDataValidation()
    {
        /** @var string $templateFilePath */
        $templateFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;

        return new SystemTemplateValidator(new MetaDataValidation(new Ruleset(),$templateFilePath));
    }
}