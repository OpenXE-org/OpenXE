<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

class ShippingLabelRequest
{
    /** @var string */
    protected $purchaseOrderNumber;
    /** @var SellingParty */
    protected $sellingParty;
    /** @var Warehouse */
    protected $warehouse;
    /** @var array|Container[] */
    protected $containers = [];

    public function __construct(string $purchaseOrderNumber, SellingParty $sellingParty, Warehouse $warehouse)
    {
        $this->purchaseOrderNumber = $purchaseOrderNumber;
        $this->sellingParty = $sellingParty;
        $this->warehouse = $warehouse;
    }

    public function addContainer(Container $container)
    {
        $this->containers[] = $container;
    }

    public function toArray()
    {
        return [
            'purchaseOrderNumber' => $this->purchaseOrderNumber,
            'sellingParty'        => [
                'partyId' => $this->sellingParty->getPartyId()
            ],
            'shipFromParty'       => [
                'partyId' => $this->warehouse->getWarehouseId()
            ],
            'containers'          => array_map(
                function (Container $container) {
                    return $container->toArray();
                },
                $this->containers
            ),
        ];
    }
}
