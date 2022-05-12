<?php

declare(strict_types=1);

namespace Xentral\Modules\TimeManagement\Data;

use JsonSerializable;

final class DayInfoData implements JsonSerializable
{

    /** @var string $type */
    private $type = '';

    /** @var int $workMinutes */
    private $workMinutes = 0;

    /** @var int $vacationMinutes */
    private $vacationMinutes = 0;

    /** @var string $internalComment */
    private $internalComment = '';

    private function __construct()
    {
    }

    /**
     * @param $data
     *
     * @return DayInfoData
     */
    public static function fromDbState($data): DayInfoData
    {
        $dayInfoData = new DayInfoData();

        if (isset($data['type'])) {
            $dayInfoData->type = (string)$data['type'];
        }
        if (isset($data['workminutes'])) {
            $dayInfoData->workMinutes = (int)$data['workminutes'];
        }
        if (isset($data['vacationminutes'])) {
            $dayInfoData->vacationMinutes = (int)$data['vacationminutes'];
        }
        if (isset($data['internal_comment'])) {
            $dayInfoData->internalComment = (string)$data['internal_comment'];
        }

        return $dayInfoData;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getWorkMinutes(): int
    {
        return $this->workMinutes;
    }

    /**
     * @return int
     */
    public function getVacationMinutes(): int
    {
        return $this->vacationMinutes;
    }

    /**
     * @return string
     */
    public function getInternalComment(): string
    {
        return $this->internalComment;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'type'             => $this->type,
            'workminutes'      => $this->workMinutes,
            'vacationminutes'  => $this->vacationMinutes,
            'internal_comment' => $this->internalComment,
        ];
    }
}
