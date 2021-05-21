<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Provider;

use DateTimeInterface;
use Xentral\Modules\SuperSearch\SearchIndex\Collection\ItemFormatterCollection;

interface DiffIndexProviderInterface extends SearchIndexProviderInterface
{
    /**
     * Returns index items that were changed or created since the passed date time
     *
     * @param DateTimeInterface $since
     *
     * @return ItemFormatterCollection
     */
    public function getItemsSince(DateTimeInterface $since);
}
