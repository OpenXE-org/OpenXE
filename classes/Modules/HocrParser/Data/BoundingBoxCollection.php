<?php

namespace Xentral\Modules\HocrParser\Data;

use Xentral\Components\ScanbotApi\Exception\RuntimeException;

class BoundingBoxCollection
{
    /** @var array|BoundingBox[] $boxes */
    private $boxes;

    /**
     * @param array|BoundingBox[] $boxes
     */
    public function __construct(array $boxes = [])
    {
        $this->boxes = $boxes;
    }

    /**
     * @return array|BoundingBox[]
     */
    public function GetBoxes()
    {
        return $this->boxes;
    }

    /**
     * Box mit der kürzesten Entfernung rechts vom übergebenen Punkt zurückgeben
     *
     * @param int $x
     * @param int $y
     *
     * @return BoundingBox|false
     */
    public function GetNearestBoxRightFromPoint($x, $y)
    {
        // Erstmal alle Elemente rechts vom Punkt finden; inkl. Entfernung zum Punkt
        $candidates = $this->GetBoxesRightFromPoint($x, $y);

        return $this->GetBoxWithLowestDistance($candidates);
    }

    /**
     * Alle Boxen zurückgeben die sich rechts vom übergebenen Punkt befinden; inkl. Entfernung
     *
     * @param int $x
     * @param int $y
     *
     * @return array|BoundingBox[]
     */
    public function GetBoxesRightFromPoint($x, $y)
    {
        $result = [];

        foreach ($this->boxes as $box) {
            if ($box->IsRightFromPoint($x, $y)) {
                $distance = $box->GetDistanceFromPoint($x, $y);
                $box->SetData('distance', $distance);
                $result[] = $box;
            }
        }

        return $result;
    }

    /**
     * Box mit dem geringsten Abstand ermitteln
     *
     * Hinweis: Data-Eigenschaft "distance" muss dafür gesetzt sein.
     *
     * @param array|BoundingBox[] $boxes
     *
     * @return BoundingBox|false
     */
    private function GetBoxWithLowestDistance(array $boxes)
    {
        // Index neu aufbauen
        $boxes = array_values($boxes);

        // Keine Box gefunden
        if (count($boxes) === 0) {
            return false;
        }

        // Nur eine Box gefunden
        if (count($boxes) === 1) {
            return $boxes[0];
        }

        $shortestDistance = $boxes[0]->GetData('distance');
        $shortestDistanceKey = 0;

        // Prüfen ob Entfernung gesetzt sind
        if ($shortestDistance === null) {
            throw new RuntimeException('Distance not set.');
        }

        // Box mit der geringsten Entfernung suchen
        /** @var BoundingBox $box */
        foreach ($boxes as $key => $box) {
            $boxDistance = $box->GetData('distance');
            if ($boxDistance < $shortestDistance) {
                $shortestDistance = $boxDistance;
                $shortestDistanceKey = $key;
            }
        }

        return $boxes[$shortestDistanceKey];
    }

    /**
     * Box mit der kürzesten Entfernung links vom übergebenen Punkt zurückgeben
     *
     * @param int $x
     * @param int $y
     *
     * @return BoundingBox|false
     */
    public function GetNearestBoxLeftFromPoint($x, $y)
    {
        // Erstmal alle Elemente rechts vom Punkt finden; inkl. Entfernung zum Punkt
        $candidates = $this->GetBoxesLeftFromPoint($x, $y);

        return $this->GetBoxWithLowestDistance($candidates);
    }

    /**
     * Alle Boxen zurückgeben die sich links vom übergebenen Punkt befinden; inkl. Entfernung
     *
     * @param int $x
     * @param int $y
     *
     * @return array|BoundingBox[]
     */
    public function GetBoxesLeftFromPoint($x, $y)
    {
        $result = [];

        foreach ($this->boxes as $box) {
            if ($box->IsLeftFromPoint($x, $y)) {
                $distance = $box->GetDistanceFromPoint($x, $y);
                $box->SetData('distance', $distance);
                $result[] = $box;
            }
        }

        return $result;
    }

    /**
     * Box mit der kürzesten Entfernung oberhalb vom übergebenen Punkt zurückgeben
     *
     * @param int $x
     * @param int $y
     *
     * @return BoundingBox|false
     */
    public function GetNearestBoxAboveFromPoint($x, $y)
    {
        $candidates = $this->GetBoxesAboveFromPoint($x, $y);

        return $this->GetBoxWithLowestDistance($candidates);
    }

    /**
     * Alle Boxen zurückgeben die sich oberhalb vom übergebenen Punkt befinden; inkl. Entfernung
     *
     * @param int $x
     * @param int $y
     *
     * @return array|BoundingBox[]
     */
    public function GetBoxesAboveFromPoint($x, $y)
    {
        $result = [];

        foreach ($this->boxes as $box) {
            if ($box->IsAbovePoint($x, $y)) {
                $distance = $box->GetDistanceFromPoint($x, $y);
                $box->SetData('distance', $distance);
                $result[] = $box;
            }
        }

        return $result;
    }

    /**
     * Box mit der kürzesten Entfernung unterhalb vom übergebenen Punkt zurückgeben
     *
     * @param int $x
     * @param int $y
     *
     * @return BoundingBox|false
     */
    public function GetNearestBoxBelowFromPoint($x, $y)
    {
        $candidates = $this->GetBoxesBelowFromPoint($x, $y);

        return $this->GetBoxWithLowestDistance($candidates);
    }

    /**
     * Alle Boxen zurückgeben die sich unterhalb vom übergebenen Punkt befinden; inkl. Entfernung
     *
     * @param int $x
     * @param int $y
     *
     * @return array|BoundingBox[]
     */
    public function GetBoxesBelowFromPoint($x, $y)
    {
        $result = [];

        foreach ($this->boxes as $box) {
            if ($box->IsBelowPoint($x, $y)) {
                $distance = $box->GetDistanceFromPoint($x, $y);
                $box->SetData('distance', $distance);
                $result[] = $box;
            }
        }

        return $result;
    }
}
