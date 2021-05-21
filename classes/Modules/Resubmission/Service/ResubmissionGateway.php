<?php

namespace Xentral\Modules\Resubmission\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Resubmission\Exception\StageNotFoundException;
use Xentral\Modules\Resubmission\Exception\ResubmissionNotFoundException;
use Xentral\Modules\Resubmission\Exception\ViewNotFoundException;

final class ResubmissionGateway
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param int $resubmissionId
     *
     * @return bool
     */
    public function existsResubmission($resubmissionId)
    {
        $sql = 'SELECT w.id FROM `wiedervorlage` AS `w` WHERE w.id = :resubmission_id';
        $check = $this->db->fetchValue($sql, ['resubmission_id' => (int)$resubmissionId]);

        return (int)$check === (int)$resubmissionId;
    }

    /**
     * @param int $resubmissionId
     *
     * @throws ResubmissionNotFoundException
     *
     * @return int
     */
    public function getViewIdByResubmission($resubmissionId)
    {
        $sql =
            'SELECT ws.view AS `view_id` 
             FROM `wiedervorlage` AS `w`
             LEFT JOIN `wiedervorlage_stages` AS `ws` ON w.stages = ws.id 
             WHERE w.id = :resubmission_id';
        $viewId = $this->db->fetchValue($sql, ['resubmission_id' => (int)$resubmissionId]);

        if ($viewId === false) {
            throw new ResubmissionNotFoundException(sprintf('Resubmission not found: ID%s', $resubmissionId));
        }

        return (int)$viewId;
    }

    /**
     * @param int $stageId
     *
     * @throws StageNotFoundException
     *
     * @return int
     */
    public function getViewIdByStage($stageId)
    {
        $sql = 'SELECT ws.view AS `view_id` FROM `wiedervorlage_stages` AS `ws` WHERE ws.id = :stage_id';
        $viewId = $this->db->fetchValue($sql, ['stage_id' => (int)$stageId]);

        if ($viewId === false) {
            throw new StageNotFoundException(sprintf('Stage not found: ID%s', $stageId));
        }

        return (int)$viewId;
    }

    /**
     * @param int $viewId
     *
     * @return array
     */
    public function getStagesByView($viewId)
    {
        $sql =
            'SELECT 
                 ws.id, 
                 IF(ws.kurzbezeichnung != \'\', ws.kurzbezeichnung, ws.name) AS `shortname`, 
                 ws.name AS `longname`
             FROM `wiedervorlage_stages` AS `ws` 
             LEFT JOIN `wiedervorlage_view` AS `wv` ON ws.view = wv.id AND wv.active = 1
             WHERE ws.view = :view_id
             ORDER BY ws.sort, ws.id';
        $stages = $this->db->fetchAll($sql, ['view_id' => (int)$viewId]);

        $rank = 1;
        foreach ($stages as &$stage) {
            $stage['rank'] = $rank;
            $rank++;
        }
        unset($stage);

        return $stages;
    }

    /**
     * Alle Geschwister-Stages ermitteln
     *
     * D.h. alle Stages ermitteln die sich in der gleichen View befinden wie die übergebene Stage
     *
     * @param int $stageId
     *
     * @return array
     */
    public function getSiblingStages($stageId)
    {
        $viewId = $this->getViewIdByStage($stageId);

        return $this->getStagesByView($viewId);
    }

    /**
     * @param int $stageId
     *
     * @throws StageNotFoundException
     *
     * @return array
     */
    public function getStage($stageId)
    {
        $sql =
            'SELECT 
                 ws.id,
                 IF(ws.kurzbezeichnung != \'\', ws.kurzbezeichnung, ws.name) AS `shortname`, 
                 ws.name AS `longname`
             FROM `wiedervorlage_stages` AS `ws` 
             WHERE ws.id = :stage_id';
        $stage = $this->db->fetchRow($sql, ['stage_id' => (int)$stageId]);
        if (empty($stage)) {
            throw new StageNotFoundException(sprintf('Stage ID "%s" not found', $stageId));
        }

        return $stage;
    }

    /**
     * @param int $resubmissionId
     *
     * @throws ResubmissionNotFoundException
     * @throws StageNotFoundException
     *
     * @return array
     */
    public function getStageByResubmission($resubmissionId)
    {
        $sql =
            'SELECT
                 w.id AS resubmission_id,
                 ws.view AS view_id,
                 ws.id, 
                 IF(ws.kurzbezeichnung != \'\', ws.kurzbezeichnung, ws.name) AS `shortname`, 
                 ws.name AS `longname`
             FROM `wiedervorlage` AS `w`
             LEFT JOIN `wiedervorlage_stages` AS `ws` ON w.stages = ws.id
             WHERE w.id = :resubmissionId';
        $result = $this->db->fetchRow($sql, ['resubmissionId' => (int)$resubmissionId]);

        if (empty($result['resubmission_id'])) {
            throw new ResubmissionNotFoundException(
                sprintf(
                    'Resubmission not found: ID%s',
                    $resubmissionId
                )
            );
        }
        if (empty($result['id'])) {
            throw new StageNotFoundException(
                sprintf(
                    'Stage not found for resubmission: Resubmission-ID%s',
                    $resubmissionId
                )
            );
        }
        unset($result['resubmission_id']);

        return $result;
    }

    /**
     * @param int $resubmissionId Wiedervorlagen-ID
     *
     * @return array
     */
    public function getStagesByResubmission($resubmissionId)
    {
        // View-ID der aktuellen Stage ermitteln; View-ID darf 0 sein; 0 = Standard-View
        $viewId = $this->getViewIdByResubmission($resubmissionId);

        $sql =
            'SELECT 
                 ws.id, 
                 IF(ws.kurzbezeichnung != \'\', ws.kurzbezeichnung, ws.name) AS `shortname`, 
                 ws.name AS `longname`
             FROM `wiedervorlage_stages` AS `ws` 
             LEFT JOIN `wiedervorlage_view` AS `wv` ON ws.view = wv.id AND wv.active = 1
             WHERE ws.view = :view_id
             ORDER BY ws.sort, ws.id';

        return $this->db->fetchAll($sql, ['view_id' => $viewId]);
    }

    /**
     * @return array
     */
    public function getStagesWithViews()
    {
        $sql =
            'SELECT
                   ws.id,
                   ws.name AS stage_name,
                   wv.name AS view_name
                 FROM `wiedervorlage_stages` AS `ws`
                 LEFT JOIN `wiedervorlage_view` AS `wv` ON wv.id = ws.view
                 ORDER BY ws.view, ws.sort ';
        $stages = $this->db->fetchAll($sql);

        $result = [];
        foreach ($stages as $stage) {
            $viewName = $stage['view_name'] !== null ? $stage['view_name'] : 'Standard';
            $result[] = [
                'id'    => (int)$stage['id'],
                'label' => sprintf('%s > %s', $viewName, $stage['stage_name']),
            ];
        }

        return $result;
    }

    /**
     * @param int $sourceStageId ID der Stage von der aus verschoben wird
     * @param int $targetStageId ID der Stage in die verschoben werden soll
     *
     * @throws StageNotFoundException
     *
     * @return int Positiver Wert = Target-Stage befindet sich in aufsteigender Position
     *             Negativer Wert = Target-Stage befindet sich vor der Ursprungs-Stage
     *                       Null = Source- und Target-Stage sind identisch
     */
    public function getDistanceBetweenStages($sourceStageId, $targetStageId)
    {
        $sourceStageId = (int)$sourceStageId;
        $targetStageId = (int)$targetStageId;
        $stages = $this->getSiblingStages($targetStageId);

        // Prüfen ob übergebene Stage-ID überhaupt valide ist; Muss in der selben Ansicht/View sein
        $validStageIds = array_column($stages, 'id');
        $isTargetStageIdValid = in_array($targetStageId, $validStageIds, true);
        if ($isTargetStageIdValid === false) {
            throw new StageNotFoundException(sprintf('Target stage ID "%s" not found', $targetStageId));
        }

        $targetStageRank = 0;
        $sourceStageRank = 0;
        $rank = 1;
        foreach ($stages as $stage) {
            if ((int)$stage['id'] === $sourceStageId) {
                $sourceStageRank = $rank;
            }
            if ((int)$stage['id'] === $targetStageId) {
                $targetStageRank = $rank;
            }
            $rank++;
        }

        return $targetStageRank - $sourceStageRank;
    }

    /**
     * @param int $resubmissionId
     *
     * @return array
     */
    public function getById($resubmissionId)
    {
        if (!is_numeric($resubmissionId)) {
            throw new ResubmissionNotFoundException(
                sprintf(
                    'Resubmission not found: ID%s',
                    $resubmissionId
                )
            );
        }

        $sql = 'SELECT w.id,
       w.adresse,
       w.projekt,
       w.parameter,
       w.abgeschlossen,
       w.action,
       w.adresse_mitarbeiter,
       w.bearbeiter,
       w.beschreibung,
       w.bezeichnung,
       w.betrag,
       w.ergebnis,
       w.erinnerung,
       w.erinnerung_empfaenger,
       w.datum_erinnerung,
       w.zeit_erinnerung,
       w.datum_status,
       w.datum_abschluss,
       w.datum_angelegt,
       w.stages,
       w.prio,
       w.color,
       w.chance,
       w.status,
       w.module,
       w.link,
       w.oeffentlich,
       w.erinnerung_per_mail,
       w.zeit_angelegt
       FROM `wiedervorlage` AS `w` WHERE w.id = :resubmission_id';

        return $this->db->fetchRow($sql, ['resubmission_id' => (int)$resubmissionId]);
    }

    /**
     * @param string $name
     * @param string $description
     *
     * @throws ViewNotFoundException
     *
     * @return int
     */
    public function getViewIdByNameAndDescription(string $name, string $description): int
    {
        $viewId = $this->db->fetchValue(
            'SELECT wv.id FROM `wiedervorlage_view` AS `wv`
                 WHERE wv.name = :name AND wv.shortname = :desc AND wv.active = :active AND wv.project = 0',
            [
                'name'   => $name,
                'desc'   => $description,
                'active' => 1,
            ]
        );

        if ($viewId === false) {
            throw new ViewNotFoundException(sprintf('View not found for name:%s and desc:%s', $name, $description));
        }

        return (int)$viewId;
    }

    /**
     * @param int $viewId
     *
     * @return int
     */
    public function getMaxSortByViewId(int $viewId): int
    {
        $maxPosition = $this->db->fetchValue(
            'SELECT MAX(ws.sort) FROM `wiedervorlage_stages` AS `ws` WHERE ws.`view` = :id',
            ['id' => $viewId]
        );

        return (int)$maxPosition;
    }
}
