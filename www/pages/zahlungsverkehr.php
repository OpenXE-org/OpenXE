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
        $this->app->ActionHandler("ueberweisung", "zahlungsverkehr_list");
//        $this->app->ActionHandler("create", "zahlungsverkehr_edit"); // This automatically adds a "New" button
//        $this->app->ActionHandler("edit", "zahlungsverkehr_edit");
        $this->app->ActionHandler("delete", "zahlungsverkehr_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
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
        $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'zahlungsverkehr_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "zahlungsverkehr_list.tpl");
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
