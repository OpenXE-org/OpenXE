<?php

declare(strict_types=1);

namespace Xentral\Components\Logger;

use Xentral\Components\Logger\Context\ContextHelper;
use Xentral\Components\Logger\Handler\MemoryLogHandler;

final class MemoryLogger extends AbstractLogger
{
    /** @var MemoryLogHandler $handler */
    private $handler;

    /** @var Logger $logger */
    private $logger;

    /**
     * MemoryLogger constructor.
     *
     * @param ContextHelper $contextHelper
     */
    public function __construct(ContextHelper $contextHelper)
    {
        $this->handler = new MemoryLogHandler('debug');
        $this->logger = new Logger($contextHelper);
        $this->logger->pushHandler($this->handler);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->handler->getMessages();
    }

    /**
     * @return array
     */
    public function getContexts(): array
    {
        $contextArray = [];
        foreach ($this->handler->getContexts() as $context) {
            $contextArray[] = $context->getDump();
        }

        return $contextArray;
    }
}
