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

    private $configuration_identifier;

    private $mirakl_error_text_product_missing;

    private $normalTaxId;
    private $reducedTaxId;

    private $offer_configuration;
    private $product_configuration;

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
                'configuration_identifier_source' => [
                    'typ' => 'select',
                    'bezeichnung' => '{|Konfigurationsidentifizierer-Typ|}:',
                    'size' => 40,
                    'info' => 'Woher soll die Konfiguration des jeweiligen Artikels bezogen werden?',
                    'default' => 'feld',
                    'optionen' => ['feld' => '{|Feld|}', 'freifeld' => '{|Freifeld|}', 'eigenschaft' => '{|Eigenschaft|}', 'wert' => '{|Fester Wert|}']
                ],
                'configuration_identifier_source_value' => [
                    'typ' => 'text',
                    'bezeichnung' => '{|Konfigurationsidentifizierer-Wert|}:',
                    'size' => 40,
                    'info' => '',
                    'default' => 'kategoriename'
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
                'product_configuration' => [
                    'typ' => 'textarea',
                    'bezeichnung' => '{|Zuordnung Produkt-Felder je Konfiguration (JSON)|}:',
                    'cols' => 80,
                    'rows' => 20,
                    'info' => 'Die Felder werden vom Mirakl-Betreiber vorgegeben. Ist keine Konfiguration definiert, gilt der Eintrag für alle Artikel. Jedes Feld kann wie folgt zugeordnet werden:<br>Artikelfeld: &quot;Mirakel-Feldname&quot;: {&quot;feld&quot;: &quot;xyz&quot;} oder kurz &quot;Mirakel-Feldname&quot;: &quot;xyz&quot;,<br>Freifeld: &quot;Mirakel-Feldname&quot;: {&quot;freifeld&quot;: &quot;Bezeichnung in Shop&quot;} (Siehe Reiter &quot;Freifelder&quot;),<br>Eigenschaft: &quot;Mirakel-Feldname&quot;: {&quot;eigenschaft&quot;: &quot;Eigenschaftenname xyz&quot;},<br>Fester Wert: &quot;Mirakel-Feldname&quot;: {&quot;wert&quot;: &quot;xyz&quot;}<br><br>Optionen:<br>Text voranstellen: &quot;praefix: &quot;Dieser Text vorne&quot;,<br>Text hinten anstellen: &quot;postfix: &quot;Dieser Text hinten&quot;,<br>Standardwert:  &quot;standardwert: &quot;Dieser Wert wenn nichts gefunden wurde&quot;,<br>Als Zusatzfeld senden: &quot;zusatzfeld&quot;: true',
                    'placeholder' => '[
    {
        &quot;konfigurationen&quot;: [
            &quot;Schuhe&quot;, &quot;Hosen&quot;
        ],
        &quot;felder&quot;: {
          &quot;category&quot;: {&quot;freifeld&quot;: &quot;Kategorie&quot;},
          &quot;Product.SellerProductID&quot;: {&quot;feld&quot;: &quot;nummer&quot;},
          &quot;SHOP.PRODUCT.TITLE&quot;: {&quot;feld&quot;: &quot;nummer&quot;},
          &quot;ATT.GLOBAL.Brandname&quot;: {&quot;feld&quot;: &quot;preis&quot;},
          &quot;Product.BaseUnit&quot;: {&quot;freifeld&quot;: &quot;Kategorie&quot;},
          &quot;ATT.GLOBAL.NoCUperOU&quot;: {&quot;eigenschaft&quot;: &quot;Mirakl Steuertext&quot;, &quot;praefix&quot;: &quot;ST-&quot;},
          &quot;ATT.GLOBAL.NoCUperOU__UNIT&quot;: {&quot;wert&quot;: &quot;false&quot;,&quot;zusatzfeld&quot;: true},
          &quot;Product.TaxIndicator&quot;: {&quot;wert&quot;: &quot;1&quot;,&quot;zusatzfeld&quot;: true}
        }
    }
]'
                ],

                'offer_configuration' => [
                    'typ' => 'textarea',
                    'bezeichnung' => '{|Zuordnung Angebots-Felder je Konfiguration (JSON)|}:',
                    'cols' => 80,
                    'rows' => 20,
                    'info' => '',
                    'placeholder' => '[
    {
        &quot;konfigurationen&quot;: [
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
       
        $this->configuration_identifier = array($einstellungen['felder']['configuration_identifier_source'] => $einstellungen['felder']['configuration_identifier_source_value']);

        $this->mirakl_error_text_product_missing = $einstellungen['felder']['mirakl_error_text_product_missing'];

        $this->normalTaxId = $einstellungen['felder']['normalTaxId'];
        $this->reducedTaxId = $einstellungen['felder']['reducedTaxId'];

        $this->offer_configuration = json_decode($einstellungen['felder']['offer_configuration'], true, flags: JSON_THROW_ON_ERROR);                
        $this->product_configuration = json_decode($einstellungen['felder']['product_configuration'], true, flags: JSON_THROW_ON_ERROR);                                
        
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

        $prefix = null;
        $postfix = null;
        $returnval = null;

        foreach ($field_map_entry as $key => $value) {                   
            switch ($key) {
                case 'feld': 
                    if (isset($article[$value])) {
                        $returnval = $article[$value];
                    } else {
                       throw new Exception("Artikelfeld existiert nicht: \"".$value."\"");               
                    }
                break;
                case 'freifeld': 
                    if (isset($article['freifelder']['DE'][$value])) {
                        $returnval = $article['freifelder']['DE'][$value];
                    } else {
                       throw new Exception("Freifeld existiert nicht: \"".$value."\"");               
                    }
                break;
                case 'eigenschaft':
                    $sql = "SELECT wert FROM artikeleigenschaften ae INNER JOIN artikeleigenschaftenwerte aew ON aew.artikeleigenschaften = ae.id WHERE aew.artikel = '".$article['artikelid']."' AND ae.name = '".$value."' LIMIT 1";
                    $result = $this->app->DB->SelectRow($sql);
                    if (!empty($result)) {
                        $returnval = $result['wert'];
                    }
                break;
                case 'wert':
                    return($prefix.$value.$postfix);
                break;   
                case 'praefix':
                    $prefix = $value;
                break;
                case 'postfix':
                    $postfix = $value;
                break;
                case 'standardwert':
                    if(empty($returnval)) {
                        $returnval = $value;
                    }
                break;
            }                            
        }                      
        return($prefix.$returnval.$postfix);
    }

    public function ImportSendListLager() {
        return $this->mirakl_export_offers_and_products(export_products : false);
    }

    public function ImportSendList() {
        return $this->mirakl_export_offers_and_products(export_products : true);
    }

    /*
     *  Send articles to shop
     */
    public function mirakl_export_offers_and_products(bool $export_products) {

        $message = "";
        $komma = "";
        $status = true;
            
        $articleList = $this->CatchRemoteCommand('data');

        if ($export_products) {                       
            $create_products_result = $this->mirakl_create_products($articleList);    
            if ($create_products_result['returncode'] != 0) {
                $this->Log("Produktsync nach Mirakl hat Fehler", print_r($create_products_result, true));            
                $message = "Produktsync nach Mirakl hat Fehler";
                $komma = ", ";
            } else {
                $message = "Produktimport in Mirakl ok";
                $komma = ", ";
            }
        }
                       
        $offer_export_result = $this->mirakl_export_offers($articleList);
           
        if ($offer_export_result['returncode'] != 0) {
            $this->Log("Angebotsync nach Mirakl hat Fehler", print_r($offer_export_result, true));
            $message .= $komma."Angebotsync in Mirakl hat Fehler";
        } else {
            $message .= $komma."Angebotsimport in Mirakl ok";
        }
       
        return(array('status' => $status, 'message' => $message));

        // Check for missing products and try to create
/*        if ($export_products) {                       
        
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
       
        return(array('status' => false, 'message' => "Angebotsimport in Mirakl hat Fehler")); */

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
        $this->Log('ImportGetAuftrag', print_r($response,true));    
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
            
//            $cart['kunde_sprache'] = '?';
            $cart['kundennummer'] = $order->customer->customer_id;
            
            $cart['name'] = ($order->customer->civility?$order->customer->civility." ":"").$order->customer->firstname." ".$order->customer->lastname;

            if (!empty($order->customer->billing_address->company)) {
                $cart['anrede'] = 'firma';
                $cart['ansprechpartner'] = $cart['name'];
                $cart['name'] = strval($order->customer->billing_address->company);
            }

            $cart['strasse'] = strval($order->customer->billing_address->street_1);
            $cart['adresszusatz'] = strval($order->customer->billing_address->street_2);
            $cart['telefon'] = strval($order->customer->billing_address->phone);
            $cart['plz'] = strval($order->customer->billing_address->zip_code);
            $cart['ort'] = strval($order->customer->billing_address->city);

//            $cart['ustid'] = '?';
            $sql = "SELECT iso FROM laender WHERE iso3 = '".$order->customer->billing_address->country_iso_code."'";           
            $cart['land'] = $this->app->DB->Select($sql);           

            $cart['abweichendelieferadresse'] = 1;
            
            $sql = "SELECT iso FROM laender WHERE iso3 = '".$order->customer->shipping_address->country_iso_code."'";           
            $cart['lieferadresse_land'] = $this->app->DB->Select($sql);           

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

            $cart['waehrung'] = strval($order->currency_iso_code);

//            $cart['ust_befreit'] = '?'; // 1, 2

//            $cart['steuersatz_normal'] = 19;//strval($order->taxes[0]->amount); 
//            $cart['steuersatz_ermaessigt'] 07; // strval($order->taxes[0]->amount);

            $cart['articlelist'] = [];            
            

            $shipping_tax_amount = 0;

            foreach ($order->order_lines as $order_row) {

                $steuersatz = 0;
                $umsatzsteuer_typ = 'normal';
                foreach($order_row->taxes as $tax) {
                    if($tax->rate > $steuersatz) {
                        $steuersatz = $tax->rate;
                    }
                    switch ($order->row->taxes[0]->code) {
                        case $this->reducedTaxId:
                            $umsatzsteuer_typ = 'ermaessigt';
                        break;
                        default:                   
                        case $this->normalTaxId:
                            $umsatzsteuer_typ = 'normal';
                        break;
                    }
                }

                $article = [
                    'articleid' => strval($order_row->offer_sku),
//                    'name' => strval($order_row->product_title),
                    'quantity' => strval($order_row->quantity),
                    'price_netto' => strval($order_row->price_unit),
                    'umsatzsteuer' => $steuersatz
                ];

                foreach ($order_row->shipping_taxes as $shipment_tax) {
                    $shipping_tax_amount += $shipment_tax->amount;
                }
                $cart['articlelist'][] = $article;
            } // foreach articles

            $cart['versandkostennetto'] = round(strval($order->shipping_price),2);
            $cart['versandkostenbrutto'] = round($cart['versandkostennetto']+$shipping_tax_amount,2);

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

        $this->Log('Angebotsexport Start', print_r($articleList,true));
               
        // First gather all articles as offers and send them
        // Wait for import to finish
        // Evaluate import  

        foreach ($articleList as $key => $article) {          

            $skip_article = false;

            /*
             * Export offer
             */
            $configuration_found = false;
            $additional_fields = array();
             
            $offer_for_mirakl = array(
                'state_code' => '11', // ?!?!
                'update_delete' => null // Update delete flag. Could be empty (means "update"), "update" or "delete".
            );

            $configuration_identifier = $this->GetFieldValue($article, $this->configuration_identifier);               
                      
            foreach ($this->offer_configuration as $offer_configuration_entry) {
                if ($offer_configuration_entry['konfigurationen'] != null) {
                    if (!in_array($configuration_identifier,$offer_configuration_entry['konfigurationen'])) {
                        continue;
                    }
                }    
                $configuration_found = true;
                $offer_configuration = $offer_configuration_entry;
                break;
            }

            if ($configuration_found) {
                // Check required attributes
                $required = [
                    'product_id_type',
                    'product_id',
                    'shop_sku',
                    'price'
                ];
                $missing = null;
                foreach ($required as $key) {
                    if (!isset($offer_configuration['felder'][$key])) {
                        $missing[] = $key;
                    }
                }       
                if ($missing) {
                    $mirakl_export_offers_return_value['returncode'] = 1;
                    $article['mirakl_export_offers_result'] = array('returncode' => 10, 'message' => "Pflichtfelder fehlen in Angebotskonfiguration \"".$configuration['konfiguration']."\": ".implode(', ',$missing));
                    $mirakl_export_offers_return_value['articleList'][] = $article;
                    $skip_article = true;
                }
                // Check Required attributes
                
                foreach ($offer_configuration['felder'] as $offer_field => $offer_field_source) {
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
                
                if ($skip_article) {
                    continue;
                }
                                       
                if (!empty($additional_fields)) {
                    $offer_for_mirakl['offer_additional_fields'] = $additional_fields;
                }
                            
                $offers_for_mirakl[] = $offer_for_mirakl;
                
                $article['mirakl_export_offers_result'] = array('returncode' => 0, 'message' => "");
                $mirakl_export_offers_return_value['articleList'][] = $article;

            } else { // configuration_found
                $mirakl_export_offers_return_value['returncode'] = 1;
                $article['mirakl_export_offers_result'] = array('returncode' => 11, 'message' => "Angebotskonfiguration für Artikel ".$article['nummer'].", Konfiguration \"".$konfiguration."\" nicht gefunden");
                $mirakl_export_offers_return_value['articleList'][] = $article;
                continue;
            }                
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
               
        $number_of_articles = 0;

        // Build CSV        
        $csv_header = "";
        $newline = "";        
        foreach ($articleList as $article) {
            $product_for_mirakl = array();  

             // Determine configuration
            $configuration_found = false;
            $konfiguration = $this->GetFieldValue($article, $this->configuration_identifier);               
                      
            foreach ($this->product_configuration as $product_configuration) {
                if ($product_configuration['konfigurationen'] != null) {
                    if (!in_array($konfiguration,$product_configuration['konfigurationen'])) {
                        continue;
                    }
                }                
                $configuration_found = true;
            }         

            if (!$configuration_found) {        
                $mirakl_create_products_return_value['returncode'] = 1;
                $article['mirakl_export_offers_result'] = array('returncode' => 11, 'message' => "Produktkonfiguration für Artikel ".$article['nummer'].", Konfiguration \"".$konfiguration."\" nicht gefunden");
                $mirakl_create_products_return_value['articleList'][] = $article;
                continue;
            }
                  
            foreach ($this->product_configuration as $product_field_entry) {                    
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
              
        if (!$csv) { // No articles found
            return($mirakl_create_products_return_value);        
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
            sleep(30);
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

