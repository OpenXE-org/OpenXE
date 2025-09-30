<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Zahlungsverkehr {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "zahlungsverkehr_list");
        $this->app->ActionHandler("ueberweisung", "zahlungsverkehr_ueberweisung");
//        $this->app->ActionHandler("create", "zahlungsverkehr_edit"); // This automatically adds a "New" button
//        $this->app->ActionHandler("edit", "zahlungsverkehr_edit");
        $this->app->ActionHandler("delete", "zahlungsverkehr_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    function TableSearch(&$app, $name, $erlaubtevars) {  
   
        switch ($name) {
            case "zahlungsverkehr_ueberweisung":
                $allowed['zahlungsverkehr_ueberweisung'] = array('list');
                $heading = array('','','Typ','Belegnr','RE-Datum','Adresse', 'Nummer', 'RE-Nr', 'Betrag (brutto)', 'W&auml;hrung','Status', 'Ziel','Skonto','Skontoziel', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8);

                $findcols = array(
                    'id',
                    'id',
                    'doc_typ',
                    'belegnr',
                    'datum',
                    'name',
                    'nummer',
                    'rechnung',
                    'cast(betrag AS decimal)',
                    'waehrung',
                    'status',
                    'zahlbarbis',
                    'skonto',
                    'skontobis',
                    'id'
                );

                $searchsql = array(
                    'a.name',
                    'a.lieferantennummer',
                    'v.rechnung',
                    'v.internebemerkung'
                );

                $defaultorder = 12;
                $defaultorderdesc = 0;
                $alignright = array(9);
                $sumcol = array(9);

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',id,'\" />') AS `auswahl`";

//                $moreinfo = true; // Allow drop down details
//                $moreinfoaction = "lieferschein"; // specify suffix for minidetail-URL to allow different minidetails
//                $menucol = 11; // Set id col for moredata/menu
               
                $columns = "
                            id,
                            $dropnbox,
                            doc_typ,
                            belegnr,
                            ".$app->erp->FormatDate("datum").",
                            name,
                            nummer,
                            rechnung,
                            ".$app->erp->FormatMenge('v.betrag',2)." betrag,
                            waehrung,
                            status,
                            ".$app->erp->FormatDate("v.zahlbarbis").",
                            IF(skonto <> 0,CONCAT(".$app->erp->FormatMenge('skonto',0).",'%'),''),
                            IF(skonto <> 0,".$app->erp->FormatDate("skontobis").",'')
                        ";
                $tables = "
                            (
                            SELECT 
                                CONCAT('v',v.id) id,
                                'Verbindlichkeit' doc_typ,
                                v.belegnr,
                                v.rechnungsdatum datum,
                                a.name,
                                CONCAT(a.kundennummer,' ',a.lieferantennummer) nummer,
                                v.rechnung,
                                v.betrag,
                                v.waehrung,                        
                                v.status,
                                v.zahlbarbis,
                                v.skonto,
                                v.skontobis,
                                v.bezahlt
                            FROM verbindlichkeit v
                            LEFT JOIN adresse a ON v.adresse = a.id                        
                            UNION
                            SELECT
                                CONCAT('g',g.id) id,
                                'Gutschrift' doc_typ,
                                g.belegnr,
                                datum,
                                a.name,
                                CONCAT(a.kundennummer,' ',a.lieferantennummer),
                                if(g.rechnung <> 0,g.rechnung,''),
                                g.soll,
                                g.waehrung,                        
                                g.status,
                                DATE_ADD(g.datum, INTERVAL g.zahlungszieltage DAY),
                                g.zahlungszielskonto,
                                DATE_ADD(g.datum, INTERVAL g.zahlungszieltageskonto DAY),
                                g.zahlungsstatus = 'bezahlt'
                            FROM gutschrift g
                            LEFT JOIN adresse a ON g.adresse = a.id
                        ) v
                        ";
                        
                $sql = "SELECT SQL_CALC_FOUND_ROWS ".$columns." FROM ".$tables;
                        
                $where = " v.bezahlt <> 1";

                $where .= " AND v.belegnr <> ''";
                $count = "SELECT count(DISTINCT id) FROM ".$tables." WHERE $where";
                
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

                $where .= " AND v.status <> 'angelegt'";
                // END Toggle filters             

                $moreinfo = true; // Allow drop down details
                $menucol = 1; // For moredata
            break;
            case "zahlungsverkehr_list":
                $allowed['zahlungsverkehr_list'] = array('list');
                $heading = array(
                     '',
                     '',
                     'Status',
                     'Ziel',
                     'Zahlungsweise',
                     'Addresse',
                     'Betrag',
                     'W&auml;hrung',
                     'Beleg',
                     'Informationen',
                     'Daten',
                     'Datum',
                     'Men&uuml;'
                );
                $width = array('1%','1%','1%','1%','1%','1%','1%','1%','5%','10%','30%','5%','1%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8);

                $findcols = array(
                     'z.id',
                     'z.id',
                     'z.payment_status',
                     'z.duedate',
                     'zw.bezeichnung',
                     'a.name',
                     'z.amount',
                     'z.currency',
                     'z.payment_reason',
                     'z.payment_info',
                     'z.payment_json',
                     'z.created_at'
                ); // use 'null' for non-searchable columns

                $searchsql = array('z.returnorder_id',
                     'z.payment_status',
                     'z.payment_account_id',
                     'z.payment_reason',
                     'z.payment_json',
                     'z.payment_info'
                );

                $defaultorder = 1;
                $defaultorderdesc = 0;
                $aligncenter = array();
                $alignright = array(6);
                $numbercols = array();
                $sumcol = array(6);

        		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',z.id,'\" />') AS `auswahl`";

//                $moreinfo = true; // Allow drop down details
//                $moreinfoaction = "lieferschein"; // specify suffix for minidetail-URL to allow different minidetails
//                $menucol = 11; // Set id col for moredata/menu

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=zahlungsverkehr&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $beleglink = array (
                    '<a href="index.php?module=',
                    ['sql' => 'z.doc_typ'],
                    '&action=edit&id=',
                    ['sql' => 'z.doc_id'],
                    '">',
                    ['sql' => 'z.payment_reason'],
                    '</a>'
                );

                $sql = "SELECT SQL_CALC_FOUND_ROWS z.id,
                         $dropnbox,
                         z.payment_status,
                        ".$app->erp->FormatDate("z.duedate").",
                         zw.bezeichnung,
                         a.name,
                         z.amount,
                         z.currency,
                         ".$app->erp->ConcatSQL($beleglink).",
                         z.payment_info,
                         z.payment_json,
                         z.created_at,
                         z.id
                    FROM payment_transaction z
                    LEFT JOIN zahlungsweisen zw ON z.payment_account_id = zw.id
                    LEFT JOIN adresse a ON z.address_id = a.id
                    ";

                $where = "1";

                // Toggle filters
                $app->Tpl->Add('JQUERYREADY', "$('#offene').click( function() { fnFilterColumn1( 0 ); } );");
                $app->Tpl->Add('JQUERYREADY', "$('#exportierte').click( function() { fnFilterColumn2( 0 ); } );");
                $app->Tpl->Add('JQUERYREADY', "$('#fehlgeschlagene').click( function() { fnFilterColumn3( 0 ); } );");

                for ($r = 1;$r <= 3;$r++) {
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


                $statusfilter = array();

                $more_data1 = $app->Secure->GetGET("more_data1");
                if ($more_data1 == 1) {
                    $statusfilter[] = 'angelegt';
                } else {
                }

                $more_data2 = $app->Secure->GetGET("more_data2");
                if ($more_data2 == 1) {
                    $statusfilter[] = 'exportiert';
                }
                else {
                }

                $more_data3 = $app->Secure->GetGET("more_data3");
                if ($more_data3 == 1) {
                    $statusfilter[] = 'fehlgeschlagen';
                }
                else {
                }

                if (!empty($statusfilter)) {
                    $where .= " AND z.payment_status IN ('".implode("','",$statusfilter)."')";
                }

                // END Toggle filters

                $count = "SELECT count(DISTINCT id) FROM payment_transaction z WHERE $where";
//                $groupby = "";

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

    function zahlungsverkehr_list() {
        $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=ueberweisung", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=list", "Transaktionen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'zahlungsverkehr_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "zahlungsverkehr_list.tpl");
    }

    function zahlungsverkehr_ueberweisung() {
        $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=ueberweisung", "Offene");
        $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=list", "Transaktionen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'zahlungsverkehr_ueberweisung', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "zahlungsverkehr_ueberweisung.tpl");
    }

    public function zahlungsverkehr_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        $this->app->DB->Delete("DELETE FROM `payment_transaction` WHERE `id` = '{$id}' AND `payment_status` = 'angelegt'");
        $this->app->Tpl->addMessage('error', 'Der Eintrag wurde gel&ouml;scht');
        $this->zahlungsverkehr_list();
    }

    /*
     * Edit zahlungsverkehr item
     * If id is empty, create a new one
     */

    function zahlungsverkehr_edit() {
        $id = $this->app->Secure->GetGET('id');

        // Check if other users are editing this id
        if($this->app->erp->DisableModul('zahlungsverkehr',$id))
        {
          return;
        }

        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
        $id = $this->app->Secure->GetGET('id');
        $input = $this->GetInput();

        // Convert here
    	// $input['prio'] = !empty($this->app->Secure->GetPOST('prio'))?"1":"0";

        $submit = $this->app->Secure->GetPOST('submit');

        if (empty($id)) {
            // New item
            $id = 'NULL';
        }

        if ($submit != '')
        {

            // Write to database

            // Add checks here

    //        $input['projekt'] = $this->app->erp->ReplaceProjekt(true,$input['projekt'],true); // Parameters: Target db?, value, from form?

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

            $sql = "INSERT INTO payment_transaction (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=zahlungsverkehr&action=list&msg=$msg");
            } else {
                $this->app->Tpl->addMessage('success', 'Die Einstellungen wurden erfolgreich &uuml;bernommen.');
            }
        }


        // Load values again from database
        if ($id != 'NULL') {

        	$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',z.id,'\" />') AS `auswahl`";
            $result = $this->app->DB->SelectArr("SELECT SQL_CALC_FOUND_ROWS z.id, $dropnbox, z.returnorder_id, z.payment_status, z.payment_account_id, z.address_id, z.amount, z.currency, z.payment_reason, z.payment_json, z.liability_id, z.payment_transaction_group_id, z.payment_info, z.created_at, z.doc_typ, z.doc_id, z.id FROM payment_transaction z"." WHERE id=$id");

            foreach ($result[0] as $key => $value) {
                $this->app->Tpl->Set(strtoupper($key), $value);
            }

            if (!empty($result)) {
                $zahlungsverkehr_from_db = $result[0];
            } else {
                return;
            }
        }

        /*
         * Add displayed items later
         *

        $this->app->Tpl->Add('KURZUEBERSCHRIFT2', $email);
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);

        $this->app->YUI->AutoComplete("artikel", "artikelnummer");
        $this->app->Tpl->Set('PROJEKT',$this->app->erp->ReplaceProjekt(false,$zahlungsverkehr_from_db['projekt'],false));
      	$this->app->Tpl->Set('PRIO', $zahlungsverkehr_from_db['prio']==1?"checked":"");

         */

        $this->app->Tpl->Parse('PAGE', "zahlungsverkehr_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');

        $input['returnorder_id'] = $this->app->Secure->GetPOST('returnorder_id');
	$input['payment_status'] = $this->app->Secure->GetPOST('payment_status');
	$input['payment_account_id'] = $this->app->Secure->GetPOST('payment_account_id');
	$input['address_id'] = $this->app->Secure->GetPOST('address_id');
	$input['amount'] = $this->app->Secure->GetPOST('amount');
	$input['currency'] = $this->app->Secure->GetPOST('currency');
	$input['payment_reason'] = $this->app->Secure->GetPOST('payment_reason');
	$input['payment_json'] = $this->app->Secure->GetPOST('payment_json');
	$input['liability_id'] = $this->app->Secure->GetPOST('liability_id');
	$input['payment_transaction_group_id'] = $this->app->Secure->GetPOST('payment_transaction_group_id');
	$input['payment_info'] = $this->app->Secure->GetPOST('payment_info');
	$input['created_at'] = $this->app->Secure->GetPOST('created_at');
	$input['doc_typ'] = $this->app->Secure->GetPOST('doc_typ');
	$input['doc_id'] = $this->app->Secure->GetPOST('doc_id');
	

        return $input;
    }
 }
