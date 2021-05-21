<?php

namespace FiskalyClient\responses;

class RequestResponse
{
    private $response;
    private $context;

    /**
     * RequestResponse constructor.
     * @param $response
     * @param $context
     */
    public function __construct($response, $context)
    {
        $this->response = $response;
        $this->context = $context;
    }


    public function getResponse()
    {
        return $this->response;
    }


    public function getContext()
    {
        return $this->context;
    }


    public function __toString()
    {
        return __CLASS__ . " [context: " . $this->context . ", response:" . $this->response . "]";
    }
}
