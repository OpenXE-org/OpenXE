<?php

namespace Xentral\Modules\EtsyApi;

use Exception;
use Xentral\Modules\EtsyApi\Credential\ClientCredentialData;
use Xentral\Modules\EtsyApi\Credential\TemporaryCredentialData;
use Xentral\Modules\EtsyApi\Credential\TokenCredentialData;
use League\OAuth1\Client\Credentials\TemporaryCredentials as LeagueTemporaryCredentials;
use League\OAuth1\Client\Credentials\TokenCredentials as LeagueTokenCredentials;
use Xentral\Modules\EtsyApi\Exception\EtsyOAuthException;
use Y0lk\OAuth1\Client\Server\Etsy as Y0lkEtsy;

final class EtsyOAuthHelper
{
    /** @var Y0lkEtsy $server */
    private $server;

    /**
     * @param ClientCredentialData $clientCredentials
     * @param string               $callbackUri
     * @param array|string[]       $scopes
     *
     * @throws EtsyOAuthException
     */
    public function __construct(ClientCredentialData $clientCredentials, $callbackUri, array $scopes = [])
    {
        try {
            $this->server = new Y0lkEtsy([
                'identifier'   => $clientCredentials->getIdentifier(),
                'secret'       => $clientCredentials->getSecret(),
                'callback_uri' => $callbackUri,
                'scope'        => implode(' ', $scopes),
            ]);
        } catch (Exception $exception) {
            throw new EtsyOAuthException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @throws EtsyOAuthException
     *
     * @return TemporaryCredentialData
     */
    public function getTemporaryCredentials()
    {
        try {
            /** @var LeagueTemporaryCredentials $temp */
            $temp = $this->server->getTemporaryCredentials();
        } catch (Exception $exception) {
            throw new EtsyOAuthException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return new TemporaryCredentialData(
            $temp->getIdentifier(),
            $temp->getSecret()
        );
    }

    /**
     * @param TemporaryCredentialData $temporaryCredentials
     * @param string                  $temporaryIdentifier
     * @param string                  $verifier
     *
     * @throws EtsyOAuthException
     *
     * @return TokenCredentialData
     */
    public function getTokenCredentials(TemporaryCredentialData $temporaryCredentials, $temporaryIdentifier, $verifier)
    {
        $leagueTemp = new LeagueTemporaryCredentials();
        $leagueTemp->setIdentifier($temporaryCredentials->getIdentifier());
        $leagueTemp->setSecret($temporaryCredentials->getSecret());

        try {
            $leagueToken = $this->server->getTokenCredentials($leagueTemp, $temporaryIdentifier, $verifier);
            $tokenCredentials = new TokenCredentialData($leagueToken->getIdentifier(), $leagueToken->getSecret());
        } catch (Exception $exception) {
            throw new EtsyOAuthException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $tokenCredentials;
    }

    /**
     * @param TemporaryCredentialData $temporaryCredentials
     *
     * @throws EtsyOAuthException
     *
     * @return void
     */
    public function authorize(TemporaryCredentialData $temporaryCredentials)
    {
        $leagueCredentials = new LeagueTemporaryCredentials();
        $leagueCredentials->setIdentifier($temporaryCredentials->getIdentifier());
        $leagueCredentials->setSecret($temporaryCredentials->getSecret());

        try {
            $this->server->authorize($leagueCredentials);
        } catch (Exception $exception) {
            throw new EtsyOAuthException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param TemporaryCredentialData $temporaryCredentials
     *
     * @throws EtsyOAuthException
     *
     * @return string
     */
    public function getAuthorizationUrl(TemporaryCredentialData $temporaryCredentials)
    {
        $leagueCredentials = new LeagueTemporaryCredentials();
        $leagueCredentials->setIdentifier($temporaryCredentials->getIdentifier());
        $leagueCredentials->setSecret($temporaryCredentials->getSecret());

        try {
            return $this->server->getAuthorizationUrl($leagueCredentials);
        } catch (Exception $exception) {
            throw new EtsyOAuthException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param TokenCredentialData $tokenCredentials
     * @param string              $httpMethod
     * @param string              $url
     * @param array               $bodyParams
     *
     * @throws EtsyOAuthException
     *
     * @return array
     */
    public function getHeaders(TokenCredentialData $tokenCredentials, $httpMethod, $url, array $bodyParams = [])
    {
        $leagueCredentials = new LeagueTokenCredentials();
        $leagueCredentials->setIdentifier($tokenCredentials->getIdentifier());
        $leagueCredentials->setSecret($tokenCredentials->getSecret());

        try {
            return $this->server->getHeaders($leagueCredentials, $httpMethod, $url, $bodyParams);
        } catch (Exception $exception) {
            throw new EtsyOAuthException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
