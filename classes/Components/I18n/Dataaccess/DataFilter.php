<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Dataaccess;


/**
 * Abstract filter class.
 *
 * @see      DataFilterInterface
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
abstract class DataFilter implements DataFilterInterface
{
    /**
     * Pointer to next filter in chain.
     *
     * @var DataFilterInterface
     */
    private $nextFilter = null;
    
    
    
    /**
     * {@inheritDoc}
     * @see \Xentral\Components\I18n\Dataaccess\DataFilterInterface::then()
     */
    public function then(DataFilterInterface $filter): DataFilterInterface
    {
        if (!$this->nextFilter) {
            $this->nextFilter = $filter;
        } else {
            $this->nextFilter->then($filter);
        }
        return $this;
    }
    
    
    
    /**
     * Applies the filter to the data and executes the next filter
     * if present.
     *
     * @see \Xentral\Components\I18n\Dataaccess\DataFilterInterface::__invoke()
     */
    public function __invoke(array $data): array
    {
// 		echo get_called_class()."::__invoke(\$data)".PHP_EOL;
        
        $filteredData = [];
        foreach ($data as $key => $val) {
            if ($this->selectItem($key, $val)) {
                $filteredData[$key] = $val;
            }
        }
        
        if ($this->nextFilter) {
            $filteredData = ($this->nextFilter)($filteredData);
        }
        return $filteredData;
    }
    
    
    
    /**
     * Check if the current item is to be selected for
     * the dataset.
     *
     * @param mixed $key
     * @param mixed $val
     *
     * @return bool
     */
    abstract protected function selectItem(&$key, &$val): bool;
}
