<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle\Scheduler;


use Xentral\Components\Database\Database;

class TaskMutexService implements TaskMutexServiceInterface
{
    /** @var Database $db */
    private $db;

    /**
     * TaskMutexService constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $parameter
     * @param bool   $active
     */
    public function setMutex(string $parameter, bool $active = true): void
    {
        $this->db->perform(
            'UPDATE `prozessstarter` SET `mutex` = :mutex, `mutexcounter` = 0, `letzteausfuerhung` = NOW()
            WHERE `parameter` = :parameter AND `aktiv` = 1',
            ['mutex' => (int)$active, 'parameter' => $parameter]
        );
    }

    /**
     * @param string $parameter
     *
     * @return bool
     */
    public function isTaskInstanceRunning(string $parameter): bool
    {
        return (int)$this->db->fetchValue(
                'SELECT COUNT(`id`) FROM `prozessstarter` WHERE `parameter` = :parameter AND `aktiv` = 1 AND `mutex` = 1',
                ['parameter' => $parameter]
            ) > 0;
    }
}
