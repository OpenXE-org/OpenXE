<?php

namespace Xentral\Modules\Api\Resource\Result;

class CollectionResult extends AbstractResult
{
    /**
     * @param array      $collection
     * @param array|null $pagination
     */
    public function __construct(array $collection, array $pagination = null)
    {
        if (empty($pagination)) {
            //throw new \CountryInvalidArgumentException('CollectionResult must contain pagination'); // @todo für GetIDs
        }

        if (empty($collection)) {
            throw new \InvalidArgumentException('CollectionResult can not be empty');
        }
        $firstKey = key($collection);
        if (!is_numeric($firstKey)) {
            throw new \InvalidArgumentException('CollectionResult can only store an index based array');
        }
        if (!is_array($collection[$firstKey]) || empty($collection[$firstKey])) {
            throw new \RuntimeException('CollectionResult must contain at least one result');
        }

        // @todo Sicherstellen dass Paginierung passt

        $this->type = self::RESULT_TYPE_COLLECTION;
        $this->data = $collection;
        $this->pagination = $pagination;
    }
}
