<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

use DateTime;
use Xentral\Modules\AmazonVendorDF\Exception\MissingInformationException;

class AcknowledgementItem
{
    /**
     * Shipping 100 percent of ordered product
     *
     * @var string
     */
    const CODE_ACCEPTED = '00';
    /**
     * Canceled out of stock
     *
     * @var string
     */
    const CODE_REJECT_OUT_OF_STOCK = '03';
    /**
     * No article found for SKU
     *
     * @var string
     */
    const CODE_REJECT_INVALID_SKU = '02';

    const AVAILABLE_CODES = [
        '00' => 'Shipping 100 percent of ordered product',
        '02' => 'Canceled due to missing/invalid SKU',
        '03' => 'Canceled out of stock',
        '04' => 'Canceled due to duplicate Amazon Ship ID',
        '05' => 'Canceled due to missing/invalid Bill To Location Code',
        '06' => 'Canceled due to missing/invalid Ship From Location Code',
        '07' => 'Canceled due to missing/invalid Customer Ship to Name',
        '08' => 'Canceled due to missing/invalid Customer Ship to Address Line 1',
        '10' => 'Canceled due to missing/invalid Customer Ship to City',
        '11' => 'Canceled due to missing/invalid Customer Ship to State',
        '12' => 'Canceled due to missing/invalid Customer Ship to Postal Code',
        '13' => 'Canceled due to missing/invalid Customer Ship to Country Code',
        '20' => 'Canceled due to missing/invalid Shipping Carrier/Shipping Method',
        '21' => 'Canceled due to missing/invalid Ship to Address Line 2',
        '22' => 'Canceled due to missing/invalid Ship to Address Line 3',
        '50' => 'Canceled due to Tax Nexus Issue',
        '51' => 'Canceled due to Restricted SKU/Qty',
    ];

    /** @var PurchaseOrderItem */
    private $item;

    /** @var string */
    private $code;

    public function __construct(PurchaseOrderItem $item, string $code)
    {
        $this->item = $item;
        $this->code = $code;
    }

    public function isRejected(): bool
    {
        return $this->code !== self::CODE_ACCEPTED;
    }

    public function isAccepted(): bool
    {
        return $this->code === self::CODE_ACCEPTED;
    }

    public function getStatusCode(): string
    {
        return $this->code;
    }

    public function toArray(): array
    {
        $data = [
            'itemSequenceNumber'      => $this->item->getItemSequenceNumber(),
            'buyerProductIdentifier'  => $this->item->getBuyerProductIdentifier(),
            'vendorProductIdentifier' => $this->item->getVendorProductIdentifier(),
            'acknowledgedQuantity'    => $this->item->getQuantity()->toArray(),
        ];

        unset($data['acknowledgedQuantity']['unitSize']);

        return $data;
    }
}
