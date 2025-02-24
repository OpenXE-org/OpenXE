<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: LicenseRef-EGPL-3.1

use Xentral\Carrier\Go\Data\MoneyValue;
use Xentral\Carrier\Go\Data\ShipmentService;
use Xentral\Carrier\Go\Data\CreateOrderRequest;
use Xentral\Carrier\Go\Data\CreateOrderResponse;
use Xentral\Carrier\Go\Data\LabelType;
use Xentral\Carrier\Go\Data\OrderStatus;
use Xentral\Carrier\Go\GoApi;
use Xentral\Modules\ShippingMethod\Model\CreateShipmentResult;
use Xentral\Modules\ShippingMethod\Model\Product;
use Xentral\Modules\ShippingMethod\Model\ShipmentStatus;

require_once dirname(__DIR__) . '/class.versanddienstleister.php';

class Versandart_go extends Versanddienstleister
{
  protected GoApi $api;
  protected array $options;

  public function __construct(ApplicationCore $app, ?int $id)
  {
    parent::__construct($app, $id);
    if (!isset($this->id) || !isset($this->settings->username) || !isset($this->settings->password))
      return;
    $this->api = new GoApi(
        $this->app->Container->get('Logger'),
        $this->settings->username,
        $this->settings->password,
        $this->settings->useTestEndpoint ?? true,
    );
  }

  public function GetName(): string
  {
    return "GO!";
  }

  public function AdditionalSettings(): array
  {
    return [
        'username' => ['typ' => 'text', 'bezeichnung' => 'API Username:'],
        'password' => ['typ' => 'text', 'bezeichnung' => 'API Password:'],
        'responsibleStation' => ['typ' => 'text', 'bezeichnung' => 'Responsible Station:'],
        'customerId' => ['typ' => 'text', 'bezeichnung' => 'Customer ID:'],
        'useTestEndpoint' => ['typ' => 'checkbox', 'bezeichnung' => 'Testsystem verwenden:', 'default' => true],
        'orderAsDraft' => ['typ' => 'checkbox', 'bezeichnung' => 'Order As Draft:'],
        'labelType' => ['typ' => 'select', 'bezeichnung' => 'Label Type:', 'optionen' => [
            LabelType::PDF_A6->value => 'PDF A6',
            LabelType::PDF_A4->value => 'PDF A4',
        ]],
        'consignorName1' => ['typ' => 'text', 'bezeichnung' => 'Absender Name1:'],
        'consignorName2' => ['typ' => 'text', 'bezeichnung' => 'Absender Name2:'],
        'consignorName3' => ['typ' => 'text', 'bezeichnung' => 'Absender Name3:'],
        'consignorStreet' => ['typ' => 'text', 'bezeichnung' => 'Absender Straße:'],
        'consignorHouseNumber' => ['typ' => 'text', 'bezeichnung' => 'Absender Hausnummer:'],
        'consignorZipCode' => ['typ' => 'text', 'bezeichnung' => 'Absender PLZ:'],
        'consignorCity' => ['typ' => 'text', 'bezeichnung' => 'Absender Stadt:'],
        'consignorCountry' => ['typ' => 'text', 'bezeichnung' => 'Absender Land:', 'info' => '2-Letter-Code (z.B. \'DE\')'],
        'consignorPhoneNumber' => ['typ' => 'text', 'bezeichnung' => 'Absender Telefon:'],
        'consignorRemarks' => ['typ' => 'text', 'bezeichnung' => 'Absender Bemerkungen:'],
        'consignorEmail' => ['typ' => 'text', 'bezeichnung' => 'Absender Email:'],
        'defaultPickupFrom' => ['typ' => 'text', 'bezeichnung' => 'Standard Abholzeit von:'],
        'defaultPickupTill' => ['typ' => 'text', 'bezeichnung' => 'Standard Abholzeit bis:'],
        'defaultContent' => ['typ' => 'text', 'bezeichnung' => 'Standard Inhalt:']
    ];
  }

  public function CreateShipment(object $json, array $address): CreateShipmentResult
  {
    $order = new CreateOrderRequest();
    $order->responsibleStation = $this->settings->responsibleStation;
    $order->customerId = $this->settings->customerId;
    $order->label = LabelType::from($this->settings->labelType);
    $order->shipment->orderStatus = $this->settings->orderAsDraft ? OrderStatus::New : OrderStatus::Released;
    $order->shipment->SetService($json->product);
    $order->shipment->customerReference = $json->order_number;
    $order->shipment->content = $this->settings->defaultContent ?? '';
    $order->shipment->weight = floatval($json->weight);
    $order->shipment->pickup->dateFrom = new DateTime($this->settings->defaultPickupFrom);
    if ($order->shipment->pickup->dateFrom < new DateTime('now'))
        $order->shipment->pickup->dateFrom = $order->shipment->pickup->dateFrom->add(new DateInterval('P1D'));
    $order->shipment->pickup->dateTill = new DateTime($this->settings->defaultPickupTill);
    if ($json->total_insured_value > 0)
        $order->shipment->insurance->amount = $json->total_insured_value;
    $order->consignorAddress->name1 = $this->settings->consignorName1;
    $order->consignorAddress->name2 = $this->settings->consignorName2;
    $order->consignorAddress->name3 = $this->settings->consignorName3;
    $order->consignorAddress->street = $this->settings->consignorStreet;
    $order->consignorAddress->houseNumber = $this->settings->consignorHouseNumber;
    $order->consignorAddress->zipCode = $this->settings->consignorZipCode;
    $order->consignorAddress->city = $this->settings->consignorCity;
    $order->consignorAddress->country = $this->settings->consignorCountry;
    $order->consignorAddress->phoneNumber = $this->settings->consignorPhoneNumber;
    $order->consignorAddress->remarks = $this->settings->consignorRemarks;
    $order->consignorAddress->email = $this->settings->consignorEmail;
    switch ($json->addresstype) {
      case 0:     
        $order->consigneeAddress->name1 = $json->company_name;
        $order->consigneeAddress->name2 = join(
                        ';', 
                        array_filter(
                            [
                                $json->contact_name,
                                $json->company_division
                            ],
                            fn(string $item) => !empty(trim($item))
                        )
                    );                
        break;
      case 3:
        $order->consigneeAddress->name1 = $json->name;
        $order->consigneeAddress->name2 = $json->contact_name;
        break;

    }
    $order->consigneeAddress->name3 = $json->address2;
    $order->consigneeAddress->street = $json->street;
    $order->consigneeAddress->houseNumber = $json->streetnumber;
    $order->consigneeAddress->country = $json->country;
    $order->consigneeAddress->zipCode = $json->zip;
    $order->consigneeAddress->city = $json->city;
    $order->consigneeAddress->email = $json->email;
    $order->consigneeAddress->phoneNumber = $json->phone;
    $ret = new CreateShipmentResult();
    $result = $this->api->createOrder($order);
    if ($result instanceof CreateOrderResponse) {
      $ret->Success = true;
      $ret->TrackingNumber = $result->hwbNumber;
      $ret->TrackingUrl =  'https://www.general-overnight.com/deu_de/versenden/sendungsverfolgung.html?reference='.$result->hwbNumber;
      $ret->Label = base64_decode($result->hwbOrPackageLabel);
      $ret->AdditionalInfo = "Abholtag: ".$result->pickupDate->format('d.m.Y')." / Zustelltag: ".$result->deliveryDate->format('d.m.Y');
    } else {
      $ret->Errors[] = $result;
    }
    return $ret;
  }

  public function GetShippingProducts(): array
  {
      $result = [];
      $result[] = Product::Create(ShipmentService::Overnight->value, 'GO! Overnight');
      $result[] = Product::Create(ShipmentService::Worldwide->value, 'GO! Worldwide');
      $result[] = Product::Create(ShipmentService::OvernightLetter->value, 'GO! Overnight - Letter')
        ->WithWeight(0, 0.25);
      $result[] = Product::Create(ShipmentService::WorldwideLetter->value, 'GO! Worldwide - Letter')
          ->WithWeight(0, 0.25);
      $result[] = Product::Create(ShipmentService::OvernightCodedDelivery->value, 'GO! Overnight Coded Delivery');
      $result[] = Product::Create(ShipmentService::OvernightCodedLetter->value, 'GO! Overnight Letter Coded Delivery')
          ->WithWeight(0, 0.25);
      return $result;
  }

    public function GetShipmentStatus(string $tracking): ShipmentStatus|null
    {
        // TODO: Implement GetShipmentStatus() method.
        return null;
    }


}
