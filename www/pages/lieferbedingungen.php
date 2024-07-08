<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Lieferbedingungen {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "lieferbedingungen_list");        
        $this->app->ActionHandler("create", "lieferbedingungen_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "lieferbedingungen_edit");
        $this->app->ActionHandler("delete", "lieferbedingungen_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "lieferbedingungen_list":
                $allowed['lieferbedingungen_list'] = array('list');
                $heading = array('','','Lieferbedingungen', 'Kennzeichen', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('l.id','l.id','l.lieferbedingungen', 'l.kennzeichen');
                $searchsql = array('l.lieferbedingungen', 'l.kennzeichen');

                $defaultorder = 1;
                $defaultorderdesc = 0;
                $aligncenter = array();
                $alignright = array();
                $numbercols = array();
                $sumcol = array();

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',l.id,'\" />') AS `auswahl`";

//                $moreinfo = true; // Allow drop down details
//                $moreinfoaction = "lieferschein"; // specify suffix for minidetail-URL to allow different minidetails
//                $menucol = 11; // Set id col for moredata/menu

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=lieferbedingungen&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=lieferbedingungen&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, $dropnbox, l.lieferbedingungen, l.kennzeichen, l.id FROM lieferbedingungen l";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM lieferbedingungen WHERE $where";
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
    
    function lieferbedingungen_list() {
        $this->app->erp->MenuEintrag("index.php?module=lieferbedingungen&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=lieferbedingungen&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'lieferbedingungen_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "lieferbedingungen_list.tpl");
    }    

    public function lieferbedingungen_delete() {
        $id = (int) $this->app->Secure->GetGET('id');     
        $this->app->DB->Delete("DELETE FROM `lieferbedingungen` WHERE `id` = '{$id}'");        
        $this->app->Tpl->addMessage('error', 'Der Eintrag wurde gel&ouml;scht');        
        $this->lieferbedingungen_list();
    } 

    /*
     * Edit lieferbedingungen item
     * If id is empty, create a new one
     */
        
    function lieferbedingungen_edit() {
        $id = $this->app->Secure->GetGET('id');
        
/*        // Check if other users are editing this id
        if($this->app->erp->DisableModul('lieferbedingungen',$id))
        {
          return;
        }   */
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=lieferbedingungen&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=lieferbedingungen&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
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

            $sql = "INSERT INTO lieferbedingungen (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=lieferbedingungen&action=list&msg=$msg");
            } else {
                $this->app->Tpl->addMessage('success', 'Die Einstellungen wurden erfolgreich &uuml;bernommen.');
            }
        }

    
        // Load values again from database
        if ($id != 'NULL') {

        	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',l.id,'\" />') AS `auswahl`";
            $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS l.id, $dropnbox, l.lieferbedingungen, l.kennzeichen, l.id FROM lieferbedingungen l"." WHERE id=$id");        

            foreach ($result[0] as $key => $value) {
                $this->app->Tpl->Set(strtoupper($key), $value);   
            }

            if (!empty($result)) {
                $lieferbedingungen_from_db = $result[0];
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
        $this->app->Tpl->Set('PROJEKT',$this->app->erp->ReplaceProjekt(false,$lieferbedingungen_from_db['projekt'],false));
      	$this->app->Tpl->Set('PRIO', $lieferbedingungen_from_db['prio']==1?"checked":"");

         */

        $this->app->Tpl->Parse('PAGE', "lieferbedingungen_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['lieferbedingungen'] = $this->app->Secure->GetPOST('lieferbedingungen');
	$input['kennzeichen'] = $this->app->Secure->GetPOST('kennzeichen');
	

        return $input;
    }
 }
