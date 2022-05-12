<?php

namespace Xentral\Modules\Api\Controller\Version1;

use Exception;
use Xentral\Components\Http\Request;
use Xentral\Components\Http\Response;
use Xentral\Modules\Api\Exception\ResourceNotFoundException;
use Xentral\Modules\Api\Exception\ServerErrorException;
use Xentral\Modules\Api\LegacyBridge\LegacyApplication;
use Xentral\Modules\Report\ReportCsvExportService;
use Xentral\Modules\Report\ReportGateway;
use Xentral\Modules\Report\ReportPdfExportService;

class ReportsController
{
    /** @var LegacyApplication $api*/
    private $app;

    /** @var Request $request */
    private $request;

    /** @var int $apiAccountId */
    private $apiAccountId;

    /**
     * @param LegacyApplication    $app
     * @param Request $request
     * @param int     $apiAccountId
     */
    public function __construct(LegacyApplication $app, Request $request, $apiAccountId)
    {
        $this->app = $app;
        $this->request = $request;
        $this->apiAccountId = $apiAccountId;
    }

    /**
     * Datei als Download senden
     *
     * @return Response
     */
    public function downloadAction()
    {
        $reportId = $this->request->attributes->getInt('id');
        $parameters = $this->request->get->all();

        /** @var ReportGateway $gateway */
        $gateway = $this->app->Container->get('ReportGateway');
        $reportObject = $gateway->getReportById($reportId);
        if ($reportObject === null) {
            throw new ResourceNotFoundException('Resource not found');
        }

        /** @var ReportGateway $gateway */
        $gateway = $this->app->Container->get('ReportGateway');
        $transferOptions = $gateway->findTransferArrayByReportId($reportId);
        if (
            empty($transferOptions)
            || !isset(
                $transferOptions['api_active'],
                $transferOptions['api_account_id'],
                $transferOptions['api_format']
            )
            || $transferOptions['api_active'] === 0
            || $transferOptions['api_account_id'] !== $this->apiAccountId
        ) {
            return new Response(
                json_encode(
                    ['error' => ['http_code' => 403, 'message' => 'Access denied']]
                    , JSON_PRETTY_PRINT
                ),
                Response::HTTP_FORBIDDEN
            );
        }

        $clientFileName = '';
        $filePath = '';
        try {
            switch ($transferOptions['api_format']) {
                case 'csv':
                    /** @var ReportCsvExportService $csvExporter */
                    $csvExporter = $this->app->Container->get('ReportCsvExportService');
                    $clientFileName = $csvExporter->generateFileName($reportObject);
                    $filePath = $csvExporter->createCsvFileFromReport($reportObject, $parameters);

                    break;

                case 'pdf':
                    /** @var ReportPdfExportService $pdfExporter */
                    $pdfExporter = $this->app->Container->get('ReportPdfExportService');
                    $clientFileName = $pdfExporter->generateFileName($reportObject);
                    $filePath = $pdfExporter->createPdfFileFromReport($reportObject, $parameters);

                    break;

                default:
            }
        } catch (Exception $e) {
            throw new ServerErrorException();
        }

        if (!is_file($filePath)) {
            throw new ServerErrorException();
        }

        $fileMime = mime_content_type($filePath);
        $header = [
            'Content-Type' => $fileMime,
            'Content-Disposition' => sprintf('attachment; filename="%s"', $clientFileName),
            'Content-Length' => (string)filesize($filePath),
        ];
        $response =  new Response(file_get_contents($filePath), 200, $header);
        unlink($filePath);

        return $response;
    }
}
