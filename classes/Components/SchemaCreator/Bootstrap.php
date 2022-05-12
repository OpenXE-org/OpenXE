<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator;

use Xentral\Components\SchemaCreator\Adapter\Driver\MysqlDriver;
use Xentral\Components\SchemaCreator\Collection\SchemaCollection;
use Xentral\Components\SchemaCreator\Exception\SchemaCreatorMissingDriverException;
use Xentral\Components\SchemaCreator\LineGenerator\Common\ColumnLineGenerator;
use Xentral\Components\SchemaCreator\LineGenerator\Common\ConstraintLineGenerator;
use Xentral\Components\SchemaCreator\LineGenerator\Common\IndexLineGenerator;
use Xentral\Components\SchemaCreator\LineGenerator\Common\TableOptionsGenerator;
use Xentral\Core\DependencyInjection\ServiceContainer;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'SchemaCreator' => 'onInitSchemaCreator',
        ];
    }

    /**
     * @param SchemaCollection $collection
     *
     * @return SchemaCollection
     */
    public static function registerTableSchemas(SchemaCollection $collection): SchemaCollection
    {
        return $collection;
    }

    /**
     * @param ServiceContainer $container
     *
     * @return SchemaCreator
     */
    public static function onInitSchemaCreator(ServiceContainer $container): SchemaCreator
    {
        $db = $container->get('Database');
        $versionString = $db->fetchValue('SELECT VERSION()');
        $databaseDetector = new DatabaseDetector(new DatabaseVersionStringParser($versionString));

        if (!$databaseDetector->isMariaDb() && !$databaseDetector->isMySQL()) {
            throw new SchemaCreatorMissingDriverException('Unknown Database Driver');
        }

        $driver = new MysqlDriver(
            new ColumnLineGenerator($db),
            new IndexLineGenerator($db),
            new TableOptionsGenerator($db),
            new ConstraintLineGenerator($db)
        );

        return new SchemaCreator($db, $driver);
    }
}
