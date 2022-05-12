<?php

namespace Xentral\Modules\AmazonVendorDF\Service;

use GuzzleHttp\ClientInterface;
use Xentral\Modules\AmazonVendorDF\Data\ShipmentConfirmation;
use Xentral\Modules\AmazonVendorDF\Data\ShippingLabel;
use Xentral\Modules\AmazonVendorDF\Data\ShippingLabelRequest;
use Xentral\Modules\AmazonVendorDF\Data\Transaction;

class ShippingService
{
    /** @var ClientInterface */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $purchaseOrderNumber
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array|ShippingLabel[]
     */
    public function getShippingLabels(string $purchaseOrderNumber): array
    {
        $response = $this->client->request(
            'GET',
            "/vendor/directFulfillment/shipping/v1/shippingLabels/{$purchaseOrderNumber}"
        );

        $payload = json_decode($response->getBody()->getContents(), true)['payload'];

        return array_map(
            function (array $data) use ($payload) {
                $label = new ShippingLabel($payload['purchaseOrderNumber'], $data['content'], $payload['labelFormat']);
                if (isset($data['trackingNumber']) && $data['trackingNumber'] !== '') {
                    $label->setTrackingNumber($data['trackingNumber']);
                }

                return $label;
            },
            $payload['labelData']
        );
    }

    public function submitShippingLabelRequest(ShippingLabelRequest $shippingLabelRequest): Transaction
    {
        $response = $this->client->request(
            'POST',
            '/vendor/directFulfillment/shipping/v1/shippingLabels',
            [
                'json' => [
                    'shippingLabelRequests' => [$shippingLabelRequest->toArray()],
                ],
            ]
        );

        // The response data is wrapped in a `payload` key
        $payload = json_decode($response->getBody()->getContents(), true)['payload'];

        return (new Transaction('shipping_label_request'))->setExternalId($payload['transactionId']);
    }

    public function submitShipmentConfirmation(ShipmentConfirmation $confirmation)
    {
        $response = $this->client->request(
            'POST',
            '/vendor/directFulfillment/shipping/v1/shipmentConfirmations',
            [
                'json' => [
                    'shipmentConfirmations' => [
                        $confirmation->toArray(),
                    ],
                ],
            ]
        );

        // The response data is wrapped in a `payload` key
        $payload = json_decode($response->getBody()->getContents(), true)['payload'];

        return (new Transaction('shipment_confirmation'))->setExternalId($payload['transactionId']);
    }
}
