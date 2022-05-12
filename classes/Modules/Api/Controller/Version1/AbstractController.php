<?php

namespace Xentral\Modules\Api\Controller\Version1;

use Xentral\Components\Database\Database;
use Xentral\Components\Http\Request;
use Xentral\Components\Http\Response;
use Xentral\Modules\Api\Converter\Converter;
use Xentral\Modules\Api\Converter\Exception\ConvertionException;
use Xentral\Modules\Api\Error\ApiError;
use Xentral\Modules\Api\Exception\BadRequestException;
use Xentral\Modules\Api\Exception\InvalidArgumentException;
use Xentral\Modules\Api\Resource\AbstractResource;
use Xentral\Modules\Api\Resource\ResourceManager;
use Xentral\Modules\Api\Resource\Result\AbstractResult;

abstract class AbstractController
{
    /** @var Database $db */
    protected $db;

    /** @var Request $request */
    protected $request;

    /** @var Response $response */
    protected $response;

    /** @var ResourceManager $resourceManager */
    protected $resourceManager;

    /** @var string $resourceClass */
    protected $resourceClass;

    /** @var \Api $db */
    protected $legacyApi;

    /**
     * @param \Api            $legacyApi
     * @param Database        $database
     * @param Converter       $converter
     * @param Request         $request
     * @param ResourceManager $resource
     */
    public function __construct($legacyApi, $database, $converter, $request, $resource)
    {
        $this->resourceManager = $resource;
        $this->legacyApi = $legacyApi;
        $this->converter = $converter;
        $this->request = $request;
        $this->db = $database;
    }

    /**
     * @param string $action Controller-Action
     *
     * @return Response
     */
    public function dispatch($action)
    {
        if (substr($action, -6) !== 'Action') {
            throw new \RuntimeException(sprintf(
                'API controller action "%s" is not dispatchable.', $action
            ));
        }
        if (!method_exists($this, $action)) {
            throw new \RuntimeException(sprintf(
                'API controller method "%s" not found', $action
            ));
        }

        $this->response = $this->$action();
        if ($this->response === null) {
            throw new \RuntimeException('Controller must return a Response object. Null given.');
        }
        if (!$this->response instanceof Response) {
            throw new \RuntimeException('Controller must return a Response object.');
        }

        return $this->response;
    }

    /**
     * @param string $className
     */
    public function setResourceClass($className)
    {
        $this->resourceClass = $className;
    }

    /**
     * ID aus der URL (Route) bekommen
     *
     * @return int
     */
    protected function getResourceId()
    {
        return (int)$this->request->attributes->getDigits('id');
    }

    /**
     * @param string|null $className
     *
     * @return AbstractResource
     */
    protected function getResource($className = null)
    {
        return $this->resourceManager->get($className !== null ? $className : $this->resourceClass);
    }

    /**
     * Request-Body in Array wandeln
     *
     * @return array
     */
    protected function getRequestData()
    {
        try {
            return $this->converter->toArray($this->getContentType(), $this->request->getContent());
        } catch (ConvertionException $e) {
            throw new BadRequestException(
                sprintf('%s could not be decoded.', strtoupper($this->getContentType())),
                ApiError::CODE_MALFORMED_REQUEST_BODY
            );
        }
    }

    /**
     * @return null|string [json|xml]
     */
    protected function getContentType()
    {
        return $this->request->getContentType();
    }

    /**
     * @param AbstractResult $result
     * @param int            $statusCode
     *
     * @return Response
     */
    protected function sendResult(AbstractResult $result, $statusCode = Response::HTTP_OK)
    {
        $contentType = $this->determineResponseContentType();
        $data = [];

        if ($contentType === 'xml') {
            if ($result->isCollection()) {
                $data['items'] = $result->getData();
                $data['pagination'] = $result->getPagination();
            } else {
                $data['item'] = $result->getData();
            }
        }
        if ($contentType === 'json') {
            $data = $result->getResult();
        }

        return $this->sendResponse($data, $contentType, $statusCode);
    }

    /**
     * Content-Type für die Ausgabe bestimmen
     *
     * @return string [xml|json]
     */
    protected function determineResponseContentType()
    {
        // Accept-Header auslesen
        $acceptable = $this->request->getAcceptableContentTypes();

        switch ($acceptable[0]) {
            // Client ist vermutlich ein Browser > JSON ausliefern
            case 'text/html':
                $contentType = 'json';
                break;

            // Client hat JSON angefragt
            case 'application/json':
                $contentType = 'json';
                break;

            // Client hat XML angefragt
            case 'application/xml':
                $contentType = 'xml';
                break;

            // Nicht eindeutig > JSON bevorzugen
            default:
                if (in_array('application/xml', $acceptable)) {
                    $contentType = 'xml';
                    break;
                }
                $contentType = 'json';
                break;
        }

        return $contentType;
    }

    /**
     * @param array  $data
     * @param string $contentType [xml|json]
     * @param int    $statusCode HTTP-Statuscode
     *
     * @return Response
     */
    protected function sendResponse($data, $contentType, $statusCode = Response::HTTP_OK)
    {
        if ($contentType === 'xml') {
            return new Response(
                $this->converter->arrayToXml($data, 'result'),
                $statusCode,
                ['Content-Type' => 'application/xml; charset=UTF-8']
            );
        }

        return new Response(
            $this->converter->arrayToJson($data),
            $statusCode,
            ['Content-Type' => 'application/json; charset=UTF-8']
        );
    }

    /**
     * Filterparameter aufbereiten
     *
     * @example /resource?title=123&project=1
     * @example /resource?title_starts_with=123&project=1
     *
     * @return array
     */
    protected function prepareFilterParams()
    {
        $queryParams = $this->request->get->all();

        // Reservierte Parameter ignorieren
        unset(
            $queryParams['sort'],
            $queryParams['page'],
            $queryParams['items'],
            $queryParams['filter'],
            $queryParams['include']
        );

        $filter = [];
        foreach ($queryParams as $filterKey => $filterValue) {
            $filter[$filterKey] = filter_var($filterValue, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
        }

        // Komplexe Suchfilter enthalten Array
        $filter['filter'] = $this->prepareComplexFilterParams();

        return $filter;
    }

    /**
     * Filterparameter für komplexe Suche aufbereiten
     *
     * @example /resource?filter[0][property]=satz&filter[0][expression]=gte&filter[0][value]=10
     *                   &filter[1][property]=bezeichnung&filter[1][value]=%Irland%
     *
     * @return array
     */
    protected function prepareComplexFilterParams()
    {
        $filter = [];

        $params = $this->request->get->get('filter');
        if (!is_array($params)) {
            return $filter;
        }

        ksort($params);
        $params = array_values($params);

        return $params;

        foreach ($params as $param) {

            echo "<pre>";
            var_dump($params);
            echo "</pre>";
            exit;
            // @todo Sanitize

            echo "<pre>";
            var_dump($param);
            echo "</pre>";
            exit;
        }

        return $filter;
    }

    /**
     * Sortierungsparameter aufbereiten
     *
     * @example /resource?sort=name,project
     * @example /resource?sort=-name,project
     *
     * @return array
     */
    protected function prepareSortingParams()
    {
        $sorting = [];
        $sortQuery = filter_var($this->request->get->get('sort'), FILTER_SANITIZE_URL);
        if (empty($sortQuery)) {
            return $sorting;
        }

        /**
         * Alte Syntax
         *
         * @example /resource?sort=title:desc|projekt:asc
         */
        if (strpos($sortQuery, '|')) {
            $sortParams = explode('|', $sortQuery);
            foreach ($sortParams as $sortParam) {
                if (strpos($sortParam, ':')) {
                    list($sortField, $sortOrder) = explode(':', $sortParam, 2);
                } else {
                    $sortField = $sortParam;
                    $sortOrder = 'asc';
                }

                if (empty($sortField) || $sortField === ':') {
                    throw new InvalidArgumentException('Sorting parameter can not be empty');
                }
                if (!in_array(strtolower($sortOrder), ['asc', 'desc'], true)) {
                    throw new InvalidArgumentException(sprintf(
                        'Sorting order "%s" is not valid. Use "asc" or "desc".', $sortOrder
                    ));
                }

                $sortOrder = strtolower($sortOrder) === 'desc' ? 'DESC' : 'ASC';
                $sorting[$sortField] = $sortOrder;
            }

            return $sorting;
        }

        /**
         * Neue Syntax: Minuszeichen vor dem Feld kehrt die Sortierung um
         *
         * @example /resource?sort=-title,projekt
         */
        $sortParams = explode(',', $sortQuery);
        foreach ($sortParams as $sortParam) {
            if (strpos($sortParam, '-') === 0) {
                $sortField = substr_replace($sortParam, '', 0, 1);
                $sortOrder = 'DESC';
            } else {
                $sortField = $sortParam;
                $sortOrder = 'ASC';
            }

            if (empty($sortField) || $sortField === '-') {
                throw new InvalidArgumentException('Sorting parameter can not be empty');
            }

            $sorting[$sortField] = $sortOrder;
        }

        return $sorting;
    }

    /**
     * @return array
     */
    protected function prepareIncludeParams()
    {
        $includesQuery = $this->request->get->get('include');
        if (empty($includesQuery)) {
            return [];
        }

        $includes = explode(',', $includesQuery);
        $includes = array_map('trim', $includes);
        $includes = array_map('htmlspecialchars', $includes);

        return $includes;
    }

    /**
     * @return int
     */
    protected function getPaginationPage()
    {
        $page = $this->request->get->getInt('page');

        return $page > 0 && $page <= 1000 ? $page : 1;
    }

    /**
     * @return int
     */
    protected function getPaginationCount()
    {
        $items = $this->request->get->getInt('items');

        return $items > 0 && $items <= 1000 ? $items : 20;
    }
}
