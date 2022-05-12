<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Factory;

use Xentral\Modules\FiskalyApi\Data\CashPointClosing\AmountPerVatId;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\AmountPerVatIdCollection;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\BusinessCase;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\BusinessCaseCollection;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashAmountByCurrency;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashAmountByCurrencyCollection;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingPaymentType;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingPaymentTypeCollection;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingTransaction;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingTransactionAddress;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingTransactionBuyer;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingTransactionCollection;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingTransactionLine;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingTransactionLineCollection;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingTransactionUser;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\TransactionData;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\TransactionHead;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\TransactionSecurity;
use Xentral\Modules\FiskalyApi\Data\Transaction\AmountsPerPaymentTypeCollection;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponse;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponseCollection;
use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

class FiskalyCashPointClosingFactory
{
    private const VAT_DEFINITION_EXPORT_ID_NOT_TAXABLE = 5;

    private const VAT_DEFINITION_EXPORT_ID_NORMAL = 1;

    private const VAT_DEFINITION_EXPORT_ID_REDUCED = 2;

    private const BUYER_ADDRESS_THRESHOLD_AMOUNT = 200;

    /** @var float|null $thresholdNormal */
    private $thresholdNormal;

    /**
     * CashPointClosingFactory constructor.
     *
     * @param float|null $thresholdNormal
     */
    public function __construct(?float $thresholdNormal = null)
    {
        $this->thresholdNormal = $thresholdNormal;
    }

    /**
     * @param float $tax
     *
     * @return $this
     */
    public function setTaxNormal(float $tax): self
    {
        $this->thresholdNormal = $tax;

        return $this;
    }


    /**
     * @param float $inclVat
     *
     * @return BusinessCase
     */
    public function getEmployeeTipBusinessCase(float $inclVat): BusinessCase
    {
        return $this->getNotTaxableBusinessCase('TrinkgeldAN', $inclVat);
    }

    /**
     * @param string $type
     * @param float  $inclVat
     *
     * @return BusinessCase
     */
    public function getNotTaxableBusinessCase(string $type, float $inclVat): BusinessCase
    {
        return new BusinessCase(
            $type, AmountPerVatIdCollection::fromDbState(
            [
                [
                    'vat_definition_export_id' => self::VAT_DEFINITION_EXPORT_ID_NOT_TAXABLE,
                    'incl_vat'                 => $inclVat,
                    'excl_vat'                 => null,
                    'vat'                      => 0,
                ],
            ]
        )
        );
    }

    /**
     * @param float $inclVat
     * @param string $baseCurrencyCode
     *
     * @return CashPointClosingPaymentType
     */
    public function getPaymentType(float $inclVat, string $baseCurrencyCode = 'EUR'): CashPointClosingPaymentType
    {
        return new CashPointClosingPaymentType('Bar', $inclVat, $baseCurrencyCode);
    }

    /**
     * @param array $posJournals
     *
     * @return AmountPerVatIdCollection
     */
    public function createAmountPerVatIdCollectionFromPosJournalDbState(
        array $posJournals
    ): AmountPerVatIdCollection {
        $collection = new AmountPerVatIdCollection();
        foreach ($posJournals as $posJournal) {
            $collection->addAmountPerVatId(
                $this->createAmountPerVatIdFromPosJournalDbState($posJournal)
            );
        }

        return $collection->groupByVatDefinitionExportId();
    }

    /**
     * @param array $posJournal
     *
     * @return AmountPerVatId
     */
    public function createAmountPerVatIdFromPosJournalDbState(
        array $posJournal
    ): AmountPerVatId {
        if ($this->thresholdNormal === null) {
            throw new InvalidArgumentException('no normal tax set');
        }
        $vatDefinitionExportId = self::VAT_DEFINITION_EXPORT_ID_NOT_TAXABLE;
        if ($posJournal['tax'] > $this->thresholdNormal) {
            $vatDefinitionExportId = self::VAT_DEFINITION_EXPORT_ID_NORMAL;
        } elseif ($posJournal['tax'] > 0) {
            $vatDefinitionExportId = self::VAT_DEFINITION_EXPORT_ID_REDUCED;
        }

        return new AmountPerVatId(
            $vatDefinitionExportId,
            (float)$posJournal['amount_gross'],
            (float)$posJournal['amount_net']
        );
    }

    /**
     * @param array $posJournals
     *
     * @return BusinessCaseCollection
     */
    public function createBusinessCaseCollection(
        array $posJournals
    ): BusinessCaseCollection {
        $collection = new BusinessCaseCollection();
        foreach ($posJournals as $posJournal) {
            $collection->addBusinessCase($this->createBusinessCase($posJournal));
        }

        return $collection->groupByType();
    }

    /**
     * @param TransactionReponse $transactionResponse
     *
     * @return AmountsPerPaymentTypeCollection
     */
    public function getPaymentTypesFromTransaction(TransactionReponse $transactionResponse): AmountsPerPaymentTypeCollection
    {
        $instance = new AmountsPerPaymentTypeCollection();
        $schema = $transactionResponse->getSchema();
        if ($schema === null) {
            return $instance;
        }
        $standardV1 = $schema->getStandardV1();
        if ($standardV1 === null) {
            return $instance;
        }
        $receipt = $standardV1->getReceipt();
        if ($receipt === null) {
            return $instance;
        }

        return $receipt->getAmountsPerPaymentType();
    }

    /**
     * @param TransactionReponseCollection $collection
     *
     * @return AmountsPerPaymentTypeCollection
     */
    public function getPaymentTypesFromTransactionCollection(TransactionReponseCollection $collection
    ): AmountsPerPaymentTypeCollection {
        $instance = new AmountsPerPaymentTypeCollection();
        /** @var TransactionReponse $item */
        foreach ($collection as $item) {
            $instance->combine($this->getPaymentTypesFromTransaction($item));
        }

        return $instance;
    }

    /**
     * @param AmountsPerPaymentTypeCollection $amountsPerPaymentTypeCollection
     *
     * @return CashAmountByCurrencyCollection
     */
    public function getCashAmountByCurrencyCollection(
        AmountsPerPaymentTypeCollection $amountsPerPaymentTypeCollection
    ): CashAmountByCurrencyCollection {
        $currencyCodes = $amountsPerPaymentTypeCollection->getCurrencyCodes();
        $collection = new CashAmountByCurrencyCollection();
        foreach ($currencyCodes as $currencyCode) {
            $collection->addAmountPerCurrecy(
                new CashAmountByCurrency($amountsPerPaymentTypeCollection->getSum($currencyCode), $currencyCode)
            );
        }

        return $collection;
    }

    /**
     * @param array $posJournals
     *
     * @return CashPointClosingPaymentTypeCollection
     */
    public function getCashPointClosingPaymentTypeCollection(array $posJournals
    ): CashPointClosingPaymentTypeCollection {
        $collection = new CashPointClosingPaymentTypeCollection();
        foreach ($posJournals as $posJournal) {
            $collection->addPaymentType($this->getCashPointClosingPaymentType($posJournal));
        }

        return $collection->getGrouped();
    }

    /**
     * @param array $posJournalCollection
     *
     * @return CashPointClosingPaymentTypeCollection
     */
    public function getCashPointClosingPaymentTypeCollectionByPosJournalCollection(array $posJournalCollection
    ): CashPointClosingPaymentTypeCollection {
        $collection = new CashPointClosingPaymentTypeCollection();
        foreach ($posJournalCollection as $posJournals) {
            $collection->combine($this->getCashPointClosingPaymentTypeCollection($posJournals));
        }

        return $collection->getGrouped();
    }

    /**
     * @param array $posJournal
     *
     * @return CashPointClosingPaymentType
     */
    public function getCashPointClosingPaymentType(array $posJournal): CashPointClosingPaymentType
    {
        $currencyCode = !empty($posJournal['currency']) ? $posJournal['currency'] : 'EUR';
        $amount = (float)$posJournal['amount_gross'];
        switch ($posJournal['payment_type']) {
            case 'ec':
            case 'eckarte':
                $type = 'ECKarte';
                break;
            case 'kredit':
            case 'kreditkarte':
                $type = 'Kreditkarte';
                break;
            case 'Ueb':
            case 'rechnung':
                $type = 'Unbar';
                break;
            default:
                $type = 'Bar';
                break;
        }
        if ($amount == 0) {
            $type = 'Keine';
        }

        return new CashPointClosingPaymentType($type, $amount, $currencyCode);
    }

    public function getCashPointClosingTransactionCollection(
        TransactionReponseCollection $transactionResponseCollection,
        array $posJournalCollection,
        array $posSessions
    ): CashPointClosingTransactionCollection {
        $collection = new CashPointClosingTransactionCollection();
        /** @var TransactionReponse $item */
        foreach ($transactionResponseCollection as $item) {
            $posJournals = $posJournalCollection[$item->getId()];
            $posSession = $posSessions[$item->getId()];
            $collection->addTransaction($this->getCashPointClosingTransaction($item, $posJournals, $posSession));
        }

        return $collection;
    }

    /**
     * @param string $receiptType
     *
     * @return string
     */
    public function mapReceiptType(string $receiptType): string
    {
        switch ($receiptType) {
            case 'RECEIPT':
                return 'Beleg';
            case 'TRANSFER':
                return 'AVTransfer';
            case 'ORDER':
                return 'AVBestellung';
            case 'CANCELLATION':
                return 'AVBelegabbruch';
            case 'ABORT':
                return 'AVBelegabbruch';
            case 'BENEFIT_IN_KIND':
                return 'AVSachbezug';
            case 'INVOICE':
                return 'AVRechnung';
            case 'OTHER':
                return 'AVSonstige';
            case 'ANNULATION':
                return 'AVBelegstorno';
            default:
                return $receiptType;
        }
    }

    /**
     * @param TransactionReponse $transactionResponse
     * @param array              $posJournals
     * @param array              $posSession
     *
     * @return CashPointClosingTransaction
     */
    public function getCashPointClosingTransaction(
        TransactionReponse $transactionResponse,
        array $posJournals,
        array $posSession
    ): CashPointClosingTransaction {
        $businessCollection = $this->createBusinessCaseCollection($posJournals);
        $user = new CashPointClosingTransactionUser((string)$posSession['kassiererId']);
        $isBuyerCustomer = !empty($posSession['address']['kundennummer']);
        $needUserAddress = !empty($posSession['soll']) && $posSession['soll'] >= self::BUYER_ADDRESS_THRESHOLD_AMOUNT;
        $userAddress = !$needUserAddress ? null : new CashPointClosingTransactionAddress(
            $posSession['address']['strasse'],
            $posSession['address']['plz'],
            $posSession['address']['ort'],
            $posSession['land_iso3']
        );
        $buyer = new CashPointClosingTransactionBuyer(
            $posSession['addr']['name'],
            $isBuyerCustomer ? $posSession['address']['kundennummer'] : $posSession['address']['mitarbeiternummer'],
            $isBuyerCustomer ? 'Kunde' : 'Mitarbeiter',
            $userAddress
        );
        // TODO add error
        $schema = $transactionResponse->getSchema();
        $standardV1 = $schema === null ? null : $schema->getStandardV1();
        $receipt = $standardV1 === null ? null : $standardV1->getReceipt();
        $receiptType = $receipt === null ? 'Beleg' : $this->mapReceiptType($receipt->getReceiptType());

        $lines = new CashPointClosingTransactionLineCollection();//@todo lines generieren

        return new CashPointClosingTransaction(
            new TransactionHead(
                $transactionResponse->getId(),
                $transactionResponse->getId(),
                $transactionResponse->getClientId(),
                $receiptType,
                false,
                $transactionResponse->getNumber(),
                $transactionResponse->getTimeStart(),
                $transactionResponse->getTimeEnd(),
                $user,
                $buyer
            ),
            new TransactionData(
                $businessCollection->getSumInclVat(),
                $this->getCashPointClosingPaymentTypeCollection($posJournals),
                $this->createAmountPerVatIdCollectionFromPosJournalDbState($posJournals),
                $lines
            ),
            new TransactionSecurity($transactionResponse->getId())
        );
    }

    public static function getLines(BusinessCaseCollection $businessCollection
    ): CashPointClosingTransactionLineCollection {
        $lineItemExportId = '';
        $lines = new CashPointClosingTransactionLineCollection();
        /** @var BusinessCase $businessCase */
        foreach ($businessCollection as $businessCase) {
            $line = new CashPointClosingTransactionLine($businessCase, $lineItemExportId, false);
            $lines->addLine($line);
        }

        return $lines;
    }

    /**
     * @param array $posJournal
     *
     * @return BusinessCase
     */
    public function createBusinessCase(array $posJournal): BusinessCase
    {
        switch ($posJournal['type']) {
            case 'Anfangsbestand':
                $type = 'Anfangsbestand';
                break;
            case 'Einlage':
            case 'Entnahme':
                $type = 'Geldtransit';
                break;
            case 'RE_Beleg':
            case 'GS_Beleg':
                $type = 'Umsatz';
                break;
            case 'Gutscheineinlösung':
                $type = 'MehrzweckgutscheinEinloesung';
                break;
            case 'Gutscheinverkauf':
                $type = 'MehrzweckgutscheinKauf';
                break;
            case 'Kassendifferenz':
                $type = 'DifferenzSollIst';
                break;
            case 'Trinkgeld':
                $type = 'TrinkgeldAN';
                break;
            default:
                $type = 'Umsatz';
                break;
        }

        return new BusinessCase(
            $type,
            $this->createAmountPerVatIdCollectionFromPosJournalDbState([$posJournal])
        );
    }
}
