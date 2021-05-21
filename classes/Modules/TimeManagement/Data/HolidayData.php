<?php

declare(strict_types=1);

namespace Xentral\Modules\TimeManagement\Data;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Xentral\Modules\TimeManagement\Exception\InvalidDateFormatException;

final class HolidayData
{
    /** @var string $name */
    private $name = 'Unknown';

    /** @var DateTimeInterface $date */
    private $date;

    private function __construct()
    {
    }

    /**
     * @param array $data
     *
     * @throws InvalidDateFormatException
     *
     * @return HolidayData
     *
     */
    public static function fromDbState(array $data): HolidayData
    {
        $holidayData = new HolidayData();

        $holidayData->name = $data['name'];
        try {
            $holidayData->date = new DateTimeImmutable($data['date']);
        } catch (Exception $e) {
            throw new InvalidDateFormatException('Could not convert date: ' . $data['date']);
        }

        return $holidayData;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
