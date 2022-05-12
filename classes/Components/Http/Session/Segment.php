<?php

namespace Xentral\Components\Http\Session;

use Xentral\Components\Http\Exception\InvalidArgumentException;

final class Segment
{
    /** @var Session $session */
    private $session;

    /** @var string $name */
    private $name;

    /** @var array $data */
    private $data;

    /**
     * @param Session $session
     * @param string  $name
     * @param array   $data
     */
    public function __construct(Session $session, $name, $data = [])
    {
        $this->session = $session;
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * Makes an entry to the segment
     *
     * @param string                 $key
     * @param int|float|string|array $value
     *
     * @return void
     */
    public function setValue($key, $value)
    {
        $this->ensureSegmentKeyFormat($key);
        $this->data[$key] = $value;
    }

    /**
     * Gets a value with a specific key
     *
     * @param string     $key
     * @param mixed|null $default
     * @param bool       $clear true=remove entry from the session
     *
     * @return mixed|null
     */
    public function getValue($key, $default = null, $clear = false)
    {
        $this->ensureSegmentKeyFormat($key);
        $value = $default;
        if (isset($this->data[$key])) {
            $value = $this->data[$key];
        }
        if ($clear === true) {
            $this->removeValue($key);
        }

        return $value;
    }

    /**
     * Adds new flash message to the segment
     *
     * @internal The actual segment which holds the flashes is 'flash_messages'.
     *
     * @param string $message
     * @param string $type
     * @param int    $priority sorted highest to lowest
     *
     * @return void
     */
    public function addFlashMessage($message, $type = FlashMessageData::FLASHTYPE_DEFAULT, $priority = 0)
    {
        $this->session->addFlashMessage($this->name, $message, $type, $priority);
    }

    /**
     * Removes single entry
     *
     * @param string $key
     *
     * @return void
     */
    public function removeValue($key)
    {
        $this->ensureSegmentKeyFormat($key);
        unset($this->data[$key]);
    }

    /**
     * Removes all entries and flashes of this segment
     *
     * @return void
     */
    public function clearAll()
    {
        $this->data = [];
        $this->session->getFlashMessages($this->name);
    }

    /**
     * Gets all entries
     *
     * @return array
     */
    public function getAll()
    {
        return $this->data;
    }

    /**
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private function ensureSegmentKeyFormat($key)
    {
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $key)) {
            throw new InvalidArgumentException('Invalid segment key format.');
        }
    }
}
