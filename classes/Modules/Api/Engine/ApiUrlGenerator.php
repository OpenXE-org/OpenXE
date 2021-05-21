<?php

declare(strict_types=1);

namespace Xentral\Modules\Api\Engine;

use Xentral\Components\Http\Request;
use Xentral\Components\Util\StringUtil;
use Xentral\Modules\Api\Exception\InvalidArgumentException;

final class ApiUrlGenerator
{
    /** @var Request $request */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $endpointUrl Beispiel: /v1/adressen
     * @param array  $queryParams Query-Parameter (GET-Parameter)
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public function generate(string $endpointUrl, array $queryParams = []): string
    {
        if (empty($endpointUrl)) {
            throw new InvalidArgumentException('Endpoint URL can not be empty.');
        }
        if (!StringUtil::startsWith($endpointUrl, '/')) {
            throw new InvalidArgumentException('Endpoint URL must start with a slash character.');
        }
        if (isset($queryParams['path'])) {
            throw new InvalidArgumentException('Parameter "path" is reserved.');
        }

        // 1. Normal:      http://locahost/xentral-20.3/www/api/v1/docscan?foo=bar
        // 2. Alternative: http://locahost/xentral-20.3/www/api/index.php/v1/docscan?foo=bar
        // 3. Failsafe:    http://locahost/xentral-20.3/www/api/index.php?path=/v1/docscan&foo=bar
        // => Base-URI in allen Fällen: http://locahost/xentral-20.3/www/api/
        $baseUrl = $this->request->getUrlForPath('/');
        $baseUrl = substr($baseUrl, 0, -1); // Remove last slash

        // Query-Parameter zusammenbauen
        $queryString = http_build_query($queryParams, '', '&');

        if ($this->isFailsafeMode()) {
            $fullUrl = $baseUrl . '/index.php?path=' . $endpointUrl;
            if (!empty($queryParams)) {
                $fullUrl .= '&' . $queryString;
            }

            return $fullUrl;
        }

        if ($this->isAlternateMode()) {
            $fullUrl = $baseUrl . '/index.php' . $endpointUrl;
        } else {
            $fullUrl = $baseUrl . $endpointUrl;
        }

        if (!empty($queryParams)) {
            $fullUrl .= '?' . $queryString;
        }

        return $fullUrl;
    }

    /**
     * Failsafe URL: /www/api/index.php?path=/v1/adressen&foo=bar
     *
     * @return bool
     */
    private function isFailsafeMode(): bool
    {
        $pathInfo = $this->request->getPathInfo();
        if (!empty($pathInfo)) {
            return false;
        }

        $queryString = $this->request->getServer('QUERY_STRING');
        parse_str($queryString, $queryParts);

        return isset($queryParts['path']);
    }

    /**
     * Alternative URL-Variante: /www/api/index.php/v1/adressen?foo=bar
     *
     * @return bool
     */
    private function isAlternateMode(): bool
    {
        $pathInfo = $this->request->getPathInfo();
        if (empty($pathInfo)) {
            return false;
        }

        $requestUri = $this->request->getRequestUri();
        $apiRootPos = strpos($requestUri, 'api/index.php');

        return is_int($apiRootPos) && $apiRootPos > 0;
    }
}
