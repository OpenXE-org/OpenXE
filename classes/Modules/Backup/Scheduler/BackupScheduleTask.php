<?php

namespace Xentral\Modules\Backup\Scheduler;

use ApplicationCore;
use ArrayObject;
use Backup;
use Xentral\Components\Database\Database;
use Xentral\Modules\Backup\BackupNotificationService;
use Xentral\Modules\Backup\BackupService;
use Xentral\Modules\Backup\BackupSystemConfigurationService;

final class BackupScheduleTask implements BackupSchedulerTaskInterface
{
    /** @var string $action */
    private $action;

    /** @var int waiting time in seconds before start backup Task */
    const TASK_WAITING_TIMEOUT = 25;

    const SPACE_OF_SET = 2048000000;

    /** @var Database $db */
    private $db;

    /** @var BackupNotificationService $notificationService */
    private $notificationService;

    /** @var BackupSystemConfigurationService $configurationService */
    private $configurationService;

    /** @var ApplicationCore $app */
    private $app;

    /** @var BackupService $backupService */
    private $backupService;

    public function __construct(
        Database $db,
        BackupSystemConfigurationService $configurationService,
        BackupNotificationService $notificationService,
        ApplicationCore $app,
        BackupService $backupService
    ) {
        $this->db = $db;
        $this->configurationService = $configurationService;
        $this->notificationService = $notificationService;
        $this->app = $app;
        $this->backupService = $backupService;
    }

    public function execute()
    {
        $isAutoBackup = false;
        $autoConfig = null;
        $dateiname = null;
        $conf = $this->configurationService->getConfiguration('backup_configuration_cron');

        $minimumSpace = $this->getUsedSpaceFromSystemhealth();

        if (empty($conf)) {
            // Auto Backup deactivated OS419883
            $isAutoBackup = false;
        }

        if ($isAutoBackup === true) {

            $dateiname = date('Y-m-d_') . sprintf('%s_SystemBackup.%s', $this->app->DB->dbname, 'zip');
            if ($this->db->fetchValue('SELECT true FROM backup WHERE dateiname=:name LIMIT 1',
                ['name' => $dateiname])) {
                // AUTO BACKUP SHOULD RUN ONLY ONCE PER DAY
                return;
            }

            $autoConfig = [
                'action'    => null,
                'config'    => $this->app->Conf,
                'file_name' => $dateiname,
                'options'   => [
                    'addr'        => 1, // just for auto
                    'name'        => 'SystemBackup',
                    'ssid'        => uniqid('auto', false),
                    'user_id'     => 0,
                    'ip'          => '0.0.0.0',
                    'auto_backup' => true,
                ],
            ];
        }

        if ($isAutoBackup === true || (($oConfig = json_decode($conf)) && property_exists($oConfig, 'action'))) {
            if ($isAutoBackup === true) {
                $oConfig = (object)$autoConfig;
            }
            $this->action = $oConfig->action;
            $this->notificationService->addNotification(
                BackupNotificationService::BACKUP_CONF_MODE,
                'Bitte schließen Sie Ihre Aufgaben, System Backup startet in Kürze',
                ['Backup']
            );

            // LOGOUT ALL users except LoggedIn USER
            $userId = 0;
            if (property_exists($oConfig, 'options')) {
                $options = (array)$oConfig->options;
                $userId = array_key_exists('user_id', $options) ? $options['user_id'] : 0;
            }
            $this->db->perform('DELETE FROM useronline WHERE user_id <>:uid', ['uid' => $userId]);

            $this->configurationService->trySetConfiguration('backup_configuration_cron', '');

            $this->app->erp->setMaintainance(true);

            $timeout = static::TASK_WAITING_TIMEOUT;
            // CHECK WHETHER A JOB IS RUNNING
            while ($this->db->perform('SELECT id FROM prozessstarter WHERE aktiv=1 AND mutex=1 LIMIT 1')) {
                $timeout -= 5;
                usleep(5000000);

                if ($timeout <= 0) {
                    // DISABLE ALL CRON JOBS FOR RESTORE
                    if ($oConfig->action === 'RunRestoreJob') {
                        $this->db->perform('UPDATE prozessstarter SET aktiv=0');
                    }
                    break;
                }
            }
            if ($isAutoBackup === false) {
                /** @var Backup $backup */
                $backup = $this->app->loadModule('backup');
                if (in_array($this->action, ['RunRestoreJob', 'RunCreateJob'])) {
                    $backup->{$this->action}($oConfig);
                }
            } elseif (null !== $autoConfig) {
                $this->backupService->create($this->app->Conf, $dateiname, $autoConfig['options'], $minimumSpace);
            }
        }
    }

    public function cleanup()
    {
        if ($this->action === 'RunRestoreJob') {
            $this->app->erp->UpgradeDatabase();
        }

        $this->notificationService->removeNotification(BackupNotificationService::BACKUP_CONF_MODE, ['Backup']);
        $this->app->erp->setMaintainance(false);

    }

    public function beforeScheduleAction(ArrayObject $args)
    {
        // TODO: Implement beforeScheduleAction() method.
    }

    public function afterScheduleAction(ArrayObject $args)
    {
        // TODO: Implement afterScheduleAction() method.
    }


    private function getUsedSpaceFromSystemhealth(): int
    {
        $minimumSpace = self::SPACE_OF_SET + 1024 * 1024
            * (int)$this->configurationService->getConfiguration('databasesize');

        foreach (['dms', 'pdfarchiv', 'pdfmirror', 'emailbackup', 'tmp', 'uebertragung'] as $subDir) {
            $minimumSpace *= 1024 * 1024 * (int)$this->configurationService->getConfiguration("userdata{$subDir}size");
        }

        return $minimumSpace;
    }
}
