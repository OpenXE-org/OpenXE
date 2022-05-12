<?php

namespace Xentral\Modules\Sipgate;

use Xentral\Modules\Sipgate\Exception\CurlException;
use Xentral\Modules\Sipgate\Exception\ResponseException;
use Xentral\Modules\Sipgate\Exception\UnauthorizedException;
use Xentral\Modules\Sipgate\Exception\InvalidArgumentException;

/**
 * @url https://developer.sipgate.io/rest-api/rtcm/
 * @url https://api.sipgate.com/v2/doc#/
 */
class SipgateRequest
{
    /** @var string API_BASE We'll use the v2 endpoint. */
    const API_BASE = 'https://api.sipgate.com/v2/';

    /** @var array The request header stay here as Key -> value pairs. */
    private $headers = [];

    /** @var string Basic auth string. */
    private $auth = '';

    /**
     * Uses Basic auth!
     *
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->auth = 'Basic ' . base64_encode("{$username}:{$password}");
    }

    /**
     * @throws ResponseException
     *
     * @return bool
     */
    public function ping()
    {
        $response = $this->getPingResponse();

        if ($response->getStatusCode() !== 200) {
            throw new ResponseException('API nicht erreichbar.');
        }
        $pong = $response->getBody();
        if (!is_array($pong) || !array_key_exists('ping', $pong) || $pong['ping'] !== 'pong') {
            throw new ResponseException('API nicht erreicht.');
        }

        return true;
    }

    /**
     * Check if API is reachable
     *
     * Expect:
     * status: 200
     * body:
     * {
     *  "ping": "pong"
     * }
     *
     * @throws InvalidArgumentException
     * @throws CurlException
     *
     * @return SipgateResponse
     */
    public function getPingResponse()
    {
        return $this->curl([
            CURLOPT_URL => 'ping',
        ]);
    }

    /**
     * @return SipgateResponse
     */
    public function getAccountResponse()
    {
        return $this->curl([
            CURLOPT_URL => 'account',
        ]);
    }

    /**
     * @param bool $checkVerified
     *
     * @throws ResponseException
     * @throws CurlException
     * @throws InvalidArgumentException
     *
     * @return array Example:
     *   [
     *     "company" => "Xentral ERP Software GmbH",
     *     "mainProductType" => "TEAM",
     *     "logoUrl" => ""
     *     "verified" => true
     *   ];
     */
    public function getAccount($checkVerified = true)
    {
        $response = $this->getAccountResponse();

        if ($response->getStatusCode() === 401) {
            throw new ResponseException('Zugangsdaten sind ung&uuml;ltig.');
        }
        if ($response->getStatusCode() === 404) {
            throw new ResponseException('Account nicht gefunden.');
        }

        $account = $response->getBody();
        if (!is_array($account)) {
            $type = gettype($account);

            throw new ResponseException(sprintf('Expected array, got %s', $type));
        }

        if (!array_key_exists('verified', $account)) {
            throw new ResponseException('Verified field is missing.');
        }

        if ($checkVerified && !$account['verified']) {
            throw new ResponseException('Account ist nicht verifiziert.');
        }

        return $account;
    }

    /**
     * @deprecated
     *
     * @param array  $arguments
     * @param string $url
     *
     * @throws InvalidArgumentException
     * @throws CurlException
     *
     * @return SipgateResponse
     */
    public function getRequest($url, $arguments = [])
    {
        if ($arguments) {
            $arguments = (array)$arguments;
            $arguments = array_filter($arguments, 'is_string');
            $arguments = array_filter($arguments, 'is_string', ARRAY_FILTER_USE_KEY);

            $argumentString = http_build_query($arguments);
            if (!empty($argumentString)) {
                $url = $url . '?' . $argumentString;
            }
        }

        return $this->curl([
            CURLOPT_URL => $url,
        ]);
    }

    /**
     * Initiate a new call
     *
     * DeviceId is only required if the caller parameter is a phone number and not a
     * deviceId itself.
     *
     * Use callerId to set a custom number that will be displayed to the callee.
     *
     * @see: https://api.sipgate.com/v2/doc#/sessions/newCall
     *
     * body:
     * {
     *  "deviceId": "e0",
     *  "caller": "e0",
     *  "callee": "+4915799912345",
     *  "callerId": "+4915799912345"
     * }
     *
     * returns:
     * 200:
     *  {
     *    "sessionId": "string"
     *  }
     * 400:
     *  User supplied invalid callee number
     *  User supplied invalid caller number
     *  DeviceId is required if caller is a phone number
     * 402:
     *  Insufficient funds
     * 403:
     *  User is not allowed to initiate call with given parameters
     *
     * @param string $caller
     * @param string $callee
     * @param array  $optional
     *
     * @throws InvalidArgumentException
     * @throws CurlException
     * @throws ResponseException
     *
     * @return string
     */
    public function startCall($caller, $callee, $optional = [])
    {
        $response = $this->startCallResponse($caller, $callee, $optional);

        if ($response->getStatusCode() !== 200) {
            $msg = $response->getPlainResult();

            throw new ResponseException($msg);
        }

        $body = $response->getBody();

        return $body['sessionId'];
    }

    /**
     * @param       $caller
     * @param       $callee
     * @param array $optional
     *
     * @return SipgateResponse
     */
    public function startCallResponse($caller, $callee, $optional = [])
    {
        $optional = (array)$optional;
        $optional = array_filter($optional, 'is_string');
        $optional = array_filter($optional, 'is_string', ARRAY_FILTER_USE_KEY);
        $allowed = ['deviceId', 'callerId'];
        $optional = array_intersect_key($optional, array_flip($allowed));
        $callee = preg_replace('/[^0-9+]/', '', $callee);

        $config = [
            'caller' => $caller,
            'callee' => $callee,
        ];

        $body = array_merge($optional, $config);

        return $this->curl([
            CURLOPT_URL        => '/sessions/calls',
            CURLOPT_POSTFIELDS => $body,
        ]);
    }

    /**
     * @param $config
     *
     * @throws InvalidArgumentException
     * @throws CurlException
     *
     * @return SipgateResponse
     */
    public function getHistory($config)
    {
        /*
         * If the value is an array, it's used as white list
         */
        $allowed = [
            'types'      => ['CALL', 'VOICEMAIL', 'SMS', 'FAX'],
            'directions' => ['INCOMING', 'OUTGOING', 'MISSED_INCOMING', 'MISSED_OUTGOING'],
            'offset'     => 0,
            'limit'      => 10,
            'archived'   => false,
        ];

        $config = array_intersect_key($config, array_flip(array_keys($allowed)));

        $query = [];
        foreach ($config as $key => $value) {
            $value = (array)$value;
            foreach ($value as $val) {
                if (!is_array($allowed[$key]) || in_array($val, $allowed[$key], true)) {
                    $query[] = urlencode($key) . '=' . urlencode($val);
                }
            }
        }
        $query = implode('&', $query);

        return $this->curl([
            CURLOPT_URL => '/history' . '?' . $query,
        ]);
    }

    /**
     * @return SipgateResponse
     */
    public function getMissedCalls()
    {
        $query = [
            'types'      => 'CALL',
            'directions' => [
                'MISSED_INCOMING',
                'MISSED_OUTGOING',
            ],
            'offset'     => 0,
            'limit'      => 10,
            'archived'   => false,
        ];

        return $this->getHistory($query);
    }

    /**
     * @return SipgateResponse
     */
    public function getBalanceResponse()
    {
        return $this->curl([
            CURLOPT_URL => '/balance',
        ]);
    }

    /**
     * @throws CurlException
     * @throws ResponseException
     * @throws InvalidArgumentException
     *
     * @return string like 3.50 Euro
     */
    public function getBalance()
    {
        $response = $this->getBalanceResponse();

        if ($response->getStatusCode() !== 200) {
            throw new ResponseException($response->getPlainResult());
        }

        $data = $response->getBody();

        if (!in_array('amount', $data, true)) {
            throw new ResponseException('Amount is missing.');
        }
        if (!in_array('currency', $data, true)) {
            throw new ResponseException('Currency is missing.');
        }

        $amount = $data['amount'];
        $amount = (int)$amount / 10000;
        $amount = round($amount, 2, PHP_ROUND_HALF_UP);
        $currency = $data['currency'];

        return sprintf('%s %s', $amount, $currency);
    }

    /**
     * @param $url
     *
     * @return SipgateResponse
     */
    public function registerWebHookUrlResponse($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('URL is not valid');
        }

        $body = [
            'incomingUrl' => $url,
            'outgoingUrl' => $url,
            'log'         => true,
        ];

        return $this->curl([
            CURLOPT_URL           => '/settings/sipgateio',
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS    => $body,
        ]);
    }

    /**
     * Register an endpoint to Sipgate.io
     *
     * @param string $url
     *
     * @throws InvalidArgumentException
     *
     * @return SipgateResponse
     */
    public function registerWebHookUrl($url)
    {
        $response = $this->registerWebHookUrlResponse($url);

        // status code 204: no content
        if (!in_array($response->getStatusCode(), [200, 204], true)) {
            $msg = $response->getPlainResult();
            throw new ResponseException($msg);
        }

        return $response;
    }

    /**
     * {
     * "data": [ {
     *    "callId": "ABCDEF0123456789",
     *    "muted": "false",
     *    "recording": "false",
     *    "hold": "false",
     *    "participants": [
     *      {
     *        "participantId": "ABCDEF0123456789",
     *        "phoneNumber": "+4915799912345",
     *        "muted": "false",
     *        "hold": "false",
     *        "owner": "false"
     *      }
     *    ]
     *  } ]
     * }
     *
     * @throws InvalidArgumentException
     * @throws CurlException
     *
     * @return SipgateResponse
     */
    public function getCurrentCallsResponse()
    {
        return $this->curl([
            CURLOPT_URL => '/calls/',
        ]);
    }

    /**
     * @deprecated
     *
     * @return SipgateResponse
     */
    public function getCurrentCalls()
    {
        return $this->getCurrentCallsResponse();
    }

    /**
     * @return SipgateResponse
     */
    public function getUsersResponse()
    {
        return $this->curl([
            CURLOPT_URL => '/users/',
        ]);
    }

    /**
     * @return array
     */
    public function getUsers()
    {
        $response = $this->getUsersResponse();

        if ($response->getStatusCode() === 401) {
            throw new UnauthorizedException('Zugangsdaten sind ungültig');
        }

        $users = $response->getBody();
        if (!array_key_exists('items', $users) || !is_array($users['items']) || !$users['items']) {
            throw new ResponseException('Kein API User gefunden');
        }

        $users = $users['items'];

        return $users;
    }

    /**
     * Run the http request.
     *
     * Requires at least the 'CURLOPT_URL' option set to the api path.
     * The API base is not required.
     *
     * If the body is set (CURLOPT_POSTFIELDS) it's required as string
     * or array. If it's an array, it will be encoded via json_encode.
     *
     * @param array $options
     *
     * @throws InvalidArgumentException
     * @throws CurlException
     *
     * @return SipgateResponse
     */
    protected function curl($options = [])
    {
        /*
         * Extract the url from the given options.
         */
        if (!array_key_exists(CURLOPT_URL, $options)) {
            throw new InvalidArgumentException('No URL given.');
        }
        $url = $options[CURLOPT_URL];
        $url = ltrim($url, '/');
        $url = self::API_BASE . $url;
        unset($options[CURLOPT_URL]);

        /*
         * If a body is set:
         * -> json_encode the array
         * -> append the content length.
         * -> set method to POST if no custom post is defined.
         */
        if (array_key_exists(CURLOPT_POSTFIELDS, $options)) {
            /*
             * 1. encode
             */
            $data = $options[CURLOPT_POSTFIELDS];
            if (is_array($data)) {
                $data = json_encode($data);
                if (json_last_error()) {
                    throw new InvalidArgumentException(json_last_error_msg());
                }
                $options[CURLOPT_POSTFIELDS] = $data;
                $options[CURLOPT_HTTPHEADER]['Content-Type'] = 'application/json';
            }
            if (!is_string($data)) {
                $type = gettype($data);
                throw new InvalidArgumentException('Body is required as string, got ' . $type);
            }

            /*
             * 2. Set content length
             */
            $options[CURLOPT_HTTPHEADER]['Content-Length'] = strlen($data);

            /*
             * 3. Set request type
             */
            if (!array_key_exists(CURLOPT_CUSTOMREQUEST, $options)) {
                $options[CURLOPT_POST] = true;
            }
        }

        /*
         * Extract the headers from given options.
         */
        $headers = $this->headers;
        if (array_key_exists(CURLOPT_HTTPHEADER, $options)) {
            $add = $options[CURLOPT_HTTPHEADER];
            unset($options[CURLOPT_HTTPHEADER]);
            $headers = $headers + (array)$add;
            unset($add);
        }

        $headers['accept'] = 'application/json';
        $headers['Authorization'] = $this->auth;
        $headers = $this->mergeHeader($headers);


        if (!function_exists('curl_init')) {
            throw new CurlException('Curl is not available');
        }
        $ch = curl_init();
        if (!$ch) {
            throw new CurlException('Cannot initialize curl');
        }

        $default = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_CONNECTTIMEOUT => 15,
        ];
        $final = [
            CURLOPT_URL        => $url,
            CURLOPT_HTTPHEADER => $headers,
        ];

        curl_setopt_array($ch, $default);
        curl_setopt_array($ch, $options);
        curl_setopt_array($ch, $final);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno || $error) {
            throw new CurlException("Curl ({$errno}): {$error}");
        }

        return new SipgateResponse($result, $info);
    }

    /**
     * Combine array keys with their value using $glue between
     * key & value. Used in 'curl' method to create the auth
     * header fields
     *
     * @param array  $opt
     * @param string $glue
     *
     * @return array
     */
    private function mergeHeader($opt, $glue = ': ')
    {
        $tmp = [];
        $opt = (array)$opt;
        $glue = (string)$glue;

        foreach ($opt as $key => $value) {
            $tmp[] = "{$key}{$glue}{$value}";
        }

        return $tmp;
    }
}
