<?php

namespace Xentral\Modules\Hubspot;

use ApplicationCore;
use Xentral\Components\Database\Database;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Country\Gateway\StateGateway;
use Xentral\Modules\Hubspot\RequestQueues\HubspotRequestQueuesGateway;
use Xentral\Modules\Hubspot\RequestQueues\HubspotRequestQueuesService;
use Xentral\Modules\Hubspot\Scheduler\HubspotProcessSchedulerTask;
use Xentral\Modules\Hubspot\Scheduler\HubspotPullContactsTask;
use Xentral\Modules\Hubspot\Scheduler\HubspotPullDealsTask;
use Xentral\Modules\Hubspot\Scheduler\HubspotPullEngagementsTask;
use Xentral\Modules\Hubspot\Validators\ContactValidator;
use Xentral\Modules\Hubspot\Validators\DealValidator;
use Xentral\Modules\SubscriptionCycle\Scheduler\TaskMutexService;

final class Bootstrap
{

    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'HubspotContactService'           => 'onInitHubspotContactService',
            'HubspotDealService'              => 'onInitHubspotDealService',
            'HubspotClientService'            => 'onInitHubspotClientService',
            'HubspotHttpClientService'        => 'onInitHubspotHttpClientService',
            'HubspotConfigurationService'     => 'onInitHubspotConfigurationService',
            'HubspotPullContactsTask'         => 'onInitHubspotPullContactsTask',
            'HubspotContactGateway'           => 'onInitHubspotContactGateway',
            'HubspotDealGateway'              => 'onInitHubspotDealGateway',
            'HubspotContactPropertyService'   => 'onInitHubspotContactPropertyService',
            'HubspotContactPropertyGateway'   => 'onInitHubspotContactPropertyGateway',
            'HubspotPullDealsTask'            => 'onInitHubspotPullDealsTask',
            'HubspotDealPropertyService'      => 'onInitHubspotDealPropertyService',
            'HubspotProcessSchedulerTask'     => 'onInitHubspotProcessSchedulerTask',
            'HubspotRequestQueuesGateway'     => 'onInitRequestQueuesGateway',
            'HubspotRequestQueuesService'     => 'onInitRequestQueuesService',
            'HubspotEventService'             => 'onInitHubspotEventService',
            HubspotEngagementService::class   => 'onInitHubspotEngagementService',
            HubspotPullEngagementsTask::class => 'onInitHubspotPullEngagementsTask',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotContactService
     */
    public static function onInitHubspotContactService(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new HubspotContactService(
            $container->get('HubspotClientService'),
            new HubspotMetaService($app->erp->GetTMP()),
            new ContactValidator(),
            $container->get('HubspotConfigurationService')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotClientService
     */
    public static function onInitHubspotClientService(ContainerInterface $container)
    {
        return new HubspotClientService(
            $container->get('HubspotHttpClientService'),
            $container->get('HubspotConfigurationService')
        );
    }

    /**
     * @return HubspotHttpClientService
     */
    public static function onInitHubspotHttpClientService()
    {
        return new HubspotHttpClientService(30);
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotConfigurationService
     */
    public static function onInitHubspotConfigurationService(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new HubspotConfigurationService(
            $app->erp,
            new HubspotMetaService($app->erp->GetTMP()),
            $container->get('HubspotContactPropertyGateway'),
            $container->get('HubspotDealGateway'),
            $container->get('CountryGateway'),
            $container->get(StateGateway::class)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotContactGateway
     */
    public static function onInitHubspotContactGateway(ContainerInterface $container)
    {
        return new HubspotContactGateway($container->get('Database'), $container->get('HubspotConfigurationService'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotPullContactsTask
     */
    public static function onInitHubspotPullContactsTask(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new HubspotPullContactsTask(
            $container->get('HubspotContactService'),
            $container->get('Database'),
            new HubspotMetaService($app->erp->GetTMP()),
            $container->get('HubspotContactGateway'),
            $container->get('HubspotConfigurationService'),
            $container->get('HubspotEventService'),
            $container->get('CountryGateway'),
            new TaskMutexService($container->get('Database'))
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotDealGateway
     */
    public static function onInitHubspotDealGateway(ContainerInterface $container)
    {
        return new HubspotDealGateway($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotDealService
     */
    public static function onInitHubspotDealService(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new HubspotDealService(
            $container->get('HubspotClientService'),
            new HubspotMetaService($app->erp->GetTMP()),
            new DealValidator()
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotContactPropertyService
     */
    public static function onInitHubspotContactPropertyService(ContainerInterface $container)
    {
        return new HubspotContactPropertyService(
            $container->get('HubspotClientService'),
            $container->get('HubspotContactPropertyGateway'),
            $container->get('Database')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotContactPropertyGateway
     */
    public static function onInitHubspotContactPropertyGateway(ContainerInterface $container)
    {
        return new HubspotContactPropertyGateway($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotPullDealsTask
     */
    public static function onInitHubspotPullDealsTask(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new HubspotPullDealsTask(
            $container->get('HubspotDealService'),
            $container->get('Database'),
            new HubspotMetaService($app->erp->GetTMP()),
            $container->get('HubspotDealGateway'),
            $container->get('HubspotConfigurationService'),
            $container->get('HubspotEventService'),
            $container->get('HubspotContactGateway'),
            new TaskMutexService($container->get('Database'))
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotDealPropertyService
     */
    public function onInitHubspotDealPropertyService(ContainerInterface $container)
    {
        return new HubspotDealPropertyService(
            $container->get('HubspotClientService'),
            $container->get('Database'),
            $container->get('HubspotContactPropertyGateway')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotProcessSchedulerTask
     */
    public static function onInitHubspotProcessSchedulerTask(ContainerInterface $container)
    {
        return new HubspotProcessSchedulerTask(
            $container->get('HubspotRequestQueuesService'),
            new TaskMutexService($container->get('Database'))
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotRequestQueuesGateway
     */
    public static function onInitRequestQueuesGateway(ContainerInterface $container)
    {
        return new HubspotRequestQueuesGateway($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotRequestQueuesService
     */
    public static function onInitRequestQueuesService(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new HubspotRequestQueuesService(
            $container->get('HubspotRequestQueuesGateway'),
            $container->get('Database'),
            $app,
            $container->get('HubspotEventService')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotEventService
     */
    public static function onInitHubspotEventService(ContainerInterface $container): HubspotEventService
    {
        return new HubspotEventService($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotEngagementService
     */
    public static function onInitHubspotEngagementService(ContainerInterface $container): HubspotEngagementService
    {
        return new HubspotEngagementService($container->get('HubspotClientService'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HubspotPullEngagementsTask
     */
    public static function onInitHubspotPullEngagementsTask(ContainerInterface $container): HubspotPullEngagementsTask
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new HubspotPullEngagementsTask(
            $container->get('Database'),
            $container->get(HubspotEngagementService::class),
            new HubspotMetaService($app->erp->GetTMP()),
            $container->get('HubspotConfigurationService'),
            $container->get('HubspotEventService'),
            $container->get('HubspotContactGateway'),
            new TaskMutexService($container->get('Database'))
        );
    }
}
