<?php

declare(strict_types=1);

namespace Xentral\Modules\EbayPayment;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\EbayPayment\Service\EbayPaymentDocumentService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            EbayPaymentDocumentService::class => 'onInitEbayPaymentDocumentService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return EbayPaymentDocumentService
     */
    public static function onInitEbayPaymentDocumentService(ContainerInterface $container): EbayPaymentDocumentService
    {
        return new EbayPaymentDocumentService($container->get('Database'));
    }
}
