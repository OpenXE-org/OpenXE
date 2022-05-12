<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

class PropertyResource extends AbstractResource
{
    const TABLE_NAME = 'artikeleigenschaften';

    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams(
            [
                'artikel'   => 'a.artikel =',
                'name'      => 'a.name =',
                'typ'       => 'a.typ =',
                'projekt'   => 'a.projekt =',
                'geloescht' => 'a.geloescht =',
            ]
        );

        $this->registerSortingParams(
            [
                'artikel'   => 'a.artikel =',
                'name'      => 'a.name =',
                'typ'       => 'a.typ =',
                'projekt'   => 'a.projekt =',
                'geloescht' => 'a.geloescht =',
            ]
        );

        $this->registerValidationRules(
            [
                'id'        => 'not_present',
                'artikel'   => 'integer',
                'projekt'   => 'integer',
                'geloescht' => 'in:0,1',
                'name' => 'unique:artikeleigenschaften,name'
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
                'a.artikel',
                'a.name',
                'a.typ',
                'a.projekt',
                'a.geloescht',
            ]
        )
            ->from('artikeleigenschaften AS a');
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
            ->table('artikeleigenschaften')
            ->where('id = :id');
    }

    /** @return false */
    protected function deleteQuery()
    {
        return $this->db->delete()
            ->from('artikeleigenschaften')
            ->where('id = :id');
    }

}
