<?php

declare(strict_types=1);

namespace Xentral\Modules\Ebay\Data;

final class TokenData
{
    /** @var string */
    protected $token;
    /** @var string */
    protected $refreshToken;
    /** @var string */
    protected $type;
    /** @var bool */
    protected $valid;

    /**
     * TokenData constructor.
     *
     * @param string $token
     * @param string $refreshToken
     * @param string $type
     * @param bool   $valid
     */
    public function __construct(string $token, string $refreshToken, string $type, bool $valid)
    {
        $this->token = $token;
        $this->refreshToken = $refreshToken;
        $this->type = $type;
        $this->valid = $valid;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return TokenData
     */
    public function setToken(string $token): TokenData
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     *
     * @return TokenData
     */
    public function setRefreshToken(string $refreshToken): TokenData
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return TokenData
     */
    public function setType(string $type): TokenData
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @param bool $valid
     *
     * @return TokenData
     */
    public function setValid(bool $valid): TokenData
    {
        $this->valid = $valid;

        return $this;
    }
}
