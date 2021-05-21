<?php

namespace Xentral\Modules\Resubmission\Data;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Xentral\Modules\Resubmission\Exception\InvalidArgumentException;

final class ResubmissionTaskData
{
    /** @var string STATE_OPEN "Offen" */
    const STATE_OPEN = 'open';

    /** @var string STATE_PROCESSING "In Bearbeitung" */
    const STATE_PROCESSING = 'processing';

    /** @var string STATE_COMPLETED "Abgeschlossen" */
    const STATE_COMPLETED = 'completed';

    /** @var string PRIORITY_HIGH */
    const PRIORITY_HIGH = 'high';

    /** @var string PRIORITY_MEDIUM */
    const PRIORITY_MEDIUM = 'medium';

    /** @var string PRIORITY_LOW */
    const PRIORITY_LOW = 'low';

    /** @var int|null $id ID der Aufgabe; null wenn neue Aufgabe */
    private $id;

    /** @var int $resubmissionId ID der Wiedervorlagen */
    private $resubmissionId;

    /** @var int|null $creatorAddressId Address-ID des Erstellers */
    private $creatorAddressId;

    /** @var int|null $employeeAddressId Für Feld "Bearbeiter/Mitarbeiter" */
    private $employeeAddressId;

    /** @var int|null $customerAddressId Für Feld "Adresse" */
    private $customerAddressId;

    /** @var int|null $projectId */
    private $projectId;

    /** @var int|null $subProjectId Teilprojekt/Arbeitspaket */
    private $subProjectId;

    /** @var string $title */
    private $title;

    /** @var string|null $description */
    private $description;

    /** @var string $state */
    private $state;

    /** @var string $priority */
    private $priority;

    /** @var DateTimeInterface|null $completionDateTime Abgeschlossen am */
    private $completionDateTime;

    /** @var DateTimeInterface|null $submissionDateTime Abschliessen bis */
    private $submissionDateTime;

    /** @var int $finshedOnStage Stage-ID ab der die Aufgabe abgeschlossen sein muss */
    private $requiredCompletionStageId;

    /**
     * Private constructor
     *
     * @internal Don't change visibility. Use self::fromFormData instead.
     */
    private function __construct()
    {
    }

    /**
     * @param array $formData
     *
     * @return self
     */
    public static function fromFormData(array $formData)
    {
        $task = new self();
        $task->setResubmissionId($formData['resubmission_id']);
        $task->setRequiredCompletionStageId($formData['required_completion_stage_id']);
        $task->setTitle($formData['title']);
        $task->setPriority($formData['priority']);
        $task->setState($formData['state']);

        // Optional properties
        if (isset($formData['task_id'])) {
            $task->setId($formData['task_id']);
        }
        if (isset($formData['description'])) {
            $task->setDescription($formData['description']);
        }
        if (isset($formData['employee_address_id'])) {
            $task->setEmployeeAddressId($formData['employee_address_id']);
        }
        if (isset($formData['customer_address_id'])) {
            $task->setCustomerAddressId($formData['customer_address_id']);
        }
        if (isset($formData['project_id'])) {
            $task->setProjectId($formData['project_id']);
        }
        if (isset($formData['creator_address_id'])) {
            $task->setCreatorAddressId($formData['creator_address_id']);
        }
        if (isset($formData['subproject_id'])) {
            $task->setSubProjectId($formData['subproject_id']);
        }
        $submissionDate = trim(sprintf('%s %s', $formData['submission_date'], $formData['submission_time']));
        if (!empty($submissionDate)) {
            $task->setSubmissionDateTimeByString($submissionDate);
        }

        return $task;
    }

    /**
     * @return array
     */
    public static function getValidStates()
    {
        return [
            self::STATE_OPEN,
            self::STATE_PROCESSING,
            self::STATE_COMPLETED,
        ];
    }

    /**
     * @return int|null Null only when new task
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getResubmissionId()
    {
        return $this->resubmissionId;
    }

    /**
     * @return int|null
     */
    public function getEmployeeAddressId()
    {
        return $this->employeeAddressId;
    }

    /**
     * @return int|null
     */
    public function getCustomerAddressId()
    {
        return $this->customerAddressId;
    }

    /**
     * @return int
     */
    public function getRequiredCompletionStageId()
    {
        return $this->requiredCompletionStageId;
    }

    /**
     * @return int|null
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @return int|null
     */
    public function getSubProjectId()
    {
        return $this->subProjectId;
    }

    /**
     * @return int|null
     */
    public function getCreatorAddressId()
    {
        return $this->creatorAddressId;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getSubmissionDateTime()
    {
        return $this->submissionDateTime;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCompletionDateTime()
    {
        return $this->completionDateTime;
    }

    /**
     * @param int $id
     *
     * @return void
     */
    private function setId($id)
    {
        $this->id = (int)$id;
    }

    /**
     * @param int $resubmissionId
     *
     * @return void
     */
    private function setResubmissionId($resubmissionId)
    {
        $resubmissionId = (int)$resubmissionId;
        if ($resubmissionId <= 0) {
            throw new InvalidArgumentException('Resubmission ID can not be empty.');
        }

        $this->resubmissionId = $resubmissionId;
    }

    /**
     * @param string $title
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private function setTitle($title)
    {
        $title = trim($title);
        if (empty($title)) {
            throw new InvalidArgumentException('Title can not be empty.');
        }

        $this->title = $title;
    }

    /**
     * @param string $description
     *
     * @return void
     */
    private function setDescription($description)
    {
        $this->description = trim($description);
    }

    /**
     * @param string $priority
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private function setPriority($priority)
    {
        if (!in_array($priority, [self::PRIORITY_HIGH, self::PRIORITY_MEDIUM, self::PRIORITY_LOW], true)) {
            throw new InvalidArgumentException(sprintf('Priority value is invalid: "%s"', $priority));
        }

        $this->priority = $priority;
    }

    /**
     * @param string $state
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private function setState($state)
    {
        if (!in_array($state, [self::STATE_COMPLETED, self::STATE_PROCESSING, self::STATE_OPEN], true)) {
            throw new InvalidArgumentException(sprintf('State value is invalid: "%s"', $state));
        }

        try {
            if ($state === self::STATE_COMPLETED) {
                $this->completionDateTime = new DateTimeImmutable('now');
            }
            if ($state === self::STATE_OPEN || $state === self::STATE_PROCESSING) {
                $this->completionDateTime = new DateTimeImmutable('0000-00-00 00:00:00');
            }
            $this->state = $state;
        } catch (Exception $exception) {
            throw new InvalidArgumentException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param int $employeeAddressId
     *
     * @return void
     */
    private function setEmployeeAddressId($employeeAddressId)
    {
        $this->employeeAddressId = (int)$employeeAddressId;
    }

    /**
     * @param int $customerAddressId
     *
     * @return void
     */
    private function setCustomerAddressId($customerAddressId)
    {
        $this->customerAddressId = (int)$customerAddressId;
    }

    /**
     * @param int $projectId
     *
     * @return void
     */
    private function setProjectId($projectId)
    {
        $this->projectId = (int)$projectId;
    }

    /**
     * @param int $subProjectId
     *
     * @return void
     */
    private function setSubProjectId($subProjectId)
    {
        $this->subProjectId = (int)$subProjectId;
    }

    /**
     * @param int $creatorAddressId
     *
     * @return void
     */
    private function setCreatorAddressId($creatorAddressId)
    {
        $this->creatorAddressId = (int)$creatorAddressId;
    }

    /**
     * @param DateTimeInterface $dateTime
     *
     * @return void
     */
    private function setSubmissionDateTime(DateTimeInterface $dateTime)
    {
        $this->submissionDateTime = $dateTime;
    }

    /**
     * @param string $dateTimeString
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private function setSubmissionDateTimeByString($dateTimeString)
    {
        try {
            $submissionDate = new DateTimeImmutable($dateTimeString);
        } catch (Exception $exception) {
            throw new InvalidArgumentException(
                'Invalid submission date: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }

        $this->setSubmissionDateTime($submissionDate);
    }

    /**
     * @param int $stageId
     *
     * @return void
     */
    private function setRequiredCompletionStageId($stageId)
    {
        $this->requiredCompletionStageId = (int)$stageId;
    }
}
