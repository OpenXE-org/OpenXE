<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Client;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Xentral\Components\HttpClient\Response\ServerResponse;
use Xentral\Components\Logger\LoggerAwareTrait;
use Xentral\Modules\GoogleApi\Client\GoolgeApiClientInterface;
use Xentral\Modules\GoogleApi\Data\GoogleAccountData;
use Xentral\Modules\GoogleCalendar\Data\GoogleCalendarColorCollection;
use Xentral\Modules\GoogleCalendar\Data\GoogleCalendarEventData;
use Xentral\Modules\GoogleCalendar\Data\GoogleCalendarListItem;
use Xentral\Modules\GoogleCalendar\Exception\GoogleCalendarApiException;
use Xentral\Modules\GoogleCalendar\Exception\GoogleCalendarNotFoundException;
use Xentral\Modules\GoogleCalendar\Exception\InvalidArgumentException;

final class GoogleCalendarClient implements GoogleCalendarClientInterface
{
    use LoggerAwareTrait;

    /** @var string CALENDAR_PRIMARY */
    public const CALENDAR_PRIMARY = 'primary';

    /** @var string BASE_URL */
    private const BASE_URL = 'https://www.googleapis.com/calendar/v3';

    /** @var GoolgeApiClientInterface $googleApiClient */
    private $googleApiClient;

    /**
     * @param GoolgeApiClientInterface $googleApiClient
     */
    public function __construct(
        GoolgeApiClientInterface $googleApiClient
    ) {
        $this->googleApiClient = $googleApiClient;
    }

    /**
     * @param array $filters
     *
     * @throws GoogleCalendarApiException
     *
     * @return GoogleCalendarListItem[]
     */
    public function getCalendarList(array $filters = []): array
    {
        $uri = $this->createUri('users/me/calendarList', $filters);
        try {
            $result = $this->googleApiClient->sendRequest('GET', $uri);
        } catch (Exception $e) {
            throw new GoogleCalendarApiException($e->getMessage(), $e->getCode(), $e);
        }
        $list = [];
        if (isset($result['items']) && is_array($result['items'])) {
            foreach ($result['items'] as $item) {
                $list[] = GoogleCalendarListItem::fromArray($item);
            }
        }

        return $list;
    }

    /**
     * @throws GoogleCalendarNotFoundException
     * @throws GoogleCalendarApiException
     *
     * @return GoogleCalendarListItem
     */
    public function getPrimaryCalendar(): GoogleCalendarListItem
    {
        $ownedCalendars = $this->getCalendarList(['minAccessRole' => 'owner']);
        foreach ($ownedCalendars as $calendarListItem) {
            if ($calendarListItem->isPrimary()) {
                return $calendarListItem;
            }
        }
        throw new GoogleCalendarNotFoundException(
            sprintf('Cannot get primary calendar of user "id=%s"', $this->getAccount()->getUserId())
        );
    }

    /**
     * @param string            $calendar
     * @param DateTimeInterface $modifiedSince
     *
     * @return GoogleCalendarEventData[]
     */
    public function getModifiedEvents(string $calendar, DateTimeInterface $modifiedSince): array
    {
        $modifiedTimestamp = $modifiedSince->format(DateTimeInterface::RFC3339);
        $filters = [
            'updatedMin' => $modifiedTimestamp,
        ];
        $now = new DateTimeImmutable();
        $now = $now->setTimestamp(time());
        $from = $now->sub(new DateInterval('P1W'));
        $to = $now->add(new DateInterval('P3W'));
        $filters['timeMax'] = $to->format(DateTimeInterface::RFC3339);
        $filters['timeMin'] = $from->format(DateTimeInterface::RFC3339);

        return $this->getEventList($calendar, $filters);
    }

    /**
     * @param string            $calendar
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     *
     * @return GoogleCalendarEventData[]
     */
    public function getAbsoluteEvents(string $calendar, DateTimeInterface $from, DateTimeInterface $to): array
    {
        $filters = [];
        $filters['timeMax'] = $to->format(DateTimeInterface::RFC3339);
        $filters['timeMin'] = $from->format(DateTimeInterface::RFC3339);

        return $this->getEventList($calendar, $filters);
    }

    /**
     * @param string $eventId
     *
     * @throws GoogleCalendarApiException
     *
     * @return GoogleCalendarEventData
     */
    public function getEvent($eventId): GoogleCalendarEventData
    {
        $path = sprintf('calendars/%s/events/%s', self::CALENDAR_PRIMARY, $eventId);
        $url = $this->createUri($path);
        try {
            $result = $this->googleApiClient->sendRequest('GET', $url);
        } catch (Exception $e) {
            throw new GoogleCalendarApiException($e->getMessage(), $e->getCode(), $e);
        }

        return GoogleCalendarEventData::fromArray($result);
    }

    /**
     * @param string $calendar calendar identifier
     * @param array  $filters
     *
     * @throws GoogleCalendarApiException
     *
     * @return GoogleCalendarEventData[]
     */
    public function getEventList(string $calendar, $filters = []): array
    {
        $path = sprintf('calendars/%s/events', $calendar);
        $filters['singleEvents'] = 'true';
        $url = $this->createUri($path, $filters);
        try {
            $result = $this->googleApiClient->sendRequest('GET', $url);
        } catch (Exception $e) {
            throw new GoogleCalendarApiException($e->getMessage(), $e->getCode(), $e);
        }
        $events = [];
        foreach ($result['items'] as $event) {
            $events[] = GoogleCalendarEventData::fromArray($event);
        }

        return $events;
    }

    /**
     * @param GoogleCalendarEventData $event
     * @param string                  $sendUpdates
     *
     * @throws GoogleCalendarApiException
     *
     * @return GoogleCalendarEventData
     */
    public function insertEvent(
        GoogleCalendarEventData $event,
        $sendUpdates = self::SENDUPDATES_DEFAULT
    ): GoogleCalendarEventData {
        $this->validateSendUpdatesParam($sendUpdates);
        $queryParams = [];
        if ($sendUpdates !== self::SENDUPDATES_DEFAULT) {
            $queryParams = ['sendUpdates' => $sendUpdates];
        }
        $url = $this->createUri(sprintf('calendars/%s/events', self::CALENDAR_PRIMARY), $queryParams);
        try {
            $result = $this->googleApiClient->sendRequest('POST', $url, $event->toArray());
        } catch (Exception $e) {
            throw new GoogleCalendarApiException($e->getMessage(), $e->getCode(), $e);
        }

        return GoogleCalendarEventData::fromArray($result);
    }

    /**
     * @param GoogleCalendarEventData $event
     * @param string                  $sendUpdates
     *
     * @throws GoogleCalendarApiException
     *
     * @return GoogleCalendarEventData
     */
    public function updateEvent(
        GoogleCalendarEventData $event,
        $sendUpdates = self::SENDUPDATES_DEFAULT
    ): GoogleCalendarEventData {
        $this->validateSendUpdatesParam($sendUpdates);
        $postData = $event->toArray();
        $queryParams = [];
        if ($sendUpdates !== self::SENDUPDATES_DEFAULT) {
            $queryParams = ['sendUpdates' => $sendUpdates];
        }
        $url = $this->createUri(
            sprintf(
                'calendars/%s/events/%s',
                self::CALENDAR_PRIMARY,
                $event->getId()
            ),
            $queryParams
        );
        try {
            $result = $this->googleApiClient->sendRequest('PUT', $url, $postData);
        } catch (Exception $e) {
            throw new GoogleCalendarApiException($e->getMessage(), $e->getCode(), $e);
        }

        return GoogleCalendarEventData::fromArray($result);
    }

    /**
     * @param GoogleCalendarEventData $event
     * @param string                  $targetCalendar
     * @param string                  $sendUpdates
     *
     * @throws GoogleCalendarApiException
     *
     * @return GoogleCalendarEventData
     */
    public function moveEvent(
        GoogleCalendarEventData $event,
        $targetCalendar,
        $sendUpdates = self::SENDUPDATES_DEFAULT
    ): GoogleCalendarEventData {
        $this->validateSendUpdatesParam($sendUpdates);
        $queryParams = ['destination' => $targetCalendar];
        if ($sendUpdates !== self::SENDUPDATES_DEFAULT) {
            $queryParams['sendUpdates'] = $sendUpdates;
        }
        $url = $this->createUri(
            sprintf('calendars/%s/events/%s/move', self::CALENDAR_PRIMARY, $event->getId()),
            $queryParams
        );
        try {
            $result = $this->googleApiClient->sendRequest('POST', $url);
        } catch (Exception $e) {
            throw new GoogleCalendarApiException($e->getMessage(), $e->getCode(), $e);
        }

        return GoogleCalendarEventData::fromArray($result);
    }

    /**
     * @param string $eventId
     * @param string $sendUpdates
     *
     * @return bool
     */
    public function deleteEvent(
        $eventId,
        $sendUpdates = self::SENDUPDATES_DEFAULT
    ): bool {
        $this->validateSendUpdatesParam($sendUpdates);
        $queryParams = [];
        if ($sendUpdates !== self::SENDUPDATES_DEFAULT) {
            $queryParams = ['sendUpdates' => $sendUpdates];
        }
        $url = $this->createUri(
            sprintf('calendars/%s/events/%s', self::CALENDAR_PRIMARY, $eventId),
            $queryParams
        );
        try {
            $this->googleApiClient->sendRequest('DELETE', $url, null, []);
        } catch (Exception $e) {
            $httpCode = $e->getCode();
            if ($httpCode> 399 && $httpCode < 500) {
                return false;
            }
            throw new GoogleCalendarApiException($e->getMessage(), $e->getCode(), $e);
        }

        return true;
    }

    /**
     * @param string $calendar
     *
     * @throws GoogleCalendarApiException
     *
     * @return bool
     */
    public function canAccessCalendar(string $calendar): bool
    {
        $url = $this->createUri(sprintf('calendars/%s', $calendar));
        try {
            $this->googleApiClient->sendRequest('GET', $url);
        } catch (Exception $e) {
            $httpCode = $e->getCode();
            if ($httpCode > 399 && $httpCode < 500) {
                return false;
            }
            throw new GoogleCalendarApiException($e->getMessage(), $e->getCode(), $e);
        }

        return true;
    }

    /**
     * @throws GoogleCalendarApiException
     *
     * @return array
     */
    public function getUserSettings(): array
    {
        $url = $this->createUri('users/me/settings');
        try {
            $result = $this->googleApiClient->sendRequest('GET', $url);
        } catch (Exception $e) {
            throw new GoogleCalendarApiException($e->getMessage(), $e->getCode(), $e);
        }
        $settings = [];
        if (isset($result['items']) && is_array($result['items'])) {
            foreach ($result['items'] as $setting) {
                $settings[$setting['id']] = $setting['value'];
            }
        }

        return $settings;
    }

    /**
     * @throws GoogleCalendarApiException
     *
     * @return GoogleCalendarColorCollection
     */
    public function getAvailableColors(): GoogleCalendarColorCollection
    {
        $url = $this->createUri('colors');
        try {
            $result = $this->googleApiClient->sendRequest('GET', $url);
        } catch (Exception $e) {
            throw new GoogleCalendarApiException($e->getMessage(), $e->getCode(), $e);
        }
        $colors = GoogleCalendarColorCollection::createFromJsonArray($result);
        $default = $this->getDefaultColorId();
        $colors->setDefaultColorId($default);

        return $colors;
    }

    /**
     * @return GoogleAccountData
     */
    public function getAccount(): GoogleAccountData
    {
        return $this->googleApiClient->getAccount();
    }

    /**
     * @param string $uri
     * @param array  $queryParams
     *
     * @return string
     */
    private function createUri(string $uri, array $queryParams = []): string
    {
        $url = sprintf('%s/%s', self::BASE_URL, $uri);
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }

    /**
     * @param string $sendUpdates
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    private function validateSendUpdatesParam(string $sendUpdates): bool
    {
        if (
            $sendUpdates !== self::SENDUPDATES_DEFAULT
            && $sendUpdates !== self::SENDUPDATES_EXTERNALONLY
            && $sendUpdates !== self::SENDUPDATES_NONE
            && $sendUpdates !== self::SENDUPDATES_ALL
        ) {
            throw new InvalidArgumentException('Ivalid value for query parameter "sendUpdates".');
        }

        return true;
    }

    /**
     * @throws GoogleCalendarApiException
     *
     * @return string
     */
    private function getDefaultColorId(): string
    {
        $colorId = '';
        $filters = [];
        $filters['minAccessRole'] = 'owner';
        $calendars = $this->getCalendarList($filters);
        foreach ($calendars as $calendar) {
            if ($calendar->isPrimary()) {
                $colorId = $calendar->getColorId();
            }
        }

        return $colorId;
    }
}
