<?php

namespace Xentral\Modules\Resubmission\Data;

use Xentral\Modules\Resubmission\Exception\ValidationFailedException;

final class FreeTextFieldConfigData
{
    /** @var int|null $id */
    public $id;

    /** @var string $title */
    public $title;

    /** @var bool $showInPipeline */
    public $showInPipeline = false;

    /** @var bool $showInTables */
    public $showInTables = false;

    /** @var int $availableFromStageId */
    public $availableFromStageId = 0;

    /** @var int */
    public $requiredFromStageId = 0;

    /**
     * @param array $formData
     *
     * @throws ValidationFailedException
     *
     * @return self
     */
    public static function fromFromData(array $formData)
    {
        $data = new self();
        $data->id = $formData['id'];
        $data->title = trim($formData['title']);
        $data->showInPipeline = (bool)$formData['show_in_pipeline'];
        $data->showInTables = (bool)$formData['show_in_tables'];
        $data->availableFromStageId = $formData['available_from_stage_id'];
        $data->requiredFromStageId = $formData['required_from_stage_id'];

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
            $errors['id'][] = 'The "id" property must be an integer.';
        }
        if ($this->id !== null && $this->id <= 0) {
            $errors['id'][] = 'The "id" property must be greater than zero.';
        }

        // title-Property
        if (!is_string($this->title) || empty($this->title)) {
            $errors['title'][] = 'The "title" property can not be empty.';
        }

        // showInPipeline-Property
        if (!is_bool($this->showInPipeline)) {
            $errors['showInPipeline'][] = 'The "showInPipeline" property must be type boolean.';
        }

        // showInTables-Property
        if (!is_bool($this->showInTables)) {
            $errors['showInTables'][] = 'The "showInTables" property must be type boolean.';
        }

        // availableFromStageId-Property
        if (!is_int($this->availableFromStageId)) {
            $errors['availableFromStageId'][] = 'The "availableFromStageId" property must be type integer.';
        }
        if ($this->availableFromStageId < 0) {
            $errors['availableFromStageId'][] = 'The "availableFromStageId" property must be zero or greater than zero.';
        }

        // requiredFromStageId-Property
        if (!is_int($this->requiredFromStageId)) {
            $errors['requiredFromStageId'][] = 'The "requiredFromStageId" property must be type integer.';
        }
        if ($this->requiredFromStageId < 0) {
            $errors['requiredFromStageId'][] = 'The "requiredFromStageId" property must be zero or greater than zero.';
        }

        return $errors;
    }
}
