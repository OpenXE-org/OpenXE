<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Data;

use DateTime;
use DateTimeInterface;

final class GoogleCalenderSyncValue
{
    /** @var int $id */
    private $id;

    /** @var int $eventId */
    private $eventId;

    /** @var string|null $googleId */
    private $googleId;

    /** @var bool $isFromGoogle */
    private $isFromGoogle;

    /** @var DateTimeInterface|null $eventDate */
    private $eventDate;

    /** @var int $owner */
    private $owner;

    /** @var string|null $htmlLink */
    private $htmlLink;

    /**
     * @param int                    $id
     * @param int                    $eventId
     * @param string|null            $googleId
     * @param int                    $owner
     * @param bool                   $isFromGoogle
     * @param DateTimeInterface|null $eventDate
     * @param string|null            $htmlLink
     */
    public function __construct(
        int $id = 0,
        int $eventId = 0,
        string $googleId = null,
        int $owner = 0,
        bool $isFromGoogle = false,
        DateTimeInterface $eventDate = null,
        string $htmlLink = null
    ) {
        $this->id = $id;
        $this->eventId = $eventId;
        $this->googleId = $googleId;
        $this->isFromGoogle = $isFromGoogle;
        $this->eventDate = $eventDate;
        $this->owner = $owner;
        $this->htmlLink = $htmlLink;
    }

    /**
     * @return string
     */
    public function getEventDateAsString(): string
    {
        if ($this->eventDate === null) {
            return '';
        }

        return $this->eventDate->format('Y-m-d H:i:s');
    }

    /**
     * @param array $data
     *
     * @return GoogleCalenderSyncValue
     */
    public static function fromDbState(array $data): GoogleCalenderSyncValue
    {
        $instance = new self(
            $data['id'],
            $data['event_id'],
            $data['foreign_id'],
            $data['owner'],
            $data['from_google'] === 1,
            null,
            $data['html_link']
        );
        if ($data['event_date'] !== null) {
            $instance->setEventDate(DateTime::createFromFormat('Y-m-d H:i:s', $data['event_date']));
        }

        return $instance;
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
     *
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getEventId(): int
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     *
     * @return void
     */
    public function setEventId(int $eventId): void
    {
        $this->eventId = $eventId;
    }

    /**
     * @return string
     */
    public function getGoogleId(): string
    {
        if ($this->googleId === null) {
            return '';
        }

        return $this->googleId;
    }

    /**
     * @param string $googleId
     *
     * @return void
     */
    public function setGoogleId(string $googleId): void
    {
        $this->googleId = $googleId;
    }

    /**
     * @return bool
     */
    public function isFromGoogle(): bool
    {
        return $this->isFromGoogle;
    }

    /**
     * @param bool $isFromGoogle
     *
     * @return void
     */
    public function setIsFromGoogle(bool $isFromGoogle): void
    {
        $this->isFromGoogle = $isFromGoogle;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getEventDate(): ?DateTimeInterface
    {
        return $this->eventDate;
    }

    /**
     * @param DateTimeInterface $eventDate
     *
     * @return void
     */
    public function setEventDate(DateTimeInterface $eventDate): void
    {
        $this->eventDate = $eventDate;
    }

    /**
     * @return int
     */
    public function getOwner(): int
    {
        return $this->owner;
    }

    /**
     * @param int $owner
     *
     * @return void
     */
    public function setOwner(int $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return string
     */
    public function getHtmlLink(): string
    {
        if ($this->htmlLink === null) {
            return '';
        }

        return $this->htmlLink;
    }

    /**
     * @param string $htmlLink
     *
     * @return void
     */
    public function setHtmlLink(string $htmlLink): void
    {
        $this->htmlLink = $htmlLink;
    }
}
