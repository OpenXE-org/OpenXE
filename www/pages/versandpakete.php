<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Versandpakete {

    const STATUS = ARRAY ('neu','versendet','abgeschlossen','storniert');

    const SQL_VERSANDPAKETE_LIEFERSCHEIN = "
                    SELECT DISTINCT
                        versandpaket,
                        lieferschein
                    FROM
                        versandpaket_lieferschein_position vlp
                    INNER JOIN lieferschein_position lp ON
                        vlp.lieferschein_position = lp.id
                    UNION
                    SELECT DISTINCT
                        id,
                        lieferschein_ohne_pos AS lieferschein
                    FROM
                        versandpakete
                    WHERE
                        lieferschein_ohne_pos <> 0
                ";

    const SQL_VERSANDPAKETE_LIEFERSCHEIN_WITH_POS = "
                    SELECT DISTINCT
                        versandpaket,
                        lieferschein
                    FROM
                        versandpaket_lieferschein_position vlp
                    INNER JOIN lieferschein_position lp ON
                        vlp.lieferschein_position = lp.id                   
                ";

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "versandpakete_list");        
        $this->app->ActionHandler("create", "versandpakete_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "versandpakete_edit");
        $this->app->ActionHandler("add", "versandpakete_add");
        $this->app->ActionHandler("lieferungen", "versandpakete_lieferungen");
        $this->app->ActionHandler("lieferung", "versandpakete_lieferung");
        $this->app->ActionHandler("stapelverarbeitung", "versandpakete_stapelverarbeitung");
        $this->app->ActionHandler("paketmarke", "versandpakete_paketmarke");
        $this->app->ActionHandler("delete", "versandpakete_delete");
        $this->app->ActionHandler("deletepos", "versandpakete_deletepos");
        $this->app->ActionHandler("minidetail", "versandpakete_minidetail");
        $this->app->ActionHandler("minidetaillieferschein", "versandpakete_minidetaillieferschein");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        $lieferschein_filter = null;
        switch ($name) {
            case "lieferung_versandpakete_list":
                $lieferschein_filter = $app->User->GetParameter('versandpakete_lieferschein_filter');
                if ($lieferschein_filter) {
                    $lieferschein_filter_where = "WHERE lieferschein = ".$lieferschein_filter." OR lieferschein_ohne_pos = ".$lieferschein_filter;
                    $lieferung_link = "&lieferung=".$lieferschein_filter;
                }
            // break omitted intentionally
            case "versandpakete_list":
                $allowed['versandpakete_list'] = array('list');
                $heading = array('',  '',  'Paket-Nr.','Datum','Adresse', 'Lieferung', 'Versandart', 'Tracking', 'Menge auf Lieferscheinen', 'Menge', 'Gewicht','Versender', 'Bemerkung', 'Status', 'Monitor', 'Men&uuml;', '');
                $width = array(  '1%','1%','1%',       '1%',   '10%',     '1%',           '2%',         '2%',       '1%',                       '1%',    '1%',     '2%',        '10%',       '1%',     '1%',      '1%',        '1%');

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('id','id','id','datum','name','lieferscheine','versandart','tracking','lmenge','vmenge','gewicht','versender','bemerkung','status','id','id');
                $searchsql = array('name', 'if (lieferscheine IS NULL, lieferscheine_ohne_pos, lieferscheine)', 'tracking', 'bemerkung');

                $defaultorder = 1;
                $defaultorderdesc = 0;
                $aligncenter = [9,10,11];

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',id,'\" />') AS `auswahl`";

                $menu = "";              
                $menucol = 1;     
                $moreinfo = true; // Allow drop down details        

                $menu_link = array(
                    '<a href="index.php?module=versandpakete&action=edit&id=',
                    ['sql' => 'id'],
                    $lieferung_link,
                    '">',
                    "<img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>",
                    '</a>',
                    "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=versandpakete&action=delete&id=",
                    ['sql' => 'id'],
                    "\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>"
                );

                $lieferschein_link = array(
                    '<a href="index.php?module=lieferschein&action=edit&id=',
                    ['sql' => 'l.id'],
                    '">',
                    ['sql' => 'l.belegnr'],
                    '</a>'     
                );

                $lieferung_link = array(
                    '<a href="index.php?module=versandpakete&action=lieferung&id=',
                    ['sql' => 'l.id'],
                    '">',
                    ['sql' => 'l.belegnr'],
                    '</a>'     
                );

                $lieferung_ohne_pos_link = array(
                    '<a href="index.php?module=versandpakete&action=lieferung&id=',
                    ['sql' => 'lop.id'],
                    '">',
                    ['sql' => 'lop.belegnr'],
                    '</a>'     
                );

                $tracking_link = array(
                    '<a href="',
                    ['sql' => 'tracking_link'],
                    '">',
                    ['sql' => 'tracking'],
                    '</a>'     
                );                

                $sql_lieferschein_mengen = "
                    SELECT
                        l.id,
                        l.belegnr,
                        l.adresse,
                        l.name,
                        SUM(lp.menge) lmenge
                    FROM
                        lieferschein_position lp
                    INNER JOIN 
                        lieferschein l ON l.id = lp.lieferschein
                    INNER JOIN 
                        artikel a ON a.id = lp.artikel
                    WHERE 
                        a.lagerartikel
                    GROUP BY l.id
                ";

                $sql_pakete_zu_lieferschein = "
                    SELECT 
                        v.id,                       
                        v.datum,
                        v.lieferschein_ohne_pos,                            
                        v.versandart,
                        v.tracking_link,                                                        
                        v.tracking,                                                
                        v.gewicht,
                        v.versender,
                        v.bemerkung,
                        v.status,
                        lp.lieferschein,
                        SUM(vlp.menge) AS vmenge
                    FROM 
                        versandpakete v
                    LEFT JOIN 
                        versandpaket_lieferschein_position vlp ON vlp.versandpaket = v.id                       
                    LEFT JOIN
                        lieferschein_position lp ON vlp.lieferschein_position = lp.id
                    GROUP BY v.id, lp.lieferschein
                ";

                $sql = "
                    SELECT SQL_CALC_FOUND_ROWS
                        id,
                        ".$dropnbox.",                       
                        id id2, 
                        ".$app->erp->FormatDateTimeShort("datum").",
                        name,
                        lieferscheine,
                        versandart,
                        ".$app->erp->ConcatSQL($tracking_link)." AS tracking,
                        lmenge,
                        vmenge,
                        gewicht,
                        versender,
                        bemerkung,
                        status,
                        ".$app->YUI->IconsSQL_versandpaket()." icons,
                        ".$app->erp->ConcatSQL($menu_link)." AS paket_edit,
                        id,
                        id
                    FROM (
                        SELECT
                            v.id,
                            v.datum,
                            if(lop.id IS NOT NULL,lop.name,l.name) AS name,
                            if(lop.id IS NOT NULL,".$app->erp->ConcatSQL($lieferung_ohne_pos_link).", GROUP_CONCAT(".$app->erp->ConcatSQL($lieferung_link)." SEPARATOR ', ')) AS lieferscheine,
                            v.versandart,
                            tracking,
                            tracking_link,
                            ".$app->erp->FormatMenge("SUM(l.lmenge)")." AS lmenge,
                            ".$app->erp->FormatMenge("SUM(v.vmenge)")." AS vmenge,
                            v.gewicht,
                            v.versender,
                            v.bemerkung,
                            v.status,
                            lieferschein_ohne_pos
                        FROM
                            (".$sql_pakete_zu_lieferschein.") AS v
                        LEFT JOIN 
                            (".$sql_lieferschein_mengen.") as l
                            ON v.lieferschein = l.id
                        LEFT JOIN
                            lieferschein lop ON lop.id = v.lieferschein_ohne_pos
                            ".$lieferschein_filter_where."                                              
                        GROUP BY
                            v.id
                    ) final
                ";

                if (!$lieferschein_filter) {                    
                    $where = "((status IN ('neu', 'versendet')";       
                    // Toggle filters
                    $app->Tpl->Add('JQUERYREADY', "$('#geschlossene').click( function() { fnFilterColumn1( 0 ); } );");
                    $app->Tpl->Add('JQUERYREADY', "$('#stornierte').click( function() { fnFilterColumn2( 0 ); } );");

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
                       $where .= "  OR status IN ('abgeschlossen'))";
                    } else {
                       $where .= "  )";
                    }

                    $more_data2 = $app->Secure->GetGET("more_data2");
                    if ($more_data2 == 1) {
                      $where .= " OR status IN ('storniert'))";
                    }
                    else {
                       $where .= "  )";
                    }                
                    // END Toggle filters   

            
                } else {
                    $where = "1";
                }

                $count = "SELECT count(DISTINCT id) FROM versandpakete v WHERE ".$where;

                $groupby = "";
                break;
            case "versandpakete_lieferscheine":

                $allowed['versandpakete_lieferscheine'] = array('lieferscheine');
                
                $heading = array('',  '',       'Lieferschein', 'Adresse','Menge','Menge in Versandpaketen','Projekt','Monitor','Pakete','Paket hinzuf&uuml;gen');
                $width = array(  '1%','1%',     '10%',          '10%',    '10%',  '10%',                    '5%',      '1%',     '1%',    '1%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('id','id','belegnr','name','lmenge','vmenge','projekt','(alle_versendet+alle_abgeschlossen*2)','id','id');
                $searchsql = array('belegnr','name');

                $defaultorder = 1;
                $defaultorderdesc = 0;

                $menucol = 1;             
                $moreinfoaction = "lieferschein";
                $moreinfo = true; // Allow drop down details        
                $aligncenter = [5,6,7,8,9,10];

                $menu = "<a href=\"index.php?module=versandpakete&action=add&lieferschein=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/add.png\" border=\"0\"></a>";                

                $sql = Versandpakete::versandpakete_lieferstatus_sql($app);

                $where = "";

                // Toggle filters
                $app->Tpl->Add('JQUERYREADY', "$('#geschlossene').click( function() { fnFilterColumn1( 0 ); } );");
                $app->Tpl->Add('JQUERYREADY', "$('#unterwegs').click( function() { fnFilterColumn2( 0 ); } );");
             
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
                   $where = "1";
                } else {
                   $where = "(!alle_abgeschlossen)";
                }
                $more_data2 = $app->Secure->GetGET("more_data2");
                if ($more_data2 == 1) {
                   $where .= "  AND (alle_versendet | eins_versendet)";
                } else {
                }

                // END Toggle filters



//                $count = "SELECT count(DISTINCT id) FROM versandpakete v WHERE $where";
                $groupby = "";

                break;
            case "versandpakete_paketinhalt_list":

                $id = (int) $app->Secure->GetGET('id');

                $allowed['versandpakete_paketinhalt_list'] = array('list');
                $heading = array('Lieferschein','Pos', 'Artikel', 'Artikel-Nr.','Menge Lieferschein', 'Menge Paket', 'Men&uuml;','');
                $width = array(  '10%',         '10%', '10%',     '10%',        '1%',                 '1%',          '1%',       '1%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('l.belegnr','sort','a.name_de','a.nummer','lp.menge', 'vlp.menge', 'l.belegnr', 'l.belegnr');
                $searchsql = array('v.versand', 'v.nr', 'v.tracking', 'v.versender', 'v.gewicht', 'v.bemerkung', 'v.status');

                $defaultorder = 1;
                $defaultorderdesc = 0;
    
                $paket_link = array(
                    '<a href="index.php?module=versandpakete&action=edit&id=',
                    ['sql' => 'vlp.versandpaket'],
                    '">',
                    ['sql' => 'vlp.versandpaket'],
                    '</a>'     
                );

//                $menu = "<a href=\"index.php?module=versandpakete&action=deletepos&pos=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";
                $menu = "";
                $menucol = 6;         
                $aligncenter = [5,6,7];    

                $lieferschein_link = array(
                    '<a href="index.php?module=lieferschein&action=edit&id=',
                    ['sql' => 'l.id'],
                    '">',
                    ['sql' => 'l.belegnr'],
                    '</a>'     
                );

                $delete_link = array(
                    '<a href="index.php?module=versandpakete&action=deletepos&id=',
                    ['sql' => 'v.id'],
                    '&pos=',
                    ['sql' => 'vlp.id'],
                    '">',
                    "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">",
                    '</a>'     
                );
             
                $sql = "SELECT SQL_CALC_FOUND_ROWS
                        lp.id,
                        ".$app->erp->ConcatSQL($lieferschein_link)." as lieferschein, 
                        lp.sort,
                        a.name_de,
                        a.nummer,
                        ".$app->erp->FormatMenge('lp.menge')." as l_menge,
                        ".$app->erp->FormatMenge('SUM(vlp.menge)')." as v_menge,
                        ".$app->erp->ConcatSQL($delete_link)." as delete_link,
                        vlp.id
                    FROM
                        versandpakete v
                    INNER JOIN versandpaket_lieferschein_position vlp ON
                        v.id = vlp.versandpaket
                    INNER JOIN lieferschein_position lp ON
                        vlp.lieferschein_position = lp.id 
                    INNER JOIN lieferschein l ON
                        lp.lieferschein = l.id                    
                    INNER JOIN artikel a ON
	                    lp.artikel = a.id
                        ";

                $where = "v.id =".$id;
//                $count = "SELECT count(DISTINCT id) FROM versandpakete v WHERE $where";
                $groupby = "GROUP BY lp.id";
                break;
            case "versandpakete_lieferschein_paket_list":

                $lieferschein_id = $app->User->GetParameter('versandpakete_lieferschein');

                $allowed['versandpakete_lieferschein_paket_list'] = array('list');
                $heading = array('Pos', 'Artikel', 'Artikel-Nr.','Menge Lieferschein', 'Menge in Versandpaketen', 'Paket-Nr.','');
                $width = array('10%','10%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('sort','name_de','a.nummer','lp.menge','vlp.menge','versandpaket');
                $searchsql = array('v.versand', 'v.nr', 'v.tracking', 'v.versender', 'v.gewicht', 'v.bemerkung', 'v.status');

                $defaultorder = 1;
                $defaultorderdesc = 0;
    
                $menu = "";
                $menucol = 6;       
                $aligncenter = [4,5];      

                $paket_link = array(
                    '<a href="index.php?module=versandpakete&action=edit&id=',
                    ['sql' => 'vlp.versandpaket'],
                    '">',
                    ['sql' => 'vlp.versandpaket'],
                    '</a>'     
                );

                $sql = "SELECT SQL_CALC_FOUND_ROWS
                        vlp.id,
                        lp.sort,
                        a.name_de,
                        a.nummer,
                        ".$app->erp->FormatMenge('lp.menge')." as l_menge,
                        ".$app->erp->FormatMenge('SUM(vlp.menge)')." as v_menge,
                        GROUP_CONCAT(".$app->erp->ConcatSQL($paket_link)." SEPARATOR ', ') as pakete,
                        vlp.id
                    FROM lieferschein l
                    INNER JOIN lieferschein_position lp ON lp.lieferschein = l.id
                    INNER JOIN artikel a ON lp.artikel = a.id
                    LEFT JOIN versandpaket_lieferschein_position vlp ON vlp.lieferschein_position = lp.id
                        ";

                $where = "l.id =".$lieferschein_id." AND a.lagerartikel";
//                $count = "SELECT count(DISTINCT id) FROM versandpakete v WHERE $where";
//                $groupby = "GROUP BY lp.id";
                $groupby = "GROUP BY lp.id";
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

    function versandpakete_menu() {
        $this->app->erp->MenuEintrag("index.php?module=versandpakete&action=lieferungen", "Lieferungen");
        $this->app->erp->MenuEintrag("index.php?module=versandpakete&action=list", "Versandpakete");
    }
    
    function versandpakete_status_select() {
        // Status select
        $options_text = "";
        foreach (self::STATUS as $status)
        {
            $options_text .= "<option value=\"".$status."\">".$status."</option>";
        }
        $this->app->Tpl->Set('STATUS_OPTIONS', $options_text);
    }

    function versandpakete_list() {           
        $this->versandpakete_menu();
        $this->app->erp->MenuEintrag("index.php?module=versandpakete&action=create", "Neu anlegen");
        $this->versandpakete_status_select();
        $this->app->YUI->TableSearch('TAB1', 'versandpakete_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "versandpakete_list.tpl");
    }    

    function versandpakete_stapelverarbeitung() {
        // Process multi action
        $auswahl = $this->app->Secure->GetPOST('auswahl');
        $selectedIds = [];
        if(!empty($auswahl)) {
          foreach($auswahl as $selectedId) {
            $selectedId = (int)$selectedId;
            if($selectedId > 0) {
              $selectedIds[] = $selectedId;
            }
          }          
          $status = $this->app->Secure->GetPOST('status');          
          $sql = "UPDATE versandpakete SET status = '".$status."'";
          $sql .= " WHERE id IN (".implode(",",$selectedIds).")";
          $this->app->DB->Update($sql);
        }     

        $from = $this->app->Secure->GetGET('from');        
        if ($from == "lieferung") {
            $this->versandpakete_lieferung();
        }
        else {
            $this->versandpakete_list();
        }
    }

    function versandpakete_lieferungen() {
        $this->versandpakete_menu();
        $this->app->YUI->TableSearch('TAB1', 'versandpakete_lieferscheine', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->SetText('KURZUEBERSCHRIFT2', 'Lieferungen');
        $this->app->Tpl->Parse('PAGE', "versandpakete_lieferungen.tpl");
    }    

    function versandpakete_lieferung() {
        $lieferschein_filter = (int) $this->app->Secure->GetGET('id');        
        $this->versandpakete_menu();
        $this->app->erp->MenuEintrag("index.php?module=versandpakete&action=add&lieferschein=".$lieferschein_filter, "Neu anlegen");
        $this->app->erp->MenuEintrag("index.php?module=versandpakete&action=lieferung&id=".$lieferschein_filter, "Details");        
        $this->app->User->SetParameter('versandpakete_lieferschein_filter',$lieferschein_filter);
        $this->versandpakete_status_select();

        $sql = "SELECT 
                    belegnr,
                    l.name
                FROM 
                    lieferschein l
                WHERE l.id = ".$lieferschein_filter." LIMIT 1";

        $info = $this->app->DB->SelectArr($sql);

        if (!empty($info)) {
            $this->app->Tpl->Set('BELEGNR', $info[0]['belegnr']);
            $this->app->Tpl->Set('BELEGID', $lieferschein_filter);
            $this->app->Tpl->SetText('KURZUEBERSCHRIFT2', $info[0]['name']." Lieferung ".$info[0]['belegnr']);

            $complete = $this->versandpakete_check_completion($lieferschein_filter, null);
            if ($complete === true) {
                $this->app->Tpl->addMessage('success', 'Lieferung vollst&auml;ndig in Paketen.', false, 'MESSAGE');
            }
            else {
                $this->app->Tpl->addMessage('info', 'Lieferung unvollst&auml;ndig.', false, 'MESSAGE');
            }
        }
   
        $this->app->Tpl->Set('FROMID', $lieferschein_filter);

        $this->app->YUI->TableSearch('TAB1', 'lieferung_versandpakete_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "versandpakete_lieferung.tpl");
    }    

    public function versandpakete_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("UPDATE `versandpakete` SET status='storniert' WHERE `id` = '{$id}' AND `status` IN ('neu', 'versendet')");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde storniert.</div>");        

        $this->versandpakete_list();
    } 
 
    public function versandpakete_deletepos() {
        $id = (int) $this->app->Secure->GetGET('id');
        $pos = (int) $this->app->Secure->GetGET('pos');
        
        $sql = "DELETE vlp FROM `versandpaket_lieferschein_position` vlp INNER JOIN versandpakete v ON vlp.versandpaket = v.id WHERE vlp.`id` = '{$pos}' AND v.status = 'neu'";

        $this->app->DB->Delete($sql);        
        $this->app->Location->execute("Location: index.php?module=versandpakete&action=edit&id=".$id);
    } 

    /*
     * Edit versandpakete item
     * If id is empty, create a new one
     */
        
    function versandpakete_edit() {

        $id = $this->app->Secure->GetGET('id');
        $lieferung = $this->app->Secure->GetGET('lieferung');

        if ($lieferung) {
            $this->app->erp->MenuEintrag("index.php?module=versandpakete&action=lieferung&id=".$lieferung, "Zur&uuml;ck");        
        }

        $this->versandpakete_menu();
        $this->app->erp->MenuEintrag("index.php?module=versandpakete&action=edit&id=".$id, "Details");        

        // Check if other users are editing this id
        if($this->app->erp->DisableModul('versandpakete',$id))
        {
          return;
        }   
              
        $this->app->Tpl->Set('ID', $id);
        $input = $this->GetInput();
        $submit = $this->app->Secure->GetPOST('submit');
                
        if (empty($id)) {
            // New item
            $new_item = true;
            $id = 'NULL';
            $sql = "INSERT INTO versandpakete (status, versender) VALUES ('neu','".$this->app->User->GetName()."')";
            $this->app->DB->Insert($sql);
            $id = $this->app->DB->GetInsertId();                
            $this->app->Location->execute("Location: index.php?module=versandpakete&action=edit&id=".$id);
        } 

        // Check versandart
        $sql = "UPDATE versandpakete SET versandart = (SELECT versandart FROM (".self::SQL_VERSANDPAKETE_LIEFERSCHEIN.") v INNER JOIN lieferschein l ON v.lieferschein = l.id WHERE v.versandpaket = ".$id." LIMIT 1) WHERE id = ".$id;
        $this->app->DB->Update($sql);

        switch ($submit) {
            case 'speichern':
                // Write to database                
                // Add checks here

                $sql = "SELECT * FROM versandpakete WHERE id = ".$id;
                $paket_db = $this->app->DB->SelectRow($sql);
                $input['status'] = $paket_db['status']; // Status is not changeable
                if ($input['status'] != 'neu') { 
                    $input = Array('bemerkung' => $input['bemerkung']);
                } 

                if (!empty($paket_db['tracking'])) { // Tracking is not changeable
                    unset($input['tracking']);
                    unset($input['tracking_link']);
                }

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
                $sql = "INSERT INTO versandpakete (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;
                $this->app->DB->Insert($sql);
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            break;            
            case 'absenden':
                $sql = "UPDATE versandpakete SET status = 'versendet', versender = '".$this->app->User->GetName()."' WHERE id = ".$id;
                $this->app->DB->Update($sql);
            break;
            case 'abschliessen':
                $sql = "UPDATE versandpakete SET status = 'abgeschlossen' WHERE id = ".$id;
                $this->app->DB->Update($sql);
            break;
        }
         
        // Load values again from database
	    $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',v.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS v.id, $dropnbox, ".$this->app->erp->FormatDate('datum')." as datum, v.versand, v.versandart, v.nr, v.tracking, v.tracking_link, v.versender, v.gewicht, v.bemerkung, v.status, v.id FROM versandpakete v"." WHERE id=$id");        

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);   
        }
       
        // Check for only one delivery adress
        $this->app->YUI->AutoComplete("lieferschein", "lieferschein");
        $sql = "SELECT DISTINCT a.name, l.adresse FROM (".self::SQL_VERSANDPAKETE_LIEFERSCHEIN.") vpl INNER JOIN lieferschein l ON vpl.lieferschein = l.id  INNER JOIN adresse a ON l.adresse = a.id WHERE vpl.versandpaket = ".$id;
        $adress_check = $this->app->DB->SelectArr($sql);
        if (!empty($adress_check)) {
            if (count($adress_check) != 1) {
                // More than one adress for the packet -> error
            } else {
                $this->app->Tpl->Set('ADRESSE', $adress_check[0]['name']);   
                $this->app->YUI->AutoComplete("lieferschein", "kundenlieferschein",0,"&adresse=".$adress_check[0]['adresse']);
            }
        } 
        if ($new_item) {
            $this->app->Tpl->Set('LIEFERSCHEIN_POS_HIDDEN', 'hidden');      
        }
        $sql = "SELECT 
                    lieferschein_ohne_pos,
                    l.belegnr,
                    lop.belegnr lieferschein_ohne_pos_belegnr,
                    GROUP_CONCAT(lieferschein_position) lieferschein_position,
                    l.versandart
                FROM versandpakete v 
                LEFT JOIN lieferschein l ON v.lieferschein_ohne_pos = l.id 
                LEFT JOIN versandpaket_lieferschein_position vlp ON vlp.versandpaket = v.id 
                LEFT JOIN lieferschein lop ON lop.id = v.lieferschein_ohne_pos
                WHERE v.id = ".$id."
                GROUP BY v.id
                ";
        $lieferschein_check = $this->app->DB->SelectArr($sql);  

        if (empty($lieferschein_check[0]['lieferschein_position']) && empty($lieferschein_check[0]['lieferschein_ohne_pos'])) {
            $this->app->Tpl->Set('NO_ADDRESS_HIDDEN', 'hidden');                            
            $this->app->Tpl->Set('PAKETMARKE_ADD_HIDDEN', 'hidden');  
        }
        if (empty($lieferschein_check[0]['lieferschein_ohne_pos'])) {
            $this->app->Tpl->Set('LIEFERSCHEIN_OHNE_POS_HIDDEN', 'hidden');                
        }
        if (!empty($lieferschein_check[0]['lieferschein_ohne_pos'])) {         
            $this->app->Tpl->Set('LIEFERSCHEIN_OHNE_POS', $lieferschein_check[0]['lieferschein_ohne_pos_belegnr']);      
            $this->app->Tpl->Set('LIEFERSCHEIN_OHNE_POS_ID', $lieferschein_check[0]['lieferschein_ohne_pos']); 
        } 
        if (empty($lieferschein_check[0]['lieferschein_position'])) {
            $this->app->Tpl->Set('LIEFERSCHEIN_POS_HIDDEN', 'hidden');      
        }
        if ($result[0]['status'] != 'neu') {
            $this->app->Tpl->Set('LIEFERSCHEIN_ADD_POS_HIDDEN', 'hidden');
            $this->app->Tpl->Set('LIEFERSCHEIN_GEWICHT_DISABLED', 'disabled');
            $this->app->Tpl->Set('PAKETMARKE_ADD_HIDDEN', 'hidden');
            $this->app->Tpl->Set('TRACKING_DISABLED', 'disabled');
            $this->app->Tpl->Set('TRACKING_LINK_EDIT_HIDDEN', 'hidden');   
        }
        if ($result[0]['status'] != 'versendet') {
            $this->app->Tpl->Set('ABSCHLIESSEN_HIDDEN', 'hidden');
        } else {
             $this->app->Tpl->Set('ABSENDEN_HIDDEN', 'hidden');
        }
        if (!empty($result[0]['tracking'])) {
             $this->app->Tpl->Set('PAKETMARKE_ADD_HIDDEN', 'hidden');
        } else {         
             $this->app->Tpl->Set('ABSENDEN_HIDDEN', 'hidden');
        }
        if (empty($result[0]['tracking_link'])) {        
             $this->app->Tpl->Set('TRACKING_LINK_HIDDEN', 'hidden');
        }

        $versandart = $this->app->DB->SelectRow("SELECT va.id, va.modul FROM versandpakete vp INNER JOIN versandarten va ON vp.versandart = va.type WHERE vp.id = ".$id." LIMIT 1");
        if (empty($versandart['modul']) || empty($versandart['id'])) {
            $this->app->Tpl->Set('PAKETMARKE_ADD_HIDDEN', 'hidden');
        } else {
            $this->app->Tpl->Set('TRACKING_DISABLED', 'disabled');
            $this->app->Tpl->Set('TRACKING_LINK_EDIT_HIDDEN', 'hidden');    
        }        

        $file_attachments = $this->app->erp->GetDateiSubjektObjekt('paketmarke','versandpaket',$id);         
        if (!empty($file_attachments)) {
          foreach ($file_attachments as $file_attachment) {
              $this->app->Tpl->Add('PAKETMARKE_LINK', "index.php?module=dateien&action=send&id=".$file_attachment);
          }
        } else {
            $this->app->Tpl->Set('PAKETMARKE_HIDDEN', 'hidden');
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                            ".$this->app->YUI->IconsSQL_versandpaket()." icons 
                         FROM 
                            (
                            SELECT      
                                ".$this->app->erp->FormatMenge('SUM(lp.menge)')." as lmenge,
                                ".$this->app->erp->FormatMenge('SUM(vlp.menge)')." AS vmenge,
                                v.status,
                                v.lieferschein_ohne_pos,
                                GROUP_CONCAT(DISTINCT lieferschein SEPARATOR ', ') as lieferscheine,
                                tracking
                            FROM 
                                versandpakete v
                            LEFT JOIN 
                                versandpaket_lieferschein_position vlp ON vlp.versandpaket = v.id                       
                            LEFT JOIN
                                lieferschein_position lp ON vlp.lieferschein_position = lp.id                                             
                            WHERE v.id = ".$id."
                            GROUP BY v.id
                        ) temp                        
                        ";

        $icons = $this->app->DB->SelectArr($sql);
        $this->app->Tpl->Set('ICONS', $icons[0]['icons']);

        $complete = $this->versandpakete_check_completion(null, $id);
        if ($complete === true) {
            $this->app->Tpl->addMessage('success', 'Lieferung vollst&auml;ndig in Paketen.', false, 'MESSAGE');
        }
        else if ($complete === false) {
            $this->app->Tpl->addMessage('info', 'Lieferung unvollst&auml;ndig.', false, 'MESSAGE');
        }

        $this->app->YUI->TableSearch('PAKETINHALT', 'versandpakete_paketinhalt_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "versandpakete_edit.tpl");
    }

    function versandpakete_add() {     
        $id = $this->app->Secure->GetGET('id');
        $input = $this->GetInput();

        $this->versandpakete_menu();
        $this->app->erp->MenuEintrag("index.php?module=versandpakete&action=edit&id=".$id, "Details");        
        $this->app->Tpl->SetText('KURZUEBERSCHRIFT2', 'Artikel hinzuf&uuml;gen');
        if (empty($id)) { 

            $lieferschein = $this->app->Secure->GetGET('lieferschein'); 
            if (empty($lieferschein)) {               
                $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Kein Lieferschein angegeben.</div>");
                $this->app->Location->execute("Location: index.php?module=versandpakete&action=list&msg=$msg");
            } else {
                $lieferschein_belegnr = $this->app->erp->ReplaceLieferschein(false, $lieferschein, false); // Parameters: Target db?, value, from form?

                if (empty($lieferschein_belegnr)) {
                    $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Lieferschein ist nicht freigegeben.</div>");
                    $this->app->Location->execute("Location: index.php?module=versandpakete&action=list&msg=$msg");  
                }

                // Flag lieferschein for versand
                $sql = "UPDATE lieferschein SET versand_status = 1 WHERE id = ".$lieferschein." AND versand_status = 0";
                $this->app->DB->Update($sql);

                // Check if there is an unused paket waiting... 
                $sql = "SELECT 
                            v.id,
                            lieferschein_ohne_pos,
                            l.id AS lieferschein_mit_pos
                        FROM
                            versandpakete v 
                        LEFT JOIN versandpaket_lieferschein_position vlp ON vlp.versandpaket = v.id
                        LEFT JOIN lieferschein_position lp ON vlp.lieferschein_position = lp.id
                        LEFT JOIN lieferschein l on l.id = lp.lieferschein
                        WHERE
                            v.status = 'neu' AND
                            l.id IS NULL AND
                            v.lieferschein_ohne_pos = ".$lieferschein."
                            LIMIT 1
                ";
                $waiting_paket = $this->app->DB->SelectArr($sql);

                if (!empty($waiting_paket)) {
                    // Use existing
                    $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Versandpaket Paket Nr. ".$waiting_paket[0]['id']." wurde zugeordnet.</div>");
                    $this->app->Location->execute("Location: index.php?module=versandpakete&action=add&id=".$waiting_paket[0]['id']."&lieferschein=".$lieferschein."&msg=$msg");                    
                }
                else {
                    // Create new paket and add the given lieferschein
                    $sql = "INSERT INTO versandpakete (status, lieferschein_ohne_pos, versender) VALUES ('neu',".$lieferschein.",'".$this->app->User->GetName()."')";
                    $this->app->DB->Insert($sql);
                    $id = $this->app->DB->GetInsertId();  
                    $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Versandpaket Paket Nr. ".$id." wurde erstellt.</div>");
                    $this->app->Location->execute("Location: index.php?module=versandpakete&action=add&id=".$id."&lieferschein=".$lieferschein."&msg=$msg");
                }
            }
        } else { // $id not empty
            $lieferschein_post = $this->app->Secure->GetPOST('lieferschein');
            $lieferschein = $this->app->erp->ReplaceLieferschein(true, $lieferschein_post, true); // Parameters: Target db?, value, from form?           
            $lieferschein_belegnr = $this->app->erp->ReplaceLieferschein(false, $lieferschein_post, true); // Parameters: Target db?, value, from form?
            if (empty($lieferschein)) {     
                $lieferschein = $this->app->Secure->GetGET('lieferschein');          
                if (empty($lieferschein)) {     
                    $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Kein Lieferschein angegeben.</div>");
                    $this->app->Location->execute("Location: index.php?module=versandpakete&action=edit&id=".$id."&msg=$msg");
                }    
                $lieferschein_belegnr = $this->app->erp->ReplaceLieferschein(false, $lieferschein, false); // Parameters: Target db?, value, from form?
                $input['lieferschein'] = $lieferschein;
            } else {

            }
        }      

        $this->app->erp->SeriennummernCheckLieferscheinWarnung(lieferschein_id: $lieferschein);
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('versandpakete',$id))
        {
          return;
        }           

    	$artikel_input = $this->app->Secure->GetPOST('artikel');
        $artikel = $this->app->erp->ReplaceArtikel(true, $artikel_input,true); // Parameters: Target db?, value, from form?   
    	$menge = $this->app->Secure->GetPOST('menge');             
        $this->app->Tpl->Set('ID', $id);      
        $submit = $this->app->Secure->GetPOST('submit');                   
   
        // Check Status
        $sql = "SELECT status, lieferschein_ohne_pos FROM versandpakete WHERE id = ".$id." LIMIT 1";
        $result = $this->app->DB->SelectArr($sql);
        if ($result[0]['status'] != 'neu') {
            return;
        }      

        switch ($submit) {
            case 'hinzufuegen':    

                if ($menge <= 0) {
                    $msg = "<div class=\"error\">Falsche Mengenangabe.</div>";
                    break;
                }

                // Find a matching lieferschein_position
                $sql = "SELECT 
                            lp.id AS lp_id, 
                            MAX(lp.menge) AS lp_menge,
                            SUM(vlp.menge) AS v_menge
                        FROM lieferschein_position lp 
                        INNER JOIN artikel a ON lp.artikel = a.id
                        LEFT JOIN versandpaket_lieferschein_position vlp ON vlp.lieferschein_position = lp.id                         
                        WHERE lp.lieferschein = ".$lieferschein." AND lp.artikel = ".$artikel." AND a.lagerartikel
                        GROUP BY lp.id
                        ";

                $lieferschein_positionen = $this->app->DB->SelectArr($sql);        
                if (empty($lieferschein_positionen)) {
                    $msg = "<div class=\"error\">Keine passende Lieferscheinposition gefunden.</div>";
                    break;
                }

                $buchmenge_gesamt = 0;                                
                $buchmenge = 0;

                foreach($lieferschein_positionen as $lieferschein_position) {
                    $restmenge = $lieferschein_position['lp_menge']-$lieferschein_position['v_menge'];                
                    if ($restmenge <= 0 || $menge <= 0) {
                        continue;
                    }
                    if ($menge > $restmenge) {
                        $buchmenge = $restmenge;
                    } else {
                        $buchmenge = $menge;
                    }                   
                    $buchmenge_gesamt += $buchmenge;
                    $menge -= $buchmenge;

                    $sql = "INSERT INTO versandpaket_lieferschein_position (versandpaket, lieferschein_position, menge) VALUES (".$id.",".$lieferschein_position['lp_id'].",".$buchmenge.") ON DUPLICATE KEY UPDATE menge = menge+".$buchmenge."";
                    $this->app->DB->Insert($sql);      

                    if ($menge <= 0) {
                        break;
                    }

                }    

                if ($menge > 0) {
                    $msg = "<div class=\"error\">Menge wurde angepasst auf ".$buchmenge_gesamt.".</div>";
                }   

            break;
            case 'lieferschein_komplett_hinzufuegen':
                // Find all lieferschein_position
                $sql = "SELECT 
                            lp.id AS lp_id, 
                            MAX(lp.menge) AS lp_menge,
                            SUM(vlp.menge) AS v_menge
                        FROM lieferschein_position lp 
                        INNER JOIN artikel a ON lp.artikel = a. id
                        LEFT JOIN versandpaket_lieferschein_position vlp ON vlp.lieferschein_position = lp.id 
                        WHERE lp.lieferschein = ".$lieferschein." AND a.lagerartikel
                        GROUP BY lp.id
                        ";

                $lieferschein_positionen = $this->app->DB->SelectArr($sql);        
                if (empty($lieferschein_positionen)) {
                    $msg = "<div class=\"error\">Keine passende Lieferscheinposition gefunden.</div>";
                    break;
                }
                $buchmenge_gesamt = 0;
                foreach($lieferschein_positionen as $lieferschein_position) {
                    $buchmenge = $lieferschein_position['lp_menge']-$lieferschein_position['v_menge']; 
                    if ($buchmenge <= 0) {
                        continue;
                    }
                    $buchmenge_gesamt += $buchmenge;
                    $sql = "INSERT INTO versandpaket_lieferschein_position (versandpaket, lieferschein_position, menge) VALUES (".$id.",".$lieferschein_position['lp_id'].",".$buchmenge.") ON DUPLICATE KEY UPDATE menge = menge+'".$buchmenge."'";
                    $this->app->DB->Insert($sql);      
                }    

                if ($buchmenge_gesamt > 0) {
                    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Lieferschein hinzugef&uuml;gt.</div>");
                    $this->app->Location->execute("Location: index.php?module=versandpakete&action=edit&id=".$id."&msg=$msg");
                }
            break;
        }                 

        $this->app->Tpl->Set('LIEFERSCHEIN', $lieferschein_belegnr);
        $this->app->Tpl->Set('LIEFERSCHEIN_ID', $lieferschein);
        $this->app->Tpl->Set('VERSANDPAKET_ID', $id);

        $this->app->YUI->AutoComplete("artikel", "artikelnummerbeleg",0,"&doctype=lieferschein&doctypeid=".$lieferschein);

        // For transfer to tablesearch    
        $this->app->User->SetParameter('versandpakete_lieferschein', $lieferschein);
        $this->app->User->SetParameter('versandpakete_versandpaket', $id);

        $complete = $this->versandpakete_check_completion($lieferschein,null);
        if ($complete === true) {
            $this->app->Tpl->addMessage('success', 'Lieferung vollst&auml;ndig in Paketen.', false, 'MESSAGE');
        }
        else {
            $this->app->Tpl->addMessage('info', 'Lieferung unvollst&auml;ndig.', false, 'MESSAGE');
        }

        $this->app->YUI->TableSearch('LIEFERSCHEININHALT', 'versandpakete_lieferschein_paket_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->YUI->TableSearch('PAKETINHALT', 'versandpakete_paketinhalt_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Add('MESSAGE', $msg);
        $this->app->Tpl->Parse('PAGE', "versandpakete_add.tpl");
    }

    // null if versandpaket not associated with lieferschein
    function versandpakete_check_completion($lieferung, $versandpaket) : ?bool {

        if (!empty($lieferung)) {
            $sql_where_lieferung = " AND l.id = ".$lieferung;
        } else {   
            $sql_join_lieferschein = "
                INNER JOIN (
                    SELECT DISTINCT
                        lieferschein
                    FROM
                        versandpaket_lieferschein_position vlp
                    INNER JOIN lieferschein_position lp ON vlp.lieferschein_position = lp.id
                    WHERE versandpaket = ".$versandpaket."
                ) lieferschein_filter
                ON lieferschein_filter.lieferschein = lp.lieferschein
            ";
        }
       
        $sql_lieferschein_mengen = "
            SELECT 
                belegnr,
                lp.id lieferschein_position,
                menge lmenge
            FROM
                lieferschein_position lp
            INNER JOIN
                artikel a ON a.id = lp.artikel
            INNER JOIN
                lieferschein l on l.id = lp.lieferschein
            ".$sql_join_lieferschein."
            WHERE 
                a.lagerartikel
            ".$sql_where_lieferung."
            GROUP BY 
                lp.id
        ";

        // Check completion
        $sql_versandmengen = "
                SELECT 
                    v.id versandpaket,
                    lieferschein_position, 
                    SUM(menge) vmenge 
                FROM 
                    versandpaket_lieferschein_position vlp
                INNER JOIN
                    versandpakete v ON v.id = vlp.versandpaket
                WHERE
                    v.status <> 'storniert'                
                GROUP BY 
                    lieferschein_position
            ";

        $sql_intermediate = "
            SELECT 
                vmengen.versandpaket,
                GROUP_CONCAT(DISTINCT lmengen.belegnr SEPARATOR ', ') lieferscheine,
                SUM(lmenge) lmenge,
                SUM(vmenge) vmenge
            FROM
            (".$sql_lieferschein_mengen.") lmengen
            LEFT JOIN
            (".$sql_versandmengen.") vmengen
            ON lmengen.lieferschein_position = vmengen.lieferschein_position
        ";

        $sql = "
            SELECT
                *,
                ".$this->app->YUI->IconsSQL_versandpaket()." icons
            FROM
            (".$sql_intermediate.") final INNER JOIN versandpakete v ON v.id = final.versandpaket
        ";

        $completion = $this->app->DB->SelectArr($sql);

        if (!empty($completion)) {
            if ($completion[0]['lmenge'] == $completion[0]['vmenge']) {
                return(true);
            } else {
                return(false);
            }      
        } else {
            return(null);
        }
    }    

    function versandpakete_minidetail() {
        $id = $this->app->Secure->GetGET('id');        
        $table = new EasyTable($this->app);
        $table->Query("SELECT SQL_CALC_FOUND_ROWS
                        l.belegnr as Lieferschein, 
                        lp.sort as Pos,
                        a.name_de as Artikel,
                        a.nummer as `Artikel-Nr.`,
                        ".$this->app->erp->FormatMenge('lp.menge')." as `Menge Lieferschein`,
                        ".$this->app->erp->FormatMenge('SUM(vlp.menge)')." as `Menge Paket`
                    FROM
                        versandpakete v
                    INNER JOIN versandpaket_lieferschein_position vlp ON
                        v.id = vlp.versandpaket
                    INNER JOIN lieferschein_position lp ON
                        vlp.lieferschein_position = lp.id 
                    INNER JOIN lieferschein l ON
                        lp.lieferschein = l.id                    
                    INNER JOIN artikel a ON
	                    lp.artikel = a.id
                    WHERE vlp.versandpaket = ".$id."
                    GROUP BY
                        l.belegnr, lp.id
                    ORDER BY l.belegnr, lp.sort
                        ");
        $table->DisplayNew('TABLE', 'Menge Paket', 'noAction');
        $this->app->Tpl->Output('table.tpl');
        $this->app->ExitXentral();
    }        

    function versandpakete_minidetaillieferschein() {
        $id = $this->app->Secure->GetGET('id');        
        $table = new EasyTable($this->app);

        $paket_link = array(
            '<a href="index.php?module=versandpakete&action=edit&id=',
            ['sql' => 'versandpaket'],
            '">',
            ['sql' => 'versandpaket'],
            '</a>'     
        );

        $table->Query("
                            SELECT 
                                lp.sort AS Pos,
                                lp.bezeichnung,
                                ".$this->app->erp->FormatMenge('lp.menge')." AS 'Menge',
                                ".$this->app->erp->FormatMenge('SUM(vlp.menge)')." AS 'Menge in Versandpaketen',
                                GROUP_CONCAT(DISTINCT ".$this->app->erp->ConcatSQL($paket_link)." SEPARATOR ', ')
                            FROM 
                                lieferschein l 
                            INNER JOIN lieferschein_position lp ON lp.lieferschein = l.id
                            INNER JOIN artikel a ON lp.artikel = a.id
                            LEFT JOIN versandpaket_lieferschein_position vlp ON vlp.lieferschein_position = lp.id 
                            WHERE l.id = ".$id." AND a.lagerartikel                            
                            GROUP BY lp.id"
                    );
        $table->DisplayNew('TABLE', 'Paket', 'noAction');
        $this->app->Tpl->Output('table.tpl');
        $this->app->ExitXentral();
    }        


    function versandpakete_paketmarke()
      {
        $id = $this->app->Secure->GetGET('id');
        $this->app->erp->MenuEintrag("index.php?module=versandpakete&action=edit&id=".$id, "Zur&uuml;ck");
        $this->versandpakete_menu();

        $this->app->Tpl->Set('TABTEXT',"Paketmarke");

        $versandart = $this->app->DB->SelectRow("SELECT va.id, va.modul FROM versandpakete vp INNER JOIN versandarten va ON vp.versandart = va.type WHERE vp.id = ".$id." LIMIT 1");
        if (empty($versandart['modul']) || empty($versandart['id'])) {
            $this->app->Tpl->addMessage('error', 'Bitte zuerst eine gültige Versandart auswählen', false, 'PAGE');
            return;
        }
        $lieferschein = $this->app->DB->SelectRow("SELECT * FROM (".self::SQL_VERSANDPAKETE_LIEFERSCHEIN.") temp WHERE versandpaket = ".$id." LIMIT 1");
        $versandmodul = $this->app->erp->LoadVersandModul($versandart['modul'], $versandart['id']);
    
        $sql = "
            SELECT 
                SUM(COALESCE(a.gewicht,0)*vlp.menge)
            FROM
                artikel a
            INNER JOIN lieferschein_position lp ON
                a.id = lp.artikel
            INNER JOIN
                versandpaket_lieferschein_position vlp ON
                vlp.lieferschein_position = lp.id
            WHERE vlp.versandpaket = ".$id."
        ";

        $gewicht = $this->app->DB->Select($sql);       
        $versandmodul->Paketmarke('TAB1', docType: 'lieferschein', docId: $lieferschein['lieferschein'], versandpaket: $id, gewicht: $gewicht);
        $this->app->Tpl->Parse('PAGE',"tabview.tpl");
      }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
    	$input['gewicht'] = $this->app->Secure->GetPOST('gewicht');
    	$input['bemerkung'] = $this->app->Secure->GetPOST('bemerkung');
    	$input['tracking'] = $this->app->Secure->GetPOST('tracking');
    	$input['tracking_link'] = $this->app->Secure->GetPOST('tracking_link');
        return $input;
    }

    static function versandpakete_lieferstatus_sql($app) {

      $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',id,'\" />') AS `auswahl`";

      $sql_lieferschein_position = "
                            SELECT
                                l.id,
                                p.abkuerzung AS projekt,
                                l.belegnr,
                                l.name,
                                lp.menge lmenge,
                                SUM(vlp.menge) vmenge,
                                GROUP_CONCAT(vop.id) vop,
                                BIT_OR(COALESCE(v.status,0) IN ('versendet')) AS eins_versendet,
                                BIT_AND(COALESCE(v.status,0) IN ('versendet')) AS alle_versendet,
                                BIT_OR(COALESCE(v.status,0) IN ('abgeschlossen')) AS eins_abgeschlossen,
                                BIT_AND(COALESCE(v.status,0) IN ('abgeschlossen')) AS alle_abgeschlossen
                            FROM
                                lieferschein l
                            INNER JOIN lieferschein_position lp ON lp.lieferschein = l.id
                            LEFT JOIN versandpaket_lieferschein_position vlp ON vlp.lieferschein_position = lp.id
                            LEFT JOIN versandpakete v ON vlp.versandpaket = v.id
                            LEFT JOIN versandpakete vop ON vop.lieferschein_ohne_pos = l.id                            
                            LEFT JOIN projekt p ON p.id = l.projekt
                            WHERE
                                l.versand_status <> 0 AND
                                l.belegnr <> '' AND 
                                (v.status <> 'storniert' OR v.status IS NULL)
                            GROUP BY lp.id
                ";

                $sql_lieferschein = "
                    SELECT 
                        id,
                        projekt,
                        belegnr,
                        name,
                        SUM(lmenge) lmenge,
                        SUM(COALESCE(vmenge,0)) vmenge,
                        eins_versendet,
                        alle_versendet,
                        eins_abgeschlossen,
                        alle_abgeschlossen,
                        vop
                    FROM (
                        ".$sql_lieferschein_position."
                    ) lp
                    GROUP BY id
                ";

                $sql = "                        
                        SELECT SQL_CALC_FOUND_ROWS
                            id,
                            ".$dropnbox.",
                            CONCAT('<a href=\"index.php?module=lieferschein&action=edit&id=',id,'\">',belegnr,'</a>'),
                            name,
                            ".$app->erp->FormatMenge("lmenge").",
                            ".$app->erp->FormatMenge("vmenge").",
                            projekt,
                            ".$app->YUI->IconsSQL_lieferung().",  
                            if(vmenge > 0 OR vop IS NOT NULL,CONCAT('<a href=\"index.php?module=versandpakete&action=lieferung&id=',id,'\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\" title=\"Pakete anzeigen\" border=\"0\"></a>'),''),
                            id,
                            alle_abgeschlossen
                        FROM (
                            ".$sql_lieferschein."
                        ) l                        
                       ";
        return($sql);
    }

}
