<?php

/*
 * Copyright (c) 2024 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;
use Xentral\Modules\SystemNotification\Service\NotificationMessageData;
use Xentral\Modules\SystemNotification\Service\NotificationService;

class Zahlungsverkehr {


    const UNIFIED_SQL_TABLES =  "
                        (
                            SELECT
                                CONCAT('v',v.id) id,
                                'Verbindlichkeit' doc_typ_name,
                                'verbindlichkeit' doc_typ,
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
                                v.bezahlt,
                                v.zahlungsweise,
                                z.bezeichnung as zahlungsweisebezeichnung,
                                v.adresse,
                                v.id doc_id
                            FROM verbindlichkeit v
                            LEFT JOIN adresse a ON v.adresse = a.id
                            LEFT JOIN zahlungsweisen z ON z.type = v.zahlungsweise
                            UNION
                            SELECT
                                CONCAT('g',g.id) id,
                                'Gutschrift' doc_typ_name,
                                'gutschrift' doc_typ,
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
                                g.zahlungsstatus = 'bezahlt',
                                g.zahlungsweise,
                                z.bezeichnung as zahlungsweisebezeichnung,
                                g.adresse,
                                g.id doc_id
                            FROM gutschrift g
                            LEFT JOIN adresse a ON g.adresse = a.id
                            LEFT JOIN zahlungsweisen z ON z.type = g.zahlungsweise
                        ) belege";

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "zahlungsverkehr_list");
        $this->app->ActionHandler("ueberweisung", "zahlungsverkehr_ueberweisung");
        $this->app->ActionHandler("minidetail", "zahlungsverkehr_minidetail");
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
                $heading = array('','Typ','Belegnr','RE-Datum','Adresse', 'Nummer', 'RE-Nr', 'Betrag (brutto)', 'W&auml;hrung',' Zahlungsweise', 'Bezahlt', 'Ziel', 'Skonto','Skontoziel','Status','Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                // columns that are aligned right (numbers etc)
                // $alignright = array(4,5,6,7,8);

                $findcols = array(
                    'id',
                    'doc_typ',
                    'belegnr',
                    'datum',
                    'name',
                    'nummer',
                    'rechnung',
                    'cast(betrag AS decimal)',
                    'waehrung',
                    'zahlungsweisebezeichnung',
                    'if(bezahlt,\'ja\',\'nein\')',
                    'zahlbarbis',
                    'skonto',
                    'skontobis',
                    'status',
                    'id'
                );

                $searchsql = array(
                    'a.name',
                    'a.lieferantennummer',
                    'v.rechnung',
                    'v.internebemerkung'
                );

                $defaultorder = 13;
                $defaultorderdesc = 0;
                $alignright = array(9);
                $sumcol = array(9);

        		$dropnbox = "CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',id,'\" />') AS `auswahl`";

//                $moreinfo = true; // Allow drop down details
//                $moreinfoaction = "lieferschein"; // specify suffix for minidetail-URL to allow different minidetails
//                $menucol = 11; // Set id col for moredata/menu


                $beleglink = array (
                        '<a href="index.php?module=',
                        ['sql' => 'LOWER(doc_typ)'],
                        '&action=edit&id=',
                        ['sql' => 'doc_id'],
                        '">',
                        ['sql' => 'belegnr'],
                        '</a>'
                );

                $columns = "
                            id,
                            $dropnbox,
                            doc_typ_name,
                            ".$this->app->erp->ConcatSQL($beleglink).",
                            ".$app->erp->FormatDate("datum").",
                            name,
                            nummer,
                            rechnung,
                            ".$app->erp->FormatMenge('betrag',2)." betrag,
                            waehrung,
                            zahlungsweisebezeichnung,
                            if(bezahlt,'ja','nein'),
                            ".$app->erp->FormatDate("zahlbarbis").",
                            IF(skonto <> 0,CONCAT(".$app->erp->FormatMenge('skonto',0).",'%'),''),
                            IF(skonto <> 0,".$app->erp->FormatDate("skontobis").",''),
                            status,
                            id
                        ";
                $tables = self::UNIFIED_SQL_TABLES;

                $sql = "SELECT SQL_CALC_FOUND_ROWS ".$columns." FROM ".$tables;
                $where .= "1";
                $where .= " AND belegnr <> ''";

                // Toggle filters
                $app->Tpl->Add('JQUERYREADY', "$('#bezahlt').click( function() { fnFilterColumn1( 0 ); } );");
                $app->Tpl->Add('JQUERYREADY', "$('#imzahllauf').click( function() { fnFilterColumn2( 0 ); } );");
                $app->Tpl->Add('JQUERYREADY', "$('#stornierte').click( function() { fnFilterColumn3( 0 ); } );");
                $app->Tpl->Add('JQUERYREADY', "$('#abgeschlossen').click( function() { fnFilterColumn4( 0 ); } );");

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

                $statusfilter = array('freigegeben');

                $more_data1 = $app->Secure->GetGET("more_data1");
                if ($more_data1 == 1) {
                } else {
                    $where .= " AND belege.bezahlt <> 1";
                }

                $more_data2 = $app->Secure->GetGET("more_data2");
                if ($more_data2 == 1) {
                } else {
                    $where .= " AND (SELECT id FROM payment_transaction pt WHERE pt.doc_typ = belege.doc_typ AND pt.doc_id = belege.doc_id) IS NULL";
                }

                $more_data3 = $app->Secure->GetGET("more_data3");
                if ($more_data3 == 1) {
                    $statusfilter[] = 'storniert';
                } else {
                }

                $more_data4 = $app->Secure->GetGET("more_data4");
                if ($more_data4 == 1) {
                    $statusfilter[] = 'abgeschlossen';
                } else {
                }

                if (!empty($statusfilter)) {
                    $where .= " AND belege.status IN ('".implode("','",$statusfilter)."')";
                }

                $this->app->YUI->DatePicker('zahlbarbis');
                $filterzahlbarbis = $this->app->YUI->TableSearchFilter($name, 7,'zahlbarbis');
                if (!empty($filterzahlbarbis)) {
                    $filterzahlbarbis = $this->app->String->Convert($filterzahlbarbis,'%1.%2.%3','%3-%2-%1');
                    $where .= " AND belege.zahlbarbis <= '".$filterzahlbarbis."'";
                }

                $this->app->YUI->DatePicker('skontobis');
                $filterskontobis = $this->app->YUI->TableSearchFilter($name, 8,'skontobis');
                if (!empty($filterskontobis)) {
                    $filterskontobis = $this->app->String->Convert($filterskontobis,'%1.%2.%3','%3-%2-%1');
                    $where .= " AND belege.skontobis <= '".$filterskontobis."'";
                }

                // END Toggle filters
                $count = "SELECT count(DISTINCT id) FROM ".$tables." WHERE $where";

//                echo($sql." WHERE ".$where);

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
                     'z.created_at',
                     'z.id'
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

                $moreinfo = true; // Allow drop down details
//                $moreinfoaction = "lieferschein"; // specify suffix for minidetail-URL to allow different minidetails
                $menucol = 1; // Set id col for moredata/menu

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
                         ".$app->erp->FormatDateTime("z.created_at").",
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
                    $statusfilter[] = 'offen';
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

    function zahlungsverkehr_menu() {
        $offene = $this->app->DB->Select("SELECT COUNT(id) FROM payment_transaction WHERE payment_status = 'offen'");
        $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=ueberweisung", "Offene Belege");
        $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=list", "Transaktionen".($offene?(" (".$offene.")"):""));
        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");
    }

    function zahlungsverkehr_list() {
        // Process multi action
        $auswahl = $this->app->Secure->GetPOST('auswahl');
        $submit = $this->app->Secure->GetPOST('submit');
        switch($submit) {
            case 'ausfuehren':
                if(!empty($auswahl)) {
                    $this->zahlungsverkehr_ausfuehren_und_meldung($auswahl);
                }
            break;
        }

        $this->zahlungsverkehr_menu();
        $this->app->YUI->TableSearch('TAB1', 'zahlungsverkehr_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "zahlungsverkehr_list.tpl");
    }

    function zahlungsverkehr_ueberweisung() {
        // Process multi action
        $auswahl = $this->app->Secure->GetPOST('auswahl');
        $submit = $this->app->Secure->GetPOST('submit');
        switch($submit) {
            case 'ausfuehren':
                $ausfuehren = true;
                // break omitted
            case 'anlegen':
                $selectedIds = [];
                if(!empty($auswahl)) {
                    foreach($auswahl as $selectedId) {
                        if (str_starts_with($selectedId, 'v')) {
                            $doc_typ = 'verbindlichkeit';
                        } else if (str_starts_with($selectedId, 'g')) {
                            $doc_typ = 'gutschrift';
                        }
                        $selectedId = (int) substr($selectedId,1);
                        if($selectedId > 0) {
                            $selectedIds[] = array('doc_typ' => $doc_typ,'doc_id' => $selectedId);
                        }
                    }

                    $result = $this->zahlungsverkehr_anlegen($selectedIds);
                    if (empty($result['errors'])) {
                        $this->app->Tpl->AddMessage('success',$result['success']." Belege zum Zahllauf gegeben.");
                    } else {
                        $ausfuehren = false;
                        $this->app->Tpl->AddMessage('error',"Belege konnten nicht zum Zahllauf gegeben werden: " .implode(', ',array_column($result['errors'],'beleg')));
                        foreach ($result['errors'] as $error) {
                            $this->notification('Beleg konnte nicht zum Zahllauf gegeben werden', $error['beleg'].": ".$error['msg']);
                        }
                    }
                }

                if ($ausfuehren) {
                    $this->zahlungsverkehr_ausfuehren_und_meldung($result['payment_transaction_ids']);
                }

            break;
        }

        $this->zahlungsverkehr_menu();
        $this->app->YUI->TableSearch('TAB1', 'zahlungsverkehr_ueberweisung', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "zahlungsverkehr_ueberweisung.tpl");
    }

    function zahlungsverkehr_anlegen(array $items) {

        $result = array();

        foreach ($items as $item) {
            $doc_typ= $item['doc_typ'];
            $id = $item['doc_id'];
            $belegrow = $this->app->DB->SelectRow("SELECT * FROM ".self::UNIFIED_SQL_TABLES." WHERE doc_typ = '".$doc_typ."' AND doc_id = ".$id);
            $doc_name = ucfirst($doc_typ)." ".$belegrow['belegnr'];

            $sql = "SELECT id FROM payment_transaction WHERE doc_typ = '".$doc_typ."' AND doc_id = ".$id." LIMIT 1";
            $pm = $this->app->DB->Select($sql);
            if ($pm) {
                $result['errors'][] = array('beleg' => $doc_name, 'doc_typ' => $doc_typ, 'doc_id' => $id, 'msg' => 'Bereits im Zahllauf');
                continue;
            }

            if ($belegrow['status'] <> 'freigegeben') {
                $result['errors'][] = array('beleg' => $doc_name, 'doc_typ' => $doc_typ, 'doc_id' => $id, 'msg' => 'Falscher Status');
                continue;
            }

            if ($belegrow['bezahlt']) {
                $result['errors'][] = array('beleg' => $doc_name, 'doc_typ' => $doc_typ, 'doc_id' => $id, 'msg' => 'Bereits bezahlt');
                continue;
            }

            $paymentMethodService = $this->app->Container->get('PaymentMethodService');
            try {
                $zahlungsweiseData = $paymentMethodService->getFromShortname($belegrow['zahlungsweise']);
                if (empty($zahlungsweiseData)) {
                    $result['errors'][] = array('beleg' => $doc_name, 'doc_typ' => $doc_typ, 'doc_id' => $id, 'msg' => 'Kein Zahlungsweisemodul');
                    continue;
                }
            } catch (Exception $e) {
                $result['errors'][] = array('beleg' => $doc_name, 'doc_typ' => $doc_typ, 'doc_id' => $id, 'msg' => 'Kein Zahlungsweisemodul');
                continue;
            }

            // Skonto
            $skontobis = date_create_from_format('!Y-m-d+', $belegrow['skontobis']);
            $heute = new DateTime('midnight');
            $abstand = $skontobis->diff($heute)->format("%r%a"); // What a load of bullshit, WTF php...

            if ($abstand <= 0) {
                $betrag = round($belegrow['betrag']*(100-($belegrow['skonto']/100)),2);
                $duedate = $belegrow['skontobis'];
            } else {
                $betrag = $belegrow['betrag'];
                $duedate = $belegrow['zahlbarbis'];
            }

            if ($duedate == '0000-00-00') {
                $result['errors'][] = array('beleg' => $doc_name, 'doc_typ' => $doc_typ, 'doc_id' => $id, 'msg' => 'Ung&uuml;ltiges Zahlungsziel');
                continue;
            }

            if (empty($zahlungsweiseData['einstellungen']['konto'])) {
                $result['errors'][] = array('beleg' => $doc_name, 'doc_typ' => $doc_typ, 'doc_id' => $id, 'msg' => 'Keine Kontodaten');
                continue;
            }

            // Save to DB
            $db_data = array(
                 'payment_account_id' => $zahlungsweiseData['id'],
                 'doc_typ' => $doc_typ,
                 'doc_id' => $id,
                 'address_id' => $belegrow['adresse'],
                 'payment_status' => 'offen',
                 'amount' => $betrag,
                 'currency' => $belegrow['waehrung'],
                 'duedate' => $duedate,
                 'payment_reason' => $doc_name,
                 'payment_info' => $belegrow['rechnung']
            );

            $columns = "id, ";
            $values = "NULL, ";
            $fix = "";

            foreach ($db_data as $key => $value) {
                $columns = $columns.$fix.$key;
                $values = $values.$fix."'".$value."'";
                $fix = ", ";
            }
            $sql = "INSERT INTO payment_transaction (".$columns.") VALUES (".$values.")";
            $this->app->DB->Insert($sql);
            $newid = $this->app->DB->GetInsertID();
            $result['payment_transaction_ids'][] = $newid;
        }

        return($result);

    } // zahlungsverkehr_anlegen

    // Execute all pending transactions
    function zahlungsverkehr_ausfuehren(array $payment_transaction_ids) {

        $result = array();
        $prepared_transaction_blocks = array();
        $prepared_headers = array();
        $successcount = 0;

        $payment_transactions = $this->app->DB->SelectArr("SELECT * FROM payment_transaction WHERE payment_status = 'offen' AND id IN (".implode(',',$payment_transaction_ids).")");

        foreach ($payment_transactions as $item) {
            // Generate Dataset for payment service
            $doc_typ= $item['doc_typ'];
            $id = $item['doc_id'];
            $belegrow = $this->app->DB->SelectRow("SELECT * FROM ".self::UNIFIED_SQL_TABLES." WHERE doc_typ = '".$doc_typ."' AND doc_id = ".$id);
            $doc_name = ucfirst($doc_typ)." ".$belegrow['belegnr'];

            $paymentMethodService = $this->app->Container->get('PaymentMethodService');
            try {
                $zahlungsweiseData = $paymentMethodService->getFromShortname($belegrow['zahlungsweise']);
                if (empty($zahlungsweiseData)) {
                    $result['errors'][] = array('beleg' => $doc_name, 'doc_typ' => $doc_typ, 'doc_id' => $id, 'msg' => 'Kein Zahlungsweisemodul');
                    continue;
                }
            } catch (Exception $e) {
                $result['errors'][] = array('beleg' => $doc_name, 'doc_typ' => $doc_typ, 'doc_id' => $id, 'msg' => 'Kein Zahlungsweisemodul');
                continue;
            }

            $kontodaten = $this->app->DB->SelectRow("SELECT * FROM konten WHERE id = ".$zahlungsweiseData['einstellungen']['konto']." LIMIT 1");
            $adressdaten = $this->app->DB->SelectRow("SELECT * FROM adresse WHERE id = ".$belegrow['adresse']);

            $dataset = array(
                'id' => $item['id'],
                'beleg_typ' => $doc_typ,
                'beleg_id' => $id,
                'betrag' => $belegrow['betrag'],
                'waehrung' => $belegrow['waehrung'],
                'datum_faellig' => $duedate,
                'verwendungszweck' => $item['payment_info'],
                'adresse' => $adressdaten,
                'belegdaten' => $belegrow
            );
            $prepared_transaction_blocks[$zahlungsweiseData['id']]['transactions'][] = $dataset;
            $prepared_transaction_blocks[$zahlungsweiseData['id']]['accountdata'] = $kontodaten;
            $prepared_transaction_blocks[$zahlungsweiseData['id']]['paymenttype']['name'] = $zahlungsweiseData['bezeichnung'];
            $prepared_transaction_blocks[$zahlungsweiseData['id']]['paymenttype']['type'] = $zahlungsweiseData['type'];
            $prepared_transaction_blocks[$zahlungsweiseData['id']]['paymenttype']['module'] = $zahlungsweiseData['modul'];
        }

        if (!empty($result['errors'])) {
            return($result);
        }

        $result['success'] = true;
        $result['successcount'] = 0;

        // ----------------------------------------------
        // Call PaymentMethodService to process
        // ----------------------------------------------
        foreach ($prepared_transaction_blocks as $blockkey => $prepared_transaction_block) {
            $module = $this->app->erp->LoadZahlungsweiseModul($prepared_transaction_block['paymenttype']['module'], $blockkey);
            if ($module) {
                $payment_result = $module->ProcessPayment($prepared_transaction_block);
            } else {
                $result['results'][$blockkey]['errors'] = 'Zahlungsweisemodul \''.$prepared_transaction_block['paymenttype']['module'].'\' nicht gefunden';
                $result['success'] = false;
                continue;
            }

            if ($payment_result['success']) {
                foreach ($payment_result['payment_objects'] as $payment_object_key => $payment_object) {
                    foreach ($payment_object['attachments'] as $attachment_key => $attachment) {
                        $fileid = $this->app->erp->CreateDatei(
                            name: $attachment['filename'],
                            titel: $attachment['filename'],
                            beschreibung: $attachment['description'],
                            nummer: "",
                            datei: $attachment['contents'],
                            ersteller: $this->app->User->GetName(),
                            geschuetzt: true
                        );

                        foreach ($payment_object['payment_transaction_ids'] as $transaction) {
                            $this->app->erp->AddDateiStichwort($fileid, "anhang", "payment_transaction", $transaction);
                        }

                        $payment_result['payment_objects'][$payment_object_key]['attachments'][$attachment_key]['file'] = $fileid;
                    }
                }
                $result['successcount'] += count($payment_result['successful_transactions']);
                $this->app->DB->Update("UPDATE payment_transaction SET payment_status = 'ausgefuehrt' WHERE id IN (".implode(', ',$payment_result['successful_transactions']).")");
            } else {
                $result['success'] = false;
            }

            $result['results'][$blockkey] = $payment_result;
        }

        return($result);
    }

    function zahlungsverkehr_ausfuehren_und_meldung(array $auswahl) {
        $attachments = array();
        $result = $this->zahlungsverkehr_ausfuehren($auswahl);

        if ($result['success']) {
            $this->app->Tpl->AddMessage('success',$result['successcount']." Transaktionen im Zahllauf ausgef&uuml;hrt.");
        } else {
            $this->app->Tpl->AddMessage('error',"Belege konnten nicht im Zahllauf ausgef&uuml;hrt werden!");

            foreach ($result['results'] as $blockresult) {
                if (!empty($blockresult['errors'])) {
                    $this->notification('Belege konnten nicht im Zahllauf ausgef&uuml;hrt werden!', implode(", ",$blockresult['errors']));
                }
            }
        }

        foreach ($result['results'] as $blockresult) {
            foreach ($blockresult['payment_objects'] as $payment_object) {
                foreach ($payment_object['attachments'] as $attachment) {
                    $attachments[] = $attachment;
                }
            }
        }

        if (!empty($attachments)) {
            $this->notification('Anh&auml;nge zu Zahllauf','Anh&auml;nge k&ouml;nnen heruntergeladen werden.', $attachments);
        }
    }

    public function zahlungsverkehr_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        if ($this->app->DB->Select("SELECT id FROM `payment_transaction` WHERE `id` = '{$id}' AND `payment_status` = 'offen'")) {
            $this->app->DB->Delete("DELETE FROM `payment_transaction` WHERE `id` = '{$id}' AND `payment_status` = 'offen'");
            $this->app->Tpl->addMessage('warning', 'Der Eintrag wurde gel&ouml;scht');
        }
        else {
            $this->app->Tpl->addMessage('error', 'Der Eintrag konnte nicht gel&ouml;scht werden!');
        }

        $this->zahlungsverkehr_list();
    }

    function notification($title, $text, $attachments = array()) {
        // Notification erstellen
        $notification_message = new NotificationMessageData('default', $title);

        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                $linktext .= '<br><a href="'.sprintf('index.php?module=dateien&action=send&id=%d', $attachment['file']).'">'.$attachment['filename'].'</a>';
            }
            $text .= $linktext;
        }

        $notification_message->setMessage($text);
        $notification_message->setPriority(true);

        /** @var NotificationService $notification */
        $notification = $this->app->Container->get('NotificationService');
        $notification->createFromData($this->app->User->GetID(), $notification_message);
    }

    function zahlungsverkehr_minidetail() {
        $id = $this->app->Secure->GetGET('id');

        $file_attachments = $this->app->erp->GetDateiSubjektObjekt('anhang','payment_transaction',$id);

        if (!empty($file_attachments)) {

            foreach ($file_attachments as $file_attachment) {

                echo(
                  "<a href=\"index.php?module=dateien&action=send&id=".$file_attachment.
                  "\">".
                  htmlentities($this->app->erp->GetDateiName($file_attachment)).
                  " (".
                  $this->app->erp->GetDateiDatumZeitFormat($file_attachment).", ".
                  $this->app->erp->GetDateiSize($file_attachment).
                  ")".
                  "</a> ".
                  "<br>");
          }
        }

        $this->app->ExitXentral();
    }

 }
