<?php

namespace Xentral\Widgets\Chart;

use InvalidArgumentException;
use JsonSerializable;

class Color implements JsonSerializable
{
    private $red;
    private $green;
    private $blue;
    private $alpha;

    /**
     * @param int   $red   Farbwert von 0 bis 255
     * @param int   $green Farbwert von 0 bis 255
     * @param int   $blue  Farbwert von 0 bis 255
     * @param float $alpha Transparenzwert von 0 bis 1
     */
    public function __construct($red = 0, $green = 0, $blue = 0, $alpha = 0.1)
    {
        $this->red = $this->ensureColorValue($red);
        $this->green = $this->ensureColorValue($green);
        $this->blue = $this->ensureColorValue($blue);
        $this->setAlpha($alpha);
    }

    /**
     * @param string $hexColor Beispiel: "#112233"
     *
     * @return self
     */
    public static function createFromHex($hexColor)
    {
        $hexColor = str_replace('#', '', $hexColor);
        if (strlen($hexColor) !== 6) {
            throw new InvalidArgumentException('Only full length hex values are supported.');
        }

        $parts = str_split($hexColor, 2);
        $red = hexdec($parts[0]);
        $green = hexdec($parts[1]);
        $blue = hexdec($parts[2]);

        return new self($red, $green, $blue, 1);
    }

    /**
     * @param string $cssRgba Beispiel: rgba(255, 128, 0, 0.5)
     *
     * @return self
     */
    public static function createFromCssRgba($cssRgba)
    {
        $cssRgba = str_replace(' ', '', $cssRgba);
        preg_match('/^rgba\((\d+),(\d+),(\d+),([\d\.]+)\);?/i', $cssRgba, $colors);

        return new self((int)$colors[1], (int)$colors[2], (int)$colors[3], (float)$colors[4]);
    }

    /**
     * @param string $cssRgb Beispiel: "rgb(255, 128, 0)"
     *
     * @return self
     */
    public static function createFromCssRgb($cssRgb)
    {
        $cssRgb = str_replace(' ', '', $cssRgb);
        preg_match('/^rgb\((\d+),(\d+),(\d+)\);?/i', $cssRgb, $colors);

        return new self((int)$colors[1], (int)$colors[2], (int)$colors[3], 1.0);
    }

    /**
     * @param int $value
     *
     * @return int
     */
    private function ensureColorValue($value)
    {
        $value = (int)$value;
        if ($value < 0) {
            $value = 0;
        }
        if ($value > 255) {
            $value = 255;
        }

        return $value;
    }

    /**
     * @param float $alpha Wert zwischen 0 und 1
     *
     * @return void
     */
    public function setAlpha($alpha)
    {
        $alpha = (float)$alpha;
        if ($alpha < 0.0) {
            $alpha = 0.0;
        }
        if ($alpha > 1.0) {
            $alpha = 1.0;
        }

        $this->alpha = $alpha;
    }

    /**
     * Farbwerte per Zufall variieren
     *
     * @param int $difference
     *
     * @return void
     */
    public function makeVariant($difference)
    {
        $difference = (int)$difference;

        $redVariant = $this->red + mt_rand($difference * -1, $difference);
        $this->red = $this->ensureColorValue($redVariant);

        $greenVariant = $this->green + mt_rand($difference * -1, $difference);
        $this->green = $this->ensureColorValue($greenVariant);

        $blueVariant = $this->blue + mt_rand($difference * -1, $difference);
        $this->blue = $this->ensureColorValue($blueVariant);
    }

    /**
     * Farbe heller machen; verändert nicht die Transparenz
     *
     * @param float $percent
     *
     * @return void
     */
    public function makeLighter($percent = 10.0)
    {
        $percent = (float)$percent;
        $difference = (int)($percent * 2.55 / 2);
        $this->red = $this->ensureColorValue($this->red + $difference);
        $this->blue = $this->ensureColorValue($this->blue + $difference);
        $this->green = $this->ensureColorValue($this->green + $difference);
    }

    /**
     * Farbe dunkler machen; verändert nicht die Transparenz
     *
     * @param float $percent
     *
     * @return void
     */
    public function makeDarker($percent = 10.0)
    {
        $percent = (float)$percent;
        $difference = (int)($percent * 2.55 / 2);
        $this->red = $this->ensureColorValue($this->red - $difference);
        $this->blue = $this->ensureColorValue($this->blue - $difference);
        $this->green = $this->ensureColorValue($this->green - $difference);
    }

    /**
     * Ausgabe in CSS rgba() Notation
     *
     * @return string
     */
    public function toCssRgba()
    {
        return sprintf(
            'rgba(%s, %s, %s, %s)',
            $this->red,
            $this->green,
            $this->blue,
            number_format($this->alpha, 3, '.', '')
        );
    }

    /**
     * Ausgabe in CSS rgb() Notation; Transparenz geht verloren
     *
     * @return string
     */
    public function toCssRgb()
    {
        return sprintf(
            'rgb(%s, %s, %s)',
            $this->red,
            $this->green,
            $this->blue
        );
    }

    /**
     * Ausgabe als Hex-Farbwert; Transparenz geht verloren
     *
     * @return string
     */
    public function toHex()
    {
        return sprintf(
            '#%s%s%s',
            str_pad(dechex($this->red), 2, '0', STR_PAD_LEFT),
            str_pad(dechex($this->green), 2, '0', STR_PAD_LEFT),
            str_pad(dechex($this->blue), 2, '0', STR_PAD_LEFT)
        );
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->__toString();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toCssRgba();
    }
}
