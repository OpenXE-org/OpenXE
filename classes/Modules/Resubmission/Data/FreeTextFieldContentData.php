<?php

namespace Xentral\Modules\Resubmission\Data;

use Xentral\Modules\Resubmission\Exception\ValidationFailedException;

final class FreeTextFieldContentData
{
    /** @var int $resubmissionId Wiedervorlagen-ID */
    public $resubmissionId;

    /** @var int $configId Textfield-Config-ID */
    public $configId;

    /** @var string|null $content */
    public $content;

    /**
     * @param array $formData
     *
     * @throws ValidationFailedException
     *
     * @return self
     */
    public static function fromFormData(array $formData)
    {
        $formData['content'] = trim($formData['content']);

        $data = new self();
        $data->configId = (int)$formData['textfield_config_id'];
        $data->resubmissionId = (int)$formData['resubmission_id'];
        $data->content = !empty($formData['content']) ? $formData['content'] : null;

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

        // resubmissionId-Property
        if (!is_int($this->resubmissionId)) {
            $errors['resubmission_id'][] = 'The "resubmissionId" property must be an integer.';
        }
        if ($this->resubmissionId <= 0) {
            $errors['resubmission_id'][] = 'The "resubmissionId" property must be greater than zero.';
        }

        // configId-Property
        if (!is_int($this->configId)) {
            $errors['config_id'][] = 'The "configId" property must be type integer.';
        }
        if ($this->configId <= 0) {
            $errors['config_id'][] = 'The "configId" property must be greater than zero.';
        }

        // content-Property
        if ($this->content !== null && !is_string($this->content)) {
            $errors['content'][] = 'The "content" property must be null or a non empty string.';
        }

        return $errors;
    }
}
