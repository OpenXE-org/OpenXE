<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Uebersetzung {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "uebersetzung_list");        
        $this->app->ActionHandler("create", "uebersetzung_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "uebersetzung_edit");
        $this->app->ActionHandler("delete", "uebersetzung_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    public function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "uebersetzung_list":
                $allowed['uebersetzung_list'] = array('list');

                // Transfer a parameter from form -> see below for setting of parameter
                // $parameter = $this->app->User->GetParameter('parameter');

                $heading = array('','Label', 'Sprache','&Uuml;bersetzung', 'Original', 'Men&uuml;');
                $width = array('1%','5%','5%','20%','20%','1%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('id','u.label', 'u.sprache', 'u.beschriftung', 'u.original');
                $searchsql = array('u.label', 'u.beschriftung', 'u.sprache', 'u.original');

                $defaultorder = 1;
                $defaultorderdesc = 0;

                // Some options for the columns:
                //                $numbercols = array(1,2);
                //                $sumcol = array(1,2);
                //                $alignright = array(1,2);                                

        		$dropnbox = "CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',u.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=uebersetzung&action=edit&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=uebersetzung&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS 
                    u.id,
            		$dropnbox,
                    u.label, 
                    u.sprache, 
                    if( CHAR_LENGTH(u.beschriftung) > 100,
                        CONCAT('<span style=\"word-wrap:anywhere;\">',u.beschriftung,'</span>'),
                        u.beschriftung)
                    as beschriftung,
                    if( CHAR_LENGTH(u.original) > 100,
                        CONCAT('<span style=\"word-wrap:anywhere;\">',u.original,'</span>'),
                        u.original)
                    as original,
                    u.id FROM uebersetzung u";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM uebersetzung WHERE $where";
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
    
    function uebersetzung_list() {

        // For transfer of form parameter to tablesearch   
        // $parameter = $this->app->Secure->GetPOST('parameter');
        // $this->app->User->SetParameter('parameter', $parameter);

        $this->app->erp->MenuEintrag("index.php?module=uebersetzung&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=uebersetzung&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'uebersetzung_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "uebersetzung_list.tpl");
    }    

    public function uebersetzung_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("DELETE FROM `uebersetzung` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->uebersetzung_list();
    } 

    /*
     * Edit uebersetzung item
     * If id is empty, create a new one
     */
        
    function uebersetzung_edit() {
        $id = $this->app->Secure->GetGET('id');
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('artikel',$id))
        {
          return;
        }   
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=uebersetzung&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=uebersetzung&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
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

            $sql = "INSERT INTO uebersetzung (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=uebersetzung&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

    
        // Load values again from database
	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',u.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS u.id, $dropnbox, u.label, u.beschriftung, u.sprache, u.original, u.id FROM uebersetzung u"." WHERE id=$id");

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

        $sprachen = $this->app->erp->GetSprachenSelect();

        foreach ($sprachen as $key => $value) {
            $this->app->Tpl->Add('SPRACHENSELECT', "<option value='".$key."'>".$value."</option>");
        }

//        $this->SetInput($input);              
        $this->app->Tpl->Parse('PAGE', "uebersetzung_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['label'] = $this->app->Secure->GetPOST('label');
	$input['beschriftung'] = $this->app->Secure->GetPOST('beschriftung');
	$input['sprache'] = $this->app->Secure->GetPOST('sprache');
	$input['original'] = $this->app->Secure->GetPOST('original');
	

        return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);        
        
        $this->app->Tpl->Set('LABEL', $input['label']);
	$this->app->Tpl->Set('BESCHRIFTUNG', $input['beschriftung']);
	$this->app->Tpl->Set('SPRACHE', $input['sprache']);
	$this->app->Tpl->Set('ORIGINAL', $input['original']);
	
    }

}
