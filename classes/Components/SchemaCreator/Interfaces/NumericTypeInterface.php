<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Interfaces;

interface NumericTypeInterface
{
    public const NON_NEGATIV = 'UNSIGNED';
    public const WITH_NEGATIV = 'SIGNED';

    /**
     * @return bool
     */
    public function isUnsigned(): bool;

}
