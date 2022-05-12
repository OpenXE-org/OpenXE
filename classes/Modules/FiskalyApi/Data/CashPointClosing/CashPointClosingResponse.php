<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use stdClass;
use Xentral\Modules\FiskalyApi\Data\ErrorMessage;
use Xentral\Modules\FiskalyApi\Data\MetaData;

class CashPointClosingResponse extends CashPointClosing
{
    /** @var DateTimeInterface|null $timeCreation */
    private $timeCreation;

    /** @var DateTimeInterface|null $timeUpdate */
    private $timeUpdate;

    /** @var string|null $state */
    private $state;

    /** @var ErrorMessage|null $error */
    private $error;

    /** @var string|null $closingId */
    private $closingId;

    /**
     * CashPointClosingResponse constructor.
     *
     * @param string                                     $clientId
     * @param int                                        $cashPointClosingExportId
     * @param CashPointClosingHead|null                  $head
     * @param CashPointClosingCashStatement|null         $cashStatement
     * @param CashPointClosingTransactionCollection|null $transactions
     * @param MetaData|null                              $metaData
     * @param DateTimeInterface|null                     $timeCreation
     * @param DateTimeInterface|null                     $timeUpdate
     * @param string|null                                $state
     * @param ErrorMessage|null                          $error
     */
    public function __construct(
        string $clientId,
        int $cashPointClosingExportId,
        ?CashPointClosingHead $head,
        ?CashPointClosingCashStatement $cashStatement,
        ?CashPointClosingTransactionCollection $transactions,
        ?MetaData $metaData = null,
        ?DateTimeInterface $timeCreation = null,
        ?DateTimeInterface $timeUpdate = null,
        ?string $state = null,
        ?ErrorMessage $error = null
    ) {
        parent::__construct($clientId, $cashPointClosingExportId, $head, $cashStatement, $transactions, $metaData);
        $this->setTimeCreation($timeCreation);
        $this->setTimeUpdate($timeUpdate);
        $this->setState($state);
        $this->setError($error);
    }

    /**
     * @param $apiResult
     *
     * @throws Exception
     * @return CashPointClosingResponse
     */
    public static function fromApiResult(object $apiResult): CashPointClosingResponse
    {
        $instance = new self(
            $apiResult->client_id,
            (int)$apiResult->cash_point_closing_export_id,
            empty($apiResult->head) ? null : CashPointClosingHead::fromApiResult($apiResult->head),
            empty($apiResult->cash_statement) ? null : CashPointClosingCashStatement::fromApiResult($apiResult->cash_statement),
            empty($apiResult->transactions) ? null : CashPointClosingTransactionCollection::fromApiResult($apiResult->transactions)
        );
        if (!empty($apiResult->time_creation)) {
            $instance->setTimeCreation(
                (new DateTime('now', new DateTimeZone('UTC')))->setTimestamp($apiResult->time_creation)
            );
        }
        if (!empty($apiResult->time_update)) {
            $instance->setTimeUpdate(
                (new DateTime('now', new DateTimeZone('UTC')))->setTimestamp($apiResult->time_update)
            );
        }
        if (!empty($apiResult->state)) {
            $instance->setState($apiResult->state);
        }

        return $instance;
    }

    /**
     * @param array $dbState
     *
     * @throws Exception
     * @return CashPointClosingResponse
     */
    public static function fromDbState(array $dbState): CashPointClosingResponse
    {
        $instance = new self(
            $dbState['client_id'],
            (int)$dbState['cash_point_closing_export_id'],
            isset($dbState['head']) ? CashPointClosingHead::fromDbState($dbState['head']) : null,
            isset($dbState['cash_statement']) ? CashPointClosingCashStatement::fromDbState(
                $dbState['cash_statement']
            ) : null,
            isset($dbState['transactions']) ? CashPointClosingTransactionCollection::fromDbState(
                $dbState['transactions']
            ) : null
        );
        if (!empty($dbState['time_creation'])) {
            $instance->setTimeCreation(
                (new DateTime('now', new DateTimeZone('UTC')))->setTimestamp($dbState['time_creation'])
            );
        }
        if (!empty($dbState['time_update'])) {
            $instance->setTimeUpdate(
                (new DateTime('now', new DateTimeZone('UTC')))->setTimestamp($dbState['time_update'])
            );
        }
        if (!empty($dbState['state'])) {
            $instance->setState($dbState['state']);
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = parent::toArray();
        if ($this->timeCreation !== null) {
            $dbState['time_creation'] = $this->timeCreation->getTimestamp();
        }
        if ($this->timeUpdate !== null) {
            $dbState['time_update'] = $this->timeUpdate->getTimestamp();
        }
        if ($this->state !== null) {
            $dbState['state'] = $this->state;
        }
        if ($this->error !== null) {
            $dbState['error'] = $this->error->toArray();
        }

        return $dbState;
    }

    /**
     * @return stdClass
     */
    public function toApiResult(): stdClass
    {
        $apiResult = parent::toApiResult();
        if ($this->timeCreation !== null) {
            $apiResult->time_creation = $this->timeCreation->getTimestamp();
        }
        if ($this->timeUpdate !== null) {
            $apiResult->time_update = $this->timeUpdate->getTimestamp();
        }
        if ($this->state !== null) {
            $apiResult->state = $this->state;
        }

        return $apiResult;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getTimeCreation(): ?DateTimeInterface
    {
        return $this->timeCreation;
    }

    /**
     * @param DateTimeInterface|null $timeCreation
     */
    public function setTimeCreation(?DateTimeInterface $timeCreation): void
    {
        $this->timeCreation = $timeCreation;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getTimeUpdate(): ?DateTimeInterface
    {
        return $this->timeUpdate;
    }

    /**
     * @param DateTimeInterface|null $timeUpdate
     */
    public function setTimeUpdate(?DateTimeInterface $timeUpdate): void
    {
        $this->timeUpdate = $timeUpdate;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string|null $state
     */
    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return ErrorMessage|null
     */
    public function getError(): ?ErrorMessage
    {
        return $this->error === null ? null : ErrorMessage::fromDbState($this->error->toArray());
    }

    /**
     * @param ErrorMessage|null $error
     */
    public function setError(?ErrorMessage $error): void
    {
        $this->error = $error === null ? null : ErrorMessage::fromDbState($error->toArray());
    }
}
