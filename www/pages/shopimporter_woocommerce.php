<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
* 
* Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
*
* This file is licensed under the Embedded Projects General Public License *Version 3.1. 
*
* You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis 
* to obtain the text of the corresponding license version.  
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/
?>
<?php
use Xentral\Components\Http\JsonResponse;

class Shopimporter_Woocommerce extends ShopimporterBase
{

  // protected $canexport = false;

  public $intern = false;
  public $shopid;
  public $data;

  /**
   * @var $client WCClient $client
   */
  public $client;
  public $url;

  /** These variables hold the status strings WooCommerce is using to represent
   * the order status. They can easily be adjusted by the user via the GUI.
   */
  public $statusPending;
  public $statusProcessing;
  public $statusCompleted;
  public $priceType;

  /**
   * Stores the preferences that the user can change when editing this shop importer
   * TODO: Looks like this should be moved to ShopimporterBase
   * Unlike the other shop importers this is stored as an object variable - we dont
   * want every single function that needs some prefrences to init a new SQL query
   * @var $preferences array
   */
  protected $preferences;

  private $protokoll;
  /**
   * @var Application
   */
  protected $app;
  protected $dump;
  public function __construct($app, $intern = false)
  {
    $this->app=$app;
    $this->intern = true;

  }




  public function ImportList()
  {
    $msg = $this->app->erp->base64_url_encode('<div class="info">Sie k&ouml;nnen hier die Shops einstellen</div>');
    header('Location: index.php?module=onlineshops&action=list&msg='.$msg);
    exit;
  }



  /**
   * This function returns the number of orders which have not yet been imported
   */
  public function ImportGetAuftraegeAnzahl()
  {
    // Query the API to get new orders, filtered by the order status as specifed by the user.
    // We set per_page to 100 - this could lead to a situation where there are more than
    // 100 new Orders, but we still only return 100.


    // Array containing additional settings, namely 'ab_nummer' (containting the next order number to get)
    // and 'holeallestati' (an integer)
    $tmp = $this->CatchRemoteCommand('data');

    // Only orders having an order number greater or equal than this should be fetched. null otherwise
    $number_from = empty($tmp['ab_nummer']) ? null : (int)$tmp['ab_nummer'];

    // pending orders will be fetched into this array. it's length is returned at the end of the funciton
    $pendingOrders = array();

    if ($number_from) {
      // Number-based import is selected


      // The WooCommerce API doenst allow for a proper "greater than id n" request.
      // we fake this behavior by creating an array that contains 'many' (~ 1000) consecutive
      // ids that are greater than $from_number and use this array with the 'include' property
      // of the WooCommerce API

      $number_to = $number_from+800;
      if(!empty($tmp['bis_nummer'])){
        $number_to = $tmp['bis_nummer'];
      }

      $fakeGreaterThanIds = range($number_from, $number_to);

      $pendingOrders = $this->client->get('orders', [
        'per_page' => 100,
        'include' => implode(",",$fakeGreaterThanIds),
      ]);


    } else {
      // fetch posts by status

      $pendingOrders = $this->client->get('orders', [
        'status' => array_map('trim',explode(';', $this->statusPending)),
        'per_page' => 100
      ]);

    }

    return count($pendingOrders);
  }


  /**
   * Calling this function queries the api for pending orders and returns them
   * as an array.
   *
   * TODO: Only one single order is returned per invocation of this function.
   * Given that we have to perform an exteremly expensive external HTTP call
   * every time we call this function and could easily process more than one
   * order this seems very bad performance-wise.
   */
  public function ImportGetAuftrag()
  {

    // Array containing additional settings, namely 'ab_nummer' (containting the next order number to get)
    // and 'holeallestati' (an integer)
    $tmp = $this->CatchRemoteCommand('data');

    // Only orders having an order number greater or equal than this should be fetched. null otherwise
    $number_from = empty($tmp['ab_nummer']) ? null : (int)$tmp['ab_nummer'];

    // pending orders will be fetched into this array. it's length is returned at the end of the funciton
    $pendingOrders = array();

    if ($number_from) {
      // Number-based import is selected


      // The WooCommerce API doenst allow for a proper "greater than id n" request.
      // we fake this behavior by creating an array that contains 'many' (~ 1000) consecutive
      // ids that are greater than $from_number and use this array with the 'include' property
      // of the WooCommerce API

      $number_to = $number_from+800;
      if(!empty($tmp['bis_nummer'])){
        $number_to = $tmp['bis_nummer'];
      }

      $fakeGreaterThanIds = range($number_from, $number_to);

      $pendingOrders = $this->client->get('orders', [
        'per_page' => 20,
        'include' => implode(',',$fakeGreaterThanIds),
        'order' => 'asc',
        'orderby' => 'id'
      ]);


    } else {
      // fetch posts by status

      $pendingOrders = $this->client->get('orders', [
        'status' => array_map('trim',explode(';', $this->statusPending)),
        'per_page' => 20,
        'order' => 'asc',
        'orderby' => 'id'
      ]);

    }


    // Return an empty array in case there are no orders to import
    if (count($pendingOrders) === 0) {
      return null;
    }

    $tmp = [];

    foreach ($pendingOrders as $pendingOrder){
      $wcOrder = $pendingOrder;
      $order = $this->parseOrder($wcOrder);

      if (is_null($wcOrder)) {
        continue;
      }

      $tmp[] = [
        'id' => $order['auftrag'],
        'sessionid' => '',
        'logdatei' => '',
        'warenkorb' => base64_encode(serialize($order)),
      ];
    }

    return $tmp;


  }


  // This function searches the wcOrder for the specified WC Meta key
  // and returns it if found, null otherise
  public function get_wc_meta($wcOrder, $meta_key) {
    $value = null;
    foreach ($wcOrder->meta_data as $meta) {
      if ($meta->key == $meta_key) {
        $value = $meta->value;
        break;
      }
    }
    return $value;
  }


  // Parse the given WooCommerce order, return a Xentral array-represented order.
  // Overload this method whenever additional attributes are required.
  public function parseOrder($wcOrder) {





    $order = array();
    $order['auftragsdaten'] = $wcOrder;

    $isBillingCompany = !self::emptyString(
      $wcOrder->billing->company
    );

    $isShippingCompany = !self::emptyString(
      $wcOrder->shipping->company
    );

    $seperateShippingAddress = !self::compareObjects(
      $wcOrder->billing,
      $wcOrder->shipping,
      ['first_name', 'second_name', 'company', 'address_1', 'address_2',
        'city', 'state', 'postcode', 'country']
    );

    if ($isBillingCompany) {
      $order['name'] = $wcOrder->billing->company;
      $order['anrede'] = 'firma';
      $order['ansprechpartner'] = $wcOrder->billing->first_name . ' ' . $wcOrder->billing->last_name;
    } else {
      $order['name'] = $wcOrder->billing->first_name . ' ' . $wcOrder->billing->last_name;

      // Retrieve title from meta data
      // This is not a standard WC Feature! Should work with the very popuplar "WooCommerce germanized" plugin though
      $meta_title = $this->get_wc_meta($wcOrder, "_billing_title");
      if (!is_null($meta_title)) {
        $order['anrede'] = ($meta_title == 'mrs') ? 'frau' : 'herr';
      }
    }


    if(!empty($wcOrder->subshop)){
      $order['subshop'] = $wcOrder->subshop;
    }

    // General order properties and billing address
    $order['auftrag'] = $wcOrder->id;
    $order['order'] = json_decode(json_encode($wcOrder), true);
    $order['strasse'] = $wcOrder->billing->address_1;
    if(!empty($wcOrder->billing->address_2)){
      $order['adresszusatz'] =  $wcOrder->billing->address_2;
    }
    $order['plz'] = $wcOrder->billing->postcode;
    $order['ort'] = $wcOrder->billing->city;
    $order['land'] = $wcOrder->billing->country;
    $order['email'] = $wcOrder->billing->email;
    $order['telefon'] = $wcOrder->billing->phone;
    $order['bestelldatum'] = $wcOrder->date_created;
    $order['gesamtsumme'] = $wcOrder->total;
    $order['transaktionsnummer'] = $wcOrder->transaction_id;
    $order['onlinebestellnummer'] = $wcOrder->number;
    $order['versandkostenbrutto'] = $wcOrder->shipping_total + $wcOrder->shipping_tax;
    $order['internebemerkung'] = $wcOrder->customer_note;
    
    if(!empty((string)$wcOrder->currency)){
      $warenkorb['waehrung'] = (string)$wcOrder->currency;
    }
    //


    //
    // Coupon Codes
    //
    $discount_total = (float)$wcOrder->discount_total;
    $discount_tax = (float)$wcOrder->discount_tax;

    if($discount_total != 0) { // Discount was applied to this order

      // Calculate coupon amount
      if($discount_tax == 0) {
        // Tax calculations are not enabled for any used coupon
        $order['rabattnetto'] = -abs((float)$discount_total);
      } else{
        // At least one used coupon has tax calculations enabled
        $order['rabattbrutto'] = -abs((float)$discount_total);
        $order['rabattbrutto'] += -abs((float)$discount_tax);
      }

      // Set coupon name
      $couponLine = $wcOrder->coupon_lines;

      // Check if we have a valid coupon line just to be sure and set the coupon name
      if($couponLine && is_array($couponLine) && isset($couponLine[0]) && (String)$couponLine[0]->code !== '') {
        $order['rabattname'] =  (String)$couponLine[0]->code;
      }

    }




    $seperateShippingAddress = !self::compareObjects(
      $wcOrder->billing,
      $wcOrder->shipping,
      ['first_name', 'second_name', 'company', 'address_1', 'address_2',
        'city', 'state', 'postcode', 'country']
    );

    if ($seperateShippingAddress) {
      $order['abweichendelieferadresse'] = '1';

      if ($isShippingCompany) {
        $order['lieferadresse_name'] = $wcOrder->shipping->company;
        $order['lieferadresse_ansprechpartner'] = $wcOrder->shipping->first_name . ' ' . $wcOrder->shipping->last_name;
      } else {
        $order['lieferadresse_name'] = $wcOrder->shipping->first_name . ' ' . $wcOrder->shipping->last_name;
      }

      $order['lieferadresse_strasse'] = $wcOrder->shipping->address_1;
      if(!empty($wcOrder->shipping->address_2)){
        $order['lieferadresse_adresszusatz'] = $wcOrder->shipping->address_2;
      }
      $order['lieferadresse_plz'] = $wcOrder->shipping->postcode;
      $order['lieferadresse_ort'] = $wcOrder->shipping->city;
      $order['lieferadresse_land'] = $wcOrder->shipping->country;
    }


    // VAT stuff

    $vatId = $this->get_wc_meta($wcOrder, "_billing_ustid");
    if (!is_null($vatId) && !self::emptyString($vatId)) {
      $order['ustid'] = $vatId;
    }

    foreach ($wcOrder->line_items as $wcOrderItem){
      $order['articlelist'][] = $this->parseItem($wcOrderItem);
    }

    $order['zahlungsweise'] = $wcOrder->payment_method;
    $order['lieferung'] = $wcOrder->shipping_lines[0]->method_id;


    return $order;
  }

  function parseItem($wcOrderItem){
    // The WC API doesnt expose the net price of a single product in the get order endpoint.
    // We could query each individual product and get the price, but that would result in a
    // huge amount of HTTP requests.
    //
    // We could use the price_netto attribute in the order item, but we get a higher precision using
    // this custom calculation as we have access to the exact `total_tax` amount.
    // Passing the net value of the line eliminates rounding issues if the position happens to have large quantites

    switch ($this->priceType){
      case 'grosscalculated':
        $priceValue = ((float)$wcOrderItem->subtotal + (float)$wcOrderItem->subtotal_tax) / (float)$wcOrderItem->quantity;
        $priceType = 'price';
        break;
      case 'netcalculated':
      default:
        $priceType = 'price_netto';
        $priceValue = (float)$wcOrderItem->subtotal / (float)$wcOrderItem->quantity;
        break;
    }

    $orderItem = array();
    $orderItem['articleid'] = $wcOrderItem->sku;
    $orderItem['name'] = $wcOrderItem->name;
    $orderItem[$priceType] = $priceValue;
    $orderItem['quantity'] = $wcOrderItem->quantity;

    // The item could be a variable product in which case we have to retrieve the sku of the variation product
    if (!empty($wcOrderItem->variation_id)) {
      $variation_product_sku = $this->getSKUByShopId($wcOrderItem->id,$wcOrderItem->variation_id);
      if (!empty($variation_product_sku)) {
        $orderItem['articleid'] = $variation_product_sku;
      }
    }

    return $orderItem;
  }


  /**
   * Sets the Order status to processing, meaning we've successfully imported
   * the order into our DB. This prevents the order from beeing imported again.
   */
  public function ImportDeleteAuftrag()
  {
    $orderId = $this->CatchRemoteCommand('data')['auftrag'];

    if (!empty($orderId)) {
      $this->client->put('orders/'.$orderId, [
        'status' => $this->statusProcessing,
      ]);
    }


    return 'ok';
  }

  /**
   * Updates the order status once payment and shipping are set to ok.
   * Also updates the order with the shipping tracking code
   * @return string
   * @throws WCHttpClientException
   */
  public function ImportUpdateAuftrag()
  {
    $tmp = $this->CatchRemoteCommand('data');

    $orderId = $tmp['auftrag'];
    $paymentOk = $tmp['zahlung'];
    $shippingOk = $tmp['versand'];
    $trackingCode = $tmp['tracking'];
    $carrier = $tmp['versandart'];

    if ($paymentOk === 'ok' || $paymentOk === '1'){
      $paymentOk = true;
    }

    if ($shippingOk === 'ok' || $shippingOk === '1'){
      $shippingOk = true;
    }

    if (!empty($trackingCode)) {
      $this->client->post('orders/'.$orderId.'/notes', [
        'note' => 'Tracking Code: ' . $trackingCode
      ]);
      $this->WooCommerceLog("Tracking Code Rückmeldung für Auftrag: $orderId", $trackingCode);
    }

    if ($paymentOk && $shippingOk) {
        $updateData = [
            'status' => $this->statusCompleted,
            'meta_data' => [
                [
                    'key' => 'tracking_code',
                    'value' => $trackingCode
                ],
                [
                    'key' => 'shipping_carrier',
                    'value' => $carrier
                ]
            ],
        ];
        $this->client->put('orders/'.$orderId, $updateData);
        $this->WooCommerceLog("Statusrückmeldung 'completed' für Auftrag: $orderId",$this->statusCompleted );
    }


    return 'ok';
  }


  /**
   * This function syncs the current stock to the remote WooCommerce shop
   * @return int
   * @throws WCHttpClientException
   */
  public function ImportSendListLager()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $anzahl = 0;
    $ctmp = count($tmp);

    for($i=0;$i<$ctmp;$i++)
    {
      // Get important values from input data
      $artikel = $tmp[$i]['artikel'];
      if($artikel === 'ignore') {
        continue;
      }
      $nummer = $tmp[$i]['nummer'];
      if(!empty($tmp[$i]['artikelnummer_fremdnummern'][0]['nummer'])){
        $nummer = $tmp[$i]['artikelnummer_fremdnummern'][0]['nummer'];
      }
      $lageranzahl = $tmp[$i]['anzahl_lager'];
      $pseudolager = trim($tmp[$i]['pseudolager']);
      $inaktiv = $tmp[$i]['inaktiv'];
      $status ='publish';

      // Do some computations, sanitize input

      if($pseudolager !== ''){
        $lageranzahl = $pseudolager;
      }

      if($tmp[$i]['ausverkauft']){
        $lageranzahl = 0;
      }

      if($inaktiv){
        $status = 'private';
      }

      // get the product id that WooCommerce uses to represent the current article
      $remoteIdInformation = $this->getShopIdBySKU($nummer);

      if (empty($remoteIdInformation['id'])) {
        // The online shop doesnt know this article, write to log and continue with next product
        $this->WooCommerceLog("Artikel $nummer wurde im Online-Shop nicht gefunden! Falsche Artikelnummer im Shop hinterlegt?");

        continue;
      }

      // Sync settings to online store
      $updateProductParams = [
        'manage_stock' => true,
        'status' => $status,
        'stock_quantity' => $lageranzahl
        // WooCommerce doesnt have a standard property for the other values, we're ignoring them
      ];
      if($remoteIdInformation['isvariant']){
        $result = $this->client->put('products/' . $remoteIdInformation['parent'].'/variations/'. $remoteIdInformation['id'], $updateProductParams);
      }else{
        $result = $this->client->put('products/' . $remoteIdInformation['id'], $updateProductParams);
      }
      $this->WooCommerceLog("WooCommerce Lagerzahlenübertragung für Artikel: $nummer / $remoteIdInformation[id] - Anzahl: $lageranzahl", $result);

      $anzahl++;
    }


    return $anzahl;
  }



  public function ImportStorniereAuftrag() {
    $orderId = $this->CatchRemoteCommand('data')['auftrag'];


    if (!empty($orderId)) {
      $this->client->put('orders/'.$orderId, [
        'status' => 'cancelled',
      ]);
    } else {
      return 'failed';
    }

    return 'ok';
  }


  public function ImportSendList() {


    $tmp = $this->catchRemoteCommand('data');

    $anzahl = 0;

    for($i=0;$i<count($tmp);$i++){



      $artikel = $tmp[$i]['artikel'];
      $nummer = $tmp[$i]['nummer'];
      if(!empty($tmp[$i]['artikelnummer_fremdnummern'][0]['nummer'])){
        $nummer = $tmp[$i]['artikelnummer_fremdnummern'][0]['nummer'];
      }
      $laststock = $tmp[$i]['restmenge'];
      $inaktiv = $tmp[$i]['inaktiv'];
      $shippingtime = $tmp[$i]['lieferzeitmanuell'];

      $hersteller = $tmp[$i]['hersteller'];
      $herstellerlink = $tmp[$i]['herstellerlink'];

      $name_de = $tmp[$i]['name_de'];
      $name_en = $tmp[$i]['name_en'];
      $description = html_entity_decode($tmp[$i]['uebersicht_de']);
      $description_en = html_entity_decode($tmp[$i]['uebersicht_en']);
      $preis = $tmp[$i]['preis'];

      $kurzbeschreibung = $tmp[$i]['kurztext_de'];

      $weight_kg = $tmp[$i]['gewicht'];
      $dim_length = $tmp[$i]['laenge'];
      $dim_width = $tmp[$i]['breite'];
      $dim_height = $tmp[$i]['hoehe'];


      // Sanitize dimensions
      if (self::emptyString($weight_kg))
        $weight_kg = null;

      if (self::emptyString($dim_length))
        $dim_length = null;

      if (self::emptyString($dim_width))
        $dim_width = null;

      if (self::emptyString($dim_height))
        $dim_height = null;



      $meta_desc = $tmp[$i]['metadescription_de'];
      $meta_title = $tmp[$i]['metatitle_de'];


      $pseudopreis = $tmp[$i]['pseudopreis'];//*1.19;
      if($pseudopreis <= $preis)$pseudopreis = $preis;
      $steuersatz = $tmp[$i]['steuersatz'];
      if($steuersatz > 1.10){
        $steuersatz = 'normal';
      }
      else{
        $steuersatz = 'ermaessigt';
      }

      $lageranzahl = $tmp[$i]['anzahl_lager'];
      $pseudolager = trim($tmp[$i]['pseudolager']);

      if($pseudolager > 0) $lageranzahl=$pseudolager;
      if($tmp[$i]['ausverkauft']=="1"){
        $lageranzahl=0; $laststock="1";
      }

      if($inaktiv)$aktiv=0;
      else $aktiv=1;

      $product_id = 0;
      if($laststock!="1") $laststock=0;

      $remoteIdInformation = $this->getShopIdBySKU($nummer);
      $product_id  = $remoteIdInformation['id'];

      $commonMetaData = [
        ['key' => '_yoast_wpseo_metadesc', 'value' => $meta_desc],
        ['key' => '_yoast_wpseo_title', 'value' => $meta_title],
      ];



      // Attributes that are used for both updating an existing product as well as creating a new one
      $commonProductAtts = [
        'name' => $name_de,
        'description' => $description,
        'status'=>($aktiv?'publish':'private'),
        'regular_price' => number_format($pseudopreis,2,'.',''),
        'sale_price' => number_format($preis,2,'.',''),
        'short_description' => $kurzbeschreibung,
        'weight' => $weight_kg,
        'dimensions' => [
          'length' => $dim_length,
          'width' => $dim_width,
          'height' => $dim_height
        ],
        'meta_data' => $commonMetaData,
      ];


      if($lageranzahl===0){
        $commonProductAtts['stock_status'] = 'outofstock';
        $commonProductAtts['manage_stock'] = true;
      }
      elseif($lageranzahl===''){
        $commonProductAtts['stock_status'] = 'instock';
        $commonProductAtts['manage_stock'] = false;
      }
      else{
        $commonProductAtts['stock_status'] = 'instock';
        $commonProductAtts['manage_stock'] = true;
      }

      if($lageranzahl!=='') {
        $commonProductAtts['stock_quantity'] = (int)$lageranzahl;
      }



      if(!is_null($product_id)) {
        // Such a product already appears to exist, so we update it
        $this->client->put('products/'.$product_id, array_merge([

        ], $commonProductAtts));

        $this->WooCommerceLog("WooCommerce Artikel geändert für Artikel: $nummer / $product_id");

      }
      else{
        // create a new product
        $product_id = $this->client->post('products/', array_merge([
          'sku' => $nummer,
        ], $commonProductAtts))->id;



        $this->WooCommerceLog("WooCommerce neuer Artikel angelegt: $nummer");

      }


      // TODO: Kategoriebaum und Bilder werden noch nicht uebertragen

      // if(isset($tmp[$i]['kompletter_kategorienbaum'])){
      //   $baum = $tmp[$i]['kompletter_kategorienbaum'];
      //   $this->updateKategorieBaum($baum);
      // }

      // if(isset($tmp[$i]['Dateien'])){
      //   $dateien = $tmp[$i]['Dateien'];
      //   $this->save_images($dateien, $product_id);
      // }


      // Update the associated product categories

      $chosenCats = array();
      if(isset($tmp[$i]['kategorien']) || isset($tmp[$i]['kategoriename'])){


        $kategorien = $tmp[$i]['kategorien'];
        if (!($kategorien) && !self::emptyString($tmp[$i]['kategoriename'])) {
          $kategorien = array(
            array(
              'name' => $tmp[$i]['kategoriename'],
            )
          );
        }
        if(count($kategorien)>0){

          // Retrive all WC categories via API
          $allWooCommerceCategories = $this->client->get('products/categories', ['per_page' => '100']);


          $searchWpCategories = [];
          foreach($allWooCommerceCategories as $a){
            $searchWpCategories[$a->id] = $a->name;
          }
          // searchWPCategories is an assoc array of type WCCatId(Int) -> WCCatName(string)

          // Iterate over the categories that are choosen in xentral
          foreach($kategorien as $k => $v){
            $wawi_cat_name = $v['name'];

            $wcCatId = null;

            // If WC has a matching category. We match based on name!
            if(array_search($wawi_cat_name,array_values($searchWpCategories)) !== false) {

              // get  id of that WC Category
              $wcCatId = array_search($wawi_cat_name,$searchWpCategories);


            } else {

              // No matching category exists
              $wcCatId = $this->client->post('products/categories', [
                'name' => $wawi_cat_name,
              ])->id;

            }


            if ($wcCatId) {
              // update category. We first retrieve the product and append the new product category, not replace the entire category array.
              $alreadyAssignedWCCats = $this->client->get('products/'.$product_id, [
                'per_page' => 1,
              ])->categories;


              // Get ids of existing categories
              $existingCategoryIds = [];
              foreach ($alreadyAssignedWCCats as $cat) {
                $existingCategoryIds[] = $cat->id;
              }

              $allCatIds = array_merge($existingCategoryIds, array($wcCatId));

              // prepare data to be in correct format for WC api. should be individual items with key 'id' and id as value
              $allCatIdsWCAPIRep = array();
              foreach($allCatIds as $id) {
                $allCatIdsWCAPIRep[] = ['id' => $id];
              }

              // Update category assignment
              $this->client->put('products/'.$product_id, [
                'categories' => $allCatIdsWCAPIRep,
              ]);

              $chosenCats[] = $wcCatId;
            }
          }
        }
      }

      $anzahl++;
    }




    return $anzahl;

    // return array($product_id,$anzahl,$nummer,$steuersatz, $preis);

  }


  /**
   * Checks the connection to the WooCommerce API by trying a simple API request
   *
   * @return string
   */
  public function ImportAuth()
  {
    try {
      $orders = $this->client->get('orders', ['per_page' => '1']);
      return 'success';
    } catch (Exception $e) {
      return 'failed: Keine Verbindung zur API - ' . $e->getMessage();
    }
  }


  /**
   * This is called by class.remote.php, initializes some class variables from the DB
   * @param  [type] $shopid [description]
   * @param  [type] $data   [description]
   * @return [type]         [description]
   * @throws WCHttpClientException
   */
  public function getKonfig($shopid, $data)
  {
    $this->shopid = $shopid;
    $this->data = $data;

    $preferences_json = $this->app->DB->Select("SELECT einstellungen_json FROM shopexport WHERE id = '$this->shopid' LIMIT 1");
    if($preferences_json){
      $preferences = json_decode($preferences_json,true);
    }

    $this->protokoll = $preferences['felder']['protokoll'];
    $ImportWooCommerceApiSecret = $preferences['felder']['ImportWoocommerceApiSecret'];
    $ImportWooCommerceApiKey = $preferences['felder']['ImportWoocommerceApiKey'];
    $ImportWooCommerceApiUrl = $preferences['felder']['ImportWoocommerceApiUrl'];

    $this->statusPending = $preferences['felder']['statusPending'];
    $this->statusProcessing = $preferences['felder']['statusProcessing'];
    $this->statusCompleted = $preferences['felder']['statusCompleted'];

    $this->priceType = $preferences['felder']['priceType'];

    $this->url = $ImportWooCommerceApiUrl;
    $this->client = new WCClient(
    //URL des WooCommerce Rest Servers
      $ImportWooCommerceApiUrl,
      //WooCommerce API Key
      $ImportWooCommerceApiKey,
      //WooCommerce API Secret
      $ImportWooCommerceApiSecret,

      ["query_string_auth" => true]
    );

  }

  /**
   * @param array $shopArr
   * @param array $postData
   *
   * @return array
   */
  public function updateShopexportArr($shopArr, $postData)
  {
    $shopArr['demomodus'] = 0;
    $shopArr['anzgleichzeitig'] = 1;
    $shopArr['cronjobaktiv'] = 1;

    return $shopArr;
  }

  /**
   * @return JsonResponse|null
   */
  public function AuthByAssistent()
  {
    $ImportWooCommerceApiKey = $this->app->Secure->GetPOST('ImportWoocommerceApiKey');
    $ImportWooCommerceApiSecret = $this->app->Secure->GetPOST('ImportWoocommerceApiSecret');
    $ImportWooCommerceApiUrl = $this->app->Secure->GetPOST('ImportWoocommerceApiUrl');

    if(empty($ImportWooCommerceApiUrl)) {
      return new JsonResponse(['error' => 'Bitte die API-Url angeben'], JsonResponse::HTTP_BAD_REQUEST);
    }
    if(empty($ImportWooCommerceApiKey)) {
      return new JsonResponse(['error' => 'Bitte den API-Key angeben'], JsonResponse::HTTP_BAD_REQUEST);
    }
    if(empty($ImportWooCommerceApiSecret)) {
      return new JsonResponse(['error' => 'Bitte das API-Secret angeben'], JsonResponse::HTTP_BAD_REQUEST);
    }
    $this->client = new WCClient(
      $ImportWooCommerceApiUrl,
      $ImportWooCommerceApiKey,
      $ImportWooCommerceApiSecret,

      ['query_string_auth' => true]
    );
    $auth = $this->ImportAuth();

    if ($auth !== 'success') {
      return new JsonResponse(['error' => $auth], JsonResponse::HTTP_BAD_REQUEST);
    }

    return null;
  }


  /**
   * @return array[]
   */
  public function getCreateForm()
  {
    return [
      [
        'id' => 0,
        'name' => 'urls',
        'inputs' => [
          [
            'label' => 'API Url',
            'type' => 'text',
            'name' => 'ImportWoocommerceApiUrl',
            'validation' => true,
          ],

        ],
      ],
      [
        'id' => 1,
        'name' => 'username',
        'inputs' => [
          [
            'label' => 'API Key',
            'type' => 'text',
            'name' => 'ImportWoocommerceApiKey',
            'validation' => true,
          ],
        ],
      ],
      [
        'id' => 2,
        'name' => 'password',
        'inputs' => [
          [
            'label' => 'API Secret',
            'type' => 'password',
            'name' => 'ImportWoocommerceApiSecret',
            'validation' => true,
          ],
        ],
      ],
    ];
  }

  /**
   * Returns the WooCommerce Product Id of a product given the SKU (= Xentral arikelnummer)
   *
   * @param string $sku Artikelnummer
   *
   * @return array|null The WooCommerce product id of the given product, null if such a product does not exist
   * @throws WCHttpClientException
   */
  private function getShopIdBySKU($sku) {

    // Retrieve the product with the given sku.
    // Note: We limit the result set to 1 (per_page=1), so this doesnt work
    // if there are multiple products with the same sku. should not happen in practice anyway
    $product = $this->client->get('products', ['sku' => $sku, 'per_page' => 1]);

    // We look at the first product in the array.
    // We may get an empty array, in that case null is returned
    if (isset($product[0])){
      return [
        'id' => $product[0]->id,
        'parent' => $product[0]->parent_id,
        'isvariant' => !empty($product[0]->parent_id)];
    }

    return null;
  }


  private function getSKUByShopId($articleid, $variationid) {
    $product = $this->client->get("products/$articleid/variations/$variationid");

    // We look at the first product in the array.
    // We may get an empty array, in that case null is returned
    if (!empty($product))
      return $product->sku;
    return null;
  }

  public function EinstellungenStruktur()
  {
    return
      array(
        'ausblenden'=>array('abholmodus'=>array('zeitbereich')),
        'archiv'=>array('ab_nummer'),
        'felder'=>array(
          'protokoll'=>array('typ'=>'checkbox','bezeichnung'=>'Protokollierung im Logfile:'),
          'ImportWoocommerceApiKey'=>array('typ'=>'text','bezeichnung'=>'{|API Key:','size'=>60),
          'ImportWoocommerceApiSecret'=>array('typ'=>'text','bezeichnung'=>'{|API Secret|}:','size'=>60),
          'ImportWoocommerceApiUrl'=>array('typ'=>'text','bezeichnung'=>'{|API Url|}:','size'=>40),
          'statusPending'=>array('typ'=>'text','bezeichnung'=>'{|Statusname Bestellung offen|}:','size'=>40, 'default' => 'pending', 'info' => '({|ggfs. getrennt durch ";": pending;on-hold|})'),
          'statusProcessing'=>array('typ'=>'text','bezeichnung'=>'{|Statusname Bestellung in Bearbeitung|}:','size'=>10, 'default' => 'processing'),
          'statusCompleted'=>array('typ'=>'text','bezeichnung'=>'{|Statusname Bestellung fertig|}:','size'=>10, 'default' => 'completed'),
          'priceType'=>array('typ'=>'select','bezeichnung'=>'{|Preisberechnungsgrundlage bei Auftragsimport|}','optionen'=>array('netcalculated'=>'{|Nettopreis zurückrechnen (Standard)|}','grosscalculated'=>'{|Bruttopreis zurückrechnen|}')),
        ));
  }


  /**
   * Writes data to the syslog
   * @param [type] $nachricht message that will be logged
   * @param string $dump      php array or object, printed using print_r
   */
  public function WooCommerceLog($nachricht, $dump = '')
  {
    if($this->protokoll){
      $this->app->erp->LogFile($nachricht, print_r($dump, true));
    }
  }


  /**
   * Compares two Objects and returns true if every variable in items
   * is the same in $a and $b
   * @param  Obj $a     [description]
   * @param  Obj $b     [description]
   * @param  array $items [description]
   * @return Bool        [description]
   */
  protected static function compareObjects($a, $b, $items) {
    foreach($items as $v) {
      if (property_exists($a, $v) && property_exists($b, $v)) {
        if ($a->$v != $b->$v) {
          //print_r($v);
          return false;
        }
      }
    }
    return true;
  }



  /**
   * Returns true when the string entered is empty, after stripping whitespace
   * @param  String $string input
   * @return String         output
   */
  protected static function emptyString($string) {
    return (strlen(trim($string)) == 0);
  }

}



class WCClient
{

  /**
   * WooCommerce REST API WCClient version.
   */
  const VERSION = '3.0.0';

  /**
   * HttpClient instance.
   *
   * @var WCHttpClient
   */
  public $http;

  /**
   * Initialize client.
   *
   * @param string $url            Store URL.
   * @param string $consumerKey    Consumer key.
   * @param string $consumerSecret Consumer secret.
   * @param array  $options        WCOptions (version, timeout, verify_ssl).
   *
   * @throws WCHttpClientException
   */
  public function __construct($url, $consumerKey, $consumerSecret, $options = [])
  {
    $this->http = new WCHttpClient($url, $consumerKey, $consumerSecret, $options);
  }

  /**
   * POST method.
   *
   * @param string $endpoint API endpoint.
   * @param array  $data     WCRequest data.
   *
   * @throws WCHttpClientException
   *
   * @return array
   */
  public function post($endpoint, $data)
  {
    return $this->http->request($endpoint, 'POST', $data);
  }

  /**
   * PUT method.
   *
   * @param string $endpoint API endpoint.
   * @param array  $data     WCRequest data.
   *
   * @throws WCHttpClientException
   *
   * @return array
   */
  public function put($endpoint, $data)
  {
    return $this->http->request($endpoint, 'PUT', $data);
  }

  /**
   * GET method.
   *
   * @param string $endpoint   API endpoint.
   * @param array  $parameters WCRequest parameters.
   *
   * @throws WCHttpClientException
   *
   * @return array
   */
  public function get($endpoint, $parameters = [])
  {
    return $this->http->request($endpoint, 'GET', [], $parameters);
  }

  /**
   * DELETE method.
   *
   * @param string $endpoint   API endpoint.
   * @param array  $parameters WCRequest parameters.
   *
   * @throws WCHttpClientException
   *
   * @return array
   */
  public function delete($endpoint, $parameters = [])
  {
    return $this->http->request($endpoint, 'DELETE', [], $parameters);
  }

  /**
   * OPTIONS method.
   *
   * @param string $endpoint API endpoint.
   *
   * @throws WCHttpClientException
   *
   * @return array
   */
  public function options($endpoint)
  {
    return $this->http->request($endpoint, 'OPTIONS');
  }
}

class WCResponse
{

  /**
   * WCResponse code.
   *
   * @var int
   */
  private $code;

  /**
   * WCResponse headers.
   *
   * @var array
   */
  private $headers;

  /**
   * WCResponse body.
   *
   * @var string
   */
  private $body;

  /**
   * Initialize response.
   *
   * @param int    $code    WCResponse code.
   * @param array  $headers WCResponse headers.
   * @param string $body    WCResponse body.
   */
  public function __construct($code = 0, $headers = [], $body = '')
  {
    $this->code    = $code;
    $this->headers = $headers;
    $this->body    = $body;
  }

  /**
   * Set code.
   *
   * @param int $code WCResponse code.
   */
  public function setCode($code)
  {
    $this->code = (int) $code;
  }

  /**
   * Set headers.
   *
   * @param array $headers WCResponse headers.
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }

  /**
   * Set body.
   *
   * @param string $body WCResponse body.
   */
  public function setBody($body)
  {
    $this->body = $body;
  }

  /**
   * Get code.
   *
   * @return int
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * Get headers.
   *
   * @return array $headers WCResponse headers.
   */
  public function getHeaders()
  {
    return $this->headers;
  }

  /**
   * Get body.
   *
   * @return string $body WCResponse body.
   */
  public function getBody()
  {
    return $this->body;
  }
}

class WCOptions
{

  /**
   * Default WooCommerce REST API version.
   */
  const VERSION = 'wc/v3';

  /**
   * Default request timeout.
   */
  const TIMEOUT = 30;

  /**
   * Default WP API prefix.
   * Including leading and trailing slashes.
   */
  const WP_API_PREFIX = '/wp-json/';

  /**
   * Default User Agent.
   * No version number.
   */
  const USER_AGENT = 'WooCommerce API Client-PHP';

  /**
   * WCOptions.
   *
   * @var array
   */
  private $options;

  /**
   * Initialize HTTP client options.
   *
   * @param array $options Client options.
   */
  public function __construct($options)
  {
    $this->options = $options;
  }

  /**
   * Get API version.
   *
   * @return string
   */
  public function getVersion()
  {
    return isset($this->options['version']) ? $this->options['version'] : self::VERSION;
  }

  /**
   * Check if need to verify SSL.
   *
   * @return bool
   */
  public function verifySsl()
  {
    return isset($this->options['verify_ssl']) ? (bool) $this->options['verify_ssl'] : true;
  }

  /**
   * Get timeout.
   *
   * @return int
   */
  public function getTimeout()
  {
    return isset($this->options['timeout']) ? (int) $this->options['timeout'] : self::TIMEOUT;
  }

  /**
   * Basic Authentication as query string.
   * Some old servers are not able to use CURLOPT_USERPWD.
   *
   * @return bool
   */
  public function isQueryStringAuth()
  {
    return isset($this->options['query_string_auth']) ? (bool) $this->options['query_string_auth'] : false;
  }

  /**
   * Check if is WP REST API.
   *
   * @return bool
   */
  public function isWPAPI()
  {
    return isset($this->options['wp_api']) ? (bool) $this->options['wp_api'] : true;
  }

  /**
   * Custom API Prefix for WP API.
   *
   * @return string
   */
  public function apiPrefix()
  {
    return isset($this->options['wp_api_prefix']) ? $this->options['wp_api_prefix'] : self::WP_API_PREFIX;
  }

  /**
   * oAuth timestamp.
   *
   * @return string
   */
  public function oauthTimestamp()
  {
    return isset($this->options['oauth_timestamp']) ? $this->options['oauth_timestamp'] : \time();
  }

  /**
   * Custom user agent.
   *
   * @return string
   */
  public function userAgent()
  {
    return isset($this->options['user_agent']) ? $this->options['user_agent'] : self::USER_AGENT;
  }

  /**
   * Get follow redirects
   *
   * @return bool
   */
  public function getFollowRedirects()
  {
    return isset($this->options['follow_redirects']) ? (bool) $this->options['follow_redirects'] : false;
  }
}

class WCRequest
{

  /**
   * WCRequest url.
   *
   * @var string
   */
  private $url;

  /**
   * WCRequest method.
   *
   * @var string
   */
  private $method;

  /**
   * WCRequest paramenters.
   *
   * @var array
   */
  private $parameters;

  /**
   * WCRequest headers.
   *
   * @var array
   */
  private $headers;

  /**
   * WCRequest body.
   *
   * @var string
   */
  private $body;

  /**
   * Initialize request.
   *
   * @param string $url        WCRequest url.
   * @param string $method     WCRequest method.
   * @param array  $parameters WCRequest paramenters.
   * @param array  $headers    WCRequest headers.
   * @param string $body       WCRequest body.
   */
  public function __construct($url = '', $method = 'POST', $parameters = [], $headers = [], $body = '')
  {
    $this->url        = $url;
    $this->method     = $method;
    $this->parameters = $parameters;
    $this->headers    = $headers;
    $this->body       = $body;
  }

  /**
   * Set url.
   *
   * @param string $url WCRequest url.
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }

  /**
   * Set method.
   *
   * @param string $method WCRequest method.
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }

  /**
   * Set parameters.
   *
   * @param array $parameters WCRequest paramenters.
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }

  /**
   * Set headers.
   *
   * @param array $headers WCRequest headers.
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }

  /**
   * Set body.
   *
   * @param string $body WCRequest body.
   */
  public function setBody($body)
  {
    $this->body = $body;
  }

  /**
   * Get url.
   *
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }

  /**
   * Get method.
   *
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }

  /**
   * Get parameters.
   *
   * @return array
   */
  public function getParameters()
  {
    return $this->parameters;
  }

  /**
   * Get headers.
   *
   * @return array
   */
  public function getHeaders()
  {
    return $this->headers;
  }

  /**
   * Get raw headers.
   *
   * @return array
   */
  public function getRawHeaders()
  {
    $headers = [];

    foreach ($this->headers as $key => $value) {
      $headers[] = $key . ': ' . $value;
    }

    return $headers;
  }

  /**
   * Get body.
   *
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }
}

class WCOAuth
{

  /**
   * OAuth signature method algorithm.
   */
  const HASH_ALGORITHM = 'SHA256';

  /**
   * API endpoint URL.
   *
   * @var string
   */
  protected $url;

  /**
   * Consumer key.
   *
   * @var string
   */
  protected $consumerKey;

  /**
   * Consumer secret.
   *
   * @var string
   */
  protected $consumerSecret;

  /**
   * API version.
   *
   * @var string
   */
  protected $apiVersion;

  /**
   * WCRequest method.
   *
   * @var string
   */
  protected $method;

  /**
   * WCRequest parameters.
   *
   * @var array
   */
  protected $parameters;

  /**
   * Timestamp.
   *
   * @var string
   */
  protected $timestamp;

  /**
   * Initialize oAuth class.
   *
   * @param string $url            Store URL.
   * @param string $consumerKey    Consumer key.
   * @param string $consumerSecret Consumer Secret.
   * @param string $method         WCRequest method.
   * @param string $apiVersion     API version.
   * @param array  $parameters     WCRequest parameters.
   * @param string $timestamp      Timestamp.
   */
  public function __construct(
    $url,
    $consumerKey,
    $consumerSecret,
    $apiVersion,
    $method,
    $parameters = [],
    $timestamp = ''
  ) {
    $this->url            = $url;
    $this->consumerKey    = $consumerKey;
    $this->consumerSecret = $consumerSecret;
    $this->apiVersion     = $apiVersion;
    $this->method         = $method;
    $this->parameters     = $parameters;
    $this->timestamp      = $timestamp;
  }

  /**
   * Encode according to RFC 3986.
   *
   * @param string|array $value Value to be normalized.
   *
   * @return string
   */
  //TODO Rückgbabetyp prüfen
  protected function encode($value)
  {
    if (is_array($value)) {
      return array_map([$this, 'encode'], $value);
    } else {
      return str_replace(['+', '%7E'], [' ', '~'], rawurlencode($value));
    }
  }

  /**
   * Normalize parameters.
   *
   * @param array $parameters Parameters to normalize.
   *
   * @return array
   */
  protected function normalizeParameters($parameters)
  {
    $normalized = [];

    foreach ($parameters as $key => $value) {
      // Percent symbols (%) must be double-encoded.
      $key   = $this->encode($key);
      $value = $this->encode($value);

      $normalized[$key] = $value;
    }

    return $normalized;
  }

  /**
   * Process filters.
   *
   * @param array $parameters WCRequest parameters.
   *
   * @return array
   */
  protected function processFilters($parameters)
  {
    if (isset($parameters['filter'])) {
      $filters = $parameters['filter'];
      unset($parameters['filter']);
      foreach ($filters as $filter => $value) {
        $parameters['filter[' . $filter . ']'] = $value;
      }
    }

    return $parameters;
  }

  /**
   * Get secret.
   *
   * @return string
   */
  protected function getSecret()
  {
    $secret = $this->consumerSecret;

    // Fix secret for v3 or later.
    if (!\in_array($this->apiVersion, ['v1', 'v2'])) {
      $secret .= '&';
    }

    return $secret;
  }

  /**
   * Generate oAuth1.0 signature.
   *
   * @param array $parameters WCRequest parameters including oauth.
   *
   * @return string
   */
  protected function generateOauthSignature($parameters)
  {
    $baseRequestUri = rawurlencode($this->url);

    // Extract filters.
    $parameters = $this->processFilters($parameters);

    // Normalize parameter key/values and sort them.
    $parameters = $this->normalizeParameters($parameters);
    uksort($parameters, 'strcmp');

    // Set query string.
    $queryString  = implode('%26', $this->joinWithEqualsSign($parameters)); // Join with ampersand.
    $stringToSign = $this->method . '&' . $baseRequestUri . '&' . $queryString;
    $secret       = $this->getSecret();

    return base64_encode(hash_hmac(self::HASH_ALGORITHM, $stringToSign, $secret, true));
  }

  /**
   * Creates an array of urlencoded strings out of each array key/value pairs.
   *
   * @param  array  $params      Array of parameters to convert.
   * @param  array  $queryParams Array to extend.
   * @param  string $key         Optional Array key to append
   * @return string              Array of urlencoded strings
   */
  protected function joinWithEqualsSign($params, $queryParams = [], $key = '')
  {
    foreach ($params as $paramKey => $paramValue) {
      if ($key) {
        $paramKey = $key . '%5B' . $paramKey . '%5D'; // Handle multi-dimensional array.
      }

      if (is_array($paramValue)) {
        //TODO Typ prüfen
        $queryParams = $this->joinWithEqualsSign($paramValue, $queryParams, $paramKey);
      } else {
        $string = $paramKey . '=' . $paramValue; // Join with equals sign.
        $queryParams[] = $this->encode($string);
      }
    }

    return $queryParams;
  }

  /**
   * Sort parameters.
   *
   * @param array $parameters Parameters to sort in byte-order.
   *
   * @return array
   */
  protected function getSortedParameters($parameters)
  {
    uksort($parameters, 'strcmp');

    foreach ($parameters as $key => $value) {
      if (is_array($value)) {
        uksort($parameters[$key], 'strcmp');
      }
    }

    return $parameters;
  }

  /**
   * Get oAuth1.0 parameters.
   *
   * @return string
   */
  public function getParameters()
  {
    $parameters = \array_merge($this->parameters, [
      'oauth_consumer_key'     => $this->consumerKey,
      'oauth_timestamp'        => $this->timestamp,
      'oauth_nonce'            => \sha1(\microtime()),
      'oauth_signature_method' => 'HMAC-' . self::HASH_ALGORITHM,
    ]);

    // The parameters above must be included in the signature generation.
    $parameters['oauth_signature'] = $this->generateOauthSignature($parameters);

    //TODO Typ prüfen
    return $this->getSortedParameters($parameters);
  }
}

class WCHttpClientException extends \Exception
{
  /**
   * WCRequest.
   *
   * @var WCRequest
   */
  private $request;

  /**
   * WCResponse.
   *
   * @var WCResponse
   */
  private $response;

  /**
   * Initialize exception.
   *
   * @param string   $message  Error message.
   * @param int      $code     Error code.
   * @param WCRequest  $request  Request data.
   * @param WCResponse $response Response data.
   */
  public function __construct($message, $code, WCRequest $request, WCResponse $response)
  {
    parent::__construct($message, $code);

    $this->request  = $request;
    $this->response = $response;
  }

  /**
   * Get request data.
   *
   * @return WCRequest
   */
  public function getRequest()
  {
    return $this->request;
  }

  /**
   * Get response data.
   *
   * @return WCResponse
   */
  public function getResponse()
  {
    return $this->response;
  }
}

class WCHttpClient
{

  /**
   * cURL handle.
   *
   * @var resource
   */
  protected $ch;

  /**
   * Store API URL.
   *
   * @var string
   */
  protected $url;

  /**
   * Consumer key.
   *
   * @var string
   */
  protected $consumerKey;

  /**
   * Consumer secret.
   *
   * @var string
   */
  protected $consumerSecret;

  /**
   * WCClient options.
   *
   * @var WCOptions
   */
  protected $options;

  /**
   * WCRequest.
   *
   * @var WCRequest
   */
  private $request;

  /**
   * WCResponse.
   *
   * @var WCResponse
   */
  private $response;

  /**
   * WCResponse headers.
   *
   * @var string
   */
  private $responseHeaders;

  /**
   * Initialize HTTP client.
   *
   * @param string $url            Store URL.
   * @param string $consumerKey    Consumer key.
   * @param string $consumerSecret Consumer Secret.
   * @param array  $options        WCClient options.
   *
   * @throws WCHttpClientException
   */
  public function __construct($url, $consumerKey, $consumerSecret, $options)
  {
    if (!function_exists('curl_version')) {
      throw new WCHttpClientException('cURL is NOT installed on this server', -1, new WCRequest(), new WCResponse());
    }

    $this->options        = new WCOptions($options);
    $this->url            = $this->buildApiUrl($url);
    $this->consumerKey    = $consumerKey;
    $this->consumerSecret = $consumerSecret;
  }

  /**
   * Check if is under SSL.
   *
   * @return bool
   */
  protected function isSsl()
  {
    return strpos($this->url,'https://')===0;

  }

  /**
   * Build API URL.
   *
   * @param string $url Store URL.
   *
   * @return string
   */
  protected function buildApiUrl($url)
  {
    $api = $this->options->isWPAPI() ? $this->options->apiPrefix() : '/wc-api/';

    return rtrim($url, '/') . $api . $this->options->getVersion() . '/';
  }

  /**
   * Build URL.
   *
   * @param string $url        URL.
   * @param array  $parameters Query string parameters.
   *
   * @return string
   */
  protected function buildUrlQuery($url, $parameters = [])
  {
    if (!empty($parameters)) {
      $url .= '?' . http_build_query($parameters);
    }

    return $url;
  }

  /**
   * Authenticate.
   *
   * @param string $url        WCRequest URL.
   * @param string $method     WCRequest method.
   * @param array  $parameters WCRequest parameters.
   *
   * @return array
   */
  protected function authenticate($url, $method, $parameters = [])
  {
    // Setup authentication.
    if ($this->isSsl()) {
      $basicAuth  = new WCBasicAuth(
        $this->ch,
        $this->consumerKey,
        $this->consumerSecret,
        $this->options->isQueryStringAuth(),
        $parameters
      );
      $parameters = $basicAuth->getParameters();
    } else {
      $oAuth      = new WCOAuth(
        $url,
        $this->consumerKey,
        $this->consumerSecret,
        $this->options->getVersion(),
        $method,
        $parameters,
        $this->options->oauthTimestamp()
      );
      //TODO Typ prüfen
      $parameters = $oAuth->getParameters();
    }

    return $parameters;
  }

  /**
   * Setup method.
   *
   * @param string $method WCRequest method.
   */
  protected function setupMethod($method)
  {
    if ('POST' === $method) {
      curl_setopt($this->ch, CURLOPT_POST, true);
    } elseif (in_array($method, ['PUT', 'DELETE', 'OPTIONS'])) {
      curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
    }
  }

  /**
   * Get request headers.
   *
   * @param  bool $sendData If request send data or not.
   *
   * @return array
   */
  protected function getRequestHeaders($sendData = false)
  {
    $headers = [
      'Accept'     => 'application/json',
      'User-Agent' => $this->options->userAgent() . '/' . WCClient::VERSION,
    ];

    if ($sendData) {
      $headers['Content-Type'] = 'application/json;charset=utf-8';
    }

    return $headers;
  }

  /**
   * Create request.
   *
   * @param string $endpoint   WCRequest endpoint.
   * @param string $method     WCRequest method.
   * @param array  $data       WCRequest data.
   * @param array  $parameters WCRequest parameters.
   *
   * @return WCRequest
   */
  protected function createRequest($endpoint, $method, $data = [], $parameters = [])
  {
    $body    = '';
    $url     = $this->url . $endpoint;
    $hasData = !empty($data);

    // Setup authentication.
    $parameters = $this->authenticate($url, $method, $parameters);

    // Setup method.
    $this->setupMethod($method);

    // Include post fields.
    if ($hasData) {
      $body = json_encode($data);
      curl_setopt($this->ch, CURLOPT_POSTFIELDS, $body);
    }

    $this->request = new WCRequest(
      $this->buildUrlQuery($url, $parameters),
      $method,
      $parameters,
      $this->getRequestHeaders($hasData),
      $body
    );

    return $this->getRequest();
  }

  /**
   * Get response headers.
   *
   * @return array
   */
  protected function getResponseHeaders()
  {
    $headers = [];
    $lines   = explode("\n", $this->responseHeaders);
    $lines   = array_filter($lines, 'trim');

    foreach ($lines as $index => $line) {
      // Remove HTTP/xxx params.
      if (strpos($line, ': ') === false) {
        continue;
      }

      list($key, $value) = explode(': ', $line);

      $headers[$key] = isset($headers[$key]) ? $headers[$key] . ', ' . trim($value) : trim($value);
    }

    return $headers;
  }

  /**
   * Create response.
   *
   * @return WCResponse
   */
  protected function createResponse()
  {

    // Set response headers.
    $this->responseHeaders = '';
    curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, function ($_, $headers) {
      $this->responseHeaders .= $headers;
      return strlen($headers);
    });

    // Get response data.
    $body    = curl_exec($this->ch);
    $code    = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    $headers = $this->getResponseHeaders();

    // Register response.
    $this->response = new WCResponse($code, $headers, $body);

    return $this->getResponse();
  }

  /**
   * Set default cURL settings.
   */
  protected function setDefaultCurlSettings()
  {
    $verifySsl       = $this->options->verifySsl();
    $timeout         = $this->options->getTimeout();
    $followRedirects = $this->options->getFollowRedirects();

    curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $verifySsl);
    if (!$verifySsl) {
      curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, $verifySsl);
    }
    if ($followRedirects) {
      curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
    }
    curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->request->getRawHeaders());
    curl_setopt($this->ch, CURLOPT_URL, $this->request->getUrl());
  }

  /**
   * Look for errors in the request.
   *
   * @throws WCHttpClientException
   *
   * @param array $parsedResponse Parsed body response.
   */
  protected function lookForErrors($parsedResponse)
  {
    // Any non-200/201/202 response code indicates an error.
    if (!in_array($this->response->getCode(), ['200', '201', '202'])) {
      $errors = isset($parsedResponse->errors) ? $parsedResponse->errors : $parsedResponse;
      $errorMessage = '';
      $errorCode = '';

      if (is_array($errors)) {
        $errorMessage = $errors[0]->message;
        $errorCode    = $errors[0]->code;
      } elseif (isset($errors->message, $errors->code)) {
        $errorMessage = $errors->message;
        $errorCode    = $errors->code;
      }

      throw new WCHttpClientException(
        sprintf('Error: %s [%s]', $errorMessage, $errorCode),
        $this->response->getCode(),
        $this->request,
        $this->response
      );
    }
  }

  /**
   * Process response.
   *
   * @throws WCHttpClientException
   * @return array
   */

  protected function processResponse()
  {
    $body = $this->response->getBody();

    // Look for UTF-8 BOM and remove.
    if (0 === strpos(bin2hex(substr($body, 0, 4)), 'efbbbf')) {
      $body = substr($body, 3);
    }

    $parsedResponse = json_decode($body);

    // Test if return a valid JSON.
    if (JSON_ERROR_NONE !== json_last_error()) {
      $message = function_exists('json_last_error_msg') ? json_last_error_msg() : 'Invalid JSON returned';
      throw new WCHttpClientException(
        sprintf('JSON ERROR: %s', $message),
        $this->response->getCode(),
        $this->request,
        $this->response
      );
    }

    $this->lookForErrors($parsedResponse);

    return $parsedResponse;
  }

  /**
   * Make requests.
   *
   * @param string $endpoint   WCRequest endpoint.
   * @param string $method     WCRequest method.
   * @param array  $data       WCRequest data.
   * @param array  $parameters WCRequest parameters.
   *
   * @throws WCHttpClientException
   *
   * @return array
   */
  public function request($endpoint, $method, $data = [], $parameters = [])
  {



    // Initialize cURL.
    $this->ch = curl_init();

    // Set request args.
    $request = $this->createRequest($endpoint, $method, $data, $parameters);

    // Default cURL settings.
    $this->setDefaultCurlSettings();

    // Get response.
    $response = $this->createResponse();


    // Check for cURL errors.
    if (curl_errno($this->ch)) {
      throw new WCHttpClientException('cURL Error: ' . \curl_error($this->ch), 0, $request, $response);
    }

    curl_close($this->ch);

    return $this->processResponse();
  }

  /**
   * Get request data.
   *
   * @return WCRequest
   */
  public function getRequest()
  {
    return $this->request;
  }

  /**
   * Get response data.
   *
   * @return WCResponse
   */
  public function getResponse()
  {
    return $this->response;
  }
}

class WCBasicAuth
{
  /**
   * cURL handle.
   *
   * @var resource
   */
  protected $ch;

  /**
   * Consumer key.
   *
   * @var string
   */
  protected $consumerKey;

  /**
   * Consumer secret.
   *
   * @var string
   */
  protected $consumerSecret;

  /**
   * Do query string auth.
   *
   * @var bool
   */
  protected $doQueryString;

  /**
   * WCRequest parameters.
   *
   * @var array
   */
  protected $parameters;

  /**
   * Initialize Basic Authentication class.
   *
   * @param resource $ch             cURL handle.
   * @param string   $consumerKey    Consumer key.
   * @param string   $consumerSecret Consumer Secret.
   * @param bool     $doQueryString  Do or not query string auth.
   * @param array    $parameters     WCRequest parameters.
   */
  public function __construct($ch, $consumerKey, $consumerSecret, $doQueryString, $parameters = [])
  {
    $this->ch             = $ch;
    $this->consumerKey    = $consumerKey;
    $this->consumerSecret = $consumerSecret;
    $this->doQueryString  = $doQueryString;
    $this->parameters     = $parameters;

    $this->processAuth();
  }

  /**
   * Process auth.
   */
  protected function processAuth()
  {
    if ($this->doQueryString) {
      $this->parameters['consumer_key']    = $this->consumerKey;
      $this->parameters['consumer_secret'] = $this->consumerSecret;
    } else {
      \curl_setopt($this->ch, CURLOPT_USERPWD, $this->consumerKey . ':' . $this->consumerSecret);
    }
  }

  /**
   * Get parameters.
   *
   * @return array
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}
