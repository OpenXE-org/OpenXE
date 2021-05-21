<?php

declare(strict_types=1);

namespace Xentral\Components\HttpClient\Exception;

use Xentral\Components\HttpClient\Request\ClientRequestInterface;
use Xentral\Components\HttpClient\Response\ServerResponseInterface;

interface TransferErrorExceptionInterface extends HttpClientExceptionInterface
{
    /**
     * @return bool
     */
    public function hasResponse(): bool;

    /**
     * @return ServerResponseInterface|null
     */
    public function getResponse(): ?ServerResponseInterface;

    /**
     * @return ClientRequestInterface
     */
    public function getRequest(): ClientRequestInterface;
}
