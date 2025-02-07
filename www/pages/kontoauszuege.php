<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Kontoauszuege {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "kontoauszuege_konto_list");        
        $this->app->ActionHandler("listentries", "kontoauszuege_list");
//        $this->app->ActionHandler("create", "kontoauszuege_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "kontoauszuege_edit");
        $this->app->ActionHandler("delete", "kontoauszuege_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "kontoauszuege_konto_list":

                $allowed['konten_list'] = array('list');
                $heading = array('Bezeichnung', 'Kurzbezeichnung', 'Typ', 'Kontostand','Letzter Import', 'Men&uuml;');
//                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8); sdds

                $findcols = array('k.bezeichnung', 'k.kurzbezeichnung', 'k.type', 'k.kontostand','ka.datum', 'k.id');
                $searchsql = array('k.bezeichnung', 'k.kurzbezeichnung', 'k.datevkonto', 'k.blz', 'k.konto', 'k.swift', 'k.iban', 'k.inhaber', 'k.firma','p.abkuerzung');

                $defaultorder = 1;
                $defaultorderdesc = 0;

//                $sumcol = array(5);

		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',k.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=kontoauszuege&action=listentries&kid=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a></td></tr></table>";                            

                $saldolink = array (
                    '<a href=\"index.php?module=fibu_buchungen&action=zuordnen&typ=kontoauszuege',
                    '">',
                    ['sql' => $this->app->erp->FormatMenge('SUM(COALESCE(fb.betrag,0))',2)],
                    '</a>'
                );

                $sql = "SELECT    
                            k.id,
                            k.bezeichnung,
                            k.kurzbezeichnung,
                            ".$this->app->erp->FormatUCfirst('k.type').",
                            ".$this->app->erp->FormatMenge('SUM(COALESCE(ka.soll,0))+k.saldo_betrag',2)." AS kontostand,
                            ".$this->app->erp->FormatDatetime("MIN(ka.importdatum)")." AS datum,
                            k.id
                        FROM
                            konten k
                        LEFT JOIN kontoauszuege ka ON
                            k.id = ka.konto";                        

                $where = " k.aktiv = 1 AND ka.importfehler IS NULL ";

                // Toggle filters
                $app->Tpl->Add('JQUERYREADY', "$('#archiv').click( function() { fnFilterColumn1( 0 ); } );");

                for ($r = 1;$r <= 1;$r++) {
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
                   $where .= "  OR k.aktiv <> 1";
                } else {
                }

//                echo($sql);

//                $count = "SELECT count(DISTINCT id) FROM konten k WHERE $where";
                $groupby = " GROUP BY k.id";

                break;
            case "kontoauszuege_list":
                $allowed['kontoauszuege_list'] = array('list');

                $kontoid = $this->app->User->getParameter('kontoauszuege_konto_id');
                $onlysaldo = $this->app->User->getParameter('kontoauszuege_only_saldo');

                $heading = array('','',   'Importdatum', 'Konto', 'Datum', 'Betrag', 'Waehrung', 'Buchungstext','Interne Bemerkung', 'Saldo', 'Men&uuml;');
                $width = array('1%','1%', '1%',          '10%',   '1%',    '1%',     '1%',       '20%',         '20%',               '1%',    '1%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                $alignright = array(6); 

                $sumcol = array(10);

                $findcols = array('q.id','q.id', 'q.importdatum', 'q.kurzbezeichnung', 'q.buchungdatum', 'q.soll', 'q.waehrung', 'q.buchungstext','q.internebemerkung','q.saldo');
                $searchsql = array('q.kurzbezeichnung', 'q.buchungdatum', 'q.soll', 'q.buchungstext','q.internebemerkung');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',k.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=kontoauszuege&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=kontoauszuege&action=delete&kid=".$kontoid."&onlysaldo=".$onlysaldo."&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                // Toggle filters
                $app->Tpl->Add('JQUERYREADY', "$('#importfehler').click( function() { fnFilterColumn1( 0 ); } );");

                for ($r = 1;$r <= 4;$r++) {
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
                   $subwhere .= "";
                } else {
                   $subwhere .= " AND k.importfehler IS NULL ";
                }
                // END Toggle filters

                $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM ( SELECT 
                            k.id,
                            $dropnbox,
                            ".$app->erp->FormatDateTimeShort('k.importdatum')." AS importdatum,                            
                            (SELECT kurzbezeichnung FROM konten WHERE konten.id = k.konto) as kurzbezeichnung,
                            ".$app->erp->FormatDate('k.buchung')." as buchung,                            
                            IF(
                                k.importfehler,
                                CONCAT(
                                    '<del>',
                                    ".$app->erp->FormatMenge('(k.soll)',2).",
                                    '</del>'
                                ),
                                ".$app->erp->FormatMenge('(k.soll)',2).") AS soll,
                            k.waehrung,
                            k.buchungstext,
                            k.internebemerkung,
                            ".$app->erp->FormatMenge('SUM(fb.betrag)',2)." AS saldo,
                            k.id as menuid,
                            SUM(fb.betrag) AS saldonum,
                            k.buchung AS buchungdatum
                        FROM kontoauszuege k
                        LEFT JOIN fibu_buchungen_alle fb ON
                            fb.id = k.id AND fb.typ = 'kontoauszuege'
                        WHERE k.konto = ".$kontoid.$subwhere."
                        GROUP BY k.id ) AS q
                        ";

                $where = "1";
                if ($onlysaldo) {
                    $where .= " AND saldonum != 0";
                }

//                $count = "SELECT count(DISTINCT id) FROM kontoauszuege k WHERE $where";
//                $groupby = "";

//                echo($sql." WHERE ".$where." ".$groupby);

                break;
            case "kontoauszuege_salden":
                $allowed['kontoauszuege_list'] = array('list');

                $kontoid = $this->app->User->getParameter('kontoauszuege_konto_id');
                $onlysaldo = $this->app->User->getParameter('kontoauszuege_only_saldo');

                $heading = array('','',   'Buchungstext', 'Betrag', 'Waehrung', 'Betrag zuordnen', 'Beleg', 'ID','Beleg-Nr.','', 'Men&uuml;');
                $width = array('1%','1%', '20%',          '1%',     '1%',       '1%',              '1%',    '1%','1%',       '1%','1%'); 

                // columns that are aligned right (numbers etc)
                $alignright = array(6); 

                $sumcol = array(10);

                $findcols = array('q.id','q.id','q.konto', 'q.importdatum', 'q.buchung', 'q.soll', 'q.waehrung', 'q.buchungstext','q.internebemerkung','q.saldo');
                $searchsql = array('q.konto', 'q.buchung', 'q.soll', 'q.buchungstext','q.internebemerkung');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		        $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',q.id,'\"',if(b.doc_belegnr IS NOT NULL,'checked',''),' />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=kontoauszuege&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=kontoauszuege&action=delete&kid=".$kontoid."&onlysaldo=".$onlysaldo."&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";              


                $sql = "SELECT 
                            ".$this->app->erp->FormatUCfirst('typ')."
                            SUM(betrag)
                        FROM `fibu_buchungen_alle`                         
                        ";

                $where = "1";
                if ($onlysaldo) {
                    $where .= " AND q.saldonum != 0";
                }

//                $count = "SELECT count(DISTINCT id) FROM kontoauszuege k WHERE $where";
                $groupby = "GROUP BY typ";

//                echo($sql." WHERE ".$where." ".$groupby);

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
    
    function kontoauszuege_konto_list() {       

        $this->app->erp->MenuEintrag("index.php?module=kontoauszuege&action=list", "&Uuml;bersicht");
//        $this->app->erp->MenuEintrag("index.php?module=kontoauszuege&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'kontoauszuege_konto_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "kontoauszuege_konto_list.tpl");
    }    

    function kontoauszuege_mark_as_error(int $id) : ?string {
        $sql = "SELECT id FROM fibu_buchungen_alle WHERE CONCAT(doc_typ,doc_id) <> CONCAT('kontoauszuege','".$id."') AND typ = 'kontoauszuege' AND id = ".$id;
        $result = $this->app->DB->SelectArr($sql);
        
        if (!empty($result)) {
            return("Es existieren Buchungen, Eintrag wurde nicht als Importfehler markiert!");        
        } else {
            $this->app->DB->Delete("UPDATE `kontoauszuege` SET importfehler = 1 WHERE `id` = '{$id}'");        
            return("Der Eintrag wurde als Importfehler markiert.");        
        }

        return(null);

    }

    function kontoauszuege_list() {

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

            $submit = $this->app->Secure->GetPOST('ausfuehren');

            if ($submit == 'Importfehler') {

                $message = "";

                foreach ($selectedIds as $selectedId) {
                    $result = $this->kontoauszuege_mark_as_error($selectedId);
                    if ($result) {
                        $message = $result;
                    }
                }

                if ($message) {
                    $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">".$message."</div>");
                } else {
                    $this->app->Tpl->Set('MESSAGE', "<div class=\"warning\">Eintr&auml;ge wurden als Importfehler markiert.</div>");        
                }              
            }
        }

        $this->app->erp->MenuEintrag("index.php?module=kontoauszuege&action=list", "&Uuml;bersicht");
//        $this->app->erp->MenuEintrag("index.php?module=kontoauszuege&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $kontoid = $this->app->Secure->GetGET('kid');
        $this->app->User->SetParameter('kontoauszuege_konto_id', $kontoid);   
        $onlysaldo = $this->app->Secure->GetGET('onlysaldo');
        $this->app->User->SetParameter('kontoauszuege_only_saldo', $onlysaldo);   

        if ($onlysaldo) {
            $this->app->Tpl->Set('INFO','Nicht zugeordnete Posten');
        }

        $this->app->YUI->TableSearch('TAB1', 'kontoauszuege_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "kontoauszuege_list.tpl");

    }    

    public function kontoauszuege_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $result = $this->kontoauszuege_mark_as_error($id);

        if ($result) {
            $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">".$result."</div>");
        } else {
            $this->app->Tpl->Set('MESSAGE', "<div class=\"warning\">Der Eintrag wurde als Importfehler markiert.</div>");        
        }
        
        $this->kontoauszuege_list();
    } 

    /*
     * Edit kontoauszuege item
     * If id is empty, create a new one
     */
        
    function kontoauszuege_edit() {
        $id = $this->app->Secure->GetGET('id');
        
        // Check if other users are editing this id
        if($this->app->erp->DisableModul('artikel',$id))
        {
          return;
        }   
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=kontoauszuege&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=kontoauszuege&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
        $id = $this->app->Secure->GetGET('id');
        $input = $this->GetInput();
        $submit = $this->app->Secure->GetPOST('submit');
                
/*        if (empty($id)) {
            // New item
            $id = 'NULL';
        } */

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

            $sql = "INSERT INTO kontoauszuege (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=kontoauszuege&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

    
        // Load values again from database
	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',k.id,'\" />') AS `auswahl`";
        $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS k.id, $dropnbox, k.konto, k.buchung, k.originalbuchung, k.vorgang, k.originalvorgang, k.soll, k.originalsoll, k.haben, k.originalhaben, k.gebuehr, k.originalgebuehr, k.waehrung, k.originalwaehrung, k.fertig, k.datev_abgeschlossen, k.buchungstext, k.gegenkonto, k.belegfeld1, k.bearbeiter, k.mailbenachrichtigung, k.pruefsumme, k.kostenstelle, k.importgroup, k.diff, k.diffangelegt, k.internebemerkung, k.importfehler, k.parent, k.sort, k.doctype, k.doctypeid, k.vorauswahltyp, k.vorauswahlparameter, k.klaerfall, k.klaergrund, k.bezugtyp, k.bezugparameter, k.vorauswahlvorschlag, k.id FROM kontoauszuege k"." WHERE id=$id");

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

        $sql = "SELECT kurzbezeichnung FROM konten WHERE id=".$result[0]['konto'];
        $konto = $this->app->DB->Select($sql);
        $this->app->Tpl->Set('KONTO', $konto);

        $this->app->Tpl->Set('BUCHUNG', $this->app->erp->ReplaceDatum(false,$result[0]['buchung'],false));
        $this->app->Tpl->Set('SOLL', $this->app->erp->ReplaceBetrag(false,$result[0]['soll'],false));

        $this->app->Tpl->Parse('PAGE', "kontoauszuege_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
/*        $input['konto'] = $this->app->Secure->GetPOST('konto');
	$input['buchung'] = $this->app->Secure->GetPOST('buchung');
	$input['originalbuchung'] = $this->app->Secure->GetPOST('originalbuchung');
	$input['vorgang'] = $this->app->Secure->GetPOST('vorgang');
	$input['originalvorgang'] = $this->app->Secure->GetPOST('originalvorgang');
	$input['soll'] = $this->app->Secure->GetPOST('soll');
	$input['originalsoll'] = $this->app->Secure->GetPOST('originalsoll');
	$input['haben'] = $this->app->Secure->GetPOST('haben');
	$input['originalhaben'] = $this->app->Secure->GetPOST('originalhaben');
	$input['gebuehr'] = $this->app->Secure->GetPOST('gebuehr');
	$input['originalgebuehr'] = $this->app->Secure->GetPOST('originalgebuehr');
	$input['waehrung'] = $this->app->Secure->GetPOST('waehrung');
	$input['originalwaehrung'] = $this->app->Secure->GetPOST('originalwaehrung');
	$input['fertig'] = $this->app->Secure->GetPOST('fertig'); 
	$input['datev_abgeschlossen'] = $this->app->Secure->GetPOST('datev_abgeschlossen');
	$input['buchungstext'] = $this->app->Secure->GetPOST('buchungstext');
	$input['gegenkonto'] = $this->app->Secure->GetPOST('gegenkonto');
	$input['belegfeld1'] = $this->app->Secure->GetPOST('belegfeld1');
	$input['bearbeiter'] = $this->app->Secure->GetPOST('bearbeiter');
	$input['mailbenachrichtigung'] = $this->app->Secure->GetPOST('mailbenachrichtigung');
	$input['pruefsumme'] = $this->app->Secure->GetPOST('pruefsumme');
	$input['kostenstelle'] = $this->app->Secure->GetPOST('kostenstelle');
	$input['importgroup'] = $this->app->Secure->GetPOST('importgroup');
	$input['diff'] = $this->app->Secure->GetPOST('diff');
	$input['diffangelegt'] = $this->app->Secure->GetPOST('diffangelegt');
*/	$input['internebemerkung'] = $this->app->Secure->GetPOST('internebemerkung'); 
/*	$input['importfehler'] = $this->app->Secure->GetPOST('importfehler'); 
	$input['parent'] = $this->app->Secure->GetPOST('parent');
	$input['sort'] = $this->app->Secure->GetPOST('sort');
	$input['doctype'] = $this->app->Secure->GetPOST('doctype');
	$input['doctypeid'] = $this->app->Secure->GetPOST('doctypeid');
	$input['vorauswahltyp'] = $this->app->Secure->GetPOST('vorauswahltyp');
	$input['vorauswahlparameter'] = $this->app->Secure->GetPOST('vorauswahlparameter');
	$input['klaerfall'] = $this->app->Secure->GetPOST('klaerfall');
	$input['klaergrund'] = $this->app->Secure->GetPOST('klaergrund');
	$input['bezugtyp'] = $this->app->Secure->GetPOST('bezugtyp');
	$input['bezugparameter'] = $this->app->Secure->GetPOST('bezugparameter');
	$input['vorauswahlvorschlag'] = $this->app->Secure->GetPOST('vorauswahlvorschlag');*/
	

        return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);        
        
        $this->app->Tpl->Set('KONTO', $input['konto']);
	$this->app->Tpl->Set('BUCHUNG', $input['buchung']);
	$this->app->Tpl->Set('ORIGINALBUCHUNG', $input['originalbuchung']);
	$this->app->Tpl->Set('VORGANG', $input['vorgang']);
	$this->app->Tpl->Set('ORIGINALVORGANG', $input['originalvorgang']);
	$this->app->Tpl->Set('SOLL', $input['soll']);
	$this->app->Tpl->Set('ORIGINALSOLL', $input['originalsoll']);
	$this->app->Tpl->Set('HABEN', $input['haben']);
	$this->app->Tpl->Set('ORIGINALHABEN', $input['originalhaben']);
	$this->app->Tpl->Set('GEBUEHR', $input['gebuehr']);
	$this->app->Tpl->Set('ORIGINALGEBUEHR', $input['originalgebuehr']);
	$this->app->Tpl->Set('WAEHRUNG', $input['waehrung']);
	$this->app->Tpl->Set('ORIGINALWAEHRUNG', $input['originalwaehrung']);
	$this->app->Tpl->Set('FERTIG', $input['fertig']);
	$this->app->Tpl->Set('DATEV_ABGESCHLOSSEN', $input['datev_abgeschlossen']);
	$this->app->Tpl->Set('BUCHUNGSTEXT', $input['buchungstext']);
	$this->app->Tpl->Set('GEGENKONTO', $input['gegenkonto']);
	$this->app->Tpl->Set('BELEGFELD1', $input['belegfeld1']);
	$this->app->Tpl->Set('BEARBEITER', $input['bearbeiter']);
	$this->app->Tpl->Set('MAILBENACHRICHTIGUNG', $input['mailbenachrichtigung']);
	$this->app->Tpl->Set('PRUEFSUMME', $input['pruefsumme']);
	$this->app->Tpl->Set('KOSTENSTELLE', $input['kostenstelle']);
	$this->app->Tpl->Set('IMPORTGROUP', $input['importgroup']);
	$this->app->Tpl->Set('DIFF', $input['diff']);
	$this->app->Tpl->Set('DIFFANGELEGT', $input['diffangelegt']);
	$this->app->Tpl->Set('INTERNEBEMERKUNG', $input['internebemerkung']);
	$this->app->Tpl->Set('IMPORTFEHLER', $input['importfehler']);
	$this->app->Tpl->Set('PARENT', $input['parent']);
	$this->app->Tpl->Set('SORT', $input['sort']);
	$this->app->Tpl->Set('DOCTYPE', $input['doctype']);
	$this->app->Tpl->Set('DOCTYPEID', $input['doctypeid']);
	$this->app->Tpl->Set('VORAUSWAHLTYP', $input['vorauswahltyp']);
	$this->app->Tpl->Set('VORAUSWAHLPARAMETER', $input['vorauswahlparameter']);
	$this->app->Tpl->Set('KLAERFALL', $input['klaerfall']);
	$this->app->Tpl->Set('KLAERGRUND', $input['klaergrund']);
	$this->app->Tpl->Set('BEZUGTYP', $input['bezugtyp']);
	$this->app->Tpl->Set('BEZUGPARAMETER', $input['bezugparameter']);
	$this->app->Tpl->Set('VORAUSWAHLVORSCHLAG', $input['vorauswahlvorschlag']);
	
    }

}
