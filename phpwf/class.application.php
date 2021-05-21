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
use Xentral\Components\Http\RedirectResponse;

function WithGUI($first = false)
{
  if (defined('API_REQUEST') && (bool)API_REQUEST === true) {
    return false;
  }
  if(isset($_GET['withgui']) && $_GET['withgui']){
    return true;
  }
	$module = isset($_GET['module'])?$_GET['module']:'';
	$action = isset($_GET['action'])?$_GET['action']:'';
	if($action === 'editable') {
	  return false;
  }
  if(!$first && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'))
  {
    if($action === 'positionen' || strpos($action,'minidetail') === 0){
      return true;
    }
    if($module === 'adresse'){
      return true;
    }
    if($action === 'verkauf'){
      return true;
    }
    return false;
  }
	return !($module==='ajax' || $module==='api'
    || ($module==='welcome' && $action==='css')
    || ($module==='report' && $action==='export')
    || ($module==='welcome' && $action==='cronjob')
    || ($module==='welcome' && $action==='adapterbox')
    || ($module==='welcome' && $action==='logo')
    || ($module==='artikel' && $action==='ajaxwerte')
    || ($module==='artikel' && $action==='thumbnail')
    || ($module==='wiki' && $action==='getfile')
    || ($module==='benutzer' && $action==='chrights')
    || ($module==='callcenter' && $action==='call')
    || ($module==='waage' && $action==='gewicht')
    || ($module==='welcome' && $action==='poll')
    || ($module==='artikel' && $action==='thumbnail')
  );
}

//include ('phpwf/engine/class.engine.php';
if(WithGUI())
{
	include dirname(__DIR__).'/phpwf/plugins/class.formhandler.php';
	include dirname(__DIR__).'/phpwf/plugins/class.pagebuilder.php';
	include dirname(__DIR__).'/phpwf/plugins/class.widgetapi.php';
	include dirname(__DIR__).'/phpwf/widgets/easytable.php';
	include dirname(__DIR__).'/phpwf/widgets/grouptable.php';
	include dirname(__DIR__).'/phpwf/widgets/childtable.php';
	include dirname(__DIR__).'/phpwf/widgets/table.php';
	include dirname(__DIR__).'/phpwf/plugins/class.picosafelogin.php';
	include dirname(__DIR__).'/phpwf/plugins/class.wawision_otp.php';
	include dirname(__DIR__).'/phpwf/htmltags/all.php';
	include dirname(__DIR__).'/phpwf/types/class.simplelist.php';
  include dirname(__DIR__).'/phpwf/plugins/class.modulescriptcache.php';
}

include dirname(__DIR__).'/phpwf/plugins/class.templateparser.php';
//include dirname(__DIR__).'/phpwf/plugins/class.yui.php';

include dirname(__DIR__).'/phpwf/plugins/class.acl.php';
include dirname(__DIR__).'/phpwf/plugins/class.user.php';
include dirname(__DIR__).'/phpwf/plugins/class.page.php';
include dirname(__DIR__).'/phpwf/plugins/class.phpwfapi.php';
include dirname(__DIR__).'/phpwf/plugins/class.secure.php';
//if(is_file(__DIR__.'/www/lib/class.location.php'))@include (dirname(__DIR__).'/www/lib/class.location.php';
include dirname(__DIR__).'/phpwf/plugins/class.wfmonitor.php';
include dirname(__DIR__).'/phpwf/plugins/class.string.php';
include dirname(__DIR__).'/phpwf/plugins/class.objectapi.php';

/**
 * @property Config $Conf
 * @property Secure $Secure
 * @property TemplateParser $Tpl
 * @property FormHandler $FormHandler
 * @property Table $Table
 * @property WidgetAPI $Widget
 * @property PageBuilder $PageBuilder
 * @property Page $Page
 * @property ObjectAPI $ObjAPI
 * @property WFMonitor $WFM
 * @property ModuleScriptCache $ModuleScriptCache
 * @property YUI $YUI
 * @property User $User
 * @property Acl $acl
 * @property phpWFAPI $WF
 * @property WawiString $String
 * @property DB $DB
 */
class Application extends ApplicationCore
{

    public $ActionHandlerList;
    public $ActionHandlerDefault;
    public $http;
    public $caller;
    public $BuildNavigation;
    public $PopupJS;
    public $NoHooks;

    public function __construct($config,$group='')
    {
      parent::__construct($config,$group);

      /*if(!isset($_GET['module']) || $_GET['module'] != 'api') {
        if(!(isset($_GET['module']) && isset($_GET['action']) && isset($_GET['cmd']) && $_GET['module'] == 'welcome' && (($_GET['action'] == 'login' && $_GET['cmd'] == 'checkrfid') || $_GET['action'] == 'cronjob' || $_GET['action'] == 'adapterbox'))) {
         // @session_cache_limiter('private');
          //@session_start();
        }
      }*/

      $this->Conf= $config;

      //include dirname(__DIR__).'/phpwf/plugins/class.mysql.php';

      if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on'){
        $this->http = 'https';
      }
      else{
        $this->http = 'http';
      }

      //$this->Secure         = new Secure($this);   // empty $_GET, and $_POST so you
                                                // have to need the secure layer always


			if(WithGUI()){
        $this->Tpl               = new TemplateParser($this);
      	$this->FormHandler       = new FormHandler($this);
      	$this->Table	           = new Table($this);
      	$this->Widget	           = new WidgetAPI($this);
      	$this->PageBuilder       = new PageBuilder($this);
      	$this->Page              = new Page($this);
      	$this->ObjAPI	           = new ObjectAPI($this);
      	$this->WFM               = new WFMonitor($this);
        $this->ModuleScriptCache = new ModuleScriptCache();
			}

      //$this->YUI            = new YUI($this);
      //$this->User           = new User($this);
      //$this->acl            = new Acl($this);
      //$this->WF             = new phpWFAPI($this);
      //$this->String         = new WawiString();

      $this->BuildNavigation = true;
      $this->PopupJS = false;
          
      //$this->DB             = new DB($this->Conf->WFdbhost,$this->Conf->WFdbname,$this->Conf->WFdbuser,$this->Conf->WFdbpass,$this);

			if(WithGUI()){
        $this->Tpl->ReadTemplatesFromPath(__DIR__ . '/widgets/templates/');
        $this->Tpl->Set('LAYOUTFIXMARKERCLASS', 'layoutfix');
      }
    }

    public function __destruct()
    {
      //$this->DB->Close();
    }

    public function ActionHandlerInit($caller)
    {
      $this->caller = $caller;
    }

    public function ActionHandler($command,$function)
    {
      $this->ActionHandlerList[$command]=$function; 
    }
    
    public function DefaultActionHandler($command)
    {
      $this->ActionHandlerDefault=$command;
    }

    public function ActionHandlerListen($app)
    {
      $fkt = '';
      $action = $app->Secure->GetGET('action');
      $module = $app->Secure->GetGET('module');
      if(!empty($this->ActionHandlerList)) {
        $app->erp->addActionHandler($module, $this->ActionHandlerList);
      }
      if(!empty($action))
      {
        if(!empty($this->ActionHandlerList[$action]))
        {
          $fkt = $this->ActionHandlerList[$action];
        }
      }
      else
      {
        if(empty($this->ActionHandlerDefault) && isset($this->ActionHandlerList['list']))
        {
          if(empty($action))
          {
            $app->Secure->GET['action'] = 'list';
          }
          $this->ActionHandlerDefault = 'list';
        }
        if(!empty($this->ActionHandlerDefault))
        {
          $fkt = $this->ActionHandlerList[$this->ActionHandlerDefault];
        }
      }

      if(!empty($fkt)){
        $callhooks = empty($this->NoHooks) || !in_array($action,$this->NoHooks);
        if($callhooks){
          $app->erp->RunHook($module . '_' . $action . '_before');
        }
        $response = @$this->caller->$fkt();
        if($callhooks){
          $app->erp->RunHook($module . '_' . $action . '_after');
        }

        if($response instanceof JsonResponse) {
          $response->send();
          $this->ExitXentral();
        }
        if($response instanceof RedirectResponse) {
          $response->send();
          $this->ExitXentral();
        }
      }
    }

    public function DisableLayoutFix()
    {
      if(WithGUI()){
        $this->Tpl->Set('LAYOUTFIXMARKERCLASS', '');
      }
    }
}
