<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\SendCloud\Data;

use DateTimeImmutable;
use Exception;

class ParcelResponse extends ParcelBase
{
  public int $Id;
  public string $CarrierCode;
  public DateTimeImmutable $DateCreated;
  public DateTimeImmutable $DateUpdated;
  public DateTimeImmutable $DateAnnounced;
  public string $ShipmentMethodName;
  public int $StatusId;
  public string $StatusMessage;
  public array $Documents;
  public ?string $TrackingNumber = null;
  public ?string $TrackingUrl = null;
  public ?array $Errors;

  public function GetDocumentByType(string $type): ?Document
  {
    /** @var Document $item */
    foreach ($this->Documents as $item)
      if ($item->Type == $type)
        return $item;
    return null;
  }

  /**
   * @throws Exception
   */
  public static function fromApiResponse(object $data): ParcelResponse
  {
    $obj = new ParcelResponse();
    $obj->Address = $data->address_divided->street;
    $obj->Address2 = $data->address_2;
    $obj->HouseNumber = $data->address_divided->house_number;
    $obj->CarrierCode = $data->carrier->code;
    $obj->City = $data->city;
    $obj->CompanyName = $data->company_name;
    $obj->Country = $data->country->iso_2;
    $obj->CustomsInvoiceNr = $data->customs_invoice_nr;
    $obj->CustomsShipmentType = $data->customs_shipment_type;
    $obj->DateCreated = new DateTimeImmutable($data->date_created);
    $obj->DateUpdated = new DateTimeImmutable($data->date_updated);
    $obj->DateAnnounced = new DateTimeImmutable($data->date_announced);
    $obj->EMail = $data->email;
    $obj->Id = $data->id;
    $obj->Name = $data->name;
    $obj->OrderNumber = $data->order_number;
    $obj->ParcelItems = array_map(fn($item)=>ParcelItem::fromApiResponse($item), $data->parcel_items);
    $obj->PostalCode = $data->postal_code;
    $obj->ExternalReference = $data->external_reference;
    $obj->ShippingMethodId = $data->shipment->id;
    $obj->ShipmentMethodName = $data->shipment->name;
    $obj->StatusId = $data->status->id;
    $obj->StatusMessage = $data->status->message;
    $obj->Documents = array_map(fn($item)=>Document::fromApiResponse($item), $data->documents);
    $obj->Telephone = $data->telephone;
    $obj->TotalInsuredValue = $data->total_insured_value;
    $obj->TotalOrderValue = $data->total_order_value;
    $obj->TotalOrderValueCurrency = $data->total_order_value_currency;
    $obj->TrackingNumber = $data->tracking_number;
    $obj->TrackingUrl = $data->tracking_url;
    $obj->Weight = $data->weight;
    $obj->Length = $data->length;
    $obj->Height = $data->height;
    $obj->Width = $data->width;
    $obj->IsReturn = $data->is_return;
    $obj->Errors = $data->errors->non_field_errors;
    return $obj;
  }
}