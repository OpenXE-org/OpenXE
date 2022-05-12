<?php

namespace Xentral\Widgets\Chart;

use InvalidArgumentException;

class Chart
{
    /** @var array $validTypes */
    private static $validTypes = [
        'line',
        'bar',
        'radar',
        'pie',
        'doughnut',
        'polarArea',
        //'bubble',
        //'scatter',
    ];

    /** @var array $defaultColors In RGB */
    private static $defaultColors = [
        [162, 197, 90],
        [69, 185, 211],
        [246, 158, 6],
        [14, 131, 148],
    ];

    /** @var array $defaultOptions */
    private static $defaultOptions = [
        'responsive' => true,
        'responsiveAnimationDuration' => 0,
        'maintainAspectRatio' => true,
        'tooltips' => [
            'enabled' => true,
            'mode' => 'nearest',
            'backgroundColor' => 'rgba(0, 0, 0, 0.5)',
        ],
        'legend' => [
            'display' => true,
        ],
        'animation' => [
            'duration' => 1000,
            'animateRotate' => true, // Pie + Doughnut
        ],
        'scales' => [
            'xAxes' => [
                [
                    'display' => true,
                ],
            ],
            'yAxes' => [
                [
                    'display' => true,
                    'ticks' => [
                        'beginAtZero' => true,
                    ],
                ],
            ],
        ],
    ];

    protected $type;
    protected $labels;
    protected $datasets;
    protected $options;

    /** @var int $currentColor Zähler für die letzte Default-Farbe die verwendet wurde */
    private $currentColor;

    /**
     * @param string          $type    Chart-Typ
     * @param array           $labels
     * @param array|Dataset[] $datasets
     * @param array           $options chart.js Optionen
     */
    public function __construct($type = 'line', $labels = [], array $datasets = [], array $options = [])
    {
        if (!in_array($type, self::$validTypes, true)) {
            throw new InvalidArgumentException(sprintf('Chart type "%s" is not valid.', $type));
        }
        // Ungültige Werte in leeres Array wandeln
        if (!is_array($labels)) {
            $labels = [];
        }

        $this->type = $type;
        $this->labels = $labels;
        $this->datasets = $datasets;
        $this->options = array_replace_recursive(self::$defaultOptions, $options);

        if ($type === 'line') {
            $this->options['tooltips']['mode'] = 'index';
        }
        if ($type === 'doughnut' || $type === 'pie') {
            $this->options['scales']['xAxes'][0]['display'] = false;
            $this->options['scales']['yAxes'][0]['display'] = false;
        }
    }

    /**
     * @param array $labels
     *
     * @return void
     */
    public function addLabels($labels)
    {
        // Ungültige Werte in leeres Array wandeln
        if (!is_array($labels)) {
            $labels = [];
        }

        foreach ($labels as $label) {
            $this->addLabel($label);
        }
    }

    /**
     * @param string $label
     *
     * @return void
     */
    public function addLabel($label)
    {
        $this->labels[] = (string)$label;
    }

    /**
     * @param Dataset $dataset
     *
     * @return void
     */
    public function addDataset(Dataset $dataset)
    {
        $this->datasets[] = $dataset;
    }

    /**
     * Wie setYAxis(), nur dass das Dataset zusätzlich noch zum Chart hinzugefügt wird
     *
     * @see setYAxis()
     *
     * @param Dataset $dataset
     * @param string  $position
     * @param string  $type
     *
     * @return void
     */
    public function addDatasetAsYAxis(Dataset $dataset, $position = 'left', $type = 'linear')
    {
        $this->setYAxis($dataset, $position, $type);
        $this->addDataset($dataset);
    }

    /**
     * Übergebenes Dataset für die Anzeige der Y-Achse verwenden
     *
     * * Mehrere Y-Achsen sind möglich
     * * Dataset wird aber nicht zum Chart hinzugefügt; muss über addDataset() passieren
     *
     * @param Dataset $dataset
     * @param string  $position
     * @param string  $type
     *
     * @return void
     */
    public function setYAxis(Dataset $dataset, $position = 'left', $type = 'linear')
    {
        // Falls vorher noch kein Dataset als y-Achse definiert wurde, Default-y-Achsen-Config löschen.
        // (Ansonsten wird eine y-Achse zu viel angezeigt)
        $hasMultipleAxesConfig = array_key_exists('id', $this->options['scales']['yAxes'][0]);
        if (!$hasMultipleAxesConfig) {
            unset($this->options['scales']['yAxes']);
        }

        $id = $dataset->generateYAxisId();
        $this->options['scales']['yAxes'][] = [
            'id' => $id,
            'display' => true,
            'type' => $type,
            'position' => $position,
            'ticks' => [
                'beginAtZero' => true,
            ],
        ];
    }

    /**
     * @return void
     */
    public function accumulateData()
    {
        foreach ($this->datasets as $dataset) {
            $dataset->accumulateData();
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getData()
    {
        foreach ($this->datasets as $dataset) {
            /** @var Dataset $dataset */
            // Default-Farben durchiterieren, wenn noch keine Farbe gesetzt
            if (!$dataset->hasColorAssigned()) {
                $color = $this->getNextDefaultColor();
                $dataset->setColorByRgb(...$color);
            }
        }

        return [
            'labels' => $this->labels,
            'datasets' => $this->datasets,
        ];
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode([
            'type' => $this->getType(),
            'data' => $this->getData(),
            'options' => $this->getOptions(),
        ]);
    }

    /**
     * Liefert bei jedem Aufruf die nächste Farbe aus den Standard-Farben
     *
     * @return array Array mit drei Farbwerten [R, G, B]
     */
    private function getNextDefaultColor()
    {
        // Bei jedem Aufruf Index hochzählen
        $this->currentColor = $this->currentColor === null ? 0 : $this->currentColor + 1;

        // Wieder vorne anfangen wenn letzte Farbe ausgeliefert wurde
        if ($this->currentColor >= count(self::$defaultColors)) {
            $this->currentColor = 0;
        }

        return self::$defaultColors[$this->currentColor];
    }

    /**
     * Beim Klonen die Dataset-Objekte einzeln klonen
     *
     * Ansonsten beinhaltet das geklonte Chart-Objekt die Referenzen zum Ursprungsobjekt!
     *
     * @return void
     */
    public function __clone()
    {
        foreach ($this->datasets as $key => $dataset) {
            $this->datasets[$key] = clone $dataset;
        }
    }
}
