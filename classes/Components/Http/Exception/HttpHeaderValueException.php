<?php

namespace Xentral\Components\Http\Exception;

use RuntimeException;
use Throwable;

class HttpHeaderValueException extends RuntimeException implements HttpComponentExceptionInterface
{
    public function __construct($message = '', $code = 0, Throwable $previous = null, $headerValue = null)
    {
        $headerString = '';
        if ($headerValue === null) {
            $headerString = 'null';
        }
        if (is_array($headerValue)) {
            $headerString = sprintf('[%s]', implode(',', $headerValue));
        }
        $headerString = strval($headerString);
        $message = sprintf('%s value:"%s"', $message, $headerString);

        parent::__construct($message, $code, $previous);
    }
}