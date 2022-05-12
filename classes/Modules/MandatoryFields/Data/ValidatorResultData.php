<?php

declare(strict_types=1);

namespace Xentral\Modules\MandatoryFields\Data;

final class ValidatorResultData
{
    /** @var bool $isError */
    private $isError;

    /** @var string $message */
    private $message;

    /**
     * @param bool   $isError
     * @param string $message
     */
    public function __construct(bool $isError, string $message)
    {
        $this->isError = $isError;
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->isError;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'error'   => $this->isError,
            'message' => $this->message,
        ];
    }
}
