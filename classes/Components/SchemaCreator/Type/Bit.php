<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Type;

use Xentral\Components\SchemaCreator\Exception\SchemaCreatorInvalidArgumentException;
use Xentral\Components\SchemaCreator\Interfaces\IntegerTypeInterface;
use Xentral\Components\SchemaCreator\Interfaces\ColumnInterface;

final class Bit implements ColumnInterface, IntegerTypeInterface
{
    /** @var string $type */
    private $type = 'BIT';

    /** @var int $length */
    private $length;

    /** @var string $name */
    private $name;

    /** @var array $options */
    private $options;

    /** @var bool $nullable */
    private $nullable;

    /** @var int */
    private const DEFAULT_LENGTH = 6;

    /**
     * @param string   $name
     * @param int      $length
     * @param int|null $default
     * @param bool     $nullable
     * @param array    $options
     */
    public function __construct(
        string $name,
        int $length = self::DEFAULT_LENGTH,
        ?int $default = null,
        bool $nullable = true,
        array $options = []
    ) {
        $this->name = $name;
        $this->length = $length;
        $this->nullable = $nullable;
        $options['default'] = $default;
        $this->options = $options;
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
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param array $options
     *
     * @return Bit
     *
     * @internal Use constructor instead
     *
     */
    public static function fromDBColumn(array $options): Bit
    {
        if (empty($options)) {
            throw new SchemaCreatorInvalidArgumentException('Options cannot be empty');
        }

        if ($options['length'] === null) {
            $options['length'] = self::DEFAULT_LENGTH;
        }

        return new self(
            $options['field'],
            $options['length'],
            $options['default'],
            $options['nullable'],
            $options
        );
    }

    /**
     * @inheritDoc
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }
}
