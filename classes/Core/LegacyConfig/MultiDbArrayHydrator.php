<?php

namespace Xentral\Core\LegacyConfig;

use Config;
use Xentral\Core\LegacyConfig\Exception\InvalidArgumentException;

final class MultiDbArrayHydrator
{
    /**
     * @param Config $defaultConfig
     * @param array  $multiDbArray
     *
     * @throws InvalidArgumentException
     *
     * @return array $multiDbArray
     */
    public static function hydrate(Config $defaultConfig, $multiDbArray)
    {
        if (!is_array($multiDbArray)) {
            throw new InvalidArgumentException('Can not hydrate array. Parameter is not an array.');
        }

        $result = $multiDbArray;
        $result = self::fillEmptyValues($defaultConfig, $result);
        $result = self::includeDefaultConfig($defaultConfig, $result);
        $result = self::prepareArrayKeys($result);

        return $result;
    }

    /**
     * Fülle leere Werte mit Werten aus der Default-Config, so dass alle Einträge die gleiche Struktur haben.
     *
     * @param Config $defaultConfig
     * @param array  $multiDbArray
     *
     * @return array
     */
    private static function fillEmptyValues(Config $defaultConfig, $multiDbArray)
    {
        $result = [];

        // MultiDb-Array mit Werten aus der Default-Config füllen
        foreach ($multiDbArray as $key => $item) {

            // Beschreibung darf nicht leer sein; als Fallback den Datenbanknamen verwenden
            $description = !empty($item['description']) ? $item['description'] : $defaultConfig->WFdbname;

            // Cronjobs nur aktivieren, wenn Einstellung vorhanden und gesetzt (Default `false`).
            $cronjobsActive = (int)$item['cronjob'] === 1;

            if(!empty($item['dbname']) && $defaultConfig->WFdbname === $item['dbname']) {
                $item = [];
            }

            $dbhost = !empty($item['dbhost']) ? $item['dbhost'] : $defaultConfig->WFdbhost;
            $dbport = !empty($item['dbport']) ? $item['dbport'] : $defaultConfig->WFdbport;
            $dbname = !empty($item['dbname']) ? $item['dbname'] : $defaultConfig->WFdbname;
            $dbuser = !empty($item['dbuser']) ? $item['dbuser'] : $defaultConfig->WFdbuser;
            $dbpass = !empty($item['dbpass']) ? $item['dbpass'] : $defaultConfig->WFdbpass;

            $result[$key] = [
                'description' => $description,
                'dbhost'      => $dbhost,
                'dbport'      => $dbport,
                'dbname'      => $dbname,
                'dbuser'      => $dbuser,
                'dbpass'      => $dbpass,
                'cronjob'     => $cronjobsActive,
            ];
        }

        return $result;
    }

    /**
     * Stellt sicher dass die Default-Config im MultiDbArray vorkommt
     *
     * @param Config $defaultConfig
     * @param array  $multiDbArray
     *
     * @return array
     */
    private static function includeDefaultConfig(Config $defaultConfig, $multiDbArray)
    {
        // Prüfen ob Default-Config in MultiDb-Array vorhanden ist
        foreach ($multiDbArray as $key => $item) {
            if ($item['dbhost'] === $defaultConfig->WFdbhost &&
                $item['dbport'] === $defaultConfig->WFdbport &&
                $item['dbname'] === $defaultConfig->WFdbname) {
                return $multiDbArray; // Default-Config ist bereits enthalten
            }
        }

        // Default-Config in MultiDb-Array anhängen
        $defaultDbName = $defaultConfig->WFdbname;
        $defaultConfigKey = !isset($multiDbArray[$defaultDbName]) ? $defaultDbName : '__default__';
        $multiDbArray[$defaultConfigKey] = [
            'description' => $defaultConfig->WFdbname,
            'dbhost'      => $defaultConfig->WFdbhost,
            'dbport'      => $defaultConfig->WFdbport,
            'dbname'      => $defaultConfig->WFdbname,
            'dbuser'      => $defaultConfig->WFdbuser,
            'dbpass'      => $defaultConfig->WFdbpass,
            'cronjob'     => true,
        ];

        return $multiDbArray;
    }

    /**
     * Ersetzt numerische Array-Schlüssel durch den Datenbanknamen
     *
     * @param array $multiDbArray
     *
     * @return array
     */
    private static function prepareArrayKeys($multiDbArray)
    {
        $result = [];

        foreach ($multiDbArray as $key => $item) {
            if (is_numeric($key)) {
                $dbname = $item['dbname'];
                if (!isset($multiDbArray[$dbname])) {
                    $key = $dbname;
                }
            }
            $result[$key] = $item;
        }

        return $result;
    }
}
