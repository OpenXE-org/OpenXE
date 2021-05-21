<?php

namespace Xentral\Modules\Dashboard;

/**
 * Klasse ist ohne Funktion; dient lediglich als Ideensammlung
 */
class DashboardWidget
{
    /**
     * Einstellungsoptionen für Widget abrufen
     *
     * @return array
     */
    public function getOptions()
    {
        // Allgemeine Einstelloptionen für alle Widgets
        $general = [
            'dashboard'      => [
                'type'    => 'int',
                'options' => [
                    1 => 'Dashboard #1',
                    2 => 'Dashboard #2',
                ],
            ],
            'project'        => [
                'type'    => 'int|list',
                'options' => [
                    1 => 'Projekt 1',
                    2 => 'Projekt 2',
                ],
            ],
            'size' => [
                'type' => 'string',
                'options' => [
                    '1x1' => '1x1',
                    '1x2' => '1x2',
                    '2x1' => '2x1',
                    '2x2' => '2x2',
                ],
            ],
            'color'          => [
                'type'    => 'string',
                'options' => [
                    '#45B9D3' => 'Blau',
                    '#0E8394' => 'Dunkelblau',
                    '#A2C55A' => 'Grün',
                    '#F69E06' => 'Orange',
                ],
            ],
            'color_inverted' => [
                'type' => 'bool',
            ],
        ];

        // Spezielle Einstellungsoptionen für Uhrzeit-Widget
        $clock = [
            'time_format' => [
                'type'    => 'string',
                'default' => '%H:%i',
            ],
            'date_format' => [
                'type'    => 'string',
                'default' => '%d.%m.%Y',
            ],
        ];

        // Spezielle Einstellungsoptionen für Chart-Widget
        $chart = [
            'source'   => [
                'type'    => 'string',
                'options' => [
                    'offers'      => 'Angebote',
                    'orders'      => 'Bestellungen',
                    'invoices'    => 'Rechnungen',
                    'creditnotes' => 'Gutschriften',
                ],
            ],
            'interval' => [
                'type'    => 'string',
                'default' => 'day',
                'options' => [
                    'day'   => 'Tag',
                    'week'  => 'Woche',
                    'month' => 'Monat',
                ],
            ],
            'period'   => [
                'type' => 'int',
                'min'  => 2,
                'max'  => 14,
            ],
        ];

        return [];
    }

    /**
     * In welcher Kategorie wird das Widget gelistet?
     *
     * @return string
     */
    public function getCategory()
    {
        $all = ['time', 'sales', 'chart', 'email'];

        return 'time';
    }

    /**
     * Unter welchen Tags wird das Widget gelistet
     *
     * @return array
     */
    public function getTags()
    {
        $all = [
            'time',
            'sales',
            'chart',
            'shop',
            'article',
            'email',
            'ticket',
            'customer',
            'supplier',
            'orders',
            'offers',
            'invoices',
            'creditnotes',
        ];

        return ['sales', 'chart'];
    }
}
