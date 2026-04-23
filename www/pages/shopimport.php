<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 * SPDX-FileCopyrightText: 2019 Xentral (c) Xentral ERP Software GmbH, Fuggerstrasse 11, D-86150 Augsburg, Germany
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

/*
 * *** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
 *
 * Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
 *
 * This file is licensed under the Embedded Projects General Public License *Version 3.1.
 *
 * You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis
 * to obtain the text of the corresponding license version.
 *
 * *** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
 */
?>
<?php

class Shopimport {

    public $error;

    /** @var Application $app */
    public $app;

    const MODULE_NAME = 'Shopimport';

    const CUSTOMER_NO_MATCH = 0;
    const CUSTOMER_FULL_MATCH = 1;
    const CUSTOMER_DATA_MISMATCH = 2;
    const CUSTOMER_NUMBER_MISMATCH = 3;
    const CUSTOMER_ONLY_NAME_MATCH = 4;

    public $javascript = [
        './classes/Modules/Shopimport/www/js/shopimport.js'
    ];

    /**
     * Shopimport constructor.
     *
     * @param Application $app
     * @param bool        $intern
     */
    public function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern) {
            return;
        }
        $this->app->ActionHandlerInit($this);

        $this->app->ActionHandler("login", "ShopimportLogin");
        $this->app->ActionHandler("main", "ShopimportMain");
        $this->app->ActionHandler("list", "ShopimportList");
        $this->app->ActionHandler("alle", "ShopimportAlle");
        $this->app->ActionHandler("import", "ShopimportImport");
        $this->app->ActionHandler("fileupload", "ShopimportFileUpload");
        $this->app->ActionHandler("view", "ShopimportView");
        $this->app->ActionHandler("einzelimport", "ShopimportEinzelimport");
        $this->app->ActionHandler("navigation", "ShopimportNavigation");
        $this->app->ActionHandler("logout", "ShopimportLogout");
        $this->app->ActionHandler("archiv", "ShopimportArchiv");

        $this->app->DefaultActionHandler("list");

        $this->app->Tpl->Set('UEBERSCHRIFT', 'Shop Import');
        $this->app->ActionHandlerListen($app);
    }

    /**
     * @param int   $shopId
     * @param array $data
     *
     * @return int
     *
     * @throws Exception
     */
    public function Refund($shopId, $data) {
        if (empty($shopId)) {
            throw new Exception('no Shop given');
        }
        if (empty($data)) {
            throw new Exception('no Data given');
        }

        $shop = $this->app->DB->SelectRow(
                sprintf(
                        'SELECT `aktiv`, `id`, `artikelrabatt` FROM `shopexport` WHERE `id` = %d',
                        $shopId
                )
        );
        if (empty($shop)) {
            throw new Exception('Shop not found');
        }
        if (empty($shop['aktiv'])) {
            throw new Exception('Shop is not active');
        }

        if (!empty($data->extid)) {
            $order = $this->app->DB->SelectRow(
                    sprintf(
                            "SELECT *
          FROM `auftrag`
          WHERE `shop` = %d AND `shopextid` = '%s' AND `shopextid` <> ''
          AND `status` <> 'storniert'
          LIMIT 1",
                            $shopId, $this->app->DB->real_escape_string((string) $data->extid)
                    )
            );
        } elseif (!empty($data->internet)) {
            $order = $this->app->DB->SelectRow(
                    sprintf(
                            "SELECT *
          FROM `auftrag`
          WHERE `shop` = %d AND `internet` = '%s' AND `internet` <> ''
          AND `status` <> 'storniert'
          LIMIT 1",
                            $shopId, $this->app->DB->real_escape_string((string) $data->extid)
                    )
            );
        } else {
            throw new Exception('No order given');
        }

        if (empty($order)) {
            throw new Exception('order not found');
        }

        $invoice = $this->app->DB->SelectRow(
                sprintf(
                        "SELECT * FROM `rechnung` WHERE `auftragid` = %d ORDER BY `status` = 'storniert' LIMIT 1",
                        $order['id']
                )
        );

        if (empty($invoice)) {
            if (empty($data->createinvoice)) {
                throw new Exception('order has no invoice');
            }
            $invoiceId = $this->app->erp->WeiterfuehrenAuftragZuRechnung($order['id']);
            $this->app->erp->AuftragProtokoll($order['id'], 'Rechnung erstellt durch Shop-Trigger');
            $this->app->erp->RechnungProtokoll($invoiceId, 'Rechnung erstellt durch Shop-Trigger');
            $this->app->erp->BelegFreigabe('rechnung', $invoiceId);
            $this->app->erp->RechnungNeuberechnen($invoiceId);
            $invoice = $this->app->DB->SelectRow(
                    sprintf(
                            "SELECT * FROM `rechnung` WHERE `auftragid` = %d ORDER BY `status` = 'storniert' LIMIT 1",
                            $order['id']
                    )
            );
        }

        if ($invoice['status'] === 'storniert') {
            throw new Exception('order invoice is already cancelled');
        }
        if (!empty($data->positions)) {
            $positions = $this->app->DB->SelectArr(
                    sprintf(
                            "SELECT `op`.webid, ip.artikel, ip.menge,ip.preis, ip.rabatt, ip.auftrag_position_id
          FROM `rechnung_position` AS `ip`
          INNER JOIN `auftrag_position` AS `op` ON ip.auftrag_position_id = op.id
          WHERE `ip`.rechnung = %d",
                            $invoice['id']
                    )
            );
            if (empty($positions)) {
                throw new Exception('invoice has no positions');
            }
            $webIds = [];
            $foundWebIds = [];
            foreach ($positions as $position) {
                if (!empty($position['webid'])) {
                    $webIds[] = $position['webid'];
                }
            }

            $webIdToPosition = [];

            foreach ($data->positions as $position) {
                if (!empty($position->webid)) {
                    if (!in_array((string) $position->webid, $webIds)) {
                        throw new Exception(sprintf('position %s not found', (string) $position->webid));
                    }
                    $foundWebIds[] = (string) $position->webid;
                    $webIdToPosition[$position->webid] = $position;
                } else {
                    throw new Exception('no webId given');
                }
            }

            if (empty($foundWebIds)) {
                throw new Exception('positions empty');
            }

            $toDeleteInvoicePositionIds = [0];
            foreach ($positions as $position) {
                if (empty($position['webid']) || !in_array($position['webid'], $foundWebIds)) {
                    $toDeleteInvoicePositionIds[] = (int) $position['auftrag_position_id'];
                }
            }

            $creditNoteId = $this->app->erp->WeiterfuehrenRechnungZuGutschrift($invoice['id']);
            $this->app->erp->RechnungProtokoll($invoice['id'], 'Rechnung durch Shop-Trigger storniert');
            $this->app->erp->GutschriftProtokoll($creditNoteId, 'Gutschrift durch Shop-Trigger angelegt');
            $this->app->erp->BelegFreigabe('gutschrift', $creditNoteId);
            $this->app->DB->Delete(
                    sprintf(
                            "DELETE FROM `gutschrift_position` WHERE `gutschrift` = %d AND `auftrag_position_id` IN (%s)",
                            $creditNoteId, implode(',', $toDeleteInvoicePositionIds)
                    )
            );
            $hasCreditNoteTax = !empty($this->app->erp->GutschriftMitUmsatzeuer($creditNoteId));
            $this->app->erp->GutschriftNeuberechnen($creditNoteId);
            $creditNote = $this->app->DB->SelectRow(
                    sprintf(
                            'SELECT * FROM `gutschrift` WHERE `id` = %d',
                            $creditNoteId
                    )
            );
            $creditNotePositions = $this->app->DB->SelectArr(
                    sprintf(
                            "SELECT op.webid, cnp.id, cnp.menge, cnp.preis, cnp.rabatt, cnp.umsatzsteuer,
            cnp.steuersatz
          FROM `gutschrift_position` AS `cnp`
          INNER JOIN `auftrag_position` AS `op` ON cnp.auftrag_position_id = op.id
          WHERE cnp.gutschrift = %d",
                            $creditNoteId
                    )
            );
            foreach ($creditNotePositions as $creditNotePosition) {
                if (isset($webIdToPosition[$creditNotePosition['webid']])) {
                    $position = $webIdToPosition[$creditNotePosition['webid']];
                    if (!empty($position->quantity)) {
                        $this->app->DB->Update(
                                sprintf(
                                        "UPDATE `gutschrift_position` SET `menge` = %f WHERE `id` = %d",
                                        (float) $position->quantity, $creditNotePosition['id']
                                )
                        );
                    } elseif (!empty($position->menge)) {
                        $this->app->DB->Update(
                                sprintf(
                                        "UPDATE `gutschrift_position` SET `menge` = %f WHERE `id` = %d",
                                        (float) $position->menge, $creditNotePosition['id']
                                )
                        );
                    }
                    $preis = null;
                    if (isset($position->price)) {
                        $preis = (float) $position->price;
                    } elseif (isset($position->preis)) {
                        $preis = (float) $position->preis;
                    } elseif (isset($position->amount)) {
                        $preis = (float) $position->amount;
                    }
                    if ($preis !== null) {
                        if ($hasCreditNoteTax) {
                            if ($creditNotePosition['steuersatz'] !== null && $creditNotePosition['steuersatz'] >= 0) {
                                $preis /= 1 + $creditNotePosition['steuersatz'] / 100;
                            } elseif ($creditNotePosition['umsatzsteuer'] === 'ermaessigt') {
                                $preis /= 1 + $creditNote['steuersatz_ermaessigt'] / 100;
                            } elseif ($creditNotePosition['umsatzsteuer'] !== 'befreit') {
                                $preis /= 1 + $creditNote['steuersatz_normal'] / 100;
                            }
                        }
                        $this->app->DB->Update(
                                sprintf(
                                        'UPDATE `gutschrift_position` SET `preis` = %f, `rabatt` = 0 WHERE `id` = %d',
                                        $preis, $creditNotePosition['id']
                                )
                        );
                    }
                }
            }

            $this->app->erp->GutschriftNeuberechnen($creditNoteId);

            if (!empty($data->amount)) {
                $soll = $this->app->DB->Select(sprintf('SELECT `soll` FROM `gutschrift` WHERE `id` = %d', $creditNoteId));
                $diff = round($soll, 2) - round((float) $data->amount, 2);
                if ($diff == 0) {
                    return $creditNoteId;
                }
                $this->app->erp->AddPositionManuellPreis(
                        'gutschrift', $creditNoteId, $shop['artikelrabatt'], 1, 'Differenz', -$diff, 'befreit', $invoice['waehrung']
                );
            }

            return (int) $creditNoteId;
        }
        $creditNoteId = $this->app->erp->WeiterfuehrenRechnungZuGutschrift($invoice['id']);
        $this->app->erp->RechnungProtokoll($invoice['id'], 'Rechnung durch Shop-Trigger storniert');
        $this->app->erp->GutschriftProtokoll($creditNoteId, 'Gutschrift durch Shop-Trigger angelegt');
        $this->app->erp->BelegFreigabe('gutschrift', $creditNoteId);
        $this->app->erp->GutschriftNeuberechnen($creditNoteId);
        if (!empty($data->amount)) {
            $soll = $this->app->DB->Select(sprintf('SELECT `soll` FROM `gutschrift` WHERE `id` = %d', $creditNoteId));
            $diff = round($soll, 2) - round((float) $data->amount, 2);
            if ($diff == 0) {
                return $creditNoteId;
            }
            $this->app->erp->AddPositionManuellPreis(
                    'gutschrift', $creditNoteId, $shop['artikelrabatt'], 1, 'Differenz', -$diff, 'befreit', $invoice['waehrung']
            );
        }

        return (int) $creditNoteId;
    }

    public function ShopimportList() {
        $msg = $this->app->Secure->GetGET('msg');
        if (!empty($msg)) {
            $msg = $this->app->erp->base64_url_decode($msg);
            $this->app->Tpl->Set('MESSAGE', $msg);
        }
        $this->app->erp->MenuEintrag('index.php?module=importvorlage&action=uebersicht', 'Zur&uuml;ck zur &Uuml;bersicht');

        //$this->app->Tpl->Add(TABS,"<li><h2 style=\"background-color: [FARBE5];\">Shopimport</h2></li>");
        $this->app->erp->Headlines('Shopimport');
        //$this->app->erp->MenuEintrag("index.php?module=shopimport&action=alle","Alle importieren");
        $this->app->erp->MenuEintrag('index.php?module=shopimport&action=list', '&Uuml;bersicht');

        if ($this->app->erp->RechteVorhanden('shopimport', 'alle')) {
            $this->app->Tpl->Add('INHALT', "<center><input type=\"button\" onclick=\"window.location.href='index.php?module=shopimport&action=alle';\" value=\"Alle importieren\" class=\"buttonsave\"></center><br><br>");
        }
        //$this->app->Tpl->Set('SUBHEADING',"Imports");
        //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");
        $runningcronjob = $this->app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'shopimport' AND aktiv=1 AND mutex = 1 AND UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP(letzteausfuerhung) < 300 LIMIT 1");
        if ($runningcronjob) {
            $this->app->Tpl->Set('TAB1', '<div class="error">Es l&auml;uft gerade ein Cronjob der Auftr&auml;ge abholt. Die manuelle Auftragsabholung ist in dieser Zeit gesperrt. Bitte warten Sie ein paar Minuten und versuchen Sie es erneut.</div>');
        } else {
            $table = new EasyTable($this->app);
            $table->Query("SELECT ae.bezeichnung,p.abkuerzung as Projekt,
          ae.id FROM shopexport ae LEFT JOIN projekt p ON p.id=ae.projekt WHERE ae.aktiv='1'");
            $table->DisplayNew('INHALT', "<a href=\"index.php?module=shopimport&action=fileupload&id=%value%\"><img src=\"./themes/new/images/upload.svg\" title=\"Datei hochladen\"></a>&nbsp;<a href=\"index.php?module=shopimport&action=import&id=%value%\"><img src=\"./themes/new/images/download.svg\" title=\"Importieren\"></a>&nbsp;<a href=\"index.php?module=shopimport&action=view&id=%value%\"><img src=\"./themes/new/images/lupe.svg\" title=\"Ansicht\"></a>&nbsp;<a href=\"index.php?module=onlineshops&action=edit&id=%value%\"><img src=\"./themes/new/images/edit.svg\" title=\"Bearbeiten\"></a>&nbsp;<!--<a href=\"index.php?module=shopimport&action=archiv&id=%value%\">Archiv</a>-->");
            $this->app->Tpl->Parse('TAB1', 'rahmen.tpl');
        }
        $this->app->Tpl->Set('INHALT', '');

        // Archiv GESTERN
        $table = new EasyTable($this->app);
        $table->Query("SELECT a.datum, a.internet, a.transaktionsnummer,a.name,  a.land, a.gesamtsumme as betrag, (SELECT SUM(r.soll) FROM rechnung r WHERE r.adresse=a.adresse AND r.status='offen') as mahnwesen, a.zahlungsweise, a.partnerid as Partner, a.id FROM auftrag a WHERE
        (datum=DATE_FORMAT( DATE_SUB( NOW() , INTERVAL 1 DAY ) , '%Y-%m-%d' ) OR (datum=DATE_FORMAT( NOW(), '%Y-%m-%d' ))) AND a.internet>0
        ORDER by a.id DESC");
        $table->DisplayNew('INHALT', "<a href=\"index.php?module=auftrag&action=edit&id=%value%\" target=\"_blank\"><img src=\"./themes/" . $this->app->Conf->WFconf['defaulttheme'] . "/images/edit.svg\"></a>
        <a href=\"index.php?module=auftrag&action=pdf&id=%value%\"><img src=\"./themes/" . $this->app->Conf->WFconf['defaulttheme'] . "/images/pdf.svg\"></a>");

        $this->app->Tpl->Parse('TAB2', 'rahmen.tpl');
        $this->app->Tpl->Set('INHALT', '');

        $summe_heute = $this->app->DB->Select("SELECT SUM(a.gesamtsumme) FROM auftrag a WHERE
        a.datum=DATE_FORMAT( NOW(), '%Y-%m-%d' ) AND a.internet>0
        ");

        $summe_gestern = $this->app->DB->Select("SELECT SUM(a.gesamtsumme) FROM auftrag a WHERE
        a.datum=DATE_FORMAT( DATE_SUB( NOW() , INTERVAL 1 DAY ) , '%Y-%m-%d' ) AND a.internet>0
        ");

        $this->app->Tpl->Add('TAB2', "<div class=\"warning\">Heute: $summe_heute EUR (inkl. Steuer und Versand) <i>Umsatz aus den Online-Shop</i></div>");
        $this->app->Tpl->Add('TAB2', "<div class=\"warning\">Gestern: $summe_gestern EUR (inkl. Steuer und Versand) <i>Umsatz aus den Online-Shop</i></div>");

        $this->app->Tpl->Set('SUBHEADING', '');
        $this->app->Tpl->Parse('PAGE', 'shopimport_list.tpl');
    }

    public function ShopimportArchiv() {
        $id = $this->app->Secure->GetGET('id');
        $more = $this->app->Secure->GetGET('more');
        $datum = $this->app->Secure->GetGET('datum');

        $this->app->Tpl->Set('TABTEXT', 'Shopimport - Archiv');

        //$this->app->YUI->TableSearch('TAB1',"shopimportarchiv");
        //$this->app->Tpl->Set('TAB1',"Shopimport - Archiv");

        if ($datum == '') {
            $datum = date('Y-m-d');
        }

        $result = $this->app->DB->SelectArr("SELECT * FROM shopimport_auftraege WHERE DATE_FORMAT(logdatei,'%Y-%m-%d') = '$datum'");

        $table = '<table border="1" width="100%">';

        if (is_array($result)) {
            foreach ($result as $key => $row) {
                //$table = $row['imported'];
                if (isset($row['jsonencoded']) && $row['jsonencoded']) {
                    $warenkorb = json_decode(base64_decode($row['warenkorb']), true);
                } else {
                    $warenkorb = unserialize(base64_decode($row['warenkorb']));
                }
                $this->app->stringcleaner->XMLArray_clean($warenkorb);

                $table .= "<tr><td>" . $warenkorb["onlinebestellnummer"] . "/" . $warenkorb["transaktionsnummer"] . "</td><td>" . $warenkorb["name"] . "</td><td>" . $warenkorb["email"] . "</td><td>" . $warenkorb["gesamtsumme"] . "</td><td>
          <a href=\"index.php?module=shopimport&action=archiv&id=$id&more=" . $row['id'] . "&datum=$datum\">mehr Informationen</a></td></tr>";
            }
        }

        $table .= '</table>';

        if ($more > 0) {
            $result = $this->app->DB->Select("SELECT warenkorb FROM shopimport_auftraege WHERE id='$more' LIMIT 1");
            if ($this->app->DB->Select("SELECT id FROM shopimport_auftraege WHERE id='$more' AND jsonencoded = 1 LIMIT 1")) {
                $warenkorb = json_decode(base64_decode($result), true);
            } else {
                $warenkorb = unserialize(base64_decode($result));
            }

            ob_start();
            var_dump($warenkorb);
            $var_dump_result = ob_get_clean();

            $table .= '<pre>' . $var_dump_result . '</pre>';
        }

        $this->app->Tpl->Set('TAB1', $table);
        $this->app->Tpl->Parse('PAGE', 'tabview.tpl');
    }

    public function ShopimportAlle() {
        $runningcronjob = $this->app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'shopimport' AND aktiv=1 AND mutex = 1 AND UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP(letzteausfuerhung) < 300 LIMIT 1");
        if (!empty($runningcronjob)) {
            $this->app->Location->execute('index.php?module=shopimport&action=list');
        }
        $lastshop = false;
        $shops = $this->app->DB->SelectArr("SELECT ae.id as id FROM shopexport ae LEFT JOIN projekt p ON p.id=ae.projekt WHERE ae.aktiv='1' and ae.demomodus <> '1'");
        if (!empty($shops)) {
            $anz = 0;
            $fp = $this->app->erp->ProzessLock('shopimport_alle');
            $cshops = (!empty($shops) ? count($shops) : 0);
            for ($i = 0; $i < $cshops - 1; $i++) {
                $anz += $this->ShopimportImport($shops[$i]['id'], $anz, true);
            }
            $lastshop = $shops[(!empty($shops) ? count($shops) : 0) - 1]['id'];
            $this->app->erp->ProzessUnlock($fp);
        }
        if ($lastshop && is_numeric($lastshop)) {
            $this->app->Location->execute('index.php?module=shopimport&action=import&id=' . $lastshop);
        } else {
            $this->app->Location->execute('index.php?module=shopimport&action=list');
        }
    }

    /**
     * @param int    $shopId
     * @param string $shopOrderNumber
     * @param bool   $changeShopOrderStatus
     * @param int    $projectId
     * @param bool   $allStati
     *
     * @return array
     */
    public function importSingleOrder($shopId, $shopOrderNumber, $changeShopOrderStatus, $projectId, $allStati = false) {
        $pageContents = $this->app->remote->RemoteConnection($shopId);
        if ($pageContents === 'success') {
            $orderCount = $this->app->remote->RemoteGetAuftraegeAnzahlNummer($shopId, $shopOrderNumber);

            if ($orderCount > 0) {
                if ($allStati) {
                    $carts = $this->app->remote->RemoteCommand($shopId, 'getauftrag', ['nummer' => $shopOrderNumber]);
                } else {
                    $carts = $this->app->remote->RemoteGetAuftragNummer($shopId, $shopOrderNumber);
                }

                if (is_array($carts)) {
                    $cart = $carts[0];
                    $shopExtId = $cart['id'];

                    $result = $this->create_shopimport_cart_entry($cart, projectId: $projectId, shopId: $shopId);

                    if ($changeShopOrderStatus && (String) $shopExtId !== '') {
                        $this->app->remote->RemoteDeleteAuftrag($shopId, $shopExtId);
                    }
                    return($result);
                }
            }
            return ['status' => 0, 'error' => 'Auftrag wurde nicht gefunden!'];
        }
        return ['status' => 0, 'error' => 'Verbindung fehlgeschlagen!'];
    }

    public function ShopimportEinzelimport() {
        $runningcronjob = $this->app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'shopimport' AND aktiv=1 AND mutex = 1 AND UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP(letzteausfuerhung) < 300 LIMIT 1");
        if (!empty($runningcronjob)) {
            $this->app->Location->execute('index.php?module=shopimport&action=list');
        }
        $id = (int) $this->app->Secure->GetGET('id');
        $nummer = (String) $this->app->Secure->GetGET('nummer');
        if ($nummer === '') {
            $nummer = (String) $this->app->Secure->GetPOST('nummer');
        }
        $deleteauftrag = (int) $this->app->Secure->GetPOST('deleteauftrag');
        if (!empty($id) && !empty($nummer)) {
            $shoparr = $this->app->DB->SelectRow("SELECT projekt,holealle FROM shopexport WHERE id = '$id' LIMIT 1");
            $projekt = $shoparr['projekt']; //$this->app->DB->Select("SELECT projekt FROM shopexport WHERE id = '$id' LIMIT 1");
            $holealle = $shoparr['holealle']; //$this->app->DB->Select("SELECT holealle FROM shopexport WHERE id = '$id' LIMIT 1");
            if ($holealle) {

                $res = $this->importSingleOrder($id, $nummer, $deleteauftrag, $projekt);
                if ($res['status'] == 1) {
                    $this->app->Tpl->Add('MESSAGE', '<div class="info">' . $res['info'] . '</div>');
                } else {
                    $this->app->Tpl->Add('MESSAGE', '<div class="error">' . $res['error'] . '</div>');
                }
            } else {
                $this->app->Tpl->Add('MESSAGE', '<div class="error">Shop nicht auf alle Auftr&auml;ge eingstellt!</div>');
            }
        } elseif ($id <= 0) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Kein Shop gew&auml;hlt!</div>');
            $this->app->Location->execute('index.php?module=shopimport&action=list&msg=' . $msg);
        } else {
            $this->app->Tpl->Add('MESSAGE', '<div class=\"error\">Keine Nummer angegeben!</div>');
        }
        $this->app->erp->MenuEintrag('index.php?module=shopimport&action=einzelimport&id=' . $id, 'Einzelimport');
        $this->app->erp->MenuEintrag('index.php?module=shopimport&action=import', 'Shopauftrag-Liste');
        $this->app->Tpl->Parse('PAGE', 'shopimport_einzelimport.tpl');
    }

    public function KundeAnlegenUpdate($shopimportid, $shopextid, $warenkorb, bool $bestandskunde = false, $import_kundennummer, &$unbekanntezahlungsweisen) {
        if (empty($warenkorb)) {
            return 0;
        }
        if (!empty($this->app->stringcleaner)) {
            $this->app->stringcleaner->XMLArray_clean($warenkorb);
        }
        $i = 0;
        $success_import = 0;
        $shopid = $this->app->DB->Select("SELECT shopid FROM shopimport_auftraege WHERE id='$shopimportid' LIMIT 1");
        $shopexportArr = $this->app->DB->SelectRow(sprintf('SELECT * FROM shopexport WHERE id=%d', $shopid));
        $projekt = $shopexportArr['projekt']; //$this->app->DB->Select("SELECT projekt FROM shopexport WHERE id='$shopid'");
        if (!empty($warenkorb['projekt']) && $this->app->DB->Select("SELECT id FROM projekt WHERE id = '" . (int) $warenkorb['projekt'] . "' LIMIT 1")) {
            $projekt = (int) $warenkorb['projekt'];
        }
        $adresseprojekt = '';
        $kundenurvonprojekt = $shopexportArr['kundenurvonprojekt']; //$this->app->DB->Select("SELECT kundenurvonprojekt FROM shopexport WHERE id = '".$shopid."' LIMIT 1");
        if ($kundenurvonprojekt) {
            $adresseprojekt = $projekt;
        }

        if (isset($warenkorb['subshop']) && $warenkorb['subshop']) {
            $subshopprojekt = $this->app->DB->SelectArr("SELECT * FROM shopexport_subshop WHERE shop = '" . $shopid . "' AND aktiv = 1 AND subshopkennung = '" . $this->app->DB->real_escape_string($warenkorb['subshop']) . "' LIMIT 1");
            if ($subshopprojekt) {
                if ($subshopprojekt[0]['projekt']) {
                    $adresseprojekt = $subshopprojekt[0]['projekt'];
                    $projekt = $subshopprojekt[0]['projekt'];
                    $arr[$i]['abkuerzung'] = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$adresseprojekt' LIMIT 1");
                }
                if ($subshopprojekt[0]['sprache']) {
                    $defaultsprache = $this->app->DB->Select("SELECT sprache FROM shopexport_sprachen WHERE shop = '$shopid' AND (projekt = '$projekt' OR projekt = 0) AND
          land = '' ORDER BY projekt = '$projekt' DESC, land = '" . $warenkorb['land'] . "' DESC LIMIT 1");
                    $checksprache = $this->app->DB->Select("SELECT sprache FROM shopexport_sprachen WHERE shop = '$shopid' AND (projekt = '$projekt' OR projekt = 0) AND
          land = '" . (isset($warenkorb['land']) ? $warenkorb['land'] : $this->app->erp->Firmendaten('land')) . "' ORDER BY projekt = '$projekt' DESC, land = '" . $warenkorb['land'] . "' DESC LIMIT 1");
                    if ($checksprache != '') {
                        if (empty($warenkorb['kunde_sprache'])) {
                            $warenkorb['kunde_sprache'] = $checksprache;
                        }
                    } else {
                        if (empty($warenkorb['kunde_sprache'])) {
                            $warenkorb['kunde_sprache'] = $defaultsprache;
                        }
                    }

                    if (empty($warenkorb['kunde_sprache'])) {
                        $warenkorb['kunde_sprache'] = $subshopprojekt[0]['sprache'];
                    }
                }
            } else {
                if (!$this->app->DB->Select("SELECT id FROM shopexport_subshop WHERE shop = '" . $shopid . "' AND subshopkennung = '" . $this->app->DB->real_escape_string($warenkorb['subshop']) . "' LIMIT 1")) {
                    $this->app->DB->Insert("INSERT INTO shopexport_subshop (shop, subshopkennung, aktiv, projekt) VALUES ('" . $shopid . "','" . $this->app->DB->real_escape_string($warenkorb['subshop']) . "','0','$projekt')");
                }
            }
        }

        $kundenurvonprojekt = $this->app->DB->Select("SELECT kundenurvonprojekt FROM shopexport WHERE id = '$shopid' LIMIT 1");
        if ($kundenurvonprojekt) {
            $adresseprojekt = " AND projekt = '" . $adresseprojekt . "' ";
        } else {
            $adresseprojekt = '';
        }

        if (empty($warenkorb['name'])) {
            $warenkorb['name'] = $warenkorb['ansprechpartner'];
            $warenkorb['ansprechpartner'] = '';
        }
        if (empty($warenkorb['name']) && !empty($warenkorb['lieferadresse_name'])) {
            $warenkorb['name'] = $warenkorb['lieferadresse_name'];
        }
        if (empty($warenkorb['lieferadresse_name'])) {
            $warenkorb['lieferadresse_name'] = $warenkorb['lieferadresse_ansprechpartner'];
            $warenkorb['lieferadresse_ansprechpartner'] = '';
        }
        $warenkorb['email'] = trim($warenkorb['email'], " \t\n\r\0\x0B\xc2\xa0");
        if (empty($warenkorb['name'])) {
            return 0;
        }
        //$projekt = $arr[0][projekt];

        if ($bestandskunde == true) {
            $warenkorb['kundennummer'] = $import_kundennummer;
            if (strlen($warenkorb['kundennummer']) != '') {
                $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='{$warenkorb['kundennummer']}' AND geloescht!=1 $adresseprojekt LIMIT 1");
            }
            if ($adresse <= 0) {
                $adresse = $this->app->erp->KundeAnlegen($warenkorb['anrede'], $warenkorb['name'], $warenkorb['abteilung'],
                        $warenkorb['unterabteilung'], $warenkorb['ansprechpartner'], $warenkorb['adresszusatz'], $warenkorb['strasse'], $warenkorb['land'], $warenkorb['plz'], $warenkorb['ort'], $warenkorb['email'],
                        $warenkorb['telefon'], $warenkorb['telefax'], $warenkorb['ustid'], $warenkorb['affiliate_ref'], $projekt);
                $warenkorb['customer_created'] = true;
                if (isset($warenkorb['kunde_sprache'])) {
                    if ($warenkorb['kunde_sprache'] == 'englisch' || $warenkorb['kunde_sprache'] == 'english') {
                        $this->app->DB->Update("UPDATE adresse SET sprache = 'englisch' WHERE id = '$adresse' LIMIT 1");
                    } elseif ($warenkorb['kunde_sprache'] == 'deutsch' || $warenkorb['kunde_sprache'] == 'german') {
                        $this->app->DB->Update("UPDATE adresse SET sprache = 'deutsch' WHERE id = '$adresse' LIMIT 1");
                    } elseif (method_exists($this->app->erp, 'GetSprachenSelect')) {
                        $sprachen = $this->app->erp->GetSprachenSelect;
                        if (isset($sprachen[strtolower($warenkorb['kunde_sprache'])])) {
                            $this->app->DB->Update("UPDATE adresse SET sprache = '" . strtolower($warenkorb['kunde_sprache']) . "' WHERE id = '$adresse' LIMIT 1");
                        }
                    }
                }
                if ($warenkorb['titel'] != '') {
                    $this->app->DB->Update("UPDATE adresse SET titel = '" . $this->app->DB->real_escape_string($warenkorb['titel']) . "' WHERE id = '$adresse' LIMIT 1");
                }
                if (isset($warenkorb['ust_befreit'])) {
                    $this->app->DB->Update("UPDATE adresse SET ust_befreit = '" . (int) $warenkorb['ust_befreit'] . "' WHERE id = '$adresse' LIMIT 1");
                }

                $kundenGruppen = $this->app->DB->SelectArr("SELECT gruppeid,type FROM shopexport_kundengruppen WHERE shopid=$shopid AND aktiv=1 AND apply_to_new_customers=1 AND type<>'Artikel' AND (projekt=0 OR projekt='$projekt')");
                if (!empty($kundenGruppen)) {
                    foreach ($kundenGruppen as $gruppe) {
                        $this->app->erp->AddRolleZuAdresse($adresse, $gruppe['type'], 'von', 'Gruppe', $gruppe['gruppeid']);
                    }
                }

                if (isset($warenkorb['kundengruppe'])) {
                    $this->shopimportAdresseGruppenMapping($warenkorb['kundengruppe'], $adresse, $shopid, $projekt);
                }
                if ($shopexportArr['vertrieb']) {
                    $this->app->DB->Update(
                            sprintf(
                                    'UPDATE adresse SET vertrieb = %d WHERE id = %d',
                                    $shopexportArr['vertrieb'], $adresse
                            )
                    );
                }
                $this->app->DB->Update(
                        sprintf(
                                'UPDATE adresse SET fromshop = %d WHERE fromshop = 0 AND id = %d',
                                $shopexportArr['id'], $adresse
                        )
                );
            } else {
                if (!empty($warenkorb['anrede'])) {
                    $typ = $warenkorb['anrede'];
                } else {
                    $typ = $this->app->DB->Select("SELECT typ FROM adresse WHERE id = '$adresse' LIMIT 1");
                }
                $name = $warenkorb['name'];

                if (!empty($warenkorb['abteilung'])) {
                    $abteilung = $warenkorb['abteilung'];
                } else {
                    $abteilung = $this->app->DB->Select("SELECT abteilung FROM adresse WHERE id = '$adresse' LIMIT 1");
                }
                if (!empty($warenkorb['unterabteilung'])) {
                    $unterabteilung = $warenkorb['unterabteilung'];
                } else {
                    $unterabteilung = $this->app->DB->Select("SELECT unterabteilung FROM adresse WHERE id = '$adresse' LIMIT 1");
                }

                $ansprechpartner = $warenkorb['ansprechpartner'];
                $adresszusatz = $warenkorb['adresszusatz'];
                $strasse = $warenkorb['strasse'];
                $land = $warenkorb['land'];
                $plz = $warenkorb['plz'];
                $ort = $warenkorb['ort'];
                if (!empty($warenkorb['email'])) {
                    $email = $warenkorb['email'];
                } else {
                    $email = $this->app->DB->Select("SELECT email FROM adresse WHERE id = '$adresse' LIMIT 1");
                }
                if (!empty($warenkorb['telefon'])) {
                    $telefon = $warenkorb['telefon'];
                } else {
                    $telefon = $this->app->DB->Select("SELECT telefon FROM adresse WHERE id = '$adresse' LIMIT 1");
                }
                if (!empty($warenkorb['telefax'])) {
                    $telefax = $warenkorb['telefax'];
                } else {
                    $telefax = $this->app->DB->Select("SELECT telefax FROM adresse WHERE id = '$adresse' LIMIT 1");
                }
                if (!empty($warenkorb['ustid'])) {
                    $ustid = $warenkorb['ustid'];
                } else {
                    $ustid = $this->app->DB->Select("SELECT ustid FROM adresse WHERE id = '$adresse' LIMIT 1");
                }
                if (!empty($warenkorb['affiliate_ref'])) {
                    $partner = $warenkorb['affiliate_ref'];
                } else {
                    $partner = $this->app->DB->Select("SELECT partner FROM adresse WHERE id = '$adresse' LIMIT 1");
                }
                // Update + protokoll
                if (!$this->app->DB->Select("SELECT adressennichtueberschreiben FROM shopexport WHERE id = '$shopid' LIMIT 1")) {
                    if ($warenkorb['mobil'] != '') {
                        $this->app->DB->Update("UPDATE adresse SET mobil = '" . $this->app->DB->real_escape_string($warenkorb['mobil']) . "' WHERE id = '$adresse' LIMIT 1");
                    }
                    if ($warenkorb['titel'] != '') {
                        $this->app->DB->Update("UPDATE adresse SET titel = '" . $this->app->DB->real_escape_string($warenkorb['titel']) . "' WHERE id = '$adresse' LIMIT 1");
                    }
                    if ($warenkorb['geburtstag'] != '') {
                        $this->app->DB->Update("UPDATE adresse SET geburtstag = '" . $this->app->DB->real_escape_string($warenkorb['geburtstag']) . "' WHERE id = '$adresse' AND ISNULL(geburtstag) LIMIT 1");
                    }
                    if (isset($warenkorb['ust_befreit'])) {
                        $query = sprintf('UPDATE `adresse` SET `ust_befreit` = %d WHERE `id` = %d LIMIT 1',
                                $warenkorb['ust_befreit'], $adresse);
                        $this->app->DB->Update($query);
                    }
                    $this->app->erp->KundeUpdate($adresse, $typ, $name, $abteilung,
                            $unterabteilung, $ansprechpartner, $adresszusatz, $strasse, $land, $plz, $ort, $email, $telefon, $telefax, $ustid, $partner, $projekt);

                    if (!empty($warenkorb['bundesland'])) {
                        $this->app->DB->Update("UPDATE adresse SET bundesland = '" . $this->app->DB->real_escape_string($warenkorb['bundesland']) . "' WHERE id = '$adresse' LIMIT 1");
                    }
                }
            }
        } else {
            //echo "import als Neu-Kunde $shopimportid<br>";
            $typ = $warenkorb['anrede'];
            $name = $warenkorb['name'];
            $abteilung = $warenkorb['abteilung'];
            $unterabteilung = $warenkorb['unterabteilung'];
            $ansprechpartner = $warenkorb['ansprechpartner'];
            $adresszusatz = $warenkorb['adresszusatz'];
            $strasse = $warenkorb['strasse'];
            $land = $warenkorb['land'];
            $plz = $warenkorb['plz'];
            $ort = $warenkorb['ort'];
            $email = $warenkorb['email'];
            $telefon = $warenkorb['telefon'];
            $telefax = $warenkorb['telefax'];
            $ustid = $warenkorb['ustid'];
            $partner = $warenkorb['affiliate_ref'];

            // denn fall das es kunde 1:1 schon gibt = alte Kundennummer verwenden kommt vor allem vor, wenn ein Kunde an einem Tag oefters bestellt hat
            //								$adresse = $this->app->erp->KundeAnlegen($typ,$name,$abteilung,
            //								$unterabteilung,$ansprechpartner,$adresszusatz,$strasse,$land,$plz,$ort,$email,$telefon,$telefax,$ustid,$partner,$projekt);
            if (!empty($warenkorb['kundennummer'])) {
                $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='{$warenkorb['kundennummer']}' $adresseprojekt AND geloescht!=1 LIMIT 1");
            }

            if (empty($adresse)) {
                $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE email='{$warenkorb['email']}' and email <> '' $adresseprojekt AND geloescht!=1 LIMIT 1");
            }

            if ($adresse <= 0) {
                $adresse = $this->app->erp->KundeAnlegen($typ, $name, $abteilung,
                        $unterabteilung, $ansprechpartner, $adresszusatz, $strasse, $land, $plz, $ort, $email, $telefon, $telefax, $ustid, $partner, $projekt);
                $warenkorb['customer_created'] = true;
                if (!empty($warenkorb['bundesland'])) {
                    $this->app->DB->Update("UPDATE adresse SET bundesland = '" . $this->app->DB->real_escape_string($warenkorb['bundesland']) . "' WHERE id = '$adresse' LIMIT 1");
                }

                if (isset($warenkorb['kunde_sprache'])) {
                    if ($warenkorb['kunde_sprache'] === 'englisch' || $warenkorb['kunde_sprache'] === 'english') {
                        $this->app->DB->Update("UPDATE adresse SET sprache = 'englisch' WHERE id = '$adresse' LIMIT 1");
                    } elseif ($warenkorb['kunde_sprache'] === 'deutsch' || $warenkorb['kunde_sprache'] === 'german') {
                        $this->app->DB->Update("UPDATE adresse SET sprache = 'deutsch' WHERE id = '$adresse' LIMIT 1");
                    } elseif (method_exists($this->app->erp, 'GetAdressSprachen')) {
                        $sprachen = $this->app->erp->GetAdressSprachen();
                        if (isset($sprachen[strtolower($warenkorb['kunde_sprache'])])) {
                            $this->app->DB->Update("UPDATE adresse SET sprache = '" . strtolower($warenkorb['kunde_sprache']) . "' WHERE id = '$adresse' LIMIT 1");
                        }
                    }
                }
                if ($warenkorb['titel'] != '') {
                    $this->app->DB->Update("UPDATE adresse SET titel = '" . $this->app->DB->real_escape_string($warenkorb['titel']) . "' WHERE id = '$adresse' LIMIT 1");
                }
                if (isset($warenkorb['ust_befreit'])) {
                    $this->app->DB->Update("UPDATE adresse SET ust_befreit = '" . (int) $warenkorb['ust_befreit'] . "' WHERE id = '$adresse' LIMIT 1");
                }
                if ($warenkorb['mobil'] != '') {
                    $this->app->DB->Update("UPDATE adresse SET mobil = '" . $this->app->DB->real_escape_string($warenkorb['mobil']) . "' WHERE id = '$adresse' LIMIT 1");
                }
                if (!empty($warenkorb['geburtstag'])) {
                    $query = sprintf("UPDATE `adresse` SET `geburtstag` = '%s' WHERE `id` = %d",
                            $this->app->DB->real_escape_string($warenkorb['geburtstag']),
                            $adresse);
                    $this->app->DB->Update($query);
                }
                $kundenGruppen = $this->app->DB->SelectArr("SELECT gruppeid,type FROM shopexport_kundengruppen WHERE shopid=$shopid AND aktiv=1 AND apply_to_new_customers=1 AND (projekt=0 OR projekt='$projekt')");
                if (!empty($kundenGruppen)) {
                    foreach ($kundenGruppen as $gruppe) {
                        $this->app->erp->AddRolleZuAdresse($adresse, $gruppe['type'], 'von', 'Gruppe', $gruppe['gruppeid']);
                    }
                }

                if (isset($warenkorb['kundengruppe'])) {
                    $this->shopimportAdresseGruppenMapping($warenkorb['kundengruppe'], $adresse, $shopid, $projekt);
                }
                if ($shopexportArr['vertrieb']) {
                    $this->app->DB->Update(
                            sprintf(
                                    'UPDATE adresse SET vertrieb = %d WHERE id = %d AND geloescht <> 1 AND vertrieb = 0',
                                    $shopexportArr['vertrieb'], $adresse
                            )
                    );
                }
                $this->app->DB->Update(
                        sprintf(
                                'UPDATE adresse SET fromshop = %d WHERE fromshop = 0 AND id = %d',
                                $shopexportArr['id'], $adresse
                        )
                );
            } else {
                // Update + protokoll
                if (!$this->app->DB->Select("SELECT adressennichtueberschreiben FROM shopexport WHERE id = '$shopid' LIMIT 1")) {
                    $this->app->erp->KundeUpdate($adresse, $typ, $name, $abteilung,
                            $unterabteilung, $ansprechpartner, $adresszusatz, $strasse, $land, $plz, $ort, $email, $telefon, $telefax, $ustid, $partner, $projekt);
                    if (!empty($warenkorb['bundesland'])) {
                        $this->app->DB->Update("UPDATE adresse SET bundesland = '" . $this->app->DB->real_escape_string($warenkorb['bundesland']) . "' WHERE id = '$adresse' LIMIT 1");
                    }
                }
            }
            // abweichende lieferadresse gleich angelegen?
            if (strlen($warenkorb['lieferadresse_ansprechpartner']) > 3) {
                $this->app->DB->Insert("INSERT INTO lieferadressen (typ,name,abteilung,unterabteilung,land,strasse,ort,plz,adresszusatz,adresse) VALUES
              ('','{$warenkorb['lieferadresse_ansprechpartner']}',
               '{$warenkorb['lieferadresse_abteilung']}','{$warenkorb['lieferadresse_unterabteilung']}','{$warenkorb['lieferadresse_land']}',
               '{$warenkorb['lieferadresse_strasse']}','{$warenkorb['lieferadresse_ort']}','{$warenkorb['lieferadresse_plz']}','{$warenkorb['lieferadresse_adresszusatz']}','$adresse')");
            }

            if (strlen($warenkorb['lieferadresse_name']) > 3 && $warenkorb['lieferadresse_ansprechpartner'] == '') {
                $this->app->DB->Insert("INSERT INTO lieferadressen (typ,name,abteilung,unterabteilung,land,strasse,ort,plz,adresszusatz,adresse) VALUES
              ('','{$warenkorb['lieferadresse_name']}',
               '{$warenkorb['lieferadresse_abteilung']}','{$warenkorb['lieferadresse_unterabteilung']}','{$warenkorb['lieferadresse_land']}',
               '{$warenkorb['lieferadresse_strasse']}','{$warenkorb['lieferadresse_ort']}','{$warenkorb['lieferadresse_plz']}','{$warenkorb['lieferadresse_adresszusatz']}','$adresse')");
            }
        }

        //print_r($warenkorb);
        //echo "<br><br>Ende";
        //exit;
        //imort auf kunde
        //$bekanntezahlungsweisen = array('rechnung','vorkasse','nachnahme','kreditkarte','einzugsermaechtigung','bar','paypal','amazon','amazon_bestellung','sofortueberweisung','amazoncba','secupay','lastschrift');

        $bekanntezahlungsweisen = $this->app->erp->GetZahlungsweise();

        $import_result = $this->app->erp->ImportAuftrag($adresse, $warenkorb, $projekt, $shopid);

        if ($import_result['status']) {
            $tmpauftragid = $import_result['auftragid'];
            $success_import = true;

            $doctype = 'auftrag';
            if ($this->app->DB->Select("SELECT angeboteanlegen FROM shopexport WHERE id = '$shopid' LIMIT 1")) {
                $doctype = 'angebot';
            }

            if (isset($warenkorb['doctype']) && $warenkorb['doctype'] === 'angebot') {
                $doctype = 'angebot';
            }

            $warenkorb['zahlungsweise'] = $this->app->DB->Select("SELECT zahlungsweise FROM $doctype WHERE id = '$tmpauftragid' LIMIT 1");
            if ($warenkorb['zahlungsweise'] != '') {
                if (!isset($bekanntezahlungsweisen[$warenkorb['zahlungsweise']])) {
                    if (!$unbekanntezahlungsweisen || !isset($unbekanntezahlungsweisen[strtolower($warenkorb['zahlungsweise'])])) {
                        $unbekanntezahlungsweisen[strtolower($warenkorb['zahlungsweise'])] = false;
                    }
                    $tmp = array();
                    $tmp['bestellnummer'] = $warenkorb['onlinebestellnummer'];
                    $unbekanntezahlungsweisen[strtolower($warenkorb['zahlungsweise'])][] = $tmp;
                }
            }

            if ($shopimportid && $tmpauftragid) {
                $this->app->DB->Update("UPDATE shopimport_auftraege SET imported='1' WHERE id='$shopimportid' LIMIT 1");
            }
            if ($shopimportid) {
                $shopextid = $this->app->DB->real_escape_string($this->app->DB->Select("SELECT extid FROM shopimport_auftraege WHERE id='$shopimportid' LIMIT 1"));
            }
            if ($shopextid) {
                $this->app->DB->Select("UPDATE $doctype SET shopextid='$shopextid' WHERE id='$tmpauftragid' LIMIT 1");
            }
            $this->app->erp->RunHook('Shopimportwarenkorb', 4, $doctype, $tmpauftragid, $shopid, $warenkorb);
            $this->app->erp->RunHook('Shopimport', 3, $doctype, $tmpauftragid, $shopid);
            $adresse = '';
        } else {
            // Error...
        }

        return $success_import;
    }

    /**
     * @param String $gruppenBezeichnung im Shop
     * @param int $adresseId
     * @param int $shopId
     * @param int $projektId
     *
     * @return string
     */
    private function shopimportAdresseGruppenMapping($gruppenBezeichnung, $adresseId, $shopId, $projektId) {
        $kundenGruppen = $this->getShopimportKundenGruppenZuordnungen($gruppenBezeichnung, $shopId, $projektId);
        if (empty($kundenGruppen)) {
            return '';
        }
        foreach ($kundenGruppen as $gruppe => $rolle) {
            $this->app->erp->AddRolleZuAdresse($adresseId, $rolle, 'von', 'Gruppe', $gruppe);
        }

        return '';
    }

    /**
     * @param String $gruppenBezeichnung im Shop
     * @param int $shopId
     * @param int $projektId
     *
     * @return array
     */
    private function getShopimportKundenGruppenZuordnungen($gruppenBezeichnung, $shopId, $projektId) {
        $gefundeneGruppen = $this->app->DB->SelectArr("SELECT gruppeid, type FROM shopexport_kundengruppen WHERE shopid='$shopId' AND extgruppename='$gruppenBezeichnung' AND projekt='$projektId'");

        if (empty($gefundeneGruppen)) {
            //Fallback, falls kein projektspezifisches Mapping gefunden
            $gefundeneGruppen = $this->app->DB->SelectArr("SELECT gruppeid, type FROM shopexport_kundengruppen WHERE shopid='$shopId' AND extgruppename='$gruppenBezeichnung' AND projekt='0'");
        }

        if (empty($gefundeneGruppen)) {
            return [];
        }

        $kundenGruppen = [];
        foreach ($gefundeneGruppen as $gruppe) {
            $kundenGruppen[$gruppe['gruppeid']] = $gruppe['type'];
        }

        return $kundenGruppen;
    }

    /**
     * @param $shopImportedOrderId
     *
     * @return bool
     */
    public function setShopImportedOrderTrash($shopImportedOrderId) {
        $this->app->DB->Update(
                sprintf(
                        'UPDATE shopimport_auftraege SET trash=1 WHERE id= %d LIMIT 1',
                        (int) $shopImportedOrderId
                )
        );
        return $this->app->DB->affected_rows() > 0;
    }

    public function importShopOrder($shopImportedOrderId, $utf8coding, $bestandskunde, $custumerNumberImported, &$unknownPaymentTypes) {
        $shopImportedOrder = $this->app->DB->SelectRow(
                sprintf(
                        'SELECT * FROM shopimport_auftraege WHERE imported=0 AND trash=0 AND id=%d LIMIT 1',
                        $shopImportedOrderId
                )
        );
        if (empty($shopImportedOrder)) {
            return ['success' => 0];
        }

        if (isset($shopImportedOrder['jsonencoded']) && $shopImportedOrder['jsonencoded']) {
            $shopOrder = json_decode(base64_decode($shopImportedOrder['warenkorb']), true);
        } else {
            $shopOrder = unserialize(base64_decode($shopImportedOrder['warenkorb']));
        }

        //alle leerzeichen am amfang und ende entfernen + umbrueche komplett entfernen
        if ($utf8coding == '1') {
            $shopOrderCleaned = $this->app->erp->CleanDataBeforImportUTF8($shopOrder, false);
        } else {
            $shopOrderCleaned = $this->app->erp->CleanDataBeforImport($shopOrder, false);
        }
        if ($shopOrderCleaned['name'] === '') {
            $shopOrderCleaned['name'] = $shopOrderCleaned['ansprechpartner'];
            $shopOrderCleaned['ansprechpartner'] = '';
        }
        if ($shopOrderCleaned['lieferadresse_name'] === '') {
            $shopOrderCleaned['lieferadresse_name'] = $shopOrderCleaned['lieferadresse_ansprechpartner'];
            $shopOrderCleaned['lieferadresse_ansprechpartner'] = '';
        }

        if ($shopOrderCleaned['name'] === '' && !empty($shopOrderCleaned['lieferadresse_name'])) {
            $shopOrderCleaned['name'] = $shopOrderCleaned['lieferadresse_name'];
        }

        foreach ($shopOrderCleaned as $k => $v) {
            if (!is_array($v)) {
                $shopOrderCleaned[$k] = $this->app->erp->fixeUmlaute($v);
            }
        }
        $umlautefehler = false;
        if ((String) $shopOrder['name'] !== '' && (String) $shopOrderCleaned['name'] === '') {
            $umlautefehler = true;
            $this->app->Container->get('Logger')->error('Kodierungsfehler in shopimport_auftraege ' . $shopImportedOrderId);
        }
        $succes = $this->KundeAnlegenUpdate($shopImportedOrderId, '', $shopOrderCleaned, $bestandskunde, $custumerNumberImported, $unknownPaymentTypes);

        return ['codingerror' => $umlautefehler, 'success' => $succes];
    }

    public function ShopimportView() {
        $this->ShopimportImport(showonly: true);
    }

    public function ShopimportImport($id = '', $count = 0, $returncount = false, $showonly = false) {
        $deletedRows = 0;
        if (!is_numeric($id) && $this->app->Secure->GetPOST('deletedouble')) {
            $showonly = true;
            $id = (int) $this->app->Secure->GetGET('id');
            $this->app->DB->Update(
                    sprintf(
                            'UPDATE shopimport_auftraege AS sa
          INNER JOIN auftrag a ON sa.bestellnummer = a.internet AND sa.shopid = a.shop
          SET sa.trash = 1
          WHERE IFNULL(a.internet,\'\') <> \'\' AND sa.trash = 0 AND sa.imported = 0 AND
                (sa.shopid = %d OR %d = 0)',
                            $id, $id
                    )
            );
            $deletedRows = (int) $this->app->DB->affected_rows();
            $this->app->Tpl->Add('IMPORT', '<div class="info">Es wurden ' . $deletedRows . ' bereits importierte Datens&auml;tze entfernt.</div>');
        }
        $runningcronjob = $this->app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'shopimport' AND aktiv=1 AND mutex = 1 AND UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP(letzteausfuerhung) < 300 LIMIT 1");
        if ($runningcronjob && !$showonly) {
            $this->app->Location->execute('index.php?module=shopimport&action=list');
        }
        if (!is_numeric($id)) {
            $id = $this->app->Secure->GetGET('id');
        }

        $shopexportarr = $this->app->DB->SelectRow("SELECT * FROM shopexport WHERE id='$id'");
        $projekt = $shopexportarr['projekt']; //$this->app->DB->Select("SELECT projekt FROM shopexport WHERE id='$id'");
        $demomodus = $shopexportarr['demomodus']; //$this->app->DB->Select("SELECT demomodus FROM shopexport WHERE id='$id'");
        $einzelsync = $shopexportarr['einzelsync']; //$this->app->DB->Select("SELECT einzelsync FROM shopexport WHERE id='$id'");
        $utf8codierung = $shopexportarr['utf8codierung']; //$this->app->DB->Select("SELECT utf8codierung FROM shopexport WHERE id='$id'");

        if (!$returncount) {
            $this->app->erp->Headlines('Shopimport');
            $this->app->erp->MenuEintrag('index.php?module=shopimport&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
        }
        //name, strasse, ort, plz und kundenummer, emailadresse  oder bestellung kam von login account ==> Kunde aus DB verwenden
        //ACHTUNG Lieferadresse immer aus Auftrag!!! aber Lieferadresse extra bei Kunden anlegen
        if ($this->app->Secure->GetPOST('submit') != '') {
            $auftraege = $this->app->Secure->GetPOST('auftrag');

            $import = $this->app->Secure->GetPOST('import');
            $import_kundennummer = $this->app->Secure->GetPOST('import_kundennummer');
            $success_import = 0;
            $insgs_import = 0;
            $unbekanntezahlungsweisen = null;
            $cauftraege = $auftraege ? count($auftraege) : 0;
            for ($i = 0; $i < $cauftraege; $i++) {
                $adresse = '';
                $shopimportid = $auftraege[$i];
                $shopid = $this->app->DB->Select("SELECT shopid FROM shopimport_auftraege WHERE id='$shopimportid' LIMIT 1");
                if ($shopid) {
                    $demomodus = $this->app->DB->Select("SELECT demomodus FROM shopexport WHERE id='$shopid'");
                    $einzelsync = $this->app->DB->Select("SELECT einzelsync FROM shopexport WHERE id='$shopid'");
                    $utf8codierung = $this->app->DB->Select("SELECT utf8codierung FROM shopexport WHERE id='$shopid'");
                }
                $projekt = $this->app->DB->Select("SELECT projekt FROM shopimport_auftraege WHERE id='$shopimportid' LIMIT 1");
                if ($import[$shopimportid] === 'warten') {

                } else if ($import[$shopimportid] === 'muell') {
                    $this->setShopImportedOrderTrash($shopimportid);
                } else if ($import[$shopimportid] === 'import') {

                    // Check given number!
                    $bestandskunde = false;
                    if (!empty($import_kundennummer[$shopimportid])) {
                        $kundennummer = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer = '".$import_kundennummer[$shopimportid]."'");
                        if (empty($kundennummer)) {
                            continue;
                        }
                        $bestandskunde = true;
                    }

                    $res = $this->importShopOrder($shopimportid, $utf8codierung, $bestandskunde, $import_kundennummer[$shopimportid], $unbekanntezahlungsweisen);
                    if ($res['codingerror']) {
                        $umlautefehler = true;
                    }
                    if ($res['success']) {
                        $success_import += $res['success'];
                    }
                    $insgs_import++;
                }
            } // ende for

            if ($unbekanntezahlungsweisen) {
                $meldung = '';
                foreach ($unbekanntezahlungsweisen as $k => $v) {
                    $meldung .= 'Unbekannte Zahlungsart: ' . $k . ' in Bestellung(en): ';
                    $first = true;
                    foreach ($v as $k2 => $v2) {
                        if (!$first) {
                            $meldung .= ', ';
                        }
                        $first = false;
                        $meldung .= $v2['bestellnummer'];
                    }
                    $meldung .= "<br />\r\n";
                }

                if (isset($this->app->User) && method_exists($this->app->User, 'GetID') && $this->app->User->GetID()) {
                    $this->app->erp->EventMitSystemLog($this->app->User->GetID(), $meldung, -1, '', 'warning', 1);
                }
            }
            if (!empty($umlautefehler)) {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">$success_import" . ($success_import != $insgs_import ? " von $insgs_import " : '') . " Auftr&auml;ge importiert. Auftr&auml;ge mit Kodierungsproblemen. Bitte pr&uuml;fen Sie sie UTF8-Einstellung in der Shopschnittstelle!</div>");
            } else {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">$success_import" . ($success_import != $insgs_import ? " von $insgs_import " : '') . " Auftr&auml;ge importiert!</div>");
            }
            $this->app->Location->execute('index.php?module=shopimport&action=list&msg=' . $msg);
        }


        if (!$showonly && $id)   {
            try {
                $pageContents = $this->app->remote->RemoteConnection($id);
            } catch (Exception $e) {
                $pageContents = $e->getMessage();
            }
            if ($pageContents === 'success') {
                $shopexportarr = $this->app->DB->SelectRow("SELECT * FROM shopexport WHERE id = '$id' LIMIT 1");
                $holealle = $shopexportarr['holealle']; //$this->app->DB->Select("SELECT holealle FROM shopexport WHERE id = '$id' LIMIT 1");
                $statusaendern = $shopexportarr['nummersyncstatusaendern']; //$this->app->DB->Select("SELECT nummersyncstatusaendern FROM shopexport WHERE id = '$id' LIMIT 1");
                $auftragabgleich = $shopexportarr['auftragabgleich']; //$this->app->DB->Select("SELECT auftragabgleich FROM shopexport WHERE id = '$id' LIMIT 1");
                $zeitraum = array('datumvon' => $shopexportarr['datumvon'], 'datumbis' => $shopexportarr['datumbis'], 'tmpdatumvon' => $shopexportarr['tmpdatumvon'], 'tmpdatumbis' => $shopexportarr['tmpdatumbis'], 'anzgleichzeitig' => $shopexportarr['anzgleichzeitig']);
                //$this->app->DB->SelectArr("SELECT datumvon, datumbis,tmpdatumvon, tmpdatumbis, anzgleichzeitig FROM shopexport WHERE id = '$id' LIMIT 1");
                /* if(!empty($zeitraum))
                  {
                  $zeitraum = reset($zeitraum);
                  } */
                $anzgleichzeitig = 1;
                if (isset($zeitraum['anzgleichzeitig'])) {
                    $anzgleichzeitig = (int) $zeitraum['anzgleichzeitig'];
                }

                if ($anzgleichzeitig > 1) {
                    $result = $this->app->remote->RemoteGetAuftrag($id);
                    if (!empty($result) && is_array($result) && isset($result[0])) {
                        $maxtime = false;
                        $mintime = false;
                        $gesamtanzahl = (!empty($result) ? count($result) : 0);
                        for ($i = 0; $i < $gesamtanzahl; $i++) {
                            $projekt = $this->app->DB->Select("SELECT projekt FROM shopexport WHERE id = '$id' LIMIT 1");
                            $auftrag = $result[$i]['id'];
                            if (isset($result[$i]['warenkorbjson'])) {
                                $isjson = true;
                                $tmpwarenkorb = json_decode(base64_decode($result[$i]['warenkorbjson']), true);
                            } else {
                                $isjson = false;
                                $tmpwarenkorb = unserialize(base64_decode($result[$i]['warenkorb']));
                            }
                            if (!empty($tmpwarenkorb['projekt']) && $this->app->DB->Select("SELECT id FROM projekt WHERE id = '" . (int) $tmpwarenkorb['projekt'] . "' LIMIT 1")) {
                                $projekt = (int) $tmpwarenkorb['projekt'];
                            }
                            if (!empty($tmpwarenkorb['zeitstempel'])) {
                                $time = strtotime($tmpwarenkorb['zeitstempel']);
                                if ($time < 0) {
                                    $time = 0;
                                }
                                if ($maxtime === false) {
                                    $maxtime = $time;
                                }
                                if ($mintime === false) {
                                    $mintime = $time;
                                }
                                if ($time > $maxtime) {
                                    $maxtime = $time;
                                }
                                if ($time < $mintime) {
                                    $mintime = $time;
                                }
                            }
                            $onlinebestellnummer = $tmpwarenkorb['onlinebestellnummer'];
                            if (!empty($tmpwarenkorb['useorderid']) ||
                                    (!is_numeric($onlinebestellnummer) && trim((String) $onlinebestellnummer) !== '')) {
                                $onlinebestellnummer = $tmpwarenkorb['auftrag'];
                            }

                            if ($holealle && $onlinebestellnummer) {
                                $neue_nummer = (int) $onlinebestellnummer + 1;
                                $this->app->DB->Update("UPDATE shopexport SET ab_nummer = '$neue_nummer' WHERE id = '$id'");
                            }
                            $sessionid = isset($result[$i]['sessionid']) ? $result[$i]['sessionid'] : '';
                            if ($isjson) {
                                $warenkorb = $result[$i]['warenkorbjson'];
                            } else {
                                $warenkorb = $result[$i]['warenkorb'];
                            }
                            $logdatei = isset($result[$i]['logdatei']) ? $result[$i]['logdatei'] : null;
                            if (empty($logdatei)) {
                                $logdatei = date('Y-m-d H:i:s');
                            }
                            if (isset($tmpwarenkorb['subshop']) && $tmpwarenkorb['subshop']) {
                                $subshopprojekt = $this->app->DB->Select("SELECT projekt FROM shopexport_subshop WHERE shop = '$id' AND aktiv = 1 AND subshopkennung = '" . $this->app->DB->real_escape_string($tmpwarenkorb['subshop']) . "' LIMIT 1");
                                if ($subshopprojekt) {
                                    $projekt = $subshopprojekt;
                                }
                            }
                            $letzteonlinebestellnummer = $tmpwarenkorb['onlinebestellnummer'];
                            unset($tmpwarenkorb);

                            //globalerauftragsnummernkreis
                            $standardcheck = true;
                            $modulename = $this->app->DB->Select(
                                    sprintf(
                                            "SELECT modulename FROM shopexport WHERE id = %d AND modulename <> '' AND (shoptyp = 'intern')",
                                            $id
                                    )
                            );
                            $shopIds = [$id];
                            $otherModules = empty($modulename) ? null :
                                    $this->app->DB->SelectFirstCols(
                                            sprintf(
                                                    "SELECT id
                  FROM shopexport
                  WHERE modulename = '%s' AND id <> %d",
                                                    $this->app->DB->real_escape_string($modulename), $id
                                            )
                            );
                            if (!empty($otherModules) && $this->app->erp->ModulVorhanden($modulename)) {
                                $obj = $this->app->erp->LoadModul($modulename);
                                if (!empty($obj) && method_exists($obj, 'EinstellungenStruktur')) {
                                    $konfiguration = $obj->EinstellungenStruktur();
                                    if ($konfiguration && isset($konfiguration['globalerauftragsnummernkreis']) && $konfiguration['globalerauftragsnummernkreis']) {
                                        $shopIds = array_merge([$id], $otherModules);
                                        $standardcheck = false;
                                        /* $checkdoppeltimported = $this->app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$this->app->DB->real_escape_string($auftrag)."' and ((modulename = '".$this->app->DB->real_escape_string($modulename)."' AND shoptyp = 'intern') OR shopid = '$id') and warenkorb = '".$this->app->DB->real_escape_string($warenkorb)."' AND trash = 0
                                          AND (imported = 0 OR (imported = 1 AND DATE_SUB(NOW(),INTERVAL 10 MINUTE)>logdatei ))
                                          LIMIT 1"); */
                                        /* $checkdoppeltimported = $this->app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$this->app->DB->real_escape_string($auftrag)."' and ((modulename = '".$this->app->DB->real_escape_string($modulename)."' AND shoptyp = 'intern') OR shopid = '$id') AND trash = 0
                                          AND (imported = 0 OR (imported = 1 AND DATE_SUB(NOW(),INTERVAL 10 MINUTE)>logdatei ))
                                          LIMIT 1"); */
                                    }
                                }
                            }
                            $checkdoppeltimported = $this->app->DB->Select(
                                    sprintf(
                                            "SELECT id
                  FROM shopimport_auftraege
                  WHERE extid = '%s' and shopid IN (%s) AND trash = 0
                  AND (imported = 0 OR (imported = 1 AND DATE_SUB(NOW(),INTERVAL 10 MINUTE)>logdatei ))
                  LIMIT 1",
                                            $this->app->DB->real_escape_string($auftrag), implode(',', $shopIds)
                                    )
                            );
                            /* if($standardcheck)
                              { */
                            /* $checkdoppeltimported = $this->app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$this->app->DB->real_escape_string($auftrag)."' and shopid = '$id' and warenkorb = '".$this->app->DB->real_escape_string($warenkorb)."' AND trash = 0
                              AND (imported = 0 OR (imported = 1 AND DATE_SUB(NOW(),INTERVAL 10 MINUTE)>logdatei ))
                              LIMIT 1"); */
                            /* $checkdoppeltimported = $this->app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$this->app->DB->real_escape_string($auftrag)."' and shopid = '$id' AND trash = 0
                              AND (imported = 0 OR (imported = 1 AND DATE_SUB(NOW(),INTERVAL 10 MINUTE)>logdatei ))
                              LIMIT 1");
                              } */
                            $insid = null;
                            if (empty($checkdoppeltimported)) {
                                $this->app->DB->Insert("INSERT INTO shopimport_auftraege (id,extid,sessionid,warenkorb,imported,projekt,bearbeiter,logdatei)
                    VALUES('','" . $this->app->DB->real_escape_string($auftrag) . "','" . $this->app->DB->real_escape_string($sessionid) . "','" . $this->app->DB->real_escape_string($warenkorb) . "','0','$projekt','" . $this->app->DB->real_escape_string($this->app->User->GetName()) . "','" . $this->app->DB->real_escape_string($logdatei) . "')");
                                $insid = $this->app->DB->GetInsertID();
                                if ($insid) {
                                    if ($isjson) {
                                        $this->app->DB->Update("UPDATE shopimport_auftraege set jsonencoded = 1 where id = '$insid'");
                                    }
                                    $this->app->DB->Update("UPDATE shopimport_auftraege set shopid = '$id' where id = '$insid'");
                                    $this->app->DB->Update("UPDATE shopimport_auftraege set bestellnummer = '" . $this->app->DB->real_escape_string($letzteonlinebestellnummer) . "' where id = '$insid'");
                                }
                            }
                            if ($demomodus != '1') {
                                $this->app->remote->RemoteDeleteAuftrag($id, $auftrag, $letzteonlinebestellnummer);
                            } elseif ($demomodus == '1') {
                                break;
                            }
                            unset($letzteonlinebestellnummer);
                        }
                        if (!$demomodus) {
                            if (empty($maxtime)) {
                                $maxtime = strtotime(date('Y-m-d H:i:s'));
                            }
                            $datumvon = strtotime($zeitraum['datumvon']);
                            $datumbis = strtotime($zeitraum['datumbis']);
                            $tmpdatumvon = strtotime($zeitraum['tmpdatumvon']);
                            $tmpdatumbis = strtotime($zeitraum['tmpdatumbis']);
                            if ($datumvon < 0) {
                                $datumvon = 0;
                            }
                            if ($datumbis < 0) {
                                $datumbis = 0;
                            }
                            if ($tmpdatumvon < 0) {
                                $tmpdatumvon = 0;
                            }
                            if ($tmpdatumbis < 0) {
                                $tmpdatumbis = 0;
                            }
                            $this->app->DB->Update("UPDATE shopexport SET datumvon = '" . date('Y-m-d H:i:s', $maxtime) . "', tmpdatumbis = '0000-00-00' WHERE id = '$id' LIMIT 1");
                        }
                    } else {
                        if (!$demomodus) {
                            if (is_array($result) && !empty($result['zeitstempel'])) {
                                $this->app->DB->Update("UPDATE shopexport SET datumvon = '" . date('Y-m-d H:i:s', strtotime($result['zeitstempel'])) . "', tmpdatumbis = '0000-00-00' WHERE id = '$id' AND datumvon < '" . date('Y-m-d H:i:s', strtotime($result['zeitstempel'])) . "' LIMIT 1");
                            }
                            if (empty($maxtime)) {
                                $maxtime = strtotime(date('Y-m-d H:i:s'));
                            }
                            $datumvon = strtotime($zeitraum['datumvon']);
                            $datumbis = strtotime($zeitraum['datumbis']);
                            $tmpdatumvon = strtotime($zeitraum['tmpdatumvon']);
                            $tmpdatumbis = strtotime($zeitraum['tmpdatumbis']);
                            if ($datumvon < 0) {
                                $datumvon = 0;
                            }
                            if ($datumbis < 0) {
                                $datumbis = 0;
                            }
                            if ($tmpdatumvon < 0) {
                                $tmpdatumvon = 0;
                            }
                            if ($tmpdatumbis < 0) {
                                $tmpdatumbis = 0;
                            }
                            if ($tmpdatumbis) {
                                $this->app->DB->Update("UPDATE shopexport SET tmpdatumbis = '0000-00-00' WHERE id = '$id' LIMIT 1");
                            }
                        }
                    }
                } else {
                    $gesamtanzahl = $this->app->remote->RemoteGetAuftraegeAnzahl($id);
                    $maxmanuell = (int) $this->app->DB->Select("SELECT maxmanuell FROM shopexport WHERE id = '$id' LIMIT 1");
                    if ($maxmanuell <= 0) {
                        $maxmanuell = 100;
                    }
                    if ($gesamtanzahl > $maxmanuell) {
                        $gesamtanzahl = $maxmanuell;
                    }
                    if ($einzelsync == '1' && $gesamtanzahl > 1 && $maxmanuell <= 1) {
                        $gesamtanzahl = 1;
                    }
                    if ($gesamtanzahl > 0) {
                        for ($i = 0; $i < $gesamtanzahl; $i++) {
                            //import au
                            $result = $this->app->remote->RemoteGetAuftrag($id);
                            if (is_array($result)) {
                                foreach ($result as $cart) {
                                    $auftrag = $cart['id'];
                                    if (isset($cart['warenkorbjson'])) {
                                        $isjson = true;
                                        $tmpwarenkorb = json_decode(base64_decode($cart['warenkorbjson']), true);
                                    } else {
                                        $isjson = false;
                                        $tmpwarenkorb = unserialize(base64_decode($cart['warenkorb']));
                                    }
                                    $onlinebestellnummer = $tmpwarenkorb['onlinebestellnummer'];
                                    if (!empty($tmpwarenkorb['useorderid']) || (!is_numeric($onlinebestellnummer) && trim((String) $onlinebestellnummer) !== '')) {
                                        $onlinebestellnummer = $tmpwarenkorb['auftrag'];
                                    }
                                    if ($holealle && $onlinebestellnummer) {
                                        $neue_nummer = (int) $onlinebestellnummer + 1;
                                        $this->app->DB->Update("UPDATE shopexport SET ab_nummer = '$neue_nummer' WHERE id = '$id'");
                                    }
                                    $sessionid = $cart['sessionid'];
                                    if ($isjson) {
                                        $warenkorb = $cart['warenkorbjson'];
                                    } else {
                                        $warenkorb = $cart['warenkorb'];
                                    }
                                    $logdatei = $cart['logdatei'];
                                    if (empty($logdatei)) {
                                        $logdatei = date('Y-m-d H:i:s');
                                    }
                                    $projekt = $this->app->DB->Select("SELECT projekt FROM shopexport WHERE id = '$id' LIMIT 1");
                                    if (!empty($tmpwarenkorb['projekt']) && $this->app->DB->Select("SELECT id FROM projekt WHERE id = '" . (int) $tmpwarenkorb['projekt'] . "' LIMIT 1")) {
                                        $projekt = (int) $tmpwarenkorb['projekt'];
                                    }
                                    if (isset($tmpwarenkorb['subshop']) && $tmpwarenkorb['subshop']) {
                                        $subshopprojekt = $this->app->DB->Select("SELECT projekt FROM shopexport_subshop WHERE shop = '$id' AND aktiv = 1 AND subshopkennung = '" . $this->app->DB->real_escape_string($tmpwarenkorb['subshop']) . "' LIMIT 1");
                                        if ($subshopprojekt) {
                                            $projekt = $subshopprojekt;
                                        }
                                    }
                                    unset($tmpwarenkorb);

                                    $standardcheck = true;
                                    $modulename = $this->app->DB->Select(
                                            sprintf(
                                                    "SELECT modulename FROM shopexport WHERE id = %d AND modulename <> '' AND (shoptyp = 'intern')",
                                                    $id
                                            )
                                    );
                                    $shopIds = [$id];
                                    $otherModules = empty($modulename) ? null :
                                            $this->app->DB->SelectFirstCols(
                                                    sprintf(
                                                            "SELECT id
                      FROM shopexport
                      WHERE modulename = '%s' AND id <> %d",
                                                            $this->app->DB->real_escape_string($modulename), $id
                                                    )
                                    );
                                    if (!empty($otherModules) && $this->app->erp->ModulVorhanden($modulename)) {
                                        $obj = $this->app->erp->LoadModul($modulename);
                                        if (!empty($obj) && method_exists($obj, 'EinstellungenStruktur')) {
                                            $konfiguration = $obj->EinstellungenStruktur();
                                            if ($konfiguration && isset($konfiguration['globalerauftragsnummernkreis']) && $konfiguration['globalerauftragsnummernkreis']) {
                                                $shopIds = array_merge([$id], $otherModules);
                                                $standardcheck = false;
                                                /* $checkdoppeltimported = $this->app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$this->app->DB->real_escape_string($auftrag)."' and ((modulename = '".$this->app->DB->real_escape_string($modulename)."' AND shoptyp = 'intern') OR shopid = '$id') and warenkorb = '".$this->app->DB->real_escape_string($warenkorb)."' AND trash = 0
                                                  AND (imported = 0 OR (imported = 1 AND DATE_SUB(NOW(),INTERVAL 10 MINUTE)>logdatei ))
                                                  LIMIT 1"); */
                                                /* $checkdoppeltimported = $this->app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '".$this->app->DB->real_escape_string($auftrag)."' and ((modulename = '".$this->app->DB->real_escape_string($modulename)."' AND shoptyp = 'intern') OR shopid = '$id') AND trash = 0
                                                  AND (imported = 0 OR (imported = 1 AND DATE_SUB(NOW(),INTERVAL 10 MINUTE)>logdatei ))
                                                  LIMIT 1"); */
                                            }
                                        }
                                    }
                                    $checkdoppeltimported = $this->app->DB->Select(
                                            sprintf(
                                                    "SELECT id
                      FROM shopimport_auftraege
                      WHERE extid = '%s' and shopid IN (%s) AND trash = 0
                      AND (imported = 0 OR (imported = 1 AND DATE_SUB(NOW(),INTERVAL 10 MINUTE)>logdatei ))
                      LIMIT 1",
                                                    $this->app->DB->real_escape_string($auftrag), implode(',', $shopIds)
                                            )
                                    );

                                    /* if($standardcheck){ */
                                    /* $checkdoppeltimported = $this->app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '" . $this->app->DB->real_escape_string($auftrag) . "' and shopid = '$id' and warenkorb = '" . $this->app->DB->real_escape_string($warenkorb) . "' AND trash = 0
                                      AND (imported = 0 OR (imported = 1 AND DATE_SUB(NOW(),INTERVAL 10 MINUTE)>logdatei ))
                                      LIMIT 1"); */
                                    /* $checkdoppeltimported = $this->app->DB->Select("SELECT id FROM shopimport_auftraege WHERE extid = '" . $this->app->DB->real_escape_string($auftrag) . "' and shopid = '$id' AND trash = 0
                                      AND (imported = 0 OR (imported = 1 AND DATE_SUB(NOW(),INTERVAL 10 MINUTE)>logdatei ))
                                      LIMIT 1");
                                      } */

                                    $insid = null;
                                    if ($demomodus == '1') {
                                        $checkdoppeltimported = null;
                                    }
                                    if (!$checkdoppeltimported) {
                                        $this->app->DB->Insert("INSERT INTO shopimport_auftraege (id,extid,sessionid,warenkorb,imported,projekt,bearbeiter,logdatei)
                          VALUES('','$auftrag','$sessionid','$warenkorb','0','$projekt','" . $this->app->User->GetName() . "','$logdatei')");
                                        $insid = $this->app->DB->GetInsertID();
                                        if ($insid) {
                                            $this->app->DB->Update("UPDATE shopimport_auftraege set shopid = '$id' where id = '$insid'");
                                            $this->app->DB->Update("UPDATE shopimport_auftraege set logdatei = now() where id = '$insid' AND logdatei = '0000-00-00' OR logdatei > now()");
                                            if ($isjson) {
                                                $this->app->DB->Update("UPDATE shopimport_auftraege set jsonencoded = 1 where id = '$insid'");
                                            }
                                        }
                                    }

                                    if ($demomodus != '1') {
                                        $this->app->remote->RemoteDeleteAuftrag($id, $auftrag);
                                    } elseif ($demomodus == '1') {
                                        $i = $gesamtanzahl;
                                    }
                                } // foreach
                            } // is_array
                        } // for
                    } // gesamtanzahl
                }
            } else {
                if (!$returncount) {
                    $this->app->Tpl->Set('IMPORT', "<div class=\"error\">Verbindungsprobleme! Bitte Administrator kontaktieren! ($pageContents)</div>");
                } else {
                    $this->error = "<div class=\"error\">Verbindungsprobleme! Bitte Administrator kontaktieren! ($pageContents)</div>";
                }
            }
        }

        if (!$returncount) {
            $this->drawShopOrderTable($deletedRows);
        } else {
            return $count + (is_numeric($gesamtanzahl) ? $gesamtanzahl : 0);
        }
    }

    /**
     * @param array $arr
     * Find the correct customer
     *
     * @return array
     * array('kundennummer', int 'match')
     */

    public function getCustomerNumberFromShopCart($arr) {

        $match = SELF::CUSTOMER_NO_MATCH;

        if (!empty($arr['jsonencoded'])) {
            $warenkorb = json_decode(base64_decode($arr['warenkorb']), true);
        } else {
            $warenkorb = unserialize(base64_decode($arr['warenkorb']));
        }
        foreach ($warenkorb as $key => $value) {
            if (is_string($warenkorb[$key])) {
                $warenkorb[$key] = trim($warenkorb[$key]);
            }
        }
        foreach ($warenkorb as $k => $v) {
            $warenkorb[$k] = $this->app->erp->fixeUmlaute($v);
        }

        $kundenurvonprojekt = $this->app->DB->Select("SELECT kundenurvonprojekt FROM shopexport WHERE id = '" . $arr['shopid'] . "' LIMIT 1");
        $adresseprojekt = '';
        if ($kundenurvonprojekt) {
            $adresseprojekt = $this->app->DB->Select("SELECT projekt FROM shopexport WHERE id = '" . $arr['shopid'] . "' LIMIT 1");
        }
        if (isset($warenkorb['subshop']) && $warenkorb['subshop']) {
            $subshopprojekt = $this->app->DB->Select("SELECT projekt FROM shopexport_subshop WHERE shop = '" . $arr['shopid'] . "' AND aktiv = 1 AND subshopkennung = '" . $this->app->DB->real_escape_string($warenkorb['subshop']) . "' LIMIT 1");
            if ($subshopprojekt) {
                if (!$kundenurvonprojekt) {
                    $adresseprojekt = $subshopprojekt;
                }
                $arr['abkuerzung'] = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$adresseprojekt' LIMIT 1");
            }
        }
        if ($kundenurvonprojekt) {
            $adresseprojekt = " AND projekt = '" . $adresseprojekt . "' ";
        } else {
            $adresseprojekt = '';
        }

        $kundennummer_data = $this->app->DB->Select("
            SELECT kundennummer FROM adresse
            WHERE name='" . $this->app->DB->real_escape_string($warenkorb['name']) . "'
            AND strasse='" . $this->app->DB->real_escape_string($warenkorb['strasse']) . "'
            AND plz='" . $this->app->DB->real_escape_string($warenkorb['plz']) . "'
            AND ort='" . $this->app->DB->real_escape_string($warenkorb['ort']) . "'
            AND geloescht!=1
            AND kundennummer <> ''
            $adresseprojekt
            LIMIT 1"
        );

        if (!empty($warenkorb['kundennummer'])) {
            $kundennummer_nummer = $this->app->DB->Select("
                SELECT kundennummer
                FROM adresse
                WHERE kundennummer='" . $warenkorb['kundennummer'] . "' $adresseprojekt AND geloescht!=1 AND kundennummer <> ''
                LIMIT 1"
            );
            if ($kundennummer_nummer) {
                if ($kundennummer_data && ($kundennummer_data == $kundennummer_nummer)) {
                    $kundennummer = $kundennummer_name;
                    $match = SELF::CUSTOMER_FULL_MATCH;
                } else {
                    $kundennummer = $kundennummer_nummer;
                    $match = SELF::CUSTOMER_DATA_MISMATCH;
                }
            } else if ($kundennummer_data) {
                $kundennummer = $kundennummer_data;
                $match = SELF::CUSTOMER_NUMBER_MISMATCH;
            } else {
                $match = SELF::CUSTOMER_NO_MATCH;
            }

        } else { // empty($warenkorb['kundennummer'])
            if ($kundennummer_data) {
                $kundennummer = $kundennummer_data;
                $match = SELF::CUSTOMER_FULL_MATCH;
            } else {
                $kundennummer_name = $this->app->DB->Select("
                    SELECT kundennummer FROM adresse
                    WHERE name='" . $this->app->DB->real_escape_string($warenkorb['name']) . "'
                    AND geloescht!=1
                    AND kundennummer <> ''
                    $adresseprojekt
                    LIMIT 1"
                );
                if ($kundennummer_name) {
                    $match = SELF::CUSTOMER_ONLY_NAME_MATCH;
                }
            }
        }

        return array('kundennummer' => $kundennummer, 'match' => $match);
    }

    public function drawShopOrderTable($deletedRows) {
        $checkglobal = null;
        $htmltable = new HTMLTable(0, '100%', '', 3, 1, 'font-size:85%');
        $htmltable->AddRowAsHeading(array('Import', 'M&uuml;ll', 'Sp&auml;ter', 'Projekt','Internet','Datum', 'Name', 'Bemerkung', 'Strasse', 'PLZ', 'Ort', 'Land', 'Zahlung', 'Kd.Nr.', 'vorhanden')); //, 'Betrag', 'Offen', 'Zahlung', 'Partner'));
        $htmltable->ChangingRowColors('#e0e0e0', '#fff');

        $shopid = $this->app->Secure->GetGET('shopid');
        $where = '';
        if (!empty($shopid)) {
            $where = 'AND sa.shopid=' . (int) $shopid;
        }

        $bestellnummer = $this->app->Secure->GetGET('bestellnummer');
        if (!empty($bestellnummer)) {
            $bestellnummer = preg_replace('/[^\w-]/', '', $bestellnummer);
            $where .= " AND sa.bestellnummer='$bestellnummer'";
        }

        $arr = $this->app->DB->SelectArr("SELECT sa.*, p.abkuerzung FROM shopimport_auftraege sa left join projekt p on sa.projekt = p.id WHERE sa.imported='0' AND sa.trash='0' $where ORDER BY sa.logdatei LIMIT 100");
        if (is_array($arr) && (!empty($arr) ? count($arr) : 0) > 0) {

            //Alte Auftraege prüfen
            $alteauftraegeohnebestellnummer = $this->app->DB->Query("SELECT sa.* FROM shopimport_auftraege sa WHERE isnull(bestellnummer) AND sa.trash='0' LIMIT 100");
            while ($row = $this->app->DB->Fetch_Assoc($alteauftraegeohnebestellnummer)) {
                if ($row['warenkorb'] != '') {
                    if (!empty($row['jsonencoded'])) {
                        $warenkorb = json_decode(base64_decode($row['warenkorb']), true);
                    } else {
                        $warenkorb = unserialize(base64_decode($row['warenkorb']));
                    }
                    $this->app->DB->Update("UPDATE shopimport_auftraege set bestellnummer = '" . (isset($warenkorb['onlinebestellnummer']) ? $warenkorb['onlinebestellnummer'] : '') . "' where id = '" . $row['id'] . "'");
                } else {
                    $this->app->DB->Update("UPDATE shopimport_auftraege set bestellnummer = '' where id = '" . $row['id'] . "'");
                }
            }
            $this->app->DB->free($alteauftraegeohnebestellnummer);

            $unbekanntezahlungsweisen = null;
            $enthaeltdoppeltenummern = false;
            $carr = $arr ? count($arr) : 0;
            for ($i = 0; $i < $carr; $i++) {
                if (empty($checkglobal) || !isset($checkglobal[$arr[$i]['shopid']])) {
                    $checkglobal[$arr[$i]['shopid']] = null;

                    if (($modulename = $this->app->DB->Select("SELECT modulename FROM shopexport WHERE id = '" . $arr[$i]['shopid'] . "' AND modulename <> '' AND shoptyp = 'intern'")) && $this->app->DB->Select("SELECT id FROM shopexport WHERE modulename = '" . $this->app->DB->real_escape_string($modulename) . "' AND id <> '" . $arr[$i]['shopid'] . "' LIMIT 1")
                    ) {
                        $shopsintern = $this->app->DB->SelectArr("SELECT id FROM shopexport WHERE id = '" . $arr[$i]['shopid'] . "' OR (modulename = '" . $this->app->DB->real_escape_string($modulename) . "' AND shoptyp = 'intern')");
                        if ($shopsintern) {
                            foreach ($shopsintern as $vs) {
                                $checkglobal[$arr[$i]['shopid']][] = " shopid = '" . $vs['id'] . "' ";
                            }
                            if ($this->app->erp->ModulVorhanden($modulename)) {
                                /** @var ShopimporterBase $obj */
                                $obj = $this->app->erp->LoadModul($modulename);
                                if ($obj && method_exists($obj, 'EinstellungenStruktur')) {
                                    $konfiguration = $obj->EinstellungenStruktur();
                                    if ($konfiguration && isset($konfiguration['globalerauftragsnummernkreis']) && $konfiguration['globalerauftragsnummernkreis']) {
                                    } else {
                                        $checkglobal[$arr[$i]['shopid']] = null;
                                    }
                                } else {
                                    $checkglobal[$arr[$i]['shopid']] = null;
                                }
                            } else {
                                $checkglobal[$arr[$i]['shopid']] = null;
                            }
                        }
                    }
                }
            }

            $carr = $arr ? count($arr) : 0;
            for ($i = 0; $i < $carr; $i++) {
                $projekt = $this->app->DB->Select("SELECT projekt FROM shopexport WHERE id = '" . $arr[$i]['shopid'] . "' LIMIT 1");
                $auftraegeaufspaeter = $this->app->DB->Select("SELECT auftraegeaufspaeter FROM shopexport WHERE id='" . $arr[$i]['shopid'] . "'");
                if (!empty($arr[$i]['jsonencoded'])) {
                    $warenkorb = json_decode(base64_decode($arr[$i]['warenkorb']), true);
                } else {
                    $warenkorb = unserialize(base64_decode($arr[$i]['warenkorb']));
                }
                foreach ($warenkorb as $key => $value) {
                    if (is_string($warenkorb[$key])) {
                        $warenkorb[$key] = trim($warenkorb[$key]);
                    }
                }
                foreach ($warenkorb as $k => $v) {
                    $warenkorb[$k] = $this->app->erp->fixeUmlaute($v);
                }
      
                $getCustomerNumberFromShopCart_result = $this->getCustomerNumberFromShopCart($arr[$i]);

                switch ($getCustomerNumberFromShopCart_result['match']) {
                    case SELF::CUSTOMER_FULL_MATCH:
                        $matchtext = "OK";
                        $matchcolor = 'green';
                        $matchfont = 'normal';
                        $checked = 'checked';
                    break;
                    case SELF::CUSTOMER_NO_MATCH:
                        $matchtext = "Nicht gefunden!";
                        $matchcolor = 'red';
                        $matchfont = 'bold';
                        $auftraegeaufspaeter = true;
                    break;
                    case SELF::CUSTOMER_DATA_MISMATCH:
                        $matchtext = "Adressdaten abweichend";
                        $matchcolor = 'purple';
                        $matchfont = 'bold';
                        $checked = 'checked';
                        $auftraegeaufspaeter = true;
                    break;
                    case SELF::CUSTOMER_NUMBER_MISMATCH:
                        $matchtext = "Kundennummer abweichend";
                        $matchcolor = 'red';
                        $matchfont = 'bold';
                        $auftraegeaufspaeter = true;
                    break;
                    case SELF::CUSTOMER_ONLY_NAME_MATCH:
                        $matchtext = "Name gefunden";
                        $matchcolor = 'purple';
                        $matchfont = 'bold';
                        $auftraegeaufspaeter = true;
                    break;
                }

                $htmltable->NewRow();

                $doppelteonlinebestellnummer = false;
                if (!empty($warenkorb['onlinebestellnummer'])) {
                    $check = $this->app->DB->Select("SELECT id from shopimport_auftraege where id <> '" . $arr[$i]['id'] . "' and shopid = '" . $arr[$i]['shopid'] . "' and bestellnummer = '" . $warenkorb['onlinebestellnummer'] . "' and trash = '0' LIMIT 1");
                    if ($check) {
                        $doppelteonlinebestellnummer = true;
                        $enthaeltdoppeltenummern = true;
                    } elseif (!empty($checkglobal) && isset($checkglobal[$arr[$i]['shopid']]) && $checkglobal[$arr[$i]['shopid']]) {
                        $check = $this->app->DB->Select("SELECT id from shopimport_auftraege where id <> '" . $arr[$i]['id'] . "' and (shopid = '" . $arr[$i]['shopid'] . "' OR " . (implode(" OR ", $checkglobal[$arr[$i]['shopid']])) . ") and bestellnummer = '" . $warenkorb['onlinebestellnummer'] . "' and trash = '0' LIMIT 1");
                        if ($check) {
                            $doppelteonlinebestellnummer = true;
                            $enthaeltdoppeltenummern = true;
                        }
                    }
                }

                $htmltable->AddCol('<input type="hidden" name="auftrag[]" value="' . $arr[$i]['id'] . '"><input type="radio" name="import[' . $arr[$i]['id'] . ']" value="import" ' . (($doppelteonlinebestellnummer || $auftraegeaufspaeter) ? '' : ' checked="checked" ') . '>');
                $htmltable->AddCol('<input type="radio" name="import[' . $arr[$i]['id'] . ']" value="muell">');
                $htmltable->AddCol('<input type="radio" name="import[' . $arr[$i]['id'] . ']" value="warten"' . (($doppelteonlinebestellnummer || $auftraegeaufspaeter) ? ' checked="checked" ' : '') . '>');
                $htmltable->AddCol($arr[$i]['abkuerzung']);
                $htmltable->AddCol(($doppelteonlinebestellnummer ? '<b style="color:red">' : '') . $warenkorb['onlinebestellnummer'] . ($doppelteonlinebestellnummer ? '</b>' : ''));
                $date = DateTimeImmutable::createFromFormat('Y-m-d', $warenkorb['zeitstempel']);
                if ($date !== false) {
                    $date = $date->format('d.m.Y');
                } else {
                    $date = "";
                }
                $htmltable->AddCol($date);
                $htmltable->AddCol($warenkorb['name'].'<br>'.$warenkorb['email']);
                $htmltable->AddCol($warenkorb['internebezeichnung']);

                $htmltable->AddCol($warenkorb['strasse']);
                $htmltable->AddCol($warenkorb['plz']);
                $htmltable->AddCol($warenkorb['ort']);
                $htmltable->AddCol($warenkorb['land']);
                $htmltable->AddCol($warenkorb['zahlungsweise']);

                $input_kundennummer_id = 'import_kundennummer_'.$arr[$i]['id'].'';
                $input_kundennummer_name = 'import_kundennummer['.$arr[$i]['id'].']';
                $input_kundennummer_value = $getCustomerNumberFromShopCart_result['kundennummer'];
                $this->app->YUI->AutoComplete($input_kundennummer_id, "kunde", 1);
                $htmltable->AddCol('<input type="text" size="10" id="'.$input_kundennummer_id.'" name="'.$input_kundennummer_name.'" value="'.$input_kundennummer_value.'">');
                $htmltable->AddCol('<font color="'.$matchcolor.'" style="font-weight:'.$matchfont.'">'.$matchtext.'');

/*                $htmltable->AddCol(number_format($warenkorb['gesamtsumme'], 2, ',', '.'));
                $saldo_kunde = round($this->app->erp->SaldoAdresse($kdr_addresse_id), 2);
                if ($saldo_kunde > 0) {
                    $saldo_kunde = '<b style="color:red">' . number_format($saldo_kunde, 2, ',', '.') . '</b>';
                } else {
                    $saldo_kunde = '-';
                }
                $htmltable->AddCol($saldo_kunde);
                $htmltable->AddCol($warenkorb['zahlungsweise']);
                $htmltable->AddCol($warenkorb['affiliate_ref']);
                $gesamtsumme = (isset($gesamtsumme) ? $gesamtsumme : 0) + $warenkorb['gesamtsumme'];                
*/
            }
        } else {
            if ($deletedRows > 0) {
                $msg = $this->app->erp->base64_url_encode('<div class="info">Es wurden ' . $deletedRows . ' bereits importierte Datens&auml;tze entfernt.</div>');
            } else {
                $msg = $this->app->erp->base64_url_encode('<div class="error2">Aktuell sind keine Auftr&auml;ge in den Online-Shops vorhanden!</div>  ');
            }
            $this->app->Location->execute('Location: index.php?module=shopimport&action=list&msg=' . $msg);
        }


        $htmltable->NewRow();
        $htmltable->AddCol('<input type="radio" value="import" id="import" name="auswahl" checked>');
        $htmltable->AddCol('<input type="radio" value="muell" id="muell" name="auswahl">');
        $htmltable->AddCol('<input type="radio" value="spaeter" id="spaeter" name="auswahl">');
        $htmltable->AddCol('');
        $htmltable->AddCol('');
        $htmltable->AddCol('');
        $htmltable->AddCol('');
        $htmltable->AddCol('');
        $htmltable->AddCol('');
        $htmltable->AddCol('');
        $htmltable->AddCol('');
        $htmltable->AddCol('');
        $htmltable->AddCol('');
/*        $htmltable->AddCol(number_format($gesamtsumme, 2, ',', '.'));
        $htmltable->AddCol('');
        $htmltable->AddCol('');*/

        if ($enthaeltdoppeltenummern) {
            $this->app->Tpl->Add('INHALT', '<div class="error">Es wurde ein Auftrag aus einem Shop geholt, der bereits importiert wurde!</div>');
        }
        $this->app->Tpl->Add('INHALT', $htmltable->Get());

        $this->app->Tpl->Add('INHALT', "<br><br>Bedeutung: <ul>
          <li style=\"color:red\">Kundennummer von Kunde angegeben, eventuell ist diese falsch!</li>
          <li style=\"color:red\">Doppelte Internetbestellnummer!</li>
          <li style=\"color:purple\">Kundennummer bitte manuell pr&uuml;fen!</li>
          <li style=\"color:green\">Kundennummer aufgrund Felder Ort, Strasse, Name, Abteilung und E-Mail eindeutig gefunden!</li>
          </ul>");

        $this->app->Tpl->Parse('IMPORT', 'rahmen70.tpl');

        $this->app->Tpl->Parse('PAGE', 'shopimport_import.tpl');
    }

    public function ShopimportNavigation() {
        $id = $this->app->Secure->GetGET('id');
        $tmp = new Navigation($this->app, $id);
        $this->app->Tpl->Set('ID', $id);
        $this->app->Tpl->Set('PAGE', $tmp->Get());
        $this->app->BuildNavigation = false;
    }

    /*
    * Create Order import from file or files
    * Expects shop interface to produce the same output as ImportGetAuftrag()
    * return $fetchedOrders[] = [
                'id' => $cart['auftrag'],
                'sessionid' => '',
                'logdatei' => '',
                'warenkorb' => base64_encode(serialize($cart)),
                'warenkorbjson' => base64_encode(json_encode($cart)),
            ];
    */

    function ShopimportFileUpload() {

        $shopId = $this->app->Secure->GetGET('id');
        $submit = $this->app->Secure->GetPOST('submit');

        $projekt = $this->app->DB->Select("SELECT projekt FROM shopexport WHERE id = '$shopId' LIMIT 1");

        if ($submit == 'addfile') {
            if(isset($_FILES['upload']) && is_array($_FILES['upload']))
            {

                $success_count = 0;
                $errors = array();

                foreach($_FILES['upload']['tmp_name'] as $key => $file)
                {
                    if($file != "")
                    {
                        $filename = $_FILES['upload']['name'][$key];
                        $contents = file_get_contents($_FILES['upload']['tmp_name'][$key]);

                        $result = $this->app->remote->RemoteCommand($shopId, 'fileupload', ['filename' => $filename, 'contents' => $contents]);

                        if (is_array($result)) {
                            if (isset($result['errors'])) {
                                foreach ($result['errors'] as $error) {
                                    $errors[] = 'Auftragsimport fehlgeschlagen: '.$error;
                                }
                            } else {
                                foreach ($result as $cart) {
                                    $this->create_shopimport_cart_entry($cart, $projekt, $shopId);                                    
                                }
                                $success_count++;
                            }
                        } else {
                            $errors[] = 'Dateiimport fehlgeschlagen: '.$filename." ".print_r($result, true);
                        }
                    }                      
                } // foreach

                if ($success_count) {
                    $this->app->Tpl->AddMessage('info','Dateiimport ausgeführt ('.$success_count.' Warenk&ouml;rbe).<a href="index.php?module=shopimport&action=view&id='.$shopId.'"><button type="submit" class="ui-button-icon">Shopimport Zwischentabelle</button></a>',html: true);
                }
                if (!empty($errors)) {
                    foreach($errors as $error) {
                        $this->app->Tpl->AddMessage('error',$error);
                    }
                }

            }
        } else {
            $this->app->Tpl->AddMessage('info','Auftragsimport über Datei, falls vom Shop unterstützt');
        }

        $this->app->Tpl->Add('TAB1','<form action="" enctype="multipart/form-data" method="POST"><input type="file" name="upload[]" id="file" multiple/><button name="submit" value="addfile" id="addfile" class="ui-button-icon">Hinzufügen</button></form>');
        $this->app->Tpl->Parse('PAGE', 'tabview.tpl');
    }

    /*
    * Processes a single cart dataset and creates a shopimport_auftraege to be imported later into erpapi
    */

    function create_shopimport_cart_entry(array $cart, int $projectId, int $shopId) {
        $shopExtId = $cart['id'];
        $sessionid = $cart['sessionid'];
        $jsonEncoded = 0;
        if (empty(!$cart['warenkorbjson'])) {
            $jsonEncoded = 1;
            $warenkorb = $cart['warenkorbjson'];
        } else {
            $warenkorb = $cart['warenkorb'];
        }
        $logdatei = $cart['logdatei'];
        $username = !empty($this->app->User) ? $this->app->User->GetName() : 'Cronjob';
        $this->app->DB->Insert(
                sprintf(
                        "INSERT INTO shopimport_auftraege (extid,sessionid,warenkorb,imported,projekt,bearbeiter,logdatei, jsonencoded, shopid)
      VALUES('%s','%s','%s',0,%d, '%s','%s', %d, %d)",
                        $this->app->DB->real_escape_string($shopExtId),
                        $this->app->DB->real_escape_string($sessionid),
                        $this->app->DB->real_escape_string($warenkorb),
                        (int) $projectId,
                        $this->app->DB->real_escape_string($username),
                        $this->app->DB->real_escape_string($logdatei),
                        $jsonEncoded,
                        (int) $shopId
                )
        );
        $insid = $this->app->DB->GetInsertID();
        return ['status' => 1, 'id' => $insid, 'info' => 'Auftrag wurde f&uuml;r Import erfasst.'];
    }
}

