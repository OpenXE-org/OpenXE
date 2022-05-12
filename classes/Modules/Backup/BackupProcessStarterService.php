<?php


namespace Xentral\Modules\Backup;

use DateTimeInterface;
use erpAPI;
use Xentral\Modules\Backup\Exception\BackupProcessStarterException;

/**
 * Class BackupProcessStarterService
 *
 * @property erpAPI erp
 * @package Xentral\Modules\Backup
 */
final class BackupProcessStarterService
{
    /** @var erpAPI $erp */
    private $erp;

    /**
     * BackupProcessStarterService constructor.
     *
     * @param erpAPI $erp
     */
    public function __construct(erpAPI $erp)
    {
        $this->erp = $erp;
    }

    /**
     * @param string            $cronFile
     * @param int               $period
     * @param DateTimeInterface $startTime
     * @param string|null       $title
     *
     * @throws BackupProcessStarterException
     *
     * @return bool|int|string|null
     */
    public function tryCheckProcess($cronFile, $period, DateTimeInterface $startTime, $title = null)
    {
        if (empty($cronFile)) {
            throw new BackupProcessStarterException('Cron file is missing');
        }
        if (null === $title) {
            $title = $cronFile;
        }

        return $this->erp->CheckProzessstarter($title, 'periodisch', $period, $startTime->format('Y-m-d H:i:s'),
            'cronjob', $cronFile, 1);
    }
}