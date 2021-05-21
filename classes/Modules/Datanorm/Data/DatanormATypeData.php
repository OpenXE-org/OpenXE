<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Data;

final class DatanormATypeData extends AbstractDatanormTypeData implements DatanormTypeDataInterface
{
    /** @var string $workflowState */
    protected $workflowState = '';

    /** @var string $articleNumber */
    protected $articleNumber = '';

    /** @var string $shortDescription1 */
    protected $shortDescription1 = '';

    /** @var string $shortDescription2 */
    protected $shortDescription2 = '';

    /** @var string $matchcode */
    protected $matchcode = '';

    /** @var string $ean */
    protected $ean = '';

    /** @var string $discountGroup */
    protected $discountGroup = '';

    /** @var string $mainProductGroup */
    protected $mainProductGroup = '';

    /** @var string $productGroup */
    protected $productGroup = '';

    /** @var string $minimumPackageAmount */
    protected $minimumPackageAmount = '';

    /** @var string $priceMark */
    protected $priceMark = '';

    /** @var float $price */
    protected $price = 0.0;

    /** @var int $priceAmount */
    protected $priceAmount = 0;

    /** @var string $packingUnit */
    protected $packingUnit = '';

    /** @var string $producerToken1 */
    protected $producerToken1 = '';

    /** @var string $producerToken2 */
    protected $producerToken2 = '';

    /** @var string $producerToken3 */
    protected $producerToken3 = '';

    /** @var string $altArticleNumber */
    protected $altArticleNumber = '';

    /** @var string $producerModel */
    protected $producerModel = '';

    /** @var string $connectionNumber */
    protected $connectionNumber = '';

    /** @var string $cataloguePage */
    protected $cataloguePage = '';

    /** @var string $longDecriptionKey */
    protected $longDecriptionKey = '';

    /** @var string $costType */
    protected $costType = '';

    /** @var string $articleType */
    protected $articleType = '';

    /** @var string $referenceNumber */
    protected $referenceNumber = '';

    /** @var int $mwstType */
    protected $mwstType = 1;

    /** @var string $textkey */
    protected $textkey = '';

    /** @var string $producerNumber */
    protected $producerNumber = '';

    /**
     * Values : N:Neuanlage, L:Löschung, A:Änderung, X:Artikelnummernänderung
     *
     * @return string
     */
    public function getWorkflowState(): string
    {
        return $this->workflowState;
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
    public function getShortDescription1(): string
    {
        return $this->shortDescription1;
    }

    /**
     * @return string
     */
    public function getShortDescription2(): string
    {
        return $this->shortDescription2;
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
    public function getEan(): string
    {
        return $this->ean;
    }

    /**
     * @return string
     */
    public function getDiscountGroup(): string
    {
        return $this->discountGroup;
    }

    /**
     * @return string
     */
    public function getMainProductGroup(): string
    {
        return $this->mainProductGroup;
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
    public function getMinimumPackageAmount(): string
    {
        return $this->minimumPackageAmount;
    }

    /**
     * Values: 1:Listenpreis, 2:Nettopreis
     *
     * @return string
     */
    public function getPriceMark(): string
    {
        return $this->priceMark;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getPriceAmount(): int
    {
        return $this->priceAmount;
    }

    /**
     * @return string
     */
    public function getPackingUnit(): string
    {
        return $this->packingUnit;
    }

    /**
     * @return string
     */
    public function getProducerToken1(): string
    {
        return $this->producerToken1;
    }

    /**
     * @return string
     */
    public function getProducerToken2(): string
    {
        return $this->producerToken2;
    }

    /**
     * @return string
     */
    public function getProducerToken3(): string
    {
        return $this->producerToken3;
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
    public function getProducerModel(): string
    {
        return $this->producerModel;
    }

    /**
     * @return string
     */
    public function getConnectionNumber(): string
    {
        return $this->connectionNumber;
    }

    /**
     * @return string
     */
    public function getCataloguePage(): string
    {
        return $this->cataloguePage;
    }

    /**
     * @return string
     */
    public function getLongDecriptionKey(): string
    {
        return $this->longDecriptionKey;
    }

    /**
     * @return string
     */
    public function getCostType(): string
    {
        return $this->costType;
    }

    /**
     * Values: 1:Lagerartikel, 2:Bestellartikel
     *
     * @return string
     */
    public function getArticleType(): string
    {
        return $this->articleType;
    }

    /**
     * @return string
     */
    public function getReferenceNumber(): string
    {
        return $this->referenceNumber;
    }

    /**
     * Values: 1:normal 2:erhöht 3:reduziert
     *
     * @return int
     */
    public function getMwstType(): int
    {
        return $this->mwstType;
    }

    /**
     * Values: 0:'KT1 + KT2', 1:'LT + KT2', 2:'KT1 + DT', 3: 'LT + DT', 4:'KT1 + KT2 + LT', 5:'KT1 + KT2 + DT', 6: 'KT1
     * + KT2 + LT + DT' KT1 = $shortDescription1, KT2 = $shortDescription2
     *
     * @return string
     */
    public function getTextkey(): string
    {
        return $this->textkey;
    }

    /**
     * @return string
     */
    public function getProducerNumber(): string
    {
        return $this->producerNumber;
    }

    /**
     * Values : N:Neuanlage, L:Löschung, A:Änderung, X:Artikelnummernänderung
     *
     * @param string $workflowState
     */
    public function setWorkflowState(string $workflowState): void
    {
        $this->workflowState = $workflowState;
    }

    /**
     * @param string $articleNumber
     */
    public function setArticleNumber(string $articleNumber): void
    {
        $this->articleNumber = $articleNumber;
    }

    /**
     * @param string $shortDescription1
     */
    public function setShortDescription1(string $shortDescription1): void
    {
        $this->shortDescription1 = $shortDescription1;
    }

    /**
     * @param string $shortDescription2
     */
    public function setShortDescription2(string $shortDescription2): void
    {
        $this->shortDescription2 = $shortDescription2;
    }

    /**
     * @param string $matchcode
     */
    public function setMatchcode(string $matchcode): void
    {
        $this->matchcode = $matchcode;
    }

    /**
     * @param string $ean
     */
    public function setEan(string $ean): void
    {
        $this->ean = $ean;
    }

    /**
     * @param string $discountGroup
     */
    public function setDiscountGroup(string $discountGroup): void
    {
        $this->discountGroup = $discountGroup;
    }

    /**
     * @param string $mainProductGroup
     */
    public function setMainProductGroup(string $mainProductGroup): void
    {
        $this->mainProductGroup = $mainProductGroup;
    }

    /**
     * @param string $productGroup
     */
    public function setProductGroup(string $productGroup): void
    {
        $this->productGroup = $productGroup;
    }

    /**
     * @param string $minimumPackageAmount
     */
    public function setMinimumPackageAmount(string $minimumPackageAmount): void
    {
        $this->minimumPackageAmount = $minimumPackageAmount;
    }

    /**
     * Values: 1:Listenpreis, 2:Nettopreis
     *
     * @param string $priceMark
     */
    public function setPriceMark(string $priceMark): void
    {
        $this->priceMark = $priceMark;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @param int $priceAmount
     */
    public function setPriceAmount(int $priceAmount): void
    {
        $this->priceAmount = $priceAmount;
    }

    /**
     * @param string $packingUnit
     */
    public function setPackingUnit(string $packingUnit): void
    {
        $this->packingUnit = $packingUnit;
    }

    /**
     * @param string $producerToken1
     */
    public function setProducerToken1(string $producerToken1): void
    {
        $this->producerToken1 = $producerToken1;
    }

    /**
     * @param string $producerToken2
     */
    public function setProducerToken2(string $producerToken2): void
    {
        $this->producerToken2 = $producerToken2;
    }

    /**
     * @param string $producerToken3
     */
    public function setProducerToken3(string $producerToken3): void
    {
        $this->producerToken3 = $producerToken3;
    }

    /**
     * @param string $altArticleNumber
     */
    public function setAltArticleNumber(string $altArticleNumber): void
    {
        $this->altArticleNumber = $altArticleNumber;
    }

    /**
     * @param string $producerModel
     */
    public function setProducerModel(string $producerModel): void
    {
        $this->producerModel = $producerModel;
    }

    /**
     * @param string $connectionNumber
     */
    public function setConnectionNumber(string $connectionNumber): void
    {
        $this->connectionNumber = $connectionNumber;
    }

    /**
     * @param string $cataloguePage
     */
    public function setCataloguePage(string $cataloguePage): void
    {
        $this->cataloguePage = $cataloguePage;
    }

    /**
     * @param string $longDecriptionKey
     */
    public function setLongDecriptionKey(string $longDecriptionKey): void
    {
        $this->longDecriptionKey = $longDecriptionKey;
    }

    /**
     * @param string $costType
     */
    public function setCostType(string $costType): void
    {
        $this->costType = $costType;
    }

    /**
     * Values: 1:Lagerartikel, 2:Bestellartikel
     *
     * @param string $articleType
     */
    public function setArticleType(string $articleType): void
    {
        $this->articleType = $articleType;
    }

    /**
     * @param string $referenceNumber
     */
    public function setReferenceNumber(string $referenceNumber): void
    {
        $this->referenceNumber = $referenceNumber;
    }

    /**
     * Values: 1:normal 2:erhöht 3:reduziert
     *
     * @param int $mwstType
     */
    public function setMwstType(int $mwstType): void
    {
        $this->mwstType = $mwstType;
    }

    /**
     * Values: 0:'KT1 + KT2', 1:'LT + KT2', 2:'KT1 + DT', 3: 'LT + DT', 4:'KT1 + KT2 + LT', 5:'KT1 + KT2 + DT', 6: 'KT1
     * + KT2 + LT + DT' KT1 = $shortDescription1, KT2 = $shortDescription2
     *
     * @param string $textkey
     */
    public function setTextkey(string $textkey): void
    {
        $this->textkey = $textkey;
    }

    /**
     * @param string $producerNumber
     */
    public function setProducerNumber(string $producerNumber): void
    {
        $this->producerNumber = $producerNumber;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'workflowState'        => $this->workflowState,
            'articleNumber'        => $this->articleNumber,
            'shortDescription1'    => $this->shortDescription1,
            'shortDescription2'    => $this->shortDescription2,
            'matchcode'            => $this->matchcode,
            'ean'                  => $this->ean,
            'discountGroup'        => $this->discountGroup,
            'mainProductGroup'     => $this->mainProductGroup,
            'productGroup'         => $this->productGroup,
            'minimumPackageAmount' => $this->minimumPackageAmount,
            'priceMark'            => $this->priceMark,
            'price'                => $this->price,
            'priceAmount'          => $this->priceAmount,
            'packingUnit'          => $this->packingUnit,
            'producerToken1'       => $this->producerToken1,
            'producerToken2'       => $this->producerToken2,
            'producerToken3'       => $this->producerToken3,
            'altArticleNumber'     => $this->altArticleNumber,
            'producerModel'        => $this->producerModel,
            'connectionNumber'     => $this->connectionNumber,
            'cataloguePage'        => $this->cataloguePage,
            'longDecriptionKey'    => $this->longDecriptionKey,
            'costType'             => $this->costType,
            'articleType'          => $this->articleType,
            'referenceNumber'      => $this->referenceNumber,
            'mwstType'             => $this->mwstType,
            'textkey'              => $this->textkey,
            'producerNumber'       => $this->producerNumber,
        ];
    }

    /**
     * @param string $data
     */
    public function fillByJson(string $data): void
    {
        $obj = json_decode($data);

        $this->workflowState = $obj->workflowState;
        $this->articleNumber = $obj->articleNumber;
        $this->shortDescription1 = $obj->shortDescription1;
        $this->shortDescription2 = $obj->shortDescription2;
        $this->matchcode = $obj->matchcode;
        $this->ean = $obj->ean;
        $this->discountGroup = $obj->discountGroup;
        $this->mainProductGroup = $obj->mainProductGroup;
        $this->productGroup = $obj->productGroup;
        $this->minimumPackageAmount = $obj->minimumPackageAmount;
        $this->priceMark = $obj->priceMark;
        $this->price = (float)$obj->price;
        $this->priceAmount = (int)$obj->priceAmount;
        $this->packingUnit = $obj->packingUnit;
        $this->producerToken1 = $obj->producerToken1;
        $this->producerToken2 = $obj->producerToken2;
        $this->producerToken3 = $obj->producerToken3;
        $this->altArticleNumber = $obj->altArticleNumber;
        $this->producerModel = $obj->producerModel;
        $this->connectionNumber = $obj->connectionNumber;
        $this->cataloguePage = $obj->cataloguePage;
        $this->longDecriptionKey = $obj->longDecriptionKey;
        $this->costType = $obj->costType;
        $this->articleType = $obj->articleType;
        $this->referenceNumber = $obj->referenceNumber;
        $this->mwstType = (int)$obj->mwstType;
        $this->textkey = $obj->textkey;
        $this->producerNumber = $obj->producerNumber;
    }
}
