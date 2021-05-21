<?php
namespace FiskalyClient\errors\exceptions;

class FiskalyHttpException extends FiskalyException
{
    protected $error;
    protected $code;
    protected $status;
    protected $requestId;

    public function __construct($message, $code = '', $error = '', $status = '', $requestId = '')
    {
        parent::__construct($message);
        $this->error = $error;
        $this->code = $code;
        $this->status = $status;
        $this->requestId = $requestId;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getStatusCode()
    {
        return $this->code;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}," .
         " Status: " . $this->status .
         " Error: " . $this->error .
         " RequestId: " . $this->requestId . "\n";
    }
}
