<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Provider;

use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexIdentifier;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexItem;

interface ItemIndexProviderInterface extends SearchIndexProviderInterface
{
    /**
     * Returns a single item for writing into the search index
     *
     * @param IndexIdentifier $identifier
     *
     * @return IndexItem|null
     */
    public function getItem(IndexIdentifier $identifier);
}
