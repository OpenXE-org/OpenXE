<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi;

use Xentral\Components\SchemaCreator\Collection\SchemaCollection;
use Xentral\Components\SchemaCreator\Index\Index;
use Xentral\Components\SchemaCreator\Index\Primary;
use Xentral\Components\SchemaCreator\Index\Unique;
use Xentral\Components\SchemaCreator\Schema\TableSchema;
use Xentral\Components\SchemaCreator\Type;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\FiskalyApi\Factory\FiskalyCashPointClosingFactory;
use Xentral\Modules\FiskalyApi\Service\FiskalyConfig;
use Xentral\Modules\FiskalyApi\Service\FiskalyPosClosingService;
use Xentral\Modules\FiskalyApi\Service\FiskalyTransferService;
use Xentral\Modules\FiskalyApi\Service\FiskalyTransactionCacheService;
use Xentral\Modules\FiskalyApi\Service\FiskalyPosMappingService;
use Xentral\Modules\FiskalyApi\Service\FiskalyTransactionPosSessionService;
use Xentral\Modules\FiskalyApi\Service\FiskalyCashPointClosingDBService;
use Xentral\Modules\FiskalyApi\Factory\FiskalyApiFactory;
use Xentral\Modules\FiskalyApi\Factory\FiskalyTransactionFactory;
use Xentral\Modules\FiskalyApi\Wrapper\TaxSettingWrapper;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            FiskalyApiFactory::class                   => 'onInitFiskalyApiFactory',
            FiskalyTransferService::class              => 'onInitFiskalyTransferService',
            FiskalyTransactionCacheService::class      => 'onInitFiskalyTransactionCacheService',
            FiskalyPosMappingService::class            => 'onInitFiskalyPosMappingService',
            FiskalyTransactionPosSessionService::class => 'onInitFiskalyTransactionPosSessionService',
            FiskalyPosClosingService::class            => 'onInitFiskalyPosClosingService',
            FiskalyCashPointClosingDBService::class    => 'onInitFiskalyCashPointClosingDBService',
            FiskalyCashPointClosingFactory::class      => 'onInitFiskalyCashPointClosingFactory',
            TaxSettingWrapper::class                   => 'onInitTaxSettingWrapper',
            FiskalyTransactionFactory::class           => 'onInitFiskalyTransactionFactory',
            FiskalyConfig::class                       => 'onInitFiskalyConfig',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return FiskalyApiFactory
     */
    public static function onInitFiskalyApiFactory(ContainerInterface $container): FiskalyApiFactory
    {
        return new FiskalyApiFactory(
            $container->get(FiskalyConfig::class)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return FiskalyPosMappingService
     */
    public static function onInitFiskalyPosMappingService(ContainerInterface $container): FiskalyPosMappingService
    {
        return new FiskalyPosMappingService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return FiskalyTransactionPosSessionService
     */
    public static function onInitFiskalyTransactionPosSessionService(ContainerInterface $container
    ): FiskalyTransactionPosSessionService {
        return new FiskalyTransactionPosSessionService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return FiskalyPosClosingService
     */
    public static function onInitFiskalyPosClosingService(ContainerInterface $container): FiskalyPosClosingService
    {
        $legacyApi = $container->get('LegacyApplication');

        return new FiskalyPosClosingService(
            $container->get('Database'),
            $container->get(FiskalyCashPointClosingFactory::class),
            $container->get(TaxSettingWrapper::class)
        );
    }

    public static function onInitTaxSettingWrapper(ContainerInterface $container): TaxSettingWrapper
    {
        $legacyApi = $container->get('LegacyApplication');

        return new TaxSettingWrapper($legacyApi->erp);
    }

    /**
     * @param ContainerInterface $container
     *
     * @return FiskalyCashPointClosingDBService
     */
    public static function onInitFiskalyCashPointClosingDBService(ContainerInterface $container
    ): FiskalyCashPointClosingDBService {
        return new FiskalyCashPointClosingDBService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return FiskalyCashPointClosingFactory
     */
    public static function onInitFiskalyCashPointClosingFactory(ContainerInterface $container
    ): FiskalyCashPointClosingFactory {
        return new FiskalyCashPointClosingFactory();
    }

    /**
     * @param ContainerInterface $container
     *
     * @return FiskalyTransactionFactory
     */
    public static function onInitFiskalyTransactionFactory(ContainerInterface $container
    ): FiskalyTransactionFactory {
        return new FiskalyTransactionFactory($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return FiskalyConfig
     */
    public static function onInitFiskalyConfig(ContainerInterface $container): FiskalyConfig
    {
        return new FiskalyConfig($container->get('SystemConfigModule'), $container->get('EnvironmentConfig'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return FiskalyTransactionCacheService
     */
    public static function onInitFiskalyTransactionCacheService(ContainerInterface $container
    ): FiskalyTransactionCacheService {
        return new FiskalyTransactionCacheService();
    }

    /**
     * @param ContainerInterface $container
     *
     * @return FiskalyTransferService
     */
    public static function onInitFiskalyTransferService(ContainerInterface $container): FiskalyTransferService
    {
        return new FiskalyTransferService(
            $container->get(FiskalyApiFactory::class),
            $container->get(FiskalyTransactionFactory::class),
            $container->get('Database')
        );
    }

    /**
     * @param SchemaCollection $collection
     *
     * @return void
     */
    public static function registerTableSchemas(SchemaCollection $collection): void
    {
        $posMappingTable = new TableSchema('fiskaly_pos_mapping');
        $posMappingTable->addColumn(Type\Integer::asAutoIncrement('id'));
        $posMappingTable->addColumn(new Type\Integer('pos_id', 10, true, null, false));
        $posMappingTable->addColumn(new Type\Varchar('tss_uuid', 36, null, false));
        $posMappingTable->addColumn(new Type\Text('tss_description', false));
        $posMappingTable->addColumn(new Type\Varchar('client_uuid', 36, null, false));
        $posMappingTable->addColumn(new Type\Text('client_description', false));

        $posMappingTable->addIndex(new Primary(['id']));
        $posMappingTable->addIndex(new Unique(['pos_id', 'tss_uuid', 'client_uuid']));
        $posMappingTable->addIndex(new Index(['pos_id']));

        $collection->add($posMappingTable);
    }
}
