<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Provider;

use Xentral\Modules\SuperSearch\SearchIndex\Collection\ItemFormatterCollection;

interface FullIndexProviderInterface extends SearchIndexProviderInterface
{
    /**
     * Returns all items; No sectioning, just all items
     *
     * Use BulkIndexProviderInterface for large datasets instead
     *
     * @return ItemFormatterCollection
     */
    public function getAllItems();
}
