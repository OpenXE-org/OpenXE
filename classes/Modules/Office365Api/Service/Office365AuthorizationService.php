<?php

declare(strict_types=1);

namespace Xentral\Modules\Office365Api\Service;

use DateTime;
use Xentral\Modules\Office365Api\Data\Office365AccessTokenData;
use Xentral\Modules\Office365Api\Data\Office365AccountData;
use Xentral\Modules\Office365Api\Data\Office365CredentialsData;
use Xentral\Modules\Office365Api\Data\Office365TokenResponseData;
use Xentral\Modules\Office365Api\Exception\AuthorizationExpiredException;
use Xentral\Modules\Office365Api\Exception\NoAccessTokenException;
use Xentral\Modules\Office365Api\Exception\NoRefreshTokenException;
use Xentral\Modules\Office365Api\Exception\Office365OAuthException;

final class Office365AuthorizationService
{
    /** @var Office365AccountGateway */
    private $gateway;

    /** @var Office365CredentialsService */
    private $credentialsService;

    public function __construct(
        Office365AccountGateway $gateway,
        Office365CredentialsService $credentialsService
    ) {
        $this->gateway = $gateway;
        $this->credentialsService = $credentialsService;
    }

    public function getAuthorizationUrl(array $scopes = [], string $state = ''): string
    {
        $credentials = $this->credentialsService->getCredentials();

        if (empty($scopes)) {
            $scopes = ['https://outlook.office365.com/.default', 'offline_access'];
        }

        $params = [
            'client_id' => $credentials->getClientId(),
            'redirect_uri' => $credentials->getRedirectUri(),
            'response_type' => 'code',
            'scope' => implode(' ', $scopes),
            'response_mode' => 'query',
            'prompt' => 'select_account',
        ];

        if (!empty($state)) {
            $params['state'] = $state;
        }

        $url = sprintf(
            'https://login.microsoftonline.com/%s/oauth2/v2.0/authorize?%s',
            urlencode($credentials->getTenantId()),
            http_build_query($params)
        );

        return $url;
    }

    public function authorizationCallback(
        string $code,
        int $userId,
        string $tenantId = null
    ): Office365AccountData {
        $credentials = $this->credentialsService->getCredentials();
        $tenantId = $tenantId ?? $credentials->getTenantId();

        $tokenResponse = $this->requestAccessToken($code, $credentials);

        $accountData = new Office365AccountData(
            0,
            $userId,
            null,
            $tokenResponse->getRefreshToken(),
            $tenantId
        );

        $accountId = $this->gateway->saveAccount($accountData);
        $accountData = new Office365AccountData(
            $accountId,
            $userId,
            null,
            $tokenResponse->getRefreshToken(),
            $tenantId
        );

        $this->gateway->saveAccessToken($accountId, $tokenResponse->toAccessTokenData());
        $this->gateway->saveAccountScope($accountId, 'https://outlook.office365.com/.default');

        return $accountData;
    }

    public function refreshAccessToken(Office365AccountData $account): Office365AccessTokenData
    {
        if (!$account->hasRefreshToken()) {
            throw new NoRefreshTokenException('No refresh token available for account');
        }

        $credentials = $this->credentialsService->getCredentials();

        $postData = [
            'client_id' => $credentials->getClientId(),
            'client_secret' => $credentials->getClientSecret(),
            'refresh_token' => $account->getRefreshToken(),
            'grant_type' => 'refresh_token',
            'scope' => 'https://outlook.office365.com/.default offline_access',
        ];

        $tokenUrl = sprintf(
            'https://login.microsoftonline.com/%s/oauth2/v2.0/token',
            urlencode($credentials->getTenantId())
        );

        $response = $this->postRequest($tokenUrl, $postData);

        if (empty($response['access_token'])) {
            throw new Office365OAuthException('Failed to refresh access token');
        }

        $tokenResponse = Office365TokenResponseData::fromArray($response);
        $accessTokenData = $tokenResponse->toAccessTokenData();

        $this->gateway->saveAccessToken($account->getId(), $accessTokenData);

        return $accessTokenData;
    }

    private function requestAccessToken(string $code, Office365CredentialsData $credentials): Office365TokenResponseData
    {
        $postData = [
            'client_id' => $credentials->getClientId(),
            'client_secret' => $credentials->getClientSecret(),
            'code' => $code,
            'redirect_uri' => $credentials->getRedirectUri(),
            'grant_type' => 'authorization_code',
            'scope' => 'https://outlook.office365.com/.default offline_access',
        ];

        $tokenUrl = sprintf(
            'https://login.microsoftonline.com/%s/oauth2/v2.0/token',
            urlencode($credentials->getTenantId())
        );

        $response = $this->postRequest($tokenUrl, $postData);

        if (empty($response['access_token'])) {
            throw new Office365OAuthException('Failed to obtain access token');
        }

        return Office365TokenResponseData::fromArray($response);
    }

    private function postRequest(string $url, array $postData): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            throw new Office365OAuthException('Microsoft OAuth request failed');
        }

        $decoded = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Office365OAuthException('Invalid JSON response from Microsoft');
        }

        return $decoded ?? [];
    }

    public function revokeAuthorization(Office365AccountData $account): void
    {
        // Microsoft doesn't have a simple revoke endpoint like Google
        // We just clear the refresh token from our database
        $accountToUpdate = new Office365AccountData(
            $account->getId(),
            $account->getUserId(),
            $account->getIdentifier(),
            null,
            $account->getTenantId()
        );

        $this->gateway->saveAccount($accountToUpdate);
    }
}
