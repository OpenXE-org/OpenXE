<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleApi\Client;

use Xentral\Components\HttpClient\HttpClientFactory;
use Xentral\Components\HttpClient\RequestOptions;
use Xentral\Components\Logger\LoggerAwareTrait;
use Xentral\Modules\GoogleApi\Exception\GoogleAccountNotFoundException;
use Xentral\Modules\GoogleApi\Exception\NoAccessTokenException;
use Xentral\Modules\GoogleApi\Exception\NoRefreshTokenException;
use Xentral\Modules\GoogleApi\Service\GoogleAccountGateway;
use Xentral\Modules\GoogleApi\Service\GoogleAuthorizationService;

final class GoogleApiClientFactory
{
    use LoggerAwareTrait;

    /** @var GoogleAccountGateway $gateway */
    private $gateway;

    /** @var GoogleAuthorizationService $authorizer */
    private $auth;

    /** @var HttpClientFactory $clientFactory */
    private $clientFactory;

    /**
     * @param GoogleAccountGateway       $gateway
     * @param GoogleAuthorizationService $auth
     * @param HttpClientFactory          $clientFactory
     */
    public function __construct(
        GoogleAccountGateway $gateway,
        GoogleAuthorizationService $auth,
        HttpClientFactory $clientFactory
    )
    {
        $this->gateway = $gateway;
        $this->auth = $auth;
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param int $userId
     *
     * @throws GoogleAccountNotFoundException
     * @throws NoRefreshTokenException
     *
     * @return GoogleApiClient
     */
    public function createClient(int $userId): GoogleApiClient
    {
        $account = $this->gateway->getAccountByUser($userId);
        try{
            $token = $this->gateway->getAccessToken($account->getId());
        } catch (NoAccessTokenException $e) {
            $token = null;
        }
        if ($token === null || $token->getTimeToLive() < 10) {
            $token = $this->auth->refreshAccessToken($account);
        }
        $options = new RequestOptions();
        $options->setHeader(
            'Authorization',
            sprintf('Bearer %s', $token->getToken())
        );
        $options->setHeader('Accept', 'application/json');
        $httpClient = $this->clientFactory->createClient($options);
        $client = new GoogleApiClient($httpClient, $account);
        $client->setLogger($this->logger);

        return $client;
    }
}
