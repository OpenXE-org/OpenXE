<?php

declare(strict_types=1);

namespace Xentral\Modules\LexwareOffice\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Xentral\Modules\LexwareOffice\Exception\LexwareOfficeException;

final class LexwareOfficeApiClient
{
    private const BASE_URI = 'https://api.lexware.io/v1/';

    public function __construct(private ?Client $client = null)
    {
        $this->client ??= new Client([
            'base_uri' => self::BASE_URI,
            'timeout' => 20,
        ]);
    }

    /**
     * @param string $apiKey
     * @param array  $query
     *
     * @return array
     */
    public function searchContacts(string $apiKey, array $query): array
    {
        return $this->request('GET', 'contacts', $apiKey, ['query' => $query]);
    }

    /**
     * @param string $apiKey
     * @param array  $payload
     *
     * @return array
     */
    public function createContact(string $apiKey, array $payload): array
    {
        return $this->request('POST', 'contacts', $apiKey, ['json' => $payload]);
    }

    /**
     * @param string $apiKey
     * @param array  $payload
     * @param bool   $finalize
     *
     * @return array
     */
    public function createInvoice(string $apiKey, array $payload, bool $finalize = true): array
    {
        $query = $finalize ? ['finalize' => 'true'] : [];

        return $this->request('POST', 'invoices', $apiKey, ['json' => $payload, 'query' => $query]);
    }

    /**
     * @param string $method
     * @param string $path
     * @param string $apiKey
     * @param array  $options
     *
     * @return array
     */
    private function request(string $method, string $path, string $apiKey, array $options = []): array
    {
        $headers = [
            'Authorization' => sprintf('Bearer %s', $apiKey),
            'Accept' => 'application/json',
        ];

        $options['headers'] = array_merge($headers, $options['headers'] ?? []);

        try {
            $response = $this->client->request($method, ltrim($path, '/'), $options);
        } catch (GuzzleException $exception) {
            throw new LexwareOfficeException(
                sprintf('Lexware Office API Fehler: %s', $exception->getMessage()),
                (int)$exception->getCode(),
                $exception
            );
        }

        $content = (string)$response->getBody();
        $decoded = json_decode($content, true);

        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new LexwareOfficeException('Die Antwort von Lexware Office konnte nicht gelesen werden.');
        }

        return $decoded;
    }
}
