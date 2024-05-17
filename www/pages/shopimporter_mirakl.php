<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

class Shopimporter_Mirakl extends ShopimporterBase
{
  private $app;
  private $intern;
  private $shopid;
  private $protocol;
  private $apiKey;
  private $shopUrl;
  private $createManufacturerAllowed = false;
  private $idsabholen;
  private $idbearbeitung;
  private $idabgeschlossen;

  public $data;

  // TODO
  private $langidToIso = [3 => 'de', 1 => 'en'];
  private $taxationByDestinationCountry;
  private $orderSearchLimit;


  public function __construct($app, $intern = false)
  {
    $this->app = $app;
    $this->intern = $intern;
    if ($intern)
      return;

  }

  public function EinstellungenStruktur()
  {
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
            ],*/
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
            'shopidmirakl' => [
                'typ' => 'text',
                'bezeichnung' => '{|Shop ID des Shops (optional, int64)|}:',
                'size' => 40,
            ],/*
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
            ],*/
        ]
    ];
  }

  public function getKonfig($shopid, $data)
  {
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
    if ($einstellungen['felder']['autoerstellehersteller'] === '1') {
      $this->createManufacturerAllowed = true;
    }
    $this->idsabholen = $einstellungen['felder']['abholen'];
    $this->idbearbeitung = $einstellungen['felder']['bearbeitung'];
    $this->idabgeschlossen = $einstellungen['felder']['abgeschlossen'];
    $query = sprintf('SELECT `steuerfreilieferlandexport` FROM `shopexport`  WHERE `id` = %d', $this->shopid);
    $this->taxationByDestinationCountry = !empty($this->app->DB->Select($query));
  }

  private function miraklRequest(string $endpoint, array $postdata = null, string $content_type = null, bool $raw = false)
  {
    $ch = curl_init($this->shopUrl.$endpoint);
    
    $headers = array("Authorization: ".$this->apiKey);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if (!empty($postdata)) {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
      $headers[] = 'Content-Type: '.$content_type;
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_VERBOSE,true);
	
    $response = curl_exec($ch);
    if (curl_error($ch)) {
      $this->error[] = curl_error($ch);
    }
    curl_close($ch);

    $information = curl_getinfo($ch);
    print_r($information);

    if ($raw)
      return $response;

    return simplexml_load_string($response);
  }


  public function ImportAuth() {
    $ch = curl_init($this->shopUrl."version");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: ".$this->apiKey));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);    
    if ($code == 200) {
      return 'success '.print_r($response,true);
    }
    return $response;
  }

  /*
  *   Fetches article list from the shop, puts them into table shopexport_getarticles, starts the prozessstarter getarticles which fetches details for each article via ImportGetArticle()
  */
  public function ImportGetArticleList()
  {
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
  public function ImportGetArticle()
  {
    throw new Exception("Not implemented");
  }

  /*
  *  Send articles to shop
  */

  public function ImportSendList()
  {
      $articleList = $this->CatchRemoteCommand('data');
            
      //print_r($articleList);
      
      /*
      Array
(
    [0] => Array
        (
            [artikel] => 1
            [artikelid] => 1
            [nummer] => 700001
            [inaktiv] => 
            [name_de] => Schraube M10x20
            [name_en] => 
            [einheit] => 
            [hersteller] => 
            [herstellernummer] => 
            [ean] => 
            [artikelnummer_fremdnummern] => 
            [kurztext_de] => 
            [kurztext_en] => 
            [anabregs_text] => 
            [anabregs_text_en] => 
            [beschreibung_de] => 
            [beschreibung_en] => 
            [uebersicht_de] => 
            [uebersicht_en] => 
            [herkunftsland] => DE
            [texteuebertragen] => 1
            [metadescription_de] => 
            [metadescription_en] => 
            [metakeywords_de] => 
            [metakeywords_en] => 
            [metatitle_de] => 
            [metatitle_en] => 
            [links_de] => 
            [altersfreigabe] => 
            [links_en] => 
            [startseite_de] => 
            [startseite_en] => 
            [restmenge] => 0
            [startseite] => 0
            [standardbild] => 
            [herstellerlink] => 
            [lieferzeit] => 
            [lieferzeitmanuell] => 
            [gewicht] => 
            [laenge] => 0.00
            [breite] => 0.00
            [hoehe] => 0.00
            [wichtig] => 0
            [porto] => 0
            [gesperrt] => 0
            [sperrgrund] => 
            [gueltigbis] => 0000-00-00
            [umsatzsteuer] => normal
            [ausverkauft] => 0
            [variante] => 0
            [variante_von_id] => 0
            [variantevon] => 
            [pseudopreis] => 0.00
            [keinrabatterlaubt] => 0
            [einkaufspreis] => 0.12000000
            [pseudolager] => 
            [downloadartikel] => 0
            [zolltarifnummer] => 
            [typ] => 1_kat
            [kategoriename] => Handelsware (100000)
            [steuer_art_produkt] => 0
            [steuer_art_produkt_download] => 0
            [anzahl_bilder] => 0
            [anzahl_lager] => 
            [lagerkorrekturwert] => -0
            [autolagerlampe] => 0
            [crosssellingartikel] => Array
                (
                )

            [waehrung] => EUR
            [preis] => 0.16000000
            [steuersatz] => 19
            [staffelpreise_standard] => Array
                (
                    [0] => Array
                        (
                            [ab_menge] => 1.0000
                            [preis] => 0.16000000
                            [bruttopreis] => 0.1904
                            [waehrung] => EUR
                        )

                )

            [staffelpreise] => Array
                (
                    [0] => Array
                        (
                            [ab_menge] => 1.0000
                            [preis] => 0.16000000
                            [bruttopreis] => 0.1904
                            [waehrung] => EUR
                        )

                )

            [bruttopreis] => 0.1904
            [checksum] => 
            [variantevorhanden] => 0
        )

)

      */          
      
     
      foreach ($articleList as $article) {
      
      /*
      * Export product
        POST P41 - Import products to the operator information system
        Url
        /api/products/imports
        Description

        Import products to the operator information system

        Returns the import identifier to track the status of the import
        Call frequencies

        Recommended: Every hour, for each seller
        Maximum: Every 15 minutes, for each seller
        
        Query parameters
        
        shop_id
          optional
          integer - int64
          Use this parameter when your user has access to several shops. If not specified, the shop_id from your default shop will be used.

        HTTP Return Codes

        201 - Created
        Response Headers

        Location - Pre-calculated URL to call to get the import status
        Input (body)

        file
          required
          string - binary
          Import file (CSV or XML or XLSX) to upload. Use multipart/form-data with name file
          
        operator_format
          optional
          boolean
          Force the use of the operator product format
          Default to:false
          Output (response)

        import_id
          required
          integer - int64
      */
      
      $file_contents = 'category;Product.SellerProductID;SHOP.PRODUCT.TITLE;Product.EAN.Main_MP;ATT.GLOBAL.Brandname;ATT.GLOBAL.ManufacturerAID;ATT.GLOBAL.ManufacturerTypeDesc;Product.BaseUnit;ATT.GLOBAL.NoCUperOU;ATT.GLOBAL.NoCUperOU__UNIT;Product.DetailpageVariantValue_MP;Product.RoHS.Compliant;ATT.Text.ProductHeadline;ATT.Text.ProductFeatures;ATT.Text.ProductTextLong;ATT.Text.ProductFacts;ATT.TXT.ProductSpecifications_MP;ATT.Text.ProductDelivery;ATT.Text.ProductSysReq;ATT.Text.ProductSpecialAdd;ATT.CPCS.ArticleKeywords;Product.PrimaryImageURL_MP;Product.Image02URL_MP;Product.Image03URL_MP;Product.Image04URL_MP;Product.Image05URL_MP;Product.ImageIllustration01URL_MP;Product.ImageIllustration02URL_MP;Product.ImageSymbol01URL_MP;Product.ImageSymbol02URL_MP;Product.ImageSymbol03URL_MP;Product.ImageSymbol04URL_MP;Product.ImageSymbol05URL_MP;Product.ImageAward01URL_MP;Product.ImageAward02URL_MP;Product.ImageEnergyEfficiencyLabelURL_MP;Product.DocumentEnergyEfficiencyFicheURL_MP;Product.DocumentDatasheet01URL_MP;Product.DocumentManual01URL_MP;Product.DocumentSecurityAdvisory01URL_MP;Product.DocumentCertificate01URL_MP;Product.DocumentCertificate02URL_MP;Product.DocumentCertificate03URL_MP;Product.Video01URL_MP;Product.Video02URL_MP;Product.TaxIndicator;Product.InvalidationFlag;Product.Disposal.EPR.Category@FR;Product.Disposal.ElektroG.Code@DE;Product.Disposal.ElektroG.Brand@DE;Product.CO2.Footprint;Product.CO2.Footprint__UNIT;Product.Order-Based-Production;ATT.PRODPSEC.SOURCEVOLTAGEMIN;ATT.LOV.MEASUREMENT-CATEGORY;ATT.LOV.MOUNTING_MEASUREMENT_FEATURES;ATT.INSTALLATION_WIDTH;ATT.NUM.SOURCE_VOLTAGE;ATT.PRODSPEC.SOURCEVOLTAGEMAX__UNIT;ATT.WEIGHT.VALUE;ATT.IP;ATT.DIMENSION.LENGTH;ATT.NUM.SOURCE_VOLTAGE__UNIT;ATT.DIMENSION.WIDTH;ATT.NUM.MEASURING_RANGE_UNIVERSAL_MAX;ATT.DIMENSION.DIAMETER;ATT.NUM.INSTALLATION_HEIGHT;ATT.PRODSPEC.SOURCEVOLTAGEMAX;ATT.FRAME-HEIGHT__UNIT;ATT.FRAME-HEIGHT;ATT.LOV.MEASURING_FUNCTION;ATT.NUM.FRAME_WIDTH__UNIT;ATT.MOUNTING_DEPTH__UNIT;ATT.LOV.DISPLAY_LIGHTING;ATT.INSTALLATION_WIDTH__UNIT;ATT.NUM.DISPLAY_COUNTS;ATT.WEIGHT.VALUE__UNIT;ATT.DIGIT_HEIGHT__UNIT;ATT.DIMENSION.LENGTH__UNIT;ATT.DIMENSION.WIDTH__UNIT;ATT.NUM.MEASURING_RANGE_UNIVERSAL_MIN__UNIT;Product.Disposal.ElektroG.Pickup;ATT.DIMENSION.DIAMETER__UNIT;ATT.NUM.INSTALLATION_HEIGHT__UNIT;ATT.CALIBRATION_POSSIBLE;ATT.MOUNTING_DEPTH;ATT.NUM.MEASURING_RANGE_UNIVERSAL_MIN;ATT.DISPLAY_TYPE;ATT.DIMENSION.HEIGHT;ATT.KAT.MOUNTING_MEASUREMENT_TYPE;ATT.PRODPSEC.SOURCEVOLTAGEMIN__UNIT;ATT.COLOR;ATT.DIGIT_HEIGHT;ATT.DIMENSION.HEIGHT__UNIT;ATT.INTERFACE;ATT.NUM.FRAME_WIDTH;ATT.LOV.INTERFACES_COMPONENTS;ATT.INT.RACK_UNIT;ATT.NUM.MEASURING_RANGE_UNIVERSAL_MAX__UNIT;ATT.INT.RACK_UNIT__UNIT;ATT.LOV.CALIBRATION_TO;Product.DetailpageVariantGroup_MP
1105510;700002;"OpenXE Schraube M10x20";;Brand.5105083;OpenXE Schraube M10x20 vom Hersteller;;ST;1;unece.unit.C62;;ROHS-2;;;;;;;;;;https://conradb2b-prod.mirakl.net/mmp/media/product-media/28071/CIRCUTOR_DHC_CPM_1.png;;;;;;;;;;;;;;;;https://tde-instruments.de/wp-content/uploads/CIRCUTOR_DHC-96_CPM-HS_Datenblatt_Englisch.pdf;https://tde-instruments.de/wp-content/uploads/CIRCUTOR_DHC-96_CPM_Anleitung_Englisch.pdf;;;;;;;1;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;';
      
      $postdata = array('file' => new CURLStringFile($file_contents,'file.csv'));
      
      $response = $this->miraklRequest('products/imports', postdata: $postdata, content_type: 'multipart/form-data', raw: true);

      print_r($response);
      exit();
      
      /*
      * Export offer
      */
        $offers_for_mirakl[] = array(
          'product_id_type' => 'SHOP_SKU', // ?!?!
          'price' => $article['preis'],
//          'pricing_unit' => $article['waehrung'],
          'product_id' => $article['nummer'],
          'shop_sku' => $article['nummer'],
          'state_code' => '11', // ?!?!
          'update_delete' => 'update' // update or delete
        );
      }                      
      
      json_decode(null);
      
      $data_for_mirakl = array();
      $data_for_mirakl['offers'] = $offers_for_mirakl;
      
      $json_for_mirakl = json_encode($data_for_mirakl);
      
      $result = [];
      $response = $this->miraklRequest('offers', postdata: $json_for_mirakl, content_type: 'application/json', raw: true);

      $result_array = json_decode($response);
      
      
      print_r($result_array); // stdClass Object ( [import_id] => 69751 ) 
      exit();
  }

  private function toMultilangArray($xmlnode) {
    $res = [];
    foreach ($xmlnode as $item) {
      $iso = $this->langidToIso[strval($item->attributes()->id)];
      $res[$iso] = strval($item);
    }
    return $res;
  }

  private function getOrdersToProcess(int $limit)
  {
    $states = implode('|', explode(',', $this->idsabholen));
    $response = $this->miraklRequest('GET', "orders?display=[id]&limit=$limit&filter[current_state]=[$states]");
    $result = [];
    foreach ($response->orders->order as $order) {
      $result[] = strval($order->id);
    }
    return $result;
  }

  public function getOrderSearchLimit(): int
  {
    if(in_array($this->orderSearchLimit, ['50', '75', '100'])) {
      return (int)$this->orderSearchLimit;
    }

    return 25;
  }

  private function Log($message, $dump = '')
  {
    if ($this->protocol) {
      $this->app->erp->Logfile($message, print_r($dump, true));
    }
  }
 
   public function ImportDeleteAuftrag()
  {
    $auftrag = $this->data['auftrag'];

    $obj = $this->miraklRequest('GET', 'order_histories?schema=blank');
    $obj->order_history->id_order = $auftrag;
    $obj->order_history->id_order_state = $this->idbearbeitung;

    $this->miraklRequest('POST', 'order_histories', $obj->asXML());
  }

  public function ImportUpdateAuftrag()
  {
    $auftrag = $this->data['auftrag'];

    $obj = $this->miraklRequest('GET', 'order_histories?schema=blank');
    $obj->order_history->id_order = $auftrag;
    $obj->order_history->id_order_state = $this->idabgeschlossen;

    $this->miraklRequest('POST', 'order_histories', $obj->asXML());

    $req = $this->miraklRequest('GET', "order_carriers?filter[id_order]=$auftrag&display=[id]");
    $orderCarrierId = strval($req->order_carriers->order_carrier[0]->id);
    $req = $this->miraklRequest('GET', "order_carriers/$orderCarrierId");
    $req->order_carrier->tracking_number = $this->data['tracking'];
    $this->miraklRequest('PUT', "order_carriers/$orderCarrierId", $req->asXML());
  }

  public function ImportGetAuftraegeAnzahl()
  {
    $ordersToProcess = $this->getOrdersToProcess($this->getOrderSearchLimit());
    return count($ordersToProcess);
  }

  public function ImportGetAuftrag()
  {
    $voucherArticleId = $this->app->DB->Select("SELECT s.artikelrabatt FROM `shopexport` AS `s` WHERE s.id='$this->shopid' LIMIT 1");
    $voucherArticleNumber = $this->app->DB->Select("SELECT a.nummer FROM `artikel` AS `a` WHERE a.id='$voucherArticleId' LIMIT 1");

    if (empty($this->idsabholen)) {
      return false;
    }
    $expectOrderArray = !empty($this->data['anzgleichzeitig']) && (int)$this->data['anzgleichzeitig'] > 1;
    $expectNumber = !empty($this->data['nummer']);
    if ($expectNumber) {
      $ordersToProcess = [$this->data['nummer']];
    } elseif (!$expectOrderArray) {
      $ordersToProcess = $this->getOrdersToProcess(1);
    } else {
      $ordersToProcess = $this->getOrdersToProcess($this->getOrderSearchLimit());
    }

    $fetchedOrders = [];
    foreach ($ordersToProcess as $currentOrderId) {
      $order = $this->miraklRequest('GET', "orders/$currentOrderId");
      $order = $order->order;
      $cart = [];
      $cart['zeitstempel'] = strval($order->date_add);
      $cart['auftrag'] = strval($order->id);
      $cart['onlinebestellnummer'] = strval($order->reference);
      $cart['gesamtsumme'] = strval($order->total_paid);
      $cart['versandkostennetto'] = strval($order->total_shipping_tax_excl);
      $cart['bestelldatum'] = strval($order->date_add);

      $carrier = $this->miraklRequest('GET', "carriers/$order->id_carrier");
      $cart['lieferung'] = strval($carrier->carrier->name);

      $customer = $this->miraklRequest('GET', "customers/$order->id_customer");
      $cart['email'] = strval($customer->customer->email);

      $language = $this->miraklRequest('GET', "languages/{$customer->customer->id_lang}");
      if ($language->language->iso_code == "en") {
        $cart['kunde_sprache'] = 'englisch';
      }

      $invoiceAddress = $this->miraklRequest('GET', "addresses/$order->id_address_invoice");
      $invoiceAddress = $invoiceAddress->address;
      $invoiceCountry = $this->miraklRequest('GET', "countries/$invoiceAddress->id_country");
      $invoiceCountry = $invoiceCountry->country;
      $cart['name'] = "$invoiceAddress->firstname $invoiceAddress->lastname";
      if (!empty(strval($invoiceAddress->company))) {
        $cart['ansprechpartner'] = $cart['name'];
        $cart['name'] = strval($invoiceAddress->company);
      }
      $cart['strasse'] = strval($invoiceAddress->address1);
      $cart['adresszusatz'] = strval($invoiceAddress->address2);
      $cart['telefon'] = strval($invoiceAddress->phone_mobile);
      if (empty($cart['telefon']))
        $cart['telefon'] = strval($invoiceAddress->phone);
      $cart['plz'] = strval($invoiceAddress->postcode);
      $cart['ort'] = strval($invoiceAddress->city);
      $cart['ustid'] = strval($invoiceAddress->vat_number);
      $cart['land'] = strval($invoiceCountry->iso_code);

      if (strval($order->id_address_invoice) != strval($order->id_address_delivery)) {
        $deliveryAddress = $this->miraklRequest('GET', "addresses/$order->id_address_delivery");
        $deliveryAddress = $deliveryAddress->address;
        $deliveryCountry = $this->miraklRequest('GET', "countries/$deliveryAddress->id_country");
        $deliveryCountry = $deliveryCountry->country;
        $cart['abweichendelieferadresse'] = 1;
        $cart['lieferadresse_name'] = "$deliveryAddress->firstname $deliveryAddress->lastname";
        if (!empty(strval($deliveryAddress->company))) {
          $cart['lieferadresse_ansprechpartner'] = $cart['lieferadresse_name'];
          $cart['lieferadresse_name'] = strval($deliveryAddress->company);
        }
        $cart['lieferadresse_strasse'] = strval($deliveryAddress->address1);
        $cart['lieferadresse_adresszusatz'] = strval($deliveryAddress->address2);
        $cart['lieferadresse_plz'] = strval($deliveryAddress->postcode);
        $cart['lieferadresse_ort'] = strval($deliveryAddress->city);
        $cart['lieferadresse_land'] = strval($deliveryCountry->iso_code);
      }

      //TODO
      //$cart['transaktionsnummer']
      $cart['zahlungsweise'] = strval($order->payment);

      $taxedCountry = $cart['land'];
      if (!empty($cart['lieferadresse_land']) && $this->taxationByDestinationCountry) {
        $taxedCountry = $cart['lieferadresse_land'];
      }
      $lieferschwelle = $this->app->DB->SelectArr("SELECT * FROM lieferschwelle WHERE empfaengerland='$taxedCountry' LIMIT 1");
      if ($this->app->erp->IstEU($taxedCountry) || !empty($lieferschwelle['ueberschreitungsdatum'])) {
        $cart['ust_befreit'] = 1;
      } elseif ($this->app->erp->Export($taxedCountry)) {
        $cart['ust_befreit'] = 2;
      }

      $taxes = [];
      $this->app->erp->RunHook('getTaxRatesFromShopOrder', 2, $taxedCountry, $taxes);

      if (isset($taxes['normal']) && $taxes['normal'] > 0)
        $cart['steuersatz_normal'] = $taxes['normal'];
      if (isset($taxes['ermaessigt']) && $taxes['ermaessigt'] > 0)
        $cart['steuersatz_ermaessigt'] = $taxes['ermaessigt'];

      $cart['articlelist'] = [];
      foreach ($order->associations->order_rows->order_row as $order_row) {
        $article = [
            'articleid' => strval($order_row->product_reference),
            'name' => strval($order_row->product_name),
            'quantity' => strval($order_row->product_quantity),
            'price_netto' => strval($order_row->unit_price_tax_excl),
        ];

        if ($order_row->unit_price_tax_excl > 0) {
          $steuersatz = (strval($order_row->unit_price_tax_incl) / strval($order_row->unit_price_tax_excl)) - 1;
          $steuersatz = round($steuersatz, 1);
          $article['steuersatz'] = $steuersatz;
        }

        $cart['articlelist'][] = $article;
      }

      $fetchedOrders[] = [
          'id' => $cart['auftrag'],
          'sessionid' => '',
          'logdatei' => '',
          'warenkorb' => base64_encode(serialize($cart)),
          'warenkorbjson' => base64_encode(json_encode($cart)),
      ];
    }
    $this->Log('Precessed order from mirakl', $fetchedOrders);

    return $fetchedOrders;
  }

 
}
