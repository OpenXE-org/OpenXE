<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;
use Xentral\Modules\SystemNotification\Service\NotificationMessageData;
use Xentral\Modules\SystemNotification\Service\NotificationService;

class Seriennummern {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "seriennummern_artikel_list");
        $this->app->ActionHandler("nummern_list", "seriennummern_nummern_list");
        $this->app->ActionHandler("lieferscheine_list", "seriennummern_lieferscheine_list");                                
        $this->app->ActionHandler("enter", "seriennummern_enter"); 
        $this->app->ActionHandler("delete", "seriennummern_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "seriennummern_list":
                $allowed['seriennummern_list'] = array('list');
                $heading = array('','','Nummer','Artikel', 'Seriennummer','Erfasst am','Eingelagert','Adresse','Lieferschein','Lieferdatum', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('s.id','s.id', 'a.nummer', 'a.name_de', 's.seriennummer','s.datum','s.eingelagert','ad.name','l.belegnr','l.datum','s.id');
                $searchsql = array('a.nummer', 'a.name_de', 's.seriennummer');

                $defaultorder = 1;
                $defaultorderdesc = 0;
                $aligncenter = array();
                $alignright = array();
                $numbercols = array();
                $sumcol = array();

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',s.id,'\" />') AS `auswahl`";

//                $moreinfo = true; // Allow drop down details
//                $moreinfoaction = "lieferschein"; // specify suffix for minidetail-URL to allow different minidetails
//                $menucol = 11; // Set id col for moredata/menu

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=seriennummern&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "
                    SELECT SQL_CALC_FOUND_ROWS 
                            s.id,
                            $dropnbox,
                            CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',a.id,'\">',a.nummer,'</a>') as nummer,
                            a.name_de,
                            s.seriennummer,
                            ".$app->erp->FormatDate("s.datum").",
                            if(s.eingelagert,'Ja','Nein'),
                            ad.name,
                            l.belegnr,
                            ".$app->erp->FormatDate("l.datum").",
                            s.id 
                        FROM 
                            seriennummern s 
                        INNER JOIN 
                            artikel a ON s.artikel = a.id
                        LEFT JOIN 
                            lieferschein_position lp ON lp.id = s.lieferscheinpos
                        LEFT JOIN 
                            lieferschein l ON l.id = lp.lieferschein
                        LEFT JOIN
                            adresse ad ON ad.id = l.adresse
                 ";

                $artikel_id = $app->User->GetParameter('seriennummern_artikel_id');

                $where = "(a.id = '".$artikel_id."' OR '".$artikel_id."' = '')";

                // Toggle filters
                $app->Tpl->Add('JQUERYREADY', "$('#verfuegbar').click( function() { fnFilterColumn1( 0 ); } );");
                $app->Tpl->Add('JQUERYREADY', "$('#versendet').click( function() { fnFilterColumn2( 0 ); } );");

                for ($r = 1;$r <= 2;$r++) {
                  $app->Tpl->Add('JAVASCRIPT', '
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

                $more_data1 = $app->Secure->GetGET("more_data1");
                if ($more_data1 == 1) {
                   $where .= " AND s.eingelagert = 1";                 
                } else {
                }
                
                $more_data2 = $app->Secure->GetGET("more_data2");
                if ($more_data2 == 1) {
                   $where .= " AND s.lieferscheinpos <> 0";                 
                } else {
                }

                $count = "SELECT count(DISTINCT s.id) FROM seriennummern s LEFT JOIN artikel a on a.id = s.artikel WHERE $where";
//                $groupby = "";

                break;
            case "seriennummern_artikel_list":
                $allowed['seriennummern_artikel_list'] = array('list');
                $heading = array('','', 'Nummer', 'Artikel', 'Lagermenge', 'Nummern verf&uuml;gbar', 'Nummern ausgeliefert', 'Nummern gesamt', 'Men&uuml;','');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('a.id','a.id', 'a.nummer', 'a.name_de' , 'null', 'null', 'null', 'null', 'null', 'null');
                $searchsql = array('a.name_de', 'a.nummer');

                $menucol = 1;
                $defaultorder = 1;
                $defaultorderdesc = 0;
                $aligncenter = array();
                $alignright = array();
                $numbercols = array();
                $sumcol = array();

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',a.id,'\" />') AS `auswahl`";

//                $moreinfo = true; // Allow drop down details
//                $moreinfoaction = "lieferschein"; // specify suffix for minidetail-URL to allow different minidetails
//                $menucol = 11; // Set id col for moredata/menu

                $menu_link = array(
                    '<a href="index.php?module=seriennummern&action=enter&artikel=',
                    ['sql' => 'a.id'],
                    '">',
                    '<img src="./themes/'.$app->Conf->WFconf['defaulttheme'].'/images/add.png" title="Neue Seriennummern erfassen" border="0">',
                    '</a>',    
                    '<a href="index.php?module=seriennummern&action=nummern_list&artikel=',
                    ['sql' => 'a.id'],
                    '">',
                    '<img src="./themes/'.$app->Conf->WFconf['defaulttheme'].'/images/lupe.svg" title="Seriennummern anzeigen" border="0">',
                    '</a>'    
                );

                $sql = "SELECT SQL_CALC_FOUND_ROWS 
                        a.id,
                        $dropnbox,
                        CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',a.id,'\">',a.nummer,'</a>') as nummer,
                        a.name_de,
                        ".$app->erp->FormatMenge('auf_lager.anzahl').",
                        SUM(if(s.eingelagert <> 0,1,0)),
                        SUM(if(s.lieferscheinpos <> 0,1,0)),
                        SUM(if(s.id IS NULL,0,1)),
                        ".$app->erp->ConcatSQL($menu_link).",
                        a.id
                    FROM 
                        artikel a
                    LEFT JOIN
                    (
                        SELECT
                            a.id,
                            a.nummer,
                            a.name_de name,
                            SUM(lpi.menge) anzahl
                        FROM
                            artikel a
                        INNER JOIN lager_platz_inhalt lpi ON
                            a.id = lpi.artikel
                        WHERE
                            a.seriennummern <> 'keine' AND a.seriennummern <> ''
                        GROUP BY
                            a.id
                    ) auf_lager ON auf_lager.id = a.id
                    LEFT JOIN
                        seriennummern s ON s.artikel = a.id
                ";

                $where = "a.seriennummern <> 'keine' AND a.seriennummern <> ''";
                $count = "SELECT count(DISTINCT a.id) FROM artikel a WHERE $where";
                $groupby = "GROUP BY a.id";

                break;
            case "seriennummern_lieferscheine_list":
                $allowed['seriennummern_artikel_list'] = array('list');
                $heading = array('','', 'Lieferschein', 'Vom', 'Adresse', 'Menge Artikel', 'Nummern zugeordnet', 'Nummern fehlen', 'Men&uuml;','');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('l.id','l.id', 'l.belegnr', 'l.datum', 'adr.name', 'null', 'null', 'null', 'null', 'null');
                $searchsql = array('l.belegnr');

                $defaultorder = 1;
                $defaultorderdesc = 0;
                $aligncenter = array();
                $alignright = array();
                $numbercols = array();
                $sumcol = array();

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',l.id,'\" />') AS `auswahl`";

                //$menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=seriennummern&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=seriennummern&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $menu_link = array(
                    '<a href="index.php?module=seriennummern&action=enter&artikel=',
                    ['sql' => 'a.id'],
                    '">',
                    '<img src="./themes/'.$app->Conf->WFconf['defaulttheme'].'/images/add.png" title="Seriennummern erfassen" border="0">',
                    '</a>',    
                );

                $sql = "SELECT SQL_CALC_FOUND_ROWS 
                            l.id,
                            $dropnbox,
                            l.belegnr,
                            ".$app->erp->FormatDate("l.datum").",
                            adr.name,
                            ".$app->erp->FormatMengeFuerFormular("SUM(menge)").",
                            ".$app->erp->FormatMengeFuerFormular("COUNT(s.id)").",
                            ".$app->erp->FormatMengeFuerFormular("SUM(menge)-COUNT(s.id)").",
                            ".$app->erp->ConcatSQL($menu_link).",
                            l.id
                        FROM
                            lieferschein_position lp
                        INNER JOIN lieferschein l ON
                            l.id = lp.lieferschein
                        INNER JOIN artikel a ON
                            a.id = lp.artikel
                        INNER JOIN adresse adr ON
                            adr.id = l.adresse
                        LEFT JOIN seriennummern s ON
                            lp.id = s.lieferscheinpos
                ";

                $where = "(a.seriennummern <> 'keine') AND (l.datum >= (SELECT MIN(datum) FROM seriennummern WHERE artikel = a.id))";
                $count = "";
                $groupby = "GROUP BY l.id";
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
    
    function seriennummern_menu() {
        $this->app->erp->MenuEintrag("index.php?module=seriennummern&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=seriennummern&action=nummern_list", "Seriennummern");
        $this->app->erp->MenuEintrag("index.php?module=seriennummern&action=lieferscheine_list", "Lieferscheine");
     //   $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");    
    }
    
    function seriennummern_check_and_message($artikel_id) {
        $check_seriennummern = $this->seriennummern_check_serials($artikel_id);
               
        if (!empty($check_seriennummern)) {        
            $artikel_minus_id_links = array();
            $artikel_plus_id_links = array();                      
            foreach ($check_seriennummern as $artikel_id) {        
                if ($artikel_id['menge_nummern'] < $artikel_id['menge_auf_lager']) {                    
                    $artikel_minus_id_links[] = '<a href="index.php?module=seriennummern&action=enter&artikel='.$artikel_id['id'].'">'.$artikel_id['nummer'].'</a>';
                }
                else if ($artikel_id['menge_nummern'] > $artikel_id['menge_auf_lager']) {                    
                    $artikel_plus_id_links[] = '<a href="index.php?module=seriennummern&action=nummern_list&artikel='.$artikel_id['id'].'">'.$artikel_id['nummer'].'</a>';
                }
            }                
            if (!empty($artikel_minus_id_links)) {
                $this->app->YUI->Message('warning','Seriennummern fehlen f&uuml;r Artikel: '.implode(', ',$artikel_minus_id_links));                    
            }              
            if (!empty($artikel_plus_id_links)) {
                $this->app->YUI->Message('warning','Seriennummern Überschuss f&uuml;r Artikel: '.implode(', ',$artikel_plus_id_links));                    
            }              
        }              
    }
    
    function seriennummern_nummern_list() {
    
        $this->seriennummern_menu();
        
        // For transfer to tablesearch    
        $artikel_id = $this->app->Secure->GetGET('artikel');
        $this->app->User->SetParameter('seriennummern_artikel_id', $artikel_id);
        
        if (empty($artikel_id)) {
            $this->app->Tpl->Set('ARTIKEL_HIDDEN', "hidden");
        } else {
            $artikel = $this->app->DB->SelectRow("SELECT name_de, nummer FROM artikel WHERE id ='".$artikel_id."'");
            
            $check_seriennummern = $this->seriennummern_check_serials($artikel_id);                
            $check_seriennummern = $check_seriennummern[0];                          
              
            $this->app->Tpl->SetText('KURZUEBERSCHRIFT1','Anzeigen');                
            $this->app->Tpl->SetText('KURZUEBERSCHRIFT2',$artikel['name_de']." (Artikel ".$artikel['nummer'].")");
              
            $anzahl_fehlt = $check_seriennummern['menge_auf_lager']-$check_seriennummern['menge_nummern'];
        
            if ($anzahl_fehlt == 0) {
                $this->app->Tpl->addMessage('success', 'Seriennummern vollst&auml;ndig.');                 
            } 

            if ($anzahl_fehlt < 0) {
                $anzahl_fehlt = 0;
            }

            $letzte_seriennummer = (string) $this->app->DB->Select("SELECT seriennummer FROM seriennummern WHERE artikel = '".$artikel_id."' ORDER BY id DESC LIMIT 1");       
            $this->app->Tpl->Set('LETZTE', $letzte_seriennummer);

            $this->app->Tpl->Set('ANZAHL', $anzahl_fehlt);
            $this->app->Tpl->Set('ARTIKEL_ID', $artikel_id);

            $this->app->Tpl->Set('ARTIKELNUMMER', '<a href="index.php?module=artikel&action=edit&id='.$check_seriennummern['id'].'">'.$check_seriennummern['nummer'].'</a>');
            $this->app->Tpl->Set('ARTIKEL', $check_seriennummern['name']);
            $this->app->Tpl->Set('ANZLAGER', $check_seriennummern['menge_auf_lager']);        
            $this->app->Tpl->Set('ANZVORHANDEN', $check_seriennummern['menge_nummern']);
            $this->app->Tpl->Set('ANZFEHLT', $anzahl_fehlt);
        }
        
        $this->seriennummern_check_and_message($artikel_id);

        $this->app->YUI->TableSearch('TAB1', 'seriennummern_list', "show", "", "", basename(__FILE__), __CLASS__);              
        
        $this->app->Tpl->Parse('PAGE', "seriennummern_nummern_list.tpl");
    }    

    function seriennummern_artikel_list() {
        $this->seriennummern_menu();
        $this->seriennummern_check_and_message(null);

        $this->app->YUI->TableSearch('TAB1', 'seriennummern_artikel_list', "show", "", "", basename(__FILE__), __CLASS__);
               
        $this->app->Tpl->Parse('PAGE', "seriennummern_list.tpl");
    }    

    function seriennummern_lieferscheine_list() {
        $this->seriennummern_menu();
        $this->seriennummern_check_and_message(null);

        $this->app->YUI->TableSearch('TAB1', 'seriennummern_lieferscheine_list', "show", "", "", basename(__FILE__), __CLASS__);
               
        $this->app->Tpl->Parse('PAGE', "seriennummern_list.tpl");
    }   

    public function seriennummern_delete() {
        $id = (int) $this->app->Secure->GetGET('id');     

        if ($this->app->DB->Select("SELECT id FROM `seriennummern` WHERE `id` = '{$id}' AND `lieferscheinpos` = 0")) {
            $this->app->DB->Delete("DELETE FROM `seriennummern` WHERE `id` = '{$id}' AND `lieferscheinpos` = 0");        
            $this->app->Tpl->addMessage('error', 'Der Eintrag wurde gel&ouml;scht');        
        } else {
            $this->app->Tpl->addMessage('error', 'Der Eintrag kann nicht gel&ouml;scht werden!');        
        }
        $this->seriennummern_nummern_list();
    } 
      
    function seriennummern_enter() {

        $this->app->erp->MenuEintrag("index.php?module=seriennummern&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
        $artikel_id = (int) $this->app->Secure->GetGET('artikel');
        
        $artikel = $this->app->DB->SelectRow("SELECT name_de, nummer FROM artikel WHERE id ='".$artikel_id."'");

        $this->app->Tpl->SetText('KURZUEBERSCHRIFT1','Erfassen');                
        $this->app->Tpl->SetText('KURZUEBERSCHRIFT2',$artikel['name_de']." (Artikel ".$artikel['nummer'].")");

        $submit = $this->app->Secure->GetPOST('submit');
        $seriennummern = array();

        $seriennummern_text = $this->app->Secure->GetPOST('seriennummern');
        $seriennummern = explode('\n',str_replace(['\r'],'',$seriennummern_text));           

        switch ($submit) {
            case 'hinzufuegen':
                $eingabe = $this->app->Secure->GetPOST('eingabeneu');        
                if (!empty($eingabe)) {
                   $seriennummern[] = $eingabe;                          
                }
            break;
            case 'speichern':
                $seriennummern_not_written = array();
                foreach ($seriennummern as $seriennummer) {  
                
                    $seriennummer = trim($seriennummer);
                              
                    if (empty($seriennummer)) {
                        continue;
                    }
                              
                    $sql = "INSERT INTO seriennummern (seriennummer, artikel, logdatei, eingelagert) VALUES ('".$this->app->DB->real_escape_string($seriennummer)."', '".$artikel_id."', CURRENT_TIMESTAMP, 1)";
                    try {                
                        $this->app->DB->Insert($sql);
                    } catch (mysqli_sql_exception $e) {
                        $error = true;
                        $seriennummern_not_written[] = $seriennummer;
                    }                   
                }                              
                if ($error) {
                    $this->app->Tpl->addMessage('error', 'Einige Seriennummern konnten nicht gespeichert werden.');          
                }
                $seriennummern = $seriennummern_not_written;                             
            break;
            case 'assistent':
                $praefix = $this->app->Secure->GetPOST('praefix');
                $start = $this->app->Secure->GetPOST('start');
                $postfix = $this->app->Secure->GetPOST('postfix');
                $anzahl = (int) $this->app->Secure->GetPOST('anzahl');

                while ($anzahl) {
                    $seriennummern[] = $praefix.$start.$postfix;                          
                    $anzahl--;
                    $start++;
                }

            break;
        }
        
        $seriennummern = array_unique($seriennummern);                

        $check_seriennummern = $this->seriennummern_check_serials($artikel_id);                
        $check_seriennummern = $check_seriennummern[0];
              
        $anzahl_fehlt = $check_seriennummern['menge_auf_lager']-$check_seriennummern['menge_nummern'];
        
        if ($anzahl_fehlt == 0) {
            $this->app->Tpl->addMessage('success', 'Seriennummern vollst&auml;ndig.');                 
        } 

        if ($anzahl_fehlt < 0) {
            $anzahl_fehlt = 0;
        }

        $letzte_seriennummer = (string) $this->app->DB->Select("SELECT seriennummer FROM seriennummern WHERE artikel = '".$artikel_id."' ORDER BY id DESC LIMIT 1");       
        $regex_result = array(preg_match('/(.*?)(\d+)(?!.*\d)(.*)/', $letzte_seriennummer, $matches));

        $this->app->Tpl->Set('LETZTE', $letzte_seriennummer);

        $this->app->Tpl->Set('PRAEFIX', $matches[1]);
        $this->app->Tpl->Set('START', $matches[2]+1);
        $this->app->Tpl->Set('POSTFIX', $matches[3]);

        $this->app->Tpl->Set('ANZAHL', $anzahl_fehlt);

        $this->app->Tpl->Set('ARTIKELNUMMER', '<a href="index.php?module=artikel&action=edit&id='.$check_seriennummern['id'].'">'.$check_seriennummern['nummer'].'</a>');
        $this->app->Tpl->Set('ARTIKEL', $check_seriennummern['name']);
        $this->app->Tpl->Set('ANZLAGER', $check_seriennummern['menge_auf_lager']);        
        $this->app->Tpl->Set('ANZVORHANDEN', $check_seriennummern['menge_nummern']);
        $this->app->Tpl->Set('ANZFEHLT', $anzahl_fehlt);
        $this->app->Tpl->Set('SERIENNUMMERN', implode("\n",$seriennummern));                              
                               
        $this->app->YUI->AutoComplete("eingabe", "seriennummerverfuegbar",0,"&artikel=$artikel_id");   
        $this->app->Tpl->Parse('PAGE', "seriennummern_enter.tpl");        
                
    }
   
    /*
    * Check if all serial numbers are given
    * Return array of article ids
    */
    public function seriennummern_check_serials($artikel_id = null) : array {
        $sql = "
            SELECT
                auf_lager.id,
                nummer,
                name,                
                ".$this->app->erp->FormatMenge('auf_lager.anzahl')." menge_auf_lager,
                COALESCE(nummern_verfuegbar.anzahl,0) menge_nummern
            FROM
                (
                SELECT
                    a.id,
                    a.nummer,
                    a.name_de name,
                    SUM(lpi.menge) anzahl
                FROM
                    artikel a
                INNER JOIN lager_platz_inhalt lpi ON
                    a.id = lpi.artikel
                WHERE
                    a.seriennummern <> 'keine' AND a.seriennummern <> '' AND (a.id = '".$artikel_id."' OR '".$artikel_id."' = '')
                GROUP BY
                    a.id
            ) auf_lager
            LEFT JOIN(
                SELECT
                    artikel,
                    COUNT(id) anzahl
                FROM
                    seriennummern
                WHERE
                    lieferscheinpos = 0
                GROUP BY
                    artikel
            ) nummern_verfuegbar
            ON
                auf_lager.id = nummern_verfuegbar.artikel
            GROUP BY
                auf_lager.id
        ";
        
        $result = $this->app->DB->SelectArr($sql);        
        return(empty($result)?array():$result);
    }

    /*
    * Check if all delivery notes have serials
    * Return array of delivery note positions and head information
    */
    public function seriennummern_check_deliver_notes($lieferschein_id = null) : array {
        $sql = "
            SELECT
                l.id lieferschein,
                l.datum,
                l.belegnr lieferschein_belegnr,
                lp.id lieferschein_position,
                a.nummer,
                menge,
                COUNT(s.id) anzahl_nummern,
                GROUP_CONCAT(s.seriennummer)
            FROM
                lieferschein_position lp
            INNER JOIN lieferschein l ON
                l.id = lp.lieferschein
            INNER JOIN artikel a ON
                a.id = lp.artikel
            LEFT JOIN seriennummern s ON
                lp.id = s.lieferscheinpos
            WHERE (l.id = '".$lieferschein_id."' OR '".$lieferschein_id."' = '')
            GROUP BY
                lp.id    
            HAVING menge > anzahl_nummern        
        ";
        
        $result = $this->app->DB->SelectArr($sql);        
        return(empty($result)?array():$result);
    }
    
    protected function seriennummern_create_notification_artikel($artikel_id, $action, $title = 'Seriennummern', $message = 'Meldung', $button = 'Ok')
    {      
        // Notification erstellen
        $notification_message = new NotificationMessageData('default', $title);
        $artikel = $this->app->DB->SelectRow("SELECT name_de, nummer FROM artikel WHERE id = '".$artikel_id."' LIMIT 1");
        $notification_message->setMessage($message.' Artikel ('.$artikel['nummer'].') '.$artikel['name']);
        $notification_message->addTags(['seriennummern']);
        $notification_message->setPriority(true);

        $messageButtons = [
            [
                'text' => $button,
                'link' => sprintf('index.php?module=seriennummern&action='.$action.'&artikel=%s', $artikel_id),
            ]
        ];
        $notification_message->setOption('buttons', $messageButtons);

        /** @var NotificationService $notification */
        $notification = $this->app->Container->get('NotificationService');
        $notification->createFromData($this->app->User->GetID(), $notification_message);
    }

    protected function seriennummern_create_notification_lieferschein($lieferschein_id, $action, $title = 'Seriennummern', $message = 'Meldung', $button = 'Ok')
    {      
        // Notification erstellen
        $notification_message = new NotificationMessageData('default', $title);
        $lieferschein = $this->app->DB->SelectRow("SELECT belegnr FROM lieferschein WHERE id = '".$lieferschein_id."' LIMIT 1");
        $notification_message->setMessage($message.' Lieferschein '.$lieferschein['belegnr']);
        $notification_message->addTags(['seriennummern']);
        $notification_message->setPriority(true);

        $messageButtons = [
            [
                'text' => $button,
                'link' => sprintf('index.php?module=seriennummern&action='.$action.'&lieferschein=%s', $lieferschein_id),
            ]
        ];
        $notification_message->setOption('buttons', $messageButtons);

        /** @var NotificationService $notification */
        $notification = $this->app->Container->get('NotificationService');
        $notification->createFromData($this->app->User->GetID(), $notification_message);
    }
    
    /*
    * Check if new numbers need to be entered, if yes, create notification
    */
    public function seriennummern_check_and_message_stock_added(int $artikel_id) {
        $check_seriennummern = $this->seriennummern_check_serials($artikel_id);
        if ($check_seriennummern[0]['menge_nummern'] < $check_seriennummern[0]['menge_auf_lager']) {
            $this->seriennummern_create_notification_artikel($artikel_id, 'enter', 'Seriennummern','Bitte Seriennummern f&uuml;r Einlagerung erfassen','Zur Eingabe');
        }          
    }    

    /*
    * Check if numbers need to be entered after stock removal, if yes, create notification
    */
    public function seriennummern_check_and_message_delivery_note_removed(int $lieferschein_id) {
        $check_deliver_notes = $this->seriennummern_check_deliver_notes($lieferschein_id);
        if (!empty($check_deliver_notes)) {
            $this->seriennummern_create_notification_lieferschein($lieferschein_id, 'enter', 'Seriennummern','Bitte Seriennummern f&uuml;r Lieferschein erfassen','Zur Eingabe');
        }          
    }
    
 }
