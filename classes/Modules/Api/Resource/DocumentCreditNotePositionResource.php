<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressoure für Gutschriften-Positionen
 *
 * Ressource hat keinen eigenen Endpunkt; Ressource wird nur für Includes verwendet.
 */
class DocumentCreditNotePositionResource extends AbstractResource
{
    /** @var string TABLE_NAME */
    const TABLE_NAME = 'gutschrift_position';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerSortingParams([
            'sort' => 'gupos.sort',
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('gupos.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db
            ->select()
            ->cols([
                'gupos.id',
                //'gupos.gutschrift', // Index
                'gupos.projekt',
                'gupos.artikel', // Index
                'gupos.bezeichnung',
                'gupos.beschreibung',
                //'gupos.internerkommentar',
                'gupos.nummer',
                'gupos.menge',
                'gupos.einheit',
                'gupos.preis',
                'gupos.waehrung',
                'gupos.lieferdatum',
                'gupos.vpe',
                //'gupos.sort',
                //'gupos.status',
                'gupos.umsatzsteuer',
                'gupos.bemerkung',
                'gupos.artikelnummerkunde',
                //'gupos.logdatei',
                //'gupos.explodiert_parent_artikel',
                //'gupos.keinrabatterlaubt',
                //'gupos.grundrabatt',
                //'gupos.rabattsync',
                //'gupos.rabatt1',
                //'gupos.rabatt2',
                //'gupos.rabatt3',
                //'gupos.rabatt4',
                //'gupos.rabatt5',
                'gupos.rabatt',
                'gupos.zolltarifnummer',
                'gupos.herkunftsland',
                'gupos.lieferdatumkw',
                'gupos.auftrag_position_id',
                'gupos.teilprojekt',
                'gupos.kostenstelle',
                'gupos.steuersatz',
                'gupos.steuertext',
                //'gupos.erloese',
                //'gupos.erloesefestschreiben',
                'gupos.einkaufspreiswaehrung',
                'gupos.einkaufspreis',
                'gupos.einkaufspreisurspruenglich',
                //'gupos.einkaufspreisid',
                //'gupos.ekwaehrung',
                //'gupos.deckungsbeitrag',
                //'gupos.freifeld1',
                //'gupos.freifeld2',
                //'gupos.freifeld3',
                //'gupos.freifeld4',
                //'gupos.freifeld5',
                //'gupos.freifeld6',
                //'gupos.freifeld7',
                //'gupos.freifeld8',
                //'gupos.freifeld9',
                //'gupos.freifeld10',
                //'gupos.freifeld11',
                //'gupos.freifeld12',
                //'gupos.freifeld13',
                //'gupos.freifeld14',
                //'gupos.freifeld15',
                //'gupos.freifeld16',
                //'gupos.freifeld17',
                //'gupos.freifeld18',
                //'gupos.freifeld19',
                //'gupos.freifeld20',
                //'gupos.freifeld21',
                //'gupos.freifeld22',
                //'gupos.freifeld23',
                //'gupos.freifeld24',
                //'gupos.freifeld25',
                //'gupos.freifeld26',
                //'gupos.freifeld27',
                //'gupos.freifeld28',
                //'gupos.freifeld29',
                //'gupos.freifeld30',
                //'gupos.freifeld31',
                //'gupos.freifeld32',
                //'gupos.freifeld33',
                //'gupos.freifeld34',
                //'gupos.freifeld35',
                //'gupos.freifeld36',
                //'gupos.freifeld37',
                //'gupos.freifeld38',
                //'gupos.freifeld39',
                //'gupos.freifeld40',
                //'gupos.formelmenge',
                //'gupos.formelpreis',
                'gupos.ohnepreis',
                'gupos.skontobetrag',
                'gupos.steuerbetrag',
                'gupos.skontosperre',
                'gupos.ausblenden_im_pdf',
                //'gupos.umsatz_netto_einzeln',
                //'gupos.umsatz_netto_gesamt',
                //'gupos.umsatz_brutto_einzeln',
                //'gupos.umsatz_brutto_gesamt',
            ])
            ->from(self::TABLE_NAME . ' AS gupos');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('gupos.id IN (:ids)');
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
