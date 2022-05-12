<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

class CashPointClosingTransaction
{
    /** @var TransactionHead $head */
    private $head;

    /** @var TransactionData $data */
    private $data;

    /** @var TransactionSecurity $security */
    private $security;

    /**
     * CashPointClosingTransaction constructor.
     *
     * @param TransactionHead     $head
     * @param TransactionData     $data
     * @param TransactionSecurity $security
     */
    public function __construct(TransactionHead $head, TransactionData $data, TransactionSecurity $security)
    {
        $this->setHead($head);
        $this->setData($data);
        $this->setSecurity($security);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            TransactionHead::fromApiResult($apiResult->head),
            TransactionData::fromApiResult($apiResult->data),
            TransactionSecurity::fromApiResult($apiResult->security)
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
            TransactionHead::fromDbState($dbState['head']),
            TransactionData::fromDbState($dbState['data']),
            TransactionSecurity::fromDbState($dbState['security'])
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'head'     => $this->getHead()->toArray(),
            'data'     => $this->getData()->toArray(),
            'security' => $this->getSecurity()->toArray(),
        ];
    }

    /**
     * @return TransactionHead
     */
    public function getHead(): TransactionHead
    {
        return TransactionHead::fromDbState($this->head->toArray());
    }

    /**
     * @param TransactionHead $head
     */
    public function setHead(TransactionHead $head): void
    {
        $this->head = TransactionHead::fromDbState($head->toArray());
    }

    /**
     * @return TransactionData
     */
    public function getData(): TransactionData
    {
        return TransactionData::fromDbState($this->data->toArray());
    }

    /**
     * @param TransactionData $data
     */
    public function setData(TransactionData $data): void
    {
        $this->data = TransactionData::fromDbState($data->toArray());
    }

    /**
     * @return TransactionSecurity
     */
    public function getSecurity(): TransactionSecurity
    {
        return TransactionSecurity::fromDbState($this->security->toArray());
    }

    /**
     * @param TransactionSecurity $security
     */
    public function setSecurity(TransactionSecurity $security): void
    {
        $this->security = TransactionSecurity::fromDbState($security->toArray());
    }
}
