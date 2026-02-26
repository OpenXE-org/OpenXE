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

use Xentral\Modules\DatevExport\DatevExport;
use Xentral\Modules\DatevExport\ConsistencyException;

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
        $sachkontofehlend = $this->app->Secure->GetPOST('sachkontofehlend');
        $pdfexport = $this->app->Secure->GetPOST("pdfexport");
        $format = $this->app->Secure->GetPOST('format');

	    $account_id = null;
    	if (!empty($sachkonto)) {
    	    $sachkonto_kennung = explode(' ',$sachkonto)[0];
            $account_id = $this->app->DB->SelectArr("SELECT id from kontorahmen WHERE sachkonto = '".$sachkonto_kennung."'")[0]['id'];
    	}
        if (!empty($sachkontofehlend)) {
            $sachkontofehlend_kennung = explode(' ',$sachkontofehlend)[0];
            $account_id_fehlend = $this->app->DB->SelectArr("SELECT id from kontorahmen WHERE sachkonto = '".$sachkontofehlend."'")[0]['id'];
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
            $sachkonto = $this->app->User->GetParameter('exportbuchhaltung_sachkonto');
            $sachkontofehlend = $this->app->User->GetParameter('exportbuchhaltung_sachkontofehlend');
            $pdfexport = $this->app->User->GetParameter('exportbuchhaltung_pdfexport');
        } else {
            $this->app->User->SetParameter('exportbuchhaltung_sachkonto', $sachkonto);
            $this->app->User->SetParameter('exportbuchhaltung_sachkontofehlend', $sachkontofehlend);
            $this->app->User->SetParameter('exportbuchhaltung_pdfexport', $pdfexport);
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
                $belege = array();
                $typen = $this->typen($rgchecked, $gschecked, $vbchecked, $lgchecked);
                foreach ($typen as $typkey => $typvalue) {
                    if (!$typvalue['do']) {
                        continue;
                    }
                    $where = "b.".$typvalue['field_date']." BETWEEN '".date_format($von,"Y-m-d")."' AND '".date_format($bis,"Y-m-d")."' AND (b.projekt=$projekt OR $projekt=0)".$typvalue['condition_where'];
                    $sql = "SELECT
                        b.id,
                        " . $typvalue['field_belegnr'] . " as belegnr,
                        " . $typvalue['field_auftrag'] . " as auftrag,
                        " . $typvalue['field_zahlweise'] . " as zahlweise,
                        if(" . $typvalue['field_kontonummer'] . " <> ''," . $typvalue['field_kontonummer'] . "," . $typvalue['field_kundennummer'] . ") as kundennummer,
                        " . $typvalue['field_name'] . " as name,
                        a.ustid,
                        b." . $typvalue['field_date'] . " as datum,
                        " . $typvalue['field_betrag_gesamt'] . " as betrag_gesamt,
                        b.waehrung,
                        UUID() uuid
                    FROM
                        " . $typvalue['typ'] . " b
                            INNER JOIN
                        adresse a ON a.id = b.adresse
                            WHERE
                        " . $where;

                    $belegearr = $this->app->DB->SelectArr($sql);

                    $belege[$typkey]['table'] = $typvalue['typ'];
                    $belege[$typkey]['kennzeichen'] = $typvalue['kennzeichen'];
                    $belege[$typkey]['kennzeichen_negativ'] = $typvalue['kennzeichen_negativ'];
                    $belege[$typkey]['field_gegenkonto'] = $typvalue['field_gegenkonto'];

                    foreach ($belegearr as $value) {
                        $belege[$typkey]['belege'][$value['id']] = $value;
                        $belege[$typkey]['belege'][$value['id']]['typ'] = $typvalue['typ'];
                    }

                    // Hole alle Positionen dazu
                    if (!empty($typvalue['field_gegenkonto'])) {
                        $sql_gegenkonto = $typvalue['field_gegenkonto'];
                    } else {
                        $sql_gegenkonto = "NULL";
                    }

                    $sql = "SELECT
                        b.id as beleg_id,
                        p.id as pos_id,
                        ROUND(" . $typvalue['field_betrag'] . ",2) as betrag,
                        " . $sql_gegenkonto . " as gegenkonto,
                        b.waehrung as pos_waehrung
                    FROM
                        " . $typvalue['typ'] . " b
                            LEFT JOIN
                        " . $typvalue['subtable'] . " p
                            ON
                        b.id = p." . $typvalue['typ'] . "
                            WHERE
                        " . $where;

                    $posarr = $this->app->DB->SelectArr($sql);

                    foreach ($posarr as $pos) {

                        $tmpsteuersatz = 0;
                        $tmpsteuertext = '';
                        $erloes = '';
                        $result = array();
                        $this->app->erp->GetSteuerPosition($typvale['typ'], $pos['id'], $tmpsteuersatz, $tmpsteuertext, $erloes);

                        $pos['steuersatz'] = $tmpsteuersatz;
                        $pos['erloes'] = $erloes;

                        $belege[$typkey]['belege'][$pos['beleg_id']]['positionen'][] = $pos;
                    }
                } // foreach typen

                $usernamearr = explode(' ',strtoupper($this->app->User->GetName()." X X"));

                if (count($usernamearr) < 2) {
                    $kuerzel = $usernamearr[0][0].$usernamearr[0][1];
                }
                else {
                    $kuerzel = $usernamearr[0][0].$usernamearr[1][0];
                }

                $filename_csv = "EXTF_".date('Ymd') . "_Buchungsstapel_DATEV_export.csv";
                try {
                    $csv = DatevExport::createBuchungsstapelCSV(
                        beleg_data: $belege,
                        berater: $buchhaltung_berater,
                        mandant: $buchhaltung_mandant,
                        bearbeiter: $kuerzel,
                        wj_beginn: $buchhaltung_wj_beginn,
                        sachkontenlaenge: $buchhaltung_sachkontenlaenge,
                        von: $von,
                        bis: $bis,
                        filename: $filename_csv,
                        diffignore: $diffignore,
                        sachkonto_differences: $sachkonto_kennung,
                        sachkonto_missing: $sachkontofehlend_kennung,
                        format: $format
                    );

                    if ($pdfexport) {

                        $dateinamezip = 'Export_Buchhaltung_'.date('Y-m-d').'.zip';

                        $zip = new ZipArchive;
                        $zip->open($dateinamezip, ZipArchive::CREATE);

                        $zip->addFromString("/".$filename_csv, $csv);

                        $typen = $this->typen($rgchecked, $gschecked, $vbchecked, $lgchecked);

                        foreach ($typen as $typvalue) {
                            $sql = "
                                SELECT id, ".$typvalue['field_belegnr']." belegnr FROM ".$typvalue['typ']." b
                                WHERE
                                b.".$typvalue['field_date']." BETWEEN '".date_format($von,"Y-m-d")."' AND '".date_format($bis,"Y-m-d")."' AND (b.projekt=$projekt OR $projekt=0)".$typvalue['condition_where'];

                            $belege = $this->app->DB->SelectArr($sql);

                            foreach ($belege as $beleg) {
                                if (!$typvalue['do']) {
                                    continue;
                                }

                                $action = $typvalue['pdf'];

                                if ($typvalue['typ'] == 'rechnung') {
                                    if ($this->app->DB->Select("SELECT xmlrechnung FROM rechnung WHERE id = ".$beleg['id'])) {
                                        $action = 'load';
                                    }
                                }
                                switch ($action) {
                                    case 'print':
                                        switch ($typvalue['typ']) {
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
                                        $zip->addFromString($typvalue['typ']."/".$file_name, file_get_contents($tmpfile));
             			            break;
                                    case 'load':
                                        $file_attachments = $this->app->erp->GetDateiSubjektObjekt('%',$typvalue['typ'],$beleg['id']);
                                        $suffix = "";
                                        $count = 0;
                                        foreach ($file_attachments as $file_attachment) {
		                    			    $ending = $this->app->erp->GetDateiEndung($file_attachment);
                                            if (in_array($ending,['pdf','xml'])) {
                                                $file_contents = $this->app->erp->GetDatei($file_attachment);
                                                $file_name = filter_var($beleg['belegnr'],FILTER_SANITIZE_EMAIL).$suffix.".".$ending;
                                                $zip->addFromString($typvalue['typ']."/".$file_name, $file_contents);
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
                    $msg = "<div class=error>Inkonsistente Daten: <br>";
                    $data = $e->getData();
                    $count = 0;
                    foreach($data as $item) {
                        $msg .= ucfirst($item['typ'])." ".$item['belegnr']." (Kopf ".$this->app->erp->ReplaceMengeBetrag(false,$item['betrag_gesamt'],false)." Positionen ".$this->app->erp->ReplaceMengeBetrag(false,$item['betrag_summe'],false).")<br>";
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
        $this->app->YUI->AutoComplete('sachkontofehlend', 'sachkonto');

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
        $this->app->Tpl->SET('SACHKONTOFEHLEND', $sachkontofehlend);

        $this->app->Tpl->Parse('PAGE', "exportbuchhaltung_export.tpl");
    }
}

