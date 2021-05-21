<?php

namespace Xentral\Widgets\DataTable\Service;

use Xentral\Components\Http\FileResponse;
use Xentral\Components\Http\JsonResponse;
use Xentral\Components\Http\Response;
use Xentral\Widgets\DataTable\DataTableBuildConfig;
use Xentral\Widgets\DataTable\Request\DataTableRequest;

final class DataTableRequestHandler
{
    /** @var DataTableService $service */
    private $service;

    /** @var DataTableRequest $request */
    private $request;

    /**
     * @param DataTableService $service
     * @param DataTableRequest $request
     */
    public function __construct(DataTableService $service, DataTableRequest $request)
    {
        $this->service = $service;
        $this->request = $request;
    }

    /**
     * @param DataTableBuildConfig $config
     *
     * @return string
     */
    public function generateHtml(DataTableBuildConfig $config)
    {
        return $this->service->renderHtml($config);
    }

    /**
     * @param DataTableBuildConfig $config
     *
     * @return bool
     */
    public function canHandleRequest(DataTableBuildConfig $config)
    {
        if ($this->request->getMethod() !== $config->getAjaxMethod()) {
            return false;
        }
        if ($this->request->isDataRequest()) {
            return true;
        }
        if ($this->request->isExportRequest()) {
            return true;
        }

        return false;
    }

    /**
     * @param DataTableBuildConfig $config
     *
     * @return Response
     */
    public function handleRequest(DataTableBuildConfig $config)
    {
        if ($this->request->isDataRequest()) {
            return $this->handleDataRequest($config);
        }
        if ($this->request->isExportRequest()) {
            return $this->handleExportRequest($config);
        }

        return new JsonResponse([
            'success' => false,
            'error'   => 'Can not fetch data from datatable. This is not a valid request.',
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param DataTableBuildConfig $config
     *
     * @return Response
     */
    private function handleExportRequest(DataTableBuildConfig $config)
    {
        $filePath = $this->service->exportData($config);

        return FileResponse::createFromFile($filePath, 'export.csv', 'text/csv', true);
    }

    /**
     * @param DataTableBuildConfig $config
     *
     * @return Response
     */
    private function handleDataRequest(DataTableBuildConfig $config)
    {
        if (!$this->service->canFetchData($config)) {
            return new JsonResponse([
                'success' => false,
                'error'   => 'Can not fetch data from datatable. Build config does not match with request parameters.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->service->fetchData($config);
        $status = $result->hasError() ? Response::HTTP_INTERNAL_SERVER_ERROR : Response::HTTP_OK;

        return new JsonResponse($result, $status);
    }
}
