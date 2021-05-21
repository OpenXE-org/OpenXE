<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\SqlQuery\SelectQuery;

class AddressResource extends AbstractResource
{
    const TABLE_NAME = 'adresse';

    protected function configure()
    {
        $this->setTableName(self::TABLE_NAME);

        $this->registerFilterParams([
            'rolle'                        => 'ar.rolle %LIKE%',
            'projekt'                      => 'a.projekt =',
            'firma'                        => 'a.firma =',
            'typ'                          => 'a.typ LIKE',
            'sprache'                      => 'a.sprache LIKE',
            'waehrung'                     => 'a.waehrung LIKE',
            'land'                         => 'a.land LIKE',
            'name'                         => 'a.name %LIKE%',
            'name_equals'                  => 'a.name LIKE',
            'name_startswith'              => 'a.name LIKE%',
            'name_endswith'                => 'a.name %LIKE',
            'kundennummer'                 => 'a.kundennummer %LIKE%',
            'kundennummer_equals'          => 'a.kundennummer LIKE',
            'kundennummer_startswith'      => 'a.kundennummer LIKE%',
            'kundennummer_endswith'        => 'a.kundennummer %LIKE',
            'lieferantennummer'            => 'a.lieferantennummer %LIKE%',
            'lieferantennummer_equals'     => 'a.lieferantennummer LIKE',
            'lieferantennummer_startswith' => 'a.lieferantennummer LIKE%',
            'lieferantennummer_endswith'   => 'a.lieferantennummer %LIKE',
            'mitarbeiternummer'            => 'a.mitarbeiternummer %LIKE%',
            'mitarbeiternummer_equals'     => 'a.mitarbeiternummer LIKE',
            'mitarbeiternummer_startswith' => 'a.mitarbeiternummer LIKE%',
            'mitarbeiternummer_endswith'   => 'a.mitarbeiternummer %LIKE',
            'email'                        => 'a.email %LIKE%',
            'email_equals'                 => 'a.email LIKE',
            'email_startswith'             => 'a.email LIKE%',
            'email_endswith'               => 'a.email %LIKE',
            'freifeld1'                    => 'a.freifeld1 %LIKE%',
            'freifeld2'                    => 'a.freifeld2 %LIKE%',
            'freifeld3'                    => 'a.freifeld3 %LIKE%',
            'freifeld4'                    => 'a.freifeld4 %LIKE%',
            'freifeld5'                    => 'a.freifeld5 %LIKE%',
            'freifeld6'                    => 'a.freifeld6 %LIKE%',
            'freifeld7'                    => 'a.freifeld7 %LIKE%',
            'freifeld8'                    => 'a.freifeld8 %LIKE%',
            'freifeld9'                    => 'a.freifeld9 %LIKE%',
            'freifeld10'                   => 'a.freifeld10 %LIKE%',
            'freifeld1_equals'             => 'a.freifeld1 LIKE',
            'freifeld2_equals'             => 'a.freifeld2 LIKE',
            'freifeld3_equals'             => 'a.freifeld3 LIKE',
            'freifeld4_equals'             => 'a.freifeld4 LIKE',
            'freifeld5_equals'             => 'a.freifeld5 LIKE',
            'freifeld6_equals'             => 'a.freifeld6 LIKE',
            'freifeld7_equals'             => 'a.freifeld7 LIKE',
            'freifeld8_equals'             => 'a.freifeld8 LIKE',
            'freifeld9_equals'             => 'a.freifeld9 LIKE',
            'freifeld10_equals'            => 'a.freifeld10 LIKE',
        ]);

        $this->registerSortingParams([
            'name'              => 'a.name',
            'kundennummer'      => 'a.kundennummer',
            'lieferantennummer' => 'a.lieferantennummer',
            'mitarbeiternummer' => 'a.mitarbeiternummer',
        ]);

        /*$this->registerValidationRules([
            'id' => 'not_present',
            'bezeichnung' => 'required',
            'type' => 'required',
            'projekt' => 'numeric',
            'netto' => 'boolean',
            'aktiv' => 'boolean',
        ]);*/

        /*$this->registerIncludes([
            'projekt' => [
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
        ]);*/
    }

    /**
     * @return SelectQuery
     */
    protected function selectAllQuery()
    {
        return $this->db->select()
            ->cols([
                'a.id',
                'ar.rolle',
                'a.typ',
                'a.marketingsperre',
                'a.trackingsperre',
                'a.rechnungsadresse',
                'a.sprache',
                'a.name',
                'a.abteilung',
                'a.unterabteilung',
                'a.ansprechpartner',
                'a.land',
                'a.strasse',
                'a.ort',
                'a.plz',
                'a.telefon',
                'a.telefax',
                'a.mobil',
                'a.email',
                'a.ustid',
                'a.ust_befreit',
                'a.passwort_gesendet',
                'a.sonstiges',
                'a.adresszusatz',
                'a.kundenfreigabe',
                'a.steuer',
                'a.logdatei',
                'a.kundennummer',
                'a.lieferantennummer',
                'a.mitarbeiternummer',
                'a.konto',
                'a.blz',
                'a.bank',
                'a.inhaber',
                'a.swift',
                'a.iban',
                'a.waehrung',
                'a.paypal',
                'a.paypalinhaber',
                'a.paypalwaehrung',
                'a.projekt',
                'a.partner',
                'a.zahlungsweise',
                'a.zahlungszieltage',
                'a.zahlungszieltageskonto',
                'a.zahlungszielskonto',
                'a.versandart',
                'a.kundennummerlieferant',
                'a.zahlungsweiselieferant',
                'a.zahlungszieltagelieferant',
                'a.zahlungszieltageskontolieferant',
                'a.zahlungszielskontolieferant',
                'a.versandartlieferant',
                'a.geloescht',
                'a.firma',
                'a.webid',
                'a.vorname',
                'a.kennung',
                'a.sachkonto',
                'a.filiale',
                'a.vertrieb',
                'a.innendienst',
                'a.verbandsnummer',
                'a.abweichendeemailab',
                'a.portofrei_aktiv',
                'a.portofreiab',
                'a.infoauftragserfassung',
                'a.mandatsreferenz',
                'a.mandatsreferenzdatum',
                'a.mandatsreferenzaenderung',
                'a.glaeubigeridentnr',
                'a.kreditlimit',
                'a.tour',
                'a.zahlungskonditionen_festschreiben',
                'a.rabatte_festschreiben',
                'a.mlmaktiv',
                'a.mlmvertragsbeginn',
                'a.mlmlizenzgebuehrbis',
                'a.mlmfestsetzenbis',
                'a.mlmfestsetzen',
                'a.mlmmindestpunkte',
                'a.mlmwartekonto',
                'a.abweichende_rechnungsadresse',
                'a.rechnung_vorname',
                'a.rechnung_name',
                'a.rechnung_titel',
                'a.rechnung_typ',
                'a.rechnung_strasse',
                'a.rechnung_ort',
                'a.rechnung_plz',
                'a.rechnung_ansprechpartner',
                'a.rechnung_land',
                'a.rechnung_abteilung',
                'a.rechnung_unterabteilung',
                'a.rechnung_adresszusatz',
                'a.rechnung_telefon',
                'a.rechnung_telefax',
                'a.rechnung_anschreiben',
                'a.rechnung_email',
                'a.geburtstag',
                'a.rolledatum',
                'a.liefersperre',
                'a.liefersperregrund',
                'a.mlmpositionierung',
                'a.steuernummer',
                'a.steuerbefreit',
                'a.mlmmitmwst',
                'a.mlmabrechnung',
                'a.mlmwaehrungauszahlung',
                'a.mlmauszahlungprojekt',
                'a.sponsor',
                'a.geworbenvon',
                'a.logfile',
                'a.kalender_aufgaben',
                'a.verrechnungskontoreisekosten',
                'a.usereditid',
                'a.useredittimestamp',
                'a.rabatt',
                'a.provision',
                'a.rabattinformation',
                'a.rabatt1',
                'a.rabatt2',
                'a.rabatt3',
                'a.rabatt4',
                'a.rabatt5',
                'a.internetseite',
                'a.bonus1',
                'a.bonus1_ab',
                'a.bonus2',
                'a.bonus2_ab',
                'a.bonus3',
                'a.bonus3_ab',
                'a.bonus4',
                'a.bonus4_ab',
                'a.bonus5',
                'a.bonus5_ab',
                'a.bonus6',
                'a.bonus6_ab',
                'a.bonus7',
                'a.bonus7_ab',
                'a.bonus8',
                'a.bonus8_ab',
                'a.bonus9',
                'a.bonus9_ab',
                'a.bonus10',
                'a.bonus10_ab',
                'a.rechnung_periode',
                'a.rechnung_anzahlpapier',
                'a.rechnung_permail',
                'a.titel',
                'a.anschreiben',
                'a.nachname',
                'a.arbeitszeitprowoche',
                'a.folgebestaetigungsperre',
                'a.lieferantennummerbeikunde',
                'a.verein_mitglied_seit',
                'a.verein_mitglied_bis',
                'a.verein_mitglied_aktiv',
                'a.verein_spendenbescheinigung',
                'a.freifeld1',
                'a.freifeld2',
                'a.freifeld3',
                'a.freifeld4',
                'a.freifeld5',
                'a.freifeld6',
                'a.freifeld7',
                'a.freifeld8',
                'a.freifeld9',
                'a.freifeld10',
                'a.rechnung_papier',
                'a.angebot_cc',
                'a.auftrag_cc',
                'a.rechnung_cc',
                'a.gutschrift_cc',
                'a.lieferschein_cc',
                'a.bestellung_cc',
                'a.angebot_fax_cc',
                'a.auftrag_fax_cc',
                'a.rechnung_fax_cc',
                'a.gutschrift_fax_cc',
                'a.lieferschein_fax_cc',
                'a.bestellung_fax_cc',
                'a.abperfax',
                'a.abpermail',
                'a.kassiereraktiv',
                'a.kassierernummer',
                'a.kassiererprojekt',
                'a.portofreilieferant_aktiv',
                'a.portofreiablieferant',
                'a.mandatsreferenzart',
                'a.mandatsreferenzwdhart',
                'a.serienbrief',
                'a.kundennummer_buchhaltung',
                'a.lieferantennummer_buchhaltung',
                'a.lead',
                'a.zahlungsweiseabo',
                'a.bundesland',
                'a.mandatsreferenzhinweis',
                'a.geburtstagkalender',
                'a.geburtstagskarte',
                'a.liefersperredatum',
                'a.umsatzsteuer_lieferant',
                'a.lat',
                'a.lng',
                'a.art',
                'a.angebot_email',
                'a.auftrag_email',
                'a.rechnungs_email',
                'a.gutschrift_email',
                'a.lieferschein_email',
                'a.bestellung_email',
                'a.firmensepa',
                'a.anzeigesteuerbelege',
                'a.gln',
                'a.rechnung_gln',
                'a.keinealtersabfrage',
                'a.lieferbedingung',
                'a.mlmintranetgesamtestruktur',
                'a.kommissionskonsignationslager',
                'a.zollinformationen',
                'a.bundesstaat',
                'a.rechnung_bundesstaat',
                'a.lieferschwellenichtanwenden',
            ])
            ->from(self::TABLE_NAME . ' AS a')
            ->joinSubSelect(
                'LEFT',
                'SELECT ar.adresse, GROUP_CONCAT(LOWER(ar.subjekt)) AS rolle ' .
                'FROM adresse_rolle AS ar ' .
                'WHERE (ar.bis = \'0000-00-00\' OR ar.bis >= CURDATE())' .
                'AND (ar.von = \'0000-00-00\' OR  ar.von <= CURDATE())' .
                'AND (ar.subjekt = \'Kunde\' OR ar.subjekt = \'Lieferant\') ' .
                'GROUP BY ar.adresse ',
                'ar',
                'a.id = ar.adresse'
            )
            ->where('a.geloescht <> 1');
    }

    /**
     * @return SelectQuery
     */
    protected function selectOneQuery()
    {
        return $this->selectAllQuery()->where('a.id = :id');
    }

    /**
     * @return SelectQuery
     */
    protected function selectIdsQuery()
    {
        return $this->selectAllQuery()->where('a.id IN (:ids)');
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
