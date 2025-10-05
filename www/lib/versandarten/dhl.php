<?php

/*
 * SPDX-FileCopyrightText: 2022-2025 Andreas Palm
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

use Xentral\Carrier\Dhl\Data\Communication;
use Xentral\Carrier\Dhl\Data\Country;
use Xentral\Carrier\Dhl\Data\CreateShipmentOrderResponse;
use Xentral\Carrier\Dhl\Data\PackStation;
use Xentral\Carrier\Dhl\Data\Postfiliale;
use Xentral\Carrier\Dhl\Data\ReceiverNativeAddress;
use Xentral\Carrier\Dhl\Data\Shipment;
use Xentral\Carrier\Dhl\Data\ShipmentItem;
use Xentral\Carrier\Dhl\DhlApi;
use Xentral\Modules\ShippingMethod\Model\CreateShipmentResult;
use Xentral\Modules\ShippingMethod\Model\Product;
use Xentral\Modules\ShippingMethod\Model\Service;
use Xentral\Modules\ShippingMethod\Model\ShipmentStatus;

require_once(dirname(__DIR__).'/class.versanddienstleister.php');
class Versandart_dhl extends Versanddienstleister{

  public function GetName():string
  {
    return 'DHL';
  }

  public function AdditionalSettings(): array
  {
    return [
        'user' => array('typ' => 'text', 'bezeichnung' => 'Benutzer:', 'info' => 'geschaeftskunden_api (Versenden/Intraship-Benutzername)'),
        'signature' => array('typ' => 'text', 'bezeichnung' => 'Signature:', 'info' => 'Dhl_ep_test1 (Versenden/IntrashipPasswort)'),
        'ekp' => array('typ' => 'text', 'bezeichnung' => 'EKP', 'info' => '5000000000 (gültige DHL Kundennummer)'),
        'accountnumber' => array('typ' => 'text', 'bezeichnung' => 'Abrechnungsnummer Paket:'),
        'accountnumber_int' => array('typ' => 'text', 'bezeichnung' => 'Abrechnungsnummer Paket International:'),
        'accountnumber_euro' => array('typ' => 'text', 'bezeichnung' => 'Abrechnungsnummer Europaket:'),
        'accountnumber_connect' => array('typ' => 'text', 'bezeichnung' => 'Abrechnungsnummer Paket Connect:'),
        'accountnumber_wp' => array('typ' => 'text', 'bezeichnung' => 'Abrechnungsnummer Warenpost:'),
        'accountnumber_wpint' => array('typ' => 'text', 'bezeichnung' => 'Abrechnungsnummer Warenpost International:'),
//        'intraship_retourenaccount' => array('typ' => 'text', 'bezeichnung' => 'Retouren Account:', 'info' => '14 Stellige DHL-Retoure Abrechnungsnummer'),
//        'intraship_retourenlabel' => array('typ' => 'checkbox', 'bezeichnung' => 'Vorauswahl Retourenlabel:', 'info' => 'Druckt Retourenlabel mit'),

        'sender_name1' => array('typ' => 'text', 'bezeichnung' => 'Versender Firma:'),
        'sender_street' => array('typ' => 'text', 'bezeichnung' => 'Versender Strasse:'),
        'sender_streetnumber' => array('typ' => 'text', 'bezeichnung' => 'Versender Strasse Nr.:'),
        'sender_zip' => array('typ' => 'text', 'bezeichnung' => 'Versender PLZ:'),
        'sender_city' => array('typ' => 'text', 'bezeichnung' => 'Versender Stadt:'),
        'sender_country' => array('typ' => 'text', 'bezeichnung' => 'Versender ISO Code:', 'info' => 'DE'),
        'sender_email' => array('typ' => 'text', 'bezeichnung' => 'Versender E-Mail:'),
        'sender_phone' => array('typ' => 'text', 'bezeichnung' => 'Versender Telefon:'),
        'sender_web' => array('typ' => 'text', 'bezeichnung' => 'Versender Web:'),
        'sender_contact_person' => array('typ' => 'text', 'bezeichnung' => 'Versender Ansprechpartner:'),

        'cod_account_owner' => array('typ' => 'text', 'bezeichnung' => 'Nachnahme Kontoinhaber:'),
        'cod_bank_name' => array('typ' => 'text', 'bezeichnung' => 'Nachnahme Bank Name:'),
        'cod_account_iban' => array('typ' => 'text', 'bezeichnung' => 'Nachnahme IBAN:'),
        'cod_account_bic' => array('typ' => 'text', 'bezeichnung' => 'Nachnahme BIC:'),
        'cod_extra_fee' => array('typ' => 'text', 'bezeichnung' => 'Nachnahme Gebühr:', 'info' => 'z.B. 2,00 wird auf Rechnungsbetrag addiert, da DHL dies als extra Gebühr für sich behält'),

        'weight' => array('typ' => 'text', 'bezeichnung' => 'Standard Gewicht:', 'info' => 'in KG'),
        'length' => array('typ' => 'text', 'bezeichnung' => 'Standard Länge:', 'info' => 'in cm'),
        'width' => array('typ' => 'text', 'bezeichnung' => 'Standard Breite:', 'info' => 'in cm'),
        'height' => array('typ' => 'text', 'bezeichnung' => 'Standard Höhe:', 'info' => 'in cm'),

        'product' => array('typ' => 'text', 'bezeichnung' => 'Standard Produkt:', 'info' => 'z.B. in DE: V01PAK oder AT: V86PARCEL'),
        'use_premium' => array('typ' => 'checkbox', 'bezeichnung' => 'Premiumversand verwenden:'),
/*
        'intraship_vorausverfuegung' => array('typ' => 'select', 'bezeichnung' => 'Vorausverf&uuml;gung: ', 'optionen' => array('-' => 'keine Vorausverf&uuml;gung', 'IMMEDIATE' => 'Sofortige R&uuml;cksendung an den Absender', 'AFTER_DEADLINE' => 'R&uuml;cksenden an den Absender nach Ablauf der Frist', 'ABANDONMENT' => 'Preisgabe des Pakets durch den Absender (entgeltfrei)')),

        'sperrgut' => array('typ' => 'checkbox', 'bezeichnung' => 'Sperrgut:'),
        'keineversicherung' => array('typ' => 'checkbox', 'bezeichnung' => 'Extra Versicherung ausschalten:', 'info' => 'Option muss von Hand im Paketmarkendialog gesetzt werden.'),
        'leitcodierung' => array('typ' => 'checkbox', 'bezeichnung' => 'Leitcodierung aktivieren:'),
        'use_shipping_article_from_order_on_export' => ['typ' => 'checkbox', 'bezeichnung' => 'Bei Export Porto aus Auftrag senden:'],
        'autotracking' => array('typ' => 'checkbox', 'bezeichnung' => 'Tracking übernehmen:'),
        'log' => array('typ' => 'checkbox', 'bezeichnung' => 'Logging')*/
    ];
  }

  protected function CreateShipment(object $json): CreateShipmentResult
  {
    $shipment = new Shipment();
    $shipment->ShipmentDetails->product = $json->productId;
    $shipment->ShipmentDetails->accountNumber = $this->GetAccountNumber($json->productId);
    $shipment->ShipmentDetails->SetShipmentDate(new DateTimeImmutable('today'));
    $shipment->ShipmentDetails->ShipmentItem = new ShipmentItem();
    $shipment->ShipmentDetails->ShipmentItem->weightInKG = $json->package->weight ?? 0;
    $shipment->ShipmentDetails->ShipmentItem->lengthInCM = $json->package->length;
    $shipment->ShipmentDetails->ShipmentItem->widthInCM = $json->package->width;
    $shipment->ShipmentDetails->ShipmentItem->heightInCM = $json->package->height;
    $shipment->Shipper->Name->name1 = $this->settings->sender_name1 ?? '';
    $shipment->Shipper->Address->streetName = $this->settings->sender_street ?? '';
    $shipment->Shipper->Address->streetNumber = $this->settings->sender_streetnumber;
    $shipment->Shipper->Address->zip = $this->settings->sender_zip ?? '';
    $shipment->Shipper->Address->city = $this->settings->sender_city ?? '';
    $shipment->Shipper->Address->Origin = Country::Create($this->settings->sender_country ?? 'DE');
    $shipment->Shipper->Communication = new Communication();
    $shipment->Shipper->Communication->phone = $this->settings->sender_phone;
    $shipment->Shipper->Communication->email = $this->settings->sender_email;
    $shipment->Shipper->Communication->contactPerson = $this->settings->sender_contact_person;
    $shipment->Receiver->name1 = $json->address->name;
    switch ($json->address->addresstype) {
      case 0:
        $shipment->Receiver->Address = new ReceiverNativeAddress();
                
        $shipment->Receiver->name1 = $json->address->companyName;
        $shipment->Receiver->Address->name2 = join(
                        ';', 
                        array_filter(
                            [
                                $json->address->contactName,
                                $json->address->companyDivision
                            ],
                            fn(string $item) => !empty(trim($item))
                        )
                    );                        
                              
        $shipment->Receiver->Address->streetName = $json->address->street ?? '';
        $shipment->Receiver->Address->streetNumber = $json->address->streetnumber;
        $shipment->Receiver->Address->city = $json->address->city ?? '';
        $shipment->Receiver->Address->zip = $json->address->zip ?? '';
        $shipment->Receiver->Address->Origin = Country::Create($json->address->country ?? 'DE', $json->address->state);
        if (isset($json->address->address2) && !empty($json->address->address2))
          $shipment->Receiver->Address->addressAddition[] = $json->address->address2;
        break;
      case 1:
        $shipment->Receiver->Packstation = new PackStation();
        $shipment->Receiver->Packstation->postNumber = $json->address->postnumber;
        $shipment->Receiver->Packstation->packstationNumber = $json->address->parcelstationNumber;
        $shipment->Receiver->Packstation->city = $json->address->city ?? '';
        $shipment->Receiver->Packstation->zip = $json->address->zip ?? '';
        $shipment->Receiver->Packstation->Origin = Country::Create($json->address->country ?? 'DE', $json->address->state);
        break;
      case 2:
        $shipment->Receiver->Postfiliale = new Postfiliale();
        $shipment->Receiver->Postfiliale->postNumber = $json->address->postnumber;
        $shipment->Receiver->Postfiliale->postfilialeNumber = $json->address->postofficeNumber;
        $shipment->Receiver->Postfiliale->city = $json->address->city ?? '';
        $shipment->Receiver->Postfiliale->zip = $json->address->zip ?? '';
        $shipment->Receiver->Postfiliale->Origin = Country::Create($json->address->country ?? 'DE', $json->address->state);
        break;
      case 3:
        $shipment->Receiver->Address = new ReceiverNativeAddress();
                
        $shipment->Receiver->name1 = $json->address->name;
        $shipment->Receiver->Address->name2 = $json->address->contactName;
                       
        $shipment->Receiver->Address->streetName = $json->address->street ?? '';
        $shipment->Receiver->Address->streetNumber = $json->address->streetnumber;
        $shipment->Receiver->Address->city = $json->address->city ?? '';
        $shipment->Receiver->Address->zip = $json->address->zip ?? '';
        $shipment->Receiver->Address->Origin = Country::Create($json->address->country ?? 'DE', $json->address->state);
        if (isset($json->address->address2) && !empty($json->address->address2))
          $shipment->Receiver->Address->addressAddition[] = $json->address->address2;
        break;
    }
    $shipment->Receiver->Communication = new Communication();
    $shipment->Receiver->Communication->email = $json->address->email;
    $shipment->Receiver->Communication->phone = $json->address->phone;
    $api = new DhlApi($this->settings->user, $this->settings->signature);

    $ret = new CreateShipmentResult();
    $result = $api->CreateShipment($shipment);
    if (!$result instanceof CreateShipmentOrderResponse) {
      $ret->Errors[] = $result;
      return $ret;
    }
    if ($result->Status->statusCode === 0) {
      $ret->Success = true;
      $ret->TrackingNumber = $result->CreationState->shipmentNumber;
      $ret->TrackingUrl = sprintf('https://www.dhl.de/de/privatkunden/pakete-empfangen/verfolgen.html?piececode=%s', $ret->TrackingNumber);
      if (isset($result->CreationState->LabelData->labelData))
        $ret->Label = base64_decode($result->CreationState->LabelData->labelData);
      if (isset($result->CreationState->LabelData->exportLabelData))
        $ret->ExportDocuments = base64_decode($result->CreationState->LabelData->exportLabelData);
    } else if (isset($result->CreationState)) {
      if (is_array($result->CreationState->LabelData->Status->statusMessage))
        $ret->Errors = $result->CreationState->LabelData->Status->statusMessage;
      else
        $ret->Errors[] = $result->CreationState->LabelData->Status->statusMessage;
    } else {
      $ret->Errors[] = $result->Status->statusText;
    }
    return $ret;
  }

  protected function GetShippingProducts(): array
  {
    $result = [];
    if ($this->settings->accountnumber) {
      $result[] = Product::Create('V01PAK', 'DHL Paket')
          ->WithLength(15, 120)
          ->WithWidth(11, 60)
          ->WithHeight(1, 60)
          ->WithWeight(0.01, 31.5);
    }
    if ($this->settings->accountnumber_int) {
      $result[] = Product::Create('V53WPAK', 'DHL Paket International')
          ->WithLength(15, 120)
          ->WithWidth(11, 60)
          ->WithHeight(1, 60)
          ->WithWeight(0.01, 31.5)
          ->WithServices([Service::SERVICE_PREMIUM]);
    }
    if ($this->settings->accountnumber_euro) {
      $result[] = Product::Create('V54EPAK', 'DHL Europaket')
          ->WithLength(15, 120)
          ->WithWidth(11, 60)
          ->WithHeight(3.5, 60)
          ->WithWeight(0.01, 31.5);
    }
    if ($this->settings->accountnumber_connect) {
      $result[] = Product::Create('V55PAK', 'DHL Paket Connect')
          ->WithLength(15, 120)
          ->WithWidth(11, 60)
          ->WithHeight(3.5, 60)
          ->WithWeight(0.01, 31.5);
    }
    if ($this->settings->accountnumber_wp) {
      $result[] = Product::Create('V62WP', 'DHL Warenpost')
          ->WithLength(10, 35)
          ->WithWidth(7, 25)
          ->WithHeight(0.1, 5)
          ->WithWeight(0.01, 1);
    }
    if ($this->settings->accountnumber_wpint) {
      $result[] = Product::Create('V66WPI', 'DHL Warenpost International')
          ->WithLength(10, 35)
          ->WithWidth(7, 25)
          ->WithHeight(0.1, 10)
          ->WithWeight(0.01, 1)
          ->WithServices([Service::SERVICE_PREMIUM]);
    }
    return $result;
  }

  private function GetAccountNumber(string $product):?string {
    switch ($product) {
      case 'V01PAK': return $this->settings->accountnumber;
      case 'V53WPAK': return $this->settings->accountnumber_int;
      case 'V54EPAK': return $this->settings->accountnumber_euro;
      case 'V55PAK': return $this->settings->accountnumber_connect;
      case 'V62WP': return $this->settings->accountnumber_wp;
      case 'V66WPI': return $this->settings->accountnumber_wpint;
    }
    return null;
  }

    public function GetShipmentStatus(string $tracking): ShipmentStatus|null
    {
        return null;
    }
}
