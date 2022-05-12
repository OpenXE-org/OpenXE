<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Service;

use Psr\Http\Message\StreamInterface;

interface PipedriveServerResponseInterface
{

    /**
     * Returns the json response body
     *
     * @return array|mixed
     */
    public function getJson();

    /**
     * Gets the response body.
     *
     * @return StreamInterface
     */
    public function getBody(): StreamInterface;

    /**
     * Gets the response status code.
     *
     * @return int Status code.
     */
    public function getStatusCode(): int;

    /**
     * Returns the error message
     *
     * @return string
     */
    public function getError(): string;

    /**
     * Checks whether the call was successful or not
     *
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * Retrieves Data from the response
     *
     * @return array
     */
    public function getData(): array;

    /**
     * Retrieves Additional Data from the response
     *
     * @return array
     */
    public function getAdditionalData(): array;
}
