<?php

declare(strict_types=1);

namespace Xentral\Modules\Log\Data;

use DateTime;
use Exception;

final class LogData
{
    /** @var int|null $id */
    private $id;

    /** @var DateTime|null $logTime */
    private $logTime;

    /** @var string|null $level */
    private $level;

    /** @var string|null $message */
    private $message;

    /** @var string|null $class */
    private $class;

    /** @var string|null $method */
    private $method;

    /** @var int|null $line */
    private $line;

    /** @var string|null $originType */
    private $originType;

    /** @var string|null $originDetail */
    private $originDetail;

    /** @var string|null $dump */
    private $dump;

    private function __construct()
    {
    }

    /**
     * @param array $data
     *
     * @return LogData
     */
    public static function fromDbState(array $data): LogData
    {
        $logData = new LogData();

        if (isset($data['id'])) {
            $logData->id = (int)$data['id'];
        }

        if (isset($data['log_time'])) {
            try {
                $logData->logTime = new DateTime($data['log_time']);
            } catch (Exception $e) {
            }
        }

        if (isset($data['level'])) {
            $logData->level = (string)$data['level'];
        }

        if (isset($data['message'])) {
            $logData->message = (string)$data['message'];
        }

        if (isset($data['class'])) {
            $logData->class = (string)$data['class'];
        }

        if (isset($data['method'])) {
            $logData->method = (string)$data['method'];
        }

        if (isset($data['line'])) {
            $logData->line = (int)$data['line'];
        }

        if (isset($data['origin_type'])) {
            $logData->originType = (string)$data['origin_type'];
        }

        if (isset($data['origin_detail'])) {
            $logData->originDetail = (string)$data['origin_detail'];
        }

        if (isset($data['dump'])) {
            $logData->dump = (string)$data['dump'];
        }

        return $logData;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return DateTime|null
     */
    public function getLogTime(): ?DateTime
    {
        return $this->logTime;
    }

    /**
     * @return string|null
     */
    public function getLevel(): ?string
    {
        return $this->level;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @return int|null
     */
    public function getLine(): ?int
    {
        return $this->line;
    }

    /**
     * @return string|null
     */
    public function getOriginType(): ?string
    {
        return $this->originType;
    }

    /**
     * @return string|null
     */
    public function getOriginDetail(): ?string
    {
        return $this->originDetail;
    }

    /**
     * @return string|null
     */
    public function getDump(): ?string
    {
        return $this->dump;
    }
}
