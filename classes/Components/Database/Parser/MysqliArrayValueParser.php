<?php

namespace Xentral\Components\Database\Parser;

/**
 * @example
 *      self::rebuild('SELECT * FROM foo WHERE id IN (:ids)', ['ids' => [1, 2, 3]])
 *      Erzeugt:
 *          [
 *              'statement' => 'SELECT * FROM foo WHERE id IN (:ids_expl_0_, :ids_expl_1_, :ids_expl_2_)',
 *              'values' => [
 *                  '_ids_expl_0_' => 1,
 *                  '_ids_expl_1_' => 2,
 *                  '_ids_expl_2_' => 3,
 *              ]
 *          ]
 */
final class MysqliArrayValueParser implements ParserInterface
{
    /**
     * @param string $statement
     * @param array  $values
     *
     * @return array
     *  - Array key 'statement' contains the rebuild statement
     *  - Array key 'values' contains the rebuild bind parameters
     */
    public function rebuild($statement, array $values = [])
    {
        return $this->replaceArrayValues($statement, $values);
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @return array
     */
    private function replaceArrayValues($statement, array $values = [])
    {
        foreach ($values as $paramName => $paramValue) {
            if (is_array($paramValue)) {
                $counter = 0;
                $additionalParams = [];
                foreach ($paramValue as $arrayValue) {
                    $additionalParamName = '_' . $paramName . '_expl_' . $counter . '_';
                    $additionalParams[] = ':' . $additionalParamName;
                    $values[$additionalParamName] = $arrayValue;
                    $counter++;
                }

                // Replace original named parameter by exploded parameters in statement
                $replaceString = implode(', ', $additionalParams);
                $statement = str_replace(':' . $paramName, $replaceString, $statement);

                // Remove original parameter value
                unset($values[$paramName]);
            }
        }

        return [
            'statement' => $statement,
            'values'    => $values,
        ];
    }
}
