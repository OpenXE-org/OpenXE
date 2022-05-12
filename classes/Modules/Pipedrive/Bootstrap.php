<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive;

use ApplicationCore;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Pipedrive\Gateway\PipedriveContactGateway;
use Xentral\Modules\Pipedrive\Gateway\PipedriveDealGateway;
use Xentral\Modules\Pipedrive\Gateway\PipedrivePersonPropertyGateway;
use Xentral\Modules\Pipedrive\RequestQueues\PipedriveRequestQueuesGateway;
use Xentral\Modules\Pipedrive\RequestQueues\PipedriveRequestQueuesService;
use Xentral\Modules\Pipedrive\Scheduler\PipedriveProcessSchedulerTask;
use Xentral\Modules\Pipedrive\Scheduler\PipedrivePullDealsTask;
use Xentral\Modules\Pipedrive\Scheduler\PipedrivePullPersonsTask;
use Xentral\Modules\Pipedrive\Service\PipedriveClientService;
use Xentral\Modules\Pipedrive\Service\PipedriveConfigurationService;
use Xentral\Modules\Pipedrive\Service\PipedriveDealPropertyService;
use Xentral\Modules\Pipedrive\Service\PipedriveDealService;
use Xentral\Modules\Pipedrive\Service\PipedriveEventService;
use Xentral\Modules\Pipedrive\Service\PipedriveHttpClientService;
use Xentral\Modules\Pipedrive\Service\PipedriveMetaReaderService;
use Xentral\Modules\Pipedrive\Service\PipedriveMetaWriterService;
use Xentral\Modules\Pipedrive\Service\PipedrivePersonPropertyService;
use Xentral\Modules\Pipedrive\Service\PipedrivePersonService;
use Xentral\Modules\Pipedrive\Validator\PipedriveDealValidator;
use Xentral\Modules\Pipedrive\Validator\PipedrivePersonValidator;
use Xentral\Modules\Pipedrive\Wrapper\PipedriveAddAddressRoleWrapper;
use Xentral\Modules\Pipedrive\Wrapper\PipedriveResubmissionWrapper;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'PipedriveConfigurationService'  => 'onInitPipedriveConfigurationService',
            'PipedrivePersonService'         => 'onInitPipedrivePersonService',
            'PipedriveClientService'         => 'onInitPipedriveClientService',
            'PipedriveRequestQueuesGateway'  => 'onInitPipedriveRequestQueuesGateway',
            'PipedriveRequestQueuesService'  => 'onInitPipedriveRequestQueuesService',
            'PipedriveContactGateway'        => 'onInitPipedriveContactGateway',
            'PipedriveDealGateway'           => 'onInitPipedriveDealGateway',
            'PipedrivePersonPropertyService' => 'onInitPipedrivePersonPropertyService',
            'PipedrivePersonPropertyGateway' => 'onInitPipedrivePersonPropertyGateway',
            'PipedriveDealPropertyService'   => 'onInitPipedriveDealPropertyService',
            'PipedriveProcessSchedulerTask'  => 'onInitPipedriveProcessSchedulerTask',
            'PipedrivePullPersonsTask'       => 'onInitPipedrivePullPersonsTask',
            'PipedrivePullDealsTask'         => 'onInitPipedrivePullDealsTask',
            'PipedriveDealService'           => 'onInitPipedriveDealService',
            'PipedriveEventService'          => 'onInitPipedriveEventService',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PipedriveConfigurationService
     */
    public static function onInitPipedriveConfigurationService(
        ContainerInterface $container
    ): PipedriveConfigurationService {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');
        $metaTmp = $app->erp->GetTMP() . 'meta' . DIRECTORY_SEPARATOR . 'pipedrive';

        return new PipedriveConfigurationService(
            $container->get('SystemConfigModule'),
            new PipedriveMetaWriterService($metaTmp),
            $container->get('PipedrivePersonPropertyGateway'),
            $container->get('PipedriveDealGateway'),
            new PipedriveMetaReaderService($metaTmp),
            new PipedriveAddAddressRoleWrapper($app->erp)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @throws Exception\PipedriveHttpClientException
     *
     * @return PipedriveClientService
     */
    public static function onInitPipedriveClientService(ContainerInterface $container): PipedriveClientService
    {
        return new PipedriveClientService(
            new PipedriveHttpClientService($container->get('HttpClientFactory'), 30),
            $container->get('PipedriveConfigurationService')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PipedrivePersonService
     */
    public static function onInitPipedrivePersonService(ContainerInterface $container): PipedrivePersonService
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');
        $metaTmp = $app->erp->GetTMP() . 'meta' . DIRECTORY_SEPARATOR . 'pipedrive';

        return new PipedrivePersonService(
            $container->get('PipedriveClientService'),
            new PipedrivePersonValidator(),
            new PipedriveMetaReaderService($metaTmp)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PipedriveRequestQueuesGateway
     */
    public static function onInitPipedriveRequestQueuesGateway(ContainerInterface $container
    ): PipedriveRequestQueuesGateway {
        return new PipedriveRequestQueuesGateway($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PipedriveRequestQueuesService
     */
    public static function onInitPipedriveRequestQueuesService(ContainerInterface $container
    ): PipedriveRequestQueuesService {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new PipedriveRequestQueuesService(
            $container->get('PipedriveRequestQueuesGateway'),
            $container->get('Database'),
            $app,
            $container->get('PipedriveConfigurationService'),
            $container->get('PipedriveEventService')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PipedriveContactGateway
     */
    public static function onInitPipedriveContactGateway(ContainerInterface $container): PipedriveContactGateway
    {
        return new PipedriveContactGateway(
            $container->get('Database'), $container->get('PipedriveConfigurationService')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PipedriveDealGateway
     */
    public static function onInitPipedriveDealGateway(ContainerInterface $container): PipedriveDealGateway
    {
        return new PipedriveDealGateway($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PipedrivePersonPropertyGateway
     */
    public static function onInitPipedrivePersonPropertyGateway(ContainerInterface $container
    ): PipedrivePersonPropertyGateway {
        return new PipedrivePersonPropertyGateway($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PipedrivePersonPropertyService
     */
    public static function onInitPipedrivePersonPropertyService(ContainerInterface $container
    ): PipedrivePersonPropertyService {
        return new PipedrivePersonPropertyService($container->get('PipedriveClientService'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PipedrivePullPersonsTask
     */
    public static function onInitPipedrivePullPersonsTask(ContainerInterface $container): PipedrivePullPersonsTask
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');
        $metaTmp = $app->erp->GetTMP() . 'meta' . DIRECTORY_SEPARATOR . 'pipedrive';

        return new PipedrivePullPersonsTask(
            $container->get('PipedrivePersonService'),
            $container->get('Database'),
            new PipedriveMetaWriterService($metaTmp),
            $container->get('PipedriveContactGateway'),
            $container->get('PipedriveConfigurationService'),
            $container->get('PipedriveEventService'),
            new PipedriveMetaReaderService($metaTmp)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PipedriveProcessSchedulerTask
     */
    public static function onInitPipedriveProcessSchedulerTask(ContainerInterface $container
    ): PipedriveProcessSchedulerTask {
        return new PipedriveProcessSchedulerTask($container->get('PipedriveRequestQueuesService'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PipedriveDealPropertyService
     */
    public static function onInitPipedriveDealPropertyService(ContainerInterface $container
    ): PipedriveDealPropertyService {
        return new PipedriveDealPropertyService(
            $container->get('PipedriveClientService'),
            $container->get('Database'),
            $container->get('PipedrivePersonPropertyGateway'),
            $container->get('ResubmissionGateway'),
            new PipedriveResubmissionWrapper($container->get('Database'))
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PipedrivePullDealsTask
     */
    public static function onInitPipedrivePullDealsTask(ContainerInterface $container): PipedrivePullDealsTask
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');
        $metaTmp = $app->erp->GetTMP() . 'meta' . DIRECTORY_SEPARATOR . 'pipedrive';

        return new PipedrivePullDealsTask(
            $container->get('PipedriveDealService'),
            $container->get('Database'),
            new PipedriveMetaWriterService($metaTmp),
            $container->get('PipedriveDealGateway'),
            $container->get('PipedriveConfigurationService'),
            $container->get('PipedriveEventService'),
            new PipedriveMetaReaderService($metaTmp),
            new PipedriveResubmissionWrapper($container->get('Database'))
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PipedriveDealService
     */
    public static function onInitPipedriveDealService(ContainerInterface $container): PipedriveDealService
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');
        $metaTmp = $app->erp->GetTMP() . 'meta' . DIRECTORY_SEPARATOR . 'pipedrive';

        return new PipedriveDealService(
            $container->get('PipedriveClientService'),
            new PipedriveDealValidator(),
            new PipedriveMetaReaderService($metaTmp)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PipedriveEventService
     */
    public static function onInitPipedriveEventService(ContainerInterface $container): PipedriveEventService
    {
        return new PipedriveEventService($container->get('Database'));
    }

}
