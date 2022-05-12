<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;

class AddressTypeResource extends AbstractResource
{
    const TABLE_NAME = 'adresse_typ';

    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'bezeichnung' => 't.bezeichnung %LIKE%',
            'bezeichnung_exakt' => 't.bezeichnung LIKE',
            'type' => 't.type LIKE',
            'projekt' => 't.projekt =',
            'netto' => 't.netto =',
            'aktiv' => 't.aktiv =',
        ]);

        $this->registerSortingParams([
            'bezeichnung' => 't.bezeichnung',
            'type' => 't.type',
            'projekt' => 't.projekt',
            'netto' => 't.netto',
            'aktiv' => 't.aktiv',
        ]);

        $this->registerValidationRules([
            'id' => 'not_present',
            'bezeichnung' => 'required',
            'type' => 'required',
            'projekt' => 'numeric',
            'netto' => 'boolean',
            'aktiv' => 'boolean',
        ]);

        $this->registerIncludes([
            'projekt' => [
                'key'      => 'projekt',
                'resource' => ProjectResource::class,
                'columns'  => [
                    'p.id',
                    'p.name',
                    'p.abkuerzung',
                    'p.beschreibung',
                    'p.farbe',
                ],
            ],
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                't.id',
                't.type',
                't.bezeichnung',
                't.projekt',
                't.netto',
                't.aktiv',
            ])->from(self::TABLE_NAME . ' AS t')
            ->where('t.geloescht <> 1');
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('t.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('t.id IN (:ids)');
    }

    /**
     * @return InsertQuery
     */
    protected function insertQuery()
    {
        return $this->db->insert()->into(self::TABLE_NAME);
    }

    /**
     * @return UpdateQuery
     */
    protected function updateQuery()
    {
        return $this->db->update()->table(self::TABLE_NAME)->where('id = :id');
    }

    /**
     * @return false
     */
    protected function deleteQuery()
    {
        return false;
    }
}
