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
        //$this->app->ActionHandler("zuordnen", "fibu_buchungen_zuordnen");
        $this->app->ActionHandler("zuordnen", "fibu_buchungen_zuordnen_tablesearch");
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

                $findcols = array('f.id','f.id','f.buchungsart', 'f.typ', 'f.datum', 'f.doc_typ', 'f.betrag', 'f.waehrung');
                $searchsql = array('f.buchungsart', 'f.typ', 'f.datum', 'f.doc_typ', 'f.doc_belegnr');

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
                    CONCAT(".$app->erp->FormatUCfirst('f.doc_typ').",' ',f.doc_belegnr),
                    ".$app->erp->FormatMenge('f.betrag',2).",
                    f.waehrung,
                    CONCAT(f.edit_module,'&id=',f.edit_id) 
                FROM fibu_buchungen_alle f";

                $count = "SELECT count(*) FROM fibu_buchungen_alle WHERE $where";
//                $groupby = "";

                break;       
            case "fibu_buchungen_salden":
                $allowed['fibu_buchungen_salden'] = array('list');
                $heading = array( '',  'Typ','Anzahl', 'Saldo', 'Men&uuml;');
                $width = array(  '1%', '96%', '1%',    '1%',   '1%');

                $findcols = array('','','typ','anzahl','saldo');
                $searchsql = array();

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
                    '',
                    ".$this->app->erp->FormatUCfirst('typ')." AS typ,
                    count(id) anzahl,
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
                $heading = array('',  '',  'Datum', 'Typ', 'Beleg', 'Von','Nach', 'Men&uuml;');
                $width = array(  '1%','1%','1%',  '20%',   '80%',   '1%', '1%',    '%1'   );

                $findcols = array('f.id','f.id','f.typ');
                $searchsql = array('f.buchungsart', 'f.typ', 'f.datum', 'f.doc_typ', 'f.doc_belegnr');

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
                $count = "SELECT count(DISTINCT id) FROM fibu_buchungen_alle WHERE $where";
//                $groupby = "";

            break;       
            case 'fibu_buchungen_zuordnen':

                $allowed['fibu_buchungen_zuordnung'] = array('list');
                $heading = array('','','Datum', 'Info', 'Betrag', 'W&auml;hrung', 'Buchungsbetrag','Vorschlag', 'Men&uuml;');
                $width = array(  );

                $findcols = array('','auswahl','datum','objektlink','saldo','waehrung','buchwert_input','vorschlag');
                $searchsql = array();

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
                    ['sql' => "if(fo.info <> '','checked','')"],
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
                    ['sql' => "if (SUM(fbd.betrag) IS NULL,'',CONCAT('(Saldo ',".$this->app->erp->FormatMenge('SUM(fbd.betrag)',2).",')'))"],
                    '</a> ',
                    '<input type="text" name="vorschlaege[]" value="',
                    ['sql' => 'COALESCE(fo.typ,\'\')'],
                    '_',
                    ['sql' => 'COALESCE(fo.id,\'\')'],
                    '" hidden/>'
                );                                  
                
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
                            datum,
                            objektlink,
                            saldo,
                            waehrung,
                            wert,
                            vorschlag,
                            doc,
                            doc_id
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
                                ".$this->app->erp->ConcatSQL($doc)." AS doc
                            FROM
                                (
                                SELECT
                                    ".$this->app->erp->FormatDate(" fb.datum ")." AS datum,
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
                                fo.info
                            FROM
                                fibu_objekte fo                            
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
                            salden.id    
                        ) AS erg  
                ";

                $where = "1";

                // Toggle filters 
                $this->app->Tpl->Add('JQUERYREADY', "$('#vorschlagfilter').click( function() { fnFilterColumn1( 0 ); } );");

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
                // END Toggle filters


//                $count = "SELECT count(DISTINCT id) FROM fibu_buchungen_alle WHERE $where";
                $groupby = "GROUP BY typ, id";

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

        $submit = $this->app->Secure->GetPOST('submit');
        if ($submit == 'neuberechnen') {
            $this->fibu_rebuild_tables();
            $msg = "<div class=\"info\">Buchungen wurden neu berechnet.</div>";
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
     * If id is empty, create a new one
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
        $this->app->erp->MenuEintrag("index.php?module=fibu_buchungen&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
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
            $input['benutzer'] = $this->app->User->GetId();
            $input['zeit'] = date("Y-m-d H:i");
            $input['betrag'] = $this->app->erp->ReplaceBetrag(true,$input['betrag']);
            $input['zeit'] = date("Y-m-d H:i");

            $input['internebemerkung'] = $this->app->DB->real_escape_string($input['internebemerkung']);

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
	$input['zeit'] = $this->app->Secure->GetPOST('zeit');
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

    function fibu_buchungen_zuordnen_tablesearch() {
           $this->app->erp->MenuEintrag("index.php?module=fibu_buchungen&action=list", "&Uuml;bersicht");
//        $this->app->erp->MenuEintrag("index.php?module=fibu_buchungen&action=create", "Neu anlegen");

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
      
            if (!empty($auswahl)) {
                foreach ($ids as $id) {
                    
                    $key_ids = array_search($id,$ids);
                    $key_auswahl = array_search($id,$auswahl);                

                    if ($key_auswahl !== false && $vorschlaege[$key_ids] != '_') {                    
                        $von = explode('_',$id);
                        $von_typ = strtolower($von[0]);
                        $von_id = (int) $von[1];

                        $doc = $vorschlaege[$key_ids];
                        $doc = explode('_',$doc);
                        $doc_typ = strtolower($doc[0]);
                        $doc_id = (int) $doc[1];

                        $betrag = $werte[$key_ids];
                        $waehrung = $waehrungen[$key_ids];
            
                        $sql = "INSERT INTO `fibu_buchungen` (`von_typ`, `von_id`, `nach_typ`, `nach_id`, `betrag`, `waehrung`, `benutzer`, `zeit`, `internebemerkung`) VALUES ('".$von_typ."','".$von_id."','".$doc_typ."', '".$doc_id."', '".-$betrag."', '".$waehrung."', '".$this->app->User->GetID()."','".          $input['zeit'] = date("Y-m-d H:i")."', '')";
                   
                        $this->app->DB->Insert($sql);  

                    } 
                }
            }
        }

        $this->fibu_rebuild_tables();

        // For transfer to tablesearch    
        $doc_typ = $this->app->Secure->GetGET('typ');
        $this->app->User->SetParameter('fibu_buchungen_doc_typ', $doc_typ);


//        $this->app->YUI->TableSearch('TAB1', 'fibu_buchungen_salden', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->YUI->TableSearch('TAB1', 'fibu_buchungen_zuordnen', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->YUI->TableSearch('TAB2', 'fibu_buchungen_list', "show", "", "", basename(__FILE__), __CLASS__);

        if (!empty($msg)) {
            $this->app->Tpl->Set('MESSAGE', $msg);
        }

        $this->app->Tpl->Parse('PAGE', "fibu_buchungen_zuordnen.tpl");
    }

    function fibu_buchungen_zuordnen() {       

        $this->app->erp->MenuEintrag("index.php?module=fibu_buchungen&action=list", "&Uuml;bersicht");

        $submit = $this->app->Secure->GetPOST('submit');        
        $count_success = 0;
        if ($submit == 'BUCHEN') {
           
            // Process multi action
            $von_typen = $this->app->Secure->GetPOST('fibu_typ');
            $von_ids = $this->app->Secure->GetPOST('fibu_id');
            $betraege = $this->app->Secure->GetPOST('fibu_betrag');
            $waehrungen = $this->app->Secure->GetPOST('fibu_waehrung');
            $objekte = $this->app->Secure->GetPOST('fibu_objekt');
          
            if(!empty($von_ids)) {
                $count = -1;
                foreach ($von_ids as $von_id) {
                    $count++;           
                    if ($von_id > 0) {
                        $von_typ = $von_typen[$count];
                        $objekt = $objekte[$count];
                        $objekt = explode('-',$objekt);
                        $doc_typ = strtolower($objekt[0]);
                        $doc_id = (int) $objekt[1];
                        $betrag = $betraege[$count]; 
                        $betrag = (float) $this->app->erp->ReplaceBetrag(true,$betrag);
                        $waehrung = $waehrungen[$count];
                        if (empty($von_typ) || empty($doc_typ) || empty($doc_id) || empty($betrag) || empty($waehrung)) {
                            continue;
                        }
                        $sql = "INSERT INTO `fibu_buchungen` (`von_typ`, `von_id`, `nach_typ`, `nach_id`, `betrag`, `waehrung`, `benutzer`, `zeit`, `internebemerkung`) VALUES ('".$von_typ."','".$von_id."','".$doc_typ."', '".$doc_id."', '".-$betrag."', '".$waehrung."', '".$this->app->User->GetID()."','".          $input['zeit'] = date("Y-m-d H:i")."', '')";

                        $this->app->DB->Insert($sql);  

                        $count_success++;                    
                    }
                }
                $this->fibu_rebuild_tables();
            }
            $msg .= "<div class=\"info\">".$count_success." Buchung".(($count_success===1)?'':'en')." durchgef&uuml;hrt.</div>";
        }

        $typ = $this->app->Secure->GetGET('typ');      

        $objektlink = array (
                    '<a href=\"index.php?action=edit&module=',
                    ['sql' => 'fb.typ'],
                    '&id=',
                    ['sql' => 'fb.id'],
                    '">',
                    ['sql' => 'fo.info'],
                    '</a>'
                ); 

        $sql = "SELECT
                    salden.datum,
                    salden.typ,
                    salden.id,
                    salden.info,
                    salden.saldo,
                    salden.objektlink,
                    salden.saldonum,
                    salden.waehrung,
                    fo.typ as doc_typ,
                    fo.id as doc_id,
                    fo.info as doc_info
                FROM
                    (
                    SELECT
                        ".$this->app->erp->FormatDate("fb.datum")." as datum,
                        fb.typ,
                        fb.id,
                        fo.info,
                        ".$this->app->erp->ConcatSQL($objektlink)." AS objektlink,
                        ".$this->app->erp->FormatMenge('SUM(COALESCE(fb.betrag,0))',2)."AS saldo,
                        SUM(betrag) AS saldonum,
                        fb.waehrung
                    FROM
                        `fibu_buchungen_alle` fb
                    INNER JOIN fibu_objekte fo ON
                        fb.typ = fo.typ AND fb.id = fo.id
                    WHERE (fb.typ = '".$typ."' OR '".$typ."' = '')
                    GROUP BY
                        fb.typ,
                        fb.id,
                        fb.waehrung
                ) salden
                LEFT JOIN fibu_objekte fo ON
                    salden.info LIKE CONCAT('%', fo.info, '%') 
                        AND 
                    salden.typ <> fo.typ AND fo.info <> ''              
                WHERE
                    salden.saldonum <> 0
                GROUP BY
                    salden.typ,salden.id
                LIMIT 100      
            ";

//        echo($sql);

        $items = $this->app->DB->SelectArr($sql);

        //print_r($items);        

        $et = new EasyTable($this->app);

        $et->headings = array('Datum','Typ','Info','Betrag','Buchbetrag','Zuordnung');

        foreach ($items as $item) {

            $checked = empty($item['doc_typ'])?'':'checked';

            if (empty($item['doc_id'])) {
                $object_identifier = '';
            } else {
                $object_identifier = ucfirst($item['doc_typ'])."-".$item['doc_id']."-".$item['doc_info'];
            }         

            $input_id = 'fibu_object_select_'.$item['id'];
            $object_select = '<input 
                                type="text" 
                                size="40"
                                id="'.$input_id.'"
                                name="fibu_objekt[]"  
                                value="'
                                .$object_identifier.'"/>';            

            if ($item['saldonum'] < 0) {
                $min = $item['saldo'];
                $max = '0';
            } else {
                $max = $item['saldo'];
                $min = '0';
            }

            $row = array(
                $item['datum'],
                ucfirst($item['typ']),
                $item['objektlink'],
                $item['saldo'],
                '<input type="number" step="0.01" size="10" name="fibu_betrag[]" value="'.$item['saldonum'].'" min="'.$min.'" max="'.$max.'"></input>'.$item['waehrung'],                    
                $object_select,
                '<input type="text" name="fibu_typ[]" value="'.$item['typ'].'" hidden/>',
                '<input type="text" name="fibu_id[]" value="'.$item['id'].'" hidden/>',
                '<input type="text" name="fibu_waehrung[]" value="'.$item['waehrung'].'" hidden/>'
            );
            $et->AddRow($row);

            $this->app->YUI->Autocomplete($input_id,'fibu_objekte');

        }      

        $et->DisplayNew('TAB1',"Gegenbuchung","noAction");                       
        $this->app->Tpl->Set('MESSAGE', $msg);
        $this->app->Tpl->Parse('PAGE', "fibu_buchungen_zuordnen.tpl");
    }    

}
