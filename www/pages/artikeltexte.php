<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Artikeltexte {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "artikel_texte_list");        
        $this->app->ActionHandler("create", "artikel_texte_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "artikel_texte_edit");
        $this->app->ActionHandler("delete", "artikel_texte_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "artikel_texte_list":
                $allowed['artikel_texte_list'] = array('list');
                $heading = array('','','Nummer','Artikel','Sprache', 'Name', 'Kurztext', 'Beschreibung', 'Beschreibung_online', 'Meta title', 'Meta description', 'Meta keywords', 'Katalogartikel', 'Katalogbezeichnung', 'Katalogtext', 'Shop', 'Aktiv', 'Men&uuml;');
                $width = array('1%','1%','1%'); // Fill out manually later

                $artikel = $app->User->GetParameter('artikeltexte_artikel');

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('a.id','a.id','art.nummer', 'art.name_de', 'a.sprache', 'a.name', 'a.kurztext', 'a.beschreibung', 'a.beschreibung_online', 'a.meta_title', 'a.meta_description', 'a.meta_keywords', 'a.katalogartikel', 'a.katalog_bezeichnung', 'a.katalog_text', 'a.shop', 'a.aktiv');
                $searchsql = array('a.artikel', 'a.sprache', 'a.name', 'a.kurztext', 'a.beschreibung', 'a.beschreibung_online', 'a.meta_title', 'a.meta_description', 'a.meta_keywords', 'a.katalogartikel', 'a.katalog_bezeichnung', 'a.katalog_text', 'a.shop', 'a.aktiv');

                $defaultorder = 1;
                $defaultorderdesc = 0;
                $aligncenter = array();
                $alignright = array();
                $numbercols = array();
                $sumcol = array();

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',a.id,'\" />') AS `auswahl`";

//                $moreinfo = true; // Allow drop down details
//                $moreinfoaction = "lieferschein"; // specify suffix for minidetail-URL to allow different minidetails
//                $mencol = 11; // Set id col for moredata/menu

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=artikeltexte&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikeltexte&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "
                    SELECT SQL_CALC_FOUND_ROWS a.id, $dropnbox,
                        art.nummer,
                        art.name_de,
                        a.sprache,
                        a.name,
                        a.kurztext,
                        a.beschreibung,
                        a.beschreibung_online,
                        a.meta_title,
                        a.meta_description,
                        a.meta_keywords,
                        a.katalogartikel,
                        a.katalog_bezeichnung,
                        a.katalog_text,
                        a.shop,
                        a.aktiv,
                        a.id FROM artikel_texte a
                        INNER JOIN artikel art ON art.id = a.artikel 
                ";

                $where = "1";

                if ($artikel) {
         //           $where .= " AND a.artikel = '".$artikel."'";
                }

                $count = "SELECT count(DISTINCT id) FROM artikel_texte a WHERE $where";
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
    
    function artikel_texte_list() {

        $this->app->erp->MenuEintrag("index.php?module=artikeltexte&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=artikeltexte&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $artikel = $this->app->Secure->GetGET('artikel');
        $this->app->User->SetParameter('artikeltexte_artikel', $artikel);

        $this->app->YUI->TableSearch('TAB1', 'artikel_texte_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "artikeltexte_list.tpl");
    }    

    public function artikel_texte_delete() {
        $id = (int) $this->app->Secure->GetGET('id');     
        $this->app->DB->Delete("DELETE FROM `artikel_texte` WHERE `id` = '{$id}'");        
        $this->app->Tpl->addMessage('error', 'Der Eintrag wurde gel&ouml;scht');        
        $this->artikel_texte_list();
    } 

    /*
     * Edit artikel_texte item
     * If id is empty, create a new one
     */
        
    function artikel_texte_edit() {
        $id = $this->app->Secure->GetGET('id');
        // Check if other users are editing this id
/*        if($this->app->erp->DisableModul('artikel_texte',$id))
        {
          return;
        }   */
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=artikeltexte&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=artikeltexte&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
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

            $sql = "INSERT INTO artikel_texte (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=artikeltexte&action=list&msg=$msg");
            } else {
                $this->app->Tpl->addMessage('success', 'Die Einstellungen wurden erfolgreich &uuml;bernommen.');
            }
        }

    
        // Load values again from database
        if ($id != 'NULL') {

        	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',a.id,'\" />') AS `auswahl`";
            $result = $this->app->DB->SelectArr("
                SELECT SQL_CALC_FOUND_ROWS 
                    a.id,
                    $dropnbox,
                    art.name_de,
                    a.sprache,
                    a.name,
                    a.kurztext,
                    a.beschreibung,
                    a.beschreibung_online,
                    a.meta_title,
                    a.meta_description,
                    a.meta_keywords,
                    a.katalogartikel,
                    a.katalog_bezeichnung,
                    a.katalog_text,
                    a.shop,
                    a.aktiv,
                    a.id
                FROM
                    artikel_texte a 
                INNER JOIN artikel art ON a.artikel = art.id
                WHERE a.id=$id
            ");        


            foreach ($result[0] as $key => $value) {
                $this->app->Tpl->Set(strtoupper($key), $value);   
            }

            if (!empty($result)) {
                $artikel_texte_from_db = $result[0];
            } else {
                return;
            }
        }
             
        /*
         * Add displayed items later
         * 
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);         

        $this->app->YUI->AutoComplete("artikel", "artikelnummer");

         */

        $sprachenOptions = $this->app->erp->GetSprachenSelect();    
        $this->app->Tpl->Set('SPRACHE', $this->app->erp->GetSelectAsso($sprachenOptions, $artikel_texte_from_db['sprache']));
        $this->app->Tpl->Parse('PAGE', "artikeltexte_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
	    $input['sprache'] = $this->app->Secure->GetPOST('sprache');
	    $input['name'] = $this->app->Secure->GetPOST('name');
	    $input['kurztext'] = $this->app->Secure->GetPOST('kurztext');
	    $input['beschreibung'] = $this->app->Secure->GetPOST('beschreibung');
	    $input['beschreibung_online'] = $this->app->Secure->GetPOST('beschreibung_online');
	    $input['meta_title'] = $this->app->Secure->GetPOST('meta_title');
	    $input['meta_description'] = $this->app->Secure->GetPOST('meta_description');
	    $input['meta_keywords'] = $this->app->Secure->GetPOST('meta_keywords');
	    $input['katalogartikel'] = $this->app->Secure->GetPOST('katalogartikel');
	    $input['katalog_bezeichnung'] = $this->app->Secure->GetPOST('katalog_bezeichnung');
	    $input['katalog_text'] = $this->app->Secure->GetPOST('katalog_text');
	    $input['shop'] = $this->app->Secure->GetPOST('shop');
	    $input['aktiv'] = $this->app->Secure->GetPOST('aktiv')?'1':'0';
	
        return $input;
    }
 }
