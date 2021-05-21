<?php

declare(strict_types=1);

namespace Xentral\Components\Logger\Handler;

use Xentral\Components\Logger\Exception\InvalidArgumentException;
use Xentral\Components\Logger\LogLevel;

abstract class AbstractLogHandler implements LogHandlerInterface
{
    /** @var int[] LEVEL_ORDER */
    protected const LEVEL_ORDER = [
        LogLevel::EMERGENCY => 8,
        LogLevel::ALERT     => 7,
        LogLevel::CRITICAL  => 6,
        LogLevel::ERROR     => 5,
        LogLevel::WARNING   => 4,
        LogLevel::NOTICE    => 3,
        LogLevel::INFO      => 2,
        LogLevel::DEBUG     => 1,
    ];

    /** @var string $minimumLevel */
    protected $minimumLevel;

    /**
     * @param string $level
     *
     * @return void
     */
    protected function setMinimumLevel(string $level): void
    {
        if (!array_key_exists($level, self::LEVEL_ORDER)) {
            throw new InvalidArgumentException(sprintf('Unrecognised loglevel "%s".', $level));
        }

        $this->minimumLevel = $level;
    }

    /**
     * @param string $level
     *
     * @return bool
     */
    public function canHandle(string $level): bool
    {
        if (!array_key_exists($level, self::LEVEL_ORDER)) {
            return false;
        }

        return self::LEVEL_ORDER[$level] >= self::LEVEL_ORDER[$this->minimumLevel];
    }
}
