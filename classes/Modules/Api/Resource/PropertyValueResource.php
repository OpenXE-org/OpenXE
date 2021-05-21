<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

class PropertyValueResource extends AbstractResource
{
    const TABLE_NAME = 'artikeleigenschaftenwerte';

    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams(
            [
                'artikeleigenschaften'   => 'a.artikeleigenschaften =',
                'artikel'   => 'a.artikel =',
                'wert'      => 'a.wert =',
            ]
        );

        $this->registerSortingParams(
            [
                'artikel'   => 'a.artikel =',
                'wert'      => 'a.wert =',
            ]
        );

        $this->registerValidationRules(
            [
                'id'        => 'not_present',
                'artikel'   => 'numeric|db_value:artikel,id',
                'artikeleigenschaften'   => 'numeric|db_value:artikeleigenschaften,id',
            ]
        );
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols(
            [
                'a.id',
                'a.artikeleigenschaften',
                'a.wert',
                'a.artikel',
            ]
        )
            ->from('artikeleigenschaftenwerte AS a');
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('a.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('a.id IN (:ids)');
    }

    /** @return false */
    protected function insertQuery()
    {
        return $this->db->insert()->into(self::TABLE_NAME);
    }

    /** @return false */
    protected function updateQuery()
    {
        return $this->db->update()
            ->table('artikeleigenschaftenwerte')
            ->where('id = :id');
    }

    /** @return false */
    protected function deleteQuery()
    {
        return $this->db->delete()
            ->from('artikeleigenschaftenwerte')
            ->where('id = :id');
    }

}
