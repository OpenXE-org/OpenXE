<?php
namespace Y0lk\OAuth1\Client\Server;

use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Server\Server;
use League\OAuth1\Client\Server\User;

class Etsy extends Server
{
    const API_URL = 'https://openapi.etsy.com/v2/';

    /**
     * Application scope.
     *
     * @var string
     */
    protected $applicationScope = "";

    /**
     * Login url for authorization provided by Etsy
     * @var string
     */
    protected $login_url = "";


    /**
     * {@inheritDoc}
     */
    public function __construct($clientCredentials, SignatureInterface $signature = null)
    {
        parent::__construct($clientCredentials, $signature);
        if (is_array($clientCredentials)) {
            $this->parseConfiguration($clientCredentials);
        }
    }

    /**
     * Set the application scope.
     *
     * @param string $applicationScope
     *
     * @return Etsy
     */
    public function setApplicationScope($applicationScope)
    {
        $this->applicationScope = $applicationScope;
        return $this;
    }

    /**
     * Get application scope.
     *
     * @return string
     */
    public function getApplicationScope()
    {
        return $this->applicationScope;
    }

    /**
     * {@inheritDoc}
     */
    public function urlTemporaryCredentials()
    {
        return self::API_URL.'oauth/request_token?scope='.$this->applicationScope;
    }
    /**
     * {@inheritDoc}
     */
    public function urlAuthorization()
    {
        return $this->login_url;
    }
    /**
     * {@inheritDoc}
     */
    public function urlTokenCredentials()
    {
        return self::API_URL.'oauth/access_token';
    }
    /**
     * {@inheritDoc}
     */
    public function urlUserDetails()
    {
        return self::API_URL.'users/__SELF__';
    }

    /**
     * {@inheritDoc}
     */
    public function userDetails($data, TokenCredentials $tokenCredentials)
    {
        $data = $data['results'][0];

        $user = new User();
        $user->uid = $data['user_id'];
        $user->nickname = $data['login_name'];

        $used = array('user_id', 'login_name');

        // Save all extra data
        $user->extra = array_diff_key($data, array_flip($used));
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function userUid($data, TokenCredentials $tokenCredentials)
    {
        return $data['user']['user_id'];
    }

    /**
     * {@inheritDoc}
     */
    public function userEmail($data, TokenCredentials $tokenCredentials)
    {
        return;
    }

    /**
     * {@inheritDoc}
     */
    public function userScreenName($data, TokenCredentials $tokenCredentials)
    {
        return $data['user']['login_name'];
    }

    /**
     * Parse configuration array to set attributes.
     *
     * @param array $configuration
     */
    private function parseConfiguration(array $configuration = array())
    {
        $configToPropertyMap = array(
            'scope' => 'applicationScope'
        );
        foreach ($configToPropertyMap as $config => $property) {
            if (isset($configuration[$config])) {
                $this->$property = $configuration[$config];
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getTemporaryCredentials()
    {
        $uri = $this->urlTemporaryCredentials();

        $client = $this->createHttpClient();

        $header = $this->temporaryCredentialsProtocolHeader($uri);
        $authorizationHeader = array('Authorization' => $header);
        $headers = $this->buildHttpClientHeaders($authorizationHeader);

        try {
            $response = $client->post($uri, [
                'headers' => $headers
            ]);
        } catch (BadResponseException $e) {
            return $this->handleTemporaryCredentialsBadResponse($e);
        }

        //Catch body and retrieve Etsy login_url
        $body = $response->getBody();
        parse_str($body, $data);

        $this->login_url = $data['login_url'];

        return $this->createTemporaryCredentials($response->getBody());
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthorizationUrl($temporaryIdentifier, array $options = [])
    {
        // Somebody can pass through an instance of temporary
        // credentials and we'll extract the identifier from there.
        if ($temporaryIdentifier instanceof TemporaryCredentials) {
            $temporaryIdentifier = $temporaryIdentifier->getIdentifier();
        }

        //Return the authorization url directly since it's provided by Etsy and contains all parameters
        return $this->urlAuthorization();
    }
}