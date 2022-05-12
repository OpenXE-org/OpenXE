<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use stdClass;
use Xentral\Modules\FiskalyApi\Data\MetaData;
use Xentral\Modules\FiskalyApi\Transaction\Transaction;
use Xentral\Modules\FiskalyApi\UuidTool;

class CashPointClosing
{
    /** @var string $clientId */
    private $clientId;

    /** @var int $cashPointClosingExportId */
    private $cashPointClosingExportId;

    /** @var CashPointClosingHead $head */
    private $head;

    /** @var CashPointClosingCashStatement $cashStatement */
    private $cashStatement;

    /** @var CashPointClosingTransactionCollection $transactions */
    private $transactions;

    /** @var MetaData|null $metaData */
    private $metaData;

    /** @var string|null $closingId */
    private $closingId;

    /**
     * CashPointClosing constructor.
     *
     * @param string                                     $clientId
     * @param int                                        $cashPointClosingExportId
     * @param CashPointClosingHead|null                  $head
     * @param CashPointClosingCashStatement|null         $cashStatement
     * @param CashPointClosingTransactionCollection|null $transactions
     * @param MetaData|null                              $metaData
     */
    public function __construct(
        string $clientId,
        int $cashPointClosingExportId,
        ?CashPointClosingHead $head = null,
        ?CashPointClosingCashStatement $cashStatement = null,
        ?CashPointClosingTransactionCollection $transactions = null,
        ?MetaData $metaData = null
    ) {
        $this->setClientId($clientId);
        $this->setCashPointClosingExportId($cashPointClosingExportId);
        $this->setHead($head);
        $this->setCashStatement($cashStatement);
        $this->setTransactions($transactions);
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
            $apiResult->client_id,
            (int)$apiResult->cash_point_closing_export_id,
            empty($apiResult->head) ? null : CashPointClosingHead::fromApiResult($apiResult->head),
            empty($apiResult->cash_statement) ? null : CashPointClosingCashStatement::fromApiResult($apiResult->cash_statement),
            empty($apiResult->transactions) ? null : CashPointClosingTransactionCollection::fromApiResult($apiResult->transactions)
        );
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState)
    {
        return new self(
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
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [
            'client_id'                    => $this->getClientId(),
            'cash_point_closing_export_id' => $this->getCashPointClosingExportId(),
        ];
        if($this->head !== null) {
            $dbState['head'] = $this->getHead()->toArray();
        }
        if($this->cashStatement !== null) {
            $dbState['cash_statement'] = $this->getCashStatement()->toArray();
        }
        if($this->transactions !== null) {
            $dbState['transactions'] = $this->getTransactions()->toArray();
        }
        if ($this->metaData !== null) {
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
        $apiResult->client_id = $this->getClientId();
        $apiResult->cash_point_closing_export_id = $this->getCashPointClosingExportId();
        $apiResult->head = json_decode(json_encode($this->getHead()->toArray()));
        $apiResult->cash_statement = json_decode(json_encode($this->getCashStatement()->toArray()));
        $apiResult->transactions = json_decode(json_encode($this->getTransactions()->toArray()));
        if ($this->metaData !== null) {
            $apiResult->metadata = $this->metaData->toApiResult();
        }

        return $apiResult;
    }

    /**
     * @param BusinessCase $businessCase
     *
     * @return $this
     */
    public function addBusinessCase(BusinessCase $businessCase): self
    {
        $this->cashStatement->setBusinessCases(
            $this->cashStatement->getBusinessCases()->addBusinessCase($businessCase)
        );

        return $this;
    }

    /**
     * @param CashPointClosingTransaction $transaction
     *
     * @return $this
     */
    public function addTransaction(CashPointClosingTransaction $transaction): self
    {
        $this->transactions->addTransaction($transaction);

        return $this;
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
     * @return CashPointClosingHead
     */
    public function getHead(): ?CashPointClosingHead
    {
        return $this->head === null ? null : CashPointClosingHead::fromDbState($this->head->toArray());
    }

    /**
     * @param CashPointClosingHead|null $head
     */
    public function setHead(?CashPointClosingHead $head): void
    {
        $this->head = $head === null ? null : CashPointClosingHead::fromDbState($head->toArray());
    }

    /**
     * @return CashPointClosingCashStatement|null
     */
    public function getCashStatement(): ?CashPointClosingCashStatement
    {
        return $this->cashStatement === null ? null : CashPointClosingCashStatement::fromDbState(
            $this->cashStatement->toArray()
        );
    }

    /**
     * @param CashPointClosingCashStatement|null $cashStatement
     */
    public function setCashStatement(?CashPointClosingCashStatement $cashStatement): void
    {
        $this->cashStatement = $cashStatement === null ? null : CashPointClosingCashStatement::fromDbState(
            $cashStatement->toArray()
        );
    }

    /**
     * @return CashPointClosingTransactionCollection|null
     */
    public function getTransactions(): ?CashPointClosingTransactionCollection
    {
        return $this->transactions === null ? null : CashPointClosingTransactionCollection::fromDbState(
            $this->transactions->toArray()
        );
    }

    /**
     * @param CashPointClosingTransactionCollection|null $transactions
     */
    public function setTransactions(?CashPointClosingTransactionCollection $transactions): void
    {
        $this->transactions = $transactions === null ? null : CashPointClosingTransactionCollection::fromDbState(
            $transactions->toArray()
        );
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
     * @return string|null
     */
    public function getClosingId(): ?string
    {
        if($this->closingId !== null) {
            return $this->closingId;
        }
        $this->closingId = UuidTool::generateUuid();

        return $this->closingId;
    }

    /**
     * @param string|null $closingId
     */
    public function setClosingId(?string $closingId): void
    {
        $this->closingId = $closingId;
    }
}
