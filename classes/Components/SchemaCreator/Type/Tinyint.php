<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Type;

use Xentral\Components\SchemaCreator\Exception\SchemaCreatorInvalidArgumentException;
use Xentral\Components\SchemaCreator\Interfaces\IntegerTypeInterface;
use Xentral\Components\SchemaCreator\Interfaces\NumericTypeInterface;
use Xentral\Components\SchemaCreator\Interfaces\ColumnInterface;

final class Tinyint implements ColumnInterface, NumericTypeInterface, IntegerTypeInterface
{
    /** @var string $type */
    private $type = 'TINYINT';

    /** @var int $length */
    private $length;

    /** @var string $name */
    private $name;

    /** @var array $options */
    private $options;

    /** @var bool $isUnsigned */
    private $isUnsigned;

    /** @var bool $nullable */
    private $nullable;

    /** @var int */
    private const DEFAULT_LENGTH = 4;

    /**
     * @param string   $name
     * @param int      $length
     * @param bool     $unsigned
     * @param int|null $default
     * @param bool     $nullable
     * @param array    $options
     */
    public function __construct(
        string $name,
        int $length = self::DEFAULT_LENGTH,
        bool $unsigned = false,
        ?int $default = null,
        bool $nullable = true,
        array $options = []
    ) {
        $this->name = $name;
        $this->length = $length;
        $this->isUnsigned = $unsigned;
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
     * @inheritDoc
     */
    public function isUnsigned(): bool
    {
        return $this->isUnsigned;
    }

    /**
     * @param string $name
     * @param int    $length
     * @param bool   $unsigned
     * @param array  $options
     *
     * @return IntegerTypeInterface
     */
    public static function asAutoIncrement(
        string $name,
        int $length = self::DEFAULT_LENGTH,
        bool $unsigned = true,
        array $options = []
    ): IntegerTypeInterface {
        $options['extra'] = 'ai';

        return new self($name, $length, $unsigned, null, false, $options);
    }

    /**
     * @param string   $name
     * @param int      $length
     * @param int|null $default
     * @param bool     $nullable
     * @param array    $options
     *
     * @return IntegerTypeInterface
     */
    public static function asUnsigned(
        string $name,
        int $length = self::DEFAULT_LENGTH,
        ?int $default = null,
        bool $nullable = true,
        array $options = []
    ): IntegerTypeInterface {
        return new self($name, $length, true, $default, $nullable, $options);
    }

    /**
     * @param array $options
     *
     * @return IntegerTypeInterface
     *
     * @internal Use constructor instead
     */
    public static function fromDBColumn(array $options): IntegerTypeInterface
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
            $options['unsigned'],
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
