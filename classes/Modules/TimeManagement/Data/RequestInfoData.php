<?php

declare(strict_types=1);

namespace Xentral\Modules\TimeManagement\Data;

use DateTimeInterface;
use DateTimeImmutable;
use Exception;
use JsonSerializable;
use Xentral\Modules\TimeManagement\Exception\InvalidDateFormatException;

final class RequestInfoData implements JsonSerializable
{
    /** @var int $employeeId */
    private $employeeId = 0;

    /** @var string $employeeNumber */
    private $employeeNumber = '';

    /** @var string $employeeName */
    private $employeeName = '';

    /** @var DateTimeInterface $minDate */
    private $minDate;

    /** @var DateTimeInterface $maxDate */
    private $maxDate;

    /** @var int $amount */
    private $amount = 0;

    /** @var string $comment */
    private $comment = '';

    /** @var string $type */
    private $type = '';

    /** @var string $internalComment */
    private $internalComment = '';

    private function __construct()
    {
    }

    /**
     * @return int
     */
    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    /**
     * @return string
     */
    public function getEmployeeNumber(): string
    {
        return $this->employeeNumber;
    }

    /**
     * @return string
     */
    public function getEmployeeName(): string
    {
        return $this->employeeName;
    }

    /**
     * @return DateTimeInterface
     */
    public function getMinDate(): DateTimeInterface
    {
        return $this->minDate;
    }

    /**
     * @return DateTimeInterface
     */
    public function getMaxDate(): DateTimeInterface
    {
        return $this->maxDate;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getInternalComment(): string
    {
        return $this->internalComment;
    }

    /**
     * @param array $data
     *
     * @throws InvalidDateFormatException
     *
     * @return RequestInfoData
     */
    public static function fromDbState(array $data): RequestInfoData
    {
        $requestInfoData = new RequestInfoData();

        $requestInfoData->employeeId = (int)$data['employee_id'];
        $requestInfoData->employeeNumber = (string)$data['employee_number'];
        $requestInfoData->employeeName = (string)$data['employee_name'];

        try {
            $requestInfoData->minDate = new DateTimeImmutable($data['min_date']);
        } catch (Exception $e) {
            throw new InvalidDateFormatException('Could not convert date: ' . $data['date']);
        }

        try {
            $requestInfoData->maxDate = new DateTimeImmutable($data['max_date']);
        } catch (Exception $e) {
            throw new InvalidDateFormatException('Could not convert date: ' . $data['date']);
        }

        $requestInfoData->amount = (int)$data['amount'];
        $requestInfoData->comment = (string)$data['comment'];
        $requestInfoData->type = (string)$data['type'];
        $requestInfoData->internalComment = (string)$data['internal_comment'];

        return $requestInfoData;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'employee_id'      => $this->employeeId,
            'employee_number'  => $this->employeeNumber,
            'employee_name'    => $this->employeeName,
            'min_date'         => empty($this->minDate) ? '0000-00-00' : $this->minDate->format('Y-m-d'),
            'max_date'         => empty($this->maxDate) ? '0000-00-00' : $this->maxDate->format('Y-m-d'),
            'amount'           => $this->amount,
            'comment'          => $this->comment,
            'type'             => $this->type,
            'internal_comment' => $this->internalComment,
        ];
    }
}
