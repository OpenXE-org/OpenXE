<?php

declare(strict_types=1);

namespace Xentral\Modules\TimeManagement\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\TimeManagement\Data\HolidayData;
use Xentral\Modules\TimeManagement\Exception\InvalidDateFormatException;

final class HolidayGateway
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
     * @param $year
     *
     * @throws InvalidDateFormatException
     *
     * @return array|HolidayData[]
     */
    public function findHolidayDataByYear(int $year): array
    {
        $sql =
            'SELECT 
                af.bezeichnung as `name`,
                af.datum as `date`
            FROM `arbeitsfreietage` AS `af`
            WHERE af.datum >= :first_date_of_year
            AND af.typ = \'feiertag\'
            ORDER BY af.datum';

        $holidays = $this->db->fetchAll($sql, ['first_date_of_year' => $year . '-01-01']);

        $holidayData = [];
        if (!empty($holidays)) {
            foreach ($holidays as $holiday) {
                $holidayData[] = HolidayData::fromDbState($holiday);
            }
        }

        return $holidayData;
    }
}
