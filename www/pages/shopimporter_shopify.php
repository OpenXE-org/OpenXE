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

include_once 'Shopimporter_Shopify_Adapter.php';

/**
 * Class Shopimporter_Shopify
 */
class Shopimporter_Shopify extends ShopimporterBase
{
  /** @var Application $app */
  var $app;
  protected static $requestcount;
  var $errors;
  var $shopid;
  var $data;
  var $ShopifyURL = '';
  var $ShopifyAPIKey = '';
  var $ShopifyPassword = '';
  var $ShopifyToken = '';
  var $locations = '';
  var $location = '';
  var $bearbeiter;
  var $table;
  var $partial;
  var $logging;
  var $dump;
  var $allow0;
  var $gotpendig;
  var $fulfilledabziehen;
  var $bezprodukt = 'shopifyproductid';
  var $bezvariant = 'shopifyvariantid';
  var $archive = false;
  var $autofullfilltax;
  public $eigenschaftenzubeschreibung = false;
  public $timezone;
  protected $apiVersion = '2019-10';
  /**
   * @var Shopimporter_Shopify_Adapter
   */
  protected $adapter;
  /** @var \Xentral\Components\Logger\Logger */
  private $logger;

  /**
   * Shopimporter_Shopify constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app = $app;
    /** @var \Xentral\Components\Logger\Logger $logger */
    $this->logger = $this->app->Container->get('Logger');
    if($intern){
      return;
    }
    $this->app->ActionHandlerInit($this);

    $this->dump=true;

    $this->app->ActionHandler("list","Shopimporter_ShopifyList");
    $this->app->ActionHandler("auth","ImportAuth");
    $this->app->ActionHandler("getlist","ImportGetList");
    $this->app->ActionHandler("sendlist","ImportSendList");
    $this->app->ActionHandler("sendlistlager","ImportSendListLager");
    $this->app->ActionHandler("getarticle","ImportGetArticle");
    $this->app->ActionHandler("getauftraegeanzahl","ImportGetAuftraegeAnzahl");
    $this->app->ActionHandler("getauftrag","ImportGetAuftrag");
    $this->app->ActionHandler("deletearticle","ImportDeleteArticle");
    $this->app->ActionHandler("deleteauftrag","ImportDeleteAuftrag");
    $this->app->ActionHandler("updateauftrag","ImportUpdateAuftrag");
    $this->app->ActionHandler("artikelgruppen","ImportArtikelgruppen");
    $this->app->ActionHandler("getarticlelist","ImportGetArticleList");
    $this->app->ActionHandler("test","ImportTest");
    $this->app->ActionHandler("storniereauftrag","ImportStorniereAuftrag");

    $this->app->DefaultActionHandler("list");

    $this->app->ActionHandlerListen($app);
  }


    /**
     * @return string
     */
    public function getClickByClickHeadline()
    {
        return 'Bitte im Shopify Backend eine eigene App für Xentral anlegen und die Zugangsdaten hier eintragen.';
    }

  /**
   * @param array $data
   *
   * @return array
   */
  public function checkApiApp($data)
  {
    if(empty($data['data']['ShopifyToken'])) {
      foreach (['ShopifyURL', 'ShopifyAPIKey', 'ShopifyPassword'] as $field) {
        if(empty($data['data'][$field])){
          return ['success' => false, 'error' => sprintf('%s is empty', $field)];
        }
      }
    }

    $shops = $this->app->DB->SelectArr(
      sprintf(
        "SELECT `einstellungen_json`, `bezeichnung`,`id` 
        FROM `shopexport` WHERE `modulename` = 'shopimporter_shopify'"
      )
    );
    if(empty($shops)) {
      return [
        'info' => [
          'Shop' => 'Shopify',
          'info' => $data['data']['ShopifyURL'],
        ]
      ];
    }
    foreach($shops as $shop) {
      $json = @json_decode($shop['einstellungen_json'], true);
      if(empty($json['felder']) || empty($json['felder']['ShopifyURL'])) {
        continue;
      }
      if($json['felder']['ShopifyURL'] === $data['data']['ShopifyURL']) {
        return [
          'success'=>false,
          'error' => sprintf('Shop with url %s allready exists', $data['data']['ShopifyURL'])
        ];
      }
    }

    return [
      'info' => [
        'Shop' => 'Shopify',
        'info' => $data['data']['ShopifyURL'],
      ]
    ];
  }

  public function ImportGetArticleList()
  {
    $result = $this->adapter->call('products.json?fields=id&limit=100');
    $productIds = $result['data']['products'];

    while(!empty($result['links']['next'])){
      $result = $this->adapter->call('products.json?'.$result['links']['next']);
      $productIds = array_merge($productIds, $result['data']['products']);
    }
    
    $response = array_map(function($value) {return reset($value);}, $productIds);

    return $response;
  }


  public function Install()
  {
    $this->app->erp->CheckTable("shopimporter_shopify_auftraege");
    $this->app->erp->CheckColumn("id", "int(11)", "shopimporter_shopify_auftraege", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("shop", "INT(11)", "shopimporter_shopify_auftraege", "DEFAULT '0' NOT NULL");
    $this->app->erp->CheckColumn("extid", "varchar(32)", "shopimporter_shopify_auftraege", "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn("status", "INT(11)", "shopimporter_shopify_auftraege", "DEFAULT '0' NOT NULL");
    $this->app->erp->CheckColumn("bearbeiter", "varchar(32)", "shopimporter_shopify_auftraege", "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn("zeitstempel", "TIMESTAMP", "shopimporter_shopify_auftraege", "DEFAULT CURRENT_TIMESTAMP NOT NULL");
    $this->app->erp->CheckColumn("transaction_id","varchar(64)","shopimporter_shopify_auftraege","DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn("zahlungsweise","varchar(64)","shopimporter_shopify_auftraege","DEFAULT '' NOT NULL");
    $this->app->erp->CheckIndex('shopimporter_shopify_auftraege','shop');
    $this->app->erp->CheckIndex('shopimporter_shopify_auftraege','extid');
    $this->app->erp->RegisterHook('shopexport_create', 'shopimporter_shopify', 'ShopifyImporterShopexportCreate');
    $this->app->erp->RegisterHook('shopexport_show', 'shopimporter_shopify', 'ShopexportShow');
  }

  public function ShopexportShow($id, $obj, $tab)
  {
    $json = $id <= 0?null:$this->app->DB->Select(
      sprintf(
        "SELECT `einstellungen_json` 
        FROM `shopexport` 
        WHERE `id` = %d AND `shoptyp` = 'intern' AND `modulename` = 'shopimporter_shopify'",
        $id
      )
    );
    if(empty($json)) {
      return;
    }
    $json = json_decode($json,true);
    if(!empty($json['felder']['locations']) && empty($json['felder']['location'])) {
      $this->app->Tpl->Add(
        'MESSAGE',
        '<div class="warning">Bitte stellen Sie einen Lagerstandort ein.</div>'
      );
    }
  }

  public function ShopifyImporterShopexportCreate($id)
  {
    if($id <= 0 || !$this->app->DB->Select("SELECT id FROM shopexport WHERE shoptyp = 'intern' AND id = '$id' AND modulename = 'shopimporter_shopify' LIMIT 1")){
      return;
    }
    $this->app->DB->Update("UPDATE shopexport SET positionsteuersaetzeerlauben = 1 WHERE id = $id LIMIT 1");
  }

  public function getKonfig($shopid, $data = null)
  {
    $this->app->DB->Select('SELECT transaction_id FROM shopimporter_shopify_auftraege LIMIT 1');
    if($this->app->DB->error()){
      $this->Install();
    }
    $this->timezone = 'Europe/Berlin';
    $this->shopid = $shopid;
    $this->data = $data;
    $this->bearbeiter = 'Cronjob';
    if(isset($this->app->User) && $this->app->User && method_exists($this->app->User, 'GetName')){
      $this->bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
    }
    $einstellungen = $this->app->DB->Select("SELECT einstellungen_json FROM shopexport WHERE id = '$shopid' LIMIT 1");
    if($einstellungen){
      $einstellungen = json_decode($einstellungen,true);
    }
    $this->ShopifyURL=trim($einstellungen['felder']['ShopifyURL']);
    if(stripos($this->ShopifyURL,'http') === false){
      $this->ShopifyURL = 'https://'.$this->ShopifyURL;
    }
    $this->ShopifyAPIKey=$einstellungen['felder']['ShopifyAPIKey'];
    $this->ShopifyPassword=$einstellungen['felder']['ShopifyPassword'];
    $this->ShopifyToken = $einstellungen['felder']['ShopifyToken'];
    if(empty($this->ShopifyToken)) {
      if(strpos($this->ShopifyURL, 'https://') !== false) {
        $this->ShopifyURL = 'https://' . $this->ShopifyAPIKey . ':' . $this->ShopifyPassword . '@' . str_replace('https://', '', $this->ShopifyURL);
      }
      else{
        $this->ShopifyURL = 'http://' . $this->ShopifyAPIKey . ':' . $this->ShopifyPassword . '@' . str_replace('http://', '', $this->ShopifyURL);
      }
    }
    $this->table = 'shopimporter_shopify_auftraege';
    $this->partial = (int)$einstellungen['felder']['partial'];
    $this->logging = (int)$einstellungen['felder']['logging'];
    $this->variantnameauseigenschaften = (int)$einstellungen['felder']['variantnameauseigenschaften'];
    $this->optionsnameauseigenschaften = (int)$einstellungen['felder']['optionsnameauseigenschaften'];
    $this->eigenschaftenzubeschreibung = (int)$einstellungen['felder']['eigenschaftenzubeschreibung'];
    $this->allow0 = (int)$einstellungen['felder']['allow0'];
    $this->fulfilledabziehen = (int)$einstellungen['felder']['fulfilledabziehen'];
    $this->gotpendig = (int)$einstellungen['felder']['gotpendig'];
    $this->preisalsnetto = (int)$einstellungen['felder']['preisalsnetto'];
    $this->shopifytracking = (int)$einstellungen['felder']['shopifytracking'];
    $this->location = $einstellungen['felder']['location'];
    $this->locations = $einstellungen['felder']['locations'];
    $this->autofullfilltax = $einstellungen['felder']['autofullfilltax'];

    if($this->adapter === null){
      $this->adapter = new Shopimporter_Shopify_Adapter($this->app, $this->ShopifyURL, $this->shopid, $this->ShopifyToken);
    }

  }

  /**
   * @param $adapter
   */
  public function setAdapter($adapter){
    $this->adapter = $adapter;
  }

  public function getAdapter(){
    return $this->adapter;
  }

  public function EinstellungenStruktur($id = 0)
  {
    $options = $this->getKonfig($id,null);
    if(!empty($this->locations))
    {
      $options = json_decode(base64_decode($this->locations),true);
    }else{
      $options = array();
    }
    return 
    array(
    'ausblenden'=>array('abholmodus'=>array('ab_nummer','status')),
    'functions'=>array('getarticlelist'),
    'felder'=>array(
        'logging'=>array('typ'=>'checkbox','bezeichnung'=>'{|Erweiterte Protokollierung|}:'),
        'ShopifyURL'=>array('typ'=>'text','bezeichnung'=>'{|Shopify URL|}:','size'=>40),
        'ShopifyAPIKey'=>array('typ'=>'text','bezeichnung'=>'{|Shopify API-Key|}:','size'=>40),
        'ShopifyPassword'=>array('typ'=>'text','bezeichnung'=>'{|Shopify Passwort|}:','size'=>40),
        'ShopifyToken'=>array('typ'=>'text','bezeichnung'=>'{|Shopify Token|}:','size'=>40),
        'preisalsnetto'=>array('typ'=>'checkbox', 'bezeichnung'=>'{|Preise als Netto uebertragen|}:', 'default'=>0),
        'partial'=>array('typ'=>'checkbox','bezeichnung'=>'{|auch Teilversendete Auftr&auml;ge abholen|}:'),
        'allow0'=>array('typ'=>'checkbox', 'bezeichnung'=>'{|erlaube Verkauf von Varianten mit Lagerzahl 0|}:', 'default'=>1),
        'fulfilledabziehen'=>array('typ'=>'checkbox', 'bezeichnung'=>'{|Teilgelieferte Mengen abziehen|}:', 'default'=>0,'defaultcreate'=>1),
        'gotpendig'=>array('typ'=>'checkbox', 'bezeichnung'=>'{|auch Pending Payment abholen|}:', 'default'=>0),
        'shopifytracking'=>array('typ'=>'checkbox','bezeichnung'=>'{|Tracking E-Mails &uuml;ber Shopify versenden|}:','default'=>0),
        'variantnameauseigenschaften'=>array('typ'=>'checkbox','bezeichnung'=>'{|Variantentitel aus Eigenschaften zusammensetzen|}:','default'=>0),
        'optionsnameauseigenschaften'=>array('typ'=>'checkbox','bezeichnung'=>'{|Bei Export Optionenname aus Eigenschaftenbezeichnungen zusammensetzen|}:','default'=>0),
        'eigenschaftenzubeschreibung'=>array('typ'=>'checkbox','bezeichnung'=>'{|Bei Import Optionen in Shopify zu Artikelbeschreibung übernehmen|}:','default'=>0),
        'locations'=>array('typ'=>'hidden'),
        'location'=>array('typ'=>'select','bezeichnung'=>'{|Lagerstandort|}:','optionen'=>$options),
          'autofullfilltax'=>array('typ'=>'checkbox','bezeichnung'=>'{|Versandsteuer nach Positionen ermitteln|}:'),
        ));
  }
    
  public function Shopimporter_ShopifyList()
  {
    $this->app->DB->Select('SELECT transaction_id FROM shopimporter_shopify_auftraege LIMIT 1');
    if($this->app->DB->error()){
      $this->Install();
    }
    if($id = $this->app->DB->Select("SELECT id FROM shopexport WHERE modulename = 'shopimporter_shopify' ORDER BY aktiv = 1 DESC LIMIT 1"))
    {
      header('Location: index.php?module=onlineshops&action=edit&id='.$id);
      exit;
    }
    $msg = $this->app->erp->base64_url_encode('<div class="info">Sie k&ouml;nnen hier die Shops einstellen</div>');
    header('Location: index.php?module=onlineshops&action=list&msg='.$msg);
    exit;
  }

  /**
   * @return float|null
   */
  public function getTaxRateFromCountries()
  {
    $tax = null;
    $countries = $this->adapter->call('countries.json');
    if(empty($countries['data']) || empty($countries['data']['countries'])) {
      return null;
    }
    $xentralCountry = $this->app->erp->Firmendaten('land');
    foreach($countries['data']['countries'] as $country) {
      if($country['code'] !== $xentralCountry) {
        continue;
      }
      if(isset($country['tax'])) {
        return (float)$country['tax'];
      }
      return null;
    }

    return null;
  }
  
  // Wenn WaWision Artikel abholt wird diese Funktion aufgerufen / es muss das $data array gefüllt werden
  public function ImportGetArticle()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $tax = 1.0 + (float)$this->getTaxRateFromCountries();
    $nummer = $tmp['nummer'];
    if(isset($tmp['nummerintern']))
    {
      $nummer = $tmp['nummerintern'];
    }

    $result = $this->adapter->call('products/'.$nummer.'.json');

    if(!isset($result['data']['product']) || !isset($result['data']['product']['id']))
    {
      $resultv = $this->adapter->call('variants/'.$nummer.'.json');
      if(!empty($resultv['data']['variant']) && !empty($resultv['data']['variant']['product_id']))
      {
        $result = $this->adapter->call('products/'.$resultv['data']['variant']['product_id'].'.json');
      }else{
        return $resultv['data'];
      }
    }
    if(empty($result['data']['product']) || empty($result['data']['product']['id']))
    {
      $this->error[]='Artikel in der Shop Datenbank nicht gefunden!';
    } else {

      // erlaubte felder
      $fields = array('name_de','kurztext_de','uebersicht_de','name_en','kurztext_en','uebersicht_en','beschreibung_de','beschreibung_en','metakeywords_de','metakeywords_en','metadescription_de','metadescription_en',
          'inaktiv','pseudopreis','lieferzeitmanuell','pseudolager','autolagerlampe','restmenge','gewicht','downloadartikel');

      if(isset($result['data']['product']))
      {
        $data['name']=$result['data']['product']['title'];
        $data['uebersicht_de']=$result['data']['product']['body_html'];
        $data['hersteller']=$result['data']['product']['vendor'];
        $data['result'] = $result['data'];
        $this->adapter->call("products/".$result['data']['product']['id']."/metafields.json", 'POST', array('metafield' => [
          'key' => 'sync_status',
          'value' => 1,
          'value_type' => 'integer',
          'namespace' => 'xentral',
        ]));
        if($result['data']['product']['id'] == $nummer) {
          $data['fremdnummern'][] = [
            'nummer' => $nummer,
            'bezeichnung' => $this->bezprodukt,
          ];
        }
        if(isset($result['data']['product']['options']) && $result['data']['product']['options'])
        {
          foreach($result['data']['product']['options'] as $k2 => $v)
          {
            $data['matrixprodukt_gruppe'.$v['position']] = $v['name'];
            foreach($v['values'] as $k3 => $v3)
            {
              $data['matrixprodukt_optionen'.$v['position']][] = $v3;
            }
          }
        }
        
        if(!empty($result['data']['product']['variants']) && !empty($result['data']['product']['variants'][0]))
        {
          if(!empty($resultv))
          {
            foreach($result['data']['product']['variants'] as $k => $v)
            {
              if($v['id'] == $resultv['data']['variant']['id'])
              {
                $data['variante_von'] = $resultv['data']['variant']['product_id'];
                $this->adapter->call("variants/".$resultv['variant']['id']."/metafields.json", 'POST', array('metafield' => [
                  'key' => 'sync_status',
                  'value' => 1,
                  'value_type' => 'integer',
                  'namespace' => 'xentral',
                ]));
                $data['nummer'] = $resultv['data']['variant']['sku'];
                $data['name'] .= ' '.$resultv['data']['variant']['title'];
                $data['uebersicht_de']='';//$result['product']['body_html']; // use from parent article
                $data['hersteller']='';//$result['product']['vendor']; // use from parent article
                break;
              }
            } 
          } else {
            $k = 0;
          }

          if(isset($tmp['nummerintern'])){
            unset($data['nummer']);
          }
          
          /*
          if(isset($tmp['nummerintern']) && isset($result['product']['variants'][$k]['sku']) && $result['product']['variants'][$k]['sku'] != '' && !isset($result['product']['variants'][1]))
          {
            $data['nummer'] = $result['product']['variants'][$k]['sku'];
          }elseif($tmp['nummerintern']){
            $data['nummer'] = $this->app->erp->GetNextArtikelnummer("",1,$this->app->DB->Select("SELECT projekt FROM shopexport WHERE id = '".$this->shopid."' LIMIT 1"));
          }*/
          if($result['data']['product']['variants'][$k]['grams'])
          {
            $data['gewicht']=$result['data']['product']['variants'][$k]['grams']/1000;//kg
          }else{
            $data['gewicht']=$result['data']['product']['variants'][$k]['weight'];
          }
          if(!empty($result['data']['product']['variants'][$k]['taxable'])) {
            $data['preis_netto'] = $result['data']['product']['variants'][$k]['price'] / $tax;
          }
          else{
            $data['preis_netto'] = $result['data']['product']['variants'][$k]['price'];
          }
          $data['restmenge'] = $result['data']['product']['variants'][$k]['inventory_quantity'];
          if(!empty($result['data']['product']['variants'][$k]['barcode'])){
            $data['ean'] = $result['data']['product']['variants'][$k]['barcode'];
          }
          if(!(isset($tmp['nummerintern']) && isset($result['data']['product']['variants'][1])))
          {
            if(!empty($result['data']['product']['variants'][$k]['option1'])){
              $data['matrixprodukt_wert1'] = $result['data']['product']['variants'][$k]['option1'];
            }
            if(!empty($result['data']['product']['variants'][$k]['option2'])){
              $data['matrixprodukt_wert2'] = $result['data']['product']['variants'][$k]['option2'];
            }
            if(!empty($result['data']['product']['variants'][$k]['option3'])){
              $data['matrixprodukt_wert3'] = $result['data']['product']['variants'][$k]['option3'];
            }
            if($nummer == $result['data']['product']['variants'][$k]['id']
              && !empty($result['data']['product']['variants'][$k]['sku'])) {
              $data['artikelnummerausshop'] = $result['data']['product']['variants'][$k]['sku'];
              $data['fremdnummern'][] = [
                'nummer' => $nummer,
                'bezeichnung' => $this->bezvariant,
              ];
            }
          }
          
          $tmpid = null;
          if(!empty($result['data']['product']['image']) && !empty($result['data']['product']['image']['src']))
          {
            $tmpid = $result['data']['product']['image']['id'];
            $data['bilder'][] = array('path'=>$result['data']['product']['image']['src'], 'content'=>@base64_encode(@file_get_contents($result['data']['product']['image']['src'])));
          }
          if(isset($result['data']['product']['images']) && $result['data']['product']['images'])
          {
            foreach($result['data']['data']['product']['images'] as $v)
            {
              if($v['id'] != $tmpid)
              {
                $data['bilder'][] = array('path'=>$v['src'], 'content'=>@base64_encode(@file_get_contents($v['src'])));
              }
            }
          }
          
          if(isset($tmp['nummerintern']) && !empty($result['data']['product']['variants'][1]))
          {
            //$data['variante_von'] = $result['product']['variants'][0]['product_id'];
            $data['shoparticleid'] = $result['data']['product']['variants'][0]['product_id'];
            if(isset($data['nummer'])){
              unset($data['nummer']);
            }
            if(isset($data['result'])){
              unset($data['result']);
            }
            $_data[] = $data;
            unset($data['uebersicht_de']);
            if(isset($data['result'])){
              unset($data['result']);
            }
            $_name = $data['name'];
            foreach($result['data']['product']['variants'] as $k2 => $v2)
            {
              unset($data['nummer'],$data['bilder']);
              $data['variante_von'] = $result['data']['product']['variants'][$k2]['product_id'];
              $data['nummerintern'] = $result['data']['product']['variants'][$k2]['id'];
              $data['shoparticleid'] = $result['data']['product']['variants'][$k2]['id'];
              $data['name'] = trim($_name . ' '.$result['data']['product']['variants'][$k2]['title']);
              if(isset($result['data']['product']['variants'][$k2]['sku']) && $result['data']['product']['variants'][$k2]['sku'] != '')
              {
                $data['nummer'] = $result['data']['product']['variants'][$k2]['sku'];
                $data['artikelnummerausshop'] = $result['data']['product']['variants'][$k2]['sku'];
              }//else $data['nummer'] = $result['product']['variants'][$k2]['id'];
              if($result['data']['product']['variants'][$k2]['grams'])
              {
                $data['gewicht']=$result['data']['product']['variants'][$k2]['grams']/1000;//kg
              }else{
                $data['gewicht']=$result['data']['product']['variants'][$k2]['weight'];
              }
              if(!empty($result['data']['product']['variants'][$k2]['taxable'])){
                $data['preis_netto'] = $result['data']['product']['variants'][$k2]['price'] / $tax;
              }
              else {
                $data['preis_netto'] = $result['data']['product']['variants'][$k2]['price'];
              }
              $data['restmenge'] = $result['data']['product']['variants'][$k2]['inventory_quantity'];
              if(!empty($result['data']['product']['variants'][$k2]['option1'])){
                $data['matrixprodukt_wert1'] = $result['data']['product']['variants'][$k2]['option1'];
              }
              if(!empty($result['data']['product']['variants'][$k2]['option2'])){
                $data['matrixprodukt_wert2'] = $result['data']['product']['variants'][$k2]['option2'];
              }
              if(!empty($result['data']['product']['variants'][$k2]['option3'])){
                $data['matrixprodukt_wert3'] = $result['data']['product']['variants'][$k2]['option3'];
              }
              if($nummer == $result['data']['product']['variants'][$k2]['id']
                && !empty($result['data']['product']['variants'][$k2]['sku'])) {
                $data['artikelnummerausshop'] = $result['data']['product']['variants'][$k2]['sku'];
              }
              if(!empty($result['data']['product']['variants'][$k2]['barcode'])){
                $data['ean'] = $result['data']['product']['variants'][$k2]['barcode'];
              }
              $data['result'] = $result['data'];
              $_data[]= $data;
            }
            return $_data;
          }
        }
      }else{
        $data['name']=$result['data']['product']['title'];
        $data['uebersicht_de']=$result['data']['product']['body_html'];
        $data['hersteller']=$result['data']['product']['vendor'];
      }

      $this->DumpVar($data);
    }
    if($this->app->Conf->Debug ){
      $data['result'] = $result['data'];
    }

    return $data;
  }

  public function ImportTest()
  {//$result = $this->ApiConnect("/admin/orders.json?status=any&since_id=".$_tmp['ab_nummer']."&limit=25");
    //$result = $this->ApiConnect("/admin/orders.json?fulfillment_status=unshipped");
    //$result = json_decode($result,true);
    //$result = "Test";
   // $result = $this->ApiConnect('/admin/product_listings.json');
    $result = $this->adapter->call('locations.json');

    return $result['links'];
  }

  // receive all new articles
  public function ImportSendListLager()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $bezprodukt = $this->bezprodukt;
    $bezvariant = $this->bezvariant;
    //Falls mehrere Locations existieren, hier aufbohren:
    if(!empty($this->location))
    {
      $locationid = $this->location;
    }else{
      $location = $this->adapter->call('locations.json');
      $locationid = $location['data']['locations'][0]['id'];
    }
    $anzahl = 0;
    $ctmp = !empty($tmp)?count($tmp):0;
    for($i=0;$i<$ctmp;$i++)
    {
      if(isset($tmp[$i]['artikel_varianten'])){
        foreach ($tmp[$i]['artikel_varianten'] as $key => $value) {
          $variantid = '';
          foreach ($value['artikelnummer_fremdnummern'] as $fkey => $fvalue) {
            if(strtolower($fvalue['bezeichnung']) === $bezvariant){
              $variantid = $fvalue['nummer'];
              break;
            }
          }
          if($variantid != ''){
            $resultv = $this->adapter->call('variants/'.$variantid.'.json');
            if(!isset($resultv['data']['errors'])){
              $lag = $value['lag'];
              $pseudolag = $value['pseudolager'];
              if($pseudolag  != '') {
                $lag=$pseudolag;
              }
              $inventoryitemid = $resultv['data']['variant']['inventory_item_id'];
              $resulti = $this->adapter->call("inventory_levels.json?inventory_item_ids=$inventoryitemid&location_ids=$locationid");
              if(!is_array($resulti['data'])){
                $this->ShopifyLog('Shopify Lagerzahlübertragung Fehler für Artikel: '.$value['nummer'], $resulti['data']);
                return 'error: '.$resulti['data'];
              }
              $vorhanden = $resulti['data']['inventory_levels'][0]['available'];
              $adjust = $lag - $vorhanden;
              if($adjust != 0){
                $data = array("location_id" => $locationid,
                              "inventory_item_id"=> $inventoryitemid,
                              "available_adjustment"=> $adjust);
                $result = $this->adapter->call('inventory_levels/adjust.json', 'POST',$data);
                $this->ShopifyLog("Sendlistlager A Variant $variantid",$result['data']);
              }
            }
          }
        }
      }else{
        if(isset($tmp[$i]['artikelnummer_fremdnummern'])){
          foreach ($tmp[$i]['artikelnummer_fremdnummern'] as $key => $value) {
            if(strtolower($value['bezeichnung']) === $bezprodukt){
              $productid = $value['nummer'];
            }
            if(strtolower($value['bezeichnung']) === $bezvariant){
              $variantid = $value['nummer'];
            }
          }
        }

        if($productid != ''){
          if($variantid !=''){
            $resultv = $this->adapter->call('variants/'.$variantid.'.json');
            if(isset($resultv['data']['errors'])){
              $variantid = '';
            }
          }

          if($variantid == ''){
            $result = $this->adapter->call('products/'.$productid.'.json');
            $variantid = $result['data']['product']['variants'][0]['id'];
            $this->FremdnummerInsert($tmp[$i]['artikel'],$variantid,$bezvariant);
          }
        }

        if($variantid != ''){
          $resultv = $this->adapter->call('variants/'.$variantid.'.json');
          if(isset($resultv['data']['errors'])){
            $variantid = '';
          }
          $lageranzahl = $tmp[$i]['anzahl_lager'];
          $pseudolager = trim($tmp[$i]['pseudolager']);
          if($pseudolager  != '') {
            $lageranzahl=$pseudolager;
          }
          $inventoryitemid = $resultv['data']['variant']['inventory_item_id'];
          $resulti = $this->adapter->call("inventory_levels.json?inventory_item_ids=$inventoryitemid&location_ids=$locationid");
          $vorhanden = $resulti['data']['inventory_levels'][0]['available'];
          $adjust = $lageranzahl - $vorhanden;
          if($adjust != 0){
            $data = array("location_id" => $locationid,
                          "inventory_item_id"=> $inventoryitemid,
                          "available_adjustment"=> $adjust);
            $result = $this->adapter->call('inventory_levels/adjust.json', 'POST',$data);
            $this->ShopifyLog("Sendlistlager B Variant $variantid",$result['data']);
          }
        }
      }
      $anzahl++;
    }

    return $anzahl;
  }

  // artikel von wawision zu shop uebertragen
  public function ImportSendList()
  {
    $shopid = $this->shopid;
    $location = $this->adapter->call('locations.json');
    $locationid = $location['data']['locations'][0]['id'];
    $tmp = $this->CatchRemoteCommand('data');
    $this->DumpVar($tmp); // hier alle Datenfelder sehen
    $denycontinue = 'continue';
    if(!$this->allow0){
      $denycontinue = 'deny';
    }
    $anzahl = 0;
    if($this->preisalsnetto){
      $preisart = "preis";
    }else{
      $preisart = "bruttopreis";
    }

    $shopeinstellungen = $this->app->DB->SelectArr("SELECT * FROM shopexport WHERE id = '".$this->shopid."' AND aktiv = 1 LIMIT 1");
    if(!empty($shopeinstellungen))
    {
      $shopeinstellungen = reset($shopeinstellungen);
      $eigenschaftenuebertragen = $shopeinstellungen['eigenschaftenuebertragen'];
      $kategorienuebertragen = $shopeinstellungen['kategorienuebertragen'];
      $shopbilderuebertragen = $shopeinstellungen['shopbilderuebertragen'];
      $variantenuebertragen = $shopeinstellungen['variantenuebertragen'];
      $crosssellingartikeluebertragen = $shopeinstellungen['crosssellingartikeluebertragen'];
    }else{
      $eigenschaftenuebertragen = 0;
      $kategorienuebertragen = 0;
      $shopbilderuebertragen = 0;
      $variantenuebertragen = 0;
      $crosssellingartikeluebertragen = 0;
    }
    $bezprodukt = $this->bezprodukt;
    $bezvariant = $this->bezvariant;
    $ctmp = !empty($tmp)?count($tmp):0;
    for($i=0;$i<$ctmp;$i++)
    {
      if($tmp[$i]['variante'] && $tmp[$i]['variantevon'] != ''){
        return "error: Variantenexport ist nur über den Hauptartikel möglich.";
      }
      $artikel = $tmp[$i]['artikel'];
      if($artikel == 'ignore'){
        continue;
      }
      $name_de = $tmp[$i]['name_de'];
      $name_en = $tmp[$i]['name_en'];
      if(isset($tmp[$i]['artikelnummer'])) {
        $nummer = $tmp[$i]['artikelnummer'];
      }
      else {
        $nummer = $tmp[$i]['nummer'];
      }
      $laststock = $tmp[$i]['restmenge'];
      $inaktiv = $tmp[$i]['inaktiv'];
      $shippingtime = $tmp[$i]['lieferzeitmanuell'];
      $gewicht = str_replace(",", ".", $tmp[$i]['gewicht']);
      $preis = (float)$tmp[$i][$preisart];
      $abverkauf = $tmp[$i]['restmenge'];
      $kategorie = $tmp[$i]['kategoriename'];
      $zolltarifnummer = $tmp[$i]['zolltarifnummer'];
      $metatitle_de = $tmp[$i]['metatitle_de'];
      $metadescription_de = $tmp[$i]['metadescription_de'];
      $metakeywords_de = $tmp[$i]['metakeywords_de'];
      $metatitle_en = $tmp[$i]['metatitle_en'];
      $metadescription_en = $tmp[$i]['metadescription_en'];
      $metakeywords_en = $tmp[$i]['metakeywords_en'];
      $lageranzahl = $tmp[$i]['anzahl_lager'];
      $pseudopreis = trim($tmp[$i]['pseudopreis']);
      $ean = $tmp[$i]['ean'];
      $hersteller = $tmp[$i]['hersteller'];

      $pseudolager = trim($tmp[$i]['pseudolager']);
      if($pseudolager  != '') $lageranzahl=$pseudolager;

      $productid = '';
      $variantid = '';
      $variantidhauptartikel = '';

      $eigenschaften = array();
      $eigenschaften[] = array(
            'namespace' => 'global',
            'key' => 'metatitle_de',
            'value' => $metatitle_de,
            'value_type' => 'string');
      $eigenschaften[] = array(
            'namespace' => 'global',
            'key' => 'metadescription_de',
            'value' => $metadescription_de,
            'value_type' => 'string');
      $eigenschaften[] = array(
            'namespace' => 'global',
            'key' => 'metakeywords_de',
            'value' => $metakeywords_de,
            'value_type' => 'string');

      $eigenschaften[] = array(
            'namespace' => 'ml_en',
            'key' => 'title',
            'value' => $name_en,
            'value_type' => 'string');
      $eigenschaften[] = array(
            'namespace' => 'ml_en',
            'key' => 'description',
            'value' => htmlspecialchars_decode($tmp[$i]['uebersicht_en']),
            'value_type' => 'string');
      $eigenschaften[] = array(
            'namespace' => 'ml_en',
            'key' => 'title',
            'value' => $metatitle_en,
            'value_type' => 'string');
      $eigenschaften[] = array(
            'namespace' => 'ml_en',
            'key' => 'meta_description',
            'value' => $metadescription_en,
            'value_type' => 'string');
      $eigenschaften[] = array(
            'namespace' => 'ml_en',
            'key' => 'metakeywords',
            'value' => $metakeywords_en,
            'value_type' => 'string');

      if(isset($tmp[$i]['freifelder'])){
        foreach ($tmp[$i]['freifelder'] as $iso => $ffdata) {
          foreach ($ffdata as $ffkey => $ffvalue) {
            $metakey = $ffkey;
            $metavalue = $ffvalue;
            if($iso != 'DE'){
              $metakey = substr($iso."_".$ffvalue['mapping'],0,30);
              $metavalue = $ffvalue['wert'];
            }
            $eigenschaften[] = array(
              'namespace' => 'global',
              'key' => $metakey,
              'value' => $metavalue,
              'value_type' => 'string');
          }
        }
      }

      if($eigenschaftenuebertragen){
        foreach ($tmp[$i]['eigenschaften'] as $key => $value) {
          $eigenschaften[] = array(
            'namespace' => 'global',
            'key' => substr($value['name'],0,30),
            'value' => $value['values'],
            'value_type' => 'string');
        }
      }
      if(!empty($tmp[$i]['eigenschaftenuebersetzungen'])){
        $eigenschaftenuebersetzungen = array();
        foreach ($tmp[$i]['eigenschaftenuebersetzungen'] as $eukey => $euvalue) {
          $eigenschaftenuebersetzungen[$euvalue['language_from']."_".$euvalue['language_to']][$euvalue['property_from']] = $euvalue['property_to'];
          $eigenschaftenwerteuebersetzungen[$euvalue['language_from']."_".$euvalue['language_to']][$euvalue['property_value_from']] = $euvalue['property_value_to'];
        }
        foreach ($eigenschaftenwerteuebersetzungen as $eutranslationkey => $eutranslationvalues) {
          if($eutranslationkey!= ''){
            foreach ($eutranslationvalues as $eufrom => $euto) {
              if($eufrom != '' && $euto != ''){
                $eigenschaften[] = array(
                'namespace' => 'translation_'.$eutranslationkey,
                'key' => substr($eufrom,0,30),
                'value' => $euto,
                'value_type' => 'string');
              }
            }
          }
        }
        foreach ($eigenschaftenuebersetzungen as $eutranslationkey => $eutranslationvalues) {
          if($eutranslationkey!= ''){
            foreach ($eutranslationvalues as $eufrom => $euto) {
              if($eufrom != '' && $euto != ''){
                $eigenschaften[] = array(
                'namespace' => 'ktranslation_'.$eutranslationkey,
                'key' => substr($eufrom,0,30),
                'value' => $euto,
                'value_type' => 'string');
              }
            }
          }
        }
      }

      if(!empty($tmp[$i]['artikelnummer_fremdnummern'])){
        foreach ($tmp[$i]['artikelnummer_fremdnummern'] as $key => $value) {
          if(strtolower($value['bezeichnung']) === $bezprodukt){
            $productid = $value['nummer'];
          }
          if(strtolower($value['bezeichnung']) === $bezvariant){
            $variantid = $value['nummer'];
          }
        }
      }
      $variantidskumatching = array();
      if($productid == '' && $variantid == '' && $tmp[$i]['variantevon'] == ''){
        //Suche über Titel
        $result = $this->adapter->call("products.json?title=".urlencode($name_de));
        if(count($result['data']['products']) == 1){
          //Artikel gefunden -> ID verwenden
          $productid = $result['data']['products'][0]['id'];
          $this->FremdnummerInsert($artikel,$productid,$bezprodukt);
          foreach ($result['data']['products'][0]['variants'] as $key => $value) {
            $variantidskumatching[$value['sku']] = $value['id'];
          }
        }else if(count($result['data']['products']) > 1){
          //zu viel -> Abbruch
        }else{
          //Artikel nicht gefunden -> Neu anlegen
        }
      }
      if(isset($tmp[$i]['artikel_varianten'])){
        $allvariantsinactive = true;
        foreach ($tmp[$i]['artikel_varianten'] as $key => $value) {
          if(!$value['inaktiv'] && !$value['gesperrt']){
            $allvariantsinactive = false;
            break;
          }
        }
        if(isset($tmp[$i]['matrix_varianten'])){
          $matrixtmp = array();
          foreach ($tmp[$i]['matrix_varianten']['artikel'] as $key => $value) {
            foreach ($value as $mkey => $mvalue) {
              $matrixtmp[$key][$mvalue['name']] = $mvalue['values'];
            }
          }
          foreach ($tmp[$i]['artikel_varianten'] as $key => $value) {
            $tmp[$i]['artikel_varianten'][$key]['matrix'] = $matrixtmp[$value['artikel']];
          }
        }else{
          $optionsname = "Title";
          if($eigenschaftenuebertragen && $this->optionsnameauseigenschaften){
            if(isset($tmp[$i]['artikel_varianten'])){
              if(isset($tmp[$i]['artikel_varianten'][0]['eigenschaften'])){
                $optionsteile = array();
                foreach ($tmp[$i]['artikel_varianten'][0]['eigenschaften'] as $ekey => $evalue) {
                  $optionsteile[] = $evalue['name'];
                }
                if(count($optionsteile) > 0){
                  $optionsname = implode(' | ', $optionsteile);
                }
              }
            }
          }
        }
        //Wenn für einen Artikel mit Varianten keine aktive Variante vorliegt: Artikel ausblenden
        if($allvariantsinactive){
          $inaktiv = true;
        }
      }

      $productanlegen = false;
      $variantanlegen = false;
      if($productid != ''){
        $result = $this->adapter->call('products/'.$productid.'.json');
        if(!$result['data']['product']['id']){
          $this->app->DB->Delete("DELETE FROM artikelnummer_fremdnummern WHERE artikel='$artikel' AND aktiv='1' AND shopid='$shopid' AND bezeichnung='$bezprodukt'");
          $productanlegen = true;
          $variantidskumatching = array();
        }
      }else{
        $productanlegen = true;
      }
      if($variantid != ''){
        $resultv = $this->adapter->call('variants/'.$variantid.'.json');
        if(!$resultv['data']['variant']['id']){
          $this->app->DB->Delete("DELETE FROM artikelnummer_fremdnummern WHERE artikel='$artikel' AND aktiv='1' AND shopid='$shopid' AND bezeichnung='$bezvariant'");
          $variantanlegen = true;
        }
      }else{
        $variantanlegen = true;
      }

      $matrixvarianthilfsid = '';
      $dataproduct = array();
      $datavariant = array();
      if($tmp[$i]['variante'] && $tmp[$i]['variantevon'] != ''){
        //Artikel ist eine Variante -> Export nur über Hauptartikel
      }else{
        $dataproduct['product']['title'] = $name_de;
        $dataproduct['product']['body_html'] = htmlspecialchars_decode($tmp[$i]['uebersicht_de']);
        $dataproduct['product']['vendor'] = $hersteller;
        if($kategorienuebertragen){
          $dataproduct['product']['product_type'] = $kategorie;
        }
        if(isset($tmp[$i]['artikel_varianten'])){
          if(isset($tmp[$i]['matrix_varianten'])){
            $counter = 1;
            foreach ($tmp[$i]['matrix_varianten']['gruppen'] as $key => $value) {
              $dataproduct['product']['options'][] = array("name" => $key, "position" =>$counter, "values"=>array("x"));
              $dataproduct['product']['variants'][0]['option'.$counter] = key($value);
              $counter++;
            }
          }else{
            $dataproduct['product']['options'][] = array("name" => $optionsname, "position" =>1, "values"=>array("x"));
            //Variante ohne Matrixartikel
            $dataproduct['product']['published'] = false;
          }
        }else{
          $dataproduct['product']['variants'][0]['weight'] = $gewicht;
          $dataproduct['product']['variants'][0]['price'] = $preis;
          $dataproduct['product']['variants'][0]['inventory_management'] = "shopify";
          $dataproduct['product']['variants'][0]['sku'] = $nummer;
          $dataproduct['product']['variants'][0]['barcode'] = $ean;
          $dataproduct['product']['variants'][0]['inventory_policy'] = ($abverkauf?"deny":"continue");
          $dataproduct['product']['variants'][0]['metafields'] = array(array(
            "key" => "harmonized_system_code",
            "value"=> $zolltarifnummer,
            "value_type"=> "string",
            "namespace"=> "global"),
            [
              'key' => 'sync_status',
              'value' => 1,
              'value_type' => 'integer',
              'namespace' => 'xentral',
            ]);
          if($pseudopreis != ''){
            $dataproduct['product']['variants'][0]['compare_at_price'] = round($pseudopreis,2);
          }
        }
        if(!$inaktiv){
          $dataproduct['product']['published'] = true;
        }
        else {
          $dataproduct['product']['published'] = false;
        }

        if($productanlegen){
          $result = $this->adapter->call('products.json','POST', $dataproduct);
          $this->ShopifyLog("Sendlist POST Artikel $nummer",$result['data']);
          $productid = $result['data']['product']['id'];
          if(!isset($result['data']['errors'])){
            $this->FremdnummerInsert($artikel,$productid,$bezprodukt);
            $variantidhauptartikel = $result['data']['product']['variants'][0]['id'];
            if($variantid == ''){
              $variantid = $variantidhauptartikel;
              $variantanlegen = false;
            }
            if(!isset($tmp[$i]['matrix_varianten'])){
              $this->FremdnummerInsert($artikel,$variantidhauptartikel,$bezvariant);
            }else{
              $matrixvarianthilfsid = $variantidhauptartikel;
            }
          }
        }else{
          $tempvar = $dataproduct['product']['variants'];
          if(isset($tmp[$i]['artikel_varianten'])){
            unset($dataproduct['product']['variants']);
          }
          $result = $this->adapter->call('products/'.$productid.'.json','PUT', $dataproduct);
          $this->ShopifyLog("Sendlist POST Artikel $nummer Product $productid",$result['data']);
          if(isset($tmp[$i]['artikel_varianten'])){
            if(isset($tmp[$i]['matrix_varianten'])){
              $matrixvarianthilfsid = $result['data']['product']['variants'][0]['id'];
            }
          }else{
            $tmpres = $this->adapter->call('variants/'.$variantid.'.json', 'PUT',array('variant' => $tempvar[0]));
            $this->ShopifyLog("Sendlist PUT Artikel $nummer Variant 0 $variantid", $tmpres['data']);
          }
        }
        $this->ImportSendListLager();

        $vorhandenemetafelder = $this->adapter->call("products/$productid/metafields.json", 'GET');
        $metafieldkeys = array();
        foreach ($vorhandenemetafelder['data']['metafields'] as $key => $value) {
          $metafieldkeys[$value['key']] = $value['id'];
        }

        foreach ($eigenschaften as $key => $value) {
          if($value['value']){
             $this->adapter->call("products/$productid/metafields.json", 'POST', array('metafield' => $value));
          }else{
            if(array_key_exists($value['key'], $metafieldkeys)){
              $metafieldid = $metafieldkeys[$value['key']];
              $this->adapter->call("metafields/$metafieldid.json", 'DELETE');
            }
          }
        }
        if($shopbilderuebertragen && isset($tmp[$i]['Dateien'])){
          $altebilder = $this->adapter->call("products/$productid/images.json");
          foreach ($altebilder['data']['images'] as $key => $value) {
            $this->adapter->call("products/$productid/images/".$value['id'].'.json', 'DELETE');
          }
          foreach ($tmp[$i]['Dateien'] as $key => $value) {
            $this->adapter->call("products/$productid/images.json", 'POST',array('image' => array('attachment' => $value['datei'], 'filename' => $value['filename'])));
          }
        }
      }

      if(isset($tmp[$i]['artikel_varianten'])){
        $productdata = $this->adapter->call('products/'.$productid.'.json');

        foreach ($tmp[$i]['artikel_varianten'] as $key => $value) {
          $variantid = $this->app->DB->Select("SELECT nummer FROM artikelnummer_fremdnummern WHERE artikel='".$value['artikel']."' AND bezeichnung='$bezvariant' AND shopid='$shopid' AND aktiv='1' LIMIT 1");
          if($variantid){
            $resultv = $this->adapter->call('variants/'.$variantid.'.json');
            if(isset($resultv['data']['errors'])){
              $this->app->DB->Delete("DELETE FROM artikelnummer_fremdnummern WHERE artikel='".$value['artikel']."' AND aktiv='1' AND shopid='$shopid' AND bezeichnung='$bezvariant'");
              $variantid = '';
            }
          }

          if(!$variantid){
            foreach ($productdata['data']['product']['variants'] as $varkey => $vardata){
              if($vardata['sku'] === $value['nummer']){
                $variantid = $vardata['id'];
                $this->FremdnummerInsert($value['artikel'],$variantid,$bezvariant);
                break;
              }
            }
          }

          if(!$variantid){
            $variantid = $variantidskumatching[$value['nummer']];
            if($variantid){
              $resultv = $this->adapter->call('variants/'.$variantid.'.json');
              if(isset($resultv['data']['errors'])){
                $this->app->DB->Delete("DELETE FROM artikelnummer_fremdnummern WHERE artikel='".$value['artikel']."' AND aktiv='1' AND shopid='$shopid' AND bezeichnung='$bezvariant'");
                $variantid = '';
              }else{
                $this->FremdnummerInsert($value['artikel'],$variantid,$bezvariant);
              }
            }
          }

          //Falls Artikel gesperrt oder inaktiv: Variante nicht übertragen, bzw. von Elternartikel entfernen 
          $deletevariant = false;
          if($value['gesperrt']){
            $deletevariant = true;
          }
          if($value['inaktiv']){
            $deletevariant = true;
          }
          if($deletevariant){
            if($variantid){
              $this->adapter->call("products/$productid/variants/$variantid.json", 'DELETE');
              $this->app->DB->Delete("DELETE FROM artikelnummer_fremdnummern WHERE artikel='".$value['artikel']."' AND aktiv='1' AND shopid='$shopid' AND bezeichnung='$bezvariant'");
              $this->ShopifyLog('Sendlist DELETE Variant '.$value['nummer'],json_decode($tmpres,true));
            }
            continue;
          }

          $lag = $value['lag'];
          $pseudolag = $value['pseudolager'];
          if($pseudolag  !== '') {
            $lag=$pseudolag;
          }
          $vabverkauf = $value['restmenge'];

          $datavariant = array();
          $titel = $value['name_de'];
          if($eigenschaftenuebertragen && $this->variantnameauseigenschaften){
            $eigenschaftenwerte = array();
            foreach ($value['eigenschaften'] as $ekey => $evalue){
              $eigenschaftenwerte[] = $evalue['values'];
            }
            if(count($eigenschaftenwerte) > 0){
              $titel = implode(' | ', $eigenschaftenwerte);
            }
          }
          $datavariant['title'] = $titel;
          $datavariant['sku'] = $value['nummer'];
          $weight = (float)$value['gewicht'];
          if(strpos($value['gewicht'], ',') !== false){
              $weight = (float)str_replace(',','.', $value['gewicht']);
          }
          if(!empty($weight)){
              $datavariant['weight'] = $weight;
          }
          $datavariant['barcode'] = $value['ean'];
          $datavariant['compare_at_price'] = round($value['pseudopreis'],2);
          $datavariant['price'] = (float)$value[$preisart];
          $datavariant['inventory_management'] = "shopify";
          $datavariant['inventory_policy'] = ($vabverkauf?"deny":"continue");
          $veigenschaften = array();
          $veigenschaften[] = array(
            "key" => "harmonized_system_code",
            "value"=> $value['zolltarifnummer'],
            "value_type"=> "string",
            "namespace"=> "global");
          $veigenschaften[] = [
            'key' => 'sync_status',
            'value' => 1,
            'value_type' => 'integer',
            'namespace' => 'xentral',
          ];

          $query = sprintf('SELECT uebersicht_en,metatitle_de,metadescription_de,metakeywords_de,name_en,metatitle_en,
            metadescription_en,metakeywords_en FROM artikel WHERE id=%d', $value['artikel']);
          $variantenArtikelDaten = $this->app->DB->SelectRow($query);
          $veigenschaften[] = array(
            'namespace' => 'global',
            'key' => 'metatitle_de',
            'value' => $variantenArtikelDaten['metatitle_de'],
            'value_type' => 'string');
          $veigenschaften[] = array(
            'namespace' => 'global',
            'key' => 'metadescription_de',
            'value' => $variantenArtikelDaten['metadescription_de'],
            'value_type' => 'string');
          $veigenschaften[] = array(
            'namespace' => 'global',
            'key' => 'metakeywords_de',
            'value' => $variantenArtikelDaten['metakeywords_de'],
            'value_type' => 'string');
          $veigenschaften[] = array(
            'namespace' => 'ml_en',
            'key' => 'title',
            'value' => $variantenArtikelDaten['name_en'],
            'value_type' => 'string');
          $veigenschaften[] = array(
            'namespace' => 'ml_en',
            'key' => 'description',
            'value' => htmlspecialchars_decode($variantenArtikelDaten['uebersicht_en']),
            'value_type' => 'string');
          $veigenschaften[] = array(
            'namespace' => 'ml_en',
            'key' => 'title',
            'value' => $variantenArtikelDaten['metatitle_en'],
            'value_type' => 'string');
          $veigenschaften[] = array(
            'namespace' => 'ml_en',
            'key' => 'meta_description',
            'value' => $variantenArtikelDaten['metadescription_en'],
            'value_type' => 'string');
          $veigenschaften[] = array(
            'namespace' => 'ml_en',
            'key' => 'metakeywords',
            'value' => $variantenArtikelDaten['metakeywords_en'],
            'value_type' => 'string');

          foreach ($value['freifelder'] as $iso => $ffdata){
            foreach ($ffdata as $ffkey => $ffvalue) {
              $vmetakey = substr($ffkey,0,30);
              $vmetavalue = $ffvalue;
              if($iso != 'DE'){
                $vmetakey = substr($iso."_".$ffvalue['mapping'],0,30);
                $vmetavalue = $ffvalue['wert'];
              }

              if($vmetavalue != ''){
                $veigenschaften[] = array(
                  'key' => $vmetakey,
                  'value' => $vmetavalue,
                  'value_type' => 'string',
                  'namespace' => 'variant');
              }
            }
          }

          if(count($value['eigenschaftenuebersetzungen'])>0){
            $eigenschaftenuebersetzungen = array();
            $eigenschaftenwerteuebersetzungen = array();
            foreach ($value['eigenschaftenuebersetzungen'] as $eukey => $euvalue) {
              $eigenschaftenuebersetzungen[$euvalue['language_from']."_".$euvalue['language_to']][$euvalue['property_from']] = $euvalue['property_to'];
              $eigenschaftenwerteuebersetzungen[$euvalue['language_from']."_".$euvalue['language_to']][$euvalue['property_value_from']] = $euvalue['property_value_to'];
            }
            foreach ($eigenschaftenwerteuebersetzungen as $eutranslationkey => $eutranslationvalues) {
              if($eutranslationkey!= ''){
                foreach ($eutranslationvalues as $eufrom => $euto) {
                  if($eufrom != '' && $euto != ''){
                    $veigenschaften[] = array(
                    'key' => substr($eufrom,0,30),
                    'value' => $euto,
                    'value_type' => 'string',
                    'namespace' => 'vtranslation_'.$eutranslationkey);
                  }
                }
              }
            }
            foreach ($eigenschaftenuebersetzungen as $eutranslationkey => $eutranslationvalues) {
              if($eutranslationkey!= ''){
                foreach ($eutranslationvalues as $eufrom => $euto) {
                  if($eufrom != '' && $euto != ''){
                    $veigenschaften[] = array(
                    'key' => substr($eufrom,0,30),
                    'value' => $euto,
                    'value_type' => 'string',
                    'namespace' => 'vktranslation_'.$eutranslationkey);
                  }
                }
              }
            }
          }
          if(array_key_exists($value['artikel'], $tmp[$i]['matrix_varianten']['artikel'])){
            $counter = 1;
            foreach ($tmp[$i]['matrix_varianten']['artikel'][$value['artikel']] as $mvalue) {
              if($counter <= 3){
                $datavariant['option'.$counter] = $mvalue['values'];
              }
              $counter +=1;
            }
          }else{
            $datavariant['option1'] = $titel;
          }

          $result = null;
          if($variantid == ''){
            $result = $this->adapter->call('products/'.$productid.'/variants.json', 'POST',array('variant' => $datavariant));
            $this->ShopifyLog("Sendlist POST Variant ".$value['nummer'],$result['data']);
            if(isset($result['data']['errors']) && $matrixvarianthilfsid != ''){
              $result = $this->adapter->call('variants/'.$matrixvarianthilfsid.'.json', 'PUT',array('variant' => $datavariant));
              $this->ShopifyLog("Sendlist PUT A Variant ".$value['nummer'],$result['data']);
              $variantid = $matrixvarianthilfsid;
              $matrixvarianthilfsid = '';
            }else{
              $variantid = $result['data']['variant']['id'];
            }
            $this->FremdnummerInsert($value['artikel'],$variantid,$bezvariant);
          }else{
            $result = $this->adapter->call('variants/'.$variantid.'.json', 'PUT',array('variant' => $datavariant));
            $this->ShopifyLog("Sendlist PUT B Variant ".$value['nummer'],$result['data']);
          }

          if(!empty($variantid) && !empty($result['data']['variant']['inventory_item_id'])){
            $inventoryitemid = $result['data']['variant']['inventory_item_id'];
            $resulti = $this->adapter->call("inventory_levels.json?inventory_item_ids=$inventoryitemid&location_ids=$locationid");
            if(!is_array($resulti['data'])){
              $this->ShopifyLog('Shopify Lagerzahlübertragung Fehler für Artikel: '.$value['nummer'], $resulti['data']);
              return 'error: '.$resulti['data'];
            }
            $vorhanden = $resulti['data']['inventory_levels'][0]['available'];
            $adjust = $lag - $vorhanden;
            if($adjust != 0){
              $data = array("location_id" => $locationid,
                "inventory_item_id"=> $inventoryitemid,
                "available_adjustment"=> $adjust);
              $result = $this->adapter->call('inventory_levels/adjust.json', 'POST',$data);
              $this->ShopifyLog("Sendlistlager A Variant $variantid",$result['data']);
            }
          }

          if($shopbilderuebertragen && $variantid != ''){
            if(isset($value['Dateien'])){
              $altebilder = $this->adapter->call("variants/$variantid/images.json");
              foreach ($altebilder['data']['images'] as $ikey => $ivalue) {
                $this->adapter->call("variants/$variantid/images/".$ivalue['id'].'.json', 'DELETE');
              }
              $cdateien = !empty($value['Dateien']['filename'])?count($value['Dateien']['filename'])-1:0;
              for ($im = $cdateien; $im >= 0; $im--) {
                $this->adapter->call("products/$productid/images.json", 'POST',array('image' => array('variant_ids' => array($variantid),'attachment' => $value['Dateien']['datei'][$im], 'filename' => $value['Dateien']['filename'][$im])));
              }
            }
          }

          $vorhandenemetafeldervariant = $this->adapter->call("variants/$variantid/metafields.json", 'GET');
          $metafieldkeysvariant = array();
          foreach ($vorhandenemetafeldervariant['data']['metafields'] as $vmkey => $vmvalue) {
            $metafieldkeysvariant[$vmvalue['key']] = $vmvalue['id'];
          }
          foreach ($veigenschaften as $vekey => $vevalue) {
            if($vevalue['value'] != ''){
              $this->adapter->call("variants/$variantid/metafields.json", 'POST', array('metafield' => $vevalue));
            }else{
              if(array_key_exists($vevalue['key'], $metafieldkeysvariant)){
                $metafieldid = $metafieldkeysvariant[$vevalue['key']];
                $this->adapter->call("metafields/$metafieldid.json", 'DELETE');
              }
            }
          }
        }

        if(!isset($tmp[$i]['matrix_varianten'])){
          //Variante 0 entfernen, falls vorhanden:
          if($variantidhauptartikel != ''){
            $this->adapter->call("variants/$variantidhauptartikel.json", 'DELETE');
            $this->app->DB->Delete("DELETE FROM artikelnummer_fremdnummern WHERE artikel='$artikel' AND aktiv='1' AND shopid='$shopid' AND bezeichnung='$bezvariant' AND nummer = '$variantidhauptartikel'");
          }
        }
      }

      if($inaktiv){
        $this->adapter->call("product_listings/$productid.json", 'DELETE');
      }else{
        $this->adapter->call("product_listings/$productid.json", 'PUT');
      }

      $productTranslations = [];

        $productTranslations['de'] = [
          'title' => $name_de,
          'meta_title' => $metatitle_de,
          'body_html' => htmlspecialchars_decode($tmp[0]['uebersicht_de']),
          'meta_description' => $metadescription_de
      ];

        $productTranslations['en'] = [
          'title' => $name_en,
          'meta_title' => $metatitle_en,
          'body_html' => htmlspecialchars_decode($tmp[0]['uebersicht_en']),
          'meta_description' => $metadescription_en
      ];

      foreach ($tmp[0]['texte'] as $explicitTranslation){
          $productTranslations[strtolower($explicitTranslation['sprache'])] = [
              'title' => $explicitTranslation['name'],
              'meta_title' => $explicitTranslation['meta_title'],
              'body_html' => $explicitTranslation['beschreibung_online'],
              'meta_description' => $explicitTranslation['meta_description']
          ];
      }

      $this->translateProduct($productid, $productTranslations);

      if(!empty($variantTranslations = $tmp[$i]['matrix_varianten']['texte'])){
        $this->translateVariants($productid, $variantTranslations = $tmp[$i]['matrix_varianten']['texte']);
      }

      $freeFieldTranslations = [];
      foreach ($tmp[0]['freifelder'] as $iso => $freeFields){
        if($iso === 'DE'){
          continue;
        }
        foreach ($freeFields as $freeField){
            $freeFieldTranslations[$freeField['mapping']][strtolower($iso)] = $freeField['wert'];
        }
      }
      $this->translateFreeFields($productid, $freeFieldTranslations);

      $anzahl++;
    }
    return $anzahl;
  }


  // delete an article
  function ImportDeleteArticle()
  {
    return;
  }

  function DumpVar($variable)
  {
  }

  // receive all new articles
  public function ImportArtikelgruppen()
  {
    return;
    $tmp = $this->CatchRemoteCommand('data');
    $anzahl = 0;
    $this->app->DB->Delete("DELETE FROM artikelgruppen");
    $ctmp = !empty($tmp)?count($tmp):0;
    for($i=0;$i<$ctmp;$i++)
    {
      $id = $tmp[$i]['id'];

      $this->app->DB->Insert("INSERT INTO artikelgruppen (id) VALUES ('$id')");

      foreach($tmp[$i] as $key=>$value)
      {
        $this->app->DB->Update("UPDATE artikelgruppen SET $key='$value' WHERE id='$id' LIMIT 1");
      }

      $anzahl++;
    }
    return $anzahl;
  }


  //get checksum list from onlineshop
  public function ImportGetAuftraegeAnzahl()
  {
    $tmp = $this->CatchRemoteCommand('data');
    if(!empty($tmp['ab_nummer'])){
      $tmp['ab_nummer']--;
    }
    if(!empty($tmp['ab_nummer']))
    {
      if(!empty($tmp['holeallestati']) && $tmp['holeallestati'] == 1)
      {
        $result = $this->adapter->call('orders.json?status=any&since_id='.$tmp['ab_nummer'].'&limit=25');
      }else{
        $result = $this->adapter->call('orders.json?fulfillment_status=unshipped&since_id='.$tmp['ab_nummer'].'&limit=25');
        if(!(isset($result['data']['orders']) && count($result['data']['orders']) >= 25) && $this->partial)
        {
          $result2 = $this->adapter->call('orders.json?fulfillment_status=partial&since_id='.$tmp['ab_nummer'].'&limit=25');
          if(isset($result2['data']['orders']))
          {
            return count($result['orders'])+count($result2['data']['orders']);
          }

          return count($result['orders']);
        }
      }
      if(isset($result['data']['orders']))
      {
        return count($result['data']['orders']);
      }

      return 0;

    }elseif(!empty($tmp['nummer']))
    {
      $result = $this->adapter->call('orders.json?status=any&ids='.$tmp['nummer']);
      if(!empty($result['data']['orders']))
      {
        return 1;
      }

      return 0;
    }else{
      if((int)$tmp['count'] >= 25 || !$this->partial){
        return $tmp['count'];
      }
      $result2 = $this->adapter->call('orders/count.json?fulfillment_status=partial&financial_status=paid&limit=25');
      return (int)$tmp['count']+(int)$result2['data']['count'];
    }

  }

	private function validateDate($date, $format='Y-m-d H:i:s')
	{
		if(strpos($date, '.') !== False){
			$date = strstr($date, '.', True);
		}
		$d = DateTime::createFromFormat($format, $date);
		$valid = ($d && ($d->format($format) === $date));
		if(($valid === False) && ($date != '0000-00-00 00:00:00') && ($date != NULL) && ($date != '')){
			if($this->Debug){
			  $this->DumpVar("ERROR: Falsche Datumsangabe: $date");
      }
			$this->error[] = "ERROR: Falsche Datumsangabe: $date";
		}

		return $valid;
	}

  
  function CheckOldAuftrag($anz = 50)
  {
    $arr = $this->app->DB->SelectArr("SELECT id,extid FROM shopimporter_shopify_auftraege WHERE shop = '".$this->shopid."' AND extid <> '' AND  transaction_id = '' AND getestet = 0 ORDER BY id LIMIT $anz");
    if(!$arr){
      return;
    }
    foreach($arr as $v)
    {
      $i = 0;
      $checkid = $v['id'];
      $_tmp['nummer'] = $v['extid'];
      $result = $this->adapter->call('orders.json?status=any&ids='.$_tmp['nummer']);

      if(!isset($result['data']['orders']) || !isset($result['data']['orders'][$i]['id']) || !$result['data']['orders'][$i]['id'])
      {
        
      }else{
        $transactionsarr = $this->adapter->call('orders/'.$result['data']['orders'][$i]['id'].'/transactions.json');
        if(isset($transactionsarr['data']['transactions']) && isset($transactionsarr['data']['transactions'][0]))
        {
          $transactionsarr = $transactionsarr['data']['transactions'][0];
          if(isset($transactionsarr['data']['authorization']) && (String)$transactionsarr['data']['authorization'] !== '')
          {
            if(isset($transactionsarr['data']['receipt']) && isset($transactionsarr['data']['receipt']['transaction_id']) && (String)$transactionsarr['data']['receipt']['transaction_id'] !== '')
            {
              $transaction_id = $this->app->DB->real_escape_string($transactionsarr['data']['receipt']['transaction_id']);
            }else{
              $transaction_id = $this->app->DB->real_escape_string($transactionsarr['data']['authorization']);
            }
            $this->app->DB->Update("UPDATE shopimporter_shopify_auftraege SET transaction_id = '$transaction_id',zahlungsweise ='".$this->app->DB->real_escape_string((String)$result['orders'][$i]['gateway'])."' WHERE id = '$checkid' LIMIT 1");
          }
        }
      }
      $this->app->DB->Update("UPDATE shopimporter_shopify_auftraege SET getestet = 1 WHERE id = '$checkid' LIMIT 1");
    }
  }

  protected function log(string $message, array $context = [])
  {
    if(!$this->logging){
      return;
    }
    $this->logger->debug($message, $context);
  }
  
  public function ImportGetAuftrag()
  {
    $this->log('Shopify Import started');
    $alleabholen = false;
    $_tmp = $this->CatchRemoteCommand('data');
    if(!empty($_tmp['archive']))
    {
      $this->archive = true;
    }

    $minTime = $this->validateDate($_tmp['datumvon'])
      ? $_tmp['datumvon']
      : 'now - 90 days';

    $von = new DateTime($minTime, new DateTimeZone($this->timezone));
    $von->setTimeZone(new DateTimeZone('UTC'));
    $von = $von->format('Y-m-d\\TH:i:s\\Z');

    $maxTime = $this->validateDate($_tmp['datumbis'])
      ? $_tmp['datumbis']
      : 'now';

    $bis = new DateTime($maxTime, new DateTimeZone($this->timezone));
    $bis->setTimeZone(new DateTimeZone("UTC"));
    $bis = $bis->format("Y-m-d\TH:i:s\Z");

		$bismax = new DateTime("now", new DateTimeZone($this->timezone));
		$bismax->setTimeZone(new DateTimeZone("UTC"));
		$bismax = $bismax->format("Y-m-d\TH:i:s\Z");
		if(strtotime($bis) > strtotime($bismax)){
		  $bis = $bismax;
    }
    $demomodus = $this->app->DB->Select("SELECT demomodus FROM shopexport WHERE id = '".$this->shopid."' LIMIT 1");
    
		// Wenn man keinen kleinen timeout eingestellt hat und kein Problem damit das es etwas länger dauert,
		// kann man hier die Anzahl der Aufträge die auf eimal abgeholt werden sollen einstellen.
		$anzGleich = empty($_tmp['anzgleichzeitig'])? 0 : (int)$_tmp['anzgleichzeitig'];

    $exitzeitstempel = null;
    if($anzGleich < 1){
      $anzGleich = 1;
    }
    $_anzGleich = $anzGleich;
		if($anzGleich >= 50){
			$anzGleich = 100;
		}
    if($anzGleich == 1){
      $anzGleich = 100;
    }

    if(empty($_tmp['nummer']))
    {
      $this->CheckOldAuftrag();
      $paid = 'paid';
      if($this->gotpendig){
        $paid = 'paid,pending';
      }
      if($alleabholen)
      {
        $result = $this->adapter->call("orders.json?financial_status=$paid&limit=".$anzGleich."&updated_at_min=".$von."&updated_at_max=".$bis);
        $result = $result['data'];
      }else{
        $result = $this->adapter->call("orders.json?fulfillment_status=unshipped&financial_status=$paid&limit=".$anzGleich."&updated_at_min=".$von."&updated_at_max=".$bis);
        $result = $result['data'];
        if($this->partial)
        {
          $result2 = $this->adapter->call("orders.json?fulfillment_status=partial&financial_status=$paid&limit=".$anzGleich."&updated_at_min=".$von."&updated_at_max=".$bis);
          $result2 = $result2['data'];
          if(count($result2['orders']) > 0 && count($result['orders']) == 0)
          {
            $result = $result2;
          }elseif(count($result2['orders']) > 0 && count($result['orders']) > 0)
          {
            $result['orders'] = array_merge($result['orders'], $result2['orders']);
            unset($result2);
          }
        }
      }
      $this->log('Shopify Log at LOC 1649',[
        'querystring' => "1limit=".$anzGleich."&updated_at_min=".$von."&updated_at_max=".$bis
      ]);
      if(count($result['orders']) == 0)
      {
        $this->log('Finished: order count 0');
        return;
      }
      $maxzaehler = 40;

      $startdate = $this->app->DB->Select(
        "SELECT `startdate` FROM shopexport WHERE `id` = {$this->shopid} AND startdate!= '0000-00-00'"
      );
      $startdate = empty($startdate)?0:(int)strtotime($startdate);

      //$erg[] = array(0,count($result['orders']),"/admin/orders.json?fulfillment_status=shipped&status=any&financial_status=paid&limit=".$anzGleich."&updated_at_min=".$von."&updated_at_max=".$bis);
      while(count($result['orders']) >= $anzGleich || count($result['orders']) == 0)
      {
        $maxzaehler--;
        if($maxzaehler <= 0)
        {
          if($exitzeitstempel)
          {
            $this->app->DB->Insert("INSERT INTO `shopexport_log` (`shopid`,`typ`,`bearbeiter`,`parameter1`,`parameter2`) VALUES ('".$this->shopid."','GetAuftrag','".$this->bearbeiter."','Es sind Auftr&auml;ge vorhanden, wurden aber nicht abgeholt','')");
            
            date_default_timezone_set($this->timezone);
            $this->log('Shopify import finished LOC: 1675', ['zeitstempel'=>date("Y-m-d H:i:s",strtotime($exitzeitstempel))]);

            return array('zeitstempel'=>date("Y-m-d H:i:s",strtotime($exitzeitstempel)));
          }else{
            return '';
          }
        }
        if(count($result['orders']) == 0)
        {
          date_default_timezone_set('UTC');
          $von = !empty($zwischen)?$zwischen:$von;
          $_von = strtotime($von);
          $_bis = strtotime($bis);
          $_zwischen = ceil(($_bis + $_von)/2);
          $zwischen = date('Y-m-d\TH:i:s\Z', $_zwischen);
          if($alleabholen)
          {
            $result = $this->adapter->call("orders.json?financial_status=$paid&limit=".$anzGleich."&updated_at_min=".$von."&updated_at_max=".$zwischen);
            $result = $result['data'];
          }else{
            $result = $this->adapter->call("orders.json?fulfillment_status=unshipped&financial_status=$paid&limit=".$anzGleich."&updated_at_min=".$von."&updated_at_max=".$zwischen);
            $this->log('Shopify Log at LOC: 1696', ['querystring'=> "2limit=".$anzGleich."&updated_at_min=".$von."&updated_at_max=".$zwischen]);
            $result = $result['data'];

            if($this->partial)
            {
              $result2 = $this->adapter->call("orders.json?fulfillment_status=partial&financial_status=$paid&limit=".$anzGleich."&updated_at_min=".$von."&updated_at_max=".$zwischen);
              $result2 = $result2['data'];

              if(count($result2['orders']) > 0 && count($result['orders']) == 0)
              {
                $result = $result2;
              }elseif(count($result2['orders']) > 0 && count($result['orders']) > 0)
              {
                $result['orders'] = array_merge($result['orders'], $result2['orders']);
                unset($result2);
              }
            }
          }

          $this->log('Shopify Log at LOC 1715', [
            'von' => date('Y-m-d\TH:i:s\Z', $von),
            'zwischen' => date('Y-m-d\TH:i:s\Z', $zwischen),
            'bis' => date('Y-m-d\TH:i:s\Z', $bis),
          ]);

          $erg[] = array(3,count($result['orders']), "orders/count.json?fulfillment_status=shipped&status=any&financial_status=$paid&updated_at_min=".$von."&updated_at_max=".$zwischen);
          if((count($result['orders']) < $anzGleich) && count($result['orders']) > 0){
            break;
          }
          if(count($result['orders']) == 0){
            $exitzeitstempel = $zwischen;
          }
        }else{
          date_default_timezone_set('UTC');
          $_von = strtotime($von);
          if(isset($zwischen))
          {
            $_bis = strtotime($zwischen);
          }else{
            $_bis = strtotime($bis);
          }
          $_zwischen = ceil(($_bis + $_von)/2);
          $zwischen = date('Y-m-d\TH:i:s\Z', $_zwischen);
          if($alleabholen)
          {
            $result = $this->adapter->call("orders.json?financial_status=$paid&limit=".$anzGleich."&updated_at_min=".$von."&updated_at_max=".$zwischen);
            $result = $result['data'];

          }else{
            $result = $this->adapter->call("orders.json?fulfillment_status=unshipped&financial_status=$paid&limit=".$anzGleich."&updated_at_min=".$von."&updated_at_max=".$zwischen);
            $result = $result['data'];

            if($this->partial)
            {
              $result2 = $this->adapter->call("orders.json?fulfillment_status=partial&financial_status=$paid&limit=".$anzGleich."&updated_at_min=".$von."&updated_at_max=".$zwischen);
              $result2 = $result2['data'];

              if(count($result2['orders']) > 0 && count($result['orders']) == 0)
              {
                $result = $result2;
              }elseif(count($result2['orders']) > 0 && count($result['orders']) > 0)
              {
                $result['orders'] = array_merge($result['orders'], $result2['orders']);
                unset($result2);
              }
            }
          }

          $this->log('Shopify Log at LOC: 1764', [
            'querystring' => "3limit=".$anzGleich."&updated_at_min=".$von."&updated_at_max=".$zwischen,
            'von' => date('Y-m-d\TH:i:s\Z', $_von),
            'zwischen' => date('Y-m-d\TH:i:s\Z', $zwischen),
            'bis' => date('Y-m-d\TH:i:s\Z', $_bis),
          ]);

          if((count($result['orders']) < $anzGleich) && count($result['orders']) > 0){
            break;
          }
          if(count($result['orders']) == 0){
            $exitzeitstempel = $zwischen;
          }
        }
      }
    }else{
      if(!empty($_tmp['ab_nummer'])){
        $_tmp['ab_nummer']--;
      }
      if(!empty($_tmp['ab_nummer']))
      {
        if(!empty($_tmp['holeallestati']) && $_tmp['holeallestati'] == 1)
        {
          $result = $this->adapter->call('orders.json?status=any&since_id='.$_tmp['ab_nummer']);
          $result = $result['data'];

        }else{
          $result = $this->adapter->call('orders.json?fulfillment_status=unshipped&since_id='.$_tmp['ab_nummer']);
          $result = $result['data'];

          if($this->partial)
          {
            $result2 = $this->adapter->call('orders.json?fulfillment_status=partial&since_id='.$_tmp['ab_nummer']);
            $result2 = $result2['data'];

            if(count($result2['orders']) > 0 && count($result['orders']) == 0)
            {
              $result = $result2;
            }elseif(count($result2['orders']) > 0 && count($result['orders']) > 0)
            {
              $result['orders'] = array_merge($result['orders'], $result2['orders']);
              unset($result2);
            }
          }
        }
      }elseif(!empty($_tmp['nummer']))
      {
        $result = $this->adapter->call('orders.json?status=any&ids='.$_tmp['nummer']);
        $result = $result['data'];
      }else{
        $result = $this->adapter->call('orders.json?fulfillment_status=unshipped&financial_status=paid&limit=1');
        $result = $result['data'];

        if($this->partial)
        {
          $result2 = $this->adapter->call('orders.json?fulfillment_status=partial&financial_status=paid&limit=1');
          $result2 = $result2['data'];

          if(count($result2['orders']) > 0 && count($result['orders']) == 0)
          {
            $result = $result2;
          }elseif(count($result2['orders']) > 0 && count($result['orders']) > 0)
          {
            $result['orders'] = array_merge($result['orders'], $result2['orders']);
            unset($result2);
          }
        }
        
      }
      
    }
    
    if($anzGleich >= 1)
    {
      $anzahl = count($result['orders']);
    }else{
      $anzahl = 1;
    }

    if(($_anzGleich == 1 || $demomodus) && $anzahl > 1){
      $anzahl = 1;
    }
    $j = -1;
    for($i = 0; $i < $anzahl; $i++)
    {
      $auftrag = (String)$result['orders'][$i]['id'];
      if($auftrag === ''){
        continue;
      }
      
      $this->app->DB->Select('SELECT transaction_id FROM shopimporter_shopify_auftraege LIMIT 1');
      if($this->app->DB->error())
      {
        $this->Install();
      }
      
      if(!$this->archive && ($checkid = $this->app->DB->Select("SELECT id FROM `".$this->table."` WHERE extid = '$auftrag' AND extid <> '' AND shop = '".$this->shopid."' LIMIT 1")))// && (($i < $anzahl - 1) || $j > -1))
      {
        if($result['orders'][$i]['updated_at'])
        {
          if($exitzeitstempel)
          {
            if(strtotime($exitzeitstempel) < strtotime($result['orders'][$i]['updated_at'])){
              $exitzeitstempel = $result['orders'][$i]['updated_at'];
            }
          }else{
            $exitzeitstempel = $result['orders'][$i]['updated_at'];
          }
        }
        if($this->app->DB->Select("SELECT id FROM shopimporter_shopify_auftraege WHERE id = '$checkid' AND transaction_id = '' LIMIT 1"))
        {
          $transactionsarr = $this->adapter->call('orders/'.$result['orders'][$i]['id'].'/transactions.json');
          $transactionsarr = $transactionsarr['data'];
          if(isset($transactionsarr['transactions']) && isset($transactionsarr['transactions'][0]))
          {
            $transactionsarr = $transactionsarr['transactions'][0];
            if(isset($transactionsarr['authorization']) && (String)$transactionsarr['authorization'] !== '')
            {
              if(isset($transactionsarr['receipt']) && isset($transactionsarr['receipt']['transaction_id']) && (String)$transactionsarr['receipt']['transaction_id'] !== '')
              {
                $transaction_id = $this->app->DB->real_escape_string($transactionsarr['receipt']['transaction_id']);
              }else{
                $transaction_id = $this->app->DB->real_escape_string($transactionsarr['authorization']);
              }
              $this->app->DB->Update("UPDATE shopimporter_shopify_auftraege SET transaction_id = '$transaction_id',zahlungsweise ='".$this->app->DB->real_escape_string((String)$result['orders'][$i]['gateway'])."' WHERE id = '$checkid' LIMIT 1");
            }
          }
        }
        continue;
      }
      if(!$this->archive && ($result['orders'][$i]['fulfillment_status'] === 'fulfilled' && empty($_tmp['nummer'])))
      {
        continue;
      }

      $purchaseDate = substr($result['orders'][$i]['created_at'],0,10);
      if($purchaseDate > 0 && $startdate > strtotime($purchaseDate) && $startdate > 0) {
        continue;
      }

      $j++;
      $warenkorb = [];
      $warenkorb2 = [];

      //if($this->app->Conf->Debug)$warenkorb['result'] = $result;
      $warenkorb['auftrag'] = $result['orders'][$i]['id'];
      $warenkorb['auftragsdaten'] = $result['orders'][$i];
      $warenkorb['orderData'] = $result['orders'][$i];
      date_default_timezone_set($this->timezone);
      $warenkorb['zeitstempel'] = date('Y-m-d H:i:s',strtotime($result['orders'][$i]['updated_at']));
      $warenkorb['gesamtsumme'] = $result['orders'][$i]['total_price'];
      
      if(!empty($result['orders'][$i]['note_attributes']) &&
        is_array($result['orders'][$i]['note_attributes']) &&
        count($result['orders'][$i]['note_attributes']) > 0)
      {
        foreach($result['orders'][$i]['note_attributes'] as $kn => $vn)
        {
          if(isset($vn['name']) && isset($vn['value']) && $vn['name'] === 'vat_id')
          {
            $warenkorb['ustid'] = (String)$vn['value'];
          } elseif ($vn['name'] === 'Delivery-Date' && $vn['value']) {
            try{
              $wunschlieferdatum = DateTime::createFromFormat('d/m/Y', (String)$vn['value']);
              $warenkorb['lieferdatum'] = $wunschlieferdatum->format('Y-m-d');
              unset($result['orders'][$i]['note_attributes'][$kn]);
            }catch (Exception $x){

            }
          }
        }
      }
      $steuersaetze = [];
      //$warenkorb['result'] = $result;
      $warenkorb['transaktionsnummer'] = $result['orders'][$i]['token'];

      $transactionsarr = $this->adapter->call('/orders/'.$result['orders'][$i]['id'].'/transactions.json');
      $transactionsarr = $transactionsarr['data'];
      $warenkorb['orderData']['transactions'] = $transactionsarr;
      $warenkorb['zahlungsweise'] = (string)$result['orders'][$i]['gateway'];
      if(isset($transactionsarr['transactions']) && isset($transactionsarr['transactions'][0])) {
        foreach($transactionsarr['transactions'] as $transAction) {
          if(isset($transAction['authorization']) && (string)$transAction['authorization'] !== ''){
            if(!empty($transAction['status']) && $transAction['status'] === 'failure') {
              continue;
            }
            $warenkorb['transaktionsnummer'] = $transAction['authorization'];
            if(!empty($transAction['receipt']) && isset($transAction['receipt']['transaction_id']) && (string)$transAction['receipt']['transaction_id'] !== ''){
              $warenkorb['transaktionsnummer'] = $transAction['receipt']['transaction_id'];
            }
            if(!empty($transAction['receipt']['user_variable_0']) && $transAction['authorization'] === $transAction['receipt']['transaction']){
              $warenkorb['transaktionsnummer'] = $transAction['receipt']['user_variable_0'];
            }
          }
          if(
            in_array($result['orders'][$i]['financial_status'], ['pending', 'paid'])
            && !empty($transAction['gateway'])
            && !empty($transAction['status'])
            && $transAction['status'] === $result['orders'][$i]['financial_status']
          ) {
            $warenkorb['zahlungsweise'] = (string)$transAction['gateway'];
            $warenkorb['transaktionsnummer'] = $transAction['authorization'];
            if(!empty($transAction['receipt']) && isset($transAction['receipt']['transaction_id']) && (string)$transAction['receipt']['transaction_id'] !== ''){
              $warenkorb['transaktionsnummer'] = $transAction['receipt']['transaction_id'];
            }
            if(!empty($transAction['receipt']['user_variable_0']) && $transAction['authorization'] === $transAction['receipt']['transaction']){
              $warenkorb['transaktionsnummer'] = $transAction['receipt']['user_variable_0'];
            }
            if($transAction['status'] === 'paid') {
              break;
            }
          }
        }
        unset($transAction);
      }
      unset($transactionsarr);
      $warenkorb['onlinebestellnummer'] = $result['orders'][$i]['name'];
      if(empty($warenkorb['onlinebestellnummer'])){
        $warenkorb['onlinebestellnummer'] = $result['orders'][$i]['order_number'];
      }
      $taxes_included = (int)$result['orders'][$i]['taxes_included'];
      if($taxes_included)
      {
        $taxFromShippingCountry = !empty(
          $this->app->DB->Select(
            sprintf(
              'SELECT `steuerfreilieferlandexport` FROM `shopexport` WHERE `id` = %d',
              $this->shopid
            )
          )
        );
        $addressField = $taxFromShippingCountry?'shipping_address':'billing_address';
        $isTaxFree = $this->app->erp->Export($result['orders'][$i][$addressField]['country_code']);
        if(!empty($warenkorb['ustid']) && !$isTaxFree) {
          $isTaxFree = $this->app->erp->IsEU($result['orders'][$i][$addressField]['country_code']);
        }
        if($result['orders'][$i]['total_tax'] == 0 && $isTaxFree){
          $warenkorb['versandkostennetto'] = $result['orders'][$i]['shipping_lines'][0]['price'];
        }else{
          $warenkorb['versandkostenbrutto'] = $result['orders'][$i]['shipping_lines'][0]['price'];
        }
      }else{
        if(isset($result['orders'][$i]['shipping_lines'][0]['tax_lines']) && isset($result['orders'][$i]['shipping_lines'][0]['tax_lines'][0]) && isset($result['orders'][$i]['shipping_lines'][0]['tax_lines'][0]['price']) && $result['orders'][$i]['shipping_lines'][0]['tax_lines'][0]['price'] > 0)
        {
          if(isset($result['orders'][$i]['shipping_lines'][0]['tax_lines'][0]['rate']) && $result['orders'][$i]['shipping_lines'][0]['tax_lines'][0]['rate'] > 0 && $result['orders'][$i]['shipping_lines'][0]['tax_lines'][0]['rate'] <= 0.1)
          {
            $warenkorb['portosteuersatz'] = 'ermaessigt';
          }
          if(isset($result['orders'][$i]['shipping_lines'][0])){
            $warenkorb['versandkostenbrutto'] = $result['orders'][$i]['shipping_lines'][0]['price']*(1+$result['orders'][$i]['shipping_lines'][0]['tax_lines'][0]['rate']);
          }
        }else{
          if(isset($result['orders'][$i]['shipping_lines'][0])){
            $warenkorb['versandkostennetto'] = $result['orders'][$i]['shipping_lines'][0]['price'];
          }
        }
      }
      //$warenkorb[versandkostennetto] = $result[data][invoiceShippingNet]; //TODO

      $artikelsteuer = 0;
      foreach ($result['orders'][$i]['line_items'] as $likey => $livalue) {
        if($livalue['tax_lines'][0]['rate'] > $artikelsteuer){
          $artikelsteuer = $livalue['tax_lines'][0]['rate'];
        }
      }
      if($artikelsteuer < 0.1 && $artikelsteuer> 0){
        $warenkorb['portosteuersatz'] = 'ermaessigt';
      }

      if(isset($result['orders'][$i]['shipping_lines'][0])){
        $warenkorb['lieferung'] = (String)$result['orders'][$i]['shipping_lines'][0]['carrier_identifier'];
      }
      if(isset($result['orders'][$i]['shipping_lines'][0]) && $warenkorb['lieferung'] === ''){
        $warenkorb['lieferung'] = (String)$result['orders'][$i]['shipping_lines'][0]['title'];
      }
      $warenkorb['rabattnetto'] = -abs($result['orders'][$i]['total_discounts']);
      $warenkorb['rabattsteuer'] = 0;

      if($result['orders'][$i]['billing_address']['company']!='')
      {
        $warenkorb['name'] = $result['orders'][$i]['billing_address']['company'];
        $warenkorb['ansprechpartner'] = $result['orders'][$i]['billing_address']['name'];
        if($this->app->DB->Select("SELECT id FROM adresse_typ WHERE type = 'firma' AND aktiv = 1 AND geloescht = 0 LIMIT 1")){
          $warenkorb['anrede'] = 'firma';
        }
      } else {
        $warenkorb['name'] = $result['orders'][$i]['billing_address']['name'];
        $warenkorb['ansprechpartner'] = '';
        $projekt = $this->app->DB->Select("SELECT projekt FROM shopexport WHERE id = '".$this->shopid."' LIMIT 1");
        if($this->app->DB->Select("SELECT id FROM adresse_typ WHERE type = 'privat' AND aktiv = 1 AND geloescht = 0 AND (projekt = 0 OR projekt = '".$projekt."') LIMIT 1")){
          $warenkorb['anrede'] = 'privat';
        }
      }
      
      if(strlen($result['orders'][$i]['billing_address']['address2'])<5){
        $warenkorb['strasse'] = $result['orders'][$i]['billing_address']['address1'] . ' ' . $result['orders'][$i]['billing_address']['address2'];
      }
      else {
        $warenkorb['strasse'] = $result['orders'][$i]['billing_address']['address1'];
        $warenkorb['adresszusatz'] = $result['orders'][$i]['billing_address']['address2'];
      }

      $warenkorb['plz'] = $result['orders'][$i]['billing_address']['zip'];
      $warenkorb['ort'] = $result['orders'][$i]['billing_address']['city'];
      $warenkorb['land'] = $result['orders'][$i]['billing_address']['country_code'];
      $warenkorb['email'] = $result['orders'][$i]['contact_email'];
      if(isset($result['orders'][$i]['billing_address']['province']) && $result['orders'][$i]['billing_address']['province'] != '')
      {
        $warenkorb['bundesstaat'] = $result['orders'][$i]['billing_address']['province'];
      }

      $warenkorb['waehrung'] = $result['orders'][$i]['currency'];

      $warenkorb['bestelldatum'] = substr($result['orders'][$i]['created_at'],0,10);

      $warenkorb['telefon'] = $result['orders'][$i]['billing_address']['phone'];

      if(!empty($result['orders'][$i]['note'])){
        $warenkorb['freitext'] = (String)$result['orders'][$i]['note'];
      }
      if(empty($warenkorb['freitext']) && !empty($result['orders'][$i]['note_attributes'][0])
         && (String)$result['orders'][$i]['note_attributes'][0]['value'] != '' 
         && (String)$result['orders'][$i]['note_attributes'][0]['value'] != 'true' 
         && (String)$result['orders'][$i]['note_attributes'][0]['value'] != 'false'){
        $warenkorb['freitext'] = (String)$result['orders'][$i]['note_attributes'][0]['value'];
      }

      //$warenkorb[telefax] = $result[data][billing][fax]; //TODO
      //$warenkorb[ustid] = $result[data][billing][vatId]; //TODO
      //$warenkorb[anrede]="firma"; //TODO

      //if(!$warenkorb['subshop'])$warenkorb['subshop'] = $result[data][customer][shopId];
      //$warenkorb[abteilung] = $result[data][billing][department];
      //$warenkorb[steuerfrei] = $result[data][taxFree];

      //$warenkorb[unterabteilung] = $result[data][billing][additionalAddressLine1]; //TODO

      if($result['orders'][$i]['shipping_address']['company']!='')
      {
        $warenkorb2['lieferadresse_name'] = $result['orders'][$i]['shipping_address']['company'];
        $warenkorb2['lieferadresse_ansprechpartner'] = $result['orders'][$i]['shipping_address']['name'];
      } else {
        $warenkorb2['lieferadresse_name'] = $result['orders'][$i]['shipping_address']['name'];
        $warenkorb2['lieferadresse_ansprechpartner'] = '';
      }

      if(strlen($result['orders'][$i]['shipping_address']['address2'])<5)
      {
        $warenkorb2['lieferadresse_strasse'] = $result['orders'][$i]['shipping_address']['address1'].' '.$result['orders'][$i]['shipping_address']['address2'];
        $warenkorb2['lieferadresse_adresszusatz'] = '';
      }
      else {
        $warenkorb2['lieferadresse_strasse'] = $result['orders'][$i]['shipping_address']['address1'];
        $warenkorb2['lieferadresse_adresszusatz'] = $result['orders'][$i]['shipping_address']['address2'];
      }
      $warenkorb2['lieferadresse_plz'] = $result['orders'][$i]['shipping_address']['zip'];
      $warenkorb2['lieferadresse_ort'] = $result['orders'][$i]['shipping_address']['city'];
      $warenkorb2['lieferadresse_land'] = $result['orders'][$i]['shipping_address']['country_code'];
      if(!empty($result['orders'][$i]['shipping_address']['province']))
      {
        $warenkorb2['lieferadresse_bundesstaat'] = $result['orders'][$i]['shipping_address']['province'];
      }
      
      //$warenkorb2[lieferadresse_abteilung] = $result[data][shipping][department]; //TODO
      //$warenkorb2[lieferadresse_unterabteilung] = $result[data][shipping][additionalAddressLine1]; //TODO

      $bruttosumme = 0;
      if($warenkorb2['lieferadresse_name']!=$warenkorb['name'] ||
          $warenkorb2['lieferadresse_ansprechpartner']!=$warenkorb['ansprechpartner'] ||
          $warenkorb2['lieferadresse_strasse']!=$warenkorb['strasse'] ||
          $warenkorb2['lieferadresse_plz']!=$warenkorb['plz'] ||
          $warenkorb2['lieferadresse_ort']!=$warenkorb['ort'] ||
          $warenkorb2['lieferadresse_land']!=$warenkorb['land'] 
         //|| $warenkorb2['lieferadresse_abteilung']!=$warenkorb['abteilung']
        
        )
      {
        $warenkorb['abweichendelieferadresse']="1";
        $warenkorb['lieferadresse_name']  = $warenkorb2['lieferadresse_name'] ;
        $warenkorb['lieferadresse_ansprechpartner'] = $warenkorb2['lieferadresse_ansprechpartner'];
        $warenkorb['lieferadresse_strasse'] = $warenkorb2['lieferadresse_strasse'];
        $warenkorb['lieferadresse_plz'] = $warenkorb2['lieferadresse_plz'];
        $warenkorb['lieferadresse_ort'] = $warenkorb2['lieferadresse_ort'];
        $warenkorb['lieferadresse_land'] = $warenkorb2['lieferadresse_land'];
        //$warenkorb['lieferadresse_abteilung'] = $warenkorb2['lieferadresse_abteilung'];
        //$warenkorb['lieferadresse_unterabteilung'] = $warenkorb2['lieferadresse_unterabteilung'];
        $warenkorb['lieferadresse_adresszusatz'] = $warenkorb2['lieferadresse_adresszusatz'];
        if(isset($warenkorb2['lieferadresse_bundesstaat'])){
          $warenkorb['lieferadresse_bundesstaat'] = $warenkorb2['lieferadresse_bundesstaat'];
        }
      }elseif(empty($warenkorb['telefon']) && !empty($result['orders'][$i]['shipping_address']['phone'])){
          $warenkorb['telefon'] = $result['orders'][$i]['shipping_address']['phone'];
      }
      unset($warenkorb2);
      $steuermenge = 0;
      $citems = !empty($result['orders'][$i]['line_items'])?count($result['orders'][$i]['line_items']):0;

      $discount_applications_percent = array();
      $discount_applications_absolute = array();
      $lineDiscounts = [];
      $lineDiscountsValues = [];
      $lineDiscountsToItems = [];

      $linePercentageDiscounts = [];
      $linePercentageDiscountsValues = [];
      $linePercentageDiscountsToItems = [];

      $itemsToLineDiscount = [];
      $itemsPercentageToLineDiscount = [];
      $discount_applications_index = -1;
      $discount_applications_absolute_index = -1;
      $discount_applications_absolute_sum = 0;
      $fullprecent_discount = false;
      $absolute_discount = false;
      $sumdiscountApplicationsAmount = 0;
      $shippingDiscountValue = 0;
      $shippingDiscountName = 0;
      $fixedApplicationIndexes = [];
      if(!empty($result['orders'][$i]['discount_applications'])) {
        $rabattartikelid = $this->app->DB->Select("SELECT artikelrabatt FROM shopexport WHERE id='$this->shopid' LIMIT 1");
        $rabattartikelnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$rabattartikelid' LIMIT 1");
        if($taxes_included && !empty($rabattartikelnummer)) {
          foreach ($result['orders'][$i]['discount_applications'] as $k => $v) {
            $isFixedAmount = $v['value_type'] === 'fixed_amount';
            $isLineItem = $v['target_type'] === 'line_item';
            $isShipping = $v['target_type'] === 'shipping_line';
            $isAllocationMethodOne = $v['allocation_method'] === 'one';
            $hasCode = !empty($v['code']);
            $hasTitle = !empty($v['title']);
            $tmpTotalDiscount = abs($warenkorb['rabattnetto']);
            //$warenkorb['rabattnetto'] = -abs($result['orders'][$i]['total_discounts']);

            if($isFixedAmount && $isLineItem && $isAllocationMethodOne && !$hasCode && $hasTitle
              && abs($v['value']) <= $tmpTotalDiscount){
              $found = 0;
              $lineDiscountsToItem = -1;
              foreach ($result['orders'][$i]['line_items'] as $likey => $livalue) {
                if(empty($livalue['discount_allocations'])){
                  continue;
                }
                foreach ($livalue['discount_allocations'] as $discount_allocationVal) {
                  if(!isset($discount_allocationVal['discount_application_index'])){
                    continue;
                  }
                  if($discount_allocationVal['discount_application_index'] == $k){
                    $found++;
                    $lineDiscountsToItem = $likey;
                    break;
                  }
                }
              }
              if($found === 1) {
                $v['nummer'] = $rabattartikelnummer;
                $lineDiscounts[] = $k;
                $lineDiscountsValues[] = $v;
                $lineDiscountsToItems[] = $lineDiscountsToItem;
                $warenkorb['rabattnetto'] += abs($v['value']);
                if($result['orders'][$i]['total_discounts'] < 0) {
                  $result['orders'][$i]['total_discounts'] += abs($v['value']);
                }
                elseif($result['orders'][$i]['total_discounts'] > 0) {
                  $result['orders'][$i]['total_discounts'] -= abs($v['value']);
                }
                unset($result['orders'][$i]['discount_applications'][$k]);
              }
            }
            elseif(!$isFixedAmount && $isLineItem) {
              $found = 0;
              $lineDiscountsToItem = -1;
              foreach ($result['orders'][$i]['line_items'] as $likey => $livalue) {
                if(empty($livalue['discount_allocations'])){
                  continue;
                }
                foreach ($livalue['discount_allocations'] as $discount_allocationVal) {
                  if(!isset($discount_allocationVal['discount_application_index'])){
                    continue;
                  }
                  if($discount_allocationVal['discount_application_index'] == $k){
                    $found++;
                    $lineDiscountsToItem = $likey;
                    break;
                  }
                }
              }
              if($found === 1) {
                $v['nummer'] = $rabattartikelnummer;
                $linePercentageDiscounts[] = $k;
                $linePercentageDiscountsValues[] = $v;
                $linePercentageDiscountsToItems[] = $lineDiscountsToItem;
                unset($result['orders'][$i]['discount_applications'][$k]);
              }
            }
            elseif($isShipping && $isFixedAmount) {

              $shippingDiscount = abs($v['value']);
              if($shippingDiscount <= $warenkorb['versandkostenbrutto'] && $shippingDiscount <= $tmpTotalDiscount) {
                $warenkorb['rabattnetto'] += abs($v['value']);
                $shippingDiscountValue = $shippingDiscount;
                $shippingDiscountName = !empty($v['description'])?$v['description']:$v['title'];
                unset($result['orders'][$i]['discount_applications'][$k]);
              }
            }
          }
          $itemsPercentageToLineDiscount = array_flip($linePercentageDiscountsToItems);
          $itemsToLineDiscount = array_flip($lineDiscountsToItems);
          $warenkorb['lineDiscountsValues'] = $lineDiscountsValues;
          $warenkorb['linePercentageDiscountsValues'] = $linePercentageDiscountsValues;
        }

        foreach ($result['orders'][$i]['discount_applications'] as $k => $v) {
          if(in_array($k, $lineDiscounts)) {
            continue;
          }
          $isFixedAmount = $v['value_type'] === 'fixed_amount';
          $isLineItem =  $v['target_type'] === 'line_item';
          if($isFixedAmount) {
            $sumdiscountApplicationsAmount += $v['value'];
            $fixedApplicationIndexes[] = $k;
          }
          if($v['value_type'] === 'percentage' && $isLineItem && $v['target_selection'] === 'all'){
            $discount_applications_index = $k;
            if($v['value'] == 100.0) {
              $fullprecent_discount = true;
            }
          }
          elseif($isFixedAmount && $isLineItem) {
            $discount_applications_absolute_index = $k;
            $absolute_discount = true;
          }
        }
      }

      foreach ($result['orders'][$i]['discount_codes'] as $discount_code){
        $warenkorb['gutscheincode'] = $discount_code['code'];
      }

      if($sumdiscountApplicationsAmount == 0 || abs($result['orders'][$i]['total_discounts']) != $sumdiscountApplicationsAmount) {
        $fixedApplicationIndexes = [];
      }

      for($ii=0; $ii < $citems; $ii++)
      {
        $variante_id = $result['orders'][$i]['line_items'][$ii]['variant_id'];
        $variante_id = $this->app->DB->Select("SELECT af.nummer FROM `artikelnummer_fremdnummern` af INNER JOIN artikel art ON af.artikel = art.id AND art.geloescht <> 1 AND art.nummer <> 'DEL' WHERE af.nummer = '$variante_id' AND af.nummer <> '' AND af.shopid = '".$this->shopid."' AND af.aktiv = 1 LIMIT 1");
        $product_id = $result['orders'][$i]['line_items'][$ii]['product_id'];
        $product_id = $this->app->DB->Select("SELECT af.nummer FROM `artikelnummer_fremdnummern` af INNER JOIN artikel art ON af.artikel = art.id AND art.geloescht <> 1 AND art.nummer <> 'DEL' WHERE af.nummer = '$product_id' AND af.nummer <> '' AND af.shopid = '".$this->shopid."' AND af.aktiv = 1 LIMIT 1");

        $options = [];
        if($this->eigenschaftenzubeschreibung && !empty($result['orders'][$i]['line_items'][$ii]['properties'])){
          foreach ($result['orders'][$i]['line_items'][$ii]['properties'] as $option){
            if(!empty($option['name']) && !empty($option['value'])){
              $options[] = $option['name'].' : '.$option['value'];
            }
          }
        }
        if(!empty($options)){
          $hilfsarray['options'] = implode("\n",$options);
        }

        if($taxes_included)
        {
          $taxcol = 'price';
          if($warenkorb['rabattnetto'] == 0)
          {
            $taxcol = 'rate';
          }
          $articleData = array('articleid'=>  empty($result['orders'][$i]['line_items'][$ii]['sku'])? ($variante_id == ''?($product_id == ''?$result['orders'][$i]['line_items'][$ii]['variant_id']:$product_id):$variante_id):$result['orders'][$i]['line_items'][$ii]['sku'],
              'fremdnummer'=>$result['orders'][$i]['line_items'][$ii]['variant_id'],
              'webid' => $result['orders'][$i]['line_items'][$ii]['id'],
              'name'=>$result['orders'][$i]['line_items'][$ii]['name'],
              'price'=>$result['orders'][$i]['line_items'][$ii]['price'],
              'quantity'=>$result['orders'][$i]['line_items'][$ii]['quantity'],
              'price_netto'=>$result['orders'][$i]['line_items'][$ii]['price']
              );
          if(!empty($options)){
            $articleData['options'] = implode("\n",$options);
          }
          if(!empty($fixedApplicationIndexes) && !empty($result['orders'][$i]['line_items'][$ii]['discount_allocations'])) {
            $itemDiscountAllocation = $result['orders'][$i]['line_items'][$ii]['discount_allocations'][0]['discount_application_index'];
            $discountApplictionByItem = $result['orders'][$i]['discount_applications'][$itemDiscountAllocation]['value'];
            if(in_array($itemDiscountAllocation, $fixedApplicationIndexes) &&
              $discountApplictionByItem == $result['orders'][$i]['line_items'][$ii]['discount_allocations'][0]['amount'] &&
              $discountApplictionByItem ==  $result['orders'][$i]['line_items'][$ii]['total_discount']
            ) {
              $articleData['price'] = $result['orders'][$i]['line_items'][$ii]['price'];
              $articleData['price_netto'] = $result['orders'][$i]['line_items'][$ii]['price']
                /  ($result['orders'][$i]['line_items'][$ii]['tax_lines'][0][$taxcol] != 0?
                  (1+  (!empty($result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate'])?$result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate']:0)):
                  1 );
            }
          }

          if($discount_applications_absolute_index > -1 &&
            !empty($result['orders'][$i]['line_items'][$ii]['discount_allocations']) &&
            $result['orders'][$i]['line_items'][$ii]['discount_allocations'][0]['discount_application_index'] == $discount_applications_absolute_index)
          {
            $ssatz = 100*(!empty($result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate'])?$result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate']:0);
            if(empty($discount_applications_absolute[(String)$ssatz])){
              $discount_applications_absolute[(String)$ssatz] = 0;
            }
            $discount_applications_absolute_sum += $result['orders'][$i]['line_items'][$ii]['discount_allocations'][0]['amount'];
            $discount_applications_absolute[(String)$ssatz] += $result['orders'][$i]['line_items'][$ii]['discount_allocations'][0]['amount'];
          }

        }
        else {
          $taxRate = !empty($result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate'])?$result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate']:0;
          $articleData = array(
            'articleid'=> empty($result['orders'][$i]['line_items'][$ii]['sku'])? ($variante_id == ''?($product_id == ''?$result['orders'][$i]['line_items'][$ii]['variant_id']:$product_id):$variante_id):$result['orders'][$i]['line_items'][$ii]['sku'],
            'fremdnummer'=>$result['orders'][$i]['line_items'][$ii]['variant_id'],
            'webid' => $result['orders'][$i]['line_items'][$ii]['id'],
            'name'=>$result['orders'][$i]['line_items'][$ii]['name'],
            'price_netto'=> $result['orders'][$i]['line_items'][$ii]['price'],
            'quantity'=>$result['orders'][$i]['line_items'][$ii]['quantity'],
            'price'=>$result['orders'][$i]['line_items'][$ii]['price'] * (1+ $taxRate)
          );
          if(!empty($options)){
            $articleData['options'] = implode("\n",$options);
          }
          if(!empty($fixedApplicationIndexes) && !empty($result['orders'][$i]['line_items'][$ii]['discount_allocations'])) {
            $itemDiscountAllocation = $result['orders'][$i]['line_items'][$ii]['discount_allocations'][0]['discount_application_index'];
            $discountApplictionByItem = $result['orders'][$i]['discount_applications'][$itemDiscountAllocation]['value'];
            if(in_array($itemDiscountAllocation, $fixedApplicationIndexes) &&
              $discountApplictionByItem == $result['orders'][$i]['line_items'][$ii]['discount_allocations'][0]['amount'] &&
              $discountApplictionByItem ==  $result['orders'][$i]['line_items'][$ii]['total_discount']
            ) {
              $articleData['price'] = $result['orders'][$i]['line_items'][$ii]['price'] *  ($result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['price'] != 0?
                  (1+  (!empty($result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate'])?$result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate']:0)):
                  1 );
              $articleData['price_netto'] = $result['orders'][$i]['line_items'][$ii]['price'];
            }
          }
        }


        if($result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate'] > $steuermenge
          && !(isset($result['orders'][$i]['line_items'][$ii]['tax_lines'][0]) && $result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['price'] == 0))
        {
          $steuermenge = $result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate'];
        }

        $isdiscountpercent = !empty($result['orders'][$i]['discount_codes'][0]['amount']) &&
          !empty($result['orders'][$i]['discount_codes'][0]['type']) &&
          $result['orders'][$i]['discount_codes'][0]['type'] === 'percentage' &&
          $discount_applications_index > -1;


        if(isset($result['orders'][$i]['line_items'][$ii]['tax_lines'][0]))
        {
          if($result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['price'] == 0 && !$isdiscountpercent
          && $result['orders'][$i]['total_price'] == 0
          )
          {
            $articleData['steuersatz']=0;
            $bruttosumme += $articleData['price'] * $articleData['quantity'];
            $articleData['steuersatz_orig']=$result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate']*100;
            unset($articleData['price']);
          }else{
            $articleData['steuersatz']=$result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate']*100;
            $steuersaetze[(String)round($result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate']*100,2)] = true;
          }
        }

        //$this->archive = true; //Debug sent Orders
        if(!$this->archive){
          if($result['orders'][$i]['line_items'][$ii]['fulfillable_quantity'] < $articleData['quantity'] && $this->fulfilledabziehen){
            $abziehen = $articleData['quantity'] - $result['orders'][$i]['line_items'][$ii]['fulfillable_quantity'];
            if($articleData['quantity'] > $abziehen){
              $articleData['quantity'] -= $abziehen;
              if(isset($articleData['price'])){
                $warenkorb['gesamtsumme'] -= $articleData['price'] * $abziehen;
              }else{
                $warenkorb['gesamtsumme'] -= $articleData['price_netto'] * $abziehen;
              }
            }else{
              if(isset($articleData['price'])){
                $warenkorb['gesamtsumme'] -= $articleData['price'] * $result['orders'][$i]['line_items'][$ii]['quantity'];
              }else{
                $warenkorb['gesamtsumme'] -= $articleData['price_netto'] * $result['orders'][$i]['line_items'][$ii]['quantity'];
              }
              unset($articleData);
            }
          }
        }
        if(empty($articleData)){
          continue;
        }
        if(empty($result['orders'][$i]['line_items'][$ii]['tax_lines'])){
          $articleData['steuersatz'] = 0;
        }else{
          foreach ($result['orders'][$i]['line_items'][$ii]['tax_lines'] as $taxLine){
            if($taxLine['title'] === 'VAT' || $taxLine['rate'] > 0){
              $articleData['steuersatz'] = $taxLine['rate']*100;
            }
          }
        }
        $articlearray[] =  $articleData;

        if($taxes_included) {
          if(!empty($itemsToLineDiscount) && isset($itemsToLineDiscount[$ii])) {
            $lineDiscountsValue = $lineDiscountsValues[$itemsToLineDiscount[$ii]];
            $articlearray[] = array(
              'articleid'=>  $lineDiscountsValue['nummer'],
              'name'=>$lineDiscountsValue['title'],
              'price'=> -abs($lineDiscountsValue['value']),
              'quantity'=>1,
              'steuersatz' =>
                (
                !empty($result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate'])?
                  $result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate'] * 100:
                  0
                )
            );
          }
          if(!empty($itemsPercentageToLineDiscount) && isset($itemsPercentageToLineDiscount[$ii])) {

            $linePercentageDiscountsKey = $linePercentageDiscounts[$itemsPercentageToLineDiscount[$ii]];
            $linePercentageDiscountsValue = $linePercentageDiscountsValues[$itemsPercentageToLineDiscount[$ii]];
            if(!empty($result['orders'][$i]['line_items'][$ii]['discount_allocations'])) {
              foreach($result['orders'][$i]['line_items'][$ii]['discount_allocations'] as $discountAll) {
                if(isset($discountAll['discount_application_index']) && $discountAll['discount_application_index'] == $linePercentageDiscountsKey) {
                  $rabattItemPrice = -(!empty($discountAll['amount_set']['shop_money']['amount'])?$discountAll['amount_set']['shop_money']['amount']:$discountAll['amount']);
                  if(isset($articlearray[count($articlearray) -1]['price_netto'])
                    && $articlearray[count($articlearray) -1]['price_netto'] == -$rabattItemPrice) {
                    unset($articlearray[count($articlearray) -1]['price_netto']);
                    $articlearray[count($articlearray) -1]['price'] = abs($rabattItemPrice);
                  }
                  $articlearray[] = array(
                    'articleid'=>  $linePercentageDiscountsValue['nummer'],
                    'name'=>
                      !empty($linePercentageDiscountsValue['title'])?
                        $linePercentageDiscountsValue['title']:
                        (!empty($linePercentageDiscountsValue['code'])?$linePercentageDiscountsValue['code']:'Rabatt'),
                    'price'=> $rabattItemPrice,
                    'quantity'=>1,
                    'steuersatz' =>
                      (
                      !empty($result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate'])?
                        $result['orders'][$i]['line_items'][$ii]['tax_lines'][0]['rate'] * 100:
                        0
                      )
                  );
                  $itemKey = count($articlearray) - 2;
                  if(!empty($articlearray[$itemKey]['price_netto']) && round(-$rabattItemPrice, 2)
                    === round($articlearray[$itemKey]['price_netto'] * $articlearray[$itemKey]['quantity'], 2)
                  ) {
                    if(!empty($articlearray[$itemKey]['steuersatz_orig'])) {
                      $articlearray[$itemKey]['steuersatz'] = $articlearray[$itemKey]['steuersatz_orig'];
                      unset($articlearray[$itemKey]['steuersatz_orig']);
                    }
                    $articlearray[$itemKey]['price'] = $articlearray[$itemKey]['price_netto'];
                    unset($articlearray[$itemKey]['price_netto']);
                  }
                  if(isset($warenkorb['rabattnetto'])){
                      $warenkorb['rabattnetto'] -= $rabattItemPrice;
                      $warenkorb['rabattnetto'] = round($warenkorb['rabattnetto'],5);
                  }
                  if(!empty($warenkorb['rabattnetto']) && abs($warenkorb['rabattnetto']) >= abs($rabattItemPrice)) {
                    if($warenkorb['rabattnetto'] < 0) {
                      $warenkorb['rabattnetto'] += abs($rabattItemPrice);
                    }
                    elseif($warenkorb['rabattnetto'] > 0) {
                      $warenkorb['rabattnetto'] -= abs($rabattItemPrice);
                    }
                  }
                  $lastKey = count($articlearray) - 1;
                  if($articlearray[$lastKey]['steuersatz'] == 0
                    && !empty($articlearray[$lastKey]['price'])
                    && !isset($articlearray[$lastKey]['price_netto'])) {
                    if(!empty($articlearray[$lastKey - 1]['price'])
                    && !isset($articlearray[$lastKey - 1]['price_netto'])
                    && round($articlearray[$lastKey - 1]['price'] * $articlearray[$lastKey - 1]['quantity'],2)
                      ==round(- $articlearray[$lastKey]['price'],2)
                    ) {
                      $articlearray[$lastKey - 1]['price_netto'] = $articlearray[$lastKey - 1]['price'];
                      $articlearray[$lastKey - 1]['steuersatz'] = 0;
                      unset($articlearray[$lastKey - 1]['price']);
                    }
                    $articlearray[$lastKey]['price_netto'] = $articlearray[$lastKey]['price'];
                    unset($articlearray[$lastKey]['price']);
                  }
                  break;
                }
              }
            }
          }
        }
        //total_discount
      }

      if(isset($result['orders'][$i]['discount_codes']) && is_array($result['orders'][$i]['discount_codes']) && isset($result['orders'][$i]['discount_codes'][0]))
      {
        if($result['orders'][$i]['discount_codes'][0]['amount'] && isset($result['orders'][$i]['discount_codes'][0]['type']) && $result['orders'][$i]['discount_codes'][0]['type'] === 'fixed_amount')
        {
          $warenkorb['rabattnetto'] = -$result['orders'][$i]['discount_codes'][0]['amount'];
          $warenkorb['rabattname'] = !empty($result['orders'][$i]['discount_codes'][0]['code'])?$result['orders'][$i]['discount_codes'][0]['code']:'Rabatt';
          $bruttosumme += $warenkorb['rabattnetto'];
          $warenkorb['rabattsteuer'] = 0;
          if(round($bruttosumme,4) == 0)
          {
            foreach($articlearray as $k => $v)
            {
              if(isset($v['price_netto']) && !isset($v['price']))
              {
                $articlearray[$k]['price'] = $v['price_netto'];
                unset($articlearray[$k]['price_netto']);
              }
            }
          }
        }elseif($result['orders'][$i]['discount_codes'][0]['amount'] && isset($result['orders'][$i]['discount_codes'][0]['type']) && $result['orders'][$i]['discount_codes'][0]['type'] === 'percentage'){
          if($discount_applications_index > -1){
            foreach ($articlearray as $k => $v) {
              if(
                !empty($result['orders'][$i]['line_items'][$k]['discount_allocations'])
                //&& !empty($v['steuersatz'])
              ){
                foreach($result['orders'][$i]['line_items'][$k]['discount_allocations'] as $kd => $vd)
                {
                  if(isset($vd['discount_application_index']) && $vd['discount_application_index'] == $discount_applications_index)
                  {
                    $v['steuersatz'] = empty($v['steuersatz'])?'':(String)$v['steuersatz'];
                    if($fullprecent_discount)
                    {
                      if(empty($discount_applications_percent[$v['steuersatz']])){
                        $discount_applications_percent[$v['steuersatz']] = array('price'=>0,'price_netto'=>0);
                      }
                      if(!empty($v['price']))
                      {
                        $discount_applications_percent[$v['steuersatz']]['price'] += $v['price'] * $v['quantity'];
                      }
                      if(!empty($v['price_netto']))
                      {
                        if(!empty($v['price']) && $v['price'] == $v['price_netto'] && !empty($v['steuersatz']) && $v['steuersatz'] != 0)
                        {
                          $articlearray[$k]['price_netto'] = $v['price'] / (1+$v['steuersatz']/100);
                          $v['price_netto'] = $articlearray[$k]['price_netto'];
                        }
                        $discount_applications_percent[$v['steuersatz']]['price_netto'] += $v['price_netto'] * $v['quantity'];
                      }
                    }else{
                      if(empty($discount_applications_percent[$v['steuersatz']])){
                        $discount_applications_percent[$v['steuersatz']] = 0;
                      }
                      $discount_applications_percent[$v['steuersatz']] += $vd['amount'];
                    }
                  }
                }
              }
            }
          }
        }
      }

      if($warenkorb['zahlungsweise'] === '')
      {
        if($warenkorb['gesamtsumme'] == 0 && (isset($warenkorb['rabattnetto']) || isset($warenkorb['rabattnetto']))){
          $warenkorb['zahlungsweise'] = 'Gutschein';
        }
      }
      
      if(isset($warenkorb['rabattnetto']) && $warenkorb['rabattnetto'] == 0)
      {
        unset($warenkorb['rabattnetto']);
        if(isset($warenkorb['rabattname'])){
          unset($warenkorb['rabattname']);
        }
      }

      if(!empty($warenkorb['rabattsteuer']) && !empty($warenkorb['rabattnetto'] && !empty($result['orders'][$i]['total_discounts'])) &&
      !empty($result['orders'][$i]['taxes_included']) &&  $warenkorb['rabattsteuer'] > 0 && $warenkorb['rabattnetto'] > 0 &&
      $warenkorb['rabattnetto'] == $result['orders'][$i]['total_discounts'] )
      {
        $warenkorb['rabattbrutto'] = $warenkorb['rabattnetto'];
        unset($warenkorb['rabattnetto']);
      }elseif(!empty($warenkorb['rabattnetto']) && $result['orders'][$i]['tax_inculeded'] && $steuermenge > 0)
      {
        if($result['orders'][$i]['total_discount'] + $result['orders'][$i]['subtotal_price'] +
          (!empty($warenkorb['versandkostenbrutto'])?$warenkorb['versandkostenbrutto']:0) ==
          $result['orders'][$i]['total_line_item_price'])
        {
          $warenkorb['rabattnetto'] /= (1+$steuermenge);
        }
      }

      $warenkorb['rabattsteuer'] = $steuermenge*100;


      if($warenkorb['rabattnetto'] != 0){
        $steuersatzzugruppe = array();
        $steuergruppen = array();
        $summegesamt = 0;
        foreach ($articlearray as $key => $value) {
          $steuerart = 'Befreit';
          if($value['steuersatz'] > 0){
            if($value['steuersatz'] <= 11){
              $steuerart = 'Ermaessigt';
            }else{
              $steuerart = 'Normal';
            }
          }
          $steuersatzzugruppe[$value['steuersatz']] = $steuerart;
          $positionskosten = (isset($value['price_netto'])?$value['price_netto']:$value['price'])*$value['quantity'];
          if(!isset($steuergruppen[$steuerart])){
            $steuergruppen[$steuerart] = $positionskosten;
          }else{
            $steuergruppen[$steuerart] += $positionskosten;
          }
          $summegesamt += (isset($value['price_netto'])?$value['price_netto']:$value['price'])*$value['quantity'];
        }

        if(count($steuergruppen) > 1){
          if(!empty($discount_applications_absolute) && count($discount_applications_absolute) === 1 && round(-$discount_applications_absolute_sum,2) == round($warenkorb['rabattnetto'],2)){
            $absolutekey = array_keys($discount_applications_absolute);
            $absolutekey = reset($absolutekey);
            $warenkorb['rabattbrutto'] = $warenkorb['rabattnetto'];
            $warenkorb['rabattsteuer'] = $absolutekey;
            unset($warenkorb['rabattnetto']);
          }elseif(!empty($discount_applications_absolute) && count($discount_applications_absolute) > 1 && round(-$discount_applications_absolute_sum,2) == round($warenkorb['rabattnetto'],2)){
            $rabattartikelid = $this->app->DB->Select("SELECT artikelrabatt FROM shopexport WHERE id='$this->shopid' LIMIT 1");
            $rabattartikelnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$rabattartikelid' LIMIT 1");
            foreach($discount_applications_absolute as $steuersatz => $value)
            {
              $key = !empty($steuersatzzugruppe[$steuersatz]) ? $steuersatzzugruppe[$steuersatz] : $steuersatz;
              if(!is_numeric($key)){
                $articlearray[] = array(
                  'articleid' => $rabattartikelnummer,
                  'name' => 'Rabatt ' . $key,
                  'quantity' => '1',
                  'umsatzsteuer' => strtolower($key),
                  'price' => -abs($value)
                );
              }else{
                $articlearray[] = array(
                  'articleid' => $rabattartikelnummer,
                  'name' => 'Rabatt ' . $key,
                  'quantity' => '1',
                  'steuersatz' => $key,
                  'price' => -abs($value)
                );
              }
              if(is_numeric($steuersatz))
              {
                $articlearray[count($articlearray)-1]['steuersatz'] = $steuersatz;
                $articlearray[count($articlearray)-1]['price_netto'] = $articlearray[count($articlearray)-1]['price'] / (1+$steuersatz/100);
              }
            }
            unset($warenkorb['rabattnetto']);
            unset($warenkorb['rabattbrutto']);
            unset($warenkorb['rabattname']);
          }else{
            $rabattartikelid = $this->app->DB->Select("SELECT artikelrabatt FROM shopexport WHERE id='$this->shopid' LIMIT 1");
            $rabattartikelnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$rabattartikelid' LIMIT 1");
            if(!empty($discount_applications_percent)){
              if(!$fullprecent_discount){
                foreach ($discount_applications_percent as $steuersatz => $value) {
                  $key = !empty($steuersatzzugruppe[$steuersatz]) ? $steuersatzzugruppe[$steuersatz] : $steuersatz;
                  if(!is_numeric($key)){
                    $articlearray[] = array(
                      'articleid' => $rabattartikelnummer,
                      'name' => 'Rabatt ' . $key,
                      'quantity' => '1',
                      'umsatzsteuer' => strtolower($key),
                      'price' => -abs($value)
                    );
                  }else{
                    $articlearray[] = array(
                      'articleid' => $rabattartikelnummer,
                      'name' => 'Rabatt ' . $key,
                      'quantity' => '1',
                      'steuersatz' => $key,
                      'price' => -abs($value)
                    );
                  }
                  if(is_numeric($steuersatz))
                  {
                    $articlearray[count($articlearray)-1]['steuersatz'] = $steuersatz;
                    $articlearray[count($articlearray)-1]['price_netto'] = $articlearray[count($articlearray)-1]['price'] / (1+$steuersatz/100);
                  }
                }
              }else{
                foreach ($discount_applications_percent as $steuersatz => $value) {
                  $pricekey = !empty($value['price_netto']) && $value['price_netto'] != 0 ? 'price_netto' : 'price';
                  $key = !empty($steuersatzzugruppe[$steuersatz]) ? $steuersatzzugruppe[$steuersatz] : $steuersatz;
                  if(!is_numeric($key)){
                    $articlearray[] = array(
                      'articleid' => $rabattartikelnummer,
                      'name' => 'Rabatt ' . $key,
                      'quantity' => '1',
                      'umsatzsteuer' => strtolower($key),
                      'steuersatz' => $steuersatz,
                      $pricekey => -abs($value[$pricekey])
                    );
                  }else{
                    $articlearray[] = array(
                      'articleid' => $rabattartikelnummer,
                      'name' => 'Rabatt ' . $key,
                      'quantity' => '1',
                      'steuersatz' => $key,
                      $pricekey => -abs($value[$pricekey])
                    );
                  }
                }
              }
            }else{
              foreach ($steuergruppen as $key => $value) {
                if($value > 0){
                  $anteil = $value / $summegesamt;
                  $articlearray[] = array(
                    'articleid' => $rabattartikelnummer,
                    'name' => 'Rabatt ' . $key,
                    'quantity' => '1',
                    'umsatzsteuer' => strtolower($key),
                    'price' => $warenkorb['rabattnetto'] * $anteil
                  );
                }
              }
            }
            unset($warenkorb['rabattnetto']);
            unset($warenkorb['rabattsteuer']);
          }
        }
      }

      foreach($articlearray as $key => $value)
      {
        if(!empty($value['price']) && !empty($value['price_netto']) && !empty($value['steuersatz']) && $value['price'] == $value['price_netto'])
        {
          $articlearray[$key]['price_netto'] /= 1+($value['steuersatz'] / 100);
        }
      }
      
      $warenkorb['articlelist']=$articlearray;

      if($result['orders'][$i]['total_tax'] == 0 &&  $this->app->erp->Export($warenkorb['land']))
      {
        $warenkorb['steuerfrei']=1;
      }

      if(count($steuersaetze) === 1)
      {
        $steuersaetze = array_keys($steuersaetze);
        $steuersaetze = reset($steuersaetze);
        if($steuersaetze >= 14)
        {
          $warenkorb['umsatzsteuer_normal'] = $steuersaetze;
        }elseif($steuersaetze <= 10 && $steuersaetze > 0){
          $warenkorb['umsatzsteuer_ermassigt'] = $steuersaetze;
        }
      }elseif(count($steuersaetze) === 2)
      {
        $steuersaetze = array_keys($steuersaetze);
        if($steuersaetze[0] > 0 && $steuersaetze[1] > 0 && $steuersaetze[0] > $steuersaetze[1])
        {
          $warenkorb['umsatzsteuer_normal'] = $steuersaetze[0];
          $warenkorb['umsatzsteuer_ermassigt'] = $steuersaetze[1];
        }elseif($steuersaetze[0] > 0 && $steuersaetze[1] > 0 && $steuersaetze[0] < $steuersaetze[1])
        {
          $warenkorb['umsatzsteuer_normal'] = $steuersaetze[1];
          $warenkorb['umsatzsteuer_ermassigt'] = $steuersaetze[0];
        }
      }
      if(!empty($steuersaetze) && !empty($warenkorb['lieferadresse_land']) &&
        $result['orders'][$i]['total_tax'] != 0 &&
        $this->app->erp->IstEU($warenkorb['lieferadresse_land']) &&
        !$this->app->erp->Export($warenkorb['lieferadresse_land'])
      )
      {
        $warenkorb['ust_befreit'] = 1;
      }
      if(empty($warenkorb['ust_befreit']) && !empty($warenkorb['rabattnetto']) && empty($warenkorb['rabattbrutto'])
      && empty($warenkorb['versandkostennetto']) && $result['orders'][$i]['taxes_included']
      )
      {
        if((!empty($warenkorb['versandkostenbrutto'])?$warenkorb['versandkostenbrutto']:0)+
          $result['orders'][$i]['total_line_items_price'] - $result['orders'][$i]['total_discounts'] == $result['orders'][$i]['total_price']
        ){
          $warenkorb['rabattbrutto'] = $warenkorb['rabattnetto'];
          unset($warenkorb['rabattnetto']);
        }
      }

      if(!empty($steuergruppen) && count($steuergruppen) === 1  && empty($warenkorb['rabattsteuer']) && !empty($warenkorb['rabattbrutto']) && empty($warenkorb['rabattnetto']))
      {
        if(!empty($discount_applications_percent) && count($discount_applications_percent) === 1){
          foreach($discount_applications_percent as $tax_percent => $prices)
          {
            if(is_numeric($tax_percent)){
              $warenkorb['rabattsteuer'] = $tax_percent;
            }
            break;
          }
        }elseif(!empty($discount_applications_absolute) && count($discount_applications_absolute) === 1)
        {
          foreach($discount_applications_absolute as $tax_percent => $prices)
          {
            if(is_numeric($tax_percent)){
              $warenkorb['rabattsteuer'] = $tax_percent;
            }
            break;
          }
        }
      }

      if($this->autofullfilltax && empty($warenkorb['versandkostennetto']) && !empty($warenkorb['versandkostenbrutto']))
      {
        if(is_array($steuersaetze) &&  count($steuersaetze) > 1){
          $itemtaxes = [];
          foreach ($articlearray as $value) {
            if(!empty($value['steuersatz']) && $value['steuersatz']){
              if(!empty($value['price_netto'])){
                if(empty($itemtaxes[$value['steuersatz']])){
                  $itemtaxes[$value['steuersatz']] = 0;
                }
                $itemtaxes[$value['steuersatz']] += $value['price_netto'] * $value['quantity'];
              }
            }
          }
          if(count($itemtaxes) > 1){
            arsort($itemtaxes);
            $itemtaxes = array_keys($itemtaxes);
            $itemtaxes = reset($itemtaxes);
            if(!empty($warenkorb['umsatzsteuer_normal']) && $itemtaxes == $warenkorb['umsatzsteuer_normal'])
            {
              $warenkorb['portosteuersatz'] = 'normal';
            }elseif(!empty($warenkorb['umsatzsteuer_ermassigt']) && $itemtaxes == $warenkorb['umsatzsteuer_ermassigt'])
            {
              $warenkorb['portosteuersatz'] = 'ermaessigt';
            }
          }
        }elseif(is_numeric($steuersaetze))
        {
          if(!empty($warenkorb['umsatzsteuer_normal']) && $steuersaetze == $warenkorb['umsatzsteuer_normal'])
          {
            $warenkorb['portosteuersatz'] = 'normal';
          }elseif(!empty($warenkorb['umsatzsteuer_ermassigt']) && $steuersaetze == $warenkorb['umsatzsteuer_ermassigt'])
          {
            $warenkorb['portosteuersatz'] = 'ermaessigt';
          }
        }
      }elseif($taxes_included && !$this->autofullfilltax && empty($warenkorb['versandkostennetto']) && !empty($warenkorb['versandkostenbrutto'])){
        if(!empty($result['orders'][$i]['shipping_lines'][0])
        && !empty($result['orders'][$i]['shipping_lines'][0]['tax_lines'])
          && !empty($result['orders'][$i]['shipping_lines'][0]['tax_lines'][0]['rate'])
        ){
          if(!empty($warenkorb['umsatzsteuer_ermassigt']) && round(100*$result['orders'][$i]['shipping_lines'][0]['tax_lines'][0]['rate'],2) === round($warenkorb['umsatzsteuer_ermassigt'],2))
          {
            $warenkorb['portosteuersatz'] = 'ermaessigt';
          }elseif(!empty($warenkorb['umsatzsteuer_normal']) && round(100*$result['orders'][$i]['shipping_lines'][0]['tax_lines'][0]['rate'],2) === round($warenkorb['umsatzsteuer_normal'],2))
          {
            $warenkorb['portosteuersatz'] = 'normal';
          }
        }
      }
      if(isset($warenkorb['rabattsteuer']) && $warenkorb['rabattsteuer'] == 0 && !empty($warenkorb['rabattbrutto']) && empty($warenkorb['rabattnetto']))
      {
        $warenkorb['rabattnetto'] = $warenkorb['rabattbrutto'];
        unset($warenkorb['rabattbrutto']);
      }elseif(!empty($warenkorb['rabattsteuer']) && $warenkorb['rabattsteuer'] > 0 &&
        !empty($warenkorb['rabattnetto']) && $warenkorb['rabattnetto'] == -$result['orders'][$i]['total_discounts']) {
        $warenkorb['rabattbrutto'] = $warenkorb['rabattnetto'];
        unset($warenkorb['rabattnetto']);
      }

      if(empty($warenkorb['steuerfrei']) && $taxes_included && ($result['orders'][$i]['total_tax'] == 0)) {
        $warenkorb = $this->removeNetIfEqualToGross($warenkorb);
      }

      $warenkorb = $this->changeNetGrossIfDiscount($warenkorb);
      $warenkorb = $this->changeItemPercentDiscountTax($warenkorb, $result['orders'][$i]);

      if($taxes_included && !empty($warenkorb['versandkostenbrutto']) && $warenkorb['versandkostenbrutto'] != 0
        && empty($warenkorb['versandkostennetto']) && empty($warenkorb['versandkostensteuersatz'])) {
        if($warenkorb['portosteuersatz'] === 'ermaessigt' && !empty($warenkorb['umsatzsteuer_ermassigt'])
        && $warenkorb['umsatzsteuer_ermassigt'] > 0
          && $warenkorb['umsatzsteuer_ermassigt'] != $this->app->erp->Firmendaten('steuersatz_ermaessigt')) {
          $warenkorb['versandkostensteuersatz'] = $warenkorb['umsatzsteuer_ermassigt'];
        }
        elseif(!empty($warenkorb['umsatzsteuer_normal']) && $warenkorb['umsatzsteuer_normal'] > 0
          && (empty($warenkorb['portosteuersatz']) || $warenkorb['portosteuersatz'] === 'normal')
            && $warenkorb['umsatzsteuer_normal'] != $this->app->erp->Firmendaten('steuersatz_normal')) {
          $warenkorb['versandkostensteuersatz'] = $warenkorb['umsatzsteuer_normal'];
        }
      }

      if(!empty($warenkorb['rabattnetto']) && $result['orders'][$i]['total_tax'] > 0){
          $warenkorb['rabattbrutto'] = $warenkorb['rabattnetto'];
          unset($warenkorb['rabattnetto']);
      }

      if($shippingDiscountValue > 0) {
        $shippingDiscountItem = array(
          'articleid' => $rabattartikelnummer,
          'name' => $shippingDiscountName,
          'quantity' => '1',
          'umsatzsteuer' => $warenkorb['portosteuersatz'],
        );
        if(!empty($warenkorb['versandkostensteuersatz'])) {
          $shippingDiscountItem['steuersatz'] = $warenkorb['versandkostensteuersatz'];
        }

        foreach ($result['orders'][$i]['shipping_lines'] as $shippingLine){
          $shippingDiscountItem['steuersatz'] = 0;
          if(!empty($shippingLine['tax_lines'])){
            foreach ($shippingLine['tax_lines'] as $taxLine){
              if($taxLine['title'] === 'VAT' || $taxLine['rate'] > 0){
                $shippingDiscountItem['steuersatz'] = $taxLine['rate'] * 100;
              }
            }
          }
        }
        $shippingDiscountTax = isset($shippingDiscountItem['steuersatz']) ? $shippingDiscountItem['steuersatz'] : 0;
        if($shippingDiscountTax > 0){
          $shippingDiscountItem['price_netto'] = -$shippingDiscountValue / (1 + $shippingDiscountTax / 100);
        }
        elseif($warenkorb['portosteuersatz'] === 'ermaessigt' && !empty($warenkorb['umsatzsteuer_ermassigt'])) {
          $shippingDiscountTax = $warenkorb['umsatzsteuer_ermassigt'];
          $shippingDiscountItem['steuersatz'] = $shippingDiscountTax;
          $shippingDiscountItem['price_netto'] = -$shippingDiscountValue / (1 + $shippingDiscountTax / 100);
        }
        elseif($warenkorb['portosteuersatz'] === 'normal' && !empty($warenkorb['umsatzsteuer_normal'])) {
          $shippingDiscountTax = $warenkorb['umsatzsteuer_normal'];
          $shippingDiscountItem['steuersatz'] = $shippingDiscountTax;
          $shippingDiscountItem['price_netto'] = -$shippingDiscountValue / (1 + $shippingDiscountTax / 100);
        }
        else{
          $shippingDiscountItem['price'] = -$shippingDiscountValue;
        }

        $warenkorb['articlelist'][] = $shippingDiscountItem;
      }

      if(!empty($warenkorb['rabattbrutto']) && !empty($result['orders'][$i]['total_discounts'])
        && !empty($rabattartikelnummer)) {
        $articleListRabatt = 0;
        foreach($warenkorb['articlelist'] as $subatricle) {
          if($subatricle['articleid'] == $rabattartikelnummer && !empty($subatricle['price'])) {
            $articleListRabatt -= $subatricle['price'];
          }
        }
        $rabattbrutto = round($result['orders'][$i]['total_discounts'] - $articleListRabatt,7);
        if($rabattbrutto == 0) {
          unset($warenkorb['rabattbrutto']);
          if(!empty($warenkorb['rabattsteuer'])) {
            unset($warenkorb['rabattsteuer']);
          }
        }
        elseif($rabattbrutto > 0 && $rabattbrutto < $warenkorb['rabattbrutto']) {
          $warenkorb['rabattbrutto'] = $rabattbrutto;
        }
      }

      if(!empty($warenkorb['rabattbrutto'])
        && !isset($warenkorb['rabattnetto'])
        && !empty($warenkorb['rabattsteuer'])
        && is_numeric($warenkorb['rabattsteuer'])
      ) {
        $warenkorb['rabattnetto'] = $warenkorb['rabattbrutto'] / (1+$warenkorb['rabattsteuer'] / 100);
        unset($warenkorb['rabattbrutto']);
      }
      if($result['orders'][$i]['total_tax'] > 0 && !empty($warenkorb['articlelist'])) {
        foreach($warenkorb['articlelist'] as $articleKey => $article) {
          if(!empty($article['steuersatz'])
            && is_numeric($article['steuersatz'])
            && isset($article['price']) && !isset($article['price_netto'])
          ) {
            $warenkorb['articlelist'][$articleKey]['price_netto'] = $article['price'] / (1+$article['steuersatz'] / 100);
          }
        }
      }
      if($taxes_included && abs($warenkorb['rabattnetto']) > 0 && $warenkorb['rabattsteuer'] == 0) {
        $isItemNetGross = true;
        foreach($warenkorb['articlelist'] as $article) {
          if(!isset($article['price']) || !isset($article['price_netto']) || $article['price'] != $article['price_netto']) {
            $isItemNetGross = false;
            break;
          }
        }
        if($isItemNetGross) {
          foreach($warenkorb['articlelist'] as $articleKey => $article) {
            $warenkorb['articlelist'][$articleKey]['steuersatz'] = 0;
          }
        }
      }
      unset($steuersaetze);
      unset($articlearray);
      $tmp[$j]['id'] = $warenkorb['auftrag'];

      $tmp[$j]['warenkorb'] = base64_encode(serialize($warenkorb));
      unset($warenkorb);
    }
    if(isset($tmp)) {
      $this->log('Shopify import finished at LOC: 3023', ['tmp' => $tmp]);

      return $tmp;
    }

    date_default_timezone_set($this->timezone);
    $this->log('Shopify import finished at LOC: 3029', ['zeitstempel'=>date("Y-m-d H:i:s",strtotime($exitzeitstempel))]);

    return array('zeitstempel'=>date('Y-m-d H:i:s',strtotime($exitzeitstempel)));
  }

  /**
   * @param array $warenkorb
   * @param array $order
   *
   * @return array
   */
  protected function changeItemDiscountTax($warenkorb, $order)
  {
    if(empty($order['taxes_included']) || empty($warenkorb['rabattnetto'])) {
      return $warenkorb;
    }
    if(isset($warenkorb['rabattsteuer']) && $warenkorb['rabattsteuer'] > 0) {
      return $warenkorb;
    }
    if(empty($order['discount_applications']) || count($order['discount_applications']) > 0) {
      return $warenkorb;
    }
    if($order['discount_applications'][0]['value_type'] !== 'percentage'
      || $order['discount_applications'][0]['target_type'] !== 'line_item'
      || $order['discount_applications'][0]['value'] != 100.0
    ) {
      return $warenkorb;
    }
    $shopexport = $this->app->DB->SelectRow(
      sprintf(
        'SELECT art.umsatzsteuer, s.artikelrabattsteuer FROM shopexport AS s 
        INNER JOIN artikel AS art ON s.artikelrabatt = art.id
        WHERE s.id = %d',
        $this->shopid
      )
    );
    if(empty($shopexport) || $shopexport['artikelrabattsteuer'] === 0.0
      || $shopexport['artikelrabattsteuer'] === 'befreit') {
      return $warenkorb;
    }
    $tax = false;
    foreach($warenkorb['articlelist'] as $article) {
      if(!isset($article['steuersatz'])) {
        return $warenkorb;
      }
      if($tax === false) {
        $tax = $article['steuersatz'];
      }
      elseif($tax !== $article['steuersatz']) {
        return $warenkorb;
      }
    }

    if($tax === false) {
      return $warenkorb;
    }

    if(!empty($warenkorb['umsatzsteuer_ermassigt'])
      && $tax == $warenkorb['umsatzsteuer_ermassigt'] &&
      $shopexport['artikelrabattsteuer'] === 'ermaessigt'
    ) {
      $warenkorb['rabattbrutto'] = $warenkorb['rabattnetto'];
      unset($warenkorb['rabattnetto']);
      $warenkorb['rabattsteuer'] = $tax;
      return $warenkorb;
    }
    if(!empty($warenkorb['umsatzsteuer_normal'])
      && $tax == $warenkorb['umsatzsteuer_normal'] &&
      $shopexport['artikelrabattsteuer'] === 'normal'
    ) {
      $warenkorb['rabattbrutto'] = $warenkorb['rabattnetto'];
      unset($warenkorb['rabattnetto']);
      $warenkorb['rabattsteuer'] = $tax;
      return $warenkorb;
    }
    return $warenkorb;
  }

  /**
   * @param array $warenkorb
   * @param array $order
   *
   * @return array
   */
  protected function changeItemPercentDiscountTax($warenkorb, $order)
  {
    if(empty($order['taxes_included']) || empty($warenkorb['rabattnetto'])) {
      return $warenkorb;
    }
    if(isset($warenkorb['rabattsteuer']) && $warenkorb['rabattsteuer'] > 0) {
      return $warenkorb;
    }
    if(empty($order['discount_applications']) || count($order['discount_applications']) > 0) {
      return $warenkorb;
    }
    if($order['discount_applications'][0]['value_type'] !== 'percentage'
      || $order['discount_applications'][0]['target_type'] !== 'line_item'
      || $order['discount_applications'][0]['value'] != 100.0
    ) {
      return $warenkorb;
    }
    $shopexport = $this->app->DB->SelectRow(
      sprintf(
        'SELECT art.umsatzsteuer, s.artikelrabattsteuer FROM shopexport AS s 
        INNER JOIN artikel AS art ON s.artikelrabatt = art.id
        WHERE s.id = %d',
        $this->shopid
      )
    );
    if(empty($shopexport) || $shopexport['artikelrabattsteuer'] === 0.0
      || $shopexport['artikelrabattsteuer'] === 'befreit') {
      return $warenkorb;
    }
    $tax = false;
    foreach($warenkorb['articlelist'] as $article) {
      if(!isset($article['steuersatz'])) {
        return $warenkorb;
      }
      if($tax === false) {
        $tax = $article['steuersatz'];
      }
      elseif($tax !== $article['steuersatz']) {
        return $warenkorb;
      }
    }

    if($tax === false) {
      return $warenkorb;
    }

    if(!empty($warenkorb['umsatzsteuer_ermassigt'])
      && $tax == $warenkorb['umsatzsteuer_ermassigt'] &&
      $shopexport['artikelrabattsteuer'] === 'ermaessigt'
    ) {
      $warenkorb['rabattbrutto'] = $warenkorb['rabattnetto'];
      unset($warenkorb['rabattnetto']);
      $warenkorb['rabattsteuer'] = $tax;
      return $warenkorb;
    }
    if(!empty($warenkorb['umsatzsteuer_normal'])
      && $tax == $warenkorb['umsatzsteuer_normal'] &&
      $shopexport['artikelrabattsteuer'] === 'normal'
    ) {
      $warenkorb['rabattbrutto'] = $warenkorb['rabattnetto'];
      unset($warenkorb['rabattnetto']);
      $warenkorb['rabattsteuer'] = $tax;
      return $warenkorb;
    }
    
    return $warenkorb;
  }

  /**
   * @param array $warenkorb
   *
   * @return array
   */
  protected function changeNetGrossIfDiscount($warenkorb)
  {
    if(empty($warenkorb['articlelist'])){
      return $warenkorb;
    }
    foreach ($warenkorb['articlelist'] as $key => $article){
      if($article['steuersatz']===0 && !empty($article['steuersatz_orig'])){
        $warenkorb['articlelist'][$key]['steuersatz'] = $article['steuersatz_orig'];
      }
    }
    if($warenkorb['gesamtsumme'] != 0 || empty($warenkorb['rabattnetto'])) {
      return $warenkorb;
    }
    if(!empty($warenkorb['land']) && ($this->app->erp->Export($warenkorb['land']) || $this->app->erp->IstEU($warenkorb['land']))) {
      return $warenkorb;
    }
    $steuersatz = false;
    $price = 0;
    foreach($warenkorb['articlelist'] as $articleKey => $article) {
      if(empty($article['price']) || !empty($article['price_netto'])) {
        return $warenkorb;
      }
      $tmpSteuersatz = !empty($article['steuersatz_orig'])?$article['steuersatz_orig']:$article['steuersatz'];
      if(empty($tmpSteuersatz)) {
        return $warenkorb;
      }
      if($steuersatz === false) {
        $steuersatz = $tmpSteuersatz;
      }elseif($steuersatz != $tmpSteuersatz) {
        return $warenkorb;
      }
      $price += $article['price'] * $article['quantity'];
    }
    if(round(-$price,2) === round($warenkorb['rabattnetto'],2)) {
      $warenkorb['rabattbrutto'] = $warenkorb['rabattnetto'];
      unset($warenkorb['rabattnetto']);
      $warenkorb['rabattsteuer'] = $steuersatz;
      foreach($warenkorb['articlelist'] as $articleKey => $article) {
        $warenkorb['articlelist'][$articleKey]['steuersatz'] = $steuersatz;
      }
    }

    return $warenkorb;
  }

  /**
   * @param array $warenkorb
   *
   * @return array
   */
  protected function removeNetIfEqualToGross($warenkorb)
  {
    if(!empty($warenkorb['rabattnetto']) || !empty($warenkorb['rabattbrutto']) || empty($warenkorb['articlelist'])) {
      return $warenkorb;
    }
    if(!empty($warenkorb['versandkostenbrutto']) && !empty($warenkorb['versandkostennetto']) &&
      $warenkorb['versandkostenbrutto'] != $warenkorb['versandkostennetto']) {
      return $warenkorb;
    }

    foreach($warenkorb['articlelist'] as $article) {
      if(empty($article['price'])) {
        return $warenkorb;
      }

      if(!empty($article['price_netto'])) {
        if(!empty($article['price']) && $article['price'] != $article['price_netto']) {
          return $warenkorb;
        }
      }
    }

    foreach ($warenkorb['articlelist'] as $articleKey => $article) {
      unset($warenkorb['articlelist']['price_netto']);
    }

    if(!empty($warenkorb['versandkostenbrutto']) && !empty($warenkorb['versandkostennetto']))
    {
      unset($warenkorb['versandkostennetto']);
    }

    return $warenkorb;
  }


  //TODO: Umstellen des Auftrags nach dem er abgeholt wurde auf in inbearbeitung o.ä.
  public function ImportDeleteAuftrag()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $bearbeiter = 'Cronjob';
    if(!empty($this->app->User) && method_exists($this->app->User,'GetName')){
      $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
    }

    // pruefe ob $tmp[datei] vorhanden wenn nicht lege an sonst update [inhalt] und [checksum]
    $auftrag = $tmp['auftrag'];
    if(isset($this->app->DB) && $this->app->DB->connection)
    {
      if(!$this->app->DB->Select("SELECT id FROM `".$this->table."` WHERE `extid` = '".$auftrag."' AND shop = '".$this->shopid."' LIMIT 1"))
      {
        $this->app->DB->Insert("INSERT INTO `".$this->table."` (`extid`, `zeitstempel`,`shop`,`bearbeiter`) VALUES ('$auftrag', now(),'".$this->shopid."','$bearbeiter')");
      }
    }
    $this->adapter->call('orders/' . $auftrag . '/metafields.json', 'POST', array('metafield' => [
      'key' => 'sync_status',
      'value' => 1,
      'value_type' => 'integer',
      'namespace' => 'xentral',
    ]));
    return 'ok';
  }

  public function ImportStorniereAuftrag()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $auftrag = $tmp['auftrag'];


    if($auftrag != ''){
      $this->adapter->call('orders/'.$auftrag.'/cancel.json','POST');

      $result = $this->adapter->call('orders/'.$auftrag.'.json');
      $gesamtpreis = $result['data']['order']['total_price'];
      $lineitems = array();
      if(!empty($result['data']['order']['line_items'])){
        foreach ($result['data']['order']['line_items'] as $key => $value) {
          $lineitems[] = array('line_item_id' => $value['id'], 'quantity' => $value['quantity'],);
        }
      }
      $data = array('refund' => array('amount' => $gesamtpreis, 'refund_line_items' => $lineitems));
      $this->adapter->call('orders/'.$auftrag.'/refunds.json','POST',$data);

      $result = $this->adapter->call('orders/'.$auftrag.'/transactions.json');
      $transaktionsid = $result['data']['transactions'][0]['id'];
      $data = array('transaction' => array('amout' => $gesamtpreis, 'kind' => 'void', 'parent_id' => $transaktionsid));
      $this->adapter->call('orders/'.$auftrag.'/transactions.json','POST',$data);
      $this->adapter->call('orders/' . $auftrag . '/metafields.json', 'POST', array('metafield' => [
        'key' => 'sync_status',
        'value' => 3,
        'value_type' => 'integer',
        'namespace' => 'xentral',
      ]));
    }

    return 'OK';
  }

  public function ImportUpdateAuftrag()
  {
    $tmp = $this->CatchRemoteCommand('data');

    // pruefe ob $tmp[datei] vorhanden wenn nicht lege an sonst update [inhalt] und [checksum]
    $auftrag = $tmp['auftrag'];
    if(!empty($auftrag)){
      $zahlungok = $tmp['zahlung'];
      $versandok = $tmp['versand'];
      $tracking = $tmp['tracking'];
      $versandart = $tmp['versandart'];
      $data = array();
      $data['fulfillment'] = array('tracking_number' => $tracking, 'tracking_company' => $versandart, 'notify_customer' => false);
      if(!empty($this->location)){
        $data['fulfillment']['location_id'] = $this->location;
      }
      elseif(!empty($this->locations)) {
        $options = json_decode(base64_decode($this->locations),true);
        if(!empty($options) && is_array($options)) {
          if(isset($options[''])){
            unset($options['']);
          }
          if(count($options) === 1) {
            $options = array_keys($options);
            $data['fulfillment']['location_id'] = reset($options);
          }
        }
      }
      if($this->shopifytracking){
        $data['fulfillment']['notify_customer'] = true;
      }
      if(!empty($tmp['trackinglinkraw'])) {
        $data['fulfillment']['tracking_urls'] = [$tmp['trackinglinkraw']];
      }
      elseif(!empty($tmp['trackinglink'])){
        $data['fulfillment']['tracking_urls'] = [$tmp['trackinglink']];
      }

      $result = $this->adapter->call('orders/' . $auftrag . '/fulfillments.json', 'POST', $data);
      if($this->logging){
        $this->app->erp->LogFile(array($tmp, $auftrag, $data, $result['data']));
      }
      $this->adapter->call('orders/' . $auftrag . '/metafields.json', 'POST', array('metafield' => [
        'key' => 'sync_status',
        'value' => 2,
        'value_type' => 'integer',
        'namespace' => 'xentral',
      ]));
    }else{
      if($this->logging){
        $this->app->erp->LogFile(array($tmp, $auftrag,'Kein Auftrag'));
      }
    }
    return 'OK';
  }

  //TODO fuer Artikelexport
  // get checksum list from onlineshop
  public function ImportGetList()
  {

  }

    public function translateVariants($productId, $variantTranslations): void{
        $variantsInShopify = $this->adapter->call("products/$productId/variants.json", 'GET');

        $translationQuery = 'mutation translationsRegister($resourceId: ID!, $translations: [TranslationInput!]!) {
        translationsRegister(resourceId: $resourceId, translations: $translations) {
        translations { key locale outdated value }
        userErrors { code field message } } }';

        foreach ($variantsInShopify['data']['variants'] as $variant){
            $variantId = $variant['id'];

            $digestQuery = '{ translatableResource(resourceId: "gid://shopify/ProductVariant/'.$variantId.'") { resourceId
                translatableContent { key value digest locale } } }';
            $data = ['query' => $digestQuery];
            $digestInformation = $this->adapter->call('graphql.json', 'POST', $data, true);

            $digestMapping = [];
            foreach ($digestInformation['data']['data']['translatableResource']['translatableContent'] as $information){
                $digestMapping[$information['key']] = $information['digest'];
            }

            foreach ($variantTranslations['werte'] as $iso => $optionTranslations){
                $translationBlocks = [];

                for ($i = 1; $i <= 3; $i++){
                    $key = 'option'.$i;
                    if(!empty($variant[$key])
                        && array_key_exists($variant[$key], $optionTranslations)
                        && array_key_exists($key, $digestMapping)){
                        $translationBlocks[] = [
                            'locale' => strtolower($iso),
                            'key' => $key,
                            'value' => $optionTranslations[$variant[$key]],
                            'translatableContentDigest' => $digestMapping[$key]
                        ];
                    }
                }

                if(empty($translationBlocks)){
                    continue;
                }

                $variables = [
                    'resourceId' => $digestInformation['data']['data']['translatableResource']['resourceId'],
                    'translations' => $translationBlocks
                ];
                $translationRegister = [
                    'operationName' => 'translationsRegister',
                    'query' => $translationQuery,
                    'variables' => $variables
                ];
                $this->adapter->call('graphql.json', 'POST', $translationRegister, true, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
    }

    /**
     * @param string $productId
     * @param string $freeFieldTranslationData
     */
    public function translateFreeFields($productId, $freeFieldTranslationData): void
    {
        $query = '{ product(id: "gid://shopify/Product/' . $productId . '") {';
        $counter = 0;
        foreach ($freeFieldTranslationData as $metaFieldKey => $x) {
            $query .= sprintf('metafield_%d: metafield(namespace: "global", key: "%s") { key id}',
                $counter,
                $metaFieldKey);
            $counter++;
        }
        $query .= ' } }';

        $data = ['query' => $query];
        $productKeysData = $this->adapter->call('graphql.json', 'POST', $data, true);

        $metaFieldIdInformation = [];
        foreach ($productKeysData['data']['data']['product'] as $metaFieldData) {
            $metaFieldIdInformation[$metaFieldData['key']] = $metaFieldData['id'];
        }

        $translationQuery = 'mutation translationsRegister($resourceId: ID!, $translations: [TranslationInput!]!) {
        translationsRegister(resourceId: $resourceId, translations: $translations) {
        translations { key locale outdated value }
        userErrors { code field message } } }';

        foreach ($freeFieldTranslationData as $metaFieldKey => $translationData) {
            if (!isset($metaFieldIdInformation[$metaFieldKey])) {
                continue;
            }

            $digestQuery = '{ translatableResource(resourceId: "' . $metaFieldIdInformation[$metaFieldKey] . '") { resourceId
                translatableContent { key value digest } } }';

            $data = ['query' => $digestQuery];
            $digestInformation = $this->adapter->call('graphql.json', 'POST', $data, true);

            if (empty($digestInformation['data']['data']['translatableResource']['translatableContent'][0]['digest'])) {
                continue;
            }
            $digest = $digestInformation['data']['data']['translatableResource']['translatableContent'][0]['digest'];

            $translationBlocks = [];
            foreach ($translationData as $iso => $translation) {
                $translationBlocks[] = [
                    'locale' => $iso,
                    'key' => 'value',
                    'value' => $translation,
                    'translatableContentDigest' => $digest
                ];
            }
            if (!empty($translationBlocks)) {
                $variables = [
                    'resourceId' => $metaFieldIdInformation[$metaFieldKey],
                    'translations' => $translationBlocks
                ];
                $translationRegister = [
                    'operationName' => 'translationsRegister',
                    'query' => $translationQuery,
                    'variables' => $variables
                ];
                $this->adapter->call('graphql.json', 'POST', $translationRegister, true, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
    }

    /**
     * @param string $productId
     * @param array $translationData
     */
    public function translateProduct($productId, $translationData): void
    {
        $data = ['query' => 'query { translatableResource(resourceId: "gid://shopify/Product/'.$productId.'") { resourceId
      translatableContent { key value digest locale } } }'];
        $productInformationData = $this->adapter->call('graphql.json', 'POST', $data, true);

        $digestInformation = [];
        foreach ($productInformationData['data']['data']['translatableResource']['translatableContent'] as $information) {
            $digestInformation[$information['key']] = $information['digest'];
        }

        $translationQuery = 'mutation translationsRegister($resourceId: ID!, $translations: [TranslationInput!]!) {
        translationsRegister(resourceId: $resourceId, translations: $translations) {
        translations { key locale outdated value }
        userErrors { code field message } } }';
        foreach ($translationData as $iso => $translations) {
            $translationBlocks = [];
            foreach ($translations as $key => $translation) {
                $translationBlocks[] = [
                    'locale' => $iso,
                    'key' => $key,
                    'value' => $translation,
                    'translatableContentDigest' => $digestInformation[$key]
                ];
            }
            if (!empty($translationBlocks)) {
                $variables = [
                    'resourceId' => 'gid://shopify/Product/' . $productId,
                    'translations' => $translationBlocks
                ];
                $translationRegister = [
                    'operationName' => 'translationsRegister',
                    'query' => $translationQuery,
                    'variables' => $variables
                ];
                $this->adapter->call('graphql.json', 'POST', $translationRegister, true, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
    }


  public function ImportAuth()
  {
    if((String)$this->ShopifyToken !== ''
      ||
      ((String)$this->ShopifyURL !== ''
      && (String)$this->ShopifyAPIKey !== ''
      && (String)$this->ShopifyPassword !== '')
    ) {
      $data = $this->CatchRemoteCommand('data');
      if($data)
      {
        $result = $this->adapter->call('locations.json','','',false);
        if(strpos($result['data'], '<html><body>') === 0) {
          $result = $this->adapter->call('orders/count.json?fulfillment_status=unshipped&financial_status=paid','','',false);
        }else{
          $result['data'] = json_decode($result['data'],true);
          if(!$result['data']){
            $result = $this->adapter->call('orders/count.json?fulfillment_status=unshipped&financial_status=paid','','',false);
          }else{
            if(!empty($result['data']['locations']))
            {
              $locations = array(''=>'');
              foreach($result['data']['locations'] as $v)
              {
                if($v['active'])
                {
                  $locations[$v['id']] = $v['name'];
                }
              }
              if(!empty($this->shopid)){
                $einstellungen = $this->app->DB->Select("SELECT einstellungen_json FROM shopexport WHERE id = '$this->shopid' LIMIT 1");
                if(!empty($einstellungen)){
                  $einstellungen = json_decode($einstellungen, true);
                }else{
                  $einstellungen = [];
                }
                $einstellungen['felder']['locations'] = base64_encode(json_encode($locations));
                if(empty($einstellungen['felder']['location'])){
                  foreach ($locations as $locationId => $locationName){
                    if(!empty($locationId)){
                      $einstellungen['felder']['location'] = $locationId;
                      break;
                    }
                  }
                }
                $this->app->DB->UPDATE("UPDATE shopexport SET einstellungen_json = '" . $this->app->DB->real_escape_string(json_encode($einstellungen)) . "' WHERE id = '$this->shopid'");
              }
              return 'success';
            }
          }
        }
        if(strpos($result['data'], '<html><body>') === 0) {
          return 'Bitte URL prüfen';
        }
        $result['data'] = json_decode($result['data'],true);
        if(!$result['data']){
          return 'Bitte API-Daten prüfen';
        }
        if(isset($result['data']['errors'])){
          return $result['data']['errors'];
        }
      }
      return 'success';
    }

    return 'Bitte API-Daten ausfüllen';
  }


  function ShopifyLog($nachricht, $dump = ''){
    if($this->logging){
      $this->app->erp->LogFile($nachricht, print_r($dump,true));
    }
  }


  function ApiConnect($path, $anweisung = '', $data = '', $withheader = 0)
  {
    if(empty(self::$requestcount[$this->shopid]))
    {
      self::$requestcount[$this->shopid] = 1;
    }else{
      self::$requestcount[$this->shopid]++;
    }
    if(self::$requestcount[$this->shopid] >= 30)
    {
      sleep(2);
      self::$requestcount[$this->shopid]-=2;
    }elseif(self::$requestcount[$this->shopid] >= 20)
    {
      sleep(2);
      self::$requestcount[$this->shopid]-=2;
    }
    $url = $this->ShopifyURL.$path;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_VERBOSE, 0);
    //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HEADER, $withheader);
    if($anweisung != '')curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $anweisung);
    if($data != '')curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec ($curl);
    $httpcode = curl_getinfo($curl);
    $httpcode = $httpcode['http_code'];
    if($httpcode == '429')
    {
      sleep(5);
      self::$requestcount[$this->shopid] = 40;
      return $this->ApiConnect($path, $anweisung, $data, $withheader);
    }
    curl_close ($curl);
    if(!empty($response['errors']) && preg_match_all('/\This action requires merchant approval for ([a-zA-Z\_]+) scope/',$response['errors'], $erg) && !empty($erg[1]))
    {
      $this->SetError($erg[1][0], $response['errors']);
    }

    return $response;
  }

  public function SetError($scope, $error)
  {
    $scope = $this->app->DB->real_escpa_string($scope);
    $error = $this->app->DB->real_escpa_string($error);
    $bearbeiter = $this->app->erp->GetBearbeiter(true);
    $this->app->DB->Insert('INSERT INTO shopexport_log (shopid, typ,parameter1,parameter2,bearbeiter,zeitstempel)
        VALUES ('.$this->shopid.",'fehler','Fehlendes API-Recht: $scope','$error','$bearbeiter',now())");
  }

  function SendResponseAES($value)
  {
    $z = $this->app->Conf->ImportKey;//"12345678912345678912345678912345"; // 256-bit key
    $aes = new AES($z);
    return base64_encode($aes->encrypt(serialize($value)));
  }


  function FremdnummerInsert($artikelid, $fremdnummer, $bezeichnung){
    $shopid = $this->shopid;
    if(!empty($fremdnummer)){
      $fremdnummerid = $this->app->DB->Select("SELECT id FROM artikelnummer_fremdnummern WHERE artikel=$artikelid AND shopid='$shopid' AND aktiv='1' AND bezeichnung='$bezeichnung'");
      if($fremdnummerid ){
        $this->app->DB->Update("UPDATE artikelnummer_fremdnummern SET nummer='$fremdnummer' WHERE id ='$fremdnummerid'");
      }else{
        $this->app->DB->Insert("INSERT INTO artikelnummer_fremdnummern (shopid, artikel, nummer, bezeichnung, bearbeiter) 
          VALUES ('$shopid','$artikelid','$fremdnummer','$bezeichnung', 'Shopimporter')");
      }
    }
  }

  /**
   * @param array $shopArr
   * @param array $postData
   *
   * @return array
   */
  public function updateShopexportArr($shopArr, $postData)
  {
    $shopArr['anzgleichzeitig'] = 50;
    $shopArr['datumvon'] = date('Y-m-d H:i:s');
    $shopArr['demomodus'] = 0;

    return $shopArr;
  }

  /**
   * @return JsonResponse|null
   */
  public function AuthByAssistent()
  {
    $shopifyURL = $this->app->Secure->GetPOST('ShopifyURL');
    $this->shopifytracking = !empty($this->app->Secure->GetPOST('shopifytracking'));
    if(empty($shopifyURL)) {
      return new JsonResponse(['error' => 'Bitte die URL des Shops angeben.'], JsonResponse::HTTP_BAD_REQUEST);
    }
    $shopifyURL = trim($shopifyURL);
    if(stripos($shopifyURL,'http') === false){
      $shopifyURL = 'https://'.$shopifyURL;
    }

    $shopifyAPIKey = $this->app->Secure->GetPOST('ShopifyAPIKey');
    $shopifyPassword = $this->app->Secure->GetPOST('ShopifyPassword');
    $shopifyToken = $this->app->Secure->GetPOST('ShopifyToken');
    $step = (int)$this->app->Secure->GetPOST('step');
    $this->ShopifyToken = $shopifyToken;
    $this->ShopifyAPIKey = $shopifyAPIKey;
    $this->ShopifyPassword = $shopifyPassword;
    if(empty($shopifyToken) && (empty($shopifyAPIKey) || empty($shopifyPassword))) {
      return new JsonResponse(['error' => 'Bitte ApiKey/Passwort oder Token angeben'], JsonResponse::HTTP_BAD_REQUEST);
    }
    if(empty($shopifyToken)) {
      if(strpos($shopifyURL, 'https://') !== false) {
        $shopifyURL = 'https://' . $shopifyAPIKey . ':' . $shopifyPassword . '@' . str_replace('https://', '', $shopifyURL);
      }
      else{
        $shopifyURL = 'http://' . $shopifyAPIKey . ':' . $shopifyPassword . '@' . str_replace('http://', '', $shopifyURL);
      }
    }
    $this->ShopifyURL = $shopifyURL;
    if($step < 1){
      $adapter = new Shopimporter_Shopify_Adapter($this->app, $shopifyURL, 0, $shopifyToken);
      $this->setAdapter($adapter);
      $this->data = true;

      $result = $this->adapter->call('locations.json', '', '', false);
      if(strpos($result['data'], '<html><body>') === 0){
        return new JsonResponse(['error' => 'bitte prüfen Sie die Zugangsdaten'], JsonResponse::HTTP_BAD_REQUEST);
      }

      $result['data'] = @json_decode($result['data'], true);

      if(!$result['data']){
        return new JsonResponse(['error' => 'bitte prüfen Sie die Zugangsdaten'], JsonResponse::HTTP_BAD_REQUEST);
      }
      if(empty($result['data']['locations'])){
        return null;
      }
      $locations = ['' => ''];
      $this->location = '';
      $activeCount = 0;
      foreach ($result['data']['locations'] as $location) {
        if($location['active']){
          $activeCount++;
          $locations[$location['id']] = $location['name'];
          if($this->location === '') {
            $this->location = $location['id'];
          }
        }
      }
      $this->locations = base64_encode(json_encode($locations));
      if($activeCount > 1) {
        $this->location = '';
      }
      return $this->getStorageSelectionPage(null, $locations);
    }

    return null;
  }

  /**
   * @return array
   */
  public function getVueLocations()
  {
    $ret = [];
    $locations = json_decode(base64_decode($this->locations), true);
    foreach($locations as $locationId => $location) {
      $ret[] = [
        'value' => $locationId,
        'text' => $location,
      ];
    }

    return $ret;
  }

  /**
   * @param null|array $requiredForSubmit
   *
   * @return JsonResponse
   */
  public function getStorageSelectionPage($requiredForSubmit = null, $locations = [])
  {
    if($requiredForSubmit === null) {
      $requiredForSubmit = $this->app->Secure->POST;
      $requiredForSubmit['step'] = 1;
      $requiredForSubmit = $this->updatePostDataForAssistent($requiredForSubmit);
    }

    $page = [
      'type' => 'form',
      'submitType' => 'submit',
      'icon'=> 'password-icon',
      'headline' => 'Shopify',
      'subHeadline' => 'Bitte wähle den Shop aus?',
      'submitUrl' => 'index.php?module=onlineshops&action=create&cmd=saveassistent&shopmodule=shopimporter_shopify',
      'form' => [
      ],
      'ctaButtons' => [
        [
          'title' => 'Weiter',
          'type' => 'submit',
          'action' => 'submit',
        ],
      ]
    ];

    if(count($locations) > 2) {
      $page['form'][] = [
        'id' => 0,
        'name' => 'exportArticlesGroup',
        'inputs' => [
          [
            'label' => 'Shop',
            'type' => 'select',
            'name' => 'location',
            'validation' => false,
            'options' => $this->getVueLocations(),
          ],

        ],
      ];
    }
    $page['form'][] = [
      'id' => 1,
      'name' => 'sendTrackingGroup',
      'inputs' => [
        [
          'label' => 'Tracking E-Mails über Shopify versenden',
          'type' => 'checkbox',
          'name' => 'shopifytracking',
        ],
      ],
    ];

    return new JsonResponse(
      [
        'page' => $page,
        'dataRequiredForSubmit' => $requiredForSubmit,
      ]
    );
  }

  /**
   * @param array $postData
   *
   * @return array
   */
  public function updatePostDataForAssistent($postData)
  {
    if(!empty($this->ShopifyURL)) {

      $url = $this->ShopifyURL;
      if(!empty($this->ShopifyAPIKey) && !empty($this->ShopifyPassword) && empty($this->ShopifyToken)) {
        $url = str_replace($this->ShopifyAPIKey . ':' . $this->ShopifyPassword . '@','', $url);
      }

      $postData['ShopifyURL'] = $url;
    }
    if(!empty($this->shopifytracking)) {
      $postData['shopifytracking'] = 1;
    }
    if(!empty($this->locations)) {
      $postData['locations'] = $this->locations;
    }
    if(!empty($this->location) && empty($postData['location'])) {
      $postData['location'] = $this->location;
    }

    return $postData;
  }

  /**
   * @return array
   */
  public function getStructureDataForClickByClickSave()
  {
    $ret = [];
    $locations = $this->app->Secure->GetPOST('locations');
    if(!empty($locations)) {
      $ret['locations'] = $locations;
    }
    $location = $this->app->Secure->GetPOST('location');
    if(!empty($location)) {
      $ret['location'] = $location;
    }

    if(!empty($this->app->Secure->GetPOST('shopifytracking'))) {
      $ret['shopifytracking'] = 1;
    }

    return $ret;
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
            'label' => 'URL des Shops',
            'type' => 'text',
            'name' => 'ShopifyURL',
            'validation' => true,
          ],

        ],
      ],
      [
        'id' => 1,
        'name' => 'username',
        'inputs' => [
          [
            'label' => 'API-Key aus Shopify',
            'type' => 'text',
            'name' => 'ShopifyAPIKey',
            'validation' => false,
          ],
        ],
      ],
      [
        'id' => 2,
        'name' => 'password',
        'inputs' => [
          [
            'label' => 'Passwort aus Shopify',
            'type' => 'password',
            'name' => 'ShopifyPassword',
            'validation' => false,
          ],
        ],
      ],
      [
        'id' => 3,
        'name' => 'token',
        'inputs' => [
          [
            'label' => 'Token aus Shopify',
            'type' => 'text',
            'name' => 'ShopifyToken',
            'validation' => false,
          ],
        ],
      ],
    ];
  }

  public function getBoosterHeadline(): string
  {
    return 'Shopify Business Booster App';
  }

  public function getBoosterSubHeadline(): string
  {
    return 'Bitte gehe auf dein Shopify Shop und installiere dort die App Xentral Business Booster App. 
    Dort kann man sich dann mit ein paar Klicks mit Xentral verbinden';
  }

  /**
   * @return string
   */
  public function getYoutubeLink(): string
  {
    return 'https://www.youtube.com/embed/xEBl3h8mIPg';
  }
}
