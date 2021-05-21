<?php

declare(strict_types=1);

namespace Xentral\Components\Logger\Handler;

use Xentral\Components\Logger\Context\ContextInterface;

final class StreamLogHandler extends AbstractLogHandler
{
    /** @var string FORMAT */
    private const FORMAT = '%TIME%  [%ORIGIN_TYPE%:%ORIGIN_DETAIL%]  %CLASS%::%METHOD%(%LINE%)'
                           .'  %LEVEL%  %MESSAGE%  %DUMP%';

    /** @var string $stream */
    private $logFile;

    /**
     * @param string     $logFilePath
     * @param string     $level
     */
    public function __construct(string $logFilePath, string $level)
    {
        $this->logFile = $logFilePath;
        $this->setMinimumLevel($level);
    }

    /**
     * @param string           $level
     * @param string           $message
     * @param ContextInterface $context
     *
     * @return void
     */
    public function addLogEntry(string $level, string $message, ContextInterface $context): void
    {
        if (!$this->canHandle($level)) {
            return;
        }
        $dump = '';
        if ($context->hasDump() && !$context->hasException()) {
            $dump = print_r($context->getDump(), true);
        }
        if ($context->hasException()) {
            $dump = (string)$context->getException();
        }
        $entry = self::FORMAT;
        $entry = preg_replace(
            [
                '/%TIME%/',
                '/%LEVEL%/',
                '/%MESSAGE%/',
                '/%CLASS%/',
                '/%METHOD%/',
                '/%LINE%/',
                '/%ORIGIN_TYPE%/',
                '/%ORIGIN_DETAIL%/',
                '/%DUMP%/',
            ],
            [
                $context->getTime()->format('Y-m-d H:i:s'),
                strtoupper($level),
                $message,
                $context->getClass(),
                $context->getFunction(),
                $context->getLine(),
                $context->getOriginType(),
                $context->getOriginDetail(),
                $dump
            ],
            $entry
        );
        $entry .= "\r\n";
        $stream = fopen($this->logFile, 'ab+');
        fwrite($stream, $entry);
        fclose($stream);
    }
}
