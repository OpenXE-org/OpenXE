<?php

namespace Xentral\Modules\SuperSearch\Factory;

use Xentral\Components\Database\Database;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\SuperSearch\Exception\InvalidArgumentException;
use Xentral\Modules\SuperSearch\Exception\InvalidReturnTypeException;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\SearchIndexProviderInterface;

final class ProviderFactory
{
    /** @var Database $db */
    private $db;

    /** @var array $callbacks */
    private $callbacks = [];

    /**
     * @param Database           $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return array|SearchIndexProviderInterface[]
     */
    public function createActiveProviders(ContainerInterface $container)
    {
        // Grundsätzlich sind alle registrierten Provider aktiv
        $indexNames = array_keys($this->callbacks);

        // Inaktiv markierte Provider aus Liste entfernen
        $sql = 'SELECT sig.name FROM `supersearch_index_group` AS `sig` WHERE sig.active = 0';
        $inactiveIndexes = $this->db->fetchCol($sql);
        foreach ($inactiveIndexes as $inactiveIndex) {
            $key = array_search($inactiveIndex, $indexNames, true);
            if ($key !== false) {
                unset($indexNames[$key]);
            }
        }

        // Provider-Instanzen erzeugen
        $providers = [];
        foreach ($indexNames as $indexName) {
            $providers[] = $this->createProvider($indexName, $container);
        }

        return $providers;
    }

    /**
     * @param string   $indexName
     * @param callable $factoryMethod
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function registerProviderFactory($indexName, callable $factoryMethod)
    {
        if (!is_callable($factoryMethod, true)) {
            throw new InvalidArgumentException(sprintf(
                'Factory method for class "%s" is not callable.', $indexName
            ));
        }

        $this->callbacks[$indexName] = $factoryMethod;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    private function hasProviderFactory($className)
    {
        return isset($this->callbacks[$className]);
    }

    /**
     * @param string             $className
     * @param ContainerInterface $container
     *
     * @throws InvalidArgumentException
     * @throws InvalidReturnTypeException
     *
     * @return SearchIndexProviderInterface
     */
    private function createProvider($className, ContainerInterface $container)
    {
        if (!$this->hasProviderFactory($className)) {
            throw new InvalidArgumentException(sprintf(
                'Provider class "%s" does not exists.', $className
            ));
        }
        $callback = $this->callbacks[$className];

        $provider = $callback($container);
        if (!$provider instanceof SearchIndexProviderInterface) {
            throw new InvalidReturnTypeException(sprintf(
                'Factory method for class "%s" returned invalid type. Provider must implement "%s".',
                $className,
                SearchIndexProviderInterface::class
            ));
        }

        return $provider;
    }
}
