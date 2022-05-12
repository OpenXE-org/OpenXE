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
use Xentral\Modules\Onlineshop\Data\ShopConnectorResponseInterface;

class Remote
{
  /** @var ApplicationCore $app */
  public $app;
  public function __construct($app)
  {
    $this->app=$app;
  }

  /**
   * @param int       $shopId
   * @param null|bool $extra
   *
   * @return mixed
   * @throws Exception
   */
  public function RemoteConnection($shopId, $extra = null)
  {
    $ret = $this->RemoteCommand($shopId,'auth', $extra);
    if($ret !== 'success' && empty($extra) && method_exists($this->app->erp, 'setSystemHealth')) {
      $this->app->erp->setSystemHealth(
        'shopexport',
        'auth',
        'warning',
        'Verbindung zu '.
        $this->app->DB->Select(
          sprintf(
            'SELECT bezeichnung FROM shopexport WHERE id = %d',
            $shopId
          )
        ).' fehlgeschlagen.'
      );
      try {
        /** @var Systemhealth $systemhealth */
        $systemhealth = $this->app->erp->LoadModul('systemhealth');
        if($systemhealth !== null && method_exists($systemhealth, 'createEvent')){
          $systemhealth->createEvent('shopexport', 'auth', 'warning', 'shopexport', $shopId);
        }
      }
      catch (Exception $d) {

      }
    }
    return $ret;
  }

  /**
   * @param int $shopId
   *
   * @return mixed
   * @throws Exception
   */
  public function RemoteGetUpdateArticleList($shopId)
  {
    return $this->RemoteCommand($shopId,'getlist');
  }

  /**
   * @param int $shopId
   *
   * @return mixed
   * @throws Exception
   */
  public function RemoteGetFileList($shopId)
  {
    return $this->RemoteCommand($shopId,'getfilelist');
  }

  /**
   * @param int        $shopId
   * @param int|string $articleId
   *
   * @return mixed
   * @throws Exception
   */
  public function RemoteGetFileListArticle($shopId, $article)
  {
    $data['artikel'] = $article;
    
    return $this->RemoteCommand($shopId,'getfilelistarticle', $data);
  }

  /**
   * @param int          $shopId
   * @param array|string $data
   *
   * @return mixed|string
   * @throws Exception
   */
  public function RemoteGetAuftraegeAnzahl($shopId,$data = '')
  {
    if(!empty($data))
    {
      return $this->RemoteCommand($shopId,'getauftraegeanzahl', $data);
    }
    $data = array();
    $shopexportArr = $this->app->DB->SelectRow(sprintf('SELECT * FROM shopexport WHERE id = %d LIMIT 1', $shopId));
    $holealle = $shopexportArr['holealle'];
    $holeallestati = $shopexportArr['holeallestati'];
    $zeitraum = [
      'datumvon'=>$shopexportArr['datumvon'],
      'datumbis'=>$shopexportArr['datumbis'],
      'tmpdatumvon'=>$shopexportArr['tmpdatumvon'],
      'tmpdatumbis'=>$shopexportArr['tmpdatumbis'],
      'anzgleichzeitig'=>$shopexportArr['anzgleichzeitig'],
    ];
    if(!empty($zeitraum))
    {
      $data['datumvon'] = $zeitraum['datumvon'];
      $data['datumbis'] = $zeitraum['datumbis'];
      //if(strtotime($zeitraum[0]['tmpdatumbis']) > 0)$data['datumbis'] = $zeitraum[0]['tmpdatumbis'];
      //if(strtotime($zeitraum[0]['tmpdatumvon']) > 0)$data['datumvon'] = $zeitraum[0]['tmpdatumbis'];
      if($data['datumvon'] === null || $data['datumvon'] === '0000-00-00 00:00:00'|| strtotime($data['datumvon'])<=0) {
        $data['datumvon'] = '2000-01-01 00:00:00';
      }
      if($data['datumbis'] === null || $data['datumbis'] === '0000-00-00 00:00:00'|| strtotime($data['datumbis'])<=0) {
        $data['datumbis'] = date('Y-m-d H:i:s');
        $data['datumbis_utc'] = $this->app->DB->Select('select date_sub(UTC_TIMESTAMP, INTERVAL 0 MINUTE)');
      }
      $data['anzgleichzeitig'] = $zeitraum[0]['anzgleichzeitig'];
      if($holeallestati && $data['anzgleichzeitig'] > 1)
      {
        $data['holeallestati'] = 1;
      }
    }
    if($holealle)
    {
      $ab_nummer = $shopexportArr['ab_nummer'];
      $data = array('ab_nummer' => $ab_nummer);
      if($holeallestati)
      {
        $data['holeallestati'] = 1;
      }

      return $this->RemoteCommand($shopId,'getauftraegeanzahl', $data);
    }

    return $this->RemoteCommand($shopId,'getauftraegeanzahl');
  }

  /**
   * @param int $shopId
   * @param string $nummer
   *
   * @return mixed|string
   * @throws Exception
   */
  public function RemoteGetAuftraegeAnzahlNummer($shopId, $nummer)
  {
    $data = array('nummer' => $nummer);

    return $this->RemoteCommand($shopId,'getauftraegeanzahl', $data);
  }

  /**
   * @param int        $shopId
   * @param null|array $data
   *
   * @return mixed|string
   * @throws Exception
   */
  public function RemoteGetAuftrag($shopId, $data = null)
  {
    if(!empty($data)) {
      return $this->RemoteCommand($shopId,'getauftrag', $data);
    }
    $data = array();

    $shopexportArr = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM shopexport WHERE id = %d LIMIT 1',
        $shopId
      )
    );
    $holealle = $shopexportArr['holealle'];
    $holeallestati = $shopexportArr['holeallestati'];
    $zeitraum = [
      'datumvon'=>$shopexportArr['datumvon'],
      'datumbis'=>$shopexportArr['datumbis'],
      'tmpdatumvon'=>$shopexportArr['tmpdatumvon'],
      'tmpdatumbis'=>$shopexportArr['tmpdatumbis'],
      'anzgleichzeitig'=>$shopexportArr['anzgleichzeitig'],
    ];
    if(!empty($zeitraum)) {
      $data['datumvon'] = $zeitraum['datumvon'];
      $data['datumbis'] = $zeitraum['datumbis'];
      if($zeitraum['tmpdatumbis'] !== null && $zeitraum['tmpdatumbis'] !== '0000-00-00 00:00:00' &&
        strtotime($zeitraum['tmpdatumbis']) > 0) {
        $data['datumbis'] = $zeitraum['tmpdatumbis'];
      }
      //if(strtotime($zeitraum[0]['tmpdatumvon']) > 0)$data['datumvon'] = $zeitraum[0]['tmpdatumbis'];
      if($data['datumvon'] === null || $data['datumvon'] === '0000-00-00 00:00:00' || strtotime($data['datumvon'])<=0) {
        $data['datumvon'] = '2000-01-01 00:00:00';
      }
      if($data['datumbis'] === null || $data['datumbis'] === '0000-00-00 00:00:00' || strtotime($data['datumbis'])<=0) {
        $data['datumbis'] = date('Y-m-d H:i:s');
        $data['datumbis_utc'] = $this->app->DB->Select('select date_sub(UTC_TIMESTAMP, INTERVAL 0 MINUTE)');
      }
      $data['anzgleichzeitig'] = $zeitraum['anzgleichzeitig'];
      if($data['anzgleichzeitig'] > 1 && $shopexportArr['einzelsync']) {
        $data['anzgleichzeitig'] = 1;
      }
      if($holeallestati) {
        $data['holeallestati'] = 1;
      }
    }
    if($holealle) {
      $ab_nummer = $shopexportArr['ab_nummer'];
      $data['ab_nummer'] = $ab_nummer;
      if($holeallestati) {
        $data['holeallestati'] = 1;
      }

      return $this->RemoteCommand($shopId,'getauftrag', $data);
    }
    if(!empty($zeitraum)) {
      return $this->RemoteCommand($shopId,'getauftrag', $data);
    }
    
    return $this->RemoteCommand($shopId,'getauftrag');
  }

  /**
   * @param int    $shopId
   * @param string $nummer
   *
   * @return mixed|string
   * @throws Exception
   */
  public function RemoteGetAuftragNummer($shopId, $nummer)
  {
    $holealle = $this->app->DB->Select("SELECT holealle FROM shopexport WHERE id = '$shopId' LIMIT 1");
    if($holealle)
    {
      $data = array('nummer' => $nummer);

      return $this->RemoteCommand($shopId,'getauftrag', $data);
    }

    return '';
  }

  /**
   * @param int $shopId
   *
   * @return mixed
   * @throws Exception
   */
  public function RemoteSendExportlink($shopId)
  {
    // passwort erzeugen , daten verschluesseln, wenn passwort neu link an kunden senden
    // alternativ artikel umfrage
    //    $all = $this->app->DB->SelectArr("SELECT * FROM artikelgruppen WHERE shop='$id'");

    // alle artikelid = 38 die in einem auftrag sind


    // usb90key
    $artikelid= 38;

    $all = $this->app->DB->SelectArr("SELECT a.id as auftrag, a.adresse as adresse  
        FROM auftrag_position ap 
        LEFT JOIN auftrag a ON a.id=ap.auftrag 
        WHERE ap.artikel='$artikelid' AND ap.geliefert_menge < ap.menge AND a.status!='storniert'
        AND a.status!='abgeschlossen'");
    // mail mit url senden
    if(empty($all)) {
      return '';
    }
    $loop = 0;
    $call = count($all);
    for($i=0;$i<$call;$i++)
    {
      $auftragid = $all[$i]['auftrag'];
      $adresse = $all[$i]['adresse'];

      $grund = 'artikel';

      $check = $this->app->DB->Select("SELECT reg FROM exportlink_sent WHERE adresse='$adresse' AND objekt='$artikelid' AND ident='$auftragid' LIMIT 1");

      if($check=='')
      {
        $checkreg = 1;
        while($checkreg!='')
        {
          $token1 = md5(uniqid(rand(), true));
          $token2 = md5(uniqid(rand(), true));

          $token3 = md5(uniqid(rand(), true));
          $token4 = md5(uniqid(rand(), true));

          $zufall = $token1 . '-' . $token2;
          $zufall = md5($zufall);

          $zufall2 = $token3 . '-' . $token4;
          $zufall2 = md5($zufall2);

          $reg = md5($zufall2.$zufall);

          $checkreg = $this->app->DB->Select("SELECT reg FROM exportlink_sent WHERE reg='$reg' LIMIT 1");
        }

        // pruefen ob es zahl schon gibt sonst nochmal

        $data[$loop]['reg'] = $reg;
        $data[$loop]['grund'] = $grund;
        $data[$loop]['objekt'] = $artikelid; // artikel id
        $data[$loop]['ident'] = $auftragid;//
        $loop++;

        $this->app->DB->Insert("INSERT INTO exportlink_sent (reg,grund,objekt,ident,adresse,datum) VALUES ($reg','$grund','$artikelid','$auftragid','$adresse',NOW())");
      }
    }

    return $this->RemoteCommand($shopId,'exportlink',$data);
  }

  /**
   * @param int $shopId
   *
   * @return mixed
   * @throws Exception
   */
  public function RemoteSendNavigation($shopId)
  {
    //$data[0] = array('aasas','asddd');

    $all = $this->app->DB->SelectArr("SELECT * FROM shopnavigation WHERE shop='$shopId'");
    $call = $all?count($all):0;
    $data = $call > 0?array():null;
    for($i=0;$i<$call;$i++)
    {
      $data[$i]['id'] = $all[$i]['id'];
      $data[$i]['bezeichnung'] = $all[$i]['bezeichnung'];
      $data[$i]['position'] = $all[$i]['position'];
      $data[$i]['parent'] = $all[$i]['parent'];
      $data[$i]['bezeichnung_en'] = $all[$i]['bezeichnung_en'];
      $data[$i]['plugin'] = $all[$i]['plugin'];
      $data[$i]['pluginparameter'] =  $all[$i]['pluginparameter'];
      $data[$i]['target'] =  $all[$i]['target'];
    }

    return $this->RemoteCommand($shopId,'navigation',$data);
  }

  /**
   * @param int $shopId
   *
   * @return mixed|string
   * @throws Exception
   */
  public function RemoteSendArtikelgruppen($shopId)
  {
    $all = $this->app->DB->SelectArr("SELECT id, bezeichnung,bezeichnung_en,beschreibung_de,beschreibung_en 
      FROM artikelgruppen WHERE shop='$shopId'");
    if(empty($all)) {
      return '';
    }

    return $this->RemoteCommand($shopId,'artikelgruppen',$all);
  }

  /**
   * @param int $shopId
   *
   * @return mixed
   * @throws Exception
   */
  public function RemoteSendInhalt($shopId)
  {
    $all = $this->app->DB->SelectArr("SELECT * FROM inhalt WHERE shop='$shopId' AND aktiv=1");
    if(empty($all)) {
      return '';
    }
    $call = count($all);

    $data = array();
    for ($i = 0; $i < $call; $i++) {
      $data[$i]['sprache'] = $all[$i]['sprache'];
      $data[$i]['inhalt'] = $all[$i]['inhalt'];
      $data[$i]['kurztext'] = $all[$i]['kurztext'];
      $data[$i]['html'] = $all[$i]['html'];
      $data[$i]['title'] = $all[$i]['title'];
      $data[$i]['description'] = $all[$i]['description'];
      $data[$i]['keywords'] = $all[$i]['keywords'];
      $data[$i]['inhaltstyp'] = $all[$i]['inhaltstyp'];
      $data[$i]['template'] = $all[$i]['template'];
      $data[$i]['finalparse'] = $all[$i]['finalparse'];
      $data[$i]['navigation'] = $all[$i]['navigation'];
      $data[$i]['sichtbarbis'] = $all[$i]['sichtbarbis'];
      $data[$i]['datum'] = $all[$i]['datum'];
      $data[$i]['aktiv'] = $all[$i]['aktiv'];
    }

    return $this->RemoteCommand($shopId, 'inhalt', $data);
  }

  /**
   * @param int $shopId
   *
   * @return mixed
   * @throws Exception
   */
  public function RemoteSendArtikelArtikelgruppen($shopId)
  {
    $all = $this->app->DB->SelectArr('SELECT * FROM artikel_artikelgruppe');
    $call = $all?count($all):0;
    if($call > 0){
      $data = array();
      for ($i = 0; $i < $call; $i++) {
        $data[$i]['id'] = $all[$i]['id'];
        $data[$i]['artikel'] = $all[$i]['artikel'];
        $data[$i]['artikelgruppe'] = $all[$i]['artikelgruppe'];
        $data[$i]['position'] = $all[$i]['position'];
      }

      return $this->RemoteCommand($shopId, 'artikelartikelgruppen', $data);
    }

    return '';
  }

  /**
   * @param int    $shopId
   * @param string $tabelle
   * @param int    $intid
   * @param int    $intid2
   *
   * @return mixed
   */
  public function GetShopexportMappingExt($shopId, $tabelle, $intid, $intid2 = 0)
  {
    return $this->app->DB->Select("SELECT extid 
      FROM shopexport_mapping 
      WHERE shop = '$shopId' AND tabelle = '$tabelle' AND intid = '$intid' ".($intid2?" AND intid2 = '$intid2' ":'')."  LIMIT 1");
  }

  /**
   * @param int    $shopId
   * @param string $tabelle
   * @param string $extid
   * @param int    $intid2
   *
   * @return mixed
   */
  protected function GetShopexportMappingInt($shopId, $tabelle, $extid, $intid2 = 0)
  {
    return $this->app->DB->Select("SELECT intid 
      FROM shopexport_mapping 
      WHERE shop = '$shopId' AND tabelle = '$tabelle' AND extid = '$extid' ".($intid2?" AND intid2 = '$intid2' ":'')." 
      LIMIT 1");
  }

  /**
   * @param int    $shop
   * @param string $tabelle
   * @param int    $intid
   * @param string $extid
   * @param int    $intid2
   *
   * @return mixed
   */
  public function ShopexportMappingSet($shop, $tabelle, $intid, $extid, $intid2 = 0)
  {
    $check = $this->app->DB->Select("SELECT id FROM shopexport_mapping 
      WHERE shop = '$shop' AND tabelle = '$tabelle' AND intid = '$intid' AND intid2 = '$intid2' LIMIT 1");
    if(!$check)
    {
      $this->app->DB->Insert("INSERT INTO shopexport_mapping (shop, tabelle, intid, intid2, extid, zeitstempel) 
        VALUES ('$shop', '$tabelle', '$intid', '$intid2', '$extid', now())");
      return $this->app->DB->GetInsertID();
    }
    $this->app->DB->Update("UPDATE shopexport_mapping SET extid = '$extid' WHERE id = '$check' LIMIT 1");

    return $check;
  }

  /**
   * @param int    $id
   * @param string $nummer
   * @param bool   $create
   *
   * @return mixed
   * @throws Exception
   */
  public function RemoteGetArticle($id,$nummer, $create = false)
  {
    if($create)
    {
      $data['nummerintern'] = $nummer;
    }else {
      $data['nummer'] = $nummer;
    }
    $ret = $this->RemoteCommand($id,'getarticle',$data);
    $variante_von = null;
    //if(!$create)return $ret;
    $_nummer = null;
    $shopexportArr = $this->app->DB->SelectRow("SELECT * FROM shopexport WHERE id = '$id' LIMIT 1");
    $projekt = $shopexportArr['projekt'];
    $nurneueartikel = $shopexportArr['nurneueartikel'];
    $artikelnummerbeimanlegenausshop = $shopexportArr['artikelnummerbeimanlegenausshop'];
    if(!$create){
      $nurneueartikel = false;
    }
    if(!isset($ret['nummer']) && isset($ret[0]) && isset($ret[0]['nummer']))
    {
      $reta = $ret;
    }elseif(isset($ret['nummer']))
    {
      $reta[] = $ret;
    }elseif(isset($ret[0]) && isset($ret[0]['name'])){
      $ret[0]['nummer'] = '';//$nummer;
      $reta = $ret;
    }elseif(isset($ret['name'])){
      $ret['nummer'] = '';//$nummer;
      $reta[] = $ret;
    }else
    {
      return $ret;
    }
    if(!$reta || !is_array($reta))
    {
      return $ret;
    }
    $stuecklistenmechanik = false;
    $variantevonorig = null;
    $steuersatz_normal = $this->app->erp->Firmendaten('steuersatz_normal');
    $steuersatz_ermaessigt = $this->app->erp->Firmendaten('steuersatz_ermaessigt');
    if(empty($steuersatz_ermaessigt))
    {
      $steuersatz_ermaessigt = 7;
    }
    if(empty($steuersatz_normal))
    {
      $steuersatz_normal = 19;
    }
    $crossellingInstalled = $this->app->erp->ModulVorhanden('crossselling');
    foreach($reta as $k => $ret) 
    {
      if(isset($ret['stueckliste'])){
        $stuecklistenmechanik = $ret['stueckliste'];
      }
      $ret['nummer'] = $this->app->DB->real_escape_string(trim($ret['nummer']));
      $articleid = null;
      if($k == 0)
      {
        $articleid = $this->app->erp->GetArticleIDFromShopnumber($id, $nummer,true);
        if($articleid)
        {
          $variantevonorig = $this->app->DB->Select("SELECT variante_von 
          FROM artikel 
          WHERE id = '$articleid' AND variante = 1 
          LIMIT 1");
          if(!$variantevonorig)
          {
            $articleid = $this->app->DB->Select("SELECT id 
              FROM artikel 
              WHERE id = '$articleid' AND ifnull(geloescht,0) = 0 AND nummer <> 'DEL' 
              LIMIT 1");
          }
        }
      }
      if((isset($ret['nummerintern']) && $ret['nummerintern'] != '') && !$articleid)
      {
        $articleid = $this->app->erp->GetArticleIDFromShopnumber($id, $ret['nummerintern']);
      }
      if(($ret['nummer'] != '') && !$articleid)
      {
        $articleid = $this->app->erp->GetArticleIDFromShopnumber($id, $ret['nummer']);
      }
      if(($ret['ean'] != '') && !$articleid)
      {
        $articleid = $this->app->erp->GetArticleIDFromShopnumber($id, $ret['ean']);
      }
      if($articleid)
      {
        if(isset($ret['shoparticleid']) && $ret['shoparticleid'] != '' && trim($this->app->DB->real_escape_string($ret['shoparticleid'])) != '')
        {
          if(!$this->app->DB->Select("SELECT id FROM `artikelnummer_fremdnummern` WHERE shopid = '$id' AND artikel = '$articleid' AND nummer = '".trim($this->app->DB->real_escape_string($ret['shoparticleid']))."' LIMIT 1"))
          {
            $this->app->DB->Insert("INSERT INTO `artikelnummer_fremdnummern` (artikel, shopid, aktiv, nummer, bezeichnung) 
                VALUES ('$articleid','$id','1','".trim($this->app->DB->real_escape_string($ret['shoparticleid']))."','SHOPID')");
          }
        }
      }
      if(!$articleid && !$create)
      {
        continue;
      }
      if(!$articleid || !$nurneueartikel)
      {
        if($articleid)
        {
          $arr['id'] = $articleid;
          $arr['nummer'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$articleid' LIMIT 1");
        }elseif($ret['nummer'] == '')
        {
          $arr['nummer'] = $this->app->erp->GetNextArtikelnummer('',1,$shopexportArr['projekt']);
        }
        /*if(isset($ret['nummer']) && $ret['nummer'] == '' && !is_null($variante_von))
        {
          $ret['nummer'] = $_nummer.'-'.$ret['nummer'];
          $arr['nummer'] = $ret['nummer'];
        }*/
        if(isset($ret['stueckliste']) && empty($variante_von)){
          $arr['stueckliste'] = $ret['stueckliste'];
        }
        $arr['projekt'] = $shopexportArr['projekt'];
        $arr['name_de'] = $ret['name'];
        $arr['uebersicht_de'] = isset($ret['uebersicht_de'])?$ret['uebersicht_de']:'';
        $arr['kurztext_de'] = isset($ret['kurztext_de'])?$ret['kurztext_de']:'';
        //$arr['anabregs_text'] = isset($ret['uebersicht_de'])?$ret['uebersicht_de']:'';
        if(isset($ret['ean']) && $ret['ean'] != '')
        {
          $arr['ean'] = $ret['ean'];
        }
        if(isset($ret['gewicht']) && $ret['gewicht'] !== '')
        {
          $arr['gewicht'] = $ret['gewicht'];
        }
        if(isset($ret['laenge']) && $ret['laenge'] !== '')
        {
          $arr['laenge'] = $ret['laenge'];
        }
        if(isset($ret['hoehe']) && $ret['hoehe'] !== '')
        {
          $arr['hoehe'] = $ret['hoehe'];
        }
        if(isset($ret['breite']) && $ret['breite'] !== '')
        {
          $arr['breite'] = $ret['breite'];
        }

        if(!$articleid){
          $arr['lagerartikel'] = 1;
        }
        if(true)
        {
          if(!empty($ret['umsatzsteuer'])){
            if(is_numeric($ret['umsatzsteuer'])){
              if($ret['umsatzsteuer'] == $steuersatz_normal)
              {
                $arr['umsatzsteuer'] = 'normal';
              }elseif($ret['umsatzsteuer'] == $steuersatz_ermaessigt)
              {
                $arr['umsatzsteuer'] = 'ermaessigt';
              }
            }elseif(in_array($ret['umsatzsteuer'], array('normal', 'ermaessigt', 'befreit'))){
              $arr['umsatzsteuer'] = $ret['umsatzsteuer'];
            }
          }
        }

        $arr['hersteller'] = $ret['hersteller'];
        if(!$stuecklistenmechanik && isset($ret['variante_von']) && !$articleid){
          $variantevon = $this->app->DB->Select("SELECT artikel 
            FROM `artikelnummer_fremdnummern` 
            WHERE shopid = '$id' AND aktiv = 1 AND nummer = '".trim($ret['variante_von'])."' AND nummer <> '' 
            LIMIT 1");
          if(empty($variantevon))
          {
            $variantevon = $this->app->DB->Select("SELECT id 
              FROM artikel 
              WHERE ifnull(geloescht,0) = 0 AND nummer <> 'DEL' AND nummer <> '' AND nummer = '".trim($ret['variante_von'])."' 
              ORDER BY projekt = '$projekt' DESC 
              LIMIT 1");
          }
          if(empty($variantevon))
          {
            $variantevon = $variantevonorig;
          }
          if(!empty($variantevon))
          {
            $arr['variante'] = 1;
            $arr['variante_von'] = $variantevon;
          }
        }elseif($variante_von)
        {
          if($variante_von)
          {
            $arr['variante'] = 1;
          }
          if($variante_von)
          {
            $arr['variante_von'] = $variante_von;
          }
        }
        $freifeldmapping = $this->app->DB->SelectArr("SELECT * 
          FROM shopexport_freifelder 
          WHERE shop = '$id' AND aktiv = 1 AND freifeld_shop <> '' AND freifeld_wawi <> ''");
        if(!empty($freifeldmapping))
        {
          foreach($freifeldmapping as $freifeldval)
          {
            $freifeldShopKey = 'freifeld_'.$freifeldval['freifeld_shop'];
            if(isset($ret[$freifeldShopKey]))
            {
              $arr[$freifeldval['freifeld_wawi']] = $ret[$freifeldShopKey];
            }
          }
        }
        $articleid = $this->app->erp->InsertUpdateArtikel($arr);
        unset($arr);
        if($artikelnummerbeimanlegenausshop && !empty($ret['artikelnummerausshop']))
        {
          $this->app->DB->Update("UPDATE artikel SET nummer = '".$ret['artikelnummerausshop']."' WHERE id = '$articleid'");
        }
        
          $ret['nummer'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$articleid' LIMIT 1");
        if(isset($ret['bilder']) && $ret['bilder'] && is_array($ret['bilder']))
        {
          $dateien = $this->app->DB->SelectArr("SELECT * FROM datei_stichwoerter ds INNER JOIN datei_version dv ON ds.datei = dv.datei AND ds.parameter = '$articleid' AND ds.objekt LIKE 'Artikel' ORDER BY ds.sort");
          if(empty($dateien))
          {
            foreach($ret['bilder'] as $v)
            {
              if($v['path'] != '' && $v['content'] != '')
              {
                $path_parts = pathinfo($v['path']);
                $fileid = $this->app->erp->CreateDatei($path_parts['basename'], 'Shopbild', '', '', base64_decode($v['content']), 'Cronjob');
                $this->app->erp->AddDateiStichwort($fileid, 'Shopbild', 'artikel', $articleid);
              }
            }
          }elseif($dateien[0]['subjekt'] === 'shopbild'){
            $this->app->DB->Update("UPDATE datei_stichwoerter SET subjekt = 'Shopbild' WHERE id = '".$dateien[0]['id']."' AND subjekt = 'shopbild' LIMIT 1");
          }
        }
        if($variante_von === null)
        {
          $variante_von = $articleid;
          $_nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$articleid' LIMIT 1");
          $nummer = isset($ret['nummerintern'])?$ret['nummerintern']:(isset($ret['nummer'])?$ret['nummer']:'');
        }
        if(!empty($ret['preis_netto']) && $ret['preis_netto'] > 0)
        {
          $this->app->erp->AddVerkaufspreis($articleid,1,0,$ret['preis_netto']);
        }
        
        if(!empty($ret['bilder']) && is_array($ret['bilder']))
        {
          $dateien = $this->app->DB->SelectArr("SELECT * 
            FROM datei_stichwoerter ds 
            INNER JOIN datei_version dv ON ds.datei = dv.datei AND ds.parameter = '$articleid' AND ds.objekt LIKE 'Artikel' 
            ORDER BY ds.sort");
          if(empty($dateien))
          {
            foreach($ret['bilder'] as $v)
            {
              if($v['path'] != '' && $v['content'] != '')
              {
                $path_parts = pathinfo($v['path']);
                $fileid = $this->app->erp->CreateDatei($path_parts['basename'], 'Shopbild', '', '', base64_decode($v['content']), 'Cronjob');
                if(isset($v['id']))
                {
                  $this->ShopexportMappingSet($id, 'datei', $fileid, $v['id'], $articleid);
                }
                $this->app->erp->AddDateiStichwort($fileid, 'Shopbild', 'artikel', $articleid);
              }
            }
          }
        }

        if($crossellingInstalled){
          if(!empty($ret['crossselling'])){
            if(!empty($ret['crossselling']['similar'])){
              foreach ($ret['crossselling']['similar'] as $cskey => $csvalue) {
                $crosssellingartikel = $this->app->DB->Select("SELECT id from artikel WHERE nummer= '".$csvalue."' and geloescht='0'");
                if($crosssellingartikel){
                  if(!$this->app->DB->Select("SELECT id FROM crossselling_artikel WHERE artikel='$articleid' AND crosssellingartikel='$crosssellingartikel' AND shop='$id'")){
                    $sort = $this->app->DB->Select("SELECT MAX(sort)+1 from crossselling_artikel WHERE artikel='$articleid' AND shop='$id'");
                    $this->app->DB->Insert("INSERT INTO crossselling_artikel (aktiv, art, artikel, crosssellingartikel, shop, sort, bemerkung) VALUES ('1','1','$articleid','$crosssellingartikel','$id','$sort','Über Artikelimport hinzugefügt')");
                  }
                }
              }
            }
            if(!empty($ret['crossselling']['related'])){
              foreach ($ret['crossselling']['related'] as $cskey => $csvalue) {
                $crosssellingartikel = $this->app->DB->Select("SELECT id from artikel WHERE nummer= '".$csvalue."' and geloescht='0'");
                if($crosssellingartikel){
                  if(!$this->app->DB->Select("SELECT id FROM crossselling_artikel WHERE artikel='$articleid' AND crosssellingartikel='$crosssellingartikel' AND shop='$id'")){
                    $sort = $this->app->DB->Select("SELECT MAX(sort)+1 from crossselling_artikel WHERE artikel='$articleid' AND shop='$id'");
                    $this->app->DB->Insert("INSERT INTO crossselling_artikel (aktiv, art, artikel, crosssellingartikel, shop, sort, bemerkung) VALUES ('1','2','$articleid','$crosssellingartikel','$id','$sort','Über Artikelimport hinzugefügt')");
                  }
                }
              }
            }
          }
        }

        $katext = 0;
        if(isset($ret['kategorien_all']) && $ret['kategorien_all'])
        {
          foreach($ret['kategorien_all'] as $v)
          {
            if($katext == 0 && $v['parent'] == 0)
            {
              $katext = $v['id'];
            }
            if(!$this->app->DB->Select("SELECT id 
              FROM shopexport_kategorien 
              WHERE shop = '$id' AND extid = '".$this->app->DB->real_escape_string($v['id'])."' LIMIT 1")){
              $this->app->DB->Insert("INSERT INTO shopexport_kategorien (shop, extid, aktiv,extname,extparent,extsort) VALUES 
            ('$id','" . $this->app->DB->real_escape_string($v['id']) . "','" . (int)$v['aktiv'] . "','" . $this->app->DB->real_escape_string($v['name']) . "','" . $this->app->DB->real_escape_string($v['parent']) . "','" . (int)$v['sort'] . "')");
            }
          }
          $shopkategorien = $this->getShopKatgeorien($id);
          /** @var Onlineshops $onlineShopObj */
          $onlineShopObj = $this->app->loadModule('onlineshops');
          $categoryRootId = 0;
          if(!empty($onlineShopObj) && method_exists($onlineShopObj, 'getJsonSettings')) {
            $categoryRootId = (int)$onlineShopObj->getJsonSettings($id, 'category_root_id');
          }
          $kategorientoint = [$categoryRootId];

          if(!empty($shopkategorien))
          {
            foreach($shopkategorien as $v)
            {
              if(!$v['kategorie'])
              {
                $parentInXentral = $kategorientoint[$v['extparent']];
                if(empty($parentInXentral)){
                  $parentInXentral = $this->app->DB->Select("SELECT kategorie FROM shopexport_kategorien WHERE shop='$id' AND extid='".$v['extparent']."' LIMIT 1");
                }
                $this->app->DB->Insert("INSERT INTO `artikelkategorien` (bezeichnung,parent) 
                  VALUES ('".$this->app->DB->real_escape_string($v['extname'])."','$parentInXentral')");
                $kategorientoint[$v['extid']] = $this->app->DB->GetInsertID();
                $this->app->DB->Update("UPDATE shopexport_kategorien 
                  SET kategorie = '".$kategorientoint[$v['extid']]."' 
                  WHERE id = '".$v['id']."' LIMIT 1");
                $v['kategorie'] = $kategorientoint[$v['extid']];
              }
            }
          }
        }
        if(!empty($ret['kategorien']))
        {
          $shopkategorien = $this->app->DB->SelectArr("SELECT * 
              FROM shopexport_kategorien 
              WHERE shop = '$id' AND kategorie > 0 
              ORDER BY extparent, extsort");
          if(!empty($shopkategorien))
          {
            foreach($shopkategorien as $v)
            {
              $kategorientoint[$v['extid']] = $v['kategorie'];
            }
            $kategoriezugewiesen = false;
            foreach($ret['kategorien'] as $v)
            {
              if(!$kategoriezugewiesen){
                $kategorieid = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE bezeichnung='".$v['name']."' AND parent=0 AND geloescht=0 LIMIT 1");
                if($kategorieid){
                  $this->app->DB->Update("UPDATE artikel SET typ = '".$kategorieid."_kat' WHERE id = '$articleid' LIMIT 1");
                  $kategoriezugewiesen = true;
                }
              }


              if(isset($kategorientoint[$v['id']]) && $kategorientoint[$v['id']])
              {
                if(!$this->app->DB->Select("SELECT id FROM artikelbaum_artikel WHERE artikel = '$articleid' AND kategorie = '".$kategorientoint[$v['id']]."' LIMIT 1"))
                {
                  $this->app->DB->Insert("INSERT INTO artikelbaum_artikel (artikel, kategorie) 
                    VALUES ('$articleid', '".$kategorientoint[$v['id']]."')");
                }
              }
            }
          }
        }
        if(!empty($ret['eigenschaften']))
        {
          foreach($ret['eigenschaften'] as $v)
          {
            if($v['name'] != '')
            {
              $eigenschaft = $this->app->DB->Select("SELECT id 
                FROM `artikeleigenschaften` 
                WHERE name = '".$this->app->DB->real_escape_string($v['name'])."' AND geloescht = 0 
                LIMIT 1");
              if(!$eigenschaft)
              {
                $this->app->DB->Insert("INSERT INTO `artikeleigenschaften` (name) 
                  VALUES ('".$this->app->DB->real_escape_string($v['name'])."')");
                $eigenschaft = $this->app->DB->GetInsertID();
              }
              if($eigenschaft)
              {
                $wert = $this->app->DB->Select("SELECT id 
                  FROM artikeleigenschaftenwerte 
                  WHERE artikel = '$articleid' AND artikeleigenschaften = '$eigenschaft' AND wert = '".$this->app->DB->real_escape_string($v['values'])."' 
                  LIMIT 1");
                if(!$wert)
                {
                  $this->app->DB->Insert("INSERT INTO artikeleigenschaftenwerte (artikel,artikeleigenschaften,wert) 
                  VALUES ('$articleid','$eigenschaft','".$this->app->DB->real_escape_string($v['values'])."')");
                }
              }
            }
          }
        }

        if(!empty($ret['matrixprodukt_gruppe1'])){
          $query = sprintf("UPDATE artikel SET matrixprodukt=1 WHERE id='%s'",$articleid);
          $this->app->DB->Update($query);
          for($m=1;$m<=3;$m++){
            if(empty($ret['matrixprodukt_gruppe'.$m])){
              continue;
            }

            $gruppeName = $this->app->DB->real_escape_string($ret['matrixprodukt_gruppe'.$m]);
            $query = sprintf("SELECT id FROM matrixprodukt_eigenschaftengruppen_artikel WHERE artikel='%s' AND name='%s'",
              $articleid, $gruppeName);
            $gruppeId = $this->app->DB->Select($query);
            if(empty($gruppeId)){
              $query = sprintf("SELECT MAX(sort) FROM matrixprodukt_eigenschaftengruppen_artikel WHERE artikel='%s' AND name='%s'",
                $articleid, $gruppeName);
              $sort = $this->app->DB->Select($query);
              if(empty($sort)){
                $sort = '0';
              }
              $query = sprintf("INSERT INTO matrixprodukt_eigenschaftengruppen_artikel 
                (artikel, aktiv, name, name_ext,projekt,bearbeiter,erstellt,sort, pflicht, oeffentlich,typ) VALUES 
                ('%s','1','%s','','0','','NOW()','%s','1','0','0')", $articleid, $gruppeName, $sort);
              $this->app->DB->Insert($query);
              $gruppeId = $this->app->DB->GetInsertID();
            }

            foreach ($ret['matrixprodukt_optionen'.$m] as $optionBezeichnung){
              $optionBezeichnung = $this->app->DB->real_escape_string($optionBezeichnung);
              $query = sprintf("SELECT id FROM matrixprodukt_eigenschaftenoptionen_artikel WHERE artikel='%s' AND name='%s'",
                $articleid, $optionBezeichnung);
              $optionId = $this->app->DB->Select($query);
              if(empty($optionId)){
                $query = sprintf("SELECT MAX(sort) FROM matrixprodukt_eigenschaftenoptionen_artikel WHERE artikel='%s' AND name='%s'",
                  $articleid, $optionBezeichnung);
                $sort = $this->app->DB->Select($query);
                if(empty($sort)){
                  $sort = '1';
                }
                $query = sprintf("INSERT INTO matrixprodukt_eigenschaftenoptionen_artikel 
                    (artikel, matrixprodukt_eigenschaftenoptionen, aktiv,name,name_ext,sort,erstellt,gruppe,bearbeiter,artikelnummer) 
                    VALUES ('%s','0','1','%s','','%s','NOW()','%s','','')",
                  $articleid, $optionBezeichnung, $sort, $gruppeId);
                $this->app->DB->Insert($query);
              }
            }
          }
        }

        if(!empty($ret['matrixprodukt_wert1'])){
          for($m=1;$m<=3;$m++){
            if(empty($ret['matrixprodukt_wert'.$m])){
              continue;
            }

            $optionBezeichnung = $this->app->DB->real_escape_string($ret['matrixprodukt_wert'.$m]);
            $query = sprintf("SELECT id FROM matrixprodukt_eigenschaftenoptionen_artikel WHERE artikel='%s' AND name='%s'",
              $variante_von, $optionBezeichnung);
            $optionId = $this->app->DB->Select($query);
            if(!empty($optionId)){
              $query = sprintf("SELECT id FROM matrixprodukt_optionen_zu_artikel WHERE artikel='%s' AND option_id='%s'",
                $articleid, $optionId);
              $zuordnungId = $this->app->DB->Select($query);
              if(empty($zuordnungId)){
                $query = sprintf("INSERT INTO matrixprodukt_optionen_zu_artikel (artikel,option_id) VALUES ('%s','%s')",
                  $articleid,$optionId);
                $this->app->DB->Insert($query);
              }
            }
          }
        }

        if(!$this->app->DB->Select("SELECT id FROM `artikel_onlineshops` WHERE artikel = '$articleid' AND shop = '$id' AND aktiv = 1 LIMIT 1")) {
          $this->app->DB->Insert("INSERT INTO `artikel_onlineshops` (artikel, shop,aktiv, ausartikel) VALUES ('$articleid', '$id',1 ,1)");
        }
        if(isset($ret['nummerintern']) && trim($ret['nummerintern']) != '')
        {
          $data['nummerintern'] = trim($ret['nummerintern']);
        }
        if(($ret['nummer'] != $data['nummerintern']) && $data['nummerintern'] != '' && trim($data['nummerintern']) != '')
        {
          $data['nummerintern'] = trim($data['nummerintern']);
          if(!$this->app->DB->Select("SELECT id FROM `artikelnummer_fremdnummern` WHERE artikel = '$articleid' AND shopid = '$id' AND nummer = '".$this->app->DB->real_escape_string($data['nummerintern'])."' AND nummer <> '' LIMIT 1"))
          {
            $this->app->DB->Insert("INSERT INTO `artikelnummer_fremdnummern` (artikel, shopid, aktiv, bearbeiter, zeitstempel,nummer) VALUES ('$articleid','$id','1','Cronjob',now(),'".$this->app->DB->real_escape_string($data['nummerintern'])."')");
          }
        }
      }elseif($articleid)
      {
        if(isset($ret['ean']) && ($ret['ean'] != '') && $this->app->DB->Select("SELECT id FROM artikel WHERE id = '$articleid' AND ifnull(ean,'') = '' LIMIT 1"))
        {
          $this->app->DB->Update("UPDATE artikel SET ean = '".$this->app->DB->real_escape_string($ret['ean'])."' WHERE id = '$articleid' AND ifnull(ean,'') = '' LIMIT 1");
        }
      }

      if($stuecklistenmechanik && !empty($variante_von) && !$this->app->erp->IstStuecklistenZirkel($articleid,$variante_von)){
        $stuecklistenmenge = 1;
        if(!empty($ret['stuecklistemenge'])){
          $stuecklistenmenge = $ret['stuecklistemenge'];
        }
        $query = sprintf('SELECT MAX(sort) FROM stueckliste WHERE stuecklistevonartikel=%d',$variante_von);
        $sort = $this->app->DB->Select($query);
        if($sort !== NULL){
          $sort++;
        }else{
          $sort = 0;
        }
        $query = sprintf("INSERT INTO stueckliste (sort, artikel, referenz, place, layer, stuecklistevonartikel, 
                         menge, firma, wert, bauform, alternative, zachse, xpos, ypos, art) 
            VALUES (%d, %d, '', 'DP', 'Top', %d, %d, 1, '', '', 0, '', '', '', 'et')",
          $sort,$articleid,$variante_von,$stuecklistenmenge);
        $this->app->DB->Insert($query);
      }

      if($variante_von === null) {
        if(!empty($articleid) && $stuecklistenmechanik){
          $query = sprintf('DELETE FROM stueckliste WHERE stuecklistevonartikel=%d',$articleid);
          $this->app->DB->Delete($query);
        }
        $variante_von = $articleid;
      }

      if(isset($ret['inaktiv'])){
        $ret['aktiv'] = !$ret['inaktiv'];
      }

      if(isset($ret['aktiv']))
      {
        if($ret['aktiv']){
          $this->app->DB->Update("UPDATE artikel SET inaktiv = '0' WHERE id = '$articleid' LIMIT 1");
        }else{
          $this->app->DB->Update("UPDATE artikel SET inaktiv = '1' WHERE id = '$articleid' LIMIT 1");
        }
      }
      if(isset($ret['staffelpreise'])){
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $gestern = $date->format('Y-m-d');
        $this->app->DB->Update("UPDATE verkaufspreise SET gueltig_bis='$gestern' WHERE artikel='$articleid' AND adresse='0' AND art='Kunde'");

        foreach ($ret['staffelpreise'] as $staffelpreis){
          $preis = $staffelpreis['preis'];
          if($staffelpreis['brutto']){
            $umsatzsteuer = $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id='$articleid' LIMIT 1");
            $steuersatz = 0;
            if($umsatzsteuer === 'ermaessigt')
            {
              $steuersatz = $this->app->erp->Firmendaten('steuersatz_ermaessigt');
            }
            if($umsatzsteuer === 'normal'){
              $steuersatz = $this->app->erp->Firmendaten('steuersatz_normal');
            }
            $preis /= 1+$steuersatz/100;
          }
          $this->app->erp->AddVerkaufspreis($articleid,$staffelpreis['ab_menge'],0,$preis);
        }
      }
      if(isset($ret['fremdnummern']) && !empty($ret['fremdnummern'])){
        foreach ($ret['fremdnummern'] as $fremdnummer){
          $query = sprintf("SELECT id FROM artikelnummer_fremdnummern WHERE shopid=%d AND nummer='%s' AND bezeichnung='%s' AND aktiv=%d AND artikel=%s",
          $id,$this->app->DB->real_escape_string($fremdnummer['nummer']),$this->app->DB->real_escape_string($fremdnummer['bezeichnung']),'1',$articleid);
          $fremdnummerfehlt = empty($this->app->DB->Select($query));

          if($fremdnummerfehlt){
            $query = sprintf("INSERT INTO artikelnummer_fremdnummern 
                (artikel, bezeichnung, nummer, shopid, bearbeiter, aktiv)  VALUES 
                (%d,'%s','%s',%d,'%s',%d)",
              $articleid,$this->app->DB->real_escape_string($fremdnummer['bezeichnung']),
              $this->app->DB->real_escape_string($fremdnummer['nummer']),$id,$this->app->User->GetName(),1);
            $this->app->DB->Insert($query);
          }
        }
      }

      if(isset($ret['pseudopreis'])){
        $this->app->DB->Update(sprintf("UPDATE artikel SET pseudopreis='%s' WHERE id=%d",$ret['pseudopreis'],$articleid));
      }
      if(!$create)
      {
        return $ret;
      }
    }

    return $ret;
  }

  /**
   * @param int $shopId
   * @param int $parent
   * @param int $lvl
   *
   * @return array
   */
  protected function getShopKatgeorien($shopId, $parent = 0, $lvl = 0)
  {
    if($lvl > 20)
    {
      return [];
    }
    $ret = [];
    $shopkategorien = $this->app->DB->SelectArr("SELECT * FROM shopexport_kategorien WHERE shop = '$shopId' AND extparent = '$parent' AND extid <> '' ORDER BY extsort");
    if($shopkategorien)
    {
      foreach($shopkategorien as $v)
      {
        $ret[] = $v;
        $subkategorien = $this->getShopKatgeorien($shopId, $v['extid'], $lvl + 1);
        if(!empty($subkategorien))
        {
          foreach($subkategorien as $v2)
          {
            $ret[] = $v2;
          }
        }
      }
    }

    return $ret;
  }

  /**
   * @param int          $id
   * @param array        $artikel_arr
   * @param string|array $extnummer
   * @param bool         $nurlager
   *
   * @return mixed|null
   * @throws Exception
   */
  public function RemoteSendArticleList($id,$artikel_arr, $extnummer = '', $nurlager = false)
  {
    if(!class_exists('ObjGenArtikel') &&
      is_file(dirname(__DIR__) . '/objectapi/mysql/_gen/object.gen.artikel.php')){
        include_once dirname(__DIR__) . '/objectapi/mysql/_gen/object.gen.artikel.php';
    }
    $shopexportarr = $this->app->DB->SelectRow("SELECT * FROM shopexport WHERE id='$id' LIMIT 1");
    $artikelexport = $shopexportarr['artikelexport'];
    $translationpossible = false;
    if($this->app->erp->ModulVorhanden('artikel_texte'))
    {
      $translationpossible = true;
    }
    $loadElements = [
      'foreign_numbers' => true,
      'article_descriptions' => true,
      'translations' => true,
      'categories' => true,
      'properties' => true,
      'pictures' => true,
      'cross_selling' => true,
      'bulk_prices' => true,
      'standard_price' => true,
      'variants' => true,
      'free_fields' => true,
    ];

    if(
        ($nurlager || empty($artikelexport))
      && !empty($shopexportarr['modulename'])
      && !is_file(dirname(__DIR__).'/pages/'.$shopexportarr['modulename'].'_custom.php')
    ) {
      $elementsNotNeededByModule = ShopimporterBase::storageNotNeededElements($shopexportarr['modulename']);
      foreach($elementsNotNeededByModule as $element) {
        $loadElements[$element] = false;
      }
    }

    //$lagerexport = $shopexportarr['lagerexport'];
    $lagergrundlage = $shopexportarr['lagergrundlage'];
    $shopbilderuebertragen = $shopexportarr['shopbilderuebertragen'];
    $projekt = (int)$shopexportarr['projekt'];
    $projektlager = $this->app->DB->Select("SELECT id FROM projekt WHERE id = $projekt AND projektlager = 1 LIMIT 1");
    $tmp = new ObjGenArtikel($this->app);
    $cartikel_arr = !empty($artikel_arr)?count($artikel_arr):0;
    for($i=0;$i<$cartikel_arr;$i++)
    {
      $artikel = $artikel_arr[$i];
      $lagerexport = $this->app->erp->GetArtikelShopEinstellung('autolagerlampe', $artikel, $shopexportarr);
      $tmp->Select($artikel);
      $data[$i] = ['artikel' => $artikel,'artikelid' => $artikel];
      $data[$i]['nummer'] = $tmp->GetNummer();
      $projekt = $tmp->GetProjekt();
      if(is_array($extnummer) && count($extnummer) > $i && !empty($extnummer[$i]) && trim($extnummer[$i]) != '')
      {
        if($data[$i]['nummer'] != trim($extnummer[$i])){
          $data[$i]['fremdnummer'] = trim($extnummer[$i]);
        }
      }

      $data[$i]['inaktiv'] = $tmp->GetInaktiv();

      if($tmp->GetIntern_Gesperrt()=='1'){
        $data[$i]['inaktiv'] = 1;
      }

      $data[$i]['name_de'] = $tmp->GetName_De();
      $data[$i]['name_en'] = $tmp->GetName_En();
      $data[$i]['einheit'] = $tmp->GetEinheit();
      $data[$i]['hersteller'] = $tmp->GetHersteller();
      $data[$i]['herstellernummer'] = $tmp->GetHerstellernummer();
      $data[$i]['ean'] = $tmp->GetEan();
      if(!empty($loadElements['foreign_numbers'])) {
        $data[$i]['artikelnummer_fremdnummern'] = $this->app->DB->SelectArr("SELECT * FROM artikelnummer_fremdnummern WHERE artikel = '" . $tmp->GetId() . "' AND shopid = '$id' AND aktiv = '1' AND nummer <> ''  ORDER BY bezeichnung = 'SHOPID' DESC");
        if(!empty($data[$i]['artikelnummer_fremdnummern'])){
          foreach ($data[$i]['artikelnummer_fremdnummern'] as $fkey => $fval) {
            $data[$i]['artikelnummer_fremdnummern'][$fkey]['nummer'] = trim($fval['nummer']);
          }
        }
      }

      if(!empty($loadElements['article_descriptions'])) {
      $data[$i]['kurztext_de'] = $tmp->GetKurztext_De();
      $data[$i]['kurztext_en'] = $tmp->GetKurztext_En();
      $data[$i]['anabregs_text'] = htmlspecialchars($tmp->GetAnabregs_Text(),ENT_QUOTES);
      $data[$i]['anabregs_text_en'] = htmlspecialchars($tmp->GetAnabregs_Text_En(),ENT_QUOTES);
      $data[$i]['beschreibung_de'] = $tmp->GetBeschreibung_De();
      $data[$i]['beschreibung_en'] = $tmp->GetBeschreibung_En();
      $data[$i]['uebersicht_de'] = htmlspecialchars($tmp->GetUebersicht_De(),ENT_QUOTES);
      $data[$i]['uebersicht_en'] = htmlspecialchars($tmp->GetUebersicht_En(),ENT_QUOTES);
      $data[$i]['herkunftsland'] = $tmp->GetHerkunftsland();
      if(method_exists($tmp,'GetMetadescription_De'))
      {
        $data[$i]['metadescription_de'] = $tmp->GetMetadescription_De();
      }
      if(method_exists($tmp,'GetMetadescription_En'))
      {
        $data[$i]['metadescription_en'] = $tmp->GetMetadescription_En();
      }
      if(method_exists($tmp,'GetMetakeywords_De'))
      {
        $data[$i]['metakeywords_de'] = $tmp->GetMetakeywords_De();
      }
      if(method_exists($tmp,'GetMetakeywords_En'))
      {
        $data[$i]['metakeywords_en'] = $tmp->GetMetakeywords_En();
      }
      if(method_exists($tmp,'GetMetatitle_De'))
      {
        $data[$i]['metatitle_de'] = $tmp->GetMetatitle_De();
      }
      if(method_exists($tmp,'GetMetatitle_En'))
      {
        $data[$i]['metatitle_en'] = $tmp->GetMetatitle_En();
      }
      }
      if($tmp->GetVariante())
      {
        $variante_von = $tmp->GetVariante_Von();
        if($variante_von)
        {
          $tmp_2 = new ObjGenArtikel($this->app);
          $tmp_2->Select($variante_von);
          if($data[$i]['kurztext_de'] == '')
          {
            $data[$i]['kurztext_de'] = $tmp_2->GetKurztext_De();
          }
          if($data[$i]['kurztext_en'] == '')
          {
            $data[$i]['kurztext_en'] = $tmp_2->GetKurztext_En();
          }
          if($data[$i]['beschreibung_de'] == '')
          {
            $data[$i]['beschreibung_de'] = $tmp_2->GetBeschreibung_De();
          }
          if($data[$i]['beschreibung_en'] == '')
          {
            $data[$i]['beschreibung_en'] = $tmp_2->GetBeschreibung_En();
          }
          if($data[$i]['uebersicht_de'] == '')
          {
            $data[$i]['uebersicht_de'] = htmlspecialchars($tmp_2->GetUebersicht_De(),ENT_QUOTES);
          }
          if($data[$i]['uebersicht_en'] == '')
          {
            $data[$i]['uebersicht_en'] = htmlspecialchars($tmp_2->GetUebersicht_En(),ENT_QUOTES);
          }
        }
      }

      if($data[$i]['uebersicht_de'] == '' && $data[$i]['beschreibung_de'] == '' && $data[$i]['kurztext_de'] == '')
      {
        $data[$i]['uebersicht_de'] = $data[$i]['anabregs_text'];
      }
      
      $data[$i]['links_de'] = $tmp->GetLinks_De();
      $data[$i]['altersfreigabe'] = $tmp->GetAltersfreigabe();
      $data[$i]['links_en'] = $tmp->GetLinks_En();
      $data[$i]['startseite_de'] = $tmp->GetStartseite_De();
      $data[$i]['startseite_en'] = $tmp->GetStartseite_En();
      $data[$i]['restmenge'] = $this->app->erp->GetArtikelShopEinstellung('restmenge', $artikel, $shopexportarr);
      $data[$i]['startseite'] = $tmp->GetStartseite();
      $data[$i]['standardbild'] = $tmp->GetStandardbild();
      $data[$i]['herstellerlink'] = $tmp->GetHerstellerlink();
      $data[$i]['herstellernummer'] = $tmp->GetHerstellernummer();
      $data[$i]['hersteller'] = $tmp->GetHersteller();
      $data[$i]['lieferzeit'] = $tmp->GetLieferzeit();
      $data[$i]['lieferzeitmanuell'] = $this->app->erp->GetArtikelShopEinstellung('lieferzeitmanuell', $artikel, $shopexportarr);
      $data[$i]['gewicht'] = $tmp->GetGewicht();
      $data[$i]['laenge'] = $tmp->GetLaenge();
      $data[$i]['breite'] = $tmp->GetBreite();
      $data[$i]['hoehe'] = $tmp->GetHoehe();
      
      $data[$i]['wichtig'] = $tmp->GetWichtig();
      $data[$i]['porto'] = $tmp->GetPorto();
      $data[$i]['gesperrt'] = $tmp->GetGesperrt();
      $data[$i]['sperrgrund'] = $tmp->GetSperrgrund();
      $data[$i]['gueltigbis'] = $tmp->GetGueltigbis();
      $data[$i]['umsatzsteuer'] = $tmp->GetUmsatzsteuer();
      if($data[$i]['umsatzsteuer']!=='ermaessigt' && $data[$i]['umsatzsteuer']!=='befreit') {
        $data[$i]['umsatzsteuer']="normal";
      }
      $data[$i]['ausverkauft'] = $tmp->GetAusverkauft();
      $data[$i]['variante'] = $tmp->GetVariante();
      $data[$i]['variante_von_id'] = $tmp->GetVariante_Von();
      $data[$i]['variantevon'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='".$tmp->GetVariante_Von()."' LIMIT 1");
      $data[$i]['variantevon_fremdnummer'] = trim($this->app->DB->Select("SELECT nummer FROM `artikelnummer_fremdnummern` WHERE shopid = '$id' AND aktiv = 1 AND artikel = '".$tmp->GetVariante_Von()."' AND trim(nummer) <> '' ORDER BY bezeichnung = 'SHOPID' DESC LIMIT 1"));
      if($data[$i]['variantevon_fremdnummer'] == '')
      {
        unset($data[$i]['variantevon_fremdnummer']);
      }
      //$data[$i]['pseudopreis'] = $tmp->GetPseudopreis();
      $data[$i]['pseudopreis'] = $this->app->erp->GetArtikelShopEinstellung('pseudopreis', $artikel, $shopexportarr);
      $data[$i]['keinrabatterlaubt'] = $tmp->GetKeinrabatterlaubt();
      $data[$i]['einkaufspreis'] = $this->app->erp->GetEinkaufspreis($artikel,1);
      //$data[$i]['pseudolager'] = $tmp->GetPseudolager();
      $data[$i]['pseudolager'] = $this->app->erp->GetArtikelShopEinstellung('pseudolager', $artikel, $shopexportarr);
      $this->app->erp->RunHook('remote_send_article_list_pseudostorage', 3, $id, $artikel, $data[$i]['pseudolager']);
      if(is_numeric($data[$i]['pseudolager']) && $data[$i]['pseudolager'] < 0) {
        $data[$i]['pseudolager'] = 0;
      }
      $data[$i]['downloadartikel'] = $tmp->GetDownloadartikel();
      $data[$i]['zolltarifnummer'] = $tmp->GetZolltarifnummer();
      $preisgruppe = $this->app->DB->Select("SELECT s.preisgruppe FROM shopexport s INNER JOIN gruppen g ON s.preisgruppe = g.id AND g.aktiv = 1 WHERE s.id='$id' LIMIT 1");

      if(empty($loadElements['free_fields'])) {
        $freifelder = null;
      }
      else{
        $freifelder = $this->app->DB->SelectArr("SELECT freifeld_wawi, freifeld_shop FROM shopexport_freifelder WHERE shop = '$id' AND aktiv = 1 AND freifeld_wawi <> '' AND freifeld_shop <> ''");
      }
      if($freifelder)
      {
        /**
        * @deprecated 20.1
        * AP
        * Konstrunktion 'freifeld_Mappingname -> Wert' wird ersetzt durch 'Freifelder-> DE -> Mappingname -> Wert'
        */
        foreach($freifelder as $freifeld)
        {
          if(method_exists($tmp,'Get'.ucfirst($freifeld['freifeld_wawi'])))
          {
            $_funktionsname = 'Get'.ucfirst($freifeld['freifeld_wawi']);
            $data[$i]['freifeld_'.$freifeld['freifeld_shop']] = $tmp->$_funktionsname();
          }
        }
        /**
        * @deprecated Ende
        */

        foreach($freifelder as $freifeld)
        {
          $_funktionsname = 'Get'.ucfirst($freifeld['freifeld_wawi']);
          if(method_exists($tmp,$_funktionsname))
          {
            $data[$i]['freifelder']['DE'][$freifeld['freifeld_shop']] = $tmp->$_funktionsname();
          }
        }

        //Freifeldübersetzungen
        $freifelduebersetzungen = $this->app->DB->SelectArr("SELECT sf.freifeld_shop, af.sprache, af.nummer, af.wert FROM shopexport_freifelder sf JOIN artikel_freifelder af ON sf.freifeld_wawi = CONCAT('freifeld',af.nummer) WHERE sf.shop = '$id' AND af.artikel = '$artikel' AND sf.aktiv = 1 AND sf.freifeld_wawi <> '' AND sf.freifeld_shop <> '' AND af.wert <> ''");
        if(!empty($freifelduebersetzungen)){
          foreach ($freifelduebersetzungen as $ffukey => $ffuvalue) {
            $data[$i]['freifelder'][$ffuvalue['sprache']][] = array('mapping' => $ffuvalue['freifeld_shop'], 'freifeldnummer' => $ffuvalue['nummer'], 'wert' => $ffuvalue['wert']);
          }
        }
      }

      $data[$i]['typ'] = $tmp->GetTyp();
      $data[$i]['kategoriename'] = $tmp->GetTyp();
      if(strpos($data[$i]['typ'], '_kat'))
      {
        $data[$i]['kategoriename'] = $this->app->DB->Select("SELECT bezeichnung FROM artikelkategorien WHERE id = '".(int)str_replace('_kat','',$data[$i]['typ'])."' LIMIT 1");
      }

      $kategorienuebertragen = $shopexportarr['kategorienuebertragen'];//$this->app->DB->Select("SELECT kategorienuebertragen FROM shopexport WHERE id = '$id' LIMIT 1");
      if(!empty($kategorienuebertragen) && !empty($loadElements['categories']))
      {
        $kategorienbaum = null;
        /** @var Onlineshops $onlineShopObj */
        $onlineShopObj = $this->app->loadModule('onlineshops');
        $categoryRootId = 0;
        if(!empty($onlineShopObj) && method_exists($onlineShopObj, 'getJsonSettings')) {
          $categoryRootId = (int)$onlineShopObj->getJsonSettings($id, 'category_root_id');
        }
        $this->app->erp->GetKategorienbaum($kategorienbaum, $categoryRootId, 0, $id);
        if(!empty($kategorienbaum))
        {        
          $kategorien = $this->app->DB->SelectArr("SELECT ak.id, ak.bezeichnung FROM `artikelbaum_artikel` aa INNER JOIN `artikelkategorien` ak ON aa.kategorie = ak.id AND ak.geloescht <> 1 AND aa.artikel = '$artikel' ORDER by ak.bezeichnung");
          if($kategorien)
          {
            foreach($kategorien as $v)
            {
              $katid[$v['id']] = true;
            }
          }
          if(!empty($katid))
          {
            foreach($kategorienbaum as $v)
            {
              $data[$i]['kompletter_kategorienbaum'][] = array('name'=>$v['bezeichnung'],'parent'=>$v['parent'],'id'=>$v['id'],'lvl'=>$v['lvl'],'extid'=>$v['extid']);
              if(isset($katid[$v['id']]))
              {
                $data[$i]['kategorien'][] = array('name'=>$v['bezeichnung'],'parent'=>$v['parent'],'id'=>$v['id'],'lvl'=>$v['lvl'],'extid'=>$v['extid']);
              }
            }
          }
        }
      }

      $eigenschaftenuebertragen = $shopexportarr['eigenschaftenuebertragen'];//$this->app->DB->Select("SELECT eigenschaftenuebertragen FROM shopexport WHERE id = '$id' LIMIT 1");
      if(!empty($eigenschaftenuebertragen) && !empty($loadElements['properties'])) {
        $eigenschaften = $this->app->DB->SelectArr("SELECT e.name, ew.wert FROM artikeleigenschaften e INNER JOIN artikeleigenschaftenwerte ew ON e.id = ew.artikeleigenschaften
        WHERE ew.artikel = '$artikel' AND e.name <> '' AND ew.wert <> '' AND e.geloescht <> 1");
        if(!empty($eigenschaften))
        {
          $eigenschaftind = 0;
          foreach($eigenschaften as $eigenschaft)
          {
            $eigenschaftind++;
            $data[$i]['eigenschaften'][] = array('name'=>$eigenschaft['name'], 'values'=>$eigenschaft['wert']);
          }
        }

        $eigenschaftenuebersetzungen = $this->app->DB->SelectArr("SELECT language_from, language_to, property_from, property_to,property_value_from,property_value_to FROM article_property_translation WHERE article_id = '$artikel'");
        $data[$i]['eigenschaftenuebersetzungen'] = $eigenschaftenuebersetzungen;
      }

      //Bilder
      $dateien = null;
      if ($shopbilderuebertragen && !empty($loadElements['pictures'])) {
        $dateien = $this->getImagesForArticle($artikel);
        if (!empty($dateien)) {
          $data[$i]['Dateien'] = [];
          foreach ($dateien as $datei) {
            $filename = $this->app->erp->GetDateiName($datei['id']);
            $path_info = pathinfo($filename);
            $data[$i]['Dateien'][] = array(
                'datei' => base64_encode($this->app->erp->GetDatei($datei['id'])),
                'filename' => $filename,
                'extension' => $path_info['extension'],
                'titel' => $datei['titel'],
                'beschreibung' => $datei['beschreibung'],
                'id' => $datei['id'],
                'version' => $datei['version'],
                'stichwort' => $datei['subjekt'],
                'extid' => $this->GetShopexportMappingExt($id, 'datei', $datei['id']));
          }
        }
        $dateiengeloescht = $this->app->DB->SelectArr("SELECT s.extid FROM shopexport_mapping s LEFT JOIN datei d ON s.intid = d.id AND d.geloescht = 0 WHERE s.shop = '$id' AND s.tabelle = 'datei' AND s.intid2 = '$artikel' AND isnull(d.id)");
        if (!empty($dateiengeloescht)) {
          if (empty($dateien)) {
            $data[$i]['Dateien'] = [];
          }
          foreach ($dateiengeloescht as $datei) {
            $data[$i]['Dateien'][] = array('loeschen' => 1, 'extid' => $datei['extid']);
          }
        }
      }


      if (method_exists($tmp, 'GetSteuer_Art_Produkt')) {
        $data[$i]['steuer_art_produkt'] = $tmp->GetSteuer_Art_Produkt();
      }
      if (method_exists($tmp, 'GetSteuer_Art_Produkt_Download')) {
        $data[$i]['steuer_art_produkt_download'] = $tmp->GetSteuer_Art_Produkt_Download();
      }

      $projekt = $tmp->GetProjekt();
      $projektlager = $this->app->DB->Select("SELECT id FROM projekt WHERE id = '$projekt' AND projektlager = 1 LIMIT 1");
      $data[$i]['anzahl_bilder'] = $this->app->DB->Select("SELECT COUNT(datei) FROM  datei_stichwoerter WHERE subjekt='Shopbild' AND objekt='Artikel' AND parameter='$artikel'");

      $bestandalternativartikel = method_exists($tmp, 'GetBestandalternativartikel') ? $tmp->GetBestandalternativartikel() : 0;

      if ($bestandalternativartikel) {
        $data[$i]['anzahl_lager'] = floor($this->app->erp->ArtikelAnzahlVerkaufbar($bestandalternativartikel, 0, $projektlager, $id, $lagergrundlage));
        $data[$i]['lagerkorrekturwert'] = floor(-($this->app->erp->ArtikelAnzahlVerkaufbar($bestandalternativartikel, 0, $projektlager, 0, $lagergrundlage) - $data[$i]['anzahl_lager']));
      } else {
        $data[$i]['anzahl_lager'] = floor($this->app->erp->ArtikelAnzahlVerkaufbar($artikel, 0, $projektlager, $id, $lagergrundlage));
        $data[$i]['lagerkorrekturwert'] = floor(-($this->app->erp->ArtikelAnzahlVerkaufbar($artikel, 0, $projektlager, 0, $lagergrundlage) - $data[$i]['anzahl_lager']));
      }

      $data[$i]['pseudolager'] = $this->app->erp->GetArtikelShopEinstellung('pseudolager', $artikel, $shopexportarr);
      $this->app->erp->RunHook('remote_send_article_list_pseudostorage', 3, $id, $artikel, $data[$i]['pseudolager']);
      if (is_numeric($data[$i]['pseudolager']) && $data[$i]['pseudolager'] < 0) {
        $data[$i]['pseudolager'] = 0;
        $data[$i]['anzahl_lager'] = 0;
      } elseif ($data[$i]['pseudolager'] > 0) {
        $data[$i]['anzahl_lager'] = $data[$i]['pseudolager'];
      }
      if ($data[$i]['anzahl_lager'] < 0) {
        $data[$i]['anzahl_lager'] = 0;
      }
      $data[$i]['autolagerlampe'] = $tmp->GetAutolagerlampe();
      if (method_exists($this->app->erp, 'GetArtikelShopEinstellung')) {
        $data[$i]['autolagerlampe'] = $this->app->erp->GetArtikelShopEinstellung('autolagerlampe', $artikel, $shopexportarr);
      }
      if ($data[$i]['autolagerlampe'] != '1') {
        $data[$i]['anzahl_lager'] = '';
        $data[$i]['pseudolager'] = '';
        $this->app->DB->Update(
          sprintf(
            'UPDATE `artikel_onlineshops` 
            SET `storage_cache` = NULL, `pseudostorage_cache` = NULL 
            WHERE `artikel` = %d AND `shop` = %d',
            $artikel, $id
          )
        );
      } else {
        $this->app->DB->Update(
          sprintf(
            'UPDATE `artikel_onlineshops` 
            SET `storage_cache` = %d, `pseudostorage_cache` = %s 
            WHERE `artikel` = %d AND `shop` = %d',
            $data[$i]['anzahl_lager'],
            !isset($data[$i]['pseudolager']) || !is_numeric($data[$i]['pseudolager']) ? 'NULL' : $data[$i]['pseudolager'],
            $artikel,
            $id
          )
        );
      }

      $projekt = $shopexportarr['projekt'];// $this->app->DB->Select("SELECT projekt FROM shopexport WHERE id='$id' LIMIT 1");
      if (!empty($shopexportarr['crosssellingartikeluebertragen']) && !empty($loadElements['cross_selling'])) {
        //if($this->app->DB->Select("SELECT crosssellingartikeluebertragen FROM shopexport WHERE id = '$id' LIMIT 1")){
        $data[$i]['crosssellingartikel'] = $this->app->DB->SelectArr("SELECT a.id, a.nummer, ak.bezeichnung as kategorie, a.name_de, a.name_en, ca.art, ca.gegenseitigzuweisen, af.nummer AS fremdnummer
            FROM crossselling_artikel ca 
            JOIN artikel a ON ca.crosssellingartikel = a.id 
            LEFT JOIN artikelkategorien ak ON CONCAT(ak.id,'_kat') = a.typ 
            LEFT JOIN (SELECT af.id,af.nummer,af.artikel,af.shopid FROM artikelnummer_fremdnummern af JOIN (SELECT artikel, MAX(shopid) AS maxid FROM artikelnummer_fremdnummern WHERE aktiv=1 AND (shopid=0 OR shopid=2) GROUP BY artikel) x ON x.artikel = af.artikel AND af.shopid=x.maxid WHERE af.aktiv = 1) af ON af.artikel = a.id
            WHERE ca.artikel='" . $tmp->GetId() . "' AND (ca.shop='$id' OR ca.shop='0') 
            GROUP BY ca.crosssellingartikel, ca.art");

        if (empty($data[$i]['crosssellingartikel'])) {
          $data[$i]['crosssellingartikel'] = [];
        }

        $gegenseitigzugewiesen = $this->app->DB->SelectArr("SELECT a.id, a.nummer, ak.bezeichnung as kategorie, a.name_de, a.name_en, ca.art, ca.gegenseitigzuweisen, af.nummer AS fremdnummer
            FROM crossselling_artikel ca 
            JOIN artikel a ON ca.artikel = a.id 
            LEFT JOIN artikelkategorien ak ON CONCAT(ak.id,'_kat') = a.typ 
            LEFT JOIN (SELECT af.id,af.nummer,af.artikel,af.shopid FROM artikelnummer_fremdnummern af JOIN (SELECT artikel, MAX(shopid) AS maxid FROM artikelnummer_fremdnummern WHERE aktiv=1 AND (shopid=0 OR shopid=2) GROUP BY artikel) x ON x.artikel = af.artikel AND af.shopid=x.maxid WHERE af.aktiv = 1) af ON af.artikel = a.id            
            WHERE ca.crosssellingartikel='" . $tmp->GetId() . "' AND ca.gegenseitigzuweisen=1 AND (ca.shop='$id' OR ca.shop='0') 
            LEFT JOIN (SELECT nummer,artikel FROM artikelnummer_fremdnummern WHERE shopid=0 OR shopid='$id' ORDER BY shopid DESC LIMIT 1 ) af ON af.artikel = a.id
            GROUP BY ca.artikel, ca.art");
        if (!empty($gegenseitigzugewiesen)) {
          foreach ($gegenseitigzugewiesen as $gegenseitigzugewiesenercrosssellingartikel) {
            $data[$i]['crosssellingartikel'][] = $gegenseitigzugewiesenercrosssellingartikel;
          }
        }
      }

      if (!empty($loadElements['article_descriptions']) && $this->app->erp->ModulVorhanden('artikel_texte')) {
        $sprachen = ['de', 'en'];
        foreach ($sprachen as $sprache) {
          $query = sprintf("SELECT * FROM artikel_texte WHERE shop=%d AND sprache='%s' AND artikel=%d AND aktiv=1 LIMIT 1",
            $id, strtoupper($sprache), $artikel);
          $ersetzeStandardbeschreibung = $this->app->DB->SelectRow($query);
          if (!empty($ersetzeStandardbeschreibung)) {
            $data[$i]['name_' . $sprache] = $ersetzeStandardbeschreibung['name'];
            $data[$i]['beschreibung_' . $sprache] = $ersetzeStandardbeschreibung['beschreibung_online'];
            $data[$i]['kurztext_' . $sprache] = $ersetzeStandardbeschreibung['kurztext'];
            $data[$i]['metadescription_' . $sprache] = $ersetzeStandardbeschreibung['meta_description'];
            $data[$i]['metakeywords_' . $sprache] = $ersetzeStandardbeschreibung['meta_keywords'];
            $data[$i]['metatitle_' . $sprache] = $ersetzeStandardbeschreibung['meta_title'];
            if ($sprache === 'de') {
              $data[$i]['anabregs_text'] = $ersetzeStandardbeschreibung['beschreibung'];
            } else {
              $data[$i]['anabregs_text_en'] = $ersetzeStandardbeschreibung['beschreibung'];
            }
          }
        }
      }

      if(!empty($loadElements['standard_price'])){

        $priceInformation = $this->app->DB->SelectArr("SELECT v.preis AS preis, v.waehrung 
          FROM `verkaufspreise` AS `v` 
          WHERE v.artikel = '{$artikel}' AND v.ab_menge = 1 AND v.geloescht = 0 AND v.art != 'Gruppe'
            AND (v.objekt = 'Standard' OR v.objekt = '') AND (v.adresse = '0' OR v.adresse = '') 
            AND (v.gueltig_bis >= NOW() OR v.gueltig_bis = '0000-00-00') 
          ORDER BY v.preis DESC LIMIT 1");
        $priceInformation = reset($priceInformation);
        $defaultPrice = $priceInformation['preis'];
        $defaultCurrency = $priceInformation['waehrung'] ?: 'EUR';
        if($preisgruppe && method_exists($this->app->erp, 'GetVerkaufspreisGruppe')){
          $defaultCurrency = 'EUR'; //the follow up function imply EUR as the default currency
          $defaultPrice = $this->app->erp->GetVerkaufspreisGruppe($artikel, 1, $preisgruppe);
        }

        $data[$i]['waehrung'] = $defaultCurrency;
        $data[$i]['preis'] = $defaultPrice;

        if(!empty($tmp->GetSteuersatz()) && $tmp->GetSteuersatz() != -1){
          $data[$i]['steuersatz'] = (float)$tmp->GetSteuersatz();
        }elseif($data[$i]['umsatzsteuer'] === 'ermaessigt'){
          $data[$i]['steuersatz'] = (float)$this->app->erp->GetStandardSteuersatzErmaessigt($projekt);
        }elseif($data[$i]['umsatzsteuer'] === 'befreit'){
          $data[$i]['steuersatz'] = 0;
        }else{
          $data[$i]['steuersatz'] = (float)$this->app->erp->GetStandardSteuersatzNormal($projekt);
        }

        $steuer = ($data[$i]['steuersatz'] + 100) / 100.0;

        if(!empty($shopexportarr['staffelpreiseuebertragen']) && !empty($loadElements['bulk_prices'])){
          $preisgruppenzusatz = " AND v.art = 'Kunde' AND v.adresse = 0";
          if(!empty($preisgruppe)){
            $preisgruppenzusatz = " AND v.gruppe = '$preisgruppe'";
          }
          $query = sprintf("SELECT v.adresse, g.name, g.kennziffer, v.ab_menge, v.preis, g.id, v.waehrung
        FROM `verkaufspreise` AS `v`
        LEFT JOIN `gruppen` AS `g` on v.gruppe = g.id
        WHERE v.artikel = %d AND v.geloescht = 0 AND (v.gueltig_bis = '0000-00-00' OR curdate() <= v.gueltig_bis OR isnull(v.gueltig_bis))
            AND v.adresse = 0 AND (v.gueltig_ab = '0000-00-00' OR curdate() >= v.gueltig_ab OR isnull(v.gueltig_ab)) %s
        ORDER by g.id, v.ab_menge", $artikel, $preisgruppenzusatz);
          $standardstaffelpreise = $this->app->DB->SelectArr($query);
          $data[$i]['staffelpreise_standard'] = [];
          if(!empty($standardstaffelpreise)){
            foreach ($standardstaffelpreise as $staffelpreis) {
              $data[$i]['staffelpreise_standard'][] = [
                'ab_menge' => $staffelpreis['ab_menge'],
                'preis' => $staffelpreis['preis'],
                'bruttopreis' => $staffelpreis['preis'] * $steuer,
                'waehrung' => $staffelpreis['waehrung'] ?: 'EUR'
              ];
            }
          }

          $query = sprintf("SELECT a.name AS `kundenname`, a.email, v.adresse, v.ab_menge, v.preis, v.waehrung
        FROM `verkaufspreise` AS `v`
        JOIN `adresse` AS `a` ON v.adresse = a.id AND v.art LIKE 'Kunde'
        WHERE v.`artikel` = %d AND v.geloescht = 0 AND (v.gueltig_bis = '0000-00-00' OR curdate() <= v.gueltig_bis OR isnull(v.gueltig_bis))
            AND (v.gueltig_ab = '0000-00-00' OR curdate() >= v.gueltig_ab OR isnull(v.gueltig_ab))
        ORDER by v.adresse, v.ab_menge", $artikel);
          $staffelpreisekunden = $this->app->DB->SelectArr($query);
          if(!empty($staffelpreisekunden)){
            foreach ($staffelpreisekunden as $staffelpreis) {
              $data[$i]['staffelpreise_kunden'][] = [
                'ab_menge' => $staffelpreis['ab_menge'],
                'preis' => $staffelpreis['preis'],
                'bruttopreis' => $staffelpreis['preis'] * $steuer,
                'waehrung' => $staffelpreis['waehrung'] ?: 'EUR',
                'email' => $staffelpreis['email'],
                'kundenname' => $staffelpreis['kundenname']
              ];
            }
          }

          $query = sprintf("SELECT g.name, g.kennziffer, v.ab_menge, v.preis, g.id, v.waehrung, sk.extgruppename
        FROM `verkaufspreise` AS `v`
        JOIN `gruppen` AS `g` on v.gruppe = g.id
        JOIN `shopexport_kundengruppen` AS `sk` ON sk.gruppeid = v.gruppe
        WHERE v.`artikel` = %d AND v.geloescht = 0 AND (v.gueltig_bis = '0000-00-00' OR curdate() <= v.gueltig_bis OR isnull(v.gueltig_bis))
            AND v.art like 'Gruppe' AND v.gruppe <> '%s' AND sk.shopid = %d AND sk.aktiv = 1 AND (v.gueltig_ab = '0000-00-00' OR curdate() >= v.gueltig_ab OR isnull(v.gueltig_ab))
        ORDER by g.id, v.ab_menge", $artikel, $preisgruppe, $id);
          $staffelpreisegruppen = $this->app->DB->SelectArr($query);
          if(!empty($staffelpreisegruppen)){
            foreach ($staffelpreisegruppen as $staffelpreis) {
              $data[$i]['staffelpreise_gruppen'][] = [
                'ab_menge' => $staffelpreis['ab_menge'],
                'preis' => $staffelpreis['preis'],
                'bruttopreis' => $staffelpreis['preis'] * $steuer,
                'waehrung' => $staffelpreis['waehrung'] ?: 'EUR',
                'kennziffer' => $staffelpreis['kennziffer'],
                'gruppenname' => $staffelpreis['name'],
                'gruppeextern' => $staffelpreis['extgruppename']
              ];
            }
          }

          /**
           * @deprecated 20.3
           * Staffelpreise werden gesondert uebergeben statt in einem sammelarray
           */
          $staffelpreise = $this->app->DB->SelectArr("
        SELECT a.name AS kundenname, a.email, v.adresse, g.name,g.kennziffer, v.ab_menge, v.preis, g.id, v.waehrung, art.umsatzsteuer  
        FROM `verkaufspreise` AS v 
        INNER JOIN artikel art ON v.artikel = art.id
        LEFT JOIN gruppen g on v.gruppe = g.id AND v.art like 'Gruppe'
        LEFT JOIN adresse a ON v.adresse = a.id AND v.art LIKE 'Kunde'
        WHERE v.`artikel` = '$artikel' AND v.geloescht = 0 AND (v.gueltig_bis = '0000-00-00' OR curdate() <= v.gueltig_bis OR isnull(v.gueltig_bis)) 
        AND (v.gueltig_ab = '0000-00-00' OR curdate() >= v.gueltig_ab OR isnull(v.gueltig_ab)) ORDER by g.id, ab_menge");

          if(!empty($staffelpreise)){
            foreach ($staffelpreise as $k => $v) {
              if(empty($v['adresse'])){
                $data[$i]['staffelpreise'][] = array('ab_menge' => $v['ab_menge'], 'preis' => $v['preis'], 'bruttopreis' => $v['preis'] * $steuer, 'waehrung' => ($v['waehrung'] ? $v['waehrung'] : 'EUR'));
                if($v['id']){
                  $data[$i]['staffelpreise'][count($data[$i]['staffelpreise']) - 1]['gruppenkennziffer'] = $v['kennziffer'];
                  $data[$i]['staffelpreise'][count($data[$i]['staffelpreise']) - 1]['gruppenname'] = $v['name'];
                }
              }
            }
          }
          /**
           * @deprecated Ende
           */
        }


        $data[$i]['bruttopreis'] = $data[$i]['preis'] * $steuer;
      }
      $data[$i]['checksum'] = $tmp->GetChecksum();
      if ($data[$i]['preis'] == '' && !empty($artikelexport) && !$nurlager) {
        if($lagerexport){
          $nurlager = true;
        } else {
          $this->app->erp->Systemlog(
            'Shopexport bei Artikel ' . $data[$i]['nummer'] . ' ' . $data[$i]['name_de'] . ' fehlgeschlagen, da Verkaufspreis fehlt.'
          );
          $data[$i]['artikel'] = 'ignore';
        }
      }

      $steuerermaessigt = ($this->app->erp->GetStandardSteuersatzErmaessigt($projekt) + 100) / 100.0;
      $steuernormal = ($this->app->erp->GetStandardSteuersatzNormal($projekt) + 100) / 100.0;
      $steuermapping = [
        'befreit' => 1,
        'ermaessigt' => $steuerermaessigt,
        'normal' => $steuernormal
      ];

      if (!empty($loadElements['variants']) && !empty($shopexportarr['variantenuebertragen'])
        && $this->app->erp->ModulVorhanden('matrixprodukt') && $tmp->GetMatrixprodukt()) {
        $artfreifeld = '';
        for ($_i = 1; $_i <= 40; $_i++) {
          $artfreifeld .= sprintf(' art.freifeld%d AS `freifeld%d`, ',$_i,$_i);
        }
        $varianten = null;

        $data[$i]['artikel_varianten'] = [];
        $db = $this->app->Container->get('Database');

        $offset = 0;
        $eigenschaftind = 0;

        $gruppenuebersetzung = [];
        $gruppenwertuebersetzung = [];

        $query = sprintf('SELECT meoa.name AS wert,meoa.name_ext AS wert_ext, mega.name AS gruppe, mega.name_ext AS gruppe_ext
                    FROM matrixprodukt_optionen_zu_artikel AS moza
                    JOIN matrixprodukt_eigenschaftenoptionen_artikel AS meoa ON moza.option_id = meoa.id
                    JOIN matrixprodukt_eigenschaftengruppen_artikel AS mega ON mega.id = meoa.gruppe
                    JOIN artikel art ON art.id = moza.artikel
                    WHERE art.variante_von=%d ANd art.geloescht=0 AND art.nummer <> "DEL" AND art.intern_gesperrt=0
                    GROUP BY mega.id, meoa.id
                    ORDER BY SUM(POW(10,1-mega.sort) * meoa.sort) ASC', $artikel);
        foreach ($db->yieldAll($query) as $matrixdaten) {
          $data[$i]['matrix_varianten']['gruppen'][$matrixdaten['gruppe']][$matrixdaten['wert']] = true;

          if ($translationpossible && !empty($loadElements['translations'])) {
            if (empty($gruppenuebersetzung[$matrixdaten['gruppe']])) {
              $gruppennamen = $this->app->DB->SelectArr("SELECT 
                                        IF(name_external_from<>'',name_external_from,name_from) AS name_from, 
                                        IF(name_external_to<>'',name_external_to,name_to) AS name_to, language_to 
                                        FROM `matrix_article_translation` 
                                        WHERE name_from='" . $matrixdaten['gruppe'] . "' AND (project=0 OR project='$projekt')");
              $gruppenuebersetzung[$matrixdaten['gruppe']] = true;
              if (!empty($gruppennamen)) {
                foreach ($gruppennamen as $gruppenname) {
                  $data[$i]['matrix_varianten']['texte']['gruppen'][$gruppenname['language_to']][$gruppenname['name_from']] = $gruppenname['name_to'];
                }
              }
            }
            if (empty($gruppenwertuebersetzung[$matrixdaten['wert']])) {
              $gruppenwerte = $this->app->DB->SelectArr("SELECT
                        IF(maot.name_external_from<>'',maot.name_external_from,maot.name_from) AS `name_from`,
                        IF(maot.name_external_to<>'',maot.name_external_to,maot.name_to) AS `name_to`, maot.language_to
                        FROM `matrix_article_options_translation` AS `maot`
                        WHERE maot.name_from='" . $this->app->DB->real_escape_string($matrixdaten['wert']) . "'");
              $gruppenwertuebersetzung[$matrixdaten['wert']] = true;
              foreach ($gruppenwerte as $gruppenwert) {
                $data[$i]['matrix_varianten']['texte']['werte'][$gruppenwert['language_to']][$gruppenwert['name_from']] = $gruppenwert['name_to'];
              }
            }
          }
        }

        do {
          unset($data[$i]['matrix_varianten']['artikel']);
          unset($data[$i]['artikel_varianten']);
          //if($this->app->DB->Select("SELECT variantenuebertragen FROM shopexport WHERE id = '$id' LIMIT 1")){
          $varianten = "SELECT art.pseudolager AS `pseudolager`, art.nummer AS `nummer`, art.inaktiv AS `inaktiv`, 
        art.id AS `artikel`, art.gewicht AS `gewicht`, art.zolltarifnummer AS `zolltarifnummer`, art.name_en AS `name_en`,
        $artfreifeld
        art.pseudopreis AS `pseudopreis`, art.lieferzeitmanuell AS `lieferzeitmanuell`, art.altersfreigabe AS `altersfreigabe`,
        IF(art.intern_gesperrt = 1,1,0) AS `gesperrt`, art.uebersicht_de AS `uebersicht_de`, art.uebersicht_en AS `uebersicht_en`,
        IF(IFNULL(vp2.preis,0) > 0, vp2.preis, vp.preis) as `preis`, art.umsatzsteuer AS `umsatzsteuer`, art.ean AS `ean`,
        art.name_de AS `name_de`, art.restmenge AS `restmenge`, art.steuersatz AS `steuersatz`
        FROM artikel AS `art` 
        LEFT JOIN 
        ( SELECT v.artikel,min(v.preis) as `preis`  
         FROM verkaufspreise AS `v` 
         WHERE (ifnull(v.gueltig_bis,'0000-00-00')='0000-00-00' OR v.gueltig_bis >= curdate() ) AND v.ab_menge <= 1
           AND v.art = 'Kunde' AND v.adresse = 0
         GROUP BY v.artikel ) AS `vp` ON vp.artikel = art.id
        LEFT JOIN (SELECT v2.artikel,min(v2.preis) as `preis`  
         FROM verkaufspreise AS `v2` 
         WHERE (ifnull(v2.gueltig_bis,'0000-00-00')='0000-00-00' OR v2.gueltig_bis >= curdate() ) AND v2.ab_menge <= 1 
           AND v2.art = 'Gruppe' AND v2.gruppe = '$preisgruppe'
         GROUP BY v2.artikel
        ) AS `vp2` ON vp2.artikel = art.id
        JOIN `matrixprodukt_optionen_zu_artikel` AS `moza` ON moza.artikel = art.id
        JOIN `matrixprodukt_eigenschaftenoptionen_artikel` AS `meoa` ON moza.option_id = meoa.id
        JOIN `matrixprodukt_eigenschaftengruppen_artikel` AS `mega` ON meoa.gruppe = mega.id
        WHERE art.variante_von = '$artikel' AND art.geloescht <> 1 AND art.nummer <> 'DEL'
        GROUP BY art.id
        ORDER BY SUM(POW(10,1-mega.sort) * meoa.sort) ASC
        LIMIT 5000 OFFSET $offset";
          $offset += 5000;

          foreach ($db->yieldAll($varianten) as $eigenschaft) {
            $eigenschaftind++;
            $steuer = $steuermapping[$eigenschaft['umsatzsteuer']];
            if(!empty($eigenschaft['steuersatz']) && $eigenschaft['steuersatz'] != -1){
                $steuer = ($eigenschaft['steuersatz'] + 100) / 100;
            }
            if (empty($steuer)) {
              $steuer = $steuernormal;
            }
            $matrixPseudoStorage = $eigenschaft['pseudolager'];
            $this->app->erp->RunHook('remote_send_article_list_pseudostorage', 3, $id, $eigenschaft['artikel'], $matrixPseudoStorage);
            if(is_numeric($matrixPseudoStorage) && $matrixPseudoStorage < 0) {
              $matrixPseudoStorage = 0;
            }
            $matrixStock = (float)$this->app->erp->ArtikelAnzahlVerkaufbar($eigenschaft['artikel'], 0, $projektlager, $id, $lagergrundlage);
            if($matrixStock < 0) {
              $matrixStock = 0;
            }
            $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][] =
              array('zolltarifnummer' => $eigenschaft['zolltarifnummer'], 'gewicht' => $eigenschaft['gewicht'],
                'artikel' => $eigenschaft['artikel'], 'preis' => $eigenschaft['preis'], 'gesperrt' => $eigenschaft['gesperrt'],
                'nummer' => $eigenschaft['nummer'], 'lieferzeitmanuell' => $eigenschaft['lieferzeitmanuell'],
                'altersfreigabe' => $eigenschaft['altersfreigabe'], 'ean' => $eigenschaft['ean'],
                'lag' => $matrixStock,
                'pseudolager' => $matrixPseudoStorage, 'pseudopreis' => $eigenschaft['pseudopreis'],
                'restmenge' => $eigenschaft['restmenge'], 'steuersatz' => ($steuer - 1) * 100,
                'bruttopreis' => $eigenschaft['preis'] * $steuer, 'inaktiv' => $eigenschaft['inaktiv'],
                'name_de' => $eigenschaft['name_de'], 'name_en' => $eigenschaft['name_en'],
                'uebersicht_de' => $eigenschaft['uebersicht_de'], 'uebersicht_en' => $eigenschaft['uebersicht_en']);
            if ($freifelder) {
              foreach ($freifelder as $freifeld) {
                $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][count($data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']]) - 1]['freifelder']['DE'][$freifeld['freifeld_shop']] =
                  $this->app->DB->Select('SELECT ' . $freifeld['freifeld_wawi'] . ' FROM artikel WHERE id=' . $eigenschaft['artikel']);
              }

              //Freifeldübersetzungen
              $freifelduebersetzungen = $this->app->DB->SelectArr("SELECT sf.freifeld_shop, af.sprache, af.nummer, af.wert FROM shopexport_freifelder sf JOIN artikel_freifelder af ON sf.freifeld_wawi = CONCAT('freifeld',af.nummer) WHERE sf.shop = '$id' AND af.artikel = '" . $eigenschaft['artikel'] . "' AND sf.aktiv = 1 AND sf.freifeld_wawi <> '' AND sf.freifeld_shop <> '' AND af.wert <> ''");
              if (!empty($freifelduebersetzungen)) {
                foreach ($freifelduebersetzungen as $ffukey => $ffuvalue) {
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][count($data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']]) - 1]['freifelder'][$ffuvalue['sprache']][] = array('mapping' => $ffuvalue['freifeld_shop'], 'freifeldnummer' => $ffuvalue['nummer'], 'wert' => $ffuvalue['wert']);
                }
              }
            }

            if ($shopbilderuebertragen && !empty($loadElements['pictures'])) {
              $dateien = $this->getImagesForArticle($eigenschaft['artikel']);
              if (!empty($dateien)) {
                foreach ($dateien as $datei) {
                  $filename = $this->app->erp->GetDateiName($datei['id']);
                  $path_info = pathinfo($filename);
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['Dateien']['datei'][] = base64_encode($this->app->erp->GetDatei($datei['id']));
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['Dateien']['filename'][] = $filename;
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['Dateien']['extension'][] = $path_info['extension'];
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['Dateien']['titel'][] = $datei['titel'];
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['Dateien']['beschreibung'][] = $datei['beschreibung'];
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['Dateien']['stichwort'][] = $datei['subjekt'];
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['Dateien']['id'][] = $datei['id'];
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['Dateien']['version'][] = $datei['version'];
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['Dateien']['extid'][] = $this->GetShopexportMappingExt($id, 'datei', $datei['id']);
                }
              }
            }

            $standardstaffelpreise = null;
            if(!empty($loadElements['standard_price'])){
              $preisgruppenzusatz = " AND v.art = 'Kunde' AND v.adresse = 0";
              if(!empty($preisgruppe)){
                $preisgruppenzusatz = " AND v.gruppe = '$preisgruppe'";
              }
              $standardstaffelpreise = $this->app->DB->SelectArr("
              SELECT v.adresse, g.name,g.kennziffer, v.ab_menge, v.preis, g.id, v.waehrung, art.umsatzsteuer  
              FROM `verkaufspreise` AS v 
              INNER JOIN artikel art ON v.artikel = art.id
              LEFT JOIN gruppen g on v.gruppe = g.id 
              WHERE v.`artikel` = '" . $eigenschaft['artikel'] . "' AND v.geloescht = 0 AND (v.gueltig_bis = '0000-00-00' OR curdate() <= v.gueltig_bis OR isnull(v.gueltig_bis))  
                  AND v.adresse=0 AND (v.gueltig_ab = '0000-00-00' OR curdate() >= v.gueltig_ab OR isnull(v.gueltig_ab)) $preisgruppenzusatz
              ORDER by g.id, ab_menge");
            }
            $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['staffelpreise_standard'] = [];
            if (!empty($standardstaffelpreise)) {
              foreach ($standardstaffelpreise as $staffelpreis) {
                $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['staffelpreise_standard'][] = [
                  'ab_menge' => $staffelpreis['ab_menge'],
                  'preis' => $staffelpreis['preis'],
                  'bruttopreis' => $staffelpreis['preis'] * $steuer,
                  'waehrung' => $staffelpreis['waehrung'] ?: 'EUR'
                ];
              }
            }
            if(!empty($loadElements['bulk_prices'])){
              $staffelpreisekunden = $this->app->DB->SelectArr("
              SELECT a.name AS kundenname, a.email, v.adresse,  v.ab_menge, v.preis, v.waehrung, art.umsatzsteuer  
              FROM `verkaufspreise` AS v 
              INNER JOIN artikel art ON v.artikel = art.id
              JOIN adresse a ON v.adresse = a.id AND v.art LIKE 'Kunde'
              WHERE v.`artikel` = '" . $eigenschaft['artikel'] . "' AND v.geloescht = 0 AND (v.gueltig_bis = '0000-00-00' OR curdate() <= v.gueltig_bis OR isnull(v.gueltig_bis)) 
              AND (v.gueltig_ab = '0000-00-00' OR curdate() >= v.gueltig_ab OR isnull(v.gueltig_ab))
              ORDER by v.adresse, ab_menge");
              if(!empty($staffelpreisekunden)){
                foreach ($staffelpreisekunden as $staffelpreis) {
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['staffelpreise_kunden'][] = [
                    'ab_menge' => $staffelpreis['ab_menge'],
                    'preis' => $staffelpreis['preis'],
                    'bruttopreis' => $staffelpreis['preis'] * $steuer,
                    'waehrung' => $staffelpreis['waehrung'] ?: 'EUR',
                    'email' => $staffelpreis['email'],
                    'kundenname' => $staffelpreis['kundenname']
                  ];
                }
              }

              $staffelpreisegruppen = $this->app->DB->SelectArr("
              SELECT g.name,g.kennziffer, v.ab_menge, v.preis, g.id, v.waehrung, art.umsatzsteuer,sk.extgruppename 
              FROM `verkaufspreise` AS v 
              INNER JOIN artikel art ON v.artikel = art.id
              JOIN gruppen g on v.gruppe = g.id 
              JOIN shopexport_kundengruppen sk ON sk.gruppeid=v.gruppe
              WHERE v.`artikel` = '" . $eigenschaft['artikel'] . "' AND v.geloescht = 0 AND (v.gueltig_bis = '0000-00-00' OR curdate() <= v.gueltig_bis OR isnull(v.gueltig_bis)) 
              AND v.art like 'Gruppe' AND v.gruppe<>'$preisgruppe' AND sk.shopid='$id' AND sk.aktiv=1 AND (v.gueltig_ab = '0000-00-00' OR curdate() >= v.gueltig_ab OR isnull(v.gueltig_ab))
              ORDER by g.id, ab_menge");
              if(!empty($staffelpreisegruppen)){
                foreach ($staffelpreisegruppen as $staffelpreis) {
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['staffelpreise_gruppen'][] = [
                    'ab_menge' => $staffelpreis['ab_menge'],
                    'preis' => $staffelpreis['preis'],
                    'bruttopreis' => $staffelpreis['preis'] * $steuer,
                    'waehrung' => $staffelpreis['waehrung'] ?: 'EUR',
                    'kennziffer' => $staffelpreis['kennziffer'],
                    'gruppenname' => $staffelpreis['name'],
                    'gruppeextern' => $staffelpreis['extgruppename']
                  ];
                }
              }
            }
            $staffelpreisema = null;
            if (!empty($shopexportarr['crosssellingartikeluebertragen']) && !empty($loadElements['cross_selling'])) {
              //if($this->app->DB->Select("SELECT crosssellingartikeluebertragen FROM shopexport WHERE id = '$id' LIMIT 1")){
              $preisgruppenzusatz = '';
              if (!empty($preisgruppe)) {
                $preisgruppenzusatz = " AND v.gruppe = '$preisgruppe'";
              }
              $staffelpreisema = $this->app->DB->SelectArr("      
            SELECT g.name, g.kennziffer, v.ab_menge, v.preis, g.id, v.waehrung, v.id, v.adresse, v.art, sk.extgruppename 
            FROM `verkaufspreise` AS v 
            LEFT JOIN gruppen g on v.gruppe = g.id AND v.art like 'Gruppe' 
            LEFT JOIN shopexport_kundengruppen sk ON sk.gruppeid=v.gruppe
            WHERE v.`artikel` = '" . $eigenschaft['artikel'] . "' AND v.geloescht = 0 AND (ifnull(v.gueltig_bis,'0000-00-00') = '0000-00-00' OR curdate() <= v.gueltig_bis OR isnull(v.gueltig_bis)) 
            AND (v.gueltig_ab = '0000-00-00' OR curdate() >= v.gueltig_ab OR isnull(v.gueltig_ab)) $preisgruppenzusatz ORDER by g.id, ab_menge");
            }
            if (!empty($staffelpreisema)) {
              foreach ($staffelpreisema as $k => $v) {
                $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['staffelpreise'][] = array('ab_menge' => $v['ab_menge'], 'preis' => $v['preis'], 'bruttopreis' => $v['preis'] * $steuer, 'waehrung' => ($v['waehrung'] ? $v['waehrung'] : 'EUR'));
                if ($v['id']) {
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['staffelpreise'][count($data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['staffelpreise']) - 1]['gruppenkennziffer'] = $v['kennziffer'];
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['staffelpreise'][count($data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['staffelpreise']) - 1]['gruppenname'] = $v['name'];
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['staffelpreise'][count($data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['staffelpreise']) - 1]['adresse'] = $v['adresse'];
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['staffelpreise'][count($data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['staffelpreise']) - 1]['art'] = $v['art'];
                  $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['staffelpreise'][count($data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['staffelpreise']) - 1]['gruppeextern'] = $v['extgruppename'];
                }
              }
            }

            $articleVariant = $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0];
            $articleVariant['artikel'] = $eigenschaft['artikel'];
            $data[$i]['artikel_varianten'][] = $articleVariant;
            unset($data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][0]['Dateien']);


            $query = sprintf('SELECT meoa.name AS wert,meoa.name_ext AS wert_ext, mega.name AS gruppe, mega.name_ext AS gruppe_ext
                    FROM matrixprodukt_optionen_zu_artikel AS moza
                    JOIN matrixprodukt_eigenschaftenoptionen_artikel AS meoa ON moza.option_id = meoa.id
                    JOIN matrixprodukt_eigenschaftengruppen_artikel AS mega ON mega.id = meoa.gruppe
                    WHERE moza.artikel=%d
                    ORDER BY mega.id, moza.id', $eigenschaft['artikel']);
            $matrixdaten = $this->app->DB->SelectArr($query);

            for ($iv = 0, $ivMax = count($matrixdaten); $iv < $ivMax; $iv++) {
              $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][$iv]['name'] = $matrixdaten[$iv]['gruppe'];
              $data[$i]['matrix_varianten']['artikel'][$eigenschaft['artikel']][$iv]['values'] = $matrixdaten[$iv]['wert'];
            }
          }

          $result = null;
          if(empty($data)) {
            continue;
          }
          if (!empty($lagerexport)) {
            $result = $this->sendlistlager($i, $id, $data);
          }
          if (!empty($artikelexport) && !$nurlager) {
            $result = $this->sendlist($i, $id, $data, true);
          }

        } while (count($data[$i]['matrix_varianten']['artikel'])>=5000);

        return $result;
      }

      if(!empty($loadElements['variants']) && $this->app->DB->Select("SELECT id FROM artikel WHERE variante_von = '$artikel' AND variante = 1 AND geloescht <> 1 AND nummer != 'DEL' LIMIT 1")) {
        if($this->app->DB->Select("SELECT id FROM artikel WHERE id = '$artikel' AND variante = 0 AND matrixprodukt=0 AND geloescht <> 1 AND nummer != 'DEL' LIMIT 1")){
          $data[$i]['artikel_varianten'] = [];
          $data[$i]['variantevorhanden'] = 1;
          $varianten = "SELECT * FROM artikel WHERE variante_von = '$artikel' AND variante = 1 AND geloescht <> 1 AND nummer != 'DEL'";
          $db = $this->app->Container->get('Database');
          foreach ($db->yieldAll($varianten) as $v) {
            $steuer = $steuermapping[$v['umsatzsteuer']];
            if($v['steuersatz'] != -1 && !empty($v['steuersatz'])){
              $steuer = ($v['steuersatz'] + 100) / 100;
            }
            if(empty($steuer)) {
              $steuer = $steuernormal;
            }
            $variantennettopreis = null;
            if(!empty($loadElements['standard_price'])){
              if($preisgruppe && method_exists($this->app->erp, 'GetVerkaufspreisGruppe')){
                $variantennettopreis = $this->app->erp->GetVerkaufspreisGruppe($v['id'], 1, $preisgruppe);
              }else{
                $variantennettopreis = $this->app->erp->GetVerkaufspreis($v['id'], 1, 0);
              }
            }
            if(!empty($loadElements['translations']) && $this->app->erp->ModulVorhanden('artikel_texte')){
              $sprachen = ['de','en'];
              foreach ($sprachen as $sprache){
                $query = sprintf("SELECT * FROM artikel_texte WHERE shop=%d AND sprache='%s' AND artikel=%d AND aktiv=1 LIMIT 1",
                  $id,strtoupper($sprache),$v['id']);
                $ersetzeStandardbeschreibung = $this->app->DB->SelectRow($query);
                if(!empty($ersetzeStandardbeschreibung)){
                  $v['name_'.$sprache] = $ersetzeStandardbeschreibung['name'];
                }
              }
            }
            $this->app->erp->RunHook('remote_send_article_list_pseudostorage', 3, $id, $v['id'], $v['pseudolager']);
            if(is_numeric($v['pseudolager']) && $v['pseudolager'] < 0) {
              $v['pseudolager'] = 0;
            }
            $projectStockId = $this->app->DB->Select("SELECT `id` FROM `projekt` WHERE `id` = '{$projekt}' AND `projektlager` = 1 LIMIT 1");
            $stock = (float)$this->app->erp->ArtikelAnzahlVerkaufbar(
              $v['id'],
              0,
              $projectStockId,
              $id,
              $lagergrundlage
            );
            if($stock < 0) {
              $stock = 0;
            }
            $data[$i]['artikel_varianten'][] = array('nummer' => $v['nummer'], 'name_de' => $v['name_de'], 'name_en' => $v['name_en'],'restmenge' => $v['restmenge'], 'gesperrt' => ($v['gesperrt']==1||$v['intern_gesperrt']==1?1:0),
              'artikel' => $v['id'],'zolltarifnummer' => $v['zolltarifnummer'],'ean' => $v['ean'],'gewicht' => $v['gewicht'], 'inaktiv' => $v['inaktiv'], 'uebersicht_de' => $v['uebersicht_de'],
              'lag' => $stock,
              'pseudolager' => $v['pseudolager'], 'pseudopreis' => $v['pseudopreis'], 'preis' => $variantennettopreis, 'bruttopreis' => $variantennettopreis * $steuer,
              'artikelnummer_fremdnummern' => $this->app->DB->SelectArr("SELECT * FROM artikelnummer_fremdnummern WHERE artikel = '" . $v['id'] . "' AND nummer <> '' AND shopid = '$id' AND aktiv = '1'"),
              'steuersatz' => ($steuer - 1) * 100);
            if(!empty($data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten'])-1]['artikelnummer_fremdnummern'])) {
              foreach($data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten'])-1]['artikelnummer_fremdnummern'] as $fkey => $fval) {
                $data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten'])-1]['artikelnummer_fremdnummern'][$fkey]['nummer'] = trim($fval['nummer']);
              }
            }
            if($eigenschaftenuebertragen && !empty($loadElements['properties'])){
              $varianteneigenschaften = $this->app->DB->SelectArr("SELECT e.name, ew.wert FROM artikeleigenschaften e INNER JOIN artikeleigenschaftenwerte ew ON e.id = ew.artikeleigenschaften
              WHERE ew.artikel = '".$v['id']."' AND e.name <> '' AND ew.wert <> '' AND e.geloescht <> 1 ORDER BY e.name");
              if(!empty($varianteneigenschaften)){
                foreach($varianteneigenschaften as $eigenschaft){
                  $data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten']) - 1]['eigenschaften'][] = array('name'=>$eigenschaft['name'], 'values'=>$eigenschaft['wert']);
                }
              }
              $eigenschaftenuebersetzungen = $this->app->DB->SelectArr("SELECT language_from, language_to, property_from, property_to,property_value_from,property_value_to FROM article_property_translation WHERE article_id = '".$v['id']."'");
              $data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten']) - 1]['eigenschaftenuebersetzungen'] = $eigenschaftenuebersetzungen;
            }

            if(!empty($freifelder) && !empty($loadElements['free_fields']))
            {
              foreach($freifelder as $freifeld)
              {
                if(method_exists($tmp,'Get'.ucfirst($freifeld['freifeld_wawi'])))
                {
                  $_funktionsname = 'Get'.ucfirst($freifeld['freifeld_wawi']);
                  $data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten']) - 1]['freifelder']['DE'][$freifeld['freifeld_shop']] = $v[$freifeld['freifeld_wawi']];
                }
              }
              //Freifeldübersetzungen
              $freifelduebersetzungen = $this->app->DB->SelectArr("SELECT sf.freifeld_shop, af.sprache, af.nummer, af.wert FROM shopexport_freifelder sf JOIN artikel_freifelder af ON sf.freifeld_wawi = CONCAT('freifeld',af.nummer) WHERE sf.shop = '$id' AND af.artikel = '".$v['id']."' AND sf.aktiv = 1 AND sf.freifeld_wawi <> '' AND sf.freifeld_shop <> '' AND af.wert <> ''");
              if(!empty($freifelduebersetzungen)){
                foreach ($freifelduebersetzungen as $ffukey => $ffuvalue) {
                  $data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten']) - 1]['freifelder'][$ffuvalue['sprache']][] = array('mapping' => $ffuvalue['freifeld_shop'], 'freifeldnummer' => $ffuvalue['nummer'], 'wert' => $ffuvalue['wert']);
                }
              }
            }
            if ($shopbilderuebertragen && !empty($loadElements['pictures'])) {
              $dateien = $this->getImagesForArticle($v['id']);
              if (!empty($dateien)) {
                foreach ($dateien as $datei) {
                  $filename = $this->app->erp->GetDateiName($datei['id']);
                  $path_info = pathinfo($filename);
                  $data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten']) - 1]['Dateien']['datei'][] = base64_encode($this->app->erp->GetDatei($datei['id']));
                  $data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten']) - 1]['Dateien']['filename'][] = $filename;
                  $data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten']) - 1]['Dateien']['extension'][] = $path_info['extension'];
                  $data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten']) - 1]['Dateien']['titel'][] = $datei['titel'];
                  $data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten']) - 1]['Dateien']['beschreibung'][] = $datei['beschreibung'];
                  $data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten']) - 1]['Dateien']['stichwort'][] = $datei['subjekt'];
                  $data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten']) - 1]['Dateien']['id'][] = $datei['id'];
                  $data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten']) - 1]['Dateien']['version'][] = $datei['version'];
                  $data[$i]['artikel_varianten'][count($data[$i]['artikel_varianten']) - 1]['Dateien']['extid'][] = $this->GetShopexportMappingExt($id, 'datei', $datei['id']);
                }
              }
            }
          }
        }
      }else{
        $data[$i]['variantevorhanden'] = 0;
      }

      $result = null;
      if(empty($data)){
        continue;
      }
      if(!empty($lagerexport)) {
        $result = $this->sendlistlager($i, $id, $data);
      }
      if (!empty($artikelexport) && !$nurlager) {
        $result = $this->sendlist($i, $id, $data, true);
      }
    }

    return $result;
  }

    /**
     * @param int $articleId
     * @return array|null
     */
  protected function getImagesForArticle($articleId){
      $query = sprintf("SELECT d.id AS `id`, dv.id AS `vid`, d.titel, d.beschreibung, ds.subjekt, ds.sort, dv.version AS `version`
                FROM `datei_stichwoerter` AS `ds` 
                INNER JOIN `datei` AS `d` ON ds.datei = d.id  
                INNER JOIN `datei_version` AS `dv` ON dv.datei = ds.datei
                INNER JOIN (SELECT MAX(`version`) AS `version`, `datei` FROM `datei_version` GROUP BY `datei`) AS `dvm` ON dvm.datei = dv.datei AND dvm.version = dv.version
                WHERE ds.parameter = %d AND ds.objekt like 'Artikel' AND ds.subjekt LIKE 'Shopbild' AND d.geloescht = 0
                ORDER BY ds.sort", $articleId);

      return $this->app->DB->SelectArr($query);
  }

  protected function sendlistlager($i,$id,$data){
    $data2 = $data;
    foreach ($data2 as $key => $value){
      $data2[$key]['artikel'] = $value['artikelid'];
    }
    $result =  $this->RemoteCommand($id,'sendlistlager',$data2);
    $this->app->DB->Update(
      sprintf(
        'UPDATE artikel_onlineshops SET last_storage_transfer = NOW() WHERE artikel = %d AND shop = %d',
        $data2[$i]['artikel'], $id
      )
    );
    return $result;
  }

  protected function sendlist($i,$id,$data,$isLagerExported){
    /** @var Shopexport $objShopexport */
    $objShopexport = $this->app->loadModule('shopexport');
    $changedHash = $objShopexport->hasArticleHashChanged($data[0]['artikel'], $id);
    $hash = $changedHash['hash'];
    //$changedHash = $changedHash['changed'];

    $result = $this->RemoteCommand($id,'sendlist',$data);
    $checkAo = $this->app->DB->Select(
      sprintf(
        'SELECT id FROM artikel_onlineshops WHERE artikel = %d AND shop=%d LIMIT 1',
        $data[0]['artikel'], $id
      )
    );
    if(empty($checkAo)) {
      $this->app->DB->Insert(
        sprintf(
          'INSERT INTO artikel_onlineshops (artikel, shop, aktiv, ausartikel) 
            VALUES (%d, %d, 1, 1) ',
          $data[0]['artikel'], $id
        )
      );
    }
    $this->app->DB->Update(
      sprintf(
        "UPDATE artikel_onlineshops 
          SET last_article_transfer = NOW(), last_article_hash = '%s' 
          WHERE artikel = %d AND shop = %d",
        $this->app->DB->real_escape_string($hash) ,$data[0]['artikel'], $id
      )
    );
    if(!empty($result) && is_array($result) && !empty($result['new']))
    {
      foreach($result['new'] as $artikelid => $fremdnummer)
      {
        $artikelid = (int)$artikelid;
        $artikelnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$artikelid' LIMIT 1");
        if($artikelid > 0 && $artikelnummer != trim($fremdnummer) &&
          ($this->app->DB->Select("SELECT id FROM artikel WHERE id = '$artikelid' AND (shop = '$id' OR shop2 = '$id' OR shop3 = '$id') LIMIT 1") ||
            $this->app->DB->Select("SELECT id FROM artikel_onlineshops WHERE artikel = '$artikelid' AND aktiv = 1")
          ) && trim($fremdnummer) !== '')
        {
          //Nur falls Artikel zum Shop passt und keine aktive Fremdnummer exisitert.
          if(!$this->app->DB->Select("SELECT id FROM `artikelnummer_fremdnummern` WHERE artikel = '$artikelid' AND shopid = '$id' AND nummer <> '' AND (aktiv = 1 OR nummer = '".trim($this->app->DB->real_escape_string($fremdnummer))."') LIMIT 1 "))
          {
            $this->app->DB->Insert("INSERT INTO `artikelnummer_fremdnummern` (artikel, bezeichnung, nummer, shopid, bearbeiter, zeitstempel, aktiv)
              VALUES ('$artikelid','Erstellt durch Artikelexport','".trim($this->app->DB->real_escape_string($fremdnummer))."','$id','".((isset($this->app->User)&& method_exists($this->app->User,'GetName'))?$this->app->DB->real_escape_string($this->app->User->GetName()):'Cronjob') ."',now(),0)
              ");
          }
        }
      }
      if(isset($result['anzahl']))
      {
        $result = $result['anzahl'];
      }//Altes Verhalten
    }
    if(!$isLagerExported){
      $data2 = $data;
      foreach ($data2 as $key => $value){
        $data2[$key]['artikel'] = $value['artikelid'];
      }
      $this->RemoteCommand($id,'sendlistlager',$data2);
      $this->app->DB->Update(
        sprintf(
          'UPDATE artikel_onlineshops SET last_storage_transfer = NOW() WHERE artikel = %d AND shop = %d',
          $data2[$i]['artikel'], $id
        )
      );
    }
    return $result;
  }

  public function getDataToSendForUpdateOrder(int $shopId, int $orderId): array
  {
    $orderArr = $this->app->DB->SelectRow("SELECT * FROM `auftrag` WHERE `id` = {$orderId} LIMIT 1");
    $status = $orderArr['status'];
    $zahlungsweise = $orderArr['zahlungsweise'];
    $shopextid = $orderArr['shopextid'];
    $internet = $orderArr['internet'];
    $deliveryNoteArr = $this->app->DB->SelectRow(
      "SELECT `id`, `versandart` FROM `lieferschein` WHERE `auftragid` = {$orderId} LIMIT 1"
    );
    $trackingArr = null;
    $versandart = '';
    $tracking = '';
    $shippingProduct = null;
    if(!empty($deliveryNoteArr)) {
      $deliveryNoteId = $deliveryNoteArr['id'];
      $versandart = $deliveryNoteArr['versandart'];
      $query =
        "SELECT *
        FROM `shopexport_versandarten` 
        WHERE `aktiv`=1 AND `versandart_wawision` = '{$versandart}' AND `shop` = {$shopId} AND `versandart_wawision` <> '' 
        LIMIT 1";
      $shippingMapping = $this->app->DB->SelectRow($query);
      $versandartAusgehend = $shippingMapping['versandart_ausgehend'] ?? null;
      $shippingProduct = $shippingMapping['produkt_ausgehend'] ?? null;

      if(!empty($versandartAusgehend)){
        $versandart = $versandartAusgehend;
      }
      $trackingArr = $this->app->DB->SelectPairs(
        sprintf(
          "SELECT `id`, `tracking` 
          FROM `versand` 
          WHERE `lieferschein` = {$deliveryNoteId} AND `tracking` <> '' 
          ORDER BY `id` DESC"
        )
      );
      $tracking = '';
      if(!empty($trackingArr)) {
        $tracking = reset($trackingArr);
      }

      $positionen = $this->app->DB->SelectArr(
          "SELECT ap.webid, trim(lp.geliefert)+0 AS `geliefert`, trim(lp.menge)+0 AS `menge`, lp.id 
          FROM `lieferschein_position` AS `lp` 
          INNER JOIN `lieferschein` AS `l` ON l.id = lp.lieferschein 
          INNER JOIN `auftrag` AS `a` ON a.id = l.auftragid 
          INNER JOIN `auftrag_position` AS `ap` ON ap.id = lp.auftrag_position_id 
          WHERE l.id = {$deliveryNoteId} AND ap.webid <> '' "
      );
      $allPositions = false;
      if(!empty($positionen)) {
        $allPositions = true;
        foreach($positionen as $position) {
          if($position['geliefert'] > 0)  {
            $itemlist[] = array('webid'=>$position['webid'],'quantity'=>$position['geliefert']);
            if($position['geliefert'] < $position['menge']) {
              $allPositions = false;
            }
          }
          elseif($this->app->DB->Select("SELECT trim(sum(geliefert))+0 
            FROM lieferschein_position 
            WHERE explodiert_parent = '".$position['id']."' AND lieferschein = '$deliveryNoteId'")) {
            $itemlist[] = array('webid'=>$position['webid'],'quantity'=>$position['menge']);
          }
          else {
            $allPositions = false;
          }
        }
        if($allPositions && count($itemlist) <
          $this->app->DB->Select(
            sprintf('SELECT count(id) FROM auftrag_position WHERE auftrag = %d', $orderId)
          )
        ) {
          $allPositions = false;
        }
      }
    }
    if(!empty($itemlist)) {
      $data['itemlist'] = $itemlist;
      if($allPositions) {
        $data['allpositions'] = 1;
      }
    }

    $data['orderId'] = $orderId;
    $data['auftrag'] = $shopextid;
    $data['internet'] = $internet;
    $data['zahlungsweise'] = $zahlungsweise;
    $data['versandart'] = $versandart;
    if(!empty($trackingArr)) {
      $data['trackinglist'] = $trackingArr;
    }
    if($status==='abgeschlossen') {
      $data['versand']='1';
      $data['zahlung']='1';
      if($shippingProduct !== null) {
        $data['shipping_product'] = $shippingProduct;
      }
      if($tracking!='') {
        $data['tracking']=$tracking;
        $lastShippingId = (int)$this->app->DB->Select(
          sprintf(
            "SELECT `id` FROM `versand` WHERE `lieferschein` = %d AND `lieferschein` > 0 
            ORDER BY `id` DESC LIMIT 1",
            $deliveryNoteId
          )
        );
        $trackinglink = $lastShippingId > 0 && method_exists($this->app->erp,'GetTrackinglink')
          ?$this->app->erp->GetTrackinglink($lastShippingId):'';
        if($trackinglink) {
          $data['trackinglink'] = $trackinglink;
          if(!empty($trackingArr)) {
            foreach($trackingArr as $versandId => $track) {
              $data['trackinglinklist'][$versandId] = $this->app->erp->GetTrackinglink($versandId);
            }
          }
        }
        $trackinglinkRaw = $lastShippingId > 0 && method_exists($this->app->erp,'GetTrackingRawLink')
          ?$this->app->erp->GetTrackingRawLink($lastShippingId):'';
        if(!empty($trackinglinkRaw)) {
          $data['trackinglinkraw'] = $trackinglinkRaw;
        }
      }
    }

    return $data;
  }

  /**
   * @param int $shopId
   * @param int $orderId
   *
   * @throws Exception
   */
  public function RemoteUpdateAuftrag($shopId, $orderId)
  {
    $data = $this->getDataToSendForUpdateOrder((int)$shopId, (int)$orderId);
    if($data['versand']=='1' || $data['zahlung']=='1')
    {
      $bearbeiter = 'Cronjob';
      if(isset($this->app->User)){
         $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
      }

      $response = $this->RemoteCommand($shopId, 'updateauftrag', $data);
      if($response instanceOf ShopConnectorResponseInterface && !$response->isSuccessful()){
          $query = sprintf('UPDATE `auftrag`
            SET `shop_status_update_attempt` = `shop_status_update_attempt` + 1,
                `shop_status_update_last_attempt_at` = NOW()
            WHERE `id` = %d', $orderId);
          $this->app->DB->Update($query);

          $this->app->erp->AuftragProtokoll($orderId, 'Versandmeldung an Shop fehlgeschlagen', $bearbeiter);
          $this->app->erp->Logfile('Versandmeldung an Shop fehlgeschlagen', print_r([
            'orderId' => $orderId,
            'shopId'  => $shopId,
            'message' => $response->getMessage()],true));

          return;
      }

      $this->app->erp->AuftragProtokoll($orderId, 'Versandmeldung an Shop &uuml;bertragen', $bearbeiter);
      $this->app->DB->Update("UPDATE `auftrag` SET `shopextstatus` = 'abgeschlossen' WHERE `id` = $orderId LIMIT 1");
    }
  }

  /**
   * @param int    $shopId
   * @param string $auftrag
   *
   * @param string $internet
   *
   * @throws Exception
   */
  public function RemoteDeleteAuftrag($shopId, $auftrag, $internet='')
  {
    $data['auftrag'] = $auftrag;
    $data['internet'] = $internet;
    $this->RemoteCommand($shopId,'deleteauftrag',$data);
  }

  /**
   * @param int $shopId
   * @param int $orderId
   *
   * @throws Exception
   */
  public function RemoteStorniereAuftrag($shopId, $orderId)
  {
    $orderArr = $this->app->DB->SelectRow("SELECT shopextid, internet FROM auftrag WHERE id='$orderId' LIMIT 1");
    if(empty($orderArr)) {
      return;
    }
    $shopextid = $orderArr['shopextid'];
    $internet = $orderArr['internet'];
    $data['auftrag'] = $shopextid;
    $data['internet'] = $internet;
    
    $this->app->DB->Insert("
    INSERT INTO auftrag_protokoll (auftrag, zeit, bearbeiter, grund) 
    VALUES ($orderId,now(),'".(isset($this->app->User)?$this->app->DB->real_escape_string($this->app->User->GetName()):'Cronjob')."',
    'Stonierung an Shop &uuml;bertragen')"
    );
    $this->RemoteCommand($shopId,'storniereauftrag',$data);
    $shopextstatus='storniert';
    $this->app->DB->Update("UPDATE auftrag SET shopextstatus='$shopextstatus' WHERE id='$orderId' LIMIT 1");
  }

  /**
   * @param int $shopId
   * @param int $fileId
   *
   * @return mixed
   * @throws Exception
   */
  public function RemoteDeleteFile($shopId, $fileId)
  {
    $inhalt = $this->app->erp->GetDatei($fileId);
    $fileArr = $this->app->DB->SelectRow("SELECT titel,beschreibung FROM datei WHERE id='$fileId' LIMIT 1");
    $titel = $fileArr['titel'];
    $beschreibung = $fileArr['beschreibung'];

    $data['datei'] = $fileId;
    //$data['checksum'] = md5($inhalt);
    $data['checksum'] = md5($inhalt.$titel.$beschreibung);
    return $this->RemoteCommand($shopId,'deletefile',$data);
  }

  /**
   * @param int $articleId
   * @param int $shopId
   */
  public function RemoteUpdateFilesArtikel($articleId, $shopId)
  {
    $files = $this->app->DB->SelectArr("SELECT DISTINCT ds.datei 
      FROM datei_stichwoerter ds, datei d, artikel a 
      WHERE d.id=ds.datei AND (ds.subjekt='Shopbild' OR ds.subjekt='Gruppenbild') AND ((ds.objekt='Artikel' AND 
      ds.parameter=a.id)  OR (ds.objekt='Kampangen' AND ds.parameter='$shopId')) AND 
      d.firma='".$this->app->User->GetFirma()."' AND a.shop='$shopId' AND a.id='$articleId'"
    );

    $tmp = $this->app->remote->RemoteGetFileListArticle($shopId,$articleId);
    if(!empty($tmp)){
      foreach ($tmp as $row) {
        $checkarray[$row['datei']] = $row['checksum'];
      }
    }

    if(!empty($files)){
      foreach($files as $file) {
        $fid = $file['datei'];
        $geloescht = $this->app->DB->Select("SELECT geloescht FROM datei WHERE id='$fid' LIMIT 1");
        if($geloescht){
          $this->app->remote->RemoteDeleteFile($shopId, $fid);
          $checkarray[$fid] = 'delete';
        }else{
          $md5 = !empty($checkarray[$fid]) ? (String)$checkarray[$fid] : '';
          $md5file = md5($this->app->erp->GetDatei($fid));
          if($md5 !== $md5file){
            $this->app->remote->RemoteSendFile($shopId, $fid);
            $this->app->remote->RemoteAddFileSubject($shopId, $fid);
            $checkarray[$fid] = 'update';
          }
        }
        $checkarray[$fid] = 'mark';
      }
    }
    if(!empty($checkarray))
    {
      foreach($checkarray as $key=>$value)
      {   
        if($checkarray[$key]!=='mark' && $checkarray[$key]!=='delete' && $checkarray[$key]!=='update')
        {
          $this->app->remote->RemoteDeleteFile($shopId,$key);
        }
      }   
    }
  }


  /**
   * @param int $shopId
   * @param int $fileId
   *
   * @return mixed|string
   * @throws Exception
   */
  public function RemoteSendFile($shopId, $fileId)
  {
    // sende stichwoerter
    $fileArr = $this->app->DB->SelectRow("SELECT geloescht, titel, beschreibung FROM datei WHERE id='$fileId' LIMIT 1");
    if(empty($fileArr)) {
      return '';
    }
    $geloescht = $fileArr['geloescht'];
    $titel = $fileArr['titel'];
    $beschreibung = $fileArr['beschreibung'];

    $inhalt = $this->app->erp->GetDatei($fileId);
    $data['datei'] = $fileId;
    $data['titel'] = $titel;
    $data['beschreibung'] =$beschreibung; 
    $data['inhalt'] = base64_encode($inhalt);
    $data['checksum'] = md5($inhalt.$titel.$beschreibung);
    if(!$geloescht){
      return $this->RemoteCommand($shopId, 'sendfile', $data);
    }
    return '';
  }

  /**
   * @param int $shopId
   * @param int $fileId
   *
   * @throws Exception
   */
  public function RemoteAddFileSubject($shopId, $fileId)
  {
    // sende stichwoerter
    $fileList = $this->app->DB->SelectArr("SELECT subjekt, parameter 
      FROM datei_stichwoerter 
      WHERE (objekt='Artikel' OR objekt='Kampangen') AND datei='$fileId'"
    );
    if(empty($fileList)) {
      return;
    }

    foreach($fileList as $fileRow) {
      $geloescht = $this->app->DB->Select("SELECT geloescht FROM datei WHERE id='$fileId' LIMIT 1");
      $stichwort['subjekt'] = $fileRow['subjekt'];
      $stichwort['artikel'] = $fileRow['parameter'];
      $stichwort['datei'] =  $fileId;
      if(!$geloescht){
        $this->RemoteCommand($shopId, 'addfilesubjekt', $stichwort);
      }
    }
  }

  /**
   * @param int   $shopId
   * @param array $artikel
   *
   * @return mixed
   * @throws Exception
   */
  public function RemoteDeleteArticle($shopId,$artikel)
  {
    return $this->RemoteCommand($shopId,'deletearticle',$artikel);
  }

  /**
   * @param int   $shopId
   * @param array $partner
   *
   * @return mixed
   * @throws Exception
   */
  public function RemoteSendPartner($shopId, $partner)
  {
    return $this->RemoteCommand($shopId, 'partnerlist', $partner);
  }

  /**
   * @param object $obj
   * @param string $methodname
   *
   * @return string
   */
  public function getMethod($obj, $methodname)
  {
    $methodname = trim((String)$methodname);
    if($methodname === '')
    {
      return '';
    }
    if(!$obj)
    {
      return '';
    }
    if(method_exists($obj, $methodname))
    {
      return $methodname;
    }
    $methods = get_class_methods($obj);
    $classname = get_class($obj);
    foreach($methods as $v)
    {
      if(strtolower($v) === 'import'.strtolower($methodname))
      {
        return $v;
      }
      if(strtolower($v) === strtolower($classname.$methodname))
      {
        return $v;
      }
    }
    foreach($methods as $v)
    {
      if(strtolower($v) === strtolower($methodname))
      {
        return $v;
      }
    }
    return '';
  }

  /**
   * @param int          $id
   * @param string       $action
   * @param string|array $data
   *
   * @return mixed
   * @throws Exception
   */
  public function RemoteCommand($id,$action,$data='')
  {
    $challenge = '';
    $shoptyp = $this->app->DB->Select("SELECT shoptyp FROM shopexport WHERE id='$id' LIMIT 1");
    $modulename = trim($this->app->DB->Select("SELECT modulename FROM shopexport WHERE id='$id' LIMIT 1"),'.');
    $isActionAuth = $action === 'auth';
    if($shoptyp === 'custom')
    {
      if($modulename != '')
      {
        
        $file = dirname(__DIR__) . '/plugins/external/shopimporter/'.$modulename;
        $classa = explode('_',str_replace('_'.$id.'.php','', $modulename));
        foreach($classa as $k => $v)
        {
          $classa[$k] = ucfirst($v);
        }
        $class = implode('_', $classa);
        if($this->app->DB->Select("SELECT aktiv FROM shopexport WHERE id = '$id' LIMIT 1"))
        {
          if(file_exists($file))
          {
            include_once($file);
            if(class_exists($class)){
              $obj = new $class($this->app, true);
              if($obj){
                if(method_exists($obj,'getKonfig'))
                {
                  $obj->getKonfig($id, $data);
                }
                $method = $this->getMethod($obj, $action);
                if(method_exists($obj,$method))
                {
                  $ret = $obj->$method();
                  if(!empty($this->app->stringcleaner)){
                    $this->app->stringcleaner->XMLArray_clean($ret);
                  }
                }elseif($isActionAuth)
                {
                  return 'Fehler: Importer konnte nicht initialisiert werden';
                }
              }elseif($isActionAuth)
              {
                return 'Fehler: Importer konnte nicht initialisiert werden';
              }
            }elseif($isActionAuth)
            {
              return 'Fehler: Importer konnte nicht initialisiert werden';
            }
          }elseif($isActionAuth)
          {
            return 'Fehler: Datei '.$file.' existiert nicht';
          }
        }elseif($isActionAuth) {
          return 'Fehler: Schnittstelle nicht aktiv';
        }
      }
      return '';
    }
    if($shoptyp === 'intern')
    {
      if($modulename != '')
      {
        if($this->app->erp->ModulVorhanden($modulename))
        {
          $obj = $this->app->erp->LoadModul($modulename);
          if($obj){
            if(method_exists($obj,'getKonfig'))
            {
              $obj->getKonfig($id, $data);
            }
            $method = 'Import'.$action;
            if(method_exists($obj,$method)) {
              try {
                $ret = $obj->$method();
              }
              catch(Exception $e) {
                if($isActionAuth) {
                  return 'Fehler: '.$e->getMessage();
                }
                return '';
              }

              if(!empty($this->app->stringcleaner)){
                $this->app->stringcleaner->XMLArray_clean($ret);
              }
              $this->parseReturn($ret, $id, $action);
              return $ret;
            }
          }elseif($isActionAuth)
          {
            return 'Fehler: Importer konnte nicht initialisiert werden';
          }
        }elseif($isActionAuth)
        {
          return 'Fehler: Dieses Modul ist nicht verf&uuml;gbar';
        }
      }elseif($isActionAuth)
      {
        return 'Fehler: Kein Modul vorhanden';
      }
      return '';
    }
    $shopexport = $this->app->DB->SelectRow("SELECT * FROM shopexport WHERE id='$id' LIMIT 1");
    if($shopexport){
      if($shopexport['shoptyp'] === 'intern' || $shopexport['shoptyp'] === 'custom'){
        return '';
      }
      $token = $shopexport['token'];
      $url = $shopexport['url'];
      $z = $shopexport['passwort'];
      $bezeichnung = $shopexport['bezeichnung'];
    }
    else {
      $token = '';
      $z = '';
      $url = '';
    }
    if($isActionAuth) {
      if($token === '' || strlen($z) < 32 || $url === '') {
        return 'Fehler: Bitte Zugangsdaten pr&uuml;fen';
      }
    }
    elseif($token === '' || strlen($z) < 32 || $url === '' || !$this->app->DB->Select("SELECT id FROM shopexport WHERE id = '$id' AND aktiv = 1 LIMIT 1")){
      return '';
    }
    
    $tmp = parse_url($url);
    $tmp['host'] = rtrim($tmp['host'],'/');
    $tmp['path'] = rtrim($tmp['path'],'/').'/';

    $aes = new AES($z);
    $token = base64_encode($aes->encrypt(serialize($token)));
    $client = new HttpClient($tmp['host'], stripos($url,'https') === 0?443:80);
    $geturl = $tmp['path'].'index.php?module=import&action='.$action.'&challenge='.(isset($challenge)?$challenge:'');
    //Kein Fragezeichen vor module=import...
    if(false !== stripos($bezeichnung, 'woocommerce')) {
      $geturl = $tmp['path'].'module=import&action='.$action.'&challenge='.(isset($challenge)?$challenge:'');
    }
    if(false !== stripos($bezeichnung, 'shopware plugin')) {
      $geturl = $tmp['path'].'wawisionimporter/?smodule=import&saction='.$action.'&challenge='.(isset($challenge)?$challenge:'');
    }

    $post_data['token'] = $token;
    $post_data['data'] = base64_encode(serialize($data));
    $client->timeout = 120;
    if(!$client->post($geturl,$post_data)) {
      $this->app->erp->LogFile(mysqli_real_escape_string($this->app->DB->connection,'An error occurred: '.$client->getError()));
      throw new Exception('An error occurred: '.$client->getError());
      //return 'Netzwerkverbindung von WaWison zu Shopimporter fehlgeschlagen: '.$client->getError();
    }
    $ret = unserialize(base64_decode($client->getContent()));
    if(!empty($this->app->stringcleaner)){
      $this->app->stringcleaner->XMLArray_clean($ret);
    }
    $this->parseReturn($ret, $id, $action);
    return $ret;
  }

  /**
   * @param mixed  $ret
   * @param int    $id
   * @param string $action
   */
  public function parseReturn($ret, $id, $action)
  {
    if($action === 'getarticlelist') {
      if(empty($ret)) {
        return;
      }
      $anz = 0;
      if(empty($ret['errors'])) {
        foreach($ret as $v) {
          $anz++;
          $this->app->DB->Insert("INSERT INTO shopexport_getarticles (shop, nummer) VALUES ('$id', '".$this->app->DB->real_escape_string($v)."')");
        }
        if(!$this->app->DB->Select("SELECT id FROM `prozessstarter` WHERE aktiv = 1 AND parameter = 'getarticles' LIMIT 1")) {
          $this->app->DB->Update("UPDATE `prozessstarter` SET aktiv = 1 WHERE parameter = 'getarticles' LIMIT 1");
        }
        $this->app->DB->Update("UPDATE `prozessstarter` SET letzteausfuerhung = DATE_SUB(now(), INTERVAL periode MINUTE) WHERE aktiv = 1 AND parameter = 'getarticles' AND mutex = 0 LIMIT 1");
      }
      $this->app->erp->SetKonfigurationValue('artikelimportanzahl_'.$id, $anz);
    }
  }

  /**
   * @param int          $id
   * @param string       $action
   * @param string|array $data
   *
   * @return mixed|string
   * @throws Exception
   */
  public function RemoteCommandAES($id,$action,$data='')
  {
    $challenge = '';
    $shopexport = $this->app->DB->SelectRow("SELECT * FROM shopexport WHERE id='$id' LIMIT 1");
    if(!empty($shopexport)){
      if($shopexport['shoptyp'] === 'intern' || $shopexport['shoptyp'] === 'custom'){
        return '';
      }
      $token = $shopexport['token'];
      $url = $shopexport['url'];
      $z = $shopexport['passwort'];
      if($z === '' || $token === '' || $url === '')
      {
        throw new Exception('Der ImportKey/ImportToken/ImportUrl ist leer');
      }
    }
    $tmp = parse_url($url);

    $aes = new AES($z);
    $token = base64_encode($aes->encrypt(serialize($token)));

    $client = new HttpClient($tmp['host']);
    $geturl = $tmp['path'].'index.php?module=import&action='.$action.'&challenge='.$challenge;

    $post_data['token'] = $token;
    $post_data['data'] = base64_encode($aes->encrypt(serialize($data)));

    if(!$client->post($geturl,$post_data))
    {
      $this->app->erp->LogFile(mysqli_real_escape_string($this->app->DB->connection,'An error occurred: '.$client->getError()));
      throw new Exception('An error occurred: '.$client->getError());
    }
    return unserialize($aes->decrypt(base64_decode($client->getContent())));
  }
}
