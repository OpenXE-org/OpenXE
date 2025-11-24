<?php
/*
 * SPDX-FileCopyrightText: 2025 Andreas Palm
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

namespace Xentral\Modules\CrossSelling;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\MatrixProduct\MatrixProductGateway;
use Xentral\Modules\MatrixProduct\MatrixProductService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'CrossSellingGateway' => 'onInitCrossSellingGateway',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return MatrixProductService
     */
    public static function onInitMatrixProductService(ContainerInterface $container) : MatrixProductService
    {
        return new MatrixProductService(
            $container->get('Database'),
            $container->get('MatrixProductGateway'),
            $container->get('ArticleGateway')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return CrossSellingGateway
     */
    public static function onInitCrossSellingGateway(ContainerInterface $container) : CrossSellingGateway
    {
        return new CrossSellingGateway($container->get('Database'));
    }
}
