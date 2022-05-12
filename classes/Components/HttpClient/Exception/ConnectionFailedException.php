<?php

declare(strict_types=1);

namespace Xentral\Components\HttpClient\Exception;

use Xentral\Components\HttpClient\Response\ServerResponseInterface;

/**
 * HTTP connect failed, e.g. timeout
 *
 * Response is not available
 */
class ConnectionFailedException extends TransferErrorException
{
    /**
     * @inheritDoc
     */
    public function hasResponse(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getResponse(): ?ServerResponseInterface
    {
        return null;
    }
}
