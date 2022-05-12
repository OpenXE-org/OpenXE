<?php

namespace Xentral\Modules\HocrParser\Finder;

use Xentral\Modules\HocrParser\Data\BoundingBox;
use Xentral\Modules\HocrParser\Data\BoundingBoxCollection;
use Xentral\Modules\HocrParser\Exception\InvalidArgumentException;

class RelativePositionFinderFacet implements FinderFacetInterface
{
    const DIRECTION_LEFT = 'left';
    const DIRECTION_RIGHT = 'right';
    const DIRECTION_ABOVE = 'above';
    const DIRECTION_BELOW = 'below';

    /** @var array $validDirections */
    private static $validDirections = [
        self::DIRECTION_ABOVE,
        self::DIRECTION_RIGHT,
        self::DIRECTION_BELOW,
        self::DIRECTION_LEFT,
    ];

    /** @var string $text */
    private $text;

    /** @var PatternMatcher $matcher */
    private $matcher;

    /** @var string $direction */
    private $direction;

    /**
     * @param string $searchText
     * @param string $direction
     * @param string $pattern
     */
    public function __construct($searchText, $direction, $pattern)
    {
        if (!in_array($direction, self::$validDirections, true)) {
            throw new InvalidArgumentException(sprintf('Direction "%s" is not allowed.', $direction));
        }

        $this->matcher = new PatternMatcher($pattern);
        $this->text = trim($searchText);
        $this->direction = $direction;
    }

    /**
     * @param string $text
     *
     * @return bool
     */
    public function MatchPreCondition($text)
    {
        return $text === $this->text;
    }

    /**
     * @param array|BoundingBox[]   $candidates
     * @param BoundingBoxCollection $boxes
     *
     * @return string|false
     */
    public function Select(array $candidates, BoundingBoxCollection $boxes)
    {
        foreach ($candidates as $candidate) {
            $coords = $candidate->GetCenterPoint();
            /** @var BoundingBox $nearestBox */
            $nearestBox = false;

            switch ($this->direction) {
                case self::DIRECTION_RIGHT:
                    $nearestBox = $boxes->GetNearestBoxRightFromPoint($coords['x'], $coords['y']);
                    break;
                case self::DIRECTION_LEFT:
                    $nearestBox = $boxes->GetNearestBoxLeftFromPoint($coords['x'], $coords['y']);
                    break;
                case self::DIRECTION_ABOVE:
                    $nearestBox = $boxes->GetNearestBoxAboveFromPoint($coords['x'], $coords['y']);
                    break;
                case self::DIRECTION_BELOW:
                    $nearestBox = $boxes->GetNearestBoxBelowFromPoint($coords['x'], $coords['y']);
                    break;
            }

            if ($nearestBox === false) {
                continue;
            }

            // Prüfen ob Wert einem bestimmten Muster folgt
            $patternMatching = $this->IsPatternMatching($nearestBox->GetData('text'));
            if ($patternMatching) {
                return $nearestBox->GetData('text');
            }
        }

        return false;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function IsPatternMatching($value)
    {
        return $this->matcher->Match($value);
    }
}

