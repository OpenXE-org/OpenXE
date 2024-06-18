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
    private $idbearbeitung;
    private $idabgeschlossen;
    public $data;
    // TODO
    private $langidToIso = [3 => 'de', 1 => 'en'];
    private $taxationByDestinationCountry;
    private $orderSearchLimit;

    private $category_identifier;

    private $create_products;
    private $mirakl_error_text_product_missing;

    private $normalTaxId;
    private $reducedTaxId;

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
                'create_products' => [
                    'typ' => 'checkbox',
                    'bezeichnung' => '{|Produkte anlegen|}:',
                    'size' => 40,
                    'info' => 'Produkte automatisch anlegen wenn sie nicht existieren'
                ],
                'mirakl_error_text_product_missing' => [
                    'typ' => 'text',
                    'bezeichnung' => '{|Fehlertext Produkt fehlt|}:',
                    'size' => 40,
                    'info' => 'Der Fehlertext der anzeigt dass das Produkt nicht existiert (Angebotsimport, Fehlerbericht)',
                    'default' => 'The product does not exist'
                ],
                'normalTaxId' => [
                    'typ' => 'text',
                    'bezeichnung' => '{|TaxId für Steuersatz "normal"|}',
                    'size' => 40,
                ],
                'reducedTaxId' => [
                    'typ' => 'text',
                    'bezeichnung' => '{|TaxId für Steuersatz "ermäßigt"|}',
                    'size' => 40,
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
       
        $this->category_identifier = array($einstellungen['felder']['category_identifier_source'] => $einstellungen['felder']['category_identifier_source_value']);

        $this->create_products = $einstellungen['felder']['create_products'];
        $this->mirakl_error_text_product_missing = $einstellungen['felder']['mirakl_error_text_product_missing'];

        $this->normalTaxId = $einstellungen['felder']['normalTaxId'];
        $this->reducedTaxId = $einstellungen['felder']['reducedTaxId'];

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
            return 'success';
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
                       
        $offer_export_result = $this->mirakl_export_offers($articleList);
           
        if ($offer_export_result['returncode'] == 0) {
            return(array('status' => true, 'message' => "Angebotsimport in Mirakl ok"));
        }
       
        // Check for missing products and try to create
        if ($this->create_products) {                       
        
            $create_products_list = array();
        
            foreach ($offer_export_result['articleList'] as $key => $article) {                
                if ($article['mirakl_export_offers_result']['returncode'] == 12) {
                    switch ($article['mirakl_export_offers_result']['message']) {
                        case $this->mirakl_error_text_product_missing:
                            $create_products_list[] = $article;
                        break;
                    }
                }
            }
            
            if (empty($create_products_list)) {
                return(array('status' => false, 'message' => "Produktimport nicht möglich"));
            }
            
            $create_products_result = $this->mirakl_create_products($create_products_list);
            
            if ($create_products_result['returncode'] != 0) {
                $this->Log("Produktimport in Mirakl hat Fehler", print_r($create_products_result, true));            
                return(array('status' => false, 'message' => "Produktimport in Mirakl hat Fehler"));
            }
            
            // Retry offer import
            $offer_export_result = $this->mirakl_export_offers($articleList);
            if ($offer_export_result['returncode'] == 0) {
                return(array('status' => true, 'message' => "Angebots und Produktimport in Mirakl ok"));
            }                           
        }
       
        $this->Log("Angebotsimport in Mirakl hat Fehler", print_r($offer_export_result, true));
       
        return(array('status' => false, 'message' => "Angebotsimport in Mirakl hat Fehler"));

    }

    private function getOrdersToProcess(int $limit) {
        echo("getOrdersToProcess");        
        exit();
    }

    private function Log($message, $dump = '') {
        if ($this->protocol) {
            $this->app->erp->Logfile('Mirakl (Shop '.$this->shopid.') '.$message, print_r($dump, true));
        }
    }

    public function ImportDeleteAuftrag() {
        echo("ImportDeleteAuftrag");        
        exit();
    }

    public function ImportUpdateAuftrag() {
        echo("ImportUpdateAuftrag");        
        exit();
    }

    // STAGING, WAITING_ACCEPTANCE, WAITING_DEBIT, WAITING_DEBIT_PAYMENT, SHIPPING, SHIPPED, TO_COLLECT, RECEIVED, CLOSED, REFUSED, CANCELED
    public function ImportGetAuftraegeAnzahl() {    
        $response = $this->miraklRequest('orders', getdata: array('order_state_codes' => 'WAITING_ACCEPTANCE'),  raw: true);                                     
        $this->Log('ImportGetAuftraegeAnzahl', print_r($response,true));    
        $result_array = json_decode($response);
        return($result_array->total_count);    
    }

    public function ImportGetAuftrag() {

        $parameters = array('order_state_codes' => 'WAITING_ACCEPTANCE');

        if(!empty($this->data['nummer'])) {
            $parameters['order_ids'] = $this->data['nummer'];
        }

        $response = $this->miraklRequest('orders', getdata: $parameters,  raw: true);          
        $this->Log('ImportGetAuftraegeAnzahl', print_r($response,true));    
        $result_array = json_decode($response);

        $fetchedOrders = [];
        foreach ($result_array->orders as $order) {
            $cart = [];
            $cart['zeitstempel'] = strval($order->created_date);

            $cart['auftrag'] = strval($order->order_id);
            $cart['onlinebestellnummer'] = strval($order->commercial_id);
            $cart['gesamtsumme'] = strval($order->total_price);
            
            $cart['bestelldatum'] = strval($order->created_date);

            $cart['lieferung'] = strval($order->shipping_type_code);

            $cart['email'] = strval($order->customer_notification_email);
            
            $cart['kunde_sprache'] = '?';
            $cart['kundennummer'] = $order->customer->customer_id;
            
            $cart['name'] = ($order->customer->civility?$order->customer->civility." ":"").$order->customer->firstname." ".$order->customer->lastname;
            if (!empty(strval($order->customer->company))) {
                $cart['ansprechpartner'] = $cart['name'];
                $cart['name'] = strval($order->customer->company);
            }
            $cart['strasse'] = strval($order->customer->billing_address->street_1);
            $cart['adresszusatz'] = strval($order->customer->billing_address->street_2);
            $cart['telefon'] = strval($order->customer->billing_address->phone);
            $cart['plz'] = strval($order->customer->billing_address->zip_code);
            $cart['ort'] = strval($order->customer->billing_address->city);

            $cart['ustid'] = '?';
            $cart['land'] = strval($order->customer->billing_address->country_iso_code);

            $cart['abweichendelieferadresse'] = 1;
            $cart['lieferadresse_name'] = ($order->customer->shipping_address->civility?$order->customer->shipping_address->civility." ":"").$order->customer->shipping_address->firstname." ".$order->customer->shipping_address->lastname;
            if (!empty(strval($order->customer->shipping_address->company))) {
              $cart['lieferadresse_ansprechpartner'] = $cart['lieferadresse_name'];
              $cart['lieferadresse_name'] = strval($deliveryAddress->company);
            }
            $cart['lieferadresse_strasse'] = strval($order->customer->shipping_address->street_1);
            $cart['lieferadresse_adresszusatz'] = strval($order->customer->shipping_address->street_2);
            $cart['lieferadresse_telefon'] = strval($order->customer->shipping_address->phone);
            $cart['lieferadresse_plz'] = strval($order->customer->shipping_address->zip_code);
            $cart['lieferadresse_ort'] = strval($order->customer->shipping_address->city);

            $cart['zahlungsweise'] = strval($order->payment_type);

            $cart['ust_befreit'] = '?'; // 1, 2

            $cart['steuersatz_normal'] = '?';//strval($order->taxes[0]->amount); 
            $cart['steuersatz_ermaessigt'] = '?'; // strval($order->taxes[0]->amount);

            $cart['articlelist'] = [];

            $shipping_tax_amount = 0;

            foreach ($order->order_lines as $order_row) {

                $steuersatz = '?';

                switch ($order->row->taxes[0]->code) {
                    case $this->normalTaxId:
                        $steuersatz = 'normal';
                    break;
                    case $this->reducedTaxId:
                        $steuersatz = 'ermaessigt';
                    break;
                }

                $article = [
                    'articleid' => strval($order_row->offer_sku),
                    'name' => strval($order_row->product_title),
                    'quantity' => strval($order_row->quantity),
                    'price_netto' => strval($order_row->price_unit),
                    'steuersatz' => $steuersatz
                ];

                foreach ($order_row->shipping_taxes as $shipment_tax) {
                    $shipping_tax_amount += $shipment_tax->amount;
                }
                $cart['articlelist'][] = $article;
            } // foreach articles

            $cart['versandkostennetto'] = strval($order->shipping_price);
            $cart['versandkostenbrutto'] = $cart['versandkostennetto']+$shipping_tax_amount;

        } // foreach orders

        $fetchedOrders[] = [
            'id' => $cart['auftrag'],
            'sessionid' => $cart['onlinebestellnummer'],
            'logdatei' => '',
            'warenkorb' => base64_encode(serialize($cart)),
            'warenkorbjson' => base64_encode(json_encode($cart))
        ];
        
        $this->Log('Auftragsimport', $cart);
        return $fetchedOrders;
    }
    
    /*
    *   Send offer data to mirakl
    *   $articleList = $this->CatchRemoteCommand('data');
    *   Return 
    *       array (returncode, message, articleList) articleList with added mirakl_export_offers_result element (array (returncode, message)) for further processing (e.g. create product)
    *       returncode 0 = ok, 1 = not ok
    *       returncode articleList 0 = ok, 10 = missing required attributes, 11 category not found, 12 rejected from mirakl
    */
    private function mirakl_export_offers(array $articleList) : array {

        $mirakl_export_offers_return_value = array();
        $mirakl_export_offers_return_value['returncode'] = 0;
        $mirakl_export_offers_return_value['articleList'] = array();

        $this->Log('Angebotsexport start', print_r($articleList,true));
               
        // First gather all articles as offers and send them
        // Wait for import to finish
        // Evaluate import  

        foreach ($articleList as $key => $article) {          

            $skip_article = false;

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
    
                // Check required attributes
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
                    $mirakl_export_offers_return_value['returncode'] = 1;
                    $article['mirakl_export_offers_result'] = array('returncode' => 10, 'message' => "Pflichtfelder fehlen in Angebotskonfiguration von Kategorie \"".$offer_field_entry['kategorie']."\": ".implode(', ',$missing));
                    $mirakl_export_offers_return_value['articleList'][] = $article;
                    $skip_article = true;
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

            if ($skip_article) {
                continue;
            }

            if (!$category_found) {            
                $mirakl_export_offers_return_value['returncode'] = 1;
                $article['mirakl_export_offers_result'] = array('returncode' => 11, 'message' => "Angebotskonfiguration für Artikel ".$article['nummer'].", Kategorie \"".$kategorie."\" nicht gefunden");
                $mirakl_export_offers_return_value['articleList'][] = $article;
                continue;
            }                               

            if (!empty($additional_fields)) {
                $offer_for_mirakl['offer_additional_fields'] = $additional_fields;
            }
                        
            $offers_for_mirakl[] = $offer_for_mirakl;
            
            $article['mirakl_export_offers_result'] = array('returncode' => 0, 'message' => "");
            $mirakl_export_offers_return_value['articleList'][] = $article;
            
        }

        if (empty($offers_for_mirakl)) {
            $mirakl_export_offers_return_value['returncode'] = 1;
            $this->Log('Angebotsexport keine Artikel bereit', $mirakl_export_offers_return_value);
            $mirakl_export_offers_return_value['message'] = "Angebotsexport keine Artikel bereit";
            return($mirakl_export_offers_return_value);
        }

        $data_for_mirakl = array();
        $data_for_mirakl['offers'] = $offers_for_mirakl;

        $json_for_mirakl = json_encode($data_for_mirakl);

        $this->Log('Angebotsexport Daten', $json_for_mirakl);

        $result = [];
        $response = $this->miraklRequest('offers', postdata: $json_for_mirakl, content_type: 'application/json', raw: true);

        $result = json_decode($response);

        if (!isset($result->import_id)) {
            $mirakl_export_offers_return_value['returncode'] = 1;
            $this->Log('Angebotsimport abgelehnt', print_r($response,true));
            $mirakl_export_offers_return_value['message'] = "Angebotsimport abgelehnt: ".print_r($response,true);
            return($mirakl_export_offers_return_value);
        } 
        
        $this->Log('Angebotsimport angelegt', print_r($response,true));

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
            $this->Log('Angebotsimport fehlgeschlagen', print_r($response,true));
            
            $mirakl_export_offers_return_value['returncode'] = 2;
            $mirakl_export_offers_return_value['message'] = "Angebotsimport fehlgeschlagen: ".print_r($response,true);
            return($mirakl_export_offers_return_value);
        }
               
        if ($result->lines_in_error == 0) {
            $this->Log('Angebotsimport ok', print_r($response,true));
            return($mirakl_export_offers_return_value);
        }
        
        $this->Log('Angebotsimport meldet Fehler in '.$result->lines_in_error.' Zeilen', print_r($response,true));
   
        $result = array();                        
        // Check errors with CSV unfucking...
        $response = $this->miraklRequest('offers/imports/'.$import_id.'/error_report', raw: true);                                     
        $response_lines = preg_split('/\R/', $response, flags: PREG_SPLIT_NO_EMPTY);              
              
        $error_message_key = null;
        $firstline = true;

        foreach ($response_lines as $key => $response_line) {
            $response_array = str_getcsv($response_line,';','"');            
            if ($firstline) {    
                $error_message_key = array_search("error-message",$response_array);
                $firstline = false;
                continue;
            }             
                        
            $article_key = array_search(
                                $response_array['product_id'],
                                array_column(
                                    $mirakl_export_offers_return_value['articleList'],
                                    'product_id')                            
                            );
                 
            $mirakl_export_offers_return_value['articleList'][$article_key]['mirakl_export_offers_result'] = array('returncode' => 12, 'message' => $response_array[$error_message_key]);                          
        }       

        $this->Log('Angebotsimport Fehlerbericht', print_r($response,true));

        $mirakl_export_offers_return_value['returncode'] = 1;

        return($mirakl_export_offers_return_value);        
    }
    
    /*
    *   Create products
    *   Return 
    *       array (returncode, message, articleList) articleList with added mirakl_export_offers_result element (array (returncode, message)) for further processing (e.g. create product)
    *       returncode 0 = ok, 1 = not ok
    *       returncode articleList 0 = ok, 20 rejected from mirakl
    */
    private function mirakl_create_products(array $articleList) : array {
        
        $mirakl_create_products_return_value = array();
        $mirakl_create_products_return_value['returncode'] = 0;
        $mirakl_create_products_return_value['articleList'] = $articleList;
        
        // Build CSV        
        $csv_header = "";
        $newline = "";        
        foreach ($articleList as $article) {
    
            // Try to create the product                   
            $product_for_mirakl = array();                      
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
                      
            // Create CSV from array                                                    
            if (empty($csv_header)) {           
                $csv_header .= '"'.implode('";"',array_keys($product_for_mirakl)).'"';
            }            
            $csv .= $newline.'"'.implode('";"',$product_for_mirakl).'"';                    
            $newline = "\r\n";
        }                
               
        $csv = $csv_header.$newline.$csv;
               
        $result = [];
        
        $this->Log('Produktexport Daten', print_r($csv,true));
                           
        $postdata = array('file' => new CURLStringFile(postname: 'import.csv', data: $csv));
                            
        $response = $this->miraklRequest('products/imports', postdata: $postdata, content_type: 'multipart/form-data', raw: true);

        $result = json_decode($response);

        if (!isset($result->import_id)) {
            $this->Log('Produktimport abgelehnt', print_r($response,true));        
            $mirakl_create_products_return_value['returncode'] = 1;                 
            return($mirakl_create_products_return_value);        
        } 

        $this->Log('Produktimport angelegt', print_r($result,true));

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
            $this->Log('Produktimport fehlgeschlagen', print_r($response,true));
            $mirakl_create_products_return_value['returncode'] = 1;
            return($mirakl_create_products_return_value);        
        }

        if (!$result->has_error_report && !$result->has_transformation_error_report) {
            $this->Log('Produktimport ok', print_r($response,true));        
        } else {
            $this->Log('Produktimport meldet Fehler', print_r($response,true));
            $mirakl_create_products_return_value['returncode'] = 1;
        }    

        if ($result->has_new_product_report) {
            $response = $this->miraklRequest('products/imports/'.$import_id.'/new_product_report', raw: true);
            $this->Log('Produktimport "Hinzugefügte Produkte"-Bericht', print_r($response,true));
        }

        if ($result->has_transformed_file) {
            $response = $this->miraklRequest('products/imports/'.$import_id.'/transformed_file', raw: true);
            $this->Log('Produktimport Datei im Marketplace-Format', print_r($response,true));
        }                        

        if ($result->has_error_report) {
            // Check errors with CSV unfucking...
            $response = $this->miraklRequest('products/imports/'.$import_id.'/error_report', raw: true);
            $response_lines = preg_split('/\R/', $response, flags: PREG_SPLIT_NO_EMPTY);                                  
            $error_message_key = null;
            $firstline = true;
            foreach ($response_lines as $key => $response_line) {
                $response_array = str_getcsv($response_line,';','"');            
                if ($firstline) {    
                    $error_message_key = array_search("ERRORS",$response_array);
                    $firstline = false;
                    continue;
                }                                        
                $article_key = array_search(
                                    $response_array['product_id'],
                                    array_column(
                                        $mirakl_create_products_return_value['articleList'],
                                        'product_id')                            
                                );
                     
                $mirakl_create_products_return_value['articleList'][$article_key]['mirakl_create_products_result'] = array('returncode' => 13, 'message' => $response_array[$error_message_key]);                          
            }       
            $this->Log('Produktimport Fehlerbericht', print_r($response,true));
        }

        if ($result->has_transformation_error_report) {
            // Check errors with CSV unfucking...
            $response = $this->miraklRequest('products/imports/'.$import_id.'/transformation_error_report', raw: true);
            $response_lines = preg_split('/\R/', $response, flags: PREG_SPLIT_NO_EMPTY);                                  
            $error_message_key = null;
            $firstline = true;
            foreach ($response_lines as $key => $response_line) {
                $response_array = str_getcsv($response_line,';','"');            
                if ($firstline) {    
                    $error_message_key = array_search("errors",$response_array);
                    $firstline = false;
                    continue;
                }                                        
                $article_key = array_search(
                                    $response_array['product_id'],
                                    array_column(
                                        $mirakl_create_products_return_value['articleList'],
                                        'product_id')                            
                                );
                     
                $mirakl_create_products_return_value['articleList'][$article_key]['mirakl_create_products_result'] = array('returncode' => 14, 'message' => $response_array[$error_message_key]);                          
            }       
            $this->Log('Produktimport Transformation-Fehlerbericht', print_r($response,true));
        }

        return($mirakl_create_products_return_value);               
    }
    
}

