<?php

/*
 * Copyright (c) 2022 OpenXE project
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
        $this->app->ActionHandler("minidetail", "ticket_minidetail");
        $this->app->ActionHandler("text", "ticket_text"); // Output text for iframe display
        $this->app->ActionHandler("text_ausgang", "ticket_text_ausgang"); // Output text for iframe display
        $this->app->ActionHandler("statusfix", "ticket_statusfix"); // Xentral 20 compatibility set all ticket status to latest ticket_nachricht status
        $this->app->ActionHandler("datefix", "ticket_datefix"); // Xentral 20 compatibility set all ticket dates to latest ticket_nachricht date
        $this->app->ActionHandler("dateien", "ticket_dateien"); 
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    function ticket_status_icon(string $status) {
      return('<img src="./themes/new/images/status_'.$status.'.png" style="margin-right:1px" title="'.$status.'" border="0">');
    }


    public function TableSearch(&$app, $name, $erlaubtevars) {


       function ticket_iconssql() {
            return "CONCAT('<img src=\"./themes/new/images/status_',`t`.`status`,'.png\" style=\"margin-right:1px\" title=\"',`t`.`status`,'\" border=\"0\">')";
        }


        switch ($name) {
            case "ticket_list":

                $allowed['ticket_list'] = array('list');
                $heading = array('','','Ticket #', 'Aktion','Adresse', 'Betreff',  'Tags', 'Verant.', 'Nachr.', 'Status', 'Projekt', 'Men&uuml;');
                $width = array('1%','1%','5%',     '5%',        '5%',      '30%',      '1%',     '5%',     '1%',    '1%',      '1%',      '1%');

                $findcols = array('t.id','t.id','t.schluessel', 't.zeit', 'a.name', 't.betreff',            't.tags', 'w.warteschlange', 'nachrichten_anz', 't.status', 'p.abkuerzung');
                $searchsql = array(             't.schluessel', 't.zeit', 'a.name', 't.betreff','t.notiz',  't.tags', 'w.warteschlange', 't.status', 'p.abkuerzung','(SELECT mail FROM ticket_nachricht tn WHERE tn.ticket = t.schluessel AND tn.versendet <> 1 LIMIT 1)');

                $defaultorder = 1;
                $defaultorderdesc = 0;

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=ticket&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "</td></tr></table>";


                $timedifference = "if (
                                    TIMESTAMPDIFF(hour, t.zeit, NOW()) < 24,
                                    CONCAT(TIMESTAMPDIFF(hour, t.zeit, NOW()),'h'),
                                    CONCAT(TIMESTAMPDIFF(day, t.zeit, NOW()), 'd ',MOD(TIMESTAMPDIFF(hour, t.zeit, NOW()),24),'h'))";

                $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`,
                              CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',t.id,'\" />') AS `auswahl`";

                $priobetreff = "if(t.prio!=1,REGEXP_REPLACE(t.betreff, '<[^>]*>+', ''),CONCAT('<b><font color=red>',REGEXP_REPLACE(t.betreff, '<[^>]*>+', ''),'</font></b>'))"; //+ #20230916 XSS

                $anzahlnachrichten = "(SELECT COUNT(n.id) FROM ticket_nachricht n WHERE n.ticket = t.schluessel)";

                $letztemail = $app->erp->FormatDateTimeShort("(SELECT MAX(n.zeit) FROM ticket_nachricht n WHERE n.ticket = t.schluessel AND n.zeit IS NOT NULL)");

                $tagstart = "<li class=\"tag-editor-tag\">";
                $tagend = "</li>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS 
                        t.id,
                        ".$dropnbox.",
                        CONCAT('<a href=\"index.php?module=ticket&action=edit&id=',t.id,'\">',t.schluessel,'</a>'),".
                        $app->erp->FormatDateTimeShort('zeit')." as aktion,
                        CONCAT(COALESCE(CONCAT(a.name,'<br>'),''),COALESCE((SELECT mail FROM ticket_nachricht tn WHERE tn.ticket = t.schluessel AND tn.versendet <> 1 LIMIT 1),'')) as combiadresse,
                        CONCAT('<b>',".$priobetreff.",'</b><br/><i>',replace(substring(ifnull(t.notiz,''),1,500),'\n','<br/>'),'</i>'), 
                        CONCAT('<div class=\"ticketoffene\"><ul class=\"tag-editor\">'\n,'".$tagstart."',replace(t.tags,',','".$tagend."<div class=\"tag-editor-spacer\">&nbsp;</div>".$tagstart."'),'".$tagend."','</ul></div>'),
                        w.warteschlange,
                        ".$anzahlnachrichten." as `nachrichten_anz`,
                        ".ticket_iconssql().",
                        p.abkuerzung,
                        t.id 
                        FROM ticket t 
                        LEFT JOIN adresse a ON t.adresse = a.id 
                        LEFT JOIN warteschlangen w ON t.warteschlange = w.label 
                        LEFT JOIN projekt p on t.projekt = p.id";

                $where = "1";

                // Toggle filters
                $this->app->Tpl->Add('JQUERYREADY', "$('#meinetickets').click( function() { fnFilterColumn1( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#prio').click( function() { fnFilterColumn2( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#geschlossene').click( function() { fnFilterColumn3( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#spam').click( function() { fnFilterColumn4( 0 ); } );");

                for ($r = 1;$r <= 4;$r++) {
                  $this->app->Tpl->Add('JAVASCRIPT', '
                                         function fnFilterColumn' . $r . ' ( i )
                                         {
                                         if(oMoreData' . $r . $name . '==1)
                                         oMoreData' . $r . $name . ' = 0;
                                         else
                                         oMoreData' . $r . $name . ' = 1;

                                         $(\'#' . $name . '\').dataTable().fnFilter( 
                                           \'\',
                                           i, 
                                           0,0
                                           );
                                         }
                                         ');
                }


                $more_data1 = $this->app->Secure->GetGET("more_data1");
                if ($more_data1 == 1) {
                   $where .= " AND t.warteschlange IN (SELECT w.label FROM warteschlangen w WHERE adresse=".$this->app->User->GetAdresse().")"; // Queues of user
                } else {
                }

                $more_data2 = $this->app->Secure->GetGET("more_data2");
                if ($more_data2 == 1) {
                  $where .= " AND t.prio = '1'";
                }
                else {
                }                

                $more_data3 = $this->app->Secure->GetGET("more_data3");
                if ($more_data3 == 1) {            
                }
                else {
                  $where .= " AND (t.status <> 'abgeschlossen')"; // Exclude and geschlossen
                }                

                $more_data4 = $this->app->Secure->GetGET("more_data4");
                if ($more_data4 == 1) {
                }
                else {
                  $where .= " AND (t.status <> 'spam')";
                }                
                // END Toggle filters

                $moreinfo = true; // Allow drop down details
                $menucol = 11; // For moredata

                $count = "SELECT count(DISTINCT id) FROM ticket t WHERE $where";

//                echo(htmlentities($sql." ".$where));

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
    
    // Ensure status 'offen' on self-assigned tickets
    function ticket_set_self_assigned_status(array $ids) {
        $sql = "UPDATE ticket SET status = 'offen' 
                WHERE 
                    status = 'neu' 
                    AND id IN (".implode(',',$ids).")
                    AND warteschlange IN (SELECT label FROM warteschlangen WHERE adresse = '".$this->app->User->GetAdresse()."')";
        $this->app->DB->Update($sql);
    }

    function ticket_list() {
  
        // Process multi action
        $auswahl = $this->app->Secure->GetPOST('auswahl');
        $submit = $this->app->Secure->GetPOST('submit');
        $selectedIds = [];
        if(!empty($auswahl)) {
          foreach($auswahl as $selectedId) {
            $selectedId = (int)$selectedId;
            if($selectedId > 0) {
              $selectedIds[] = $selectedId;
            }
          }          

            switch ($submit) {
                case 'zuordnen':
                    $status = $this->app->Secure->GetPOST('status');
                    $warteschlange = $this->app->Secure->GetPOST('warteschlange');

                    $sql = "UPDATE ticket SET status = '".$status."', zeit = NOW()";
                    if ($warteschlange != '') {
                        $sql .= ", warteschlange = '".explode(" ",$warteschlange)[0]."'";
                    }

                    $sql .= " WHERE id IN (".implode(",",$selectedIds).")";
                    $this->app->DB->Update($sql);
                    $this->ticket_set_self_assigned_status($selectedIds);
                break;
                case 'spam_filter':
                    if($this->app->erp->RechteVorhanden('ticketregeln','create')) {
                        $sql = "UPDATE ticket SET status = 'spam', zeit = NOW()";
                        $sql .= " WHERE id IN (".implode(",",$selectedIds).")";
                        $this->app->DB->Update($sql);

                        foreach ($selectedIds as $selectedId) {

                            // Check existing
                            $sql = "SELECT id FROM ticket_regeln WHERE
                                        empfaenger_email = '' AND
                                        sender_email = (SELECT mailadresse FROM ticket WHERE id = ".$selectedId." LIMIT 1) AND
                                        name = '' AND
                                        betreff = '' AND
                                        spam = 1 AND
                                        aktiv = 1
                                    ";

                            if (!$this->app->DB->Select($sql)) {
                                $sql = "INSERT IGNORE INTO ticket_regeln (
                                                        empfaenger_email,
                                                        sender_email,
                                                        name,
                                                        betreff,
                                                        spam,
                                                        persoenlich,
                                                        prio,
                                                        dsgvo,
                                                        adresse,
                                                        warteschlange,
                                                        aktiv
                                        ) VALUES (
                                            '',
                                            (SELECT mailadresse FROM ticket WHERE id = ".$selectedId." LIMIT 1),
                                            '',
                                            '',
                                            1,
                                            0,
                                            0,
                                            0,
                                            0,
                                            '',
                                            1
                                        )";
                                $this->app->DB->Insert($sql);
                            }
                        }

                    }
                break;
            }    
        }

        // List
        $this->app->YUI->TagEditor('taglist', array('width'=>370));
        $this->app->Tpl->Add('SCRIPTJAVASCRIPT','<link rel="stylesheet" type="text/css" href="./css/jquery.tag-editor.css">');

        $this->app->erp->MenuEintrag("index.php?module=ticket&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=ticket&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->Tpl->Set('STATUS', $this->app->erp->GetStatusTicketSelect('neu'));
        $this->app->YUI->AutoComplete("warteschlange","warteschlangename");

        if(!$this->app->erp->RechteVorhanden('ticketregeln','create')) {
            $this->app->Tpl->Set('SPAM_HIDDEN', 'hidden');
        }

        $this->app->YUI->TableSearch('TAB1', 'ticket_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "ticket_list.tpl");
    }    

    function get_messages_of_ticket($ticket_id, $where, $limit) {

        if ($limit) {
            $limitsql = " LIMIT ".((int) $limit);
        } else {
            $limitsql = "";
        }

        if (empty($ticket_id)) {
          $ticket_where = "";
        } else {
          $ticket_where = " AND t.id = ".$ticket_id;
        }

        if (empty($where)) {
          $where = "1";
        }

//        $sql = "SELECT n.id, n.betreff, n.verfasser, n.mail, n.mail_cc, n.zeit, n.zeitausgang, n.versendet, n.text, n.verfasser_replyto, mail_replyto, (SELECT GROUP_CONCAT(value SEPARATOR ', ' FROM ticket_header th WHERE th.ticket_nachricht = n.id AND th.type = 'cc') value from) as cc FROM ticket_nachricht n INNER JOIN ticket t ON t.schluessel = n.ticket WHERE (".$where.") AND t.id = ".$ticket_id." ORDER BY n.zeit DESC ".$limitsql; 

        $sql =  "SELECT n.id,
                n.betreff,
                n.bearbeiter,
                n.verfasser,
                n.mail,
                t.quelle,
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
                WHERE (".$where.") ".$ticket_where." ORDER BY n.zeit DESC ".$limitsql; 

        return $this->app->DB->SelectArr($sql);        
    }

    function add_attachments_html($ticket_id, $message_id,$templatepos,$showdelete) {
        $file_attachments = $this->app->erp->GetDateiSubjektObjekt('Anhang','Ticket',$message_id);           

        if (!empty($file_attachments)) {

          $this->app->Tpl->Add('NACHRICHT_ANHANG',"<hr style=\"border-style:solid; border-width:1px\">");

          foreach ($file_attachments as $file_attachment) {

                if ($showdelete) {
                    $deletetext = '<a href=index.php?module=ticket&action=edit&id='.$ticket_id.'&cmd=deleteattachment'.'&fileid='.$file_attachment.'>'.
                  '<img src="./themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/delete.svg" /></a>';
                } else {
                   $deletetext = "";
                }
  
                $attachtext = "";
/*              Not implemented -> Attachment of ticket_nachricht to business object is the better option -> implement later
               $attachtext = '<a href=index.php?module=dateien&action=edit&id='.$file_attachment.'>'.
                  '<img src="./themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/copy.svg" /></a>';  */


              $this->app->Tpl->Add($templatepos,                    
                  "<a href=\"index.php?module=dateien&action=send&id=".$file_attachment.
                  "\">".
                  htmlentities($this->app->erp->GetDateiName($file_attachment)).
                  " (".
                  $this->app->erp->GetDateiSize($file_attachment).
                  ")".  
                  "</a>".
                  $deletetext.
                  $attachtext.
                  "<br>");
          }
        }
    }

    function add_attachments_header_html($ticket_id, $templatepos) {
        $file_attachments = $this->app->erp->GetDateiSubjektObjekt('%','ticket_header',$ticket_id);       

        if (!empty($file_attachments)) {     
          $this->app->Tpl->Add($templatepos,"<tr><td>{|Anh&auml;nge|}:</td><td><div class=\"ticket_attachments\">");
          foreach ($file_attachments as $file_attachment) {

              $this->app->Tpl->Add($templatepos,                    
                  "<a href=\"index.php?module=dateien&action=send&id=".$file_attachment.
                  "\">".
                  htmlentities($this->app->erp->GetDateiName($file_attachment)).
                  " (".
                  $this->app->erp->GetDateiSize($file_attachment).
                  ")".  
                  "</a>".       
                  "<br>");
          }
          $this->app->Tpl->Add($templatepos,"</div></td></tr>");
        }
    }

    function add_messages_tpl($messages, $showdrafts) {

        // Add Messages now
        foreach ($messages as $message) {

            $message['betreff'] = strip_tags($message['betreff']); //+ #20230916 XSS

            // Clear this first
            $this->app->Tpl->Set('NACHRICHT_ANHANG',"");         

            if (empty($message['betreff'])) {
                $message['betreff'] = "...";
            }

            // Xentral 20 compatibility
            if ($message['textausgang'] != '') {
              // Sent message 

              $this->app->Tpl->Set("NACHRICHT_BETREFF",'<a href="index.php?module=ticket&action=text_ausgang&mid='.$message['id'].'" target="_blank">'.htmlentities($message['betreff']).'</a>');
              $this->app->Tpl->Set("NACHRICHT_ZEIT",$message['zeitausgang']);            
              $this->app->Tpl->Set("NACHRICHT_FLOAT","right");
              $this->app->Tpl->Set("META_FLOAT","left");
              $this->app->Tpl->Set("NACHRICHT_TEXT",$message['textausgang']);
              $this->app->Tpl->Set("NACHRICHT_SENDER",htmlentities($message['bearbeiter']));
              $this->app->Tpl->Set("NACHRICHT_RECIPIENTS",htmlentities($message['verfasser']." <".$message['mail'].">"));

//              $this->app->Tpl->Set("NACHRICHT_TEXT",$message['textausgang']);
              $this->app->Tpl->Set("NACHRICHT_TEXT",'<iframe class="ticket_text" src="index.php?module=ticket&action=text_ausgang&mid='.$message['id'].'"></iframe>');

              $this->app->Tpl->Parse('MESSAGES', "ticket_nachricht.tpl");
            }

            if ($message['versendet'] == '1' && empty($message['textausgang'])) { //  textausgang is always empty, except for old Xentral 20 tickets

               // Sent message          

                if (is_null($message['zeitausgang'])) {                    
                    if (!$showdrafts) {
                        continue;
                    }
                    $this->app->Tpl->Set("NACHRICHT_BETREFF",htmlentities($message['betreff']." (Entwurf)"));
                } else {
                  $this->app->Tpl->Set("NACHRICHT_BETREFF",'<a href="index.php?module=ticket&action=text&mid='.$message['id'].'&insecure=1" target="_blank">'.htmlentities($message['betreff']).'</a>');
                }
                $this->app->Tpl->Set("NACHRICHT_SENDER",htmlentities($message['verfasser']." <".$message['mail_replyto'].">"));
                $this->app->Tpl->Set("NACHRICHT_RECIPIENTS",htmlentities($message['mail']));
                $this->app->Tpl->Set("NACHRICHT_CC_RECIPIENTS",htmlentities($message['mail_cc']));  
                $this->app->Tpl->Set("NACHRICHT_FLOAT","right");
                $this->app->Tpl->Set("META_FLOAT","left");
                $this->app->Tpl->Set("NACHRICHT_ZEIT",$message['zeitausgang']);            
                $this->app->Tpl->Set("NACHRICHT_NAME",htmlentities($message['verfasser']));
            } else {

                // Received message

                $this->app->Tpl->Set("NACHRICHT_SENDER",htmlentities($message['verfasser']." <".$message['mail'].">"));

                if ($message['mail_recipients'] != '') {
                  $this->app->Tpl->Set("NACHRICHT_RECIPIENTS",htmlentities($message['mail_recipients']));
                }
                else {
                  // Xentral 20 compatibility
                  $this->app->Tpl->Set("NACHRICHT_RECIPIENTS",htmlentities($message['quelle']));
                }
                $this->app->Tpl->Set("NACHRICHT_CC_RECIPIENTS",htmlentities($message['mail_cc_recipients']));
                $this->app->Tpl->Set("NACHRICHT_BETREFF",'<a href="index.php?module=ticket&action=text&mid='.$message['id'].'&insecure=1" target="_blank">'.htmlentities($message['betreff']).'</a>');
                $this->app->Tpl->Set("NACHRICHT_FLOAT","left");
                $this->app->Tpl->Set("META_FLOAT","right");
                $this->app->Tpl->Set("NACHRICHT_ZEIT",$message['zeit']);            
            }

//            $this->app->Tpl->Set("NACHRICHT_TEXT",$message['text']);
            $this->app->Tpl->Set("NACHRICHT_TEXT",'<iframe class="ticket_text" src="index.php?module=ticket&action=text&mid='.$message['id'].'"></iframe>');


            $this->add_attachments_html($id,$message['id'],'NACHRICHT_ANHANG',false);
         
            $this->app->Tpl->Parse('MESSAGES', "ticket_nachricht.tpl");

        }
    }

    function ticket_text() {           

        $secure_html_tags = array(
            '<br>',
            '<p>',
            '<strong>',
            '<b>',
            '<table>',
            '<tr>',
            '<td>',
            '<style>',
            '<ol>',
            '<ul>',
            '<li>',
            '<dd>',
            '<dt>',
            '<img>'
        );

        $mid = $this->app->Secure->GetGET('mid');
        $insecure = $this->app->Secure->GetGET('insecure');

        if (empty($mid)) {
          return;
        }

        $messages = $this->get_messages_of_ticket("", "n.id = ".$mid, NULL);

        if (empty($messages)) {
        }

        $html_start = "<!DOCTYPE html><html>";
        $head_start = "<head>";
        $security = "";
        $style = "<link rel=\"stylesheet\" type=\"text/css\" href=\"./themes/new/css/ticket_iframe.css?v=3\"/>";
        $head_end = "</head>";
        $html_end = "</html>";
        $prepared_text = $messages[0]['text'];
               
        // Adjust cid images
        $attachments = $this->app->erp->GetDateiSubjektObjekt('Anhang','Ticket',$mid);
        foreach($attachments as $attachment) {
            $filename = $this->app->erp->GetDateiName($attachment);
            $prepared_text = str_replace($filename,'index.php?module=dateien&action=send&id='.$attachment,$prepared_text);
        }
        
        if ($insecure) {            
            // Add Content Security Policy
        } else {
       
            // Add Content Security Policy
            $security = "<meta  http-equiv=\"Content-Security-Policy\" content=\"default-src 'self';\" />";
       
            // Strip html tags
            $stripped_prepared_text = strip_tags($prepared_text,$secure_html_tags);           
           
            if (strlen($stripped_prepared_text) != strlen($prepared_text)) {                                           
                $stripped_prepared_text = "<img class=\"eye blink\" src=\"./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/icon-invisible.svg\" alt=\"Einige Elemente wurden durch OpenXE blockiert.\" title=\"Einige Elemente wurden durch OpenXE blockiert.\" border=\"0\">".$stripped_prepared_text;
            }        
            $prepared_text = $stripped_prepared_text;        
        }        
        $this->app->Tpl->Set("TEXT",$html_start.$head_start.$security.$style.$head_end.$prepared_text.$html_end);        
        $this->app->Tpl->Output('ticket_text.tpl');
        $this->app->ExitXentral();
    }

    function ticket_text_ausgang() {
        $mid = $this->app->Secure->GetGET('mid');

        if (empty($mid)) {
          return;
        }

        $messages = $this->get_messages_of_ticket("", "n.id = ".$mid, NULL);

        if (empty($messages)) {
        }

        $this->app->Tpl->Set("TEXT",$messages[0]['textausgang']);
        $this->app->Tpl->Output('ticket_text.tpl');
        $this->app->ExitXentral();
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

        if ($input['betreff'] == '') {
          $input['betreff'] = "...";
        }

        $input['projekt'] = $this->app->erp->ReplaceProjekt(true,$input['projekt'],true); // Parameters: Target db?, value, from form?
        $input['adresse'] = $this->app->erp->ReplaceAdresse(true,$input['adresse'],true); // Parameters: Target db?, value, from form?
        $input['warteschlange'] = explode(" ",$input['warteschlange'])[0]; // Just the label
        $input['zeit'] = date('Y-m-d H:i:s', time());

        $tags = explode(',',$input['tags']);
        // Replace multiple '!' and '?'
        foreach ($tags as &$tag) {
            $pos = strpos($tag, '?');
            if ($pos !== false) {
                $tag = substr($tag,0,$pos+1) . str_replace('?','',substr($tag,$pos+1));
            }
            $tag = preg_replace("/([?!])\\1+/", "$1", $tag);
        }        
        $input['tags'] = implode(',',$tags);

        $input['tags'] = str_replace(' ?','?',$input['tags']);
        $input['tags'] = str_replace(' !','!',$input['tags']);
        $input['tags'] = str_replace('?!','?',$input['tags']);
        $input['tags'] = str_replace('!?','?',$input['tags']);

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

        $this->ticket_set_self_assigned_status(array($id));

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
        $projekt_id = $this->app->User->DefaultProjekt();
        $projekt = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = ".$projekt_id);

        if ($submit != '') {

            $input['schluessel'] =  $this->generateRandomTicketNumber();       
            $input['kunde'] = $this->app->User->GetName();
          
            $id = $this->ticket_save_to_db($id, $input);

            header("Location: index.php?module=ticket&action=edit&id=$id");
            exit();
        }

        $this->app->Tpl->Set('PROJEKT', $projekt);

        $this->app->Tpl->Set('STATUSICON', $this->ticket_status_icon('neu')."&nbsp;");
        $this->app->YUI->AutoComplete("adresse","adresse");
        $this->app->YUI->AutoComplete("projekt","projektname",1);
        $this->app->YUI->AutoComplete("status","ticketstatus",1);
        $this->app->Tpl->Set('STATUS', $this->app->erp->GetStatusTicketSelect('neu'));
        $this->app->YUI->AutoComplete("warteschlange","warteschlangename");
        $this->app->Tpl->Parse('PAGE', "ticket_create.tpl");
    }


    function ticket_menu($id) {
        $this->app->erp->MenuEintrag("index.php?module=ticket&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=ticket&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
        $anzahldateien = $this->app->erp->AnzahlDateien("ticket_header",$id);
        if ($anzahldateien > 0) {
            $anzahldateien = " (".$anzahldateien.")"; 
        } else {
            $anzahldateien="";
        }
        $this->app->erp->MenuEintrag("index.php?module=ticket&action=dateien&id=$id", "Dateien".$anzahldateien);
    }

    function ticket_edit() {
        $id = $this->app->Secure->GetGET('id');

        if (empty($id)) {
            return;
        }
             
        $this->ticket_menu($id);

        $this->app->Tpl->Set('ID', $id);

        $id = $this->app->Secure->GetGET('id');
        $cmd = $this->app->Secure->GetGET('cmd');
        $input = $this->GetInput();
        $submit = $this->app->Secure->GetPOST('submit');
        $msg = $this->app->erp->base64_url_decode($this->app->Secure->GetGET('msg'));                      

        if ($input['neue_notiz'] != '') {
            $input['notiz'] = $this->app->User->GetName()." ".date("d.m.Y H:i").": ".$input['neue_notiz']."\r\n".$input['notiz'];
        }

        // Always save
        if ($submit != '')
        {
           $this->ticket_save_to_db($id, $input);  
           $msg = "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>";
        }

        // Load values again from database

        $sql = "SELECT t.id, t.schluessel, ".$this->app->erp->FormatDateTimeShort("zeit",'zeit').", p.abkuerzung as projekt, t.bearbeiter, t.quelle, t.status, t.prio, t.adresse, t.kunde, CONCAT(w.label,' ',w.warteschlange) as warteschlange, t.mailadresse, t.betreff, t.zugewiesen, t.inbearbeitung, t.inbearbeitung_user, t.firma, t.notiz, t.bitteantworten, t.service, t.kommentar, t.privat, t.dsgvo, t.tags, t.nachrichten_anz, t.id FROM ticket t LEFT JOIN adresse a ON t.adresse = a.id LEFT JOIN projekt p on t.projekt = p.id LEFT JOIN warteschlangen w on t.warteschlange = w.label WHERE t.id=$id";

        $ticket_from_db = $this->app->DB->SelectArr($sql)[0];

        $ticket_from_db['betreff'] = strip_tags($ticket_from_db['betreff']);

        foreach ($ticket_from_db as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);   
        }
  
      	$this->app->Tpl->Set('PRIO', $ticket_from_db['prio']==1?"checked":"");
       	$this->app->Tpl->Set('STATUSICON', $this->ticket_status_icon($ticket_from_db['status'])."&nbsp;");
        $this->app->YUI->AutoComplete("adresse","adresse");
        $this->app->Tpl->Set('ADRESSE', $this->app->erp->ReplaceAdresse(false,$ticket_from_db['adresse'],false)); // Convert ID to form display


        if ($ticket_from_db['mailadresse'] != "") {
            $this->app->Tpl->Set('MAILADRESSE',"&lt;".$ticket_from_db['mailadresse']."&gt;");
        }

        $this->app->Tpl->Set('ADRESSE_ID',$ticket_from_db['adresse']);

        $this->app->YUI->AutoComplete("projekt","projektname",1);
        $this->app->YUI->AutoComplete("status","ticketstatus",1);
        $this->app->YUI->TagEditor('tags', array('width'=>370));

        $this->app->Tpl->Set('STATUS', $this->app->erp->GetStatusTicketSelect($ticket_from_db['status']));
        $input['projekt'] = $this->app->erp->ReplaceProjekt(false,$input['projekt'],false); // Parameters: Target db?, value, from form?
        $this->app->YUI->AutoComplete("warteschlange","warteschlangename");
        // END Header

        // Check for draft
        $drafted_messages = $this->get_messages_of_ticket($id, "zeitausgang IS NULL AND versendet = '1'",NULL);     

        if (!empty($drafted_messages)) {

            // Draft from form?
            if ($submit != '') {
                $this->save_draft($drafted_messages[0]['id'],$input);
                // Reload        
                $drafted_messages = $this->get_messages_of_ticket($id, "zeitausgang IS NULL AND versendet = '1'",NULL);     
            }

            // Load the draft for editing
            $this->app->Tpl->Set('EMAIL_AN', htmlentities($drafted_messages[0]['mail']));
            $this->app->Tpl->Set('EMAIL_CC', htmlentities($drafted_messages[0]['mail_cc']));
            $this->app->Tpl->Set('EMAIL_BCC', htmlentities($drafted_messages[0]['mail_bcc']));
            $this->app->Tpl->Set('EMAIL_BETREFF', htmlentities($drafted_messages[0]['betreff']));
            $this->app->Tpl->Set('EMAIL_TEXT',htmlentities($drafted_messages[0]['text']));

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
        $messages = $this->get_messages_of_ticket($id, 1, NULL);
        $recv_messages = $this->get_messages_of_ticket($id,"n.versendet != 1",NULL);

        $an_alle = false;

        switch ($submit) {
          case 'neue_email_alle':
            $an_alle = true;
            // break omitted
          case 'neue_email':

            $senderName = $this->app->User->GetName()." (".$this->app->erp->GetFirmaAbsender().")";
            $senderAddress = $this->app->erp->GetFirmaMail();

            if (empty($drafted_messages)) {
                // Create new message and save it for editing   

               	$this->app->Tpl->Set('EMAIL_AN', htmlentities($recv_messages[0]['mail']));

                $to = "";
                $cc = "";
                
                if (!empty($recv_messages)) {
                    if (!str_starts_with(strtoupper($recv_messages[0]['betreff']),"RE:")) {
                        $betreff = "RE: ".strip_tags($recv_messages[0]['betreff']); //+ #20230916 XSS
                    }
                    else {
                        $betreff = strip_tags($recv_messages[0]['betreff']); //+ #20230916 XSS
                    }

                    $to = $recv_messages[0]['mail'];
                    if ($an_alle) {
                        $sql = "SELECT GROUP_CONCAT(DISTINCT `value` ORDER BY `value` SEPARATOR ', ') FROM ticket_header th WHERE th.ticket_nachricht = ".$recv_messages[0]['id']." AND `value` <> '".$senderAddress."' AND type='to'"; 
                        $to_additional = $this->app->DB->Select($sql);
                        if (!empty($to_additional)) {
                          $to .= ", ".$to_additional;
                        }
                        $sql = "SELECT GROUP_CONCAT(DISTINCT `value` ORDER BY `value` SEPARATOR ', ') FROM ticket_header th WHERE th.ticket_nachricht = ".$recv_messages[0]['id']." AND `value` <> '".$senderAddress."' AND type='cc'";
                        $cc = $this->app->DB->Select($sql);
                    } else {
                        $cc = null;
                    }    
                }
                else {
                    $betreff = $ticket_from_db['betreff'];

                    $sql = "SELECT email FROM adresse WHERE id =".$ticket_from_db['adresse'];
                    $to = $this->app->DB->Select($sql);

                }

                $anschreiben = $this->app->DB->Select("SELECT anschreiben FROM adresse WHERE id='".$ticket_from_db['adresse']."' LIMIT 1");
                if($anschreiben=="")
                {
                  $anschreiben = $this->app->erp->Beschriftung("dokument_anschreiben");
                }

                $anschreiben = $anschreiben.",<br>".$this->app->erp->Grussformel($projekt,$sprache);

                $sql = "INSERT INTO `ticket_nachricht` (
                        `ticket`, `zeit`, `text`, `betreff`, `medium`, `versendet`,
                        `verfasser`, `mail`,`status`, `verfasser_replyto`, `mail_replyto`,`mail_cc`
                    ) VALUES ('".
                        $ticket_from_db['schluessel'].
                        "',NOW(),'".
                        $this->app->DB->real_escape_string($anschreiben).
                        "','".
                        $this->app->DB->real_escape_string($betreff).
                        "','email','1','".
                        $this->app->DB->real_escape_string($senderName).
                        "','".
                        $this->app->DB->real_escape_string($to).
                        "','neu','".
                        $this->app->DB->real_escape_string($senderName).
                        "','".
                        $this->app->DB->real_escape_string($senderAddress).
                        "','".
                        $this->app->DB->real_escape_string($cc)."');";

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
          case 'zitat':
            if (!empty($drafted_messages) && !empty($recv_messages)) {

                $nl = "<br />";
                $citation_info =$recv_messages[0]['zeit']." ".$recv_messages[0]['verfasser']." &lt;".$recv_messages[0]['mail']."&gt;";
                $text = $drafted_messages[0]['text'].$nl.$nl.$citation_info.":".$nl."<blockquote type=\"cite\">".$recv_messages[0]['text']."</blockquote>";

                $sql = "UPDATE ticket_nachricht SET text='".$this->app->DB->real_escape_string($text)."' WHERE id=".$drafted_messages[0]['id'];
                $this->app->DB->Update($sql);  
                header("Location: index.php?module=ticket&action=edit&id=$id");
                $this->app->ExitXentral();
            }
          break;
          case 'absenden':            

            if (empty($drafted_messages)) {
                break;
            }

            $msg = "";

            // Enforce Ticket #
            if (!preg_match("/Ticket #[0-9]{12}/i", $drafted_messages[0]['betreff'])) {
              $drafted_messages[0]['betreff'].= " Ticket #".$ticket_from_db['schluessel'];
            }
                
            // Attachments
            $files = $this->app->erp->GetDateiSubjektObjektDateiname('Anhang','Ticket',$drafted_messages[0]['id'],"");

            $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z\-]{2,63})(?:\.[a-z]{2,63})?/i';

            preg_match_all($pattern, $drafted_messages[0]['mail'], $matches);
            $to = $matches[0];

            if ($drafted_messages[0]['mail_cc'] != '') {
              preg_match_all($pattern, $drafted_messages[0]['mail_cc'], $matches);
              $cc = $matches[0];
            } else {
              $cc = null;
            }

            $senderName = $this->app->User->GetName()." (".$this->app->erp->GetFirmaAbsender().")";
            $senderAddress = $this->app->erp->GetFirmaMail();

            //   function MailSend($from,$from_name,$to,$to_name,$betreff,$text,$files="",$projekt="",$signature=true,$cc="",$bcc="", $system = false)

            if (
                $this->app->erp->MailSend(
                  $senderAddress,
                  $senderName,
                  $to,
                  $to,
                  htmlentities($drafted_messages[0]['betreff']),
                  htmlentities($drafted_messages[0]['text']),
                  $files,
                  0,
                  true,
                  $cc,
                  '',
                  true
              ) != 0
            ) {

                // Update message in ticket_nachricht
                $sql = "UPDATE `ticket_nachricht` SET `zeitausgang` = NOW(), `betreff` = '".$this->app->DB->real_escape_string($drafted_messages[0]['betreff'])."', `verfasser` = '$senderName', `verfasser_replyto` = '$senderName', `mail_replyto` = '$senderAddress' WHERE id = ".$drafted_messages[0]['id'];
                $this->app->DB->Insert($sql);

                $msg .=  '<div class="info">Die E-Mail wurde erfolgreich versendet an '.$input['email_an'].'.'; 

                if ($drafted_messages[0]['mail_cc'] != '') {
                  $msg .= ' (CC: '.$drafted_messages[0]['mail_cc'].')</div>';
                }
                else {
                  $msg .= '</div>';
                }
                header("Location: index.php?module=ticket&action=edit&id=".$id."&msg=".$this->app->erp->base64_url_encode($msg));

            }
            else {
              $msg = '<div class="error">Fehler beim Versenden der E-Mail: '.$this->app->erp->mail_error.'</div>';
            }

            // Get messsages again
            $messages = $this->get_messages_of_ticket($id,1,NULL);

          break;
        } 

        $this->add_messages_tpl($messages, false);
        $this->add_attachments_header_html($id,'TICKET_ANHANG');
        $this->app->Tpl->Set('MESSAGE', $msg);
        $this->app->Tpl->Parse('PAGE', "ticket_edit.tpl");
    }

    function ticket_dateien()
    {
        $id = $this->app->Secure->GetGET("id");
        $this->ticket_menu($id);
        $this->app->Tpl->Add('UEBERSCHRIFT'," (Dateien)");
        $this->app->YUI->DateiUpload('PAGE',"ticket_header",$id);
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
    	$input['neue_notiz'] = $this->app->Secure->GetPOST('neue_notiz');
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

  public function ticket_minidetail($parsetarget='',$menu=true) {

      $id = $this->app->Secure->GetGET('id');

      // Get last 3 messages
      $messages = $this->get_messages_of_ticket($id, "1", 3);        

      if(!empty($messages)) {
          $this->add_messages_tpl($messages, true); // With drafts
          $render = true;
      } else {
      }

      if($parsetarget=='')
      {
        if ($render) {
           $this->app->Tpl->Output('ticket_minidetail.tpl');
        }
        $this->app->ExitXentral();
      }
      if ($render) {
        $this->app->Tpl->Parse($parsetarget,'ticket_minidetail.tpl');
      }
  }

  /*
  * After import of Xentral 20 ticket system
  * Set all ticket status to the status of the latest ticket_nachricht
  */
  function ticket_statusfix() {

    $confirmed = $this->app->Secure->GetGET('confirmed');
 
    if ($confirmed == "yes") {

      $sql = "UPDATE
                  ticket
              SET
              STATUS
                  =ifnull((
                  SELECT
                      tn.status
                  FROM
                      ticket_nachricht tn
                  INNER JOIN(
                      SELECT
                          ticket,
                          MAX(zeit) AS lastzeit
                      FROM
                          ticket_nachricht
                      GROUP BY
                          ticket
                  ) l
              ON
                  tn.ticket = l.ticket AND tn.zeit = l.lastzeit
              WHERE
                  ticket.schluessel = tn.ticket   
                      LIMIT 1
              ),'abgeschlossen')
              WHERE ticket.status = 'neu'";

      $this->app->DB->Update($sql);

      $this->app->Tpl->Set('TEXT', "Status fix abgeschlossen.");
      $this->app->Tpl->Parse('PAGE','ticket_text.tpl');
    }
    else {
//      $this->app->Tpl->Set('TEXT', "This will replace all ticket status with the status of the latest ticket_nachricht. To confirm, press here: ");
      $this->app->Tpl->Set('TEXT', "Dieser Assistent ersetzt den Status aller offenen Tickets (Weder abgeschlossen noch Spam) mit dem Status der letzten Nachricht im Ticket. Hier starten: ");
      $this->app->Tpl->Add('TEXT', '<a href="index.php?module=ticket&action=statusfix&confirmed=yes"><button>OK</button></a>');
      $this->app->Tpl->Parse('PAGE','ticket_text.tpl');
    }

  }
  /*
  * After import of Xentral 20 ticket system
  * Set all ticket dates to the date of the latest ticket_nachricht
  */
  function ticket_datefix() {

    $confirmed = $this->app->Secure->GetGET('confirmed');
 
    if ($confirmed == "yes") {

      $sql = "UPDATE ticket set zeit = 
              (SELECT    
                  MAX(zeit) AS lastzeit
              FROM
                  ticket_nachricht
              WHERE ticket.schluessel = ticket_nachricht.ticket AND ticket.schluessel
              LIMIT 1
              )    
              WHERE ticket.status <> 'abgeschlossen' AND ticket.status <> 'spam'";

      $this->app->DB->Update($sql);

      $this->app->Tpl->Set('TEXT', "Datum fix abgeschlossen.");
      $this->app->Tpl->Parse('PAGE','ticket_text.tpl');
    }
    else {
//      $this->app->Tpl->Set('TEXT', "This will replace all open ticket dates with the date of the latest ticket_nachricht. To confirm, press here: ");
      $this->app->Tpl->Set('TEXT', "Dieser Assistent ersetzt das Datum aller offenen Tickets (Weder abgeschlossen noch Spam) mit dem Datum der letzten Nachricht im Ticket. Hier starten: ");
      $this->app->Tpl->Add('TEXT', '<a href="index.php?module=ticket&action=datefix&confirmed=yes"><button>OK</button></a>');
      $this->app->Tpl->Parse('PAGE','ticket_text.tpl');
    }

  }
}



