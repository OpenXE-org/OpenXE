<?php

namespace Xentral\Modules\Resubmission\Data;

use Xentral\Modules\Resubmission\Exception\ValidationFailedException;

final class TaskTemplateData
{
  /** @var int|null $id */
  public $id;

  /** @var string $title */
  public $title;

  /** @var int $requiredFromStageId */
  public $requiredFromStageId = 0;

  /** @var int $addTaskAtStageId */
  public $addTaskAtStageId = 0;


  /**
   * @param array $formData
   *
   * @throws ValidationFailedException
   *
   * @return self
   */
  public static function fromFormData(array $formData)
  {
    $data = new self();
    $data->id = $formData['id'];
    $data->requiredFromStageId = $formData['required_from_stage_id'];
    $data->addTaskAtStageId = $formData['add_task_at_stage_id'];
    $data->employeeAddressId = $formData['employee_address_id'];
    $data->projectId = $formData['project_id'];
    $data->subprojectId = $formData['subproject_id'];
    $data->title = trim($formData['title']);
    $data->submissionDateDays = trim($formData['submission_date_days']);
    $data->submissionTime = trim($formData['submission_time']);
    $data->state = $formData['state'];
    $data->priority = $formData['priority'];
    $data->description = $formData['description'];


    $errors = $data->validate();
    if (!empty($errors)) {
      throw ValidationFailedException::fromErrors($errors);
    }

    return $data;
  }

  /**
   * @return array
   */
  public function validate()
  {
    $errors = [];

    // id-Property
    if ($this->id !== null && !is_int($this->id)) {
      $errors['id'][] = 'The "id" property must be an integer.'."\n";
    }
    if ($this->id !== null && $this->id <= 0) {
      $errors['id'][] = 'The "id" property must be greater than zero.'."\n";
    }

    // title-Property
    if (!is_string($this->title) || empty($this->title)) {
      $errors['title'][] = 'Bitte Bezeichnung ausfüllen.'."\n";
    }

    // requiredFromStageId-Property
    if (!is_int($this->requiredFromStageId)) {
      $errors['requiredFromStageId'][] = 'The "requiredFromStageId" property must be type integer.'."\n";
    }
    if ($this->requiredFromStageId < 0) {
      $errors['requiredFromStageId'][] = 'The "requiredFromStageId" property must be zero or greater than zero.'."\n";
    }

    // addTaskAtStageId-Property
    if (!is_int($this->addTaskAtStageId)) {
      $errors['addTaskAtStageId'][] = 'The "addTaskAtStageId" property must be type integer.'."\n";
    }
    if ($this->addTaskAtStageId <= 0) {
      $errors['addTaskAtStageId'][] = 'The "addTaskAtStageId" property must be greater than zero.'."\n";
    }

    return $errors;
  }
}
