<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

class CashPointClosingTransactionLine
{
    /** @var BusinessCase $businessCase */
    private $businessCase;

    /** @var string $lineitemExportId */
    private $lineitemExportId;

    /** @var bool $storno */
    private $storno;

    /** @var string $text */
    private $text;

    /** @var CashPointClosingTransactionLineItem $item */
    private $item;

    /** @var bool|null $inHouse */
    private $inHouse;

    /** @var CashPointClosingTransactionLineReferenceCollection $references */
    private $references;

    /** @var string|null $voucherId */
    private $voucherId;

    /**
     * CashPointClosingTransactionLine constructor.
     *
     * @param BusinessCase                                             $businessCase
     * @param string                                                   $lineitemExportId
     * @param bool                                                     $isStorno
     * @param string                                                   $text
     * @param CashPointClosingTransactionLineItem                      $item
     * @param bool|null                                                $inHouse
     * @param CashPointClosingTransactionLineReferenceCollection|null $references
     * @param string|null                                              $voucherId
     */
    public function __construct(
        BusinessCase $businessCase,
        string $lineitemExportId,
        bool $isStorno,
        string $text,
        CashPointClosingTransactionLineItem $item,
        ?bool $inHouse = null,
        ?CashPointClosingTransactionLineReferenceCollection $references = null,
        ?string $voucherId = null
    ) {
        $this->businessCase = BusinessCase::fromDbState($businessCase->toArray());
        $this->lineitemExportId = $lineitemExportId;
        $this->storno = $isStorno;
        $this->text = $text;
        $this->item = CashPointClosingTransactionLineItem::fromDbState($item->toArray());
        $this->inHouse = $inHouse;
        if ($references !== null) {
            $this->references = CashPointClosingTransactionLineReferenceCollection::fromDbState(
                $references->toArray()
            );
        }
        $this->voucherId = $voucherId;
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            BusinessCase::fromApiResult($apiResult->business_case),
            $apiResult->lineitem_export_id,
            (bool)$apiResult->storno,
            $apiResult->text,
            CashPointClosingTransactionLineItem::fromApiResult($apiResult->item),
            isset($apiResult->in_house) ? (bool)$apiResult->in_house : null,
            !empty($apiResult->references) ? CashPointClosingTransactionLineReferenceCollection::fromApiResult(
                $apiResult->references
            ) : null,
            $apiResult->voucher_id ?? null
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
            BusinessCase::fromDbState($dbState['business_case']),
            $dbState['lineitem_export_id'],
            (bool)$dbState['storno'],
            $dbState['text'],
            CashPointClosingTransactionLineItem::fromDbState($dbState['item']),
            isset($dbState['in_house']) ? (bool)$dbState['in_house'] : null,
            !empty($dbState['references']) ? CashPointClosingTransactionLineReferenceCollection::fromDbState(
                $dbState['references']
            ) : null,
            $dbState['voucher_id'] ?? null
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [
            'business_case'      => $this->businessCase->toArray(),
            'lineitem_export_id' => $this->getLineitemExportId(),
            'storno'             => $this->isStorno(),
            'text'               => $this->getText(),
            'item'               => $this->getItem()->toArray(),
        ];
        if ($this->inHouse !== null) {
            $dbState['in_house'] = $this->getInHouse();
        }
        if ($this->references !== null) {
            $dbState['references'] = $this->getReferences();
        }
        if ($this->voucherId !== null) {
            $dbState['voucher_id'] = $this->getVoucherId();
        }

        return $dbState;
    }

    /**
     * @return BusinessCase
     */
    public function getBusinessCase(): BusinessCase
    {
        return $this->businessCase;
    }

    /**
     * @param BusinessCase $businessCase
     */
    public function setBusinessCase(BusinessCase $businessCase): void
    {
        $this->businessCase = $businessCase;
    }

    /**
     * @return string
     */
    public function getLineitemExportId(): string
    {
        return $this->lineitemExportId;
    }

    /**
     * @param string $lineitemExportId
     */
    public function setLineitemExportId(string $lineitemExportId): void
    {
        $this->lineitemExportId = $lineitemExportId;
    }

    /**
     * @return bool
     */
    public function isStorno(): bool
    {
        return $this->storno;
    }

    /**
     * @param bool $storno
     */
    public function setStorno(bool $storno): void
    {
        $this->storno = $storno;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return CashPointClosingTransactionLineItem
     */
    public function getItem(): CashPointClosingTransactionLineItem
    {
        return $this->item;
    }

    /**
     * @param CashPointClosingTransactionLineItem $item
     */
    public function setItem(CashPointClosingTransactionLineItem $item): void
    {
        $this->item = $item;
    }

    /**
     * @return bool|null
     */
    public function getInHouse(): ?bool
    {
        return $this->inHouse;
    }

    /**
     * @param bool|null $inHouse
     */
    public function setInHouse(?bool $inHouse): void
    {
        $this->inHouse = $inHouse;
    }

    /**
     * @return CashPointClosingTransactionLineReferenceCollection|null
     */
    public function getReferences(): ?CashPointClosingTransactionLineReferenceCollection
    {
        return $this->references === null ? null : CashPointClosingTransactionLineReferenceCollection::fromDbState(
            $this->references->toArray()
        );
    }

    /**
     * @param CashPointClosingTransactionLineReferenceCollection|null $references
     */
    public function setReferences(?CashPointClosingTransactionLineReferenceCollection $references): void
    {
        $this->references = $references === null ? null : CashPointClosingTransactionLineReferenceCollection::fromDbState(
            $references->toArray()
        );
    }

    /**
     * @return string|null
     */
    public function getVoucherId(): ?string
    {
        return $this->voucherId;
    }

    /**
     * @param string|null $voucherId
     */
    public function setVoucherId(?string $voucherId): void
    {
        $this->voucherId = $voucherId;
    }

}
