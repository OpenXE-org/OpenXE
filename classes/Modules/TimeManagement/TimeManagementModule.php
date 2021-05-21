<?php

declare(strict_types=1);

namespace Xentral\Modules\TimeManagement;

use DateTimeImmutable;
use DateTimeInterface;
use Xentral\Modules\TimeManagement\Data\CalendarData;
use Xentral\Modules\TimeManagement\Data\DayInfoData;
use Xentral\Modules\TimeManagement\Data\HolidayData;
use Xentral\Modules\TimeManagement\Data\RequestInfoData;
use Xentral\Modules\TimeManagement\Data\WorkDayData;
use Xentral\Modules\TimeManagement\Exception\InvalidDateFormatException;
use Xentral\Modules\TimeManagement\Exception\InvalidQueryException;
use Xentral\Modules\TimeManagement\Exception\InvalidRequestTokenException;
use Xentral\Modules\TimeManagement\Exception\SupervisorNotFoundException;
use Xentral\Modules\TimeManagement\Service\GroupGateway;
use Xentral\Modules\TimeManagement\Service\HolidayGateway;
use Xentral\Modules\TimeManagement\Service\TimeManagementHistoryService;
use Xentral\Modules\TimeManagement\Service\TimeManagementSettingGateway;
use Xentral\Modules\TimeManagement\Service\TimeManagementTargetHourGateway;
use Xentral\Modules\TimeManagement\Service\TimeManagementTargetHourService;
use Xentral\Modules\TimeManagement\Wrapper\TimeManagementTargetHourWrapper;


class TimeManagementModule
{
    /** @var TimeManagementTargetHourGateway $targetHourGateway */
    private $targetHourGateway;

    /** @var TimeManagementTargetHourService $targetHourService */
    private $targetHourService;

    /** @var  HolidayGateway $holidayGateway */
    private $holidayGateway;

    /** @var  GroupGateway $groupGateway */
    private $groupGateway;

    /** @var  TimeManagementSettingGateway $settingGateway */
    private $settingGateway;

    /** @var TimeManagementTargetHourWrapper $targetHourWrapper */
    private $targetHourWrapper;

    /** @var TimeManagementHistoryService $historyService */
    private $historyService;

    /** @var string UNPAID */
    public const UNPAID = 'N';

    /** @var string ABSENT_DAY */
    public const ABSENT_DAY = 'X';

    /** @var string NONE */
    public const NONE = '';

    /** @var string SICK */
    public const SICK = 'K';

    /** @var string SICK */
    public const SICKREQUEST = 'S';

    /** @var string SICKREMOVE */
    public const SICKREMOVE = 'V';

    /** @var string SICKREJECT */
    public const SICKREJECT = 'C';

    /** @var string VACATION */
    public const VACATION = 'U';

    /** @var string VACATIONREQUEST */
    public const VACATIONREQUEST = 'R';

    /** @var string VACATIONREMOVE */
    public const VACATIONREMOVE = 'L';

    /** @var string VACATIONREJECT */
    public const VACATIONREJECT = 'J';

    /**
     * @param TimeManagementTargetHourGateway $targetHourGateway
     * @param TimeManagementTargetHourService $targetHourService
     * @param TimeManagementSettingGateway    $settingGateway
     * @param HolidayGateway                  $holidayGateway
     * @param GroupGateway                    $groupGateway
     * @param TimeManagementTargetHourWrapper $targetHourWrapper
     * @param TimeManagementHistoryService    $historyService
     */
    public function __construct(
        TimeManagementTargetHourGateway $targetHourGateway,
        TimeManagementTargetHourService $targetHourService,
        TimeManagementSettingGateway $settingGateway,
        HolidayGateway $holidayGateway,
        GroupGateway $groupGateway,
        TimeManagementTargetHourWrapper $targetHourWrapper,
        TimeManagementHistoryService $historyService
    ) {
        $this->targetHourGateway = $targetHourGateway;
        $this->targetHourService = $targetHourService;
        $this->holidayGateway = $holidayGateway;
        $this->groupGateway = $groupGateway;
        $this->settingGateway = $settingGateway;
        $this->targetHourWrapper = $targetHourWrapper;
        $this->historyService = $historyService;
    }

    /**
     * @param int               $addressId
     * @param DateTimeImmutable $fromDate
     * @param DateTimeImmutable $tillDate
     * @param string            $statusOldType
     * @param string            $statusWishType
     *
     * @return array
     */
    private function findPossibleDays(
        int $addressId,
        DateTimeImmutable $fromDate,
        DateTimeImmutable $tillDate,
        string $statusOldType,
        string $statusWishType
    ): array {
        $evaluatedDays = [];

        while ($fromDate <= $tillDate) {
            $dayInfo = $this->targetHourGateway->findDayInfo($addressId, $fromDate);
            $dayType = $dayInfo->getType();

            $workminutes = (int)$dayInfo->getWorkMinutes();
            if ($workminutes < 0) {
                $workminutes = 0;
            }

            $isWorkDay = $workminutes != 0;

            //allowed are:
            //- days with no type
            //- days of the same type like the old
            //- rejected days which can be reclaimed
            if (
                empty($dayType) ||
                $dayType === $statusOldType ||
                $dayType === self::SICKREJECT ||
                $dayType === self::VACATIONREJECT
            ) {
                if ($this->isAddableDay($fromDate, $isWorkDay)) {
                    $evaluatedDays[] = [
                        'date'            => $fromDate,
                        'type'            => $this->getPossibleDayType($statusOldType, $statusWishType),
                        'workminutes'     => $workminutes,
                        'vacationminutes' => $dayInfo->getVacationMinutes(),
                    ];
                }
            }

            $fromDate = $fromDate->modify('1 day');
        }

        return $evaluatedDays;
    }

    /**
     * @param DateTimeInterface $date
     * @param bool              $isWorkDay
     *
     * @return bool
     */
    private function isAddableDay(DateTimeInterface $date, bool $isWorkDay): bool
    {
        $year = (int)$date->format('Y');
        $holidays = $this->holidayGateway->findHolidayDataByYear($year);
        $isHoliday = $this->isHoliday($date, $holidays);

        if (!$isHoliday && $isWorkDay) {
            return true;
        }

        return false;
    }

    /**
     * @param string $statusOldType
     * @param string $statusWishType
     *
     * @return string
     */
    private function getPossibleDayType(string $statusOldType, string $statusWishType): string
    {
        $isStatusAcceptedRemove = false;
        $isStatusRequestedRemove = false;

        if (
            strstr($statusOldType, TimeManagementModule::VACATIONREQUEST) !== false ||
            strstr($statusOldType, TimeManagementModule::SICKREQUEST) !== false
        ) {
            $isStatusRequestedRemove = true;
        }

        if (
            strstr($statusOldType, TimeManagementModule::VACATION) !== false ||
            strstr($statusOldType, TimeManagementModule::SICK) !== false ||
            strstr($statusOldType, TimeManagementModule::ABSENT_DAY) !== false ||
            strstr($statusOldType, TimeManagementModule::UNPAID) !== false
        ) {
            $isStatusAcceptedRemove = true;
        }

        $type = self::VACATIONREQUEST;
        if ($statusWishType === self::SICK) {
            $type = self::SICKREQUEST;
        }

        if ($isStatusRequestedRemove) {
            $type = self::NONE;
        } elseif ($isStatusAcceptedRemove) {
            $type = self::VACATIONREMOVE;
            if ($statusOldType === self::SICK) {
                $type = self::SICKREMOVE;
            }
        }

        return $type;
    }

    /**
     * @param DateTimeInterface $date
     * @param array             $holidays
     *
     * @return bool
     */
    private function isHoliday(DateTimeInterface $date, array $holidays): bool
    {
        /** @var HolidayData $holiday */
        foreach ($holidays as $holiday) {
            if ($holiday->getDate()->getTimestamp() === $date->getTimestamp()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int  $addressId
     * @param bool $hasSuperPrivileges
     *
     * @return array
     */
    public function findGroupsByAddressId(int $addressId, bool $hasSuperPrivileges): array
    {
        if ($hasSuperPrivileges) {
            return $this->groupGateway->findAllActiveGroupsWithMembers();
        }

        return $this->groupGateway->findGroupsByAddressId($addressId);
    }

    /**
     * @param string $requestToken
     *
     * @throws InvalidRequestTokenException
     * @throws InvalidDateFormatException
     *
     * @return RequestInfoData
     */
    public function getRequestInfoByToken(string $requestToken): RequestInfoData
    {
        return $this->targetHourGateway->getRequestInfoByToken($requestToken);
    }

    /**
     * @param $year
     *
     * @return array|HolidayData[]
     */
    public function findHolidayDataByYear($year): array
    {
        return $this->holidayGateway->findHolidayDataByYear($year);
    }

    /**
     * @param int  $addressId
     * @param int  $year
     * @param bool $isAnonymised
     * @param int  $groupId
     *
     * @throws InvalidDateFormatException
     *
     * @return array|CalendarData[]
     */
    public function getCalendarData(
        int $addressId,
        int $year,
        bool $isAnonymised,
        int $groupId = 0
    ): array {
        if ($isAnonymised) {
            if (empty($groupId)) {
                $calendarData = $this->targetHourGateway->findAnonymisedVacationCalendarDataByYearAndAddressId(
                    $year,
                    $addressId
                );
            } else {
                $calendarData = $this->targetHourGateway->findAnonymisedVacationCalendarDataByYearAndAddressIdAndGroupId(
                    $year,
                    $addressId,
                    $groupId
                );
            }
        } else {
            $calendarData = $this->targetHourGateway->findAllVacationCalendarDataByYear($year);
        }

        return $calendarData;
    }

    /**
     * @param int $addressId
     *
     * @throws InvalidQueryException
     *
     * @return WorkDayData
     */
    public function getWorkingDaysForAddress(int $addressId): WorkDayData
    {
        return $this->settingGateway->getWorkingDaysForAddress($addressId);
    }

    /**
     * @param int               $addressId
     * @param DateTimeInterface $date
     *
     * @return DayInfoData
     */
    public function getDayInfo(int $addressId, DateTimeInterface $date): DayInfoData
    {
        return $this->targetHourGateway->findDayInfo($addressId, $date);
    }

    /**
     * @param int $addressId
     *
     * @return float
     */
    public function getAmountRequestedVacation(int $addressId): float
    {
        return $this->targetHourGateway->findAmountRequestedVacation($addressId);
    }

    /**
     * @param string $dayType
     * @param bool   $isReject
     *
     * @return string
     */
    private function evaluateNextDayType(string $dayType, bool $isReject): string
    {
        $isVacation =
            strstr($dayType, self::VACATIONREMOVE) !== false ||
            strstr($dayType, self::VACATIONREQUEST) !== false;

        $isRemove =
            strstr($dayType, self::VACATIONREMOVE) !== false ||
            strstr($dayType, self::SICKREMOVE) !== false;

        if ($isRemove) {
            if ($isReject) {
                if ($isVacation) {
                    $type = self::VACATION;
                } else {
                    $type = self::SICK;
                }
            } else {
                $type = self::NONE;
            }
        } else {
            if ($isReject) {
                if ($isVacation) {
                    $type = self::VACATIONREJECT;
                } else {
                    $type = self::SICKREJECT;
                }
            } else {
                if ($isVacation) {
                    $type = self::VACATION;
                } else {
                    $type = self::SICK;
                }
            }
        }

        return $type;
    }

    /**
     * @param int               $addressId
     * @param DateTimeImmutable $from
     * @param DateTimeImmutable $till
     * @param bool              $halfDay
     * @param string            $comment
     * @param string            $statusOldType
     * @param string            $statusWishType
     *
     * @return string
     */
    public function changeDays(
        int $addressId,
        DateTimeImmutable $from,
        DateTimeImmutable $till,
        bool $halfDay,
        string $comment,
        string $statusOldType,
        string $statusWishType
    ): string {
        if ($from > $till) {
            $till = $from;
        }

        $possibleDays =
            $this->findPossibleDays(
                $addressId,
                $from,
                $till,
                $statusOldType,
                $statusWishType
            );

        if (empty($possibleDays)) {
            return '';
        }
        $date = new DateTimeImmutable();
        $requestToken = (string)$date->getTimestamp();

        $this->historyService->saveActivity(
            $addressId,
            0,
            $statusOldType,
            $possibleDays[0]['type'],
            $requestToken,
            $from,
            $till,
            $comment
        );

        foreach ($possibleDays as $day) {
            $time = $day['vacationminutes'] / 60;

            if ($halfDay) {
                $workminutes = $day['workminutes'];
                if ($workminutes > 0) {
                    $time = $workminutes / (2 * 60);
                }
            }

            $this->changeDayType(
                $addressId,
                $day['date'],
                $day['type'],
                $comment,
                (string)$time,
                $requestToken
            );
        }
        if($day['type'] === self::NONE){
            return '';
        }
        else{
            return $requestToken;
        }
    }

    /**
     * @param int               $addressId
     * @param DateTimeInterface $date
     * @param string            $type
     * @param string            $comment
     * @param string            $time
     * @param string            $requestToken
     */
    public function changeDayType(
        int $addressId,
        DateTimeInterface $date,
        string $type,
        string $comment,
        string $time,
        string $requestToken = ''
    ): void {
        $this->clearDayType($addressId, $date, $type);

        if ($type !== self::NONE) {
            $this->targetHourWrapper->handleType($addressId, $date, $type, true, $time, $requestToken);
        } else {
            if (!empty($requestToken)) {
                $this->targetHourService->updateVacationRequestToken($addressId, $date, $requestToken);
            }
        }

        if (!empty($comment)) {
            $this->targetHourWrapper->saveComment($addressId, $date, $comment);
        }

        $this->targetHourWrapper->recalculate($addressId, $date);
    }

    /**
     * @param int               $addressId
     * @param DateTimeInterface $date
     * @param string            $type
     */
    private function clearDayType(int $addressId, DateTimeInterface $date, string $type): void
    {
        $types = [
            TimeManagementModule::UNPAID,
            TimeManagementModule::ABSENT_DAY,

            TimeManagementModule::VACATION,
            TimeManagementModule::VACATIONREQUEST,
            TimeManagementModule::VACATIONREJECT,
            TimeManagementModule::VACATIONREMOVE,

            TimeManagementModule::SICK,
            TimeManagementModule::SICKREQUEST,
            TimeManagementModule::SICKREMOVE,
            TimeManagementModule::SICKREJECT,
        ];

        if ($type != TimeManagementModule::NONE) {
            unset($types[$type]);
        }

        $this->targetHourWrapper->handleType($addressId, $date, implode('', $types), false);
    }

    /**
     * @param int    $employeeAddressId
     * @param int    $supervisorAddressId
     * @param bool   $isReject
     * @param string $internalComment
     * @param string $requestToken
     *
     * @throws InvalidDateFormatException
     * @throws InvalidRequestTokenException
     */
    public function handleRequestedDays(
        int $employeeAddressId,
        int $supervisorAddressId,
        bool $isReject,
        string $internalComment,
        string $requestToken
    ): void {
        $requestedDays = $this->targetHourGateway->getRequestedDaysByToken($requestToken);

        if (!empty($requestedDays)) {
            $from = $requestedDays[0]['date'];
            $till = $requestedDays[count($requestedDays) - 1]['date'];

            $oldType = $requestedDays[0]['type'];
            $newType = $this->evaluateNextDayType($oldType, $isReject);

            $this->historyService->saveActivity(
                0,
                $supervisorAddressId,
                $oldType,
                $newType,
                $requestToken,
                $from,
                $till,
                $internalComment
            );

            $date = new DateTimeImmutable();
            $requestToken = (string)$date->getTimestamp();

            foreach ($requestedDays as $requestedDay) {
                $this->targetHourService->updateTargetHourType(
                    $employeeAddressId,
                    $requestedDay['date'],
                    $requestedDay['type'],
                    $newType
                );
                $this->targetHourService->updateVacationRequestToken(
                    $employeeAddressId,
                    $requestedDay['date'],
                    $requestToken
                );
                $this->targetHourWrapper->recalculate($employeeAddressId,$requestedDay['date']);
            }
        }

        if (!empty($internalComment)) {
            $this->targetHourService->saveInternalComment($requestToken, $internalComment);
        }
    }

    /**
     * @param int $daysTillDeletion
     * @param int $addressId
     *
     * @throws InvalidDateFormatException
     */
    public function removeRejectedAfterXDays(int $daysTillDeletion, int $addressId): void
    {
        $rejectedDays = $this->targetHourGateway->findRejectedDays($daysTillDeletion, $addressId);

        if (!empty($rejectedDays)) {
            foreach ($rejectedDays as $day) {
                $this->clearDayType($addressId, $day['date'], TimeManagementModule::NONE);
            }
        }
    }

    /**
     * @param int $addressId
     * @param int $groupId
     *
     * @return bool
     */
    public function checkAddressHasGroup(int $addressId, int $groupId): bool
    {
        return $this->groupGateway->isAddressInGroup($addressId, $groupId);
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function mapTypeToLanguage(string $type): string
    {
        $mapping =
            [
                self::NONE            => 'Kein Status',
                self::VACATION        => 'Urlaub',
                self::VACATIONREQUEST => 'Urlaubsantrag',
                self::VACATIONREJECT  => 'Urlaub ablehnen',
                self::VACATIONREMOVE  => 'Urlaub entfernen',
                self::SICK            => 'Krank',
                self::SICKREQUEST     => 'Krankheitsantrag',
                self::SICKREMOVE      => 'Krankheit entfernen',
                self::SICKREJECT      => 'Krankheit ablehnen',
                self::UNPAID          => 'Unbezahlter Urlaub',
                self::ABSENT_DAY      => 'Fehltag',
            ];

        return $mapping[$type];
    }

    /**
     * @param int $employeeAddressId
     * @param int $groupId
     *
     * @throws SupervisorNotFoundException
     *
     * @return int[]
     */
    public function getSupervisorAddressIds(int $employeeAddressId, int $groupId): array
    {
        return $this->groupGateway->getSupervisorAddressIds($employeeAddressId, $groupId);
    }
}
