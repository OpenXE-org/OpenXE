<?php

namespace Xentral\Modules\Pos;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Pos\Service\PosJournalService;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'PosJournalService' => 'onInitPosJournalService',
        ];
    }



    /**
     * @param ContainerInterface $container
     *
     * @return PosJournalService
     */
    public static function onInitPosJournalService(ContainerInterface $container)
    {
        return new PosJournalService($container->get('Database'));
    }

}