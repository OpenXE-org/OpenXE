<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;

class GroupResource extends AbstractResource
{
    const TABLE_NAME = 'gruppen';

    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'name' => 'g.name %LIKE%',
            'name_exakt' => 'g.name LIKE',
            'kennziffer' => 'g.kennziffer %LIKE%',
            'kennziffer_exakt' => 'g.kennziffer LIKE',
            'art' => 'g.art LIKE',
            'projekt' => 'g.projekt =',
            'kategorie' => 'g.kategorie =',
            'aktiv' => 'g.aktiv =',
        ]);

        $this->registerSortingParams([
            'name' => 'g.name',
            'art' => 'g.art',
            'kennziffer' => 'g.kennziffer',
            'projekt' => 'g.projekt',
            'kategorie' => 'g.kategorie',
            'aktiv' => 'g.aktiv',
        ]);

        $this->registerValidationRules([
            'id' => 'not_present',
            'name' => 'required',
            'kennziffer' => 'required|alpha_dash|unique:gruppen,kennziffer',
            'art' => 'in:gruppe,preisgruppe,verband,regionalgruppe,kategorie,vertreter',
            'projekt' => 'numeric',
            'kategorie' => 'numeric',
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
            // @todo Gruppenkategorien
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                'g.id',
                'g.name',
                'g.art',
                'g.kennziffer',
                'g.internebemerkung',
                'g.projekt',
                'g.kategorie',
                'g.aktiv',
            ])->from(self::TABLE_NAME . ' AS g');
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('g.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('g.id IN (:ids)');
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
