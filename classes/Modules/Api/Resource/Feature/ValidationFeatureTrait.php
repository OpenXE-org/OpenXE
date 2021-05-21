<?php

namespace Xentral\Modules\Api\Resource\Feature;

use Rakit\Validation\Validation;
use Xentral\Modules\Api\Exception\ValidationErrorException;
use Xentral\Modules\Api\Resource\Exception\ValidationRequiredException;

trait ValidationFeatureTrait
{
    /** @var array $validationRules */
    private $validationRules;

    /** @var string $resourceTableName */
    private $resourceTableName;

    /**
     * Validierungsregeln festlegen
     *
     * @example $this->registerValidationRules([
     *              'id' => 'not_present',
     *              'bezeichnung' => 'required|unique:artikelkategorien,bezeichnung',
     *              'next_number' => 'numeric',
     *              'projekt' => 'numeric',
     *              'parent' => 'numeric',
     *              'externenummer' => 'numeric',
     *              'geloescht' => 'in:0,1',
     *          ]);

     * @see https://github.com/rakit/validation#available-rules
     *
     * @param array $rules
     */
    protected function registerValidationRules(array $rules)
    {
        $this->validationRules = $rules;
    }

    /**
     * @param array $inputVars
     * @param int   $selfId
     */
    protected function validateData($inputVars, $selfId = null)
    {
        if (empty($this->validationRules)) {
            throw new ValidationRequiredException();
        }

        // Regeln aufbereiten
        $rules = $this->validationRules;
        if ($selfId) {
            $needle = sprintf('unique:%s,', $this->resourceTableName);
            foreach ($rules as $ruleKey => $ruleVal) {
                if ($pos = strpos($ruleVal, $needle)) {

                    // Nach Anfang der nachfolgenden Regel suchen
                    $searchPos = $pos + strlen($needle);
                    $insertPos = strpos($ruleVal, '|', $searchPos);

                    // Keine weitere Regel gefunden; am Ende anfügen
                    if (!$insertPos) {
                        $insertPos = strlen($ruleVal);
                    }

                    // ID als dritten Parameter für UniqueRule übergeben
                    /** @see UniqueRule Parameter "except" */
                    $newRuleVal = substr_replace($ruleVal, ',' . $selfId, $insertPos, 0);

                    $rules[$ruleKey] = $newRuleVal;
                }
            }
        }

        /** @var Validation $validation */
        $validation = $this->validator->validate($inputVars, $rules);
        if ($validation->fails()) {
            throw new ValidationErrorException($validation->errors()->all());
        }
    }

    /**
     * @param string $tableName
     */
    protected function setTableName($tableName)
    {
        $this->resourceTableName = $tableName;
    }
}
