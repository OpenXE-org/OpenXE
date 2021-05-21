<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Service;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Throwable;
use Xentral\Components\Logger\LoggerAwareTrait;
use Xentral\Modules\Calendar\CalendarService;
use Xentral\Modules\Calendar\Data\CalendarEvent;
use Xentral\Modules\GoogleCalendar\Client\GoogleCalendarClientInterface;
use Xentral\Modules\GoogleCalendar\Data\GoogleCalendarColorCollection;
use Xentral\Modules\GoogleCalendar\Data\GoogleCalendarEventData;
use Xentral\Modules\GoogleCalendar\Data\GoogleCalenderSyncValue;
use Xentral\Modules\GoogleCalendar\Exception\GoogleCalendarApiException;
use Xentral\Modules\GoogleCalendar\Exception\GoogleCalendarSyncException;
use Xentral\Modules\GoogleCalendar\Wrapper\UserAddressGatewayWrapper;
use Xentral\Modules\User\Service\UserConfigService;

final class GoogleCalendarSynchronizer
{
    use LoggerAwareTrait;

    /** @var string CONFIG_KEY_LAST_SYNC */
    public const CONFIG_KEY_LAST_SYNC = 'last_google_calendar_sync';

    /** @var GoogleSyncGateway $gateway */
    private $gateway;

    /** @var GoogleSyncService $service */
    private $service;

    /** @var CalendarService $calendarService */
    private $calendarService;

    /** @var UserAddressGatewayWrapper $addressService */
    private $addressService;

    /** @var UserConfigService $userConfigService */
    private $userConfigService;

    /** @var GoogleEventConverter $converter */
    private $converter;

    /**
     * @param GoogleSyncGateway         $gateway
     * @param GoogleSyncService         $service
     * @param CalendarService           $calendarService
     * @param GoogleEventConverter      $converter
     * @param UserAddressGatewayWrapper $addressService
     * @param UserConfigService         $userConfigService
     */
    public function __construct(
        GoogleSyncGateway $gateway,
        GoogleSyncService $service,
        CalendarService $calendarService,
        GoogleEventConverter $converter,
        UserAddressGatewayWrapper $addressService,
        UserConfigService $userConfigService
    ) {
        $this->calendarService = $calendarService;
        $this->gateway = $gateway;
        $this->service = $service;
        $this->userConfigService = $userConfigService;
        $this->converter = $converter;
        $this->addressService = $addressService;
    }

    /**
     * @param int $addressId
     * @param int $calendarEventId
     *
     * @return bool
     */
    public function canAddressEditEvent(int $addressId, int $calendarEventId): bool
    {
        $eventId = $calendarEventId;
        $event = $this->calendarService->tryGetEventWithoutUsers($eventId);
        if ($event === null) {
            return false;
        }
        $canEdit = false;
        $creator = $event->getCreator();
        if ($creator !== null && $creator->getAddressId() === $addressId) {
            $canEdit = true;
        }
        $organizer = $event->getOrganizer();
        if ($organizer !== null && $organizer->getAddressId() === $addressId) {
            $canEdit = true;
        }

        return $canEdit;
    }

    /**
     * @param GoogleCalendarClientInterface $client
     * @param mixed                         $eventId
     * @param string                        $action 'added', 'modified' or 'deleted'
     *
     * @return void
     */
    public function calendarEventHook(GoogleCalendarClientInterface $client, $eventId, $action): void
    {
        try {
            $eventId = (int)$eventId;
            switch ($action) {
                case 'added':
                    //NO BREAK

                case 'modified':
                    $event = $this->calendarService->tryGetEvent($eventId);
                    if ($event === null) {
                        return;
                    }
                    $this->exportCalendarEvent($client, $event, false);
                    break;

                case 'deleted':
                    $this->exportDeleteEvent($client, $eventId);
                    break;
            }
        } catch (Throwable $e) {
            $this->logger->error(
                'Exception in calendarEventHook: {message}',
                ['message' => $e->getMessage(), 'exception' => $e]
            );
        }
    }

    /**
     * @param GoogleCalendarClientInterface $client
     * @param CalendarEvent                 $event
     * @param bool                          $sendUpdates
     *
     * @throws GoogleCalendarApiException
     *
     * @return void
     */
    public function exportCalendarEvent(
        GoogleCalendarClientInterface $client,
        CalendarEvent $event,
        bool $sendUpdates = false
    ): void {
        $sync = $this->gateway->tryGetSyncEntryByEvent($event->getId());
        if ($sync === null || $sync->getGoogleId() === '') {
            $googleEvent = $this->converter->convertToGoogleEvent($event);

            $sendUpdateMethod = GoogleCalendarClientInterface::SENDUPDATES_DEFAULT;
            if ($sendUpdates === true) {
                $sendUpdateMethod = GoogleCalendarClientInterface::SENDUPDATES_ALL;
            }

            $insertedEvent = $client->insertEvent($googleEvent, $sendUpdateMethod);
            $newOrganizer = '';
            if ($googleEvent->getOrganizer() !== null) {
                $newOrganizer = $googleEvent->getOrganizer()->getEmail();
            }

            if ($newOrganizer !== '' && $newOrganizer !== $insertedEvent->getOrganizer()->getEmail()) {
                try {
                    $insertedEvent = $client->moveEvent($insertedEvent, $newOrganizer);
                } catch (GoogleCalendarApiException $e) {
                    $this->logger->error('Failed to export calendar event', ['event' => $event->toArray()]);
                    throw new GoogleCalendarApiException($e->getMessage(), $e->getCode(), $e);
                }
            }

            $address = $this->addressService->getAddressByUser($client->getAccount()->getUserId());
            $sync = new GoogleCalenderSyncValue(
                0,
                $event->getId(),
                $insertedEvent->getId(),
                $address,
                false,
                $event->getStart(),
                $insertedEvent->getHtmlLink()
            );
        } else {
            $existingEvent = $client->getEvent($sync->getGoogleId());
            $googleEvent = $this->converter->convertToGoogleEvent($event, $existingEvent);
            $updated = $client->updateEvent($googleEvent);
            $sync->setEventDate($event->getStart());
            $sync->setHtmlLink($updated->getHtmlLink());
        }
        $this->service->saveSyncEntry($sync);
    }

    /**
     * @param GoogleCalendarClientInterface $client
     * @param int                           $eventId
     *
     * @return void
     */
    public function exportDeleteEvent(GoogleCalendarClientInterface $client, int $eventId): void
    {
        $sync = $this->gateway->tryGetSyncEntryByEvent($eventId);
        if ($sync === null || $sync->getGoogleId() === '') {
            return;
        }
        $client->deleteEvent($sync->getGoogleId());
        $this->service->deleteSyncEntry($sync->getGoogleId(), $sync->getEventId());
    }

    /**
     * @param GoogleCalendarClientInterface $client
     *
     * @return void
     */
    public function importChangedEvents(GoogleCalendarClientInterface $client): void
    {
        $lastSync = $this->userConfigService->tryGet(
            self::CONFIG_KEY_LAST_SYNC,
            $client->getAccount()->getUserId()
        );
        if ($lastSync === null) {
            $now = new DateTime('now');
            $lastSyncDate = $now->sub(new DateInterval('P1D'));
        } else {
            $lastSyncDate = DateTime::createFromFormat('Y-m-d H:i:s', $lastSync);
        }

        $importEvents = $client->getModifiedEvents('primary', $lastSyncDate);
        $userAddress = $this->addressService->getAddressByUser($client->getAccount()->getUserId());

        try {
            $colors = $client->getAvailableColors();
        } catch (Exception $e) {
            $colors = null;
        }

        try {
            $this->importGoogleEvents($importEvents, $userAddress, $colors);
        } catch (Exception $e) {
            throw new GoogleCalendarSyncException('Error during Google Calender Sync.', $e->getCode(), $e);
        }

        $now = new DateTime('now');
        $this->userConfigService->set(
            self::CONFIG_KEY_LAST_SYNC,
            $now->format('Y-m-d H:i:s'),
            $client->getAccount()->getUserId()
        );
    }

    /**
     * @param GoogleCalendarClientInterface $client
     * @param DateTimeInterface|null        $from
     * @param DateTimeInterface|null        $to
     *
     * @throws GoogleCalendarSyncException
     *
     * @return void
     */
    public function importAbsoluteEvents(
        GoogleCalendarClientInterface $client,
        DateTimeInterface $from = null,
        DateTimeInterface $to = null
    ): void {
        $now = new DateTimeImmutable('now');
        if ($from === null) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $from = $now->sub(new DateInterval('P1W'));
        }
        if ($to === null) {
            $to = $now->add(new DateInterval('P3W'));
        }

        $importEvents = $client->getAbsoluteEvents('primary', $from, $to);
        $this->logger->debug(
            'Google responded with {count} events to import for "user_id={user}"',
            ['count' => count($importEvents), 'user' => $client->getAccount()->getUserId()]
        );

        try {
            $colors = $client->getAvailableColors();
        } catch (Exception $e) {
            $colors = null;
        }

        $userAddress = $this->addressService->getAddressByUser($client->getAccount()->getUserId());
        try {
            $this->importGoogleEvents($importEvents, $userAddress, $colors);
        } catch (Exception $e) {
            $this->logger->error('Exception ' . $e->getMessage(), ['exception' => $e]);
            throw new GoogleCalendarSyncException('Error during Google Calender Sync.', $e->getCode(), $e);
        }
    }

    /**
     * @param GoogleCalendarEventData            $googleEvent
     * @param int                                $addressId
     * @param GoogleCalendarColorCollection|null $colors
     *
     * @return void
     */
    public function importGoogleEvent(
        GoogleCalendarEventData $googleEvent,
        int $addressId,
        GoogleCalendarColorCollection $colors = null
    ): void {
        if (strtolower($googleEvent->getStatus()) === 'cancelled') {
            $this->importDeletedGoogleEvent($googleEvent, $addressId);

            return;
        }
        $creator = $googleEvent->getCreator();
        $owner = 0;
        if ($creator !== null) {
            $owner = $this->addressService->findAddressByEmail($creator->getEmail());
        }

        $sync = $this->gateway->tryGetSyncEntryByGoogleEvent($googleEvent->getId());
        if ($sync === null) {
            $sync = new GoogleCalenderSyncValue(
                0,
                0,
                $googleEvent->getId(),
                $owner,
                true,
                $googleEvent->getTime()->getBeginning(),
                $googleEvent->getHtmlLink()
            );
        }

        $existingEvent = null;
        if ($sync->getEventId() > 0) {
            $existingEvent = $this->calendarService->tryGetEventWithoutUsers($sync->getEventId());
        }

        $event = $this->converter->convertToEvent($googleEvent, $existingEvent);
        if ($colors !== null) {
            $color = $colors->getEventColorById($googleEvent->getColorId());
            if ($color === null) {
                $color = $colors->getDefaultColor();
            }
            if ($color !== null) {
                $event->setColor($color->getBackground());
            }
        }

        $id = $this->calendarService->saveEvent($event);
        $sync->setEventId($id);
        $sync->setEventDate($event->getStart());
        $sync->setOwner($owner);
        $this->service->saveSyncEntry($sync);
    }

    /**
     * @param GoogleCalendarEventData $googleEvent
     * @param int                     $addressId
     *
     * @return void
     */
    public function importDeletedGoogleEvent(
        GoogleCalendarEventData $googleEvent,
        int $addressId = 0
    ): void {
        $sync = $this->gateway->tryGetSyncEntryByGoogleEvent($googleEvent->getId());
        if ($sync === null) {
            return; //event has never been synced
        }
        $event = $this->calendarService->tryGetEventWithoutUsers($sync->getEventId());
        if ($event === null) {
            return; //event was already deleted
        }
        $owner = 0;
        if ($event->getCreator() !== null) {
            $owner = $event->getCreator()->getAddressId();
        }
        $editor = 0;
        if ($event->getOrganizer() !== null) {
            $editor = $event->getOrganizer()->getAddressId();
        }
        if ($owner === $addressId || $editor === $addressId) {
            $this->calendarService->deleteEvent($sync->getEventId());
            $this->service->deleteSyncEntry($sync->getGoogleId(), $sync->getEventId());
        } else {
            $userId = $this->addressService->getUserByAddress($addressId);
            $this->calendarService->removeUserFromEvent($sync->getEventId(), $userId);
        }
    }

    /**
     *
     * @param array                              $googleEvents
     * @param int                                $addressId
     * @param GoogleCalendarColorCollection|null $colors
     *
     * @return void
     */
    public function importGoogleEvents(
        array $googleEvents,
        int $addressId,
        GoogleCalendarColorCollection $colors = null
    ): void {
        $this->logger->debug(
            'import {count} events on addressId {address}',
            ['count' => count($googleEvents), 'address' => $addressId,]
        );
        foreach ($googleEvents as $googleEvent) {
            try {
                $this->importGoogleEvent($googleEvent, $addressId, $colors);
            } catch (Exception $e) {
                $this->logger->error(
                    'failed to import Google event {event}',
                    ['event' => $googleEvent->getHtmlLink(), 'exception' => $e]
                );
                continue;
            }
        }
    }
}
