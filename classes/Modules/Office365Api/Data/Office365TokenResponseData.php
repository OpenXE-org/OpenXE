<?php

declare(strict_types=1);

namespace Xentral\Modules\Office365Api\Data;

use DateTime;
use Xentral\Modules\Office365Api\Exception\Office365OAuthException;

final class Office365TokenResponseData
{
    /** @var string */
    private $accessToken;

    /** @var int */
    private $expiresIn;

    /** @var string|null */
    private $refreshToken;

    /** @var string */
    private $tokenType;

    public function __construct(string $accessToken, int $expiresIn, ?string $refreshToken = null, string $tokenType = 'Bearer')
    {
        $this->accessToken = $accessToken;
        $this->expiresIn = $expiresIn;
        $this->refreshToken = $refreshToken;
        $this->tokenType = $tokenType;
    }

    public static function fromArray(array $data): self
    {
        if (empty($data['access_token'])) {
            throw new Office365OAuthException('Missing access_token in OAuth response');
        }

        $accessToken = $data['access_token'];
        $expiresIn = (int)($data['expires_in'] ?? 3600);
        $refreshToken = $data['refresh_token'] ?? null;
        $tokenType = $data['token_type'] ?? 'Bearer';

        return new self($accessToken, $expiresIn, $refreshToken, $tokenType);
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getExpirationDateTime(): DateTime
    {
        $now = new DateTime();
        $now->modify("+{$this->expiresIn} seconds");

        return $now;
    }

    public function toAccessTokenData(): Office365AccessTokenData
    {
        return new Office365AccessTokenData(
            $this->accessToken,
            $this->getExpirationDateTime(),
            $this->tokenType
        );
    }
}
