<?php

namespace Xentral\Widgets\DataTable\Result;

use JsonSerializable;
use Xentral\Widgets\DataTable\Exception\InvalidArgumentException;

/**
 * @see https://datatables.net/manual/server-side
 */
final class DataTableDataResult implements JsonSerializable
{
    /** @var int $drawCounter The draw counter */
    private $drawCounter;

    /** @var int $recordsTotal Total number of records, before filtering */
    private $recordsTotal;

    /** @var int $recordsFiltered Total number of records, after filtering */
    private $recordsFiltered;

    /** @var array $data */
    private $data;

    /** @var string|null $errorMessage */
    private $errorMessage;

    /** @var array|null $debugInfo */
    private $debugInfo;

    /**
     * @param int   $drawCounter
     * @param int   $recordsTotal
     * @param int   $recordsFiltered
     * @param array $data
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $drawCounter = 1,
        $recordsTotal = 0,
        $recordsFiltered = 0,
        $data = []
    ) {
        if (!is_int($drawCounter)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid argument. Draw counter must be an integer. Given type: %s',
                strtolower(gettype($drawCounter))
            ));
        }
        if (!is_int($recordsTotal)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid argument. Total records count must be an integer. Given type: %s',
                strtolower(gettype($recordsTotal))
            ));
        }
        if (!is_int($recordsFiltered)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid argument. Filtered records count be an integer. Given type: %s',
                strtolower(gettype($recordsFiltered))
            ));
        }
        if (!is_array($data)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid argument. Data parameter must be an array. Given type: %s',
                strtolower(gettype($data))
            ));
        }

        $this->drawCounter = $drawCounter;
        $this->recordsTotal = $recordsTotal;
        $this->recordsFiltered = $recordsFiltered;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        $result = [
            'draw'            => $this->drawCounter,
            'recordsTotal'    => $this->recordsTotal,
            'recordsFiltered' => $this->recordsFiltered,
            'data'            => $this->data,
        ];

        if ($this->debugInfo !== null) {
            $result['debug'] = $this->debugInfo;
        }
        if ($this->errorMessage !== null) {
            $result['error'] = $this->errorMessage;
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getDrawCounter()
    {
        return $this->drawCounter;
    }

    /**
     * @return int
     */
    public function getRecordsTotal()
    {
        return $this->recordsTotal;
    }

    /**
     * @return int
     */
    public function getRecordsFiltered()
    {
        return $this->recordsFiltered;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array|null
     */
    public function getDebugInfo()
    {
        return $this->debugInfo;
    }

    /**
     * @param array $debugInfo
     */
    public function setDebugInfo($debugInfo)
    {
        if (!is_array($debugInfo)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid argument. Debug information must be an array. Given type: %s',
                strtolower(gettype($debugInfo))
            ));
        }

        $this->debugInfo = $debugInfo;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->errorMessage !== null;
    }

    /**
     * @return string|null
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     *
     * @throws InvalidArgumentException
     */
    public function setErrorMessage($errorMessage)
    {
        if (!is_string($errorMessage)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid argument. Error message must be a string. Given type: %s',
                strtolower(gettype($errorMessage))
            ));
        }

        $this->errorMessage = $errorMessage;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getResult();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
