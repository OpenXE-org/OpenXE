<?php

declare(strict_types=1);

namespace Xentral\Components\Logger\Context;

use DateTimeInterface;
use Exception;
use Throwable;

final class LoggerContext implements ContextInterface
{
    /** @var DateTimeInterface $dateTime */
    private $dateTime;

    /** @var Invocation $invocation */
    private $invocation;

    /** @var OriginInterface $origin */
    private $origin;

    /** @var Exception $exception */
    private $exception;

    /** @var array $dump */
    private $dump;

    /**
     * @param DateTimeInterface    $dateTime
     * @param Invocation|null      $invocation
     * @param OriginInterface|null $origin
     * @param Throwable|null       $exception
     * @param array|null           $dump
     */
    public function __construct(
        DateTimeInterface $dateTime,
        Invocation $invocation = null,
        OriginInterface $origin = null,
        Throwable $exception = null,
        array $dump = null
    ) {
        $this->dateTime = $dateTime;
        $this->invocation = $invocation;
        $this->origin = $origin;
        $this->exception = $exception;
        $this->dump = $dump;
    }

    /**
     * @return DateTimeInterface
     */
    public function getTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    /**
     * @return string|null
     */
    public function getClass(): ?string
    {
        if ($this->invocation === null) {
            return null;
        }

        return $this->invocation->getClass();
    }

    /**
     * @return string|null
     */
    public function getFunction(): ?string
    {
        if ($this->invocation === null) {
            return null;
        }

        return $this->invocation->getFunction();
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        if ($this->invocation === null) {
            return 0;
        }

        return $this->invocation->getLine();
    }

    /**
     * @return bool
     */
    public function hasOrigin(): bool
    {
        return $this->origin !== null;
    }

    /**
     * @return bool
     */
    public function hasException(): bool
    {
        return $this->exception !== null;
    }

    /**
     * @return Exception|null
     */
    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    /**
     * @return bool
     */
    public function hasDump(): bool
    {
        return is_array($this->dump) && count($this->dump) > 0;
    }

    /**
     * @return array
     */
    public function getDump(): array
    {
        if (!is_array($this->dump)) {
            return [];
        }

        return $this->dump;
    }

    /**
     * @return string|null
     */
    public function getOriginType(): ?string
    {
        if ($this->origin === null) {
            return null;
        }

        return $this->origin->getType();
    }

    /**
     * @return string|null
     */
    public function getOriginDetail(): ?string
    {
        if ($this->origin === null) {
            return null;
        }

        return $this->origin->getDetail();
    }
}
