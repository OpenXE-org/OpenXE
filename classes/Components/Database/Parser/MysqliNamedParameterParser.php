<?php

namespace Xentral\Components\Database\Parser;

use Xentral\Components\Database\Exception\MissingParameterException;

/**
 * Responsibility of this class is to make sql statement and bind values compatible with mysqli
 *
 * (Mysqli does not support named parameters)
 *
 * It does this by:
 * - Replacing named parameters (:param) by ?-Placeholder (in statement)
 * - Rearranging bind values in order of appearance of named parameters
 */
final class MysqliNamedParameterParser implements ParserInterface
{
    /**
     * @param string $statement
     * @param array  $values
     *
     * @return array
     */
    public function rebuild($statement, array $values = [])
    {
        return $this->replaceNamedParameters($statement, $values);
    }

    /**
     * @param string $statement
     * @param array  $values
     *
     * @throws MissingParameterException
     *
     * @return array
     */
    private function replaceNamedParameters($statement, array $values = [])
    {
        $result = [
            'statement' => $statement,
            'values'    => [],
            'params'    => [],
        ];

        if (empty($values)) {
            return $result;
        }

        // Split statement on named parameters
        $parts = preg_split('/(:[a-zA-Z0-9_]+)/um', $statement, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        foreach ($parts as &$part) {
            if (strpos($part, ':') !== 0) {
                continue; // SQL part does not contain named parameter
            }

            $parameterName = substr_replace($part, '', 0, 1);
            if (!array_key_exists($parameterName, $values)) {
                throw new MissingParameterException(sprintf(
                    'Parameter "%s" is missing from the bound values',
                    $parameterName
                ));
            }

            // Push values in same order of parameters for binding
            $result['values'][] = $values[$parameterName];
            $result['params'][] = $parameterName; // For debugging only

            // Replace named parameter by ?-Placeholder
            $part = '?';
        }
        unset($part);

        // Rebuild statement from (changed) parts
        $result['statement'] = implode('', $parts);

        return $result;
    }
}
