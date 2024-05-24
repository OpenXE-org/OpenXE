<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 * SPDX-FileCopyrightText: 2024 OpenXE project
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */


/*

JSON example for field_map

{
    "katalogkonfiguration": {
        "kategorie": {"feld": "freifeld_Kategorie"}
    },
    "angebotskonfiguration": 
    [
        {
            "felder": {
              "product_id_type": {"wert": "SHOP_SKU"},
              "product_id": {"feld": "nummer"},
              "shop_sku": {"feld": "nummer"},
              "price": {"feld": "preis"},
              "description": "freifeld_Kategorie",
              "internal_description": {"eigenschaft": "Mirakl Steuertext"},
              "reversecharge": {"wert": "false","zusatzfeld": true},
              "warehouse": {"wert": "1","zusatzfeld": true},
              "quantity": {"feld": "anzahl_lager"}
            }
        }
    ]
}
*/

class Shopimporter_Mirakl extends ShopimporterBase {

    private $app;
    private $intern;
    private $shopid;
    private $protocol;
    private $apiKey;
    private $shopUrl;
    private $mirakl_shopid;
    private $createManufacturerAllowed = false;
    private $idsabholen;
    private $idbearbeitung;
    private $idabgeschlossen;
    public $data;
    // TODO
    private $langidToIso = [3 => 'de', 1 => 'en'];
    private $taxationByDestinationCountry;
    private $orderSearchLimit;

    private $category_identifier_source;
    private $category_identifier_source_field;
    private $product_identifier_type;
    private $product_identifier_source;
    private $product_identifier_source_field;
    private $product_field_map;
    private $field_map;

    public function __construct($app, $intern = false) {
        $this->app = $app;
        $this->intern = $intern;
        if ($intern)
            return;
    }

    /*
     * See widget.shopexport.php
     */

    public function EinstellungenStruktur() {
        return [
            'ausblenden' => ['abholmodus' => ['ab_nummer', 'zeitbereich']],
            'functions' => ['getarticlelist'],
            'felder' => [
                'protokoll' => [
                    'typ' => 'checkbox',
                    'bezeichnung' => '{|Protokollierung im Logfile|}:'
                ],
                /*            'textekuerzen' => [
                  'typ' => 'checkbox',
                  'bezeichnung' => '{|Texte bei Artikelexport auf Maximallänge kürzen|}:'
                  ],
                  'useKeyAsParameter' => [
                  'typ' => 'checkbox',
                  'bezeichnung' => '{|Shop Version ist mindestens 1.6.1.1|}:'
                  ], */
                'apikey' => [
                    'typ' => 'text',
                    'bezeichnung' => '{|API Key|}:',
                    'size' => 40,
                ],
                'shopurl' => [
                    'typ' => 'text',
                    'bezeichnung' => '{|Shop URL|}:',
                    'size' => 40,
                ],
                'mirakl_shopid' => [
                    'typ' => 'text',
                    'bezeichnung' => '{|Shop ID des Shops|}:',
                    'size' => 40,
                    'info' => 'optional, int64'
                ],
/*                'category_identifier_source' => [
                    'typ' => 'select',
                    'bezeichnung' => '{|Kategorie-Identifizierer|}:',
                    'size' => 40,
                    'optionen' => ['Kategorie' => '{|Kategorie|}', 'Freifeld' => '{|Freifeld|}', 'Eigenschaft' => '{|Eigenschaft|}'],
                    'info' => 'Feld in OpenXE für die Zuordnung der Artikel zu den Katalogkategorien in Mirakl'
                ],
                'category_identifier_source_field' => [
                    'typ' => 'text',
                    'bezeichnung' => '{|Kategorie-Identifizierer Freifeld oder Eigenschaft|}:',
                    'size' => 40,
                    'info' => 'Wenn oben Freifeld oder Eigenschaft gew&auml;hlt wurde'
                ],
                'product_field_map' => [
                    'typ' => 'textarea',
                    'bezeichnung' => '{|Zuordnung Produkt-Felder je Kategorie (JSON)|}:',
                    'info' => 'Die Felder werden vom Mirakl-Betreiber vorgegeben. Mögliche Zuordnungen aus OpenXE sind: Artikelnummer, Artikelname, Einheit, Hersteller, Herstellernummer, EAN oder eine konkrete Artikeleigenschaft'
                ],*/
                'field_map' => [
                    'typ' => 'textarea',
                    'bezeichnung' => '{|Zuordnung Angebots-Felder je Kategorie (JSON)|}:',
                    'info' => 'Die Felder werden vom Mirakl-Betreiber vorgegeben. Zuordnung &uuml;ber "Mirakl-xyz": {"feld": "xyz"} oder kurz "Mirakl-xyz": "xyz" Mögliche Zuordnungen aus OpenXE sind: nummer, name_de, einheit, hersteller, herstellernummer, ean u.v.m. Freifelder wie im Reiter Freifelder mit Pr&auml;fix \'freifeld_\',  Eigenschaften: {"eigenschaft": "Eigenschaftenname xyz"}, Fester Wert: {"wert": "xyz"}, Zusatzfelder zusätzlich mit der Eigenschaft "zusatzfeld": true versehen: z.B. {"feld": "name_de", "zusatzfeld": true}'
                ],
            /*
              'steuergruppen' => [
              'typ' => 'text',
              'bezeichnung' => '{|Steuergruppenmapping|}:',
              'size' => 40,
              ],
              'zustand' => [
              'typ' => 'text',
              'bezeichnung' => '{|Freifeld Zustand|}:',
              'size' => 40,
              ],
              'abholen' => [
              'typ' => 'text',
              'bezeichnung' => '{|\'Abholen\' Status IDs|}:',
              'size' => 40,
              ],
              'bearbeitung' => [
              'typ' => 'text',
              'bezeichnung' => '{|\'In Bearbeitung\' Status IDs|}:',
              'size' => 40,
              ],
              'abgeschlossen' => [
              'typ' => 'text',
              'bezeichnung' => '{|\'Abgeschlossen\' Status IDs|}:',
              'size' => 40,
              ],
              'autoerstellehersteller' => [
              'typ' => 'checkbox',
              'bezeichnung' => '{|Fehlende Hersteller automatisch anlegen|}:',
              'col' => 2
              ],
              'zeigezustand' => [
              'typ' => 'checkbox',
              'bezeichnung' => '{|Artikelzustand im Shop anzeigen|}:',
              'col' => 2
              ],
              'zeigepreis' => [
              'typ' => 'checkbox',
              'bezeichnung' => '{|Artikelpreis im Shop anzeigen|}:',
              'col' => 2
              ], */
            ]
        ];
    }

    public function getKonfig($shopid, $data) {
        $this->shopid = $shopid;
        $this->data = $data;
        $importerSettings = $this->app->DB->SelectArr("SELECT `einstellungen_json` FROM `shopexport` WHERE `id` = '$shopid' LIMIT 1");
        $importerSettings = reset($importerSettings);
        $einstellungen = [];
        if (!empty($importerSettings['einstellungen_json'])) {
            $einstellungen = json_decode($importerSettings['einstellungen_json'], true);
        }
        $this->protocol = $einstellungen['felder']['protokoll'];
        $this->apiKey = $einstellungen['felder']['apikey'];
        $this->shopUrl = rtrim($einstellungen['felder']['shopurl'], '/') . '/';
        $this->mirakl_shopid = $einstellungen['felder']['mirakl_shopid'];

        if ($einstellungen['felder']['autoerstellehersteller'] === '1') {
            $this->createManufacturerAllowed = true;
        }
        $this->idsabholen = $einstellungen['felder']['abholen'];
        $this->idbearbeitung = $einstellungen['felder']['bearbeitung'];
        $this->idabgeschlossen = $einstellungen['felder']['abgeschlossen'];
        $query = sprintf('SELECT `steuerfreilieferlandexport` FROM `shopexport`  WHERE `id` = %d', $this->shopid);
        $this->taxationByDestinationCountry = !empty($this->app->DB->Select($query));
        
        $this->category_identifier_source = $einstellungen['felder']['category_identifier_source'];
        $this->category_identifier_source_field = $einstellungen['felder']['category_identifier_source_field'];
        $this->product_identifier_type = $einstellungen['felder']['product_identifier_type'];
        $this->product_identifier_source = $einstellungen['felder']['product_identifier_source'];
        $this->product_identifier_source_field = $einstellungen['felder']['product_identifier_source_field'];
        $this->product_field_map = json_decode($einstellungen['felder']['product_field_map'], true);
        $this->field_map = json_decode($einstellungen['felder']['field_map'], true);        
    }

    private function miraklRequest(string $endpoint, $postdata = null, array $getdata = null, string $content_type = null, bool $raw = false) {
        $ch = curl_init();
        $url_addition = "";

        $headers = array("Authorization: " . $this->apiKey);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (!empty($this->mirakl_shopid)) {
            $getdata['shop_id'] = $this->mirakl_shopid;
        }

        if (!empty($getdata)) {
            $url_addition = "?";
            $ampersand = "";
            foreach ($getdata as $key => $value) {
                $url_addition .= $ampersand . $key . "=" . $value;
                $ampersand = "&";
            }
        } 
        if (!empty($postdata)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            $headers[] = 'Content-Type: ' . $content_type;
        }

        curl_setopt($ch, CURLOPT_URL, $this->shopUrl . $endpoint . $url_addition);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $response = curl_exec($ch);
        if (curl_error($ch)) {
            $this->error[] = curl_error($ch);
        }
        curl_close($ch);

        $information = curl_getinfo($ch);

/*        print_r($information);
        print_r($postdata);
        print_r($response);
        exit();
*/

        if ($raw)
            return $response;

        return simplexml_load_string($response);
    }

    public function ImportAuth() {
        $ch = curl_init($this->shopUrl . "version");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: " . $this->apiKey));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if ($code == 200) {
            return 'success ' . print_r($response, true);
        }
        return $response;
    }

    /*
     *   Fetches article list from the shop, puts them into table shopexport_getarticles, starts the prozessstarter getarticles which fetches details for each article via ImportGetArticle()
     */

    public function ImportGetArticleList() {
        $result = [];

        $response = $this->miraklRequest('offers', raw: true);

        $result_array = json_decode($response);

        foreach ($result_array->offers as $offer) {
            $result[] = $offer->shop_sku;
        }

        array_unique($result);
        return $result;
    }

    /*
     *   Fetches article details from the shop
     */

    public function ImportGetArticle() {

        $articleList = $this->CatchRemoteCommand('data');

        $parameters = array('product_references' => 'productID', 'product_');

        $response = $this->miraklRequest('products?', raw: true);

        throw new Exception("Not implemented");
    }

    /*
    *   Gets a flexible mapped fieldvalue from feld, wert or eigenschaft
    */
    public function GetFieldValue($article, $field_map_entry) {    
        foreach ($field_map_entry as $key => $value) {                   
            switch ($key) {
                case 'feld': 
                    return($article[$value]);
                break;
                case 'eigenschaft':
                    $sql = "SELECT wert FROM artikeleigenschaften ae INNER JOIN artikeleigenschaftenwerte aew ON aew.artikeleigenschaften = ae.id WHERE aew.artikel = '".$article['artikelid']."' AND ae.name = '".$value."' LIMIT 1";
                    return($this->app->DB->Select($sql));
                break;
                case 'wert':
                    return($value);
                break;                
            }                
        }
        return(null);
    }

    public function ImportSendListLager() {
        return($this->ImportSendList());
    }

    /*
     *  Send articles to shop
     */
    public function ImportSendList() {
            
        $articleList = $this->CatchRemoteCommand('data');
               
        // First gather all articles as offers and send them
        // Wait for import to finish
        // Evaluate import

        // Unimplemented (needed?)
        // Select offers with no product
        // Create products and send
        // Wait for import to finish
        // Evaluate import

        foreach ($articleList as $article) {

            /*
             * Export offer
             */
            $processed = false;
            $additional_fields = array();
             
            $offer_for_mirakl = array(
                'state_code' => '11', // ?!?!
                'update_delete' => null // Update delete flag. Could be empty (means "update"), "update" or "delete".
            );
                      
            foreach ($this->field_map['angebotskonfiguration'] as $offer_field_entry) {

                $kategorie = $this->GetFieldValue($article, $this->field_map['katalogkonfiguration']['kategorie']);               
                if ($offer_field_entry['kategorien'] != null) {
                    if (!in_array($kategorie,$offer_field_entry['kategorien'])) {
                        continue;
                    }
                }
                                
                // Check Required attributes
                $required = [
                    'product_id_type',
                    'product_id',
                    'shop_sku',
                    'price'
                ];
                $missing = null;
                foreach ($required as $key) {
                    if (!isset($offer_field_entry['felder'][$key])) {
                        $missing[] = $key;
                    }
                }       
                if ($missing) {
                    return(array('status' => false, 'message' => "Pflichtfelder fehlen in Angebotskonfiguration von Kategorie \"".$offer_field_entry['kategorie']."\": ".implode(', ',$missing)));
                }
                // Check Required attributes
                
                foreach ($offer_field_entry['felder'] as $offer_field => $offer_field_source) {
                    if (!is_array($offer_field_source)) {
                        $offer_field_source = array('feld' => $offer_field_source);
                    }
                    $offer_field_value = null;           
                    $is_additional_field = false;
                    $offer_field_value = $this->GetFieldValue($article, $offer_field_source);                
                    if (in_array('zusatzfeld', $offer_field_source)) {
                        $is_additional_field = true;
                    }
                    if ($is_additional_field) {
                        $additional_field = array (
                            "code" => $offer_field,
                            "value" => $offer_field_value
                        );
                        $additional_fields[] = $additional_field;
                    } else {
                        $offer_for_mirakl[$offer_field] = $offer_field_value; 
                    }                             
                }                                
                $processed = true;
            }

            if (!$processed) {
                return(array('status' => false, 'message' => "Angebotskonfiguration für Kategorie \"".$kategorie."\" nicht gefunden"));
            }

            if (!empty($additional_fields)) {
                $offer_for_mirakl['offer_additional_fields'] = $additional_fields;
            }
             
            $offers_for_mirakl[] = $offer_for_mirakl;
        }

        $data_for_mirakl = array();
        $data_for_mirakl['offers'] = $offers_for_mirakl;

        $json_for_mirakl = json_encode($data_for_mirakl);

        $result = [];
        $response = $this->miraklRequest('offers', postdata: $json_for_mirakl, content_type: 'application/json', raw: true);

        $result = json_decode($response);

        if (!isset($result->import_id)) {
            return(array('status' => false, 'message' => "Angebotsimport in Mirakl abgelehnt: ".print_r($response,true)));
        } 
        
        $import_id = $result->import_id;

        // Wait for import to finish
        
        $status = null;
        
        /*
        WAITING_SYNCHRONIZATION_PRODUCT, WAITING, RUNNING, COMPLETE, FAILED
        */
        
        while ($status != 'COMPLETE' && $status != 'FAILED') {
            sleep(5);
            $response = $this->miraklRequest('offers/imports/'.$import_id, raw: true);
            $result = json_decode($response);
            $status = $result->status;
        }
        
        if ($status == 'FAILED') {
            return(array('status' => false, 'message' => "Angebotsimport in Mirakl fehlgeschlagen: ".print_r($response,true)));
        }
        
        if ($result->lines_in_error == 0) {
            return($result->lines_in_success);
        }
        
        // Check errors 
        $response = $this->miraklRequest('offers/imports/'.$import_id.'/error_report', raw: true);               
        return(array('status' => false, 'message' => "Angebotsimport in Mirakl hat Fehler: ".print_r($response,true)));

    }

    private function getOrdersToProcess(int $limit) {
        
    }

    private function Log($message, $dump = '') {
        if ($this->protocol) {
            $this->app->erp->Logfile($message, print_r($dump, true));
        }
    }

    public function ImportDeleteAuftrag() {
        
    }

    public function ImportUpdateAuftrag() {
        
    }

    public function ImportGetAuftraegeAnzahl() {
        
    }

    public function ImportGetAuftrag() {
        
    }
}

