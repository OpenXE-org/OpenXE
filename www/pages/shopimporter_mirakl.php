<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 * SPDX-FileCopyrightText: 2024 OpenXE project
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
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

    private $category_identifier;

    private $offer_field_map;
    private $product_field_map;

    public function __construct($app, $intern = false) {
        $this->app = $app;
        $this->intern = $intern;
        if ($intern)
            return;
    }

    /*
     * See widget.shopexport.php, ShowExtraeinstellungen()
     */

    public function EinstellungenStruktur() {
        $einstellungen = [
            'ausblenden' => ['abholmodus' => ['ab_nummer', 'zeitbereich']],
            'functions' => ['getarticlelist'],
            'felder' => [
                'protokoll' => [
                    'typ' => 'checkbox',
                    'bezeichnung' => '{|Protokollierung im Logfile|}:'
                ],
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
                'category_identifier_source' => [
                    'typ' => 'select',
                    'bezeichnung' => '{|Katalogkategorie-Typ|}:',
                    'size' => 40,
                    'info' => 'Woher soll die Katalogkategorie des jeweiligen Artikels bezogen werden?',
                    'default' => 'feld',
                    'optionen' => ['feld' => '{|Feld|}', 'freifeld' => '{|Freifeld|}', 'eigenschaft' => '{|Eigenschaft|}', 'wert' => '{|Fester Wert|}']
                ],
                'category_identifier_source_value' => [
                    'typ' => 'text',
                    'bezeichnung' => '{|Katalogkategorie-Wert|}:',
                    'size' => 40,
                    'info' => '',
                    'default' => 'kategoriename'
                ],
                'product_field_map' => [
                    'typ' => 'textarea',
                    'bezeichnung' => '{|Zuordnung Produkt-Felder je Kategorie (JSON)|}:',
                    'cols' => 80,
                    'rows' => 20,
                    'info' => 'Die Felder werden vom Mirakl-Betreiber vorgegeben. Ist keine Kategorie definiert, gilt der Eintrag für alle Artikel. Jedes Feld kann wie folgt zugeordnet werden:<br>Artikelfeld: &quot;Mirakel-Feldname&quot;: {&quot;feld&quot;: &quot;xyz&quot;} oder kurz &quot;Mirakel-Feldname&quot;: &quot;xyz&quot;,<br>Freifeld: &quot;Mirakel-Feldname&quot;: {&quot;freifeld&quot;: &quot;Bezeichnung in Shop&quot;} (Siehe Reiter &quot;Freifelder&quot;),<br>Eigenschaft: &quot;Mirakel-Feldname&quot;: {&quot;eigenschaft&quot;: &quot;Eigenschaftenname xyz&quot;},<br>Fester Wert: &quot;Mirakel-Feldname&quot;: {&quot;wert&quot;: &quot;xyz&quot;}<br><br>Zusatzfelder zusätzlich mit der Eigenschaft &quot;zusatzfeld&quot;: true versehen: z.B. &quot;Mirakel-Feldname&quot;: {&quot;feld&quot;: &quot;name_de&quot;, &quot;zusatzfeld&quot;: true}',
                    'placeholder' => '[
    {
        &quot;kategorien&quot;: [
            &quot;Schuhe&quot;, &quot;Hosen&quot;
        ],
        &quot;felder&quot;: {
          &quot;category&quot;: {&quot;freifeld&quot;: &quot;Kategorie&quot;},
          &quot;Product.SellerProductID&quot;: {&quot;feld&quot;: &quot;nummer&quot;},
          &quot;SHOP.PRODUCT.TITLE&quot;: {&quot;feld&quot;: &quot;nummer&quot;},
          &quot;ATT.GLOBAL.Brandname&quot;: {&quot;feld&quot;: &quot;preis&quot;},
          &quot;Product.BaseUnit&quot;: {&quot;freifeld&quot;: &quot;Kategorie&quot;},
          &quot;ATT.GLOBAL.NoCUperOU&quot;: {&quot;eigenschaft&quot;: &quot;Mirakl Steuertext&quot;},
          &quot;ATT.GLOBAL.NoCUperOU__UNIT&quot;: {&quot;wert&quot;: &quot;false&quot;,&quot;zusatzfeld&quot;: true},
          &quot;Product.TaxIndicator&quot;: {&quot;wert&quot;: &quot;1&quot;,&quot;zusatzfeld&quot;: true}
        }
    }
]'
                ],

                'offer_field_map' => [
                    'typ' => 'textarea',
                    'bezeichnung' => '{|Zuordnung Angebots-Felder je Kategorie (JSON)|}:',
                    'cols' => 80,
                    'rows' => 20,
                    'info' => 'Die Felder werden vom Mirakl-Betreiber vorgegeben. Ist keine Kategorie definiert, gilt der Eintrag für alle Artikel. Jedes Feld kann wie folgt zugeordnet werden:<br>Artikelfeld: &quot;Mirakel-Feldname&quot;: {&quot;feld&quot;: &quot;xyz&quot;} oder kurz &quot;Mirakel-Feldname&quot;: &quot;xyz&quot;,<br>Freifeld: &quot;Mirakel-Feldname&quot;: {&quot;freifeld&quot;: &quot;Bezeichnung in Shop&quot;} (Siehe Reiter &quot;Freifelder&quot;),<br>Eigenschaft: &quot;Mirakel-Feldname&quot;: {&quot;eigenschaft&quot;: &quot;Eigenschaftenname xyz&quot;},<br>Fester Wert: &quot;Mirakel-Feldname&quot;: {&quot;wert&quot;: &quot;xyz&quot;}<br><br>Zusatzfelder zusätzlich mit der Eigenschaft &quot;zusatzfeld&quot;: true versehen: z.B. &quot;Mirakel-Feldname&quot;: {&quot;feld&quot;: &quot;name_de&quot;, &quot;zusatzfeld&quot;: true}',
                    'placeholder' => '[
    {
        &quot;kategorien&quot;: [
            &quot;Schuhe&quot;, &quot;Hosen&quot;
        ],
        &quot;felder&quot;: {
          &quot;product_id_type&quot;: {&quot;wert&quot;: &quot;SHOP_SKU&quot;},
          &quot;product_id&quot;: {&quot;feld&quot;: &quot;nummer&quot;},
          &quot;shop_sku&quot;: {&quot;feld&quot;: &quot;nummer&quot;},
          &quot;price&quot;: {&quot;feld&quot;: &quot;preis&quot;},
          &quot;description&quot;: {&quot;freifeld&quot;: &quot;Kategorie&quot;},
          &quot;internal_description&quot;: {&quot;eigenschaft&quot;: &quot;Mirakl Steuertext&quot;},
          &quot;reversecharge&quot;: {&quot;wert&quot;: &quot;false&quot;,&quot;zusatzfeld&quot;: true},
          &quot;warehouse&quot;: {&quot;wert&quot;: &quot;1&quot;,&quot;zusatzfeld&quot;: true},
          &quot;quantity&quot;: {&quot;feld&quot;: &quot;anzahl_lager&quot;}
        }
    }
]'
                ],
                 'Artikelfelder' => [
                    'heading' => 'Zusatzinformationen',
                    'typ' => 'info',
                    'text' => 'Folgende Artikelfelder stehen zur Verf&uuml;gung:',
                    'bezeichnung' => null,
                    'info' => 'artikel, artikelid, nummer, inaktiv, name_de, name_en, einheit, hersteller, herstellernummer, ean, artikelnummer_fremdnummern, kurztext_de, kurztext_en, anabregs_text, anabregs_text_en, beschreibung_de, beschreibung_en, uebersicht_de, uebersicht_en, herkunftsland, texteuebertragen, metadescription_de, metadescription_en, metakeywords_de, metakeywords_en, metatitle_de, metatitle_en, links_de, altersfreigabe, links_en, startseite_de, startseite_en, restmenge, startseite, standardbild, herstellerlink, lieferzeit, lieferzeitmanuell, gewicht, laenge, breite, hoehe, wichtig, porto, gesperrt, sperrgrund, gueltigbis, umsatzsteuer, ausverkauft, variante, variante_von_id, variantevon, pseudopreis, keinrabatterlaubt, einkaufspreis, pseudolager, downloadartikel, zolltarifnummer, freifeld_Kategorie, typ, kategoriename, steuer_art_produkt, steuer_art_produkt_download, anzahl_bilder, anzahl_lager, lagerkorrekturwert, autolagerlampe, waehrung, preis, steuersatz, bruttopreis, checksum, variantevorhanden'
                ]
            ]
        ];
    
        return($einstellungen);
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
        
        $this->category_identifier = array($einstellungen['felder']['category_identifier_source'] => $einstellungen['felder']['category_identifier_source_value']);

        $this->offer_field_map = json_decode($einstellungen['felder']['offer_field_map'], true, flags: JSON_THROW_ON_ERROR);
        $this->product_field_map = json_decode($einstellungen['felder']['product_field_map'], true, flags: JSON_THROW_ON_ERROR);                
    }

    private function miraklRequest(string $endpoint, $postdata = null, array $getdata = null, string $content_type = null, bool $raw = false, $debug = false, $debugurl = null) {
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

        if ($debugurl) {
            $url = $debugurl;
        } else {
            $url = $this->shopUrl;
        }

        curl_setopt($ch, CURLOPT_URL, $url . $endpoint . $url_addition);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $response = curl_exec($ch);
        if (curl_error($ch)) {
            $this->error[] = curl_error($ch);
        }
        curl_close($ch);

        $information = curl_getinfo($ch);

        if ($debug) {
            print_r($information);
            print_r($postdata);
            print_r($response);
            exit();
        }

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
                    if (isset($article[$value])) {
                        return($article[$value]);
                    } else {
                       throw new Exception("Artikelfeld existiert nicht: \"".$value."\"");               
                    }
                break;
                case 'freifeld': 
                    if (isset($article['freifelder']['DE'][$value])) {
                        return($article['freifelder']['DE'][$value]);
                    } else {
                       throw new Exception("Freifeld existiert nicht: \"".$value."\"");               
                    }
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
        
        $this->Log('ImportSendList start', print_r($articleList,true));
               
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
            $category_found = false;
            $additional_fields = array();
             
            $offer_for_mirakl = array(
                'state_code' => '11', // ?!?!
                'update_delete' => null // Update delete flag. Could be empty (means "update"), "update" or "delete".
            );

            $kategorie = $this->GetFieldValue($article, $this->category_identifier);               
                      
            foreach ($this->offer_field_map as $offer_field_entry) {
                if ($offer_field_entry['kategorien'] != null) {
                    if (!in_array($kategorie,$offer_field_entry['kategorien'])) {
                        continue;
                    }
                }    

                $category_found = true;
    
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
            }

            if (!$category_found) {
                return(array('status' => false, 'message' => "Angebotskonfiguration für Artikel ".$article['nummer'].", Kategorie \"".$kategorie."\" nicht gefunden"));
            }                               

            if (!empty($additional_fields)) {
                $offer_for_mirakl['offer_additional_fields'] = $additional_fields;
            }
             
            $offers_for_mirakl[] = $offer_for_mirakl;
        }

        $data_for_mirakl = array();
        $data_for_mirakl['offers'] = $offers_for_mirakl;

        $json_for_mirakl = json_encode($data_for_mirakl);

        $this->Log('posting offer data', $json_for_mirakl);

        $result = [];
        $response = $this->miraklRequest('offers', postdata: $json_for_mirakl, content_type: 'application/json', raw: true);

        $result = json_decode($response);

        if (!isset($result->import_id)) {
            $this->Log('posting offer data error', print_r($response,true));
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
            $this->Log('importing of offer data failed in mirakl', print_r($response,true));
            return(array('status' => false, 'message' => "Angebotsimport in Mirakl fehlgeschlagen: ".print_r($response,true)));
        }
               
        if ($result->lines_in_error == 0) {
            $this->Log('importing of offer data ok', print_r($response,true));
            return($result->lines_in_success);
        }
        
        $this->Log('importing of offer returned with '.$result->lines_in_error.' lines', print_r($response,true));
   
        $result = array();                        
        // Check errors with CSV unfucking...
        $response = $this->miraklRequest('offers/imports/'.$import_id.'/error_report', raw: true);                                     
        $response_lines = preg_split('/\R/', $response, flags: PREG_SPLIT_NO_EMPTY);              

        $error_message_key = null;
        $firstline = true;

        foreach ($response_lines as $response_line) {

            $response_array = str_getcsv($response_line,';','"');            

            if ($firstline) {    
                $error_message_key = array_search("error-message",$response_array);
                $firstline = false;
                continue;
            }    
                                                   
            switch ($response_array[$error_message_key]) {
                case 'The product does not exist':
                
                    // Try to create the product                   
                    $product_for_mirakl = array();
                    
                    print_r($this->product_field_map);
                    
                    foreach ($this->product_field_map as $product_field_entry) {                    
                        foreach ($product_field_entry['felder'] as $product_field => $product_field_source) {
                            if (!is_array($product_field_source)) {
                                $product_field_source = array('feld' => $product_field_source);
                            }
                            $product_field_value = null;           
                            $product_field_value = $this->GetFieldValue($article, $product_field_source);                
                            $product_for_mirakl[$product_field] = $product_field_value; 
                        }                                          
                    }                                           
                    
                    $this->Log('creating product', print_r($product_for_mirakl,true));
                    
                    // Create CSV from array                                        
                    $csv1 .= '"'.implode('";"',array_keys($product_for_mirakl)).'"';
                    $csv2 .= '"'.implode('";"',$product_for_mirakl).'"';                    
                    $csv = $csv1."\r\n".$csv2;
                                            
                    $result = [];
                    
                    $this->Log('creating product csv', print_r($csv,true));
                                       
                    $postdata = array('file' => new CURLStringFile(postname: 'import.csv', data: $csv));
                                        
                    $response = $this->miraklRequest('products/imports', postdata: $postdata, content_type: 'multipart/form-data', raw: true);

                    $result = json_decode($response);

                    $this->Log('posting product data posted', print_r($result,true));

                    if (!isset($result->import_id)) {
                        $this->Log('posting product data error', print_r($response,true));
                        return(array('status' => false, 'message' => "Produktimport in Mirakl abgelehnt: ".print_r($response,true)));
                    } 

                    $import_id = $result->import_id;

                    // Wait for import to finish

                    $status = null;

                    /*
                    WAITING_SYNCHRONIZATION_PRODUCT, WAITING, RUNNING, COMPLETE, FAILED
                    */

                    while ($status != 'COMPLETE' && $status != 'FAILED') {
                        sleep(5);
                        $response = $this->miraklRequest('products/imports/'.$import_id, raw: true);
                        $result = json_decode($response);
                        $status = $result->import_status;
                    }

                    if ($status == 'FAILED') {
                        $this->Log('importing of product data failed in mirakl', print_r($response,true));
                        return(array('status' => false, 'message' => "Produktimport in Mirakl fehlgeschlagen: ".print_r($response,true)));
                    }
                    
                    if ($result->transform_lines_in_error == 0) {
                        $this->Log('importing of product data ok', print_r($response,true));
                        return($result->lines_in_success);
                    }
                    
                    $this->Log('importing of product returned with '.$result->transform_lines_in_error.' error lines', print_r($response,true));

                    $response = $this->miraklRequest('products/imports/'.$import_id.'/transformation_error_report', raw: true);

                    $this->Log('product import error report', print_r($response,true));

                
                break;
                default:
                    $result[] = array('Unhandled error' => $response_array);
                break;
            }
        }
       
        return(array('status' => false, 'message' => "Angebotsimport in Mirakl hat Fehler: ".print_r($result,true)));

    }

    private function getOrdersToProcess(int $limit) {
        
    }

    private function Log($message, $dump = '') {
        if ($this->protocol) {
            $this->app->erp->Logfile('Mirakl (Shop '.$this->shopid.') '.$message, print_r($dump, true));
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

