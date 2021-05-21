<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

class InvoiceItem
{
    /** @var string */
    private $itemSequenceNumber;
    /** @var string */
    private $buyerProductIdentifier;
    /** @var string */
    private $vendorProductIdentifier;
    /** @var Quantity */
    private $invoicedQuantity;
    /** @var Price */
    private $netCost;
    /** @var string */
    private $purchaseOrderNumber;
    /** @var TaxDetails */
    private $taxDetails;

    public function getItemSequenceNumber(): string
    {
        return $this->itemSequenceNumber;
    }

    public function setItemSequenceNumber(string $itemSequenceNumber): self
    {
        $this->itemSequenceNumber = $itemSequenceNumber;

        return $this;
    }

    public function getBuyerProductIdentifier(): string
    {
        return $this->buyerProductIdentifier;
    }

    public function setBuyerProductIdentifier(string $buyerProductIdentifier): self
    {
        $this->buyerProductIdentifier = $buyerProductIdentifier;

        return $this;
    }

    public function getVendorProductIdentifier(): string
    {
        return $this->vendorProductIdentifier;
    }

    public function setVendorProductIdentifier(string $vendorProductIdentifier): self
    {
        $this->vendorProductIdentifier = $vendorProductIdentifier;

        return $this;
    }

    public function getInvoicedQuantity(): Quantity
    {
        return $this->invoicedQuantity;
    }

    public function setInvoicedQuantity(Quantity $invoicedQuantity): self
    {
        $this->invoicedQuantity = $invoicedQuantity;

        return $this;
    }

    public function getNetCost(): Price
    {
        return $this->netCost;
    }

    public function setNetCost(Price $netCost): self
    {
        $this->netCost = $netCost;

        return $this;
    }

    public function getPurchaseOrderNumber(): string
    {
        return $this->purchaseOrderNumber;
    }

    public function setPurchaseOrderNumber(string $purchaseOrderNumber): self
    {
        $this->purchaseOrderNumber = $purchaseOrderNumber;

        return $this;
    }

    public function getTaxDetails(): TaxDetails
    {
        return $this->taxDetails;
    }

    public function setTaxDetails(TaxDetails $taxDetails): self
    {
        $this->taxDetails = $taxDetails;

        return $this;
    }

    public function toArray()
    {
        return [
            'purchaseOrderNumber' => $this->purchaseOrderNumber,
            'itemSequenceNumber' => $this->itemSequenceNumber,
            'invoicedQuantity' => $this->invoicedQuantity->toArray(),
            'netCost' => $this->netCost->toArray(),
            'taxDetails' => $this->taxDetails->toArray(),
            // not implemented yet
            'chargeDetails' => [],
        ];
    }
}
