<?php

declare(strict_types=1);

namespace Xentral\Modules\MandatoryFields\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\MandatoryFields\Data\MandatoryFieldData;
use Xentral\Modules\MandatoryFields\Exception\MandatoryFieldNotFoundException;
use Xentral\Modules\MandatoryFields\Exception\RuntimeException;

final class MandatoryFieldsService
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
     * @param MandatoryFieldData $mandatoryField
     *
     * @return int
     */
    public function create(MandatoryFieldData $mandatoryField): int
    {
        $sql =
            'INSERT INTO `mandatory_field` (
                `module`,
                `action`,
                `field_id`,
                `error_message`,
                `type`,
                `min_length`,
                `max_length`,
                `mandatory`,
                `comparator`,
                `compareto`
            ) 
            VALUES (
                :module,
                :action,
                :field_id,
                :error_message,
                :type,
                :min_length,
                :max_length,
                :mandatory,
                :comparator,
                :compare_to    
            )';
        $values = [
            'module'        => $mandatoryField->getModule(),
            'action'        => $mandatoryField->getAction(),
            'field_id'      => $mandatoryField->getFieldId(),
            'error_message' => $mandatoryField->getErrorMessage(),
            'type'          => $mandatoryField->getType(),
            'min_length'    => $mandatoryField->getMinLength(),
            'max_length'    => $mandatoryField->getMaxLength(),
            'mandatory'     => $mandatoryField->isMandatory(),
            'comparator'    => $mandatoryField->getComparator(),
            'compare_to'    => $mandatoryField->getCompareto(),
        ];

        $this->db->perform($sql, $values);

        return $this->db->lastInsertId();
    }

    /**
     * @param MandatoryFieldData $mandatoryField
     *
     * @throws MandatoryFieldNotFoundException
     */
    public function edit(MandatoryFieldData $mandatoryField): void
    {
        $sql = 'SELECT m.id FROM `mandatory_field` AS `m` WHERE m.id = :id';
        $data = $this->db->fetchRow($sql, ['id' => $mandatoryField->getId()]);

        if (empty($data)) {
            throw new MandatoryFieldNotFoundException('The record can not be edited: ' . $mandatoryField->getId());
        }

        $sql =
            'UPDATE `mandatory_field` SET
                `module` = :module,
                `action` = :action,
                `field_id` = :field_id,
                `error_message` = :error_message,
                `type` = :type,
                `min_length` = :min_length,
                `max_length` = :max_length,
                `mandatory` = :mandatory,
                `comparator` = :comparator,
                `compareto` = :compare_to
            WHERE `id` = :id';

        $values = [
            'id'            => $mandatoryField->getId(),
            'module'        => $mandatoryField->getModule(),
            'action'        => $mandatoryField->getAction(),
            'field_id'      => $mandatoryField->getFieldId(),
            'error_message' => $mandatoryField->getErrorMessage(),
            'type'          => $mandatoryField->getType(),
            'min_length'    => $mandatoryField->getMinLength(),
            'max_length'    => $mandatoryField->getMaxLength(),
            'mandatory'     => $mandatoryField->isMandatory(),
            'comparator'    => $mandatoryField->getComparator(),
            'compare_to'    => $mandatoryField->getCompareto(),
        ];

        $this->db->perform($sql, $values);
    }

    /**
     * @param int $mandatoryFieldId
     *
     * @throws MandatoryFieldNotFoundException
     */
    public function removeById(int $mandatoryFieldId): void
    {
        $sql = 'SELECT m.id FROM `mandatory_field` AS `m` WHERE m.id = :id';
        $data = $this->db->fetchRow($sql, ['id' => $mandatoryFieldId]);

        if (empty($data)) {
            throw new MandatoryFieldNotFoundException('The Id does not exist: ' . $mandatoryFieldId);
        }

        $sql = 'DELETE FROM `mandatory_field` WHERE `id` = :id';
        $numAffected = (int)$this->db->fetchAffected($sql, ['id' => $mandatoryFieldId]);

        if ($numAffected === 0) {
            throw new RuntimeException('Mandatory field could not be deleted, id: ' . $mandatoryFieldId);
        }
    }
}
