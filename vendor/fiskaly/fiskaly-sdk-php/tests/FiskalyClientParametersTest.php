<?php

namespace FiskalyClient;

use PHPUnit\Framework\TestCase;
use Exception;

require_once(__DIR__ . '/../examples/env.php');

class FiskalyClientParametersTest extends TestCase
{
    /**
     * @test
     */
    public function testClientInitServiceParameter()
    {
        try {
            return FiskalyClient::createUsingCredentials(null, null, null, null);
        } catch (Exception $e) {
            $this->assertEquals('fiskaly_service must be provided', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function testClientInitApiKeyParameter()
    {
        try {
            return FiskalyClient::createUsingCredentials($_ENV["FISKALY_SERVICE_URL"], null, null, null);
        } catch (Exception $e) {
            $this->assertEquals('api_key must be provided', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function testClientInitApiSecretParameter()
    {
        try {
            return FiskalyClient::createUsingCredentials($_ENV["FISKALY_SERVICE_URL"], $_ENV["FISKALY_API_KEY"], null, null);
        } catch (Exception $e) {
            $this->assertEquals('api_secret must be provided', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function testClientInitBaseUrlParameter()
    {
        try {
            return FiskalyClient::createUsingCredentials($_ENV["FISKALY_SERVICE_URL"], $_ENV["FISKALY_API_KEY"], $_ENV["FISKALY_API_SECRET"], null);
        } catch (Exception $e) {
            $this->assertEquals('base_url must be provided', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function testClientServiceParameterInitUsingContext()
    {
        try {
            return FiskalyClient::createUsingContext(null, null);
        } catch (Exception $e) {
            $this->assertEquals('fiskaly_service must be provided', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function testClientContextParameterInitUsingContext()
    {
        try {
            return FiskalyClient::createUsingContext($_ENV["FISKALY_SERVICE_URL"], null);
        } catch (Exception $e) {
            $this->assertEquals('context must be provided', $e->getMessage());
        }
    }
}
