<?php

namespace Xentral\Carrier\SendCloud;

use Exception;

class SendcloudApiException extends Exception
{
  public static function fromResponse(array $response) : SendcloudApiException {
    if (!isset($response['body']) || !is_object($response['body']))
      return new SendcloudApiException(print_r($response,true));

    return new SendcloudApiException(
        print_r($response['body'],true),
        $response['code']
    );
  }
}