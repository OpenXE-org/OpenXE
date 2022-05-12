<?php

namespace Xentral\Modules\SuperSearch;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\SuperSearch\Factory\ProviderFactory;
use Xentral\Modules\SuperSearch\Scheduler\SuperSearchDiffIndexTask;
use Xentral\Modules\SuperSearch\Scheduler\SuperSearchFullIndexTask;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\AddressProvider;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\AppProvider;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\ArticleProvider;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\CreditNoteProvider;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\InvoiceProvider;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\OfferProvider;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\OrderProvider;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\DeliveryNoteProvider;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\TrackingNumberProvider;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\SearchIndexProviderInterface;
use Xentral\Modules\SuperSearch\SystemHealth\SuperSearchHealthChecker;
use Xentral\Modules\SuperSearch\Wrapper\CompanyConfigWrapper;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'SuperSearchService'         => 'onInitSuperSearchService',
            'SuperSearchIndexer'         => 'onInitSuperSearchIndexer',
            'SuperSearchEngine'          => 'onInitSuperSearchEngine',
            'SuperSearchProviderFactory' => 'onInitSuperSearchProviderFactory',
            'SuperSearchHealthChecker'   => 'onInitSuperSearchHealthChecker',

            // Cronjob-Tasks
            'SuperSearchFullIndexTask'   => 'onInitSuperSearchFullIndexTask',
            'SuperSearchDiffIndexTask'   => 'onInitSuperSearchDiffIndexTask',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SuperSearchService
     */
    public static function onInitSuperSearchService(ContainerInterface $container)
    {
        return new SuperSearchService($container->get('Database'), $container->get('SuperSearchIndexer'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SuperSearchEngine
     */
    public static function onInitSuperSearchEngine(ContainerInterface $container)
    {
        return new SuperSearchEngine($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SuperSearchIndexer
     */
    public static function onInitSuperSearchIndexer(ContainerInterface $container)
    {
        $provider = self::createSearchIndexProvider($container);

        return new SuperSearchIndexer($container->get('Database'), $provider);
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ProviderFactory
     */
    public static function onInitSuperSearchProviderFactory(ContainerInterface $container)
    {
        $factory = new ProviderFactory($container->get('Database'));

        $factory->registerProviderFactory(
            'addresses', static function (ContainerInterface $container) {
            return new AddressProvider($container->get('Database'));
        });
        $factory->registerProviderFactory(
            'articles', static function (ContainerInterface $container) {
            return new ArticleProvider($container->get('Database'));
        });
        $factory->registerProviderFactory(
            'creditnotes', static function (ContainerInterface $container) {
            return new CreditNoteProvider($container->get('Database'));
        });
        $factory->registerProviderFactory(
            'deliverynote', static function (ContainerInterface $container) {
            return new DeliveryNoteProvider($container->get('Database'));
        });
        $factory->registerProviderFactory(
            'invoices', static function (ContainerInterface $container) {
            return new InvoiceProvider($container->get('Database'));
        });
        $factory->registerProviderFactory(
            'offers', static function (ContainerInterface $container) {
            return new OfferProvider($container->get('Database'));
        });
        $factory->registerProviderFactory(
            'orders', static function (ContainerInterface $container) {
            return new OrderProvider($container->get('Database'));
        });
        $factory->registerProviderFactory(
            'trackingnumber', static function (ContainerInterface $container) {
            return new TrackingNumberProvider($container->get('Database'));
        });
        $factory->registerProviderFactory(
            'apps',
            static function (ContainerInterface $container) {
                /** @var \ApplicationCore $app */
                $app = $container->get('LegacyApplication');
                /** @var \Appstore $appstoreModule */
                $appstoreModule = $app->erp->LoadModul('appstore');

                return new AppProvider($appstoreModule);
            }
        );

        return $factory;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SuperSearchHealthChecker
     */
    public static function onInitSuperSearchHealthChecker(ContainerInterface $container)
    {
        return new SuperSearchHealthChecker($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SuperSearchFullIndexTask
     */
    public static function onInitSuperSearchFullIndexTask(ContainerInterface $container)
    {
        /** @var SuperSearchService $service */
        $service = $container->get('SuperSearchService');

        /** @var SuperSearchIndexer $factory */
        $indexer = $container->get('SuperSearchIndexer');

        $config = self::onInitCompanyConfigWrapper($container);

        return new SuperSearchFullIndexTask($service, $indexer, $config);
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SuperSearchDiffIndexTask
     */
    public static function onInitSuperSearchDiffIndexTask(ContainerInterface $container)
    {
        /** @var SuperSearchService $service */
        $service = $container->get('SuperSearchService');

        /** @var SuperSearchIndexer $factory */
        $indexer = $container->get('SuperSearchIndexer');

        $config = self::onInitCompanyConfigWrapper($container);

        return new SuperSearchDiffIndexTask($service, $indexer, $config);
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SearchIndexProviderInterface[]|array
     */
    private static function createSearchIndexProvider(ContainerInterface $container)
    {
        /** @var ProviderFactory $factory */
        $factory = $container->get('SuperSearchProviderFactory');

        return $factory->createActiveProviders($container);
    }

    /**
     * @param ContainerInterface $container
     *
     * @return CompanyConfigWrapper
     */
    private static function onInitCompanyConfigWrapper(ContainerInterface $container)
    {
        /** @var \ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new CompanyConfigWrapper($app->erp);
    }
}
