<?php

// SPDX-FileCopyrightText: 2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Carrier\DpdDepot\LoginService;

use Psr\Log\LoggerInterface;
use Xentral\Carrier\DpdDepot\LoginService\Data\GetAuth;
use Xentral\Carrier\DpdDepot\LoginService\Data\Login;

class LoginService
{
    protected const BASEURL_SANDBOX = 'https://public-ws-stage.dpd.com/restservices/LoginService/V2_0/';
    protected const BASEURL_LIVE = 'https://public-ws.dpd.com/restservices/LoginService/V2_0/';
    protected string $baseUrl;

    public function __construct(protected LoggerInterface $logger, bool $sandbox = false)
    {
        if ($sandbox) {
            $this->baseUrl = self::BASEURL_SANDBOX;
        } else {
            $this->baseUrl = self::BASEURL_LIVE;
        }
    }

    /**
     * @throws LoginServiceException
     */
    public function getAuth(GetAuth $auth): Login
    {
        $data = json_encode($auth);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . 'getAuth',
            CURLOPT_RETURNTRANSFER => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (isset($response->getAuthResponse)) {
            $login = new Login();
            $login->delisId = $response->getAuthResponse->return->delisId;
            $login->customerUid = $response->getAuthResponse->return->customerUid;
            $login->authToken = $response->getAuthResponse->return->authToken;
            $login->depot = $response->getAuthResponse->return->depot;
            return $login;
        }
       if (isset($response->status)) {
            throw new LoginServiceException(
                "Login Error({$response->status->code}) {$response->status->type}: {$response->status->message}"
            );
        }
        $this->logger->error(
            'DPD Login failed with unknown response',
            ['response' => $response, 'reqData' => $data, 'code' => $code],
        );
        throw new LoginServiceException('unknown error (no valid response)');
    }
}
