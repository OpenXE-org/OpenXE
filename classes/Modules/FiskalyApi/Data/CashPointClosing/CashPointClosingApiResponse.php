<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use stdClass;

class CashPointClosingApiResponse
{
    /** @var string $closingId */
    private $closingId;

    /** @var int $cashPointClosingExportId */
    private $cashPointClosingExportId;

    /** @var string $state */
    private $state;

    /** @var string $clientId */
    private $clientId;

    /** @var string $firstTransactionExportId */
    private $firstTransactionExportId;

    /** @var string $lastTransactionExportId */
    private $lastTransactionExportId;

    /** @var int $exportCreationDate */
    private $exportCreationDate;

    /** @var float $fullAmount */
    private $fullAmount;

    /** @var float $cashAmount */
    private $cashAmount;

    /** @var int $timeCreation */
    private $timeCreation;

    /** @var int $timeUpdate */
    private $timeUpdate;

    /** @var string $type */
    private $type;

    /** @var string $env */
    private $env;

    /** @var string $version */
    private $version;

    /**
     * CashPointClosingApiResponse constructor.
     *
     * @param null $apiResult
     */
    public function __construct($apiResult = null)
    {
        if (isset($apiResult->closing_id)) {
            $this->setClosingId($apiResult->closing_id);
        }
        if (isset($apiResult->cash_point_closing_export_id)) {
            $this->setCashPointClosingExportId($apiResult->cash_point_closing_export_id);
        }
        if (isset($apiResult->state)) {
            $this->setState($apiResult->state);
        }
        if (isset($apiResult->client_id)) {
            $this->setClientId($apiResult->client_id);
        }
        if (isset($apiResult->first_transaction_export_id)) {
            $this->setFirstTransactionExportId($apiResult->first_transaction_export_id);
        }
        if (isset($apiResult->last_transaction_export_id)) {
            $this->setLastTransactionExportId($apiResult->last_transaction_export_id);
        }
        if (isset($apiResult->export_creation_date)) {
            $this->setExportCreationDate((int)$apiResult->export_creation_date);
        }
        if (isset($apiResult->full_amount)) {
            $this->setFullAmount((float)$apiResult->full_amount);
        }
        if (isset($apiResult->cash_amount)) {
            $this->setCashAmount((float)$apiResult->cash_amount);
        }
        if (isset($apiResult->time_creation)) {
            $this->setTimeCreation((int)$apiResult->time_creation);
        }
        if (isset($apiResult->time_update)) {
            $this->setTimeUpdate((int)$apiResult->time_update);
        }
        if (isset($apiResult->_type)) {
            $this->setType($apiResult->_type);
        }
        if (isset($apiResult->_env)) {
            $this->setEnv($apiResult->_env);
        }
        if (isset($apiResult->_version)) {
            $this->setVersion($apiResult->_version);
        }
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        $apiResult = new stdClass();
        $apiResult->closing_id = $dbState['closing_id'] ?? null;
        $apiResult->cash_point_closing_export_id = $dbState['cash_point_closing_export_id'] ?? null;
        $apiResult->state = $dbState['state'] ?? null;
        $apiResult->client_id = $dbState['client_id'] ?? null;
        $apiResult->first_transaction_export_id = $dbState['first_transaction_export_id'] ?? null;
        $apiResult->last_transaction_export_id = $dbState['last_transaction_export_id'] ?? null;
        $apiResult->export_creation_date = $dbState['export_creation_date'] ?? null;
        $apiResult->full_amount = $dbState['full_amount'] ?? null;
        $apiResult->cash_amount = $dbState['cash_amount'] ?? null;
        $apiResult->time_creation = $dbState['time_creation'] ?? null;
        $apiResult->time_update = $dbState['time_update'] ?? null;
        $apiResult->_type = $dbState['_type'] ?? null;
        $apiResult->_env = $dbState['_env'] ?? null;
        $apiResult->_version = $dbState['_version'] ?? null;

        return new self($apiResult);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self($apiResult);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return json_decode(json_encode($this->toApiResult()), true);
    }

    public function toApiResult(): stdClass
    {
        $apiResult = new stdClass();
        $apiResult->closing_id = $this->getClosingId();
        $apiResult->cash_point_closing_export_id = $this->getCashPointClosingExportId();
        $apiResult->state = $this->getState();
        $apiResult->client_id = $this->getClientId();
        $apiResult->first_transaction_export_id = $this->getFirstTransactionExportId();
        $apiResult->last_transaction_export_id = $this->getLastTransactionExportId();
        $apiResult->export_creation_date = $this->getExportCreationDate();
        $apiResult->full_amount = $this->getFullAmount();
        $apiResult->cash_amount = $this->getCashAmount();
        $apiResult->time_creation = $this->getTimeCreation();
        $apiResult->time_update = $this->getTimeUpdate();
        $apiResult->_type = $this->getType();
        $apiResult->_env = $this->getEnv();
        $apiResult->_version = $this->getVersion();

        return $apiResult;
    }

    /**
     * @return string
     */
    public function getClosingId(): string
    {
        return $this->closingId;
    }

    /**
     * @param string $closingId
     */
    public function setClosingId(string $closingId): void
    {
        $this->closingId = $closingId;
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
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
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
     * @return int
     */
    public function getExportCreationDate(): int
    {
        return $this->exportCreationDate;
    }

    /**
     * @param int $exportCreationDate
     */
    public function setExportCreationDate(int $exportCreationDate): void
    {
        $this->exportCreationDate = $exportCreationDate;
    }

    /**
     * @return float
     */
    public function getFullAmount(): float
    {
        return $this->fullAmount;
    }

    /**
     * @param float $fullAmount
     */
    public function setFullAmount(float $fullAmount): void
    {
        $this->fullAmount = $fullAmount;
    }

    /**
     * @return float
     */
    public function getCashAmount(): float
    {
        return $this->cashAmount;
    }

    /**
     * @param float $cashAmount
     */
    public function setCashAmount(float $cashAmount): void
    {
        $this->cashAmount = $cashAmount;
    }

    /**
     * @return int
     */
    public function getTimeCreation(): int
    {
        return $this->timeCreation;
    }

    /**
     * @param int $timeCreation
     */
    public function setTimeCreation(int $timeCreation): void
    {
        $this->timeCreation = $timeCreation;
    }

    /**
     * @return int
     */
    public function getTimeUpdate(): int
    {
        return $this->timeUpdate;
    }

    /**
     * @param int $timeUpdate
     */
    public function setTimeUpdate(int $timeUpdate): void
    {
        $this->timeUpdate = $timeUpdate;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getEnv(): string
    {
        return $this->env;
    }

    /**
     * @param string $env
     */
    public function setEnv(string $env): void
    {
        $this->env = $env;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }
}
