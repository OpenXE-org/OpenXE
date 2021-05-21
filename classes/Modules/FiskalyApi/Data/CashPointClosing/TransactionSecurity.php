<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

class TransactionSecurity
{
    /** @var string|null $tssTxId */
    private $tssTxId;

    /** @var string|null $errorMessage */
    private $errorMessage;

    public function __construct(?string $tssTxId, ?string $errorMessage = null)
    {
        if($tssTxId === null && $errorMessage) {
            throw new InvalidArgumentException('tssTxId or error_message must be not null');
        }
        $this->tssTxId = $tssTxId;
        $this->errorMessage = $errorMessage;
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self($apiResult->tss_tx_id ?? null, $apiResult->error_message ?? null);
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        return new self($dbState['tss_tx_id'] ?? null, $dbState['error_message'] ?? null);
    }

    /**
     * @return null[]|string[]
     */
    public function toArray(): array
    {
        if($this->tssTxId !== null) {
            return ['tss_tx_id' => $this->getTssTxId()];
        }

        return ['error_message' => $this->getErrorMessage()];
    }

    /**
     * @return string|null
     */
    public function getTssTxId(): ?string
    {
        return $this->tssTxId;
    }

    /**
     * @param string|null $tssTxId
     */
    public function setTssTxId(?string $tssTxId): void
    {
        $this->tssTxId = $tssTxId;
    }

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * @param string|null $errorMessage
     */
    public function setErrorMessage(?string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }
}
