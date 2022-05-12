<?php

declare(strict_types=1);

namespace Xentral\Modules\ShopimporterAmazon;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\ShopimporterAmazon\Service\AmazonDocumentService;
use Xentral\Modules\ShopimporterAmazon\Service\InvoiceUploadDocumentService;
use Xentral\Modules\ShopimporterAmazon\Service\InvoiceUploadQueueService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            AmazonDocumentService::class        => 'onInitAmazonDocumentService',
            InvoiceUploadDocumentService::class => 'onInitInvoiceUploadDocumentService',
            InvoiceUploadQueueService::class    => 'onInitInvoiceUploadQueueService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return AmazonDocumentService
     */
    public static function onInitAmazonDocumentService(ContainerInterface $container): AmazonDocumentService
    {
        return new AmazonDocumentService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return InvoiceUploadDocumentService
     */
    public static function onInitInvoiceUploadDocumentService(ContainerInterface $container
    ): InvoiceUploadDocumentService {
        return new InvoiceUploadDocumentService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return InvoiceUploadQueueService
     */
    public static function onInitInvoiceUploadQueueService(ContainerInterface $container): InvoiceUploadQueueService
    {
        return new InvoiceUploadQueueService($container->get('Database'));
    }
}
