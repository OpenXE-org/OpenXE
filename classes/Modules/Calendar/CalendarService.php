<?php

declare(strict_types=1);

namespace Xentral\Modules\Calendar;

use Exception;
use Xentral\Components\Database\Database;
use Xentral\Modules\Calendar\Data\CalendarEvent;
use Xentral\Modules\Calendar\Data\CalendarEventUser;
use Xentral\Modules\Calendar\Exception\CalendarEventDeleteException;
use Xentral\Modules\Calendar\Exception\CalendarEventSaveException;
use Xentral\Modules\GoogleCalendar\Exception\GoogleCalendarException;

final class CalendarService
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param $eventId
     *
     * @return CalendarEvent|null
     */
    public function tryGetEvent(int $eventId): ?CalendarEvent
    {
        $event = $this->tryGetEventWithoutUsers($eventId);
        if ($event !== null) {
            $users = $this->tryGetEventUsers($eventId);
            foreach ($users as $user) {
                $event->addAttendee($user);
            }
        }

        return $event;
    }

    /**
     * @param $eventId
     *
     * @return CalendarEvent|null
     */
    public function tryGetEventWithoutUsers(int $eventId): ?CalendarEvent
    {
        if($eventId < 1) {
            return null;
        }
        $sql = 'SELECT e.id, e.kalender as `calendar_id`, e.bezeichnung as `title`, e.beschreibung as `description`,
                       e.von as `start`, e.bis as `end`, e.allDay as `all_day`, e.color, e.public, 
                       e.ort as `location`, e.typ as `type`, e.angelegtvon as `creator_id`,
                       e.adresseintern as `organizer_id`, e.adresse as `attendee_id`
                FROM kalender_event AS e 
                WHERE e.id = :idValue';
        $values = ['idValue' => $eventId];
        $result = $this->db->fetchRow($sql, $values);
        if (!empty($result)) {
            return CalendarEvent::fromDbState($result);
        }

        return null;
    }

    /**
     * @param int $eventId
     *
     * @return CalendarEventUser[]
     */
    public function tryGetEventUsers(int $eventId): array
    {
        $sql = 'SELECT ku.id, ku.event as `event_id`, ku.userid as `user_id`, ku.gruppe as `group_id`,
                       u.adresse as `address_id`, a.email as `email`
                FROM `kalender_user` AS ku
                LEFT JOIN `user` AS u ON ku.userid = u.id
                LEFT JOIN `adresse` AS a ON u.adresse = a.id
                WHERE ku.event = :eventId';
        $values = ['eventId' => $eventId];
        $rows = $this->db->fetchAll($sql, $values);

        if (empty($rows)) {
            return [];
        }

        $users = [];
        foreach ($rows as $row) {
            $users[] = CalendarEventUser::fromDbState($row);
        }

        return $users;
    }

    /**
     * @param CalendarEvent $event
     *
     * @return int
     */
    public function saveEvent(CalendarEvent $event): int
    {
        if ($event->getId() > 0 && $this->eventExists($event->getId())) {
            return $this->updateEvent($event);
        }

        return $this->insertEvent($event);
    }

    /**
     * @param int $eventId
     *
     * @throws CalendarEventDeleteException
     *
     * @return void
     */
    public function deleteEvent(int $eventId): void
    {
        $this->db->beginTransaction();
        try {
            $sql = 'DELETE FROM `kalender_event` WHERE id = :eventId';
            $values = ['eventId' => $eventId];
            $this->db->perform($sql, $values);
            $deleteUser = 'DELETE FROM `kalender_user` WHERE event = :eventId';
            $this->db->perform($deleteUser, $values);
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new CalendarEventDeleteException($e->getMessage(), $e->getCode(), $e);
        }
        $this->db->commit();
    }

    /**
     * @param int $eventId
     * @param int $userId
     *
     * @return void
     */
    public function removeUserFromEvent(int $eventId, int $userId): void
    {
        $sql = 'DELETE FROM `kalender_user` WHERE event = :eventId AND userid = :userId';
        $values = ['eventId' => $eventId, 'userId' => $userId];
        $this->db->perform($sql, $values);
    }

    /**
     * @param $eventId
     *
     * @return bool
     */
    public function eventExists(int $eventId): bool
    {
        if ($eventId < 1) {
            return false;
        }

        $sql  = 'SELECT e.id FROM `kalender_event` AS e WHERE e.id = :idValue';
        $values = ['idValue' => (int)$eventId];
        $result = $this->db->fetchRow($sql, $values);

        return (isset($result['id']) && $result['id'] === $eventId);
    }

    /**
     * @param CalendarEvent $event
     *
     * @throws CalendarEventSaveException
     *
     * @return int
     */
    private function insertEvent(CalendarEvent $event): int
    {
        $this->db->beginTransaction();
        try {
            $insertEvent = 'INSERT INTO kalender_event 
                (kalender, bezeichnung, beschreibung, von, bis, allDay, color,
                 public, ort, typ, angelegtvon, adresseintern, adresse)
                VALUES
                (:calendar_id, :title, :description, :start, :end, :all_day, :color, 
                 :public, :location,:type,  :creator_id, :organizer_id, :attendee_id)';
            $values = $this->getBindValuesFromEvent($event);
            $this->db->perform($insertEvent, $values);
            $newId = $this->db->lastInsertId();
            $users = $event->getAllUsers();
            $this->insertEventUsers($users, $newId);
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new CalendarEventSaveException($e->getMessage(), $e->getCode(), $e);
        }

        $this->db->commit();

        return $newId;
    }

    /**
     * @param CalendarEvent $event
     *
     * @throws CalendarEventSaveException
     *
     * @return int
     */
    private function updateEvent(CalendarEvent $event): int
    {
        $this->db->beginTransaction();

        try {
            $sql = 'UPDATE kalender_event SET 
                          kalender = :calendar_id,
                          bezeichnung = :title,
                          beschreibung = :description,
                          von = :start,
                          bis = :end,
                          allDay = :all_day,
                          color = :color,
                          public = :public,
                          ort = :location,
                          angelegtvon = :creator_id,
                          adresseintern = :organizer_id,
                          adresse = :attendee_id,
                          typ = :type
                WHERE id = :id';
            $values = $this->getBindValuesFromEvent($event);
            $this->db->perform($sql, $values);
            $this->deleteEventUsers($event->getId());
            $users = $event->getAllUsers();
            $this->insertEventUsers($users, $event->getId());
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new CalendarEventSaveException($e->getMessage(), $e->getCode(), $e);
        }

        $this->db->commit();

        return $event->getId();
    }

    /**
     * @param $users
     * @param $eventId
     *
     * @return void
     */
    private function insertEventUsers($users, $eventId): void
    {
        if (!is_array($users) || count($users) < 1) {
            return;
        }
        $insert = $this->db->insert();
        $insert->into('kalender_user');
        foreach ($users as $user) {
            $insert->addRow()
                ->cols([
                    'event'  => $eventId,
                    'userid' => $user->getUserId(),
                    'gruppe' => $user->getGroupId(),
                ]);
        }

        $insertSql = $insert->getStatement();
        $values = $insert->getBindValues();
        $this->db->perform($insertSql, $values);
    }

    /**
     * @param $eventId
     *
     * @return void
     */
    private function deleteEventUsers($eventId): void
    {
        $sql = 'DELETE FROM kalender_user WHERE event = :eventId';
        $this->db->perform($sql, ['eventId' => $eventId]);
    }

    /**
     * @param CalendarEvent $event
     *
     * @return array
     */
    private function getBindValuesFromEvent(CalendarEvent $event): array
    {
        $values = $event->toArray();
        if (empty($values['start'])) {
            $values['start'] = null;
        }
        if (empty($values['end'])) {
            $values['end'] = null;
        }

        return $values;
    }
}
