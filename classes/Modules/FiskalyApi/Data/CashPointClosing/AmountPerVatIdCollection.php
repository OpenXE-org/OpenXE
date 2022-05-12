<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class AmountPerVatIdCollection implements IteratorAggregate, Countable
{
    /** @var AmountPerVatId[] $amountsPerVatId */
    private $amountsPerVatId = [];

    /**
     * AmountPerVatIdCollection constructor.
     *
     * @param array $amountsPerVatId
     */
    public function __construct(array $amountsPerVatId = [])
    {
        foreach ($amountsPerVatId as $amountPerVatId) {
            $this->addAmountPerVatId($amountPerVatId);
        }
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult($apiResult): self
    {
        $instance = new self();
        foreach ($apiResult as $item) {
            $instance->addAmountPerVatId(AmountPerVatId::fromApiResult($item));
        }

        return $instance;
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        $instance = new self();
        foreach ($dbState as $item) {
            $instance->addAmountPerVatId(AmountPerVatId::fromDbState($item));
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [];
        /** @var AmountPerVatId $amountPerVat */
        foreach($this as $amountPerVat) {
            $dbState[] = $amountPerVat->toArray();
        }

        return $dbState;
    }

    /**
     * @param AmountPerVatId $amountPerVatId
     */
    public function addAmountPerVatId(AmountPerVatId $amountPerVatId): void
    {
        $this->amountsPerVatId[] = AmountPerVatId::fromDbState($amountPerVatId->toArray());
    }

    /**
     * @param AmountPerVatIdCollection $collection
     *
     * @return $this
     */
    public function combine(self $collection): self
    {
        $instance = new self();
        foreach($this as $item) {
            $instance->addAmountPerVatId($item);
        }
        foreach($collection as $item) {
            $instance->addAmountPerVatId($item);
        }

        return $instance->groupByVatDefinitionExportId();
    }

    /**
     * @return $this
     */
    public function groupByVatDefinitionExportId(): self
    {
        $instance = new self();
        /** @var AmountPerVatId $item */
        $indexByVatDefinitionExportId = [];
        foreach($this as $item) {
            $vatDefinitionExportId = $item->getVatDefinitionExportId();
            if(!isset($indexByVatDefinitionExportId[$vatDefinitionExportId])) {
                $indexByVatDefinitionExportId[$vatDefinitionExportId] = $item;
            }
            else {
                $indexByVatDefinitionExportId[$vatDefinitionExportId]->setAmounts(
                    $indexByVatDefinitionExportId[$vatDefinitionExportId]->getInclVat() + $item->getExclVat(),
                    $indexByVatDefinitionExportId[$vatDefinitionExportId]->getExclVat() + $item->getExclVat()
                );
            }
        }
        foreach($indexByVatDefinitionExportId as $item) {
            $instance->addAmountPerVatId($item);
        }

        return $instance;
    }

    /**
     * @return float
     */
    public function getSumInclVat(): float
    {
        $sum = 0;
        foreach($this as $item) {
            $sum += $item->getInclVat();
        }

        return $sum;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->amountsPerVatId);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->amountsPerVatId);
    }
}
