<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Interfaces;

interface EnumAndSetInterface
{
    /**
     * @return string
     */
    public function getReferences(): string;
}
