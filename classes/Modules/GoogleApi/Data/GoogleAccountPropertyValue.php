<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleApi\Data;

use Xentral\Modules\GoogleApi\Exception\InvalidArgumentException;

final class GoogleAccountPropertyValue
{
    /** @var string $key */
    private $key;

    /** @var string $value */
    private $value;

    /** @var int|null */
    private $id;

    /** @var int|null */
    private $accountId;

    /**
     * @param int|null $id
     * @param int      $accountId
     * @param string   $key
     * @param string   $value
     */
    public function __construct(?int $id, ?int $accountId, string $key, string $value)
    {
        $this->id = $id;
        $this->accountId = $accountId;
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @param array $data
     *
     * @return GoogleAccountPropertyValue
     */
    public static function fromDbState(array $data): GoogleAccountPropertyValue
    {
        if (!isset($data['id'], $data['google_account_id'], $data['varname'], $data['value'])) {
            throw new InvalidArgumentException('Invalid or incomplete Dataset.');
        }

        return new static($data['id'], $data['google_account_id'], $data['varname'], $data['value']);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'google_account_id' => $this->getAccountId(),
            'varname' => $this->getKey(),
            'value' => $this->getValue()
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
     * @return int|null
     */
    public function getAccountId(): ?int
    {
        return $this->accountId;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
