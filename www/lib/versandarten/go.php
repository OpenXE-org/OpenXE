<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: LicenseRef-EGPL-3.1

use Xentral\Carrier\Go\Data\ShipmentService;
use Xentral\Carrier\Go\Data\CreateOrderRequest;
use Xentral\Carrier\Go\Data\CreateOrderResponse;
use Xentral\Carrier\Go\Data\LabelType;
use Xentral\Carrier\Go\Data\OrderStatus;
use Xentral\Carrier\Go\GoApi;
use Xentral\Modules\ShippingMethod\Model\CreateShipmentResult;
use Xentral\Modules\ShippingMethod\Model\Product;
use Xentral\Modules\ShippingMethod\Model\Service;
use Xentral\Modules\ShippingMethod\Model\Shipment;
use Xentral\Modules\ShippingMethod\Model\ShipmentStatus;

require_once dirname(__DIR__) . '/class.versanddienstleister.php';

class Versandart_go extends Versanddienstleister
{
    protected GoApi $api;
    protected array $options;
    protected DateTimeZone $timeZone;
    public function __construct(ApplicationCore $app, ?int $id)
    {
        parent::__construct($app, $id);
        if (!isset($this->id) || !isset($this->settings->username) || !isset($this->settings->password)) {
            return;
        }
        $this->api = new GoApi(
            $this->app->Container->get('Logger'),
            $this->settings->username,
            $this->settings->password,
            $this->settings->useTestEndpoint ?? true,
        );
        $this->timeZone = new DateTimeZone('Europe/Berlin');
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
            'labelType' => [
                'typ' => 'select',
                'bezeichnung' => 'Label Type:',
                'optionen' => [
                    LabelType::PDF_A6->value => 'PDF A6',
                    LabelType::PDF_A4->value => 'PDF A4',
                ],
            ],
            'consignorName1' => ['typ' => 'text', 'bezeichnung' => 'Absender Name1:'],
            'consignorName2' => ['typ' => 'text', 'bezeichnung' => 'Absender Name2:'],
            'consignorName3' => ['typ' => 'text', 'bezeichnung' => 'Absender Name3:'],
            'consignorStreet' => ['typ' => 'text', 'bezeichnung' => 'Absender Straße:'],
            'consignorHouseNumber' => ['typ' => 'text', 'bezeichnung' => 'Absender Hausnummer:'],
            'consignorZipCode' => ['typ' => 'text', 'bezeichnung' => 'Absender PLZ:'],
            'consignorCity' => ['typ' => 'text', 'bezeichnung' => 'Absender Stadt:'],
            'consignorCountry' => [
                'typ' => 'text',
                'bezeichnung' => 'Absender Land:',
                'info' => '2-Letter-Code (z.B. \'DE\')',
            ],
            'consignorPhoneNumber' => ['typ' => 'text', 'bezeichnung' => 'Absender Telefon:'],
            'consignorRemarks' => ['typ' => 'text', 'bezeichnung' => 'Absender Bemerkungen:'],
            'consignorEmail' => ['typ' => 'text', 'bezeichnung' => 'Absender Email:'],
            'defaultPickupFrom' => ['typ' => 'text', 'bezeichnung' => 'Standard Abholzeit von:'],
            'defaultPickupTill' => ['typ' => 'text', 'bezeichnung' => 'Standard Abholzeit bis:'],
            'defaultContent' => ['typ' => 'text', 'bezeichnung' => 'Standard Inhalt:'],
        ];
    }

    public function CreateShipment(object $json): CreateShipmentResult
    {
        $ret = new CreateShipmentResult();
        $order = new CreateOrderRequest();
        $order->responsibleStation = $this->settings->responsibleStation;
        $order->customerId = $this->settings->customerId;
        $order->label = LabelType::from($this->settings->labelType);
        $order->shipment->orderStatus = $this->settings->orderAsDraft ? OrderStatus::New : OrderStatus::Released;
        $order->shipment->SetService($json->productId);
        $order->shipment->customerReference = $json->reference;
        $order->shipment->content = $json->content;
        $order->shipment->weight = floatval($json->package->weight);
        if ($json->services->pickup) {
            try {
                $order->shipment->pickup->date = (new DateTime($json->services->pickupDate))->setTimezone($this->timeZone);
                $order->shipment->pickup->from = (new DateTime($json->services->pickupTimeFrom))->setTimezone($this->timeZone);
                $order->shipment->pickup->till = (new DateTime($json->services->pickupTimeTill))->setTimezone($this->timeZone);
            } catch (Exception) {
                $ret->Errors[] = "Abholdatum/-zeit fehlerhaft";
                return $ret;
            }
        } else {
            $order->shipment->selfPickup = true;
        }

        if ($json->insuranceValue > 0) {
            $order->shipment->insurance->amount = $json->insuranceValue;
        }
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
        switch ($json->address->addresstype) {
            case 0:
                $order->consigneeAddress->name1 = $json->address->companyName;
                $order->consigneeAddress->name2 = join(
                    ';',
                    array_filter(
                        [
                            $json->address->contactName,
                            $json->address->companyDivision,
                        ],
                        fn(string $item) => !empty(trim($item)),
                    ),
                );
                break;
            case 3:
                $order->consigneeAddress->name1 = $json->address->name;
                $order->consigneeAddress->name2 = $json->address->contactName;
                break;
        }
        $order->consigneeAddress->name3 = $json->address->address2;
        $order->consigneeAddress->street = $json->address->street;
        $order->consigneeAddress->houseNumber = $json->address->streetnumber;
        $order->consigneeAddress->country = $json->address->country;
        $order->consigneeAddress->zipCode = $json->address->zip;
        $order->consigneeAddress->city = $json->address->city;
        $order->consigneeAddress->email = $json->address->email;
        $order->consigneeAddress->phoneNumber = $json->address->phone;
        $order->packages[0]->length = ceil($json->length);
        $order->packages[0]->width = ceil($json->width);
        $order->packages[0]->height = ceil($json->height);
        $result = $this->api->createOrder($order);
        if ($result instanceof CreateOrderResponse) {
            $ret->Success = true;
            $ret->TrackingNumber = $result->hwbNumber;
            $ret->TrackingUrl = 'https://www.general-overnight.com/deu_de/versenden/sendungsverfolgung.html?reference=' . $result->hwbNumber;
            $ret->Label = base64_decode($result->hwbOrPackageLabel);
            $ret->AdditionalInfo = "Abholtag: ".$result->pickupDate->format('d.m.Y')." / Zustelltag: ".$result->deliveryDate->format('d.m.Y');
        } else {
            $ret->Errors[] = $result;
        }
        return $ret;
    }

    protected function GetShippingProducts(): array
    {
        $result = [];
        $result[] = Product::Create(ShipmentService::Overnight->value, 'GO! Overnight')
            ->WithServices([Service::SERVICE_PICKUP, Service::SERVICE_PICKUP_DATE, Service::SERVICE_PICKUP_TIME]);
        $result[] = Product::Create(ShipmentService::Worldwide->value, 'GO! Worldwide')
            ->WithServices([Service::SERVICE_PICKUP, Service::SERVICE_PICKUP_DATE, Service::SERVICE_PICKUP_TIME]);
        $result[] = Product::Create(ShipmentService::OvernightLetter->value, 'GO! Overnight - Letter')
            ->WithWeight(0, 0.25)
            ->WithServices([Service::SERVICE_PICKUP, Service::SERVICE_PICKUP_DATE, Service::SERVICE_PICKUP_TIME]);
        $result[] = Product::Create(ShipmentService::WorldwideLetter->value, 'GO! Worldwide - Letter')
            ->WithWeight(0, 0.25)
            ->WithServices([Service::SERVICE_PICKUP, Service::SERVICE_PICKUP_DATE, Service::SERVICE_PICKUP_TIME]);
        $result[] = Product::Create(ShipmentService::OvernightCodedDelivery->value, 'GO! Overnight Coded Delivery')
            ->WithServices([Service::SERVICE_PICKUP, Service::SERVICE_PICKUP_DATE, Service::SERVICE_PICKUP_TIME]);
        $result[] = Product::Create(ShipmentService::OvernightCodedLetter->value, 'GO! Overnight Letter Coded Delivery')
            ->WithWeight(0, 0.25)
            ->WithServices([Service::SERVICE_PICKUP, Service::SERVICE_PICKUP_DATE, Service::SERVICE_PICKUP_TIME]);
        return $result;
    }

    public function GetShipmentStatus(string $tracking): ShipmentStatus|null
    {
        // TODO: Implement GetShipmentStatus() method.
        return null;
    }

    public function GetShipmentDefaults(int $lieferscheinId): Shipment
    {
        $shipment = parent::GetShipmentDefaults($lieferscheinId);
        $shipment->content = $this->settings->defaultContent;
        if (!empty($this->settings->defaultPickupFrom) && !empty($this->settings->defaultPickupTill)) {
            $shipment->services['pickup'] = true;
            try {
                $pickupFrom = new DateTime($this->settings->defaultPickupFrom, $this->timeZone);
                $pickupTimeFrom = new DateTime($this->settings->defaultPickupFrom, $this->timeZone);
                $pickupTimeTill = new DateTime($this->settings->defaultPickupTill, $this->timeZone);
                if ($pickupFrom < new DateTime('now')) {
                    $pickupFrom = $pickupFrom->add(new DateInterval('P1D'));
                }
                $shipment->services['pickupDate'] = $pickupFrom->format(DATE_ATOM);
                $shipment->services['pickupTimeFrom'] = $pickupTimeFrom->format(DATE_ATOM);
                $shipment->services['pickupTimeTill'] = $pickupTimeTill->format(DATE_ATOM);
            } catch (Exception) {
            }
        }
        return $shipment;
    }


}
