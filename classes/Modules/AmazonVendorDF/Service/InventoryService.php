<?php

namespace Xentral\Modules\AmazonVendorDF\Service;

use GuzzleHttp\ClientInterface;
use Xentral\Modules\AmazonVendorDF\Data\InventoryItem;
use Xentral\Modules\AmazonVendorDF\Data\Transaction;

class InventoryService
{
    /** @var ClientInterface */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function updateInventory(string $warehouseId, array $items, string $sellingPartyId, bool $isFullUpdate = false)
    {
        //First map all InventoryItems to an array
        $items = array_map(
            function (InventoryItem $item) {
                $data =  $item->toArray();
                unset($data['availableQuantity']['unitSize']);

                return $data;
            },
            $items
        );

        $response = $this->client->request(
            'POST',
            "/vendor/directFulfillment/inventory/v1/warehouses/{$warehouseId}/items",
            [
                'json' => [
                    'inventory' => [
                            'sellingParty' => [
                                'partyId' => $sellingPartyId
                            ],
                            'items' => $items,
                            'isFullUpdate' => $isFullUpdate
                        ]
                ],
            ]
        );

        // The response data is wrapped in a `payload` key
        $payload = json_decode($response->getBody()->getContents(), true)['payload'];

        return (new Transaction('inventory_update'))->setExternalId($payload['transactionId']);
    }
}
