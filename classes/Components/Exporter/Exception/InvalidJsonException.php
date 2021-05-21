<?php

namespace Xentral\Components\Exporter\Exception;

use Exception;

class InvalidJsonException extends Exception implements ExporterExceptionInterface
{
    /**
     * @param int $errorCode
     *
     * @return self
     */
    public static function fromJsonError($errorCode)
    {
        $exception = new self(self::mapJsonError($errorCode));
        return $exception;
    }

    private static function mapJsonError($jsonError)
    {
        switch ($jsonError) {
            case JSON_ERROR_NONE:
                $msg = 'Unknown error';
                break;
            case JSON_ERROR_DEPTH:
                $msg = 'The maximum stack depth has been exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $msg = 'Invalid or malformed JSON';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $msg = 'Control character error, possibly incorrectly encoded';
                break;
            case JSON_ERROR_SYNTAX:
                $msg = 'Syntax error';
                break;
            case JSON_ERROR_UTF8:
                $msg = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            case JSON_ERROR_RECURSION:
                $msg = 'One or more recursive references in the value to be encoded';
                break;
            case JSON_ERROR_INF_OR_NAN:
                $msg = 'One or more NAN or INF values in the value to be encoded';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $msg = 'A value of a type that cannot be encoded was given';
                break;
            case JSON_ERROR_INVALID_PROPERTY_NAME:
                $msg = 'A property name that cannot be encoded was given';
                break;
            case JSON_ERROR_UTF16:
                $msg = 'Malformed UTF-16 characters, possibly incorrectly encoded';
                break;
            default:
                $msg = 'Unknown Error';
        }

        return $msg;
    }
}
