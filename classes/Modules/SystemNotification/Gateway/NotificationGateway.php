<?php

namespace Xentral\Modules\SystemNotification\Gateway;

use Xentral\Components\Database\Database;

final class NotificationGateway
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
     * @param int $userId
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function findByUserId($userId, $offset = 0, $limit = 500)
    {
        $sql = 'SELECT n.id, n.type, n.title, n.message, n.options_json, n.priority 
                FROM notification_message AS n 
                WHERE n.user_id = :user_id 
                ORDER BY n.created_at ASC
                LIMIT :offset, :limit';
        $result = $this->db->fetchAll($sql, [
            'user_id' => (int)$userId,
            'offset'  => (int)$offset,
            'limit'   => (int)$limit,
        ]);

        if (empty($result)) {
            return [];
        }

        return $result;
    }

    /**
     * @param int    $userId
     * @param string $title
     * @param string $message
     *
     * @return bool
     */
    public function hasDuplicatedMessage($userId, $title, $message)
    {
        $result = $this->db->fetchValue(
            'SELECT COUNT(n.id) AS num 
            FROM notification_message AS n 
            WHERE n.user_id = :user_id 
            AND n.title = :title 
            AND n.message = :message 
            LIMIT 1',
            [
                'user_id' => (int)$userId,
                'message' => $message,
                'title'   => $title,
            ]
        );

        return (int)$result > 0;
    }
}
