<?php

namespace Xentral\Modules\DocuvitaApi;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\DocuvitaApi\Exception\ConfigurationMissingException;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'DocuvitaApiService' => 'onInitDocuvitaApiService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @throws ConfigurationMissingException
     *
     * @return DocuvitaApiService
     */
    public static function onInitDocuvitaApiService(ContainerInterface $container)
    {
        $app = $container->get('LegacyApplication');

        $key = $app->erp->GetKonfiguration('docuvita_password');
        if (!isset($key) || empty($key)) {
            throw new ConfigurationMissingException('API key missing');
        }

        $url = $app->erp->GetKonfiguration('docuvita_url');
        if (!isset($url) || empty($url)) {
            throw new ConfigurationMissingException('Endpoint missing');
        }

        $classIDFolder = (int)$app->erp->GetKonfiguration('docuvita_class_id_folder');
        $classIDReceipt = (int)$app->erp->GetKonfiguration('docuvita_class_id_receipt');

        return new DocuvitaApiService($key, $url, 'Xentral', $classIDFolder, $classIDReceipt);
    }
}
