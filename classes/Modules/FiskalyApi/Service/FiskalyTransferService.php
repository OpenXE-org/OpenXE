<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Service;

use Aura\SqlQuery\Exception;
use FiskalyClient\errors\exceptions\FiskalyClientException;
use FiskalyClient\errors\exceptions\FiskalyHttpException;
use FiskalyClient\errors\exceptions\FiskalyHttpTimeoutException;
use Xentral\Components\Database\Database;
use Xentral\Modules\FiskalyApi\Data\TechnicalSecuritySystem;
use Xentral\Modules\FiskalyApi\Data\Transaction\AmountsPerPaymentType;
use Xentral\Modules\FiskalyApi\Data\Transaction\AmountsPerPaymentTypeCollection;
use Xentral\Modules\FiskalyApi\Data\Transaction\AmountsPerVatType;
use Xentral\Modules\FiskalyApi\Data\Transaction\AmountsPerVatTypeCollection;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponse;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponseCollection;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionRequest;
use Xentral\Modules\FiskalyApi\Factory\FiskalyApiFactory;
use Xentral\Modules\FiskalyApi\Factory\FiskalyTransactionFactory;
use Xentral\Modules\FiskalyApi\Transaction\Payment\CashPayment;
use Xentral\Modules\FiskalyApi\Transaction\Payment\NonCashPayment;
use Xentral\Modules\FiskalyApi\Transaction\Payment\OrderLineItem;
use Xentral\Modules\FiskalyApi\Transaction\Transaction;
use Xentral\Modules\FiskalyApi\Transaction\VatAmount\BaseVatAmount;

class FiskalyTransferService
{
    /** @var FiskalyKassenSichVApi */
    private $fiskalyApi;

    /** @var FiskalyTransactionFactory $transactionFactory */
    private $transactionFactory;

    /** @var Database */
    private $database;

    /** @var FiskalyApiFactory $fiskalyApiFactory */
    private $fiskalyApiFactory;

    /**
     * FiskalyTransferService constructor.
     *
     * @param FiskalyApiFactory $fiskalyApiFactory
     * @param Database          $database
     */
    public function __construct(
        FiskalyApiFactory $fiskalyApiFactory,
        FiskalyTransactionFactory $transactionFactory,
        Database $database
    ) {
        $this->fiskalyApiFactory = $fiskalyApiFactory;
        $this->transactionFactory = $transactionFactory;
        $this->database = $database;
    }

    /**
     * @param string $organizationId
     *
     * @throws \Exception
     */
    public function loadOrganization(string $organizationId): self
    {
        $this->fiskalyApi = $this->fiskalyApiFactory->createFiskalyKassenSichVApiFromSystemSettings($organizationId);
        return $this;
    }

    /**
     * @param int   $cashierId
     * @param bool  $incoming
     * @param float $amount
     * @param bool  $isCash
     *
     * @throws Exception
     * @return Transaction
     *
     * @depracated
     */
    public function createTransactionFromSingleJournal(
        int $cashierId,
        bool $incoming,
        float $amount,
        bool $isCash
    ): Transaction {
        $posProjectQuery = $this->database->select()
            ->from('pos_kassierer AS p')
            ->cols(['f.tss_uuid', 'f.client_uuid'])
            ->where('p.kassenkennung=:kennung')
            ->leftJoin('fiskaly_pos_mapping AS f', 'f.pos_id = p.projekt')
            ->bindValue('kennung', $cashierId);
        $result = $this->database->fetchRow($posProjectQuery->getStatement(), $posProjectQuery->getBindValues());
        $tssUuid = $result['tss_uuid'];
        $clientId = $result['client_uuid'];
        $tssDescription = $result['tss_description'];

        if ($isCash) {
            $paymentTypePayment = new CashPayment($amount);
        } else {
            $paymentTypePayment = new NonCashPayment($amount);
        }

        $vat = (float)0;
        $sum = $amount * ($incoming ? 1 : -1);
        $vatTypePayment = BaseVatAmount::fromPercentage($vat, $sum);


        $transaction = new Transaction([$paymentTypePayment], [$vatTypePayment], [], $clientId);

        $tss = new TechnicalSecuritySystem($tssUuid, $tssDescription);

        return $this->fiskalyApi->uploadTransaction($transaction, $tss);
    }

    /**
     * @param TransactionRequest $transactionRequest
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return TransactionReponse
     */
    public function startTransaction(TransactionRequest $transactionRequest): TransactionReponse
    {
        return $this->fiskalyApi->createTransaction($transactionRequest);
    }

    /**
     * @param string|null $tssUuid
     * @param int         $offset
     * @param int         $limit
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
        return $this->fiskalyApi->getTransactions($tssUuid, $offset, $limit);
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
        return $this->fiskalyApi->updateTransaction($transactionRequest);
    }

    /**
     * @param TransactionReponse $transactionResponse
     * @param bool               $incoming
     * @param float              $value
     * @param bool               $isTraining
     *
     * @return TransactionRequest
     */
    public function createTransactionRequestFromPosCounting(
        TransactionReponse $transactionResponse,
        bool $incoming,
        float $value,
        bool $isTraining = false
    ): TransactionRequest {
        $negativeMultiplier = $incoming ? 1 : -1;
        $vatTypePayment = BaseVatAmount::fromPercentage(0, $value);
        $amountsPerVatTypeCollection = new AmountsPerVatTypeCollection(
            [
                new AmountsPerVatType(
                    $vatTypePayment->getVatType(), number_format($negativeMultiplier * $value, 2, '.', '')
                ),
            ]
        );
        $amountsPerPaymentTypeCollection = new AmountsPerPaymentTypeCollection(
            [new AmountsPerPaymentType('CASH', number_format($negativeMultiplier * $value, 2, '.', ''), 'EUR')]
        );
        $receiptType = $isTraining ? 'TRAINING' : 'TRANSFER';

        return $this->fiskalyApi->getFinishTransactionRequest(
            $transactionResponse,
            !empty($posSession['training']) ? 'TRAINING' : $receiptType,
            $amountsPerVatTypeCollection,
            $amountsPerPaymentTypeCollection
        );
    }

    /**
     * @param TransactionReponse $transactionResponse
     * @param array              $posSession
     *
     * @return TransactionRequest
     */
    public function createFinishTransactionFromPosSession(
        TransactionReponse $transactionResponse,
        array $posSession
    ): TransactionRequest {
        $amountsPerVatTypeCollection = new AmountsPerVatTypeCollection();
        $amountsPerPaymentTypeCollection = new AmountsPerPaymentTypeCollection();
        $paymentType = $posSession['ptype'];
        $receiptType = 'RECEIPT';
        $type = $posSession['rtype'];
        if (in_array($type, ['einlage', 'entnahme'])) {
            $type = 'TRANSFER';
        }
        $negativeMultiplier = 1;
        if ($type === 'entnahme' || in_array($posSession['cmd'], ['stornieren', 'teilstornieren'])) {
            $negativeMultiplier = -1;
        }
        $tip = isset($posSession['tip']) ? (float)round(str_replace(',', '.', $posSession['tip']), 2) : 0.;
        $amount = (float)$posSession['soll'] * $negativeMultiplier;
        if ($paymentType === 'bar') {
            $amountsPerPaymentTypeCollection->addPaymentType(
                new AmountsPerPaymentType('CASH', number_format($amount + $tip, 2, '.', ''), 'EUR')
            );
        } else {
            $amountsPerPaymentTypeCollection->addPaymentType(
                new AmountsPerPaymentType('NON_CASH', number_format($amount, 2, '.', ''), 'EUR')
            );
            if ($tip > 0) {
                $amountsPerPaymentTypeCollection->addPaymentType(
                    new AmountsPerPaymentType('CASH', number_format($tip, 2, '.', ''), 'EUR')
                );
            }
        }

        foreach ($posSession['wk'] as $position) {
            $vat = str_replace('%', '', $position['tax']);
            $vat = (float)str_replace(',', '.', $vat);
            $sum = (float)str_replace(',', '.', $position['preis']) * (float)str_replace(',', '.', $position['amount'])
                * (1 - (float)str_replace(',', '.', $position['rabatt']) / 100);
            $vatTypePayment = BaseVatAmount::fromPercentage($vat, $sum);
            $amountsPerVatTypeCollection->combine(
                new AmountsPerVatTypeCollection(
                    [
                        new AmountsPerVatType($vatTypePayment->getVatType(), number_format($sum, 2, '.', '')),
                    ]
                )
            );
        }
        if ($tip > 0) {
            $vatTypePayment = BaseVatAmount::fromPercentage(0, $tip);
            $amountsPerVatTypeCollection->combine(
                new AmountsPerVatTypeCollection(
                    [
                        new AmountsPerVatType($vatTypePayment->getVatType(), number_format($tip, 2, '.', '')),
                    ]
                )
            );
        }

        return $this->fiskalyApi->getFinishTransactionRequest(
            $transactionResponse,
            !empty($posSession['training']) ? 'TRAINING' : $receiptType,
            $amountsPerVatTypeCollection,
            $amountsPerPaymentTypeCollection
        );
    }

    /**
     * @param $posSession
     *
     * @throws Exception
     * @return Transaction
     *
     * @depracated
     */
    public function transferPosSession($posSession): Transaction
    {
        $cashierId = $posSession['kassiererId'];
        $result = $this->transactionFactory->getClientAndTssInfoFromCashierId((string)$cashierId);
        $tssUuid = $result['tss_uuid'];
        $clientId = $result['client_uuid'];
        $tssDescription = $result['tss_description'];
        $paymentType = $posSession['ptype'];

        $negativeMultiplier = 1;
        if (in_array($posSession['cmd'], ['stornieren', 'teilstornieren'])) {
            $negativeMultiplier = -1;
        }
        $tip = isset($posSession['tip']) ? (float)round(str_replace(',', '.', $posSession['tip']), 2) : 0.;
        $amount = (float)$posSession['soll'] * $negativeMultiplier;
        $paymentTypePayments = [];
        if ($paymentType === 'bar') {
            $paymentTypePayments[] = new CashPayment($amount + $tip);
        } else {
            $paymentTypePayments[] = new NonCashPayment($amount);
            if ($tip > 0) {
                $paymentTypePayments[] = new CashPayment($tip);
            }
        }

        $vatTypeAmounts = [];
        $oderLineItems = [];

        foreach ($posSession['wk'] as $position) {
            $vat = str_replace('%', '', $position['tax']);
            $vat = (float)str_replace(',', '.', $vat);
            $sum = (float)str_replace(',', '.', $position['preis']);
            $amount = (float)$position['amount'];
            $oderLineItems[] = new OrderLineItem($amount, (string)$position['artikel'], $sum * $negativeMultiplier);
            $sum *= $amount * $negativeMultiplier;
            $vatTypePayment = BaseVatAmount::fromPercentage($vat, $sum);

            $vatTypeClass = get_class($vatTypePayment);
            /** @var BaseVatAmount $vatTypeAmount */
            $cachedVatTypePayment = $vatTypeAmounts[$vatTypeClass] ?? null;
            if (empty($cachedVatTypePayment)) {
                $vatTypeAmounts[$vatTypeClass] = $vatTypePayment;
            } else {
                $cachedVatTypePayment->add($sum);
            }
        }
        if ($tip > 0) {
            $vatTypePayment = BaseVatAmount::fromPercentage(0., $tip);
            $vatTypeClass = get_class($vatTypePayment);
            /** @var BaseVatAmount $vatTypeAmount */
            $cachedVatTypePayment = $vatTypeAmounts[$vatTypeClass] ?? null;
            if ($cachedVatTypePayment === null) {
                $vatTypeAmounts[$vatTypeClass] = $vatTypePayment;
            } else {
                $cachedVatTypePayment->add($tip);
            }
            $oderLineItems[] = new OrderLineItem(1, 'Trinkgeld', $tip);
        }

        $transaction = new Transaction($paymentTypePayments, array_values($vatTypeAmounts), $oderLineItems, $clientId);

        $tss = new TechnicalSecuritySystem($tssUuid, $tssDescription);

        return $this->fiskalyApi->uploadTransaction($transaction, $tss);
    }
}
