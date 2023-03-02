<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Kontorahmen {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "kontorahmen_list");        
        $this->app->ActionHandler("create", "kontorahmen_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "kontorahmen_edit");
        $this->app->ActionHandler("delete", "kontorahmen_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "kontorahmen_list":
                $allowed['kontorahmen_list'] = array('list');
                $heading = array('',  'Sachkonto', 'Beschriftung', 'Art', 'Bemerkung', 'Projekt', 'Ausblenden', 'Men&uuml;');
                $width = array(  '1%','2%' ,       '10%',          '2%',  '10%',       '2%',      '1%',         '1%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $art = "CASE 
                        WHEN k.art = 1 THEN 'Aufwendungen'
                        WHEN k.art = 2 THEN 'Erl&ouml;se'
                        WHEN k.art = 3 THEN 'Geldtransit'
                        WHEN k.art = 9 THEN 'Saldo'
                        ELSE ''                   
                    END";

                $findcols = array('','k.sachkonto', 'k.beschriftung', "($art)",'k.bemerkung', '(SELECT abkuerzung FROM projekt WHERE projekt.id = k.projekt LIMIT 1)', 'k.ausblenden');
                $searchsql = array('k.sachkonto', 'k.beschriftung', 'k.bemerkung', 'k.art', 'k.projekt');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		        $dropnbox = "CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',k.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=kontorahmen&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=kontorahmen&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS 
                    k.id, 
                    $dropnbox, 
                    if(k.ausblenden,CONCAT('<strike>', k.sachkonto,'</strike>'),k.sachkonto) AS sachkonto, 
                    k.beschriftung, 
                    $art
                    AS art, 
                    k.bemerkung, 
                    (SELECT abkuerzung FROM projekt WHERE projekt.id = k.projekt LIMIT 1), 
                    k.ausblenden,
                    k.id 
                    FROM kontorahmen k";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM kontorahmen WHERE $where";
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
    
    function kontorahmen_list() {

        // Process multi action
        $auswahl = $this->app->Secure->GetPOST('auswahl');
        $selectedIds = [];
        if(!empty($auswahl)) {
          foreach($auswahl as $selectedId) {
            $selectedId = (int)$selectedId;
            if($selectedId > 0) {
              $selectedIds[] = $selectedId;
            }
          }          

          $sql = "DELETE FROM kontorahmen";
          $sql .= " WHERE id IN (".implode(",",$selectedIds).")";
          $this->app->DB->Update($sql);
        }

        $this->app->erp->MenuEintrag("index.php?module=kontorahmen&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=kontorahmen&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'kontorahmen_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "kontorahmen_list.tpl");
    }    

    public function kontorahmen_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("DELETE FROM `kontorahmen` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->kontorahmen_list();
    } 

    /*
     * Edit kontorahmen item
     * If id is empty, create a new one
     */
        
    function kontorahmen_edit() {
        $id = $this->app->Secure->GetGET('id');
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('artikel',$id))
        {
          return;
        }   
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=kontorahmen&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=kontorahmen&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
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

            $sql = "INSERT INTO kontorahmen (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=kontorahmen&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

        // Load values again from database
    	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',k.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS k.id, $dropnbox, k.sachkonto, k.beschriftung, k.bemerkung, k.ausblenden, k.art, k.projekt, k.id FROM kontorahmen k"." WHERE id=$id");

        $result[0]['projekt'] = $this->app->erp->ReplaceProjekt(false,$result[0]['projekt'],false); // Parameters: Target db?, value, from form?        

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


      	$this->app->Tpl->Set('AUSBLENDEN', $result[0]['ausblenden']==1?"checked":"");
        $this->app->YUI->AutoComplete("projekt","projektname",1);

        $art_array = array(
            '1' => 'Aufwendungen',
            '2' => 'Erl&ouml;se',
            '3' => 'Geldtransit',
            '9' => 'Saldo'
        );
      	$this->app->Tpl->Set('ART', $this->app->erp->GetSelectAsso($art_array,$result[0]['art']));

//        $this->SetInput($input);              
        $this->app->Tpl->Parse('PAGE', "kontorahmen_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['sachkonto'] = $this->app->Secure->GetPOST('sachkonto');
	    $input['beschriftung'] = $this->app->Secure->GetPOST('beschriftung');
	    $input['bemerkung'] = $this->app->Secure->GetPOST('bemerkung');
	    $input['ausblenden'] = !empty($this->app->Secure->GetPOST('ausblenden'))?"1":"0"; 
	    $input['art'] = $this->app->Secure->GetPOST('art');
	    $input['projekt'] = $this->app->Secure->GetPOST('projekt');

        return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);        
        
        $this->app->Tpl->Set('SACHKONTO', $input['sachkonto']);
	$this->app->Tpl->Set('BESCHRIFTUNG', $input['beschriftung']);
	$this->app->Tpl->Set('BEMERKUNG', $input['bemerkung']);
	$this->app->Tpl->Set('AUSBLENDEN', $input['ausblenden']);
	$this->app->Tpl->Set('ART', $input['art']);
	$this->app->Tpl->Set('PROJEKT', $input['projekt']);
	
    }

}
