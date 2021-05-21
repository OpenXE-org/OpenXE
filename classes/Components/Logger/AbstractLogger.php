<?php

declare(strict_types=1);

namespace Xentral\Components\Logger;

abstract class AbstractLogger implements LoggerInterface
{
    /** @var string[] LOGLEVELS */
    protected const LOGLEVELS = [
        LogLevel::DEBUG,
        LogLevel::INFO,
        LogLevel::NOTICE,
        LogLevel::WARNING,
        LogLevel::ERROR,
        LogLevel::CRITICAL,
        LogLevel::ALERT,
        LogLevel::EMERGENCY
    ];

    /**
     * An emergency error should lead to customer calling the support for "emergency problem".
     * immediate action required | data loss is imminent | software is no more able to run
     *
     * "The last call for help before the application dies."
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Use in cases where an administrator should get notification by email or sms.
     * system related errors | system admin can resolve | e.g. cannot connect to database
     *
     * "Hey Sysadmin, you must fix this! Otherwise, some/many people are not able to work."
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Unhandled/unknown errors and errors that make a piece of software stop working.
     * component or dependency missing | unhandled exception | module/cronjob not able to work
     *
     * "Something about this module is broken persistently - developer's attention required."
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Catchable Error that will not break the application but needs to be logged.
     * temporary issue | error response form API | error message on screen
     *
     * "That is the minimum information I need when an Error gets reported."
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     * incomplete configuriation | future error | low performance
     *
     * "It works that way, but it's not ideal - user should chage that."
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Notable events that let you understand a user's intention.
     * "User XY created a new lead." | a cronjob started/finished
     *
     * "What was the user trying to do before the application crashed?"
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Common events that describe a user's intention more detailed.
     * More verbose form of NOTICE.
     *
     * "Might be helpful to reproduce the user's exact actions."
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     * retrace the code | variable dumps
     *
     * "Same amount of information you get from actual debugging."
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}
