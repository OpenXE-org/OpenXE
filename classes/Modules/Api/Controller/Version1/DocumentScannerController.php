<?php

namespace Xentral\Modules\Api\Controller\Version1;

use DateTimeImmutable;
use Xentral\Components\Http\Response;
use Xentral\Components\Util\StringUtil;
use Xentral\Modules\Api\Engine\ApiUrlGenerator;
use Xentral\Modules\Api\Error\ApiError;
use Xentral\Modules\Api\Exception\BadRequestException;
use Xentral\Modules\Api\Exception\ResourceNotFoundException;
use Xentral\Modules\Api\Exception\ServerErrorException;
use Xentral\Modules\Api\Resource\FileResource;
use Xentral\Modules\Api\Resource\Result\ItemResult;

class DocumentScannerController extends AbstractController
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
     * Datei anlegen/hochladen
     *
     * @throws BadRequestException Wenn Pflichtfelder leer, oder Content-Type falsch
     * @throws ServerErrorException Wenn Datei aus unbekannten Gründen nicht angelegt werden konnte (sollte nicht auftreten)
     *
     * @return Response
     */
    public function createAction()
    {
        $input = null;
        $contentTypeRaw = $this->request->getHeader('Content-Type');
        if (empty($contentTypeRaw)) {
            $errorMsg = 'Content-Type header is empty. ';
            $errorMsg .= 'Only "application/x-www-form-urlencoded" or "multipart/form-data" is supported.';
            throw new BadRequestException(
                'Unsupported Content-Type', ApiError::CODE_CONTENT_TYPE_NOT_SUPPORTED, null, [$errorMsg]
            );
        }
        if (StringUtil::startsWith($contentTypeRaw, 'multipart/form-data')) {
            $input = $this->getRequestDataFromMultipartForm();
        }
        if (StringUtil::startsWith($contentTypeRaw, 'application/x-www-form-urlencoded')) {
            $input = $this->getRequestDataFromUrlEncodedForm();
        }
        if ($input === null) {
            $errorMsg = sprintf('Content-Type "%s" is not supported. ', $contentTypeRaw);
            $errorMsg .= 'Only "application/x-www-form-urlencoded" or "multipart/form-data" is supported.';
            throw new BadRequestException(
                'Unsupported Content-Type', ApiError::CODE_CONTENT_TYPE_NOT_SUPPORTED, null, [$errorMsg]
            );
        }

        if (empty($input['dateiname'])) {
            throw new BadRequestException('Required property "dateiname" is missing.');
        }
        if (empty($input['titel'])) {
            throw new BadRequestException('Required property "titel" is missing.');
        }
        if (empty($input['file_content'])) {
            throw new BadRequestException('Required property "file_content" is missing or file is empty.');
        }

        // Meta-Daten prüfen
        if (!empty($input['meta'])) {
            $this->checkMetaData($input['meta']);
            $metaData = $input['meta'];
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

        // Datei in docscan-Tabelle verknüpfen und Datei-Stichwort hinzufügen
        $this->db->perform(
            'INSERT INTO `docscan` (`id`, `datei`, `kategorie`) VALUES (NULL, :file_id, NULL)',
            ['file_id' => $fileId]
        );
        $docscanId = $this->db->lastInsertId();
        $erp->AddDateiStichwort($fileId, 'Sonstige', 'DocScan', $docscanId);

        // Meta-Daten speichern
        if (isset($metaData) && !empty($metaData)) {
            $this->saveMetaData($docscanId, $metaData);
        }

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
    protected function getRequestDataFromMultipartForm()
    {
        if ($this->request->getContentType() !== 'form-data') {
            throw new BadRequestException(
                'Unsupported Content-Type',
                ApiError::CODE_CONTENT_TYPE_NOT_SUPPORTED,
                null,
                ['Content-Type must be "multipart/form-data"']
            );
        }

        $input = $this->request->post->all();
        if (!isset($input['file_content']) && $this->request->files->has('file_content')) {
            $upload = $this->request->files->get('file_content');
            $input['file_content'] = $upload->getContent();
        }

        return $input;
    }

    /**
     * @throws BadRequestException
     *
     * @return array
     */
    protected function getRequestDataFromUrlEncodedForm()
    {
        if ($this->request->getContentType() !== 'x-www-form-urlencoded') {
            throw new BadRequestException(
                'Unsupported Content-Type',
                ApiError::CODE_CONTENT_TYPE_NOT_SUPPORTED,
                null,
                ['Content-Type must be "application/x-www-form-urlencoded"']
            );
        }

        return $this->request->post->all();
    }

    /**
     * @param array $data
     *
     * @throws BadRequestException
     *
     * @return void
     */
    protected function checkMetaData($data)
    {
        if (!is_array($data)) {
            throw new BadRequestException('Wrong value type in property "meta". Only type array is allowed.');
        }

        $allowedKeys = ['invoice_number', 'invoice_date', 'invoice_amount', 'invoice_tax', 'invoice_currency'];

        foreach ($data as $key => $value) {
            if (is_int($key)) {
                throw new BadRequestException('Wrong format in property "meta". Numeric keys are not allowed.');
            }
            $cleanedKey = (string)preg_replace('#[^a-z0-9_]#', '', trim($key));
            if ($key !== $cleanedKey) {
                throw new BadRequestException(sprintf(
                    'Meta key "%s" contains an illegal character. Allowed characters: a-z, 0-9 and underscore.', $key
                ));
            }
            if (!in_array($key, $allowedKeys, true)) {
                throw new BadRequestException(sprintf(
                    'Meta key "%s" is not allowed. Allowed keys: %s', $key, implode(', ', $allowedKeys)
                ));
            }
            if (mb_strlen($value) > 32) {
                throw new BadRequestException(sprintf(
                    'Wrong value format in property "meta.%s". Max value length is 32 characters.',
                    $key
                ));
            }

            if ($key === 'invoice_number') {
                if (!is_string($value)) {
                    throw new BadRequestException(
                        'Wrong value type in property "meta.invoice_number". Only type string is allowed.'
                    );
                }
            }
            if ($key === 'invoice_date') {
                $invoiceDate = DateTimeImmutable::createFromFormat('Y-m-d', $value);
                if ($invoiceDate === false || array_sum($invoiceDate::getLastErrors()) > 0) {
                    throw new BadRequestException(
                        'Wrong value format or invalid date in property "meta.invoice_date". Allowed format: "YYYY-MM-DD"'
                    );
                }
            }
            if ($key === 'invoice_amount') {
                $cleanedInvoiceAmount = (string)preg_replace('#[^0-9.]#', '', $value);
                if ($value !== $cleanedInvoiceAmount) {
                    throw new BadRequestException(
                        'Wrong value format in property "meta.invoice_amount". Value can only contain numbers and a period character.'
                    );
                }
            }
            if ($key === 'invoice_tax') {
                $cleanedInvoiceTax = (string)preg_replace('#[^0-9.]#', '', $value);
                if ($value !== $cleanedInvoiceTax) {
                    throw new BadRequestException(
                        'Wrong value format in property "meta.invoice_tax". Value can only contain numbers and a period character.'
                    );
                }
            }
            if ($key === 'invoice_currency') {
                if (!is_string($value)) {
                    throw new BadRequestException(
                        'Wrong value type in property "meta.invoice_currency". Only type string is allowed.'
                    );
                }
                if (mb_strlen($value) !== 3) {
                    throw new BadRequestException(
                        'Wrong value format in property "meta.invoice_currency". Value must be three characters long.'
                    );
                }
                $cleanedCurrencyCode = (string)preg_replace('#[^A-Z]#', '', $value);
                if ($value !== $cleanedCurrencyCode) {
                    throw new BadRequestException(
                        'Wrong value format in property "meta.invoice_currency". Value must contain three uppercase characters.'
                    );
                }
            }
        }
    }

    /**
     * @param int   $docscanId
     * @param array $metaData
     *
     * @return void
     */
    protected function saveMetaData(int $docscanId, array $metaData)
    {
        if (empty($metaData)) {
            return;
        }

        $this->db->beginTransaction();
        foreach ($metaData as $metaKey => $metaValue) {
            $this->db->perform(
                'INSERT INTO `docscan_metadata` (`id`, `docscan_id`, `meta_key`, `meta_value`) 
                 VALUES (NULL, :docscan_id, :meta_key, :meta_value)',
                [
                    'docscan_id' => $docscanId,
                    'meta_key'   => (string)$metaKey,
                    'meta_value' => (string)$metaValue,
                ]
            );
        }
        $this->db->commit();
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
        $includes = ['metadata'];//$this->prepareIncludeParams();
        $result = $resource->getOne($fileId, $includes);
        $downloadBaseUrl = $this->buildDownloadBaseUrl($fileId);

        // Daten anreichern um Download-Links
        $data = $result->getData();
        $data['mimetype'] = $fileMime;
        $data['links'] = [
            'download' => $downloadBaseUrl  . '/download',
            'base64' => $downloadBaseUrl . '/base64',
        ];

        return new ItemResult($data);
    }

    /**
     * @param int $fileId
     *
     * @return string
     */
    protected function buildDownloadBaseUrl($fileId)
    {
        $urlGenerator = new ApiUrlGenerator($this->request);

        return $urlGenerator->generate('/v1/dateien/' . (int)$fileId);
    }
}
