<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*
* Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
*
* This file is licensed under the Embedded Projects General Public License *Version 3.1.
*
* You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis
* to obtain the text of the corresponding license version.
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/

/*
*   Copyright (c) 2023 OpenXE project
*/

?>
<?php

class ConsistencyException extends Exception {

    /*
        contains the result data as array(string 'belegnr', float 'betrag_gesamt', float 'betrag_summe'))
    */

    private $_data = array();

    public function __construct($message, $data)
    {
        $this->_data = $data;
        parent::__construct($message);
    }

    public function getData()
    {
        return $this->_data;
    }
}

class Exportbuchhaltung
{
    /** @var Application $app */
    var $app;
    var $belegnummer;
    var $headerwritten = false;

    function typen($rechnung, $gutschrift, $verbindlichkeit, $lieferantengutschrift) : array {
        return(
            array(
                array(
                    'typ' => 'rechnung',
                    'subtable' => 'rechnung_position',
                    'kennzeichen' => 'S',
                    'kennzeichen_negativ' => 'H',
                    'field_belegnr' => 'b.belegnr',
                    'field_name' => 'b.name',
                    'field_date' => 'datum',
                    'field_auftrag' => 'MAKE_SET(3,b.auftrag,(SELECT auftrag.internet FROM auftrag WHERE auftrag.id = auftragid))',
                    'field_zahlweise' => 'CONCAT(UCASE(LEFT(b.zahlungsweise, 1)),SUBSTRING(b.zahlungsweise, 2))',
                    'field_kontonummer' => 'a.kundennummer_buchhaltung',
                    'field_kundennummer' => 'b.kundennummer',
                    'field_betrag_gesamt' => 'b.soll',
                    'field_betrag' => 'p.umsatz_brutto_gesamt',
                    'condition_where' => ' AND b.status IN (\'freigegeben\',\'versendet\',\'storniert\')',
                    'Buchungstyp' => 'SR',
                    'do' => $rechnung,
                    'pdf' => 'print'
                ),
                array(
                    'typ' => 'gutschrift',
                    'subtable' => 'gutschrift_position',
                    'kennzeichen' => 'H',
                    'kennzeichen_negativ' => 'S',
                    'field_belegnr' => 'b.belegnr',
                    'field_name' => 'b.name',
                    'field_date' => 'datum',
                    'field_auftrag' => '\'\'',
                    'field_zahlweise' => '\'\'',
                    'field_kontonummer' => 'a.kundennummer_buchhaltung',
                    'field_kundennummer' => 'b.kundennummer',
                    'field_betrag_gesamt' => 'b.soll',
                    'field_betrag' => 'p.umsatz_brutto_gesamt',
                    'condition_where' => ' AND b.status IN (\'freigegeben\',\'versendet\')',
                    'Buchungstyp' => '',
                    'do' => $gutschrift,
                    'pdf' => 'print'
                ),
                array(
                    'typ' => 'verbindlichkeit',
                    'subtable' => 'verbindlichkeit_position',
                    'kennzeichen' => 'H',
                    'kennzeichen_negativ' => 'S',
                    'field_belegnr' => 'b.rechnung',
                    'field_name' => 'a.name',
                    'field_date' => 'rechnungsdatum',
                    'field_auftrag' => 'b.auftrag',
                    'field_zahlweise' => '\'\'',
                    'field_kontonummer' => 'a.lieferantennummer_buchhaltung',
                    'field_kundennummer' => 'a.lieferantennummer',
                    'field_betrag_gesamt' => 'b.betrag',
                    'field_betrag' => 'p.preis*p.menge*((100+p.steuersatz)/100)',
                    'field_gegenkonto' => '(SELECT sachkonto FROM kontorahmen k WHERE k.id = p.kontorahmen)',
                    'condition_where' => ' AND b.status IN (\'freigegeben\', \'abgeschlossen\')',
                    'Buchungstyp' => '',
                    'do' => $verbindlichkeit,
                    'pdf' => 'load'
                ),
                array(
                    'typ' => 'lieferantengutschrift',
                    'subtable' => 'lieferantengutschrift_position',
                    'kennzeichen' => 'S',
                    'kennzeichen_negativ' => 'H',
                    'field_belegnr' => 'b.rechnung',
                    'field_name' => 'a.name',
                    'field_date' => 'rechnungsdatum',
                    'field_auftrag' => '\'\'',
                    'field_zahlweise' => '\'\'',
                    'field_kontonummer' => 'a.lieferantennummer_buchhaltung',
                    'field_kundennummer' => 'a.lieferantennummer',
                    'field_betrag_gesamt' => 'b.betrag',
                    'field_betrag' => 'p.preis*p.menge*((100+p.steuersatz)/100)',
                    'field_gegenkonto' => '(SELECT sachkonto FROM kontorahmen k WHERE k.id = p.kontorahmen)',
                    'condition_where' => ' AND b.status IN (\'freigegeben\', \'abgeschlossen\')',
                    'Buchungstyp' => '',
                    'do' => $lieferantengutschrift,
                    'pdf' => 'load'
                )
            )
        );
    }

    /**
    * Exportbelegepositionen constructor.
    *
    * @param Application $app
    * @param bool        $intern
    */
    public function __construct($app, $intern = false)
    {
        $this->app = $app;
        if ($intern == true) {
            return;
        }

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("export", "ExportBuchhaltungList");
        $this->app->ActionHandlerListen($app);
        $this->app->erp->Headlines('Buchhaltung Export DATEV');
    }

    function ExportBuchhaltungList() {
        $submit = $this->app->Secure->GetPOST('submit');
        $von_form = $this->app->Secure->GetPOST("von");
        $bis_form = $this->app->Secure->GetPOST("bis");
        $von = date_create($this->app->erp->ReplaceDatum(true, $von_form, true));
        $bis = date_create($this->app->erp->ReplaceDatum(true, $bis_form, true));
        $projektkuerzel = $this->app->Secure->GetPOST("projekt");
        $projekt = $this->app->erp->ReplaceProjekt(true, $projektkuerzel, true);

        $rgchecked = $this->app->Secure->GetPOST("rechnung");
        $gschecked = $this->app->Secure->GetPOST("gutschrift");
        $vbchecked = $this->app->Secure->GetPOST("verbindlichkeit");
        $lgchecked = $this->app->Secure->GetPOST("lieferantengutschrift");
        $diffignore = $this->app->Secure->GetPOST("diffignore");
		$sachkonto = $this->app->Secure->GetPOST('sachkonto');
        $format = $this->app->Secure->GetPOST('format');
        $pdfexport = $this->app->Secure->GetPOST("pdfexport");
			
		$account_id = null;
		if (!empty($sachkonto)) {
		    $sachkonto_kennung = explode(' ',$sachkonto)[0];
            $account_id = $this->app->DB->SelectArr("SELECT id from kontorahmen WHERE sachkonto = '".$sachkonto_kennung."'")[0]['id'];
		}

        $msg = "";

        // Preload values
        if (empty($submit)) {
            $von = date_create('now')->modify('first day of last month');
            $von_form = $this->app->erp->ReplaceDatum(false,$von->format('Y-m-d'),false);
            $bis = date_create('now')->modify('last day of last month');
            $bis_form = $this->app->erp->ReplaceDatum(false,$bis->format('Y-m-d'),false);
            $rgchecked = true;
            $gschecked = true;
            $vbchecked = true;
            $lgchecked = true;
        }

        $missing_obligatory = array();

        $buchhaltung_berater = $this->app->erp->Firmendaten('buchhaltung_berater');
        $buchhaltung_mandant = $this->app->erp->Firmendaten('buchhaltung_mandant');
        $buchhaltung_wj_beginn = $this->app->erp->Firmendaten('buchhaltung_wj_beginn');
        $buchhaltung_sachkontenlaenge = $this->app->erp->Firmendaten('buchhaltung_sachkontenlaenge');

        $buchhaltung_berater = $this->app->erp->Firmendaten('buchhaltung_berater');
        if (empty($buchhaltung_berater)) {
            $missing_obligatory[] = "Berater";
        }
        $buchhaltung_mandant = $this->app->erp->Firmendaten('buchhaltung_mandant');
        if (empty($buchhaltung_mandant)) {
            $missing_obligatory[] = "Mandant";
        }
        $buchhaltung_wj_beginn = $this->app->erp->Firmendaten('buchhaltung_wj_beginn');
        if (empty($buchhaltung_wj_beginn)) {
            $missing_obligatory[] = "Wirtschaftsjahr";
        }
        $buchhaltung_sachkontenlaenge = $this->app->erp->Firmendaten('buchhaltung_sachkontenlaenge');
        if (empty($buchhaltung_sachkontenlaenge)) {
            $missing_obligatory[] = "Sachkontenl&auml;nge";
        }

        if (!empty($missing_obligatory)) {
            $msg = "<div class=warning>Angaben in den Grundeinstellungen fehlen: ".implode(", ",$missing_obligatory).".</div>";
        }

        //---------- DOWNLOAD HERE
        if ($submit == 'Download') {
            $dataok = true;

            if (
              !$rgchecked &&
              !$gschecked &&
              !$vbchecked &&
              !$lgchecked
            ) {
                $msg = "<div class=error>Bitte mindestens eine Belegart auswählen.</div>";
                $dataok = false;
            }

            $von_next_year = clone $von;
            $von_next_year = $von_next_year->modify("+1 year");;

            $buchhaltung_wj_beginn = date_create(date_format($von,'Y').$buchhaltung_wj_beginn);
            if ($buchhaltung_wj_beginn > $von) {
                $buchhaltung_wj_beginn = $buchhaltung_wj_beginn->modify("-1 year");
            }

            $buchhaltung_wj_beginn_next_year = clone $buchhaltung_wj_beginn;
            $buchhaltung_wj_beginn_next_year->modify("+1 year");

            if ($bis < $von || $bis > $von_next_year || $bis >= $buchhaltung_wj_beginn_next_year) {
                $msg = "<div class=error>Ung&uuml;ltiger Datumsbereich.</div>";
                $dataok = false;
            }

            if ($dataok) {
                $filename_csv = "EXTF_".date('Ymd') . "_Buchungsstapel_DATEV_export.csv";
                try {
                    $csv = $this->DATEV_Buchuchungsstapel($rgchecked, $gschecked, $vbchecked, $lgchecked, $buchhaltung_berater, $buchhaltung_mandant, $buchhaltung_wj_beginn, $buchhaltung_sachkontenlaenge, $von, $bis, $projekt, $filename_csv, $diffignore, $sachkonto_kennung, $format);
                    if ($pdfexport) {

                        $dateinamezip = 'Export_Buchhaltung_'.date('Y-m-d').'.zip';

                        $zip = new ZipArchive;
                        $zip->open($dateinamezip, ZipArchive::CREATE);

                        $zip->addFromString($typ['typ']."/".$filename_csv, $csv);

                        $typen = $this->typen($rgchecked, $gschecked, $vbchecked, $lgchecked);

                        foreach ($typen as $typ) {
                            $sql = "
                                SELECT id, ".$typ['field_belegnr']." belegnr FROM ".$typ['typ']." b
                                WHERE
                                b.".$typ['field_date']." BETWEEN '".date_format($von,"Y-m-d")."' AND '".date_format($bis,"Y-m-d")."' AND (b.projekt=$projekt OR $projekt=0)".$typ['condition_where'];
                            $belege = $this->app->DB->SelectArr($sql);
                            foreach ($belege as $beleg) {
                            
                                if (!$typ['do']) {
                                    continue;
                                }
                            
                                switch ($typ['pdf']) {
                                    case 'print':
                                        switch ($typ['typ']) {
                                            case 'rechnung':
                                                if(class_exists('GutschriftPDFCustom')) {
                                                    $Brief = new RechnungPDFCustom($this->app,$projekt);
                                                }
                                                else{
                                                    $Brief = new RechnungPDF($this->app,$projekt);
                                                }
                                                $Brief->GetRechnung($beleg['id']);
                                            break;
                                            case 'gutschrift':
                                                if(class_exists('RechnungPDFCustom')) {
                                                    $Brief = new GutschriftPDFCustom($this->app,$projekt);
                                                }
                                                else{
                                                    $Brief = new GutschriftPDF($this->app,$projekt);
                                                }
                                                $Brief->GetGutschrift($beleg['id']);
                                            break;
                                            default:
                                                exit();
                                            break;
                                        }
                                        $tmpfile = $Brief->displayTMP();
                                        $file_name = $beleg['belegnr'].".pdf";
                                        $zip->addFromString($typ['typ']."/".$file_name, file_get_contents($tmpfile));
                                    break;
                                    case 'load':
                                        $file_attachments = $this->app->erp->GetDateiSubjektObjekt('%',$typ['typ'],$beleg['id']);
                                        $suffix = "";
                                        $count = 0;
                                        foreach ($file_attachments as $file_attachment) {
                                            if ($this->app->erp->GetDateiEndung($file_attachment) == 'pdf') {
                                                $file_contents = $this->app->erp->GetDatei($file_attachment);
                                                $file_name = filter_var($beleg['belegnr'],FILTER_SANITIZE_EMAIL).$suffix.".pdf";                                     
                                                $zip->addFromString($typ['typ']."/".$file_name, $file_contents);
                                                $count++;
                                                $suffix = "_".$count;
                                            }
                                        }
                                    break;
                                }
                            }
                        }
                        $zip->close();

                        // download
                        header('Content-Type: application/zip');
                        header("Content-Disposition: attachment; filename=$dateinamezip");
                        header('Content-Length: ' . filesize($dateinamezip));

                        readfile($dateinamezip);
                        unlink($dateinamezip);
                    } else {
                        header("Content-Disposition: attachment; filename=" . $filename_csv);
                        header("Pragma: no-cache");
                        header("Expires: 0");
                        echo($csv);
                    }
                    $this->app->ExitXentral();
                }
                catch (ConsistencyException $e) {
                    $msg = "<div class=error>Inkonsistente Daten (".$e->getMessage()."): <br>";

                    $data = $e->getData();

                    $count = 0;
                    foreach($data as $item) {
                        $msg .= $item['typ']." ".$item['belegnr']." (Kopf ".$this->app->erp->ReplaceMengeBetrag(false,$item['betrag_gesamt'],false)." Positionen ".$this->app->erp->ReplaceMengeBetrag(false,$item['betrag_summe'],false).")<br>";
                        $count++;
                        if ($count == 10) {
                            $msg .= "...";
                            break;
                        }
                    }
                    $msg .= "</div>";
                }
            }
        }
        //---------- DOWNLOAD HERE

        $this->app->erp->MenuEintrag("index.php?module=exportbuchhaltung&action=export", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=importvorlage&action=uebersicht", "Zur&uuml;ck");
        $this->app->YUI->AutoComplete("projekt", "projektname", 1);
        $this->app->YUI->DatePicker("von");
        $this->app->YUI->DatePicker("bis");
        $this->app->YUI->AutoComplete('sachkonto', 'sachkonto');

        $this->app->Tpl->SET('MESSAGE', $msg);

        $this->app->Tpl->SET('RGCHECKED',$rgchecked?'checked':'');
        $this->app->Tpl->SET('GSCHECKED',$gschecked?'checked':'');
        $this->app->Tpl->SET('VBCHECKED',$vbchecked?'checked':'');
        $this->app->Tpl->SET('LGCHECKED',$lgchecked?'checked':'');
        $this->app->Tpl->SET('DIFFIGNORE',$diffignore?'checked':'');
        $this->app->Tpl->SET('PDFEXPORT',$pdfexport?'checked':'');

        $this->app->Tpl->SET('VON', $von_form);
        $this->app->Tpl->SET('BIS', $bis_form);
        $this->app->Tpl->SET('PROJEKT', $projektkuerzel);
        $this->app->Tpl->SET('SACHKONTO', $sachkonto);

        $this->app->Tpl->Parse('PAGE', "exportbuchhaltung_export.tpl");
    }

    /*
    * Create DATEV Buchhungsstapel
    * format: "ISO-8859-1", "UTF-8", "UTF-8-BOM"
    * @throws ConsistencyException with string (list of items) if consistency check fails and no sachkonto for differences is given
    */
    function DATEV_Buchuchungsstapel(bool $rechnung, bool $gutschrift, bool $verbindlichkeit, bool $lieferantengutschrift, string $berater, string $mandant, datetime $wj_beginn, int $sachkontenlaenge, datetime $von, datetime $bis, int $projekt = 0, string $filename = 'EXTF_Buchungsstapel_DATEV_export.csv', $diffignore = false, $sachkonto_differences, string $format = "ISO-8859-1") : string {

        $datev_header_definition = array (
            '1' => 'Kennzeichen',
            '2' => 'Versionsnummer',
            '3' => 'Formatkategorie',
            '4' => 'Formatname',
            '5' => 'Formatversion',
            '6' => 'Erzeugt am',
            '7' => 'Reserviert',
            '8' => 'Reserviert',
            '9' => 'Reserviert',
            '10' => 'Reserviert',
            '11' => 'Beraternummer',
            '12' => 'Mandantennummer',
            '13' => 'WJ-Beginn',
            '14' => 'Sachkontenlänge',
            '15' => 'Datum von',
            '16' => 'Datum bis',
            '17' => 'Bezeichnung',
            '18' => 'Diktatkürzel',
            '19' => 'Buchungstyp',
            '20' => 'Rechnungs- legungszweck',
            '21' => 'Festschreibung',
            '22' => 'WKZ',
            '23' => 'Reserviert',
            '24' => 'Derivatskennzeichen',
            '25' => 'Reserviert',
            '26' => 'Reserviert',
            '27' => 'Sachkonten- rahmen',
            '28' => 'ID der Branchen- lösung',
            '29' => 'Reserviert',
            '30' => 'Reserviert',
            '31' => 'Anwendungs- information'
        );

        $datev_buchungsstapel_definition = array (
            '1' => 'Umsatz',
            '2' => 'Soll-/Haben-Kennzeichen',
            '3' => 'WKZ Umsatz',
            '4' => 'Kurs',
            '5' => 'Basisumsatz',
            '6' => 'WKZ Basisumsatz',
            '7' => 'Konto',
            '8' => 'Gegenkonto (ohne BU-Schlüssel)',
            '9' => 'BU-Schlüssel',
            '10' => 'Belegdatum',
            '11' => 'Belegfeld 1',
            '12' => 'Belegfeld 2',
            '13' => 'Skonto',
            '14' => 'Buchungstext',
            '15' => 'Postensperre',
            '16' => 'Diverse Adressnummer',
            '17' => 'Geschäftspartnerbank',
            '18' => 'Sachverhalt',
            '19' => 'Zinssperre',
            '20' => 'Beleglink',
            '21' => 'Beleginfo -Art 1',
            '22' => 'Beleginfo -Inhalt 1',
            '23' => 'Beleginfo -Art 2',
            '24' => 'Beleginfo -Inhalt 2',
            '25' => 'Beleginfo -Art 3',
            '26' => 'Beleginfo -Inhalt 3',
            '27' => 'Beleginfo -Art 4',
            '28' => 'Beleginfo -Inhalt 4',
            '29' => 'Beleginfo -Art 5',
            '30' => 'Beleginfo -Inhalt 5',
            '31' => 'Beleginfo -Art 6',
            '32' => 'Beleginfo -Inhalt 6',
            '33' => 'Beleginfo -Art 7',
            '34' => 'Beleginfo -Inhalt 7',
            '35' => 'Beleginfo -Art 8',
            '36' => 'Beleginfo -Inhalt 8',
            '37' => 'KOST1 -Kostenstelle',
            '38' => 'KOST2 -Kostenstelle',
            '39' => 'KOST-Menge',
            '40' => 'EU-Mitgliedstaat u. UStID (Bestimmung)',
            '41' => 'EU-Steuersatz (Bestimmung)',
            '42' => 'Abw. Versteuerungsart',
            '43' => 'Sachverhalt L+L',
            '44' => 'Funktionsergänzung L+L',
            '45' => 'BU 49 Hauptfunktiontyp',
            '46' => 'BU 49 Hauptfunktionsnummer',
            '47' => 'BU 49 Funktionsergänzung',
            '48' => 'Zusatzinformation – Art 1',
            '49' => 'Zusatzinformation – Inhalt 1',
            '50' => 'Zusatzinformation – Art 2',
            '51' => 'Zusatzinformation – Inhalt 2',
            '52' => 'Zusatzinformation – Art 3',
            '53' => 'Zusatzinformation – Inhalt 3',
            '54' => 'Zusatzinformation – Art 4',
            '55' => 'Zusatzinformation – Inhalt 4',
            '56' => 'Zusatzinformation – Art 5',
            '57' => 'Zusatzinformation – Inhalt 5',
            '58' => 'Zusatzinformation – Art 6',
            '59' => 'Zusatzinformation – Inhalt 6',
            '60' => 'Zusatzinformation – Art 7',
            '61' => 'Zusatzinformation – Inhalt 7',
            '62' => 'Zusatzinformation – Art 8',
            '63' => 'Zusatzinformation – Inhalt 8',
            '64' => 'Zusatzinformation – Art 9',
            '65' => 'Zusatzinformation – Inhalt 9',
            '66' => 'Zusatzinformation – Art 10',
            '67' => 'Zusatzinformation – Inhalt 10',
            '68' => 'Zusatzinformation – Art 11',
            '69' => 'Zusatzinformation – Inhalt 11',
            '70' => 'Zusatzinformation – Art 12',
            '71' => 'Zusatzinformation – Inhalt 12',
            '72' => 'Zusatzinformation – Art 13',
            '73' => 'Zusatzinformation – Inhalt 13',
            '74' => 'Zusatzinformation – Art 14',
            '75' => 'Zusatzinformation – Inhalt 14',
            '76' => 'Zusatzinformation – Art 15',
            '77' => 'Zusatzinformation – Inhalt 15',
            '78' => 'Zusatzinformation – Art 16',
            '79' => 'Zusatzinformation – Inhalt 16',
            '80' => 'Zusatzinformation – Art 17',
            '81' => 'Zusatzinformation – Inhalt 17',
            '82' => 'Zusatzinformation – Art 18',
            '83' => 'Zusatzinformation – Inhalt 18',
            '84' => 'Zusatzinformation – Art 19',
            '85' => 'Zusatzinformation – Inhalt 19',
            '86' => 'Zusatzinformation – Art 20',
            '87' => 'Zusatzinformation – Inhalt 20',
            '88' => 'Stück',
            '89' => 'Gewicht',
            '90' => 'Zahlweise',
            '91' => 'Forderungsart',
            '92' => 'Veranlagungsjahr',
            '93' => 'Zugeordnete Fälligkeit',
            '94' => 'Skontotyp',
            '95' => 'Auftragsnummer',
            '96' => 'Buchungstyp',
            '97' => 'USt-Schlüssel (Anzahlungen)',
            '98' => 'EU-Mitgliedstaat (Anzahlungen)',
            '99' => 'Sachverhalt L+L (Anzahlungen)',
            '100' => 'EU-Steuersatz (Anzahlungen)',
            '101' => 'Erlöskonto (Anzahlungen)',
            '102' => 'Herkunft-Kz',
            '103' => 'Leerfeld',
            '104' => 'KOST-Datum',
            '105' => 'SEPA-Mandatsreferenz',
            '106' => 'Skontosperre',
            '107' => 'Gesellschaftername',
            '108' => 'Beteiligtennummer',
            '109' => 'Identifikationsnummer',
            '110' => 'Zeichnernummer',
            '111' => 'Postensperre bis',
            '112' => 'Bezeichnung SoBil-Sachverhalt',
            '113' => 'Kennzeichen SoBil-Buchung',
            '114' => 'Festschreibung',
            '115' => 'Leistungsdatum',
            '116' => 'Datum Zuord. Steuerperiode',
            '117' => 'Fälligkeit',
            '118' => 'Generalumkehr',
            '119' => 'Steuersatz',
            '120' => 'Land',
            '121' => 'Abrechnungsreferenz',
            '122' => 'BVV-Position (Betriebsvermögensvergleich)',
            '123' => 'EU-Mitgliedstaat u. UStID (Ursprung)',
            '124' => 'EU-Steuersatz (Ursprung)');

        $usernamearr = explode(' ',strtoupper($this->app->User->GetName()." X X"));

        if (count($usernamearr) < 2) {
            $kuerzel = $usernamearr[0][0].$usernamearr[0][1];
        }
        else {
            $kuerzel = $usernamearr[0][0].$usernamearr[1][0];
        }

        $data['Kennzeichen'] = 'EXTF';
        $data['Versionsnummer'] = '700';
        $data['Formatkategorie'] = '21';
        $data['Formatname'] = 'Buchungsstapel';
        $data['Formatversion'] = '12';
        $data['Erzeugt am'] = date('YmdHis').'000';
        $data['Reserviert'] = '';
        $data['Reserviert'] = '';
        $data['Reserviert'] = '';
        $data['Reserviert'] = '';
        $data['Beraternummer'] = $berater;
        $data['Mandantennummer'] = $mandant;
        $data['WJ-Beginn'] = date_format($wj_beginn,"Ymd");
        $data['Sachkontenlänge'] = $sachkontenlaenge;
        $data['Datum von'] = date_format($von,"Ymd");
        $data['Datum bis'] = date_format($bis,"Ymd");
        $data['Bezeichnung'] = mb_strimwidth($filename,0,30);
        $data['Diktatkürzel'] = $kuerzel;
        $data['Buchungstyp'] = 1;
        $data['Rechnungs- legungszweck'] = 0;
        $data['Festschreibung'] = 1;
        $data['WKZ'] = 'EUR';
        $data['Reserviert'] = '';
        $data['Derivatskennzeichen'] = '';
        $data['Reserviert'] = '';
        $data['Reserviert'] = '';
        $data['Sachkonten- rahmen'] = '';
        $data['ID der Branchen- lösung'] = '';
        $data['Reserviert'] = '';
        $data['Reserviert'] = '';
        $data['Anwendungs- information'] = '';

        // Start
        $csv = "";

        // Output data header row
        $comma = "";
        foreach ($datev_header_definition as $key => $value) {
            if (!isset($data[$value])) {
                $data[$value] = '';
            }
            $csv .= $comma.'"'.$data[$value].'"';
            $comma = ";";
        }
        $csv .= "\r\n";

        // Output column captions
        $comma = "";
        foreach ($datev_buchungsstapel_definition as $key => $value) {
            $csv .= $comma.'"'.$value.'"';
            $comma = ";";
        }
        $csv .= "\r\n";

        // Collate data and transform in RAM
        $typen = $this->typen($rechnung, $gutschrift, $verbindlichkeit, $lieferantengutschrift);
        foreach ($typen as $typ) {

            if (!$typ['do']) {
                continue;
            }

            if (!empty($typ['field_gegenkonto'])) {
                $sql_gegenkonto = $typ['field_gegenkonto'];
            } else
            {
                $sql_gegenkonto = "NULL";
            }

            $sql = "SELECT
                ".$typ['typ']." id,
                ".$typ['field_belegnr']." as belegnr,
                ".$typ['field_auftrag']." as auftrag,
                ".$typ['field_zahlweise']." as zahlweise,
                if(".$typ['field_kontonummer']." <> '',".$typ['field_kontonummer'].",".$typ['field_kundennummer'].") as kundennummer,
                ".$typ['field_name']." as name,
                a.ustid,
                b.".$typ['field_date']." as datum,
                p.id as pos_id,
                ".$typ['field_betrag_gesamt']." as betrag_gesamt,
                b.waehrung,
                ROUND(".$typ['field_betrag'].",2) as betrag,
                ".$sql_gegenkonto." as gegenkonto,
                b.waehrung as pos_waehrung
            FROM
                ".$typ['typ']." b
                    LEFT JOIN
                ".$typ['subtable']." p
                    ON
                b.id = p.".$typ['typ']."
                    INNER JOIN
                adresse a ON a.id = b.adresse
                    WHERE
                b.".$typ['field_date']." BETWEEN '".date_format($von,"Y-m-d")."' AND '".date_format($bis,"Y-m-d")."' AND (b.projekt=$projekt OR $projekt=0)".$typ['condition_where'];

            // Check consistency of positions
            if (!$diffignore) {
                $sql_check = "SELECT *
                FROM
                    (
                    SELECT
                        id,
                        belegnr,
                        datum,
                        betrag_gesamt,
                        ROUND(SUM(betrag),2) AS betrag_summe,
                        waehrung,
                        kundennummer,
                        ustid,
                        auftrag
                    FROM
                (".$sql.") posten
                GROUP BY
                    id
                ) summen
                WHERE betrag_gesamt <> betrag_summe OR betrag_summe IS NULL";

                $result = $this->app->DB->SelectArr($sql_check);
                if (!empty($result)) {

                    if (!$sachkonto_differences) {
                        $e = new ConsistencyException(ucfirst($typ['typ']),$result);
                        throw $e;
        		    } else {
                        // Create differences entries
                        foreach ($result as $row) {

                            $posid = $row['pos_id'];
                            $tmpsteuersatz = 0;
                            $tmpsteuertext = '';
                            $erloes = '';
                            $result = array();
                            $this->app->erp->GetSteuerPosition($typ['typ'], $posid, $tmpsteuersatz, $tmpsteuertext, $erloes);

                            $data = array();

                            $difference = $row['betrag_gesamt']-$row['betrag_summe'];

                            $data['Umsatz'] = number_format(abs($difference), 2, ',', ''); // obligatory
                            $data['EU-Steuersatz (Bestimmung)'] = 0;
                            $data['WKZ Umsatz'] = $row['waehrung'];
                            $data['Belegfeld 1'] = mb_strimwidth($row['belegnr'],0,36);
                            $data['Konto'] = $row['kundennummer'];
                            $data['Soll-/Haben-Kennzeichen'] = ($difference < 0)?'S':'H'; // obligatory

                            $data['Gegenkonto (ohne BU-Schlüssel)'] = $sachkonto_differences; // obligatory

                            $data['Belegdatum'] = date_format(date_create($row['datum']),"dm"); // obligatory
                            $data['Buchungstext'] = "Differenz";
                            $data['EU-Mitgliedstaat u. UStID (Bestimmung)'] = $row['ustid'];
                            $data['Auftragsnummer'] = $row['auftrag'];
                            $data['Zahlweise'] = $row['zahlweise'];
                		    $csv .= $this->create_line($datev_buchungsstapel_definition,$data);
                        }
        		    }
                }
            }      // diffignore

            // Query position data
            $arr = $this->app->DB->Query($sql);
            while ($row = $this->app->DB->Fetch_Assoc($arr)) {

                    //print_r($row);

                $posid = $row['pos_id'];
                $tmpsteuersatz = 0;
                $tmpsteuertext = '';
                $erloes = '';
                $result = array();
                $this->app->erp->GetSteuerPosition($typ['typ'], $posid, $tmpsteuersatz, $tmpsteuertext, $erloes);

                $data = array();

                if ($row['betrag'] > 0) {
                    $data['Umsatz'] = number_format($row['betrag'], 2, ',', ''); // obligatory
                    $data['Soll-/Haben-Kennzeichen'] = $typ['kennzeichen']; // obligatory
                } else if ($row['betrag'] < 0) {
                    $data['Umsatz'] = number_format(-$row['betrag'], 2, ',', ''); // obligatory
                    $data['Soll-/Haben-Kennzeichen'] = $typ['kennzeichen_negativ']; // obligatory
                } else {
                    continue;
                }

                $data['EU-Steuersatz (Bestimmung)'] = number_format($tmpsteuersatz, 2, ',', '');
                $data['WKZ Umsatz'] = $row['pos_waehrung'];
                $data['Belegfeld 1'] = mb_strimwidth($row['belegnr'],0,36);
                $data['Konto'] = $row['kundennummer']; // obligatory

                if (!empty($typ['field_gegenkonto'])) {
                    $data['Gegenkonto (ohne BU-Schlüssel)'] = $row['gegenkonto']; // obligatory
                } else {
                    $data['Gegenkonto (ohne BU-Schlüssel)'] = $erloes; // obligatory
                }

                $data['Belegdatum'] = date_format(date_create($row['datum']),"dm"); // obligatory
                $data['Buchungstext'] = mb_strimwidth($row['name'],0,60);
                $data['EU-Mitgliedstaat u. UStID (Bestimmung)'] = $row['ustid'];

                $data['Auftragsnummer'] = ($row['auftrag']!=0)?$row['auftrag']:'';
                $data['Zahlweise'] = $row['zahlweise'];

                $csv .= $this->create_line($datev_buchungsstapel_definition,$data);
            }
        }

        $csv .= '"0";"S";"EUR";"0";"";"";"1234";"1370";"";"101";"";"";"";"Testbuchung";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"0";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";""'; // Testbuchung

        switch ($format) {
            case "UTF-8":
            break;
            case "UTF-8-BOM":
                $csv = "\xef\xbb\xbf".$csv;
            break;
            default:
                $csv = mb_convert_encoding($csv, "ISO-8859-1", "UTF-8");
            break;
        }

        return($csv);
    }

    function create_line($definition, $data) : string {
        $csv = "";
        $comma = "";
        foreach ($definition as $key => $value) {
            if (!isset($data[$value])) {
                $data[$value] = '';
            }
            $csv .= $comma.'"'.$data[$value].'"';
            $comma = ";";
        }
        $csv .= "\r\n";
        return($csv);
    }

}

/*
Documentation DATEV formats
HEADER
﻿| #  | Überschrift             | Ausdruck                                                                                                                            | Beschreibung                                                                                                                                          |
|----|-------------------------|-------------------------------------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------|
| 1  | Kennzeichen             | ^["](EXTF|DTVF)["]$                                                                                                                 | EXTF = Export aus 3rd-Party App DTVF = Export aus DATEV App                                                                                           |
| 2  | Versionsnummer          | ^(700)$                                                                                                                             | Versionsnummer des Headers. Anhand der Versionsnummer können ältere Versionen abwärtskompatibel verarbeitet werden.                                   |
| 3  | Formatkategorie         | ^(16|20|21|46|48|65)$                                                                                                               | 16 = Debitoren-/Kreditoren 20 = Kontenbeschriftungen 21 = Buchungsstapel 46 = Zahlungsbedingungen 48 = Diverse Adressen 65 = Wiederkehrende Buchungen |
| 4  | Formatname              | ^["](Buchungsstapel|Wiederkehrende Buchungen|Debitoren/Kreditoren| Kontenbeschriftungen| Zahlungsbedingungen| Diverse Adressen)["]$ | Formatname                                                                                                                                            |
| 5  | Formatversion           | ^(2|4|5|12)$                                                                                                                        | Debitoren-/Kreditoren = 5 Kontenbeschriftungen = 3 Buchungsstapel = 12 Zahlungsbedingungen = 2 Wiederkehrende Buchungen = 4 Diverse Adressen = 2      |
| 6  | Erzeugt am              | ^([2])([0])([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])(2[0-3]|[01][0-9])([0-5][0-9])([0-5][0-9][0-9][0-9][0-9])$            | Zeitstempel: YYYYMMDDHHMMSSFFF                                                                                                                        |
| 7  | Reserviert              | ^[]$                                                                                                                                | Leerfeld                                                                                                                                              |
| 8  | Reserviert              | ^["]\w{0,2}["]$                                                                                                                     | Leerfeld                                                                                                                                              |
| 9  | Reserviert              | ^["]\w{0,25}["]$                                                                                                                    | Leerfeld                                                                                                                                              |
| 10 | Reserviert              | ^["]\w{0,25}["]$                                                                                                                    | Leerfeld                                                                                                                                              |
| 11 | Beraternummer           | ^(\d{4,6}|\d{7})$                                                                                                                   | Bereich 1001-9999999                                                                                                                                  |
| 12 | Mandantennummer         | ^\d{1,5}$                                                                                                                           | Bereich 1-99999                                                                                                                                       |
| 13 | WJ-Beginn               | ^([2])([0])([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])$                                                                     | Wirtschaftsjahresbeginn Format: YYYYMMDD                                                                                                              |
| 14 | Sachkontenlänge         | ^[4-8]$                                                                                                                             | Nummernlänge der Sachkonten. Wert muss beim Import mit Konfiguration des Mandats in der DATEV App übereinstimmen.                                     |
| 15 | Datum von               | ^([2])([0])([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])$                                                                     | Beginn der Periode des Stapels Format: YYYYMMDD Siehe Anhang 2.                                                                                       |
| 16 | Datum bis               | ^([2])([0])([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])$                                                                     | Ende der Periode des Stapels Format: YYYYMMDD Siehe Anhang 2.                                                                                         |
| 17 | Bezeichnung             | ^["][\w.-/ ]{0,30}["]$                                                                                                              | Bezeichnung des Stapels z.B. „Rechnungsausgang 09/2019“                                                                                               |
| 18 | Diktatkürzel            | ^["]([A-Z]{2}){0,2}["]$                                                                                                             | Kürzel in Großbuchstaben des Bearbeiters z.B. "MM" für Max Mustermann                                                                                 |
| 19 | Buchungstyp             | ^[1-2]$                                                                                                                             | 1 = Finanzbuchführung        (default) 2 = Jahresabschluss                                                                                            |
| 20 | Rechnungs- legungszweck | ^(0|30|40|50|64)$                                                                                                                   | 0 = unabhängig        (default) 30 = Steuerrecht 40 = Kalkulatorik 50 = Handelsrecht 64 = IFRS                                                        |
| 21 | Festschreibung          | ^(0|1)$                                                                                                                             | 0 = keine Festschreibung 1 = Festschreibung        (default)                                                                                          |
| 22 | WKZ                     | ^["]([A-Z]{3})["]$                                                                                                                  | ISO-Code der Währung "EUR" = default Liste der ISO-Codes                                                                                              |
| 23 | Reserviert              | ^[]$                                                                                                                                | Leerfeld                                                                                                                                              |
| 24 | Derivatskennzeichen     | ^["]["]$                                                                                                                            | Leerfeld                                                                                                                                              |
| 25 | Reserviert              | ^[]$                                                                                                                                | Leerfeld                                                                                                                                              |
| 26 | Reserviert              | ^[]$                                                                                                                                | Leerfeld                                                                                                                                              |
| 27 | Sachkonten- rahmen      | ^["](\d{2}){0,2}["]$                                                                                                                | Sachkontenrahmen der für die Bewegungsdaten verwendet wurde                                                                                           |
| 28 | ID der Branchen- lösung | ^\d{0,4}$                                                                                                                           | Falls eine spezielle DATEV Branchenlösung genutzt wird.                                                                                               |
| 29 | Reserviert              | ^[]$                                                                                                                                | Leerfeld                                                                                                                                              |
| 30 | Reserviert              | ^["]["]$                                                                                                                            | Leerfeld                                                                                                                                              |
| 31 | Anwendungs- information | ^["].{0,16}["]$                                                                                                                     | Verarbeitungskennzeichen der abgebenden Anwendung z.B. „09/2019“                                                                                      |

| #   | Überschrift                               | Ausdruck                                                                                                         | Beschreibung                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     |
|-----|-------------------------------------------|------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| 1   | Umsatz                                    | ^\d{1,10}[,]\d{2}$                                                                                               | Umsatz/Betrag für den Datensatz z.B.: 1234567890,12 Betrag muss positiv sein.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    |
| 2   | Soll-/Haben-Kennzeichen                   | ^["](S|H)["]$                                                                                                    | Soll-/Haben-Kennzeichnung bezieht sich auf das Feld #7 Konto S = SOLL (default) H = HABEN                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 3   | WKZ Umsatz                                | ^["]([A-Z]{3})["]$                                                                                               | ISO-Code der Währung #22 aus Header = default Liste der ISO-Codes                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| 4   | Kurs                                      | ^([1-9]\d{0,3}[,]\d{2,6})$                                                                                       | Wenn Umsatz in Fremdwährung bei #1 angegeben wird #004, 005 und 006 sind zu übergeben z.B.: 1234,123456                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| 5   | Basisumsatz                               | ^(\d{1,10}[,]\d{2})$                                                                                             | Siehe #004. z.B.: 1234567890,12                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| 6   | WKZ Basisumsatz                           |                                                                                                                  | Siehe #004. Liste der ISO-Codes                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| 7   | Konto                                     | ^(\d{1,9})$                                                                                                      | Sach- oder Personenkonto z.B. 8400                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               |
| 8   | Gegenkonto (ohne BU-Schlüssel)            | ^(\d{1,9})$                                                                                                      | Sach- oder Personenkonto z.B. 70000                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              |
| 9   | BU-Schlüssel                              | ^(["]\d{4}["])$                                                                                                  | Steuerungskennzeichen zur Abbildung verschiedener Funktionen/Sachverhalte Weitere Details                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 10  | Belegdatum                                | ^(\d{4})$                                                                                                        | Format: TTMM, z.B. 0105 Das Jahr wird immer aus dem Feld 13 des Headers ermittelt                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| 11  | Belegfeld 1                               | ^(["][\w$%\-\/]{0,36}["])$                                                                                       | Rechnungs-/Belegnummer Wird als "Schlüssel" für den Ausgleich offener Rechnungen genutzt z.B. "Rg32029/2019" Sonderzeichen: $ & % * + - / Andere Zeichen sind unzulässig (insbesondere Leerzeichen, Umlaute, Punkt, Komma, Semikolon und Doppelpunkt).                                                                                                                                                                                                                                                                                                                                                                                                           |
| 12  | Belegfeld 2                               | ^(["][\w$%\-\/]{0,12}["])$                                                                                       | Mehrere Funktionen Details siehe hier                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            |
| 13  | Skonto                                    | ^([1-9]\d{0,7}[,]\d{2})$                                                                                         | Skontobetrag z.B. 3,71 nur bei Zahlungsbuchungen zulässig                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 14  | Buchungstext                              | ^(["].{0,60}["])$                                                                                                | 0-60 Zeichen                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     |
| 15  | Postensperre                              | ^(0|1)$                                                                                                          | Mahn- oder Zahlsperre 0 = keine Sperre (default) 1 = Sperre Die Rechnung kann aus dem Mahnwesen / Zahlungsvorschlag ausgeschlossen werden.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       |
| 16  | Diverse Adressnummer                      | ^(["]\w{0,9}["])$                                                                                                | Adressnummer einer diversen Adresse. #OPOS                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       |
| 17  | Geschäftspartnerbank                      | ^(\d{3})$                                                                                                        | Referenz um für Lastschrift oder Zahlung eine bestimmte Geschäftspartnerbank genutzt werden soll. #OPOS Beim Import der Geschäftspartnerbank muss auch das Feld SEPA-Mandatsreferenz (Feld-Nr. 105) gefüllt sein.                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| 18  | Sachverhalt                               | ^(\d{2})$                                                                                                        | Kennzeichen für einen Mahnzins/Mahngebühr-Datensatz 31 = Mahnzins 40 = Mahngebühr #OPOS                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| 19  | Zinssperre                                | ^(0|1)$                                                                                                          | Sperre für Mahnzinsen 0 = keine Sperre (default) 1 = Sperre #OPOS                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| 20  | Beleglink                                 | Generell:^(["].{0,210}["])$ Konkret für Link in eine DATEV App:^ ["](BEDI|DDMS|DORG)[ ]["] ["][<GUID>]["]["]["]$ | Link zu einem digitalen Beleg in einer DATEV App. BEDI = Unternehmen online Der Beleglink besteht aus einem Programmkürzel und der GUID. Da das Feld Beleglink ein Textfeld ist, müssen in der Schnittstellendatei die Anführungszeichen verdoppelt werden. z.B. "BEDI ""f9a0475d-d0df…"""                                                                                                                                                                                                                                                                                                                                                                       |
| 21  | Beleginfo -Art 1                          | ^(["].{0,20}["])$                                                                                                | Bei einem DATEV-Format, das aus einem DATEV-Rechnungswesen-Programm erstellt wurde, können diese Felder Informationen aus einem Beleg (z. B. einem elektronischen Kontoumsatz) enthalten. Wird die Feldlänge eines Beleginfo-Inhalts-Feldes überschrit- ten, wird die Information im nächsten Beleginfo-Feld weitergeführt. Wichtiger Hinweis Eine Beleginfo besteht immer aus den Bestandteilen Beleginfo-Art und Beleginfo-Inhalt. Wenn Sie die Beleginfo nutzen möchten, füllen Sie bitte immer beide Felder. Beispiel: Beleginfo-Art: Kontoumsätze der jeweiligen Bank Beleginfo-Inhalt: Buchungsspezifische Inhalte zu den oben genannten Informationsarten |
| 22  | Beleginfo -Inhalt 1                       | ^(["].{0,210}["])$                                                                                               | siehe #21                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 23  | Beleginfo -Art 2                          | ^(["].{0,20}["])$                                                                                                | siehe #21                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 24  | Beleginfo -Inhalt 2                       | ^(["].{0,210}["])$                                                                                               | siehe #21                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 25  | Beleginfo -Art 3                          | ^(["].{0,20}["])$                                                                                                | siehe #21                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 26  | Beleginfo -Inhalt 3                       | ^(["].{0,210}["])$                                                                                               | siehe #21                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 27  | Beleginfo -Art 4                          | ^(["].{0,20}["])$                                                                                                | siehe #21                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 28  | Beleginfo -Inhalt 4                       | ^(["].{0,210}["])$                                                                                               | siehe #21                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 29  | Beleginfo -Art 5                          | ^(["].{0,20}["])$                                                                                                | siehe #21                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 30  | Beleginfo -Inhalt 5                       | ^(["].{0,210}["])$                                                                                               | siehe #21                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 31  | Beleginfo -Art 6                          | ^(["].{0,20}["])$                                                                                                | siehe #21                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 32  | Beleginfo -Inhalt 6                       | ^(["].{0,210}["])$                                                                                               | siehe #21                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 33  | Beleginfo -Art 7                          | ^(["].{0,20}["])$                                                                                                | siehe #21                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 34  | Beleginfo -Inhalt 7                       | ^(["].{0,210}["])$                                                                                               | siehe #21                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 35  | Beleginfo -Art 8                          | ^(["].{0,20}["])$                                                                                                | siehe #21                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 36  | Beleginfo -Inhalt 8                       | ^(["].{0,210}["])$                                                                                               | siehe #21                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 37  | KOST1 -Kostenstelle                       | ^(["][\w ]{0,36}["])$                                                                                            | Über KOST1 erfolgt die Zuordnung des Geschäftsvorfalls für die anschließende Kostenrechnung. Die benutzte Länge muss vorher in den Stammdaten vom KOST-Programm eingestellt werden.                                                                                                                                                                                                                                                                                                                                                                                                                                                                              |
| 38  | KOST2 -Kostenstelle                       | ^(["][\w ]{0,36}["])$                                                                                            | Über KOST2 erfolgt die Zuordnung des Geschäftsvorfalls für die anschließende Kostenrechnung. Die benutzte Länge muss vorher in den Stammdaten vom KOST-Programm eingestellt werden.                                                                                                                                                                                                                                                                                                                                                                                                                                                                              |
| 39  | KOST-Menge                                | ^\d{12}[,]\d{4}$                                                                                                 | Im KOST-Mengenfeld wird die Wertgabe zu einer bestimmten Bezugsgröße für eine Kostenstelle erfasst. Diese Bezugsgröße kann z. B. kg, g, cm, m, % sein. Die Bezugsgröße ist definiert in den Kostenrechnungs-Stammdaten. Beispiel:123123123,1234                                                                                                                                                                                                                                                                                                                                                                                                                  |
| 40  | EU-Mitgliedstaat u. UStID (Bestimmung)    | ^(["].{0,15}["])$                                                                                                | Die USt-IdNr. besteht aus  - 2-stelligen Länderkürzel (siehe Dok.-Nr. 1080169; Ausnahme Griechenland und Nordirland: Das Länderkürzel lautet EL für Griechenland und XI für Nordirland)  - 13-stelliger USt-IdNr.  - Beispiel: DE133546770. Die USt-IdNr kann auch Buchstaben haben, z.B.: bei Österreich Detaillierte Informationen zur Erfassung von EU-Informationen im Buchungssatz: Dok.-Nr: 9211462.                                                                                                                                                                                                                                                       |
| 41  | EU-Steuersatz (Bestimmung)                | ^\d{2}[,]\d{2}$                                                                                                  | Nur für entsprechende EU-Buchungen: Der im EU-Bestimmungsland gültige Steuersatz. Beispiel: 12,12                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| 42  | Abw. Versteuerungsart                     | ^(["](I|K|P|S)["])$                                                                                              | Für Buchungen, die in einer von der Mandantenstammdaten- Schlüsselung abweichenden Umsatzsteuerart verarbeitet werden sollen, kann die abweichende Versteuerungsart im Buchungssatz übergeben werden: I = Ist-Versteuerung K = keine Umsatzsteuerrechnung P = Pauschalierung (z. B. für Land- und Forstwirtschaft) S = Soll-Versteuerung                                                                                                                                                                                                                                                                                                                         |
| 43  | Sachverhalt L+L                           | ^(\d{1,3})$                                                                                                      | Sachverhalte gem. § 13b Abs. 1 Satz 1 Nrn. 1.-5. UStG Achtung: Der Wert 0 ist unzulässig. Sachverhalts-Nummer siehe Info-Doku 1034915                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            |
| 44  | Funktionsergänzung L+L                    | ^\d{0,3}$                                                                                                        | Steuersatz / Funktion zum L+L-Sachverhalt Achtung: Der Wert 0 ist unzulässig. Beispiel: Wert 190 für 19%                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         |
| 45  | BU 49 Hauptfunktiontyp                    | ^\d$                                                                                                             | Bei Verwendung des BU-Schlüssels 49 für „andere Steuer- sätze“ muss der steuerliche Sachverhalt mitgegeben werden                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| 46  | BU 49 Hauptfunktionsnummer                | ^\d{0,2}$                                                                                                        | siehe #45                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 47  | BU 49 Funktionsergänzung                  | ^\d{0,3}$                                                                                                        | siehe #45                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 48  | Zusatzinformation – Art 1                 | ^(["].{0,20}["])$                                                                                                | Zusatzinformationen, die zu Buchungssätzen erfasst werden können. Diese Zusatzinformationen besitzen den Charakter eines Notizzettels und können frei erfasst werden. Wichtiger Hinweis Eine Zusatzinformation besteht immer aus den Bestandtei- len Informationsart und Informationsinhalt. Wenn Sie die Zusatzinformation nutzen möchten, füllen Sie bitte immer beide Felder. Beispiel: Informationsart, z. B. Filiale oder Mengengrößen (qm) Informationsinhalt: buchungsspezifische Inhalte zu den oben genannten Informationsarten.                                                                                                                        |
| 49  | Zusatzinformation – Inhalt 1              | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 50  | Zusatzinformation – Art 2                 | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 51  | Zusatzinformation – Inhalt 2              | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 52  | Zusatzinformation – Art 3                 | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 53  | Zusatzinformation – Inhalt 3              | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 54  | Zusatzinformation – Art 4                 | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 55  | Zusatzinformation – Inhalt 4              | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 56  | Zusatzinformation – Art 5                 | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 57  | Zusatzinformation – Inhalt 5              | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 58  | Zusatzinformation – Art 6                 | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 59  | Zusatzinformation – Inhalt 6              | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 60  | Zusatzinformation – Art 7                 | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 61  | Zusatzinformation – Inhalt 7              | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 62  | Zusatzinformation – Art 8                 | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 63  | Zusatzinformation – Inhalt 8              | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 64  | Zusatzinformation – Art 9                 | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 65  | Zusatzinformation – Inhalt 9              | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 66  | Zusatzinformation – Art 10                | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 67  | Zusatzinformation – Inhalt 10             | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 68  | Zusatzinformation – Art 11                | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 69  | Zusatzinformation – Inhalt 11             | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 70  | Zusatzinformation – Art 12                | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 71  | Zusatzinformation – Inhalt 12             | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 72  | Zusatzinformation – Art 13                | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 73  | Zusatzinformation – Inhalt 13             | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 74  | Zusatzinformation – Art 14                | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 75  | Zusatzinformation – Inhalt 14             | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 76  | Zusatzinformation – Art 15                | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 77  | Zusatzinformation – Inhalt 15             | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 78  | Zusatzinformation – Art 16                | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 79  | Zusatzinformation – Inhalt 16             | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 80  | Zusatzinformation – Art 17                | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 81  | Zusatzinformation – Inhalt 17             | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 82  | Zusatzinformation – Art 18                | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 83  | Zusatzinformation – Inhalt 18             | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 84  | Zusatzinformation – Art 19                | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 85  | Zusatzinformation – Inhalt 19             | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 86  | Zusatzinformation – Art 20                | ^(["].{0,20}["])$                                                                                                | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 87  | Zusatzinformation – Inhalt 20             | ^(["].{0,210}["])$                                                                                               | siehe #48                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 88  | Stück                                     | ^\d{0,8}$                                                                                                        | Wirkt sich nur bei Sachverhalt mit SKR 14 Land- und Forst- wirtschaft aus, für andere SKR werden die Felder beim Import / Export überlesen bzw. leer exportiert.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| 89  | Gewicht                                   | ^(\d{1,8}[,]\d{2})$                                                                                              | siehe #88                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 90  | Zahlweise                                 | ^\d{0,2}$                                                                                                        | OPOS-Informationen 1 = Lastschrift 2 = Mahnung 3 = Zahlung                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       |
| 91  | Forderungsart                             | ^(["]\w{0,10}["])$                                                                                               | OPOS-Informationen                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               |
| 92  | Veranlagungsjahr                          | ^(([2])([0])([0-9]{2}))$                                                                                         | OPOS-Informationen Format: JJJJ                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| 93  | Zugeordnete Fälligkeit                    | ^((0[1-9]|[1-2][0-9]|3[0-1])(0[1-9]|1[0-2])([2])([0])([0-9]{2}))$                                                | OPOS-Informationen Format: TTMMJJJJ                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              |
| 94  | Skontotyp                                 | ^\d$                                                                                                             | 1 = Einkauf von Waren 2 = Erwerb von Roh-Hilfs- und Betriebsstoffen                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              |
| 95  | Auftragsnummer                            | ^(["].{0,30}["])$                                                                                                | Allgemeine Bezeichnung, des Auftrags / Projekts. Mit der Auftragsnummer muss auch der Buchungstyp (Feld 96) angegeben werden.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    |
| 96  | Buchungstyp                               | ^(["][A-Z]{2}["])$                                                                                               | AA = Angeforderte Anzahlung / Abschlagsrechnung AG = Erhaltene Anzahlung (Geldeingang) AV = Erhaltene Anzahlung (Verbindlichkeit) SR = Schlussrechnung SU = Schlussrechnung (Umbuchung) SG = Schlussrechnung (Geldeingang) SO = Sonstige                                                                                                                                                                                                                                                                                                                                                                                                                         |
| 97  | USt-Schlüssel (Anzahlungen)               | ^\d{0,2}$                                                                                                        | USt-Schlüssel der späteren Schlussrechnung                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       |
| 98  | EU-Mitgliedstaat (Anzahlungen)            | ^(["][A-Z]{2}["])$                                                                                               | EU-Mitgliedstaat der späteren Schlussrechnung siehe Info-Doku 1080169                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            |
| 99  | Sachverhalt L+L (Anzahlungen)             | ^\d{0,3}$                                                                                                        | L+L-Sachverhalt der späteren Schlussrechnung Sachverhalte gem. § 13b UStG Achtung: Der Wert 0 ist unzulässig. Sachverhalts-Nummer siehe Info-Doku 1034915                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| 100 | EU-Steuersatz (Anzahlungen)               | ^(\d{1,2}[,]\d{2})$                                                                                              | EU-Steuersatz der späteren Schlussrechnung Nur für entsprechende EU-Buchungen: Der im EU-Bestimmungsland gültige Steuersatz. Beispiel: 12,12                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     |
| 101 | Erlöskonto (Anzahlungen)                  | ^(\d{4,8})$                                                                                                      | Erlöskonto der späteren Schlussrechnung                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| 102 | Herkunft-Kz                               | ^(["][A-Z]{2}["])$                                                                                               | Wird beim Import durch SV (Stapelverarbeitung) ersetzt.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| 103 | Leerfeld                                  | ^(["].{0,36}["])$                                                                                                | Wird von DATEV verwendet                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         |
| 104 | KOST-Datum                                | ^((0[1-9]|[1-2]\d|3[0-1])(0[1-9]|1[0-2])([2])([0])(\d{2}))$                                                      | Format TTMMJJJJ                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| 105 | SEPA-Mandatsreferenz                      | ^(["].{0,35}["])$                                                                                                | Vom Zahlungsempfänger individuell vergebenes Kennzeichen eines Mandats (z.B. Rechnungs- oder Kundennummer). Beim Import der SEPA-Mandatsreferenz muss auch das Feld Geschäftspartnerbank (Feld-Nr. 17) gefüllt sein.                                                                                                                                                                                                                                                                                                                                                                                                                                             |
| 106 | Skontosperre                              | ^[0|1]$                                                                                                          | Gültige Werte: 0, 1. 1 = Skontosperre 0 = Keine Skontosperre                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     |
| 107 | Gesellschaftername                        | ^(["].{0,76}["])$                                                                                                |                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| 108 | Beteiligtennummer                         | ^(\d{4})$                                                                                                        | Die Beteiligtennummer muss der amtlichen Nummer aus der Feststellungserklärung entsprechen, diese darf nicht beliebig vergeben werden. Die Pflege der Gesellschafterdaten und das Anlegen von Sonderbilanzsachverhalte ist nur in Absprache mit der Steuerkanzlei möglich. Betrifft Feld 107-110.                                                                                                                                                                                                                                                                                                                                                                |
| 109 | Identifikationsnummer                     | ^(["].{0,11}["])$                                                                                                |                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| 110 | Zeichnernummer                            | ^(["].{0,20}["])$                                                                                                |                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| 111 | Postensperre bis                          | ^((0[1-9]|[1-2]\d|3[0-1])(0[1-9]|1[0-2])([2])([0])(\d{2}))$                                                      | Format TTMMJJJJ                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| 112 | Bezeichnung SoBil-Sachverhalt             | ^(["].{0,30}["])$                                                                                                |                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| 113 | Kennzeichen SoBil-Buchung                 | ^(\d{1,2})$                                                                                                      | Sobil-Buchung erzeugt = 1 Sobil-Buchung nicht erzeugt = (Default) bzw. 0                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         |
| 114 | Festschreibung                            | ^(0|1)$                                                                                                          | leer = nicht definiert; wird automatisch festgeschrieben 0 = keine Festschreibung 1 = Festschreibung Hat ein Buchungssatz in diesem Feld den Inhalt 1, so wird der gesamte Stapel nach dem Import festgeschrieben.                                                                                                                                                                                                                                                                                                                                                                                                                                               |
| 115 | Leistungsdatum                            | ^((0[1-9]|[1-2]\d|3[0-1])(0[1-9]|1[0-2])([2])([0])(\d{2}))$                                                      | Format TTMMJJJJ siehe Info-Doku 9211426 Beim Import des Leistungsdatums muss das Feld „116 Datum Zuord. Steuer-periode“ gefüllt sein. Der Einsatz des Leistungsdatums muss in Absprache mit dem Steuerberater erfolgen.                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| 116 | Datum Zuord. Steuerperiode                | ^((0[1-9]|[1-2]\d|3[0-1])(0[1-9]|1[0-2])([2])([0])(\d{2}))$                                                      | Format TTMMJJJJ                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| 117 | Fälligkeit                                | ^((0[1-9]|[1-2]\d|3[0-1])(0[1-9]|1[0-2])([2])([0])(\d{2}))$                                                      | OPOS Informationen, Format: TTMMJJJJ OPOS-Verarbeitungsinformationen über Belegfeld 2 (Feldnummer 12) sind in diesem Fall nicht nutzbar                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| 118 | Generalumkehr                             | ^(["](0|1)["])$                                                                                                  | G oder 1 = Generalumkehr 0 = keine Generalumkehr                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| 119 | Steuersatz                                | ^(\d{1,2}[,]\d{2})$                                                                                              | Wird bei Verwendung von BU-Schlüssel ohne festen Steuersatz benötigt (z. B. BU-Schlüssel 100). Weitere Informationen unter Dok.Nr. 9231347 Kapitel „Erfassung eines Steuersatzes bei Steuerschlüsseln“                                                                                                                                                                                                                                                                                                                                                                                                                                                           |
| 120 | Land                                      | ^(["][A-Z]{2}["])$                                                                                               | Beispiel: DE für Deutschland                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     |
| 121 | Abrechnungsreferenz                       | ^(["].{0,50}["])$                                                                                                | Die Abrechnungsreferenz stellt eine Klammer über alle Transaktionen des Zahlungsdienstleisters und die dazu gehörige Auszahlung dar. Sie wird über den Zahlungsdatenservice bereitgestellt und bei der Erzeugung von Buchungsvorschläge berücksichtigt.                                                                                                                                                                                                                                                                                                                                                                                                          |
| 122 | BVV-Position (Betriebsvermögensvergleich) | ^([1|2|3|4|5])$                                                                                                  | Details zum Feld siehe hier 1 Kapitalanpassung 2 Entnahme / Ausschüttung lfd. WJ 3 Einlage / Kapitalzuführung lfd. WJ 4 Übertragung § 6b Rücklage 5 Umbuchung (keine Zuordnung)                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| 123 | EU-Mitgliedstaat u. UStID (Ursprung)      | ^(["].{0,15}["])$                                                                                                | Die USt-IdNr. besteht aus  - 2-stelligen Länderkürzel (siehe Dok.-Nr. 1080169) Ausnahme Griechenland: Das Länderkürzel lautet EL)  - 13-stelliger USt-IdNr.  - Beispiel: DE133546770. Die USt-IdNr kann auch Buchstaben haben, z.B.: bei Österreich Detaillierte Informationen zur Erfassung von EU-Informationen im Buchungssatz: Dok.-Nr: 9211462.                                                                                                                                                                                                                                                                                                             |
| 124 | EU-Steuersatz (Ursprung)                  | ^\d{2}[,]\d{2}$                                                                                                  | Nur für entsprechende EU-Buchungen: Der im EU-Ursprungsland gültige Steuersatz. Beispiel: 12,12                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
*/
