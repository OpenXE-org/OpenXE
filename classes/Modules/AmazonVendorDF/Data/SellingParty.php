<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

class SellingParty
{
    /** @var string */
    private $partyId;

    /** @var Address */
    private $address;

    /** @var TaxRegistrationDetails */
    private $taxRegistrationDetails;

    public function __construct(string $partyId)
    {
        $this->partyId = $partyId;
    }

    public function getPartyId(): string
    {
        return $this->partyId;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getTaxRegistrationDetails(): TaxRegistrationDetails
    {
        return $this->taxRegistrationDetails;
    }

    public function setTaxRegistrationDetails(TaxRegistrationDetails $taxRegistrationDetails): self
    {
        $this->taxRegistrationDetails = $taxRegistrationDetails;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'partyId' => $this->partyId,
            'address' => $this->address->toArray(),
            'taxRegistrationDetails' => $this->taxRegistrationDetails->toArray()
        ];
    }
}
