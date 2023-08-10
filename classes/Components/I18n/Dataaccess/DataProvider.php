<?php

declare(strict_types=1);

namespace Xentral\Components\I18n\Dataaccess;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * Abstract implementation of a general data provider.
 *
 * @see      DataProviderInterface
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
abstract class DataProvider implements Countable, Iterator, ArrayAccess, DataProviderInterface
{
    use CountableTrait;
    use IteratorTrait;
    use ArrayAccessTrait;
    
    
    /**
     * Holds filtered data.
     *
     * @var mixed
     */
    private $DataProvider_DATA = null;
    
    
    
    /**
     * Create the object and apply data filter.
     *
     * @param DataFilterInterface|null $filter
     */
    public function __construct(DataFilterInterface $filter = null)
    {
        if ($filter) {
            $this->DataProvider_DATA = $filter($this->getOriginalData());
        } else {
            $this->DataProvider_DATA = $this->getOriginalData();
        }
    }
    
    
    
    /**
     * Returns the original data array).
     * Raw data before any filtering takes place.
     *
     * @return array
     */
    abstract protected function getOriginalData(): array;
    
    
    
    /**
     * Returns an array suitable for select fields.
     * The key of the filtered data set is used as key of the
     * array and $desiredName field is used as value.
     *
     * @param string $desiredName
     *
     * @return array;
     */
    public function getMultiOptions($desiredName = 'NAME_deu'): array
    {
        $a = [];
        foreach ($this as $key => $l) {
            $a[$key] = $l[$desiredName];
        }
        return $a;
    }
    
    
    
    /**
     * Returns the field $desiredName from the record $id.
     *
     * @param mixed  $id
     * @param string $desiredName
     *
     * @return string
     */
    public function getString($id, $desiredName): string
    {
        if (!isset($this[$id])) {
            throw new Exception\OutOfRangeException("Index '{$id}' not found");
        }
        $d = $this[$id];
        if ($desiredName) {
            if ($desiredName == 'POST') {
                return strtoupper($this->getString($id, 'NAME_eng'));
            }
            return $d[$desiredName];
        }
        throw new Exception\OutOfRangeException("No '{$desiredName}' data for '{$id}'");
    }
    
    
    
    /**
     * Returns the item at position $id.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function getData($id)
    {
        if (empty($id)) {
            return null;
        }
        if (!isset($this[$id])) {
            throw new Exception\OutOfRangeException("Index '{$id}' not found.");
        }
        return $this[$id];
    }
}
