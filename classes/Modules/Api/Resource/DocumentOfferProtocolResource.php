<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressoure für das Angebots-Protokoll
 *
 * Ressource hat keinen eigenen Endpunkt; Ressource wird nur für Incldudes verwendet.
 */
class DocumentOfferProtocolResource extends AbstractResource
{
    /** @var string TABLE_NAME */
    const TABLE_NAME = 'angebot_protokoll';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerSortingParams([
            'zeit' => 'anproto.zeit',
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('anproto.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db
            ->select()
            ->cols([
                'anproto.id',
                //'anproto.angebot',
                'anproto.zeit',
                'anproto.bearbeiter',
                'anproto.grund',
            ])
            ->from(self::TABLE_NAME . ' AS anproto');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('anproto.id IN (:ids)');
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
