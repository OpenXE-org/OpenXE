<?php

namespace Xentral\Modules\ImportTemplate;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\ImportTemplate\Service\ImportTemplateGateway;
use Xentral\Modules\ImportTemplate\Service\ImportTemplateJsonService;
use Xentral\Modules\ImportTemplate\Service\ImportTemplateService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'ImportTemplateJsonService' => 'onInitImportTemplateJsonService',
            'ImportTemplateService'     => 'onInitImportTemplateService',
            'ImportTemplateGateway'     => 'onInitImportTemplateGateway',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ImportTemplateJsonService
     */
    public static function onInitImportTemplateJsonService(ContainerInterface $container)
    {
        return new ImportTemplateJsonService(
            $container->get('ImportTemplateService'),
            $container->get('ImportTemplateGateway')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ImportTemplateService
     */
    public function onInitImportTemplateService(ContainerInterface $container)
    {
        return new ImportTemplateService(
            $container->get('Database')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ImportTemplateGateway
     */
    public function onInitImportTemplateGateway(ContainerInterface $container)
    {
        return new ImportTemplateGateway(
            $container->get('Database')
        );
    }
}
