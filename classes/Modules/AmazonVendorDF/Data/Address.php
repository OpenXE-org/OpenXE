<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

class Address
{
    /** @var string */
    private $name;
    /** @var array */
    private $addressLines;
    /** @var string */
    private $city;
    /** @var string */
    private $countryCode;
    /** @var string */
    private $postalCode;
    /** @var string */
    private $stateOrRegion;
    /** @var string */
    private $phone;

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setAddressLines(array $addressLines): self
    {
        $this->addressLines = $addressLines;

        return $this;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }


    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function setStateOrRegion(string $stateOrRegion): self
    {
        $this->stateOrRegion = $stateOrRegion;

        return $this;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddressLines(): array
    {
        return $this->addressLines;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getStateOrRegion(): string
    {
        return $this->stateOrRegion;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function toArray(): array
    {
        return array_filter([
            'name'          => $this->name,
            'addressLine1'  => $this->addressLines[0],
            'addressLine2'  => $this->addressLines[1],
            'addressLine3'  => $this->addressLines[2],
            'city'          => $this->city,
            'stateOrRegion' => $this->stateOrRegion,
            'postalCode'    => $this->postalCode,
            'countryCode'   => $this->countryCode,
            'phone'         => $this->phone,
        ]);
    }
}
