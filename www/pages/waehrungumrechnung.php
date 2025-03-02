<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Waehrungumrechnung {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "waehrung_umrechnung_list");        
        $this->app->ActionHandler("create", "waehrung_umrechnung_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "waehrung_umrechnung_edit");
        $this->app->ActionHandler("delete", "waehrung_umrechnung_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "waehrung_umrechnung_list":
                $allowed['waehrung_umrechnung_list'] = array('list');
                $heading = array('','','W&auml;hrung von', 'W&auml;hrung nach', 'Kurs', 'G&uuml;ltig bis', 'Ge&auml;ndert am', 'Bearbeiter', 'Kommentar', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('id','id','w.waehrung_von', 'w.waehrung_nach', 'w.kurs', 'w.gueltig_bis', 'w.zeitstempel', 'w.bearbeiter', 'w.kommentar');
                $searchsql = array('w.waehrung_von', 'w.waehrung_nach', 'w.kurs', 'w.gueltig_bis', 'w.zeitstempel', 'w.bearbeiter', 'w.kommentar');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',w.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=waehrungumrechnung&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=waehrungumrechnung&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS w.id, $dropnbox, w.waehrung_von, w.waehrung_nach, ".$app->erp->FormatMenge('w.kurs',4).", ".$app->erp->FormatDate("w.gueltig_bis").", ".$app->erp->FormatDateTime('w.zeitstempel').", w.bearbeiter, w.kommentar, w.id FROM waehrung_umrechnung w";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM waehrung_umrechnung WHERE $where";
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
    
    function waehrung_umrechnung_list() {

        $this->app->erp->MenuEintrag("index.php?module=waehrungumrechnung&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=waehrungumrechnung&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'waehrung_umrechnung_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "waehrungumrechnung_list.tpl");
    }    

    public function waehrung_umrechnung_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("DELETE FROM `waehrung_umrechnung` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->waehrung_umrechnung_list();
    } 

    /*
     * Edit waehrung_umrechnung item
     * If id is empty, create a new one
     */
        
    function waehrung_umrechnung_edit() {
        $id = $this->app->Secure->GetGET('id');
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('artikel',$id))
        {
          return;
        }   
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=waehrungumrechnung&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=waehrungumrechnung&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
        $id = $this->app->Secure->GetGET('id');
        $input = $this->GetInput();
        $submit = $this->app->Secure->GetPOST('submit');

        $input['gueltig_bis'] = $this->app->erp->ReplaceDatum(true,$input['gueltig_bis'],true);
                
        if (empty($id)) {
            // New item
            $id = 'NULL';
        } 

        if ($submit != '')
        {

            // Write to database
            
            // Add checks here

            $input['bearbeiter'] = $this->app->DB->real_escape_string($this->app->User->GetName());
            $input['zeitstempel'] = date('Y-m-d H:m:s');

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

            $sql = "INSERT INTO waehrung_umrechnung (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=waehrungumrechnung&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

    
        // Load values again from database
	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',w.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS w.id, $dropnbox, w.waehrung_von, w.waehrung_nach, w.kurs, w.gueltig_bis, w.zeitstempel, w.bearbeiter, w.kommentar, w.id FROM waehrung_umrechnung w"." WHERE id=$id");

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

        $this->app->YUI->DatePicker("gueltig_bis");
        $this->app->Tpl->Set('GUELTIG_BIS',$this->app->erp->ReplaceDatum(false,$result[0]['gueltig_bis'],true));
    
        $this->app->Tpl->Set('WAEHRUNG_VON',$this->app->erp->getSelectAsso($this->app->erp->GetWaehrung(), $result[0]['waehrung_von']));
        $this->app->Tpl->Set('WAEHRUNG_NACH',$this->app->erp->getSelectAsso($this->app->erp->GetWaehrung(), $result[0]['waehrung_nach']));

        $this->app->Tpl->Parse('PAGE', "waehrungumrechnung_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['waehrung_von'] = $this->app->Secure->GetPOST('waehrung_von');
	$input['waehrung_nach'] = $this->app->Secure->GetPOST('waehrung_nach');
	$input['kurs'] = $this->app->Secure->GetPOST('kurs');
	$input['gueltig_bis'] = $this->app->Secure->GetPOST('gueltig_bis');
	$input['zeitstempel'] = $this->app->Secure->GetPOST('zeitstempel');
	$input['bearbeiter'] = $this->app->Secure->GetPOST('bearbeiter');
	$input['kommentar'] = $this->app->Secure->GetPOST('kommentar');
	

        return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);        
        
        $this->app->Tpl->Set('WAEHRUNG_VON', $input['waehrung_von']);
	$this->app->Tpl->Set('WAEHRUNG_NACH', $input['waehrung_nach']);
	$this->app->Tpl->Set('KURS', $input['kurs']);
	$this->app->Tpl->Set('GUELTIG_BIS', $input['gueltig_bis']);
	$this->app->Tpl->Set('ZEITSTEMPEL', $input['zeitstempel']);
	$this->app->Tpl->Set('BEARBEITER', $input['bearbeiter']);
	$this->app->Tpl->Set('KOMMENTAR', $input['kommentar']);
	
    }

    public function GetWaehrungUmrechnungskurs($von, $nach, $onlytable) {
        $result = $this->app->DB->Select("SELECT kurs FROM waehrung_umrechnung WHERE waehrung_von = '$von' AND waehrung_nach = '$nach' AND gueltig_bis is NULL");
        if (!is_null($result)) {
            return $result;
        }
        return 0;
    }
}
