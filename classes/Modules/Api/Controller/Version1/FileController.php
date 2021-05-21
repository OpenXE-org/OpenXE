<?php

namespace Xentral\Modules\Api\Controller\Version1;

use Xentral\Components\Http\Response;
use Xentral\Modules\Api\Error\ApiError;
use Xentral\Modules\Api\Exception\BadRequestException;
use Xentral\Modules\Api\Exception\ResourceNotFoundException;
use Xentral\Modules\Api\Exception\ServerErrorException;
use Xentral\Modules\Api\Resource\FileResource;
use Xentral\Modules\Api\Resource\Result\ItemResult;

class FileController extends AbstractController
{
    /**
     * Resourcen-Liste abrufen
     *
     * @return Response
     */
    public function listAction()
    {
        // Filter, Sortierung und Paginierung
        $filter = $this->prepareFilterParams();
        $sorting = $this->prepareSortingParams();
        $includes = $this->prepareIncludeParams();
        $currentPage = $this->getPaginationPage();
        $itemsPerPage = $this->getPaginationCount();

        // Liste laden
        $resource = $this->getResource($this->resourceClass);
        $result = $resource->getList($filter, $sorting, [], $includes, $currentPage, $itemsPerPage);

        return $this->sendResult($result);
    }

    /**
     * Einzelne Resource anhand ID laden
     *
     * @return Response
     */
    public function readAction()
    {
        return $this->sendResult($this->readResult());
    }

    /**
     * Datei als Download senden
     *
     * @return Response
     */
    public function downloadAction()
    {
        $fileId = $this->getResourceId();

        $erp = $this->legacyApi->app->erp;
        $filePath = $erp->GetDateiPfad($fileId);
        $fileName = $erp->GetDateiName($fileId);
        if (!is_file($filePath)) {
            throw new ResourceNotFoundException('File not found in filesystem.');
        }

        $fileMime = mime_content_type($filePath);
        if ($fileMime === 'directory') {
            throw new ResourceNotFoundException('File not found. File is a directory.');
        }

        $header = [
            'Content-Type' => $fileMime,
            'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
            'Content-Length' => (string)filesize($filePath),
        ];

        return new Response(file_get_contents($filePath), 200, $header);
    }

    /**
     * Datei base64-kodiert senden
     *
     * @return Response
     */
    public function base64Action()
    {
        $fileId = $this->getResourceId();

        $erp = $this->legacyApi->app->erp;
        $filePath = $erp->GetDateiPfad($fileId);
        if (!is_file($filePath)) {
            throw new ResourceNotFoundException('File not found in filesystem.');
        }

        $fileMime = mime_content_type($filePath);
        if ($fileMime === 'directory') {
            throw new ResourceNotFoundException('File not found. File is a directory.');
        }

        $prefix = 'data:' . $fileMime . ';base64,';
        $header = [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'inline',
        ];

        return new Response($prefix . base64_encode(file_get_contents($filePath)), 200, $header);
    }

    /**
     * Datei anlegen/hochladen
     *
     * @throws BadRequestException Wenn Pflichtfelder leer, oder Content-Type falsch
     * @throws ServerErrorException Wenn Datei aus unbekannten Gründen nicht angelegt werden konnte (sollte nicht auftreten)
     *
     * @return Response
     */
    public function createAction()
    {
        $input = $this->getRequestDataFromUrlEncodedForm();

        if (empty($input['dateiname'])) {
            throw new BadRequestException('Required property "dateiname" is missing.');
        }
        if (empty($input['titel'])) {
            throw new BadRequestException('Required property "titel" is missing.');
        }
        if (empty($input['file_content'])) {
            throw new BadRequestException('Required property "file_content" is missing.');
        }

        $fileName = $input['dateiname']; // Pflichtfeld
        $fileTitle = $input['titel']; // Pflichtfeld
        $fileDescription = $input['beschreibung'] ?? '';
        $fileNumber = null;
        $fileCreatorUserId = null;

        $erp = $this->legacyApi->app->erp;
        $fileId = (int)$erp->CreateDatei(
            $fileName,
            $fileTitle,
            $fileDescription,
            $fileNumber,
            $input['file_content'],
            $fileCreatorUserId
        );

        if ($fileId <= 0) {
            throw new ServerErrorException('Failed to create file.');
        }

//        if (!empty($input['belegtyp'])) {
//            $erp->AddDateiStichwort($fileId, 'Belege', $input['belegtyp'], $belegId); // @todo $belegId
//        }

        // Bei Erfolg die angelegte Resource zurückliefern; mit Success-Flag
        /** @var FileResource $resource */
        $result = $this->readResult($fileId);
        $result->setSuccess(true);

        return $this->sendResult($result, Response::HTTP_CREATED);
    }

    /**
     * @throws ResourceNotFoundException
     *
     * @return void
     */
    public function updateAction()
    {
        throw new ResourceNotFoundException();
    }

    /**
     * @throws BadRequestException
     *
     * @return array
     */
    protected function getRequestDataFromUrlEncodedForm()
    {
        $request = $this->request;
        if ($request->getContentType() !== 'x-www-form-urlencoded') {
            throw new BadRequestException(
                'Unsupported Content-Type',
                ApiError::CODE_CONTENT_TYPE_NOT_SUPPORTED,
                null,
                ['Content-Type must be "application/x-www-form-urlencoded"']
            );
        }

        return $request->post->all();
    }

    /**
     * @param int|null $useFileId
     *
     * @throws ResourceNotFoundException
     *
     * @return ItemResult
     */
    protected function readResult($useFileId = null)
    {
        $fileId = (int)$useFileId > 0 ? (int)$useFileId : $this->getResourceId();

        $erp = $this->legacyApi->app->erp;
        $filePath = $erp->GetDateiPfad($fileId);
        if (!is_file($filePath)) {
            throw new ResourceNotFoundException('File not found in filesystem.');
        }

        $fileMime = mime_content_type($filePath);
        if ($fileMime === 'directory') {
            throw new ResourceNotFoundException('File not found. File is a directory.');
        }

        $resource = $this->getResource($this->resourceClass);
        $includes = $this->prepareIncludeParams();
        $result = $resource->getOne($fileId, $includes);
        $fullUri = $this->request->getFullUrl();

        // URI um File-ID erweitern, wenn der Request ohne ID war (beim Anlegen)
        if ((int)$useFileId > 0) {
            $fullUri .= '/' . $useFileId;
        }

        // Daten anreichern um Download-Links
        $data = $result->getData();
        $data['mimetype'] = $fileMime;
        $data['links'] = [
            'download' => $fullUri . '/download',
            'base64' => $fullUri . '/base64',
        ];

        return new ItemResult($data);
    }
}
