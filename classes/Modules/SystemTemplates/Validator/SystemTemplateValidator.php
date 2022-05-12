<?php


namespace Xentral\Modules\SystemTemplates\Validator;


use Xentral\Modules\SystemTemplates\Validator\Exception\SystemTemplateValidatorException;

final class SystemTemplateValidator
{

    /** @var SystemTemplateValidatorInterface $validator */
    private $validator;


    /**
     * SystemTemplateValidator constructor.
     *
     * @param SystemTemplateValidatorInterface $validator
     */
    public function __construct(SystemTemplateValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $data
     *
     * @return SystemTemplateValidator
     */
    public function fromMeta($data = [])
    {
        $this->validator->setData($data);

        return $this;
    }

    /**
     * @param string|null $validateConfig
     *
     * @throws SystemTemplateValidatorException
     * @return bool
     */
    public function isValid($validateConfig = null)
    {
        $this->validator->applyRules($validateConfig);

        return $this->validator->isValid($validateConfig);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->validator->getErrors();
    }
}