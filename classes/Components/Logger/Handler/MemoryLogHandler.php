<?php

declare(strict_types=1);

namespace Xentral\Components\Logger\Handler;

use Xentral\Components\Logger\Context\ContextInterface;

final class MemoryLogHandler extends AbstractLogHandler
{
    /** @var string[] $messages */
    private $messages;

    /** @var ContextInterface[] $entries */
    private $contexts;

    /**
     * @param string $level
     */
    public function __construct(string $level)
    {
        $this->messages = [];
        $this->contexts = [];
        $this->setMinimumLevel($level);
    }

    /**
     * @param string                $level
     * @param string                $message
     * @param ContextInterface $context
     *
     * @return void
     */
    public function addLogEntry(string $level, string $message, ContextInterface $context): void
    {
        $this->contexts[] =  $context;
        $this->messages[] = sprintf('%s: %s', strtoupper($level), $message);
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @return ContextInterface[]
     */
    public function getContexts(): array
    {
        return $this->contexts;
    }
}
