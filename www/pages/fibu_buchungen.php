<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Fibu_buchungen {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "fibu_buchungen_list");        
        $this->app->ActionHandler("create", "fibu_buchungen_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "fibu_buchungen_edit");
        $this->app->ActionHandler("delete", "fibu_buchungen_delete");
        $this->app->ActionHandler("assoc", "fibu_buchungen_assoc");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);

        $this->app->erp->Headlines('Buchhaltung Buchungen');
    }

    public function Install() {
        /* Fill out manually later */
    }

    function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "fibu_buchungen_list_tabelle":
                $allowed['fibu_buchungen_list'] = array('list');
                $heading = array('','','Von_typ', 'Von_id', 'Nach_typ', 'Nach_id', 'Betrag', 'Waehrung', 'Benutzer', 'Zeit', 'Internebemerkung', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('f.id','f.id','f.von_typ', 'f.von_id', 'f.nach_typ', 'f.nach_id', 'f.betrag', 'f.waehrung', 'f.benutzer', 'f.zeit', 'f.internebemerkung');
                $searchsql = array('f.von_typ', 'f.von_id', 'f.nach_typ', 'f.nach_id', 'f.betrag', 'f.waehrung', 'f.benutzer', 'f.zeit', 'f.internebemerkung');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',f.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=fibu_buchungen&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=fibu_buchungen&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS f.id, $dropnbox, f.von_typ, f.von_id, f.nach_typ, f.nach_id, f.betrag, f.waehrung, f.benutzer, f.zeit, f.internebemerkung, f.id FROM fibu_buchungen f";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM fibu_buchungen WHERE $where";
//                $groupby = "";

                break;
            case "fibu_buchungen_list":
                $allowed['fibu_buchungen_list'] = array('list');

                $doc_typ = $this->app->User->GetParameter('fibu_buchungen_doc_typ');
                $doc_id = $this->app->User->GetParameter('fibu_buchungen_doc_id');

                $heading = array('','','Buchungsart', 'Typ', 'Datum', 'Beleg', 'Betrag', 'W&auml;hrung', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                $alignright = array(7); 
                $sumcol= array(7);

                $findcols = array('f.id','f.id','f.buchungsart', 'f.typ', 'f.datum', 'f.doc_typ', 'f.betrag', 'f.waehrung');
                $searchsql = array('f.buchungsart', 'f.typ', 'f.datum', 'f.doc_typ', 'f.doc_belegnr');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		        $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',f.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?action=edit&module=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a></td></tr></table>";

               if (!empty($doc_typ) && !empty($doc_id)) {
                    $where = "`doc_typ` = '$doc_typ' AND `doc_id` = $doc_id";                
                } else{
                    $where = "1";
                }

                $sql = "SELECT SQL_CALC_FOUND_ROWS 
                    f.id, 
                    $dropnbox, 
                    ".$app->erp->FormatUCfirst('f.buchungsart').",
                    ".$app->erp->FormatUCfirst('f.typ').",
                    ".$app->erp->FormatDate('f.datum').",
                    CONCAT(".$app->erp->FormatUCfirst('f.doc_typ').",' ',f.doc_belegnr),
                    ".$app->erp->FormatMenge('f.betrag',2).",
                    f.waehrung,
                    CONCAT(f.edit_module,'&id=',f.edit_id) 
                FROM fibu_buchungen_alle f";

                $count = "SELECT count(*) FROM fibu_buchungen_alle WHERE $where";
//                $groupby = "";

                break;       
            case "fibu_buchungen_wahl":
                $allowed['fibu_buchungen_wahl'] = array('list');
                $heading = array('',  '',  'Datum', 'Typ', 'Beleg', 'Von','Nach', 'Men&uuml;');
                $width = array(  '1%','1%','1%',  '20%',   '80%',   '1%', '1%',    '%1'   );

                $findcols = array('f.id','f.id','f.typ');
                $searchsql = array('f.buchungsart', 'f.typ', 'f.datum', 'f.doc_typ', 'f.doc_belegnr');

                $defaultorder = 1;
                $defaultorderdesc = 0;

	            $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',f.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?action=edit&module=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a></td></tr></table>";
                $menu = null;

                $linkstart = '<table cellpadding=0 cellspacing=0><tr><td nowrap><a href="index.php?module=fibu_buchungen&action=edit&';
                $linkend = '"><img src="./themes/'.$app->Conf->WFconf['defaulttheme'].'/images/forward.svg" border=0></a></td></tr></table>';

                $id = $app->Secure->GetGET('id');

                $sql = "SELECT SQL_CALC_FOUND_ROWS 
                    f.id, 
                    $dropnbox, 
                    ".$app->erp->FormatDate('f.datum').",
                    ".$app->erp->FormatUCfirst('f.typ').",
                    f.info,
                    CONCAT('".$linkstart."','direction=von&id=".$id."&doc_typ=',f.typ,'&doc_id=',f.id,'".$linkend."'),
                    CONCAT('".$linkstart."','direction=nach&id=".$id."&doc_typ=',f.typ,'&doc_id=',f.id,'".$linkend."'),
                    f.id,
                    f.id 
                FROM fibu_objekte f
                ";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM fibu_buchungen_alle WHERE $where";
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
    
    function fibu_buchungen_list() {
        $this->app->erp->MenuEintrag("index.php?module=fibu_buchungen&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=fibu_buchungen&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        // For transfer to tablesearch    
        $doc_typ = $this->app->Secure->GetGET('doc_typ');
        $doc_id = $this->app->Secure->GetGET('doc_id');

        $this->app->User->SetParameter('fibu_buchungen_doc_typ', $doc_typ);
        $this->app->User->SetParameter('fibu_buchungen_doc_id', $doc_id);

        $this->app->YUI->TableSearch('TAB1', 'fibu_buchungen_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "fibu_buchungen_list.tpl");
    }    

    public function fibu_buchungen_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
//        $this->app->DB->Delete("DELETE FROM `fibu_buchungen` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->fibu_buchungen_list();
    } 

    /*
     * Edit fibu_buchungen item
     * If id is empty, create a new one
     */
        
    function fibu_buchungen_edit() {
        $id = $this->app->Secure->GetGET('id');
       
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('artikel',$id))
        {
          return;
        }   

        // Assoc?
        $direction = $this->app->Secure->GetGET('direction');
        $doc_typ = $this->app->Secure->GetGET('doc_typ');
        $doc_id = $this->app->Secure->GetGET('doc_id');
        if (in_array($direction,array('von','nach'))) {
            $sql = "SELECT typ, id FROM fibu_objekte WHERE typ = '".$doc_typ."' AND id = '".$doc_id."'";

            $result = $this->app->DB->SelectArr($sql);

            if (!empty($result)) {
                $sql = "UPDATE fibu_buchungen SET ".$direction."_typ = '".$doc_typ."', ".$direction."_id = '".$doc_id."' WHERE id = '".$id."'";
                $this->app->DB->Update($sql);
            }
        }
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=fibu_buchungen&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=fibu_buchungen&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
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
            $input['benutzer'] = $this->app->User->GetId();
            $input['zeit'] = date("Y-m-d H:i");
            $input['betrag'] = $this->app->erp->ReplaceBetrag(true,$input['betrag']);
            $input['zeit'] = date("Y-m-d H:i");

            $input['internebemerkung'] = $this->app->DB->real_escape_string($input['internebemerkung']);

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

            $sql = "INSERT INTO fibu_buchungen (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=fibu_buchungen&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

    
        // Load values again from database
     	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',f.id,'\" />') AS `auswahl`";

        $sql = "
            SELECT SQL_CALC_FOUND_ROWS f.id,
                $dropnbox,
                f.von_typ,
                f.von_id,
                f.nach_typ,
                f.nach_id,
                ".$this->app->erp->FormatMenge('f.betrag',2)." AS betrag,
                f.waehrung,
                adresse.name as benutzer,
                ".$this->app->erp->FormatDateTime('f.zeit','zeit').",
                f.internebemerkung,
                f.id,
                fvon.info AS von_info,
                fnach.info AS nach_info
            FROM 
                fibu_buchungen f"." 
            LEFT JOIN
                fibu_objekte fvon
            ON
                f.von_typ = fvon.typ AND f.von_id = fvon.id
            LEFT JOIN
                fibu_objekte fnach
            ON
                f.nach_typ = fnach.typ AND f.nach_id = fnach.id
            LEFT JOIN 
                user
            ON
                f.benutzer = user.id
            LEFT JOIN
                adresse
            ON
                user.adresse = adresse.id
            WHERE 
                f.id=$id
            
        ";

        $result = $this->app->DB->SelectArr($sql);             

        $this->app->erp->ReplaceDatum(false,$result[0]['zeit'],false);

        $result[0]['internebemerkung'] = htmlentities($result[0]['internebemerkung']);

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

        $this->app->Tpl->Set('VON',ucfirst($result[0]['von_typ'])." ".$result[0]['von_info']);
        $this->app->Tpl->Set('NACH',ucfirst($result[0]['nach_typ'])." ".$result[0]['nach_info']);

        $this->app->Tpl->Set('WAEHRUNG',$this->app->erp->getSelectAsso($this->app->erp->GetWaehrung(), $result[0]['waehrung_von']));            

        $this->app->YUI->TableSearch('TAB1', 'fibu_buchungen_wahl', "show", "", "", basename(__FILE__), __CLASS__);

        $this->app->Tpl->Parse('PAGE', "fibu_buchungen_edit.tpl");
    }
     
    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['von_typ'] = $this->app->Secure->GetPOST('von_typ');
	$input['von_id'] = $this->app->Secure->GetPOST('von_id');
	$input['nach_typ'] = $this->app->Secure->GetPOST('nach_typ');
	$input['nach_id'] = $this->app->Secure->GetPOST('nach_id');
	$input['betrag'] = $this->app->Secure->GetPOST('betrag');
	$input['waehrung'] = $this->app->Secure->GetPOST('waehrung');
	$input['benutzer'] = $this->app->Secure->GetPOST('benutzer');
	$input['zeit'] = $this->app->Secure->GetPOST('zeit');
	$input['internebemerkung'] = $this->app->Secure->GetPOST('internebemerkung');
	

        return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);        
        
        $this->app->Tpl->Set('VON_TYP', $input['von_typ']);
	$this->app->Tpl->Set('VON_ID', $input['von_id']);
	$this->app->Tpl->Set('NACH_TYP', $input['nach_typ']);
	$this->app->Tpl->Set('NACH_ID', $input['nach_id']);
	$this->app->Tpl->Set('BETRAG', $input['betrag']);
	$this->app->Tpl->Set('WAEHRUNG', $input['waehrung']);
	$this->app->Tpl->Set('BENUTZER', $input['benutzer']);
	$this->app->Tpl->Set('ZEIT', $input['zeit']);
	$this->app->Tpl->Set('INTERNEBEMERKUNG', $input['internebemerkung']);
	
    }

}
