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

class Session
{

  // set check to true when user have permissions
  private $check = false;

  public $module;
  public $action;
  /** @var Application $app */
  public $app;
  public $reason;

  public function __construct()
  {

  }

  /**
   * @param Application $appObj
   */
  public function Check($appObj)
  {
    $this->app = $appObj;
    $this->check = true;

    $db = $this->app->Secure->GetPOST('db','nothtml','',true);
    $dbselect = $this->app->Secure->GetPOST('dbselect','nothtml','',true);
    if(!empty($db) && $dbselect === 'true')
    {
      $_COOKIE['DBSELECTED'] = $db;
      setcookie('DBSELECTED', $db);
    }
    $hasModuleAction = !empty($this->app->Secure->GET['module']) && !empty($this->app->Secure->GET['action']);
    if ($hasModuleAction &&
      (
      ($this->app->Secure->GET['module'] === 'artikel' && $this->app->Secure->GET['action'] === 'thumbnail')
        || ($this->app->Secure->GET['module'] === 'wiki' && $this->app->Secure->GET['action'] === 'getfile')
      )
    ) {
      if (!$this->app->User->getUserByCache()) {
        $this->check = false;
        $this->reason = 'PLEASE_LOGIN';
      }
      else {
        $this->check = true;
      }
      return;
    }

    if (!empty($this->app->Secure->GET['module']) && !empty($this->app->Secure->GET['action']) &&
      in_array($this->app->Secure->GET['module'],['backup', 'systemtemplates']) && $this->app->Secure->GET['action'] === 'readstatus'){
      $sessionFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . 'session.txt';
      if(file_exists($sessionFile) && ((string) $bckSession = file_get_contents($sessionFile)) && trim($bckSession) === session_id()){
        $this->check = true;
        return;
      }
    }

    $this->module = $this->app->Secure->GetGET('module');
    $this->action = $this->app->Secure->GetGET('action');

    /*
     * Skip the permissions check on all listed actions.
     * The module name is required as key, the action(s)
     * can be either a single string or a list of strings.
     *
     * e.G.:
     * valid whitelistings are:
     *
     * 'awesomemodule' => 'worldchangingaction'
     * or:
     * 'awesomemodule' => ['worldchangingaction']
     * or:
     * 'awesomemodule' => ['worldchangingaction', 'anotherbeautifulaction']
     */
    $whitelist = [
      'snapaddy' => 'single',
    ];

    if (array_key_exists($this->module, $whitelist)) {
      $actions = $whitelist[$this->module];
      // convert to array if not, so multiple actions
      $actions = (array)$actions;

      if (in_array($this->action, $actions)) {
        /*
         * The module and it's action are listed in
         * $whitelist, so make sure, the check flag
         * is set to true and return.
         */
        $this->check = true;
        return;
      }
    }

    if (!$this->app->acl->CheckTimeOut()
      && $this->module !== 'api'
      && !($this->module === 'kalender' && $this->action === 'ics')
      && !($this->module === 'welcome' && $this->action === 'cronjob')
      && !($this->module === 'welcome' && $this->action === 'cronjob2')
      && !($this->module === 'welcome' && $this->action === 'adapterbox')
      && !($this->module === 'onlineshops' && $this->action === 'getapi')
      && !($this->module === 'callcenter' && $this->action === 'call')
      && !($this->module === 'welcome' && $this->action === 'poll')
      && !($this->module === 'welcome' && $this->action === 'passwortvergessen')
      && !($this->module === 'report' && $this->action === 'export')
      && !($this->module === 'googleapi' && $this->action === 'redirect')
    ) {
      $this->check = false;
      $this->reason = 'PLEASE_LOGIN';
      return;
    }

    if ($this->module === 'api') {
      $this->check = true;
      header('Access-Control-Allow-Origin: *'); // Externe Domains duerfen auf API zugreifen
      return;
    }
    if ($this->module === 'kalender' && $this->action === 'ics'){
      $this->check = true;
      return;
    }
    if ($this->module === 'welcome'
      && in_array($this->action,
        [
          'cronjob',
          'cronjob2',
          'passwortvergessen',
          'adapterbox',
          'poll',
        ])
    ){
      $this->check = true;
      return;
    }

    if ($this->module === 'appstore' && $this->action === 'list') {
      $this->check = true;
      return;
    }
    if ($this->module === 'onlineshops' && $this->action === 'getapi') {
      $this->check = true;
      return;
    }
    if ($this->module === 'report' && $this->action === 'export') {
      $this->check = true;
      return;
    }
    if ($this->module === 'googleapi' && $this->action === 'redirect') {
      $this->check = true;
      return;
    }
    if ($this->module === 'callcenter' && $this->action === 'call'){
      $this->check = true;
      return;
    }
    if (
      $this->app->acl->Check(//benutzer ist schon mal erfolgreich angemeldet
        $this->app->User->GetType(),
        $this->module, $this->action,
        $this->app->User->GetID()
      )
    ) {
        $this->check = true;
      return;
    }

    $this->reason = 'NO_PERMISSIONS';
    $this->check = false;
  }

  public function GetCheck()
  {
    return $this->check;
  }

  public function UserSessionCheck()
  {
    $this->check = false;
    $this->reason = 'PLEASE_LOGIN';
    return true;
  }

}

