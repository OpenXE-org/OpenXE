<?php

namespace Xentral\Modules\SystemNotification\Service;

use Xentral\Modules\SystemNotification\Exception\InvalidArgumentException;

final class NotificationMessageData
{
    /** @var array $validMessageTypes */
    private static $validMessageTypes = [
        NotificationServiceInterface::TYPE_DEFAULT,
        NotificationServiceInterface::TYPE_NOTICE,
        NotificationServiceInterface::TYPE_SUCESS,
        NotificationServiceInterface::TYPE_WARNING,
        NotificationServiceInterface::TYPE_ERROR,
        NotificationServiceInterface::TYPE_PUSH,
    ];

    /** @var string $type */
    private $type;

    /** @var string $title */
    private $title;

    /** @var string|null $message */
    private $message;

    /** @var bool $priority */
    private $priority;

    /** @var array $options */
    private $options = [];

    /** @var array $tags */
    private $tags = [];

    /**
     * @param string      $type
     * @param string      $title
     * @param string|null $message
     * @param bool        $priority
     *
     * @throws InvalidArgumentException
     */
    public function __construct($type, $title, $message = null, $priority = false)
    {
        if (!in_array($type, self::$validMessageTypes, true)) {
            throw new InvalidArgumentException(sprintf(
                'Message type "%s" is invalid. Valid types are: %s', $type, implode(', ', self::$validMessageTypes)
            ));
        }
        if (empty($title)) {
            throw new InvalidArgumentException('Title is empty.');
        }
        if (mb_strlen($title) > 64) {
            throw new InvalidArgumentException(sprintf('Message title "%s" is longer than 64 characters.', $title));
        }

        $this->type = (string)$type;
        $this->title = (string)$title;

        $this->setMessage($message);
        $this->setPriority($priority);
    }

    /**
     * @param string      $text
     * @param string      $link
     * @param string|null $htmlId Html id attribute (<button id="{$htmlId}">)
     *
     * @return void
     */
    public function addButton($text, $link, $htmlId = null)
    {
        if (!isset($this->options['buttons'])) {
            $this->options['buttons'] = [];
        }

        $this->options['buttons'][] = [
            'text' => $text,
            'link' => $link,
            'id'   => !empty($htmlId) ? $htmlId : null,
        ];
    }

    /**
     * @param string $tag
     *
     * @return void
     */
    public function addTag($tag)
    {
        $this->tags[] = (string)$tag;
    }

    /**
     * @param array $tags
     *
     * @return void
     */
    public function addTags(array $tags)
    {
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isPriority()
    {
        return $this->priority;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param string|null $message
     *
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = !empty($message) ? (string)$message : null;
    }

    /**
     * @param bool $priority
     *
     * @return void
     */
    public function setPriority($priority)
    {
        $this->priority = (bool)$priority;
    }

    /**
     * @param string $property
     * @param mixed  $value
     *
     * @return void
     */
    public function setOption($property, $value)
    {
        $this->options[(string)$property] = $value;
    }

    /**
     * @param array $options
     *
     * @return void
     */
    public function setOptions(array $options = [])
    {
        $this->options = $options;
    }
}
