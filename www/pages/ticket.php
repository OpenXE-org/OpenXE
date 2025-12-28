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
        $this->app->ActionHandler("adddoc", "ticket_beleg_hinzufuegen");
        $this->app->ActionHandler("protokoll", "ticket_protokoll");
        $this->app->ActionHandler("portal_session", "ticket_portal_session");
        $this->app->ActionHandler("portal_status", "ticket_portal_status");
        $this->app->ActionHandler("portal_messages", "ticket_portal_messages");
        $this->app->ActionHandler("portal_message", "ticket_portal_message");
        $this->app->ActionHandler("portal_notifications", "ticket_portal_notifications");
        $this->app->ActionHandler("portal_notification", "ticket_portal_notification");
        $this->app->ActionHandler("portal_offer", "ticket_portal_offer");
        $this->app->ActionHandler("portal_offer_confirm", "ticket_portal_offer_confirm");
        $this->app->ActionHandler("portal_token", "ticket_portal_token");
        $this->app->ActionHandler("portal_magic", "ticket_portal_magic");
        $this->app->ActionHandler("portal_magic_token", "ticket_portal_magic_token");
        $this->app->ActionHandler("portal_print", "ticket_portal_print");
        $this->app->ActionHandler("portal_staff", "ticket_portal_staff");
        $this->app->ActionHandler("portal_plugin_download", "ticket_portal_plugin_download");
        $this->app->ActionHandler("create_customer", "ticket_create_customer");
        $this->app->ActionHandler("create_offer", "ticket_create_offer");
        $this->app->ActionHandler("portal_settings", "ticket_portal_settings");
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
                case 'adddoc':

                    $heading = array('Typ','Belegnr.','Datum','Name', 'Men&uuml;','');
                    $width = array(  '10%',  '10%',   '10%',  '80%',   '1%');

                    $findcols = array('typ','belegnr','datum','name');
                    $searchsql = array('belegnr');

//                    $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=ticket&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>" . "</td></tr></table>";

                    $fileid = $this->app->User->GetParameter('ticket_adddoc_fileid');
                    $ticketid = $this->app->User->GetParameter('ticket_adddoc_ticketid');

                    $beleglink = array (
                        '<a href="index.php?module=',
                        ['sql' => 'LOWER(typ)'],
                        '&action=edit&id=',
                        ['sql' => 'belegid'],
                        '">',
                        ['sql' => 'belegnr'],
                        '</a>'
                    );

                    $addlink = array (
                        '<a href="index.php?module=ticket&action=adddoc&docid=',
                        ['sql' => 'belegid'],
                        '&doctype=',
                        ['sql' => 'LOWER(typ)'],
                        '&fileid='.$fileid,
                        '&id='.$ticketid,
                        '">',
                        '<img src=\"./themes/'.$app->Conf->WFconf['defaulttheme'].'/images/forward.svg\" border=\"0\">',
                        '</a>'
                    );

                    $sql = "
                        SELECT SQL_CALC_FOUND_ROWS
                        belegid,
                        typ,
                        ".$this->app->erp->ConcatSQL($beleglink).",
                        ".$app->erp->FormatDate('datum').",
                        name,
                        ".$this->app->erp->ConcatSQL($addlink)."
                        FROM
                        (
                            SELECT 'Angebot' typ, id belegid, CONCAT(belegnr,' (',anfrage,')') belegnr, datum, name FROM angebot
                            UNION
                            SELECT 'Auftrag' typ, id belegid, CONCAT(belegnr,' (',ihrebestellnummer,')'), datum, name FROM auftrag
                            UNION
                            SELECT 'Verbindlichkeit', v.id, CONCAT(v.belegnr,' (',v.rechnung,')'), v.datum, a.name FROM verbindlichkeit v INNER JOIN adresse a ON v.adresse = a.id WHERE (v.belegnr <> '' OR v.rechnung <> '')
                            UNION
                            SELECT 'Lieferantengutschrift', lg.id, CONCAT(lg.belegnr,' (',lg.rechnung,')'), lg.datum, a.name FROM lieferantengutschrift lg JOIN adresse a ON lg.adresse = a.id WHERE (lg.belegnr <> '' OR lg.rechnung <> '')
                        ) belege
                    ";

                    $where = "(belegnr <> '')";

//                    echo($sql);

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

    function ticket_log_changes($id, bool $onlyread = false, array $old_values = null) {
        $sql = "SELECT status, warteschlange, adresse, name, tags FROM ticket LEFT JOIN adresse ON adresse.id = ticket.adresse WHERE ticket.id = ".$id;
        $values = $this->app->DB->SelectRow($sql);
        if ($onlyread) {
            return($values);
        }
        $changes = array();

        if ($old_values['status'] != $values['status']) {
            $status_values = $this->app->erp->GetTicketStatusValues();
            $changes[] = "Status '".($status_values[$values['status']]?$status_values[$values['status']]:$values['status'])."'";
        }
        if ($old_values['warteschlange'] != $values['warteschlange']) {
            $changes[] = "Warteschlange '".$values['warteschlange']."'";
        }
        if ($old_values['adresse'] != $values['adresse']) {
            $changes[] = "Adresse '".$values['name']."'";
        }
        if ($old_values['tags'] != $values['tags']) {
            $changes[] = "Tags '".$values['tags']."'";
        }
        if (!empty($changes)) {
            $this->app->erp->TicketProtokoll($id, "Ge&auml;ndert: ".implode(', ', $changes));
        }
    }

    public function ticket_protokoll()
    {
        $id = $this->app->Secure->GetGET('id');
        $this->ticket_menu($id);
        $tmp = new EasyTable($this->app);
        $tmp->Query("SELECT zeit,bearbeiter,grund FROM ticket_protokoll WHERE ticket='$id' ORDER by zeit DESC");
        $tmp->DisplayNew('TAB1','Protokoll','noAction');
        $this->app->Tpl->Parse('PAGE','tabview.tpl');
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
                    $oldStatuses = $this->app->DB->SelectArr(
                      "SELECT id, status FROM ticket WHERE id IN (".implode(",", $selectedIds).")"
                    );

                    $sql = "UPDATE ticket SET status = '".$status."', zeit = NOW()";
                    if ($warteschlange != '') {
                        $sql .= ", warteschlange = '".explode(" ",$warteschlange)[0]."'";
                    }

                    $sql .= " WHERE id IN (".implode(",",$selectedIds).")";
                    $this->app->DB->Update($sql);
                    $this->ticket_set_self_assigned_status($selectedIds);
                    if (!empty($oldStatuses)) {
                      $currentStatuses = $this->app->DB->SelectArr(
                        "SELECT id, status FROM ticket WHERE id IN (".implode(",", $selectedIds).")"
                      );
                      $oldMap = [];
                      foreach ($oldStatuses as $row) {
                        $oldMap[(int)$row['id']] = (string)$row['status'];
                      }
                      $userId = (int)$this->app->User->GetID();
                      foreach ($currentStatuses as $row) {
                        $ticketId = (int)$row['id'];
                        if (!isset($oldMap[$ticketId])) {
                          continue;
                        }
                        $this->portalHandleInternalStatusChange($ticketId, $oldMap[$ticketId], (string)$row['status'], $userId);
                      }
                    }
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
        if ($this->app->erp->RechteVorhanden('firmendaten', 'edit')) {
            $this->app->erp->MenuEintrag("index.php?module=ticket&action=portal_settings", "Portal Einstellungen");
        }

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
                    $file_beleg_attachments = $this->app->erp->GetDateiStichwoerter($file_attachment);
                    $linked = false;
                    foreach($file_beleg_attachments as $file_beleg_attachment) {
                        if (in_array($file_beleg_attachment['objekt'],['angebot','auftrag','verbindlichkeit','lieferantengutschrift'])) {
                            $linked = true;
                            break;
                        }
                    }
                    if ($linked) {
                        $attachtext = '<a href=index.php?module='.$file_beleg_attachment['objekt'].'&action=edit&id='.$file_beleg_attachment['parameter'].'><img src="./themes/new/images/check_circle_outlined.svg" width="20" height="20" title="Beleg zugewiesen">';
                    } else {
                        $attachtext = '<a href=index.php?module=ticket&action=adddoc&id='.$ticket_id.'&message='.$message_id.'&fileid='.$file_attachment.'><img src="./themes/new/images/adddoc.svg" width="20" height="20" title="Beleg zuweisen">';
                    }
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

    function add_messages_tpl($ticket_id, $messages, $showdrafts) {

        // Chat-Container Start
        echo '<div class="ticket-chat-container">';
        echo '<div class="chat-messages-wrapper" id="chatMessagesWrapper">';

        $lastDate = null;

        // Add Messages now
        foreach ($messages as $message) {

            $message['betreff'] = strip_tags($message['betreff'] ?? ''); //+ #20230916 XSS

            // Clear this first
            $this->app->Tpl->Set('NACHRICHT_ANHANG',"");
            $this->app->Tpl->Set('NACHRICHT_CC',"");

            if (empty($message['betreff'])) {
                $message['betreff'] = "...";
            }

            // Direction & Alignment Logic (King-Mode: Customer Left / Team Right)
            $isOutgoing = ($message['versendet'] == '1' || !empty($message['textausgang']));
            $direction = $isOutgoing ? 'outgoing' : 'incoming';
            $senderName = $isOutgoing ? ($message['bearbeiter'] ?: 'Team') : ($message['verfasser'] ?: 'Kunde');
            $senderIcon = $isOutgoing ? '👤' : '📧';
            
            $rawTime = $isOutgoing ? ($message['zeitausgang'] ?: $message['zeit']) : $message['zeit'];
            $messageTime = date('H:i', strtotime($rawTime));
            $messageDate = date('Y-m-d', strtotime($rawTime));

            // Date Separator
            if ($lastDate !== $messageDate) {
                $dateLabel = date('d.m.Y', strtotime($messageDate));
                $today = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                
                if ($messageDate === $today) $dateLabel = 'Heute';
                elseif ($messageDate === $yesterday) $dateLabel = 'Gestern';
                
                echo '<div class="chat-date-separator"><span>' . $dateLabel . '</span></div>';
                $lastDate = $messageDate;
            }

            $this->app->Tpl->Set("BUBBLE_DIRECTION", $direction);
            $this->app->Tpl->Set("SENDER_NAME", htmlentities($senderName));
            $this->app->Tpl->Set("SENDER_ICON", $senderIcon);
            $this->app->Tpl->Set("MESSAGE_TIME", $messageTime);

            // CC Information
            $cc = $isOutgoing ? ($message['mail_cc'] ?? '') : ($message['mail_cc_recipients'] ?? '');
            if (!empty($cc)) {
                $this->app->Tpl->Set("NACHRICHT_CC", "CC: " . htmlentities($cc));
            }

            // Xentral 20 compatibility & Message Rendering
            if (!empty($message['textausgang'])) {
              $this->app->Tpl->Set("NACHRICHT_BETREFF", '<a href="index.php?module=ticket&action=text_ausgang&mid='.$message['id'].'" target="_blank">'.htmlentities($message['betreff']).'</a>');
              $this->app->Tpl->Set("NACHRICHT_TEXT", '<iframe class="ticket_text" src="index.php?module=ticket&action=text_ausgang&mid='.$message['id'].'"></iframe>');
            } else {
              $betreffPrefix = (is_null($message['zeitausgang']) && $isOutgoing) ? " (Entwurf)" : "";
              $action = $isOutgoing ? 'text' : 'text'; // Simplified for modern OpenXE
              $this->app->Tpl->Set("NACHRICHT_BETREFF", '<a href="index.php?module=ticket&action=text&mid='.$message['id'].'&insecure=1" target="_blank">'.htmlentities($message['betreff']).$betreffPrefix.'</a>');
              $this->app->Tpl->Set("NACHRICHT_TEXT", '<iframe class="ticket_text" src="index.php?module=ticket&action=text&mid='.$message['id'].'"></iframe>');
            }

            $this->add_attachments_html($ticket_id, $message['id'], 'NACHRICHT_ANHANG', false);
            $this->app->Tpl->Parse('MESSAGES', "ticket_nachricht.tpl");
        }

        // Chat-Container End + Floating Button
        echo '</div>'; // .chat-messages-wrapper
        echo '<button class="chat-scroll-bottom" id="chatScrollBtn" onclick="document.getElementById(\'chatMessagesWrapper\').scrollTop = document.getElementById(\'chatMessagesWrapper\').scrollHeight">↓</button>';
        echo '</div>'; // .ticket-chat-container

        // Inline JS for Scroll Visibility and iFrame resizing
        echo '<script>
            (function() {
                var w = document.getElementById("chatMessagesWrapper");
                var b = document.getElementById("chatScrollBtn");
                if (w && b) {
                    w.scrollTop = w.scrollHeight;
                    w.onscroll = function() {
                        if (w.scrollHeight - w.scrollTop > w.clientHeight + 100) b.classList.add("visible");
                        else b.classList.remove("visible");
                    };
                }
                
                // iFrame Auto-Height - Premium execution
                window.addEventListener("message", function(e) {
                    // Handle height updates if cross-domain isn\'t an issue 
                    // or just use a robust interval/observer approach for local frames
                });

                // Periodic check for local frames
                setInterval(function() {
                    var frames = document.querySelectorAll(".ticket_text");
                    frames.forEach(function(f) {
                        try {
                            var h = f.contentWindow.document.body.scrollHeight;
                            if (h > 0 && Math.abs(f.offsetHeight - h) > 5) f.style.height = h + "px";
                        } catch(e) {}
                    });
                }, 1000);
            })();
        </script>';
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
            $prepared_text = str_replace('cid:'.$filename,'index.php?module=dateien&action=send&id='.$attachment,$prepared_text);
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

    $old = $this->ticket_log_changes($id, onlyread: true);
    $sql = "INSERT INTO ticket (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;
    $this->app->DB->Update($sql);
    $id = $this->app->DB->GetInsertID();
    $this->ticket_set_self_assigned_status(array($id));
    $this->ticket_log_changes($id, old_values: $old);
    $oldStatus = is_array($old) ? (string)($old['status'] ?? '') : '';
    $currentStatus = (string)$this->app->DB->Select("SELECT status FROM ticket WHERE id = ".$id);
    if ($oldStatus !== '' && $currentStatus !== '' && $oldStatus !== $currentStatus) {
      $this->portalHandleInternalStatusChange((int)$id, $oldStatus, $currentStatus, (int)$this->app->User->GetID());
    }

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
        $this->app->erp->MenuEintrag("index.php?module=ticket&action=protokoll&id=$id", "Protokoll");
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
        $portalPublicNote = trim((string)$this->app->Secure->GetPOST('portal_public_note'));
        $portalInternalNote = trim((string)$this->app->Secure->GetPOST('portal_internal_note'));
        $portalNoteRequested = ($submit === 'portal_note');

        if ($input['neue_notiz'] != '') {
            $input['notiz'] = $this->app->User->GetName()." ".date("d.m.Y H:i").": ".$input['neue_notiz']."\r\n".$input['notiz'];
        }

        if ($portalNoteRequested) {
            if (!$this->portalGetSettingBool('ticketportal_enabled')) {
                $msg = '<div class="error">Portal ist deaktiviert.</div>';
            } elseif ($portalPublicNote === '' && $portalInternalNote === '') {
                $msg = '<div class="error">Bitte einen Kommentar eingeben.</div>';
            } else {
                $ticket = $this->portalGetTicketForPortal((int)$id);
                if (!$ticket) {
                    $msg = '<div class="error">Ticket nicht gefunden.</div>';
                } else {
                    $userId = (int)$this->app->User->GetID();
                    if ($portalPublicNote !== '') {
                        $this->portalInsertPortalMessage($ticket, 'staff', $userId, $portalPublicNote, true);
                    }
                    if ($portalInternalNote !== '') {
                        $this->portalInsertPortalMessage($ticket, 'staff', $userId, $portalInternalNote, false);
                    }
                    $msg = '<div class="info">Portal-Kommentar gespeichert.</div>';
                }
            }
            $msg = $this->app->erp->base64_url_encode($msg);
            header("Location: index.php?module=ticket&action=edit&id=$id&msg=$msg");
            $this->app->ExitXentral();
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
        $addressId = (int)$ticket_from_db['adresse'];
        $ticketId = (int)$id;
        $createCustomerButton = '';
        $createOfferButton = '';
        $portalTokenButton = '';
        $portalLinkButton = '';
        $portalMagicButton = '';
        $portalCommentsBlock = '';
        if ($this->app->erp->RechteVorhanden('adresse', 'edit') && $addressId <= 0) {
            $createCustomerButton = '<td><button type="button" class="ui-button-icon" style="width:100%;" onclick="window.location.href=\'index.php?module=ticket&action=create_customer&id='.$ticketId.'\';">Kunde anlegen</button></td></tr>';
        }
        if ($this->app->erp->RechteVorhanden('angebot', 'edit') && $addressId > 0) {
            $createOfferButton = '<td><button type="button" class="ui-button-icon" style="width:100%;" onclick="window.location.href=\'index.php?module=ticket&action=create_offer&id='.$ticketId.'\';">Angebot anlegen</button></td></tr>';
        }
        if ($this->app->erp->RechteVorhanden('ticket', 'edit') && $this->portalGetSettingBool('ticketportal_enabled')) {
            $portalUrl = trim((string)$this->app->erp->Firmendaten('ticketportal_portal_url'));
            $portalTicketNumber = (string)$ticket_from_db['schluessel'];
            $portalTokenButton = '<td><button type="button" class="ui-button-icon" style="width:100%;" id="ticket-portal-token-btn" data-ticket="'.$ticketId.'">Portal-Hash kopieren</button></td></tr>';
            $portalLinkButton = '<td><button type="button" class="ui-button-icon" style="width:100%;" id="ticket-portal-link-btn" data-ticket="'.$ticketId.'" data-portal-url="'.htmlentities($portalUrl).'" data-ticket-number="'.htmlentities($portalTicketNumber).'">Portal-Link kopieren</button></td></tr>';
            $portalMagicButton = '<td><button type="button" class="ui-button-icon" style="width:100%;" id="ticket-portal-magic-btn" data-ticket="'.$ticketId.'" data-portal-url="'.htmlentities($portalUrl).'">Magic Link kopieren</button></td></tr>';
            $this->app->Tpl->Add('JQUERYREADY', "
              function portalCopyText(text, label) {
                if (!text) {
                  return;
                }
                if (navigator.clipboard && navigator.clipboard.writeText) {
                  navigator.clipboard.writeText(text).catch(function() {});
                }
                window.prompt(label, text);
              }
              function portalFetchToken(ticketId, done) {
                $.getJSON('index.php?module=ticket&action=portal_token&id=' + ticketId)
                  .done(function(resp) {
                    if (resp && resp.token) {
                      done(null, resp.token);
                      return;
                    }
                    var msg = (resp && resp.error) ? resp.error : 'Token konnte nicht geladen werden.';
                    done(msg, null);
                  })
                  .fail(function() {
                    done('Token konnte nicht geladen werden.', null);
                  });
              }
              function portalFetchMagicToken(ticketId, done) {
                $.getJSON('index.php?module=ticket&action=portal_magic_token&id=' + ticketId)
                  .done(function(resp) {
                    if (resp && resp.token) {
                      done(null, resp.token, resp.expires_at || '');
                      return;
                    }
                    var msg = (resp && resp.error) ? resp.error : 'Magic Link konnte nicht geladen werden.';
                    done(msg, null, '');
                  })
                  .fail(function() {
                    done('Magic Link konnte nicht geladen werden.', null, '');
                  });
              }
              $('#ticket-portal-token-btn').on('click', function(e) {
                e.preventDefault();
                var btn = $(this);
                var ticketId = btn.data('ticket');
                if (!ticketId) {
                  alert('Ticket ID fehlt.');
                  return;
                }
                btn.prop('disabled', true);
                portalFetchToken(ticketId, function(err, token) {
                  if (err) {
                    alert(err);
                  } else {
                    portalCopyText(token, 'Portal-Hash (kopieren):');
                  }
                  btn.prop('disabled', false);
                });
              });
              $('#ticket-portal-link-btn').on('click', function(e) {
                e.preventDefault();
                var btn = $(this);
                var ticketId = btn.data('ticket');
                var portalUrl = btn.data('portal-url') || '';
                var ticketNumber = btn.data('ticket-number') || '';
                if (!ticketId) {
                  alert('Ticket ID fehlt.');
                  return;
                }
                if (!portalUrl) {
                  alert('Portal URL fehlt. Bitte in den Portal-Einstellungen setzen.');
                  return;
                }
                if (!ticketNumber) {
                  alert('Ticketnummer fehlt.');
                  return;
                }
                btn.prop('disabled', true);
                var separator = portalUrl.indexOf('?') === -1 ? '?' : '&';
                var params = 'ticket_number=' + encodeURIComponent(ticketNumber);
                var link = portalUrl + separator + params;
                portalCopyText(link, 'Portal-Link (kopieren):');
                btn.prop('disabled', false);
              });
              $('#ticket-portal-magic-btn').on('click', function(e) {
                e.preventDefault();
                var btn = $(this);
                var ticketId = btn.data('ticket');
                var portalUrl = btn.data('portal-url') || '';
                if (!ticketId) {
                  alert('Ticket ID fehlt.');
                  return;
                }
                if (!portalUrl) {
                  alert('Portal URL fehlt. Bitte in den Portal-Einstellungen setzen.');
                  return;
                }
                btn.prop('disabled', true);
                portalFetchMagicToken(ticketId, function(err, token, expiresAt) {
                  if (err) {
                    alert(err);
                  } else {
                    var separator = portalUrl.indexOf('?') === -1 ? '?' : '&';
                    var params = 'magic_token=' + encodeURIComponent(token);
                    var link = portalUrl + separator + params;
                    var label = 'Magic Link (kopieren):';
                    if (expiresAt) {
                      label = 'Magic Link (gueltig bis ' + expiresAt + '):';
                    }
                    portalCopyText(link, label);
                  }
                  btn.prop('disabled', false);
                });
              });
            ");
            $portalCommentsBlock = '
              <div class="row">
                <div class="row-height">
                  <div class="col-xs-14 col-md-12 col-md-height">
                    <div class="inside inside-full-height">
                      <fieldset>
                        <legend>Portal Kommentare</legend>
                        <table width="100%" border="0" class="mkTableFormular">
                          <tr><td>{|Kommentar fuer Kunden|}:</td><td><textarea name="portal_public_note" rows="3" style="width:100%;"></textarea></td></tr>
                          <tr><td>{|Interne Notiz|}:</td><td><textarea name="portal_internal_note" rows="3" style="width:100%;"></textarea></td></tr>
                          <tr><td></td><td><button name="submit" value="portal_note" class="ui-button-icon">Portal-Kommentar speichern</button></td></tr>
                        </table>
                      </fieldset>
                    </div>
                  </div>
                </div>
              </div>';
        }
        $this->app->Tpl->Set('CREATE_CUSTOMER_BUTTON', $createCustomerButton);
        $this->app->Tpl->Set('CREATE_OFFER_BUTTON', $createOfferButton);
        $this->app->Tpl->Set('PORTAL_TOKEN_BUTTON', $portalTokenButton);
        $this->app->Tpl->Set('PORTAL_LINK_BUTTON', $portalLinkButton);
        $this->app->Tpl->Set('PORTAL_MAGIC_BUTTON', $portalMagicButton);
        $this->app->Tpl->Set('PORTAL_COMMENTS_BLOCK', $portalCommentsBlock);

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

        $this->add_messages_tpl($id, $messages, false);
        $this->add_attachments_header_html($id,'TICKET_ANHANG');
        $this->app->Tpl->Set('MESSAGE', $msg);

        $belege = $this->app->erp->GetTicketBelege($id);

        if (!empty($belege)) {
            function beleglink($beleg) {
               return "<a href=index.php?module=".$beleg['doctype']."&action=edit&id=".$beleg['id'].">".(empty($beleg['belegnr'])?'ENTWURF':$beleg['belegnr']).(empty($beleg['externenr'])?"":" (".$beleg['externenr'].")")."</a>";
            }
            $this->app->Tpl->AddMessage('info',"Zu diesem Ticket geh&ouml;ren Belege: ".implode(', ',array_map('beleglink', $belege)), html: true);
        }
        $this->app->Tpl->Parse('PAGE', "ticket_edit.tpl");
    }

    public function ticket_create_customer()
    {
        $ticketId = (int)$this->app->Secure->GetGET('id');
        if ($ticketId <= 0) {
            return;
        }
        if (!$this->app->erp->RechteVorhanden('adresse', 'edit')) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Keine Berechtigung um Kunden anzulegen.</div>');
            header("Location: index.php?module=ticket&action=edit&id=$ticketId&msg=$msg");
            $this->app->ExitXentral();
        }

        $ticket = $this->app->DB->SelectRow(
            "SELECT id, schluessel, adresse, kunde, mailadresse, projekt
             FROM ticket
             WHERE id = $ticketId
             LIMIT 1"
        );
        if (empty($ticket)) {
            return;
        }
        if (!empty($ticket['adresse']) && (int)$ticket['adresse'] > 0) {
            $msg = $this->app->erp->base64_url_encode('<div class="info">Ticket hat bereits eine Adresse.</div>');
            header("Location: index.php?module=ticket&action=edit&id=$ticketId&msg=$msg");
            $this->app->ExitXentral();
        }

        $name = trim((string)$ticket['kunde']);
        $email = trim((string)$ticket['mailadresse']);
        if ($name === '') {
            $name = $email;
        }
        if ($name === '') {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Kein Name oder E-Mail im Ticket gefunden.</div>');
            header("Location: index.php?module=ticket&action=edit&id=$ticketId&msg=$msg");
            $this->app->ExitXentral();
        }

        $adresseId = (int)$this->app->erp->CreateAdresse($this->app->DB->real_escape_string($name));
        if ($adresseId <= 0) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Kunde konnte nicht angelegt werden.</div>');
            header("Location: index.php?module=ticket&action=edit&id=$ticketId&msg=$msg");
            $this->app->ExitXentral();
        }

        $updateParts = [];
        if ($email !== '') {
            $updateParts[] = "email = '".$this->app->DB->real_escape_string($email)."'";
        }
        if (!empty($ticket['projekt']) && (int)$ticket['projekt'] > 0) {
            $updateParts[] = "projekt = ".(int)$ticket['projekt'];
        }
        if (!empty($updateParts)) {
            $this->app->DB->Update("UPDATE adresse SET ".implode(', ', $updateParts)." WHERE id = ".$adresseId." LIMIT 1");
        }
        if (!empty($ticket['projekt']) && (int)$ticket['projekt'] > 0) {
            $this->app->erp->AddRolleZuAdresse($adresseId, 'Kunde', 'von', 'Projekt', (int)$ticket['projekt']);
        }

        $this->app->DB->Update("UPDATE ticket SET adresse = ".$adresseId." WHERE id = ".$ticketId." LIMIT 1");
        $this->app->erp->TicketProtokoll($ticketId, 'Kunde angelegt (Adresse #'.$adresseId.')');

        $link = 'index.php?module=adresse&action=edit&id='.$adresseId;
        $msg = $this->app->erp->base64_url_encode('<div class="success">Kunde angelegt: <a href="'.$link.'">Adresse #'.$adresseId.'</a></div>');
        header("Location: index.php?module=ticket&action=edit&id=$ticketId&msg=$msg");
        $this->app->ExitXentral();
    }

    public function ticket_create_offer()
    {
        $ticketId = (int)$this->app->Secure->GetGET('id');
        if ($ticketId <= 0) {
            return;
        }
        if (!$this->app->erp->RechteVorhanden('angebot', 'edit')) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Keine Berechtigung um Angebote anzulegen.</div>');
            header("Location: index.php?module=ticket&action=edit&id=$ticketId&msg=$msg");
            $this->app->ExitXentral();
        }

        $ticket = $this->app->DB->SelectRow(
            "SELECT id, schluessel, adresse, betreff
             FROM ticket
             WHERE id = $ticketId
             LIMIT 1"
        );
        if (empty($ticket)) {
            return;
        }
        $addressId = (int)$ticket['adresse'];
        if ($addressId <= 0) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Keine Adresse am Ticket hinterlegt.</div>');
            header("Location: index.php?module=ticket&action=edit&id=$ticketId&msg=$msg");
            $this->app->ExitXentral();
        }

        $angebotId = (int)$this->app->erp->CreateAngebot($addressId);
        if ($angebotId <= 0) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Angebot konnte nicht angelegt werden.</div>');
            header("Location: index.php?module=ticket&action=edit&id=$ticketId&msg=$msg");
            $this->app->ExitXentral();
        }
        $this->app->erp->LoadAngebotStandardwerte($angebotId, $addressId);

        $updates = [];
        if (!empty($ticket['schluessel'])) {
            $updates[] = "anfrage = '".$this->app->DB->real_escape_string($ticket['schluessel'])."'";
        }
        $betreff = trim(strip_tags((string)$ticket['betreff']));
        if ($betreff !== '') {
            $updates[] = "betreff = '".$this->app->DB->real_escape_string($betreff)."'";
        }
        if (!empty($updates)) {
            $this->app->DB->Update("UPDATE angebot SET ".implode(', ', $updates)." WHERE id = ".$angebotId." LIMIT 1");
        }

        // Automatic Status Change for Portal
        $this->portalSetCustomerStatus($ticketId, 'warten_kd', 'Angebot erstellt', null);
        $this->portalLogStatus($ticketId, null, 'warten_kd', null, 'Angebot #' . $angebotId . ' erstellt', null);
        $this->portalInsertPortalMessage($ticket, 'system', 0, 'Ein Angebot (#'.$angebotId.') wurde für Sie erstellt.', true);

        $this->app->erp->TicketProtokoll($ticketId, 'Angebot angelegt (#'.$angebotId.') - Portal Status: Warten auf Kunde');
        header("Location: index.php?module=angebot&action=edit&id=$angebotId");
        $this->app->ExitXentral();
    }

    function ticket_dateien()
    {
        $id = $this->app->Secure->GetGET("id");
        $this->ticket_menu($id);
        $this->app->Tpl->Add('UEBERSCHRIFT'," (Dateien)");
        $this->app->YUI->DateiUpload('PAGE',"ticket_header",$id);
    }

    function ticket_beleg_hinzufuegen()
    {
        $id = $this->app->Secure->GetGET("id");
        $fileid = $this->app->Secure->GetGET("fileid");
        $this->ticket_menu($id);
        $this->app->Tpl->Add('UEBERSCHRIFT'," (Dateien)");

        $docid = $this->app->Secure->GetGET("docid");
        if ($docid) {
            $doctype = $this->app->Secure->GetGET("doctype");
            $sendto = "ticket&action=edit&id=".$id;
        }

        $adresse = $this->app->DB->Select("SELECT adresse FROM ticket WHERE id = ".$id);
        if (empty($adresse)) {
            $this->app->Tpl->AddMessage("info", "Keine Adresse hinterlegt");
        }

        $submit = $this->app->Secure->GetPOST("submit");

        switch ($submit) {
            case 'angebotneu':
                $docid = $this->app->erp->CreateAngebot($adresse);
                if (!empty($docid)) {
                    $doctype = 'angebot';
                    $sendto = "angebot&action=edit&id=".$docid;
                } else {
                    $error_msg = 'Angebot konnte nicht angelegt werden.';
                }
            break;
            case 'auftragneu':
                $docid = $this->app->erp->CreateAuftrag($adresse);
                if (!empty($docid)) {
                    $doctype = 'auftrag';
                    $sendto = "auftrag&action=edit&id=".$docid;
                } else {
                    $error_msg = 'Auftrag konnte nicht angelegt werden.';
                }
            break;
            case 'verbindlichkeitneu':
                $datum = $this->app->erp->GetDateiDatum($fileid);
                $docid = $this->app->erp->CreateVerbindlichkeit($adresse, $datum);
                if (!empty($docid)) {
                    $doctype = 'verbindlichkeit';
                    $sendto = "verbindlichkeit&action=edit&id=".$docid;
                } else {
                    $error_msg = 'Verbindlichkeit konnte nicht angelegt werden.';
                }
            break;
            case 'lieferantengutschriftneu':
                $datum = $this->app->erp->GetDateiDatum($fileid);
                $docid = $this->app->erp->CreateLieferantengutschrift($adresse, $datum);
                if (!empty($docid)) {
                    $doctype = 'lieferantengutschrift';
                    $sendto = "lieferantengutschrift&action=edit&id=".$docid;
                } else {
                    $error_msg = 'Lieferantengutschrift konnte nicht angelegt werden.';
                }
            break;
        }

        if ($docid) {
            $this->app->erp->AddDateiStichwort($fileid, "Anhang", $doctype , $docid);
            header("Location: index.php?module=".$sendto);
        }

        if ($error_msg) {
            $this->app->Tpl->addMessage('error', $error_msg);
        }

        // For transfer to tablesearch
        $this->app->User->SetParameter('ticket_adddoc_fileid', $fileid);
        $this->app->User->SetParameter('ticket_adddoc_ticketid', $id);

        $this->app->YUI->TableSearch('TAB1', 'adddoc', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "ticket_adddoc.tpl");
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
          $this->add_messages_tpl($id, $messages, true); // With drafts
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
      $oldStatuses = $this->app->DB->SelectArr(
        "SELECT id, status FROM ticket WHERE status = 'neu'"
      );

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
      if (!empty($oldStatuses)) {
        $ids = array_map(static function ($row) {
          return (int)$row['id'];
        }, $oldStatuses);
        $ids = array_filter($ids);
        if (!empty($ids)) {
          $currentStatuses = $this->app->DB->SelectArr(
            "SELECT id, status FROM ticket WHERE id IN (".implode(",", $ids).")"
          );
          $oldMap = [];
          foreach ($oldStatuses as $row) {
            $oldMap[(int)$row['id']] = (string)$row['status'];
          }
          $userId = (int)$this->app->User->GetID();
          foreach ($currentStatuses as $row) {
            $ticketId = (int)$row['id'];
            if (!isset($oldMap[$ticketId])) {
              continue;
            }
            $this->portalHandleInternalStatusChange($ticketId, $oldMap[$ticketId], (string)$row['status'], $userId);
          }
        }
      }

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

  private function portalJsonResponse(array $payload, int $statusCode = 200): void
  {
    if ($statusCode >= 400) {
      $action = (string)($_GET['action'] ?? '');
      $this->portalLog('warning', 'portal_error', [
        'action' => $action,
        'status' => $statusCode,
        'payload' => $payload,
        'method' => (string)($_SERVER['REQUEST_METHOD'] ?? ''),
        'ip' => $this->portalGetRequestIp(),
      ]);
    }
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload);
    $this->app->ExitXentral();
  }

  private function portalReadJsonInput(): array
  {
    $raw = file_get_contents('php://input');
    if (!empty($raw)) {
      $decoded = json_decode($raw, true);
      if (is_array($decoded)) {
        return $decoded;
      }
    }
    return $_POST ?? [];
  }

  private function portalGetSettingBool(string $name, bool $default = false): bool
  {
    $value = (string)$this->app->erp->Firmendaten($name);
    if ($value === '') {
      return $default;
    }
    return $value === '1';
  }

  private function portalGetSettingInt(string $name, int $default): int
  {
    $value = (int)$this->app->erp->Firmendaten($name);
    return $value > 0 ? $value : $default;
  }

  private function portalLogEnabled(): bool
  {
    return $this->portalGetSettingBool('ticketportal_log_enabled', false);
  }

  private function portalGetLogPath(): string
  {
    $dir = sys_get_temp_dir();
    if ($dir === '' || !is_dir($dir)) {
      $dir = dirname(__DIR__, 2);
    }
    return rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'openxe-ticket-portal.log';
  }

  private function portalReadLogTail(int $maxLines = 200, int $maxBytes = 20000): string
  {
    $path = $this->portalGetLogPath();
    if (!is_file($path) || !is_readable($path)) {
      return '';
    }
    $size = filesize($path);
    if ($size === false || $size <= 0) {
      return '';
    }
    $bytes = min($maxBytes, (int)$size);
    $fp = fopen($path, 'rb');
    if ($fp === false) {
      return '';
    }
    if ($bytes < $size) {
      fseek($fp, -$bytes, SEEK_END);
    }
    $data = fread($fp, $bytes);
    fclose($fp);
    if ($data === false || $data === '') {
      return '';
    }
    $lines = preg_split('/\\r\\n|\\r|\\n/', $data);
    if ($bytes < $size && !empty($lines)) {
      array_shift($lines);
    }
    if (count($lines) > $maxLines) {
      $lines = array_slice($lines, -$maxLines);
    }
    return implode("\n", $lines);
  }

  private function portalMaskValue(string $value): string
  {
    $value = (string)$value;
    $len = strlen($value);
    if ($len <= 4) {
      return '****';
    }
    return substr($value, 0, 2).'***'.substr($value, -2);
  }

  private function portalSanitizeLogContext($value)
  {
    if (is_array($value)) {
      $sanitized = [];
      foreach ($value as $key => $item) {
        $keyLower = strtolower((string)$key);
        if (in_array($keyLower, ['token', 'magic_token', 'session_token', 'verifier_value', 'shared_secret', 'x-openxe-portal-secret'], true)) {
          $sanitized[$key] = $this->portalMaskValue((string)$item);
        } else {
          $sanitized[$key] = $this->portalSanitizeLogContext($item);
        }
      }
      return $sanitized;
    }
    if (is_scalar($value)) {
      return (string)$value;
    }
    return $value;
  }

  private function portalLog(string $level, string $message, array $context = []): void
  {
    if (!$this->portalLogEnabled()) {
      return;
    }
    $context = $this->portalSanitizeLogContext($context);
    try {
      if (isset($this->app->Container)) {
        $logger = $this->app->Container->get('Logger');
        if ($logger && method_exists($logger, $level)) {
          $logger->$level($message, $context);
          return;
        }
      }
    } catch (Exception $e) {
    }
    $payload = [
      'time' => gmdate('c'),
      'level' => $level,
      'message' => $message,
      'context' => $context,
    ];
    $line = json_encode($payload);
    if ($line !== false) {
      @file_put_contents($this->portalGetLogPath(), $line.PHP_EOL, FILE_APPEND);
      error_log($line);
    }
  }

  private function portalRequireSharedSecret(): void
  {
    $secret = trim((string)$this->app->erp->Firmendaten('ticketportal_shared_secret'));
    if ($secret === '') {
      return;
    }
    $header = (string)($_SERVER['HTTP_X_OPENXE_PORTAL_SECRET'] ?? '');
    if ($header === '' || !hash_equals($secret, $header)) {
      $this->portalJsonResponse(['error' => 'forbidden'], 403);
    }
  }

  private function portalDefaultNotifySubject(): string
  {
    return 'OpenXE Service - Statusaktualisierung zu Ticket #{ticket_number}';
  }

  private function portalDefaultNotifyBody(): string
  {
    return "Guten Tag {customer_name},\n\n".
      "wir informieren Sie ueber den aktuellen Stand Ihres Reparaturtickets #{ticket_number}.\n\n".
      "Aktueller Status: {status_label}\n".
      "Hinweis: {public_note}\n\n".
      "Falls Sie Fragen haben, antworten Sie bitte auf diese Nachricht und nennen Sie die Ticketnummer.\n\n".
      "Mit freundlichen Gruessen\n".
      "{company_name}";
  }

  private function portalEnsureFirmendatenDefaults(): void
  {
    $defaults = [
      'ticketportal_enabled' => ['tinyint', '1', '', '0', '0', 0, 0],
      'ticketportal_portal_url' => ['varchar', '255', '', '', '', 0, 0],
      'ticketportal_allow_offer_confirm' => ['tinyint', '1', '', '0', '0', 0, 0],
      'ticketportal_allow_customer_comments' => ['tinyint', '1', '', '0', '0', 0, 0],
      'ticketportal_notify_all_status' => ['tinyint', '1', '', '1', '1', 0, 0],
      'ticketportal_agb_url' => ['varchar', '255', '', '', '', 0, 0],
      'ticketportal_agb_version' => ['varchar', '64', '', '', '', 0, 0],
      'ticketportal_shared_secret' => ['varchar', '255', '', '', '', 0, 0],
      'ticketportal_log_enabled' => ['tinyint', '1', '', '0', '0', 0, 0],
      'ticketportal_session_ttl_min' => ['int', '11', '', '60', '60', 0, 0],
      'ticketportal_code_ttl_min' => ['int', '11', '', '15', '15', 0, 0],
      'ticketportal_magic_ttl_min' => ['int', '11', '', '30', '30', 0, 0],
      'ticketportal_doi_ttl_min' => ['int', '11', '', '120', '120', 0, 0],
      'ticketportal_max_attempts' => ['int', '11', '', '5', '5', 0, 0],
      'ticketportal_lockout_min' => ['int', '11', '', '15', '15', 0, 0],
      'ticketportal_status_labels' => ['text', '', '', json_encode($this->portalDefaultStatusLabels()), '', 0, 0],
      'ticketportal_status_map' => ['text', '', '', json_encode($this->portalDefaultStatusMap()), '', 0, 0],
      'ticketportal_notify_subject' => ['varchar', '255', '', $this->portalDefaultNotifySubject(), $this->portalDefaultNotifySubject(), 0, 0],
      'ticketportal_notify_body' => ['text', '', '', $this->portalDefaultNotifyBody(), '', 0, 0],
    ];
    foreach ($defaults as $name => $data) {
      $exists = $this->app->DB->Select(
        "SELECT id FROM firmendaten_werte WHERE name = '".$this->app->DB->real_escape_string($name)."' LIMIT 1"
      );
      if ($exists) {
        continue;
      }
      $this->app->erp->AddNeuenFirmendatenWert(
        $name,
        $data[0],
        $data[1],
        $data[2],
        $data[3],
        $data[4],
        $data[5],
        $data[6]
      );
    }
  }

  private function portalHashToken(string $token): string
  {
    return hash('sha256', $token);
  }

  private function portalGenerateToken(int $bytes = 32): string
  {
    return bin2hex(random_bytes($bytes));
  }

  private function portalGenerateCode(): string
  {
    return (string)random_int(100000, 999999);
  }

  private function portalNormalizeEmail(string $email): string
  {
    return strtolower(trim($email));
  }

  private function portalNormalizePlz(string $plz): string
  {
    return preg_replace('/\\s+/', '', trim($plz));
  }

  private function portalGetRequestIp(): string
  {
    return (string)($_SERVER['REMOTE_ADDR'] ?? '');
  }

  private function portalGetRequestUa(): string
  {
    return substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
  }

  private function portalGetAccessByToken(string $token, string $scope): ?array
  {
    if ($token === '') {
      return null;
    }
    $hash = $this->portalHashToken($token);
    $hashEsc = $this->app->DB->real_escape_string($hash);
    $scopeEsc = $this->app->DB->real_escape_string($scope);
    $sql = "SELECT * FROM ticket_portal_access
            WHERE token_hash = '$hashEsc'
              AND scope = '$scopeEsc'
              AND revoked_at IS NULL
              AND (expires_at IS NULL OR expires_at > NOW())
            LIMIT 1";
    $row = $this->app->DB->SelectRow($sql);
    return !empty($row) ? $row : null;
  }

  private function portalGetOrCreateLookupAccess(int $ticketId, string $ticketNumber): array
  {
    $ticketId = (int)$ticketId;
    $scope = 'lookup';
    $row = $this->app->DB->SelectRow(
      "SELECT * FROM ticket_portal_access
       WHERE ticket_id = $ticketId AND scope = '$scope' AND revoked_at IS NULL
       LIMIT 1"
    );
    if (!empty($row)) {
      return $row;
    }
    $tokenHash = $this->app->DB->real_escape_string($this->portalHashToken($ticketNumber));
    $this->app->DB->Insert(
      "INSERT INTO ticket_portal_access (ticket_id, token_hash, scope, created_at)
       VALUES ($ticketId, '$tokenHash', '$scope', NOW())"
    );
    $newId = (int)$this->app->DB->GetInsertID();
    $row = $this->app->DB->SelectRow("SELECT * FROM ticket_portal_access WHERE id = $newId LIMIT 1");
    return !empty($row) ? $row : [];
  }

  private function portalIsAccessLocked(array $access): bool
  {
    if (empty($access['locked_until'])) {
      return false;
    }
    $lockedUntil = strtotime((string)$access['locked_until']);
    if ($lockedUntil !== false && $lockedUntil <= time()) {
      $this->portalResetAccessFailures((int)$access['id']);
      return false;
    }
    return true;
  }

  private function portalRegisterAccessFailure(int $accessId, int $currentAttempts): array
  {
    $maxAttempts = $this->portalGetSettingInt('ticketportal_max_attempts', 5);
    $lockoutMin = $this->portalGetSettingInt('ticketportal_lockout_min', 15);
    $nextAttempts = max(0, $currentAttempts) + 1;
    $lockedUntil = null;
    if ($nextAttempts >= $maxAttempts) {
      $lockedUntil = date('Y-m-d H:i:s', time() + ($lockoutMin * 60));
    }
    $lockedSql = $lockedUntil !== null ? "'".$this->app->DB->real_escape_string($lockedUntil)."'" : 'NULL';
    $this->app->DB->Update(
      "UPDATE ticket_portal_access
       SET failed_attempts = ".(int)$nextAttempts.",
           last_failed_at = NOW(),
           locked_until = ".$lockedSql."
       WHERE id = ".(int)$accessId." LIMIT 1"
    );
    return ['attempts' => $nextAttempts, 'locked_until' => $lockedUntil];
  }

  private function portalResetAccessFailures(int $accessId): void
  {
    $this->app->DB->Update(
      "UPDATE ticket_portal_access
       SET failed_attempts = 0, last_failed_at = NULL, locked_until = NULL
       WHERE id = ".(int)$accessId." LIMIT 1"
    );
  }

  private function portalTouchAccess(int $accessId): void
  {
    $ip = $this->app->DB->real_escape_string($this->portalGetRequestIp());
    $ua = $this->app->DB->real_escape_string($this->portalGetRequestUa());
    $this->app->DB->Update(
      "UPDATE ticket_portal_access
       SET last_access_at = NOW(), last_access_ip = '$ip', last_access_ua = '$ua'
       WHERE id = ".(int)$accessId." LIMIT 1"
    );
  }

  private function portalReadSessionToken(array $data): string
  {
    if (!empty($data['session_token'])) {
      return trim((string)$data['session_token']);
    }
    if ($this->app->Secure->GetGET('session_token')) {
      return trim((string)$this->app->Secure->GetGET('session_token'));
    }
    if (!empty($_SERVER['HTTP_AUTHORIZATION']) && stripos($_SERVER['HTTP_AUTHORIZATION'], 'Bearer ') === 0) {
      return trim(substr($_SERVER['HTTP_AUTHORIZATION'], 7));
    }
    return '';
  }

  private function portalGetSessionAccess(array $data): ?array
  {
    $sessionToken = $this->portalReadSessionToken($data);
    $access = $this->portalGetAccessByToken($sessionToken, 'session');
    if ($access) {
      $this->portalTouchAccess((int)$access['id']);
    }
    return $access;
  }

  private function portalGetTicketForPortal(int $ticketId): ?array
  {
    $ticketId = (int)$ticketId;
    if ($ticketId <= 0) {
      return null;
    }
    $sql = "SELECT t.id, t.schluessel, t.status, t.mailadresse, t.adresse,
                   t.betreff, t.notiz, t.kommentar,
                   a.name AS customer_name, a.email AS customer_email, a.plz AS customer_plz
            FROM ticket t
            LEFT JOIN adresse a ON t.adresse = a.id
            WHERE t.id = $ticketId
            LIMIT 1";
    $row = $this->app->DB->SelectRow($sql);
    return !empty($row) ? $row : null;
  }

  private function portalGetTicketByNumber(string $ticketNumber): ?array
  {
    $ticketNumber = trim($ticketNumber);
    if ($ticketNumber === '') {
      return null;
    }
    $ticketNumberEsc = $this->app->DB->real_escape_string($ticketNumber);
    $sql = "SELECT t.id, t.schluessel, t.status, t.mailadresse, t.adresse,
                   t.betreff, t.notiz, t.kommentar,
                   a.name AS customer_name, a.email AS customer_email, a.plz AS customer_plz
            FROM ticket t
            LEFT JOIN adresse a ON t.adresse = a.id
            WHERE t.schluessel = '$ticketNumberEsc'
            LIMIT 1";
    $row = $this->app->DB->SelectRow($sql);
    return !empty($row) ? $row : null;
  }

  private function portalCreateSession(int $ticketId): array
  {
    $ttl = $this->portalGetSettingInt('ticketportal_session_ttl_min', 60);
    $expiresAt = date('Y-m-d H:i:s', time() + ($ttl * 60));
    $token = $this->portalGenerateToken();
    $hash = $this->app->DB->real_escape_string($this->portalHashToken($token));
    $this->app->DB->Insert(
      "INSERT INTO ticket_portal_access (ticket_id, token_hash, scope, created_at, expires_at)
       VALUES (".(int)$ticketId.", '$hash', 'session', NOW(), '".$this->app->DB->real_escape_string($expiresAt)."')"
    );
    return ['token' => $token, 'expires_at' => $expiresAt];
  }

  private function portalCreateMagicAccess(int $ticketId): array
  {
    $ttl = $this->portalGetSettingInt('ticketportal_magic_ttl_min', 30);
    $expiresAt = date('Y-m-d H:i:s', time() + ($ttl * 60));
    $token = $this->portalGenerateToken(24);
    $hash = $this->app->DB->real_escape_string($this->portalHashToken($token));
    $this->app->DB->Update(
      "UPDATE ticket_portal_access
       SET revoked_at = NOW()
       WHERE ticket_id = ".(int)$ticketId." AND scope = 'magic' AND revoked_at IS NULL"
    );
    $this->app->DB->Insert(
      "INSERT INTO ticket_portal_access (ticket_id, token_hash, scope, created_at, expires_at)
       VALUES (".(int)$ticketId.", '$hash', 'magic', NOW(), '".$this->app->DB->real_escape_string($expiresAt)."')"
    );
    return ['token' => $token, 'expires_at' => $expiresAt];
  }

  private function portalSendVerificationCodeMail(array $ticket, string $email, string $code): void
  {
    $subject = 'Ticket Portal Zugriffscode';
    $body = "Ihr Zugriffscode fuer Ticket #".$ticket['schluessel'].": ".$code;
    $this->app->erp->MailSend(
      $this->app->erp->GetFirmaMail(),
      $this->app->erp->GetFirmaName(),
      $email,
      $ticket['customer_name'] ?? '',
      $subject,
      $body
    );
  }

  private function portalSendOfferDoiMail(array $ticket, string $email, string $doiToken): void
  {
    $link = rtrim($this->portalGetServerUrl(), '/').'/index.php?module=ticket&action=portal_offer_confirm&doi_token='.$doiToken;
    $subject = 'Angebotsbestaetigung bestaetigen';
    $body = "Bitte bestaetigen Sie Ihre Entscheidung:\n".$link;
    $agbUrl = (string)$this->app->erp->Firmendaten('ticketportal_agb_url');
    if ($agbUrl !== '') {
      $body .= "\nAGB: ".$agbUrl;
    }
    $this->app->erp->MailSend(
      $this->app->erp->GetFirmaMail(),
      $this->app->erp->GetFirmaName(),
      $email,
      $ticket['customer_name'] ?? '',
      $subject,
      $body
    );
  }

  private function portalGetServerUrl(): string
  {
    $serverUrl = (string)$this->app->erp->Firmendaten('server_url');
    if ($serverUrl === '') {
      $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
      $host = (string)($_SERVER['HTTP_HOST'] ?? '');
      $serverUrl = $scheme.'://'.$host;
    }
    return rtrim($serverUrl, '/');
  }

  private function portalMirrorMessageToTicket(array $ticket, string $text, string $authorType): int
  {
    $ticketNumber = $this->app->DB->real_escape_string($ticket['schluessel']);
    $verfasser = $authorType === 'customer' ? ($ticket['customer_name'] ?? '') : 'Portal';
    $verfasser = $this->app->DB->real_escape_string($verfasser);
    $mail = $this->portalNormalizeEmail($ticket['mailadresse'] ?? '');
    if ($mail === '') {
      $mail = $this->portalNormalizeEmail($ticket['customer_email'] ?? '');
    }
    $mailEsc = $this->app->DB->real_escape_string($mail);
    $textEsc = $this->app->DB->real_escape_string($text);
    $statusEsc = $this->app->DB->real_escape_string($ticket['status'] ?? '');
    $this->app->DB->Insert("INSERT INTO ticket_nachricht (
      ticket, verfasser, bearbeiter, mail, zeit, text, textausgang, betreff, bemerkung,
      medium, versendet, status, mail_cc, verfasser_replyto, mail_replyto
    ) VALUES (
      '$ticketNumber', '$verfasser', '', '$mailEsc', NOW(), '$textEsc', '', 'Portal Nachricht', '',
      'portal', '0', '$statusEsc', '', '', ''
    )");
    return (int)$this->app->DB->GetInsertID();
  }

  private function portalGetNotificationPreferences(int $ticketId, int $customerId): array
  {
    $ticketId = (int)$ticketId;
    $customerId = (int)$customerId;
    if ($ticketId <= 0 || $customerId <= 0) {
      return [];
    }
    $rows = $this->app->DB->SelectArr(
      "SELECT status_key, enabled
       FROM ticket_notification_pref
       WHERE ticket_id = $ticketId AND customer_id = $customerId"
    );
    $prefs = [];
    if (!empty($rows)) {
      foreach ($rows as $row) {
        if (!isset($row['status_key'])) {
          continue;
        }
        $prefs[(string)$row['status_key']] = (int)$row['enabled'] === 1;
      }
    }
    return $prefs;
  }

  private function portalShouldNotifyStatus(int $ticketId, int $customerId, string $statusKey): bool
  {
    $prefs = $this->portalGetNotificationPreferences($ticketId, $customerId);
    if (empty($prefs)) {
      return $this->portalGetSettingBool('ticketportal_notify_all_status', true);
    }
    if (array_key_exists($statusKey, $prefs)) {
      return (bool)$prefs[$statusKey];
    }
    return false;
  }

  private function portalRenderTemplate(string $template, array $replacements): string
  {
    foreach ($replacements as $key => $value) {
      $template = str_replace($key, $value, $template);
    }
    return $template;
  }

  private function portalSendStatusNotification(array $ticket, string $statusKey, string $statusLabel, ?string $publicNote = null): void
  {
    $email = $this->portalNormalizeEmail($ticket['mailadresse'] ?? '');
    if ($email === '') {
      $email = $this->portalNormalizeEmail($ticket['customer_email'] ?? '');
    }
    if ($email === '') {
      return;
    }
    $subjectTemplate = (string)$this->app->erp->Firmendaten('ticketportal_notify_subject');
    $bodyTemplate = (string)$this->app->erp->Firmendaten('ticketportal_notify_body');
    if ($subjectTemplate === '') {
      $subjectTemplate = 'Ticket #{ticket_number} Statusaenderung';
    }
    if ($bodyTemplate === '') {
      $bodyTemplate = "Der Status Ihres Tickets #{ticket_number} wurde aktualisiert.\nStatus: {status_label}\n\n{public_note}\n\nViele Gruesse\n{company_name}";
    }
    $replacements = [
      '{ticket_number}' => (string)$ticket['schluessel'],
      '{ticket_id}' => (string)$ticket['id'],
      '{status_key}' => $statusKey,
      '{status_label}' => $statusLabel,
      '{customer_name}' => (string)($ticket['customer_name'] ?? ''),
      '{public_note}' => $publicNote !== null ? $publicNote : '',
      '{company_name}' => (string)$this->app->erp->GetFirmaName(),
    ];
    $subject = $this->portalRenderTemplate($subjectTemplate, $replacements);
    $body = $this->portalRenderTemplate($bodyTemplate, $replacements);
    $this->app->erp->MailSend(
      $this->app->erp->GetFirmaMail(),
      $this->app->erp->GetFirmaName(),
      $email,
      $ticket['customer_name'] ?? '',
      $subject,
      $body
    );
  }

  private function portalMaybeNotifyCustomer(int $ticketId, string $statusKey, string $statusLabel, ?string $publicNote = null): void
  {
    if (!$this->portalGetSettingBool('ticketportal_enabled')) {
      return;
    }
    $ticket = $this->portalGetTicketForPortal($ticketId);
    if (!$ticket) {
      return;
    }
    $customerId = (int)($ticket['adresse'] ?? 0);
    if ($customerId <= 0) {
      return;
    }
    if (!$this->portalShouldNotifyStatus($ticketId, $customerId, $statusKey)) {
      return;
    }
    $this->portalSendStatusNotification($ticket, $statusKey, $statusLabel, $publicNote);
  }

  private function portalInsertPortalMessage(array $ticket, string $authorType, int $authorId, string $text, bool $isPublic): array
  {
    $textEsc = $this->app->DB->real_escape_string($text);
    $authorTypeEsc = $this->app->DB->real_escape_string($authorType);
    $isPublicValue = $isPublic ? 1 : 0;
    $this->app->DB->Insert(
      "INSERT INTO ticket_portal_message (ticket_id, author_type, author_id, text, is_public, created_at)
       VALUES (".(int)$ticket['id'].", '$authorTypeEsc', ".(int)$authorId.", '$textEsc', $isPublicValue, NOW())"
    );
    $portalMessageId = (int)$this->app->DB->GetInsertID();
    $mirroredId = $this->portalMirrorMessageToTicket($ticket, $text, $authorType);
    $this->app->DB->Update(
      "UPDATE ticket_portal_message SET mirrored_message_id = ".(int)$mirroredId."
       WHERE id = $portalMessageId LIMIT 1"
    );
    return ['portal_message_id' => $portalMessageId, 'mirrored_message_id' => $mirroredId];
  }

  private function portalSetCustomerStatus(int $ticketId, string $statusKey, string $statusLabel, ?int $userId = null, ?string $publicNote = null): bool
  {
    $ticketId = (int)$ticketId;
    $statusKeyEsc = $this->app->DB->real_escape_string($statusKey);
    $statusLabelEsc = $this->app->DB->real_escape_string($statusLabel);
    $updatedBy = $userId !== null ? (int)$userId : 'NULL';
    $existing = $this->app->DB->SelectRow(
      "SELECT status_key, status_label FROM ticket_customer_status WHERE ticket_id = $ticketId LIMIT 1"
    );
    $hasRecord = !empty($existing);
    $changed = !$hasRecord || $existing['status_key'] !== $statusKey;
    $labelChanged = !$hasRecord || $existing['status_label'] !== $statusLabel;
    if (!$changed && !$labelChanged) {
      return false;
    }
    if ($hasRecord) {
      $this->app->DB->Update(
        "UPDATE ticket_customer_status
         SET status_key = '$statusKeyEsc', status_label = '$statusLabelEsc', updated_at = NOW(), updated_by = $updatedBy
         WHERE ticket_id = $ticketId LIMIT 1"
      );
    } else {
      $this->app->DB->Insert(
        "INSERT INTO ticket_customer_status (ticket_id, status_key, status_label, updated_at, updated_by)
         VALUES ($ticketId, '$statusKeyEsc', '$statusLabelEsc', NOW(), $updatedBy)"
      );
    }
    if ($changed) {
      $this->portalMaybeNotifyCustomer($ticketId, $statusKey, $statusLabel, $publicNote);
    }
    return $changed;
  }

  private function portalLogStatus(int $ticketId, ?string $from, string $to, ?int $userId = null, ?string $publicNote = null, ?string $internalNote = null): void
  {
    $fromEsc = $from !== null ? "'".$this->app->DB->real_escape_string($from)."'" : "NULL";
    $toEsc = $this->app->DB->real_escape_string($to);
    $userIdSql = $userId !== null ? (int)$userId : 'NULL';
    $publicEsc = $publicNote !== null ? "'".$this->app->DB->real_escape_string($publicNote)."'" : "NULL";
    $internalEsc = $internalNote !== null ? "'".$this->app->DB->real_escape_string($internalNote)."'" : "NULL";
    $this->app->DB->Insert(
      "INSERT INTO ticket_status_log (ticket_id, status_from, status_to, changed_by, changed_at, note_public, note_internal)
       VALUES (".(int)$ticketId.", $fromEsc, '$toEsc', $userIdSql, NOW(), $publicEsc, $internalEsc)"
    );
  }

  private function portalDefaultStatusLabels(): array
  {
    return [
      'paket_eingegangen' => 'Paket eingegangen',
      'versandschaden_klaeren' => 'Versandschaden zu klaeren',
      'warte_ersatzteile' => 'Warte auf Ersatzteile',
      'in_bearbeitung' => 'In Bearbeitung',
      'qualitaetspruefung' => 'Qualitaetspruefung',
      'rueckfrage' => 'Rueckfrage',
      'warten_auf_rueckmeldung' => 'Warten auf Ihre Rueckmeldung',
      'angebot_erstellt' => 'Angebot erstellt',
      'angebot_bestaetigt' => 'Angebot bestaetigt',
      'angebot_abgelehnt' => 'Angebot abgelehnt',
      'versandbereit' => 'Versandbereit',
      'abgeschlossen' => 'Abgeschlossen',
    ];
  }

  private function portalDefaultStatusMap(): array
  {
    return [
      'neu' => 'paket_eingegangen',
      'offen' => 'paket_eingegangen',
      'warten_e' => 'in_bearbeitung',
      'klaeren' => 'rueckfrage',
      'warten_kd' => 'warten_auf_rueckmeldung',
      'abgeschlossen' => 'abgeschlossen',
    ];
  }

  private function portalGetStatusLabels(): array
  {
    $labels = $this->portalDefaultStatusLabels();
    $rawLabels = (string)$this->app->erp->Firmendaten('ticketportal_status_labels');
    $decodedLabels = json_decode($rawLabels, true);
    if (is_array($decodedLabels)) {
      foreach ($labels as $k => $v) {
        if (isset($decodedLabels[$k]) && trim((string)$decodedLabels[$k]) !== '') {
          $labels[$k] = trim((string)$decodedLabels[$k]);
        }
      }
    }
    $rawCustom = (string)$this->app->erp->Firmendaten('ticketportal_custom_statuses');
    $custom = json_decode($rawCustom, true);
    if (is_array($custom)) {
      foreach ($custom as $item) {
        if (!empty($item['key']) && !empty($item['label']) && !empty($item['enabled'])) {
          $labels[(string)$item['key']] = (string)$item['label'];
        }
      }
    }
    return $labels;
  }

  private function portalGetStatusMap(): array
  {
    $default = $this->portalDefaultStatusMap();
    $labels = $this->portalGetStatusLabels();
    $raw = (string)$this->app->erp->Firmendaten('ticketportal_status_map');
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
      $decoded = [];
    }
    $map = [];
    foreach ($default as $key => $fallback) {
      $value = $decoded[$key] ?? $fallback;
      if (!isset($labels[$value])) {
        $value = $fallback;
      }
      $map[$key] = $value;
    }
    return $map;
  }

  private function portalStatusMap(): array
  {
    $labels = $this->portalGetStatusLabels();
    $map = $this->portalGetStatusMap();
    $result = [];
    foreach ($map as $internal => $statusKey) {
      $result[$internal] = $labels[$statusKey] ?? $statusKey;
    }
    return $result;
  }

  private function portalDefaultCustomerStatusKey(string $ticketStatus, int $projectId = 0): string
  {
    if ($projectId > 0) {
      $rawProjectMap = (string)$this->app->erp->Firmendaten('ticketportal_status_map_projects');
      $projectMap = json_decode($rawProjectMap, true);
      if (is_array($projectMap) && isset($projectMap[$projectId][$ticketStatus])) {
        return (string)$projectMap[$projectId][$ticketStatus];
      }
    }
    $map = $this->portalGetStatusMap();
    return $map[$ticketStatus] ?? 'in_bearbeitung';
  }

  private function portalCustomerStatusOptions(): array
  {
    return $this->portalGetStatusLabels();
  }

  private function portalHandleInternalStatusChange(int $ticketId, string $oldStatus, string $newStatus, ?int $userId = null): void
  {
    if ($oldStatus === '' || $newStatus === '' || $oldStatus === $newStatus || $newStatus === 'spam') {
      return;
    }
    $ticket = $this->app->DB->SelectRow("SELECT projekt FROM ticket WHERE id = ".(int)$ticketId." LIMIT 1");
    $projectId = (int)($ticket['projekt'] ?? 0);

    $oldKey = $this->portalDefaultCustomerStatusKey($oldStatus, $projectId);
    $newKey = $this->portalDefaultCustomerStatusKey($newStatus, $projectId);
    if ($oldKey === $newKey) {
      return;
    }
    $labels = $this->portalGetStatusLabels();
    $existing = $this->app->DB->SelectRow(
      "SELECT status_key FROM ticket_customer_status WHERE ticket_id = ".(int)$ticketId." LIMIT 1"
    );
    if (!empty($existing) && (string)$existing['status_key'] !== $oldKey) {
      return;
    }
    $label = $labels[$newKey] ?? $newKey;
    $changed = $this->portalSetCustomerStatus($ticketId, $newKey, $label, $userId, null);
    if ($changed) {
      $this->portalLogStatus($ticketId, $oldKey, $newKey, $userId, null, null);
    }
  }

  public function ticket_portal_session()
  {
    if (!$this->portalGetSettingBool('ticketportal_enabled')) {
      $this->portalJsonResponse(['error' => 'portal_disabled'], 403);
    }
    $this->portalRequireSharedSecret();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->portalJsonResponse(['error' => 'method_not_allowed'], 405);
    }
    $data = $this->portalReadJsonInput();
    $token = trim((string)($data['token'] ?? ''));
    $ticketNumber = trim((string)($data['ticket_number'] ?? $data['ticket'] ?? ''));
    $verifierType = trim((string)($data['verifier_type'] ?? ''));
    $verifierValue = trim((string)($data['verifier_value'] ?? ''));
    if ($token === '' && $ticketNumber === '') {
      $this->portalJsonResponse(['error' => 'invalid_request'], 400);
    }
    $access = null;
    $ticket = null;
    if ($token !== '') {
      $access = $this->portalGetAccessByToken($token, 'customer');
      if (!$access) {
        $this->portalJsonResponse(['error' => 'token_not_found'], 404);
      }
      $ticket = $this->portalGetTicketForPortal((int)$access['ticket_id']);
      if (!$ticket) {
        $this->portalJsonResponse(['error' => 'ticket_not_found'], 404);
      }
    } else {
      $ticket = $this->portalGetTicketByNumber($ticketNumber);
      if (!$ticket) {
        $this->portalJsonResponse(['error' => 'ticket_not_found'], 404);
      }
      $access = $this->portalGetOrCreateLookupAccess((int)$ticket['id'], (string)$ticket['schluessel']);
      if (empty($access)) {
        $this->portalJsonResponse(['error' => 'access_failed'], 500);
      }
    }
    if ($this->portalIsAccessLocked($access)) {
      $this->portalJsonResponse([
        'error' => 'access_locked',
        'locked_until' => $access['locked_until'] ?? null,
      ], 429);
    }

    $email = $this->portalNormalizeEmail($ticket['mailadresse'] ?? '');
    if ($email === '') {
      $email = $this->portalNormalizeEmail($ticket['customer_email'] ?? '');
    }
    $plz = $this->portalNormalizePlz($ticket['customer_plz'] ?? '');

    if ($verifierType === '' || $verifierType === 'auto') {
      $verifierType = $email !== '' ? 'code' : 'plz';
    }

    switch ($verifierType) {
      case 'email':
        if ($email === '') {
          $this->portalJsonResponse(['error' => 'email_required'], 400);
        }
        if ($email === '' || $this->portalNormalizeEmail($verifierValue) !== $email) {
          $result = $this->portalRegisterAccessFailure((int)$access['id'], (int)($access['failed_attempts'] ?? 0));
          if (!empty($result['locked_until'])) {
            $this->portalJsonResponse(['error' => 'access_locked', 'locked_until' => $result['locked_until']], 429);
          }
          $this->portalJsonResponse(['error' => 'verification_failed'], 401);
        }
        break;
      case 'plz':
        if ($plz === '') {
          $this->portalJsonResponse(['error' => 'plz_required'], 400);
        }
        if ($plz === '' || $this->portalNormalizePlz($verifierValue) !== $plz) {
          $result = $this->portalRegisterAccessFailure((int)$access['id'], (int)($access['failed_attempts'] ?? 0));
          if (!empty($result['locked_until'])) {
            $this->portalJsonResponse(['error' => 'access_locked', 'locked_until' => $result['locked_until']], 429);
          }
          $this->portalJsonResponse(['error' => 'verification_failed'], 401);
        }
        break;
      case 'code':
        if ($email === '') {
          $this->portalJsonResponse(['error' => 'email_required'], 400);
        }
        if ($verifierValue === '') {
          $code = $this->portalGenerateCode();
          $codeHash = $this->app->DB->real_escape_string($this->portalHashToken($code));
          $ttl = $this->portalGetSettingInt('ticketportal_code_ttl_min', 15);
          $expiresAt = date('Y-m-d H:i:s', time() + ($ttl * 60));
          $expiresEsc = $this->app->DB->real_escape_string($expiresAt);
          $this->app->DB->Update(
            "UPDATE ticket_portal_access
             SET verifier_type = 'code', verifier_hash = '$codeHash', verifier_expires_at = '$expiresEsc'
             WHERE id = ".(int)$access['id']." LIMIT 1"
          );
          $this->portalSendVerificationCodeMail($ticket, $email, $code);
          $this->portalJsonResponse(['status' => 'verification_sent']);
        }
        if (empty($access['verifier_hash']) || empty($access['verifier_expires_at'])) {
          $result = $this->portalRegisterAccessFailure((int)$access['id'], (int)($access['failed_attempts'] ?? 0));
          if (!empty($result['locked_until'])) {
            $this->portalJsonResponse(['error' => 'access_locked', 'locked_until' => $result['locked_until']], 429);
          }
          $this->portalJsonResponse(['error' => 'verification_failed'], 401);
        }
        if (strtotime($access['verifier_expires_at']) < time()) {
          $result = $this->portalRegisterAccessFailure((int)$access['id'], (int)($access['failed_attempts'] ?? 0));
          if (!empty($result['locked_until'])) {
            $this->portalJsonResponse(['error' => 'access_locked', 'locked_until' => $result['locked_until']], 429);
          }
          $this->portalJsonResponse(['error' => 'verification_expired'], 401);
        }
        $expectedHash = (string)$access['verifier_hash'];
        $providedHash = $this->portalHashToken($verifierValue);
        if (!hash_equals($expectedHash, $providedHash)) {
          $result = $this->portalRegisterAccessFailure((int)$access['id'], (int)($access['failed_attempts'] ?? 0));
          if (!empty($result['locked_until'])) {
            $this->portalJsonResponse(['error' => 'access_locked', 'locked_until' => $result['locked_until']], 429);
          }
          $this->portalJsonResponse(['error' => 'verification_failed'], 401);
        }
        $this->app->DB->Update(
          "UPDATE ticket_portal_access
           SET verifier_hash = NULL, verifier_expires_at = NULL
           WHERE id = ".(int)$access['id']." LIMIT 1"
        );
        break;
      default:
        $this->portalJsonResponse(['error' => 'invalid_verifier'], 400);
    }

    $this->portalResetAccessFailures((int)$access['id']);
    $session = $this->portalCreateSession((int)$ticket['id']);
    $this->portalTouchAccess((int)$access['id']);
    $this->portalJsonResponse(['session_token' => $session['token'], 'expires_at' => $session['expires_at']]);
  }

  public function ticket_portal_token()
  {
    if (!$this->app->erp->RechteVorhanden('ticket', 'edit')) {
      $this->portalJsonResponse(['error' => 'forbidden'], 403);
    }
    $data = $this->portalReadJsonInput();
    $ticketId = (int)($data['ticket_id'] ?? $this->app->Secure->GetGET('id'));
    if ($ticketId <= 0) {
      $this->portalJsonResponse(['error' => 'invalid_request'], 400);
    }
    $token = $this->portalGenerateToken();
    $hash = $this->app->DB->real_escape_string($this->portalHashToken($token));
    $this->app->DB->Update(
      "UPDATE ticket_portal_access
       SET revoked_at = NOW()
       WHERE ticket_id = $ticketId AND scope = 'customer' AND revoked_at IS NULL"
    );
    $this->app->DB->Insert(
      "INSERT INTO ticket_portal_access (ticket_id, token_hash, scope, created_at)
       VALUES ($ticketId, '$hash', 'customer', NOW())"
    );
    $this->portalJsonResponse(['token' => $token]);
  }

  public function ticket_portal_magic()
  {
    if (!$this->portalGetSettingBool('ticketportal_enabled')) {
      $this->portalJsonResponse(['error' => 'portal_disabled'], 403);
    }
    $this->portalRequireSharedSecret();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->portalJsonResponse(['error' => 'method_not_allowed'], 405);
    }
    $data = $this->portalReadJsonInput();
    $magicToken = trim((string)($data['magic_token'] ?? ''));
    if ($magicToken === '') {
      $this->portalJsonResponse(['error' => 'invalid_request'], 400);
    }
    $access = $this->portalGetAccessByToken($magicToken, 'magic');
    if (!$access) {
      $this->portalJsonResponse(['error' => 'token_not_found'], 404);
    }
    $ticket = $this->portalGetTicketForPortal((int)$access['ticket_id']);
    if (!$ticket) {
      $this->portalJsonResponse(['error' => 'ticket_not_found'], 404);
    }
    $this->portalTouchAccess((int)$access['id']);
    $this->app->DB->Update(
      "UPDATE ticket_portal_access SET revoked_at = NOW()
       WHERE id = ".(int)$access['id']." LIMIT 1"
    );
    $session = $this->portalCreateSession((int)$ticket['id']);
    $this->portalJsonResponse(['session_token' => $session['token'], 'expires_at' => $session['expires_at']]);
  }

  public function ticket_portal_magic_token()
  {
    if (!$this->app->erp->RechteVorhanden('ticket', 'edit')) {
      $this->portalJsonResponse(['error' => 'forbidden'], 403);
    }
    if (!$this->portalGetSettingBool('ticketportal_enabled')) {
      $this->portalJsonResponse(['error' => 'portal_disabled'], 403);
    }
    $data = $this->portalReadJsonInput();
    $ticketId = (int)($data['ticket_id'] ?? $this->app->Secure->GetGET('id'));
    if ($ticketId <= 0) {
      $this->portalJsonResponse(['error' => 'invalid_request'], 400);
    }
    $magic = $this->portalCreateMagicAccess($ticketId);
    $this->portalJsonResponse(['token' => $magic['token'], 'expires_at' => $magic['expires_at']]);
  }

  public function ticket_portal_status()
  {
    if (!$this->portalGetSettingBool('ticketportal_enabled')) {
      $this->portalJsonResponse(['error' => 'portal_disabled'], 403);
    }
    $this->portalRequireSharedSecret();
    $data = $this->portalReadJsonInput();
    $access = $this->portalGetSessionAccess($data);
    if (!$access) {
      $this->portalJsonResponse(['error' => 'session_invalid'], 401);
    }
    $ticket = $this->portalGetTicketForPortal((int)$access['ticket_id']);
    if (!$ticket) {
      $this->portalJsonResponse(['error' => 'ticket_not_found'], 404);
    }
    $customerStatus = $this->app->DB->SelectRow(
      "SELECT status_key, status_label, updated_at
       FROM ticket_customer_status
       WHERE ticket_id = ".(int)$ticket['id']." LIMIT 1"
    );
    $labels = $this->portalGetStatusLabels();
    if (!empty($customerStatus)) {
      $statusKey = $customerStatus['status_key'];
      $statusLabel = $labels[$statusKey] ?? $customerStatus['status_label'];
      $updatedAt = $customerStatus['updated_at'];
    } else {
      $map = $this->portalGetStatusMap();
      $statusKey = $map[$ticket['status']] ?? $this->portalDefaultCustomerStatusKey((string)$ticket['status']);
      $statusLabel = $labels[$statusKey] ?? $statusKey;
      $updatedAt = null;
    }
    $this->portalJsonResponse([
      'ticket_number' => $ticket['schluessel'],
      'status_key' => $statusKey,
      'status_label' => $statusLabel,
      'updated_at' => $updatedAt,
    ]);
  }

  public function ticket_portal_messages()
  {
    if (!$this->portalGetSettingBool('ticketportal_enabled')) {
      $this->portalJsonResponse(['error' => 'portal_disabled'], 403);
    }
    $this->portalRequireSharedSecret();
    $data = $this->portalReadJsonInput();
    $access = $this->portalGetSessionAccess($data);
    if (!$access) {
      $this->portalJsonResponse(['error' => 'session_invalid'], 401);
    }
    $messages = $this->app->DB->SelectArr(
      "SELECT m.id, m.author_type, m.text, m.created_at, 
              REPLACE(COALESCE(NULLIF(m.source, ''), n.medium, 'portal'), 'telefon', 'phone') as source
       FROM ticket_portal_message m
       LEFT JOIN ticket_nachricht n ON n.id = m.mirrored_message_id
       WHERE m.ticket_id = ".(int)$access['ticket_id']." AND m.is_public = 1
       ORDER BY m.created_at ASC"
    );
    $this->portalJsonResponse(['messages' => $messages ?? []]);
  }

  public function ticket_portal_message()
  {
    if (!$this->portalGetSettingBool('ticketportal_enabled')) {
      $this->portalJsonResponse(['error' => 'portal_disabled'], 403);
    }
    $this->portalRequireSharedSecret();
    if (!$this->portalGetSettingBool('ticketportal_allow_customer_comments')) {
      $this->portalJsonResponse(['error' => 'comments_disabled'], 403);
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->portalJsonResponse(['error' => 'method_not_allowed'], 405);
    }
    $data = $this->portalReadJsonInput();
    $access = $this->portalGetSessionAccess($data);
    if (!$access) {
      $this->portalJsonResponse(['error' => 'session_invalid'], 401);
    }
    $text = trim((string)($data['text'] ?? ''));
    if ($text === '') {
      $this->portalJsonResponse(['error' => 'empty_message'], 400);
    }
    $ticket = $this->portalGetTicketForPortal((int)$access['ticket_id']);
    if (!$ticket) {
      $this->portalJsonResponse(['error' => 'ticket_not_found'], 404);
    }
    $authorId = (int)($ticket['adresse'] ?? 0);
    $result = $this->portalInsertPortalMessage($ticket, 'customer', $authorId, $text, true);
    $this->portalJsonResponse([
      'id' => $result['portal_message_id'],
      'mirrored_message_id' => $result['mirrored_message_id'],
    ]);
  }

  public function ticket_portal_notifications()
  {
    if (!$this->portalGetSettingBool('ticketportal_enabled')) {
      $this->portalJsonResponse(['error' => 'portal_disabled'], 403);
    }
    $this->portalRequireSharedSecret();
    $data = $this->portalReadJsonInput();
    $access = $this->portalGetSessionAccess($data);
    if (!$access) {
      $this->portalJsonResponse(['error' => 'session_invalid'], 401);
    }
    $ticket = $this->portalGetTicketForPortal((int)$access['ticket_id']);
    if (!$ticket) {
      $this->portalJsonResponse(['error' => 'ticket_not_found'], 404);
    }
    $customerId = (int)($ticket['adresse'] ?? 0);
    $prefs = $this->portalGetNotificationPreferences((int)$ticket['id'], $customerId);
    $defaultAll = $this->portalGetSettingBool('ticketportal_notify_all_status', true);
    $statuses = [];
    foreach ($this->portalCustomerStatusOptions() as $key => $label) {
      $enabled = array_key_exists($key, $prefs) ? (bool)$prefs[$key] : $defaultAll;
      $statuses[] = [
        'key' => $key,
        'label' => $label,
        'enabled' => $enabled,
      ];
    }
    $this->portalJsonResponse([
      'default_all' => $defaultAll,
      'statuses' => $statuses,
    ]);
  }

  public function ticket_portal_notification()
  {
    if (!$this->portalGetSettingBool('ticketportal_enabled')) {
      $this->portalJsonResponse(['error' => 'portal_disabled'], 403);
    }
    $this->portalRequireSharedSecret();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->portalJsonResponse(['error' => 'method_not_allowed'], 405);
    }
    $data = $this->portalReadJsonInput();
    $access = $this->portalGetSessionAccess($data);
    if (!$access) {
      $this->portalJsonResponse(['error' => 'session_invalid'], 401);
    }
    $ticket = $this->portalGetTicketForPortal((int)$access['ticket_id']);
    if (!$ticket) {
      $this->portalJsonResponse(['error' => 'ticket_not_found'], 404);
    }
    $statusOptions = $this->portalCustomerStatusOptions();
    $selected = [];
    if (!empty($data['selected']) && !is_array($data['selected'])) {
      $decoded = json_decode((string)$data['selected'], true);
      if (is_array($decoded)) {
        $data['selected'] = $decoded;
      }
    }
    if (!empty($data['selected']) && is_array($data['selected'])) {
      foreach ($data['selected'] as $key) {
        $key = trim((string)$key);
        if ($key !== '') {
          $selected[$key] = true;
        }
      }
    } elseif (!empty($data['statuses']) && is_array($data['statuses'])) {
      foreach ($data['statuses'] as $key => $value) {
        $selected[$key] = (bool)$value;
      }
    } else {
      $this->portalJsonResponse(['error' => 'invalid_request'], 400);
    }

    $ticketId = (int)$ticket['id'];
    $customerId = (int)($ticket['adresse'] ?? 0);
    $this->app->DB->Update(
      "DELETE FROM ticket_notification_pref WHERE ticket_id = $ticketId AND customer_id = $customerId"
    );

    foreach ($statusOptions as $key => $label) {
      $enabled = !empty($selected[$key]) ? 1 : 0;
      $keyEsc = $this->app->DB->real_escape_string($key);
      $this->app->DB->Insert(
        "INSERT INTO ticket_notification_pref (ticket_id, customer_id, status_key, enabled)
         VALUES ($ticketId, $customerId, '$keyEsc', $enabled)"
      );
    }

    $this->portalJsonResponse(['saved' => true]);
  }

  public function ticket_portal_offers()
  {
    if (!$this->portalGetSettingBool('ticketportal_enabled')) {
      $this->portalJsonResponse(['error' => 'portal_disabled'], 403);
    }
    $this->portalRequireSharedSecret();
    if (!$this->portalGetSettingBool('ticketportal_allow_offer_confirm')) {
      $this->portalJsonResponse(['error' => 'offer_disabled'], 403);
    }
    $data = $this->portalReadJsonInput();
    $access = $this->portalGetSessionAccess($data);
    if (!$access) {
      $this->portalJsonResponse(['error' => 'session_invalid'], 401);
    }
    $ticket = $this->portalGetTicketForPortal((int)$access['ticket_id']);
    if (!$ticket) {
      $this->portalJsonResponse(['error' => 'ticket_not_found'], 404);
    }
    $ticketKey = $this->app->DB->real_escape_string((string)($ticket['schluessel'] ?? ''));
    if ($ticketKey === '') {
      $this->portalJsonResponse(['offers' => []]);
    }
    $offers = $this->app->DB->SelectArr(
      "SELECT id, belegnr, datum, gesamtsumme, waehrung, status
       FROM angebot
       WHERE anfrage = '$ticketKey'
         AND status IN ('freigegeben', 'beauftragt')
       ORDER BY datum DESC, id DESC"
    );
    $this->portalJsonResponse(['offers' => $offers ?? []]);
  }

  public function ticket_portal_offer()
  {
    if (!$this->portalGetSettingBool('ticketportal_enabled')) {
      $this->portalJsonResponse(['error' => 'portal_disabled'], 403);
    }
    $this->portalRequireSharedSecret();
    if (!$this->portalGetSettingBool('ticketportal_allow_offer_confirm')) {
      $this->portalJsonResponse(['error' => 'offer_disabled'], 403);
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->portalJsonResponse(['error' => 'method_not_allowed'], 405);
    }
    $data = $this->portalReadJsonInput();
    $access = $this->portalGetSessionAccess($data);
    if (!$access) {
      $this->portalJsonResponse(['error' => 'session_invalid'], 401);
    }
    $angebotId = (int)($data['angebot_id'] ?? 0);
    $action = trim((string)($data['action'] ?? ''));
    $comment = trim((string)($data['comment'] ?? ''));
    $agbVersion = trim((string)($data['agb_version'] ?? ''));
    if ($angebotId <= 0 || !in_array($action, ['accept', 'decline'], true)) {
      $this->portalJsonResponse(['error' => 'invalid_request'], 400);
    }
    if ($action === 'accept' && $agbVersion === '') {
      $agbVersion = (string)$this->app->erp->Firmendaten('ticketportal_agb_version');
      if ($agbVersion === '') {
        $this->portalJsonResponse(['error' => 'agb_required'], 400);
      }
    }
    $ticket = $this->portalGetTicketForPortal((int)$access['ticket_id']);
    if (!$ticket) {
      $this->portalJsonResponse(['error' => 'ticket_not_found'], 404);
    }
    $offer = $this->app->DB->SelectRow("SELECT id, adresse, status FROM angebot WHERE id = $angebotId LIMIT 1");
    if (empty($offer)) {
      $this->portalJsonResponse(['error' => 'offer_not_found'], 404);
    }
    if ((int)$offer['adresse'] !== (int)$ticket['adresse']) {
      $this->portalJsonResponse(['error' => 'offer_mismatch'], 403);
    }
    $commentEsc = $this->app->DB->real_escape_string($comment);
    $agbVersionEsc = $this->app->DB->real_escape_string($agbVersion);
    $doiToken = '';
    $doiHashSql = "NULL";
    $doiRequestedSql = "NULL";
    if ($action === 'accept') {
      $doiToken = $this->portalGenerateToken(24);
      $doiHashSql = "'".$this->app->DB->real_escape_string($this->portalHashToken($doiToken))."'";
      $doiRequestedSql = "NOW()";
    }
    $ip = $this->app->DB->real_escape_string($this->portalGetRequestIp());
    $ua = $this->app->DB->real_escape_string($this->portalGetRequestUa());
    $this->app->DB->Insert(
      "INSERT INTO ticket_offer_confirmation (
        ticket_id, angebot_id, action, comment, agb_version, doi_token_hash, doi_requested_at,
        created_at, created_by_type, created_by_id, ip, user_agent
      ) VALUES (
        ".(int)$ticket['id'].", $angebotId, '".$this->app->DB->real_escape_string($action)."', '$commentEsc',
        '$agbVersionEsc', $doiHashSql, $doiRequestedSql, NOW(), 'customer', ".(int)$ticket['adresse'].", '$ip', '$ua'
      )"
    );
    if ($action === 'accept') {
      $email = $this->portalNormalizeEmail($ticket['mailadresse'] ?? '');
      if ($email === '') {
        $email = $this->portalNormalizeEmail($ticket['customer_email'] ?? '');
      }
      if ($email === '') {
        $this->portalJsonResponse(['error' => 'email_required'], 400);
      }
      $this->portalSendOfferDoiMail($ticket, $email, $doiToken);
      $this->portalJsonResponse(['status' => 'pending_doi', 'doi_sent' => true]);
    }
    $this->portalSetCustomerStatus((int)$ticket['id'], 'angebot_abgelehnt', 'Angebot abgelehnt', null);
    $this->portalLogStatus((int)$ticket['id'], null, 'angebot_abgelehnt', null, 'Angebot abgelehnt', null);
    $this->portalJsonResponse(['status' => 'decline_recorded', 'doi_sent' => false]);
  }

  public function ticket_portal_media()
  {
    if (!$this->portalGetSettingBool('ticketportal_enabled')) {
      $this->portalJsonResponse(['error' => 'portal_disabled'], 403);
    }
    $this->portalRequireSharedSecret();
    $data = $this->portalReadJsonInput();
    $access = $this->portalGetSessionAccess($data);
    if (!$access) {
      $this->portalJsonResponse(['error' => 'session_invalid'], 401);
    }
    $media = $this->app->DB->SelectArr(
      "SELECT id, filename, mime_type, file_size, created_at
       FROM ticket_repair_media
       WHERE ticket_id = ".(int)$access['ticket_id']."
         AND is_public = 1
       ORDER BY created_at DESC"
    );
    $this->portalJsonResponse(['media' => $media ?? []]);
  }

  public function ticket_portal_media_download()
  {
    if (!$this->portalGetSettingBool('ticketportal_enabled')) {
      $this->portalJsonResponse(['error' => 'portal_disabled'], 403);
    }
    $this->portalRequireSharedSecret();
    $data = $this->portalReadJsonInput();
    $access = $this->portalGetSessionAccess($data);
    $mediaId = (int)($data['media_id'] ?? 0);
    if (!$access) {
      $this->portalJsonResponse(['error' => 'session_invalid'], 401);
    }
    $row = $this->app->DB->SelectRow(
      "SELECT * FROM ticket_repair_media WHERE id = $mediaId AND ticket_id = ".(int)$access['ticket_id']." AND is_public = 1 LIMIT 1"
    );
    if (empty($row)) {
      $this->portalJsonResponse(['error' => 'not_found'], 404);
    }
    $uploadDir = dirname(__DIR__, 1).DIRECTORY_SEPARATOR.'userfiles'.DIRECTORY_SEPARATOR.'ticket_media';
    $extension = pathinfo($row['filename'], PATHINFO_EXTENSION);
    $path = $uploadDir.DIRECTORY_SEPARATOR.$row['file_hash'].'.'.$extension;
    if (!is_file($path)) {
      $this->portalJsonResponse(['error' => 'file_missing'], 404);
    }
    header('Content-Type: '.$row['mime_type']);
    header('Content-Length: '.$row['file_size']);
    header('Content-Disposition: attachment; filename=\"'.addslashes($row['filename']).'\"');
    readfile($path);
    $this->app->ExitXentral();
  }

  public function ticket_portal_offer_confirm()
  {
    if (!$this->portalGetSettingBool('ticketportal_enabled')) {
      $this->portalJsonResponse(['error' => 'portal_disabled'], 403);
    }
    if (!$this->portalGetSettingBool('ticketportal_allow_offer_confirm')) {
      $this->portalJsonResponse(['error' => 'offer_disabled'], 403);
    }
    $data = $this->portalReadJsonInput();
    $doiToken = trim((string)($data['doi_token'] ?? $this->app->Secure->GetGET('doi_token')));
    if ($doiToken === '') {
      $this->portalJsonResponse(['error' => 'invalid_request'], 400);
    }
    $doiHash = $this->app->DB->real_escape_string($this->portalHashToken($doiToken));
    $record = $this->app->DB->SelectRow(
      "SELECT * FROM ticket_offer_confirmation
       WHERE doi_token_hash = '$doiHash' AND doi_confirmed_at IS NULL
       ORDER BY id DESC LIMIT 1"
    );
    if (empty($record)) {
      $this->portalJsonResponse(['error' => 'doi_invalid'], 404);
    }
    $ttl = $this->portalGetSettingInt('ticketportal_doi_ttl_min', 120);
    if (!empty($record['doi_requested_at']) && strtotime($record['doi_requested_at']) < (time() - ($ttl * 60))) {
      $this->portalJsonResponse(['error' => 'doi_expired'], 401);
    }
    $this->app->DB->Update(
      "UPDATE ticket_offer_confirmation
       SET doi_confirmed_at = NOW(), agb_accepted_at = IF(agb_accepted_at IS NULL, NOW(), agb_accepted_at)
       WHERE id = ".(int)$record['id']." LIMIT 1"
    );

    $ticket = $this->portalGetTicketForPortal((int)$record['ticket_id']);
    if (!$ticket) {
      $this->portalJsonResponse(['error' => 'ticket_not_found'], 404);
    }

    if ($record['action'] === 'accept') {
      $offer = $this->app->DB->SelectRow("SELECT id, auftragid, status FROM angebot WHERE id = ".(int)$record['angebot_id']." LIMIT 1");
      if (!empty($offer['auftragid']) || $offer['status'] === 'beauftragt') {
        $this->portalJsonResponse(['error' => 'offer_already_ordered'], 409);
      }
      $orderId = $this->app->erp->WeiterfuehrenAngebotZuAuftrag((int)$record['angebot_id']);
      if (empty($orderId)) {
        $this->portalJsonResponse(['error' => 'order_failed'], 500);
      }
      $this->app->DB->Update(
        "UPDATE ticket_offer_confirmation SET order_id = ".(int)$orderId."
         WHERE id = ".(int)$record['id']." LIMIT 1"
      );
      $this->portalSetCustomerStatus((int)$ticket['id'], 'angebot_bestaetigt', 'Angebot bestaetigt', null);
      $this->portalLogStatus((int)$ticket['id'], null, 'angebot_bestaetigt', null, 'Angebot bestaetigt', null);
      
      $this->portalInsertPortalMessage($ticket, 'system', 0, 'Angebot #'.($offer['belegnr'] ?? $record['angebot_id']).' wurde bestaetigt. Auftrag #'.$orderId.' wurde erstellt.', true);
      
      $this->portalJsonResponse(['status' => 'confirmed', 'order_id' => (int)$orderId]);
    }

    $this->portalSetCustomerStatus((int)$ticket['id'], 'angebot_abgelehnt', 'Angebot abgelehnt', null);
    $this->portalLogStatus((int)$ticket['id'], null, 'angebot_abgelehnt', null, 'Angebot abgelehnt', null);
    
    $this->portalInsertPortalMessage($ticket, 'system', 0, 'Angebot #'.$record['angebot_id'].' wurde abgelehnt.', true);
    
    $this->portalJsonResponse(['status' => 'declined']);
  }

  public function ticket_portal_settings()
  {
    if (!$this->app->erp->RechteVorhanden('firmendaten', 'edit')) {
      $this->app->Tpl->Set('PAGE', '<div class="error">Keine Berechtigung.</div>');
      return;
    }
    $this->portalEnsureFirmendatenDefaults();
    if ($this->app->Secure->GetPOST('clear_log')) {
      $logPath = $this->portalGetLogPath();
      if (is_file($logPath)) {
        @unlink($logPath);
      }
      $msg = $this->app->erp->base64_url_encode('<div class="info">Portal Log geleert.</div>');
      $this->app->Location->execute('index.php?module=ticket&action=portal_settings&msg='.$msg);
      return;
    }
    if ($this->app->Secure->GetPOST('save')) {
      $this->app->erp->FirmendatenSet('ticketportal_enabled', !empty($this->app->Secure->GetPOST('ticketportal_enabled')) ? 1 : 0);
      $this->app->erp->FirmendatenSet('ticketportal_portal_url', (string)$this->app->Secure->GetPOST('ticketportal_portal_url'));
      $this->app->erp->FirmendatenSet('ticketportal_allow_offer_confirm', !empty($this->app->Secure->GetPOST('ticketportal_allow_offer_confirm')) ? 1 : 0);
      $this->app->erp->FirmendatenSet('ticketportal_allow_customer_comments', !empty($this->app->Secure->GetPOST('ticketportal_allow_customer_comments')) ? 1 : 0);
      $this->app->erp->FirmendatenSet('ticketportal_notify_all_status', !empty($this->app->Secure->GetPOST('ticketportal_notify_all_status')) ? 1 : 0);
      $this->app->erp->FirmendatenSet('ticketportal_agb_url', (string)$this->app->Secure->GetPOST('ticketportal_agb_url'));
      $this->app->erp->FirmendatenSet('ticketportal_agb_version', (string)$this->app->Secure->GetPOST('ticketportal_agb_version'));
      $this->app->erp->FirmendatenSet('ticketportal_shared_secret', (string)$this->app->Secure->GetPOST('ticketportal_shared_secret'));
      $this->app->erp->FirmendatenSet('ticketportal_log_enabled', !empty($this->app->Secure->GetPOST('ticketportal_log_enabled')) ? 1 : 0);
      $this->app->erp->FirmendatenSet('ticketportal_notify_subject', (string)$this->app->Secure->GetPOST('ticketportal_notify_subject'));
      $this->app->erp->FirmendatenSet('ticketportal_notify_body', (string)$this->app->Secure->GetPOST('ticketportal_notify_body'));
      $sessionTtl = max(1, (int)$this->app->Secure->GetPOST('ticketportal_session_ttl_min'));
      $codeTtl = max(1, (int)$this->app->Secure->GetPOST('ticketportal_code_ttl_min'));
      $magicTtl = max(1, (int)$this->app->Secure->GetPOST('ticketportal_magic_ttl_min'));
      $doiTtl = max(1, (int)$this->app->Secure->GetPOST('ticketportal_doi_ttl_min'));
      $maxAttempts = max(1, (int)$this->app->Secure->GetPOST('ticketportal_max_attempts'));
      $lockoutMin = max(1, (int)$this->app->Secure->GetPOST('ticketportal_lockout_min'));
      $this->app->erp->FirmendatenSet('ticketportal_session_ttl_min', $sessionTtl);
      $this->app->erp->FirmendatenSet('ticketportal_code_ttl_min', $codeTtl);
      $this->app->erp->FirmendatenSet('ticketportal_magic_ttl_min', $magicTtl);
      $this->app->erp->FirmendatenSet('ticketportal_doi_ttl_min', $doiTtl);
      $this->app->erp->FirmendatenSet('ticketportal_max_attempts', $maxAttempts);
      $this->app->erp->FirmendatenSet('ticketportal_lockout_min', $lockoutMin);
      $labelsInput = $_POST['status_label'] ?? [];
      $mapInput = $_POST['status_map'] ?? [];
      $defaultLabels = $this->portalDefaultStatusLabels();
      $labels = [];
      foreach ($defaultLabels as $key => $fallback) {
        $value = $fallback;
        if (is_array($labelsInput) && array_key_exists($key, $labelsInput)) {
          $candidate = trim((string)$labelsInput[$key]);
          if ($candidate !== '') {
            $value = $candidate;
          }
        }
        $labels[$key] = $value;
      }
      $defaultMap = $this->portalDefaultStatusMap();
      $map = [];
      foreach ($defaultMap as $internal => $fallback) {
        $selected = $fallback;
        if (is_array($mapInput) && array_key_exists($internal, $mapInput)) {
          $candidate = trim((string)$mapInput[$internal]);
          if (isset($labels[$candidate])) {
            $selected = $candidate;
          }
        }
        $map[$internal] = $selected;
      }
      $this->app->erp->FirmendatenSet('ticketportal_status_labels', json_encode($labels));
      $this->app->erp->FirmendatenSet('ticketportal_status_map', json_encode($map));

      $customInput = $_POST['custom_status'] ?? [];
      $customStatuses = [];
      if (is_array($customInput)) {
        foreach ($customInput as $c) {
          if (!empty($c['key']) && !empty($c['label'])) {
            $customStatuses[] = [
              'key' => trim((string)$c['key']),
              'label' => trim((string)$c['label']),
              'enabled' => !empty($c['enabled']) ? 1 : 0
            ];
          }
        }
      }
      $this->app->erp->FirmendatenSet('ticketportal_custom_statuses', json_encode($customStatuses));
      $this->app->erp->FirmendatenSet('ticketportal_status_map_projects', (string)$this->app->Secure->GetPOST('ticketportal_status_map_projects'));

      $msg = $this->app->erp->base64_url_encode('<div class="info">Portal Einstellungen gespeichert.</div>');
      $this->app->Location->execute('index.php?module=ticket&action=portal_settings&msg='.$msg);
    }

    $this->app->Tpl->Set('PORTAL_ENABLED', $this->portalGetSettingBool('ticketportal_enabled') ? 'checked' : '');
    $this->app->Tpl->Set('PORTAL_URL', $this->app->erp->Firmendaten('ticketportal_portal_url'));
    $this->app->Tpl->Set('PORTAL_ALLOW_OFFER', $this->portalGetSettingBool('ticketportal_allow_offer_confirm') ? 'checked' : '');
    $this->app->Tpl->Set('PORTAL_ALLOW_COMMENTS', $this->portalGetSettingBool('ticketportal_allow_customer_comments') ? 'checked' : '');
    $this->app->Tpl->Set('PORTAL_NOTIFY_ALL', $this->portalGetSettingBool('ticketportal_notify_all_status', true) ? 'checked' : '');
    $this->app->Tpl->Set('PORTAL_AGB_URL', $this->app->erp->Firmendaten('ticketportal_agb_url'));
    $this->app->Tpl->Set('PORTAL_AGB_VERSION', $this->app->erp->Firmendaten('ticketportal_agb_version'));
    $this->app->Tpl->Set('PORTAL_SHARED_SECRET', $this->app->erp->Firmendaten('ticketportal_shared_secret'));
    $this->app->Tpl->Set('PORTAL_LOG_ENABLED', $this->portalLogEnabled() ? 'checked' : '');
    $this->app->Tpl->Set('PORTAL_LOG_PATH', htmlentities($this->portalGetLogPath()));
    $this->app->Tpl->Set('PORTAL_LOG_CONTENT', htmlentities($this->portalReadLogTail()));
    $notifySubject = trim((string)$this->app->erp->Firmendaten('ticketportal_notify_subject'));
    if ($notifySubject === '') {
      $notifySubject = $this->portalDefaultNotifySubject();
    }
    $notifyBody = trim((string)$this->app->erp->Firmendaten('ticketportal_notify_body'));
    if ($notifyBody === '') {
      $notifyBody = $this->portalDefaultNotifyBody();
    }
    $this->app->Tpl->Set('PORTAL_NOTIFY_SUBJECT', $notifySubject);
    $this->app->Tpl->Set('PORTAL_NOTIFY_BODY', $notifyBody);
    $this->app->Tpl->Set('PORTAL_SESSION_TTL', $this->portalGetSettingInt('ticketportal_session_ttl_min', 60));
    $this->app->Tpl->Set('PORTAL_CODE_TTL', $this->portalGetSettingInt('ticketportal_code_ttl_min', 15));
    $this->app->Tpl->Set('PORTAL_MAGIC_TTL', $this->portalGetSettingInt('ticketportal_magic_ttl_min', 30));
    $this->app->Tpl->Set('PORTAL_DOI_TTL', $this->portalGetSettingInt('ticketportal_doi_ttl_min', 120));
    $this->app->Tpl->Set('PORTAL_MAX_ATTEMPTS', $this->portalGetSettingInt('ticketportal_max_attempts', 5));
    $this->app->Tpl->Set('PORTAL_LOCKOUT_MIN', $this->portalGetSettingInt('ticketportal_lockout_min', 15));
    $statusOptions = $this->portalCustomerStatusOptions();
    $labels = $this->portalGetStatusLabels();
    $labelRows = '';
    foreach ($statusOptions as $key => $label) {
      $labelRows .= '<tr><td>'.htmlentities($key).'</td><td><input type="text" name="status_label['.
        htmlentities($key).']" value="'.htmlentities($labels[$key] ?? $label).'" size="40"></td></tr>';
    }
    $this->app->Tpl->Set('STATUS_LABEL_ROWS', $labelRows);

    $internalKeys = array_keys($this->portalDefaultStatusMap());
    $internalLabels = $this->app->erp->GetTicketStatusValues();
    $map = $this->portalGetStatusMap();
    $mapRows = '';
    foreach ($internalKeys as $internal) {
      $internalLabel = $internalLabels[$internal] ?? $internal;
      $optionsHtml = '';
      foreach ($statusOptions as $key => $label) {
        $selected = ($map[$internal] ?? '') === $key ? ' selected' : '';
        $optionsHtml .= '<option value="'.htmlentities($key).'"'.$selected.'>'.htmlentities($label).'</option>';
      }
      $mapRows .= '<tr><td>'.htmlentities($internalLabel).'</td><td><select name="status_map['.
        htmlentities($internal).']">'.$optionsHtml.'</select></td></tr>';
    }
    $this->app->Tpl->Set('STATUS_MAP_ROWS', $mapRows);

    $customRaw = (string)$this->app->erp->Firmendaten('ticketportal_custom_statuses');
    $customList = json_decode($customRaw, true) ?: [];
    $customRows = '';
    $idx = 0;
    foreach ($customList as $c) {
      $enabledChecked = !empty($c['enabled']) ? 'checked' : '';
      $customRows .= '<tr><td><input type="text" name="custom_status['.$idx.'][key]" value="'.htmlentities($c['key']).'" placeholder="key"></td>';
      $customRows .= '<td><input type="text" name="custom_status['.$idx.'][label]" value="'.htmlentities($c['label']).'" placeholder="Label" size="40"></td>';
      $customRows .= '<td><input type="checkbox" name="custom_status['.$idx.'][enabled]" value="1" '.$enabledChecked.'></td>';
      $customRows .= '<td><button type="button" onclick=\"this.closest(\'tr\').remove();\">Entfernen</button></td></tr>';
      $idx++;
    }
    // Add one empty row for new entry
    $customRows .= '<tr><td><input type="text" name="custom_status['.$idx.'][key]" value="" placeholder="neuer_key"></td>';
    $customRows .= '<td><input type="text" name="custom_status['.$idx.'][label]" value="" placeholder="Neues Label" size="40"></td>';
    $customRows .= '<td><input type="checkbox" name="custom_status['.$idx.'][enabled]" value="1" checked></td><td></td></tr>';
    $this->app->Tpl->Set('CUSTOM_STATUS_ROWS', $customRows);

    $projectMap = (string)$this->app->erp->Firmendaten('ticketportal_status_map_projects');
    $this->app->Tpl->Set('STATUS_MAP_PROJECTS', htmlentities($projectMap));
    $pluginUrl = $this->portalGetServerUrl().'/index.php?module=ticket&action=portal_plugin_download';
    $this->app->Tpl->Set('PORTAL_PLUGIN_URL', $pluginUrl);
    $msg = $this->app->Secure->GetGET('msg');
    if ($msg) {
      $this->app->Tpl->Set('MESSAGE', $this->app->erp->base64_url_decode($msg));
    } else {
      $this->app->Tpl->Set('MESSAGE', '');
    }
    $this->app->Tpl->Parse('PAGE', 'ticket_portal_settings.tpl');
  }

  public function ticket_portal_plugin_download()
  {
    if (!$this->app->erp->RechteVorhanden('firmendaten', 'edit')) {
      $secret = trim((string)$this->app->erp->Firmendaten('ticketportal_shared_secret'));
      $header = (string)($_SERVER['HTTP_X_OPENXE_PORTAL_SECRET'] ?? '');
      if ($secret === '' || $header === '' || !hash_equals($secret, $header)) {
        http_response_code(403);
        $this->app->Tpl->Set('TEXT', '<div class="error">Keine Berechtigung.</div>');
        $this->app->Tpl->Output('ticket_text.tpl');
        $this->app->ExitXentral();
      }
    }
    $pluginDir = dirname(__DIR__, 2).'/wp-plugin/openxe-ticket-portal';
    if (!is_dir($pluginDir)) {
      http_response_code(404);
      $this->app->Tpl->Set('TEXT', '<div class="error">Plugin nicht gefunden.</div>');
      $this->app->Tpl->Output('ticket_text.tpl');
      $this->app->ExitXentral();
    }
    if (!class_exists('ZipArchive')) {
      http_response_code(500);
      $this->app->Tpl->Set('TEXT', '<div class="error">Zip-Erweiterung nicht verfuegbar.</div>');
      $this->app->Tpl->Output('ticket_text.tpl');
      $this->app->ExitXentral();
    }

    $tmpBase = tempnam(sys_get_temp_dir(), 'openxe-portal-');
    if ($tmpBase === false) {
      http_response_code(500);
      $this->app->Tpl->Set('TEXT', '<div class="error">Temporaere Datei konnte nicht erstellt werden.</div>');
      $this->app->Tpl->Output('ticket_text.tpl');
      $this->app->ExitXentral();
    }
    $zipPath = $tmpBase.'.zip';
    @unlink($tmpBase);

    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
      http_response_code(500);
      $this->app->Tpl->Set('TEXT', '<div class="error">Zip konnte nicht erstellt werden.</div>');
      $this->app->Tpl->Output('ticket_text.tpl');
      $this->app->ExitXentral();
    }

    $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($pluginDir, FilesystemIterator::SKIP_DOTS),
      RecursiveIteratorIterator::SELF_FIRST
    );
    $pluginDirLength = strlen($pluginDir) + 1;
    foreach ($iterator as $file) {
      if (!$file->isFile()) {
        continue;
      }
      $path = $file->getPathname();
      $relative = substr($path, $pluginDirLength);
      $zip->addFile($path, $relative);
    }
    $zip->close();

    $filename = 'openxe-ticket-portal.zip';
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Content-Length: '.filesize($zipPath));
    readfile($zipPath);
    @unlink($zipPath);
    $this->app->ExitXentral();
  }

  public function ticket_portal_staff()
  {
    if (!$this->app->erp->RechteVorhanden('ticket', 'edit')) {
      $this->app->Tpl->Set('PAGE', '<div class="error">Keine Berechtigung.</div>');
      return;
    }
    $ticketId = (int)$this->app->Secure->GetGET('id');
    if ($ticketId <= 0) {
      $this->app->Tpl->Set('PAGE', '<div class="error">Ticket nicht gefunden.</div>');
      return;
    }
    $ticket = $this->portalGetTicketForPortal($ticketId);
    if (!$ticket) {
      $this->app->Tpl->Set('PAGE', '<div class="error">Ticket nicht gefunden.</div>');
      return;
    }

    $statusOptions = $this->portalCustomerStatusOptions();
    $currentStatus = $this->app->DB->SelectRow(
      "SELECT status_key, status_label FROM ticket_customer_status WHERE ticket_id = $ticketId LIMIT 1"
    );
    $currentKey = $currentStatus['status_key'] ?? $this->portalDefaultCustomerStatusKey((string)$ticket['status']);
    if (!isset($statusOptions[$currentKey]) && !empty($currentStatus['status_label'])) {
      $statusOptions = [$currentKey => $currentStatus['status_label']] + $statusOptions;
    } elseif (!isset($statusOptions[$currentKey])) {
      $currentKey = 'in_bearbeitung';
    }

    if ($this->app->Secure->GetPOST('save')) {
      $statusKey = trim((string)$this->app->Secure->GetPOST('status_key'));
      $publicNote = trim((string)$this->app->Secure->GetPOST('public_note'));
      $internalNote = trim((string)$this->app->Secure->GetPOST('internal_note'));
      if ($statusKey === '' || !isset($statusOptions[$statusKey])) {
        $msg = $this->app->erp->base64_url_encode('<div class="error">Bitte gueltigen Status waehlen.</div>');
        $this->app->Location->execute('index.php?module=ticket&action=portal_staff&id='.$ticketId.'&msg='.$msg);
        return;
      }
      $statusLabel = $statusOptions[$statusKey];
      $userId = (int)$this->app->User->GetID();
      $changed = $this->portalSetCustomerStatus(
        $ticketId,
        $statusKey,
        $statusLabel,
        $userId,
        $publicNote !== '' ? $publicNote : null
      );
      if ($changed) {
        $this->portalLogStatus(
          $ticketId,
          $currentKey,
          $statusKey,
          $userId,
          $publicNote !== '' ? $publicNote : null,
          $internalNote !== '' ? $internalNote : null
        );
      }
      if ($publicNote !== '') {
        $this->portalInsertPortalMessage($ticket, 'staff', $userId, $publicNote, true);
      }
      if ($internalNote !== '') {
        $this->portalInsertPortalMessage($ticket, 'staff', $userId, $internalNote, false);
      }
      if (!$changed && $publicNote === '' && $internalNote === '') {
        $messageText = 'Keine Aenderung gespeichert.';
      } elseif ($changed) {
        $messageText = 'Status gespeichert.';
      } else {
        $messageText = 'Kommentar gespeichert.';
      }
      $msg = $this->app->erp->base64_url_encode('<div class="info">'.$messageText.'</div>');
      $this->app->Location->execute('index.php?module=ticket&action=portal_staff&id='.$ticketId.'&msg='.$msg);
      return;
    }

    $optionsHtml = '';
    foreach ($statusOptions as $key => $label) {
      $selected = $key === $currentKey ? ' selected' : '';
      $optionsHtml .= '<option value="'.htmlentities($key).'"'.$selected.'>'.htmlentities($label).'</option>';
    }

    $mediaFiles = $this->app->DB->SelectArr(
      "SELECT id, filename, mime_type, file_size, created_at, is_public FROM ticket_repair_media WHERE ticket_id = $ticketId ORDER BY created_at DESC"
    );
    $mediaHtml = '';
    if (!empty($mediaFiles)) {
      $mediaHtml .= '<table class="list" style="width:100%;"><tr><th>Datei</th><th>Groesse</th><th>Datum</th><th>Sichtbar</th><th>Menue</th></tr>';
      foreach ($mediaFiles as $row) {
        $size = round($row['file_size'] / 1024, 1).' KB';
        $publicChecked = $row['is_public'] ? 'checked' : '';
        $mediaHtml .= '<tr><td><a href="index.php?module=ticket&action=portal_staff_download&id='.$row['id'].'" target="_blank">'.htmlentities($row['filename']).'</a></td>';
        $mediaHtml .= '<td>'.$size.'</td><td>'.htmlentities($row['created_at']).'</td>';
        $mediaHtml .= '<td><input type="checkbox" disabled '.$publicChecked.'></td>';
        $mediaHtml .= '<td><a href="index.php?module=ticket&action=portal_staff_media_delete&id='.$row['id'].'&ticket_id='.$ticketId.'" onclick=\"return confirm(\'Datei wirklich loeschen?\');\">Loeschen</a></td></tr>';
      }
      $mediaHtml .= '</table>';
    } else {
      $mediaHtml = '<p>Keine Medien hochgeladen.</p>';
    }

    $msg = $this->app->Secure->GetGET('msg');
    if ($msg) {
      $this->app->Tpl->Set('MESSAGE', $this->app->erp->base64_url_decode($msg));
    } else {
      $this->app->Tpl->Set('MESSAGE', '');
    }
    $this->app->Tpl->Set('ID', $ticketId);
    $this->app->Tpl->Set('TICKETNUMMER', htmlentities((string)$ticket['schluessel']));
    $this->app->Tpl->Set('BETREFF', htmlentities((string)($ticket['betreff'] ?? '')));
    $this->app->Tpl->Set('STATUS_OPTIONS', $optionsHtml);
    $this->app->Tpl->Set('MEDIA_LIST', $mediaHtml);
    $this->app->Tpl->Parse('PAGE', 'ticket_portal_staff.tpl');
  }

  public function ticket_portal_staff_upload()
  {
    if (!$this->app->erp->RechteVorhanden('ticket', 'edit')) {
      $this->portalJsonResponse(['error' => 'forbidden'], 403);
    }
    $ticketId = (int)$this->app->Secure->GetGET('id');
    if ($ticketId <= 0 || empty($_FILES['file'])) {
      $this->app->Location->execute('index.php?module=ticket&action=portal_staff&id='.$ticketId);
      return;
    }
    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
      $msg = $this->app->erp->base64_url_encode('<div class="error">Upload fehlgeschlagen (Fehler '.$file['error'].').</div>');
      $this->app->Location->execute('index.php?module=ticket&action=portal_staff&id='.$ticketId.'&msg='.$msg);
      return;
    }
    $maxSize = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $maxSize) {
      $msg = $this->app->erp->base64_url_encode('<div class="error">Datei zu gross (max. 10MB).</div>');
      $this->app->Location->execute('index.php?module=ticket&action=portal_staff&id='.$ticketId.'&msg='.$msg);
      return;
    }
    $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, $allowedMime, true)) {
      $msg = $this->app->erp->base64_url_encode('<div class="error">Dateityp nicht erlaubt (nur JPG, PNG, WebP, PDF).</div>');
      $this->app->Location->execute('index.php?module=ticket&action=portal_staff&id='.$ticketId.'&msg='.$msg);
      return;
    }

    $uploadDir = dirname(__DIR__, 1).DIRECTORY_SEPARATOR.'userfiles'.DIRECTORY_SEPARATOR.'ticket_media';
    if (!is_dir($uploadDir)) {
      @mkdir($uploadDir, 0775, true);
    }
    $fileHash = hash_file('sha256', $file['tmp_name']);
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $targetName = $fileHash.'.'.$extension;
    $targetPath = $uploadDir.DIRECTORY_SEPARATOR.$targetName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
      $fileNameEsc = $this->app->DB->real_escape_string($file['name']);
      $mimeTypeEsc = $this->app->DB->real_escape_string($mimeType);
      $isPublic = (int)$this->app->Secure->GetPOST('is_public');
      $userId = (int)$this->app->User->GetID();
      $this->app->DB->Insert(
        "INSERT INTO ticket_repair_media (ticket_id, filename, mime_type, file_size, file_hash, created_at, created_by, is_public)
         VALUES ($ticketId, '$fileNameEsc', '$mimeTypeEsc', ".(int)$file['size'].", '$fileHash', NOW(), $userId, $isPublic)"
      );
      $msg = $this->app->erp->base64_url_encode('<div class="info">Datei hochgeladen.</div>');
    } else {
      $msg = $this->app->erp->base64_url_encode('<div class="error">Datei konnte nicht gespeichert werden.</div>');
    }
    $this->app->Location->execute('index.php?module=ticket&action=portal_staff&id='.$ticketId.'&msg='.$msg);
  }

  public function ticket_portal_staff_download()
  {
    if (!$this->app->erp->RechteVorhanden('ticket', 'edit')) {
      $this->app->ExitXentral();
    }
    $id = (int)$this->app->Secure->GetGET('id');
    $row = $this->app->DB->SelectRow("SELECT * FROM ticket_repair_media WHERE id = $id LIMIT 1");
    if (empty($row)) {
      $this->app->ExitXentral();
    }
    $uploadDir = dirname(__DIR__, 1).DIRECTORY_SEPARATOR.'userfiles'.DIRECTORY_SEPARATOR.'ticket_media';
    $extension = pathinfo($row['filename'], PATHINFO_EXTENSION);
    $path = $uploadDir.DIRECTORY_SEPARATOR.$row['file_hash'].'.'.$extension;

    if (!is_file($path)) {
      echo "Datei nicht gefunden.";
      $this->app->ExitXentral();
    }

    header('Content-Type: '.$row['mime_type']);
    header('Content-Length: '.$row['file_size']);
    header('Content-Disposition: inline; filename=\"'.addslashes($row['filename']).'\"');
    readfile($path);
    $this->app->ExitXentral();
  }

  public function ticket_portal_staff_media_delete()
  {
    if (!$this->app->erp->RechteVorhanden('ticket', 'edit')) {
      $this->app->ExitXentral();
    }
    $id = (int)$this->app->Secure->GetGET('id');
    $ticketId = (int)$this->app->Secure->GetGET('ticket_id');
    if ($id > 0) {
      $this->app->DB->Update("DELETE FROM ticket_repair_media WHERE id = $id LIMIT 1");
    }
    $msg = $this->app->erp->base64_url_encode('<div class="info">Datei geloescht.</div>');
    $this->app->Location->execute('index.php?module=ticket&action=portal_staff&id='.$ticketId.'&msg='.$msg);
  }

  public function ticket_portal_print()
  {
    if (!$this->portalGetSettingBool('ticketportal_enabled')) {
      http_response_code(403);
      $this->app->Tpl->Set('TEXT', '<div class="error">Portal deaktiviert.</div>');
      $this->app->Tpl->Output('ticket_text.tpl');
      $this->app->ExitXentral();
    }
    $data = $this->portalReadJsonInput();
    $sessionToken = $this->portalReadSessionToken($data);
    $access = $this->portalGetSessionAccess($data);
    if (!$access) {
      http_response_code(401);
      $this->app->Tpl->Set('TEXT', '<div class="error">Sitzung ung&uuml;ltig.</div>');
      $this->app->Tpl->Output('ticket_text.tpl');
      $this->app->ExitXentral();
    }
    $ticket = $this->portalGetTicketForPortal((int)$access['ticket_id']);
    if (!$ticket) {
      http_response_code(404);
      $this->app->Tpl->Set('TEXT', '<div class="error">Ticket nicht gefunden.</div>');
      $this->app->Tpl->Output('ticket_text.tpl');
      $this->app->ExitXentral();
    }

    $adresse = $this->app->DB->SelectRow(
      "SELECT name, abteilung, unterabteilung, strasse, adresszusatz, plz, ort, land, email
       FROM adresse WHERE id = ".(int)$ticket['adresse']." LIMIT 1"
    );

    $addressLines = [];
    foreach (['name','abteilung','unterabteilung','strasse','adresszusatz'] as $field) {
      if (!empty($adresse[$field])) {
        $addressLines[] = $adresse[$field];
      }
    }
    $cityLine = trim(($adresse['land'] ?? '').'-'.($adresse['plz'] ?? '').' '.($adresse['ort'] ?? ''));
    if ($cityLine !== '-') {
      $addressLines[] = $cityLine;
    }

    $email = $this->portalNormalizeEmail($adresse['email'] ?? '');
    if ($email === '') {
      $email = $this->portalNormalizeEmail($ticket['mailadresse'] ?? '');
    }

    // Message History
    $messages = $this->app->DB->SelectArr(
      "SELECT author_type, text, created_at, source
       FROM ticket_portal_message
       WHERE ticket_id = ".(int)$ticket['id']." AND is_public = 1
       ORDER BY created_at ASC"
    );
    $messagesHtml = '';
    if (!empty($messages)) {
      foreach ($messages as $msg) {
        $author = ($msg['author_type'] === 'customer') ? 'Kunde' : (($msg['author_type'] === 'system') ? 'System' : 'Team');
        $date = date('d.m.Y H:i', strtotime($msg['created_at']));
        $messagesHtml .= '<div class="message ' . $msg['author_type'] . '">';
        $messagesHtml .= '<div class="meta"><strong>' . $author . '</strong> (' . $date . ')</div>';
        $messagesHtml .= '<div class="text">' . nl2br(htmlentities($msg['text'])) . '</div>';
        $messagesHtml .= '</div>';
      }
    }

    // Offers
    $offers = $this->app->DB->SelectArr(
      "SELECT belegnr, datum, gesamtsumme, waehrung, status
       FROM angebot
       WHERE anfrage = '".$this->app->DB->real_escape_string($ticket['schluessel'])."'
         AND status IN ('freigegeben', 'beauftragt')
       ORDER BY datum DESC"
    );
    $offersHtml = '';
    if (!empty($offers)) {
      $offersHtml .= '<table><tr><th>Belegnr</th><th>Datum</th><th>Summe</th><th>Status</th></tr>';
      foreach ($offers as $offer) {
        $offersHtml .= '<tr>';
        $offersHtml .= '<td>' . htmlentities($offer['belegnr']) . '</td>';
        $offersHtml .= '<td>' . date('d.m.Y', strtotime($offer['datum'])) . '</td>';
        $offersHtml .= '<td>' . number_format($offer['gesamtsumme'], 2, ',', '.') . ' ' . $offer['waehrung'] . '</td>';
        $offersHtml .= '<td>' . htmlentities($offer['status']) . '</td>';
        $offersHtml .= '</tr>';
      }
      $offersHtml .= '</table>';
    }

    $ticketStaffUrl = $this->portalGetServerUrl().'/index.php?module=ticket&action=portal_staff&id='.(int)$ticket['id'];
    /** @var \Xentral\Components\Barcode\BarcodeFactory $barcodeFactory */
    $barcodeFactory = $this->app->Container->get('BarcodeFactory');
    $qrHtml = $barcodeFactory->createQrCode($ticketStaffUrl, 'M')->toHtml(2, 2);

    $this->app->Tpl->Set('TICKETNUMMER', htmlentities((string)$ticket['schluessel']));
    $this->app->Tpl->Set('BETREFF', htmlentities((string)$ticket['betreff']));
    $this->app->Tpl->Set('FEHLERBESCHREIBUNG', nl2br(htmlentities((string)$errorDescription)));
    $this->app->Tpl->Set('KUNDENADRESSE', nl2br(htmlentities(implode("\n", $addressLines))));
    $this->app->Tpl->Set('EMAIL', htmlentities((string)$email));
    $this->app->Tpl->Set('QR_HTML', $qrHtml);
    $this->app->Tpl->Set('MESSAGES_HTML', $messagesHtml);
    $this->app->Tpl->Set('OFFERS_HTML', $offersHtml);
    $this->app->Tpl->Set('STAFF_URL', htmlentities($ticketStaffUrl));

    $downloadUrl = '';
    if ($sessionToken !== '') {
      $downloadUrl = $this->portalGetServerUrl().'/index.php?module=ticket&action=portal_print&session_token='.
        rawurlencode($sessionToken).'&download=1';
    }
    $this->app->Tpl->Set('DOWNLOAD_URL', $downloadUrl);

    if ($downloadUrl === '') {
      $this->app->Tpl->Set('DOWNLOAD_URL', '#');
    }

    if ($this->app->Secure->GetGET('download')) {
      $filename = 'ticket-'.$ticket['schluessel'].'.html';
      header('Content-Type: text/html; charset=utf-8');
      header('Content-Disposition: attachment; filename="'.$filename.'"');
    }
    $this->app->Tpl->Output('ticket_portal_print.tpl');
    $this->app->ExitXentral();
  }
}



