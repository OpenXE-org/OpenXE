<?php

declare(strict_types=1);

namespace Xentral\Modules\TimeManagement\Data;

final class WorkDayData
{

    /** @var bool $isMondayWorkDay */
    private $isMondayWorkDay = false;

    /** @var bool $isTuesdayWorkDay */
    private $isTuesdayWorkDay = false;

    /** @var bool $isWednesdayWorkDay */
    private $isWednesdayWorkDay = false;

    /** @var bool $isThursdayWorkDay */
    private $isThursdayWorkDay = false;

    /** @var bool $isFridayWorkDay */
    private $isFridayWorkDay = false;

    /** @var bool $isSaturdayWorkDay */
    private $isSaturdayWorkDay = false;

    /** @var bool $isSundayWorkDay */
    private $isSundayWorkDay = false;

    private function __construct()
    {
    }

    public static function fromDbState(array $data): WorkDayData
    {
        $workDayData = new WorkDayData();

        $workDayData->isMondayWorkDay = !empty($data['is_monday_workday']);
        $workDayData->isTuesdayWorkDay = !empty($data['is_tuesday_workday']);
        $workDayData->isWednesdayWorkDay = !empty($data['is_wednesday_workday']);
        $workDayData->isThursdayWorkDay = !empty($data['is_thursday_workday']);
        $workDayData->isFridayWorkDay = !empty($data['is_friday_workday']);
        $workDayData->isSaturdayWorkDay = !empty($data['is_saturday_workday']);
        $workDayData->isSundayWorkDay = !empty($data['is_sunday_workday']);

        return $workDayData;
    }

    /**
     * @return bool
     */
    public function isMondayWorkDay(): bool
    {
        return $this->isMondayWorkDay;
    }

    /**
     * @return bool
     */
    public function isTuesdayWorkDay(): bool
    {
        return $this->isTuesdayWorkDay;
    }

    /**
     * @return bool
     */
    public function isWednesdayWorkDay(): bool
    {
        return $this->isWednesdayWorkDay;
    }

    /**
     * @return bool
     */
    public function isThursdayWorkDay(): bool
    {
        return $this->isThursdayWorkDay;
    }

    /**
     * @return bool
     */
    public function isFridayWorkDay(): bool
    {
        return $this->isFridayWorkDay;
    }

    /**
     * @return bool
     */
    public function isSaturdayWorkDay(): bool
    {
        return $this->isSaturdayWorkDay;
    }

    /**
     * @return bool
     */
    public function isSundayWorkDay(): bool
    {
        return $this->isSundayWorkDay;
    }

}
