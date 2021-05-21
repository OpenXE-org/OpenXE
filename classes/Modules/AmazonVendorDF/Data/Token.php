<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

use DateTime;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;

class Token
{
    /** @var string */
    private $accessToken;

    /** @var string */
    private $refreshToken;

    /** @var DateTimeImmutable */
    private $expirationDate;

    public function __construct(string $accessToken, string $refreshToken, int $expiresInSeconds = 3600)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expirationDate = new DateTimeImmutable( "+{$expiresInSeconds} seconds");
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function isExpired(): bool
    {
        return new DateTime('now') > $this->expirationDate;
    }

    public static function fromResponse(ResponseInterface $response): self
    {
        $tokenInformation = json_decode($response->getBody()->getContents(), true);

        return new static($tokenInformation['access_token'], $tokenInformation['refresh_token'], $tokenInformation['expires_in']);
    }
}
