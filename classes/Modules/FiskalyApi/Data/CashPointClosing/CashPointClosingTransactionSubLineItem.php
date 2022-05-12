<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

class CashPointClosingTransactionSubLineItem
{
    /** @var string $number */
    private $number;

    /** @var float $quantity */
    private $quantity;

    /** @var AmountPerVatIdCollection $amountsPerVatId */
    private $amountsPerVatId;

    /** @var string|null $gtin */
    private $gtin;

    /** @var string|null $name */
    private $name;

    /** @var float|null $quantityFactor */
    private $quantityFactor;

    /** @var string|null $quantityMeasure */
    private $quantityMeasure;

    /** @var string|null $groupId */
    private $groupId;

    /** @var string|null $groupName */
    private $groupName;

    /**
     * CashPointClosingTransactionSubLineItem constructor.
     *
     * @param string      $number
     * @param float       $quantity
     * @param array       $amountsPerVatId
     * @param string|null $gtin
     * @param string|null $name
     * @param float|null  $quantityFactor
     * @param string|null $quantityMeasure
     * @param string|null $groupId
     * @param string|null $groupName
     */
    public function __construct(
        string $number,
        float $quantity,
        AmountPerVatIdCollection $amountsPerVatId,
        ?string $gtin = null,
        ?string $name = null,
        ?float $quantityFactor = null,
        ?string $quantityMeasure = null,
        ?string $groupId = null,
        ?string $groupName = null
    ) {
        $this->number = $number;
        $this->quantity = $quantity;
        $this->amountsPerVatId = AmountPerVatIdCollection::fromDbState($amountsPerVatId->toArray());
        $this->gtin = $gtin;
        $this->name = $name;
        $this->quantityFactor = $quantityFactor;
        $this->quantityMeasure = $quantityMeasure;
        $this->groupId = $groupId;
        $this->groupName = $groupName;
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        $instance = new self(
            $apiResult->number,
            (float)$apiResult->quantity,
            AmountPerVatIdCollection::fromApiResult($apiResult->amounts_per_vat_id),
            $apiResult->gtin ?? null,
            $apiResult->name ?? null,
            $apiResult->quantity_factor ?? null,
            $apiResult->quantity_measure ?? null,
            $apiResult->group_id ?? null,
            $apiResult->groupName ?? null
        );

        return $instance;
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        $instance = new self(
            $dbState['number'],
            (float)$dbState['quantity'],
            AmountPerVatIdCollection::fromDbState($dbState['amounts_per_vat_id'])
        );
        if (isset($dbState['gtin'])) {
            $instance->setGtin($dbState['gtin']);
        }
        if (isset($dbState['name'])) {
            $instance->setName($dbState['name']);
        }
        if (isset($dbState['quantity_factor'])) {
            $instance->setQuantityFactor($dbState['quantity_factor']);
        }
        if (isset($dbState['quantity_meassure'])) {
            $instance->setQuantityMeasure($dbState['quantity_meassure']);
        }
        if (isset($dbState['group_id'])) {
            $instance->setGroupId($dbState['group_id']);
        }
        if (isset($dbState['group_name'])) {
            $instance->setGroupName($dbState['group_name']);
        }

        return new $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [
            'number'            => $this->getNumber(),
            'quantity'          => $this->getQuantity(),
            'amount_per_vat_id' => $this->amountsPerVatId->toArray(),
        ];
        if ($this->gtin !== null) {
            $dbState['gtin'] = $this->getGtin();
        }
        if ($this->name !== null) {
            $dbState['name'] = $this->getName();
        }
        if ($this->quantityFactor !== null) {
            $dbState['quantity_factor'] = $this->getQuantityFactor();
        }
        if ($this->quantityMeasure !== null) {
            $dbState['quantity_meassure'] = $this->getQuantityMeasure();
        }
        if ($this->groupId !== null) {
            $dbState['group_id'] = $this->getGroupId();
        }
        if ($this->groupName !== null) {
            $dbState['group_name'] = $this->getGroupName();
        }

        return $dbState;
    }

    /**
     * @param AmountPerVatIdCollection $amountPerVatId
     */
    public function addAmountPerVatId(AmountPerVatIdCollection $amountsPerVatId): void
    {
        $this->amountsPerVatId = AmountPerVatIdCollection::fromDbState($amountsPerVatId->toArray());
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    /**
     * @return float
     */
    public function getQuantity(): float
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     */
    public function setQuantity(float $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return AmountPerVatIdCollection
     */
    public function getAmountsPerVatId(): AmountPerVatIdCollection
    {
        return AmountPerVatIdCollection::fromDbState($this->amountsPerVatId->toArray());
    }

    /**
     * @param AmountPerVatIdCollection $amountsPerVatId
     */
    public function setAmountsPerVatId(AmountPerVatIdCollection $amountsPerVatId): void
    {
        $this->amountsPerVatId = AmountPerVatIdCollection::fromDbState($amountsPerVatId->toArray());
    }

    /**
     * @return string|null
     */
    public function getGtin(): ?string
    {
        return $this->gtin;
    }

    /**
     * @param string|null $gtin
     */
    public function setGtin(?string $gtin): void
    {
        $this->gtin = $gtin;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return float|null
     */
    public function getQuantityFactor(): ?float
    {
        return $this->quantityFactor;
    }

    /**
     * @param float|null $quantityFactor
     */
    public function setQuantityFactor(?float $quantityFactor): void
    {
        $this->quantityFactor = $quantityFactor;
    }

    /**
     * @return string|null
     */
    public function getQuantityMeasure(): ?string
    {
        return $this->quantityMeasure;
    }

    /**
     * @param string|null $quantityMeasure
     */
    public function setQuantityMeasure(?string $quantityMeasure): void
    {
        $this->quantityMeasure = $quantityMeasure;
    }

    /**
     * @return string|null
     */
    public function getGroupId(): ?string
    {
        return $this->groupId;
    }

    /**
     * @param string|null $groupId
     */
    public function setGroupId(?string $groupId): void
    {
        $this->groupId = $groupId;
    }

    /**
     * @return string|null
     */
    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    /**
     * @param string|null $groupName
     */
    public function setGroupName(?string $groupName): void
    {
        $this->groupName = $groupName;
    }
}
