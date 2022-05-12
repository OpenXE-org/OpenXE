<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressoure für Rechnungs-Positionen
 *
 * Ressource hat keinen eigenen Endpunkt; Ressource wird nur für Includes verwendet.
 */
class DocumentInvoicePositionResource extends AbstractResource
{
    /** @var string TABLE_NAME */
    const TABLE_NAME = 'rechnung_position';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerSortingParams([
            'sort' => 'repos.sort',
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('repos.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db
            ->select()
            ->cols([
                'repos.id',
                //'repos.rechnung', // Index
                'repos.projekt',
                'repos.artikel', // Index
                'repos.bezeichnung',
                'repos.beschreibung',
                //'repos.internerkommentar',
                'repos.nummer',
                'repos.menge',
                'repos.preis',
                'repos.waehrung',
                'repos.lieferdatum',
                'repos.vpe',
                //'repos.sort',
                //'repos.status',
                'repos.umsatzsteuer',
                'repos.bemerkung',
                //'repos.logdatei',
                //'repos.explodiert_parent_artikel',
                //'repos.punkte',
                //'repos.bonuspunkte',
                //'repos.mlmdirektpraemie',
                //'repos.mlm_abgerechnet',
                //'repos.keinrabatterlaubt',
                //'repos.grundrabatt',
                //'repos.rabattsync',
                //'repos.rabatt1',
                //'repos.rabatt2',
                //'repos.rabatt3',
                //'repos.rabatt4',
                //'repos.rabatt5',
                'repos.einheit',
                'repos.rabatt',
                'repos.zolltarifnummer',
                'repos.herkunftsland',
                'repos.artikelnummerkunde',
                'repos.lieferdatumkw',
                //'repos.auftrag_position_id', // Index
                //'repos.teilprojekt',
                //'repos.kostenstelle',
                //'repos.erloese',
                //'repos.erloesefestschreiben',
                'repos.einkaufspreiswaehrung',
                'repos.einkaufspreis',
                'repos.einkaufspreisurspruenglich',
                //'repos.einkaufspreisid',
                //'repos.ekwaehrung',
                //'repos.deckungsbeitrag',
                //'repos.freifeld1',
                //'repos.freifeld2',
                //'repos.freifeld3',
                //'repos.freifeld4',
                //'repos.freifeld5',
                //'repos.freifeld6',
                //'repos.freifeld7',
                //'repos.freifeld8',
                //'repos.freifeld9',
                //'repos.freifeld10',
                //'repos.freifeld11',
                //'repos.freifeld12',
                //'repos.freifeld13',
                //'repos.freifeld14',
                //'repos.freifeld15',
                //'repos.freifeld16',
                //'repos.freifeld17',
                //'repos.freifeld18',
                //'repos.freifeld19',
                //'repos.freifeld20',
                //'repos.freifeld21',
                //'repos.freifeld22',
                //'repos.freifeld23',
                //'repos.freifeld24',
                //'repos.freifeld25',
                //'repos.freifeld26',
                //'repos.freifeld27',
                //'repos.freifeld28',
                //'repos.freifeld29',
                //'repos.freifeld30',
                //'repos.freifeld31',
                //'repos.freifeld32',
                //'repos.freifeld33',
                //'repos.freifeld34',
                //'repos.freifeld35',
                //'repos.freifeld36',
                //'repos.freifeld37',
                //'repos.freifeld38',
                //'repos.freifeld39',
                //'repos.freifeld40',
                //'repos.formelmenge',
                //'repos.formelpreis',
                'repos.ohnepreis',
                'repos.steuersatz',
                'repos.steuertext',
                'repos.steuerbetrag',
                'repos.skontobetrag',
                'repos.skontosperre',
                'repos.ausblenden_im_pdf',
                //'repos.umsatz_netto_einzeln',
                //'repos.umsatz_netto_gesamt',
                //'repos.umsatz_brutto_einzeln',
                //'repos.umsatz_brutto_gesamt',
            ])
            ->from(self::TABLE_NAME . ' AS repos');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('repos.id IN (:ids)');
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
