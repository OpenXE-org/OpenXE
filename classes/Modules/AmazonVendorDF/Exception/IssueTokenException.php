<?php

namespace Xentral\Modules\AmazonVendorDF\Exception;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class IssueTokenException extends RuntimeException
{
    public static function fromResponse(?ResponseInterface $response = null): self
    {
        return new self($response ? 'exception with response info' : 'exception without response info');
    }
}
