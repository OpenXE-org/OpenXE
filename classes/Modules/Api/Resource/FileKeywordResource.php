<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressource hat keinen eigenen API-Endpunkt (keine URL).
 * Ressource dient nur als Include für die Dateien-Ressource.
 */
class FileKeywordResource extends AbstractResource
{
    const TABLE_NAME = 'datei_stichwoerter';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db
            ->select()
            ->cols([
                'ds.id',
                'ds.subjekt',
                'ds.objekt',
                'ds.parameter',
                'ds.sort',
            ])
            ->from(self::TABLE_NAME . ' AS ds');
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
