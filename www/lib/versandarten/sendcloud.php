<?php

/*
 * SPDX-FileCopyrightText: 2022-2024 Andreas Palm
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
use Xentral\Modules\ShippingMethod\Model\ShipmentStatus;

use Xentral\Components\Logger\Logger;

require_once dirname(__DIR__) . '/class.versanddienstleister.php';

class Versandart_sendcloud extends Versanddienstleister
{
  protected SendCloudApi $api;
  protected array $options;

  /** @var Logger $logger */
  public Logger $logger;

  public function __construct(ApplicationCore $app, ?int $id)
  {
    parent::__construct($app, $id);
    if (!isset($this->id))
      return;
    $this->api = new SendCloudApi($this->settings->public_key, $this->settings->private_key);
    $this->logger = $app->Container->get('Logger');
  }

  public function GetName(): string
  {
    return "SendCloud";
  }

  protected function FetchOptionsFromApi() : void
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

  protected function CreateShipment(object $json): CreateShipmentResult
  {
    $parcel = new ParcelCreation();
    $parcel->SenderAddressId = $this->settings->sender_address;
    $parcel->ShippingMethodId = $json->productId;
    $parcel->Name = $json->address->name;
    switch ($json->address->addresstype) {
      case 0:     
        $parcel->CompanyName = $json->address->companyName;
        $parcel->Name = join(
                        ';', 
                        array_filter(
                            [
                                $json->address->contactName,
                                $json->address->companyDivision
                            ],
                            fn(string $item) => !empty(trim($item))
                        )
                    );                
        $parcel->Address = $json->address->street;
        $parcel->Address2 = $json->address->address2;
        $parcel->HouseNumber = $json->address->streetnumber;
        break;
      case 1:
        $parcel->CompanyName = $json->address->postnumber;
        $parcel->Address = "Packstation";
        $parcel->HouseNumber = $json->address->parcelstationNumber;
        break;
      case 2:
        $parcel->CompanyName = $json->address->postnumber;
        $parcel->Address = "Postfiliale";
        $parcel->HouseNumber = $json->address->postofficeNumber;
        break;
      case 3:           
        $parcel->Name = join(
                        ';', 
                        array_filter(
                            [
                                $json->address->name,
                                $json->address->contactName
                            ],
                            fn(string $item) => !empty(trim($item))
                        )
                    );
                
        $parcel->Address = $json->address->street;
        $parcel->Address2 = $json->address->address2;
        $parcel->HouseNumber = $json->address->streetnumber;
        break;

    }
    $parcel->Country = $json->address->country;
    $parcel->PostalCode = $json->address->zip;
    $parcel->City = $json->address->city;
    $parcel->EMail = $json->address->email;
    $parcel->Telephone = $json->address->phone;
    $parcel->CountryState = $json->address->state;
    $parcel->TotalInsuredValue = $json->insuranceValue;
    $parcel->OrderNumber = $json->reference;
    if (!$this->app->erp->IsEU($json->address->country)) {
      $parcel->CustomsInvoiceNr = $json->customsDeclaration->invoiceNumber;
      $parcel->CustomsShipmentType = $json->customsDeclaration->shipmentType;
      foreach ($json->customsDeclaration->positions as $pos) {
        $item = new ParcelItem();
        $item->HsCode = $pos->hsCode ?? '';
        $item->Description = $pos->description;
        $item->Quantity = $pos->quantity;
        $item->OriginCountry = $pos->originCountryCode ?? '';
        $item->Price = $pos->itemValue;
        $item->Weight = $pos->itemWeight * 1000;
        $parcel->ParcelItems[] = $item;
      }
    }
    $parcel->Weight = floatval($json->package->weight) * 1000;
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

  protected function GetShippingProducts(): array
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

    public function GetShipmentStatus(string $tracking): ShipmentStatus|null
    {
        $this->logger->debug("Sendcloud tracking status request ".$this->id,
            [
                'trackingCode' => $tracking
            ]
        );
        try {
            $result = $this->api->GetTrackingStatus($tracking);
            $this->logger->debug("Sendcloud tracking status result ".$this->id,
                [
                    'result' => $result
                ]
            );
            return ($result);
        } catch (SendcloudApiException $e) {
            $this->logger->debug("Sendcloud tracking status error ".$this->id,
                [
                    'exception' => $e
                ]
            );
            return null;
        }
    }
}
