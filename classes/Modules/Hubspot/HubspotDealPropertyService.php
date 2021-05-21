<?php

namespace Xentral\Modules\Hubspot;

use Xentral\Components\Database\Database;
use Xentral\Modules\Hubspot\Exception\HubspotException;

final class HubspotDealPropertyService
{

    /**
     * @var HubspotClientService
     */
    private $client;
    /**
     * @var Database
     */
    private $db;

    /** @var HubspotContactPropertyGateway $propertyGateway */
    private $propertyGateway;

    /**
     * HubspotDealPropertyService constructor.
     *
     * @param HubspotClientService          $client
     * @param Database                      $db
     * @param HubspotContactPropertyGateway $propertyGateway
     */
    public function __construct(
        HubspotClientService $client,
        Database $db,
        HubspotContactPropertyGateway $propertyGateway
    ) {
        $this->client = $client;
        $this->db = $db;
        $this->propertyGateway = $propertyGateway;
    }

    /**
     * @throws HubspotException
     * @return array
     */
    public function getDealStages()
    {
        $response = $this->client->setResource('getPipelineProperties')->read([], ['deals']);
        if ($response->getStatusCode() !== 200) {
            throw new HubspotException($response->getError());
        }

        if (($data = $response->getJson()) && array_key_exists('results', $data)) {
            $stages = array_column($data['results'], 'stages');

            return reset($stages);
        }

        return [];
    }

    public function installDealStages()
    {
        // Get Deal Stages
        $firstStage = 0;
        $viewId = 0;
        if ($dealStages = $this->propertyGateway->getLeadsByType('deals')) {
            $stage = array_unique(array_column($dealStages, 'wiedervorlage_stage_id'));
            $firstStage = $stage[0];
        }
        if ($firstStage > 0) {
            $viewId = $this->db->fetchValue(
                'SELECT ws.view FROM wiedervorlage_stages `ws` WHERE ws.id=:id',
                [
                    'id' => $firstStage,
                ]
            );
        }

        if (empty($firstStage) || empty($viewId)) {
            // CHECK the default view
            if ($this->db->fetchValue(
                'SELECT wv.id FROM wiedervorlage_view `wv`
                    WHERE wv.name=:name AND wv.shortname=:desc AND wv.active=:active AND wv.project=0',
                [
                    'name'   => 'Hubspot',
                    'desc'   => 'Hubspot',
                    'active' => 1,
                ]
            )) {
                return;
            }

            $this->db->perform(
                'INSERT INTO wiedervorlage_view(name,shortname,active) VALUES (:name,:desc_short,1)',
                [
                    'name'       => 'Hubspot',
                    'desc_short' => 'Hubspot',
                ]
            );
            $viewId = $this->db->lastInsertId();
        }

        if ($viewId > 0 && ($ahDealStages = $this->getDealStages())) {
            $position = (int)$this->getMaxSortByViewId($viewId) + 1;
            foreach ($ahDealStages as $hDealStage) {
                if ($this->db->fetchValue(
                    'SELECT hm.id FROM hs_mapping_leads `hm` WHERE hm.value=:value AND hm.type=:type',
                    [
                        'value' => $hDealStage['stageId'],
                        'type'  => 'deals',
                    ]
                )) {
                    continue;
                }

                $this->db->perform(
                    'INSERT INTO wiedervorlage_stages (kurzbezeichnung,name,stageausblenden,sort,view)
                            VALUES(:desc, :name,:enabled, :position,:wiedervorlage_view_id)',
                    [
                        'desc'                  => $hDealStage['label'],
                        'name'                  => $hDealStage['label'],
                        'position'              => $position,
                        'wiedervorlage_view_id' => $viewId,
                        'enabled'               => 1,
                    ]
                );

                if ($stageId = $this->db->lastInsertId()) {
                    $this->db->perform(
                        'INSERT INTO hs_mapping_leads (`label`, `value`, `type`, `wiedervorlage_stage_id`, `is_system`,
                              `wiedervorlage_view_id`)
                            VALUES(:label, :value,:type, :wstage_id, :is_system, :view_id)',
                        [
                            'label'     => $hDealStage['label'],
                            'value'     => $hDealStage['stageId'],
                            'type'      => 'deals',
                            'wstage_id' => $stageId,
                            'is_system' => 1,
                            'view_id'   => $viewId,
                        ]
                    );
                }
                $position++;
            }
        }
    }

    /**
     * @param int $viewId
     *
     * @return false|float|int|string
     */
    public function getMaxSortByViewId($viewId)
    {
        return $this->db->fetchValue(
            'SELECT MAX(ws.sort) FROM wiedervorlage_stages `ws` WHERE ws.`view`=:id',
            ['id' => $viewId]
        );
    }

    /**
     * @param int $viewId
     *
     * @return false|float|int|string
     */
    public function getMinStageByViewId($viewId)
    {
        return $this->db->fetchValue(
            'SELECT MIN(ws.id) FROM wiedervorlage_stages `ws` WHERE ws.`view`=:id',
            ['id' => $viewId]
        );
    }
}
