<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data;

use stdClass;

class ErrorMessage
{
    /** @var string $code */
    private $code;

    /** @var string $message */
    private $message;

    /**
     * ErrorMessage constructor.
     *
     * @param string $code
     * @param string $message
     */
    public function __construct(string $code, string $message)
    {
        $this->setCode($code);
        $this->setMessage($message);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self($apiResult->code, $apiResult->message);
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        return new self($dbState['code'], $dbState['message']);
    }

    public function toArray(): array
    {
        return [
            'code'    => $this->getCode(),
            'message' => $this->getMessage(),
        ];
    }

    public function toApiResult(): stdClass
    {
        $apiResult = new stdClass();
        $apiResult->code = $this->getCode();
        $apiResult->message = $this->getMessage();

        return $apiResult;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
