<?php

namespace Xentral\Components\Database\Profiler;

use Xentral\Components\Logger\LoggerInterface;

interface ProfilerInterface
{
    /**
     * @param string $className
     * @param string $methodName
     *
     * @return void
     */
    public function start($className, $methodName);

    /**
     * @param string|null $statement
     * @param array       $values
     *
     * @return void
     */
    public function finish($statement = null, array $values = []);

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @param bool $active
     *
     * @return void
     */
    public function setActive($active);

    /**
    /**
     * @return string
     */
    public function getLogLevel();

    /**
     * @param string $logLevel
     *
     * @return void
     */
    public function setLogLevel($logLevel);

    /**
     * @return LoggerInterface
     */
    public function getLogger();
}
