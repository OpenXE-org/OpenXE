<?php

namespace Xentral\Widgets\Chart;

/**
 * Spezielles Dataset für Balkendiagramme
 *
 * Besonderheiten:
 * * Balkenhintergrund und -rahmen haben keine Transparenz
 */
class BarDataset extends Dataset
{
    /** @var array $defaultAlphaValues Transparenz-Werte */
    protected static $defaultAlphaValues = [
        'borderColor' => 1.0,
        'backgroundColor' => 1.0,
        'hoverBorderColor' => 1.0,
    ];
}
