<?php

declare(strict_types=1);

namespace Xentral\Modules\TimeManagement\Service;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Modules\TimeManagement\Data\CalendarData;
use Xentral\Modules\TimeManagement\Data\DayInfoData;
use Xentral\Modules\TimeManagement\Data\RequestInfoData;
use Xentral\Modules\TimeManagement\Exception\InvalidDateFormatException;
use Xentral\Modules\TimeManagement\Exception\InvalidRequestTokenException;

final class TimeManagementTargetHourGateway
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
     * @param int $year
     * @param int $addressId
     * @param int $groupId
     *
     * @throws InvalidDateFormatException
     *
     * @return array|CalendarData[]
     */
    public function findAnonymisedVacationCalendarDataByYearAndAddressIdAndGroupId(
        int $year,
        int $addressId,
        int $groupId
    ): array {
        $sql =
            'SELECT 
                MONTH(days.date) AS `month`,
                days.date,
                days.address_id,
                days.name,
                days.type,
                IF(days.urlaubminuten > 0,true,false) AS `is_half`
            FROM
            (
                SELECT 
                    ms.datum AS `date`, 
                    ms.adresse AS `address_id`, 
                    a.name,
                    \'away\' AS `type`,
                    ms.urlaubminuten
                FROM `mitarbeiterzeiterfassung_sollstunden` AS `ms` 
                INNER JOIN `adresse` AS `a` ON ms.adresse = a.id
                WHERE (ms.kuerzel LIKE \'%U%\' OR ms.kuerzel LIKE \'%S%\')
                AND ms.adresse != :address_id  
                AND year(ms.datum) = :year
                AND ms.datum > CURDATE()
                UNION
                SELECT 
                    ms.datum AS `date`, 
                    ms.adresse AS `address_id`, 
                    a.name,
                    (CASE 
                        WHEN ms.kuerzel LIKE \'%U%\' THEN \'vacation\'
                        WHEN ms.kuerzel LIKE \'%R%\' THEN \'request-vacation\'
                        WHEN ms.kuerzel LIKE \'%L%\' THEN \'remove-vacation\'
                        WHEN ms.kuerzel LIKE \'%K%\' THEN \'sick\'
                        WHEN ms.kuerzel LIKE \'%S%\' THEN \'request-sick\'
                        WHEN ms.kuerzel LIKE \'%V%\' THEN \'remove-sick\'
                        WHEN ms.kuerzel LIKE \'%X%\' THEN \'absent\'
                        WHEN ms.kuerzel LIKE \'%N%\' THEN \'unpaid\'
                    END) AS `type`,
                    ms.urlaubminuten
                FROM `mitarbeiterzeiterfassung_sollstunden` AS `ms` 
                INNER JOIN `adresse` AS `a` ON ms.adresse = a.id
                WHERE ms.kuerzel != \'\' AND ms.kuerzel NOT LIKE \'%C%\' AND ms.kuerzel NOT LIKE \'%J%\'
                AND ms.adresse = :address_id
                AND year(ms.datum) = :year
            ) AS `days`
            INNER JOIN(
                SELECT DISTINCT
                    ar_groups.adresse
                FROM `adresse_rolle` AS `ar_groups`
                WHERE ar_groups.parameter = :group_id
                AND ar_groups.subjekt = :subject
                AND (ar_groups.bis = "0000-00-00" OR ar_groups.bis > CURDATE())
            ) AS `ar` ON ar.adresse = days.address_id
            ORDER BY days.address_id, days.date';

        $results = $this->db->fetchAll(
            $sql,
            [
                'year'       => $year,
                'group_id'   => $groupId,
                'subject'    => 'Mitglied',
                'address_id' => $addressId,
            ]
        );

        $calendarDatas = [];
        if (!empty($results)) {
            foreach ($results as $result) {
                $calendarDatas[] = CalendarData::fromDbState($result);
            }
        }

        return $calendarDatas;
    }

    /**
     * @param int $year
     * @param int $addressId
     *
     * @throws InvalidDateFormatException
     *
     * @return array|CalendarData[]
     */
    public function findAnonymisedVacationCalendarDataByYearAndAddressId(int $year, int $addressId): array
    {
        $sql =
            'SELECT 
                MONTH(days.date) AS `month`,
                days.date,
                days.address_id,
                days.name,
                days.type,
                IF(days.urlaubminuten > 0,true,false) AS `is_half`
            FROM
            (
                SELECT 
                    ms.datum AS `date`, 
                    ms.adresse AS `address_id`, 
                    a.name,
                    \'away\' AS `type`,
                    ms.urlaubminuten
                FROM `mitarbeiterzeiterfassung_sollstunden` AS `ms` 
                INNER JOIN `adresse` AS `a` ON ms.adresse = a.id
                WHERE (ms.kuerzel LIKE \'%U%\' OR ms.kuerzel LIKE \'%S%\')
                AND ms.adresse != :address_id  
                AND year(ms.datum) = :year
                AND ms.datum > CURDATE()
                UNION
                SELECT 
                    ms.datum AS `date`, 
                    ms.adresse AS `address_id`, 
                    a.name,
                    (CASE 
                        WHEN ms.kuerzel LIKE \'%U%\' THEN \'vacation\'
                        WHEN ms.kuerzel LIKE \'%R%\' THEN \'request-vacation\'
                        WHEN ms.kuerzel LIKE \'%L%\' THEN \'remove-vacation\'
                        WHEN ms.kuerzel LIKE \'%K%\' THEN \'sick\'
                        WHEN ms.kuerzel LIKE \'%S%\' THEN \'request-sick\'
                        WHEN ms.kuerzel LIKE \'%V%\' THEN \'remove-sick\'
                        WHEN ms.kuerzel LIKE \'%X%\' THEN \'absent\'
                        WHEN ms.kuerzel LIKE \'%N%\' THEN \'unpaid\'
                    END) AS `type`,
                    ms.urlaubminuten
                FROM `mitarbeiterzeiterfassung_sollstunden` AS `ms` 
                INNER JOIN `adresse` AS `a` ON ms.adresse = a.id
                WHERE ms.kuerzel != \'\' AND ms.kuerzel NOT LIKE \'%C%\' AND ms.kuerzel NOT LIKE \'%J%\'
                AND ms.adresse = :address_id
                AND year(ms.datum) = :year
            ) AS `days`
            WHERE days.address_id = :address_id
            ORDER BY days.address_id, days.date';

        $results = $this->db->fetchAll($sql, ['year' => $year, 'address_id' => $addressId]);

        $calendarDatas = [];
        if (!empty($results)) {
            foreach ($results as $result) {
                $calendarDatas[] = CalendarData::fromDbState($result);
            }
        }

        return $calendarDatas;
    }

    /**
     * @param int $year
     *
     * @throws InvalidDateFormatException
     *
     * @return array|CalendarData[]
     */
    public function findAllVacationCalendarDataByYear(int $year): array
    {
        $sql =
            'SELECT 
                MONTH(days.date) AS `month`,
                days.date,
                days.address_id,
                days.name,
                days.type,
                IF(days.urlaubminuten > 0,true,false) AS `is_half`
            FROM
            (
                SELECT 
                    ms.datum AS `date`, 
                    ms.adresse AS `address_id`, 
                    a.name,
                    (CASE 
                        WHEN ms.kuerzel LIKE \'%U%\' THEN \'vacation\'
                        WHEN ms.kuerzel LIKE \'%K%\' THEN \'sick\'
                        WHEN ms.kuerzel LIKE \'%X%\' THEN \'absent\'
                        WHEN ms.kuerzel LIKE \'%N%\' THEN \'unpaid\'
                    END) AS `type`,
                    ms.urlaubminuten
                FROM `mitarbeiterzeiterfassung_sollstunden` AS `ms` 
                INNER JOIN `adresse` AS `a` ON ms.adresse = a.id
                WHERE (
                    ms.kuerzel LIKE \'%U%\'
                    OR ms.kuerzel LIKE \'%N%\'
                    OR ms.kuerzel LIKE \'%K%\'
                    OR ms.kuerzel LIKE \'%X%\'
                )
                AND year(ms.datum) = :year
            ) AS `days`
            INNER JOIN(
                SELECT DISTINCT
                    ar_groups.adresse
                FROM `adresse_rolle` AS `ar_groups`
                WHERE ar_groups.subjekt = :subject
                AND (ar_groups.bis = \'0000-00-00\' OR ar_groups.bis > CURDATE())
            ) AS `ar` ON ar.adresse = days.address_id
            ORDER BY days.date, days.address_id';

        $results = $this->db->fetchAll($sql, ['year' => $year, 'subject' => 'Mitarbeiter']);

        $calendarDatas = [];
        if (!empty($results)) {
            foreach ($results as $result) {
                $calendarDatas[] = CalendarData::fromDbState($result);
            }
        }

        return $calendarDatas;
    }

    /**
     * @param int               $addressId
     * @param DateTimeInterface $date
     *
     * @return DayInfoData
     */
    public function findDayInfo(int $addressId, DateTimeInterface $date): DayInfoData
    {
        $sql =
            'SELECT 
            ms.kuerzel AS `type`, 
            ms.internal_comment AS `internal_comment`,
            (CASE WEEKDAY(:date)
                WHEN 0 THEN IFNULL(minutes.vorlagemo,0)
                WHEN 1 THEN IFNULL(minutes.vorlagedi,0)
                WHEN 2 THEN IFNULL(minutes.vorlagemi,0)
                WHEN 3 THEN IFNULL(minutes.vorlagedo,0)
                WHEN 4 THEN IFNULL(minutes.vorlagefr,0)
                WHEN 5 THEN IFNULL(minutes.vorlagesa,0)
                WHEN 6 THEN IFNULL(minutes.vorlageso,0)
            END) AS `workminutes`,
            ms.urlaubminuten AS `vacationminutes`
            FROM (
                SELECT 
                me.adresse,
                me.vorlagemo, 
                me.vorlagedi, 
                me.vorlagemi, 
                me.vorlagedo, 
                me.vorlagefr, 
                me.vorlagesa, 
                me.vorlageso  
                FROM `mitarbeiterzeiterfassung_einstellungen` AS `me`
                WHERE me.adresse = :address_id
                ORDER BY id DESC
                LIMIT 1
            ) AS `minutes`
            LEFT JOIN `mitarbeiterzeiterfassung_sollstunden` AS `ms` ON minutes.adresse = ms.adresse AND ms.datum = :date
            LIMIT 1';

        $result = $this->db->fetchRow($sql, ['date' => $date->format('Y-m-d'), 'address_id' => $addressId]);

        return DayInfoData::fromDbState($result);
    }

    /**
     * @param string $requestToken
     *
     * @throws InvalidRequestTokenException
     * @throws InvalidDateFormatException
     *
     * @return RequestInfoData
     */
    public function getRequestInfoByToken(string $requestToken): RequestInfoData
    {
        $sql =
            'SELECT 
                a.id AS `employee_id`,
                a.mitarbeiternummer AS `employee_number`,
                a.name AS `employee_name`,
                DATE_FORMAT(MIN(ms.datum), \'%d.%m.%Y\') AS `min_date`,
                DATE_FORMAT(MAX(ms.datum), \'%d.%m.%Y\') AS `max_date`,
                COUNT(ms.id) AS `amount`,
                ms.kommentar AS `comment`,
                ms.kuerzel AS `type`,
                ms.internal_comment
            FROM `mitarbeiterzeiterfassung_sollstunden` AS `ms`
            INNER JOIN `adresse` AS `a` ON a.id = ms.adresse
            WHERE ms.vacation_request_token = :request_token
            AND (
                ms.kuerzel LIKE \'%R%\' 
                OR ms.kuerzel LIKE \'%L%\'
                OR ms.kuerzel LIKE \'%S%\'
                OR ms.kuerzel LIKE \'%V%\'
            )
            GROUP BY ms.vacation_request_token
            ORDER BY ms.datum';

        $result = $this->db->fetchRow($sql, ['request_token' => $requestToken]);

        if (empty($result)) {
            throw new InvalidRequestTokenException($requestToken . 'not valid.');
        }

        return RequestInfoData::fromDbState($result);
    }

    /**
     * @param string $requestToken
     *
     * @throws InvalidDateFormatException
     *
     * @throws InvalidRequestTokenException
     * @return array
     */
    public function getRequestedDaysByToken(string $requestToken): array
    {
        $sql =
            'SELECT 
                ms.datum AS `date`, 
                ms.kuerzel AS `type`
            FROM `mitarbeiterzeiterfassung_sollstunden` AS `ms` 
            WHERE ms.vacation_request_token = :vacation_request_token
            ORDER BY ms.datum';

        $results = $this->db->fetchAll($sql, ['vacation_request_token' => $requestToken]);

        if (empty($results)) {
            throw new InvalidRequestTokenException($requestToken . 'not valid.');
        }

        $formatted = [];
        foreach ($results as $result) {
            try {
                $formatted[] = ['date' => new DateTimeImmutable($result['date']), 'type' => $result['type']];
            } catch (Exception $e) {
                throw new InvalidDateFormatException('Could not convert date: ' . $result['date']);
            }
        }

        return $formatted;
    }

    /**
     * @param int $daysTillDeletion
     * @param int $addressId
     *
     * @throws InvalidDateFormatException
     *
     * @return array
     */
    public function findRejectedDays(int $daysTillDeletion, int $addressId): array
    {
        $sql =
            'SELECT 
            ms.datum AS `date`
            FROM `mitarbeiterzeiterfassung_sollstunden` AS `ms` 
            WHERE (ms.kuerzel LIKE \'%J%\' OR ms.kuerzel LIKE \'%C%\')
            AND ms.adresse = :address_id
            AND DATE_ADD(FROM_UNIXTIME(ms.vacation_request_token), INTERVAL :days_till_deletion DAY) < CURDATE()';

        $results = $this->db->fetchAll(
            $sql,
            [
                'days_till_deletion' => $daysTillDeletion,
                'address_id'         => $addressId,
            ]
        );

        $formatted = [];
        if (!empty($results)) {
            foreach ($results as $result) {
                try {
                    $formatted[] = ['date' => new DateTimeImmutable($result['date'])];
                } catch (Exception $e) {
                    throw new InvalidDateFormatException('Could not convert date: ' . $result['date']);
                }
            }
        }

        return $formatted;
    }

    /**
     * @param int $addressId
     *
     * @return float
     */
    public function findAmountRequestedVacation(int $addressId): float
    {
        $sql =
            'SELECT SUM(info.amount) AS `amount`
            FROM( 
                SELECT
                count(ms.id) AS `amount`
                FROM `mitarbeiterzeiterfassung_sollstunden` AS `ms` 
                WHERE ms.adresse = :address_id
                AND ms.urlaubminuten = 0
                AND ms.kuerzel LIKE \'%R%\'
                UNION
                SELECT
                count(ms.id) / 2 AS `amount`
                FROM `mitarbeiterzeiterfassung_sollstunden` AS `ms` 
                WHERE ms.adresse = :address_id
                AND ms.urlaubminuten > 0
                AND ms.kuerzel LIKE \'%R%\'
            ) AS `info`';

        $result = $this->db->fetchRow($sql, ['address_id' => $addressId]);

        if (!empty($result)) {
            return (float)$result['amount'];
        }

        return 0;
    }
}
