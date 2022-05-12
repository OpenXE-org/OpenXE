<?php

namespace Xentral\Modules\AmaInvoice;

use Xentral\Core\DependencyInjection\ContainerInterface;

use Xentral\Modules\AmaInvoice\Scheduler\AmaInvoiceTask;
use Xentral\Modules\AmaInvoice\Service\AmaInvoiceService;
use Xentral\Modules\SuperSearch\Wrapper\CompanyConfigWrapper;


final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'AmaInvoiceService' => 'onInitAmaInvoiceService',
            // Cronjob-Tasks
            'AmaInvoiceTask'   => 'onInitAmaInvoiceTask',
        ];
    }


    /**
     * @param ContainerInterface $container
     *
     * @return AmaInvoiceTask
     */
    public static function onInitAmaInvoiceTask(ContainerInterface $container)
    {
        return new AmaInvoiceTask(
            $container->get('AmaInvoiceService'),
            self::onInitCompanyConfigWrapper($container)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return AmaInvoiceService
     */
    public static function onInitAmaInvoiceService(ContainerInterface $container)
    {
        return new AmaInvoiceService(
            $container->get('Database'),
            $container->get('FilesystemFactory'),
            $container->get('LegacyApplication')
        );
    }


    /**
     * @param ContainerInterface $container
     *
     * @return CompanyConfigWrapper
     */
    private static function onInitCompanyConfigWrapper(ContainerInterface $container)
    {
        /** @var \ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new CompanyConfigWrapper($app->erp);
    }
}
