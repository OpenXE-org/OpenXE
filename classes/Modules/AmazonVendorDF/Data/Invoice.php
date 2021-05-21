<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

use DateTime;
use Xentral\Modules\AmazonVendorDF\Exception\MissingInformationException;

class Invoice
{
    /** @var string */
    private $invoiceNumber;
    /** @var DateTime */
    private $invoiceDate;
    /** @var Address */
    private $billToAddress;
    /** @var Price */
    private $invoiceTotal;
    /** @var array|InvoiceItem[] */
    private $items;
    /** @var SellingParty */
    private $remitToParty;
    /** @var Warehouse */
    private $warehouse;

    public function __construct(
        string $invoiceNumber,
        DateTime $invoiceDate,
        SellingParty $remitToParty,
        Warehouse $warehouse
    ) {
        $this->invoiceNumber = $invoiceNumber;
        $this->invoiceDate = $invoiceDate;
        $this->remitToParty = $remitToParty;
        $this->warehouse = $warehouse;
    }

    public function addItem(InvoiceItem $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function setBillToAddress(Address $address)
    {
        $this->billToAddress = $address;
    }

    public function setInvoiceTotal(Price $invoiceTotal): self
    {
        $this->invoiceTotal = $invoiceTotal;

        return $this;
    }

    public function toArray()
    {
        if (!$this->invoiceTotal) {
            throw MissingInformationException::property('invoiceTotal');
        }

        return [
            'invoiceNumber' => $this->invoiceNumber,
            'invoiceDate'   => $this->invoiceDate,
            'remitToParty'  => $this->remitToParty->toArray(),
            'shipFromParty' => $this->formatShipFromParty(),
            'invoiceTotal'  => $this->invoiceTotal->toArray(),
            'taxTotals'     => $this->grabTaxTotalsFromInvoiceItems(),
            'items'         => $this->mapInvoiceItemsToArray(),
        ];
    }

    private function grabTaxTotalsFromInvoiceItems(): array
    {
        return array_map(function (InvoiceItem $item){
            return $item->getTaxDetails()->toArray();
        }, $this->items);
    }

    private function mapInvoiceItemsToArray()
    {
        return array_map(
            function (InvoiceItem $item) {
                return $item->toArray();
            },
            $this->items
        );
    }

    /**
     * Currently the warehouse uses the taxRegistrationDetails and address of the remitToParty
     */
    private function formatShipFromParty(): array
    {
        $data = $this->remitToParty->toArray();
        $data['partyId'] = $this->warehouse->getWarehouseId();

        return $data;
    }
}
