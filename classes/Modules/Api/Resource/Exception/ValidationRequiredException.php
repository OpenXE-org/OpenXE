<?php

namespace Xentral\Modules\Api\Resource\Exception;

class ValidationRequiredException extends \RuntimeException
{
    protected $message = 'Validation is required for inserting and updating resources.';
}
