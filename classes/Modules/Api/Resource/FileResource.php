<?php

namespace Xentral\Modules\Api\Resource;

use Aura\SqlQuery\Exception;
use Xentral\Components\Database\SqlQuery\SelectQuery;

class FileResource extends AbstractResource
{
    const TABLE_NAME = 'datei';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'titel' => 'd.titel %LIKE%',
            'titel_equals' => 'd.titel LIKE',
            'titel_startswith' => 'd.titel LIKE%',
            'titel_endswith' => 'd.titel %LIKE',
            'dateiname' => 'dv.dateiname %LIKE%',
            'dateiname_equals' => 'dv.dateiname LIKE',
            'dateiname_startswith' => 'dv.dateiname LIKE%',
            'dateiname_endswith' => 'dv.dateiname %LIKE',
            'belegtyp' => 'ds.belegtypen %LIKE%',
            'stichwort' => 'ds.stichwoerter %LIKE%',
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
                'ds.belegtypen',
                'ds.stichwoerter',
            ])
            ->from(self::TABLE_NAME . ' AS d')
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
            ->joinSubSelect(
                'LEFT',
                'SELECT 
                   ds.datei, 
                   GROUP_CONCAT(ds.subjekt) AS stichwoerter, 
                   GROUP_CONCAT(ds.objekt) AS belegtypen
                 FROM datei_stichwoerter AS ds 
                 GROUP BY ds.datei',
                'ds',
                'd.id = ds.datei'
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
     * @internal Insert ist möglich; wird aber über den FileController verarbeitet.
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
