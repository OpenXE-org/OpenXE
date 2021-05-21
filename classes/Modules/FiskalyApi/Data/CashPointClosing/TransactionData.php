<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

class TransactionData
{
    /** @var float $fullAmountInclVat */
    private $fullAmountInclVat;

    /** @var CashPointClosingPaymentTypeCollection $paymentTypes */
    private $paymentTypes;

    /** @var AmountPerVatIdCollection $amountsPerVatId */
    private $amountsPerVatId;

    /** @var CashPointClosingTransactionLineCollection $lines */
    private $lines;

    /** @var string $notes */
    private $notes;

    /**
     * TransactionData constructor.
     *
     * @param float                                     $fullAmountInclVat
     * @param AmountPerVatIdCollection                  $amountsPerVatId
     * @param CashPointClosingTransactionLineCollection $lines
     * @param string|null                               $notes
     */
    public function __construct(
        float $fullAmountInclVat,
        CashPointClosingPaymentTypeCollection $paymentTypes,
        AmountPerVatIdCollection $amountsPerVatId,
        CashPointClosingTransactionLineCollection $lines,
        ?string $notes = null
    ) {
        $this->setFullAmountInclVat($fullAmountInclVat);
        $this->setPaymentTypes($paymentTypes);
        $this->setAmountsPerVatId($amountsPerVatId);
        $this->setLines($lines);
        $this->setNotes($notes);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            (float)$apiResult->full_amount_incl_vat,
            CashPointClosingPaymentTypeCollection::fromApiResult($apiResult->payment_types),
            AmountPerVatIdCollection::fromApiResult($apiResult->amounts_per_vat_id),
            CashPointClosingTransactionLineCollection::fromApiResult($apiResult->lines),
            $apiResult->notes ?? null
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
            (float)$dbState['full_amount_incl_vat'],
            CashPointClosingPaymentTypeCollection::fromDbState($dbState['payment_types']),
            AmountPerVatIdCollection::fromDbState($dbState['amounts_per_vat_id']),
            CashPointClosingTransactionLineCollection::fromDbState($dbState['lines']),
            $dbState['notes'] ?? null
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [
            'full_amount_incl_vat' => $this->getFullAmountInclVat(),
            'payment_types'        => $this->getPaymentTypes()->toArray(),
            'amounts_per_vat_id'   => $this->getAmountsPerVatId()->toArray(),
            'lines'                => $this->getLines()->toArray(),
        ];
        if ($this->notes !== null) {
            $dbState['notes'] = $this->getNotes();
        }

        return $dbState;
    }

    /**
     * @return float
     */
    public function getFullAmountInclVat(): float
    {
        return $this->fullAmountInclVat;
    }

    /**
     * @param float $fullAmountInclVat
     */
    public function setFullAmountInclVat(float $fullAmountInclVat): void
    {
        $this->fullAmountInclVat = $fullAmountInclVat;
    }

    /**
     * @return CashPointClosingPaymentTypeCollection
     */
    public function getPaymentTypes(): CashPointClosingPaymentTypeCollection
    {
        return CashPointClosingPaymentTypeCollection::fromDbState($this->paymentTypes->toArray());
    }

    /**
     * @param CashPointClosingPaymentTypeCollection $paymentTypes
     */
    public function setPaymentTypes(CashPointClosingPaymentTypeCollection $paymentTypes): void
    {
        $this->paymentTypes = CashPointClosingPaymentTypeCollection::fromDbState($paymentTypes->toArray());
    }

    /**
     * @return AmountPerVatIdCollection
     */
    public function getAmountsPerVatId(): AmountPerVatIdCollection
    {
        return AmountPerVatIdCollection::fromDbState($this->amountsPerVatId->toArray());
    }

    /**
     * @param AmountPerVatIdCollection $amountsPerVatId
     */
    public function setAmountsPerVatId(AmountPerVatIdCollection $amountsPerVatId): void
    {
        $this->amountsPerVatId = AmountPerVatIdCollection::fromDbState($amountsPerVatId->toArray());
    }

    /**
     * @return CashPointClosingTransactionLineCollection
     */
    public function getLines(): CashPointClosingTransactionLineCollection
    {
        return CashPointClosingTransactionLineCollection::fromDbState($this->lines->toArray());
    }

    /**
     * @param CashPointClosingTransactionLineCollection $lines
     */
    public function setLines(CashPointClosingTransactionLineCollection $lines): void
    {
        $this->lines = CashPointClosingTransactionLineCollection::fromDbState($lines->toArray());
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     */
    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }
}
