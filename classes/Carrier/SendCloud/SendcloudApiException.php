<?php

namespace Xentral\Carrier\SendCloud;

use Exception;

class SendcloudApiException extends Exception
{
  public static function fromResponse(array $response) : SendcloudApiException {
    if (!isset($response['body']) || !is_object($response['body']))
      return new SendcloudApiException();

    return new SendcloudApiException(
        $response['body']->error->message ?? '',
        $response['body']->error->code ?? 0
    );
  }
}