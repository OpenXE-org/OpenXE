<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\DeleteQuery;
use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;

class CrmDocumentResource extends AbstractResource
{
    const TABLE_NAME = 'dokumente';

    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'typ'           => 'd.typ %LIKE%',
            'typ_equals'     => 'd.typ LIKE',
            'typ_exakt'     => 'd.typ LIKE',
            'betreff'       => 'd.betreff %LIKE%',
            'betreff_equals' => 'd.betreff LIKE',
            'betreff_exakt' => 'd.betreff LIKE',
            'projekt'       => 'd.projekt =',
            'adresse_from'  => 'd.adresse_from =',
            'adresse_to'    => 'd.adresse_to =',
            'deleted'       => 'd.deleted =',
        ]);

        $this->registerValidationRules([
            'id'           => 'not_present',
            'typ'          => 'required|in:email,brief,telefon,notiz',
            'betreff'      => 'required',
            'projekt'      => 'numeric',
            'adresse_from' => 'numeric',
            'adresse_to'   => 'numeric',
            'signatur'     => 'numeric',
            'fax'          => 'boolean',
            'printer'      => 'boolean',
            'sent'         => 'boolean',
            'deleted'      => 'boolean',
        ]);

        $this->registerIncludes([
            'projekt'      => [
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
            'adresse_to'   => [
                'key'      => 'adresse_to',
                'resource' => AddressResource::class,
                'columns'  => [
                    'a.id',
                    'a.name',
                    'a.email',
                    'a.strasse',
                    'a.plz',
                    'a.ort',
                    'a.land',
                    'a.ansprechpartner',
                ],
            ],
            'adresse_from' => [
                'key'      => 'adresse_from',
                'resource' => AddressResource::class,
                'columns'  => [
                    'a.id',
                    'a.name',
                    'a.email',
                    'a.strasse',
                    'a.plz',
                    'a.ort',
                    'a.land',
                    'a.ansprechpartner',
                ],
            ],
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                'd.id',
                'd.adresse_from',
                'd.adresse_to',
                'd.typ',
                'd.von',
                'd.an',
                'd.email_an',
                'd.send_as',
                'd.email',
                'd.email_cc',
                'd.email_bcc',
                'd.bearbeiter',
                'd.email_an',
                'd.firma_an',
                'd.adresse',
                'd.ansprechpartner',
                'd.plz',
                'd.ort',
                'd.land',
                'd.datum',
                'd.uhrzeit',
                'd.betreff',
                'd.content',
                'd.projekt',
                'd.internebezeichnung',
                'd.signatur',
                'd.fax',
                'd.sent',
                'd.printer',
                'd.deleted',
            ])->from(self::TABLE_NAME . ' AS d');
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
     * @return InsertQuery
     */
    protected function insertQuery()
    {
        return $this->db->insert()->into(self::TABLE_NAME);
    }

    /**
     * @return UpdateQuery
     */
    protected function updateQuery()
    {
        return $this->db->update()->table(self::TABLE_NAME)->where('id = :id');
    }

    /**
     * @return DeleteQuery
     */
    protected function deleteQuery()
    {
        return $this->db->delete()->from(self::TABLE_NAME)->where('id = :id');
    }
}
