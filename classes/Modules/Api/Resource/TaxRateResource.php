<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;

class TaxRateResource extends AbstractResource
{
    const TABLE_NAME = 'steuersaetze';

    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'bezeichnung' => 's.bezeichnung %LIKE%',
            'country_code' => 's.country_code %LIKE%',
            'satz' => 's.satz =',
            'aktiv' => 's.aktiv =',
        ]);

        $this->registerSortingParams([
            'bezeichnung' => 's.bezeichnung',
            'country_code' => 's.country_code',
            'satz' => 's.satz',
            'aktiv' => 's.aktiv',
        ]);

        $this->registerValidationRules([
            'id' => 'not_present',
            'bezeichnung' => 'required|unique:steuersaetze,bezeichnung',
            'satz' => 'required|decimal',
            'aktiv' => 'boolean',
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                's.id',
                's.bezeichnung',
                's.country_code',
                's.satz',
                's.aktiv',
            ])->from(self::TABLE_NAME . ' AS s');
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('s.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('s.id IN (:ids)');
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
