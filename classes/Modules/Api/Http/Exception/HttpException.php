<?php

namespace Xentral\Modules\Api\Http\Exception;

use RuntimeException;
use Throwable;

class HttpException extends RuntimeException
{
    /** @var int $statusCode */
    protected $statusCode = 500;

    /** @var array $errors */
    protected $errors;

    /**
     * @param int            $statusCode
     * @param string         $message
     * @param int            $code
     * @param array          $errors
     * @param Throwable|null $previous
     */
    public function __construct(
        $statusCode = 500,
        $message = "",
        $code = 0,
        Throwable $previous = null,
        array $errors = array()
    ) {
        parent::__construct($message, $code, $previous);

        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    /**
     * @return int HTTP-Statuscode
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return sizeof($this->errors) > 0;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
