<?php

declare(strict_types=1);

namespace Xentral\Modules\Hubspot;

use \Psr\Http\Message\ResponseInterface;

final class HubspotHttpResponseService
{

    /** @var ResponseInterface $response */
    private $response;

    /**
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Returns the json response body
     *
     * @return array
     */
    public function getJson(): array
    {
        $content = (string)$this->response->getBody();
        $jsonResponse = json_decode($content, true);

        if ($jsonResponse === null || (json_last_error() !== JSON_ERROR_NONE)) {
            return [];
        }

        return $jsonResponse;
    }

    /**
     * @return int
     */
    public function getStatusCode() : int
    {
        return $this->response->getStatusCode();
    }

    /**
     * Returns the error message
     *
     * @return string
     */
    public function getError(): string
    {
        if (!in_array($this->getStatusCode(), [200, 201, 204])) {
            if (($resp = $this->getJson()) && array_key_exists('error', $resp)) {
                return $resp['error'];
            }

            return 'Unknown Error';
        }

        return '';
    }
}
