<?php
use Datto\JsonRpc\Http\Client;
use Datto\JsonRpc\Responses\ErrorResponse;
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/../examples/env.php');

class JsonRPCTest extends TestCase
{

    /** @var Client|null  */
    protected $json_rpc = null;

    public function setUp(): void
    {
        $this->json_rpc = new Client($_ENV['FISKALY_SERVICE_URL']);
    }

    public function testInit()
    {
        $this->assertNotNull($this->json_rpc);
    }

    public function testInitWithoutParams()
    {
        $this->json_rpc = new Client(null);

        $this->assertNotNull($this->json_rpc);
        $this->assertTrue($this->json_rpc instanceof Client);
        $this->assertNotNull($this->json_rpc);
        $this->assertEquals('close', $this->json_rpc->getHeaders()['Connection']);
    }

    public function testWrongMethodQuery()
    {
        $this->json_rpc->query('wrong-method-name', null, $response)->send();

        $this->assertNotNull($response);
        $this->assertTrue($response instanceof ErrorResponse);
        $this->assertEquals(-32601, $response->getCode());
        $this->assertEquals('Method not found', $response->getMessage());
    }

    public function testWrongDataQuery()
    {
        $this->json_rpc->query('create-context', null, $response)->send();

        $this->assertNotNull($response);
        $this->assertTrue($response instanceof ErrorResponse);
        $this->assertEquals(-32603, $response->getCode());
        $this->assertEquals('Internal error', $response->getMessage());
    }

    public function testUndefinedResponseQuery()
    {
        $this->expectErrorMessage('Cannot pass parameter 3 by reference');
        $this->json_rpc->query('create-context', null, null)->send();
    }
}
