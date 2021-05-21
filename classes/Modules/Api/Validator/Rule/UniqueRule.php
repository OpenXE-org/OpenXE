<?php

namespace Xentral\Modules\Api\Validator\Rule;

use Rakit\Validation\Rule;
use Xentral\Components\Database\Database;

class UniqueRule extends Rule
{
    /** @var string $message */
    protected $message = "The attribute ':attribute' has to be unique. The value ':value' is already in use.";

    /** @var array $fillable_params */
    protected $fillable_params = ['table', 'column', 'except'];

    /** @var Database $db */
    protected $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     *
     * @throws \Rakit\Validation\MissingRequiredParameterException
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

        return intval($count) === 0;
    }
}
