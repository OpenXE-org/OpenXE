<?php

declare(strict_types=1);

namespace Xentral\Modules\Shopware6\Client;

use Xentral\Components\HttpClient\HttpClient;
use Xentral\Components\HttpClient\HttpClientInterface;
use Xentral\Components\HttpClient\Request\ClientRequest;
use Xentral\Modules\Shopware6\Data\PriceData;

final class Shopware6Client
{

    /** @var HttpClient */
    protected $client;
    /** @var string */
    protected $token;
    /** @var string */
    protected $userName;
    /** @var string */
    protected $password;
    /** @var string */
    protected $url;

    /** @var array */
    protected $knownGroupRuleIds = [];


    /**
     * Shopware6Client constructor.
     *
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function setCredentials(string $userName, string $password, string $url): void
    {
        $this->userName = $userName;
        $this->password = $password;
        $this->url = $url;
    }

    public function getGroupRuleId(string $groupName): string
    {
        if (array_key_exists($groupName, $this->knownGroupRuleIds)) {
            return $this->knownGroupRuleIds[$groupName];
        }

        $groupRuleData = $this->request(
            'GET',
            "rule?filter[rule.name]={$groupName}&sort=priority"
        );

        $groupRuleId = '';
        if (!empty($groupRuleData['data'][0]['id'])) {
            $groupRuleId = $groupRuleData['data'][0]['id'];
        }
        $this->knownGroupRuleIds[$groupName] = $groupRuleId;

        return $groupRuleId;
    }

    protected function request(string $method, string $endpoint, array $body = [], array $headerInformation = [])
    {
        $this->ensureAuthentication();
        $headerInformation['Content-Type'] = 'application/json';
        $headerInformation['Accept'] = 'application/json';
        $headerInformation['Authorization'] = 'Bearer ' . $this->token;

        $request = new ClientRequest(
            $method,
            $this->url . 'v2/' . $endpoint,
            $headerInformation,
            empty($body) ? null : json_encode($body)
        );

        $response = $this->client->sendRequest($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    protected function ensureAuthentication(): void
    {
        if ($this->token === null) {
            $this->getAuthenticationToken();
        }
    }

    protected function getAuthenticationToken(): void
    {
        $headerInformation = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $body = [
            'username'   => $this->userName,
            'password'   => $this->password,
            'grant_type' => 'password',
            'scopes'     => 'write',
            'client_id'  => 'administration',
        ];
        $request = new ClientRequest(
            'POST',
            $this->url . 'oauth/token',
            $headerInformation,
            json_encode($body)
        );

        $response = $this->client->sendRequest($request);
        $this->token = json_decode($response->getBody()->getContents(), true)['access_token'];
    }

    public function saveBulkPrice(string $productId, string $ruleId, string $currencyId, PriceData $priceData)
    {
        $data = [
            'productId'     => $productId,
            'quantityStart' => $priceData->getStartingQuantity(),
            'ruleId'        => $ruleId,
            'price'         => [
                [
                    'currencyId' => $currencyId,
                    'gross'      => $priceData->getGross(),
                    'net'        => $priceData->getNet(),
                    'linked'     => true,
                ],
            ],
        ];

        return $this->request(
            'POST',
            'product-price?_response=true',
            $data
        );
    }

    public function getAllSalesChannels()
    {
        return $this->request(
            'GET',
            'sales-channel'
        );
    }

}
