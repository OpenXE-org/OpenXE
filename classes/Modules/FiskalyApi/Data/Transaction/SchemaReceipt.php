<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

class SchemaReceipt
{
    /** @var string $receipType */
    private $receiptType;

    /** @var AmountsPerVatTypeCollection $amountsPerVatRate */
    private $amountsPerVatRate;

    /** @var AmountsPerPaymentTypeCollection $amountsPerPaymentType */
    private $amountsPerPaymentType;

    /**
     * SchemaReceipt constructor.
     *
     * @param string                          $receiptType
     * @param AmountsPerVatTypeCollection     $amountsPerVatId
     * @param AmountsPerPaymentTypeCollection $amountsPerPaymentType
     */
    public function __construct(
        string $receiptType,
        AmountsPerVatTypeCollection $amountsPerVatId,
        AmountsPerPaymentTypeCollection $amountsPerPaymentType
    ) {
        $this->setReceiptType($receiptType);
        $this->setAmountsPerVatRate($amountsPerVatId);
        $this->setAmountsPerPaymentType($amountsPerPaymentType);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            $apiResult->receipt_type,
            AmountsPerVatTypeCollection::fromApiResult($apiResult->amounts_per_vat_rate),
            AmountsPerPaymentTypeCollection::fromApiResult($apiResult->amounts_per_payment_type)
        );
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        return new self(
            $dbState['receipt_type'],
            AmountsPerVatTypeCollection::fromDbState($dbState['amounts_per_vat_rate']),
            AmountsPerPaymentTypeCollection::fromDbState($dbState['amounts_per_payment_type'])
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'receipt_type'             => $this->getReceiptType(),
            'amounts_per_vat_rate'     => $this->amountsPerVatRate->toArray(),
            'amounts_per_payment_type' => $this->amountsPerPaymentType->toArray(),
        ];
    }

    /**
     * @return string
     */
    public function getReceiptType(): string
    {
        return $this->receiptType;
    }

    /**
     * @param string $receiptType
     */
    public function setReceiptType(string $receiptType): void
    {
        $this->ensureType($receiptType);
        $this->receiptType = $receiptType;
    }

    /**
     * @return AmountsPerVatTypeCollection
     */
    public function getAmountsPerVatRate(): AmountsPerVatTypeCollection
    {
        return AmountsPerVatTypeCollection::fromDbState($this->amountsPerVatRate->toArray());
    }

    /**
     * @param AmountsPerVatTypeCollection $amountsPerVatRate
     */
    public function setAmountsPerVatRate(AmountsPerVatTypeCollection $amountsPerVatRate): void
    {
        $this->amountsPerVatRate = AmountsPerVatTypeCollection::fromDbState($amountsPerVatRate->toArray());
    }

    /**
     * @return AmountsPerPaymentTypeCollection
     */
    public function getAmountsPerPaymentType(): AmountsPerPaymentTypeCollection
    {
        return AmountsPerPaymentTypeCollection::fromDbState($this->amountsPerPaymentType->toArray());
    }

    /**
     * @param AmountsPerPaymentTypeCollection $amountsPerPaymentType
     */
    public function setAmountsPerPaymentType(AmountsPerPaymentTypeCollection $amountsPerPaymentType): void
    {
        $this->amountsPerPaymentType = AmountsPerPaymentTypeCollection::fromDbState($amountsPerPaymentType->toArray());
    }

    /**
     * @param string $type
     */
    private function ensureType(string $type): void
    {
        if (in_array(
            $type,
            [
                'RECEIPT',
                'TRAINING',
                'TRANSFER',
                'ORDER',
                'CANCELLATION',
                'ABORT',
                'BENEFIT_IN_KIND',
                'INVOICE',
                'OTHER',
                'ANNULATION',
            ]
        )
        ) {
            return;
        }
        throw new InvalidArgumentException("invalid Type '{$type}'");
    }
}
