<?php

declare(strict_types=1);

namespace Xentral\Modules\Calendar\Data;

use Xentral\Modules\Calendar\Exception\InvalidArgumentException;

final class CalendarEventUser
{
    /** @var int $id */
    private $id;

    /** @var int $eventId */
    private $eventId;

    /** @var int $userId */
    private $userId;

    /** @var int $groupId */
    private $groupId;

    /** @var string $email */
    private $email;

    /** @var int $addressId */
    private $addressId;

    /**
     * @param int    $id
     * @param int    $eventId
     * @param int    $userId
     * @param int    $groupId
     * @param int    $addressId
     * @param string $email
     */
    public function __construct(
        int $id = 0,
        int $eventId = 0,
        int $userId = 0,
        int $groupId = 0,
        int $addressId = 0,
        string $email = ''
    ) {
        $this->id = $id;
        $this->eventId = $eventId;
        $this->userId = $userId;
        $this->groupId = $groupId;
        $this->addressId = $addressId;
        $this->email = $email;
    }

    /**
     * @param array $data
     *
     * @throws InvalidArgumentException
     *
     * @return CalendarEventUser
     */
    public static function fromDbState(array $data): CalendarEventUser
    {
        if (!isset($data['id'], $data['event_id'], $data['user_id'], $data['group_id'])) {
            throw new InvalidArgumentException('Invalid or incomplete dataset.');
        }
        $instance = new self();
        $instance->setId((int)$data['id']);
        $instance->setEventId((int)$data['event_id']);
        $instance->setUserId((int)$data['user_id']);
        $instance->setGroupId((int)$data['group_id']);
        if (array_key_exists('address_id', $data)) {
            $instance->setAddressId((int)$data['address_id']);
        }
        if (isset($data['email']) && $data['email'] !== '') {
            $instance->setEmail($data['email']);
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'       => $this->getId(),
            'event_id' => $this->getEventId(),
            'user_id'  => $this->getUserId(),
            'group_id' => $this->getGroupId(),
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getEventId(): int
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     */
    public function setEventId(int $eventId): void
    {
        $this->eventId = $eventId;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     */
    public function setGroupId(int $groupId): void
    {
        $this->groupId = $groupId;
    }

    /**
     * @return int
     */
    public function getAddressId(): int
    {
        return $this->addressId;
    }

    /**
     * @param int $addressId
     */
    public function setAddressId(int $addressId): void
    {
        $this->addressId = $addressId;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
