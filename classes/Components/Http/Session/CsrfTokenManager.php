<?php

namespace Xentral\Components\Http\Session;

use Xentral\Components\Http\Exception\CsrfTokenException;
use Xentral\Components\Http\Exception\InvalidArgumentException;
use Xentral\Components\Util\StringUtil;

final class CsrfTokenManager
{
    /** @var int CSRF_TOKEN_LENGTH */
    const CSRF_TOKEN_LENGTH = 32;
    /** @var array $tokenData */
    private $tokenData;

    /**
     * @param array $tokenData
     */
    public function __construct($tokenData = [])
    {
        $this->tokenData = $tokenData;
    }

    /**
     * @param string $key
     * @param string $value
     * @param bool   $remove
     *
     * @return bool
     */
    public function isTokenValid($key, $value, $remove = true)
    {
        $this->ensureTokenKeyFormat($key);
        $valid = false;
        if (array_key_exists($key, $this->tokenData) && $this->tokenData[$key] === $value) {
            $valid = true;
        }
        if ($valid === true && $remove === true) {
            $this->removeToken($key);
        }

        return $valid;
    }

    /**
     * @param $key
     *
     * @throws CsrfTokenException
     *
     * @return string
     */
    public function createToken($key)
    {
        $this->ensureTokenKeyFormat($key);
        $token = StringUtil::random(self::CSRF_TOKEN_LENGTH, true);
        $this->tokenData[$key] = $token;

        if (strlen($token) < self::CSRF_TOKEN_LENGTH) {
            throw new CsrfTokenException('Could not create CSRF token.');
        }

        return $token;
    }

    /**
     * @param $key
     *
     * @return void
     */
    public function removeToken($key)
    {
        $this->ensureTokenKeyFormat($key);
        unset($this->tokenData[$key]);
    }

    /**
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public function refreshToken($key)
    {
        $this->ensureTokenKeyFormat($key);
        $this->removeToken($key);

        return $this->createToken($key);
    }

    /**
     * @param $target
     *
     * @return void
     */
    public function dumpTokens(&$target)
    {
        $target = $this->tokenData;
    }

    /**
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private function ensureTokenKeyFormat($key)
    {
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $key)) {
            throw new InvalidArgumentException('Invalid token key format.');
        }
    }
}
