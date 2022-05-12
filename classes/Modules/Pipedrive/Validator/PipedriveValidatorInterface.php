<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Validator;

use Xentral\Modules\Pipedrive\Exception\PipedriveValidatorException;

interface PipedriveValidatorInterface
{
    /**
     * Checks whether the given data is valid or not
     *
     * @param array $data
     *
     * @throws PipedriveValidatorException
     *
     * @return bool
     */
    public function isValid(array $data = []): bool;

    /**
     * Defines the default validator data object
     *
     * @return array
     */
    public function validatorRuleDefault(): array;
}
