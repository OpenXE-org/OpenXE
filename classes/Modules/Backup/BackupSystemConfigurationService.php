<?php

namespace Xentral\Modules\Backup;

use DateTime;
use erpAPI;
use Exception as DatetimeException;
use Xentral\Modules\Backup\Exception\BackupSystemConfigurationException;

final class BackupSystemConfigurationService
{
    /** @var erpAPI $erp */
    private $erp;

    /**
     * @param erpAPI $erp
     */
    public function __construct(erpAPI $erp)
    {
        $this->erp = $erp;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function trySetConfiguration($name, $value)
    {
        if (empty($name) || (!is_string($value) && !is_numeric($value))) {
            throw new BackupSystemConfigurationException('Cannot set Configuration');

        }

        $this->erp->SetKonfigurationValue($name, $value);

    }

    /**
     * @param int    $comparedTime time in second to check if the cron has been enabled
     *
     * @param string $confName
     *
     * @throws DatetimeException
     * @return bool
     */
    public function tryCheckCronIsEnabled($comparedTime = 300, $confName = 'prozessstarter_letzteraufruf')
    {

        try {
            $latestRun = $this->getConfiguration($confName);
        } catch (BackupSystemConfigurationException $exception) {
            return false;
        }

        if (empty($latestRun)) {
            return false;
        }
        $latestRunTime = new DateTime($latestRun);

        return $this->getDiffDateTime($latestRunTime) < $comparedTime + 1;
    }

    /**
     * Return difference between $latestRunTime and $now
     *
     * @param DateTime        $latestRunTime
     * @param Datetime|String $now
     *
     * @throws DatetimeException
     * @return int
     */
    private function getDiffDateTime(DateTime $latestRunTime, $now = 'NOW')
    {
        if (!($now instanceOf DateTime)) {
            $now = new DateTime($now);
        }

        return $now->getTimestamp() - $latestRunTime->getTimestamp();
    }

    /**
     * @param string $name
     *
     * @return array|mixed|string|null
     */
    public function getConfiguration($name)
    {
        if (empty(trim($name))) {
            throw new BackupSystemConfigurationException('Cannot get Configuration');
        }

        return $this->erp->GetKonfiguration($name);
    }
}
