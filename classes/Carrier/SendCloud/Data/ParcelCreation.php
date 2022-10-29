<?php

namespace Xentral\Carrier\SendCloud\Data;

class ParcelCreation extends ParcelBase
{
  public ?int $SenderAddressId = null;

  public function toApiRequest(): array {
    return [
        'name' => $this->Name,
        'company_name' => $this->CompanyName,
        'address' => $this->Address,
        'address_2' => $this->Address2,
        'house_number' => $this->HouseNumber,
        'city' => $this->City,
        'postal_code' => $this->PostalCode,
        'telephone' => $this->Telephone,
        'request_label' => $this->RequestLabel,
        'email' => $this->EMail,
        'country' => $this->Country,
        'shipment' => ['id' => $this->ShippingMethodId],
        'weight' => number_format($this->Weight / 1000, 3, '.', null),
        'order_number' => $this->OrderNumber,
        'total_order_value_currency' => $this->TotalOrderValueCurrency,
        'total_order_value' => number_format($this->TotalOrderValue, 2, '.', null),
        'country_state' => $this->CountryState,
        'sender_address' => $this->SenderAddressId,
        'customs_invoice_nr' => $this->CustomsInvoiceNr,
        'customs_shipment_type' => $this->CustomsShipmentType,
        'external_reference' => $this->ExternalReference,
        'total_insured_value' => $this->TotalInsuredValue ?? 0,
        'parcel_items' => array_map(fn(ParcelItem $item)=>$item->toApiRequest(), $this->ParcelItems),
        'is_return' => $this->IsReturn,
        'length' => $this->Length,
        'width' => $this->Width,
        'height' => $this->Height,
    ];
  }

}