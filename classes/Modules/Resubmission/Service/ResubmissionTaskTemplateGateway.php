<?php

namespace Xentral\Modules\Resubmission\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Resubmission\Exception\ResubmissionNotFoundException;
use Xentral\Modules\Resubmission\Exception\TaskTemplateNotFoundException;

final class ResubmissionTaskTemplateGateway
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
   * @param int $taskTemplateId
   *
   * @return bool
   */
  public function existsTaskTemplate($taskTemplateId)
  {
    $sql =
      'SELECT COUNT(wav.id) AS `task_template_count` 
            FROM `wiedervorlage_aufgabe_vorlage` AS `wav`
            WHERE wav.id = :task_template_id';
    $taskTemplateCount = (int)$this->db->fetchValue($sql, ['task_template_id' => (int)$taskTemplateId]);

    return $taskTemplateCount === 1;
  }

  /**
   * @param int $taskTemplateId
   *
   * @throws TaskTemplateNotFoundException
   *
   * @return array
   */
  public function getTaskTemplateById($taskTemplateId)
  {
    $sql =
      'SELECT
                wav.id,
                wav.required_from_stage_id,
                wav.add_task_at_stage_id,
                CONCAT(adr.mitarbeiternummer, \' \', adr.name) AS `employee`,
                wav.project_id,
                wav.subproject_id,
                wav.title,
                wav.submission_date_days,
                TIME_FORMAT(wav.submission_time, \'%H:%i\') AS `submission_time`,
                wav.state,
                wav.priority,
                wav.description
       
            FROM `wiedervorlage_aufgabe_vorlage` AS `wav`
            LEFT JOIN `adresse` AS `adr` ON wav.employee_address_id = adr.id
            WHERE wav.id = :task_template_id';

    $result = $this->db->fetchRow($sql, ['task_template_id' => (int)$taskTemplateId]);
    if (empty($result)) {
      throw new TaskTemplateNotFoundException(sprintf(
        'Task template not found: ID %s', $taskTemplateId
      ));
    }

    if (empty($result['required_from_stage_id'])) {
      $result['required_from_stage_id'] = 0;
    }
    if (empty($result['add_task_at_stage_id'])) {
      $result['add_task_at_stage_id'] = 0;
    }
    if (empty($result['employee_address_id'])) {
      $result['employee_address_id'] = 0;
    }
    if (empty($result['project_id'])) {
      $result['project_id'] = 0;
    }
    if (empty($result['subproject_id'])) {
      $result['subproject_id'] = 0;
    }
    if (empty($result['submission_time']) || $result['submission_time'] === '00:00') {
      $result['submission_time'] = null;
    }


    return $result;
  }


    function getTaskTemplatesByStageId($stageId){
        $sql =
            'SELECT
                wav.id,
                wav.required_from_stage_id,
                wav.add_task_at_stage_id,
                IFNULL(wav.employee_address_id, 0) AS employee_address_id,
                wav.project_id,
                wav.subproject_id,
                wav.title,
                wav.submission_date_days,
                TIME_FORMAT(wav.submission_time, \'%H:%i\') AS `submission_time`,
                wav.state,
                wav.priority,
                wav.description
       
            FROM `wiedervorlage_aufgabe_vorlage` AS `wav`
            LEFT JOIN `adresse` AS `adr` ON wav.employee_address_id = adr.id
            WHERE wav.add_task_at_stage_id = :stage_id';

        $result = $this->db->fetchAll($sql, ['stage_id' => (int)$stageId]);
        if (empty($result)) {
            throw new TaskTemplateNotFoundException(sprintf(
                                                        'Task template with following stage id not found: ID %s', $stageId
                                                    ));
        }

        return $result;
    }


}
