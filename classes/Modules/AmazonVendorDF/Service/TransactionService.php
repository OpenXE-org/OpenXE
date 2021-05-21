<?php

namespace Xentral\Modules\AmazonVendorDF\Service;

use GuzzleHttp\ClientInterface;
use Xentral\Modules\AmazonVendorDF\Data\Transaction;

class TransactionService
{
    /** @var ClientInterface */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function updateTransactionStatus(Transaction $transaction): Transaction
    {
        $response = $this->client->request(
            'GET',
            "/vendor/directFulfillment/transactions/v1/transactions/{$transaction->getExternalId()}"
        );

        // The response data is wrapped in a `payload` key
        $payload = json_decode($response->getBody()->getContents(), true)['payload']['transactionStatus'];

        $transaction->setStatus($payload['status']);
        if($transaction->hasFailed()){
            $transaction->setErrors($payload['errors']);
        }

        return $transaction;
    }

    public function getTransactionByTransactionId(string $transactionId): Transaction
    {
        $response = $this->client->request(
            'GET',
            "/vendor/directFulfillment/transactions/v1/transactions/{$transactionId}"
        );

        // The response data is wrapped in a `payload` key
        $payload = json_decode($response->getBody()->getContents(), true)['payload']['transactionStatus'];

        $transaction = new Transaction();
        $transaction->setStatus($payload['status']);
        if ($transaction->hasFailed()) {
            $transaction->setErrors($payload['errors']);
        }

        return $transaction;
    }
}
