<?php

namespace Xentral\Widgets\Chart;

use InvalidArgumentException;
use JsonSerializable;

class Dataset implements JsonSerializable
{
    const LINE_STYLE_SOLID = 'solid';
    const LINE_STYLE_DASHED = 'dashed';
    const LINE_STYLE_DOTTED = 'dotted';

    const COLOR_GREEN = 'green';
    const COLOR_BLUE = 'blue';
    const COLOR_ORANGE = 'orange';
    const COLOR_DARKBLUE = 'darkblue';

    /** @var array $validLineStyles */
    protected static $validLineStyles = [
        self::LINE_STYLE_SOLID,
        self::LINE_STYLE_DOTTED,
        self::LINE_STYLE_DASHED,
    ];

    /** @var array $colorMap */
    protected static $colorMap = [
        self::COLOR_GREEN => [162, 197, 90],
        self::COLOR_BLUE => [69, 185, 211],
        self::COLOR_ORANGE => [246, 158, 6],
        self::COLOR_DARKBLUE => [14, 131, 148],
    ];

    /** @var array $defaultOptions Standardwerte für Dataset-Optionen */
    protected static $defaultOptions = [
        'pointBorderWidth' => 1,
        'pointHoverBorderWidth' => 2,
        'pointRadius' => 1,
        'pointHoverRadius' => 5,
        'pointHitRadius' => 15,
        'lineTension' => 0.2,
        'fill' => true,
        'borderWidth' => 3,
        'borderDash' => [],
        'borderCapStyle' => 'butt',
        'borderColor' => 'rgba(0, 0, 0, 0.1)',
        'backgroundColor' => 'rgba(0, 0, 0, 0.1)',
        'pointBackgroundColor' => 'rgba(0, 0, 0, 0.1)',
    ];

    /** @var array $defaultAlphaValues Transparenz-Werte */
    protected static $defaultAlphaValues = [
        'borderColor' => 1.0,
        'backgroundColor' => 0.1,
        'pointBackgroundColor' => 1.0,
    ];

    protected $data;
    protected $label;
    protected $options;

    /** @var bool $isDataAccumulated Gibt an ob Daten bereits kumuliert wurden */
    protected $isDataAccumulated = false;

    /** @var bool $hasColorSet Gibt an ob bereits eine Farbe manuell gesetzt wurde */
    protected $hasColorAssigned = false;

    /**
     * @param string $label
     * @param array  $data
     * @param array  $options
     */
    public function __construct($label, $data, array $options = [])
    {
        // Ungültige Werte in leeres Array wandeln
        if (!is_array($data)) {
            $data = [];
        }

        $this->data = $data;
        $this->label = $label;
        $this->options = array_replace(self::$defaultOptions, $options);

        $defaultColor = new Color(0, 0, 0, 0.1);
        if (!is_object($this->options['borderColor'])) {
            $this->options['borderColor'] = $defaultColor;
        }
        if (!is_object($this->options['backgroundColor'])) {
            $this->options['backgroundColor'] = $defaultColor;
        }
        if (!is_object($this->options['pointBackgroundColor'])) {
            $this->options['pointBackgroundColor'] = $defaultColor;
        }
    }

    /**
     * @return int
     */
    public function getDataCount()
    {
        return count($this->data);
    }

    /**
     * @return bool Wurde bereits eine Farbe gesetzt
     */
    public function hasColorAssigned()
    {
        return $this->hasColorAssigned;
    }

    /**
     * @return void
     */
    public function accumulateData()
    {
        if ($this->isDataAccumulated === true) {
            return;
        }

        $sum = 0;
        foreach ($this->data as $key => $value) {
            $sum += (float)$value;
            $this->data[$key] = $sum;
        }

        $this->isDataAccumulated = true;
    }

    /**
     * @param string $lineStyle
     *
     * @return void
     */
    public function setLineStyle($lineStyle)
    {
        if (!in_array($lineStyle, self::$validLineStyles, true)) {
            throw new InvalidArgumentException(sprintf(
                'Line style "%s" is not valid.', $lineStyle
            ));
        }

        if ($lineStyle === self::LINE_STYLE_DOTTED) {
            $this->options['borderDash'] = [1, 15];
            $this->options['borderCapStyle'] = 'round';
        }
        if ($lineStyle === self::LINE_STYLE_DASHED) {
            $this->options['borderDash'] = [15, 10];
            $this->options['borderCapStyle'] = 'butt';
        }
        if ($lineStyle === self::LINE_STYLE_SOLID) {
            $this->options['borderDash'] = [];
            $this->options['borderCapStyle'] = 'butt';
        }
    }

    /**
     * @param Color $color
     *
     * @return void
     */
    public function setColor(Color $color)
    {
        $borderColor = clone $color;
        $backgroundColor = clone $color;
        $pointBackgroundColor = clone $color;

        $borderColor->setAlpha($this->getDefaultAlphaValue('borderColor'));
        $backgroundColor->setAlpha($this->getDefaultAlphaValue('backgroundColor'));
        $pointBackgroundColor->setAlpha($this->getDefaultAlphaValue('pointBackgroundColor'));

        $this->options['borderColor'] = $borderColor;
        $this->options['backgroundColor'] = $backgroundColor;
        $this->options['pointBackgroundColor'] = $pointBackgroundColor;

        $this->hasColorAssigned = true;
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return void
     */
    public function setColorByRgb($red, $green, $blue)
    {
        $this->setColor(new Color($red, $green, $blue, 1.0));
    }

    /**
     * @param string $hexColor
     *
     * @return void
     */
    public function setColorByHex($hexColor)
    {
        $hexColor = str_replace('#', '', $hexColor);
        if (strlen($hexColor) !== 6) {
            throw new InvalidArgumentException('Only full length hex values are supported.');
        }

        $parts = str_split($hexColor, 2);
        $red = hexdec($parts[0]);
        $green = hexdec($parts[1]);
        $blue = hexdec($parts[2]);

        $this->setColorByRgb($red, $green, $blue);
    }

    /**
     * @param string $colorName
     *
     * @return void
     */
    public function setColorByName($colorName)
    {
        if (!isset(self::$colorMap[$colorName])) {
            throw new InvalidArgumentException(sprintf(
                'Color name "%s" is not valid.', $colorName
            ));
        }

        $rgb = self::$colorMap[$colorName];
        $this->setColorByRgb(...$rgb);
    }

    /**
     * Zufällige ID generieren; wird für Multiple Axes Config benötigt
     *
     * @see http://www.chartjs.org/docs/latest/axes/cartesian/#axis-id
     *
     * @return string
     */
    public function generateYAxisId()
    {
        if (!isset($this->options['yAxisID'])) {
            $this->options['yAxisID'] = uniqid('', false);
        }

        return $this->options['yAxisID'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = $this->options;
        $result['data'] = $this->data;
        if (!empty($this->label)) {
            $result['label'] = $this->label;
        }

        return $result;
    }

    /**
     * @param string $colorType
     *
     * @return float
     */
    protected function getDefaultAlphaValue($colorType)
    {
        if (!isset(static::$defaultAlphaValues[$colorType])) {
            return 1.0;
        }

        // Wichtig: static nicht self!
        // Sonst hat Überschreiben von $defaultAlphaValues in abgeleiteten Klassen keine Auswirkung.
        return static::$defaultAlphaValues[$colorType];
    }
}
