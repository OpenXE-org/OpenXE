<?php

namespace Xentral\Components\Database\SqlQuery;

use Aura\SqlQuery\Mysql\Select;
use Closure;

final class SelectQuery extends Select
{
    /**
     * @return bool
     */
    public function hasOrderBy()
    {
        return !empty($this->order_by);
    }

    /**
     * @param string         $andor
     * @param array|callable $args
     *
     * @return Select
     */
    protected function addWhere($andor, $args)
    {
        if ($args[0] instanceof Closure) {
            $this->addClauseCondClosure('where', $andor, $args[0]);

            return $this;
        }

        return parent::addWhere($andor, $args);
    }

    /**
     * Aura.SqlQuery 2.x unterstützt keine Klammersetzung in WHERE-Bedingungen
     *
     * @see https://github.com/auraphp/Aura.SqlQuery/issues/97
     *
     * Feature aus Version 3 importiert: https://github.com/auraphp/Aura.SqlQuery/pull/136/files
     *
     * @copyright Paul M. Jones
     * @license   MIT
     *
     * @param string   $clause
     * @param string   $andor
     * @param callable $closure
     */
    protected function addClauseCondClosure($clause, $andor, $closure)
    {
        // retain the prior set of conditions, and temporarily reset the clause
        // for the closure to work with (otherwise there will be an extraneous
        // opening AND/OR keyword)
        $set = $this->$clause;
        $this->$clause = [];

        // invoke the closure, which will re-populate the $this->$clause
        $closure($this);

        // are there new clause elements?
        if (!$this->$clause) {
            // no: restore the old ones, and done
            $this->$clause = $set;

            return;
        }

        // append an opening parenthesis to the prior set of conditions,
        // with AND/OR as needed ...
        if ($set) {
            $set[] = "{$andor} (";
        } else {
            $set[] = "(";
        }

        // append the new conditions to the set, with indenting
        foreach ($this->$clause as $cond) {
            $set[] = "    {$cond}";
        }
        $set[] = ")";

        // ... then put the full set of conditions back into $this->$clause
        $this->$clause = $set;
    }
}
