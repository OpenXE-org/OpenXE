<?php

namespace Xentral\Modules\SystemHealth\Gateway;

use Xentral\Components\Database\Database;

final class SystemHealthGateway
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param $systemHealthId
     *
     * @return array
     */
    public function getById($systemHealthId)
    {
        $sql = 'SELECT sh.*, scel.status AS custom_status 
                FROM `systemhealth` AS `sh` 
                LEFT JOIN `systemhealth_custom_error_lvl` AS `scel` ON sh.id = scel.systemhealth_id 
                WHERE sh.id = :systemhealth_id
                LIMIT 1';

        return $this->db->fetchRow($sql, [
            'systemhealth_id' => $systemHealthId,
        ]);
    }

    /**
     * @param int $systemHealthCategoryId
     *
     * @return array
     */
    public function getCategoryById($systemHealthCategoryId)
    {
        $sql = 'SELECT sh.* 
                FROM `systemhealth_category` AS `sh` 
                WHERE sh.id = :systemhealth_category_id
                LIMIT 1';

        return $this->db->fetchRow($sql, [
            'systemhealth_category_id' => $systemHealthCategoryId,
        ]);
    }

    /**
     * @param int $systemHealthId
     *
     * @return int|float|string|false false on empty result
     */
    public function getCustomStatusLvlFromId($systemHealthId)
    {
        $sql = 'SELECT scel.status 
            FROM `systemhealth_custom_error_lvl` AS `scel` 
            WHERE scel.systemhealth_id = :systemhealth_id 
            LIMIT 1';

        return $this->db->fetchValue($sql, ['systemhealth_id' => $systemHealthId]);
    }

    /**
     * @param int    $systemHealthId
     * @param string $status
     *
     * @return array
     */
    public function getUserNotificationsByIdAndStatus($systemHealthId, $status)
    {
        if ($status !== 'warning' && $status !== 'error') {
            return [];
        }
        if ($status === 'warning') {
            $sql = "SELECT sni.user_id, sh.name, sh.description 
            FROM `systemhealth_notification_item` AS `sni`
            INNER JOIN `systemhealth` AS `sh` ON sni.systemhealth_id = sh.id
            WHERE sni.systemhealth_id = :systemhealth_id
            AND sni.status = 'warning'";
        } else {
            $sql = "SELECT sni.user_id, sh.name, sh.description
            FROM `systemhealth_notification_item` AS `sni` 
            INNER JOIN `systemhealth` AS `sh` ON sni.systemhealth_id = sh.id
            WHERE sni.systemhealth_id = :systemhealth_id
            AND (sni.status = 'warning' OR sni.status = 'error')";
        }

        return $this->db->fetchAll($sql, ['systemhealth_id' => $systemHealthId]);
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getCategoryByName($name)
    {
        $sql = 'SELECT shc.* 
                  FROM `systemhealth_category` AS `shc` 
                  WHERE shc.name = :name
                  LIMIT 1';

        return $this->db->fetchRow($sql, [
            'name' => $name,
        ]);
    }

    /**
     * @param int    $systemHealthCategoryId
     * @param string $name
     *
     * @return array
     */
    public function getByName($systemHealthCategoryId, $name)
    {
        $sql = 'SELECT sh.* 
               FROM `systemhealth` AS `sh`
               WHERE sh.`name` = :name AND sh.systemhealth_category_id = :systemhealth_category_id
               LIMIT 1';

        return $this->db->fetchRow($sql, [
            'name'                     => $name,
            'systemhealth_category_id' => $systemHealthCategoryId,
        ]);
    }

    /**
     * @param int $systemHealthCategoryId
     *
     * @return array
     */
    public function getEntriesByCategoryId($systemHealthCategoryId)
    {
        return $this->db->fetchAll(
            'SELECT sh.*, scel.status AS custom_status
            FROM `systemhealth` AS `sh`
            LEFT JOIN `systemhealth_custom_error_lvl` AS `scel` ON sh.id = scel.systemhealth_id 
            WHERE sh.systemhealth_category_id = :systemhealth_category_id
            ORDER BY sh.name',
            ['systemhealth_category_id' => $systemHealthCategoryId]
        );
    }

    /**
     * @param int $userId
     * @param int $systemHealthCategoryId
     *
     * @return array
     */
    public function getItemNoticiationsByUserId($userId, $systemHealthCategoryId = 0)
    {
        return $this->db->fetchGroup(
            'SELECT sni.systemhealth_id, sni.status, sni.email 
            FROM `systemhealth_notification_item` AS `sni`
            INNER JOIN `systemhealth` AS `sh` ON sni.systemhealth_id = sh.id 
              AND (systemhealth_category_id = :systemhealth_category_id OR :systemhealth_category_id = 0) 
            WHERE sni.user_id = :user_id
            ORDER BY sni.user_id DESC',
            [
                'user_id'                  => (int)$userId,
                'systemhealth_category_id' => (int)$systemHealthCategoryId,
            ]
        );
    }

    /**
     * @param int $systemHealthId
     * @param int $userId
     *
     * @return array
     */
    public function getItemNotificationByUserIdSystemHealthId($systemHealthId, $userId = 0)
    {
        return $this->db->fetchRow(
            'SELECT sni.* 
            FROM `systemhealth_notification_item` AS `sni` 
            WHERE sni.systemhealth_id = :systemhealth_id AND sni.user_id = :user_id
            LIMIT 1',
            ['systemhealth_id' => (int)$systemHealthId, 'user_id' => (int)$userId]
        );
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->db->fetchAll(
            'SELECT shc.* 
            FROM `systemhealth_category` AS `shc`
            ORDER BY shc.name'
        );
    }

    /**
     * @param int $systemCategorieId
     *
     * @return array
     */
    public function getStatusCount($systemCategorieId = 0)
    {
        if ($systemCategorieId > 0) {
            return $this->db->fetchPairs(
                'SELECT sh.status, COUNT(sh.id) 
                FROM `systemhealth` AS `sh` 
                WHERE sh.systemhealth_category_id = :systemhealth_category_id
                GROUP BY sh.status',
                ['systemhealth_category_id' => $systemCategorieId]
            );
        }

        return $this->db->fetchPairs(
            'SELECT sh.status, COUNT(sh.id) 
                FROM `systemhealth` AS `sh` 
                GROUP BY sh.status'
        );
    }
}
