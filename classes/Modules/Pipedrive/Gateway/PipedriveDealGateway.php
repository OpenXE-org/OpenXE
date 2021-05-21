<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Gateway;

use Xentral\Components\Database\Database;

final class PipedriveDealGateway
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param int $pdDealId
     *
     * @return array
     */
    public function getDealByPipedriveId(int $pdDealId): array
    {
        return $this->db->fetchRow(
            'SELECT
            d.id,
            d.created_at,
            d.data,
            d.wiedervorlage_id
            FROM `pipedrive_deals` AS `d`
            WHERE d.hidden = 0 AND d.pd_deal_id = :id',
            ['id' => $pdDealId]
        );
    }

    /**
     * @param int $resubmissionId
     *
     * @return array
     */
    public function getDealByResubmissionId(int $resubmissionId): array
    {
        return $this->db->fetchRow(
            'SELECT
            d.id,
            d.created_at,
            d.data,
            d.wiedervorlage_id,
            d.pd_deal_id
            FROM `pipedrive_deals` AS `d`
            WHERE d.hidden = 0 AND d.wiedervorlage_id = :id',
            ['id' => $resubmissionId]
        );
    }

    /**
     * @param int $stageId
     *
     * @return array
     */
    public function getMappingStageByResubmissionStageId(int $stageId): array
    {
        return $this->db->fetchRow(
            'SELECT
            pm.id, 
            pm.created_at, 
            pm.wiedervorlage_stage_id,
            pm.label,
            pm.value,
            pm.wiedervorlage_view_id,
            pm.type
            FROM `pipedrive_mappings` AS `pm`
            WHERE pm.wiedervorlage_stage_id=:resubmission_id AND pm.type =:type',
            ['resubmission_id' => $stageId, 'type' => 'deals']
        );
    }

}
