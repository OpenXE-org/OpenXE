<?php

namespace FiskalyClient\errors;

use Datto\JsonRpc\Responses\ErrorResponse;
use FiskalyClient\errors\exceptions\FiskalyClientException;
use FiskalyClient\errors\exceptions\FiskalyHttpException;
use FiskalyClient\errors\exceptions\FiskalyHttpTimeoutException;

class FiskalyErrorHandler
{
    public static $HTTP_ERROR = -20000;
    public static $HTTP_TIMEOUT_ERROR = -21000;

    /**
     * Check if response contains error object and throw exception accordingly
     * @param $response
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     */
    public static function throwOnError($response)
    {
        if ($response instanceof ErrorResponse) {
            if ($response->getCode() == self::$HTTP_ERROR) {
                $responseData = $response->getData();
                $errorBody = json_decode(base64_decode($responseData['response']['body']), true);
                $requestId = $responseData['response']['headers']['x-request-id'][0];

                throw new FiskalyHttpException($errorBody['message'], $errorBody['code'], $errorBody['error'], $errorBody['status_code'], $requestId);
            } elseif ($response->getCode() == self::$HTTP_TIMEOUT_ERROR) {
                throw new FiskalyHttpTimeoutException($response->getMessage());
            } else {
                throw new FiskalyClientException($response->getMessage(), $response->getCode(), $response->getData());
            }
        }
    }
}
