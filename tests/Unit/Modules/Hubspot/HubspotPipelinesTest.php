<?php

namespace Xentral\Tests\Unit\Modules\Hubspot;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Xentral\Components\Database\Adapter\AdapterInterface;
use Xentral\Components\Database\Database;
use Xentral\Components\Database\SqlQuery\QueryFactory;
use Xentral\Modules\Hubspot\Exception\HttpClientException;
use Xentral\Modules\Hubspot\Exception\HubspotException;
use Xentral\Modules\Hubspot\HubspotClientService;
use Xentral\Modules\Hubspot\HubspotConfigurationService;
use Xentral\Modules\Hubspot\HubspotContactPropertyGateway;
use Xentral\Modules\Hubspot\HubspotDealPropertyService;
use Xentral\Modules\Hubspot\HubspotHttpClientService;

final class HubspotPipelinesTest extends TestCase
{
    /** @var array */
    private $history = [];

    /** @var MockHandler */
    private $handler;

    /** @var HubspotHttpClientService */
    private $httpClient;

    protected function setUp(): void
    {
        $this->handler = new MockHandler();
        $stack = HandlerStack::create($this->handler);
        $stack->push(Middleware::history($this->history));
        $this->httpClient = new HubspotHttpClientService(30, new Client(['handler' => $stack]));
    }

    public function testReadsDatedEndpointWithConfiguredBearerToken(): void
    {
        $stages = [['id' => 'appointmentscheduled', 'label' => 'Appointment scheduled']];
        $this->queuePipelines([
            ['id' => 'default', 'stages' => $stages],
            ['id' => 'renewals', 'stages' => [['id' => '42', 'label' => 'Renewal']]],
        ]);
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->expects(self::never())->method('perform');
        $service = $this->createDealService($this->createClient(), $adapter);

        self::assertSame($stages, $service->getDealStages());
        $request = $this->history[0]['request'];
        self::assertSame('GET', $request->getMethod());
        self::assertSame('https://api.hubapi.com/crm/pipelines/2026-09/deals', (string)$request->getUri());
        self::assertSame('Bearer test-access-token', $request->getHeaderLine('Authorization'));
        self::assertSame('Xentral-ERP-CRM', $request->getHeaderLine('User-Agent'));
    }

    /** @dataProvider requestMethods */
    public function testEveryRequestEntryPointUsesBearerAuthentication(string $method, bool $apiCall): void
    {
        $this->handler->append(new Response(200, [], '{}'));
        $client = $this->createClient('explicit-test-token');
        $data = $method === 'get' || $method === 'read' ? ['archived' => 'false'] : ['label' => 'Test'];
        if ($apiCall) {
            $client->apiCall('getPipelineProperties', $method, $data, ['deals']);
        } else {
            $client->setResource('getPipelineProperties')->{$method}($data, ['deals']);
        }

        $request = $this->history[0]['request'];
        self::assertSame(strtoupper($method === 'read' ? 'get' : $method), $request->getMethod());
        self::assertSame('Bearer explicit-test-token', $request->getHeaderLine('Authorization'));
        self::assertStringNotContainsString('explicit-test-token', (string)$request->getUri());
        self::assertStringNotContainsString('hapikey', (string)$request->getUri());
        if ($method === 'get' || $method === 'read') {
            self::assertSame('archived=false', $request->getUri()->getQuery());
        } elseif (in_array($method, ['post', 'put', 'patch'], true)) {
            self::assertSame($data, json_decode((string)$request->getBody(), true));
        }
    }

    public static function requestMethods(): array
    {
        return [
            ['read', false], ['get', false], ['post', false], ['put', false], ['patch', false], ['delete', false],
            ['get', true], ['post', true], ['put', true], ['patch', true], ['delete', true],
        ];
    }

    public function testTokenRotationOnSharedTransportUsesCurrentCredential(): void
    {
        $this->handler->append(new Response(200, [], '{}'), new Response(200, [], '{}'));
        $configuration = $this->createMock(HubspotConfigurationService::class);
        $configuration->expects(self::exactly(2))->method('getDecryptedConfiguration')
            ->with(HubspotConfigurationService::HUBSPOT_SALT_CONF_NAME)
            ->willReturnOnConsecutiveCalls('first-test-token', 'second-test-token');
        $client = new HubspotClientService($this->httpClient, $configuration);
        $client->setResource('getPipelineProperties')->read([], ['deals']);
        $client->read([], ['deals']);

        self::assertSame('Bearer first-test-token', $this->history[0]['request']->getHeaderLine('Authorization'));
        self::assertSame('Bearer second-test-token', $this->history[1]['request']->getHeaderLine('Authorization'));
    }

    public function testAuthorizationDoesNotPersistOnSharedTransport(): void
    {
        $this->handler->append(new Response(200, [], '{}'), new Response(200, [], '{}'));
        $this->createClient('test-token')->setResource('getPipelineProperties')->read([], ['deals']);
        $this->httpClient->get('https://example.invalid/');

        self::assertSame('', $this->history[1]['request']->getHeaderLine('Authorization'));
    }

    public function testExistingQueryParametersRemainIntact(): void
    {
        $this->handler->append(new Response(200, [], '{}'));
        $this->createClient('test-token')->setResource('getCompanies')->read(['limit' => 10]);
        $request = $this->history[0]['request'];

        self::assertStringContainsString('properties=name&properties=website', (string)$request->getUri());
        self::assertStringContainsString('&limit=10', (string)$request->getUri());
        self::assertSame('Bearer test-token', $request->getHeaderLine('Authorization'));
        self::assertStringNotContainsString('hapikey', (string)$request->getUri());
    }

    public function testMissingTokenStopsBeforeHttpRequest(): void
    {
        $this->expectException(HubspotException::class);
        $this->expectExceptionMessage('HubSpot access token is missing');
        try {
            $this->createClient('  ')->setResource('getPipelineProperties')->read([], ['deals']);
        } finally {
            self::assertSame([], $this->history);
        }
    }

    public function testEmptyPipelineListReturnsArray(): void
    {
        $this->queuePipelines([]);
        $service = $this->createDealService($this->createClient(), $this->createMock(AdapterInterface::class));

        self::assertSame([], $service->getDealStages());
    }

    public function testEmptyPipelineListDoesNotCreateLocalView(): void
    {
        $this->queuePipelines([]);
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->expects(self::never())->method('perform');
        $service = $this->createDealService($this->createClient(), $adapter);

        $service->installDealStages();
    }

    public function testPipelineErrorDoesNotCreateLocalView(): void
    {
        $this->handler->append(new Response(403, [], '{"status":"error","message":"Missing scopes"}'));
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->expects(self::never())->method('perform');
        $service = $this->createDealService($this->createClient(), $adapter);

        $this->expectException(HttpClientException::class);
        $service->installDealStages();
    }

    public function testFirstInstallCreatesViewAndStageMapping(): void
    {
        $this->queuePipelines([['id' => 'default', 'stages' => [
            ['id' => 'closedwon', 'label' => 'Closed won'],
        ]]]);
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->method('fetchAssoc')->willReturn([]);
        $adapter->method('fetchValue')->willReturn(false);
        $adapter->expects(self::exactly(2))->method('lastInsertId')->willReturnOnConsecutiveCalls(3, 11);
        $writes = [];
        $adapter->expects(self::exactly(3))->method('perform')
            ->willReturnCallback(static function ($query, array $values) use (&$writes) {
                $writes[] = [$query, $values];
            });
        $service = $this->createDealService($this->createClient(), $adapter);

        $service->installDealStages();

        self::assertStringContainsString('INSERT INTO wiedervorlage_view', $writes[0][0]);
        self::assertStringContainsString('INSERT INTO wiedervorlage_stages', $writes[1][0]);
        self::assertSame(1, $writes[1][1]['position']);
        self::assertSame(3, $writes[1][1]['wiedervorlage_view_id']);
        self::assertStringContainsString('INSERT INTO hs_mapping_leads', $writes[2][0]);
        self::assertSame('closedwon', $writes[2][1]['value']);
        self::assertSame(11, $writes[2][1]['wstage_id']);
    }

    public function testInstallsNewStageAndPreservesExistingMapping(): void
    {
        $this->queuePipelines([['id' => 'default', 'stages' => [
            ['id' => 'appointmentscheduled', 'label' => 'Appointment scheduled'],
            ['id' => '123456789', 'label' => 'Custom stage'],
        ]]]);
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->method('fetchAssoc')->willReturn([['wiedervorlage_stage_id' => 7]]);
        $adapter->method('fetchValue')->willReturnCallback(static function ($query, array $values) {
            if (strpos($query, 'SELECT ws.view') !== false) {
                return 3;
            }
            if (strpos($query, 'SELECT MAX(ws.sort)') !== false) {
                return 4;
            }
            if (strpos($query, 'SELECT hm.id') !== false) {
                self::assertSame('deals', $values['type']);
                self::assertContains($values['value'], ['appointmentscheduled', '123456789']);
                return $values['value'] === 'appointmentscheduled' ? 9 : false;
            }
            self::fail('Unexpected database lookup: ' . $query);
        });
        $adapter->method('lastInsertId')->willReturn(11);
        $writes = [];
        $adapter->expects(self::exactly(2))->method('perform')
            ->willReturnCallback(static function ($query, array $values) use (&$writes) {
                $writes[] = [$query, $values];
            });
        $service = $this->createDealService($this->createClient(), $adapter);

        $service->installDealStages();

        self::assertStringContainsString('INSERT INTO wiedervorlage_stages', $writes[0][0]);
        self::assertSame('Custom stage', $writes[0][1]['name']);
        self::assertSame(5, $writes[0][1]['position']);
        self::assertSame(3, $writes[0][1]['wiedervorlage_view_id']);
        self::assertStringContainsString('INSERT INTO hs_mapping_leads', $writes[1][0]);
        self::assertSame([
            'label' => 'Custom stage', 'value' => '123456789', 'type' => 'deals',
            'wstage_id' => 11, 'is_system' => 1, 'view_id' => 3,
        ], $writes[1][1]);
    }

    private function createClient(?string $token = null): HubspotClientService
    {
        $configuration = $this->createMock(HubspotConfigurationService::class);
        $configuration->expects($token === null ? self::once() : self::never())
            ->method('getDecryptedConfiguration')
            ->with(HubspotConfigurationService::HUBSPOT_SALT_CONF_NAME)
            ->willReturn('test-access-token');

        return new HubspotClientService($this->httpClient, $configuration, $token);
    }

    private function createDealService(HubspotClientService $client, AdapterInterface $adapter): HubspotDealPropertyService
    {
        $database = new Database($adapter, new QueryFactory('mysql'));

        return new HubspotDealPropertyService($client, $database, new HubspotContactPropertyGateway($database));
    }

    private function queuePipelines(array $pipelines): void
    {
        $this->handler->append(new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'results' => $pipelines,
        ])));
    }
}
