<?php

namespace Xentral\Modules\TaxdooApi;

use Xentral\Modules\TaxdooApi\Exception\InvalidKeyException;
use Xentral\Modules\TaxdooApi\Exception\TaxdooApiException;
use Xentral\Modules\TaxdooApi\Exception\TaxdooFatalExcepion;
use Xentral\Modules\TaxdooApi\Exception\TooManyReuestsException;
use Xentral\Modules\TaxdooApi\Exception\UnsufficientPermissionException;

class TaxdooApiService
{
    /** @var string $key */
    private $key;

    //TODO remove sanbox endpoint
    /** @var string $endpoint */
    private $endpoint = 'https://api.taxdoo.com';

    /**
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * @param int $limit
     *
     * @throws TaxdooApiException
     *
     * @return array
     */
    public function getTransactions($limit = 100)
    {
        $curl = curl_init("{$this->endpoint}/transactions?limit={$limit}");
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', "AuthToken: {$this->key}"]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = json_decode(curl_exec($curl));
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->checkHTTPCode($code, $result);
        if ($result->status !== 'success') {
            throw new TaxdooApiException($result->message);
        }

        return $result->transactions;
    }

    /**
     * @param array $transactions
     *
     * @throws TaxdooApiException
     *
     * @return array
     */
    public function addTransactions($transactions)
    {
        if (!is_array($transactions)) {
            $transactions = [$transactions];
        }
        $curl = curl_init("{$this->endpoint}/transactions");
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', "AuthToken: {$this->key}"]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(['transactions' => $transactions]));
        $resultString = curl_exec($curl);
        $result = json_decode($resultString);
        $result->transactions = $transactions;
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->checkHTTPCode($code, $result);
        if (!isset($result->status) || $result->status !== 'success') {
            $result->transactions = $transactions;
            $result->httpCode = $code;
            throw new TaxdooApiException(json_encode($result));
        }

        return $result;
    }

    /**
     * @param string $type "Sale" / "Refund"
     * @param string $date "YYYY-MM-DDTHH:MM:SSZ"
     * @param string $recipientName
     * @param string $recipientStreet
     * @param string $recipientZip
     * @param string $recipientCity
     * @param string $recipientState
     * @param string $recipientCountry
     * @param string $billingName
     * @param string $billingStreet
     * @param string $billingZip
     * @param string $billingCity
     * @param string $billingState
     * @param string $billingCountry
     * @param string $senderName
     * @param string $senderStreet
     * @param string $senderZip
     * @param string $senderCity
     * @param string $senderState
     * @param string $senderCountry
     * @param string $amazonFulfillmentCenterId
     * @param int    $quantity
     * @param string $sku
     * @param string $description
     * @param string $currency
     * @param float  $price
     * @param string $transactionNumber
     * @param string $transactionNumberRemote
     * @param string $project
     *
     * @param string $vatNumber
     *
     * @return array
     */
    public function createTransaction(
        $type,
        $date,
        $recipientName,
        $recipientStreet,
        $recipientZip,
        $recipientCity,
        $recipientState,
        $recipientCountry,
        $billingName,
        $billingStreet,
        $billingZip,
        $billingCity,
        $billingState,
        $billingCountry,
        $senderName,
        $senderStreet,
        $senderZip,
        $senderCity,
        $senderState,
        $senderCountry,
        $amazonFulfillmentCenterId,
        $quantity,
        $sku,
        $description,
        $currency,
        $price,
        $transactionNumber,
        $transactionNumberRemote,
        $project,
        $vatNumber,
        $invoiceNumber
    ) {
        $date = date_create($date)->format(DATE_RFC3339);
        $recipientZip = substr($recipientZip, 0, 15);
        $billingZip = substr($billingZip, 0, 15);
        $senderZip = substr($senderZip, 0, 15);

        $channelIdentifier = preg_replace('/[^a-zA-Z0-9-_]/', '', $project);
        $channelIdentifier = substr($channelIdentifier, 0, 8);

        return [
            'type'                => $type,
            'source'              => [
                'identifier'        => 'XENTRAL',
                'transactionNumber' => $transactionNumber,
                'itemNumber'        => $sku,
            ],
            'channel'             => [
                'identifier'        => $channelIdentifier,
                'transactionNumber' => $transactionNumberRemote,
            ],
            'paymentDate'         => $date,
            'sentDate'            => $date,
            'deliveryAddress'     => [
                'fullName' => $recipientName,
                'street'   => $recipientStreet,
                'zip'      => $recipientZip,
                'city'     => $recipientCity,
                'state'    => $recipientState,
                'country'  => $recipientCountry,
            ],
            'billingAddress'      => [
                'fullName' => $billingName,
                'street'   => $billingStreet,
                'zip'      => $billingZip,
                'city'     => $billingCity,
                'state'    => $billingState,
                'country'  => $billingCountry,
            ],
            'senderAddress'       => [
                'fullName' => $senderName,
                'street'   => $senderStreet,
                'zip'      => $senderZip,
                'city'     => $senderCity,
                'state'    => $senderState,
                'country'  => $senderCountry,
                'amazonFulfillmentCenterId' => $amazonFulfillmentCenterId
            ],
            'quantity'            => (float)$quantity,
            'productIdentifier'   => $sku,
            'description'         => $description,
            'transactionCurrency' => $currency,
            'itemPrice'           => (float)$price,
            'buyerVatNumber'      => $vatNumber,
            'invoiceNumber'       => $invoiceNumber,
        ];
    }

    /**
     * @param string $type "Sale" / "Refund"
     * @param string $date "YYYY-MM-DDTHH:MM:SSZ"
     * @param string $recipientName
     * @param string $recipientStreet
     * @param string $recipientZip
     * @param string $recipientCity
     * @param string $recipientState
     * @param string $recipientCountry
     * @param string $billingName
     * @param string $billingStreet
     * @param string $billingZip
     * @param string $billingCity
     * @param string $billingState
     * @param string $billingCountry
     * @param string $senderName
     * @param string $senderStreet
     * @param string $senderZip
     * @param string $senderCity
     * @param string $senderState
     * @param string $senderCountry
     * @param int    $quantity
     * @param string $sku
     * @param string $description
     * @param string $currency
     * @param float  $price
     * @param string $transactionNumber
     * @param string $transactionNumberRemote
     * @param        $refundNumber
     * @param        $refundNumberRemote
     * @param string $project
     *
     * @param        $vatNumber
     *
     * @return array
     */
    public function createRefund(
        $type,
        $date,
        $recipientName,
        $recipientStreet,
        $recipientZip,
        $recipientCity,
        $recipientState,
        $recipientCountry,
        $billingName,
        $billingStreet,
        $billingZip,
        $billingCity,
        $billingState,
        $billingCountry,
        $senderName,
        $senderStreet,
        $senderZip,
        $senderCity,
        $senderState,
        $senderCountry,
        $quantity,
        $sku,
        $description,
        $currency,
        $price,
        $transactionNumber,
        $transactionNumberRemote,
        $refundNumber,
        $refundNumberRemote,
        $project,
        $vatNumber,
        $invoiceNumber
    ) {
        $date = date_create($date)->format(DATE_RFC3339);
        $recipientZip = substr($recipientZip, 0, 15);
        $billingZip = substr($billingZip, 0, 15);
        $senderZip = substr($senderZip, 0, 15);

        $channelIdentifier = preg_replace('/[^a-zA-Z0-9-_]/', '', $project);
        $channelIdentifier = substr($channelIdentifier, 0, 8);

        return [
            'type'                => $type,
            'source'              => [
                'identifier'        => 'XENTRAL',
                'transactionNumber' => $transactionNumber,
                'itemNumber'        => $sku,
                'refundNumber'      => $refundNumber
            ],
            'channel'             => [
                'identifier'        => $channelIdentifier,
                'transactionNumber' => $transactionNumberRemote,
                'refundNumber'      => $refundNumberRemote
            ],
            'paymentDate'         => $date,
            'sentDate'            => $date,
            'deliveryAddress'     => [
                'fullName' => $recipientName,
                'street'   => $recipientStreet,
                'zip'      => $recipientZip,
                'city'     => $recipientCity,
                'state'    => $recipientState,
                'country'  => $recipientCountry,
            ],
            'billingAddress'      => [
                'fullName' => $billingName,
                'street'   => $billingStreet,
                'zip'      => $billingZip,
                'city'     => $billingCity,
                'state'    => $billingState,
                'country'  => $billingCountry,
            ],
            'senderAddress'       => [
                'fullName' => $senderName,
                'street'   => $senderStreet,
                'zip'      => $senderZip,
                'city'     => $senderCity,
                'state'    => $senderState,
                'country'  => $senderCountry,
            ],
            'quantity'            => (float)$quantity,
            'productIdentifier'   => $sku,
            'description'         => $description,
            'transactionCurrency' => $currency,
            'itemPrice'           => (float)$price,
            'buyerVatNumber'      => $vatNumber,
            'invoiceNumber'       => $invoiceNumber,
        ];
    }


    /**
     * @param int   $code
     * @param mixed $jsonObject
     *
     * @throws InvalidKeyException|TooManyReuestsException|TaxdooFatalExcepion
     *
     * @return void
     */
    private function checkHTTPCode($code, $jsonObject)
    {
        if ($code === 200 || $code === 400) {
            return;
        }
        switch ($code) {
            case 401:
                throw InvalidKeyException::fromKey($this->key);
            case 403:
                throw UnsufficientPermissionException::fromKey($this->key);
            case 429:
                throw TooManyReuestsException::fromTimeout(0);
        }
        throw new TaxdooFatalExcepion(json_encode($jsonObject, JSON_PRETTY_PRINT));
    }
}
