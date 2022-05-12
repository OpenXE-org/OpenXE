<?php

declare(strict_types=1);

namespace Xentral\Modules\TimeManagement\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\TimeManagement\Data\WorkDayData;
use Xentral\Modules\TimeManagement\Exception\InvalidQueryException;

final class TimeManagementSettingGateway
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
     * @param int $addressId
     *
     * @throws InvalidQueryException
     *
     * @return WorkDayData
     */
    public function getWorkingDaysForAddress(int $addressId): WorkDayData
    {
        $sql =
            'SELECT
            e.vorlagemo > 0 AS `is_monday_workday`, 
            e.vorlagedi > 0 AS `is_tuesday_workday`, 
            e.vorlagemi > 0 AS `is_wednesday_workday`, 
            e.vorlagedo > 0 AS `is_thursday_workday`, 
            e.vorlagefr > 0 AS `is_friday_workday`, 
            e.vorlagesa > 0 AS `is_saturday_workday`, 
            e.vorlageso > 0 AS `is_sunday_workday`
            FROM `mitarbeiterzeiterfassung_einstellungen` AS `e`
            WHERE e.adresse = :address_id
            ORDER BY e.id DESC
            LIMIT 1';

        $result = $this->db->fetchRow($sql, ['address_id' => $addressId]);
        if (empty($result)) {
            throw new InvalidQueryException('Address is not valid: ' . $addressId);
        }

        return WorkDayData::fromDbState($result);
    }
}
