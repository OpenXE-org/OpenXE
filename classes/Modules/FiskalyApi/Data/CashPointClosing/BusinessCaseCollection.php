<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class BusinessCaseCollection implements IteratorAggregate, Countable
{
    private $businessCases = [];

    public function __construct(array $businessCases = [])
    {
        foreach($businessCases as $businessCase) {
            $this->addBusinessCase($businessCase);
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
            $instance->addBusinessCase(BusinessCase::fromApiResult($item));
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
            $instance->addBusinessCase(BusinessCase::fromDbState($item));
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [];
        foreach($this as $businessCase) {
            $dbState[] = $businessCase->toArray();
        }

        return $dbState;
    }

    /**
     * @param BusinessCase $businessCase
     */
    public function addBusinessCase(BusinessCase $businessCase): self
    {
        $this->businessCases[] = BusinessCase::fromDbState($businessCase->toArray());

        return $this;
    }

    /**
     * @param BusinessCaseCollection $collection
     *
     * @return $this
     */
    public function combine(BusinessCaseCollection $collection): self
    {
        $instance = new self();
        /** @var BusinessCase $item */
        foreach($this as $item) {
            $instance->addBusinessCase($item);
        }
        foreach($collection as $item) {
            $instance->addBusinessCase($item);
        }

        return $instance->groupByType();
    }

    /**
     * @return float
     */
    public function getSumInclVat(): float
    {
        $sum = 0;
        foreach($this as $item) {
            $sum += $item->getSumInclVat();
        }

        return $sum;
    }

    /**
     * @return $this
     */
    public function groupByType(): self
    {
        $instance = new self();
        $businessTypes = [];
        /** @var BusinessCase $item */
        foreach($this as $item) {
            $type = $item->getType();
            if (!isset($businessTypes[$type])) {
                $businessTypes[$type] = BusinessCase::fromDbState($item->toArray());
            } else {
                $actualAmountsPerVatId = $businessTypes[$type]->getAmountsPerVatId();
                $itemAmountsPerVatId = $item->getAmountsPerVatId();
                $amountsPerVatId = $actualAmountsPerVatId->combine($itemAmountsPerVatId);
                $businessTypes[$type]->setAmountsPerVatId($amountsPerVatId);
            }
        }
        foreach($businessTypes as $businessCase) {
            $instance->addBusinessCase($businessCase);
        }

        return $instance;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->businessCases);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->businessCases);
    }
}
