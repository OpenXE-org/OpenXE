<?php

declare(strict_types=1);

namespace Xentral\Modules\AmazonVendorDF;

use Xentral\Components\SchemaCreator\Collection\SchemaCollection;
use Xentral\Components\SchemaCreator\Index;
use Xentral\Components\SchemaCreator\Option\TableOption;
use Xentral\Components\SchemaCreator\Schema\TableSchema;
use Xentral\Components\SchemaCreator\Type;
use Xentral\Core\DependencyInjection\ContainerInterface;

final class Bootstrap
{

    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'PurchaseOrderInformationRepository' => 'onInitPurchaseOrderInformationRepository',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PurchaseOrderInformationRepository
     */
    public static function onInitPurchaseOrderInformationRepository(ContainerInterface $container
    ): PurchaseOrderInformationRepository {
        return new PurchaseOrderInformationRepository(
            $container->get('Database')
        );
    }
}
