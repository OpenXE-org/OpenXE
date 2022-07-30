<?php

/*
 * Copyright (c) 2022 Xenomporio project
 */

use Xentral\Components\Database\Exception\QueryFailureException;
use Xentral\Modules\Ticket\Task;

class Ticket {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "ticket_list");        
        $this->app->ActionHandler("create", "ticket_create"); // This automatically adds a "New" button
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
                $heading = array('','','Ticket #', 'Datum', 'Adresse', 'Betreff', 'Notiz', 'Tags', 'Verantwortlich', 'Nachr.', 'Status', 'Alter', 'Projekt', 'Men&uuml;');
                $width = array('1%','1%','5%',     '5%',    '5%',      '20%',      '20%',    '5%',   '5%',             '1%',                 '1%',     '5%',    '5%',      '5%');

                $findcols = array('t.id','t.id','t.schluessel', 't.zeit', 't.bearbeiter', 'a.name', 't.betreff', 't.notiz', 't.tags', 'w.warteschlange', 'nachrichten_anz', 't.status', 't.projekt');
                $searchsql = array('t.schluessel', 't.zeit', 't.bearbeiter', 'a.name', 't.betreff', 't.notiz', 't.tags', 'w.warteschlange', 't.status', 't.projekt');

                $defaultorder = 1;
                $defaultorderdesc = 0;

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=ticket&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.png\" border=\"0\"></a>" . "</td></tr></table>";


                $timedifference = "if (
                                    TIMESTAMPDIFF(hour, t.zeit, curdate()) < 24,
                                    CONCAT(TIMESTAMPDIFF(hour, t.zeit, curdate()), 'h '),
                                    CONCAT(
                                        TIMESTAMPDIFF(day, t.zeit, curdate()), 'd ',MOD(TIMESTAMPDIFF(hour, t.zeit, curdate()), 24), 'h'))";

                $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',t.id,'\" />') AS `auswahl`";

                $priobetreff = "if(t.prio!=1,t.betreff,CONCAT('<b><font color=red>',t.betreff,'</font></b>'))";

                $sql = "SELECT t.id,".$dropnbox.", t.schluessel, t.zeit, a.name, ".$priobetreff.", t.notiz, t.tags, w.warteschlange, (SELECT COUNT(n.id) FROM ticket_nachricht n WHERE n.ticket = t.schluessel) as nachrichten_anz, ".ticket_iconssql().", ".$timedifference.", p.abkuerzung, t.id 
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

    function get_messages_of_ticket($ticket_id, $where) {
        return $this->app->DB->SelectArr("SELECT n.id, n.betreff, n.verfasser, n.mail, n.mail_cc, n.zeit, n.zeitausgang, n.versendet, n.text, n.verfasser_replyto, mail_replyto FROM ticket_nachricht n INNER JOIN ticket t ON t.schluessel = n.ticket WHERE (".$where.") AND t.id = ".$ticket_id." ORDER BY n.zeit DESC");        
    }

    function add_attachments_html($ticket_id, $message_id,$templatepos,$showdelete) {
        $file_attachments = $this->app->erp->GetDateiSubjektObjekt('Anhang','Ticket',$message_id);           

        if (!empty($file_attachments)) {

          $this->app->Tpl->Add('NACHRICHT_ANHANG',"<hr style=\"border-style:solid; border-width:1px\">");

          foreach ($file_attachments as $file_attachment) {

                if ($showdelete) {
                    $deletetext = '<a href=index.php?module=ticket&action=edit&id='.$ticket_id.'&cmd=deleteattachment'.'&fileid='.$file_attachment.'>'.
                  '<img src="./themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/delete.svg" />';
                } else {
                   $deletetext = "";
                }   

              $this->app->Tpl->Add($templatepos,                    
                  "<a href=\"index.php?module=dateien&action=send&id=".$file_attachment.
                  "\">".
                  htmlentities($this->app->erp->GetDateiName($file_attachment)).
                  " (".
                  $this->app->erp->GetDateiSize($file_attachment).
                  ")".  
                  "</a>".
                  $deletetext.
                  "</a>". 
                  "</br>");
          }
        }
    }


     /**
    * @throws NumberGeneratorException
    *
    * @return string
    */
    private function generateRandomTicketNumber(): string
    {
        $random = rand(300,700);
        $loopCounter = 0;
        while(true) {
            $candidate = sprintf('%s%04d', date('Ymd'), $random++);

            if (!$this->app->DB->Select('SELECT id FROM ticket WHERE schluessel = '.$candidate)) {
                return($candidate);            
            }

            if ($loopCounter > 99) {
                throw new NumberGeneratorException('ticket number generation failed');
            }
            $loopCounter++;
        }
    }

    function ticket_save_to_db($id, $input) {
         // Write to database
            
        // Add checks here
        if (empty($id)) {
            // New item
            $id = 'NULL';
        } 

        $input['projekt'] = $this->app->erp->ReplaceProjekt(true,$input['projekt'],true); // Parameters: Target db?, value, from form?
        $input['adresse'] = $this->app->erp->ReplaceAdresse(true,$input['adresse'],true); // Parameters: Target db?, value, from form?
        $input['warteschlange'] = explode(" ",$input['warteschlange'])[0]; // Just the label

        $columns = "id, ";
        $values = "$id, ";
        $update = ""; 

        $fix = "";

        foreach ($input as $key => $value) {

          if ($this->app->DB->ColumnExists('ticket',$key)) {
            $columns = $columns.$fix.$key;
            $values = $values.$fix."'".$value."'";
            $update = $update.$fix.$key." = '$value'";
            $fix = ", ";  
          }
        }

        $sql = "INSERT INTO ticket (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;
        $this->app->DB->Update($sql);
        $id = $this->app->DB->GetInsertID();      
        return($id);
    }

    function save_draft($id, $input) {
        $columns = "id, ";
        $values = "$id, ";
        $update = ""; 

        $fix = "";

        // Translate form to table        
        $input['betreff'] = $input['email_betreff'];
        $input['mail'] = $input['email_an'];
        $input['mail_cc'] = $input['email_cc'];
        $input['text'] = $input['email_text'];

        foreach ($input as $key => $value) {

          if ($this->app->DB->ColumnExists('ticket_nachricht',$key)) {
            $columns = $columns.$fix.$key;
            $values = $values.$fix."'".$value."'";
            $update = $update.$fix.$key." = '$value'";
            $fix = ", ";  
          }
        }

        $sql = "INSERT INTO ticket_nachricht (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

        $this->app->DB->Update($sql);
    }

    function ticket_create() {

        $submit = $this->app->Secure->GetPOST('submit');
        $input = $this->GetInput();        

        if ($submit != '') {

            $input['schluessel'] =  $this->generateRandomTicketNumber();       
            $input['zeit'] = date('Y-m-d H:i:s', time());
            $input['kunde'] = $this->app->User->GetName();

            $id = $this->ticket_save_to_db($id, $input);

            header("Location: index.php?module=ticket&action=edit&id=$id");
            exit();
        }

        $this->app->Tpl->Set('STATUSICON', $this->ticket_status_icon('neu')."&nbsp;");
        $this->app->YUI->AutoComplete("adresse","adresse");
        $this->app->YUI->AutoComplete("projekt","projektname",1);
        $this->app->YUI->AutoComplete("status","ticketstatus",1);
        $this->app->Tpl->Set('STATUS', $this->app->erp->GetStatusTicketSelect('neu'));
        $this->app->YUI->AutoComplete("warteschlange","warteschlangename");
        $this->app->Tpl->Parse('PAGE', "ticket_create.tpl");
    }

    function ticket_edit() {
        $id = $this->app->Secure->GetGET('id');

        if (empty($id)) {
            return;
        }
             
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=ticket&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=ticket&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
        $id = $this->app->Secure->GetGET('id');
        $cmd = $this->app->Secure->GetGET('cmd');
        $input = $this->GetInput();
        $submit = $this->app->Secure->GetPOST('submit');
        $msg = $this->app->erp->base64_url_decode($this->app->Secure->GetGET('msg'));                      

        // Always save
        if ($submit != '')
        {
           $this->ticket_save_to_db($id, $input);  
           $msg = "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>";
        }

        // Load values again from database
        $result = $this->app->DB->SelectArr("SELECT t.id, t.schluessel, t.zeit, p.abkuerzung as projekt, t.bearbeiter, t.quelle, t.status, t.prio, t.adresse, t.kunde, CONCAT(w.label,' ',w.warteschlange) as warteschlange, t.mailadresse, t.betreff, t.zugewiesen, t.inbearbeitung, t.inbearbeitung_user, t.firma, t.notiz, t.bitteantworten, t.service, t.kommentar, t.privat, t.dsgvo, t.tags, t.nachrichten_anz, t.id FROM ticket t LEFT JOIN adresse a ON t.adresse = a.id LEFT JOIN projekt p on t.projekt = p.id LEFT JOIN warteschlangen w on t.warteschlange = w.label WHERE t.id=$id");

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);   
        }
  
      	$this->app->Tpl->Set('PRIO', $result[0]['prio']==1?"checked":"");
       	$this->app->Tpl->Set('STATUSICON', $this->ticket_status_icon($result[0]['status'])."&nbsp;");
        $this->app->YUI->AutoComplete("adresse","adresse");
        $this->app->Tpl->Set('ADRESSE', $this->app->erp->ReplaceAdresse(false,$result[0]['adresse'],false)); // Convert ID to form display
        $this->app->YUI->AutoComplete("projekt","projektname",1);
        $this->app->YUI->AutoComplete("status","ticketstatus",1);

        $this->app->Tpl->Set('STATUS', $this->app->erp->GetStatusTicketSelect($result[0]['status']));
        $input['projekt'] = $this->app->erp->ReplaceProjekt(false,$input['projekt'],false); // Parameters: Target db?, value, from form?
        $this->app->YUI->AutoComplete("warteschlange","warteschlangename");
        // END Header

        // Check for draft
        $drafted_messages = $this->get_messages_of_ticket($id, "zeitausgang IS NULL AND versendet = '1'");     

        if (!empty($drafted_messages)) {

            // Draft from form?
            if ($submit != '') {
                $this->save_draft($drafted_messages[0]['id'],$input);
                // Reload        
                $drafted_messages = $this->get_messages_of_ticket($id, "zeitausgang IS NULL AND versendet = '1'");     
            }

            // Load the draft for editing
            $this->app->Tpl->Set('EMAIL_AN', $drafted_messages[0]['mail']);
            $this->app->Tpl->Set('EMAIL_CC', $drafted_messages[0]['mail_cc']);
            $this->app->Tpl->Set('EMAIL_BCC', $drafted_messages[0]['mail_bcc']);
            $this->app->Tpl->Set('EMAIL_BETREFF', $drafted_messages[0]['betreff']);
            $this->app->Tpl->Set('EMAIL_TEXT',$drafted_messages[0]['text']);

            // Show new message dialog                   
            $this->app->Tpl->Set('EMAIL_SENDER', $this->app->erp->GetSelectEmailMitName($dokument['von']));
            $this->app->YUI->AutoComplete("email_an","emailname");
            $this->app->YUI->AutoComplete("email_cc","emailname");
            $this->app->YUI->AutoComplete("email_bcc","emailname");
            $this->app->YUI->CkEditor("email_text","internal", null, 'JQUERY');

            // Delete attachment from draft
            if ($cmd=='deleteattachment') {
                $fileid = $this->app->Secure->GetGET('fileid');

                // Check if this file is only attached to this draft and nowhere else
                $check = $this->app->erp->GetDateiStichwoerter($fileid);

                $save_to_delete = true;
                foreach ($check as $stichwort) {
                    if ($stichwort['subjekt'] != 'anhang' || $stichwort['objekt'] != 'Ticket' || $stichwort['parameter'] != $drafted_messages[0]['id']) {
                        $save_to_delete = false;
                        break;
                    }       
                }
                if ($save_to_delete) {
                    $this->app->erp->DeleteDatei($fileid);    
                } else {
                    $msg .= "<div class=\"success\">Fehler beim Löschen der Datei: In Verwendung.</div>";
                }
            }

            // Upload of attachments
            if(isset($_FILES['upload']) && is_array($_FILES['upload']))
            {
              foreach($_FILES['upload']['tmp_name'] as $key => $file)
              {
                if($file != "")
                {
                  $fileid = $this->app->erp->CreateDatei($_FILES['upload']['name'][$key], $_FILES['upload']['name'][$key], "", "", $_FILES['upload']['tmp_name'][$key], $this->app->User->GetName());
                  // stichwoerter hinzufuegen
                  $this->app->erp->AddDateiStichwort($fileid, "anhang", "Ticket", $drafted_messages[0]['id']);
                }
              }
            }

            $this->add_attachments_html($id,$drafted_messages[0]['id'],'ANHAENGE',true);
            $this->app->Tpl->Parse('NEW_MESSAGE', "ticket_new_message.tpl");
        }     
        // END Draft    

        // Get all messsages
        $messages = $this->get_messages_of_ticket($id, 1);

        switch ($submit) {
          case 'neue_email':

            if (empty($drafted_messages)) {
                // Create new message and save it for editing   

                $recv_messages = $this->get_messages_of_ticket($id,"n.versendet != 1");
               	$this->app->Tpl->Set('EMAIL_AN', $recv_messages[0]['mail']);
                
                if (!empty($recv_messages)) {
                    if (!str_starts_with(strtoupper($recv_messages[0]['betreff']),"RE:")) {
                        $betreff = "RE: ".$recv_messages[0]['betreff'];
                    }
                    else {
                        $betreff = $recv_messages[0]['betreff'];
                    }
                }
                else {
                    $betreff = $result[0]['betreff'];
                }

                $anschreiben = $this->app->DB->Select("SELECT anschreiben FROM adresse WHERE id='".$result[0]['adresse']."' LIMIT 1");
                if($anschreiben=="")
                {
                  $anschreiben = $this->app->erp->Beschriftung("dokument_anschreiben").",\n".$this->app->erp->Grussformel($projekt,$sprache);
                }

                $senderName = $this->app->User->GetName()." (".$this->app->erp->GetFirmaAbsender().")";
                $senderAddress = $this->app->erp->GetFirmaMail();
                           
                $sql = "INSERT INTO `ticket_nachricht` (
                        `ticket`, `zeit`, `text`, `betreff`, `medium`, `versendet`,
                        `verfasser`, `mail`,`status`, `verfasser_replyto`, `mail_replyto`
                    ) VALUES ('".$result[0]['schluessel']."',NOW(),'".$anschreiben."','".$betreff."','email','1','','".$recv_messages[0]['mail']."','neu','".$senderName."','".$senderAddress."');";

                $this->app->DB->Insert($sql);         
                // Show new message dialog                   
                header("Location: index.php?module=ticket&action=edit&id=$id");          
                $this->app->ExitXentral();
            } 
        break;
          case 'entwurfloeschen':
            if (!empty($drafted_messages)) {
                $sql = "UPDATE ticket_nachricht SET ticket = '' WHERE id=".$drafted_messages[0]['id'];
                $this->app->DB->Update($sql);  
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Der Entwurf wurde gel&ouml;scht.</div>");
                header("Location: index.php?module=ticket&action=edit&msg=$msg&id=$id");
                $this->app->ExitXentral();
            }
          break;
          case 'absenden':            

            if (empty($drafted_messages)) {
                break;
            }

            // Enforce Ticket #
            if (!preg_match("/Ticket #[0-9]{12}/i", $drafted_messages[0]['betreff'])) {
              $drafted_messages[0]['betreff'].= " Ticket #".$result[0]['schluessel'];
            }
                
            // Attachments
            $files = $this->app->erp->GetDateiSubjektObjektDateiname('Anhang','Ticket',$drafted_messages[0]['id'],"");

            foreach ($files as $file) {
                $msg .= $file."<br>";
            }
   
            if (
                $this->app->erp->MailSend(
                  $drafted_messages[0]['mail_replyto'],
                  $drafted_messages[0]['verfasser_replyto'],
                  $drafted_messages[0]['mail'],
                  $drafted_messages[0]['mail'],
                  $drafted_messages[0]['betreff'],
                  $drafted_messages[0]['text'],
                  $files,
                  0,false,'','',
                  true
              ) != 0
            ) {

                // Update message in ticket_nachricht
                $sql = "UPDATE `ticket_nachricht` SET `zeitausgang` = NOW(), `betreff` = '".$drafted_messages[0]['betreff']."' WHERE id = ".$drafted_messages[0]['id'];
                $this->app->DB->Insert($sql);

                $msg .=  '<div class="info">Die E-Mail wurde erfolgreich versendet an '.$input['email_an'].'. '.$this->app->erp->mail_error.'</div>';
                header("Location: index.php?module=ticket&action=edit&id=".$id."&msg=".$this->app->erp->base64_url_encode($msg));

            }
            else {
              $msg = '<div class="error">Fehler beim Versenden der E-Mail: '.$this->app->erp->mail_error.'</div>';
            }

            // Get messsages again
            $messages = $this->get_messages_of_ticket($id,1);

          break;
        } 

        // Add Messages now
        foreach ($messages as $message) {
            if ($message['versendet'] == '1') {

                if (is_null($message['zeitausgang'])) {
                    continue;
                }


                $this->app->Tpl->Set("NACHRICHT_RICHTUNG","An");
                $this->app->Tpl->Set("NACHRICHT_FLOAT","right");
                $this->app->Tpl->Set("NACHRICHT_ZEIT",$message['zeitausgang']);            
            } else {
                $this->app->Tpl->Set("NACHRICHT_RICHTUNG","Von");
                $this->app->Tpl->Set("NACHRICHT_FLOAT","left");
                $this->app->Tpl->Set("NACHRICHT_ZEIT",$message['zeit']);            
            }
            $this->app->Tpl->Set("NACHRICHT_BETREFF",$message['betreff']);
            $this->app->Tpl->Set("NACHRICHT_NAME",$message['verfasser']);
            $this->app->Tpl->Set("NACHRICHT_EMAILADRESSE",$message['mail']);
            $this->app->Tpl->Set("NACHRICHT_TEXT",$message['text']);

            $this->app->Tpl->Set('NACHRICHT_ANHANG',"");         
            $this->add_attachments_html($id,$message['id'],'NACHRICHT_ANHANG',false);
         
            $this->app->Tpl->Parse('MESSAGES', "ticket_nachricht.tpl");
        }

        $this->app->Tpl->Set('MESSAGE', $msg);
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
      	$input['prio'] = !empty($this->app->Secure->GetPOST('prio'))?"1":"0";
      	$input['notiz'] = $this->app->Secure->GetPOST('notiz');
      	$input['tags'] = $this->app->Secure->GetPOST('tags');
      	$input['betreff'] = $this->app->Secure->GetPOST('betreff');
      	$input['email_sender'] = $this->app->Secure->GetPOST('email_sender');
      	$input['email_an'] = $this->app->Secure->GetPOST('email_an');
      	$input['email_cc'] = $this->app->Secure->GetPOST('email_cc');
      	$input['email_bcc'] = $this->app->Secure->GetPOST('email_bcc');
      	$input['email_betreff'] = $this->app->Secure->GetPOST('email_betreff');
      	$input['email_text'] = $this->app->Secure->GetPOST('email_text');
        return $input;
    }   
}
