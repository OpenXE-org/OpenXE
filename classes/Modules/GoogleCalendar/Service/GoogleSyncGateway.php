<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\GoogleCalendar\Data\GoogleCalenderSyncValue;

final class GoogleSyncGateway
{
    /**  @var Database $db */
    private $db;

    /**
     * @param Database               $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * Gets all sync entries as key-value list.
     *
     * @return array key-value pairs ['GoogleEventId' => 'EventId']
     */
    public function getAllSyncEntries(): array
    {
        $sql = 'SELECT s.foreign_id, s.event_id
                FROM `googleapi_calendar_sync` AS `s`
                ORDER BY s.id DESC
                LIMIT 30';

        return $this->db->fetchPairs($sql);
    }

    /**
     * @param int $entryId
     *
     * @return bool true = entry exists
     */
    public function existsSyncEntry(int $entryId): bool
    {
        $sql = 'SELECT `id` FROM `googleapi_calendar_sync` WHERE `id` = :id';

        return $entryId === $this->db->fetchValue($sql, ['id' => $entryId]);
    }

    /**
     * @param int $eventId
     *
     * @return GoogleCalenderSyncValue|null
     */
    public function tryGetSyncEntryByEvent(int $eventId): ?GoogleCalenderSyncValue
    {
        if ($eventId < 1) {
            return null;
        }
        $sql = 'SELECT s.id, s.event_id, s.foreign_id, s.owner, s.from_google, s.event_date, s.html_link
                FROM `googleapi_calendar_sync` AS `s`
                WHERE s.event_id = :event_id LIMIT 1';
        $row = $this->db->fetchRow($sql, ['event_id' => $eventId]);
        if (empty($row)) {
            return null;
        }

        return GoogleCalenderSyncValue::fromDbState($row);
    }

    /**
     * @param string $googleId
     *
     * @return GoogleCalenderSyncValue|null
     */
    public function tryGetSyncEntryByGoogleEvent(string $googleId): ?GoogleCalenderSyncValue
    {
        if ($googleId === '') {
            return null;
        }
        $sql = 'SELECT s.id, s.event_id, s.foreign_id,s.owner, s.from_google, s.event_date, s.html_link
                FROM `googleapi_calendar_sync` AS `s`
                WHERE s.foreign_id = :google_id LIMIT 1';
        $row = $this->db->fetchRow($sql, ['google_id' => $googleId]);
        if (empty($row)) {
            return null;
        }

        return GoogleCalenderSyncValue::fromDbState($row);
    }
}
