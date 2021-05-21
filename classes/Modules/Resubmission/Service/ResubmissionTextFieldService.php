<?php

namespace Xentral\Modules\Resubmission\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Resubmission\Data\FreeTextFieldConfigData;
use Xentral\Modules\Resubmission\Data\FreeTextFieldContentData;
use Xentral\Modules\Resubmission\Exception\TextFieldConfigNotFoundException;
use Xentral\Modules\Resubmission\Exception\TextFieldRequiredException;
use Xentral\Modules\Resubmission\Exception\ValidationFailedException;
use Xentral\Modules\Resubmission\Exception\ResubmissionNotFoundException;

final class ResubmissionTextFieldService
{
    /** @var Database $db */
    private $db;

    /** @var ResubmissionTextFieldGateway $textFieldGateway */
    private $textFieldGateway;

    /** @var ResubmissionGateway $resubmissionGateway */
    private $resubmissionGateway;

    /**
     * @param Database                     $database
     * @param ResubmissionTextFieldGateway $textFieldGateway
     * @param ResubmissionGateway          $resubmissionGateway
     */
    public function __construct(
        Database $database,
        ResubmissionTextFieldGateway $textFieldGateway,
        ResubmissionGateway $resubmissionGateway
    ) {
        $this->db = $database;
        $this->textFieldGateway = $textFieldGateway;
        $this->resubmissionGateway = $resubmissionGateway;
    }

    /**
     * @param FreeTextFieldConfigData $config
     *
     * @throws ValidationFailedException
     *
     * @return int Inserted id
     */
    public function createConfig(FreeTextFieldConfigData $config)
    {
        if ($config->id !== null) {
            $errorMsg = sprintf('The "id" property must be null. Given value: "%s".', $config->id);
            throw ValidationFailedException::fromErrors(['id' => [$errorMsg]]);
        }

        // @todo 1. Prüfen ob available_from_stage_id vor required_from_stage_id kommt;
        // @todo    Nur wenn available_from_stage_id > 0 UND required_from_stage_id > 0

        // Prüfen ob available_from_stage_id und required_from_stage_id im gleichen View sind
        if ($config->availableFromStageId > 0 && $config->requiredFromStageId > 0) {
            $availableFromViewId = $this->resubmissionGateway->getViewIdByStage($config->availableFromStageId);
            $requiredFromViewId = $this->resubmissionGateway->getViewIdByStage($config->requiredFromStageId);
            if ($availableFromViewId !== $requiredFromViewId) {
                $errorMsg = 'The "available_from_stage_id" and the "required_from_stage_id" must be ';
                $errorMsg .= 'on the same View.';
                throw ValidationFailedException::fromErrors(['available_from_stage_id' => [$errorMsg]]);
            }
        }

        $sql = 'INSERT INTO `wiedervorlage_freifeld_konfiguration` 
                (
                    `id`, `title`, `show_in_pipeline`, `show_in_tables`, 
                    `available_from_stage_id`, `required_from_stage_id`, `created_at`, `updated_at`
                ) VALUES (
                    NULL, :title, :show_in_pipeline, :show_in_tables,
                    :available_from_stage_id, :required_from_stage_id, NOW(), NULL
                )';
        $bindValues = [
            'title'                   => $config->title,
            'show_in_pipeline'        => $config->showInPipeline === true ? 1 : 0,
            'show_in_tables'          => $config->showInTables === true ? 1 : 0,
            'available_from_stage_id' => $config->availableFromStageId,
            'required_from_stage_id'  => $config->requiredFromStageId,
        ];

        $this->db->perform($sql, $bindValues);
        $configId =  $this->db->lastInsertId();

        // Sicherstellen dass Freitext-Spalte existiert
        $this->checkCreateContentColumn($configId);

        return $configId;
    }

    /**
     * @param FreeTextFieldConfigData $config
     *
     * @throws TextFieldConfigNotFoundException
     * @throws ValidationFailedException
     *
     * @return void
     */
    public function modifyConfig(FreeTextFieldConfigData $config)
    {
        if (!$this->textFieldGateway->existsConfig($config->id)) {
            throw new TextFieldConfigNotFoundException(sprintf(
                'Text field config not found: ID%s', $config->id
            ));
        }

        // @todo Prüfen ob available_from_stage_id vor required_from_stage_id kommt;
        // @todo Nur wenn available_from_stage_id > 0 UND required_from_stage_id > 0

        // Prüfen ob available_from_stage_id und required_from_stage_id im gleichen View sind
        if ($config->availableFromStageId > 0 && $config->requiredFromStageId > 0) {
            $availableFromViewId = $this->resubmissionGateway->getViewIdByStage($config->availableFromStageId);
            $requiredFromViewId = $this->resubmissionGateway->getViewIdByStage($config->requiredFromStageId);
            if ($availableFromViewId !== $requiredFromViewId) {
                $errorMsg = 'The "available_from_stage_id" and the "required_from_stage_id" must be ';
                $errorMsg .= 'on the same View.';
                throw ValidationFailedException::fromErrors(['available_from_stage_id' => [$errorMsg]]);
            }
        }

        // Sicherstellen dass Freitext-Spalte existiert
        $this->checkCreateContentColumn($config->id);

        $sql = 'UPDATE `wiedervorlage_freifeld_konfiguration` 
                SET 
                    `title` = :title,
                    `show_in_pipeline` = :show_in_pipeline,
                    `show_in_tables` = :show_in_tables,
                    `available_from_stage_id` = :available_from_stage_id,
                    `required_from_stage_id` = :required_from_stage_id,
                    `updated_at` = NOW()
                WHERE `id` = :id
                LIMIT 1';
        $bindValues = [
            'id'                      => $config->id,
            'title'                   => $config->title,
            'show_in_pipeline'        => $config->showInPipeline === true ? 1 : 0,
            'show_in_tables'          => $config->showInTables === true ? 1 : 0,
            'available_from_stage_id' => $config->availableFromStageId,
            'required_from_stage_id'  => $config->requiredFromStageId,
        ];

        $this->db->perform($sql, $bindValues);
    }

    /**
     * @param int $configId
     *
     * @throws TextFieldConfigNotFoundException
     *
     * @return void
     */
    public function deleteConfigById($configId)
    {
        if (!$this->textFieldGateway->existsConfig($configId)) {
            throw new TextFieldConfigNotFoundException(sprintf(
                'Text field config not found: ID%s', $configId
            ));
        }

        $sql = 'DELETE FROM `wiedervorlage_freifeld_konfiguration` WHERE `id` = :id LIMIT 1';
        $bindValues = ['id' => (int)$configId];

        // Inhalts-Tabelle `wiedervorlage_freifeld_inhalt` nicht anpassen, sonst Datenverlust

        $this->db->perform($sql, $bindValues);
    }

    /**
     * Alle Freifeld-Inhalte für eine Wiedervorlage speichern
     *
     * WICHTIG: Es müssen alle Pflicht-Freitexte mitgeschickt werden
     *          Optionale Felder die nicht mitgeschickt werden, werden nicht verändert.
     *
     * @example $contents = [123 => 'Inhalt für das Freifeld mit der Freifeld-Config-ID 123']
     *
     * @param int   $resubmissionId
     * @param array $contents
     *
     * @throws ResubmissionNotFoundException
     * @throws TextFieldRequiredException
     *
     * @return void
     */
    public function saveAllFieldContents($resubmissionId, array $contents)
    {
        if (!$this->resubmissionGateway->existsResubmission($resubmissionId)) {
            throw new ResubmissionNotFoundException(sprintf('Resubmission not found: ID%s', $resubmissionId));
        }

        $stage = $this->resubmissionGateway->getStageByResubmission($resubmissionId);
        $requiredFields = $this->textFieldGateway->getRequiredTextFieldsForStage($stage['id']);

        // Prüfen ob Pflichtfeld leer
        foreach ($requiredFields as $requiredField) {
            $configId = (int)$requiredField['config_id'];
            if (empty($contents[$configId])) {
                throw TextFieldRequiredException::onEmpty(
                    $requiredField['label'],
                    $requiredField['required_from_stage_name']
                );
            }
        }

        foreach ($contents as $configId => $content) {
            $textfield = new FreeTextFieldContentData();
            $textfield->resubmissionId = (int)$resubmissionId;
            $textfield->configId = (int)$configId;
            $textfield->content = (string)$content;
            $this->updateFieldContent($textfield);
        }
    }

    /**
     * Vorhandenen Freifeld-Inhalt bearbeiten
     *
     * @param FreeTextFieldContentData $textfield
     *
     * @throws ValidationFailedException
     *
     * @return void
     */
    private function updateFieldContent(FreeTextFieldContentData $textfield)
    {
        $errors = $textfield->validate();
        if (!empty($errors)) {
            throw ValidationFailedException::fromErrors($errors);
        }

        // Sicherstellen dass Freitext-Zeile für Wiedervorlage existiert
        $contentId = $this->getCreateContentRowId($textfield->resubmissionId);
        $columnName = sprintf('freifeld%s', (int)$textfield->configId);

        $sql = sprintf(
            'UPDATE `wiedervorlage_freifeld_inhalt` 
             SET %s = :content
             WHERE `id` = :content_id AND `resubmission_id` = :resubmission_id
             LIMIT 1',
            $this->db->escapeIdentifier($columnName)
        );
        $bindValues = [
            'content_id'          => $contentId,
            'resubmission_id'     => $textfield->resubmissionId,
            'content'             => !empty($textfield->content) ? $textfield->content : null,
        ];

        $this->db->perform($sql, $bindValues);
    }

    /**
     * Holt die ID einer Freifeld-Zeile; Zeile wird angelegt wenn nicht vorhanden
     *
     * @param int $resubmissionId
     *
     * @return int Primary ID aus Freifeld-Inhalts-Tabelle
     */
    private function getCreateContentRowId($resubmissionId)
    {
        $sql = 'SELECT wfi.id FROM `wiedervorlage_freifeld_inhalt` AS `wfi` WHERE resubmission_id = :resubmission_id';
        $contentId = (int)$this->db->fetchValue($sql, ['resubmission_id' => (int)$resubmissionId]);

        if ($contentId === 0) {
            $this->db->perform(
                'INSERT INTO `wiedervorlage_freifeld_inhalt` (`id`, `resubmission_id`) VALUES (NULL, :resubmission_id)',
                ['resubmission_id' => (int)$resubmissionId]
            );
            $contentId = (int)$this->db->lastInsertId();
        }

        return $contentId;
    }

    /**
     * Stellt sicher dass eine Freifeld-Spalte für eine Config-ID existiert
     *
     * @param int $configId
     *
     * @return void
     */
    private function checkCreateContentColumn($configId)
    {
        if (!$this->textFieldGateway->existsConfig($configId)) {
            return;
        }

        if ($this->existsContentColumn($configId)) {
            return;
        }

        $this->db->exec(sprintf(
            'ALTER TABLE `wiedervorlage_freifeld_inhalt` 
             ADD `freifeld%s` VARCHAR(255) NULL DEFAULT NULL; ',
            (int)$configId
        ));
    }

    /**
     * Prüft ob die Freifeld-Spalte für eine Config-ID existiert
     *
     * @param int $configId
     *
     * @return bool
     */
    private function existsContentColumn($configId)
    {
        $columnName = 'freifeld' . (int)$configId;
        $exists = $this->db->fetchAll(sprintf(
            'SHOW COLUMNS FROM `wiedervorlage_freifeld_inhalt` LIKE %s;',
            $this->db->escapeString($columnName)
        ));

        return count($exists) === 1;
    }
}
