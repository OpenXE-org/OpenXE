<?php

declare(strict_types=1);

namespace Xentral\Components\Logger\Context;

use DateTimeInterface;
use Throwable;

interface ContextInterface
{
    public function getTime(): DateTimeInterface;

    public function getClass(): ?string;

    public function getFunction(): ?string;

    public function getLine(): int;

    public function hasOrigin(): bool;

    public function getOriginType(): ?string;

    public function getOriginDetail(): ?string;

    public function hasException(): bool;

    public function getException(): ?Throwable;

    public function hasDump(): bool;

    public function getDump(): array;
}
