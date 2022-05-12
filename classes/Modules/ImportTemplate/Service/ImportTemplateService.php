<?php

namespace Xentral\Modules\ImportTemplate\Service;

use RuntimeException;
use Xentral\Modules\ImportTemplate\Data\ImportTemplate;
use Xentral\Components\Database\Database;

class ImportTemplateService
{
    /** @var Database $db */
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
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
        $sql = 'INSERT INTO `importvorlage` 
                (
                    `bezeichnung`,
                    `ziel`,
                    `internebemerkung`,
                    `fields`,
                    `importtrennzeichen`,
                    `importerstezeilenummer`,
                    `importdatenmaskierung`,
                    `importzeichensatz`,
                    `utf8decode`,
                    `charset`
                    
                )
                VALUES (
                    :bezeichnung, 
                    :ziel, 
                    :internebemerkung, 
                    :fields, 
                    :importtrennzeichen, 
                    :importerstezeilenummer, 
                    :importdatenmaskierung, 
                    :importzeichensatz,
                    :utf8decode,
                    :charset
                    )';

        $values = [
            'bezeichnung'            => $importTemplate->getLabel(),
            'ziel'                   => $importTemplate->getTarget(),
            'internebemerkung'       => $importTemplate->getInternalNote(),
            'fields'                 => $importTemplate->getFields(),
            'importtrennzeichen'     => $importTemplate->getDelimiter(),
            'importerstezeilenummer' => $importTemplate->getLineNumber(),
            'importdatenmaskierung'  => $importTemplate->getMasking(),
            'importzeichensatz'      => $importTemplate->getImportCharSet(),
            'utf8decode'             => $importTemplate->getUtf8decode(),
            'charset'                => $importTemplate->getCharset(),
        ];
        $this->db->perform($sql, $values);
        $insertId = $this->db->lastInsertId();
        if ($insertId === 0) {
            throw new RuntimeException('Import template could not be created.');
        }

        return $insertId;
    }
}
