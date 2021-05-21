<?php

namespace Xentral\Modules\Label;

use Xentral\Components\Database\Database;
use Xentral\Modules\Label\Exception\LabelTypeNotFoundException;

final class LabelGateway
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
     * @param string $labelType
     *
     * @throws LabelTypeNotFoundException
     *
     * @return int
     */
    public function getLabelTypeId($labelType)
    {
        $labelType = (string)$labelType;

        $labelTypeId = (int)$this->db->fetchValue(
            'SELECT lt.id FROM label_type AS lt WHERE lt.type = :label_type',
            ['label_type' => $labelType]
        );

        if ($labelTypeId === 0) {
            throw new LabelTypeNotFoundException(sprintf(
                'Label type "%s" not found.', $labelType
            ));
        }

        return $labelTypeId;
    }

    /**
     * @param string $referenceTable
     * @param int    $referenceId
     *
     * @return array
     */
    public function findLabelsByReference($referenceTable, $referenceId)
    {
        $referenceTable = (string)$referenceTable;
        $referenceId = (int)$referenceId;

        $result = $this->db->fetchAll(
            'SELECT lr.id, lr.reference_table, lr.reference_id, lt.type, lt.title, lt.hexcolor 
             FROM label_reference AS lr 
             INNER JOIN label_type AS lt ON lr.label_type_id = lt.id
             LEFT JOIN label_group AS lg ON lg.id = lt.label_group_id
             WHERE lr.reference_table = :reference_table AND lr.reference_id = :reference_id
             AND (lt.label_group_id = 0 OR lg.group_table = :reference_table)',
            [
                'reference_table' => $referenceTable,
                'reference_id'    => $referenceId,
            ]
        );

        return $result;
    }

    /**
     * @param string      $referenceTable
     * @param int[]|array $referenceIds
     *
     * @return array
     */
    public function findLabelsByReferences($referenceTable, $referenceIds)
    {
        $referenceTable = (string)$referenceTable;
        $referenceIds = (array)$referenceIds;

        $cleanedIds = [];
        foreach ($referenceIds as $referenceId) {
            $referenceId = (int)$referenceId;
            if ($referenceId > 0) {
                $cleanedIds[] = $referenceId;
            }
        }

        $result = $this->db->fetchAll(
            'SELECT lr.id, lr.reference_table, lr.reference_id, lt.type, lt.title, lt.hexcolor 
             FROM label_reference AS lr 
             INNER JOIN label_type AS lt ON lr.label_type_id = lt.id
             LEFT JOIN label_group AS lg ON lt.label_group_id = lg.id
             WHERE lr.reference_table = :reference_table AND lr.reference_id IN (:reference_ids)
               AND (lt.label_group_id = 0 OR lg.group_table = :reference_table)',
            [
                'reference_table' => $referenceTable,
                'reference_ids'   => $cleanedIds,
            ]
        );

        return $result;
    }

    /**
     * @param string $referenceTable
     * @param int    $referenceId
     *
     * @return array
     */
    public function findLabelTypesByReference($referenceTable, $referenceId)
    {
        $referenceId = (int)$referenceId;
        $referenceTable = (string)$referenceTable;

        $labelTypes = (array)$this->db->fetchAll(
            'SELECT 
                lt.id, lt.type, lt.title, lt.hexcolor, lr.id AS label_id
             FROM label_type AS lt 
             LEFT JOIN label_group AS lg ON lt.label_group_id = lg.id
             LEFT JOIN label_reference AS lr 
                 ON lr.label_type_id = lt.id 
                AND lr.reference_id = :reference_id
                AND lr.reference_table = :reference_table
             WHERE (lt.label_group_id = 0 OR lg.group_table = :reference_table)',
            [
                'reference_table' => $referenceTable,
                'reference_id'    => $referenceId,
            ]
        );

        return $labelTypes;
    }
}
