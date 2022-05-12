<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Task;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use Exception;
use Xentral\Components\Logger\LoggerAwareTrait;
use Xentral\Modules\GoogleApi\GoogleScope;
use Xentral\Modules\GoogleApi\Service\GoogleAccountGateway;
use Xentral\Modules\GoogleCalendar\Client\GoogleCalendarClientFactory;
use Xentral\Modules\GoogleCalendar\Service\GoogleCalendarSynchronizer;
use Xentral\Modules\User\Service\UserConfigService;

/**
 * Task class for cronjobs/google_calendar_import.php
 */
final class GoogleCalendarSynchronizerTask
{
    use LoggerAwareTrait;

    /** @var GoogleAccountGateway $gateway */
    private $gateway;

    /** @var GoogleCalendarClientFactory $factory */
    private $factory;

    /** @var GoogleCalendarSynchronizer $synchronizer */
    private $synchronizer;

    /** @var UserConfigService $userConfig */
    private $userConfig;

    /**
     * @param GoogleAccountGateway        $gateway
     * @param GoogleCalendarClientFactory $factory
     * @param GoogleCalendarSynchronizer  $synchronizer
     * @param UserConfigService           $userConfig
     */
    public function __construct(
        GoogleAccountGateway $gateway,
        GoogleCalendarClientFactory $factory,
        GoogleCalendarSynchronizer $synchronizer,
        UserConfigService $userConfig
    ) {
        $this->gateway = $gateway;
        $this->factory = $factory;
        $this->synchronizer = $synchronizer;
        $this->userConfig = $userConfig;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $this->logger->notice('Google synchronization jop starts');
        try {
            $accounts = $this->gateway->getAccountsByScope(GoogleScope::CALENDAR);
            if (count($accounts) === 0) {
                $this->logger->notice(
                    'Google synchronization exit cleanly: No accounts available for import.'
                );

                return;
            }
            $timeNow = new DateTimeImmutable('now');
            $past = $timeNow->sub(new DateInterval('P1M'));
            $future = $timeNow->add(new DateInterval('P3M'));
            foreach ($accounts as $account) {
                try {
                    $client = $this->factory->createClient($account->getUserId());
                    $this->synchronizer->importAbsoluteEvents($client, $past, $future);
                    $now = new DateTime('now');
                    $this->userConfig->set(
                        GoogleCalendarSynchronizer::CONFIG_KEY_LAST_SYNC,
                        $now->format('Y-m-d H:i:s'),
                        $account->getUserId()
                    );
                } catch (Exception $e) {
                    $this->logger->debug(
                        'ERROR during import with user "user_id={user}".',
                        ['user' => $account->getUserId(), 'exception' => $e]
                    );
                }
            }
        } catch (Exception $e) {
            $this->logger->error(
                'Google synchronization Error: {message}',
                ['exception' => $e, 'message' => $e]
            );

            return;
        }

        $this->logger->notice('Google synchronization finished');
    }

    /**
     * @return void
     */
    public function cleanup(): void
    {
    }
}
