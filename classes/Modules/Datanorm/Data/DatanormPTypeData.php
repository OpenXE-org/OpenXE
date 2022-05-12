<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Data;

final class DatanormPTypeData extends AbstractDatanormTypeData implements DatanormTypeDataInterface
{
    /** @var  string $discountGroup */
    protected $discountGroup = ''; //nur V5

    /** @var  string $articleNumber1 */
    protected $articleNumber1 = '';

    /** @var  string $priceMark1 */
    protected $priceMark1 = '';

    /** @var  int $priceAmount1 */
    protected $priceAmount1 = 0;

    /** @var  float $price1 */
    protected $price1 = 0.0;

    /** @var  string $discountKey1a */
    protected $discountKey1a = '';

    /** @var  float $discount1a */
    protected $discount1a = '';

    /** @var  string $discountGroup */
    protected $discountKey1b = '';

    /** @var  float $discount1b */
    protected $discount1b = '';

    /** @var  string $discountKey1c */
    protected $discountKey1c = '';

    /** @var  float $discount1c */
    protected $discount1c = '';

    /** @var  string $validFromDate */
    protected $validFromDate = '';

    /** @var  string $articleNumber2 */
    protected $articleNumber2 = '';

    /** @var  string $priceMark2 */
    protected $priceMark2 = '';

    /** @var  int $priceAmount2 */
    protected $priceAmount2 = 0;

    /** @var  float $price2 */
    protected $price2 = 0.0;

    /** @var  string $discountKey2a */
    protected $discountKey2a = '';

    /** @var  float $discount2a */
    protected $discount2a = '';

    /** @var  string $discountKey2b */
    protected $discountKey2b = '';

    /** @var  float $discount2b */
    protected $discount2b = '';

    /** @var  string $discountKey2c */
    protected $discountKey2c = '';

    /** @var  float $discount2c */
    protected $discount2c = '';

    /** @var  string $articleNumber3 */
    protected $articleNumber3 = '';

    /** @var  string $discountGroup */
    protected $priceMark3 = '';

    /** @var  int $priceAmount3 */
    protected $priceAmount3 = 0;

    /** @var  float $price3 */
    protected $price3 = 0.0;

    /** @var  string $discountKey3a */
    protected $discountKey3a = '';

    /** @var  float $discount3a */
    protected $discount3a = '';

    /** @var  string $discountKey3b */
    protected $discountKey3b = '';

    /** @var  float $discount3b */
    protected $discount3b = '';

    /** @var  string $discountKey3c */
    protected $discountKey3c = '';

    /** @var  float $discount3c */
    protected $discount3c = '';

    /**
     * @return string
     */
    public function getDiscountGroup(): string
    {
        return $this->discountGroup;
    }

    /**
     * @param string $discountGroup
     */
    public function setDiscountGroup(string $discountGroup): void
    {
        $this->discountGroup = $discountGroup;
    }

    /**
     * @return string
     */
    public function getArticleNumber1(): string
    {
        return $this->articleNumber1;
    }

    /**
     * @param string $articleNumber1
     */
    public function setArticleNumber1(string $articleNumber1): void
    {
        $this->articleNumber1 = $articleNumber1;
    }

    /**
     * @return string
     */
    public function getPriceMark1(): string
    {
        return $this->priceMark1;
    }

    /**
     * @param string $priceMark1
     */
    public function setPriceMark1(string $priceMark1): void
    {
        $this->priceMark1 = $priceMark1;
    }

    /**
     * @return int
     */
    public function getPriceAmount1(): int
    {
        return $this->priceAmount1;
    }

    /**
     * @param int $priceAmount1
     */
    public function setPriceAmount1(int $priceAmount1): void
    {
        $this->priceAmount1 = $priceAmount1;
    }

    /**
     * @return float
     */
    public function getPrice1(): float
    {
        return $this->price1;
    }

    /**
     * @param float $price1
     */
    public function setPrice1(float $price1): void
    {
        $this->price1 = $price1;
    }

    /**
     * @return string
     */
    public function getDiscountKey1a(): string
    {
        return $this->discountKey1a;
    }

    /**
     * @param string $discountKey1a
     */
    public function setDiscountKey1a(string $discountKey1a): void
    {
        $this->discountKey1a = $discountKey1a;
    }

    /**
     * @return float
     */
    public function getDiscount1a(): float
    {
        return $this->discount1a;
    }

    /**
     * @param float $discount1a
     */
    public function setDiscount1a(float $discount1a): void
    {
        $this->discount1a = $discount1a;
    }

    /**
     * @return string
     */
    public function getDiscountKey1b(): string
    {
        return $this->discountKey1b;
    }

    /**
     * @param string $discountKey1b
     */
    public function setDiscountKey1b(string $discountKey1b): void
    {
        $this->discountKey1b = $discountKey1b;
    }

    /**
     * @return float
     */
    public function getDiscount1b(): float
    {
        return $this->discount1b;
    }

    /**
     * @param float $discount1b
     */
    public function setDiscount1b(float $discount1b): void
    {
        $this->discount1b = $discount1b;
    }

    /**
     * @return string
     */
    public function getDiscountKey1c(): string
    {
        return $this->discountKey1c;
    }

    /**
     * @param string $discountKey1c
     */
    public function setDiscountKey1c(string $discountKey1c): void
    {
        $this->discountKey1c = $discountKey1c;
    }

    /**
     * @return float
     */
    public function getDiscount1c(): float
    {
        return $this->discount1c;
    }

    /**
     * @param float $discount1c
     */
    public function setDiscount1c(float $discount1c): void
    {
        $this->discount1c = $discount1c;
    }

    /**
     * @return string
     */
    public function getValidFromDate(): string
    {
        return $this->validFromDate;
    }

    /**
     * @param string $validFromDate
     */
    public function setValidFromDate(string $validFromDate): void
    {
        $this->validFromDate = $validFromDate;
    }

    /**
     * @return string
     */
    public function getArticleNumber2(): string
    {
        return $this->articleNumber2;
    }

    /**
     * @param string $articleNumber2
     */
    public function setArticleNumber2(string $articleNumber2): void
    {
        $this->articleNumber2 = $articleNumber2;
    }

    /**
     * @return string
     */
    public function getPriceMark2(): string
    {
        return $this->priceMark2;
    }

    /**
     * @param string $priceMark2
     */
    public function setPriceMark2(string $priceMark2): void
    {
        $this->priceMark2 = $priceMark2;
    }

    /**
     * @return int
     */
    public function getPriceAmount2(): int
    {
        return $this->priceAmount2;
    }

    /**
     * @param int $priceAmount2
     */
    public function setPriceAmount2(int $priceAmount2): void
    {
        $this->priceAmount2 = $priceAmount2;
    }

    /**
     * @return float
     */
    public function getPrice2(): float
    {
        return $this->price2;
    }

    /**
     * @param float $price2
     */
    public function setPrice2(float $price2): void
    {
        $this->price2 = $price2;
    }

    /**
     * @return string
     */
    public function getDiscountKey2a(): string
    {
        return $this->discountKey2a;
    }

    /**
     * @param string $discountKey2a
     */
    public function setDiscountKey2a(string $discountKey2a): void
    {
        $this->discountKey2a = $discountKey2a;
    }

    /**
     * @return float
     */
    public function getDiscount2a(): float
    {
        return $this->discount2a;
    }

    /**
     * @param float $discount2a
     */
    public function setDiscount2a(float $discount2a): void
    {
        $this->discount2a = $discount2a;
    }

    /**
     * @return string
     */
    public function getDiscountKey2b(): string
    {
        return $this->discountKey2b;
    }

    /**
     * @param string $discountKey2b
     */
    public function setDiscountKey2b(string $discountKey2b): void
    {
        $this->discountKey2b = $discountKey2b;
    }

    /**
     * @return float
     */
    public function getDiscount2b(): float
    {
        return $this->discount2b;
    }

    /**
     * @param float $discount2b
     */
    public function setDiscount2b(float $discount2b): void
    {
        $this->discount2b = $discount2b;
    }

    /**
     * @return string
     */
    public function getDiscountKey2c(): string
    {
        return $this->discountKey2c;
    }

    /**
     * @param string $discountKey2c
     */
    public function setDiscountKey2c(string $discountKey2c): void
    {
        $this->discountKey2c = $discountKey2c;
    }

    /**
     * @return float
     */
    public function getDiscount2c(): float
    {
        return $this->discount2c;
    }

    /**
     * @param float $discount2c
     */
    public function setDiscount2c(float $discount2c): void
    {
        $this->discount2c = $discount2c;
    }

    /**
     * @return string
     */
    public function getArticleNumber3(): string
    {
        return $this->articleNumber3;
    }

    /**
     * @param string $articleNumber3
     */
    public function setArticleNumber3(string $articleNumber3): void
    {
        $this->articleNumber3 = $articleNumber3;
    }

    /**
     * @return string
     */
    public function getPriceMark3(): string
    {
        return $this->priceMark3;
    }

    /**
     * @param string $priceMark3
     */
    public function setPriceMark3(string $priceMark3): void
    {
        $this->priceMark3 = $priceMark3;
    }

    /**
     * @return int
     */
    public function getPriceAmount3(): int
    {
        return $this->priceAmount3;
    }

    /**
     * @param int $priceAmount3
     */
    public function setPriceAmount3(int $priceAmount3): void
    {
        $this->priceAmount3 = $priceAmount3;
    }

    /**
     * @return float
     */
    public function getPrice3(): float
    {
        return $this->price3;
    }

    /**
     * @param float $price3
     */
    public function setPrice3(float $price3): void
    {
        $this->price3 = $price3;
    }

    /**
     * @return string
     */
    public function getDiscountKey3a(): string
    {
        return $this->discountKey3a;
    }

    /**
     * @param string $discountKey3a
     */
    public function setDiscountKey3a(string $discountKey3a): void
    {
        $this->discountKey3a = $discountKey3a;
    }

    /**
     * @return float
     */
    public function getDiscount3a(): float
    {
        return $this->discount3a;
    }

    /**
     * @param float $discount3a
     */
    public function setDiscount3a(float $discount3a): void
    {
        $this->discount3a = $discount3a;
    }

    /**
     * @return string
     */
    public function getDiscountKey3b(): string
    {
        return $this->discountKey3b;
    }

    /**
     * @param string $discountKey3b
     */
    public function setDiscountKey3b(string $discountKey3b): void
    {
        $this->discountKey3b = $discountKey3b;
    }

    /**
     * @return float
     */
    public function getDiscount3b(): float
    {
        return $this->discount3b;
    }

    /**
     * @param float $discount3b
     */
    public function setDiscount3b(float $discount3b): void
    {
        $this->discount3b = $discount3b;
    }

    /**
     * @return string
     */
    public function getDiscountKey3c(): string
    {
        return $this->discountKey3c;
    }

    /**
     * @param string $discountKey3c
     */
    public function setDiscountKey3c(string $discountKey3c): void
    {
        $this->discountKey3c = $discountKey3c;
    }

    /**
     * @return float
     */
    public function getDiscount3c(): float
    {
        return $this->discount3c;
    }

    /**
     * @param float $discount3c
     */
    public function setDiscount3c(float $discount3c): void
    {
        $this->discount3c = $discount3c;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'discountGroup'  => $this->discountGroup,
            'articleNumber1' => $this->articleNumber1,
            'priceMark1'     => $this->priceMark1,
            'priceAmount1'   => $this->priceAmount1,
            'price1'         => $this->price1,
            'discountKey1a'  => $this->discountKey1a,
            'discount1a'     => $this->discount1a,
            'discountKey1b'  => $this->discountKey1b,
            'discount1b'     => $this->discount1b,
            'discountKey1c'  => $this->discountKey1c,
            'discount1c'     => $this->discount1c,
            'validFromDate'  => $this->validFromDate,
            'articleNumber2' => $this->articleNumber2,
            'priceMark2'     => $this->priceMark2,
            'priceAmount2'   => $this->priceAmount2,
            'price2'         => $this->price2,
            'discountKey2a'  => $this->discountKey2a,
            'discount2a'     => $this->discount2a,
            'discountKey2b'  => $this->discountKey2b,
            'discount2b'     => $this->discount2b,
            'discountKey2c'  => $this->discountKey2c,
            'discount2c'     => $this->discount2c,
            'articleNumber3' => $this->articleNumber3,
            'priceMark3'     => $this->priceMark3,
            'priceAmount3'   => $this->priceAmount3,
            'price3'         => $this->price3,
            'discountKey3a'  => $this->discountKey3a,
            'discount3a'     => $this->discount3a,
            'discountKey3b'  => $this->discountKey3b,
            'discount3b'     => $this->discount3b,
            'discountKey3c'  => $this->discountKey3c,
            'discount3c'     => $this->discount3c,
        ];
    }

    /**
     * @param string $data
     */
    public function fillByJson(string $data): void
    {
        $obj = json_decode($data);

        $this->discountGroup = $obj->discountGroup;
        $this->articleNumber1 = $obj->articleNumber1;
        $this->priceMark1 = $obj->priceMark1;
        $this->priceAmount1 = (int)$obj->priceAmount1;
        $this->price1 = (float)$obj->price1;
        $this->discountKey1a = $obj->discountKey1a;
        $this->discount1a = (float)$obj->discount1a;
        $this->discountKey1b = $obj->discountKey1b;
        $this->discount1b = (float)$obj->discount1b;
        $this->discountKey1c = $obj->discountKey1c;
        $this->discount1c = (float)$obj->discount1c;
        $this->validFromDate = $obj->validFromDate;
        $this->articleNumber2 = $obj->articleNumber2;
        $this->priceMark2 = $obj->priceMark2;
        $this->priceAmount2 = (int)$obj->priceAmount2;
        $this->price2 = (float)$obj->price2;
        $this->discountKey2a = $obj->discountKey2a;
        $this->discount2a = (float)$obj->discount2a;
        $this->discountKey2b = $obj->discountKey2b;
        $this->discount2b = (float)$obj->discount2b;
        $this->discountKey2c = $obj->discountKey2c;
        $this->discount2c = (float)$obj->discount2c;
        $this->articleNumber3 = $obj->articleNumber3;
        $this->priceMark3 = $obj->priceMark3;
        $this->priceAmount3 = (int)$obj->priceAmount3;
        $this->price3 = (float)$obj->price3;
        $this->discountKey3a = $obj->discountKey3a;
        $this->discount3a = (float)$obj->discount3a;
        $this->discountKey3b = $obj->discountKey3b;
        $this->discount3b = (float)$obj->discount3b;
        $this->discountKey3c = $obj->discountKey3c;
        $this->discount3c = (float)$obj->discount3c;
    }
}
