<?php

namespace FiskalyClient;

use Datto\JsonRpc\Http\Client;
use Datto\JsonRpc\Http\Exceptions\HttpException;
use Exception;
use FiskalyClient\errors\exceptions\FiskalyClientException;
use FiskalyClient\errors\exceptions\FiskalyHttpException;
use FiskalyClient\errors\exceptions\FiskalyHttpTimeoutException;
use FiskalyClient\errors\FiskalyErrorHandler;
use FiskalyClient\responses\ClientConfiguration;
use FiskalyClient\responses\RequestResponse;
use FiskalyClient\responses\VersionResponse;
use FiskalyClient\responses\SelfTestResponse;
use TypeError;

/**
 * fiskaly API client Class
 * @package FiskalyClient
 */
class FiskalyClient
{
    /** @var string */
    const SDK_VERSION = '1.2.100';

    /** @var string */
    private $context = '';

    /** @var Client */
    private $json_rpc = null;


    /**
     * FiskalyClient constructor.
     * @param string $fiskaly_service
     * @throws Exception
     */
    private function __construct($fiskaly_service)
    {
        $this->json_rpc = new Client($fiskaly_service);
    }


    /**
     * Send JSON RPC Request to Client
     * @param $method
     * @param $params
     * @return mixed
     * @throws Exception
     */
    private function doRequest($method, $params)
    {
        try {
            $response = null;
            $this->json_rpc->query($method, $params, $response)->send();
            return $response;
        } catch (Exception | HttpException | TypeError $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * FiskalyClient Credentials Constructor - Construct an instance of the fiskaly API client Class
     * @param string $fiskaly_service
     * @param $api_key
     * @param $api_secret
     * @param string $base_url
     * @param string email
     * @param string password
     * @param string organization_id
     * @param string environment
     * @return FiskalyClient
     * @throws Exception
     */
    public static function createUsingCredentials($fiskaly_service, $api_key = '', $api_secret = '', $base_url, $email = '', $password = '', $organization_id = '', $environment = '')
    {
        if (empty($fiskaly_service)) {
            throw new Exception("fiskaly_service must be provided");
        }

        if (empty($email)) {
            if (empty($api_key)) {
                throw new Exception("api_key must be provided");
            }

            if (empty($api_secret)) {
                throw new Exception("api_secret must be provided");
            }
        } else if (empty($password)) {
            throw new Exception("password must be provided in combination with email");
        }


        if (empty($base_url)) {
            throw new Exception("base_url must be provided");
        }

        $instance = new self($fiskaly_service);
        $instance->createContext(trim($api_key), trim($api_secret), trim($base_url), trim($email), trim($password), trim($organization_id), trim($environment));
        return $instance;
    }

    /**
     * FiskalyClient Context constructor - Construct an instance of the fiskaly API client Class using context string
     * @param string $context - Context base64 encoded string
     * @param string $fiskaly_service - Fiskaly service
     * @return FiskalyClient
     * @throws Exception
     */
    public static function createUsingContext($fiskaly_service, $context)
    {
        if (empty($fiskaly_service)) {
            throw new Exception("fiskaly_service must be provided");
        }

        if (empty($context)) {
            throw new Exception("context must be provided");
        }

        $instance = new self($fiskaly_service);
        $instance->updateContext($context);
        return $instance;
    }


    /**
     * Create context
     * @param $api_key
     * @param $api_secret
     * @param $base_url
     * @throws Exception
     */
    private function createContext($api_key, $api_secret, $base_url, $email, $password, $organization_id, $environment)
    {
        $contextParams = [
            'base_url' => $base_url,
            'api_key' => $api_key,
            'api_secret' => $api_secret,
            'email' => $email,
            'password' => $password, 
            'organization_id' => $organization_id,
            'environment' => $environment,
            'sdk_version' => self::SDK_VERSION
        ];

        $response = $this->doRequest('create-context', $contextParams);
        $this->updateContext($response['context']);
    }


    /**
     * Update Context
     * @param $context - New base64 encoded context
     */
    private function updateContext($context)
    {
        $this->context = $context;
    }


    /**
     * Get Context
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }


    /**
     * Get fiskaly client configuration
     * @return ClientConfiguration
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @throws Exception
     */
    public function getConfig()
    {
        $params = [
            'context' => $this->context
        ];
        $response = $this->doRequest('config', $params);

        /** Check if error exists */
        FiskalyErrorHandler::throwOnError($response);

        $config = $response['config'];

        return new ClientConfiguration($config['debug_level'], $config['debug_file'], $config['client_timeout'], $config['smaers_timeout'], $config['http_proxy']);
    }


    /**
     * Get Version
     * @return VersionResponse - version information of the currently used client and SMAERS.
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @throws Exception
     */
    public function getVersion()
    {
        $response = $this->doRequest('version', null);

        /** Check if error exists */
        FiskalyErrorHandler::throwOnError($response);

        $client = $response['client'];
        $smaers = $response['smaers'];

        return new VersionResponse($client['version'], $client['source_hash'], $client['commit_hash'], $smaers['version']);
    }

    /**
     * SelfTest
     * @return SelfTestResponse - response of the selftest
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @throws Exception
     */
    public function selfTest()
    {
        $params = [
            'context' => $this->context
        ];

        $response = $this->doRequest('self-test', $params);

        /** Check if error exists */
        FiskalyErrorHandler::throwOnError($response);

        $proxy = $response['proxy'];
        $backend = $response['backend'];
        $smaers = $response['smaers'];

        return new SelfTestResponse($proxy, $backend, $smaers);
    }


    /**
     * Configure the client
     * @param $config_params array
     * @return ClientConfiguration|null - Client configuration
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @throws Exception
     */
    public function configure($config_params)
    {
        $params = [
            'config' => $config_params,
            'context' => $this->context
        ];

        $response = $this->doRequest('config', $params);

        /** Check if error exists */
        FiskalyErrorHandler::throwOnError($response);

        /** Update context */
        $this->updateContext($response['context']);

        $config = $response['config'];

        return new ClientConfiguration($config['debug_level'], $config['debug_file'], $config['client_timeout'], $config['smaers_timeout'], $config['http_proxy']);
    }

    
    /**
     * Execute the request
     * @param string $method
     * @param string $path
     * @param null $query
     * @param null $headers
     * @param string $body - Base64 encoded JSON.
     * @param string $destination_file - To store the response body in a file, provide a value to the destination_file property.
     * @return RequestResponse
     * @throws Exception
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     */
    public function request($method = 'GET', $path = '/', $query = null, $headers = null, $body = null, $destination_file = '')
    {
        $request_data = [
            'method' => $method,
            'path' => $path
        ];

        if (!empty($query)) {
            $request_data['query'] = $query;
        }

        if (!empty($headers)) {
            $request_data['headers'] = $headers;
        }

        if (!empty($body)) {
            $request_data['body'] = $body;
        }

        if (!empty($destination_file)) {
            $request_data['destination_file'] = $destination_file;
        }

        $params = [
            'request' => $request_data,
            'context' => $this->context
        ];

        $response = $this->doRequest('request', $params);

        /** Check if error exists */
        FiskalyErrorHandler::throwOnError($response);

        $request_response = new RequestResponse($response['response'], $response['context']);

        /** Update context */
        $this->updateContext($request_response->getContext());

        return $request_response;
    }
}
