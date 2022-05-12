<?php

namespace Xentral\Components\Database;

use Xentral\Components\Database\Adapter\MysqliAdapter;
use Xentral\Components\Database\Exception\ConfigException;
use Xentral\Components\Database\Profiler\Profiler;
use Xentral\Components\Database\SqlQuery\QueryFactory;
use Xentral\Components\Logger\Context\ContextHelper;
use Xentral\Components\Logger\MemoryLogger;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Core\DependencyInjection\ServiceContainer;
use Xentral\Core\LegacyConfig\ConfigLoader;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'Database'         => 'onInitDatabase',
            'DatabaseProfiler' => 'onGetDatabaseProfiler',
            'MysqliAdapter'    => 'onInitMysqliAdapter',
            'QueryFactory'     => 'onInitQueryFactory',
        ];
    }

    /**
     * @param ServiceContainer $container
     *
     * @return Database
     */
    public static function onInitDatabase(ServiceContainer $container)
    {
        return new Database($container->get('MysqliAdapter'), $container->get('QueryFactory'));
    }

    /**
     * @return QueryFactory
     */
    public static function onInitQueryFactory()
    {
        return new QueryFactory('mysql');
    }

    /**
     * @param ServiceContainer $container
     *
     * @return Profiler
     */
    public static function onGetDatabaseProfiler(ServiceContainer $container)
    {
        $request = $container->get('Request');
        return new Profiler(new MemoryLogger(new ContextHelper($request)));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return MysqliAdapter
     */
    public static function onInitMysqliAdapter(ContainerInterface $container)
    {
        $conf = ConfigLoader::load();

        $dbHost = property_exists($conf, 'WFdbhost') ? $conf->WFdbhost : 'localhost';
        $dbPort = property_exists($conf, 'WFdbport') ? $conf->WFdbport : 3306;
        $dbName = property_exists($conf, 'WFdbname') ? $conf->WFdbname : null;
        $dbUser = property_exists($conf, 'WFdbuser') ? $conf->WFdbuser : null;
        $dbPass = property_exists($conf, 'WFdbpass') ? $conf->WFdbpass : null;

        if (empty($dbName)) {
            throw new ConfigException('Could not connect to database. Database name is missing or empty.');
        }
        if (empty($dbUser)) {
            throw new ConfigException('Could not connect to database. Database user is missing or empty.');
        }
        if (empty($dbPass)) {
            throw new ConfigException('Could not connect to database. Database password is missing or empty.');
        }

        $startupQueries = [
            "SET NAMES 'utf8', " .
            "CHARACTER SET 'utf8', " .
            "lc_time_names = 'de_DE', " .
            "SESSION sql_mode = '', " .
            "SESSION sql_big_selects = 1;",
        ];
        $config = new DatabaseConfig($dbHost, $dbUser, $dbPass, $dbName, 'utf8', $dbPort, $startupQueries);

        // Profiler aktivieren
        // Kann mit $container->get('DatabaseProfiler')->getContexts() abgefragt werden
        $profiler = $container->get('DatabaseProfiler');
        if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
            $profiler->setActive(true);
        }

        return new MysqliAdapter($config, $profiler);
    }
}
