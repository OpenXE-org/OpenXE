<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Type;

use Xentral\Components\SchemaCreator\Exception\SchemaCreatorInvalidArgumentException;
use Xentral\Components\SchemaCreator\Interfaces\ColumnInterface;
use Xentral\Components\SchemaCreator\Interfaces\EnumAndSetInterface;

final class Enum implements ColumnInterface, EnumAndSetInterface
{
    /** @var string $type */
    private $type = 'ENUM';

    /** @var string $name */
    private $name;

    /** @var array $options */
    private $options;

    /** @var array $references */
    private $references;

    /** @var bool $nullable */
    private $nullable;

    /**
     * @param string      $name
     * @param array       $references
     * @param string|null $default
     * @param bool        $nullable
     * @param array       $options
     */
    public function __construct(
        string $name,
        array $references,
        ?string $default = null,
        bool $nullable = true,
        array $options = []
    ) {
        $this->references = $references;
        $this->name = $name;
        $this->nullable = $nullable;
        $options['default'] = $default;
        $this->options = $options;
        if (null !== $default && !in_array($default, $references, true)) {
            throw new SchemaCreatorInvalidArgumentException(
                sprintf('Default value %s not found in References', $default)
            );
        }
    }

    /**
     * @param array $options
     *
     * @return ColumnInterface
     *
     * @internal Use constructor instead
     */
    public static function fromDBColumn(array $options): ColumnInterface
    {
        if (empty($options)) {
            throw new SchemaCreatorInvalidArgumentException('Options cannot be empty');
        }

        return new self(
            $options['field'],
            $options['references'],
            $options['default'],
            $options['nullable'],
            $options
        );
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function getField(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public function getReferences(): string
    {
        $formattedReferences = array_map(
            static function ($value) {
                return sprintf("'%s'", $value);
            },
            $this->references
        );

        return implode(',', $formattedReferences);
    }

    /**
     * @inheritDoc
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }
}
