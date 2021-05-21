<?php

/**
 * Class ShopimporterBase
 */

Class ShopimporterBase{
  const CANNOT = 1;//1: kann nicht (Importer) - inaktiv,disabled
  const CAN = 2;//2: kann -enabled
  const MUST = 3;//3: muss -aktiv,disabled
  const UNDEFINED = 4;//4: nicht definiert -
  const CANNOTSHOP = 5; //5: not supported by shop

  protected $articlecapability = [];
  protected $canexport = true;
  protected $canimport = true;

  /**
   * @return array
   */
  public function importerCapability()
  {
    if(empty($this->articlecapability))
    {
      $this->importerLoadCapability();
    }
    return $this->articlecapability;
  }

  /**
   * @return bool
   */
  public function importerCanImport()
  {
    return $this->canimport;
  }

  /**
   * @return bool
   */
  public function importerCanExport()
  {
    return $this->canexport;
  }

  /**
   * @param int    $file
   * @param string $name
   * @param string $shopname
   * @param string $url
   * @param string $overWriteUrl
   *
   * @return bool|string|null
   */
  public function saveFileToServer(
    $file,
    $name,
    $shopname = '',
    $url = '',
    $overWriteUrl = ''
  )
  {
    return $this->saveFile(
      $file,
      $name,
      $shopname,
      $url,
      $overWriteUrl
    );
  }

  /**
   * @param int    $file
   * @param string $name
   * @param string $shopname
   * @param string $url
   * @param string $overWriteUrl
   *
   * @return bool|string|null
   */
  protected function saveFile(
    $file,
    $name,
    $shopname = '',
    $url = '',
    $overWriteUrl = ''
  )
  {
    if($url === '') {
      $url = "https://{$this->app->Conf->updateHost}/shopimage.php";
    }
    if(empty($file) || empty($url)) {
      return null;
    }
    $serial = $this->app->erp->Firmendaten('lizenz');
    if(empty($serial)){
      return null;
    }
    if(empty($name)){
      $name = $this->app->erp->GetDateiName($file);
    }
    if(empty($name)){
      return null;
    }
    $content = base64_encode($this->app->erp->GetDatei($file));
    if(empty($content)){
      return null;
    }

    $paras = [
      'serial'=>$serial,
      'name'=>$name,
      'image'=>$content,
      'shopname' => $shopname,
      'lasturl' => $overWriteUrl,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, True);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $paras);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);
    $ret = curl_exec($ch);
    curl_close($ch);

    return $ret;
  }

  /**
   * @param $configurationArray
   */
  public function setConfiguration($configurationArray){
    foreach ($configurationArray as $parameter => $value){
      $this->$parameter = $value;
    }
  }

  protected function importerLoadCapability(){
    $ability['auftrag'] = array(
      'name' => 'Auftrag Importieren',
      'info' => 'Der Import von Aufträgen ist verfügbar',
      'description' => 'Importiert Aufträge aus dem Shop',
      'tooltip' => 'Wird regelmäßig vom Prozessstarter übertragen');
    $ability['artikelname'] = array(
      'name' => 'Artikelbezeichnung',
      'info' => 'Einstellungen wurde beim Erstellen aus dem Tab "Einstellungen" übernommen',
      'description' => '&Uuml;bertr&auml;gt den Namen des Artikels zum Shop',
      'tooltip' => 'Wird beim Anlegen eines neuen Artikels immer &uuml;bertragen');
    $ability['artikelnummer'] = array(
      'name' => 'Artikelnummer',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt die Artikelnummer zum Shop',
      'tooltip' => '');
    $ability['beschreibung'] = array(
      'info' => '',
      'name' => 'Artikelbeschreibung',
      'description' => '&Uuml;bertr&auml;gt die Artikelbeschreibung',
      'tooltip' => 'Genaugenommen die Beschreibung aus OnlineShop Texte des Artikels');
    $ability['kurztext'] = array(
      'info' => '',
      'name' => 'Kurztext',
      'description' => '&Uuml;bertr&auml;gt den Kurztext des Artikels',
      'tooltip' => '');
    $ability['aktiv'] = array(
      'name' => 'Aktiv',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt Aktiv/Inaktiv Status an den Shop',
      'tooltip' => '');
    $ability['restmenge'] = array(
      'name' => 'Restmenge',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt Restmenge/Abverkauf an den Shop',
      'tooltip' => '');
    $ability['umsatzsteuer'] = array(
      'name' => 'Umsatzsteuer',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt die Umsatzsteuer des Artikels',
      'tooltip' => '');
    $ability['ean'] = array(
      'name' => 'EAN',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt die EAN des Artikels',
      'tooltip' => '');
    $ability['hersteller'] = array(
      'name' => 'Hersteller',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt den Hersteller des Artikels',
      'tooltip' => '');
    $ability['herstellernummer'] = array(
      'name' => 'Herstellernummer',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt die Artikelnummer des Herstellers',
      'tooltip' => '');
    $ability['herstellerlink'] = array(
      'name' => 'Herstellerlink',
      'info' => '',
      'description' => 'Link zum Artikel beim Hersteller',
      'tooltip' => '');
    $ability['zolltarifnummer'] = array(
      'name' => 'Zolltarifnummer',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt die Zolltrarifnummer des Artikels',
      'tooltip' => '');
    $ability['gewicht'] = array(
      'name' => 'Gewicht',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt das Gewicht des Artikels',
      'tooltip' => '');
    $ability['einheit'] = array(
      'name' => 'Artikeleinheit',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt die Einheit des Artikels',
      'tooltip' => '');
    $ability['laenge'] = array(
      'name' => 'L&auml;nge',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt die L&auml;nge des Artikels',
      'tooltip' => '');
    $ability['hoehe'] = array(
      'name' => 'Gewicht',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt die H&ouml;he des Artikels',
      'tooltip' => '');
    $ability['breite'] = array(
      'name' => 'Breite',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt die Breite des Artikels',
      'tooltip' => '');
    $ability['verkaufspreis'] = array(
      'name' => 'Verkaufspreis',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt den Verkaufspreis',
      'tooltip' => 'Genaugenommen den Standardpreis des Artikels');
    $ability['pseudopreis'] = array(
      'name' => 'Pseudopreis',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt den Pseudopreis',
      'tooltip' => 'Falls eingetragen');
     $ability['gruppenpreise'] = array(
      'name' => 'Gruppenpreise',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt die Gruppenpreise',
      'tooltip' => 'zus&auuml;tzlich zum Standard-Verkaufspreis');
    $ability['staffelpreise'] = array(
      'name' => 'Staffelpreise',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt die Staffelpreise',
      'tooltip' => 'zus&auuml;tzlich zum Standard-Verkaufspreis');
    $ability['lagerzahlen'] = array(
      'name' => 'Lagerzahlen',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt Lagerzahlen',
      'tooltip' => 'Genaugenommen den Lagerzahlen mit ggf. eingestellten Korrekturwert des Artikels');
    $ability['pseudolager'] = array(
      'name' => 'Pseudolagerzahl',
      'info' => '&Uuml;berschreibt die tatsaechlichen Lagerzahlen',
      'description' => '&Uuml;bertr&auml;gt die Anzahl im Pseudolager',
      'tooltip' => 'Falls eingetragen');
    $ability['fremdsprachen'] = array(
      'name' => 'Fremdsprache',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt sofern verf&uuml;gbar Artikeldaten in Fremdsprachen zum Shop',
      'tooltip' => 'F&uuml;r alle Sprachen außer Englisch wird das Modul ArtikelTexte benötigt');
    $ability['kategorie'] = array(
      'name' => '(Haupt-) Kategorie',
      'info' => '',
      'description' => 'Verschiebt den Artikel in die passende Kategorie',
      'tooltip' => 'Wenn Kategoriebaum &uuml;bertragen wird,wird der Artikel in die markierten Kategorien verschoben');
    $ability['kategoriebaum'] = array(
      'name' => 'Kategoriebaum / Artikelbaum',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt den Kategoriebaum zum Shop',
      'tooltip' => 'Hat nur Auswirkung in Kombination mit den anderen Kategorieoptionen');
    $ability['bilder'] = array(
      'name' => 'Bilder',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt Artikelbilder zum Shop',
      'tooltip' => '&Uuml;bertr&auml;gt alle Dateien die dem Artikel zugeordnet sind');
    $ability['varianten'] = array(
      'name' => 'Varianten',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt Verianten des Artikels zum Shop',
      'tooltip' => '&Uuml;bertr&auml;gt alle Verianten die dem Artikel zugeordnet sind');
    $ability['matrixeigenschaften'] = array(
      'name' => 'Matrixartikel / Matrixeigenschaften',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt Matrixproduktvarianten des Artikels zum Shop',
      'tooltip' => '&Uuml;bertr&auml;gt alle Matrixproduktvarianten die dem Artikel zugeordnet sind');
    $ability['eigenschaften'] = array(
      'name' => 'Eigenschaften',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt Eigenschaften des Artikels zum Shop',
      'tooltip' => '&Uuml;bertr&auml;gt alle Eigenschaften zum Artikel');
    $ability['crossselling'] = array(
      'name' => 'Crossselling',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt Crosssellingartikel des Artikels zum Shop',
      'tooltip' => '&Uuml;bertr&auml;gt alle Crosssellingartikel die dem Artikel zugeordnet sind');
    $ability['herkunftsland'] = array(
      'name' => 'Herkunftsland',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt das Herkunftsland des Artikels',
      'tooltip' => '');
    $ability['metatitel'] = array(
      'name' => 'Metatitel',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt den Metatitel des Artikels',
      'tooltip' => '');
    $ability['metabeschreibung'] = array(
      'name' => 'Metabeschreibung',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt die Metabeschreibungdes Artikels',
      'tooltip' => '');
    $ability['metakeywords'] = array(
      'name' => 'Metakeywords',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt die Metakeywords des Artikels',
      'tooltip' => '');
    $ability['freifelder'] = array(
      'name' => 'Freifelder',
      'info' => '',
      'description' => '&Uuml;bertr&auml;gt die Freifelder des Artikels',
      'tooltip' => '');


    foreach ($ability as $key => $value){
      $ability[$key]['createarticle'] = self::UNDEFINED;
      $ability[$key]['updatearticle'] = self::UNDEFINED;
      $ability[$key]['importarticle'] = self::UNDEFINED;
      $ability[$key]['importorder'] = self::UNDEFINED;
      $ability[$key]['createarticledefault'] = false;
      $ability[$key]['updatearticledefault'] = false;
      $ability[$key]['importarticledefault'] = false;
      $ability[$key]['importorderdefault'] = false;
    }

    $this->articlecapability = $ability;
  }

  /**
   * @return bool
   */
  public function canGetOrderStatus(){
    return method_exists( $this,'ImportGetOrderStatus');
  }

  /**
   * @param $name
   * @param $createarticle
   * @param $updatearticle
   * @param $importarticle
   * @param $importorder
   */
  protected function importerSetCapability($name, $createarticle, $updatearticle, $importarticle, $importorder){
    $this->articlecapability[$name]['createarticle'] = $createarticle;
    $this->articlecapability[$name]['updatearticle'] = $updatearticle;
    $this->articlecapability[$name]['importarticle'] = $importarticle;
    $this->articlecapability[$name]['importorder'] = $importorder;
  }

  /**
   * @return bool
   */
  public function canExportAdress(){
    return method_exists( $this,'ImportSendAdresse');
  }

  /**
   * @return bool
   */
  public function canImportAdress(){
    return method_exists( $this,'ImportGetAdresse');
  }

  /**
   * @return bool
   */
  public function canExportArticleTree(){
    return method_exists( $this,'ImportSendArtikelbaum');
  }

  /**
   * @param string $value
   */
  public function CatchRemoteCommandAES($value)
  {

  }

  /**
   * @param string $value
   *
   * @return mixed
   */
  public function CatchRemoteCommand($value)
  {
    return $this->data;
  }

  public function CatchRemoteAuth()
  {

  }

  /**
   * @param string $value
   *
   * @return mixed
   */
  public function SendResponse($value)
  {
    return $value;
  }

  /**
   * @param string $value
   */
  public function SendResponseAES($value)
  {

  }

  /**
   * @param int    $shopId
   * @param int    $articleId
   * @param string $name
   *
   * @return array|string
   */
  public function GetShopArtikel($shopId, $articleId, $name = '')
  {
    if(empty($name)) {
      return $this->getShopexportArticleValues($shopId, $articleId);
    }

    return $this->getShopexportArticleValue($shopId, $articleId, $name);
  }

  /**
   * @param int    $shopId
   * @param int    $articleId
   * @param string $name
   *
   * @return string
   */
  public function getShopexportArticleValue($shopId, $articleId, $name) {
    return (String)$this->app->DB->Select(
      sprintf('SELECT sa.`wert` 
        FROM `shopexport_artikel` AS sa
        WHERE sa.`shopid` = %d AND sa.`artikel` = %d  AND sa.`name` <> \'\' AND sa.`name` = \'%s\'
        LIMIT 1',
        $shopId, $articleId, $this->app->DB->real_escape_string($name)
       )
      );
  }

  /**
   * @param int $shopId
   * @param int $articleId
   *
   * @return array
   */
  public function getShopexportArticleValues($shopId, $articleId) {
    $arr = $this->app->DB->SelectArr(
      sprintf('SELECT sa.`name`, sa.`wert` 
        FROM `shopexport_artikel` AS sa
        WHERE sa.`shopid` = %d AND sa.`artikel` = %d  AND sa.`name` <> \'\'',
        $shopId, $articleId
      )
    );
    $shopexportArticleValues = [];
    if(!empty($arr)) {
      foreach($arr as $keyRow => $row) {
        $shopexportArticleValues[$row['name']] = $row['wert'];
      }
    }

    return $shopexportArticleValues;
  }

  /**
   * @param int $shopId
   * @param int $articleId
   *
   * @return array
   */
  public function getNotEmptyShopexportArticleValues($shopId, $articleId) {
    $arr = $this->app->DB->SelectArr(
      sprintf('SELECT sa.`name`, sa.`wert` 
        FROM `shopexport_artikel` AS sa
        WHERE sa.`shopid` = %d AND sa.`artikel` = %d  AND sa.`name` <> \'\'
          AND sa.`wert`<> \'\'',
        $shopId, $articleId
      )
    );
    $shopexportArticleValues = [];
    if(!empty($arr)) {
      foreach($arr as $keyRow => $row) {
        $shopexportArticleValues[$row['name']] = $row['wert'];
      }
    }

    return $shopexportArticleValues;
  }

  /**
   * @param int $shopId
   *
   * @return bool
   */
  public function isInDemoMode($shopId = 0){
    if($this->app->DB->Select("SELECT demomodus FROM shopexport WHERE id = $shopId")){

      return true;
    }

    return false;
  }

  /**
   * @param int    $shopid
   * @param string $typ
   * @param string $nummer
   * @param string $parameter2
   * @param string $parameter3
   * @param string $parameter4
   */
  public function shopLog($shopid,$typ,$nummer,$parameter2,$parameter3='',$parameter4=''){
    $this->app->DB->Insert("INSERT INTO shopexport_log(shopid,typ,bearbeiter,parameter1,parameter2,parameter3,parameter4,zeitstempel) 
        VALUES ($shopid,'$typ','".$this->app->User->GetUsername()."','$nummer',
        '".$this->app->DB->real_escape_string($parameter2)."','$parameter3','$parameter4',NOW())");
  }

  /**
   * Es werden Werte nur hinzugefuegt falls der Wert noch nicht existiert bzw. leer ist
   *
   * @refactor in ShopImporter Module
   *
   * @param int $shopId Shopid
   * @param int $articleId Artikelid
   * @param array $arr Array zum Einfuegen / Updaten
   * @param bool $escape Werte werden fuer DB escaped
   *
   * @return bool wenn Daten OK true sonst falsch
   */
  public function UpdateShopArtikel($shopId, $articleId, $arr, $escape = true)
  {
    if(empty($articleId) || empty($shopId) || empty($arr)){
      return false;
    }
    $bearbeiter = $this->app->erp->GetBearbeiter(true);
    $oldarr = array();
    $old = $this->app->DB->SelectArr("SELECT min(id) as id, name,wert  FROM shopexport_artikel 
      WHERE shopid = '$shopId' AND artikel = '$articleId' GROUP BY name,wert");
    if(!empty($old))
    {
      foreach($old as $v)
      {
        $oldarr[$v['name']] = array('id'=>$v['id'],'wert'=>$v['wert']);
      }
    }

    foreach($arr as $k => $v)
    {
      if($k)
      {
        $check = !empty($oldarr[$k])?$oldarr[$k]['id']:0;
        //$check = $this->app->DB->Select("SELECT id FROM shopexport_artikel WHERE shopid = '$shop' AND artikel = '$artikel' AND name = '".$this->app->DB->real_escape_string($k)."' LIMIT 1");
        if(!$check)
        {
          $this->app->DB->Insert("INSERT INTO shopexport_artikel (shopid, artikel, name) VALUES ('$shopId','$articleId','".$this->app->DB->real_escape_string($k)."')");
          $new = $this->app->DB->GetInsertID();
          $check = $new;
        }else{
          $new = null;
        }
        if(!empty($new) || $oldarr[$k]['wert'] != $v){
          if($escape){
            $this->app->DB->Update("UPDATE shopexport_artikel SET bearbeiter = '$bearbeiter', wert = '" . $this->app->DB->real_escape_string($v) . "' WHERE id = '$check' LIMIT 1");
          }else{
            $this->app->DB->Update("UPDATE shopexport_artikel SET bearbeiter = '$bearbeiter', wert = '$v' WHERE id = '$check' LIMIT 1");
          }
        }
      }
    }
    return true;
  }

  /**
   * @var string $moduleName
   *
   * @return string[]
   */
  public static function storageNotNeededElements(string $moduleName): array
  {
    $notNeededElementsByModule = [
      'shopimporter_ebay' => [
        'foreign_numbers',
        'article_descriptions',
        'translations',
        'categories',
        'properties',
        'pictures',
        'cross_selling',
        'bulk_prices',
        'standard_price',
        'variants',
        'free_fields',
      ],
      'shopimporter_amazon' => [
        'article_descriptions',
        'translations',
        'categories',
        'properties',
        'pictures',
        'cross_selling',
        'bulk_prices',
        'standard_price',
        'variants',
        'free_fields',
      ],
      'shopimporter_presta' => [
        'article_descriptions',
        'translations',
        'categories',
        'properties',
        'pictures',
        'cross_selling',
        'bulk_prices',
        'standard_price',
        'variants',
        'free_fields',
      ],
      'shopimporter_shopify' => [
        'article_descriptions',
        'translations',
        'categories',
        'properties',
        'pictures',
        'cross_selling',
        'bulk_prices',
        'standard_price',
        'free_fields',
      ],
      'shopimporter_shopware' => [
        'article_descriptions',
        'translations',
        'categories',
        'properties',
        'pictures',
        'cross_selling',
        'bulk_prices',
        'standard_price',
        'free_fields',
      ],
      'shopimporter_shopware6' => [
        'article_descriptions',
        'translations',
        'categories',
        'properties',
        'pictures',
        'cross_selling',
        'bulk_prices',
        'standard_price',
        'free_fields',
      ],
      'shopimporter_magento' => [
        'article_descriptions',
        'translations',
        'categories',
        'properties',
        'pictures',
        'cross_selling',
        'bulk_prices',
        'standard_price',
        'variants',
        'free_fields',
      ],
      'shopimporter_magento2' => [
        'article_descriptions',
        'translations',
        'categories',
        'properties',
        'pictures',
        'cross_selling',
        'bulk_prices',
        'standard_price',
        'variants',
        'free_fields',
      ],
      'shopimporter_gambio' => [
        'article_descriptions',
        'translations',
        'categories',
        'properties',
        'pictures',
        'cross_selling',
        'bulk_prices',
        'standard_price',
        'free_fields',
      ],
      'shopimporter_woocommerce' => [
        'article_descriptions',
        'translations',
        'categories',
        'properties',
        'pictures',
        'cross_selling',
        'bulk_prices',
        'standard_price',
        'variants',
        'free_fields',
      ],
      'shopimporter_tillhub' => [
        'article_descriptions',
        'translations',
        'categories',
        'properties',
        'pictures',
        'cross_selling',
        'bulk_prices',
        'standard_price',
        'free_fields',
      ],
      'shopimporter_real' => [
        'article_descriptions',
        'translations',
        'categories',
        'properties',
        'pictures',
        'cross_selling',
        'bulk_prices',
        'variants',
        'free_fields',
      ],
      'shopimporter_hood' => [
        'article_descriptions',
        'translations',
        'categories',
        'properties',
        'pictures',
        'cross_selling',
        'bulk_prices',
        'standard_price',
        'free_fields',
      ],
      'shopimporter_spryker' => [
        'article_descriptions',
        'translations',
        'categories',
        'properties',
        'pictures',
        'cross_selling',
        'bulk_prices',
        'standard_price',
        'free_fields',
      ],
    ];
    if(isset($notNeededElementsByModule[$moduleName])) {
      return $notNeededElementsByModule[$moduleName];
    }

    return [];
  }

  /**
   * @return int[][][]
   */
  public static function shopCapabilities()
  {
    return [
      'shopimporter_hood' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'beschreibung' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_gambio' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CAN, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CAN, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'beschreibung' => ['createarticle' => self::MUST, 'updatearticle' => self::CANNOT, 'importarticle' => self::CAN, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CAN, 'updatearticle' => self::CANNOT, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CAN, 'updatearticle' => self::CANNOT, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::MUST, 'updatearticle' => self::CANNOT, 'importarticle' => self::CAN, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CAN, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CAN, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CAN, 'updatearticle' => self::CANNOT, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::MUST, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CAN, 'updatearticle' => self::CANNOT, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CANNOT, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CAN, 'updatearticle' => self::CANNOT, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_ebay' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'beschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_real' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'beschreibung' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CAN, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_shopify' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'beschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOTSHOP, 'updatearticle' => self::CANNOTSHOP, 'importarticle' => self::CANNOTSHOP, 'importorder' => self::CANNOTSHOP],
        'hoehe' => ['createarticle' => self::CANNOTSHOP, 'updatearticle' => self::CANNOTSHOP, 'importarticle' => self::CANNOTSHOP, 'importorder' => self::CANNOTSHOP],
        'breite' => ['createarticle' => self::CANNOTSHOP, 'updatearticle' => self::CANNOTSHOP, 'importarticle' => self::CANNOTSHOP, 'importorder' => self::CANNOTSHOP],
        'verkaufspreis' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_spryker' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'beschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_shopware6' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'beschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_hhg' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'beschreibung' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_billbee' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'beschreibung' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_manomano' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'beschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_magento2' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'beschreibung' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_shopware' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'beschreibung' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CAN, 'updatearticle' => self::CANNOT, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_xtcommerce' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'beschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_presta' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'beschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_modified' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'beschreibung' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_magento' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'beschreibung' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_rakuten' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'beschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_amazon' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'beschreibung' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CAN, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_epages' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::MUST, 'updatearticle' => self::MUST, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'beschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_tillhub' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'beschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_woocommerce' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'beschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'kurztext' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'aktiv' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'umsatzsteuer' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'ean' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::MUST, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CAN, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_oxid' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'beschreibung' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CAN, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::MUST, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::MUST, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ],
      'shopimporter_getcore' => [
        'auftrag' => ['createarticle' => self::UNDEFINED, 'updatearticle' => self::UNDEFINED, 'importarticle' => self::UNDEFINED, 'importorder' => self::UNDEFINED],
        'artikelname' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'artikelnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'aktiv' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'restmenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'beschreibung' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'ean' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hersteller' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellerlink' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herstellernummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'zolltarifnummer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gewicht' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'einheit' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'laenge' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'hoehe' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'breite' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'verkaufspreis' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CAN],
        'pseudopreis' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'lagerzahlen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'pseudolager' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'staffelpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'gruppenpreise' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'fremdsprachen' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategorie' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kategoriebaum' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'bilder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'matrixeigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'über 5000 Varianten' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'eigenschaften' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'crossselling' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'kurztext' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'herkunftsland' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'umsatzsteuer' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metatitel' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metabeschreibung' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'metakeywords' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
        'freifelder' => ['createarticle' => self::CANNOT, 'updatearticle' => self::CANNOT, 'importarticle' => self::CANNOT, 'importorder' => self::CANNOT],
      ]
    ];
  }

}
