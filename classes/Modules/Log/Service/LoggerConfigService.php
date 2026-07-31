<?php

declare(strict_types=1);

namespace Xentral\Modules\Log\Service;

use Xentral\Components\Logger\LogLevel;
use Xentral\Modules\Log\Exception\InvalidArgumentException;
use Xentral\Modules\Log\Exception\InvalidLoglevelException;
use Xentral\Modules\SystemConfig\SystemConfigModule;

final class LoggerConfigService
{
    private const NAMESPACE = 'logger';
    private const CONFIG_KEY_LEVEL = 'log_level';

    public function __construct(private readonly SystemConfigModule $systemConfigModule)
    { }

    /**
     * @throws InvalidLoglevelException
     *
     * @return string
     */
    public function getLogLevel(): string
    {
        $level = $this->systemConfigModule->tryGetValue(self::NAMESPACE, self::CONFIG_KEY_LEVEL);
        if ($level === null) {
            $level = $this->systemConfigModule->tryGetLegacyValue('logfile_logging_level');
            $level ??= LogLevel::ERROR;
            $this->systemConfigModule->setValue(self::NAMESPACE, self::CONFIG_KEY_LEVEL, $level);
        }
        $level = strtolower($level);
        if (!$this->isAllowedLogLevel($level)) {
            throw new InvalidLoglevelException(sprintf('Unrecognized Loglevel "%s".', $level));
        }

        return $level;
    }

    /**
     * @param string $level
     *
     * @throws InvalidLoglevelException
     *
     * @return void
     */
    public function setLogLevel(string $level): void
    {
        if (!$this->isAllowedLogLevel($level)) {
            throw new InvalidArgumentException(sprintf('Unrecognised Loglevel "%s"',  $level));
        }
        $this->systemConfigModule->setValue(self::NAMESPACE, self::CONFIG_KEY_LEVEL, $level);
    }

    /**
     * @param string $level
     *
     * @return bool
     */
    public function isAllowedLogLevel(string $level): bool
    {
        return (
            $level === LogLevel::DEBUG
            || $level === LogLevel::INFO
            || $level === LogLevel::NOTICE
            || $level === LogLevel::WARNING
            || $level === LogLevel::ERROR
            || $level === LogLevel::CRITICAL
            || $level === LogLevel::ALERT
            || $level === LogLevel::EMERGENCY
        );
    }
}
