<?php

namespace Xentral\Modules\SystemNotification\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\SystemNotification\Exception\InvalidArgumentException;
use Xentral\Modules\SystemNotification\Exception\RuntimeException;

final class NotificationService implements NotificationServiceInterface
{
    /** @var array $validMessageTypes */
    private static $validMessageTypes = [
        self::TYPE_DEFAULT,
        self::TYPE_NOTICE,
        self::TYPE_SUCESS,
        self::TYPE_WARNING,
        self::TYPE_ERROR,
        self::TYPE_PUSH,
    ];

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
     * Create a notification
     *
     * @param int         $userId
     * @param string      $type
     * @param string      $title
     * @param string|null $message
     * @param bool        $priority Play sound and make notification sticky
     * @param array       $options
     * @param array       $tags
     *
     * @throws InvalidArgumentException|RuntimeException
     *
     * @return int|false Created Notification-ID
     */
    public function create(
        $userId,
        $type,
        $title,
        $message = null,
        $priority = false,
        $options = [],
        $tags = []
    ) {
        if (!in_array($type, self::$validMessageTypes, true)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is not a valid message type. Valid types are: %s', $type, implode(', ', self::$validMessageTypes)
            ));
        }
        if (!$this->isValidUser($userId)) {
            throw new InvalidArgumentException(sprintf(
                'User #%s is not a valid user.', $userId
            ));
        }

        // Truncate long messages
        $message = $this->truncateMessage($message);

        // Sanitize buttons
        if (is_array($options['buttons']) && count($options['buttons']) > 0) {
            foreach ($options['buttons'] as $index => $button) {
                if (empty($button['text']) || empty($button['link'])) {
                    unset($options['buttons'][$index]);
                }
                if (empty($button['id'])) {
                    $options['buttons'][$index]['id'] = uniqid('notification-button-', false); // Set Html-Id attribute
                }
            }
        }

        // Create notification
        $this->db->perform(
            'INSERT INTO `notification_message` (`user_id`, `type`, `title`, `message`, `options_json`, `tags`, `priority`, `created_at`)
            VALUES (:user_id, :type, :title, :message, :options_json, :tags, :priority, NOW())',
            [
                'user_id'      => (int)$userId,
                'type'         => $type,
                'title'        => $title,
                'message'      => $message,
                'priority'     => (int)$priority,
                'options_json' => !empty($options) ? json_encode($options) : null,
                'tags'         => !empty($tags) ? $this->transformTagsArrayToString($tags) : null,
            ]
        );
        $insertId = (int)$this->db->lastInsertId();
        if ($insertId === 0) {
            throw new RuntimeException('Notification message could not be created.');
        }

        return $insertId;
    }

    /**
     * Create push notification
     *
     * @param int    $userId
     * @param string $title
     * @param string $message
     * @param bool   $priority
     *
     * @return int Created ID
     */
    public function createPushNotification($userId, $title, $message, $priority = false)
    {
        // strip_tags ist notwendig, da HTML von Browser-Benachrichtigungen nicht unterstützt wird.
        return $this->create($userId, self::TYPE_PUSH, strip_tags($title), strip_tags($message), $priority);
    }

    /**
     * @param int                     $userId
     * @param NotificationMessageData $data
     *
     * @return int|false Created ID
     */
    public function createFromData($userId, NotificationMessageData $data)
    {
        return $this->create(
            $userId,
            $data->getType(),
            $data->getTitle(),
            $data->getMessage(),
            $data->isPriority(),
            $data->getOptions(),
            $data->getTags()
        );
    }

    /**
     * @param int   $notificationId
     * @param array $tags
     *
     * @return bool Returns true on success
     */
    public function addTags($notificationId, array $tags)
    {
        try {
            $tagsArray = [];

            // Fetch existing tags
            $tagsExisting = $this->db->fetchValue(
                'SELECT n.tags FROM notification_message AS n WHERE n.id = :id',
                ['id' => (int)$notificationId]
            );
            if (!empty($tagsExisting)) {
                $tagsArray = $this->transformTagsStringToArray($tagsExisting);
            }

            // Update notification
            $tagsMerged = array_merge($tagsArray, $tags);
            $tagsString = $this->transformTagsArrayToString($tagsMerged);
            $this->db->perform(
                'UPDATE notification_message SET tags = :tags WHERE id = :id',
                ['id' => (int)$notificationId, 'tags' => $tagsString]
            );
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param int $notificationId
     *
     * @return bool
     */
    public function delete($notificationId)
    {
        $numRows = (int)$this->db->fetchAffected(
            'DELETE FROM notification_message WHERE id = :id LIMIT 1',
            ['id' => (int)$notificationId]
        );

        return $numRows === 1;
    }

    /**
     * Delete notification messages by UserID
     *
     * @param int  $userId
     * @param bool $keepPriorityMessages If true, high priority messages will not be deleted
     *
     * @return int Number of deleted messages
     */
    public function deleteByUser($userId, $keepPriorityMessages = true)
    {
        $delete = $this->db->delete()
            ->from('notification_message')
            ->where('user_id = ?', (int)$userId);

        if ((bool)$keepPriorityMessages === true) {
            $delete->where('priority <> ?', 1);
        }

        $numRows = (int)$this->db->fetchAffected(
            $delete->getStatement(),
            $delete->getBindValues()
        );

        return $numRows;
    }

    /**
     * Delete notification messages by tags
     *
     * If multiple tags submitted, all tags must occur in the same message.
     *
     * @example deleteByTags(['callcenter','incomingcall'])
     *
     * @param array $tags
     * @param int   $userId
     * @param bool  $keepPriorityMessages If true, high priority messages will not be deleted
     *
     * @return int Number of deleted messages
     */
    public function deleteByTags(array $tags, $userId = null, $keepPriorityMessages = true)
    {
        $delete = $this->db->delete()->from('notification_message');

        foreach ($tags as $tag) {
            $tag = $this->normalizeTag($tag);
            $delete->where('tags LIKE ?', "%|{$tag}|%");
        }
        if ($userId !== null) {
            $delete->where('user_id = ?', (int)$userId);
        }
        if ((bool)$keepPriorityMessages === true) {
            $delete->where('priority <> ?', 1);
        }

        $numRows = (int)$this->db->fetchAffected(
            $delete->getStatement(),
            $delete->getBindValues()
        );

        return $numRows;
    }

    /**
     * @return array
     */
    public function getValidTypes()
    {
        return self::$validMessageTypes;
    }

    /**
     * @param int $userId
     *
     * @return bool
     */
    private function isValidUser($userId)
    {
        // @todo @refactor Move to UserGateway
        $userCheck = (int)$this->db->fetchValue(
            'SELECT u.id FROM `user` AS u WHERE u.id = :user_id AND u.activ = 1',
            ['user_id' => (int)$userId]
        );

        return $userCheck === (int)$userId;
    }

    /**
     * @param array $tags
     *
     * @return string
     */
    private function transformTagsArrayToString(array $tags)
    {
        if (empty($tags)) {
            return '';
        }

        sort($tags);
        $tags = array_unique($tags);

        $string = '|';
        foreach ($tags as $tag) {
            $tag = $this->normalizeTag($tag);
            $string .= $tag . '|';
        }

        return $string;
    }

    /**
     * @param string $string
     *
     * @return array
     */
    private function transformTagsStringToArray($string)
    {
        $tags = [];
        $parts = explode('|', $string);
        foreach ($parts as $tag) {
            $tag = $this->normalizeTag($tag);
            if (!empty($tag)) {
                $tags[] = $tag;
            }
        }

        sort($tags);
        $tags = array_unique($tags);

        return $tags;
    }

    /**
     * @param string $tag
     *
     * @return string
     */
    private function normalizeTag($tag)
    {
        $tag = strtolower(trim($tag));
        $tag = preg_replace('/[^a-z0-9\-]/', '', $tag); // Remove invalid chars
        $tag = preg_replace('/[-]+/', '-', $tag); // Replace multiple dashes

        return (string)$tag;
    }

    /**
     * @param null|string $message
     *
     * @return null|string
     */
    private function truncateMessage($message = null)
    {
        if ($message !== null && mb_strlen($message) > 1024) {
            $message = mb_substr($message, 0, 1020) . ' ...';
        }

        return $message;
    }

    /**
     * @param string      $type
     * @param string      $title
     * @param null|string $message
     * @param bool        $priority
     * @param array       $options
     * @param array       $tags
     *
     * @return int
     */
    public function createPushNotificationForConnectedUsers(
        $type,
        $title,
        $message = null,
        $priority = false,
        $options = [],
        $tags = []
    ) {
        if (!in_array($type, self::$validMessageTypes, true)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is not a valid message type. Valid types are: %s', $type, implode(', ', self::$validMessageTypes)
            ));
        }

        $sql = "INSERT INTO notification_message (user_id, type, title, message, tags, options_json, priority, created_at)
        SELECT u.id, :type, :title, :msg,:tags,'',:priority,NOW()
        FROM `user` AS u
        INNER JOIN useronline uo on u.id = uo.user_id AND uo.login = 1";

        return (int)$this->db->fetchAffected($sql, [
            'title'        => strip_tags($title),
            'msg'          => strip_tags($message),
            'type'         => $type,
            'priority'     => (int)$priority,
            'options_json' => !empty($options) ? json_encode($options) : null,
            'tags'         => !empty($tags) ? $this->transformTagsArrayToString($tags) : null,
        ]);
    }
}
