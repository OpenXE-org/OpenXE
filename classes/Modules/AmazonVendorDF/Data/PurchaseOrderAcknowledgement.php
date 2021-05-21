<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

use DateTime;
use Xentral\Modules\AmazonVendorDF\Exception\MissingInformationException;

class PurchaseOrderAcknowledgement
{
    /** @var string */
    private $vendorOrderNumber;
    /** @var string */
    private $purchaseOrderNumber;
    /** @var SellingParty */
    private $sellingParty;
    /** @var Warehouse */
    private $warehouse;
    /** @var array|AcknowledgementItem[] */
    private $items = [];

    public function __construct(string $purchaseOrderNumber)
    {
        $this->purchaseOrderNumber = $purchaseOrderNumber;
    }

    public function getPurchaseOrderNumber(): string
    {
        return $this->purchaseOrderNumber;
    }

    public function addItem(AcknowledgementItem $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function setVendorOrderNumber(string $vendorOrderNumber): self
    {
        $this->vendorOrderNumber = $vendorOrderNumber;

        return $this;
    }

    public function setSellingParty(SellingParty $sellingParty): self
    {
        $this->sellingParty = $sellingParty;

        return $this;
    }

    public function setWarehouse(Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    public function hasRejectedItems(): bool
    {
        foreach ($this->items as $item) {
            if ($item->isRejected()) {
                return true;
            }
        }

        return false;
    }

    protected function getStatusCodeOfFirstRejectedItem(): string
    {
        foreach ($this->items as $item) {
            if ($item->isRejected()) {
                return $item->getStatusCode();
            }
        }
        throw new \RuntimeException('No rejected item found');
    }

    protected function generateStatus(): array
    {
        $statusCode = AcknowledgementItem::CODE_ACCEPTED;
        if ($this->hasRejectedItems()) {
            $statusCode = $this->getStatusCodeOfFirstRejectedItem();
        }

        return [
            'code'        => $statusCode,
            'description' => AcknowledgementItem::AVAILABLE_CODES[$statusCode],
        ];
    }

    public function toArray(): array
    {
        if (!$this->warehouse) {
            throw MissingInformationException::property('warehouse');
        }
        if ($this->warehouse->hasNoAddress()) {
            throw MissingInformationException::property('warehouse address');
        }
        if (!$this->vendorOrderNumber) {
            throw MissingInformationException::property('vendorOrderNumber');
        }

        // Map AcknowledgementItems to array
        $items = array_map(
            function (AcknowledgementItem $item) {
                return $item->toArray();
            },
            $this->items
        );

        $data = [
            'purchaseOrderNumber'   => $this->purchaseOrderNumber,
            'vendorOrderNumber'     => $this->vendorOrderNumber,
            'acknowledgementDate'   => (new DateTime())->format(DateTime::ATOM),
            'acknowledgementStatus' => $this->generateStatus(),
            'sellingParty'          => $this->sellingParty->toArray(),
            'shipFromParty'         => $this->warehouse->toArray(),
            'itemAcknowledgements'  => $items,
        ];

        // In the PurchaseOrderAcknowledgement endpoint the key
        // is named taxInfo instead of taxRegistrationDetails
        $data['sellingParty']['taxInfo'] = $data['sellingParty']['taxRegistrationDetails'];
        $data['shipFromParty']['taxInfo'] = $data['sellingParty']['taxInfo'];
        unset($data['sellingParty']['taxRegistrationDetails']);
        unset($data['sellingParty']['taxInfo']['taxRegistrationAddress']);
        unset($data['shipFromParty']['taxInfo']['taxRegistrationAddress']);

        return $data;
    }
}
