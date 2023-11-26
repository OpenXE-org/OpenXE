<?php

/*
 * Copyright (c) 2022 OpenXE project
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
        $this->app->ActionHandler("delete", "verbindlichkeit_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "verbindlichkeit_list":
                $allowed['verbindlichkeit_list'] = array('list');
                $heading = array('','','Belegnr','Adresse', 'Lieferant', 'RE-Nr', 'RE-Datum', 'Betrag (brutto)', 'W&auml;hrung', 'Ziel','Skontoziel','Skonto','Monitor', 'Men&uuml;');
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
                            ".$app->YUI->IconsSQLVerbindlichkeit().",
                            v.id FROM verbindlichkeit v
                        LEFT JOIN adresse a ON v.adresse = a.id

";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM verbindlichkeit WHERE $where";
//                $groupby = "";

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
        $this->app->erp->MenuEintrag("index.php?module=verbindlichkeit&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=verbindlichkeit&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'verbindlichkeit_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "verbindlichkeit_list.tpl");
    }    

    public function verbindlichkeit_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("DELETE FROM `verbindlichkeit` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->verbindlichkeit_list();
    } 

    /*
     * Edit verbindlichkeit item
     * If id is empty, create a new one
     */
        
    function verbindlichkeit_edit() {
        $id = $this->app->Secure->GetGET('id');
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('artikel',$id))
        {
          return;
        }   
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=verbindlichkeit&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=verbindlichkeit&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
        $id = $this->app->Secure->GetGET('id');
        $input = $this->GetInput();
        $submit = $this->app->Secure->GetPOST('submit');
                
        if (empty($id)) {
            // New item
            $id = 'NULL';
        } 

        if ($submit != '')
        {

            // Write to database
            
            // Add checks here

    //        $input['projekt'] = $this->app->erp->ReplaceProjekt(true,$input['projekt'],true); // Parameters: Target db?, value, from form?

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
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=verbindlichkeit&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

    
        // Load values again from database
	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',v.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS v.id, $dropnbox, v.belegnr, v.status_beleg, v.schreibschutz, v.rechnung, v.zahlbarbis, v.betrag, v.umsatzsteuer, v.ustid, v.summenormal, v.summeermaessigt, v.summesatz3, v.summesatz4, v.steuersatzname3, v.steuersatzname4, v.skonto, v.skontobis, v.skontofestsetzen, v.freigabe, v.freigabemitarbeiter, v.bestellung, v.adresse, v.projekt, v.teilprojekt, v.auftrag, v.status, v.bezahlt, v.kontoauszuege, v.firma, v.logdatei, v.bestellung1, v.bestellung1betrag, v.bestellung1bemerkung, v.bestellung1projekt, v.bestellung1kostenstelle, v.bestellung1auftrag, v.bestellung2, v.bestellung2betrag, v.bestellung2bemerkung, v.bestellung2kostenstelle, v.bestellung2auftrag, v.bestellung2projekt, v.bestellung3, v.bestellung3betrag, v.bestellung3bemerkung, v.bestellung3kostenstelle, v.bestellung3auftrag, v.bestellung3projekt, v.bestellung4, v.bestellung4betrag, v.bestellung4bemerkung, v.bestellung4kostenstelle, v.bestellung4auftrag, v.bestellung4projekt, v.bestellung5, v.bestellung5betrag, v.bestellung5bemerkung, v.bestellung5kostenstelle, v.bestellung5auftrag, v.bestellung5projekt, v.bestellung6, v.bestellung6betrag, v.bestellung6bemerkung, v.bestellung6kostenstelle, v.bestellung6auftrag, v.bestellung6projekt, v.bestellung7, v.bestellung7betrag, v.bestellung7bemerkung, v.bestellung7kostenstelle, v.bestellung7auftrag, v.bestellung7projekt, v.bestellung8, v.bestellung8betrag, v.bestellung8bemerkung, v.bestellung8kostenstelle, v.bestellung8auftrag, v.bestellung8projekt, v.bestellung9, v.bestellung9betrag, v.bestellung9bemerkung, v.bestellung9kostenstelle, v.bestellung9auftrag, v.bestellung9projekt, v.bestellung10, v.bestellung10betrag, v.bestellung10bemerkung, v.bestellung10kostenstelle, v.bestellung10auftrag, v.bestellung10projekt, v.bestellung11, v.bestellung11betrag, v.bestellung11bemerkung, v.bestellung11kostenstelle, v.bestellung11auftrag, v.bestellung11projekt, v.bestellung12, v.bestellung12betrag, v.bestellung12bemerkung, v.bestellung12projekt, v.bestellung12kostenstelle, v.bestellung12auftrag, v.bestellung13, v.bestellung13betrag, v.bestellung13bemerkung, v.bestellung13kostenstelle, v.bestellung13auftrag, v.bestellung13projekt, v.bestellung14, v.bestellung14betrag, v.bestellung14bemerkung, v.bestellung14kostenstelle, v.bestellung14auftrag, v.bestellung14projekt, v.bestellung15, v.bestellung15betrag, v.bestellung15bemerkung, v.bestellung15kostenstelle, v.bestellung15auftrag, v.bestellung15projekt, v.waehrung, v.zahlungsweise, v.eingangsdatum, v.buha_konto1, v.buha_belegfeld1, v.buha_betrag1, v.buha_konto2, v.buha_belegfeld2, v.buha_betrag2, v.buha_konto3, v.buha_belegfeld3, v.buha_betrag3, v.buha_konto4, v.buha_belegfeld4, v.buha_betrag4, v.buha_konto5, v.buha_belegfeld5, v.buha_betrag5, v.rechnungsdatum, v.rechnungsfreigabe, v.kostenstelle, v.beschreibung, v.sachkonto, v.art, v.verwendungszweck, v.dta_datei, v.frachtkosten, v.internebemerkung, v.ustnormal, v.ustermaessigt, v.uststuer3, v.uststuer4, v.betragbezahlt, v.bezahltam, v.klaerfall, v.klaergrund, v.skonto_erhalten, v.kurs, v.sprache, v.id FROM verbindlichkeit v"." WHERE id=$id");

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);   
        }
             
        /*
         * Add displayed items later
         * 

        $this->app->Tpl->Add('KURZUEBERSCHRIFT2', $email);
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);         

        $this->app->YUI->AutoComplete("artikel", "artikelnummer");

         */

//        $this->SetInput($input);              
        $this->app->Tpl->Parse('PAGE', "verbindlichkeit_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['belegnr'] = $this->app->Secure->GetPOST('belegnr');
	$input['status_beleg'] = $this->app->Secure->GetPOST('status_beleg');
	$input['schreibschutz'] = $this->app->Secure->GetPOST('schreibschutz');
	$input['rechnung'] = $this->app->Secure->GetPOST('rechnung');
	$input['zahlbarbis'] = $this->app->Secure->GetPOST('zahlbarbis');
	$input['betrag'] = $this->app->Secure->GetPOST('betrag');
	$input['umsatzsteuer'] = $this->app->Secure->GetPOST('umsatzsteuer');
	$input['ustid'] = $this->app->Secure->GetPOST('ustid');
	$input['summenormal'] = $this->app->Secure->GetPOST('summenormal');
	$input['summeermaessigt'] = $this->app->Secure->GetPOST('summeermaessigt');
	$input['summesatz3'] = $this->app->Secure->GetPOST('summesatz3');
	$input['summesatz4'] = $this->app->Secure->GetPOST('summesatz4');
	$input['steuersatzname3'] = $this->app->Secure->GetPOST('steuersatzname3');
	$input['steuersatzname4'] = $this->app->Secure->GetPOST('steuersatzname4');
	$input['skonto'] = $this->app->Secure->GetPOST('skonto');
	$input['skontobis'] = $this->app->Secure->GetPOST('skontobis');
	$input['skontofestsetzen'] = $this->app->Secure->GetPOST('skontofestsetzen');
	$input['freigabe'] = $this->app->Secure->GetPOST('freigabe');
	$input['freigabemitarbeiter'] = $this->app->Secure->GetPOST('freigabemitarbeiter');
	$input['bestellung'] = $this->app->Secure->GetPOST('bestellung');
	$input['adresse'] = $this->app->Secure->GetPOST('adresse');
	$input['projekt'] = $this->app->Secure->GetPOST('projekt');
	$input['teilprojekt'] = $this->app->Secure->GetPOST('teilprojekt');
	$input['auftrag'] = $this->app->Secure->GetPOST('auftrag');
	$input['status'] = $this->app->Secure->GetPOST('status');
	$input['bezahlt'] = $this->app->Secure->GetPOST('bezahlt');
	$input['kontoauszuege'] = $this->app->Secure->GetPOST('kontoauszuege');
	$input['firma'] = $this->app->Secure->GetPOST('firma');
	$input['logdatei'] = $this->app->Secure->GetPOST('logdatei');
	$input['bestellung1'] = $this->app->Secure->GetPOST('bestellung1');
	$input['bestellung1betrag'] = $this->app->Secure->GetPOST('bestellung1betrag');
	$input['bestellung1bemerkung'] = $this->app->Secure->GetPOST('bestellung1bemerkung');
	$input['bestellung1projekt'] = $this->app->Secure->GetPOST('bestellung1projekt');
	$input['bestellung1kostenstelle'] = $this->app->Secure->GetPOST('bestellung1kostenstelle');
	$input['bestellung1auftrag'] = $this->app->Secure->GetPOST('bestellung1auftrag');
	$input['bestellung2'] = $this->app->Secure->GetPOST('bestellung2');
	$input['bestellung2betrag'] = $this->app->Secure->GetPOST('bestellung2betrag');
	$input['bestellung2bemerkung'] = $this->app->Secure->GetPOST('bestellung2bemerkung');
	$input['bestellung2kostenstelle'] = $this->app->Secure->GetPOST('bestellung2kostenstelle');
	$input['bestellung2auftrag'] = $this->app->Secure->GetPOST('bestellung2auftrag');
	$input['bestellung2projekt'] = $this->app->Secure->GetPOST('bestellung2projekt');
	$input['bestellung3'] = $this->app->Secure->GetPOST('bestellung3');
	$input['bestellung3betrag'] = $this->app->Secure->GetPOST('bestellung3betrag');
	$input['bestellung3bemerkung'] = $this->app->Secure->GetPOST('bestellung3bemerkung');
	$input['bestellung3kostenstelle'] = $this->app->Secure->GetPOST('bestellung3kostenstelle');
	$input['bestellung3auftrag'] = $this->app->Secure->GetPOST('bestellung3auftrag');
	$input['bestellung3projekt'] = $this->app->Secure->GetPOST('bestellung3projekt');
	$input['bestellung4'] = $this->app->Secure->GetPOST('bestellung4');
	$input['bestellung4betrag'] = $this->app->Secure->GetPOST('bestellung4betrag');
	$input['bestellung4bemerkung'] = $this->app->Secure->GetPOST('bestellung4bemerkung');
	$input['bestellung4kostenstelle'] = $this->app->Secure->GetPOST('bestellung4kostenstelle');
	$input['bestellung4auftrag'] = $this->app->Secure->GetPOST('bestellung4auftrag');
	$input['bestellung4projekt'] = $this->app->Secure->GetPOST('bestellung4projekt');
	$input['bestellung5'] = $this->app->Secure->GetPOST('bestellung5');
	$input['bestellung5betrag'] = $this->app->Secure->GetPOST('bestellung5betrag');
	$input['bestellung5bemerkung'] = $this->app->Secure->GetPOST('bestellung5bemerkung');
	$input['bestellung5kostenstelle'] = $this->app->Secure->GetPOST('bestellung5kostenstelle');
	$input['bestellung5auftrag'] = $this->app->Secure->GetPOST('bestellung5auftrag');
	$input['bestellung5projekt'] = $this->app->Secure->GetPOST('bestellung5projekt');
	$input['bestellung6'] = $this->app->Secure->GetPOST('bestellung6');
	$input['bestellung6betrag'] = $this->app->Secure->GetPOST('bestellung6betrag');
	$input['bestellung6bemerkung'] = $this->app->Secure->GetPOST('bestellung6bemerkung');
	$input['bestellung6kostenstelle'] = $this->app->Secure->GetPOST('bestellung6kostenstelle');
	$input['bestellung6auftrag'] = $this->app->Secure->GetPOST('bestellung6auftrag');
	$input['bestellung6projekt'] = $this->app->Secure->GetPOST('bestellung6projekt');
	$input['bestellung7'] = $this->app->Secure->GetPOST('bestellung7');
	$input['bestellung7betrag'] = $this->app->Secure->GetPOST('bestellung7betrag');
	$input['bestellung7bemerkung'] = $this->app->Secure->GetPOST('bestellung7bemerkung');
	$input['bestellung7kostenstelle'] = $this->app->Secure->GetPOST('bestellung7kostenstelle');
	$input['bestellung7auftrag'] = $this->app->Secure->GetPOST('bestellung7auftrag');
	$input['bestellung7projekt'] = $this->app->Secure->GetPOST('bestellung7projekt');
	$input['bestellung8'] = $this->app->Secure->GetPOST('bestellung8');
	$input['bestellung8betrag'] = $this->app->Secure->GetPOST('bestellung8betrag');
	$input['bestellung8bemerkung'] = $this->app->Secure->GetPOST('bestellung8bemerkung');
	$input['bestellung8kostenstelle'] = $this->app->Secure->GetPOST('bestellung8kostenstelle');
	$input['bestellung8auftrag'] = $this->app->Secure->GetPOST('bestellung8auftrag');
	$input['bestellung8projekt'] = $this->app->Secure->GetPOST('bestellung8projekt');
	$input['bestellung9'] = $this->app->Secure->GetPOST('bestellung9');
	$input['bestellung9betrag'] = $this->app->Secure->GetPOST('bestellung9betrag');
	$input['bestellung9bemerkung'] = $this->app->Secure->GetPOST('bestellung9bemerkung');
	$input['bestellung9kostenstelle'] = $this->app->Secure->GetPOST('bestellung9kostenstelle');
	$input['bestellung9auftrag'] = $this->app->Secure->GetPOST('bestellung9auftrag');
	$input['bestellung9projekt'] = $this->app->Secure->GetPOST('bestellung9projekt');
	$input['bestellung10'] = $this->app->Secure->GetPOST('bestellung10');
	$input['bestellung10betrag'] = $this->app->Secure->GetPOST('bestellung10betrag');
	$input['bestellung10bemerkung'] = $this->app->Secure->GetPOST('bestellung10bemerkung');
	$input['bestellung10kostenstelle'] = $this->app->Secure->GetPOST('bestellung10kostenstelle');
	$input['bestellung10auftrag'] = $this->app->Secure->GetPOST('bestellung10auftrag');
	$input['bestellung10projekt'] = $this->app->Secure->GetPOST('bestellung10projekt');
	$input['bestellung11'] = $this->app->Secure->GetPOST('bestellung11');
	$input['bestellung11betrag'] = $this->app->Secure->GetPOST('bestellung11betrag');
	$input['bestellung11bemerkung'] = $this->app->Secure->GetPOST('bestellung11bemerkung');
	$input['bestellung11kostenstelle'] = $this->app->Secure->GetPOST('bestellung11kostenstelle');
	$input['bestellung11auftrag'] = $this->app->Secure->GetPOST('bestellung11auftrag');
	$input['bestellung11projekt'] = $this->app->Secure->GetPOST('bestellung11projekt');
	$input['bestellung12'] = $this->app->Secure->GetPOST('bestellung12');
	$input['bestellung12betrag'] = $this->app->Secure->GetPOST('bestellung12betrag');
	$input['bestellung12bemerkung'] = $this->app->Secure->GetPOST('bestellung12bemerkung');
	$input['bestellung12projekt'] = $this->app->Secure->GetPOST('bestellung12projekt');
	$input['bestellung12kostenstelle'] = $this->app->Secure->GetPOST('bestellung12kostenstelle');
	$input['bestellung12auftrag'] = $this->app->Secure->GetPOST('bestellung12auftrag');
	$input['bestellung13'] = $this->app->Secure->GetPOST('bestellung13');
	$input['bestellung13betrag'] = $this->app->Secure->GetPOST('bestellung13betrag');
	$input['bestellung13bemerkung'] = $this->app->Secure->GetPOST('bestellung13bemerkung');
	$input['bestellung13kostenstelle'] = $this->app->Secure->GetPOST('bestellung13kostenstelle');
	$input['bestellung13auftrag'] = $this->app->Secure->GetPOST('bestellung13auftrag');
	$input['bestellung13projekt'] = $this->app->Secure->GetPOST('bestellung13projekt');
	$input['bestellung14'] = $this->app->Secure->GetPOST('bestellung14');
	$input['bestellung14betrag'] = $this->app->Secure->GetPOST('bestellung14betrag');
	$input['bestellung14bemerkung'] = $this->app->Secure->GetPOST('bestellung14bemerkung');
	$input['bestellung14kostenstelle'] = $this->app->Secure->GetPOST('bestellung14kostenstelle');
	$input['bestellung14auftrag'] = $this->app->Secure->GetPOST('bestellung14auftrag');
	$input['bestellung14projekt'] = $this->app->Secure->GetPOST('bestellung14projekt');
	$input['bestellung15'] = $this->app->Secure->GetPOST('bestellung15');
	$input['bestellung15betrag'] = $this->app->Secure->GetPOST('bestellung15betrag');
	$input['bestellung15bemerkung'] = $this->app->Secure->GetPOST('bestellung15bemerkung');
	$input['bestellung15kostenstelle'] = $this->app->Secure->GetPOST('bestellung15kostenstelle');
	$input['bestellung15auftrag'] = $this->app->Secure->GetPOST('bestellung15auftrag');
	$input['bestellung15projekt'] = $this->app->Secure->GetPOST('bestellung15projekt');
	$input['waehrung'] = $this->app->Secure->GetPOST('waehrung');
	$input['zahlungsweise'] = $this->app->Secure->GetPOST('zahlungsweise');
	$input['eingangsdatum'] = $this->app->Secure->GetPOST('eingangsdatum');
	$input['buha_konto1'] = $this->app->Secure->GetPOST('buha_konto1');
	$input['buha_belegfeld1'] = $this->app->Secure->GetPOST('buha_belegfeld1');
	$input['buha_betrag1'] = $this->app->Secure->GetPOST('buha_betrag1');
	$input['buha_konto2'] = $this->app->Secure->GetPOST('buha_konto2');
	$input['buha_belegfeld2'] = $this->app->Secure->GetPOST('buha_belegfeld2');
	$input['buha_betrag2'] = $this->app->Secure->GetPOST('buha_betrag2');
	$input['buha_konto3'] = $this->app->Secure->GetPOST('buha_konto3');
	$input['buha_belegfeld3'] = $this->app->Secure->GetPOST('buha_belegfeld3');
	$input['buha_betrag3'] = $this->app->Secure->GetPOST('buha_betrag3');
	$input['buha_konto4'] = $this->app->Secure->GetPOST('buha_konto4');
	$input['buha_belegfeld4'] = $this->app->Secure->GetPOST('buha_belegfeld4');
	$input['buha_betrag4'] = $this->app->Secure->GetPOST('buha_betrag4');
	$input['buha_konto5'] = $this->app->Secure->GetPOST('buha_konto5');
	$input['buha_belegfeld5'] = $this->app->Secure->GetPOST('buha_belegfeld5');
	$input['buha_betrag5'] = $this->app->Secure->GetPOST('buha_betrag5');
	$input['rechnungsdatum'] = $this->app->Secure->GetPOST('rechnungsdatum');
	$input['rechnungsfreigabe'] = $this->app->Secure->GetPOST('rechnungsfreigabe');
	$input['kostenstelle'] = $this->app->Secure->GetPOST('kostenstelle');
	$input['beschreibung'] = $this->app->Secure->GetPOST('beschreibung');
	$input['sachkonto'] = $this->app->Secure->GetPOST('sachkonto');
	$input['art'] = $this->app->Secure->GetPOST('art');
	$input['verwendungszweck'] = $this->app->Secure->GetPOST('verwendungszweck');
	$input['dta_datei'] = $this->app->Secure->GetPOST('dta_datei');
	$input['frachtkosten'] = $this->app->Secure->GetPOST('frachtkosten');
	$input['internebemerkung'] = $this->app->Secure->GetPOST('internebemerkung');
	$input['ustnormal'] = $this->app->Secure->GetPOST('ustnormal');
	$input['ustermaessigt'] = $this->app->Secure->GetPOST('ustermaessigt');
	$input['uststuer3'] = $this->app->Secure->GetPOST('uststuer3');
	$input['uststuer4'] = $this->app->Secure->GetPOST('uststuer4');
	$input['betragbezahlt'] = $this->app->Secure->GetPOST('betragbezahlt');
	$input['bezahltam'] = $this->app->Secure->GetPOST('bezahltam');
	$input['klaerfall'] = $this->app->Secure->GetPOST('klaerfall');
	$input['klaergrund'] = $this->app->Secure->GetPOST('klaergrund');
	$input['skonto_erhalten'] = $this->app->Secure->GetPOST('skonto_erhalten');
	$input['kurs'] = $this->app->Secure->GetPOST('kurs');
	$input['sprache'] = $this->app->Secure->GetPOST('sprache');
	

        return $input;
    }
 }
