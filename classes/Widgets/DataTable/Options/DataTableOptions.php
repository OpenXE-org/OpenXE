<?php

namespace Xentral\Widgets\DataTable\Options;

use JsonSerializable;

final class DataTableOptions implements JsonSerializable
{
    /** @var array $options Datatable initialisation options */
    private $options;

    /** @var array $defaultSorting */
    private $defaultSorting = [];

    /** @var array $postSorting */
    private $postSorting = [];

    /** @var array $preSorting */
    private $preSorting = [];

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $this->getDefaults();
        foreach ($options as $property => $value) {
            $this->setOption($property, $value);
        }
    }

    /**
     * @param string $property
     *
     * @return bool
     */
    public function hasOption($property)
    {
        return isset($this->options[(string)$property]);
    }

    /**
     * @param string     $property
     * @param mixed|null $fallbackValue
     *
     * @return mixed|null
     */
    public function getOption($property, $fallbackValue = null)
    {
        if ($this->hasOption($property)) {
            return $this->options[(string)$property];
        }

        return $fallbackValue;
    }

    /**
     * Sets/overwrites a property
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return void
     */
    public function setOption($property, $value)
    {
        $this->options[(string)$property] = $value;
    }

    /**
     * Unsets a property
     *
     * @param $property
     *
     * @return void
     */
    public function removeOption($property)
    {
        if ($this->hasOption($property)) {
            unset($this->options[(string)$property]);
        }
    }

    /**
     * @return array
     */
    public function getDefaultSorting()
    {
        return $this->defaultSorting;
    }

    /**
     * @return array
     */
    public function getPreSorting()
    {
        return $this->preSorting;
    }

    /**
     * @return array
     */
    public function getPostSorting()
    {
        return $this->postSorting;
    }

    /**
     * Default-Sortierung; Benutzer-Sortierung überschreibt Default-Sortierung
     *
     * @example ['lagerbestand' => 'DESC', 'bezeichnung' => 'ASC']
     *
     * @param array $sorting
     *
     * @return void
     */
    public function setDefaultSorting(array $sorting = [])
    {
        $this->defaultSorting = $sorting;
    }

    /**
     * Feste Vor-Sortierung; kann vom Benutzer nicht geändert werden
     *
     * @example ['lagerbestand' => 'DESC', 'bezeichnung' => 'ASC']
     *
     * @param array $sorting
     *
     * @return void
     */
    public function setPreSorting(array $sorting = [])
    {
        $this->preSorting = $sorting;
    }

    /**
     * Feste Nach-Sortierung; kann vom Benutzer nicht geändert werden
     *
     * @example ['lagerbestand' => 'DESC', 'bezeichnung' => 'ASC']
     *
     * @param array $sorting
     *
     * @return void
     */
    public function setPostSorting(array $sorting = [])
    {
        $this->postSorting = $sorting;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->options;
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
    private function getDefaults()
    {
        return [
            'processing'    => true,
            'serverSide'    => true,
            'ajax'          => [
                'url'  => null,
                'type' => 'GET',
                'data' => [],
            ],
            'scrollX'       => true,
            'orderCellsTop' => true, // Handle sorting events only on first header row
            'orderMulti'    => true, // Multiple column ordering ability control
            'dom'           => $this->getDefaultDomTemplate(),
            'language'      => [
                'emptyTable'     => 'Keine Einträge gefunden',
                'info'           => 'Zeige _START_ bis _END_ von _TOTAL_ Einträgen',
                'infoEmpty'      => 'Zeile 0 bis 0 von 0 Einträgen',
                'infoFiltered'   => '(gefiltert aus insgesamt _MAX_ Einträgen)',
                'infoPostFix'    => '',
                'decimal'        => ',',
                'thousands'      => '.',
                'lengthMenu'     => '_MENU_ Einträge pro Seite',
                'loadingRecords' => 'Lade...',
                'processing'     => 'Verarbeite...',
                'search'         => 'Suche:',
                'zeroRecords'    => 'Keine passenden Einträge gefunden',
                'paginate'       => [
                    'first'    => '&#8676;',
                    'last'     => '&#8677;',
                    'next'     => '&raquo;',
                    'previous' => '&laquo;',
                ],
                'aria'           => [
                    'sortAscending'  => ': Anklicken für aufsteigende Sortierung',
                    'sortDescending' => ': Anklicken für absteigende Sortierung',
                ],
            ],

            // Plugins
            'responsive'    => false,

            // Own config options
            'autoinit'      => true,
        ];
    }

    /**
     * - l = Length changing input control ("Einträge pro Seite")
     * - f = Filtering input (Search)
     * - t = Table
     * - i = Information summary ("Zeige 1 bis 10 von 14 Einträgen")
     * - p = Pagination
     * - r = Processing display element (Loading overlay)
     * - B = Buttons
     * - R = ColReorder (Column visibility)
     *
     * @see https://datatables.net/reference/option/dom
     *
     * @return string
     */
    private function getDefaultDomTemplate()
    {
        return
            "<'datatable-top'<'datatable-length'l><'datatable-search'f>" .
            'r>t' .
            "<'datatable-bottom'<'datatable-info'i><'datatable-buttons'B><'datatable-paginate'p>>";
    }
}
