<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

class CashPointClosingInternalTransaktionReference extends CashPointClosingTransactionLineReference
{
    /** @var string $txId */
    private $txId;

    /**
     * CashPointClosingInternalTransaktionReference constructor.
     *
     * @param string $type
     * @param string $txId
     */
    public function __construct(string $type, string $txId)
    {
        parent::__construct($type);
        $this->txId = $txId;
    }

    /**
     * @param $apiResult
     *
     * @return CashPointClosingInternalTransaktionReference
     */
    public static function fromApiResult(object $apiResult): CashPointClosingInternalTransaktionReference
    {
        return new self($apiResult->type, $apiResult->tx_id);
    }

    /**
     * @param array $dbState
     *
     * @return CashPointClosingInternalTransaktionReference
     */
    public static function fromDbState(array $dbState): CashPointClosingInternalTransaktionReference
    {
        return new self($dbState['type'], $dbState['tx_id']);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = parent::toArray();
        $dbState['tx_id'] = $this->txId;

        return $dbState;
    }

    /**
     * @return string
     */
    public function getTxId(): string
    {
        return $this->txId;
    }

    /**
     * @param string $txId
     */
    public function setTxId(string $txId): void
    {
        $this->txId = $txId;
    }
}
