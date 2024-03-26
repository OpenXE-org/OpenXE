<?php
/*
 * SPDX-FileCopyrightText: 2023 Andreas Palm
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

namespace Xentral\Modules\MatrixProduct;

use Xentral\Core\DependencyInjection\ContainerInterface;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'MatrixProductService' => 'onInitMatrixProductService',
            'MatrixProductGateway' => 'onInitMatrixProductGateway',
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
     * @return MatrixProductGateway
     */
    public static function onInitMatrixProductGateway(ContainerInterface $container) : MatrixProductGateway
    {
        return new MatrixProductGateway($container->get('Database'));
    }
}
