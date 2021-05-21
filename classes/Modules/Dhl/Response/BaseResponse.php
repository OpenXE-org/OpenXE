<?php

namespace Xentral\Modules\Dhl\Response;

use Xentral\Modules\Dhl\Exception\DhlBaseException;
use Xentral\Modules\Dhl\Exception\InvalidRequestDataException;

/**
 * Class BaseResponse
 *
 * @package Xentral\Modules\Dhl\Response
 */
class BaseResponse
{
    /**
     * @param string $responseXml
     *
     * @return \SimpleXMLElement
     */
    public static function createXmlElement($responseXml){
        $xmlElement = new \SimpleXMLElement($responseXml);

        $xmlElement->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xmlElement->registerXPathNamespace('bcs', 'http://dhl.de/webservices/businesscustomershipping/3.0');

        $faultString = $xmlElement->xpath('/SOAP-ENV:Envelope/SOAP-ENV:Body/SOAP-ENV:Fault/faultstring');
        if (!empty($faultString)) {
            $faultString = (string)$faultString[0];
            if (!empty($faultString)) {
                throw new DhlBaseException($faultString);
            }
        }

        $faultString = $xmlElement->xpath('/soap:Envelope/soap:Body/soap:Fault/faultstring');
        if (!empty($faultString)) {
            $faultString = (string)$faultString[0];
            if (!empty($faultString)) {
                throw new DhlBaseException($faultString);
            }
        }

        $statusCode = $xmlElement->xpath('/soap:Envelope/soap:Body/bcs:CreateShipmentOrderResponse/Status/statusCode');
        $statusCode = (int)$statusCode[0];

        if ($statusCode != 0) {
            $errorMessages = array_merge(
                $xmlElement->xpath(
                    '/soap:Envelope/soap:Body/bcs:CreateShipmentOrderResponse/Status/statusText'
                ),
                $xmlElement->xpath(
                    '/soap:Envelope/soap:Body/bcs:CreateShipmentOrderResponse/CreationState/LabelData/Status/statusMessage'
                )
            );
            $errorMsg = implode(' ', array_map(function ($error){
                return (string) $error;
            }, $errorMessages));

            throw DhlBaseException::fromDhlStatusCode($statusCode, $errorMsg);
        }


        $statusCode = $xmlElement->xpath('/soap:Envelope/soap:Body/bcs:CreateShipmentOrderResponse/CreationState/LabelData/Status/statusCode');
        $statusCode = (int)$statusCode[0];

        if ($statusCode != 0) {
            $errorMessages = $xmlElement->xpath(
                '/soap:Envelope/soap:Body/bcs:CreateShipmentOrderResponse/CreationState/LabelData/Status/statusMessage'
            );

            $errorMsg = implode(' ', array_map(function ($error){
                return (string) $error;
            }, $errorMessages));
            throw new InvalidRequestDataException($errorMsg);
        }

        return $xmlElement;
    }
}
