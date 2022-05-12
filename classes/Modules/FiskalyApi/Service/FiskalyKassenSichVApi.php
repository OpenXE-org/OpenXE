<?php


namespace Xentral\Modules\FiskalyApi\Service;


use Exception;
use FiskalyClient\errors\exceptions\FiskalyClientException;
use FiskalyClient\errors\exceptions\FiskalyHttpException;
use FiskalyClient\errors\exceptions\FiskalyHttpTimeoutException;
use Xentral\Modules\FiskalyApi\Data\Client;
use Xentral\Modules\FiskalyApi\Data\Export;
use Xentral\Modules\FiskalyApi\Data\TechnicalSecuritySystem;
use Xentral\Modules\FiskalyApi\Data\Transaction\AmountsPerPaymentTypeCollection;
use Xentral\Modules\FiskalyApi\Data\Transaction\AmountsPerVatTypeCollection;
use Xentral\Modules\FiskalyApi\Data\Transaction\SchemaReceipt;
use Xentral\Modules\FiskalyApi\Data\Transaction\SchemaStandardV1;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponse;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponseCollection;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionRequest;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionSchema;
use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;
use Xentral\Modules\FiskalyApi\Exception\InvalidTransactionException;
use Xentral\Modules\FiskalyApi\Transaction\Transaction;
use Xentral\Modules\FiskalyApi\UuidTool;

class FiskalyKassenSichVApi extends FiskalyApi
{
    /** @var string */
    private const ENDPOINT_BASE = 'https://kassensichv.io/api/v1/';

    /**
     * FiskalyKassenSichVApi constructor.
     *
     * @param string $smaEndpoint
     * @param string $apiKey
     * @param string $apiSecret
     *
     * @throws Exception
     */
    public function __construct(string $smaEndpoint, string $apiKey, string $apiSecret)
    {
        parent::__construct($smaEndpoint, $apiKey, $apiSecret, self::ENDPOINT_BASE);
    }

    /**
     * @return TechnicalSecuritySystem[]
     */
    public function getTechnicalSecuritySystemList(): array
    {
        $result = $this->callApiGet('tss');

        return array_map([TechnicalSecuritySystem::class, 'fromApiResult'], $result->data);
    }


    /**
     * @param string $tssUuid
     *
     * @return TechnicalSecuritySystem
     */
    public function getTechnicalSecuritySystemByUuid(string $tssUuid): TechnicalSecuritySystem
    {
        $result = $this->callApiGet("tss/{$tssUuid}");

        return TechnicalSecuritySystem::fromApiResult($result);
    }

    /**
     * @param string      $tssUuid
     * @param string      $state
     * @param string|null $description
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return TechnicalSecuritySystem
     */
    public function changeSecuritySystem(
        string $tssUuid,
        string $state = 'INITIALIZED',
        ?string $description = null
    ): TechnicalSecuritySystem {
        if (!in_array($state, ['UNINITIALIZED', 'INITIALIZED', 'DISABLED'])) {
            throw new InvalidArgumentException("unknown state '{$state}'");
        }
        $body = ['state' => $state];
        if ($description !== null) {
            $body['description'] = $description;
        }
        $result = $this->callApiPut("tss/{$tssUuid}", json_encode($body));

        return TechnicalSecuritySystem::fromApiResult($result);
    }


    /**
     * @param null|string $tssUuid
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     *
     * @return Client[]
     */
    public function getClients($tssUuid = null): array
    {
        if (empty($tssUuid)) {
            $result = $this->callApiGet("client");
        } else {
            $result = $this->callApiGet("tss/{$tssUuid}/client");
        }

        return array_map([Client::class, 'fromApiResult'], $result->data);
    }

    /**
     * @param string      $tssUuid
     * @param string|null $clientId
     * @param string|null $exportId
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     */
    public function triggerExport(string $tssUuid, ?string $clientId = null, ?string $exportId = null): Export
    {
        if ($exportId === null) {
            $exportId = UuidTool::generateUuid();
        }
        if ($clientId !== null) {
            Export::fromApiResult(
                $this->callApiPut("tss/{$tssUuid}/export/{$exportId}", '{}', ['client_id' => $clientId])
            );
        }

        return Export::fromApiResult($this->callApiPut("tss/{$tssUuid}/export/{$exportId}", '{}'));
    }

    /**
     * @param string $tssUuid
     * @param string $serialNumber
     * @param string $clientId
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return Client
     */
    public function createClient(string $tssUuid, string $serialNumber, string $clientId): Client
    {
        $result = $this->callApiPut(
            "tss/{$tssUuid}/client/{$clientId}",
            json_encode(['serial_number' => $serialNumber])
        );

        return Client::fromApiResult($result);
    }

    /**
     * @param $clientUuid
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     *
     * @return Client
     */
    public function getClientByUuid($tssUuid, $clientUuid): Client
    {
        $result = $this->callApiGet("tss/{$tssUuid}/client/{$clientUuid}");

        return Client::fromApiResult($result);
    }

    /**
     * @param string|null $tssUuid
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return TransactionReponseCollection
     */
    public function getTransactions(
        ?string $tssUuid = null,
        int $offset = 0,
        int $limit = 100
    ): TransactionReponseCollection {
        if ($tssUuid === null) {
            $result = $this->callApiGet("tx", null, ['offset' => $offset, 'limit' => $limit,]);
        } else {
            $result = $this->callApiGet("tss/{$tssUuid}/tx", null, ['offset' => $offset, 'limit' => $limit,]);
        }

        return TransactionReponseCollection::fromApiResult($result->data);
    }

    /**
     * @param string $tssUuid
     * @param string $txId
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return TransactionReponse
     */
    public function getTransaction(string $tssUuid, string $txId): TransactionReponse
    {
        $result = $this->callApiGet("tss/{$tssUuid}/tx/{$txId}");

        return TransactionReponse::fromApiResult($result);
    }

    /**
     * @param Transaction             $transaction
     * @param TechnicalSecuritySystem $technicalSecuritySystem
     *
     * @return Transaction
     */
    public function uploadTransaction(
        Transaction $transaction,
        TechnicalSecuritySystem $technicalSecuritySystem
    ): Transaction {
        $transaction = $this->startTransaction($transaction, $technicalSecuritySystem);

        return $this->finishTransactionOld($transaction, $technicalSecuritySystem);
    }

    /**
     * @param Transaction             $transaction
     * @param TechnicalSecuritySystem $tss
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return Transaction
     */
    public function startTransaction(Transaction $transaction, TechnicalSecuritySystem $tss)
    {
        $tssId = $tss->getUuid();

        $body = json_encode(
            [
                'state'     => 'ACTIVE',
                'client_id' => $transaction->getClientUuid(),
            ]
        );

        $result = $this->callApiPut("tss/{$tssId}/tx/" . $transaction->getUuid(), $body);

        $transaction->setLastRevision($result->revision);

        return $transaction;
    }

    /**
     * @param TransactionRequest $transactionRequest
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return TransactionReponse
     */
    public function createTransaction(TransactionRequest $transactionRequest): TransactionReponse
    {
        return TransactionReponse::fromApiResult(
            $this->callApiPut(
                "tss/{$transactionRequest->getTssId()}/tx/{$transactionRequest->getId()}",
                json_encode($transactionRequest->toApiResult())
            )
        );
    }

    /**
     * @param TransactionRequest $transactionRequest
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return TransactionReponse
     */
    public function updateTransaction(TransactionRequest $transactionRequest): TransactionReponse
    {
        return TransactionReponse::fromApiResult(
            $this->callApiPut(
                "tss/{$transactionRequest->getTssId()}/tx/{$transactionRequest->getId()}",
                json_encode($transactionRequest->toApiResult()),
                ['last_revision' => $transactionRequest->getRevision()]
            )
        );
    }

    /**
     * @param TransactionReponse              $reponse
     * @param string                          $receiptType
     * @param AmountsPerVatTypeCollection     $amountsPerVatTypeCollection
     * @param AmountsPerPaymentTypeCollection $amountsPerPaymentTypeCollection
     *
     * @return TransactionRequest
     */
    public function getFinishTransactionRequest(
        TransactionReponse $reponse,
        string $receiptType,
        AmountsPerVatTypeCollection $amountsPerVatTypeCollection,
        AmountsPerPaymentTypeCollection $amountsPerPaymentTypeCollection
    ): TransactionRequest {
        return (new TransactionRequest(
            'FINISHED',
            $reponse->getClientId(),
            new TransactionSchema(
                new SchemaStandardV1(
                    new SchemaReceipt(
                        $receiptType,
                        $amountsPerVatTypeCollection,
                        $amountsPerPaymentTypeCollection
                    )
                )
            ), $reponse->getMetaData()
        )
        )->setTssId($reponse->getTssId())
            ->setId($reponse->getId())
            ->setRevision($reponse->getLatestRevision());
    }

    /**
     * @param TransactionRequest $request
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @throws Exception
     * @return TransactionReponse
     */
    public function finishTransaction(TransactionRequest $request): TransactionReponse
    {
        $result = $this->callApiPut(
            "tss/{$request->getTssId()}/tx/{$request->getId()}",
            json_encode($request->toArray()),
            ['last_revision' => $request->getRevision()]
        );

        return TransactionReponse::fromApiResult($result);
    }

    /**
     * @param Transaction             $transaction
     * @param TechnicalSecuritySystem $tss
     *
     * @return Transaction
     */
    public function finishTransactionOld(Transaction $transaction, TechnicalSecuritySystem $tss)
    {
        $tssId = $tss->getUuid();

        $vatTypeAmounts = [];
        $paymentTypeAmounts = [];

        foreach ($transaction->getAmountsPerVatRate() as $vatTypeAmount) {
            $vatTypeAmounts[] = [
                'vat_rate' => $vatTypeAmount->getVatType(),
                'amount'   => (string)number_format($vatTypeAmount->getAmount(), 2, '.', ''),
            ];
        }

        foreach ($transaction->getAmountsPerPaymentType() as $paymentTypeAmount) {
            $paymentTypeAmounts[] = [
                'payment_type'  => $paymentTypeAmount->getPaymentType(),
                'amount'        => (string)number_format($paymentTypeAmount->getAmount(), 2, '.', ''),
                'currency_code' => 'EUR',
            ];
        }

        $hasOrderLineItems = count($transaction->getOrderLineItems()) > 0;

        $body =
            [
                'state'     => 'FINISHED',
                'client_id' => $transaction->getClientUuid(),
                'schema'    => [
                    'standard_v1' => [],
                ],
            ];

        $body['schema']['standard_v1'] = [
            'receipt' => [
                'receipt_type'             => 'RECEIPT',
                'amounts_per_vat_rate'     => $vatTypeAmounts,
                'amounts_per_payment_type' => $paymentTypeAmounts,
            ],
        ];
        if ($hasOrderLineItems) {
            foreach ($transaction->getOrderLineItems() as $orderLineItem) {
                $body['schema']['standard_v1']['receipt']['line_items'][] = [
                    'quantity'       => $orderLineItem->getQuantity(),
                    'text'           => $orderLineItem->getText(),
                    'price_per_unit' => $orderLineItem->getPricePerUnit(),
                ];
            }
        }

        if (!$transaction->isLastRevisionSet()) {
            throw new InvalidTransactionException("Transaction last_revision not set");
        }

        $query = ['last_revision' => $transaction->getLastRevision()];

        $uuid = $transaction->getUuid();

        $result = $this->callApiPut("tss/{$tssId}/tx/{$uuid}", json_encode($body), $query);

        $transaction->setLastRevision($result->revision);
        $transaction->setTransactionNumber($result->number);
        $transaction->setStartTime($result->time_start);
        $transaction->setEndTime($result->time_end);
        $transaction->setClientSerialNumber($result->client_serial_number);
        $transaction->setCertificateSerial($result->certificate_serial);
        $transaction->setSignature($result->signature->value);
        $transaction->setSignatureAlgorithm($result->signature->algorithm);
        $transaction->setSignatureCounter($result->signature->counter);
        $transaction->setPublicKey($result->signature->public_key);

        return $transaction;
    }

    /**
     * @param string|null $tssId
     * @param bool        $orderIsDesc
     * @param int         $offset
     * @param int         $limit
     * @param string      $orderBy
     * @param array       $states
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return array
     */
    public function listExports(
        ?string $tssId = null,
        bool $orderIsDesc = false,
        int $offset = 0,
        int $limit = 100,
        string $orderBy = 'time_request',
        array $states = []
    ): array {
        $query = null;
        if ($orderIsDesc) {
            $query['order'] = 'desc';
        }

        if (!empty($states)) {
            $query['states'] = '';
            foreach ($states as $keyState => $state) {
                $query['states'] .= ($keyState > 0 ? '&' : '') . "states%5B{$keyState}%5D={$state}";
            }
        }
        $query['order_by'] = $orderBy;
        $query['limit'] = $limit;
        $query['offset'] = $offset;

        $endPoint = 'export';
        if ($tssId !== null) {
            $endPoint = "tss/{$tssId}/export";
        }
        $result = $this->callApiGet($endPoint, null, $query);

        return array_map([Export::class, 'fromApiResult'], $result->data);
    }
}
