<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Type;

use Xentral\Components\SchemaCreator\Exception\SchemaCreatorInvalidArgumentException;
use Xentral\Components\SchemaCreator\Interfaces\CharTypeInterface;
use Xentral\Components\SchemaCreator\Interfaces\ColumnInterface;

final class Binary implements ColumnInterface, CharTypeInterface
{

    /** @var string $type */
    private $type = 'BINARY';

    /** @var int $length */
    private $length;

    /** @var array $options */
    private $options;

    /** @var string $name */
    private $name;

    /** @var bool $nullable */
    private $nullable;

    /** @var int  */
    private const DEFAULT_LENGTH = 32;

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
        $this->length = $length;
        $this->options = $options;
        $this->name = $name;
        $this->nullable = $nullable;
        $this->options['default'] = $default;
    }

    /**
     * @inheritDoc
     */
    public function getLength(): int
    {
        return $this->length;
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
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public function getField(): string
    {
        return $this->name;
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
    public function isNullable(): bool
    {
        return $this->nullable;
    }
}
