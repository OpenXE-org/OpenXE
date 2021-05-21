<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Type;

use Xentral\Components\SchemaCreator\Exception\SchemaCreatorInvalidArgumentException;
use Xentral\Components\SchemaCreator\Interfaces\ColumnInterface;
use Xentral\Components\SchemaCreator\Interfaces\ColumnTextInterface;

final class Tinyblob implements ColumnInterface, ColumnTextInterface
{

    /** @var string $type */
    private $type = 'TINYBLOB';

    /** @var string $name */
    private $name;

    /** @var array $options */
    private $options;

    /** @var bool $nullable */
    private $nullable;

    /**
     * @param string $name
     * @param bool   $nullable
     * @param array  $options
     */
    public function __construct(string $name, bool $nullable = true, array $options = [])
    {
        $this->name = $name;
        $this->nullable = $nullable;
        $options['default'] = null;
        $this->options = $options;
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
    public function isNullable(): bool
    {
        return $this->nullable;
    }
}
