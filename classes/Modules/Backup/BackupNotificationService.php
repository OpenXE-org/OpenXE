<?php

namespace Xentral\Modules\Backup;

use Xentral\Modules\Backup\Exception\BackupNotificationServiceException;
use Xentral\Modules\SystemNotification\Service\NotificationService;

final class BackupNotificationService
{
    /** @var string login sperre Konfiguration */
    const BACKUP_CONF_MODE = 'login_lock_mode';

    /** @var  NotificationService $notificationService */
    private $notificationService;
    /**
     * @var BackupSystemConfigurationService
     */
    private $configurationService;

    public function __construct(
        BackupSystemConfigurationService $configurationService,
        NotificationService $notificationService
    ) {
        $this->configurationService = $configurationService;
        $this->notificationService = $notificationService;
    }

    /**
     * @param string $name
     * @param string $message
     * @param array  $tags
     * @param string $title
     * @param string $level
     */
    public function addNotification($name, $message, $tags = [], $title = '', $level = 'warning')
    {

        if (!is_string($name) || empty(trim($name))) {
            throw new BackupNotificationServiceException('Name cannot be empty');
        }

        if (!is_string($message) || empty(trim($message))) {
            throw new BackupNotificationServiceException('Message cannot be empty');
        }

        if (empty($tags)) {
            throw new BackupNotificationServiceException('Tags cannot be empty');
        }

        if (!is_array($tags)) {
            throw new BackupNotificationServiceException('Tags should be an array');
        }

        if (empty(trim($title))) {
            $title = 'Laufender Backupprozess';
        }

        $this->notificationService->createPushNotificationForConnectedUsers($level, $title, $message, true, [], $tags);

        $this->configurationService->trySetConfiguration($name, '1');
        if ($name === static::BACKUP_CONF_MODE) {
            $this->configurationService->trySetConfiguration('login_lock_mode_time', time());
            $this->configurationService->trySetConfiguration('login_lock_mode_timeout', '900');
        }
    }

    /**
     * @param string $name
     * @param array  $tags
     */
    public function removeNotification($name, $tags = [])
    {

        if (!is_string($name) || empty(trim($name))) {
            throw new BackupNotificationServiceException('Name cannot be empty');
        }

        if (empty($tags)) {
            throw new BackupNotificationServiceException('Tags cannot be empty');
        }

        if (!is_array($tags)) {
            throw new BackupNotificationServiceException('Tags should be an array');
        }

        if ($this->configurationService->getConfiguration($name)) {
            $this->configurationService->trySetConfiguration($name, '0');
            if ($name === static::BACKUP_CONF_MODE) {
                $this->configurationService->trySetConfiguration('login_lock_mode_time', '');
                $this->configurationService->trySetConfiguration('login_lock_mode_timeout', '0');
            }

            $this->notificationService->deleteByTags($tags, null, false);
        }
    }

}
