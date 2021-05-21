<?php

declare(strict_types=1);

namespace Xentral\Components\Logger\Handler;

use Xentral\Components\Logger\Context\ContextInterface;

interface LogHandlerInterface
{
    /**
     * @param string                $level
     * @param string                $message
     * @param ContextInterface      $context
     *
     * @return void
     */
    public function addLogEntry(
        string $level,
        string $message,
        ContextInterface $context
    ): void;

    /**
     * @param string $level
     *
     * @return bool
     */
    public function canHandle(string $level): bool;
}
