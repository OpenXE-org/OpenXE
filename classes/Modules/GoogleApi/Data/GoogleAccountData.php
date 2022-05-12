<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleApi\Data;

use Xentral\Modules\GoogleApi\Exception\InvalidArgumentException;

final class GoogleAccountData
{
    /** @var int */
    private $id;

    /** @var int */
    private $userId;

    /** @var string $refreshToken */
    private $refreshToken;

    /** @var string $identifier */
    private $identifier;

    /**
     * @param int|null               $id
     * @param int                    $userId
     * @param string|null            $identifier
     * @param string|null            $refreshToken
     */
    public function __construct(
        ?int $id,
        int $userId,
        ?string $identifier,
        string $refreshToken = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->identifier = $identifier;
        $this->refreshToken = $refreshToken;
    }

    /**
     * @param array $dataSet
     *
     * @return GoogleAccountData
     */
    public static function fromDbState($dataSet): GoogleAccountData
    {
        if (!isset($dataSet['user_id'], $dataSet['id'])) {
            throw new InvalidArgumentException('Invalid or incomplete Dataset.');
        }

        return new self(
            $dataSet['id'],
            $dataSet['user_id'],
            $dataSet['identifier'],
            $dataSet['refresh_token']
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'            => $this->getId(),
            'user_id'       => $this->getUserId(),
            'identifier'    => $this->getIdentifier(),
            'refresh_token' => $this->getRefreshToken(),
        ];
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }
}
