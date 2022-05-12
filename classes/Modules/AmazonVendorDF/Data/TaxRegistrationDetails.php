<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

class TaxRegistrationDetails
{
    /** @var string */
    private $taxRegistrationType;
    /** @var string */
    private $taxRegistrationNumber;
    /** @var Address */
    private $taxRegistrationAddress;

    public function getTaxRegistrationType(): string
    {
        return $this->taxRegistrationType;
    }

    public function setTaxRegistrationType(string $taxRegistrationType): self
    {
        $this->taxRegistrationType = $taxRegistrationType;

        return $this;
    }

    public function getTaxRegistrationNumber(): string
    {
        return $this->taxRegistrationNumber;
    }

    public function setTaxRegistrationNumber(string $taxRegistrationNumber): self
    {
        $this->taxRegistrationNumber = $taxRegistrationNumber;

        return $this;
    }

    public function getTaxRegistrationAddress(): Address
    {
        return $this->taxRegistrationAddress;
    }

    public function setTaxRegistrationAddress(Address $taxRegistrationAddress): self
    {
        $this->taxRegistrationAddress = $taxRegistrationAddress;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'taxRegistrationType'    => $this->taxRegistrationType,
            'taxRegistrationNumber'  => $this->taxRegistrationNumber,
            'taxRegistrationAddress' => $this->taxRegistrationAddress->toArray(),
        ];
    }
}
