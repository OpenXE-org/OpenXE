<?php

namespace Xentral\Modules\Dhl\Exception;

class DhlBaseException extends \RuntimeException implements DhlExceptionInterface
{
    public static function fromDhlStatusCode($code, $message)
    {
        switch ($code){
            case 118: return new InvalidCredentialsException($message);
            case 1101: return new InvalidRequestDataException($message);
        }
        return new DhlBaseException($message);
    }
}
