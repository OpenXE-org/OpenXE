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
/* Author: Benedikt Sauter, sauter@ibat.de, 2007
 * Player for PHP Applications
 */

class Player {

  public $DefautTemplates;
  public $DefautTheme;

  /** @var erpooSystem $app the application object */
  public $app;

  function __construct()
  {
    $this->DefautTemplates="defaulttemplates";
    $this->DefautTheme="default";
  }

  function SetDefaultTemplates($path)
  {
  }

  function SetDefaultTheme($path)
  {

  }

  public function BuildNavigation()
  {
    if(!WithGUI() || !method_exists($this->app->Page,'CreateNavigation') || !method_exists('erpAPI','Navigation')) {
      return;
    }

    $this->app->Page->CreateNavigation($this->app->erp->Navigation());
  }

  public function Run($sessionObj)
  {
    $this->app = $sessionObj->app;
    // play application only when layer 2 said that its ok
    if(!$sessionObj->GetCheck()) {
      if($sessionObj->reason=='PLEASE_LOGIN')
      {
        $module = 'welcome';
        $action = 'login';
        $this->app->Secure->GET['module']='welcome';
        $this->app->Secure->GET['action']='login';
      }
    } else {
      $hasModuleAndAction = !empty($this->app->Secure->GET['module']) && !empty($this->app->Secure->GET['action']);
      if($hasModuleAndAction &&
        $this->app->Secure->GET['module'] === 'artikel' && $this->app->Secure->GET['action'] === 'thumbnail') {
        /** @var Artikel $obj */
        $obj = $this->app->loadModule('artikel');
        if(!empty($obj) && method_exists($obj, 'ArtikelThumbnailCache')) {
          $obj->ArtikelThumbnailCache();
        }
      }
      elseif($hasModuleAndAction &&
        $this->app->Secure->GET['module'] === 'wiki' && $this->app->Secure->GET['action'] === 'getfile') {
        /** @var Wiki $obj */
        $obj = $this->app->loadModule('wiki');
        if(!empty($obj) && method_exists($obj, 'WikiGetFileCache')) {
          $obj->WikiGetFileCache();
        }
      }

      if (!empty($this->app->Secure->GET['module']) && !empty($this->app->Secure->GET['action']) &&
        in_array($this->app->Secure->GET['module'],['backup', 'systemtemplates']) && $this->app->Secure->GET['action'] === 'readstatus'){
        $sessionFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . 'session.txt';
        if(file_exists($sessionFile) && ((string) $bckSession = file_get_contents($sessionFile)) && trim($bckSession) === session_id()){
          $obj = $this->app->loadModule($this->app->Secure->GET['module']);
          $obj->ReadStatus();
        }
      }

      // Get actual commands from URL
      $module = $this->app->Secure->GetGET('module','module');
      $action = $this->app->Secure->GetGET('action','module');
      if($module =='') {
        $module = 'welcome';
        $action = 'main';
      }
      

      if($this->app->erp->isIoncube() && method_exists($this->app->erp, 'IoncubeProperty')
        && WithGUI() && !(($module=='welcome' && $action=='upgrade') || $module=='' || ($module=='welcome' && $action=='start')))
      {
        if(method_exists('erpAPI','Ioncube_getMaxUser'))
        {
          $maxuser = erpAPI::Ioncube_getMaxUser();
        }elseif(method_exists($this->app->erp, 'IoncubegetMaxUser'))
        {
          $maxuser = $this->app->erp->IoncubegetMaxUser();
        }else{
          $maxuser = 0;
        }
        if(method_exists('erpAPI','Ioncube_getMaxLightusers'))
        {
          $maxlightuser = erpAPI::Ioncube_getMaxLightusers();
        }else{
          $maxlightuser = 0;
        }
        if($maxuser)
        {
          $anzuser2 = 0;
          if($maxlightuser > 0) {
            $anzuser2 = (int)$this->app->DB->Select("SELECT count(DISTINCT u.id) FROM `user` u WHERE activ = 1 AND type = 'lightuser' ");
            $anzuser = (int)$this->app->DB->Select("SELECT count(id) FROM `user` WHERE activ = 1 AND not isnull(hwtoken) AND hwtoken <> 4") - $anzuser2;
            $anzuserzeiterfassung = (int)$this->app->DB->Select("SELECT count(*) from user where activ = 1 AND hwtoken = 4 AND type != 'lightuser'");
          }else{
            $anzuser = $this->app->DB->Select("SELECT count(*) from user where activ = 1 AND hwtoken <> 4 ");
            $anzuserzeiterfassung = (int)$this->app->DB->Select("SELECT count(*) from user where activ = 1 AND hwtoken = 4");
          }

          $maxmitarbeiterzeiterfassung = $this->app->erp->ModulVorhanden('mitarbeiterzeiterfassung')?$maxuser:0;
          if($anzuser > $maxuser
            || (
              ($anzuser + $anzuserzeiterfassung + $anzuser2) >
              $maxmitarbeiterzeiterfassung + $maxuser + $maxlightuser
            )
            || (($anzuser + $anzuserzeiterfassung) > $maxmitarbeiterzeiterfassung + $maxuser)
          ) {
            if(!(($module == 'welcome' &&
                ($action=='info' || $action == 'start' || $action == 'logout' || $action == '' || $action == 'main')) ||
              ($module == 'einstellungen' && ($action == 'list' || $action == '')) ||
              $module == 'benutzer'
            ))
            {
              if($this->app->erp->RechteVorhanden('benutzer','list'))
              {
                $module = 'benutzer';
                $action = 'list';

                if($maxlightuser > 0){
                  $error = 'Es existieren mehr aktive Benutzer als Ihre Lizenz erlaubt: Benutzer ' . ($anzuser + $anzuser2) . ($maxlightuser > 0 ? ' (davon ' . $anzuser2 . ' Light-User)' : '') . ' von ' . ($maxuser + $maxlightuser) . ($maxlightuser > 0 ? ' (' . $maxlightuser . ' Light-User)' : '');
                }else{
                  $error = 'Es existieren mehr aktive Benutzer als Ihre Lizenz erlaubt: Benutzer ' . ($anzuser + $anzuser2) . ($maxlightuser > 0 ? ' (davon ' . $anzuser2 . ' Zeiterfassungs-User)' : '') . ' von ' . ($maxuser + $anzuser2) . ($anzuser2 > 0 ? ' (' . $anzuser2 . ' Zeiterfassungs-User)' : '');
                }
                $error = '<div class="error">'.$error.'</div>';
                $this->app->Tpl->Add('MESSAGE', $error);
                $this->app->Secure->GET['msg'] = $this->app->erp->base64_url_encode($error);
              }else{
                $module = 'welcome';
                $action = 'info';
              }
              $this->app->Secure->GET['module'] = $module;
              $this->app->Secure->GET['action'] = $action;
            }
          }
        }
        if(method_exists('erpAPI','Ioncube_Property'))
        {
          $deaktivateonexp = erpAPI::Ioncube_Property('deaktivateonexp');
        }else{
          $deaktivateonexp = $this->app->erp->IoncubeProperty('deaktivateonexp');
        }
        if($deaktivateonexp)
        {
          if(method_exists('erpAPI','Ioncube_HasExpired'))
          {
            $IoncubeHasExpired = erpAPI::Ioncube_HasExpired();
          }elseif(method_exists($this->app->erp, 'IoncubeHasExpired'))
          {
            $IoncubeHasExpired = $this->app->erp->IoncubeHasExpired();
          }else{
            $IoncubeHasExpired = false;
          }
        }else{
          $IoncubeHasExpired = false;
        }
        if($deaktivateonexp && $IoncubeHasExpired
        && !(($module == 'welcome' && $action='logout') || ($module == 'welcome' && $action='start') || ($module == 'welcome' && $action='main'))
        )
        {
          $module = 'welcome';
          $action = 'info';
          $this->app->Secure->GET['module'] = $module;
          $this->app->Secure->GET['action'] = $action;
        }
      }
    } 

    if($action!="list" && $action!="css" && $action!="logo" && $action!="poll" && $module!="ajax" && $module!="protokoll" && $action!="thumbnail"){
      $this->app->erp->Protokoll();
    }
    $id = $this->app->Secure->GetGET('id');
    $lid = $this->app->Secure->GetGET('lid');
    if($module !== 'welcome' && $action !== 'poll' && $module !== 'ajax'){
      if(($module !== 'artikel' || $action !== 'thumbnail')
        && ($module !== 'wiki' || $action !== 'getfile')
        && $action !== 'editable' && $action !== 'positionen'){
        $this->app->Laender($module, $action, $id, $lid);
      }
    }

    // plugin instanzieren
    // start module
    if(file_exists(dirname(__DIR__).'/www/pages/'.$module.'.php')){
      if(file_exists(dirname(__DIR__).'/www/pages/'.$module.'_custom.php')){
        include_once dirname(__DIR__).'/www/pages/'.$module.'.php';
        include_once dirname(__DIR__).'/www/pages/'.$module.'_custom.php';
        //create dynamical an object
        $constr = strtoupper($module[0]) . substr($module, 1) . 'Custom';
        if(class_exists($constr))
        {
          $myApp = new $constr($this->app);
        }else{
          $constr = strtoupper($module[0]) . substr($module, 1);
          if(class_exists($constr))
          {
            $myApp = new $constr($this->app);
          }
          elseif(file_exists(dirname(__DIR__).'/www/pages/'.$module.'.php')){
            include dirname(__DIR__).'/www/pages/'.$module.'.php';
            if(class_exists($constr)){
              $myApp = new $constr($this->app);
            }
          }
        }
        if(method_exists($constr, 'AllowedVersion') && isset($this->app->User) && $this->app->User && method_exists($this->app->User, 'GetType') && $this->app->User->GetType() == 'admin')
        {
          $r2 =  new ReflectionMethod($constr, 'AllowedVersion');
          if($r2->isStatic())
          {
            $allowed = $constr::AllowedVersion();
            include(dirname(__DIR__) . '/version.php');
            if((isset($allowed['max']) && ((float)$allowed['max'] < (float)$version_revision))
               || 
               (isset($allowed['versionen']) && (
                 (is_array($allowed['versionen']) && !in_array($version_revision, $allowed['versionen']))
                || (!is_array($allowed['versionen']) && $allowed['versionen'] != $version_revision)
               ))
              )
            {
              $title = 'Inkompatibilität festgestellt';
              $message = 'Die Datei '.$module."_custom.php".' auf Ihrem System, ist nicht für Ihre Version geeignet';

              /** @var \Xentral\Modules\SystemNotification\Gateway\NotificationGateway $notifyGateway */
              $notifyGateway = $this->app->Container->get('NotificationGateway');
              if (!$notifyGateway->hasDuplicatedMessage($this->app->User->GetID(), $title, $message)) {
                  /** @var \Xentral\Modules\SystemNotification\Service\NotificationServiceInterface $notifyService */
                  $notifyService = $this->app->Container->get('NotificationService');
                  $notifyService->create($this->app->User->GetID(), 'warning', $title, $message);
              }
            }
          }
        }
      } else {
        include_once(dirname(__DIR__)."/www/pages/".$module.".php");
        //create dynamical an object
        $constr = strtoupper($module[0]) . substr($module, 1);
        if(class_exists($constr))$myApp = new $constr($this->app);
      }
    }
    else {
      if(file_exists(dirname(__DIR__)."/www/pages/_gen/".$module.".php")){
        include_once(dirname(__DIR__)."/www/pages/_gen/".$module.".php");
        //create dynamical an object
        $constr = "Gen" . strtoupper($module[0]) . substr($module, 1);
        $myApp = new $constr($this->app);
      }
      else {
        if(file_exists(dirname(__DIR__)."/www/pages/_gen/".$module.".php")){
          include_once(dirname(__DIR__)."/www/pages/_gen/".$module.".php");
          //create dynamical an object
          $constr = "Gen" . strtoupper($module[0]) . substr($module, 1);
          $myApp = new $constr($this->app);
        }
      }
    }
    $this->app->erp->RunHook('player_run_before_include_js_css');

    /** @deprecated-block-start Wird später über den Installer eingebunden */
    if (class_exists('Xentral\Modules\SystemNotification\Bootstrap', true)) {
      $javascript = forward_static_call(['Xentral\\Modules\\SystemNotification\\Bootstrap', 'registerJavascript']);
      foreach ($javascript as $cacheName => $jsFiles) {
        $this->app->ModuleScriptCache->IncludeJavascriptFiles($cacheName, $jsFiles);
      }
      $stylesheets = forward_static_call(['Xentral\\Modules\\SystemNotification\\Bootstrap', 'registerStylesheets']);
      foreach ($stylesheets as $cacheName => $cssFiles) {
        $this->app->ModuleScriptCache->IncludeStylesheetFiles($cacheName, $cssFiles);
      }
    }
    if (class_exists('Xentral\Modules\Wizard\Bootstrap', true)) {
      $javascript = forward_static_call(['Xentral\\Modules\\Wizard\\Bootstrap', 'registerJavascript']);
      foreach ($javascript as $cacheName => $jsFiles) {
        $this->app->ModuleScriptCache->IncludeJavascriptFiles($cacheName, $jsFiles);
      }
      $stylesheets = forward_static_call(['Xentral\\Modules\\Wizard\\Bootstrap', 'registerStylesheets']);
      foreach ($stylesheets as $cacheName => $cssFiles) {
        $this->app->ModuleScriptCache->IncludeStylesheetFiles($cacheName, $cssFiles);
      }
    }
      if (class_exists('Xentral\\Widgets\\ClickByClickAssistant\\Bootstrap', true)) {
        $this->app->ModuleScriptCache->IncludeWidgetNew('ClickByClickAssistant');
      }
    if (class_exists('Xentral\\Widgets\\SuperSearch\\Bootstrap', true)) {
      $this->app->ModuleScriptCache->IncludeWidgetNew('SuperSearch');
    }
    /** @deprecated-block-end */

    $moduleClassName = strtoupper($module[0]) . substr($module, 1);
    $this->app->ModuleScriptCache->IncludeModule($moduleClassName);
    $this->app->Tpl->Add('MODULESTYLESHEET', $this->app->ModuleScriptCache->GetStylesheetHtmlTags());
    $this->app->Tpl->Add('MODULEJAVASCRIPTHEAD', $this->app->ModuleScriptCache->GetJavascriptHtmlTags('head'));
    $this->app->Tpl->Add('MODULEJAVASCRIPTBODY', $this->app->ModuleScriptCache->GetJavascriptHtmlTags('body'));

    $permission = true;
    if(isset($myApp) && method_exists($myApp,'CheckRights'))$permission = $myApp->CheckRights();
    
    if(!$permission)
    {
      if($this->app->User->GetID()<=0)
      {
        $this->app->erp->Systemlog("Keine gueltige Benutzer ID erhalten",1);
        @session_destroy();
        echo str_replace('BACK',"index.php?module=welcome&action=login",$this->app->Tpl->FinalParse("permissiondenied.tpl"));
      }
      else {
        $this->app->erp->Systemlog("Fehlendes Recht",1);
        echo str_replace('BACK',isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'',$this->app->Tpl->FinalParse("permissiondenied.tpl"));
      }
      exit;
    }
    
    $this->app->calledWhenAuth($this->app->User->GetType());
    if($this->app->BuildNavigation==true)
      $this->BuildNavigation();

    $this->app->endtime = microtime(); 

    $right = $this->app->Secure->GetGET("right");
    
    $tmpfirmendatenfkt = 'Firmendaten';
    if(method_exists($this->app->erp,'TplFirmendaten'))$tmpfirmendatenfkt = 'TplFirmendaten';
    

    $firmenfarbehell = $this->app->erp->$tmpfirmendatenfkt("firmenfarbehell");
    if($firmenfarbehell =="")
      $firmenfarbehell = "#3fbac9";
    $this->app->Tpl->Set('COLOR1',$firmenfarbehell);
    
    $this->app->calledBeforeFinish();

    $this->app->Tpl->Set('JQUERYMIGRATESRC', './js/jquery/jquery-migrate-3.2.0.min.js');

    $this->app->Tpl->Set('TESTLIZENZABLAUF', '');
    if($this->app->BuildNavigation==true)
    {
      if($right==1) 
        echo $this->app->Tpl->FinalParse('right.tpl');
      else
      {
        if($module==='welcome' && $action==='login'){
          if(is_file(dirname(__DIR__).'/www/themes/new/templates/loginslider.tpl')) {
            $this->app->Tpl->Set(
              'LOGINSLIDER',
              file_get_contents(dirname(__DIR__).'/www/themes/new/templates/loginslider.tpl')
            );
          }
          else{
            $this->app->Tpl->Set(
              'LOGINSLIDER',
              '<div class="slide" style="background-image: url(\'./themes/new/templates/white-cup-filled-by-coffee.jpg\');">
              </div>'
            );
          }

          $this->app->erp->RunHook('loginpage');
          echo $this->app->Tpl->FinalParse('loginpage.tpl');
        }
        elseif($module==='welcome' && $action==='passwortvergessen'){
          echo $this->app->Tpl->FinalParse('passwortvergessenpage.tpl');
        }
        else {
          $this->app->erp->addFav();

          $this->app->erp->HelpIconAndTooltip();

          if(
            ($module !== 'welcome'
              || ($action !== 'settings' && $action !== 'logout' && $action !== 'login'
                && $action !== 'start'&& $action !== 'startseite' && $action !== 'main'
              )
            )
            && ($isadminadmin = $this->app->acl->IsAdminadmin()))
          {
            header('Location: index.php?module=welcome&action=start');
            exit;
          }

          $this->app->HeaderBoxen();
          if($this->app->erp->UserDevice()==='smartphone'){
            echo $this->app->Tpl->FinalParse('page_smartphone.tpl');
          }
          else{

            $this->app->Tpl->Set('VUEJS', 'vue.min.js');
              $this->app->erp->RunHook('before_final_parse_page');
            echo $this->app->Tpl->FinalParse('page.tpl');
          }
        }
      }
    }
    else {
      if($this->app->PopupJS){
        echo $this->app->Tpl->FinalParse('popup_js.tpl');
      }
      else{
        echo $this->app->Tpl->FinalParse('popup.tpl');
      }
    }
  }
}
