<?php

namespace Xentral\Modules\Api\Resource;

use Aura\SqlQuery\Exception;
use Xentral\Components\Database\SqlQuery\DeleteQuery;
use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;

/**
 * Ressource für Lager Mindesthaltbarkeitsdatum (MHD)
 */
class StorageBestBeforeDateResource extends AbstractResource
{
    const TABLE_NAME = 'lager_mindesthaltbarkeitsdatum';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'artikel'                          => 'lm.artikel =',
            'artikelnummer'                    => 'a.nummer %LIKE%',
            'artikelnummer_equals'             => 'a.nummer LIKE',
            'artikelnummer_startswith'         => 'a.nummer LIKE%',
            'artikelnummer_endswith'           => 'a.nummer %LIKE',
            'lagerplatz'                       => 'lm.lager_platz =',
            'lagerplatzbezeichnung'            => 'lp.kurzbezeichnung %LIKE%',
            'lagerplatzbezeichnung_equals'     => 'lp.kurzbezeichnung LIKE',
            'lagerplatzbezeichnung_startswith' => 'lp.kurzbezeichnung LIKE%',
            'lagerplatzbezeichnung_endswith'   => 'lp.kurzbezeichnung %LIKE',
            'charge'                           => 'lm.charge %LIKE%',
            'charge_equals'                    => 'lm.charge LIKE',
            'charge_startswith'                => 'lm.charge LIKE%',
            'charge_endswith'                  => 'lm.charge %LIKE',
            'mhddatum'                         => 'lm.mhddatum LIKE',
            'mhddatum_gt'                      => 'lm.mhddatum >',
            'mhddatum_gte'                     => 'lm.mhddatum >=',
            'mhddatum_lt'                      => 'lm.mhddatum <',
            'mhddatum_lte'                     => 'lm.mhddatum <=',
            'datum'                            => 'lm.datum LIKE',
            'datum_gt'                         => 'lm.datum >',
            'datum_gte'                        => 'lm.datum >=',
            'datum_lt'                         => 'lm.datum <',
            'datum_lte'                        => 'lm.datum <=',
        ]);

        $this->registerSortingParams([
            'lagerplatzbezeichnung' => 'lp.kurzbezeichnung',
            'artikelnummer'         => 'a.nummer',
            'charge'                => 'lm.charge',
            'mhddatum'              => 'lm.mhddatum',
            'datum'                 => 'lm.datum',
            'menge'                 => 'lm_menge.menge',
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
                //'lm.id',
                'lm.artikel',
                'a.nummer AS artikelnummer',
                'lm.lager_platz AS lagerplatz',
                'lp.kurzbezeichnung AS lagerplatzbezeichnung',
                'lm.charge',
                'lm.mhddatum',
                'lm.datum',
                'lm_menge.menge',
                'lm.internebemerkung',
                //'lm.zwischenlagerid',
            ])
            ->from(self::TABLE_NAME . ' AS lm')
            ->innerJoin('artikel AS a', 'a.id = lm.artikel AND a.geloescht <> 1')
            ->innerJoin('lager_platz AS lp', 'lm.lager_platz = lp.id')
            ->joinSubSelect(
                'INNER',
                'SELECT lm.id, SUM(lm.menge) AS menge 
                 FROM lager_mindesthaltbarkeitsdatum AS lm 
                 GROUP BY lm.artikel, lm.mhddatum, lm.lager_platz, lm.charge',
                'lm_menge',
                'lm.id = lm_menge.id'
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
