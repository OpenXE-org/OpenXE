<?php

namespace Xentral\Modules\SystemHealth\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\SystemHealth\Exception\InvalidArgumentException;
use Xentral\Modules\SystemHealth\Exception\RuntimeException;
use Xentral\Modules\SystemHealth\Gateway\SystemHealthGateway;
use Xentral\Modules\SystemNotification\Service\NotificationService;

final class SystemHealthService implements SystemHealthServiceInterface
{

    /** @var Database $db */
    private $db;

    /** @var SystemHealthGateway $gateway */
    private $gateway;

    /** @var NotificationService $notficationService */
    private $notficationService;

    /**
     * @param Database            $database
     * @param SystemHealthGateway $gateway
     * @param NotificationService $notificationService
     */
    public function __construct(
        Database $database,
        SystemHealthGateway $gateway,
        NotificationService $notificationService
    ) {
        $this->db = $database;
        $this->gateway = $gateway;
        $this->notficationService = $notificationService;
    }

    /**
     * Create a SystemHealth
     *
     * @param int    $systemHealthCategoryId
     * @param string $name
     * @param string $description
     * @param string $status
     * @param bool   $resetAble
     *
     * @throws InvalidArgumentException|RuntimeException
     *
     * @return int|false Created SystemHealth-ID
     */
    public function create(
        $systemHealthCategoryId,
        $name,
        $description = '',
        $status = '',
        $resetAble = false
    ) {
        if (empty($name)) {
            throw new RuntimeException('name must not empty.');
        }

        if (!$this->gateway->getCategoryById($systemHealthCategoryId)) {
            throw new RuntimeException('Category not found.');
        }

        if ($this->db->fetchValue(
            'SELECT `id` 
            FROM `systemhealth` 
            WHERE `name` = :name AND `systemhealth_category_id` = :systemhealth_category_id',
            [
                'name'                     => $name,
                'systemhealth_category_id' => $systemHealthCategoryId,
            ]
        )) {
            throw new RuntimeException(sprintf('name %s allready exists.', $name));
        }

        $this->db->perform(
            'INSERT INTO `systemhealth` 
            (`id`, `systemhealth_category_id`, `name`, `description`, `status`, `created_at`, `resetable`)
            VALUES (NULL, :systemhealth_category_id, :name, :description, :status, NOW(), :resetable)',
            [
                'systemhealth_category_id' => $systemHealthCategoryId,
                'name'                     => $name,
                'description'              => $description,
                'status'                   => $status,
                'resetable'                => $resetAble,
            ]
        );
        $insertId = (int)$this->db->lastInsertId();
        if ($insertId === 0) {
            throw new RuntimeException('Notification message could not be created.');
        }

        return $insertId;
    }

    /**
     * @param int    $systemHealthId
     * @param string $description
     * @param string $status
     * @param bool   $resetAble
     */
    public function update($systemHealthId, $description, $status, $resetAble)
    {
        $systemHealth = $this->gateway->getById($systemHealthId);
        if (empty($systemHealth)) {
            throw new RuntimeException('SystemHealthentry not found.');
        }

        if (!empty($status) && $status !== 'ok') {
            $customStatus = (string)$this->gateway->getCustomStatusLvlFromId($systemHealthId);
            if (!empty($customStatus)) {
                $status = $customStatus;
            }
        }

        $this->db->perform(
            'UPDATE `systemhealth` 
            SET `description` = :description, `status` = :status, `resetable` = :resetable
            WHERE `id` = :systemhealth_id',
            [
                'description'     => $description,
                'status'          => $status,
                'resetable'       => $resetAble,
                'systemhealth_id' => $systemHealthId,
            ]
        );
    }

    /**
     * @param int $systemHealthId
     * @param int $userId
     */
    public function deleteSystemHealthItemNotificationSetting($systemHealthId, $userId = 0)
    {
        $systemHealth = $this->gateway->getById($systemHealthId);
        if (empty($systemHealth)) {
            throw new RuntimeException('SystemHealthentry not found.');
        }
        $notificationItem = $this->gateway->getItemNotificationByUserIdSystemHealthId($systemHealthId, $userId);
        if (empty($notificationItem)) {
            return;
        }
        $this->db->perform(
            'DELETE FROM `systemhealth_notification_item` WHERE `id` = :id',
            ['id' => $notificationItem['id']]
        );
    }

    /**
     * @param int $systemHealthId
     * @param int $userId
     */
    public function createSystemHealthItemNotificationSetting($systemHealthId, $userId = 0)
    {
        $systemHealth = $this->gateway->getById($systemHealthId);
        if (empty($systemHealth)) {
            throw new RuntimeException('SystemHealthentry not found.');
        }
        $systemHealthStatus = !empty($systemHealth['custom_status']) ?
            $systemHealth['custom_status'] :
            $systemHealth['status'];
        $notificationItem = $this->gateway->getItemNotificationByUserIdSystemHealthId($systemHealthId, $userId);
        if (empty($notificationItem)) {
            $this->db->perform(
                'INSERT INTO `systemhealth_notification_item` (`systemhealth_id`, `user_id`, `status`) 
                VALUES (:systemhealth_id, :user_id, :status)',
                [
                    'systemhealth_id' => (int)$systemHealthId,
                    'user_id'         => (int)$userId,
                    'status'          => $systemHealthStatus,
                ]
            );

            $insertId = (int)$this->db->lastInsertId();
            if ($insertId === 0) {
                throw new RuntimeException('SystemHealthCategory could not be created.');
            }

            return;
        }
        if ($systemHealthStatus === $notificationItem['status']) {
            return;
        }
        $this->db->perform(
            'UPDATE `systemhealth_notification_item` SET `status` = :status WHERE `id` = :id',
            ['status' => $systemHealthStatus, 'id' => $notificationItem['id']]
        );
    }

    /**
     * @param int $systemHealthId
     */
    public function resetStatus($systemHealthId)
    {
        $systemHealth = $this->gateway->getById($systemHealthId);
        if (empty($systemHealth)) {
            throw new RuntimeException('SystemHealthentry not found.');
        }
        if (empty($systemHealth['resetable'])) {
            throw new RuntimeException('SystemHealthentry is not resetable.');
        }
        $this->db->fetchAffected(
            "UPDATE `systemhealth` 
            SET `status` = '',
                `message` = '',
                `lastupdate` = NOW(), 
                `last_reset` = NOW()
            WHERE `id` = :id LIMIT 1",
            [
                'id' => (int)$systemHealthId,
            ]
        );
    }

    /**
     * @param int    $systemHealthId
     * @param string $status
     * @param string $message
     *
     * @throws RuntimeException
     * @return bool
     *
     */
    public function setStatus($systemHealthId, $status, $message = '')
    {
        $systemHealth = $this->gateway->getById($systemHealthId);
        if (empty($systemHealth)) {
            throw new RuntimeException('SystemHealthentry not found.');
        }

        if (!empty($status) && $status !== 'ok') {
            $customStatus = (string)$this->gateway->getCustomStatusLvlFromId($systemHealthId);
            if (!empty($customStatus)) {
                $status = $customStatus;
            }
        }

        $numRows = (int)$this->db->fetchAffected(
            'UPDATE `systemhealth` 
            SET `status` = :status,
                `message` = :message, 
                `lastupdate` = NOW()
            WHERE `id` = :id LIMIT 1',
            [
                'id'      => (int)$systemHealthId,
                'status'  => $status,
                'message' => $message,
            ]
        );

        $return = $numRows === 1;
        if (!$return) {
            return false;
        }

        if ($status === $systemHealth['status']) {
            return true;
        }

        $notifications = $this->gateway->getUserNotificationsByIdAndStatus($systemHealthId, $status);

        if (empty($notifications)) {
            return true;
        }

        foreach ($notifications as $notification) {
            $this->notficationService->create(
                $notification['user_id'],
                $status,
                $systemHealth['description'],
                strip_tags($message),
                $status === 'error',
                [],
                ['systemhealth', $notification['name']]
            );
        }

        return true;
    }

    /**
     * Create a SystemHealthCategory
     *
     * @param string $name
     * @param string $description
     *
     * @throws InvalidArgumentException|RuntimeException
     *
     * @return int|false Created SystemHealthCategory-ID
     */
    public function createCategory($name, $description = '')
    {
        if (empty($name)) {
            throw new RuntimeException('name must not empty.');
        }

        if ($this->db->fetchValue(
            'SELECT sc.id FROM `systemhealth_category` AS `sc` WHERE sc.`name` = :name',
            ['name' => (string)$name]
        )) {
            throw new RuntimeException(sprintf('name %s allready exists.', $name));
        }

        $this->db->perform(
            'INSERT INTO `systemhealth_category` (`id`, `name`, `description`, `created_at`)
            VALUES (NULL, :name, :description,  NOW())',
            [
                'name'        => (string)$name,
                'description' => $description,
            ]
        );
        $insertId = (int)$this->db->lastInsertId();
        if ($insertId === 0) {
            throw new RuntimeException('SystemHealthCategory could not be created.');
        }

        return $insertId;
    }

    /**
     * @param int $systemHealthId
     *
     * @return bool
     */
    public function delete($systemHealthId)
    {
        $numRows = (int)$this->db->fetchAffected(
            'DELETE FROM `systemhealth` WHERE `id` = :id LIMIT 1',
            ['id' => (int)$systemHealthId]
        );

        return $numRows === 1;
    }

    /**
     * @param int $SystemHealthCategoryId
     *
     * @return bool
     */
    public function deleteCategory($SystemHealthCategoryId)
    {
        if ($this->db->fetchValue(
            'SELECT sh.id FROM `systemhealth` AS `sh` WHERE sh.systemhealth_category_id = :id LIMIT 1',
            ['id' => (int)$SystemHealthCategoryId]
        )) {
            throw new RuntimeException('Category is not empty.');
        }

        $numRows = (int)$this->db->fetchAffected(
            'DELETE FROM `systemhealth_category` WHERE `id` = :id LIMIT 1',
            ['id' => (int)$SystemHealthCategoryId]
        );

        return $numRows === 1;
    }

    /**
     * @param int    $systemHealthId
     * @param string $status
     * @param string $doctype
     * @param int    $doctypeId
     * @param string $message
     *
     * @return int
     */
    public function createEvent($systemHealthId, $status, $doctype, $doctypeId, $message = '')
    {
        $systemHealth = $this->gateway->getById($systemHealthId);
        if (empty($systemHealth)) {
            throw new RuntimeException('Item is not empty.');
        }

        $this->db->perform(
            'INSERT INTO `systemhealth_event` 
            (`id`, `systemhealth_id`, `created_at`, `doctype`, `doctype_id`, `status`, `message`)
            VALUES (NULL, :systemhealth_id, NOW(), :doctype, :doctype_id, :status, :message)',
            [
                'systemhealth_id' => (int)$systemHealthId,
                'doctype'         => $doctype,
                'doctype_id'      => (int)$doctypeId,
                'status'          => $status,
                'message'         => (string)$message,
            ]
        );
        $insertId = (int)$this->db->lastInsertId();
        if ($insertId === 0) {
            throw new RuntimeException('SystemHealthEvent could not be created.');
        }

        return $insertId;
    }


    /**
     * @param string $dir
     *
     * @return false|float
     */
    public function getDiskFree($dir = '')
    {
        if (!function_exists('disk_free_space')) {
            throw new RuntimeException('function disk_free_space not found');
        }

        if (empty($dir)) {
            return disk_free_space(dirname(__DIR__, 4));
        }

        if (!is_dir($dir)) {
            throw new InvalidArgumentException(sprintf('%s is no valid directory', $dir));
        }

        return disk_free_space($dir);
    }

    /**
     * @param string $database
     *
     * @return array
     */
    public function getTableSizes($database)
    {
        if (!is_string($database) || empty($database) || strpos($database, '`')) {
            throw new InvalidArgumentException(sprintf('%s is no valid database', $database));
        }

        return $this->db->fetchPairs(
            'SELECT table_name AS `Table`, ROUND((data_length + index_length) / 1024) AS `KB`
            FROM information_schema.TABLES 
            WHERE table_schema = :database AND NOT ISNULL(data_length)
            ORDER BY data_length + index_length DESC',
            ['database' => $database]
        );
    }

    /**
     * @param string $database
     *
     * @return int
     */
    public function getDbSize($database)
    {
        $sizes = $this->getTableSizes($database);
        $ret = 0;
        foreach ($sizes as $size) {
            $ret += (int)$size;
        }

        return $ret;
    }

    /**
     * @return array
     */
    public function getSystemLoad()
    {
        if (!function_exists('exec')) {
            throw new RuntimeException('function exec not found');
        }
        @exec('cat /proc/loadavg 2>/dev/null ', $output, $return);

        if (empty($output[0])) {
            return [null, null, null, null, null];
        }
        $output = reset($output);
        while (strpos($output, '  ') !== false) {
            $output = str_replace('  ', ' ', $output);
        }
        $output = explode(' ', $output);
        $runable = null;
        $threads = null;
        if (!empty($output[3]) && strpos($output[3], '/') !== false) {
            [$runable, $threads] = explode('/', $output[3]);
        }

        return [
            'act'     => $output[0],
            '5min'    => isset($output[1]) ? $output[1] : null,
            '15min'   => isset($output[2]) ? $output[2] : null,
            'runable' => $runable,
            'threads' => $threads,
        ];
    }

    /**
     * @return array
     */
    public function getMemoryUsage()
    {
        if (!function_exists('exec')) {
            throw new RuntimeException('function exec not found');
        }
        @exec('free -t -w 2>/dev/null ', $output, $return);
        $ret = [];
        if (empty($output[1])) {
            return [];
        }

        foreach ($output as $key => $row) {
            if ($key > 0) {
                $pos = strpos($row, ':');
                $name = substr($row, 0, $pos);
                $row = substr($row, $pos + 1);
                while (strpos($row, '  ') !== false) {
                    $row = str_replace('  ', ' ', $row);
                }
                $mems = explode(' ', trim($row));

                if ($key === 1) {
                    $type = 'memory';
                } elseif ($key === count($output) - 1) {
                    $type = 'sum';
                } elseif (count($output) === 4) {
                    $type = 'swap';
                } else {
                    continue;
                }
                $ret[$type] = [
                    'name' => $name,
                    'sum'  => $mems[0],
                    'used' => empty($mems[1]) ? 0 : $mems[1],
                    'free' => empty($mems[2]) ? 0 : $mems[2],
                ];
            }
        }

        return $ret;
    }

    /**
     * @param string $dir
     *
     * @return array
     */
    public function getPartions($dir = '')
    {
        if (!function_exists('exec')) {
            throw new RuntimeException('function exec not found');
        }
        $command = 'df -B1024 -text4 -T --total 2>/dev/null';
        @exec($command, $out, $return);
        $ret = [];
        $total = [];
        $matches = [];
        if (!empty($out) && count($out) > 1) {
            $totalKey = count($out) - 1;
            unset($out[0]);
            foreach ($out as $key => $line) {
                $line = str_replace(["\r", "\t", '  '], ' ', $line);
                while (strpos($line, '  ') !== false) {
                    $line = str_replace('  ', ' ', $line);
                }
                $cols = explode(' ', $line);
                if (count($cols) < 7) {
                    continue;
                }
                $mountPoint = $cols[6];
                if ($key === $totalKey) {
                    $total = ['total' => $cols[2], 'used' => $cols[3], 'free' => $cols[4]];
                }

                if ($dir !== '' && strpos($dir, $mountPoint) !== 0) {
                    unset($out[$key]);
                    continue;
                }

                $ret[] = ['total' => $cols[2], 'used' => $cols[3], 'free' => $cols[4], 'mount' => $cols[6]];
                $matches[] = strlen($cols[6]);
            }
        }

        if (empty($ret)) {
            return $total;
        }
        if (count($ret) === 1) {
            return reset($ret);
        }

        array_multisort($matches, SORT_DESC, $ret);

        return reset($ret);
    }

    /**
     * @param string   $dir
     * @param string[] $excludeDirs
     *
     * @return int
     */
    public function getUsedSpace($dir, $excludeDirs = [])
    {
        if (empty($dir) || !is_dir($dir)) {
            throw new InvalidArgumentException(sprintf('%s is no valid directory', $dir));
        }

        if (!function_exists('exec')) {
            throw new RuntimeException('function exec not found');
        }

        if (!empty($excludeDirs)) {
            foreach ($excludeDirs as $key => $excludeDir) {
                $excludeDir = trim($excludeDir);
                if (strpos($excludeDir, '..') !== false
                    || strpos($excludeDir, "\n") !== false
                    || strpos($excludeDir, "\r") !== false
                    || strpos($excludeDir, "\t") !== false
                    || strpos($excludeDir, ' ') !== false
                ) {
                    unset($excludeDirs[$key]);
                } else {
                    $excludeDirs[$key] = $excludeDir;
                }
            }
        }

        if (empty($excludeDirs)) {
            $command = sprintf(
                'cd %s && du -d0  --block-size=1024 2>/dev/null',
                $dir
            );
        } else {
            $command = sprintf(
                'cd %s && du -d0  --block-size=1024 --exclude=%s 2>/dev/null',
                $dir,
                implode(' --exclude=', $excludeDirs)
            );
        }


        $userdata = @exec($command, $out, $return);
        if (empty($userdata)) {
            throw new RuntimeException('failed to get Space');
        }

        $pos = strpos($userdata, '.');
        if ($pos === false) {
            $pos = strpos($userdata, 'total');
        }
        if ($pos === false) {
            $pos = strpos($userdata, 'insgesamt');
        }
        if ($pos === false && !empty($out)) {
            $userdata = trim(reset($out));
            if (is_numeric($userdata)) {
                $pos = strlen($userdata);
            }
        }

        if ((int)$pos <= 0) {
            throw new RuntimeException('failed to get Space');
        }

        return (int)trim(substr($userdata, 0, $pos));
    }
}
