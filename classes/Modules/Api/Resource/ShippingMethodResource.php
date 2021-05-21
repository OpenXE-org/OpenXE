<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;

class ShippingMethodResource extends AbstractResource
{
    const TABLE_NAME = 'versandarten';

    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'bezeichnung' => 'v.bezeichnung %LIKE%',
            'bezeichnung_exakt' => 'v.bezeichnung LIKE',
            'type' => 'v.type %LIKE%',
            'type_exakt' => 'v.type LIKE',
            'projekt' => 'v.projekt =',
            'modul' => 'v.modul =',
            'aktiv' => 'v.aktiv =',
        ]);

        $this->registerSortingParams([
            'bezeichnung' => 'v.bezeichnung',
            'type' => 'v.type',
            'projekt' => 'v.projekt',
            'modul' => 'v.modul',
            'aktiv' => 'v.aktiv',
        ]);

        $this->registerValidationRules([
            'id' => 'not_present',
            'einstellungen_json' => 'not_present',
            'bezeichnung' => 'required',
            'type' => 'required|unique:versandarten,type',
            'projekt' => 'numeric',
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
                'v.id',
                'v.type',
                'v.bezeichnung',
                'v.aktiv',
                'v.projekt',
                'v.modul',
                'v.paketmarke_drucker',
                'v.export_drucker',
                'v.ausprojekt',
                'v.versandmail',
                'v.geschaeftsbrief_vorlage',
            ])->from(self::TABLE_NAME . ' AS v')
            ->where('v.geloescht <> 1');
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('v.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('v.id IN (:ids)');
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
