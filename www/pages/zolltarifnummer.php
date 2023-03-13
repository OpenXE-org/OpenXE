<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Zolltarifnummer {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "zolltarifnummer_list");        
        $this->app->ActionHandler("create", "zolltarifnummer_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "zolltarifnummer_edit");
        $this->app->ActionHandler("delete", "zolltarifnummer_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "zolltarifnummer_list":
                $allowed['zolltarifnummer_list'] = array('list');
                $heading = array('','','Nummer', 'Beschreibung', 'Interne Bemerkung', 'Men&uuml;');
                $width = array('1%','1%','30%','30%','30%','1%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('z.id','z.id','z.nummer', 'z.beschreibung', 'z.internebemerkung');
                $searchsql = array('z.nummer', 'z.beschreibung', 'z.internebemerkung');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',z.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=zolltarifnummer&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=zolltarifnummer&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS z.id, $dropnbox, z.nummer, z.beschreibung, z.internebemerkung, z.id FROM zolltarifnummer z";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM zolltarifnummer WHERE $where";
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
    
    function zolltarifnummer_list() {
        $this->app->erp->MenuEintrag("index.php?module=zolltarifnummer&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=zolltarifnummer&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'zolltarifnummer_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "zolltarifnummer_list.tpl");
    }    

    public function zolltarifnummer_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("DELETE FROM `zolltarifnummer` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->zolltarifnummer_list();
    } 

    /*
     * Edit zolltarifnummer item
     * If id is empty, create a new one
     */
        
    function zolltarifnummer_edit() {
        $id = $this->app->Secure->GetGET('id');
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('artikel',$id))
        {
          return;
        }   
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=zolltarifnummer&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=zolltarifnummer&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
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

            $sql = "INSERT INTO zolltarifnummer (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=zolltarifnummer&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

    
        // Load values again from database
	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',z.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS z.id, $dropnbox, z.nummer, z.beschreibung, z.internebemerkung, z.id FROM zolltarifnummer z"." WHERE id=$id");

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

//        $this->SetInput($input);              
        $this->app->Tpl->Parse('PAGE', "zolltarifnummer_edit.tpl");
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

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);        
        
        $this->app->Tpl->Set('NUMMER', $input['nummer']);
	$this->app->Tpl->Set('BESCHREIBUNG', $input['beschreibung']);
	$this->app->Tpl->Set('INTERNEBEMERKUNG', $input['internebemerkung']);
	
    }

}
