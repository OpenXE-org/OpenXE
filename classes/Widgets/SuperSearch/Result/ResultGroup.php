<?php

namespace Xentral\Widgets\SuperSearch\Result;

final class ResultGroup implements \JsonSerializable
{
    /** @var string $key */
    private $key;

    /** @var string $title */
    private $title;

    /** @var ResultItem[]|array $items */
    private $items = [];

    /**
     * @param string             $groupKey
     * @param string             $groupTitle
     * @param ResultItem[]|array $resultItems
     */
    public function __construct($groupKey, $groupTitle, array $resultItems = [])
    {
        $this->key = $groupKey;
        $this->title = $groupTitle;

        foreach ($resultItems as $resultItem) {
            $this->addItem($resultItem);
        }
    }

    /**
     * @param ResultItem $item
     *
     * @return void
     */
    public function addItem(ResultItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return ResultItem[]|array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function countItems()
    {
        return count($this->items);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'key'   => $this->key,
            'title' => $this->title,
            'count' => count($this->items),
            'items' => $this->items,
        ];
    }
}
