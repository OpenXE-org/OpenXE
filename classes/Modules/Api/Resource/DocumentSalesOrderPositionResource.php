<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressoure für Auftragspositionen
 *
 * Ressource hat keinen eigenen Endpunkt; Ressource wird nur für Includes verwendet.
 */
class DocumentSalesOrderPositionResource extends AbstractResource
{
    /** @var string TABLE_NAME */
    const TABLE_NAME = 'auftrag_position';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerSortingParams([
            'sort' => 'aupos.sort',
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('aupos.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db
            ->select()
            ->cols([
                'aupos.id',
                'aupos.auftrag', // Index
                'aupos.projekt',
                'aupos.artikel', // Index
                'aupos.bezeichnung',
                'aupos.beschreibung',
                //'aupos.internerkommentar',
                'aupos.nummer',
                'aupos.menge',
                'aupos.preis',
                'aupos.waehrung',
                'aupos.lieferdatum',
                'aupos.vpe',
                //'aupos.sort',
                //'aupos.status',
                'aupos.umsatzsteuer',
                'aupos.bemerkung',
                'aupos.geliefert',
                'aupos.geliefert_menge',
                //'aupos.logdatei',
                //'aupos.punkte',
                //'aupos.bonuspunkte',
                //'aupos.mlmdirektpraemie',
                //'aupos.keinrabatterlaubt',
                //'aupos.grundrabatt',
                //'aupos.rabattsync',
                //'aupos.rabatt1',
                //'aupos.rabatt2',
                //'aupos.rabatt3',
                //'aupos.rabatt4',
                //'aupos.rabatt5',
                'aupos.einheit',
                'aupos.webid',
                'aupos.rabatt',
                'aupos.nachbestelltexternereinkauf',
                'aupos.potentiellerliefertermin',
                'aupos.zolleinzelwert',
                'aupos.zollgesamtwert',
                'aupos.zollwaehrung',
                'aupos.zolleinzelgewicht',
                'aupos.zollgesamtgewicht',
                'aupos.zolltarifnummer',
                'aupos.herkunftsland',
                'aupos.artikelnummerkunde',
                'aupos.lieferdatumkw',
                //'aupos.teilprojekt',
                //'aupos.kostenstelle',
                //'aupos.erloese',
                //'aupos.erloesefestschreiben',
                //'aupos.einkaufspreiswaehrung',
                'aupos.einkaufspreis',
                'aupos.einkaufspreisurspruenglich',
                //'aupos.einkaufspreisid',
                //'aupos.ekwaehrung',
                //'aupos.deckungsbeitrag',
                //'aupos.freifeld1',
                //'aupos.freifeld2',
                //'aupos.freifeld3',
                //'aupos.freifeld4',
                //'aupos.freifeld5',
                //'aupos.freifeld6',
                //'aupos.freifeld7',
                //'aupos.freifeld8',
                //'aupos.freifeld9',
                //'aupos.freifeld10',
                //'aupos.freifeld11',
                //'aupos.freifeld12',
                //'aupos.freifeld13',
                //'aupos.freifeld14',
                //'aupos.freifeld15',
                //'aupos.freifeld16',
                //'aupos.freifeld17',
                //'aupos.freifeld18',
                //'aupos.freifeld19',
                //'aupos.freifeld20',
                //'aupos.freifeld21',
                //'aupos.freifeld22',
                //'aupos.freifeld23',
                //'aupos.freifeld24',
                //'aupos.freifeld25',
                //'aupos.freifeld26',
                //'aupos.freifeld27',
                //'aupos.freifeld28',
                //'aupos.freifeld29',
                //'aupos.freifeld30',
                //'aupos.freifeld31',
                //'aupos.freifeld32',
                //'aupos.freifeld33',
                //'aupos.freifeld34',
                //'aupos.freifeld35',
                //'aupos.freifeld36',
                //'aupos.freifeld37',
                //'aupos.freifeld38',
                //'aupos.freifeld39',
                //'aupos.freifeld40',
                //'aupos.formelmenge',
                //'aupos.formelpreis',
                'aupos.ohnepreis',
                'aupos.steuersatz',
                'aupos.steuertext',
                'aupos.steuerbetrag',
                'aupos.skontobetrag',
                'aupos.skontosperre',
                'aupos.ausblenden_im_pdf',
                //'aupos.explodiert',
                //'aupos.explodiert_parent', // Index
                //'aupos.umsatz_netto_einzeln',
                //'aupos.umsatz_netto_gesamt',
                //'aupos.umsatz_brutto_einzeln',
                //'aupos.umsatz_brutto_gesamt',
            ])
            ->from(self::TABLE_NAME . ' AS aupos');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('aupos.id IN (:ids)');
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
