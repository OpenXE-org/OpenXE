<?php

namespace Xentral\Modules\Api\Resource;

use Aura\SqlQuery\Exception;
use Xentral\Components\Database\SqlQuery\SelectQuery;

class DocumentScannerResource extends AbstractResource
{
    /** @var string TABLE_NAME */
    const TABLE_NAME = 'docscan';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'titel'                => 'd.titel %LIKE%',
            'titel_equals'         => 'd.titel LIKE',
            'titel_startswith'     => 'd.titel LIKE%',
            'titel_endswith'       => 'd.titel %LIKE',
            'dateiname'            => 'dv.dateiname %LIKE%',
            'dateiname_equals'     => 'dv.dateiname LIKE',
            'dateiname_startswith' => 'dv.dateiname LIKE%',
            'dateiname_endswith'   => 'dv.dateiname %LIKE',
            'datum'                => 'dv.datum =',
            'datum_gt'             => 'dv.datum >',
            'datum_gte'            => 'dv.datum >=',
            'datum_lt'             => 'dv.datum <',
            'datum_lte'            => 'dv.datum <=',
            'belegtyp'             => 'dsg.belegtypen %LIKE%',
            'stichwort'            => 'dsg.stichwoerter %LIKE%',
            'firma'                => 'd.firma =',
        ]);

        $this->registerSortingParams([
            'titel' => 'd.titel',
            'dateiname' => 'dv.dateiname',
            'datum' => 'dv.datum',
        ]);

        /*$this->registerValidationRules([
            'id' => 'not_present',
            'geloescht' => 'in:0,1',
        ]);*/

        $this->registerIncludes([
            'metadata' => [
                'key'      => 'metadata',
                'resource' => DocumentScannerMetaDataResource::class,
                'filter' => [
                    ['property' => 'docscan_id', 'value' => ':docscan_id'],
                ],
                'columns' => [
                    //'dm.id',
                    'dm.meta_key',
                    'dm.meta_value',
                ],
            ],
            'stichwoerter' => [
                'key'      => 'stichwoerter',
                'resource' => FileKeywordResource::class,
                'filter' => [
                    ['property' => 'datei', 'value' => ':id'],
                ],
                'columns' => [
                    'ds.id',
                    'ds.subjekt AS stichwort',
                    'ds.objekt AS belegtyp',
                    'ds.parameter AS beleg_id',
                    'ds.sort',
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
        return $this->db
            ->select()
            ->cols([
                'd.id',
                'doc.id AS docscan_id',
                'd.titel',
                'd.beschreibung',
                'd.nummer',
                'd.firma',
                'dv.ersteller',
                'dv.datum',
                'dv.version',
                'dv.dateiname',
                'dv.bemerkung',
                'dv.size',
//                'dsg.belegtypen',
//                'dsg.stichwoerter',
            ])
            ->from(self::TABLE_NAME . ' AS doc')
            ->innerJoin('datei AS d', 'doc.datei = d.id')
            ->innerJoin('datei_stichwoerter AS ds', 'd.id = ds.datei AND ds.objekt LIKE \'DocScan\'')
            ->joinSubSelect(
                'INNER',
                'SELECT dv.datei, MAX(dv.id) AS max_id 
                 FROM datei_version AS dv 
                 GROUP BY dv.datei',
                'dvm',
                'd.id = dvm.datei'
            )
            ->innerJoin(
                'datei_version AS dv',
                'd.id = dv.datei AND dv.id = dvm.max_id'
            )
            ->joinSubSelect( // wird für Filter benötigt
                'LEFT',
                'SELECT 
                   dsg.datei, 
                   GROUP_CONCAT(DISTINCT dsg.subjekt) AS stichwoerter, 
                   GROUP_CONCAT(DISTINCT dsg.objekt) AS belegtypen
                 FROM datei_stichwoerter AS dsg 
                 GROUP BY dsg.datei',
                'dsg',
                'd.id = dsg.datei'
            )
            ->where('d.geloescht <> 1');
    }

    /**
     * @return SelectQuery
     *
     * @throws Exception
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('d.id = :id');
    }

    /**
     * @return SelectQuery
     *
     * @throws Exception
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('d.id IN (:ids)');
    }

    /**
     * @internal Insert ist möglich; wird aber über den Controller verarbeitet.
     *
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
