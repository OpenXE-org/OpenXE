<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Interfaces;


interface CharTypeInterface
{
    /**
     * @return int
     */
    public function getLength(): int;
}
