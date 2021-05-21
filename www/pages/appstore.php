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
use Xentral\Modules\SystemConfig\SystemConfigModule;

class Appstore {
  /** @var Application $app */
  var $app;

  /** @var array $deactivatesModules */
  protected $deactivatesModules = [];

  /** @var array $cachedAppsList */
  protected static $cachedAppsList = [];

  /** @var array $nodeactivateable */
  private $nodeactivateable = [];

  /** @var mixed $buyList */
  protected $buyList = null;

  /** @var mixed $buyInfo */
  protected $buyInfo = null;

  const MODULE_NAME = 'Appstore';

  public $javascript = [
    './classes/Modules/Appstore/www/js/appstore.js',
    './classes/Modules/Appstore/www/js/appstore_buy.js',
    './www/js/counter-component.js',
  ];

  /**
   * Appstore constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app = $app;
    if($intern) {
      return;
    }
    $this->deactivatesModules[$this->app->Conf->WFdbname] = null;
    $this->nodeactivateable = [
      'appstore',
      'firmendaten',
      'artikel',
      'auftrag',
      'lieferschein',
      'systemlog',
      'dsgvo',
      'dataprotection',
      'pdfarchiv',
      'orderstatus',
      'welcome',
      'ajax',
    ];
    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler('list', 'AppstoreList');
    $this->app->ActionHandler('testmodule', 'AppStoreTestModule');
    $this->app->ActionHandler('activate', 'AppstoreActivate');
    $this->app->ActionHandler('deactivate', 'AppstoreDeactivate');

    $this->app->ActionHandler('testen', 'AppstoreTesten');
    $this->app->DefaultActionHandler('list');
    $this->app->ActionHandlerListen($app);
  }

  public function AppStoreTestModule(): void
  {
    //for user-rights do not remove
  }

  public function addFav(): void
  {
    $apps = $this->getAppsList();
    $module = $this->app->Secure->GetGET('module');
    $id = $this->app->Secure->GetGET('id');
    $module2 = $this->getSubModule($id, $module);
    $favModuleName = $module;
    if(!empty($apps['installiert'])) {
      foreach($apps['installiert'] as $app) {
        if(!empty($module2) && $app['key'] === $module2) {
          $favModuleName = $app['Bezeichnung'];
          break;
        }
        if($app['key'] === $module) {
          $favModuleName = $app['Bezeichnung'];
          break;
        }
      }
    }
    $this->app->Tpl->Set('FAVMODULENAME', $favModuleName);
    $this->app->Tpl->Parse('BODYENDE','addfav.tpl');
  }

  /**
   * @param int    $id
   * @param string $module
   *
   * @return string
   */
  public function getSubModule($id, $module): string
  {
    $module2 = '';
    if($id > 0) {
      switch($module) {
        case 'onlineshops':
          $module2 = (String)$this->app->DB->Select(
            sprintf(
              'SELECT `modulename` FROM `shopexport` WHERE `id` = %d LIMIT 1',
              $id
            )
          );
          break;
        case 'uebertragungen':
          $module2 = (String)$this->app->DB->Select(
            sprintf(
              'SELECT `xml_pdf` FROM `uebertragungen_account` WHERE `id` = %d LIMIT 1',
              $id
            )
          );
          if(strpos($module2,'Transfer') !== 0) {
            $module2 = '';
          }
          break;
        case 'versandarten':
          $module2 = (String)$this->app->DB->Select(
            sprintf(
              'SELECT `modul` FROM `versandarten` WHERE `id` = %d LIMIT 1',
              $id
            )
          );
          if(!empty($module2)) {
            $module2 = 'versandarten_'.$module2;
          }
          break;
        case 'zahlungsweisen':
          $module2 = (String)$this->app->DB->Select(
            sprintf(
              'SELECT `modul` FROM `zahlungsweisen` WHERE `id` = %d LIMIT 1',
              $id
            )
          );
          if(!empty($module2)) {
            $module2 = 'zahlungsweise_'.$module2;
          }
          break;
        case 'drucker':
          $module2 = (String)$this->app->DB->Select(
            sprintf(
              'SELECT `anbindung` FROM `drucker` WHERE `id` = %d LIMIT 1',
              $id
            )
          );
          if(strpos($module2,'Printer') !== 0) {
            $module2 = '';
          }
          break;
        case 'konten':
          $module2 = (String)$this->app->DB->Select(
            sprintf(
              'SELECT `type` FROM `konten` WHERE `id` = %d LIMIT 1',
              $id
            )
          );
          if(!empty($module2)) {
            $module2 = 'konto_'.$module2;
          }
          break;
      }
    }

    return $module2;
  }


  /**
   * @param bool $parameter
   *
   * @return null|string
   */
  protected function sendBeta($parameter): ?string
  {
    $setBeta = $parameter;
    $ok = null;
    include_once dirname(__DIR__, 2).'/upgradesystemclient2_includekey.php';

    return $ok;
  }

  /**
   * @param array $parameter
   *
   * @return null|string
   */
  protected function sendBuyFromDemo($parameter): ?string
  {
    $buyFromDemo = $parameter;
    $ok = null;
    include_once dirname(__DIR__, 2).'/upgradesystemclient2_includekey.php';

    return $ok;
  }

  /**
   * @param array $parameter
   *
   * @return null|string
   */
  protected function sendBuy($parameter): ?string
  {
    $buy = $parameter;
    $ok = null;
    include_once dirname(__DIR__, 2).'/upgradesystemclient2_includekey.php';
    $this->resetBuyListCache();

    return $ok;
  }

  /**
   * @return null|string
   */
  protected function sendGetBuyList(): ?string
  {
    $getBuyList = true;
    $ok = null;
    include_once dirname(__DIR__, 2).'/upgradesystemclient2_includekey.php';
    $this->SetKonfigurationValue('appstore_buy_lasttime', (new DateTime())->format('Y-m-d H:i:s'));
    $this->SetKonfigurationValue('appstore_buy_list', $ok);

    return $ok;
  }
  /**
   * @return null|string
   */
  protected function sendGetBuyInfo(): ?string
  {
    $getBuyInfo = true;
    $ok = null;
    include_once dirname(__DIR__, 2).'/upgradesystemclient2_includekey.php';

    return $ok;
  }

  /**
   * @return JsonResponse
   */
  protected function HandleGetBuyInfoAjaxAction(): JsonResponse
  {
    $data = $this->getActualBuyInfo();
    if(is_string($data)) {
      $json = @json_decode($data, true);
      if(!empty($json)) {
        $data = $json;
      }
    }

    $html = '';
    if(!empty($data['summedAbos']['modul'])) {
      $html = '<table><tr><td><b>Modul</b></td><td><b>Preis</b></td></tr>';
      foreach($data['summedAbos']['modul'] as $articleId => $module) {
        $module['preis'] = $this->formatBuyPrice($module['preis']);
        $html .= '<tr>
        <td>'.htmlspecialchars($module['bezeichnung']).'</td>
        <td>'.number_format($module['preis'],2,',','.').'</td>
        </tr>';
      }
      $html .= '</table>';
    }

    $ret = [
      'message' => $data,
      'data'    => $html,
    ];

    if(!empty($data['summedAbos']['user'])) {
      $art = reset($data['summedAbos']['user']);
      $ret['user'] = (int)$art['menge'];
    }
    if(!empty($data['summedAbos']['lightuser'])) {
      $art = reset($data['summedAbos']['lightuser']);
      $ret['lightuser'] = (int)$art['menge'];
    }

    $maxUser = erpAPI::Ioncube_Property('maxuser');
    $maxLightUser = erpAPI::Ioncube_Property('maxlightuser');
    $ret['maxuser'] = (int)$maxUser;
    $ret['maxlightuser'] = (int)$maxLightUser;
    if(!empty($data['customerinfo'])) {
      $ret = array_merge($ret, $data['customerinfo']);
      $ret['customerinfo'] = $this->getCustomInfoHtml($data['customerinfo']);
    }

    //$this->resetBuyListCache();
    return new JsonResponse($ret);
  }

  /**
   * @param array $customerinfos
   *
   * @return string
   */
  protected function getCustomInfoHtml($customerinfos): string
  {
    $html = '<table><tr>';
    $rows = [];
    foreach($customerinfos as $key => $customerinfo) {
      $rows[] = '<td>'.htmlspecialchars(ucfirst($key)).'</td><td>'.htmlspecialchars($customerinfo).'</td>';
    }

    return $html.implode('</tr><tr>', $rows).'</tr></table>';
  }

  /**
   * @return JsonResponse
   */
  protected function HandleGetBuyListAjaxAction(): JsonResponse
  {
    $data = $this->sendGetBuyList();
    $data = ['message' => $data];

    return new JsonResponse($data);
  }

  /**
   * @return JsonResponse
   */
  protected function HandleSendBuyFromDemoAjaxAction(): JsonResponse
  {
    $data = [
      'company' => $this->app->Secure->GetPOST('company', '', '', true),
      'name' => $this->app->Secure->GetPOST('name', '', '', true),
      'email' => $this->app->Secure->GetPOST('email', '', '', true),
      'street' => $this->app->Secure->GetPOST('street', '', '', true),
      'street2' => $this->app->Secure->GetPOST('street2', '', '', true),
      'zip' => $this->app->Secure->GetPOST('zip', '', '', true),
      'city' => $this->app->Secure->GetPOST('city', '', '', true),
      'country' => $this->app->Secure->GetPOST('country'),
      'bank' => $this->app->Secure->GetPOST('bank', '', '', true),
      'bankname' => $this->app->Secure->GetPOST('bankname', '', '', true),
      'iban' => $this->app->Secure->GetPOST('iban', '', '', true),
      'bic' => $this->app->Secure->GetPOST('bic', '', '', true),
      'user' => (int)$this->app->Secure->GetPOST('user'),
      'agreement' => (int)$this->app->Secure->GetPOST('agreement'),
    ];

    if($data['user'] <= 0) {
      $data['user'] = null;
    }

    $mapping = [
      'company' => 'Firma',
      'name' => 'Ansprechpartner',
      'email' => 'Email',
      'street' => 'Straße',
      'zip' => 'PLZ',
      'city' => 'Ort',
      'country' => 'Land',
      'bank' => 'Bank',
      'bankname' => 'Kontoinhaber',
      'iban' => 'IBAN',
      'bic' => 'BIC',
      'user' => 'Benutzeranzahl',
      'agreement' => 'Einverständnis',
    ];
    $isTestlizenz = !empty(erpAPI::Ioncube_Property('testlizenz'));
    if(!$isTestlizenz) {
      unset($mapping['agreement']);
      $field = 'add_user';
      $new = $data['user'];
      $old = erpAPI::Ioncube_Property('maxuser');
      return $this->SendBuyInfo($field, $new, $old);
    }
    $error = [];

    foreach($mapping as $field => $name) {
      if(empty($data[$field])) {
          $error[] = $field;
      }
    }

    $isInvalidIban = !empty($data['iban']) && !$this->isValidIban($data['iban']);

    if(!empty($error)) {
      $errorMessage = 'Pflichtfelder sind nicht ausgefüllt.';
      if($isInvalidIban) {
        $error[] = 'iban';
        return new JsonResponse(
          [
            'status' => 0,
            'errorMessage' => $errorMessage.' IBAN ist nicht gültig.',
            'invalidFields' => $error
          ]
        );
      }
      return new JsonResponse(
        ['status' => 0, 'errorMessage' => $errorMessage, 'invalidFields' => $error]
      );
    }

    if($isInvalidIban) {
      return new JsonResponse(
        ['status' => 0, 'errorMessage' => 'IBAN ist nicht gültig.', 'invalidFields' => ['iban']]
      );
    }
    $data['change'] =  (int)$this->app->Secure->GetPOST('change');
    if(empty($this->app->erp->Firmendaten('lizenz')) || empty($this->app->erp->Firmendaten('schluessel'))) {
      return new JsonResponse(
        ['status' => 0, 'errorMessage' => 'Es sind keine Lizenzdaten in den Firmendaten eingestellt']
      );
    }
    $data = $this->sendBuyFromDemo($data);
    if(is_string($data)){
      if($data === 'ERROR' || $data === '') {
        return new JsonResponse(
          ['status' => 0, 'errorMessage' => 'Lizenz in Firmendaten scheinen nicht zu stimmen.']
        );
      }
      if(strpos($data,'ERROR') === 0) {
        return new JsonResponse(
          ['status' => 0, 'errorMessage' => trim(substr($data,5))]
        );
      }
    }
    $this->app->User->SetParameter('updatekey', 1);
    $this->resetBuyListCache();
    $this->resetBuyInfoCache();
    $this->app->Tpl->Set('appstore_customeruser', '');

    return new JsonResponse(['status' => $data]);
  }

  /**
   * @return JsonResponse
   */
  protected function HandleSendBuyInfoAjaxAction(): JsonResponse
  {
    $field = $this->app->Secure->GetPOST('field');
    $new = $this->app->Secure->GetPOST('new');
    $old = $this->app->Secure->GetPOST('old');

    return $this->SendBuyInfo($field, $new, $old);
  }

  /**
   * @param $field
   * @param $new
   * @param $old
   *
   * @return JsonResponse
   */
  public function SendBuyInfo($field, $new, $old): JsonResponse
  {
    if($field === 'reduce_user') {
      $possibleToChangeUser =$this->isPossibleToChangeUser('user', $new);
      if(!empty($possibleToChangeUser['error'])) {
        return new JsonResponse($possibleToChangeUser);
      }
    }
    if($field === 'reduce_lightuser') {
      $possibleToChangeUser =$this->isPossibleToChangeUser('lightuser', $new);
      if(!empty($possibleToChangeUser['error'])) {
        return new JsonResponse($possibleToChangeUser);
      }
    }
    $input = ['field'=>$field, 'value_new'=>$new, 'value_old'=>$old,];
    $data = $this->sendBuy($input);
    if($data === 'OK') {
      $this->app->User->SetParameter('updatekey', 1);
      if($field === 'add_module') {
        $this->addModuleForUpdateInfo($new);
      }
      if($field === 'delete_module') {
        $this->removeModuleForUpdateInfo($new);
      }
      $this->resetBuyListCache();
      $this->resetBuyInfoCache();
      $msg = $this->app->erp->base64_url_encode(
        '<div class="info">Anfrage erfolgreich ausgeführt. Sie können in etwa 15 Minuten ein Update herunterladen</div>'
      );
      return new JsonResponse(['status' => true, 'url' => 'index.php?module=appstore&action=buy&msg='.$msg]);
    }

    return new JsonResponse(['status' => false, 'error' => $data]);
  }

  /**
   * @param string $module
   */
  protected function addModuleForUpdateInfo($module): void
  {
    $modules = $this->getModulesForUpdateInfo();
    if(in_array($module, $modules)) {
      return;
    }
    $modules[] = $module;
    $this->SetKonfigurationValue(
      'appstore_modules_for_update_info',
      json_encode($modules)
    );
  }

  /**
   * @param string $module
   */
  protected function removeModuleForUpdateInfo($module): void
  {
    $modules = $this->getModulesForUpdateInfo(false);
    if(!in_array($module, $modules)) {
      return;
    }
    $modules = array_diff($modules, [$module]);
    $this->SetKonfigurationValue(
      'appstore_modules_for_update_info',
      json_encode($modules)
    );
  }

  /**
   * @var bool $removeIfModuleExists
   *
   * @return array
   */
  protected function getModulesForUpdateInfo($removeIfModuleExists = true): array
  {
    $modules = $this->GetBuyKonfigurationWithResetOnError('appstore_modules_for_update_info');
    if(empty($modules)) {
      return [];
    }
    $modules = json_decode($modules, true);
    if(empty($modules) || !is_array($modules)) {
      return [];
    }

    if(!$removeIfModuleExists) {
      return $modules;
    }

    foreach($modules as $key => $module) {
      if($this->app->erp->ModulVorhanden($module, true)) {
        $this->removeModuleForUpdateInfo($module);
        unset($modules[$key]);
      }
    }

    return $modules;
  }

  public function resetBuyListCache(): void
  {
    $this->buyList = null;
    $this->SetKonfigurationValue('appstore_buy_lasttime', '');
  }

  public function resetBuyInfoCache(): void
  {
    $this->buyInfo = null;
    $this->SetKonfigurationValue('appstore_buyinfo_lasttime', '');
  }

  /**
   * @param string $key
   * @param mixed  $value
   */
  public function SetKonfigurationValue(string $key, $value): void
  {
    if(strpos($key, 'appstore_') === 0) {
      $key = substr($key, 9);
    }
    try {
      /** @var SystemConfigModule $systemConfigModule */
      $systemConfigModule = $this->app->Container->get('SystemConfigModule');
      $systemConfigModule->setValue('appstore', $key, (string)$value);
    }
    catch(Exception $e) {

    }
  }

  /**
   * @param string $key
   *
   * @return string|null
   */
  public function GetKonfiguration(string $key): ?string
  {
    if(strpos($key, 'appstore_') === 0) {
      $key = substr($key, 9);
    }
    /** @var SystemConfigModule $systemConfigModule */
    $systemConfigModule = $this->app->Container->get('SystemConfigModule');

    return $systemConfigModule->tryGetValue('appstore', $key);
  }

  /**
   * @return mixed|string|null
   */
  public function getActualBuyInfo()
  {
    if($this->buyInfo !== null) {
      return $this->buyInfo;
    }

    $lastBuyInfo = $this->GetBuyKonfigurationWithResetOnError('appstore_buyinfo_lasttime');
    try{
      if(!empty($lastBuyInfo)) {
        $lastBuyInfo = new DateTime($lastBuyInfo);
        if($lastBuyInfo > new DateTime('-1 hour')) {
          $lastBuyInfo = null;
        }
        else {
          $lastBuyInfo = (new DateTime())->format('Y-m-d H:i:s');
        }
      }
      else {
        $lastBuyInfo = (new DateTime())->format('Y-m-d H:i:s');
      }
    }
    catch (Exception $e) {
      $lastBuyInfo = (new DateTime())->format('Y-m-d H:i:s');
    }

    $buyInfo = $this->GetBuyKonfigurationWithResetOnError('appstore_buyinfo_list');
    $buyInfoDecoded = !empty($buyInfo) ? json_decode($buyInfo, true) : [];
    $buyInfoCustomerBuy = $this->GetBuyKonfigurationWithResetOnError('appstore_buyinfo_customer_buy');
    $buyInfoCustomerBuy = empty($buyInfoCustomerBuy) ? []: json_decode($buyInfoCustomerBuy, true);
    $moduleMapping = $this->GetBuyKonfigurationWithResetOnError('appstore_buyinfo_module_mapping');
    $moduleMapping = empty($moduleMapping) ? []: json_decode($moduleMapping, true);
    $buyInfoDecoded['customer_buy'] = $buyInfoCustomerBuy;
    $buyInfoDecoded['module_mapping'] = $moduleMapping;
    $buyInfo = json_encode($buyInfoDecoded);
    if(!empty($lastBuyInfo)) {
      $buyInfo = $this->sendGetBuyInfo();
      $buyInfoDecoded = json_decode($buyInfo, true);
      $buyInfoCustomerBuy = null;
      $moduleMapping = null;
      if(isset($buyInfoDecoded['customer_buy'])){
        $buyInfoCustomerBuy = $buyInfoDecoded['customer_buy'];
        unset($buyInfoDecoded['customer_buy']);
      }
      if(isset($buyInfoDecoded['module_mapping'])){
        $moduleMapping = $buyInfoDecoded['module_mapping'];
        unset($buyInfoDecoded['module_mapping']);
      }
      $buyInfoCustomerBuy = json_encode($buyInfoCustomerBuy);
      $moduleMapping = json_encode($moduleMapping);
      $buyInfoPart = json_encode($buyInfoDecoded);

      $this->SetKonfigurationValue(
        'appstore_buyinfo_lasttime', $lastBuyInfo
      );
      $this->SetKonfigurationValue(
        'appstore_buyinfo_list', $buyInfoPart
      );
      $this->SetKonfigurationValue(
        'appstore_buyinfo_customer_buy', $buyInfoCustomerBuy
      );
      $this->SetKonfigurationValue(
        'appstore_buyinfo_module_mapping', $moduleMapping
      );
    }

    if(is_string($buyInfo)){
      $json = json_decode($buyInfo, true);
      if(!empty($json)) {
        $buyInfo = $json;
      }
    }

    if(!is_array($buyInfo)) {
      $this->buyList = $buyInfo;
      return $buyInfo;
    }

    $this->buyList = $buyInfo;

    return $buyInfo;
  }

  /**
   * @param string $key
   *
   * @return string
   */
  public function GetBuyKonfigurationWithResetOnError(string $key): string
  {
    $value = (string)$this->GetKonfiguration($key);
    if(strpos($value, 'ERROR') === 0) {
      $this->SetKonfigurationValue($key, '');
      return '';
    }

    return $value;
  }

  /**
   * @return mixed|string|null
   */
  public function getActualBuyList()
  {
    if($this->buyList !== null) {
      return $this->buyList;
    }
    $lastBuyList = $this->GetBuyKonfigurationWithResetOnError('appstore_buy_lasttime');

    try{
      if(!empty($lastBuyList)) {
        $lastBuyList = new DateTime($lastBuyList);
        if($lastBuyList > new DateTime('-1 hour')) {
          $lastBuyList = null;
        }
        else {
          $lastBuyList = (new DateTime())->format('Y-m-d H:i:s');
        }
      }
      else {
        $lastBuyList = (new DateTime())->format('Y-m-d H:i:s');
      }
    }
    catch (Exception $e) {
      $lastBuyList = (new DateTime())->format('Y-m-d H:i:s');
    }
    $buyList = $this->GetBuyKonfigurationWithResetOnError('appstore_buy_list');
    if(!empty($lastBuyList)) {
      $buyList = $this->sendGetBuyList();
      $this->SetKonfigurationValue('appstore_buy_lasttime', $lastBuyList);
      $this->SetKonfigurationValue('appstore_buy_list', $buyList);
    }

    if(is_string($buyList)){
      $json = json_decode($buyList, true);
      if(!empty($json)) {
        $buyList = $json;
      }
    }

    if(!is_array($buyList)) {
      $this->buyList = $buyList;
      return $buyList;
    }

    $ret = [
      'user' => [],
      'lightuser' => [],
      'modules' => [],
      'modulearticle' => [],
      'cloud' => [],
    ];

    foreach($buyList as $buyItem) {
      if($buyItem['typ'] === 'version') {
        $ret['user'] = $buyItem;
        continue;
      }
      if($buyItem['typ'] === 'lightuser') {
        $ret['lightuser'] = $buyItem;
        continue;
      }
      if($buyItem['typ'] === 'cloud') {
        $ret['cloud'] = $buyItem;
        continue;
      }
      $ret['modulearticle'][] = $buyItem;
      $modules = explode(',', $buyItem['parameter']);
      foreach($modules as $module) {
        $module = trim($module);
        if(empty($module) || in_array($module, $ret['modules'])) {
          continue;
        }
        $ret['modules'][] = $module;
      }
    }

    $this->buyList = $ret;

    return $ret;
  }

  /**
   * @param string $type
   * @param int    $count
   *
   * @return array
   */
  private function isPossibleToChangeUser($type, $count)
  {
    $oldUser = (int)erpAPI::Ioncube_Property('maxuser');
    $oldLightUser = (int)erpAPI::Ioncube_Property('maxlightuser');
    $maxUser = $oldUser;
    $maxLightUser = $oldLightUser;
    if($type === 'user') {
      $maxUser = $count;
    }
    elseif($type === 'lightuser') {
      $maxLightUser = $count;
    }

    $maxTimeUser = $this->app->erp->ModulVorhanden('mitarbeiterzeiterfassung') ? $maxUser : 0;
    $countActiveLightUser = 0;
    if($maxLightUser > 0) {
      $countActiveLightUser = (int)$this->app->DB->Select(
        "SELECT COUNT(DISTINCT u.id) FROM `user` AS `u` WHERE u.activ = 1 AND u.type = 'lightuser'"
      );
      $countActiveStandardUser = (int)$this->app->DB->Select(
        "SELECT COUNT(u.id) FROM `user` AS `u` WHERE u.activ = 1 AND u.hwtoken IS NOT NULL AND u.hwtoken <> 4"
        ) - $countActiveLightUser;
      $countActiveTimeAccountUser = (int)$this->app->DB->Select(
        "SELECT COUNT(*) FROM `user` AS `u` WHERE u.activ = 1 AND u.hwtoken = 4 AND u.type != 'lightuser'"
      );
    }
    else{
      $countActiveStandardUser = (int)$this->app->DB->Select(
        "SELECT COUNT(*) FROM `user` AS `u` WHERE u.activ = 1 AND u.hwtoken <> 4"
      );
      $countActiveTimeAccountUser = (int)$this->app->DB->Select(
        "SELECT COUNT(*) FROM `user` AS `u` WHERE u.activ = 1 AND u.hwtoken = 4"
      );
    }

    $isStandardUserOK = $countActiveStandardUser <= $maxUser;
    $isLightUserOK = $countActiveStandardUser + $countActiveTimeAccountUser + $countActiveLightUser
      <= $maxTimeUser + $maxUser + $maxLightUser;
    $isTimeUserOk = ($countActiveStandardUser + $countActiveTimeAccountUser)
      <= $maxTimeUser + $maxUser;

    if($isLightUserOK && $isStandardUserOK && $isTimeUserOk) {
      return ['status' => true];
    }

    if($type === 'user') {
      $minStandard = $countActiveStandardUser;
      if($maxTimeUser > 0) {
        $minStandard2 = (int)ceil(($countActiveStandardUser
          + $countActiveTimeAccountUser
          + $countActiveLightUser
          - $maxLightUser)/ 2);
        $minStandard3 = (int)ceil(($countActiveStandardUser + $countActiveTimeAccountUser) / 2);
      }
      else {
        $minStandard2 = $countActiveStandardUser
          + $countActiveTimeAccountUser
          + $countActiveLightUser
          - $maxLightUser;
        $minStandard3 = $countActiveStandardUser + $countActiveTimeAccountUser;
      }
      if($minStandard2 > $minStandard) {
        $minStandard = $minStandard2;
      }
      if($minStandard3 > $minStandard) {
        $minStandard = $minStandard3;
      }
      if($minStandard > $maxUser) {
        if($minStandard >= $oldUser) {
          return [
            'status' => false,
            'error' => sprintf(
              'Es sind %d Benutzer aktiv, Sie können keine Benutzer kündigen.
               Deaktivieren Sie nicht benötigte Accounts.',
              $countActiveLightUser + $countActiveTimeAccountUser + $countActiveStandardUser
            )
          ];
        }
        return [
          'status' => false,
          'error' => sprintf(
            'Es sind %d Benutzer aktiv, sie können die Benutzerzahl auf %d reduzieren oder 
            nicht benötigte Accounts deaktivieren.',
            $countActiveLightUser + $countActiveTimeAccountUser + $countActiveStandardUser,
            $minStandard
          )
        ];
      }
    }
    elseif($type === 'lightuser') {
      $minLight = $countActiveLightUser + $countActiveTimeAccountUser + $countActiveStandardUser
        - $maxUser - $maxTimeUser;
      if($minLight > $maxLightUser) {
        if($minLight >= $oldLightUser) {
          return [
            'status' => false,
            'error' => sprintf(
              'Es sind %d Light-User aktiv. Deaktivieren Sie nicht benötigte Accounts um fortzusetzen.',
              $countActiveLightUser
            )
          ];
        }
        return [
          'status' => false,
          'error' => sprintf(
            'Es sind %d Light-User aktiv, sie können die Benutzerzahl auf %d reduzieren oder 
            nicht benötigte Accounts deaktivieren.',
            $countActiveLightUser,
            $minLight
          )
        ];
      }
    }

    return [
      'status' => false,
      'error' => sprintf(
        'Es sind %d Benutzer aktiv',
        $countActiveLightUser + $countActiveTimeAccountUser + $countActiveStandardUser
      )
    ];
  }

  /**
   * @param string $key
   *
   * @return array|null
   */
  public function getBuyModuleFromModuleKey($key)
  {
    $buyList = $this->getActualBuyList();
    if(empty($buyList['modulearticle'])) {
      return null;
    }
    foreach($buyList['modulearticle'] as $mod) {
      if($mod['parameter'] === $key) {
        return $mod;
      }
    }

    return null;
  }

  /**
   * @param string $price
   *
   * @return string
   */
  public function formatBuyPrice($price)
  {
    if(empty($price)) {
      return $price;
    }
    if(substr($price, -3) === '.00') {
      return substr($price, 0, -3);
    }

    return $price;
  }

  public function AppstoreBuy()
  {
    $this->app->Location->execute('index.php?module&appstore&action=list');
  }

  /**
   * @return string[]
   */
  protected static function getHubspotEvents()
  {
    return [
      'RENTBUTTONHUBSPOTEVENT' => 'buyClick1',
      'RENTPOPUPHUBSPOTEVENT' => 'buyClick2',
      'RENTPOPUPOKHUBSPOTEVENT' => 'buyOk',
      'RENTPOPUPABORTHUBSPOTEVENT' => 'buyAbort',
      'RENTPOPUPERRORHUBSPOTEVENT' => 'buyError',
      'RENTPOPUPINITHUBSPOTEVENT' => 'buyInit',
    ];
  }

  protected function showUpdateMessage(): void
  {
    if(!$this->canBuy()) {
      return;
    }
    if($this->app->User->GetType() !== 'admin') {
      return;
    }
    $moduleForInfos = $this->getModulesForUpdateInfo();
    if(empty($moduleForInfos)) {
      return;
    }
    $this->app->Tpl->Set(
      'MESSAGE',
      '<div class="info">Es sind Module zum Herunterladen verfügbar. Sie können nun ein Update installieren. 
      <a target="_blank" href="update.php?rand='.sha1(uniqid('', true)).'"><input type="button" value="Update starten" /></a></div>'
    );
  }

  /**
   * @return bool
   */
  public function canBuy(): bool
  {
    return method_exists('erpAPI', 'Ioncube_Property') && !empty(erpAPI::Ioncube_Property('canbuy'));
  }

  /**
   * @param string $iban
   *
   * @return bool
   */
  public function isValidIban($iban): bool
  {
    $iban = str_replace(' ','', strtolower($iban));
    $countries = [
      'al' => 28, 'ad' => 24, 'at' => 20, 'az' => 28, 'bh' => 22, 'be' => 16, 'ba' => 20, 'br' => 29, 'bg' => 22,
      'cr' => 21, 'hr' => 21, 'cy' => 28, 'cz' => 24, 'dk' => 18, 'do' => 28, 'ee' => 20, 'fo' => 18, 'fi' => 18,
      'fr' => 27, 'ge' => 22, 'de' => 22, 'gi' => 23, 'gr' => 27, 'gl' => 18, 'gt' => 28, 'hu' => 28, 'is' => 26,
      'ie' => 22, 'il' => 23, 'it' => 27, 'jo' => 30, 'kz' => 20, 'kw' => 30, 'lv' => 21, 'lb' => 28, 'li' => 21,
      'lt' => 20, 'lu' => 20, 'mk' => 19, 'mt' => 31, 'mr' => 27, 'mu' => 30, 'mc' => 27, 'md' => 24, 'me' => 22,
      'nl' => 18, 'no' => 15, 'pk' => 24, 'ps' => 29, 'pl' => 28, 'pt' => 25, 'qa' => 29, 'ro' => 24, 'sm' => 27,
      'sa' => 24, 'rs' => 22, 'sk' => 24, 'si' => 19, 'es' => 24, 'se' => 24, 'ch' => 21, 'tn' => 24, 'tr' => 26,
      'ae' => 23, 'gb' => 22, 'vg' => 24
    ];

    $chars = [
      'a' => 10, 'b' => 11, 'c' => 12, 'd' => 13, 'e' => 14, 'f' => 15, 'g' => 16, 'h' => 17, 'i' => 18,
      'j' => 19, 'k' => 20, 'l' => 21, 'm' => 22, 'n' => 23, 'o' => 24, 'p' => 25, 'q' => 26, 'r' => 27,
      's' => 28, 't' => 29, 'u' => 30, 'v' => 31, 'w' => 32, 'x' => 33, 'y' => 34, 'z' => 35
    ];

    if (strlen($iban) !== $countries[ substr($iban,0,2) ]) {
      return false;
    }

    $movedChar = substr($iban, 4) . substr($iban,0,4);
    $movedCharArray = str_split($movedChar);
    $newString = '';

    foreach ($movedCharArray as $k => $v) {

      if ( !is_numeric($movedCharArray[$k]) ) {
        $movedCharArray[$k] = $chars[$movedCharArray[$k]];
      }
      $newString .= $movedCharArray[$k];
    }
    if (function_exists('bcmod')) {
      return bcmod($newString, '97') == 1;
    }

    $take = 5;
    $mod = 0;

    do {
      $a = $mod . substr($newString, 0, $take);
      $newString = substr($newString, $take);
      $mod = (int)$a % 97;
    }
    while (strlen($newString));

    return $mod === 1;
  }

  /**
   * @param string $module
   *
   * @return bool
   */
  public function isModulePossibleToBuy($module): bool
  {
    if(!$this->canBuy()) {
      return false;
    }

      if($this->app->erp->ModulVorhanden($module)) {
        return false;
      }

    $buyList = $this->getActualBuyList();
    if(empty($buyList['modules']) || !is_array($buyList['modules'])) {
      return false;
    }
    return in_array($module, $buyList['modules']);
    //return !empty(erpAPI::Ioncube_Property('canbuymodule'.str_replace('_','X', $module)));
  }


  /**
   * @param bool $intern
   *
   * @return bool|void
   */
  public function AppstoreActivate($intern = false)
  {
    $smodule = $this->app->Secure->GetGET('smodule');
    $activated = $this->activateModule($smodule);
    if($intern) {
      return $activated;
    }
    $this->app->Location->execute('index.php?module=appstore&action=list');
  }

  /**
   * @param bool $intern
   *
   * @return bool|void
   */
  public function AppstoreDeactivate($intern = false)
  {
    $smodule = $this->app->Secure->GetGET('smodule');
    $deactivated = $this->deactivateModule($smodule);
    if($intern) {
      return $deactivated;
    }
    $this->app->Location->execute('index.php?module=appstore&action=list');
  }

  public function addBetaToHeadline(): void
  {
    if(empty($this->app->erp->kurzueberschrift1)) {
      return;
    }
    if(stripos($this->app->erp->kurzueberschrift1, 'BETA') !== false) {
      return;
    }

    $this->app->erp->Headlines('', $this->app->erp->kurzueberschrift1.' <span class="beta">BETA</span>');
  }

  /**
   * @param string $module
   *
   * @return bool
   */
  public function isBeta($module): bool
  {
    if(empty($module)) {
      return false;
    }
    $apps = $this->getAppList();
    if(empty($apps[$module])) {
      return false;
    }

    if(!isset($apps[$module]['beta']) || !empty($apps[$module]['beta'])) {
      return true;
    }

    return false;
  }

  /**
   * @param string   $module
   * @param int      $parameter
   *
   * @return null|string
   */
  public function GetCategoryByModule($module, $parameter = 0): ?string
  {
    if(empty($module)) {
      return null;
    }
    if($parameter > 0) {
      $module2 = $this->app->erp->getSubModule($parameter, $module);
      if(!empty($module2)) {
        $module = (string)$module2;
      }
    }
    $apps = $this->GetApps();
    if(empty($apps['installiert'])) {
      return null;
    }
    $apps = $apps['installiert'];
    $modul = null;
    foreach($apps as $app) {
      if($app['key'] === $module) {
        $modul = $app;
        break;
      }
    }
    if($modul === null) {
      return null;
    }

    if(empty($modul['kategorie'])) {
      return null;
    }

    $category = (string)$modul['kategorie'];
    if(strpos($category,'{|') === 0) {
      $category = substr($category, 2);
    }
    if(substr($category, -2) === '|}') {
      $category = substr($category, 0, -2);
    }

    return (string)$category;
  }

  public function addLegacyToHeadline(): void
  {
    if(empty($this->app->erp->kurzueberschrift1)){
      return;
    }
    if(stripos($this->app->erp->kurzueberschrift1, 'LEGACY') !== false){
      return;
    }
    $this->app->erp->Headlines('', $this->app->erp->kurzueberschrift1.'<span class="legacy">LEGACY</span>');
  }

  /**
   * @param string $module
   *
   * @return bool
   */
  public function isPopular($module): bool
  {
    if(empty($module)){
      return false;
    }
    $apps = $this->getAppList();
    if(empty($apps[$module])){
      return false;
    }

    return !empty($apps[$module]['popular']);
  }

  /**
   * @param string $module
   *
   * @return bool
   */
  public function isLegacy($module): bool
  {
    if(empty($module)){
      return false;
    }
    $apps = $this->getAppList();
    if(empty($apps[$module])){
      return false;
    }

    return !empty($apps[$module]['legacy']);
  }

  public function addSpecialToHeadline(): void
  {
    if(empty($this->app->erp->kurzueberschrift1)){
      return;
    }
    if(stripos($this->app->erp->kurzueberschrift1, 'SPECIAL') !== false){
      return;
    }
    $this->app->erp->Headlines('', $this->app->erp->kurzueberschrift1.'<span class="special">SPECIAL</span>');
  }

  /**
   * @param string $module
   *
   * @return bool
   */
  public function isSpecial($module): bool
  {
    if(empty($module)){
      return false;
    }
    $apps = $this->getAppList();
    if(empty($apps[$module])){
      return false;
    }

    return isset($apps[$module]['special']) && !empty($apps[$module]['special']);
  }

  /**
   * @param string $module
   *
   * @return bool
   */
  public function isProjectSensitive($module): bool
  {
    if(empty($module)){
      return false;
    }
    $apps = $this->getAppList();
    if(empty($apps[$module])){
      return false;
    }
    if(isset($apps[$module]['project_sensitive']) && !empty($apps[$module]['project_sensitive'])){
      return $apps[$module]['project_sensitive'];
    }

    return false;
  }

  /**
   * @return string
   */
  protected function getCacheKey(): string
  {
    return (string)$this->app->Conf->WFdbname;
  }

  protected function clearCache(): void
  {
    $cacheKey = $this->getCacheKey();
    if(isset(self::$cachedAppsList[$cacheKey])) {
      unset(self::$cachedAppsList[$cacheKey]);
    }
  }

  /**
   * @param string $prefix
   * @param bool   $withDeactivated
   *
   * @return array|null
   */
  public function getAppsListWithPrefix($prefix, $withDeactivated = false): ?array
  {
    $apps = $this->getAppsList($withDeactivated);
    if(empty($apps)) {
      return $apps;
    }

    $filteredApps = [];

    if(!empty($apps['installiert'])) {
      $filteredApps['installiert'] = [];
      foreach($apps['installiert'] as $app) {
        if(strpos($app['key'], $prefix) === 0) {
          $filteredApps['installiert'][] = $app;
        }
      }
    }
    if(!empty($apps['kauf'])) {
      $filteredApps['kauf'] = [];
      foreach($apps['kauf'] as $app) {
        if(strpos($app['key'], $prefix) === 0) {
          $filteredApps['kauf'][] = $app;
        }
      }
    }

    return $filteredApps;
  }

  /**
   * @param string $link
   *
   * @return array|null[]
   */
  public function getModuleActionByLink($link): array
  {
    $module = null;
    $action = null;
    $linkArray = explode('?', $link);
    foreach($linkArray as $linkParts) {
      $linka2 = explode('&',$linkParts);
      foreach($linka2 as $k2 => $v2) {
        $linka3 = explode('=',$v2);
        if($linka3[0] === 'module'){
          $module = $linka3[1];
        }
        if($linka3[0] === 'action'){
          $action = $linka3[1];
        }
      }
    }

    return ['module' => $module, 'action' => $action,];
  }

  /**
   * @param array $app
   *
   * @return bool
   */
  public function hasAppUserRights($app): bool
  {
    $link = !empty($app['Link'])?$app['Link']:'';
    $moduleAction = $this->getModuleActionByLink($link);
    $module = $moduleAction['module'];
    $action = $moduleAction['action'];

    return !empty($module) && !empty($action) && $this->app->erp->RechteVorhanden($module, $action);
  }

  /**
   * @param string $key
   *
   * @return null|string
   */
  public function getAppNameByKey($key): ?string
  {
    $apps = $this->getAppList();
    if(empty($apps[$key]) || !isset($apps[$key]['Bezeichnung'])) {
      return null;
    }

    return $apps[$key]['Bezeichnung'];
  }

  /**
   * @param bool $withDeactivated
   *
   * @return null|array
   */
  public function getAppsList($withDeactivated = false): ?array
  {
    $cacheKey = $this->getCacheKey();
    if(
      !empty(self::$cachedAppsList[$cacheKey])
    && !empty(self::$cachedAppsList[$cacheKey][(int)$withDeactivated])
    ) {
      return self::$cachedAppsList[$cacheKey][(int)$withDeactivated];
    }
    $apps = $this->getAppList();
    $res = null;
    $version = $this->app->erp->Version();

    if(empty($apps)){
      return null;
    }
    foreach($apps as $key => $app) {
      $app['key'] = $key;
      if(!isset($app['beta'])) {
        $app['beta'] = true;
      }
      if(!isset($app['legacy'])){
        $app['legacy'] = false;
      }
      if(!isset($app['special'])){
        $app['special'] = false;
      }
      if(!isset($app['project_sensitive'])){
        $app['project_sensitive'] = false;
      }
      $ablaufdatum = $this->app->erp->IoncubeProperty('moduleablaufdatumX'.str_replace('_','X',$key));
      $test = $this->app->erp->IoncubeProperty('testmoduleX'.str_replace('_','X',$key));
      if(!empty($ablaufdatum)) {
        $app['ablaufdatum'] = $ablaufdatum;
      }
      if($test) {
        $app['test'] = $test;
      }
      if($this->app->erp->ModulVorhanden($key, $withDeactivated)) {
        if($app['Versionen'] == '' || $app['Versionen'] === 'ALL') {
          $res['installiert'][] = $app;
          if(!empty($app['Link2'])) {
            $res['installiert'][] = $app;
            $appIndex = count($res['installiert']) - 1;
            $res['installiert'][$appIndex]['key'] .= 'booster';
            $res['installiert'][$appIndex]['Link'] = $app['Link2'];
            $res['installiert'][$appIndex]['Bezeichnung'] = $app['Bezeichnung2'];
            unset($res['installiert'][$appIndex]['Link2'], $res['installiert'][$appIndex]['Bezeichnung2']);
          }
        }
        else {
          $versionen = explode(',',$app['Versionen']);
          if(in_array($version, $versionen)) {
            $res['installiert'][] = $app;
            if(!empty($app['Link2'])) {
              $res['installiert'][] = $app;
              $appIndex = count($res['installiert']) - 1;
              $res['installiert'][$appIndex]['key'] .= 'booster';
              $res['installiert'][$appIndex]['Link'] = $app['Link2'];
              $res['installiert'][$appIndex]['Bezeichnung'] = $app['Bezeichnung2'];
              unset($res['installiert'][$appIndex]['Link2'], $res['installiert'][$appIndex]['Bezeichnung2']);
            }
          }
          elseif(empty($app['showonlywheninstalled'])) {
            $res['kauf'][] = $app;
          }
        }
      }
      else {
        if(empty($app['showonlywheninstalled'])) {
          $app['Link'] = '';
          $res['kauf'][] = $app;
        }
      }
    }

    self::$cachedAppsList[$cacheKey][(int)$withDeactivated] = $res;

    return $res;
  }

  /**
   * @param string $prefix
   *
   * @return array
   */
  public function getAppListWithPrefix($prefix): array
  {
    $apps = $this->getAppList();

    foreach($apps as $appKey => $app) {
      if(strpos($appKey, $prefix) !== 0) {
        unset($apps[$appKey]);
      }
    }

    return $apps;
  }

  /**
   * @param string $prefix
   *
   * @return array
   */
  public function getBetaModulesByPrefix($prefix): array
  {
    $ret = [];
    $apps = $this->getAppListWithPrefix($prefix);
    foreach($apps as $moduleName => $app)  {
      if(!isset($app['beta']) || !empty($app['beta'])) {
        $ret[] = $moduleName;
      }
    }

    return $ret;
  }

  /**
   * @return array
   */
  public function getAppList(): array
  {
    $apps = array(
      'taxdoo'=>array('Bezeichnung'=>'Taxdoo Schnittstelle',
        'Link'=>'index.php?module=taxdoo&action=log',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','install'=>true,'beta' => false,'kategorie'=>'{|Buchhaltung|}','settings'=>true),
      'cronjob_paqato'=>array('Bezeichnung'=>'Paqato Schnittstelle',
         'Link'=>'index.php?module=paqato&action=log',
         'Icon'=>'Icons_dunkel_9.gif',
         'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Schnittstellen|}','settings'=>true),
      'aufgaben'=>array('Bezeichnung'=>'Aufgaben',
        'Link'=>'index.php?module=aufgaben&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'', 'beta' => false,'kategorie'=>'{|Team Funktionen|}'),
      'chat'=>array('Bezeichnung'=>'Chat',
        'Link'=>'index.php?module=chat&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'','install'=>true, 'beta' => false,'kategorie'=>'{|Team Funktionen|}'),
      'docscan'=>array('Bezeichnung'=>'Dokumenten Scanner',
        'Link'=>'index.php?module=docscan&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'','install'=>true, 'beta' => false,'kategorie'=>'{|Belege|}'),
      'vertriebscockpit'=>array('Bezeichnung'=>'Vertriebscockpit',
        'Link'=>'index.php?module=vertriebscockpit&action=wiedervorlagen',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,
        'legacy' => true,'kategorie'=>'{|Verkauf|}',
        'popular' => true,
      ),
      'datanorm'=>array('Bezeichnung'=>'Datanorm Importer',
        'Link'=>'index.php?module=datanorm&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Schnittstellen|}'),

      'adresse_typ'=>array('Bezeichnung'=>'Adresse Typ',
        'Link'=>'index.php?module=adresse_typ&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'', 'beta' => false,'kategorie'=>'{|Stammdaten|}','settings'=>true,
        'project_sensitive' => true),


      'wiedervorlage'=>array('Bezeichnung'=>'Wiedervorlage',
        'Link'=>'index.php?module=wiedervorlage&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Verkauf|}','settings'=>true,
        'project_sensitive' => true),
      /*'stammdatenbereinigen'=>array('Bezeichnung'=>'Stammdaten Bereinigung',
                        'Link'=>'index.php?module=stammdatenbereinigen&action=list',
                        'Icon'=>'Icons_dunkel_1.gif',
                        'Versionen'=>'ALL'),*/
      /*
          'lagerpruefung'=>array('Bezeichnung'=>'Lagerpr&uuml;fung',
                            'Link'=>'index.php?module=lagerpruefung&action=list',
                            'Icon'=>'Icons_dunkel_2.gif',
                            'Versionen'=>'ALL'),
      */
      'layoutvorlagen'=>array('Bezeichnung'=>'Layoutvorlagen',
        'Link'=>'index.php?module=layoutvorlagen&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'', 'beta' => false,'kategorie'=>'{|Workflows|}','settings'=>true),
      'zertifikatgenerator'=>array('Bezeichnung'=>'Zertifikatgenerator',
        'Link'=>'index.php?module=zertifikatgenerator&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Workflows|}'),
      'systemlog'=>array('Bezeichnung'=>'Systemlog',
        'Link'=>'index.php?module=systemlog&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'', 'beta' => false,'kategorie'=>'{|System|}'),
      'filiallieferung'=>array('Bezeichnung'=>'Filiallieferung',
        'Link'=>'index.php?module=filiallieferung&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Lager und Logistik|}',
        'project_sensitive' => true),
      'lagermindestmengen'=>array('Bezeichnung'=>'Lagermindestmengen',
        'Link'=>'index.php?module=lagermindestmengen&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Lager und Logistik|}','settings'=>true,
        'project_sensitive' => true),
      'rechnung_stratedi'=>array('Bezeichnung'=>'EDI Stratedi',
        'Link'=>'index.php?module=rechnung_stratedi&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Schnittstellen|}','settings'=>true),
      'lieferschwelle'=>array('Bezeichnung'=>'Lieferschwelle',
        'Link'=>'index.php?module=lieferschwelle&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Buchhaltung|}','settings'=>true),
      'gefahrgut'=>array('Bezeichnung'=>'Gefahrgut',
        'Link'=>'index.php?module=gefahrgut&action=list',
        'Icon'=>'Icons_dunkel_14.gif','install'=>true,
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Lager und Logistik|}','settings'=>true),
      'freearticle'=>array('Bezeichnung'=>'Kostenloser Artikel (Probe, Neukunde, Flyer, Geschenk)',
        'Link'=>'index.php?module=freearticle&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ENT',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|Verkauf|}'),
      'extendedapproval'=>array('Bezeichnung'=>'Erweiterte Freigabe',
        'Link'=>'index.php?module=extendedapproval&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Buchhaltung|}','settings'=>true),
      'eangenerator'=>array('Bezeichnung'=>'EAN Generator',
        'Link'=>'index.php?module=eangenerator&action=list',
        'Icon'=>'Icons_dunkel_15.gif', 'Versionen'=>'ENT','beta' => false,
        'kategorie'=>'{|Lager und Logistik|}','settings'=>true),
      'internalsupport'=>array('Bezeichnung'=>'Interner Support',
        'Link'=>'index.php?module=internalsupport&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|System|}'),
      'productionscanner'=>array('Bezeichnung'=>'Produktionsscanner',
        'Link'=>'index.php?module=productionscanner&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Produktion|}'),
      'xcs'=>array('Bezeichnung'=>'Xentral Customizing Suite (XCS)',
        'Link'=>'index.php?module=xcs&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Workflows|}'),
      'receiptprotocolitems'=>array('Bezeichnung'=>'Belegprotokolleintr&auml;ge',
        'Link'=>'index.php?module=receiptprotocolitems&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Workflows|}','settings'=>true),
      'itemtemplate'=>array('Bezeichnung'=>'Vorlagen',
        'Link'=>'index.php?module=itemtemplate&action=list',
        'Icon'=>'Icons_dunkel_15.gif',
        'Versionen'=>'ENT', 'beta' => false),
      'mandatoryfields'=>array('Bezeichnung'=>'Pflichtfelder',
        'Link'=>'index.php?module=mandatoryfields&action=list',
        'Icon'=>'Icons_dunkel_15.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Workflows|}','settings'=>true),
      'layouttemplateattachment'=>array('Bezeichnung'=>'Layoutvorlagen Anhang',
        'Link'=>'index.php?module=layouttemplateattachment&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ENT', 'beta' => false, 'kategorie'=>'{|Workflows|}','settings'=>true),
      'ups'=>array('Bezeichnung'=>'UPS Accounts',
        'Link'=>'index.php?module=ups&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Versandarten|}'),
      'gls'=>array('Bezeichnung'=>'GLS',
        'Link'=>'index.php?module=gls&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Versandarten|}','settings'=>true),
      'packagedeliverylist'=>array('Bezeichnung'=>'Paketbef&ouml;rderungsliste',
        'Link'=>'index.php?module=packagedeliverylist&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Lager und Logistik|}'),
      'bmd'=>array('Bezeichnung'=>'BMD Buchhaltungsexport',
        'Link'=>'index.php?module=bmd&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Buchhaltung|}','settings'=>true),
      'standardpackages'=>array('Bezeichnung'=>'Boxen',
        'Link'=>'index.php?module=standardpackages&action=list',
        'Icon'=>'Icons_dunkel_15.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Lager und Logistik|}'),
      'supplierdeliveryreport'=>array('Bezeichnung'=>'Lieferant Paket Auswertung ',
        'Link'=>'index.php?module=supplierdeliveryreport&action=list',
        'Icon'=>'Icons_dunkel_15.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Controlling und Statistik|}'),
      'maximumdiscount'=>array('Bezeichnung'=>'Maximalrabatt',
        'Link'=>'index.php?module=maximumdiscount&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Verkauf|}','settings'=>true),
      'filelink'=>array('Bezeichnung'=>'Verkn&uuml;pfungen',
        'Link'=>'index.php?module=filelink&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Workflows|}'),
      'inventur'=>array('Bezeichnung'=>'Inventur',
        'Link'=>'index.php?module=inventur&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Lager und Logistik|}',
        'project_sensitive' => true),
      'waage'=>array('Bezeichnung'=>'Waage',
        'Link'=>'index.php?module=waage&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Lager und Logistik|}'),
      'paymentslip_swiss'=>array('Bezeichnung'=>'Einzahlungsschein Schweiz (ESR)',
        'Link'=>'index.php?module=paymentslip_swiss&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Buchhaltung|}','settings'=>true),
      'rechnungslauf'=>array('Bezeichnung'=>'Abolauf / Abosystem',
        'Link'=>'index.php?module=rechnungslauf&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Verkauf|}','settings'=>true,'install' => true
      ),
      'meiapps'=>array('Bezeichnung'=>'MEiAPPS',
        'Link'=>'index.php?module=meiapps&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Mitarbeiter Verwaltung|}'),
      'stechuhrdevice'=>array('Bezeichnung'=>'Mitarbeiterzeiterfassung Ger&auml;teeinstellungen',
        'Link'=>'index.php?module=stechuhrdevice&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Mitarbeiter Verwaltung|}',
        'settings'=>true),
      'berichte'=>array('Bezeichnung'=>'Berichte',
        'Link'=>'index.php?module=berichte&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'','install'=>true, 'beta' => false,
        'legacy' => true,'kategorie'=>'{|Controlling und Statistik|}'),
      'preisanfrage'=>array('Bezeichnung'=>'Preisanfrage',
        'Link'=>'index.php?module=preisanfrage&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Einkauf|}',
        'project_sensitive' => true),
      'tagespreise'=>array('Bezeichnung'=>'Tagespreise',
        'Link'=>'index.php?module=tagespreise&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL', 'beta' => false,
        'special' => true,'kategorie'=>'{|Stammdaten|}'),
      'waage_artikel'=>array('Bezeichnung'=>'Waage Einstellungen',
        'Link'=>'index.php?module=waage_artikel&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Lager und Logistik|}'),
      'ueberzahlterechnungen'=>array('Bezeichnung'=>'&Uuml;berzahlte Rechnungen',
        'Link'=>'index.php?module=ueberzahlterechnungen&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT', 'beta' => false,
        'special' => true,'kategorie'=>'{|Buchhaltung|}'),
      'serienbrief'=>array('Bezeichnung'=>'Serienbriefe',
        'Link'=>'index.php?module=serienbrief&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT', 'beta' => false,
        'legacy' => true,'kategorie'=>'{|Marketing|}'),
      'pos_kassierer'=>array('Bezeichnung'=>'POS (Konfiguration)',
        'Link'=>'index.php?module=pos_kassierer&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Verkauf|}','settings'=>true),
      'pos'=>array('Bezeichnung'=>'POS',
        'Link'=>'index.php?module=pos&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Verkauf|}',
        'popular' => true,
      ),
      'reisekostenart'=>array('Bezeichnung'=>'Reisekostenart',
        'Link'=>'index.php?module=reisekostenart&action=list',
        'Icon'=>'Icons_dunkel_19.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Buchhaltung|}','settings'=>true),
      'reisekosten'=>array('Bezeichnung'=>'Reisekosten',
        'Link'=>'index.php?module=reisekosten&action=list',
        'Icon'=>'Icons_dunkel_19.gif',
        'Versionen'=>'ENT', 'install'=>true, 'beta' => false,'kategorie'=>'{|Mitarbeiter Verwaltung|}',
        'project_sensitive' => true),
      'aktionscode_liste'=>array('Bezeichnung'=>'Aktionscodes',
        'Link'=>'index.php?module=aktionscode_liste&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Controlling und Statistik|}','settings'=>true,
        'project_sensitive' => true),
      'aktionscodes'=>array('Bezeichnung'=>'Aktionscodes Auswertung',
        'Link'=>'index.php?module=aktionscodes&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Controlling und Statistik|}',),
      'artikeleigenschaften'=>array('Bezeichnung'=>'Artikel Eigenschaften',
        'Link'=>'index.php?module=artikeleigenschaften&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ENT','showonlywheninstalled'=>true, 'beta' => false,'kategorie'=>'{|Stammdaten|}'),
      'sammelrechnung'=>array('Bezeichnung'=>'Sammelrechnung',
        'Link'=>'index.php?module=sammelrechnung&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ENT', 'install'=>true, 'beta' => false,'kategorie'=>'{|Buchhaltung|}',
        'project_sensitive' => true),
      'schnellproduktion'=>array('Bezeichnung'=>'Schnellproduktion',
        'Link'=>'index.php?module=schnellproduktion&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL', 'beta' => false,
        'special' => true,'kategorie'=>'{|Produktion|}',
        'project_sensitive' => true),
      'einkaufabgleich'=>array('Bezeichnung'=>'EK-API (Farnell / Conrad)',
        'Link'=>'index.php?module=einkaufabgleich&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ALL', 'beta' => false,
        'legacy' => true,'kategorie'=>'{|Schnittstellen|}'),
      'waehrungumrechnung'=>array('Bezeichnung'=>'W&auml;hrung Umrechnung',
        'Link'=>'index.php?module=waehrungumrechnung&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Buchhaltung|}','settings'=>true),
      'propertytranslation'=>array('Bezeichnung'=>'Artikel Eigenschaften &Uuml;bersetzung',
        'Link'=>'index.php?module=propertytranslation&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Stammdaten|}'),
      'rahmenvertrag'=>array('Bezeichnung'=>'Rahmenvertrag (Lieferanten)',
        'Link'=>'index.php?module=rahmenvertrag&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Einkauf|}'),
      'frameagreement'=>array('Bezeichnung'=>'Rahmenvertrag (Kunden)',
        'Link'=>'index.php?module=frameagreement&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Verkauf|}'),
      'pdfarchiv'=>array('Bezeichnung'=>'PDF Archivierung',
        'Link'=>'index.php?module=pdfarchiv&action=list',
        'Icon'=>'Icons_dunkel_22.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Belege|}'),

      /*
          'kommissionierlauf'=>array('Bezeichnung'=>'Kommissionierung',
                            'Link'=>'index.php?module=kommissionierlauf&action=list',
                            'Icon'=>'Icons_dunkel_1.gif',
                            'Versionen'=>'ENT'),
      */
      'artikelbaum'=>array('Bezeichnung'=>'Artikelbaum',
        'Link'=>'index.php?module=artikelbaum&action=list',
        'Icon'=>'Icons_dunkel_15.gif',
        'Versionen'=>'','install'=>true, 'beta' => false,'kategorie'=>'{|Stammdaten|}','settings'=>true,
        'project_sensitive' => true),

      'verkaufkarte'=>array('Bezeichnung'=>'Karte: Verkaufte Artikel',
        'Link'=>'index.php?module=verkaufkarte&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ENT', 'beta' => false,
        'special' => true,'kategorie'=>'{|Verkauf|}'),

      'verkaufszahlen'=>array('Bezeichnung'=>'Verkaufszahlen',
        'Link'=>'index.php?module=verkaufszahlen&action=list',
        'Icon'=>'Icons_dunkel_17.gif',
        'Versionen'=>'', 'install'=>true, 'beta' => false,'kategorie'=>'{|Controlling und Statistik|}'),

      'firmendaten'=>[
        'Bezeichnung'=>'Grundeinstellungen',
        'Link'=>'index.php?module=firmendaten&action=edit',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true
      ],

      'benutzer'=>[
        'Bezeichnung'=>'Benutzer',
        'Link'=>'index.php?module=benutzer&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true
      ],

      'geschaeftsbrief_vorlagen'=>[
        'Bezeichnung'=>'Gesch&auml;ftsbrief Vorlagen',
        'Link'=>'index.php?module=geschaeftsbrief_vorlagen&action=list',
        'Icon'=>'Icons_dunkel_3.gif',
        'Versionen'=>'',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true,
        'project_sensitive' => true
      ],

      'emailbackup'=>[
        'Bezeichnung'=>'E-Mail Accounts',
        'Link'=>'index.php?module=emailbackup&action=list',
        'Icon'=>'Icons_dunkel_4.gif',
        'Versionen'=>'ENT',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true,
        'project_sensitive' => true
      ],

      'ticket_vorlage'=>[
        'Bezeichnung'=>'Ticket Vorlagen',
        'Link'=>'index.php?module=ticket_vorlage&action=list',
        'Icon'=>'Icons_dunkel_5.gif',
        'Versionen'=>'ENT',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true,
        'project_sensitive' => true
      ],

      'warteschlangen'=>[
        'Bezeichnung'=>'Warteschlangen',
        'Link'=>'index.php?module=warteschlangen&action=list',
        'Icon'=>'Icons_dunkel_5.gif',
        'Versionen'=>'ENT',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true
      ],

      'artikelkategorien'=>[
        'Bezeichnung'=>'Artikel Kategorien',
        'Link'=>'index.php?module=artikelkategorien&action=list',
        'Icon'=>'Icons_dunkel_15.gif',
        'Versionen'=>'ENT',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|Stammdaten|}',
        'settings'=>true
      ],

      'artikeleinheit'=>[
        'Bezeichnung'=>'Artikel Einheiten',
        'Link'=>'index.php?module=artikeleinheit&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|Stammdaten|}',
        'settings'=>true
      ],

      'gruppen'=>[
        'Bezeichnung'=>'Gruppen',
        'Link'=>'index.php?module=gruppen&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|Buchhaltung|}',
        'settings'=>true
      ],

      'uservorlage'=>[
        'Bezeichnung'=>'Benutzer Vorlage',
        'Link'=>'index.php?module=uservorlage&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ENT',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true
      ],

      'laender'=>[
        'Bezeichnung'=>'L&auml;nderliste',
        'Link'=>'index.php?module=laender&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true
      ],

      /*'kalendergruppen'=>[
        'Bezeichnung'=>'Kalender Gruppen',
        'Link'=>'index.php?module=kalender&action=gruppenlist',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'',
        //'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true
      ],*/

      'prozessstarter'=>[
        'Bezeichnung'=>'Prozessstarter',
        'Link'=>'index.php?module=prozessstarter&action=list',
        'Icon'=>'Icons_dunkel_22.gif',
        'Versionen'=>'',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true
      ],

      /*'welcomeinfo'=>[
        'Bezeichnung'=>'Lizenz',
        'Link'=>'index.php?module=welcome&action=info',
        'Icon'=>'Icons_dunkel_23.gif',
        'Versionen'=>'',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true
      ],*/

      'drucker'=>[
        'Bezeichnung'=>'Drucker',
        'Link'=>'index.php?module=drucker&action=list',
        'Icon'=>'Icons_dunkel_24.gif',
        'Versionen'=>'',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true
      ],

      'adapterbox'=>[
        'Bezeichnung'=>'Adapterbox',
        'Link'=>'index.php?module=adapterbox&action=list',
        'Icon'=>'Icons_dunkel_25.gif',
        'Versionen'=>'',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true
      ],

      'protokoll'=>[
        'Bezeichnung'=>'Protokoll',
        'Link'=>'index.php?module=protokoll&action=list',
        'Icon'=>'Icons_dunkel_22.gif',
        'Versionen'=>'',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true
      ],

      'etiketten'=>[
        'Bezeichnung'=>'Etiketten',
        'Link'=>'index.php?module=etiketten&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true
      ],

      'uebersetzung'=>[
        'Bezeichnung'=>'&Uuml;bersetzungen',
        'Link'=>'index.php?module=uebersetzung&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}'
      ],

      'api_account'=>[
        'Bezeichnung'=>'API-Account',
        'Link'=>'index.php?module=api_account&action=list',
        'Icon'=>'Icons_dunkel_25.gif',
        'Versionen'=>'',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true
      ],

      'umkreissuche'=>array('Bezeichnung'=>'Karte: Umkreissuche Kunden',
        'Link'=>'index.php?module=umkreissuche&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'', 'beta' => false,'kategorie'=>'{|Verkauf|}'),

      'wissensdatenbank'=>array('Bezeichnung'=>'Wissensdatenbank',
        'Link'=>'index.php?module=wissensdatenbank&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Team Funktionen|}'),

      'lagermobil'=>array('Bezeichnung'=>'Mobile Lagerverwaltung',
        'Link'=>'index.php?module=lagermobil&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Lager und Logistik|}'),

      'lagermobilold'=>array('Bezeichnung'=>'Lagermobil',
        'Link'=>'index.php?module=lagermobilold&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ENT', 'beta' => false,
        'legacy' => true,'kategorie'=>'{|Lager und Logistik|}'),

      'matrixprodukt'=>array('Bezeichnung'=>'Matrixprodukt',
        'Link'=>'index.php?module=matrixprodukt&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Stammdaten|}'),
      'massenbearbeitung'=>array('Bezeichnung'=>'Massenbearbeiten',
        'Link'=>'index.php?module=massenbearbeitung&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Stammdaten|}'),

      'massenartikel'=>array('Bezeichnung'=>'Massenartikel',
        'Link'=>'index.php?module=massenartikel&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Stammdaten|}'),


      'tagesbedarf'=>array('Bezeichnung'=>'Tagesbedarf',
        'Link'=>'index.php?module=tagesbedarf&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ALL', 'beta' => false,
        'special' => true,'kategorie'=>'{|Einkauf|}'),


      'artikel_texte'=>array('Bezeichnung'=>'Artikel Texte (Sprachen/Multishop)',
        'Link'=>'index.php?module=artikel_texte&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'install'=>true, 'beta' => false,'kategorie'=>'{|Stammdaten|}'),

      'verpackungen'=>array('Bezeichnung'=>'Verpackungen Lizenzen/Übersicht',
        'Link'=>'index.php?module=verpackungen&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Lager und Logistik|}'),
      'adressabhaengigesetikett'=>array('Bezeichnung'=>'Adressabh&auml;ngiges Etikett',
        'Link'=>'index.php?module=adressabhaengigesetikett&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Workflows|}','settings'=>true),

      'zmmeldung'=>array('Bezeichnung'=>'ZM Meldung',
        'Link'=>'index.php?module=zmmeldung&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Buchhaltung|}'),

      'serviceauftragscan'=>array('Bezeichnung'=>'Serviceauftrag Scan',
        'Link'=>'index.php?module=serviceauftragscan&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Verkauf|}'),

      'adresssucheerweitert' =>array('Bezeichnung'=>'Adresssuche Erweitert',
        'Link'=>'index.php?module=adresssucheerweitert&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Stammdaten|}'),

      'anfrage'=>array('Bezeichnung'=>'Anfrage',
        'Link'=>'index.php?module=anfrage&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Verkauf|}',
        'project_sensitive' => true),

      'arbeitsnachweis'=>array('Bezeichnung'=>'Arbeitsnachweis',
        'Link'=>'index.php?module=arbeitsnachweis&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Mitarbeiter Verwaltung|}',
        'project_sensitive' => true),

      'tagesabschluss'=>array('Bezeichnung'=>'Tagesabschluss',
        'Link'=>'index.php?module=tagesabschluss&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,
        'special' => true,'kategorie'=>'{|Belege|}'),
      /*
          'lagerchargen'=>array('Bezeichnung'=>'Lager Chargen',
                            'Link'=>'index.php?module=lagerchargen&action=list',
                            'Icon'=>'Icons_dunkel_14.gif',
                            'Versionen'=>'ALL'),
      */
      'mailausgang'=>array('Bezeichnung'=>'Mailausgang Log',
        'Link'=>'index.php?module=mailausgang&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'', 'beta' => false,'kategorie'=>'{|System|}'),

      'ftpbackup'=>array('Bezeichnung'=>'FTP-Backup',
        'Link'=>'index.php?module=ftpbackup&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'','install'=>true, 'beta' => false,'kategorie'=>'{|System|}','settings'=>true),

      'netstock'=>array('Bezeichnung'=>'Netstock CSV Export',
        'Link'=>'index.php?module=netstock&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Schnittstellen|}','settings'=>true),

      'exporttabelle'=>array('Bezeichnung'=>'Export der Tabellen ohne Bewegungsdaten',
        'Link'=>'index.php?module=exporttabelle&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT',
        'special'=>true),

      'placetel'=>array('Bezeichnung'=>'Placetel Anbindung',
        'Link'=>'index.php?module=placetel&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'', 'beta' => false,'kategorie'=>'{|Schnittstellen|}','settings'=>true),

      /*
          'vorlage'=>array('Bezeichnung'=>'Vorlage',
                            'Link'=>'index.php?module=vorlage&action=list',
                            'Icon'=>'Icons_dunkel_14.gif',
                            'Versionen'=>'ALL'),
      */
      'uebertragungen'=>array('Bezeichnung'=>'&Uuml;bertragungen (CSV/XML/EDI/PDF)',
        'Link'=>'index.php?module=uebertragungen&action=list',
        'Icon'=>'Icons_dunkel_25.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Schnittstellen|}'),

      'intrastat_meldung'=>array('Bezeichnung'=>'Intrastat Meldung',
        'Link'=>'index.php?module=intrastat_meldung&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Versandarten|}','settings'=>true),

      'auftragforecast'=>array('Bezeichnung'=>'Auftragforecast',
        'Link'=>'index.php?module=auftragforecast&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Controlling und Statistik|}'),
      'zeiterfassungvorlage'=>array('Bezeichnung'=>'Zeiterfassung Vorlage',
        'Link'=>'index.php?module=zeiterfassungvorlage&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Mitarbeiter Verwaltung|}','settings'=>true),
      'datei_stichwortvorlagen'=>array('Bezeichnung'=>'Datei Stichwort Vorlagen',
        'Link'=>'index.php?module=datei_stichwortvorlagen&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|System|}','settings'=>true),
      'ratenzahlung'=>array('Bezeichnung'=>'Ratenzahlung',
        'Link'=>'index.php?module=ratenzahlung&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Buchhaltung|}'),
      'doppelte_nummern'=>array('Bezeichnung'=>'Doppelte Nummern',
        'Link'=>'index.php?module=doppelte_nummern&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|System|}'),
      'auftragautoversand'=>array('Bezeichnung'=>'Tages-Auto-Versand',
        'Link'=>'index.php?module=auftragautoversand&action=list',
        'Icon'=>'Icons_dunkel_10.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Workflows|}',
        'project_sensitive'=>true),
      'kassescannen'=>array('Bezeichnung'=>'Kasse Scannen',
        'Link'=>'index.php?module=kassescannen&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Verkauf|}'),
      'belegeimport'=>array('Bezeichnung'=>'Belege Importer',
        'Link'=>'index.php?module=belegeimport&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Belege|}'),
      'gruppenrechnung'=>array('Bezeichnung'=>'Gruppenrechnung',
        'Link'=>'index.php?module=gruppenrechnung&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Buchhaltung|}',
        'project_sensitive' => true),
      'einfachelohnauszahlung'=>array('Bezeichnung'=>'Einfache Lohnauszahlung',
        'Link'=>'index.php?module=einfachelohnauszahlung&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Mitarbeiter Verwaltung|}'),
      'zeiterfassung_stundenuebersicht'=>array('Bezeichnung'=>'Zeiterfassung Stunden&uuml;bersicht',
        'Link'=>'index.php?module=zeiterfassung_stundenuebersicht&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Mitarbeiter Verwaltung|}'),
      'zeiterfassung_kosten'=>array('Bezeichnung'=>'Zeiterfassung Kosten',
        'Link'=>'index.php?module=zeiterfassung_kosten&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Mitarbeiter Verwaltung|}'),
      'auftragsampel'=>array('Bezeichnung'=>'Auftragsampel',
        'Link'=>'index.php?module=auftragsampel&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Workflows|}','settings'=>true),
      'prozess_monitor'=>array('Bezeichnung'=>'Prozess Monitor',
        'Link'=>'index.php?module=prozess_monitor&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Workflows|}','settings'=>true),
      'preisanpassung'=>array('Bezeichnung'=>'Preisanpassungen',
        'Link'=>'index.php?module=preisanpassung&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Stammdaten|}'),
      'steuersaetze'=>array('Bezeichnung'=>'Steuers&auml;tze',
        'Link'=>'index.php?module=steuersaetze&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'','install'=>true,
        'beta' => false,'kategorie'=>'{|Buchhaltung|}','settings'=>true),
      'eigenschaften_vorlagen'=>array('Bezeichnung'=>'Eigenschaften Vorlagen',
        'Link'=>'index.php?module=eigenschaften_vorlagen&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Stammdaten|}'),
      'rma_vorlagen'=>array('Bezeichnung'=>'Retouren Gr&uuml;nde',
        'Link'=>'index.php?module=rma_vorlagen&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Lager und Logistik|}',
        'project_sensitive' => true),
      'dateibrowser'=>array('Bezeichnung'=>'Dateibrowser',
        'Link'=>'index.php?module=dateibrowser&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|System|}'),
      'formeln'=>array('Bezeichnung'=>'Formeln',
        'Link'=>'index.php?module=formeln&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Belege|}'),
      'multilabelprint'=>array('Bezeichnung'=>'Multilabelprint',
        'Link'=>'index.php?module=multilabelprint&action=list',
        'Icon'=>'Icons_dunkel_15.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Workflows|}','settings'=>true,
        'project_sensitive' => true),
      'artikelarbeitsanweisung_vorlagen'=>array('Bezeichnung'=>'Artikel Arbeitsanweisung Vorlagen',
        'Link'=>'index.php?module=artikelarbeitsanweisung_vorlagen&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Produktion|}', 'settings'=>true),
      'transus'=>array('Bezeichnung'=>'EDI Transus',
        'Link'=>'index.php?module=transus&action=list',
        'Icon'=>'Icons_dunkel_25.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Schnittstellen|}','settings'=>true,
        'project_sensitive' => true),
      'artikelfunktionsprotokoll_vorlagen'=>array('Bezeichnung'=>'Artikel Funktionsprotokoll Vorlagen',
        'Link'=>'index.php?module=artikelfunktionsprotokoll_vorlagen&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Produktion|}','settings'=>true),
      'sprachen'=>array('Bezeichnung'=>'Sprachenliste',
        'Link'=>'index.php?module=sprachen&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'', 'beta' => false,'kategorie'=>'{|System|}','settings'=>true),
      'artikel_fremdnummern'=>array('Bezeichnung'=>'Artikel Fremdnummern',
        'Link'=>'index.php?module=artikel_fremdnummern&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Stammdaten|}'),
      'telefonrueckruf'=>array('Bezeichnung'=>'Telefonr&uuml;ckruf',
        'Link'=>'index.php?module=telefonrueckruf&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'','install'=>true, 'beta' => false,'kategorie'=>'{|Mitarbeiter Verwaltung|}')
    , 'ticket'=>array('Bezeichnung'=>'Tickets',
        'Link'=>'index.php?module=ticket&action=offene',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Team Funktionen|}')
    , 'zahlungseingang'=>array('Bezeichnung'=>'Zahlungseingang',
        'Link'=>'index.php?module=zahlungseingang&action=list',
        'Icon'=>'Icons_dunkel_17.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Buchhaltung|}')
    , 'verbindlichkeit'=>array('Bezeichnung'=>'Verbindlichkeiten',
        'Link'=>'index.php?module=verbindlichkeit&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Buchhaltung|}')
    , 'service'=>array('Bezeichnung'=>'Service &amp; Support',
        'Link'=>'index.php?module=service&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Team Funktionen|}')
    , 'bestellvorschlagapp'=>array('Bezeichnung'=>'Bestellvorschlag',
        'Link'=>'index.php?module=bestellvorschlagapp&action=list',
        'Icon'=>'Icons_dunkel_17.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Einkauf|}')
    , 'bestellvorschlagtag'=>array('Bezeichnung'=>'Bestellvorschlag Tag',
        'Link'=>'index.php?module=bestellvorschlagtag&action=list',
        'Icon'=>'Icons_dunkel_17.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,
        'legacy' => true,'kategorie'=>'{|Einkauf|}')
    , 'bestellvorschlagemail'=>array('Bezeichnung'=>'Bestellvorschlag E-Mail',
        'Link'=>'index.php?module=bestellvorschlagemail&action=list',
        'Icon'=>'Icons_dunkel_17.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Einkauf|}')
    , 'bestellvorschlagfiliale'=>array('Bezeichnung'=>'Bestellvorschlag Filiale',
        'Link'=>'index.php?module=bestellvorschlagfiliale&action=list',
        'Icon'=>'Icons_dunkel_17.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false, 'kategorie'=>'{|Einkauf|}')
    , 'belegeinauslagern'=>array('Bezeichnung'=>'Belege ein- auslagern',
        'Link'=>'index.php?module=belegeinauslagern&action=list',
        'Icon'=>'Icons_dunkel_17.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Lager und Logistik|}')
    , 'serviceauftrag'=>array('Bezeichnung'=>'Serviceauftrag',
        'Link'=>'index.php?module=serviceauftrag&action=list',
        'Icon'=>'Icons_dunkel_17.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Verkauf|}',
        'project_sensitive' => true)
    , 'gesamtrabatte'=>array('Bezeichnung'=>'Gesamtrabatte',
        'Link'=>'index.php?module=gesamtrabatte&action=list',
        'Icon'=>'Icons_dunkel_11.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Verkauf|}')
    , 'lieferkette'=>array('Bezeichnung'=>'Lieferkette',
        'Link'=>'index.php?module=lieferkette&action=list',
        'Icon'=>'Icons_dunkel_11.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,
        'legacy' => true,'kategorie'=>'{|Einkauf|}'),
      'spedition'=>array('Bezeichnung'=>'Spedition',
        'Link'=>'index.php?module=spedition&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Lager und Logistik|}'),
      'sevensenders' => [
        'Bezeichnung' => 'SevenSenders',
        'Link' => 'index.php?module=sevensenders&action=log',
        'install' => true,
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen' => 'ENT',
        'beta' => false,
        'kategorie'=>'{|Versandarten|}',
        'settings'=>true
      ],
      'produktion'=>array('Bezeichnung'=>'Produktion',
        'Link'=>'index.php?module=produktion&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Produktion|}',
        'project_sensitive' => true),
      'produktionszentrum'=>array('Bezeichnung'=>'Produktionszentrum',
        'Link'=>'index.php?module=produktionszentrum&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Produktion|}',
        'project_sensitive' => true),
      'mitarbeiterzeiterfassung'=>array('Bezeichnung'=>'Mitarbeiterzeiterfassung',
        'Link'=>'index.php?module=mitarbeiterzeiterfassung&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Mitarbeiter Verwaltung|}'),
      'auftragzubestellung'=>array('Bezeichnung'=>'Auftrag/Preisanfrage zu Bestellung',
        'Link'=>'index.php?module=auftragzubestellung&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Einkauf|}',
        'project_sensitive' => true),

      'schneller_wareneingang'=>array('Bezeichnung'=>'Schneller Wareneingang',
        'Link'=>'index.php?module=schneller_wareneingang&action=einstellungen',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Lager und Logistik|}'),

      'proformarechnung'=>array('Bezeichnung'=>'Proformarechnung',
        'Link'=>'index.php?module=proformarechnung&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Buchhaltung|}',
        'project_sensitive' => true),

      'fahrtenbuch'=>array('Bezeichnung'=>'Fahrtenbuch',
        'Link'=>'index.php?module=fahrtenbuch&action=list',
        'Icon'=>'Icons_dunkel_11.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Mitarbeiter Verwaltung|}'),
      'scheck'=>array('Bezeichnung'=>'Scheck',
        'Link'=>'index.php?module=scheck&action=list',
        'Icon'=>'Icons_dunkel_11.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Buchhaltung|}'),
      'mhdumlagern'=>array('Bezeichnung'=>'MHD Umlagern mit neuem Datum',
        'Link'=>'index.php?module=mhdumlagern&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Lager und Logistik|}'),
      'kommissionskonsignationslager'=>array('Bezeichnung'=>'Kommissions-/Konsignationslager',
        'Link'=>'index.php?module=kommissionskonsignationslager&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Lager und Logistik|}'),
      'liefertermine'=>array('Bezeichnung'=>'Liefertermine',
        'Link'=>'index.php?module=liefertermine&action=list',
        'Icon'=>'Icons_dunkel_22.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Verkauf|}')
    , 'bestellung_einlagern'=>array('Bezeichnung'=>'Bestellung Einlagern',
        'Link'=>'index.php?module=bestellung_einlagern&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Einkauf|}')
    , 'amazon'=>array('Bezeichnung'=>'Amazon Seller App',
        'Link'=>'index.php?module=amazon&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}')
    , 'arbeitsplatzgruppen'=>array('Bezeichnung'=>'Arbeitsplatzgruppen',
        'Link'=>'index.php?module=arbeitsplatzgruppen&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Produktion|}','settings'=>true)
    , 'artikelkalkulation'=>array('Bezeichnung'=>'Artikel Kalkulation',
        'Link'=>'index.php?module=artikelkalkulation&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Stammdaten|}','settings'=>true)


    ,'belegevorlagen'=>array('Bezeichnung'=>'Belege Vorlagen',
        'Link'=>'index.php?module=belegevorlagen&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Verkauf|}',
        'project_sensitive' => true)

    ,'preisliste'=>array('Bezeichnung'=>'Preisliste',
        'Link'=>'index.php?module=preisliste&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Verkauf|}')


    ,'positionenaktualisieren'=>array('Bezeichnung'=>'Positionen Aktualisieren',
        'Link'=>'index.php?module=positionenaktualisieren&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Verkauf|}')
    ,'wawision_uebersetzung'=>array('Bezeichnung'=>'Oberfl&auml;chen &Uuml;bersetzung',
        'Link'=>'index.php?module=wawision_uebersetzung&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|System|}')
    ,'bestellungzuproduktion'=>array('Bezeichnung'=>'Bestellung zu Produktion',
        'Link'=>'index.php?module=bestellungzuproduktion&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Produktion|}')

    ,'TransferAmazonMfn'=>array('Bezeichnung'=>'Transfer Plug-In Amazon MFN',
        'Link'=>'index.php?module=uebertragungen&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Schnittstellen|}')
    ,'TransferAmazonVendor'=>array('Bezeichnung'=>'Transfer Plug-In Amazon Vendor',
        'Link'=>'index.php?module=uebertragungen&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>true,'beta' => false,'kategorie'=>'{|Schnittstellen|}')
    ,'TransferApp4sales'=>array('Bezeichnung'=>'Transfer Plug-In App4Sales',
        'Link'=>'index.php?module=uebertragungen&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>false,'kategorie'=>'{|Schnittstellen|}')
    ,'TransferDS'=>array('Bezeichnung'=>'Transfer Plug-In DS',
        'Link'=>'index.php?module=uebertragungen&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>false,
        'special' => true,'kategorie'=>'{|Schnittstellen|}')
    ,'TransferNetstock'=>array('Bezeichnung'=>'Transfer Plug-In Netstock',
        'Link'=>'index.php?module=uebertragungen&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true,'kategorie'=>'{|Schnittstellen|}')
    ,'TransferOpentrans'=>array('Bezeichnung'=>'Transfer Plug-In Opentrans',
        'Link'=>'index.php?module=uebertragungen&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>false,'kategorie'=>'{|Schnittstellen|}')
    ,'TransferPixi'=>array('Bezeichnung'=>'Transfer Plug-In Pixi',
        'Link'=>'index.php?module=uebertragungen&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>true,'kategorie'=>'{|Schnittstellen|}')
    ,'TransferSmarty'=>array('Bezeichnung'=>'Transfer Smarty',
        'Link'=>'index.php?module=uebertragungen&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true,'kategorie'=>'{|Schnittstellen|}')
    ,'TransferBrickfox'=>array('Bezeichnung'=>'Transfer Plug-In Brickfox',
        'Link'=>'index.php?module=uebertragungen&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>true,'kategorie'=>'{|Schnittstellen|}')
    ,'onlineshops'=>array(
        'Bezeichnung'=>'Online-Shops',
        'Link'=>'index.php?module=onlineshops&action=list',
        'Icon'=>'Icons_dunkel_20.gif',
        'Versionen' => '',
        'install'=>true,
        'beta' => false,
        'kategorie'=>'{|Shop Schnittstelle|}',
        'settings'=>true)
    ,'shopimporter_hhg'=>array('Bezeichnung'=>'H.H.G. Multistore',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_hhg',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true,
        'special' => true,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_modified'=>array('Bezeichnung'=>'Modified',
        'Link'=>'index.php?module=onlineshops&action=create',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,
        'legacy' => true, 'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_amazon'=>array('Bezeichnung'=>'Amazon',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_amazon',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}',
        'popular' => true,
      )
    ,'shopimporter_magento'=>array('Bezeichnung'=>'Magento 1.9',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_magento',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_magento2'=>array('Bezeichnung'=>'Magento 2',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_magento2',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_shopware'=>array('Bezeichnung'=>'Shopware 5',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_shopware',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_shopware6'=>array('Bezeichnung'=>'Shopware 6 API Advanced',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_shopware6',
        'Bezeichnung2' => 'Shopware 6 Business Booster App',
        'Link2'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_shopware6&sid=booster',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'','install'=>true,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_getcore'=>array('Bezeichnung'=>'Getcore',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_getcore',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,
        'special' => true,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_woocommerce'=>array('Bezeichnung'=>'Woocommerce',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_woocommerce',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_spryker'=>array('Bezeichnung'=>'Spryker',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_spryker',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,
        'special' => true, 'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_oxid'=>array('Bezeichnung'=>'Oxid',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_oxid',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_gambio'=>array('Bezeichnung'=>'Gambio',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_gambio',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_xtcommerce'=>array('Bezeichnung'=>'XTCommerce',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_xtcommerce',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,
        'legacy'=>true,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_channeladvisor'=>array('Bezeichnung'=>'Channeladvisor',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_channeladvisor',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>true,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_epages'=>array('Bezeichnung'=>'ePages',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_epages',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'retailpricetemplate'=>array('Bezeichnung'=>'Artikelpreisvorlage',
        'Link'=>'index.php?module=retailpricetemplate&action=template',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Verkauf|}')
    ,'shopimporter_billbee'=>array('Bezeichnung'=>'Billbee',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_billbee',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_hood'=>array('Bezeichnung'=>'Hood',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_hood',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_manomano'=>array('Bezeichnung'=>'Manomano',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_manomano',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_ebay'=>array('Bezeichnung'=>'Ebay',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_ebay',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_etsy'=>array('Bezeichnung'=>'Etsy',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_etsy',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'kategorie'=>'{|Shop Schnittstelle|}')
    ,'ebay'=>array('Bezeichnung'=>'Ebay App',
        'Link'=>'index.php?module=ebay&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_presta'=>array('Bezeichnung'=>'Presta',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_presta',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_shopify'=>array(
        'Bezeichnung'=>'Shopify API Advanced',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_shopify',
        'Bezeichnung2'=>'Shopify Business Booster App',
        'Link2'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_shopify&sid=booster',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}',
        'popular' => true,
      )
    ,'shopimporter_real'=>array('Bezeichnung'=>'Real',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_real',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_tillhub'=>array('Bezeichnung'=>'Tillhub',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_tillhub',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true,'showonlyoninstall'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'shopimporter_rakuten'=>array('Bezeichnung'=>'Rakuten',
        'Link'=>'index.php?module=onlineshops&action=create&cmd=shopimporter_rakuten',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Shop Schnittstelle|}','legacy'=>true)
    ,'alkoholsteuerrechner'=>array('Bezeichnung'=>'Alkoholsteuerrechner',
        'Link'=>'index.php?module=alkoholsteuerrechner&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Controlling und Statistik|}')
    ,'documenttoproject'=>array('Bezeichnung'=>'Belege zu Projekt',
        'Link'=>'index.php?module=documenttoproject&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'showonlywheninstalled' => true, 'kategorie'=>'{|Workflows|}')
    ,'crossselling'=>array('Bezeichnung'=>'Crossselling',
        'Link'=>'index.php?module=crossselling&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Stammdaten|}')
    , 'dekodinexus' => [
        'Bezeichnung' => 'Nexus',
        'Link' => 'index.php?module=dekodinexus&action=list',
        'Icon' => 'Icons_dunkel_1.gif',
        'Versionen' => 'ALL',
        'install' => true,
        'beta' => false,
        'legacy' => true,
        'kategorie' => '{|Schnittstellen|}'
      ]
    ,'cronjob_autoversand_plus'=>array('Bezeichnung'=>'Autoversand Plus',
        'Link'=>'index.php?module=cronjob_autoversand_plus&action=list',
        'Icon'=>'Icons_dunkel_10.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Lager und Logistik|}')
    ,'unterproduktionen'=>array('Bezeichnung'=>'Unterproduktionen',
        'Link'=>'index.php?module=unterproduktionen&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Produktion|}')
    ,'dokumentation'=>array('Bezeichnung'=>'Dokumentation',
        'Link'=>'index.php?module=dokumentation&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Stammdaten|}')
    ,'bundesstaaten'=>array('Bezeichnung'=>'Bundesstaaten',
        'Link'=>'index.php?module=bundesstaaten&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'','install'=>true, 'beta' => false,'kategorie'=>'{|System|}','settings'=>true)
    ,'datenbankbereinigen'=>array('Bezeichnung'=>'Gel&ouml;schte Datens&auml;tze wiederherstellen',
        'Link'=>'index.php?module=datenbankbereinigen&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'','install'=>true, 'beta' => false,'kategorie'=>'{|Stammdaten|}')
    ,'artikelforecast'=>array('Bezeichnung'=>'Artikelforecast',
        'Link'=>'index.php?module=artikelforecast&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Controlling und Statistik|}')
    ,'artikel_verfuegbarkeit'=>array('Bezeichnung'=>'Artikel Verfügbarkeit',
        'Link'=>'index.php?module=artikel_verfuegbarkeit&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Einkauf|}')
    ,'multiorderpicking'=>array('Bezeichnung'=>'Multi-Order Picking',
        'Link'=>'index.php?module=multiorderpicking&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Lager und Logistik|}')
    ,'managementboard_liquiditaet'=>array('Bezeichnung'=>'Managementboard Liquidität',
        'Link'=>'index.php?module=managementboard_liquiditaet&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Controlling und Statistik|}')
    ,'elo'=>array('Bezeichnung'=>'ELO DMS Archiv',
        'Link'=>'index.php?module=elo&action=einstellungen',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,
        'legacy'=>true,'kategorie'=>'{|Schnittstellen|}')
    ,'dropshipping'=>array('Bezeichnung'=>'Dropshipping Lieferant',
        'Link'=>'index.php?module=dropshipping&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Lager und Logistik|}','settings'=>true)
    ,'dropshippinglager'=>array('Bezeichnung'=>'Dropshipping Lager',
        'Link'=>'index.php?module=dropshippinglager&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Lager und Logistik|}','settings'=>true)
    ,'rabatte'=>array('Bezeichnung'=>'Erweiterte Rabatte',
        'Link'=>'index.php?module=rabatte&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Verkauf|}','settings'=>true)
    ,'angebot_stueckliste'=>array('Bezeichnung'=>'Angebot Stückliste einfügen',
        'Link'=>'index.php?module=angebot_stueckliste&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false ,'kategorie'=>'{|Verkauf|}')
    ,'cryptoweb'=>array('Bezeichnung'=>'CryptoWeb',
        'Link'=>'index.php?module=cryptoweb&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,
        'special' => true,'kategorie'=>'{|Schnittstellen|}')
    ,'lagerstueckliste'=>array('Bezeichnung'=>'Lagerst&uuml;ckliste',
        'Link'=>'index.php?module=lagerstueckliste&action=list',
        'Icon'=>'Icons_dunkel_15.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Lager und Logistik|}')
    ,'zahlungsweise_tagxmonat'=>array('Bezeichnung'=>'Tag X Monat',
        'Link'=>'index.php?module=zahlungsweisen&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Zahlungsweisen|}','settings'=>true)
    ,'zahlungsweise_payone'=>array('Bezeichnung'=>'BS Payone',
        'Link'=>'index.php?module=zahlungsweisen&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Zahlungsweisen|}', 'beta' => false,'settings'=>true)
    ,'zahlungsweise_billpay'=>array('Bezeichnung'=>'Billpay',
        'Link'=>'index.php?module=zahlungsweisen&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ENT', 'beta' => false,
        'legacy' => true,'kategorie'=>'{|Zahlungsweisen|}','settings'=>true)
    ,'zahlungsweise_klarna'=>array('Bezeichnung'=>'Klarna',
        'Link'=>'index.php?module=zahlungsweisen&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ENT',
        'beta' => false,'kategorie'=>'{|Zahlungsweisen|}','settings'=>true)
    ,'zahlungsweise_secupay'=>array('Bezeichnung'=>'Secupay',
        'Link'=>'index.php?module=zahlungsweisen&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Zahlungsweisen|}', 'beta' => false,'settings'=>true)
    ,'zahlungsweise_paypalplus'=>array('Bezeichnung'=>'Paypalplus',
        'Link'=>'index.php?module=zahlungsweisen&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Zahlungsweisen|}','settings'=>true,
        'popular' => true,
      )
    ,'zahlungsweise_rechnung'=>array('Bezeichnung'=>'Rechnung',
        'Link'=>'index.php?module=zahlungsweisen&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'', 'beta' => false,'kategorie'=>'{|Zahlungsweisen|}','settings'=>true)
    ,'versandarten_logoix'=>array('Bezeichnung'=>'LogoiX',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Versandarten|}','settings'=>true)
    ,'versandarten_addresslabel'=>array('Bezeichnung'=>'Adress Aufkleber',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Versandarten|}', 'beta' => false,'settings'=>true)

    ,'versandarten_dhlexpress'=>array('Bezeichnung'=>'DHL Express',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Versandarten|}', 'beta' => false,'settings'=>true)
    ,'versandarten_dpd_at'=>array('Bezeichnung'=>'DPD AT',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Versandarten|}','settings'=>true)
    ,'versandarten_dhlversenden'=>array('Bezeichnung'=>'DHL Versenden',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'','kategorie'=>'{|Versandarten|}', 'beta' => false,'settings'=>true)


    ,'versandarten_dpdapi'=>array('Bezeichnung'=>'DPD (API)',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Versandarten|}', 'beta' => false,'settings'=>true)

    ,'versandarten_ups'=>array('Bezeichnung'=>'UPS',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Versandarten|}', 'beta' => false,'settings'=>true)

    ,'versandarten_glsapi'=>array('Bezeichnung'=>'GLS (API)',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Versandarten|}', 'beta' => false,'settings'=>true)

    ,'versandarten_fedexapi'=>array('Bezeichnung'=>'Fedex',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Versandarten|}', 'beta' => false,'settings'=>true)

    ,'versandarten_postat'=>array('Bezeichnung'=>'Post.AT',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Versandarten|}', 'beta' => false,'settings'=>true)

    ,'versandarten_dpd'=>array('Bezeichnung'=>'DPD (CSV)',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Versandarten|}', 'beta' => false,'settings'=>true)
    ,'versandarten_dpdesolutions'=>array('Bezeichnung'=>'DPD eSolutions',
            'Link'=>'index.php?module=versandarten&action=list',
            'Icon'=>'Icons_dunkel_9.gif',
            'Versionen'=>'ENT','kategorie'=>'{|Versandarten|}', 'beta' => false,
            'special'=>true,'settings'=>true)

    ,'versandarten_postch'=>array('Bezeichnung'=>'Post.CH',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Versandarten|}', 'beta' => false,'settings'=>true)

    ,'versandarten_gls'=>array('Bezeichnung'=>'GLS (CSV)',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Versandarten|}', 'beta' => false,'settings'=>true)

    ,'versandarten_amazonprime'=>array('Bezeichnung'=>'Amazon Prime',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ALL',
        'beta' => false,
        'kategorie'=>'{|Versandarten|}',
        'settings'=>true)

    ,'versandarten_sendcloud'=>array('Bezeichnung'=>'Sendcloud',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Versandarten|}', 'beta' => false,'settings'=>true)

    ,'versandarten_shipcloud'=>array('Bezeichnung'=>'Shipcloud',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','kategorie'=>'{|Versandarten|}', 'beta' => false,'settings'=>true)


    ,'verpackungsmaterial'=>array('Bezeichnung'=>'Verpackungsmaterial Lagermanagement',
        'Link'=>'index.php?module=verpackungsmaterial&action=list',
        'Icon'=>'Icons_dunkel_10.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Lager und Logistik|}')
    ,'abschlagsrechnung'=>array('Bezeichnung'=>'Abschlagsrechnung',
        'Link'=>'index.php?module=abschlagsrechnung&action=list',
        'Icon'=>'Icons_dunkel_9.gif','install'=>true,
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Buchhaltung|}')
    ,'arbeitsfreietage'=>array('Bezeichnung'=>'Arbeitsfreie Tage / Feiertage',
        'Link'=>'index.php?module=arbeitsfreietage&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Mitarbeiter Verwaltung|}',
        'settings'=>true)
    ,'produktionkorrektur'=>array('Bezeichnung'=>'Produktionkorrektur',
        'Link'=>'index.php?module=produktionkorrektur&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Produktion|}')
    ,'provisionenartikelvertreter'=>array('Bezeichnung'=>'Provisionen',
        'Link'=>'index.php?module=provisionenartikelvertreter&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Buchhaltung|}','settings'=>true)
    ,'auftragoffenepositionen'=>array('Bezeichnung'=>'Auftrag offene Positionen',
        'Link'=>'index.php?module=auftragoffenepositionen&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Workflows|}',
        'project_sensitive' => true)
    ,'auftragoffenepositionendienstleistung'=>array('Bezeichnung'=>'Auftrag offene Dienstleistungen',
        'Link'=>'index.php?module=auftragoffenepositionendienstleistung&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Verkauf|}',
        'project_sensitive' => true)
    ,'rechnungzuverbindlichkeit'=>array('Bezeichnung'=>'Rechnung zu Verbindlichkeit',
        'Link'=>'index.php?module=rechnungzuverbindlichkeit&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT', 'beta' => false,
        'special' => true,'kategorie'=>'{|Workflows|}')
    ,'dsgvo'=>array('Bezeichnung'=>'DSGVO',
        'Link'=>'index.php?module=dsgvo&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'', 'beta' => false,'kategorie'=>'{|Sicherheit und Datenschutz|}')
    ,'dataprotection'=> [
        'Bezeichnung' => 'Datenschutz',
        'Link' => 'index.php?module=dataprotection&action=list',
        'Icon' => 'Icons_dunkel_2.gif',
        'Versionen' => '',
        'beta' => false,
        'kategorie' => '{|Sicherheit und Datenschutz|}',
    ]
    ,'stuecklistendetails'=>array('Bezeichnung'=>'St&uuml;cklistendetails',
        'Link'=>'index.php?module=stuecklistendetails&action=list',
        'Icon'=>'Icons_dunkel_15.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Stammdaten|}')
    ,'kopiebelegempfaenger'=>array('Bezeichnung'=>'Kopie Belege Empf&auml;nger',
        'Link'=>'index.php?module=kopiebelegempfaenger&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ENT','install'=>true, 'beta' => false,'kategorie'=>'{|Verkauf|}','settings'=>true)
    ,'verleih'=>array('Bezeichnung'=>'Verleih',
        'Link'=>'index.php?module=verleih&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,
        'special' => true,'kategorie'=>'{|Verkauf|}')
    ,'belegpositionberechnung'=>array('Bezeichnung'=>'Belege Positionen Berechnung',
        'Link'=>'index.php?module=belegpositionberechnung&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'Versionen'=>'ALL','install'=>true, 'beta' => false,'kategorie'=>'{|Belege|}')
    ,'iaaplus'=>array('Bezeichnung'=>'IaaPlus',
        'Link'=>'index.php?module=iaaplus&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Lager und Logistik|}')
    ,'singleshipment'=>array('Bezeichnung'=>'Einzelversand&uuml;bergabe',
        'Link'=>'index.php?module=singleshipment&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Workflows|}')
    ,'trackingscan'=>array('Bezeichnung'=>'Trackingnummer Scan',
        'Link'=>'index.php?module=trackingscan&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Lager und Logistik|}')
    ,'retoure'=>array('Bezeichnung'=>'Retouren Belege',
        'Link'=>'index.php?module=retoure&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT','install'=>true,'kategorie'=>'{|Lager und Logistik|}',
        'project_sensitive' => true)
    ,'fulfillment'=>array('Bezeichnung'=>'Fulfillment',
        'Link'=>'index.php?module=fulfillment&action=list',
        'Icon'=>'Icons_dunkel_9.gif','install'=>true,
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Schnittstellen|}')
    ,'realsms' => array('Bezeichnung'=>'RealSMS',
        'Icon'=>'Icons_dunkel_9.gif',
        'Link'=>'index.php?module=realsms&action=list',
        'install'=>true,
        'Versionen'=>'ALL', 'beta' => false,
        'special' => true,'kategorie'=>'{|Shop Schnittstelle|}')
    ,'receiptdocument'=>array('Bezeichnung'=>'Wareneingangsbelege',
        'Link'=>'index.php?module=receiptdocument&action=list',
        'Icon'=>'Icons_dunkel_9.gif','install'=>true,'showonlyoninstall'=>true,
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Lager und Logistik|}',
        'project_sensitive' => true)
    ,'orderstatus'=>array('Bezeichnung'=>'Auftrag Status',
        'Link'=>'index.php?module=orderstatus&action=list',
        'Icon'=>'Icons_dunkel_1.gif','install'=>true,
        'Versionen'=>'ENT', 'beta' => false,
        'special' => true,'kategorie'=>'{|Verkauf|}')
    ,'lastprices'=>array('Bezeichnung'=>'Letzte Preise',
        'Link'=>'index.php?module=lastprices&action=list',
        'Icon'=>'Icons_dunkel_16.gif','install'=>true,
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Verkauf|}')
    ,'bestbeforebatchtoposition'=>array('Bezeichnung'=>'Wunsch MHD/Charge',
        'Link'=>'index.php?module=bestbeforebatchtoposition&action=list',
        'Icon'=>'Icons_dunkel_16.gif','install'=>true,
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Lager und Logistik|}')
    ,'voucher' =>array('Bezeichnung' =>'Gutscheine',
        'Link'        =>'index.php?module=voucher&action=list',
        'Icon'        =>'Icons_dunkel_16.gif', 'install' =>true,
        'Versionen'   =>'ALL','kategorie'=>'{|Verkauf|}')
    ,'slacknotification'=>array('Bezeichnung'=>'Slack-Benachrichtigung',
        'Link'=>'index.php?module=slacknotification&action=list',
        'Icon'=>'Icons_dunkel_16.gif','install'=>true,
        'Versionen'=>'ALL', 'beta' => false,'kategorie'=>'{|Schnittstellen|}','settings'=>true)
    ,'konto_fidor'=>array('Bezeichnung'=>'Konto: Fidor (API)',
        'Link'=>'index.php?module=konten&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ENT',
        'special' => false,
        'legacy' => true,
        'kategorie'=>'{|Zahlungsweisen|}',
        'settings'=>true)
    ,'konto_hbci4phpapi' => [
        'Bezeichnung'=>'Konto: HBCI/FinTS (HBCI4PHP)',
        'Link'=>'index.php?module=konten&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ENT',
        'kategorie'=>'{|Zahlungsweisen|}',
        'settings'=>true,
        'beta' => false
      ]
    ,'konto_paypal' => [
        'Bezeichnung'=>'Konto: Paypal',
        'Link'=>'index.php?module=konten&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ENT',
        'kategorie'=>'{|Zahlungsweisen|}',
        'settings'=>true,
        'beta' => false
      ]
    ,'konto_amazonpay'=>array('Bezeichnung'=>'Konto: AmazonPay (API)',
        'Link'=>'index.php?module=konten&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ENT',
        'special' => false,
        'beta' => false,
        'kategorie'=>'{|Zahlungsweisen|}',
        'settings'=>true)
    ,'konto_amazonfbm'=>array('Bezeichnung'=>'Konto: AmazonFBM',
        'Link'=>'index.php?module=konten&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ENT',
        'special' => false,
        'beta' => false,
        'kategorie'=>'{|Zahlungsweisen|}',
        'settings'=>true)
    ,'konto_stripe'=>array('Bezeichnung'=>'Konto: Stripe (API)',
        'Link'=>'index.php?module=konten&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ENT',
        'special' => false,
        'beta' => false,
        'kategorie'=>'{|Zahlungsweisen|}',
        'settings'=>true)
    ,'konto_ebaypayment'=>[
        'Bezeichnung'=>'Konto: EbayPayment (API)',
        'Link'=>'index.php?module=konten&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ENT',
        'special' => false,
        'beta' => true,
        'showonlyoninstall' => true,
        'kategorie'=>'{|Zahlungsweisen|}',
        'settings'=>true
      ]
    ,'sage'=>array('Bezeichnung'=>'Sage Buchhaltungsexport',
        'Link'=>'index.php?module=sage&action=list',
        'Icon'=>'Icons_dunkel_16.gif','showonlyoninstall'=>true,
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Buchhaltung|}','settings'=>true)
    ,'shippingtaxsplit'=>array('Bezeichnung'=>'Versand Aufsplitten',
        'Link'=>'index.php?module=shippingtaxsplit&action=list',
        'Icon'=>'Icons_dunkel_16.gif','install'=>true,
        'Versionen'=>'ENT', 'beta' => false,'kategorie'=>'{|Workflows|}'
      )
    ,'snapaddy'=>array('Bezeichnung'=>'snapADDY',
        'Link'=>'index.php?module=snapaddy&action=list',
        'Icon'=>'Icons_dunkel_16.gif', 'install'=>true,
        'Versionen'=>'ENT',
        'kategorie'=>'{|Schnittstellen|}'
      )
    ,'sipgate'=>array('Bezeichnung'=>'Sipgate',
        'Link'=>'index.php?module=sipgate&action=list',
        'Icon'=>'Icons_dunkel_16.gif', 'install'=>false,
        'Versionen'=>'', 'beta' => false,'kategorie'=>'{|Schnittstellen|}'
      )
    ,'wizard'=>array('Bezeichnung'=>'Wizard',
        'Link'=>'index.php?module=wizard&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'install'=>true,
        'Versionen'=>'',
        'beta' => false,
        'special' => true
      )
    ,'merge_order'=>array('Bezeichnung'=>'Auftr&auml;ge zusammenf&uuml;hren',
        'Link'=>'index.php?module=merge_order&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'install'=>true,
        'Versionen'=>'ALL','kategorie'=>'{|Verkauf|}'
      )
    ,'replenishmentmobile'=>array('Bezeichnung'=>'Mobile Nachschublagerbuchung',
        'Link'=>'index.php?module=replenishmentmobile&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'install'=>true,
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}'
      )
    ,'zolltarifnummer'=>array('Bezeichnung'=>'Zolltarifnummer',
        'Link'=>'index.php?module=zolltarifnummer&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'install'=>true,
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}',
        'settings'=>true
      )
    ,'versandarten'=>array('Bezeichnung'=>'Versandarten',
        'Link'=>'index.php?module=versandarten&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'install'=>true,
        'Versionen' => '',
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}',
        'settings'=>true
      )
    ,'lieferbedingungen'=>array('Bezeichnung'=>'Lieferbedingungen',
        'Link'=>'index.php?module=lieferbedingungen&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'install'=>true,
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}',
        'settings'=>true
      )
    ,'pseudostorage'=>array('Bezeichnung'=>'Pseudolagerzahlen Formeln',
        'Link'=>'index.php?module=pseudostorage&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'install'=>true,
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}'
      )
    ,'goodspostingdocument'=>array('Bezeichnung'=>'Warenbuchungsbeleg',
          'Link'=>'index.php?module=goodspostingdocument&action=list',
          'Icon'=>'Icons_dunkel_16.gif',
          'install'=>true,
          'Versionen'=>'ENT',
          'beta' => false,
          'kategorie'=>'{|Lager und Logistik|}'
      )
    ,'goodspostingdocumentscan'=>array('Bezeichnung'=>'Warenbuchungsbeleg Scannen',
        'Link'=>'index.php?module=goodspostingdocumentscan&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'install'=>true,
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}'
      ),


      'docuvitasolutions' => [
          'Bezeichnung' => 'docuvita DMS',
          'Link' => 'index.php?module=docuvitasolutions',
          'Icon' => 'Icons_dunkel_16.gif',
          'install' => true,
          'Versionen' => 'ALL',
          'beta' => true,
          'special' => true,
          'kategorie'=>'{|Sicherheit und Datenschutz|}'
      ]

    ,'datatablelabels'=>array('Bezeichnung'=>'Labels',
        'Link'=>'index.php?module=datatablelabels&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'install'=>true,
        'Versionen'=>'',
        'beta' => false,
        'kategorie'=>'{|Workflows|}',
        'settings'=>true
      )
    ,'loqate' => array('Bezeichnung'=>'Adresspr&uuml;fung (Loqate)',
        'Link'=>'index.php?module=loqate&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'install'=>true,
        'Versionen'=>'ALL',
        'kategorie'=>'{|Workflows|}'
      ),
      'caldav' => [
        'Bezeichnung' => 'CalDAV Schnittstelle',
        'Link'=>'index.php?module=caldav&action=config',
        'Icon'=>'Icons_dunkel_16.gif',
        'install' => true,
        'Versionen'=>'ENT',
        'beta' => false,
        'legacy' => true,
        'special' => true,
        'kategorie'=>'{|Schnittstellen|}'
      ]
    ,'differentialtaxation' => [
        'Bezeichnung' => 'Differenzenbesteuerung',
        'Link'=>'index.php?module=differentialtaxation&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'install'=>true,
        'Versionen'=>'ALL',
        'kategorie'=>'{|Buchhaltung|}',
        'settings'=>true
      ]
    ,'batches' => [
        'Bezeichnung' => 'Batches',
        'Link'=>'index.php?module=batches&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'install'=>true,
        'Versionen'=>'ALL',
        'kategorie'=>'{|Lager und Logistik|}',
        'settings'=>true
      ],
      'datev' => [
        'Bezeichnung' => 'DATEVconnect online',
        'Link' => 'index.php?module=datev&action=export',
        'Icon' => 'Icons_dunkel_16.gif',
        'install' => true,
        'Versionen' => 'ENT',
        'beta' => true,
        'kategorie'=>'{|Buchhaltung|}',
        'settings'=>true
      ]
    ,'collectivedebitors' => [
        'Bezeichnung' => 'Sammeldebitoren',
        'Link'=>'index.php?module=collectivedebitors&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'install'=>true,
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Buchhaltung|}',
        'settings'=>true
      ]
    ,'googleapi' => [
        'Bezeichnung' => 'Google API',
        'Link'=>'index.php?module=googleapi&action=list',
        'Icon'=>'Icons_dunkel_25.gif',
        'install'=>true,
        'Versionen' => '',
        'kategorie'=>'{|System|}',
        'settings'=>true
      ]
    ,'log' => [
          'Bezeichnung' => 'Log File',
          'Link'=>'index.php?module=log&action=list',
          'Icon'=>'Icons_dunkel_25.gif',
          'install'=>true,
          'Versionen' => '',
          'beta' => true,
          'kategorie'=>'{|System|}'
      ]
    , 'einstellungen' => [
        'Bezeichnung' => 'Beta Programm',
        'Link' => 'index.php?module=einstellungen&action=betaprogram',
        'Icon' => 'Icons_dunkel_25.gif',
        'Versionen' => '',
        'kategorie' => '{|System|}',
        'beta' => false,
      ]
    ,'systemhealth' => [
        'Bezeichnung' => 'System Health',
        'Link'=>'index.php?module=systemhealth&action=list',
        'Icon'=>'Icons_dunkel_25.gif',
        'install'=>true,
        'Versionen'=>'',
        'kategorie'=>'{|System|}'
      ],
      'epost' => [
        'Bezeichnung' => 'EPost App',
        'Link' => 'index.php?module=epost',
        'Icon'=>'Icons_dunkel_9.gif',
        'install' => true,
        'Versionen' => 'ENT',
        'beta' => false,
        'kategorie'=>'{|Buchhaltung|}'
      ]
    ,'documentbatches' => [
        'Bezeichnung' => 'Dokumentenstapelverarbeitung',
        'Link'=>'index.php?module=documentbatches&action=list',
        'Icon'=>'Icons_dunkel_25.gif',
        'install'=>true,
        'Versionen'=>'ENT',
        'kategorie'=>'{|Workflows|}',
        'project_sensitive' => true
      ]
    ,'systemtemplates'=>array('Bezeichnung'=>'System Templates',
        'Link'=>'index.php?module=systemtemplates&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'install'=>true,
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true
      )
    ,'projektlogbuch' => [
        'Bezeichnung' => 'Projektlogbuch',
        'Link'=>'index.php?module=projektlogbuch&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'install'=>true,
        'Versionen'=>'ENT',
        'beta' => false,
        'legacy' => true,
        'kategorie'=>'{|Workflows|}'
      ]
    ,'adresse' => [
        'Bezeichnung' => 'Adressen',
        'Link'=>'index.php?module=adresse&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'install'=>true,
        'Versionen'=>'', 'beta' => false,'kategorie'=>'{|Stammdaten|}'
      ]
    ,'angebot' => [
        'Bezeichnung' => 'Angebot',
        'Link'=>'index.php?module=angebot&action=list',
        'Icon'=>'Icons_dunkel_11.gif',
        'install'=>true,
        'Versionen'=>'', 'beta' => false,'kategorie'=>'{|Verkauf|}',
        'project_sensitive' => true
      ]
    ,'artikel' => [
        'Bezeichnung' => 'Artikel',
        'Link'=>'index.php?module=artikel&action=list',
        'Icon'=>'Icons_dunkel_15.gif',
        'Versionen'=>'', 'beta' => false,'kategorie'=>'{|Stammdaten|}',
        'project_sensitive'=>true
      ]
    ,'auftrag' => [
        'Bezeichnung' => 'Auftrag',
        'Link'=>'index.php?module=auftrag&action=list',
        'Icon'=>'Icons_dunkel_11.gif',
        'install'=>true,
        'Versionen'=>'', 'beta' => false, 'kategorie'=>'{|Verkauf|}',
        'project_sensitive' => true
      ]
    ,'backup' => [
        'Bezeichnung' => 'Backup',
        'Link'=>'index.php?module=backup&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'',
        'install' => true, 'kategorie'=>'{|System|}'
      ]
    ,'bestellung' => [
        'Bezeichnung' => 'Bestellung',
        'Link'=>'index.php?module=bestellung&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'', 'beta' => false, 'kategorie'=>'{|Einkauf|}',
        'project_sensitive' => true
      ]
    ,'gutschrift' => [
        'Bezeichnung' => 'Gutschrift / Stornorechnung',
        'Link'=>'index.php?module=gutschrift&action=list',
        'Icon'=>'Icons_dunkel_11.gif',
        'Versionen'=>'', 'beta' => false,
        'install' => true,
        'kategorie'=>'{|Buchhaltung|}',
        'project_sensitive' => true
      ]
    ,'lieferschein' => [
        'Bezeichnung' => 'Lieferschein',
        'Link'=>'index.php?module=lieferschein&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'',
        'install' => true,
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}',
        'project_sensitive' => true
      ]
    ,'rechnung' => [
        'Bezeichnung' => 'Rechnung',
        'Link'=>'index.php?module=rechnung&action=list',
        'Icon'=>'Icons_dunkel_11.gif',
        'Versionen'=>'', 'beta' => false,
        'install' => true,
        'kategorie'=>'{|Buchhaltung|}',
        'project_sensitive' => true
      ]
    ,'artikelkontingente' => [
        'Bezeichnung' => 'Artikelkontingente',
        'Link'=>'index.php?module=artikelkontingente&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ALL',
        'beta' => false,
        'legacy' => true,
        'kategorie'=>'{|Verkauf|}'
      ]
    ,'buchhaltungexport' => [
        'Bezeichnung' => 'Finanzbuchhaltung Export',
        'Link'=>'index.php?module=buchhaltungexport&action=list',
        'Icon'=>'Icons_dunkel_17.gif',
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Buchhaltung|}',
        'settings'=>true
      ]
    ,'chargen' => [
        'Bezeichnung' => 'Chargen',
        'Link'=>'index.php?module=chargen&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}',
        'project_sensitive' => true,
        'install'=>true
      ]
    ,'etikettendrucker' => [
        'Bezeichnung' => 'Etikettendrucker',
        'Link'=>'index.php?module=etikettendrucker&action=list',
        'Icon'=>'Icons_dunkel_24.gif',
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|System|}',
        'settings'=>true
      ]
    ,'importvorlage' => [
        'Bezeichnung' => 'Import/Export Zentrale',
        'Link'=>'index.php?module=importvorlage&action=uebersicht',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'',
        'beta' => false,
        'install' => true,
        'kategorie'=>'{|Stammdaten|}'
      ]
    ,'kalender' => [
        'Bezeichnung' => 'Kalender',
        'Link'=>'index.php?module=kalender&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'',
        'beta' => false,
        'kategorie'=>'{|Team Funktionen|}'
      ]
    ,'kasse' => [
        'Bezeichnung' => 'Kassenbuch',
        'Link'=>'index.php?module=kasse&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Buchhaltung|}',
        'settings'=>true,
        'project_sensitive' => true
      ]
    ,'kommissionieraufkleber' => [
        'Bezeichnung' => 'Kommissionieraufkleber',
        'Link'=>'index.php?module=kommissionieraufkleber&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}'
      ]
    ,'kommissionierlauf' => [
        'Bezeichnung' => 'Kommissionierlauf',
        'Link'=>'index.php?module=kommissionierlauf&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}',
        'project_sensitive' => true
      ]
    ,'lager' => [
        'Bezeichnung' => 'Lagerverwaltung',
        'Link'=>'index.php?module=lager&action=list',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'',
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}'
      ]
    ,'lohnabrechnung' => [
        'Bezeichnung' => 'Lohnabrechnung',
        'Link'=>'index.php?module=lohnabrechnung&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Buchhaltung|}'
      ]
    ,'managementboard' => [
        'Bezeichnung' => 'Management Board',
        'Link'=>'index.php?module=managementboard&action=list',
        'Icon'=>'Icons_dunkel_15.gif',
        'Versionen'=>'',
        'beta' => false,
        'kategorie'=>'{|Controlling und Statistik|}'
      ]
    ,'mahnwesen' => [
        'Bezeichnung' => 'Mahnwesen',
        'Link'=>'index.php?module=mahnwesen&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Buchhaltung|}',
        'settings'=>true,
        'project_sensitive' => true
      ]
    ,'mhdwarning' => [
        'Bezeichnung' => 'Mindesthaltbarkeit',
        'Link'=>'index.php?module=mhdwarning&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}',
        'project_sensitive' => true
      ]
    ,'projekt' => [
        'Bezeichnung' => 'Projekte',
        'Link'=>'index.php?module=projekt&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'',
        'beta' => false,
        'kategorie'=>'{|Stammdaten|}',
        'project_sensitive' => true
      ]
    ,'provisionenartikel' => [
        'Bezeichnung' => 'Provisionen Artikel Brutto',
        'Link'=>'index.php?module=provisionenartikel&action=list',
        'Icon'=>'Icons_dunkel_17.gif',
        'Versionen'=>'ALL',
        'beta' => false,
        'legacy' => true,
        'kategorie'=>'{|Buchhaltung|}'
      ]
    ,'rma' => [
        'Bezeichnung' => 'RMA Lieferungen',
        'Link'=>'index.php?module=rma&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}'
      ]
    ,'seriennummern' => [
        'Bezeichnung' => 'Seriennummern',
        'Link'=>'index.php?module=seriennummern&action=list',
        'Icon'=>'Icons_dunkel_15.gif',
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}',
        'project_sensitive' => true
      ]
    ,'statistiken' => [
        'Bezeichnung' => 'Statistiken',
        'Link'=>'index.php?module=statistiken&action=dashboard',
        'Icon'=>'Icons_dunkel_22.gif',
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Controlling und Statistik|}',
        'project_sensitive' => true
      ]
    ,'steuerregeln' => [
        'Bezeichnung' => 'Steuerregeln',
        'Link'=>'index.php?module=steuerregeln&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'install'=>true,
        'Versionen'=>'ALL',
        'legacy' => true,
        'kategorie'=>'{|Buchhaltung|}',
        'settings'=>true
      ]
    ,'umsatzstatistik' => [
        'Bezeichnung' => 'Umsatzstatistik',
        'Link'=>'index.php?module=umsatzstatistik&action=allgemein',
        'Icon'=>'Icons_dunkel_22.gif',
        'Versionen'=>'ENT',
        'beta' => false,'kategorie'=>'{|Controlling und Statistik|}'
      ]
    /*
    ,'vereinsverwaltung' => [
        'Bezeichnung' => 'Vereinsverwaltung',
        'Link'=>'index.php?module=vereinsverwaltung&action=list',
        'Icon'=>'Icons_dunkel_14.gif',
        'Versionen'=>'ALL',
        'beta' => false,
        'legacy' => true,
        'kategorie'=>'{|Sonstiges|}'
      ]
    */
    ,'versanderzeugen' => [
        'Bezeichnung' => 'Versandzentrum',
        'Link'=>'index.php?module=versanderzeugen&action=offene',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}'
      ]
    ,'vertreter' => [
        'Bezeichnung' => 'Vertreterabrechnungen',
        'Link'=>'index.php?module=vertreter&action=list',
        'Icon'=>'Icons_dunkel_17.gif',
        'Versionen'=>'ALL',
        'beta' => false,
        'legacy' => true,
        'kategorie'=>'{|Buchhaltung|}'
      ]
    ,'wareneingang' => [
        'Bezeichnung' => 'Wareneingang Paket Annahme',
        'Link'=>'index.php?module=wareneingang&action=paketannahme',
        'Icon'=>'Icons_dunkel_1.gif',
        'Versionen'=>'',
        'beta' => false,
        'kategorie'=>'{|Lager und Logistik|}'
      ]
    ,'webmail' => [
        'Bezeichnung' => 'E-Mail-Archiv',
        'Link'=>'index.php?module=webmail&action=list',
        'Icon'=>'Icons_dunkel_4.gif',
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|System|}'
      ]

    ,'pinwand' => [
        'Bezeichnung' => 'Pinnwand',
        'Link'=>'index.php?module=welcome&action=pinwand',
        'Icon'=>'Icons_dunkel_15.gif',
        'Versionen'=>'',
        'beta' => false,
        'kategorie'=>'{|Team Funktionen|}'
      ]
    ,'welcome' => [
        'Bezeichnung' => 'Persönliche Einstellungen',
        'Link'=>'index.php?module=welcome&action=settings',
        'Icon'=>'Icons_dunkel_15.gif',
        'Versionen'=>'',
        'beta' => false,
        'kategorie'=>'{|System|}'
      ]

    ,'zahlungsverkehr' => [
        'Bezeichnung' => 'Zahlungsverkehr',
        'Link'=>'index.php?module=zahlungsverkehr&action=ueberweisung',
        'Icon'=>'Icons_dunkel_18.gif',
        'install'=>true,
        'beta' => false,
        'Versionen'=>'ENT',
        'kategorie'=>'{|Buchhaltung|}'
      ]
    ,'zeiterfassung' => [
        'Bezeichnung' => 'Zeiterfassung',
        'Link'=>'index.php?module=zeiterfassung&action=create',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'',
        'beta' => false,
        'kategorie'=>'{|Mitarbeiter Verwaltung|}'
      ],
      'TransferEdi' => [
        'Bezeichnung' => 'Übertragungsmodul EDI',
        'Link'=>'index.php?module=uebertragungen&action=accounts',
        'Icon'=>'Icons_dunkel_2.gif',
        'install'=>true,
        'Versionen' => 'ALL',
        'beta' => false,
        'kategorie'=>'{|Schnittstellen|}'
      ],
      'trigger' => [
        'Bezeichnung' => 'Trigger',
        'Link'=>'index.php?module=trigger&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen' => 'ALL',
        'beta' => false,
        'kategorie'=>'{|Workflows|}'
      ],
      'report' => [
          'Bezeichnung' => 'Berichte',
          'Link'=>'index.php?module=report&action=list',
          'Icon'=>'Icons_dunkel_1.gif',
          'install'=>true,
          'Versionen'=>'',
          'kategorie'=>'{|Controlling und Statistik|}'
      ],
      'supersearch' => [
          'Bezeichnung' => 'SuperSearch',
          'Link'=>'index.php?module=supersearch&action=settings',
          'Icon'=>'Icons_dunkel_1.gif',
          'install'=>true,
          'Versionen'=>'',
          'beta' => false,
          'kategorie'=>'{|System|}'
      ],
      'hubspot' => ['Bezeichnung' => 'Hubspot',
        'Link' => 'index.php?module=hubspot&action=settings',
        'Icon' => 'Icons_dunkel_2.gif',
        'install' => true,
        'Versionen' => 'ALL',
        'beta' => true,
        'kategorie'=>'{|Schnittstellen|}',
        'settings'=>true
      ],
      'amainvoice' => ['Bezeichnung' => 'amainvoice',
        'Link' => 'index.php?module=amainvoice&action=list',
        'Icon' => 'Icons_dunkel_2.gif',
        'install' => true,
        'Versionen' => '',
        'beta' => true,
        'kategorie'=>'{|Buchhaltung|}',
        'settings'=>true
      ],
      'vatreduction2020' => [
        'Bezeichnung' => 'Auftragsblocker',
        'Link'        => 'index.php?module=vatreduction2020&action=list',
        'Icon'        => 'Icons_dunkel_18.gif',
        'install'     => true,
        'Versionen'   => '',
        'beta'        => false,
      ],
      'databaseviewer' => [
        'Bezeichnung' => 'Datenbank Ansicht',
        'Link' => 'index.php?module=databaseviewer&action=list',
        'Icon' => 'Icons_dunkel_14.gif',
        'Versionen' => '',
        'beta' => true,
        'kategorie'=>'{|Workflows|}'
      ],
      'avocadostore' => [
        'Bezeichnung' => 'Avocadostore',
        'Link' => 'index.php?module=avocadostore&action=list',
        'Icon' => 'Icons_dunkel_15.gif',
        'Versionen' => '',
        'beta' => true,
        'kategorie'=>'{|Shop Schnittstelle|}'
      ],
      'tableau' => [
        'Bezeichnung' => 'Tableau',
        'Link' => 'index.php?module=tableau&action=list',
        'Icon' => 'Icons_dunkel_15.gif',
        'Versionen' => '',
        'beta' => true,
        'kategorie'=>'{|Schnittstellen|}'
      ],
      'zapier' => [
        'Bezeichnung' => 'Zapier',
        'Link' => 'index.php?module=zapier&action=list',
        'Icon' => 'Icons_dunkel_15.gif',
        'Versionen' => '',
        'beta' => true,
        'kategorie'=>'{|Schnittstellen|}'
      ],
      'sellerlogic' => [
        'Bezeichnung' => 'Sellerlogic',
        'Link' => 'index.php?module=sellerlogic&action=list',
        'Icon' => 'Icons_dunkel_15.gif',
        'Versionen' => '',
        'beta' => true,
        'kategorie'=>'{|Schnittstellen|}'
      ],
      'quickscan' => [
        'Bezeichnung' => 'Artikel QuickScan',
        'Link' => 'index.php?module=quickscan&action=list',
        'Icon' => 'Icons_dunkel_15.gif',
        'Versionen' => 'ENT',
        'beta' => true,
        'kategorie'=>'{|Stammdaten|}'
      ],
      'otto' => [
        'Bezeichnung'=>'Otto Schnittstelle',
        'Link'=>'index.php?module=uebertragungen&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ENT',
        'install'=>true,
        'kategorie'=>'{|Schnittstellen|}'
      ],
      'batchpicking' => [
        'Bezeichnung'=>'Batch Kommissionierung',
        'Link'=>'index.php?module=batchpicking&action=list',
        'Icon'=>'Icons_dunkel_9.gif',
        'Versionen'=>'ALL',
        'install'=>true,
        'beta' => true,
        'kategorie'=>'{|Lager und Logistik|}'
      ],
      'totp' => [
        'Bezeichnung' => '2 Faktor Authentifizierung',
        'Link' => 'index.php?module=welcome&action=settings',
        'install' => true,
        'Versionen' => '',
        'beta' => true,
        'Icon'=>'Icons_dunkel_2.gif',
        'kategorie'=>'{|Sicherheit und Datenschutz|}'
      ],
      'dataprotect' => [
        'Bezeichnung'=>'Data Protect',
        'Link'=>'index.php?module=dataprotect&action=list',
        'Icon'=>'Icons_dunkel_15.gif',
        'Versionen'=>'ALL',
        'install'=>true,
        'kategorie'=>'{|Sicherheit und Datenschutz|}'
      ],

      'kontorahmen' => [
        'Bezeichnung' => 'Kontorahmen',
        'Link'=>'index.php?module=kontorahmen&action=list',
        'Icon'=>'Icons_dunkel_18.gif',
        'install'=>true,
        'Versionen'=>'ENT',
        'beta' => false,
        'kategorie'=>'{|Buchhaltung|}',
        'settings'=>true
      ],

      'kostenstellen' => [
        'Bezeichnung' => ' Kostenstellen ',
        'Link'=>'index.php?module=kostenstellen&action=list',
        'Icon'=>'Icons_dunkel_17.gif',
        'install'=>true,
        'Versionen' => 'ENT',
        'beta' => false,
        'kategorie'=>'{|Buchhaltung|}',
        'settings'=>true
      ],

      'zahlungsweisen' => [
        'Bezeichnung' => ' Zahlungsweisen',
        'Link'=>'index.php?module=zahlungsweisen&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'install'=>true,
        'Versionen' => '',
        'beta' => false,
        'kategorie'=>'{|Buchhaltung|}',
        'settings'=>true,
        'project_sensitive' => true
      ],
      'konten' => [
        'Bezeichnung' => ' Geschäftskonten',
        'Link'=>'index.php?module=konten&action=list',
        'Icon'=>'Icons_dunkel_16.gif',
        'install'=>true,
        'Versionen' => 'ENT',
        'beta' => false,
        'kategorie'=>'{|Buchhaltung|}',
        'settings'=>true,
        'project_sensitive' => true
      ],
      'voucherpos' => [
        'Bezeichnung' => 'Gutschein-App ohne POS',
        'Link'=>'index.php?module=voucherpos&action=create',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'ALL',
        'kategorie'=>'{|Verkauf|}',
        'settings'=>true
      ],
      'getmyinvoices' => [
        'Bezeichnung' => 'GetMyInvoices',
        'Link'=>'index.php?module=getmyinvoices&action=list',
        'Icon'=>'Icons_dunkel_2.gif',
        'Versionen'=>'',
        'kategorie'=>'{|Buchhaltung|}'
      ],
      'pipedrive' => ['Bezeichnung' => 'Pipedrive',
        'Link' => 'index.php?module=pipedrive&action=settings',
        'Icon' => 'Icons_dunkel_2.gif',
        'install' => true,
        'Versionen' => 'ALL',
        'beta' => true
      ],
      'shopimporter_amazonvendordf' => [
        'Bezeichnung' => 'Amazon Vendor DF',
        'Link' => 'index.php?module=onlineshops&action=create&cmd=shopimporter_amazonvendordf',
        'Icon' => 'Icons_dunkel_1.gif',
        'Versionen' => 'ALL',
        'showonlyoninstall' => true,
        'install' => true,
        'kategorie' => '{|Shop Schnittstelle|}',
        'beta' => true,
      ],
      'versandarten_amazonvendordf' => [
        'Bezeichnung' => 'Amazon Vendor DF',
        'Link' => 'index.php?module=versandarten&action=list',
        'Icon' => 'Icons_dunkel_9.gif',
        'Versionen' => 'ALL',
        'showonlyoninstall' => true,
        'kategorie' => '{|Versandarten|}',
        'settings' => true
      ],
      'fiskaly' => [
        'Bezeichnung' => 'Fiskaly Anbindung',
        'Link' => 'index.php?module=fiskaly',
        'Icon' => 'Icons_dunkel_2.gif',
        'install' => true,
        'Versionen' => 'ALL',
        'showonlyoninstall' => true,
        'beta' => true
      ],
      'coppersurcharge'=>[
        'Bezeichnung'=>'Kupferzuschlag',
        'Link'=>'index.php?module=coppersurcharge&action=list',
        'Icon'=>'Icons_dunkel_13.gif',
        'Versionen'=>'ALL',
        'install'=>true,
        'beta' => true,
        'kategorie'=>'{|Stammdaten|}'
      ],
    );

    if(empty(erpAPI::Ioncube_Property('disabledevelopmentprogram'))) {
      $apps['developmentprogram'] = [
        'Bezeichnung' => 'Development Programm',
        'Link' => 'index.php?module=developmentprogram&action=list',
        'Icon' => 'Icons_dunkel_25.gif',
        'Versionen' => 'ENT',
        'kategorie' => '{|System|}',
        'beta' => false,
      ];
    }

    if(!empty($this->app->Tpl) && method_exists($this->app->Tpl,'pruefeuebersetzung')) {
      foreach($apps as $k => $v) {
        if(strpos($v['Bezeichnung'],'<') === false) {
          $apps[$k]['Bezeichnung'] = $this->app->Tpl->pruefeuebersetzung($v['Bezeichnung'],'appstore');
        }
      }
    }
    return $apps;
  }

  public function AppstoreTesten(): void
  {
    $this->app->erp->MenuEintrag("index.php?module=appstore&action=list","&Uuml;bersicht");

    // nur wenn OSS und keine Testversion
    $this->app->erp->MenuEintrag("index.php?module=appstore&action=testen","Jetzt Business kostenlos testen");

    $this->app->Tpl->Add('TAB1','<iframe src="https://'.$this->app->Conf->updateHost.'/xentralanfrage/" width="100%" border="0" style="border:0px;" height="700"></iframe>');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  /**
   * @param string $haystack
   * @param string $needle
   *
   * @return bool
   */
  public function match($haystack, $needle): bool
  {
    $needle = strtolower($this->app->erp->UmlauteEntfernen($needle));
    $haystack = strtolower($this->app->erp->UmlauteEntfernen($haystack));
    $needleWords = explode(' ',$needle);
    foreach($needleWords as $word) {
      if($word !== '') {
        if(strpos($haystack, $word) === false) {
          return false;
        }
      }
    }

    return true;
  }

  /**
   * @param array|null $apps
   *
   * @return array|null
   */
  public function markGetAppsWithUserRights($apps): ?array
  {
    if(empty($apps) || empty($apps['installiert'])) {
      return $apps;
    }

    foreach($apps['installiert'] as $key => $app) {
      $apps['installiert'][$key]['my_app'] = $this->hasAppUserRights($app);
    }

    return $apps;
  }

  /**
   * @param string      $val
   * @param string|null $filterCategory
   *
   * @return null|array
   */
  public function GetApps($val = '', $filterCategory = null): ?array
  {
    $val = (string)$val;
    $module = $this->getAppsList(true);
    if(empty($module)) {
      return $module;
    }

    $sprachen = ['german', 'english',];
    if(method_exists($this->app->User,'GetSprachen')) {
      $sprachen = $this->app->User->GetSprachen();
      if (in_array('deutsch', $sprachen, true) && !in_array('german', $sprachen, true)) {
        $sprachen[] = 'german';
      }
    }
    if(!is_array($sprachen)) {
      return [];
    }
    $sprachen = array_reverse($sprachen);
    $inline = [];
    foreach($sprachen as $sprache)  {
      if(preg_match_all('/[a-zA-Z0-9\-]/', $sprache, $erg)) {
        if(is_file(dirname(dirname(__DIR__)).'/languages/'.$sprache.'/inline.php')) {
          include_once dirname(dirname(__DIR__)).'/languages/'.$sprache.'/inline.php';
        }
      }
    }


    if(isset($module['installiert'])) {
      foreach($module['installiert'] as $k => $v) {
        $moduleInstallKey = $module['installiert'][$k]['key'];
        $module['installiert'][$k]['md5'] = md5($v['Bezeichnung']);
        $module['installiert'][$k]['helpdesk'] = (isset($inline[$moduleInstallKey]['default']['link']))
          ? $inline[$moduleInstallKey]['default']['link'] : '';
        $module['installiert'][$k]['beschreibung'] = (isset($inline[$moduleInstallKey]['default']['description']))
          ? $inline[$moduleInstallKey]['default']['description'] : '';
        $module['installiert'][$k]['screenshots'] = (isset($inline[$moduleInstallKey]['default']['screenshots']))
          ? $inline[$moduleInstallKey]['default']['screenshots'] : array();
        $module['installiert'][$k]['youtube'] = (isset($inline[$moduleInstallKey]['default']['youtube']))
          ? $inline[$moduleInstallKey]['default']['youtube'] : '';

        if(isset($inline[$moduleInstallKey]['default']['shop'])) {
          $module['installiert'][$k]['shop'] = $inline[$moduleInstallKey]['default']['shop'];
        }
        if(isset($inline[$moduleInstallKey]['default']['kategorie'])) {
          $module['installiert'][$k]['kategorie'] = $inline[$moduleInstallKey]['default']['kategorie'];
          $v['kategorie'] = $module['installiert'][$k]['kategorie'];
        }

        $kategorien['installiert'][$k] = isset($v['kategorie'])?$v['kategorie']:'';
        $bezeichnung['installiert'][$k] = str_replace(array('&Uuml;','&Auml;','&Ouml;','&auml;','&uuml;','&ouml;'),array('Ue','Ae','Oe','ae','ue','oe'), $v['Bezeichnung']);
        $matchBezeichnung = $this->match($v['Bezeichnung'], $val);
        $matchKategorie = (!empty($v['kategorie'])) ? $this->match($v['kategorie'], $val) : false;
        if(!empty($filterCategory)
          && $v['kategorie'] !== $filterCategory
          && $v['kategorie'] !== '{|'.$filterCategory.'|}'
        ) {
          $matchBezeichnung = false;
          $matchKategorie = false;
        }
        $module['installiert'][$k]['match'] = $matchBezeichnung || $matchKategorie;
      }
      array_multisort($kategorien['installiert'], SORT_ASC,$bezeichnung['installiert'], SORT_ASC, $module['installiert']);
    }

    if(empty($module['kauf'])) {
      return $module;
    }


    foreach($module['kauf'] as $k => $v) {
      $moduleKaufKey = $module['kauf'][$k]['key'];
      $module['kauf'][$k]['md5'] = md5($v['Bezeichnung']);
      $module['kauf'][$k]['helpdesk'] = (isset($inline[$moduleKaufKey]['default']['link']))
        ? $inline[$moduleKaufKey]['default']['link'] : '';
      $module['kauf'][$k]['beschreibung'] = (isset($inline[$moduleKaufKey]['default']['description']))
        ? $inline[$moduleKaufKey]['default']['description'] : '';
      $module['kauf'][$k]['screenshots'] = (isset($inline[$moduleKaufKey]['default']['screenshots']))
        ? $inline[$moduleKaufKey]['default']['screenshots'] : array();
      $module['kauf'][$k]['youtube'] = (isset($inline[$moduleKaufKey]['default']['youtube']))
        ? $inline[$moduleKaufKey]['default']['youtube'] : '';

      if(isset($inline[$moduleKaufKey]['default']['shop'])){
        $module['kauf'][$k]['shop'] = $inline[$moduleKaufKey]['default']['shop'];
      }
      if(isset($inline[$moduleKaufKey]['default']['kategorie'])){
        $module['kauf'][$k]['kategorie'] = $inline[$moduleKaufKey]['default']['kategorie'];
        $v['kategorie'] = $module['kauf'][$k]['kategorie'];
      }

      $kategorien['kauf'][$k] = isset($v['kategorie']) ? $v['kategorie'] : '';
      $bezeichnung['kauf'][$k] = str_replace(array('&Uuml;','&Auml;','&Ouml;','&auml;','&uuml;','&ouml;'),array('Ue','Ae','Oe','ae','ue','oe'), $v['Bezeichnung']);
      $matchBezeichnung = $this->match($v['Bezeichnung'], $val);
      $matchKategorie = (!empty($v['kategorie'])) ? $this->match($v['kategorie'], $val) : false;
      if(!empty($filterCategory)
        && $v['kategorie'] !== $filterCategory
        && $v['kategorie'] !== '{|'.$filterCategory.'|}'
      ) {
        $matchBezeichnung = false;
        $matchKategorie = false;
      }
      $module['kauf'][$k]['match'] = $matchBezeichnung || $matchKategorie;
    }
    array_multisort($kategorien['kauf'], SORT_ASC,$bezeichnung['kauf'], SORT_ASC, $module['kauf']);

    return $module;
  }

  /**
   * @return JsonResponse
   */
  public function GetSearchResults(): JsonResponse
  {
    $filterCategory = $this->app->Secure->GetPOST('category');
    if(empty($filterCategory)) {
      $filterCategory = null;
    }
    $module = $this->GetApps($this->app->Secure->GetPOST('val'), $filterCategory);

    $anzeigen = array();
    $ausblenden = array();
    $kateinblenden = array();
    $katausblenden = array();
    $kaufbarGefunden = 0;
    $installiertGefunden = 0;

    if ($module) {
      if (!empty($module['installiert'])) {
        foreach ($module['installiert'] as $modul) {
          if (empty($modul['kategorie'])) {
            $modul['kategorie'] = '{|Allgemein|}';
          }
          $katausblendenIndex = 'i'.md5($modul['kategorie']);
          if ($modul['match']) {
            $installiertGefunden++;
            $anzeigen['m'.md5($modul['Bezeichnung'])] = true;
            if (isset($modul['kategorie'])) {
              $kateinblenden[$katausblendenIndex] = true;
              if (isset($katausblenden) && isset($katausblenden[$katausblendenIndex])) {
                unset($katausblenden[$katausblendenIndex]);
              }
            }
          }
          else {
            $ausblenden['m'.md5($modul['Bezeichnung'])] = true;
            if (isset($modul['kategorie']) && (!isset($kateinblenden) || !isset($kateinblenden[$katausblendenIndex]))) {
              $katausblenden[$katausblendenIndex] = true;
            }
          }
        }
      }
      if (!empty($module['kauf'])) {
        foreach($module['kauf'] as $modul) {
          if (empty($modul['kategorie'])) {
            $modul['kategorie'] = '{|Allgemein|}';
          }
          $katausblendenIndex = 'k'.md5($modul['kategorie']);
          if ($modul['match']) {
            $kaufbarGefunden++;
            $anzeigen['m'.md5($modul['Bezeichnung'])] = true;
            if (isset($modul['kategorie'])) {
              $kateinblenden[$katausblendenIndex] = true;
              if(isset($katausblenden) && isset($katausblenden[$katausblendenIndex])){
                unset($katausblenden[$katausblendenIndex]);
              }
            }
          }
          else {
            $ausblenden['m'.md5($modul['Bezeichnung'])] = true;
            if (isset($modul['kategorie']) && (!isset($kateinblenden) || !isset($kateinblenden[$katausblendenIndex]))) {
              $katausblenden[$katausblendenIndex] = true;
            }
          }
        }
      }
    }

    return new JsonResponse(
      [
        'anzeigen'=>$anzeigen,
        'ausblenden'=>$ausblenden,
        'kateinblenden'=>$kateinblenden,
        'katausblenden'=>$katausblenden,
        'kaufbargefunden'=>$kaufbarGefunden,
        'installiertgefunden'=>$installiertGefunden,
      ]
    );
  }

  /**
   * @return JsonResponse
   */
  public function HandleActivateAjaxAction(): JsonResponse
  {
    $module = $this->app->Secure->GetPOST('module');
    $ret = ['status' => 0, 'module' => $module];
    if($this->app->erp->RechteVorhanden('appstore', 'activate')) {
      if($this->activateModule($module)) {
        $ret['status'] = 1;
      }
      else {
        $ret['error'] = 'Das Modul konnte nicht aktiviert werden';
      }
    }
    else {
      $ret['error'] = 'Sie haben keine Rechte um das Modul zu aktivieren';
    }

    return new JsonResponse($ret);
  }

  /**
   * @return JsonResponse
   */
  public function HandleDeactivateAjaxAction(): JsonResponse
  {
    $module = $this->app->Secure->GetPOST('module');
    $ret = ['status' => 0, 'module' => $module];
    if($this->app->erp->RechteVorhanden('appstore', 'deactivate')) {
      if(!empty($module) && !in_array($module, $this->nodeactivateable)) {
        if($this->deactivateModule($module)) {
          $ret['status'] = 1;
        }
        else {
          $ret['error'] = 'Das Modul konnte nicht deaktiviert werden';
        }
      }
      else {
        $ret['error'] = 'Das Modul kann nicht deaktiviert werden';
      }
    }
    else {
      $ret['error'] = 'Sie haben keine Rechte um das Modul zu deaktivieren';
    }

    return new JsonResponse($ret);
  }

  public function setTabItemActive($filter){
      if($filter === "userapps"){
          $this->app->Tpl->Add('ACTIVEINSTALLED', 'appstore-tab-active');
      } else {
          $this->app->Tpl->Add('ACTIVEALL', 'appstore-tab-active');
      }
  }

  public function appTypeVisibility($filter){
      $hide = 'display: none';

      if($filter === "userapps"){
          $this->app->Tpl->Set('POPULARVISIBILITY', $hide);
          $this->app->Tpl->Set('AVAILABLEVISIBILITY', $hide);
      }
  }

  public function AppstoreList()
  {
    $filter = $this->app->User->GetParameter('appstore_filter');

    $this->setTabItemActive($filter);
    $this->appTypeVisibility($filter);

    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'allapps') {
      $this->app->User->SetParameter('appstore_filter', '');
      $this->app->Location->execute('index.php?module=appstore&action=list');
    }
    if($cmd === 'activate') {
      return $this->HandleActivateAjaxAction();
    }


    if($cmd === 'deactivate') {
      return $this->HandleDeactivateAjaxAction();
    }

    $isDetail = $cmd === 'detail';
    if($isDetail) {
      $app = $this->app->Secure->GetGET('app');
      $this->app->erp->MenuEintrag('index.php?module=appstore&action=list&cmd=detail&app='.$app, '{|&Uuml;bersicht|}', true);
    }
    else{
      $this->app->erp->MenuEintrag('index.php?module=appstore&action=list', '{|&Uuml;bersicht|}');
    }
    $this->app->erp->MenuEintrag('index.php?module=appstore&action=testen','{|14 Tage Business testen|}');

    // Detail-Seite anzeigen
    if ($isDetail) {
      $this->app->erp->MenuEintrag('index.php?module=appstore&action=list','{|zur&uuml;ck zur &Uuml;bersicht|}');
      $this->DisplayAppDetailPage();
      return;
    }

    // Suche wurde ausgefüllt
    if ($cmd === 'suche') {
      return $this->GetSearchResults();
    }

    $module = $this->GetApps();

    $canUserTestModule = $this->app->erp->RechteVorhanden('appstore', 'testmodule');

    if($canUserTestModule && $this->app->erp->isIoncube() && $this->app->Secure->GetPOST('testen')) {
      $modul = $this->app->Secure->GetPOST('modul');
      $get = $this->app->Secure->GetPOST('modulbestaetigen');
      $json = false;
      if($modul == '' && $get != '') {
        $json = true;
        $message = '';
        if($module && !empty($module['kauf'])) {
          foreach($module['kauf'] as $k => $v) {
            if($v['md5'] === $get) {
              $mods = $this->app->erp->getAppList();
              foreach($mods as $k2 => $v2) {
                if(md5($v2['Bezeichnung']) === $get) {
                  $modul = $k2;
                  break;
                }
              }
            }
          }
        }
      }
      if($modul) {
        $testapp = $modul;
        if(is_file(dirname(__DIR__).'/update.php')) {
          $result = '';
          include_once dirname(__DIR__).'/update.php';
          if($result === 'OK') {
            $message = '<div class="info">Das Modul wurde zum Testen angefragt. Bitte ziehen Sie sich über den Update-Prozess in ca 10 Minuten ein Update. Sie erhalten mit diesem das Modul zum Testen.</div>';
            if(!$json) {
              $this->app->Tpl->Add('MESSAGE', $message);
            }
          }
          else{
            $message = '<div class="error">Es ist ein Fehler beim Senden der Anfrage aufgetreten: '.$result.'</div>';
            if(!$json) {
              $this->app->Tpl->Add('MESSAGE', $message);
            }
          }
        }
      }
      if($json) {
        return new JsonResponse(['html'=>$message]);
      }
      $this->clearCache();
    }
    elseif($canUserTestModule && !empty($get = $this->app->Secure->GetGET('get')) && !empty($module['kauf'])) {
      foreach($module['kauf'] as $v) {
        if($v['md5'] === $get) {
          $mods = $this->app->erp->getAppList();
          foreach($mods as $k2 => $v2) {
            if(md5($v2['Bezeichnung']) === $get) {
              $this->app->Tpl->Add(
                'MESSAGE',
                '<div class="info">Bitte best&auml;tigen: 
                  <form method="POST" action="index.php?module=appstore&action=list">
                  <input type="hidden" name="modul" value="'
                .$k2.'" /><input type="submit" style="float:right;" value="Testmodul '
                .$v2['Bezeichnung'].' anfragen" name="testen" />
                  </form>
                  </div>'
              );
              break;
            }
          }
        }
      }
    }

    $this->app->erp->RunHook('appstoreModulelistToShow', 1, $module);
    $module = $this->markGetAppsWithUserRights($module);
    $categoryFilter = $this->app->Secure->GetGET('category');
    if(!empty($categoryFilter)) {
        $this->app->Tpl->Set('PAGETYPE', "category-page");

      foreach($module as $type => $apps) {
        foreach($apps as $appKey => $app) {
          if(empty($app['kategorie']) || str_replace(['{|','|}'], '', $app['kategorie']) !== $categoryFilter) {
            unset($module[$type][$appKey]);
          }
        }
      }
      //foreach($module)
    }
    $this->app->Tpl->Set('CATEGORYFILTER', $categoryFilter);
    if($categoryFilter === ''){
      $categoryFilter = null;
    }
    $filterCategory = null;

    // HTML für installierte/gekaufte Module generieren
    if (count($module['installiert']) === 1){
      $this->app->Tpl->Add('INSTALLIERTEAUSBLENDEN', 'display: none;');
    }
    else{
      $moduleInstalliert = $this->GenerateInstallierteModuleHtml($module['installiert'], $categoryFilter);
      $this->app->Tpl->Add('MODULEINSTALLIERT', $moduleInstalliert);
    }

    // HTML für verfügbare/kaufbare Module generieren
    $moduleVerfuegbar = $this->GenerateVerfuegbareModuleHtml($module['kauf'], $categoryFilter);
    $numberAvailableModules = strlen($moduleVerfuegbar);

    $this->app->Tpl->Add('MODULEVERFUEGBAR', $moduleVerfuegbar);

    // Suchfilter generieren
    $suchFilterHtml = $this->GenerateSuchfilterHtml();
    $this->app->Tpl->Add('SUCHFILTER', $suchFilterHtml);

    // Highlight-Boxen generieren
    $highlightsHtml = $this->GenerateHighlightsHtml($categoryFilter);

    $this->app->Tpl->Add('HIGHLIGHTS', $highlightsHtml);

    // Detail-Seite ausblenden
    $this->app->Tpl->Set('APPSTOREDETAILSEITEAUSBLENDEN', 'display: none;');
    $this->showUpdateMessage();

    /** @var Welcome $welcome */
    $welcome = $this->app->loadModule('welcome');
    $welcome->loadChangeLogByTime();
    $this->app->Tpl->Set('COUNTUPDATES', $this->app->erp->GetKonfiguration('welcome_changelog_count'));
    $this->app->Tpl->Parse('TAB1','appstore.tpl');
    $this->app->Tpl->Add('TAB1', '<script id="modulesJson" type="application/json">'.json_encode($module).'</script>');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  /**
   * @param array $module
   *
   * @return string
   */
  public function GenerateVerfuegbareModuleHtml($module, $filterCategory = null): string
  {
    $html = '';
    $kategorieVorher = '';
    foreach ($module as $modul) {
      // Externen AppStore nicht unter verfügbare anzeigen
      if ($modul['key'] === 'appstore_extern') {
        continue;
      }
      if(!empty($modul['showonlyoninstall'])) {
        continue;
      }
      // Kategorie-Überschriften einfügen
      $kategorie = (!empty($modul['kategorie'])) ? $modul['kategorie'] : '{|Allgemein|}';
      if(
        $filterCategory !== null
        && $kategorie !== $filterCategory
        && $kategorie !== '{|'.$filterCategory.'|}'
      ) {
        continue;
      }
      if ($kategorie !== $kategorieVorher) {
        $html .= '<h3 id="k' . md5($kategorie) . '">' . $kategorie . '</h3>';
      }

      $showTestVersion = (empty($modul['test']) || empty($modul['ablaufdatum'])) && stripos($modul['Bezeichnung'],'AppStore') === false;

      $contentlink = 'index.php?module=appstore&action=list&cmd=detail&app='.$modul['key'];
        $iconTag = $this->GetAppIconTagByCategory($kategorie);
        $html .=
            '<a href="index.php?module=appstore&action=list&cmd=detail&app='
            .$modul['key'].'&overviewpage=appstore&overviewpageaction=list" class="module" id="m'.$modul['md5'].'">'.
            '<div class="icon app-category-icon-'. $iconTag .'"></div>'.
            '<div class="content">' .
            '<div class="title">' .
            strip_tags($modul['Bezeichnung'])
            .'</div>' .
            (!empty($modul['ablaufdatum'])
                ? '<small>{|Ablauf am|} '
                . $this->app->String->Convert($modul['ablaufdatum'], '%1-%2-%3', '%3.%2.%1') . '</small>'
                : '')
            . '<div>'
            . (!empty($modul['beta'])?' <span class="beta">BETA</span>':'')
            . (!empty($modul['legacy']) && $modul['legacy']?' <span class="legacy">LEGACY</span>':'')
            . (!empty($modul['special']) && $modul['special']?' <span class="special">SPECIAL</span>':'')
            . (!empty($modul['project_sensitive']) && $modul['project_sensitive']?' <span class="project_sensitive">PROJEKT</span>':'')
            . '</div>'
            . '<button class="install">{|Installieren|}</button>'
            . '</div>'
            . '</a>';

      $kategorieVorher = $kategorie;
    }

    return $html;
  }

  /**
   * @param array $module
   *
   * @return string
   */
  public function GenerateInstallierteModuleHtml($module, $filterCategory = null): string
  {
    $html = '';
    $kategorieVorher = '';

    foreach ($module as $modul) {
      // AppStore-Modul nicht in "Käufe" anzeigen
      if (stripos($modul['Bezeichnung'],'AppStore') !== false) {
        continue;
      }

      $kategorie = (!empty($modul['kategorie'])) ? $modul['kategorie'] : '{|Allgemein|}';
      if(
        $filterCategory !== null
        && $kategorie !== $filterCategory
        && $kategorie !== '{|'.$filterCategory.'|}'
      ) {
        continue;
      }
      // Kategorie-Überschriften einfügen
      if ($kategorie !== $kategorieVorher) {
        $html .= '<h3 id="i' . md5($kategorie) . '">' . $kategorie . '</h3>';
      }

      $contentlink = 'index.php?module=appstore&action=list&cmd=detail&app='.$modul['key'];
      if(!empty($modul['Link'])) {
        $contentlink = $modul['Link'];
      }
      $moduleDeactivated = $this->isModuleDeactivated($modul['key']);
      $iconTag = $this->GetAppIconTagByCategory($kategorie);
      $html .=

          '<a href="index.php?module=appstore&action=list&cmd=detail&app='
          .$modul['key'].'&overviewpage=appstore&overviewpageaction=list" class="module installed-app" id="m'.$modul['md5'].'">'.
        '<div class="icon app-category-icon-'. $iconTag .'"></div>'.
        '<div class="content">' .
        '<div class="title">' .
        strip_tags($modul['Bezeichnung'])
        .'</div>' .
        (!empty($modul['ablaufdatum'])
          ? '<small>{|Ablauf am|} '
          . $this->app->String->Convert($modul['ablaufdatum'], '%1-%2-%3', '%3.%2.%1') . '</small>'
          : '')
        . '<div>'
        . (!empty($modul['beta'])?' <span class="beta">BETA</span>':'')
        . (!empty($modul['legacy']) && $modul['legacy']?' <span class="legacy">LEGACY</span>':'')
        . (!empty($modul['special']) && $modul['special']?' <span class="special">SPECIAL</span>':'')
        . (!empty($modul['project_sensitive']) && $modul['project_sensitive']?' <span class="project_sensitive">PROJEKT</span>':'')
        . '</div>'
        . '<button>{|Öffnen|}</button></div>'
        . '</a>';

      $kategorieVorher = $kategorie;
    }

    return $html;
  }

  /**
   * @param string $category
   *
   * @return array|null
   */
  public function getOverViewFromCategory($category): ?array
  {
    switch($category) {
      case 'Shop Schnittstelle':
        return [
          'key' => 'onlineshops',
          'Bezeichnung' => '{|&Uumlbersicht|}',
          'Link' => 'index.php?module=onlineshops&action=list',
        ];
      case 'Versandarten':
        return [
          'key' => 'versandarten',
          'Bezeichnung' => '{|&Uumlbersicht|}',
          'Link' => 'index.php?module=versandarten&action=list',
        ];
      case 'Zahlungsweisen':
        return [
          'key' => 'zahlungsweisen',
          'Bezeichnung' => '{|&Uumlbersicht|}',
          'Link' => 'index.php?module=zahlungsweisen&action=list',
        ];
    }

    return null;
  }

  /**
   * @param string $category
   *
   * @return string
   */
  public function GetAppIconTagByCategory($category): string
  {
      static $categoryIconMap = [
              '{|Allgemein|}'                  => 'general',
              '{|Belege|}'                     => 'documents',
              '{|Buchhaltung|}'                => 'accounting-calculator',
              '{|Controlling und Statistik|}'  => 'controlling',
              '{|Einkauf|}'                    => 'buying',
              '{|Lager und Logistik|}'         => 'warehouse',
              '{|Marketing|}'                  => 'marketing',
              '{|Mitarbeiter Verwaltung|}'     => 'employees',
              '{|Produktion|}'                 => 'production',
              '{|Schnittstelle|}'              => 'interface',
              '{|Shop Schnittstelle|}'         => 'shop-interface',
              '{|Sicherheit und Datenschutz|}' => 'security',
              '{|Sonstiges|}'                  => 'misc',
              '{|Stammdaten|}'                 => 'master-data',
              '{|System|}'                     => 'system',
              '{|Team Funktionen|}'            => 'team-functions',
              '{|Verkauf|}'                    => 'sales',
              '{|Versandarten|}'               => 'shipping',
              '{|Workflows|}'                  => 'workflows',
              '{|Zahlungsweisen|}'             => 'payment',
      ];

      if (isset($categoryIconMap[$category])) {
          return $categoryIconMap[$category];
      }

      return 'general'; // Fallback, falls Mapping fehlt
  }

  /**
   * @return string
   */
  public function GenerateSuchfilterHtml(): string
  {
    $html = '';
    $filterArray = array(
      '{|Belege|}',
      '{|Buchhaltung|}',
      '{|Controlling und Statistik|}',
      '{|Einkauf|}',
      '{|Lager und Logistik|}',
      '{|Marketing|}',
      '{|Mitarbeiter Verwaltung|}',
      '{|Produktion|}',
      '{|Schnittstelle|}',
      '{|Shop Schnittstelle|}',
      '{|Sicherheit und Datenschutz|}',
      '{|Sonstiges|}',
      '{|Stammdaten|}',
      '{|System|}',
      '{|Team Funktionen|}',
      '{|Verkauf|}',
      '{|Versandarten|}',
      '{|Workflows|}',
      '{|Zahlungsweisen|}'
    );
    foreach ($filterArray as $filter) {
        $icon = $this->GetAppIconTagByCategory($filter);

      $html .= sprintf(
        '<a href="index.php?module=appstore&action=list&category=%s" data-category="%s" class="category-link app-category-icon-'. $icon .'"><span>%s</span></a>',
        urlencode(substr($filter,2,-2)) , substr($filter,2,-2) , $filter
      );
    }

    return $html;
  }

  /**
   * @var string|null $categoryFilter
   *
   * @return string
   */
  public function GenerateHighlightsHtml(?string $categoryFilter = null): string
  {
    $highlightApps = [
      'aufgaben',
      'chat',
      'vertriebscockpit',
      'wiedervorlage',
      'layoutvorlagen',
      'zertifikatgenerator',
      'filiallieferung',
      'sammelrechnung',
      'serienbrief'
    ];

    $highlights = [];
    $appList = $this->getAppsList();
    foreach($appList['installiert'] as $appKey => $appVal) {
      $appList['installiert'][$appKey]['installiert'] = true;
    }
    $appListKomplett = array_merge($appList['kauf'], $appList['installiert']);
    foreach ($appListKomplett as $appItem) {
      if (in_array($appItem['key'], $highlightApps)) {
        $highlights[] = $appItem;
      }
    }

    $html = '';
    foreach ($highlights as $key => $highlight) {
        $kategorie = (!empty($highlight['kategorie'])) ? $highlight['kategorie'] : '{|Allgemein|}';
        if(!empty($categoryFilter) && !empty($kategorie)
          && str_replace(['{|','|}'], '', $kategorie) !== $categoryFilter) {
          continue;
        }
        $iconTag = $this->GetAppIconTagByCategory($kategorie);
        $html .=
            '<a href="index.php?module=appstore&action=list&cmd=detail&app='
            .$highlight['key'].'&overviewpage=appstore&overviewpageaction=list" class="module" id="m'.md5($highlight['key']).'">'.
            '<div class="icon app-category-icon-'. $iconTag .'"></div>'.
            '<div class="content">' .
            '<div class="title">' .
            strip_tags($highlight['Bezeichnung'])
            .'</div>' .
            (!empty($highlight['ablaufdatum'])
                ? '<small>{|Ablauf am|} '
                . $this->app->String->Convert($highlight['ablaufdatum'], '%1-%2-%3', '%3.%2.%1') . '</small>'
                : '')
            . '<div>'
            . (!empty($highlight['beta'])?' <span class="beta">BETA</span>':'')
            . (!empty($highlight['legacy']) && $highlight['legacy']?' <span class="legacy">LEGACY</span>':'')
            . (!empty($highlight['special']) && $highlight['special']?' <span class="special">SPECIAL</span>':'')
            . (!empty($highlight['project_sensitive']) && $highlight['project_sensitive']?' <span class="project_sensitive">PROJEKT</span>':'')
            . '</div>'
            . ($highlight['installiert'] ? '<button class="">{|Öffnen|}</button>' : '<button class="install">{|Installieren|}</button>'). '</div>'
            . '</a>';
    }

    return $html;
  }

  function DisplayAppDetailPage()
  {
    $module = $this->app->Secure->GetGET('app');
    $appList = $this->GetApps();
    foreach ($appList['kauf'] as $appItem) {
      if ($module === $appItem['key']) {
        $appInfo = $appItem;
        $appInfo['kaufbar'] = true;
        break;
      }
    }
    foreach ($appList['installiert'] as $appItem) {
      if ($module === $appItem['key']) {
        $appInfo = $appItem;
        $appInfo['installiert'] = true;
        break;
      }
    }


    $sprache = 'german';
    $inline = [];
    include_once dirname(dirname(__DIR__)).'/languages/'.$sprache.'/inline.php';
    if(empty($appInfo['beschreibung']) && !empty($appInfo['key']) && !empty($inline[$appInfo['key']])) {
      $appInfo['beschreibung'] = !empty($inline[$appInfo['key']]['default']['description'])?
        $inline[$appInfo['key']]['default']['description']:'';
    }

    if($appInfo['Versionen']=='') {
      $appInfo['version'] = 'OSS,FREE,PRO,ENT,PRE';
    }
    else {
      $appInfo['version'] = $appInfo['Versionen'];
    }

    $tmpversion = $appInfo['version'];
    $tmpversion = str_replace('ALL', 'Extra im App-Store',$tmpversion);
    $tmpversion = str_replace('OSS', 'Open-Source',$tmpversion);
    $tmpversion = str_replace('PRO', 'Starter',$tmpversion);
    $tmpversion = str_replace('ENT', 'Business',$tmpversion);
    $tmpversion = str_replace('PRE', 'Enterprise (Neues Preismodell)',$tmpversion);
    $tmpversion = str_replace(',',', ',$tmpversion);
    $tmpversion = rtrim($tmpversion,',');
    $appInfo['version'] = $tmpversion;

    if($this->app->erp->ModulVorhanden($appInfo['key'], true))
    {
      $appInfo['kaufbar'] = false;
      $appInfo['installiert']= true;
    }

    $this->app->erp->RunHook('appstoreModuleDetailToShow', 1, $appInfo);
    $appInfo['beschreibung'] = $appInfo['beschreibung']."<br><br><i>Inkl. ab Version: ".$appInfo['version']."</i>";

    $zusatzInfos = '';
    if(!empty($appInfo['beta'])){
      $zusatzInfos .= '<span class="beta">BETA</span>';
    }
    if(!empty($appInfo['legacy']) && $appInfo['legacy']){
      $zusatzInfos .= '<span class="legacy">LEGACY</span>';
    }
    if(!empty($appInfo['special']) && $appInfo['special']){
      $zusatzInfos .= '<span class="special">SPECIAL</span>';
    }
    if(!empty($appInfo['project_sensitive']) && $appInfo['project_sensitive']){
      $zusatzInfos .= '<span class="project_sensitive">PROJEKT</span>';
    }

    $price = null;

    if (isset($appInfo)) {
      $iconTag = $this->GetAppIconTagByCategory($appInfo['kategorie']);
      $html =
        '<div class="information-left">' .
        '<div class="app-icon app-category-icon-'. $iconTag .'"></div>' .
        '</div>' .
        '<div class="information-right">' .
        '<h1 class="title">' . $appInfo['Bezeichnung']
        .(empty($price)?'':' (pro Monat '.$price.' EUR zzgl. ges. USt.)')
        . '</h1>' .
        '<div class="description">' . $appInfo['beschreibung'] .'<br>'. $zusatzInfos .'</div>'.
        '<div class="dropdown primary">';
      if ($appInfo['kaufbar']){
      }
      if ($appInfo['installiert']){
        $moduleDeactivated = $this->isModuleDeactivated($appInfo['key']);
        $html .=
          '<a class="dropdown-link button button-primary'.($moduleDeactivated?' deactivated':' activated').'" href="' . ($moduleDeactivated?'#':$appInfo['Link']) . '" target="_blank">'.
          '{|Zum Modul|}'.
          '</a>';
      }
      $html .= '<ul class="dropdown-menu">';
      if(!empty($price)) {
        $html .=
          '<li><a class="dropdown-link buybutton" data-type="add_module" data-newvalue="'.$appInfo['key']
          .'" data-oldvalue="" data-info="pro Monat '.$price.' EUR zzgl. ges. USt." data-price="' . $price . '" href="#">' .
          '{|Modul mieten|}' .
          '</a></li>';
        $this->app->Tpl->Set('UPDATEKEY', '');
        $this->app->Tpl->Parse('MODALBUYPOPUP', 'appstore_modulbuypopup.tpl');
      }
      if(!in_array($appInfo['key'], $this->nodeactivateable)) {
        if($moduleDeactivated && $this->app->erp->RechteVorhanden('appstore', 'activate')){
          $html .= '<li><a class="dropdown-sublink activate" target="_blank" data-module="'.$appInfo['key'].'" href="index.php?module=appstore&action=activate&smodule=' . $appInfo['key'] . '">aktivieren</a></li>';
        }elseif(!$moduleDeactivated && $this->app->erp->RechteVorhanden('appstore', 'deactivate')){
          $html .= '<li><a class="dropdown-sublink deactivate" target="_blank" data-module="'.$appInfo['key'].'" href="index.php?module=appstore&action=deactivate&smodule=' . $appInfo['key'] . '">deaktivieren</a></li>';
        }
      }

      if(!empty($appInfo['shop'])){
        $html .= '<li><a class="dropdown-sublink" target="_blank" href="' . $appInfo['shop'] . '">{|Zum AppStore|}</a></li>';
      }
      if(!empty($appInfo['helpdesk'])){
        $html .= '<li><a class="dropdown-sublink" target="_blank" href="' . $appInfo['helpdesk'] . '">{|Handbuch|}</a></li>';
      }
      if(!empty($appInfo['youtube'])){
        $html .= '<li><a class="dropdown-sublink" target="_blank" href="https://youtu.be/' . $appInfo['youtube'] . '">{|Video|}</a></li>';
      }
      $html .= '</ul></div></div>';
      $screenshots = '';
      if (is_array($appInfo['screenshots']) && !empty($appInfo['screenshots'])) {
        foreach ($appInfo['screenshots'] as $screenshot) {
          $screenshots .= '<div class="screenshot"><img src="'.$screenshot.'" alt=""></div>';
        }
      }

    }
    else {
      $html = '<h1>{|App wurde nicht gefunden|}</h1>';
      $screenshots = '';
    }

    // Übersichtsseite ausblenden
    $this->app->Tpl->Set('APPSTOREUEBERSICHTAUSBLENDEN', 'display: none;');

    $this->app->Tpl->Set('MODULINFORMATION', $html);
    $this->app->Tpl->Set('MODULSCREENSHOTS', $screenshots);

    /** @var Welcome $welcome */
    $welcome = $this->app->loadModule('welcome');
    $welcome->loadChangeLogByTime();
    $this->app->Tpl->Set('COUNTUPDATES', $this->app->erp->GetKonfiguration('welcome_changelog_count'));

    $this->app->Tpl->Parse('TAB1', 'appstore.tpl');
    $module = $this->GetApps();
    $this->app->Tpl->Add('TAB1', '<script id="modulesJson" type="application/json">'.json_encode($module).'</script>');
    $this->app->Tpl->Parse('PAGE', 'tabview.tpl');
  }

    public function getOverViewLink($overviewPageAction): ?string
    {
        if(!in_array($overviewPageAction, ['', 'list'])) {
            return null;
        }

        return 'index.php?module=appstore&action=list';
    }

  /**
   * @param array       $modullist
   * @param string      $prefix
   * @param string|null $link
   * @param array|null  $customIcon
   */
  public function AddModuleHtml($modullist, $prefix = '', $link = null, $customIcon = null)
  {
    $this->AddModuleInstalledHtml($modullist, $prefix);
    if($customIcon !== null){
      $this->app->Tpl->Add(
        'MODULEINSTALLIERT',
        '<a href="'.$customIcon['link'].'" class="modulecol tilegrid-tile" id="m' . md5('Extern') . '">'
      );
      $this->app->Tpl->Add(
        'MODULEINSTALLIERT',
        '<div class="icon app-category-icon-general"></div>'
      );
      $this->app->Tpl->Add(
        'MODULEINSTALLIERT',
        '<div><div> '.$customIcon['title'].'</div></div>'
      );
      $this->app->Tpl->Add(
        'MODULEINSTALLIERT',
        '</a>' . "\n");
    }
    $this->app->Tpl->Add(
      'MODULEINSTALLIERT',
      '<div class="clear"></div></div>' . "\n");
    $this->AddModuleBuyHtml($modullist, $prefix, $link);
  }


  /**
   * @param array       $modullist
   * @param string      $prefix
   * @param string|null $link
   */
  public function AddModuleBuyHtml($modullist, $prefix = '', $link = null)
  {
    if($link === null) {
      $link = 'index.php?module=appstore&action=list&cmd=detail&app=';
    }
    if($modullist && isset($modullist['kauf'])) {
      $this->app->Tpl->Add('MODULEVERFUEGBAR','<div class="modulediv tilegrid-container">'."\n");
      $countBuyModules = 0;
      foreach($modullist['kauf'] as $modul) {
        if(isset($modul['key']) && ($prefix === '' || strpos($modul['key'], $prefix) === 0)) {
          $countBuyModules++;
          $kategorie = (!empty($modul['kategorie'])) ? $modul['kategorie'] : '{|Allgemein|}';
          $iconTag = $this->GetAppIconTagByCategory($kategorie);
          $this->app->Tpl->Add(
            'MODULEVERFUEGBAR',
            '<a href="'.$link.md5($modul['Bezeichnung']).'" class="modulecol tilegrid-tile" id="m'.md5($modul['Bezeichnung']).'">'
          );
          $this->app->Tpl->Add(
            'MODULEVERFUEGBAR',
            '<div class="icon app-category-icon-'. $iconTag .'"></div>'
          );
          $this->app->Tpl->Add(
            'MODULEVERFUEGBAR',
            '<div class="tilegrid-tile-title">'
            .$modul['Bezeichnung']
            .(!empty($modul['beta'])?'<br><span class="beta">Beta</span>':'')
            .'</div>'

            .'</a>');
        }
      }
      $this->app->Tpl->Add('MODULEVERFUEGBAR','<div class="clear"></div> </div>'."\r\n");
      if($countBuyModules === 0) {
        $this->app->Tpl->Set('BEFOREMODULESTOBUY', '<!--');
        $this->app->Tpl->Set('AFTERMODULESTOBUY', '-->');
      }
    }
  }

  /**
   * @param array  $modullist
   * @param string $prefix
   */
  public function AddModuleInstalledHtml($modullist, $prefix = '')
  {
    $this->app->Tpl->Add('MODULEINSTALLIERT','<div class="modulediv tilegrid-container">'."\n");
    if($modullist && !empty($modullist['installiert'])) {
      foreach($modullist['installiert'] as $modul)  {
        if(isset($modul['key']) && strpos($modul['key'], $prefix) === 0) {

          $kategorie = (!empty($modul['kategorie'])) ? $modul['kategorie'] : '{|Allgemein|}';
          $iconTag = $this->GetAppIconTagByCategory($kategorie);
          $link = '#';

          $this->app->Tpl->Add(
            'MODULEINSTALLIERT',
            '<a data-module="'.$modul['key'].'" href="' .$link. '" class="createbutton modulecol tilegrid-tile" id="m'.md5($modul['Bezeichnung']).'">'
          );

          $this->app->Tpl->Add(
            'MODULEINSTALLIERT',
            '<div class="icon app-category-icon-'. $iconTag. '"></div>'
          );
          $this->app->Tpl->Add(
            'MODULEINSTALLIERT',
            '<div><div> <span class="tilegrid-tile-title">'.$modul['Bezeichnung'] .'</span><br>'
            .(!empty($modul['beta'])?' <span class="beta">Beta</span>':'')
            .(!empty($modul['legacy'])?' <span class="legacy">Legacy</span>':'')
            .(!empty($modul['special'])?' <span class="special">Special</span>':'')
            .(!empty($modul['test'])?' (Testversion Ablauf am '.$this->app->String->Convert($modul['ablaufdatum'], "%1-%2-%3", "%3.%2.%1").')':'').'</div></div>'
          );
          $this->app->Tpl->Add(
            'MODULEINSTALLIERT',
            '</a>'."\n"
          );
        }
      }
    }
  }

  public function Install(): void
  {
    $this->app->erp->CheckTable('module_status');
    $this->app->erp->CheckColumn('module', 'VARCHAR(64)','module_status',"DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn('active', 'TINYINT(1)','module_status','DEFAULT 0 NOT NULL');

    $this->app->erp->RegisterHook('supersearch_detail', 'appstore', 'AppstoreSupersearchDetail');
    $this->app->erp->RegisterHook('HeadLines', 'appstore', 'AppstoreHeadLines');
  }

  /**
   * @param string       $headline
   * @param string       $headline1
   * @param string|array $headline2
   */
  public function AppstoreHeadLines($headline, $headline1, $headline2): void
  {
    if((empty($headline)) && empty($headline1)) {
      return;
    }
    if(stripos($headline, 'BETA') !== false || stripos($headline1, 'BETA') !== false ||
       stripos($headline, 'LEGACY') !== false || stripos($headline1, 'LEGACY') !== false ||
       stripos($headline, 'SPECIAL') !== false || stripos($headline1, 'SPECIAL') !== false ||
       stripos($headline, 'PROJEKT') !== false || stripos($headline1, 'PROJEKT') !== false) {
      return;
    }
    if(!empty($headline2) && is_string($headline2) && (stripos($headline2, 'BETA') !== false &&
        stripos($headline2, 'LEGACY') !== false && stripos($headline2, 'SPECIAL') !== false) &&
        stripos($headline2, 'PROJEKT') !== false) {
      return;
    }
    if(!empty($headline2) && is_array($headline2) && (stripos(implode(' ',$headline2), 'BETA') !== false &&
        stripos(implode(' ',$headline2), 'LEGACY') !== false && stripos(implode(' ', $headline2), 'SPECIAL') !== false &&
        stripos(implode(' ',$headline2), 'PROJEKT') !== false)) {
      return;
    }
    if(empty($this->app->Secure)) {
      return;
    }
    $module = $this->app->Secure->GetGET('module');
    if(empty($module)) {
      return;
    }
    if(in_array($module,['onlineshops','shopexport','uebertragungen','versandarten','zahlungsweisen'])) {
      return;
    }
    if(!$this->isBeta($module) && !$this->isLegacy($module) && !$this->isSpecial($module) && !$this->isProjectSensitive($module)) {
      return;
    }
    if(!empty($headline)) {
      if($this->isBeta($module)){
        $this->app->erp->Headlines($headline.' <span class="beta">Beta</span>');
        return;
      }
      if($this->isLegacy($module)){
        $this->app->erp->Headlines($headline.' <span class="legacy">Legacy</span>');
        return;
      }
      if($this->isSpecial($module)){
        $this->app->erp->Headlines($headline.' <span class="special">Special</span>');
        return;
      }
      if($this->isProjectSensitive($module)){
        //$this->app->erp->Headlines($headline.' <span class="project_sensitive">Projekt</span>');
        return;
      }
    }
    if(!empty($headline1)) {
      if($this->isBeta($module)){
        $this->app->erp->Headlines('', $headline1.' <span class="beta">Beta</span>');
        return;
      }
      if($this->isLegacy($module)){
        $this->app->erp->Headlines('', $headline1.' <span class="legacy">Legacy</span>');
        return;
      }
      if($this->isSpecial($module)){
        $this->app->erp->Headlines('', $headline1.' <span class="special">Special</span>');
        return;
      }
      if($this->isProjectSensitive($module)){
        //$this->app->erp->Headlines('', $headline1.'<span class="project_sensitive">Projekt</span>');
        return;
      }
    }
  }

  /**
   * @param \Xentral\Widgets\SuperSearch\Query\DetailQuery   $detailQuery
   * @param \Xentral\Widgets\SuperSearch\Result\ResultDetail $detailResult
   *
   * @return void
   */
  public function AppstoreSupersearchDetail($detailQuery, $detailResult): void
  {
    if ($detailQuery->getGroupKey() !== 'apps') {
      return;
    }

    $modules = $this->BuildModuleList();
    $moduleKey = $detailQuery->getItemIdentifier();

    // Wiki-Inhalte aufbereiten
    /** @var Wiki $wiki */
    $wiki = $this->app->erp->LoadModul('wiki');
    $workspace = $wiki->getWorkspaceByName('XentralHandbuch');
    $workspaceId = !empty($workspace)? (int)$workspace['id'] : 0;
    $wikiArticle = $wiki->getArticleByName($moduleKey, $workspaceId);
    list($wikiMenuArray, $wikiArticleContent) = $wiki->parseMenuFromHtml($wikiArticle['content'], 3, 'supersearch');
    $wikiMenuHtml = '<h2>Inhaltsverzeichnis</h2>' . $wiki->drawMenuFromArray($wikiMenuArray, 'supersearch');
    $wikiContent = $wikiMenuHtml . $wikiArticleContent;
    $wikiLink = !empty($wikiArticle) ? sprintf('index.php?module=wiki&action=list&workspace=%s&cmd=%s', $workspaceId, $moduleKey) : null;
    // ENDE Wiki-Inhalte aufbereiten

    $moduleData = isset($modules[$moduleKey]) ? $modules[$moduleKey] : false;
    if (is_array($moduleData) && $moduleData['key'] === $moduleKey) {
      if($this->app->erp->ModulVorhanden($moduleData['key'])){
        if (!empty($moduleData['module_link'])) {
          $detailResult->addButton('Zum Modul', $moduleData['module_link']);
        }
      }else{
        $detailResult->addButton('Zum Modul', 'index.php?module=appstore&action=list&cmd=detail&app='.$moduleData['key']);
      }


      // Einstellungs-Button deaktivieren, falls Action-Liste leer
      // Grund: Modul wurde dann noch nie aufgerufen und erpApi::RechteVorhanden() würde ein falsches Ergebnis liefern
      $disableSettingsButton = empty($this->app->erp->getActionsByModule($moduleKey)) ? true : false;

      // Einstellungs-Button hinzufügen
      if (!$disableSettingsButton){
        $hasSettingsPermission1 = $this->app->erp->RechteVorhanden($moduleKey, 'einstellungen');
        $hasSettingsPermission2 = $this->app->erp->RechteVorhanden($moduleKey, 'settings');
        if($hasSettingsPermission1 || $hasSettingsPermission2){
          $settingsAction = $hasSettingsPermission1 ? 'einstellungen' : 'settings';
          $settingsLink = sprintf('index.php?module=%s&action=%s', $moduleKey, $settingsAction);
          $detailResult->addButton('Einstellungen', $settingsLink, ['target' => '_blank', 'data-icon' => 'settings']);
        }
      }

      if (!empty($wikiLink)) {
        $detailResult->addButton('Handbuch', $wikiLink, ['target' => '_blank', 'data-icon' => 'help', 'class' => 'button button-secondary']);
      }
      if (empty($wikiLink) && !empty($moduleData['helpdesk_link'])){
        $detailResult->addButton('Handbuch', $moduleData['helpdesk_link'], ['target' => '_blank', 'data-icon' => 'help', 'class' => 'button button-secondary']);
      }
      if (!empty($moduleData['youtube_link'])){
        $detailResult->addButton('Video', $moduleData['youtube_link'], ['target' => '_blank']);
      }
      if (!empty($moduleData['description'])){
        $detailResult->setDescription($moduleData['description']);
      }
      if (!empty($wikiArticle['content'])) {
        $detailResult->setDescription($wikiContent);
      }

      $detailResult->setTitle($moduleData['title']);
    }
  }

  /**
   * @return array
   */
  public function BuildModuleList(): array
  {
    $apps = $this->getAppList();

    // ANFANG inline.php einlesen
    $sprachen = ['german','english'];
    if(method_exists($this->app->User,'GetSprachen')) {
      $sprachen = $this->app->User->GetSprachen();
      if (in_array('deutsch', $sprachen, true) && !in_array('german', $sprachen, true)) {
        $sprachen[] = 'german';
      }
    }
    if(!is_array($sprachen)) {
      return [];
    }
    $sprachen = array_reverse($sprachen);
    $inline = [];
    foreach($sprachen as $sprache)  {
      if(preg_match_all('/[a-zA-Z0-9\-]/', $sprache, $erg)) {
        $inlineFilePath = dirname(dirname(__DIR__)) . sprintf('/languages/%s/inline.php', $sprache);
        if(is_file($inlineFilePath)) {
          include_once $inlineFilePath;
        }
      }
    }
    // ENDE inline.php einlesen

    $modules = [];
    foreach($apps as $appKey => $app) {
      $modules[$appKey] = [
        'key' => $appKey,
        'title' => $app['Bezeichnung'],
        'title2' => empty($app['Bezeichnung2'])?'': $app['Bezeichnung2'],
        'module_link' => $app['Link'],
        'module_link2' => empty($app['Link2'])?'':$app['Link2'],
        'description' => isset($inline[$appKey]['default']['description']) ? $inline[$appKey]['default']['description'] : null,
        'category' => isset($inline[$appKey]['default']['kategorie']) ? $inline[$appKey]['default']['kategorie'] : null,
        'helpdesk_link' => isset($inline[$appKey]['default']['link']) ? $inline[$appKey]['default']['link'] : null,
        'youtube_link' => isset($inline[$appKey]['default']['youtube']) ? 'https://youtu.be/' . $inline[$appKey]['default']['youtube'] : null,
        'beta' => isset($app['beta']) ? $app['beta'] : null,
        'legacy' => isset($app['legacy']) ? $app['legacy'] : false,
        'special' => isset($app['special']) ? $app['special'] : false,
        'popular' => !empty($app['popular']),
        'project_sensitive' => isset($app['project_sensitive']) ? $app['project_sensitive'] : false
      ];
    }

    return $modules;
  }

  /**
   * @param string $moduleName
   *
   * @return bool
   */
  public function deactivateModule($moduleName): bool
  {
    if(empty($moduleName) || in_array($moduleName, $this->nodeactivateable)) {
      return false;
    }

    if(!$this->app->erp->ModulVorhanden($moduleName, true)) {
      return false;
    }

    $check = $this->app->DB->Select(
      sprintf(
        "SELECT `id` FROM `module_status` WHERE `module` = '%s' LIMIT 1",
        $this->app->DB->real_escape_string($moduleName)
      )
    );
    if($check){
      $this->app->DB->Update(
        sprintf(
          'UPDATE `module_status` SET `active` = 0 WHERE `id` = %d', $check
        )
      );
    }
    else {
      $this->app->DB->Insert(
        sprintf(
          'INSERT INTO `module_status` (`module`, `active`) VALUES (\'%s\', 0)',
          $this->app->DB->real_escape_string($moduleName)
        )
      );
    }
    if(!isset($this->deactivatesModules[$this->app->Conf->WFdbname])
      || $this->deactivatesModules[$this->app->Conf->WFdbname] === null) {
      $this->loadDeactivatedModules();
    }
    elseif(!in_array($moduleName, $this->deactivatesModules[$this->app->Conf->WFdbname])) {
      $this->deactivatesModules[$this->app->Conf->WFdbname][] = $moduleName;
    }

    $obj = $this->app->loadModule($moduleName);
    if(!empty($obj) && method_exists($obj, 'Uninstall')) {
      $obj->Uninstall();
    }

    return true;
  }

  /**
   * @param string $moduleName
   *
   * @return bool
   */
  public function activateModule($moduleName): bool
  {
    if(empty($moduleName)) {
      return false;
    }
    if(!$this->app->erp->ModulVorhanden($moduleName, true)) {
      return false;
    }
    $check = $this->app->DB->Select(
      sprintf(
        "SELECT `id` FROM `module_status` WHERE `module` = '%s' LIMIT 1",
        $this->app->DB->real_escape_string($moduleName)
      )
    );
    if($check) {
      $this->app->DB->Update(
        sprintf(
          'UPDATE `module_status` SET `active` = 1 WHERE `id` = %d', $check
        )
      );
    }
    else {
      $this->app->DB->Insert(
        sprintf(
          'INSERT INTO `module_status` (`module`, `active`) VALUES (\'%s\', 1)',
          $this->app->DB->real_escape_string($moduleName)
        )
      );
    }
    if(!isset($this->deactivatesModules[$this->app->Conf->WFdbname])
      || $this->deactivatesModules[$this->app->Conf->WFdbname] === null) {
      $this->loadDeactivatedModules();
    }
    elseif(in_array($moduleName, $this->deactivatesModules[$this->app->Conf->WFdbname])) {
      $this->deactivatesModules[$this->app->Conf->WFdbname] =
        array_diff($this->deactivatesModules[$this->app->Conf->WFdbname], [$moduleName]);
    }

    $this->app->erp->InstallModul($moduleName);

    return true;
  }

  /**
   * @param string $moduleName
   *
   * @return bool
   */
  public function isModuleDeactivated($moduleName): bool
  {
    if(empty($moduleName) || in_array($moduleName, $this->nodeactivateable)) {
      return false;
    }

    if(!isset($this->deactivatesModules[$this->app->Conf->WFdbname])
      || $this->deactivatesModules[$this->app->Conf->WFdbname] === null) {
      $this->loadDeactivatedModules();
    }

    return in_array($moduleName, $this->deactivatesModules[$this->app->Conf->WFdbname]);
  }

  /**
   * @return void
   */
  protected function loadDeactivatedModules(): void
  {
    $deactivatedModules = $this->app->DB->SelectFirstCols(
      'SELECT DISTINCT `module` FROM `module_status` WHERE `active` = 0 AND `module` <> \'\''
    );
    $this->deactivatesModules[$this->app->Conf->WFdbname] = [];

    if(empty($deactivatedModules)) {
      return;
    }
    $this->deactivatesModules[$this->app->Conf->WFdbname] = array_diff($deactivatedModules, $this->nodeactivateable);
  }

}
