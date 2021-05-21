<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

class SchemaStandardV1
{
    /** @var SchemaReceipt|null $receipt */
    private $receipt;

    /** @var SchemaOrder|null $order */
    private $order;

    /** @var SchemaOther|null $other */
    private $other;

    /**
     * SchemaStandardV1 constructor.
     *
     * @param SchemaReceipt|null $receipt
     * @param SchemaOrder|null   $order
     * @param SchemaOther|null   $other
     */
    public function __construct(?SchemaReceipt $receipt, ?SchemaOrder $order = null, ?SchemaOther $other = null)
    {
        $this->setReceipt($receipt);
        $this->setOrder($order);
        $this->setOther($other);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            empty($apiResult->receipt) ? null : SchemaReceipt::fromApiResult($apiResult->receipt),
            empty($apiResult->order) ? null : SchemaOrder::fromApiResult($apiResult->order),
            empty($apiResult->other) ? null : SchemaOther::fromApiResult($apiResult->other)
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
            empty($dbState['receipt']) ? null : SchemaReceipt::fromDbState($dbState['receipt']),
            empty($dbState['order']) ? null : SchemaOrder::fromDbState($dbState['order']),
            empty($dbState['other']) ? null : SchemaOther::fromDbState($dbState['other'])
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [];
        if($this->receipt !== null) {
            $dbState['receipt'] = $this->receipt->toArray();
        }
        if($this->order !== null) {
            $dbState['order'] = $this->order->toArray();
        }
        if($this->other !== null) {
            $dbState['other'] = $this->other->toArray();
        }

        return $dbState;
    }

    /**
     * @return SchemaReceipt|null
     */
    public function getReceipt(): ?SchemaReceipt
    {
        return $this->receipt === null ? null : SchemaReceipt::fromDbState($this->receipt->toArray());
    }

    /**
     * @param SchemaReceipt|null $receipt
     */
    public function setReceipt(?SchemaReceipt $receipt): void
    {
        $this->receipt = $receipt === null ? null : SchemaReceipt::fromDbState($receipt->toArray());
    }

    /**
     * @return SchemaOrder|null
     */
    public function getOrder(): ?SchemaOrder
    {
        return $this->order === null ? null : SchemaOrder::fromDbState($this->order->toArray());
    }

    /**
     * @param SchemaOrder|null $order
     */
    public function setOrder(?SchemaOrder $order): void
    {
        $this->order = $order === null ? null : SchemaOrder::fromDbState($order->toArray());
    }

    /**
     * @return SchemaOther|null
     */
    public function getOther(): ?SchemaOther
    {
        return $this->other === null ? null : SchemaOther::fromDbState($this->other->toArray());
    }

    /**
     * @param SchemaOther|null $other
     */
    public function setOther(?SchemaOther $other): void
    {
        $this->other = $other === null ? null : SchemaOther::fromDbState($other->toArray());
    }
}
