<?php

namespace Xentral\Modules\Api\Resource\Result;

class ItemResult extends AbstractResult
{
    /**
     * @param array      $item
     * @param array|null $pagination
     */
    public function __construct(array $item, array $pagination = null)
    {
        if ($pagination !== null) {
            throw new \InvalidArgumentException('ItemResult can not have pagination');
        }

        if (empty($item)) {
            throw new \InvalidArgumentException('ItemResult can not be empty');
        }
        if (is_numeric(key($item))) {
            throw new \InvalidArgumentException('ItemResult can only store an associative array');
        }

        $this->type = self::RESULT_TYPE_ITEM;
        $this->data = $item;
    }
}
