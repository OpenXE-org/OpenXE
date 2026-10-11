<?php

declare(strict_types=1);

namespace Xentral\Modules\Office365Api\Data;

final class Office365AccountData
{
    /** @var int */
    private $id;

    /** @var int */
    private $userId;

    /** @var string|null */
    private $identifier;

    /** @var string|null */
    private $refreshToken;

    /** @var string|null */
    private $tenantId;

    public function __construct(
        int $id,
        int $userId,
        ?string $identifier = null,
        ?string $refreshToken = null,
        ?string $tenantId = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->identifier = $identifier;
        $this->refreshToken = $refreshToken;
        $this->tenantId = $tenantId;
    }

    public static function fromDbRow(array $row): self
    {
        return new self(
            (int)$row['id'],
            (int)$row['user_id'],
            $row['identifier'] ?? null,
            $row['refresh_token'] ?? null,
            $row['tenant_id'] ?? null
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }

    public function hasRefreshToken(): bool
    {
        return $this->refreshToken !== null && $this->refreshToken !== '';
    }
}
