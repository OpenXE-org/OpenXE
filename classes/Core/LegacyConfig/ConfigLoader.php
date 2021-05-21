<?php

namespace Xentral\Core\LegacyConfig;

use Config;
use Xentral\Core\LegacyConfig\Exception\MultiDbConfigNotFoundException;

final class ConfigLoader
{
    /**
     * @return Config
     */
    public static function load()
    {
        $defaultConfig = self::loadDefaultConfig();

        // Ist MultiDb-Key gesetzt?
        $dbSelect = self::determineMultiDbConfigKey();
        if ($dbSelect === null) {
            return $defaultConfig;
        }

        // Ist MultiDb-Config vorhanden?
        $multiDbArray = self::loadMultiDbArray();
        if (empty($multiDbArray)) {
            return $defaultConfig;
        }

        // MultiDb-Array aufbereiten
        $multiDbArray = MultiDbArrayHydrator::hydrate($defaultConfig, $multiDbArray);

        // Zuerst MultiDB-Keys durchsuchen (wenn assoziatives Array)
        foreach ($multiDbArray as $multiDbKey => $multiDbItem) {
            if ($dbSelect === $multiDbKey) {
                return self::buildConfigFromMultiDbArray($defaultConfig, $multiDbItem);
            }
        }

        // Fallback: MultiDB-Konfigurationen nach Feld 'dbname' durchsuchen
        foreach ($multiDbArray as $multiDbKey => $multiDbItem) {
            if ($dbSelect === $multiDbItem['dbname']) {
                return self::buildConfigFromMultiDbArray($defaultConfig, $multiDbItem);
            }
        }

        throw new MultiDbConfigNotFoundException(sprintf(
            'MultiDb-Config "%s" not found.', $dbSelect
        ));
    }

    /**
     * @return Config[]|array
     */
    public static function loadAll()
    {
        $defaultConfig = self::loadDefaultConfig();
        $multiDbArray = self::loadMultiDbArray();

        // MultiDb-Array aufbereiten
        $multiDbArray = MultiDbArrayHydrator::hydrate($defaultConfig, $multiDbArray);

        $result = [];
        foreach ($multiDbArray as $multiDbKey => $multiDbItem) {
            $result[$multiDbKey] = self::buildConfigFromMultiDbArray($defaultConfig, $multiDbItem);
        }

        return $result;
    }

    /**
     * @return Config[]|array
     */
    public static function loadAllWithActiveCronjobs()
    {
        $defaultConfig = self::loadDefaultConfig();
        $multiDbArray = self::loadMultiDbArray();

        // MultiDb-Array aufbereiten
        $multiDbArray = MultiDbArrayHydrator::hydrate($defaultConfig, $multiDbArray);

        $result = [];
        foreach ($multiDbArray as $multiDbKey => $multiDbItem) {
            if ($multiDbItem['cronjob'] === true) {
                $result[$multiDbKey] = self::buildConfigFromMultiDbArray($defaultConfig, $multiDbItem);
            }
        }

        return $result;
    }

    /**
     * @return string[]|array
     */
    public static function loadAllDescriptions()
    {
        $defaultConfig = self::loadDefaultConfig();
        $multiDbArray = self::loadMultiDbArray();

        // MultiDb-Array aufbereiten
        $multiDbArray = MultiDbArrayHydrator::hydrate($defaultConfig, $multiDbArray);

        $result = [];
        foreach ($multiDbArray as $multiDbKey => $multiDbItem) {
            $result[$multiDbKey] = $multiDbItem['description'];
        }

        return $result;
    }
    
    /**
     * @param Config $defaultConfig
     * @param array  $multiDbItem
     *
     * @return Config
     */
    private static function buildConfigFromMultiDbArray($defaultConfig, $multiDbItem)
    {
        $config = clone $defaultConfig;

        $config->WFdbhost = $multiDbItem['dbhost'];
        $config->WFdbport = $multiDbItem['dbport'];
        $config->WFdbname = $multiDbItem['dbname'];
        $config->WFdbuser = $multiDbItem['dbuser'];
        $config->WFdbpass = $multiDbItem['dbpass'];

        return $config;
    }

    /**
     * @return Config
     */
    private static function loadDefaultConfig()
    {
        if (!class_exists('\\Config', true)) {
            $configClassFilePath = dirname(dirname(dirname(__DIR__))) . '/conf/main.conf.php';
            if (is_file($configClassFilePath)) {
                require_once $configClassFilePath;
            }
        }

        return new Config();
    }

    /**
     * @return array
     */
    private static function loadMultiDbArray()
    {
        $multiDbArray = [];

        $multiDbFilePath = dirname(dirname(dirname(__DIR__))) . '/conf/multidb.conf.php';
        if (is_file($multiDbFilePath)) {
            $multiDbArray = include $multiDbFilePath;
            if (!is_array($multiDbArray)) {
                $multiDbArray = [];
            }
        }

        return $multiDbArray;
    }

    /**
     * @uses $_POST
     * @uses $_COOKIE
     *
     * @return string|null
     */
    private static function determineMultiDbConfigKey()
    {
        /**
         * Wenn MULTIDB-Konstante gesetzt, dann kommt der Request aus der Rest-API.
         *
         * @see www/api/bootstrap.php
         */
        if (defined('MULTIDB')) {
            return constant('MULTIDB');
        }

        /**
         * Wenn POST['db'] und POST['dbselect'] gesetzt, dann kommt der Request übers vom Login-Formular (Frontend).
         *
         * @see \Acl::Login()
         */
        if (isset($_POST['db'], $_POST['dbselect']) && $_POST['dbselect'] === 'true') {
            return $_POST['db'];
        }

        /**
         * Wenn COOKIE['DBSELECTED'] gesetzt, dann kommt der Request übers Frontend und
         * der Login war in einem frührem Request erfolgreich.
         *
         * @see \Acl::Login()
         */
        if (isset($_COOKIE['DBSELECTED']) && !empty($_COOKIE['DBSELECTED'])) {
            return $_COOKIE['DBSELECTED'];
        }

        return null;
    }
}
