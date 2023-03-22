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
        $this->app->ActionHandler("list", "kontoauszuege_list");        
        $this->app->ActionHandler("create", "kontoauszuege_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "kontoauszuege_edit");
        $this->app->ActionHandler("delete", "kontoauszuege_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "kontoauszuege_list":
                $allowed['kontoauszuege_list'] = array('list');
                $heading = array('','',   'Importdatum', 'Konto', 'Datum', 'Betrag', 'Waehrung', 'Buchungstext','Interne Bemerkung', 'Men&uuml;');
                $width = array('1%','1%', '1%',          '10%',   '1%',    '1%',     '1%',       '20%',         '20%',               '1%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                $alignright = array(6); 

                $findcols = array('k.id','k.id','k.konto', 'k.importdatum', 'k.buchung', 'k.soll', 'k.waehrung', 'k.buchungstext','k.internebemerkung');
                $searchsql = array('k.konto', 'k.buchung', 'k.soll', 'k.buchungstext','k.internebemerkung');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',k.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=kontoauszuege&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=kontoauszuege&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS k.id,
                            $dropnbox,
                            ".$app->erp->FormatDateTimeShort('k.importdatum').",                            
                            (SELECT kurzbezeichnung FROM konten WHERE konten.id = k.konto),
                            ".$app->erp->FormatDate('k.buchung').",                            
                            IF(
                                k.importfehler,
                                CONCAT(
                                    '<del>',
                                    ".$app->erp->FormatMenge('k.soll',2).",
                                    '</del>'
                                ),
                                ".$app->erp->FormatMenge('k.soll',2)."),
                            k.waehrung,
                            k.buchungstext,
                            k.internebemerkung,
                            k.id 
                        FROM kontoauszuege k";

                $where = "1";

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
                   $where .= "";
                } else {
                   $where .= " AND k.importfehler IS NULL ";
                }
                // END Toggle filters

                $count = "SELECT count(DISTINCT id) FROM kontoauszuege k WHERE $where";
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
               $sql = "UPDATE kontoauszuege SET importfehler = 1 WHERE id IN (".implode(",",$selectedIds).")";
               $this->app->DB->Update($sql);
            }
        }

        $this->app->erp->MenuEintrag("index.php?module=kontoauszuege&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=kontoauszuege&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'kontoauszuege_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "kontoauszuege_list.tpl");
    }    

    public function kontoauszuege_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("UPDATE `kontoauszuege` SET importfehler = 1 WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde als Importfehler markiert.</div>");        

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
