<?php

declare(strict_types=1);

namespace Xentral\Components\Logger\Context;

use DateTime;
use Throwable;
use Xentral\Components\Http\Request;
use Xentral\Components\Logger\LoggerInterface;
use Xentral\Components\Util\StringUtil;

final class ContextHelper
{
    /** @var Request $request */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(
        Request $request
    ) {
        $this->request = $request;
    }

    /**
     * @param array $contextArray
     *
     * @return ContextInterface
     */
    public function createContext(array $contextArray): ContextInterface
    {
        //time context
        $time = new DateTime('now');
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        //calling code line
        $invocation = $this->findInvocation($backtrace);

        //origin of the call
        $instigation = $this->createOrigin($backtrace);

        //possible exception
        $exceptionEntry = null;
        if (array_key_exists('exception', $contextArray)) {
            $exceptionEntry = $contextArray['exception'];
            unset($contextArray['exception']);
        }
        $exception = null;
        if (is_subclass_of($exceptionEntry, Throwable::class)) {
            $exception = $exceptionEntry;
        }

        //possible dump
        if (count($contextArray) === 0) {
            $dump = null;
        } else {
            $dump = $contextArray;
        }

        return new LoggerContext(
            $time,
            $invocation,
            $instigation,
            $exception,
            $dump
        );
    }

    /**
     * @param string $message
     * @param array  $context
     *
     * @return string
     */
    public function interpolateMessage(string $message, array &$context): string
    {
        $keys = array_keys($context);
        $interpolated = $message;
        foreach ($keys as $key) {
            if ($key === 'exception') {
                continue;
            }
            if (!preg_match('/^\w+$/', (string)$key)) {
                continue;
            }
            if (preg_match(sprintf('/\{%s\}/', $key), $interpolated)) {
                $pattern = sprintf('{%s}', $key);
                try {
                    $interpolated = str_replace($pattern, (string)$context[$key], $interpolated);
                } catch (Throwable $e) {
                    continue;
                }
                unset($context[$key]);
            }
        }

        return $interpolated;
    }

    /**
     * @param array $backtrace
     *
     * @return Invocation
     */
    public function findInvocation(array $backtrace): Invocation
    {
        $lastLine = 0;
        $lastFile = '';
        foreach ($backtrace as $trace) {
            if (isset($trace['class'])) {
                $class = $trace['class'];
                if (
                    $class !== self::class
                    && !in_array(LoggerInterface::class, class_implements($class), true)
                ) {
                    return new Invocation(
                        $trace['class'],
                        $trace['function'],
                        $lastLine,
                        $lastFile
                    );
                }
                if (array_key_exists('line', $trace)) {
                    $lastLine = $trace['line'];
                    $lastFile = $trace['file'];
                }
            }
        }

        return new Invocation('unknown', 'unknown', 0, 'unknown');
    }

    /**
     * @param array $backtrace
     *
     * @return OriginInterface
     */
    public function createOrigin(array $backtrace): OriginInterface
    {
        try {
            $instigation = $this->tryCreateFrontendOrigin();
            if ($instigation !== null) {
                return $instigation;
            }
            $instigation = $this->tryCreateSchedulerJobOrigin($backtrace);
            if ($instigation !== null) {
                return $instigation;
            }
            $instigation = $this->tryCreateRestApiOrigin();
            if ($instigation !== null) {
                return $instigation;
            }
            $instigation = $this->tryCreateLegacyApiOrigin();
            if ($instigation !== null) {
                return $instigation;
            }
            $instigation = $this->tryCreateCliOrigin();
            if ($instigation !== null) {
                return $instigation;
            }
        } catch (Throwable $e) {
        }

        return new Origin(OriginInterface::TYPE_UNKNOWN, 'unknown');
    }

    /**
     * @return OriginInterface|null
     */
    private function tryCreateFrontendOrigin(): ?OriginInterface
    {
        $module = $this->request->get->get('module', null);
        if ($this->request === null || $module === null || $module === 'api') {
            return null;
        }
        $action = $this->request->get->get('action', null);
        $cmd = $this->request->get->get('cmd', null);
        $payload = [strtoupper($this->request->getMethod())];
        if ($module !== null) {
            $payload[] = sprintf('module=%s', $module);
        }
        if ($action !== null) {
            $payload[] = sprintf('action=%s', $action);
        }
        if ($cmd !== null) {
            $payload[] = sprintf('cmd=%s', $cmd);
        }

        return new Origin(OriginInterface::TYPE_FRONTEND, implode(' ', $payload));
    }

    /**
     * @return OriginInterface|null
     */
    private function tryCreateLegacyApiOrigin(): ?OriginInterface
    {
        if ($this->request->get->get('module', '') === 'api') {
            return new Origin(
                OriginInterface::TYPE_LEGACY_API,
                sprintf(
                    '%s action=%s',
                    $this->request->getMethod(),
                    $this->request->get->get('action', '')
                )
            );
        }

        return null;
    }

    /**
     * @return OriginInterface|null
     */
    private function tryCreateRestApiOrigin(): ?OriginInterface
    {
        $url = $this->request->getFullUrl();
        $match = preg_match('/api\/(v\d.?\/[^?]+)\??/', $url, $matches);
        if ($match && count($matches) > 1) {
            return new Origin(
                OriginInterface::TYPE_REST_API,
                sprintf(
                    '%s endpoint=%s',
                    $this->request->getMethod(),
                    $matches[1]
                )
            );
        }
        $path = $this->request->get->get('path', '');
        if (preg_match('/(v\d.?\/[^?]+)/', $path)
            && preg_match('/api\/index.php\?/', $url)
        ) {
            return new Origin(
                OriginInterface::TYPE_REST_API,
                sprintf(
                    '%s path=%s',
                    $this->request->getMethod(),
                    $path
                )
            );
        }

        return null;
    }

    /**
     * @param array $backtrace
     *
     * @return OriginInterface|null
     */
    private function tryCreateSchedulerJobOrigin(array $backtrace): ?OriginInterface
    {
        if (count($backtrace) === 0) {
            return null;
        }
        $trace = $backtrace[count($backtrace) - 1];
        $file = $trace['file'] ?? '';
        if (!StringUtil::endsWith($file, '/cronjobs/command.php')) {
            return null;
        }
        $jobFile = $trace['args'][0] ?? '';
        $payload = $jobFile;
        $matchresult = [];
        preg_match('/^.+\/cronjobs\/(.+)$/', $jobFile, $matchresult);
        if (count($matchresult) > 1) {
            $payload = sprintf('job=%s', $matchresult[1]);
        }

        return new Origin(OriginInterface::TYPE_SCHEDULER_JOB, $payload);
    }

    /**
     * @return OriginInterface|null
     */
    private function tryCreateCliOrigin(): ?OriginInterface
    {
        if ($this->request->server->getInt('argc', 0) < 1) {
            return null;
        }
        $file = $this->request->server->get('argv', [''])[0];
        $payload = sprintf('script=%s', $file);

        return new Origin(OriginInterface::TYPE_CLI, $payload);
    }
}
