<?php

declare(strict_types=1);

namespace Xentral\Modules\LexwareOffice\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Xentral\Modules\LexwareOffice\Exception\LexwareOfficeException;

final class LexwareOfficeApiClient
{
    private const BASE_URI = 'https://api.lexware.io/v1/';
    private const MAX_ERROR_DETAILS = 5;

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
            $statusCode = (int)$exception->getCode();
            $errorText = '';
            if ($exception instanceof RequestException && $exception->hasResponse()) {
                $statusCode = $exception->getResponse()->getStatusCode();
                $body = (string)$exception->getResponse()->getBody();
                $decoded = json_decode($body, true);
                if (is_array($decoded)) {
                    $errorText = $decoded['message'] ?? $decoded['error_description'] ?? $decoded['error'] ?? '';
                    $detailText = $this->formatErrorDetails($decoded);
                    if ($detailText !== '') {
                        $errorText = $errorText !== '' ? ($errorText.' | '.$detailText) : $detailText;
                    }
                }
                if ($errorText === '' && $body !== '') {
                    $errorText = substr($body, 0, 400);
                }
            }
            if ($errorText === '') {
                $errorText = $exception->getMessage();
            }
            throw new LexwareOfficeException(
                sprintf(
                    'Lexware Office API Fehler%s: %s',
                    $statusCode > 0 ? sprintf(' (HTTP %d)', $statusCode) : '',
                    $errorText
                ),
                $statusCode,
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

    private function formatErrorDetails(array $decoded): string
    {
        $parts = [];
        $details = [];
        if (isset($decoded['details']) && is_array($decoded['details'])) {
            $details = $decoded['details'];
        } elseif (isset($decoded['errors']) && is_array($decoded['errors'])) {
            $details = $decoded['errors'];
        }

        $count = 0;
        foreach ($details as $detail) {
            if ($count >= self::MAX_ERROR_DETAILS) {
                $parts[] = sprintf('... (%d weitere)', count($details) - self::MAX_ERROR_DETAILS);
                break;
            }
            if (is_string($detail)) {
                $parts[] = $detail;
            } elseif (is_array($detail)) {
                $msg = $detail['message'] ?? $detail['detail'] ?? '';
                $field = $detail['field'] ?? $detail['path'] ?? '';
                if ($field !== '' && $msg !== '') {
                    $parts[] = sprintf('%s: %s', $field, $msg);
                } elseif ($msg !== '') {
                    $parts[] = $msg;
                } elseif ($field !== '') {
                    $parts[] = $field;
                }
            }
            $count++;
        }

        return implode('; ', array_filter($parts, static fn($p) => $p !== ''));
    }
}
