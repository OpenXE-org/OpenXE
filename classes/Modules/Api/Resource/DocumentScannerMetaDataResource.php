<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressource hat keinen eigenen API-Endpunkt (keine URL).
 * Ressource dient nur als Include für die DocumentScanner-Ressource.
 */
class DocumentScannerMetaDataResource extends AbstractResource
{
    /** @var string TABLE_NAME */
    const TABLE_NAME = 'docscan_metadata';

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
                'dm.id',
                'dm.meta_key',
                'dm.meta_value',
            ])
            ->from(self::TABLE_NAME . ' AS dm');
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
