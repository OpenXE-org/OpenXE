<?php

namespace Xentral\Modules\ImportTemplate\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\ImportTemplate\Exception\ImportTemplateNotFoundException;

class ImportTemplateGateway
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param int $importTemplateId
     *
     * @throws ImportTemplateNotFoundException
     *
     * @return array
     */
    public function getImportTemplateById($importTemplateId)
    {
        $importTemplateData = $this->db->fetchRow(
            'SELECT 
                i.id,
                i.bezeichnung AS `label`,
                i.ziel AS `target`,
                i.internebemerkung AS `internalnote`,
                i.fields AS `fields`,
                i.importtrennzeichen AS `delimiter`,
                i.importerstezeilenummer AS `linenumber`,
                i.importdatenmaskierung AS `masking`,
                i.importzeichensatz AS `importcharset`,
                i.utf8decode AS `utf8decode`,
                i.charset AS `charset`
            FROM `importvorlage` AS `i`
            WHERE i.id = :id',
            ['id' => $importTemplateId]
        );

        if (empty($importTemplateData)) {
            throw new ImportTemplateNotFoundException('ImportTemplateId not found: ID ' . $importTemplateId);
        }

        return $importTemplateData;
    }
}
