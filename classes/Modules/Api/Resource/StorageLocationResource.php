<?php

namespace Xentral\Modules\Api\Resource;

use Aura\SqlQuery\Exception;
use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressoure für Lagerplatz
 *
 * Ressource hat keinen eigenen Endpunkt; Ressource wird nur für Incldudes verwendet.
 */
class StorageLocationResource extends AbstractResource
{
    const TABLE_NAME = 'lager_platz';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);
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
                'lp.id',
                'lp.lager',
                'lp.kurzbezeichnung',
                'lp.bemerkung',
                'lp.projekt',
                'lp.firma',
                'lp.geloescht',
                'lp.logdatei',
                'lp.autolagersperre',
                'lp.verbrauchslager',
                'lp.sperrlager',
                'lp.laenge',
                'lp.breite',
                'lp.hoehe',
                'lp.poslager',
                'lp.adresse',
            ])
            ->from(self::TABLE_NAME . ' AS lp')
            ->innerJoin('lager AS l', 'l.id = lp.lager')
            ->where('lp.geloescht <> 1');
    }

    /**
     * @return SelectQuery
     *
     * @throws Exception
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('lp.id = :id');
    }

    /**
     * @return SelectQuery
     *
     * @throws Exception
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('lp.id IN (:ids)');
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
