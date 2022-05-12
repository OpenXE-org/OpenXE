<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Data;

final class DatanormVTypeData extends AbstractDatanormTypeData implements DatanormTypeDataInterface
{

    /** @var string $dataMark */
    protected $dataMark = '';

    /** @var string $date */
    protected $date = '';

    /** @var string $currency */
    protected $currency = '';

    /** @var string $description */
    protected $description = '';

    /** @var string $producerToken */
    protected $producerToken = '';

    /** @var string $adress1 */
    protected $adress1 = '';

    /** @var string $adress2 */
    protected $adress2 = '';

    /** @var string $adress3 */
    protected $adress3 = '';

    /** @var string $street */
    protected $street = '';

    /** @var string $countryId */
    protected $countryId = '';

    /** @var string $zip */
    protected $zip = '';

    /** @var string $city */
    protected $city = '';

    /** @var int $userAddressId */
    protected $userAddressId;

    /** @var int $supplierAddressId */
    protected $supplierAddressId;

    /**
     * @return string
     */
    public function getDataMark(): string
    {
        return $this->dataMark;
    }

    /**
     * @return string
     */
    public function getAdress3(): string
    {
        return $this->adress3;
    }

    /**
     * @return string
     */
    public function getProducerToken(): string
    {
        return $this->producerToken;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getAdress1(): string
    {
        return $this->adress1;
    }

    /**
     * @return string
     */
    public function getAdress2(): string
    {
        return $this->adress2;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getCountryId(): string
    {
        return $this->countryId;
    }

    /**
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return int
     */
    public function getUserAddressId(): int
    {
        return $this->userAddressId;
    }

    /**
     * @return int
     */
    public function getSupplierAddressId(): int
    {
        return $this->supplierAddressId;
    }

    /**
     * @param string $dataMark
     */
    public function setDataMark(string $dataMark): void
    {
        $this->dataMark = $dataMark;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param string $adress1
     */
    public function setAdress1(string $adress1): void
    {
        $this->adress1 = $adress1;
    }

    /**
     * @param string $adress2
     */
    public function setAdress2(string $adress2): void
    {
        $this->adress2 = $adress2;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * @param string $countryId
     */
    public function setCountryId(string $countryId): void
    {
        $this->countryId = $countryId;
    }

    /**
     * @param string $zip
     */
    public function setZip(string $zip): void
    {
        $this->zip = $zip;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @param string $producerToken
     */
    public function setProducerToken(string $producerToken): void
    {
        $this->producerToken = $producerToken;
    }

    /**
     * @param string $adress3
     */
    public function setAdress3(string $adress3): void
    {
        $this->adress3 = $adress3;
    }

    /**
     * @param int $userAddressId
     */
    public function setUserAddressId(int $userAddressId): void
    {
        $this->userAddressId = $userAddressId;
    }

    /**
     * @param int $supplierAddressId
     */
    public function setSupplierAddressId(int $supplierAddressId): void
    {
        $this->supplierAddressId = $supplierAddressId;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'dataMark'      => $this->dataMark,
            'date'          => $this->date,
            'currency'      => $this->currency,
            'description'   => $this->description,
            'adress1'       => $this->adress1,
            'adress2'       => $this->adress2,
            'adress3'       => $this->adress3,
            'street'        => $this->street,
            'countryId'     => $this->countryId,
            'zip'           => $this->zip,
            'city'          => $this->city,
            'producerToken' => $this->producerToken,
        ];
    }

    /**
     * @param string $data
     */
    public function fillByJson(string $data): void
    {
        $obj = json_decode($data);

        $this->dataMark = $obj->dataMark;
        $this->date = $obj->date;
        $this->currency = $obj->currency;
        $this->description = $obj->description;
        $this->adress1 = $obj->adress1;
        $this->adress2 = $obj->adress2;
        $this->adress3 = $obj->adress3;
        $this->street = $obj->street;
        $this->countryId = $obj->countryId;
        $this->zip = $obj->zip;
        $this->city = $obj->city;
        $this->producerToken = $obj->producerToken;
    }
}
