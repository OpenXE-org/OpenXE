<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use DateTime;
use DateTimeInterface;
use DateTimeZone;

class CashPointClosingHead
{
    /** @var DateTimeInterface $exportCreationDate */
    private $exportCreationDate;

    /** @var string $firstTransactionExportId */
    private $firstTransactionExportId;

    /** @var string $lastTransactionExportId */
    private $lastTransactionExportId;

    /** @var DateTimeInterface|null $businessDate */
    private $businessDate;

    /**
     * CashPointClosingHead constructor.
     *
     * @param DateTimeInterface      $exportCreationDate
     * @param string                 $firstTransactionExportId
     * @param string                 $lastTransactionExportId
     * @param DateTimeInterface|null $businessDate
     */
    public function __construct(
        DateTimeInterface $exportCreationDate,
        string $firstTransactionExportId,
        string $lastTransactionExportId,
        ?DateTimeInterface $businessDate = null
    ) {
        $this->setExportCreationDate($exportCreationDate);
        $this->setFirstTransactionExportId($firstTransactionExportId);
        $this->setLastTransactionExportId($lastTransactionExportId);
        $this->setBusinessDate($businessDate);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            (new DateTime())->setTimestamp($apiResult->export_creation_date),
            $apiResult->first_transaction_export_id,
            $apiResult->last_transaction_export_id,
            empty($apiResult->business_date) ? null : new DateTime($apiResult->business_date, new DateTimeZone('UTC'))
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
            (new DateTime())->setTimestamp($dbState['export_creation_date']),
            $dbState['first_transaction_export_id'],
            $dbState['last_transaction_export_id'],
            empty($dbState['business_date']) ? null : new DateTime($dbState['business_date'], new DateTimeZone('UTC'))
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [
            'export_creation_date'        => $this->getExportCreationDate()->getTimestamp(),
            'first_transaction_export_id' => $this->getFirstTransactionExportId(),
            'last_transaction_export_id'  => $this->getLastTransactionExportId(),
        ];
        if ($this->businessDate !== null) {
            $dbState['business_date'] = $this->businessDate->format('Y-m-d');
        }

        return $dbState;
    }

    /**
     * @return DateTimeInterface
     */
    public function getExportCreationDate(): DateTimeInterface
    {
        return $this->exportCreationDate;
    }

    /**
     * @param DateTimeInterface $exportCreationDate
     */
    public function setExportCreationDate(DateTimeInterface $exportCreationDate): void
    {
        $this->exportCreationDate = $exportCreationDate;
    }

    /**
     * @return string
     */
    public function getFirstTransactionExportId(): string
    {
        return $this->firstTransactionExportId;
    }

    /**
     * @param string $firstTransactionExportId
     */
    public function setFirstTransactionExportId(string $firstTransactionExportId): void
    {
        $this->firstTransactionExportId = $firstTransactionExportId;
    }

    /**
     * @return string
     */
    public function getLastTransactionExportId(): string
    {
        return $this->lastTransactionExportId;
    }

    /**
     * @param string $lastTransactionExportId
     */
    public function setLastTransactionExportId(string $lastTransactionExportId): void
    {
        $this->lastTransactionExportId = $lastTransactionExportId;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getBusinessDate(): ?DateTimeInterface
    {
        return $this->businessDate;
    }

    /**
     * @param DateTimeInterface|null $businessDate
     */
    public function setBusinessDate(?DateTimeInterface $businessDate): void
    {
        $this->businessDate = $businessDate;
    }
}
