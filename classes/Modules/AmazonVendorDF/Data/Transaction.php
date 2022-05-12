<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

use DateTime;

class Transaction
{
    const STATUS_FAILURE = 'Failure';
    const STATUS_PROCESSING = 'Processing';
    const STATUS_SUCCESS = 'Success';
    const STATUS_WAITING = 'Waiting';
    const STATUS_CLOSED = 'Closed';

    /** @var int */
    private $id;
    /** @var string */
    private $externalId;
    /** @var string */
    private $subject;
    /** @var string */
    private $subject_id;
    /** @var string */
    private $status;
    /** @var array */
    private $errors;
    /** @var DateTime */
    private $created_at;
    /** @var DateTime */
    private $updated_at;


    public function __construct(string $subject = '')
    {
        $this->subject = $subject;
    }

    public function isWaiting(): bool
    {
        return $this->status === self::STATUS_WAITING;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function hasFailed(): bool
    {
        return $this->status === self::STATUS_FAILURE;
    }

    public function hasSucceeded(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getSubjectId(): ?string
    {
        return $this->subject_id;
    }

    public function setSubjectId(string $subject_id): self
    {
        $this->subject_id = $subject_id;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(DateTime $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
