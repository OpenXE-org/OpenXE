<?php

/*
 * Copyright (c) 2023 OpenXE project
 * Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class lieferantengutschrift {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "lieferantengutschrift_list");        
        $this->app->ActionHandler("create", "lieferantengutschrift_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "lieferantengutschrift_edit");
        $this->app->ActionHandler("positionen", "lieferantengutschrift_positionen");
        $this->app->ActionHandler("delete", "lieferantengutschrift_delete");
        $this->app->ActionHandler("deletepos", "lieferantengutschrift_deletepos");
        $this->app->ActionHandler("editpos", "lieferantengutschrift_editpos");
        $this->app->ActionHandler("dateien", "lieferantengutschrift_dateien");
        $this->app->ActionHandler("inlinepdf", "lieferantengutschrift_inlinepdf");
        $this->app->ActionHandler("positioneneditpopup", "lieferantengutschrift_positioneneditpopup");
        $this->app->ActionHandler("freigabe", "lieferantengutschrift_freigabe");
        $this->app->ActionHandler("freigabeeinkauf", "lieferantengutschrift_freigabeeinkauf");
        $this->app->ActionHandler("freigabebuchhaltung", "lieferantengutschrift_freigabebuchhaltung");
        $this->app->ActionHandler("freigabebezahlt", "lieferantengutschrift_freigabebezahlt");     
        $this->app->ActionHandler("ruecksetzeneinkauf", "lieferantengutschrift_ruecksetzeneinkauf");
        $this->app->ActionHandler("ruecksetzenbuchhaltung", "lieferantengutschrift_ruecksetzenbuchhaltung");
        $this->app->ActionHandler("ruecksetzenbezahlt", "lieferantengutschrift_ruecksetzenbezahlt");     
        $this->app->ActionHandler("minidetail", "lieferantengutschrift_minidetail");

        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "lieferantengutschrift_list":
                $allowed['lieferantengutschrift_list'] = array('list');
                $heading = array('','','Belegnr','Adresse', 'Lieferant', 'RE-Nr', 'RE-Datum', 'Betrag (brutto)', 'W&auml;hrung','Zahlstatus', 'Ziel','Skontoziel','Skonto','Status','Monitor', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); 

                $findcols = array(
                    'v.id',
                    'v.id',
                    'v.belegnr',
                    'a.name',
                    'a.lieferantennummer',                  
                    'v.rechnung',
                    'v.rechnungsdatum',
                    'v.betrag',
                    'v.waehrung',
                    'v.bezahlt',
                    'v.zahlbarbis',
                    'v.skontobis',
                    'v.skonto',
                    'v.status',
                    'v.status_beleg',
                    'v.id'
                );
               
                $searchsql = array(                    
                    'a.name',
                    'a.lieferantennummer',                  
                    'v.rechnung',
                    'v.internebemerkung'
                );

                $defaultorder = 1;
                $defaultorderdesc = 0;
                $alignright = array(8);
                $sumcol = array(8);

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',v.id,'\" />') AS `auswahl`";

//                $moreinfo = true; // Allow drop down details
//                $moreinfoaction = "lieferschein"; // specify suffix for minidetail-URL to allow different minidetails
//                $menucol = 11; // Set id col for moredata/menu

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=lieferantengutschrift&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=lieferantengutschrift&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS 
                            v.id,
                            $dropnbox,
                            v.belegnr,
                            a.name,
                            a.lieferantennummer,
                            v.rechnung,
                            ".$app->erp->FormatDate("v.rechnungsdatum").",
                            ".$app->erp->FormatMenge('v.betrag',2).",
                            v.waehrung,
                            if(v.bezahlt,'bezahlt','offen'),
                            ".$app->erp->FormatDate("v.zahlbarbis").",
                            IF(v.skonto <> 0,".$app->erp->FormatDate("v.skontobis").",''),                             
                            IF(v.skonto <> 0,CONCAT(".$app->erp->FormatMenge('v.skonto',0).",'%'),''), 
                            v.status,
                            ".$app->YUI->IconsSQLverbindlichkeit().",
                            v.id FROM lieferantengutschrift v
                        LEFT JOIN adresse a ON v.adresse = a.id
                        LEFT JOIN (
                            SELECT ds.parameter, COUNT(ds.objekt) datei_anzahl FROM datei_stichwoerter ds INNER JOIN datei d ON d.id = ds.datei WHERE ds.objekt='lieferantengutschrift' AND d.geloescht <> 1 GROUP BY ds.parameter
                        ) d ON d.parameter = v.id
                        ";
                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM lieferantengutschrift WHERE $where";
//                $groupby = "";

                // Toggle filters
                $this->app->Tpl->Add('JQUERYREADY', "$('#anhang').click( function() { fnFilterColumn1( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#wareneingang').click( function() { fnFilterColumn2( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#rechnungsfreigabe').click( function() { fnFilterColumn3( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#nichtbezahlt').click( function() { fnFilterColumn4( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#stornierte').click( function() { fnFilterColumn5( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#abgeschlossen').click( function() { fnFilterColumn6( 0 ); } );");

                for ($r = 1;$r <= 8;$r++) {
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
                   $where .= " AND datei_anzahl IS NULL";                 
                } else {
                }

                $more_data2 = $this->app->Secure->GetGET("more_data2");
                if ($more_data2 == 1) {
                   $where .= " AND v.freigabe <> '1'";
                }
                else {
                }                

                $more_data3 = $this->app->Secure->GetGET("more_data3");
                if ($more_data3 == 1) {
                   $where .= " AND v.rechnungsfreigabe <> '1'";
                }
                else {                  
                }                    
                
                $more_data4 = $this->app->Secure->GetGET("more_data4");
                if ($more_data4 == 1) {
                   $where .= " AND v.bezahlt <> 1";            
                }
                else {                  
                }                             

                $more_data5 = $this->app->Secure->GetGET("more_data5");
                if ($more_data5 == 1) {
                }
                else {                  
                   $where .= " AND v.status <> 'storniert'";            
                }    

                $more_data6 = $this->app->Secure->GetGET("more_data6");
                if ($more_data6 == 1) {
                }
                else {                  
                    $where .= " AND v.status <> 'abgeschlossen'";            
                }                                                                                         
                
                $this->app->YUI->DatePicker('zahlbarbis');
                $filterzahlbarbis = $this->app->YUI->TableSearchFilter($name, 7,'zahlbarbis');
                if (!empty($filterzahlbarbis)) {
                    $filterzahlbarbis = $this->app->String->Convert($filterzahlbarbis,'%1.%2.%3','%3-%2-%1');
                    $where .= " AND v.zahlbarbis <= '".$filterzahlbarbis."'";
                }

                $this->app->YUI->DatePicker('skontobis');
                $filterskontobis = $this->app->YUI->TableSearchFilter($name, 8,'skontobis');
                if (!empty($filterskontobis)) {
                    $filterskontobis = $this->app->String->Convert($filterskontobis,'%1.%2.%3','%3-%2-%1');
                    $where .= " AND v.skontobis <= '".$filterskontobis."'";
                }
                // END Toggle filters

                $moreinfo = true; // Allow drop down details
                $menucol = 1; // For moredata

                break;          
       case 'lieferantengutschrift_positionen':

                $allowed['lieferantengutschrift_positionen'] = array('list');              

                $id = $app->Secure->GetGET('id');
                $freigabe = $app->DB->Select("SELECT freigabe FROM lieferantengutschrift WHERE id = '".$id."'");
                $rechnungsfreigabe = $app->DB->Select("SELECT rechnungsfreigabe FROM lieferantengutschrift WHERE id = '".$id."'");

                $heading = array(''  ,'Verbindlichkeit', 'Artikel-Nr.','Artikel','Menge','Preis','Steuersatz','Sachkonto');
                $width = array(  '1%','1%',              '20%',        '20%',    '1%',        '1%',        '3%',       '1%',       '1%');       

                $findcols = array('lgp.id','v.belegnr','art.nummer','art.name_de','lgp.menge','lgp.preis','lgp.steuersatz',"CONCAT(skv.sachkonto,' ',skv.beschriftung)",'lgp.id');
                $searchsql = array('p.nummer', 'p.name', 'p.bemerkung');

                $alignright = array(8,9,10);                

                $defaultorder = 1;
                $defaultorderdesc = 0;     

                if (empty($freigabe)) {                                                 
                    $menu="<table cellpadding=0 cellspacing=0><tr><td nowrap>"."<a href=\"index.php?module=lieferantengutschrift&action=editpos&id=$id&posid=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=lieferantengutschrift&action=deletepos&id=$id&posid=%value%\");>"."<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>"."</td></tr></table>";
                } else if (empty($rechnungsfreigabe)) {
                    $menu="<table cellpadding=0 cellspacing=0><tr><td nowrap>"."<a href=\"index.php?module=lieferantengutschrift&action=editpos&id=$id&posid=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>"."</td></tr></table>";
                }
                else {
                    $deletepos = array('');
                }
                $heading[] = '';                      
              
        	    $box = "CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',lgp.id,'\" />') AS `auswahl`";

                $verbindlichkeitlink = array (
                    '<a href="index.php?module=verbindlichkeit&action=edit&id=',
                    ['sql' => 'v.id'],
                    '">',                    
                    ['sql' => 'v.belegnr'],
                    '</a>'
                );    

                $artikellink = array (
                    '<a href="index.php?module=artikel&action=edit&id=',
                    ['sql' => 'art.id'],
                    '">',                    
                    ['sql' => 'art.nummer'],
                    '</a>'
                );        

                $sql = "
                    SELECT SQL_CALC_FOUND_ROWS                          
                        lgp.id,
                        $box,
                        ".$this->app->erp->ConcatSQL($verbindlichkeitlink).",
                        ".$this->app->erp->ConcatSQL($artikellink).",
                        art.name_de,
                        lgp.menge,
                        lgp.preis,
                        lgp.steuersatz,
                        CONCAT(skv.sachkonto,' ',skv.beschriftung),
                        lgp.id
                    FROM
                        lieferantengutschrift_position lgp
                    INNER JOIN lieferantengutschrift lg ON
                        lg.id = lgp.lieferantengutschrift
                    LEFT JOIN verbindlichkeit_position vp ON
                        vp.id = lgp.verbindlichkeit_position
                    LEFT JOIN verbindlichkeit v ON
                        vp.verbindlichkeit = v.id
                    INNER JOIN artikel art ON
                        art.id = lgp.artikel
                    INNER JOIN adresse adr ON
                        adr.id = lg.adresse
                    LEFT JOIN kontorahmen skv ON skv.id = lgp.kontorahmen                    
                ";

                $where = "lgp.lieferantengutschrift = ".$id;

                $count = "";

                break;           
            case 'verbindlichkeit_positionen':
    
                $allowed['verbindlichkeit_positionen'] = array('list');              

                $id = $app->Secure->GetGET('id');
    
                //$verbindlichkeit = $app->DB->Select("SELECT verbindlichkeit FROM lieferantengutschrift WHERE id = '".$id."'");            

                $heading = array('',  'Verbindlichkeit',  'Artikel-Nr.','Artikel','Menge','Preis','Steuersatz','Sachkonto', '');
                $width = array(  '1%','2%',               '2%',         '20%',    '1%',   '1%',        '3%',       '1%',       '1%');       

                $findcols = array('vp.id','v.belegnr','art.nummer','art.name_de','vp.menge','vp.preis','vp.steuersatz',"CONCAT(skv.sachkonto,' ',skv.beschriftung)",'vp.id');
                $searchsql = array('p.nummer', 'p.name', 'p.bemerkung');

                $alignright = array(8,9,10);                

                $defaultorder = 1;
                $defaultorderdesc = 0;        

                $auswahl = array (
                    '<input type=\"checkbox\" name=\"ids[]\" value=\"',                   
                    ['sql' => 'vp.id'],
                    '"/>'
                );              

                $werte = array (
                    '<input type="number" name="werte[]" min="0"',
                    'max = "',
                    ['sql' => 'if(vp.menge > COALESCE(lgp.menge,0),vp.menge-COALESCE(lgp.menge,0),0)'],
                    '" ',
                    'value = "',
                    ['sql' => 'if(vp.menge > COALESCE(lgp.menge,0),vp.menge-COALESCE(lgp.menge,0),0)'],
                    '" ',
                    '/>'
                );       

                $preise = array (
                    '<input type="number" name="preise[]" step="0.00001" value="',
                    ['sql' => $this->app->erp->FormatMenge("COALESCE(bp.preis,0)",5)],
                    '" min="0"',                    
                    '/>'
                );         
                       
                $artikellink = array (
                    '<a href="index.php?module=artikel&action=edit&id=',
                    ['sql' => 'art.id'],
                    '">',                    
                    ['sql' => 'art.nummer'],
                    '</a>'
                );                        
              
        	    $box = "CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',vp.id,'\" />') AS `auswahl`";

                $sql = "
                    SELECT SQL_CALC_FOUND_ROWS                          
                        vp.id,
                        ".$this->app->erp->ConcatSQL($auswahl).",
                        v.belegnr,
                        ".$this->app->erp->ConcatSQL($artikellink).",
                        art.name_de,
                        ".$this->app->erp->ConcatSQL($werte).",
                        vp.preis,
                        vp.steuersatz,
                        CONCAT(skv.sachkonto,' ',skv.beschriftung),
                        vp.id
                    FROM
                        verbindlichkeit_position vp
                    INNER JOIN verbindlichkeit v ON
                        v.id = vp.verbindlichkeit
                    INNER JOIN paketdistribution pd ON
                        pd.id = vp.paketdistribution
                    INNER JOIN artikel art ON
                        art.id = pd.artikel
                    INNER JOIN adresse adr ON
                        adr.id = v.adresse
                    LEFT JOIN kontorahmen skv ON skv.id = vp.kontorahmen                    
                    LEFT JOIN (
                        SELECT 
                            verbindlichkeit_position,
                            SUM(menge) AS menge
                        FROM 
                            lieferantengutschrift_position lgp                         
                        INNER JOIN
                            lieferantengutschrift lg ON lg.id = lgp.lieferantengutschrift
                        WHERE 
                            lg.status <> 'storniert' AND verbindlichkeit_position <> 0 and verbindlichkeit_position IS NOT NULL
                    ) lgp ON lgp.verbindlichkeit_position = vp.id
                ";

                $where = "v.adresse = (SELECT adresse FROM lieferantengutschrift WHERE id = ".$id.") AND v.status IN ('freigegeben','abgeschlossen')";

                $count = "";

                break;
            case 'artikel_manuell':
                $allowed['paketdistribution_list'] = array('list');
         
                $heading = array('Art.-Nummer', 'Beschreibung', 'Menge', 'Preis','Steuer','Sachkonto','');
                $width = array(  '5%',          '30%',          '5%',    '5%',   '1%',    '1%',       '1%');

                $findcols = array('nummer','name_de','','','umsatzsteuer',"CONCAT(skart.sachkonto,' ',skart.beschriftung)",'id');
                $searchsql = array('');

                $alignright = array('5');
                $defaultorder = 1;
                $defaultorderdesc = 0;
                
                $auswahl = array (
                    '<input type=\"text\" name=\"manuell_artikel_ids[]\" value=\"',                   
                    ['sql' => 'a.id'],
                    '" hidden/>',
                    ['sql' => 'a.nummer']
                );              
                
                $input_for_menge = array(
                    '<input type = \"number\" min=\"0\"',
                    ' value=\"',                    
                    '\"',
                    ' name=\"manuell_mengen[]\"',
                    ' style=\"text-align:right; width:100%\">',
                    '</input>'
                );      

                $input_for_bemerkung = array(
                    '<input type = \"text\"',                    
                    ' name=\"manuell_bemerkungen[]\"',
                    ' style=\"text-align:right; width:100%\">',
                    '</input>'
                );      

                $preise = array (
                    '<input type="number" name="preise[]" step="0.00001" value="',
                    '" min="0"',                    
                    ' style=\"text-align:right; width:100%\">'                    
                );   

                $sql = "
                    SELECT SQL_CALC_FOUND_ROWS                       
                        a.id,
                        ".$this->app->erp->ConcatSQL($auswahl).",
                        name_de,
                        ".$this->app->erp->ConcatSQL($input_for_menge).",
                        ".$this->app->erp->ConcatSQL($preise)."
                        '',
                        a.umsatzsteuer,                        
                        CONCAT(skart.sachkonto,' ',skart.beschriftung)
                    FROM
                        artikel a
                    LEFT JOIN 
                        kontorahmen skart ON skart.id = a.kontorahmen";

                              
                $where = " (geloescht <> 1)";

                $multifilter = $this->app->YUI->TableSearchFilter($name, 8,'multifilter');
                if (!empty($multifilter)) {
                    $multifilter_array = explode(' ',$multifilter);
                    $where .= " AND (1=0";
                    foreach($multifilter_array as $keyword) {
                        $where .= " OR name_de LIKE '%".$keyword."%'";
                        $where .= " OR nummer LIKE '%".$keyword."%'";
                    }
                    $where .= ")";
                }
                $count = "";
         
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
    
    function lieferantengutschrift_list() {

        // Process multi action
        $submit = $this->app->Secure->GetPOST('submit');
        switch($submit) {
            case 'status_berechnen':
            
                $sql = "SELECT id FROM lieferantengutschrift WHERE status <> 'abgeschlossen' AND status <> 'storniert'";
                $ids = $this->app->DB->SelectArr($sql);
            
                foreach ($ids as $lieferantengutschrift) {
                    $this->lieferantengutschrift_abschliessen($lieferantengutschrift['id']);
                }
                                                      
            break;
            case 'ausfuehren':
                $auswahl = $this->app->Secure->GetPOST('auswahl');
                $aktion = $this->app->Secure->GetPOST('sel_aktion');
             
                $selectedIds = [];
                if(!empty($auswahl)) {
                    foreach($auswahl as $selectedId) {
                        $selectedId = (int)$selectedId;
                        if($selectedId > 0) {
                            $selectedIds[] = $selectedId;
                        }
                    }          

                    switch ($aktion) {
                        case 'freigabeeinkauf':
                            foreach ($selectedIds as $id) {
                                $result = $this->lieferantengutschrift_freigabeeinkauf($id);
                                if ($result !== true) {
                                    $this->app->YUI->Message('warning',$result);
                                }
                            }
                        break;
                        case 'freigabebuchhaltung':
                            foreach ($selectedIds as $id) {                            
                                $result = $this->lieferantengutschrift_freigabebuchhaltung($id);
                                if ($result !== true) {
                                    $this->app->YUI->Message('warning',$result);
                                }
                            }
                        break;
                        case 'bezahlt':
                            foreach ($selectedIds as $id) {
                                $result = $this->lieferantengutschrift_freigabebezahlt($id);
                                if ($result !== true) {
                                   $this->app->YUI->Message('warning',$result);
                                }
                            }
                        break;
                        case 'drucken':
                            $drucker = $this->app->Secure->GetPOST('seldrucker');
                            foreach ($selectedIds as $id) {
                                $file_attachments = $this->app->erp->GetDateiSubjektObjekt('%','lieferantengutschrift',$id);
                                if (!empty($file_attachments)) {
                                    foreach ($file_attachments as $file_attachment) {
                                        if ($this->app->erp->GetDateiEndung($file_attachment) == 'pdf') {
                                            $file_contents = $this->app->erp->GetDatei($file_attachment);
                                            $lieferantengutschrift = $this->app->DB->SelectRow("SELECT DATE_FORMAT(rechnungsdatum, '%Y%m%d') rechnungsdatum, belegnr FROM lieferantengutschrift WHERE id = ".$id." LIMIT 1");
                                            $file_name = $lieferantengutschrift['rechnungsdatum']."_LG".$lieferantengutschrift['belegnr'].".pdf";
                                            $file_path = rtrim($this->app->erp->GetTMP(),'/')."/".$file_name;
                                            $handle = fopen ($file_path, "wb");
                                            if ($handle)
                                            {
                                                fwrite($handle, $file_contents);
                                                fclose($handle);
                                                $this->app->printer->Drucken($drucker,$file_path);
                                            } else {
                                                $this->app->YUI->Message('error',"Drucken fehlgeschlagen!");
                                            }
                                        }
                                    }
                                }
                            }
                        break;
                    }    
                }         
            break;           
        }

        $this->app->erp->MenuEintrag("index.php?module=lieferantengutschrift&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=lieferantengutschrift&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'lieferantengutschrift_list', "show", "", "", basename(__FILE__), __CLASS__);


        if($this->app->erp->RechteVorhanden('lieferantengutschrift', 'freigabeeinkauf')){
            $this->app->Tpl->Set('MANUELLFREIGABEEINKAUF', '<option value="freigabeeinkauf">{|freigeben (Einkauf)|}</option>');
        }

        if($this->app->erp->RechteVorhanden('lieferantengutschrift', 'freigabebuchhaltung')){
            $this->app->Tpl->Set('MANUELLFREIGABEBUCHHALTUNG', '<option value="freigabebuchhaltung">{|freigeben (Buchhaltung)|}</option>');
        }

        if($this->app->erp->RechteVorhanden('lieferantengutschrift', 'freigabebezahlt')){
            $this->app->Tpl->Set('ALSBEZAHLTMARKIEREN', '<option value="bezahlt">{|als bezahlt markieren|}</option>');
        }

        $this->app->User->SetParameter('table_lieferantengutschrift_list_zahlbarbis', '');
        $this->app->User->SetParameter('table_lieferantengutschrift_list_skontobis', '');

        $this->app->Tpl->Set('SELDRUCKER', $this->app->erp->GetSelectDrucker());

        $this->app->Tpl->Parse('PAGE', "lieferantengutschrift_list.tpl");
    }    

    public function lieferantengutschrift_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("UPDATE `lieferantengutschrift` SET status='storniert' WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde storniert.</div>");        

        $this->lieferantengutschrift_list();
    } 

    public function lieferantengutschrift_deletepos() {
        $posid = (int) $this->app->Secure->GetGET('posid');
        $id = (int) $this->app->Secure->GetGET('id');            
        $lieferantengutschrift = $this->app->DB->Select("SELECT lieferantengutschrift FROM lieferantengutschrift_position WHERE id ='{$posid}'");
        $this->app->DB->Delete("DELETE vp FROM lieferantengutschrift_position vp INNER JOIN lieferantengutschrift v ON v.id = vp.lieferantengutschrift WHERE vp.id = '{$posid}' AND v.freigabe <> 1");        
        header("Location: index.php?module=lieferantengutschrift&action=edit&id=$id#tabs-2");
    } 

    /*
     * Edit lieferantengutschrift item
     * If id is empty, create a new one
     */
        
    function lieferantengutschrift_edit($einkauf_automatik_aus = false) {
        $id = $this->app->Secure->GetGET('id');
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('lieferantengutschrift',$id))
        {
          return;
        }   
              
        $this->app->Tpl->Set('ID', $id);

        $this->lieferantengutschrift_menu($id);

        $input = $this->GetInput();
        $submit = $this->app->Secure->GetPOST('submit');
                
        if (empty($id)) {
            // New item
            $id = 'NULL';
            $input['status'] = 'angelegt';
        } 

        if (!empty($submit)) {
            $einkauf_automatik_aus = false;
        }

        switch($submit)
        {
            case 'speichern':
                   // Write to database            
                // Add checks here

                $freigabe = $this->app->DB->SelectArr("SELECT rechnungsfreigabe, freigabe, adresse, belegnr FROM lieferantengutschrift WHERE id =".$id)[0];             

                if ($freigabe['rechnungsfreigabe'] || $freigabe['freigabe']) {
                    $internebemerkung = $input['internebemerkung'];
                    $projekt = $input['projekt'];
                    $kostenstelle = $input['kostenstelle'];
                    unset($input);
                    $input['internebemerkung'] = $internebemerkung;
                    $input['projekt'] = $this->app->erp->ReplaceProjekt(true,$projekt,true);
                    $input['kostenstelle'] = $this->app->DB->Select("SELECT id FROM kostenstellen WHERE nummer = '".$kostenstelle."'");
                } else {
                 
                    if ($freigabe['belegnr']) {
                        unset($input['adresse']);               
                    } else {
                        $input['adresse'] = $this->app->erp->ReplaceLieferantennummer(true,$input['adresse'],true); // Parameters: Target db?, value, from form?
                    }

                    $input['rechnungsdatum'] = $this->app->erp->ReplaceDatum(true,$input['rechnungsdatum'],true); // Parameters: Target db?, value, from form?
                    $input['eingangsdatum'] = $this->app->erp->ReplaceDatum(true,$input['eingangsdatum'],true); // Parameters: Target db?, value, from form?
                    $input['skontobis'] = $this->app->erp->ReplaceDatum(true,$input['skontobis'],true); // Parameters: Target db?, value, from form?
                    $input['zahlbarbis'] = $this->app->erp->ReplaceDatum(true,$input['zahlbarbis'],true); // Parameters: Target db?, value, from form?
                    $input['projekt'] = $this->app->erp->ReplaceProjekt(true,$input['projekt'],true);
                    $input['kostenstelle'] = $this->app->DB->Select("SELECT id FROM kostenstellen WHERE nummer = '".$input['kostenstelle']."'");
                    if(empty($input['projekt']) && !empty($input['adresse'])) {
                        $input['projekt'] = $this->app->erp->GetCreateProjekt($input['adresse']);                
                    }

                    if (!empty($input['adresse'])) {
                        $adressdaten = $this->app->DB->SelectRow("
                            SELECT 
                                zahlungszieltagelieferant,
                                zahlungszieltageskontolieferant,
                                zahlungszielskontolieferant,
                                ust_befreit
                            FROM adresse WHERE id = ".$input['adresse']
                        );

                        
                        if ($input['zahlbarbis'] == '0000-00-00' && $input['rechnungsdatum'] != '0000-00-00' && !empty($adressdaten['zahlungszieltagelieferant'])) {
                            $input['zahlbarbis'] = date('Y-m-d',strtotime($input['rechnungsdatum']." + ".$adressdaten['zahlungszieltagelieferant']." days"));
                        }
                        if ($input['skontobis'] == '0000-00-00' && $input['rechnungsdatum'] != '0000-00-00' && !empty($adressdaten['zahlungszieltageskontolieferant'])) {
                            $input['skontobis'] = date('Y-m-d',strtotime($input['rechnungsdatum']." + ".$adressdaten['zahlungszieltageskontolieferant']." days"));
                            $input['skonto'] = $adressdaten['zahlungszielskontolieferant'];
                        }
                    }
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

    //            echo($columns."<br>");
    //            echo($values."<br>");
    //            echo($update."<br>");

                $sql = "INSERT INTO lieferantengutschrift (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//               echo($sql);

                $this->app->DB->Update($sql);

                if ($id == 'NULL') {
                    $id = $this->app->DB->GetInsertID();
                    $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                    header("Location: index.php?module=lieferantengutschrift&action=edit&id=$id&msg=$msg");
                } else {
                    $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
                }        
            break;
            case 'positionen_hinzufuegen':

                $freigabe = $this->app->DB->SelectArr("SELECT rechnungsfreigabe, freigabe FROM lieferantengutschrift WHERE id =".$id)[0];
                if ($freigabe['rechnungsfreigabe'] || $freigabe['freigabe']) {
                    break;
                }

               // Process multi action
                $ids = $this->app->Secure->GetPOST('ids');
                $werte = $this->app->Secure->GetPOST('werte');
                $preise = $this->app->Secure->GetPOST('preise');

                foreach ($ids as $key => $verbindlichkeit_position) {
                    $menge = $werte[$key];

                    if ($menge <= 0) {
                        continue;
                    }

                    // Check available number
                    $sql = "
                        SELECT   
                            IF(
                                vp.menge > COALESCE(lgp.menge,0),
                                vp.menge - COALESCE(lgp.menge,0),
                                0
                            ) offen_menge
                        FROM                        
                            verbindlichkeit_position vp
                        LEFT JOIN(
                            SELECT
                                verbindlichkeit_position,
                                SUM(menge) AS menge
                            FROM
                                lieferantengutschrift_position lgp
                            GROUP BY
                                verbindlichkeit_position
                        ) lgp
                        ON
                            vp.id = lgp.verbindlichkeit_position           
                        WHERE vp.id = ".$verbindlichkeit_position."
                        ";
                    $offen_menge = $this->app->DB->Select($sql);

                    if ($offen_menge === 0) {
                        continue;
                    } 

                    if ($menge > $offen_menge && !empty($offen_menge)) {
                        $menge = $offen_menge;
                    }
                    
                    if ($menge == 0) {
                        continue;
                    } 

                    $sql = "SELECT 
                                a.id,
                                a.umsatzsteuer,
                                a.steuersatz,
                                COALESCE(if (skart.id <> 0,skart.id,skadr.id),0) AS kontorahmen,
                                vp.preis
                            FROM 
                                verbindlichkeit_position vp 
                            INNER JOIN
                                verbindlichkeit v ON v.id = vp.verbindlichkeit
                            INNER JOIN 
                                artikel a ON a.id = vp.artikel
                            INNER JOIN 
                                adresse adr ON v.adresse = adr.id
                            LEFT JOIN 
                                kontorahmen skart ON skart.id = a.kontorahmen
                            LEFT JOIN 
                                kontorahmen skadr ON skadr.id = adr.kontorahmen           
                            WHERE vp.id =".$verbindlichkeit_position;

                    $artikel = $this->app->DB->SelectRow($sql);
                        
                    $einartikel = $artikel['id'];
                    $umsatzsteuer = $artikel['umsatzsteuer'];
                    $kontorahmen = $artikel['kontorahmen'];                    
                    $preis = $artikel['preis'];                    

                    if(empty($umsatzsteuer) && is_numeric($artikel['steuersatz'])) {
                        $steuersatz = $artikel['steuersatz'];
                    } else {
                        $steuersatz = $this->get_steuersatz($umsatzsteuer,$id);
                    }
                   
                    $sql = "INSERT INTO lieferantengutschrift_position (lieferantengutschrift,verbindlichkeit_position, menge, preis, steuersatz, artikel, kontorahmen) VALUES ($id, $verbindlichkeit_position, $menge, $preis, $steuersatz, $einartikel, $kontorahmen)";
                    $this->app->DB->Insert($sql);
                }
            break;
            case 'artikel_manuell_hinzufuegen':

                $freigabe = $this->app->DB->SelectArr("SELECT rechnungsfreigabe, freigabe FROM lieferantengutschrift WHERE id =".$id)[0];
                if ($freigabe['rechnungsfreigabe'] || $freigabe['freigabe']) {
                    break;
                }

               // Process multi action
                $ids = $this->app->Secure->GetPOST('manuell_artikel_ids');
                $werte = $this->app->Secure->GetPOST('manuell_mengen');
                $preise = $this->app->Secure->GetPOST('preise');

                $bruttoeingabe = $this->app->Secure->GetPOST('bruttoeingabe');                                                                

                foreach ($ids as $key => $artikelid) {
                    $menge = $werte[$key];

                    if ($menge <= 0) {
                        continue;
                    }

                    $preis = $preise[$key];
                    if ($preis <= 0) {
                        $preis = 0;
                    }

                   $sql = "SELECT 
                                a.id,
                                a.umsatzsteuer,
                                a.steuersatz,
                                COALESCE(skart.id,0) AS kontorahmen
                            FROM
                                artikel a
                            LEFT JOIN 
                                kontorahmen skart ON skart.id = a.kontorahmen
                            WHERE a.id =".$artikelid;

                    $artikel = $this->app->DB->SelectRow($sql);
                    $einartikel = $artikel['id'];
                    $umsatzsteuer = $artikel['umsatzsteuer'];
                    if (empty($artikel['kontorahmen'])) {
                        $kontorahmen = $this->app->DB->Select("SELECT a.kontorahmen FROM adresse a INNER JOIN lieferantengutschrift lg ON lg.adresse = a.id WHERE lg.id = ".$id);
                    } else {
                        $kontorahmen = $artikel['kontorahmen'];                    
                    }

                    if(empty($umsatzsteuer) && is_numeric($artikel['steuersatz'])) {
                        $steuersatz = $artikel['steuersatz'];
                    } else {
                        $steuersatz = $this->get_steuersatz($umsatzsteuer,$id);
                    }

                    if ($bruttoeingabe) {
                        $preis = $preis / (1+($steuersatz/100));
                    }    
                    $sql = "INSERT INTO lieferantengutschrift_position (lieferantengutschrift,verbindlichkeit_position, menge, preis, steuersatz, artikel, kontorahmen) VALUES ($id, 0, $menge, $preis, $steuersatz, $einartikel, $kontorahmen)";

                    $this->app->DB->Insert($sql);
                }
            break;        
            case 'positionen_entfernen':           

                $freigabe = $this->app->DB->SelectArr("SELECT rechnungsfreigabe, freigabe FROM lieferantengutschrift WHERE id =".$id)[0];
                if ($freigabe['rechnungsfreigabe'] || $freigabe['freigabe']) {
                    break;
                }
                // Process multi action
                $ids = $this->app->Secure->GetPOST('auswahl');
                if (!is_array($ids)) {
                    break;
                }
                $this->app->DB->Delete("DELETE vp FROM lieferantengutschrift_position vp INNER JOIN lieferantengutschrift v ON v.id = vp.lieferantengutschrift WHERE vp.id IN (".implode(',',$ids).") AND v.freigabe <> 1");        

            break;       
            case 'positionen_steuersatz_zu_netto':           

                $freigabe = $this->app->DB->SelectArr("SELECT rechnungsfreigabe, freigabe FROM lieferantengutschrift WHERE id =".$id)[0];
                if ($freigabe['rechnungsfreigabe'] || $freigabe['freigabe']) {
                    break;
                }
                // Process multi action
                $ids = $this->app->Secure->GetPOST('auswahl');
                if (!is_array($ids)) {
                    break;
                }

                foreach ($ids as $posid) {
                    $tmpsteuersatz = null;
                    $tmpsteuertext = null;
                    $erloes = null;
                    $this->app->erp->GetSteuerPosition("lieferantengutschrift",$posid,$tmpsteuersatz,$tmpsteuertext,$erloes);

                    $faktor = 1+($tmpsteuersatz/100);

                    $sql = "UPDATE lieferantengutschrift_position SET preis = preis / ".$faktor." WHERE id = $posid";   
                    $this->app->DB->Update($sql);
                }    

            break;
            case 'positionen_kontorahmen_setzen':                                
                $freigabe = $this->app->DB->SelectArr("SELECT rechnungsfreigabe, freigabe FROM lieferantengutschrift WHERE id =".$id)[0];
                if ($freigabe['rechnungsfreigabe']) {
                    break;
                }
                // Process multi action
                $ids = $this->app->Secure->GetPOST('auswahl');
                if (!is_array($ids)) {
                    break;
                }

                $positionen_sachkonto = $this->app->Secure->GetPOST('positionen_sachkonto');
                $positionen_kontorahmen = $this->app->erp->ReplaceKontorahmen(true,$positionen_sachkonto,false);

                foreach ($ids as $posid) {                    
                    $sql = "UPDATE lieferantengutschrift_position SET kontorahmen = '".$positionen_kontorahmen."' WHERE id =".$posid;                       
                    $this->app->DB->Update($sql);
                }    
            break;
        }

    
        // Load values again from database
	    $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',v.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS v.id,
                                                     $dropnbox,
                                                     v.belegnr,
                                                     v.status_beleg,
                                                     v.schreibschutz,
                                                     v.rechnung,
                                                     v.zahlbarbis,
                                                     ".$this->app->erp->FormatMengeBetrag('v.betrag')." AS betrag,                                                   
                                                     v.skonto,
                                                     v.skontobis,
                                                     v.freigabe,
                                                     v.freigabemitarbeiter,
                                                     v.adresse,
                                                     v.projekt,
                                                     v.status,
                                                     v.bezahlt,
                                                     v.firma,
                                                     v.logdatei,                                                 
                                                     v.waehrung,
                                                     v.zahlungsweise,
                                                     v.eingangsdatum,
                                                     v.rechnungsdatum,
                                                     v.rechnungsfreigabe,
                                                     v.kostenstelle,
                                                     v.beschreibung,
                                                     v.sachkonto,
                                                     v.internebemerkung,
                                                     a.lieferantennummer,                                                        
                                                     a.name AS adresse_name FROM lieferantengutschrift v LEFT JOIN adresse a ON a.id = v.adresse"." WHERE v.id=$id");

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);   
        }

        if (!empty($result[0])) {
            $lieferantengutschrift_from_db = $result[0];
        }

        // Check  positions        
        $pos_check = $this->check_positions($lieferantengutschrift_from_db['id'],$lieferantengutschrift_from_db['betrag']);                         
                      
        $this->app->Tpl->Set('BETRAGNETTO', $pos_check['betrag_netto']);
        $this->app->Tpl->Set('BETRAGBRUTTOPOS', $pos_check['betrag_brutto']);
            
        if (empty($pos_check['rundungsdifferenz'])) {
            $this->app->Tpl->Set('RUNDUNGSDIFFERENZICONHIDDEN', 'hidden');            
        } else {
            $this->app->Tpl->Set('RUNDUNGSDIFFERENZ', $pos_check['rundungsdifferenz']);            
        }

        if ($pos_check['pos_ok']) {            
            if (!$lieferantengutschrift_from_db['freigabe'] && !$einkauf_automatik_aus) {
                if ($this->lieferantengutschrift_freigabeeinkauf($id,"lieferantengutschrift automatisch freigegeben (Einkauf)") === true) {
                    $this->app->YUI->Message('success',"lieferantengutschrift automatisch freigegeben (Einkauf)");
                    $lieferantengutschrift_from_db['freigabe'] = 1;
                } else {
                    $this->app->YUI->Message('warning','Waren-/Leistungspr&uuml;fung (Einkauf) nicht abgeschlossen');            
                }
            } 
            $this->app->Tpl->Set('POSITIONENMESSAGE', '<div class="success">Positionen vollst&auml;ndig</div>');                                    
            
            if ($lieferantengutschrift_from_db['status'] != 'abgeschlossen' && $lieferantengutschrift_from_db['status'] != 'storniert') {
                $this->lieferantengutschrift_abschliessen($id);            
            }            
            
        } else {
            $this->app->Tpl->Set('
                                    POSITIONENMESSAGE', '<div class="warning">Positionen nicht vollst&auml;ndig. Bruttobetrag '.
                                    $lieferantengutschrift_from_db['betrag'].
                                    ', Summe Positionen (brutto) '.
                                    $pos_check['betrag_brutto'].
                                    ', Differenz '.
                                    round($pos_check['betrag_brutto']-$lieferantengutschrift_from_db['betrag'],2).
                                    '</div>'
                                );            
            if ($lieferantengutschrift_from_db['freigabe']) {
                $this->app->DB->Update("UPDATE lieferantengutschrift SET freigabe = 0 WHERE id = ".$id);
                $lieferantengutschrift_from_db['freigabe'] = 0;
                $this->app->YUI->Message('warning',"lieferantengutschrift r&uuml;ckgesetzt (Einkauf)");        
            }
        }                    
       
        /*
         * Add displayed items later
         * 

        $this->app->Tpl->Add('KURZUEBERSCHRIFT2', $email);
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);         
        $this->app->YUI->AutoComplete("artikel", "artikelnummer");

         */

        $this->app->Tpl->Set('FREIGABEEINKAUFHIDDEN','hidden'); // prevent manual setting          

        if (!empty($lieferantengutschrift_from_db['belegnr'])) {
            $this->app->Tpl->Set('ADRESSESAVEDISABLED','disabled');
        } else {
            $this->app->YUI->AutoComplete("adresse", "lieferant");
            $this->app->YUI->AutoComplete("verbindlichkeit", "verbindlichkeit",false,"&adresse=".$lieferantengutschrift_from_db['adresse']); 
        }

        if (empty($lieferantengutschrift_from_db['adresse']) || $lieferantengutschrift_from_db['status'] == 'angelegt') {
            $this->app->Tpl->Set('FREIGABEBUCHHALTUNGHIDDEN','hidden'); 
            $this->app->Tpl->Set('FREIGABEBEZAHLTHIDDEN','hidden'); 
            $this->app->Tpl->Set('POSITIONHINZUFUEGENHIDDEN','hidden');
            $this->app->Tpl->Set('POSITIONENHIDDEN','hidden');
        }

        if ($lieferantengutschrift_from_db['freigabe']) {
            $this->app->Tpl->Set('FREIGABEEINKAUFHIDDEN','hidden');
            $this->app->Tpl->Set('EINKAUFINFOHIDDEN','hidden');
            $this->app->Tpl->Set('SAVEDISABLED','disabled');
            $this->app->Tpl->Set('POSITIONHINZUFUEGENHIDDEN','hidden');
        } else {
            $this->app->Tpl->Set('RUECKSETZENEINKAUFHIDDEN','hidden');
            $this->app->Tpl->Set('FREIGABEBUCHHALTUNGHIDDEN','hidden');
        }

        if (!empty($positionen)) {
            $this->app->Tpl->Set('FREIGABEEINKAUFHIDDEN','hidden');
        }
                
        if ($lieferantengutschrift_from_db['rechnungsfreigabe']) {
            $this->app->Tpl->Set('FREIGABEBUCHHALTUNGHIDDEN','hidden');
            $this->app->Tpl->Set('RUECKSETZENEINKAUFHIDDEN','hidden');
            $this->app->Tpl->Set('SACHKONTOCHANGEHIDDEN','hidden'); 
        } else {
            $this->app->Tpl->Set('RUECKSETZENBUCHHALTUNGHIDDEN','hidden');
        }                    
        if ($lieferantengutschrift_from_db['bezahlt'] == '1') {
            $this->app->Tpl->Set('FREIGABEBEZAHLTHIDDEN','hidden');
        } else {
            $this->app->Tpl->Set('RUECKSETZENBEZAHLTHIDDEN','hidden');
        }                    

      	$this->app->Tpl->Set('WARENEINGANGCHECKED', $lieferantengutschrift_from_db['freigabe']==1?"checked":"");
      	$this->app->Tpl->Set('RECHNUNGSFREIGABECHECKED', $lieferantengutschrift_from_db['rechnungsfreigabe']==1?"checked":"");
      	$this->app->Tpl->Set('BEZAHLTCHECKED', $lieferantengutschrift_from_db['bezahlt']==1?"checked":"");

        $this->app->Tpl->Set('RECHNUNGSDATUM',$this->app->erp->ReplaceDatum(false,$lieferantengutschrift_from_db['rechnungsdatum'],false));
        $this->app->YUI->DatePicker("rechnungsdatum");
        $this->app->Tpl->Set('EINGANGSDATUM',$this->app->erp->ReplaceDatum(false,$lieferantengutschrift_from_db['eingangsdatum'],false));
        $this->app->YUI->DatePicker("eingangsdatum");
        $this->app->Tpl->Set('SKONTOBIS',$this->app->erp->ReplaceDatum(false,$lieferantengutschrift_from_db['skontobis'],false));
        $this->app->YUI->DatePicker("skontobis");
        $this->app->Tpl->Set('ZAHLBARBIS',$this->app->erp->ReplaceDatum(false,$lieferantengutschrift_from_db['zahlbarbis'],false));
        $this->app->YUI->DatePicker("zahlbarbis");

    	$sql = "SELECT 
    	            ".$this->app->YUI->IconsSQLverbindlichkeit() . " AS `icons` 
	                FROM lieferantengutschrift v 
    	            LEFT JOIN (
                        SELECT ds.parameter, COUNT(ds.objekt) datei_anzahl FROM datei_stichwoerter ds INNER JOIN datei d ON d.id = ds.datei WHERE ds.objekt='lieferantengutschrift' AND d.geloescht <> 1 GROUP BY ds.parameter
                    ) d ON d.parameter = v.id
                    WHERE id=$id";
	    $icons = $this->app->DB->SelectArr($sql);
        $this->app->Tpl->Add('STATUSICONS',  $icons[0]['icons']);

        $this->app->Tpl->Set('VERBINDLICHKEIT',$this->app->erp->ReplaceVerbindlichkeit(false,$lieferantengutschrift_from_db['verbindlichkeit'],false));
         
        $this->app->YUI->AutoComplete("projekt", "projektname", 1);
        $this->app->Tpl->Set('PROJEKT',$this->app->erp->ReplaceProjekt(false,$lieferantengutschrift_from_db['projekt'],false));
        $this->app->YUI->AutoComplete("kostenstelle", "kostenstelle", 1);
        $this->app->Tpl->Set('KOSTENSTELLE',$this->app->DB->SELECT("SELECT nummer FROM kostenstellen WHERE id = '".$lieferantengutschrift_from_db['kostenstelle']."'"));

        $waehrungenselect = $this->app->erp->GetSelect($this->app->erp->GetWaehrung(), $lieferantengutschrift_from_db['waehrung']);
        $this->app->Tpl->Set('WAEHRUNGSELECT', $waehrungenselect);

        $this->app->Tpl->Set('ADRESSE_ID', $lieferantengutschrift_from_db['adresse']);

        $this->app->Tpl->Set('ADRESSE', $this->app->erp->ReplaceLieferantennummer(false,$lieferantengutschrift_from_db['adresse'],false)); // Convert ID to form display     

        $this->app->Tpl->Set('BESTELLUNG',$this->app->erp->ReplaceBestellung(false,$lieferantengutschrift_from_db['bestellung'],false));
        $this->app->YUI->AutoComplete("bestellung", "lieferantenbestellung",0,"&adresse=".$lieferantengutschrift_from_db['adresse']);     

        $this->app->YUI->CkEditor("internebemerkung");

        $anzahldateien = $this->app->erp->AnzahlDateien("lieferantengutschrift",$id);
        if ($anzahldateien > 0) {
            $file = urlencode("../../../../index.php?module=lieferantengutschrift&action=inlinepdf&id=$id");        
            $iframe = "<iframe width=\"100%\" height=\"100%\" style=\"height:calc(100vh - 110px)\" src=\"./js/production/generic/web/viewer.html?file=$file\"></iframe>";
            $this->app->Tpl->Set('INLINEPDF', $iframe);
        } else {
            $this->app->Tpl->Set('INLINEPDF', 'Keine Dateien vorhanden.');
        }
               
        // -- POSITIONEN
        if (empty($lieferantengutschrift_from_db['freigabe'])) {
            $this->app->YUI->TableSearch('PAKETDISTRIBUTION', 'verbindlichkeit_positionen', "show", "", "", basename(__FILE__), __CLASS__);
        } 
        $this->app->YUI->AutoComplete("positionen_sachkonto", "sachkonto", 1);
        $this->app->YUI->TableSearch('POSITIONEN', 'lieferantengutschrift_positionen', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('POSITIONENTAB', "lieferantengutschrift_positionen.tpl");
        // -- POSITIONEN

        // -- POSITIONEN manuell
        if (empty($lieferantengutschrift_from_db['freigabe'])) {
                $this->app->YUI->TableSearch('ARTIKELMANUELL', 'artikel_manuell', "show", "", "", basename(__FILE__), __CLASS__);
        }         
        $this->app->Tpl->Parse('POSITIONENMANUELLTAB', "lieferantengutschrift_artikel_manuell.tpl");
        // -- POSITIONEN manuell

        $this->lieferantengutschrift_minidetail('MINIDETAIL',false);
        $this->app->Tpl->Parse('PAGE', "lieferantengutschrift_edit.tpl");

    }

   function lieferantengutschrift_editpos() {
        $id = $this->app->Secure->GetGET('id');
        $posid = $this->app->Secure->GetGET('posid');             
              
        $this->app->Tpl->Set('ID', $id);
        $this->app->erp->MenuEintrag("index.php?module=lieferantengutschrift&action=edit&id=$id#tabs-2", "Zur&uuml;ck");

        $sachkonto = $this->app->Secure->GetPOST('sachkonto');
        $menge = $this->app->Secure->GetPOST('menge');        
        $preis = $this->app->Secure->GetPOST('preis');        
        $steuersatz = $this->app->Secure->GetPOST('steuersatz');                

        $kontorahmen = $this->app->erp->ReplaceKontorahmen(true,$sachkonto,false);
        if ($menge < 0) {
            $menge = 0;
        }
        if ($preis < 0) {
            $preis = 0;
        }
        if ($steuersatz < 0) {
            $steuersatz = 0;
        }
        $submit = $this->app->Secure->GetPOST('submit');    

        $freigabe = $this->app->DB->SelectArr("SELECT rechnungsfreigabe, freigabe FROM lieferantengutschrift WHERE id =".$id)[0];       
        if ($freigabe['rechnungsfreigabe'] && $freigabe['freigabe']) {
            $this->app->Tpl->Set('SAVEDISABLED','disabled');
            $this->app->Tpl->Set('SACHKONTOSAVEDISABLED','disabled');
        } else if ($freigabe['freigabe']) {
            $this->app->Tpl->Set('SAVEDISABLED','disabled');   
            if ($submit != '')
            {                
                $sql = "
                    UPDATE lieferantengutschrift_position SET              
                        kontorahmen = '$kontorahmen'
                    WHERE id = ".$posid."                
                ";
                $this->app->DB->Update($sql);
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
                header("Location: index.php?module=lieferantengutschrift&action=edit&id=$id&msg=$msg#tabs-2");
            }
        } else {
            if ($submit != '')
            {                
                $sql = "
                    UPDATE lieferantengutschrift_position SET 
                        menge = '$menge',
                        preis = '$preis',
                        steuersatz = '$steuersatz',
                        kontorahmen = '$kontorahmen'
                    WHERE id = ".$posid."                
                ";
                $this->app->DB->Update($sql);

                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
                header("Location: index.php?module=lieferantengutschrift&action=edit&id=$id&msg=$msg#tabs-2");
            }
        }
   
        // Load values again from database
	    $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',v.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS v.id, $dropnbox, v.steuersatz, v.preis, v.menge, v.kontorahmen, v.id FROM lieferantengutschrift_position v"." WHERE id=$posid");        

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);   
        }

        if (!empty($result)) {
            $lieferantengutschrift_position_from_db = $result[0];
        } else {
            return;
        }
             
        /*
         * Add displayed items later
         * 

        $this->app->Tpl->Add('KURZUEBERSCHRIFT2', $email);
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);         

        $this->app->YUI->AutoComplete("artikel", "artikelnummer");

         */

        $this->app->YUI->AutoComplete("sachkonto", "sachkonto", 1);
        $this->app->Tpl->Set('SACHKONTO', $this->app->erp->ReplaceKontorahmen(false,$lieferantengutschrift_position_from_db['kontorahmen'],false));

        $this->app->Tpl->Parse('PAGE', "lieferantengutschrift_position_edit.tpl");
    }


    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
	    $input['adresse'] = $this->app->Secure->GetPOST('adresse');
	    $input['rechnung'] = $this->app->Secure->GetPOST('rechnung');
	    $input['zahlbarbis'] = $this->app->Secure->GetPOST('zahlbarbis');
	    $input['betrag'] = $this->app->Secure->GetPOST('betrag');
	    $input['waehrung'] = $this->app->Secure->GetPOST('waehrung');
	    $input['skonto'] = $this->app->Secure->GetPOST('skonto');
	    $input['skontobis'] = $this->app->Secure->GetPOST('skontobis');
	    $input['projekt'] = $this->app->Secure->GetPOST('projekt');
	    $input['zahlungsweise'] = $this->app->Secure->GetPOST('zahlungsweise');
	    $input['eingangsdatum'] = $this->app->Secure->GetPOST('eingangsdatum');
	    $input['rechnungsdatum'] = $this->app->Secure->GetPOST('rechnungsdatum');
	    $input['kostenstelle'] = $this->app->Secure->GetPOST('kostenstelle');
	    $input['internebemerkung'] = $this->app->Secure->GetPOST('internebemerkung');
        return $input;
    }

    function lieferantengutschrift_menu($id) {       

        $this->app->erp->MenuEintrag("index.php?module=lieferantengutschrift&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=lieferantengutschrift&action=list", "Zur&uuml;ck zur &Uuml;bersicht");

        $anzahldateien = $this->app->erp->AnzahlDateien("lieferantengutschrift",$id);
        if ($anzahldateien > 0) {
            $anzahldateien = " (".$anzahldateien.")"; 
        } else {
            $anzahldateien="";
        }

        if ($id != 'NULL') {
            $this->app->erp->MenuEintrag("index.php?module=lieferantengutschrift&action=dateien&id=$id", "Dateien".$anzahldateien);
        }

        $invoiceArr = $this->app->DB->SelectRow("SELECT v.belegnr, a.name, v.status, schreibschutz FROM lieferantengutschrift v LEFT JOIN adresse a ON v.adresse = a.id WHERE v.id='$id' LIMIT 1");
        $belegnr = $invoiceArr['belegnr'];
        $name = $invoiceArr['name'];
        if($belegnr=='0' || $belegnr=='') {
            $belegnr ='(Entwurf)';
        }
        $this->app->Tpl->Set('KURZUEBERSCHRIFT2',"$name lieferantengutschrift $belegnr");

        if ($invoiceArr['status'] === 'angelegt' || empty($invoiceArr['status'])) {
            $this->app->erp->MenuEintrag("index.php?module=lieferantengutschrift&action=freigabe&id=$id",'Freigabe');
        }       
    }

    function lieferantengutschrift_dateien()
    {
        $id = $this->app->Secure->GetGET("id");
        $this->lieferantengutschrift_menu($id);
        $this->app->Tpl->Add('UEBERSCHRIFT'," (Dateien)");
        $this->app->YUI->DateiUpload('PAGE',"lieferantengutschrift",$id);
    }

    function lieferantengutschrift_inlinepdf() {
        $id = $this->app->Secure->GetGET('id');         

        $file_attachments = $this->app->erp->GetDateiSubjektObjekt('%','lieferantengutschrift',$id);             

        if (!empty($file_attachments)) {

            // Try to merge all PDFs
            $file_paths = array();
            foreach ($file_attachments as $file_attachment) {
                if ($this->app->erp->GetDateiEndung($file_attachment) == 'pdf') {
                    $file_paths[] = $this->app->erp->GetDateiPfad($file_attachment);
                }
            }
            $pdfMerger = $this->app->Container->get('PdfMerger');
            $mergeOutputPath = realpath($this->app->erp->GetTMP()) . '/' . uniqid('sammelpdf_', true) . '.pdf';
            try {
                $pdfMerger->merge($file_paths, $mergeOutputPath);
                header('Content-type:application/pdf');
                header('Content-Disposition: attachment;filename='.md5(microtime(true)).'.pdf');
                readfile($mergeOutputPath);
                $this->app->ExitXentral();
            } catch (Exception $exception) {
                // Just the first PDF
                foreach ($file_attachments as $file_attachment) {
                    if ($this->app->erp->GetDateiEndung($file_attachment) == 'pdf') {
                        $file_contents = $this->app->erp->GetDatei($file_attachment);
                        header('Content-type:application/pdf');
                        header('Content-Disposition: attachment;filename=lieferantengutschrift_'.$id.'.pdf');
                        echo($file_contents);
                        $this->app->ExitXentral();
                    }
                }            
            }    
        }  
        $this->app->ExitXentral();
    }
  
    function lieferantengutschrift_freigabe()
    {      
        $id = $this->app->Secure->GetGET('id');
        $this->app->erp->BelegFreigabe('lieferantengutschrift',$id);
        $this->app->erp->BelegProtokoll("lieferantengutschrift",$id,"lieferantengutschrift freigegeben");
        $this->lieferantengutschrift_edit();
    }

    // Returns true or error message
    function lieferantengutschrift_freigabeeinkauf($id = null, $text = null)
    {      
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }

        $error = false;

        if (!$this->lieferantengutschrift_is_freigegeben($id)) {                   
            if ($gotoedit) {
                $this->app->YUI->Message('warning','lieferantengutschrift nicht freigegeben');                    
                $error = true;
            } else {
                return('lieferantengutschrift nicht freigegeben '.$this->lieferantengutschrift_get_belegnr($id));
            }            
        }           
       
        $sql = "UPDATE lieferantengutschrift SET freigabe = 1 WHERE id=".$id;
        $this->app->DB->Update($sql);

        if (!$text) {
            $text = "lieferantengutschrift freigegeben (Einkauf)";
        }
        $this->app->erp->BelegProtokoll("lieferantengutschrift",$id,$text);

        if ($gotoedit) {
            $this->lieferantengutschrift_edit();
        }
        else {
            return(true);
        }        
    }

    // Returns true or error message
    function lieferantengutschrift_freigabebuchhaltung($id = null)
    {      
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }        

        $error = false;

        if (!$this->lieferantengutschrift_is_freigegeben($id)) {                   
            if ($gotoedit) {
                $this->app->YUI->Message('warning','lieferantengutschrift nicht freigegeben');
                $error = true;
            } else {
                return('lieferantengutschrift nicht freigegeben '.$this->lieferantengutschrift_get_belegnr($id));
            }            
        }

        if (!$error) {
            // Check accounting
            $sql = "
                SELECT 
                        vp.id,
                        v.belegnr                     
                        FROM lieferantengutschrift_position vp 
                        LEFT JOIN lieferantengutschrift v ON v.id = vp.lieferantengutschrift
                        WHERE 
                            lieferantengutschrift='$id'
                        AND vp.kontorahmen = 0
            ";

            $check = $this->app->DB->SelectArr($sql); 

            if (!empty($check)) {            
                if ($gotoedit) {
                    $this->app->YUI->Message('warning','Kontierung unvollst&auml;ndig');            
                    $error = true;
                } else {
                    return('Kontierung unvollst&auml;ndig '.$this->lieferantengutschrift_get_belegnr($id));
                }
            }
        }
           
        if (!$error) {           
            $sql = "UPDATE lieferantengutschrift SET rechnungsfreigabe = 1 WHERE freigabe = 1 AND id=".$id;
            $this->app->DB->Update($sql);
            $this->app->erp->BelegProtokoll("lieferantengutschrift",$id,"lieferantengutschrift freigegeben (Buchhaltung)");            
        }

        if ($gotoedit) {
            $this->lieferantengutschrift_edit();
        } else {
            return(true);
        }
    }

    // Returns true or error message
    function lieferantengutschrift_freigabebezahlt($id = null)
    {      
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }

        if (!$this->lieferantengutschrift_is_freigegeben($id)) {                   
            if ($gotoedit) {
                $this->app->YUI->Message('warning','lieferantengutschrift nicht freigegeben');                    
                $error = true;
            } else {
                return('lieferantengutschrift nicht freigegeben '.$this->lieferantengutschrift_get_belegnr($id));
            }            
        }
    
        if (!$error) {
            $sql = "UPDATE lieferantengutschrift SET bezahlt = 1 WHERE id=".$id;
            $this->app->DB->Update($sql);
            $this->app->erp->BelegProtokoll("lieferantengutschrift",$id,"lieferantengutschrift als bezahlt markiert");
            if ($gotoedit) {
                $this->lieferantengutschrift_edit();
            } else {
                return(true);
            }       
        }
    }  
    
    function lieferantengutschrift_abschliessen($id = null)
    {      
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }
        
        $sql = "SELECT freigabe, rechnungsfreigabe, bezahlt, betrag FROM lieferantengutschrift WHERE id =".$id;             
        $lieferantengutschrift = $this->app->DB->SelectRow($sql);
           
        if ($lieferantengutschrift['freigabe'] != 1) {
            $einkauf_check = $this->check_positions($id,$lieferantengutschrift['betrag']);
            if ($einkauf_check['pos_ok']) {
                $this->lieferantengutschrift_freigabeeinkauf($id);
                $lieferantengutschrift['freigabe'] = 1;
            }
        }
               
        $anzahldateien = $this->app->erp->AnzahlDateien("lieferantengutschrift",$id);                    
        if (!empty($anzahldateien) && $lieferantengutschrift['freigabe'] && $lieferantengutschrift['rechnungsfreigabe'] && $lieferantengutschrift['bezahlt']) {
            $sql = "UPDATE lieferantengutschrift SET status = 'abgeschlossen' WHERE id=".$id;
            $this->app->DB->Update($sql);
            $this->app->erp->BelegProtokoll("lieferantengutschrift",$id,"lieferantengutschrift abgeschlossen");            
            if ($gotoedit) {
                $this->lieferantengutschrift_edit();
            }        
        }                        
    }  

    function lieferantengutschrift_ruecksetzeneinkauf($id = null)
    {      
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }
        $sql = "UPDATE lieferantengutschrift SET freigabe = 0 WHERE id=".$id;
        $this->app->DB->Update($sql);
        $this->app->erp->BelegProtokoll("lieferantengutschrift",$id,"lieferantengutschrift r&uuml;ckgesetzt (Einkauf)");
        if ($gotoedit) {
            $this->lieferantengutschrift_edit(true);
        }
    }

    function lieferantengutschrift_ruecksetzenbuchhaltung($id = null)
    {      
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }
        $sql = "UPDATE lieferantengutschrift SET rechnungsfreigabe = 0 WHERE id=".$id;
        $this->app->DB->Update($sql);
        $this->app->erp->BelegProtokoll("lieferantengutschrift",$id,"lieferantengutschrift r&uuml;ckgesetzt (Buchhaltung)");
        if ($gotoedit) {
            $this->lieferantengutschrift_edit();
        }
    }

    function lieferantengutschrift_ruecksetzenbezahlt($id = null)
    {      
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }
        $sql = "UPDATE lieferantengutschrift SET bezahlt = 0 WHERE id=".$id;
        $this->app->DB->Update($sql);
        $this->app->erp->BelegProtokoll("lieferantengutschrift",$id,"lieferantengutschrift bezahlt r&uuml;ckgesetzt");
        if ($gotoedit) {
            $this->lieferantengutschrift_edit();
        }        
    }  
        
/*    function lieferantengutschrift_schreibschutz($id = null)
    {      
        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
            $gotoedit = true;
        }
        $sql = "UPDATE lieferantengutschrift SET schreibschutz = 0 WHERE id=".$id;
        $this->app->DB->Update($sql);
        $this->app->erp->BelegProtokoll("lieferantengutschrift",$id,"lieferantengutschrift Schreibschutz entfernt");
        if ($gotoedit) {
            $this->lieferantengutschrift_edit();
        }        
    }  */

    public function lieferantengutschrift_minidetail($parsetarget='',$menu=true) {

        $id = $this->app->Secure->GetGET('id');  

        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS 
                                                v.id,
                                                v.belegnr,
                                                v.status_beleg,
                                                v.schreibschutz,
                                                v.rechnung,
                                                ".$this->app->erp->FormatDate('v.zahlbarbis', 'zahlbarbis').",
                                                ".$this->app->erp->FormatMengeBetrag('v.betrag')." AS betrag,
                                                v.skonto,
                                                ".$this->app->erp->FormatDate('v.skontobis', 'skontobis').",
                                                v.freigabe,
                                                v.freigabemitarbeiter,
                                                p.abkuerzung AS projekt,
                                                v.status,
                                                v.bezahlt,
                                                v.firma,
                                                v.logdatei,
                                                v.waehrung,
                                                v.zahlungsweise,
                                                ".$this->app->erp->FormatDate('v.eingangsdatum', 'eingangsdatum').",
                                                ".$this->app->erp->FormatDate('v.rechnungsdatum', 'rechnungsdatum').",
                                                v.rechnungsfreigabe,
                                                k.nummer as kostenstelle,
                                                v.beschreibung,
                                                v.internebemerkung,
                                                v.id,
                                                CONCAT(a.lieferantennummer,' ',a.name) AS adresse
                                                FROM lieferantengutschrift v 
                                                LEFT JOIN adresse a ON a.id = v.adresse
                                                LEFT JOIN projekt p ON a.projekt = p.id
                                                LEFT JOIN kostenstellen k ON v.kostenstelle = k.id
                                                WHERE v.id='$id'");        

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);   
        }

        if (!empty($result[0])) {
            $lieferantengutschrift_from_db = $result[0];
        }

        $positionen = $this->app->DB->SelectArr("SELECT vp.id,
                                                    vp.sort,
                                                    art.name_de,
                                                    art.nummer,
                                                    vp.menge,
                                                    vp.preis,
                                                    vp.steuersatz,
                                                    CONCAT(skv.sachkonto,' ',skv.beschriftung) AS sachkonto,
                                                    '' 
                                                    FROM lieferantengutschrift_position vp 
                                                    INNER JOIN artikel art ON art.id = vp.artikel 
                                                    LEFT JOIN lieferantengutschrift v ON v.id = vp.lieferantengutschrift
                                                    LEFT JOIN adresse adr ON adr.id = v.adresse           
                                                    LEFT JOIN kontorahmen skv ON skv.id = vp.kontorahmen
                                                    WHERE lieferantengutschrift='$id'
                                                    ORDER by vp.sort ASC");

        $tmp = new EasyTable($this->app);
        $tmp->headings = array('Pos.','Artikel-Nr.','Artikel','Menge','Preis','Steuersatz','WIRDUNTENGEFÜLLTWARUMAUCHIMMER');
        $betrag_netto = 0;
        $betrag_brutto = 0;
        $steuer_normal = 0;
        $steuer_ermaessigt = 0;
        foreach ($positionen as $position) {

            $tmpsteuersatz = null;
            $tmpsteuertext = null;
            $erloes = null;

            $this->app->erp->GetSteuerPosition("lieferantengutschrift",$position['id'],$tmpsteuersatz,$tmpsteuertext,$erloes);

            $position['steuersatz_berechnet'] = $tmpsteuersatz;
            $position['steuertext_berechnet'] = $tmpsteuertext;
            $position['steuererloes_berechnet'] = $erloes;

            $betrag_netto += ($position['menge']*$position['preis']);
            $betrag_brutto += ($position['menge']*$position['preis'])*(1+($tmpsteuersatz/100));

            $row = array(
                $position['sort'],
                $position['nummer'],
                $position['name_de'],
                $position['menge'],
                $position['preis'],
                $position['steuersatz_berechnet'],
                $position['sachkonto']
            );
            $tmp->AddRow($row);
        }

        $row = array(
            '',
            '',
            '',
            '',
            '<b>Betrag Positionen netto</b>',
            '<b>Betrag Positionen brutto</b>' 
        );
        $tmp->AddRow($row);
        $row = array(
            '',
            '',
            '',
            '',
            round($betrag_netto,2),
            round($betrag_brutto,2)
        );
        $tmp->AddRow($row);
        $tmp->DisplayNew('ARTIKEL',"Sachkonto","noAction");

        $tmp = new EasyTable($this->app);
        $tmp->Query("SELECT zeit,bearbeiter,grund FROM lieferantengutschrift_protokoll WHERE lieferantengutschrift='$id' ORDER by zeit DESC",0,"");
        $tmp->DisplayNew('PROTOKOLL',"Protokoll","noAction");

        if($parsetarget=='')
        {
            $this->app->Tpl->Output('lieferantengutschrift_minidetail.tpl');
            $this->app->ExitXentral();
        }
        $this->app->Tpl->Parse($parsetarget,'lieferantengutschrift_minidetail.tpl');
  }

    function lieferantengutschrift_is_freigegeben($id) {
        $sql = "SELECT 
                    belegnr 
                FROM 
                    lieferantengutschrift
                WHERE
                    id='$id' 
                AND
                    status IN ('freigegeben','abgeschlossen')                
                ";

        $check = $this->app->DB->SelectArr($sql); 
        if (empty($check)) {
            return(false);
        } else 
        {
            return(true);
        }
    }

    function lieferantengutschrift_get_belegnr($id) {
        return($this->app->DB->Select("SELECT belegnr FROM lieferantengutschrift WHERE id =".$id));
    }

    /* Calculate steuersatz
        Get from 
        Check address first, if foreign, then steuersatz = 0
        if not foreign there are three cases: befreit = 0, ermaessigt, normal
        if not befreit, get from projekt or firmendaten
    */
    function get_steuersatz($umsatzsteuer, $lieferantengutschrift) {
        if (is_numeric($umsatzsteuer)) {
            return($umsatzsteuer);
        }

        if ($umsatzsteuer == 'befreit') {
            return(0);
        }
        
        $adresse = $this->app->DB->Select("SELECT adresse FROM lieferantengutschrift WHERE id=".$lieferantengutschrift);
        $umsatzsteuer_lieferant = $this->app->DB->Select("SELECT umsatzsteuer_lieferant FROM adresse WHERE id=".$adresse); /* inland, eu-lieferung, import*/

        if (in_array($umsatzsteuer_lieferant,array('import','eulieferung'))) {
            return(0);
        }
        
        $projekt = $this->app->DB->Select("SELECT projekt FROM lieferantengutschrift WHERE id=".$lieferantengutschrift);
        $steuersatz_projekt = $this->app->DB->SelectRow("SELECT steuersatz_normal, steuersatz_ermaessigt FROM projekt WHERE id ='".$projekt."'");
        $steuersatz_normal_projekt = $steuersatz_projekt['steuer_normal'];
        $steuersatz_ermaessigt_projekt = $steuersatz_projekt['steuer_ermaessigt'];
           
        $steuersatz_normal = $this->app->erp->Firmendaten('steuersatz_normal');
        $steuersatz_ermaessigt = $this->app->erp->Firmendaten('steuersatz_ermaessigt');

        switch($umsatzsteuer) {
            case 'normal':
                if (!empty($steuersatz_normal_projekt)) {
                    return($steuersatz_normal_projekt);
                } else {
                    return($steuersatz_normal);
                }               
            break;
            case 'ermaessigt':
                if (!empty($steuersatz_ermaessigt_projekt)) {
                    return($steuersatz_ermaessigt_projekt);
                } else {
                    return($steuersatz_ermaessigt);
                }               
            break;
            default:   
                return(0);
            break;
        }

    }
    
    // Check positions and return status and values
    function check_positions($id, $bruttobetrag_lieferantengutschrift) : array {

        $result = array(
            "pos_ok" => false,
            "betrag_netto" => 0,
            "betrag_brutto" => 0,
            "rundungsdifferenz" => 0,
            "bruttobetrag_lieferantengutschrift" => $bruttobetrag_lieferantengutschrift
        );        

        if (empty($id)) {
            return($result);
        }

        // Summarize positions
        $sql = "SELECT * FROM lieferantengutschrift_position WHERE lieferantengutschrift = ".$id;
        $positionen = $this->app->DB->SelectArr($sql);        

        if (!empty($positionen)) {
            $betrag_netto = 0;
            $betrag_brutto = 0;
            $betrag_brutto_pos_summe = 0;
            $steuer_normal = 0;
            $steuer_ermaessigt = 0;

            /* 
                Normal: umsatzsteuer leer, steuersatz = leer
                Ermäßigt: umsatzsteuer ermaessigt, steuersatz = -1
                Befreit: umsatzsteuer befreit, steursatz = -1
                Individuell: umsatzsteuer leer, steuersatz = wert
            */
            foreach ($positionen as $position) {

                $tmpsteuersatz = null;
                $tmpsteuertext = null;
                $erloes = null;

                $this->app->erp->GetSteuerPosition("lieferantengutschrift",$position['id'],$tmpsteuersatz,$tmpsteuertext,$erloes);

                $position['steuersatz_berechnet'] = $tmpsteuersatz;
                $position['steuertext_berechnet'] = $tmpsteuertext;
                $position['steuererloes_berechnet'] = $erloes;
           
                $betrag_netto += ($position['menge']*$position['preis']);
                $betrag_brutto += ($position['menge']*$position['preis'])*(1+($tmpsteuersatz/100));
                $betrag_brutto_pos_summe += round(($position['menge']*$position['preis'])*(1+($tmpsteuersatz/100)),2);

            }
      
            $result['betrag_netto'] = round($betrag_netto,2);
            $result['betrag_brutto'] = round($betrag_brutto,2);
      
            if ($bruttobetrag_lieferantengutschrift == round($betrag_brutto,2)) {
                $result['pos_ok'] = true;    
            }
            else if (round($bruttobetrag_lieferantengutschrift,2) == round($betrag_brutto_pos_summe,2)) {
                $result['pos_ok'] = true;    
                if (round($bruttobetrag_lieferantengutschrift,2) != round($betrag_brutto_pos_summe,2)) {
                    $result['rundungsdifferenz'] = round(round($betrag_brutto,2) - $betrag_brutto_pos_summe,2);
                }   
            }                                    
        }       
             
        return($result);
    }
}
