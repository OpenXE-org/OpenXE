<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

use Xentral\Carrier\SendCloud\Data\Document;
use Xentral\Carrier\SendCloud\Data\ParcelCreation;
use Xentral\Carrier\SendCloud\Data\ParcelItem;
use Xentral\Carrier\SendCloud\Data\ParcelResponse;
use Xentral\Carrier\SendCloud\SendCloudApi;
use Xentral\Carrier\SendCloud\Data\SenderAddress;
use Xentral\Carrier\SendCloud\Data\ShippingProduct;
use Xentral\Carrier\SendCloud\Data\ShippingMethod;
use Xentral\Carrier\SendCloud\SendcloudApiException;
use Xentral\Modules\ShippingMethod\Model\CreateShipmentResult;
use Xentral\Modules\ShippingMethod\Model\Product;

require_once dirname(__DIR__) . '/class.versanddienstleister.php';

class Versandart_sendcloud extends Versanddienstleister
{
  protected SendCloudApi $api;
  protected array $options;

  public function __construct(ApplicationCore $app, ?int $id)
  {
    parent::__construct($app, $id);
    if (!isset($this->id))
      return;
    $this->api = new SendCloudApi($this->settings->public_key, $this->settings->private_key);
  }

  public function GetName(): string
  {
    return "SendCloud";
  }

  protected function FetchOptionsFromApi()
  {
    if (isset($this->options))
      return;
    try {
      $list = $this->api->GetSenderAddresses();
      foreach ($list as $item) {
        /* @var SenderAddress $item */
        $senderAddresses[$item->Id] = $item;
      }
      $senderCountry = $senderAddresses[$this->settings->sender_address]->Country ?? 'DE';
      $list = $this->api->GetShippingProducts($senderCountry);
      foreach ($list as $item) {
        /* @var ShippingProduct $item */
        $shippingProducts[$item->Code] = $item;
      }
    } catch (SendcloudApiException $e) {
      $this->app->Tpl->addMessage('error', $e->getMessage());
    }
    $this->options['senders'] = array_map(fn(SenderAddress $x) => strval($x), $senderAddresses ?? []);
    $this->options['products'] = array_map(fn(ShippingProduct $x) => $x->Name, $shippingProducts ?? []);
    $this->options['products'][0] = '';
    $this->options['selectedProduct'] = $shippingProducts[$this->settings->shipping_product] ?? [];
    natcasesort($this->options['products']);
  }

  public function AdditionalSettings(): array
  {
    $this->FetchOptionsFromApi();
    return [
        'public_key' => ['typ' => 'text', 'bezeichnung' => 'API Public Key:'],
        'private_key' => ['typ' => 'text', 'bezeichnung' => 'API Private Key:'],
        'sender_address' => ['typ' => 'select', 'bezeichnung' => 'Absender-Adresse:', 'optionen' => $this->options['senders']],
        'shipping_product' => ['typ' => 'select', 'bezeichnung' => 'Versand-Produkt:', 'optionen' => $this->options['products']],
    ];
  }

  public function CreateShipment(object $json, array $address): CreateShipmentResult
  {
    $parcel = new ParcelCreation();
    $parcel->SenderAddressId = $this->settings->sender_address;
    $parcel->ShippingMethodId = $json->product;
    $parcel->Name = $json->name;
    switch ($json->addresstype) {
      case 0:     
        $parcel->CompanyName = $json->company_name;                
        $parcel->Name = join(
                        ';', 
                        array_filter(
                            [
                                $json->contact_name,
                                $json->company_division
                            ],
                            fn(string $item) => !empty(trim($item))
                        )
                    );                
        $parcel->Address = $json->street;
        $parcel->Address2 = $json->address2;
        $parcel->HouseNumber = $json->streetnumber;
        break;
      case 1:
        $parcel->CompanyName = $json->postnumber;
        $parcel->Address = "Packstation";
        $parcel->HouseNumber = $json->parcelstationNumber;
        break;
      case 2:
        $parcel->CompanyName = $json->postnumber;
        $parcel->Address = "Postfiliale";
        $parcel->HouseNumber = $json->postofficeNumber;
        break;
      case 3:           
        $parcel->Name = join(
                        ';', 
                        array_filter(
                            [
                                $json->name,
                                $json->contact_name
                            ],
                            fn(string $item) => !empty(trim($item))
                        )
                    );
                
        $parcel->Address = $json->street;
        $parcel->Address2 = $json->address2;
        $parcel->HouseNumber = $json->streetnumber;
        break;

    }
    $parcel->Country = $json->country;
    $parcel->PostalCode = $json->zip;
    $parcel->City = $json->city;
    $parcel->EMail = $json->email;
    $parcel->Telephone = $json->phone;
    $parcel->CountryState = $json->state;
    $parcel->TotalInsuredValue = $json->total_insured_value;
    $parcel->OrderNumber = $json->order_number;
    if (!$this->app->erp->IsEU($json->country)) {
      $parcel->CustomsInvoiceNr = $json->invoice_number;
      $parcel->CustomsShipmentType = $json->shipment_type;
      foreach ($json->positions as $pos) {
        $item = new ParcelItem();
        $item->HsCode = $pos->zolltarifnummer ?? '';
        $item->Description = $pos->bezeichnung;
        $item->Quantity = $pos->menge;
        $item->OriginCountry = $pos->herkunftsland ?? '';
        $item->Price = $pos->zolleinzelwert;
        $item->Weight = $pos->zolleinzelgewicht * 1000;
        $parcel->ParcelItems[] = $item;
      }
    }
    $parcel->Weight = floatval($json->weight) * 1000;
    $ret = new CreateShipmentResult();
    try {
      $result = $this->api->CreateParcel($parcel);
      if ($result instanceof ParcelResponse) {
        $ret->Success = true;
        $ret->TrackingNumber = $result->TrackingNumber;
        $ret->TrackingUrl = $result->TrackingUrl;

        $doc = $result->GetDocumentByType(Document::TYPE_LABEL);
        $ret->Label = $this->api->DownloadDocument($doc);

        $doc = $result->GetDocumentByType(Document::TYPE_CN23);
        if ($doc)
          $ret->ExportDocuments = $this->api->DownloadDocument($doc);
      } else {
        $ret->Errors[] = $result;
      }
    } catch (SendcloudApiException $e) {
      $ret->Errors[] = strval($e);
    }
    return $ret;
  }

  public function GetShippingProducts(): array
  {
    $this->FetchOptionsFromApi();
    /** @var ShippingProduct $product */
    $product = $this->options['selectedProduct'];
    $result = [];
    /** @var ShippingMethod $item */
    foreach ($product->ShippingMethods as $item) {
      $p = new Product();
      $p->Id = $item->Id;
      $p->Name = $item->Name;
      $p->WeightMin = $item->MinWeight / 1000;
      $p->WeightMax = $item->MaxWeight / 1000;
      $result[] = $p;
    }
    return $result;
  }


}
