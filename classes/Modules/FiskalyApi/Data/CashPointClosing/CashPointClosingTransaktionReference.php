<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use DateTime;
use DateTimeInterface;

class CashPointClosingTransaktionReference extends CashPointClosingTransactionLineReference
{
    /** @var int $cashPointClosingExportId */
    private $cashPointClosingExportId;

    /** @var string $cashRegisterExportId */
    private $cashRegisterExportId;

    /** @var string $transactionExportId */
    private $transactionExportId;

    /** @var DateTimeInterface $date */
    private $date;

    public function __construct(
        string $type, int $cashPointClosingExportId, string $cashRegisterExportId,
        string $transactionExportId, ?DateTimeInterface $date = null)
    {
        parent::__construct($type);
        $this->cashPointClosingExportId = $cashPointClosingExportId;
        $this->cashRegisterExportId = $cashRegisterExportId;
        $this->transactionExportId = $transactionExportId;
        $this->date = $date;
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): CashPointClosingTransaktionReference
    {
        return new self(
            $apiResult->type,
            (int)$apiResult->cash_point_closing_export_id,
            $apiResult->cash_register_export_id,
            $apiResult->transaction_export_id,
            $apiResult->date === null ? null : (new DateTime())->setTimestamp($apiResult->date)
        );
    }

    /**
     * @param array $dbState
     *
     * @return CashPointClosingTransaktionReference
     */
    public static function fromDbState(array $dbState): CashPointClosingTransaktionReference
    {
        return new self(
            $dbState['type'],
            (int)$dbState['cash_point_closing_export_id'],
            $dbState['cash_register_export_id'],
            $dbState['transaction_export_id'],
            empty($dbState['date']) ? null : (new DateTime())->setTimestamp($dbState['date'])
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [
            'type' => $this->type,
            'cash_point_closing_export_id' => $this->cashPointClosingExportId,
            'cash_register_export_id' => $this->cashRegisterExportId,
            'transaction_export_id' => $this->transactionExportId,
        ];
        if($this->date !== null) {
            $dbState['date'] = $this->date->getTimestamp();
        }

        return $dbState;
    }

    /**
     * @return int
     */
    public function getCashPointClosingExportId(): int
    {
        return $this->cashPointClosingExportId;
    }

    /**
     * @param int $cashPointClosingExportId
     */
    public function setCashPointClosingExportId(int $cashPointClosingExportId): void
    {
        $this->cashPointClosingExportId = $cashPointClosingExportId;
    }

    /**
     * @return string
     */
    public function getCashRegisterExportId(): string
    {
        return $this->cashRegisterExportId;
    }

    /**
     * @param string $cashRegisterExportId
     */
    public function setCashRegisterExportId(string $cashRegisterExportId): void
    {
        $this->cashRegisterExportId = $cashRegisterExportId;
    }

    /**
     * @return string
     */
    public function getTransactionExportId(): string
    {
        return $this->transactionExportId;
    }

    /**
     * @param string $transactionExportId
     */
    public function setTransactionExportId(string $transactionExportId): void
    {
        $this->transactionExportId = $transactionExportId;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param DateTimeInterface $date
     */
    public function setDate(DateTimeInterface $date): void
    {
        $this->date = $date;
    }


}
