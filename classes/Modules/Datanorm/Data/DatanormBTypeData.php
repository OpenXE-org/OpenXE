<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Data;

final class DatanormBTypeData extends AbstractDatanormTypeData implements DatanormTypeDataInterface
{
    /** @var string $processingFlag */
    protected $processingFlag = '';

    /** @var string $articleNumber */
    protected $articleNumber = '';

    /** @var string $articleNumberNew */
    protected $articleNumberNew = '';

    /** @var string $expirationDate */
    protected $expirationDate = '';

    /** @var string $matchcode */
    protected $matchcode = '';

    /** @var string $altArticleNumber */
    protected $altArticleNumber = '';

    /** @var string $copperWeightIndicator */
    protected $copperWeightIndicator = '';

    /** @var string $copperRawPrice */
    protected $copperRawPrice = '';

    /** @var string $copperWeight */
    protected $copperWeight = '';

    /** @var string $ean */
    protected $ean = '';

    /** @var string $graphicNumber */
    protected $graphicNumber = '';

    /** @var string $productGroup */
    protected $productGroup = '';

    /** @var string $costIndicator */
    protected $costIndicator = '';

    /** @var string $orderAmount */
    protected $orderAmount = '';

    /** @var string $creatorReferenceNumber */
    protected $creatorReferenceNumber = '';

    /** @var string $referenceNumber */
    protected $referenceNumber = '';

    /**
     * @return string
     */
    public function getProcessingFlag(): string
    {
        return $this->processingFlag;
    }

    /**
     * @return string
     */
    public function getArticleNumber(): string
    {
        return $this->articleNumber;
    }

    /**
     * @return string
     */
    public function getArticleNumberNew(): string
    {
        return $this->articleNumberNew;
    }

    /**
     * @return string
     */
    public function getExpirationDate(): string
    {
        return $this->expirationDate;
    }

    /**
     * @return string
     */
    public function getMatchcode(): string
    {
        return $this->matchcode;
    }

    /**
     * @return string
     */
    public function getAltArticleNumber(): string
    {
        return $this->altArticleNumber;
    }

    /**
     * @return string
     */
    public function getCopperWeightIndicator(): string
    {
        return $this->copperWeightIndicator;
    }

    /**
     * @return string
     */
    public function getCopperRawPrice(): string
    {
        return $this->copperRawPrice;
    }

    /**
     * @return string
     */
    public function getCopperWeight(): string
    {
        return $this->copperWeight;
    }

    /**
     * @return string
     */
    public function getEan(): string
    {
        return $this->ean;
    }

    /**
     * @return string
     */
    public function getGraphicNumber(): string
    {
        return $this->graphicNumber;
    }

    /**
     * @return string
     */
    public function getProductGroup(): string
    {
        return $this->productGroup;
    }

    /**
     * @return string
     */
    public function getCostIndicator(): string
    {
        return $this->costIndicator;
    }

    /**
     * @return string
     */
    public function getOrderAmount(): string
    {
        return $this->orderAmount;
    }

    /**
     * @return string
     */
    public function getCreatorReferenceNumber(): string
    {
        return $this->creatorReferenceNumber;
    }

    /**
     * @return string
     */
    public function getReferenceNumber(): string
    {
        return $this->referenceNumber;
    }

    /**
     * @param string $processingFlag
     */
    public function setProcessingFlag(string $processingFlag): void
    {
        $this->processingFlag = $processingFlag;
    }

    /**
     * @param string $articleNumber
     */
    public function setArticleNumber(string $articleNumber): void
    {
        $this->articleNumber = $articleNumber;
    }

    /**
     * @param string $articleNumberNew
     */
    public function setArticleNumberNew(string $articleNumberNew): void
    {
        $this->articleNumberNew = $articleNumberNew;
    }

    /**
     * @param string $expirationDate
     */
    public function setExpirationDate(string $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

    /**
     * @param string $matchcode
     */
    public function setMatchcode(string $matchcode): void
    {
        $this->matchcode = $matchcode;
    }

    /**
     * @param string $altArticleNumber
     */
    public function setAltArticleNumber(string $altArticleNumber): void
    {
        $this->altArticleNumber = $altArticleNumber;
    }

    /**
     * @param string $copperWeightIndicator
     */
    public function setCopperWeightIndicator(string $copperWeightIndicator): void
    {
        $this->copperWeightIndicator = $copperWeightIndicator;
    }

    /**
     * @param string $copperRawPrice
     */
    public function setCopperRawPrice(string $copperRawPrice): void
    {
        $this->copperRawPrice = $copperRawPrice;
    }

    /**
     * @param string $copperWeight
     */
    public function setCopperWeight(string $copperWeight): void
    {
        $this->copperWeight = $copperWeight;
    }

    /**
     * @param string $ean
     */
    public function setEan(string $ean): void
    {
        $this->ean = $ean;
    }

    /**
     * @param string $graphicNumber
     */
    public function setGraphicNumber(string $graphicNumber): void
    {
        $this->graphicNumber = $graphicNumber;
    }

    /**
     * @param string $productGroup
     */
    public function setProductGroup(string $productGroup): void
    {
        $this->productGroup = $productGroup;
    }

    /**
     * @param string $costIndicator
     */
    public function setCostIndicator(string $costIndicator): void
    {
        $this->costIndicator = $costIndicator;
    }

    /**
     * @param string $orderAmount
     */
    public function setOrderAmount(string $orderAmount): void
    {
        $this->orderAmount = $orderAmount;
    }

    /**
     * @param string $creatorReferenceNumber
     */
    public function setCreatorReferenceNumber(string $creatorReferenceNumber): void
    {
        $this->creatorReferenceNumber = $creatorReferenceNumber;
    }

    /**
     * @param string $referenceNumber
     */
    public function setReferenceNumber(string $referenceNumber): void
    {
        $this->referenceNumber = $referenceNumber;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'processingFlag'         => $this->processingFlag,
            'articleNumber'          => $this->articleNumber,
            'articleNumberNew'       => $this->articleNumberNew,
            'expirationDate'         => $this->expirationDate,
            'matchcode'              => $this->matchcode,
            'altArticleNumber'       => $this->altArticleNumber,
            'copperWeightIndicator'  => $this->copperWeightIndicator,
            'copperRawPrice'         => $this->copperRawPrice,
            'copperWeight'           => $this->copperWeight,
            'ean'                    => $this->ean,
            'graphicNumber'          => $this->graphicNumber,
            'productGroup'           => $this->productGroup,
            'costIndicator'          => $this->costIndicator,
            'orderAmount'            => $this->orderAmount,
            'creatorReferenceNumber' => $this->creatorReferenceNumber,
            'referenceNumber'        => $this->referenceNumber,
        ];
    }

    /**
     * @param string $data
     */
    public function fillByJson(string $data): void
    {
        $obj = json_decode($data);

        $this->processingFlag = $obj->processingFlag;
        $this->articleNumber = $obj->articleNumber;
        $this->articleNumberNew = $obj->articleNumberNew;
        $this->expirationDate = $obj->expirationDate;
        $this->matchcode = $obj->matchcode;
        $this->altArticleNumber = $obj->altArticleNumber;
        $this->copperWeightIndicator = $obj->copperWeightIndicator;
        $this->copperRawPrice = $obj->copperRawPrice;
        $this->copperWeight = $obj->copperWeight;
        $this->ean = $obj->ean;
        $this->graphicNumber = $obj->graphicNumber;
        $this->productGroup = $obj->productGroup;
        $this->costIndicator = $obj->costIndicator;
        $this->orderAmount = $obj->orderAmount;
        $this->creatorReferenceNumber = $obj->creatorReferenceNumber;
        $this->referenceNumber = $obj->referenceNumber;
    }
}
