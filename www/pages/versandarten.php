<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 * SPDX-FileCopyrightText: 2019 Xentral (c) Xentral ERP Software GmbH, Fuggerstrasse 11, D-86150 Augsburg, Germany
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
* 
* Xentral (c) Xentral ERP Software GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
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
use Xentral\Components\Http\Response;
use Xentral\Widgets\ClickByClickAssistant\VueUtil;

class Versandarten {
  const MODULE_NAME = 'ShippingMethod';

  var ApplicationCore $app;
  /** @var string[] $stylesheet */
  public array $stylesheet = [
    './classes/Modules/Appstore/www/css/tilegrid.css',
  ];

  /** @var string[] $javascript */
  public array $javascript = [
    './classes/Modules/ShippingMethod/www/js/shipping_method_create.js',
  ];

  /**
   * Versandarten constructor.
   *
   * @param ApplicationCore $app
   * @param bool        $intern
   */
  public function __construct(ApplicationCore $app, bool $intern = false)
  {
    $this->app=$app;
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);

    // ab hier alle Action Handler definieren die das Modul hat
    $this->app->ActionHandler("create", "VersandartenCreate");
    $this->app->ActionHandler("neusonstige", "VersandartenNeuSonstige");
    $this->app->ActionHandler("edit", "VersandartenEdit");
    $this->app->ActionHandler("list", "VersandartenList");
    $this->app->ActionHandler("delete", "VersandartenDelete");
    $this->app->ActionHandler("copy", 'VersandartenCopy');
    $this->app->ActionHandler('createShipment', 'CreateShipment');

    $this->app->ActionHandlerListen($app);
  }

  public function Install(): void
  {
    $this->app->erp->GetVersandartAuftrag();
  }

  /** @noinspection PhpUnused */
  public function VersandartenCopy():void
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $id = $this->app->DB->Select("SELECT `id` FROM `versandarten` WHERE `id` = $id LIMIT 1");
    if(!$id) {
      $this->app->Location->execute('index.php?module=versandarten&action=list');
    }
    $newId = $this->app->DB->MysqlCopyRow('versandarten', 'id', $id);
    if($newId) {
      $this->app->DB->Update(
          "UPDATE `versandarten` set `aktiv` = 0, `ausprojekt` = 0 WHERE `id` = $newId LIMIT 1"
      );
    }
    $this->app->Location->execute('index.php?module=versandarten&action=edit&id='.$newId);
  }

  /** @noinspection PhpUnused */
  public function VersandartenList(): void
  {
    $this->app->erp->MenuEintrag('index.php?module=versandarten&action=create','Neue Versandart anlegen');
    $this->app->erp->MenuEintrag('index.php?module=versandarten&action=list','&Uuml;bersicht');
    $this->app->YUI->TableSearch('TAB1','versandarten_list', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE','versandarten_list.tpl');
  }

  public static function TableSearch(Application $app, string $name, array $erlaubtevars): array
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name)  {
      case 'versandarten_list':
        $allowed['versandarten'] = array('list');
        $allowed['einstellungen'] = array('category');
        $isSettingAction = $app->Secure->GetGET('module') === 'einstellungen'
          || $app->Secure->GetGET('smodule') === 'einstellungen';
        if($isSettingAction) {
          $maxrows = 10;
        }
        $heading = array('Bezeichnung', 'Typ','Modul', 'Projekt', 'Men&uuml;');
        $width = array('39%', '20%', '20%','20%','5%');

        $findcols = array('v.bezeichnung', 'v.type','v.modul', "if(v.projekt, (SELECT `abkuerzung` FROM `projekt` WHERE `id` = v.projekt), '')",'v.id');
        $searchsql = array('v.bezeichnung', 'v.type','v.modul', 'v.projekt');

        $defaultorder = 1;
        $defaultorderdesc = 0;

        $menu = "<table style=\"border-collapse: collapse\"><tr><td nowrap>"
          ."<a href=\"index.php?module=versandarten&action=edit&id=%value%\">"
          ."<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" alt=\"edit\" style=\"border: 0\"></a>";
        $menu .= "&nbsp;"
          ."<a href=\"index.php?module=versandarten&action=copy&id=%value%\">"
          ."<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/copy.svg\" alt=\"copy\" style=\"border: 0\"></a>";
        if(!$isSettingAction) {
          $menu .= "&nbsp;"
            . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=versandarten&action=delete&id=%value%\")>"
            . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" alt=\"delete\" style=\"border: 0\"></a>";
        }
        $menu .= "</td></tr></table>";

        $where = " v.id > 0 ";

        $sql = "SELECT SQL_CALC_FOUND_ROWS v.id, if(v.aktiv, v.bezeichnung, CONCAT('<s>',v.bezeichnung,'</s>')), 
            if(v.aktiv, v.type, CONCAT('<s>',v.type,'</s>')), 
            if(v.aktiv, v.modul, CONCAT('<s>',v.modul,'</s>')), 
            if(v.projekt, (SELECT `abkuerzung` FROM `projekt` WHERE `id` = v.projekt), ''), v.id 
            FROM `versandarten` AS `v`";

        $count = "SELECT count(v.id) FROM `versandarten` AS `v` WHERE $where";
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

    public function VersandartenNeuSonstige() {
        $sql = "INSERT INTO versandarten (aktiv) VALUES (1)";
        $this->app->DB->Insert($sql);
        $id = $this->app->DB->GetInsertID();
        $this->app->Location->execute('index.php?module=versandarten&action=edit&id='.$id);
    }

  /** @noinspection PhpUnused */
  public function VersandartenEdit(): void
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $submit = $this->app->Secure->GetPOST('speichern');

    if (!$id)
      return;

    $this->app->erp->MenuEintrag('index.php?module=versandarten&action=edit&id='.$id,'Details');
    $this->app->erp->MenuEintrag('index.php?module=versandarten&action=list','Zur&uuml;ck zur &Uuml;bersicht');

    $error = [];
    if($submit != '') { // handle form submit
      $form = $this->GetInput();

/*      if ($obj === null)
        $error[] = sprintf('Versandart "%s" existiert nicht.', $form['selmodul']); */

      $obj = $this->loadModule($form['modul'], $id);
      $module_errors = $obj?->getErrors();
      foreach ($module_errors as $module_error) {
          $this->app->Tpl->addMessage('error', $module_error);
      }

      if(trim($form['bezeichnung']) == '')
        $error[] = 'Bitte eine Bezeichnung angeben!';

      if(trim($form['type']) == '')
        $error[] = 'Bitte einen Typ angeben!';

      $projektId = 0;
      if(!empty($form['projekt'])){
        $projektId = $this->app->DB->Select(
            "SELECT `id` FROM `projekt` WHERE `abkuerzung` = '{$form['projekt']}' LIMIT 1"
        );
      }

      if ($this->app->DB->Select(
          "SELECT `id` FROM `versandarten` WHERE `type` = '{$form['type']}' AND `id` <> $id LIMIT 1"
      ))
        $error[] = 'Typ ist bereits für eine andere Versandart vergeben';

      if ($obj !== null) {
        foreach ($obj->AdditionalSettings() as $k => $v) {
          $form[$k] = $this->app->Secure->GetPOST($k);
        }
        $error = array_merge($error, $obj->ValidateSettings($form));
        foreach ($obj->AdditionalSettings() as $k => $v) {
          $json[$k] = $form[$k];
        }
      }
      $json = json_encode($json ?? null);

      foreach ($error as $e) {
        $this->app->Tpl->addMessage('error', $e);
      }

      if (empty($error)) {
        $this->app->DB->Update(
            "UPDATE `versandarten`
                SET `bezeichnung`='{$form['bezeichnung']}', `type` ='{$form['type']}',
                    `projekt`=$projektId, `aktiv`={$form['aktiv']}, `modul`='{$form['modul']}',
                    `export_drucker` = {$form['export_drucker']}, 
                    `paketmarke_drucker` = {$form['paketmarke_drucker']}, 
                    `ausprojekt` = {$form['ausprojekt']}, `versandmail` = {$form['versandmail']},
                    `geschaeftsbrief_vorlage` = {$form['geschaeftsbrief_vorlage']},
                    `keinportocheck`={$form['keinportocheck']},
                    einstellungen_json='$json'
                WHERE `id` = $id LIMIT 1"
        );

        $this->app->Tpl->Set('MESSAGE', '');
        $this->app->Tpl->addMessage('success', "Die Daten wurden erfolgreich gespeichert!");
      }
    }
    $daten = $this->app->DB->SelectRow("SELECT * FROM `versandarten` WHERE `id` = $id LIMIT 1");
    if (empty($daten))
      $this->app->Location->execute('index.php?module=versandarten&action=list');

    $this->app->erp->Headlines('', $daten['bezeichnung']);
    $this->app->Tpl->Set('AKTMODUL', $daten['modul']);

    $obj = $this->loadModule($daten['modul'], $daten['id']);
    $module_errors = $obj?->getErrors();
    foreach ($module_errors as $module_error) {
        $this->app->Tpl->addMessage('error', $module_error);
    }

    if (empty($error) || !isset($form)) { //overwrite form data from database if no validation error is present
      $form = json_decode($daten['einstellungen_json'],true);
      $form['bezeichnung'] = $daten['bezeichnung'];
      $form['type'] = $daten['type'];
      $form['projekt'] = $this->app->erp->Projektdaten($daten['projekt'], 'abkuerzung');
      $form['aktiv'] = $daten['aktiv'];
      $form['keinportocheck'] = $daten['keinportocheck'];
      $form['modul'] = $daten['modul'];
      $form['export_drucker'] = $daten['export_drucker'];
      $form['paketmarke_drucker'] = $daten['paketmarke_drucker'];
    }

    $obj?->RenderAdditionalSettings('MODULESETTINGS', $form);

    $this->app->Tpl->addSelect('EXPORT_DRUCKER', 'export_drucker', 'export_drucker',
        $this->getPrinterByModule($obj, false), $form['export_drucker']);

    $this->app->Tpl->addSelect('PAKETMARKE_DRUCKER', 'paketmarke_drucker', 'paketmarke_drucker',
        $this->getPrinterByModule($obj), $form['paketmarke_drucker']);

    $this->app->YUI->HideFormular('versandmail', array('0'=>'versandbetreff','1'=>'dummy'));
    $this->app->Tpl->addSelect('SELVERSANDMAIL', 'versandmail', 'versandmail', [
        0 => 'Standardverhalten',
        -1 => 'Keine Versandmail',
        1 => 'Eigene Textvorlage'
    ], $daten['versandmail']);

    $geschaeftsbrief_vorlagen = $this->app->DB->SelectPairs(
      "SELECT gv.id, CONCAT_WS(' - ', gv.subjekt, p.abkuerzung) as val 
      FROM `geschaeftsbrief_vorlagen` AS `gv` 
      LEFT JOIN `projekt` AS `p` ON gv.projekt = p.id 
      ORDER by gv.subjekt"
    );
    $this->app->Tpl->addSelect('SELGESCHAEFTSBRIEF_VORLAGE', 'geschaeftsbrief_vorlage',
        'geschaeftsbrief_vorlage', $geschaeftsbrief_vorlagen, $daten['geschaeftsbrief_vorlage']);

    $this->app->Tpl->addSelect('SELMODUL', 'modul', 'modul',
        $this->VersandartenSelModul(true), $form['modul']);
    $this->app->Tpl->Set('BEZEICHNUNG', $form['bezeichnung']);
    $this->app->Tpl->Set('TYPE', $form['type']);
    $this->app->Tpl->Set('PROJEKT', $form['projekt']);
    $this->app->YUI->AutoComplete('projekt', 'projektname', 1);
    if($form['aktiv']) $this->app->Tpl->Set('AKTIV', 'checked');
    if($form['keinportocheck']) $this->app->Tpl->Set('KEINPORTOCHECK', 'checked');
    if ($obj->Beta ?? false)
      $this->app->Tpl->addMessage('warning','Dieses Modul ist noch im Beta Stadium');
    $this->app->Tpl->Parse('PAGE', 'versandarten_edit.tpl');
  }

  protected function getPrinterByModule(?Versanddienstleister $obj, bool $includeLabelPrinter = true): array
  {
    $printer = $this->app->erp->GetDrucker();

    if ($includeLabelPrinter && $obj?->isEtikettenDrucker()) {
      $labelPrinter = $this->app->erp->GetEtikettendrucker();
      $printer = array_merge($printer ?? [], $labelPrinter ?? []);
    }
    natcasesort($printer);
    return $printer;
  }

  /**
   * @param string $shippingModule
   *
   * @return array
   */
  protected function getVuePrinterOptions(string $shippingModule = ''): array
  {
    try{
      $obj = $this->loadModule($shippingModule);
      return VueUtil::keyValueArrayToVueOptions($this->getPrinterByModule($obj));
    }
    catch(Exception) {
      return [];
    }
  }

  public function getVueExportPrinterOptions(): array
  {
    $printer = $this->app->erp->GetDrucker();
    try{
      return VueUtil::keyValueArrayToVueOptions($printer ?? []);
    }
    catch(Exception) {
      return [];
    }
  }

  public function getVueProjects(): array
  {
    $projects = array_merge(
      [ 0 => '', ],
      $this->app->DB->SelectPairs(
          "SELECT p.id, p.abkuerzung 
          FROM `projekt` AS `p` 
          WHERE p.geloescht = 0 {$this->app->erp->ProjektRechte()} 
          ORDER BY p.abkuerzung"
      )
    );

    try{
      return VueUtil::keyValueArrayToVueOptions($projects);
    }
    catch(Exception) {
      return [];
    }
  }



  /**
   * @var string     $shippingModule
   * @var string     $shippingModuleName
   * @var null|array $requiredForSubmit
   *
   * @return JsonResponse
   */
  public function getStep2Page(string $shippingModule, string $shippingModuleName, ?array $requiredForSubmit = null): JsonResponse
  {
    if($requiredForSubmit === null) {
      $requiredForSubmit = $this->app->Secure->POST;
      $requiredForSubmit['step'] = 2;
    }
    $page = [
      'type' => 'form',
      'submitType' => 'submit',
      'icon'=> 'password-icon',
      'headline' => $shippingModuleName,
      'subHeadline' => 'Welche Drucker möchtest Du nutzen?',
      'submitUrl' => 'index.php?module=versandarten&action=create&cmd=saveAssistant&shippingmodule='.
        $shippingModule,
      'form' => $this->getFeatureForm($shippingModule),
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

  public function getFeatureForm(string $shippingModule): array
  {
    return [
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
      [
        'id' => 1,
        'name' => 'printerGroup',
        'inputs' => [
          [
            'label' => 'Drucker',
            'type' => 'select',
            'name' => 'paketmarke_drucker',
            'validation' => false,
            'options' => $this->getVuePrinterOptions($shippingModule),
          ],
        ],
      ],
      [
        'id' => 2,
        'name' => 'exportprinterGroup',
        'inputs' => [
          [
            'label' => 'Export-Drucker',
            'type' => 'select',
            'name' => 'export_drucker',
            'validation' => false,
            'options' => $this->getVueExportPrinterOptions(),
          ],
        ],
      ],
    ];
  }

  /** @noinspection PhpUnused */
  public function VersandartenDelete(): void
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $this->app->DB->Delete("DELETE FROM `versandarten` WHERE `id` = $id LIMIT 1");
    $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Die Versandart wurde gelöscht!</div>");
    $this->app->Location->execute("index.php?module=versandarten&action=list&msg=$msg");
  }

  public function getApps(): ?array
  {
    $module = [];
    $modularr = $this->VersandartenSelModul();
    foreach($modularr as $k => $v)  {
      $module['installiert'][$k] = [
        'md5'=>md5($v),
        'Bezeichnung'=>$v,
        'key'=> $k,
        'Icon'=>'Icons_dunkel_9.gif'
      ];
    }
    if(!empty($module['installiert']) && count($module['installiert']) > 0) {
      $sort = null;
      foreach($module['installiert'] as $k => $v) {
        $sort[$k] = $v['Bezeichnung'];
      }
      array_multisort($sort, SORT_ASC, $module['installiert']);
    }

    return $module;
  }


  public function getVueShippingMethodSuccessPage(int $shippingMethodId): JsonResponse
  {
    $successpage = [
      'type' => 'defaultPage',
      'icon' => 'add-person-icon',
      'headline'=> 'Versandart angelegt',
      'subHeadline'=> 'Die Versandart wurde angelegt',
      'ctaButtons' => [
        [
          'title'  => 'Klasse',
          'action' => 'close',
          'link'   => 'index.php?module=versandarten&action=edit&id='.$shippingMethodId
        ]
      ]
    ];

    return new JsonResponse(
      ['page'=>$successpage]
    );
  }

  /**
   * @return JsonResponse
   */
  public function HandleGetAssistantAjaxAction(): JsonResponse
  {
    $module = $this->app->Secure->GetPOST('shippingmodule');
    $obj = $this->loadModule($module);

    if($obj === null || !method_exists($obj, 'getCreateForm')) {
      return new JsonResponse(['location' => 'index.php?module=versandarten&action=create&auswahl='.$module]);
    }
    $form = $obj->getCreateForm();
    if(!empty($form)) {
      $form[(count($form)) - 1]['link'] = [
        'link' => 'index.php?module=versandarten&action=create&auswahl=' . $module,
        'title' => 'Expertenmodus',
      ];
    }
    $name = ucfirst(substr($module, 13));
    if(method_exists($obj, 'GetBezeichnung')){
      $name = $obj->GetBezeichnung();
    }
    $page = [
      'type' => 'form',
      'dataRequiredForSubmit' =>
        [
          'shippingmodule' => $module,
          'id' => 'NEW',
        ],
      'submitType' => 'submit',
      'icon'=> 'password-icon',
      'headline' => $name,
      'subHeadline' => method_exists($obj, 'getClickByClickHeadline')
        ?$obj->getClickByClickHeadline():'Bitte Zugangsdaten eingeben',
      'submitUrl' => 'index.php?module=versandarten&action=create&cmd=saveAssistant&shippingmodule='.$module,
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
    $shippingMethodId = $this->app->Secure->GetPOST('id','','',true);
    $step = (int)$this->app->Secure->GetPOST('step');
    $shippingModule = $this->app->Secure->GetGET('shippingmodule');
    if(empty($shippingModule)) {
      $shippingModule = $this->app->Secure->GetPOST('shippingmodule');
    }
    if($shippingMethodId === 'NEW') {
      $postData = $this->app->Secure->POST;
      /** @var Versanddienstleister $obj */
      $obj = $this->loadModule($shippingModule);
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
        $shippingModuleName = $shippingModule;
        if(str_starts_with($shippingModuleName, 'versandarten_')) {
          $shippingModuleName = substr($shippingModuleName, 13);
        }
        $shippingModuleName = str_replace('_', ' ', ucfirst($shippingModuleName));
        if($obj !== null && method_exists($obj, 'GetBezeichnung')) {
          $shippingModuleName = $obj->GetBezeichnung();
        }
        $postData['step'] = 2;

        return $this->getStep2Page($shippingModule, $shippingModuleName, $postData);
      }

      $printer = (int)$this->app->Secure->GetPOST('paketmarke_drucker');
      $exportPrinter = (int)$this->app->Secure->GetPOST('export_drucker');
      $projektId = (int)$this->app->Secure->GetPOST('projekt');

      $createShippingResult = $this->createShippingMethodFromModuleName($shippingModule);
      if(!empty($createShippingResult['id'])) {
        $shippingMethodId = $createShippingResult['id'];
        if(method_exists($obj, 'getStructureDataForClickByClickSave')) {
          $structureData = $obj->getStructureDataForClickByClickSave();
          if(!empty($structureData)) {
            $this->saveCreateData($shippingMethodId, $structureData);
          }
        }
        $this->saveCreateData($shippingMethodId, $postData);
        $this->app->DB->UpdateArr(
          'versandarten',
          $shippingMethodId,
          'id',
          [
            'paketmarke_drucker' => $printer,
            'export_drucker'     => $exportPrinter,
            'projekt'            => $projektId,
          ], true
        );
      }
      elseif(!empty($createShippingResult['error'])) {
        return new JsonResponse($createShippingResult, Response::HTTP_BAD_REQUEST);
      }
    }

    if((int)$shippingMethodId > 0) {

      return $this->getVueShippingMethodSuccessPage((int)$shippingMethodId);
    }

    return new JsonResponse($data, Response::HTTP_BAD_REQUEST);
  }


  /**
   * @param int        $shippingMethodId
   * @param null|array $post
   */
  public function saveCreateData(int $shippingMethodId, ?array $post = null): void
  {
    $shippingMethod = $this->app->DB->SelectRow(
      sprintf('SELECT * FROM `versandarten` WHERE `id` = %d', $shippingMethodId)
    );
    /** @var Versanddienstleister $obj */
    $obj = $this->loadModule($shippingMethod['modul'], $shippingMethodId);
    if($obj === null) {
      return;
    }
    $form = $obj->getCreateForm();
    $data = json_decode($shippingMethod['einstellungen_json'], true);
    if(empty($data) || !is_array($data)) {
      $data = [];
    }

    if($post === null) {
      try {
        $vueFields = VueUtil::getInputNamesFromVuePages($form);
      }
      catch(Exception) {
        $vueFields = [];
      }
      foreach($vueFields as $input) {
        $post[$input] = !isset($this->app->Secure->POST[$input])
          ?null:$this->app->Secure->POST[$input];
      }
    }
    if(empty($post)) {
      return;
    }
    foreach($post as $key => $value) {
      $data[$key] = $value;
    }
    $this->app->DB->Update(
      sprintf(
        "UPDATE `versandarten` SET `einstellungen_json` = '%s' WHERE `id` = %d",
        $this->app->DB->real_escape_string(json_encode($data)), $shippingMethodId
      )
    );
  }

  /**
   * @param string $shippingMethodModule
   *
   * @return array
   */
  public function createShippingMethodFromModuleName(string $shippingMethodModule): array
  {
    $obj = $this->loadModule($shippingMethodModule);
    if ($obj === null)
      return ['success' => false, 'error' => 'Modul nicht vorhanden'];
    $name = $obj->name ?? ucfirst($shippingMethodModule);
    $type = $shippingMethodModule;
    $i = 1;
    $originalName = $name;
    while(
      $this->app->DB->Select(
        sprintf(
          "SELECT `id` FROM `versandarten` WHERE `type` = '%s' OR `bezeichnung` = '%s' LIMIT 1",
          $this->app->DB->real_escape_string($type), $this->app->DB->real_escape_string($name)
        )
      )
    ) {
      $i++;
      $type = $shippingMethodModule.'_'.$i;
      $name = $originalName.' '.$i;
    }
    $versandmail = 0;
    if($shippingMethodModule === 'versandarten_selbstabholer'){
      $versandmail = 1;
    }
    $this->app->DB->Insert(
        "INSERT INTO `versandarten` 
              (`bezeichnung`, `type`,`aktiv`, `geloescht`, `modul`,`ausprojekt`, `versandmail`, `einstellungen_json`) 
            VALUES ('$name','$type','1','0','$shippingMethodModule','0', $versandmail,'')"
    );
    $id = $this->app->DB->GetInsertID();
    $this->app->erp->RunHook('versandarten_create', 1, $id);

    return ['id'=>$id];
  }

  /**
   * @return JsonResponse|void
   * @noinspection PhpUnused
   */
  public function VersandartenCreate()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'getAssistant') {
      return $this->HandleGetAssistantAjaxAction();
    }
    if($cmd === 'saveAssistant') {
      return $this->HandleSaveAssistantAjaxAction();
    }

    $modulelist = $this->VersandartenSelModul(true);
    $auswahlmodul = $this->app->Secure->GetGET('auswahl');

    if($auswahlmodul && isset($modulelist[$auswahlmodul])) {
      $ret = $this->createShippingMethodFromModuleName($auswahlmodul);
      if(!empty($ret['id'])) {
        $this->app->Location->execute('index.php?module=versandarten&action=edit&id='.$ret['id']);
      }
      $this->app->Location->execute('index.php?module=versandarten&action=create');
    }

    /** @var Appstore $appstore */
    $appstore = $this->app->loadModule('appstore');
    $modulelist = $this->GetApps();
    $appstore->AddModuleHtml(
      $modulelist, '', 'index.php?module=versandarten&action=create&get='
    );
    $this->app->ModuleScriptCache->IncludeWidgetNew('ClickByClickAssistant');
    $this->app->Tpl->Parse('PAGE', 'versandarten_neu.tpl');
  }

  /**
   * @return array
   */
  public function GetInput(): array
  {
    $input = [];
    $input['bezeichnung'] = $this->app->Secure->GetPOST('bezeichnung');
    $input['type'] = $this->app->Secure->GetPOST('type');
    $input['projekt'] = $this->app->Secure->GetPOST('projekt');
    $input['modul'] = $this->app->Secure->GetPOST('modul');
    $input['aktiv'] = (int)$this->app->Secure->GetPOST('aktiv');
    $input['keinportocheck'] = (int)$this->app->Secure->GetPOST('keinportocheck');
    $input['ausprojekt'] = (int)$this->app->Secure->GetPOST('ausprojekt');
    $input['export_drucker'] = (int)$this->app->Secure->GetPOST('export_drucker');
    $input['paketmarke_drucker'] = (int)$this->app->Secure->GetPOST('paketmarke_drucker');
    $input['geschaeftsbrief_vorlage'] = (int)$this->app->Secure->GetPOST('geschaeftsbrief_vorlage');
    $input['versandmail'] = (int)$this->app->Secure->GetPOST('versandmail');

    return $input;
  }

  /**
   * @param string $module
   * @param int    $moduleId
   *
   * @return ?Versanddienstleister
   */
  public function loadModule(string $module, int $moduleId = 0) : ?Versanddienstleister
  {
    if(str_starts_with($module, 'versandarten_')) {
      $module = substr($module, 13);
    }
    if(empty($module)) {
      return null;
    }
    if(str_contains($module, '.') || str_contains($module, '/') || str_contains($module, '\\')) {
      return null;
    }

    $path = dirname(__DIR__).'/lib/versandarten/'.$module.'.php';
    if(!is_file($path)) {
      return null;
    }

    include_once $path ;
    $classname = 'Versandart_'.$module;
    if(!class_exists($classname)) {
      return null;
    }

    return new $classname($this->app, $moduleId);
  }

  /**
   * Retrieve all Versandarten from lib/versandarten/
   * @return array
   */
  function VersandartenSelModul(bool $addEmpty = false) : array
  {
    $result = [];
    if ($addEmpty)
      $result[''] = '';

    $pfad = dirname(__DIR__).'/lib/versandarten';
    if(!is_dir($pfad))
      return $result;
    $handle = opendir($pfad);
    $files = [];
    if($handle) {
      while (($file = readdir($handle)) !== false) {
        $files[] = $file;
      }
    }
    closedir($handle);

    foreach($files as $file) {
      if(str_starts_with($file, '.') || !str_ends_with($file, '.php') || !is_file($pfad.'/'.$file)
            || str_ends_with($file, '.src.php'))
        continue;

      $modul = str_replace('.php','',$file);
      if(str_ends_with($modul, '_custom')
          && !$this->app->DB->Select(
              "SELECT `id` FROM `versandarten` WHERE `modul` = '".$this->app->DB->real_escape_string($modul)."' LIMIT 1"
              )
            )
        continue;

      if($modul == '' || $modul=='rocketshipit')
        continue;

      $obj = $this->loadModule($modul);
      if ($obj === null)
        continue;
      $result[$modul] = $obj->GetName() ?? ucfirst($modul);
      unset($obj);
    }

    return $result;
  }
}
