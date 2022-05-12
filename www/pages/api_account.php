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

class Api_account
{
  /** @var Application $app */
  protected $app;


  const MODULE_NAME = 'ApiAccount';

  /** @var string[] $javascript */
  public $javascript = [
    './classes/Modules/ApiAccount/www/js/api_account.js',
  ];

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
      case 'api_account_list':
        $allowed['api_account'] = array('list');
        $heading = array('API Account ID', 'Bezeichnung', 'Aktiv', 'Men&uuml;');
        $width = array('10%', '79%', '10%', '1%');
        $findcols = array('aa.id', 'bezeichnung', "if(aktiv = 1, 'ja','nein')", 'id');
        $searchsql = array('bezeichnung');
        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc = 1;
        $menucol = 3;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a data-id=\"%value%\" class=\"get\" href=\"#\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a></td></tr></table>";

        $sql = "SELECT aa.id, aa.id, aa.bezeichnung, 
                           if(aa.aktiv = 1, 'ja','nein') as aktiv, 
                           aa.id
        FROM `api_account` AS `aa`
        ";
        $fastcount = "SELECT COUNT(`aa`.`id`) FROM `api_account` AS `aa`";
            
      break;

    }
    
    $erg = [];
    foreach($erlaubtevars as $k => $v)  {
      if(isset($$v)) {
        $erg[$v] = $$v;
      }
    }
    return $erg; 
  }

  /**
   * Api_account constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app=$app;
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Api_AccountCreate");
    $this->app->ActionHandler("edit","Api_AccountEdit");
    $this->app->ActionHandler("list","Api_AccountList");
    $this->app->ActionHandler("delete","Api_AccountDelete");

    $this->app->DefaultActionHandler('list');
    $this->app->ActionHandlerListen($app);
  }
  
  function Api_AccountCreate(){

  }

  function Api_AccountEdit(){

  }

  public function Api_AccountDelete(){
    $id = $this->app->Secure->GetGET('id');
    $this->app->DB->Delete(sprintf('DELETE FROM `api_account` WHERE `id` = %d', $id));
    $this->app->Location->execute('index.php?module=api_account&action=list');
  }


  /**
   * @return JsonResponse
   */
  public function HandleGetAjaxAction()
  {
    $id = (int)$this->app->Secure->GetPOST('id');
    if($id === 0) {
      $data = [
        'aktiv' => 0,
        'id' => '',
        'bezeichnung' => '',
        'projekt' => '',
        'remotedomain' => '',
        'initkey' => '',
        'importwarteschlange' => 0,
        'importwarteschlange_name' => '',
        'event_url' => '',
        'cleanutf8' => 0,
        'apitempkey' => '',
        'ishtmltransformation' => 0,
      ];

      return new JsonResponse($data);
    }
    if($id > 0) {
      $data = $this->app->DB->SelectRow(
        sprintf(
          "SELECT a.id, a.bezeichnung, a.aktiv, p.abkuerzung AS `projekt`, a.remotedomain, a.initkey,
            a.importwarteschlange, a.importwarteschlange_name, a.cleanutf8, a.event_url, a.permissions, a.ishtmltransformation
          FROM `api_account` AS `a` 
          LEFT JOIN `projekt` AS `p` ON a.projekt = p.id
          WHERE a.id = %d",
          $id
        )
      );
      /** @var Api $api */
      $api = $this->app->loadModule('api');
      $data['apitempkey'] = $api->generateHashFromDomainAndKey($data['initkey'], $data['remotedomain']);
      if(!empty($data)) {
        return new JsonResponse($data);
      }
    }

    return new JsonResponse(['error'=>'Account nicht gefunden'], JsonResponse::HTTP_BAD_REQUEST);
  }

  /**
   * @return JsonResponse
   */
  public function HandleSaveAjaxAction()
  {
    if(!$this->app->erp->RechteVorhanden('api_account', 'edit')) {
      return new JsonResponse(['error'=>'Fehlende Rechte'], JsonResponse::HTTP_BAD_REQUEST);
    }

    $id = (int)$this->app->Secure->GetPOST('id');
    $bezeichnung = $this->app->Secure->GetPOST('bezeichnung');
    if(empty($bezeichnung)) {
      return new JsonResponse(['error'=>'Bitte füllen Sie die Bezeichnung aus'], JsonResponse::HTTP_BAD_REQUEST);
    }
    $projekt = (string)$this->app->Secure->GetPOST('projekt');
    if($projekt !== ''){
      $projekt = (int)$this->app->erp->ReplaceProjekt(1, $projekt, 1);
    }
    else {
      $projekt = 0;
    }
    $aktiv = (int)(bool)$this->app->Secure->GetPOST('aktiv');
    $importwarteschlange = (int)(bool)$this->app->Secure->GetPOST('importwarteschlange');
    $cleanutf8 = (int)(bool)$this->app->Secure->GetPOST('cleanutf8');
    $remotedomain = $this->app->Secure->GetPOST('remotedomain');
    $initkey = $this->app->Secure->GetPOST('initkey');
    $importwarteschlange_name = $this->app->Secure->GetPOST('importwarteschlange_name');
    $event_url = $this->app->Secure->GetPOST('event_url');
    $isHtmlTransformation = (int)(bool)$this->app->Secure->GetPOST('ishtmltransformation');
    $api_permissions = $this->prepareApiPermissions($this->app->Secure->GetPOST('api_permissions'));
    if($id <= 0) {
      $this->app->DB->Insert(
        sprintf(
          "INSERT INTO `api_account` 
            (`bezeichnung`, `initkey`, `importwarteschlange_name`, `event_url`, `remotedomain`, `aktiv`, 
             `importwarteschlange`, `cleanutf8`, `uebertragung_account`, `projekt`, `permissions`, `ishtmltransformation`) 
             VALUES ('%s', '%s', '%s', '%s', '%s', %d,
             %d, %d, 0, %d, '%s', %d) ",
          $bezeichnung, $initkey, $importwarteschlange_name, $event_url, $remotedomain, $aktiv,
          $importwarteschlange, $cleanutf8, $projekt, $api_permissions, $isHtmlTransformation
        )
      );
      $id = (int)$this->app->DB->GetInsertID();
      if($id){
        $data = ['success' => true, 'id' => $id];
        return new JsonResponse($data);
      }
      return new JsonResponse(['error'=>'Account konnte nicht erstellt werden'], JsonResponse::HTTP_BAD_REQUEST);
    }

    $data = $this->app->DB->SelectRow(
      sprintf(
        "SELECT a.id, a.bezeichnung, a.aktiv, p.abkuerzung AS `projekt`, a.remotedomain, a.initkey,
          a.importwarteschlange, a.importwarteschlange_name, a.cleanutf8, a.event_url, a.permissions, a.ishtmltransformation
        FROM `api_account` AS `a` 
        LEFT JOIN `projekt` AS `p` ON a.projekt = p.id
        WHERE a.id = %d",
        $id
      )
    );
    if(empty($data)) {
      return new JsonResponse(['error'=>'Account nicht gefunden'], JsonResponse::HTTP_BAD_REQUEST);
    }
    $this->app->DB->Update(
      sprintf(
        "UPDATE `api_account` 
        SET `bezeichnung` = '%s',
         `initkey` = '%s',
         `importwarteschlange_name` = '%s',
         `event_url` = '%s',
         `remotedomain` = '%s',
         `aktiv` = %d,
         `importwarteschlange` = %d,
         `cleanutf8` = %d,
         `uebertragung_account` = 0,
         `projekt` = %d   ,       
         `permissions` = '%s',
         `ishtmltransformation` = %d   
        WHERE `id` = %d",
        $bezeichnung, $initkey, $importwarteschlange_name, $event_url, $remotedomain, $aktiv,
        $importwarteschlange, $cleanutf8, $projekt, $api_permissions, $isHtmlTransformation, $id
      )
    );
    if(empty($this->app->DB->error())) {
      $data = ['success' => true, 'id' => $id];
      return new JsonResponse($data);
    }
    return new JsonResponse(['error'=>'Account konnte nicht geändert werden'], JsonResponse::HTTP_BAD_REQUEST);
  }

  private function prepareApiPermissions(array $apiPermissions){
    $cleanedPermissions = [];
    foreach ($apiPermissions as $permission => $value){
      if($value === 'true'){
        $cleanedPermissions[] = $permission;
      }
    }

    return json_encode($cleanedPermissions);
  }

  public function Api_AccountList(){
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'get') {
      return $this->HandleGetAjaxAction();
    }
    if($cmd === 'save') {
      return $this->HandleSaveAjaxAction();
    }

    $apiPermissions = $this->app->DB->SelectArr("SELECT * FROM `api_permission`");

    $groupedApiPermissions = [];
    foreach ($apiPermissions as $apiPermission){
      $groupedApiPermissions[$apiPermission['group']][] =$apiPermission;
    }

    $apiPermissionsHtml = '';
    foreach ($groupedApiPermissions as $group => $permissions) {
      $apiPermissionsHtml .= '<tr>';
      $apiPermissionsHtml .= "<td>{$group}</td>";
      $apiPermissionsHtml .= "<td>";
      foreach ($permissions as $permission){
        $apiPermissionsHtml .= "<label for='{$permission['key']}'>";
        $apiPermissionsHtml .= "<input class='permission-checkbox' type='checkbox' name='{$permission['key']}'>";
        $apiPermissionsHtml .= "&nbsp;&nbsp;{$permission['key']}</label>";
        $apiPermissionsHtml .= "<br>";
      }
      $apiPermissionsHtml .= "</td>";
      $apiPermissionsHtml .= '</tr>';
    }

    $this->app->YUI->TableSearch('TAB1','api_account_list', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->erp->MenuEintrag('#', 'Neu');
    $this->app->erp->MenuEintrag('index.php?module=api_account&action=list', '&Uuml;bersicht');
    $this->app->erp->Headlines('API Account');
    $this->app->Tpl->Set('API_PERMISSIONS_HTML', $apiPermissionsHtml);
    $this->app->YUI->Autocomplete('projekt', 'projektname', 1);
    $this->app->Tpl->Parse('PAGE','api_account_list.tpl');
  }
}
