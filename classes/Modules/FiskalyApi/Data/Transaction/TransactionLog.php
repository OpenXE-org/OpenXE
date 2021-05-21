<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

use DateTimeInterface;
use DateTimeZone;
use DateTime;
use stdClass;

class TransactionLog
{
    /** @var string $operation */
    private $operation;

    /** @var DateTimeInterface $timestamp */
    private $timestamp;

    /** @var string $timestampFormat */
    private $timestampFormat;

    /**
     * TransactionLog constructor.
     *
     * @param string            $operation
     * @param DateTimeInterface $timestamp
     * @param string            $timestampFormat
     */
    public function __construct(string $operation, DateTimeInterface $timestamp, string $timestampFormat = 'utcTime')
    {
        $this->setOperation($operation);
        $this->setTimestamp($timestamp);
        $this->setTimestampFormat($timestampFormat);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            $apiResult->operation,
            (new DateTime('now', new DateTimeZone('UTC')))->setTimestamp($apiResult->timestamp),
            $apiResult->timestamp_format
        );
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        return new self(
            $dbState['operation'],
            (new DateTime('now', new DateTimeZone('UTC')))->setTimestamp($dbState['timestamp']),
            $dbState['timestamp_format']
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'operation'        => $this->getOperation(),
            'timestamp'        => $this->getTimestamp()->getTimestamp(),
            'timestamp_format' => $this->getTimestampFormat(),
        ];
    }

    /**
     * @return stdClass
     */
    public function toApiResult(): stdClass
    {
        $apiResult = new stdClass();
        $apiResult->operation = $this->getOperation();
        $apiResult->timestamp = $this->getTimestamp()->getTimestamp();
        $apiResult->timestamp_format = $this->getTimestampFormat();

        return $apiResult;
    }

        /**
     * @return string
     */
    public function getOperation(): string
    {
        return $this->operation;
    }

    /**
     * @param string $operation
     */
    public function setOperation(string $operation): void
    {
        $this->operation = $operation;
    }

    /**
     * @return DateTimeInterface
     */
    public function getTimestamp(): DateTimeInterface
    {
        return $this->timestamp;
    }

    /**
     * @param DateTimeInterface $timestamp
     */
    public function setTimestamp(DateTimeInterface $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getTimestampFormat(): string
    {
        return $this->timestampFormat;
    }

    /**
     * @param string $timestampFormat
     */
    public function setTimestampFormat(string $timestampFormat): void
    {
        $this->timestampFormat = $timestampFormat;
    }
}
