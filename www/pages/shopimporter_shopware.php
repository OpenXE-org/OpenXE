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

class Shopimporter_Shopware extends ShopimporterBase
{
  var $intern = false;
  var $shopid;
  var $data;
  var $nurpreise;
  var $url;
  var $kategorien;
  var $groups;
  var $ignoreartikelbeschreibung;
  var $useorderid;
  protected $protokoll;
  /**
   * @var Application
   */
  protected $app;
  protected $dump;


  protected $abzuholenderStatusId = 0;
  protected $abgeholtStatusId = 1;
  protected $abgeschlossenStatusId = 7;

  /** @var bool $useDigestAuth */
  protected $useDigestAuth;

  /**
   * @var Shopimporter_Shopware_Adapter
   */
  protected $adapter;
  /* Legacy Objekt für überladene Shopimporter */
  public $client;

  public function __construct($app, $intern = false)
  {
    $this->app=$app; 
    $this->intern = true;
    if($intern)
    {
      return;
    }
    $this->app->ActionHandlerInit($this);

    $this->dump=false;

    $this->app->ActionHandler("auth","ImportAuth");
    $this->app->ActionHandler("list","ImportList");
    $this->app->ActionHandler("getlist","ImportGetList");
    $this->app->ActionHandler("sendlist","ImportSendList");
    $this->app->ActionHandler("sendadresse","ImportSendAdresse");
    $this->app->ActionHandler("creategroup","ImportCreateGroup");
    $this->app->ActionHandler("sendlistlager","ImportSendListLager");
    $this->app->ActionHandler("getarticle","ImportGetArticle");
    $this->app->ActionHandler("getfilelist","ImportGetFileList");
    $this->app->ActionHandler("getfilelistarticle","ImportGetFileListArticle");
    $this->app->ActionHandler("getauftraegeanzahl","ImportGetAuftraegeAnzahl");
    $this->app->ActionHandler("getauftrag","ImportGetAuftrag");
    $this->app->ActionHandler("deletearticle","ImportDeleteArticle");
    $this->app->ActionHandler("sendfile","ImportSendFile");
    $this->app->ActionHandler("deletefile","ImportDeleteFile");
    $this->app->ActionHandler("deleteauftrag","ImportDeleteAuftrag");
    $this->app->ActionHandler("updateauftrag","ImportUpdateAuftrag");
    $this->app->ActionHandler("navigation","ImportNavigation");
    $this->app->ActionHandler("artikelgruppen","ImportArtikelgruppen");
    $this->app->ActionHandler("exportlink","ImportExportlink");
    $this->app->ActionHandler("artikelartikelgruppen","ImportArtikelArtikelGruppe");
    $this->app->ActionHandler("addfilesubjekt","ImportAddFileSubjekt");
    $this->app->ActionHandler("inhalt","ImportInhalt");
    $this->app->ActionHandler("test","ImportTest");
    $this->app->ActionHandler("partnerlist","ImportPartnerList");
    $this->app->ActionHandler("getarticlelist","ImportGetArticleList");
    $this->app->ActionHandler("updatezahlungsstatus","ImportUpdateZahlungsstatus");
    $this->app->ActionHandler("storniereauftrag","ImportStorniereAuftrag");

    $this->app->DefaultActionHandler("list");
    $this->app->ActionHandlerListen($app);
  }

  /**
   * @param null $dummy
   */
  public function DumpVar($dummy = null)
  {

  }


  public function ImportCreateGroup(){
    $tmp = $this->CatchRemoteCommand('data');
    $gruppenfilter = 'filter[0][property]=key&filter[0][expression]==&filter[0][value]='.$tmp['kennziffer'];
    $gruppenresult = $this->adapter->get('customerGroups',[],$gruppenfilter);

    $gruppendaten = array(
      'discount' => $tmp['grundrabatt'],
      'name' => $tmp['name']);
    if(count($gruppenresult['data'])>0){
      $this->adapter->put('customerGroups/'.$gruppenresult['data'][0]['id'], $gruppendaten);
    }else{
      $gruppendaten['key'] = $tmp['kennziffer'];
      $this->adapter->post('customerGroups/',  $gruppendaten);
    }
    return true;
  }

  public function ImportGetOrderStatus($external_order){
    $order = $this->getOrderById($external_order);
    $orderData = [];
    if(!is_array($order)){
      //Muss nicht zwingend heißen dass die Bestellung gelöscht wurde, vielleicht nur Verbindungsproblem?
      //return 'deleted';
    }
    $orderData['orderStatus'] = 'unpaid';
    if((int)$order['data']['orderStatusId'] === 4){
      $orderData['orderStatus'] = 'canceled';
    }
    if((int)$order['data']['paymentStatusId'] === 12){
      $orderData['orderStatus'] = 'paid';
    }
    return $orderData;
  }

  public function ImportSendAdresse(){
    $adressId = $this->CatchRemoteCommand('data');
    $meldung = array();

    $data = reset($this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adressId'"));

    if($data['email']){
      $direktquery = 'filter[0][property]=email&filter[0][expression]==&filter[0][value]='.$data['email'];
      $adresseAusShop = $this->adapter->get('customers',[],$direktquery);
      $sprache = $data['sprache'];
      $subshopid = $this->app->DB->Select("SELECT subshopkennung FROM shopexport_subshop WHERE shop='$this->shopid' and sprache='$sprache' and aktiv=1");

      $firma ='';
      if(strtolower($data['typ'])==='firma'){
        $firma = $data['name'];
        $name = explode(' ',$data['ansprechpartner']);
      }else{
        $name = explode(' ',$data['name']);
      }
      $vorname = $name[0];
      unset($name[0]);
      $nachname = implode(' ',$name);
      $anrede = 'mr';
      if(strtolower($data['typ'])==='frau'){
        $anrede = 'ms';
      }
      $telefon = $data['telefon'];
      if($data['abweichende_rechnungsadresse']){
        $rechnungname = explode(' ',$data['rechnung_name']);
        $rechnungnachname = (isset($rechnungname[1])?$rechnungname[1]:' ');
        $rechnungstrasse = $data['rechnung_name'];
        $rechnungort = $data['rechnung_ort'];
        $rechnungplz = $data['rechnung_plz'];
        $rechnungland = $data['rechnung_land'];
      }else{
        $rechnungname = $vorname;
        $rechnungnachname = $nachname;
        $rechnungstrasse = $data['strasse'];
        $rechnungort = $data['ort'];
        $rechnungplz = $data['plz'];
        $rechnungland = $data['land'];
      }
      $gruppenschluessel = '';
      $gruppen = $this->app->DB->SelectArr("SELECT g.kennziffer FROM adresse_rolle ar
        JOIN gruppen g ON ar.parameter = g.id 
        WHERE ar.adresse = '$adressId'  AND ar.subjekt = 'Mitglied' AND ar.objekt = 'Gruppe'");

      if(count($gruppen)>0){
        foreach ($gruppen as $key => $value) {
          $gruppenfilter = 'filter[0][property]=key&filter[0][expression]==&filter[0][value]='.$value['kennziffer'];
          $gruppenresult = $this->adapter->get('customerGroups',[],$gruppenfilter);
          $gruppenschluessel = $gruppenresult['data'][0]['key'];
          if($gruppenschluessel){
            continue;
          }
        }
      }
      $rechnunglandid = 2;
      $laender = $this->adapter->get('countries');
      foreach ($laender['data'] as $indexkey => $laenderdata) {
        if($laenderdata['iso'] === $rechnungland){
          $rechnunglandid = $laenderdata['id'];
          break;
        }
      }

      $adressdaten = array();
      $adressdaten['firstname'] = $vorname;
      $adressdaten['lastname'] = $nachname;
      $adressdaten['salutation'] = $anrede;
      $adressdaten['billing']['firstname'] = $rechnungname;
      $adressdaten['billing']['lastname'] = $rechnungnachname;
      $adressdaten['billing']['salutation'] = $anrede;
      $adressdaten['billing']['street'] = $rechnungstrasse;
      $adressdaten['billing']['city'] = $rechnungort;
      $adressdaten['billing']['zipcode'] = $rechnungplz;
      $adressdaten['billing']['country'] = $rechnunglandid;
      $adressdaten['billing']['phone'] = $telefon;
      if($firma){
        $adressdaten['billing']['company'] = $firma;
      }

      if($gruppenschluessel){
        $adressdaten['groupKey'] = $gruppenschluessel;
      }

      if($data['kundennummer']){
        $adressdaten['number'] = $data['kundennummer'];
      }

      if($subshopid){
        $adressdaten['languageId'] = $subshopid;
      }

      if($adresseAusShop['total'] < 1){
        //Adresse anlegen
        $adressdaten['email'] = $data['email'];
        //'encoderName' => "bcrypt",
        //$adressdaten['password'] = $data['email']; //Besser wäre: //'hashPassword' => password_hash($data['email'],PASSWORD_BCRYPT),

        $antwortVomShop = $this->adapter->post('customers', $adressdaten);
        $meldungtext = 'angelegt.';
      }else{
        $antwortVomShop = $this->adapter->post('customers/'.$adresseAusShop['data'][0]['id'], $adressdaten);
        $meldungtext = 'aktualisiert.';
      }
      if(is_array($antwortVomShop) && $antwortVomShop['success']){
        $meldung['meldung'] = 'Adresse wurde erfolgreich '.$meldungtext;
      }else{
        $meldung['fehler'] = $antwortVomShop;
      }
    }

    return $meldung;
  }

  public function ImportGetArticleList()
  {
    $limit = 1000;
    $start = 1;

    do{
      $ret = [];
      $params = array(
        'limit' => $limit,
        'start' => $start
      );
      $result = $this->adapter->get('articles',$params);
      if(count($result['data'])>0){
        foreach ($result['data'] as $article){
          $ret[] = $article['mainDetail']['number'];
        }
      }

      foreach($ret as $v)
      {
        $this->app->DB->Insert("INSERT INTO shopexport_getarticles (shop, nummer) VALUES ('$this->shopid', '".$this->app->DB->real_escape_string($v)."')");
      }
      $start +=$limit;
    }while(count($result['data']) === $limit);


    $anzahl = $this->app->DB->Select("SELECT COUNT(id) FROM shopexport_getarticles WHERE shop=$this->shopid");
    $this->app->erp->SetKonfigurationValue('artikelimportanzahl_'.$this->shopid, $anzahl);

  }
  
  public function ImportStorniereAuftrag()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $einstellungen = $this->app->DB->Select("SELECT einstellungen_json FROM shopexport WHERE id = '$this->shopid' LIMIT 1");
    if($einstellungen)$einstellungen = json_decode($einstellungen,true);
    $stornostatusid = $einstellungen['felder']['StorniertStatusID'];

    $auftrag = $tmp['auftrag'];
    $this->adapter->put('orders/'.$auftrag, array('orderStatusId' => $stornostatusid));
  }


  function getKonfig($shopid, $data)
  {
    $this->shopid = $shopid;
    $this->data = $data;
    $einstellungen = $this->app->DB->Select("SELECT einstellungen_json FROM shopexport WHERE id = '$shopid' LIMIT 1");
    if($einstellungen)$einstellungen = json_decode($einstellungen,true);

    $this->protokoll = $einstellungen['felder']['protokoll'];
    $ImportShopwareApiUser = $einstellungen['felder']['ImportShopwareApiUser'];
    $ImportShopwareKey = $einstellungen['felder']['ImportShopwareKey'];
    $ImportShopwareApiUrl = $einstellungen['felder']['ImportShopwareApiUrl'];
    $this->useorderid = $einstellungen['felder']['useorderid'];
    $this->url = $ImportShopwareApiUrl;
    $this->nurpreise = $this->app->DB->Select("SELECT nurpreise FROM shopexport WHERE id = '$shopid' LIMIT 1");
    $this->RootCategoryName = $einstellungen['felder']['RootCategoryName'];
    $useDigestAuth = !empty($einstellungen['felder']['useDigestAuth']);

    include_once 'Shopimporter_Shopware_Adapter.php';
    if($this->adapter === null){
      $this->adapter = new Shopimporter_Shopware_Adapter($ImportShopwareApiUrl, $ImportShopwareApiUser, $ImportShopwareKey, $useDigestAuth);

      // Legacy Objekt für überladene Shopimporter
      $this->client = $this->adapter;
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


  public function EinstellungenStruktur()
  {
    return 
    array(
    'ausblenden'=>array('abholmodus'=>array('zeitbereich')),
    'erlauben'=>array('nurpreise'),
    'archiv'=>array('ab_nummer'),
    'functions'=>array('getarticlelist','updatezahlungsstatus','exportartikelbaum'),
    'felder'=>array(
        'protokoll'=>array('typ'=>'checkbox','bezeichnung'=>'Protokollierung im Logfile:'),
        'ImportShopwareApiUser'=>array('typ'=>'text','bezeichnung'=>'{|API User:','size'=>40),
        'ImportShopwareKey'=>array('typ'=>'text','bezeichnung'=>'{|API Key|}:','size'=>40),
        'ImportShopwareApiUrl'=>array('typ'=>'text','bezeichnung'=>'{|API URL|}:','size'=>40),
        'StorniertStatusID'=>array('typ'=>'text','bezeichnung'=>'{|Storniert Status ID|}:','size'=>5, 'default'=>'4'),
        'priceGroupIdStandard'=>array('typ'=>'text','bezeichnung'=>'{|ID Standard Preisgruppe|}:','size'=>5, 'default'=>'1'),
        'limitedtosubshop'=>array('typ'=>'text','bezeichnung'=>'{|Begrenzen auf Subshop|}:','size'=>5, 'default'=>''),
        'benutzergruppenuebernehmen'=>array('typ'=>'checkbox','bezeichnung'=>'{|Benutzergruppen aus Shop übernehmen|}:'),
        'benutzergruppenanlegen'=>array('typ'=>'checkbox','bezeichnung'=>'{|Neue Benutzergruppen anlegen|}:'),
        'gutscheinalslagerartikel'=>array('typ'=>'checkbox','bezeichnung'=>'{|Gutscheine als Lagerartikel anlegen|}:'),
        'RootCategoryName'=>array('typ'=>'text','bezeichnung'=>'{|Wurzelkategoriename|}:', 'default' => 'Deutsch'),
        'useorderid'=>array('typ'=>'checkbox','bezeichnung'=>'{|Order ID statt Bestellnummer verwenden|}:'),
        'getunpaidorders'=>array('typ'=>'checkbox','bezeichnung'=>'{|Unbezahlte Bestellungen abholen|}:'),
        'useDigestAuth'=>array('typ'=>'checkbox','bezeichnung'=>'{|Authentifizierung über Digest|}:')
        ));
  }



  public function ImportSendArtikelbaum(){
    $kategorienbaum = [];
    $this->app->erp->GetKategorienbaum($kategorienbaum, 0, 0, $this->shopid);
    //Wenn Artikelbaum übertragen wird, Arays vorbereiten
    $kategorieparentid = array();
    foreach ($kategorienbaum as $key => $value) {
      $kategorienbaum[$key]['erledigt'] = false;
      $kategorienbaum[$key]['shopid'] = '';
      $kategorienbaum[$key]['aktiv'] = false;
      $kategorieparentid[$value['id']] = $key;
    }

    $rootcategoryid = 1;
    if(!empty($this->RootCategoryName)){
      $params = ['filter' => [[
        'property' => 'name',
        'value' => $this->RootCategoryName
      ]]];
      $result = $this->adapter->get('categories', $params);
      if($result['total'] > 0){
        $rootcategoryid = $result['data'][0]['id'];
      }
    }elseif(strtolower($kategorienbaum[0]['bezeichnung']) === 'root'){
        $rootcategoryid = null;
    }

    foreach ($kategorienbaum as $key => $value) {
      $this->kategoriebaumanlegen($key, $kategorienbaum, $kategorieparentid,$rootcategoryid);
    }
  }

  private function kategoriebaumanlegen($id, &$kompletterbaum, $hilfsbaum, $rootcategoryid){
    $idparent = $rootcategoryid;
    $kategorieid = 0;
    if($kompletterbaum[$id]['parent']){
      $idparent = $kompletterbaum[$hilfsbaum[$kompletterbaum[$id]['parent']]]['shopid'];
    }
    if($kompletterbaum[$id]['parent'] && !$kompletterbaum[$hilfsbaum[$kompletterbaum[$id]['parent']]]['erledigt']){
      $this->kategoriebaumanlegen($hilfsbaum[$kompletterbaum[$id]['parent']],$kompletterbaum,$hilfsbaum,$rootcategoryid);
    }
    $kompletterbaum[$id]['erledigt'] = true;


    $kategoriebezeichnung = $kompletterbaum[$id]['bezeichnung'];
    $params = [
      'filter' => [
        ['property' => 'name','value' => $kategoriebezeichnung],
        ['property' => 'parentId','value' => $idparent]
      ]
    ];
    $result = $this->adapter->get('categories',  $params);
    $gefunden = $result['total'];
    if($gefunden)
    {
      $kategorieid = $result['data'][0]['id'];
    }

    if(!$kategorieid){
      $kategoriedaten =  array('parentId' => $idparent,
        'name' => $kategoriebezeichnung,
        'metaKeywords' => $kategoriebezeichnung,
        'metaDescription' => $kategoriebezeichnung,
        'cmsheadline' => $kategoriebezeichnung
      );
      $result = $this->adapter->post('categories', $kategoriedaten);
      if($result['data']['id'])
      {
        $kategorieid = $result['data']['id'];
      }
    }

    if($kategorieid){
      $kompletterbaum[$id]['shopid'] = $kategorieid;
    }

  }




  public function ImportList()
  {
    $msg = $this->app->erp->base64_url_encode('<div class="info">Sie k&ouml;nnen hier die Shops einstellen</div>');
    header('Location: index.php?module=onlineshops&action=list&msg='.$msg);
    exit;
  }

  /**
   * @param $nummer
   * @param $fremdnummer
   *
   * @return array|null
   * @throws Exception
   */

  protected function getIdByNumber($artikelId, $nummer, $matrixartikel= false, $variantennummer = ''){
    $params = array('useNumberAsId' => true);
    $response = [
      'id' => '',
      'typ' => 'normal'
    ];


    if($matrixartikel){
      $id = $this->app->erp->GetShopArtikel($this->shopid,$artikelId,'ShopwareArtikelId');
      if(!empty($id)){
        $result = $this->adapter->put('articles/'.$id,  []);
        $id = '';
        if(is_array($result)){
          $id = $result['data']['id'];
        }
      }
      if(!empty($id)){
        $response['id'] = $id;
        return $response;
      }
      $id = '';
      $result = $this->adapter->get('articles/'.$nummer, $params);
      if(is_array($result)){
        $id = $result['data']['id'];
      }
      if(empty($id)){
        $result = $this->adapter->get('variants/'.$variantennummer, $params);
        if(is_array($result['data'])){
          $id = $result['data']['articleId'];
        }
      }
      if(!empty($id)){
        $response['id'] = $id;
        $this->app->erp->SaveShopArtikel($this->shopid, $artikelId, ['ShopwareArtikelId' => $response['id']], false);
        return $response;
      }
    }

    $result = $this->adapter->get('variants/'.$nummer, $params);

    if(!is_array($result)){
      return $response;
    }
    $response['id'] = $result['data']['articleId'];
    if($matrixartikel && !empty($response['id'])){
      $this->app->erp->SaveShopArtikel($this->shopid, $artikelId, ['ShopwareArtikelId' => $response['id']], false);
    }
    if(count($result['data']['configuratorOptions']) > 1){
      //TODO
    }
    if($result['data']['id'] !== $result['data']['article']['mainDetailId']){
      $response['id'] = $result['data']['id'];
      $response['typ'] = 'variant';
    }

    return $response;
  }

  protected function getVariantProductInfoByNumber($nummer, $fremdnummer,$forcevariant = false){
    $params = array('useNumberAsId' => true);
    $result = null;
    $nummer = (string)$nummer;
    $fremdnummer = (string)$fremdnummer;

    if($fremdnummer !== '' && $fremdnummer !== $nummer){
      $result = $this->adapter->get('articles/'.$fremdnummer,$params);
    }

    if(!is_array($result) && $nummer !== ''){
      $result = $this->adapter->get('articles/'.$nummer, $params);
    }

    if(!is_array($result)){
      return null;
    }

    if($result['data']['mainDetail']['number'] === $nummer || $result['data']['mainDetail']['number'] === $fremdnummer || $result['data']['mainDetail']['id'] === $nummer || $result['data']['mainDetail']['id'] === $fremdnummer){
      if($forcevariant && !empty($result['data']['mainDetail']['id'])){
        return array('typ'=>'variant', 'id' => $result['data']['mainDetail']['id']);
      }
      return array('typ'=>'normal', 'id' => $result['data']['id']);
    }
    foreach ($result['data']['details'] as $detail){
      if($detail['number'] === $nummer || $detail['number'] === $fremdnummer || $detail['id'] === $nummer || $detail['id'] === $fremdnummer){
        return array('typ'=>'variant', 'id' => $detail['id']);
      }
    }

    return array('typ'=>'normal','id'=>$result['data']['id']);
  }

  protected function GetArticleByNumber($nummer, $fremdnummer = '')
  {
    $params = array('useNumberAsId' => true);
    $result = null;

    if($fremdnummer != '' && $fremdnummer != $nummer){
      $result = $this->adapter->get('articles/'.$fremdnummer, $params);
    }

    if(!is_array($result) && $nummer != ''){
      $result = $this->adapter->get('articles/'.$nummer, $params);
    }

    if(!is_array($result)){
      return null;
    }else{
      return $result['data'];
    }
  }

  protected function GetIdbyNummer($nummer, $fremdnummer = '')
  {
    $result = $this->GetArticleByNumber($nummer, $fremdnummer);
    if(is_null($result))
    {
      return $result;
    }
    return $result['id'];
  }


  public function ImportPartnerList()
  {
    $tmp = $this->CatchRemoteCommand('data');
    if(count($tmp) > 0)
    {
      foreach($tmp as $key=>$value)
      {

        $checkid = $this->app->DB->Select("SELECT id FROM s_emarketing_partner WHERE idcode='".$value['ref']."' AND idcode!='' LIMIT 1");
        if($checkid<=0)
        {
          $this->app->DB->Insert("INSERT INTO s_emarketing_partner (id,idcode,datum,active,userID) VALUES ('','".$value['ref']."',NOW(),1,0)");
          $checkid = $this->app->DB->GetInsertID();
        }

        foreach($value as $column=>$cvalue)
        { 
          switch($column)
          {
            case "name": $this->app->DB->Update("UPDATE s_emarketing_partner SET company='$cvalue' WHERE id='$checkid' LIMIT 1");break;
            case "netto": $this->app->DB->Update("UPDATE s_emarketing_partner SET percent='$cvalue' WHERE id='$checkid' LIMIT 1");break;
            case "strasse": $this->app->DB->Update("UPDATE s_emarketing_partner SET street='$cvalue' WHERE id='$checkid' LIMIT 1");break;
            case "email": $this->app->DB->Update("UPDATE s_emarketing_partner SET email='$cvalue' WHERE id='$checkid' LIMIT 1");break;
            case "telefax": $this->app->DB->Update("UPDATE s_emarketing_partner SET fax='$cvalue' WHERE id='$checkid' LIMIT 1");break;
            case "telefon": $this->app->DB->Update("UPDATE s_emarketing_partner SET phone='$cvalue' WHERE id='$checkid' LIMIT 1");break;
            case "ort": $this->app->DB->Update("UPDATE s_emarketing_partner SET city='$cvalue' WHERE id='$checkid' LIMIT 1");break;
            case "plz": $this->app->DB->Update("UPDATE s_emarketing_partner SET zipcode='$cvalue' WHERE id='$checkid' LIMIT 1");break;
            case "land": $this->app->DB->Update("UPDATE s_emarketing_partner SET country='$cvalue' WHERE id='$checkid' LIMIT 1");break;
          }
        }
      }
    }
    return 'ok';
  }


  // get checksum list from the files 
  public function ImportGetArticle()
  {
    $tmp = $this->CatchRemoteCommand('data');
    if(isset($tmp['nummerintern']))
    {
      $nummer = $tmp['nummerintern'];
    }else{
      $nummer = $tmp['nummer'];
    }
    $params = array(
      'useNumberAsId' => true
    );

    $result = $this->adapter->get('articles/'.$nummer,$params);

    if(!isset($result['data']['name']))
    {
      $this->error[]='Artikel in der Shop Datenbank nicht gefunden!';
      return;
    } else {
      $data['result'] = json_decode(json_encode($result['data']),true);
      $data['name']=$result['data']['name'];
      if(isset($tmp['nummerintern']))
      {
        $data['nummer'] = $result['data']['mainDetail']['number'];
      }
      $staffelpreise = array();
      foreach ($result['data']['mainDetail']['prices'] as $staffelpreis){
        if($staffelpreis['customerGroupKey'] === 'EK'){
          $staffelpreise[] = array('ab_menge'=>$staffelpreis['from'], 'preis'=>$staffelpreis['price'], 'brutto'=>$staffelpreis['customerGroup']['taxInput']);
        }
      }
      if(count($staffelpreise) >1){
        $data['staffelpreise'] = $staffelpreise;
      }
      $data['artikelnummerausshop'] = $result['data']['mainDetail']['number'];
      $data['kurztext_de']=$result['data']['description'];
      $data['uebersicht_de']=str_replace('<br />','</p><p>',$result['data']['descriptionLong']);

      if(isset($result['data']['price']) && $result['data']['price']!=''){
        $data['preis_netto'] = $result['data']['price'];
      }
      else{
        $data['preis_netto'] = $result['data']['mainDetail']['prices'][0]['price'];
      }

      $data['aktiv']=$result['data']['active'];
      $data['restmenge']=$result['data']['lastStock'];

      if($result['data']['mainDetail']['prices'][0]['customerGroup']['taxInput'] === true){
        $data['pseudopreis'] = $result['data']['mainDetail']['prices'][0]['pseudoPrice']*(1+(float)$result['data']['tax']['tax']/100);
      }else{
        $data['pseudopreis'] = $result['data']['mainDetail']['prices'][0]['pseudoPrice'];
      }

      //$data['pseudolager']=$result['data']['mainDetail']['inStock'];
      $data['lieferzeitmanuell']=$result['data']['mainDetail']['shippingTime'];
      

      if(isset($result['data']['mainDetail']['weight']) && $result['data']['mainDetail']['weight'] > 0)
      {
        $data['gewicht'] = $result['data']['mainDetail']['weight'];
      }
      if(isset($result['data']['mainDetail']['supplierNumber']) && (String)$result['data']['mainDetail']['supplierNumber'] <> '')
      {
        $data['herstellernummer'] = (String)$result['data']['mainDetail']['supplierNumber'];
      }
      if(isset($result['data']['mainDetail']['ean']) && (String)$result['data']['mainDetail']['ean'] <> '')
      {
        $data['ean'] = (String)$result['data']['mainDetail']['ean'];
      }

      if(isset($result['data']['supplier']) && isset($result['data']['supplier']['name']) && (String)$result['data']['supplier']['name'] != '')
      {
        $data['hersteller'] = (String)$result['data']['supplier']['name'];
      }

      if(isset($result['data']['propertyValues']) && $result['data']['propertyValues'] && isset($result['data']['propertyGroup']))
      {
        $groups = $this->GetGroups();
        if($groups)
        {
          foreach($groups as $v)
          {
            if($v['id'] == $result['data']['propertyGroup']['id'])
            {
              if(!empty($v['options']))
              {
                foreach($v['options'] as $v2)
                {
                  $optionen[$v2['id']] = $v2['name'];
                }
                foreach($result['data']['propertyValues'] as $v3)
                {
                  if(isset($optionen[$v3['optionId']]))
                  {
                    $data['eigenschaften'][] = array('name'=> $optionen[$v3['optionId']], 'values'=>$v3['value']);
                  }
                }
              }
            }
          }
        }
      }

      if(isset($result['data']['categories']))
      {
        foreach($result['data']['categories'] as $v)
        {
          $data['kategorien'][] = array('id'=>$v['id'],'name'=>$v['name']);
        }
      }

      $data['kategorien_all'] = $this->GetKategorien();

      if(!empty($result['data']['images']))
      {
        foreach($result['data']['images'] as $k => $v)
        {
          $media = $this->adapter->get('media/'.$v['mediaId']);
          if(!empty($media['data']) && isset($media['data']['path']))
          {
            $data['bilder'][] = $media['data'];
            $data['bilder'][count($data['bilder'])-1]['content']=base64_encode(@file_get_contents($media['data']['path']));
            $data['bilder'][count($data['bilder'])-1]['id'] = $v['mediaId'];
          }
        }
      }

      if(!empty($result['data']['mainDetail']['attribute']))
      {
        foreach($result['data']['mainDetail']['attribute'] as $k => $v)
        {
          if(strpos($k, 'attr') === 0)
          {
            $data['freifeld_'.$k] = $v;
          }
        }
      }

      if(isset($result['data']['similar'])){
        foreach ($result['data']['similar'] as $key => $value) {
          $id = $value['id'];
          $crosssellingartikel = $this->adapter->get('articles/'.$id);
          if(!isset($crosssellingartikel['data']['name']))
          {
            continue;
          }
          $nummertmp = $crosssellingartikel['data']['mainDetail']['number'];
          $data['crossselling']['similar'][] = $nummertmp;
        }
      }
      if(isset($result['data']['similar'])){
        foreach ($result['data']['related'] as $key => $value) {
          $id = $value['id'];
          $crosssellingartikel  = $this->adapter->get('articles/'.$id);
          if(!isset($crosssellingartikel['data']['name']))
          {
            continue;
          }
          $nummertmp = $crosssellingartikel['data']['mainDetail']['number'];
          $data['crossselling']['related'][] = $nummertmp;
        }
      }

      if(!empty($result['data']['tax']) && !empty($result['data']['tax']['tax']))
      {
        $data['umsatzsteuer'] = $result['data']['tax']['tax'];
      }

      if(isset($tmp['nummerintern']) && !empty($result['data']['details']) && is_array($result['data']['details']) && count($result['data']['details']) > 0)
      {
        $arrayOfArticles = array($data);
        unset($data['artikelnummerausshop']);
        if(isset($data['bilder']))
        {
          unset($data['bilder']);
        }
        if(isset($data['result']))
        {
          unset($data['result']);
        }
        unset($data['kurztext_de']);
        unset($data['uebersicht_de']);
        if(isset($data['ean']))unset($data['ean']);
        if(isset($data['herstellernummer']))unset($data['herstellernummer']);
        foreach($data as $k => $v)
        {
          if(strpos($k, 'freifeld_') === 0)unset($data[$k]);
        }
        foreach($result['data']['details'] as $vdetail)
        {
          if($vdetail['number'] == $result['data']['mainDetail']['number'])
          {
          }else{
            if(!empty($result['data']['mainDetail']['configuratorOptions']))
            {
              $namea = null;
              foreach($result['data']['mainDetail']['configuratorOptions'] as $k => $v)
              {
                if(isset($v['name']))
                {
                  $namea[$v['id']] = (String)$v['name'];
                }
              }
              sort($namea);
              $data['name'] .= ' '.implode(' ', $namea);
            }

            $arrayOfArticles[] = $data;
            unset($arrayOfArticles[count($arrayOfArticles)-1]['pseudopreis']);
            $arrayOfArticles[count($arrayOfArticles)-1]['nummer'] = $vdetail['number'];
            if(!empty($vdetail['number']))
            {
              $arrayOfArticles[count($arrayOfArticles)-1]['artikelnummerausshop'] = $vdetail['number'];
            }
            $arrayOfArticles[count($arrayOfArticles)-1]['name'] = $arrayOfArticles[0]['name'];
            if(isset($vdetail['price']) && $vdetail['price']!=''){
              $arrayOfArticles[count($arrayOfArticles) - 1]['preis_netto'] = $vdetail['data']['price'];
            }
            elseif(!empty($vdetail['prices']) && !empty($vdetail['prices'][0]) && $vdetail['prices'][0]['price'] != 0){
              $arrayOfArticles[count($arrayOfArticles) - 1]['preis_netto'] = $vdetail['prices'][0]['price'];
            }
            if(isset( $vdetail['prices'][0]['pseudoPrice']) &&  $vdetail['prices'][0]['pseudoPrice']!='' &&
              round($vdetail['prices'][0]['price'],5)!==round($vdetail['prices'][0]['pseudoPrice'],5)){
              $arrayOfArticles[count($arrayOfArticles)-1]['pseudopreis'] = $vdetail['prices'][0]['pseudoPrice'] * ((100 + $result['data']['tax']['tax']) / 100);
            }
            if(isset($vdetail['shippingTime']) && $vdetail['shippingTime'] != '')$arrayOfArticles[count($arrayOfArticles)-1]['lieferzeitmanuell'] = $vdetail['shippingTime'];
            if(isset($vdetail['ean']) && $vdetail['ean'] != '')$arrayOfArticles[count($arrayOfArticles)-1]['ean'] = $vdetail['ean'];
            if(isset($vdetail['supplierNumber']) && $vdetail['supplierNumber'] != '')$arrayOfArticles[count($arrayOfArticles)-1]['herstellernummer'] = $vdetail['supplierNumber'];
            if(isset($vdetail['weight']) && $vdetail['shippingTime'] > 0)$arrayOfArticles[count($arrayOfArticles)-1]['gewicht'] = $vdetail['weight'];
            if(isset($vdetail['active']))$arrayOfArticles[count($arrayOfArticles)-1]['aktiv'] = $vdetail['active'];
            if(!empty($vdetail['configuratorOptions']))
            {
              $namea = null;
              foreach($vdetail['configuratorOptions'] as $k => $v)
              {
                if(isset($v['name']))
                {
                  $namea[$v['id']] = (String)$v['name'];
                }
              }
              sort($namea);
              $arrayOfArticles[count($arrayOfArticles)-1]['name'] .= ' '.implode(' ', $namea);
            }
            if(!empty($vdetail['attribute']))
            {
              foreach($vdetail['attribute'] as $k => $v)
              {
                if(strpos($k, 'attr') === 0)
                {
                  $arrayOfArticles[count($arrayOfArticles)-1]['freifeld_'.$k] = $v;
                }
              }
            }
          }
        }
        if(!empty($result['data']['mainDetail']['configuratorOptions']))
        {
          $namea = null;
          foreach($result['data']['mainDetail']['configuratorOptions'] as $k => $v)
          {
            if(isset($v['name']))
            {
              $namea[$v['id']] = (String)$v['name'];
            }
          }
          sort($namea);

          $arrayOfArticles[0]['name'] .= ' '.implode(' ', $namea);
        }
        return $arrayOfArticles;
      }
    }

    return $data;
  }

  protected function GetGroup($name)
  {
    $groups = $this->adapter->get('propertyGroups/');
    foreach ($groups['data'] as $key => $value) {
      if($value['name'] == $name){
        $group = $value;
      }
    }
    return $group;
  }

  protected function GetGroups()
  {
    if($this->groups)
    {
      return $this->groups;
    }
    $groups = $this->adapter->get('propertyGroups/');
    if(isset($groups['data']))
    {
      $this->groups = $groups['data'];
    }
    return $this->groups;
  }
  
  protected function GetKategorien()
  {
    if($this->kategorien)
    {
      return $this->kategorien;
    }
    $data = $this->adapter->get('categories/');

    $rootId = 0;
    $rootParentId = 0;

    if(!empty($data['data'][0]['id']) && $data['data'][0]['name'] === 'Root'){
      $rootId = $data['data'][0]['id'];
      $rootParentId = $data['data'][0]['parentId'];
    }

    if(isset($data['data']))
    {
      $_data = null;
      foreach($data['data'] as $v)
      {
        if($v['id'] === $rootId){
          continue;
        }
        if($v['parentId'] === $rootId){
          $v['parentId'] = $rootParentId;
        }
        $_data[] = array('id'=>$v['id'],'name'=>$v['name'],'aktiv'=>$v['active'],'parent'=>(int)$v['parentId'],'pos'=>$v['position']);
      }
      $this->kategorien = $_data;
    }
    return $this->kategorien;
  }

  public function ImportTest()
  {
    return 'TEST';
  }

  // receive all new articles
  public function ImportSendListLager()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $anzahl = 0;
    $ctmp = count($tmp);
    for($i=0;$i<$ctmp;$i++)
    {
      $artikel = $tmp[$i]['artikel'];
      $nummer = $tmp[$i]['nummer'];
      $fremdnummer = isset($tmp[$i]['fremdnummer'])?$tmp[$i]['fremdnummer']:'';
      $lageranzahl = $tmp[$i]['anzahl_lager'];
      $laststock = $tmp[$i]['restmenge'];
      $inaktiv = $tmp[$i]['inaktiv'];
      $shippingtime = $tmp[$i]['lieferzeitmanuell'];
      $pseudolager = trim($tmp[$i]['pseudolager']);

      $aktiv=1;
      if($inaktiv){
        $aktiv=0;
      }

      if($pseudolager > 0){
        $lageranzahl=$pseudolager;
      }

      if($tmp[$i]['ausverkauft']){
        $lageranzahl=0;
        $laststock=1;
      } 

      if($laststock!="1"){
        $laststock=0;
      }
      $variantennummer = '';
      if(isset($tmp[$i]['matrix_varianten'])){
        foreach ($tmp[$i]['matrix_varianten']['artikel'] as $artikelId => $variantenInfos){
          if($variantenInfos[0]['gesperrt']){
            continue;
          }
          foreach ($variantenInfos as $daten){
            if(!empty($daten['nummer'])){
              $variantennummer = $daten['nummer'];
              break 2;
            }
          }
        }
      }

      $artikelinfo = $this->getIdByNumber($tmp[$i]['artikelid'],$fremdnummer?:$nummer,!empty($tmp[$i]['matrix_varianten']),$variantennummer);
      $id = $artikelinfo['id'];
      $artikeltyp = $artikelinfo['typ'];
      if($artikel!=='ignore' && !empty($id))
      {
        if($artikeltyp === 'normal'){
          $updateInStock = array(
            'active'   => $aktiv,
            'lastStock' => $laststock,
            'attributeVisionPhysicalStockForSale' => $lageranzahl,
            'mainDetail' => array(
              'lastStock' => $laststock,
              'shippingtime' => $shippingtime,
              'active'   => $aktiv,
              'inStock' => $lageranzahl,
              'attributeVisionPhysicalStockForSale' => $lageranzahl,
            )
          );

          $result = $this->adapter->put('articles/'.$id,  $updateInStock);
          $this->ShopwareLog("Shopware Lagerzahlenübertragung für Artikel: $nummer / $fremdnummer - Anzahl: $lageranzahl", print_r($result,true));

          $anzahl++;
        }else{
          $updateVariantInStock = array(
            'inStock' => $lageranzahl,
            'attributeVisionPhysicalStockForSale' => $lageranzahl,
            'active'   => $aktiv,
          );
          $result = $this->adapter->put('variants/'.$id,  $updateVariantInStock);
          $this->ShopwareLog("Shopware Lagerzahlenübertragung für Variante: $nummer / $fremdnummer - Anzahl: $lageranzahl", print_r($result,true));

          $anzahl++;
        }
      }

    }
    return $anzahl;
  }


    function getCategoryParentId($categoryData,&$kategoriebaumid){
      $parentid = $kategoriebaumid[$categoryData['parent']]['shopwareid'];
      if(!empty($parentid)){
        return $parentid;
      }

      $parentCategoryData = $this->app->DB->SelectRow("SELECT id,parent,bezeichnung AS name FROM artikelkategorien WHERE id<>'' AND id<>'0' AND id='".$categoryData['parent']."' LIMIT 1");
      if(empty($parentCategoryData)){
        return null;
      }

      $params = ['filter' => [['property' => 'name','value' => $parentCategoryData['name']]]];
      $result = $this->adapter->get('categories', $params);
      if(count($result['data']) < 1){
        return null;
      }

      if(count($result['data']) === 1){
        $parentCategoryData['shopwareid'] = $result['data'][0]['id'];
        $kategoriebaumid[$parentCategoryData['id']] = $parentCategoryData;
        return $result['data'][0]['id'];
      }

      $grandparentid = $this->getCategoryParentId($parentCategoryData,$kategoriebaumid);
      $params = ['filter' => [['property' => 'name','value' => $parentCategoryData['name']],['property' => 'parentId', 'value' => $grandparentid]]];
      $result = $this->adapter->get('categories', $params);
      if(count($result['data']) === 1){
        $parentCategoryData['shopwareid'] = $result['data'][0]['id'];
        $kategoriebaumid[$parentCategoryData['id']] = $parentCategoryData;
        return $result['data'][0]['id'];
      }

      return null;
    }

  // receive all new articles
  public function ImportSendList()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $anzahl = 0;
    $bilderarray = array();
    $ctmp = count($tmp);
    $shopeinstellungen = $this->app->DB->SelectArr("SELECT * FROM shopexport WHERE id = '".$this->shopid."' AND aktiv = 1 LIMIT 1");
    if(!empty($shopeinstellungen))
    {
      $shopeinstellungen = reset($shopeinstellungen);
      $einstellungen = json_decode($shopeinstellungen['einstellungen_json'],true);
      $eigenschaftenuebertragen = $shopeinstellungen['eigenschaftenuebertragen'];
      $kategorienuebertragen = $shopeinstellungen['kategorienuebertragen'];
      $shopbilderuebertragen = $shopeinstellungen['shopbilderuebertragen'];
      $variantenuebertragen = $shopeinstellungen['variantenuebertragen'];
      $crosssellingartikeluebertragen = $shopeinstellungen['crosssellingartikeluebertragen'];
      $preisgruppe = $shopeinstellungen['preisgruppe'];
      $this->GetGroups();
    }else{
      $eigenschaftenuebertragen = 0;
      $kategorienuebertragen = 0;
      $shopbilderuebertragen = 0;
      $variantenuebertragen = 0;
      $crosssellingartikeluebertragen = 0;
      $preisgruppe = 0;
    }
    for($i=0;$i<$ctmp;$i++)
    {
      $filtergruppe = 0;
      $rootcategory = 1;
      if(!empty($this->RootCategoryName)) {
        $params = ['filter' => [[
              'property' => 'name',
              'value' => $this->RootCategoryName
            ]]];
        $result = $this->adapter->get('categories', $params);
        if($result['total'])
        {
          $rootcategory = $result['data'][0]['id'];
        }
      }

      //Schritt 1: Kategorien abarbeiten
      $kategoriename = $tmp[$i]['kategoriename'];
      $kategoriebaum = $tmp[$i]['kategorien'];

      $kategorientmp = array();
      $kategorien = array();

      if(!empty($kategoriebaum)){
        $kategoriebaumid = array();
        foreach ($kategoriebaum as $key => $value){
          $value['shopwareparent'] = 0;
          if(!$value['parent']){
            $value['shopwareid'] = $rootcategory;
          }
          $kategoriebaumid[$value['id']] = $value;
        }

        foreach ($kategoriebaum as $key => $value) {
          $parentid = $rootcategory;
          if(!empty($value['parent'])){
            $parentid = $this->getCategoryParentId($value,$kategoriebaumid);
          }

          $params = [
            'filter' => [
              ['property' => 'name','value' => $value['name']]
            ]
          ];
          if(!empty($parentid)){
            $params['filter'][] = ['property' => 'parentId', 'value' => $parentid];
          }

          $result = $this->adapter->get('categories',  $params);
          $gefunden = $result['total'];
          if($gefunden)
          {
            $kategoriebaumid[$value['id']]['shopwareid'] = $result['data'][0]['id'];
            $kategorientmp[] = $result['data'][0]['id'];
          }

        }

      }else if($kategoriename){
        //einfache Artikelkategorie benutzen
        $params = ['filter' => [[
          'property' => 'name',
          'value' => $kategoriename
        ]]];
        $result = $this->adapter->get('categories',  $params);
        $gefunden = $result['total'];
        if($gefunden)
        {
          //TODO Falls mehrere Kategorien mit dem selben Namen existieren sollten hier aufbohren:
          $kategorientmp[] = $result['data'][0]['id'];
        } elseif($kategorienuebertragen) {
          //Erstelle einfache Kategorie immer in der obersten Ebene

          $kategoriedaten =  array('parentId' => $rootcategory,
            'name' => $kategoriename,
            'metaKeywords' => $kategoriename,
            'metaDescription' => $kategoriename,
            'cmsheadline' => $kategoriename
          );
          $result = $this->adapter->post('categories', $kategoriedaten);
          if(!empty($result['data']['id']))
          {
            $kategorientmp[] = $result['data']['id'];
          }
        }
      }

      if(!empty($kategorientmp)){
        foreach ($kategorientmp as $key => $value) {
          $kategorien[] = array('id' => $value);
        }
      }

      //Schritt 2: Artikeleigenschaften
      $artikel = $tmp[$i]['artikel'];
      $nummer = isset($tmp[$i]['fremdnummer'])?$tmp[$i]['fremdnummer']:$tmp[$i]['nummer'];
      $inaktiv = $tmp[$i]['inaktiv'];
      $name_de = $tmp[$i]['name_de'];
      $name_en = $tmp[$i]['name_en'];
      $hersteller = $tmp[$i]['hersteller'];
      $herstellernummer = $tmp[$i]['herstellernummer'];
      $herstellerlink = $tmp[$i]['herstellerlink'];
      $description = $tmp[$i]['metadescription_de'];
      $description_en = $tmp[$i]['metadescription_en'];
      $keywords = $tmp[$i]['metakeywords_de'];
      $keywords_en = $tmp[$i]['metakeywords_en'];
      $metatitle = $tmp[$i]['metatitle_de'];
      $metatitle_en = $tmp[$i]['metatitle_en'];

      $laenge = $tmp[$i]['laenge'];
      $breite = $tmp[$i]['breite'];
      $hoehe = $tmp[$i]['hoehe'];
      $gewicht = $tmp[$i]['gewicht'];

      $lageranzahl = $tmp[$i]['anzahl_lager'];
      $laststock = $tmp[$i]['restmenge'];

      $preis = $tmp[$i]['bruttopreis'];
      if(!$preis){
        return 'error: Im Artikel ist kein Preis hinterlegt.';
      }

      $nettopreis =$tmp[$i]['preis'];
      $einkaufspreis = $tmp[$i]['einkaufspreis'];
      $pseudopreisBrutto = $tmp[$i]['pseudopreis'];//*1.19;
      $steuersatz = $tmp[$i]['steuersatz'];
      $ean = $tmp[$i]['ean'];
      $shippingtime = $tmp[$i]['lieferzeitmanuell'];
      $mindestabnahme = 999999;

      $description_long = htmlspecialchars_decode($tmp[$i]['uebersicht_de']);
      $description_long_en = htmlspecialchars_decode($tmp[$i]['uebersicht_en']);

      if(isset($tmp[$i]['texte'])){
        $ctexte = count($tmp[$i]['texte']);
        for ($t=0; $t < $ctexte; $t++){
          if($tmp[$i]['texte'][$t]['sprache'] === 'DE'){
            if(!empty($tmp[$i]['texte'][$t]['name'])){
              $name_de = $tmp[$i]['texte'][$t]['name'];
            }
            if(!empty($tmp[$i]['texte'][$t]['meta_description'])){
              $description = $tmp[$i]['texte'][$t]['meta_description'];
            }
            if(!empty($tmp[$i]['texte'][$t]['beschreibung_online'])){
              $description_long = $tmp[$i]['texte'][$t]['beschreibung_online'];
            }
            if(!empty($tmp[$i]['texte'][$t]['meta_keywords'])){
              $keywords = $tmp[$i]['texte'][$t]['meta_keywords'];
            }
            if(!empty($tmp[$i]['texte'][$t]['meta_title'])){
              $metatitle = $tmp[$i]['texte'][$t]['meta_title'];
            }
            continue;
          }
        }
      }

      $crosssellingaehnlich = array();
      $crosssellingzubehoer = array();
      $gegenseitigzuweisen = array();
      if($crosssellingartikeluebertragen && isset($tmp[$i]['crosssellingartikel']) && is_array($tmp[$i]['crosssellingartikel'])){
        foreach ($tmp[$i]['crosssellingartikel'] as $crosssellingartikel){
          $csnummer = $crosssellingartikel['nummer'];
          if(!empty($crosssellingartikel['fremdnummer'])){
            $csnummer = $crosssellingartikel['fremdnummer'];
          }
          $cstmpid = 0;
          $response = $this->getIdByNumber($crosssellingartikel['id'],$csnummer);
          if(!empty($response['id'])){
            $cstmpid = $response['id'];
          }

          if($cstmpid > 0){
            switch ($crosssellingartikel['art']) {
              case '1':
                $crosssellingaehnlich[] = array('id' => $cstmpid, 'name' => $crosssellingartikel['name_de']);
                break;
              case '2':
                $crosssellingzubehoer[] = array('id' => $cstmpid, 'name' => $crosssellingartikel['name_de']);
                break;
              default:
                break;
            }

            if($crosssellingartikel['gegenseitigzuweisen']){
              $crosssellingartikel['shopwareid'] = $cstmpid;
              $gegenseitigzuweisen[] = $crosssellingartikel;
            }
          }
        }
      }


      if(empty($hersteller))
      {
        $hersteller='Sonstige';
      }

      if($laststock!='1')
      {
        $laststock=0;
      }

      //HTML Maskierung bekämpfen
      $name_de = str_replace(array("&apos;"), "'", $name_de);
      $name_en = str_replace(array("&apos;"), "'", $name_en);
      $description = str_replace(array("&apos;"), "'", $description);
      $description_en = str_replace(array("&apos;"), "'", $description_en);
      $metatitle = str_replace(array("&apos;"), "'", $metatitle);
      $metatitle_en = str_replace(array("&apos;"), "'", $metatitle_en);
      $keywords = str_replace(array("&apos;"), "'", $keywords);
      $keywords_en = str_replace(array("&apos;"), "'", $keywords_en);
      $description = str_replace(array("<",">","&"), "", $description);
      $description_en = str_replace(array("<",">","&"), "", $description_en);
      $metatitle = str_replace(array("<",">","&"), "", $metatitle);
      $metatitle_en = str_replace(array("<",">","&"), "", $metatitle_en);
      $keywords = str_replace(array("<",">","&"), "", $keywords);
      $keywords_en = str_replace(array("<",">","&"), "", $keywords_en);
      //$keywords = preg_replace("/[^a-zA-Z0-9äüöß ]/",'',$keywords);
      //$metatitle = preg_replace("/[^a-zA-Z0-9äöüß ]/",'',$metatitle);

      if($inaktiv)
      {
        $aktiv=0;
      } else {
        $aktiv=1;
      }

      if($tmp[$i]['ausverkauft']){
        $lageranzahl=0;
        $laststock='1';
      }

      $topseller=0;
      if($tmp[$i]['topseller']){
        $topseller=1;
      }

      $pseudolager = trim($tmp[$i]['pseudolager']);
      if($pseudolager > 0){
        $lageranzahl=$pseudolager;
      }

      $prices = array();
      $kundengruppentmp = $this->adapter->get('customerGroups');
      $kundengruppen = array();
      if(!empty($kundengruppentmp['data']) && is_array($kundengruppentmp['data'])){
        foreach ($kundengruppentmp['data'] as $key => $value) {
          $kundengruppen[$value['key']] = array('id' => $value['id'],'taxInput' =>$value['taxInput']);
        }
      }

      $pseudoPriceForDefaultGroup = $pseudopreisBrutto;
      if(!$kundengruppen['EK']['taxInput']){
          $pseudoPriceForDefaultGroup = $pseudopreisBrutto / (1 + ($steuersatz / 100));
      }

      if(count($tmp[$i]['staffelpreise_standard'])>0){
        $bismenge = 0;
        $mindestabnahme = 1;

        $hilfsarray = [];
        foreach ($tmp[$i]['staffelpreise_standard'] as $standardstaffelpreis){
          $hilfsarray[$standardstaffelpreis['ab_menge']] = [
            'preis' => $standardstaffelpreis['preis'],
            'bruttopreis' => $standardstaffelpreis['bruttopreis']];
        }
        krsort($hilfsarray);

        foreach ($hilfsarray as $key => $value)
        {
          if($mindestabnahme > $key){
            $mindestabnahme = $key;
          }
          $prices[] = array(
            'customerGroupKey' => 'EK',
            'price' => $kundengruppen['EK']['taxInput']?$value['bruttopreis']:$value['preis'],
            'pseudoPrice' => $pseudoPriceForDefaultGroup,
            'from' => $key,
            'to' => $bismenge-1);
          $bismenge = $key;
        }
        $prices[count($prices)-1]['from'] = 1;
      }else{
        //Einfacher Preis
        $prices[] = array(
          'customerGroupKey' => 'EK',
          'price' => $kundengruppen['EK']['taxInput']?$preis:$nettopreis,
          'pseudoPrice' => $pseudoPriceForDefaultGroup);
        $mindestabnahme = 1;
      }

      if(count($tmp[$i]['staffelpreise_gruppen'])>0){
        $hilfsarray = array();

        foreach ($tmp[$i]['staffelpreise_gruppen'] as $gruppenstaffelpreise){
          if(array_key_exists($gruppenstaffelpreise['gruppeextern'], $kundengruppen)){
            $gruppenpreis = $gruppenstaffelpreise['bruttopreis'];
            $gruppenpseudopreis = $pseudopreisBrutto;
            if(!$kundengruppen[$gruppenstaffelpreise['gruppeextern']]['taxInput']){
              $gruppenpreis = $gruppenstaffelpreise['preis'];
              $gruppenpseudopreis = $pseudopreisBrutto / (1 + ($steuersatz / 100));
            }
            $hilfsarray[$gruppenstaffelpreise['gruppeextern']][$gruppenstaffelpreise['ab_menge']] = [
              'preis' => $gruppenpreis,
              'pseudopreis' => $gruppenpseudopreis];
          }
        }

        foreach ($hilfsarray as $kundengruppe => $preisdatenarray) {
          $bismenge = 0;
          krsort($preisdatenarray);
          foreach ($preisdatenarray as $abmenge => $preisdaten) {
            $prices[] = array(
              'customerGroupKey' => $kundengruppe,
              'price' => $preisdaten['preis'],
              'pseudoPrice' => $preisdaten['pseudopreis'],
              'from' => $abmenge,
              'to' => $bismenge-1);
            $bismenge = $abmenge;
          }
          $prices[count($prices)-1]['from'] = 1;
        }
      }

      //Schritt 3: Bild des Hauptartikels hinzufügen
      if($shopbilderuebertragen && !empty($tmp[$i]['matrix_varianten']) && count($tmp[$i]['matrix_varianten'])){
        //Es gibt Varianten also Suche nach Gruppenbild
        //Versuch 1: Suche nach Standardgruppenbild
        $bildgefunden = false;
        $vorschaubereitsgesetzt = false;
        $cdateien = count($tmp[$i]['Dateien']);
        for ($k=0; $k < $cdateien; $k++){
          if(!$bildgefunden && $tmp[$i]['Dateien'][$k]['stichwort'] === 'Gruppenbild'){
            $dateiname = $tmp[$i]['Dateien'][$k]['titel'];
            if($dateiname === ''){
              $dateiname = $tmp[$i]['Dateien'][$k]['filename'];
              $dateiname = substr($dateiname, 0, strlen($dateiname)-(strlen($tmp[$i]['Dateien'][$k]['extension'])+1));
            }
            $dateiname = preg_replace('/[^a-zA-Z0-9._\-]/', '', $dateiname);
            $bildgefunden = true;
            $vorschaubereitsgesetzt = true;
            $bilderarray[$nummer][] = array(
              'album' => -1,
              'extension' => $tmp[$i]['Dateien'][$k]['extension'],
              'name' => $dateiname,
              'description' => $tmp[$i]['Dateien'][$k]['beschreibung']?$tmp[$i]['Dateien'][$k]['beschreibung']:$name_de,
              'filename' => $tmp[$i]['Dateien'][$k]['filename'],
              'main' => 1,
              'intid' => $tmp[$i]['Dateien'][$k]['id'],
              'extid' => $tmp[$i]['Dateien'][$k]['extid'],
              'version' => $tmp[$i]['Dateien'][$k]['version'],
              'file' => 'data:image/'.$tmp[$i]['Dateien'][$k]['extension'].';base64,'.$tmp[$i]['Dateien'][$k]['datei']);
            break;
          }
        }
        //Falls kein Standardgruppenbild existiert: Nimm erstbestes Shopbild als Gruppenbild
        if(!$bildgefunden){
          $cdateien = !empty($tmp[$i]['Dateien'])?count($tmp[$i]['Dateien']):0;
          for ($k=0; $k < $cdateien; $k++) {
            if($tmp[$i]['Dateien'][$k]['stichwort'] === 'Shopbild'){
              $dateiname = $tmp[$i]['Dateien'][$k]['titel'];
              if($dateiname === ''){
                $dateiname = $tmp[$i]['Dateien'][$k]['filename'];
                $dateiname = substr($dateiname, 0, strlen($dateiname)-(strlen($tmp[$i]['Dateien'][$k]['extension'])+1));
              }
              $dateiname = preg_replace('/[^a-zA-Z0-9._\-]/', '', $dateiname);
              $bilderarray[$nummer][] = array(
                'album' => -1,
                'extension' => $tmp[$i]['Dateien'][$k]['extension'],
                'name' => $dateiname,
                'description' => $tmp[$i]['Dateien'][$k]['beschreibung']?:$name_de,
                'filename' => $tmp[$i]['Dateien'][$k]['filename'],
                'intid' => $tmp[$i]['Dateien'][$k]['id'],
                'extid' => $tmp[$i]['Dateien'][$k]['extid'],
                'version' => $tmp[$i]['Dateien'][$k]['version'],
                'main' => 2,
                'file' => 'data:image/'.$tmp[$i]['Dateien'][$k]['extension'].';base64,'.$tmp[$i]['Dateien'][$k]['datei']);
            }
          }
        }
      }elseif($shopbilderuebertragen){
        $firstismain = 1;
        $cdateien = count($tmp[$i]['Dateien']);
        for ($l=0; $l < $cdateien; $l++) {
          if($tmp[$i]['Dateien'][$l]['stichwort'] === 'Shopbild'){
            $dateiname = $tmp[$i]['Dateien'][$l]['titel'];
            if($dateiname === ''){
              $dateiname = $tmp[$i]['Dateien'][$l]['filename'];
              $dateiname = substr($dateiname, 0, strlen($dateiname)-(strlen($tmp[$i]['Dateien'][$l]['extension'])+1));
            }
            $dateiname = preg_replace('/[^a-zA-Z0-9._\-]/', '', $dateiname);
            $bilderarray[$nummer][] = array(
              'album' => -1,
              'extension' => $tmp[$i]['Dateien'][$l]['extension'],
              'name' => $dateiname,
              'description' => $tmp[$i]['Dateien'][$l]['beschreibung']?$tmp[$i]['Dateien'][$l]['beschreibung']:$name_de,
              'filename' => $tmp[$i]['Dateien'][$l]['filename'],
              'intid' => $tmp[$i]['Dateien'][$l]['id'],
              'extid' => $tmp[$i]['Dateien'][$l]['extid'],
              'version' => $tmp[$i]['Dateien'][$l]['version'],
              'main' => $firstismain?1:2,
              'file' => 'data:image/'.$tmp[$i]['Dateien'][$l]['extension'].';base64,'.$tmp[$i]['Dateien'][$l]['datei']);
            $firstismain = 0;
          }
        }
      }


      //Schritt 4: Varianten abarbeiten
      $varianten = null;
      if($variantenuebertragen && !empty($tmp[$i]['matrix_varianten']) && count($tmp[$i]['matrix_varianten']))
      {
        $gruppen = null;
        $first = true;

        foreach($tmp[$i]['matrix_varianten']['gruppen'] as $gruppe => $eigenschaftenarr)
        {
          $varianten['configuratorSet']['groups'][] = array('name'=>$gruppe);
          $key = count($varianten['configuratorSet']['groups'])-1;
          foreach($eigenschaftenarr as $eigenschaft => $v)
          {
            $varianten['configuratorSet']['groups'][$key]['options'][] = array('name'=>(string)$eigenschaft);
          }
        }
        $firstismain = 1;
        foreach($tmp[$i]['matrix_varianten']['artikel'] as $art => $varr)
        {
          if($varr[0]['gesperrt']){
            continue;
          }
          //Der Artikel muss als Variante von sich selbst eingetragen werden um über seine eigene Nummer gefunden werden zu können
          if($firstismain){
            $varianten['variants'][] = array(
              'number'=>$nummer,
              'inStock'=>($varr[0]['pseudolager'] > 0?$varr[0]['pseudolager']:$varr[0]['lag']),
              'attributeVisionPhysicalStockForSale'=>($varr[0]['pseudolager'] > 0?$varr[0]['pseudolager']:$varr[0]['lag']),
              'active'   => 0,
              'ean' => $varr[0]['ean'],
              'isMain' => 0,
              'lastStock' => $varr[0]['restmenge']);
            $key = count($varianten['variants'])-1;
            $varianten['variants'][$key]['additionalText'] = '';
          }

          $varianten['variants'][] = array(
            'number'=> $varr[0]['nummer'],
            'inStock'=> ($varr[0]['pseudolager'] > 0?$varr[0]['pseudolager']:$varr[0]['lag']),
            'attributeVisionPhysicalStockForSale'=> ($varr[0]['pseudolager'] > 0?$varr[0]['pseudolager']:$varr[0]['lag']),
            'active' => 1,
            'ean' => $varr[0]['ean'],
            'isMain' => $firstismain,
            'lastStock' => $varr[0]['restmenge']);
          $key = count($varianten['variants'])-1;
          $varianten['variants'][$key]['additionalText'] = '';


          //Schritt 3b: Variantenbilder
          $variantenarrayid = -1;
          //Prüfen welchen Index die Variante im Variantenarray hat
          $cvarianten = count($tmp[$i]['artikel_varianten']);
          for ($k=0; $k < $cvarianten; $k++) {
            if($tmp[$i]['artikel_varianten'][$k]['nummer'] == $varr[0]['nummer']){
              $variantenarrayid = $k;
              break;
            }
          }

          if($vorschaubereitsgesetzt){
            $firstismain = 0;
          }
          if($shopbilderuebertragen){
            $cdateien = count($tmp[$i]['artikel_varianten'][$variantenarrayid]['Dateien']['filename']);
            for ($l = $cdateien-1; $l >= 0; $l--) {
              if($tmp[$i]['artikel_varianten'][$variantenarrayid]['Dateien']['stichwort'][$l] === 'Shopbild'){
                $bilderarray[$tmp[$i]['artikel_varianten'][$k]['nummer']][] = array(
                  'album' => -1,
                  'extension' => $tmp[$i]['artikel_varianten'][$variantenarrayid]['Dateien']['extension'][$l],
                  'title' =>  preg_replace('/[^a-zA-Z0-9._\-]/', '', $tmp[$i]['artikel_varianten'][$variantenarrayid]['Dateien']['titel'][$l]),
                  'description' => ($tmp[$i]['artikel_varianten'][$variantenarrayid]['Dateien']['beschreibung'][$l] ? $tmp[$i]['artikel_varianten'][$variantenarrayid]['Dateien']['beschreibung'][$l] : $name_de),
                  'filename' => $tmp[$i]['artikel_varianten'][$variantenarrayid]['Dateien']['filename'][$l],
                  'main' => ($firstismain ? 1 : 2),
                  'intid' => $tmp[$i]['artikel_varianten'][$variantenarrayid]['Dateien']['id'][$l],
                  'extid' => $tmp[$i]['artikel_varianten'][$variantenarrayid]['Dateien']['extid'][$l],
                  'version' => $tmp[$i]['artikel_varianten'][$variantenarrayid]['Dateien']['version'][$l],
                  'file' => 'data:image/' . $tmp[$i]['artikel_varianten'][$variantenarrayid]['Dateien']['extension'][$l] . ';base64,' . $tmp[$i]['artikel_varianten'][$variantenarrayid]['Dateien']['datei'][$l]);
              }
            }
          }
          foreach($varr as $v)
          {
            $varianten['variants'][$key]['additionalText'] .= ($varianten['variants'][$key]['additionalText']!=''?' / ': '').$v['values'];
            $varianten['variants'][$key]['configuratorOptions'][] = array(
              'group'=>$v['name'],
              'option'=>$v['values']);
          }

          $gruppenpseudopreis = $varr[0]['pseudopreis'];
          if(!empty($varr[0]['staffelpreise_standard'])){
            $vbismenge = 0;
            $vmindestabnahme = 1;

            $hilfsarray = [];
            foreach ($varr[0]['staffelpreise_standard'] as $standardstaffelpreis){
              $hilfsarray[$standardstaffelpreis['ab_menge']] = [
                'preis' => $standardstaffelpreis['preis'],
                'bruttopreis' => $standardstaffelpreis['bruttopreis']];
            }

            krsort($hilfsarray);

            $vprices = [];
            foreach ($hilfsarray as $vkey => $vvalue)
            {
              if($vmindestabnahme > $vkey){
                $vmindestabnahme = $vkey;
              }
              $vprices[] = array(
                'customerGroupKey' => 'EK',
                'pseudoPrice' => $gruppenpseudopreis,
                'price' => $kundengruppen['EK']['taxInput']?$vvalue['bruttopreis']:$vvalue['preis'],
                'from' => $vkey,
                'to' => $vbismenge-1);
              $vbismenge = $vkey;
            }
            $vprices[count($vprices)-1]['from'] = 1;


            if(count($varr[0]['staffelpreise_gruppen'])>0){
              $hilfsarray = array();

              foreach ($varr[0]['staffelpreise_gruppen'] as $gruppenstaffelpreise){
                if(array_key_exists($gruppenstaffelpreise['gruppeextern'], $kundengruppen)){
                  $gruppenpreis = $gruppenstaffelpreise['bruttopreis'];                  
                  $gruppenpseudopreis = $pseudopreisBrutto;
                  if(!$kundengruppen[$gruppenstaffelpreise['gruppeextern']]['taxInput']){
                    $gruppenpreis = $gruppenstaffelpreise['preis'];
                    $gruppenpseudopreis = $pseudopreisBrutto / (1 + ($steuersatz / 100));
                  }
                  $hilfsarray[$gruppenstaffelpreise['gruppeextern']][$gruppenstaffelpreise['ab_menge']] = [
                    'preis' => $gruppenpreis,
                    'pseudopreis' => $gruppenpseudopreis];
                }
              }

              foreach ($hilfsarray as $kundengruppe => $preisdatenarray) {
                $bismenge = 0;
                krsort($preisdatenarray);
                foreach ($preisdatenarray as $abmenge => $preisdaten) {
                  $vprices[] = array(
                    'customerGroupKey' => $kundengruppe,
                    'price' => $preisdaten['preis'],
                    'pseudoPrice' => $preisdaten['pseudoPrice'],
                    'from' => $abmenge,
                    'to' => $bismenge-1);
                  $bismenge = $abmenge;
                }
                $vprices[count($vprices)-1]['from'] = 1;
              }
            }

            $varianten['variants'][$key]['prices'] = $vprices;
          }else{
            //Einfacher Preis
            $vprices[] = array(
              'customerGroupKey' => 'EK',
              'price' => $varr[0]['preis'],
              'pseudoPrice' => $pseudoPriceForDefaultGroup);
            $mindestabnahme = 1;
            $varianten['variants'][$key]['prices'] = $vprices;
          }

          $varianten['variants'][$key]['shippingtime'] = $varr[0]['lieferzeitmanuell'];

          foreach ($varr[0]['freifelder']['DE'] as $ffkey => $ffvalue) {
            $tmpval = explode('_', $ffkey);
            $prefix = $tmpval[0];
            unset($tmpval[0]);
            $anweisung = implode('_',$tmpval);
            switch ($prefix) {
              case 'mainDetail':
                if (isset($anweisung) && $anweisung !== '') {
                  $varianten['variants'][$key][$anweisung] = $ffvalue;
                }
                break;
              case 'configuratorSet':
              case 'property':
                break;
              default:
                if (isset($ffkey) && $ffkey) {
                  $varianten['variants'][$key]['attribute'][$ffkey] = $ffvalue;
                }
                break;
            }
          }
          $firstismain = 0;
        }
      }

      //Schritt 5: Filtergruppen abarbeiten
      $eigenschaften = array();
      if(!empty($tmp[$i]['matrix_varianten'])){
        $filtergruppe = 3;
        $eigenschaften = array();
        foreach ($tmp[$i]['matrix_varianten']['gruppen'] as $gruppe => $eigenschaftenarr) {
          foreach ($eigenschaftenarr as $gruppenwert => $unused) {
            $eigenschaften[] = array(
              'option' => array(
                'name' => $gruppe,
                'filterable' => 'true'
              ),
              'value' => $gruppenwert
            );
          }
        }
      }

      //Schritt 6: Artikeldaten zusammensetzen
      $artikeldaten = array(
      'name' => $name_de,
      'lastStock' => $laststock,
      'tax' => $steuersatz, // alternativ 'taxId' => 1,
      'supplier' => $hersteller, // alternativ 'supplierId' => 2,
      'description' => $description,
      'descriptionLong' => $description_long,
      'keywords' => $keywords,
      'metaTitle' => $metatitle,
      'highlight' => $topseller,
      'active'   => $aktiv,
      //'filterGroupId'  => $filtergruppe,
      'propertyValues' => $eigenschaften,
      'attributeVisionPhysicalStockForSale' => $lageranzahl,
      'mainDetail' => array(
        'active' => $aktiv,
        'shippingtime' => $shippingtime,
        'ean' => $ean,
        'weight' => $gewicht,
        'width' => $breite,
        'len' => $laenge,
        'height' => $hoehe,
        'minPurchase' => $mindestabnahme,
        'supplierNumber' => $herstellernummer,
        'inStock' => $lageranzahl,
        'attributeVisionPhysicalStockForSale' => $lageranzahl,
        'prices' => $prices
        )
      );

      $pricegroupid = null;
      if(!empty($einstellungen['felder']['priceGroupIdStandard'])){
        $pricegroupid = $einstellungen['felder']['priceGroupIdStandard'];
      }
      $artikeldaten['priceGroupId'] = $pricegroupid;

      if(!empty($kategorien))
      {
        $artikeldaten['categories'] = $kategorien;
      }

      if($crosssellingartikeluebertragen)
      {
        $artikeldaten['similar'] = $crosssellingaehnlich;
        $artikeldaten['related'] = $crosssellingzubehoer;
      }else{
        if(isset($artikeldaten['similar']))
        {
          unset($artikeldaten['similar']);
        }
        if(isset($artikeldaten['related']))
        {
          unset($artikeldaten['related']);
        }
      }
      if($this->nurpreise){
        unset($artikeldaten['description'],
          $artikeldaten['descriptionLong'],
          $artikeldaten['keywords'],
          $artikeldaten['metaTitle']);
      }

      if(!$eigenschaftenuebertragen)
      {
        unset($artikeldaten['filterGroupId']);
      }
      if($varianten)
      {
        foreach($varianten as $k => $v)
        {
          $artikeldaten[$k] = $v;
        }
      }

      //Schritt 7: Eigenschaften / Freifelder
      if(isset($tmp[$i]['eigenschaften'])){
        foreach ($tmp[$i]['eigenschaften'] as $key => $value) {
          $tmpval = explode('_', $value['name']);
          $prefix = $tmpval[0];
          unset($tmpval[0]);
          $anweisung = implode('_',$tmpval);
          switch ($prefix) {
            case 'mainArticle':
              if(!empty($anweisung)){
                switch ($anweisung) {
                  case 'filterGroup':
                      $filtergruppe = $this->GetGroup($value['values']);
                      $filtergruppenid = $filtergruppe['id'];
                      $artikeldaten['filterGroupId'] = $filtergruppenid;
                    break;
                  case 'customerGroups':
                    $customerGroups = explode(',', str_replace(' ', '', $value['values']));
                    foreach ($customerGroups as $customerGroup){
                      if(array_key_exists($customerGroup, $kundengruppen)){
                        if(!is_array($artikeldaten['customerGroups'])){
                          $artikeldaten['customerGroups'] = array();
                        }
                        $artikeldaten['customerGroups'][] = array('id' => $kundengruppen[$customerGroup]['id']);
                      }
                    }
                    break;
                  default:
                    $artikeldaten[$anweisung] = $value['values'];
                    break;
                }
              }
              break;
            case 'mainDetail':
              if(!empty($anweisung)){
                $artikeldaten['mainDetail'][$anweisung] = $value['values'];
              }
              break;
            case 'configuratorSet':
              if(!empty($tmpval[1])){
                $artikeldaten['configuratorSet'][$anweisung] = $value['values'];
              }
              break;
            case 'articleAttribute':
              if(!empty($anweisung)){
                $artikeldaten['attribute'][$anweisung] = $value['values'];
              }
              break;
            default:
              if(!empty($value['name'])){
                $artikeldaten['propertyValues'][] = array('option'=>array('name'=>$value['name']),'value'=>$value['values']);
              }
              break;
          }
        }
      }
      if(isset($tmp[$i]['freifelder']['DE'])){
        foreach ($tmp[$i]['freifelder']['DE'] as $ffkey => $ffvalue) {
          $tmpval = explode('_', $ffkey);
          $prefix = $tmpval[0];
          unset($tmpval[0]);
          $anweisung = implode('_',$tmpval);
          switch ($prefix) {
            case 'mainArticle':
              if(isset($anweisung) && $anweisung !== ''){
                switch ($anweisung) {
                  case 'filterGroup':
                    $filtergruppe = $this->GetGroup($ffvalue);
                    $filtergruppenid = $filtergruppe['id'];
                    $artikeldaten['filterGroupId'] = $filtergruppenid;
                    break;
                  case 'customerGroups':
                    $cgtmp = explode(',', str_replace(' ', '', $ffvalue));
                    foreach ($cgtmp as $customerGroup){
                      if(array_key_exists($customerGroup, $kundengruppen)){
                        if(!is_array($artikeldaten['customerGroups'])){
                          $artikeldaten['customerGroups'] = array();
                        }
                        $artikeldaten['customerGroups'][] = array('id' => $kundengruppen[$customerGroup]['id']);
                      }
                    }
                    break;
                  default:
                    $artikeldaten[$anweisung] = $ffvalue;
                    break;
                }
              }
              break;
            case 'mainDetail':
              if(isset($anweisung) && $anweisung!==''){
                $artikeldaten['mainDetail'][$anweisung] = $ffvalue;
              }
              break;
            case 'configuratorSet':
              if(isset($anweisung) && $anweisung!==''){
                $artikeldaten['configuratorSet'][$anweisung] = $ffvalue;
              }
              break;
            case 'property':
              if(isset($anweisung) && $anweisung!==''){
                $artikeldaten['propertyValues'][] = array('option'=>array('name'=>$anweisung),'value'=>$ffvalue);
              }
              break;
            default:
              if(isset($ffkey) && $ffkey){
                $artikeldaten['attribute'][$ffkey] = $ffvalue;
              }
              break;
          }
        }
      }

      //Schritt 8: Artikel senden
      $variantBatches = [];
      $variantNummer = '';
      if(!empty($artikeldaten['variants'])){
        $variantNummer = $artikeldaten['variants'][count($artikeldaten['variants'])-1]['number'];
      }
      if(count($artikeldaten['variants']) > 1000){
        foreach ($artikeldaten['variants'] as $index => $variante){
          $variantBatches[(int)floor($index/1000)][] = $variante;
        }
        $artikeldaten['variants'] = $variantBatches[0];
        $variantNummer = $artikeldaten['variants'][count($artikeldaten['variants'])-1]['number'];
        unset($variantBatches[0]);
      }

      $artikelexistiert = null;
      $response = $this->getIdByNumber($tmp[$i]['artikelid'],$nummer,!empty($artikeldaten['variants']),$variantNummer);
      if(!empty($response['id'])){
        $artikelexistiert = $response['id'];
      }
      if(!$artikelexistiert){
        $artikeldaten['mainDetail']['number'] = $nummer;
      }

      if(!empty($artikeldaten['variants'])){
        foreach ($artikeldaten['variants'] as $variant){
          if($variant['isMain']){
            $artikeldaten['mainDetail']['prices'] = $variant['prices'];
            $artikeldaten['mainDetail']['inStock'] = $variant['inStock'];
            $artikeldaten['mainDetail']['active'] = $variant['active'];
            $artikeldaten['mainDetail']['attributeVisionPhysicalStockForSale'] = $variant['attributeVisionPhysicalStockForSale'];
            $artikeldaten['mainDetail']['ean'] = $variant['ean'];
            $artikeldaten['mainDetail']['height'] = $variant['height'];
            $artikeldaten['mainDetail']['len'] = $variant['len'];
            $artikeldaten['mainDetail']['width'] = $variant['width'];
            $artikeldaten['mainDetail']['weight'] = $variant['weight'];
            $artikeldaten['mainDetail']['supplierNumber'] = $variant['supplierNumber'];
            $artikeldaten['mainDetail']['shippingtime'] = $variant['shippingtime'];
            $artikeldaten['mainDetail']['minPurchase'] = $variant['minPurchase'];
            break;
          }
        }
      }

      if(empty($tmp[$i]['variante']))
      {
        //Artikel ist keine Variante, wenn nicht vorhanden neu anlegen
        //Schritt 8b: Artikel (neu) anlegen
        //Propertyvalues verhindert den Upload des Artikels wenn keine Filtergruppenid, oder keine Propertygruppe gesetzt ist
        if(isset($artikeldaten['filterGroupId'])){
          if(!$eigenschaftenuebertragen || $artikeldaten['filterGroupId'] < 1)
          {
            unset($artikeldaten['propertyValues']);
          }
        }else{
          unset($artikeldaten['propertyValues']);
        }
        if(!$artikelexistiert)
        {
          $artikeldaten['categories'] = $kategorien; // Beim anlegen immer Kategorien übergeben
          $result = $this->adapter->post('articles',  $artikeldaten);
          $response = $this->getIdByNumber($tmp[$i]['artikelid'],$nummer,!empty($artikeldaten['variants']));
          if(!empty($response['id'])){
            $artikelexistiert = $response['id'];
          }

          $this->ShopwareLog('Artikel neu anlegen: '.$nummer, print_r($result,true));
        } else {

          if(!$kategorienuebertragen)
          {
            unset($artikeldaten['categories']);
          }
          if($this->ignoreartikelbeschreibung){
            unset($artikeldaten['name'],
              $artikeldaten['metaTitle'],
              $artikeldaten['description'],
              $artikeldaten['descriptionLong'],
              $artikeldaten['keywords']);
          }
          $result = $this->adapter->put('articles/'.$artikelexistiert,  $artikeldaten);
          $this->ShopwareLog('Artikel Update: '.$nummer, print_r($result,true));
        }
        if(!empty($artikelexistiert) && !empty($artikeldaten['variants'])){
          if(!empty($variantBatches)){
            foreach ($variantBatches as $variantBatch){
              $artikeldatenKlein['variants'] = $variantBatch;

              $this->adapter->put('articles/'.$artikelexistiert,  $artikeldatenKlein);
            }
          }

          unset($artikeldaten['variants'],$artikeldaten['mainDetail']['number']);
          $result = $this->adapter->put('articles/'.$artikelexistiert,  $artikeldaten);
        }

        if(!is_array($result)){
          return $result;
        }
        //Schritt 8c: Alle Bilder anlegen und neu verlinken
        $first = 1;
        $posbild = 0;
        if($shopbilderuebertragen){
          foreach ($bilderarray as $key => $value) {
            $cvalue = count($value);
            for ($k = 0; $k < $cvalue; $k++) {

              $mediaId = '';
              $extId = $this->app->remote->GetShopexportMappingExt($this->shopid, 'datei', $value[$k]['intid'], $value[$k]['version']);
              if(!empty($extId)){
                $mediaData = $this->adapter->get('media/'.$extId);
                if(!empty($mediaData['success'])){
                  $mediaId = $mediaData['data']['id'];
                }
              }

              if(empty($mediaId)){
                $resultb = $this->adapter->post('media',  $value[$k]);
                if(is_array($resultb) && $resultb['success']) {
                  $mediaId = $resultb['data']['id'];
                  $this->app->remote->ShopexportMappingSet($this->shopid, 'datei', $value[$k]['intid'], $mediaId, $value[$k]['version']);
                }
              }

              if(empty($mediaId)){
                continue;
              }

              //Bild verlinken
              $posbild++;
              $bildlinkdaten = array(
                'id' => $key,
                'images' => array(
                  array('mediaId' => $mediaId, 'position' => $posbild))
              );
              if($first){
                //Entfernt vorherige Verlinkungen von Artikelvarianten
                $bildlinkdaten['__options_images'] = array('replace' => 1);
                $first = 0;
              }
              if($key == $nummer){
                $this->adapter->put('articles/' . $artikelexistiert, $bildlinkdaten);
              }else{
                $artikelinfo = $this->getVariantProductInfoByNumber($key,$key,true);
                $id = $artikelinfo['id'];
                if($artikelinfo['typ']==='normal'){
                  $this->adapter->put('articles/' .$id,  $bildlinkdaten);
                }else{
                  $this->adapter->put('variants/' .$id,  $bildlinkdaten);
                }
              }
            }
          }
        }
      } else {
        //Artikel ist Variante
        //Falls Bildleichen auftauchen liegt es vermutlich daran dass bei einzelnen Varianten die zugeordneten Bilder nicht gelöscht werden
          $artikeldaten = array(
            'variants' => array(
              array(
                'number' => $nummer,
                'active' => $aktiv,
                'inStock' => $lageranzahl,
                'attributeVisionPhysicalStockForSale' => $lageranzahl)));
        $result = $this->adapter->put('articles/'.$artikelexistiert, $artikeldaten);
      }

      if(!is_array($result)){
        return $result;
      }

      $artikelid = $result['data']['id'];
      //Schritt 9:Übersetzung
      if(!$this->nurpreise){
        $storestmp = $this->app->DB->SelectArr("SELECT subshopkennung, aktiv, sprache FROM shopexport_subshop WHERE shop = '$this->shopid' AND aktiv = 1");
        if(is_array($storestmp)){
          $dimensonIds = [];
          $dimensionValuesIds = [];
          if(!empty($artikelexistiert)){
            $articleData = $this->adapter->get('articles/'.$artikelexistiert);
            if(!empty($articleData['data']['configuratorSet']['groups'])){
              foreach ($articleData['data']['configuratorSet']['groups'] as $configuratorGroup){
                $dimensonIds[$configuratorGroup['name']] = $configuratorGroup['id'];
              }
            }
            if(!empty($articleData['data']['details'][0])){
              foreach ($articleData['data']['details'] as $articleDetail){
                foreach ($articleDetail['configuratorOptions'] as $detailOption){
                  $dimensionValuesIds[$detailOption['name']] = $detailOption['id'];
                }
              }
            }
            if(!empty($articleData['data']['mainDetail']['configuratorOptions'])){
              foreach ($articleData['data']['mainDetail']['configuratorOptions'] as $detailOption){
                  $dimensionValuesIds[$detailOption['name']] = $detailOption['id'];
              }
            }
          }

          foreach ($storestmp as $key => $value){
            if($value['sprache'] !== 'deutsch'){
              $iso = $this->app->DB->Select("SELECT iso FROM sprachen WHERE iso <> '' AND (iso='".$value['sprache']."' OR LOWER(bezeichnung_de)='".$value['sprache']."' OR LOWER(alias)='".$value['sprache']."' ) LIMIT 1");
              if($iso === 'DE' || empty($iso)){
                continue;
              }

              $uebersetzung = array();
              if($iso === 'EN'){
                if(!empty($name_en)){
                  $uebersetzung['name'] = $name_en;
                }
                if(!empty($description_en)){
                  $uebersetzung['description'] = $description_en;
                }
                if(!empty($description_long_en)){
                  $uebersetzung['descriptionLong'] = $description_long_en;
                }
                if(!empty($keywords_en)){
                  $uebersetzung['keywords'] = $keywords_en;
                }
                if(!empty($metatitle_en)){
                  $uebersetzung['metaTitle'] = $metatitle_en;
                }
              }

              if(isset($tmp[$i]['texte'])){
                $ctexte = count($tmp[$i]['texte']);
                for ($t=0; $t < $ctexte; $t++) {
                  if($tmp[$i]['texte'][$t]['sprache'] === $iso){
                    if(!empty($tmp[$i]['texte'][$t]['name']))
                    {
                      $uebersetzung['name'] = $tmp[$i]['texte'][$t]['name'];
                    }
                    if(!empty($tmp[$i]['texte'][$t]['meta_description']))
                    {
                      $uebersetzung['description'] = $tmp[$i]['texte'][$t]['meta_description'];
                    }
                    if(!empty($tmp[$i]['texte'][$t]['beschreibung_online']))
                    {
                      $uebersetzung['descriptionLong'] = $tmp[$i]['texte'][$t]['beschreibung_online'];
                    }
                    if(!empty($tmp[$i]['texte'][$t]['meta_keywords']))
                    {
                      $uebersetzung['keywords'] = $tmp[$i]['texte'][$t]['meta_keywords'];
                    }
                    if(!empty($tmp[$i]['texte'][$t]['meta_title']))
                    {
                      $uebersetzung['metaTitle'] = $tmp[$i]['texte'][$t]['meta_title'];
                    }
                    continue;
                  }
                }
              }
              if($iso !== 'DE' && isset($tmp[$i]['freifelder'][$iso])){
                //Anzupassen wenn veraltet Freifeldübertragung die Anpassung von freifeld_ notwending macht AP 14.11.18
                $cfreifelder = count($tmp[$i]['freifelder'][$iso]);
                for ($f=0; $f < $cfreifelder; $f++) {
                  $uebersetzung['__attribute_'.$tmp[$i]['freifelder'][$iso][$f]['mapping']] = $tmp[$i]['freifelder'][$iso][$f]['wert'];
                }
              }

              if(count($uebersetzung) > 0 ){
                $uebersetzungsdaten = array('shopId' => $value['subshopkennung'], 'type' => 'article', 'key' => $artikelid, 'data' => $uebersetzung);
                $tresult = $this->adapter->post('translations',  $uebersetzungsdaten);
              }


              if(!empty($tmp[0]['matrix_varianten']['texte']['gruppen'][$iso])){
                foreach ($tmp[0]['matrix_varianten']['texte']['gruppen'][$iso] as $dimensionName => $dimensionTranslation){
                  if(isset($dimensonIds[$dimensionName])){
                    $uebersetzung = ['name' => $dimensionTranslation];
                    $uebersetzungsdaten = array('shopId' => $value['subshopkennung'], 'type' => 'configuratorgroup', 'key' => $dimensonIds[$dimensionName], 'data' => $uebersetzung);
                    $tresult = $this->adapter->post('translations',  $uebersetzungsdaten);
                  }
                }
              }

              if(!empty($tmp[0]['matrix_varianten']['texte']['werte'][$iso])){
                foreach ($tmp[0]['matrix_varianten']['texte']['werte'][$iso] as $valueName => $valueTranslation){
                  if(isset($dimensionValuesIds[$valueName])){
                    $uebersetzung = ['name' => $valueTranslation];
                    $uebersetzungsdaten = array('shopId' => $value['subshopkennung'], 'type' => 'configuratoroption', 'key' => $dimensionValuesIds[$valueName], 'data' => $uebersetzung);
                    $tresult = $this->adapter->post('translations',  $uebersetzungsdaten);
                  }
                }
              }

            }
          }
        }
      }

      //Schritt 10: Crossselling gegenseitige zuweisung
      if($crosssellingartikeluebertragen && !empty($gegenseitigzuweisen) && !empty($artikelid)){
        foreach ($gegenseitigzuweisen as $crosssellingartikel){
          $this->crosssellingzuweisen($crosssellingartikel['shopwareid'],$artikelexistiert,$crosssellingartikel['art']);
        }
      }

      if($result['success'])
      {
        $anzahl++;
        //Erstellt Thumbnailbilder für den Artikel - behauptet die Doku
        $result = $this->adapter->put('generateArticleImages/'.$artikelexistiert);
      }
    }

    return $anzahl;
  }

  function GetImagesIdsbyNummer($nummer){
    $params = array(
        'useNumberAsId' => true
        );
    $result = $this->adapter->get('articles/'.$nummer, $params);
    if(is_array($result)){
      return $result["data"]['images'];
    }
    return null;
  }

  public function crosssellingzuweisen($artikelid, $crosssellingartikelid,$art){
    $artikeldaten=[];
    $typ = 'similar';
    if((int)$art===2){
      $typ = 'related';
    }
    $artikelInShopware = $this->adapter->get('articles/'.$artikelid);
    $vorhandeRelation = $artikelInShopware['data'][$typ];
    $vorhandeRelation[] = ['id'=>$crosssellingartikelid];
    $artikeldaten[$typ] = $vorhandeRelation;
    $this->adapter->put('articles/'.$artikelid, $artikeldaten);
  }


  // receive all new articles
  public function ImportInhalt()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $anzahl = 0;
    $this->app->DB->Delete('DELETE FROM inhalt');
    if(!empty($tmp)){
      $ctmp = count($tmp);
      for ($i = 0; $i < $ctmp; $i++) {
        $this->app->DB->Insert("INSERT INTO inhalt (id) VALUES ('')");
        $id = $this->app->DB->GetInsertID();

        foreach ($tmp[$i] as $key => $value) {
          $this->app->DB->Update("UPDATE inhalt SET $key='$value' WHERE id='$id' LIMIT 1");
        }

        $anzahl++;
      }
    }
    // anzahl erfolgreicher updates
    return $anzahl;
  }

  public function ImportDeleteFile()
  {
    $tmp = $this->CatchRemoteCommand('data');

    // pruefe ob $tmp[datei] vorhanden wenn nicht lege an sonst update [inhalt] und [checksum]
    if(!empty($tmp['datei']) && is_numeric($tmp['datei'])){
      //$checksum= $tmp['checksum'];
      $datei = $tmp['datei'];
      $this->app->DB->Delete("DELETE FROM datei WHERE datei='$datei' LIMIT 1");
      $this->app->DB->Delete("DELETE FROM datei_stichwoerter WHERE datei='$datei'");
    }
    return 'ok';
  }

  public function ImportSendFile()
  {
    $tmp = $this->CatchRemoteCommand('data');

    // pruefe ob $tmp[datei] vorhanden wenn nicht lege an sonst update [inhalt] und [checksum]
    if(!empty($tmp['datei']) && is_numeric($tmp['datei'])){
      $datei = $tmp['datei'];
      $inhalt = $this->app->DB->real_escape_string($tmp['inhalt']);
      $checksum = $this->app->DB->real_escape_string($tmp['checksum']);

      $this->app->DB->Delete("DELETE FROM datei WHERE datei='$datei' LIMIT 1");
      $this->app->DB->Delete("INSERT INTO datei (id,datei,inhalt,checksum,logdatei) VALUES ('','$datei','$inhalt','$checksum',NOW())");
    }
    return 'ok';
  }


  public function ImportAddFileSubjekt()
  {
    $tmp = $this->CatchRemoteCommand('data');
    if(!empty($tmp['datei']) && is_numeric($tmp['datei'])
      && !empty($tmp['artikel']) && is_numeric($tmp['artikel'])){
      $artikel = $tmp['artikel'];
      $subjekt = !empty($tmp['subjekt'])?$this->app->DB->real_escape_string($tmp['subjekt']):'';
      $datei = $tmp['datei'];
      //loesche alle stichwoerter und lege alle neu an /subjekt /artikel
      $this->app->DB->Delete("DELETE FROM datei_stichwoerter WHERE artikel='$artikel' AND subjekt='$subjekt' AND datei='$datei' LIMIT 1");
      $this->app->DB->Delete("INSERT INTO datei_stichwoerter (artikel,subjekt,datei) VALUES ('$artikel','$subjekt','$datei')");
    }
    return 'ok';
  }


  // delete an article
  public function ImportDeleteArticle()
  {
    $tmp = $this->CatchRemoteCommand('data');
    if(is_numeric($tmp) && $tmp > 0){
      $this->app->DB->Select("DELETE FROM artikel WHERE artikel='$tmp' LIMIT 1");
    }
    // anzahl erfolgreicher updates
    return $tmp;
  }

  // receive all new articles
  public function ImportExportlink()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $anzahl = 0;
    //$this->app->DB->Delete("DELETE FROM exportlink WHERE datum < DATE_ADD(NOW(), INTERVAL 5 DAYS)");

    $ctmp = count($tmp);
    for($i=0;$i<$ctmp;$i++)
    {
      $this->app->DB->Insert("INSERT INTO exportlink (id,datum) VALUES ('',NOW())");
      $id = $this->app->DB->GetInsertID();

      foreach($tmp[$i] as $key=>$value)
      {
        $this->app->DB->Update("UPDATE exportlink SET $key='$value' WHERE id='$id' LIMIT 1");
      }

      $anzahl++;
    }

    return $anzahl;
  }

  // receive all new articles
  public function ImportArtikelgruppen()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $anzahl = 0;
    $this->app->DB->Delete('DELETE FROM artikelgruppen');
    $ctmp = count($tmp);
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

  // receive all new articles
  public function ImportNavigation()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $anzahl = 0;
    $this->app->DB->Delete('DELETE FROM shopnavigation');
    $ctmp = count($tmp);
    for($i=0;$i<$ctmp;$i++)
    {
      $id = $tmp[$i]['id'];

      $this->app->DB->Insert("INSERT INTO shopnavigation (id) VALUES ('$id')");

      foreach($tmp[$i] as $key=>$value)
      {
        $this->app->DB->Update("UPDATE shopnavigation SET $key='$value' WHERE id='$id' LIMIT 1");
      }

      $anzahl++;
    }

    return $anzahl;
  }

  // receive all new articles
  public function ImportArtikelArtikelGruppe()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $anzahl = 0;
    $this->app->DB->Delete('DELETE FROM artikel_artikelgruppe');
    $ctmp = count($tmp);
    for($i=0;$i<$ctmp;$i++)
    {
      $id = $tmp[$i]['id'];

      $this->app->DB->Insert("INSERT INTO artikel_artikelgruppe (id) VALUES ('$id')");

      foreach($tmp[$i] as $key=>$value)
      {
        $this->app->DB->Update("UPDATE artikel_artikelgruppe SET $key='$value' WHERE id='$id' LIMIT 1");
      }

      $anzahl++;
    }

    return $anzahl;
  }

  //TODO fuer Auftragimport
  //get checksum list from onlineshop
  public function ImportGetAuftraegeAnzahl()
  {
    $einstellungen = $this->app->DB->Select("SELECT einstellungen_json FROM shopexport WHERE id = '$this->shopid' LIMIT 1");
    if($einstellungen){
      $einstellungen = json_decode($einstellungen,true);
    }
    $limitedtosubshop = (int)$einstellungen['felder']['limitedtosubshop'];

    $__tmp = $this->CatchRemoteCommand('data');
    $nummer = '';
    $bisnummer = '';

    if(!empty($__tmp['nummer']))
    {
      $nummer = $__tmp['nummer'];
    }
    if(!empty($__tmp['ab_nummer']))
    {
      $nummer = $__tmp['ab_nummer'];
    }
    if(!empty($__tmp['bis_nummer']))
    {
      $bisnummer = $__tmp['bis_nummer'];
    }

    if($nummer != '')
    {
      if($this->useorderid)
      {
        $iststring = true;
      }else{
        $iststring = false;
        $testbestellungen = $this->adapter->get('orders',  array('limit' => '10', 'sort' => array(array('property' => 'number','direction' => 'DESC'))));
        if(!empty($testbestellungen['data'])){
          foreach ($testbestellungen['data'] as $key => $value) {
            if(((string)(int)($value['number']) != $value['number'])){
              $iststring = true;
            }
          }
        }
      }

      $property = "number";
      if($iststring)$property = "id";
      $filterByOrderStatus = array(
          array(
            'property' => $property,
            'expression' => '>=',
            'value'    =>  $nummer
            ),
          );
      if($bisnummer != ''){
        $filterByOrderStatus[] = array(
            'property' => $property,
            'expression' => '<=',
            'value'    =>  $bisnummer
            );
          if(!$__tmp['storniete_abholen']){
            $filterByOrderStatus[] = array(
            'property' => 'status',
            'expression' => '<>',
            'value'    =>  '4'
            );
          }
      }
    }else{
      // filter nach orderStatusId
      $filterByOrderStatus = array(
          array(
            'property' => 'status',
            'value'    => $this->abzuholenderStatusId
            ),
          );
    }
    if($limitedtosubshop){
      $filterByOrderStatus[] =array('property' => 'shopId', 'expression' => '=','value' => $limitedtosubshop);
    }
    $params = array(
        'filter' => $filterByOrderStatus,
         'sort' => [['property' => 'id','direction' => 'ASC']]
        );
    $result = $this->adapter->get('orders',  $params);
    if(is_string($result) || empty($result['data'])) {
      return 0;
    }
    //$tmp = $this->app->DB->Select("SELECT COUNT(id) FROM auftraege"); // WHERE noch nicht abgeholt
    return count($result['data']);
  }

  //TODO fuer AuftragImport
  public function ImportUpdateAuftrag()
  {
    $tmp = $this->CatchRemoteCommand('data');

    // pruefe ob $tmp[datei] vorhanden wenn nicht lege an sonst update [inhalt] und [checksum]
    $auftrag = $tmp['auftrag'];
    $zahlungok = $tmp['zahlung'];
    $versandok = $tmp['versand'];
    $tracking = $tmp['tracking'];

    /*if($zahlungok=='ok' || $zahlungok=='1')
      $status_zahlung=12;
    else
      $status_zahlung=1;

    if($versandok=='ok' || $versandok=='1')
      $status_versand=7;
    else
      $status_versand=1;*/
    /*
       $date = new DateTime();
       $date->modify('+10 days');
       $date = $date->format(DateTime::ISO8601);
     */
    $result = $this->adapter->put('orders/'.$auftrag, array(
          // 'paymentStatusId' => $status_zahlung,
          'orderStatusId' => $this->abgeschlossenStatusId,//$status_versand,
          'trackingCode' => $tracking
          //'comment' => 'Neuer Kommentar',
          //'transactionId' => '0',
          //   'clearedDate' => $date,
          ));
    $this->ShopwareLog("Abschlussstatusrückmeldung für Auftrag: $auftrag", print_r($result,true));
    //$this->app->DB->Delete("DELETE FROM auftraege WHERE id='$auftrag' LIMIT 1");
    return 'ok';
  }

  //TODO fuer AuftragImport
  public function ImportDeleteAuftrag()
  {
    $tmp = $this->CatchRemoteCommand('data');

    // pruefe ob $tmp[datei] vorhanden wenn nicht lege an sonst update [inhalt] und [checksum]
    $auftrag = $tmp['auftrag'];
    if(!empty($auftrag)){
      $result = $this->adapter->put('orders/' . $auftrag,  array(
        'orderStatusId' => $this->abgeholtStatusId,
      ));
    }
    $this->ShopwareLog("Abholstatusrückmeldung für Auftrag: $auftrag", print_r($result,true));
    //$this->app->DB->Delete("DELETE FROM auftraege WHERE id='$auftrag' LIMIT 1");
    return 'ok';
  }

  public function ImportUpdateZahlungsstatus()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $auftrag = $tmp['auftrag'];
    $status_zahlung = $tmp['zahlungsstatus'];
    switch($status_zahlung)
    {
      case 'bezahlt':
        $status_zahlung = 12;
      break;
      default:
        return 'unbekannt';
      break;
    }
    if(!empty($auftrag)){
      $ret = $this->adapter->put('orders/' . $auftrag,  array(
        'paymentStatusId' => $status_zahlung,
        //'orderStatusId' => 7,//$status_versand,
        //'trackingCode' => $tracking
        //'comment' => $kommentar,
        //'transactionId' => '0',
        //   'clearedDate' => $date,
      ));
    }
    $this->ShopwareLog("Zahlungsstatusrückmeldung für Auftrag: $auftrag", print_r($ret,true));
    if(isset($ret['success']) && $ret['success'] == 1)
    {
      return 'ok';
    }

    return $ret;
  }

  protected function getOrdersByParam($params) {
    $resultOrders = $this->adapter->get('orders', $params);
    if(!is_array($resultOrders)) {
      $resultOrders = json_decode(json_encode($resultOrders), true);
    }
    if(!empty($resultOrders['data'][0])) {
      foreach($resultOrders['data'] as $resultKey => $resultOrder) {
        if(empty($resultOrder['id'])) {
          unset($resultOrders['data'][$resultKey]);
        }
      }
    }

    return $resultOrders;
  }

  protected function getOrderById($orderId) {
    if(empty($orderId)) {
      return '';
    }
    $result = $this->adapter->get('orders/' . $orderId);
    if(!is_array($result)){
      $result = json_decode(json_encode($result), true);
    }
    return $result;
  }

  protected function removeOrderFromOrderList($orderId, $orders) {
    foreach($orders['data'] as $resultKey => $resultOrder) {
      if($resultOrder['id'] == $orderId) {
        unset($orders['data'][$resultKey]);
      }
    }

    return $orders;
  }

  public function ShopwareLog($nachricht, $dump = '')
  {
    if($this->protokoll){
      $this->app->erp->Logfile($nachricht, print_r($dump, true));
    }
  }

  //TODO fuer Auftragimport
  // get checksum list from onlineshop
  public function ImportGetAuftrag()
  {
    $einstellungen = $this->app->DB->Select("SELECT einstellungen_json FROM shopexport WHERE id = '$this->shopid' LIMIT 1");
    if($einstellungen)
    {
      $einstellungen = json_decode($einstellungen,true);
    }
    $benutzergruppenuebernehmen = $einstellungen['felder']['benutzergruppenuebernehmen'];
    $benutzergruppenanlegen = $einstellungen['felder']['benutzergruppenanlegen'];
    $gutscheinalslagerartikel = $einstellungen['felder']['gutscheinalslagerartikel'];
    $limitedtosubshop = (int)$einstellungen['felder']['limitedtosubshop'];
    $getunpaidorders = $einstellungen['felder']['getunpaidorders'];

    $__tmp = $this->CatchRemoteCommand('data');
    $nummer = '';
    $bisnummer = '';

    if(!empty($__tmp['nummer']))
    {
      $nummer = $__tmp['nummer'];
    }
    if(!empty($__tmp['ab_nummer']))
    {
      $nummer = $__tmp['ab_nummer'];
    }
    if(!empty($__tmp['bis_nummer']))
    {
      $bisnummer = $__tmp['bis_nummer'];
    }
    $useorderid = null;
    if($nummer != '')
    {
      if($this->useorderid)
      {
        $iststring = true;
      }else{
        $testbestellungen = $this->adapter->get('orders',  array('limit' => '10', 'sort' => array(array('property' => 'number', 'direction' => 'DESC'))));
        $iststring = false;
        if(!empty($testbestellungen['data'])){
          foreach ($testbestellungen['data'] as $key => $value) {
            if(((string)(int)($value['number']) != $value['number'])){
              $iststring = true;
            }
          }
        }
      }
      $useorderid = $iststring;
      $property = "number";
      if($iststring)
      {
        $property = "id";
      }

      $filterByOrderStatus = array(
          array(
            'property' => $property,
            'expression' => '>=',
            'value'    => $nummer
            ),
          );
        if($bisnummer != ''){
          if(!$__tmp['storniete_abholen']){
            $filterByOrderStatus[] = array(
            'property' => 'status',
            'expression' => '<>',
            'value'    =>  '4'
            );
          }
          $filterByOrderStatus[] = array(
              'property' => $property,
              'expression' => '<=',
              'value'    =>  $bisnummer
              );
        }
    }else{
      // filter nach orderStatusId
      $filterByOrderStatus = array(
          array(
            'property' => 'status',
            'value'    => $this->abzuholenderStatusId
            ),
          );
    }

    if($limitedtosubshop){
      $filterByOrderStatus[] =array('property' => 'shopId', 'expression' => '=','value' => $limitedtosubshop);
    }
    $params = array(
        'limit' => 1,
        'filter' => $filterByOrderStatus 
        );
    // holt immer einen Eintrag ab

    $resultOrders = $this->getOrdersByParam($params);


    $needToCheckMoreOrders = empty($__tmp['nummer']);
    if(empty($resultOrders['data'][0]['id']) && empty($__tmp['nummer'])) {
      $needToCheckMoreOrders = false;
      //if first Order is deleted search for all open Orders
      $countOrders = $this->ImportGetAuftraegeAnzahl();
      if($countOrders > 1) {
        $params['limit'] = $countOrders;
        $resultOrders = $this->getOrdersByParam($params);
      }
    }

    if(!is_array($resultOrders) || !($resultOrders['success'] == 1) || empty($resultOrders['data']))
    {
      return '';
    }

    $resultOrder = reset($resultOrders['data']);
    $warenkorb['auftrag'] = $resultOrder['id'];
    $warenkorb['subshop'] = $resultOrder['shopId'];
    $resultOrders = $this->removeOrderFromOrderList($warenkorb['auftrag'], $resultOrders);

    if(empty($warenkorb['auftrag'])) {
      return '';
    }

    if($needToCheckMoreOrders) {
      $countOrders = $this->ImportGetAuftraegeAnzahl();
      if($countOrders >= 1) {
        $params['limit'] = $countOrders;
        $resultOrders = $this->getOrdersByParam($params);
      }else{
        return '';
      }
    }

    while(true) {
      $result = $this->getOrderById($warenkorb['auftrag']);
      if(is_array($result)) {
        break;
      }

      if(!is_array($result) && !empty($resultOrders['data'])) {
        //check next Order
        $resultOrder = reset($resultOrders['data']);
        $warenkorb['auftrag'] = $resultOrder['id'];
        $warenkorb['subshop'] = $resultOrder['shopId'];
        $resultOrders = $this->removeOrderFromOrderList($warenkorb['auftrag'], $resultOrders);
        continue;
      }

      return '';
    }

    if(empty($result['data']['number'])){
      return '';
    }

    unset($resultOrders);
    $warenkorb['order'] = $result['data'];
    if($benutzergruppenuebernehmen){
      $kundeninfos = $this->adapter->get('customers/'. $result['data']['customerId']);
      $allegruppen = $this->adapter->get('customerGroups/');
      $cgruppen = count($allegruppen['data']);
      for ($i=0; $i < $cgruppen; $i++) {
        if($allegruppen['data'][$i]['key'] == $kundeninfos['data']['groupKey']){
          $warenkorb['benutzergruppe'] = $allegruppen['data'][$i]['name'];
          $warenkorb['benutzergruppekuerzel'] = $kundeninfos['data']['groupKey'];
          $warenkorb['kundengruppe'] = $kundeninfos['data']['groupKey'];
        }
      }
      if($benutzergruppenanlegen){
        $warenkorb['benutzergruppeanlegen'] = true;
      }
    }
    //$warenkorb['result'] = $result;
    $warenkorb['gesamtsumme'] = $result['data']['invoiceAmount'];

    if(!empty($result['data']['currency']) &&
      ($currencyUpper = strtoupper($result['data']['currency'])) !== 'EUR') {
      $warenkorb['waehrung'] = $currencyUpper;
    }

    $warenkorb['transaktionsnummer'] = $result['data']['transactionId'];
    $warenkorb['onlinebestellnummer'] = $result['data']['number'];
    if(empty($result['data']['invoiceShippingTaxRate'])){
      $warenkorb['versandkostennetto'] = $result['data']['invoiceShippingNet'];
    }else{
      $warenkorb['versandkostenbrutto'] = (float)$result['data']['invoiceShipping'];
    }
    if($useorderid !== null)
    {
      $warenkorb['useorderid'] = $useorderid;
    }

    $warenkorb['freitext'] = $result['data']['customerComment'];

    if($result['data']['billing']['company']==''){
      $warenkorb['name'] = $result['data']['billing']['firstName'] . ' ' . $result['data']['billing']['lastName'];
    }
    else {
      $warenkorb['name'] = $result['data']['billing']['company'];
      $warenkorb['ansprechpartner'] = $result['data']['billing']['firstName'].' '.$result['data']['billing']['lastName'];
    }

    if($result['data']['billing']['salutation']=='mr'){
      $warenkorb['anrede'] = 'herr';
    }

    if($result['data']['billing']['salutation']=='mrs'){
      $warenkorb['anrede'] = 'frau';
    }

    if($result['data']['billing']['salutation']=='ms'){
      $warenkorb['anrede'] = 'frau';
    }

    if($result['data']['billing']['company']!=''){
      $warenkorb['anrede'] = 'firma';
    }

    $warenkorb['strasse'] = $result['data']['billing']['street'];
    $warenkorb['plz'] = $result['data']['billing']['zipCode'];
    $warenkorb['ort'] = $result['data']['billing']['city'];
    $warenkorb['land'] = $result['data']['billing']['country']['iso'];
    $warenkorb['email'] = $result['data']['customer']['email'];
    $geburtstag = $result['data']['customer']['birthday'];
    $warenkorb['geburtstag'] = substr($geburtstag,0,10);

    if(!$warenkorb['subshop'])$warenkorb['subshop'] = $result['data']['customer']['shopId'];
    $warenkorb['affiliate_ref'] = $result['data']['customer']['affiliate'];
    $warenkorb['abteilung'] = $result['data']['billing']['department'];
    $warenkorb['steuerfrei'] = $result['data']['taxFree'];

    $warenkorb['adresszusatz'] = $result['data']['billing']['additionalAddressLine1'];
    $warenkorb['unterabteilung'] = $result['data']['billing']['additionalAddressLine2'];

    //10 = Komplett in Rechnung gestellt
    //12 = Komplett bezahlt
    //18 = Reserviert ????
    //31 = Der Kredit wurde vorlaeufig akzeptiert.
    //32 = Der Kredit wurde genehmigt.
    //33 = Die Zahlung wurde von der Hanseatic Bank angewiesen.
    if($result['data']['paymentStatus']['id']==12)
    {
      $warenkorb['vorabbezahltmarkieren']=1;
    } else {
      $warenkorb['vorabbezahltmarkieren']=0;
      if($getunpaidorders){
        $warenkorb['auftragsstatus']='unbezahlt';
      }
    }

    $warenkorb['zahlungsweise'] = $result['data']['payment']['name'];
    $warenkorb['lieferung'] = $result['data']['dispatch']['name'];
    $warenkorb['bestelldatum'] = substr($result['data']['orderTime'],0,10);

    $warenkorb['ustid'] = $result['data']['billing']['vatId'];
    $warenkorb['telefon'] = $result['data']['billing']['phone'];
    if(isset($result['data']['billing']['fax']))$warenkorb['telefax'] = $result['data']['billing']['fax'];
    $warenkorb['bundesstaat'] = isset($result['data']['billing']['state']) && isset($result['data']['billing']['state']['shortCode'])
      ?(String)$result['data']['billing']['state']['shortCode']:'';

    if($result['data']['shipping']['company']=="")
      $warenkorb2['lieferadresse_name'] = $result['data']['shipping']['firstName'].' '.$result['data']['shipping']['lastName'];
    else {
      $warenkorb2['lieferadresse_name'] = $result['data']['shipping']['company'];
      $warenkorb2['lieferadresse_ansprechpartner'] = $result['data']['shipping']['firstName'].' '.$result['data']['shipping']['lastName'];
    }

    $warenkorb2['lieferadresse_strasse'] = $result['data']['shipping']['street'];
    $warenkorb2['lieferadresse_plz'] = $result['data']['shipping']['zipCode'];
    $warenkorb2['lieferadresse_ort'] = $result['data']['shipping']['city'];
    $warenkorb2['lieferadresse_land'] = $result['data']['shipping']['country']['iso'];
    $warenkorb2['lieferadresse_abteilung'] = $result['data']['shipping']['department'];

    $warenkorb2['lieferadresse_unterabteilung'] = $result['data']['shipping']['additionalAddressLine1'];
    $warenkorb2['lieferadresse_adresszusatz'] = $result['data']['shipping']['additionalAddressLine2'];
    $warenkorb2['lieferadresse_bundesstaat'] = isset($result['data']['shipping']['state']) && isset($result['data']['shipping']['state']['shortCode'])
      ?(String)$result['data']['shipping']['state']['shortCode']:'';
    if($warenkorb2['lieferadresse_name']!=$warenkorb['name'] ||
        (isset($warenkorb2['lieferadresse_ansprechpartner']) && $warenkorb2['lieferadresse_ansprechpartner']!=$warenkorb['ansprechpartner']) ||
        $warenkorb2['lieferadresse_strasse']!=$warenkorb['strasse'] ||
        $warenkorb2['lieferadresse_plz']!=$warenkorb['plz'] ||
        $warenkorb2['lieferadresse_ort']!=$warenkorb['ort'] ||
        $warenkorb2['lieferadresse_land']!=$warenkorb['land'] ||
        $warenkorb2['lieferadresse_bundesland']!=$warenkorb['bundesstaat'] ||
        $warenkorb2['lieferadresse_abteilung']!=$warenkorb['abteilung'])
    {
      $warenkorb['abweichendelieferadresse']="1";
      $warenkorb['lieferadresse_name']  = $warenkorb2['lieferadresse_name'] ;
      if(isset($warenkorb2['lieferadresse_ansprechpartner']))
      {
        $warenkorb['lieferadresse_ansprechpartner'] = $warenkorb2['lieferadresse_ansprechpartner'];
      }
      $warenkorb['lieferadresse_strasse'] = $warenkorb2['lieferadresse_strasse'];
      $warenkorb['lieferadresse_plz'] = $warenkorb2['lieferadresse_plz'];
      $warenkorb['lieferadresse_ort'] = $warenkorb2['lieferadresse_ort'];
      $warenkorb['lieferadresse_land'] = $warenkorb2['lieferadresse_land'];
      $warenkorb['lieferadresse_abteilung'] = $warenkorb2['lieferadresse_abteilung'];
      $warenkorb['lieferadresse_unterabteilung'] = $warenkorb2['lieferadresse_unterabteilung'];
      $warenkorb['lieferadresse_adresszusatz'] = $warenkorb2['lieferadresse_adresszusatz'];
      $warenkorb['lieferadresse_bundesstaat'] = $warenkorb2['lieferadresse_bundesstaat'];
    } 

    //articlelist
    //articleid
    //quantity
    $portosteuer = '';
    $cdetails = !empty($result['data']['details'])?count($result['data']['details']):0;
    $taxRates = array();
    $rates = null;
    for($i=0; $i < $cdetails; $i++)
    {
      //Vielleicht besser über taxId?
      if($result['data']['details'][$i]['taxRate'] == 0){
        $steuerart = 'befreit';
        if($portosteuer === ''){
          $portosteuer = 'befreit';
        }
      }elseif($result['data']['details'][$i]['taxRate'] < 10){
        $steuerart = 'ermaessigt';
        if($portosteuer !== 'normal'){
          $portosteuer = 'ermaessigt';
        }
      }else{
        $steuerart = 'normal';
        $portosteuer = 'normal';
      }
      if((float)$result['data']['details'][$i]['taxRate'] > 0){
        if(!in_array($result['data']['details'][$i]['taxRate'],$taxRates) && is_numeric($result['data']['details'][$i]['taxRate']))
        {
          $taxRates[] = (float)$result['data']['details'][$i]['taxRate'];
        }
      }
      $articlearray[] = array('articleid'=>$result['data']['details'][$i]['articleNumber'],
          'name'=>$result['data']['details'][$i]['articleName'],
          'price'=>$result['data']['details'][$i]['price'],
          'quantity'=>$result['data']['details'][$i]['quantity'],
          'umsatzsteuer' => $steuerart
          );
      //$result['data']['details'][$i]['mode'] <- kann der Wert mit Gewissheit für Gutscheine verwendet werden? Wäre mitunter besser als stur vom Preis auszugehen

      $rates[count($articlearray)-1] = $result['data']['details'][$i]['taxRate'];
      if(!$gutscheinalslagerartikel && $result['data']['details'][$i]['price'] < 0)
      {
        $articlearray[count($articlearray)-1]['lagerartikel'] = 0;
      }
    }
    //$warenkorb['taxRates'] = $taxRates;
    if(count($taxRates) > 1)
    {
      rsort($taxRates);
      $warenkorb['steuersatz_normal'] = $taxRates[0];
      $warenkorb['steuersatz_ermaessigt'] = $taxRates[count($taxRates) - 1];
      if($warenkorb['versandkostenbrutto'] > 0 && (float)$result['data']['invoiceShippingNet'] > 0)
      {
        $satz = round(100*(($warenkorb['versandkostenbrutto']/(float)$result['data']['invoiceShippingNet']) - 1));
        if($satz == round($warenkorb['steuersatz_normal']))
        {
          $portosteuer = 'normal';
        }elseif($satz == round($warenkorb['steuersatz_ermaessigt']))
        {
          $portosteuer = 'ermaessigt';
        }
      }
      foreach($rates as $k => $v)
      {
        if($v > 0 && ($v == $warenkorb['steuersatz_ermaessigt']))
        {
          $articlearray[$k]['umsatzsteuer'] = 'ermaessigt';
        }
      }
    }elseif(count($taxRates) > 0)
    {
      $steuersatz_normal = (float)$this->app->erp->Firmendaten('steuersatz_normal');
      $steuersatz_ermaessigt = (float)$this->app->erp->Firmendaten('steuersatz_ermaessigt');
      $_steuersatz_normal = $steuersatz_normal;
      $_steuersatz_ermaessigt = $steuersatz_ermaessigt;
      $this->app->erp->RunHook('shopimport_getorder_taxes', 3,$warenkorb,  $_steuersatz_normal, $_steuersatz_ermaessigt);
      if(is_numeric($_steuersatz_ermaessigt) && $_steuersatz_ermaessigt > 0)
      {
        $steuersatz_ermaessigt = $_steuersatz_ermaessigt;
      }
      if(is_numeric($_steuersatz_normal) && $_steuersatz_normal > 0)
      {
        $steuersatz_normal = $_steuersatz_normal;
      }
      $portosteuerchanged = false;
      if($taxRates[0] == $steuersatz_normal)
      {
        $warenkorb['steuersatz_normal'] = $steuersatz_normal;
        if($warenkorb['versandkostenbrutto'] > 0 && (float)$result['data']['invoiceShippingNet'] > 0)
        {
          $satz = round(100*($warenkorb['versandkostenbrutto']/(float)$result['data']['invoiceShippingNet'] -1));
          if($satz == round($warenkorb['steuersatz_normal']))
          {
            $portosteuer = 'normal';
            $portosteuerchanged = true;
          }
        }
      }elseif($taxRates[0] == $steuersatz_ermaessigt)
      {
        $warenkorb['steuersatz_ermaessigt'] = $steuersatz_ermaessigt;
        if($warenkorb['versandkostenbrutto'] > 0 && (float)$result['data']['invoiceShippingNet'] > 0)
        {
          $satz = round(100*(($warenkorb['versandkostenbrutto']/(float)$result['data']['invoiceShippingNet']) - 1));
          if($satz == round($warenkorb['steuersatz_ermaessigt']))
          {
            $portosteuer = 'ermaessigt';
            $portosteuerchanged = true;
          }
        }
      }
      elseif($taxRates[0] > 10)
      {
        $warenkorb['steuersatz_normal'] = $taxRates[0];
      }elseif($taxRates[0] > 0){
        $warenkorb['steuersatz_ermaessigt'] = $taxRates[0];
      }
      if(isset($warenkorb['versandkostenbrutto']) && !$portosteuerchanged && (float)$result['data']['invoiceShippingNet'] > 0)
      {
        //auf ,5 genau runden
        $steuersatz = round(round(2*100*(($warenkorb['versandkostenbrutto']/(float)$result['data']['invoiceShippingNet'])-1))/2,1);
        if(is_numeric($steuersatz) && $steuersatz > 0){
          if(!empty($warenkorb['steuersatz_normal']) && empty($warenkorb['steuersatz_ermaessigt'])){
            if($steuersatz < $warenkorb['steuersatz_normal']){
              $portosteuer = 'ermaessigt';
              $warenkorb['steuersatz_ermaessigt'] = $steuersatz;
            }elseif($steuersatz == $warenkorb['steuersatz_normal']){
              $portosteuer = 'normal';
            }else{
              $warenkorb['steuersatz_ermaessigt'] = $warenkorb['steuersatz_normal'];
              $warenkorb['steuersatz_normal'] = $steuersatz;
              $portosteuer = 'normal';
            }
          }elseif(!empty($warenkorb['steuersatz_ermaessigt']) && empty($warenkorb['steuersatz_normal'])){
            if($steuersatz < $warenkorb['steuersatz_ermaessigt']){
              $portosteuer = 'ermaessigt';
              $warenkorb['steuersatz_normal'] = $warenkorb['steuersatz_ermaessigt'];
              $warenkorb['steuersatz_ermaessigt'] = $steuersatz;
            }elseif($steuersatz == $warenkorb['steuersatz_ermaessigt']){
              $portosteuer = 'ermaessigt';
            }else{
              $warenkorb['steuersatz_normal'] = $steuersatz;
              $portosteuer = 'normal';
            }
          }
        }
      }
    }

    if(!empty($portosteuer)){
      $warenkorb['portosteuersatz'] = $portosteuer;
    }

    foreach($articlearray as $k => $v)
    {
      if(!empty($warenkorb['steuersatz_ermaessigt']) && !empty($rates[$k]) && $warenkorb['steuersatz_ermaessigt'] == $rates[$k])
      {
        $articlearray[$k]['umsatzsteuer'] = 'ermaessigt';
      }elseif(!empty($warenkorb['steuersatz_normal']) && !empty($rates[$k]) && $warenkorb['steuersatz_normal'] == $rates[$k])
      {
        $articlearray[$k]['umsatzsteuer'] = 'normal';
      }elseif(!empty($rates[$k]))
      {
        $articlearray[$k]['steuersatz'] = $rates[$k];
      }

      $articlearray[$k]['price'] = number_format($v['price'],2,'.','');
      if((isset($result['data']['net']) && $result['data']['net'] == 1))
      {
        $articlearray[$k]['price_netto'] = number_format($v['price'],2,'.','');
      }
    }

    if($this->useorderid){
      $warenkorb['onlinebestellnummerueberschreiben'] = $warenkorb['auftrag'];
    }

    $warenkorb['articlelist']=$articlearray;
    $warenkorb['result']=base64_encode(json_encode($result));
    $warenkorb = $this->processCart($warenkorb,$result);
    //$warenkorb['paymentStatus']=$result['data']['paymentStatus'];
    $tmp[0]['id'] = $warenkorb['auftrag'];
    $tmp[0]['sessionid'] = '';
    $tmp[0]['logdatei'] = '';
    $tmp[0]['warenkorb'] = base64_encode(serialize($warenkorb));
    $tmp[0]['warenkorbjson'] = base64_encode(json_encode($warenkorb));

    return $tmp;
  }

  /**
   * Dummy function. Allows for manipulation of the shopping cart without impeding the updateabilty
   *
   * @param $cart
   * @param $order
   *
   * @return mixed
   */
  protected function processCart($cart,$order){
    return $cart;
  }

  //TODO fuer Artikelexport
  // get checksum list from onlineshop
  public function ImportGetList()
  {
    $tmp = $this->app->DB->SelectArr("SELECT artikel,checksum FROM artikel");
    return $tmp;
  }


  // get checksum list from the files 
  public function ImportGetFileList()
  {
    $tmp = $this->app->DB->SelectArr("SELECT datei, checksum FROM datei");
    return $tmp;
  }

  // get checksum list from the files 
  public function ImportGetFileListArticle()
  {
    $tmp = $this->CatchRemoteCommand('data');
    $artikel = $tmp['artikel'];

    $tmp = $this->app->DB->SelectArr("SELECT d.datei, d.checksum FROM datei d, datei_stichwoerter ds WHERE articleID=$artikel");
    //$tmp = $this->app->DB->SelectArr("SELECT d.datei, d.checksum FROM datei d, datei_stichwoerter ds WHERE d.datei=ds.datei AND ds.artikel=$artikel");
    return $tmp;
  }

  public function ImportAuth()
  {
    $tmp = $this->CatchRemoteCommand('data');
    if($tmp)
    {
      $params = array(
          'limit' => 1
          );
      $result = $this->adapter->get('orders',$params);
      if($result['success'] == 1){
        $result['version'] = '1.1';
        $result['erlaubtefunktionen']['auftragnummer'] = 1;
        $result['erlaubtefunktionen']['auftragabnummer'] = 0;
        $result['erlaubtefunktionen']['subshops'] = 1;
        $result['erlaubtefunktionen']['eigenschaften'] = 1;
        $result['erlaubtefunktionen']['freifelder'] = 1;
        $result['erlaubtefunktionen']['varianten'] = 1;
        $result['erlaubtefunktionen']['zeitbereich'] = 0;
        $result['erlaubtefunktionen']['rabattartikel'] = 0;
        $result['erlaubtefunktionen']['artikelbilder'] = 0;
        $result['erlaubtefunktionen']['kategorien'] = 0;


        if($tmp === 'info')
        {
          $result = null;
          $erg = $this->adapter->get('shops');
          if(!empty($erg['data']) && is_array($erg['data']))
          {
            foreach($erg['data'] as $store)
            {
              $result['subshops'][] = array('id'=>$store['id'],'name'=>$store['name'],'aktiv'=>$store['active']);
            }
          }
          return $result;
        }
        return "success";
      }
      if($result){
        return "failed ".$result;
      }
      return "failed Keine Verbindung zur API. Falsche URL?";
    }
    return "success";
  }


  /**
   * @param array $shopArr
   * @param array $postData
   *
   * @return array
   */
  public function updateShopexportArr($shopArr, $postData)
  {
    $shopArr['stornoabgleich'] = 1;
    $shopArr['anzgleichzeitig'] = 1;
    $shopArr['demomodus'] = 0;

    return $shopArr;
  }
  
  /**
   * @param array $postData
   *
   * @return array
   */
  public function updatePostDataForAssistent($postData)
  {
    if(!empty($this->useDigestAuth)) {
      $postData['useDigestAuth'] = $this->useDigestAuth;
    }

    return $postData;
  }

  /**
   * @return JsonResponse|null
   */
  public function AuthByAssistent()
  {
    $ImportShopwareApiUrl = $this->app->Secure->GetPOST('ImportShopwareApiUrl');
    $ImportShopwareApiUser = $this->app->Secure->GetPOST('ImportShopwareApiUser');
    $ImportShopwareKey = $this->app->Secure->GetPOST('ImportShopwareKey');

    $step = (int)$this->app->Secure->GetPOST('step');

    if($step <= 1) {
      if(empty($ImportShopwareApiUrl)){
        return new JsonResponse(['error' => 'Bitte die URL des Shops angeben.'], JsonResponse::HTTP_BAD_REQUEST);
      }
      if(empty($ImportShopwareApiUser)){
        return new JsonResponse(['error' => 'Bitte den Benutzernamen angeben'], JsonResponse::HTTP_BAD_REQUEST);
      }
      if(empty($ImportShopwareKey)){
        return new JsonResponse(['error' => 'Bitte das Passwort angeben'], JsonResponse::HTTP_BAD_REQUEST);
      }
      $useDigestAuth = false;
      $adapter =
        new Shopimporter_Shopware_Adapter(
          $ImportShopwareApiUrl, $ImportShopwareApiUser, $ImportShopwareKey, false
        );
      $params = array(
        'limit' => 1
      );
      $result = $adapter->get('orders',$params);
      if(empty($result['success'])){
        $adapter =
          new Shopimporter_Shopware_Adapter(
            $ImportShopwareApiUrl, $ImportShopwareApiUser, $ImportShopwareKey, true
          );
        $result = $adapter->get('orders',$params);
        $useDigestAuth = true;
      }

      if(empty($result['success'])){
        if($result){
          return new JsonResponse(['error' => $result], JsonResponse::HTTP_BAD_REQUEST);
        }
        return new JsonResponse(
          ['error' => "Keine Verbindung zur API. Falsche URL?"
        ], JsonResponse::HTTP_BAD_REQUEST);
      }
      $this->useDigestAuth = $useDigestAuth;
    }

    return null;
  }

  /**
   * @return string
   */
  public function getClickByClickHeadline()
  {
    return 'Bitte im Shopware Backend einen eigenen Benutzer für Xentral anlegen und diese 
      Zugangsdaten hier eintragen.';
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
            'name' => 'ImportShopwareApiUrl',
            'validation' => true,
          ],
        ],
      ],
      [
        'id' => 1,
        'name' => 'username',
        'inputs' => [
          [
            'label' => 'Benutzername aus Shopware',
            'type' => 'text',
            'name' => 'ImportShopwareApiUser',
            'validation' => true,
          ],
        ],
      ],
      [
        'id' => 2,
        'name' => 'password',
        'inputs' => [
          [
            'label' => 'Passwort aus Shopware',
            'type' => 'password',
            'name' => 'ImportShopwareKey',
            'validation' => true,
          ],
        ],
      ],
    ];
  }

}

