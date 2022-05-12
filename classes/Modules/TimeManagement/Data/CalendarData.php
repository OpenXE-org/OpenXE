<?php

declare(strict_types=1);

namespace Xentral\Modules\TimeManagement\Data;

use DateTimeImmutable;
use Exception;
use DateTimeInterface;
use Xentral\Modules\TimeManagement\Exception\InvalidDateFormatException;

final class CalendarData
{
    /** @var int $month */
    private $month = 0;

    /** @var DateTimeInterface $date */
    private $date;

    /** @var int $addressId */
    private $addressId = 0;

    /** @var string $employeeName */
    private $employeeName = '';

    /** @var string $type */
    private $type = '';

    /** @var bool $isHalf */
    private $isHalf = false;

    private function __construct()
    {
    }

    /**
     * @param array $data
     *
     * @return CalendarData
     *
     * @throws InvalidDateFormatException
     */
    public static function fromDbState(array $data): CalendarData
    {
        $calendarData = new CalendarData();
        $calendarData->month = (int)$data['month'];
        try {
            $calendarData->date = new DateTimeImmutable($data['date']);
        } catch (Exception $e) {
            throw new InvalidDateFormatException('Could not convert date: ' . $data['date']);
        }

        $calendarData->addressId = (int)$data['address_id'];
        $calendarData->employeeName = (string)$data['name'];
        $calendarData->type = (string)$data['type'];
        $calendarData->isHalf = (bool)$data['is_half'];

        return $calendarData;
    }

    /**
     * @return int
     */
    public function getMonth(): int
    {
        return $this->month;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getAddressId(): int
    {
        return $this->addressId;
    }

    /**
     * @return string
     */
    public function getEmployeeName(): string
    {
        return $this->employeeName;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isHalf(): bool
    {
        return $this->isHalf;
    }
}
