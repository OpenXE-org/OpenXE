<?php

declare(strict_types=1);

namespace Xentral\Modules\Ebay\Client;

use DateTime;
use GuzzleHttp\Psr7\Request;
use Xentral\Modules\Ebay\Data\AccountCredentialsData;
use Xentral\Modules\Ebay\Data\TokenData;

class EbayRestApiClient
{

    public const TOKEN_TYPE_APPLICATION = 'Application Access Token';
    public const TOKEN_TYPE_USER = 'User Access Token';
    public const DEFAULT_ORDER_IMPORT_LIMIT = 50;

    protected $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param AccountCredentialsData $accountCredentialsData
     *
     * @return mixed
     */
    public function getRestApiApplicationAccessTokenFromEbay(AccountCredentialsData $accountCredentialsData): array
    {
        $headers = [
            'Content-Type'  => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . base64_encode(
                    $accountCredentialsData->getClientId() . ':' . $accountCredentialsData->getClientSecret()
                ),
        ];

        $scope = ['https://api.ebay.com/oauth/api_scope'];
        $body = [
            'grant_type' => 'client_credentials',
            'scope'      => implode(' ', $scope),
        ];
        $request = new Request(
            'POST',
            'https://api.ebay.com/identity/v1/oauth2/token',
            $headers,
            http_build_query($body)
        );

        $response = $this->client->send($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getRestApiUserAccessTokenFromEbay(AccountCredentialsData $accountCredentialsData, string $code): array
    {
        $headers = [
            'Content-Type'  => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . base64_encode(
                    $accountCredentialsData->getClientId() . ':' . $accountCredentialsData->getClientSecret()
                ),
        ];
        $body = [
            'grant_type'   => 'authorization_code',
            'code'         => $code,
            'redirect_uri' => $accountCredentialsData->getRedirectUrl(),
        ];
        $request = new Request(
            'POST',
            'https://api.ebay.com/identity/v1/oauth2/token',
            $headers,
            http_build_query($body)
        );

        $response = $this->client->send($request);

        return json_decode($response->getBody()->getContents(), true);
    }


    /**
     * @param int    $siteId
     * @param int    $categoryId
     * @param string $token
     *
     * @return array
     */
    public function getCategorySpecificProperties(int $siteId, int $categoryId, string $token): array
    {
        $headers = [
            'Authorization' => 'Bearer ' . $token,
        ];

        $url = sprintf(
            'https://api.ebay.com/commerce/taxonomy/v1_beta/category_tree/%d/get_item_aspects_for_category?category_id=%d',
            $siteId,
            $categoryId
        );

        $request = new Request(
            'GET',
            $url,
            $headers
        );

        $response = $this->client->send($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getOrders(string $token, DateTime $dateFrom, int $offset, ?int $limit): array
    {
        $headers = [
            'Authorization' => 'Bearer ' . $token,
        ];
        $dateFromString = $dateFrom->format('Y-m-d\TH:i:s');

        $url = 'https://api.ebay.com/sell/fulfillment/v1/order?';
        $url .= http_build_query([
            'offset' => $offset,
            'fieldGroups' => 'TAX_BREAKDOWN',
            'limit' => ($limit ?: self::DEFAULT_ORDER_IMPORT_LIMIT),
            'filter' => "lastmodifieddate:[{$dateFromString}.000Z..],orderfulfillmentstatus:{NOT_STARTED|IN_PROGRESS}",
        ]);
        $request = new Request(
            'GET',
            $url,
            $headers
        );
        $response = $this->client->send($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function renewToken(AccountCredentialsData $accountCredentialsData, TokenData $tokenData): array
    {
        $headers = [
            'Content-Type'  => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . base64_encode(
                    $accountCredentialsData->getClientId() . ':' . $accountCredentialsData->getClientSecret()
                ),
        ];

        $scope = ['https://api.ebay.com/oauth/api_scope'];
        if ($tokenData->getType() === self::TOKEN_TYPE_USER) {
            $scope = $this->getCompleteScope();
        }

        $body = [
            'grant_type'    => 'refresh_token',
            'scope'         => implode(' ', $scope),
            'refresh_token' => $tokenData->getRefreshToken(),
        ];
        $request = new Request(
            'POST',
            'https://api.ebay.com/identity/v1/oauth2/token',
            $headers,
            http_build_query($body)
        );

        $response = $this->client->send($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getCompleteScope(): array
    {
        return [
            'https://api.ebay.com/oauth/api_scope',
            'https://api.ebay.com/oauth/api_scope/sell.marketing.readonly',
            'https://api.ebay.com/oauth/api_scope/sell.marketing',
            'https://api.ebay.com/oauth/api_scope/sell.inventory.readonly',
            'https://api.ebay.com/oauth/api_scope/sell.inventory',
            'https://api.ebay.com/oauth/api_scope/sell.account.readonly',
            'https://api.ebay.com/oauth/api_scope/sell.account',
            'https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly',
            'https://api.ebay.com/oauth/api_scope/sell.fulfillment',
            'https://api.ebay.com/oauth/api_scope/sell.analytics.readonly',
            'https://api.ebay.com/oauth/api_scope/sell.finances',
            'https://api.ebay.com/oauth/api_scope/sell.payment.dispute',
            'https://api.ebay.com/oauth/api_scope/commerce.identity.readonly',
        ];
    }
}
