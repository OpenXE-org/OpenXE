<?php

namespace Xentral\Modules\Api\Controller\Version1;

use Xentral\Components\Http\Response;
use Xentral\Modules\Api\Error\ApiError;
use Xentral\Modules\Api\Exception\ResourceNotFoundException;
use Xentral\Modules\Api\Exception\RouteNotFoundException;
use Xentral\Modules\Api\Exception\ServerErrorException;
use Xentral\Modules\Api\Exception\WebserverMisconfigurationException;
use Xentral\Modules\Api\Http\Exception\HttpException;
use Xentral\Modules\Api\Http\PathInfoDetector;
use Xentral\Modules\Api\Resource\Result\ItemResult;

class StartController extends AbstractController
{
    /**
     * @throws HttpException
     *
     * @return Response
     */
    public function indexAction()
    {
        if (!$this->request->isFailsafeUri()) {

            /*
             * Erkennung von fehlerhafter Server-Konfiguration
             *
             * Problem:
             * Nginx übermittelt in der Standard-Konfiguration nicht den PathInfo an PHP.
             * Wenn PathInfo nicht gesetzt ist, landet man immer in diesem Controller und es sieht so aus
             * als würde die API grundsätzlich funktionieren, obwohl man im falschen Endpunkt rauskommt.
             *
             * Lösung:
             * Nachfolgend wird versucht den PathInfo-Teil aus anderen Server-Variablen zu ermitteln.
             * Bei Unterschieden zwischen dem ermittelten und dem gesetzten PathInfo wird eine Exception geworfen.
             */

            $pathInfoDetector = new PathInfoDetector($this->request);
            $pathInfoExpected = $pathInfoDetector->detect();
            $pathInfoActual = (string)$this->request->server->get('PATH_INFO');

            if ($pathInfoActual !== $pathInfoExpected) {
                throw new WebserverMisconfigurationException(
                    'Webserver configuration incorrect. Pathinfo is invalid.',
                    ApiError::CODE_WEBSERVER_PATHINFO_INVALID
                );
            }
        }

        return $this->sendResult(new ItemResult(['info' => 'Nothing here']));
    }

    /**
     * Action zum Ausliefern der /api/docs.html
     *
     * Action greift nur wenn der Webserver falsch konfiguriert ist. Der Webserver müsste existierende
     * Dateien direkt ausliefern ohne Umweg über den API-Frontcontroller.
     *
     * @throws ServerErrorException
     *
     * @return Response
     */
    public function docsAction()
    {
        $docsHtmlFilePath = $this->getApiRootPath() . DIRECTORY_SEPARATOR . 'docs.html';
        if (!is_file($docsHtmlFilePath)) {
            throw new ServerErrorException(sprintf('File not found: %s', $docsHtmlFilePath));
        }

        return new Response(file_get_contents($docsHtmlFilePath), Response::HTTP_OK, ['Content-Type' => 'text/html']);
    }

    /**
     * Action zum Ausliefern von Assets (CSS und JS) der /api/docs.html
     *
     * @throws RouteNotFoundException
     * @throws ResourceNotFoundException
     * @throws ServerErrorException
     *
     * @return Response
     */
    public function docsAssetsAction()
    {
        $assetFileName = $this->request->attributes->get('assetfile');
        if (empty($assetFileName)) {
            throw new RouteNotFoundException('Empty asset file name');
        }

        $mapping = [
            'docs.css'        => 'text/css',
            'docs_custom.css' => 'text/css',
            'docs.js'         => 'application/json',
            '0.docs.js'       => 'application/json',
        ];

        if (!array_key_exists($assetFileName, $mapping)) {
            throw new ResourceNotFoundException(sprintf('Asset file "%s" not found.', $assetFileName));
        }

        $apiRootDir = $this->getApiRootPath() . DIRECTORY_SEPARATOR;
        $assetFilePath = $apiRootDir . 'assets' . DIRECTORY_SEPARATOR . $assetFileName;
        $contentType = $mapping[$assetFileName];

        if (!is_file($assetFilePath)) {
            throw new ServerErrorException(sprintf('File not found: %s', $assetFilePath));
        }

        return new Response(file_get_contents($assetFilePath), Response::HTTP_OK, ['Content-Type' => $contentType]);
    }

    /**
     * @return string Absoute Path without trailing slash
     */
    private function getApiRootPath()
    {
        return dirname(__DIR__, 5) . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'api';
    }
}
