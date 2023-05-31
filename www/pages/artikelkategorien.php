<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Artikelkategorien {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "artikelkategorien_list");        
        $this->app->ActionHandler("create", "artikelkategorien_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "artikelkategorien_edit");
        $this->app->ActionHandler("delete", "artikelkategorien_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "artikelkategorien_list":
                $allowed['artikelkategorien_list'] = array('list');
                $heading = array('','','Bezeichnung', 'Projekt','Gel&ouml;scht', 'Men&uuml;');
                $width = array('1%','1%','30%','10%','1%','1%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('a.id','a.id','a.bezeichnung', 'a.projekt'); 
                $searchsql = array('a.bezeichnung');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',a.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=artikelkategorien&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikelkategorien&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, $dropnbox, a.bezeichnung, p.abkuerzung, a.geloescht, a.id FROM artikelkategorien a LEFT JOIN projekt p ON a.projekt = p.id";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM artikelkategorien WHERE $where";
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
    
    function artikelkategorien_list() {
        $this->app->erp->MenuEintrag("index.php?module=artikelkategorien&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=artikelkategorien&action=create", "Neu anlegen");
        $this->app->erp->MenuEintrag("index.php?module=artikelbaum&action=list", "Artikelbaum");

//        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'artikelkategorien_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "artikelkategorien_list.tpl");
    }    

    public function artikelkategorien_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("DELETE FROM `artikelkategorien` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->artikelkategorien_list();
    } 

    /*
     * Edit artikelkategorien item
     * If id is empty, create a new one
     */
        
    function artikelkategorien_edit() {
        $id = $this->app->Secure->GetGET('id');
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('artikel',$id))
        {
          return;
        }   
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=artikelkategorien&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=artikelkategorien&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
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
            $input['projekt'] = $this->app->erp->ReplaceProjekt(true,$input['projekt'],true); // Parameters: Target db?, value, from form?

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

            $sql = "INSERT INTO artikelkategorien (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=artikelkategorien&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

    
        // Load values again from database
	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',a.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS a.id, $dropnbox, a.bezeichnung, a.next_nummer, a.projekt, a.geloescht, a.externenummer, a.parent, a.steuer_erloese_inland_normal, a.steuer_aufwendung_inland_normal, a.steuer_erloese_inland_ermaessigt, a.steuer_aufwendung_inland_ermaessigt, a.steuer_erloese_inland_steuerfrei, a.steuer_aufwendung_inland_steuerfrei, a.steuer_erloese_inland_innergemeinschaftlich, a.steuer_aufwendung_inland_innergemeinschaftlich, a.steuer_erloese_inland_eunormal, a.steuer_erloese_inland_nichtsteuerbar, a.steuer_erloese_inland_euermaessigt, a.steuer_aufwendung_inland_nichtsteuerbar, a.steuer_aufwendung_inland_eunormal, a.steuer_aufwendung_inland_euermaessigt, a.steuer_erloese_inland_export, a.steuer_aufwendung_inland_import, a.steuertext_innergemeinschaftlich, a.steuertext_export, a.id FROM artikelkategorien a"." WHERE id=$id");

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);   
        }
             
        /*
         * Add displayed items later
         * 

        $this->app->Tpl->Add('KURZUEBERSCHRIFT2', $email);
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);         
         */

        $this->app->Tpl->Set('PROJEKT',$this->app->erp->ReplaceProjekt(false,$result[0]['projekt'],false)); // Parameters: Target db?, value, from form?
        $this->app->YUI->AutoComplete('projekt', 'projektname', 1);

//        $this->SetInput($input);              
        $this->app->Tpl->Parse('PAGE', "artikelkategorien_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['bezeichnung'] = $this->app->Secure->GetPOST('bezeichnung');
	$input['next_nummer'] = $this->app->Secure->GetPOST('next_nummer');
	$input['projekt'] = $this->app->Secure->GetPOST('projekt');
	$input['geloescht'] = $this->app->Secure->GetPOST('geloescht');
	$input['externenummer'] = $this->app->Secure->GetPOST('externenummer');
	$input['parent'] = $this->app->Secure->GetPOST('parent');
	$input['steuer_erloese_inland_normal'] = $this->app->Secure->GetPOST('steuer_erloese_inland_normal');
	$input['steuer_aufwendung_inland_normal'] = $this->app->Secure->GetPOST('steuer_aufwendung_inland_normal');
	$input['steuer_erloese_inland_ermaessigt'] = $this->app->Secure->GetPOST('steuer_erloese_inland_ermaessigt');
	$input['steuer_aufwendung_inland_ermaessigt'] = $this->app->Secure->GetPOST('steuer_aufwendung_inland_ermaessigt');
	$input['steuer_erloese_inland_steuerfrei'] = $this->app->Secure->GetPOST('steuer_erloese_inland_steuerfrei');
	$input['steuer_aufwendung_inland_steuerfrei'] = $this->app->Secure->GetPOST('steuer_aufwendung_inland_steuerfrei');
	$input['steuer_erloese_inland_innergemeinschaftlich'] = $this->app->Secure->GetPOST('steuer_erloese_inland_innergemeinschaftlich');
	$input['steuer_aufwendung_inland_innergemeinschaftlich'] = $this->app->Secure->GetPOST('steuer_aufwendung_inland_innergemeinschaftlich');
	$input['steuer_erloese_inland_eunormal'] = $this->app->Secure->GetPOST('steuer_erloese_inland_eunormal');
	$input['steuer_erloese_inland_nichtsteuerbar'] = $this->app->Secure->GetPOST('steuer_erloese_inland_nichtsteuerbar');
	$input['steuer_erloese_inland_euermaessigt'] = $this->app->Secure->GetPOST('steuer_erloese_inland_euermaessigt');
	$input['steuer_aufwendung_inland_nichtsteuerbar'] = $this->app->Secure->GetPOST('steuer_aufwendung_inland_nichtsteuerbar');
	$input['steuer_aufwendung_inland_eunormal'] = $this->app->Secure->GetPOST('steuer_aufwendung_inland_eunormal');
	$input['steuer_aufwendung_inland_euermaessigt'] = $this->app->Secure->GetPOST('steuer_aufwendung_inland_euermaessigt');
	$input['steuer_erloese_inland_export'] = $this->app->Secure->GetPOST('steuer_erloese_inland_export');
	$input['steuer_aufwendung_inland_import'] = $this->app->Secure->GetPOST('steuer_aufwendung_inland_import');
	$input['steuertext_innergemeinschaftlich'] = $this->app->Secure->GetPOST('steuertext_innergemeinschaftlich');
	$input['steuertext_export'] = $this->app->Secure->GetPOST('steuertext_export');
	

        return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);        
        
        $this->app->Tpl->Set('BEZEICHNUNG', $input['bezeichnung']);
	$this->app->Tpl->Set('NEXT_NUMMER', $input['next_nummer']);
	$this->app->Tpl->Set('PROJEKT', $input['projekt']);
	$this->app->Tpl->Set('GELOESCHT', $input['geloescht']);
	$this->app->Tpl->Set('EXTERNENUMMER', $input['externenummer']);
	$this->app->Tpl->Set('PARENT', $input['parent']);
	$this->app->Tpl->Set('STEUER_ERLOESE_INLAND_NORMAL', $input['steuer_erloese_inland_normal']);
	$this->app->Tpl->Set('STEUER_AUFWENDUNG_INLAND_NORMAL', $input['steuer_aufwendung_inland_normal']);
	$this->app->Tpl->Set('STEUER_ERLOESE_INLAND_ERMAESSIGT', $input['steuer_erloese_inland_ermaessigt']);
	$this->app->Tpl->Set('STEUER_AUFWENDUNG_INLAND_ERMAESSIGT', $input['steuer_aufwendung_inland_ermaessigt']);
	$this->app->Tpl->Set('STEUER_ERLOESE_INLAND_STEUERFREI', $input['steuer_erloese_inland_steuerfrei']);
	$this->app->Tpl->Set('STEUER_AUFWENDUNG_INLAND_STEUERFREI', $input['steuer_aufwendung_inland_steuerfrei']);
	$this->app->Tpl->Set('STEUER_ERLOESE_INLAND_INNERGEMEINSCHAFTLICH', $input['steuer_erloese_inland_innergemeinschaftlich']);
	$this->app->Tpl->Set('STEUER_AUFWENDUNG_INLAND_INNERGEMEINSCHAFTLICH', $input['steuer_aufwendung_inland_innergemeinschaftlich']);
	$this->app->Tpl->Set('STEUER_ERLOESE_INLAND_EUNORMAL', $input['steuer_erloese_inland_eunormal']);
	$this->app->Tpl->Set('STEUER_ERLOESE_INLAND_NICHTSTEUERBAR', $input['steuer_erloese_inland_nichtsteuerbar']);
	$this->app->Tpl->Set('STEUER_ERLOESE_INLAND_EUERMAESSIGT', $input['steuer_erloese_inland_euermaessigt']);
	$this->app->Tpl->Set('STEUER_AUFWENDUNG_INLAND_NICHTSTEUERBAR', $input['steuer_aufwendung_inland_nichtsteuerbar']);
	$this->app->Tpl->Set('STEUER_AUFWENDUNG_INLAND_EUNORMAL', $input['steuer_aufwendung_inland_eunormal']);
	$this->app->Tpl->Set('STEUER_AUFWENDUNG_INLAND_EUERMAESSIGT', $input['steuer_aufwendung_inland_euermaessigt']);
	$this->app->Tpl->Set('STEUER_ERLOESE_INLAND_EXPORT', $input['steuer_erloese_inland_export']);
	$this->app->Tpl->Set('STEUER_AUFWENDUNG_INLAND_IMPORT', $input['steuer_aufwendung_inland_import']);
	$this->app->Tpl->Set('STEUERTEXT_INNERGEMEINSCHAFTLICH', $input['steuertext_innergemeinschaftlich']);
	$this->app->Tpl->Set('STEUERTEXT_EXPORT', $input['steuertext_export']);
	
    }

}
