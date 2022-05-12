<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Type;

use Xentral\Components\SchemaCreator\Exception\SchemaCreatorInvalidArgumentException;
use Xentral\Components\SchemaCreator\Interfaces\DateAndTimeColumnInterface;
use Xentral\Components\SchemaCreator\Interfaces\ColumnInterface;

final class Year implements ColumnInterface, DateAndTimeColumnInterface
{
    /** @var string $type */
    private $type = 'YEAR';

    /** @var array $options */
    private $options;

    /** @var string $name */
    private $name;

    /** @var int $length */
    private $length;

    /** @var bool $nullable */
    private $nullable;

    /** @var int */
    private const DEFAULT_LENGTH = 4;

    /**
     * @param string      $name
     * @param int         $length
     * @param string|null $default
     * @param bool        $nullable
     * @param array       $options
     */
    public function __construct(
        string $name,
        int $length = self::DEFAULT_LENGTH,
        ?string $default = null,
        bool $nullable = true,
        array $options = []
    ) {
        {
            $this->length = $length;
            $this->name = $name;
            $this->options = $options;
            $this->nullable = $nullable;
            $this->options['default'] = $default;
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

    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @inheritDoc
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }
}
