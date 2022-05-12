<?php

namespace Xentral\Components\Database\Profiler;

use Exception;
use Xentral\Components\Logger\LoggerInterface;

final class Profiler implements ProfilerInterface
{
    /** @var LoggerInterface $logger */
    private $logger;

    /** @var bool $active */
    private $active = false;

    /** @var string $logLevel */
    private $logLevel = 'debug';

    /** @var string $logFormat */
    private $logFormat = "{method} ({duration}): {statement} \n{backtrace}";

    /** @var array $context */
    private $context = [];

    /** @var array $contexts */
    private $contexts = [];

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $className
     * @param string $methodName
     *
     * @return void
     */
    public function start($className, $methodName)
    {
        if (!$this->active) {
            return;
        }

        $this->context = [
            'class'  => $className,
            'method' => $methodName,
            'start'  => microtime(true),
        ];
    }

    /**
     * @param string|null $statement
     * @param array       $values
     *
     * @return void
     */
    public function finish($statement = null, array $values = [])
    {
        if (!$this->active) {
            return;
        }

        $finish = microtime(true);
        $exception = new Exception();

        $this->context['finish'] = $finish;
        $this->context['duration_real'] = $finish - $this->context['start'];
        $this->context['duration'] = sprintf('%.6f', $this->context['duration_real']) . ' seconds';
        $this->context['statement'] = $statement;
        $this->context['bindings'] = $values;
        $this->context['backtrace'] = $exception->getTraceAsString();

        $this->logger->log($this->logLevel, $this->logFormat, $this->context);

        $this->contexts[] = $this->context;
        $this->context = [];
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return void
     */
    public function setActive($active)
    {
        $this->active = (bool)$active;
    }

    /**
     * /**
     * @return string
     */
    public function getLogLevel()
    {
        return $this->logLevel;
    }

    /**
     * @param string $logLevel
     *
     * @return void
     */
    public function setLogLevel($logLevel)
    {
        $this->logLevel = (string)$logLevel;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return array
     */
    public function getContexts()
    {
        return $this->contexts;
    }
}
