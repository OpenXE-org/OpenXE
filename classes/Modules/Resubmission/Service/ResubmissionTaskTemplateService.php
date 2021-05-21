<?php

namespace Xentral\Modules\Resubmission\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Resubmission\Data\TaskTemplateData;
use Xentral\Modules\Resubmission\Exception\TaskTemplateNotFoundException;
use Xentral\Modules\Resubmission\Exception\ValidationFailedException;

final class ResubmissionTaskTemplateService
{
  /** @var Database $db */
  private $db;

  /** @var ResubmissionTaskTemplateGateway $taskTemplateGateway */
  private $taskTemplateGateway;

  /** @var ResubmissionGateway $resubmissionGateway */
  private $resubmissionGateway;

  /**
   * @param Database                        $database
   * @param ResubmissionTaskTemplateGateway $taskTemplateGateway
   * @param ResubmissionGateway             $resubmissionGateway
   */
  public function __construct(
    Database $database,
    ResubmissionTaskTemplateGateway $taskTemplateGateway,
    ResubmissionGateway $resubmissionGateway
  ) {
    $this->db = $database;
    $this->taskTemplateGateway = $taskTemplateGateway;
    $this->resubmissionGateway = $resubmissionGateway;
  }

  /**
   * @param TaskTemplateData $config
   *
   * @throws ValidationFailedException
   *
   * @return int Inserted id
   */
  public function createTaskTemplate(TaskTemplateData $config)
  {
    if ($config->id !== null) {
      $errorMsg = sprintf('The "id" property must be null. Given value: "%s".', $config->id);
      throw ValidationFailedException::fromErrors(['id' => [$errorMsg]]);
    }

    // Prüfen ob add_task_at_stage_id und required_from_stage_id im gleichen View sind
    if ($config->addTaskAtStageId > 0 && $config->requiredFromStageId > 0) {
      $addTaskAtViewId = $this->resubmissionGateway->getViewIdByStage($config->addTaskAtStageId);
      $requiredFromViewId = $this->resubmissionGateway->getViewIdByStage($config->requiredFromStageId);
      if ($addTaskAtViewId !== $requiredFromViewId) {
        $errorMsg = 'The "add_task_at_stage_id" and the "required_from_stage_id" must be ';
        $errorMsg .= 'on the same View.';
        throw ValidationFailedException::fromErrors(['add_task_at_stage_id' => [$errorMsg]]);
      }
    }

    $sql = 'INSERT INTO `wiedervorlage_aufgabe_vorlage` 
                (
                    `id`, `required_from_stage_id`, `add_task_at_stage_id`, `employee_address_id`, 
                    `project_id`, `subproject_id`, `title`, `submission_date_days`, `submission_time`,
                    `state`, `priority`, `description`
                ) VALUES (
                    NULL, :required_from_stage_id, :add_task_at_stage_id, :employee_address_id,
                    :project_id, :subproject_id, :title, :submission_date_days, :submission_time,
                    :state, :priority, :description
                )';
    $bindValues = [
      'required_from_stage_id'  => $config->requiredFromStageId,
      'add_task_at_stage_id'    => $config->addTaskAtStageId,
      'employee_address_id'     => $config->employeeAddressId,
      'project_id'              => $config->projectId,
      'subproject_id'           => $config->subprojectId,
      'title'                   => $config->title,
      'submission_date_days'    => $config->submissionDateDays,
      'submission_time'         => $config->submissionTime,
      'state'                   => $config->state,
      'priority'                => $config->priority,
      'description'             => $config->description
    ];

    $this->db->perform($sql, $bindValues);
    $taskTemplateId = $this->db->lastInsertId();

    return $taskTemplateId;
  }

  /**
   * @param TaskTemplateData $config
   *
   * @throws TaskTemplateNotFoundException
   * @throws ValidationFailedException
   *
   * @return void
   */
  public function modifyTaskTemplate(TaskTemplateData $config)
  {
    if (!$this->taskTemplateGateway->existsTaskTemplate($config->id)) {
      throw new TaskTemplateNotFoundException(sprintf(
        'Task template not found: ID%s', $config->id
      ));
    }

    // Prüfen ob add_task_at_stage_id und required_from_stage_id im gleichen View sind
    if ($config->addTaskAtStageId > 0 && $config->requiredFromStageId > 0) {
      $addTaskAtViewId = $this->resubmissionGateway->getViewIdByStage($config->addTaskAtStageId);
      $requiredFromViewId = $this->resubmissionGateway->getViewIdByStage($config->requiredFromStageId);
      if ($addTaskAtViewId !== $requiredFromViewId) {
        $errorMsg = 'The "add_task_at_stage_id" and the "required_from_stage_id" must be ';
        $errorMsg .= 'on the same View.';
        throw ValidationFailedException::fromErrors(['add_task_at_stage_id' => [$errorMsg]]);
      }
    }

    $sql = 'UPDATE `wiedervorlage_aufgabe_vorlage` 
                SET 
                    `required_from_stage_id` = :required_from_stage_id,
                    `add_task_at_stage_id` = :add_task_at_stage_id,
                    `employee_address_id` = :employee_address_id,
                    `project_id` = :project_id,
                    `subproject_id` = :subproject_id,
                    `title` = :title,
                    `submission_date_days` = :submission_date_days,
                    `submission_time` = :submission_time,
                    `state` = :state,
                    `priority` = :priority,
                    `description` = :description
                WHERE `id` = :id
                LIMIT 1';
    $bindValues = [
      'id'                      => $config->id,
      'required_from_stage_id'  => $config->requiredFromStageId,
      'add_task_at_stage_id'    => $config->addTaskAtStageId,
      'employee_address_id'     => $config->employeeAddressId,
      'project_id'              => $config->projectId,
      'subproject_id'           => $config->subprojectId,
      'title'                   => $config->title,
      'submission_date_days'    => $config->submissionDateDays,
      'submission_time'         => $config->submissionTime,
      'state'                   => $config->state,
      'priority'                => $config->priority,
      'description'             => $config->description
    ];

    $this->db->perform($sql, $bindValues);
  }

  /**
   * @param int $taskTemplateId
   *
   * @throws TaskTemplateNotFoundException
   *
   * @return void
   */
  public function deleteTaskTemplateById($taskTemplateId)
  {
    if (!$this->taskTemplateGateway->existsTaskTemplate($taskTemplateId)) {
      throw new TaskTemplateNotFoundException(sprintf(
        'Task template not found: ID%s', $taskTemplateId
      ));
    }

    $sql = 'DELETE FROM `wiedervorlage_aufgabe_vorlage` WHERE `id` = :id LIMIT 1';
    $bindValues = ['id' => (int)$taskTemplateId];

    $this->db->perform($sql, $bindValues);
  }


}
