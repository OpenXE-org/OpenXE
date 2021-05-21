<?php

namespace Xentral\Modules\Api\Resource;

use Aura\SqlQuery\Exception;
use Xentral\Components\Database\SqlQuery\DeleteQuery;
use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;

/**
 * Ressource für Lager Chargen
 */
class StorageBatchResource extends AbstractResource
{
    const TABLE_NAME = 'lager_charge';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'artikel'                          => 'lc.artikel =',
            'artikelnummer'                    => 'a.nummer %LIKE%',
            'artikelnummer_equals'             => 'a.nummer LIKE',
            'artikelnummer_startswith'         => 'a.nummer LIKE%',
            'artikelnummer_endswith'           => 'a.nummer %LIKE',
            'lagerplatz'                       => 'lc.lager_platz =',
            'lagerplatzbezeichnung'            => 'lp.kurzbezeichnung %LIKE%',
            'lagerplatzbezeichnung_equals'     => 'lp.kurzbezeichnung LIKE',
            'lagerplatzbezeichnung_startswith' => 'lp.kurzbezeichnung LIKE%',
            'lagerplatzbezeichnung_endswith'   => 'lp.kurzbezeichnung %LIKE',
            'charge'                           => 'lc.charge %LIKE%',
            'charge_equals'                    => 'lc.charge LIKE',
            'charge_startswith'                => 'lc.charge LIKE%',
            'charge_endswith'                  => 'lc.charge %LIKE',
            'datum'                            => 'lc.datum LIKE',
            'datum_gt'                         => 'lc.datum >',
            'datum_gte'                        => 'lc.datum >=',
            'datum_lt'                         => 'lc.datum <',
            'datum_lte'                        => 'lc.datum <=',
        ]);

        $this->registerSortingParams([
            'lagerplatzbezeichnung' => 'lp.kurzbezeichnung',
            'artikelnummer'         => 'a.nummer',
            'charge'                => 'lc.charge',
            'datum'                 => 'lc.datum',
            'menge'                 => 'lc_menge.menge',
        ]);

        $this->registerIncludes([
            'artikel'    => [
                'key'      => 'artikel',
                'resource' => ArticleResource::class,
                'columns'  => [
                    'a.id',
                    'a.nummer',
                    'a.name_de',
                    'a.name_en',
                ],
            ],
            'lagerplatz' => [
                'key'      => 'lagerplatz',
                'resource' => StorageLocationResource::class,
                'columns'  => [
                    'lp.id',
                    'l.bezeichnung AS lager',
                    'lp.kurzbezeichnung',
                    'lp.autolagersperre',
                    'lp.verbrauchslager',
                    'lp.sperrlager',
                    'lp.laenge',
                    'lp.breite',
                    'lp.hoehe',
                    'lp.geloescht',
                ],
            ],
        ]);
    }

    /**
     * @return SelectQuery
     *
     * @throws Exception
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                //'lc.id',
                'lc.artikel',
                'a.nummer AS artikelnummer',
                'lc.lager_platz AS lagerplatz',
                'lp.kurzbezeichnung AS lagerplatzbezeichnung',
                'lc.charge',
                'lc.datum',
                'lc_menge.menge',
                'lc.internebemerkung',
                //'lc.zwischenlagerid',
            ])
            ->from(self::TABLE_NAME . ' AS lc')
            ->innerJoin('artikel AS a', 'a.id = lc.artikel AND a.geloescht <> 1')
            ->innerJoin('lager_platz AS lp', 'lc.lager_platz = lp.id')
            ->joinSubSelect(
                'INNER',
                'SELECT lc.id, SUM(lc.menge) AS menge 
                 FROM lager_charge AS lc 
                 GROUP BY lc.artikel, lc.lager_platz, lc.charge',
                'lc_menge',
                'lc.id = lc_menge.id'
            );
    }

    /**
     * @return false
     */
    protected function selectOneQuery()
    {
        return false;
    }

    /**
     * @return false
     */
    protected function selectIdsQuery()
    {
        return false;
    }

    /**
     * @return InsertQuery|false
     */
    protected function insertQuery()
    {
        return false;
    }

    /**
     * @return UpdateQuery|false
     */
    protected function updateQuery()
    {
        return false;
    }

    /**
     * @return UpdateQuery|DeleteQuery|false
     */
    protected function deleteQuery()
    {
        return false;
    }
}
