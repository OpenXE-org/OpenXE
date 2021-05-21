<?php

declare(strict_types=1);

namespace Xentral\Modules\Calendar\Data;

use DateTime;
use DateTimeInterface;
use Exception;
use Xentral\Modules\Calendar\Exception\InvalidArgumentException;

final class CalendarEvent
{
    /** @var int $id */
    private $id;

    /** @var int $calendarId */
    private $calendarId;

    /** @var string $title */
    private $title;

    /** @var string $description */
    private $description;

    /** @var DateTimeInterface $start */
    private $start;

    /** @var DateTimeInterface $end */
    private $end;

    /** @var bool $allDay */
    private $allDay;

    /** @var string $color */
    private $color;

    /** @var bool $public */
    private $public;

    /** @var string $location */
    private $location;

    /** @var CalendarEventUser $creator */
    private $creator;

    /** @var CalendarEventUser $organizer */
    private $organizer;

    /** @var string $type */
    private $type;

    /** @var CalendarEventUser[] $attendees */
    private $attendees;

    /**
     * @param int                 $id
     * @param int                 $calendarId
     * @param string              $title
     * @param string              $description
     * @param DateTimeInterface   $start
     * @param DateTimeInterface   $end
     * @param bool                $allDay
     * @param string              $color
     * @param bool                $public
     * @param string              $location
     * @param CalendarEventUser   $creator
     * @param CalendarEventUser   $organizer
     * @param string              $type
     * @param CalendarEventUser[] $attendees
     */
    public function __construct(
        int $id,
        int $calendarId = 0,
        string $title = '',
        string $description = '',
        DateTimeInterface $start = null,
        DateTimeInterface $end = null,
        bool $allDay = false,
        string $color = '',
        bool $public = false,
        string $location = '',
        CalendarEventUser $creator = null,
        CalendarEventUser $organizer = null,
        string $type = '',
        array $attendees = []
    ) {
        $this->id = $id;
        $this->calendarId = $calendarId;
        $this->title = $title;
        $this->description = $description;
        $this->start = $start;
        $this->end = $end;
        $this->allDay = $allDay;
        $this->color = $color;
        $this->public = $public;
        $this->location = $location;
        $this->creator = $creator;
        $this->organizer = $organizer;
        $this->type = $type;
        $this->attendees = $attendees;
    }

    /**
     * @param array $dataSet
     *
     * @return CalendarEvent
     */
    public static function fromDbState(array $dataSet): CalendarEvent
    {
        if (!isset($dataSet['id'], $dataSet['calendar_id'], $dataSet['title'])) {
            throw new InvalidArgumentException('Invalid or incomplete event data.');
        }
        $instance = new self((int)$dataSet['id'], (int)$dataSet['calendar_id'], $dataSet['title']);

        if (isset($dataSet['description'])) {
            $instance->description = $dataSet['description'];
        }
        if (isset($dataSet['start'])) {
            $instance->start = DateTime::createFromFormat('Y-m-d H:i:s', $dataSet['start']);
        }
        if (isset($dataSet['end'])) {
            $instance->end = DateTime::createFromFormat('Y-m-d H:i:s', $dataSet['end']);
        }
        if (isset($dataSet['all_day'])) {
            $instance->allDay = $dataSet['all_day'] === 1;
        }
        if (isset($dataSet['color'])) {
            $instance->color = $dataSet['color'];
        }
        if (isset($dataSet['public'])) {
            $instance->public = $dataSet['public'] === 1;
        }
        if (isset($dataSet['location'])) {
            $instance->location = $dataSet['location'];
        }
        if (isset($dataSet['type'])) {
            $instance->type = $dataSet['type'];
        }
        if (isset($dataSet['creator_id'])) {
            $creator = new CalendarEventUser(0, $dataSet['id'], 0, 0, $dataSet['creator_id']);
            $instance->setCreator($creator);
        }
        if (isset($dataSet['organizer_id'])) {
            $organizer = new CalendarEventUser(0, $dataSet['id'], 0, 0, $dataSet['organizer_id']);
            $instance->setOrganizer($organizer);
        }
        if (isset($dataSet['attendee_id']) && $dataSet['attendee_id'] > 0) {
            $attendee = new CalendarEventUser(0, $dataSet['id'], 0, 0, $dataSet['attendee_id']);
            $instance->addAttendee($attendee);
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        if ($this->start === null) {
            $start = '';
        } else {
            $start = $this->start->format('Y-m-d H:i:s');
        }

        if ($this->end === null) {
            $end = '';
        } else {
            $end = $this->end->format('Y-m-d H:i:s');
        }

        $mainAttendee = 0;
        if ($this->getPrimaryAttendee() !== null) {
            $mainAttendee = $this->getPrimaryAttendee()->getAddressId();
        }
        $creatorAddress = 0;
        if ($this->getCreator() !== null) {
            $creatorAddress = $this->getCreator()->getAddressId();
        }
        $organizerAddress = 0;
        if ($this->getOrganizer() !== null) {
            $organizerAddress = $this->getOrganizer()->getAddressId();
        }

        return [
            'id'           => $this->getId(),
            'calendar_id'  => $this->getCalendarId(),
            'title'        => $this->getTitle(),
            'description'  => $this->getDescription(),
            'start'        => $start,
            'end'          => $end,
            'all_day'      => (int)$this->isAllDay(),
            'color'        => $this->getColor(),
            'public'       => (int)$this->isPublic(),
            'location'     => $this->getLocation(),
            'creator_id'   => $creatorAddress,
            'organizer_id' => $organizerAddress,
            'type'         => $this->getType(),
            'attendee_id'  => $mainAttendee,
        ];
    }

    /**
     * @param CalendarEventUser $creator
     */
    public function setCreator(CalendarEventUser $creator): void
    {
        $this->creator = $creator;
    }

    /**
     * @param CalendarEventUser $organizer
     */
    public function setOrganizer(CalendarEventUser $organizer): void
    {
        $this->organizer = $organizer;
    }

    /**
     * @param CalendarEventUser $attendee
     */
    public function addAttendee(CalendarEventUser $attendee): void
    {
        $this->attendees[] = $attendee;
    }

    /**
     * @return CalendarEventUser|null
     */
    public function getPrimaryAttendee(): ?CalendarEventUser
    {
        if ($this->attendees === null || empty($this->attendees[0])) {
            return null;
        }

        $nonUsers = [];
        foreach ($this->attendees as $attendee) {
            if ($attendee->getUserId() === 0) {
                $nonUsers[] = $attendee;
            }
        }
        if (count($nonUsers) === 0) {
            return null;
        }

        return $nonUsers[0];
    }

    /**
     * @return CalendarEventUser[]
     */
    public function getAllUsers(): array
    {
        $ids = [];
        $users = [];
        $attendees = $this->attendees;

        if ($this->creator !== null) {
            $attendees[] = $this->creator;
        }
        if ($this->organizer !== null) {
            $attendees[] = $this->organizer;
        }
        foreach ($attendees as $attendee) {
            if ($attendee->getAddressId() > 0 && !in_array($attendee->getAddressId(), $ids, true)) {
                $ids[] = $attendee->getAddressId();
                $users[] = $attendee;
            }
        }

        return $users;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getCalendarId(): int
    {
        return $this->calendarId;
    }

    /**
     * @param int $calendarId
     */
    public function setCalendarId(int $calendarId):void
    {
        $this->calendarId = $calendarId;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return DateTimeInterface
     */
    public function getStart(): DateTimeInterface
    {
        return $this->start;
    }

    /**
     * @param DateTimeInterface $start
     */
    public function setStart(DateTimeInterface $start): void
    {
        try {
            $this->start = new DateTime();
            $this->start->setTimestamp($start->getTimestamp());
        } catch (Exception $e) {
            $this->start = null;
        }
    }

    /**
     * @return DateTimeInterface
     */
    public function getEnd(): DateTimeInterface
    {
        return $this->end;
    }

    /**
     * @param DateTimeInterface $end
     */
    public function setEnd(DateTimeInterface $end): void
    {
        try {
            $this->end = new DateTime();
            $this->end->setTimestamp($end->getTimestamp());
        } catch (Exception $e) {
            $this->end = null;
        }
    }

    /**
     * @return bool
     */
    public function isAllDay(): bool
    {
        return $this->allDay;
    }

    /**
     * @param bool $allDay
     */
    public function setAllDay(bool $allDay): void
    {
        $this->allDay = $allDay;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->public;
    }

    /**
     * @param bool $public
     */
    public function setPublic(bool $public): void
    {
        $this->public = $public;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return CalendarEventUser|null
     */
    public function getCreator(): ?CalendarEventUser
    {
        return $this->creator;
    }

    /**
     * @return CalendarEventUser|null
     */
    public function getOrganizer(): ?CalendarEventUser
    {
        return $this->organizer;
    }
}
