<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\GoogleCalendar\Data\GoogleCalenderSyncValue;

final class GoogleSyncService
{
    /**  @var Database $db */
    private $db;

    /**  @var GoogleSyncGateway $gateway */
    private $gateway;

    /**
     * @param Database          $database
     * @param GoogleSyncGateway $gateway
     */
    public function __construct(Database $database, GoogleSyncGateway $gateway)
    {
        $this->db = $database;
        $this->gateway = $gateway;
    }

    /**
     * @param GoogleCalenderSyncValue $sync
     *
     * @return int new Id
     */
    public function saveSyncEntry(GoogleCalenderSyncValue $sync): int
    {
        if ($sync->getId() > 0 && $this->gateway->existsSyncEntry($sync->getId())) {
            return $this->updateSyncEntry($sync);
        }

        return $this->insertSyncEntry($sync);
    }

    /**
     * @param string $googleEventId
     * @param int    $calendarEventId
     *
     * @return void
     */
    public function deleteSyncEntry(string $googleEventId, int $calendarEventId): void
    {
        $sql = 'DELETE FROM `googleapi_calendar_sync` WHERE `foreign_id` = :foreign_id AND `event_id` = :event_id';
        $values = ['foreign_id' => $googleEventId, 'event_id' => $calendarEventId];
        $this->db->perform($sql, $values);
    }

    /**
     * @param GoogleCalenderSyncValue $sync
     *
     * @return int
     */
    private function insertSyncEntry(GoogleCalenderSyncValue $sync): int
    {
        $sql = 'INSERT INTO `googleapi_calendar_sync`
                (`event_id`, `foreign_id`, `owner`, `from_google`, `event_date`, `html_link`) VALUES 
                (:event_id, :foreign_id, :owner, :from_google, :event_date, :html_link)';
        $values = [
            'event_id'    => $sync->getEventId(),
            'foreign_id'  => $sync->getGoogleId(),
            'owner'       => $sync->getOwner(),
            'from_google' => (int)$sync->isFromGoogle(),
            'event_date'  => $sync->getEventDateAsString(),
            'html_link'   => $sync->getHtmlLink(),
        ];
        $this->db->perform($sql, $values);

        return $this->db->lastInsertId();
    }

    /**
     * @param GoogleCalenderSyncValue $sync
     *
     * @return int updated Id
     */
    private function updateSyncEntry(GoogleCalenderSyncValue $sync): int
    {
        $sql = 'UPDATE `googleapi_calendar_sync`
                    SET `event_id` = :event_id,
                        `foreign_id` = :foreign_id,
                        `event_date` = :event_date,
                        `owner` = :owner
                    WHERE `id` = :id';
        $values = [
            'id'         => $sync->getId(),
            'event_id'   => $sync->getEventId(),
            'foreign_id' => $sync->getGoogleId(),
            'event_date' => $sync->getEventDateAsString(),
            'owner'      => $sync->getOwner(),
        ];
        $this->db->fetchAffected($sql, $values);

        return $sync->getId();
    }
}
