<?php

namespace Xentral\Widgets\SuperSearch\Query;

use Xentral\Widgets\SuperSearch\Exception\InvalidArgumentException;

final class SearchQuery
{
    /** @var string $searchTerm */
    private $searchTerm;

    /** @var array $searchWords */
    private $searchWords = null;

    /**
     * @param string $searchTerm
     *
     * @throws InvalidArgumentException
     */
    public function __construct($searchTerm)
    {
        if (empty($searchTerm)) {
            throw new InvalidArgumentException('Parameter "searchTerm" is empty.');
        }

        $this->searchTerm = trim($searchTerm);
    }

    /**
     * @return string
     */
    public function getSearchTerm()
    {
        return $this->searchTerm;
    }

    /**
     * @return array
     */
    public function getSearchWords()
    {
        if ($this->searchWords === null) {
            $this->searchWords = (array)preg_split('/([\s]+)/um', $this->searchTerm, -1, PREG_SPLIT_NO_EMPTY);
        }

        return $this->searchWords;
    }
}
