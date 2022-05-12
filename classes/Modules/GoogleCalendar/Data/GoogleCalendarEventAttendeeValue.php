<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Data;

use Xentral\Modules\GoogleCalendar\Exception\InvalidArgumentException;

final class GoogleCalendarEventAttendeeValue
{
    /** @var string STATUS_ACCEPTED */
    public const STATUS_ACCEPTED = 'accepted';

    /** @var string STATUS_TENTATIVE */
    public const STATUS_TENTATIVE = 'tentative';

    /** @var string STATUS_NEEDSACTION */
    public const STATUS_NEEDSACTION = 'needsAction';

    /** @var string STATUS_DECLINED */
    public const STATUS_DECLINED = 'declined';

    /** @var string $email */
    private $email;

    /** @var bool $self */
    private $self;

    /** @var string $displayName */
    private $displayName;

    /** @var string $identifier */
    private $identifier;

    /** @var string $responseStatus */
    private $responseStatus;

    /** @var bool $optional */
    private $optional;

    /**
     * @param string $email
     * @param string $displayName
     * @param bool   $optional
     * @param string $identifier
     * @param string $responseStatus
     * @param bool   $self
     */
    public function __construct(
        string $email,
        string $displayName = '',
        bool $optional = false,
        string $identifier = '',
        string $responseStatus = '',
        bool $self = false
    ) {
        if ($email === '' || $email === null) {
            throw new InvalidArgumentException('Email address is required.');
        }
        $this->email = $email;
        $this->self = $self;
        $this->identifier = $identifier;
        $this->displayName = $displayName;
        $this->responseStatus = $responseStatus;
        $this->optional = $optional;
    }

    /**
     * @param array $data
     *
     * @return GoogleCalendarEventAttendeeValue
     */
    public static function createFromJsonArray(array $data): GoogleCalendarEventAttendeeValue
    {
        if (!isset($data['email'])) {
            throw new InvalidArgumentException('Invalid data format.');
        }
        $email = $data['email'];
        $self = false;
        if (isset($data['self'])) {
            $self = (bool)$data['self'];
        }
        $displayName = '';
        if (isset($data['displayName'])) {
            $displayName = $data['displayName'];
        }
        $id = '';
        if (isset($data['id'])) {
            $id = $data['id'];
        }
        $responseStatus = '';
        if (isset($data['responseStatus'])) {
            $responseStatus = $data['responseStatus'];
        }

        return new self($email, $displayName, false, $id, $responseStatus, $self);
    }

    /**
     * @return array
     */
    public function toDataArray(): array
    {
        $data = [];
        $data['email'] = $this->getEmail();
        $data['displayName'] = $this->getDisplayName();
        $data['optional'] = $this->isOptional();
        if ($this->getIdentifier() !== '') {
            $data['id'] = $this->getIdentifier();
        }
        if ($this->responseStatus !== '') {
            $data['responseStatus'] = $this->responseStatus;
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function isSelf(): bool
    {
        return $this->self;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        $display = $this->displayName;
        if ($display === '' || $display === null) {
            $display = $this->email;
        }

        return $display;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return bool
     */
    public function isAttending(): bool
    {
        return (
            $this->responseStatus === self::STATUS_ACCEPTED
            || $this->responseStatus === self::STATUS_TENTATIVE
            || $this->responseStatus === self::STATUS_NEEDSACTION
        );
    }

    /**
     * @return bool
     */
    public function isOptional(): bool
    {
        return $this->optional;
    }
}
