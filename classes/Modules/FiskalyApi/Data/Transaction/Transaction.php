<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

use stdClass;
use Xentral\Modules\FiskalyApi\Data\MetaData;
use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

class Transaction
{
    /** @var string $state */
    protected $state;

    /** @var string $clientId */
    protected $clientId;

    /** @var TransactionSchema|null $schema */
    protected $schema;

    /** @var MetaData|null $metaData */
    protected $metaData;

    /**
     * Transaction constructor.
     *
     * @param string                 $state
     * @param string                 $clientId
     * @param TransactionSchema|null $schema
     * @param MetaData|null          $metaData
     */
    public function __construct(string $state, string $clientId, ?TransactionSchema $schema = null, ?MetaData $metaData = null)
    {
        $this->setState($state);
        $this->setClientId($clientId);
        $this->setSchema($schema);
        $this->setMetaData($metaData);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult)
    {
        return new self(
            $apiResult->state,
            $apiResult->client_id,
            empty($apiResult->schema) ? null : TransactionSchema::fromApiResult($apiResult->schema),
            empty($apiResult->metadata) ? null : MetaData::fromApiResult($apiResult->metadata)
        );
    }

    /**
     * @param array $dbState
     *
     * @return Transaction
     */
    public static function fromDbState(array $dbState)
    {
        return new self(
            $dbState['state'],
            $dbState['client_id'],
            empty($dbState['schema']) ? null : TransactionSchema::fromDbState($dbState['schema']),
            empty($dbState['metadata']) ? null : MetaData::fromDbState($dbState['metadata'])
        );
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        $dbState = ['state' => $this->getState(), 'client_id' => $this->getClientId()];
        if($this->schema !== null) {
            $dbState['schema'] = $this->schema->toArray();
        }
        if($this->metaData !== null) {
            $dbState['metadata'] = $this->metaData->toArray();
        }

        return $dbState;
    }

    /**
     * @return stdClass
     */
    public function toApiResult()
    {
        $apiResult = new stdClass();
        $apiResult->state = $this->getState();
        $apiResult->client_id = $this->getClientId();
        if($this->schema !== null) {
            $apiResult->schema = $this->schema->toApiResult();
        }
        if($this->metaData !== null) {
            $apiResult->metadata = $this->metaData->toApiResult();
        }

        return $apiResult;
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
        $this->ensureState($state);
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
    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return TransactionSchema|null
     */
    public function getSchema(): ?TransactionSchema
    {
        return $this->schema;
    }

    /**
     * @param TransactionSchema|null $schema
     */
    public function setSchema(?TransactionSchema $schema): void
    {
        $this->schema = $schema === null ? null : TransactionSchema::fromDbState($schema->toArray());
    }

    /**
     * @return MetaData|null
     */
    public function getMetaData(): ?MetaData
    {
        return $this->metaData === null ? null : MetaData::fromDbState($this->metaData->toArray());
    }

    /**
     * @param MetaData|null $metaData
     */
    public function setMetaData(?MetaData $metaData): void
    {
        $this->metaData = $metaData === null ? null : MetaData::fromDbState($metaData->toArray());
    }

    /**
     * @param string $state
     */
    private function ensureState(string $state):void
    {
        if(!in_array($state, ['ACTIVE', 'CANCELLED', 'FINISHED'])) {
            throw new InvalidArgumentException("invalid state '{$state}'");
        }
    }
}
