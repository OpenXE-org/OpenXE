<?php

namespace Xentral\Components\Http\Session;

use Xentral\Components\Http\Exception\InvalidArgumentException;
use Xentral\Components\Http\Exception\SessionSegmentException;

class Session
{
    /** @var string FLASH_SEGMENTKEY */
    const FLASH_SEGMENTKEY = 'flash_messages';

    /** @var string CSRF_SEGMENTKEY */
    const CSRF_SEGMENTKEY = 'csrf_tokens';

    /** @var array $data */
    protected $data;

    /** @var Segment[] $segments */
    protected $segments;

    /** @var FlashMessageCollection $flashData */
    protected $flashMessages;

    /** @var CsrfTokenManager $csrfTokens */
    protected $csrfTokens;

    /**
     * @param array  $data
     */
    public function __construct($data = [])
    {
        $this->data = (array)$data;
        $this->segments = [];
        $this->flashMessages = $this->createFlashMessageCollection();
        $this->csrfTokens = $this->createCsrfTokenManager();
    }

    /**
     * Clears all Session data and flash messages
     *
     * @return void
     */
    public function clearAll()
    {
        $this->data = [];
        $this->segments = [];
        $this->flashMessages = new FlashMessageCollection();
    }

    /**
     * Gets a value with a specific key from the current Segment
     *
     * @param string $segment
     * @param string $key
     * @param null   $default
     * @param bool   $clear true=remove entry from the session
     *
     * @return mixed|null
     */
    public function getValue($segment, $key, $default = null, $clear = false)
    {

        return $this->getSegment($segment)->getValue($key, $default, $clear);
    }

    /**
     * Makes an entry to the current Segment
     *
     * @param string                 $segment
     * @param string                 $key
     * @param string|int|float|array $value
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function setValue($segment, $key, $value)
    {
        $this->getSegment($segment)->setValue($key, $value);
    }

    /**
     * Removes a single Entry from the current segment
     *
     * @param string $segment
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function removeValue($segment, $key)
    {
        $this->getSegment($segment)->removeValue($key);
    }

    /**
     * Gets a segment object by it's name
     *
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return Segment
     */
    public function getSegment($name = '')
    {
        if ($name === self::FLASH_SEGMENTKEY || $name === self::CSRF_SEGMENTKEY) {
            throw new SessionSegmentException(
                sprintf('"%s" is a reserved segment name.', self::FLASH_SEGMENTKEY)
            );
        }
        $segmentId = $this->getSegmentKey($name);
        if (array_key_exists($segmentId, $this->segments)) {
            return $this->segments[$segmentId];
        }
        $data = [];
        if (array_key_exists($segmentId, $this->data)) {
            $data = $this->data[$segmentId];
        }
        $segment = new Segment($this, $name, $data);
        $this->segments[$segmentId] = $segment;

        return $segment;
    }

    /**
     * Dumps the whole session into specific target variable.
     *
     * @param mixed $targetVariable
     */
    public function dumpSession(&$targetVariable)
    {
        $this->mergeSession();
        $targetVariable = $this->data;
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        $this->dumpSession($dump);
        $this->csrfTokens->dumpTokens($tokendump);
        $tokendump = array_keys($tokendump);

        return [
            'data'           => $dump,
            'tokens' => $tokendump
        ];
    }

    /**
     * Adds new flash message to specific segment.
     *
     * @param string $segment if empty: default segment will be used
     * @param string $message
     * @param string $type
     * @param int    $priority
     *
     * @return void
     */
    public function addFlashMessage($segment, $message, $type = FlashMessageData::FLASHTYPE_DEFAULT, $priority = 0)
    {
        $flash = new FlashMessageData($message, $type, $segment, $priority);
        $flashData = $flash->toSessionArray();
        $key = (string)$this->getSegmentKey(self::FLASH_SEGMENTKEY);
        $this->data[$key][] = $flashData;
    }

    /**
     * Gets flash message(s) by specific filter conditions.
     *
     * The flash message will be cleared from the session after retrieving
     *
     * @param string|null $segment filter for segment name
     * @param string|null $type    filter for message type
     *
     * @return FlashMessageData[] flash messages sorted by priority
     */
    public function getFlashMessages($segment = null, $type = null)
    {
        return $this->flashMessages->getMessages($segment, $type);
    }

    /**
     * Creates a CSRF Token and stores it in the Session
     *
     * @param string $tokenKey
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public function createCsrfToken($tokenKey)
    {
        return $this->csrfTokens->createToken($tokenKey);
    }

    /**
     * Returns true if specified Token is valid
     *
     * @param string $tokenKey
     * @param string $tokenValue
     * @param bool   $remove true=remove token from session to mitigate second use
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function isCsrfTokenValid($tokenKey, $tokenValue, $remove = false)
    {
        return $this->csrfTokens->isTokenValid($tokenKey, $tokenValue, $remove);
    }

    /**
     * @param $segmentName
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    private function getSegmentKey($segmentName)
    {
        $this->ensureSegmentNameFormat($segmentName);

        return sprintf('segment_%s', $segmentName);
    }

    /**
     * Merge all Segments and Flashmessages and CsrfTokens into the session array.
     *
     * @return void
     */
    private function mergeSession()
    {
        foreach ($this->segments as $key => $segment) {
            $segmentData = $segment->getAll();
            if (count($segmentData) > 0) {
                $this->data[$key] = $segmentData;
            }
        }

        $flashKey = $this->getSegmentKey(self::FLASH_SEGMENTKEY);
        $newFlashes = [];
        if (array_key_exists($flashKey, $this->data)) {
            $newFlashes = $this->data[$flashKey];
        }
        $oldFlashes = $this->flashMessages->toSessionArray();
        $this->flashMessages = new FlashMessageCollection();
        $allFlashes = array_merge($oldFlashes, $newFlashes);
        if (count($allFlashes) > 0) {
            $this->data[$flashKey] = $allFlashes;
        } else {
            unset($this->data[$flashKey]);
        }

        $this->csrfTokens->dumpTokens($tokens);
        $tokenSegmentKey = $this->getSegmentKey(self::CSRF_SEGMENTKEY);
        if (is_array($tokens) && count($tokens) > 0) {
            $this->data[$tokenSegmentKey] = $tokens;
        } else {
            unset($this->data[$tokenSegmentKey]);
        }
    }

    /**
     * @return FlashMessageCollection
     */
    private function createFlashMessageCollection()
    {
        $key = $this->getSegmentKey(self::FLASH_SEGMENTKEY);
        if (!array_key_exists($key, $this->data)) {
            return new FlashMessageCollection();
        }
        $messages = $this->data[$key];
        $result = new FlashMessageCollection($messages);
        unset($this->data[$key]);
        $this->data[$key] = [];

        return $result;
    }

    /**
     * @return CsrfTokenManager
     */
    private function createCsrfTokenManager()
    {
        $key = $this->getSegmentKey(self::CSRF_SEGMENTKEY);
        if (!array_key_exists($key, $this->data)) {
            return new CsrfTokenManager();
        }
        $tokens = $this->data[$key];
        $result = new CsrfTokenManager($tokens);
        unset($this->data[$key]);
        $this->data[$key] = [];

        return $result;
    }

    /**
     * @param $name
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private function ensureSegmentNameFormat($name)
    {
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $name)) {
            throw new InvalidArgumentException('Invalid segment name format.');
        }
    }
}
