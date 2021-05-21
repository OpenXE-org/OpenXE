<?php

declare(strict_types=1);

namespace Xentral\Modules\MandatoryFields\Data;

use Xentral\Modules\MandatoryFields\Exception\ValidationFailedException;

final class MandatoryFieldData
{

    /** @var int $id */
    private $id = 0;

    /** @var string $module */
    private $module = '';

    /** @var string $action */
    private $action = '';

    /** @var string $fieldId */
    private $fieldId = '';

    /** @var string $errorMessage */
    private $errorMessage = '';

    /** @var string $type */
    private $type = '';

    /** @var int $minLength */
    private $minLength = 0;

    /** @var int $maxLength */
    private $maxLength = 0;

    /** @var bool $mandatory */
    private $mandatory = false;

    /** @var string $comparator */
    private $comparator = '';

    /** @var string $compareto */
    private $compareto = '';

    private function __construct()
    {
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private static function validate(array $data): array
    {
        $errors = [];

        if (!isset($data['module']) || empty($data['module'])) {
            $errors['module'][] = 'Module is missing.';
        }

        if (!isset($data['action']) || empty($data['action'])) {
            $errors['action'][] = 'Action is missing.';
        }

        if (!isset($data['field_id']) || empty($data['field_id'])) {
            $errors['field_id'][] = 'Field_id is missing.';
        }

        if (!isset($data['type']) || empty($data['type'])) {
            $errors['type'][] = 'Type is missing.';
        }

        return $errors;
    }

    /**
     * @param array $data
     *
     * @throws ValidationFailedException
     *
     * @return MandatoryFieldData
     */
    public static function fromArray(array $data): MandatoryFieldData
    {
        $errors = self::validate($data);

        if (!empty($errors)) {
            throw ValidationFailedException::fromErrors($errors);
        }

        return self::fromDbState($data);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'module'        => $this->module,
            'action'        => $this->action,
            'field_id'      => $this->fieldId,
            'error_message' => $this->errorMessage,
            'type'          => $this->type,
            'min_length'    => $this->minLength,
            'max_length'    => $this->maxLength,
            'mandatory'     => $this->mandatory,
            'comparator'    => $this->comparator,
            'compareto'     => $this->compareto,
        ];
    }

    /**
     * @param array $data
     *
     * @return MandatoryFieldData
     */
    public static function fromDbState(array $data): MandatoryFieldData
    {
        $mandatoryField = new MandatoryFieldData();

        $mandatoryField->module = $data['module'];
        $mandatoryField->action = $data['action'];
        $mandatoryField->fieldId = $data['field_id'];
        $mandatoryField->type = $data['type'];

        if (isset($data['id'])) {
            $mandatoryField->id = (int)$data['id'];
        }

        if (isset($data['error_message'])) {
            $mandatoryField->errorMessage = $data['error_message'];
        }

        if (isset($data['min_length'])) {
            $mandatoryField->minLength = (int)$data['min_length'];
        }

        if (isset($data['max_length'])) {
            $mandatoryField->maxLength = (int)$data['max_length'];
        }

        if (isset($data['mandatory'])) {
            $mandatoryField->mandatory = (bool)$data['mandatory'];
        }

        if (isset($data['comparator'])) {
            $mandatoryField->comparator = $data['comparator'];
        }

        if (isset($data['compareto'])) {
            $mandatoryField->compareto = str_replace(',', '.', $data['compareto']);
        }

        return $mandatoryField;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getFieldId(): string
    {
        return $this->fieldId;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getMinLength(): int
    {
        return $this->minLength;
    }

    /**
     * @return int
     */
    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    /**
     * @return bool
     */
    public function isMandatory(): bool
    {
        return $this->mandatory;
    }

    /**
     * @return string
     */
    public function getComparator(): string
    {
        return $this->comparator;
    }

    /**
     * @return string
     */
    public function getCompareto(): string
    {
        return $this->compareto;
    }
}
