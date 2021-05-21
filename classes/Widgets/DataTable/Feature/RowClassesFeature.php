<?php

namespace Xentral\Widgets\DataTable\Feature;

use Closure;
use Xentral\Widgets\DataTable\DataTableInterface;

final class RowClassesFeature implements DataTableFeatureInterface
{
    /** @var array $colors */
    private static $availableColors = [
        'lightgray',
        'lightgreen',
        'lightteal',
        'lightcyan',
        'lightblue',
        'lightindigo',
        'lightviolet',
        'lightfuchsia',
        'lightpink',
        'lightred',
        'lightorange',
        'lightyellow',
        'lightlime',
    ];

    /** @var array|string[] $classes */
    private $classes;

    /** @var array|Closure[] $customFormatter */
    private $customFormatter = [];

    /**
     * @param array|string[] $classes
     * @param array|Closure  $customFormatter
     */
    public function __construct(array $classes = [], array $customFormatter = [])
    {
        foreach ($classes as $class) {
            $this->addClass($class);
        }
        foreach ($customFormatter as $formatter) {
            $this->addCustomFormatter($formatter);
        }
    }

    /**
     * @param string $className
     *
     * @return void
     */
    public function addClass($className)
    {
        $this->classes[] = trim($className);
    }

    /**
     * @return array|string[]
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @return string
     */
    public function getClassesString()
    {
        return implode(' ', $this->classes);
    }

    /**
     * @return bool
     */
    public function hasCustomFormatter()
    {
        return !empty($this->customFormatter);
    }

    /**
     * @return array|Closure[]
     */
    public function getCustomFormatter()
    {
        return $this->customFormatter;
    }

    /**
     * @param Closure $closure
     *
     * @return void
     */
    public function addCustomFormatter(Closure $closure)
    {
        $this->customFormatter[] = $closure;
    }

    /**
     * @param DataTableInterface $table
     *
     * @return void
     */
    public function modifyTable(DataTableInterface $table)
    {
        // @todo Logik steckt momentan in DataTableRenderer; muss aber hier rein
    }

    /**
     * @return string
     */
    public function getRandomColor()
    {
        $count = count(self::$availableColors);
        $index = mt_rand() % $count;

        return self::$availableColors[$index];
    }
}
