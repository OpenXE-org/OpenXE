<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Data;

use Xentral\Modules\GoogleCalendar\Exception\InvalidArgumentException;

final class GoogleCalendarListItem
{
    /** @var string ROLE_READER */
    public const ROLE_READER = 'reader';

    /** @var string ROLE_OWNER */
    public const ROLE_OWNER = 'owner';

    /** @var string ROLE_FREEBUSYREADER */
    public const ROLE_FREEBUSYREADER = 'freeBusyReader';

    /** @var string $kind */
    private static $kind = 'calendar#calendarListEntry';

    /** @var string $id */
    private $id;

    /** @var string $summary */
    private $summary;

    /** @var string $role */
    private $role;

    /** @var string $timeZone */
    private $timeZone;

    /** @var string $colorId */
    private $colorId;

    /** @var bool $selected */
    private $selected;

    /** @var bool $primary */
    private $primary;

    /**
     * @param string $id
     * @param string $summary
     * @param string $role
     * @param string $timeZone
     * @param string $colorId
     * @param bool   $selected
     * @param bool   $primary
     */
    public function __construct($id, $summary, $role, $timeZone, $colorId, $selected = false, $primary = false)
    {
        $this->id = $id;
        $this->summary = $summary;
        if ($role !== self::ROLE_FREEBUSYREADER && $role !== self::ROLE_READER && $role !== self::ROLE_OWNER) {
            throw  new InvalidArgumentException('Invalid calendar Role.');
        }
        $this->role = $role;
        $this->timeZone = $timeZone;
        $this->colorId = $colorId;
        $this->selected = $selected;
        $this->primary = $primary;
    }

    /**
     * @param $data
     *
     * @return GoogleCalendarListItem
     */
    public static function fromArray($data)
    {
        if (!isset($data['kind']) || $data['kind'] !== self::$kind) {
            throw new InvalidArgumentException('Invalid resource type. Expected: calendar#event');
        }
        if (!isset($data['id'])) {
            throw new InvalidArgumentException('Missing required resorce field "id".');
        }
        $id = $data['id'];
        $summary = $data['summary'];
        $role = $data['accessRole'];
        $timeZone = $data['timeZone'];
        $colorId = $data['colorId'];
        $selected = (isset($data['selected']) && $data['selected'] === true);
        $primary = (isset($data['primary']) && $data['primary'] === true);

        return new self($id, $summary, $role, $timeZone, $colorId, $selected, $primary);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * @return string
     */
    public function getColorId()
    {
        return $this->colorId;
    }

    /**
     * @return bool
     */
    public function isSelected()
    {
        return $this->selected;
    }

    /**
     * @return bool
     */
    public function isPrimary()
    {
        return $this->primary;
    }
}
