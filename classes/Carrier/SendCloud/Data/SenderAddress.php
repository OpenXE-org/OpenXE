<?php
namespace Xentral\Carrier\SendCloud\Data;

class SenderAddress {
    public ?string $City;
    public ?string $CompanyName;
    public ?string $ContactName;
    public string $Country;
    public ?string $CountryState;
    public ?string $Email;
    public ?string $HouseNumber;
    public int $Id;
    public ?string $PostalBox;
    public ?string $PostalCode;
    public ?string $Street;
    public ?string $Telephone;
    public ?string $VatNumber;
    public ?string $EoriNumber;
    public int $BrandId;
    public ?string $Label;
    public ?string $SignatureFullName;
    public ?string $SignatureInitials;

    public function __toString(): string
    {
        return "$this->CompanyName; $this->ContactName; $this->Street $this->HouseNumber; $this->PostalCode; $this->City";
    }


    public static function fromApiResponse(object $data): SenderAddress {
        $obj = new SenderAddress();
        $obj->City = $data->city;
        $obj->CompanyName = $data->company_name;
        $obj->ContactName = $data->contact_name;
        $obj->Country = $data->country;
        $obj->CountryState = $data->country_state;
        $obj->Email = $data->email;
        $obj->HouseNumber = $data->house_number;
        $obj->Id = $data->id;
        $obj->PostalBox = $data->postal_box;
        $obj->PostalCode = $data->postal_code;
        $obj->Street = $data->street;
        $obj->Telephone = $data->telephone;
        $obj->VatNumber = $data->vat_number;
        $obj->EoriNumber = $data->eori_number;
        $obj->BrandId = $data->brand_id;
        $obj->Label = $data->label;
        $obj->SignatureFullName = $data->signature_full_name;
        $obj->SignatureInitials = $data->signature_initials;
        return $obj;
    }
}