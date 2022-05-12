<?php

namespace Xentral\Modules\Api\Validator\Rule;

use Rakit\Validation\MissingRequiredParameterException;
use Rakit\Validation\Rule;
use Xentral\Components\Database\Database;

/**
 * Validierungsregel um zu prüfen ob ein Wert überhaupt in der Datenbank existiert.
 *
 * @example 'required|numeric|db_value:adresse,id' Bedeutet dass der zu validierende Wert
 *  in der Tabelle 'adresse' in der Spalte 'id' existieren muss.
 */
class DbValueRule extends Rule
{
    /** @var Database $db */
    protected $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->fillable_params = ['table', 'column', 'except'];
        $this->message =
            "The attribute ':attribute' has to be present in database. " .
            "The value ':value' is not present in table ':table'.";
    }

    /**
     * @param mixed $value
     *
     * @return bool
     *
     * @throws MissingRequiredParameterException
     */
    public function check($value)
    {
        // make sure required parameters exists
        $this->requireParameters(['table', 'column']);

        // getting parameters
        $column = $this->parameter('column');
        $table = $this->parameter('table');
        $except = (int)$this->parameter('except'); // ID des Datensatzes der von der Prüfung ausgenommen werden soll

        $select = $this->db->select()
            ->cols(['COUNT(*) AS num'])
            ->from($table)
            ->where($this->db->escapeIdentifier($column) . ' = :value');
        $bindValues = ['value' => $value];

        if ($except) {
            $select->where('id != :id');
            $bindValues['id'] = $except;
        }

        $count = $this->db->fetchValue($select->getStatement(), $bindValues);

        return (int)$count > 0;
    }
}
