<?php

/*
 * Copyright (c) 2023 OpenXE project
 * Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Verbindlichkeit {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "verbindlichkeit_list");
        $this->app->ActionHandler("create", "verbindlichkeit_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "verbindlichkeit_edit");
        $this->app->ActionHandler("positionen", "verbindlichkeit_positionen");
        $this->app->ActionHandler("delete", "verbindlichkeit_delete");
        $this->app->ActionHandler("deletepos", "verbindlichkeit_deletepos");
        $this->app->ActionHandler("editpos", "verbindlichkeit_editpos");
        $this->app->ActionHandler("dateien", "verbindlichkeit_dateien");
        $this->app->ActionHandler("inlinepdf", "verbindlichkeit_inlinepdf");
        $this->app->ActionHandler("positioneneditpopup", "verbindlichkeit_positioneneditpopup");
        $this->app->ActionHandler("freigabe", "verbindlichkeit_freigabe");
        $this->app->ActionHandler("freigabeeinkauf", "verbindlichkeit_freigabeeinkauf");
        $this->app->ActionHandler("freigabebuchhaltung", "verbindlichkeit_freigabebuchhaltung");
        $this->app->ActionHandler("freigabebezahlt", "verbindlichkeit_freigabebezahlt");
        $this->app->ActionHandler("ruecksetzeneinkauf", "verbindlichkeit_ruecksetzeneinkauf");
        $this->app->ActionHandler("ruecksetzenbuchhaltung", "verbindlichkeit_ruecksetzenbuchhaltung");
        $this->app->ActionHandler("ruecksetzenbezahlt", "verbindlichkeit_ruecksetzenbezahlt");
        $this->app->ActionHandler("minidetail", "verbindlichkeit_minidetail");

        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "verbindlichkeit_list":
                $allowed['verbindlichkeit_list'] = array('list');
                $heading = array('','','Belegnr','Adresse', 'Lieferant', 'RE-Nr', 'RE-Datum', 'Betrag (brutto)', 'W&auml;hrung','Zahlstatus', 'Ziel','Skontoziel','Skonto','Status','Monitor', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8);

                $findcols = array(
                    'v.id',
                    'v.id',
                    'v.belegnr',
                    'a.name',
                    'a.lieferantennummer',
                    'v.rechnung',
                    'v.rechnungsdatum',
                    'v.betrag',
                    'v.waehrung',
                    'v.bezahlt',
                    'v.zahlbarbis',
                    'v.skontobis',
                    'v.skonto',
                    'v.status',
                    'v.status_beleg',
                    'v.id'
                );

                $searchsql = array(
                    'a.name',
                    'a.lieferantennummer',
                    'v.rechnung',
                    'v.internebemerkung'
                );

                $defaultorder = 1;
                $defaultorderdesc = 0;
                $alignright = array(8);
                $sumcol = array(8);

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',v.id,'\" />') AS `auswahl`";

//                $moreinfo = true; // Allow drop down details
//                $moreinfoaction = "lieferschein"; // specify suffix for minidetail-URL to allow different minidetails
//                $menucol = 11; // Set id col for moredata/menu

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=verbindlichkeit&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=verbindlichkeit&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS
                            v.id,
                            $dropnbox,
                            v.belegnr,
                            a.name,
                            a.lieferantennummer,
                            v.rechnung,
                            ".$app->erp->FormatDate("v.rechnungsdatum").",
                            ".$app->erp->FormatMenge('v.betrag',2).",
                            v.waehrung,
                            if(v.bezahlt,'bezahlt','offen'),
                            ".$app->erp->FormatDate("v.zahlbarbis").",
                            IF(v.skonto <> 0,".$app->erp->FormatDate("v.skontobis").",''),
                            IF(v.skonto <> 0,CONCAT(".$app->erp->FormatMenge('v.skonto',0).",'%'),''),
                            v.status,
                            ".$app->YUI->IconsSQLVerbindlichkeit().",
                            v.id FROM verbindlichkeit v
                        LEFT JOIN adresse a ON v.adresse = a.id
                        LEFT JOIN (
                            SELECT ds.parameter, COUNT(ds.objekt) datei_anzahl FROM datei_stichwoerter ds INNER JOIN datei d ON d.id = ds.datei WHERE ds.objekt='verbindlichkeit' AND d.geloescht <> 1 GROUP BY ds.parameter
                        ) d ON d.parameter = v.id
                        ";
                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM verbindlichkeit WHERE $where";
//                $groupby = "";

                // Toggle filters
                $this->app->Tpl->Add('JQUERYREADY', "$('#anhang').click( function() { fnFilterColumn1( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#wareneingang').click( function() { fnFilterColumn2( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#rechnungsfreigabe').click( function() { fnFilterColumn3( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#nichtbezahlt').click( function() { fnFilterColumn4( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#stornierte').click( function() { fnFilterColumn5( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#abgeschlossen').click( function() { fnFilterColumn6( 0 ); } );");

                for ($r = 1;$r <= 8;$r++) {
                  $this->app->Tpl->Add('JAVASCRIPT', '
                                         function fnFilterColumn' . $r . ' ( i )
                                         {
                                         if(oMoreData' . $r . $name . '==1)
                                         oMoreData' . $r . $name . ' = 0;
                                         else
                                         oMoreData' . $r . $name . ' = 1;

                                         $(\'#' . $name . '\').dataTable().fnFilter(
                                           \'\',
                                           i,
                                           0,0
                                           );
                                         }
                                         ');
                }

                $more_data1 = $this->app->Secure->GetGET("more_data1");
                if ($more_data1 == 1) {
                   $where .= " AND datei_anzahl IS NULL";
                } else {
                }

                $more_data2 = $this->app->Secure->GetGET("more_data2");
                if ($more_data2 == 1) {
                   $where .= " AND v.freigabe <> '1'";
                }
                else {
                }

                $more_data3 = $this->app->Secure->GetGET("more_data3");
                if ($more_data3 == 1) {
                   $where .= " AND v.rechnungsfreigabe <> '1'";
                }
                else {
                }

                $more_data4 = $this->app->Secure->GetGET("more_data4");
                if ($more_data4 == 1) {
                   $where .= " AND v.bezahlt <> 1";
                }
                else {
                }

                $more_data5 = $this->app->Secure->GetGET("more_data5");
                if ($more_data5 == 1) {
                }
                else {
                   $where .= " AND v.status <> 'storniert'";
                }

                $more_data6 = $this->app->Secure->GetGET("more_data6");
                if ($more_data6 == 1) {
                }
                else {
                    $where .= " AND v.status <> 'abgeschlossen'";
                }

                $this->app->YUI->DatePicker('zahlbarbis');
                $filterzahlbarbis = $this->app->YUI->TableSearchFilter($name, 7,'zahlbarbis');
                if (!empty($filterzahlbarbis)) {
                    $filterzahlbarbis = $this->app->String->Convert($filterzahlbarbis,'%1.%2.%3','%3-%2-%1');
                    $where .= " AND v.zahlbarbis <= '".$filterzahlbarbis."'";
                }

                $this->app->YUI->DatePicker('skontobis');
                $filterskontobis = $this->app->YUI->TableSearchFilter($name, 8,'skontobis');
                if (!empty($filterskontobis)) {
                    $filterskontobis = $this->app->String->Convert($filterskontobis,'%1.%2.%3','%3-%2-%1');
                    $where .= " AND v.skontobis <= '".$filterskontobis."'";
                }
                // END Toggle filters

                $moreinfo = true; // Allow drop down details
                $menucol = 1; // For moredata

                break;
            case 'verbindlichkeit_paketdistribution_list':
                $allowed['verbindlichkeit_paketdistribution_list'] = array('list');

                $id = $app->Secure->GetGET('id');
                if (empty($id)) {
                    break;
                }

                $verbindlichkeit = $app->DB->SelectArr("SELECT v.adresse, v.rechnung, b.belegnr FROM verbindlichkeit v LEFT JOIN bestellung b ON b.id = v.bestellung WHERE v.id = ".$id)[0];

                $lieferant = $verbindlichkeit['adresse'];
                $bestellnummer = $verbindlichkeit['belegnr'];
                $rechnung = $verbindlichkeit['rechnung'];

                $heading = array('',  'Paket-Nr.','Paket-Pos.', 'Bestellung', 'Lieferschein', 'Rechnung', 'Artikel-Nr.','Artikel','Bemerkung','Menge','Menge offen','Eingabe','Preis','Steuer','Sachkonto','');
                $width = array(  '1%','1%',        '1%',        '5%',         '5%',           '5%',       '5%',         '20%',    '20%',       '2%',   '1%',         '1%',     '1%',   '1%',    '1%',       '1%');

                $findcols = array('id','pa','id','belegnr','lsnr','renr','artikelnummer','name_de','bemerkung','menge','offen_menge','offen_menge','preis','steuer','sachkonto','pa');
                $searchsql = array('p.nummer', 'p.name', 'p.bemerkung');

                $alignright = array(9,10);

                $defaultorder = 1;
                $defaultorderdesc = 0;

                $offen_menge = "TRIM(IF(
                            pd.menge > COALESCE(vp.menge,0),
                            pd.menge - COALESCE(vp.menge,0),
                            0
                        ))+0";

                $auswahl = array (
                    '<input type=\"checkbox\" name=\"ids[]\" value=\"',
                    ['sql' => 'pd.id'],
                    '"/>'
                );

                $werte = array (
                    '<input type="number" name="werte[]" value="',
                    ['sql' => $offen_menge],
                    '" min="0"',
                    ' max="',
                    ['sql' => $offen_menge],
                    '"/>'
                );

                $preise = array (
                    '<input type="number" name="preise[]" step="0.0000000001" value="',
                    ['sql' => "TRIM(COALESCE(bp.preis,0))+0"],
                    '" min="0"',
                    '/>'
                );

                $artikellink = array (
                    '<a href="index.php?module=artikel&action=edit&id=',
                    ['sql' => 'art.id'],
                    '">',
                    ['sql' => 'art.nummer'],
                    '</a>'
                );

                $paketlink = array (
                    '<a href="index.php?module=wareneingang&action=distriinhalt&id=',
                    ['sql' => 'pa.id'],
                    '">',
                    ['sql' => 'pa.id'],
                    '</a>'
                );

                $where = "offen_menge > 0";

                // Toggle filters
                $this->app->Tpl->Add('JQUERYREADY', "$('#passende').click( function() { fnFilterColumn1( 0 ); } );");

                for ($r = 1;$r <= 1;$r++) {
                  $this->app->Tpl->Add('JAVASCRIPT', '
                                         function fnFilterColumn' . $r . ' ( i )
                                         {
                                         if(oMoreData' . $r . $name . '==1)
                                         oMoreData' . $r . $name . ' = 0;
                                         else
                                         oMoreData' . $r . $name . ' = 1;

                                         $(\'#' . $name . '\').dataTable().fnFilter(
                                           \'\',
                                           i,
                                           0,0
                                           );
                                         }
                                         ');
                }

                $more_data1 = $this->app->Secure->GetGET("more_data1");
                if ($more_data1 == 1) {
                    $innerwhere .= " AND ((b.belegnr LIKE '%".$bestellnummer."%' AND '".$bestellnummer."' <> '') OR (pa.renr LIKE '%".$rechnung."%' AND pa.renr <> ''))";
                } else {
                }
                // END Toggle filters

                $sql = "
                    SELECT SQL_CALC_FOUND_ROWS * FROM (
                        SELECT
                            pa.id pa_id,
                            ".$this->app->erp->ConcatSQL($auswahl)." AS auswahl,
                            ".$this->app->erp->ConcatSQL($paketlink)." pa,
                            pd.id,
                            if(b.belegnr LIKE '%".$bestellnummer."%',CONCAT('<b>',b.belegnr,'</b>'),b.belegnr) AS belegnr,
                            pa.lsnr,
                            if(pa.renr LIKE '%".$rechnung."%',CONCAT('<b>',pa.renr,'</b>'),pa.renr) AS renr,
                            ".$this->app->erp->ConcatSQL($artikellink)." AS artikelnummer,
                            art.name_de,
                            pd.bemerkung,
                            pd.menge,
                            IF(
                                pd.menge > COALESCE(vp.menge,0),
                                pd.menge - COALESCE(vp.menge,0),
                                0
                            ) offen_menge,
                            ".$this->app->erp->ConcatSQL($werte).",
                            ".$this->app->erp->ConcatSQL($preise)." AS preis,
                            if(art.umsatzsteuer = '',art.steuersatz,art.umsatzsteuer) steuer,
                            if (skart.id <> 0,
                                CONCAT(skart.sachkonto,' ',skart.beschriftung),
                                CONCAT(skadr.sachkonto,' ',skadr.beschriftung)
                            ) AS sachkonto
                        FROM
                            paketannahme pa
                        INNER JOIN paketdistribution pd ON
                            pd.paketannahme = pa.id
                        INNER JOIN artikel art ON
                            art.id = pd.artikel
                        LEFT JOIN adresse adr ON
                            adr.id = pa.adresse
                        LEFT JOIN bestellung_position bp ON
                            bp.id = pd.bestellung_position
                        LEFT JOIN bestellung b ON
                            b.id = bp.bestellung
                        LEFT JOIN(
                            SELECT
                                paketdistribution,
                                SUM(menge) AS menge
                            FROM
                                verbindlichkeit_position vp
                            GROUP BY
                                paketdistribution
                        ) vp
                        ON
                            vp.paketdistribution = pd.id
                        LEFT JOIN
                            kontorahmen skart ON skart.id = art.kontorahmen
                        LEFT JOIN
                            kontorahmen skadr ON skadr.id = adr.kontorahmen
                        WHERE pa.adresse = ".$lieferant." AND pd.vorlaeufig IS NULL".$innerwhere."
                    ) temp
                        ";

                $count = "";
//                $groupby = "";

                break;
       case 'verbindlichkeit_positionen':

                $allowed['verbindlichkeit_positionen'] = array('list');

                $id = $app->Secure->GetGET('id');
                $freigabe = $app->DB->Select("SELECT freigabe FROM verbindlichkeit WHERE id = '".$id."'");
                $rechnungsfreigabe = $app->DB->Select("SELECT rechnungsfreigabe FROM verbindlichkeit WHERE id = '".$id."'");

                $heading = array('',  'Paket-Nr.','Paket-Pos.', 'Bestellung', 'Artikel-Nr.','Artikel','Bemerkung','Menge','Preis','Steuersatz','Sachkonto');
                $width = array(  '1%','1%',       '1%' ,        '2%',         '2%',         '20%',    '20%',   '1%',   '1%',        '3%',       '1%',       '1%');

                $findcols = array('vp.id','pd.paketannahme','pd.id','b.belegnr','art.nummer','art.name_de','pd.bemerkung','vp.menge','vp.preis','vp.steuersatz',"CONCAT(skv.sachkonto,' ',skv.beschriftung)",'vp.id');
                $searchsql = array('p.nummer', 'p.name', 'p.bemerkung');

                $alignright = array(8,9,10);

                $defaultorder = 1;
                $defaultorderdesc = 0;

                if (empty($freigabe)) {
                    $menu="<table cellpadding=0 cellspacing=0><tr><td nowrap>"."<a href=\"index.php?module=verbindlichkeit&action=editpos&id=$id&posid=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=verbindlichkeit&action=deletepos&id=$id&posid=%value%\");>"."<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>"."</td></tr></table>";
                } else if (empty($rechnungsfreigabe)) {
                    $menu="<table cellpadding=0 cellspacing=0><tr><td nowrap>"."<a href=\"index.php?module=verbindlichkeit&action=editpos&id=$id&posid=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>"."</td></tr></table>";
                }
                else {
                    $deletepos = array('');
                }
                $heading[] = '';

        	    $box = "CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',vp.id,'\" />') AS `auswahl`";

               $paketlink = array (
                    '<a href="index.php?module=wareneingang&action=distriinhalt&id=',
                    ['sql' => 'pd.paketannahme'],
                    '">',
                    ['sql' => 'pd.paketannahme'],
                    '</a>'
                );

                $sql = "
                    SELECT SQL_CALC_FOUND_ROWS
                        vp.id,
                        $box,
                        ".$this->app->erp->ConcatSQL($paketlink)." pa,
                        pd.id paket_position,
                        b.belegnr,
                        art.nummer,
                        art.name_de,
                        pd.bemerkung,
                        vp.menge,
                        vp.preis,
                        vp.steuersatz,
                        CONCAT(skv.sachkonto,' ',skv.beschriftung),
                        vp.id
                    FROM
                        verbindlichkeit_position vp
                    INNER JOIN verbindlichkeit v ON
                        v.id = vp.verbindlichkeit
                    INNER JOIN paketdistribution pd ON
                        pd.id = vp.paketdistribution
                    INNER JOIN artikel art ON
                        art.id = pd.artikel
                    INNER JOIN adresse adr ON
                        adr.id = v.adresse
                    LEFT JOIN bestellung_position bp ON pd.bestellung_position = bp.id
                    LEFT JOIN bestellung b ON b.id = bp.bestellung
                    LEFT JOIN kontorahmen skv ON skv.id = vp.kontorahmen
                ";

                $where = "vp.verbindlichkeit = ".$id;

                $count = "";

                break;
        }

        $erg = false;

        foreach ($erlaubtevars as $k => $v) {
            if (isset($$v)) {
                $erg[$v] = $$v;
            }
        }
        return $erg;
    }

    function verbindlichkeit_list() {

        // Process multi action
        $submit = $this->app->Secure->GetPOST('submit');
        switch($submit) {
            case 'status_berechnen':

                $sql = "SELECT id FROM verbindlichkeit WHERE status <> 'abgeschlossen' AND status <> 'storniert'";
                $ids = $this->app->DB->SelectArr($sql);

                foreach ($ids as $verbindlichkeit) {
                    $this->verbindlichkeit_abschliessen($verbindlichkeit['id']);
                }

            break;
            case 'ausfuehren':
                $auswahl = $this->app->Secure->GetPOST('auswahl');
                $aktion = $this->app->Secure->GetPOST('sel_aktion');

                $selectedIds = [];
                if(!empty($auswahl)) {
                    foreach($auswahl as $selectedId) {
                        $selectedId = (int)$selectedId;
                        if($selectedId > 0) {
                            $selectedIds[] = $selectedId;
                        }
                    }

                    switch ($aktion) {
                        case 'freigabeeinkauf':
                            foreach ($selectedIds as $id) {
                                $result = $this->verbindlichkeit_freigabeeinkauf($id);
                                if ($result !== true) {
                                    $this->app->YUI->Message('warning',$result);
                                }
                            }
                        break;
                        case 'freigabebuchhaltung':
                            foreach ($selectedIds as $id) {
                                $result = $this->verbindlichkeit_freigabebuchhaltung($id);
                                if ($result !== true) {
                                    $this->app->YUI->Message('warning',$result);
                                }
                            }
                        break;
                        case 'bezahlt':
                            foreach ($selectedIds as $id) {
                                $result = $this->verbindlichkeit_freigabebezahlt($id);
                                if ($result !== true) {
                                   $this->app->YUI->Message('warning',$result);
                                }
                            }
                        break;
                        case 'drucken':
                            $drucker = $this->app->Secure->GetPOST('seldrucker');
                            foreach ($selectedIds as $id) {
                                $file_attachments = $this->app->erp->GetDateiSubjektObjekt('%','verbindlichkeit',$id);
                                if (!empty($file_attachments)) {
                                    foreach ($file_attachments as $file_attachment) {
                                        if ($this->app->erp->GetDateiEndung($file_attachment) == 'pdf') {
                                            $file_contents = $this->app->erp->GetDatei($file_attachment);
                                            $verbindlichkeit = $this->app->DB->SelectRow("SELECT DATE_FORMAT(rechnungsdatum, '%Y%m%d') rechnungsdatum, belegnr FROM verbindlichkeit WHERE id = ".$id." LIMIT 1");
                                            $file_name = $verbindlichkeit['rechnungsdatum']."_VB".$verbindlichkeit['belegnr'].".pdf";
                                            $file_path = rtrim($this->app->erp->GetTMP(),'/')."/".$file_name;
                                            $handle = fopen ($file_path, "wb");
                                            if ($handle)
                                            {
                                                fwrite($handle, $file_contents);
                                                fclose($handle);
                                                $this->app->printer->Drucken($drucker,$file_path);
                                            } else {
                                                $this->app->YUI->Message('error',"Drucken fehlgeschlagen!");
                                            }
                                        }
                                    }
                                }
                            }
                        break;
                    }
                }
            break;
        }

        $this->app->erp->MenuEintrag("index.php?module=verbindlichkeit&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=verbindlichkeit&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'verbindlichkeit_list', "show", "", "", basename(__FILE__), __CLASS__);


        if($this->app->erp->RechteVorhanden('verbindlichkeit', 'freigabeeinkauf')){
            $this->app->Tpl->Set('MANUELLFREIGABEEINKAUF', '<option value="freigabeeinkauf">{|freigeben (Einkauf)|}</option>');
        }

        if($this->app->erp->RechteVorhanden('verbindlichkeit', 'freigabebuchhaltung')){
            $this->app->Tpl->Set('MANUELLFREIGABEBUCHHALTUNG', '<option value="freigabebuchhaltung">{|freigeben (Buchhaltung)|}</option>');
        }

        if($this->app->erp->RechteVorhanden('verbindlichkeit', 'freigabebezahlt')){
            $this->app->Tpl->Set('ALSBEZAHLTMARKIEREN', '<option value="bezahlt">{|als bezahlt markieren|}</option>');
        }

        $this->app->User->SetParameter('table_verbindlichkeit_list_zahlbarbis', '');
        $this->app->User->SetParameter('table_verbindlichkeit_list_skontobis', '');

        $this->app->Tpl->Set('SELDRUCKER', $this->app->erp->GetSelectDrucker());

        $this->app->Tpl->Parse('PAGE', "verbindlichkeit_list.tpl");
    }

    public function verbindlichkeit_delete() {
        $id = (int) $this->app->Secure->GetGET('id');

        $this->app->DB->Delete("UPDATE `verbindlichkeit` SET status='storniert' WHERE `id` = '{$id}'");
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde storniert.</div>");

        $this->verbindlichkeit_list();
    }

    public function verbindlichkeit_deletepos() {
        $posid = (int) $this->app->Secure->GetGET('posid');
        $id = (int) $this->app->Secure->GetGET('id');
        $verbindlichkeit = $this->app->DB->Select("SELECT verbindlichkeit FROM verbindlichkeit_position WHERE id ='{$posid}'");
        $this->app->DB->Delete("DELETE vp FROM verbindlichkeit_position vp INNER JOIN verbindlichkeit v ON v.id = vp.verbindlichkeit WHERE vp.id = '{$posid}' AND v.freigabe <> 1");
        header("Location: index.php?module=verbindlichkeit&action=edit&id=$id#tabs-2");
    }

    /*
     * Edit verbindlichkeit item
     * If id is empty, create a new one
     */

    function verbindlichkeit_edit($einkauf_automatik_aus = false) {
        $id = $this->app->Secure->GetGET('id');

        // Check if other users are editing this id
        if($this->app->erp->DisableModul('verbindlichkeit',$id))
        {
          return;
        }

        $this->app->Tpl->Set('ID', $id);

        $this->verbindlichkeit_menu($id);

        $input = $this->GetInput();
        $submit = $this->app->Secure->GetPOST('submit');

        if (empty($id)) {
            // New item
            $id = 'NULL';
            $input['status'] = 'angelegt';
        }

        if (!empty($submit)) {
            $einkauf_automatik_aus = false;
        }

        switch($submit)
        {
            case 'speichern':
                   // Write to database
                // Add checks here

                $freigabe = $this->app->DB->SelectArr("SELECT rechnungsfreigabe, freigabe FROM verbindlichkeit WHERE id =".$id)[0];
                if ($freigabe['rechnungsfreigabe'] || $freigabe['freigabe']) {
                    $internebemerkung = $input['internebemerkung'];
                    $projekt = $input['projekt'];
                    $kostenstelle = $input['kostenstelle'];
                    unset($input);
                    $input['internebemerkung'] = $internebemerkung;
                    $input['projekt'] = $this->app->erp->ReplaceProjekt(true,$projekt,true);
                    $input['kostenstelle'] = $this->app->DB->Select("SELECT id FROM kostenstellen WHERE nummer = '".$kostenstelle."'");
                } else {

                    $input['rechnungsdatum'] = $this->app->erp->ReplaceDatum(true,$input['rechnungsdatum'],true); // Parameters: Target db?, value, from form?
                    $input['eingangsdatum'] = $this->app->erp->ReplaceDatum(true,$input['eingangsdatum'],true); // Parameters: Target db?, value, from form?
                    $input['skontobis'] = $this->app->erp->ReplaceDatum(true,$input['skontobis'],true); // Parameters: Target db?, value, from form?
                    $input['zahlbarbis'] = $this->app->erp->ReplaceDatum(true,$input['zahlbarbis'],true); // Parameters: Target db?, value, from form?

                    if($input['zahlbarbis_tage'] != '') {
                        $zahlbarbis = date_create_from_format('Y-m-d', $input['rechnungsdatum']);
                        date_add($zahlbarbis,date_interval_create_from_date_string($input['zahlbarbis_tage']." days"));
                        $input['zahlbarbis'] = date_format($zahlbarbis, 'Y-m-d');
                    }
                    unset($input['zahlbarbis_tage']);
                    if($input['skontobis_tage'] != '') {
                        $skontobis = date_create_from_format('Y-m-d', $input['rechnungsdatum']);
                        date_add($skontobis,date_interval_create_from_date_string($input['skontobis_tage']." days"));
                        $input['skontobis'] = date_format($skontobis, 'Y-m-d');
                    }
                    unset($input['skontobis_tage']);

                    $input['adresse'] = $this->app->erp->ReplaceLieferantennummer(true,$input['adresse'],true); // Parameters: Target db?, value, from form?
                    $input['projekt'] = $this->app->erp->ReplaceProjekt(true,$input['projekt'],true);
                    $input['kostenstelle'] = $this->app->DB->Select("SELECT id FROM kostenstellen WHERE nummer = '".$input['kostenstelle']."'");
                    $input['bestellung'] = $this->app->erp->ReplaceBestellung(true,$input['bestellung'],true);
                    if(empty($input['projekt']) && !empty($input['adresse'])) {
                        $input['projekt'] = $this->app->erp->GetCreateProjekt($input['adresse']);
                    }

                    if (!empty($input['adresse'])) {
                        $adressdaten = $this->app->DB->SelectRow("
                            SELECT
                                zahlungszieltagelieferant,
                                zahlungszieltageskontolieferant,
                                zahlungszielskontolieferant,
                                ust_befreit
                            FROM adresse WHERE id = ".$input['adresse']
                        );


                        if ($input['zahlbarbis'] == '0000-00-00' && $input['rechnungsdatum'] != '0000-00-00' && !empty($adressdaten['zahlungszieltagelieferant'])) {
                            $input['zahlbarbis'] = date('Y-m-d',strtotime($input['rechnungsdatum']." + ".$adressdaten['zahlungszieltagelieferant']." days"));
                        }
                        if ($input['skontobis'] == '0000-00-00' && $input['rechnungsdatum'] != '0000-00-00' && !empty($adressdaten['zahlungszieltageskontolieferant'])) {
                            $input['skontobis'] = date('Y-m-d',strtotime($input['rechnungsdatum']." + ".$adressdaten['zahlungszieltageskontolieferant']." days"));
                            $input['skonto'] = $adressdaten['zahlungszielskontolieferant'];
                        }

                    }

                }

                $columns = "id, ";
                $values = "$id, ";
                $update = "";

                $fix = "";

                foreach ($input as $key => $value) {
                    $columns = $columns.$fix.$key;
                    $values = $values.$fix."'".$value."'";
                    $update = $update.$fix.$key." = '$value'";

                    $fix = ", ";
                }

    //            echo($columns."<br>");
    //            echo($values."<br>");
    //            echo($update."<br>");

                $sql = "INSERT INTO verbindlichkeit (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//               echo($sql);

                $this->app->DB->Update($sql);

                if ($id == 'NULL') {
                    $id = $this->app->DB->GetInsertID();
                    $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                    header("Location: index.php?module=verbindlichkeit&action=edit&id=$id&msg=$msg");
                } else {
                    $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
                }
            break;
            case 'positionen_hinzufuegen':

                $freigabe = $this->app->DB->SelectArr("SELECT rechnungsfreigabe, freigabe FROM verbindlichkeit WHERE id =".$id)[0];
                if ($freigabe['rechnungsfreigabe'] || $freigabe['freigabe']) {
                    break;
                }

               // Process multi action
                $ids = $this->app->Secure->GetPOST('ids');
                $werte = $this->app->Secure->GetPOST('werte');
                $preise = $this->app->Secure->GetPOST('preise');

                $bruttoeingabe = $this->app->Secure->GetPOST('bruttoeingabe');

                foreach ($ids as $key => $paketdistribution) {
                    $menge = $werte[$key];

                    if ($menge <= 0) {
                        continue;
                    }

                    // Check available number
                    $sql = "
                        SELECT
                            IF(
                                pd.menge > COALESCE(vp.menge,0),
                                pd.menge - COALESCE(vp.menge,0),
                                0
                            ) offen_menge
                        FROM
                            paketdistribution pd
                        LEFT JOIN(
                            SELECT
                                paketdistribution,
                                SUM(menge) AS menge
                            FROM
                                verbindlichkeit_position vp
                            GROUP BY
                                paketdistribution
                        ) vp
                        ON
                            vp.paketdistribution = pd.id
                        WHERE pd.id = ".$paketdistribution."
                        ";
                    $offen_menge = $this->app->DB->Select($sql);

                    if ($offen_menge == 0) {
                        continue;
                    }

                    if ($menge > $offen_menge) {
                        $menge = $offen_menge;
                    }

                    $preis = $preise[$key];
                    $sql = "SELECT
                                a.id,
                                a.umsatzsteuer,
                                a.steuersatz,
                                COALESCE(if (skart.id <> 0,skart.id,skadr.id),0) AS kontorahmen
                            FROM
                                paketdistribution pd
                            INNER JOIN
                                paketannahme pa ON pa.id = pd.paketannahme
                            INNER JOIN
                                artikel a ON a.id = pd.artikel
                            INNER JOIN
                                adresse adr ON pa.adresse = adr.id
                            LEFT JOIN
                                kontorahmen skart ON skart.id = a.kontorahmen
                            LEFT JOIN
                                kontorahmen skadr ON skadr.id = adr.kontorahmen
                            WHERE pd.id =".$paketdistribution;

                    $artikel = $this->app->DB->SelectRow($sql);

                    $einartikel = $artikel['id'];
                    $umsatzsteuer = $artikel['umsatzsteuer'];
                    $kontorahmen = $artikel['kontorahmen'];

                    if(empty($umsatzsteuer) && is_numeric($artikel['steuersatz'])) {
                        $steuersatz = $artikel['steuersatz'];
                    } else {
                        $steuersatz = $this->get_steuersatz($umsatzsteuer,$id);
                    }

                    if ($bruttoeingabe) {
                        $preis = $preis / (1+($steuersatz/100));
                    }
                    $sql = "INSERT INTO verbindlichkeit_position (verbindlichkeit,paketdistribution, menge, preis, steuersatz, artikel, kontorahmen) VALUES ($id, $paketdistribution, $menge, $preis, $steuersatz, $einartikel, $kontorahmen)";
                    $this->app->DB->Insert($sql);

                }
            break;
            case 'positionen_entfernen':

                $freigabe = $this->app->DB->SelectArr("SELECT rechnungsfreigabe, freigabe FROM verbindlichkeit WHERE id =".$id)[0];
                if ($freigabe['rechnungsfreigabe'] || $freigabe['freigabe']) {
                    break;
                }
                // Process multi action
                $ids = $this->app->Secure->GetPOST('auswahl');
                if (!is_array($ids)) {
                    break;
                }
                $this->app->DB->Delete("DELETE vp FROM verbindlichkeit_position vp INNER JOIN verbindlichkeit v ON v.id = vp.verbindlichkeit WHERE vp.id IN (".implode(',',$ids).") AND v.freigabe <> 1");

            break;
            case 'positionen_steuersatz_zu_netto':

                $freigabe = $this->app->DB->SelectArr("SELECT rechnungsfreigabe, freigabe FROM verbindlichkeit WHERE id =".$id)[0];
                if ($freigabe['rechnungsfreigabe'] || $freigabe['freigabe']) {
                    break;
                }
                // Process multi action
                $ids = $this->app->Secure->GetPOST('auswahl');
                if (!is_array($ids)) {
                    break;
                }

                foreach ($ids as $posid) {
                    $tmpsteuersatz = null;
                    $tmpsteuertext = null;
                    $erloes = null;
                    $this->app->erp->GetSteuerPosition("verbindlichkeit",$posid,$tmpsteuersatz,$tmpsteuertext,$erloes);

                    $faktor = 1+($tmpsteuersatz/100);

                    $sql = "UPDATE verbindlichkeit_position SET preis = preis / ".$faktor." WHERE id = $posid";
                    $this->app->DB->Update($sql);
                }

            break;
            case 'positionen_kontorahmen_setzen':
                $freigabe = $this->app->DB->SelectArr("SELECT rechnungsfreigabe, freigabe FROM verbindlichkeit WHERE id =".$id)[0];
                if ($freigabe['rechnungsfreigabe']) {
                    break;
                }
                // Process multi action
                $ids = $this->app->Secure->GetPOST('auswahl');
                if (!is_array($ids)) {
                    break;
                }

                $positionen_sachkonto = $this->app->Secure->GetPOST('positionen_sachkonto');
                $positionen_kontorahmen = $this->app->erp->ReplaceKontorahmen(true,$positionen_sachkonto,false);

                foreach ($ids as $posid) {
                    $sql = "UPDATE verbindlichkeit_position SET kontorahmen = '".$positionen_kontorahmen."' WHERE id =".$posid;
                    $this->app->DB->Update($sql);
                }
            break;
        }


        // Load values again from database
	    $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',v.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS v.id,
                                                     $dropnbox,
                                                     v.belegnr,
                                                     v.status_beleg,
                                                     v.schreibschutz,
                                                     v.rechnung,
                                                     v.zahlbarbis,
                                                     ".$this->app->erp->FormatMengeBetrag('v.betrag')." AS betrag,
                                                     v.umsatzsteuer,
                                                     v.ustid,
                                                     v.summenormal,
                                                     v.summeermaessigt,
                                                     v.summesatz3,
                                                     v.summesatz4,
                                                     v.steuersatzname3,
                                                     v.steuersatzname4,
                                                     v.skonto,
                                                     v.skontobis,
                                                     v.skontofestsetzen,
                                                     v.freigabe,
                                                     v.freigabemitarbeiter,
                                                     v.bestellung,
                                                     v.adresse,
                                                     v.projekt,
                                                     v.teilprojekt,
                                                     v.auftrag,
                                                     v.status,
                                                     v.bezahlt,
                                                     v.kontoauszuege,
                                                     v.firma,
                                                     v.logdatei,
                                                     v.waehrung,
                                                     v.zahlungsweise,
                                                     v.eingangsdatum,
                                                     v.buha_konto1,
                                                     v.buha_belegfeld1,
                                                     v.buha_betrag1,
                                                     v.buha_konto2,
                                                     v.buha_belegfeld2,
                                                     v.buha_betrag2,
                                                     v.buha_konto3,
                                                     v.buha_belegfeld3,
                                                     v.buha_betrag3,
                                                     v.buha_konto4,
                                                     v.buha_belegfeld4,
                                                     v.buha_betrag4,
                                                     v.buha_konto5,
                                                     v.buha_belegfeld5,
                                                     v.buha_betrag5,
                                                     v.rechnungsdatum,
                                                     v.rechnungsfreigabe,
                                                     v.kostenstelle,
                                                     v.beschreibung,
                                                     v.sachkonto,
                                                     v.art,
                                                     v.verwendungszweck,
                                                     v.dta_datei,
                                                     v.frachtkosten,
                                                     v.internebemerkung,
                                                     v.ustnormal,
                                                     v.ustermaessigt,
                                                     v.uststuer3,
                                                     v.uststuer4,
                                                     v.betragbezahlt,
                                                     v.bezahltam,
                                                     v.klaerfall,
                                                     v.klaergrund,
                                                     v.skonto_erhalten,
                                                     v.kurs,
                                                     v.sprache,
                                                     v.id,
                                                     a.lieferantennummer,
                                                     a.name AS adresse_name FROM verbindlichkeit v LEFT JOIN adresse a ON a.id = v.adresse"." WHERE v.id=$id");

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);
        }

        if (!empty($result[0])) {
            $verbindlichkeit_from_db = $result[0];
        }

        // Check  positions
        $pos_check = $this->check_positions($verbindlichkeit_from_db['id'],$verbindlichkeit_from_db['betrag']);

        $this->app->Tpl->Set('BETRAGNETTO', $pos_check['betrag_netto']);
        $this->app->Tpl->Set('BETRAGBRUTTOPOS', $pos_check['betrag_brutto']);

        if (empty($pos_check['rundungsdifferenz'])) {
            $this->app->Tpl->Set('RUNDUNGSDIFFERENZICONHIDDEN', 'hidden');
        } else {
            $this->app->Tpl->Set('RUNDUNGSDIFFERENZ', $pos_check['rundungsdifferenz']);
        }

        if ($pos_check['pos_ok']) {
            if (!$verbindlichkeit_from_db['freigabe'] && !$einkauf_automatik_aus) {
                if ($this->verbindlichkeit_freigabeeinkauf($id,"Verbindlichkeit automatisch freigegeben (Einkauf)") === true) {
                    $this->app->YUI->Message('success',"Verbindlichkeit automatisch freigegeben (Einkauf)");
                    $verbindlichkeit_from_db['freigabe'] = 1;
                } else {
                    $this->app->YUI->Message('warning','Waren-/Leistungspr&uuml;fung (Einkauf) nicht abgeschlossen');
                }
            }
            $this->app->Tpl->Set('POSITIONENMESSAGE', '<div class="success">Positionen vollst&auml;ndig</div>');

            if ($verbindlichkeit_from_db['status'] != 'abgeschlossen' && $verbindlichkeit_from_db['status'] != 'storniert') {
                $this->verbindlichkeit_abschliessen($id);
            }

        } else {
            $this->app->Tpl->Set('
                                    POSITIONENMESSAGE', '<div class="warning">Positionen nicht vollst&auml;ndig. Bruttobetrag '.
                                    $verbindlichkeit_from_db['betrag'].
                                    ', Summe Positionen (brutto) '.
                                    $pos_check['betrag_brutto'].
                                    ', Differenz '.
                                    round($pos_check['betrag_brutto']-$verbindlichkeit_from_db['betrag'],2).
                                    '</div>'
                                );
            if ($verbindlichkeit_from_db['freigabe']) {
                $this->app->DB->Update("UPDATE verbindlichkeit SET freigabe = 0 WHERE id = ".$id);
                $verbindlichkeit_from_db['freigabe'] = 0;
                $this->app->YUI->Message('warning',"Verbindlichkeit r&uuml;ckgesetzt (Einkauf)");
            }
        }

        /*
         * Add displayed items later
         *

        $this->app->Tpl->Add('KURZUEBERSCHRIFT2', $email);
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);
        $this->app->YUI->AutoComplete("artikel", "artikelnummer");

         */

        $this->app->Tpl->Set('FREIGABEEINKAUFHIDDEN','hidden'); // prevent manual setting

        if (empty($verbindlichkeit_from_db['adresse']) || $verbindlichkeit_from_db['status'] == 'angelegt') {
            $this->app->Tpl->Set('FREIGABEBUCHHALTUNGHIDDEN','hidden');
            $this->app->Tpl->Set('FREIGABEBEZAHLTHIDDEN','hidden');
            $this->app->Tpl->Set('POSITIONHINZUFUEGENHIDDEN','hidden');
            $this->app->Tpl->Set('POSITIONENHIDDEN','hidden');
        }

        if ($verbindlichkeit_from_db['freigabe']) {
            $this->app->Tpl->Set('FREIGABEEINKAUFHIDDEN','hidden');
            $this->app->Tpl->Set('EINKAUFINFOHIDDEN','hidden');
            $this->app->Tpl->Set('SAVEDISABLED','disabled');
            $this->app->Tpl->Set('POSITIONHINZUFUEGENHIDDEN','hidden');
        } else {
            $this->app->Tpl->Set('RUECKSETZENEINKAUFHIDDEN','hidden');
            $this->app->Tpl->Set('FREIGABEBUCHHALTUNGHIDDEN','hidden');
        }

        if (!empty($positionen)) {
            $this->app->Tpl->Set('FREIGABEEINKAUFHIDDEN','hidden');
        }

        if ($verbindlichkeit_from_db['rechnungsfreigabe']) {
            $this->app->Tpl->Set('FREIGABEBUCHHALTUNGHIDDEN','hidden');
            $this->app->Tpl->Set('RUECKSETZENEINKAUFHIDDEN','hidden');
            $this->app->Tpl->Set('SACHKONTOCHANGEHIDDEN','hidden');
        } else {
            $this->app->Tpl->Set('RUECKSETZENBUCHHALTUNGHIDDEN','hidden');
        }
        if ($verbindlichkeit_from_db['bezahlt'] == '1') {
            $this->app->Tpl->Set('FREIGABEBEZAHLTHIDDEN','hidden');
        } else {
            $this->app->Tpl->Set('RUECKSETZENBEZAHLTHIDDEN','hidden');
        }

      	$this->app->Tpl->Set('WARENEINGANGCHECKED', $verbindlichkeit_from_db['freigabe']==1?"checked":"");
      	$this->app->Tpl->Set('RECHNUNGSFREIGABECHECKED', $verbindlichkeit_from_db['rechnungsfreigabe']==1?"checked":"");
      	$this->app->Tpl->Set('BEZAHLTCHECKED', $verbindlichkeit_from_db['bezahlt']==1?"checked":"");

        $this->app->Tpl->Set('RECHNUNGSDATUM',$this->app->erp->ReplaceDatum(false,$verbindlichkeit_from_db['rechnungsdatum'],false));
        $this->app->YUI->DatePicker("rechnungsdatum");
        $this->app->Tpl->Set('EINGANGSDATUM',$this->app->erp->ReplaceDatum(false,$verbindlichkeit_from_db['eingangsdatum'],false));
        $this->app->YUI->DatePicker("eingangsdatum");
        $this->app->Tpl->Set('SKONTOBIS',$this->app->erp->ReplaceDatum(false,$verbindlichkeit_from_db['skontobis'],false));
        $this->app->YUI->DatePicker("skontobis");
        $this->app->Tpl->Set('ZAHLBARBIS',$this->app->erp->ReplaceDatum(false,$verbindlichkeit_from_db['zahlbarbis'],false));
        $this->app->YUI->DatePicker("zahlbarbis");

    	$sql = "SELECT
    	            ".$this->app->YUI->IconsSQLVerbindlichkeit() . " AS `icons`
	                FROM verbindlichkeit v
    	            LEFT JOIN (
                        SELECT ds.parameter, COUNT(ds.objekt) datei_anzahl FROM datei_stichwoerter ds INNER JOIN datei d ON d.id = ds.datei WHERE ds.objekt='verbindlichkeit' AND d.geloescht <> 1 GROUP BY ds.parameter
                    ) d ON d.parameter = v.id
                    WHERE id=$id";
	    $icons = $this->app->DB->SelectArr($sql);
        $this->app->Tpl->Add('STATUSICONS',  $icons[0]['icons']);

        $this->app->YUI->DatePicker("rechnungsdatum");
        $this->app->YUI->AutoComplete("adresse", "lieferant");
        $this->app->YUI->AutoComplete("projekt", "projektname", 1);
        $this->app->Tpl->Set('PROJEKT',$this->app->erp->ReplaceProjekt(false,$verbindlichkeit_from_db['projekt'],false));
        $this->app->YUI->AutoComplete("kostenstelle", "kostenstelle", 1);
        $this->app->Tpl->Set('KOSTENSTELLE',$this->app->DB->SELECT("SELECT nummer FROM kostenstellen WHERE id = '".$verbindlichkeit_from_db['kostenstelle']."'"));

        $waehrungenselect = $this->app->erp->GetSelect($this->app->erp->GetWaehrung(), $verbindlichkeit_from_db['waehrung']);
        $this->app->Tpl->Set('WAEHRUNGSELECT', $waehrungenselect);

        $this->app->Tpl->Set('ADRESSE_ID', $verbindlichkeit_from_db['adresse']);

        $this->app->Tpl->Set('ADRESSE', $this->app->erp->ReplaceLieferantennummer(false,$verbindlichkeit_from_db['adresse'],false)); // Convert ID to form display

        $this->app->Tpl->Set('BESTELLUNG',$this->app->erp->ReplaceBestellung(false,$verbindlichkeit_from_db['bestellung'],false));
        $this->app->YUI->AutoComplete("bestellung", "lieferantenbestellung",0,"&adresse=".$verbindlichkeit_from_db['adresse']);

        $this->app->YUI->CkEditor("internebemerkung");

        $anzahldateien = $this->app->erp->AnzahlDateien("verbindlichkeit",$id);
        if ($anzahldateien > 0) {
            $file = urlencode("../../../../index.php?module=verbindlichkeit&action=inlinepdf&id=$id");
            $iframe = "<iframe width=\"100%\" height=\"100%\" style=\"height:calc(100vh - 110px)\" src=\"./js/production/generic/web/viewer.html?file=$file\"></iframe>";
            $this->app->Tpl->Set('INLINEPDF', $iframe);
        } else {
            $this->app->Tpl->Set('INLINEPDF', 'Keine Dateien vorhanden.');
        }

        if (empty($verbindlichkeit_from_db['freigabe'])) {
            $this->app->YUI->TableSearch('PAKETDISTRIBUTION', 'verbindlichkeit_paketdistribution_list', "show", "", "", basename(__FILE__), __CLASS__);
        }

        if (!empty($verbindlichkeit_from_db)) {
            // -- POSITIONEN
            $this->app->YUI->AutoComplete("positionen_sachkonto", "sachkonto", 1);
            $this->app->YUI->TableSearch('POSITIONEN', 'verbindlichkeit_positionen', "show", "", "", basename(__FILE__), __CLASS__);
            $this->app->Tpl->Parse('POSITIONENTAB', "verbindlichkeit_positionen.tpl");
            // -- POSITIONEN

            $this->verbindlichkeit_minidetail('MINIDETAIL',false);
        }

        $this->app->Tpl->Parse('PAGE', "verbindlichkeit_edit.tpl");

    }

   function verbindlichkeit_editpos() {
        $id = $this->app->Secure->GetGET('id');
        $posid = $this->app->Secure->GetGET('posid');

        $this->app->Tpl->Set('ID', $id);
        $this->app->erp->MenuEintrag("index.php?module=verbindlichkeit&action=edit&id=$id#tabs-2", "Zur&uuml;ck");

        $sachkonto = $this->app->Secure->GetPOST('sachkonto');
        $menge = $this->app->Secure->GetPOST('menge');
        $preis = $this->app->Secure->GetPOST('preis');
        $steuersatz = $this->app->Secure->GetPOST('steuersatz');

        $kontorahmen = $this->app->erp->ReplaceKontorahmen(true,$sachkonto,false);
        if ($menge < 0) {
            $menge = 0;
        }
        if ($preis < 0) {
            $preis = 0;
        }
        if ($steuersatz < 0) {
            $steuersatz = 0;
        }
        $submit = $this->app->Secure->GetPOST('submit');

        $freigabe = $this->app->DB->SelectArr("SELECT rechnungsfreigabe, freigabe FROM verbindlichkeit WHERE id =".$id)[0];
        if ($freigabe['rechnungsfreigabe'] && $freigabe['freigabe']) {
            $this->app->Tpl->Set('SAVEDISABLED','disabled');
            $this->app->Tpl->Set('SACHKONTOSAVEDISABLED','disabled');
        } else if ($freigabe['freigabe']) {
            $this->app->Tpl->Set('SAVEDISABLED','disabled');
            if ($submit != '')
            {
                $sql = "
                    UPDATE verbindlichkeit_position SET
                        kontorahmen = '$kontorahmen'
                    WHERE id = ".$posid."
                ";
                $this->app->DB->Update($sql);
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
                header("Location: index.php?module=verbindlichkeit&action=edit&id=$id&msg=$msg#tabs-2");
            }
        } else {
            if ($submit != '')
            {
                $sql = "
                    UPDATE verbindlichkeit_position SET
                        menge = '$menge',
                        preis = '$preis',
                        steuersatz = '$steuersatz',
                        kontorahmen = '$kontorahmen'
                    WHERE id = ".$posid."
                ";
                $this->app->DB->Update($sql);

                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
                header("Location: index.php?module=verbindlichkeit&action=edit&id=$id&msg=$msg#tabs-2");
            }
        }

        // Load values again from database
	    $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',v.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS v.id, $dropnbox, v.steuersatz, v.preis, v.menge, v.kontorahmen, v.id FROM verbindlichkeit_position v"." WHERE id=$posid");

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);
        }

        if (!empty($result)) {
            $verbindlichkeit_position_from_db = $result[0];
        } else {
            return;
        }

        /*
         * Add displayed items later
         *

        $this->app->Tpl->Add('KURZUEBERSCHRIFT2', $email);
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);

        $this->app->YUI->AutoComplete("artikel", "artikelnummer");

         */

        $this->app->YUI->AutoComplete("sachkonto", "sachkonto", 1);
        $this->app->Tpl->Set('SACHKONTO', $this->app->erp->ReplaceKontorahmen(false,$verbindlichkeit_position_from_db['kontorahmen'],false));

        $this->app->Tpl->Parse('PAGE', "verbindlichkeit_position_edit.tpl");
    }


    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
	    $input['adresse'] = $this->app->Secure->GetPOST('adresse');
	    $input['rechnung'] = $this->app->Secure->GetPOST('rechnung');
	    $input['zahlbarbis'] = $this->app->Secure->GetPOST('zahlbarbis');
	    $input['zahlbarbis_tage'] = $this->app->Secure->GetPOST('zahlbarbis_tage');
	    $input['betrag'] = $this->app->Secure->GetPOST('betrag');
	    $input['waehrung'] = $this->app->Secure->GetPOST('waehrung');
	    $input['skonto'] = $this->app->Secure->GetPOST('skonto');
	    $input['skontobis'] = $this->app->Secure->GetPOST('skontobis');
	    $input['skontobis_tage'] = $this->app->Secure->GetPOST('skontobis_tage');
	    $input['projekt'] = $this->app->Secure->GetPOST('projekt');
	    $input['zahlungsweise'] = $this->app->Secure->GetPOST('zahlungsweise');
	    $input['eingangsdatum'] = $this->app->Secure->GetPOST('eingangsdatum');
	    $input['rechnungsdatum'] = $this->app->Secure->GetPOST('rechnungsdatum');
        $input['bestellung'] = $this->app->Secure->GetPOST('bestellung');
	    $input['kostenstelle'] = $this->app->Secure->GetPOST('kostenstelle');
	    $input['internebemerkung'] = $this->app->Secure->GetPOST('internebemerkung');
        return $input;
    }

    function verbindlichkeit_menu($id) {

        $this->app->erp->MenuEintrag("index.php?module=verbindlichkeit&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=verbindlichkeit&action=list", "Zur&uuml;ck zur &Uuml;bersicht");

        $anzahldateien = $this->app->erp->AnzahlDateien("verbindlichkeit",$id);
        if ($anzahldateien > 0) {
            $anzahldateien = " (".$anzahldateien.")";
        } else {
            $anzahldateien="";
        }

        if ($id != 'NULL') {
            $this->app->erp->MenuEintrag("index.php?module=verbindlichkeit&action=dateien&id=$id", "Dateien".$anzahldateien);
        }

        $invoiceArr = $this->app->DB->SelectRow("SELECT v.belegnr, a.name, v.status, schreibschutz FROM verbindlichkeit v LEFT JOIN adresse a ON v.adresse = a.id WHERE v.id='$id' LIMIT 1");
        $belegnr = $invoiceArr['belegnr'];
        $name = $invoiceArr['name'];
        if($belegnr=='0' || $belegnr=='') {
            $belegnr ='(Entwurf)';
        }
        $this->app->Tpl->Set('KURZUEBERSCHRIFT2',"$name Verbindlichkeit $belegnr");

        if ($invoiceArr['status'] === 'angelegt' || empty($invoiceArr['status'])) {
            $this->app->erp->MenuEintrag("index.php?module=verbindlichkeit&action=freigabe&id=$id",'Freigabe');
        }
    }

    function verbindlichkeit_dateien()
    {
        $id = $this->app->Secure->GetGET("id");
        $this->verbindlichkeit_menu($id);
        $this->app->Tpl->Add('UEBERSCHRIFT'," (Dateien)");
        $this->app->YUI->DateiUpload('PAGE',"verbindlichkeit",$id);
    }

    function verbindlichkeit_inlinepdf() {
        $id = $this->app->Secure->GetGET('id');

        $file_attachments = $this->app->erp->GetDateiSubjektObjekt('%','verbindlichkeit',$id);

        if (!empty($file_attachments)) {

            // Try to merge all PDFs
            $file_paths = array();
            foreach ($file_attachments as $file_attachment) {
                if ($this->app->erp->GetDateiEndung($file_attachment) == 'pdf') {
                    $file_paths[] = $this->app->erp->GetDateiPfad($file_attachment);
                }
            }
            $pdfMerger = $this->app->Container->get('PdfMerger');
            $mergeOutputPath = realpath($this->app->erp->GetTMP()) . '/' . uniqid('sammelpdf_', true) . '.pdf';
            try {
                $pdfMerger->merge($file_paths, $mergeOutputPath);
                header('Content-type:application/pdf');
                header('Content-Disposition: attachment;filename='.md5(microtime(true)).'.pdf');
                readfile($mergeOutputPath);
                $this->app->ExitXentral();
            } catch (Exception $exception) {
                // Just the first PDF
                foreach ($file_attachments as $file_attachment) {
                    if ($this->app->erp->GetDateiEndung($file_attachment) == 'pdf') {
                        $file_contents = $this->app->erp->GetDatei($file_attachment);
                        header('Content-type:application/pdf');
                        header('Content-Disposition: attachment;filename=verbindlichkeit_'.$id.'.pdf');
                        echo($file_contents);
                        $this->app->ExitXentral();
                    }
                }
            }
        }
        $this->app->ExitXentral();
    }

    function verbindlichkeit_freigabe()
    {
        $id = $this->app->Secure->GetGET('id');
        $this->app->erp->BelegFreigabe('verbindlichkeit',$id);
        $this->app->erp->BelegProtokoll("verbindlichkeit",$id,"Verbindlichkeit freigegeben");
        $this->verbindlichkeit_edit();
    }

    // Returns true or error message
    function verbindlichkeit_freigabeeinkauf($id = null, $text = null)
    {
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }

        $error = false;

        if (!$this->verbindlichkeit_is_freigegeben($id)) {
            if ($gotoedit) {
                $this->app->YUI->Message('warning','Verbindlichkeit nicht freigebeben');
                $error = true;
            } else {
                return('Verbindlichkeit nicht freigebeben '.$this->verbindlichkeit_get_belegnr($id));
            }
        }

        // Check wareneingang status
        $sql = "SELECT
                    pa.id
                FROM verbindlichkeit_position vp
                INNER JOIN paketdistribution pd ON pd.id = vp.paketdistribution
                INNER JOIN paketannahme pa ON pa.id = pd.paketannahme
                WHERE
                    verbindlichkeit='$id'
                AND
                    pa.status = 'abgeschlossen'
                ";

        $check = $this->app->DB->SelectArr($sql);

        if (empty($check)) {
            if ($gotoedit) {
                $this->app->YUI->Message('warning','Waren-/Leistungspr&uuml;fung (Einkauf) nicht abgeschlossen');
            } else {
                return('Waren-/Leistungspr&uuml;fung (Einkauf) nicht abgeschlossen '.$this->verbindlichkeit_get_belegnr($id));
            }
        } else {
            $sql = "UPDATE verbindlichkeit SET freigabe = 1 WHERE id=".$id;
            $this->app->DB->Update($sql);

            if (!$text) {
                $text = "Verbindlichkeit freigegeben (Einkauf)";
            }
            $this->app->erp->BelegProtokoll("verbindlichkeit",$id,$text);
        }
        if ($gotoedit) {
            $this->verbindlichkeit_edit();
        }
        else {
            return(true);
        }
    }

    // Returns true or error message
    function verbindlichkeit_freigabebuchhaltung($id = null)
    {
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }

        $error = false;

        if (!$this->verbindlichkeit_is_freigegeben($id)) {
            if ($gotoedit) {
                $this->app->YUI->Message('warning','Verbindlichkeit nicht freigebeben');
                $error = true;
            } else {
                return('Verbindlichkeit nicht freigebeben '.$this->verbindlichkeit_get_belegnr($id));
            }
        }

        if (!$error) {
            // Check accounting
            $sql = "
                SELECT
                        vp.id,
                        v.belegnr
                        FROM verbindlichkeit_position vp
                        LEFT JOIN verbindlichkeit v ON v.id = vp.verbindlichkeit
                        WHERE
                            verbindlichkeit='$id'
                        AND vp.kontorahmen = 0
            ";

            $check = $this->app->DB->SelectArr($sql);

            if (!empty($check)) {
                if ($gotoedit) {
                    $this->app->YUI->Message('warning','Kontierung unvollst&auml;ndig');
                    $error = true;
                } else {
                    return('Kontierung unvollst&auml;ndig '.$this->verbindlichkeit_get_belegnr($id));
                }
            }
        }

        if (!$error) {
            $sql = "UPDATE verbindlichkeit SET rechnungsfreigabe = 1 WHERE freigabe = 1 AND id=".$id;
            $this->app->DB->Update($sql);
            $this->app->erp->BelegProtokoll("verbindlichkeit",$id,"Verbindlichkeit freigegeben (Buchhaltung)");
        }

        if ($gotoedit) {
            $this->verbindlichkeit_edit();
        } else {
            return(true);
        }
    }

    // Returns true or error message
    function verbindlichkeit_freigabebezahlt($id = null)
    {
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }

        if (!$this->verbindlichkeit_is_freigegeben($id)) {
            if ($gotoedit) {
                $this->app->YUI->Message('warning','Verbindlichkeit nicht freigebeben');
                $error = true;
            } else {
                return('Verbindlichkeit nicht freigebeben '.$this->verbindlichkeit_get_belegnr($id));
            }
        }

        if (!$error) {
            $sql = "UPDATE verbindlichkeit SET bezahlt = 1 WHERE id=".$id;
            $this->app->DB->Update($sql);
            $this->app->erp->BelegProtokoll("verbindlichkeit",$id,"Verbindlichkeit als bezahlt markiert");
            if ($gotoedit) {
                $this->verbindlichkeit_edit();
            } else {
                return(true);
            }
        }
    }

    function verbindlichkeit_abschliessen($id = null)
    {
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }

        $sql = "SELECT freigabe, rechnungsfreigabe, bezahlt, betrag FROM verbindlichkeit WHERE id =".$id;
        $verbindlichkeit = $this->app->DB->SelectRow($sql);

        if ($verbindlichkeit['freigabe'] != 1) {
            $einkauf_check = $this->check_positions($id,$verbindlichkeit['betrag']);
            if ($einkauf_check['pos_ok']) {
                $this->verbindlichkeit_freigabeeinkauf($id);
                $verbindlichkeit['freigabe'] = 1;
            }
        }

        $anzahldateien = $this->app->erp->AnzahlDateien("verbindlichkeit",$id);
        if (!empty($anzahldateien) && $verbindlichkeit['freigabe'] && $verbindlichkeit['rechnungsfreigabe'] && $verbindlichkeit['bezahlt']) {
            $sql = "UPDATE verbindlichkeit SET status = 'abgeschlossen' WHERE id=".$id;
            $this->app->DB->Update($sql);
            $this->app->erp->BelegProtokoll("verbindlichkeit",$id,"Verbindlichkeit abgeschlossen");
            if ($gotoedit) {
                $this->verbindlichkeit_edit();
            }
        }
    }

    function verbindlichkeit_ruecksetzeneinkauf($id = null)
    {
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }
        $sql = "UPDATE verbindlichkeit SET freigabe = 0 WHERE id=".$id;
        $this->app->DB->Update($sql);
        $this->app->erp->BelegProtokoll("verbindlichkeit",$id,"Verbindlichkeit r&uuml;ckgesetzt (Einkauf)");
        if ($gotoedit) {
            $this->verbindlichkeit_edit(true);
        }
    }

    function verbindlichkeit_ruecksetzenbuchhaltung($id = null)
    {
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }
        $sql = "UPDATE verbindlichkeit SET rechnungsfreigabe = 0 WHERE id=".$id;
        $this->app->DB->Update($sql);
        $this->app->erp->BelegProtokoll("verbindlichkeit",$id,"Verbindlichkeit r&uuml;ckgesetzt (Buchhaltung)");
        if ($gotoedit) {
            $this->verbindlichkeit_edit();
        }
    }

    function verbindlichkeit_ruecksetzenbezahlt($id = null)
    {
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }
        $sql = "UPDATE verbindlichkeit SET bezahlt = 0 WHERE id=".$id;
        $this->app->DB->Update($sql);
        $this->app->erp->BelegProtokoll("verbindlichkeit",$id,"Verbindlichkeit bezahlt r&uuml;ckgesetzt");
        if ($gotoedit) {
            $this->verbindlichkeit_edit();
        }
    }

/*    function verbindlichkeit_schreibschutz($id = null)
    {
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }
        $sql = "UPDATE verbindlichkeit SET schreibschutz = 0 WHERE id=".$id;
        $this->app->DB->Update($sql);
        $this->app->erp->BelegProtokoll("verbindlichkeit",$id,"Verbindlichkeit Schreibschutz entfernt");
        if ($gotoedit) {
            $this->verbindlichkeit_edit();
        }
    }  */

    public function verbindlichkeit_minidetail($parsetarget='',$menu=true) {

        $id = $this->app->Secure->GetGET('id');

        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS
                                                v.id,
                                                v.belegnr,
                                                v.status_beleg,
                                                v.schreibschutz,
                                                v.rechnung,
                                                ".$this->app->erp->FormatDate('v.zahlbarbis', 'zahlbarbis').",
                                                ".$this->app->erp->FormatMengeBetrag('v.betrag')." AS betrag,
                                                v.umsatzsteuer,
                                                v.ustid,
                                                v.summenormal,
                                                v.summeermaessigt,
                                                v.summesatz3,
                                                v.summesatz4,
                                                v.steuersatzname3,
                                                v.steuersatzname4,
                                                v.skonto,
                                                ".$this->app->erp->FormatDate('v.skontobis', 'skontobis').",
                                                v.skontofestsetzen,
                                                v.freigabe,
                                                v.freigabemitarbeiter,
                                                p.abkuerzung AS projekt,
                                                v.teilprojekt,
                                                v.auftrag,
                                                v.status,
                                                v.bezahlt,
                                                v.kontoauszuege,
                                                v.firma,
                                                v.logdatei,
                                                v.waehrung,
                                                v.zahlungsweise,
                                                ".$this->app->erp->FormatDate('v.eingangsdatum', 'eingangsdatum').",
                                                ".$this->app->erp->FormatDate('v.rechnungsdatum', 'rechnungsdatum').",
                                                v.rechnungsfreigabe,
                                                k.nummer as kostenstelle,
                                                v.beschreibung,
                                                v.sachkonto,
                                                v.art,
                                                v.verwendungszweck,
                                                v.dta_datei,
                                                v.frachtkosten,
                                                v.internebemerkung,
                                                v.ustnormal,
                                                v.ustermaessigt,
                                                v.uststuer3,
                                                v.uststuer4,
                                                v.betragbezahlt,
                                                v.bezahltam,
                                                v.klaerfall,
                                                v.klaergrund,
                                                v.skonto_erhalten,
                                                v.kurs,
                                                v.sprache,
                                                v.id,
                                                CONCAT(a.lieferantennummer,' ',a.name) AS adresse
                                                FROM verbindlichkeit v
                                                LEFT JOIN adresse a ON a.id = v.adresse
                                                LEFT JOIN projekt p ON a.projekt = p.id
                                                LEFT JOIN kostenstellen k ON v.kostenstelle = k.id
                                                WHERE v.id='$id'");

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);
        }

        if (!empty($result[0])) {
            $verbindlichkeit_from_db = $result[0];
        }

        $positionen = $this->app->DB->SelectArr("SELECT vp.id,
                                                    vp.sort,
                                                    art.name_de,
                                                    art.nummer,
                                                    vp.menge,
                                                    vp.preis,
                                                    vp.steuersatz,
                                                    CONCAT(skv.sachkonto,' ',skv.beschriftung) AS sachkonto,
                                                    ''
                                                    FROM verbindlichkeit_position vp
                                                    INNER JOIN artikel art ON art.id = vp.artikel
                                                    LEFT JOIN verbindlichkeit v ON v.id = vp.verbindlichkeit
                                                    LEFT JOIN adresse adr ON adr.id = v.adresse
                                                    LEFT JOIN kontorahmen skv ON skv.id = vp.kontorahmen
                                                    WHERE verbindlichkeit='$id'
                                                    ORDER by vp.sort ASC");

        $tmp = new EasyTable($this->app);
        $tmp->headings = array('Pos.','Artikel-Nr.','Artikel','Menge','Preis','Steuersatz','WIRDUNTENGEFÜLLTWARUMAUCHIMMER');
        $betrag_netto = 0;
        $betrag_brutto = 0;
        $steuer_normal = 0;
        $steuer_ermaessigt = 0;
        foreach ($positionen as $position) {

            $tmpsteuersatz = null;
            $tmpsteuertext = null;
            $erloes = null;

            $this->app->erp->GetSteuerPosition("verbindlichkeit",$position['id'],$tmpsteuersatz,$tmpsteuertext,$erloes);

            $position['steuersatz_berechnet'] = $tmpsteuersatz;
            $position['steuertext_berechnet'] = $tmpsteuertext;
            $position['steuererloes_berechnet'] = $erloes;

            $betrag_netto += ($position['menge']*$position['preis']);
            $betrag_brutto += ($position['menge']*$position['preis'])*(1+($tmpsteuersatz/100));

            $row = array(
                $position['sort'],
                $position['nummer'],
                $position['name_de'],
                $position['menge'],
                $position['preis'],
                $position['steuersatz_berechnet'],
                $position['sachkonto']
            );
            $tmp->AddRow($row);
        }

        $row = array(
            '',
            '',
            '',
            '',
            '<b>Betrag Positionen netto</b>',
            '<b>Betrag Positionen brutto</b>'
        );
        $tmp->AddRow($row);
        $row = array(
            '',
            '',
            '',
            '',
            round($betrag_netto,2),
            round($betrag_brutto,2)
        );
        $tmp->AddRow($row);
        $tmp->DisplayNew('ARTIKEL',"Sachkonto","noAction");

        $tmp = new EasyTable($this->app);
        $tmp->Query("SELECT zeit,bearbeiter,grund FROM verbindlichkeit_protokoll WHERE verbindlichkeit='$id' ORDER by zeit DESC",0,"");
        $tmp->DisplayNew('PROTOKOLL',"Protokoll","noAction");

        if($parsetarget=='')
        {
            $this->app->Tpl->Output('verbindlichkeit_minidetail.tpl');
            $this->app->ExitXentral();
        }
        $this->app->Tpl->Parse($parsetarget,'verbindlichkeit_minidetail.tpl');
  }

    function verbindlichkeit_is_freigegeben($id) {
        $sql = "SELECT
                    belegnr
                FROM
                    verbindlichkeit
                WHERE
                    id='$id'
                AND
                    status IN ('freigegeben')
                ";

        $check = $this->app->DB->SelectArr($sql);
        if (empty($check)) {
            return(false);
        } else
        {
            return(true);
        }
    }

    function verbindlichkeit_get_belegnr($id) {
        return($this->app->DB->Select("SELECT belegnr FROM verbindlichkeit WHERE id =".$id));
    }

    /* Calculate steuersatz
        Get from
        Check address first, if foreign, then steuersatz = 0
        if not foreign there are three cases: befreit = 0, ermaessigt, normal
        if not befreit, get from projekt or firmendaten
    */
    function get_steuersatz($umsatzsteuer, $verbindlichkeit) {
        if (is_numeric($umsatzsteuer)) {
            return($umsatzsteuer);
        }

        if ($umsatzsteuer == 'befreit') {
            return(0);
        }

        $adresse = $this->app->DB->Select("SELECT adresse FROM verbindlichkeit WHERE id=".$verbindlichkeit);
        $umsatzsteuer_lieferant = $this->app->DB->Select("SELECT umsatzsteuer_lieferant FROM adresse WHERE id=".$adresse); /* inland, eu-lieferung, import*/

        if (in_array($umsatzsteuer_lieferant,array('import','eulieferung'))) {
            return(0);
        }

        $projekt = $this->app->DB->Select("SELECT projekt FROM verbindlichkeit WHERE id=".$verbindlichkeit);
        $steuersatz_projekt = $this->app->DB->SelectRow("SELECT steuersatz_normal, steuersatz_ermaessigt FROM projekt WHERE id ='".$projekt."'");
        $steuersatz_normal_projekt = $steuersatz_projekt['steuer_normal'];
        $steuersatz_ermaessigt_projekt = $steuersatz_projekt['steuer_ermaessigt'];

        $steuersatz_normal = $this->app->erp->Firmendaten('steuersatz_normal');
        $steuersatz_ermaessigt = $this->app->erp->Firmendaten('steuersatz_ermaessigt');

        switch($umsatzsteuer) {
            case 'normal':
                if (!empty($steuersatz_normal_projekt)) {
                    return($steuersatz_normal_projekt);
                } else {
                    return($steuersatz_normal);
                }
            break;
            case 'ermaessigt':
                if (!empty($steuersatz_ermaessigt_projekt)) {
                    return($steuersatz_ermaessigt_projekt);
                } else {
                    return($steuersatz_ermaessigt);
                }
            break;
            default:
                return(0);
            break;
        }

    }

    // Check positions and return status and values
    function check_positions($id, $bruttobetrag_verbindlichkeit) : array {

        $result = array(
            "pos_ok" => false,
            "betrag_netto" => 0,
            "betrag_brutto" => 0,
            "rundungsdifferenz" => 0,
            "bruttobetrag_verbindlichkeit" => $bruttobetrag_verbindlichkeit
        );

        if (empty($id)) {
            return($result);
        }

        // Summarize positions
        $sql = "SELECT * FROM verbindlichkeit_position WHERE verbindlichkeit = ".$id;
        $positionen = $this->app->DB->SelectArr($sql);

        if (!empty($positionen)) {
            $betrag_netto = 0;
            $betrag_brutto = 0;
            $betrag_brutto_pos_summe = 0;
            $steuer_normal = 0;
            $steuer_ermaessigt = 0;
            $betrag_brutto_alternativ = 0;

            /*
                Normal: umsatzsteuer leer, steuersatz = leer
                Ermäßigt: umsatzsteuer ermaessigt, steuersatz = -1
                Befreit: umsatzsteuer befreit, steursatz = -1
                Individuell: umsatzsteuer leer, steuersatz = wert
            */
            
            $betrag_brutto_pro_steuersatz = array();
            
            foreach ($positionen as $position) {

                $tmpsteuersatz = null;
                $tmpsteuertext = null;
                $erloes = null;

                $this->app->erp->GetSteuerPosition("verbindlichkeit",$position['id'],$tmpsteuersatz,$tmpsteuertext,$erloes);

                $position['steuersatz_berechnet'] = $tmpsteuersatz;
                $position['steuertext_berechnet'] = $tmpsteuertext;
                $position['steuererloes_berechnet'] = $erloes;

                $betrag_netto_pos = ($position['menge']*$position['preis']);
                $betrag_netto += $betrag_netto_pos;
                $betrag_brutto_pos = ($position['menge']*$position['preis'])*(1+($tmpsteuersatz/100));
                $betrag_brutto += $betrag_brutto_pos;
                $betrag_brutto_pos_summe += round($betrag_brutto_pos,2);
                $betrag_netto_pro_steuersatz[$tmpsteuersatz] += round($betrag_netto_pos,2);

            }

            $result['betrag_netto'] = round($betrag_netto,2);
            $result['betrag_brutto'] = round($betrag_brutto,2);

            foreach ($betrag_netto_pro_steuersatz as $steuersatz => $betrag_netto) {
                $betrag_brutto_alternativ += round($betrag_netto*(1+($steuersatz/100)),2);
            }
            
            if ($bruttobetrag_verbindlichkeit == round($betrag_brutto,2)) {
                $result['pos_ok'] = true;
            }
            else if (round($bruttobetrag_verbindlichkeit,2) == round($betrag_brutto_pos_summe,2)) {
                $result['pos_ok'] = true;
                $result['rundungsdifferenz'] = round($bruttobetrag_verbindlichkeit-$result['betrag_brutto'],2);
            } else if (round($bruttobetrag_verbindlichkeit,2) == $betrag_brutto_alternativ) {
                $result['pos_ok'] = true;
                $result['rundungsdifferenz'] = round($bruttobetrag_verbindlichkeit-$result['betrag_brutto'],2);
            }
        }

        return($result);
    }
}
