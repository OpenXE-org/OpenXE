<?php

namespace Xentral\Modules\SystemNotification\Service;

interface NotificationServiceInterface
{
    const TYPE_DEFAULT = 'default'; // White
    const TYPE_SUCESS = 'success'; // Green
    const TYPE_NOTICE = 'notice'; // Blue
    const TYPE_WARNING = 'warning'; // Yellow
    const TYPE_ERROR = 'error'; // Red
    const TYPE_PUSH = 'push'; // Browser push notificaion

    /**
     * Create a notification
     *
     * @param int         $recipientId
     * @param string      $type
     * @param string      $title
     * @param string|null $message
     * @param bool        $priority Play sound and make notification sticky
     * @param array|null  $buttons
     *
     * @return int Created Notification-ID
     */
    public function create($recipientId, $type, $title, $message = null, $priority = false, $buttons = null);

    /**
     * Create browser push notification
     *
     * @param int    $recipientId
     * @param string $title
     * @param string $message
     * @param bool   $priority
     *
     * @return int Created ID
     */
    public function createPushNotification($recipientId, $title, $message, $priority = false);

    /**
     * Delete notification by ID
     *
     * @param int $notificationId
     *
     * @return bool
     */
    public function delete($notificationId);

    /**
     * Returns valid notification types
     *
     * @return array
     */
    public function getValidTypes();
}
