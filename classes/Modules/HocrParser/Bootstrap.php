<?php

declare(strict_types=1);

namespace Xentral\Modules\HocrParser;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\HocrParser\Service\HocrDataExtractor;
use Xentral\Modules\HocrParser\Service\HocrParser;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'HocrParser' => 'onInitHocrParser',
            'HocrDataExtractor' => 'onInitHocrDataExtractor',
        ];
    }

    /**
     * @return HocrParser
     */
    public static function onInitHocrParser(): HocrParser
    {
        return new HocrParser();
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HocrDataExtractor
     */
    public static function onInitHocrDataExtractor(ContainerInterface $container): HocrDataExtractor
    {
        $app = $container->get('LegacyApplication');

        return new HocrDataExtractor($container->get('HocrParser'), $app->erp->GetWaehrung());
    }
}
