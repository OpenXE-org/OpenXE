<?php

declare(strict_types=1);

namespace Xentral\Modules\PaymentTransaction;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\PaymentTransaction\Service\PaymentDocumentService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'PaymentDocumentService' => 'onInitPaymentDocumentService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PaymentDocumentService
     */
    public static function onInitPaymentDocumentService(ContainerInterface $container): PaymentDocumentService
    {
        return new PaymentDocumentService($container->get('Database'));
    }
}
