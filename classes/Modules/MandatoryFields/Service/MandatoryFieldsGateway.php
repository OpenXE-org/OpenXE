<?php

declare(strict_types=1);

namespace Xentral\Modules\MandatoryFields\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\MandatoryFields\Data\MandatoryFieldData;
use Xentral\Modules\MandatoryFields\Exception\MandatoryFieldNotFoundException;

final class MandatoryFieldsGateway
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
     * @param string $module
     * @param string $action
     * @param string $fieldId
     * @param string $type
     *
     * @return MandatoryFieldData|null
     */
    public function getMandatoryFieldByParameters(
        string $module,
        string $action,
        string $fieldId,
        string $type
    ): ?MandatoryFieldData {
        $sql =
            'SELECT
                `id`,
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
            FROM `mandatory_field` AS `m`
            WHERE m.module = :module
            AND m.action = :action
            AND m.type = :type
            AND m.field_id = :field_id
            LIMIT 1';

        $values = [
            'module'  => $module,
            'action'  => $action,
            'field_id' => $fieldId,
            'type'    => $type,
        ];

        $data = $this->db->fetchRow($sql, $values);

        if (!empty($data)) {
            return MandatoryFieldData::fromDbState($data);
        }

        return null;
    }

    /**
     * @param int $mandatoryFieldId
     *
     * @throws MandatoryFieldNotFoundException
     *
     * @return MandatoryFieldData
     */
    public function getById(int $mandatoryFieldId): MandatoryFieldData
    {
        $sql =
            'SELECT
                `id`,
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
            FROM `mandatory_field` AS `m`
            WHERE m.id = :id
            LIMIT 1';

        $values = [
            'id' => $mandatoryFieldId,
        ];

        $data = $this->db->fetchRow($sql, $values);

        if (empty($data)) {
            throw new MandatoryFieldNotFoundException('No mandatory field record with the id:' . $mandatoryFieldId);
        }

        return MandatoryFieldData::fromDbState($data);
    }

    /**
     * @param string $customernumber
     *
     * @return bool
     */
    public function isAddressWithCustomerNumberActive(string $customerNumber): bool
    {
        $sql =
            'SELECT a.id 
            FROM `adresse` AS `a` 
            WHERE a.kundennummer = :customer_number 
            AND a.kundennummer != \'\' 
            AND a.geloescht != 1';

        $data = $this->db->fetchRow($sql, ['customer_number' => $customerNumber]);

        if (empty($data)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $employeeNumber
     *
     * @return bool
     */
    public function isAddressWithEmployeeNumberActive(string $employeeNumber): bool
    {
        $sql =
            'SELECT a.id 
            FROM `adresse` AS `a` 
            WHERE a.mitarbeiternummer = :employee_number 
            AND a.mitarbeiternummer != \'\' 
            AND a.geloescht != 1';

        $data = $this->db->fetchRow($sql, ['employee_number' => $employeeNumber]);

        if (empty($data)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $articleNumber
     *
     * @return bool
     */
    public function isArticleWithNumberActive(string $articleNumber): bool
    {
        $sql =
            'SELECT a.id 
            FROM `artikel` AS `a` 
            WHERE a.nummer = :article_number 
            AND a.nummer != \'\' 
            AND a.geloescht != 1 ';

        $data = $this->db->fetchRow($sql, ['article_number' => $articleNumber]);

        if (empty($data)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $documentNumber
     *
     * @return bool
     */
    public function isOrderWithDocumentNumberActive(string $documentNumber): bool
    {
        $sql =
            'SELECT a.id 
            FROM `auftrag` AS `a` 
            WHERE a.belegnr = :document_number';

        $data = $this->db->fetchRow($sql, ['document_number' => $documentNumber]);

        if (empty($data)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $documentNumber
     *
     * @return bool
     */
    public function isInvoiceWithDocumentNumberActive(string $documentNumber): bool
    {
        $sql =
            'SELECT r.id 
            FROM `rechnung` AS `r` 
            WHERE r.belegnr = :document_number';

        $data = $this->db->fetchRow($sql, ['document_number' => $documentNumber]);

        if (empty($data)) {
            return false;
        }

        return true;
    }
}
