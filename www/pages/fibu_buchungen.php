<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Fibu_buchungen {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "fibu_buchungen_list");        
        $this->app->ActionHandler("create", "fibu_buchungen_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "fibu_buchungen_edit");
        $this->app->ActionHandler("delete", "fibu_buchungen_delete");
        $this->app->ActionHandler("assoc", "fibu_buchungen_assoc");
        $this->app->ActionHandler("zuordnen", "fibu_buchungen_zuordnen");
        $this->app->ActionHandler("einzelzuordnen", "fibu_buchungen_einzelzuordnen");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);

        $this->app->erp->Headlines('Buchhaltung Buchungen');
    }

    public function Install() {
        /* Fill out manually later */
    }

    function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {      
            case "fibu_buchungen_list":
                $allowed['fibu_buchungen_list'] = array('list');

                $doc_typ = $this->app->User->GetParameter('fibu_buchungen_doc_typ');
                $doc_id = $this->app->User->GetParameter('fibu_buchungen_doc_id');

                $heading = array('','','Buchungsart', 'Typ', 'Datum', 'Beleg', 'Betrag', 'W&auml;hrung', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                $alignright = array(7); 
                $sumcol= array(7);

                $findcols = array('f.id','f.id','f.buchungsart', 'f.typ', 'f.datum', 'f.doc_info', 'f.betrag', 'f.waehrung','f.id');
                $searchsql = array('f.buchungsart', 'f.typ', 'f.datum', 'f.doc_typ', 'f.doc_info');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		        $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',f.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?action=edit&module=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a></td></tr></table>";

                if (!empty($doc_typ) && !empty($doc_id)) {
                    $where = "`doc_typ` = '$doc_typ' AND `doc_id` = $doc_id";                
                } else{
                    $where = "1";
                }

                $sql = "SELECT SQL_CALC_FOUND_ROWS 
                    f.id, 
                    $dropnbox, 
                    ".$app->erp->FormatUCfirst('f.buchungsart').",
                    ".$app->erp->FormatUCfirst('f.typ').",
                    ".$app->erp->FormatDate('f.datum').",
                    CONCAT(".$app->erp->FormatUCfirst('f.doc_typ').",' ',f.doc_info),
                    ".$app->erp->FormatMenge('f.betrag',2).",
                    f.waehrung,
                    CONCAT(f.edit_module,'&id=',f.edit_id) 
                FROM fibu_buchungen_alle f";

                $count = "SELECT count(*) FROM fibu_buchungen_alle WHERE $where";
//                $groupby = "";

                break;       
            case "fibu_buchungen_salden":
                $allowed['fibu_buchungen_salden'] = array('list');
                $heading = array( 'Typ','Anzahl', 'Saldo', 'Men&uuml;');
                $width = array(  '83%', '1%', '1%',    '1%');

                $findcols = array('typ','','saldonum');
                $searchsql = array('typ','saldonum');

                $defaultorder = 1;
                $defaultorderdesc = 0;

	            $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',f.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=fibu_buchungen&action=zuordnen&typ=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a></td></tr></table>";

                $linkstart = '<table cellpadding=0 cellspacing=0><tr><td nowrap><a href="index.php?module=fibu_buchungen&action=edit&';
                $linkend = '"><img src="./themes/'.$app->Conf->WFconf['defaulttheme'].'/images/forward.svg" border=0></a></td></tr></table>';

                $id = $app->Secure->GetGET('id');     

                $saldolink = array (
                    '<a href=\"index.php?module=fibu_buchungen&action=zuordnen&typ=',
                    ['sql' => 'typ'],
                    '">',
                    ['sql' => $this->app->erp->FormatMenge('SUM(COALESCE(saldonum,0))',2)],
                    '</a>'
                );      

                $sql = "SELECT
                    '',
                    ".$this->app->erp->FormatUCfirst('typ')." AS typ,
                    count(id) AS anzahl,
                    ".$this->app->erp->ConcatSQL($saldolink)." AS saldo,
                    typ
                FROM
                    (
                    SELECT
                        fb.typ,
                        fb.id,
                        fo.info,
                        SUM(betrag) AS saldonum
                    FROM
                        `fibu_buchungen_alle` fb                    
                    INNER JOIN fibu_objekte fo ON
                        fb.typ = fo.typ AND fb.id = fo.id
                    WHERE
                        fb.typ <> 'kontorahmen'      
                    GROUP BY
                        fb.typ,
                        fb.id
                ) salden                                
                ";

                $where = "saldonum <> 0";
//                $count = "SELECT count(DISTINCT id) FROM fibu_buchungen_alle WHERE $where";
                $groupby = "GROUP BY typ";
                $orderby = "ORDER BY typ";

                //echo($sql." WHERE ".$where." ".$groupby);

            break;                         

            case "fibu_buchungen_wahl":
                $allowed['fibu_buchungen_wahl'] = array('list');
                $heading = array('',  '',  'Datum', 'Typ', 'Info', 'Von','Nach', 'Men&uuml;');
                $width = array(  '1%','1%','1%',  '20%',   '80%',   '1%', '1%',    '%1'   );

                $findcols = array('f.id','f.id','f.datum','f.typ','f.info','f.id','f.id','f.id');
                $searchsql = array('f.typ','f.info', 'f.datum', 'f.parent_typ', 'f.parent_info');

                $defaultorder = 1;
                $defaultorderdesc = 0;

	            $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',f.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?action=edit&module=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a></td></tr></table>";
                $menu = null;

                $linkstart = '<table cellpadding=0 cellspacing=0><tr><td nowrap><a href="index.php?module=fibu_buchungen&action=edit&';
                $linkend = '"><img src="./themes/'.$app->Conf->WFconf['defaulttheme'].'/images/forward.svg" border=0></a></td></tr></table>';

                $id = $app->Secure->GetGET('id');

                $sql = "SELECT SQL_CALC_FOUND_ROWS 
                    f.id, 
                    $dropnbox, 
                    ".$app->erp->FormatDate('f.datum').",
                    ".$app->erp->FormatUCfirst('f.typ').",
                    f.info,
                    CONCAT('".$linkstart."','direction=von&id=".$id."&doc_typ=',f.typ,'&doc_id=',f.id,'".$linkend."'),
                    CONCAT('".$linkstart."','direction=nach&id=".$id."&doc_typ=',f.typ,'&doc_id=',f.id,'".$linkend."'),
                    f.id,
                    f.id 
                FROM fibu_objekte f
                ";

                $where = "1";
                $count = "SELECT count(id) FROM fibu_objekte";
//                $groupby = "";

            break;       
            case 'fibu_buchungen_zuordnen':

                $allowed['fibu_buchungen_zuordnung'] = array('list');
                $heading = array('','','Datum','Typ', 'Info', 'Betrag', 'W&auml;hrung', 'Buchungsbetrag','Vorschlag', 'Men&uuml;');
                $width = array(  );

                $findcols = array('id','auswahl','datum','typ','objektlink','saldo','waehrung','saldo','vorschlag','doc');
                $searchsql = array('objektlink','vorschlag');

                $defaultorder = 1;
                $defaultorderdesc = 0;

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=fibu_buchungen&action=einzelzuordnen&doc=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a></td></tr></table>";

                $linkstart = '<table cellpadding=0 cellspacing=0><tr><td nowrap><a href="index.php?module=fibu_buchungen&action=edit&';
                $linkend = '"><img src="./themes/'.$app->Conf->WFconf['defaulttheme'].'/images/forward.svg" border=0></a></td></tr></table>';
             
                $typ = $this->app->User->GetParameter('fibu_buchungen_doc_typ');
          
                $objektlink = array (
                    '<a href=\"index.php?action=edit&module=',
                    ['sql' => 'fb.typ'],
                    '&id=',
                    ['sql' => 'fb.id'],
                    '">',
                    ['sql' => 'fo.info'],
                    '</a>'
                );                           

                $check_sql = "(fo.info <> '' AND salden.saldonum = -SUM(fbd.betrag))";

                $auswahl = array (
                    '<input type=\"text\" name=\"ids[]\" value=\"',
                    ['sql' => 'salden.typ'],
                    '_',
                    ['sql' => 'salden.id'],
                    '" hidden/>',                    
                    '<input type=\"checkbox\" name=\"auswahl[]\" value="',
                    ['sql' => 'salden.typ'],
                    '_',
                    ['sql' => 'salden.id'],
                    '"',
                    ['sql' => "if(".$check_sql.",'checked','')"],
                    ' />'
                );              

                $vorschlaege = array (
                    '<a href=\"index.php?action=edit&module=',
                    ['sql' => 'COALESCE(fo.typ,\'\')'],
                    '&id=',
                    ['sql' => 'COALESCE(fo.id,\'\')'],
                    '\">',
                    ['sql' => $this->app->erp->FormatUCfirst('COALESCE(fo.typ,\'\')')],
                    ' ',
                    ['sql' => 'COALESCE(fo.info,\'\')'],
                    ' ',
                    ['sql' => "if (SUM(fbd.betrag) IS NULL,'',CONCAT('(Saldo ',".$this->app->erp->FormatMenge('SUM(fbd.betrag)',2).",', Diff. ',".$this->app->erp->FormatMenge('SUM(fbd.betrag)+saldonum',2).",', ',".$this->app->erp->FormatMenge('(SUM(fbd.betrag)+saldonum)/SUM(fbd.betrag)*100',0).",'%)'))"],
                    '</a> ',
                    '<input type="text" name="vorschlaege[]" value="',
                    ['sql' => 'COALESCE(fo.typ,\'\')'],
                    '_',
                    ['sql' => 'COALESCE(fo.id,\'\')'],
                    '" hidden/>'
                );                                  
                
//                    ['sql' => "if (SUM(fbd.betrag) IS NULL,'',CONCAT('(Saldo ',".$this->app->erp->FormatMenge('SUM(fbd.betrag)',2).",', Diff. ',".$this->app->erp->FormatMenge('SUM(fbd.betrag)+saldonum',2).",')'))"],

                $werte = array (
                    '<input type="number" step="0.01" name="werte[]" value="',
                    ['sql' => 'salden.saldonum'],
                    '" min="',
                    ['sql' => 'if(salden.saldonum < 0,salden.saldonum,0)'],
                    '" max="',
                    ['sql' => 'if(salden.saldonum < 0,0,salden.saldonum)'],
                    '"/>'
                );                       

                $waehrungen = array (
                    ['sql' => 'salden.waehrung'],
                    '<input type="text" name="waehrungen[]" value="',
                    ['sql' => 'salden.waehrung'],
                    '" hidden/>'
                );                       

                $doc = array (
                    ['sql' => 'salden.typ'],
                    '_',
                    ['sql' => 'salden.id'],
                );

                $sql = "SELECT
                            '' AS dummy,
                            '' AS dummy2,
                            auswahl,
                            ".$this->app->erp->FormatDate(" datum ").",
                            ".$this->app->erp->FormatUCfirst("typ").",
                            objektlink,
                            saldo,
                            waehrung,
                            wert,
                            vorschlag,
                            doc,                           
                            doc_id,
                            doc_saldo,
                            checked
                        FROM
                            (
                            SELECT
                                '' AS dummy,
                                ".$this->app->erp->ConcatSQL($auswahl)." AS auswahl,
                                salden.datum,
                                salden.typ,
                                salden.id,
                                salden.info,
                                salden.saldo,
                                salden.objektlink,
                                salden.saldonum,
                                ".$this->app->erp->ConcatSQL($vorschlaege)." AS vorschlag,
                                ".$this->app->erp->ConcatSQL($werte)." AS wert,
                                ".$this->app->erp->ConcatSQL($waehrungen)." AS waehrung,
                                fo.typ AS doc_typ,
                                fo.id AS doc_id,
                                fo.info AS doc_info,
                                SUM(fbd.betrag) as doc_saldo,
                                if(".$check_sql.",'1','0') AS checked,
                                ".$this->app->erp->ConcatSQL($doc)." AS doc                              
                            FROM
                                (
                                SELECT
                                    fb.datum,
                                    fb.typ,
                                    fb.id,
                                    fo.info,
                                    ".$this->app->erp->ConcatSQL($objektlink)." AS objektlink,
                                    ".$this->app->erp->FormatMenge('SUM(COALESCE(fb.betrag,0))',2)." AS saldo,
                                    SUM(betrag) AS saldonum,
                                    fb.waehrung
                                FROM
                                    `fibu_buchungen_alle` fb
                                INNER JOIN fibu_objekte fo ON
                                    fb.typ = fo.typ AND fb.id = fo.id
                                WHERE
                                    (
                                        fb.typ = '".$typ."' OR '".$typ."' = ''
                                    )
                                GROUP BY
                                    fb.typ,
                                    fb.id,
                                    fb.waehrung
                            ) salden
                        LEFT JOIN(
                            SELECT
                                fo.typ,
                                fo.id,
                                fo.info,
                                SUM(fob.betrag) as doc_saldo
                            FROM
                                fibu_objekte fo  
                            LEFT JOIN
                                fibu_buchungen_alle fob
                            ON
                                fo.typ = fob.typ AND fo.id = fob.id                          
                            WHERE fo.is_beleg = 1
                            GROUP BY
                                fo.typ,
                                fo.id,
                                fo.info
                        ) AS fo
                        ON
                            salden.info LIKE CONCAT('%', fo.info, '%') AND salden.typ <> fo.typ AND fo.info <> ''
                        LEFT JOIN
                            fibu_buchungen_alle fbd
                        ON
                            fbd.typ = fo.typ AND fbd.id = fo.id
                        WHERE
                            salden.saldonum <> 0
                        GROUP BY
                            salden.typ,
                            salden.id,
                            fo.typ,
                            fo.id    
                        ) AS erg  
                ";

                $where = "1";

                // Toggle filters 
                $this->app->Tpl->Add('JQUERYREADY', "$('#vorschlagfilter').click( function() { fnFilterColumn1( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#checkedfilter').click( function() { fnFilterColumn2( 0 ); } );");

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
                   $where .= " AND doc_id IS NOT NULL"; 
                } else {
                }          

                $more_data2 = $this->app->Secure->GetGET("more_data2");
                if ($more_data2 == 1) {
                   $where .= " AND checked = 1"; 
                } else {
                }                        

                // END Toggle filters


//                $count = "SELECT count(DISTINCT id) FROM fibu_buchungen_alle WHERE $where";
                $groupby = "GROUP BY typ, id";

//echo($sql." WHERE ".$where." ".$groupby);


            break;    
            case 'fibu_buchungen_einzelzuordnen':

                $allowed['fibu_buchungen_einzelzuordnen'] = array('list');
                $heading = array('','Datum','Typ', 'Info', '&Uuml;bergeordnet', 'Saldo','W&auml;hrung','Berechnet','Buchungsbetrag', 'Men&uuml;');
                $width = array(  );

                $findcols = array('fo.id','fo.datum','fo.typ','fo.info','fo.parent_info','fba.betrag','waehrung','fba.betrag','fba.betrag','fo.id');
                $searchsql = array('fo.typ','fo.info','fo.parent_info');

                $defaultorder = 1;
                $defaultorderdesc = 0;

//                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=fibu_buchungen&action=einzelzuordnen&doc=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a></td></tr></table>";

                $linkstart = '<table cellpadding=0 cellspacing=0><tr><td nowrap><a href="index.php?module=fibu_buchungen&action=edit&';
                $linkend = '"><img src="./themes/'.$app->Conf->WFconf['defaulttheme'].'/images/forward.svg" border=0></a></td></tr></table>';
             
                $doc_typ = $this->app->User->GetParameter('fibu_buchungen_doc_typ');
                $doc_id = $this->app->User->GetParameter('fibu_buchungen_doc_id');
                $abschlag = $this->app->User->GetParameter('fibu_buchungen_abschlag');   
                if (!is_numeric($abschlag)) {
                    $abschlag = 0;
                }
                    
                $multifilter = $this->app->User->GetParameter('fibu_buchungen_multifilter');                  

                if (!empty($multifilter)) {
                    $multifilter = $this->app->DB->real_escape_string($multifilter);
                    $multifilter = str_replace(',',' ',$multifilter);
                    $multifilter = str_replace(';',' ',$multifilter);
                    $multifilter = str_replace('\r',' ',$multifilter);
                    $multifilter = str_replace('\n',' ',$multifilter);
                    $multifilter_array = explode(' ',$multifilter." ");
                }
    
                $auswahl = array (
                    '<input type=\"text\" name=\"ids[]\" value=\"',
                    ['sql' => 'fo.typ'],
                    '_',
                    ['sql' => 'fo.id'],
                    '" hidden/>',                    
                    '<input type=\"checkbox\" name=\"auswahl[]\" value="',
                    ['sql' => 'fo.typ'],
                    '_',
                    ['sql' => 'fo.id'],
                    '"',
                    ' />'
                );              

                $objektlink = array (
                    '<a href=\"index.php?action=edit&module=',
                    ['sql' => 'fo.typ'],
                    '&id=',
                    ['sql' => 'fo.id'],
                    '">',
                    ['sql' => 'fo.info'],
                    '</a>'
                );       

                $parentlink = array (
                    '<a href=\"index.php?action=edit&module=',
                    ['sql' => 'fo.parent_typ'],
                    '&id=',
                    ['sql' => 'fo.parent_id'],
                    '">',
                    ['sql' => 'fo.parent_info'],
                    '</a>'
                );                  

                $calculated = 'CONVERT(COALESCE(-SUM(fba.betrag*(1-('.$abschlag.'/100))),0),DECIMAL(12,2))';

                $werte = array (
                    '<input type="number" step="0.01" name="werte[]" value="',
                    ['sql' => $calculated],
/*                    '" min="',
                    ['sql' => 'if(COALESCE(-SUM(fba.betrag),0) < 0,COALESCE(-SUM(fba.betrag),0),0)'],
                    '" max="',
                    ['sql' => 'if(COALESCE(-SUM(fba.betrag),0) < 0,0,COALESCE(-SUM(fba.betrag),0))'],*/
                    '"/>'
                );        

                $sql = "SELECT 
                            '',
                            ".$this->app->erp->ConcatSQL($auswahl)." AS auswahl,
                            ".$this->app->erp->FormatDate("fo.datum")." as datum,
                            ".$this->app->erp->FormatUCfirst("fo.typ")." as typ,
                            ".$this->app->erp->ConcatSQL($objektlink)." AS info,                        
                            ".$this->app->erp->ConcatSQL($parentlink)." AS parent_info,                        
                            ".$this->app->erp->FormatMenge('SUM(fba.betrag)',2)." as saldonum,
                            waehrung,
                            ".$this->app->erp->FormatMenge($calculated,2)." as calculated,
                            ".$this->app->erp->ConcatSQL($werte)." AS werte
                        FROM
                            fibu_objekte fo
                        LEFT JOIN
                            fibu_buchungen_alle fba
                        ON                         
                            fba.typ = fo.typ AND fba.id = fo.id                  
                ";

                $where = "fo.typ <> '".$doc_typ."'";

                if (!empty($multifilter_array)) {
                    $where .= " AND fo.info IN ('".implode("','",$multifilter_array)."')";      
                }        

//                $count = "SELECT count(DISTINCT id) FROM fibu_buchungen_alle WHERE $where";

                $groupby = "GROUP BY fo.id, fo.typ";

//echo($sql." WHERE ".$where." ".$groupby);

                $sumcol= array(6,8);

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

    function fibu_rebuild_tables() {
        $sql = "DROP TABLE IF EXISTS `fibu_buchungen_alle`";
        $this->app->DB->Update($sql);
        $sql = "DROP VIEW IF EXISTS `fibu_buchungen_alle`";
        $this->app->DB->Update($sql);
        $sql = "CREATE TABLE `fibu_buchungen_alle` AS SELECT * FROM `fibu_buchungen_alle_view`";
        $this->app->DB->Update($sql);

        $sql = "DROP TABLE IF EXISTS `fibu_objekte`";
        $this->app->DB->Update($sql);
        $sql = "DROP VIEW IF EXISTS `fibu_objekte`";
        $this->app->DB->Update($sql);
        $sql = "CREATE TABLE `fibu_objekte` AS SELECT * FROM `fibu_objekte_view`";
        $this->app->DB->Update($sql);
    }
    
    function fibu_buchungen_list() {
        $this->app->erp->MenuEintrag("index.php?module=fibu_buchungen&action=list", "&Uuml;bersicht");
//        $this->app->erp->MenuEintrag("index.php?module=fibu_buchungen&action=create", "Neu anlegen");

        $startdatum = $this->app->erp->Firmendaten('fibu_buchungen_startdatum');

        if (empty($startdatum)) {
            $msg .= '<div class="error">Startdatum <a href="index.php?module=firmendaten&action=edit#tabs-8">("Buchungen erzeugen ab Datum")</a> in den Firmendaten nicht gesetzt.</div>';
        }

        $submit = $this->app->Secure->GetPOST('submit');
        if ($submit == 'neuberechnen') {
            $this->fibu_rebuild_tables();
            $msg .= "<div class=\"info\">Buchungen wurden neu berechnet.</div>";
        }

        // For transfer to tablesearch    
        $doc_typ = $this->app->Secure->GetGET('doc_typ');
        $doc_id = $this->app->Secure->GetGET('doc_id');

        $this->app->User->SetParameter('fibu_buchungen_doc_typ', $doc_typ);
        $this->app->User->SetParameter('fibu_buchungen_doc_id', $doc_id);

        $this->app->YUI->TableSearch('TAB1', 'fibu_buchungen_salden', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->YUI->TableSearch('TAB2', 'fibu_buchungen_list', "show", "", "", basename(__FILE__), __CLASS__);

        if (!empty($msg)) {
            $this->app->Tpl->Set('MESSAGE', $msg);
        }

        $this->app->Tpl->Parse('PAGE', "fibu_buchungen_list.tpl");
    }    

    public function fibu_buchungen_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
//        $this->app->DB->Delete("DELETE FROM `fibu_buchungen` WHERE `id` = '{$id}'");        
//        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->fibu_buchungen_list();
    } 

    /*
     * Edit fibu_buchungen item
    */
        
    function fibu_buchungen_edit() {
        $id = $this->app->Secure->GetGET('id');
       
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('artikel',$id))
        {
          return;
        }   

        // Assoc?
        $direction = $this->app->Secure->GetGET('direction');
        $doc_typ = $this->app->Secure->GetGET('doc_typ');
        $doc_id = $this->app->Secure->GetGET('doc_id');
        if (in_array($direction,array('von','nach'))) {
            $sql = "SELECT typ, id FROM fibu_objekte WHERE typ = '".$doc_typ."' AND id = '".$doc_id."'";

            $result = $this->app->DB->SelectArr($sql);

            if (!empty($result)) {
                $sql = "UPDATE fibu_buchungen SET ".$direction."_typ = '".$doc_typ."', ".$direction."_id = '".$doc_id."' WHERE id = '".$id."'";
                $this->app->DB->Update($sql);
            }
        }
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=fibu_buchungen&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=fibu_buchungen&action=list#tabs-2", "Zur&uuml;ck zur &Uuml;bersicht");
        $id = $this->app->Secure->GetGET('id');
        $input = $this->GetInput();
        $submit = $this->app->Secure->GetPOST('submit');
                
        if (empty($id)) {
            $this->fibu_buchungen_list();
            return;
        } 

        if ($submit != '')
        {

            // Write to database
            
            // Add checks here
            $input['benutzer'] = $this->app->User->GetId();
            $input['zeit'] = date("Y-m-d H:i");
            $input['betrag'] = $this->app->erp->ReplaceBetrag(true,$input['betrag']);
            $input['internebemerkung'] = $this->app->DB->real_escape_string($input['internebemerkung']);

            if (empty($input['datum'])) {
                $input['datum'] = date("Y-m-d");
            } else {
                $input['datum'] = $this->app->erp->ReplaceDatum(true,$input['datum'],true);
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

            $sql = "INSERT INTO fibu_buchungen (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=fibu_buchungen&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
            $this->fibu_rebuild_tables();
        }

    
        // Load values again from database
     	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',f.id,'\" />') AS `auswahl`";

        $sql = "
            SELECT SQL_CALC_FOUND_ROWS f.id,
                $dropnbox,
                f.von_typ,
                f.von_id,
                f.nach_typ,
                f.nach_id,
                ".$this->app->erp->FormatMenge('f.betrag',2)." AS betrag,
                f.waehrung,
                adresse.name as benutzer,
                ".$this->app->erp->FormatDateTime('f.zeit','zeit').",
                f.internebemerkung,
                f.id,
                ".$this->app->erp->FormatDate('f.datum','datum').",
                fvon.info AS von_info,
                fnach.info AS nach_info
            FROM 
                fibu_buchungen f"." 
            LEFT JOIN
                fibu_objekte fvon
            ON
                f.von_typ = fvon.typ AND f.von_id = fvon.id
            LEFT JOIN
                fibu_objekte fnach
            ON
                f.nach_typ = fnach.typ AND f.nach_id = fnach.id
            LEFT JOIN 
                user
            ON
                f.benutzer = user.id
            LEFT JOIN
                adresse
            ON
                user.adresse = adresse.id
            WHERE 
                f.id=$id
            
        ";

        $result = $this->app->DB->SelectArr($sql);             

        $this->app->erp->ReplaceDatum(false,$result[0]['zeit'],false);

        $result[0]['internebemerkung'] = htmlentities($result[0]['internebemerkung']);

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

        $this->app->Tpl->Set('VON',ucfirst($result[0]['von_typ'])." ".$result[0]['von_info']);
        $this->app->Tpl->Set('NACH',ucfirst($result[0]['nach_typ'])." ".$result[0]['nach_info']);

        $this->app->Tpl->Set('WAEHRUNG',$this->app->erp->getSelectAsso($this->app->erp->GetWaehrung(), $result[0]['waehrung_von']));            

        $this->app->YUI->DatePicker("datum");
        $this->app->Tpl->Set('DATUM',$this->app->erp->ReplaceDatum(false,$result[0]['datum'],true));

        $this->app->YUI->TableSearch('TAB1', 'fibu_buchungen_wahl', "show", "", "", basename(__FILE__), __CLASS__);

        $this->app->Tpl->Parse('PAGE', "fibu_buchungen_edit.tpl");
    }
     
    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['von_typ'] = $this->app->Secure->GetPOST('von_typ');
	$input['von_id'] = $this->app->Secure->GetPOST('von_id');
	$input['nach_typ'] = $this->app->Secure->GetPOST('nach_typ');
	$input['nach_id'] = $this->app->Secure->GetPOST('nach_id');
	$input['betrag'] = $this->app->Secure->GetPOST('betrag');
	$input['waehrung'] = $this->app->Secure->GetPOST('waehrung');
	$input['benutzer'] = $this->app->Secure->GetPOST('benutzer');
	$input['datum'] = $this->app->Secure->GetPOST('datum');
	$input['internebemerkung'] = $this->app->Secure->GetPOST('internebemerkung');
	

        return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);        
        
        $this->app->Tpl->Set('VON_TYP', $input['von_typ']);
	$this->app->Tpl->Set('VON_ID', $input['von_id']);
	$this->app->Tpl->Set('NACH_TYP', $input['nach_typ']);
	$this->app->Tpl->Set('NACH_ID', $input['nach_id']);
	$this->app->Tpl->Set('BETRAG', $input['betrag']);
	$this->app->Tpl->Set('WAEHRUNG', $input['waehrung']);
	$this->app->Tpl->Set('BENUTZER', $input['benutzer']);
	$this->app->Tpl->Set('ZEIT', $input['zeit']);
	$this->app->Tpl->Set('INTERNEBEMERKUNG', $input['internebemerkung']);
	
    }


    function fibu_buchungen_buchen(string $von_typ, int $von_id, string $nach_typ, int $nach_id, $betrag, string $waehrung, $datum, string $internebemerkung) {
        $sql = "INSERT INTO `fibu_buchungen` (`von_typ`, `von_id`, `nach_typ`, `nach_id`, `datum`, `betrag`, `waehrung`, `benutzer`, `zeit`, `internebemerkung`) VALUES ('".$von_typ."','".$von_id."','".$nach_typ."', '".$nach_id."', '".$datum."', '".$betrag."', '".$waehrung."', '".$this->app->User->GetID()."','".date("Y-m-d H:i")."', '".$internebemerkung."')";
        $this->app->DB->Insert($sql);      
    }    

    function fibu_buchungen_zuordnen() {

        $submit = $this->app->Secure->GetPOST('submit');
        if ($submit == 'neuberechnen') {
            $this->fibu_rebuild_tables();

            $msg = "<div class=\"info\">Buchungen wurden neu berechnet.</div>";
        }

        if ($submit == 'BUCHEN') {
                   
            // Process multi action
            $ids = $this->app->Secure->GetPOST('ids');
            $werte = $this->app->Secure->GetPOST('werte');
            $waehrungen = $this->app->Secure->GetPOST('waehrungen');
            $auswahl = $this->app->Secure->GetPOST('auswahl');
            $vorschlaege = $this->app->Secure->GetPOST('vorschlaege');
            $aktion = $this->app->Secure->GetPOST('sel_aktion');
            $sachkonto = $this->app->Secure->GetPOST('sachkonto');

            $account_id = null;
            if (!empty($sachkonto)) {
                $sachkonto_kennung = explode(' ',$sachkonto)[0];
                $account_id = $this->app->DB->SelectArr("SELECT id from kontorahmen WHERE sachkonto = '".$sachkonto_kennung."'")[0]['id'];               
            } 
     
            if (!empty($auswahl)) {
                foreach ($ids as $id) {
                  
                    $key_ids = array_search($id,$ids);
                    $key_auswahl = array_search($id,$auswahl);                

                    if ($key_auswahl !== false) {
            
                        $von = explode('_',$id);
                        $von_typ = strtolower($von[0]);
                        $von_id = (int) $von[1];

                        $betrag = $werte[$key_ids];
    
                        $waehrung = $waehrungen[$key_ids];

                        $datum = $this->app->DB->SelectArr("SELECT datum FROM fibu_buchungen_alle WHERE typ='".$von_typ."' AND id = '".$von_id."'")[0]['datum']; // Get relevant date of source doc

                        $doc_id = null;                      
                        if ($vorschlaege[$key_ids] != '_') {                       
                            $doc = $vorschlaege[$key_ids];
                            $doc = explode('_',$doc);
                            $doc_typ = strtolower($doc[0]);
                            $doc_id = (int) $doc[1];
                        } 

                        switch ($aktion) {
                            case 'vorschlag':
                                if ($doc_id) {              
//                                    $sql = "INSERT INTO `fibu_buchungen` (`von_typ`, `von_id`, `nach_typ`, `nach_id`, `datum`, `betrag`, `waehrung`, `benutzer`, `zeit`, `internebemerkung`) VALUES ('".$von_typ."','".$von_id."','".$doc_typ."', '".$doc_id."', '".$datum."', '".-$betrag."', '".$waehrung."', '".$this->app->User->GetID()."','".date("Y-m-d H:i")."', '')";
//                                    echo($sql."\n");
//                                    $this->app->DB->Insert($sql);      
                                    $this->fibu_buchungen_buchen($von_typ, $von_id, $doc_typ, $doc_id, -$betrag, $waehrung, $datum, '');
                                }
                            break;
                            case 'sachkonto':
//                                $sql = "INSERT INTO `fibu_buchungen` (`von_typ`, `von_id`, `nach_typ`, `nach_id`, `datum`, `betrag`, `waehrung`, `benutzer`, `zeit`, `internebemerkung`) VALUES ('".$von_typ."','".$von_id."','kontorahmen', '".$account_id."', '".$datum."', '".-$betrag."', '".$waehrung."', '".$this->app->User->GetID()."','".date("Y-m-d H:i")."', '')";
//                                    echo($sql."\n");
//                                $this->app->DB->Insert($sql);  
                                    $this->fibu_buchungen_buchen($von_typ, $von_id, 'kontorahmen', $account_id, -$betrag, $waehrung, $datum, '');
                            break;
                            case 'vorschlag_diff_sachkonto':
                                if ($doc_id) {              
                                    // Retrieve counter doc saldo                                
                                    $doc_saldo = $this->app->erp->GetSaldoDokument($doc_id, $doc_typ);                    
//                                    $sql = "INSERT INTO `fibu_buchungen` (`von_typ`, `von_id`, `nach_typ`, `nach_id`, `datum`, `betrag`, `waehrung`, `benutzer`, `zeit`, `internebemerkung`) VALUES ('".$von_typ."','".$von_id."','".$doc_typ."', '".$doc_id."', '".$datum."', '".-$betrag."', '".$waehrung."', '".$this->app->User->GetID()."','".date("Y-m-d H:i")."', '')";
//                                    echo($sql."\n");
//                                    $this->app->DB->Insert($sql);      
                                    $this->fibu_buchungen_buchen($von_typ, $von_id, $doc_typ, $doc_id, -$betrag, $waehrung, $datum, '');
                                }

                                if (!empty($doc_saldo) && ($doc_saldo['waehrung'] == $waehrung) && ($account_id !== null)) {
                                    $diff = $betrag+$doc_saldo['betrag'];
//                                    $sql = "INSERT INTO `fibu_buchungen` (`von_typ`, `von_id`, `nach_typ`, `nach_id`, `datum`, `betrag`, `waehrung`, `benutzer`, `zeit`, `internebemerkung`) VALUES ('".$doc_typ."','".$doc_id."','kontorahmen', '".$account_id."', '".$datum."', '".-$diff."', '".$waehrung."', '".$this->app->User->GetID()."','".date("Y-m-d H:i")."', '')";
//                                    echo($sql."\n");
//                                    $this->app->DB->Insert($sql);  
                                    $this->fibu_buchungen_buchen($doc_typ, $doc_id, 'kontorahmen', $account_id, -$diff, $waehrung, $datum, '');
                                } else {
                                    $msg .= "<div class=\"warning\">Gegensaldo wurde nicht gebucht. ".count($doc_saldo)." ".$doc_saldo[0]['waehrung']."</div>";
                                }
                            break;
                        }                 
                    } // auswahl
                } // foreach
            } else {    // auswahl
                $msg .= "<div class=\"warning\">Keine Posten ausgew&auml;hlt.</div>";
            } 
        } // submit

        $this->fibu_rebuild_tables();

        // For transfer to tablesearch    
        $doc_typ = $this->app->Secure->GetGET('typ');
        $this->app->User->SetParameter('fibu_buchungen_doc_typ', $doc_typ);

        $this->app->erp->Headlines('Buchhaltung','zuordnen '.strtoupper($doc_typ));

        $this->app->YUI->TableSearch('TAB1', 'fibu_buchungen_zuordnen', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->YUI->TableSearch('TAB2', 'fibu_buchungen_list', "show", "", "", basename(__FILE__), __CLASS__);

        if (!empty($msg)) {
            $this->app->Tpl->Set('MESSAGE', $msg);
        }

        $this->app->YUI->AutoComplete('sachkonto', 'sachkonto');

        $this->app->erp->MenuEintrag("index.php?module=fibu_buchungen&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=fibu_buchungen&action=zuordnen&typ=".$doc_typ, "Einzelsalden");

        $this->app->Tpl->Parse('PAGE', "fibu_buchungen_zuordnen.tpl");
    }

    function fibu_buchungen_einzelzuordnen() {

        $submit = $this->app->Secure->GetPOST('submit');
        if ($submit == 'neuberechnen') {
            $this->fibu_rebuild_tables();

            $msg = "<div class=\"info\">Buchungen wurden neu berechnet.</div>";
        }

        // For transfer to tablesearch    
        $von = $this->app->Secure->GetGET('doc');
        $von = explode('_',$von);
        $von_typ = strtolower($von[0]);
        $von_id = (int) $von[1];
        $this->app->User->SetParameter('fibu_buchungen_doc_typ', $von_typ);
        $this->app->User->SetParameter('fibu_buchungen_doc_id', $von_id);
        $aktion = $this->app->Secure->GetPOST('sel_aktion');
        $sachkonto = $this->app->Secure->GetPOST('sachkonto');
        $abschlag = $this->app->Secure->GetPOST('abschlag');
        $this->app->User->SetParameter('fibu_buchungen_abschlag', $abschlag);
        $multifilter = $this->app->Secure->GetPOST('multifilter');
        $this->app->User->SetParameter('fibu_buchungen_multifilter', $multifilter);

        $account_id = null;
        if (!empty($sachkonto)) {
            $sachkonto_kennung = explode(' ',$sachkonto)[0];
            $account_id = $this->app->DB->SelectArr("SELECT id from kontorahmen WHERE sachkonto = '".$sachkonto_kennung."'")[0]['id'];               
        } 

        $this->app->erp->MenuEintrag("index.php?module=fibu_buchungen&action=zuordnen&typ=".$von_typ, "Zur&uuml;ck");

        $sql = "SELECT doc_typ, doc_id, doc_info, waehrung, sum(betrag) as saldonum,".$this->app->erp->FormatMenge("sum(betrag)",2)." as saldo FROM fibu_buchungen_alle WHERE typ = '".$von_typ."' AND id = '".$von_id."'";
        $von_row = $this->app->DB->SelectArr($sql)[0];
        $von_info = $von_row['doc_info'];
        $von_saldonum = $von_row['saldonum'];
        $von_saldo = $von_row['saldo'];
        $von_waehrung = $von_row['waehrung'];

        $datum = $this->app->DB->SelectArr("SELECT datum FROM fibu_buchungen_alle WHERE typ='".$von_typ."' AND id = '".$von_id."'")[0]['datum']; // Get relevant date of source doc

        if ($submit == 'BUCHEN') {
            // Process multi action
            $count_success = 0;
            $ids = $this->app->Secure->GetPOST('ids');
            $werte = $this->app->Secure->GetPOST('werte');
            $auswahl = $this->app->Secure->GetPOST('auswahl');
     
            if (!empty($auswahl)) {              
                $gesamtnum = 0;
                foreach ($ids as $id) {                   
                    $key_ids = array_search($id,$ids);
                    $key_auswahl = array_search($id,$auswahl);               

                    if ($key_auswahl !== false) {
                        $gesamtnum += $werte[$key_ids];
                    }
                }
                $override = $this->app->Secure->GetPOST('override');
                $diff = round($gesamtnum-$von_saldonum,2);
                $gesamtnum = round($von_saldonum,2);
                $von_saldonum = round($von_saldonum,2);                                

                if (
                    ($von_saldonum < 0 && ($gesamtnum < $von_saldonum)) ||
                    ($von_saldonum > 0 && ($gesamtnum > $von_saldonum))
                ) {
                    $msg .= "<div class=\"error\">Buchungssumme ".$this->app->erp->EUR($gesamtnum)." übersteigt Saldosumme ".$this->app->erp->EUR($von_saldonum).". (Abweichung ".$this->app->erp->EUR((float) $gesamtnum - (float) $von_saldonum).")</div>";
                }
                else if ($diff != 0 && !$override) {
                    $msg .= "<div class=\"error\">Buchungssumme ".$this->app->erp->EUR($gesamtnum)." entspricht nicht Saldosumme ".$this->app->erp->EUR($von_saldonum).". (Abweichung ".$this->app->erp->EUR((float) $gesamtnum - (float) $von_saldonum).")</div>";
                } else {
                    foreach ($ids as $id) {                    
                        $key_ids = array_search($id,$ids);
                        $key_auswahl = array_search($id,$auswahl);                

                        if ($key_auswahl !== false) {                    
                            $doc = explode('_',$id);
                            $doc_typ = strtolower($doc[0]);
                            $doc_id = (int) $doc[1];                     

                            $betrag = $werte[$key_ids];                 

                            switch ($aktion) {
                                case 'buchen':
                                    if ($betrag != 0) {
//                                        $sql = "INSERT INTO `fibu_buchungen` (`von_typ`, `von_id`, `nach_typ`, `nach_id`, `datum`,  `betrag`, `waehrung`, `benutzer`, `zeit`, `internebemerkung`) VALUES ('".$von_typ."','".$von_id."','".$doc_typ."', '".$doc_id."', '".$datum."', '".-$betrag."', '".$von_waehrung."', '".$this->app->User->GetID()."','".date("Y-m-d H:i")."', '')";    
//                                        echo($sql."\n");
//                                        $this->app->DB->Insert($sql);  
                                        $this->fibu_buchungen_buchen($von_typ, $von_id, $doc_typ, $doc_id, -$betrag, $von_waehrung, $datum, '');
                                        $count_success++;                    
                                    }
                                break;
                                case 'buchen_diff_sachkonto':
                                    $doc_saldo = $this->app->erp->GetSaldoDokument($doc_id, $doc_typ);                    
                                    if ($betrag != 0) {
//                                        $sql = "INSERT INTO `fibu_buchungen` (`von_typ`, `von_id`, `nach_typ`, `nach_id`, `datum`,  `betrag`, `waehrung`, `benutzer`, `zeit`, `internebemerkung`) VALUES ('".$von_typ."','".$von_id."','".$doc_typ."', '".$doc_id."', '".$datum."', '".-$betrag."', '".$von_waehrung."', '".$this->app->User->GetID()."','".date("Y-m-d H:i")."', '')";    
//                                          echo($sql."\n");
//                                        $this->app->DB->Insert($sql);  
                                        $this->fibu_buchungen_buchen($von_typ, $von_id, $doc_typ, $doc_id, -$betrag, $von_waehrung, $datum, '');
                                        $count_success++;                    
                                    }

                                    if (!empty($doc_saldo) && ($doc_saldo['waehrung'] == $von_waehrung) && ($account_id !== null)) {
                                        $diff = $betrag+$doc_saldo['betrag'];
//                                        $sql = "INSERT INTO `fibu_buchungen` (`von_typ`, `von_id`, `nach_typ`, `nach_id`, `datum`, `betrag`, `waehrung`, `benutzer`, `zeit`, `internebemerkung`) VALUES ('kontorahmen','".$account_id."','".$doc_typ."', '".$doc_id."', '".$datum."', '".$diff."', '".$von_waehrung."', '".$this->app->User->GetID()."','".date("Y-m-d H:i")."', '')";
//                                        echo($sql."\n");
//                                        $this->app->DB->Insert($sql);  
                                        $this->fibu_buchungen_buchen($doc_typ, $doc_id, 'kontorahmen', $account_id, -$diff, $von_waehrung, $datum, '');
                                    } else {
                                        $msg .= "<div class=\"warning\">Gegensaldo wurde nicht gebucht.</div>";
                                    }

                                break;
                            }                 
                        } 
                    }
                }
                $msg .= "<div class=\"info\">".$count_success." Buchung".(($count_success===1)?'':'en')." durchgef&uuml;hrt.</div>";
                $this->fibu_rebuild_tables();
            } else {
                $msg .= "<div class=\"warning\">Keine Posten ausgew&auml;hlt.</div>";
            }                 
        }   

        // Reload after booking
        $sql = "SELECT fo.info, fb.waehrung, sum(fb.betrag) as saldonum,".$this->app->erp->FormatMenge("sum(fb.betrag)",2)." as saldo FROM fibu_buchungen_alle fb INNER JOIN fibu_objekte fo ON fb.typ = fo.typ AND fb.id = fo.id WHERE fb.typ = '".$von_typ."' AND fb.id = '".$von_id."'";
        $row = $this->app->DB->SelectArr($sql)[0];
        
        $saldonum = $row['saldonum'];
        $saldo = $row['saldo'];
        $waehrung = $row['waehrung'];
        $info = $row['info'];

        $this->app->Tpl->Set('DOC_ZUORDNUNG', ucfirst($von_typ)." ".$info);
        $this->app->Tpl->Set('DOC_SALDO',$saldo." ".$waehrung);
        $this->app->Tpl->Set('ABSCHLAG',$abschlag);

        $this->app->Tpl->Set('MULTIFILTER',str_replace(array('\r\n', '\r', '\n'), ", ", $multifilter));

        $this->app->erp->Headlines('Buchhaltung','Einzelzuordnung '.strtoupper($von_typ));

        $this->app->YUI->TableSearch('TAB1', 'fibu_buchungen_einzelzuordnen', "show", "", "", basename(__FILE__), __CLASS__);

        if (!empty($msg)) {
            $this->app->Tpl->Set('MESSAGE', $msg);
        }

        $this->app->YUI->AutoComplete('sachkonto', 'sachkonto');

        $this->app->Tpl->Parse('PAGE', "fibu_buchungen_einzelzuordnen.tpl");
    }
}
