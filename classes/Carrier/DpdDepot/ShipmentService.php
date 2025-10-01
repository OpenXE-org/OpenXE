<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot;

use Psr\Log\LoggerInterface;
use SoapClient;
use SoapFault;
use SoapHeader;
use Xentral\Carrier\DpdDepot\Data\Authentication;
use Xentral\Carrier\DpdDepot\Data\Output;
use Xentral\Carrier\DpdDepot\Data\ParcelInformation;
use Xentral\Carrier\DpdDepot\Data\PrintOption;
use Xentral\Carrier\DpdDepot\Data\ShipmentResponse;
use Xentral\Carrier\DpdDepot\Data\StoreOrdersResponse;

class ShipmentService
{
    private const SOAP_BASE_SANDBOX = 'https://public-ws-stage.dpd.com/services/ShipmentService/V4_4/?wsdl';
    private const SOAP_BASE_LIVE = 'https://public-ws.dpd.com/restservices/ShipmentService/V4_4/?wsdl';

    private string $wsdl;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $delisId,
        private readonly string $authToken,
        bool $sandbox = false,
    ) {
        if ($sandbox) {
            $this->wsdl = self::SOAP_BASE_SANDBOX;
        } else {
            $this->wsdl = self::SOAP_BASE_LIVE;
        }
    }

    private function getAuth(): Authentication
    {
        $auth = new Authentication();
        $auth->delisId = $this->delisId;
        $auth->authToken = $this->authToken;
        return $auth;
    }

    /**
     * @throws SoapFault
     */
    public function storeOrders(PrintOption $printOption, array $orders): StoreOrdersResponse
    {
        $classmap = [
            'shipmentResponse' => ShipmentResponse::class,
            'parcelInformationType' => ParcelInformation::class,
            'OutputType' => Output::class,
            'storeOrdersResponseType' => StoreOrdersResponse::class,
        ];

        try {
            $client = new SoapClient($this->wsdl, [
                'classmap' => $classmap,
                'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            ]);
            $header = new SoapHeader(
                'http://dpd.com/common/service/types/Authentication/2.0',
                'authentication',
                $this->getAuth(),
            );
            $client->__setSoapHeaders($header);
            $response = $client->storeOrders([
                'printOptions' => ['printOption' => $printOption],
                'order' => $orders,
            ]);
        } catch (SoapFault $e) {
            $this->logger->error($e->getMessage());
            throw $e;
        }
        return $response->orderResult;
    }
}
