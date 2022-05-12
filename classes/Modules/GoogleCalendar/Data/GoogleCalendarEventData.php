<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Data;

use DateTime;
use DateTimeInterface;
use JsonSerializable;
use Xentral\Modules\GoogleCalendar\Exception\InvalidArgumentException;

final class GoogleCalendarEventData implements JsonSerializable
{
    /** @var string STATUS_CONFIRMED */
    public const STATUS_CONFIRMED = 'confirmed';

    /** @var string STATUS_TENTATIVE */
    public const STATUS_TENTATIVE = 'tentative';

    /** @var string STATUS_CANCELLED */
    public const STATUS_CANCELLED = 'cancelled';

    /** @var string VISIBILITY_PUBLIC */
    public const VISIBILITY_PUBLIC = 'public';

    /** @var string VISIBILITY_PRIVATE */
    public const VISIBILITY_PRIVATE = 'private';

    /** @var string VISIBILITY_DEFAULT */
    public const VISIBILITY_DEFAULT = 'default';

    /** @var string TRANSPARENCY_OPAQUE */
    public const TRANSPARENCY_OPAQUE = 'opaque';

    /** @var string TRANSPARENCY_TRANSPARENT */
    public const TRANSPARENCY_TRANSPARENT = 'transparent';

    /** @var string $kind */
    private static $kind = 'calendar#event';

    /** @var string $id */
    private $id;

    /** @var string $etag */
    private $etag;

    /** @var string $status */
    private $status;

    /** @var GoogleCalendarEventTimeValue $time */
    private $time;

    /** @var string $iCalUid */
    private $iCalUid;

    /** @var string $summary */
    private $summary;

    /** @var string $description */
    private $description;

    /** @var string $location */
    private $location;

    /** @var string $colorId */
    private $colorId;

    /** @var DateTime $created */
    private $created;

    /** @var DateTime $updated */
    private $updated;

    /** @var GoogleCalendarEventAttendeeValue $creator */
    private $creator;

    /** @var GoogleCalendarEventAttendeeValue $organizer */
    private $organizer;

    /** @var GoogleCalendarEventAttendeeValue[] $attendees */
    private $attendees;

    /** @var string $visibility */
    private $visibility;

    /** @var string $transparency */
    private $transparency;

    /** @var GoogleCalendarEventReminderValue[] */
    private $reminders;

    /** @var int $sequence */
    private $sequence;

    /** @var string $htmlLink */
    private $htmlLink;

    /**
     * @param string                             $id
     * @param string                             $etag
     * @param string                             $status
     * @param string                             $iCalUid
     * @param DateTimeInterface                  $created
     * @param DateTimeInterface                  $updated
     * @param GoogleCalendarEventAttendeeValue   $creator
     * @param GoogleCalendarEventAttendeeValue   $organizer
     * @param GoogleCalendarEventTimeValue       $time
     * @param string                             $summary
     * @param string                             $description
     * @param string                             $location
     * @param GoogleCalendarEventAttendeeValue[] $attendees
     * @param GoogleCalendarEventReminderValue[] $reminders
     * @param string                             $visibility
     * @param string                             $transparency
     * @param int                                $sequence
     * @param string                             $colorId
     * @param string                             $htmlLink
     */
    public function __construct(
        string $id,
        string $etag,
        string $status,
        string $iCalUid = '',
        DateTimeInterface $created = null,
        DateTimeInterface $updated = null,
        GoogleCalendarEventAttendeeValue $creator = null,
        GoogleCalendarEventAttendeeValue $organizer = null,
        GoogleCalendarEventTimeValue $time = null,
        string $summary = '',
        string $description = '',
        string $location = '',
        array $attendees = [],
        array $reminders = [],
        string $visibility = self::VISIBILITY_DEFAULT,
        string $transparency = self::TRANSPARENCY_OPAQUE,
        int $sequence = 0,
        string $colorId = '',
        string $htmlLink = ''
    ) {
        $this->id = $id;
        $this->etag = $etag;
        if (
            $status !== self::STATUS_CONFIRMED
            && $status !== self::STATUS_TENTATIVE
            && $status !== self::STATUS_CANCELLED
        ) {
            throw new InvalidArgumentException(
                'Invalid event status; only "confirmed", "tentative" and "cancelled" are allowed'
            );
        }
        $this->status = $status;
        $this->iCalUid = $iCalUid;
        $this->summary = $summary;
        $this->description = $description;
        $this->location = $location;
        $this->colorId = $colorId;
        $this->created = $created;
        $this->updated = $updated;
        $this->creator = $creator;
        $this->organizer = $organizer;
        $this->attendees = $attendees;
        $this->time = $time;
        if (
            $visibility !== self::VISIBILITY_DEFAULT
            && $visibility !== self::VISIBILITY_PRIVATE
            && $visibility !== self::VISIBILITY_PUBLIC
        ) {
            throw new InvalidArgumentException(
                'Invalid event visibility; only "default", "private" or "public" are allowed'
            );
        }
        $this->visibility = $visibility;
        if ($transparency !== self::TRANSPARENCY_OPAQUE && $transparency !== self::TRANSPARENCY_TRANSPARENT) {
            throw new InvalidArgumentException(
                'Invalid event transparency; only "opaque" or "transparent" are allowed'
            );
        }
        $this->transparency = $transparency;
        $this->reminders = $reminders;
        $this->sequence = $sequence;
        $this->htmlLink = $htmlLink;
    }

    /**
     * @param array $data
     *
     * @throws InvalidArgumentException
     *
     * @return GoogleCalendarEventData
     */
    public static function fromArray(array $data): GoogleCalendarEventData
    {
        if (!isset($data['kind']) || $data['kind'] !== self::$kind) {
            throw new InvalidArgumentException('Invalid resource type. Expected: calendar#event');
        }
        if (!isset($data['id'])) {
            throw new InvalidArgumentException('Missing required resorce field "id".');
        }
        if (!isset($data['etag'])) {
            throw new InvalidArgumentException('Missing required resorce field "etag".');
        }
        if (!isset($data['status'])) {
            throw new InvalidArgumentException('Missing required resorce field "etag".');
        }

        //mandatory
        $id = $data['id'];
        $etag = $data['etag'];
        $status = $data['status'];

        //optional
        $iCalUid = '';
        if (isset($data['iCalUID'])) {
            $iCalUid = $data['iCalUID'];
        }
        $created = null;
        if (isset($data['created'])) {
            $created = DateTime::createFromFormat('Y-m-d\TH:i:s.uP', $data['created']);
            if ($created === false) {
                throw new InvalidArgumentException('Invalid DateTime Format in "created".');
            }
        }
        $updated = null;
        if (isset($data['updated'])) {
            $updated = DateTime::createFromFormat('Y-m-d\TH:i:s.uP', $data['updated']);
            if ($updated === false) {
                throw new InvalidArgumentException('Invalid DateTime Format in "updated".');
            }
        }
        $creator = null;
        if (isset($data['creator'])) {
            $creator = GoogleCalendarEventAttendeeValue::createFromJsonArray($data['creator']);
        }
        $organizer = null;
        if (isset($data['organizer'])) {
            $organizer = GoogleCalendarEventAttendeeValue::createFromJsonArray($data['organizer']);
        }
        $time = null;
        if (isset($data['start'], $data['end'])) {
            $time = GoogleCalendarEventTimeValue::createFromJsonArray($data);
        }
        $sequence = 0;
        if (isset($data['sequence'])) {
            $sequence = $data['sequence'];
        }
        $summary = '';
        if (isset($data['summary'])) {
            $summary = $data['summary'];
        }
        $description = '';
        if (isset($data['description'])) {
            $description = $data['description'];
        }
        $location = '';
        if (isset($data['location'])) {
            $location = $data['location'];
        }
        $visibility = self::VISIBILITY_DEFAULT;
        if (isset($data['visibility'])) {
            $visibility = $data['visibility'];
        }
        $transparency = self::TRANSPARENCY_OPAQUE;
        if (isset($data['transparency'])) {
            $transparency = $data['transparency'];
        }
        $colorId = '';
        if (isset($data['colorId'])) {
            $colorId = $data['colorId'];
        }
        $htmlLink = '';
        if (isset($data['htmlLink'])) {
            $htmlLink = $data['htmlLink'];
        }
        $attendees = [];
        if (isset($data['attendees']) && is_array($data['attendees'])) {
            foreach ($data['attendees'] as $attendee) {
                $attendees[] = GoogleCalendarEventAttendeeValue::createFromJsonArray($attendee);
            }
        }
        $reminders = [];
        if (isset($data['reminders']['overrides']) && is_array($data['reminders']['overrides'])) {
            foreach ($data['reminders']['overrides'] as $reminder) {
                $reminders[] = GoogleCalendarEventReminderValue::fromArray($reminder);
            }
        }

        return new self(
            $id,
            $etag,
            $status,
            $iCalUid,
            $created,
            $updated,
            $creator,
            $organizer,
            $time,
            $summary,
            $description,
            $location,
            $attendees,
            $reminders,
            $visibility,
            $transparency,
            $sequence,
            $colorId,
            $htmlLink
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [];
        if ($this->getTime() !== null) {
            $time = $this->getTime()->toDataArray();
            $data = array_merge($data, $time);
        }
        if ($this->getCreator() !== null) {
            $data['creator'] = $this->getCreator()->toDataArray();
        }
        if ($this->getOrganizer() !== null) {
            $data['organizer'] = $this->getOrganizer()->toDataArray();
        }
        $data['attendees'] = [];
        foreach ($this->getAttendees() as $attendee) {
            $data['attendees'][] = $attendee->toDataArray();
        }
        $data['reminders']['overrides'] = [];
        foreach ($this->getReminders() as $reminder) {
            $data['reminders']['overrides'][] = $reminder->toArray();
        }
        if ($this->getColorId() !== '') {
            $data['colorId'] = $this->getColorId();
        }
        //$data['id'] = $this->getId();
        $data['status'] = $this->getStatus();
        //$data['iCalUid'] = $this->getICalUid();
        $data['summary'] = $this->getSummary();
        $data['description'] = $this->getDescription();
        $data['location'] = $this->getLocation();
        $data['visibility'] = $this->getVisibility();
        $data['transparency'] = $this->getTransparency();

        return $data;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEtag(): string
    {
        return $this->etag;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return GoogleCalendarEventTimeValue
     */
    public function getTime(): GoogleCalendarEventTimeValue
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getICalUid(): string
    {
        return $this->iCalUid;
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getColorId(): string
    {
        return $this->colorId;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreated(): DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @return DateTimeInterface
     */
    public function getUpdated(): DateTimeInterface
    {
        return $this->updated;
    }

    /**
     * @return GoogleCalendarEventAttendeeValue|null
     */
    public function getCreator(): ?GoogleCalendarEventAttendeeValue
    {
        return $this->creator;
    }

    /**
     * @return GoogleCalendarEventAttendeeValue|null
     */
    public function getOrganizer(): ?GoogleCalendarEventAttendeeValue
    {
        return $this->organizer;
    }

    /**
     * @return GoogleCalendarEventAttendeeValue[]
     */
    public function getAttendees(): array
    {
        return $this->attendees;
    }

    /**
     * @return string
     */
    public function getVisibility(): string
    {
        return $this->visibility;
    }

    /**
     * @return string
     */
    public function getTransparency(): string
    {
        return $this->transparency;
    }

    /**
     * @return GoogleCalendarEventReminderValue[]
     */
    public function getReminders(): array
    {
        return $this->reminders;
    }

    /**
     * @return int
     */
    public function getSequence(): int
    {
        return $this->sequence;
    }

    /**
     * @return string
     */
    public function getHtmlLink(): string
    {
        return $this->htmlLink;
    }

    /**
     * @param string $status
     *
     * @throws InvalidArgumentException
     *
     * @return GoogleCalendarEventData
     */
    public function setStatus(string $status): GoogleCalendarEventData
    {
        if (
            $status !== self::STATUS_CONFIRMED
            && $status !== self::STATUS_TENTATIVE
            && $status !== self::STATUS_CANCELLED
        ) {
            throw new InvalidArgumentException(
                'Invalid event status; only "confirmed", "tentative" and "cancelled" are allowed.'
            );
        }
        $eventData = $this->cloneDeep();
        $eventData->status = $status;

        return $eventData;
    }

    /**
     * @param DateTimeInterface $beginning
     * @param DateTimeInterface $end
     * @param bool              $wholeday
     * @param string            $timezone
     *
     * @return GoogleCalendarEventData
     */
    public function setTime(
        DateTimeInterface $beginning,
        DateTimeInterface $end,
        bool $wholeday = false,
        string $timezone = ''
    ): GoogleCalendarEventData {
        $time = new GoogleCalendarEventTimeValue($beginning, $end, $wholeday, $timezone);
        $eventData = $this->cloneDeep();
        $eventData->time = $time;

        return $eventData;
    }

    /**
     * @param string $summary
     *
     * @return GoogleCalendarEventData
     */
    public function setSummary(string $summary): GoogleCalendarEventData
    {
        $eventData = $this->cloneDeep();
        $eventData->summary = $summary;

        return $eventData;
    }

    /**
     * @param string $description
     *
     * @return GoogleCalendarEventData
     */
    public function setDescription(string $description): GoogleCalendarEventData
    {
        $eventData = $this->cloneDeep();
        $eventData->description = $description;

        return $eventData;
    }

    /**
     * @param string $location
     *
     * @return GoogleCalendarEventData
     */
    public function setLocation(string $location): GoogleCalendarEventData
    {
        $eventData = $this->cloneDeep();
        $eventData->location = $location;

        return $eventData;
    }

    /**
     * @param string $colorId
     *
     * @return GoogleCalendarEventData
     */
    public function setColorId(string $colorId): GoogleCalendarEventData
    {
        $eventData = $this->cloneDeep();
        $eventData->colorId = $colorId;

        return $eventData;
    }

    /**
     * @param string $visibility
     *
     * @throws InvalidArgumentException
     *
     * @return GoogleCalendarEventData
     */
    public function setVisibility(string $visibility): GoogleCalendarEventData
    {
        if (
            $visibility !== self::VISIBILITY_DEFAULT
            && $visibility !== self::VISIBILITY_PRIVATE
            && $visibility !== self::VISIBILITY_PUBLIC
        ) {
            throw new InvalidArgumentException(
                'Invalid event visibility; only "default", "private" or "public" are allowed'
            );
        }
        $eventData = $this->cloneDeep();
        $eventData->visibility = $visibility;

        return $eventData;
    }

    /**
     * @param string $transparency
     *
     * @throws InvalidArgumentException
     *
     * @return GoogleCalendarEventData
     */
    public function setTransparency(string $transparency): GoogleCalendarEventData
    {
        if ($transparency !== self::TRANSPARENCY_OPAQUE && $transparency !== self::TRANSPARENCY_TRANSPARENT) {
            throw new InvalidArgumentException(
                'Invalid event transparency; only "opaque" or "transparent" are allowed'
            );
        }
        $eventData = $this->cloneDeep();
        $eventData->transparency = $transparency;

        return $eventData;
    }

    /**
     * @param GoogleCalendarEventAttendeeValue|null $organizer
     *
     * @return GoogleCalendarEventData
     */
    public function setOrganizer(GoogleCalendarEventAttendeeValue $organizer = null): GoogleCalendarEventData
    {
        $eventData = $this->cloneDeep();
        $eventData->organizer = $organizer;

        return $eventData;
    }

    /**
     * @param string $email
     * @param string $displayName
     * @param bool   $optional
     *
     * @return GoogleCalendarEventData
     */
    public function addAttendee(
        string $email,
        string $displayName = '',
        bool $optional = false
    ): GoogleCalendarEventData {
        $attendee = new GoogleCalendarEventAttendeeValue($email, $displayName, $optional);
        $attendees = array_values($this->attendees);
        $attendees[] = $attendee;

        $eventData = $this->cloneDeep();
        $eventData->attendees = $attendees;

        return $eventData;
    }

    /**
     * @return GoogleCalendarEventData
     */
    public function removeAttendees(): GoogleCalendarEventData
    {
        $eventData = $this->cloneDeep();
        $eventData->attendees = [];

        return $eventData;
    }

    /**
     * @param string $method
     * @param int    $minutes
     *
     * @throws InvalidArgumentException
     *
     * @return GoogleCalendarEventData
     */
    public function addReminder(string $method, int $minutes): GoogleCalendarEventData
    {
        $reminder = new GoogleCalendarEventReminderValue($method, $minutes);
        $reminders = array_values($this->reminders);
        $reminders[] = $reminder;

        $eventData = $this->cloneDeep();
        $eventData->reminders = $reminders;

        return $eventData;
    }

    /**
     * @return GoogleCalendarEventData
     */
    public function removeReminders(): GoogleCalendarEventData
    {
        $eventData = $this->cloneDeep();
        $eventData->reminders = [];

        return $eventData;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return GoogleCalendarEventData
     */
    private function cloneDeep(): GoogleCalendarEventData
    {
        $time = $this->time;
        if ($this->time !== null) {
            $time = new GoogleCalendarEventTimeValue(
                $this->time->getBeginning(),
                $this->time->getEnd(),
                $this->time->isWholeday(),
                $this->time->getTimezone()
            );
        }

        $created = null;
        if ($this->created !== null) {
            $created = clone $this->created;
        }
        $updated = null;
        if ($this->updated !== null) {
            $updated = clone $this->updated;
        }
        $creator = null;
        if ($this->creator !== null) {
            $creator = clone $this->creator;
        }
        $organizer = null;
        if ($this->organizer !== null) {
            $organizer = clone $this->organizer;
        }

        return new self(
            $this->id,
            $this->etag,
            $this->status,
            $this->iCalUid,
            $created,
            $updated,
            $creator,
            $organizer,
            $time,
            $this->summary,
            $this->description,
            $this->location,
            $this->attendees,
            $this->reminders,
            $this->visibility,
            $this->transparency,
            $this->sequence,
            $this->colorId,
            $this->htmlLink
        );
    }
}
