<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressource für Gutschriften/Stornorechnungen
 */
class DocumentCreditNoteResource extends AbstractResource
{
    /** @var string */
    const TABLE_NAME = 'gutschrift';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'status'                  => 'gu.status LIKE',
            'belegnr'                 => 'gu.belegnr %LIKE%',
            'belegnr_equals'          => 'gu.belegnr LIKE',
            'belegnr_startswith'      => 'gu.belegnr LIKE%',
            'belegnr_endswith'        => 'gu.belegnr %LIKE',
            'kundennummer'            => 'gu.kundennummer %LIKE%',
            'kundennummer_equals'     => 'gu.kundennummer LIKE',
            'kundennummer_startswith' => 'gu.kundennummer LIKE%',
            'kundennummer_endswith'   => 'gu.kundennummer %LIKE',
            'datum'                   => 'gu.datum =',
            'datum_gt'                => 'gu.datum >',
            'datum_gte'               => 'gu.datum >=',
            'datum_lt'                => 'gu.datum <',
            'datum_lte'               => 'gu.datum <=',
            'rechnung'                => 'gu.rechnung LIKE',
            'rechnungid'              => 'gu.rechnungid =',
            'projekt'                 => 'gu.projekt =',
        ]);

        $this->registerSortingParams([
            'belegnr' => 'gu.belegnr',
            'datum'   => 'gu.datum',
        ]);

        $this->registerIncludes([
            'positionen' => [
                'key'      => 'positionen',
                'resource' => DocumentCreditNotePositionResource::class,
                'filter'   => [
                    [
                        'property' => 'gutschrift',
                        'value'    => ':id',
                    ],
                ],
                'sort'     => [
                    'sort' => 'ASC',
                ],
            ],
            'protokoll'  => [
                'key'      => 'protokoll',
                'resource' => DocumentCreditNoteProtocolResource::class,
                'columns'  => [
                    'guproto.id',
                    'guproto.zeit',
                    'guproto.bearbeiter',
                    'guproto.grund',
                ],
                'filter'   => [
                    [
                        'property' => 'gutschrift',
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
        return $this->selectAllQuery()->where('gu.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                'gu.id',
                'gu.firma',
                'gu.projekt', // Index
                'gu.status', // Index
                'gu.anlegeart',
                'gu.belegnr', // Index
                'gu.datum', // Index
                'gu.rechnung',
                'gu.rechnungid',
                'gu.stornorechnung',
                'gu.kundennummer',
                'gu.bearbeiter',
                'gu.bearbeiterid',
                'gu.freitext',
                'gu.internebemerkung',

                'gu.adresse', // Index
                'gu.typ',
                'gu.name',
                'gu.titel',
                'gu.ansprechpartnerid',
                'gu.ansprechpartner',
                'gu.abteilung',
                'gu.unterabteilung',
                'gu.adresszusatz',
                'gu.strasse',
                'gu.plz',
                'gu.ort',
                'gu.land',
                'gu.bundesstaat',
                'gu.telefon',
                'gu.telefax',
                'gu.email',
                'gu.anschreiben',

                //'gu.betreff',
                //'gu.lieferschein',
                'gu.versandart',
                'gu.lieferdatum',
                'gu.buchhaltung',
                'gu.zahlungsweise',
                'gu.zahlungsstatus',
                'gu.ist',
                'gu.soll',
                'gu.zahlungszieltage',
                'gu.zahlungszieltageskonto',
                'gu.zahlungszielskonto',
                'gu.gesamtsumme',
                //'gu.bank_inhaber',
                //'gu.bank_institut',
                //'gu.bank_blz',
                //'gu.bank_konto',
                //'gu.kreditkarte_typ',
                //'gu.kreditkarte_inhaber',
                //'gu.kreditkarte_nummer',
                //'gu.kreditkarte_pruefnummer',
                //'gu.kreditkarte_monat',
                //'gu.kreditkarte_jahr',
                //'gu.paypalaccount',
                'gu.versendet',
                'gu.versendet_am',
                'gu.versendet_per',
                'gu.versendet_durch',
                //'gu.inbearbeitung',
                //'gu.logdatei',
                'gu.manuell_vorabbezahlt',
                'gu.manuell_vorabbezahlt_hinweis',
                'gu.nicht_umsatzmindernd',
                //'gu.dta_datei',
                //'gu.dta_datei_verband',
                //'gu.deckungsbeitragcalc',
                //'gu.deckungsbeitrag',
                'gu.erloes_netto',
                'gu.umsatz_netto',
                'gu.vertriebid', // Index
                'gu.vertrieb',
                'gu.aktion',
                'gu.provision',
                'gu.provision_summe',
                //'gu.gruppe', // Index
                'gu.ihrebestellnummer',
                //'gu.usereditid', // Index
                //'gu.useredittimestamp',
                //'gu.realrabatt',
                'gu.rabatt',
                //'gu.rabatt1',
                //'gu.rabatt2',
                //'gu.rabatt3',
                //'gu.rabatt4',
                //'gu.rabatt5',
                'gu.steuersatz_normal',
                'gu.steuersatz_zwischen',
                'gu.steuersatz_ermaessigt',
                'gu.steuersatz_starkermaessigt',
                'gu.steuersatz_dienstleistung',
                'gu.ustid',
                'gu.ustbrief',
                'gu.ustbrief_eingang',
                'gu.ustbrief_eingang_am',
                'gu.ust_befreit',
                'gu.waehrung',
                'gu.keinsteuersatz',
                //'gu.schreibschutz',
                //'gu.pdfarchiviert',
                //'gu.pdfarchiviertversion',
                //'gu.ohne_briefpapier',
                //'gu.lieferid',
                //'gu.projektfiliale',
                //'gu.zuarchivieren',
                'gu.internebezeichnung',
                //'gu.angelegtam',
                'gu.sprache',
                'gu.gln',
                //'gu.deliverythresholdvatid',
                'gu.kurs',
                'gu.ohne_artikeltext',
                'gu.anzeigesteuer',
                'gu.kostenstelle',
                'gu.bodyzusatz',
                'gu.lieferbedingung',
                'gu.skontobetrag',
                'gu.skontoberechnet',
                'gu.extsoll',
            ])->from(self::TABLE_NAME . ' AS gu');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('gu.id IN (:ids)');
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
