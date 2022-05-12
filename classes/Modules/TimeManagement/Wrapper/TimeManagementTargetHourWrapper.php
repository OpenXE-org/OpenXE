<?php

namespace Xentral\Modules\TimeManagement\Wrapper;

use DateTimeInterface;
use Mitarbeiterzeiterfassung;

final class TimeManagementTargetHourWrapper
{

    /** @var  Mitarbeiterzeiterfassung $timeRecordingModule */
    private $timeRecordingModule;

    public function __construct(Mitarbeiterzeiterfassung $timeRecordingModule)
    {
        $this->timeRecordingModule = $timeRecordingModule;
    }

    /**
     * @param int               $address_id
     * @param DateTimeInterface $date
     *
     * @return bool
     */
    public function recalculate(int $address_id, DateTimeInterface $date): bool
    {
        return $this->timeRecordingModule->MitarbeitererfassungIstNeuberechnen($address_id, $date->format('Y-m-d'));
    }

    /**
     * @param int               $addressId
     * @param DateTimeInterface $date
     * @param string            $type
     * @param bool              $add
     * @param string            $time
     * @param string            $requestToken
     */
    public function handleType(
        int $addressId,
        DateTimeInterface $date,
        string $type,
        bool $add = true,
        string $time = '0',
        string $requestToken = ''
    ): void {
        $this->timeRecordingModule->MitarbeiterzeiterfassungInsertUpdateKuerzel(
            $addressId,
            $date->format('Y-m-d'),
            $type,
            $add,
            $time,
            $requestToken
        );
    }

    /**
     * @param int               $addressId
     * @param DateTimeInterface $date
     * @param string            $comment
     */
    public function saveComment(int $addressId, DateTimeInterface $date, string $comment): void
    {
        $this->timeRecordingModule->MitarbeiterzeiterfassungInsertUpdateKommentar(
            $addressId,
            $date->format('Y-m-d'),
            $comment
        );
    }
}
