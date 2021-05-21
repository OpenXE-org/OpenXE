<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\RequestQueues;

use Xentral\Components\Database\Database;

final class PipedriveRequestQueuesGateway
{
    /** @var Database $db */
    private $db;

    /**
     * PipedriveRequestQueuesGateway constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getNewRequestsByCallType(string $type = 'pipedrive'): array
    {

        $sql = 'SELECT 
                rq.id,
                rq.command, 
                rq.not_before,
                rq.amount_attempts, 
                rq.runner, 
                rq.check_sum,
                rq.on_after_done,
                rq.is_looped,
                rq.setting_name,
                rq.created_at,
                rq.modified_at
                FROM `pipedrive_request_queues` AS `rq`
                WHERE rq.call_type = :type AND rq.completed = 0 AND rq.deleted = 0
                ORDER BY rq.created_at';

        return $this->db->fetchAll($sql, ['type' => $type]);
    }
}
