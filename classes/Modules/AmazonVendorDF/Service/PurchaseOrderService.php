<?php

namespace Xentral\Modules\AmazonVendorDF\Service;

use DateTime;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException as GuzzleTransferException;
use Xentral\Modules\AmazonVendorDF\Data\PurchaseOrder;
use Xentral\Modules\AmazonVendorDF\Data\PurchaseOrderAcknowledgement;
use Xentral\Modules\AmazonVendorDF\Data\Transaction;
use Xentral\Modules\AmazonVendorDF\Exception\TransferException;

class PurchaseOrderService
{
    /** @var ClientInterface */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param DateTime|null $createdAfter
     * @param DateTime|null $createdBefore
     * @param int|null      $limit
     *
     * @return array|string[]
     */
    public function getPurchaseOrderNumbers(
        ?DateTime $createdAfter = null,
        ?DateTime $createdBefore = null,
        ?int $limit = null
    ): array {
        $orders = $this->getOrders($createdAfter, $createdBefore, $limit);

        return array_map(
            function (array $order) {
                return $order['purchaseOrderNumber'];
            },
            $orders
        );
    }

    /**
     * @param DateTime|null $createdAfter
     * @param DateTime|null $createdBefore
     * @param int|null      $limit
     *
     * @return array|PurchaseOrder[]
     */
    public function getPurchaseOrders(
        ?DateTime $createdAfter = null,
        ?DateTime $createdBefore = null,
        ?int $limit = null
    ): array {
        $orders = $this->getOrders($createdAfter, $createdBefore, $limit);

        return array_map(
            function (array $order) {
                return PurchaseOrder::fromPurchaseOrderResponse($order);
            },
            $orders
        );
    }

    /**
     * @param DateTime|null $createdAfter
     * @param DateTime|null $createdBefore
     * @param int|null      $limit
     *
     * @return array
     */
    protected function getOrders(
        ?DateTime $createdAfter = null,
        ?DateTime $createdBefore = null,
        ?int $limit = null
    ): array {
        $finished = false;
        $nextToken = null;
        $orders = [];
        if ($limit !== null) {
            $response = $this->sendGetOrdersRequest($createdAfter, $createdBefore, null, $limit);
            $orders = array_merge($orders, $response['payload']['orders']);
        } else {
            while (!$finished) {
                $response = $this->sendGetOrdersRequest($createdAfter, $createdBefore, $nextToken);
                if ($response['payload']['pagination']['nextToken']) {
                    $nextToken = $response['payload']['pagination']['nextToken'];
                } else {
                    $finished = true;
                }
                $orders = array_merge($orders, $response['payload']['orders']);
            }
        }

        return $orders;
    }

    public function getOrder(string $purchaseOrderNumber): PurchaseOrder
    {
        $response = $this->client->request(
            'GET',
            "/vendor/directFulfillment/orders/v1/purchaseOrders/{$purchaseOrderNumber}"
        );

        $payload = json_decode($response->getBody()->getContents(), true);

        return PurchaseOrder::fromPurchaseOrderResponse($payload);
    }

    public function submitAcknowledgement(PurchaseOrderAcknowledgement $acknowledgement): Transaction
    {
        try {
            $response = $this->client->request(
                'POST',
                '/vendor/directFulfillment/orders/v1/acknowledgements',
                [
                    'json' => [
                        'orderAcknowledgements' => [$acknowledgement->toArray()],
                    ],
                ]
            );
        }catch (GuzzleTransferException $exception){
            throw new TransferException('Error while submitting acknowledgement',0, $exception);
        }

        // The response data is wrapped in a `payload` key
        $payload = json_decode($response->getBody()->getContents(), true)['payload'];

        return (new Transaction('purchase_order_acknowledgement'))->setExternalId($payload['transactionId']);
    }

    protected function formatDate(DateTime $date)
    {
        return $date->format(DateTime::ISO8601);
    }

    protected function sendGetOrdersRequest(
        ?DateTime $createdAfter,
        ?DateTime $createdBefore,
        ?string $nextToken = null,
        ?int $limit = null
    ): array {
        $response = $this->client->request(
            'GET',
            '/vendor/directFulfillment/orders/v1/purchaseOrders',
            [
                'query' => array_merge(
                    [
                        'createdAfter'   => $this->formatDate($createdAfter ?? new DateTime('-7 days')),
                        'createdBefore'  => $this->formatDate($createdBefore ?? new DateTime()),
                        'limit'          => $limit ?: 100,
                        'includeDetails' => true,
                        'sortOrder'      => 'DESC',
                    ],
                    $nextToken !== null ? ['nextToken' => $nextToken] : []
                ),
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }
}
