<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Client;

use DateTimeInterface;
use Xentral\Modules\GoogleApi\Data\GoogleAccountData;
use Xentral\Modules\GoogleCalendar\Data\GoogleCalendarColorCollection;
use Xentral\Modules\GoogleCalendar\Data\GoogleCalendarEventData;
use Xentral\Modules\GoogleCalendar\Data\GoogleCalendarListItem;
use Xentral\Modules\GoogleCalendar\Exception\GoogleCalendarApiException;

interface GoogleCalendarClientInterface
{
    /** @var string SENDUPDATES_DEFAULT */
    public const SENDUPDATES_DEFAULT = 'default';

    /** @var string SENDUPDATES_ALL */
    public const SENDUPDATES_ALL = 'all';

    /** @var string SENDUPDATES_EXTERNALONLY */
    public const SENDUPDATES_EXTERNALONLY = 'externalOnly';

    /** @var string SENDUPDATES_NONE */
    public const SENDUPDATES_NONE = 'none';

    /**
     * @return GoogleAccountData
     */
    public function getAccount(): GoogleAccountData;

    /**
     * @param array $filters
     *
     * @return GoogleCalendarListItem[]
     */
    public function getCalendarList(array $filters = []): array;

    /**
     * @return GoogleCalendarListItem
     */
    public function getPrimaryCalendar(): GoogleCalendarListItem;

    /**
     * @param string            $calendar
     * @param DateTimeInterface $modifiedSince
     *
     * @return GoogleCalendarEventData[]
     */
    public function getModifiedEvents(string $calendar, DateTimeInterface $modifiedSince): array;


    /**
     * @param string            $calendar
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     *
     * @return GoogleCalendarEventData[]
     */
    public function getAbsoluteEvents(string $calendar, DateTimeInterface $from, DateTimeInterface $to): array;

    /**
     * @param string $eventId
     *
     * @throws GoogleCalendarApiException
     *
     * @return GoogleCalendarEventData
     */
    public function getEvent($eventId): GoogleCalendarEventData;

    /**
     * @param string $calendar calendar identifier
     * @param array  $filters
     *
     * @return GoogleCalendarEventData[]
     */
    public function getEventList(string $calendar, $filters = []): array;

    /**
     * @param GoogleCalendarEventData $event
     * @param string                  $sendUpdates
     *
     * @return GoogleCalendarEventData
     */
    public function insertEvent(
        GoogleCalendarEventData $event,
        $sendUpdates = self::SENDUPDATES_DEFAULT
    ): GoogleCalendarEventData;

    /**
     * @param GoogleCalendarEventData $event
     * @param string                  $sendUpdates
     *
     * @return GoogleCalendarEventData
     */
    public function updateEvent(
        GoogleCalendarEventData $event,
        $sendUpdates = self::SENDUPDATES_DEFAULT
    ): GoogleCalendarEventData;

    /**
     * @param GoogleCalendarEventData $event
     * @param string                  $targetCalendar
     * @param string                  $sendUpdates
     *
     * @return GoogleCalendarEventData
     */
    public function moveEvent(
        GoogleCalendarEventData $event,
        $targetCalendar,
        $sendUpdates = self::SENDUPDATES_DEFAULT
    ): GoogleCalendarEventData;

    /**
     * @param string $eventId
     * @param string $sendUpdates
     *
     * @return bool
     */
    public function deleteEvent(
        $eventId,
        $sendUpdates = self::SENDUPDATES_DEFAULT
    ): bool;

    /**
     * @param string $calendar
     *
     * @return bool
     */
    public function canAccessCalendar(string $calendar): bool;

    /**
     * @return array
     */
    public function getUserSettings(): array;

    /**
     * @return GoogleCalendarColorCollection
     */
    public function getAvailableColors(): GoogleCalendarColorCollection;
}
