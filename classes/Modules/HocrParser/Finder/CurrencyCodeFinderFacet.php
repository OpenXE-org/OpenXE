<?php

namespace Xentral\Modules\HocrParser\Finder;

use Xentral\Modules\HocrParser\Data\BoundingBox;
use Xentral\Modules\HocrParser\Data\BoundingBoxCollection;

class CurrencyCodeFinderFacet implements FinderFacetInterface
{
    /** @var array $validCodes */
    private $validCodes;

    /**
     * @param array $validCodes Array mit gültigen Währungscodes (drei-stelliger ISO-Code)
     */
    public function __construct(array $validCodes = ['EUR'])
    {
        $this->validCodes = $validCodes;
    }

    /**
     * @param string $text
     *
     * @return bool
     */
    public function MatchPreCondition($text)
    {
        if (!$this->IsCurrencyLikeValue($text)) {
            return false;
        }

        if (!$this->IsValidCurrency($text)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function IsCurrencyLikeValue($value)
    {
        return (bool)preg_match('/^[A-Z]{3}$/', $value);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function IsValidCurrency($value)
    {
        return in_array($value, $this->validCodes, true);
    }

    /**
     * @param array|BoundingBox[]   $candidates
     * @param BoundingBoxCollection $boxes
     *
     * @return string|false
     */
    public function Select(array $candidates, BoundingBoxCollection $boxes)
    {
        if (empty($candidates)) {
            return false;
        }

        // Einfach das erste Ergebnis zurückliefern;
        // In den PreConditions wurde schon sichergestellt dass es eine gültige Währung ist
        return $candidates[0]->GetData('text');
    }
}
