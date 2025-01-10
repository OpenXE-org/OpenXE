<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\Go;

use Xentral\Carrier\Go\Data\CreateOrderRequest;
use Xentral\Carrier\Go\Data\CreateOrderResponse;
use Xentral\Carrier\Go\Data\OrderStatus;

class GoApi {
    const BASE_URL_PROD = 'https://ws.api.general-overnight.com/external/ci/order/api/v1/';
    const BASE_URL_TEST = 'https://ws-tst.api.general-overnight.com/external/ci/order/api/v1/';

    protected string $baseUrl;

    public function __construct(protected string $username, protected string $password, bool $testMode = true) {
        if ($testMode)
            $this->baseUrl = self::BASE_URL_TEST;
        else
            $this->baseUrl = self::BASE_URL_PROD;
    }

    public function createOrder(CreateOrderRequest $request): CreateOrderResponse|string {
        $curl = curl_init();
        curl_setopt_array($curl, [
           CURLOPT_RETURNTRANSFER => 1,
           CURLOPT_URL => $this->baseUrl.'createOrder',
           CURLOPT_POST => 1,
           CURLOPT_USERNAME => $this->username,
           CURLOPT_PASSWORD => $this->password,
           CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
           CURLOPT_POSTFIELDS => json_encode($request, JSON_THROW_ON_ERROR),
        ]);

        $response = json_decode(curl_exec($curl));
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($code == 200) {
            $ret = new CreateOrderResponse();
            $ret->hwbNumber = $response->hwbNumber;
            $ret->orderStatus = OrderStatus::from($response->orderStatus);
            $ret->pickupDate = new \DateTime($response->pickupDate);
            $ret->deliveryDate = new \DateTime($response->deliveryDate);
            $ret->hwbOrPackageLabel = $response->hwbOrPackageLabel;
            $ret->barcodes = array_map(function ($item) { return $item->barcode; }, $response->package);
            return $ret;
        }
        if (isset($response->message))
            return $response->message;
        if (isset($response->Message))
            return $response->Message;
        return print_r($response, TRUE);
    }
}