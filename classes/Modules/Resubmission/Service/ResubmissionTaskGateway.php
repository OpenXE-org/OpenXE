<?php

namespace Xentral\Modules\Resubmission\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Resubmission\Exception\ResubmissionTaskNotFoundException;

final class ResubmissionTaskGateway
{
    /** @var ResubmissionGateway $gateway */
    private $gateway;

    /** @var Database $db */
    private $db;

    /**
     * @param Database            $db
     * @param ResubmissionGateway $gateway
     */
    public function __construct(Database $db, ResubmissionGateway $gateway)
    {
        $this->gateway = $gateway;
        $this->db = $db;
    }

    /**
     * @param int $resubmissionId Wiedervorlagen-ID
     *
     * @return array Empty array if no result
     */
    public function getTasksByResubmission($resubmissionId)
    {
        $sql =
            'SELECT
              a.id,
              CASE
                WHEN a.status = \'offen\' THEN \'open\'
                WHEN a.status = \'inbearbeitung\' THEN \'processing\'
                WHEN a.status = \'abgeschlossen\' THEN \'completed\'
              END AS `state`,
              CASE
                WHEN a.prio = \'1\' THEN \'high\'
                WHEN a.prio = \'-1\' THEN \'low\'
                ELSE \'medium\'
              END AS `priority`,
              a.adresse AS `address_id`,
              a.initiator AS `creator_address_id`,
              a.aufgabe AS `title`,
              a.beschreibung AS `description`,
              DATE_FORMAT(a.abgabe_bis, \'%d.%m.%Y\') AS `submission_date`,
              TIME_FORMAT(a.abgabe_bis_zeit, \'%H:%i\') AS `submission_time`,
              wa.required_completion_stage_id
            FROM `aufgabe` AS `a`
            INNER JOIN `wiedervorlage_aufgabe` AS `wa` ON wa.task_id = a.id
            INNER JOIN `wiedervorlage` AS `w` ON wa.resubmission_id = w.id
            WHERE w.id = :resubmission_id';

        return $this->db->fetchAll($sql, ['resubmission_id' => (int)$resubmissionId]);
    }

    /**
     * Ist die Aufgabe der übergebenen Wiedervorlage zugeordnet?
     *
     * Gleichzeitig wird auch geprüft ob Aufgabe existiert.
     *
     * @param int $taskId
     * @param int $resubmissionId
     *
     * @return bool
     */
    public function isTaskAssigendToResubmission($taskId, $resubmissionId)
    {
        $taskId = (int)$taskId;
        $resubmissionId = (int)$resubmissionId;
        if ($taskId <= 0 || $resubmissionId <= 0) {
            return false;
        }

        $sql = 'SELECT a.id 
                FROM `aufgabe` AS `a`
                INNER JOIN `wiedervorlage_aufgabe` AS `wa` ON wa.task_id = a.id
                WHERE wa.resubmission_id = :resubmission_id
                AND a.id = :task_id';
        $taskIdCheck = $this->db->fetchValue($sql, [
            'task_id'         => $taskId,
            'resubmission_id' => $resubmissionId,
        ]);

        return $taskId === $taskIdCheck;
    }

    /**
     * @param int $taskId
     *
     * @throws ResubmissionTaskNotFoundException
     *
     * @return array
     */
    public function getTask($taskId)
    {
        $sql =
            'SELECT
                  a.id,
                  w.stages AS `stage_id`,
                  a.projekt AS `project_id`,
                  a.teilprojekt AS `subproject_id`,
                  a.adresse AS `employee_id`,
                  CONCAT(adr.mitarbeiternummer, \' \', adr.name) AS `employee_name`,
                  CONCAT(a.kunde, \' \', cus.name, \' (Kdr: \', cus.kundennummer, \')\') as `customer`,  
                  a.aufgabe AS `title`,
                  a.beschreibung AS `description`,
                  DATE_FORMAT(a.abgabe_bis, \'%d.%m.%Y\') AS `submission_date`,
                  TIME_FORMAT(a.abgabe_bis_zeit, \'%H:%i\') AS `submission_time`,
                  CASE
                    WHEN a.status = \'offen\' THEN \'open\'
                    WHEN a.status = \'inbearbeitung\' THEN \'processing\'
                    WHEN a.status = \'abgeschlossen\' THEN \'completed\'
                  END AS `state`,
                  CASE
                    WHEN a.prio = \'1\' THEN \'high\'
                    WHEN a.prio = \'-1\' THEN \'low\'
                    ELSE \'medium\'
                  END AS `priority`,
                  wa.required_completion_stage_id
                FROM `aufgabe` AS `a`
                INNER JOIN `wiedervorlage_aufgabe` AS `wa` ON wa.task_id = a.id
                INNER JOIN `wiedervorlage` AS `w` ON wa.resubmission_id = w.id
                LEFT JOIN `adresse` AS adr ON a.adresse = adr.id
                LEFT JOIN `adresse` AS cus ON a.kunde = cus.id
                WHERE a.id = :task_id';

        $result = $this->db->fetchRow($sql, ['task_id' => (int)$taskId,]);

        if (empty($result)) {
            throw new ResubmissionTaskNotFoundException(sprintf('Task ID%s not found.', $taskId));
        }

        if (empty($result['submission_date']) || $result['submission_date'] === '00.00.0000') {
            $result['submission_date'] = null;
        }
        if (empty($result['submission_time']) || $result['submission_time'] === '00:00') {
            $result['submission_time'] = null;
        }

        return $result;
    }

    /**
     * @param int $resubmissionId Wiedervorlagen-ID
     * @param int $taskId         Aufgaben-ID
     *
     * @return array
     */
    public function getStages($resubmissionId, $taskId)
    {
        $sql =
            'SELECT w.stages AS `stage_id`, wa.required_completion_stage_id 
             FROM `wiedervorlage` AS `w`
             INNER JOIN `wiedervorlage_aufgabe` AS `wa` ON w.id = wa.resubmission_id 
             WHERE w.id = :resubmission_id AND wa.task_id = :task_id';
        $resubmission = $this->db->fetchRow($sql, [
            'resubmission_id' => (int)$resubmissionId,
            'task_id'         => (int)$taskId,
        ]);

        // View-ID der aktuellen Stage ermitteln; View-ID darf 0 sein; 0 = Standard-View
        $viewId = $this->gateway->getViewIdByStage($resubmission['stage_id']);

        // Alle Stages im gleichen View laden
        $stages = $this->gateway->getStagesByView($viewId);

        $rank = 1;
        $requiredBefore = false;
        foreach ($stages as &$stage) {
            $stage['rank'] = $rank++;
            $stage['current'] = (int)$stage['id'] === (int)$resubmission['stage_id'];
            $stage['required'] = (int)$stage['id'] === (int)$resubmission['required_completion_stage_id'];
            if ((int)$stage['id'] === (int)$resubmission['required_completion_stage_id']) {
                $requiredBefore = true;
            }
            $stage['required_before'] = $requiredBefore;
        }
        unset($stage);

        return $stages;
    }
}
