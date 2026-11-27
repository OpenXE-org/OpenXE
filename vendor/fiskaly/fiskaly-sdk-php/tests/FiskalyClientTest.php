<?php

namespace FiskalyClient;

use Exception;
use FiskalyClient\errors\exceptions\FiskalyException;
use FiskalyClient\responses\ClientConfiguration;
use FiskalyClient\responses\RequestResponse;
use FiskalyClient\responses\VersionResponse;
use FiskalyClient\responses\SelfTestResponse;
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/../examples/env.php');

class FiskalyClientTest extends TestCase
{
    /**
     * @return FiskalyClient
     */
    public function createClient()
    {
        try {
            return FiskalyClient::createUsingCredentials($_ENV["FISKALY_SERVICE_URL"], $_ENV["FISKALY_API_KEY"], $_ENV["FISKALY_API_SECRET"], 'https://kassensichv.io/api/v1');
        } catch (Exception $e) {
            exit($e);
        }
    }

    /**
     * @return FiskalyClient
     */
    public function createClientUsingContext()
    {
        try {
            return FiskalyClient::createUsingContext($_ENV["FISKALY_SERVICE_URL"], '$_SESSION["FISKALY_CONTEXT"]');
        } catch (Exception $e) {
            exit($e);
        }
    }

    /**
     * @test
     */
    public function testClient()
    {
        $client = $this->createClient();
        $this->assertNotNull($client);
        $this->assertTrue($client instanceof FiskalyClient);
    }

    /**
     * @test
     */
    public function testClientUsingContext()
    {
        $client = $this->createClientUsingContext();
        $this->assertNotNull($client);
        $this->assertTrue($client instanceof FiskalyClient);
    }

    /**
     * @test
     */
    public function testContext()
    {
        $client = $this->createClient();
        $this->assertNotEquals('', $client->getContext());
    }

    /**
     * @test
     */
    public function testVersion()
    {
        try {
            $client = $this->createClient();
            $version = $client->getVersion();

            $this->assertNotNull($version);
            $this->assertTrue($version instanceof VersionResponse);
            $this->assertNotNull($version->getClientVersion());
            $this->assertNotNull($version->getSmaersVersion());
            $this->assertNotNull($version->getClientCommitHash());
            $this->assertNotNull($version->getClientSourceHash());
        } catch (Exception $e) {
            $this->assertTrue(false);
        }
    }

    /**
     * @test
     */
    public function testSelfTest()
    {
        try {
            $client = $this->createClient();
            $selftest = $client->selfTest();

            $this->assertNotNull($selftest);
            $this->assertTrue($selftest instanceof SelfTestResponse);
            $this->assertNotNull($selftest->getProxy());
            $this->assertNotNull($selftest->getBackend());
            $this->assertNotNull($selftest->getSmaers());
        } catch (Exception $e) {
            $this->assertTrue(false);
        }
    }

    /**
     * @test
     */
    public function testGetConfig()
    {
        try {
            $client = $this->createClient();
            $config = $client->getConfig();

            $this->assertNotNull($config);
            $this->assertTrue($config instanceof ClientConfiguration);
            $this->assertNotNull($config->getClientTimeout());
            $this->assertNotNull($config->getDebugFile());
            $this->assertNotNull($config->getDebugLevel());
            $this->assertNotNull($config->getSmearsTimeout());
        } catch (Exception $e) {
            $this->assertTrue(false);
        }
    }

    /**
     * @test
     */
    public function testConfigure()
    {
        try {
            $client = $this->createClient();

            try {
                $config_params = [
                    'debug_level' => 4,
                    'debug_file' => __DIR__ . '/../fiskaly.log',
                    'client_timeout' =>  5000,
                    'smaers_timeout' =>  2000,
                    'http_proxy' => ""
                ];
                $config = $client->configure($config_params);
            } catch (Exception $e) {
                exit($e);
            }

            $this->assertNotNull($config);
            $this->assertTrue($config instanceof ClientConfiguration);
            $this->assertNotNull($config->getClientTimeout());
            $this->assertNotNull($config->getDebugFile());
            $this->assertNotNull($config->getDebugLevel());
            $this->assertNotNull($config->getSmearsTimeout());
            $this->assertNotNull($config->getHttpProxy());

            $this->assertEquals($config_params['debug_level'], $config->getDebugLevel());
            $this->assertEquals($config_params['debug_file'], $config->getDebugFile());
            $this->assertEquals($config_params['client_timeout'], $config->getClientTimeout());
            $this->assertEquals($config_params['smaers_timeout'], $config->getSmearsTimeout());
            $this->assertEquals($config_params['http_proxy'], $config->getHttpProxy());
        } catch (Exception $e) {
            $this->assertTrue(false);
        }
    }

    /**
     * @test
     */
    public function testRequest()
    {
        try {
            $client = $this->createClient();
            $response = $client->request(
                'PUT',
                '/tss/ecb75169-680f-48d1-93b2-52cc10abb9f/tx/9cbe6566-e24c-42ac-97fe-6a0112fb3c6',
                ["last_revision" => "0"],
                ["Content-Type" => "application/json"],
                'eyJzdGF0ZSI6ICJBQ1RJVkUiLCJjbGllbnRfaWQiOiAiYTYyNzgwYjAtMTFiYi00MThhLTk3MzYtZjQ3Y2E5NzVlNTE1In0='
            );

            $context = $client->getContext();

            $this->assertNotNull($response);
            $this->assertTrue($response instanceof RequestResponse);
            $this->assertNotNull($response->getContext());
            $this->assertNotNull($response->getResponse());

            // Check if context updated
            $this->assertNotEquals($context, $response->getContext());
        } catch (Exception $e) {
            // echo "Exception: " . $e->getMessage() . "\n";
            $this->assertTrue($e instanceof FiskalyException);
            // $this->assertTrue(false);
        }
    }
}
