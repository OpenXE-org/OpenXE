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
use Xentral\Modules\Onlineshop\Data\ArticleExportResult;
use Xentral\Modules\Onlineshop\Data\OrderStatus;
use Xentral\Modules\Onlineshop\Data\OrderStatusUpdateRequest;
use Xentral\Components\Logger\Logger;
use Xentral\Components\WooCommerce\ClientWrapper;
use Automattic\WooCommerce\HttpClient\HttpClientException;

class Shopimporter_Woocommerce extends ShopimporterBase
{
  // protected $canexport = false;

  public $intern = false;
  public $shopid;
  public $data;

  /**
   * @var ClientWrapper $client
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

  /** @var Logger $logger */
  public $logger;

  /** @var bool $ssl_ignore Whether to ignore SSL certificate validation */
  public $ssl_ignore;

  /** @var string $lastImportTimestamp ISO-8601 UTC timestamp of the last successful import */
  public $lastImportTimestamp;

  /** @var bool $lastImportTimestampIsFallback True when lastImportTimestamp was computed as 30-day fallback */
  public $lastImportTimestampIsFallback = false;

  /** @var int[] $lastImportOrderIds WooCommerce order IDs within the current timestamp bucket */
  public $lastImportOrderIds = [];

  public function __construct($app, $intern = false)
  {
    $this->app = $app;
    $this->intern = true;
    $this->logger = $app->Container->get('Logger');
  }

  public function ImportList()
  {
    $msg = $this->app->erp->base64_url_encode('<div class="info">Sie k&ouml;nnen hier die Shops einstellen</div>');
    header('Location: index.php?module=onlineshops&action=list&msg=' . $msg);
    exit;
  }

  /**
   * Returns the total number of orders pending import since the last import
   * timestamp. Uses the WC v3 after= parameter and reads the count from
   * the X-WP-Total response header (per_page=1 to minimise payload).
   *
   * @return int
   */
  public function ImportGetAuftraegeAnzahl()
  {
    $this->migrateAbNummerIfNeeded();

    $configuredStatuses = array_map('trim', explode(';', (string) $this->statusPending));

    if (!empty($this->lastImportOrderIds)) {
      $afterTs = gmdate('Y-m-d\TH:i:s', max(0, strtotime($this->lastImportTimestamp) - 1));
      $queryArgs = [
        'status'   => $configuredStatuses,
        'after'    => $afterTs,
        'per_page' => 1,
        'exclude'  => array_values($this->lastImportOrderIds),
      ];
    } else {
      $queryArgs = [
        'status'   => $configuredStatuses,
        'after'    => $this->lastImportTimestamp,
        'per_page' => 1,
      ];
    }

    try {
      $this->client->get('orders', $queryArgs);
    } catch (Exception $e) {
      $this->logger->warning('WooCommerce ImportGetAuftraegeAnzahl: API request failed: ' . $e->getMessage());
      return 0;
    }

    $wcResponse = $this->client->getLastResponse();
    if ($wcResponse === null) {
      $this->logger->warning('WooCommerce ImportGetAuftraegeAnzahl: getLastResponse() returned null');
      return 0;
    }

    $total = $wcResponse->getHeader('x-wp-total');
    if ($total === null) {
      $this->logger->warning('WooCommerce ImportGetAuftraegeAnzahl: X-WP-Total header missing');
      return 0;
    }

    return (int) $total;
  }

  /**
   * Queries the WooCommerce API for the oldest pending order since the last
   * import timestamp and returns it as a Xentral-formatted array with at most
   * one element. The caller (shopimport.php::RemoteGetAuftrag loop) expects
   * $result[0] per iteration; this contract must be maintained.
   *
   * The after-filter advances per order so each caller-iteration fetches the
   * next order. A crash between RemoteGetAuftrag() and the shopimport_auftraege
   * INSERT loses at most this one order (consistent with pre-#262 behaviour).
   *
   * @return array Array with at most one order entry, or empty array if none.
   */
  public function ImportGetAuftrag()
  {
    $data = $this->CatchRemoteCommand('data');

    $this->migrateAbNummerIfNeeded();

    $configuredStatuses = array_map('trim', explode(';', (string) $this->statusPending));

    if (!empty($this->lastImportOrderIds)) {
      $afterTs = gmdate('Y-m-d\TH:i:s', max(0, strtotime($this->lastImportTimestamp) - 1));
      $queryArgs = [
        'status'   => $configuredStatuses,
        'after'    => $afterTs,
        'per_page' => 1,
        'page'     => 1,
        'orderby'  => 'date',
        'order'    => 'asc',
        'exclude'  => array_values($this->lastImportOrderIds),
      ];
    } else {
      $queryArgs = [
        'status'   => $configuredStatuses,
        'after'    => $this->lastImportTimestamp,
        'per_page' => 1,
        'page'     => 1,
        'orderby'  => 'date',
        'order'    => 'asc',
      ];
    }

    try {
      $pageOrders = $this->client->get('orders', $queryArgs);
    } catch (Exception $e) {
      $this->logger->warning('WooCommerce ImportGetAuftrag: ' . $e->getMessage());
      return null;
    }

    if (empty($pageOrders)) {
      return null;
    }

    $wcOrder = $pageOrders[0] ?? null;
    if ($wcOrder === null) {
      return null;
    }

    $order = $this->parseOrder($wcOrder);

    // Persist tuple cursor so the next Caller-iteration advances past this order.
    // Using ts-1s in the query means same-second peers are still fetched, while
    // the exclude=[id] parameter prevents re-delivering this exact order.
    if (!empty($wcOrder->date_created_gmt) && !empty($wcOrder->id)) {
      $this->persistLastImportCursor((string) $wcOrder->date_created_gmt, (int) $wcOrder->id);
    }

    return [[
      'id'        => $order['auftrag'],
      'sessionid' => '',
      'logdatei'  => '',
      'warenkorb' => base64_encode(serialize($order)),
    ]];
  }

  /**
   * Resolves a legacy WooCommerce order ID (ab_nummer) to the order's
   * date_created_gmt timestamp for the one-shot transition from cursor-
   * based to timestamp-based import.
   *
   * @param int $abNummer WooCommerce order ID
   * @return string|null ISO-8601 UTC timestamp or null on failure
   */
  private function resolveAbNummerToTimestamp($abNummer)
  {
    try {
      $order = $this->client->get('orders/' . $abNummer);
    } catch (Exception $e) {
      $this->logger->warning('WooCommerce resolveAbNummerToTimestamp(' . $abNummer . '): ' . $e->getMessage());
      return null;
    }

    if (empty($order->date_created_gmt)) {
      $this->logger->warning('WooCommerce resolveAbNummerToTimestamp(' . $abNummer . '): date_created_gmt missing');
      return null;
    }

    $ts = strtotime((string) $order->date_created_gmt);
    if ($ts === false) {
      return null;
    }
    return gmdate('Y-m-d\TH:i:s', $ts - 1);
  }

  /**
   * Runs the one-shot legacy ab_nummer -> timestamp migration when the stored
   * cursor is still the 30-day fallback and the caller passes an ab_nummer.
   * Idempotent: once migrated, lastImportTimestampIsFallback flips to false
   * and subsequent calls become no-ops.
   */
  private function migrateAbNummerIfNeeded()
  {
    if (!$this->lastImportTimestampIsFallback) {
      return;
    }
    $data = $this->CatchRemoteCommand('data');
    if (empty($data['ab_nummer'])) {
      return;
    }
    $resolved = $this->resolveAbNummerToTimestamp((int) $data['ab_nummer']);
    if ($resolved !== null) {
      $this->persistLastImportTimestamp($resolved);
      return;
    }
    // Resolution failed (order deleted, 404, missing date_created_gmt). Persist the
    // current 30-day fallback so subsequent runs use a stable lower bound
    // instead of sliding the window on every cron cycle.
    $this->logger->warning(
      sprintf(
        'WooCommerce ab_nummer=%d konnte nicht aufgeloest werden; persistiere 30-Tage-Fallback als Cursor',
        (int) $data['ab_nummer']
      )
    );
    $this->persistLastImportTimestamp($this->lastImportTimestamp);
  }

  // This function searches the wcOrder for the specified WC Meta key
  // and returns it if found, null otherise
  public function get_wc_meta($wcOrder, $meta_key)
  {
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
  public function parseOrder($wcOrder)
  {


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
      [
        'first_name',
        'second_name',
        'company',
        'address_1',
        'address_2',
        'city',
        'state',
        'postcode',
        'country'
      ]
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

    if (!empty($wcOrder->subshop)) {
      $order['subshop'] = $wcOrder->subshop;
    }

    // General order properties and billing address
    $order['auftrag'] = $wcOrder->id;
    $order['order'] = json_decode(json_encode($wcOrder), true);
    $order['strasse'] = $wcOrder->billing->address_1;
    if (!empty($wcOrder->billing->address_2)) {
      $order['adresszusatz'] = $wcOrder->billing->address_2;
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

    if (!empty((string) $wcOrder->currency)) {
      $warenkorb['waehrung'] = (string) $wcOrder->currency;
    }
    //

    //
    // Coupon Codes
    //
    $discount_total = (float) $wcOrder->discount_total;
    $discount_tax = (float) $wcOrder->discount_tax;

    if ($discount_total != 0) { // Discount was applied to this order

      // Calculate coupon amount
      if ($discount_tax == 0) {
        // Tax calculations are not enabled for any used coupon
        $order['rabattnetto'] = -abs((float) $discount_total);
      } else {
        // At least one used coupon has tax calculations enabled
        $order['rabattbrutto'] = -abs((float) $discount_total);
        $order['rabattbrutto'] += -abs((float) $discount_tax);
      }

      // Set coupon name
      $couponLine = $wcOrder->coupon_lines;

      // Check if we have a valid coupon line just to be sure and set the coupon name
      if ($couponLine && is_array($couponLine) && isset($couponLine[0]) && (String) $couponLine[0]->code !== '') {
        $order['rabattname'] = (String) $couponLine[0]->code;
      }

    }



    $seperateShippingAddress = !self::compareObjects(
      $wcOrder->billing,
      $wcOrder->shipping,
      [
        'first_name',
        'second_name',
        'company',
        'address_1',
        'address_2',
        'city',
        'state',
        'postcode',
        'country'
      ]
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
      if (!empty($wcOrder->shipping->address_2)) {
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

    foreach ($wcOrder->line_items as $wcOrderItem) {
      $order['articlelist'][] = $this->parseItem($wcOrderItem);
    }

    $order['zahlungsweise'] = $wcOrder->payment_method;
    $order['lieferung'] = $wcOrder->shipping_lines[0]->method_id;

    return $order;
  }

  function parseItem($wcOrderItem)
  {
    // The WC API doesnt expose the net price of a single product in the get order endpoint.
    // We could query each individual product and get the price, but that would result in a
    // huge amount of HTTP requests.
    //
    // We could use the price_netto attribute in the order item, but we get a higher precision using
    // this custom calculation as we have access to the exact `total_tax` amount.
    // Passing the net value of the line eliminates rounding issues if the position happens to have large quantites

    switch ($this->priceType) {
      case 'grosscalculated':
        $priceValue = ((float) $wcOrderItem->subtotal + (float) $wcOrderItem->subtotal_tax) / (float) $wcOrderItem->quantity;
        $priceType = 'price';
        break;
      case 'netcalculated':
      default:
        $priceType = 'price_netto';
        $priceValue = (float) $wcOrderItem->subtotal / (float) $wcOrderItem->quantity;
        break;
    }

    $orderItem = array();
    $orderItem['articleid'] = $wcOrderItem->sku;
    $orderItem['name'] = $wcOrderItem->name;
    $orderItem[$priceType] = $priceValue;
    $orderItem['quantity'] = $wcOrderItem->quantity;

    // The item could be a variable product in which case we have to retrieve the sku of the variation product
    if (!empty($wcOrderItem->variation_id)) {
      $variation_product_sku = $this->getSKUByShopId($wcOrderItem->product_id, $wcOrderItem->variation_id);
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
      $this->client->put('orders/' . $orderId, [
        'status' => $this->statusProcessing,
      ]);
    }

    return 'ok';
  }

  /**
   * Updates the order status once payment and shipping are set to ok.
   * Also updates the order with the shipping tracking code
   * @return string
   * @throws HttpClientException
   */
  public function ImportUpdateAuftrag()
  {
    /** @var OrderStatusUpdateRequest $data */
    $data = $this->CatchRemoteCommand('data');

    if ($data->orderStatus !== OrderStatus::Completed)
      return;

    if (isset($data->shipments)) {
      $trackingCode = $data->shipments[0]?->trackingNumber;
    }

    if (!empty($trackingCode)) {
      $this->client->post('orders/' . $data->shopOrderId . '/notes', [
        'note' => 'Tracking Code: ' . $trackingCode
      ]);

      $this->logger->info(
        "WooCommerce Tracking Code Rückmeldung für Auftrag: " . $data->orderId,
        [
          'orderId' => $data->shopOrderId,
          'trackingCode' => $trackingCode
        ]
      );
    }

    $updateData = [
      'status' => $this->statusCompleted,
      'meta_data' => [
        [
          'key' => 'tracking_code',
          'value' => $trackingCode
        ],
        [
          'key' => 'shipping_carrier',
          'value' => $data->shipments[0]?->shippingMethod
        ]
      ],
    ];
    $this->client->put('orders/' . $data->shopOrderId, $updateData);

    $this->logger->info(
      "WooCommerce Statusrückmeldung 'completed' für Auftrag: " . $data->orderId,
      [
        'orderId' => $data->shopOrderId,
        'status' => $this->statusCompleted
      ]
    );
    return 'ok';
  }

  /**
   * This function syncs the current stock to the remote WooCommerce shop.
   * Uses WC REST v3 batch endpoints to reduce HTTP round-trips from 2n to
   * roughly ceil(n/100) + ceil(n/100) requests.
   *
   * @return int Number of articles successfully synced
   * @throws HttpClientException
   */
  public function ImportSendListLager()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $anzahl = 0;
    $ctmp = (!empty($tmp) ? count($tmp) : 0);

    // --- Step 1: Collect all SKUs and compute desired stock params ---

    // $pendingUpdates: sku => ['lageranzahl' => int, 'status' => string]
    $pendingUpdates = [];

    for ($i = 0; $i < $ctmp; $i++) {
      $artikel = $tmp[$i]['artikel'];
      if ($artikel === 'ignore') {
        continue;
      }
      $nummer = $tmp[$i]['nummer'];
      if (!empty($tmp[$i]['artikelnummer_fremdnummern'][0]['nummer'])) {
        $nummer = $tmp[$i]['artikelnummer_fremdnummern'][0]['nummer'];
      }
      $lageranzahl = $tmp[$i]['anzahl_lager'];
      $pseudolager = trim($tmp[$i]['pseudolager']);
      $inaktiv = $tmp[$i]['inaktiv'];
      $status = 'publish';

      if ($pseudolager !== '') {
        $lageranzahl = $pseudolager;
      }
      if ($tmp[$i]['ausverkauft']) {
        $lageranzahl = 0;
      }
      if ($inaktiv) {
        $status = 'private';
      }

      $pendingUpdates[$nummer] = [
        'lageranzahl' => $lageranzahl,
        'status' => $status,
      ];
    }

    if (empty($pendingUpdates)) {
      return 0;
    }

    // --- Step 2: Bulk-resolve SKUs to WC product IDs ---
    // WC REST v3 accepts a comma-separated list in the ?sku= parameter.
    // We fetch in chunks of 100 to stay within per_page limits.

    // $skuMap: sku => ['id' => int, 'parent' => int, 'isvariant' => bool]
    $skuMap = [];
    $skuChunks = array_chunk(array_keys($pendingUpdates), 100);

    foreach ($skuChunks as $skuChunk) {
      $skuCsv = implode(',', $skuChunk);
      try {
        $products = $this->client->get('products', [
          'sku' => $skuCsv,
          'per_page' => 100,
        ]);
      } catch (Exception $e) {
        $this->logger->error(
          'WooCommerce SKU-Lookup-Chunk fehlgeschlagen: ' . $e->getMessage(),
          ['chunk_size' => count($skuChunk)]
        );
        continue;
      }
      if (!is_array($products)) {
        continue;
      }
      foreach ($products as $product) {
        if (!isset($product->sku)) {
          continue;
        }
        $skuMap[$product->sku] = [
          'id' => $product->id,
          'parent' => $product->parent_id,
          'isvariant' => !empty($product->parent_id),
        ];
      }
    }

    // --- Step 3: Split into simple products and variations ---
    // simpleItems: list of batch-update items for POST products/batch
    // variationItems: parent_id => list of batch-update items for POST products/{parent}/variations/batch

    $simpleItems = [];
    $variationItems = [];

    foreach ($pendingUpdates as $sku => $params) {
      if (!isset($skuMap[$sku])) {
        $this->logger->error(
          "WooCommerce Artikel $sku wurde im Online-Shop nicht gefunden! Falsche Artikelnummer im Shop hinterlegt?"
        );
        continue;
      }

      $info = $skuMap[$sku];
      $item = [
        'id' => $info['id'],
        'manage_stock' => true,
        'stock_quantity' => $params['lageranzahl'],
        'status' => $params['status'],
      ];

      if ($info['isvariant']) {
        $variationItems[$info['parent']][] = $item;
      } else {
        $simpleItems[] = $item;
      }
    }

    // --- Step 4: Send batch updates in chunks of 100, handle partial errors ---

    // Simple products
    foreach (array_chunk($simpleItems, 100) as $chunk) {
      try {
        $response = $this->client->post('products/batch', ['update' => $chunk]);
        $anzahl += $this->processBatchResponse($response, 'products/batch');
      } catch (Exception $e) {
        $this->logger->error('WooCommerce Batch-Request fehlgeschlagen fuer products/batch: ' . $e->getMessage());
      }
    }

    // Variations (one batch endpoint per parent product)
    foreach ($variationItems as $parentId => $items) {
      foreach (array_chunk($items, 100) as $chunk) {
        $endpoint = 'products/' . $parentId . '/variations/batch';
        try {
          $response = $this->client->post($endpoint, ['update' => $chunk]);
          $anzahl += $this->processBatchResponse($response, $endpoint);
        } catch (Exception $e) {
          $this->logger->error('WooCommerce Batch-Request fehlgeschlagen fuer ' . $endpoint . ': ' . $e->getMessage());
        }
      }
    }

    return $anzahl;
  }

  /**
   * Evaluates a WC batch response object, logs per-item errors, and returns
   * the count of successfully updated items.
   *
   * @param object $response Decoded JSON response from the batch endpoint.
   * @param string $endpoint Endpoint label used in log messages.
   * @return int Number of items reported as updated without error.
   */
  private function processBatchResponse($response, $endpoint)
  {
    $successCount = 0;

    if (!is_object($response) && !is_array($response)) {
      $this->logger->error("WooCommerce Batch-Response ungueltig fuer $endpoint");
      return 0;
    }

    // Successful updates are in response->update
    $updated = is_object($response) ? ($response->update ?? []) : [];
    foreach ($updated as $item) {
      // WC embeds per-item errors inside the update array when an item fails
      if (isset($item->error)) {
        $code = $item->error->code ?? '';
        $message = $item->error->message ?? '';
        $this->logger->error(
          "WooCommerce Batch-Fehler ($endpoint) fuer ID {$item->id}: [$code] $message"
        );
      } else {
        $this->logger->info(
          "WooCommerce Lagerzahlenübertragung (Batch) fuer Artikel-ID {$item->id} erfolgreich",
          ['endpoint' => $endpoint]
        );
        $successCount++;
      }
    }

    // Top-level errors array (some WC versions use this)
    $errors = is_object($response) ? ($response->errors ?? []) : [];
    foreach ($errors as $err) {
      $code = $err->code ?? '';
      $message = $err->message ?? '';
      $this->logger->error(
        "WooCommerce Batch-Fehler ($endpoint): [$code] $message"
      );
    }

    return $successCount;
  }

  public function ImportStorniereAuftrag()
  {
    $orderId = $this->CatchRemoteCommand('data')['auftrag'];
    if (!empty($orderId)) {
      $this->client->put('orders/' . $orderId, [
        'status' => 'cancelled',
      ]);
    } else {
      return 'failed';
    }
    return 'ok';
  }

  public function ImportSendList()
  {
    $tmp = $this->catchRemoteCommand('data');
    $anzahl = 0;
    $return = [];
    for ($i = 0; $i < (!empty($tmp) ? count($tmp) : 0); $i++) {
      $return[$i] = new ArticleExportResult();
      $artikel = $tmp[$i]['artikel'];
      $return[$i]->articleId = intval($artikel);
      $nummer = $tmp[$i]['nummer'];
      if (!empty($tmp[$i]['artikelnummer_fremdnummern'][0]['nummer'])) {
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
      if ($pseudopreis <= $preis)
        $pseudopreis = $preis;
      $steuersatz = $tmp[$i]['steuersatz'];
      if ($steuersatz > 1.10) {
        $steuersatz = 'normal';
      } else {
        $steuersatz = 'ermaessigt';
      }

      $lageranzahl = $tmp[$i]['anzahl_lager'];
      $pseudolager = trim($tmp[$i]['pseudolager']);

      if ($pseudolager > 0)
        $lageranzahl = $pseudolager;
      if ($tmp[$i]['ausverkauft'] == "1") {
        $lageranzahl = 0;
        $laststock = "1";
      }

      if ($inaktiv)
        $aktiv = 0;
      else
        $aktiv = 1;

      $product_id = 0;
      if ($laststock != "1")
        $laststock = 0;

      $remoteIdInformation = $this->getShopIdBySKU($nummer);
      $product_id = $remoteIdInformation['id'] ?? null;
      $parent_id = $remoteIdInformation['parent'] ?? null;
      $isVariant = $remoteIdInformation['isvariant'] ?? false;

      $commonMetaData = [
        ['key' => '_yoast_wpseo_metadesc', 'value' => $meta_desc],
        ['key' => '_yoast_wpseo_title', 'value' => $meta_title],
      ];


      // Attributes that are used for both updating an existing product as well as creating a new one
      $commonProductAtts = [
        'name' => $name_de,
        'description' => $description,
        'status' => ($aktiv ? 'publish' : 'private'),
        'regular_price' => number_format($pseudopreis, 2, '.', ''),
        'sale_price' => number_format($preis, 2, '.', ''),
        'short_description' => $kurzbeschreibung,
        'weight' => $weight_kg,
        'dimensions' => [
          'length' => $dim_length,
          'width' => $dim_width,
          'height' => $dim_height
        ],
        'meta_data' => $commonMetaData,
      ];

      if ($lageranzahl === 0) {
        $commonProductAtts['stock_status'] = 'outofstock';
        $commonProductAtts['manage_stock'] = true;
      } elseif ($lageranzahl === '') {
        $commonProductAtts['stock_status'] = 'instock';
        $commonProductAtts['manage_stock'] = false;
      } else {
        $commonProductAtts['stock_status'] = 'instock';
        $commonProductAtts['manage_stock'] = true;
      }

      if ($lageranzahl !== '') {
        $commonProductAtts['stock_quantity'] = (int) $lageranzahl;
      }

      if (!is_null($product_id)) {
        // Product exists - check if it's a variation or regular product
        if ($isVariant && !empty($parent_id)) {
          // This is a VARIATION - use the variations endpoint
          // Variations don't support certain attributes (they inherit from parent)
          $variationAtts = [
            'regular_price' => $commonProductAtts['regular_price'],
            'sale_price' => $commonProductAtts['sale_price'],
            'weight' => $commonProductAtts['weight'],
            'dimensions' => $commonProductAtts['dimensions'],
            'stock_status' => $commonProductAtts['stock_status'],
            'manage_stock' => $commonProductAtts['manage_stock'],
          ];
          if (isset($commonProductAtts['stock_quantity'])) {
            $variationAtts['stock_quantity'] = $commonProductAtts['stock_quantity'];
          }
          // Update status: 'publish' for variations is handled differently
          if (!$aktiv) {
            $variationAtts['status'] = 'private';
          }

          $this->client->put('products/' . $parent_id . '/variations/' . $product_id, $variationAtts);

          $this->logger->info("WooCommerce Variante geändert für Artikel: $nummer / Variation: $product_id (Parent: $parent_id)");
        } else {
          // This is a regular product
          $this->client->put('products/' . $product_id, array_merge([

          ], $commonProductAtts));

          $this->logger->info("WooCommerce Artikel geändert für Artikel: $nummer / $product_id");
        }
      } else {
        // create a new product
        $product_id = $this->client->post('products/', array_merge([
          'sku' => $nummer,
        ], $commonProductAtts))->id;
        $this->logger->info("WooCommerce neuer Artikel angelegt: $nummer");
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
      if (isset($tmp[$i]['kategorien']) || isset($tmp[$i]['kategoriename'])) {
        $kategorien = $tmp[$i]['kategorien'];
        if (!($kategorien) && !self::emptyString($tmp[$i]['kategoriename'])) {
          $kategorien = array(
            array(
              'name' => $tmp[$i]['kategoriename'],
            )
          );
        }
        if ((!empty($kategorien) ? count($kategorien) : 0) > 0) {
          // Retrive all WC categories via API
          $allWooCommerceCategories = $this->client->get('products/categories', ['per_page' => '100']);

          $searchWpCategories = [];
          foreach ($allWooCommerceCategories as $a) {
            $searchWpCategories[$a->id] = $a->name;
          }
          // searchWPCategories is an assoc array of type WCCatId(Int) -> WCCatName(string)

          // Iterate over the categories that are choosen in xentral
          foreach ($kategorien as $k => $v) {
            $wawi_cat_name = $v['name'];

            $wcCatId = null;

            // If WC has a matching category. We match based on name!
            if (array_search($wawi_cat_name, array_values($searchWpCategories)) !== false) {
              // get  id of that WC Category
              $wcCatId = array_search($wawi_cat_name, $searchWpCategories);

            } else {
              // No matching category exists
              $wcCatId = $this->client->post('products/categories', [
                'name' => $wawi_cat_name,
              ])->id;

            }

            if ($wcCatId) {
              // update category. We first retrieve the product and append the new product category, not replace the entire category array.
              $alreadyAssignedWCCats = $this->client->get('products/' . $product_id, [
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
              foreach ($allCatIds as $id) {
                $allCatIdsWCAPIRep[] = ['id' => $id];
              }

              // Update category assignment
              $this->client->put('products/' . $product_id, [
                'categories' => $allCatIdsWCAPIRep,
              ]);

              $chosenCats[] = $wcCatId;
            }
          }
        }
      }

      $return[$i]->success = true;
    }

    return $return;
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
   * @throws HttpClientException
   */
  public function getKonfig($shopid, $data)
  {
    $this->shopid = $shopid;
    $this->data = $data;

    $preferences_json = $this->app->DB->Select("SELECT einstellungen_json FROM shopexport WHERE id = '$this->shopid' LIMIT 1");
    $preferences = [];
    if ($preferences_json) {
      $preferences = json_decode($preferences_json, true) ?? [];
    }

    $felder = $preferences['felder'] ?? [];

    $this->protokoll = $felder['protokoll'] ?? null;
    $this->ssl_ignore = $felder['ssl_ignore'] ?? false;
    $ImportWooCommerceApiSecret = $felder['ImportWoocommerceApiSecret'] ?? '';
    $ImportWooCommerceApiKey = $felder['ImportWoocommerceApiKey'] ?? '';
    $ImportWooCommerceApiUrl = $felder['ImportWoocommerceApiUrl'] ?? '';

    $this->statusPending = $felder['statusPending'] ?? 'pending';
    $this->statusProcessing = $felder['statusProcessing'] ?? 'processing';
    $this->statusCompleted = $felder['statusCompleted'] ?? 'completed';

    $this->priceType = $felder['priceType'] ?? null;

    $this->url = $ImportWooCommerceApiUrl;
    $this->client = new ClientWrapper(
      //URL des WooCommerce Rest Servers
      $ImportWooCommerceApiUrl,
      //WooCommerce API Key
      $ImportWooCommerceApiKey,
      //WooCommerce API Secret
      $ImportWooCommerceApiSecret,
      ["query_string_auth" => true],
      $this->logger,
      $this->ssl_ignore
    );

    $storedTimestamp = $preferences['felder']['letzter_import_timestamp'] ?? null;
    if (!empty($storedTimestamp)) {
      $this->lastImportTimestamp = $storedTimestamp;
      $this->lastImportTimestampIsFallback = false;
    } else {
      $this->lastImportTimestamp = gmdate('Y-m-d\TH:i:s', strtotime('-30 days'));
      $this->lastImportTimestampIsFallback = true;
    }

    $storedIds = $preferences['felder']['letzter_import_order_ids'] ?? null;
    $this->lastImportOrderIds = is_array($storedIds)
      ? array_values(array_filter(array_map('intval', $storedIds)))
      : [];

  }

  /**
   * Backwards-compatible wrapper: persists timestamp only (order id cleared).
   * Use persistLastImportCursor() when both timestamp and order id are available.
   *
   * @param string $isoUtcDate ISO-8601 UTC timestamp, e.g. '2026-04-20T12:34:56'
   * @return void
   */
  public function persistLastImportTimestamp($isoUtcDate)
  {
    $this->persistLastImportCursor($isoUtcDate, null);
  }

  /**
   * Persists the tuple cursor (timestamp + accumulated order-id bucket) to
   * shopexport.einstellungen_json. Does a read-modify-write to preserve all
   * other fields.
   *
   * Bucket logic:
   *  - $orderId === null  → migration path; ids list is cleared.
   *  - same timestamp as stored → append $orderId to the ids list.
   *  - new timestamp → reset ids list to [$orderId].
   *
   * @param string   $isoUtcDate ISO-8601 UTC timestamp, e.g. '2026-04-20T12:34:56'
   * @param int|null $orderId    WooCommerce order ID, or null (migration path)
   * @return void
   */
  public function persistLastImportCursor($isoUtcDate, $orderId = null)
  {
    $shopid = (int)$this->shopid;
    // Prefer DatabaseService when available (web context), fall back to DB
    // so this method also works in the CLI/cron context.
    if (!empty($this->app->DatabaseService)) {
      $einstellungen_json = $this->app->DatabaseService->selectValue(
        "SELECT einstellungen_json FROM shopexport WHERE id = :id LIMIT 1",
        ['id' => $shopid]
      );
    } else {
      $einstellungen_json = $this->app->DB->Select(
        "SELECT einstellungen_json FROM shopexport WHERE id = '$shopid' LIMIT 1"
      );
    }
    $current = [];
    if (!empty($einstellungen_json)) {
      $current = json_decode($einstellungen_json, true) ?: [];
    }
    if (!isset($current['felder']) || !is_array($current['felder'])) {
      $current['felder'] = [];
    }

    $previousTs  = $current['felder']['letzter_import_timestamp'] ?? null;
    $previousIds = $current['felder']['letzter_import_order_ids'] ?? [];
    if (!is_array($previousIds)) {
      $previousIds = [];
    }

    // Determine ids list for the new state.
    if ($orderId === null) {
      // Migration path — timestamp without a concrete order-id anchor.
      $newIds = [];
    } elseif ($previousTs !== null && $isoUtcDate === $previousTs) {
      // Same timestamp bucket — append id if not already present.
      $newIds = $previousIds;
      if (!in_array((int) $orderId, $newIds, true)) {
        $newIds[] = (int) $orderId;
      }
    } else {
      // New timestamp bucket — reset list to only this id.
      $newIds = [(int) $orderId];
    }

    $current['felder']['letzter_import_timestamp']  = $isoUtcDate;
    $current['felder']['letzter_import_order_ids']  = $newIds;

    $jsonEncoded = $this->app->DB->real_escape_string(json_encode($current));
    if (!empty($this->app->DatabaseService)) {
      $this->app->DatabaseService->execute(
        "UPDATE shopexport SET einstellungen_json = :json WHERE id = :id",
        ['json' => json_encode($current), 'id' => $shopid]
      );
    } else {
      $this->app->DB->Update(
        "UPDATE shopexport SET einstellungen_json = '$jsonEncoded' WHERE id = '$shopid'"
      );
    }
    $this->lastImportTimestamp = $isoUtcDate;
    $this->lastImportOrderIds  = $newIds;
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

    if (empty($ImportWooCommerceApiUrl)) {
      return new JsonResponse(['error' => 'Bitte die API-Url angeben'], JsonResponse::HTTP_BAD_REQUEST);
    }
    if (empty($ImportWooCommerceApiKey)) {
      return new JsonResponse(['error' => 'Bitte den API-Key angeben'], JsonResponse::HTTP_BAD_REQUEST);
    }
    if (empty($ImportWooCommerceApiSecret)) {
      return new JsonResponse(['error' => 'Bitte das API-Secret angeben'], JsonResponse::HTTP_BAD_REQUEST);
    }
    $this->client = new ClientWrapper(
      $ImportWooCommerceApiUrl,
      $ImportWooCommerceApiKey,
      $ImportWooCommerceApiSecret,
      ['query_string_auth' => true],
      $this->logger,
      $this->ssl_ignore
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
   * @throws HttpClientException
   */
  private function getShopIdBySKU($sku)
  {
    // Retrieve the product with the given sku.
    // Note: We limit the result set to 1 (per_page=1), so this doesnt work
    // if there are multiple products with the same sku. should not happen in practice anyway
    $product = $this->client->get('products', ['sku' => $sku, 'per_page' => 1]);

    // We look at the first product in the array.
    // We may get an empty array, in that case null is returned
    if (isset($product[0])) {
      return [
        'id' => $product[0]->id,
        'parent' => $product[0]->parent_id,
        'isvariant' => !empty($product[0]->parent_id)
      ];
    }

    return null;
  }

  private function getSKUByShopId($articleid, $variationid)
  {
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
        'ausblenden' => array('abholmodus' => array('zeitbereich')),
        'archiv' => array('ab_nummer'),
        'felder' => array(
          //          'protokoll'=>array('typ'=>'checkbox','bezeichnung'=>'Protokollierung im Logfile:'),
          'ssl_ignore' => array('typ' => 'checkbox', 'bezeichnung' => 'SSL-Prüfung abschalten:', 'info' => 'Nur für Testzwecke!'),
          'ImportWoocommerceApiKey' => array('typ' => 'text', 'bezeichnung' => '{|API Key:', 'size' => 60),
          'ImportWoocommerceApiSecret' => array('typ' => 'text', 'bezeichnung' => '{|API Secret|}:', 'size' => 60),
          'ImportWoocommerceApiUrl' => array('typ' => 'text', 'bezeichnung' => '{|API Url|}:', 'size' => 40),
          'statusPending' => array('typ' => 'text', 'bezeichnung' => '{|Statusname Bestellung offen|}:', 'size' => 40, 'default' => 'pending', 'info' => '({|ggfs. getrennt durch ";": pending;on-hold|})'),
          'statusProcessing' => array('typ' => 'text', 'bezeichnung' => '{|Statusname Bestellung in Bearbeitung|}:', 'size' => 10, 'default' => 'processing'),
          'statusCompleted' => array('typ' => 'text', 'bezeichnung' => '{|Statusname Bestellung fertig|}:', 'size' => 10, 'default' => 'completed'),
          'priceType' => array('typ' => 'select', 'bezeichnung' => '{|Preisberechnungsgrundlage bei Auftragsimport|}', 'optionen' => array('netcalculated' => '{|Nettopreis zurückrechnen (Standard)|}', 'grosscalculated' => '{|Bruttopreis zurückrechnen|}')),
        )
      );
  }

  /**
   * Compares two Objects and returns true if every variable in items
   * is the same in $a and $b
   * @param  Obj $a     [description]
   * @param  Obj $b     [description]
   * @param  array $items [description]
   * @return Bool        [description]
   */
  protected static function compareObjects($a, $b, $items)
  {
    foreach ($items as $v) {
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
  protected static function emptyString($string)
  {
    return (strlen(trim($string)) == 0);
  }

}
