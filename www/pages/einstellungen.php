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
class Einstellungen  {
  /** @var Application */
  var $app;

  const MODULE_NAME = 'Setting';

  /** @var array $stylesheet */
  public $stylesheet = [
      './classes/Modules/Setting/www/css/setting.css',
  ];

  /** @var array $javascript */
  public $javascript = [
    './classes/Modules/Setting/www/js/setting_betaprogram.js',
  ];

  /**
   * Einstellungen constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    $this->app=$app;
    if($intern) {
      return;
    }

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","EinstellungenCreate");
    $this->app->ActionHandler("edit","EinstellungenEdit");
    $this->app->ActionHandler("list","EinstellungenList");
    $this->app->ActionHandler("betaprogram", "EinstellungenBetaProgram");
    $this->app->ActionHandler("category","SettingsCategoryApps");
    $this->app->ActionHandlerListen($app);

    $this->app->Tpl->Set('UEBERSCHRIFT',"Einstellungen");
    $this->app->Tpl->Set('FARBE',"[FARBE5]");
  }

  /**
   * @param bool $parameter
   *
   * @return null|string
   */
  protected function sendBeta($parameter)
  {
    $setBeta = $parameter;
    $updateResultStatus = null;
    include_once dirname(__DIR__, 2).'/upgradesystemclient2_includekey.php';

    return $updateResultStatus;
  }

  /**
   * @param bool $betaStatus
   */
  public function tryChangeBetaStatusAndLocateToSite(bool $betaStatus): void
  {
    $updateServerResult = $this->sendBeta($betaStatus);
    if($updateServerResult === 'OK') {
      $this->app->User->SetParameter('updatekey', 1);
      $this->app->User->SetParameter('einstellungen_beta_activated', $betaStatus ? '1' : '');
      /** @var Appstore $appStore */
      $appStore = $this->app->loadModule('appstore');
      $appStore->resetBuyListCache();
      $appStore->resetBuyInfoCache();
      $this->app->Location->execute('index.php?module=einstellungen&action=betaprogram&cmd=updatekey');
    }
    if($betaStatus) {
      $msg = $this->app->erp->base64_url_encode(
        '<div class="error">Das Beta-Programm konnte nicht aktiviert werden</div>'
      );
    }
    else {
      $msg = $this->app->erp->base64_url_encode(
        '<div class="error">Das Beta-Programm konnte nicht beendet werden</div>'
      );
    }
    $this->app->Location->execute('index.php?module=einstellungen&action=betaprogram&msg='.$msg);
  }

  /**
   * @return void
   */
  public function EinstellungenBetaProgram()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'updatekey') {
      include dirname(dirname(__DIR__)).'/version.php';
      $this->app->User->SetParameter('updatekey','');
      include_once dirname(dirname(__DIR__)).'/upgradesystemclient2_includekey.php';
      $this->app->Location->execute('index.php?module=einstellungen&action=betaprogram');
    }
    if($this->app->Secure->GetPOST('deactivate')) {
      $this->tryChangeBetaStatusAndLocateToSite(false);
    }
    if($this->app->Secure->GetPOST('beta-agreement')) {
      $this->tryChangeBetaStatusAndLocateToSite(true);
    }

    $openPopup = !empty($this->app->User->GetParameter('einstellungen_beta_activated'));

    if($openPopup) {
      $this->app->User->SetParameter('einstellungen_beta_activated', '');
      $this->app->Tpl->Set('BETAOPEN', '1');
    }
    else {
      $this->app->Tpl->Set('BETAOPEN', '');
    }

    $this->app->erp->MenuEintrag('index.php?module=einstellungen&action=betaprogram', 'Beta Programm');
    if(!empty(erpAPI::Ioncube_Property('isbetaactive'))) {
      $this->app->Tpl->Set('BEFORESHOWIFBETADEACTIVATED', '<!--');
      $this->app->Tpl->Set('AFTERSHOWIFBETADEACTIVATED', '-->');
    }
    else {
      $this->app->Tpl->Set('BEFORESHOWIFBETAACTIVATED', '<!--');
      $this->app->Tpl->Set('AFTERSHOWIFBETAACTIVATED', '-->');
    }
    $this->app->erp->Headlines('Beta Programm');
    $this->app->Tpl->Parse('PAGE', 'einstellungen_betaprogram.tpl');
  }

  /**
   * @return string[][][]
   */
  public function getModulesByCategory()
  {
    return [
      'System' => [
        'firmendaten' => [
          'name' => 'Grundeinstellungen',
          'icon' => 'Icons_dunkel_1.gif',
          'link' => 'index.php?module=firmendaten&action=edit',
        ],
        'benutzer' => [
          'name' => 'Benutzer',
          'icon' => 'Icons_dunkel_2.gif',
          'link' => 'index.php?module=benutzer&action=list',
        ],
        'geschaeftsbrief_vorlagen' => [
          'name' => 'Gesch&auml;ftsbrief Vorlagen',
          'icon' => 'Icons_dunkel_3.gif',
          'link' => 'index.php?module=geschaeftsbrief_vorlagen&action=list',
        ],
        'emailbackup' => [
          'name' => 'E-Mail Accounts',
          'icon' => 'Icons_dunkel_4.gif',
          'link' => 'index.php?module=emailbackup&action=list',
        ],
        'ticket_vorlage' => [
          'name' => 'Ticket Vorlagen',
          'icon' => 'Icons_dunkel_5.gif',
          'link' => 'index.php?module=ticket_vorlage&action=list',
        ],
        'warteschlangen' => [
          'name' => 'Warteschlangen',
          'icon' => 'Icons_dunkel_6.gif',
          'link' => 'index.php?module=warteschlangen&action=list',
        ],
        'artikelkategorien' => [
          'name' => 'Artikel Kategorien',
          'icon' => 'Icons_dunkel_15.gif',
          'link' => 'index.php?module=artikelkategorien&action=list',
        ],
        'artikeleinheit' => [
          'name' => 'Artikel Einheiten',
          'icon' => 'Icons_dunkel_14.gif',
          'link' => 'index.php?module=artikeleinheit&action=list',
        ],
        'gruppen' => [
          'name' => 'Gruppen',
          'icon' => 'Icons_dunkel_2.gif',
          'link' => 'index.php?module=gruppen&action=list',
        ],
        'uservorlage' => [
          'name' => 'Benutzer Vorlage',
          'icon' => 'Icons_dunkel_2.gif',
          'link' => 'index.php?module=uservorlage&action=list',
        ],
        'laender' => [
          'name' => 'L&auml;nderliste',
          'icon' => 'Icons_dunkel_14.gif',
          'link' => 'index.php?module=laender&action=list',
        ],
        'kalender' => [
          'name' => 'Kalender Gruppen',
          'icon' => 'Icons_dunkel_2.gif',
          'link' => 'index.php?module=kalender&action=gruppenlist',
        ],
        'prozessstarter' => [
          'name' => 'Prozessstarter',
          'icon' => 'Icons_dunkel_22.gif',
          'link' => 'index.php?module=prozessstarter&action=list',
        ],
        'welcome' => [
          'name' => 'Lizenz',
          'icon' => 'Icons_dunkel_23.gif',
          'link' => 'index.php?module=welcome&action=info',
        ],
        'drucker' => [
          'name' => 'Drucker',
          'icon' => 'Icons_dunkel_24.gif',
          'link' => 'index.php?module=drucker&action=list',
        ],
        'adapterbox' => [
          'name' => 'Adapterbox',
          'icon' => 'Icons_dunkel_25.gif',
          'link' => 'index.php?module=adapterbox&action=list',
        ],
        'protokoll' => [
          'name' => 'Protokoll',
          'icon' => 'Icons_dunkel_22.gif',
          'link' => 'index.php?module=protokoll&action=list',
        ],
        'etiketten' => [
          'name' => 'Etiketten',
          'icon' => 'Icons_dunkel_14.gif',
          'link' => 'index.php?module=etiketten&action=list',
        ],
        'uebersetzung' => [
          'name' => '&Uuml;bersetzungen',
          'icon' => 'Icons_dunkel_14.gif',
          'link' => 'index.php?module=uebersetzung&action=list',
        ],
        'arbeitsfreietage' => [
          'name' => 'Arbeitsfreie Tage',
          'icon' => 'Icons_dunkel_14.gif',
          'link' => 'index.php?module=arbeitsfreietage&action=list',
        ],
        'support' => [
          'name' => 'Support',
          'icon' => 'Icons_dunkel_23.gif',
          'link' => 'index.php?module=support&action=list',
        ],
        'api_account' => [
          'name' => 'API-Account',
          'icon' => 'Icons_dunkel_25.gif',
          'link' => 'index.php?module=api_account&action=list',
        ],
      ],
      'Lager und Logistik' => [
        'zolltarifnummer' => [
          'name' => 'Zolltarifnummer',
          'icon' => 'Icons_dunkel_16.gif',
          'link' => 'index.php?module=zolltarifnummer&action=list',
        ],
        'versandarten' => [
          'name' => 'Versandarten',
          'icon' => 'Icons_dunkel_16.gif',
          'link' => 'index.php?module=versandarten&action=list',
        ],
        'lieferbedingungen' => [
          'name' => 'Lieferbedingungen',
          'icon' => 'Icons_dunkel_16.gif',
          'link' => 'index.php?module=lieferbedingungen&action=list',
        ],
      ],
      'Shop Schnittstelle' => [
        'onlineshops' => [
          'name' => 'Online-Shops',
          'icon' => 'Icons_dunkel_20.gif',
          'link' => 'index.php?module=onlineshops&action=list',
        ],
      ],
      'Mitarbeiter Verwaltung' => [
        'stechuhrdevice' => [
          'name' => 'Mitarbeiterzeiterfassung Ger&auml;teeinstellungen',
          'icon' => 'Icons_dunkel_14.gif',
          'link' => 'index.php?module=stechuhrdevice&action=list',
        ],
      ],
    ];
  }


  function EinstellungenCreate()
  {
    $this->app->Tpl->Add('TABS',
      "<a class=\"tab\" href=\"index.php?module=artikel&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a>");
  }

  /**
   * @return array
   */
  public function getTestModules()
  {
    /** @var Appstore $appstore */
    $ret = [];
    $appstore = $this->app->erp->LoadModul('appstore');
    $modules = $appstore->GetApps();
    if(!empty($modules['installiert'])) {
      foreach($modules['installiert'] as $k => $modul) {
        if(!empty($modul['ablaufdatum']) || ($modul['key'] === 'stechuhrdevice' && $this->app->erp->RechteVorhanden('stechuhrdevice','list'))) {
          if(!empty($modul['ablaufdatum']) && strtotime($modul['ablaufdatum']) < strtotime(date('Y-m-d'))) {
            continue;
          }
          $ret[] = [
            'Icon'        => $modul['Icon'],
            'ablaufdatum' => !empty($modul['ablaufdatum'])?$this->app->String->Convert($modul['ablaufdatum'], '%1-%2-%3', '%3.%2.%1'):null,
            'key'         => $modul['key'],
            'Link'        => $modul['Link'],
            'Bezeichnung' => strip_tags($modul['Bezeichnung'])
          ];
        }
      }
    }
    return $ret;
  }

  /**
   * @param array $array
   * @param int   $countPerRow
   *
   * @return array
   */
  public function groupArray($array, $countPerRow = 0)
  {
    $ret = [];
    if($countPerRow === 0) {
      foreach($array as $key => $element) {
        $ret[] = [$key => $element];
      }

      return $ret;
    }

    $i = 0;
    $row = [];
    foreach($array as $key => $elements) {
      $i++;
      $row[$key] = $elements;
      if($i % $countPerRow === 0) {
        $ret[] = $row;
        $row = [];
      }
    }

    if(!empty($row)) {
      $ret[] = $row;
    }

    return $ret;
  }

  /**
   * @return array
   */
  public function getAppsWithSettings()
  {
    /** @var Appstore $appstore */
    $appstore = $this->app->loadModule('appstore');
    $apps = $appstore->GetApps();
    $kauf = empty($apps['kauf'])?[]:$apps['kauf'];
    $apps = empty($apps['installiert'])?[]:$apps['installiert'];
    if(!empty($kauf)) {
      foreach($kauf as $module) {
        if($this->app->erp->ModulVorhanden($module['key'])) {
          $apps[] = $module;
        }
      }
    }
    $appsWithSettings = [];
    $modulesWithSettings = [];
    /** @var Benutzer $userModule */
    $userModule = $this->app->loadModule('benutzer');
    $enc = $userModule->ScanModules();
    if(!empty($enc)) {
      foreach($enc as $module => $actions) {
        $module = strtolower($module);
        if(in_array('settings', $actions)) {
          $modulesWithSettings[$module] = 'settings';
          continue;
        }
        if(in_array('einstellungen', $actions)) {
          $modulesWithSettings[$module] = 'einstellungen';
          continue;
        }
        if(in_array('einstellung', $actions)) {
          $modulesWithSettings[$module] = 'einstellung';
          continue;
        }
      }
    }
    $sprachen = array('german','english');
    if(method_exists($this->app->User,'GetSprachen')) {
      $sprachen = $this->app->User->GetSprachen();
      if (in_array('deutsch', $sprachen, true) && !in_array('german', $sprachen, true)) {
        $sprachen[] = 'german';
      }
    }
    $inline = [];
    if(is_array($sprachen)) {
      $sprachen = array_reverse($sprachen);

      foreach($sprachen as $sprache)  {
        if(preg_match_all('/[a-zA-Z0-9\-]/', $sprache, $erg)) {
          if(is_file(dirname(dirname(__DIR__)).'/languages/'.$sprache.'/inline.php')) {
            include dirname(dirname(__DIR__)).'/languages/'.$sprache.'/inline.php';
          }
        }
      }
    }

    foreach($apps as $app) {

      $showInSettings = isset($app['settings']) && $app['settings']==true;

      if(empty($modulesWithSettings[$app['key']]) && !$showInSettings) {
        continue;
      }
      if(!$this->app->erp->RechteVorhanden($app['key'], $modulesWithSettings[$app['key']]) && !$showInSettings) {
        continue;
      }
      if(!empty($inline[$app['key']]['default']['kategorie'])) {
        $app['kategorie'] = $inline[$app['key']]['default']['kategorie'];
      }
      $category = empty($app['kategorie'])?'{|System|}':$app['kategorie'];
      if($showInSettings){
        $link = $app['Link'];
      }else{
        $link = 'index.php?module='.$app['key'].'&action='. $modulesWithSettings[$app['key']];
      }

      $icon = [
        'name' => $app['Bezeichnung'],
        'icon' => $app['Icon'],
        'link' => $link,
        'key'  => $app['key'],
      ];
      $appsWithSettings[$category][$app['Bezeichnung']] = $icon;
    }

    ksort($appsWithSettings);
    $return = [];
    foreach($appsWithSettings as $category => $apps) {
      ksort($apps);
      $return[$category] = [];
      foreach($apps as $app) {
        $return[$category][$app['key']] = $app;
      }
    }

    return $return;
  }

  public function SettingsCategoryApps()
  {
    $categoryFromParam = $this->app->Secure->GetGET('category', null, '', true);

    $iconSearchString = $categoryFromParam;
    if(strstr($categoryFromParam,'{|') === false){
      $iconSearchString = '{|'.$categoryFromParam.'|}';
    }

    /** @var Appstore $appstore */
    $appstore = $this->app->erp->LoadModul('appstore');
    $iconTag = $appstore->GetAppIconTagByCategory($iconSearchString);
    $html = '';
    $appList = $appstore->GetApps();

    $this->app->erp->Headlines('&nbsp;');
    $overviewTile = $appstore->getOverViewFromCategory($categoryFromParam);
    $this->app->Tpl->Set(
      'CURRENTHEADLINES',
      '<span class="current-headlines">Einstellungen / <b>'. $categoryFromParam .'</b></span>'
    );
    $this->app->Tpl->Set('CATEGORY', $categoryFromParam);

    if($overviewTile !== null) {
      $html .= $this->createIconHtmlForCategory($overviewTile, $categoryFromParam, $iconTag);
    }

    foreach ($appList['installiert'] as $modul) {
      $cleanCategory = str_replace(array('{|','|}'), '', $modul['kategorie']);
      if ($categoryFromParam === $cleanCategory) {
        $html .= $this->createIconHtmlForCategory($modul, $categoryFromParam, $iconTag);
      }
    }

    $this->app->Tpl->Set('HTML', $html);

    $this->app->Tpl->Parse('STANDARDMODULE', 'einstellungen_apptile.tpl');

    $this->app->Tpl->Parse('TAB1', 'einstellungen_categorysite.tpl');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  /**
   * @param array  $modul
   * @param string $categoryFromParam
   * @param string $iconTag
   *
   * @return string
   */
    public function createIconHtmlForCategory($modul, $categoryFromParam, $iconTag)
    {
      $link = 'index.php?module='. $modul['key'] .'&action=list';
      if(!empty($modul['Link'])) {
        $link = $modul['Link'];
      }

      return '<a href="'. $link.
        '&overviewpage=einstellungen&overviewpageaction=list'.
        '&backlinkmodule='.urlencode('einstellungen').'&backlinkparameter='.urlencode((string)$categoryFromParam).'" class="tilegrid-tile modulecol" id="m'.$modul['md5'].'">'.
        '<div class="icon app-category-icon-'. $iconTag .'"></div>'.
        '<div>' .
        '<span class="tilegrid-tile-title">' .
        strip_tags($modul['Bezeichnung'])
        .'</span><br>' .
        (!empty($modul['ablaufdatum'])
          ? '<small>{|Ablauf am|} '
          . $this->app->String->Convert($modul['ablaufdatum'], '%1-%2-%3', '%3.%2.%1') . '</small>'
          : '')
        . (!empty($modul['beta'])?' <span class="beta">BETA</span>':'')
        . (!empty($modul['legacy']) && $modul['legacy']?' <span class="legacy">LEGACY</span>':'')
        . (!empty($modul['special']) && $modul['special']?' <span class="special">SPECIAL</span>':'')
        . (!empty($modul['project_sensitive']) && $modul['project_sensitive']?' <span class="project_sensitive">PROJEKT</span>':'')
        . '</div>'
        . '</a>';
    }

  /**
   * @param string $category
   *
   * @return string
   */
    public function getCategoryLinkByCategory(string $category): string
    {
      switch($category) {
        case '{|Versandarten|}':
          return 'index.php?module=versandarten&action=list&overviewpage=einstellungen&overviewpageaction=list&backlinkparameter=einstellungen&backlinkparameter=list';
        case '{|Zahlungsweisen|}':
          return 'index.php?module=zahlungsweisen&action=list&overviewpage=einstellungen&overviewpageaction=list&backlinkmodule=einstellungen&backlinkparameter=list';
        case '{|Shop Schnittstelle|}':
          return 'index.php?module=onlineshops&action=list&overviewpage=einstellungen&overviewpageaction=list&backlinkmodule=einstellungen&backlinkparameter=list';
      }

      return 'index.php?module=einstellungen&action=category&category=' . str_replace(['{|', '|}'], '', $category);
    }

  public function EinstellungenList()
  {

    $this->app->erp->Headlines('&nbsp');
    $this->app->Tpl->Set('EMAILBACKUPLINK', 'href="index.php?module=emailbackup&action=list"');
    if(!$this->app->erp->ModulVorhanden('emailbackup')){
      $this->app->Tpl->Set('EMAILBACKUPLINK', '');
      $this->app->Tpl->Set('GREYEMAILBACKUP','<span style="left:0;position:absolute;display:inline-block; z-index=2; width:100%;height:100%;background: rgba(236,236,236,0.5)" ></span>');
    }
    $this->app->Tpl->Set('TICKETVORLAGENLINK', 'href="index.php?module=ticket_vorlage&action=list"');
    if(!$this->app->erp->ModulVorhanden('ticket_vorlage')){
      $this->app->Tpl->Set('TICKETVORLAGENLINK', '');
      $this->app->Tpl->Set('GREYTICKETVORLAGEN','<span style="left:0;position:absolute;display:inline-block; z-index=2; width:100%;height:100%;background: rgba(236,236,236,0.5)" ></span>');
    }
    $this->app->Tpl->Set('UEBERSETZUNGENLINK', 'href="index.php?module=uebersetzung&action=list"');
    if(!$this->app->erp->ModulVorhanden('uebersetzung')){
      $this->app->Tpl->Set('UEBERSETZUNGENLINK', '');
      $this->app->Tpl->Set('GREYUEBERSETZUNGEN','<span style="left:0;position:absolute;display:inline-block; z-index=2; width:100%;height:100%;background: rgba(236,236,236,0.5)" ></span>');
    }
    $this->app->Tpl->Set('KOSTENSTELLENLINK', 'href="index.php?module=kostenstellen&action=list"');
    if(!$this->app->erp->ModulVorhanden('kostenstellen')){
      $this->app->Tpl->Set('KOSTENSTELLENLINK', '');
      $this->app->Tpl->Set('GREYKOSTENSTELLEN','<span style="left:0;position:absolute;display:inline-block; z-index=2; width:100%;height:100%;background: rgba(236,236,236,0.5)" ></span>');
    }
    $this->app->Tpl->Set('ZOLLTARIFNUMMERLINK', 'href="index.php?module=zolltarifnummer&action=list"');
    if(!$this->app->erp->ModulVorhanden('zolltarifnummer')){
      $this->app->Tpl->Set('ZOLLTARIFNUMMERLINK', '');
      $this->app->Tpl->Set('GREYZOLLTARIFNUMMER','<span style="left:0;position:absolute;display:inline-block; z-index=2; width:100%;height:100%;background: rgba(236,236,236,0.5)" ></span>');
    }
    $this->app->Tpl->Set('WARTESCHLANGENLINK', 'href="index.php?module=warteschlangen&action=list"');
    if(!$this->app->erp->ModulVorhanden('warteschlangen')){
      $this->app->Tpl->Set('WARTESCHLANGENLINK', '');
      $this->app->Tpl->Set('GREYWARTESCHLANGEN','<span style="left:0;position:absolute;display:inline-block; z-index=2; width:100%;height:100%;background: rgba(236,236,236,0.5)" ></span>');
    }        
        
    $this->app->Tpl->Set('GREYVERRECHNUNGSART','<span style="left:0;position:absolute;display:inline-block; z-index=2; width:100%;height:100%;background: rgba(236,236,236,0.5)" ></span>');

    $this->app->Tpl->Set('GRUPPENLINK', 'href="index.php?module=gruppen&action=list"');
    if(!$this->app->erp->ModulVorhanden('gruppen')){
      $this->app->Tpl->Set('GRUPPENLINK', '');
      $this->app->Tpl->Set('GREYGRUPPEN','<span style="left:0;position:absolute;display:inline-block; z-index=2; width:100%;height:100%;background: rgba(236,236,236,0.5)" ></span>');
    }
    $this->app->Tpl->Set('ARTIKELKATEGORIENLINK', 'href="index.php?module=artikelkategorien&action=list"');
    if(!$this->app->erp->ModulVorhanden('artikelkategorien')){
      $this->app->Tpl->Set('ARTIKELKATEGORIENLINK', '');
      $this->app->Tpl->Set('GREYARTIKELKATEGORIEN','<span style="left:0;position:absolute;display:inline-block; z-index=2; width:100%;height:100%;background: rgba(236,236,236,0.5)" ></span>');
    }
    $this->app->Tpl->Set('USERVORLAGELINK', 'href="index.php?module=uservorlage&action=list"');
    if(!$this->app->erp->ModulVorhanden('uservorlage')){
      $this->app->Tpl->Set('USERVORLAGELINK', '');
      $this->app->Tpl->Set('GREYUSERVORLAGE','<span style="left:0;position:absolute;display:inline-block; z-index=2; width:100%;height:100%;background: rgba(236,236,236,0.5)" ></span>');
    }
    $this->app->Tpl->Set('KONTENLINK', 'href="index.php?module=konten&action=list"');
    if(!$this->app->erp->ModulVorhanden('konten')){
      $this->app->Tpl->Set('KONTENLINK', '');
      $this->app->Tpl->Set('GREYKONTEN','<span style="left:0;position:absolute;display:inline-block; z-index=2; width:100%;height:100%;background: rgba(236,236,236,0.5)" ></span>');
    }
    $this->app->Tpl->Set('KONTORAHMENLINK', 'href="index.php?module=kontorahmen&action=list"');
    if(!$this->app->erp->ModulVorhanden('kontorahmen')){
      $this->app->Tpl->Set('KONTORAHMENLINK', '');
      $this->app->Tpl->Set('GREYKONTORAHMEN','<span style="left:0;position:absolute;display:inline-block; z-index=2; width:100%;height:100%;background: rgba(236,236,236,0.5)" ></span>');
    }
    $this->app->Tpl->Set('LIEFERBEDINGUNGENLINK', 'href="index.php?module=lieferbedingungen&action=list"');
    if(!$this->app->erp->ModulVorhanden('lieferbedingungen')){
      $this->app->Tpl->Set('LIEFERBEDINGUNGENLINK', '');
      $this->app->Tpl->Set('GREYLIEFERBEDINGUNGEN','<span style="left:0;position:absolute;display:inline-block; z-index=2; width:100%;height:100%;background: rgba(236,236,236,0.5)" ></span>');
    }
    $this->app->Tpl->Set('ARBEITSFREIETAGELINK', 'href="index.php?module=arbeitsfreietage&action=list"');
    if(!$this->app->erp->ModulVorhanden('arbeitsfreietage')){
      $this->app->Tpl->Set('ARBEITSFREIETAGELINK', '');
      $this->app->Tpl->Set('GREYARBEITSFREIETAGE','<span style="left:0;position:absolute;display:inline-block; z-index=2; width:100%;height:100%;background: rgba(236,236,236,0.5)" ></span>');
    }
    
    
    $this->app->Tpl->Parse('TAB1',"einstellungen.tpl");
    //$this->app->Tpl->Set(TABTEXT,"Einstellungen");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  }

  function EinstellungenMenu()
  {
    $id = $this->app->Secure->GetGET("id");

    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Einstellungen</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">St&uuml;ckliste</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Verkauf</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Einkauf</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Projekte</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Lager</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Dateien</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Provisionen</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=etiketten&id=$id\">Etiketten</a>&nbsp;");
    //$this->app->Tpl->Add('TABS',"<a href=\"index.php?module=artikel&action=kosten&id=$id\">Gesamtkalkulation</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a>&nbsp;");
  }


  function EinstellungenEdit()
  {
    $this->EinstellungenMenu();
    $this->app->Tpl->Set('TABLE_ADRESSE_KONTAKTHISTORIE',"TDB");
    $this->app->Tpl->Set('TABLE_ADRESSE_ROLLEN',"TDB");

    $this->app->Tpl->Set('TABLE_ADRESSE_USTID',"TDB");
  }

  /**
   * @param string $overviewPageAction
   *
   * @return string|null
   */
  public function getOverViewLink($overviewPageAction): ?string
  {
    if(!in_array($overviewPageAction, ['', 'list'])) {
      return null;
    }

    return 'index.php?module=einstellungen&action=list';
  }


  /**
   * @param string $backlinkParameter
   *
   * @return string|null
   */
  public function getBackLink($backlinkParameter): ?string
  {
    if($backlinkParameter === 'list') {
      return 'index.php?module=einstellungen&action=list';
    }
    $appstore = $this->app->loadModule('appstore', false);
    $appList = $appstore->GetApps();

    foreach ($appList['installiert'] as $modul) {
      $cleanCategory = str_replace(array('{|', '|}'), '', $modul['kategorie']);
      if($cleanCategory === $backlinkParameter) {
        return 'index.php?module=einstellungen&action=category&category='.urlencode($backlinkParameter);
      }
    }

    return null;
  }
}
