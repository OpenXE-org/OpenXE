<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Ticketregeln {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "ticketregeln_list");        
        $this->app->ActionHandler("create", "ticketregeln_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "ticketregeln_edit");
        $this->app->ActionHandler("delete", "ticketregeln_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "ticketregeln_list":
                $allowed['ticketregeln_list'] = array('list');
                $heading = array('E-Mail Empf&auml;nger', 'E-Mail Verfasser', 'Verfasser Name', 'Betreff', 'Papierkorb', 'Pers&ouml;nlich', 'Prio', 'DSGVO', 'Verantw.', 'Aktiv', 'Men&uuml;');
                $width = array('10%'); // Fill out manually later

                $findcols = array('t.empfaenger_email', 't.sender_email', 't.name', 't.betreff', 't.spam', 't.persoenlich', 't.prio', 't.dsgvo', 't.warteschlange', 't.aktiv');
                $searchsql = array('t.empfaenger_email', 't.sender_email', 't.name', 't.betreff', 't.spam', 't.persoenlich', 't.prio', 't.dsgvo', 't.warteschlange', 't.aktiv');

                $defaultorder = 1;
                $defaultorderdesc = 0;

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=ticketregeln&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=ticketregeln&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS t.id, t.empfaenger_email, t.sender_email, t.name, t.betreff, t.spam, t.persoenlich, t.prio, t.dsgvo, w.warteschlange, t.aktiv, t.id FROM ticket_regeln t LEFT JOIN warteschlangen w ON t.warteschlange = w.label";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM ticket_regeln WHERE $where";
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
    
    function ticketregeln_list() {
        $this->app->erp->MenuEintrag("index.php?module=ticketregeln&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=ticketregeln&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'ticketregeln_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "ticketregeln_list.tpl");
    }    

    public function ticketregeln_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("DELETE FROM `ticket_regeln` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->ticketregeln_list();
    } 

    /*
     * Edit ticketregeln item
     * If id is empty, create a new one
     */
        
    function ticketregeln_edit() {
        $id = $this->app->Secure->GetGET('id');
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=ticketregeln&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=ticketregeln&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
        $id = $this->app->Secure->GetGET('id');
        $input = $this->GetInput();
        $submit = $this->app->Secure->GetPOST('submit');
                
        if (empty($id)) {
            // New item
            $id = 'NULL';

            // Check for ticketid
            $ticketid = $this->app->Secure->GetPOST('ticketid');
            if (!empty($ticketid)) {
                $sql =  "
                    SELECT 
                        n.id,
                        n.betreff,
                        n.bearbeiter,
                        n.verfasser,
                        n.mail,
                        t.quelle,
                        t.warteschlange,
                        ".$this->app->erp->FormatDateTimeShort('n.zeit','zeit').",
                        ".$this->app->erp->FormatDateTimeShort('n.zeitausgang','zeitausgang').",
                        n.versendet,
                        n.text,
                        n.textausgang,
                        n.verfasser_replyto,
                        n.mail_replyto,
                        n.mail_cc,
                        (SELECT GROUP_CONCAT(value SEPARATOR ', ') FROM ticket_header th WHERE th.ticket_nachricht = n.id AND th.type = 'cc') as mail_cc_recipients,
                        (SELECT GROUP_CONCAT(value SEPARATOR ', ') FROM ticket_header th WHERE th.ticket_nachricht = n.id AND th.type = 'to') as mail_recipients
                    FROM ticket_nachricht n INNER JOIN ticket t ON t.schluessel = n.ticket 
                    WHERE t.id = ".$ticketid." ORDER BY n.zeit DESC LIMIT 1";

                $last_message = $this->app->DB->SelectArr($sql)[0];

                $input['empfaenger_email'] = $last_message['mail_recipients'];
                $input['sender_email'] = $last_message['mail'];
                $input['name'] = $last_message['verfasser'];
                $input['betreff'] = $last_message['betreff'];
                $input['warteschlange'] = $last_message['warteschlange'];

                $from_ticket = true;

                $result = array(
                    'empfaenger_email' => $last_message['mail_recipients'],
                    'sender_email' => $last_message['mail'],
                    'name' => $last_message['verfasser'],
                    'betreff' => $last_message['betreff'],
                    'warteschlange' => $last_message['warteschlange'],
                    'aktiv' => 1
                );

            }

        } 

        if (!$from_ticket) {

            if ($submit != '')
            {

                // Write to database
                
                // Add checks here
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

                $sql = "INSERT INTO ticket_regeln (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

    //            echo($sql);

                $this->app->DB->Update($sql);

                if ($id == 'NULL') {
                    $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                    header("Location: index.php?module=ticketregeln&action=list&msg=$msg");
//                    $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");                
//                    $id = $this->app->DB->GetInsertID();
                } else {
                    $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
                }
            }

        
            // Load values again from database
            $result = $this->app->DB->SelectArr("SELECT t.id, t.empfaenger_email, t.sender_email, t.name, t.betreff, t.spam, t.persoenlich, t.prio, t.dsgvo, CONCAT(w.label,' ',w.warteschlange) as warteschlange, t.aktiv, t.id FROM ticket_regeln t LEFT JOIN warteschlangen w on t.warteschlange = w.label WHERE t.id=$id")[0];
        } 
       

        foreach ($result as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);   
        }
             
        /*
         * Add displayed items later
         * 
         */

      	$this->app->Tpl->Set('PRIO', $result['prio']==1?"checked":"");
      	$this->app->Tpl->Set('SPAM', $result['spam']==1?"checked":"");
      	$this->app->Tpl->Set('PERSOENLICH', $result['persoenlich']==1?"checked":"");
      	$this->app->Tpl->Set('DSGVO', $result['dsgvo']==1?"checked":"");
      	$this->app->Tpl->Set('AKTIV', $result['aktiv']==1?"checked":"");

        $this->app->YUI->AutoComplete("warteschlange","warteschlangename");

//        $this->SetInput($input);              
        $this->app->Tpl->Parse('PAGE', "ticketregeln_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['empfaenger_email'] = $this->app->Secure->GetPOST('empfaenger_email');
	$input['sender_email'] = $this->app->Secure->GetPOST('sender_email');
	$input['name'] = $this->app->Secure->GetPOST('name');
	$input['betreff'] = $this->app->Secure->GetPOST('betreff');
	$input['spam'] = $this->app->Secure->GetPOST('spam');
	$input['persoenlich'] = $this->app->Secure->GetPOST('persoenlich');
	$input['prio'] = $this->app->Secure->GetPOST('prio');
	$input['dsgvo'] = $this->app->Secure->GetPOST('dsgvo');
	$input['warteschlange'] = $this->app->Secure->GetPOST('warteschlange');
	$input['aktiv'] = $this->app->Secure->GetPOST('aktiv');
	

        return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);        
        
        $this->app->Tpl->Set('EMPFAENGER_EMAIL', $input['empfaenger_email']);
	$this->app->Tpl->Set('SENDER_EMAIL', $input['sender_email']);
	$this->app->Tpl->Set('NAME', $input['name']);
	$this->app->Tpl->Set('BETREFF', $input['betreff']);
	$this->app->Tpl->Set('SPAM', $input['spam']);
	$this->app->Tpl->Set('PERSOENLICH', $input['persoenlich']);
	$this->app->Tpl->Set('PRIO', $input['prio']);
	$this->app->Tpl->Set('DSGVO', $input['dsgvo']);
	$this->app->Tpl->Set('WARTESCHLANGE', $input['warteschlange']);
	$this->app->Tpl->Set('AKTIV', $input['aktiv']);
	
    }

}
