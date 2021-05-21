<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle;

use Aboabrechnung;
use Xentral\Components\SchemaCreator\Collection\SchemaCollection;
use Xentral\Components\SchemaCreator\Schema\TableSchema;
use Xentral\Components\SchemaCreator\Type;
use Xentral\Components\SchemaCreator\Index;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\SubscriptionCycle\Scheduler\SubscriptionCycleFullTask;
use Xentral\Modules\SubscriptionCycle\Scheduler\SubscriptionCycleManualJobTask;
use Xentral\Modules\SubscriptionCycle\Scheduler\TaskMutexService;
use Xentral\Modules\SubscriptionCycle\Service\SubscriptionCycleArticleService;
use Xentral\Modules\SubscriptionCycle\Service\SubscriptionCycleAutoSubscriptionGateway;
use Xentral\Modules\SubscriptionCycle\Service\SubscriptionCycleAutoSubscriptionService;
use Xentral\Modules\SubscriptionCycle\Service\SubscriptionCycleArticleGateway;
use Xentral\Modules\SubscriptionCycle\Service\SubscriptionCycleCacheService;
use Xentral\Modules\SubscriptionCycle\Service\SubscriptionCycleJobService;
use Xentral\Modules\SubscriptionCycle\Wrapper\BusinessLetterWrapper;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'AutoSubscriptionModule'         => 'onInitAutoSubscriptionModule',
            'SubscriptionCycleCacheFiller'   => 'onInitSubscriptionCycleCacheFiller',
            'SubscriptionCycleManualJobTask' => 'onInitSubscriptionCycleManualJobTask',
            'SubscriptionCycleJobService'    => 'onInitSubscriptionCycleJobService',
            'SubscriptionCycleFullTask'      => 'onInitSubscriptionCycleFullTask',
            'TaskMutexService'               => 'onInitTaskMutexService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return AutoSubscriptionModule
     */
    public static function onInitAutoSubscriptionModule(ContainerInterface $container): AutoSubscriptionModule
    {
        return new AutoSubscriptionModule(
            self::onInitSubscriptionCycleAutoSubscriptionService($container),
            self::onInitSubscriptionCycleAutoSubscriptionGateway($container),
            self::onInitSubscriptionCycleArticleService($container),
            self::onInitBusinessLetterWrapper($container)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SubscriptionCycleManualJobTask
     */
    public static function onInitSubscriptionCycleManualJobTask(ContainerInterface $container
    ): SubscriptionCycleManualJobTask {
        $legacyApp = $container->get('LegacyApplication');

        $subscriptionCycleModule = $legacyApp->loadModule('rechnungslauf');
        $subscriptionModule = new Aboabrechnung($legacyApp);
        $subscriptionModule->cronjob = true;

        return new SubscriptionCycleManualJobTask(
            $legacyApp,
            $container->get('Database'),
            $container->get('TaskMutexService'),
            $container->get('SubscriptionCycleJobService'),
            $subscriptionCycleModule,
            $subscriptionModule,
            !empty($legacyApp->erp->GetKonfiguration('rechnungslauf_gruppen'))
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SubscriptionCycleJobService
     */
    public static function onInitSubscriptionCycleJobService(ContainerInterface $container): SubscriptionCycleJobService
    {
        return new SubscriptionCycleJobService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SubscriptionCycleFullTask
     */
    public static function onInitSubscriptionCycleFullTask(ContainerInterface $container): SubscriptionCycleFullTask
    {
        $legacyApp = $container->get('LegacyApplication');
        $legacyApp->loadModule('rechnungslauf');
        $subscriptionModule = new Aboabrechnung($legacyApp);
        $subscriptionModule->cronjob = true;

        return new SubscriptionCycleFullTask(
            $legacyApp,
            $container->get('Database'),
            $container->get('TaskMutexService'),
            $container->get('SubscriptionCycleJobService'),
            $subscriptionModule,
            !empty($legacyApp->erp->GetKonfiguration('rechnungslauf_cronjoborders')),
            !empty($legacyApp->erp->GetKonfiguration('rechnungslauf_cronjobinvoices')),
            (int)$legacyApp->erp->GetKonfiguration('rechnungslauf_cronjobprinter'),
            (string)$legacyApp->erp->GetKonfiguration('rechnungslauf_cronjobemailprinter')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return TaskMutexService
     */
    public static function onInitTaskMutexService(ContainerInterface $container): TaskMutexService
    {
        return new TaskMutexService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SubscriptionCycleAutoSubscriptionService
     */
    private static function onInitSubscriptionCycleAutoSubscriptionService(ContainerInterface $container
    ): SubscriptionCycleAutoSubscriptionService {
        return new SubscriptionCycleAutoSubscriptionService(
            $container->get('Database')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SubscriptionCycleAutoSubscriptionGateway
     */
    private static function onInitSubscriptionCycleAutosubScriptionGateway(ContainerInterface $container
    ): SubscriptionCycleAutoSubscriptionGateway {
        return new SubscriptionCycleAutoSubscriptionGateway(
            $container->get('Database')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SubscriptionCycleArticleService
     */
    private static function onInitSubscriptionCycleArticleService(ContainerInterface $container
    ): SubscriptionCycleArticleService {
        return new SubscriptionCycleArticleService(
            $container->get('Database')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return BusinessLetterWrapper
     */
    private static function onInitBusinessLetterWrapper(ContainerInterface $container): BusinessLetterWrapper
    {
        return new BusinessLetterWrapper(
            $container->get('LegacyApplication'),
            $container->get('SystemMailer'),
            $container->get('EmailAccountGateway')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SubscriptionCycleCacheService
     */
    private static function onInitSubscriptionCycleCacheService(ContainerInterface $container
    ): SubscriptionCycleCacheService {
        return new SubscriptionCycleCacheService(
            $container->get('Database')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SubscriptionCycleArticleGateway
     */
    private static function onInitSubscriptionCycleCacheGateway(ContainerInterface $container
    ): SubscriptionCycleArticleGateway {
        return new SubscriptionCycleArticleGateway(
            $container->get('Database')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SubscriptionCycleCacheFiller
     */
    public static function onInitSubscriptionCycleCacheFiller(
        ContainerInterface $container
    ): SubscriptionCycleCacheFiller {
        return new SubscriptionCycleCacheFiller(
            self::onInitSubscriptionCycleCacheGateway($container),
            self::onInitSubscriptionCycleCacheService($container)
        );
    }

    /**
     * @param SchemaCollection $collection
     *
     * @return void
     */
    public static function registerTableSchemas(SchemaCollection $collection): void
    {
        $subscriptionCycleJob = new TableSchema('subscription_cycle_job');
        $subscriptionCycleJob->addColumn(Type\Integer::asAutoIncrement('id'));
        $subscriptionCycleJob->addColumn(new Type\Integer('address_id'));
        $subscriptionCycleJob->addColumn(new Type\Varchar('document_type', 32));
        $subscriptionCycleJob->addColumn(new Type\Varchar('job_type', 32));
        $subscriptionCycleJob->addColumn(new Type\Integer('printer_id'));
        $subscriptionCycleJob->addColumn(new Type\Timestamp('created_at', 'CURRENT_TIMESTAMP'));
        $subscriptionCycleJob->addIndex(new Index\Primary(['id']));
        $subscriptionCycleJob->addIndex(new Index\Index(['address_id']));
        $collection->add($subscriptionCycleJob);
    }
}
