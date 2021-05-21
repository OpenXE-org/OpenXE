<?php

namespace Xentral\Modules\Api\Resource\Exception;

class EndpointNotAvailableException extends \RuntimeException
{
    protected $message = 'API-Endpoint is not available';
}
