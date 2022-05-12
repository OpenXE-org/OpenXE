<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Pipedrive\Exception\PipedriveClientException;
use Xentral\Modules\Pipedrive\Exception\PipedriveConfigurationException;
use Xentral\Modules\Pipedrive\Exception\PipedriveDealPropertyServiceException;
use Xentral\Modules\Pipedrive\Exception\PipedriveHttpClientException;
use Xentral\Modules\Pipedrive\Exception\PipedriveMetaException;
use Xentral\Modules\Pipedrive\Gateway\PipedrivePersonPropertyGateway;
use Xentral\Modules\Pipedrive\Wrapper\PipedriveResubmissionWrapper;
use Xentral\Modules\Resubmission\Exception\StageNotFoundException;
use Xentral\Modules\Resubmission\Exception\ViewNotFoundException;
use Xentral\Modules\Resubmission\Service\ResubmissionGateway;

final class PipedriveDealPropertyService
{

    /** @var PipedriveClientService $client */
    private $client;

    /** @var Database $db */
    private $db;

    /** @var PipedrivePersonPropertyGateway $propertyGateway */
    private $propertyGateway;

    /** @var ResubmissionGateway $resubmissionGateway */
    private $resubmissionGateway;

    /** @var PipedriveResubmissionWrapper $resubmissionWrapper */
    private $resubmissionWrapper;

    /**
     * @param PipedriveClientService         $client
     * @param Database                       $db
     * @param PipedrivePersonPropertyGateway $propertyGateway
     * @param ResubmissionGateway            $resubmissionGateway
     * @param PipedriveResubmissionWrapper   $resubmissionWrapper
     */
    public function __construct(
        PipedriveClientService $client,
        Database $db,
        PipedrivePersonPropertyGateway $propertyGateway,
        ResubmissionGateway $resubmissionGateway,
        PipedriveResubmissionWrapper $resubmissionWrapper
    ) {
        $this->client = $client;
        $this->db = $db;
        $this->propertyGateway = $propertyGateway;
        $this->resubmissionGateway = $resubmissionGateway;
        $this->resubmissionWrapper = $resubmissionWrapper;
    }

    /**
     * @param int $pipelineId
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveDealPropertyServiceException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     *
     * @return array
     */
    public function getDealStages(int $pipelineId = 0): array
    {
        $data = [];
        if ($pipelineId !== 0) {
            $data = ['pipeline_id' => $pipelineId];
        }
        $response = $this->client->read('getStages', $data);
        if ($response->getStatusCode() !== 200) {
            throw new PipedriveDealPropertyServiceException($response->getError());
        }

        return $response->getData();
    }

    /**
     * @param int|null $pipelineId
     *
     * @throws PipedriveClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveDealPropertyServiceException
     * @throws PipedriveHttpClientException
     * @throws PipedriveMetaException
     * @throws StageNotFoundException
     *
     * @return void
     */
    public function installDealStages(?int $pipelineId = null): void
    {
        // Get Deal Stages
        $firstStage = 0;
        $viewId = 0;
        if ($dealStages = $this->propertyGateway->getLeadsByType('deals')) {
            $stage = array_unique(array_column($dealStages, 'wiedervorlage_stage_id'));
            $firstStage = $stage[0];
        }

        if ($firstStage > 0) {
            $viewId = $this->resubmissionGateway->getViewIdByStage($firstStage);
        }

        if (empty($firstStage) || empty($viewId)) {
            try {
                $viewId = $this->resubmissionGateway->getViewIdByNameAndDescription('Pipedrive', 'Pipedrive');
            } catch (ViewNotFoundException $exception) {
                $viewId = $this->resubmissionWrapper->addResubmissionView('Pipedrive', 'Pipedrive');
            }
        }

        // Default Pipeline
        if ($pipelineId === null) {
            $pipeline = $this->getDefaultPipeline();
            $pipelineId = $pipeline['id'] ?? 0;
        }

        if ($viewId > 0 && ($ahDealStages = $this->getDealStages($pipelineId))) {
            $position = $this->resubmissionGateway->getMaxSortByViewId($viewId) + 1;
            foreach ($ahDealStages as $hDealStage) {
                if ($this->db->fetchValue(
                    'SELECT hm.id FROM `pipedrive_mappings` AS `hm` WHERE hm.value=:value AND hm.type = :type',
                    [
                        'value' => $hDealStage['id'],
                        'type'  => 'deals',
                    ]
                )) {
                    continue;
                }

                $stage = [
                    'desc'                  => $hDealStage['name'],
                    'name'                  => $hDealStage['name'],
                    'position'              => $position,
                    'wiedervorlage_view_id' => $viewId,
                    'enabled'               => 1,
                    'ausblenden'            => 0,
                ];
                $stageId = $this->resubmissionWrapper->addResubmissionStage($stage);

                if (!empty($stageId)) {
                    $this->db->perform(
                        'INSERT INTO `pipedrive_mappings` (`label`, `value`, `type`, `wiedervorlage_stage_id`, 
                                  `is_system`, `wiedervorlage_view_id`)
                            VALUES(:label, :value,:type, :wstage_id, :is_system, :view_id)',
                        [
                            'label'     => $hDealStage['name'],
                            'value'     => $hDealStage['id'],
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
     * @throws PipedriveClientException
     * @throws PipedriveDealPropertyServiceException
     * @throws PipedriveHttpClientException
     * @throws PipedriveConfigurationException
     * @throws PipedriveMetaException
     *
     * @return array
     */
    public function getDefaultPipeline(): array
    {
        $response = $this->client->read('getPipelines');
        if ($response->getStatusCode() !== 200) {
            throw new PipedriveDealPropertyServiceException($response->getError());
        }

        $data = $response->getData();

        return array_shift($data);
    }
}
