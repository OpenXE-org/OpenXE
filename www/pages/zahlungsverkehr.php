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
                                v.adresse,
                                v.id doc_id
                            FROM verbindlichkeit v
                            LEFT JOIN adresse a ON v.adresse = a.id
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
                                g.adresse,
                                g.id doc_id
                            FROM gutschrift g
                            LEFT JOIN adresse a ON g.adresse = a.id
                        ) tables";

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
                            doc_typ_name,
                            belegnr,
                            ".$app->erp->FormatDate("datum").",
                            name,
                            nummer,
                            rechnung,
                            ".$app->erp->FormatMenge('betrag',2)." betrag,
                            waehrung,
                            status,
                            ".$app->erp->FormatDate("zahlbarbis").",
                            IF(skonto <> 0,CONCAT(".$app->erp->FormatMenge('skonto',0).",'%'),''),
                            IF(skonto <> 0,".$app->erp->FormatDate("skontobis").",'')
                        ";
                $tables = self::UNIFIED_SQL_TABLES;

                $sql = "SELECT SQL_CALC_FOUND_ROWS ".$columns." FROM ".$tables;
                $where = " bezahlt <> 1";
                $where .= " AND belegnr <> ''";
                $where .= " AND status <> 'angelegt'";
                $where .= " AND (SELECT id FROM payment_transaction pt WHERE pt.doc_typ = tables.doc_typ AND pt.doc_id = tables.doc_id) IS NULL";
                
                $count = "SELECT count(DISTINCT id) FROM ".$tables." WHERE $where";

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
        $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=ueberweisung", "Offene Belege");
        $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=list", "Transaktionen");
        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'zahlungsverkehr_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "zahlungsverkehr_list.tpl");
    }

    function zahlungsverkehr_ueberweisung() {
        $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=ueberweisung", "Offene Belege");
        $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=list", "Transaktionen");
        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");
        $this->app->YUI->TableSearch('TAB1', 'zahlungsverkehr_ueberweisung', "show", "", "", basename(__FILE__), __CLASS__);

        $auswahl = $this->app->Secure->GetPOST('auswahl');

        // Process multi action
        $submit = $this->app->Secure->GetPOST('submit');
        switch($submit) {
            case 'ausfuehren':
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
                                       
                    $result = $this->zahlungsverkehr_ausfuehren($selectedIds);
                    if (empty($result['errors'])) {
                        $this->app->Tpl->AddMessage('success',$result['success']." Belege zum Zahllauf gegeben.");
                    } else {
                        $this->app->Tpl->AddMessage('error',"Belege konnten nicht zum Zahllauf gegeben werden: " .implode(', ',array_column($result['errors'],'beleg')));
                        foreach ($result['errors'] as $error) {
                            $this->notification('Beleg konnte nicht zum Zahllauf gegeben werden', $error['beleg'].": ".$error['msg']);
                        }
                    }
                }
            break;
        }

        $this->app->Tpl->Parse('PAGE', "zahlungsverkehr_ueberweisung.tpl");
    }

    function zahlungsverkehr_ausfuehren(array $items) {

        $result = array();
        $successcount = 0;

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
                if ($zahlungsweiseData['modul'] != 'ueberweisung') {
                    $result['errors'][] = array('beleg' => $doc_name, 'doc_typ' => $doc_typ, 'doc_id' => $id, 'msg' => $doc_name.'Falsche Zahlungsweise');
                    continue;
                }
                if (empty($zahlungsweiseData)) {
                    $result['errors'][] = array('beleg' => $doc_name, 'doc_typ' => $doc_typ, 'doc_id' => $id, 'msg' => $doc_name.'Kein Zahlungsweisemodul');
                    continue;
                }
            } catch (Exception $e) {
                $result['errors'][] = array('beleg' => $doc_name, 'doc_typ' => $doc_typ, 'doc_id' => $id, 'msg' => 'Kein Zahlungsweisemodul');
                continue;
            }

            $kontodaten = $this->app->DB->SelectRow("SELECT * FROM konten WHERE id = ".$zahlungsweiseData['einstellungen']['konto']." LIMIT 1");
            $adressdaten = $this->app->DB->SelectRow("SELECT * FROM adresse WHERE id = ".$belegrow['adresse']);

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

            // Generate Dataset
            $payment_details = array(
                'sender' => $kontodaten['inhaber'],
                'sender_iban' => $kontodaten['iban'],
                'sender_bic' => $kontodaten['swift'],
                'empfaenger' => $adressdaten['inhaber'],
                'iban' => $adressdaten['iban'],
                'bic' => $adressdaten['swift'],
                'betrag' => $betrag,
                'waehrung' => $belegrow['waehrung'],
                'vz1' => $belegrow['rechnung'],
                'datumueberweisung' => ''
            );

            // Save to DB
            $input = array(
                'payment_account_id' => $zahlungsweiseData['id'],
                'doc_typ' => $doc_typ,
                'doc_id' => $id,
                'address_id' => $adressdaten['id'],
                'payment_status' => 'angelegt',
                'amount' => $betrag,
                'currency' => $belegrow['waehrung'],
                'duedate' => $duedate,
                'payment_reason' => $doc_name.' '.$belegrow['belegnr'],
                'payment_info' => $belegrow['rechnung'],
                'payment_json ' => json_encode($payment_details)
            );

            $columns = "id, ";
            $values = "NULL, ";
            $update = "";

            $fix = "";

            foreach ($input as $key => $value) {
                $columns = $columns.$fix.$key;
                $values = $values.$fix."'".$value."'";
                $update = $update.$fix.$key." = '$value'";
                $fix = ", ";
            }
            $sql = "INSERT INTO payment_transaction (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;
                       
            $this->app->DB->Update($sql);
            $this->app->erp->BelegProtokoll($doc_typ,$id,$doc_name." zum Zahllauf gegeben.");
            $successcount++;
        }

        $result['success'] = $successcount;

        return($result);
    }

    public function zahlungsverkehr_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        $this->app->DB->Delete("DELETE FROM `payment_transaction` WHERE `id` = '{$id}' AND `payment_status` = 'angelegt'");
        $this->app->Tpl->addMessage('error', 'Der Eintrag wurde gel&ouml;scht');
        $this->zahlungsverkehr_list();
    }

    function notification($title, $text) {
        // Notification erstellen
        $notification_message = new NotificationMessageData('default', $title);
        $notification_message->setMessage($text);
        $notification_message->setPriority(true);
        /** @var NotificationService $notification */
        $notification = $this->app->Container->get('NotificationService');
        $notification->createFromData($this->app->User->GetID(), $notification_message);
    }

 }
