<?php

namespace FiskalyClient\responses;

class SelfTestResponse
{
    private $proxy;
    private $backend;
    private $smaers;

    /**
     * SelfTestResponse constructor.
     * @param $proxy
     * @param $backend
     * @param $smaers
     */
    public function __construct($proxy, $backend, $smaers)
    {
        $this->proxy = $proxy;
        $this->backend = $backend;
        $this->smaers = $smaers;
    }

    
    public function getProxy()
    {
        return $this->proxy;
    }

    
    public function getBackend()
    {
        return $this->backend;
    }

    
    public function getSmaers()
    {
        return $this->smaers;
    }

    public function __toString()
    {
        return __CLASS__ . " [proxy: " . $this->proxy . ", backend:" . $this->backend . ", smaers: " . $this->smaers . "]";
    }
}
