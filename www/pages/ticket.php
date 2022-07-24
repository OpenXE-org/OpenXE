<?php

/*
 * Copyright (c) 2022 Xenomporio project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Ticket {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "ticket_list");        
        $this->app->ActionHandler("create", "ticket_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "ticket_edit");
        $this->app->ActionHandler("edit_raw", "ticket_edit_raw");
        $this->app->ActionHandler("delete", "ticket_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    function ticket_status_icon(string $status) {
      return('<img src="./themes/new/images/status_'.$status.'.png" style="margin-right:1px" title="'.$status.'" border="0">');
    }


    static function TableSearch(&$app, $name, $erlaubtevars) {


       function ticket_iconssql() {
            return "CONCAT('<img src=\"./themes/new/images/status_',`t`.`status`,'.png\" style=\"margin-right:1px\" title=\"',`t`.`status`,'\" border=\"0\">')";
        }


        switch ($name) {
            case "ticket_list":
                $allowed['ticket_list'] = array('list');
                $heading = array('','','Ticket #', 'Datum', 'Adresse', 'Betreff', 'Notiz', 'Tags', 'Verantwortlich', 'Anzahl Nachrichten', 'Status', 'Alter', 'Projekt', 'Men&uuml;');
                $width = array('1%','1%','5%',     '5%',    '5%',      '20%',      '20%',    '5%',   '5%',             '1%',                 '1%',     '5%',    '5%',      '5%');

                $findcols = array('t.id','t.id','t.schluessel', 't.zeit', 't.bearbeiter', 'a.name', 't.betreff', 't.notiz', 't.tags', 'w.warteschlange', 't.nachrichten_anz', 't.status', 't.projekt');
                $searchsql = array('t.schluessel', 't.zeit', 't.bearbeiter', 'a.name', 't.betreff', 't.notiz', 't.tags', 'w.warteschlange', 't.status', 't.projekt');

                $defaultorder = 1;
                $defaultorderdesc = 0;

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=ticket&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.png\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=ticket&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";


                $timedifference = "if (
                                    TIMESTAMPDIFF(hour, t.zeit, curdate()) < 24,
                                    CONCAT(TIMESTAMPDIFF(hour, t.zeit, curdate()), 'h '),
                                    CONCAT(
                                        TIMESTAMPDIFF(day, t.zeit, curdate()), 'd ',MOD(TIMESTAMPDIFF(hour, t.zeit, curdate()), 24), 'h'))";

                $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',t.id,'\" />') AS `auswahl`";

                $sql = "SELECT t.id,".$dropnbox.", t.schluessel, t.zeit, a.name, t.betreff, t.notiz, t.tags, w.warteschlange, t.nachrichten_anz, ".ticket_iconssql().", ".$timedifference.", p.abkuerzung, t.id 
                        FROM ticket t 
                        LEFT JOIN adresse a ON t.adresse = a.id 
                        LEFT JOIN warteschlangen w ON t.warteschlange = w.label 
                        LEFT JOIN projekt p on t.projekt = p.id";

                $where = "1";

                $moreinfo = true; // Allow drop down details
                $menucol = 13; // For moredata

                $count = "SELECT count(DISTINCT id) FROM ticket WHERE $where";
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
    
    function ticket_list() {
        $this->app->erp->MenuEintrag("index.php?module=ticket&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=ticket&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'ticket_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "ticket_list.tpl");
    }    

    public function ticket_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("DELETE FROM `ticket` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->ticket_list();
    } 

    function ticket_edit() {
        $id = $this->app->Secure->GetGET('id');
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=ticket&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=ticket&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
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
            $input['adresse'] = $this->app->erp->ReplaceAdresse(true,$input['adresse'],true); // Parameters: Target db?, value, from form?
            $input['warteschlange'] = explode(" ",$input['warteschlange'])[0]; // Just the label

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

            $sql = "INSERT INTO ticket (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=ticket&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

    
        // Load values again from database
//        $result = $this->app->DB->SelectArr("SELECT t.id, t.schluessel, t.zeit, t.projekt, t.bearbeiter, t.quelle, t.status, t.adresse, t.kunde, t.warteschlange, t.mailadresse, t.prio, t.betreff, t.zugewiesen, t.inbearbeitung, t.inbearbeitung_user, t.firma, t.notiz, t.bitteantworten, t.service, t.kommentar, t.privat, t.dsgvo, t.tags, t.nachrichten_anz, t.id FROM ticket t"." WHERE id=$id");
  
      $result = $this->app->DB->SelectArr("SELECT t.id, t.schluessel, t.zeit, p.abkuerzung as projekt, t.bearbeiter, t.quelle, t.status, t.adresse, t.kunde, CONCAT(w.label,' ',w.warteschlange) as warteschlange, t.mailadresse, t.prio, t.betreff, t.zugewiesen, t.inbearbeitung, t.inbearbeitung_user, t.firma, t.notiz, t.bitteantworten, t.service, t.kommentar, t.privat, t.dsgvo, t.tags, t.nachrichten_anz, t.id FROM ticket t LEFT JOIN adresse a ON t.adresse = a.id LEFT JOIN projekt p on t.projekt = p.id LEFT JOIN warteschlangen w on t.warteschlange = w.label WHERE t.id=$id");

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

    	$this->app->Tpl->Set('STATUSICON', $this->ticket_status_icon($result[0]['status'])."&nbsp;");
        $this->app->YUI->AutoComplete("adresse","adresse");
        $this->app->Tpl->Set('ADRESSE', $this->app->erp->ReplaceAdresse(false,$result[0]['adresse'],false)); // Convert ID to form display
        $this->app->YUI->AutoComplete("projekt","projektname",1);
        $input['projekt'] = $this->app->erp->ReplaceProjekt(false,$input['projekt'],false); // Parameters: Target db?, value, from form?
        $this->app->YUI->AutoComplete("warteschlange","warteschlangename");

        // Add Messages now

        $messages = $this->app->DB->SelectArr("SELECT n.betreff, n.verfasser, n.mail, n.zeit, n.versendet, n.text FROM ticket_nachricht n INNER JOIN ticket t ON t.schluessel = n.ticket WHERE t.id = ".$id." ORDER BY n.zeit DESC");

        foreach ($messages as $message) {
            $this->app->Tpl->Set("NACHRICHT_BETREFF",$message['betreff']);
            $this->app->Tpl->Set("NACHRICHT_ZEIT",$message['zeit']);            
            if ($message['versendet'] == '1') {
                $this->app->Tpl->Set("NACHRICHT_RICHTUNG","An");
                $this->app->Tpl->Set("NACHRICHT_FLOAT","right");
            } else {
                $this->app->Tpl->Set("NACHRICHT_RICHTUNG","Von");
                $this->app->Tpl->Set("NACHRICHT_FLOAT","left");
            }
            $this->app->Tpl->Set("NACHRICHT_NAME",$message['verfasser']);
            $this->app->Tpl->Set("NACHRICHT_EMAILADRESSE",$message['mail']);
            $this->app->Tpl->Set("NACHRICHT_TEXT",$message['text']);
            $this->app->Tpl->Parse('MESSAGES', "ticket_nachricht.tpl");
        }

        $this->app->Tpl->Parse('PAGE', "ticket_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
      	$input['projekt'] = $this->app->Secure->GetPOST('projekt');
      	$input['status'] = $this->app->Secure->GetPOST('status');
      	$input['adresse'] = $this->app->Secure->GetPOST('adresse');
      	$input['warteschlange'] = $this->app->Secure->GetPOST('warteschlange');
      	$input['prio'] = $this->app->Secure->GetPOST('prio');
      	$input['notiz'] = $this->app->Secure->GetPOST('notiz');
      	$input['tags'] = $this->app->Secure->GetPOST('tags');
        return $input;
    }

    public function GetInput_raw(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['schluessel'] = $this->app->Secure->GetPOST('schluessel');
      	$input['zeit'] = $this->app->Secure->GetPOST('zeit');
      	$input['projekt'] = $this->app->Secure->GetPOST('projekt');
        $input['bearbeiter'] = $this->app->Secure->GetPOST('bearbeiter');
      	$input['quelle'] = $this->app->Secure->GetPOST('quelle');
      	$input['status'] = $this->app->Secure->GetPOST('status');
      	$input['adresse'] = $this->app->Secure->GetPOST('adresse');
      	$input['kunde'] = $this->app->Secure->GetPOST('kunde');
      	$input['warteschlange'] = $this->app->Secure->GetPOST('warteschlange');
      	$input['mailadresse'] = $this->app->Secure->GetPOST('mailadresse');
      	$input['prio'] = $this->app->Secure->GetPOST('prio');
      	$input['betreff'] = $this->app->Secure->GetPOST('betreff');
      	$input['zugewiesen'] = $this->app->Secure->GetPOST('zugewiesen');
      	$input['inbearbeitung'] = $this->app->Secure->GetPOST('inbearbeitung');
      	$input['inbearbeitung_user'] = $this->app->Secure->GetPOST('inbearbeitung_user');
      	$input['firma'] = $this->app->Secure->GetPOST('firma');
      	$input['notiz'] = $this->app->Secure->GetPOST('notiz');
      	$input['bitteantworten'] = $this->app->Secure->GetPOST('bitteantworten');
      	$input['service'] = $this->app->Secure->GetPOST('service');
      	$input['kommentar'] = $this->app->Secure->GetPOST('kommentar');
      	$input['privat'] = $this->app->Secure->GetPOST('privat');
      	$input['dsgvo'] = $this->app->Secure->GetPOST('dsgvo');
      	$input['tags'] = $this->app->Secure->GetPOST('tags');
      	$input['nachrichten_anz'] = $this->app->Secure->GetPOST('nachrichten_anz');
        return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);        
        
        $this->app->Tpl->Set('SCHLUESSEL', $input['schluessel']);
	$this->app->Tpl->Set('ZEIT', $input['zeit']);
	$this->app->Tpl->Set('PROJEKT', $input['projekt']);
	$this->app->Tpl->Set('BEARBEITER', $input['bearbeiter']);
	$this->app->Tpl->Set('QUELLE', $input['quelle']);
	$this->app->Tpl->Set('STATUS', $input['status']);
	$this->app->Tpl->Set('ADRESSE', $input['adresse']);
	$this->app->Tpl->Set('KUNDE', $input['kunde']);
	$this->app->Tpl->Set('WARTESCHLANGE', $input['warteschlange']);
	$this->app->Tpl->Set('MAILADRESSE', $input['mailadresse']);
	$this->app->Tpl->Set('PRIO', $input['prio']);
	$this->app->Tpl->Set('BETREFF', $input['betreff']);
	$this->app->Tpl->Set('ZUGEWIESEN', $input['zugewiesen']);
	$this->app->Tpl->Set('INBEARBEITUNG', $input['inbearbeitung']);
	$this->app->Tpl->Set('INBEARBEITUNG_USER', $input['inbearbeitung_user']);
	$this->app->Tpl->Set('FIRMA', $input['firma']);
	$this->app->Tpl->Set('NOTIZ', $input['notiz']);
	$this->app->Tpl->Set('BITTEANTWORTEN', $input['bitteantworten']);
	$this->app->Tpl->Set('SERVICE', $input['service']);
	$this->app->Tpl->Set('KOMMENTAR', $input['kommentar']);
	$this->app->Tpl->Set('PRIVAT', $input['privat']);
	$this->app->Tpl->Set('DSGVO', $input['dsgvo']);
	$this->app->Tpl->Set('TAGS', $input['tags']);
	$this->app->Tpl->Set('NACHRICHTEN_ANZ', $input['nachrichten_anz']);
	
    }

}
