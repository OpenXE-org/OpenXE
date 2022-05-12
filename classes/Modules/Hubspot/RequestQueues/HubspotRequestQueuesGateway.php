<?php

namespace Xentral\Modules\Hubspot\RequestQueues;

use Xentral\Components\Database\Database;
use Xentral\Modules\Hubspot\RequestQueues\Exception\RequestQueuesException;

final class HubspotRequestQueuesGateway
{
    /** @var Database $db */
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $type
     *
     * @throws RequestQueuesException
     * @return array
     */
    public function getNewRequestsByCallType($type)
    {
        if (!is_string($type)) {
            throw new RequestQueuesException(
                'Call Type should be a string'
            );
        }
        $sql = 'SELECT 
                rq.id,
                rq.command, 
                rq.not_before,
                rq.try, 
                rq.runner, 
                rq.check_sum,
                rq.on_after_done 
                FROM hubspot_request_queues AS `rq` WHERE rq.call_type=:type AND rq.completed=0 AND rq.deleted=0
                ORDER BY rq.created_at';

        return $this->db->fetchAll($sql, ['type' => $type]);
    }
}
