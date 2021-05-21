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
use Xentral\Components\Barcode\BarcodeFactory;
use Xentral\Components\Http\JsonResponse;
use Xentral\Components\Http\RedirectResponse;
use Xentral\Components\Http\Request;
use Xentral\Components\Http\Session\Session;
use Xentral\Components\Http\Session\SessionHandler;
use Xentral\Components\Mailer\Data\EmailRecipient;
use Xentral\Modules\DownloadSpooler\DownloadSpoolerGateway;
use Xentral\Modules\DownloadSpooler\DownloadSpoolerService;
use Xentral\Modules\DownloadSpooler\Exception\DownloadSpoolerExceptionInterface;
use Xentral\Modules\GoogleApi\Exception\AuthorizationExpiredException;
use Xentral\Modules\GoogleApi\Exception\GoogleAccountNotFoundException;
use Xentral\Modules\GoogleApi\GoogleScope;
use Xentral\Modules\GoogleApi\Service\GoogleAccountGateway;
use Xentral\Modules\GoogleApi\Service\GoogleAccountService;
use Xentral\Modules\GoogleApi\Service\GoogleAuthorizationService;
use Xentral\Modules\GoogleApi\Service\GoogleCredentialsService;
use Xentral\Modules\GoogleCalendar\Client\GoogleCalendarClientFactory;
use Xentral\Modules\GoogleCalendar\Exception\GoogleCalendarSyncException;
use Xentral\Modules\GoogleCalendar\Service\GoogleCalendarSynchronizer;
use Xentral\Modules\SystemHealth\Service\SystemHealthService;
use Xentral\Modules\SystemMailer\SystemMailer;
use Xentral\Modules\SystemNotification\Service\NotificationService;
use Xentral\Modules\TOTPLogin\TOTPLoginService;
use Xentral\Modules\RoleSurvey\SurveyGateway;
use Xentral\Modules\RoleSurvey\SurveyService;

class Welcome
{
  /** @var erpooSystem $app */
  var $app;

  var $_meineapps;

  const MODULE_NAME = 'Welcome';

  public $javascript = [
    './classes/Modules/Calendar/www/js/fullcalendar.js',
    './classes/Modules/Calendar/www/js/calendar.js',
    './classes/Modules/Calendar/www/js/calendargroups.js',
  ];

  /**
   * Welcome constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app=$app;
    $this->_meineapps = null;
    if($intern){
      return;
    }
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("login","WelcomeLogin");
    $this->app->ActionHandler("main","WelcomeMain");
    $this->app->ActionHandler("poll","WelcomePoll");
    $this->app->ActionHandler("list","TermineList");
    $this->app->ActionHandler("cronjob","WelcomeCronjob");
    $this->app->ActionHandler("cronjob2","WelcomeCronjob2");
    $this->app->ActionHandler("adapterbox","WelcomeAdapterbox");
    $this->app->ActionHandler("help","WelcomeHelp");
    $this->app->ActionHandler("info","WelcomeInfo");
    $this->app->ActionHandler("icons","WelcomeIcons");
    $this->app->ActionHandler("vorgang","VorgangAnlegen");
    $this->app->ActionHandler("removevorgang","VorgangEntfernen");
    $this->app->ActionHandler("editvorgang","VorgangEdit");
    $this->app->ActionHandler("logout","WelcomeLogout");
    $this->app->ActionHandler("start","WelcomeStart");
    $this->app->ActionHandler("list","WelcomeStart");
    $this->app->ActionHandler("settings","WelcomeSettings");
    $this->app->ActionHandler("mobileapps","WelcomeMobileApps");
    $this->app->ActionHandler("spooler","WelcomeSpooler");
    $this->app->ActionHandler("redirect","WelcomeRedirect");
    $this->app->ActionHandler("upgrade","WelcomeUpgrade");
    $this->app->ActionHandler("upgradedb","WelcomeUpgradeDB");
    $this->app->ActionHandler("startseite","WelcomeStartseite");
    
    $this->app->ActionHandler("addnote","WelcomeAddNote");
    $this->app->ActionHandler("addpinwand","WelcomeAddPinwand");
    $this->app->ActionHandler("movenote","WelcomeMoveNote");
    $this->app->ActionHandler("oknote","WelcomeOkNote");
    $this->app->ActionHandler("delnote","WelcomeDelNote");
    $this->app->ActionHandler("pinwand","WelcomePinwand");
    
    $this->app->ActionHandler("css","WelcomeCss");
    $this->app->ActionHandler("logo","WelcomeLogo");
    $this->app->ActionHandler("unlock","WelcomeUnlock");
    $this->app->ActionHandler("direktzugriff","WelcomeDirektzugriff");
    $this->app->ActionHandler("meineapps","WelcomeMeineApps");
    $this->app->ActionHandler("passwortvergessen","Welcomepasswortvergessen");
    $this->app->ActionHandler("changelog", "WelcomeChangelog");
    $this->app->ActionHandler("survey", "WelcomeSurvey");
    $this->app->NoHooks = array('poll');

    $this->app->DefaultActionHandler("login");
    $action = $this->app->Secure->GetGET('action');
    if(
      !in_array(
        $action,
        [
          'login','logout','poll','css','logo','unlock','icons','cronjob','cronjob2',
          'addnote', 'addpinwand','movenote','oknote','delnote','adapterbox','spooler',
          'removevorgang','editvorgang','vorgang', 'survey',
        ]
      )
    ){
      $this->GetMeineApps();
      $this->app->erp->RegisterMenuHook('startseite', 'MenuHook', $this);
    }
    $this->app->ModuleScriptCache->IncludeJavascriptFiles('welcome', ['./classes/Modules/TOTPLogin/www/js/totplogin.js']);
    $this->app->ActionHandlerListen($app);
  }

  public function Install()
  {
    try {
      /** @var SurveyService $surveyService */
      $surveyService = $this->app->Container->get('SurveyService');
      $surveyService->create('xentral_role', 'welcome', 'start', true, true);
    }
    catch (Exception $e) {

    }
  }

  /**
   * @return JsonResponse
   */
  public function HandleSaveSurveyDataAjaxAction()
  {
    $surveyId = $this->app->Secure->GetPOST('surveyId');
    $surveyName = $this->app->Secure->GetPOST('surveyName');
    if(empty($surveyId)){
      /** @var SurveyGateway $surveyGateway */
      $surveyGateway = $this->app->Container->get('SurveyGateway');
      $survey = $surveyGateway->getByName($surveyName);
      if(!empty($survey)) {
        $surveyId = (int)$survey['id'];
      }
    }
    /** @var SurveyService $surveyService */
    $surveyService = $this->app->Container->get('SurveyService');
    try{
      $surveyUserId = $surveyService->saveUserAnswer(
        $surveyId,
        $this->app->User->GetID(),
        $this->app->Secure->POST
      );
      try{
        $surveyService->sendToXentral(
          $surveyId,
          "https://{$this->app->Conf->updateHost}/sendsurvey.php",
          $this->app->erp->Firmendaten('lizenz'),
          $this->app->erp->Firmendaten('schluessel')
        );
      }
      catch (Exception $e) {

      }
    }
    catch(Exception $e) {
      return new JsonResponse(
        ['sucess' => false, 'error' =>$e->getMessage()],
        JsonResponse::HTTP_BAD_REQUEST
      );
    }
    $response = ['success' => true, 'surveyUserId' => $surveyUserId];
    $this->app->erp->RunHook('welcome_surveysave', 3, $surveyId, $surveyUserId, $response);

    return new JsonResponse($response);
  }

  /**
   * @return JsonResponse
   */
  public function HandleOpenSurveyAjaxAction()
  {
    $surveyId = $this->app->Secure->GetPOST('surveyId');
    $surveyName = $this->app->Secure->GetPOST('surveyName');
    /** @var SurveyGateway $surveyGateway */
    $surveyGateway = $this->app->Container->get('SurveyGateway');
    if(empty($surveyId)) {
      $survey = $surveyGateway->getByName($surveyName);
      if(!empty($survey)) {
        $surveyId = (int)$survey['id'];
      }
    }
    else{
      $survey = $surveyGateway->getById($surveyId);
    }
    if(empty($survey)) {
      return new JsonResponse(
        ['sucess' => false, 'error' => 'Umfrage nicht gefunden'],
        JsonResponse::HTTP_BAD_REQUEST
      );
    }

    $filled = $surveyGateway->getFilledSurveyByUser($surveyId, $this->app->User->GetID());
    if(!empty($filled)) {
      $filled = json_decode($filled, true);
    }

    return new JsonResponse(['success' => true, 'survey' => $survey, 'data' => $filled]);
  }

  public function WelcomeSurvey()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'saveSurveyData') {
      return $this->HandleSaveSurveyDataAjaxAction();
    }
    if($cmd === 'openSurvey') {
      return $this->HandleOpenSurveyAjaxAction();
    }
  }

  public function Welcomepasswortvergessen()
  {
    $this->app->acl->Passwortvergessen();
  }
 
  public function MenuHook()
  {
    if($this->GetMeineApps())
    {
      $this->app->erp->InsertMenuAfter('index.php?module=welcome&action=meineapps','Meine Apps','welcome','start');
    }
  }

  public function StartseiteMenu()
  {
    $module = $this->app->Secure->GetGET('module');

    $this->app->erp->MenuEintrag('index.php?module=welcome&action=start','Startseite');
    $this->app->erp->MenuEintrag('index.php?module=welcome&action=pinwand','Pinnwand');


    if($module==='aufgaben'){
      $this->app->erp->MenuEintrag('index.php?module=aufgaben&action=create', 'Neue Aufgaben');
    }

    $this->app->erp->MenuEintrag('index.php?module=aufgaben&action=list','Aufgaben');

    $this->app->erp->MenuEintrag('index.php?module=kalender&action=list','Kalender');
    $this->app->erp->MenuEintrag('index.php?module=chat&action=list','Chat');
    $this->app->erp->MenuEintrag('index.php?module=zeiterfassung&action=create','Zeiterfassung buchen');
    $this->app->erp->MenuEintrag('index.php?module=zeiterfassung&action=listuser','Eigene Zeiterfassung &Uuml;bersicht');
    if($this->app->Secure->GetGET('action') ==='changelog'){
      $this->app->erp->MenuEintrag('index.php?module=welcome&action=changelog', 'Changelog');
    }
    $this->app->erp->RunMenuHook('startseite');
  }

  /**
   * @param string $bezeichnung
   * @param string $url
   */
  public function CheckFav($bezeichnung, $url)
  {
    if($this->app->erp->GetKonfiguration('checkFav'. md5($url))){
      return;
    }
    $this->app->erp->SetKonfigurationValue('checkFav'. md5($url),1);
    $user = $this->app->DB->SelectArr("SELECT u.id FROM `user` u LEFT JOIN `userrights` ur ON u.id = ur.`user` AND ur.module = 'amazon' AND ur.action = 'list' WHERE NOT isnull(ur.id) OR u.type = 'admin'");
    if(empty($user))
    {
      return;
    }
    foreach($user as $vu)
    {
      $u = $vu['id'];
      $eigenlinks = $this->app->DB->Select("SELECT uk.`value` FROM `userkonfiguration` uk WHERE `name` = 'welcome_links_eigen' AND `user` = '$u'  LIMIT 1");
      $index = 1;
      $check2 = null;
      $check3 = null;
      if($eigenlinks)
      {
        for($i = 1; $i <= 8; $i++)
        {
          $link = $this->app->DB->SelectRow("SELECT uk.`value`, uk.id FROM `userkonfiguration` uk WHERE `name` = 'welcome_linklink".$i."' AND `user` = '$u'  LIMIT 1");
          if(empty($link)) {
            $link = array('id'=>0, 'link'=>'');
          }
          if(stripos($link['value'], $url) !== false)
          {
            $index = 9;
            break;
          }
          if($link['value'] != '')
          {
            if($index == $i)$index++;
          }else{
            $check2 = $link['id'];
            $check3 = $this->app->DB->Select("SELECT uk.id FROM `userkonfiguration` uk WHERE `name` = 'welcome_linkname".$i."' AND `user` = '$u'  LIMIT 1");
            break;
          }
        }
      }else{
        $check = $this->app->DB->Select("SELECT id FROM `userkonfiguration` uk WHERE `name` = 'welcome_links_eigen' AND `user` = '$u'  LIMIT 1");
        if($check)
        {
          $this->app->DB->Update("UPDATE `userkonfiguration` SET `value` = '1' WHERE id = '$check' LIMIT 1");
        }else{
          $this->app->DB->Insert("INSERT INTO `userkonfiguration` (`user`, `value`, `name`) VALUES ('$u', '1', 'welcome_links_eigen')");
        }
        if(!$this->app->DB->Select("SELECT id FROM `userkonfiguration` WHERE `user` = '$u' AND `name` LIKE 'welcome\_linklink_%'"))
        {
          $index = 2;
          $this->app->DB->Insert("INSERT INTO `userkonfiguration` (`user`, `value`, `name`) VALUES ('$u', 'index.php?module=welcome&action=settings', 'welcome_linklink1')");
          $this->app->DB->Insert("INSERT INTO `userkonfiguration` (`user`, `value`, `name`) VALUES ('$u', 'Eigene Einstellungen', 'welcome_linkname1')");
        }
      }
      if($index <= 8)
      {
        if($check2)
        {
          $this->app->DB->Update("UPDATE `userkonfiguration` SET `value` = '".$this->app->DB->real_escape_string($url)."' WHERE id = '$check2' LIMIT 1");
        }else
        {
          $this->app->DB->Insert("INSERT INTO `userkonfiguration` (`user`, `value`, `name`) VALUES ('$u', '".$this->app->DB->real_escape_string($url)."', 'welcome_linklink".$index."')");
        }
        if($check3)
        {
          $this->app->DB->Update("UPDATE `userkonfiguration` SET `value` = '".$this->app->DB->real_escape_string($bezeichnung)."' WHERE id = '$check3' LIMIT 1");
        }else{
          $this->app->DB->Insert("INSERT INTO `userkonfiguration` (`user`, `value`, `name`) VALUES ('$u', '".$this->app->DB->real_escape_string($bezeichnung)."', 'welcome_linkname".$index."')");
        }
      }
    }
  }

  /**
   * @return bool|int
   */
  public function CheckRights()
  {
    if($this->app->User->GetType()==='admin'){
      return true;
    }
    $action = $this->app->Secure->GetGET('action');
    if($action !== 'meineapps') {
      return true;
    }
    if(!$this->app->erp->RechteVorhanden('welcome','start')){
      return false;
    }

    return $this->GetMeineApps();
  }

  /**
   * @return bool|int
   */
  protected function GetMeineApps()
  {
    if(is_array($this->_meineapps)) {
      return count($this->_meineapps);
    }

    $anz = 0;
    /** @var Appstore $appstore */
    $appstore = $this->app->loadModule('appstore');
    $modulliste = $appstore->getAppsList();
    $modulliste = $appstore->markGetAppsWithUserRights($modulliste);

    if(!empty($modulliste['installiert'])) {
      foreach($modulliste['installiert'] as $module)
      {
        if(!empty($module['my_app'])) {
          $this->_meineapps[] = $module;
          $anz++;
        }
      }
    }
    if($anz) {
      return $anz;
    }

    return false;
  }

  public function WelcomeMeineApps()
  {
    $this->app->User->SetParameter('appstore_filter', 'userapps');
    $this->app->Location->execute('index.php?module=appstore&action=list');

    // Suchebegriff wurde eingegeben
    if ($this->app->Secure->GetGET('cmd') === 'suche') {
      return $this->GetMeineAppsSuchergebnisse();
    }

    $this->app->erp->StartseiteMenu();
    if($this->_meineapps) {
      $cmeineapps = !empty($this->_meineapps)?count($this->_meineapps):0;
      for($i = 0; $i < $cmeineapps; $i++)
      {
        $modul = $this->_meineapps[$i];
        $modul['IconUrl'] = './themes/'.$this->app->Conf->WFconf['defaulttheme'].'/images/einstellungen/'.$modul['Icon'];
        if (empty($modul['key'])){
          $modul['key'] = md5($modul['Bezeichnung']);
        }
        $appstore = $this->app->erp->LoadModul('appstore');
        $iconTag = $appstore->GetAppIconTagByCategory($modul['kategorie']);

        $modulHtml = sprintf(
          '<div class="app" id="%s">'.
          '<a href="%s"><div class="icon app-category-icon-%s" alt="%s" border="0"></div><span>%s</span></a>'.
          '</div>',
          'modul-'.$modul['key'],
          $modul['Link'],
          $iconTag,
          $modul['Bezeichnung'],
          $modul['Bezeichnung']
        );
        $this->app->Tpl->Add('APPLIST', $modulHtml);
      }
    }
    
    $this->app->Tpl->Parse('PAGE','welcome_meineapps.tpl');
  }

  /**
   * @return JsonResponse
   */
  public function GetMeineAppsSuchergebnisse(): JsonResponse
  {
    $suchbegriff = $this->app->Secure->GetPOST('val');
    $modulliste = $this->_meineapps;

    $anzeigen = [];
    $ausblenden = [];
    $moduleGefunden = 0;
    /** @var Appstore $appStore */
    $appStore = $this->app->loadModule('appstore');

    foreach ($modulliste as $modul) {
      if (empty($modul['key'])){
        $modul['key'] = md5($modul['Bezeichnung']);
      }
      $appId = 'modul-'.$modul['key'];
      if (empty($suchbegriff) || $appStore->match($modul['Bezeichnung'], $suchbegriff)) {
        $anzeigen[$appId] = true;
        $moduleGefunden++;
      } else {
        $ausblenden[$appId] = true;
      }
    }

    return new JsonResponse(['anzeigen'=>$anzeigen,'ausblenden'=>$ausblenden,'gefunden'=>$moduleGefunden]);
  }

  
  public function WelcomePoll()
  {
    //$saction = $this->app->Secure->GetGET("saction"); //Edit Bruno 14.12.17 wird nicht verwendet
    if(!empty($this->app->User) && method_exists($this->app->User, 'GetID') && !$this->app->User->GetID()) {
      echo json_encode(array(array('event'=>'logout')));
      exit;
    }
    $sid = (int)$this->app->Secure->GetGET('sid');
    $noTimeoutUserEdit = $this->app->Secure->GetGET('nousertimeout');

    if($sid > 0 && empty($noTimeoutUserEdit)) {
      $user = $this->app->Secure->GetGET('user'); //Edit Bruno 14.12.17 reingezogen
      $smodule = $this->app->Secure->GetGET('smodule');
      $this->app->erp->TimeoutUseredit($smodule,$sid,$user);
    }
   
    $inv = false;
    $invisible = (String)$this->app->Secure->GetPOST('invisible');
    if($invisible == '1'){
      $inv = true;
    }
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd==='messages') {
      $result = $this->app->erp->UserEvent($inv);
      if(!empty($result) && is_array($result)) {
        echo json_encode($result);
        $this->app->ExitXentral();
      }
      echo json_encode([]);
    }

    $this->app->ExitXentral();
  }	


  function WelcomeDirektzugriff()
  {
    $direktzugriff = $this->app->Secure->GetPOST('direktzugriff');


    switch($direktzugriff)
    {
      case "1": $link="index.php?module=adresse&action=list"; break;
      case "11": $link="index.php?module=adresse&action=list"; break;
      case "12": $link="index.php?module=artikel&action=list"; break;
      case "13": $link="index.php?module=projekt&action=list"; break;

      case "2": $link="index.php?module=angebot&action=list"; break;
      case "21": $link="index.php?module=anfrage&action=list"; break;
      case "22": $link="index.php?module=angebot&action=list"; break;
      case "23": $link="index.php?module=auftrag&action=list"; break;

      case "3": $link="index.php?module=bestellung&action=list"; break;
      case "31": $link="index.php?module=bestellung&action=list"; break;
      case "32": $link="index.php?module=lager&action=ausgehend"; break;
      case "33": $link="index.php?module=produktion&action=list"; break;

      case "5": $link="index.php?module=rechnung&action=list"; break;

      case "8": $link="index.php?module=lieferschein&action=list"; break;
      case "81": $link="index.php?module=lieferschein&action=list"; break;
      case "82": $link="index.php?module=lager&action=list"; break;
      case "84": $link="index.php?module=versanderzeugen&action=offene"; break;
      default: $link="index.php";
    }

    header('Location: '.$link);
    exit;
  }

  function WelcomeAdapterbox()
  {
    $anzahl = null;
    $ip = $this->app->Secure->GetGET('ip');
    $serial = $this->app->Secure->GetGET('serial');
    $device = $this->app->Secure->GetGET('device');
    if(is_numeric($ip)){
      $ip = long2ip($ip);
    }
    else {
      $ip='';
    }

    echo 'OK';
    $this->app->DB->Delete("DELETE FROM `adapterbox_log` WHERE `ip`='$ip' OR `seriennummer`='$serial'");
    $this->app->DB->Insert("INSERT INTO `adapterbox_log` (`id`,`datum`,`ip`,`meldung`,`seriennummer`,`device`)
        VALUES ('',NOW(),'$ip','Adapterbox connected ($device)','$serial','device')");

    // check if there is an adapterbox

    if($device==='zebra'
      && ($this->app->DB->Select("SELECT COUNT(`id`) FROM `drucker` WHERE `art`=2 AND `anbindung`='adapterbox'")
      ) <= 0 ) {
      $this->app->DB->Insert(
        "INSERT INTO `drucker` (`id`,`art`,`anbindung`,`adapterboxseriennummer`,`bezeichnung`,`name`,`aktiv`,`firma`)
          VALUES ('','2','adapterbox','$serial','Zebra','Etikettendrucker',1,1)"
      );
      $tmpid = $this->app->DB->GetInsertID();

      $this->app->erp->FirmendatenSet("standardetikettendrucker",$tmpid);
    }

    $xml ='
      <label>
      <line x="3" y="3" size="4">Step 2 of 2</line>
      <line x="3" y="8" size="4">Connection establish</line>
      <line x="3" y="13" size="4">Server: '.$_SERVER['SERVER_ADDR'].'</line>
      </label>
      ';

    if($this->app->erp->Firmendaten('deviceenable')=='1')
    {
      $job = base64_encode(json_encode(array('label'=>base64_encode($xml),'amount'=>$anzahl)));//."<amount>".$anzahl."</amount>");
      $this->app->DB->Insert(
        "INSERT INTO `device_jobs` (`id`,`zeitstempel`,`deviceidsource`,`deviceiddest`,`job`,`art`) 
        VALUES ('',NOW(),'000000000','$serial','$job','labelprinter')"
      );
    }	


    // update ip
    if($ip!=''){
      $this->app->DB->Update("UPDATE `drucker` SET `adapterboxip`='$ip' WHERE `adapterboxseriennummer`='$serial' LIMIT 1");
    }

    $this->app->erp->ExitWawi();
  }	




  function WelcomeCronjob()
  {
    @ignore_user_abort(true);
    include dirname(dirname(__DIR__)).'/cronjobs/starter.php';
    exit;
  }

  function WelcomeCronjob2()
  {
    @ignore_user_abort(true);
    include dirname(dirname(__DIR__)).'/cronjobs/starter2.php';
    exit;
  }

  public function WelcomeStart()
  {
    $addtionalcspheader = ' '.str_replace([';','"'],'',$this->app->erp->Firmendaten('additionalcspheader')).' ';
    $this->app->Tpl->Add('ADDITIONALCSPHEADER', $addtionalcspheader);

    if($this->app->erp->UserDevice()==='smartphone')
    {
      $this->WelcomeStartSmartphone();
    } else {
      $this->WelcomeStartDesktop();
    }
  }

  function WelcomeStartSmartphone()
  {
    header('Location: index.php?module=mobile&action=list');
    exit;
  }

  public function WelcomeStartDesktop()
  {
    $tpl = '';
    if($this->app->Secure->GetPOST('addfav')) {
      $link = $this->app->Secure->GetPOST('link');
      if(substr($link, -4) === '&id=') {
        $link = substr($link, 0, -4);
      }
      if(strpos($link,'action=delete') !== false) {
        $link = '';
      }
      $title = $this->app->Secure->GetPOST('title');
      $newlink = $this->app->Secure->GetPOST('newlink');
      $success = 0;
      if(!empty($link)) {
        for ($i = 1; $i <= 8; $i++) {
          $linkAct = (String)$this->app->User->GetParameter('welcome_linklink' . $i);
          if($linkAct === '' || $linkAct === $link){
            $success = 1;
            $this->app->User->SetParameter('welcome_links_eigen', 1);
            $this->app->User->SetParameter('welcome_linklink' . $i, $link);
            $this->app->User->SetParameter('welcome_linkname' . $i, $title);
            $this->app->User->SetParameter('welcome_linkintern' . $i, $newlink ? 0 : 1);
            break;
          }
        }
      }
      header('Content-Type: application/json');
      echo json_encode(['success' => $success]);
      $this->app->ExitXentral();
    }

    if($this->app->Secure->GetPOST('savelinks'))
    {
      $this->app->User->SetParameter('welcome_links_eigen',1);
      for($i = 1; $i <= 8; $i++)
      {
        $this->app->User->SetParameter('welcome_linklink'.$i, $this->app->Secure->GetPOST('linklink'.$i));
        $this->app->User->SetParameter('welcome_linkname'.$i, $this->app->Secure->GetPOST('linkname'.$i));
        $this->app->User->SetParameter('welcome_linkintern'.$i, $this->app->Secure->GetPOST('linkintern'.$i));
      }
    }
    $eigenlinks = $this->app->User->GetParameter('welcome_links_eigen');
    
    if($eigenlinks)
    {
      for($i = 1; $i <= 8; $i++)
      {
        $links[] = array('name'=>$this->app->User->GetParameter('welcome_linkname'.$i),'link'=>$this->app->User->GetParameter('welcome_linklink'.$i),'intern'=>$this->app->User->GetParameter('welcome_linkintern'.$i));
      }       
      
    }else{
      $links = array(
        array('name'=>'Eigene Einstellungen','link'=>'index.php?module=welcome&action=settings'),
        array('name'=>'','link'=>'','intern'=>''),
        array('name'=>'','link'=>'','intern'=>''),
        array('name'=>'','link'=>'','intern'=>''),
        array('name'=>'','link'=>'','intern'=>''),
        array('name'=>'','link'=>'','intern'=>''),
        array('name'=>'','link'=>'','intern'=>''),
        array('name'=>'','link'=>'','intern'=>'')
      );
    }
    if($links)
    {
      foreach($links as $k => $link)
      {
        $this->app->Tpl->Set('LINKNAME'.($k+1),$link['name']);
        $this->app->Tpl->Set('LINKLINK'.($k+1),$link['link']);
        if(isset($link['intern']) && $link['intern']){
          $this->app->Tpl->Set('LINKINTERN'.($k+1),' checked="checked" ');
        }
        if($link['link'] && $link['name'])
        {
          $this->app->Tpl->Add('LINKS','<a href="'.$link['link'].'" '.(isset($link['intern']) && $link['intern']?'':'target="_blank"').' class="button button-primary">'.$link['name'].'</a>&nbsp;');
        }
      }
    }

    $this->app->erp->Headlines('Ihre Startseite');
    $this->app->Tpl->Set('KURZUEBERSCHRIFT2','[BENUTZER]');
    $this->app->erp->StartseiteMenu();

    $this->app->Tpl->Set('TABTEXT','Ihre Startseite');

    $module = $this->app->Secure->GetGET('module');


    //fenster rechts offene vorgaenge ***
    $this->app->Tpl->Set('SUBSUBHEADING','Vorg&auml;nge');
    $arrVorgaenge = $this->app->DB->SelectArr("SELECT * FROM offenevorgaenge WHERE adresse='{$this->app->User->GetAdresse()}' ORDER by id DESC");
    $this->app->Tpl->Set('INHALT','');
    $carrVorgaenge = !empty($arrVorgaenge)?count($arrVorgaenge):0;
    for($i=0;$i<$carrVorgaenge;$i++)
    {

      $this->app->Tpl->Add('VORGAENGE',"<tr><td>".substr(ucfirst($arrVorgaenge[$i]['titel']),0,100)."</td><td align=\"right\"><img src=\"./themes/[THEME]/images/1x1t.gif\" width=\"7\" border=\"0\" align=\"right\">
          <a href=\"index.php?".$arrVorgaenge[$i]['href']."\"><img src=\"./themes/[THEME]/images/right.png\" border=\"0\" align=\"right\" title=\"Erledigen\"></a>&nbsp;
          <a href=\"index.php?module=welcome&action=removevorgang&vorgang={$arrVorgaenge[$i]['id']}\"><img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\" align=\"right\" title=\"Erledigt\"></a>&nbsp;
          <img src=\"./themes/[THEME]/images/1x1t.gif\" width=\"3\" border=\"0\" align=\"right\">
          <a href=\"javascript: var ergebnistext=prompt('Offenen Vorgang umbenennen:','".ucfirst($arrVorgaenge[$i]['titel'])."'); if(ergebnistext!='' && ergebnistext!=null) window.location.href='index.php?module=welcome&action=editvorgang&vorgang={$arrVorgaenge[$i]['id']}&titel='+ergebnistext;\"><img src=\"./themes/[THEME]/images/edit.svg\" alt=\"Bearbeiten\" title=\"Bearbeiten\" border=\"0\" align=\"right\"></a></td></tr>");

    }

    $this->app->Tpl->Set('CALENDAR_DAYNAMES','["{|Sonntag|}", "{|Montag|}", "{|Dienstag|}", "{|Mittwoch|}",
        "{|Donnerstag|}", "{|Freitag|}", "{|Samstag|}"]');
    $this->app->Tpl->Set('CALENDAR_MONTHNAMES','["{|Januar|}", "{|Februar|}", "{|März|}", "{|April|}", "{|Mai|}",
        "{|Juni|}", "{|Juli|}", "{|August|}", "{|September|}", "{|Oktober|}", "{|November|}", "{|Dezember|}"]');
    $this->app->Tpl->Set('CALENDAR_TODAY','{|Heute|}');
    $this->app->Tpl->Set('CALENDAR_MONTH','{|Monat|}');
    $this->app->Tpl->Set('CALENDAR_WEEK','{|Woche|}');
    $this->app->Tpl->Set('CALENDAR_DAY','{|Tag|}');
    $this->app->erp->KalenderList('KALENDER');

    $this->app->Tpl->Parse('STARTSEITE','lesezeichen.tpl');

    if($this->app->User->GetType()==='admin')
    {
      $this->app->Tpl->Set('UMSATZ','<h1 onmouseover="document.getElementById(\'umsatzwoche\').style.display=\'block\';" onmouseout="document.getElementById(\'umsatzwoche\').style.display=\'none\';">Umsatz ab Montag</h1>
          <div style="margin:5px;display:none" id="umsatzwoche"><table width="90%">
          '.$tpl.'
          </table>
          </div>
          <br>');
    }

    if($this->app->erp->RechteVorhanden('kalender','list'))
    {
      $this->app->Tpl->Set('TERMINE', $this->Termine($this->app->DB->Select('SELECT CURDATE();')));
      $this->app->Tpl->Set('TERMINEMORGEN', $this->Termine($this->app->DB->Select('SELECT DATE_ADD(CURDATE(), INTERVAL 1 DAY);')));
    }

    $summestunden = $this->app->DB->Select("SELECT SUM((UNIX_TIMESTAMP(z.bis)-UNIX_TIMESTAMP(z.von))/3600.0) as stunden
      FROM zeiterfassung z WHERE z.abrechnen='1' AND z.ist_abgerechnet IS NULL OR z.ist_abgerechnet='0' AND z.adresse_abrechnung > 0");

    if($summestunden > 0)
      $this->app->Tpl->Add('DRINGEND','<li>'.number_format($summestunden,2,',','.').' Stunden nicht abgerechnet (<a href="index.php?module=zeiterfassung&action=abrechnenpdf">PDF</a>)</li>');


    $this->app->Tpl->Set('USERNAME',$this->app->User->GetName());

    $tmp = $this->app->DB->SelectArr("SELECT * FROM aufgabe WHERE (adresse='".$this->app->User->GetAdresse()."' OR (initiator='".$this->app->User->GetAdresse()."' AND adresse<=0)) AND startseite='1' AND (status='offen' or status='') AND ((intervall_tage > 0 AND abgabe_bis <=NOW()) OR intervall_tage <=0) ORDER by prio DESC");
    //TODOFORUSER
    $ctmp = !empty($tmp)?count($tmp):0;
    for($i=0;$i<$ctmp;$i++)
    {
      $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='".$tmp[$i]['initiator']."' LIMIT 1");
      $high="";
      if($tmp[$i]['initiator']!=$tmp[$i]['adresse']) {
        $additional = "<br><font style=\"font-size:8pt\">von ".$name."</font>";
      } else {
        $additional='';
      }


      if($tmp[$i]['prio']=='1') {
        $class='noteit_highprio';
        $high="&nbsp;(<font color=red><strong>Prio</strong></font>)";
      }
      else {
        $class='noteit';
      }



$this->app->Tpl->Add('TODOFORUSER',"<tr><td width=\"90%\">".$tmp[$i]['aufgabe'].$additional.$high."</td><td width=\"10%\"><span style=\"cursor:pointer\" onclick=\"if(!confirm('Wirklich ".$tmp[$i]['aufgabe']." bearbeiten?')) return false; else AufgabenEdit(".$tmp[$i]['id'].");\"><img src=\"./themes/new/images/edit.svg\"></span>
          <span style=\"cursor:pointer\" onclick=\"if(!confirm('Wirklich ".$tmp[$i]['aufgabe']." abschließen?')) return false; else window.location.href='index.php?module=aufgaben&action=abschluss&id=".$tmp[$i]['id']."&referrer=1';\"><img src=\"./themes/new/images/forward.svg\"></span></td></tr>");

    }

    if($i<=0){
      $this->app->Tpl->Add('TODOFORUSER', '<tr><td><center><i>{|Keine Aufgaben f&uuml;r die Startseite|}</i></center></td></tr>');
    }

    $tmp = $this->app->DB->SelectArr("SELECT * FROM aufgabe WHERE initiator='".$this->app->User->GetAdresse()."' AND adresse!='".$this->app->User->GetAdresse()."' AND adresse > 0 AND startseite='1' AND status='offen' AND ((intervall_tage > 0 AND abgabe_bis <=NOW()) OR intervall_tage <=0) ORDER by prio DESC");
    $ctmp = !empty($tmp)?count($tmp):0;

    if($ctmp > 0){
      $this->app->Tpl->Add('TODOFORMITARBEITER', '<h4>&nbsp;Vergebene Aufgaben:</h4>');
    }


    for($i=0;$i<$ctmp;$i++)
    {
      $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='".$tmp[$i]['adresse']."' LIMIT 1");
      $high='';
      if($tmp[$i]['prio']=='1') {
        $class='noteit_highprio';
        $high='&nbsp;(Prio)';
      }
      else {
        $class='noteit';
      }


      $this->app->Tpl->Add('TODOFORMITARBEITER','<tr><td>'.$tmp[$i]['aufgabe']."$high<br><font style=\"font-size:8pt\">f&uuml;r&nbsp;".$name.'</font></td></tr>');
    }

    $this->app->Tpl->Set('ACCORDION', $this->Accordion());

    $ctx = stream_context_create(array(
    'http' => array(
        'timeout' => 1
        )
    )
    ); 

    $result_news = file_get_contents("https://{$this->app->Conf->updateHost}/news.php?version=".$this->app->erp->Version(),0, $ctx);

    if($result_news=='')
    {
        $result_news = '
                    <div class="content">
                    <span class="headline">Die neue Version 20.3 ist nun verfügbar und gespickt mit dutzenden neuen Features!</span>
                    <p>Im Xentral Community Forum könnt Ihr euch einen Überblick über die neuen Funktionen verschaffen.</p>
                    <a href="//community.xentral.com/hc/de/articles/360017815440-Top-Features-ab-Version-20-3" target="_blank" class="to-nues">Neues von Xentral</a>
                    </div>';
    }


    $this->app->Tpl->Set('EXTERNALNEWS', $result_news);

    $result_handbook =  file_get_contents('https://xentral.biz/handbook.php?version='.$this->app->erp->Version(),0, $ctx);
    if($result_handbook=='')
    {
      $result_handbook='<ul>
<li><a href="https://xentral.biz/helpdesk/kurzanleitung-einrichtung-faq-leitfaden" target="_blank">Erste Schritte / Einstieg</a></li>
<li><a style="font-weight:normal" href="https://xentral.biz/helpdesk/kurzanleitung-import-von-stammdaten" target="_blank">Import von Stammdaten</a> <a href="https://xentral.biz/helpdesk/kurzanleitung-import-von-stammdaten" target="_blank">mehr</a></li>
<li><a style="font-weight:normal" href="https://xentral.biz/helpdesk/kurzanleitung-import-von-stammdaten" target="_blank">Automatisches Backup einrichten</a> <a href="https://xentral.biz/helpdesk/backup" target="_blank">mehr</a></li>
<li><a href="https://xentral.biz/akademie-home" target="_blank">Link zu Online-Handbuch</a></li>
<li><a href="https://forum.xentral.biz/" target="_blank">Forum f&uuml;r Open-Source Version</a></li><li><a href="https://xentral.biz/versionen#tab-versionen-tab4" target="_blank">Supportvertr&auml;ge f&uuml;r Xentral</a></li></ul>';
    }

    $this->app->Tpl->Set('EXTERNALHANDBOOK', $result_handbook);

    // Aufgabe-Bearbeiten-Popup
    $pinnwaende = $this->app->erp->GetPinwandSelect();
    $pinnwand = "";
    foreach($pinnwaende as $key=>$value){
      $pinnwand .= "<option value='$key'>".$value."</option>";
    }
    $this->app->Tpl->Set("PINNWAND", $pinnwand);
    $this->app->YUI->CkEditor("e_notizen","belege",array("width"=>"625"));
    $this->app->YUI->CkEditor("e_beschreibung","belege",array("width"=>"420"));
    $this->app->YUI->DatePicker("e_datum");
    $this->app->YUI->TimePicker("e_zeit");
    $this->app->Tpl->Parse('AUFGABENPOPUP','aufgaben_popup.tpl');
    // ENDE:Aufgabe-Bearbeiten-Popup

    $this->XentralUpgradeFeed();

    $this->app->erp->RunHook('welcome_start', 1 , $this);

    if(empty($this->app->User->GetField('role')) || $this->app->acl->IsAdminadmin()) {
      $this->app->ModuleScriptCache->IncludeWidgetNew('ClickByClickAssistant');
      $this->app->ModuleScriptCache->IncludeJavascriptFiles(
        'welcome',
        [
          'body' => [
            './classes/Modules/Welcome/www/js/welcome_firststart.js',
            ],
        ]
      );
      $this->app->Tpl->Parse('AUFGABENPOPUP', 'welcome_firststart.tpl');
    }
    if($this->app->User->GetType() === 'admin') {
      $this->app->Tpl->Set('COLROWTASKS', '6');
    }
    else {
      $this->app->Tpl->Set('COLROWTASKS', '12');
      $this->app->Tpl->Set('BEFORELEARNINGDASHBOARDTILE', '<!--');
      $this->app->Tpl->Set('AFTERLEARNINGDASHBOARDTILE', '-->');
    }

    $this->checkFreeSpace();

    $this->app->Tpl->Parse('PAGE','startseite.tpl');
  }

  protected function checkFreeSpace(){
    /** @var SystemHealthService $service */
    $systemHealthService = $this->app->Container->get('SystemHealthService');
    try{
      $freeDiskSpace = $systemHealthService->getDiskFree('');

      if($freeDiskSpace === false){
        return;
      }

      $freeDiskSpaceInMegabyte = $freeDiskSpace / (1024 * 1024);
      if($freeDiskSpaceInMegabyte < 512){
        $this->app->Tpl->Set('MESSAGE', '<div class="error">{|Weniger als 500 MB Speicherplatz frei.|}</div>');
      }
      elseif($freeDiskSpaceInMegabyte < 2048){
        $this->app->Tpl->Set('MESSAGE', '<div class="warning">{|Weniger als 2 GB Speicherplatz frei.|}</div>');
      }

    } catch(Exception $e){
      $this->app->erp->LogFile('can not evaluate disk space: ' . $e->getMessage());
    }
  }


  public function WelcomeIcons()
  {
    $type = $this->app->Secure->GetGET('type');
    header('Content-type: image/svg+xml');

    switch($type)
    {
      case 'artikelgruppe.svg':
        $xml = file_get_contents('./images/icons/artikelgruppe.svg');
        break;
    }

    $farbe1 = $this->app->erp->Firmendaten('firmenfarbeganzdunkel');

    if($farbe1 ==''){
      $farbe1 = '#26727a';//$farbe1 = "rgb(7, 134, 153)";
    }

    $farbe2 = '#e43f25'; // rot im artikel
    $farbe3 = '#a6e0be'; // hell tyrkis im artikel kreis
    $farbe4 = '#449cbe'; // dunkelblau im artikel rechteck 

    $xml = str_replace('#3fb9cd',$farbe1,$xml);
    $xml = str_replace('#e43f25',$farbe2,$xml);
    $xml = str_replace('#a6e0be',$farbe3,$xml);
    $xml = str_replace('#449cbe',$farbe4,$xml);

    echo $xml;
    $this->app->erp->ExitWawi();
  }

  public function WelcomeLogo()
  {
    $firmenlogo = $this->app->erp->getSettingsFile('firmenlogo');
    if($firmenlogo!='')
    {
      header('Content-Type: image/png');
      echo $firmenlogo;
    }
    $this->app->erp->ExitWawi();
  }


  public function WelcomeCss()
  {
    $file = $this->app->Secure->GetGET('file');

    if ($this->app->erp->UserDevice()!=='smartphone') {
      if($file==='style.css'){
        $tmp = file_get_contents('./themes/new/css/style.css');
      }


      if($file==='popup.css'){
        $tmp = file_get_contents('./themes/new/css/popup.css');
      }


      if($file==='grid.css'){
        $tmp = file_get_contents('./themes/new/css/grid.css');
      }
    }	
    $tmpfirmendatenfkt = 'Firmendaten';
    if(method_exists($this->app->erp,'TplFirmendaten')){
      $tmpfirmendatenfkt = 'TplFirmendaten';
    }

    $firmenfarbehell = $this->app->erp->$tmpfirmendatenfkt('firmenfarbehell');
    if($firmenfarbehell ==''){
      $firmenfarbehell = '#c2e3ea';//rgb(67, 187, 209)"; //ALT
    }

    $firmenfarbedunkel = $this->app->erp->$tmpfirmendatenfkt('firmenfarbedunkel');
    if($firmenfarbedunkel ==''){
      $firmenfarbedunkel = '#53bed0';//rgb(2, 125, 141)"; //ALT
    }

    $firmenfarbeganzdunkel = $this->app->erp->$tmpfirmendatenfkt('firmenfarbeganzdunkel');
    if($firmenfarbeganzdunkel ==''){
      $firmenfarbeganzdunkel = '#018fa3';
    }

    $navigationfarbe = $this->app->erp->$tmpfirmendatenfkt('navigationfarbe'); //ALT
    if($navigationfarbe ==''){
      $navigationfarbe = '#48494b';
    }

    $navigationfarbeschrift = $this->app->erp->$tmpfirmendatenfkt('navigationfarbeschrift');
    if($navigationfarbeschrift ==''){
      $navigationfarbeschrift = '#c9c9cb';
    }

    $navigationfarbe2 = $this->app->erp->$tmpfirmendatenfkt('navigationfarbe2'); //ALT
    if($navigationfarbe2 ==''){
      $navigationfarbe2 = $navigationfarbeschrift;
    }
    
    $navigationfarbeschrift2 = $this->app->erp->$tmpfirmendatenfkt('navigationfarbeschrift2');
    if($navigationfarbeschrift2 ==''){
      $navigationfarbeschrift2 = $navigationfarbe;
    }
    
    $unternavigationfarbe = $this->app->erp->$tmpfirmendatenfkt('unternavigationfarbe');
    if($unternavigationfarbe ==''){
      $unternavigationfarbe = '#d5ecf2';
    }

    $unternavigationfarbeschrift = $this->app->erp->$tmpfirmendatenfkt('unternavigationfarbeschrift');
    if($unternavigationfarbeschrift ==''){
      $unternavigationfarbeschrift = '#027d8d';
    }


    $firmenfarbe = $this->app->erp->$tmpfirmendatenfkt('firmenfarbe');
    if($firmenfarbe ==''){
      $firmenfarbe = '#48494b';
    }

    $navigationfarbeschrift2 = $this->app->erp->$tmpfirmendatenfkt('navigationfarbeschrift2');
    if($navigationfarbeschrift2 ==''){
      $navigationfarbeschrift2 = '#ffffff';
    }
  
    $tmp = str_replace('[TPLSYSTEMBASE]',$firmenfarbe,$tmp);

    if($this->app->erp->Firmendaten('iconset_dunkel')=='1'){
      $tmp = str_replace('[TPLNACHRICHTBOX]', 'rgba(255,255,255,0.5)', $tmp);
    }
    else{
      $tmp = str_replace('[TPLNACHRICHTBOX]', 'rgba(255,255,255,0.1)', $tmp);
    }

    $tmp = str_replace('[TPLFIRMENFARBEHELL]',$firmenfarbehell,$tmp);
    $tmp = str_replace('[TPLFIRMENFARBEDUNKEL]',$firmenfarbedunkel,$tmp);
    $tmp = str_replace('[TPLFIRMENFARBEGANZDUNKEL]',$firmenfarbeganzdunkel,$tmp);
    $tmp = str_replace('[TPLNAVIGATIONFARBE]',$navigationfarbe,$tmp);
    $tmp = str_replace('[TPLNAVIGATIONFARBE2]',$navigationfarbe2,$tmp);
    $tmp = str_replace('[TPLNAVIGATIONFARBESCHRIFT]',$navigationfarbeschrift,$tmp);
    $tmp = str_replace('[TPLNAVIGATIONFARBESCHRIFT2]',$navigationfarbeschrift2,$tmp);

    $tmp = str_replace('[TPLUNTERNAVIGATIONFARBE]',$unternavigationfarbe,$tmp);
    $tmp = str_replace('[TPLUNTERNAVIGATIONFARBESCHRIFT]',$unternavigationfarbeschrift,$tmp);


    $subaction = $this->app->Secure->GetGET('subaction');
    $submodule = $this->app->Secure->GetGET('submodule');
    if($subaction=='pinwand' || $subaction=='start' || $submodule=='kalender'){
      $tmp = str_replace('[JSDMMZINDEX]', '10000', $tmp);
    }
    else{
      $tmp = str_replace('[JSDMMZINDEX]', '10', $tmp);
    }

    if($this->app->erp->Firmendaten('standardaufloesung')=='1'){
      $tmp = str_replace('[CSSSMALL1]','1000',$tmp);
      $tmp = str_replace('[CSSSMALL2]','1000',$tmp);
      $tmp = str_replace('[CSSMARGIN]','margin-left: auto; margin-right: auto;',$tmp);
    } else {
      $tmp = str_replace('[CSSSMALL1]','1200',$tmp);
      $tmp = str_replace('[CSSSMALL2]','1200',$tmp);
      $tmp = str_replace('[CSSMARGIN]','margin-left: auto; margin-right: auto;',$tmp);
    }

    header('Content-type: text/css');
    echo $tmp;
    $this->app->erp->ExitWawi();
  }

  protected function XentralUpgradeFeed($max=3)
  {
    if(!$this->app->Conf->WFoffline)
    {
      $version = $this->app->erp->Version();
      $revision = $this->app->erp->Revision();

      $tmp = explode('.',$revision);
      $branch = strtolower($version).'_'.$tmp[0].'.'.$tmp[1];

      $BLOGURL = "https://{$this->app->Conf->updateHost}/wawision_2016.php?branch=".$branch;
      $CACHEFILE = $this->app->erp->GetTMP().md5($BLOGURL);
      $CACHEFILE2 = $this->app->erp->GetTMP().md5($BLOGURL).'2';
      if(!file_exists($CACHEFILE2))
      {
        if(file_exists($CACHEFILE)){
          @unlink($CACHEFILE);
        }
      }else{
        if(trim(file_get_contents($CACHEFILE2)) != $version.$revision){
          @unlink($CACHEFILE);
        }
      }
      $CACHETIME = 4; # hours

      if(!file_exists($CACHEFILE) || ((time() - filemtime($CACHEFILE)) > 3600 * $CACHETIME)) {
        if($feed_contents = @file_get_contents($BLOGURL)) { 
          $fp = fopen($CACHEFILE, 'w'); 
          fwrite($fp, $feed_contents); 
          fclose($fp);
          @file_put_contents($CACHEFILE2, $version.$revision);
        } 
      }
      $feed_contents = file_get_contents($CACHEFILE);

      $xml = simplexml_load_string($feed_contents);
      $json = json_encode($xml);
      $array = json_decode($json,TRUE);
      $found = false;
      $version_revision = null;
      include dirname(dirname(__DIR__)) .'/version.php';
      if($version_revision != '') {
        $ra = explode('.', $version_revision);
        if(isset($ra[2]) && $ra[2] != '') {
          $itemsCount = isset($array['channel']['item'])?count($array['channel']['item']):0;
          for($i = 0; $i< $itemsCount; $i++) {
            if($found !== false) {
              unset($array['channel']['item'][$i]);
            }
            else{
              $rev = isset($array['channel']['item'][$i]['guid'])?(string)$array['channel']['item'][$i]['guid']:'';
              if($rev === '') {
                $rev = trim(trim($array['channel']['item'][$i]['title']),')');
                $rev = trim(substr($rev, strrpos($rev, '(')+4));
              }
              if($rev == $ra[2]) {
                $found = $i;
                unset($array['channel']['item'][$i]);
              }
            }
          }
        }
      }
      if(!empty($array['channel']) && !empty($array['channel']['item']) && is_array($array['channel']['item'])) {
        $itemsCount = isset($array['channel']['item'])?count($array['channel']['item']):0;
        for($i = 0; $i < $itemsCount; $i++) {
          $this->app->Tpl->Add('WAIWISONFEEDS','<tr><td><b>'.$array['channel']['item'][$i]['title']
                  .'</b></td></tr><tr><td  style="font-size:7pt">'.$array['channel']['item'][$i]['description'].'</td></tr>');
        }
      }
      elseif($found !== false){
        $this->app->Tpl->Add('WAIWISONFEEDS','<tr><td><br><b>Ihre Version ist auf dem neusten Stand.</b></td></tr>');
      }
      $version = $this->app->erp->Version();
      if($version==='OSS') {
        $this->app->Tpl->Set('INFO', '<br>Sie verwenden die Open-Source Version.');
        $this->app->Tpl->Set('TESTBUTTON','<div class="btn">
          <a href="index.php?module=appstore&action=testen" class="button" target="_blank">14 Tage Business testen</a>
        </div>');
      }
      $this->app->Tpl->Set('RAND',md5(microtime(true)));
      if(!$this->app->erp->RechteVorhanden('welcome','changelog')) {
        $this->app->Tpl->Set('BEFORECHANGELOG', '<!--');
        $this->app->Tpl->Set('AFTERCHANGELOG', '-->');
      }
      $this->app->erp->RunHook('welcome_news');
      $this->app->Tpl->Parse('WELCOMENEWS','welcome_news.tpl');
    }
  }

  
  public function WelcomeAddPinwand()
  {

    $user = $this->app->User->GetID();
    $users = $this->app->DB->SelectArr("SELECT u.id, a.name as description FROM user u LEFT JOIN adresse a ON a.id=u.adresse WHERE u.activ='1' ORDER BY u.username");
    $user_out = '';
    $cusers = !empty($users)?count($users):0;
    for($i=0; $i<$cusers;$i++){
      $select = $user==$users[$i]['id'] ? 'selected' : '';
      $user_out .= "<option value=\"{$users[$i]['id']}\" $select>{$users[$i]['description']}</option>";
    }
    $this->app->Tpl->Set('PERSONEN', $user_out);

    $name = $this->app->Secure->GetPOST('name');
    if($name!='')
    {
      $personen = $this->app->Secure->GetPOST('personen');
      $this->app->DB->Insert("INSERT INTO pinwand (id,name,user) VALUES ('','$name','$user')");
      $pinwand = $this->app->DB->GetInsertID();
      $cpersonen = !empty($personen)?count($personen):0;
      for($i=0;$i<=$cpersonen;$i++)
      {
        if($personen[$i] > 0)
        {
          $this->app->DB->Insert("INSERT INTO pinwand_user (pinwand,user) VALUES ('$pinwand','".$personen[$i]."')");
        }
      }

      $this->app->Tpl->Set('PAGE', "<script>
          parent.location.href = './index.php?module=welcome&action=pinwand';
          </script>");
    }
    else {
      $this->app->Tpl->Parse('PAGE','welcome_pinwand_addpinwand.tpl');
    }

    $this->app->BuildNavigation=false;
  }

  public function WelcomeAddNote()
  {
    $aufgabeid = (int)$this->app->Secure->GetGET('aufgabeid');
    $beschreibung = $this->app->Secure->GetPOST('notebody');
    if($beschreibung!='')
    {
      $color = $this->app->Secure->GetPOST('color');
      $aufgabe = $beschreibung;
      $pinwand = $this->app->Secure->GetGET('pinwand');

      $aufgabe =  str_replace('\r\n',' ',$aufgabe);

      $max_z = $this->app->DB->Select("SELECT MAX(note_z) FROM aufgabe WHERE adresse='".$this->app->User->GetAdresse()."' ");
      $new = true;
      if($aufgabeid)
      {
        $cuid = $this->app->DB->Select("SELECT id FROM aufgabe WHERE adresse = '".$this->app->User->GetAdresse()."' AND id = ".$aufgabeid." LIMIT 1");
        if($cuid)
        {
          $new = false;
          $id = $cuid;
        }
      }

      if($this->app->erp->is_html($aufgabe))
      {
        $aufgabe = strip_tags(str_replace('<', ' <', $aufgabe));
        $aufgabe = trim(str_replace('  ', ' ', $aufgabe));

        if($new){
          $id = $this->app->erp->CreateAufgabe($this->app->User->GetAdresse(),$aufgabe);
        }
      }
      else{
        if($new){
          $id = $this->app->erp->CreateAufgabe($this->app->User->GetAdresse(), $aufgabe);
        }
      }
      $xy = $this->getCoordsForNewTask($id);
      $note_x = $xy['note_x'];
      $note_y = $xy['note_y'];
      $this->app->DB->Update(
        "UPDATE aufgabe 
          SET pinwand='1',
              pinwand_id='$pinwand', 
              note_color='$color', 
              note_z='$max_z',
              note_x='".$note_x."',
              note_y='".$note_y."',
              beschreibung='$beschreibung' 
          WHERE id='$id' LIMIT 1"
      );

      $this->app->Tpl->Set('PAGE', "<script>
          parent.location.href = './index.php?module=welcome&action=pinwand&pinwand=$pinwand';
          </script>");
    }
    else {
      if($aufgabeid)
      {
        $aufg = $this->app->DB->SelectArr('SELECT * FROM aufgabe WHERE id = '.$aufgabeid.' LIMIT 1');
        if($aufg)
        {
          $aufg = reset($aufg);
          $this->app->Tpl->Set('PADDNOTE_BODY',$aufg['beschreibung']);
          $this->app->Tpl->Set('PADDNOTE_JS',"
          <script type=\"text/javascript\">
          $(document).ready(function() {
            $('#paddnotecolor').val('".$aufg['note_color']."');
          });
          </script>
          ");
          
        }
      }

      $this->app->YUI->CkEditor('notebody','belege');//,array("height"=>"450"));
     
      $this->app->Tpl->Parse('PAGE','welcome_pinwand_addnote.tpl');
    }

    $this->app->BuildNavigation=false;
  }

  public function WelcomeDelNote()
  {
    $id = $this->app->Secure->GetGET('id');
    $pinwand = $this->app->Secure->GetGET('pinwand');
    if($id > 0){
      $this->app->DB->Update("DELETE FROM aufgabe WHERE id='$id' LIMIT 1");
    }
    header('Location: index.php?module=welcome&action=pinwand&pinwand='.$pinwand);
    exit;
  }

  public function WelcomeOkNote()
  {
    $id = $this->app->Secure->GetGET('id');
    $pinwand = $this->app->Secure->GetGET('pinwand');

    $this->app->erp->AbschlussAufgabe($id);
    //$this->app->DB->Update("UPDATE aufgabe SET status='abgeschlossen' WHERE id='$id' LIMIT 1");
    header('Location: index.php?module=welcome&action=pinwand&pinwand='.$pinwand);
    exit;
  }

  public function WelcomeMoveNote()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0){
      $x = $this->app->Secure->GetGET('x');
      $y = $this->app->Secure->GetGET('y');
      $z = $this->app->Secure->GetGET('z');
      $this->app->DB->Update("UPDATE aufgabe SET note_x='$x',note_y='$y',note_z='$z' WHERE id='$id' LIMIT 1");
    }
    exit;
  }

  public function WelcomePinwand()
  {
    $this->app->erp->StartseiteMenu();

    $cmd = $this->app->Secure->GetGET('cmd');
    $pinwand = $this->app->Secure->GetGET('pinwand');

    switch($cmd)
    {
      case 'resize':
        $id = $this->app->Secure->GetGET('id');
        if($id > 0)
        {
          $w = $this->app->Secure->GetGET('w');
          $h = $this->app->Secure->GetGET('h');
          $this->app->DB->Update("UPDATE aufgabe SET note_w='".$this->app->DB->real_escape_string($w)."',
          note_h='".$this->app->DB->real_escape_string($h)."' WHERE id='$id' LIMIT 1");
          $result['status']=1;
        } else {
          $result['status']=0;
          $result['statusText']='Fehlgeschlagen';
        }
        echo json_encode($result);
        exit;
      break;
      case 'get':
        $id = $this->app->Secure->GetPOST('id');
        $result['id']=$id;
        $result['beschreibung']=$this->app->DB->Select("SELECT beschreibung FROM aufgabe WHERE id='$id' LIMIT 1");
        $result['note_color']=$this->app->DB->Select("SELECT note_color FROM aufgabe WHERE id='$id' LIMIT 1");
        $result['status']=1;
        $result['statusText']='';
        echo json_encode($result);
        exit;
      break;
      case 'save':
        $id = $this->app->Secure->GetPOST('id');
        $beschreibung = $this->app->Secure->GetPOST('beschreibung');
        $note_color = $this->app->Secure->GetPOST('note_color');
        $pinwand = $this->app->Secure->GetPOST('pinwand');
        if($pinwand <=0) {
          $pinwand=0;
        }
        if($id > 0)
        {
          $this->app->DB->Update("UPDATE aufgabe SET beschreibung='".$this->app->DB->real_escape_string($beschreibung)."',
          note_color='".$this->app->DB->real_escape_string($note_color)."' WHERE id='$id' LIMIT 1");
          $result['note_color']=$note_color;
          $result['beschreibung']=$beschreibung;
          $result['status']=1;
        } else {
          $aufgabe = strip_tags(str_replace('<', ' <', $beschreibung));
          $aufgabe = trim(str_replace('  ', ' ', $aufgabe));
          $max_z = $this->app->DB->Select("SELECT MAX(note_z) FROM aufgabe WHERE adresse='".$this->app->User->GetAdresse()."'");
          $id = $this->app->erp->CreateAufgabe($this->app->User->GetAdresse(),$aufgabe);
          $xy = $this->getCoordsForNewTask($id);
          $note_x = $xy['note_x'];
          $note_y = $xy['note_y'];
          $this->app->DB->Update(
            "UPDATE aufgabe 
            SET note_color='".$this->app->DB->real_escape_string($note_color)."',
                beschreibung='".$this->app->DB->real_escape_string($beschreibung)."', 
                note_z='".$max_z."',
                note_x='".$note_x."',
                note_y='".$note_y."',
                pinwand='1',
                pinwand_id='".$pinwand."' 
            WHERE id='$id' LIMIT 1"
          );

          $result['note_color']=$note_color;
          $result['beschreibung']=$beschreibung;
          $result['status']=1;
          $result['statusText']='';
        }
        echo json_encode($result);
        exit;
      break;

    }

    if($pinwand <=0)
    {
      $tmp = $this->app->DB->SelectArr("SELECT * FROM aufgabe WHERE adresse='".$this->app->User->GetAdresse()."' AND pinwand='1' AND pinwand_id='0' AND status='offen'");
    } else {
      $erlaubt = true;
      if($this->app->User->GetType() != 'admin')
      {
        $check = $this->app->DB->Select("SELECT id FROM pinwand WHERE id = '$pinwand' AND `user` = '".$this->app->User->GetID()."'");
        if(!$check && !$this->app->DB->Select("SELECT id FROM pinwand_user WHERE pinwand = '$pinwand' AND `user` = '".$this->app->User->GetID()."' LIMIT 1"))
        {
          $erlaubt = false;
        }
      }
      if($erlaubt)
      {
        $tmp = $this->app->DB->SelectArr("SELECT * FROM aufgabe WHERE pinwand='1' AND pinwand_id='$pinwand' AND status='offen'");
      }
    }  
    $ctmp = !empty($tmp)?count($tmp):0;
    for($i=0;$i<$ctmp;$i++)
    {
      $left = $tmp[$i]['note_x'];
      $color = $tmp[$i]['note_color'];
      if($color==''){
        $color='yellow';
      }
      $top = $tmp[$i]['note_y'];
      $zindex = $tmp[$i]['note_z'];
      $text = nl2br($this->app->erp->ReadyForPDF($tmp[$i]['beschreibung']));
      if($text=='') {
        $text = $tmp[$i]['aufgabe'];
      }
      $id = $tmp[$i]['id'];
      $projekt = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$tmp[$i]['projekt']."' LIMIT 1");

      $width = $tmp[$i]['note_w']?$tmp[$i]['note_w']:130;
      $height = $tmp[$i]['note_h']?$tmp[$i]['note_h']:130;

      if($pinwand <= 0) {
        $pinwand=0;
      }

      $seriennummer = $this->app->DB->Select("SELECT seriennummer FROM adapterbox WHERE verwendenals='bondrucker' LIMIT 1");
      if($seriennummer!=''){
        $menu_bon = '<a href="#" onclick=AjaxCall("index.php?module=aufgaben&action=bondrucker&id=' . $id . '");><img src="themes/[THEME]/images/bon_druck.png" border="0"></a>&nbsp;';
      }
      else{
        $menu_bon = '<a href="#" onclick=InfoBox("aufgabe_bondrucker");><img src="themes/[THEME]/images/bon_druck.png" border="0"></a>&nbsp;';
      }

      switch($color)
      {
        case 'yellow': $color='#f69e06'; break;
        case 'blue': $color='#41b3ce'; break;
        case 'green': $color='#a9ca45'; break;
        case 'coral': $color='#be3978'; break;
      } 

      $result = ' <div class="note" id="note'.$id.'" style="border-left-color:'.$color.';left:'.$left.'px;top:'.$top.'px;  z-index:'.$zindex.'; width:'.$width.'px;height:'.$height.'px;">
        <div id="notehtml'.$id.'">'.$text.'</div> 
        <div class="author">'.$projekt.'&nbsp;
          <a href="#" onclick="PinwandEdit('.$pinwand.','.$id.');">      
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M13.9442 2.05599C13.5429 1.65638 12.9986 1.43371 12.4323 1.43755C11.866 1.44138 11.3247 1.6714 10.9289 2.07641L2.47058 10.5347L1.4375 14.5627L5.46542 13.5296L13.9238 5.07124C14.3288 4.67543 14.5588 4.13416 14.5626 3.56787C14.5664 3.00158 14.3438 2.45725 13.9442 2.05599Z" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M10.6875 2.31836L13.6823 5.31319" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M9.46484 3.54004L12.4597 6.53487" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M2.46875 10.5347L5.4665 13.5266" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>      
          </a>&nbsp;
          <a href="#" onclick="AufgabenEdit('.$id.');return false;" target="_blank" title="Aufgabe bearbeiten" >
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M6.6875 9.31152L1.4375 14.5615" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M14.5625 5.37402V1.43652H10.625" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M1.4375 10.624V14.5615H5.375" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M14.5625 1.43652L9.3125 6.68652" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M9.3125 9.31152L14.5625 14.5615" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M1.4375 5.37402V1.43652H5.375" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M14.5625 10.624V14.5615H10.625" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M1.4375 1.43652L6.6875 6.68652" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>&nbsp;
          '
        //.$menu_bon
          .'
          <a href="index.php?module=welcome&action=oknote&id='.$id.'&pinwand='.$pinwand.'">
            <svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M4.0625 12.6855L1.4375 10.0605L4.0625 7.43555" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M1.4375 10.0605H10.1875C12.6037 10.0605 14.5625 8.10179 14.5625 5.68555C14.5625 3.2693 12.6037 1.31055 10.1875 1.31055H6.6875" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>&nbsp;
          <a href="index.php?module=welcome&action=delnote&id='.$id.'&pinwand='.$pinwand.'">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M0.875 2.625H13.125" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M8.3125 0.875H5.6875C5.20425 0.875 4.8125 1.26675 4.8125 1.75V2.625H9.1875V1.75C9.1875 1.26675 8.79575 0.875 8.3125 0.875Z" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M5.6875 10.0625V5.6875" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M8.3125 10.0625V5.6875" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M11.0046 12.3223C10.967 12.776 10.5877 13.125 10.1325 13.125H3.86808C3.41285 13.125 3.03363 12.776 2.996 12.3223L2.1875 2.625H11.8125L11.0046 12.3223Z" stroke="#929292" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
        </div>
        <span class="data">'.$id.'</span>
        </div>';

      $this->app->Tpl->Add('NOTES',$result);
    }

    $this->app->Tpl->Set('POPUPWIDTH','700');
    $this->app->Tpl->Set('POPUPHEIGHT','600');

    $tmp = $this->app->DB->SelectArr("SELECT DISTINCT p.id,p.name FROM pinwand p 
      LEFT JOIN pinwand_user pu ON pu.pinwand=p.id WHERE (pu.user='".$this->app->User->GetID()."' OR p.user='".$this->app->User->GetID()."') ORDER by p.name");
    $ctmp = !empty($tmp)?count($tmp):0;
    for($i=0;$i<$ctmp;$i++)
    {
      if($pinwand==$tmp[$i]['id']) {
        $selected='selected';
      } else {
        $selected='';
      }
      $this->app->Tpl->Add('PINWAENDE','<option value="'.$tmp[$i]['id']."\" $selected>".$tmp[$i]['name'].'</option>');
    }

    // Aufgabe-Bearbeiten-Popup
    $pinnwaende = $this->app->erp->GetPinwandSelect();
    $pinnwand = "";
    foreach($pinnwaende as $key=>$value){
      $pinnwand .= "<option value='$key'>".$value."</option>";
    }
    $this->app->Tpl->Set("PINNWAND", $pinnwand);
    // ENDE:Aufgabe-Bearbeiten-Popup

    $this->app->YUI->CkEditor('editbeschreibung', 'minimal', array('height'=>'150', 'width'=>'250','ckeditor5'=>true));
    $this->app->YUI->ColorPicker('editnote_color');
    $this->app->YUI->CkEditor("e_notizen","belege",array("width"=>"625"));
    $this->app->YUI->CkEditor("e_beschreibung","belege",array("width"=>"420"));
    $this->app->YUI->DatePicker("e_datum");
    $this->app->YUI->TimePicker("e_zeit");
    $this->app->Tpl->Parse('AUFGABENPOPUP','aufgaben_popup.tpl');
    $this->app->Tpl->Parse('PAGE','welcome_pinwand.tpl');
  }

  private function getCoordsForNewTask($taskId){

    $coords = ['note_x'=>0,'note_y'=>0];

    $oldCoords = $this->app->DB->Select(
      sprintf(
        'SELECT MAX(a.note_x) 
        FROM `aufgabe` AS `a` 
        WHERE a.id != %d 
        AND (a.note_x = a.note_y OR (a.note_x IS NULL AND a.note_y IS NULL))
        ORDER BY a.note_x',
        $taskId
      )
    );

    if(!empty($oldCoords) || $oldCoords==0){

      $toAdd = 10;

      if($oldCoords%$toAdd==0){
        $coords = ['note_x' => $oldCoords + $toAdd, 'note_y' => $oldCoords + $toAdd];
      }
    }

    return $coords;
  }

  public function Accordion()
  {
    // check if accordion is empty

      //$this->app->DB->Insert("INSERT INTO accordion (name,target,position) VALUES ('Startseite','StartseiteWiki','1')");
      $this->app->DB->DisableHTMLClearing(true);
      $check_startseite = $this->app->DB->Select("SELECT `name` FROM wiki WHERE name='StartseiteWiki' LIMIT 1");
      if($check_startseite == '')
      {
        $wikifirstpage='
<p>Herzlich Willkommen in Ihrem Xentral,<br><br>wir freuen uns Sie als Xentral Benutzer begrüßen zu dürfen. Mit Xentral organisieren Sie Ihre Firma schnell und einfach. Sie haben alle wichtigen Zahlen und Vorgänge im Überblick.<br><br>Für Einsteiger sind die folgenden Themen wichtig:<br><br></p>
<ul>
<li> <a href="index.php?module=firmendaten&amp;action=edit" target="_blank"> Firmendaten</a> (dort richten Sie Ihr Briefpapier ein)</li>
<li> <a href="index.php?module=adresse&amp;action=list" target="_blank"> Stammdaten / Adressen</a> (Kunden und Lieferanten anlegen)</li>
<li> <a href="index.php?module=artikel&amp;action=list" target="_blank"> Artikel anlegen</a> (Ihr Artikelstamm)</li>
<li> <a href="index.php?module=angebot&amp;action=list" target="_blank"> Angebot</a> / <a href="index.php?module=auftrag&amp;action=list" target="_blank"> Auftrag</a> (Alle Dokumente für Ihr Geschäft)</li>
<li> <a href="index.php?module=rechnung&amp;action=list" target="_blank"> Rechnung</a> / <a href="index.php?module=gutschrift&amp;action=list" target="_blank"> Gutschrift</a></li>
<li> <a href="index.php?module=lieferschein&amp;action=list" target="_blank"> Lieferschein</a></li>
</ul>
<p><br><br>Kennen Sie unsere Zusatzmodule die Struktur und Organisation in das tägliche Geschäft bringen?<br><br></p>
<ul>
<li> <a href="index.php?module=kalender&amp;action=list" target="_blank"> Kalender</a></li>
<li> <a href="index.php?module=wiki&amp;action=list" target="_blank"> Wiki</a></li>
</ul>';

        $this->app->DB->Insert("INSERT INTO wiki (name,content) VALUES ('StartseiteWiki','".$wikifirstpage."')");
      }
      $data = $this->app->DB->SelectArr("SELECT * FROM accordion ORDER BY position");


    $out = '';
    $entry = '';

    $edit = "<a class=\"edit\" href=\"index.php?module=wiki&action=edit&cmd=StartseiteWiki\"><img src=./themes/[THEME]/images/edit.svg></a>";

    $wikipage_exists = $this->app->DB->Select("SELECT '1' FROM wiki WHERE name='StartseiteWiki' LIMIT 1");
    if($wikipage_exists!='1'){
      $this->app->DB->Insert("INSERT INTO wiki (name) VALUES ('StartseiteWiki')");
    }
    $wikipage_content = $this->app->DB->Select("SELECT content FROM wiki WHERE name='StartseiteWiki' LIMIT 1");
    $this->app->DB->DisableHTMLClearing(false);
    $wikipage_content = $this->app->erp->ReadyForPDF($wikipage_content);
    $wikiparser = new WikiParser();
    $content = $wikiparser->parse($wikipage_content);


    $this->app->Tpl->Set("ACCORDIONEDIT",$edit);

    $this->app->Tpl->Set('ACCORDIONENTRY0', $content);
    $entry = '[ACCORDIONENTRY0]';

    $out .= "<!--<h3><a href=\"#\">Startseite</a></h3>-->
      <div><div class=\"wiki\"><!--$edit<br/>-->$entry<br><br></div></div>";
    
    return $out;
  }

  public function WelcomeUpgrade()
  {
    $this->app->erp->MenuEintrag('index.php?module=welcome&action=start','zur&uuml;ck zur Startseite');
    $this->app->erp->Headlines('Update f&uuml;r Xentral');

    $this->app->Tpl->Set('STARTBUTTON','<!--');
    $this->app->Tpl->Set('ENDEBUTTON','-->');

    $lizenz = $this->app->erp->Firmendaten('lizenz');
    $schluessel = $this->app->erp->Firmendaten('schluessel');
    if($lizenz=='' || $schluessel=='')
    {
      if(is_file('../wawision.inc.php'))
      {
        include_once '../wawision.inc.php';
        $this->app->erp->FirmendatenSet('lizenz',$WAWISION['serial']);
        $this->app->erp->FirmendatenSet('schluessel',$WAWISION['authkey']);
      }
    }

    $this->app->erp->MenuEintrag('index.php?module=welcome&action=upgrade','Update');
    $this->XentralUpgradeFeed(5);
    $result = '';
    if($this->app->Secure->GetPOST('upgrade'))
    {
      ob_start();
      // dringend nacheinander, sonst wird das alte upgrade nur ausgefuehrt
        if(!is_dir('.svn'))
        {
          echo "new update system\r\n";
          include '../upgradesystemclient2_include.php';
        } else {
          echo "Update in Entwicklungsversion\r\n";
        }

      $result .= "\r\n>>>>>>Bitte klicken Sie jetzt auf \"Weiter mit Schritt 2\"<<<<<<\r\n\r\n";
      $result .= ob_get_contents();
      $result .= "\r\n>>>>>>Bitte klicken Sie jetzt auf \"Weiter mit Schritt 2\"<<<<<<\r\n\r\n";
      ob_end_clean();

      if(is_dir('.svn'))
      {
        $version_revision = 'SVN';
      } else {
        include '../version.php';
      }

      $result .="\r\nIhre Version: $version_revision\r\n";

    } else {
      $result .=">>>>>Bitte auf \"Dateien aktualisieren jetzt starten\" klicken<<<<<<\r\n";
    }

    if($this->app->erp->Firmendaten('version')==''){
      $this->app->erp->FirmendatenSet('version', $this->app->erp->RevisionPlain());
    }

    $doc_root  = preg_replace("!{$_SERVER['SCRIPT_NAME']}$!", '', $_SERVER['SCRIPT_FILENAME']); # ex: /var/www
    $path = preg_replace("!^{$doc_root}!", '', __DIR__); 

$this->app->Tpl->Add('TAB1',"<h2>Schritt 1 von 2: Dateien aktualisieren</h2><table width=\"100%\"><tr valign=\"top\"><td width=\"70%\"><form action=\"\" method=\"post\" class=\"updateForm\"><input type=\"hidden\" name=\"upgrade\" value=\"1\">
        <textarea rows=\"15\" cols=\"90\">$result</textarea>
        <br><input type=\"submit\" value=\"Dateien aktualisieren jetzt starten\" name=\"upgrade\">&nbsp;        
       <input type=\"button\" value=\"Weiter mit Schritt 2\" onclick=\"window.location.href='index.php?module=welcome&action=upgradedb'\">&nbsp;
        </form></td><td>[WELCOMENEWS]</td></tr></table>");

    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function WelcomeUpgradeDB()
  {
    $this->app->erp->MenuEintrag('index.php?module=welcome&action=start','zur&uuml;ck zur Startseite');
    $this->app->erp->Headlines('Update f&uuml;r Xentral');

    $lizenz = $this->app->erp->Firmendaten('lizenz');
    $schluessel = $this->app->erp->Firmendaten('schluessel');
    if($lizenz=='' || $schluessel=='')
    {
      if(is_file('../wawision.inc.php'))
      {
        include_once '../wawision.inc.php';
        $this->app->erp->FirmendatenSet('lizenz',$WAWISION['serial']);
        $this->app->erp->FirmendatenSet('schluessel',$WAWISION['authkey']);
      }
    }
    $this->app->erp->MenuEintrag('index.php?module=welcome&action=upgradedb','Update');
    $this->XentralUpgradeFeed(5);
    $result = '';
    if($this->app->Secure->GetPOST('upgradedb'))
    {
      ob_start();
      //   include("upgradesystemclient.php");
        $result .="Starte DB Update\r\n";
        $this->app->erp->UpgradeDatabase();
        $this->app->erp->check_column_missing_run = true;
        $this->app->erp->UpgradeDatabase();
        
        if(count($this->app->erp->check_column_missing) > 0)
        {
          $result .= "\r\n**** INFORMATION DATENBANK ****\r\n";
          foreach($this->app->erp->check_column_missing as $tablename=>$columns)
          {
          $result .= "\r\n";
            foreach($columns as $key=>$columname) {
              $result .= $tablename . ':' . $columname . "\r\n";
            }
          }
          $result .= "\r\n**** INFORMATION DATENBANK ****\r\n\r\n";
        }
        if(count($this->app->erp->check_index_missing) > 0)
        {
          $result .= "\r\n**** INFORMATION DATENBANK INDEXE ****\r\n";
          foreach($this->app->erp->check_index_missing as $tablename=>$columns)
          {
            $result .= "\r\n";
            foreach($columns as $key=>$columname) {
              $result .= $tablename . ":" . $columname . "\r\n";
            }
          }
          $result .= "\r\n**** INFORMATION DATENBANK INDEXE ****\r\n\r\n";
        }
        $result .="Fertig DB Update\r\n";
        $result .="\r\n\r\nDas Datenbank Update wurde durchgef&uuml;hrt\r\n";
        $result .="\r\n>>>>>Sie k&ouml;nnen nun mit Xentral weiterarbeiten.<<<<<<\r\n";
        $result .= ob_get_contents();
      ob_end_clean();
    } else {
      $result .="\r\n>>>>>Bitte auf \"Datenbank Anpassungen jetzt durchf&uuml;hren\" klicken<<<<<<\r\n";
    }

    if($this->app->erp->Firmendaten('version')==''){
      $this->app->erp->FirmendatenSet('version', $this->app->erp->RevisionPlain());
    }

    $doc_root  = preg_replace("!{$_SERVER['SCRIPT_NAME']}$!", '', $_SERVER['SCRIPT_FILENAME']); # ex: /var/www
    $path = preg_replace("!^{$doc_root}!", '', __DIR__); 

$this->app->Tpl->Add('TAB1',"<h2>Schritt 2 von 2: Datenbank anpassen</h2><table width=\"100%\"><tr valign=\"top\"><td width=\"70%\"><form action=\"\" method=\"post\" class=\"updateForm\"><input type=\"hidden\" name=\"upgrade\" value=\"1\">
        <textarea rows=\"15\" cols=\"90\">$result</textarea>
        <br><input type=\"submit\" value=\"Datenbank Anpassungen jetzt durchf&uuml;hren\" name=\"upgradedb\">&nbsp;
       <input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"window.location.href='index.php?module=welcome&action=upgrade'\">&nbsp;
       <input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php'\">&nbsp;
        </form></td><td>[WELCOMENEWS]</td></tr></table>");

    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }




  public function Termine($date)
  {
    $userid = $this->app->User->GetID();

    if(is_numeric($userid)) {
      $termine = $this->app->DB->SelectArr("SELECT DISTINCT color,von,bis,bezeichnung,allDay,ke.id FROM kalender_user AS ka
          RIGHT JOIN kalender_event AS ke ON ka.event=ke.id
          WHERE (ka.userid='$userid' OR ke.public='1') AND DATE(von)='$date'
          ORDER BY von");
      $out = '';
      if($termine)
      {
        foreach($termine AS $t) {
          $von = date('G:i', strtotime($t['von']));
          $bis = date('G:i', strtotime($t['bis']));

          if($t['allDay']=='1') {
            $von = 'Ganztags';
            $bis = '';
          }else {
            if($von==$bis){
              $bis = '';
            }
            else{
              $bis = '- ' . $bis;
            }
          }

          $color = (($t['color']!='') ? "style='background-color: {$t['color']};border-color: {$t['color']};'" : '');

          $out .= "<li $color onclick=\"openeventdialog(".$t['id'].",'".$von."','".$bis."','".$t['allDay']."','".htmlspecialchars($t['bezeichnung'])."')\"><span class=\"description\">{$t['bezeichnung']}<br>$von $bis&nbsp;&nbsp;</span></li>";
        }
        $out .= '		<script>		
        function openeventdialog(id, start, end, allday, task)
        {
        if($("#TerminDialog").SetFormData(id, start, end, allday,task))
					$("#TerminDialog").dialog("open");
        }</script>';
      }

      if(!$termine || count($termine)==0) {
        $out = '<center><i>{|Keine Termine vorhanden|}</i></center>';
      }

      return $out;
    }
  }



  function Aufgaben($parse)
  {

  }

  function WelcomeHelp()
  {
  }

  /**
   * Nur eine Fake-Action um eine Berechtigung zu erzeugen
   *
   * @return void
   */
  public function WelcomeMobileApps()
  {
  }

  public function WelcomeSettings()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    switch ($cmd) {
      case 'inviteteamclickbyclick':
        return $this->HandleInviteTeamClickByClick();
        break;
      case 'startclickbyclick':
        return $this->HandleStartClickByClick();
        break;
      case 'changeroleclickbyclick':
        return $this->HandleChangeRoleClickByClick();
        break;
      case 'changepasswordclickbyclick':
        return $this->HandlePasswordChangeClickByClick();
        break;
      // Passwort ändern
      case 'password-change':
        $this->HandlePasswordChange();
        break;

      // Einstellungen speichern
      case 'settings-save':
        $this->HandleProfileSettingsSave();
        break;

      // Profilbild löschen
      case 'picture-delete':
        $this->HandleProfilePictureDeletion();
        break;

      // Profilbild hochladen
      case 'picture-upload':
        $this->HandleProfilePictureUpload();
        break;

      case 'mobile-apps-account':
        $this->HandleMobileAppsAccount();
        break;

      case 'googlecalendar-save':
         $this->HandleGoogleCalendarSave();
         break;

      case 'gmail-save':
          $this->HandleGoogleMailAuth();
          break;

      case 'gmail-test':
          $this->HandleGoogleMailTest();
          break;

      case 'totp_toggle':
          $this->HandleTOTPToggle();
          break;

      case 'totp_regenerate':
        $this->HandleTOTPRegenerate();
        break;
    }

    // Einstellungen laden
    $settings = $this->app->DB->SelectRow(
      "SELECT u.startseite, u.chat_popup, u.callcenter_notification, u.defaultcolor, u.sprachebevorzugen 
       FROM `user` AS u WHERE u.id = '" . $this->app->User->GetID() . "' LIMIT 1"
    );

    $this->app->Tpl->Set('STARTSEITE', $settings['startseite']);
    $this->app->Tpl->Set('DEFAULTCOLOR', $settings['defaultcolor']);
    $this->app->Tpl->Set('SPRACHEBEVORZUGEN', $this->languageSelectOptions($settings['sprachebevorzugen']));

    if($settings['chat_popup']){
      $this->app->Tpl->Set('CHAT_POPUP', ' checked="checked" ');
    }
    if($settings['callcenter_notification']){
      $this->app->Tpl->Set('CALLCENTER_NOTIFICATION', ' checked="checked" ');
    }

    // Profilbild laden
    $adresse = $this->app->User->GetAdresse();
    $dateiversion = (int)$this->app->DB->Select("SELECT dv.id FROM datei_stichwoerter ds INNER JOIN datei d ON ds.datei = d.id INNER JOIN datei_version dv ON dv.datei = d.id WHERE d.geloescht = 0 AND objekt like 'Adressen' AND parameter = '" . $adresse . "' AND subjekt like 'Profilbild' ORDER by d.id DESC, dv.id DESC LIMIT 1");
    if($dateiversion){
      $this->app->Tpl->Add('DATEI', "<span id=\"profilbild\" style=\"padding:0;margin:0;height:100px;width:100px;display:inline-block;position:relative;background-repeat:no-repeat; background-image: url('index.php?module=ajax&action=profilbild&id=" . $this->app->User->GetID() . "'); \"></span>");
    }else{
      $this->app->Tpl->Set('VORPROFILBILDLOESCHEN', '<!--');
      $this->app->Tpl->Set('NACHPROFILBILDLOESCHEN', '-->');
      $this->app->Tpl->Add('DATEI', "<span id=\"profilbild\" style=\"padding:0;margin:0;height:100px;width:100px;display:inline-block;position:relative;background-repeat:no-repeat; background-image: url('./themes/new/images/keinbild_dunkel.png'); \"></span>");
    }

    // Mobile Apps Einstellungen laden
    $apiAccountActive = false;
    $apiAccountExisting = false;
    $hasMobileAppsPermission = (bool)$this->app->erp->RechteVorhanden('welcome','mobileapps');
    if ($hasMobileAppsPermission){
      $apiAccountId = (int)$this->app->User->GetParameter('mobile_apps_api_account_id');
      $apiAccountData = $this->app->DB->SelectRow("SELECT a.remotedomain, a.initkey, a.aktiv FROM api_account AS a WHERE a.id = '{$apiAccountId}'");
      $apiAccountActive = isset($apiAccountData['aktiv']) && (int)$apiAccountData['aktiv'] === 1;
      $apiAccountExisting = isset($apiAccountData['remotedomain']) && !empty($apiAccountData['remotedomain']);

      /** @var Request $request */
      $request = $this->app->Container->get('Request');
      $serverUrl = $request->getBaseUrl() . '/'; // Url muss aufs www-Verzeichnis zeigen; App hängt 'api/v1/mobileapi/dashboard' an
      $qrCodeArray = [
        'server_url' => $serverUrl,
        'username' => $apiAccountData['remotedomain'],
        'password' => $apiAccountData['initkey'],
      ];
      $qrCodeData = json_encode($qrCodeArray);
    }

    if ($hasMobileAppsPermission && $apiAccountExisting && $apiAccountActive) {
      /** @var BarcodeFactory $barcodeFactory */
      $barcodeFactory = $this->app->Container->get('BarcodeFactory');
      $barcodeObject = $barcodeFactory->createQrCode($qrCodeData);
      $qrCodeHtml = $barcodeObject->toHtml(3, 3);
      $this->app->Tpl->Set('MOBILE_APP_QRCODE', $qrCodeHtml);
      $this->app->Tpl->Set('MOBILE_APP_DESCRIPTION', 'Bitte scannen Sie den hier angezeigten QR-Code mit Ihrem Handy in der Xentral App unter &quot;Registrieren&quot;.');
      $this->app->Tpl->Set('MOBILE_APP_BUTTON', '<input type="submit" name="mobile_app_api_deactivate" value="{|Zugang deaktivieren|}">');
    }
    if ($hasMobileAppsPermission && $apiAccountExisting && !$apiAccountActive){
      $this->app->Tpl->Set('MOBILE_APP_QRCODE', '');
      $this->app->Tpl->Set('MOBILE_APP_DESCRIPTION', 'API-Zugang ist deaktiviert. Aktivieren Sie den Zugang um die Mobile App nutzen zu können.');
      $this->app->Tpl->Set('MOBILE_APP_BUTTON', '<input type="submit" name="mobile_app_api_activate" value="{|Zugang aktivieren|}">');
    }
    if ($hasMobileAppsPermission && !$apiAccountExisting){
      $this->app->Tpl->Set('MOBILE_APP_QRCODE', '');
      $this->app->Tpl->Set('MOBILE_APP_DESCRIPTION', '');
      $this->app->Tpl->Set('MOBILE_APP_BUTTON', '<input type="submit" name="mobile_app_api_create" value="{|Zugang anlegen|}"> (Legt API-Account an)');
    }
    if (!$hasMobileAppsPermission){
      $this->app->Tpl->Set('MOBILE_APP_QRCODE', '');
      $this->app->Tpl->Set('MOBILE_APP_DESCRIPTION', 'Sie haben kein Rechte eine Mobile App zu registrieren. Bitte wenden Sie sich an Ihren Administrator.');
      $this->app->Tpl->Set('MOBILE_APP_BUTTON', '');
    }

    $this->renderGoogleCalendarSettings();
    $this->renderGoogleMailSettings();

    $this->app->erp->Headlines('Mein Bereich', 'Pers&ouml;nliche Einstellungen');
    $this->app->erp->MenuEintrag('index.php?module=welcome&action=settings','&Uuml;bersicht');

    $this->app->YUI->AutoSaveUserParameter('name_fuer_unterartikel','matrixprodukt_name_fuer_unterartikel');
    $this->app->YUI->ColorPicker('defaultcolor');
    $this->app->YUI->PasswordCheck('password', 'repassword', '', 'submit_password');

    $this->renderTOTP();
    $this->app->Tpl->Parse('PAGE','welcome_settings.tpl');
  }

  private function renderTOTP(){
    /** @var TOTPLoginService $totpLoginManager */
    $totpLoginManager = $this->app->Container->get('TOTPLoginService');

    $userID = $this->app->User->GetID();

    $totpEnabled = $totpLoginManager->isTOTPEnabled($userID);

    $this->app->Tpl->Set('TOTP_TOGGLE_VALUE', $totpEnabled ? 'TOTP ausschalten' : 'TOTP einschalten');
    $this->app->Tpl->Set('TOTP_TOGGLE_ID', $totpEnabled ? 'totp_disable' : 'totp_enable');
    if(!$totpEnabled) {
      $this->app->Tpl->Set('TOTP_REGENERATE_VISIBILITY', 'style="display: none"');
      return;
    }

    $label = 'Xentral' . ' | ' . $this->app->erp->GetFirmaName();
    $qrCode = $totpLoginManager->generatePairingQrCode($userID, $label);

    $secret = $totpLoginManager->getTOTPSecret($userID);

    $this->app->Tpl->Set('TOTP_KEY_HTML', "Schlüssel: {$secret}");

    $qrHtml = $qrCode->toHtml(3, 3);

    $this->app->Tpl->Set('TOTP_QR_HTML', $qrHtml);
  }
  public function WelcomeRedirect()
  {
    $url = $this->app->Secure->GetGET('url');
    if (empty($url)) {
      $this->app->Tpl->Set('MESSAGE', '<div class="error">Es wurde keine Weiterleitungs-URL &uuml;bergeben</div>');
    }

    $urlParts = parse_url($url);
    if (!is_array($urlParts)) {
      $this->app->Tpl->Set('MESSAGE', '<div class="error">Die &uuml;bergebene Weiterleitungs-URL is ung&uuml;ltig.</div>');
    }
    if (empty($urlParts['scheme']) && empty($urlParts['host'])) {
      $this->app->Tpl->Set('MESSAGE', '<div class="error">Die &uuml;bergebene Weiterleitungs-URL ist unvollst&auml;ndig.</div>');
    }
    if (!empty($urlParts['scheme']) && !in_array($urlParts['scheme'], ['http', 'https'])) {
      $this->app->Tpl->Set('MESSAGE', '<div class="warning">Die Weiterleitungs-URL führt nicht auf eine Webseite.</div>');
    }

    $this->app->Tpl->Set('REDIRECT_URL_LINK', $url);
    $this->app->Tpl->Set('REDIRECT_URL_TEXT', htmlspecialchars($url));
    $this->app->Tpl->Parse('PAGE','welcome_redirect.tpl');
  }

  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public function TableSearch(&$app, $name, $erlaubtevars)
  {
    $result = [];
    switch ($name) {
      case 'welcome_spooler':
        $id = (int)$this->app->Secure->GetGET('id');

        $aligncenter = array(1);
        $heading = array('', 'Zeit', 'Dateiname', 'Bearbeiter', 'Gedruckt', 'Men&uuml;');
        $width = array('1%', '30%', '30%', '20%', '10%', '5%');
        $findcols = array('d.id', 'd.zeitstempel', 'd.filename', 'a.name', 'd.gedruckt', 'd.id');
        $searchsql = array("DATE_FORMAT(d.zeitstempel,'%d.%m.%Y %H:%i:%s')", 'd.filename', 'a.name');

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" .
          "<a href=\"index.php?module=welcome&action=spooler&cmd=download&file=%value%\" data-cmd=\"download\">" .
          "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/download.svg\" border=\"0\"></a>" .
          "&nbsp;" .
          "<a href=\"index.php?module=welcome&action=spooler&cmd=delete&file=%value%\" data-cmd=\"delete\">" .
          "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" .
          "&nbsp;</td></tr></table>";

        $nichtGedruckt = (int)$app->YUI->TableSearchFilter($name, 1, 'nicht_gedruckt', '0', 0, 'checkbox');
        if ($nichtGedruckt === 1) {
          $subwhere = ' AND d.gedruckt = 0 ';
        } else {
          $subwhere = '';
        }

        // SQL statement
        $sql = "SELECT 
                  SQL_CALC_FOUND_ROWS d.id, 
                  CONCAT('<input type=\"checkbox\" name=\"selection[]\" value=\"', d.id, '\">'),
                  DATE_FORMAT(d.zeitstempel,'%d.%m.%Y %H:%i:%s'), 
                  IF(d.filename != '', d.filename, 'Kein Dateiname vorhanden'), 
                  a.name,
                  IF(d.gedruckt = 1, 'ja', '') as gedruckt,
                  d.id 
                FROM drucker_spooler AS d 
                LEFT JOIN `user` AS u ON u.id = d.user 
                LEFT JOIN adresse AS a ON a.id = u.adresse ";
        $where = " d.drucker = '{$id}' " . $subwhere;
        $count = "SELECT COUNT(d.id) FROM drucker_spooler AS d WHERE " . $where;

        $result = [
          'aligncenter' => $aligncenter,
          'heading' => $heading,
          'width' => $width,
          'findcols' => $findcols,
          'searchsql' => $searchsql,
          'menu' => $menu,
          'where' => $where,
          'sql' => $sql,
          'count' => $count,
          'maxrows' => 50,
        ];

        break;
    }

    // Nicht erlaubt Keys aus Result entfernen
    foreach ($result as $key => $value) {
      if (!in_array($key, $erlaubtevars, true)) {
        unset($result[$key]);
      }
    }

    return !empty($result) ? $result : [];
  }

  public function WelcomeSpooler()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    $fileId = (int)$this->app->Secure->GetGET('file');
    $printerId = (int)$this->app->Secure->GetGET('id');
    if ($printerId === 0) {
      $printerId = null;
    }

    // Zip erstellen
    if (!empty($this->app->Secure->GetPOST('makezip'))) {
      try {
        $selection = $this->app->Secure->GetPOST('selection');
        $this->DownloadSpoolerZipCompilation($selection, $printerId);
        $this->app->erp->ExitWawi();
      } catch (DownloadSpoolerExceptionInterface $e) {
          $notification = $this->GetNotificationService();
          $notification->create(
            $this->app->User->GetID(), 'error', 'Download-Drucker',
            'Zip-Datei konnte nicht erstellt werden. Fehler: ' . $e->getMessage()
          );
      }
    }

    // Sammel-PDF erstellen
    if (!empty($this->app->Secure->GetPOST('makepdf'))) {
      try {
        $selection = $this->app->Secure->GetPOST('selection');
        $this->DownloadSpoolerPdfCompilation($selection, $printerId);
        $this->app->erp->ExitWawi();
      } catch (DownloadSpoolerExceptionInterface $e) {
        $notification = $this->GetNotificationService();
        $notification->create(
          $this->app->User->GetID(), 'error', 'Download-Drucker',
          'Sammel-PDF konnte nicht erstellt werden. Fehler: ' . $e->getMessage()
        );
      }
    }

    // Alle "noch nicht gedruckte" Dateien herunterladen
    if($cmd === 'download-unprinted'){
      try {
        $this->DownloadSpoolerUnprintedFiles($printerId);
        $this->app->erp->ExitWawi();
      } catch (DownloadSpoolerExceptionInterface $e) {
        $notification = $this->GetNotificationService();
        $notification->create(
          $this->app->User->GetID(), 'error', 'Download-Drucker',
          'Dateien konnte nicht heruntergeladen werden. Fehler: ' . $e->getMessage()
        );
      }
    }

    // Einzelne Datei runterladen
    if($cmd === 'download-file'){
      try {
        $this->DownloadSpoolerFile($fileId);
        $this->app->erp->ExitWawi();
      } catch (DownloadSpoolerExceptionInterface $e) {
        $notification = $this->GetNotificationService();
        $notification->create(
          $this->app->User->GetID(), 'error', 'Download-Drucker',
          'Datei konnte nicht heruntergeladen werden. Fehler: ' . $e->getMessage()
        );
      }
    }

    // Einzelne Datei löschen
    if($cmd === 'delete-file'){
      try {
        $isFileDeleted = $this->DeleteSpoolerFile($fileId, $printerId);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => $isFileDeleted]);
        $this->app->erp->ExitWawi();
      } catch (DownloadSpoolerExceptionInterface $e) {
        $notification = $this->GetNotificationService();
        $notification->create(
          $this->app->User->GetID(), 'error', 'Download-Drucker',
          'Datei konnte nicht gelöscht werden. Fehler: ' . $e->getMessage()
        );
      }
    }

    // DataTable-HTML-Struktur
    if ($cmd === 'datatable-html') {
      $table = new DownloadSpoolerTable($this->app, $printerId);
      $settings = $table->GetSettings(sprintf('./index.php?module=welcome&action=spooler&cmd=datatable-data&id=%s', $printerId));

      header('Content-Type: text/html; charset=utf-8');
      echo $table->GetContentHtml();
      echo '<script type="application/json" id="downloadspooler-table-settings">';
      echo json_encode($settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
      echo '</script>';
      $this->app->erp->ExitWawi();
    }

    // DataTable-Daten
    if ($cmd === 'datatable-data') {
      try {
        $data = $this->DownloadSpoolerDataTableResult($printerId);
      } catch (Exception $e) {
        header('HTTP/1.1 404 Not Found');
        $data = ['success' => false, 'error' => $e->getMessage()];
      }
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($data);
      $this->app->erp->ExitWawi();
    }
  }

  /**
   * @param int $printerId
   *
   * @throws RuntimeException
   *
   * @return array
   */
  protected function DownloadSpoolerDataTableResult($printerId)
  {
    $columns = (array)$this->app->Secure->GetGET('columns');
    $search = (array)$this->app->Secure->GetGET('search');
    $order = (array)$this->app->Secure->GetGET('order');
    $offset = (int)$this->app->Secure->GetGET('start');
    $limit = (int)$this->app->Secure->GetGET('length');
    $draw = (int)$this->app->Secure->GetGET('draw');

    if ((int)$printerId === 0) {
      throw new RuntimeException('Printer-ID darf nicht leer sein');
    }

    foreach ($columns as $column) {
      if ($column['data'] === 'gedruckt') {
        if (!empty($column['search']['value'])) {
          $filter['ungedruckt'] = true;
        } else {
          $filter['ungedruckt'] = false;
        }
        if ($draw === 1) {
          $filter['ungedruckt'] = true;
        }
      }
    }

    $table = new DownloadSpoolerTable($this->app, $printerId);
    $searchQuery = !empty($search['value']) ? $search['value'] : null;
    $orderCol = (int)$order[0]['column'];
    $orderDir = strtolower($order[0]['dir']) === 'desc' ? 'DESC' : 'ASC';

    return $table->GetData($filter, $searchQuery, $orderCol, $orderDir, $offset, $limit, $draw);
  }

  /**
   * Alle "noch nicht gedruckten" Dateien herunterladen
   *
   * @param int|null $printerId
   *
   * @return void
   */
  protected function DownloadSpoolerUnprintedFiles($printerId = null)
  {
    $gateway = $this->GetDownloadSpoolerGateway();
    $unprinted = $gateway->getUnprintedFileIdsByUser($this->app->User->GetID());
    if (empty($unprinted)) {
      return;
    }

    // Einzelne Datei herunterladen
    if (count($unprinted) === 1) {
      $this->DownloadSpoolerFile($unprinted[0]);
    } else {
      $this->DownloadSpoolerZipCompilation($unprinted, $printerId);
    }
  }

  /**
   * Einzelne Druckerspooler-Datei herunterladen
   *
   * @param int $fileId
   *
   * @throws DownloadSpoolerExceptionInterface
   *
   * @return void
   */
  protected function DownloadSpoolerFile($fileId)
  {
    $service = $this->GetDownloadSpoolerService();
    $data = $service->fetchFile($fileId, $this->app->User->GetID());

    $rawData = base64_decode($data['content']);
    if(!empty($data['filename'])){
      $filename = urlencode($data['filedate'] . '-' . $data['filename']);
    } else{
      $filename = urlencode($data['filedate']);
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Type: application/force-download');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');
    header('Pragma: public');
    header('Content-Length: ' . strlen($rawData));
    echo $rawData;
    $this->app->erp->ExitWawi();
  }

  /**
   * @param array|int[] $spoolerIds
   * @param int|null    $printerId
   *
   * @return void
   */
  protected function DownloadSpoolerZipCompilation($spoolerIds, $printerId = null)
  {
    $downloadSpooler = $this->GetDownloadSpoolerService();
    $zipPath = $downloadSpooler->createZipCompilation($spoolerIds, $this->app->User->GetID(), $printerId);
    $zipName = 'DOWNLOAD_SPOOLER_' . date('Y-m-d') . '.zip';

    // Download ZIP
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename=' . $zipName);
    header('Content-Length: ' . filesize($zipPath));
    readfile($zipPath);
    unlink($zipPath);
    $this->app->erp->ExitWawi();
  }

  /**
   * @param array|int[] $spoolerIds
   * @param int|null    $printerId
   *
   * @return void
   */
  protected function DownloadSpoolerPdfCompilation($spoolerIds, $printerId = null)
  {
    $downloadSpooler = $this->GetDownloadSpoolerService();
    $pdfPath = $downloadSpooler->createPdfCompilation($spoolerIds, $this->app->User->GetID(), $printerId);
    $pdfName = 'DOWNLOAD_SPOOLER_' . date('Y-m-d') . '.pdf';

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header("Content-Type: application/force-download");
    header('Content-Disposition: attachment; filename=' . $pdfName);
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($pdfPath));
    readfile($pdfPath);
    unlink($pdfPath);
    $this->app->erp->ExitWawi();
  }

  /**
   * @param int $fileId
   * @param int $printerId
   *
   * @return bool
   */
  protected function DeleteSpoolerFile($fileId, $printerId)
  {
    $downloadSpooler = $this->GetDownloadSpoolerService();

    return $downloadSpooler->deleteFile($fileId, $printerId);
  }

  /**
   * @param string $dir
   *
   * @return bool
   */
  protected function DelFolder($dir) {
    $files = array_diff(scandir($dir,SCANDIR_SORT_NONE), array('.','..'));
    if(!empty($files)){
      foreach ($files as $file) {
        if(is_dir($dir . '/' . $file)){
          $this->DelFolder($dir . '/' . $file);
        }elseif(is_file($dir . '/' . $file)){
          @unlink($dir . '/' . $file);
        }
      }
    }
    return is_dir($dir) && @rmdir($dir);
  } 

  public function WelcomeInfo()
  {
    if($this->app->Secure->GetPOST('restoreoss') && is_dir(dirname(dirname(__DIR__)).'/www_oss') &&
      is_dir(dirname(dirname(__DIR__)) . '/phpwf_oss') && is_dir(dirname(dirname(__DIR__)) . '/phpwf_oss/types') &&
      $this->app->User->GetType() === 'admin')
    {
      chdir('..');
      $dir = getcwd();
      if(@rename($dir.'/www', $dir.'/www_old') && @rename($dir.'/phpwf', $dir.'/phpwf_old'))
      {
        sleep(1);
        if(@rename($dir.'/www_oss', $dir.'/www') && @rename($dir.'/phpwf_oss', $dir.'/phpwf'))
        {
          if(is_file($dir.'/version_oss.php'))
          {
            @rename($dir.'/version.php',$dir.'/version_old.php');
            if(@rename($dir.'/version_oss.php',$dir.'/version.php'))@unlink($dir.'/version_old.php');
          }
          if(is_dir($dir.'/www_old') && is_dir($dir.'/www')){
            $this->DelFolder($dir . '/www_old');
          }
          if(is_dir($dir.'/phpwf_old') && is_dir($dir.'/phpwf')){
            $this->DelFolder($dir . '/phpwf_old');
          }
          header('Location: index.php?module=welcome&action=info&msg='.$this->app->erp->base64_url_encode('<div class="info">Die Open Source-Version wurde wiederhergestellt</div>'));
          exit;
        }
      }
      header('Location: index.php?module=welcome&action=info&msg='.$this->app->erp->base64_url_encode('<div class="error">Die Open Source-Version konnte nicht wiederhergestellt werden. Bitte pr&uuml;fen Sie die Dateirechte!</div>'));
      exit;
    }

    $this->app->erp->Headlines('Informationen zur Software');

    $this->app->Tpl->Set('TABTEXT','Informationen zur Software');
    $this->app->Tpl->Set('TAB1','<fieldset><legend>Lizenzinformationen</legend><table><tr><td>');
    if($this->app->erp->Version()!=='OSS')
    {
      $this->app->Tpl->Add('TAB1',"Sie benutzen die kommerzielle Version von Xentral. Alle Rechte vorbehalten. Beachten Sie die Nutzungsbedinungen.<br><br>&copy; Copyright by Xentral ERP Software GmbH Augsburg");
    }
    else {
      $this->app->Tpl->Add('TAB1',"Sie benutzen die Open-Source Version von Xentral. Die Software steht unter der AGPLv3.0 (<a href=\"https://xentral.biz/lizenzhinweis\" target=\"_blank\">Hinweis</a>).<br><br><div class=\"info\">Das Logo und der Link zur Homepage <a href=\"https://xentral.biz\" target=\"_blank\">https://xentral.biz</a> d&uuml;rfen
          nicht entfernt werden.</div><br>&copy; Copyright by Xentral ERP Software GmbH Augsburg");
    }

    if($this->app->erp->isIoncube() && method_exists($this->app->erp, 'IoncubeProperty'))
    {
      if(method_exists('erpAPI','Ioncube_Property'))
      {
        $hinweis = erpAPI::Ioncube_Property('versionshinweis');
      }else{ 
        $hinweis = $this->app->erp->IoncubeProperty('versionshinweis');
      }
      if($hinweis){$hinweis = ' ('.$hinweis.')';}else{$hinweis = '';}
      if(method_exists('erpAPI', 'Ioncube_HasExpired'))
      {
        $hasexpired = erpAPI::Ioncube_HasExpired();
      }else{
        $hasexpired = $this->app->erp->IoncubeHasExpired();
      }
      if(method_exists('erpAPI', 'Ioncube_ExpireDate'))
      {
        $expiredate = erpAPI::Ioncube_ExpireDate();
      }else{      
        $expiredate = $this->app->erp->IoncubeExpireDate();
      }
      
      if($hasexpired && is_dir(dirname(dirname(__DIR__)).'/www_oss') && is_dir(dirname(dirname(__DIR__)) . '/phpwf_oss') && is_dir(dirname(dirname(__DIR__)) . '/phpwf_oss/types')  && $this->app->User->GetType() == 'admin')
      {
        if(is_dir(dirname(dirname(__DIR__)).'/www_oss') && is_dir(dirname(dirname(__DIR__)) . '/phpwf_oss') && is_dir(dirname(dirname(__DIR__)) . '/phpwf_oss/types')  && $this->app->User->GetType() == 'admin')
        {
          $this->app->Tpl->Add('TAB1','<form method="post"><div class="info">
            <input type="checkbox" value="1" name="restoreoss" /> {|Wieder auf Open-Source Version wechseln|}<br />
            Hinweis: Bitte sichern Sie wenn Sie eigene Quelltexte in Xentral hinterlegt haben diese gesondert. Es werden alle fremden Quelltextdateien entfernt.<br />
            <input type="submit" style="margin:5px;" value="{|Jetzt auf Open-Source Version wechseln|}" /><div style="clear:both;"></div>
            </div></form>');
        }
      }
    }

    $tmp = file_get_contents('../LICENSE');

    $phpmailer = file_get_contents('../www/plugins/phpmailer/LICENSE');
    $this->app->Tpl->Add('PHPMAILER',nl2br($phpmailer));
  
    $this->app->Tpl->Add('TAB1',nl2br($tmp));

    $this->app->Tpl->Add('TAB1','</td></tr>');

    if($this->app->erp->isIoncube() && method_exists($this->app->erp, 'IoncubeProperty'))
    {
      $first = true;
      if(method_exists('erpAPI','Ioncube_getMaxUser'))
      {
        $maxuser = erpAPI::Ioncube_getMaxUser();
      }else{
        $maxuser = $this->app->erp->IoncubegetMaxUser();
      }
      $maxlightuser = 0;
      $maxlightuserrechte = 0;
      $anzahllightuser = 0;

      if(method_exists('erpAPI','Ioncube_getMaxLightusers') && method_exists('erpAPI','Ioncube_getMaxLightusersRights'))
      {
        $maxlightuser = erpAPI::Ioncube_getMaxLightusers();
        $maxlightuserrechte = erpAPI::Ioncube_getMaxLightusersRights();
      }
      $mitarbeiterzeiterfassung = $this->app->erp->ModulVorhanden('mitarbeiterzeiterfassung')?$maxuser:0;

      if($maxuser) {
        $anzuser2 = 0;
        if($maxlightuser > 0) {
          $anzuser2 = (int)$this->app->DB->Select("SELECT count(DISTINCT u.id) FROM `user` u WHERE activ = 1 AND type = 'lightuser' ");
          $anzahllightuser = $anzuser2;
          $anzuser = (int)$this->app->DB->Select("SELECT count(id) FROM `user` WHERE activ = 1 AND not isnull(hwtoken) AND hwtoken <> 4") - $anzuser2;
          $anzuserzeiterfassung = (int)$this->app->DB->Select("SELECT count(*) from user where activ = 1 AND hwtoken = 4 AND type != 'lightuser'");
        }else{
          $anzuser = (int)$this->app->DB->Select("SELECT count(*) from user where activ = 1 AND hwtoken <> 4 ");
          $anzuserzeiterfassung = (int)$this->app->DB->Select("SELECT count(*) from user where activ = 1 AND hwtoken = 4");
        }

        $userred = $anzuser > $maxuser
          || (
            ($anzuser + $anzuserzeiterfassung + $anzuser2) >
            $mitarbeiterzeiterfassung + $maxuser + $maxlightuser
          )
          || (($anzuser + $anzuserzeiterfassung) > $mitarbeiterzeiterfassung + $maxuser);


        $this->app->Tpl->Add(
          'TAB1',
          '<tr><td><div' . ($userred ? ' style="color:red;" ' : '') . '>Benutzer ' .
          ($anzuser + $anzahllightuser + $anzuserzeiterfassung) .
          ($maxlightuser > 0 || $anzuserzeiterfassung > 0?' (davon ':'').
          ($maxlightuser > 0 ?  $anzahllightuser . ' Light-User' : '') .
          ($maxlightuser > 0 && $anzuserzeiterfassung > 0?', ':'').
          ($anzuserzeiterfassung > 0 ? $anzuserzeiterfassung . ' Zeiterfassung-User' : '') .
          ($maxlightuser > 0 || $anzuserzeiterfassung > 0?')':'').
          ' von ' .
          ($maxuser + $maxlightuser + $mitarbeiterzeiterfassung) .
          ($maxlightuser > 0 || $mitarbeiterzeiterfassung > 0?' (davon ':'').
          ($maxlightuser > 0 ? $maxlightuser . ' Light-User' : '') .
          ($maxlightuser > 0 && $mitarbeiterzeiterfassung > 0?', ':'').
          ($mitarbeiterzeiterfassung > 0 ? $mitarbeiterzeiterfassung . ' Zeiterfassung-User' : '') .
          ($maxlightuser > 0 || $mitarbeiterzeiterfassung > 0?')':'').
          '</div></td></tr>'
        );
      }

      if(method_exists('erpAPI','Ioncube_Property'))
      {
        $hinweis = erpAPI::Ioncube_Property('versionshinweis');
      }else{ 
        $hinweis = $this->app->erp->IoncubeProperty('versionshinweis');
      }
      if($hinweis){
        $hinweis = ' ('.$hinweis.')';
      }else{
        $hinweis = '';
      }
      if(method_exists('erpAPI', 'Ioncube_HasExpired'))
      {
        $hasexpired = erpAPI::Ioncube_HasExpired();
      }else{
        $hasexpired = $this->app->erp->IoncubeHasExpired();
      }
      if(method_exists('erpAPI', 'Ioncube_ExpireDate'))
      {
        $expiredate = erpAPI::Ioncube_ExpireDate();
      }else{      
        $expiredate = $this->app->erp->IoncubeExpireDate();
      }
      if(method_exists('erpAPI', 'Ioncube_BeforeExpire'))
      {
        $ioncubebeforeexpire = erpAPI::Ioncube_BeforeExpire();
      }else{
        $ioncubebeforeexpire = $this->app->erp->IoncubeBeforeExpire();
      }
      
      if($hasexpired)
      {
        $first = false;
        $this->app->Tpl->Add('TAB1','<tr><td><div style="color:red;">Ihre Lizenz ist am '.$expiredate.' abgelaufen'.$hinweis.'.</div></td></tr>');
      } elseif($ioncubebeforeexpire) {
        $first = false;
        $this->app->Tpl->Add('TAB1','<tr><td><div style="color:red;">Ihre Lizenz l&auml;uft am '.$expiredate.' ab'.$hinweis.'.</div></td></tr>');
      } elseif($expiredate) {
        $first = false;
        $this->app->Tpl->Add('TAB1','<tr><td><div>Die Lizenz l&auml;uft am '.$expiredate.' ab'.$hinweis.'.</div></td></tr>');
      }
    }

    if(method_exists($this->app->erp, 'VersionsInfos'))
    {
      $ver = $this->app->erp->VersionsInfos();
      $this->app->Tpl->Add('TAB1','<tr><td>'.$ver['Details'].'</td></tr>');
    }
    $this->app->Tpl->Add('TAB1','</table></fieldset>');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }


  public function WelcomeMenu()
  {
    $this->app->Tpl->Add('KURZUEBERSCHRIFT','<h2>Startseite</h2>');
  }


  public function WelcomeMain()
  {

    $this->app->Tpl->Set('UEBERSCHRIFT','Herzlich Willkommen '.$this->app->User->GetDescription().'!');
    $this->WelcomeMenu();

    // muss jeder sehen
    $this->app->erp->LagerAusgehend('ARTIKEL');

    $this->app->Tpl->Parse('PAGE','welcome_main.tpl');
  }


  public function WelcomeStartseite()
  {
    $this->app->erp->Startseite();
  }

  public function WelcomeLogin()
  {
    
    if($this->app->User->GetID()!='')
    {
      // alle cookies SpryMedia loeschen

      // Setzen des Verfalls-Zeitpunktes auf 1 Stunde in der Vergangenheit
      $this->app->erp->ClearCookies();
      $startseite = '';
      if($code = $this->app->Secure->GetPOST('code'))
      {
        $result = $this->app->DB->SelectArr("SELECT url, reduziert FROM stechuhrdevice WHERE code = '$code' AND aktiv = 1 LIMIT 1");

        $startseite = $result[0]['url'] ;
        $isReduziert = $result[0]['reduziert'];

        if($isReduziert){
          $this->app->User->SetParameter('stechuhrdevicereduziert',true);
        }

        if($isReduziert && empty($startseite)){
          $startseite = 'index.php?module=stechuhr&action=list&prodcmd=arbeitsschritt';
        }
      }
      $this->app->erp->Startseite($startseite);
    }
    else
    {
      $this->app->erp->InitialSetup();
      $this->app->Tpl->Set('UEBERSCHRIFT','Xentral &middot; Enterprise Warehouse Management');

      $this->app->acl->Login();
    }
  }

  public function WelcomeLogout()
  {
    $this->app->acl->Logout();
    $this->app->erp->ClearCookies();
  }

  public function WelcomeUnlock()
  {
    $gui = $this->app->Secure->GetGET('gui');
    $id =  $this->app->Secure->GetGET('id');
    $backlink = $this->app->Secure->GetGET('backlink');

    // Prüfen ob Backlink mit index.php? beginnt; ansonsten ist Open Redirect möglich
    if (!empty($backlink) && strpos($backlink, 'index.php?') !== 0){
      unset($backlink);
    }

    // sperre entfernen bzw umschreiben
    if($gui==='angebot' || $gui==='auftrag' || $gui==='rechnung' || $gui==='bestellung' || $gui==='gutschrift' || $gui==='lieferschein' || $gui==='retoure' || $gui==='adresse' || $gui==='artikel' || $gui==='produktion' || $gui==='reisekosten' || $gui==='preisanfrage')
    {
      $this->app->DB->Update("UPDATE $gui SET usereditid='".$this->app->User->GetID()."'  WHERE id='$id' LIMIT 1");
      if(!empty($backlink))
      {
        header('Location: '.$backlink);
      }else{
        header("Location: index.php?module=$gui&action=edit&id=$id");
      }
      exit;
    }
  }


  public function VorgangAnlegen()
  {
    //print_r($_SERVER['HTTP_REFERER']);
    $titel = $this->app->Secure->GetGET('titel');

    $url = parse_url($_SERVER['HTTP_REFERER']);
    //$url = parse_url("http://dev.eproo.de/~sauterbe/eprooSystem-2009-11-21/webroot/index.php?module=ticket&action=edit&id=1");

    //module=ticket&action=edit&id=1
    //$url['query']
    $params = explode('&',$url['query']);
    foreach($params as $value){
      $attribut = explode('=',$value);
      $arrPara[$attribut[0]] = count($attribut) > 1?$attribut[1]:'';
    }

    $adresse = $this->app->User->GetAdresse();
    if($titel==''){
      $titel = ucfirst($arrPara['module']) . ' ' . $arrPara['id'];
    }
    $href = $url['query'];
    $this->app->erp->AddOffenenVorgang($adresse, $titel, $href);

    header('Location: '.$_SERVER['HTTP_REFERER']);
  }

  /**
   * @param int $cacheTime
   *
   * @return string
   */
  public function loadChangeLogCacheFile(int $cacheTime = 4): string
  {
    $version = $this->app->erp->Version();
    $revision = $this->app->erp->Revision();

    $tmp = explode('.',$revision);
    $branch = strtolower($version).'_'.$tmp[0].'.'.$tmp[1];

    $blogUrl = "https://{$this->app->Conf->updateHost}/wawision_2016.php?all=1&branch=".$branch;
    $cacheFile = $this->app->erp->GetTMP().md5($blogUrl);
    $cacheFile2 = $this->app->erp->GetTMP().md5($blogUrl).'2';
    if(!file_exists($cacheFile2)) {
      if(file_exists($cacheFile)) {
        @unlink($cacheFile);
      }
    }
    else{
      if(trim(file_get_contents($cacheFile2)) != $version.$revision) {
        @unlink($cacheFile);
      }
    }

    if(!file_exists($cacheFile) || ((time() - filemtime($cacheFile)) > 3600 * $cacheTime)) {
      if($feed_contents = @file_get_contents($blogUrl)) {
        $fp = fopen($cacheFile, 'w');
        fwrite($fp, $feed_contents);
        fclose($fp);
        @file_put_contents($cacheFile2, $version.$revision);
      }
    }

    return $cacheFile;
  }

  /**
   * @param int $hours
   */
  public function loadChangeLogByTime(int $hours = 4): void
  {
    $lastTime = (int)$this->app->erp->GetKonfiguration('welcome_changelog_last_save');
    if($lastTime > 0 && time() - $lastTime < $hours * 3600) {
      return;
    }

    $this->loadArrFromChangeLogFile();
  }

  /**
   * @return array
   */
  public function loadArrFromChangeLogFile(): array
  {
    $file = $this->loadChangeLogCacheFile();
    $this->app->erp->SetKonfigurationValue('welcome_changelog_last_save', time());
    if(!is_file($file)){
      $this->app->erp->SetKonfigurationValue('welcome_changelog_count', 0);
      return ['changelog' => [], 'act' => [], 'found' => false, 'types' => [], 'count_new' => 0,];
    }
    $content = file_get_contents($file);
    if(empty($content)) {
      $this->app->erp->SetKonfigurationValue('welcome_changelog_count', 0);
      return ['changelog' => [], 'act' => [], 'found' => false, 'types' => [], 'count_new' => 0,];
    }
    $xml = simplexml_load_string($content);
    if(empty($xml)) {
      $this->app->erp->SetKonfigurationValue('welcome_changelog_count', 0);
      return ['changelog' => [], 'act' => [], 'found' => false, 'types' => [], 'count_new' => 0,];
    }
    $json = json_encode($xml);

    $array = json_decode($json, true);
    if(empty($array)) {
      $this->app->erp->SetKonfigurationValue('welcome_changelog_count', 0);
      return ['changelog' => [], 'act' => [], 'found' => false, 'types' => [], 'count_new' => 0,];
    }
    $found = false;
    $akt = [];
    $version_revision = null;
    include dirname(dirname(__DIR__)) .'/version.php';
    if(isset($version_revision) && $version_revision != '') {
      $ra = explode('.', $version_revision);
      if(isset($ra[2]) && $ra[2] != '') {
        $citems = isset($array['channel']['item'])?count($array['channel']['item']):0;
        for($i=0;$i<$citems;$i++) {
          if($found !== false) {
            if(!empty($array['channel']['item'][$i]['title'])){
              $akt['channel']['item'][$i] = $array['channel']['item'][$i];
            }
            unset($array['channel']['item'][$i]);
          }
          else {
            $rev = isset($array['channel']['item'][$i]['guid'])?$array['channel']['item'][$i]['guid']:'';
            if($rev == '') {
              $rev = trim(trim($array['channel']['item'][$i]['title']),')');
              $rev = trim(substr($rev, strrpos($rev, '(')+4));
            }
            if($rev == $ra[2]) {
              $found = $i;
              $akt['channel']['item'][$i] = $array['channel']['item'][$i];
              unset($array['channel']['item'][$i]);
            }
          }
        }
      }
    }
    $citems = isset($array['channel']['item'])?count($array['channel']['item']):0;

    $types = array();
    for($i=0;$i<$citems;$i++) {
      $messageType = explode(' ', ltrim($array['channel']['item'][$i]['description']));
      $messageType = strtolower($messageType[0]);
      if(!array_key_exists($messageType, $types)){
        $types[$messageType] = [];
      }
      $types[$messageType][] = $array['channel']['item'][$i];
    }

    $this->app->erp->SetKonfigurationValue('welcome_changelog_count', $citems);

    return ['changelog' => $array, 'act' => $akt, 'found' => $found, 'types' => $types,  'count_new' => $citems,];
  }

  public function WelcomeChangelog()
  {
    $this->StartseiteMenu();

    $version_revision = null;
    $revision = $this->app->erp->Revision();

    $changeLogArray = $this->loadArrFromChangeLogFile();
    $found = $changeLogArray['found'];
    $array = $changeLogArray['changelog'];
    $akt = $changeLogArray['act'];
    $types = $changeLogArray['types'];

    $this->app->Tpl->Add('TAB1', '<div class="row">
                                  <div class="row-height">
                                  <div class="col-md-12 col-md-height">
                                  <div class="inside inside-full-height">
                                  <fieldset><legend>Neue verf&uuml;gbare Updates</legend>');
    if(!empty($array['channel']) && !empty($array['channel']['item']) && is_array($array['channel']['item'])
      && count($array['channel']['item']) > 0) {
      $listingOrder = ['new', 'add', 'change', 'fix', 'merge'];
      foreach ($listingOrder as $informations){
        if(isset($types[$informations])){
          $this->app->Tpl->Add('TAB1', '<fieldset><legend>'.ucfirst($informations).'</legend><ul>');
          foreach ($types[$informations] as $information){
            $messageDate = DateTime::createFromFormat('Y-m-d', $information['pubDate']);
            $messageDate = $messageDate->format('d.m.Y');
            $this->app->Tpl->Add('TAB1', '<li>'.$information['description'].' ('.$messageDate.' rev '.$information['guid'].')</li>');
          }
          $this->app->Tpl->Add('TAB1', '</ul></fieldset>');
          unset($types[$informations]);
        }
      }

      //dynamic remaining messagetypes for output
      foreach($types as $type=>$informations){
        $this->app->Tpl->Add('TAB1', '<fieldset><legend>'.ucfirst($type).'</legend><ul>');
        foreach ($informations as $value){
          $messageDate = DateTime::createFromFormat('Y-m-d', $value['pubDate']);
          $messageDate = $messageDate->format('d.m.Y');
          $this->app->Tpl->Add('TAB1', '<li>'.$value['description'].' ('.$messageDate.' rev '.$value['guid'].')</li>');
        }
        $this->app->Tpl->Add('TAB1', '</ul></fieldset>');
        unset($types[$type]);
      }
    }
    elseif($found !== false){
      $this->app->Tpl->Add('TAB1', '<div class="info">Ihre Version ist auf dem neuesten Stand.</div>');//<fieldset><legend>Ihre Version ist auf dem neuesten Stand.</legend></fieldset>');
    }
    $this->app->Tpl->Add('TAB1', '</fieldset></div></div></div></div>');


    if(!empty($akt)) {
      $this->app->Tpl->Add('TAB1', '<div class="row">
                                    <div class="row-height">
                                    <div class="col-md-12 col-md-height">
                                    <div class="inside inside-full-height">
                                    <fieldset><legend>Letzte durchgef&uuml;hrte Updates</legend><ul>');
      $citems = isset($akt['channel']['item'])?count($akt['channel']['item']):0;
      for($i=0;$i<$citems;$i++) {
        if(empty($akt['channel']['item'][$i]['title'])){
          continue;
        }
        $messageDate = DateTime::createFromFormat('Y-m-d', $akt['channel']['item'][$i]['pubDate']);
        $messageDate = $messageDate->format('d.m.Y');
        $this->app->Tpl->Add('TAB1', '<li>'.$akt['channel']['item'][$i]['description'].' ('.$messageDate.' rev '.$akt['channel']['item'][$i]['guid'].')</li>');
      }
      $this->app->Tpl->Add('TAB1', '</ul></fieldset></div></div></div></div>');
    }

    $versionNumber = explode('.', $revision);
    $versionNumber = $versionNumber[0].'.'.$versionNumber[1];

    $allChangesInVersionUrl = "https://{$this->app->Conf->updateHost}/xentral_2020.php?branch=".$versionNumber;
    $allChangesInVersionCacheFile = $this->app->erp->GetTMP().md5($allChangesInVersionUrl);
    $allChangesInVersionCacheFile2 = $this->app->erp->GetTMP().md5($allChangesInVersionUrl).'2';

    if(!file_exists($allChangesInVersionCacheFile2)){
      if(file_exists($allChangesInVersionCacheFile)){
        @unlink($allChangesInVersionCacheFile);
      }
    }else{
      if(trim(file_get_contents($allChangesInVersionCacheFile2)) != $versionNumber){
        @unlink($allChangesInVersionCacheFile);
      }
    }
    $allChangesInVersionCacheTime = 4; # hours
    if(!file_exists($allChangesInVersionCacheFile) || ((time() - filemtime($allChangesInVersionCacheFile)) > 3600 * $allChangesInVersionCacheTime)){
      if($feed_contents = @file_get_contents($allChangesInVersionUrl)){
        $fp = fopen($allChangesInVersionCacheFile, 'w');
        fwrite($fp, $feed_contents);
        fclose($fp);
        @file_put_contents($allChangesInVersionCacheFile2, $versionNumber);
      }
    }
    $feed_contents = file_get_contents($allChangesInVersionCacheFile);

    $allChangesInVersion = json_decode($feed_contents, TRUE);
    
    $this->app->Tpl->Add('TAB2', '<div class="row">
                                  <div class="row-height">
                                  <div class="col-md-12 col-md-height">
                                  <div class="inside inside-full-height">
                                  <fieldset><legend>Alle &Auml;nderungen in Version '.$versionNumber.'</legend>');

    $countChanges = isset($allChangesInVersion['new'])?count($allChangesInVersion['new']):0;
    $types = array();
    for($i=0;$i<$countChanges;$i++) {
      $messageType = explode(' ', ltrim($allChangesInVersion['new'][$i]['msg']));
      $messageType = strtolower($messageType[0]);
      if(!array_key_exists($messageType, $types)){
        $types[$messageType] = [];
      }
      $types[$messageType][] = $allChangesInVersion['new'][$i];
    }

    //fixed order of messagetypes for output
    $listingOrder = ['new', 'add', 'change', 'fix', 'merge'];
    foreach ($listingOrder as $informations){
      if(isset($types[$informations])){
        $this->app->Tpl->Add('TAB2', '<fieldset><legend>'.ucfirst($informations).'</legend><ul>');
        foreach ($types[$informations] as $information){
          $messageDate = DateTime::createFromFormat('Y-m-d\TH:i:sP', $information['date']);
          $messageDate = $messageDate->format('d.m.Y');
          $this->app->Tpl->Add('TAB2', '<li>'.$information['msg'].' ('.$messageDate.' rev '.$information['rev'].')</li>');
        }
        $this->app->Tpl->Add('TAB2', '</ul></fieldset>');
        unset($types[$informations]);
      }
    }

    //dynamic remaining messagetypes for output
    foreach($types as $type=>$informations){
      $this->app->Tpl->Add('TAB2', '<fieldset><legend>'.ucfirst($type).'</legend><ul>');
      foreach ($informations as $value){
        $messageDate = DateTime::createFromFormat('Y-m-d\TH:i:sP', $value['date']);
        $messageDate = $messageDate->format('d.m.Y');
        $this->app->Tpl->Add('TAB2', '<li>'.$value['msg'].' ('.$messageDate.' rev '.$value['rev'].')</li>');
      }
      $this->app->Tpl->Add('TAB2', '</ul></fieldset>');
      unset($types[$type]);
    }
    $this->app->Tpl->Add('TAB2', '</fieldset></div></div></div></div>');


    $this->app->Tpl->Set('VERSION', $versionNumber);
    $this->app->Tpl->Parse('PAGE', 'welcome_changelog.tpl');
  }


  public function VorgangEdit()
  {
    $vorgang = $this->app->Secure->GetGET('vorgang');
    $titel = $this->app->Secure->GetGET('titel');
    $this->app->erp->RenameOffenenVorgangID($vorgang,$titel);
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit;
  } 

  public function VorgangEntfernen()
  {
    $vorgang = $this->app->Secure->GetGET('vorgang');
    $this->app->erp->RemoveOffenenVorgangID($vorgang);
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit;
  }

  private function getCurrentDefaultLanguage($fromPost){

    if(empty($fromPost)){
      $fromPost = $this->app->erp->Firmendaten('preferredLanguage');

      if(empty($fromPost)){
        $fromPost = 'deutsch';
      }
    }
    return $fromPost;
  }

  /**
   * Liefert einen String aus HTML-Optionen zurück
   * @param string $fromPost
   * @return string
   */
  private function languageSelectOptions($fromPost=''){

    $select = $this->getCurrentDefaultLanguage($fromPost);

    $out = "";
    $sprachen = $this->getLanguages();

    foreach($sprachen as $sprache) {
      $selected = (($select==$sprache) ? 'selected' : '');
      $out .= "<option value=\"$sprache\" $selected>$sprache</option>";
    }
    return $out;
  }

  /**
   * Liefert einen Array aus Strings zurück. Immer mindestens 'deutsch' enthalten
   * @return array
   */
  private function getLanguages(){

    $sprachen[] = 'deutsch';
    $folder = __DIR__ .'/../../languages';
    if(is_dir($folder))
    {
      $handle = opendir($folder);
      if($handle){
        while($file = readdir($handle))
        {
          if ($file[0] !== '.')
          {
            if(is_dir($folder.'/'.$file) && (file_exists($folder.'/'.$file.'/variablen.php')|| file_exists($folder.'/'.$file.'/variablen_custom.php')))
            {
              if($file == 'german')$file = 'deutsch';
              if(!in_array($file, $sprachen))$sprachen[] = $file;
            }
          }
        }
        closedir($handle);
      }
    }
    return $sprachen;
  }

  /**
   * @param array $arr
   *
   * @return array
   */
  protected function formatOptionArrForVue($arr)
  {
    $ret = [];
    foreach($arr as $key => $value) {
      $ret[] = [
        'text' => $value,
        'value' => $key,
      ];
    }
    return $ret;
  }

  /**
   * @return JsonResponse
   */
  protected function HandleStartClickByClick()
  {
    $isAdminAdmin = $this->app->acl->IsAdminadmin();
    $hasRole = !empty($this->app->User->GetField('role'));
    /** @var Benutzer $userModule */
    $userModule = $this->app->loadModule('benutzer');
    $firstPage = [
      'type' => 'form',
      'submitType' => 'submit',
      'submitUrl' => $isAdminAdmin
        ?'index.php?module=welcome&action=settings&cmd=changepasswordclickbyclick'
        :'index.php?module=welcome&action=settings&cmd=changeroleclickbyclick',
      'icon' => $isAdminAdmin?'password-icon':'add-person-icon',
      'headline' => $isAdminAdmin?'Passwort ändern':'Ihre Rolle',
      'subHeadline' => $isAdminAdmin?'Bitte gib ein Passwort ein und bestätige es mit einer zweiten Eingabe'
        :'Bitte geben Sie Ihre Rolle im Unternehmen ein',
      'form' => [],
      'ctaButtons' => [
        [
          'title' => 'Weiter',
          'type' => 'submit',
          'action' => 'submit'
        ]]
    ];

    if($isAdminAdmin) {
      $firstPage['form'] = [
        [
          'id' => 0,
          'name' => 'set-password-row',
          'inputs' => [
            [
              'type' => 'password',
              'name' => 'setPassword',
              'label' => 'Passwort',
              'validation' => true,
            ]]
        ],
        [
          'id' => 1,
          'name' => 'repeat-password-row',
          'inputs' => [
            [
              'type' => 'password',
              'name' => 'repeatPassword',
              'label' => 'Passwort wiederholen',
              'connectedTo' => 'setPassword',
              'validation' => true,
              'customErrorMsg' => 'Passwörter sind nicht identisch'
            ]]
        ]];
    }

    if(!$hasRole) {

      $firstPage['form'][] = [
        'id' => 2,
        'name' => 'role-row',
        'inputs' => [
          [
            'type' => 'select',
            'name' => 'teamMemberRole',
            'label' => 'Rolle',
            'validation' => false,
            'options' => $this->formatOptionArrForVue($userModule->getRoleOptions())
          ]
        ]
      ];
      $firstPage['form'][] = [
        'id' => 3,
        'name' => 'otherrole-row',
        'inputs' => [
          [
            'type'       => 'text',
            'name'       => 'teamMemberOtherRole',
            'label'      => 'Sonstiges',
            'validation' => false,
          ]
        ]
      ];
    }

    $pages[] = $firstPage;

    if($isAdminAdmin) {
      $isFirstAdmin = $this->app->User->GetType() === 'admin' && $this->app->User->GetName('admin');
      if($isFirstAdmin) {
        $pages[] = [
          'type' => 'form',
          'submitType' => 'submit',
          'submitUrl' => 'index.php?module=welcome&action=settings&cmd=inviteteamclickbyclick',
          'icon' => 'add-person-icon',
          'headline' => 'Lade Dein Team ein',
          'subHeadline' => 'Du kannst bis zu 5 weitere Mitglieder hinzufügen',
          'form' => [
            [
              'id' => 0,
              'name' => 'add-person-row',
              'removable' => true,
              'add' => [
                'allow'=> true,
                'maximum'=> 5,
                'text'=> 'Weitere Mitglieder hinzufügen'
              ],
              'inputs' => [
                [
                  'type' => 'text',
                  'name' => 'teamMemberName',
                  'label' => 'Name',
                  'validation' => false,
                  'customErrorMsg' => 'too short',
                ],
                [
                  'type' => 'email',
                  'name' => 'teamMemberEmail',
                  'label' => 'E-Mail',
                  'validation' => false,
                ],

                [
                  'type' => 'select',
                  'name' => 'teamMemberRole',
                  'label' => 'Rolle',
                  'validation' => false,
                  'options' => $this->formatOptionArrForVue($userModule->getRoleOptions())
                ]
              ]
            ]
          ],
          'ctaButtons' => [
            [
              'title' => 'Weiter',
              'type' => 'submit',
              'action' => 'submit'
            ]]
        ];
      }
    }

    $subHeadline = 'Du kannst nun loslegen.';
    if($isAdminAdmin && !empty(erpAPI::Ioncube_Property('isdevelopmentversion'))) {
      $subHeadline = 'Es handelt sich hier um eine Development-Version. 
      Diese ist nicht für den produktiven Einsatz gedacht.';
    }

    /** @var SystemTemplates $systemTemplates */
    $showExampleImport = !empty(erpAPI::Ioncube_Property('testlizenz'))
      && !empty(erpAPI::Ioncube_Property('iscloud'))
    && $this->app->erp->RechteVorhanden('systemtemplate', 'list')
    && ($systemTemplates = $this->app->loadModule('systemtemplates')) !== null
      && $systemTemplates->canInstallExampleData(false);

    $lastPage = [
      'type' => 'defaultPage',
      'icon' => 'add-person-icon',
      'headline' => $isAdminAdmin ? 'Das Passwort wurde geändert' : 'Ihre Rolle wurde gespeichert',
      'subHeadline' => $subHeadline,
      'ctaButtons' => [
        [
          'title' => 'Klasse',
          'action' => 'close'
        ]
      ]
    ];
    if($showExampleImport) {
      $lastPage['subHeadline'] .= '<br /><a href="index.php?module=systemtemplates&action=list">Beispieldaten einspielen</a>';
    }
    if($isAdminAdmin && $this->app->erp->ModulVorhanden('learningdashboard')){
      $lastPage['ctaButtons']['link'] = 'index.php?module=learningdashboard&action=list';
    }

    $pages[] = $lastPage;

    return new JsonResponse(['success'=>true, 'pages'=>$pages]);
  }

  /**
   * @param int $addressId
   *
   * @return int
   */
  protected function ChangeAddressIdIfCollideWithExampleData(int $addressId): int
  {
    $lastAddressIdInExampleDataset = 8;
    if($addressId > $lastAddressIdInExampleDataset) {
      return $addressId;
    }
    $this->app->DB->Update(
      sprintf(
        'UPDATE `adresse` SET `id` = %d WHERE `id` = %d',
        $lastAddressIdInExampleDataset + 1, $addressId
      )
    );
    if($this->app->DB->affected_rows() > 0) {
      $addressId = $lastAddressIdInExampleDataset + 1;
    }

    return $addressId;
  }

  /**
   * @param int $userId
   *
   * @return int
   */
  protected function ChangeUserIdIfCollideWithExampleData(int $userId): int
  {
    $lastUserIdInExampleDataset = 3;
    if($userId > $lastUserIdInExampleDataset) {
      return $userId;
    }
    $this->app->DB->Update(
      sprintf(
        'UPDATE `user` SET `id` = %d WHERE `id` = %d',
        $lastUserIdInExampleDataset + 1 , $userId
      )
    );
    if($this->app->DB->affected_rows() > 0) {
      $userId = $lastUserIdInExampleDataset + 1;
    }

    return $userId;
  }

  /**
   * @return JsonResponse
   */
  protected function HandleInviteTeamClickByClick()
  {
    $userNames = [];
    $members = [];
    $needToPreventExampleImportFailure = !empty(erpAPI::Ioncube_Property('testlizenz'))
      && !empty(erpAPI::Ioncube_Property('iscloud'));
    for($i = 0; $i < 5; $i++) {
      $userName = $this->app->Secure->GetPOST('teamMemberName'.($i > 0?(string)$i:''));
      if(empty($userName)) {
        continue;
      }
      if(in_array($userName, $userNames)) {
        return new JsonResponse(['error'=>'Usernamen sind identisch'],JsonResponse::HTTP_BAD_REQUEST);
      }
      $userNames[] = $userName;
      $userEmail = $this->app->Secure->GetPOST('teamMemberEmail'.($i > 0?(string)$i:''));
      if(empty($userEmail)) {
        return new JsonResponse(['error'=>'Bitte füllen Sie die Email-Adresse aus'],JsonResponse::HTTP_BAD_REQUEST);
      }
      $userRole = $this->app->Secure->GetPOST('teamMemberRole'.($i > 0?(string)$i:''));
      if(
        $this->app->DB->Select(sprintf("SELECT COUNT(`id`) FROM `user` WHERE `username` = '%s'", $userName)) > 0
        || $this->app->DB->Select(sprintf("SELECT COUNT(`id`) FROM `adresse` WHERE `name` = '%s'", $userName)) > 0
      ) {
        return new JsonResponse(
          ['error'=>sprintf('Usernamen %s existiert bereits', $userName)],
          JsonResponse::HTTP_BAD_REQUEST
        );
      }
      $members[] = ['username' => $userName, 'email' => $userEmail, 'role' => $userRole];
    }
    if(empty($members)) {
      return new JsonResponse(['success'=>true]);
    }
    $projectId = $this->app->DB->Select(
      'SELECT `id` FROM `projekt` WHERE `geloescht` = 0 ORDER BY `oeffentlich` DESC LIMIT 1'
    );
    foreach($members as $member) {
      $password = $member['username'];
      $this->app->DB->Insert(
        sprintf(
          "INSERT INTO `adresse` (`name`, `email`, `projekt`)
            VALUES ('%s', '%s', %d)",
          $member['username'], $member['email'], $projectId
        )
      );
      $addressId = (int)$this->app->DB->GetInsertID();
      if($needToPreventExampleImportFailure) {
        $addressId = $this->ChangeAddressIdIfCollideWithExampleData($addressId);
      }
      $this->app->erp->AddRolleZuAdresse($addressId, 'Mitarbeiter', 'von', 'Projekt', $projectId);
      $vorlage =
        $this->app->DB->real_escape_string(
          $this->app->DB->Select(
            sprintf(
              "SELECT `bezeichnung` FROM `uservorlage` WHERE `bezeichnung` = '%s'",
              $member['role']
            )
          )
        );
      $this->app->DB->Insert(
        sprintf(
          "INSERT INTO `user`
            (`username`, `passwordmd5`, `hwtoken`, `type`, `repassword`, `externlogin`,`firma`,`fehllogins`, 
             `adresse`,`standarddrucker`,`settings`, `activ`,`vorlage`,`role`) 
            VALUES ('%s', '%s', 0, 'benutzer', 0, 1,1,0,
                    %d,0,'',1,'%s','%s')",
          $member['username'], md5($password),
          $addressId, $vorlage, $member['role']
        )
      );
      $newUserId = (int)$this->app->DB->GetInsertID();
      if($needToPreventExampleImportFailure){
        $newUserId = $this->ChangeUserIdIfCollideWithExampleData($newUserId);
      }
      $this->app->erp->insertDefaultUserRights($newUserId);
      if($vorlage !== '') {
        $this->app->erp->AbgleichBenutzerVorlagen($newUserId);
      }
      $link = $this->app->Location->getServer();
      $this->app->erp->MailSend(
        $this->app->erp->Firmendaten('email'),
        $this->app->erp->Firmendaten('absendername'),
        $member['email'],
        $member['username'],
        'Einladung',
        sprintf(
          'Hallo %s,<br />
          <br />
          Willkommen auf Xentral.<br />
          <br />
          Du kannst dich mit den folgenden Zugangsdaten einloggen<br /><br />
          Username: %s<br />
          Passwort: %s<br />
          <a href="%s" style="margin-top:24px;display:inline-block;padding:10px 23px;color:#fff;background:#2DCA73;border-radius:4px;font-size:15px;font-weight:600;text-decoration:none;cursor:pointer">Hier gehts los</a>',
          $member['username'],
          $member['username'],
          $password,
          $link
        ),
        '',0,true,'','',
        true
      );
    }

    return new JsonResponse(['success'=>true]);
  }

  /**
   * @return JsonResponse
   */
  protected function HandleChangeRoleClickByClick()
  {
    $role = $this->app->Secure->GetPOST('teamMemberRole');
    if(empty($role)) {
      return new JsonResponse(
        ['error'=>'Bitte eine Rolle angeben!'],
        JsonResponse::HTTP_BAD_REQUEST
      );
    }
    $this->app->DB->Update(
      sprintf(
        "UPDATE `user` SET `role` = '%s' WHERE `id` = %d",
        $role, $this->app->User->GetID()
      )
    );

    return new JsonResponse(['success'=>true]);
  }

  /**
   * @return JsonResponse
   */
  protected function HandlePasswordChangeClickByClick()
  {
    $password = $this->app->Secure->GetPOST('setPassword');
    $repassword = $this->app->Secure->GetPOST('repeatPassword');
    $role = (string)$this->app->Secure->GetPOST('teamMemberRole');
    $otherRole = (string)$this->app->Secure->GetPOST('teamMemberOtherRole');
    $hasUserRole = !empty($this->app->User->GetField('role'));
    if($otherRole !== '' && ($role === '' || $role === 'Sonstiges')) {
      $role = $otherRole;
    }
    if(!$hasUserRole && $role === ''
      && (string)$this->app->DB->Select(
        sprintf(
          'SELECT `role` FROM `user` WHERE `id` = %d', $this->app->User->GetID()
        )
      )
    ) {
      return new JsonResponse(
        ['error'=>'Bitte gebe eine Rolle ein!'],
        JsonResponse::HTTP_BAD_REQUEST
      );
    }
    $passwordunescaped = $this->app->Secure->GetPOST('setPassword', '', '', 'noescape');
    if(empty($password)) {
      return new JsonResponse(
        ['error'=>'Passworteingabe falsch! Bitte gebe ein Passwort ein!'],
        JsonResponse::HTTP_BAD_REQUEST
      );
    }

    if(strlen($password) < 8) {
      return new JsonResponse(
        ['error'=>'Passworteingabe falsch! Das Passwort muss mindestens 8 Zeichen enthalten!'],
        JsonResponse::HTTP_BAD_REQUEST
      );
    }

    if($password !== $repassword){
      return new JsonResponse(
        ['error'=>'Passworteingabe falsch! Bitte zwei mal das gleiche Passwort eingeben!'],
        JsonResponse::HTTP_BAD_REQUEST
      );
    }

    if($password === $this->app->User->GetUsername()){
      return new JsonResponse(
        ['error'=>'Das Passwort darf nicht dem Username entsprechen!'],
        JsonResponse::HTTP_BAD_REQUEST
      );
    }

    $this->changeUserPassword($password, $passwordunescaped);
    if(!empty($role)) {
      $this->app->DB->Update(
        sprintf(
          "UPDATE `user` SET `role` = '%s' WHERE `id` = %d",
          $role, $this->app->User->GetID()
        )
      );
    }

    return new JsonResponse(['success'=>true]);
  }

  /**
   * @param string $password
   * @param string $passwordunescaped
   *
   * @return string|null
   */
  protected function changeUserPassword($password, $passwordunescaped)
  {
    if(!empty($password) && $password !== $this->app->User->GetUsername()){
      $this->app->DB->Select("SELECT u.passwordhash FROM `user` AS u WHERE u.id = '" . $this->app->User->GetID() . "' LIMIT 1");
      if(!$this->app->DB->error()){
        $options = array(
          'cost' => 12,
        );
        $passwordhash = @password_hash($passwordunescaped, PASSWORD_BCRYPT, $options);
        if(!empty($passwordhash)){
          $this->app->DB->Update(
            "UPDATE `user` SET 
            `passwordhash` = '" . $this->app->DB->real_escape_string($passwordhash) . "',
            `password` = '', 
            `passwordmd5` = '', 
            `passwordsha512` = '', 
            `salt` = '' 
            WHERE `id` = '" . $this->app->User->GetID() . "' LIMIT 1"
          );

          return '<div class="warning">{|Passwort wurde erfolgreich ge&auml;ndert!|}</div>';
        }
      }else{
        $salt = $this->app->DB->Select("SELECT u.salt FROM `user` AS u WHERE u.id = '" . $this->app->User->GetID() . "' LIMIT 1");
        if(!$this->app->DB->error()){
          if(empty($salt)){
            $salt = hash('sha512', microtime(true));
          }
          $passwordsha512 = hash('sha512', $password . $salt);
          if(!empty($salt) && !empty($passwordsha512)){
            $this->app->DB->Update(
              "UPDATE `user` SET 
               `password` = '', 
               `passwordmd5` = '', 
               `salt` = '{$salt}', 
               `passwordsha512` = '{$passwordsha512}' 
               WHERE `id` = '" . $this->app->User->GetID() . "' LIMIT 1"
            );
          }else{
            $this->app->DB->Update(
              "UPDATE `user` SET 
               `password` = '', 
               `passwordmd5` = MD5('{$password}'), 
               `salt` = '{$salt}', 
               `passwordsha512` = '{$passwordsha512}' 
               WHERE `id` = '" . $this->app->User->GetID() . "' LIMIT 1"
            );
          }
        }else{
          $this->app->DB->Update(
            "UPDATE `user` SET 
             `password` = '',
             `passwordmd5` = MD5('{$password}') 
             WHERE `id`='" . $this->app->User->GetID() . "' LIMIT 1"
          );
        }
      }
    }

    return null;
  }

  /**
   * Passwort-Änderung
   *
   * @return void
   */
  protected function HandlePasswordChange()
  {
    $password = $this->app->Secure->GetPOST('password');
    $repassword = $this->app->Secure->GetPOST('passwordre');
    $passwordunescaped = $this->app->Secure->GetPOST('password', '', '', 'noescape');

    if(!empty($password) && $password !== $repassword){
      $this->app->Tpl->Set('MESSAGE', '<div class="error">{|Passworteingabe falsch! Bitte zwei mal das gleiche Passwort eingeben!|}</div>');
      return;
    }

    if($password === $this->app->User->GetUsername()){
      $this->app->Tpl->Set('MESSAGE', '<div class="error">{|Das Passwort darf nicht dem Username entsprechen!|}</div>');
      return;
    }

    $message = $this->changeUserPassword($password, $passwordunescaped);
    if($message !== null) {
      $this->app->Tpl->Set('MESSAGE', $message);
    }
  }

  /**
   * Profileinstellungen speichern
   *
   * @return void
   */
  protected function HandleProfileSettingsSave()
  {
    $submit_startseite = $this->app->Secure->GetPOST('submit_startseite');
    $startseite = $this->app->Secure->GetPOST('startseite');
    $chat_popup = (int)$this->app->Secure->GetPOST('chat_popup');
    $callcenter_notification = (int)$this->app->Secure->GetPOST('callcenter_notification');
    $defaultcolor = $this->app->Secure->GetPOST('defaultcolor');
    if($defaultcolor === 'transparent') $defaultcolor = '';
    $sprachebevorzugen = $this->app->Secure->GetPOST('sprachebevorzugen');

    // umzug in tabelle user
    if($this->app->User->GetParameter('welcome_defaultcolor_fuer_kalender') != ''){
      $defaultcolor = $this->app->Secure->GetPOST('defaultcolor');
      $this->app->DB->Update("UPDATE `user` SET `defaultcolor` = '{$defaultcolor}' WHERE `id` = '" . $this->app->User->GetID() . "' LIMIT 1");
      $this->app->User->SetParameter('welcome_defaultcolor_fuer_kalender', '');
    }

    if($sprachebevorzugen != ''){
      $sprachebevorzugen = $this->app->Secure->GetPOST('sprachebevorzugen');
      $this->app->DB->Update("UPDATE `user` SET `sprachebevorzugen` = '$sprachebevorzugen' WHERE `id` = '" . $this->app->User->GetID() . "' LIMIT 1");
    }

    if($submit_startseite != ''){
      $this->app->DB->Update(
        "UPDATE `user` SET 
        `startseite` = '{$startseite}', 
        `chat_popup` = '{$chat_popup}', 
        `callcenter_notification` = '{$callcenter_notification}', 
        `defaultcolor` = '{$defaultcolor}' 
        WHERE `id` = '" . $this->app->User->GetID() . "' LIMIT 1"
      );
    }
  }

  /**
   * Vorhandenes Profilbild löschen
   *
   * @return void
   */
  protected function HandleProfilePictureDeletion()
  {
    if(!empty($this->app->Secure->GetPOST('delete_datei'))){
      $adresse = $this->app->User->GetAdresse();
      $dateien = $this->app->DB->SelectArr(
        "SELECT d.id 
         FROM datei AS d 
         INNER JOIN datei_stichwoerter AS ds ON d.id = ds.datei
         WHERE d.geloescht = 0 AND ds.objekt LIKE 'Adressen' AND ds.parameter = '" . $adresse . "' AND ds.subjekt LIKE 'Profilbild' 
         ORDER BY d.id DESC"
      );
      if(!empty($dateien)){
        foreach ($dateien as $datei) {
          $this->app->erp->DeleteDatei($datei['id']);
        }
      }
    }
  }

  /**
   * Neues Profilbild hochladen
   *
   * @return void
   */
  protected function HandleProfilePictureUpload()
  {
    if(!empty($this->app->Secure->GetPOST('submit_datei'))){

      $fileName = $this->app->DB->real_escape_string($_FILES['upload']['name']);
      $fileTmp = $this->app->DB->real_escape_string($_FILES['upload']['tmp_name']);

      if(empty($fileTmp)){
        $this->app->Tpl->Set('UPLOADERROR', "<div class=\"error\">Keine Datei ausgew&auml;hlt!</div>");
        $this->app->erp->EnableTab("tabs-3");
      }else{
        $addressId = $this->app->User->GetAdresse();
        $fileid = $this->app->erp->CreateDatei($fileName, 'Profilbild', '', '', $fileTmp, $this->app->User->GetName());
        $this->app->erp->AddDateiStichwort($fileid, 'Profilbild', 'Adressen', $addressId);
      }
    }
  }

  /**
   * API-Account für mobile Apps de-/aktivieren
   *
   * @return void
   */
  protected function HandleMobileAppsAccount()
  {
    $hasPermission = (bool)$this->app->erp->RechteVorhanden('welcome','mobileapps');
    if (!$hasPermission) {
      $this->app->Tpl->Set('MESSAGE', '<div class="error">Sie haben nicht die erforderlichen Rechte für einen API-Account!</div>');
      return;
    }

    // API-Account anlegen und aktivieren
    if(!empty($this->app->Secure->GetPOST('mobile_app_api_create'))){
      $title = $this->app->User->GetName() . ' / Mobile-Dashboard';
      $username = $this->app->User->GetUsername() . '_dashboard';
      $password = md5(uniqid('', true));

      $sql = sprintf(
        "INSERT INTO `api_account` 
          (`id`, `bezeichnung`, `initkey`, `importwarteschlange_name`, `event_url`, `remotedomain`, `aktiv`, 
          `importwarteschlange`, `cleanutf8`, `uebertragung_account`, `permissions`)
              VALUES (NULL, '%s', '%s', '', '', '%s', 1, 0, 1, 0 , '%s')",
        $this->app->DB->real_escape_string($title),
        $this->app->DB->real_escape_string($password),
        $this->app->DB->real_escape_string($username),
        $this->app->DB->real_escape_string(json_encode(['mobile_app_communication']))
      );
      $this->app->DB->Insert($sql);

      $apiAccountId = (int)$this->app->DB->Select(
        sprintf(
          "SELECT a.id FROM `api_account` AS `a` WHERE a.remotedomain = '%s' LIMIT 1",
          $this->app->DB->real_escape_string($username)
        )
      );
      $this->app->User->SetParameter('mobile_apps_api_account_id', $apiAccountId);

      if ($apiAccountId === 0) {
        $this->app->Tpl->Set('MESSAGE', '<div class="error">API-Account konnte nicht angelegt werden!</div>');
      }
    }

    // API-Account aktivieren
    if(!empty($this->app->Secure->GetPOST('mobile_app_api_activate'))){
      $apiAccountId = (int)$this->app->User->GetParameter('mobile_apps_api_account_id');
      $this->app->DB->Update("UPDATE api_account SET aktiv = 1 WHERE id = '{$apiAccountId}' LIMIT 1");
    }

    // API-Account deaktivieren
    if(!empty($this->app->Secure->GetPOST('mobile_app_api_deactivate'))){
      $apiAccountId = (int)$this->app->User->GetParameter('mobile_apps_api_account_id');
      $this->app->DB->Update("UPDATE api_account SET aktiv = 0 WHERE id = '{$apiAccountId}' LIMIT 1");
    }
  }

  private function handleTOTPRegenerate(){
    /** @var TOTPLoginService $totpLoginManager */
    $totpLoginManager = $this->app->Container->get('TOTPLoginService');

    $userID = $this->app->User->GetID();

    $totpLoginManager->regenerateUserSecret($userID);

    $redirect = RedirectResponse::createFromUrl('index.php?module=welcome&action=settings');
    $redirect->send();

    $this->app->ExitXentral();
  }

  private function HandleTOTPToggle(){
    /** @var TOTPLoginService $totpLoginManager */
    $totpLoginManager = $this->app->Container->get('TOTPLoginService');

    $userId = $this->app->User->GetID();

    if($totpLoginManager->isTOTPEnabled($userId)){
      $totpLoginManager->disableTotp($userId);
    }else{
      $totpLoginManager->enableTotp($userId);
    }

    $redirect = RedirectResponse::createFromUrl('index.php?module=welcome&action=settings');
    $redirect->send();

    $this->app->ExitXentral();
  }

  protected function HandleGoogleMailAuth()
  {
      /** @var Request $request */
      $request = $this->app->Container->get('Request');
      $email = $request->post->get('gmail_address');
      $doAuthorize = $request->post->has('submit_authorize_gmail');
      $doTest = $request->post->has('submit_testmail_gmail');

      if (empty($email)) {
          $this->app->Tpl->Add(
              'MSG_NO_GMAILAPI',
              '<div class="error">Fehler: Google E-Mail ist ein Pflichtfeld.</div>'
          );

          return;
      }
      /** @var GoogleAccountGateway $gateway */
      $gateway = $this->app->Container->get('GoogleAccountGateway');
      /** @var GoogleAccountService $service */
      $service = $this->app->Container->get('GoogleAccountService');
      try {
          $account = $gateway->getAccountByUser((int)$this->app->User->GetID());
      } catch (Exception $e) {
          $account = $service->createAccount((int)$this->app->User->GetID(), null, null);
      }
      $props = $gateway->getAccountProperties($account->getId());
      $props = $props->set('gmail_address', $email);
      $service->saveAccountProperties($account->getId(), $props);
      if ($doAuthorize && !$gateway->hasAccountScope($account->getId(), GoogleScope::MAIL)) {
          /** @var Session $session */
          $session = $this->app->Container->get('Session');
          /** @var GoogleAuthorizationService $authorizer */
          $authorizer = $this->app->Container->get('GoogleAuthorizationService');
          $redirect =  $authorizer->requestScopeAuthorization(
              $session,
              [GoogleScope::MAIL],
              'index.php?module=welcome&action=settings'
          );
          SessionHandler::commitSession($session);
          $redirect->send();
          $this->app->ExitXentral();
      }
      if (!$doAuthorize && $doTest) {
          $this->HandleGoogleMailTest();
      }

      RedirectResponse::createFromUrl('?module=welcome&action=settings')
          ->send();
      $this->app->ExitXentral();
  }

  protected function HandleGoogleMailTest()
  {
      /** @var Request $request */
      $request = $this->app->Container->get('Request');
      if (!$request->post->has('submit_testmail_gmail')) {
          $redirect = RedirectResponse::createFromUrl('index.php?module=welcome&action=settings');
          $redirect->send();
          $this->app->ExitXentral();
      }

      /** @var GoogleAccountGateway $gateway */
      $gateway = $this->app->Container->get('GoogleAccountGateway');
      $success = false;
      $error = false;
      $userId = (int)$this->app->User->GetID();
      $msg = '';
      try {
          $account = $gateway->getAccountByUser($userId);
          $email = $gateway->getAccountProperties($account->getId())->get('gmail_address');
          if (empty($email)) {
              throw new RuntimeException('Google Account has no email address');
          }
      } catch (Exception $e) {
          $error = true;
          $msg = '<div class="error">Google Account nicht gefunden.</div>';
      }

      if (!$error) {
          try {
              /** @var SystemMailer $mailer */
              $mailer = $this->app->Container->get('SystemMailer');
              $success = $mailer->composeAndSendEmail(
                  $email,
                  $this->app->User->GetName(),
                  [new EmailRecipient($email, $this->app->User->GetName())],
                  'Test Email Von Xentral',
                  '<p>Wenn Sie Diese Test-Email erhalten haben, hat die Verwendung von Google Mail funktioniert.</p>'
              );
          } catch (Exception $e) {
              $success = false;
          }
          if (!$success) {
              $msg = '<div class="error">Das Versenden der Test-Email ist fehlgeschlagen.
                Bitte wiederholen Sie den Authorisierungsvorgang.</div>';
          }
      }
      if (!$error && $success === true) {
          $msg = sprintf(
              '<div class="error2">Eine Test-Email wurde an "%s" verschickt. Bitte überprüfen Sie den Posteingang.</div>',
              $email
          );
      }

      $url = 'index.php?module=welcome&action=settings';
      if (!empty($msg))  {
          $url .= sprintf('%s&msg=%s', $url, base64_encode($msg));
      }
      $redirect = RedirectResponse::createFromUrl($url);
      $redirect->send();
      $this->app->ExitXentral();
  }

  /**
   * @return void
   */
  protected function renderGoogleMailSettings()
  {
      if (!$this->app->Container->has('GoogleCredentialsService')) {
          return;
      }
      /** @var GoogleCredentialsService $credService */
      $credService = $this->app->Container->get('GoogleCredentialsService');
      if (!$credService->existCredentials()) {
          $this->app->Tpl->Add(
              'MSG_NO_GMAILAPI',
              '<div class="info">Die Goolge Schnittstelle ist im System nicht aktiv.'
          );
          $this->app->Tpl->Set('GMAIL_AUTH_DISABLE', 'disabled');
          $this->app->Tpl->Set('GMAIL_ADDRESS_DISABLE', 'disabled');
          $this->app->Tpl->Set('GMAIL_TESTMAIL_DISABLE', 'disabled');
          return;
      }
      /** @var GoogleAccountGateway $gateway */
      $gateway = $this->app->Container->get('GoogleAccountGateway');
      try {
          $account = $gateway->getAccountByUser((int)$this->app->User->GetID());
      } catch (GoogleAccountNotFoundException $e) {
          $this->app->Tpl->Set('GMAIL_AUTH_DISABLE', '');
          $this->app->Tpl->Set('GMAIL_TESTMAIL_DISABLE', 'disabled');
          return;
      }
      if (!$gateway->hasAccountScope($account->getId(), GoogleScope::MAIL)) {
          $this->app->Tpl->Set('GMAIL_TESTMAIL_DISABLE', 'disabled');
      }
      $props = $gateway->getAccountProperties($account->getId());
      if ($props->has('gmail_address')) {
          $this->app->Tpl->Set('GMAIL_ADDRESS', $props->get('gmail_address'));
      }
      //try {
      //    $gmailUser = $googleApiGate->getGmailApiUserByUser($this->app->User->GetID());
      //    if ($gmailUser === null) {
      //        /** @var EmailAccountGateway $emailAccountGate */
      //        $emailAccountGate = $this->app->Container->get('EmailAccountGateway');
      //        /** @var EmailBackupAccount $emailAccount */
      //        $emailAccount = $emailAccountGate->findGmailAccountByUser($this->app->User->GetID());
      //        if ($emailAccount !== null) {
      //            /** @var GoogleAccountService $googleApiService */
      //            $googleApiService = $this->app->Container->get('GoogleApiService');
      //            try {
      //                $gmailUser = $googleApiService->createAccount(
      //                    $this->app->User->GetID(),
      //                    GoogleApiAccount::TYPE_MAIL,
      //                    $emailAccount->smtpSenderEmail
      //                );
      //            }catch (Exception $e) {}
      //        }
      //    }
      //} catch (Exception $e) {
      //    $gmailUser = null;
      //}
  }

  protected function HandleGoogleCalendarSave()
  {
      /** @var Request $request */
      $request = $this->app->Container->get('Request');
      $doAuthorize = $request->post->has('authorize_google_calendar');
      $doImport = $request->post->has('import_google_calendar');
      /** @var GoogleAccountGateway $gateway */
      $gateway = $this->app->Container->get('GoogleAccountGateway');
      /** @var GoogleAccountService $service */
      $service = $this->app->Container->get('GoogleAccountService');
      try {
          $account = $gateway->getAccountByUser((int)$this->app->User->GetID());
      } catch (Exception $e) {
          $account = $service->createAccount((int)$this->app->User->GetID(), null, null);
      }
      if ($doAuthorize && !$gateway->hasAccountScope($account->getId(), GoogleScope::CALENDAR)) {
          /** @var Session $session */
          $session = $this->app->Container->get('Session');
          /** @var GoogleAuthorizationService $authorizer */
          $authorizer = $this->app->Container->get('GoogleAuthorizationService');
          $redirect = $authorizer->requestScopeAuthorization(
              $session,
              [GoogleScope::CALENDAR],
              'index.php?module=welcome&action=settings&selectcalendar=1'
          );
          SessionHandler::commitSession($session);
          $redirect->send();
          $this->app->ExitXentral();
      }
      if (!$doAuthorize && $doImport) {
          $this->HandleGoogleCalendarImport();
      }

      RedirectResponse::createFromUrl('?module=welcome&action=settings')
          ->send();
      $this->app->ExitXentral();
  }

  protected function HandleGoogleCalendarImport()
  {
      /** @var Request $request */
      $request = $this->app->Container->get('Request');
      if (!$request->post->has('import_google_calendar')) {
          $redirect = RedirectResponse::createFromUrl('index.php?module=welcome&action=settings');
          $redirect->send();
          $this->app->ExitXentral();
      }

      $msg = '';
      $userId = (int)$this->app->User->GetID();
      try{
          /** @var GoogleCalendarClientFactory $factory */
          $factory = $this->app->Container->get('GoogleCalendarClientFactory');
          /** @var GoogleCalendarSynchronizer $synchronizer */
          $synchronizer = $this->app->Container->get('GoogleCalendarSynchronizer');
          $client = $factory->createClient($userId);
          $synchronizer->importAbsoluteEvents($client);
          $msg = '<div class="error2">Termine erfolgreich Importiert.</div>';
      }
      catch (GoogleAccountNotFoundException $e) {
          $msg = '<div class="error">Fehler: Keine Verbindung zur Google-API</div>';
      }
      catch (GoogleCalendarSyncException $e) {
          $msg = '<div class="error">Fehler beim Terminimport</div>';
      }
      catch (Exception $e) {
          $msg = sprintf('<div class="error">Fehler: %s</div>', $e->getMessage());
      }

      $url = 'index.php?module=welcome&action=settings';
      if (!empty($msg)) {
          $url .= sprintf('&msg=%s', base64_encode($msg));
      }
      $redirect = RedirectResponse::createFromUrl($url);
      $redirect->send();
      $this->app->ExitXentral();
  }

  protected function renderGoogleCalendarSettings()
  {
      if (!$this->app->Container->has('GoogleCredentialsService')) {
          return;
      }
      /** @var GoogleCredentialsService $credService */
      $credService = $this->app->Container->get('GoogleCredentialsService');
      if (!$credService->existCredentials()) {
          $this->app->Tpl->Add(
              'MSG_GOOGLE_CALENDAR',
              '<div class="info">Die Goolge Schnittstelle ist im System nicht aktiv.'
          );
          $this->app->Tpl->Set('GOOGLE_AUTH_DISABLE', 'disabled');
          $this->app->Tpl->Set('GOOGLE_SYNC_DISABLE', 'disabled');
          return;
      }
      /** @var GoogleAccountGateway $gateway */
      $gateway = $this->app->Container->get('GoogleAccountGateway');
      try {
          $userId = (int)$this->app->User->GetID();
          $account = $gateway->getAccountByUser($userId);
      } catch (GoogleAccountNotFoundException $e) {
          $this->app->Tpl->Set('GOOGLE_AUTH_DISABLE', 'disabled');
          $this->app->Tpl->Set('GOOGLE_SYNC_DISABLE', 'disabled');

          return;
      }
      $this->app->Tpl->Set('GOOGLE_AUTH_DISABLE', '');
      $this->app->Tpl->Set('GOOGLE_SYNC_DISABLE', '');
      if (!$gateway->hasAccountScope($account->getId(), GoogleScope::CALENDAR)) {
          $this->app->Tpl->Set('GOOGLE_SYNC_DISABLE', 'disabled');
          return;
      }
      $props = $gateway->getAccountProperties($account->getId());
      $this->app->Tpl->Set('GOOGLE_CALENDAR', $props->get('selected_calendar'));

      /** @var Request $request */
      $request = $this->app->Container->get('Request');
      if ($request->get->getInt('selectcalendar') !== 1) {
          return;
      }
      /** @var GoogleCalendarClientFactory $factory */
      $factory = $this->app->Container->get('GoogleCalendarClientFactory');
      $client = $factory->createClient($userId);
      $calendar = $client->getPrimaryCalendar();
      $this->app->Tpl->Set('GOOGLE_CALENDAR', $calendar->getId());
      /** @var GoogleAccountService $service */
      $service = $this->app->Container->get('GoogleAccountService');
      $props = $props->set('selected_calendar', $calendar->getId());
      $service->saveAccountProperties($account->getId(), $props);
      $redirect = RedirectResponse::createFromUrl('index.php?module=welcome&action=settings');
      $redirect->send();
      $this->app->ExitXentral();
  }

  /**
   * @return NotificationService
   */
  protected function GetNotificationService()
  {
    return $this->app->Container->get('NotificationService');
  }

  /**
   * @return DownloadSpoolerService
   */
  protected function GetDownloadSpoolerService()
  {
    return $this->app->Container->get('DownloadSpoolerService');
  }

  /**
   * @return DownloadSpoolerGateway
   */
  protected function GetDownloadSpoolerGateway()
  {
    return $this->app->Container->get('DownloadSpoolerGateway');
  }
}
