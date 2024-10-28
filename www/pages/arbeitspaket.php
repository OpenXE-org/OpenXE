<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;
use Xentral\Modules\TransferSmartyTemplate\TransferSmartyTemplate;

class Arbeitspaket {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "arbeitspaket_list");        
        $this->app->ActionHandler("create", "arbeitspaket_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "arbeitspaket_edit");
        $this->app->ActionHandler("delete", "arbeitspaket_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "arbeitspaket_list":
                $allowed['arbeitspaket_list'] = array('list');
                $heading = array('','','Adresse', 'Aufgabe', 'Beschreibung', 'Projekt', 'Zeit_geplant', 'Kostenstelle', 'Status', 'Abgabe', 'Abgenommen', 'Abgenommen_von', 'Abgenommen_bemerkung', 'Initiator', 'Art', 'Abgabedatum', 'Logdatei', 'Geloescht', 'Vorgaenger', 'Kosten_geplant', 'Artikel_geplant', 'Auftragid', 'Abgerechnet', 'Cache_BE', 'Cache_PR', 'Cache_AN', 'Cache_AB', 'Cache_LS', 'Cache_RE', 'Cache_GS', 'Last_cache', 'Aktiv', 'Startdatum', 'Sort', 'Ek_geplant', 'Vk_geplant', 'Kalkulationbasis', 'Cache_PF', 'Farbe', 'Vkkalkulationbasis', 'Projektplanausblenden', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('a.id','a.id','a.adresse', 'a.aufgabe', 'a.beschreibung', 'a.projekt', 'a.zeit_geplant', 'a.kostenstelle', 'a.status', 'a.abgabe', 'a.abgenommen', 'a.abgenommen_von', 'a.abgenommen_bemerkung', 'a.initiator', 'a.art', 'a.abgabedatum', 'a.logdatei', 'a.geloescht', 'a.vorgaenger', 'a.kosten_geplant', 'a.artikel_geplant', 'a.auftragid', 'a.abgerechnet', 'a.cache_BE', 'a.cache_PR', 'a.cache_AN', 'a.cache_AB', 'a.cache_LS', 'a.cache_RE', 'a.cache_GS', 'a.last_cache', 'a.aktiv', 'a.startdatum', 'a.sort', 'a.ek_geplant', 'a.vk_geplant', 'a.kalkulationbasis', 'a.cache_PF', 'a.farbe', 'a.vkkalkulationbasis', 'a.projektplanausblenden'); // use 'null' for non-searchable columns
                $searchsql = array('a.adresse', 'a.aufgabe', 'a.beschreibung', 'a.projekt', 'a.zeit_geplant', 'a.kostenstelle', 'a.status', 'a.abgabe', 'a.abgenommen', 'a.abgenommen_von', 'a.abgenommen_bemerkung', 'a.initiator', 'a.art', 'a.abgabedatum', 'a.logdatei', 'a.geloescht', 'a.vorgaenger', 'a.kosten_geplant', 'a.artikel_geplant', 'a.auftragid', 'a.abgerechnet', 'a.cache_BE', 'a.cache_PR', 'a.cache_AN', 'a.cache_AB', 'a.cache_LS', 'a.cache_RE', 'a.cache_GS', 'a.last_cache', 'a.aktiv', 'a.startdatum', 'a.sort', 'a.ek_geplant', 'a.vk_geplant', 'a.kalkulationbasis', 'a.cache_PF', 'a.farbe', 'a.vkkalkulationbasis', 'a.projektplanausblenden');

                $defaultorder = 1;
                $defaultorderdesc = 0;
                $aligncenter = array();
                $alignright = array();
                $numbercols = array();
                $sumcol = array();

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',a.id,'\" />') AS `auswahl`";

//                $moreinfo = true; // Allow drop down details
//                $moreinfoaction = "lieferschein"; // specify suffix for minidetail-URL to allow different minidetails
//                $menucol = 11; // Set id col for moredata/menu

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=arbeitspaket&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=arbeitspaket&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, $dropnbox, a.adresse, a.aufgabe, a.beschreibung, a.projekt, a.zeit_geplant, a.kostenstelle, a.status, a.abgabe, a.abgenommen, a.abgenommen_von, a.abgenommen_bemerkung, a.initiator, a.art, a.abgabedatum, a.logdatei, a.geloescht, a.vorgaenger, a.kosten_geplant, a.artikel_geplant, a.auftragid, a.abgerechnet, a.cache_BE, a.cache_PR, a.cache_AN, a.cache_AB, a.cache_LS, a.cache_RE, a.cache_GS, a.last_cache, a.aktiv, a.startdatum, a.sort, a.ek_geplant, a.vk_geplant, a.kalkulationbasis, a.cache_PF, a.farbe, a.vkkalkulationbasis, a.projektplanausblenden, a.id FROM arbeitspaket a";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM arbeitspaket WHERE $where";
//                $groupby = "";

//                echo($sql." WHERE ".$where." ".$groupby);

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
    
    function arbeitspaket_list() {
        $this->app->erp->MenuEintrag("index.php?module=arbeitspaket&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=arbeitspaket&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $smarty = new Smarty;
        
        $smarty->assign('text', 'Smarty-Ausgabe für Rechnung 1');
        
        $rechnung = $this->app->DB->SelectRow("
            SELECT * FROM rechnung WHERE id = 1 LIMIT 1
        ");        
        $adresse = $this->app->DB->SelectArr("
            SELECT * FROM adresse WHERE id = (SELECT adresse FROM rechnung WHERE id = 1 LIMIT 1)
        ");   
        $rechnung['adresse'] = $adresse;            

        $positionen = $this->app->DB->SelectArr("
            SELECT * FROM rechnung_position WHERE rechnung = 1 ORDER BY sort ASC
        ");        
        $rechnung['positionen'] = $positionen;        

        
        $this->app->Tpl->Set('DUMP',print_r($rechnung,true));
        
        $smarty->assign('rechnung', $rechnung);
        
        $html = $smarty->fetch('../www/pages/content/smarty/smartytest.tpl');
        
        $this->app->Tpl->Set('SMARTY', $html);

        $this->app->YUI->TableSearch('TAB1', 'arbeitspaket_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "arbeitspaket_list.tpl");
    }    

    public function arbeitspaket_delete() {
        $id = (int) $this->app->Secure->GetGET('id');     
        $this->app->DB->Delete("DELETE FROM `arbeitspaket` WHERE `id` = '{$id}'");        
        $this->app->Tpl->addMessage('error', 'Der Eintrag wurde gel&ouml;scht');        
        $this->arbeitspaket_list();
    } 

    /*
     * Edit arbeitspaket item
     * If id is empty, create a new one
     */
        
    function arbeitspaket_edit() {
        $id = $this->app->Secure->GetGET('id');
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('arbeitspaket',$id))
        {
          return;
        }   
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=arbeitspaket&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=arbeitspaket&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
        $id = $this->app->Secure->GetGET('id');
        $input = $this->GetInput();
        
        // Convert here
    	// $input['prio'] = !empty($this->app->Secure->GetPOST('prio'))?"1":"0";        
        
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

            $sql = "INSERT INTO arbeitspaket (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=arbeitspaket&action=list&msg=$msg");
            } else {
                $this->app->Tpl->addMessage('success', 'Die Einstellungen wurden erfolgreich &uuml;bernommen.');
            }
        }

    
        // Load values again from database
        if ($id != 'NULL') {

        	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',a.id,'\" />') AS `auswahl`";
            $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS a.id, $dropnbox, a.adresse, a.aufgabe, a.beschreibung, a.projekt, a.zeit_geplant, a.kostenstelle, a.status, a.abgabe, a.abgenommen, a.abgenommen_von, a.abgenommen_bemerkung, a.initiator, a.art, a.abgabedatum, a.logdatei, a.geloescht, a.vorgaenger, a.kosten_geplant, a.artikel_geplant, a.auftragid, a.abgerechnet, a.cache_BE, a.cache_PR, a.cache_AN, a.cache_AB, a.cache_LS, a.cache_RE, a.cache_GS, a.last_cache, a.aktiv, a.startdatum, a.sort, a.ek_geplant, a.vk_geplant, a.kalkulationbasis, a.cache_PF, a.farbe, a.vkkalkulationbasis, a.projektplanausblenden, a.id FROM arbeitspaket a"." WHERE id=$id");        

            foreach ($result[0] as $key => $value) {
                $this->app->Tpl->Set(strtoupper($key), $value);   
            }

            if (!empty($result)) {
                $arbeitspaket_from_db = $result[0];
            } else {
                return;
            }
        }
             
        /*
         * Add displayed items later
         * 

        $this->app->Tpl->Add('KURZUEBERSCHRIFT2', $email);
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);         

        $this->app->YUI->AutoComplete("artikel", "artikelnummer");
        $this->app->Tpl->Set('PROJEKT',$this->app->erp->ReplaceProjekt(false,$arbeitspaket_from_db['projekt'],false));
      	$this->app->Tpl->Set('PRIO', $arbeitspaket_from_db['prio']==1?"checked":"");

         */

        $this->app->Tpl->Parse('PAGE', "arbeitspaket_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['adresse'] = $this->app->Secure->GetPOST('adresse');
	$input['aufgabe'] = $this->app->Secure->GetPOST('aufgabe');
	$input['beschreibung'] = $this->app->Secure->GetPOST('beschreibung');
	$input['projekt'] = $this->app->Secure->GetPOST('projekt');
	$input['zeit_geplant'] = $this->app->Secure->GetPOST('zeit_geplant');
	$input['kostenstelle'] = $this->app->Secure->GetPOST('kostenstelle');
	$input['status'] = $this->app->Secure->GetPOST('status');
	$input['abgabe'] = $this->app->Secure->GetPOST('abgabe');
	$input['abgenommen'] = $this->app->Secure->GetPOST('abgenommen');
	$input['abgenommen_von'] = $this->app->Secure->GetPOST('abgenommen_von');
	$input['abgenommen_bemerkung'] = $this->app->Secure->GetPOST('abgenommen_bemerkung');
	$input['initiator'] = $this->app->Secure->GetPOST('initiator');
	$input['art'] = $this->app->Secure->GetPOST('art');
	$input['abgabedatum'] = $this->app->Secure->GetPOST('abgabedatum');
	$input['logdatei'] = $this->app->Secure->GetPOST('logdatei');
	$input['geloescht'] = $this->app->Secure->GetPOST('geloescht');
	$input['vorgaenger'] = $this->app->Secure->GetPOST('vorgaenger');
	$input['kosten_geplant'] = $this->app->Secure->GetPOST('kosten_geplant');
	$input['artikel_geplant'] = $this->app->Secure->GetPOST('artikel_geplant');
	$input['auftragid'] = $this->app->Secure->GetPOST('auftragid');
	$input['abgerechnet'] = $this->app->Secure->GetPOST('abgerechnet');
	$input['cache_BE'] = $this->app->Secure->GetPOST('cache_BE');
	$input['cache_PR'] = $this->app->Secure->GetPOST('cache_PR');
	$input['cache_AN'] = $this->app->Secure->GetPOST('cache_AN');
	$input['cache_AB'] = $this->app->Secure->GetPOST('cache_AB');
	$input['cache_LS'] = $this->app->Secure->GetPOST('cache_LS');
	$input['cache_RE'] = $this->app->Secure->GetPOST('cache_RE');
	$input['cache_GS'] = $this->app->Secure->GetPOST('cache_GS');
	$input['last_cache'] = $this->app->Secure->GetPOST('last_cache');
	$input['aktiv'] = $this->app->Secure->GetPOST('aktiv');
	$input['startdatum'] = $this->app->Secure->GetPOST('startdatum');
	$input['sort'] = $this->app->Secure->GetPOST('sort');
	$input['ek_geplant'] = $this->app->Secure->GetPOST('ek_geplant');
	$input['vk_geplant'] = $this->app->Secure->GetPOST('vk_geplant');
	$input['kalkulationbasis'] = $this->app->Secure->GetPOST('kalkulationbasis');
	$input['cache_PF'] = $this->app->Secure->GetPOST('cache_PF');
	$input['farbe'] = $this->app->Secure->GetPOST('farbe');
	$input['vkkalkulationbasis'] = $this->app->Secure->GetPOST('vkkalkulationbasis');
	$input['projektplanausblenden'] = $this->app->Secure->GetPOST('projektplanausblenden');
	

        return $input;
    }
 }
