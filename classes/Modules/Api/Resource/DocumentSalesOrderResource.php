<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressource für Aufträge (Verkauf)
 */
class DocumentSalesOrderResource extends AbstractResource
{
    /** @var string */
    const TABLE_NAME = 'auftrag';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'status'                  => 'au.status LIKE',
            'belegnr'                 => 'au.belegnr %LIKE%',
            'belegnr_equals'          => 'au.belegnr LIKE',
            'belegnr_startswith'      => 'au.belegnr LIKE%',
            'belegnr_endswith'        => 'au.belegnr %LIKE',
            'kundennummer'            => 'au.kundennummer %LIKE%',
            'kundennummer_equals'     => 'au.kundennummer LIKE',
            'kundennummer_startswith' => 'au.kundennummer LIKE%',
            'kundennummer_endswith'   => 'au.kundennummer %LIKE',
            'internet'                => 'au.internet %LIKE%',
            'internet_equals'         => 'au.internet LIKE',
            'internet_startswith'     => 'au.internet LIKE%',
            'internet_endswith'       => 'au.internet %LIKE',
            'datum'                   => 'au.datum =',
            'datum_gt'                => 'au.datum >',
            'datum_gte'               => 'au.datum >=',
            'datum_lt'                => 'au.datum <',
            'datum_lte'               => 'au.datum <=',
            'angebot'                 => 'au.angebot LIKE',
            'angebotid'               => 'au.angebotid =',
            'projekt'                 => 'au.projekt =',
        ]);

        $this->registerSortingParams([
            'belegnr' => 'au.belegnr',
            'datum'   => 'au.datum',
        ]);

        $this->registerIncludes([
            'positionen' => [
                'key'      => 'positionen',
                'resource' => DocumentSalesOrderPositionResource::class,
                'filter'   => [
                    [
                        'property' => 'auftrag',
                        'value'    => ':id',
                    ],
                ],
                'sort'     => [
                    'sort' => 'ASC',
                ],
            ],
            'protokoll'  => [
                'key'      => 'protokoll',
                'resource' => DocumentSalesOrderProtocolResource::class,
                'columns'  => [
                    'auproto.id',
                    'auproto.zeit',
                    'auproto.bearbeiter',
                    'auproto.grund',
                ],
                'filter'   => [
                    [
                        'property' => 'auftrag',
                        'value'    => ':id',
                    ],
                ],
                'sort'     => [
                    'zeit' => 'ASC',
                ],
            ],
        ]);
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('au.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                'au.id',
                'au.firma',
                'au.projekt', // Index
                'au.status', // Index
                'au.belegnr', // Index
                'au.kundennummer',
                'au.lieferantenauftrag',
                'au.lieferant',
                'au.aktion',
                'au.angebot',
                'au.ihrebestellnummer',
                'au.internet', // Index
                'au.internebezeichnung',
                'au.datum',
                'au.lieferdatum',
                'au.lieferdatumkw',
                'au.tatsaechlicheslieferdatum',
                'au.reservationdate',
                'au.abweichendebezeichnung',

                'au.adresse', // Index
                'au.typ',
                'au.name',
                'au.titel',
                'au.ansprechpartnerid',
                'au.ansprechpartner',
                'au.abteilung',
                'au.unterabteilung',
                'au.adresszusatz',
                'au.strasse',
                'au.plz',
                'au.ort',
                'au.land',
                'au.bundesstaat',
                'au.telefon',
                'au.telefax',
                'au.email',
                'au.anschreiben',
                //'au.betreff',
                //'au.vertrieb',
                //'au.vertriebid', // Index
                //'au.deckungsbeitragcalc',
                //'au.deckungsbeitrag',
                'au.gesamtsumme',
                'au.erloes_netto',
                'au.umsatz_netto',
                //'au.provision',
                //'au.provision_summe',
                //'au.keinsteuersatz',

                'au.lager_ok',
                'au.porto_ok',
                'au.ust_ok',
                'au.check_ok',
                'au.vorkasse_ok',
                'au.nachnahme_ok',
                'au.reserviert_ok',
                'au.partnerid',
                'au.folgebestaetigung',
                'au.zahlungsmail',
                'au.liefertermin_ok',
                'au.teillieferung_moeglich',
                'au.kreditlimit_ok',
                'au.kreditlimit_freigabe',
                'au.liefersperre_ok',
                'au.teillieferungvon', // Index
                'au.teillieferungnummer',

                'au.autofreigabe',
                'au.freigabe',
                'au.nachbesserung',
                'au.abgeschlossen',
                'au.nachlieferung',
                'au.versandart',
                'au.lieferbedingung',
                'au.autoversand',
                'au.keinporto',
                'au.art',
                'au.fastlane',
                'au.lieferungtrotzsperre',
                'au.keinestornomail',
                'au.keinetrackingmail',
                'au.zahlungsmailcounter',
                'au.zahlungsweise',
                'au.zahlungszieltage',
                'au.zahlungszieltageskonto',
                'au.zahlungszielskonto',
                'au.skontobetrag',
                'au.skontoberechnet',
                //'au.bank_inhaber',
                //'au.bank_institut',
                //'au.bank_blz',
                //'au.bank_konto',
                //'au.kreditkarte_typ',
                //'au.kreditkarte_inhaber',
                //'au.kreditkarte_nummer',
                //'au.kreditkarte_pruefnummer',
                //'au.kreditkarte_monat',
                //'au.kreditkarte_jahr',
                'au.abweichendelieferadresse',
                'au.liefername',
                'au.liefertitel',
                'au.lieferansprechpartner',
                'au.lieferabteilung',
                'au.lieferunterabteilung',
                'au.lieferadresszusatz',
                'au.lieferstrasse',
                'au.lieferort',
                'au.lieferplz',
                'au.lieferland',
                'au.lieferbundesstaat',
                'au.lieferemail',
                'au.lieferid',
                'au.liefergln',

                'au.versendet',
                'au.versendet_am',
                'au.versendet_per',
                'au.versendet_durch',
                //'au.inbearbeitung',
                //'au.logdatei',
                'au.angebotid',
                //'au.rechnungid',
                //'au.anfrageid',
                //'au.gruppe', // Index
                //'au.usereditid', // Index
                //'au.useredittimestamp',
                //'au.realrabatt',
                //'au.rabatt',
                //'au.rabatt1',
                //'au.rabatt2',
                //'au.rabatt3',
                //'au.rabatt4',
                //'au.rabatt5',
                //'au.steuersatz_normal',
                //'au.steuersatz_zwischen',
                //'au.steuersatz_ermaessigt',
                //'au.steuersatz_starkermaessigt',
                //'au.steuersatz_dienstleistung',
                //'au.schreibschutz',
                //'au.pdfarchiviert',
                //'au.pdfarchiviertversion',
                //'au.ohne_briefpapier',
                //'au.projektfiliale',
                //'au.zuarchivieren',
                //'au.angelegtam',
                //'au.partnerausgezahlt',
                //'au.partnerausgezahltam',
                //'au.kennen',
                //'au.rma',
                //'au.transaktionsnummer', // Index
                //'au.vorabbezahltmarkieren',
                //'au.einzugsdatum',
                //'au.auftragseingangper',
                //'au.systemfreitext',
                //'au.saldo',
                //'au.saldogeprueft',
                //'au.rabatteportofestschreiben',
                //'au.deliverythresholdvatid',
                //'au.lieferantennummer',
                //'au.lieferantkdrnummer', // Index
                //'au.webid',
                //'au.cronjobkommissionierung',
                //'au.standardlager',
                //'au.kommissionskonsignationslager',
                //'au.extsoll',
                'au.gln',
                'au.bearbeiterid',
                'au.bearbeiter',
                'au.ohne_artikeltext',
                'au.ustid',
                'au.ust_befreit',
                'au.ust_inner',
                'au.anzeigesteuer',
                'au.waehrung',
                'au.sprache',
                'au.kurs',
                'au.kostenstelle',
                'au.freitext',
                'au.internebemerkung',
                'au.bodyzusatz',
                'au.shop',
                'au.shopextid',
                'au.shopextstatus',
                //'au.stornogrund',
                //'au.stornosonstiges',
                //'au.stornorueckzahlung',
                //'au.stornobetrag',
                //'au.stornobankinhaber',
                //'au.stornobankkonto',
                //'au.stornobankblz',
                //'au.stornobankbank',
                //'au.stornogutschrift',
                //'au.stornogutschriftbeleg',
                //'au.stornowareerhalten',
                //'au.stornomanuellebearbeitung',
                //'au.stornokommentar',
                //'au.stornobezahlt',
                //'au.stornobezahltam',
                //'au.stornobezahltvon',
                //'au.stornoabgeschlossen',
                //'au.stornorueckzahlungper',
                //'au.stornowareerhaltenretour',
                //'au.transaktionsnummer',
                //'au.packstation_inhaber',
                //'au.packstation_station',
                //'au.packstation_ident',
                //'au.packstation_plz',
                //'au.packstation_ort',
            ])->from(self::TABLE_NAME . ' AS au');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('s.id IN (:ids)');
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
