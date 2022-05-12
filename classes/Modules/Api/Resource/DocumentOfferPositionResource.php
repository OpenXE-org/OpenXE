<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressoure für Angebotspositionen
 *
 * Ressource hat keinen eigenen Endpunkt; Ressource wird nur für Includes verwendet.
 */
class DocumentOfferPositionResource extends AbstractResource
{
    /** @var string TABLE_NAME */
    const TABLE_NAME = 'angebot_position';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerSortingParams([
            'sort' => 'anpos.sort',
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('anpos.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db
            ->select()
            ->cols([
                'anpos.id',
                //'anpos.angebot', // Index
                'anpos.projekt',
                'anpos.artikel', // Index
                'anpos.bezeichnung',
                'anpos.beschreibung',
                //'anpos.internerkommentar',
                'anpos.nummer',
                'anpos.menge',
                'anpos.preis',
                'anpos.waehrung',
                'anpos.lieferdatum',
                'anpos.vpe',
                //'anpos.sort',
                //'anpos.status',
                'anpos.umsatzsteuer',
                'anpos.bemerkung',
                'anpos.geliefert',
                //'anpos.logdatei',
                //'anpos.punkte',
                //'anpos.bonuspunkte',
                //'anpos.mlmdirektpraemie',
                //'anpos.keinrabatterlaubt',
                //'anpos.grundrabatt',
                //'anpos.rabattsync',
                //'anpos.rabatt1',
                //'anpos.rabatt2',
                //'anpos.rabatt3',
                //'anpos.rabatt4',
                //'anpos.rabatt5',
                'anpos.einheit',
                'anpos.optional',
                'anpos.rabatt',
                'anpos.zolltarifnummer',
                'anpos.herkunftsland',
                'anpos.artikelnummerkunde',
                'anpos.lieferdatumkw',
                //'anpos.teilprojekt',
                //'anpos.kostenstelle',
                //'anpos.erloese',
                //'anpos.erloesefestschreiben',
                //'anpos.einkaufspreiswaehrung',
                'anpos.einkaufspreis',
                'anpos.einkaufspreisurspruenglich',
                //'anpos.einkaufspreisid',
                //'anpos.ekwaehrung',
                //'anpos.deckungsbeitrag',
                //'anpos.freifeld1',
                //'anpos.freifeld2',
                //'anpos.freifeld3',
                //'anpos.freifeld4',
                //'anpos.freifeld5',
                //'anpos.freifeld6',
                //'anpos.freifeld7',
                //'anpos.freifeld8',
                //'anpos.freifeld9',
                //'anpos.freifeld10',
                //'anpos.freifeld11',
                //'anpos.freifeld12',
                //'anpos.freifeld13',
                //'anpos.freifeld14',
                //'anpos.freifeld15',
                //'anpos.freifeld16',
                //'anpos.freifeld17',
                //'anpos.freifeld18',
                //'anpos.freifeld19',
                //'anpos.freifeld20',
                //'anpos.freifeld21',
                //'anpos.freifeld22',
                //'anpos.freifeld23',
                //'anpos.freifeld24',
                //'anpos.freifeld25',
                //'anpos.freifeld26',
                //'anpos.freifeld27',
                //'anpos.freifeld28',
                //'anpos.freifeld29',
                //'anpos.freifeld30',
                //'anpos.freifeld31',
                //'anpos.freifeld32',
                //'anpos.freifeld33',
                //'anpos.freifeld34',
                //'anpos.freifeld35',
                //'anpos.freifeld36',
                //'anpos.freifeld37',
                //'anpos.freifeld38',
                //'anpos.freifeld39',
                //'anpos.freifeld40',
                //'anpos.formelmenge',
                //'anpos.formelpreis',
                'anpos.ohnepreis',
                'anpos.textalternativpreis',
                'anpos.steuersatz',
                'anpos.steuertext',
                'anpos.steuerbetrag',
                'anpos.skontobetrag',
                'anpos.skontosperre',
                'anpos.berechnen_aus_teile',
                'anpos.ausblenden_im_pdf',
                //'anpos.explodiert_parent',
                //'anpos.umsatz_netto_einzeln',
                //'anpos.umsatz_netto_gesamt',
                //'anpos.umsatz_brutto_einzeln',
                //'anpos.umsatz_brutto_gesamt',
            ])
            ->from(self::TABLE_NAME . ' AS anpos');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('anpos.id IN (:ids)');
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
