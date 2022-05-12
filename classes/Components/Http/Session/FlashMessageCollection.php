<?php

namespace Xentral\Components\Http\Session;

final class FlashMessageCollection
{
    /** @var FlashMessageData[] $data */
    public $data;

    /**
     * FlashMessageCollection constructor.
     *
     * @param array $flashMessageData array structure from session
     */
    public function __construct($flashMessageData = [])
    {
        $data = $this->fromSessionArray($flashMessageData);
        $this->data = $this->sortByPriority($data);
    }

    /**
     * Gets and removes all flash messages from previous session
     *
     * @return FlashMessageData[]
     */
    public function getAll()
    {
        $result = $this->data;
        $this->data = [];

        return $result;
    }

    /**
     * Gets and removes flash messages filtered
     *
     * @param string|null $segment filter for segment (null=deactivated)
     * @param string|null $type    filter for type (null=deactivated)
     *
     * @return FlashMessageData[] sorted by priority
     */
    public function getMessages($segment = null, $type = null)
    {
        $result = [];
        foreach ($this->data as $key => $item) {
            if (
                ($segment === null || $segment === $item->getSegmentName())
                && ($type === null || $type === $item->getType())
            ) {
                $result[] = $item;
                unset($this->data[$key]);
            }
        }

        return $result;
    }

    /**
     * Gets and removes flash messages filtered
     *
     * @param string $type
     *
     * @return FlashMessageData[] sorted by priority
     */
    public function getMessagesByType($type)
    {
        return $this->getMessages(null, $type);
    }

    /**
     * Gets and removes flash messages filtered
     *
     * @param string $segment
     *
     * @return FlashMessageData[] sorted by priority
     */
    public function getMessagesBySegment($segment)
    {
        return $this->getMessages($segment, null);
    }

    /**
     * Returns array to be stored in session
     *
     * @return array
     */
    public function toSessionArray()
    {
        $result = [];
        foreach ($this->data as $item) {
            $result[] = $item->toSessionArray();
        }

        return $result;
    }

    /**
     * Sorts flash messages by priority (descending)
     *
     * @param FlashMessageData[] $messages
     *
     * @return FlashMessageData[]
     */
    public function sortByPriority($messages)
    {
        usort($messages, [$this, 'comparePriorityCallback']);

        return $messages;
    }

    /**
     * @param array $data
     *
     * @return FlashMessageData[]
     */
    private function fromSessionArray($data)
    {
        $result = [];
        foreach ($data as $item) {
            $result[] = FlashMessageData::createFromArray($item);
        }

        return $result;
    }

    /**
     * Callable compare function for sorting.
     *
     * @param FlashMessageData $insert
     * @param FlashMessageData $exist
     *
     * @return int
     */
    private function comparePriorityCallback($insert, $exist)
    {
        if ($insert->getPriority() === $exist->getPriority()) {
            return 0;
        }
        if ($insert->getPriority() > $exist->getPriority()) {
            return -1;
        }

        return 1;
    }
}
