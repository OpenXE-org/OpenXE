<?php

namespace Xentral\Widgets\Chart;

/**
 * Spezielles Dataset für Pie-/Doughnut-Charts
 *
 * Besonderheiten:
 * * Farben müssen als Array angegeben werden; pro Wert eine Farbe
 * * Strichbreiten ('borderWidth') müssen als Array angegeben werden; pro Wert eine Breite
 *
 * http://www.chartjs.org/docs/master/charts/doughnut.html#dataset-properties
 */
class PieDataset extends Dataset
{
    /** @var array $defaultAlphaValues Transparenz-Werte */
    protected static $defaultAlphaValues = [
        'borderColor' => 1.0,
        'backgroundColor' => 1.0,
        'hoverBorderColor' => 1.0,
    ];

    /** @var int $colorPointer Merkt sich die zuletzt verwendete Default-Color */
    private $colorPointer = 0;

    /**
     * @inheritdoc
     */
    public function __construct($label, $data, array $options = [])
    {
        parent::__construct($label, $data, $options);

        $this->options['borderWidth'] = 3;

        unset($this->options['pointBackgroundColor']);
    }

    /**
     * @param Color $color
     *
     * @return void
     */
    public function setColor(Color $color)
    {
        $backgroundColor = clone $color;
        $borderColor = new Color(255, 255, 255);
        $hoverBorderColor = new Color(255, 255, 255);

        $borderColor->setAlpha($this->getDefaultAlphaValue('borderColor'));
        $backgroundColor->setAlpha($this->getDefaultAlphaValue('backgroundColor'));
        $hoverBorderColor->setAlpha($this->getDefaultAlphaValue('hoverBorderColors'));

        // Hintergrundfarbe als Array setzen
        // Pro Datensatz Farbe etwas heller machen
        $backgroundColors = [];
        $dataCount = $this->getDataCount();
        foreach ($this->data as $key => $value) {
            $difference = $key * 75 / $dataCount;
            $backgroundColorLighter = clone $backgroundColor;
            $backgroundColorLighter->makeLighter($difference);
            $backgroundColors[$key] = $backgroundColorLighter;
        }

        $this->options['borderColor'] = $borderColor;
        $this->options['backgroundColor'] = $backgroundColors;
        $this->options['hoverBorderColor'] = $hoverBorderColor;

        $this->hasColorAssigned = true;
    }

    /**
     * Hintergrundfarben pro Wert hinterlegen
     *
     * Die Anzahl der Farben sollte der Anzahl der Daten entsprechen
     *
     * @param array|string[] $colors Hexadecimal- oder RGB-Schreibweise
     *
     * @return void
     */
    public function setColors(array $colors)
    {
        // Rahmenfarbe auf Weiß setzen
        $borderColor = new Color(255, 255, 255);
        $hoverBorderColor = new Color(255, 255, 255);
        $borderColor->setAlpha($this->getDefaultAlphaValue('borderColor'));
        $hoverBorderColor->setAlpha($this->getDefaultAlphaValue('hoverBorderColors'));

        // Nur Hintergrundfarbe/Füllfarbe variieren
        $backgroundColors = [];
        //foreach ($colors as $color) {
        foreach ($this->data as $key => $value) {
            $color = $colors[$key];
            if (strpos($color, '#', 0) === 0) {
                $backgroundColor = Color::createFromHex($color);
            } else {
                $rgb = $this->getNextDefaultColor();
                $backgroundColor = new Color($rgb[0], $rgb[1], $rgb[2], 1);
            }

            $backgroundColor->setAlpha($this->getDefaultAlphaValue('backgroundColor'));
            $backgroundColors[] = $backgroundColor;
        }

        $this->options['borderColor'] = $borderColor;
        $this->options['hoverBorderColor'] = $hoverBorderColor;
        $this->options['backgroundColor'] = $backgroundColors;

        $this->hasColorAssigned = true;
    }

    /**
     * @return array RGB-Array
     */
    private function getNextDefaultColor()
    {
        $this->colorPointer++;
        if ($this->colorPointer >= count(self::$colorMap)) {
            $this->colorPointer = 0;
        }

        $colors = array_values(self::$colorMap);

        return $colors[$this->colorPointer];
    }
}
