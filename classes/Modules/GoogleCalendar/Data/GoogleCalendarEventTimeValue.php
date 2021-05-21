<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Data;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Xentral\Modules\GoogleCalendar\Exception\InvalidArgumentException;

final class GoogleCalendarEventTimeValue
{
    /** @var DateTimeInterface $beginning */
    private $beginning;

    /** @var DateTimeInterface $end */
    private $end;

    /** @var bool $wholeday */
    private $wholeday;

    /** @var string $timezone */
    private $timezone;

    /**
     * @param DateTimeInterface $beginning
     * @param DateTimeInterface $end
     * @param bool              $wholeday
     * @param string            $timezone
     */
    public function __construct(
        DateTimeInterface $beginning,
        DateTimeInterface $end,
        bool $wholeday = false,
        string $timezone = ''
    ) {
        $this->beginning = $beginning;
        $this->end = $end;
        $this->wholeday = $wholeday;
        $this->timezone = $timezone;
    }

    /**
     * @param array $data
     *
     * @throws InvalidArgumentException
     *
     * @return GoogleCalendarEventTimeValue
     */
    public static function createFromJsonArray(array $data): GoogleCalendarEventTimeValue
    {
        if (!array_key_exists('start', $data) || !array_key_exists('end', $data)) {
            throw new InvalidArgumentException('Data format invalid.');
        }

        $begin = null;
        $end = null;
        $wholeday = false;
        $timeZone = '';

        if (isset($data['start']['dateTime'], $data['end']['dateTime'])) {
            $begin = DateTime::createFromFormat(DateTimeInterface::RFC3339, $data['start']['dateTime']);
            $end = DateTime::createFromFormat(DateTimeInterface::RFC3339, $data['end']['dateTime']);
            if (isset($data['start']['timeZone'])) {
                $timeZone = $data['start']['timeZone'];
            }
        }

        if (isset($data['start']['date'], $data['end']['date'])) {
            $formatted = sprintf('%s 00:00:00', $data['start']['date']);
            $begin = DateTime::createFromFormat('Y-m-d H:i:s', $formatted);
            $formatted = sprintf('%s 00:00:00', $data['end']['date']);
            $end = DateTime::createFromFormat('Y-m-d  H:i:s',$formatted);
            $wholeday = true;
        }

        if (empty($begin) || empty($end)) {
            throw new InvalidArgumentException('Data format invalid.');
        }

        return new GoogleCalendarEventTimeValue($begin, $end, $wholeday, $timeZone);
    }

    /**
     * @return array
     */
    public function toDataArray(): array
    {
        $data = [];

        if ($this->isWholeday()) {
            $data['start']['date'] = $this->getBeginning()->format('Y-m-d');
            $data['end']['date'] = $this->getEnd()->format('Y-m-d');
        } else {
            $data['start']['dateTime'] = $this->getBeginning()->format(DateTimeInterface::RFC3339);
            $data['end']['dateTime'] = $this->getEnd()->format(DateTimeInterface::RFC3339);
        }
        if ($this->getTimezone() !== '') {
            $data['start']['timeZone'] = $this->getTimezone();
            $data['end']['timeZone'] = $this->getTimezone();
        }

        return $data;
    }

    /**
     * @return DateTimeInterface
     */
    public function getBeginning(): DateTimeInterface
    {
        return $this->beginning;
    }

    /**
     * @return DateTimeInterface
     */
    public function getEnd(): DateTimeInterface
    {
        return $this->end;
    }

    /**
     * returns duration in seconds
     * 
     * @return int
     */
    public function getDuration(): int
    {
        return (int) $this->end->getTimestamp() - $this->beginning->getTimestamp();
    }

    /**
     * @return DateInterval
     */
    public function getInterval(): DateInterval
    {
        return $this->end->diff($this->beginning, true);
    }

    /**
     * @return bool
     */
    public function isWholeday(): bool
    {
        return $this->wholeday;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }
}
