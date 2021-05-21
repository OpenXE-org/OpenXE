<?php

namespace Xentral\Modules\Api\Resource\Feature;

use Xentral\Modules\Api\Resource\Filter\Select\SimpleSearchFilter;

trait FilterFeatureTrait
{
    /**
     * Festlegen welche Filter erlaubt sind
     *
     * @example $this->registerFilterParams([
     *              'title'             => 'l.bezeichnung %LIKE%',
     *              'title_starts_with' => 'l.bezeichnung LIKE%',
     *              'title_ends_with'   => 'l.bezeichnung %LIKE',
     *              'title_exact'       => 'l.bezeichnung LIKE',
     *              'project'           => 'l.projekt =',
     *              'project_not'       => 'l.projekt !=',
     *              'amount_min'        => 'l.amount >=',
     *              'amount_max'        => 'l.amount <=',
     *          ]);
     *
     * @param array $params
     */
    protected function registerFilterParams(array $params)
    {
        $this->registerSelectFilter(new SimpleSearchFilter($params));
    }
}
