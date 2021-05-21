<?php

declare(strict_types=1);

namespace Xentral\Modules\TimeManagement\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\TimeManagement\Exception\SupervisorNotFoundException;

final class GroupGateway
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
     * @return array
     */
    public function findGroupsByAddressId(int $addressId): array
    {
        $sql =
            'SELECT DISTINCT
            g.id AS `group_id`,
            g.name AS `group_name`
            FROM `gruppen` AS `g`
            INNER JOIN `adresse_rolle` AS `ar` ON ar.parameter = g.id AND ar.subjekt = \'Mitglied\'
            WHERE ar.adresse = :address_id
            AND (ar.bis = \'0000-00-00\' OR ar.bis > CURDATE())';

        return $this->db->fetchAll($sql, ['address_id' => $addressId]);
    }

    /**
     * @param int $addressId
     * @param int $groupId
     *
     * @return bool
     */
    public function isAddressInGroup(int $addressId, int $groupId): bool
    {
        $sql =
            'SELECT
            g.id AS `group_id`,
            g.name AS `group_name`
            FROM `gruppen` AS `g`
            INNER JOIN `adresse_rolle` AS `ar` ON ar.parameter = g.id AND ar.subjekt = \'Mitglied\'
            WHERE ar.adresse = :address_id
            AND ar.parameter = :group_id
            AND (ar.bis = \'0000-00-00\' OR ar.bis > CURDATE())';

        $result = $this->db->fetchAll($sql, ['address_id' => $addressId, 'group_id' => $groupId]);

        return !empty($result);
    }

    /**
     * @return array
     */
    public function findAllActiveGroupsWithMembers(): array
    {
        $sql =
            'SELECT DISTINCT
            g.id AS `group_id`,
            g.name AS `group_name`
            FROM `gruppen` AS `g`
            INNER JOIN `adresse_rolle` AS `ar` ON ar.parameter = g.id AND ar.subjekt = \'Mitglied\'
            WHERE (ar.bis = \'0000-00-00\' OR ar.bis > CURDATE())';

        return $this->db->fetchAll($sql);
    }

    /**
     * @param int $employeeAddressId
     * @param int $groupId
     *
     * @throws SupervisorNotFoundException
     *
     * @return int[]
     */
    public function getSupervisorAddressIds(int $employeeAddressId, int $groupId = 0): array
    {
        $bindValues = [];
        $sql =
            'SELECT a.id AS `id`
            FROM `userrights` AS `ur`
            INNER JOIN `user` AS `u` ON u.id = ur.user
            INNER JOIN `adresse` AS `a` ON a.id = u.adresse
            INNER JOIN `adresse_rolle` AS `ar_supervisor` ON ar_supervisor.adresse = a.id
            WHERE ur.action = \'timemanagementhandle\'
            AND ar_supervisor.subjekt = \'Mitglied\'
            AND ar_supervisor.objekt = \'Gruppe\'
            ';

        if ($groupId == 0) {
            $sql .= 'AND ar_supervisor.parameter IN (
                SELECT ar_employee.parameter 
                FROM `adresse_rolle` AS `ar_employee`
                WHERE ar_employee.adresse = :employee_address_id
                AND ar_employee.subjekt = \'Mitglied\'
                AND ar_employee.objekt = \'Gruppe\'
            )';
            $bindValues['employee_address_id'] = $employeeAddressId;
        } else {
            $sql .= 'AND ar_supervisor.parameter = :group_id';
            $bindValues['group_id'] = $groupId;
        }

        $superVisorAddressIds = $this->db->fetchAll($sql, $bindValues);

        //superprivilege
        if (empty($superVisorAddressIds)) {
            $superVisorAddressIds = $this->findSupervisorAddressIdsBySuperPrivilege();
        }

        //admin
        if (empty($superVisorAddressIds)) {
            $superVisorAddressIds = $this->findSupervisorAddressIdByAdminRight();
        }

        $return = [];
        if (!empty($superVisorAddressIds)) {
            foreach ($superVisorAddressIds as $superVisorAddressId) {
                $return[] = $superVisorAddressId['id'];
            }
        } else {
            throw new SupervisorNotFoundException('No supervisor found for ' . $employeeAddressId);
        }

        return $return;
    }

    /**
     * @return array
     */
    private function findSupervisorAddressIdsBySuperPrivilege()
    {
        $sql =
            'SELECT a.id AS `id`
                FROM `userrights` AS `ur`
                INNER JOIN `user` AS `u` ON u.id = ur.user
                INNER JOIN `adresse` AS `a` ON a.id = u.adresse
                WHERE ur.action = \'timemanagementsuperprivilege\'
                ORDER BY a.id DESC';

        return $this->db->fetchAll($sql);
    }

    /**
     * @return array
     */
    private function findSupervisorAddressIdByAdminRight()
    {
        $sql =
            'SELECT u.adresse  AS `id` 
                FROM `user` AS `u` 
                WHERE u.type = \'admin\'
                ORDER BY u.adresse DESC';

        return $this->db->fetchAll($sql);
    }
}
