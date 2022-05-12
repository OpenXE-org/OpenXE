<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleApi\Data;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Xentral\Modules\GoogleApi\Exception\InvalidArgumentException;

final class GoogleTokenResponseData
{
    /** @var string $accessToken */
    private $accessToken;

    /** @var string[] $scopes */
    private $scopes;

    /** @var string|null $tokenType */
    private $tokenType;

    /** @var string|null $refreshToken */
    private $refreshToken;

    /** @var DateTimeImmutable $expirationDate */
    private $expirationDate;

    /**
     * @param string      $accessToken
     * @param int         $expiresIn
     * @param array       $scopes
     * @param string|null $tokenType
     * @param string|null $refreshToken
     */
    private function __construct(
        string $accessToken,
        int $expiresIn,
        array $scopes,
        string $tokenType,
        ?string $refreshToken = null
    ) {
        $this->accessToken = $accessToken;
        $this->scopes = $scopes;
        $this->tokenType = $tokenType;
        $this->refreshToken = $refreshToken;
        $this->setExpiration($expiresIn);
    }

    /**
     * @param array $data
     *
     * @throws InvalidArgumentException
     *
     * @return GoogleTokenResponseData
     */
    public static function createfromResponseArray(array $data): GoogleTokenResponseData
    {
        if (!isset($data['access_token'], $data['expires_in'], $data['scope'], $data['token_type'])) {
            throw new InvalidArgumentException('Invalid token response.');
        }
        $scopes = explode(' ', $data['scope']);
        $obj = new static(
            $data['access_token'],
            $data['expires_in'],
            $scopes,
            $data['token_type']
        );
        if (array_key_exists('token_type', $data)) {
            $obj->tokenType = $data['token_type'];
        }
        if (array_key_exists('refresh_token', $data)) {
            $obj->refreshToken = $data['refresh_token'];
        }

        return $obj;
    }

    /**
     * @return bool
     */
    public function hasRefreshToken(): bool
    {
        return !empty($this->refreshToken);
    }

    /**
     * @return DateTimeInterface
     */
    public function getExpirationDate(): DateTimeInterface
    {
        return $this->expirationDate;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return string[]
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @return string
     */
    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * @param int $expiresIn
     *
     * @return void
     */
    private function setExpiration(int $expiresIn): void
    {
        $this->expirationDate = new DateTimeImmutable('now');
        $interval = new DateInterval(sprintf('PT%sS', $expiresIn));
        $this->expirationDate = $this->expirationDate->add($interval);
    }
}
