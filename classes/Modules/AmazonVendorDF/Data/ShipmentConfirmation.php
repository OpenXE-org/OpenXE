<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

class ShipmentConfirmation extends ShippingLabelRequest
{
    public function toArray()
    {
        $data = [
            'purchaseOrderNumber' => $this->purchaseOrderNumber,
            'sellingParty'        => $this->sellingParty->toArray(),
            'shipmentDetails' => [
              'shippedDate' => (new \DateTime('now'))->format(DATE_ATOM),
              'shipmentStatus' => 'SHIPPED'
            ],
            'shipFromParty'       => $this->warehouse->toArray(),
            'items'               => $this->extractItemsFromContainers(),
            'containers'          => array_map(
                function (Container $container) {
                    return $container->toArray();
                },
                $this->containers
            ),
        ];

        $data['sellingParty']['taxRegistrationDetails'] = [$data['sellingParty']['taxRegistrationDetails']];

        return $data;
    }

    /**
     * Extract all items form the single containers because they are
     * needed in the top level of the shipment confirmation as well.
     */
    private function extractItemsFromContainers(): array
    {
        $items = [];

        foreach ($this->containers as $container) {
            $items = array_merge($items, $container->getItems());
        }

        return array_map(
            function (array $item) {
                // In the items of the shipmentConfirmation the key is
                // called shippedQuantity instead of packedQuantity
                $item['shippedQuantity'] = $item['packedQuantity'];
                unset($item['packedQuantity']);

                return $item;
            },
            $items
        );
    }
}
