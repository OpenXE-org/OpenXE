<?php

namespace FiskalyClient\responses;

class VersionResponse
{
    private $client_version;
    private $client_source_hash;
    private $client_commit_hash;
    private $smaers_version;

    /**
     * VersionResponse constructor.
     * @param $client_version
     * @param $client_source_hash
     * @param $client_commit_hash
     * @param $smaers_version
     */
    public function __construct($client_version, $client_source_hash, $client_commit_hash, $smaers_version)
    {
        $this->client_version = $client_version;
        $this->client_source_hash = $client_source_hash;
        $this->client_commit_hash = $client_commit_hash;
        $this->smaers_version = $smaers_version;
    }

    
    public function getClientVersion()
    {
        return $this->client_version;
    }

    
    public function getClientSourceHash()
    {
        return $this->client_source_hash;
    }

    
    public function getClientCommitHash()
    {
        return $this->client_commit_hash;
    }

    
    public function getSmaersVersion()
    {
        return $this->smaers_version;
    }

    public function __toString()
    {
        return __CLASS__ . " [client_version: " . $this->client_version . ", client_source_hash:" . $this->client_source_hash . ", client_commit_hash: " . $this->client_commit_hash . ", smaers_version: " . $this->smaers_version . "]";
    }
}
