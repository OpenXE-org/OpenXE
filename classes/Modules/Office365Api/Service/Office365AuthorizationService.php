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
            $scopes = [
                'https://outlook.office.com/SMTP.Send',
                'https://outlook.office.com/IMAP.AccessAsUser.All',
                'https://outlook.office.com/POP.AccessAsUser.All',
                'offline_access'
            ];
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
        string $tenantId = null,
        string $emailAddress = null
    ): Office365AccountData {
        $credentials = $this->credentialsService->getCredentials();
        $tenantId = $tenantId ?? $credentials->getTenantId();

        $tokenResponse = $this->requestAccessToken($code, $credentials);

        $existingAccountId = 0;
        if (!empty($emailAddress)) {
            $existing = $this->gateway->getAccountByEmailAddress($emailAddress);
            if ($existing !== null) {
                $existingAccountId = $existing->getId();
            }
        }
        if ($existingAccountId === 0) {
            $existing = $this->gateway->getAccountByUserId($userId);
            if ($existing !== null) {
                $existingAccountId = $existing->getId();
            }
        }

        $accountData = new Office365AccountData(
            $existingAccountId,
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

        $scopes = [
            'https://outlook.office.com/SMTP.Send',
            'https://outlook.office.com/IMAP.AccessAsUser.All',
            'https://outlook.office.com/POP.AccessAsUser.All',
            'offline_access'
        ];
        foreach ($scopes as $scope) {
            $this->gateway->saveAccountScope($accountId, $scope);
        }

        return $accountData;
    }

    public function refreshAccessToken(Office365AccountData $account): Office365AccessTokenData
    {
        $currentAccount = $this->gateway->getAccount($account->getId()) ?? $account;

        if (!$currentAccount->hasRefreshToken()) {
            throw new NoRefreshTokenException('No refresh token available for account');
        }

        $credentials = $this->credentialsService->getCredentials();

        $postData = [
            'client_id' => $credentials->getClientId(),
            'client_secret' => $credentials->getClientSecret(),
            'refresh_token' => $currentAccount->getRefreshToken(),
            'grant_type' => 'refresh_token',
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

        $this->gateway->saveAccessToken($currentAccount->getId(), $accessTokenData);

        if (!empty($tokenResponse->getRefreshToken()) && $tokenResponse->getRefreshToken() !== $currentAccount->getRefreshToken()) {
            $updatedAccount = new Office365AccountData(
                $currentAccount->getId(),
                $currentAccount->getUserId(),
                $currentAccount->getIdentifier(),
                $tokenResponse->getRefreshToken(),
                $currentAccount->getTenantId()
            );
            $this->gateway->saveAccount($updatedAccount);
        }

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
            'scope' => 'https://outlook.office.com/SMTP.Send https://outlook.office.com/IMAP.AccessAsUser.All https://outlook.office.com/POP.AccessAsUser.All offline_access',
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
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new Office365OAuthException('Microsoft OAuth request failed: ' . $curlError);
        }

        $decoded = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $decoded = [];
        }

        if ($httpCode >= 400) {
            $errorMsg = $decoded['error_description'] ?? $decoded['error'] ?? ('HTTP ' . $httpCode);
            throw new Office365OAuthException('Microsoft OAuth request failed: ' . $errorMsg);
        }

        return $decoded;
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
