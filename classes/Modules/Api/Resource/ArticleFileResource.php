<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

class ArticleFileResource extends AbstractResource
{
    const TABLE_NAME = 'datei';

    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        /*$this->registerFilterParams([
            'bezeichnung' => 'd.bezeichnung %LIKE%',
            'bezeichnung_exakt' => 'd.bezeichnung LIKE',
            'projekt' => 'd.projekt =',
            'parent' => 'd.parent =',
        ]);*/

        /*$this->registerSortingParams([
            'bezeichnung' => 'd.bezeichnung',
            'projekt' => 'd.projekt',
            'parent' => 'd.parent',
        ]);*/

        /*$this->registerValidationRules([
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
        ]);*/

        /*$this->registerIncludes([
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
                'd.id',
                'd.titel',
                'd.beschreibung',
                'ds.subjekt',
                'ds.parameter AS artikel',
                'd.nummer',
            ])
            ->from(self::TABLE_NAME . ' AS d')
            ->where('d.geloescht <> 1')
            ->innerJoin(
                'datei_stichwoerter AS ds',
                'd.id = ds.datei AND ds.objekt = ' . $this->db->escapeString('Artikel')
            )
            /*->innerJoin(
                'datei_version AS dv',
                'd.id = dv.datei'
            )*/
            /*->joinSubSelect(
                'INNER',
                'SELECT MAX(dv.version) AS max_version, dv.datei, dv.ersteller, dv.datum, dv.bemerkung '.
                'FROM datei_version AS dv '.
                'GROUP BY dv.datei, dv.ersteller, dv.datum, dv.bemerkung',
                'dv',
                'd.id = dv.datei'
            )*/
            ;
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('d.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('d.id IN (:ids)');
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
