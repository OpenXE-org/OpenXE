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
  private $data;
  private $protocol;
  private $apiKey;
  private $shopUrl;
  private $createManufacturerAllowed = false;
  private $idsabholen;
  private $idbearbeitung;
  private $idabgeschlossen;

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

  private function miraklRequest(string $endpoint, array $postdata = null, bool $raw = false)
  {
    $ch = curl_init($this->shopUrl.$endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: ".$this->apiKey));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if (!empty($postdata)) {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    $response = curl_exec($ch);
    if (curl_error($ch)) {
      $this->error[] = curl_error($ch);
    }
    curl_close($ch);

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

  public function ImportGetArticleList()
  {
    $result = [];
    $response = $this->miraklRequest('offers');

    print_r($response);
    exit();

    foreach ($response->products->product as $product) {
      $result[] = $product->reference;
    }

    array_unique($result);
    return $result;
  }

  public function ImportGetArticle()
  {
    $nummer = $this->data['nummer'];
    if (isset($this->data['nummerintern'])) {
      $nummer = $this->data['nummerintern'];
    }
    $nummer = trim($nummer);

    if (empty($nummer))
      return;

    $productsresult = $this->miraklRequest('GET', 'products?filter[reference]='.$nummer);
    $combinationsresult = $this->miraklRequest('GET', 'combinations?filter[reference]='.$nummer);
    $numberOfCombinations = count($combinationsresult->combinations->combination);
    $numberOfProducts = count($productsresult->products->product);
    $numberOfResults = $numberOfProducts + $numberOfCombinations;
    if ($numberOfResults > 1) {
      $this->Log('Got multiple results from Shop', $this->data);
      return;
    }
    elseif ($numberOfResults < 1) {
      $this->Log('No product found in Shop', $this->data);
      return;
    }

    $isCombination = $numberOfCombinations > 0;
    if ($isCombination) {
      $combinationId = intval($combinationsresult->combinations->combination->attributes()->id);
      $combination = $this->miraklRequest('GET', "combinations/$combinationId");
      $productId = intval($combination->combination->id_product);
    } else {
      $productId = intval($productsresult->products->product->attributes()->id);
    }
    $product = $this->miraklRequest('GET', "products/$productId");
    $res = [];
    if ($isCombination) {
      $res['nummer'] = strval($combination->combination->reference);
      $res['artikelnummerausshop'] = strval($combination->combination->reference);
      $res['ean'] = strval($combination->combination->ean13);
      $res['preis_netto'] = floatval($product->product->price) + floatval($combination->combination->price);
    } else {
      $res['nummer'] = strval($product->product->reference);
      $res['artikelnummerausshop'] = strval($product->product->reference);
      $res['ean'] = strval($product->product->ean13);
      $res['preis_netto'] = floatval($product->product->price);
    }
    $names = $this->toMultilangArray($product->product->name->language);
    $descriptions = $this->toMultilangArray($product->product->description->language);
    $shortdescriptions = $this->toMultilangArray($product->product->description_short->language);
    $metadescriptions = $this->toMultilangArray($product->product->meta_description->language);
    $metakeywords = $this->toMultilangArray($product->product->meta_keywords->language);
    $metatitles = $this->toMultilangArray($product->product->meta_title->language);
    $res['name'] = $names['de'];
    $res['name_en'] = $names['en'];
    $res['uebersicht_de'] = $descriptions['de'];
    $res['uebersicht_en'] = $descriptions['en'];
    $res['kurztext_de'] = strip_tags($shortdescriptions['de']);
    $res['kurztext_en'] = strip_tags($shortdescriptions['en']);
    $res['hersteller'] = strval($product->product->manufacturer_name);
    $res['metatitle_de'] = $metatitles['de'];
    $res['metatitle_en'] = $metatitles['en'];
    $res['metadescription_de'] = $metadescriptions['de'];
    $res['metadescription_en'] = $metadescriptions['en'];

    $tags = $product->product->associations->tags->tag;
    $keywords = [];
    foreach ($tags as $tag) {
      $tagid = intval($tag->id);
      $endpoint = "tags/{$tagid}";
      $tagdata = $this->miraklRequest('GET', $endpoint);
      $tagiso = $this->langidToIso[intval($tagdata->tag->id_lang)];
      $tagvalue = strval($tagdata->tag->name);
      if (!array_key_exists($tagiso, $keywords))
        $keywords[$tagiso] = [];
      $keywords[$tagiso][] = $tagvalue;
    }
    $res['metakeywords_de'] = join(',', $keywords['de'] ?? []);
    $res['metakeywords_en'] = join(',', $keywords['en'] ?? []);

    $images = [];
    foreach ($product->product->associations->images->image as $img) {
      $endpoint = "images/products/$productId/$img->id";
      $imgdata = $this->miraklRequest('GET', $endpoint, '', true);
      $images[] = [
          'content' => base64_encode($imgdata),
          'path' => "$img->id.jpg",
          'id' => $img->id
      ];
    }
    $res['bilder'] = $images;
    return $res;
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
 
}
