<?php

namespace Xentral\Widgets\SuperSearch\Result;

use DateTimeInterface;
use JsonSerializable as JsonSerializableAlias;

final class ResultCollection implements JsonSerializableAlias
{
    /** @var ResultGroup[]|array $groups */
    private $groups;

    /** @var DateTimeInterface|null $lastIndexUpdateTime */
    private $lastIndexUpdateTime;

    /**
     * @param ResultGroup[]|array     $resultGroups
     * @param DateTimeInterface|null $lastIndexUpdate
     */
    public function __construct(array $resultGroups = [], DateTimeInterface $lastIndexUpdate = null)
    {
        foreach ($resultGroups as $resultGroup) {
            $this->addGroup($resultGroup);
        }
        $this->lastIndexUpdateTime = $lastIndexUpdate;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->groups) === 0;
    }

    /**
     * @param ResultGroup $group
     *
     * @return void
     */
    public function addGroup(ResultGroup $group)
    {
        $this->groups[] = $group;
    }

    /**
     * @param string $groupKey
     *
     * @return bool
     */
    public function hasGroup($groupKey)
    {
        foreach ($this->groups as $group) {
            if ($groupKey === $group->getKey()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $groupKey
     *
     * @return ResultGroup|null
     */
    public function getGroup($groupKey)
    {
        foreach ($this->groups as $group) {
            if ($groupKey === $group->getKey()) {
                return $group;
            }
        }

        return null;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getLastIndexUpdateTime()
    {
        return $this->lastIndexUpdateTime;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $results = [];
        $itemCount = 0;
        foreach ($this->groups as $group) {
            $itemCount += $group->countItems();
            $index = $group->getKey();
            $results[$index] = $group;
        }

        return [
            'count'                       => $itemCount,
            'results'                     => $results,
            'last_index_update_rfc2822'   =>
                $this->lastIndexUpdateTime !== null ? $this->lastIndexUpdateTime->format(DATE_RFC2822) : null,
            'last_index_update_formatted' =>
                $this->lastIndexUpdateTime !== null ? $this->lastIndexUpdateTime->format('d.m.Y H:i') . ' Uhr' : null,
        ];
    }
}
