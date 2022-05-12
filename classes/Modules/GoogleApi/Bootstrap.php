<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleApi;

use ApplicationCore;
use Xentral\Components\Http\Request;
use Xentral\Components\HttpClient\HttpClientFactory;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\GoogleApi\Client\GoogleApiClientFactory;
use Xentral\Modules\GoogleApi\Service\GoogleAccountGateway;
use Xentral\Modules\GoogleApi\Service\GoogleAccountService;
use Xentral\Modules\GoogleApi\Service\GoogleAuthorizationService;
use Xentral\Modules\GoogleApi\Service\GoogleCredentialsService;
use Xentral\Modules\GoogleApi\Wrapper\CompanyConfigWrapper;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'GoogleCredentialsService' => 'onInitGoogleCredentialsService',
            'GoogleAccountGateway' => 'onInitGoogleAccountGateway',
            'GoogleAccountService' => 'onInitGoogleAccountService',
            'GoogleAuthorizationService' => 'onInitGoogleAuthorizationService',
            'GoogleApiClientFactory' => 'onInitGoogleApiClientFactory',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return GoogleCredentialsService
     */
    public static function onInitGoogleCredentialsService(ContainerInterface $container): GoogleCredentialsService
    {
        return new GoogleCredentialsService(
            self::onInitCompanyConfigWrapper($container)
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return GoogleAccountGateway
     */
    public static function onInitGoogleAccountGateway(ContainerInterface $container): GoogleAccountGateway
    {
        return new GoogleAccountGateway($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return GoogleAccountService
     */
    public static function onInitGoogleAccountService(ContainerInterface $container): GoogleAccountService
    {
        return new GoogleAccountService(
            $container->get('GoogleAccountGateway'),
            $container->get('Database')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return GoogleAuthorizationService
     */
    public static function onInitGoogleAuthorizationService(ContainerInterface $container): GoogleAuthorizationService
    {
        /** @var GoogleCredentialsService $credentialService */
        $credentialService = $container->get('GoogleCredentialsService');
        /** @var Request $request */
        $request = $container->get('Request');
        /** @var HttpClientFactory $clientFactory */
        $clientFactory = $container->get('HttpClientFactory');
        $httpClient = $clientFactory->createClient();
        return new GoogleAuthorizationService(
            $container->get('GoogleAccountGateway'),
            $container->get('GoogleAccountService'),
            $httpClient,
            $credentialService->getCredentials(),
            $request->getBaseUrl()
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return GoogleApiClientFactory
     */
    public static function onInitGoogleApiClientFactory(ContainerInterface $container): GoogleApiClientFactory
    {
        return new GoogleApiClientFactory(
            $container->get('GoogleAccountGateway'),
            $container->get('GoogleAuthorizationService'),
            $container->get('HttpClientFactory')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return CompanyConfigWrapper
     */
    private static function onInitCompanyConfigWrapper(ContainerInterface $container): CompanyConfigWrapper
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new CompanyConfigWrapper($app->erp);
    }
}
