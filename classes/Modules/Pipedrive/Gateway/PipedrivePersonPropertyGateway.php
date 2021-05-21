<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Gateway;

use Xentral\Components\Database\Database;

final class PipedrivePersonPropertyGateway
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
     * @param string $type
     *
     * @param bool   $isSystem
     *
     * @return array
     */
    public function getLeadsByType(string $type, bool $isSystem = false): array
    {
        return $this->db->fetchAssoc(
            'SELECT
            p.id,
            p.created_at,
            p.label,
            p.value,
            p.type,
            p.wiedervorlage_stage_id
            FROM `pipedrive_mappings` AS `p` WHERE p.type = :type AND p.is_system=:system',
            ['type' => $type, 'system' => (int)$isSystem]
        );
    }

    /**
     * @param int    $value
     * @param string $type
     *
     * @return array
     */
    public function getMappingByValueAndType(int $value, string $type): array
    {
        return $this->db->fetchRow(
            'SELECT
            hm.id,
            hm.created_at,
            hm.wiedervorlage_stage_id,
            hm.label,
            hm.value,
            hm.type
            FROM `pipedrive_mappings` AS `hm`
            WHERE hm.value =:value AND hm.type = :type',
            ['value' => $value, 'type' => $type]
        );
    }

    /**
     * @param string $dbName
     *
     * @return array
     */
    public function getAddressFreeFields(string $dbName): array
    {
        return $this->db->fetchCol(
            'SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS`
            WHERE `TABLE_SCHEMA` =:db AND `TABLE_NAME` = :table AND `COLUMN_NAME` LIKE "adressefreifeld%"',
            [
                'db'    => $dbName,
                'table' => 'firmendaten',
            ]
        );
    }

    /**
     * @return array
     */
    public function getConfiguredFreeAddressFieldValues(): array
    {
        return $this->db->fetchCol(
            'SELECT f.wert FROM `firmendaten_werte` AS `f`
             WHERE `name` LIKE "adressetabellezusatz%" AND f.wert !="" AND f.wert IS NOT NULL'
        );
    }
}
