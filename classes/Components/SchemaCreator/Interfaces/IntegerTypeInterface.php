<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Interfaces;

interface IntegerTypeInterface extends ColumnInterface
{
    /**
     * @return int
     */
    public function getLength(): int;
}
