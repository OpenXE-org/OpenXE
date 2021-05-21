<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Type;

use Xentral\Components\SchemaCreator\Exception\SchemaCreatorInvalidArgumentException;
use Xentral\Components\SchemaCreator\Interfaces\DecimalTypeInterface;
use Xentral\Components\SchemaCreator\Interfaces\IntegerTypeInterface;
use Xentral\Components\SchemaCreator\Interfaces\NumericTypeInterface;
use Xentral\Components\SchemaCreator\Interfaces\ColumnInterface;

final class Decimal
    implements ColumnInterface, NumericTypeInterface, IntegerTypeInterface, DecimalTypeInterface
{
    /** @var string $type */
    private $type = 'DECIMAL';

    /** @var int $length */
    private $length;

    /** @var string $name */
    private $name;

    /** @var array $options */
    private $options;

    /** @var bool $isUnsigned */
    private $isUnsigned;

    /** @var int $decimals */
    private $decimals;

    /** @var bool $nullable */
    private $nullable;

    /** @var int */
    private const DEFAULT_LENGTH = 10;

    /**
     * Decimal constructor.
     *
     * @param string   $name
     * @param int      $length
     * @param int      $decimals
     * @param bool     $unsigned
     * @param int|null $default
     * @param bool     $nullable
     * @param array    $options
     */
    public function __construct(
        string $name,
        int $length = self::DEFAULT_LENGTH,
        int $decimals = 0,
        bool $unsigned = false,
        ?int $default = null,
        bool $nullable = true,
        array $options = []
    ) {
        $this->name = $name;
        $this->length = $length;
        $this->decimals = $decimals;
        $this->nullable = $nullable;
        $options['default'] = $default;
        $this->options = $options;
        $this->isUnsigned = $unsigned;
    }

    /**
     * @param array $options
     *
     * @return DecimalTypeInterface
     *
     * @internal Use constructor instead
     */
    public static function fromDBColumn(array $options): DecimalTypeInterface
    {
        if (empty($options)) {
            throw new SchemaCreatorInvalidArgumentException('Options cannot be empty');
        }

        if ($options['length'] === null) {
            $options['length'] = self::DEFAULT_LENGTH;
        }

        if ($options['decimals'] === null) {
            $options['decimals'] = 0;
        }
        return new self(
            $options['field'],
            $options['length'],
            $options['decimals'],
            $options['unsigned'],
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
     * @param int    $decimals
     * @param bool   $unsigned
     * @param array  $options
     *
     * @return IntegerTypeInterface
     */
    public static function asAutoIncrement(
        string $name,
        int $length = self::DEFAULT_LENGTH,
        int $decimals = 0,
        bool $unsigned = true,
        array $options = []
    ): IntegerTypeInterface {
        $options['extra'] = 'ai';

        return new self($name, $length, $decimals, $unsigned, null, false, $options);
    }

    /**
     * @param string   $name
     * @param int      $length
     * @param int      $decimals
     * @param int|null $default
     * @param bool     $nullable
     * @param array    $options
     *
     * @return IntegerTypeInterface
     */
    public static function asUnsigned(
        string $name,
        int $length = self::DEFAULT_LENGTH,
        int $decimals = 0,
        ?int $default = null,
        bool $nullable = true,
        array $options = []
    ): IntegerTypeInterface {
        return new self($name, $length, $decimals, true, $default, $nullable, $options);
    }

    /**
     * @inheritDoc
     */
    public function getDecimals(): int
    {
        return $this->decimals;
    }

    /**
     * @inheritDoc
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }
}
