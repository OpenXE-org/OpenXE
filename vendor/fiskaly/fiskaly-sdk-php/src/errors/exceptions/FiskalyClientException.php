<?php

namespace FiskalyClient\errors\exceptions;

class FiskalyClientException extends FiskalyException
{
    protected $code;
    protected $data;

    public function __construct($message, $code = '', $data = null)
    {
        parent::__construct($message);
        $this->code = $code;
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getStatusCode()
    {
        return $this->code;
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}, Data: " . json_encode($this->data) . "\n";
    }
}
