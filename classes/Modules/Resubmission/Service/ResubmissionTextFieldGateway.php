<?php

namespace Xentral\Modules\Resubmission\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Resubmission\Exception\ResubmissionNotFoundException;
use Xentral\Modules\Resubmission\Exception\TextFieldConfigNotFoundException;

final class ResubmissionTextFieldGateway
{
    /** @var Database $db */
    private $db;

    /** @var ResubmissionGateway $resubmissionGateway */
    private $resubmissionGateway;

    /**
     * @param Database            $database
     * @param ResubmissionGateway $resubmissionGateway
     */
    public function __construct(Database $database, ResubmissionGateway $resubmissionGateway)
    {
        $this->db = $database;
        $this->resubmissionGateway = $resubmissionGateway;
    }

    /**
     * @param int $configId
     *
     * @return bool
     */
    public function existsConfig($configId)
    {
        $sql =
            'SELECT COUNT(wfk.id) AS `config_count` 
            FROM `wiedervorlage_freifeld_konfiguration` AS `wfk`
            WHERE wfk.id = :config_id';
        $configCount = (int)$this->db->fetchValue($sql, ['config_id' => (int)$configId]);

        return $configCount === 1;
    }

    /**
     * @param int $configId
     *
     * @throws TextFieldConfigNotFoundException
     *
     * @return array
     */
    public function getConfigById($configId)
    {
        $sql =
            'SELECT
                wfk.id,
                wfk.title,
                wfk.available_from_stage_id,
                wfk.required_from_stage_id,
                wfk.show_in_pipeline,
                wfk.show_in_tables
            FROM `wiedervorlage_freifeld_konfiguration` AS `wfk`
            WHERE wfk.id = :config_id';

        $result = $this->db->fetchRow($sql, ['config_id' => (int)$configId]);
        if (empty($result)) {
            throw new TextFieldConfigNotFoundException(sprintf(
                'Text field config not found: ID%s', $configId
            ));
        }

        if (empty($result['available_from_stage_id'])) {
            $result['available_from_stage_id'] = 0;
        }
        if (empty($result['required_from_stage_id'])) {
            $result['required_from_stage_id'] = 0;
        }
        $result['show_in_pipeline'] = (int)$result['show_in_pipeline'] === 1;
        $result['show_in_tables'] = (int)$result['show_in_tables'] === 1;

        return $result;
    }

    /**
     * @example Rückgabe: ['freifeld1' => 'Mein Freifeld 1', 'freifeld2' => 'Mein Freifeld 2']
     *
     * @param int $viewId
     *
     * @return array
     */
    public function getTextFieldsForTableView($viewId)
    {
        $stages = $this->resubmissionGateway->getStagesByView($viewId);
        $stageIds = array_column($stages, 'id');

        if (empty($stageIds)) {
            return [];
        }

        $sql =
            'SELECT CONCAT(\'freifeld\', wfk.id) AS name, wfk.title 
             FROM `wiedervorlage_freifeld_konfiguration` AS `wfk` 
             WHERE wfk.show_in_tables = 1
             AND (
                 wfk.available_from_stage_id = 0 
                 OR wfk.available_from_stage_id IN (:stages_ids)
                 OR wfk.required_from_stage_id IN (:stages_ids)
             )';

        return $this->db->fetchPairs($sql, ['stages_ids' => $stageIds]);
    }

    /**
     * @example Rückgabe: ['freifeld1' => 'Mein Freifeld 1', 'freifeld2' => 'Mein Freifeld 2']
     *
     * @param int $viewId
     *
     * @return array
     */
    public function getTextFieldsForPipelineView($viewId)
    {
        $stages = $this->resubmissionGateway->getStagesByView($viewId);
        $stageIds = array_column($stages, 'id');

        if (empty($stageIds)) {
            return [];
        }

        $sql =
            'SELECT CONCAT(\'freifeld\', wfk.id) AS name, wfk.title 
             FROM `wiedervorlage_freifeld_konfiguration` AS `wfk` 
             WHERE wfk.show_in_pipeline = 1
             AND (
                wfk.available_from_stage_id = 0 
                OR wfk.available_from_stage_id IN (:stages_ids)
                OR wfk.required_from_stage_id IN (:stages_ids)
             )';

        return $this->db->fetchPairs($sql, ['stages_ids' => $stageIds]);
    }

    /**
     * Lädt alle Freitextfelder die in der übergebenen Stage ein Pflichtfeld sind
     *
     * @param int $stageId
     *
     * @return array
     */
    public function getRequiredTextFieldsForStage($stageId)
    {
        // Schritt 1
        // Alle Stage-IDs ermittlen für die wir die Freitextfelder laden sollen
        // d.h. alle Stages die in der Reihenfolge vor der aktuellen Stage kommen; inklusive der aktuellen Stage
        $validStages = $this->getStagesUntilStageId($stageId);
        $validStageIds = array_column($validStages, 'id');

        // Schritt 2
        // Alle Pflicht-Freitextfelder für die Stages aus Schritt 1 ermitteln
        $sql =
            'SELECT 
                 wfk.id AS `config_id`, 
                 wfk.title AS `label`, 
                 wfk.available_from_stage_id, 
                 wfk.required_from_stage_id,
                 ws.name AS `required_from_stage_name`,
                 wfk.show_in_pipeline,
                 wfk.show_in_tables
             FROM `wiedervorlage_freifeld_konfiguration` AS `wfk`
             LEFT JOIN `wiedervorlage_stages` AS `ws` ON wfk.required_from_stage_id = ws.id
             WHERE wfk.required_from_stage_id IN (:valid_stage_ids)';

        return $this->db->fetchAll($sql, [
            'valid_stage_ids' => $validStageIds,
        ]);
    }

    /**
     * Lädt alle Stages die in der Reihenfolge vor der übergebenen Stage liegen
     *
     * @refactor ResubmissionGateway
     *
     * @param int $stageId
     *
     * @return array
     */
    private function getStagesUntilStageId($stageId)
    {
        // Alle Stages in der gleichen View ermitteln
        $stageId = (int)$stageId;
        $stages = $this->resubmissionGateway->getSiblingStages($stageId);

        // Rang der übergebenen Stage ermitteln
        $currentRank = 0;
        foreach ($stages as $stage) {
            if ($stage['id'] === $stageId) {
                $currentRank = $stage['rank'];
            }
        }

        // Alle Stages filtern die VOR der übergebenen Stage liegen (inklusive der übergebenen Stage)
        $validStages = [];
        foreach ($stages as $stage) {
            if ($stage['rank'] <= $currentRank) {
                $validStages[] = $stage;
            }
        }

        return $validStages;
    }

    /**
     * Ermittelt alle Freifelder die das Verschieben einer Wiedervorlage blockieren
     *
     * @param int $resubmissionId
     * @param int $targetStageId
     *
     * @return array Empty array if none item is blocking
     */
    public function getBlockingTextFieldsForTargetStage($resubmissionId, $targetStageId)
    {
        $resubmissionId = (int)$resubmissionId;
        $targetStageId = (int)$targetStageId;

        $textfields = $this->getTextFieldsForResubmission($resubmissionId);

        $blocking = [];
        foreach ($textfields as $textfield) {
            if (!empty($textfield['content'])) {
                continue; // Textfeld ist ausgefüllt > Textfeld darf in jede Stage geschoben werden
            }
            if ((int)$textfield['required_from_stage_id'] === 0) {
                continue; // Textfeld hat keine Fertigstellungs-Stage hinterlegt > Textfeld darf in jede Stage geschoben werden
            }

            $distance = $this->resubmissionGateway->getDistanceBetweenStages(
                $targetStageId, $textfield['required_from_stage_id']
            );
            if ($distance <= 0) {
                $blocking[] = [
                    'config_id'  => $textfield['config_id'],
                    'content_id' => $textfield['content_id'],
                    'label'      => $textfield['label'],
                ];
            }
        }

        return $blocking;
    }

    /**
     * Lädt alle Freitextfelder für eine Wiedervorlage
     *
     * @param int $resubmissionId
     *
     * @throws ResubmissionNotFoundException
     *
     * @return array
     */
    public function getTextFieldsForResubmission($resubmissionId)
    {
        $stage = $this->resubmissionGateway->getStageByResubmission($resubmissionId);
        $viewId = (int)$stage['view_id']; // View-ID `0` ist zulässig

        // 1. Alle Stage-IDs ermittlen für die wir die Freitextfelder laden sollen
        // d.h. alle Stages die in der Reihenfolge vor der aktuellen Stage kommen; inklusive der aktuellen Stage
        $validStages = $this->getStagesUntilStageId($stage['id']);
        $validStageIds = array_column($validStages, 'id');

        // 2. Alle Freitextfelder für die Stages aus Schritt 1 ermitteln
        $sql =
            'SELECT 
                 wfk.id AS `config_id`, 
                 wfi.id AS `content_id`, 
                 wfk.title AS `label`, 
                 wfk.available_from_stage_id, 
                 wfk.required_from_stage_id,
                 wfk.show_in_pipeline,
                 wfk.show_in_tables
             FROM `wiedervorlage_freifeld_konfiguration` AS `wfk`
             LEFT JOIN `wiedervorlage_freifeld_inhalt` AS `wfi` 
                 ON wfi.resubmission_id = :resubmission_id
             LEFT JOIN `wiedervorlage_stages` AS ws1 
                 ON wfk.required_from_stage_id = ws1.id
             LEFT JOIN `wiedervorlage_stages` AS ws2 
                 ON wfk.available_from_stage_id = ws2.id
             WHERE wfk.available_from_stage_id = 0 
                OR wfk.available_from_stage_id IN (:valid_stage_ids) 
                OR wfk.required_from_stage_id IN (:valid_stage_ids) 
             ORDER BY wfk.required_from_stage_id != 0 DESC, ws1.sort, ws2.sort, wfk.title';
        // Erklärung Sortierung:
        // 1. `wfk.required_from_stage_id != 0 DESC` = Felder mit Pflicht-Stage oben anzeigen
        // 2. `ws1.sort, ws2.sort` = Felder nach Reihenfolge der Stages anzeigen

        $textFields = $this->db->fetchAll($sql, [
            'resubmission_id' => $resubmissionId,
            'valid_stage_ids' => $validStageIds,
            'view_id'         => $viewId,
        ]);

        // Freitextfeld-Inhalte ergänzen
        $contents = $this->getTextFieldContentsForResubmission($resubmissionId);
        foreach ($textFields as &$textField) {
            $configId = (int)$textField['config_id'];
            $textField['content'] = $contents[$configId];
        }
        unset($textField);

        return $textFields;
    }

    /**
     * @param int $resubmissionId
     *
     * @return array
     */
    private function getTextFieldContentsForResubmission($resubmissionId)
    {
        $columnNames = $this->getTextFieldColumnNames();

        $contentsAll = $this->db->fetchRow(
            'SELECT wfi.* FROM `wiedervorlage_freifeld_inhalt` AS `wfi` 
             WHERE wfi.resubmission_id = :resubmission_id LIMIT 1',
            ['resubmission_id' => (int)$resubmissionId]
        );

        $contents = [];
        foreach ($columnNames as $configId => $columnName) {
            if (isset($contentsAll[$columnName])) {
                $contents[$configId] = $contentsAll[$columnName];
            } else {
                $contents[$configId] = null;
            }
        }

        return $contents;
    }

    /**
     * @param string|null $tableAlias
     *
     * @return array
     */
    private function getTextFieldColumnNames($tableAlias = null)
    {
        $sql = 'SELECT wfk.id FROM `wiedervorlage_freifeld_konfiguration` AS `wfk` WHERE 1';
        $configIds = $this->db->fetchCol($sql);

        $columnNames = [];
        $columnPrefix = $tableAlias !== null ? sprintf('%s.', $tableAlias) : '';
        foreach ($configIds as $configId) {
            $columnNames[$configId] = sprintf('%sfreifeld%s', $columnPrefix, $configId);
        }

        return $columnNames;
    }
}
