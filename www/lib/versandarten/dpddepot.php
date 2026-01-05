<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: LicenseRef-EGPL-3.1

use Psr\Log\LoggerInterface;
use Xentral\Carrier\DpdDepot\Data\Hazardous;
use Xentral\Carrier\DpdDepot\Data\HigherInsurance;
use Xentral\Carrier\DpdDepot\Data\PaperFormat;
use Xentral\Carrier\DpdDepot\Data\Parcel;
use Xentral\Carrier\DpdDepot\Data\Pickup;
use Xentral\Carrier\DpdDepot\Data\PrintOption;
use Xentral\Carrier\DpdDepot\Data\ShipmentServiceData;
use Xentral\Carrier\DpdDepot\LoginService\Data\GetAuth;
use Xentral\Carrier\DpdDepot\LoginService\LoginService;
use Xentral\Carrier\DpdDepot\LoginService\LoginServiceException;
use Xentral\Carrier\DpdDepot\ShipmentService;
use Xentral\Modules\ShippingMethod\Model\CreateShipmentResult;
use Xentral\Modules\ShippingMethod\Model\Product;
use Xentral\Modules\ShippingMethod\Model\Service;
use Xentral\Modules\ShippingMethod\Model\Shipment;
use Xentral\Modules\ShippingMethod\Model\ShipmentStatus;
use Xentral\Modules\SystemConfig\SystemConfigModule;


require_once dirname(__DIR__) . '/class.versanddienstleister.php';

class Versandart_dpddepot extends Versanddienstleister
{
    protected const HAZMAT_DB_URL = 'https://esolutions.dpd.com/partnerloesungen/hazdistributionservice.aspx';
    protected ShipmentService $api;
    protected SystemConfigModule $systemConfig;
    protected LoggerInterface $logger;
    protected array $options;
    protected DateTimeZone $timeZone;

    public bool $Beta = true;

    public function __construct(ApplicationCore $app, ?int $id)
    {
        parent::__construct($app, $id);
        if (!isset($this->id) || !isset($this->settings->delisId) || !isset($this->settings->password)) {
            return;
        }
        $this->systemConfig = $app->Container->get('SystemConfigModule');
        $this->logger = $app->Container->get('Logger');
        $this->timeZone = new DateTimeZone('Europe/Berlin');
        try {
            $token = $this->GetAuthToken();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->errors[] = $e->getMessage();
        }
        $this->api = new ShipmentService(
            $this->logger,
            $this->settings->delisId,
            $token ?? '',
            $this->settings->useSandbox ?? true,
        );
    }

    /**
     * @throws LoginServiceException
     * @throws DateMalformedStringException
     */
    protected function GetAuthToken(): string
    {
        if (empty($this->settings->delisId))
            return '';
        $ns = 'dpd';
        $key_expire = 'auth_expire_' . $this->settings->delisId;
        $key_token = 'auth_token_' . $this->settings->delisId;
        $expire = $this->systemConfig->tryGetValue($ns, $key_expire);
        $now = new DateTime('now', $this->timeZone);
        if ($expire == null || DateTime::createFromFormat(DATE_ATOM, $expire) < $now) {
            $loginApi = new LoginService($this->logger, $this->settings->useSandbox ?? true);
            $auth = new GetAuth();
            $auth->delisId = $this->settings->delisId;
            $auth->password = $this->settings->password;
            $login = $loginApi->getAuth($auth);
            $expire = new DateTime('03:00:00', $this->timeZone);
            if ($expire <= $now) {
                $expire->modify('+1 day');
            }
            $this->systemConfig->setValue($ns, $key_token, $login->authToken);
            $this->systemConfig->setValue($ns, $key_expire, $expire->format(DATE_ATOM));
            return $login->authToken;
        }
        return $this->systemConfig->getValue($ns, $key_token);
    }

    public function GetName(): string
    {
        return 'DPDdepot (BETA)';
    }

    public function AdditionalSettings(): array
    {
        return [
            'delisId' => ['typ' => 'text', 'bezeichnung' => 'Delis ID:'],
            'password' => ['typ' => 'text', 'bezeichnung' => 'Password:'],
            'useSandbox' => ['typ' => 'checkbox', 'bezeichnung' => 'Testsystem verwenden:', 'default' => true],
            'labelType' => [
                'typ' => 'select',
                'bezeichnung' => 'Label Type:',
                'optionen' => [
                    PaperFormat::A6->value => 'PDF A6',
                    PaperFormat::A4->value => 'PDF A4',
                ],
            ],
            'senderCustomerNumber' => ['typ' => 'text', 'bezeichnung' => 'Absender DPD Kundennummer:'],
            'senderName1' => ['typ' => 'text', 'bezeichnung' => 'Absender Name1:'],
            'senderName2' => ['typ' => 'text', 'bezeichnung' => 'Absender Name2:'],
            'senderStreet' => ['typ' => 'text', 'bezeichnung' => 'Absender Straße:'],
            'senderHouseNumber' => ['typ' => 'text', 'bezeichnung' => 'Absender Hausnummer:'],
            'senderZipCode' => ['typ' => 'text', 'bezeichnung' => 'Absender PLZ:'],
            'senderCity' => ['typ' => 'text', 'bezeichnung' => 'Absender Stadt:'],
            'senderCountry' => [
                'typ' => 'text',
                'bezeichnung' => 'Absender Land:',
                'info' => '2-Letter-Code (z.B. \'DE\')',
            ],
            'senderPhoneNumber' => ['typ' => 'text', 'bezeichnung' => 'Absender Telefon:'],
            'senderRemarks' => ['typ' => 'text', 'bezeichnung' => 'Absender Bemerkungen:'],
            'senderEmail' => ['typ' => 'text', 'bezeichnung' => 'Absender Email:'],
            'senderDepot' => [
                'type' => 'text',
                'bezeichnung' => 'Absender Depot:',
                'info' => '4 Zeichen Code (z.B. 0163)',
            ],
            'enableHazmat' => ['typ' => 'checkbox', 'bezeichnung' => 'Gefahrgut-Unterstützung:'],
        ];
    }

    public function CreateShipment(object $json): CreateShipmentResult
    {
        $ret = new CreateShipmentResult();
        $printOption = new PrintOption();
        $printOption->setPaperFormat(PaperFormat::from($this->settings->labelType));
        $order = new ShipmentServiceData();
        $order->generalShipmentData->sendingDepot = $this->settings->senderDepot;
        $order->generalShipmentData->product = $json->productId;
        $order->generalShipmentData->sender->name1 = $this->settings->senderName1;
        $order->generalShipmentData->sender->name2 = $this->settings->senderName2;
        $order->generalShipmentData->sender->street = $this->settings->senderStreet;
        $order->generalShipmentData->sender->houseNo = $this->settings->senderHouseNumber;
        $order->generalShipmentData->sender->zipCode = $this->settings->senderZipCode;
        $order->generalShipmentData->sender->city = $this->settings->senderCity;
        $order->generalShipmentData->sender->country = $this->settings->senderCountry;
        $order->generalShipmentData->sender->phone = $this->settings->senderPhoneNumber;
        $order->generalShipmentData->sender->email = $this->settings->senderEmail;
        $order->generalShipmentData->sender->comment = $this->settings->senderRemarks;
        $order->generalShipmentData->sender->customerNumer = $this->settings->senderCustomerNumber;
        $parcel = new Parcel();
        $order->parcels[] = $parcel;
        $parcel->customerReferenceNumber1 = $json->reference;
        $parcel->content = $json->content;
        $parcel->weight = intval(floatval($json->package->weight) * 100);
        if ($json->services->pickup) {
            try {
                $pickup = new Pickup();
                $pickup->quantity = 1;
                $pickup->collectionRequestAddress = $order->generalShipmentData->sender;
                $pickup->setDate((new DateTime($json->services->pickupDate))->setTimezone($this->timeZone));
                $pickup->setFromTime((new DateTime($json->services->pickupTimeFrom))->setTimezone($this->timeZone));
                $pickup->setToTime((new DateTime($json->services->pickupTimeTill))->setTimezone($this->timeZone));
                $order->productAndServiceData->pickup = $pickup;
            } catch (Exception) {
                $ret->Errors[] = 'Abholdatum/-zeit fehlerhaft';
                return $ret;
            }
        }

        if ($json->insuranceValue > 0) {
            $parcel->higherInsurance = new HigherInsurance();
            $parcel->higherInsurance->amount = floatval($json->insuranceValue) * 100;
        }
        switch ($json->address->addresstype) {
            case 0:
                $order->generalShipmentData->recipient->name1 = $json->address->companyName;
                $order->generalShipmentData->recipient->name2 = join(
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
                $order->generalShipmentData->recipient->name1 = $json->address->name;
                $order->generalShipmentData->recipient->name2 = $json->address->contactName;
                break;
        }
        //$order->generalShipmentData->recipient->?? = $json->address->address2;
        $order->generalShipmentData->recipient->street = $json->address->street;
        $order->generalShipmentData->recipient->houseNo = $json->address->streetnumber;
        $order->generalShipmentData->recipient->country = $json->address->country;
        $order->generalShipmentData->recipient->zipCode = $json->address->zip;
        $order->generalShipmentData->recipient->city = $json->address->city;
        $order->generalShipmentData->recipient->email = $json->address->email;
        $order->generalShipmentData->recipient->phone = $json->address->phone;
        if ($this->settings->enableHazmat && !empty($json->services->hazmat->unNumber)) {
            $hazardous = new Hazardous();
            $hazardous->identificationUnNo = $json->services->hazmat->unNumber;
            $hazardous->identificationClass = $json->services->hazmat->class;
            $hazardous->classificationCode = $json->services->hazmat->classificationCode;
            $hazardous->packingCode = $json->services->hazmat->packingCode;
            $hazardous->description = $json->services->hazmat->description;
            $hazardous->hazardousWeight = $json->services->hazmat->weight;
            $hazardous->factor = $json->services->hazmat->factor;
            $parcel->hazardous[] = $hazardous;
        }
        try {
            $result = $this->api->storeOrders($printOption, [$order]);

            if (empty($result->shipmentResponses[0]->faults)) {
                $ret->Success = true;
                $ret->TrackingNumber = $result->shipmentResponses[0]->parcelInformation[0]->parcelLabelNumber;
                $ret->TrackingUrl = 'https://tracking.dpd.de/status/de_DE/parcel/' . $ret->TrackingNumber;
                $ret->Label = $result->output->content;
            } else {
                $ret->Success = false;
                foreach ($result->shipmentResponses[0]->faults as $fault) {
                    $ret->Errors[] = $fault->faultCode.": ".$fault->message;
                }
            }

        } catch (Exception $e) {
            $ret->Errors[] = $e->getMessage();
            if (!empty($e->detail)) {
                $ret->Errors[] = print_r($e->detail, true);
            }
        }
        return $ret;
    }

    protected function GetShippingProducts(): array
    {
        $result = [];
        $result[] = Product::Create('CL', 'DPD CLASSIC');
        if ($this->settings->enableHazmat) {
            foreach ($result as $product) {
                $product->WithServices([Service::SERVICE_HAZMAT]);
            }
        }

        return $result;
    }

    public function GetShipmentStatus(string $tracking): ShipmentStatus|null
    {
        // TODO: Implement GetShipmentStatus() method.
        return null;
    }

    protected function GetShipmentDefaults(int $lieferscheinId): Shipment
    {
        $defaults = parent::GetShipmentDefaults($lieferscheinId);
        if (!$this->settings->enableHazmat) {
            return $defaults;
        }

/*        $check = $this->systemConfig->tryGetValue('dpd', 'hazmatdbcheck', time());
        if ($check <= time()) {
            $version_data = file_get_contents(self::HAZMAT_DB_URL);
            $version_data = json_decode($version_data);
            $dbdata = file_get_contents(self::HAZMAT_DB_URL . '?version=' . $version_data->version);
            file_put_contents($this->app->getTmpFolder() . 'dpd_hazmatdb', $dbdata);
        }*/
        $defaults->services[Service::SERVICE_HAZMAT->value] = [
            'unNumber' => '',
            'class' => '',
            'classificationCode' => '',
            'packingCode' => '',
            'description' => '',
            'weight' => 0,
            'factor' => 1
        ];
        return $defaults;
    }


}
