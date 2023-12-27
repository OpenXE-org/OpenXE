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
        $this->app->ActionHandler("dateien", "verbindlichkeit_dateien");
        $this->app->ActionHandler("inlinepdf", "verbindlichkeit_inlinepdf");
        $this->app->ActionHandler("positioneneditpopup", "verbindlichkeit_positioneneditpopup");
        $this->app->ActionHandler("freigabe", "verbindlichkeit_freigabe");
        $this->app->ActionHandler("schreibschutz", "verbindlichkeit_schreibschutz");
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
                $heading = array('','','Belegnr','Adresse', 'Lieferant', 'RE-Nr', 'RE-Datum', 'Betrag (brutto)', 'W&auml;hrung', 'Ziel','Skontoziel','Skonto','Status','Monitor', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array(
                    'v.id',
                    'v.id',
                    'v.id',
                    'a.name',
                    'a.lieferantennummer',                  
                    'v.rechnung',
                    'v.rechnungsdatum',
                    'v.betrag',
                    'v.waehrung',
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
                            ".$app->erp->FormatDate("v.zahlbarbis").",
                            IF(v.skonto <> 0,".$app->erp->FormatDate("v.skontobis").",''),                             
                            IF(v.skonto <> 0,CONCAT(".$app->erp->FormatMenge('v.skonto',0).",'%'),''), 
                            v.status,
                            ".$app->YUI->IconsSQLVerbindlichkeit().",
                            v.id FROM verbindlichkeit v
                        LEFT JOIN adresse a ON v.adresse = a.id

";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM verbindlichkeit WHERE $where";
//                $groupby = "";

                // Toggle filters
                $this->app->Tpl->Add('JQUERYREADY', "$('#wareneingang').click( function() { fnFilterColumn1( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#rechnungsfreigabe').click( function() { fnFilterColumn2( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#nichtbezahlt').click( function() { fnFilterColumn3( 0 ); } );");

                for ($r = 1;$r <= 3;$r++) {
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
                   $where .= " AND v.freigabe <> '1'";
                } else {
                }

                $more_data2 = $this->app->Secure->GetGET("more_data2");
                if ($more_data2 == 1) {
                   $where .= " AND v.rechnungsfreigabe <> '1'";
                }
                else {
                }                

                $more_data3 = $this->app->Secure->GetGET("more_data3");
                if ($more_data3 == 1) {
                   $where .= " AND v.bezahlt <> '1'";            
                }
                else {                  
                }                             
                // END Toggle filters

                $this->app->YUI->DatePicker('zahlbarbis');
                $filterzahlbarbis = $this->app->YUI->TableSearchFilter($name, 4,'zahlbarbis');
                if (!empty($filterzahlbarbis)) {
                    $filterzahlbarbis = $this->app->String->Convert($filterzahlbarbis,'%1.%2.%3','%3-%2-%1');
                    $where .= " AND v.zahlbarbis <= '".$filterzahlbarbis."'";
                }

                $this->app->YUI->DatePicker('skontobis');
                $filterskontobis = $this->app->YUI->TableSearchFilter($name, 5,'skontobis');
                if (!empty($filterskontobis)) {
                    $filterskontobis = $this->app->String->Convert($filterskontobis,'%1.%2.%3','%3-%2-%1');
                    $where .= " AND v.skontobis <= '".$filterskontobis."'";
                }

                $moreinfo = true; // Allow drop down details
                $menucol = 1; // For moredata

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
        $submit = $this->app->Secure->GetPOST('ausfuehren');
        if (!empty($submit)) {
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
                            $this->verbindlichkeit_freigabeeinkauf($id);
                        }
                    break;
                    case 'freigabebuchhaltung':
                        foreach ($selectedIds as $id) {
                            $this->verbindlichkeit_freigabebuchhaltung($id);
                        }
                    break;
                    case 'bezahlt':
                        foreach ($selectedIds as $id) {
                            $this->verbindlichkeit_freigabebezahlt($id);
                        }
                    break;
                }    
            }
        }

        $this->app->erp->MenuEintrag("index.php?module=verbindlichkeit&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=verbindlichkeit&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'verbindlichkeit_list', "show", "", "", basename(__FILE__), __CLASS__);

/*

        Prepared for later use...

        if($this->app->erp->RechteVorhanden('verbindlichkeit', 'freigabeeinkauf')){
            $this->app->Tpl->Set('MANUELLFREIGABEEINKAUF', '<option value="freigabeeinkauf">{|freigeben (Einkauf)|}</option>');
        }

        if($this->app->erp->RechteVorhanden('verbindlichkeit', 'freigabebuchhaltung')){
            $this->app->Tpl->Set('MANUELLFREIGABEBUCHHALTUNG', '<option value="freigabebuchhaltung">{|freigeben (Buchhaltung)|}</option>');
        }

        if($this->app->erp->RechteVorhanden('verbindlichkeit', 'freigabebezahlt')){
            $this->app->Tpl->Set('ALSBEZAHLTMARKIEREN', '<option value="bezahlt">{|als bezahlt markieren|}</option>');
        }
*/
        $this->app->User->SetParameter('table_verbindlichkeit_list_zahlbarbis', '');
        $this->app->User->SetParameter('table_verbindlichkeit_list_skontobis', '');

        $this->app->Tpl->Parse('PAGE', "verbindlichkeit_list.tpl");
    }    

    public function verbindlichkeit_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("UPDATE `verbindlichkeit` SET status='storniert' WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde storniert.</div>");        

        $this->verbindlichkeit_list();
    } 

    /*
     * Edit verbindlichkeit item
     * If id is empty, create a new one
     */
        
    function verbindlichkeit_edit() {
        $id = $this->app->Secure->GetGET('id');
        
        $this->app->YUI->AARLGPositionen(true); // create iframe with positionen action

        // Check if other users are editing this id
        if($this->app->erp->DisableModul('artikel',$id))
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

        if ($submit != '')
        {

            // Write to database            
            // Add checks here
            $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM verbindlichkeit WHERE id =".$id);

            if ($schreibschutz) {
                $internebemerkung = $input['internebemerkung'];
                $projekt = $input['projekt'];
                $kostenstelle = $input['kostenstelle'];
                unset($input);
                $input['internebemerkung'] = $internebemerkung;
                $input['projekt'] = $this->app->erp->ReplaceProjekt(true,$projekt,true);
                $input['kostenstelle'] = $this->app->DB->Select("SELECT id FROM kostenstellen WHERE nummer = '".$kostenstelle."'");
            } else {
                $input['adresse'] = $this->app->erp->ReplaceLieferantennummer(true,$input['adresse'],true); // Parameters: Target db?, value, from form?
                $input['rechnungsdatum'] = $this->app->erp->ReplaceDatum(true,$input['rechnungsdatum'],true); // Parameters: Target db?, value, from form?
                $input['eingangsdatum'] = $this->app->erp->ReplaceDatum(true,$input['eingangsdatum'],true); // Parameters: Target db?, value, from form?
                $input['skontobis'] = $this->app->erp->ReplaceDatum(true,$input['skontobis'],true); // Parameters: Target db?, value, from form?
                $input['zahlbarbis'] = $this->app->erp->ReplaceDatum(true,$input['zahlbarbis'],true); // Parameters: Target db?, value, from form?
                $input['projekt'] = $this->app->erp->ReplaceProjekt(true,$input['projekt'],true);
                $input['kostenstelle'] = $this->app->DB->Select("SELECT id FROM kostenstellen WHERE nummer = '".$input['kostenstelle']."'");
                if(empty($input['projekt']) && !empty($input['adresse'])) {
                    $input['projekt'] = $this->app->erp->GetCreateProjekt($input['adresse']);                
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

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $id = $this->app->DB->GetInsertID();
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=verbindlichkeit&action=edit&id=$id&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

    
        // Load values again from database
	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',v.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS v.id, $dropnbox, v.belegnr, v.status_beleg, v.schreibschutz, v.rechnung, v.zahlbarbis, v.betrag, v.umsatzsteuer, v.ustid, v.summenormal, v.summeermaessigt, v.summesatz3, v.summesatz4, v.steuersatzname3, v.steuersatzname4, v.skonto, v.skontobis, v.skontofestsetzen, v.freigabe, v.freigabemitarbeiter, v.bestellung, v.adresse, v.projekt, v.teilprojekt, v.auftrag, v.status, v.bezahlt, v.kontoauszuege, v.firma, v.logdatei, v.bestellung1, v.bestellung1betrag, v.bestellung1bemerkung, v.bestellung1projekt, v.bestellung1kostenstelle, v.bestellung1auftrag, v.bestellung2, v.bestellung2betrag, v.bestellung2bemerkung, v.bestellung2kostenstelle, v.bestellung2auftrag, v.bestellung2projekt, v.bestellung3, v.bestellung3betrag, v.bestellung3bemerkung, v.bestellung3kostenstelle, v.bestellung3auftrag, v.bestellung3projekt, v.bestellung4, v.bestellung4betrag, v.bestellung4bemerkung, v.bestellung4kostenstelle, v.bestellung4auftrag, v.bestellung4projekt, v.bestellung5, v.bestellung5betrag, v.bestellung5bemerkung, v.bestellung5kostenstelle, v.bestellung5auftrag, v.bestellung5projekt, v.bestellung6, v.bestellung6betrag, v.bestellung6bemerkung, v.bestellung6kostenstelle, v.bestellung6auftrag, v.bestellung6projekt, v.bestellung7, v.bestellung7betrag, v.bestellung7bemerkung, v.bestellung7kostenstelle, v.bestellung7auftrag, v.bestellung7projekt, v.bestellung8, v.bestellung8betrag, v.bestellung8bemerkung, v.bestellung8kostenstelle, v.bestellung8auftrag, v.bestellung8projekt, v.bestellung9, v.bestellung9betrag, v.bestellung9bemerkung, v.bestellung9kostenstelle, v.bestellung9auftrag, v.bestellung9projekt, v.bestellung10, v.bestellung10betrag, v.bestellung10bemerkung, v.bestellung10kostenstelle, v.bestellung10auftrag, v.bestellung10projekt, v.bestellung11, v.bestellung11betrag, v.bestellung11bemerkung, v.bestellung11kostenstelle, v.bestellung11auftrag, v.bestellung11projekt, v.bestellung12, v.bestellung12betrag, v.bestellung12bemerkung, v.bestellung12projekt, v.bestellung12kostenstelle, v.bestellung12auftrag, v.bestellung13, v.bestellung13betrag, v.bestellung13bemerkung, v.bestellung13kostenstelle, v.bestellung13auftrag, v.bestellung13projekt, v.bestellung14, v.bestellung14betrag, v.bestellung14bemerkung, v.bestellung14kostenstelle, v.bestellung14auftrag, v.bestellung14projekt, v.bestellung15, v.bestellung15betrag, v.bestellung15bemerkung, v.bestellung15kostenstelle, v.bestellung15auftrag, v.bestellung15projekt, v.waehrung, v.zahlungsweise, v.eingangsdatum, v.buha_konto1, v.buha_belegfeld1, v.buha_betrag1, v.buha_konto2, v.buha_belegfeld2, v.buha_betrag2, v.buha_konto3, v.buha_belegfeld3, v.buha_betrag3, v.buha_konto4, v.buha_belegfeld4, v.buha_betrag4, v.buha_konto5, v.buha_belegfeld5, v.buha_betrag5, v.rechnungsdatum, v.rechnungsfreigabe, v.kostenstelle, v.beschreibung, v.sachkonto, v.art, v.verwendungszweck, v.dta_datei, v.frachtkosten, v.internebemerkung, v.ustnormal, v.ustermaessigt, v.uststuer3, v.uststuer4, v.betragbezahlt, v.bezahltam, v.klaerfall, v.klaergrund, v.skonto_erhalten, v.kurs, v.sprache, v.id, a.lieferantennummer, a.name AS adresse_name FROM verbindlichkeit v LEFT JOIN adresse a ON a.id = v.adresse"." WHERE v.id=$id");

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);   
        }

        if (!empty($result[0])) {
            $verbindlichkeit_from_db = $result[0];
        }

        // Summarize positions

        $sql = "SELECT * FROM verbindlichkeit_position WHERE verbindlichkeit = ".$id;
        $positionen = $this->app->DB->SelectArr($sql);

        if (!empty($positionen)) {
            $betrag_netto = 0;
            $betrag_brutto = 0;
            $steuer_normal = 0;
            $steuer_ermaessigt = 0;

            /* 
                Normal: umsatzsteuer leer, steuersatz = leer
                Ermäßigt: umsatzsteuer ermaessigt, steuersatz = -1
                Befreit: umsatzsteuer befreit, steursatz = -1
                Individuell: umsatzsteuer leer, steuersatz = wert
            */

            foreach ($positionen as $position) {

                $tmpsteuersatz = null;
                $tmpsteuertext = null;
                $erloes = null;

    //                  function GetSteuerPosition($typ, $posid,&$tmpsteuersatz = null, &$tmpsteuertext = null, &$erloes = null)

                $this->app->erp->GetSteuerPosition("verbindlichkeit",$position['id'],$tmpsteuersatz,$tmpsteuertext,$erloes);

                $position['steuersatz_berechnet'] = $tmpsteuersatz;
                $position['steuertext_berechnet'] = $tmpsteuertext;
                $position['steuererloes_berechnet'] = $erloes;

                $betrag_netto += ($position['menge']*$position['preis']);
                $betrag_brutto += ($position['menge']*$position['preis'])*(1+($tmpsteuersatz/100));

            }

            $this->app->Tpl->Set('BETRAGNETTO', $betrag_netto);
            $this->app->Tpl->Set('BETRAGBRUTTOPOS', round($betrag_brutto,2));

            $this->app->Tpl->Set('BETRAGDISABLED', 'disabled');

        }
            
        /*
         * Add displayed items later
         * 

        $this->app->Tpl->Add('KURZUEBERSCHRIFT2', $email);
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);         
        $this->app->YUI->AutoComplete("artikel", "artikelnummer");

         */

        if ($verbindlichkeit_from_db['schreibschutz']) {
            $this->app->Tpl->Set('SAVEDISABLED','disabled');
            $this->app->Tpl->Set('MESSAGE',"<div class=\"warning\">Diese Verbindlichkeit ist schreibgesch&uuml;tzt und darf daher nicht mehr bearbeitet werden!&nbsp;<input type=\"button\" value=\"Schreibschutz entfernen\" onclick=\"if(!confirm('Soll der Schreibschutz f&uuml;r diese Verbindlichkeit wirklich entfernt werden?')) return false;else window.location.href='index.php?module=verbindlichkeit&action=schreibschutz&id=$id';\"></div>");
        }

        if (empty($verbindlichkeit_from_db['adresse'] || $verbindlichkeit_from_db['status'] == 'angelegt')) {
            $this->app->Tpl->Set('FREIGABEEINKAUFHIDDEN','hidden');           
            $this->app->Tpl->Set('FREIGABEBUCHHALTUNGHIDDEN','hidden'); 
            $this->app->Tpl->Set('FREIGABEBEZAHLTHIDDEN','hidden'); 
        }

        if ($verbindlichkeit_from_db['freigabe']) {
            $this->app->Tpl->Set('FREIGABEEINKAUFHIDDEN','hidden');
        } else {
            $this->app->Tpl->Set('RUECKSETZENEINKAUFHIDDEN','hidden');
        }                 
        if ($verbindlichkeit_from_db['rechnungsfreigabe']) {
            $this->app->Tpl->Set('FREIGABEBUCHHALTUNGHIDDEN','hidden');
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

    	$sql = "SELECT " . $this->app->YUI->IconsSQLVerbindlichkeit() . " AS `icons` FROM verbindlichkeit v WHERE id=$id";
	    $icons = $this->app->DB->SelectArr($sql);
        $this->app->Tpl->Add('STATUSICONS',  $icons[0]['icons']);

        $this->app->YUI->AutoComplete("adresse", "lieferant");     
        $this->app->YUI->AutoComplete("projekt", "projektname", 1);
        $this->app->Tpl->Set('PROJEKT',$this->app->erp->ReplaceProjekt(false,$verbindlichkeit_from_db['projekt'],false));
        $this->app->YUI->AutoComplete("kostenstelle", "kostenstelle", 1);
        $this->app->Tpl->Set('KOSTENSTELLE',$this->app->DB->SELECT("SELECT nummer FROM kostenstellen WHERE id = '".$verbindlichkeit_from_db['kostenstelle']."'"));

        $waehrungenselect = $this->app->erp->GetSelect($this->app->erp->GetWaehrung(), $verbindlichkeit_from_db['waehrung']);
        $this->app->Tpl->Set('WAEHRUNGSELECT', $waehrungenselect);

        $this->app->Tpl->Set('ADRESSE_ID', $verbindlichkeit_from_db['adresse']);

        $this->app->Tpl->Set('ADRESSE', $this->app->erp->ReplaceLieferantennummer(false,$verbindlichkeit_from_db['adresse'],false)); // Convert ID to form display     

        $this->app->YUI->CkEditor("internebemerkung");

        $anzahldateien = $this->app->erp->AnzahlDateien("verbindlichkeit",$id);
        if ($anzahldateien > 0) {
            $file = urlencode("../../../../index.php?module=verbindlichkeit&action=inlinepdf&id=$id");        
            $iframe = "<iframe width=\"100%\" height=\"100%\" style=\"height:calc(100vh - 110px)\" src=\"./js/production/generic/web/viewer.html?file=$file\"></iframe>";
            $this->app->Tpl->Set('INLINEPDF', $iframe);
        } else {
            $this->app->Tpl->Set('INLINEPDF', 'Keine Dateien vorhanden.');
        }

        $this->verbindlichkeit_minidetail('MINIDETAIL',false);
        $this->app->Tpl->Parse('PAGE', "verbindlichkeit_edit.tpl");

    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
	    $input['adresse'] = $this->app->Secure->GetPOST('adresse');
	    $input['rechnung'] = $this->app->Secure->GetPOST('rechnung');
	    $input['zahlbarbis'] = $this->app->Secure->GetPOST('zahlbarbis');
	    $input['betrag'] = $this->app->Secure->GetPOST('betrag');
	    $input['waehrung'] = $this->app->Secure->GetPOST('waehrung');
	    $input['skonto'] = $this->app->Secure->GetPOST('skonto');
	    $input['skontobis'] = $this->app->Secure->GetPOST('skontobis');
	    $input['projekt'] = $this->app->Secure->GetPOST('projekt');
	    $input['bezahlt'] = $this->app->Secure->GetPOST('bezahlt')?'1':'0';;
	    $input['zahlungsweise'] = $this->app->Secure->GetPOST('zahlungsweise');
	    $input['eingangsdatum'] = $this->app->Secure->GetPOST('eingangsdatum');
	    $input['rechnungsdatum'] = $this->app->Secure->GetPOST('rechnungsdatum');
	    $input['freigabe'] = $this->app->Secure->GetPOST('freigabe')?'1':'0';
	    $input['rechnungsfreigabe'] = $this->app->Secure->GetPOST('rechnungsfreigabe')?'1':'0';
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

        if ($invoiceArr['status'] === 'angelegt' || $invoiceArr['schreibschutz'] != 1) {
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

//            print_r($file_attachments);

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
            } catch (\Xentral\Components\Pdf\Exception\PdfComponentExceptionInterface $exception) {
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

    function verbindlichkeit_positionen() {
        $this->app->YUI->AARLGPositionen(false); // Render positionen editable into iframe
    }

    function verbindlichkeit_positioneneditpopup() {        
        $cmd = $this->app->Secure->GetGET('cmd');
        if($cmd === 'getopenaccordions')
        {
          $accordions = $this->app->Secure->GetPOST('accordions');
          $accordions = explode('*|*',$accordions);
          foreach($accordions as $k => $v)
          {
            if(empty($v))
            {
              unset($accordions[$k]);
            }else{
              $accordions[$k] = 'verbindlichkeit_accordion'.$v;
            }
          }
          $ret = [];
          if(!empty($accordions))
          {
            $accordions = $this->app->User->GetParameter($accordions);
            if(!empty($accordions))
            {
              foreach($accordions as $v)
              {
                if(!empty($v['value']))
                {
                  $ret['accordions'][] = str_replace('verbindlichkeit_accordion','',$v['name']);
                }
              }
            }
          }
          echo json_encode($ret);
          $this->app->ExitXentral();
        }
        if($cmd === 'setaccordion')
        {
          $name = $this->app->Secure->GetPOST('name');
          $active = $this->app->Secure->GetPOST('active');
          $this->app->User->SetParameter('verbindlichkeit_accordion'.$name, $active);
          echo json_encode(array('success'=>1));
          $this->app->ExitXentral();
        }
        $id = $this->app->Secure->GetGET('id');
        $fmodul = $this->app->Secure->GetGET('fmodul');
        $artikel= $this->app->DB->Select("SELECT artikel FROM verbindlichkeit_position WHERE id='$id' LIMIT 1");

        // nach page inhalt des dialogs ausgeben
        $filename = 'widgets/widget.auftag_position_custom.php';
        if(is_file($filename)) 
        {
          include_once $filename;
          $widget = new WidgetVerbindlichkeit_positionCustom($this->app,'PAGE');
        } else {
          $widget = new WidgetVerbindlichkeit_position($this->app,'PAGE');
        }

        $sid= $this->app->DB->Select("SELECT verbindlichkeit FROM verbindlichkeit_position WHERE id='$id' LIMIT 1");
        $widget->form->SpecialActionAfterExecute('close_refresh',
            "index.php?module=verbindlichkeit&action=positionen&id=$sid&fmodul=$fmodul");
        $widget->Edit();
        $this->app->BuildNavigation=false;
    }

    function verbindlichkeit_freigabe()
    {      
        $id = $this->app->Secure->GetGET('id');
        $this->app->erp->BelegFreigabe('verbindlichkeit',$id);
        $this->app->erp->BelegProtokoll("verbindlichkeit",$id,"Verbindlichkeit freigegeben");
        $this->app->DB->Update("UPDATE verbindlichkeit SET schreibschutz = 1 WHERE id = ".$id);
        $this->verbindlichkeit_edit();
    }

    function verbindlichkeit_freigabeeinkauf($id = null)
    {      
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }
        $sql = "UPDATE verbindlichkeit SET freigabe = 1 WHERE id=".$id;
        $this->app->DB->Update($sql);
        $this->app->erp->BelegProtokoll("verbindlichkeit",$id,"Verbindlichkeit freigegeben (Einkauf)");
        if ($gotoedit) {
            $this->verbindlichkeit_edit();
        }
    }

    function verbindlichkeit_freigabebuchhaltung($id = null)
    {      
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }
        $sql = "UPDATE verbindlichkeit SET rechnungsfreigabe = 1 WHERE id=".$id;
        $this->app->DB->Update($sql);
        $this->app->erp->BelegProtokoll("verbindlichkeit",$id,"Verbindlichkeit freigegeben (Buchhaltung)");
        if ($gotoedit) {
            $this->verbindlichkeit_edit();
        }
    }

    function verbindlichkeit_freigabebezahlt($id = null)
    {      
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }
        $sql = "UPDATE verbindlichkeit SET bezahlt = 1 WHERE id=".$id;
        $this->app->DB->Update($sql);
        $this->app->erp->BelegProtokoll("verbindlichkeit",$id,"Verbindlichkeit als bezahlt markiert");
        if ($gotoedit) {
            $this->verbindlichkeit_edit();
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
            $this->verbindlichkeit_edit();
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

    function verbindlichkeit_schreibschutz($id = null)
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
    }  

    public function verbindlichkeit_minidetail($parsetarget='',$menu=true) {

        $id = $this->app->Secure->GetGET('id');  

        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS 
                                                v.id,
                                                v.belegnr,
                                                v.status_beleg,
                                                v.schreibschutz,
                                                v.rechnung,
                                                ".$this->app->erp->FormatDate('v.zahlbarbis', 'zahlbarbis').",
                                                v.betrag,
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
                                                v.bestellung,
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

}
