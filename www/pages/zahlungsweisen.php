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
use Xentral\Modules\PaymentMethod\Data\PaymentMethodData;
use Xentral\Modules\PaymentMethod\Service\PaymentMethodService;

include_once dirname(__DIR__) .'/lib/class.zahlungsweise.php';
//class Zahlungsweisen extends GenZahlungsweisen {
class Zahlungsweisen {
  /** @var Application $app */
  var $app;

  /** @var string */
  const MODULE_NAME = 'PaymentMethod';

  /** @var string[] $stylesheet */
  public $stylesheet = [
    './classes/Modules/Appstore/www/css/tilegrid.css',
  ];

  /** @var string[] $javascript */
  public $javascript = [
    './classes/Modules/PaymentMethod/www/js/payment_method_create.js',
  ];

  /** @var PaymentMethodService $service */
  protected $service;

  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public static function TableSearch($app, $name, $erlaubtevars): array
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name) {
      case 'zahlungsweisen_list':
        $isSettingAction = $app->Secure->GetGET('module') === 'einstellungen'
          || $app->Secure->GetGET('smodule') === 'einstellungen';
        if($isSettingAction) {
          $maxrows = 10;
        }
        $heading = array('Bezeichnung', 'Typ', 'Projekt','Automatisch bezahlt','Verhalten wie','Modul', 'Men&uuml;');
        $width = array('40%', '20%', '20%','10%','10%','10%', '1%');
        $findcols = array('k.bezeichnung', 'k.type', 'p.abkuerzung',"if(k.automatischbezahlt,'Ja','')",'k.verhalten','k.modul', 'k.id');
        $searchsql = array('k.bezeichnung', 'k.type','p.abkuerzung','k.modul');
        $defaultorder = 2;
        $defaultorderdesc = 0;
        $menu = '<table cellpadding=0 cellspacing=0><tr><td nowrap>'
          . '<a href="index.php?module=zahlungsweisen&action=edit&id=%value%">'
          . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>";
        if(!$isSettingAction) {
          $menu .= '&nbsp;'
            . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=zahlungsweisen&action=delete&id=%value%\");>"
            . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";
        }
        $menu .= '</td></tr></table>';

        // SQL statement
        $sql = 'SELECT SQL_CALC_FOUND_ROWS k.id, '
          .$app->YUI->Stroke('!k.aktiv', 'k.bezeichnung').','
          .$app->YUI->Stroke('!k.aktiv', 'k.type')." as typ, 
        if(k.projekt > 0,p.abkuerzung,''), 
          if(k.automatischbezahlt,'Ja',''),k.verhalten,k.modul, k.id 
          FROM `zahlungsweisen` AS `k` 
          LEFT JOIN `projekt` AS `p` ON p.id=k.projekt";
        $where = ' k.geloescht!=1 '.$app->erp->ProjektRechte();
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
   * Zahlungsweisen constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    //parent::GenZahlungsweisen($app);
    $this->app=$app;
    $this->service = $this->app->Container->get('PaymentMethodService');
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ZahlungsweisenCreate");
    $this->app->ActionHandler("edit","ZahlungsweisenEdit");
    $this->app->ActionHandler("list","ZahlungsweisenList");
    $this->app->ActionHandler("delete","ZahlungsweisenDelete");

    $this->app->ActionHandlerListen($app);
  }

  /**
   * @return array
   */
  public function getBetaPaymentModules(): array
  {
    /** @var Appstore $appStore */
    $appStore = $this->app->erp->LoadModul('appstore');

    return $appStore->getBetaModulesByPrefix('zahlungsweise_');
  }


  /**
   * @param string $val
   *
   * @return array|null
   */
  function getApps($val = ''): ?array
  {
    $val = (string)$val;
    /** @var Appstore $appstore */
    $appstore = $this->app->loadModule('appstore');
    $module = $appstore->getAppsListWithPrefix('zahlungsweise_');
    $modularr = $this->ZahlungsweisenSelModul('', true);
    if($module) {
      if(isset($module['installiert'])){
        foreach($module['installiert'] as $k => $v) {
          $module['installiert'][$k]['match'] = $appstore->match($v['Bezeichnung'], $val);
          $module['installiert'][$k]['md5'] = md5($v['Bezeichnung']);
          $found[] = $v['key'];
        }
      }
      if(isset($module['kauf'])){
        foreach($module['kauf'] as $k => $v) {
          $module['kauf'][$k]['match'] = $appstore->match($v['Bezeichnung'], $val);
          $module['kauf'][$k]['md5'] = md5($v['Bezeichnung']);
          $found[] = $v['key'];
        }
      }
    }
    if($modularr){
      foreach($modularr as $k => $v) {
        if(!isset($found) || !in_array('zahlungsweise_'.$k,$found)) {
          $found[] = 'zahlungsweise_'.$k;
          $module['installiert'][] = [
            'md5'=>md5($v),
            'Bezeichnung'=>$v,
            'key'=>'zahlungsweise_'.$k,
            'match'=>$appstore->match($v, $val),
            'Icon'=>'Icons_dunkel_18.gif',
          ];
        }
      }
    }
    if(isset($module['installiert']) && count($module['installiert']) > 0) {
      $sort = null;
      foreach($module['installiert'] as $k => $v)$sort[$k] = $v['Bezeichnung'];
      array_multisort($sort, SORT_ASC, $module['installiert']);
    }

    return $module;
  }

  /**
   * @return JsonResponse
   */
  public function HandleSearchAjaxAction(): JsonResponse
  {
    $module = $this->getApps($this->app->Secure->GetPOST('val'));
    $anzeigen = '';
    $ausblenden = '';
    if($module) {
      if(isset($module['installiert'])) {
        foreach($module['installiert'] as $k => $v) {
          if($v['match']) {
            if($anzeigen != '') {
              $anzeigen .= ';';
            }
            $anzeigen .= 'm'.md5($v['Bezeichnung']);
          }
          else{
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
          else{
            if($ausblenden != '') {
              $ausblenden .= ';';
            }
            $ausblenden .= 'm'.md5($v['Bezeichnung']);
          }
        }
      }
    }
    $data = [
      'anzeigen'   => $anzeigen,
      'ausblenden' => $ausblenden,
    ];

    return new JsonResponse($data);
  }


  /**
   * @var int $shippingMethodId
   *
   * @return JsonResponse
   */
  public function getVueShippingMethodSuccessPage($paymentMethodId): JsonResponse
  {
    $succespage = [
      'type' => 'defaultPage',
      'icon' => 'add-person-icon',
      'headline'=> 'Zahlungsweise angelegt',
      'subHeadline'=> 'Die Zahlungsweise wurde angelegt',
      'ctaButtons' => [
        [
          'title'  => 'Klasse',
          'action' => 'close',
          'link'   => 'index.php?module=zahlungsweisen&action=edit&id='.$paymentMethodId
        ]
      ]
    ];

    return new JsonResponse(
      ['page'=>$succespage]
    );
  }


  /**
   * @var string     $paymentModule
   * @var string     $paymentModuleName
   * @var bool       $returnPages
   * @var null|array $requiredForSubmit
   *
   * @return JsonResponse
   */
  public function getFeatureVuePage(
    $paymentModule, $paymentModuleName, $returnPages = false, $requiredForSubmit = null
  ): JsonResponse
  {
    $autoPayed = null;
    $autoPayedLiability = null;
    $behaviour = null;
    if($requiredForSubmit === null) {
      $requiredForSubmit = $this->app->Secure->POST;
    }
    if(!isset($requiredForSubmit['step'])) {
      $requiredForSubmit['step'] = 2;
    }
    if(empty($requiredForSubmit['id'])) {
      $requiredForSubmit['id'] = 'NEW';
    }
    if(isset($requiredForSubmit['autoPayed'])) {
      $autoPayed = (bool)$requiredForSubmit['autoPayed'];
    }
    if(isset($requiredForSubmit['autoPayedLiability'])) {
      $autoPayedLiability = (bool)$requiredForSubmit['autoPayedLiability'];
    }
    if(isset($requiredForSubmit['verhalten'])) {
      $behaviour = (string)$requiredForSubmit['verhalten'];
    }
    $page = [
      'type' => 'form',

      'submitType' => 'submit',
      'icon'=> 'password-icon',
      'headline' => $paymentModuleName,
      'subHeadline' => '',
      'submitUrl' => 'index.php?module=zahlungsweisen&action=create&cmd=saveAssistant&paymentmodule='.
        $paymentModule,
      'form' => $this->getFeatureForm($autoPayed, $autoPayedLiability, $behaviour),
      'ctaButtons' => [
        [
          'title' => 'Weiter',
          'type' => 'submit',
          'action' => 'submit',
        ],
      ]
    ];

    if($returnPages) {

      $page['dataRequiredForSubmit'] = $requiredForSubmit;
      return new JsonResponse(
        [
          'pages' => [$page],
        ]
      );
    }

    return new JsonResponse(
      [
        'page' => $page,
        'dataRequiredForSubmit' => $requiredForSubmit,
      ]
    );
  }


  /**
   * @var null|bool   $autoPayed
   * @var null|bool   $autoPayedLiability
   * @var null|string $behaviour
   *
   * @return array
   */
  public function getFeatureForm($autoPayed = null, $autoPayedLiability = null, $behaviour = null): array
  {
    $ret = [
      [
        'id' => 0,
        'name' => 'projectGroup',
        'inputs' => [
          [
            'label' => 'Projekt Filter',
            'type' => 'select',
            'name' => 'projekt',
            'validation' => false,
            'options' => $this->getVueProjects(),
          ],
        ],
      ],
    ];
    if($autoPayed === null) {
      $ret[] =  [
        'id' => 1,
        'name' => 'autoPayed',
        'inputs' => [
          [
            'label' => 'Autom. bezahlt (Rechnung)',
            'type' => 'checkbox',
            'name' => 'automatischbezahlt',
            'validation' => false,
          ],
        ],
      ];
    }
    if($autoPayedLiability === null){
      $ret[] = [
        'id' => 2,
        'name' => 'autoPayedLiabilityGroup',
        'inputs' => [
          [
            'label' => 'Autom. bezahlt (Verbindlichkeit)',
            'type' => 'checkbox',
            'name' => 'automatischbezahltverbindlichkeit',
            'validation' => false,
          ],
        ],
      ];
    }
    if($behaviour === null){
      $ret[] = [
        'id' => 3,
        'name' => 'behaviourGroup',
        'inputs' => [
          [
            'label' => 'Verhalten',
            'type' => 'select',
            'name' => 'verhalten',
            'validation' => false,
            'options' => $this->convertArrayToVueOptions($this->getBehaviourOptions()),
          ],
        ],
      ];
    }

    return $ret;
  }


  /**
   * @return array
   */
  public function getVueProjects(): array
  {
    $projects = array_merge(
      [0=>''],
      $this->app->DB->SelectPairs(
        sprintf(
          'SELECT p.id, p.abkuerzung 
          FROM `projekt` AS `p` 
          WHERE p.geloescht = 0 %s 
          ORDER BY p.abkuerzung',
          $this->app->erp->ProjektRechte()
        )
      )
    );

    return $this->convertArrayToVueOptions($projects);
  }

  /**
   * @return JsonResponse
   */
  public function HandleGetAssistantAjaxAction(): JsonResponse
  {
    $module = $this->app->Secure->GetPOST('paymentmodule');
    $postData = $this->app->Secure->POST;
    $obj = $this->loadModule($module);
    if($obj === null) {
      return new JsonResponse(['location' => 'index.php?module=zahlungsweisen&action=create&auswahl='.$module]);
    }
    if($obj !== null && method_exists($obj, 'updatePostDataForAssistent')) {
      $postData = $obj->updatePostDataForAssistent($postData);
    }
    $paymentModuleName = $module;
    if(strpos($paymentModuleName, 'zahlungsweise_') === 0) {
      $paymentModuleName = substr($paymentModuleName, 14);
    }
    $paymentModuleName = str_replace('_', ' ', ucfirst($paymentModuleName));
    if($obj !== null && method_exists($obj, 'GetBezeichnung')) {
      $paymentModuleName = $obj->GetBezeichnung();
    }
    if(!method_exists($obj, 'getCreateForm')) {
      return $this->getFeatureVuePage($module, $paymentModuleName, true, $postData);
    }

    $form = $obj->getCreateForm();

    if(!empty($form)) {
      $form[count($form) - 1]['link'] = [
        'link' => 'index.php?module=zahlungsweisen&action=create&auswahl=' . $module,
        'title' => 'Expertenmodus',
      ];
    }
    $page = [
      'type' => 'form',
      'dataRequiredForSubmit' =>
        [
          'paymentmodule' => $module,
          'id' => 'NEW',
        ],
      'submitType' => 'submit',
      'icon'=> 'password-icon',
      'headline' => ucfirst(substr($module, 14)),
      'subHeadline' => method_exists($obj, 'getClickByClickHeadline')
        ?$obj->getClickByClickHeadline():'Bitte Zugangsdaten eingeben',
      'submitUrl' => 'index.php?module=zahlungsweisen&action=create&cmd=saveAssistant&paymentmodule='.$module,
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

  /**
   * @return JsonResponse
   */
  public function HandleSaveAssistantAjaxAction(): JsonResponse
  {
    $data = ['error' => 'Unknown error'];
    $paymentMethodId = $this->app->Secure->GetPOST('id','','',true);
    $step = (int)$this->app->Secure->GetPOST('step');
    if($paymentMethodId === 'NEW') {
      $postData = $this->app->Secure->POST;

      $paymentModule = $this->app->Secure->GetPOST('paymentmodule');
      /** @var Zahlungsweisenmodul $obj */
      $obj = $this->loadModule($paymentModule);
      if(empty($paymentModule)) {
        $paymentModule = $this->app->Secure->GetGET('paymentmodule');
      }
      if($obj !== null && method_exists($obj, 'AuthByAssistent')){
        $json = $obj->AuthByAssistent();
        if($json instanceof JsonResponse){
          return $json;
        }
      }
      if($obj !== null && method_exists($obj, 'updatePostDataForAssistent')) {
        $postData = $obj->updatePostDataForAssistent($postData);
      }
      if($step < 2) {
        $paymentModuleName = $paymentModule;
        if(strpos($paymentModuleName, 'zahlungsweise_') === 0) {
          $paymentModuleName = substr($paymentModuleName, 14);
        }
        $paymentModuleName = str_replace('_', ' ', ucfirst($paymentModuleName));
        if($obj !== null && method_exists($obj, 'GetBezeichnung')) {
          $paymentModuleName = $obj->GetBezeichnung();
        }

        return $this->getFeatureVuePage($paymentModule, $paymentModuleName, false, $postData);
      }

      $createPaymentMethodResult = $this->createPaymentMethodFromModuleName(
        $paymentModule,
        (int)$this->app->Secure->GetPOST('projekt'),
        $this->app->Secure->GetPOST('verhalten'),
        !empty($this->app->Secure->GetPOST('automatischbezahlt')),
        !empty($this->app->Secure->GetPOST('automatischbezahltverbindlichkeit')),
        ''
      );
      if(!empty($createPaymentMethodResult['id'])) {
        $paymentMethodId = $createPaymentMethodResult['id'];
        if(method_exists($obj, 'getStructureDataForClickByClickSave')) {
          $structureData = $obj->getStructureDataForClickByClickSave();
          if(!empty($structureData)) {
            $this->saveCreateData($paymentMethodId, $structureData);
          }
        }

        $this->saveCreateData($paymentMethodId);
      }
      elseif(!empty($createShippingResult['error'])) {
        return new JsonResponse($createShippingResult, JsonResponse::HTTP_BAD_REQUEST);
      }
    }

    if((int)$paymentMethodId > 0) {

      return $this->getVueShippingMethodSuccessPage((int)$paymentMethodId);
    }

    return new JsonResponse($data, JsonResponse::HTTP_BAD_REQUEST);
  }


  /**
   * @param int        $paymentMethodId
   * @param null|array $post
   */
  public function saveCreateData($paymentMethodId, $post = null): void
  {
    $paymentMethodRow = $this->app->DB->SelectRow(
      sprintf('SELECT * FROM `zahlungsweisen` WHERE `id` = %d', $paymentMethodId)
    );
    $paymentMethod = PaymentMethodData::fromDbState($paymentMethodRow);
    /** @var Zahlungsweisenmodul $obj */
    $obj = $this->loadModule($paymentMethodRow['modul'], $paymentMethodId);
    if($obj === null) {
      return;
    }
    $form = [];
    if(method_exists($obj, 'getCreateForm')){
      $form = $obj->getCreateForm();
    }
    $data = $paymentMethod->getSettings();// json_decode($paymentMethod['einstellungen_json'], true);
    /*if(empty($data) || !is_array($data)) {
      $data = [];
    }*/

    if($post === null && !empty($form)) {
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
      $data[$key] = $value;
    }

    $paymentMethod->setSettings($data);
    $this->service->update($paymentMethod);
  }

  /**
   * @param string $paymentMethodModule
   *
   * @return array
   */
  public function createPaymentMethodFromModuleName(
    $paymentMethodModule,
    $projectId,
    $paymentBehavior,
    $autoPayed,
    $autoPayedLiability,
    $text
  ): array
  {
    if(!$this->app->erp->ModulVorhanden($paymentMethodModule)) {
      return [
        'success'=>false,
        'error'=>'Modul nicht vorhanden'
      ];
    }
    $modules = $this->getApps();
    $modul = substr($paymentMethodModule,14);
    $name = ucfirst($modul);
    if($modules['installiert']) {
      foreach($modules['installiert'] as $key => $installedModule) {
        if($installedModule['key'] === $paymentMethodModule && $installedModule['Bezeichnung'] != '') {
          $name = $installedModule['Bezeichnung'];
        }
      }
    }
    $type = $modul;
    $i = 1;
    $originalName = $name;
    while(
    $this->app->DB->Select(
      sprintf(
        "SELECT `id` FROM `zahlungsweisen` WHERE `type` = '%s' OR `bezeichnung` = '%s' LIMIT 1",
        $this->app->DB->real_escape_string($type), $this->app->DB->real_escape_string($name)
      )
    )
    ) {
      $i++;
      $type = $modul.'_'.$i;
      $name = $originalName.' '.$i;
    }
    try {
      $paymentMethod = new PaymentMethodData(
        0,
        $modul,
        $name,
        $type,
        true,
        (int)$projectId,
        (string)$paymentBehavior,
        (bool)$autoPayed,
        (bool)$autoPayedLiability,
        (string)$text
      );
      $id = $this->service->create($paymentMethod);
    }
    catch(Exception $e) {
      $id = null;
    }

    return ['id'=>$id];
  }

  /**
   * @param string $module
   * @param int    $moduleId
   *
   * @return mixed|null
   */
  public function loadModule($module, $moduleId = 0)
  {
    if(empty($module)) {
      return null;
    }
    if(strpos($module,'zahlungsweise_') === 0) {
      $module = substr($module, 14);
      if(empty($module)) {
        return null;
      }
    }
    if(strpos($module, '.') !== false || strpos($module, '/') !== false || strpos($module, '\\')) {
      return null;
    }
    $path = dirname(__DIR__).'/lib/zahlungsweisen/'.$module.'.php';
    if(!is_file($path)) {
      return null;
    }

    include_once $path ;
    $classname = 'Zahlungsweise_'.$module;
    if(!class_exists($classname)) {
      return null;
    }

    return new $classname($this->app, $moduleId);
  }

  /**
   * @return JsonResponse|void
   */
  public function ZahlungsweisenCreate()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'suche') {
      return $this->HandleSearchAjaxAction();
    }
    if($cmd === 'getAssistant') {
      return $this->HandleGetAssistantAjaxAction();
    }
    if($cmd === 'saveAssistant') {
      return $this->HandleSaveAssistantAjaxAction();
    }
    $module = $this->getApps($this->app->Secure->GetPOST('val'));
    
    if($this->app->Secure->GetGET('auswahl')) {
      //$bezeichnung = $this->app->Secure->GetPOST('bezeichnung');
      $auswahlmodul = $this->app->Secure->GetGET('auswahl');
      if($auswahlmodul === 'custom') {
        $leerVorhanden = $this->app->DB->Select("SELECT `id` FROM `zahlungsweisen` WHERE `bezeichnung` = '' LIMIT 1");
        if($leerVorhanden != '' && $leerVorhanden > 0){
          $this->app->Location->execute('index.php?module=zahlungsweisen&action=edit&id='.$leerVorhanden);
        }
        $paymentMethod = new PaymentMethodData(0, '', 'Custom', 'custom', true, 0, '', false, false, '');
        $id = $this->service->create($paymentMethod);
        /*$this->app->DB->Insert(
          "INSERT INTO 
            `zahlungsweisen` (`bezeichnung`, `type`,`aktiv`, `geloescht`, `modul`) 
            VALUES ('','',1,0,'')"
        );
        $id = $this->app->DB->GetInsertID();*/
        $this->app->Location->execute('index.php?module=zahlungsweisen&action=edit&id='.$id);
      }
      if($this->app->erp->ModulVorhanden($auswahlmodul)) {
        $modul = substr($auswahlmodul,14);
        $bezeichnung = ucfirst($modul);
        if($module['installiert']) {
          foreach($module['installiert'] as $k => $v) {
            if($v['key'] == $auswahlmodul && $v['Bezeichnung'] != '') {
              $bezeichnung = $v['Bezeichnung'];
            }
          }
        }
        $type = $modul;
        $i = 1;
        $_bezeichnung = $bezeichnung;
        while($this->app->DB->Select(
          sprintf(
            "SELECT `id` 
            FROM `zahlungsweisen` 
            WHERE `type` = '%s' 
            OR `bezeichnung` = '%s' 
            LIMIT 1",
            $this->app->DB->real_escape_string($type), $this->app->DB->real_escape_string($bezeichnung)
          )
        ))  {
          $i++;
          $type = $modul.'_'.$i;
          $bezeichnung = $_bezeichnung.' '.$i;
        }

        $paymentMethod = new PaymentMethodData(
          0,
          $modul,
          $bezeichnung,
          $type,
          true,
          0,
          '',
          false,
          false,
          ''
        );
        $id = $this->service->create($paymentMethod);

        $this->app->erp->RunHook('zahlungsweisen_create', 1, $id);
        $this->app->Location->execute('index.php?module=zahlungsweisen&action=edit&id='.$id);
      }
    }

    if($this->app->erp->isIoncube() && $this->app->Secure->GetPOST('testen')) {
      $modul = $this->app->Secure->GetPOST('modul');
      if($modul) {
        $testapp = $modul;
        if(is_file(dirname(__DIR__).'/update.php')) {
          $result = '';
          include_once dirname(__DIR__).'/update.php';
          if($result === 'OK') {
            $this->app->Tpl->Add(
              'MESSAGE',
              '<div class="info">Das Modul wurde zum Testen angefragt. Bitte update xentral in 
                fr&uuml;hestens 10 Minuten um das Modul zu laden</div>'
            );
          }
          else{
            $this->app->Tpl->Add(
              'MESSAGE',
              '<div class="error">Es ist ein Fehler beim Updaten aufgetreten: '.$result.'</div>');
          }
        }
      }
    }
    else{
      $get = $this->app->Secure->GetGET('get');
      if($get && $module){
        if(isset($module['kauf'])){
          foreach($module['kauf'] as $k => $v) {
            if($v['md5'] == $get){
              $mods = $this->app->erp->getAppList();
              foreach($mods as $k2 => $v2) {
                if(md5($v2['Bezeichnung']) == $get){
                  $this->app->Tpl->Add(
                    'MESSAGE',
                    '<div class="info">Bitte best&auml;tigen: <form method="POST" action="index.php?module=versandarten&action=create"><input type="hidden" name="modul" value="'.$k2.'" /><input type="submit" style="float:right;" value="Testmodul '.$v2['Bezeichnung'].' anfragen" name="testen" /></form></div>'
                  );
                  break;
                }
              }
            }
          }
        }
      }
    }
    
    $modullist = $this->getApps();
    /** @var Appstore $appstore */
    $appstore = $this->app->loadModule('appstore');
    $appstore->AddModuleHtml(
      $modullist, 'zahlungsweise_', 'index.php?module=zahlungsweisen&action=create&get=',
      [
        'title' => 'Custom',
        'link' => 'index.php?module=zahlungsweisen&action=create&auswahl=custom',
      ]
    );

    $this->app->ModuleScriptCache->IncludeWidgetNew('ClickByClickAssistant');
    $this->app->Tpl->Parse('PAGE', 'zahlungsweisen_neu.tpl');
  }

  public function ZahlungsweisenList(): void
  {
    $this->ZahlungsweisenMenu();
    $this->app->YUI->TableSearch('TAB1','zahlungsweisen_list', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function ZahlungsweisenMenu(): void
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->MenuEintrag('index.php?module=zahlungsweisen&action=create','Neue Zahlungsweise anlegen');
    if($this->app->Secure->GetGET('action')==='list') {
      $this->app->erp->MenuEintrag('index.php?module=zahlungsweisen&action=list','&Uuml;bersicht');
    }
    elseif($this->app->Secure->GetGET('action')==='edit'){
      $this->app->erp->MenuEintrag("index.php?module=zahlungsweisen&action=edit&id=$id", 'Details');
    }
    else{
      $this->app->erp->MenuEintrag('index.php?module=zahlungsweisen&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
    }
  }

  /**
   * @var bool $escape
   *
   * @return array
   */
  public function GetInput($escape = true): array
  {
    $input = [];
    $escapeString = $escape?'1':'';
    $input['bezeichnung'] = $this->app->Secure->GetPOST('bezeichnung', null, '', $escapeString);
    $input['type'] = $this->app->Secure->GetPOST('type', null, '', $escapeString);
    $input['projekt'] = $this->app->Secure->GetPOST('projekt', null, '', $escapeString);
    $input['selmodul'] = $this->app->Secure->GetPOST('selmodul', null, '', $escapeString);
    $input['aktiv'] = (int)$this->app->Secure->GetPOST('aktiv');
    $input['automatischbezahlt'] = (int)$this->app->Secure->GetPOST('automatischbezahlt');
    $input['automatischbezahltverbindlichkeit'] = (int)$this->app->Secure->GetPOST('automatischbezahltverbindlichkeit');
    $input['verhalten'] = $this->app->Secure->GetPOST('verhalten', null, '', $escapeString);
    $input['modul'] = $this->app->Secure->GetPOST('selmodul', null, '', $escapeString);
    $input['freitext'] = $this->app->Secure->GetPOST('freitext', null, '', $escapeString);

    return $input;
  }

  /**
   * @param array $input
   */
  public function SetInput($input): void
  {
    $this->app->Tpl->Set('BEZEICHNUNG', $input['bezeichnung']);
    $this->app->Tpl->Set('TYPE', $input['type']);
    $this->app->Tpl->Set('FREITEXT', $input['freitext']);
    $this->app->Tpl->Set('PROJEKT', $input['projekt']);
    $this->app->Tpl->Set('SELVERHALTEN', $this->VerhaltenSel($input['verhalten']));
    if($input['aktiv']==1){
      $this->app->Tpl->Set('AKTIV', 'checked');
    }
    if($input['automatischbezahlt']==1){
      $this->app->Tpl->Set('AUTOMATISCHBEZAHLT', 'checked');
    }
    if($input['automatischbezahltverbindlichkeit']==1){
      $this->app->Tpl->Set('AUTOMATISCHBEZAHLTVERBINDLICHKEIT', 'checked');
    }
  }

  /**
   * @return string[]
   */
  public function getBehaviourOptions(): array
  {
    return [
      'rechnung'    => 'Rechnung',
      'vorkasse'    => 'Vorkasse',
      'lastschrift' => 'Lastschrift',
    ];
  }

  /**
   * @param array $array
   *
   * @return array
   */
  public function convertArrayToVueOptions($array): array
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
   * @param null|string $value
   *
   * @return string
   */
  public function VerhaltenSel($value = null): string
  {
    $verhalten = $this->getBehaviourOptions();

    $ret = '<option value=""></option>';
    foreach($verhalten as $k=> $v) {
      $ret .= '<option value="'.$k.'" '.(($value == $k)?' selected="selected" ':'').'>'.$v.'</option>';
    }

    return $ret;
  }
  
  public function ZahlungsweisenEdit()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $speichern = $this->app->Secure->GetPOST('speichern');
    $input = $this->GetInput(false);

    $error = '';
    if(is_numeric($id) && $speichern != ''){
      if($id) {
        $error = '';
        if(trim($input['bezeichnung']) == '') {
          $error = 'Bitte alle Pflichtfelder ausfüllen!';
        }
        if(trim($input['type']) == '') {
          $error = 'Bitte alle Pflichtfelder ausfüllen!';
        }
        $modus = 'save';
        if($id) {
          $this->app->erp->RunHook('zahlungsweisen_edit_show', 3, $modus, $id, $error);
        }
        if($error != ''){
          $this->app->Tpl->Set('MESSAGE', "<div class\"error\">$error</div>");
        }
        else{
          if($error == ''){
        
            $projektid = $this->app->DB->Select(
              sprintf(
                "SELECT `id` FROM `projekt` WHERE `abkuerzung` = '%s' LIMIT 1",
                $input['projekt']
              )
            );

            $oldtype = $this->app->DB->Select(sprintf('SELECT `type` FROM `zahlungsweisen` WHERE `id` = %d LIMIT 1', $id));
            if($oldtype != $input['type']){
              while(
                $this->app->DB->Select(
                  sprintf(
                    "SELECT `id` FROM `zahlungsweisen` WHERE `type` = '%s' AND `id` <> %d LIMIT 1",
                    $input['type'], $id
                  )
                )
              ) {
                $typa = explode('_', $input['type']);
                if(count($typa) == 1 || !is_numeric($typa[count($typa)-1]))
                {
                  $input['type'] .= '_1';
                }
                else{
                  $counter = $typa[count($typa)-1]+1;
                  unset($typa[count($typa)-1]);
                  $input['type'] = implode('_', $typa).'_'.$counter;
                }
              }
            }

            $paymentMethodRow = $this->service->get($id);
            $paymentMethodRow['bezeichnung'] = $input['bezeichnung'];
            $paymentMethodRow['type'] = $input['type'];
            $paymentMethodRow['verhalten'] = $input['verhalten'];
            $paymentMethodRow['id'] = $id;
            $paymentMethod = PaymentMethodData::fromDbState($paymentMethodRow);
            $paymentMethod->setName($input['bezeichnung']);
            $paymentMethod->setType($input['type']);
            $paymentMethod->setProjectId((int)$projektid);
            $paymentMethod->setActive((bool)$input['aktiv']);
            $paymentMethod->setModule($input['selmodul']);
            $paymentMethod->setText($input['freitext']);
            $paymentMethod->setAutoPayed((bool)$input['automatischbezahlt']);
            $paymentMethod->setAutoPayedLiability((bool)$input['automatischbezahltverbindlichkeit']);
            $paymentMethod->setPaymentBehavior($input['verhalten']);
            $this->service->update($paymentMethod);

            if($input['aktiv'] == 1){
              $this->app->Tpl->Set('AKTIV', "checked");
            }
            if($input['automatischbezahlt'] == 1){
              $this->app->Tpl->Set('AUTOMATISCHBEZAHLT', "checked");
            }
            if($input['automatischbezahltverbindlichkeit'] == 1){
              $this->app->Tpl->Set('AUTOMATISCHBEZAHLTVERBINDLICHKEIT', "checked");
            }
            $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Daten wurden erfolgreich gespeichert!</div>");
          }
        }
      }
      else{
        $error = '';

        if(trim($input['bezeichnung']) == ''){
          $error = "Bitte alle Pflichtfelder ausfüllen!";
          $this->app->Tpl->Set('MSGBEZEICHNUNG',' <span style="color:red">Pflichtfeld!</span>');
        }
        if(trim($input['type']) == ''){
          $error = "Bitte alle Pflichtfelder ausfüllen!";
          $this->app->Tpl->Set('MSGTYP',' <span style="color:red">Pflichtfeld!</span>');
        }

        if($error!=''){
          $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">$error</div>");
        }
        else {

          if(trim($input['projekt']) == ''){
            $projektid = 0;
          }
          else{
            $projektid = $this->app->DB->Select(
              sprintf(
                "SELECT `id` FROM `projekt` WHERE `abkuerzung` = '%s' LIMIT 1",
                $input['projekt']
              )
            );
          }
          
          while(
            $this->app->DB->Select(
              sprintf(
                "SELECT `id` FROM `zahlungsweisen` WHERE `type` = '%s' LIMIT 1",
                $input['type']
              )
            )
          ) {
            $typa = explode('_', $input['type']);
            if(count($typa) == 1 || !is_numeric($typa[count($typa)-1])) {
              $input['type'] .= '_1';
            }
            else{
              $counter = $typa[count($typa)-1]+1;
              unset($typa[count($typa)-1]);
              $input['type'] = implode('_', $typa).'_'.$counter;
            }
          }
          $paymentMethod = new PaymentMethodData(
            0,
            $input['modul'],
            $input['bezeichnung'],
            $input['type'],
            (bool)$input['aktiv'],
            (int)$projektid,
            (string)$input['verhalten'],
            (bool)$input['automatischbezahlt'],
            (bool)$input['automatischbezahltverbindlichkeit'],
            (string)$input['freitext']
          );
          $newid = $this->service->create($paymentMethod);

          $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Die Daten wurden erfolgreich gespeichert!</div>");
          $this->app->Location->execute("index.php?module=zahlungsweisen&action=edit&id=$newid&msg=$msg");
        }
      }
    }
    try{
      $paymentMethod = $this->service->getFromId($id);
      $daten = $paymentMethod->toArray();
    }
    catch(Exception $e) {
      $daten = null;
    }

    if(!empty($daten)) {
      $this->app->erp->Headlines('',$daten['bezeichnung']);
      $this->app->Tpl->Set('AKTMODUL', $daten['modul']);
      $pfad = dirname(__DIR__).'/lib/zahlungsweisen/'.$daten['modul'].'.php';
      if($daten['modul'] && is_file($pfad)) {
        include_once $pfad;
        $classname = 'Zahlungsweise_'.$daten['modul'];
        if(class_exists($classname)) {
          $obj = new $classname($this->app, $daten['id']);
        }
      }
      $bezeichnung = $daten['bezeichnung'];
      $type = $daten['type'];
      $projekt = $daten['projekt'];
      $aktiv = $daten['aktiv'];
      $automatischbezahlt = $daten['automatischbezahlt'];
      $automatischbezahltverbindlichkeit = $daten['automatischbezahltverbindlichkeit'];
      $vorkasse = $daten['vorkasse'];
      $freitext = $daten['freitext'];
      $verhalten = $daten['verhalten'];
      $projektname = $this->app->DB->Select(
        sprintf(
          'SELECT `abkuerzung` FROM `projekt` WHERE `id` = %d',
          $projekt
        )
      );
      if(isset($obj) && method_exists($obj, 'Einstellungen')) {
        $obj->Einstellungen('JSON');
      }
    }
    else{
      $this->app->Tpl->Set('AKTMODUL','');
      $verhalten = 'vorkasse';
    }

    if($error == '') {
      $selModul = isset($daten['modul'])?$daten['modul']:'';
    }
    else {
      $selModul = $input['selmodul'];
    }

    $this->app->Tpl->Set(
      'SELMODUL',
      $this->ZahlungsweisenSelModul($selModul)
    );

    $this->app->Tpl->Set('BEZEICHNUNG', $error == ''?$bezeichnung:$input['bezeichnung']);
    $this->app->Tpl->Set('FREITEXT', $error == ''?$freitext:$input['freitext']);
    $this->app->Tpl->Set('TYPE', $error == ''?$type:$input['type']);
    $this->app->Tpl->Set('PROJEKT', $error == ''?$projektname:$input['projekt']);

    $this->app->Tpl->Set('SELVERHALTEN', $this->VerhaltenSel($error == ''?$verhalten:$input['verhalten']));
    if(($error == '' && $aktiv == 1) || ($error != '' && $input['aktiv'])){
      $this->app->Tpl->Set('AKTIV', 'checked');
    }
    if(($error == '' && $automatischbezahlt == 1) ||  ($error != '' && $input['automatischbezahlt'])){
      $this->app->Tpl->Set('AUTOMATISCHBEZAHLT', 'checked');
    }
    if(($error == '' && $automatischbezahltverbindlichkeit == 1)
      ||  ($error != '' && $input['automatischbezahltverbindlichkeit'])){
      $this->app->Tpl->Set('AUTOMATISCHBEZAHLTVERBINDLICHKEIT', 'checked');
    }
    
    $modus = 'show';
    $error = '';
    if($id) {
      $this->app->erp->RunHook('zahlungsweisen_edit_show', 3, $modus, $id, $error);
    }
    if(!empty($daten['modul'])) {
      $beta = $this->getBetaPaymentModules();
      if(in_array('zahlungsweise_'.$daten['modul'], $beta)) {
        $this->app->Tpl->Add('MESSAGE','<div class="info">Dieses Modul ist noch im Beta Stadium.</div>');
        /** @var Appstore $appstore */
        $appstore = $this->app->erp->LoadModul('appstore');
        if($appstore !== null){
          $appstore->addBetaToHeadline();
        }
      }
    }
    $this->ZahlungsweisenMenu();
    $this->app->YUI->CkEditor('freitext', 'belege');
    $this->app->YUI->AutoComplete('projekt', 'projektname', 1);
    $this->app->Tpl->Parse('PAGE', 'zahlungsweisen_edit.tpl');
  }

  /**
   * @param string $value
   * @param bool   $retarr
   *
   * @return array|string|null
   */
  public function ZahlungsweisenSelModul($value = '', $retarr = false)
  {
    $array = null;
    $ret = '<option value=""></option>';
    $pfad = dirname(__DIR__).'/lib/zahlungsweisen';
    $beta = $this->getBetaPaymentModules();
    if(is_dir($pfad)) {
      $handle = opendir($pfad);
      if($handle) {
        while (false !== ($file = readdir($handle))) {
          $files[] = $file;
        }
        natcasesort($files);
        foreach($files as $file) {
          if($file[0] !== '.' && substr($file,-4) === '.php' && substr($file,-8) !== '.src.php'
            && is_file($pfad.'/'.$file)) {
            $modul = str_replace('.php','',$file);
            $modulKey = $modul;
            if(strpos($modulKey,'zahlungsweise_') !== 0) {
              $modulKey = 'zahlungsweise_'.$modul;
            }
            $ret .= '<option value="'.$modul.'"'.($value == $modul?' selected="selected" ':'').'>'.
              ucfirst($modul)
              .(in_array($modulKey, $beta)?' (Beta)':'')
              .'</option>';
            $array[$modul] = ucfirst($modul);
          }
        }
        closedir($handle);
      }
    }
    if($retarr) {
      return $array;
    }

    return $ret;
  }

  public function ZahlungsweisenDelete(): void
  {
    $id = (int)$this->app->Secure->GetGET('id');
    try{
      $this->service->delete($id);
      $msg = $this->app->erp->base64_url_encode(
        "<div class=\"info\">{|Die Zahlungsweise wurde gel&ouml;scht!|}</div>"
      );
    }
    catch(Exception $e) {
      $msg = $this->app->erp->base64_url_encode(
        '<div class="error">'.$e->getMessage().'</div>'
      );
    }
    $this->app->Location->execute('index.php?module=zahlungsweisen&action=list&msg='.$msg);
  }
}
