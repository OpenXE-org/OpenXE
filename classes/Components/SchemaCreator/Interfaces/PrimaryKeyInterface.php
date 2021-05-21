<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Interfaces;

interface PrimaryKeyInterface extends UniqueIndexInterface
{
    public const INDEX_NAME = 'PRIMARY';
}
