<?php

namespace Xentral\Modules\Hubspot;

use Xentral\Components\Database\Database;

final class HubspotContactPropertyGateway
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
     * @param bool   $isSystem
     * @param string $scope
     *
     * @return array
     */
    public function getLeadsByType(string $type, bool $isSystem = false, string $scope = 'contact')
    {
        return $this->db->fetchAssoc(
            'SELECT
            h.id,
            h.created_at,
            h.label,
            h.value,
            h.type,
            h.wiedervorlage_stage_id
            FROM `hs_mapping_leads` AS `h` WHERE h.type = :type AND h.is_system = :system and h.setting_scope = :scope',
            ['type' => $type, 'system' => (int)$isSystem, 'scope' => $scope]
        );
    }

    /**
     * @param string $value
     *
     * @param string $type
     *
     * @return array
     */
    public function getMappingByValueAndType($value, $type)
    {
        return $this->db->fetchRow(
            'SELECT
            hm.id,
            hm.created_at,
            hm.wiedervorlage_stage_id,
            hm.label,
            hm.value,
            hm.type FROM hs_mapping_leads `hm` WHERE hm.value=:value AND hm.type=:type',
            ['value' => $value, 'type' => $type]
        );
    }

    /**
     * @param string $dbName
     *
     * @return array
     */
    public function getAddressFreeFields($dbName)
    {
        return $this->db->fetchCol(
            '
            SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE TABLE_SCHEMA=:db AND TABLE_NAME=:table AND
                                                                           COLUMN_NAME LIKE "adressefreifeld%"
        ',
            [
                'db'    => $dbName,
                'table' => 'firmendaten',
            ]
        );
    }

    /**
     * @return array
     */
    public function getConfiguredFreeAddressFieldValues()
    {
        return $this->db->fetchCol(
            'SELECT f.wert FROM firmendaten_werte `f` WHERE name LIKE "adressetabellezusatz%" AND f.wert !="" AND f.wert IS NOT NULL'
        );
    }
}
