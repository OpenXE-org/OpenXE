<?php

namespace FiskalyClient;

use Exception;
use FiskalyClient\errors\exceptions\FiskalyHttpException;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
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
     * @test
     */
    public function testRequestFailException()
    {
        $client = $this->createClient();
        $this->expectException(FiskalyHttpException::class);

        $response = $client->request(
            'PUT',
            '/tss/ecb75169'
        );

        $this->assertNotNull($response);
        $this->assertNotNull($response->getContext());
        $this->assertNotNull($response->getResponse());
    }
}
