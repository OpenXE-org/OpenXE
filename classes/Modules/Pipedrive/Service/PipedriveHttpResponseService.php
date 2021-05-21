<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Service;

use Psr\Http\Message\StreamInterface;
use Xentral\Components\HttpClient\Response\ServerResponseInterface;

final class PipedriveHttpResponseService implements PipedriveServerResponseInterface
{
    /** @var ServerResponseInterface $response */
    private $response;

    /** @var null|array $json */
    private $json;

    /**
     * @param ServerResponseInterface $response
     */
    public function __construct(ServerResponseInterface $response)
    {
        $this->response = $response;

        $this->json = json_decode((string)$this->response->getBody(), true);
    }

    /**
     * Returns the json response body
     *
     * @return null|array
     */
    public function getJson(): ?array
    {
        return $this->json;
    }

    /**
     * @return StreamInterface
     */
    public function getBody(): StreamInterface
    {
        return $this->response->getBody();
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        $success = false;
        if (null !== $this->json && is_array($this->json)) {
            $success = array_key_exists('success', $this->json) && $this->json['success'] === true;
        }

        return $success;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [];
        if (null !== $this->json && is_array($this->json) && array_key_exists('data', $this->json)) {
            $data = $this->json['data'] ?? [];
        }

        return $data;
    }

    /**
     * Returns the error message
     *
     * @return string
     */
    public function getError(): string
    {
        $error = '';
        if (null !== $this->json && is_array($this->json) && array_key_exists('error', $this->json) &&
            !in_array($this->getStatusCode(), [200, 201])) {
            $error = $this->json['error'] ?? 'Unknown Error';
        }

        return $error;
    }

    /**
     * @return array
     */
    public function getAdditionalData(): array
    {
        $additionalData = [];
        if (null !== $this->json && is_array($this->json) && array_key_exists('additional_data', $this->json)) {
            $additionalData = $this->json['additional_data'];
        }

        return $additionalData;
    }

    /**
     * @return array|null
     */
    public function getPagination(): ?array
    {
        $pagination = null;
        if (($additional_data = $this->getAdditionalData()) && array_key_exists('pagination', $additional_data)) {
            $pagination = $additional_data['pagination'];
        }

        return $pagination;
    }
}
