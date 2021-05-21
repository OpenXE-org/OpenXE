<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Provider;

use Xentral\Modules\SuperSearch\SearchIndex\Collection\ItemFormatterCollection;

interface BulkIndexProviderInterface extends SearchIndexProviderInterface
{
    /**
     * Returns a range of the index items
     *
     * @param int $start
     * @param int $limit
     *
     * @return ItemFormatterCollection
     */
    public function getBulkItems($start, $limit);

    /**
     * Returns the number of items to process in one transaction
     *
     * @return int
     */
    public function getBulkSize();

    /**
     * Returns the total count for all items
     *
     * @return int
     */
    public function getTotalCount();
}
