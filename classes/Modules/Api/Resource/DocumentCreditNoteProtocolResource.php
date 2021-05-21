<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressoure für das Gutschrift-Protokoll
 *
 * Ressource hat keinen eigenen Endpunkt; Ressource wird nur für Includes verwendet.
 */
class DocumentCreditNoteProtocolResource extends AbstractResource
{
    /** @var string TABLE_NAME */
    const TABLE_NAME = 'gutschrift_protokoll';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerSortingParams([
            'zeit' => 'guproto.zeit',
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('guproto.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db
            ->select()
            ->cols([
                'guproto.id',
                'guproto.gutschrift',
                'guproto.zeit',
                'guproto.bearbeiter',
                'guproto.grund',
            ])
            ->from(self::TABLE_NAME . ' AS guproto');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('guproto.id IN (:ids)');
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
