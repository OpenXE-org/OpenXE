<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;

class ArticleCategoryResource extends AbstractResource
{
    const TABLE_NAME = 'artikelkategorien';

    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'bezeichnung' => 'k.bezeichnung %LIKE%',
            'bezeichnung_exakt' => 'k.bezeichnung LIKE',
            'projekt' => 'k.projekt =',
            'parent' => 'k.parent =',
        ]);

        $this->registerSortingParams([
            'bezeichnung' => 'k.bezeichnung',
            'projekt' => 'k.projekt',
            'parent' => 'k.parent',
        ]);

        $this->registerValidationRules([
            'id' => 'not_present',
            'id_ext' => 'not_present', // @todo
            'bezeichnung' => 'required|unique:artikelkategorien,bezeichnung',
            'next_number' => 'numeric',
            'projekt' => 'numeric',
            'parent' => 'numeric',
            'externenummer' => 'numeric',
            'geloescht' => 'in:0,1',
            //'id_ext' => 'numeric', @todo
            // @todo Steuerfelder
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
            ->cols(['k.*', 'am.id_ext'])->from(self::TABLE_NAME . ' AS k')->where('k.geloescht <> 1')
            ->leftJoin(
                'api_mapping AS am',
                'am.id_int = k.id AND am.tabelle = ' . $this->db->escapeString('artikelkategorien')
            );
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('k.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('k.id IN (:ids)');
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
