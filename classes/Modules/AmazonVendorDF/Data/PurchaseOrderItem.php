<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

class PurchaseOrderItem
{
    /** @var string */
    private $itemSequenceNumber;
    /** @var string */
    private $buyerProductIdentifier;
    /** @var string */
    private $vendorProductIdentifier;
    /** @var string */
    private $title;
    /** @var Quantity */
    private $quantity;
    /** @var Price */
    private $price;
    /** @var float */
    private $taxRate;

    public function __construct(
        string $itemSequenceNumber,
        string $buyerProductIdentifier,
        string $vendorProductIdentifier,
        string $title,
        Quantity $quantity,
        Price $price,
        float $taxRate
    ) {
        $this->itemSequenceNumber = $itemSequenceNumber;
        $this->buyerProductIdentifier = $buyerProductIdentifier;
        $this->vendorProductIdentifier = $vendorProductIdentifier;
        $this->title = $title;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->taxRate = $taxRate;
    }

    public function getItemSequenceNumber(): string
    {
        return $this->itemSequenceNumber;
    }

    public function getBuyerProductIdentifier(): string
    {
        return $this->buyerProductIdentifier;
    }

    public function getVendorProductIdentifier(): string
    {
        return $this->vendorProductIdentifier;
    }

    public function setVendorProductIdentifier(string $vendorProductIdentifier): void
    {
        $this->vendorProductIdentifier = $vendorProductIdentifier;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    public function reject(string $code): AcknowledgementItem
    {
        return new AcknowledgementItem($this, $code);
    }

    public function accept(): AcknowledgementItem
    {
        return new AcknowledgementItem($this, AcknowledgementItem::CODE_ACCEPTED);
    }

    public static function fromPurchaseOrderResponse(array $data): self
    {
        return new static(
            $data['itemSequenceNumber'],
            $data['buyerProductIdentifier'],
            $data['vendorProductIdentifier'],
            $data['title'],
            Quantity::fromArray($data['orderedQuantity']),
            new Price($data['netPrice']['currencyCode'], $data['netPrice']['amount']),
            (float)$data['taxDetails']['taxLineItem'][0]['taxRate']
        );
    }
}
