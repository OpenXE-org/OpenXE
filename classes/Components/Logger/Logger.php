<?php

declare(strict_types=1);

namespace Xentral\Components\Logger;

use Xentral\Components\Logger\Context\ContextHelper;
use Xentral\Components\Logger\Exception\InvalidArgumentException;
use Xentral\Components\Logger\Handler\LogHandlerInterface;

final class Logger extends AbstractLogger
{
    /** @var LogHandlerInterface[] $levelHandlers */
    private $logHandlers;

    /** @var ContextHelper $contextHelper */
    private $contextHelper;

    /**
     * @param ContextHelper $contextHelper
     */
    public function __construct(ContextHelper $contextHelper)
    {
        $this->contextHelper = $contextHelper;
        $this->logHandlers = [];
    }

    /**
     * @param LogHandlerInterface $handler
     *
     * @return void
     */
    public function pushHandler(LogHandlerInterface $handler): void
    {
        $this->logHandlers[] = $handler;
    }

    /**
     * @param string  $level
     * @param mixed $message
     * @param array  $context
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        if (!in_array($level, self::LOGLEVELS, true)) {
            throw new InvalidArgumentException(sprintf('Unrecognised loglevel "%s".', $level));
        }
        if (!is_string($message) && !method_exists($message, '__toString')) {
            throw new InvalidArgumentException('Cannot convert Message to String.');
        }
        if (count($this->logHandlers) === 0) {
            return;
        }
        $interpolated = $this->contextHelper->interpolateMessage($message, $context);
        $contextObj = $this->contextHelper->createContext($context);
        foreach ($this->logHandlers as $handler) {
            if ($handler->canHandle($level)) {
                $handler->addLogEntry($level, $interpolated, $contextObj);
            }
        }
    }
}
