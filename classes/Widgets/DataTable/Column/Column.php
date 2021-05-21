<?php

namespace Xentral\Widgets\DataTable\Column;

use JsonSerializable;
use Xentral\Widgets\DataTable\Exception\InvalidArgumentException;
use Xentral\Widgets\DataTable\Feature\ResponsiveFeature;

final class Column implements JsonSerializable
{
    /** @var string ALIGN_LEFT */
    const ALIGN_LEFT = 'left';

    /** @var string ALIGN_RIGHT */
    const ALIGN_RIGHT = 'right';

    /** @var string ALIGN_CENTER */
    const ALIGN_CENTER = 'center';

    /** @var string ALIGN_JUSTIFY */
    const ALIGN_JUSTIFY = 'justify';

    /** @var array $validAlignments */
    public static $validAlignments = [
        self::ALIGN_LEFT,
        self::ALIGN_RIGHT,
        self::ALIGN_CENTER,
        self::ALIGN_JUSTIFY,
    ];

    /** @var string $name */
    private $name;

    /** @var string $title */
    private $title;

    /** @var bool $visible */
    private $visible;

    /** @var bool $sortable */
    private $sortable;

    /** @var bool $searchable */
    private $searchable;

    /** @var bool $exportable */
    private $exportable;

    /** @var bool $fixed */
    private $fixed;

    /** @var string $alignment */
    private $alignment;

    /** @var string|null $dbColumn */
    private $dbColumn;

    /** @var string|null $width */
    private $width;

    /** @var callable|null $formatter */
    private $formatter;

    /** @var array $properties */
    private $properties = [];

    /** @var array $cssClasses CSS classes */
    private $cssClasses = [];

    /**
     * @param string      $name
     * @param string      $title
     * @param string      $align   [left|right|center|justify]
     * @param string|null $width   Column width as CSS value (e.g 20%, 3em, 55px)
     * @param bool        $visible Is column currently visible? Visibility can be changed at runtime
     * @param bool        $sortable
     * @param bool        $searchable
     * @param bool        $exportable
     * @param bool        $fixed   If true, column is always visible and visibility can not be changed at runtime
     *                             * Fixed columns can't be hidden (ResponsiveFeature, ColumnVisibilityFeature)
     *                             * Fixed columns can't be reordered (ColumnReorderFeature)
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $name,
        $title,
        $align = 'left',
        $width = null,
        $visible = true,
        $sortable = false,
        $searchable = false,
        $exportable = false,
        $fixed = false
    ) {
        if (empty($name)) {
            throw new InvalidArgumentException('Column name can not be empty.');
        }
        $cleanedName = (string)preg_replace('#[^a-z0-9_]#', '', trim($name));
        if ($cleanedName !== $name) {
            throw new InvalidArgumentException(sprintf(
                'Name "%s" contains illegal characters. Valid characters are: a-z, 0-9 and underscore.',
                $name
            ));
        }

        $this->name = (string)$name;
        $this->title = (string)$title;
        $this->visible = (bool)$visible;
        $this->sortable = (bool)$sortable;
        $this->searchable = (bool)$searchable;
        $this->exportable = (bool)$exportable;
        $this->fixed = (bool)$fixed;
        $this->alignment = (string)$align;
        $this->width = $width !== null ? (string)$width : null;
    }

    /**
     * Currently hidden column; can be unhidden
     *
     * @param string      $name
     * @param string      $title
     * @param string      $align [left|right|center|justify]
     * @param string|null $width Column width as CSS value (e.g 20%, 3em, 55px)
     *
     * @return Column
     */
    public static function hidden($name, $title, $align = 'left', $width = null)
    {
        return new static($name, $title, $align, $width, false, false, false, false, false);
    }

    /**
     * Visible column; not sortable and not searchable
     *
     * @param string      $name
     * @param string      $title
     * @param string      $align [left|right|center|justify]
     * @param string|null $width Column width as CSS value (e.g 20%, 3em, 55px)
     *
     * @return Column
     */
    public static function visible($name, $title, $align = 'left', $width = null)
    {
        return new static($name, $title, $align, $width, true, false, false, true, false);
    }

    /**
     * Visible and sortable column; not searchable
     *
     * @param string      $name
     * @param string      $title
     * @param string      $align [left|right|center|justify]
     * @param string|null $width Column width as CSS value (e.g 20%, 3em, 55px)
     *
     * @return Column
     */
    public static function sortable($name, $title, $align = 'left', $width = null)
    {
        return new static($name, $title, $align, $width, true, true, false, true, false);
    }

    /**
     * Visible, sortable und searchable column
     *
     * @param string      $name
     * @param string      $title
     * @param string      $align [left|right|center|justify]
     * @param string|null $width Column width as CSS value (e.g 20%, 3em, 55px)
     *
     * @return Column
     */
    public static function searchable($name, $title, $align = 'left', $width = null)
    {
        return new static($name, $title, $align, $width, true, true, true, true, false);
    }

    /**
     * Always visible and with fixed position (for Menu and Selection columns)
     *
     * - Always visible; Can't be hidden (ColumnVisibilityFeature)
     * - Fixed position; Can't be reordered (ColumnReorderFeature)
     * - Not searchable
     * - Not sortable
     *
     * @param string $name
     * @param string $title
     *
     * @return Column
     */
    public static function fixed($name, $title = '', $align = 'center', $width = null)
    {
        $fixed = new static($name, $title, $align, $width, true, false, false, false, true);
        $fixed->set('responsivePriority', ResponsiveFeature::PRIO_HIGHER);

        return $fixed;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $dbColumn
     */
    public function setDbColumn($dbColumn)
    {
        $this->dbColumn = (string)$dbColumn;
    }

    /**
     * @return string|null
     */
    public function getDbColumn()
    {
        return $this->dbColumn;
    }

    /**
     * @return string
     */
    public function getAlignment()
    {
        return $this->alignment;
    }

    /**
     * @see $validAlignments
     *
     * @param string $alignment [left|right|center|justify]
     */
    public function setAlignment($alignment)
    {
        if (!in_array($alignment, self::$validAlignments, true)) {
            throw new InvalidArgumentException(sprintf(
                'Alignment "%s" is not valid. Valid alignments: %s',
                $alignment,
                implode(', ', self::$validAlignments)
            ));
        }

        $this->alignment = $alignment;
    }

    /**
     * @return callable|null
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * @param callable $formatter
     *
     * @return void
     */
    public function setFormatter(callable $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @return bool
     */
    public function isFixed()
    {
        return $this->fixed;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return $this->sortable;
    }

    /**
     * @return bool
     */
    public function isExportable()
    {
        return $this->exportable;
    }

    /**
     * @return bool
     */
    public function isSearchable()
    {
        return $this->searchable;
    }

    /**
     * @param string $property
     *
     * @return bool
     */
    public function has($property)
    {
        if (isset($this->{$property})) {
            return true;
        }

        return isset($this->properties[$property]);
    }

    /**
     * @param string $property
     *
     * @return mixed|null
     */
    public function get($property)
    {
        if (isset($this->{$property})) {
            return $this->{$property};
        }

        if (isset($this->properties[$property])) {
            return $this->properties[$property];
        }

        return null;
    }

    /**
     * @param string $property
     * @param mixed  $value
     *
     * @return void
     */
    public function set($property, $value)
    {
        if (isset($this->{$property})) {
            $this->{$property} = $value;
        }

        $this->properties[$property] = $value;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    public function hasCssClass($className)
    {
        return in_array($className, $this->cssClasses, true);
    }

    /**
     * @param string $className
     *
     * @return void
     */
    public function addCssClass($className)
    {
        $this->cssClasses[] = trim($className);
        $this->cssClasses = array_unique($this->cssClasses);
    }

    /**
     * @param string $className
     *
     * @return void
     */
    public function removeCssClass($className)
    {
        $classKey = array_search($className, $this->cssClasses, true);
        if ($classKey !== false) {
            unset($this->cssClasses[$classKey]);
            $this->cssClasses = array_values($this->cssClasses);
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = $this->properties;
        $result['data'] = isset($result['data']) ? $result['data'] : $this->name;
        $result['name'] = $this->name;
        $result['title'] = $this->title;
        $result['exportable'] = $this->exportable;
        $result['searchable'] = $this->searchable;
        $result['orderable'] = $this->sortable;
        $result['visible'] = $this->visible;
        $result['fixed'] = $this->fixed;
        if ($this->fixed === true) {
            $result['visible'] = true;
        }

        // Spalte hat keine Daten; z.B. Menü-Spalte
        if ($this->dbColumn === null) {
            //$result['data'] = null;
            $result['defaultContent'] = isset($result['defaultContent']) ? $result['defaultContent'] : '';
            $result['orderable'] = false;
            $result['searchable'] = false;
//            $result['data']           = $this->name;
//            $result['searchable']     = $this->searchable;
        }

        $cssClasses = $this->cssClasses;
        $cssClasses[] = 'dt-' . $this->alignment;
        $result['className'] = implode(' ', $cssClasses);

        if ($this->width !== null) {
            $result['width'] = $this->width;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
