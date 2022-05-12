<?php


namespace Xentral\Modules\SystemTemplates\Validator;

use Xentral\Modules\SystemTemplates\Validator\Exception\SystemTemplateValidatorException;


final class MetaDataValidation implements SystemTemplateValidatorInterface
{
    /** @var array $data */
    private $data = [];

    /** @var array $errors */
    private $errors = [];

    /** @var string $templePath */
    private $templePath;

    /** @var array $mandatoryFields */
    private $mandatoryFields = [];

    /** @var bool $autoRulesCheck */
    private $autoRulesCheck;

    /** @var Ruleset $ruleSet */
    private $ruleSet;

    /**
     * @return array
     */
    public function validateDefault()
    {
        return [
            'title'       => [
                'rule'     => static function ($data) {
                    return !empty($data['title']) && is_string($data['title']);
                },
                'required' => true,
                'message'  => sprintf('%s should be a non empty String', 'Title'),
            ],
            'description' => [
                'rule'     => static function ($data) {
                    return !empty($data['description']) && is_string($data['description']);
                },
                'required' => true,
                'message'  => sprintf('%s should be a non empty String', 'Description'),
            ],
            'category'    => [
                'rule'     => static function ($data) {
                    return !empty($data['category']) && is_string($data['category']);
                },
                'required' => true,
                'message'  => sprintf('%s should be a non empty String', 'Category'),
            ],
            'filename'    => [
                'rule'     => ['isFileName'],
                'required' => true,
                'message'  => 'File name cannot be blank or non string',
            ],
        ];
    }

    /**
     * MetaDataValidation constructor.
     *
     * @param Ruleset $ruleset
     * @param         $templatePath
     * @param bool    $autoRulesCheck
     */
    public function __construct(Ruleset $ruleset, $templatePath, $autoRulesCheck = true)
    {
        $this->templePath = $templatePath;
        $this->ruleSet = $ruleset;
        $this->autoRulesCheck = $autoRulesCheck;
    }

    /**
     * @param string $error
     */
    public function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * @param string $fieldName
     *
     * @return void
     */
    public function setMandatoryFieldName($fieldName)
    {
        if (!empty($fieldName)) {
            $this->mandatoryFields = array_merge($this->mandatoryFields, [$fieldName]);
        }
    }

    /**
     * @param string|null $validateConfig
     *
     * @return bool
     */
    public function isValid($validateConfig = null)
    {
        try {
            $this->checkMandatory();
        } catch (SystemTemplateValidatorException $exception) {
            $this->addError($exception->getMessage());

            return false;
        }

        if ($this->autoRulesCheck === false) {
            $this->applyRules($validateConfig);
        }

        return empty($this->errors);
    }

    /**
     * @param string $configMethod
     *
     * @return void
     */
    public function applyRules($configMethod = null)
    {
        $this->ruleSet->setRules($this, $configMethod);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return void
     */
    private function checkMandatory()
    {
        $missing = array_diff($this->mandatoryFields, array_keys($this->data));
        if (!empty($missing)) {
            throw new SystemTemplateValidatorException(
                sprintf('Missing mandatory parameter "%s', json_encode(array_unique($missing)))
            );
        }
    }


    /**
     * @param array $data
     */
    public function setData($data = [])
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string|null $name
     *
     * @return bool
     */
    public function isFileName($name = null)
    {
        $fileName = null === $name && !empty($this->data['filename']) ? $this->data['filename'] : $name;

        if (empty($fileName) || !is_string($fileName)) {
            $this->addError('File name cannot be blank or non string');

            return false;
        }
        if (!file_exists($this->templePath . $fileName)) {
            $this->addError(sprintf('File "%s" cannot be found', $fileName));

            return false;
        }

        return true;
    }

}