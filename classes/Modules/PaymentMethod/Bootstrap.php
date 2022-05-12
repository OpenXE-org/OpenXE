<?php

declare(strict_types=1);

namespace Xentral\Modules\PaymentMethod;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\PaymentMethod\Service\PaymentMethodService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'PaymentMethodService' => 'onInitPaymentMethodService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PaymentMethodService
     */
    public static function onInitPaymentMethodService(ContainerInterface $container): PaymentMethodService
    {
        return new PaymentMethodService($container->get('Database'));
    }
}
