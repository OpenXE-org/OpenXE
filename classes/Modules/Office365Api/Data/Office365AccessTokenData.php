<?php

declare(strict_types=1);

namespace Xentral\Modules\Office365Api\Data;

use DateTime;
use DateTimeInterface;

final class Office365AccessTokenData
{
    /** @var string */
    private $token;

    /** @var DateTimeInterface|null */
    private $expiresAt;

    /** @var string */
    private $tokenType;

    public function __construct(string $token, ?DateTimeInterface $expiresAt = null, string $tokenType = 'Bearer')
    {
        $this->token = $token;
        $this->expiresAt = $expiresAt;
        $this->tokenType = $tokenType;
    }

    public static function fromArray(array $data): self
    {
        $token = $data['token'] ?? '';
        $expiresAt = null;

        if (!empty($data['expires'])) {
            try {
                $expiresAt = new DateTime($data['expires']);
            } catch (\Exception $e) {
                $expiresAt = null;
            }
        }

        $tokenType = $data['token_type'] ?? 'Bearer';

        return new self($token, $expiresAt, $tokenType);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getTimeToLive(): int
    {
        if ($this->expiresAt === null) {
            return 0;
        }

        $now = new DateTime();
        $diff = $this->expiresAt->getTimestamp() - $now->getTimestamp();

        return max(0, $diff);
    }

    public function isExpired(): bool
    {
        return $this->getTimeToLive() <= 0;
    }

    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'expires' => $this->expiresAt ? $this->expiresAt->format('Y-m-d H:i:s') : null,
            'token_type' => $this->tokenType,
        ];
    }
}
