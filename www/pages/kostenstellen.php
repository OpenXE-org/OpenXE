<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Kostenstellen {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "kostenstellen_list");        
        $this->app->ActionHandler("create", "kostenstellen_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "kostenstellen_edit");
        $this->app->ActionHandler("delete", "kostenstellen_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "kostenstellen_list":
                $allowed['kostenstellen_list'] = array('list');
                $heading = array('','','Nummer', 'Beschreibung', 'Internebemerkung', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('k.id','k.id','k.nummer', 'k.beschreibung', 'k.internebemerkung'); // use 'null' for non-searchable columns
                $searchsql = array('k.nummer', 'k.beschreibung', 'k.internebemerkung');

                $defaultorder = 1;
                $defaultorderdesc = 0;
                $aligncenter = array();
                $alignright = array();
                $numbercols = array();
                $sumcol = array();

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',k.id,'\" />') AS `auswahl`";

//                $moreinfo = true; // Allow drop down details
//                $moreinfoaction = "lieferschein"; // specify suffix for minidetail-URL to allow different minidetails
//                $menucol = 11; // Set id col for moredata/menu

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=kostenstellen&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=kostenstellen&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS k.id, $dropnbox, k.nummer, k.beschreibung, k.internebemerkung, k.id FROM kostenstellen k";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM kostenstellen WHERE $where";
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
    
    function kostenstellen_list() {
        $this->app->erp->MenuEintrag("index.php?module=kostenstellen&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=kostenstellen&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'kostenstellen_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "kostenstellen_list.tpl");
    }    

    public function kostenstellen_delete() {
        $id = (int) $this->app->Secure->GetGET('id');     
        $this->app->DB->Delete("DELETE FROM `kostenstellen` WHERE `id` = '{$id}'");        
        $this->app->Tpl->addMessage('error', 'Der Eintrag wurde gel&ouml;scht');        
        $this->kostenstellen_list();
    } 

    /*
     * Edit kostenstellen item
     * If id is empty, create a new one
     */
        
    function kostenstellen_edit() {
        $id = $this->app->Secure->GetGET('id');
        
/*        // Check if other users are editing this id
        if($this->app->erp->DisableModul('kostenstellen',$id))
        {
          return;
        } */
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=kostenstellen&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=kostenstellen&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
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

            $sql = "INSERT INTO kostenstellen (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=kostenstellen&action=list&msg=$msg");
            } else {
                $this->app->Tpl->addMessage('success', 'Die Einstellungen wurden erfolgreich &uuml;bernommen.');
            }
        }

    
        // Load values again from database
        if ($id != 'NULL') {

        	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',k.id,'\" />') AS `auswahl`";
            $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS k.id, $dropnbox, k.nummer, k.beschreibung, k.internebemerkung, k.id FROM kostenstellen k"." WHERE id=$id");        

            foreach ($result[0] as $key => $value) {
                $this->app->Tpl->Set(strtoupper($key), $value);   
            }

            if (!empty($result)) {
                $kostenstellen_from_db = $result[0];
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
        $this->app->Tpl->Set('PROJEKT',$this->app->erp->ReplaceProjekt(false,$kostenstellen_from_db['projekt'],false));
      	$this->app->Tpl->Set('PRIO', $kostenstellen_from_db['prio']==1?"checked":"");

         */

        $this->app->Tpl->Parse('PAGE', "kostenstellen_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['nummer'] = $this->app->Secure->GetPOST('nummer');
	$input['beschreibung'] = $this->app->Secure->GetPOST('beschreibung');
	$input['internebemerkung'] = $this->app->Secure->GetPOST('internebemerkung');
	

        return $input;
    }
 }
