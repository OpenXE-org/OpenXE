<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Warteschlangen {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "warteschlangen_list");        
        $this->app->ActionHandler("create", "warteschlangen_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "warteschlangen_edit");
        $this->app->ActionHandler("delete", "warteschlangen_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "warteschlangen_list":
                $allowed['warteschlangen_list'] = array('list');
                $heading = array('Name', 'Kennung', 'Verantwortlicher', 'Men&uuml;');
                $width = array('10%'); // Fill out manually later

                $findcols = array('warteschlange', 'label',  'adresse');
                $searchsql = array('warteschlange', 'label', 'adresse');

                $defaultorder = 1;
                $defaultorderdesc = 0;

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=warteschlangen&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=warteschlangen&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS w.id, w.warteschlange, w.label, (SELECT a.name from adresse a WHERE a.id = w.adresse), id FROM warteschlangen w";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM warteschlangen WHERE $where";
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
    
    function warteschlangen_list() {
        $this->app->erp->MenuEintrag("index.php?module=warteschlangen&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=warteschlangen&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'warteschlangen_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "warteschlangen_list.tpl");
    }    

    public function warteschlangen_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("DELETE FROM `warteschlangen` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->warteschlangen_list();
    } 

    /*
     * Edit warteschlangen item
     * If id is empty, create a new one
     */
        
    function warteschlangen_edit() {
        $id = $this->app->Secure->GetGET('id');
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=warteschlangen&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=warteschlangen&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
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
            $input['adresse'] = $this->app->erp->ReplaceAdresse(true,$input['adresse'],true); // Parameters: Target db?, value, from form?

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

            $sql = "INSERT INTO warteschlangen (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=warteschlangen&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

        // Load values again from database
        $result = $this->app->DB->SelectArr("SELECT id, warteschlange, label, wiedervorlage, adresse, id FROM warteschlangen"." WHERE id=$id");

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);   
        }


        $this->app->YUI->AutoComplete("adresse","mitarbeiterid");
        $this->app->Tpl->Set('ADRESSE', $this->app->erp->ReplaceAdresse(false,$result[0]['adresse'],false)); // Convert ID to form display

             
        /*
         * Add displayed items later
         * 

        $this->app->Tpl->Add('KURZUEBERSCHRIFT2', $email);
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);         
         */

        $this->app->Tpl->Parse('PAGE', "warteschlangen_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['warteschlange'] = $this->app->Secure->GetPOST('warteschlange');
      	$input['label'] = $this->app->Secure->GetPOST('label');
      	$input['wiedervorlage'] = $this->app->Secure->GetPOST('wiedervorlage');
      	$input['adresse'] = $this->app->Secure->GetPOST('adresse');
	
        return $input;
    }


}
