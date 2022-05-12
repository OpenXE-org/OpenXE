<?php

declare(strict_types=1);

namespace Xentral\Modules\HocrParser\Service;

use Xentral\Modules\HocrParser\Data\BoundingBoxCollection;
use Xentral\Modules\HocrParser\Finder\BoundingBoxFinder;
use Xentral\Modules\HocrParser\Finder\CurrencyCodeFinderFacet;
use Xentral\Modules\HocrParser\Finder\PatternMatcher;
use Xentral\Modules\HocrParser\Finder\RelativePositionFinderFacet;

class HocrDataExtractor
{
    /** @var array|string[] $validCurrencies */
    private $validCurrencies;

    /** @var HocrParser $parser */
    private $parser;

    public function __construct(HocrParser $parser, array $validCurrencies)
    {
        $this->validCurrencies = $validCurrencies;
        $this->parser = $parser;
    }

    /**
     * @param string $document HOCR-Dokument
     * @param array  $settings Einstellungen (pro Lieferant)
     *
     * @return array
     */
    public function extractLiabilityDataFromHocrDocument(string $document, array $settings): array
    {
        // Suchkriterien zusammenstellen
        $criteria = [];
        if(!empty($settings['invoice_number']['term']) && !empty($settings['invoice_number']['direction'])){
            $criteria['invoice_number'] =
                new RelativePositionFinderFacet(
                    $settings['invoice_number']['term'],
                    $settings['invoice_number']['direction'],
                    PatternMatcher::PATTERN_DOCUMENT_NUMBER
                );
        }
        if(!empty($settings['invoice_date']['term']) && !empty($settings['invoice_date']['direction'])){
            $criteria['invoice_date'] =
                new RelativePositionFinderFacet(
                    $settings['invoice_date']['term'],
                    $settings['invoice_date']['direction'],
                    PatternMatcher::PATTERN_DATE
                );
        }
        if(!empty($settings['total_gross']['term']) && !empty($settings['total_gross']['direction'])){
            $criteria['total_gross'] =
                new RelativePositionFinderFacet(
                    $settings['total_gross']['term'],
                    $settings['total_gross']['direction'],
                    PatternMatcher::PATTERN_MONEY
                );
        }
        // Immer nach Währungen suchen
        $waehrungen = $this->validCurrencies;
        $criteria['currency'] = new CurrencyCodeFinderFacet($waehrungen);

        // BoundingBoxes aus HOCR extrahieren + Suche anhand der Suchkriterien durchführen
        $boxes = new BoundingBoxCollection($this->parser->parse($document));
        $finder = new BoundingBoxFinder($boxes, $criteria);

        return $finder->Find();
    }
}
