<?php

namespace Xentral\Modules\HocrParser\Finder;

use Xentral\Modules\HocrParser\Data\BoundingBox;
use Xentral\Modules\HocrParser\Data\BoundingBoxCollection;

class BoundingBoxFinder
{
    /** @var BoundingBoxCollection $boxes */
    private $boxes;

    /** @var array|RelativePositionFinderFacet[] $criteria */
    private $criteria;

    /** @var array $currencies */
    private $currencies;

    /**
     * @param BoundingBoxCollection               $boxes
     * @param array|RelativePositionFinderFacet[] $criteria
     * @param array                               $validCurrencies
     */
    public function __construct(BoundingBoxCollection $boxes, array $criteria, array $validCurrencies = [])
    {
        $this->boxes = $boxes;
        $this->criteria = $criteria;
        $this->currencies = $validCurrencies;
    }

    /**
     * @return array
     */
    public function Find()
    {
        $search = [];
        $result = [];

        // Alle Boxen finden die den jeweiligen Suchbegriff enthalten
        /** @var BoundingBox $box */
        foreach ($this->boxes->GetBoxes() as $box) {
            foreach ($this->criteria as $searchKey => $criteria) {
                $result[$searchKey] = null;

                // Alle Boxen sammeln die den Vorbedingungen entsprechen
                if ($criteria->MatchPreCondition($box->GetData('text'))) {
                    $search[$searchKey]['boxes'][] = $box;
                    $search[$searchKey]['criteria'] = $criteria;
                }
            }
        }

        // Schauen welche der Kandidaten die genauen Vorgaben erfüllt; der erste Treffer gewinnt
        foreach ($search as $searchKey => $searchInfo) {
            $criteria = $searchInfo['criteria'];
            $candidates = $searchInfo['boxes'];
            $result[$searchKey] = $criteria->Select($candidates, $this->boxes);
        }

        return $result;
    }
}

