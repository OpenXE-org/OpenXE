<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use DateTime;
use DateTimeInterface;

class CashPointClosingOtherReference extends CashPointClosingTransactionLineReference
{
    /** @var string $externalOtherExportId */
    private $externalOtherExportId;

    /** @var string $name */
    private $name;

    /** @var DateTimeInterface $date */
    private $date;

    /**
     * CashPointClosingOtherReference constructor.
     *
     * @param string                 $type
     * @param string                 $externalOtherExportId
     * @param string                 $name
     * @param DateTimeInterface|null $date
     */
    public function __construct(string $type, string $externalOtherExportId, string $name, ?DateTimeInterface $date = null)
    {
        parent::__construct($type);
        $this->externalOtherExportId = $externalOtherExportId;
        $this->name = $name;
        $this->date = $date;
    }


    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): CashPointClosingTransactionLineReference
    {
        return new self(
            $apiResult->type,
            $apiResult->external_other_export_id,
            $apiResult->name,
            $apiResult->date === null ? null : (new DateTime())->setTimestamp($apiResult->date)
        );
    }

    /**
     * @param array $dbState
     *
     * @return CashPointClosingTransactionLineReference
     */
    public static function fromDbState(array $dbState): CashPointClosingTransactionLineReference
    {
        return new self(
            $dbState['type'],
            $dbState['external_other_export_id'],
            $dbState['name'],
            $dbState['date'] === null ? null : (new DateTime())->setTimestamp($dbState['date'])
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = parent::toArray();
        $dbState['external_other_export_id'] = $this->externalOtherExportId;
        $dbState['name'] = $this->name;
        if($this->date !== null) {
            $dbState['date'] = $this->date->getTimestamp();
        }

        return $dbState;
    }

    /**
     * @return string
     */
    public function getExternalOtherExportId(): string
    {
        return $this->externalOtherExportId;
    }

    /**
     * @param string $externalOtherExportId
     */
    public function setExternalOtherExportId(string $externalOtherExportId): void
    {
        $this->externalOtherExportId = $externalOtherExportId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param DateTimeInterface $date
     */
    public function setDate(?DateTimeInterface $date): void
    {
        $this->date = $date;
    }
}
