<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use DateTime;
use DateTimeInterface;

class CashPointClosingExternalDocumentReference extends CashPointClosingTransactionLineReference
{
    /** @var string $externalExportId */
    private $externalExportId;

    /** @var DateTimeInterface $date */
    private $date;

    /**
     * CashPointClosingExternalDocumentReference constructor.
     *
     * @param string                 $type
     * @param string                 $externalExportId
     * @param DateTimeInterface|null $date
     */
    public function __construct(string $type, string $externalExportId, ?DateTimeInterface $date = null)
    {
        parent::__construct($type);
        $this->externalExportId = $externalExportId;
        $this->date = $date;
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): CashPointClosingExternalDocumentReference
    {
        return new self(
            $apiResult->type,
            $apiResult->external_export_id,
            $apiResult->date === null ? null : (new DateTime())->setTimestamp($apiResult->date)
        );
    }

    /**
     * @param array $dbState
     *
     * @return CashPointClosingExternalDocumentReference
     */
    public static function fromDbState(array $dbState): CashPointClosingExternalDocumentReference
    {
        return new self(
            $dbState['type'],
            $dbState['external_export_id'],
            $dbState['date'] === null ? null : (new DateTime())->setTimestamp($dbState['date'])
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = parent::toArray();
        $dbState['external_export_id'] = $this->externalExportId;
        if($this->date !== null) {
            $dbState['date'] = $this->date->getTimestamp();
        }

        return $dbState;
    }

    /**
     * @return string
     */
    public function getExternalExportId(): string
    {
        return $this->externalExportId;
    }

    /**
     * @param string $externalExportId
     */
    public function setExternalExportId(string $externalExportId): void
    {
        $this->externalExportId = $externalExportId;
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
