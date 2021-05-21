<?php

namespace Xentral\Components\Database\Parser;

interface ParserInterface
{
    /**
     * @param string $statement
     * @param array  $values
     *
     * @return array
     *  - Array key 'statement' contains the corrected statement
     *  - Array key 'values' contains the corrected bind values
     */
    public function rebuild($statement, array $values = []);
}
