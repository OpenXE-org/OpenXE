<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Mahnwesen {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "mahnwesen_list");
        $this->app->ActionHandler("stufe_list", "mahnwesen_stufe_list");
        $this->app->ActionHandler("create", "mahnwesen_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "mahnwesen_edit");        
        $this->app->ActionHandler("einstellungen", "mahnwesen_einstellungen");
        $this->app->ActionHandler("delete", "mahnwesen_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    public function TableSearch($app, $name, $erlaubtevars) {
        switch ($name) {
            case "mahnwesen_list":

                $extended_mysql55 = ",'de_DE'";

                $allowed['mahnwesen_list'] = array('list');
                $heading = array('', '', 'Rechnung', 'Vom', 'Kd-Nr.', 'Kunde', 'Land', 'Projekt', 'Zahlung', 'Betrag (brutto)', 'W&auml;hrung', 'Zahlstatus', 'Differenz', 'Status','F&auml;llig am','Tage','Mahnstufe','Brief','E-Mail','Gemahnt','Mahn-Datum','Sperre','Interne Bemerkung','Men&uuml;');
                $width = array('1%','1%','01%',      '01%', '01%',    '05%',   '01%',  '01%',     '01%',     '01%',             '01%',          '01%',        '01%',       '01%',   '01%',           '01%', '01%',      '01%',    '01%', '01%',    '01%',      '01%',   '20%',              '1%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $mahnwesen_stufe_filter = $this->app->DB->real_escape_string($this->app->User->GetParameter('mahnwesen_stufe_filter'));               

                $faellig_datum = "DATE_ADD(r.datum, INTERVAL r.zahlungszieltage DAY)";
                $faellig_tage = "DATEDIFF(CURRENT_DATE,DATE_ADD(r.datum, INTERVAL r.zahlungszieltage DAY))";
                $mahn_druck = "if(m.druck,'Ja','')";
                $mahn_mail = "if(m.mail,'Ja','')";
                $mahn_versendet = "if(r.versendet_mahnwesen,'Ja','')";

                $findcols = array('r.id','r.id','r.belegnr', 'r.datum', 'r.kundennummer','r.name', 'r.land','p.abkuerzung','r.zahlungsweise','r.soll','r.waehrung','r.zahlungsstatus','r.soll','r.status',$faellig_datum,$faellig_tage,'m.name',$mahn_druck,$mahn_mail,$mahn_versendet,'mahnwesen_datum');
                $searchsql = array('r.belegnr', 'r.name', $faellig_datum, 'r.kundennummer');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		        $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',r.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql_tables = "rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id LEFT JOIN auftrag au ON au.id = r.auftragid LEFT JOIN mahnwesen m ON r.mahnwesen = m.id";

                $sql = "SELECT SQL_CALC_FOUND_ROWS 
                    r.id,
                    $dropnbox,                    
                    r.belegnr, 
                    ".$app->erp->FormatDateShort('r.datum')." as vom,
                    if(r.kundennummer <> '',r.kundennummer,adr.kundennummer),
                    CONCAT(" . $app->erp->MarkerUseredit("r.name", "r.useredittimestamp") . ", if(r.internebezeichnung!='',CONCAT('<br><i style=color:#999>',r.internebezeichnung,'</i>'),'')) as kunde,
                    r.land as land,
                    p.abkuerzung as projekt,
                    r.zahlungsweise as zahlungsweise,
                    FORMAT(r.soll,2{$extended_mysql55} ) as soll,
                    ifnull(r.waehrung,'EUR'),
                    r.zahlungsstatus as zahlung, 
                    if(r.soll-r.ist!=0 AND r.ist > 0,FORMAT(r.ist-r.soll,2{$extended_mysql55}),FORMAT((r.soll-r.ist)*-1,2{$extended_mysql55})) as fehlt,
                    if(r.status = 'storniert' AND r.teilstorno = 1,'TEILSTORNO',UPPER(r.status))  as status,
                    ".$app->erp->FormatDateShort($faellig_datum)." as faellig_datum,
                    if(".$faellig_tage.">0,".$faellig_tage.",'') as faellig_tage,
                    m.name,
                    ".$mahn_druck.",
                    ".$mahn_mail.",
                    ".$mahn_versendet.",
                    if(mahnwesen_datum <> '0000-00-00',".$app->erp->FormatDateShort('mahnwesen_datum').",''),
                    if(r.mahnwesen_gesperrt,'Ja',''),
                    REPLACE(r.mahnwesen_internebemerkung,'\r\n','<br> '),
                    r.id
                    FROM ".$sql_tables;

                $where = " r.belegnr <> '' AND r.status <> 'storniert'";
                
                if (!empty($mahnwesen_stufe_filter)) {
                    $where .= " AND m.id = '".$mahnwesen_stufe_filter."' AND r.versendet_mahnwesen ";
                }

                // Toggle filters
                $this->app->Tpl->Add('JQUERYREADY', "$('#zu_mahnen').click( function() { fnFilterColumn1( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#inkl_bezahlte').click( function() { fnFilterColumn2( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#inkl_gesperrte').click( function() { fnFilterColumn3( 0 ); } );");                
                for ($r = 1;$r <= 3;$r++) {
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

                $more_data1 = $app->Secure->GetGET("more_data1");
                if ($more_data1 == 1 && empty($mahnwesen_stufe_filter)) {
                    $where .= " AND NOT r.versendet_mahnwesen AND r.mahnwesen <> ''";
                } else {
                }
                $more_data2 = $app->Secure->GetGET("more_data2");
                if ($more_data2 == 1) {
                } else {
                    $where .= " AND r.zahlungsstatus <> 'bezahlt' ";
                }

                $more_data3 = $app->Secure->GetGET("more_data3");
                if ($more_data3 == 1) {
                }
                else {
                    $where .= " AND NOT r.mahnwesen_gesperrt ";
                }                
                // END Toggle filters

                $count = "SELECT count(DISTINCT r.id) FROM ".$sql_tables." WHERE $where";
//                $groupby = "";

                break;
                case "mahnwesen_einstellungen":
                    $allowed['mahnwesen_list'] = array('list');
                    $heading = array('', 'Tage', 'Name', 'Gebuehr', 'E-Mail', 'Druck', 'Men&uuml;');
                    $width = array('1%','1%','10%'); // Fill out manually later

                    // columns that are aligned right (numbers etc)
                    // $alignright = array(4,5,6,7,8); 

                    $findcols = array('m.id', 'm.tage','m.name', 'm.gebuehr', 'm.mail','m.druck');
                    $searchsql = array('m.name','m.tage', 'm.gebuehr', 'm.versandmethode');

                    $defaultorder = 1;
                    $defaultorderdesc = 1;

	        	    $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`";

                    $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=mahnwesen&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=mahnwesen&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                    $sql = "SELECT SQL_CALC_FOUND_ROWS 
                                m.id,
	                            $dropnbox,
	                            m.tage,
	                            m.name,
	                            ".$this->app->erp->FormatMenge('m.gebuehr',2).",
	                            if(m.mail,'Ja',''),
	                            if(m.druck,'Ja',''),
	                            m.id FROM mahnwesen m";

                    $where = "1";
                    $count = "SELECT count(DISTINCT id) FROM mahnwesen WHERE $where";
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

    // For Tab-highlighting
    function mahnwesen_stufe_list() {
        $this->mahnwesen_list();
    }
    
    function mahnwesen_list() {
        $this->app->erp->MenuEintrag("index.php?module=mahnwesen&action=list", "&Uuml;bersicht");

        if($this->app->Secure->GetPOST('sel_aktion') && $this->app->erp->RechteVorhanden('rechnung', 'edit'))
        {
          $drucker = $this->app->Secure->GetPOST('seldrucker');
          $aktion = $this->app->Secure->GetPOST('sel_aktion');
          $auswahl = $this->app->Secure->GetPOST('auswahl');
          if($drucker > 0) {
            $this->app->erp->BriefpapierHintergrundDisable($drucker);
          }
          if(is_array($auswahl)) {
            foreach($auswahl as $auswahlKey => $auswahlValue) {
              if((int)$auswahlValue > 0) {
                $auswahl[$auswahlKey] = (int)$auswahlValue;
              }
              else {
                unset($auswahl[$auswahlKey]);
              }
            }
            switch($aktion)
            {
              case 'bezahlt':
                $this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='bezahlt', bezahlt_am = now(), mahnwesenfestsetzen='1',mahnwesen_internebemerkung=CONCAT(mahnwesen_internebemerkung,'\r\n','Manuell als bezahlt markiert am ".date('d.m.Y')."')  WHERE id IN (".implode(', ',$auswahl).')');
              break;
              case 'offen':
                $this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='offen',bezahlt_am = NULL, mahnwesen_internebemerkung=CONCAT(mahnwesen_internebemerkung,'\r\n','Manuell als bezahlt entfernt am ".date('d.m.Y')."') WHERE id IN (".implode(', ',$auswahl).')');
              break;
              case 'mahnung_reset':   
                $sql = "UPDATE rechnung SET mahnwesen='', versendet_mahnwesen ='', mahnwesen_datum = '0000-00-00' WHERE id IN (".implode(', ',$auswahl).')';
                $this->app->DB->Update($sql);
              break;
              case 'mahnen':

                $mails = 0;
                $drucke = 0;
                foreach ($auswahl as $rechnung_id) {
                    $mahnung = $this->MahnwesenMessage($rechnung_id);

                    // Check first
                    if (empty($mahnung)) {
                        continue;
                    }
                    if ($mahnung['mail'] && empty($mahnung['rechnung']['email'])) {
                        $msg .= "<div class=\"error\">Keine E-Mail-Adresse hinterlegt bei Rechnung ".$mahnung['rechnung']['belegnr'].".</div>";                  
                        continue;
                    }
                    if ($mahnung['druck']) {
                        $drucker = $this->app->Secure->GetPOST('seldrucker');
                        if($drucker > 0) {
                            $this->app->erp->BriefpapierHintergrundDisable($drucker);
                        } else {
                            $msg .= "<div class=\"error\">Kein Drucker gew&auml;hlt.</div>";                  
                            break;
                        }                                           
                    }

                    // Create PDF
                    if(class_exists('RechnungPDFCustom')) {
                        $Brief = new RechnungPDFCustom($this->app,$projekt);
                    }
                    else {
                        $Brief = new RechnungPDF($this->app,$projekt);
                    }
                    $Brief->GetRechnung($rechnung_id,$mahnung['betreff'],0,null,$mahnung['body']);
                    $tmpfile = $Brief->displayTMP();

                    $fileid = $this->app->erp->CreateDatei($Brief->filename,$mahnung['betreff'],"","",$tmpfile,$this->app->User->GetName());
                    $this->app->erp->AddDateiStichwort($fileid,'mahnung','rechnung',$rechnung_id);            

                    if ($mahnung['druck']) {
                        $this->app->printer->Drucken($drucker,$tmpfile);           
                        $this->MahnungCRM('brief',$mahnung['rechnung'], $mahnung['betreff'], $mahnung['body'],$fileid,$Brief->filename);                    
                        $this->app->erp->RechnungProtokoll($rechnung_id,'Mahnung gedruckt');               
                        $drucke++;
                    }           

                    if ($mahnung['mail']) {
                         $senderName = $this->app->User->GetName()." (".$this->app->erp->GetFirmaAbsender().")";
                         $senderAddress = $this->app->erp->GetFirmaMail();
                         //   function MailSend($from,$from_name,$to,$to_name,$betreff,$text,$files="",$projekt="",$signature=true,$cc="",$bcc="", $system = false)
                         $result = $this->app->erp->MailSend(
                              $senderAddress,
                              $senderName,
                              $mahnung['rechnung']['email'],
                              $mahnung['rechnung']['email'],
                              htmlentities($mahnung['betreff']),
                              htmlentities($mahnung['body']),
                              [$tmpfile],
                              $mahnung['rechnung']['projekt'],
                              true,
                              $cc,
                              '',
                              true
                          );

                        if ($result = 0) {
                            $msg .= "<div class=\"error\">Fehler beim E-Mail-Versand bei Rechnung ".$mahnung['rechnung']['belegnr'].".</div>";                  
                            continue;
                        }

                        $this->MahnungCRM('email',$mahnung['rechnung'], $mahnung['betreff'], $mahnung['body'],$fileid,$Brief->filename);                    
                        $this->app->erp->RechnungProtokoll($rechnung_id,'Mahnung versendet');
                        $mails++;
                    }

                    unlink($tmpfile);

                    $sql = "UPDATE rechnung set mahnwesen_datum = CURRENT_DATE, versendet_mahnwesen = 1 WHERE id IN (".implode(', ',$auswahl).')';
                    $this->app->DB->Update($sql);

                }
                $msg .= "<div class=\"success\">$mails E-Mails versendet, $drucke Dokumente gedruckt.</div>";                              
              break;
            }
          }      
        } // ende ausfuehren

        // Refresh status
        if($this->app->Secure->GetPOST('mahnstufe_berechnen') && $this->app->erp->RechteVorhanden('rechnung', 'edit')) {
            $this->app->erp->rechnung_zahlstatus_berechnen();   
        }
     
        // Create tabs
        $sql = "
                SELECT
                    r.id,
                    r.mahnwesen,
                    r.versendet_mahnwesen,
                    rid_mid.mahnwesen_neu,
                    rid_mid.name
                FROM
                    rechnung r
                INNER JOIN 
                    (
                    SELECT
                        id_tage.id,
                        m.id AS mahnwesen_neu,
                        m.name,
                        m.tage
                    FROM
                        mahnwesen m
                    INNER JOIN(
                        SELECT
                            id,
                            MAX(tage) AS tage
                        FROM
                            (
                            SELECT
                                r.id,
                                m.tage                                    
                            FROM
                                rechnung r
                            INNER JOIN mahnwesen m ON
                                DATEDIFF(
                                    CURRENT_DATE,
                                    DATE_ADD(
                                        r.datum,
                                        INTERVAL r.zahlungszieltage DAY
                                    )
                                ) > m.tage
                            WHERE
                                r.zahlungsstatus = 'offen'
                            ORDER BY
                                `r`.`id` ASC
                        ) temp
                    GROUP BY
                        id
                    ) id_tage
                ON
                    m.tage = id_tage.tage
                ) rid_mid
                ON r.id = rid_mid.id                
                ORDER BY rid_mid.tage
                ";
        $offene_rechnungen = $this->app->DB->SelectArr($sql);         
                   
        foreach ($offene_rechnungen as $offene_rechnung) {
            if ($offene_rechnung['mahnwesen'] != $offene_rechnung['mahnwesen_neu']) {
                $sql = "UPDATE rechnung set mahnwesen = ".$offene_rechnung['mahnwesen_neu'].", versendet_mahnwesen = 0 WHERE id = ".$offene_rechnung['id'];
                $this->app->DB->Update($sql);               
            }             
        }

        $menus = $this->app->DB->SelectArr("
            SELECT
                m.id mahnung,
                m.name,
                SUM(if(r.versendet_mahnwesen = 1,1,0)) anzahl
            FROM
                mahnwesen m
            LEFT JOIN rechnung r ON
                m.id = r.mahnwesen
            WHERE
	            r.id IS NULL OR
                (
                    r.zahlungsstatus <> 'bezahlt' AND
                    r.mahnwesen_gesperrt <> 1
                )
            GROUP BY    
                m.id
            ORDER BY
                m.tage ASC
        ");

        foreach ($menus as $menu) {
            $suffix = "";
            if ($menu['anzahl']) {
                $suffix = " (".$menu['anzahl'].")";
            }
            $this->app->erp->MenuEintrag("index.php?module=mahnwesen&action=stufe_list&stufe=".$menu['mahnung'], $this->app->DB->real_escape_string($menu['name']).$suffix);
        }

        if (!empty($msg)) {
            $this->app->Tpl->Set('MESSAGE', $msg);
        }

        if($this->app->erp->RechteVorhanden('rechnung', 'manuellbezahltmarkiert')){
            $this->app->Tpl->Set('ALSBEZAHLTMARKIEREN', '<option value="bezahlt">{|als bezahlt markieren|}</option>');
        }

        $this->app->Tpl->Set('SELDRUCKER', $this->app->erp->GetSelectDrucker($this->app->User->GetParameter('rechnung_list_drucker')));

        $mahnwesen_stufe_filter = $this->app->Secure->GetGET('stufe');
        $this->app->User->SetParameter('mahnwesen_stufe_filter', $mahnwesen_stufe_filter);               
        if (!empty($mahnwesen_stufe_filter)) {
            $this->app->Tpl->Set('KURZUEBERSCHRIFT2',"Stufe: ".$this->app->DB->Select("SELECT name FROM mahnwesen WHERE id = ".$mahnwesen_stufe_filter." LIMIT 1"));        
            $this->app->Tpl->Set('ZU_MAHNEN_HIDDEN', 'hidden');
        }              
        
        $this->app->YUI->TableSearch('TAB1', 'mahnwesen_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "mahnwesen_list.tpl");
    }    

    function mahnwesen_einstellungen() {
        $this->app->erp->MenuEintrag("index.php?module=mahnwesen&action=einstellungen", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=mahnwesen&action=create", "Neu anlegen");
        $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list", "Zur&uuml;ck");
        $this->app->erp->Headlines('Mahnwesen Einstellungen');

        $this->app->YUI->TableSearch('TAB1', 'mahnwesen_einstellungen', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "mahnwesen_einstellungen.tpl");
    }    

    public function mahnwesen_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("DELETE FROM `mahnwesen` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->mahnwesen_einstellungen();
    } 

    /*
     * Edit mahnwesen item
     * If id is empty, create a new one
     */
        
    function mahnwesen_edit() {
        $id = $this->app->Secure->GetGET('id');
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('artikel',$id))
        {
          return;
        }   
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=mahnwesen&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=mahnwesen&action=einstellungen", "Zur&uuml;ck zur &Uuml;bersicht");
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

            $sql = "INSERT INTO mahnwesen (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=mahnwesen&action=einstellungen&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

    
        // Load values again from database
    	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',m.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS m.id, $dropnbox, m.name, m.tage, m.gebuehr, m.mail, m.druck, m.id FROM mahnwesen m"." WHERE id=$id");

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

        $this->app->Tpl->Set('MAIL', $result[0]['mail']?'checked':'');
    	$this->app->Tpl->Set('DRUCK', $result[0]['druck']?'checked':'');

        $this->app->Tpl->Parse('PAGE', "mahnwesen_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        $input['name'] = $this->app->Secure->GetPOST('name');
    	$input['tage'] = $this->app->Secure->GetPOST('tage');
    	$input['gebuehr'] = $this->app->Secure->GetPOST('gebuehr');
    	$input['mail'] = $this->app->Secure->GetPOST('mail')?'1':'0';
    	$input['druck'] = $this->app->Secure->GetPOST('druck')?'1':'0';
	    return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        $this->app->Tpl->Set('NAME', $input['name']);
    	$this->app->Tpl->Set('TAGE', $input['tage']);
	    $this->app->Tpl->Set('GEBUEHR', $input['gebuehr']);
    	$this->app->Tpl->Set('MAIL', $input['mail']);
    	$this->app->Tpl->Set('DRUCK', $input['druck']);
    }


    /*
    * Constuct the Mahnwesen message according to GeschäftsbriefVorlage
    * Returns Array (string betreff, string body, boolean mail, boolean druck, array rechnung)
    */
    function MahnwesenMessage($rechnung_id) {

        $sql = "SELECT 
                    r.*,
                    ".$this->app->erp->FormatDate('datum')." datum,
                    ".$this->app->erp->FormatDate('CURRENT_DATE')." heute,
                    m.name as mahn_name,
                    m.tage as mahn_tage,
                    m.gebuehr as mahn_gebuehr,
                    m.mail as mahn_mail,
                    m.druck as mahn_druck
                FROM 
                    rechnung r 
                INNER JOIN 
                    mahnwesen m 
                ON 
                r.mahnwesen = m.id WHERE r.id = ".$rechnung_id." LIMIT 1";       
        $rechnungarr = $this->app->DB->SelectArr($sql)[0];

        if (empty($rechnungarr)) {
            return;
        }
       
          $adresse = $rechnungarr['adresse'];
          if($sprache==''){
            $sprache = $rechnungarr['sprache'];
          }
          if($sprache==''){
            $sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' LIMIT 1");
          }

          $kundennummer = $rechnungarr['kundennummer'];
          $projekt = $rechnungarr['projekt'];
          $auftrag= $rechnungarr['auftrag'];
          $buchhaltung= $rechnungarr['buchhaltung'];
          $lieferschein = $rechnungarr['lieferschein'];
          $lieferscheinid = $lieferschein;
          if($lieferscheinid){
            $lieferschein = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
          }else{
            $lieferschein = '';
          }
          $bestellbestaetigung = $rechnungarr['kundennummer'];
          $datum = $rechnungarr['datum_de'];
          $datum_sql = $rechnungarr['datum'];
          $belegnr = $rechnungarr['belegnr'];
          $doppel = $rechnungarr['doppel'];
          $freitext = $rechnungarr['freitext'];
          $ustid = $rechnungarr['ustid'];
          $soll = $rechnungarr['soll'];
          $ist = $rechnungarr['ist'];
          $land = $rechnungarr['land'];
          $mahnwesen_datum = $rechnungarr['mahnwesen_datum'];
          $mahnwesen_datum_deutsch = $rechnungarr['mahnwesen_datum_de'];
          $zahlungsweise = $rechnungarr['zahlungsweise'];
          $zahlungsstatus = $rechnungarr['zahlungsstatus'];
          $zahlungszieltage = $rechnungarr['zahlungszieltage'];
          $zahlungszieltageskonto = $rechnungarr['zahlungszieltageskonto'];
          $zahlungszielskonto = $rechnungarr['zahlungszielskonto'];
          $waehrung = $rechnungarr['waehrung'];           

          $zahlungdatum = $this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD(datum, INTERVAL $zahlungszieltage DAY),'%d.%m.%Y') FROM rechnung WHERE id='$rechnung_id' LIMIT 1");

          if($_datum!=null)
          {
            $mahnwesen_datum = $this->app->String->Convert($_datum,'%1.%2.%3','%3-%2-%1');
            $mahnwesen_datum_deutsch = $_datum;
          }

          $zahlungsweise = strtolower($zahlungsweise);
        /*
          if($als=='zahlungserinnerung')
          {
            $body = $this->GetGeschaeftsBriefText("MahnwesenZahlungserinnerung",$sprache,$projekt,"rechnung",$rechnung_id);
            $tage = $this->GetKonfiguration('mahnwesen_m1_tage');
          }
          else if($als=='mahnung1')
          {
            $body = $this->GetGeschaeftsBriefText("MahnwesenMahnung1",$sprache,$projekt,"rechnung",$rechnung_id);
            $mahngebuehr = $this->GetKonfiguration('mahnwesen_m1_gebuehr');
            $tage = $this->GetKonfiguration('mahnwesen_m2_tage');
          }
          else if($als=='mahnung2')
          {
            $body = $this->GetGeschaeftsBriefText("MahnwesenMahnung2",$sprache,$projekt,"rechnung",$rechnung_id);
            $tage = $this->GetKonfiguration('mahnwesen_m3_tage');
            $mahngebuehr = $this->GetKonfiguration('mahnwesen_m2_gebuehr');
          }
          else if($als=='mahnung3')
          {
            $body = $this->GetGeschaeftsBriefText("MahnwesenMahnung3",$sprache,$projekt,"rechnung",$rechnung_id);
            $tage = $this->GetKonfiguration('mahnwesen_ik_tage');
            $mahngebuehr = $this->GetKonfiguration('mahnwesen_m3_gebuehr');
          }
          else if($als=='inkasso')
          {
            $body = $this->GetGeschaeftsBriefText("MahnwesenInkasso",$sprache,$projekt,"rechnung",$rechnung_id);
            //$tage = $this->GetKonfiguration("mahnwesen_ik_tage");
            $tage = 3; //eigentlich vorbei
            $mahngebuehr = $this->GetKonfiguration('mahnwesen_ik_gebuehr');
          }
          else
          {
            $body = $this->app->erp->Beschriftung("dokument_anschreiben");
          } */

          $betreff = $this->app->erp->GetGeschaeftsBriefBetreff($rechnungarr['mahn_name'],$sprache,$projekt,"rechnung",$rechnung_id);
          $body = $this->app->erp->GetGeschaeftsBriefText($rechnungarr['mahn_name'],$sprache,$projekt,"rechnung",$rechnung_id);

            if (empty($betreff) || empty($body)) {
                throw new QueryFailureException("Geschaeftsbrief-Vorlage nicht gefunden: ''".$rechnungarr['mahn_name']."'");
            }

          $offen = $this->app->erp->GetSaldoDokument($rechnung_id, 'rechnung');

          if($tage <=0) $tage = 0;

/*          $datummahnung= $this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD('$mahnwesen_datum', INTERVAL $tage DAY),'%d.%m.%Y')");
          $datumrechnungzahlungsziel= $this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD('$datum_sql', INTERVAL $zahlungszieltage DAY),'%d.%m.%Y')");

          $tage_ze = $zahlungszieltage + $this->GetKonfiguration('mahnwesen_m1_tage');
          $datumzahlungserinnerung= $this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD('$datum_sql', INTERVAL $tage_ze DAY),'%d.%m.%Y')");*/

          // checkstamp $this->CheckStamp("jhdskKUHsiusakiakuhsd"); // errechnet aus laufzeit und kundenid // wenn es nicht drinnen ist darf es nicht gehen
/*
          if($mahngebuehr=='' || !is_numeric($mahngebuehr))
            $mahngebuehr = 0;

          //$offen= '11,23';
          $body = str_replace('{RECHNUNG}',$belegnr,$body);
          $body = str_replace('{BELEGNR}',$belegnr,$body);
          $body = str_replace('{DATUMRECHNUNG}',$datum,$body);
          $body = str_replace('{TAGE}',$tage,$body);
          $body = str_replace('{OFFEN}',$this->app->erp->formatMoney(-$offen['betrag'],$offen['waehrung']),$body);
          $body = str_replace('{SOLL}',$this->app->erp->formatMoney($soll,$waehrung),$body);
          $body = str_replace('{SUMME}',$this->app->erp->formatMoney($soll - $ist + $mahngebuehr,$waehrung),$body);
          $body = str_replace('{IST}',$this->app->erp->formatMoney($ist,$waehrung),$body);
          $body = str_replace('{DATUM}',$datummahnung,$body);
          $body = str_replace('{MAHNGEBUEHR}',$this->app->erp->formatMoney($mahngebuehr,$waehrung),$body);
          $body = str_replace('{OFFENMITMAHNGEBUEHR}',$this->app->erp->formatMoney($mahngebuehr + $soll - $ist,$waehrung),$body);
          $body = str_replace('{MAHNDATUM}',$mahnwesen_datum_deutsch,$body);


          // Im Protokoll suchen Datum von Zahlungserinnerung, Mahnung 1, Mahnung 2, Mahnung 3

          $mahnung1 = $this->app->DB->Select("SELECT DATE_FORMAT(zeit,'%d.%m.%Y') FROM rechnung_protokoll WHERE rechnung='$rechnung_id'
              AND grund LIKE 'Mahnung1 versendet%' ORDER by Zeit DESC LIMIT 1");

          $mahnung2 = $this->app->DB->Select("SELECT DATE_FORMAT(zeit,'%d.%m.%Y') FROM rechnung_protokoll WHERE rechnung='$rechnung_id'
              AND grund LIKE 'Mahnung2 versendet%' ORDER by Zeit DESC LIMIT 1");

          $mahnung3 = $this->app->DB->Select("SELECT DATE_FORMAT(zeit,'%d.%m.%Y') FROM rechnung_protokoll WHERE rechnung='$rechnung_id'
              AND grund LIKE 'Mahnung3 versendet%' ORDER by Zeit DESC LIMIT 1");

          $body = str_replace('{DATUMMAHNUNG1}',$mahnung1,$body);
          $body = str_replace('{DATUMMAHNUNG2}',$mahnung2,$body);
          $body = str_replace('{DATUMMAHNUNG3}',$mahnung3,$body);

          $body = str_replace('{DATUMZAHLUNGSERINNERUNGFAELLIG}',$datumzahlungserinnerung,$body);
          $body = str_replace('{DATUMZAHLUNGSERINNERUNG}',$datumzahlungserinnerung,$body);
          $body = str_replace('{DATUMRECHNUNGZAHLUNGSZIEL}',$datumrechnungzahlungsziel,$body);*/

        $mapping = [
            'rechnung' => $belegnr,
            'belegnr' => $belegnr,
            'datum' => $datum_sql,
            'offen' => $this->app->erp->EUR(-$offen['betrag'])." ".$offen['waehrung'],
            'mahngebuehr' => $this->app->erp->EUR($rechnungarr['mahn_gebuehr']),
            'heute' => $rechnungarr['heute']
        ];

        $betreff = $this->app->erp->ParseVars($mapping,$betreff);
        $body = $this->app->erp->ParseVars($mapping,$body);
        $body = $this->app->erp->ParseUserVars('rechnung',$rechnung_id,$body);

        return(array(
                'betreff' => $betreff,
                'body' => $body,
                'mail' => $rechnungarr['mahn_mail'] != 0,
                'druck' => $rechnungarr['mahn_druck'] != 0,
                'adresse' => $rechnungarr['adresse'],
                'empfaenger' => $rechnungarr['email'],
                'projekt' => $rechnungarr['projekt'],
                'rechnung' => $rechnungarr
            ));
  
    }

    /*
    * Create CRM entry for mahnung
    * typ = brief, email
    */
    function MahnungCRM(string $typ, array $rechnung, $betreff, $text, $fileid, $filename) {
          
        $data = array();
        $data['typ'] = $typ;
        $data['projekt'] = $rechnung['projekt'];
        $data['datum'] = date('Y-m-d');
        $data['uhrzeit'] = date('Y-m-d H:i:s');
        $data['user'] = $rechnung['adresse'];
        $data['an'] = $rechnung['name'];
        $data['adresse'] = $rechnung['strasse'];            
        $data['plz'] = $rechnung['plz'];            
        $data['ort'] = $rechnung['ort'];            
        $data['betreff'] = $betreff;
        $data['content'] = $text;
        $data['email_an'] = $rechnung['email'];
        $data['sent'] = 1;

        $crm_id = $this->app->erp->DokumentCreate($data,$this->app->User->GetAdresse());
        $this->app->erp->AddDateiStichwort($fileid,'anhang','dokument',$crm_id);

    }       

}
