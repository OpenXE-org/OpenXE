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
use Xentral\Modules\TransferSmartyTemplate\TransferSmartyTemplate;

include '_gen/shopexport.php';

class Onlineshops extends GenShopexport {
  /** @var Application $app */
  public $app;

  /** @var TransferSmartyTemplate $tmpl */
  public $tmpl;

  const MODULE_NAME = 'Onlineshop';

  public $stylesheet = [
    './classes/Modules/Onlineshop/www/css/feature.css',
    './classes/Modules/Onlineshop/www/css/shopsettings.css',
    './classes/Modules/Appstore/www/css/tilegrid.css',
  ];

  public $javascript = [
    './classes/Modules/Onlineshop/www/js/onlineshop_appnew.js',
    './classes/Modules/Onlineshop/www/js/shopsettings.js',
    './classes/Modules/Onlineshop/www/js/onlineshop_create.js',
  ];

  /** @var string $smartydir */
  protected $smartydir;

  /** @var string $templateDir */
  protected $templateDir;

  /** @var string $segment */
  protected $segment;

  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public static function TableSearch($app, $name, $erlaubtevars)
  {
    switch($name)
    {
      case "shopexport_log":
        $id = $app->Secure->GetGET('id');
        $nurfehler = $app->YUI->TableSearchFilter($name, 10, 'nurfehler', 0,0,'checkbox');
        $auftraege = $app->YUI->TableSearchFilter($name, 22, 'auftraege', 0,0,'checkbox');
        $aenderungen = $app->YUI->TableSearchFilter($name, 28, 'aenderungen', 0,0,'checkbox');
        $allowed['onlineshops'] = array('edit');
        $heading = array('','Datum','Typ','Wert','Wert 2','Bearbeiter','');
        $width = array('1%','20%','10%','20%','20%','15%','1%');
        $findcols = array('open', 'sl.zeitstempel','sl.typ','sl.parameter1','sl.parameter2','sl.bearbeiter', 'sl.tid');
        $searchsql = array('sl.zeitstempel','sl.typ','sl.parameter1','sl.parameter2','sl.bearbeiter');
        
        $defaultorder = 2; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;
        
        if($auftraege)
        {
          $arr[]="(SELECT id,logdatei as zeitstempel, 'Auftrag' as typ,extid as  parameter1,bestellnummer as parameter2,bearbeiter, concat('2-',id) as tid FROM shopimport_auftraege WHERE shopid = '$id')";
        }else{
          $arr[] = "(SELECT id, zeitstempel, typ, parameter1,parameter2,bearbeiter, concat('1-',id) as tid FROM shopexport_log WHERE shopid = '$id')";
        }

        if($aenderungen){
          $arr[] = "(SELECT id, creation_timestamp, 'Einstellungsänderung',message, plaindiff,username, concat('3-',id) as tid FROM shopexport_change_log WHERE shop_id = '$id')";
        }
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS sl.id, '<img src=./themes/{$app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, sl.zeitstempel, sl.typ, substring(sl.parameter1,1,50), sl.parameter2,sl.bearbeiter, sl.tid
        FROM 
        (
          ".implode(" UNION ALL ", $arr)."
        )
        sl
        ";
        $menucol = 6;
        $moreinfo = true;
        $doppelteids = true;
        //$where = "shopid = '$id'";
        $where = " 1 ";
        $menu = "<span style=\"display:none;\">id=%value%</span>";
        if($nurfehler)$where .= " AND sl.typ like 'fehler' ";
        
      break;
      case "shopexport_sprachen":
        $id = $app->Secure->GetGET('id');
        $allowed['onlineshops'] = array('edit');
        $heading = array('Land','Projekt','Sprache','Aktiv','Men&uuml;');
        $width = array('10%','20%','20%','5%','1%');
        $findcols = array('s.land', 'p.abkuerzung','s.sprache','s.aktiv','s.id');
        $searchsql = array('s.land', 'p.abkuerzung','s.sprache','s.aktiv');
        
        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc = 0;
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, s.land, p.abkuerzung, s.sprache, if(s.aktiv = 1, 'ja','-'), s.id
        FROM 
        shopexport_sprachen s LEFT JOIN projekt p ON s.projekt = p.id
        ";
        $where = " s.shop = '$id'";
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"#\" onclick=sprachenEdit(%value%)>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=onlineshops&action=sprachendelete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";
      break;
      case "shopexport_kundengruppen":
        $id = $app->Secure->GetGET('id');
        $allowed['onlineshops'] = array('edit');
        $heading = array('Kennziffer','Kundengruppe','Bezeichnung Shop','Projekt','Rolle','Neukunden zuweisen','Aktiv','Men&uuml;');
        $width = array('5%','40%','30%','10%','5%','%5','4%','1%');
        $findcols = array('g.name', 'g.kennziffer','s.extgruppename','s.projekt','s.type',"if(s.apply_to_new_customers = 1, 'ja','-')","if(s.aktiv = 1, 'ja','-')",'s.id');
        $searchsql = array('g.name', 'g.kennziffer','s.extgruppename','s.projekt','s.type',"if(s.apply_to_new_customers = 1, 'ja','-')","if(s.aktiv = 1, 'ja','-')");

        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc = 0;

        $sql = "SELECT SQL_CALC_FOUND_ROWS 
           s.id, 
           g.kennziffer, 
           g.name, 
           s.extgruppename, 
           IF(s.projekt='','',p.name), 
           s.type,
           IF(s.apply_to_new_customers=1,'ja','-'), 
           IF(s.aktiv = 1, 'ja','-'), 
           s.id
        FROM shopexport_kundengruppen s 
        LEFT JOIN projekt p ON s.projekt = p.id
        LEFT JOIN gruppen g ON s.gruppeid = g.id
        ";
        $where = " s.shopid = '$id' AND g.aktiv = 1";
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"#\" onclick=kundengruppenEdit(%value%)>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;" . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=onlineshops&action=kundengruppendelete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";
        break;
    }
    
    $erg = [];

    foreach($erlaubtevars as $k => $v) {
      if(isset($$v)) {
        $erg[$v] = $$v;
      }
    }

    return $erg; 
  }

  /**
   * Onlineshops constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    //parent::GenShopexport($app);
    $this->app=$app;
    if($intern) {
      return;
    }
    $this->tmpl = $this->app->Container->get('Template');
    $this->tmpl->setDefaultNamespace('Modules/Onlineshop');

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ShopexportCreate");
    $this->app->ActionHandler("edit","ShopexportEdit");
    $this->app->ActionHandler("list","ShopexportList");
    $this->app->ActionHandler("delete","ShopexportDelete");
    $this->app->ActionHandler("zahlweisedelete","ShopexportZahlweisedelete");
    $this->app->ActionHandler("versandartdelete","ShopexportVersandartdelete");
    $this->app->ActionHandler("freifelddelete","ShopexportFreifelddelete");
    $this->app->ActionHandler("subshopdelete","ShopexportSubshopdelete");
    $this->app->ActionHandler("sprachendelete","ShopexportSprachendelete");
    $this->app->ActionHandler("zahlweiseget","ShopexportZahlweiseget");
    $this->app->ActionHandler("zahlweisesave","ShopexportZahlweisesave");
    $this->app->ActionHandler("sprachenget","ShopexportSprachenget");
    $this->app->ActionHandler("sprachensave","ShopexportSprachensave");
    $this->app->ActionHandler("kundengruppenget","ShopexportKundengruppenget");
    $this->app->ActionHandler("kundengruppensave","ShopexportKundengruppensave");
    $this->app->ActionHandler("kundengruppeneditsave","ShopexportKundengruppensave");
    $this->app->ActionHandler("kundengruppendelete","ShopexportKundengruppendelete");
    $this->app->ActionHandler("zahlweiseeditsave","ShopexportZahlweiseeditsave");
    $this->app->ActionHandler("exportartikelbaum","ShopexportArtikelbaumexport");
    $this->app->ActionHandler("freifeldersave","ShopexportFreifeldsave");
    $this->app->ActionHandler("subshopsave","ShopexportSubshopsave");

    $this->app->ActionHandler("versandartget","ShopexportVersandartget");
    $this->app->ActionHandler("freifeldget","ShopexportFreifeldget");
    $this->app->ActionHandler("subshopget","ShopexportSubshopget");
    $this->app->ActionHandler("versandartsave","ShopexportVersandartsave");
    $this->app->ActionHandler("versandarteditsave","ShopexportVersandarteditsave");
    $this->app->ActionHandler("freifeldereditsave","ShopexportFreifeldereditsave");
    $this->app->ActionHandler("spracheneditsave","ShopexportSpracheneditsave");
    
    $this->app->ActionHandler("subshopeditsave","ShopexportSubshopeditsave");
    $this->app->ActionHandler("minidetail","ShopexportMinidetail");
    $this->app->ActionHandler("features","ShopexportFeatures");

    $this->app->ActionHandler('appnew', 'ShopexportAppNew');
    $this->app->ActionHandler('getapi', 'ShopexportGetApi');
    $this->app->ActionHandler('itemlink', 'ShopexportItemlink');
    $this->app->ActionHandler('orderlink', 'ShopexportOrderlink');

    $this->app->erp->Headlines('Shopexport');
    $this->app->ActionHandlerListen($app);
  }

  public function InstallResouces()
  {
    $resourcesDir = dirname(__DIR__, 2).'/classes/Modules/Onlineshop/resources';
    $cacheDir = dirname(__DIR__).'/cache/Onlineshop/resources';
    $resourcesSubDirs = ['usecasesSvgDe', 'usecasesSvgEn',];
    if(!is_dir($resourcesDir)) {
      return;
    }
    if(!is_dir($cacheDir) && !mkdir($cacheDir,0777, true) && !is_dir($cacheDir)) {
      return;
    }
    foreach($resourcesSubDirs as $subDir) {
      if(!is_dir($resourcesDir.'/'.$subDir)) {
        continue;
      }
      if(!is_dir($cacheDir.'/'.$subDir) && !mkdir($cacheDir.'/'.$subDir,0777, true) && !is_dir($cacheDir.'/'.$subDir)) {
        continue;
      }
      $handle = opendir($resourcesDir.'/'.$subDir);
      if(empty($handle)) {
        continue;
      }
      while($entry = readdir($handle)) {
        if(substr($entry, -4) === '.svg' && is_file($resourcesDir.'/'.$subDir.'/'.$entry)) {
          if(!is_file($cacheDir.'/'.$subDir.'/'.$entry)) {
            copy($resourcesDir.'/'.$subDir.'/'.$entry, $cacheDir.'/'.$subDir.'/'.$entry);
          }
        }
      }
      closedir($handle);
    }
  }

  /**
   * @param int $shopId
   *
   * @return array
   */
  public function genearteApiAccount($shopId)
  {
    $shop = $this->app->DB->SelectRow(
      sprintf(
        'SELECT se.`id`, se.`api_account_id`, ac.id AS `api_id`, `se`.bezeichnung
        FROM `shopexport` AS `se` 
        LEFT JOIN `api_account` AS `ac` ON se.api_account_id = ac.id  
        WHERE se.`id` = %d',
        $shopId
      )
    );
    if(empty($shop)) {
      return ['success' => false, 'error' => 'Shop nicht gefunden'];
    }
    if(!empty($shop['api_id'])) {
      return ['success' => false, 'error' => 'Api Account existiert bereits', 'api_id' => $shop['api_id']];
    }

    $remoteDomain = $shop['bezeichnung'];
    $firstRemoteDomain = $remoteDomain;
    $apiName = $shop['bezeichnung'];
    $firstApiName = $apiName;
    $remoteIndex = 0;
    $key = sha1(uniqid('shopexport', true));
    while(
      !empty(
        $this->app->DB->Select(
          sprintf(
            "SELECT `id` FROM `api_account` WHERE `remotedomain` = '%s' OR `bezeichnung` = '%s' LIMIT 1",
            $this->app->DB->real_escape_string($remoteDomain),
            $this->app->DB->real_escape_string($apiName)
          )
        )
      )
    ) {
      $remoteIndex++;
      $remoteDomain = $firstRemoteDomain.'_'.$remoteIndex;
      $apiName = $firstApiName.' '.$remoteIndex;
    }
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `api_account` 
        (`bezeichnung`, `initkey`, `importwarteschlange_name`, `event_url`, `remotedomain`, `aktiv`, 
         `importwarteschlange`,  `cleanutf8`, `uebertragung_account`, `projekt`, `permissions`) 
         VALUES ('%s', '%s', '', '', '%s', 1, 
                 0, 0, 0, 0, '%s')",
        $this->app->DB->real_escape_string($apiName),
        $this->app->DB->real_escape_string($key), $this->app->DB->real_escape_string($remoteDomain),
        $this->app->DB->real_escape_string('["communicate_with_shop"]')
      )
    );
    $apiId = $this->app->DB->GetInsertID();
    $this->app->DB->Update(
      sprintf(
        'UPDATE `shopexport` SET `api_account_id` = %d WHERE `id` = %d',
        $apiId, $shopId
      )
    );

    return ['success' => true, 'api_id' => $apiId];
  }

  public function ShopexportItemlink()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $shop = $this->app->DB->Select(sprintf('SELECT `id` FROM `shopexport` WHERE `id` = %d', $id));
    if(empty($shop)) {
      $this->app->Location->execute(
        'index.php?module=onlineshops&action=list&msg='.
        $this->app->erp->base64_url_encode('<div class="error">Shop nicht gefunden</div>')
      );
    }
    $sid = base64_decode($this->app->Secure->GetGET('sid'));
    if(empty($sid)) {
      $this->app->Location->execute(
        'index.php?module=onlineshops&action=list&msg='.
        $this->app->erp->base64_url_encode('<div class="error">Artikel nicht angegeben</div>')
      );
    }
    $article = $this->app->DB->SelectRow(
      sprintf(
        "SELECT art.id, art.projekt FROM `artikel` AS art 
        LEFT JOIN `artikelnummer_fremdnummern` AS af on art.id = af.artikel AND af.aktiv = 1 AND af.shopid = %d
        WHERE (art.nummer = '%s' OR af.nummer = '%s') AND (art.geloescht = 0 OR art.geloescht IS NULL)
        ORDER BY af.id DESC
        LIMIT 1",
        $shop, $this->app->DB->real_escape_string($sid), $this->app->DB->real_escape_string($sid)
      )
    );
    if(empty($article)) {
      $this->app->Location->execute(
        'index.php?module=onlineshops&action=list&msg='.
        $this->app->erp->base64_url_encode(sprintf('<div class="error">Artikel %s nicht gefunden</div>', $sid))
      );
    }
    if(empty($article['projekt']) || $this->app->User->projectOk($article['projekt'])){
      $this->app->Location->execute('index.php?module=artikel&action=edit&id=' . $article['id']);
    }
    $this->app->Location->execute(
      'index.php?module=onlineshops&action=list&msg='.
      $this->app->erp->base64_url_encode(
        sprintf(
          '<div class="error">Sie haben keine Rechte den Artikel %s aufzurufen</div>',
          $sid
        )
      )
    );
  }

  /**
   * @param string $server
   *
   * @return string[][]
   */
  public function getUserCases($server)
  {
    return [
      'batchpicking' => [
        'title_de' => 'Batch Kommissionierung',
        'title_en' => 'Batch Picking',
        'svg_de'   => 'usecasesSvgDe/Batch_Kommissionierung@1x.svg',
        'svg_en'   => 'usecasesSvgEn/Batch_picking@1x.svg',
        'link'     => $server .'/index.php?module=batchpicking&action=list',
        'link_alt' => 'https://xentral.com/helpdesk/kurzanleitung-batches-picklisten',
      ],
      'buchhaltungsexport' => [
        'title_de' => 'Datev Export',
        'title_en' => 'Data export',
        'svg_de'   => 'usecasesSvgDe/Datev_Export@1x.svg',
        'svg_en'   => 'usecasesSvgEn/Data_export@1x.svg',
        'link'     => $server .'/index.php?module=buchhaltungexport&action=list',
        'link_alt' => 'https://xentral.com/helpdesk/kurzanleitung-export-fuer-die-buchhaltung',
      ],
      'dropshipping' => [
        'title_de' => 'Dropshipping',
        'title_en' => 'Dropshipping',
        'svg_de'   => 'usecasesSvgDe/Dropshipping@1x.svg',
        'svg_en'   => 'usecasesSvgEn/Dropshipping@1x.svg',
        'link'     => $server .'/index.php?module=dropshipping&action=list',
        'link_alt' => 'https://xentral.com/helpdesk/dropshipping',
      ],
      'kommissionierlauf' => [
        'title_de' => 'Einfache Pickliste',
        'title_en' => 'Simple pick list',
        'svg_de'   => 'usecasesSvgDe/Einfache_Pickliste@1x.svg',
        'svg_en'   => 'usecasesSvgEn/Simple_pick_list@1x.svg',
        'link'     => $server .'/index.php?module=kommissionierlauf&action=list',
        'link_alt' => 'https://xentral.com/helpdesk/kurzanleitung-logistikprozesse',
      ],
      'mahnwesen' => [
        'title_de' => 'Mahnwesen',
        'title_en' => 'Dunning',
        'svg_de'   => 'usecasesSvgDe/Mahnwesen@1x.svg',
        'svg_en'   => 'usecasesSvgEn/Dunning@1x.svg',
        'link'     => $server .'/index.php?module=mahnwesen&action=list',
        'link_alt' => 'https://xentral.com/helpdesk/kurzanleitung-mahnwesen',
      ],
      'versandarten' => [
        'title_de' => 'Paketmarken',
        'title_en' => 'Parcel stamps',
        'svg_de'   => 'usecasesSvgDe/Paketmarken@1x.svg',
        'svg_en'   => 'usecasesSvgEn/Parcel_stamps@1x.svg',
        'link'     => $server .'/index.php?module=versandarten&action=list',
        'link_alt' => 'https://xentral.com/helpdesk/kurzanleitung-versandarten',
      ],
      'pos' => [
        'title_de' => 'POS Anbinden',
        'title_en' => 'Connect POS',
        'svg_de'   => 'usecasesSvgDe/POS_Anbinden@1x.svg',
        'svg_en'   => 'usecasesSvgEn/Connect_POS@1x.svg',
        'link'     => $server .'/index.php?module=pos&action=list',
        'link_alt' => 'https://xentral.com/helpdesk/kurzanleitung-arbeiten-mit-der-pos-kasse',
      ],
      'retoure' => [
        'title_de' => 'Retouren',
        'title_en' => 'Returns',
        'svg_de'   => 'usecasesSvgDe/Retouren@1x.svg',
        'svg_en'   => 'usecasesSvgEn/Returns@1x.svg',
        'link'     => $server .'/index.php?module=retoure&action=list',
        'link_alt' => 'https://xentral.com/helpdesk/kurzanleitung-rma',
      ],
      'wareneingang' => [
        'title_de' => 'Wareneingang',
        'title_en' => 'Goods Receipt',
        'svg_de'   => 'usecasesSvgDe/Wareneingang@1x.svg',
        'svg_en'   => 'usecasesSvgEn/Goods_Receipt@1x.svg',
        'link'     => $server .'/index.php?module=warneingang&action=paketannahme',
        'link_alt' => 'https://xentral.com/helpdesk/kurzanleitung-zentraler-wareneingang',
      ],

    ];
  }

  /**
   * @param int $shopId
   *
   * @return array
   */
  public function getModulelinks($shopId = 0)
  {
    $server = $this->app->Location->getServer();
    $pos1 = strpos($server, 'api/index.php');
    if((int)$pos1 > 0) {
      $server = substr($server, 0, (int)$pos1);
    }
    elseif((int)($pos1 = strpos($server, 'index.php')) > 0) {
      $server = substr($server, 0, (int)$pos1);
    }
    elseif((int)($pos1 = strpos($server, '/api/shopimport/modulelinks')) > 0) {
      $server = substr($server, 0, (int)$pos1);
    }
    $server = rtrim($server,'/');
    /** @var Appstore $appstore */
    $appstore = $this->app->loadModule('appstore');
    $apps = $appstore->getAppsList();

    $ret = $this->getUserCases($server);
    if(!empty($apps['kauf'])){
      foreach ($ret as $module => $box) {
        if($this->app->erp->ModulVorhanden($module)){
          continue;
        }
        foreach ($apps['kauf'] as $app) {
          if($app['key'] !== $module) {
            continue;
          }
          $ret[$module]['link'] = $server
            .'/index.php?module=appstore&action=list&cmd=detail&app='.$app['key'];
          break;
        }
      }
    }
    $cacheDir = dirname(__DIR__).'/cache/Onlineshop/resources';
    foreach($ret as $key => $val) {
      if(!empty($val['svg_en'])) {
        $file = $cacheDir.'/'.$val['svg_en'];
        if(!is_file($cacheDir.'/'.$val['svg_en'])) {
          $this->InstallResouces();
          break;
        }
      }
      if(!empty($val['svg_de'])) {
        if(!is_file($cacheDir.'/'.$val['svg_de'])) {
          $this->InstallResouces();
          break;
        }
      }
    }
    foreach($ret as $key => $val) {
      if(!empty($val['svg_en'])) {
        $ret[$key]['svg_en'] = $server.'/cache/Onlineshop/resources/'. $val['svg_en'];
      }
      if(!empty($val['svg_de'])) {
        $ret[$key]['svg_de'] = $server.'/cache/Onlineshop/resources/'. $val['svg_de'];
      }
    }

    return $ret;
  }

  public function ShopexportOrderlink()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $shop = $this->app->DB->Select(sprintf('SELECT `id` FROM `shopexport` WHERE `id` = %d', $id));
    if(empty($shop)) {
      $this->app->Location->execute(
        'index.php?module=onlineshops&action=list&msg='.
        $this->app->erp->base64_url_encode('<div class="error">Shop nicht gefunden</div>')
      );
    }
    $sid = base64_decode($this->app->Secure->GetGET('sid'));
    if(empty($sid)) {
      $this->app->Location->execute(
        'index.php?module=onlineshops&action=list&msg='.
        $this->app->erp->base64_url_encode('<div class="error">Auftrag nicht angegeben</div>')
      );
    }
    $order = $this->app->DB->SelectRow(
      sprintf(
        "SELECT `id`, `projekt` 
        FROM `auftrag` 
        WHERE (`internet`= '%s' OR `shopextid` = '%s') AND `shop` = %d 
        ORDER BY `status` = 'storniert' 
        LIMIT 1",
        $this->app->DB->real_escape_string($sid), $this->app->DB->real_escape_string($sid), $id
      )
    );
    if(empty($order)) {
      $this->app->Location->execute(
        'index.php?module=onlineshops&action=list&msg='.
        $this->app->erp->base64_url_encode(sprintf('<div class="error">Auftrag %s nicht gefunden</div>', $sid))
      );
    }
    if(empty($order['projekt']) || $this->app->User->projectOk($order['projekt'])){
      $this->app->Location->execute('index.php?module=auftrag&action=edit&id=' . $order['id']);
    }
    $this->app->Location->execute(
      'index.php?module=onlineshops&action=list&msg='.
      $this->app->erp->base64_url_encode(
        sprintf(
          '<div class="error">Sie haben keine Rechte den Auftrag %s aufzurufen</div>',
          $sid
        )
      )
    );
  }

  /**
   * @param string $data
   *
   * @return array
   */
  public function checkAppData($data)
  {
    $data = json_decode($data, true);
    if(empty($data['shoptype'])) {
      return ['success'=>false,'error' => 'no shoptype given'];
    }
    if(empty($data['token'])) {
      return ['success'=>false,'error' => 'no token given'];
    }

    if(strpos($data['shoptype'],'shopimporter_') !== 0) {
      return ['success'=>false,'error' => sprintf('Shopmodule %s not valid', $data['shoptype'])];
    }

    if(!$this->app->erp->ModulVorhanden($data['shoptype'])) {
      return ['success'=>false,'error' => sprintf('Shopmodule %s not found', $data['shoptype'])];
    }

    /** @var ShopimporterBase $shop */
    $shop = $this->app->loadModule($data['shoptype']);
    if(!method_exists($shop, 'checkApiApp')) {
      return ['success'=>false,'error' => sprintf('Shopmodule %s provide no api', $data['shoptype'])];
    }
    $info = null;
    $check = $shop->checkApiApp($data);
    if(!empty($check['error'])) {
      return $check;
    }
    if(!empty($check['info'])) {
      $info = $check['info'];
    }

    return ['success'=>true, 'data' => $data, 'info' => $info];
  }

  /**
   * @return JsonResponse|void
   */
  public function ShopexportAppNew()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    $fromAjax = $cmd === 'createdata';
    $fromCreateButton = $this->app->Secure->GetPOST('create');
    if($fromAjax || $fromCreateButton) {
      $data = $this->app->Secure->GetPOST('data','','',true);
      $step = (int)$this->app->Secure->GetPOST('step');
      $shopId = (int)$this->app->Secure->GetPOST('shopId');
      if($step === 2 && $shopId > 0 && !empty($data)) {
        return $this->getVueShopexportAppNewSuccessPageYt($shopId, $data);
      }
      $check = $this->checkAppData($data);
      $shopId = $shopId <= 0?0:(int)$this->app->DB->Select(
        sprintf(
          "SELECT `id` FROM `shopexport` WHERE `geloescht` = 0 AND `aktiv` = 1 AND `id` = %d AND `modulename` = '%s'",
          $shopId, $this->app->DB->real_escape_string($check['data']['shoptype'])
        )
      );
      if(!empty($check['error'])) {
        if(!$fromCreateButton) {
          return new JsonResponse(
            ['error'=>'<div class="error">'.$check['error'].'</div>'],
            JsonResponse::HTTP_INTERNAL_SERVER_ERROR
          );
        }
        $this->app->Location->execute(
          'index.php?module=onlineshops&action=appnew&msg='
          .$this->app->erp->base64_url_encode('<div class="error">'.$check['error'].'</div>')
        );
      }
      if($shopId > 0) {
        $shop = ['id' => $shopId,];
      }
      else {
        $shop = $this->createInternShop($check['data']['shoptype']);
      }
      if(!empty($shop['error'])) {
        if(!$fromCreateButton) {
          return new JsonResponse(
            ['error'=>'<div class="error">'.$shop['error'].'</div>'],
            JsonResponse::HTTP_INTERNAL_SERVER_ERROR
          );
        }
        $this->app->Location->execute(
          'index.php?module=onlineshops&action=appnew&msg='
          .$this->app->erp->base64_url_encode('<div class="error">'.$shop['error'].'</div>')
        );
      }
      $apiAccount = $this->genearteApiAccount($shop['id']);
      if(!empty($apiAccount['api_id'])) {
        $json = $this->app->DB->Select(
          sprintf(
            'SELECT `einstellungen_json` FROM `shopexport` WHERE `id` = %d',
            $shop['id']
          )
        );
        $json = json_decode($json, true);
        if(!is_array($json)) {
          $json = [];
        }
        if(empty($json['felder'])) {
          $json['felder'] = [];
        }
        if(!empty($check['data']['data'])) {
          foreach($check['data']['data'] as $fieldKey => $fieldValue) {
            $json['felder'][$fieldKey] = $fieldValue;
          }
        }
        $json = json_encode($json);
        $this->app->DB->Update(
          sprintf(
            "UPDATE `shopexport` 
            SET `einstellungen_json` = '%s', 
                `api_account_token` = '%s', 
                `demomodus` = 0, 
                `cronjobaktiv` = 1,
                `lagerexport` = 1
            WHERE `id` = %d",
            $this->app->DB->real_escape_string($json),
            $this->app->DB->real_escape_string($check['data']['token']),
            $shop['id']
          )
        );
        if(!$fromCreateButton) {
          return $this->getVueShopexportAppNewSuccessPage($shop['id'], $data);
        }
        $this->app->Location->execute(
          'index.php?module=onlineshops&action=appnew&shopid='.$shop['id']
          .'&url='.urlencode($check['data']['url'])
        );
      }
    }

    if($cmd === 'checkdata') {
      $data = $this->app->Secure->GetPOST('data','','',true);
      $check = $this->checkAppData($data);
      if(!empty($check['error'])) {
        return new JsonResponse($check, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
      }
      return new JsonResponse(['html'=>'<div class="info">Shoptyp: '.$check['data']['shoptype'].'</div>']);
    }
    $shopdata = $this->app->Secure->GetGET('shopdata');
    if(!empty($shopdata)) {
      $shopdata = base64_decode($shopdata);
      $this->app->User->SetParameter('onlineshops_appnew', $shopdata);
      $this->app->User->SetParameter('onlineshops_appnew_time', date('Y-m-d H:i:s'));
      $this->app->erp->RegisterHook('welcome_start', 'onlineshops', 'OnlineShopsWelcomeStart');
      $this->app->Location->execute('index.php?module=welcome&action=start');
    }

    $this->app->erp->MenuEintrag('index.php?module=onlineshops&action=appnew', 'Shop mit API anlegen');
    $shopId = $this->app->Secure->GetGET('shopid');
    $url = $this->app->Secure->GetGET('url');
    if(!empty($shopId)) {
      if(!empty($url)) {
        $this->app->Tpl->Add(
          'MESSAGE',
          '<div class="info">Es wurde ein neuer <a href="index.php?module=onlineshops&action=edit&id='
          .$shopId.'" target="_blank">Shop</a> angelegt.
          <a href="'.$url.'" target="_blank">zur&uuml;ck zum Onlineshop</a></div>'
        );
      }
      else {
        $this->app->Tpl->Add(
          'MESSAGE',
          '<div class="info">Es wurde ein neuer <a href="index.php?module=onlineshops&action=edit&id='
          .$shopId.'" target="_blank">Shop</a> angelegt.</div>'
        );
      }
    }

    $this->app->Tpl->Parse('PAGE', 'onlineshop_appnew.tpl');
  }

  /**
   * @param Welcome $welcomeObj
   */
  public function OnlineShopsWelcomeStart($welcomeObj)
  {
    $appnew = $this->app->User->GetParameter('onlineshops_appnew');
    if(empty($appnew)) {
      return;
    }
    $appnewTime = $this->app->User->GetParameter('onlineshops_appnew_time');
    if(empty($appnewTime)) {
      return;
    }
    try {
      $lastDay = new DateTime(date('Y-m-d H:i:s'));
      $lastDay->sub(new DateInterval('P1D'));
      $appnewTime = new DateTime($appnewTime);
      if($appnewTime < $lastDay) {
        return;
      }
    }
    catch (Exception $e) {
      return;
    }
    $this->app->ModuleScriptCache->IncludeJavascriptFiles(
      'onlineshops', ['./classes/Modules/Onlineshop/www/js/onlineshop_appnew.js']
    );
    $this->app->User->SetParameter('onlineshops_appnew', '');
    $this->app->User->SetParameter('onlineshops_appnew_time', '');
    $check = $this->checkAppData($appnew);
    if(!empty($check['success'])) {
      $shopModule = $check['data']['shoptype'];
      $shopModules = [['value' => '-1', 'text'=> 'Neu anlegen',],];
      $shopModulesPair =  $this->app->DB->SelectArr(
        sprintf(
          "SELECT `id` AS `value`, `bezeichnung` AS `text` 
          FROM `shopexport` 
          WHERE `geloescht` = 0 AND `modulename` = '%s' AND `aktiv` = 1 
          ORDER BY `bezeichnung`",
          $this->app->DB->real_escape_string($shopModule)
        )
      );
      if(!empty($shopModulesPair)) {
        $shopModules = array_merge(
          [['value'=>'', 'text'=> '']],
          $shopModulesPair,$shopModules
        );
      }
      else {
        $shopModules = array_merge(
          [['value'=>'', 'text'=> '']],
          $shopModules
        );
      }

      $this->app->Tpl->Set('APPNEWJSON', json_encode($shopModules));

      $this->app->Tpl->Set('APPNEWDATA', htmlspecialchars($appnew));

      $this->app->Tpl->Set('APPNEWINFO', empty($check['info']['info'])?'':$check['info']['info']);
      $this->app->Tpl->Set('APPNEWFIELDSET', empty($check['info']['Shop'])?'':$check['info']['Shop']);


      $this->app->Tpl->Parse('AUFGABENPOPUP','onlineshops_appnewpopup.tpl');
    }
    else {
      $error =!empty($check['error'])?$check['error']:'Request ungültig';
      $this->app->Tpl->Set('ERRORMSG', $error);
      $this->app->Tpl->Parse('AUFGABENPOPUP','onlineshops_appnewerrorpopup.tpl');
    }

    $this->app->ModuleScriptCache->IncludeWidgetNew('ClickByClickAssistant');
  }

  /**
   * @return JsonResponse
   */
  public function ShopexportGetApi()
  {
    $token = $this->app->Secure->GetGET('token');
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'getusecases') {
      $data = $this->getModulelinks();
      return new JsonResponse($data);
    }
    if(empty($token)) {
      $data = ['success' => false, 'error' => 'no Token given'];
      return new JsonResponse($data, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    $shopId = $this->app->DB->SelectRow(
      sprintf(
        "SELECT `id`, `api_account_id` FROM `shopexport` WHERE `api_account_token` = '%s' LIMIT 1",
        $token
      )
    );

    if(empty($shopId)) {
      $data = ['success' => false, 'error' => 'Token is invalid'];
      return new JsonResponse($data, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    $apiAccount = $this->app->DB->SelectRow(
      sprintf(
        'SELECT `id`, `initkey`, `remotedomain` FROM `api_account` WHERE `id` = %d LIMIT 1',
        $shopId['api_account_id']
      )
    );

    $data = ['success' => true];
    $data['shopid'] = $shopId['id'];
    $data['api_account_id'] = $shopId['api_account_id'];
    $data['apiname'] = $apiAccount['remotedomain'];
    $data['apikey'] = $apiAccount['initkey'];
    $data['server'] = rtrim(str_replace('index.php','',$this->app->Location->getServer()),'/');
    $data['api_auth'] = $data['server'].'/api/shopimport/auth';
    $data['api_syncstorage'] = $data['server'].'/api/shopimport/syncstorage/{articlenumber_base64}';
    $data['api_articletoxentral'] = $data['server'].'/api/shopimport/articletoxentral/{articlenumber_base64}';
    $data['api_articletoshop'] = $data['server'].'/api/shopimport/articletoshop/{articlenumber_base64}';
    $data['api_ordertoxentral'] = $data['server'].'/api/shopimport/ordertoxentral/{ordernumber_base64}';
    $data['api_statistics'] = $data['server'].'/api/shopimport/statistics';
    $data['api_modulelinks'] = $data['server'].'/api/shopimport/articletoshop/modulelinks';
    $data['api_status'] = $data['server'].'/api/shopimport/articletoshop/status';
    $data['api_disconnect'] = $data['server'].'/api/shopimport/articletoshop/disconnect';
    $data['api_reconnect'] = $data['server'].'/api/shopimport/articletoshop/reconnect';
    $data['item_link'] = $data['server']
      .'/index.php?module=onlineshops&action=itemlink&id='.$shopId['id'].'&sid={articlenumber_base64}';
    $data['order_link'] = $data['server']
      .'/index.php?module=onlineshops&action=orderlink&id='.$shopId['id'].'&sid={ordernumber_base64}';

    return new JsonResponse($data);
  }
  
  public function ShopexportMinidetail()
  {
    $ida = explode('-',$this->app->Secure->GetGET('id'));
    if(count($ida) < 2)
    {
      $this->app->ExitXentral();
    }
    $id = $ida[1];
    if($ida[0] == 1) {
      $wert = $this->app->DB->Select("SELECT parameter1 FROM shopexport_log WHERE id = '$id' LIMIT 1");
      echo '<pre>';
      echo htmlspecialchars($wert);
      echo '</pre>';
    }elseif($ida[0]==3){
      $query = sprintf('SELECT shop_id FROM shopexport_change_log WHERE id=%d',$id);
      $shopId = $this->app->DB->Select($query);
      $query = sprintf('SELECT diff FROM shopexport_change_log WHERE shop_id=%d AND id<%d ORDER BY id ASC',
        $shopId,$id);
      $changeLogEntries = $this->app->DB->SelectArr($query);
      $fieldsToCompareTo = [];
      foreach ($changeLogEntries as $changeLogEntry){
        $existingFields = json_decode($changeLogEntry['diff'],true);
        $fieldsToCompareTo = array_merge($fieldsToCompareTo,$existingFields);
      }
      $query = sprintf('SELECT diff FROM shopexport_change_log WHERE id=%d', $id);
      $lastChanges = json_decode($this->app->DB->Select($query),true);

      $message = '<table><tr><td>Feldname</td><td>Ursprünglicher Wert</td><td>Aktualisierter Wert</td></tr>';
      foreach ($lastChanges as $lastChangeFieldName => $lastChangeFieldValue){
        $message .= '<tr><td>'.$lastChangeFieldName.'</td><td>'.$fieldsToCompareTo[$lastChangeFieldName].'</td><td>'.$lastChangeFieldValue.'</td></tr>';
      }
      $message .='</table>';
      echo $message;
    }else{
      $shopimportCartRow = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM `shopimport_auftraege` WHERE `id` = %d',
          $id
        )
      );
      if(!empty($shopimportCartRow['jsonencoded'])) {
        $warenkorb = json_decode(base64_decode($shopimportCartRow['warenkorb']), true);
      }else{
        $warenkorb = unserialize(base64_decode($shopimportCartRow['warenkorb']));
      }
      echo '<pre>';
      echo print_r($warenkorb,true);
      echo '</pre>';
      $cartInfos = $this->app->DB->SelectArr(
        sprintf(
          "SELECT * FROM `onlineshop_transfer_cart` WHERE `shop_id` = %d AND `extid` = '%s'",
          $shopimportCartRow['shopid'], $this->app->DB->real_escape_string($shopimportCartRow['extid'])
        )
      );
      if(!empty($cartInfos)) {
        $table = new EasyTable($this->app);
        $table->Query("SELECT '' AS `Datum`,  '' AS `status`, '' AS `Template`, '' AS `Transformiert`");
        $table->datasets = [];
        foreach($cartInfos as $cartInfo) {
          $cart = empty($cartInfo['cart_transfer'])?''
            :print_r(json_decode(base64_decode($cartInfo['cart_transfer'], true)));
          $template = empty($cartInfo['template'])?'':$cartInfo['template'];
          $table->datasets[] = [
            'Datum' => $cartInfo['created_at'],
            'status' => $cartInfo['status'],
            'Template' =>nl2br(htmlspecialchars($template)),
            'Transformiert' => nl2br(htmlspecialchars($cart)),
          ];
        }
        echo $table->DisplayNew('return','Transformiert','noAction');
      }
    }
    $this->app->ExitXentral();
  }

  public function ShopexportKundengruppensave()
  {
    $id = (int)$this->app->Secure->GetPOST('id');
    $aktiv = (int)$this->app->Secure->GetPOST('aktiv');
    $neukundenzuweisen = (int)$this->app->Secure->GetPOST('kundengruppeneukundenzuweisen');
    $rolle = $this->app->Secure->GetPOST('zuweisungrolle');
    $shopId = (int)$this->app->Secure->GetPOST('shop');
    $kundengruppeIdAusXentral = $this->app->Secure->GetPOST('kundengruppexentral');
    $kundengruppeBezeichnungAusShop = $this->app->Secure->GetPOST('kundengruppeshop');
    $projektAbkuerzung = $this->app->Secure->GetPOST('projekt');
    $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '$projektAbkuerzung' AND abkuerzung <> '' AND ifnull(geloescht,0) = 0 LIMIT 1");
    if(!$projekt){
      $projekt = '0';
    }
    $antwort = array(
      'status' => 1,
      'statusText' => 'Gespeichert.'
    );

    if(!$shopId){
      $antwort['status'] = 0;
      $antwort['statusText'] = 'Shopzuordnung kann nicht gefunden werden.';
    }

    $eintragBereitsvorhanden = $this->app->DB->Select("SELECT id FROM shopexport_kundengruppen WHERE projekt='$projekt' AND shopid='$shopId' AND gruppeid='$kundengruppeIdAusXentral' AND extgruppename='$kundengruppeBezeichnungAusShop' AND id<>'$id'LIMIT 1");
    if($eintragBereitsvorhanden){
      $antwort['status'] = 0;
      $antwort['statusText'] = 'Identischer Eintrag bereits vorhanden.';
    }

    if ($antwort['status']){
      $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
      if($id){
        $sql = "UPDATE shopexport_kundengruppen SET type='$rolle',gruppeid='$kundengruppeIdAusXentral',extgruppename='$kundengruppeBezeichnungAusShop', aktiv='$aktiv', apply_to_new_customers='$neukundenzuweisen',projekt = '$projekt', updated=NOW(),updatedby='$bearbeiter' WHERE id='$id'";
        $this->app->DB->Update($sql);
      }else{
        $aktiv = '1';
        $sql = "INSERT INTO shopexport_kundengruppen (shopid, gruppeid, extgruppename, aktiv, apply_to_new_customers,type, projekt, updated, updatedby) VALUES
          ('$shopId','$kundengruppeIdAusXentral','$kundengruppeBezeichnungAusShop','$aktiv','$neukundenzuweisen','$rolle','$projekt',NOW(),'$bearbeiter')";
        $this->app->DB->Insert($sql);
      }
    }

    echo json_encode($antwort);
    $this->app->ExitXentral();
  }

  public function ShopexportSprachensave()
  {
    $id = (int)$this->app->Secure->GetGET('id');

    $shop = (int)$this->app->Secure->GetPOST('shop');
    $land = $this->app->Secure->GetPOST('land');
    $sprache = $this->app->Secure->GetPOST('sprache');
    $projekt = explode(' ',$this->app->Secure->GetPOST('projekt'));
    $projekt = reset($projekt);
    $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '$projekt' AND abkuerzung <> '' AND ifnull(geloescht,0) = 0 LIMIT 1");
    /*$aktiv = $this->app->Secure->GetPOST('aktiv');
    if($aktiv!="1")
    {
      $aktiv=0;
    }*/

    $aktiv = 1;

    if ($shop) {
      $this->app->DB->Insert("INSERT INTO shopexport_sprachen (id,shop,land,projekt, aktiv, sprache) VALUES
          ('','$shop','$land','$projekt','$aktiv','$sprache')");
      $insid = $this->app->DB->GetInsertID();
      $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
      $this->app->DB->Update("UPDATE shopexport_sprachen SET updatedby = '$bearbeiter' WHERE id = '$insid' LIMIT 1");
    }

    echo json_encode(array(
          'status' => 1,
          'statusText' => 'Gespeichert.'
          ));
    $this->app->ExitXentral();
  }

  public function ShopexportZahlweisesave()
  {
    $id = (int)$this->app->Secure->GetGET('id');

    $shop = (int)$this->app->Secure->GetPOST('shop');
    $zahlweise_shop = $this->app->Secure->GetPOST('zahlweise_shop');
    $zahlweise_wawision = $this->app->Secure->GetPOST('zahlweise_wawision');
    $vorabbezahltmarkieren = $this->app->Secure->GetPOST('vorabbezahltmarkieren');
    $autoversand = $this->app->Secure->GetPOST('autoversand');
    $keinerechnung = $this->app->Secure->GetPOST('keinerechnung');
    $fastlane = (int)$this->app->Secure->GetPOST('fastlane');
    if($keinerechnung!="1")
    {
      $keinerechnung=0;
    }
    if($vorabbezahltmarkieren!="1")
    {
      $vorabbezahltmarkieren=0;
    }
    if($autoversand!="1")
    {
      $autoversand=0;
    }
    if($fastlane!==1)
    {
      $fastlane=0;
    }
    $aktiv = 1;

    if ($shop) {
      $this->app->DB->Insert("INSERT INTO shopexport_zahlweisen (id,shop,zahlweise_shop,zahlweise_wawision,vorabbezahltmarkieren,aktiv,autoversand,keinerechnung,fastlane) VALUES
          ('','$shop','$zahlweise_shop','$zahlweise_wawision','$vorabbezahltmarkieren','$aktiv','$autoversand','$keinerechnung',$fastlane)");
      $insid = $this->app->DB->GetInsertID();
      $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
      $this->app->DB->Update("UPDATE shopexport_zahlweisen SET updatedby = '$bearbeiter' WHERE id = '$insid' LIMIT 1");
    }

    echo json_encode(array(
          'status' => 1,
          'statusText' => 'Gespeichert.'
          ));
    $this->app->ExitXentral();
  }

  public function ShopexportSubshopsave()
  {
    $id = (int)$this->app->Secure->GetPOST('id');
    $shop = (int)$this->app->Secure->GetPOST('shop');
    $subshopkennung = $this->app->Secure->GetPOST('subshopkennung');
    $projekt = $this->app->Secure->GetPOST('projekt');
    $sprache = $this->app->Secure->GetPOST('sprache');
    $aktiv = 1;

    $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' AND abkuerzung!='' LIMIT 1");

    if ($shop && $subshopkennung && $projekt) {
      $this->app->DB->Insert("INSERT INTO shopexport_subshop (id,shop,subshopkennung,projekt,sprache, aktiv) VALUES ('','$shop','$subshopkennung','$projekt','$sprache','$aktiv')");
      $insid = $this->app->DB->GetInsertID();
      $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
      $this->app->DB->Update("UPDATE shopexport_subshop SET updatedby = '$bearbeiter' WHERE id = '$insid' LIMIT 1");

      echo json_encode(array(
            'status' => 1,
            'statusText' => 'Gespeichert.'
            ));        
    }
    else {
      echo json_encode(array(
            'status' => 0,
            'statusText' => 'Bitte alle Felder eingeben'
            ));

    }


    $this->app->ExitXentral();
  }

  public function ShopexportFreifeldsave()
  {
    $id = (int)$this->app->Secure->GetPOST('id');
    $shop = (int)$this->app->Secure->GetPOST('shop');
    $freifeld_shop = $this->app->Secure->GetPOST('freifeld_shop');
    $freifeld_wawi = $this->app->Secure->GetPOST('freifeld_wawi');
    $aktiv = 1;

    if ($shop && $freifeld_shop) {
      $this->app->DB->Insert("INSERT INTO shopexport_freifelder (id,shop,freifeld_shop,freifeld_wawi,aktiv) VALUES
          ('','$shop','$freifeld_shop','$freifeld_wawi','$aktiv')");
      $insid = $this->app->DB->GetInsertID();
      $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
      $this->app->DB->Update("UPDATE shopexport_freifelder SET updatedby = '$bearbeiter' WHERE id = '$insid' LIMIT 1");
      echo json_encode(array(
            'status' => 1,
            'statusText' => 'Gespeichert.'
            ));        
    }
    else {
      echo json_encode(array(
            'status' => 0,
            'statusText' => 'Bitte Freifeld eingeben'
            ));

    }

    $this->app->ExitXentral();
  }

  /**
   * @throws Exception
   */
  public function ShopexportArtikelbaumexport(){
    $shopid = (int)$this->app->Secure->GetPOST('id');
    if($this->app->DB->Select(sprintf("SELECT id FROM prozessstarter WHERE parameter = 'onlineshops_tasks' AND aktiv = 1 LIMIT 1"))){
      $command = 'sendartikelbaum';
      $task = $this->app->DB->SelectRow('SELECT * FROM onlineshops_tasks WHERE shop_id=\'' . $shopid . '\' AND command=\'' . $command . '\'');
      if(!empty($task['id'])){
        if($task['status'] !== 'running'){
          $this->app->DB->Update('UPDATE onlineshop_tasks SET status=\'inactive\',counter=0 WHERE id=' . $task['id']);
        }
      }
      else{
        $this->app->DB->Insert('INSERT INTO onlineshops_tasks (shop_id, command) VALUES (' . $shopid . ',\'' . $command . '\')');
      }
      echo json_encode(
        array(
        'status' => 1,
        'statusText' => 'Aufgabe wurde zu Shoptasks hinzugefügt und wird im Hintergrund ausgeführt.'
      ));
      $this->app->ExitXentral();
    }

    $shopinfo = $this->app->DB->SelectRow("SELECT * FROM shopexport WHERE id ='$shopid'");
    if($shopinfo['shoptyp'] === 'intern' && $this->app->erp->ModulVorhanden($shopinfo['modulename'])){

      $obj = $this->app->erp->LoadModul($shopinfo['modulename']);

      if($obj->canExportArticleTree()){

        $kategorienbaum = null;

        $this->app->erp->GetKategorienbaum($kategorienbaum, 0, 0, $shopid);

        $this->app->remote->RemoteCommand($shopid, 'sendartikelbaum', $kategorienbaum);
      }
    }
    echo json_encode(array(
      'status' => 1,
      'statusText' => 'Übertragung abgeschlossen.'
    ));
    $this->app->ExitXentral();
  }

  public function ShopexportZahlweiseeditsave()
  {
    $id = (int)$this->app->Secure->GetPOST('id');
    $zahlweise_shop = $this->app->Secure->GetPOST('zahlweise_shop');
    $zahlweise_wawision = $this->app->Secure->GetPOST('zahlweise_wawision');
    $vorabbezahltmarkieren = $this->app->Secure->GetPOST('vorabbezahltmarkieren');
    $autoversand = $this->app->Secure->GetPOST('autoversand');
    $aktiv = $this->app->Secure->GetPOST('aktiv');
    $keinerechnung = $this->app->Secure->GetPOST('keinerechnung');
    $fastlane = (int)$this->app->Secure->GetPOST('fastlane');

    if($vorabbezahltmarkieren!="1") {
      $vorabbezahltmarkieren=0;
    }
    if($keinerechnung!="1")
    {
      $keinerechnung=0;
    }
    if($autoversand!="1")
    {
      $autoversand=0;
    }
    if($aktiv!="1")
    {
      $aktiv=0;
    }
    if($fastlane!==1)
    {
      $fastlane=0;
    }

    if ($id) {
      $this->app->DB->Update("UPDATE shopexport_zahlweisen SET keinerechnung = '$keinerechnung', zahlweise_shop='$zahlweise_shop',zahlweise_wawision='$zahlweise_wawision',vorabbezahltmarkieren='$vorabbezahltmarkieren',autoversand='$autoversand',aktiv='$aktiv',fastlane = $fastlane WHERE id = '$id'");
      $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
      $this->app->DB->Update("UPDATE shopexport_zahlweisen SET updatedby = '$bearbeiter', updated = now() WHERE id = '$id'");
    }

    echo json_encode(array(
          'status' => 1,
          'statusText' => 'Gespeichert.'
          ));
    $this->app->ExitXentral();

  }

  public function ShopexportSprachenget()
  {
    $id = $this->app->Secure->GetPOST('id');
    $row = $this->app->DB->SelectRow('
        SELECT
        s.id,
        s.sprache,
        s.land,
        s.aktiv,
        p.abkuerzung as projekt
        FROM
        shopexport_sprachen s LEFT JOIN projekt p ON s.projekt = p.id
        WHERE
        s.id = "' . $id . '"
        ');
    echo json_encode($row);
    $this->app->ExitXentral();
  }

  public function ShopexportZahlweiseget() {
    $id = $this->app->Secure->GetPOST('id');
    $row = $this->app->DB->SelectRow('
        SELECT
        id,
        zahlweise_shop,
        zahlweise_wawision,
        vorabbezahltmarkieren,
        autoversand,
        aktiv,
        keinerechnung,
        fastlane
        FROM
        shopexport_zahlweisen
        WHERE
        id = "' . $id . '"
        ');

    echo json_encode($row);
    $this->app->ExitXentral();
  }

  public function ShopexportZahlweisedelete()
  {
    $id = (int)$this->app->Secure->GetGET("id");
    $sid = $this->app->DB->Select("SELECT shop FROM shopexport_zahlweisen WHERE id='$id' LIMIT 1");                      

    if($id > 0) {
      $this->app->DB->Delete("DELETE FROM shopexport_zahlweisen WHERE id='$id' LIMIT 1");
      $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Eintrag wurde entfernt!</div>  ");
      $this->app->Location->execute("index.php?module=onlineshops&action=edit&id=$sid&msg=$msg#tabs-3");
    }
  }


  public function ShopexportVersandartsave()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $shop = (int)$this->app->Secure->GetPOST('shop');
    $versandart_shop = $this->app->Secure->GetPOST('versandart_shop');
    $versandart_wawision = $this->app->Secure->GetPOST('versandart_wawision');
    $versandart_ausgehend = $this->app->Secure->GetPOST('versandart_ausgehend');
    $produkt_ausgehend = $this->app->Secure->GetPOST('produkt_ausgehend');
    $land = $this->app->Secure->GetPOST('land');
    $autoversand = $this->app->Secure->GetPOST('autoversand');
    $fastlane = (int)$this->app->Secure->GetPOST('fastlane');
    if($fastlane !== 1)
    {
      $fastlane = 0;
    }

    if($autoversand!="1") $autoversand=0;

    $aktiv = 1;

    if ($shop) {
      $this->app->DB->Update("INSERT INTO shopexport_versandarten (id,shop,versandart_shop,versandart_wawision,produkt_ausgehend,versandart_ausgehend,aktiv,autoversand,land,fastlane) VALUES
          ('','$shop','$versandart_shop','$versandart_wawision','{$produkt_ausgehend}','$versandart_ausgehend','$aktiv','$autoversand','$land',$fastlane)");
    }

    echo json_encode(array(
          'status' => 1,
          'statusText' => 'Gespeichert.'
          ));
    $this->app->ExitXentral();

  }

  public function ShopexportSpracheneditsave()
  {
    $id = (int)$this->app->Secure->GetPOST('id');
    $sprache = $this->app->Secure->GetPOST('sprache');
    $land = $this->app->Secure->GetPOST('land');
    $projekt = explode (' ',$this->app->Secure->GetPOST('projekt'));
    $projekt = reset($projekt);
    if($projekt) {
      $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '$projekt' AND abkuerzung <> '' AND geloescht = 0 LIMIT 1");
    }
    $aktiv = $this->app->Secure->GetPOST('aktiv');
    if($aktiv!="1") {
      $aktiv=0;
    }

    if ($id) {
      $this->app->DB->Update("UPDATE shopexport_sprachen SET land='$land',sprache='$sprache',projekt='$projekt',land='$land',
          aktiv='$aktiv' WHERE id = '$id'");
      
      $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
      $this->app->DB->Update("UPDATE shopexport_sprachen SET updatedby = '$bearbeiter', updated = now() WHERE id = '$id'");
    }

    echo json_encode(array(
          'status' => 1,
          'statusText' => 'Gespeichert.'
          ));
    $this->app->ExitXentral();
  }

  public function ShopexportVersandarteditsave()
  {
    $id = (int)$this->app->Secure->GetPOST('id');
    $versandart_shop = $this->app->Secure->GetPOST('versandart_shop');
    $versandart_wawision = $this->app->Secure->GetPOST('versandart_wawision');
    $versandart_ausgehend = $this->app->Secure->GetPOST('versandart_ausgehend');
    $produkt_ausgehend = $this->app->Secure->GetPOST('produkt_ausgehend');
    $autoversand = $this->app->Secure->GetPOST('autoversand');
    $land = $this->app->Secure->GetPOST('land');
    $aktiv = $this->app->Secure->GetPOST('aktiv');
    $fastlane = (int)$this->app->Secure->GetPOST('fastlane');
    if($fastlane !== 1)
    {
      $fastlane = 0;
    }

    if($aktiv!='1')
    {
      $aktiv=0;
    }

    if ($id) {
      $this->app->DB->Update("UPDATE shopexport_versandarten SET versandart_shop='$versandart_shop',versandart_wawision='$versandart_wawision',versandart_ausgehend='$versandart_ausgehend',produkt_ausgehend='$produkt_ausgehend',autoversand='$autoversand',land='$land',
          aktiv='$aktiv',fastlane= $fastlane WHERE id = '$id'");
      $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
      $this->app->DB->Update("UPDATE shopexport_versandarten SET updatedby = '$bearbeiter', updated = now() WHERE id = '$id'");
    }

    echo json_encode(array(
          'status' => 1,
          'statusText' => 'Gespeichert.'
          ));
    $this->app->ExitXentral();

  }

  public function ShopexportSubshopeditsave()
  {
    $id = (int)$this->app->Secure->GetPOST('id');
    $subshopkennung = $this->app->Secure->GetPOST('subshopkennung');
    $projekt = $this->app->Secure->GetPOST('projekt');
    $aktiv = $this->app->Secure->GetPOST('aktiv');
    $sprache = $this->app->Secure->GetPOST('sprache');

    if($aktiv!='1') {
      $aktiv=0;
    }

    $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' AND abkuerzung!='' LIMIT 1");

    if ($id && $projekt > 0) {
      $this->app->DB->Update("UPDATE shopexport_subshop SET subshopkennung='$subshopkennung',projekt='$projekt',sprache = '$sprache',
          aktiv='$aktiv' WHERE id = '$id'");
      $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
      $this->app->DB->Update("UPDATE shopexport_subshop SET updatedby = '$bearbeiter', updated = now() WHERE id = '$id'");
      echo json_encode(array(
            'status' => 1,
            'statusText' => 'Gespeichert.'
            ));
    }
    else {
      echo json_encode(array(
            'status' => 0,
            'statusText' => 'Projekt nicht vorhanden'
            ));
    }

    $this->app->ExitXentral();

  }


  public function ShopexportFreifeldereditsave()
  {
    $id = (int)$this->app->Secure->GetPOST('id');
    $freifeld_shop = $this->app->Secure->GetPOST('freifeld_shop');
    $freifeld_wawi = $this->app->Secure->GetPOST('freifeld_wawi');
    $aktiv = $this->app->Secure->GetPOST('aktiv');

    if($aktiv!='1') {
      $aktiv=0;
    }

    if ($id) {
      $this->app->DB->Update("UPDATE shopexport_freifelder SET freifeld_wawi='$freifeld_wawi',freifeld_shop='$freifeld_shop',
          aktiv='$aktiv' WHERE id = '$id'");
      $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
      $this->app->DB->Update("UPDATE shopexport_freifelder SET updatedby = '$bearbeiter', updated = now() WHERE id = '$id'");
    }

    echo json_encode(array(
          'status' => 1,
          'statusText' => 'Gespeichert.'
          ));
    $this->app->ExitXentral();

  }

  public function ShopexportVersandartget() {
    $id = $this->app->Secure->GetPOST('id');
    $row = $this->app->DB->SelectRow('
        SELECT
        id,
        versandart_shop,
        versandart_wawision,
        produkt_ausgehend,
        versandart_ausgehend,
        autoversand,
        land,
        aktiv,
        fastlane
        FROM
        shopexport_versandarten
        WHERE
        id = "' . $id . '"
        ');

    echo json_encode($row);
    $this->app->ExitXentral();
  }

  public function ShopexportKundengruppenget() {
    $id = $this->app->Secure->GetPOST('id');
    $row = $this->app->DB->SelectRow("SELECT s.id, s.gruppeid, s.extgruppename, s.aktiv, s.apply_to_new_customers AS neukundengruppezuweisen, s.type, IF(s.projekt=0,'',p.abkuerzung) AS projekt, s.shopid
        FROM shopexport_kundengruppen s LEFT JOIN projekt p ON s.projekt = p.id WHERE s.id='$id'");
    echo json_encode($row);
    $this->app->ExitXentral();
  }

  public function ShopexportSubshopget()
  {
    $id = $this->app->Secure->GetPOST('id');
    $row = $this->app->DB->SelectRow('
        SELECT
        s.id,
        s.subshopkennung,
        p.abkuerzung as projekt,
        s.sprache,
        s.aktiv
        FROM
        shopexport_subshop s LEFT JOIN projekt p ON p.id=s.projekt
        WHERE
        s.id = "' . $id . '"
        ');

    echo json_encode($row);
    $this->app->ExitXentral();
  }


  public function ShopexportFreifeldget()
  {
    $id = $this->app->Secure->GetPOST('id');
    $row = $this->app->DB->SelectRow('
        SELECT
        id,
        freifeld_shop,
        freifeld_wawi,
        aktiv
        FROM
        shopexport_freifelder
        WHERE
        id = "' . $id . '"
        ');

    echo json_encode($row);
    $this->app->ExitXentral();
  }


  public function ShopexportVersandartdelete()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $sid = $this->app->DB->Select("SELECT shop FROM shopexport_versandarten WHERE id='$id' LIMIT 1");                      
    if($id > 0) {
      $this->app->DB->Delete("DELETE FROM shopexport_versandarten WHERE id='$id' LIMIT 1");

      $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Eintrag wurde entfernt!</div>  ");
      $this->app->Location->execute("index.php?module=onlineshops&action=edit&id=$sid&msg=$msg#tabs-4");
    }
  }

  public function ShopexportKundengruppendelete()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $sid = $this->app->DB->Select("SELECT shopid FROM shopexport_kundengruppen WHERE id='$id' LIMIT 1");
    if($id > 0 ) {
      $this->app->DB->Delete("DELETE FROM shopexport_kundengruppen WHERE id='$id' LIMIT 1");

      $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Eintrag wurde entfernt!</div>  ");
      $this->app->Location->execute("index.php?module=onlineshops&action=edit&id=$sid&msg=$msg#tabs-8");
    }
}

  public function ShopexportSprachendelete()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $sid = $this->app->DB->Select("SELECT shop FROM shopexport_sprachen WHERE id='$id' LIMIT 1");                      
    if($id > 0) {
      $this->app->DB->Delete("DELETE FROM shopexport_sprachen WHERE id='$id' LIMIT 1");

      $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Eintrag wurde entfernt!</div>  ");
      $this->app->Location->execute("index.php?module=onlineshops&action=edit&id=$sid&msg=$msg#tabs-7");
    }    
  }

  public function ShopexportSubshopdelete()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $sid = $this->app->DB->Select("SELECT shop FROM shopexport_subshop WHERE id='$id' LIMIT 1");                      
    if($id > 0) {
      $this->app->DB->Delete("DELETE FROM shopexport_subshop WHERE id='$id' LIMIT 1");

      $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Eintrag wurde entfernt!</div>  ");
      $this->app->Location->execute("index.php?module=onlineshops&action=edit&id=$sid&msg=$msg#tabs-6");
    }
  }

  public function ShopexportFreifelddelete()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $sid = $this->app->DB->Select("SELECT shop FROM shopexport_freifelder WHERE id='$id' LIMIT 1");                      
    if($id > 0) {
      $this->app->DB->Delete("DELETE FROM shopexport_freifelder WHERE id='$id' LIMIT 1");

      $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Eintrag wurde entfernt!</div>  ");
      $this->app->Location->execute("index.php?module=onlineshops&action=edit&id=$sid&msg=$msg#tabs-5");
    }
  }

  protected function match($haystack, $needle)
  {
    $needle = strtolower($this->app->erp->UmlauteEntfernen($needle));
    $haystack = strtolower($this->app->erp->UmlauteEntfernen($haystack));
    $needlaa = explode(' ',$needle);
    foreach($needlaa as $v) {
      if($v &&strpos($haystack, $v) === false) {
        return false;
      }
    }
    return true;
  }
  
  public function getApps($val = '')
  {
    $val = (String)$val;
    $module = $this->app->erp->getApps();
    if($module)
    {
      if(isset($module['installiert']))
      {
        foreach($module['installiert'] as $k => $v)
        {
          $module['installiert'][$k]['match'] = $this->match($v['Bezeichnung'], $val);
          $module['installiert'][$k]['md5'] = md5($v['Bezeichnung']); 
        }
      }
      if(isset($module['kauf']))
      {
        foreach($module['kauf'] as $k => $v)
        {
          $module['kauf'][$k]['match'] = $this->match($v['Bezeichnung'], $val);
          $module['kauf'][$k]['md5'] = md5($v['Bezeichnung']);
        }
      }
    }
    return $module;
  }

  /**
   * @param int $shopId
   *
   * @return int|null
   */
  public function createShippingArticleByShopId($shopId): ?int
  {
    $shop = $this->app->DB->SelectRow(
      sprintf(
        "SELECT `s`.`projekt`, `art`.`id` 
            FROM `shopexport` AS `s`
            LEFT JOIN `artikel` AS `art` ON `s`.`artikelporto` = art.id 
                    AND (`art`.`geloescht` = 0 OR `art`.`geloescht` IS NULL)
                    AND (`art`.`intern_gesperrt` = 0 OR `art`.`intern_gesperrt` IS NULL)
            WHERE `s`.`id` = %d ",

        $shopId
      )
    );
    if (empty($shop) || !empty($shop['id'])) {
      return null;
    }
    $firstShippingArticle = $this->app->DB->SelectRow(
      'SELECT `art`.`id` 
            FROM `artikel` AS `art`
            LEFT JOIN `projekt` AS `p` ON `art`.projekt = p.id
            WHERE `art`.`porto` = 1 
              AND (`art`.`geloescht` IS NOT NULL OR `art`.geloescht = 0)
                    AND (`art`.`intern_gesperrt` = 0 OR `art`.`intern_gesperrt` IS NULL)
                AND (`p`.`id` IS NULL OR p.`oeffentlich` = 1)
                '
    );
    if (!empty($firstShippingArticle)) {
      $this->app->DB->Update(
        sprintf('UPDATE `shopexport` SET `artikelporto` = %d WHERE `id` = %d ',
          (int)$firstShippingArticle['id'], $shopId
        )
      );

      return (int)$firstShippingArticle['id'];
    }
    $shippingArticle = ['projekt' => $shop['projekt'], 'name_de' => 'Porto', 'proto' => 1];
    $shippingArticle['nummer'] = $this->app->erp->GetNextArtikelnummer('', '1', $shop['projekt']);
    $shippingArticleId = (int)$this->app->erp->InsertUpdateArtikel($shippingArticle);
    if ($shippingArticleId <= 0) {
      return null;
    }
    $this->app->DB->Update(
      sprintf(
        'UPDATE `shopexport` SET `artikelporto` = %d WHERE `id` = %d ',
        $shippingArticleId, $shopId
      )
    );

    return $shippingArticleId;
  }

  /**
   * @param int $shopId
   *
   * @return int|null
   */
  public function createDiscountArticleForShop($shopId): ?int
  {
    $shop = $this->app->DB->SelectRow(
      sprintf(
        'SELECT `s`.`projekt`, `art`.`id` 
        FROM `shopexport` AS `s`
        LEFT JOIN `artikel` AS `art` ON `s`.`artikelrabatt` = art.id 
              AND (`art`.`geloescht` = 0 OR `art`.`geloescht` IS NULL)
              AND (`art`.`intern_gesperrt` = 0 OR `art`.`intern_gesperrt` IS NULL)
        WHERE `s`.`id` = %d',
        $shopId
      )
    );
    if (empty($shop) || !empty($shop['id'])) {
      return null;
    }
    $discountArticle = [
      'projekt'      => $shop['projekt'],
      'name_de'      => 'Rabatt',
      'name_en'      => 'Discount',
      'lagerartikel' => 0,
    ];
    $discountArticle['nummer'] = $this->app->erp->GetNextArtikelnummer('', '1', $shop['projekt']);
    $discountArticleId = (int)$this->app->erp->InsertUpdateArtikel($discountArticle);
    if ($discountArticleId <= 0) {
      return null;
    }
    $this->app->DB->Update(
      sprintf(
        'UPDATE `shopexport` SET `artikelrabatt` = %d WHERE `id` = %d ',
        $discountArticleId, $shopId
      )
    );

    return $discountArticleId;
  }

  /**
   * @param string $auswahlmodul
   *
   * @return array
   */
  public function createInternShop($auswahlmodul)
  {
    if($fehler = $this->app->erp->OnlineshopsLizenzFehler($auswahlmodul)) {
      return ['success'=>false,'error'=>$fehler['Error']];
    }
    $bezeichnung = ucfirst(str_replace('shopimporter_','',$auswahlmodul));
    $i = 1;
    while($this->app->DB->Select("SELECT id FROM shopexport WHERE bezeichnung = '$bezeichnung' LIMIT 1")) {
      $i++;
      $bezeichnung = ucfirst(str_replace('shopimporter_','',$auswahlmodul)).' '.$i;
    }
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `shopexport` 
            (`shoptyp`, `modulename`,`artikelimport`,`demomodus`,`bezeichnung`,`artikeltexteuebernehmen`,
             `versandartenmapping`,
             `zahlungsweisenmapping`,`lagerexport`,`artikelexport`,`datumvon`,`auftragabgleich`,`portoartikelanlegen`, 
             `steuerfreilieferlandexport`,`multiprojekt`,`direktimport`,`rabatteportofestschreiben`) 
             VALUES ('intern','%s',1,1,'%s',1,1,1,0,0,now(),1,1,1,1,1,1)",
        $auswahlmodul, $bezeichnung
      )
    );
    $id = $this->app->DB->GetInsertID();
    $obj = $this->app->erp->LoadModul($auswahlmodul);
    if($obj && method_exists($obj, 'EinstellungenStruktur')) {
      $struktur = $obj->EinstellungenStruktur();
      if(isset($struktur['defaultoncreate']) && is_array($struktur['defaultoncreate'])) {
        $this->app->DB->UpdateArr('shopexport',$id,'id',$struktur['defaultoncreate'],true);
      }
    }
    $this->app->erp->InstallModul($auswahlmodul);
    $this->app->erp->RunHook('shopexport_create', 1, $id);

    return ['id'=>$id];
  }

  /**
   * @param int        $shopId
   * @param null|array $post
   */
  public function saveCreateData($shopId, $post = null)
  {
    $shop = $this->app->DB->SelectRow(
      sprintf('SELECT * FROM `shopexport` WHERE `id` = %d', $shopId)
    );
    $obj = $this->app->loadModule($shop['modulename']);
    if($obj === null) {
      return;
    }
    $form = $obj->getCreateForm();
    $data = json_decode($shop['einstellungen_json'], true);
    if(empty($data) || !is_array($data)) {
      $data = ['felder'=>[]];
    }
    if(empty($data['felder']) || !is_array($data['felder'])) {
      $data['felder'] = [];
    }
    if($post === null) {
      foreach($form as $row) {
        if(empty($row['inputs'])) {
          continue;
        }
        foreach($row['inputs'] as $input) {
          if(!empty($input['name'])){
            $post[$input['name']] = !isset($this->app->Secure->POST[$input['name']])
              ?null:$this->app->Secure->POST[$input['name']];
          }
        }
      }
    }
    if(empty($post)) {
      return;
    }
    foreach($post as $key => $value) {
      $data['felder'][$key] = $value;
    }
    $this->app->DB->Update(
      sprintf(
        "UPDATE `shopexport` SET `einstellungen_json` = '%s' WHERE `id` = %d",
        $this->app->DB->real_escape_string(json_encode($data)), $shopId
      )
    );
  }

  /**
   * @var int ShopId
   *
   * @return JsonResponse
   */
  public function getVueShopexportSuccessPage($shopId)
  {
    $succespage = [
      'type' => 'defaultPage',
      'icon' => 'add-person-icon',
      'headline'=> 'Shop angelegt',
      'subHeadline'=> 'Der Shop wurde angelegt',
      'ctaButtons' => [
        [
          'title'  => 'Klasse',
          'action' => 'close',
          'link'   => 'index.php?module=onlineshops&action=edit&id='.$shopId
        ]
      ]
    ];

    return new JsonResponse(
      ['page'=>$succespage]
    );
  }

  /**
   * @param $shopId
   * @param $data
   *
   * @return JsonResponse
   */
  public function getVueShopexportAppNewSuccessPageYt($shopId, $data): JsonResponse
  {
    $youtubeLink = 'https://www.youtube.com/embed/49PICIq3XP8';
    $shopType = $this->app->DB->Select(
      sprintf(
        'SELECT `modulename` FROM `shopexport` WHERE `id` = %d',
        $shopId
      )
    );
    if(!empty($shopType)
      && ($shopModule = $this->app->loadModule($shopType)) !== null
      && method_exists($shopModule, 'getYoutubeLink')
    ) {
      $youtubeLink = $shopModule->getYoutubeLink();
    }

    $buttons = [];
    $data = json_decode($data, true);
    if(!empty($data['url'])) {
      $buttons[] =
        [
          'title'  => 'Zum Shop',
          'action' => 'close',
          'link'   => $data['url'],
        ];
    }
    $buttons[] =
      [
        'title'  => 'In Xentral bleiben',
        'action' => 'close',
        'link'   => 'index.php?module=onlineshops&action=edit&id='.$shopId,
      ];
    $succespage = [
      'type' => 'defaultPage',
      'icon' => 'add-person-icon',
      'headline'=> 'Shop wurde verknüpft',
      'subHeadline'=> '',
      'headerMedia' => [
          'type' => 'video',
          'link' => $youtubeLink
      ],

      'ctaButtons' => $buttons,
    ];

    return new JsonResponse(
      [
        'page'=>$succespage,
      ]
    );
  }

  /**
   * @var int $shopId
   *
   * @return JsonResponse
   */
  public function getVueShopexportAppNewSuccessPage($shopId, $data): JsonResponse
  {
    $succespage = [
      'type' => 'form',
      'submitType' => 'submit',
      'icon' => 'add-person-icon',
      'headline'=> 'Shop wurde verknüpft',
      'submitUrl' => 'index.php?module=onlineshops&action=appnew&cmd=createdata',
      'form' => [
        'id' => 0,
        'name' => 'createPriceGroupHead',
        'inputs' => [],
      ],
      'subHeadline'=> '',
      'ctaButtons' => [
        [
          'title'  => 'Weiter',
          'type'   => 'submit',
          'action' => 'submit',
        ]
      ]
    ];

    return new JsonResponse(
      [
        'page'=>$succespage,
        'dataRequiredForSubmit' => [
          'step' => 2,
          'shopId' => $shopId,
          'data' => $data,
        ]
      ]
    );
  }

  /**
   * @return array[]
   */
  public function getPriceGroupForm(): array
  {
    $ret = [
      [
        'id' => 0,
        'name' => 'createPriceGroupHead',
        'inputs' => [
          [
            'label' => 'Neue Preisgruppe erstellen',
            'type' => 'checkbox',
            'name' => 'createPriceGroup',
            'validation' => false,
          ],
        ],
      ],
      [
        'id' => 1,
        'name' => 'usePriceGroupHead',
        'inputs' => [
          [
            'label' => 'Bestehende Preisgruppe verwenden',
            'type' => 'select',
            'name' => 'usePriceGroup',
            'validation' => false,
            'options' => $this->getVuePriceGroups(),
          ]
        ],
      ]
    ];

    return $ret;
  }

  /**
   * @return array
   */
  public function getVuePriceGroups(): array
  {
    $priceGroups = array_merge(
      [''=>''],
      $this->app->DB->SelectPairs(
        sprintf(
          "SELECT g.kennziffer, g.name 
          FROM `gruppen` AS `g` 
          WHERE g.aktiv = 1 AND g.art = 'preisgruppe' %s 
          ORDER BY g.name",
          $this->app->erp->ProjektRechte('g.projekt')
        )
      )
    );

    return $this->convertArrayToVueOptions($priceGroups);
  }

  /**
   * @param array $array
   *
   * @return array
   */
  public function convertArrayToVueOptions($array)
  {
    if(empty($array) || !is_array($array)) {
      return [];
    }
    $ret = [];
    foreach($array as $value => $text) {
      $ret[] = [
        'value' => $value,
        'text' => $text,
      ];
    }

    return $ret;
  }

  /**
   * @var null|array $options
   *
   * @return array[]
   */
  public function getFeatureForm($options = null): array
  {
    $ret = [];
    if(!empty($options['canExportArticle'])) {
      $ret[] = [
        'id' => 0,
        'name' => 'exportArticlesGroup',
        'inputs' => [
          [
            'label' => 'Artikel übertragen',
            'type' => 'checkbox',
            'name' => 'export_articles',
            'validation' => false,
          ],

        ],
      ];
    }
    if(!empty($options['canSyncArticles'])) {
      $ret[] = [
        'id' => 1,
        'name' => 'syncArticlesGroup',
        'inputs' => [
          [
            'label' => 'Lagerbestände synchronisieren',
            'type' => 'checkbox',
            'name' => 'sync_articles',
            'validation' => false,
          ],

        ],
      ];
    }
    $ret[] =       [
      'id' => 2,
      'name' => 'autoOrderGroup',
      'inputs' => [
        [
          'label' => 'Neue Aufträge automatisch abholen',
          'type' => 'checkbox',
          'name' => 'auto_order',
          'validation' => false,
        ],

      ],
    ];

    return $ret;
  }

  /**
   * @var string     $moduleName
   * @var string     $shopName
   * @var null|array $requiredForSubmit
   * @var null|array $options
   *
   * @return JsonResponse
   */
  public function getPriceGroupPage($moduleName, $shopName, $requiredForSubmit = null, $options = null)
  {
    if($requiredForSubmit === null) {
      $requiredForSubmit = $this->app->Secure->POST;
      $requiredForSubmit['step'] = 10;
    }

    $page = [
      'type' => 'form',

      'submitType' => 'submit',
      'icon'=> 'password-icon',
      'headline' => $shopName,
      'subHeadline' => sprintf('Möchtest Du eine Preisgruppe für %s verwenden?', $shopName),
      'submitUrl' => 'index.php?module=onlineshops&action=create&cmd=saveassistent&shopmodule='.$moduleName,
      'form' => $this->getPriceGroupForm(),
      'ctaButtons' => [
        [
          'title' => 'Weiter',
          'type' => 'submit',
          'action' => 'submit',
        ],
      ]
    ];

    return new JsonResponse(
      [
        'page' => $page,
        'dataRequiredForSubmit' => $requiredForSubmit,
      ]
    );
  }

  /**
   * @var string     $moduleName
   * @var string     $shopName
   * @var null|array $requiredForSubmit
   * @var null|array $options
   *
   * @return JsonResponse
   */
  public function getStep2Page($moduleName, $shopName, $requiredForSubmit = null, $options = null)
  {
    if($requiredForSubmit === null) {
      $requiredForSubmit = $this->app->Secure->POST;
      $requiredForSubmit['step'] = 2;
    }
    $page = [
      'type' => 'form',

      'submitType' => 'submit',
      'icon'=> 'password-icon',
      'headline' => $shopName,
      'subHeadline' => 'Welche Features möchtest du nutzen?',
      'submitUrl' => 'index.php?module=onlineshops&action=create&cmd=saveassistent&shopmodule='.$moduleName,
      'form' => $this->getFeatureForm($options),
      'ctaButtons' => [
        [
          'title' => 'Weiter',
          'type' => 'submit',
          'action' => 'submit',
        ],
      ]
    ];

    return new JsonResponse(
      [
        'page' => $page,
        'dataRequiredForSubmit' => $requiredForSubmit,
      ]
    );
  }

  /**
   * @param string     $moduleName
   * @param string     $shopName
   * @param null|array $requiredForSubmit
   * @param null|array $options
   *
   * @return JsonResponse
   */
  public function getExportArticlePage($moduleName, $shopName, $requiredForSubmit = null, $options = null)
  {
    if($requiredForSubmit === null) {
      $requiredForSubmit = $this->app->Secure->POST;
      $requiredForSubmit['step'] = 3;
    }

    $capabilites = ShopimporterBase::shopCapabilities();
    $capabilites = empty($capabilites[$moduleName])?[]:$capabilites[$moduleName];
    $pictures = !empty($capabilites['bilder'])
      && $capabilites['bilder']['createarticle'] === ShopimporterBase::CAN;
    $attributes = !empty($capabilites['eigenschaften'])
      && $capabilites['eigenschaften']['createarticle'] === ShopimporterBase::CAN;
    $categories= !empty($capabilites['kategorie'])
      && $capabilites['kategorie']['createarticle'] === ShopimporterBase::CAN;
    $variants = !empty($capabilites['varianten'])
      && $capabilites['varianten']['createarticle'] === ShopimporterBase::CAN;
    $crossselling = !empty($capabilites['crossselling'])
      && $capabilites['crossselling']['createarticle'] === ShopimporterBase::CAN;
    $bulkPrices = !empty($capabilites['staffelpreise'])
      && $capabilites['staffelpreise']['createarticle'] === ShopimporterBase::CAN;


    $exportOptions = [
      [
        'text'  => '',
        'value' => '',
      ]
    ];
    if(!empty($options['canExportArticle'])){
      $exportOptions[] = [
        'text' => 'xentral zum Shop',
        'value' => 'x2s',
      ];
    }
    if(!empty($options['canImportArticle'])) {
      $exportOptions[] = [
        'text' => 'Shop zu xentral',
        'value' => 's2x',
      ];
    }

    $forms = [
      [
        'id' => 0,
        'name' => 'exportFromGroup',
        'inputs' => [
          [
            'label' => 'Richtung',
            'type' => 'select',
            'name' => 'exportFrom',
            'validation' => false,
            'options' => $exportOptions,
          ],
        ],
      ],
      ];
    if($pictures) {
      $forms[] = [
        'id' => 1,
        'name' => 'syncPicturesGroup',
        'inputs' => [
          [
            'label' => 'Bilder mit übertragen',
            'type' => 'checkbox',
            'name' => 'sync_picture',
            'validation' => false,
          ],

        ],
      ];
    }
    if($attributes) {
      $forms[] = [
        'id' => 2,
        'name' => 'syncAttributesGroup',
        'inputs' => [
          [
            'label' => 'Eigenschaften mit übertragen',
            'type' => 'checkbox',
            'name' => 'sync_attributes',
            'validation' => false,
          ],
        ],
      ];
    }
    if($categories) {
      $forms[] = [
        'id' => 3,
        'name' => 'syncCategoriesGroup',
        'inputs' => [
          [
            'label' => 'Kategorien mit übertragen',
            'type' => 'checkbox',
            'name' => 'sync_categories',
            'validation' => false,
          ],
        ],
      ];
    }
    if($variants) {
      $forms[] = [
        'id' => 4,
        'name' => 'syncVariantsGroup',
        'inputs' => [
          [
            'label' => 'Varianten mit übertragen',
            'type' => 'checkbox',
            'name' => 'sync_variants',
            'validation' => false,
          ],
        ],
      ];
    }
    if($crossselling) {
      $forms[] = [
        'id' => 5,
        'name' => 'syncCrosssellingGroup',
        'inputs' => [
          [
            'label' => 'Crossselling mit übertragen',
            'type' => 'checkbox',
            'name' => 'sync_crossselling',
            'validation' => false,
          ],
        ],
      ];
    }
    if($bulkPrices) {
      $forms[] = [
        'id' => 6,
        'name' => 'syncBulkpricesGroup',
        'inputs' => [
          [
            'label' => 'Staffelpreise mit übertragen',
            'type' => 'checkbox',
            'name' => 'sync_bulkprices',
            'validation' => false,
          ],
        ],
      ];
    }

    $page = [
      'type' => 'form',
      'submitType' => 'submit',
      'icon'=> 'password-icon',
      'headline' => $shopName,
      'subHeadline' => 'Welche Artikeldaten sollen übertragen werden?',
      'submitUrl' => 'index.php?module=onlineshops&action=create&cmd=saveassistent&shopmodule='.$moduleName,
      'form' => $forms,
      'ctaButtons' => [
        [
          'title' => 'Weiter',
          'type' => 'submit',
          'action' => 'submit',
        ],
      ]
    ];

    return new JsonResponse(
      [
        'page' => $page,
        'dataRequiredForSubmit' => $requiredForSubmit,
      ]
    );
  }

  /**
   * @param string $module
   *
   * @return JsonResponse
   */
  public function getBoosterVue($module)
  {
    /** @var Appstore $appstore */
    $appstore = $this->app->loadModule('appstore');
    $moduleName = $appstore->getAppNameByKey($module);
    $shopModule = $this->app->loadModule($module);
    if($moduleName === null) {
      $moduleName = ucfirst(substr($module, 13));
    }
    $headLine = $moduleName;
    $subHeadline = 'Bitte gehe auf '.$moduleName.' und verbinden Dich mit dienem Xentral';
    if($shopModule !== null && method_exists($shopModule, 'getBoosterSubHeadline')) {
      $subHeadline = $shopModule->getBoosterSubHeadline();
    }
    if($shopModule !== null && method_exists($shopModule, 'getBoosterHeadline')) {
      $headLine = $shopModule->getBoosterHeadline();
    }
    $page = [
      'type' => 'defaultPage',
      'icon'=> 'password-icon',
      'headline' => $headLine,
      'subHeadline' => $subHeadline,
      'ctaButtons' => [
        [
          'title' => 'OK',
          'type'  => 'close',
          'action'  => 'close',
        ],
      ]
    ];

    $ret = [
      'pages'=>
        [
          $page
        ],
    ];

    return new JsonResponse($ret);
  }

  public function ShopexportCreate()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    $auswahlmodul = (string)$this->app->Secure->GetPOST('auswahl');
    if(empty($auswahlmodul)) {
      $auswahlmodul = (string)$this->app->Secure->GetGET('auswahl');
    }
    if(empty($auswahlmodul)) {
      $auswahlmodul = (string)$this->app->Secure->GetPOST('shopmodule');
    }
    if(strpos($auswahlmodul, 'shopimporter_') === 0 && substr($auswahlmodul, -7) === 'booster') {
      $module = substr($auswahlmodul, 0, -7);
      if($cmd === 'getassistant') {
        return $this->getBoosterVue($module);
      }
      $this->app->Location->execute('index.php?module=onlineshops&action=create&cmd='.$module.'&sid=booster');
    }
    if(strpos($cmd, 'shopimporter_') === 0) {
      $sid = $this->app->Secure->GetGET('sid');
      if($sid === 'booster') {
        $this->app->Tpl->Add(
          'TAB1',
          '<script class="booster" data-module="' . $cmd . '" type="application/json"></script>'
        );
      }
      else{
        /** @var ShopimporterBase $obj */
        $obj = $this->app->loadModule($cmd);
        if($obj !== null && method_exists($obj, 'AuthByAssistent')){
          $this->app->Tpl->Add(
            'TAB1',
            '<script class="autoOpenModule" data-module="' . $cmd . '" type="application/json"></script>'
          );
        }
      }
    }
    if($cmd === 'saveassistent') {
      $shopId = $this->app->Secure->GetPOST('id','','',true);
      $shopModule = $this->app->Secure->GetPOST('shopmodule');
      $syncArticles = !empty($this->app->Secure->GetPOST('sync_articles'));
      $autoOrder = !empty($this->app->Secure->GetPOST('auto_order'));
      $syncPicture = !empty($this->app->Secure->GetPOST('sync_picture'));
      $syncCategories = !empty($this->app->Secure->GetPOST('sync_categories'));
      $syncAttributes = !empty($this->app->Secure->GetPOST('sync_attributes'));
      $syncVariants = !empty($this->app->Secure->GetPOST('sync_variants'));
      $syncCrossselling = !empty($this->app->Secure->GetPOST('sync_crossselling'));
      $syncBulkprices = !empty($this->app->Secure->GetPOST('sync_bulkprices'));
      $exportFrom = $this->app->Secure->GetPOST('exportFrom');
      $step = (int)$this->app->Secure->GetPOST('step');
      $exportArticles = !empty($this->app->Secure->GetPOST('export_articles'));
      $usePriceGroup = (string)$this->app->Secure->GetPOST('usePriceGroup');
      $createPriceGroup = !empty($this->app->Secure->GetPOST('createPriceGroup'));
      if($shopId === 'NEW'){
        $postData = $this->app->Secure->POST;
        /** @var ShopimporterBase $obj */
        $obj = $this->app->loadModule($shopModule);
        $canImportArticle = false;
        if($obj !== null && method_exists($obj, 'AuthByAssistent')) {
          $json = $obj->AuthByAssistent();
          if($json instanceof JsonResponse) {
            return $json;
          }
          if(method_exists($obj,'ImportGetArticleList')) {
            $canImportArticle = true;
          }
        }

        if($obj !== null && method_exists($obj, 'updatePostDataForAssistent')) {
          $postData = $obj->updatePostDataForAssistent($postData);
        }
        $options = [
          'canImportArticle' => $canImportArticle,
          'canExportArticle' => true,
          'canSyncArticles'  => true,
          'canExportPrice'   => true,
        ];
        if($obj !== null && method_exists($obj, 'getExportArticleOptions')) {
          $options = $obj->getExportArticleOptions($options);
        }
        if($step < 2) {
          $postData['step'] = 2;

          return $this->getStep2Page($shopModule, ucfirst(substr($shopModule, 13)), $postData, $options);
        }
        if($step === 2 && $exportArticles) {
          $postData['step'] = 3;
          return $this->getExportArticlePage(
            $shopModule,
            ucfirst(substr($shopModule, 13)),
            $postData,
            $options
          );
        }
        if($step >= 2 && $step < 10 && !empty($options['canExportPrice'])) {
          $postData['step'] = 10;
          return $this->getPriceGroupPage($shopModule, ucfirst(substr($shopModule, 13)), $postData, $options);
        }

        $check = $this->createInternShop($shopModule);
        if(!empty($check['error'])){
          $data = ['error' => $check['error']];
          return new JsonResponse($data, JsonResponse::HTTP_BAD_REQUEST);
        }
        if(!empty($check['id'])){
          $shopId = $check['id'];
          $this->createShippingArticleByShopId($shopId);
          $this->createDiscountArticleForShop($shopId);
          if(method_exists($obj, 'getStructureDataForClickByClickSave')) {
            $structureData = $obj->getStructureDataForClickByClickSave();
            if(!empty($structureData)) {
              $this->saveCreateData($shopId, $structureData);
            }
          }
          if(method_exists($obj, 'afterCreateClickByClick')) {
            $obj->afterCreateClickByClick($shopId);
          }
        }
      }
      if($shopId > 0) {
        $this->saveCreateData($shopId);
        $shopArr = [];
        if($exportFrom === 'x2s') {
          $shopArr['autosendarticle'] = 1;
          $shopArr['artikelexport'] = 1;
        }
        if($syncPicture) {
          $shopArr['shopbilderuebertragen'] = 1;
        }
        if($syncCategories) {
          $shopArr['kategorienuebertragen'] = 1;
        }
        if($syncAttributes) {
          $shopArr['eigenschaftenuebertragen'] = 1;
        }
        if($syncVariants) {
          $shopArr['variantenuebertragen'] = 1;
        }
        if($syncCrossselling) {
          $shopArr['crosssellingartikeluebertragen'] = 1;
        }
        if($syncBulkprices) {
          $shopArr['staffelpreiseuebertragen'] = 1;
        }
        if($syncArticles) {
          $shopArr['lagerexport'] = 1;
        }
        if($autoOrder) {
          $shopArr['cronjobaktiv'] = 1;
          $shopArr['demomodus'] = 0;
        }
        if($obj !== null && method_exists($obj, 'updateShopexportArr')) {
          $shopArr = $obj->updateShopexportArr($shopArr, $postData);
        }
        if(empty($shopArr['preisgruppe'])){
          if($usePriceGroup !== '') {
            $shopArr['preisgruppe'] = $this->getPriceGroupIdFromCode($usePriceGroup);
          }
          elseif($createPriceGroup){
            $shopArr['preisgruppe'] = $this->createPriceGroupByShopId($shopId);
          }
        }
        if(!empty($shopArr)) {
          $this->app->DB->UpdateArr('shopexport', $shopId, 'id', $shopArr, true);
        }

        if($autoOrder && method_exists($obj, 'EinstellungenStruktur')) {
          $stucture = $obj->EinstellungenStruktur();
          if(empty($stucture['ausblenden']) || !in_array('zeitraum',$stucture['ausblenden'])) {
            $shopArr = [
              'anzgleichzeitig' => 50,
              'datumvon'        => date('Y-m-d H:i:s'),
            ];
            $this->app->DB->UpdateArr('shopexport', $shopId, 'id', $shopArr, true);
          }
        }

        if($exportFrom === 's2x') {
          $this->app->DB->Insert(
            sprintf(
              "INSERT INTO `onlineshops_tasks`
              (`shop_id`, `command`, `status`, `counter`, `created`, `lastupdate`) 
              VALUES (%d, 'GetArticleList', 'created', 0, NOW(), NOW())",
              $shopId
            )
          );
        }
        return $this->getVueShopexportSuccessPage($shopId);
      }
      $data = ['error' => 'Unknown error'];

      return new JsonResponse($data, JsonResponse::HTTP_BAD_REQUEST);
    }

    if($cmd === 'getbooster') {
      $module = $this->app->Secure->GetPOST('shopmodule');
      return $this->getBoosterVue($module);
    }

    if($cmd === 'getassistant') {
      $module = $this->app->Secure->GetPOST('shopmodule');
      $isBooster = substr($module, -7) === 'booster';
      $obj = $this->app->loadModule($module);
      if($obj === null && $isBooster) {
        $module = substr($module, 0, -7);
        return $this->getBoosterVue($module);
      }
      if(!method_exists($obj, 'getCreateForm')) {
        return new JsonResponse(['location' => 'index.php?module=onlineshops&action=create&auswahl='.$module]);
      }
      $form = $obj->getCreateForm();
      if(!empty($form)) {
        $form[count($form) - 1]['link'] = [
          'link' => 'index.php?module=onlineshops&action=create&auswahl=' . $module,
          'title' => 'Expertenmodus',
        ];
      }
      $page = [
        'type' => 'form',
        'dataRequiredForSubmit' =>
          [
            'shopmodule' => $module,
            'id' => 'NEW',
          ],
        'submitType' => 'submit',
        'icon'=> 'password-icon',
        'headline' => ucfirst(substr($module, 13)),
        'subHeadline' => method_exists($obj, 'getClickByClickHeadline')?$obj->getClickByClickHeadline():'Bitte Zugangsdaten eingeben',
        'submitUrl' => 'index.php?module=onlineshops&action=create&cmd=saveassistent&shopmodule='.$module,
        'form' => $form,
        'ctaButtons' => [
          [
            'title' => 'Weiter',
            'type' => 'submit',
            'action' => 'submit',
          ],
        ]
      ];

      $ret = [
        'pages'=>
        [
          $page
        ],
      ];

      return new JsonResponse($ret);
    }
    
    $module = $this->getApps($this->app->Secure->GetPOST('val'));
    
    if($cmd === 'suche') {
      $anzeigen = '';
      $ausblenden = '';
      if($module) {
        if(isset($module['installiert'])) {
          foreach($module['installiert'] as $k => $v) {
            if($v['match']){
              if($anzeigen != '') {
                $anzeigen .= ';';
              }
              $anzeigen .= 'm'.md5($v['Bezeichnung']);
            }
            else {
              if($ausblenden != '') {
                $ausblenden .= ';';
              }
              $ausblenden .= 'm'.md5($v['Bezeichnung']);
            }
          }
        }
        if(isset($module['kauf'])) {
          foreach($module['kauf'] as $k => $v) {
            if($v['match']) {
              if($anzeigen != '') {
                $anzeigen .= ';';
              }
              $anzeigen .= 'm'.md5($v['Bezeichnung']);
            }
            else {
              if($ausblenden != '') {
                $ausblenden .= ';';
              }
              $ausblenden .= 'm'.md5($v['Bezeichnung']);
            }
          }
        }
      }
      echo json_encode(array('anzeigen'=>$anzeigen,'ausblenden'=>$ausblenden));
      $this->app->ExitXentral();
    }
    
    if($this->app->Secure->GetGET('auswahl')) {
      //$bezeichnung = $this->app->Secure->GetPOST('bezeichnung');
      $auswahlmodul = $this->app->Secure->GetGET('auswahl');
      if($auswahlmodul === 'extern') {
        $bezeichnung = 'Onlineshop';
        $i = 1;
        while($this->app->DB->Select(
          sprintf("SELECT `id` FROM `shopexport` WHERE `bezeichnung` = '%s' LIMIT 1", $bezeichnung))) {
          $i++;
          $bezeichnung = 'Onlineshop '.$i;
        }
        $this->app->DB->Insert(
          sprintf(
            "INSERT INTO `shopexport` 
            (
             `artikelimport`,`demomodus`,`bezeichnung`,`artikeltexteuebernehmen`,`versandartenmapping`,
             `zahlungsweisenmapping`, `lagerexport`,`artikelexport`,`datumvon`,`auftragabgleich`,
             `portoartikelanlegen`,`steuerfreilieferlandexport`,`multiprojekt`
             ) 
             VALUES (1,1,'%s',1,1,1,0,0,now(),1,1,1,1)",
            $bezeichnung
          )
        );
        $id = $this->app->DB->GetInsertID();
        $this->app->erp->RunHook('shopexport_create', 1, $id);
        $this->app->Location->execute('index.php?module=onlineshops&action=edit&id='.$id.'#tabs-2');
      }
      if($this->app->erp->ModulVorhanden($auswahlmodul)) {
        $check = $this->createInternShop($auswahlmodul);
        if(!empty($check['error'])) {
          $msg = $this->app->erp->base64_url_encode('<div class="error">'.$check['Error'].'</div>');
          $this->app->Location->execute('index.php?module=onlineshops&action=list&msg='.$msg);
        }
        $this->app->Location->execute('index.php?module=onlineshops&action=edit&id='.$check['id']);
      }
    }
    
    /** @var Appstore $appstore */
    $appstore = $this->app->erp->LoadModul('appstore');
    $modullist = $this->app->erp->getApps();
    $appstore->AddModuleHtml(
      $modullist, 'shopimporter_', 'index.php?module=onlineshops&action=create&get=',
      [
        'title' => 'Extern',
        'link' => 'index.php?module=onlineshops&action=create&auswahl=extern',
      ]
    );

    $this->ShopexportMenu();
    $this->app->ModuleScriptCache->IncludeWidgetNew('ClickByClickAssistant');
    $this->app->Tpl->Parse('PAGE', 'shopexport_neu.tpl');
  }

  /**
   * @param int $shopId
   *
   * @return int|null
   */
  public function createPriceGroupByShopId($shopId): ?int
  {
    $shopName = $this->app->DB->Select(sprintf('SELECT `bezeichnung` FROM `shopexport` WHERE `id` = %d', $shopId));
    $startCode = preg_replace('/[^A-Z0-9]/','',str_replace(' ', '', strtoupper($shopName)));
    $code = $startCode;
    $startName = $shopName;
    $name = $startName;
    $counter = 0;
    while(
      (int)$this->app->DB->Select(
        sprintf(
          "SELECT COUNT(`id`) FROM `gruppen` WHERE `kennziffer` = '%s' OR `name` = '%s'",
          $this->app->DB->real_escape_string($code), $this->app->DB->real_escape_string($name)
        )
      ) > 0
    ) {
      $counter++;
      $code = $startCode.$counter;
      $name = $startName.' '.$counter;
    }

    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `gruppen` (`name`, `art`, `kennziffer`, `aktiv`, `webid`) 
            VALUES ('%s', 'preisgruppe', '%s', 1, '') ",
        $this->app->DB->real_escape_string($name), $this->app->DB->real_escape_string($code)
      )
    );
    $groupId = (int)$this->app->DB->GetInsertID();
    if($groupId <= 0) {
      return null;
    }

    return $groupId;
  }

  /**
   * @param string $code
   *
   * @return int|null
   */
  public function getPriceGroupIdFromCode($code): ?int
  {
    $groupId = $this->app->DB->Select(
      sprintf(
        "SELECT g.id
        FROM `gruppen` AS `g` 
        WHERE g.aktiv = 1 AND g.art = 'preisgruppe' AND g.kennziffer = '%s' %s 
        ORDER BY g.name
        LIMIT 1",
        $this->app->DB->real_escape_string($code), $this->app->erp->ProjektRechte('g.projekt')
      )
    );
    if(empty($groupId)) {
      return null;
    }

    return (int)$groupId;
  }

  public function ShopexportList()
  {
    $this->ShopexportMenu();

    if($this->app->DB->Select("SELECT id FROM shopexport WHERE aktiv = 1 AND cronjobaktiv LIMIT 1"))
    {
      if(!$this->app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'shopimport' AND aktiv = 1 AND art = 'periodisch' LIMIT 1"))
      {
        if($this->app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'shopimport' AND aktiv = 1 AND art <> 'periodisch' LIMIT 1"))
        {
          $this->app->Tpl->Add('MESSAGE', '<div class="error">{|Der Prozessstarter &quot;shopimport&quot; ist auf &quot;Uhrzeit&quot; eingestellt. Es werden die Einstellungen &quot;periodisch&quot; mit der Periode 5 empfohlen|}</div>');
        }
      }
    }
    
    parent::ShopexportList();
  }

  /**
   * @param array $importerCapabilities
   * @param array $featureKeys
   * @param array $shopNames
   * @param array $sequence
   *
   * @return array
   */
  protected function getShopFeaturesSorted($importerCapabilities, $featureKeys, $shopNames, $sequence): array
  {
      $data = [];

      $sequence = array_intersect($sequence, array_keys($importerCapabilities));

      $cShops = count($sequence);
      $cTables = (int)ceil($cShops / 10);
      $cTablesPerRow = (int)ceil($cShops / $cTables);

      for($iTable = 0; $iTable < $cTables; $iTable++) {
          $data[$iTable]['tabheader']['tabdescription'] = '';
      }
      foreach($sequence as $shopKey => $shop) {
          $iTable = (int)floor($shopKey / $cTablesPerRow);
          $data[$iTable]['tabheader'][$shop] = $shopNames[$shop];
      }
      foreach($featureKeys as $featureKey) {
          $name = !empty($baseImporterCapabilities[$featureKey]['name'])?$baseImporterCapabilities[$featureKey]['name']:$featureKey;
          for($iTable = 0; $iTable < $cTables; $iTable++) {
              $data[$iTable][$featureKey]['tabdescription'] = html_entity_decode($name);
          }
          foreach($sequence as $shopKey => $shop) {
              $iTable = (int)floor($shopKey / $cTablesPerRow);
              $data[$iTable][$featureKey][$shop] = [
                  'export' => !empty($importerCapabilities[$shop][$featureKey]['createarticle'])?$importerCapabilities[$shop][$featureKey]['createarticle']:null,
                  'import' => !empty($importerCapabilities[$shop][$featureKey]['importarticle'])?$importerCapabilities[$shop][$featureKey]['importarticle']:null];
          }
      }

      return $data;
  }

  public function ShopexportFeatures()
  {
    $this->ShopexportMenu();
    /** @var ShopimporterBase $obj */
    $obj = new ShopimporterBase();
    $baseImporterCapabilities = $obj->importerCapability();
    $featureKeys = [array_keys($baseImporterCapabilities)];
    $module = $this->getApps();
    $importerCapabilities = [];
    $shopNames = [];

    $shopCapabilities = ShopimporterBase::shopCapabilities();
    foreach ($shopCapabilities as $shopModule => $capabilities){
      $importerCapabilities[$shopModule] = $capabilities;
      $featureKeys[] = array_keys($capabilities);
      $shopNames[$shopModule] = ucfirst(str_replace('shopimporter_','',$shopModule));
    }

    $featureKeys = array_merge(...$featureKeys);
    $featureKeys = array_unique($featureKeys);

    //fuer marketing auftrag immer importierbar zeigen
    foreach ($importerCapabilities as &$item) {
        if ($item['auftrag']['importarticle'] === 4) {
            $item['auftrag']['importarticle'] = 3;
        }
    }
    unset($item);

    $sequenceShop =  [
        'shopimporter_shopware',
        'shopimporter_shopware6',
        'shopimporter_shopify',
        'shopimporter_magento',
        'shopimporter_magento2',
        'shopimporter_woocommerce',
        'shopimporter_gambio',
        'shopimporter_oxid',
        'shopimporter_presta',
        'shopimporter_getcore',
        'shopimporter_epages',
        'shopimporter_spryker',
        'shopimporter_hhg',
        'shopimporter_modified',
    ];
    $sequenceMarket = [
        'shopimporter_amazon',
        'shopimporter_ebay',
        'shopimporter_real',
        'shopimporter_hood',
        'shopimporter_rakuten',
        'shopimporter_manomano',
        'shopimporter_etsy',
        'shopimporter_cdiscount',
    ];
    $sequenceMisc = [
        'shopimporter_tillhub',
        'shopimporter_billbee',
    ];

    $additionalMisc = array_diff(array_keys($importerCapabilities), $sequenceMarket, $sequenceShop, $sequenceMisc);
    $sequenceMisc = array_merge($sequenceMisc, $additionalMisc);
    $features = [];
    $features['shop'] = $this->getShopFeaturesSorted(
        $importerCapabilities,
        $featureKeys,
        $shopNames,
        $sequenceShop
    );
    $features['market'] = $this->getShopFeaturesSorted(
        $importerCapabilities,
        $featureKeys,
        $shopNames,
        $sequenceMarket
    );
    $features['misc'] = $this->getShopFeaturesSorted(
        $importerCapabilities,
        $featureKeys,
        $shopNames,
        $sequenceMisc
    );

    /** @var \Xentral\Components\Template\Template $tmp */
    $tmp = $this->app->Container->get('Template');
    $tmp->setDefaultNamespace('Modules/Onlineshop');
    $tmp->assign('features', $features);
    $table = $tmp->fetch('feature.tpl');
    //$html
    $this->app->Tpl->Add('SHOPTABS', $table);
    //$this->app->Tpl->Add('TAB1','<pre>'.print_r($featureKeys,true).'</pre>');

    $this->app->Tpl->Parse('PAGE','onlineshops_features.tpl');
  }

  public function ShopexportMenu()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->MenuEintrag('index.php?module=onlineshops&action=create','Neu');
    $action = $this->app->Secure->GetGET('action');
    if(($action==='list' || $action === 'features') && $id <= 0) {
      $this->app->erp->Headlines('Shops und Marktpl&auml;tze');
      $this->app->erp->MenuEintrag('index.php?module=onlineshops&action=list','&Uuml;bersicht');
      $this->app->erp->MenuEintrag('index.php?module=onlineshops&action=features','Shopfunktionen');
    }

    if($id > 0) {
      $name = $this->app->DB->Select("SELECT bezeichnung FROM shopexport WHERE id='$id' LIMIT 1");
      $this->app->erp->Headlines('', $name);
      //$this->app->Tpl->Add('KURZUEBERSCHRIFT2',$name);
      $this->app->erp->MenuEintrag('index.php?module=onlineshops&action=edit&id='.$id,'Details');
      //$this->app->erp->MenuEintrag("index.php?module=shopexport&action=export&id=$id","Export");
      $this->app->erp->MenuEintrag('index.php?module=shopexport&action=artikeluebertragung&id='.$id,'Artikel &Uuml;bertragung');
      if($this->app->DB->Select("SELECT modulename FROM shopexport WHERE id = '$id'") === 'shopimporter_shopware'){
        //Soll nur in Shopware angezeigt werden, da nur in Shopware unterstüzt
        $this->app->erp->MenuEintrag('index.php?module=shopexport&action=adressuebertragung&id='.$id,'Adressen &Uuml;bertragung');
      }
    }


    $typ = $this->app->DB->Select("SELECT typ FROM shopexport WHERE id='$id' LIMIT 1");
    if($typ==='wawision') {
      $this->app->erp->MenuEintrag("index.php?module=shopexport&action=navigationtab&id=$id","Navigation");
      $this->app->erp->MenuEintrag("index.php?module=shopexport&action=artikelgruppen&id=$id","Artikelgruppen");
      $this->app->erp->MenuEintrag("index.php?module=shopexport&action=live&id=$id","Live-Status");
      $this->app->erp->MenuEintrag("index.php?module=inhalt&action=listshop&id=$id","Inhalte / E-Mailvorlagen");
    }

    if($this->app->Secure->GetGET('action') !== 'list'){
      $this->app->erp->MenuEintrag('index.php?module=onlineshops&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
    }
    $this->app->erp->RunMenuHook('onlineshops');
  }

  public function ShopexportDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    if(is_numeric($id)) {
      $this->app->erp->RunHook('shopexportdelete', 1, $id);
      $this->app->DB->Delete("DELETE FROM shopexport WHERE id='$id' LIMIT 1");
    }
    $this->app->Location->execute('index.php?module=onlineshops&action=list');
  }

  /**
   * @return JsonResponse
   */
  public function HandleUncheckTreeNodeAjaxAction()
  {
    $shopId = (int)$this->app->Secure->GetGET('shopId');
    $id = (int)$this->app->Secure->GetGET('id');
    $this->setJsonSettings($shopId, 'category_root_id', 0);
    $data = ['id' => $id,];

    return new JsonResponse($data);
  }

  /**
   * @return JsonResponse
   */
  public function HandleCheckTreeNodeAjaxAction()
  {
    $shopId = (int)$this->app->Secure->GetGET('shopId');
    $id = (int)$this->app->Secure->GetGET('id');
    $this->setJsonSettings($shopId, 'category_root_id', $id);
    $data = ['id' => $id,];

    return new JsonResponse($data);
  }
  /**
   * @return JsonResponse
   */
  public function HandleLoadDefaultTemplateAjaxAction()
  {
    $template = '';
    $shopId = (int)$this->app->Secure->GetPOST('shopid');
    $isJson = $this->app->Secure->GetPOST('format') === 'json';
    $shopModule = $this->app->DB->Select(sprintf('SELECT `modulename` FROM `shopexport` WHERE `id` = %d', $shopId));
    $file = dirname(dirname(__DIR__)).'/classes/Modules/Onlineshop/resources/smarty_templates/'.$shopModule.'_'
      .($isJson?'json':'xml').'.tpl';
    if(is_file($file)) {
      $template = file_get_contents($file);
    }

    return new JsonResponse(
      [
        'success' => true,
        'template' => $template
      ]
    );
  }

  public function ShopexportEdit()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'loadDefaultTemplate') {
      return $this->HandleLoadDefaultTemplateAjaxAction();
    }
    if($cmd === 'loadTree') {
      return $this->HandleLoadTreeAjaxAction();
    }
    if($cmd === 'uncheckTreeNode') {
      return $this->HandleUncheckTreeNodeAjaxAction();
    }
    if($cmd === 'checkTreeNode') {
      return $this->HandleCheckTreeNodeAjaxAction();
    }
    if($cmd === 'loadCart') {
      return $this->HandleLoadCartAjaxAction();
    }
    if($cmd === 'runincomming') {
      return $this->HandleRunSmartyIncommingAjaxAction();
    }
    if($cmd === 'savesmartyincomming') {
      return $this->HandleSaveSmartyIncommingAjaxAction();
    }
    if($cmd === 'getnotimortedorders'){
      $id = (int)$this->app->Secure->GetPOST('id');
      $notImortedOrders = $this->app->DB->Select(sprintf('SELECT COUNT(id) FROM shopimport_auftraege WHERE shopid = %d AND imported = 0 AND trash = 0', $id));
      echo json_encode(['count'=>$notImortedOrders]);
      $this->app->erp->ExitWawi();
    }

    $id = (int)$this->app->Secure->GetGET('id');
    if(!empty($id) && $this->app->Secure->GetPOST('speichern') === 'Speichern'){
      $fieldsToSave = $_POST;
      unset($fieldsToSave['nurfehler'],$fieldsToSave['auftraege'],$fieldsToSave['aenderungen'],$fieldsToSave['shopexport_log_length']);

      $moduleName = $this->app->DB->Select("SELECT modulename FROM shopexport WHERE id = '$id' LIMIT 1");
      try {
        $obj = $this->app->erp->LoadModul($moduleName);
        if(method_exists($obj,'EinstellungenStruktur')){
          $struktur = $obj->EinstellungenStruktur($id);
          foreach ($struktur['felder'] as $fieldname => $fieldData){
            if($fieldData['typ'] === 'password'){
              if($fieldsToSave[$fieldname] === '***************') {
                $oldData = json_decode($this->app->DB->Select('SELECT einstellungen_json FROM shopexport WHERE id=' . $id), true);
                $fieldsToSave[$fieldname] = $oldData['felder'][$fieldname];
              }
              $fieldsToSave[$fieldname] = substr(md5($fieldsToSave[$fieldname]),0,15);
            }
          }
        }
      }catch(Exception $ex){
        $this->app->erp->LogFile('Fehlerhafter Aufruf in Modul: '.$moduleName);
      }

      $username = $this->app->DB->real_escape_string($this->app->User->GetUsername());

      $query = sprintf('SELECT id FROM shopexport_change_log WHERE shop_id=%d',$id);
      $changeLogEntryExists = $this->app->DB->Select($query);

      if($changeLogEntryExists){
        $query = sprintf('SELECT diff FROM shopexport_change_log WHERE shop_id=%d ORDER BY id ASC',$id);
        $existingChangeLogEntries = $this->app->DB->SelectArr($query);
        $fieldsToCompareTo = [];
        foreach ($existingChangeLogEntries as $existingChangeLogEntry){
          $existingFields = json_decode($existingChangeLogEntry['diff'],true);
          $fieldsToCompareTo = array_merge($fieldsToCompareTo,$existingFields);
        }

        $differenceInValues = [];
        foreach ($fieldsToSave as $fieldToSaveName => $fieldToSaveValue){
          if($fieldsToSave[$fieldToSaveName] !== $fieldsToCompareTo[$fieldToSaveName]){
            $differenceInValues[$fieldToSaveName] = $fieldsToSave[$fieldToSaveName];
          }
          unset($fieldsToCompareTo[$fieldToSaveName]);
        }
        foreach ($fieldsToCompareTo as $fieldToCompareToName => $fieldToCompareToValue){
          if(!empty($fieldToCompareToValue)){
            $differenceInValues[$fieldToCompareToName] = '';
          }
        }

        $message = 'Geänderte Felder: '.count($differenceInValues);
        if(empty($differenceInValues)){
          $message = 'Keine Änderung';
        }
        $plaindiff = implode(', ',array_keys($differenceInValues));
        if(strlen($plaindiff)>100){
          $plaindiff = substr($plaindiff,0,97).'...';
        }
        $query = sprintf("INSERT INTO shopexport_change_log (shop_id,diff,message,username,plaindiff) VALUES (%d,'%s','%s','%s','%s')",
          $id, $this->app->DB->real_escape_string(json_encode($differenceInValues)),$message,$username,$plaindiff);
        $this->app->DB->Insert($query);
      }else{
        $query = sprintf("INSERT INTO shopexport_change_log (shop_id,diff,message,username,plaindiff) VALUES (%d,'%s','%s','%s','')",
        $id, $this->app->DB->real_escape_string(json_encode($fieldsToSave)),'Initiale Speicherung',$username);
        $this->app->DB->Insert($query);
      }
    }


    if($id && $cmd === 'archivspeichern')
    {
      //Prüfen ob Cronjob existiert, wenn nicht inaktiv anlegen
      $cronjobid = $this->app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'shopimport_auftragarchiv' LIMIT 1");
      if(!$cronjobid){
        $this->app->DB->Insert("INSERT INTO prozessstarter (bezeichnung, bedingung, art, startzeit, letzteausfuerhung, periode, typ, parameter, aktiv) VALUES ('Shopimport Auftragsarchiv','','periodisch', NOW(), '0000-00-00 00:00:00', 10080,'cronjob', 'shopimport_auftragarchiv', 0)");
        $cronjobid = $this->app->DB->GetInsertID();
      }else {
        $this->app->DB->Update(
          sprintf(
            'UPDATE prozessstarter 
          SET letzteausfuerhung = DATE_SUB(NOW(), INTERVAL 10080 MINUTE) 
          WHERE id = %d AND mutex = 0 AND aktiv = 1 AND letzteausfuerhung > DATE_SUB(NOW(), INTERVAL 10080 MINUTE)',
            $cronjobid
          )
        );
      }
      $this->app->DB->Update("UPDATE prozessstarter SET aktiv = 1 WHERE id = '$cronjobid'");

      $check = $this->app->DB->Select("SELECT id FROM shopexport_archiv WHERE shop = '$id' LIMIT 1");
      if(!$check)
      {
        $this->app->DB->Insert("INSERT INTO shopexport_archiv (shop) VALUES ('$id')");
        $check = $this->app->DB->GetInsertID();
      }
      if($check)
      {
        $this->app->DB->Update("UPDATE shopexport_archiv SET status = 'aktiv' WHERE id = '$check' LIMIT 1");
        $typ = $this->app->Secure->GetPOST('typ');
        $von = $this->app->Secure->GetPOST('von');
        $bis = $this->app->Secure->GetPOST('bis');
        $zeitvon = $this->app->Secure->GetPOST('zeitvon');
        $zeitbis = $this->app->Secure->GetPOST('zeitbis');
        $abschliessen = (int)$this->app->Secure->GetPOST('auftrag_abschliessen');
        $rechnung_erzeugen = (int)$this->app->Secure->GetPOST('rechnung_erzeugen');
        $rechnung_bezahlt = (int)$this->app->Secure->GetPOST('rechnung_bezahlt');
        $stornierteabholen = (int)$this->app->Secure->GetPOST('stornierte_abholen');
        $donotimport = (int)$this->app->Secure->GetPOST('donotimport');
        $datumvon = '';
        $datumbis = '';
        $nummervon = '';
        $nummerbis = '';
        if($typ === 'zeitraum')
        {
          if($von != '')
          {
            $datumvon = $this->app->String->Convert($von, '%1.%2.%3', '%3-%2-%1').' '.$zeitvon;
          }
          if($bis != '')
          {
            $datumbis = $this->app->String->Convert($bis, '%1.%2.%3', '%3-%2-%1').' '.$zeitbis;
          }
        }else{
          $nummervon = $von;
          $nummerbis = $bis;
        }
        if(($nummervon !='' && $nummerbis !='') || ($datumvon !='' && $datumbis !='')){
          $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
          $this->app->DB->Update("UPDATE shopexport_archiv 
            SET anzahl=0, erfolgreich=0,`type` = '$typ',bearbeiter = '$bearbeiter',abschliessen = '$abschliessen', 
                rechnung_erzeugen = '$rechnung_erzeugen',rechnung_bezahlt = '$rechnung_bezahlt', 
                nummervon = '$nummervon', nummerbis = '$nummerbis',datumvon = '$datumvon', 
                datumbis = '$datumbis', stornierteabholen='$stornierteabholen' ,
                donotimport = '$donotimport', letzteabgeholtenummer = 0
            WHERE id = '$check' LIMIT 1");
          $this->app->DB->LogIfError();
          echo json_encode(array('status'=>1));
        }
        else {
          echo json_encode(array('status'=>0));
        }
      }
      $this->app->ExitXentral();
    }
    if($cmd === 'changeaktiv') {
      $data = $this->app->DB->SelectRow("SELECT * FROM shopexport WHERE id = '$id' LIMIT 1");
      if($data) {
        if($data['aktiv']) {
          $this->app->DB->Update("UPDATE shopexport SET aktiv = 0 WHERE id = '$id' LIMIT 1");
        }
        else {
          if($data['shoptyp'] === 'custom' && $data['modulename'] !== '')
          {
            $data['modulename'] = trim($data['modulename'],'.');
            $file = dirname(__DIR__) .'/plugins/external/shopimporter/'.$data['modulename'];
            if(is_file($file)) {
              include_once $file;
            }
            else {
              echo json_encode(array('aktiv'=> $data['aktiv']));
              $this->app->ExitXentral();
            }
          }
          else {
            $this->app->DB->Update("UPDATE shopexport SET aktiv = 1 WHERE id = '$id' LIMIT 1");
          }
        }
        echo json_encode(array('aktiv'=> 1 - $data['aktiv']));
      }
      $this->app->ExitXentral();
    }
    if($cmd === 'testcustomfile') {
      $status = 0;
      $fehler = '';
      if($data = $this->app->DB->SelectRow("SELECT * FROM shopexport WHERE id = '$id' AND shoptyp = 'custom' AND modulename <> '' LIMIT 1")) {
        $data['modulename'] = trim($data['modulename'],'.');
        $file = dirname(__DIR__) .'/plugins/external/shopimporter/'.$data['modulename'];
        
        if(is_file($file)) {
          include_once $file;
          $status = 1;
        }
        else {
          $fehler = 'Datei nicht gefunden';
        }
      }
      echo json_encode(array('status'=>$status,'fehler'=>$fehler));
      $this->app->ExitXentral();
    }
    if($this->app->Secure->GetPOST('savefile')) {
      if($data = $this->app->DB->SelectRow("SELECT * FROM shopexport WHERE id = '$id' AND shoptyp = 'custom' AND modulename <> '' LIMIT 1")) {
        $file = dirname(__DIR__) .'/plugins/external/shopimporter/'.$data['modulename'];
        
        if(is_file($file)) {
          $text = htmlspecialchars_decode($_POST['customdatei']);
          file_put_contents($file, $text);
        }
      }
    }

    if($this->app->Secure->GetPOST('pruefen')) {
      
      $className = 'Remote';
      $methodName = 'RemoteConnection';
      $r = new ReflectionMethod($className, $methodName);
      $params = $r->getParameters();
      $anzargs = count($params);
      try {
        if($anzargs > 1){
          $pageContents = $this->app->remote->RemoteConnection($id, true);
        }
        else {
          $pageContents = $this->app->remote->RemoteConnection($id);
        }
      }
      catch(Exception $e) {
        $pageContents = $e->getMessage();
      }
      if(strpos($pageContents, 'success') === 0){
        if($anzargs > 1) {
          $pageContents2 = $this->app->remote->RemoteConnection($id, 'info');
        }
        
        if($anzargs > 1 && is_array($pageContents2)) {
          $json = $this->app->DB->Select("SELECT json FROM shopexport WHERE id = '$id' LIMIT 1");
          if($json) {
            $json = array_merge(json_decode($json, true), $pageContents2);
          }
          else {
            $json = $pageContents2;
          }
          $this->app->DB->Update("UPDATE shopexport set json = '".$this->app->DB->real_escape_string(json_encode($json))."' WHERE id = '$id' LIMIT 1");
          unset($json);
          $this->app->Tpl->Set('MESSAGE','<div class="info">');
          if(isset($pageContents2['subshops'])) {
            $this->app->Tpl->Add('MESSAGE','Subshops: 
            <table><tr><td>Id</td><td>Name</td><td>Aktiv</td></tr>
            ');
            foreach($pageContents2['subshops'] as $subshop) {
              $this->app->Tpl->Add('MESSAGE','<tr><td>'.$subshop['id'].'</td><td>'.$subshop['name']."</td><td>".($subshop['aktiv']?'ja':'nein')."</td></tr>");
            }
            $this->app->Tpl->Add('MESSAGE','</table>');
          }
          else {
            $this->app->Tpl->Add('MESSAGE','Verbindung: success');
          }
          $this->app->Tpl->Add('MESSAGE','</div>');
        }
        else {
          $this->app->Tpl->Set('MESSAGE',"<div class=\"info\">Verbindung: $pageContents</div>");
        }
      }
      else {
        $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Verbindungsproblem: Eventuell falsche Schl&uuml;ssel! ($pageContents)</div>");
      }
      
    }
    
    if($this->app->Secure->GetPOST('auftragabholen')) {
      /** @var Shopimport $obj */
      $obj = $this->app->erp->LoadModul('shopimport');
      if(!empty($obj)) {
        @ignore_user_abort(true);
        $anz = $obj->ShopimportImport($id, 0, true);
      }
      if($anz) {
        $this->app->Location->execute('index.php?module=shopimport&action=import');
      }
      if($obj && !empty($obj->error)) {
        $this->app->Tpl->Add('MESSAGE', $obj->error);
      }
      else{
        $this->app->Tpl->Add('MESSAGE', '<div class="error2">Aktuell sind keine Auftr&auml;ge in den Online-Shops vorhanden!</div>');
      }
    }
    
    $this->app->YUI->TableSearch('LOGTABELLE', 'shopexport_log', 'show','','',basename(__FILE__), __CLASS__);
    
    $this->app->YUI->TableSearch('TAB3','shopexport_zahlweisen');
    $this->app->YUI->TableSearch('TAB4','shopexport_versandarten');
    $this->app->YUI->TableSearch('TAB5','shopexport_freifelder');
    $this->app->YUI->TableSearch('TAB6','shopexport_subshop');
    $this->app->YUI->TableSearch('TAB7', 'shopexport_sprachen', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->YUI->TableSearch('TAB8', 'shopexport_kundengruppen', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->YUI->AutoComplete('b_projekt','projektname',1);
    $this->app->YUI->AutoComplete('sprachenprojekt', 'projektname' , 1);
    $this->app->YUI->AutoComplete('kundengruppeprojekt', 'projektname' , 1);
    $this->app->YUI->AutoComplete('k_projekt', 'projektname' , 1);
    $this->app->YUI->AutoComplete('d_projekt', 'projektname' , 1);
    
    $this->app->Tpl->Add('FREIFELDEROPTIONEN','<option value=""></option>');
    for($i = 1; $i <= 40; $i++)  {
      $freifeldname = $this->app->erp->Firmendaten('freifeld'.$i);
      if(!$freifeldname) {
        $freifeldname = 'Freifeld '.$i;
      }
      $freifeldname = explode('|',$freifeldname);
      $this->app->Tpl->Add('FREIFELDEROPTIONEN','<option value="freifeld'.$i.'">'.$freifeldname[0].'</option>');
    }

/*
    $this->CheckColumn("id","int(11)","shopexport_log","NOT NULL AUTO_INCREMENT");
    $this->CheckColumn("shopid","int(11)","shopexport_log","DEFAULT '0' NOT NULL");
    $this->CheckColumn("typ", "varchar(64)", "shopexport_log","DEFAULT '' NOT NULL");
    $this->CheckColumn("parameter1", "varchar(64)", "shopexport_log","DEFAULT '' NOT NULL");
    $this->CheckColumn("parameter2", "varchar(64)", "shopexport_log","DEFAULT '' NOT NULL");
    $this->CheckColumn("bearbeiter", "varchar(64)", "shopexport_log","DEFAULT '' NOT NULL");
    $this->CheckColumn("zeitstempel","timestamp","shopexport_log","DEFAULT CURRENT_TIMESTAMP");
*/
    $sprachenopt = $this->app->erp->GetSprachenSelect();
    $sprachensel = '';
    if($sprachenopt) {
      foreach($sprachenopt as $k => $v)  {
        $sprachensel .= '<option value="'.$k.'">{|'.$v.'|}</option>';
      }
    }

    $projektId = $this->app->DB->Select("SELECT projekt FROM shopexport WHERE id = '$id' LIMIT 1");
    $selversandarten = '<option value=""></option>';
    $selzahlungsweisen = '<option value=""></option>';
    $zahlungsweisen = $this->app->erp->GetZahlungsweise(null,null,$projektId);
    if(!empty($zahlungsweisen)){
      foreach ($zahlungsweisen as $k => $v) {
        $selzahlungsweisen .= '<option value="' . $k . '">' . $v . '</option>';
      }
    }

    $versandartenarr = $this->app->DB->SelectArr("SELECT type,bezeichnung FROM versandarten WHERE aktiv = 1 AND (projekt=0 OR projekt='' OR projekt='$projektId')ORDER BY bezeichnung");
    if(!empty($versandartenarr)) {
      $versandarten = [];
      foreach($versandartenarr as $v) {
        $versandarten[$v['type']] = $v['bezeichnung'];
      }

      foreach ($versandarten as $k => $v) {
        $selversandarten .= '<option value="' . $k . '">' . $v . '</option>';
      }
    }

    $this->app->Tpl->Set('FORMULAR','<div id="editZahlweisen" style="display:none;" title="Bearbeiten">
        <input type="hidden" id="b_id">
        <table>
        <tr>
        <td>{|Zahlweise Shop|}:</td>
        <td><input type="text" id="b_zahlweise_shop"></td>
        </tr>

        <tr>
        <td>{|Zahlweise Xentral|}:</td>
        <td><select id="b_zahlweise_wawision">'.$selzahlungsweisen.'</select></td>
        </tr>
        <tr>
        <td>{|Vorab als bezahlt markieren|}:</td>
        <td><input type="checkbox" value="1" id="b_vorabbezahltmarkieren"></td>
        </tr>
        <tr>
        <td>{|Autoversand aktiv|}:</td>
        <td><input type="checkbox" checked="checked" value="1" id="b_autoversand"></td>
        </tr>
        <tr>
        <td>{|keine Rechnung erstellen|}:</td>
        <td><input type="checkbox" value="1" id="b_keinerechnung"></td>
        </tr>
        <tr>
        <td>{|Fast-Lane|}:</td>
        <td><input type="checkbox" value="1" id="b_fastlane"></td>
        </tr>
        <tr>
        <td>{|Aktiv|}:</td>
        <td><input type="checkbox" value="1" id="b_aktiv"></td>
        </tr>
        </table>
        </div>');

    $this->app->Tpl->Add('FORMULAR','<div id="editVersandarten" style="display:none;" title="Bearbeiten">
        <input type="hidden" id="b_id2">
        <table>
        <tr>
        <td>{|Versandart Shop|}:</td>
        <td><input type="text" id="b_versandart_shop"></td>
        </tr>

        <tr>
        <td>{|Versandart Xentral|}:</td>
        <td><select id="b_versandart_wawision">'.$selversandarten.'</select></td>
        </tr>
        <tr>
        <tr>
        <td>{|Versandart Ausgehend|}:</td>
        <td><input type="text" id="b_versandart_ausgehend"></td>
        </tr>
        <tr>
        <td>{|Versandprodukt Ausgehend|}:</td>
        <td><input type="text" id="b_produkt_ausgehend"></td>
        </tr>
        <tr>
        <td>{|Land (mit Komma getrennt 2-stellig ISO)|}:</td>
        <td><input type="text" id="b_land"></td>
        </tr>
        <tr>
        <td>{|Fast-Lane|}:</td>
        <td><input type="checkbox" value="1" id="b_fastlane2"></td>
        </tr>
        <tr>
        <td>{|Autoversand aktiv|}:</td>
        <td><input type="checkbox" checked="checked" value="1" id="b_autoversand2"></td>
        </tr>
        <tr>
            <td>{|Aktiv|}:</td>
                           <td><input type="checkbox" value="1" id="b_aktiv2"></td>
                                                     </tr>
                                                     </table>
                                                     </div>');


    $alleKundengruppenAusXentral = $this->app->DB->SelectArr("SELECT id,name FROM gruppen WHERE (art='gruppe' OR art='preisgruppe') AND (projekt='' OR projekt=0 OR projekt='$projektId') AND aktiv='1'");
    $kundengruppenSelect ='';
    foreach ($alleKundengruppenAusXentral as $kundengruppe) {
      $kundengruppenSelect .= '<option value="'.$kundengruppe['id'].'">'.$kundengruppe['name'].'</option>';
    }
    $this->app->Tpl->Add('FORMULAR','<div id="editKundengruppen" style="display:none;" title="Bearbeiten">
        <input type="hidden" id="k_id">
        <input type="hidden" id="k_shop">
        <table>
        <tr>
        <td>{|Gruppe Xentral|}:</td>
        <td><select id="k_kundengruppe">'.$kundengruppenSelect.'</select></td>
        </tr>

        <tr>
        <td>{|Gruppe im Shop|}:</td>
        <td><input type="text" id="k_extbezeichnung"></td>
        </tr>
        
        <tr>
        <td>{|Projekt|}:</td>
        <td><input type="text" id="k_projekt"></td>
        </tr>

        <tr>
        <td>{|Neukunden zuweisen|}:</td>
        <td><input type="checkbox" value="1" id="k_neukundengruppezuweisen"></td>
        </tr>

        <tr>
        <td>{|Rolle|}:</td>
        <td><select id="k_rolle"><option value="Kunde">Kunde</option><option value="Mitglied">Mitglied</option><option value="Artikel">Artikel</option></select></td>
        </tr>

        <tr>
        <td>{|Aktiv|}:</td>
        <td><input type="checkbox" value="1" id="k_aktiv"></td>
        </tr>
        </table>
        </div>');

    $this->app->Tpl->Add('FORMULAR','<div id="editFreifelder" style="display:none;" title="Bearbeiten">
        <input type="hidden" id="b_id3">
        <table>
        <tr>
        <td>{|Freifeld Xentral|}:</td>
        <td><select id="b_freifeld_wawi">
        [FREIFELDEROPTIONEN]
        </select></td>
        </tr>

        <tr>
        <td>{|Bezeichnung in Shop|}:</td>
        <td><input type="text" id="b_freifeld_shop"></td>
        </tr>
        <tr>
        <td>{|Aktiv|}:</td>
        <td><input type="checkbox" value="1" id="b_aktiv3"></td>
        </tr>
        </table>
        </div>');


      $this->app->Tpl->Add('FORMULAR','<div id="editSubshop" style="display:none;" title="Bearbeiten">
        <input type="hidden" id="b_id4">
        <table>
        <tr>
        <td>{|Subshop Kennung|}:</td>
        <td><input type="text" id="b_subshopkennung"></td>
        </tr>

        <tr>
        <td>{|Projekt Xentral|}:</td>
        <td><input type="text" name="b_projekt" id="b_projekt"></td>
        </tr>
        <tr>
        <td>{|Sprache|}:</td>
        <td><select id="b_sprache"><option value=""></option>'.$sprachensel.'</select></td>
        </tr>
        <tr>
            <td>{|Aktiv|}:</td>
                           <td><input type="checkbox" value="1" id="b_aktiv4"></td>
                                                     </tr>
                                                     </table>
                                                     </div>');
      $this->app->Tpl->Add('FORMULAR','<div id="editSprachen" style="display:none;" title="Bearbeiten">
        <input type="hidden" id="d_id">
        <table>
        <tr>
        <td>{|Land|}:</td>
        <td><input type="text" id="d_land"></td>
        </tr>

        <tr>
        <td>{|Projekt Xentral|}:</td>
        <td><input type="text" name="d_projekt" id="d_projekt"></td>
        </tr>
        <tr>
        <td>{|Sprache|}:</td>
        <td><select id="d_sprache">'.$sprachensel.'</select></td>
        </tr>
        <tr>
            <td>{|Aktiv|}:</td>
                           <td><input type="checkbox" value="1" id="d_aktiv"></td>
                                                     </tr>
                                                     </table>
                                                     </div>');

    $this->app->Tpl->Set('NEUTAB3','
        <fieldset>
        <legend>{|Anlegen|}</legend>
        <form action="" onsubmit="return zahlweiseSave(this);">
        <input type="hidden" name="shop" id="shop" value="'.$id.'">
        <table width="" cellspacing="0" cellpadding="0">
        <tr>
        <td>{|Zahlweise Shop|}:&nbsp;</td>
        <td><input type="text" name="zahlweise_shop" id="zahlweise_shop"></td>
        <td>&nbsp;</td>
        <td>{|Zahlweise Xentral|}:&nbsp;</td>
        <td><select name="zahlweise_wawision" id="zahlweise_wawision">'.$selzahlungsweisen.'</select></td>
        <td>&nbsp;</td>
        <td>{|Vorab als bezahlt markieren|}:&nbsp;</td>
        <td><input type="checkbox" value="1" name="vorabbezahltmarkieren" id="vorabbezahltmarkieren"></td>
        <td>&nbsp;</td>
        <td>{|Autoversand aktiv|}:&nbsp;</td>
        <td><input type="checkbox" value="1" checked="checked" name="autoversand" id="autoversand">&nbsp;<i>(Haken muss standardmässig aktiviert sein)</i></td>
        <td>&nbsp;</td>
        <td>{|keine Rechnung erstellen|}:&nbsp;</td>
        <td><input type="checkbox" value="1" name="keinerechnung" id="keinerechnung"></td>
        <td>&nbsp;</td>
        <td>{|Fast-Lane|}:&nbsp;</td>
        <td><input type="checkbox" value="1" name="fastlane" id="fastlane"></td>
        <td>&nbsp;</td>
        <td><input type="submit" name="" value="{|Speichern|}"></td>
        </tr>
        </table>
        </form>
        </fieldset>');


    $this->app->Tpl->Set('NEUTAB4','
        <fieldset>
        <legend>{|Anlegen|}</legend>
        <form action="" onsubmit="return versandartSave(this);">
        <input type="hidden" name="shop" id="shop" value="'.$id.'">
        <table width="" cellspacing="0" cellpadding="0">
        <tr>
        <td width="105">{|Versandart Shop|}:&nbsp;</td>
        <td width="180"><input type="text" name="versandart_shop" id="versandart_shop"></td>
        <td>&nbsp;</td>
        <td width="130">{|Versandart Xentral|}:&nbsp;</td>
        <td width="180"><select name="versandart_wawision" id="versandart_wawision">'.$selversandarten.'</select></td>
        <td>&nbsp;</td>
        <td width="130">{|Versandart Ausgehend|}:&nbsp;</td>
        <td width="180"><input type="text" name="versandart_ausgehend" id="versandart_ausgehend"</td>
        <td>&nbsp;</td>
        <td width="130">{|Produkt Ausgehend|}:&nbsp;</td>
        <td width="180"><input type="text" name="produkt_ausgehend" id="produkt_ausgehend"</td>
        <td>&nbsp;</td>
        <td width="230">{|Land (2-stellig ISO mit Komma getrennt)|}:&nbsp;</td>
        <td width="60"><input type="text" name="land" id="land" size="3"></td>
        <td>&nbsp;</td>
        <td width="110">{|Autoversand aktiv|}:&nbsp;</td>
        <td width="300"><input type="checkbox" value="1" checked="checked" name="autoversand" id="autoversand">&nbsp;<i>(Haken muss standardmässig aktiviert sein)</i></td>
        <td>&nbsp;</td>
        <td width="110">{|Fast-Lane|}:&nbsp;</td>
        <td width="300"><input type="checkbox" value="1" name="fastlane" id="fastlane"></td>
        <td>&nbsp;</td>
        <td><input type="submit" name="" value="{|Speichern|}"></td>
        </tr>
        </table>
        </form>
        </fieldset>');

    $this->app->Tpl->Set('NEUTAB5','
        <fieldset>
        <legend>{|Anlegen|}</legend>
        <form action="" onsubmit="return freifelderSave(this);">
        <input type="hidden" name="shop" id="shop" value="'.$id.'">
        <table width="" cellspacing="0" cellpadding="0">
        <tr>
        <td>{|Freifeld Xentral|}:&nbsp;</td>
        <td><select name="freifeld_wawi" id="freifeld_wawi">[FREIFELDEROPTIONEN]</select></td>
        <td>&nbsp;</td>
        <td>{|Bezeichnung in Shop|}:&nbsp;</td>
        <td><input type="text" name="freifeld_shop" id="freifeld_shop"></td>
        <td>&nbsp;</td>
        <td><input type="submit" name="" value="{|Speichern|}"></td>
        </tr>
        </table>
        </form>
        </fieldset>');

    $this->app->Tpl->Set('NEUTAB6','
        <fieldset>
        <legend>{|Anlegen|}</legend>
        <form action="" onsubmit="return subshopSave(this);">
        <input type="hidden" name="shop" id="shop" value="'.$id.'">
        <table width="" cellspacing="0" cellpadding="0">
        <tr>
        <td>{|Subshop Kennung|}:&nbsp;</td>
        <td><input type="text" name="subshopkennung" id="subshopkennung"></td>
        <td>&nbsp;</td>
        <td>{|Sprache|}:&nbsp;</td>
        <td><select name="sprache" id="sprache"><option value=""></option>'.$sprachensel.'</select></td>
        <td>&nbsp;</td>
        <td>{|Projekt Xentral|}:&nbsp;</td>
        <td><input type="text" name="projekt" id="projekt"></td>
        <td>&nbsp;</td>
        <td><input type="submit" name="" value="{|Speichern|}"></td>
        </tr>
        </table>
        </form>
        </fieldset>');
        
    $this->app->Tpl->Set('NEUTAB7','
        <fieldset>
        <legend>{|Anlegen|}</legend>
        <form action="" onsubmit="return sprachenSave(this);">
        <input type="hidden" name="shop" id="spracheshop" value="'.$id.'">
        <table width="" cellspacing="0" cellpadding="0">
        <tr>
        <td>{|Land|}:&nbsp;</td>
        <td><input type="text" name="land" id="sprachenland"></td>
        <td>&nbsp;</td>
        <td>{|Sprache|}:&nbsp;</td>
        <td><select name="sprache" id="sprachensprache">'.$sprachensel.'</select></td>
        <td>&nbsp;</td>
        <td>{|Projekt Xentral|}:&nbsp;</td>
        <td><input type="text" name="projekt" id="sprachenprojekt"></td>
        <td>&nbsp;</td>
        <td><input type="submit" name="" value="{|Speichern|}"></td>
        </tr>
        <tr>
        <td></td>
        <td colspan="4"><small><i>Bitte als ISO2-L&auml;nderk&uuml;rzel eintragen</i></small></td>
        </tr>
        </table>
        </form>
        </fieldset>');

    $this->app->Tpl->Set('NEUTAB8','
        <fieldset>
        <legend>{|Anlegen|}</legend>
        <form action="" onsubmit="return kundengruppenSave(this);">
        <input type="hidden" name="shop" id="kundengruppenshopid" value="'.$id.'">
        <table width="" cellspacing="0" cellpadding="0">
        <tr>
        <td><label for="zuweisungrolle">{|Rolle|}:&nbsp;</label></td>
        <td><select name="zuweisungrolle" id="zuweisungrolle"><option value="Kunde">Kunde</option><option value="Mitglied">Mitglied</option><option value="Artikel">Artikel</option></select></td>
        <td>&nbsp;</td>
        <td>{|von|}:&nbsp;</td>
        <td><select name="kundengruppexentral" id="kundengruppexentral">'.$kundengruppenSelect.'</select></td>
        <td>&nbsp;</td>
        <td>{|Projekt Xentral|}:&nbsp;</td>
        <td><input type="text" name="projekt" id="kundengruppeprojekt"></td>
        <td>&nbsp;</td>
        <td>{|Gruppe im Shop|}:&nbsp;</td>
        <td><input type="text" name="kundengruppeshop" id="kundengruppeshop"></td>
        <td>&nbsp;</td>
        <td><label for="kundengruppeneukundenzuweisen">{|Neukunden automatisch zuweisen|}:&nbsp;</label></td>
        <td><input type="checkbox" value="1" id="kundengruppeneukundenzuweisen" name="kundengruppeneukundenzuweisen"></td>
        <td>&nbsp;</td>
        <td><input type="submit" name="" value="{|Speichern|}"></td>
        </tr>
        </table>
        </form>
        </fieldset>');

    if($this->app->DB->Select("SELECT id FROM shopexport WHERE aktiv = 1 AND cronjobaktiv AND id = '$id' LIMIT 1"))
    {
      if(!$this->app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'shopimport' AND aktiv = 1 AND art = 'periodisch' LIMIT 1"))
      {
        if($this->app->DB->Select("SELECT id FROM prozessstarter WHERE parameter = 'shopimport' AND aktiv = 1 AND art <> 'periodisch' LIMIT 1"))
        {
          $this->app->Tpl->Add('MESSAGE', '<div class="error">{|Der Prozessstarter &quot;shopimport&quot; ist auf &quot;Uhrzeit&quot; eingestellt. Es werden die Einstellungen &quot;periodisch&quot; mit der Periode 5 empfohlen|}</div>');
        }
      }
    }
    $this->ShopexportMenu();
    if($id > 0) {
      $this->showBetaMessage($id);
    }
    parent::ShopexportEdit();
  }

  /**
   * @param int $shopId
   */
  public function showBetaMessage($shopId)
  {
    if(empty($shopId)) {
      return;
    }
    $module = $this->app->DB->SelectRow(
      sprintf(
        "SELECT `modulename`, `bezeichnung`
        FROM `shopexport` 
        WHERE `id` = %d AND `shoptyp` = 'intern'
        LIMIT 1",
        $shopId
      )
    );
    if(empty($module)) {
      return;
    }
    /** @var Appstore $appstore */
    $appstore = $this->app->erp->LoadModul('appstore');
    if($appstore === null || !method_exists($appstore, 'isBeta')) {
      return;
    }
    if($appstore->isBeta($module['modulename'])) {
      $this->app->Tpl->Add(
        'MESSAGE',
        '<div class="info">Dieses Modul ist noch im Beta Stadium.</div>'
      );
      $appstore->addBetaToHeadline();
    }
  }

  /**
   * @param $json
   *
   * @return mixed
   */
  public function SmartyJsonDecode($json)
  {
    return is_string($json)?json_decode($json):$json;
  }

  /**
   * @param mixed $data
   *
   * @return false|string
   */
  public function SmartyJsonEncode($data) {
    return json_encode($data);
  }

  /**
   * @param $var
   *
   * @return string
   */
  public function SmartyPrintR($var)
  {
    return print_r($var, true);
  }

  /**
   * @param array $array
   *
   * @return string
   */
  public function SmartyArrayToList($array)
  {
    if(is_string($array)) {
      return $array;
    }
    if(!is_array($array)) {
      $array = json_decode(json_encode($array), true);
    }
    if(is_string($array)) {
      return $array;
    }
    if(!is_array($array)) {
      return '';
    }

    foreach($array as $key => $val) {
      if(is_array($val)) {
        $array[$key] = json_encode($val);
      }
    }
    return implode('<br />', $array);
  }

  /**
   * @param array|SimpleXMLElement $attributes
   *
   * @return string
   */
  protected function attributeKey($attributes) {
    $ret = '';
    if(empty($attributes)) {
      return $ret;
    }
    foreach($attributes as $key => $attribute) {
      if((is_array($attribute) || is_object($attribute)) && count($attribute) === 1) {
        $ret .= ' '.$key.'="'.reset($attribute).'"';
        continue;
      }
      $ret .= ' '.$key.'="'.$attribute.'"';
    }
    return $ret;
  }

  /**
   * Kovertiert einen XML-String in ein Array
   *
   * @param string $xml
   * @param bool   $wrap
   *
   * @return array
   *
   * @throws \RuntimeException
   */
  public function convertXmlStringToArray($xml)
  {
    $namespaces = [];
    $simplexml = simplexml_load_string($xml, null, LIBXML_NOCDATA);
    if(is_object($simplexml)) {
      $namespaces = $simplexml->getNamespaces();
    }
    if ($simplexml === false) {
      return [];
    }

    return $this->convertSimpleXmlToArray($simplexml, $namespaces);
  }

  /**
   * @param SimpleXMLElement $object
   * @param array|null       $namespaces
   * @param bool             $removeItemKey
   *
   * @return array|string
   */
  public function convertSimpleXmlToArray($object, $namespaces = null, $removeItemKey = true)
  {
    $array = [];
    $isObject = is_object($object);
    $cobject = $isObject?count($object):0;
    if($isObject && $cobject === 0) {
      $name = $object->getName();
      $attributes = $object->attributes();
      $attributeKey = $this->attributeKey($attributes);
      $array[$name.$attributeKey] = (string)$object;

      return $array;
    }
    $arr = (array)$object;
    if(isset($arr['@attributes'])) {
      unset($arr['@attributes']);
    }
    $keys = array_keys($arr);
    $count =  count($keys);
    if($isObject && !empty($arr)) {
      foreach($object as $key => $value) {
        if($key === '@attributes') {
          continue;
        }
        if($key === 0 && $count === 1) {
          return $value;
        }
        $valueArr = (array)$value;
        if(isset($valueArr['@attributes'])) {
          unset($valueArr['@attributes']);
        }
        if(is_object($value) && !empty($valueArr)) {
          $cValue = count($value);
          $cValueArr = count($valueArr);
          $attributes = $value->attributes();
          $attributeKey = $this->attributeKey($attributes);
          if(isset($array[$key.$attributeKey])) {
            if(!isset($array[$key.$attributeKey][0])) {
              $array[$key.$attributeKey] = [$array[$key.$attributeKey]];
            }
            if($cValue === 0 || ($cValue  <= 1 && $cValueArr === 1)) {
              $valueReset = reset($valueArr);
              if(is_array($valueArr)) {
                $keys = array_keys($valueArr);
                if(reset($keys) === 0) {
                  $array[$key.$attributeKey] = $valueReset;
                  continue;
                }
              }
              /*
              if(!is_object($valueReset) && !is_array($valueReset)) {
                $array[$key.$attributeKey][] = $valueReset;
                continue;
              }*/
            }

            $array[$key.$attributeKey][] = $this->convertSimpleXmlToArray($value, $namespaces, $removeItemKey);
            continue;
          }
          if($cValue === 0 || ($cValue <= 1 && $cValueArr === 1)) {
            $valueReset = reset($valueArr);
            if(is_array($valueArr)) {
              $keys = array_keys($valueArr);
              if(reset($keys) === 0) {
                $array[$key.$attributeKey] = $valueReset;
                continue;
              }
            }
            /*
            if (!is_object($valueReset) && !is_array($valueReset)) {
              $array[$key.$attributeKey] = $valueReset;
              continue;
            }*/
          }

          if($removeItemKey && $attributeKey === '' && strpos($key,'item') === 0 && is_numeric(substr($key,4))) {
            $array[(int)substr($key,4)] = $this->convertSimpleXmlToArray($value, $namespaces, $removeItemKey);
          }
          else{
            $array[$key . $attributeKey] = $this->convertSimpleXmlToArray($value, $namespaces, $removeItemKey);
          }
        }
        else {
          $array[$key] = (string)$value;
        }
      }
      return $array;
    }

    return (string)$object;
  }


  /**
   * @param array $cartArr
   *
   * @return array|null
   */
  public function getArrayFromCart($cartArr)
  {
    if(empty($cartArr)) {
      return null;
    }
    $jsonEncoded = !empty($cartArr['jsonencoded']);
    $cart = base64_decode($cartArr['warenkorb']);
    if($jsonEncoded){
      $cart = json_decode($cart, true);
    }else {
      $cart = unserialize($cart);
    }
    if(!is_array($cart)) {
      return [];
    }

    return $cart;
  }

  /**
   * @param int   $categoryId
   * @param array $categories
   *
   * @return bool
   */
  public function isIdInCategories($categoryId, $categories)
  {
    if(empty($categories)) {
      return false;
    }
    foreach($categories as $category) {
      if((int)$category['id'] === (int)$categoryId) {
        return true;
      }
    }

    return false;
  }

  /**
   * @param int    $shopId
   * @param string $name
   *
   * @return mixed|null
   */
  public function getJsonSettings($shopId, $name)
  {
    $json = $this->app->DB->Select(sprintf('SELECT `einstellungen_json` FROM `shopexport` WHERE `id` = %d', $shopId));
    if(empty($json)) {
      return null;
    }

    $json = json_decode($json, true);
    if(empty($json['felder'])) {
      return null;
    }
    if(!isset($json['felder'][$name])) {
      return null;
    }
    return $json['felder'][$name];
  }

  /**
   * @param int    $shopId
   * @param string $name
   * @param mixed  $value
   */
  public function setJsonSettings($shopId, $name, $value)
  {
    $json = $this->app->DB->Select(sprintf('SELECT `einstellungen_json` FROM `shopexport` WHERE `id` = %d', $shopId));
    if(!empty($json)){
      $json = json_decode($json, true);
    }
    if(!is_array($json)) {
      $json = ['felder' => []];
    }
    if(!isset($json['felder']) || !is_array($json['felder'])) {
      $json['felder'] = [];
    }
    $json['felder'][$name] = $value;
    $json = json_encode($json);
    $this->app->DB->Update(
      sprintf(
        "UPDATE `shopexport` SET `einstellungen_json` = '%s' WHERE `id` = %d",
        $this->app->DB->real_escape_string($json), $shopId
      )
    );
  }

  /**
   * @return JsonResponse
   */
  public function HandleLoadTreeAjaxAction()
  {
    $shopId = (int)$this->app->Secure->GetGET('id');
    $checkedId = (int)$this->getJsonSettings($shopId, 'category_root_id');
    /** @var Artikelbaum $treeObject */
    $treeObject = $this->app->loadModule('artikelbaum');
    $categories = [['id'=> 0, 'bezeichnung'=>'root', 'parent' => 0,],];
    $treeObject->getKategorien($categories, 0);
    if(count($categories) > 1) {
      if((string)$categories[count($categories) -1]['id'] === '0'){
        unset($categories[count($categories) - 1]);
      }
    }
    if($checkedId > 0 && !$this->isIdInCategories($checkedId, $categories)) {
      $checkedId = 0;
    }

    $data = $treeObject->getTreeData(
      $categories,
      [
        'checkbox' => true,
        'checked_ids' => [
          $checkedId
        ],
      ]
    );

    return new JsonResponse($data);
  }

  /**
   * @return JsonResponse
   */
  public function HandleLoadCartAjaxAction()
  {
    $shopId = (int)$this->app->Secure->GetPOST('shopid');
    $extid = $this->app->Secure->GetPOST('extid');
    $useJson = $this->app->Secure->GetPOST('format') === 'json';
    $merge = !(bool)(int)$this->app->Secure->GetPOST('replacecart');
    $content = empty($this->app->Secure->POST['content'])?'':trim($this->app->Secure->POST['content']);

    $cart = $this->app->DB->SelectRow(
      sprintf(
        "SELECT `jsonencoded`, `warenkorb` 
        FROM `shopimport_auftraege` 
        WHERE `shopid` = %d AND `extid` = '%s' 
        ORDER BY `id` DESC 
        LIMIT 1",
        $shopId, $extid
      )
    );
    if(empty($cart)) {
      return new JsonResponse(['success' => false, 'error' => 'Warenkorb nicht gefunden']);
    }

    $cart = $this->getArrayFromCart($cart);
    $cart2 = $cart;
    if($useJson) {
      $xml_data = null;
    }
    else {
      $xml_data = $this->convertArrayToSimpleXml($cart);
      $xml_data = $this->formatXmlForOutput($xml_data->asXML());
    }

    if($content === '') {
      return new JsonResponse(
        [
          'success' => true,
          'input'   => !$useJson?$xml_data:json_encode($cart),
          'object'  => '<pre>'.print_r($cart,true).'</pre>',
          'preview' => '',
        ]
      );
    }

    try {
      $cart2 = $this->addCartInfo($cart2, $shopId, $useJson, $merge, $content);
    }
    catch(Exception $e) {
      return new JsonResponse(
        [
          'success' => true,
          'input'   => !$useJson?$xml_data:json_encode($cart),
          'object'  => '<pre>'.print_r($cart,true).'</pre>',
          'preview' => print_r($e->getMessage(),true),
        ]
      );
    }
    $xmlDataPreview = '';
    if(!$useJson) {
      try {
        $xmlDataPreview = $this->convertArrayToSimpleXml($cart2);
        $xmlDataPreview = $this->formatXmlForOutput($xmlDataPreview->asXML());
      }
      catch(Exception $e) {
        $xmlDataPreview = '';
      }
    }

    return new JsonResponse(
      [
        'success' => true,
        'input'   => !$useJson?$xml_data:json_encode($cart),
        'object'  => '<pre>'.print_r($cart,true).'</pre>',
        'preview' => !$useJson?$xmlDataPreview:json_encode($cart),
      ]
    );
  }


  /**
   * @param string $xmlstring
   *
   * @return string
   */
  protected function formatXmlForOutput($xmlstring)
  {
    $ret = '';
    $startpos = 0;
    $lvl = 1;
    $xmlstring = trim($xmlstring);
    while(($pos = strpos($xmlstring,'<',$startpos)) !== false) {
      $isOpening = true;
      $isClosing = false;
      $pos1 = strpos($xmlstring,' ', $pos+1);
      $pos2 = strpos($xmlstring,'>', $pos+1);
      if($pos1 !== false && $pos2 !== false) {
        if ($xmlstring[$pos + 1] === '/') {
          $isClosing = true;
          $isOpening = false;
        }
        elseif($xmlstring[$pos2 - 1] === '/') {
          $isClosing = true;
          $isOpening = true;
        }
      }
      elseif($pos2 !== false) {
        if ($xmlstring[$pos + 1] === '/') {
          $isClosing = true;
          $isOpening = false;
        }
        elseif($xmlstring[$pos2 - 1] === '/') {
          $isClosing = true;
          $isOpening = true;
        }
      }
      else {
        $pos2 = $pos;
      }

      $prev = trim(substr($xmlstring, $startpos, $pos-$startpos));

      if($isClosing) {
        $lvl--;
      }

      $xmlPart = str_repeat('  ', $lvl).trim(substr($xmlstring, $pos, $pos2 - $pos + 1)). "\n";

      if($isOpening) {
        $lvl++;
      }

      if($lvl < 1) {
        $lvl = 1;
      }
      if($prev !== '') {
        $ret = trim($ret).$prev.ltrim($xmlPart);
      }
      else{
        $ret .= $prev . $xmlPart;
      }
      $startpos = $pos2 + 1;
    }
    $ret .= str_repeat('  ',$lvl).trim(substr($xmlstring, $startpos + 1));

    return $ret;
  }

  /**
   * @param int $shopId
   *
   * @return mixed|null
   */
  public function getSettingFields($shopId)
  {
    if(empty($shopId)) {
      return null;
    }

    $json = $this->app->DB->Select(sprintf('SELECT `einstellungen_json` FROM `shopexport` WHERE `id` = %d', $shopId));
    if(empty($json)) {
      return null;
    }

    $json = json_decode($json, true);
    if(empty($json['felder'])) {
      return null;
    }

    return $json['felder'];
  }

  /**
   * @param int $shopId
   *
   * @return string|null
   */
  protected function getTemplateFromShop($shopId)
  {
    $fields = $this->getSettingFields($shopId);
    if(empty($fields) || empty($fields['transform_cart_active']) || empty($fields['transform_cart'])) {
      return null;
    }

    return $fields['transform_cart'];
  }

  /**
   * @param int   $addressId
   * @param array $cart
   * @param int   $projekt
   * @param int   $shopId
   */
  public function ImportAuftragBeforeHook($addressId, &$cart, $projekt, $shopId)
  {
    try {
      $fields = $this->getSettingFields($shopId);
      if(empty($fields) || empty($fields['transform_cart_active']) || empty($fields['transform_cart'])) {
        return;
      }
      $content = $fields['transform_cart'];
      if(empty($content)){
        return;
      }
      $isJson = !empty($fields['transform_cart_format']) && $fields['transform_cart_format'] === 'json';
      $merge = empty($fields['transform_cart_replace']);
      if(!$isJson) {
        $xml = $this->convertArrayToSimpleXml($cart);
        $xmlString = $xml->asXML();
        if(empty($xmlString)){
          return;
        }
      }
      $extId = empty($cart['auftrag'])?'':$cart['auftrag'];
      $internet = empty($cart['onlinebestellnummer'])?'':$cart['onlinebestellnummer'];
      $this->app->DB->Insert(
        sprintf(
          "INSERT INTO `onlineshop_transfer_cart` 
            (`shop_id`, `template`, `cart_original`, `extid`, `internet`, `status`)
          VALUES (%d, '%s', '%s', '%s', '%s', 'error')  ",
          $shopId, $this->app->DB->real_escape_string($content),
          $this->app->DB->real_escape_string(base64_encode(json_encode($cart))),
          $this->app->DB->real_escape_string($extId),
          $this->app->DB->real_escape_string($internet)
        )
      );
      $transferId = (int)$this->app->DB->GetInsertID();
      $cartWithItem = $this->convertSimpleXmlToArray($xml, null, false);

      $this->loadSmarty($shopId);
      if($isJson) {
        $newCart = $this->createCartObjectFromTemplateJson($cart, $content,$shopId, $merge);
      }
      else{
        $newCarts = $this->createCartObjectFromTemplate($cart, $cartWithItem, $content, $shopId, $merge);
        $newCart = $newCarts[0];
      }
      if(!empty($newCart)){
        if($transferId > 0) {
          $this->app->DB->Update(
            sprintf(
              "UPDATE `onlineshop_transfer_cart` 
              SET `cart_transfer` = '%s', `status` = 'transferred' 
              WHERE `id` = %d",
              $this->app->DB->real_escape_string(base64_encode(json_encode($newCart))),
              $transferId
            )
          );
        }
        $cart = $newCart;
      }
    }
    catch (Exception $e) {

    }
  }

  /**
   * @param array       $cart
   * @param int         $shopId
   * @param bool        $useJson
   * @param bool        $merge
   * @param string|null $content
   *
   * @return array
   */
  public function addCartInfo($cart, $shopId, $useJson = false, $merge = true, $content = null)
  {
    if($content === null) {
      $content = $this->getTemplateFromShop($shopId);
    }
    if(empty($content)) {
      if(!$merge) {
        return null;
      }
      return $cart;
    }

    if($useJson){
      $this->loadSmarty($shopId);

      return $this->createCartObjectFromTemplateJson($cart, $content, $shopId, $merge);
    }

    $xml = $this->convertArrayToSimpleXml($cart);
    $xmlString = $xml->asXML();
    if(empty($xmlString)) {
      if(!$merge) {
        return null;
      }
      return $cart;
    }
    $cartWithItem = $this->convertSimpleXmlToArray($xml, null, false);

    $this->loadSmarty($shopId);
    $newCarts = $this->createCartObjectFromTemplate($cart, $cartWithItem, $content, $shopId, $merge);
    $newCart = $newCarts[0];
    if(!empty($newCart)) {
      return $newCart;
    }
    if(!$merge) {
      return null;
    }
    return $cart;
  }

  /**
   * @return JsonResponse
   */
  public function HandleRunSmartyIncommingAjaxAction()
  {
    $shopId = (int)$this->app->Secure->GetPOST('shopid');
    $input = !isset($this->app->Secure->POST['input'])?null:$this->app->Secure->POST['input'];
    $content = !isset($this->app->Secure->POST['content'])?null:$this->app->Secure->POST['content'];
    $isJson = $this->app->Secure->GetPOST('format') === 'json';
    $replaceCart = (bool)(int)$this->app->Secure->GetPOST('replacecart');
    $xml = null;
    $cart = null;
    $cartWithItem = null;
    if(is_string($input)) {
      $input = trim($input);
    }
    if(is_string($content)) {
      $content = trim($content);
    }
    try {
      if($isJson){
        $cart = json_decode($input, true);
      }
      else {
        $xml = new SimpleXMLElement($input);
        $cart = $this->convertSimpleXmlToArray($xml, null, true);
        $cartWithItem = $this->convertSimpleXmlToArray($xml, null, false);
      }
    }
    catch (Exception $e) {
      $xml = null;
      $cart = null;
      $cartWithItem = null;
    }
    $this->loadSmarty($shopId);
    try {
      if($isJson) {
        $newCart = $this->createCartObjectFromTemplateJson($cart, $content, $shopId, !$replaceCart);
      }
      else{
        $newCarts = $this->createCartObjectFromTemplate($cart, $cartWithItem, $content, $shopId, !$replaceCart);
        $newCart = $newCarts[0];
      }
    }
    catch(Exception $e) {
      return new JsonResponse(
        [
          'success' => true,
          'preview' => print_r($e->getMessage(), true),
          'object' => '<pre>'.print_r($cart, true).'</pre>',
        ]
      );
    }
    try{
      if($isJson) {
        $xmlData = json_encode($newCart);
      }
      else {
        $xmlData = $this->convertArrayToSimpleXml($newCart);
        $xmlData = $this->formatXmlForOutput($xmlData->asXML());
      }
    }
    catch(Exception $e) {

    }

    return new JsonResponse(
      [
        'success' => true,
        'preview' => $xmlData,
        'object' => '<pre>'.print_r($cart, true).'</pre>',
      ]
    );
  }

  /**
   * @return JsonResponse
   */
  public function HandleSaveSmartyIncommingAjaxAction()
  {
    $shopId = (int)$this->app->Secure->GetPOST('shopid');
    $input = !isset($this->app->Secure->POST['input'])?null:$this->app->Secure->POST['input'];
    $content = !isset($this->app->Secure->POST['content'])?null:$this->app->Secure->POST['content'];
    $format = $this->app->Secure->GetPOST('format');
    $replaceCart = (int)$this->app->Secure->GetPOST('replacecart');
    $active = (int)$this->app->Secure->GetPOST('active');

    $shopArr = $this->app->DB->SelectRow(
      sprintf(
        'SELECT `einstellungen_json` FROM `shopexport` WHERE `id` = %d',
        $shopId
      )
    );
    if(!empty($shopArr)) {
      $json = json_decode($shopArr['einstellungen_json'], true);
      if(empty($json)) {
        $json = [];
      }
      $json['felder']['transform_cart'] = $content;
      $json['felder']['transform_cart_format'] = $format;
      $json['felder']['transform_cart_replace'] = $replaceCart;
      $json['felder']['transform_cart_active'] = $active;
      $json['felder']['transform_cart_data'] = $input;
      $json = json_encode($json);
      if(!empty($json) && !empty(json_decode($json, true))){
        $this->app->DB->Update(
          sprintf(
            "UPDATE `shopexport` SET `einstellungen_json` = '%s' WHERE `id` = %d",
            $this->app->DB->real_escape_string($json), $shopId
          )
        );
      }
      return new JsonResponse(
        [
          'success' => true,
        ]
      );
    }

    return new JsonResponse(
      [
        'success' => false,
        'error' => 'Shop konnte nicht gespeichert werden',
      ],
      JsonResponse::HTTP_BAD_REQUEST
    );
  }


  /**
   * @param array  $array
   * @param string $rootNode Name des Root-Elements
   *
   * @return SimpleXMLElement
   */
  public function convertArrayToSimpleXml($array, $rootNode = 'xml')
  {
    $rootNodeCloser = explode(' ', $rootNode);
    $rootNodeCloser = reset($rootNodeCloser);
    $xml = new SimpleXMLElement(
      sprintf('<?xml version="1.0" encoding="UTF-8"?><%s></%s>', $rootNode, $rootNodeCloser)
    );
    $nameSpaces = $this->getNameSpacesByNode($rootNode);
    $this->arrayToXmlHelper($xml, $array, $nameSpaces);

    return $xml;
  }


  /**
   * @param string $key
   * @param array  $nameSpaces
   *
   * @return array
   */
  protected function getAttributesFromKey($key, $nameSpaces = [])
  {
    $keyArr = explode(' ', $key);
    $nameSpace = null;
    $node = $keyArr[0];
    if(strpos($node, ':') !== false) {
      list($nameSpaceShort, $node) = explode(':', $node, 2);
      if($nameSpaceShort !== '' && isset($nameSpaces[$nameSpaceShort])) {
        $nameSpace = $nameSpaces[$nameSpaceShort];
      }
    }
    unset($keyArr[0]);
    $attributes = [];
    foreach($keyArr as $attr) {
      if(empty($attr)) {
        continue;
      }
      $attrA = explode('=', $attr,2);
      if(!empty($attrA[1])) {
        $attrA[1] = trim($attrA[1],'"');
      }
      $attributes[] = $attrA;
    }

    return [$node, $attributes, $nameSpace];
  }

  /**
   * @see convertArrayToSimpleXml
   *
   * @param SimpleXMLElement $xmlObj
   * @param array            $array
   * @param array            $nameSpaces
   * @param string           $parentTag
   * @param array            $attributesFromParent
   */
  protected function arrayToXmlHelper(&$xmlObj, $array, $nameSpaces = [],  $parentTag = '', $attributesFromParent = [])
  {
    foreach ($array as $key => $value) {
      if(is_int($key)) {
        $key = 'item'.$key;
      }
      // Wenn kein Knotenname ermittelt werden konnte > den Knoten 'item' nennen
      $subNodeName = is_int($key) ? 'item' : $key;
      if(!empty($parentTag) && is_int($key)) {
        $subNodeName = $parentTag;
      }
      list($subNodeName, $attributes, $nameSpace) = $this->getAttributesFromKey($subNodeName, $nameSpaces);

      if (is_array($value)) {
        $useParentTag = false;// !empty($key);
        foreach ($value as $key2 => $value2) {
          if(!is_int($key2) || !$useParentTag) {
            $useParentTag = false;
            break;
          }
        }
        if($useParentTag) {
          $this->arrayToXmlHelper($xmlObj, $value, $nameSpaces, $subNodeName, $attributes);
        }
        else {
          $subNode = $xmlObj->addChild((string)$subNodeName, null, $nameSpace);
          if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
              $subNode->addAttribute((string)$attribute[0],
                empty($attribute[1]) ? '' : (string)$attribute[1]);
            }
          }
          elseif(!empty($attributesFromParent)) {
            foreach ($attributesFromParent as $attribute) {
              $subNode->addAttribute((string)$attribute[0],
                empty($attribute[1]) ? '' : (string)$attribute[1]);
            }
          }
          $this->arrayToXmlHelper($subNode, $value, $nameSpaces,$subNodeName);
        }
      } else {
        $subNode = $xmlObj->addChild((string)$subNodeName, htmlspecialchars($value, ENT_QUOTES), $nameSpace);
        if(!empty($attributes)) {
          foreach($attributes as $attribute) {
            $subNode->addAttribute((string)$attribute[0], empty($attribute[1])?'':(string)$attribute[1]);
          }
        }
        elseif(!empty($attributesFromParent)) {
          foreach($attributesFromParent as $attribute) {
            $subNode->addAttribute((string)$attribute[0], empty($attribute[1])?'':(string)$attribute[1]);
          }
        }
      }
    }
  }


  /**
   * @param string $node
   *
   * @return array
   */
  protected function getNameSpacesByNode($node)
  {
    $nameSpaces = [];
    $nodeArr = explode(' ', $node);
    unset($nodeArr[0]);
    foreach($nodeArr as $nodeVal) {
      $nodeVal = trim($nodeVal);
      if(empty($nodeVal)) {
        continue;
      }
      if(preg_match_all('/xmlns(:{0,1})([^=]*)="([^"]+)"/', $nodeVal, $matches)) {
        $nameSpaces[$matches[2][0]] = $matches[3][0];
      }
    }

    return $nameSpaces;
  }

  /**
   * @param $id
   */
  protected function loadSmarty($id)
  {
    $this->smartydir = 'Modules/Onlineshops';
    $this->segment = 'online'.$id;// uniqid('online', true);
    $this->templateDir = $this->app->erp->GetTMP().$this->smartydir.'/template_'.$id.$this->segment;
    $this->tmpl = $this->app->Container->get('TransferSmartyTemplate');
    if(!is_dir($this->templateDir) && !mkdir($this->templateDir, 0777, true)
      && !is_dir($this->templateDir)) {
      return;
    }

    $this->tmpl->addTemplateDir($this->segment, $this->templateDir . '/');
    try {
      $this->tmpl->registerPlugin('modifier', 'jsondecode', [$this, 'SmartyJsonDecode']);
      $this->tmpl->registerPlugin('modifier', 'jsonencode', [$this, 'SmartyJsonEncode']);
      $this->tmpl->registerPlugin('modifier', 'arraytolist', [$this, 'SmartyArrayToList']);
      $this->tmpl->registerPlugin('modifier', 'print_r', [$this, 'SmartyPrintR']);
      $this->tmpl->registerPlugin('modifier', 'preg_replace', 'preg_replace');
      $this->tmpl->registerPlugin('modifier', 'substr', 'substr');
      $this->tmpl->registerPlugin('modifier', 'strtolower', 'strtolower');
      $this->tmpl->registerPlugin('modifier', 'strtoupper', 'strtoupper');
      $this->tmpl->registerPlugin('modifier', 'explode', 'explode');
      $this->tmpl->registerPlugin('modifier', 'strstr', 'strstr');
      $this->tmpl->registerPlugin('modifier', 'strlen', 'strlen');
      $this->tmpl->registerPlugin('modifier', 'strtotime', 'strtotime');
      $this->tmpl->registerPlugin('modifier', 'intval', 'intval');
    }
    catch (Exception $e) {

    }
  }

  /**
   * @param string $content
   *
   * @return string
   */
  protected function reformatTemplate($content) {
    return str_replace("\\\n",'', str_replace("\r", '', $content));
  }

  /**
   * @param array  $cartObj
   * @param string $template
   * @param int    $shopId
   * @param bool   $merge
   *
   * @return array
   */
  protected function createCartObjectFromTemplateJson($cartObj, $template, $shopId, $merge = true)
  {
    if(!is_dir($this->templateDir) && !mkdir($this->templateDir, 0777, true)
      && !is_dir($this->templateDir)) {
      if($merge){
        return $cartObj;
      }
      return null;
    }

    @file_put_contents(
      $this->templateDir.'/cart_'.$shopId.'.tpl', trim($this->reformatTemplate($template))
    );
    try {
      $object = json_decode(json_encode($cartObj));
      $this->tmpl->assign('cart', $object);
      $this->tmpl->assign('cartarray', $cartObj);
      $this->tmpl->assign('object', $object);
      $this->tmpl->assign('objectarray', $cartObj);
      $shop = $this->app->DB->SelectRow(sprintf('SELECT * FROM `shopexport` WHERE `id` = %d', $shopId));
      $settings = @json_decode($shop['einstellungen_json'], true);
      $settings = empty($settings['felder'])?null:$settings['felder'];
      $this->tmpl->assign(
        'shop',
        $shop
      );
      $this->tmpl->assign(
        'shopsettings',
        json_decode(json_encode($settings))
      );
    }
    catch (Exception $e) {
      if($merge){
        return $cartObj;
      }
      return null;
    }
    try {
      $content = $this->tmpl->fetch($this->segment, 'cart_' . $shopId . '.tpl');
    }
    catch (Exception $e) {
      throw $e;
    }
    $content = trim($content);
    $ret = json_decode($content, true);
    if(!$merge) {
      return $ret;
    }

    return $this->mergeArrays($cartObj, $ret);
  }

  /**
   * @param array  $cartObj
   * @param array  $cartWithItemObj
   * @param string $template
   * @param int    $shopId
   * @param bool   $merge
   *
   * @return array
   */
  protected function createCartObjectFromTemplate($cartObj, $cartWithItemObj, $template, $shopId, $merge = true)
  {
    if(!is_dir($this->templateDir) && !mkdir($this->templateDir, 0777, true)
      && !is_dir($this->templateDir)) {
      if($merge){
        return [$cartObj, $cartWithItemObj];
      }
      return [null, null];
    }

    @file_put_contents(
      $this->templateDir.'/cart_'.$shopId.'.tpl', trim($this->reformatTemplate($template))
    );

    try {
      $object = json_decode(json_encode($cartWithItemObj));
      $this->tmpl->assign('cart', $object);
      $this->tmpl->assign('object', $object);
    }catch (Exception $e) {
      if($merge){
        return [$cartObj, $cartWithItemObj];
      }
      return [null, null];
    }
    try {
      $content = $this->tmpl->fetch($this->segment, 'cart_' . $shopId . '.tpl');
    }
    catch (Exception $e) {
      throw $e;
    }
    try {
      $content = trim($content);
      $xml = new SimpleXMLElement($content);
      if($xml === false) {
        $ret = json_decode($content, true);
        if(is_array($ret)) {
          if($merge){
            return [
              $this->mergeArrays($cartObj, $ret),
              null,
            ];
          }
          return [$ret, null];
        }
      }

      $ret = $this->convertSimpleXmlToArray($xml);
      $retItem = $this->convertSimpleXmlToArray($xml, null, false);
      if(is_array($ret)) {
        if($merge){
          return [
            $this->mergeArrays($cartObj, $ret),
            $this->mergeArrays($cartWithItemObj, $retItem),
          ];
        }
        return [$ret, $retItem];
      }
    }
    catch (Exception $e) {

    }

    if($merge){
      return [$cartObj, $cartWithItemObj];
    }
    return [null, null];
  }

  /**
   * @param array $from
   * @param array $to
   */
  public function mergeArrays($from, $to)
  {
    $ret = $from;
    if(is_array($to)) {
      foreach($to as $key => $value) {
        if(!isset($ret[$key])) {
          $ret[$key] = $value;
          continue;
        }
        if(is_array($value)) {
          $ret[$key] = $this->mergeArrays($ret[$key], $value);
          continue;
        }
        $ret[$key] = $value;
      }
    }

    return $ret;
  }
}
