<?php

declare(strict_types=1);

namespace Xentral\Modules\MandatoryFields;

use Xentral\Modules\MandatoryFields\Data\MandatoryFieldData;
use Xentral\Modules\MandatoryFields\Data\ValidatorResultData;
use Xentral\Modules\MandatoryFields\Exception\MandatoryFieldExistsException;
use Xentral\Modules\MandatoryFields\Exception\MandatoryFieldNotFoundException;
use Xentral\Modules\MandatoryFields\Exception\UnknownTypeException;
use Xentral\Modules\MandatoryFields\Service\MandatoryFieldsGateway;
use Xentral\Modules\MandatoryFields\Service\MandatoryFieldsService;
use Xentral\Modules\MandatoryFields\Service\MandatoryFieldsValidator;

final class MandatoryFieldsModule
{

    /** @var MandatoryFieldsGateway $gateway */
    private $gateway;

    /** @var MandatoryFieldsService $service */
    private $service;

    /** @var MandatoryFieldsValidator $validator */
    private $validator;

    /**
     * @param MandatoryFieldsGateway   $gateway
     * @param MandatoryFieldsService   $service
     * @param MandatoryFieldsValidator $validator
     */
    public function __construct(
        MandatoryFieldsGateway $gateway,
        MandatoryFieldsService $service,
        MandatoryFieldsValidator $validator
    ) {
        $this->service = $service;
        $this->gateway = $gateway;
        $this->validator = $validator;
    }

    /**
     * @param MandatoryFieldData $mandatoryField
     *
     * @throws MandatoryFieldExistsException
     *
     * @return int
     */
    public function createMandatoryField(MandatoryFieldData $mandatoryField): int
    {
        $exists = $this->existsMandatoryField(
            $mandatoryField->getModule(),
            $mandatoryField->getAction(),
            $mandatoryField->getFieldId(),
            $mandatoryField->getType()
        );

        if ($exists) {
            throw new MandatoryFieldExistsException('Mandatory field already exists.');
        }

        return $this->service->create($mandatoryField);
    }

    /**
     * @param MandatoryFieldData $mandatoryField
     *
     * @throws MandatoryFieldNotFoundException
     */
    public function editMandatoryField(MandatoryFieldData $mandatoryField): void
    {
        $this->service->edit($mandatoryField);
    }

    /**
     * @param string $module
     * @param string $action
     * @param string $fieldId
     * @param string $type
     *
     * @return bool
     */
    public function existsMandatoryField(string $module, string $action, string $fieldId, string $type): bool
    {
        $result = $this->gateway->getMandatoryFieldByParameters($module, $action, $fieldId, $type);

        return !empty($result);
    }

    /**
     * @param int $mandatoryFieldId
     *
     * @throws MandatoryFieldNotFoundException
     */
    public function removeMandatoryFieldById(int $mandatoryFieldId): void
    {
        $this->service->removeById($mandatoryFieldId);
    }


    /**
     * @param int $mandatoryFieldId
     *
     * @throws MandatoryFieldNotFoundException
     *
     * @return MandatoryFieldData
     */
    public function getMandatoryFieldById(int $mandatoryFieldId): MandatoryFieldData
    {
        return $this->gateway->getById($mandatoryFieldId);
    }

    /**
     * @param string $type
     * @param string $value
     * @param int    $mandatoryFieldId
     *
     * @throws UnknownTypeException
     * @throws MandatoryFieldNotFoundException
     *
     * @return ValidatorResultData
     */
    public function validate(string $type, string $value, int $mandatoryFieldId): ValidatorResultData
    {
        return $this->validator->validate($type, $value, $mandatoryFieldId);
    }
}
