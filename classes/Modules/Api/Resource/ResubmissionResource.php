<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\InsertQuery;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Components\Database\SqlQuery\UpdateQuery;

class ResubmissionResource extends AbstractResource
{
    const TABLE_NAME = 'wiedervorlage';

    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'adresse'             => 'w.adresse =',
            'bearbeiter'          => 'w.bearbeiter =',
            'adresse_mitarbeiter' => 'w.adresse_mitarbeiter =',
            'projekt'             => 'w.projekt =',
            'stages'              => 'w.stages =',
            'id_ext'              => 'am.id_ext =',
        ]);

        $this->registerSortingParams([
            'datum_angelegt'   => 'w.datum_angelegt',
            'zeit_angelegt'    => 'w.zeit_angelegt',
            'datum_erinnerung' => 'w.datum_erinnerung',
            'zeit_erinnerung'  => 'w.zeit_erinnerung',
            'datum_abschluss'  => 'w.datum_abschluss',
            'bezeichnung'      => 'w.bezeichnung',
            'stages'           => 'w.stages',
            'prio'             => 'w.prio',
        ]);

        $this->registerValidationRules([
            'id'                  => 'not_present',
            'id_ext'              => 'not_present',
            'datum_angelegt'      => 'date:Y-m-d',
            'zeit_angelegt'       => 'time:H:i:s',
            'datum_erinnerung'    => 'required|date:Y-m-d',
            'zeit_erinnerung'     => 'required|time:H:i:s',
            'datum_abschluss'     => 'date:Y-m-d',
            'bezeichnung'         => 'required|min:3',
            'beschreibung'        => 'min:3',
            'bearbeiter'          => 'numeric|db_value:adresse,id',
            'adresse_mitarbeiter' => 'numeric|db_value:adresse,id',
            'projekt'             => 'numeric|db_value:projekt,id',
            'stages'              => 'numeric|db_value:wiedervorlage_stages,id',
            'betrag'              => 'decimal',
            'chance'              => 'integer|between:0,100',
            'erinnerung_per_mail' => 'in:0,1',
            'abgeschlossen'       => 'in:0,1',
            'oeffentlich'         => 'in:0,1',
            'prio'                => 'in:0,1',
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                'w.id',
                'w.adresse',
                'w.projekt',
                //'w.adresse_mitarbeier',
                'w.bezeichnung',
                'w.beschreibung',
                //'w.ergebnis',
                'w.betrag',
                //'w.erinnerung',
                'w.erinnerung_per_mail',
                //'w.erinnerung_empfaenger',
                //'w.link',
                //'w.module',
                //'w.action',
                //'w.parameter',
                //'w.status',
                'w.bearbeiter',
                'w.adresse_mitarbeiter',
                'w.datum_angelegt',
                'w.zeit_angelegt',
                'w.datum_erinnerung',
                'w.zeit_erinnerung',
                'w.datum_abschluss',
                'w.oeffentlich',
                'w.abgeschlossen',
                'w.chance',
                'w.prio',
                'w.stages',
                'w.color',
                'am.id_ext',
            ])->from(self::TABLE_NAME . ' AS w')
            ->leftJoin(
                'api_mapping AS am',
                'am.id_int = w.id AND am.tabelle = ' . $this->db->escapeString(self::TABLE_NAME)
            );
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('w.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('w.id IN (:ids)');
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
     * @return false
     */
    protected function deleteQuery()
    {
        return false;
    }
}
