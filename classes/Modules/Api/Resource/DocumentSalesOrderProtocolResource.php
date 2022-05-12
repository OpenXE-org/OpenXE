<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressoure für das Auftrags-Protokoll
 *
 * Ressource hat keinen eigenen Endpunkt; Ressource wird nur für Includes verwendet.
 */
class DocumentSalesOrderProtocolResource extends AbstractResource
{
    /** @var string TABLE_NAME */
    const TABLE_NAME = 'auftrag_protokoll';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerSortingParams([
            'zeit' => 'auproto.zeit',
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('auproto.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db
            ->select()
            ->cols([
                'auproto.id',
                'auproto.auftrag',
                'auproto.zeit',
                'auproto.bearbeiter',
                'auproto.grund',
            ])
            ->from(self::TABLE_NAME . ' AS auproto');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('auproto.id IN (:ids)');
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
