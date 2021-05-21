<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

class SalesPriceResource extends AbstractResource
{
    const TABLE_NAME = 'verkaufspreise';

    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'waehrung' => 'vp.waehrung',
            'artikel' => 'vp.artikel =',
            'projekt' => 'vp.projekt =',
            'adresse' => 'vp.adresse =',
            'gruppe' => 'vp.gruppe =',
            'firma' => 'vp.firma =',
        ]);

        $this->registerSortingParams([
            'preis' => 'vp.preis',
            'menge' => 'vp.ab_menge',
            'vpe_menge' => 'vp.vpe_menge',
            'projekt' => 'k.projekt',
        ]);

        /*$this->registerValidationRules([
            'id' => 'not_present',
            'bezeichnung' => 'required|unique:artikelkategorien,bezeichnung',
            'next_number' => 'numeric',
            'projekt' => 'numeric',
            'parent' => 'numeric',
            'externenummer' => 'numeric',
            'geloescht' => 'in:0,1',
        ]);*/

        /*$this->registerIncludes([
            'projekte' => [
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
        ]);*/
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db
            ->select()
            ->cols([
                'vp.id',
                'vp.artikel',
                'vp.objekt',
                'vp.projekt',
                'vp.adresse',
                'vp.preis',
                'vp.waehrung',
                'vp.ab_menge',
                'vp.vpe',
                'vp.vpe_menge',
                'vp.angelegt_am',
                'vp.gueltig_ab',
                'vp.gueltig_bis',
                'vp.bemerkung',
                'vp.firma',
                'vp.kundenartikelnummer',
                'vp.nichtberechnet',
            ])
            ->from(self::TABLE_NAME . ' AS vp')
            ->where('vp.geloescht <> 1');
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('vp.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('vp.id IN (:ids)');
    }

    /**
     * @return false
     */
    protected function insertQuery()
    {
        return false;
    }

    /**
     * @return false
     */
    protected function updateQuery()
    {
        return false;
    }

    /**
     * @return false
     */
    protected function deleteQuery()
    {
        return false;
    }
}
