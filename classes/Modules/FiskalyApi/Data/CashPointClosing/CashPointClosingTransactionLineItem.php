<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

class CashPointClosingTransactionLineItem
{
    private $number;

    private $quantity;

    private $pricePerUnit;

    private $gtin;

    private $quantityFactor;

    private $quantityMeasure;

    private $groupId;

    private $groupName;

    private $baseAmountsPerVatId;

    private $discountsPerVatId;

    private $extraAmountsPerVatId;

    /** @var SubLineItemCollection $subItems */
    private $subItems;

    /**
     * CashPointClosingTransactionLineItem constructor.
     *
     * @param string                        $number
     * @param float                         $quantity
     * @param float                         $pricePerUnit
     * @param string|null                   $gtin
     * @param float|null                    $quantityFactor
     * @param string|null                   $quantityMeasure
     * @param string|null                   $groupId
     * @param string|null                   $groupName
     * @param AmountPerVatIdCollection|null $baseAmountsPerVatId
     * @param AmountPerVatIdCollection|null $discountsPerVatId
     * @param AmountPerVatIdCollection|null $extraAmountsPerVatId
     * @param SubLineItemCollection|null    $subItems
     */
    public function __construct(
        string $number,
        float $quantity,
        float $pricePerUnit,
        ?string $gtin = null,
        ?float $quantityFactor = null,
        ?string $quantityMeasure = null,
        ?string $groupId = null,
        ?string $groupName = null,
        ?AmountPerVatIdCollection $baseAmountsPerVatId = null,
        ?AmountPerVatIdCollection $discountsPerVatId = null,
        ?AmountPerVatIdCollection $extraAmountsPerVatId = null,
        ?SubLineItemCollection $subItems = null
    ) {
        $this->setNumber($number);
        $this->setQuantity($quantity);
        $this->setPricePerUnit($pricePerUnit);
        $this->setGtin($gtin);
        $this->setQuantityFactor($quantityFactor);
        $this->setQuantityMeasure($quantityMeasure);
        $this->setGroupId($groupId);
        $this->setGroupName($groupName);
        if ($subItems !== null) {
            $this->setSubItems(SubLineItemCollection::fromDbState($subItems->toArray()));
        }
        if ($baseAmountsPerVatId !== null) {
            $this->setBaseAmountsPerVatId($baseAmountsPerVatId);
        }
        if ($discountsPerVatId !== null) {
            $this->setDiscountsPerVatId($discountsPerVatId);
        }
        if ($extraAmountsPerVatId !== null) {
            $this->setExtraAmountsPerVatId($extraAmountsPerVatId);
        }
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
            (float)$apiResult->price_per_unit,
            $apiResult->gtin ?? null,
            $apiResult->quantity_factor ?? null,
            $apiResult->quantity_measure ?? null,
            $apiResult->quantity_factor ?? null,
            $apiResult->group_id ?? null,
            $apiResult->group_name ?? null
        );
        if (isset($apiResult->sub_items)) {
            $instance->setSubItems(SubLineItemCollection::fromApiResult($apiResult->sub_items));
        }
        if (isset($apiResult->base_amounts_per_vat_id)) {
            $instance->setBaseAmountsPerVatId(
                AmountPerVatIdCollection::fromApiResult($apiResult->base_amounts_per_vat_id)
            );
        }
        if (isset($apiResult->discounts_per_vat_id)) {
            $instance->setDiscountsPerVatId(AmountPerVatIdCollection::fromApiResult($apiResult->discounts_per_vat_id));
        }
        if (isset($apiResult->extra_amounts_per_vat_id)) {
            $instance->setExtraAmountsPerVatId(
                AmountPerVatIdCollection::fromApiResult($apiResult->extra_amounts_per_vat_id)
            );
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
        $instance = new self(
            $dbState['number'],
            (float)$dbState['quantity'],
            (float)$dbState['price_per_unit'],
            $dbState['gtin'] ?? null,
            isset($dbState['quantity_factor']) ? (float)$dbState['quantity_factor'] : null,
            $dbState['quantity_measure'] ?? null,
            isset($dbState['quantity_factor']) ? (float)$dbState['quantity_factor'] : null,
            $dbState['group_id'] ?? null,
            $dbState['group_name'] ?? null
        );
        if (!empty($dbState['sub_items'])) {
            $instance->setSubItems(SubLineItemCollection::fromDbState($dbState['sub_items']));
        }
        if (isset($dbState['base_amounts_per_vat_id'])) {
            $instance->setBaseAmountsPerVatId(
                AmountPerVatIdCollection::fromDbState($dbState['base_amounts_per_vat_id'])
            );
        }
        if (isset($dbState['discounts_per_vat_id'])) {
            $instance->setDiscountsPerVatId(AmountPerVatIdCollection::fromDbState($dbState['discounts_per_vat_id']));
        }
        if (isset($dbState['extra_amounts_per_vat_id'])) {
            $instance->setExtraAmountsPerVatId(
                AmountPerVatIdCollection::fromDbState($dbState['extra_amounts_per_vat_id'])
            );
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [
            'number'         => $this->getNumber(),
            'quantity'       => $this->getQuantity(),
            'price_per_unit' => $this->getPricePerUnit(),
        ];
        if ($this->gtin !== null) {
            $dbState['gtin'] = $this->getGtin();
        }
        if ($this->quantityFactor !== null) {
            $dbState['quantity_factor'] = $this->getQuantityFactor();
        }
        if ($this->quantityMeasure !== null) {
            $dbState['quantity_measure'] = $this->getQuantityMeasure();
        }
        if ($this->groupId !== null) {
            $dbState['group_id'] = $this->getGroupId();
        }
        if ($this->groupName !== null) {
            $dbState['group_name'] = $this->getGroupName();
        }
        if ($this->subItems !== null) {
            $dbState['sub_items'] = $this->subItems->toArray();
        }
        if ($this->baseAmountsPerVatId !== null) {
            $dbState['base_amounts_per_vat_id'] = $this->baseAmountsPerVatId->toArray();
        }
        if ($this->discountsPerVatId !== null) {
            $dbState['discounts_per_vat_id'] = $this->discountsPerVatId->toArray();
        }
        if ($this->extraAmountsPerVatId !== null) {
            $dbState['extra_amounts_per_vat_id'] = $this->extraAmountsPerVatId->toArray();
        }

        return $dbState;
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
        $this->number = mb_substr($number, 0, 50);
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
        $this->quantity = (float)number_format($quantity, 3, '.', '');
    }

    /**
     * @return float
     */
    public function getPricePerUnit(): float
    {
        return $this->pricePerUnit;
    }

    /**
     * @param float $pricePerUnit
     */
    public function setPricePerUnit(float $pricePerUnit): void
    {
        $this->pricePerUnit = (float)number_format($pricePerUnit, 5, '.', '');
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
        $this->quantityFactor = $quantityFactor === null ? null : (float)number_format($quantityFactor, 3, '.', '');
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
        $this->quantityMeasure = $quantityMeasure === null ? null : mb_substr($quantityMeasure,0, 50);
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
        $this->groupId = $groupId === null ? null : mb_substr($groupId, 0, 40);
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
        $this->groupName = $groupName === null ? null : mb_substr($groupName, 0, 50);
    }

    /**
     * @return AmountPerVatIdCollection|null
     */
    public function getBaseAmountsPerVatId(): ?AmountPerVatIdCollection
    {
        return $this->baseAmountsPerVatId === null ? null : AmountPerVatIdCollection::fromDbState(
            $this->baseAmountsPerVatId->toArray()
        );
    }

    /**
     * @param AmountPerVatIdCollection|null $baseAmountsPerVatId
     */
    public function setBaseAmountsPerVatId(?AmountPerVatIdCollection $baseAmountsPerVatId): void
    {
        $this->baseAmountsPerVatId = $baseAmountsPerVatId === null ? null : AmountPerVatIdCollection::fromDbState(
            $baseAmountsPerVatId->toArray()
        );
    }

    /**
     * @return AmountPerVatIdCollection|null
     */
    public function getDiscountsPerVatId(): ?AmountPerVatIdCollection
    {
        return $this->discountsPerVatId === null ? null : AmountPerVatIdCollection::fromDbState(
            $this->discountsPerVatId->toArray()
        );
    }

    /**
     * @param AmountPerVatIdCollection|null $discountsPerVatId
     */
    public function setDiscountsPerVatId(?AmountPerVatIdCollection $discountsPerVatId): void
    {
        $this->discountsPerVatId = $discountsPerVatId === null ? null : AmountPerVatIdCollection::fromDbState(
            $discountsPerVatId->toArray()
        );
    }

    /**
     * @return AmountPerVatIdCollection|null
     */
    public function getExtraAmountsPerVatId(): ?AmountPerVatIdCollection
    {
        return $this->extraAmountsPerVatId === null ? null : AmountPerVatIdCollection::fromDbState(
            $this->extraAmountsPerVatId->toArray()
        );
    }

    /**
     * @param AmountPerVatIdCollection|null $extraAmountsPerVatId
     */
    public function setExtraAmountsPerVatId(?AmountPerVatIdCollection $extraAmountsPerVatId): void
    {
        $this->extraAmountsPerVatId = $extraAmountsPerVatId === null ? null : AmountPerVatIdCollection::fromDbState(
            $extraAmountsPerVatId->toArray()
        );
    }

    /**
     * @return ?SubLineItemCollection
     */
    public function getSubItems(): ?SubLineItemCollection
    {
        return $this->subItems === null ? null : SubLineItemCollection::fromDbState($this->subItems->toArray());
    }

    /**
     * @param SubLineItemCollection|null $subItems
     */
    public function setSubItems(?SubLineItemCollection $subItems): void
    {
        $this->subItems = $subItems === null ? null : SubLineItemCollection::fromDbState($subItems->toArray());
    }
}
