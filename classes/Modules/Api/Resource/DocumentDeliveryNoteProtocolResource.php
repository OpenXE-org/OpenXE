<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressoure für das Lieferschein-Protokoll
 *
 * Ressource hat keinen eigenen Endpunkt; Ressource wird nur für Incldudes verwendet.
 */
class DocumentDeliveryNoteProtocolResource extends AbstractResource
{
    /** @var string TABLE_NAME */
    const TABLE_NAME = 'lieferschein_protokoll';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerSortingParams([
            'zeit' => 'liproto.zeit',
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('liproto.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db
            ->select()
            ->cols([
                'liproto.id',
                'liproto.lieferschein',
                'liproto.zeit',
                'liproto.bearbeiter',
                'liproto.grund',
            ])
            ->from(self::TABLE_NAME . ' AS liproto');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('liproto.id IN (:ids)');
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
