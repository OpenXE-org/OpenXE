<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Interfaces;

interface ColumnInterface
{

    /**
     * @var null|string[]
     */
    public const DEFAULT_PARAMS = [
        'default'  => null,
        'charset'  => null,
        'collate'  => null,
        'comment'  => null,
        'nullable' => true,
        'extra'    => null,
    ];

    /**
     * @return string
     */
    public function getField(): string;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return array
     */
    public function getOptions(): array;

    /**
     * @return bool
     */
    public function isNullable() : bool;
}
