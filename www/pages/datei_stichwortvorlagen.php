<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Datei_stichwortvorlagen {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "datei_stichwortvorlagen_list");        
        $this->app->ActionHandler("create", "datei_stichwortvorlagen_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "datei_stichwortvorlagen_edit");
        $this->app->ActionHandler("delete", "datei_stichwortvorlagen_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "datei_stichwortvorlagen_list":
                $allowed['datei_stichwortvorlagen_list'] = array('list');
                $heading = array('','','Beschriftung', 'Ausblenden', 'Modul', 'Kennung', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('d.id','d.id','d.beschriftung', 'd.ausblenden', 'd.modul', 'd.kennung'); // use 'null' for non-searchable columns
                $searchsql = array('d.beschriftung', 'd.ausblenden', 'd.modul', 'd.kennung');

                $defaultorder = 1;
                $defaultorderdesc = 0;
                $aligncenter = array();
                $alignright = array();
                $numbercols = array();
                $sumcol = array();

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',d.id,'\" />') AS `auswahl`";

//                $moreinfo = true; // Allow drop down details
//                $moreinfoaction = "lieferschein"; // specify suffix for minidetail-URL to allow different minidetails
//                $menucol = 11; // Set id col for moredata/menu

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=datei_stichwortvorlagen&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=datei_stichwortvorlagen&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS d.id, $dropnbox, d.beschriftung, d.ausblenden, d.modul, d.kennung, d.id FROM datei_stichwortvorlagen d";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM datei_stichwortvorlagen WHERE $where";
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
    
    function datei_stichwortvorlagen_list() {
        $this->app->erp->MenuEintrag("index.php?module=datei_stichwortvorlagen&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=datei_stichwortvorlagen&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'datei_stichwortvorlagen_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "datei_stichwortvorlagen_list.tpl");
    }    

    public function datei_stichwortvorlagen_delete() {
        $id = (int) $this->app->Secure->GetGET('id');             
        $this->app->DB->Update("UPDATE `datei_stichwortvorlagen` SET ausblenden = 1 WHERE `id` = '{$id}'");        
        $this->app->Tpl->addMessage('info', 'Der Eintrag wurde ausgeblendet');
        $this->datei_stichwortvorlagen_list();
    } 

    /*
     * Edit datei_stichwortvorlagen item
     * If id is empty, create a new one
     */
        
    function datei_stichwortvorlagen_edit() {
        $id = $this->app->Secure->GetGET('id');
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('datei_stichwortvorlagen',$id))
        {
          return;
        }   
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=datei_stichwortvorlagen&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=datei_stichwortvorlagen&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
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

            $sql = "SELECT kennung FROM datei_stichwortvorlagen dsv INNER JOIN datei_stichwoerter ds ON dsv.kennung = ds.subjekt WHERE dsv.id ='".$id."'";
            $kennung = $this->app->DB->Select($sql);
            if (!empty($kennung)) {
                $input['kennung'] = $kennung;
                $this->app->Tpl->addMessage('info', 'Kennung in Verwendung');
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

            $sql = "INSERT INTO datei_stichwortvorlagen (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=datei_stichwortvorlagen&action=list&msg=$msg");
            } else {
                $this->app->Tpl->addMessage('success', 'Die Einstellungen wurden erfolgreich &uuml;bernommen.');
            }
        }
   
        // Load values again from database
        if ($id != 'NULL') {

        	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',d.id,'\" />') AS `auswahl`";
            $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS d.id, $dropnbox, d.beschriftung, d.ausblenden, d.modul, d.kennung, d.id FROM datei_stichwortvorlagen d"." WHERE id=$id");        

            foreach ($result[0] as $key => $value) {
                $this->app->Tpl->Set(strtoupper($key), $value);   
            }

            if (!empty($result)) {
                $datei_stichwortvorlagen_from_db = $result[0];

              	$this->app->Tpl->Set('AUSBLENDEN', $datei_stichwortvorlagen_from_db['ausblenden']==1?"checked":"");

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
        $this->app->Tpl->Set('PROJEKT',$this->app->erp->ReplaceProjekt(false,$datei_stichwortvorlagen_from_db['projekt'],false));
      	$this->app->Tpl->Set('PRIO', $datei_stichwortvorlagen_from_db['prio']==1?"checked":"");

         */

        $this->app->Tpl->Parse('PAGE', "datei_stichwortvorlagen_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['beschriftung'] = $this->app->Secure->GetPOST('beschriftung');
    	$input['ausblenden'] = !empty($this->app->Secure->GetPOST('ausblenden'))?"1":"0";
    	$input['modul'] = $this->app->Secure->GetPOST('modul');
    	$input['kennung'] = $this->app->Secure->GetPOST('kennung');

        return $input;
    }
 }
