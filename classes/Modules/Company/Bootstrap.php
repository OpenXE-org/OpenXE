<?php

namespace Xentral\Modules\Company;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Company\Service\DocumentCustomizationService;
use Xentral\Modules\Company\Service\DocumentCustomizationBlockParser;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'DocumentCustomizationService' => 'onInitDocumentCustomizationService',
            'DocumentCustomizationBlockParser' => 'onInitDocumentCustomizationBlockParser',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DocumentCustomizationService
     */
    public static function onInitDocumentCustomizationService(ContainerInterface $container)
    {
        return new DocumentCustomizationService(
            $container->get('Database'),
            $container->get('DocumentCustomizationBlockParser')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DocumentCustomizationBlockParser
     */
    public static function onInitDocumentCustomizationBlockParser(ContainerInterface $container)
    {
        $erp = $container->get('LegacyApplication')->erp;
        return new DocumentCustomizationBlockParser($erp);
    }
}