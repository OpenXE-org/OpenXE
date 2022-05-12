<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;

class PaymentMethodResource extends AbstractResource
{
    const TABLE_NAME = 'zahlungsweisen';

    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'bezeichnung' => 'z.bezeichnung %LIKE%',
            'bezeichnung_exakt' => 'z.bezeichnung LIKE',
            'type' => 'z.type %LIKE%',
            'type_exakt' => 'z.type LIKE',
            'projekt' => 'z.projekt =',
            'verhalten' => 'z.verhalten =',
            'aktiv' => 'z.aktiv =',
        ]);

        $this->registerSortingParams([
            'bezeichnung' => 'z.bezeichnung',
            'type' => 'z.type',
            'projekt' => 'z.projekt',
            'modul' => 'z.modul',
            'aktiv' => 'z.aktiv',
        ]);

        $this->registerValidationRules([
            'id' => 'not_present',
            'einstellungen_json' => 'not_present',
            'freitext' => 'not_present',
            'bezeichnung' => 'required',
            'type' => 'required|unique:zahlungsweisen,type',
            'projekt' => 'numeric',
            'aktiv' => 'boolean',
            'vorkasse' => 'boolean',
            'automatischbezahlt' => 'boolean',
            'automatischbezahltverbindlichkeit' => 'boolean',
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
                'z.id',
                'z.type',
                'z.bezeichnung',
                'z.freitext',
                'z.aktiv',
                'z.automatischbezahlt',
                'z.automatischbezahltverbindlichkeit',
                'z.projekt',
                'z.vorkasse',
                'z.verhalten',
                'z.modul',
            ])->from(self::TABLE_NAME . ' AS z')
            ->where('z.geloescht <> 1');
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('z.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('z.id IN (:ids)');
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
