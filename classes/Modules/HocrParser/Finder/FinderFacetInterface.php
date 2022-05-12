<?php

namespace Xentral\Modules\HocrParser\Finder;

use Xentral\Modules\HocrParser\Data\BoundingBoxCollection;

interface FinderFacetInterface
{
    public function MatchPreCondition($text);

    public function Select(array $candidates, BoundingBoxCollection $boxes);
}
