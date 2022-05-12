<?php

namespace Xentral\Modules\ImportTemplate\Service;

use RuntimeException;
use Xentral\Modules\ImportTemplate\Data\ImportTemplate;
use Xentral\Modules\ImportTemplate\Exception\ImportTemplateNotFoundException;
use Xentral\Modules\ImportTemplate\Exception\InvalidTemplateDataException;

final class ImportTemplateJsonService
{
    /** @var ImportTemplateService $importTemplateService */
    private $importTemplateService;

    /** @var ImportTemplateGateway $importTemplateGateway */
    private $importTemplateGateway;

    /**
     * @param ImportTemplateService $importTemplateService
     * @param ImportTemplateGateway $importTemplateGateway
     */
    public function __construct(
        ImportTemplateService $importTemplateService,
        ImportTemplateGateway $importTemplateGateway
    ) {
        $this->importTemplateService = $importTemplateService;
        $this->importTemplateGateway = $importTemplateGateway;
    }

    /**
     * @param array $templateData
     *
     * @throws InvalidTemplateDataException
     *
     * @return int
     */
    public function insertAndValidateImportTemplate($templateData)
    {
        $validData = ImportTemplate::fromArray($templateData);

        return $this->insertImportTemplate($validData);
    }

    /**
     * @param ImportTemplate $importTemplate
     *
     * @throws RuntimeException
     *
     * @return int
     */
    public function insertImportTemplate(ImportTemplate $importTemplate)
    {
        return $this->importTemplateService->insertImportTemplate($importTemplate);
    }

    /**
     * @param int $importTemplateId
     *
     * @throws ImportTemplateNotFoundException
     *
     * @return ImportTemplate
     */
    public function getImportTemplate($importTemplateId)
    {
        $importTemplateData = $this->importTemplateGateway->getImportTemplateById($importTemplateId);
        $importTemplate = ImportTemplate::fromDbState($importTemplateData);

        return $importTemplate;
    }
}
