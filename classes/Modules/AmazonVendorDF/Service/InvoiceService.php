<?php

namespace Xentral\Modules\AmazonVendorDF\Service;

use GuzzleHttp\ClientInterface;
use Xentral\Modules\AmazonVendorDF\Data\Invoice;
use Xentral\Modules\AmazonVendorDF\Data\Transaction;

class InvoiceService
{
    /** @var ClientInterface */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function submitInvoice(Invoice $invoice): Transaction
    {
        $response = $this->client->request(
            'POST',
            '/vendor/directFulfillment/payments/v1/invoices',
            ['json' => [$invoice->toArray()]]
        );

        // The response data is wrapped in a `payload` key
        $payload = json_decode($response->getBody()->getContents(), true)['payload'];

        return (new Transaction('invoice'))->setExternalId($payload['transactionId']);
    }
}
