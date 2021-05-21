<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Client;

use Xentral\Components\Logger\LoggerAwareTrait;
use Xentral\Modules\GoogleApi\Client\GoogleApiClientFactory;
use Xentral\Modules\GoogleApi\Exception\GoogleAccountNotFoundException as AccountNotFoundException;
use Xentral\Modules\GoogleApi\Exception\NoRefreshTokenException as AccessException;
use Xentral\Modules\GoogleApi\GoogleScope;
use Xentral\Modules\GoogleApi\Service\GoogleAccountGateway;
use Xentral\Modules\GoogleCalendar\Exception\GoogleAccountNotFoundException;
use Xentral\Modules\GoogleCalendar\Exception\GoogleApiAccessException;
use Xentral\Modules\GoogleCalendar\Exception\GoogleApiScopeException;

final class GoogleCalendarClientFactory
{
    use LoggerAwareTrait;

    /** @var GoogleApiClientFactory $clientFactory */
    private $clientFactory;

    /** @var GoogleAccountGateway $gateway */
    private $gateway;

    /**
     * @param GoogleApiClientFactory $clientFactory
     * @param GoogleAccountGateway   $gateway
     *
     * @codeCoverageIgnore
     */
    public function __construct(GoogleApiClientFactory $clientFactory, GoogleAccountGateway $gateway)
    {
        $this->clientFactory = $clientFactory;
        $this->gateway = $gateway;
    }

    /**
     * @param int $userId
     *
     * @throws GoogleAccountNotFoundException
     * @throws GoogleApiAccessException
     * @throws GoogleApiScopeException
     *
     * @return GoogleCalendarClient
     */
    public function createClient(int $userId): GoogleCalendarClient
    {
        try {
            $apiClient = $this->clientFactory->createClient($userId);
        } catch (AccountNotFoundException $e) {
            throw new GoogleAccountNotFoundException($e->getMessage(), $e->getCode(), $e);
        } catch (AccessException $e) {
            throw new GoogleApiAccessException($e->getMessage(), $e->getCode(), $e);
        }
        $account = $apiClient->getAccount();
        if (!$this->gateway->hasAccountScope($account->getId(), GoogleScope::CALENDAR)) {
            $this->logger->debug(
                'User (id={id}) has not granted access to the google calendar API',
                ['id' => $account->getUserId()]
            );
            throw new GoogleApiScopeException('Access to Google calendar API scope denied');
        }
        $client = new GoogleCalendarClient($apiClient);
        $client->setLogger($this->logger);

        return $client;
    }
}
