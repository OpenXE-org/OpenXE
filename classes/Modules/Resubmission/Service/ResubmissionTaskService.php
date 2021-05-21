<?php

namespace Xentral\Modules\Resubmission\Service;

use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\DatabaseExceptionInterface;
use Xentral\Modules\Resubmission\Data\ResubmissionTaskData;
use Xentral\Modules\Resubmission\Exception\InvalidArgumentException;
use Xentral\Modules\Resubmission\Exception\ResubmissionNotFoundException;
use Xentral\Modules\Resubmission\Exception\ResubmissionTaskNotFoundException;
use Xentral\Modules\Resubmission\Exception\TaskMustBeCompletedException;

final class ResubmissionTaskService
{
    /** @var Database $db */
    private $db;

    /** @var ResubmissionTaskGateway $taskGateway */
    private $taskGateway;

    /** @var ResubmissionGateway $resubmissionGateway */
    private $resubmissionGateway;

    /**
     * @param Database                $database
     * @param ResubmissionTaskGateway $taskGateway
     * @param ResubmissionGateway     $resubmissionGateway
     */
    public function __construct(
        Database $database,
        ResubmissionTaskGateway $taskGateway,
        ResubmissionGateway $resubmissionGateway
    ) {
        $this->db = $database;
        $this->taskGateway = $taskGateway;
        $this->resubmissionGateway = $resubmissionGateway;
    }

    /**
     * Aufgabe auf "abgeschlossen" stellen
     *
     * @param int $taskId
     * @param int $resubmissionId
     *
     * @throws ResubmissionTaskNotFoundException
     *
     * @return void
     */
    public function markTaskAsCompleted($taskId, $resubmissionId)
    {
        if (!$this->taskGateway->isTaskAssigendToResubmission($taskId, $resubmissionId)) {
            throw new ResubmissionTaskNotFoundException(sprintf(
                'Task not found. Task-ID: %s - Resubmission-ID: %s', $taskId, $resubmissionId
            ));
        }

        $sql = 'UPDATE `aufgabe` 
            SET `status` = :state, `abgeschlossen_am` = :completion_date, `logdatei` = NOW() 
            WHERE `id` = :task_id
            LIMIT 1';
        $this->db->perform($sql, [
            'state'           => 'abgeschlossen',
            'completion_date' => date('Y-m-d'),
            'task_id'         => (int)$taskId,
        ]);
    }

    /**
     * Aufgabe auf "offen" stellen
     *
     * @param int $taskId
     * @param int $resubmissionId
     *
     * @throws ResubmissionTaskNotFoundException
     * @throws TaskMustBeCompletedException
     *
     * @return void
     */
    public function markTaskAsOpen($taskId, $resubmissionId)
    {
        if (!$this->taskGateway->isTaskAssigendToResubmission($taskId, $resubmissionId)) {
            throw new ResubmissionTaskNotFoundException(sprintf(
                'Task not found. Task-ID: %s - Resubmission-ID: %s', $taskId, $resubmissionId
            ));
        }

        $task = $this->taskGateway->getTask($taskId);

        // Vor der Änderung prüfen ob Änderung überhaupt gültig wäre
        $check = $this->isTaskStageChangeAllowed(
            $task['stage_id'],
            $task['required_completion_stage_id'],
            ResubmissionTaskData::STATE_OPEN
        );
        if (!$check) {
            $currentStage = $this->resubmissionGateway->getStage($task['stage_id']);
            $requiredStage = $this->resubmissionGateway->getStage($task['required_completion_stage_id']);
            throw TaskMustBeCompletedException::onChangingStateToOpen(
                $requiredStage['shortname'],
                $currentStage['shortname']
            );
        }

        $sql = 'UPDATE `aufgabe` 
                SET `status` = :state, `abgeschlossen_am` = :completion_date, `logdatei` = NOW() 
                WHERE `id` = :task_id
                LIMIT 1';
        $this->db->fetchAffected($sql, [
            'state'           => 'offen',
            'completion_date' => '0000-00-00',
            'task_id'         => (int)$taskId,
        ]);
    }

    /**
     * @param ResubmissionTaskData $task
     *
     * @throws ResubmissionTaskNotFoundException
     * @throws TaskMustBeCompletedException
     * @throws DatabaseExceptionInterface
     *
     * @return void
     */
    public function createTask(ResubmissionTaskData $task)
    {
        if (!$this->resubmissionGateway->existsResubmission($task->getResubmissionId())) {
            throw new ResubmissionNotFoundException(sprintf(
                'Resubmission not found. ID: %s', $task->getResubmissionId()
            ));
        }

        // Vor der Erstellung prüfen ob Task-Status überhaupt gültig wäre
        $currentStage = $this->resubmissionGateway->getStageByResubmission($task->getResubmissionId());
        $check = $this->isTaskStageChangeAllowed(
            $currentStage['id'],
            $task->getRequiredCompletionStageId(),
            $task->getState()
        );
        if (!$check) {
            $requiredStage = $this->resubmissionGateway->getStage($task->getRequiredCompletionStageId());
            throw TaskMustBeCompletedException::onCreation(
                $requiredStage['shortname'],
                $currentStage['shortname']
            );
        }

        $priority = $this->translatePriorityToDbValue($task->getPriority());
        $state = $this->translateStateToDbValue($task->getState());

        $insert = $this->db->insert();
        $insert->into('aufgabe');
        $insert->col('aufgabe', $task->getTitle());
        $insert->col('prio', $priority);
        $insert->col('status', $state);
        $insert->col('initiator', (int)$task->getCreatorAddressId());

        // Optionale Felder
        $insert->col('adresse', (int)$task->getEmployeeAddressId());
        $insert->col('kunde', (int)$task->getCustomerAddressId());
        $insert->col('projekt', (int)$task->getProjectId());
        $insert->col('teilprojekt', (int)$task->getSubProjectId());
        $insert->col('beschreibung', (string)$task->getDescription());

        if ($task->getSubmissionDateTime() !== null) {
            $insert->col('abgabe_bis', $task->getSubmissionDateTime()->format('Y-m-d'));
            $insert->col('abgabe_bis_zeit', $task->getSubmissionDateTime()->format('H:i:s'));
        } else {
            $insert->col('abgabe_bis', '0000-00-00');
            $insert->col('abgabe_bis_zeit', '00:00:00');
        }

        $insert->col('angelegt_am', date('Y-m-d'));
        $insert->set('logdatei', 'NOW()');

        $this->db->beginTransaction();

        try {
            $this->db->perform($insert->getStatement(), $insert->getBindValues());
            $insertId = $this->db->lastInsertId();

            $this->db->perform(
                'INSERT INTO `wiedervorlage_aufgabe` (`task_id`, `resubmission_id`, `required_completion_stage_id`) 
                VALUES (:task_id, :resubmission_id, :required_completion_stage_id)',
                [
                    'task_id'                      => $insertId,
                    'resubmission_id'              => $task->getResubmissionId(),
                    'required_completion_stage_id' => $task->getRequiredCompletionStageId(),
                ]
            );
            $this->db->commit();
            //
        } catch (DatabaseExceptionInterface $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    /**
     * @param ResubmissionTaskData $task
     *
     * @throws ResubmissionTaskNotFoundException
     * @throws TaskMustBeCompletedException
     *
     * @return void
     */
    public function editTask(ResubmissionTaskData $task)
    {
        if (!$this->taskGateway->isTaskAssigendToResubmission($task->getId(), $task->getResubmissionId())) {
            throw new ResubmissionTaskNotFoundException(sprintf(
                'Task not found. Task-ID: %s - Resubmission-ID: %s', $task->getId(), $task->getResubmissionId()
            ));
        }

        // Vor der Änderung prüfen ob Änderung überhaupt gültig wäre
        $currentStage = $this->resubmissionGateway->getStageByResubmission($task->getResubmissionId());
        $check = $this->isTaskStageChangeAllowed(
            $currentStage['id'],
            $task->getRequiredCompletionStageId(),
            $task->getState()
        );
        if (!$check) {
            $requiredStage = $this->resubmissionGateway->getStage($task->getRequiredCompletionStageId());
            throw TaskMustBeCompletedException::onModification(
                $requiredStage['shortname'],
                $currentStage['shortname']
            );
        }

        $priority = $this->translatePriorityToDbValue($task->getPriority());
        $state = $this->translateStateToDbValue($task->getState());

        $update = $this->db->update();
        $update->table('aufgabe');
        $update->col('aufgabe', $task->getTitle());
        $update->col('prio', $priority);
        $update->col('status', $state);

        if ($task->getEmployeeAddressId() !== null) {
            $update->col('adresse', $task->getEmployeeAddressId());
        }
        if ($task->getProjectId() !== null) {
            $update->col('projekt', $task->getProjectId());
        }
        if ($task->getSubProjectId() !== null) {
            $update->col('teilprojekt', $task->getSubProjectId());
        }
        if ($task->getDescription() !== null) {
            $update->col('beschreibung', $task->getDescription());
        } else {
            $update->col('beschreibung', '');
        }
        if ($task->getCustomerAddressId() !== null) {
            $update->col('kunde', $task->getCustomerAddressId());
        } else {
            $update->col('kunde', 0);
        }
        if ($task->getCompletionDateTime() !== null) {
            if ($task->getCompletionDateTime()->getTimestamp() > 0) {
                $update->col('abgeschlossen_am', $task->getCompletionDateTime()->format('Y-m-d'));
            } else {
                $update->col('abgeschlossen_am', '0000-00-00');
            }
        }
        if ($task->getSubmissionDateTime() !== null) {
            $update->col('abgabe_bis', $task->getSubmissionDateTime()->format('Y-m-d'));
            $update->col('abgabe_bis_zeit', $task->getSubmissionDateTime()->format('H:i:s'));
        } else {
            $update->col('abgabe_bis', '0000-00-00');
            $update->col('abgabe_bis_zeit', '00:00:00');
        }

        $update->set('logdatei', 'NOW()');
        $update->where('id = ?', $task->getId());
        $update->limit(1);

        try {
            $this->db->beginTransaction();
            $this->db->perform($update->getStatement(), $update->getBindValues());
            $this->db->perform(
                'UPDATE `wiedervorlage_aufgabe` SET `required_completion_stage_id` = :required_completion_stage_id 
                 WHERE `task_id` = :task_id AND `resubmission_id` = :resubmission_id LIMIT 1',
                [
                    'task_id'                      => $task->getId(),
                    'resubmission_id'              => $task->getResubmissionId(),
                    'required_completion_stage_id' => $task->getRequiredCompletionStageId(),
                ]
            );
            $this->db->commit();
            //
        } catch (DatabaseExceptionInterface $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    /**
     * Aufgabe löschen
     *
     * @param int $taskId
     * @param int $resubmissionId
     *
     * @throws ResubmissionTaskNotFoundException
     *
     * @return void
     */
    public function deleteTask($taskId, $resubmissionId)
    {
        $taskId = (int)$taskId;
        $resubmissionId = (int)$resubmissionId;

        if (!$this->taskGateway->isTaskAssigendToResubmission($taskId, $resubmissionId)) {
            throw new ResubmissionTaskNotFoundException(sprintf(
                'Task not found. Task-ID: %s - Resubmission-ID: %s', $taskId, $resubmissionId
            ));
        }

        $this->db->beginTransaction();

        try {
            // Aufgabe löschen
            $sql = 'DELETE FROM `aufgabe` WHERE `id` = :task_id LIMIT 1 ';
            $this->db->perform($sql, ['task_id' => $taskId]);

            // Verknüpfung zur Wiedervorlage löschen
            $sql =
                'DELETE FROM `wiedervorlage_aufgabe` 
                WHERE `task_id` = :task_id AND `resubmission_id` = :resubmission_id 
                LIMIT 1 ';
            $this->db->perform($sql, [
                'resubmission_id' => $resubmissionId,
                'task_id'         => $taskId,
            ]);

            $this->db->commit();
        } catch (DatabaseExceptionInterface $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    /**
     * Ermittelt alle Aufgaben die das Verschieben einer Wiedervorlage blockieren
     *
     * @param int $resubmissionId
     * @param int $targetStageId
     *
     * @return array Empty array if none item is blocking
     */
    public function getBlockingTasksForTargetStage($resubmissionId, $targetStageId)
    {
        $resubmissionId = (int)$resubmissionId;
        $targetStageId = (int)$targetStageId;

        $tasks = $this->taskGateway->getTasksByResubmission($resubmissionId);

        $blocking = [];
        foreach ($tasks as $task) {
            if ($task['state'] === ResubmissionTaskData::STATE_COMPLETED) {
                continue; // Aufgabe ist bereits abgeschlossen > Aufgabe darf in jede Stage geschoben werden
            }
            if ($task['required_completion_stage_id'] === 0) {
                continue; // Aufgabe hat keine Fertigstellungs-Stage hinterlegt > Aufgabe darf in jede Stage geschoben werden
            }

            $distance = $this->resubmissionGateway->getDistanceBetweenStages(
                $targetStageId,
                $task['required_completion_stage_id']
            );
            if ($distance <= 0) {
                $blocking[] = [
                    'id'    => $task['id'],
                    'title' => $task['title'],
                    'state' => $task['state'],
                ];
            }
        }

        return $blocking;
    }

    /**
     * Prüft ob eine Aufgabe in die Target-Stage wecheln darf
     *
     * @param int    $currentStageId ID der Stage von der aus verschoben wird
     * @param int    $targetStageId  ID der Stage in die verschoben werden soll
     * @param string $targetState    Aufgaben-Status der zugewiesen soll bzw. aktuell gesetzt ist
     *
     * @throws InvalidArgumentException
     *
     * @return bool true = Aufgabe darf auf Target-Stage wechseln
     *              false = Aufgabe muss abgeschlossen sein, um auf die Target-Stage wechseln zu dürfen
     */
    private function isTaskStageChangeAllowed($currentStageId, $targetStageId, $targetState)
    {
        $currentStageId = (int)$currentStageId;
        $targetStageId = (int)$targetStageId;
        if (!in_array($targetState, ResubmissionTaskData::getValidStates(), true)) {
            throw new InvalidArgumentException(sprintf('Target state is invalid: "%s"', $targetState));
        }

        // Aufgabe auf abgeschlossen stellen => Immer OK; solange die Aufgabe existiert
        if ($targetState === ResubmissionTaskData::STATE_COMPLETED) {
            return true;
        }

        // Es ist keine Stage-ID festgelegt bei der die Aufgabe abgeschlossen sein muss
        // > Alles Roger, solange die Aufgabe existiert
        if ($targetStageId === 0) {
            return true;
        }

        $distance = $this->resubmissionGateway->getDistanceBetweenStages($currentStageId, $targetStageId);

        return $distance > 0;
    }

    /**
     * @param string $state
     *
     * @return string|null
     */
    private function translateStateToDbValue($state)
    {
        $dbValue = null;
        switch ($state) {
            case ResubmissionTaskData::STATE_COMPLETED:
                $dbValue = 'abgeschlossen';
                break;
            case ResubmissionTaskData::STATE_PROCESSING:
                $dbValue = 'inbearbeitung';
                break;
            case ResubmissionTaskData::STATE_OPEN:
                $dbValue = 'offen';
                break;
        }

        return $dbValue;
    }

    /**
     * @param string $priority
     *
     * @return int|null
     */
    private function translatePriorityToDbValue($priority)
    {
        $dbValue = null;
        switch ($priority) {
            case ResubmissionTaskData::PRIORITY_HIGH:
                $dbValue = 1;
                break;
            case ResubmissionTaskData::PRIORITY_LOW:
                $dbValue = -1;
                break;
            case ResubmissionTaskData::PRIORITY_MEDIUM:
                $dbValue = 0;
                break;
        }

        return $dbValue;
    }
}
