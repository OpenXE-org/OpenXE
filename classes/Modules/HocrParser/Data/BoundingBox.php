<?php

namespace Xentral\Modules\HocrParser\Data;

use JsonSerializable;

class BoundingBox implements JsonSerializable
{
    /** @var array $data */
    private $data;
    private $tlx;
    private $tly;
    private $brx;
    private $bry;

    /**
     * @param int   $tlx
     * @param int   $tly
     * @param int   $brx
     * @param int   $bry
     * @param array $data Zusätzliche Nutzdaten
     */
    public function __construct($tlx, $tly, $brx, $bry, array $data = [])
    {
        $this->tlx = $tlx;
        $this->tly = $tly;
        $this->brx = $brx;
        $this->bry = $bry;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function GetDataAll()
    {
        return $this->data;
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function GetData($name)
    {
        return $this->data[$name];
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function SetData($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function HasData($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Liegt Punkt innerhalb der Box?
     *
     * @param int $x
     * @param int $y
     *
     * @return bool
     */
    public function IsPointWithin($x, $y)
    {
        if ($x > $this->tlx && $x < $this->brx &&
            $y > $this->tly && $y < $this->bry) {
            return true;
        }

        return false;
    }

    /**
     * Befindet sich Box rechts von übergebenen Koordinaten
     *
     * @param int $x
     * @param int $y
     *
     * @return bool
     */
    public function IsRightFromPoint($x, $y)
    {
        $isSameHeight = ($y > $this->tly && $y < $this->bry) ? true : false;
        if ($isSameHeight === true) {
            return ($x < $this->tlx) ? true : false;
        }

        return false;
    }

    /**
     * Befindet sich Box rechts von übergebenen Koordinaten
     *
     * @param int $x
     * @param int $y
     *
     * @return bool
     */
    public function IsLeftFromPoint($x, $y)
    {
        $isSameHeight = ($y > $this->tly && $y < $this->bry) ? true : false;
        if ($isSameHeight === true) {
            return ($x > $this->brx) ? true : false;
        }

        return false;
    }

    /**
     * Befindet sich die Box oberhalb der übergebenen Koordinaten
     *
     * @param int $x
     * @param int $y
     *
     * @return bool
     */
    public function IsAbovePoint($x, $y)
    {
        $isSameColumn = ($x > $this->tlx && $x < $this->brx) ? true : false;
        if ($isSameColumn === true) {
            return ($this->bry < $y) ? true : false;
        }

        return false;
    }

    /**
     * Befindet sich die Box unterhalb der übergebenen Koordinaten
     *
     * @param int $x
     * @param int $y
     *
     * @return bool
     */
    public function IsBelowPoint($x, $y)
    {
        $isSameColumn = ($x > $this->tlx && $x < $this->brx) ? true : false;
        if ($isSameColumn === true) {
            return ($this->tly > $y) ? true : false;
        }

        return false;
    }

    /**
     * Berechnet die Entfernung zwischen Mittelpunkt der Box und dem übergebenen Punkt
     *
     * @param int $x
     * @param int $y
     *
     * @return float
     */
    public function GetDistanceFromPoint($x, $y)
    {
        $center = $this->GetCenterPoint();

        return sqrt(pow($center['x'] - $x, 2) + pow($center['y'] - $y, 2));
    }

    /**
     * Gibt Mittelpunkt der Box zurück
     *
     * @return array
     */
    public function GetCenterPoint()
    {
        return [
            'x' => (int)($this->tlx + (($this->brx - $this->tlx) / 2)),
            'y' => (int)($this->tly + (($this->bry - $this->tly) / 2)),
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'tlx'  => $this->tlx,
            'tly'  => $this->tly,
            'brx'  => $this->brx,
            'bry'  => $this->bry,
            'data' => $this->data,
        ];
    }
}
