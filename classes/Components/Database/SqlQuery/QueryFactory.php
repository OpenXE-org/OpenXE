<?php

namespace Xentral\Components\Database\SqlQuery;

use Aura\SqlQuery\AbstractQuery;
use Aura\SqlQuery\QueryFactory as AuraQueryFactory;

final class QueryFactory extends AuraQueryFactory
{
    /**
     * @return SelectQuery
     */
    public function newSelect()
    {
        return $this->newInstance('Select');
    }

    /**
     * @return InsertQuery
     */
    public function newInsert()
    {
        $insert = $this->newInstance('Insert');
        $insert->setLastInsertIdNames($this->last_insert_id_names);

        return $insert;
    }

    /**
     * @return UpdateQuery
     */
    public function newUpdate()
    {
        return $this->newInstance('Update');
    }

    /**
     * @return DeleteQuery
     */
    public function newDelete()
    {
        return $this->newInstance('Delete');
    }

    /**
     * @param string $query The query object type.
     *
     * @return AbstractQuery
     */
    protected function newInstance($query)
    {
        $class = "Xentral\\Components\\Database\\SqlQuery\\{$query}Query";

        return new $class(
            $this->getQuoter(),
            $this->newSeqBindPrefix()
        );
    }
}
