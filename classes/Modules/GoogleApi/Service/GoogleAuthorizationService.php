<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleApi\Service;

use Exception;
use Xentral\Components\Http\RedirectResponse;
use Xentral\Components\Http\Request;
use Xentral\Components\Http\Session\Session;
use Xentral\Components\HttpClient\Exception\ClientErrorException;
use Xentral\Components\HttpClient\Exception\ServerErrorException;
use Xentral\Components\HttpClient\Exception\TransferErrorExceptionInterface;
use Xentral\Components\HttpClient\HttpClient;
use Xentral\Components\HttpClient\HttpClientInterface;
use Xentral\Components\HttpClient\Request\ClientRequest;
use Xentral\Components\Logger\LoggerAwareTrait;
use Xentral\Components\Util\StringUtil;
use Xentral\Modules\GoogleApi\Data\GoogleAccessTokenData;
use Xentral\Modules\GoogleApi\Data\GoogleAccountData;
use Xentral\Modules\GoogleApi\Data\GoogleCredentialsData;
use Xentral\Modules\GoogleApi\Data\GoogleTokenResponseData;
use Xentral\Modules\GoogleApi\Exception\AuthorizationExpiredException;
use Xentral\Modules\GoogleApi\Exception\CsrfViolationException;
use Xentral\Modules\GoogleApi\Exception\GoogleAccountNotFoundException;
use Xentral\Modules\GoogleApi\Exception\GoogleCredentialsException;
use Xentral\Modules\GoogleApi\Exception\InvalidArgumentException;
use Xentral\Modules\GoogleApi\Exception\NoAccessTokenException;
use Xentral\Modules\GoogleApi\Exception\NoRefreshTokenException;
use Xentral\Modules\GoogleApi\Exception\UserConsentException;

final class GoogleAuthorizationService
{
    use LoggerAwareTrait;

    /** @var string SESSION_SEGMENT */
    private const SESSION_SEGMENT = 'googleapiauth';

    /** @var string CSRF_KEY */
    private const CSRF_KEY = 'google_user_authorization';

    /** @var string SESSION_KEY_URI */
    private const SESSION_KEY_URI = 'uri_after_authorization';

    /** @var string URL_AUTHORIZATION_CODE */
    private const URL_AUTHORIZATION_CODE = 'https://accounts.google.com/o/oauth2/auth';

    /** @var string URL_TOKEN_FETCH */
    private const URL_TOKEN_FETCH = 'https://accounts.google.com/o/oauth2/token';

    /** @var string URL_TOKEN_REFRESH */
    private const URL_TOKEN_REFRESH = 'https://www.googleapis.com/oauth2/v3/token';

    /** @var string URL_TOKEN_REVOKE */
    private const URL_TOKEN_REVOKE = 'https://accounts.google.com/o/oauth2/revoke';

    /** @var GoogleAccountGateway $gateway */
    private $gateway;

    /** @var GoogleAccountService $service */
    private $service;

    /** @var HttpClient $httpClient */
    private $httpClient;

    /** @var string $baseUrl */
    private $baseUrl;

    /** @var GoogleCredentialsData $credentials */
    private $credentials;

    /**
     * @param GoogleAccountGateway  $gateway
     * @param GoogleAccountService  $service
     * @param HttpClientInterface   $httpClient
     * @param GoogleCredentialsData $credentials
     * @param string                $requestBaseUrl
     */
    public function __construct(
        GoogleAccountGateway $gateway,
        GoogleAccountService $service,
        HttpClientInterface $httpClient,
        GoogleCredentialsData $credentials,
        string $requestBaseUrl
    ) {
        $this->gateway = $gateway;
        $this->service = $service;
        $this->httpClient = $httpClient;
        $this->baseUrl = $requestBaseUrl;
        $this->credentials = $credentials;
    }

    /**
     * @param Session   $session
     * @param string[]  $scopes
     * @param string    $uriAfterRedirect
     *
     * @throws InvalidArgumentException
     * @throws GoogleCredentialsException
     *
     * @return RedirectResponse
     */
    public function requestScopeAuthorization(
        Session $session,
        array $scopes = [],
        string $uriAfterRedirect = 'index.php?module=welcome&action=settings'
    ): RedirectResponse {
        if (count($scopes) === 0) {
            throw new InvalidArgumentException('No scopes for Google authorization defined.');
        }
        $this->credentials->validate();
        $clientId = $this->credentials->getClientId();
        $session->setValue(self::SESSION_SEGMENT, self::SESSION_KEY_URI, $uriAfterRedirect);
        $csrfToken = $session->createCsrfToken(self::CSRF_KEY);
        $redirectUri = $this->credentials->getRedirectUri();
        if ($redirectUri === null || $redirectUri === '') {
            $redirectUri = $this->getDefaultRedirectUri();
        }
        $scopeParam = implode(' ', $scopes);
        $queryParams = [
            'client_id'              => $clientId,
            'redirect_uri'           => $redirectUri,
            'response_type'          => 'code',
            'scope'                  => $scopeParam,
            'access_type'            => 'offline',
            'include_granted_scopes' => 'true',
            'state'                  => $csrfToken,
        ];
        $url = sprintf('%s?%s', self::URL_AUTHORIZATION_CODE, http_build_query($queryParams));

        return RedirectResponse::createFromUrl($url);
    }

    /**
     * @param Session $session
     * @param Request $request
     * @param int     $userId
     *
     * @throws Exception
     *
     * @return RedirectResponse
     */
    public function authorizationCallback(Session $session, Request $request, int $userId): RedirectResponse
    {
        $code = $request->get->get('code');
        $scopes = explode(' ', $request->get->get('scope', ''));
        $error = $request->get->get('error');
        $csrfToken = $request->get->get('state');
        if (
            $csrfToken === null
            || !$session->isCsrfTokenValid(self::CSRF_KEY, $csrfToken, true)
        ) {
            throw new CsrfViolationException('Invalid CSRF token in authorization.');
        }

        // error in callback means the user declined access
        if ($error !== null) {
            $this->logger->error(
                'User consent rejected by "user_id={user}" original error: "{error}"',
                ['user_id' => $userId, 'error' => $error]
            );
            throw new UserConsentException($error);
        }

        // find/create account
        try {
            $account = $this->gateway->getAccountByUser($userId);
        } catch (GoogleAccountNotFoundException $e) {
            $account = $this->service->createAccount($userId, null);
        }

        // store granted scopes
        $this->service->deleteAccountScopes($account->getId());
        foreach ($scopes as $scope) {
            $this->service->saveAccountScope($account->getId(), $scope);
        }

        // fetch and save refresh token
        $array = $this->fetchTokenByAuthCode($code);
        $tokenResponse = GoogleTokenResponseData::createfromResponseArray($array);
        if ($tokenResponse->hasRefreshToken()) {
            $account = new GoogleAccountData(
                $account->getId(),
                $account->getUserId(),
                $account->getIdentifier(),
                $tokenResponse->getRefreshToken()
            );
            $this->service->saveAccount($account);
        }

        // cache access token
        $accessToken = new GoogleAccessTokenData(
            $account->getId(),
            $tokenResponse->getAccessToken(),
            $tokenResponse->getExpirationDate()
        );
        $this->service->saveAccessToken($accessToken);

        // read redirect uri from session
        $redirectUri = $session->getValue(
            self::SESSION_SEGMENT,
            self::SESSION_KEY_URI,
            'index.php?module=googleapi&action=edit',
            true
        );

        return RedirectResponse::createFromUrl($redirectUri);
    }

    /**
     * @param GoogleAccountData $account
     *
     * @throws NoRefreshTokenException
     * @throws GoogleCredentialsException
     * @throws AuthorizationExpiredException
     *
     * @return GoogleAccessTokenData
     */
    public function refreshAccessToken(GoogleAccountData $account): GoogleAccessTokenData
    {
        $this->credentials->validate();
        $refresh_token = $account->getRefreshToken();
        if ($refresh_token === null) {
            $this->logger->warning(
                'User "id={user_id} has no Google refresh token.',
                ['user_id' => $account->getUserId()]
            );
            try {
                $refresh_token = $this->gateway->getAccessToken($account->getId())->getToken();
            } catch (NoAccessTokenException $e) {
                throw new NoRefreshTokenException('Account not authorized.');
            }
        }
        $postData = [
            'refresh_token' => $refresh_token,
            'client_id'     => $this->credentials->getClientId(),
            'client_secret' => $this->credentials->getClientSecret(),
            'grant_type'    => 'refresh_token',
        ];
        try {
            $array = $this->apiRequest('POST', self::URL_TOKEN_REFRESH, $postData);
        } catch (ClientErrorException $e) {
            $this->logger->error(
                'Fetching new Google access token failed. Repeat the Authorization process!',
                ['exception' => $e]
            );
            $this->revokeAuthorization($account);
            throw new AuthorizationExpiredException(
                'Failed to fetch access token. Try to repeat the Google authorization process.',
                $e->getCode(),
                $e
            );
        }
        $tokenResponse = GoogleTokenResponseData::createfromResponseArray($array);
        $accessToken = new GoogleAccessTokenData(
            $account->getId(),
            $tokenResponse->getAccessToken(),
            $tokenResponse->getExpirationDate()
        );
        $this->service->saveAccessToken($accessToken);

        return $accessToken;
    }

    /**
     * @param GoogleAccountData $account
     *
     * @return GoogleAccountData
     */
    public function revokeAuthorization(GoogleAccountData $account): GoogleAccountData
    {
        try {
            $accessToken = $this->gateway->getAccessToken($account->getId());
            $this->revokeToken($accessToken->getToken());
            $this->service->deleteAccessToken($accessToken);
        } catch (NoAccessTokenException $e) {
        }
        if ($account->getRefreshToken() !== null) {
            $this->revokeToken($account->getRefreshToken());
            $account = new GoogleAccountData(
                $account->getId(),
                $account->getUserId(),
                $account->getIdentifier(),
                null
            );
            $this->service->saveAccount($account);
        }
        $this->service->deleteAccountScopes($account->getId());

        return $account;
    }

    /**
     * @return string
     */
    public function getDefaultRedirectUri(): string
    {
        return sprintf('%s/index.php?module=googleapi&action=redirect', $this->baseUrl);
    }

    /**
     * @param string $token refresh_token or access_token
     *
     * @return bool success
     */
    public function revokeToken(string $token): bool
    {
        $url = sprintf('%s?token=%s', self::URL_TOKEN_REVOKE, $token);
        try {
            $this->apiRequest('GET', $url, null, []);
        } catch (ClientErrorException $e) {
            return true;
        } catch (ServerErrorException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $authorizationCode
     *
     * @throws GoogleCredentialsException
     *
     * @return array
     */
    private function fetchTokenByAuthCode(string $authorizationCode): array
    {
        $redirectUri = $this->credentials->getRedirectUri();
        if (empty($redirectUri)) {
            $redirectUri = $this->getDefaultRedirectUri();
        }
        $this->credentials->validate();
        $postData = [
            'code'          => $authorizationCode,
            'client_id'     => $this->credentials->getClientId(),
            'client_secret' => $this->credentials->getClientSecret(),
            'redirect_uri'  => $redirectUri,
            'grant_type'    => 'authorization_code',
        ];

        return $this->apiRequest('POST', self::URL_TOKEN_FETCH, $postData, []);
    }

    /**
     * @param string     $method
     * @param string     $url
     * @param array|null $data
     * @param array      $headers
     *
     * @throws ClientErrorException
     * @throws ServerErrorException
     *
     * @return array
     */
    private function apiRequest($method, $url, $data = null, $headers = []): array
    {
        $requestBody = null;
        if ($data !== null) {
            $headers['Content-Type'] = 'application/json';
            $requestBody = json_encode($data);
        }

        $request = new ClientRequest($method, $url, $headers, $requestBody);
        try {
            $response = $this->httpClient->sendRequest($request);
            $this->logger->debug(
                'Google authorization request succeeded: {uri}',
                ['uri' => $request->getUri(), 'request' => $request, 'response' => $response]
            );
        } catch (TransferErrorExceptionInterface $e) {
            $code = $e->getCode();
            $this->logger->warning(
                'Google authorization request failed: {uri} ERROR {code}',
                [
                    'uri'      => $request->getUri(),
                    'code'     => $code,
                    'request'  => $request,
                    'response' => $e->getResponse(),
                ]
            );
            if ($code > 399 && $code < 500) {
                throw new ClientErrorException($e->getMessage(), $e->getCode(), $e);
            }
            if ($code > 499 && $code < 600) {
                throw new ServerErrorException($e->getMessage(), $e->getCode(), $e);
            }
        }

        $contentType = $response->getHeaderLine('content-type');
        $responseBody = $response->getBody()->getContents();
        $result = [];
        if ($responseBody !== '' && StringUtil::startsWith($contentType, 'application/json')) {
            $result = json_decode($responseBody, true);
        }

        return $result;
    }
}
