<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Versandpakete {

    const VERSANDPAKETE_STATUS_NEU = 'neu';
    const VERSANDPAKETE_STATUS_VERSENDET = 'versendet';
    const VERSANDPAKETE_STATUS_ABGESCHLOSSEN = 'abgeschlossen';
    const VERSANDPAKETE_STATUS_STORNIERT = 'storniert';

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

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "versandpakete_list");        
        $this->app->ActionHandler("create", "versandpakete_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "versandpakete_edit");
        $this->app->ActionHandler("add", "versandpakete_add");
        $this->app->ActionHandler("lieferscheine", "versandpakete_lieferscheine");
        $this->app->ActionHandler("delete", "versandpakete_delete");
        $this->app->ActionHandler("minidetail", "versandpakete_minidetail");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "versandpakete_list":
                $allowed['versandpakete_list'] = array('list');
                $heading = array('','', 'Paket-Nr.','Datum','Adresse', 'Lieferschein', 'Tracking', 'Versender', 'Gewicht', 'Bemerkung', 'Status', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('v.id','v.id','v.id','a.name','l.belegnr','v.tracking', 'v.versender', 'v.gewicht', 'v.bemerkung', 'v.status');
                $searchsql = array('v.versand', 'v.nr', 'v.tracking', 'v.versender', 'v.gewicht', 'v.bemerkung', 'v.status');

                $defaultorder = 1;
                $defaultorderdesc = 0;

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',v.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=versandpakete&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=versandpakete&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";              
                $menucol = 11;     
                $moreinfo = true; // Allow drop down details        

                $sql = "SELECT SQL_CALC_FOUND_ROWS 
                            v.id,
                            $dropnbox,
                            v.id,
                            ".$app->erp->FormatDateTimeShort('v.datum').",
                            a.name,
                            GROUP_CONCAT(DISTINCT l.belegnr SEPARATOR ', ') as lieferschein,
                            v.tracking,
                            v.versender,
                            v.gewicht,
                            v.bemerkung,
                            v.status,
                            v.id 
                        FROM 
                            versandpakete v
                        LEFT JOIN 
                            (".self::SQL_VERSANDPAKETE_LIEFERSCHEIN.") vl ON v.id = vl.versandpaket
                        LEFT JOIN
                            lieferschein l on vl.lieferschein = l.id
                        LEFT JOIN
                            adresse a on a.id = l.adresse                            
                        ";

                $where = "";
//                $count = "SELECT count(DISTINCT id) FROM versandpakete v WHERE $where";
                $groupby = "GROUP BY v.id";

                break;
            case "versandpakete_lieferscheine":

                $allowed['versandpakete_lieferscheine'] = array('lieferscheine');
                
                $heading = array(   'Lieferschein', 'Adresse','Menge','Menge in Versandpaketen','Paket-Nr.', 'Paket erstellen');
                $width = array(     '10%',          '10%',    '10%',  '10%'                    ,'10%',    '1%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('id','id');
                $searchsql = array('v.versand', 'v.nr', 'v.tracking', 'v.versender', 'v.gewicht', 'v.bemerkung', 'v.status');

                $defaultorder = 1;
                $defaultorderdesc = 0;
    
                $menu = "<a href=\"index.php?module=versandpakete&action=add&lieferschein=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>";
                $menucol = 5;             

                $paket_link = array(
                    '<a href="index.php?module=versandpakete&action=edit&id=',
                    ['sql' => 'versandpaket'],
                    '">',
                    ['sql' => 'versandpaket'],
                    '</a>'     
                );

                $sql = "                        
                        SELECT
                            id,
                            CONCAT('<a href=\"index.php?module=lieferschein&action=edit&id=',id,'\">',belegnr,'</a>'),
                            name,
                            ".$app->erp->FormatMenge("SUM(lmenge)")." as lmenge,
                            ".$app->erp->FormatMenge("SUM(vmenge)")." as vmenge,
                            pakete,
                            id
                        FROM
                        (
                            SELECT
                                l.id,
                                l.belegnr,
                                l.name,
                                lp.menge lmenge,
                                SUM(vlp.menge) vmenge,
                                GROUP_CONCAT(".$app->erp->ConcatSQL($paket_link)." SEPARATOR ', ') as pakete
                            FROM
                                lieferschein l
                            INNER JOIN lieferschein_position lp ON lp.lieferschein = l.id
                            LEFT JOIN versandpaket_lieferschein_position vlp ON vlp.lieferschein_position = lp.id
                            LEFT JOIN versandpakete v ON vlp.versandpaket = v.id
                            WHERE
                                l.belegnr <> '' AND l.versendet <> 1 AND (v.status IS NULL OR v.status != '".self::VERSANDPAKETE_STATUS_STORNIERT."')
                            GROUP BY lp.id
                        ) l_mengen                        
                       ";

                $where = "";
//                $count = "SELECT count(DISTINCT id) FROM versandpakete v WHERE $where";
                $groupby = "GROUP BY id";

                break;
            case "versandpakete_paketinhalt_list":

                $id = (int) $app->Secure->GetGET('id');

                $allowed['versandpakete_paketinhalt_list'] = array('list');
                $heading = array('Lieferschein','Pos', 'Artikel', 'Artikel-Nr.','Menge Lieferschein', 'Menge Paket', 'Men&uuml;');
                $width = array('10%','10%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('lp.id','v.id','v.id','a.name','l.belegnr','v.tracking', 'v.versender', 'v.gewicht', 'v.bemerkung', 'v.status');
                $searchsql = array('v.versand', 'v.nr', 'v.tracking', 'v.versender', 'v.gewicht', 'v.bemerkung', 'v.status');

                $defaultorder = 1;
                $defaultorderdesc = 0;
    
                $menu = "<a href=\"index.php?module=versandpakete&action=deletepos&pos=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";
                $menucol = 6;             

                $lieferschein_link = array(
                    '<a href="index.php?module=lieferschein&action=edit&id=',
                    ['sql' => 'l.id'],
                    '">',
                    ['sql' => 'l.belegnr'],
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
                $heading = array('Pos', 'Artikel', 'Artikel-Nr.','Menge Lieferschein', 'Menge in Versandpaketen', 'Paket-Nr.', 'Men&uuml;');
                $width = array('10%','10%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array('lp.id','lp.id');
                $searchsql = array('v.versand', 'v.nr', 'v.tracking', 'v.versender', 'v.gewicht', 'v.bemerkung', 'v.status');

                $defaultorder = 1;
                $defaultorderdesc = 0;
    
                $menu = "";
                $menucol = 6;             

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
                        ".$app->erp->ConcatSQL($paket_link).",
                        vlp.id
                    FROM lieferschein l
                    INNER JOIN lieferschein_position lp ON
                        lp.lieferschein = l.id
                    INNER JOIN artikel a ON
	                    lp.artikel = a.id
                    LEFT JOIN versandpaket_lieferschein_position vlp ON vlp.lieferschein_position = lp.id
                        ";

                $where = "l.id =".$lieferschein_id;
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
        $this->app->erp->MenuEintrag("index.php?module=versandpakete&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=versandpakete&action=lieferscheine", "Offene Lieferscheine");
        $this->app->erp->MenuEintrag("index.php?module=versandpakete&action=create", "Neu anlegen");
        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");
    }
    
    function versandpakete_list() {    
        $this->versandpakete_menu();
        $this->app->YUI->TableSearch('TAB1', 'versandpakete_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "versandpakete_list.tpl");
    }    

    function versandpakete_lieferscheine() {
        $this->versandpakete_menu();
        $this->app->YUI->TableSearch('TAB1', 'versandpakete_lieferscheine', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "versandpakete_lieferscheine.tpl");
    }    

    public function versandpakete_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("UPDATE `versandpakete` SET status='".self::VERSANDPAKETE_STATUS_STORNIERT."' WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde storniert.</div>");        

        $this->versandpakete_list();
    } 
 
    /*
     * Edit versandpakete item
     * If id is empty, create a new one
     */
        
    function versandpakete_edit() {

        $this->versandpakete_menu();

        $id = $this->app->Secure->GetGET('id');
        
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
            $id = 'NULL';
            $input['status'] = self::VERSANDPAKETE_STATUS_NEU;
            $input['versender'] = $this->app->User->GetName();
        } 

        switch ($submit) {
            case 'speichern':
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
                $sql = "INSERT INTO versandpakete (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;
                $this->app->DB->Insert($sql);
                $id = $this->app->DB->GetInsertId();
                if ($id == 'NULL') {
                    $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                    header("Location: index.php?module=versandpakete&action=edit&id=".$id."&msg=$msg");
                } else {
                    $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
                }
            break;
        }
         
        // Load values again from database
	    $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',v.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS v.id, $dropnbox, ".$this->app->erp->FormatDate('datum')." as datum, v.versand, v.nr, v.tracking, v.versender, v.gewicht, v.bemerkung, v.status, v.id FROM versandpakete v"." WHERE id=$id");

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

        $sql = "SELECT lieferschein_ohne_pos FROM versandpakete WHERE id = ".$id;
        $lieferschein_ohne_pos = $this->app->DB->SelectArr($sql);
    
        if (!empty($lieferschein_ohne_pos[0]['lieferschein_ohne_pos'])) {
            $this->app->Tpl->Set('LIEFERSCHEIN_ADD_POS_HIDDEN', 'hidden');   
        }
        if ($result[0]['status'] != self::VERSANDPAKETE_STATUS_NEU) {
            $this->app->Tpl->Set('LIEFERSCHEIN_ADD_POS_HIDDEN', 'hidden');
            $this->app->Tpl->Set('LIEFERSCHEIN_POS_HIDDEN', 'hidden');      
        }

        $this->app->YUI->TableSearch('PAKETINHALT', 'versandpakete_paketinhalt_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "versandpakete_edit.tpl");
    }

    function versandpakete_add() {
      
        $this->versandpakete_menu();

        $id = $this->app->Secure->GetGET('id');
        if (empty($id)) { 
            $lieferschein = $this->app->Secure->GetGET('lieferschein'); 
            if (empty($lieferschein)) {               
                $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Kein Lieferschein angegeben.</div>");
                header("Location: index.php?module=versandpakete&action=list&msg=$msg");
            } else {
                $lieferschein_belegnr = $this->app->erp->ReplaceLieferschein(false, $lieferschein, false); // Parameters: Target db?, value, from form?
                /* Create new paket and add the given lieferschein */       
                $sql = "INSERT INTO versandpakete (status) VALUES ('".self::VERSANDPAKETE_STATUS_NEU."')"; 
                $this->app->DB->Insert($sql);
                $id = $this->app->DB->GetInsertId();  
            }
        } else { // $id not empty
            $lieferschein_input = $this->app->Secure->GetPOST('lieferschein');
            $lieferschein = $this->app->erp->ReplaceLieferschein(true, $lieferschein_input, true); // Parameters: Target db?, value, from form?    
            $lieferschein_belegnr = $this->app->erp->ReplaceLieferschein(false, $lieferschein_input, true); // Parameters: Target db?, value, from form?
            if (empty($lieferschein_input)) {            
                $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Kein Lieferschein angegeben.</div>");
                header("Location: index.php?module=versandpakete&action=edit&id=".$id."&msg=$msg");
            }
        }      
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('versandpakete',$id))
        {
          return;
        }           

    	$artikel_input = $this->app->Secure->GetPOST('artikel');
        $artikel = $this->app->erp->ReplaceArtikel(true, $artikel_input,true); // Parameters: Target db?, value, from form?   

    	$menge = $this->app->Secure->GetPOST('menge');             
        $this->app->Tpl->Set('ID', $id);      
        $input = $this->GetInput();
        $submit = $this->app->Secure->GetPOST('submit');                   
   
        // Check Status
        $sql = "SELECT status FROM versandpakete WHERE id = ".$id." LIMIT 1";
        $result = $this->app->DB->SelectArr($sql);
        if ($result[0]['status'] != self::VERSANDPAKETE_STATUS_NEU) {
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
                        LEFT JOIN versandpaket_lieferschein_position vlp ON vlp.lieferschein_position = lp.id 
                        WHERE lp.lieferschein = ".$lieferschein." AND lp.artikel = ".$artikel."
                        GROUP BY lp.id
                        ";

                $lieferschein_positionen = $this->app->DB->SelectArr($sql);        
                if (empty($lieferschein_positionen)) {
                    $msg = "<div class=\"error\">Keine passende Lieferscheinposition gefunden.</div>";
                    break;
                }

                $buchmenge_gesamt = 0;

                foreach($lieferschein_positionen as $lieferschein_position) {
                    $restmenge = $lieferschein_position['lp_menge']-$lieferschein_position['v_menge'];                
                    $buchmenge = $menge;
                    if ($restmenge <= 0) {
                        continue;
                    }
                    if ($menge > $restmenge) {
                        $buchmenge = $restmenge;
                        $menge -= $buchmenge;    
                    }

                    $sql = "INSERT INTO versandpaket_lieferschein_position (versandpaket, lieferschein_position, menge) VALUES (".$id.",".$lieferschein_position['lp_id'].",".$buchmenge.") ON DUPLICATE KEY UPDATE menge = '".$buchmenge."'";

                    $this->app->DB->Insert($sql);      
                    $buchmenge_gesamt += $buchmenge;
                }    

                if ($menge != $buchmenge_gesamt) {
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
                        LEFT JOIN versandpaket_lieferschein_position vlp ON vlp.lieferschein_position = lp.id 
                        WHERE lp.lieferschein = ".$lieferschein."
                        GROUP BY lp.id
                        ";

                $lieferschein_positionen = $this->app->DB->SelectArr($sql);        
                if (empty($lieferschein_positionen)) {
                    $msg = "<div class=\"error\">Keine passende Lieferscheinposition gefunden.</div>";
                    break;
                }

                foreach($lieferschein_positionen as $lieferschein_position) {
                    $buchmenge = $lieferschein_position['lp_menge']-$lieferschein_position['v_menge'];                
                    $sql = "INSERT INTO versandpaket_lieferschein_position (versandpaket, lieferschein_position, menge) VALUES (".$id.",".$lieferschein_position['lp_id'].",".$buchmenge.") ON DUPLICATE KEY UPDATE menge = '".$buchmenge."'";
                    $this->app->DB->Insert($sql);      
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

        $this->app->YUI->TableSearch('LIEFERSCHEININHALT', 'versandpakete_lieferschein_paket_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->YUI->TableSearch('PAKETINHALT', 'versandpakete_paketinhalt_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Set('MESSAGE', $msg);
        $this->app->Tpl->Parse('PAGE', "versandpakete_add.tpl");
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
     
    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['versand'] = $this->app->Secure->GetPOST('versand');
        $input['nr'] = $this->app->Secure->GetPOST('nr');
    	$input['tracking'] = $this->app->Secure->GetPOST('tracking');
	    $input['versender'] = $this->app->Secure->GetPOST('versender');
    	$input['gewicht'] = $this->app->Secure->GetPOST('gewicht');
    	$input['bemerkung'] = $this->app->Secure->GetPOST('bemerkung');
    	$input['status'] = $this->app->Secure->GetPOST('status');
	     	
        return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);        
        
        $this->app->Tpl->Set('VERSAND', $input['versand']);
	$this->app->Tpl->Set('NR', $input['nr']);
	$this->app->Tpl->Set('TRACKING', $input['tracking']);
	$this->app->Tpl->Set('VERSENDER', $input['versender']);
	$this->app->Tpl->Set('GEWICHT', $input['gewicht']);
	$this->app->Tpl->Set('BEMERKUNG', $input['bemerkung']);
	$this->app->Tpl->Set('STATUS', $input['status']);
	
    }

}
