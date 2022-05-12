<?php

declare(strict_types=1);

namespace Xentral\Modules\HocrParser\Service;

use DOMDocument;
use Xentral\Modules\HocrParser\Data\BoundingBox;

final class HocrParser
{
    /**
     * @param string $content HOCR-Dokument
     *
     * @return array|BoundingBox[]
     */
    public function parse(string $content): array
    {
        $boxes = [];

        $dom = new DOMDocument;
        $dom->loadXML($content);
        $spans = $dom->getElementsByTagName('span');

        /** @var DOMElement $span */
        foreach ($spans as $span) {
            if ($span->getAttribute('class') === 'ocrx_word') {
                $title = $span->getAttribute('title');
                $coords = $this->extractCoordinates($title);
                $text = trim($span->nodeValue);

                // Boxen ohne Text auslassen; kann vorkommen bei Barcode-Fonts
                if (empty($text)) {
                    continue;
                }

                // Boxen mit Text sammeln
                $boxes[] = new BoundingBox(
                    $coords['tlx'], $coords['tly'], $coords['brx'], $coords['bry'], ['text' => $text]
                );
            }
        }

        return $boxes;
    }

    /**
     * @param string $text
     *
     * @return array|bool
     */
    private function extractCoordinates(string $text)
    {
        // bbox 599 2737 743 2758; x_wconf 96
        $parts = explode(' ', $text);
        if ($parts[0] === 'bbox') {
            return [
                'tlx' => (int)$parts[1], // Top left X-Coordinate
                'tly' => (int)$parts[2], // Top left Y
                'brx' => (int)$parts[3], // Bottom right X
                'bry' => (int)$parts[4], // Bottom right Y
            ];
        }

        return false;
    }
}
