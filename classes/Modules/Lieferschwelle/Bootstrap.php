<?php
/*
 * SPDX-FileCopyrightText: 2025 Andreas Palm
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

namespace Xentral\Modules\Lieferschwelle;

use Xentral\Core\DependencyInjection\ContainerInterface;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'LieferschwelleGateway' => 'onInitLieferschwelleGateway',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return LieferschwelleGateway
     */
    public static function onInitLieferschwelleGateway(ContainerInterface $container) : LieferschwelleGateway
    {
        return new LieferschwelleGateway($container->get('Database'));
    }
}
