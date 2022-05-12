<?php

namespace Xentral\Modules\Hubspot;

use Xentral\Components\Database\Database;
use Xentral\Modules\Hubspot\Exception\HubspotDealGatewayNotFoundException;

final class HubspotDealGateway
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
     * @param int $hsDealId
     *
     * @return array
     */
    public function getByHubspotId($hsDealId)
    {
        if (!is_numeric($hsDealId)) {
            throw new HubspotDealGatewayNotFoundException(
                sprintf(
                    'Hubspot Deal not found: HubspotID%s',
                    $hsDealId
                )
            );
        }

        return $this->db->fetchRow(
            'SELECT
            d.id,
            d.created_at,
            d.data,
            d.wiedervorlage_id
            FROM `hubspot_deals` AS `d` WHERE d.hidden = 0 AND d.hs_deal_id = :id',
            ['id' => (int)$hsDealId]
        );
    }

    /**
     * @param int $resubmissionId
     *
     * @return array
     */
    public function getByResubmissionId($resubmissionId)
    {
        if (!is_numeric($resubmissionId)) {
            throw new HubspotDealGatewayNotFoundException(
                sprintf(
                    'Hubspot Deal not found for : ResubmissionId%s',
                    $resubmissionId
                )
            );
        }

        return $this->db->fetchRow(
            'SELECT
            d.id,
            d.created_at,
            d.data,
            d.wiedervorlage_id,
            d.hs_deal_id
            FROM `hubspot_deals` AS `d` WHERE d.hidden = 0 AND d.wiedervorlage_id = :id',
            ['id' => (int)$resubmissionId]
        );
    }

    /**
     * @param int $stageId
     **
     *
     * @return array
     */
    public function getMappingStageByResubmissionStageId($stageId)
    {
        if (!is_numeric($stageId)) {
            throw new HubspotDealGatewayNotFoundException(
                sprintf(
                    'Hubspot Deal Mapping not found for stage: ID%s',
                    $stageId
                )
            );
        }

        return $this->db->fetchRow(
            'SELECT
            hm.id, 
            hm.created_at, 
            hm.wiedervorlage_stage_id,
            hm.label,
            hm.value,
            hm.wiedervorlage_view_id,
            hm.type FROM hs_mapping_leads `hm` WHERE hm.wiedervorlage_stage_id=:resubmission_id AND hm.type=:type',
            ['resubmission_id' => $stageId, 'type' => 'deals']
        );
    }

    /**
     * @param string $value
     **
     *
     * @return array
     */
    public function getMappingStageByValue($value)
    {
        if (!is_string($value)) {
            throw new HubspotDealGatewayNotFoundException(
                sprintf(
                    'Hubspot Deal Mapping not found for value: %s',
                    $value
                )
            );
        }

        return $this->db->fetchRow(
            'SELECT
            hm.id,
            hm.created_at,
            hm.wiedervorlage_stage_id,
            hm.label,
            hm.value,
            hm.wiedervorlage_view_id,
            hm.type FROM hs_mapping_leads `hm` WHERE hm.value=:value AND hm.type=:type',
            ['value' => $value, 'type' => 'deals']
        );
    }

}
