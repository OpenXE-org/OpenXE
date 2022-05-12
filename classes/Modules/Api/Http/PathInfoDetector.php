<?php

declare(strict_types=1);

namespace Xentral\Modules\Api\Http;

use Xentral\Components\Http\Request;

final class PathInfoDetector
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
     * Gibt den berechneten PathInfo-Teil der URL zurück; ohne $_SERVER['PATH_INFO'] zu verwenden
     *
     * Wird benötigt um Fehler in der Server-Konfiguration zu erkennen
     *
     * @return string|null false wenn PathInfo nicht rekonstruiert werden kann
     */
    public function detect(): ?string
    {
        $scriptName = $this->getSafeScriptName();
        if (empty($scriptName)) {
            return null; // Fehlerhafte Webserver-Konfiguration
        }

        // PathInfo aus $_SERVER['DOCUMENT_URI'] ermitteln
        // Bei Apache nicht gesetzt! Nur bei Nginx und PHP-FPM gesetzt; abhängig von Konfiguration!
        $docUri = $this->request->server->get('DOCUMENT_URI');
        if (!empty($docUri) && strpos($docUri, $scriptName) === 0) {
            return substr($docUri, strlen($scriptName));
        }

        // PathInfo aus $_SERVER['PHP_SELF'] ermitteln
        $phpSelf = $this->request->server->get('PHP_SELF');
        if (strpos($phpSelf, $scriptName) === 0) {
            return substr($phpSelf, strlen($scriptName));
        }

        // PathInfo aus $_SERVER['REQUEST_URI'] ermitteln; ohne URL-Rewriting
        // Request-URI kann Query-Parameter enthalten!
        $reqUri = $this->request->server->get('REQUEST_URI');
        if (!empty($reqUri) && strpos($reqUri, $scriptName) === 0) {
            $pathInfoWithQueryParams = substr($reqUri, strlen($scriptName));

            return $this->trimQueryParams($pathInfoWithQueryParams);
        }

        // Komplexeres URL-Rewriting, oder fehlerhafte Webserver-Konfiguration
        // => PathInfo kann nicht rekonstruiert werden
        return null;
    }

    /**
     * Ermittelt $_SERVER['SCRIPT_NAME'] ohne PathInfo
     *
     * Unter Nginx + PHP-FPM kann(!) der $_SERVER['SCRIPT_NAME'] auch den PathInfo enthalten.
     *
     * @return string
     */
    private function getSafeScriptName(): string
    {
        $scriptFilename = $this->request->server->get('SCRIPT_FILENAME');
        $documentRoot = $this->request->server->get('DOCUMENT_ROOT');

        if (strpos($scriptFilename, $documentRoot) === 0) {
            return substr($scriptFilename, strlen($documentRoot));
        }

        return $this->request->server->get('SCRIPT_NAME');
    }

    /**
     * @param string $url
     *
     * @return string URL ohne Query-Parameter
     */
    private function trimQueryParams(string $url): string
    {
        $queryParamsOffset = strpos($url, '?');
        if ($queryParamsOffset === false) {
            return $url; // Keine Query-Parameter vorhanden
        }

        return substr($url, 0, $queryParamsOffset);
    }
}
