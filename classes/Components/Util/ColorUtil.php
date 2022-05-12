<?php

namespace Xentral\Components\Util;

use Xentral\Components\Util\Exception\InvalidArgumentException;

final class ColorUtil
{
    /**
     * @example '#369' or '#336699' => array('r' => 51, 'g' => 102, 'b' => 153)
     *
     * @param string $hexColor
     *
     * @throws InvalidArgumentException
     *
     * @return array ['r' => {$red}, 'g' => {$green}, 'b' => {$blue}]
     */
    public static function convertHexToRgb($hexColor)
    {
        self::ensureHexColorFormat($hexColor);

        $hexColor = self::normalizeHexColor($hexColor);
        $hexColor = str_replace('#', '', $hexColor);

        $colorParts = str_split($hexColor, 2);
        $red = (int)hexdec($colorParts[0]);
        $green = (int)hexdec($colorParts[1]);
        $blue = (int)hexdec($colorParts[2]);

        return ['r' => $red, 'g' => $green, 'b' => $blue];
    }

    /**
     * @param int $red [0-255]
     * @param int $green [0-255]
     * @param int $blue [0-255]
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public static function convertRgbToHex($red, $green, $blue)
    {
        if ($red < 0 || $red > 255) {
            throw new InvalidArgumentException(sprintf(
                'Invalid value for color red "%s". Color values must be between 0 and 255.', $red
            ));
        }
        if ($green < 0 || $green > 255) {
            throw new InvalidArgumentException(sprintf(
                'Invalid value for color green "%s". Color values must be between 0 and 255.', $green
            ));
        }
        if ($blue < 0 || $blue > 255) {
            throw new InvalidArgumentException(sprintf(
                'Invalid value for color blue "%s". Color values must be between 0 and 255.', $blue
            ));
        }

        $rHex = StringUtil::padLeft(dechex($red), 2, '0');
        $gHex = StringUtil::padLeft(dechex($green), 2, '0');
        $bHex = StringUtil::padLeft(dechex($blue), 2, '0');
        $hexColor = '#' . strtoupper($rHex . $gHex . $bHex);
        self::ensureHexColorFormat($hexColor);

        return $hexColor;
    }

    /**
     * Normalizes hex colors
     *
     * - Transforms shorthand hexcolors (e.g. #369) into long hexcolor format (#336699)
     * - Long hexcolor format (#336699) will be unchanged
     *
     * @param string $hexColor
     *
     * @return string
     */
    public static function normalizeHexColor($hexColor)
    {
        self::ensureHexColorFormat($hexColor);

        $hexColor = str_replace('#', '', $hexColor);

        if (strlen($hexColor) === 3) {
            $red = !empty($hexColor[0]) ? $hexColor[0] : '0';
            $green = !empty($hexColor[1]) ? $hexColor[1] : '0';
            $blue = !empty($hexColor[2]) ? $hexColor[2] : '0';

            $hexColor = $red . $red . $green . $green . $blue . $blue;
        }

        return '#' . $hexColor;
    }

    /**
     * Calculates the brightness level (YIQ formula); is the given color more darker or lighter?
     *
     * Result 'dark' means: The given color is more darker than light. When using the given color as
     * a background color, then use a light text color for better contrast (and vice versa).
     *
     * @param int $red   [0-255]
     * @param int $green [0-255]
     * @param int $blue  [0-255]
     *
     * @throws InvalidArgumentException
     *
     * @return string [dark|light]
     */
    public static function calculateBrightnessLevel($red, $green, $blue)
    {
        if ($red < 0 || $red > 255) {
            throw new InvalidArgumentException(sprintf(
                'Invalid value for color red "%s". Color values must be between 0 and 255.', $red
            ));
        }
        if ($green < 0 || $green > 255) {
            throw new InvalidArgumentException(sprintf(
                'Invalid value for color green "%s". Color values must be between 0 and 255.', $green
            ));
        }
        if ($blue < 0 || $blue > 255) {
            throw new InvalidArgumentException(sprintf(
                'Invalid value for color blue "%s". Color values must be between 0 and 255.', $blue
            ));
        }

        /** @see http://dannyruchtie.com/color-contrast-calculator-with-yiq/ */
        $yiqValue = ($red * 299 + $green * 587 + $blue * 114) / 1000;

        return $yiqValue >= 128 ? 'light' : 'dark';
    }

    /**
     * @param string $hexColor
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private static function ensureHexColorFormat($hexColor)
    {
        if (strpos($hexColor, '#') !== 0) {
            throw new InvalidArgumentException(sprintf(
                'Invalid format for hex color "%s". Hex color must start with hash char (#).',
                $hexColor
            ));
        }

        $hexString = substr_replace($hexColor, '', 0, 1);
        $hexCleaned = preg_replace('/[^0-9A-Fa-f]/', '', $hexString);
        if ($hexString !== $hexCleaned) {
            throw new InvalidArgumentException(sprintf(
                'Invalid format for hex color "%s". Hex color contains invalid chars. ' .
                'Valid chars are: A-F, a-f and 0-9.',
                $hexColor
            ));
        }

        $hexColorLength = strlen($hexColor);
        if ($hexColorLength !== 4 && $hexColorLength !== 7) {
            throw new InvalidArgumentException(sprintf(
                'Invalid format for hex color "%s". Hex color must be four or seven chars long; ' .
                'with leading hash char (#).',
                $hexColor
            ));
        }
    }
}
