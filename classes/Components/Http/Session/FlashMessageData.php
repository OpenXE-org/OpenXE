<?php

namespace Xentral\Components\Http\Session;

use Xentral\Components\Http\Exception\InvalidArgumentException;

final class FlashMessageData
{
    /** @var string FLASHTYPE_DEFAULT */
    const FLASHTYPE_DEFAULT = 'default';

    /** @var string FLASHTYPE_NOTICE */
    const FLASHTYPE_NOTICE = 'notice';

    /** @var string FLASHTYPE_SUCCESS */
    const FLASHTYPE_SUCCESS = 'success';

    /** @var string FLASHTYPE_WARNING */
    const FLASHTYPE_WARNING = 'warning';

    /** @var string FLASHTYPE_ERROR */
    const FLASHTYPE_ERROR = 'error';

    /** @var array $flashTypes */
    public static $flashTypes = [
        self::FLASHTYPE_DEFAULT,
        self::FLASHTYPE_NOTICE,
        self::FLASHTYPE_SUCCESS,
        self::FLASHTYPE_WARNING,
        self::FLASHTYPE_ERROR,
    ];

    /**@var string $type */
    private $type;

    /** @var string $message */
    private $message;

    /** @var int $priority */
    private $priority;

    /** @var string $segmentName */
    private $segmentName;

    /**
     * @param string $message
     * @param string $type
     * @param string $segmentName
     * @param int    $priority
     */
    public function __construct($message, $type, $segmentName = '', $priority = 0)
    {
        $this->setType($type);
        $this->message = $message;
        $this->setSegmentName($segmentName);
        $this->priority = (int)$priority;
    }

    /**
     * Create FlashMessageData object from session array entry
     *
     * @param array $data required keys: 'priority', 'segment, 'type', 'message'
     *
     * @throws InvalidArgumentException
     *
     * @return FlashMessageData
     */
    public static function createFromArray($data)
    {
        if (
            !array_key_exists('priority', $data)
            || !array_key_exists('segment', $data)
            || !array_key_exists('type', $data)
            || !array_key_exists('message', $data)
        ) {
            throw new InvalidArgumentException('Invalid array data for FlashMessageData.');
        }

        return new self($data['message'], $data['type'], $data['segment'], $data['priority']);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return bool
     */
    public function getSegmentName()
    {
        return $this->segmentName;
    }

    /**
     * Returns array to store in the session
     *
     * @return array
     */
    public function toSessionArray()
    {
        return [
            'priority' => $this->getPriority(),
            'segment'  => $this->getSegmentName(),
            'type'     => $this->getType(),
            'message'  => $this->getMessage(),
        ];
    }

    /**
     * @param string $type
     *
     * @throws InvalidArgumentException
     */
    private function setType($type)
    {
        if (!in_array($type, self::$flashTypes, true)) {
            throw new InvalidArgumentException(sprintf('Unknown message type "%s".', $type));
        }
        $this->type = $type;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    private function setSegmentName($name)
    {
        if (!preg_match('/^[a-z0-9_]*$/', $name)) {
            throw  new InvalidArgumentException(sprintf('Invalid Segment Name "%s".', $name));
        }

        $this->segmentName = $name;
    }
}
