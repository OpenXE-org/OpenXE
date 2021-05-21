<?php

namespace Xentral\Modules\FeeReduction;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\FeeReduction\Gateway\FeeReductionGateway;
use Xentral\Modules\FeeReduction\Service\FeeReductionService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'FeeReductionService' => 'onInitFeeReductionService',
            'FeeReductionGateway' => 'onInitFeeReductionGateway',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return FeeReductionService
     */
    public static function onInitFeeReductionService(ContainerInterface $container)
    {
        return new FeeReductionService(
            $container->get('Database'), $container->get('FeeReductionGateway')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return FeeReductionGateway
     */
    public static function onInitFeeReductionGateway(ContainerInterface $container)
    {
        return new FeeReductionGateway(
            $container->get('Database')
        );
    }
}