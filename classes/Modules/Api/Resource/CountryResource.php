<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;

class CountryResource extends AbstractResource
{
    const TABLE_NAME = 'laender';

    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'bezeichnung_de' => 'l.bezeichnung_de %LIKE%',
            'bezeichnung_en' => 'l.bezeichnung_de %LIKE%',
            'iso' => 'l.iso =',
            'eu' => 'l.eu =',
            'id_ext' => 'am.id_ext =', // @todo
        ]);

        $this->registerSortingParams([
            'bezeichnung' => 'l.bezeichnung_de',
            'bezeichnung_de' => 'l.bezeichnung_de',
            'bezeichnung_en' => 'l.bezeichnung_en',
            'iso' => 'l.iso',
            'eu' => 'l.eu',
        ]);

        $this->registerValidationRules([
            'id' => 'not_present',
            'id_ext' => 'not_present', // @todo
            'bezeichnung_de' => 'required|unique:laender,bezeichnung_de',
            'bezeichnung_en' => 'required|unique:laender,bezeichnung_en',
            'iso' => 'required|upper|length:2|unique:laender,iso',
            'eu' => 'boolean',
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols(['l.*', 'am.id_ext'])->from(self::TABLE_NAME . ' AS l')
            ->leftJoin(
                'api_mapping AS am',
                'am.id_int = l.id AND am.tabelle = ' . $this->db->escapeString(self::TABLE_NAME)
            );
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('l.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('l.id IN (:ids)');
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
