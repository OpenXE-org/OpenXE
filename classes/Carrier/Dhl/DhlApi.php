<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\Dhl;

use SoapClient;
use SoapHeader;
use Xentral\Carrier\Dhl\Data\CreateShipmentOrderRequest;
use Xentral\Carrier\Dhl\Data\CreateShipmentOrderResponse;
use Xentral\Carrier\Dhl\Data\CreationState;
use Xentral\Carrier\Dhl\Data\LabelData;
use Xentral\Carrier\Dhl\Data\Shipment;
use Xentral\Carrier\Dhl\Data\ShipmentOrder;
use Xentral\Carrier\Dhl\Data\Statusinformation;
use Xentral\Carrier\Dhl\Data\Version;

class DhlApi
{
  private SoapClient $soapClient;

  private const SANDBOX_URL = 'https://cig.dhl.de/services/sandbox/soap';
  private const PRODUCTION_URL = 'https://cig.dhl.de/services/production/soap';
  private const NAMESPACE_CIS = 'http://dhl.de/webservice/cisbase';

  public function __construct(string $user, string $signature)
  {
    $this->soapClient = new SoapClient(__DIR__ . '/Wsdl/geschaeftskundenversand-api-3.4.0.wsdl', [
      'login' => 'OpenXE_1',
      'password' => 'cjzNEpGXxbbnRwcYLISX3ZTTcQrQrz',
      'location' => self::PRODUCTION_URL,
      'trace' => 1,
      'connection_timeout' => 30,
      'classmap' => [
        'CreateShipmentOrderResponse' => CreateShipmentOrderResponse::class,
        'CreationState' => CreationState::class,
        'LabelData' => LabelData::class,
        'Statusinformation' => Statusinformation::class,
        'Version' => Version::class,
      ]
    ]);

    $authHeader = new SoapHeader(self::NAMESPACE_CIS, 'Authentification', [
        'user' => $user,
        'signature' => $signature
    ]);
    $this->soapClient->__setSoapHeaders($authHeader);
  }

  public function CreateShipment(Shipment $shipment): CreateShipmentOrderResponse|string
  {
    $request = new CreateShipmentOrderRequest();
    $request->Version = $this->getVersion();
    $request->ShipmentOrder = new ShipmentOrder();
    $request->ShipmentOrder->Shipment = $shipment;
    $request->ShipmentOrder->sequenceNumber = '1';
    $request->labelResponseType = "B64";
    try {
      $response = $this->soapClient->createShipmentOrder($request);
      return $response;
    } catch (\SoapFault $e) {
      return $e->getMessage();
    }
  }

  private function getVersion() {
    $version = new Version();
    $version->majorRelease = '3';
    $version->minorRelease = '4';
    return $version;
  }
}