<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

/**
 * Ressource für Angebote
 */
class DocumentOfferResource extends AbstractResource
{
    /** @var string */
    const TABLE_NAME = 'angebot';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'status'                  => 'an.status LIKE',
            'belegnr'                 => 'an.belegnr %LIKE%',
            'belegnr_equals'          => 'an.belegnr LIKE',
            'belegnr_startswith'      => 'an.belegnr LIKE%',
            'belegnr_endswith'        => 'an.belegnr %LIKE',
            'kundennummer'            => 'an.kundennummer %LIKE%',
            'kundennummer_equals'     => 'an.kundennummer LIKE',
            'kundennummer_startswith' => 'an.kundennummer LIKE%',
            'kundennummer_endswith'   => 'an.kundennummer %LIKE',
            'datum'                   => 'an.datum =',
            'datum_gt'                => 'an.datum >',
            'datum_gte'               => 'an.datum >=',
            'datum_lt'                => 'an.datum <',
            'datum_lte'               => 'an.datum <=',
            'projekt'                 => 'an.projekt =',
        ]);

        $this->registerSortingParams([
            'belegnr' => 'an.belegnr',
            'datum'   => 'an.datum',
        ]);

        $this->registerIncludes([
            'positionen' => [
                'key'      => 'positionen',
                'resource' => DocumentOfferPositionResource::class,
                'filter'   => [
                    [
                        'property' => 'angebot',
                        'value'    => ':id',
                    ],
                ],
                'sort'     => [
                    'sort' => 'ASC',
                ],
            ],
            'protokoll'  => [
                'key'      => 'protokoll',
                'resource' => DocumentOfferProtocolResource::class,
                'filter'   => [
                    [
                        'property' => 'angebot',
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
        return $this->selectAllQuery()->where('an.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                'an.id',
                'an.firma',
                'an.projekt', // Index
                'an.status', // Index
                'an.belegnr', // Index
                'an.kundennummer',
                'an.aktion',
                'an.anfrage',
                'an.internebezeichnung',
                'an.datum',
                'an.gueltigbis',
                'an.lieferdatum',
                'an.lieferdatumkw',
                'an.planedorderdate',
                'an.abweichendebezeichnung',
                //'an.auftrag',
                'an.adresse', // Index
                'an.typ',
                'an.name',
                'an.titel',
                'an.ansprechpartnerid',
                'an.ansprechpartner',
                'an.abteilung',
                'an.unterabteilung',
                'an.adresszusatz',
                'an.strasse',
                'an.plz',
                'an.ort',
                'an.land',
                'an.bundesstaat',
                'an.telefon',
                'an.telefax',
                'an.email',
                'an.anschreiben',
                //'an.betreff',
                //'an.vertrieb',
                //'an.vertriebid', // Index
                //'an.deckungsbeitragcalc',
                //'an.deckungsbeitrag',
                'an.gesamtsumme',
                'an.erloes_netto',
                'an.umsatz_netto',
                //'an.provision',
                //'an.provision_summe',
                //'an.keinsteuersatz',
                'an.versandart',
                'an.lieferbedingung',
                'an.autoversand',
                'an.keinporto',
                'an.gesamtsummeausblenden',
                'an.zahlungsweise',
                'an.zahlungszieltage',
                'an.zahlungszieltageskonto',
                'an.zahlungszielskonto',
                'an.skontobetrag',
                'an.skontoberechnet',
                //'an.bank_inhaber',
                //'an.bank_institut',
                //'an.bank_blz',
                //'an.bank_konto',
                //'an.kreditkarte_typ',
                //'an.kreditkarte_inhaber',
                //'an.kreditkarte_nummer',
                //'an.kreditkarte_pruefnummer',
                //'an.kreditkarte_monat',
                //'an.kreditkarte_jahr',
                'an.abweichendelieferadresse',
                'an.liefername',
                'an.liefertitel',
                'an.lieferansprechpartner',
                'an.lieferabteilung',
                'an.lieferunterabteilung',
                'an.lieferadresszusatz',
                'an.lieferstrasse',
                'an.lieferort',
                'an.lieferplz',
                'an.lieferland',
                'an.lieferbundesstaat',
                'an.liefertelefon',
                'an.liefertelefax',
                'an.liefermail',
                'an.lieferid',
                'an.liefergln',
                'an.lieferemail',

                'an.abweichenderechnungsadresse',
                'an.retyp',
                'an.rechnungname',
                'an.retelefon',
                'an.reansprechpartner',
                'an.retelefax',
                'an.reabteilung',
                'an.reemail',
                'an.reunterabteilung',
                'an.readresszusatz',
                'an.restrasse',
                'an.replz',
                'an.reort',
                'an.reland',

                'an.versendet',
                'an.versendet_am',
                'an.versendet_per',
                'an.versendet_durch',
                //'an.inbearbeitung',
                //'an.vermerk',
                //'an.logdatei',
                //'an.auftragid',
                //'an.anfrageid',
                //'an.gruppe', // Index
                //'an.usereditid', // Index
                //'an.useredittimestamp',
                //'an.realrabatt',
                //'an.rabatt',
                //'an.rabatt1',
                //'an.rabatt2',
                //'an.rabatt3',
                //'an.rabatt4',
                //'an.rabatt5',
                //'an.steuersatz_normal',
                //'an.steuersatz_zwischen',
                //'an.steuersatz_ermaessigt',
                //'an.steuersatz_starkermaessigt',
                //'an.steuersatz_dienstleistung',
                //'an.schreibschutz',
                //'an.pdfarchiviert',
                //'an.pdfarchiviertversion',
                //'an.ohne_briefpapier',
                //'an.projektfiliale',
                //'an.zuarchivieren',
                //'an.angelegtam',
                //'an.kopievon',
                //'an.kopienummer',
                'an.gln',
                'an.bearbeiterid',
                'an.bearbeiter',
                'an.ohne_artikeltext',
                'an.ustid',
                'an.ust_befreit',
                'an.anzeigesteuer',
                'an.waehrung',
                'an.sprache',
                'an.kurs',
                'an.kostenstelle',
                'an.freitext',
                'an.internebemerkung',
                'an.bodyzusatz',
                'an.shop',
                'an.shopextid',
                'an.internet',
                //'an.transaktionsnummer',
                //'an.packstation_inhaber',
                //'an.packstation_station',
                //'an.packstation_ident',
                //'an.packstation_plz',
                //'an.packstation_ort',
            ])->from(self::TABLE_NAME . ' AS an');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('an.id IN (:ids)');
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
