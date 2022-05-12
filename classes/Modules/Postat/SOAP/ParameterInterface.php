<?php

declare(strict_types=1);

namespace Xentral\Modules\Postat\SOAP;

/**
 * Validate the data given to the constructor.
 *
 * In case of validation errors, the class should throw a PostAtException.
 */
interface ParameterInterface
{
    public function __construct(array $data);

    public function getData(): array;
}
