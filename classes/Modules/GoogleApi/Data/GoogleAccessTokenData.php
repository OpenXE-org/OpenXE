<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleApi\Data;

use DateTime;
use DateTimeInterface;
use Xentral\Modules\GoogleApi\Exception\InvalidArgumentException;

final class GoogleAccessTokenData
{
    /** @var int $accountId */
    private $accountId;

    /** @var string $token */
    private $token;

    /** @var DateTimeInterface $expirationDate */
    private $expirationDate;

    /**
     * @param int               $accountId
     * @param string            $token
     * @param DateTimeInterface $expirationDate
     */
    public function __construct(
        int $accountId,
        string $token,
        DateTimeInterface $expirationDate
    ) {
        $this->accountId = $accountId;
        $this->token = $token;
        $this->expirationDate = $expirationDate;
    }

    /**
     * @param $data
     *
     * @throws InvalidArgumentException
     *
     * @return GoogleAccessTokenData
     */
    public static function fromDbState($data): GoogleAccessTokenData
    {
        if (!isset($data['google_account_id'], $data['token'], $data['expires'])) {
            throw new InvalidArgumentException('Invalid Token Data.');
        }
        $expires = DateTime::createFromFormat('Y-m-d H:i:s', $data['expires']);

        return new static($data['google_account_id'], $data['token'], $expires);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $expiration = null;
        if ($this->expirationDate !== null) {
            $expiration = $this->getExpirationDate()->format('Y-m-d H:i:s');
        }

        return [
            'google_account_id' => $this->getAccountId(),
            'token'             => $this->getToken(),
            'expires'           => $expiration,
        ];
    }

    /**
     * @return int
     */
    public function getTimeToLive(): int
    {
        $now = new DateTime();
        $diff = $this->expirationDate->getTimestamp() - $now->getTimestamp();
        if ($diff < 0) {
            $diff = 0;
        }

        return $diff;
    }

    /**
     * @return int
     */
    public function getAccountId(): int
    {
        return $this->accountId;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return DateTimeInterface
     */
    public function getExpirationDate(): DateTimeInterface
    {
        return $this->expirationDate;
    }
}
