<?php

namespace Xentral\Modules\Dhl\Api;

use PHPUnit\Runner\Exception;
use SoapFault;
use SoapHeader;
use SoapVar;
use stdClass;
use Xentral\Modules\Dhl\Exception\InsufficientPermissionsException;
use Xentral\Modules\Dhl\Exception\InvalidCredentialsException;
use Xentral\Modules\Dhl\Request\CreateNationalShipmentRequest;
use Xentral\Modules\Dhl\Request\GetManifestRequest;
use Xentral\Modules\Dhl\Request\GetVersionRequest;
use Xentral\Modules\Dhl\Response\CreateShipmentResponse;
use Xentral\Modules\Dhl\Response\GetVersionResponse;

/**
 * Class DhlApi
 *
 * @package Xentral\Modules\Dhl\api
 */
class DhlApi
{
    /** @var string */
    private $user;

    /** @var string */
    private $pass;

    /** @var string */
    private $accountNumber;

    /** @var string */
    private $basicUsername;

    /** @var string */
    private $basicPassword;

    /** @var string */
    private $endpoint;

    /** @var string */
    private $senderName;
    /** @var string */
    private $senderStreetName;
    /** @var int */
    private $senderStreetNo;
    /** @var string */
    private $senderZip;
    /** @var string */
    private $senderCity;
    /** @var string */
    private $senderCountry;
    /** @var string */
    private $senderEmail;

    /** @var int */
    private $versionMajor = 3;
    /** @var int */
    private $versionMino = 0;

    /**
     * DhlApi constructor.
     *
     * @param string $user
     * @param string $pass
     * @param string $basicUsername
     * @param string $basicPassword
     * @param string $accountNumber
     * @param string $endpoint
     * @param string $senderName
     * @param string $senderStreetName
     * @param int    $senderStreetNo
     * @param string $senderZip
     * @param string $senderCity
     * @param string $senderCountry
     * @param        $senderEmail
     */
    public function __construct(
        $user,
        $pass,
        $basicUsername,
        $basicPassword,
        $accountNumber,
        $endpoint,
        $senderName,
        $senderStreetName,
        $senderStreetNo,
        $senderZip,
        $senderCity,
        $senderCountry,
        $senderEmail
    ) {
        $this->user = $user;
        $this->pass = $pass;
        $this->basicUsername = $basicUsername;
        $this->basicPassword = $basicPassword;
        $this->accountNumber = $accountNumber;
        $this->endpoint = $endpoint;

        $this->senderName = $senderName;
        $this->senderStreetName = $senderStreetName;
        $this->senderStreetNo = $senderStreetNo;
        $this->senderZip = $senderZip;
        $this->senderCity = $senderCity;
        $this->senderCountry = $senderCountry;
        $this->senderEmail = $senderEmail;
    }

    /**
     * @return GetVersionResponse
     */
    public function getVersion()
    {
        $getVersionRequest = new GetVersionRequest($this->versionMajor, $this->versionMino);

        $postFields = $getVersionRequest->toXml(
            $this->user,
            $this->pass
        );

        $response = $this->performRequest($postFields, 'getVersion');

        return GetVersionResponse::fromResponseXml($response);
    }


    /**
     * @param string $manifestDate
     *
     * @return GetVersionResponse
     */
    public function getManifest($manifestDate)
    {
        $getManifestRequest = new GetManifestRequest($manifestDate);

        $postFields = $getManifestRequest->toXml(
            $this->user,
            $this->pass
        );

        $response = $this->performRequest($postFields, 'getManifest');

        return GetVersionResponse::fromResponseXml($response);
    }

    /**
     * @param CreateNationalShipmentRequest $createShipmentRequest
     *
     * @return CreateShipmentResponse
     */
    public function createShipment($createShipmentRequest)
    {
        $postFields = $createShipmentRequest->toXml(
            $this->user,
            $this->pass,
            $this->accountNumber,
            $this->senderName,
            $this->senderStreetName,
            $this->senderStreetNo,
            $this->senderZip,
            $this->senderCity,
            $this->senderCountry,
            $this->senderEmail
        );

        $response = $this->performRequest($postFields, 'createShipmentOrder');

        return CreateShipmentResponse::fromResponseXml($response);
    }

    /**
     * @param CreateNationalShipmentRequest $createShipmentRequest
     *
     * @return CreateShipmentResponse
     */
    public function validateShipment($createShipmentRequest)
    {
        $postFields = $createShipmentRequest->toXml(
            $this->user,
            $this->pass,
            $this->accountNumber,
            $this->senderName,
            $this->senderStreetName,
            $this->senderStreetNo,
            $this->senderZip,
            $this->senderCity,
            $this->senderCountry,
            $this->senderEmail
        );

        $response = $this->performRequest($postFields, 'validateShipment');

        return CreateShipmentResponse::fromResponseXml($response);
    }



    private function performRequest($payload, $method)
    {
        $curl = curl_init($this->endpoint);

        curl_setopt_array(
            $curl,
            [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Basic ' . base64_encode("{$this->basicUsername}:{$this->basicPassword}"),
                    'Content-Type: application/xml; charset=utf-8',
                    "SOAPAction: urn:{$method}",
                ],
            ]
        );

        $response = curl_exec($curl);
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $this->checkHttpCode($responseCode);

        return $response;
    }

    /**
     * @param int $responseCode
     */
    private function checkHttpCode($responseCode)
    {
        switch ($responseCode) {
            case 401:
                throw new InvalidCredentialsException("");
            case 403:
                throw new InsufficientPermissionsException();
        }
    }
}
