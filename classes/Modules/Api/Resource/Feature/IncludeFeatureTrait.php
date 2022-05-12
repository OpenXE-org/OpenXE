<?php

namespace Xentral\Modules\Api\Resource\Feature;

use Xentral\Modules\Api\Exception\InvalidArgumentException;
use Xentral\Modules\Api\Exception\ResourceNotFoundException;
use Xentral\Modules\Api\Resource\AbstractResource;

trait IncludeFeatureTrait
{
    /** @var array $includeSettings */
    private $includeSettings;

    /**
     * @example in configure-Methode der Resource:
     *     $this->registerIncludes([
     *         'projekte' => [
     *             'key'      => 'projekt',
     *             'resource' => ProjectResource::class,
     *             'columns'  => [
     *                 'p.id',
     *                 'p.name',
     *                 'p.abkuerzung',
     *                 'p.beschreibung',
     *                 'p.farbe',
     *             ],
     *         ],
     *     ]);
     *
     * @param array $includes
     */
    protected function registerIncludes($includes)
    {
        $this->includeSettings = $includes;
    }

    /**
     * @param array $includes
     * @param array $items
     * @param bool  $isCollection true=Mehrzeilig, false=Assoziatives Array
     *
     * @return array
     */
    protected function integrateIncludes(array $includes, array &$items, $isCollection = true)
    {
        // Keine Includes gesetzt
        if (empty($includes)) {
            return $items;
        }
        if (empty($this->includeSettings)) {
            return $items;
        }

        // Doppelte Includes entfernen
        $includes = array_unique($includes);

        // Einzelnes Item in Collection verwandeln
        if (!$isCollection) {
            $items = [$items];
        }

        foreach ($includes as $includeName) {

            if (empty($includeName)) {
                continue;
            }

            $settings = $this->getIncludeSetting($includeName);
            $subKey = $settings['key'];

            if (empty($subKey)) {
                throw new \RuntimeException(sprintf(
                    'Include "%s" not posible. Key is missing.', $includeName
                ));
            }

            // Nur bestimmte Spalten inkludieren?
            $columns = isset($settings['columns']) ? $settings['columns'] : [];

            // 1:n Beziehung zwischen Resource und Subresource
            if (isset($settings['filter'])) {

                /** @var AbstractResource $subResource */
                $subResource = $this->getResource($settings['resource']);

                foreach ($items as &$item) {

                    // Filter aufbereiten
                    $filter = $settings['filter'];
                    foreach ($filter as &$filterItem) {
                        // Filter benötigt Wert aus Haupt-Resource
                        if (strpos($filterItem['value'], ':') === 0) {
                            $key = substr_replace($filterItem['value'], '', 0, 1);
                            $filterItem['value'] = $item[$key];
                        }
                    }
                    unset($filterItem);
                    $filter = ['filter' => $filter]; // In ComplexSearch-Filter wandeln

                    // Sortierung vorhanden?
                    $sort = !empty($settings['sort']) ? $settings['sort'] : [];

                    try {
                        /** @var AbstractResource $subResource */
                        $subResult = $subResource->getList($filter, $sort, $columns, [], 1, 1000);
                        $subItems = $subResult->getData();
                    } catch (ResourceNotFoundException $e) {
                        $subItems = [];
                    }
                    $item[$settings['key']] = $subItems;
                }
                unset($item);

            // 1:1 Beziehung zwischen Resource und Subresource
            } else {

                // Prüfen ob Spalte zum Integrieren in Haupt-Ergebnis existiert
                if (!$this->arrayColumnExists($items, $subKey)) {
                    throw new \RuntimeException(sprintf(
                        'Include "%s" not posible. Key "%s" is missing.', $includeName, $subKey
                    ));
                }

                // Benötigte Subresourcen-IDs aus Haupt-Ergebnis holen
                $subIds = array_unique(array_column($items, $subKey));
                if (empty($subIds)) {
                    continue;
                }

                // Subresourcen anhand der IDs laden
                try {
                    /** @var AbstractResource $subResource */
                    $subResource = $this->getResource($settings['resource']);
                    $subResult = $subResource->getIds($subIds, $columns);
                } catch (ResourceNotFoundException $e) {
                    continue;
                }

                // Gefundene Subresourcen in Haupt-Ergebnis einbinden
                array_walk($items, function (&$item, $id, $subItems) use ($subKey) {
                    $subId = (int)$item[$subKey];
                    if (isset($subItems[$subId])) {
                        $item[$subKey] = $subItems[$subId];
                    }
                }, $subResult->getData());

            }
        }

        if (!$isCollection) {
            return $items[0];
        }

        return $items;
    }

    /**
     * @param string $includeName
     *
     * @return array
     */
    private function getIncludeSetting($includeName)
    {
        if (!isset($this->includeSettings[$includeName])) {
            throw new InvalidArgumentException(
                sprintf('Include "%s" is not registered.', $includeName)
            );
        }

        return $this->includeSettings[$includeName];
    }

    /**
     * @param array  $items
     * @param string $keyName
     *
     * @return bool
     */
    private function arrayColumnExists(array $items, $keyName)
    {
        $row = current($items);

        return array_key_exists($keyName, $row);
    }
}
