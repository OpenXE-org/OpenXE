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
use Xentral\Widgets\ClickByClickAssistant\VueUtil;

class Versandarten {
  /** @var Application $app */
  var $app;

  /** @var string */
  const MODULE_NAME = 'ShippingMethod';

  /** @var string[] $stylesheet */
  public $stylesheet = [
    './classes/Modules/Appstore/www/css/tilegrid.css',
  ];

  /** @var string[] $javascript */
  public $javascript = [
    './classes/Modules/ShippingMethod/www/js/shipping_method_create.js',
  ];

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

      $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>"
        ."<a href=\"index.php?module=versandarten&action=edit&id=%value%\">"
        ."<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>";
      $menu .= "&nbsp;"
        ."<a href=\"index.php?module=versandarten&action=copy&id=%value%\">"
        ."<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>";
      if(!$isSettingAction) {
        $menu .= "&nbsp;"
          . "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=versandarten&action=delete&id=%value%\")>"
          . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";
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

  /**
   * Versandarten constructor.
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

    // ab hier alle Action Handler definieren die das Modul hat
    $this->app->ActionHandler("create", "VersandartenCreate");
    $this->app->ActionHandler("edit", "VersandartenEdit");
    $this->app->ActionHandler("list", "VersandartenList");
    $this->app->ActionHandler("delete", "VersandartenDelete");
    $this->app->ActionHandler("copy", "VersandartenCopy");

    $this->app->ActionHandlerListen($app);
  }

  /**
   * @return array
   */
  public function getBetaShippingModules(): array
  {
    /** @var Appstore $appStore */
    $appStore = $this->app->erp->LoadModul('appstore');

    return $appStore->getBetaModulesByPrefix('versandarten_');
  }

  public function Install(): void
  {
    $this->app->erp->GetVersandartAuftrag();
  }

  public function VersandartenCopy()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $id = $this->app->DB->Select(sprintf('SELECT `id` FROM `versandarten` WHERE `id` = %d LIMIT 1', $id));
    if(!$id) {
      $this->app->Location->execute('index.php?module=versandarten&action=list');
    }
    $newid = $this->app->DB->MysqlCopyRow('versandarten', 'id', $id);
    if($newid) {
      $this->app->DB->Update(
        sprintf(
          'UPDATE `versandarten` set `aktiv` = 0, `ausprojekt` = 0 WHERE `id` = %d LIMIT 1',
          $newid
        )
      );
    }
    $this->app->Location->execute('index.php?module=versandarten&action=edit&id='.$newid);
  }
  public function VersandartenMenu()
  {
    
  }

  public function VersandartenList(): void
  {
    $this->app->erp->MenuEintrag('index.php?module=versandarten&action=create','Neue Versandart anlegen');
    $this->app->erp->MenuEintrag('index.php?module=versandarten&action=list','&Uuml;bersicht');
    $this->app->YUI->TableSearch('TAB1','versandarten_list', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE','versandarten_list.tpl');
  }

  /**
   * @param string $value
   * @param bool   $retarr
   *
   * @return string|array|null
   */
  function VersandartenSelModul($value = '', $retarr = false)
  {
    $array = null;
    $ret = '<option value=""></option>';
    $pfad = dirname(__DIR__).'/lib/versandarten';
    $beta = $this->getBetaShippingModules();
    if(is_dir($pfad)) {
      $handle = opendir($pfad);
      if($handle) {
        while (false !== ($file = readdir($handle))) {
          $files[] = $file;
        } 
        natcasesort($files);
        foreach($files as $file) {
          if($file[0] !== '.' && substr($file,-4) === '.php' && is_file($pfad.'/'.$file)
            && substr($file,-8) !== '.src.php') {
            $modul = str_replace('.php','',$file);
            if(substr($modul,-7) === '_custom'
              && !$this->app->DB->Select(
                "SELECT `id` FROM `versandarten` WHERE `modul` = '".$this->app->DB->real_escape_string($modul)."' LIMIT 1"
              )
            ) {
              continue;
            }
            if($modul!=='rocketshipit' && $modul!='') {
              include_once dirname(__DIR__).'/lib/versandarten/'.$modul.'.php';
            }
            $classname = 'Versandart_'.$modul;
            if(class_exists($classname)) {
              try {
                $r = new ReflectionMethod($classname, '__construct');
                $params = $r->getParameters();
                $anzargs = count($params);
              }
              catch(Exception $e) {
                $anzargs = 1;
              }
              if($anzargs > 1) {
                $obj = new $classname($this->app, 0);
              }
              else{
                $obj = new $classname($this->app);
              }
            }
            $array[$modul] = (isset($obj->name)?$obj->name:ucfirst($modul));
            $modulKey = $modul;
            if(strpos($modulKey,'versandarten_') !== 0) {
              $modulKey = 'versandarten_'.$modul;
            }
            $ret .= '<option value="'.$modul.'"'
              .($value == $modul?' selected="selected" ':'').'>'
              .(isset($obj->name)?$obj->name:ucfirst($modul))
              .(in_array($modulKey, $beta)?' (Beta)':'')
              .'</option>';
            unset($obj);
          }
        }
        closedir($handle);
      }
    }
    if($retarr){
      return $array;
    }

    return $ret;
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
    if(strpos($module,'versandarten_') === 0) {
      $module = substr($module, 13);
      if(empty($module)) {
        return null;
      }
    }
    if(strpos($module, '.') !== false || strpos($module, '/') !== false || strpos($module, '\\')) {
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

  public function VersandartenEdit()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $speichern = $this->app->Secure->GetPOST('speichern');

    $this->app->erp->MenuEintrag('index.php?module=versandarten&action=edit&id='.$id,'Details');
    $this->app->erp->MenuEintrag('index.php?module=versandarten&action=list','Zur&uuml;ck zur &Uuml;bersicht');

    $input = $this->GetInput();

    $error = '';
    if(is_numeric($id) && $speichern != ''){
      $modulepath = dirname(__DIR__).'/lib/versandarten/'.$input['selmodul'].'.php';
      if (!empty($input['selmodul']) && is_file($modulepath)) {
          include_once($modulepath);
          $classname = 'Versandart_'.$input['selmodul'];
          if(class_exists($classname)) {
              $moduleObject = new $classname($this->app, $id);
          }
          if(!empty($moduleObject) && method_exists($moduleObject, 'checkInputParameters')) {
              if (false === $moduleObject->checkInputParameters('MESSAGE')) {
                  $error = 'error';
              }
          }
      } else {
          $error = sprintf('Versandart "%s" existiert nicht.', $input['selmodul']);
      }

      if($id) {
        if($error === '') {
            $projektid = 0;
            if(!empty($input['projekt'])){
                $projektid = $this->app->DB->Select(
                  sprintf(
                    "SELECT `id` FROM `projekt` WHERE `abkuerzung` = '%s' LIMIT 1",
                    $input['projekt']
                  )
                );
            }

            $oldtype = $this->app->DB->Select(
              sprintf('SELECT `id` FROM `versandarten` WHERE `id` = %d LIMIT 1', $id)
            );
            if($oldtype != $input['typ'])
            {
              while($this->app->DB->Select(
                sprintf(
                  "SELECT `id` FROM `versandarten` WHERE `type` = '%s' AND `id` <> %d LIMIT 1",
                  $input['typ'], $id
                )
              ))
              {
                $typa = explode('_', $input['typ']);
                if(count($typa) == 1 || !is_numeric($typa[count($typa)-1]))
                {
                  $input['typ'] .= '_1';
                }else{
                  $counter = $typa[count($typa)-1]+1;
                  unset($typa[count($typa)-1]);
                  $input['typ'] = implode('_', $typa).'_'.$counter;
                }
              }
            }
            
            $this->app->DB->Update(
              sprintf(
                "UPDATE `versandarten` 
                SET `bezeichnung`='%s', `type` ='%s', 
                        `projekt`=%d, `aktiv`=%d, `modul`='%s',
                        `export_drucker` = %d, 
                        `paketmarke_drucker` = %d, 
                        `ausprojekt` = %d, `versandmail` = %d,
                        `geschaeftsbrief_vorlage` = %d,
                        `keinportocheck`=%d 
                WHERE `id` = %d LIMIT 1",
                $input['bezeichnung'], $input['typ'], $projektid,$input['aktiv'],$input['selmodul'],
                $input['export_drucker'], $input['paketmarke_drucker'],$input['ausprojekt'], $input['versandmail'],
                $input['geschaeftsbrief_vorlage'], $input['keinportocheck'], $id
              )
            );
            if($input['aktiv'] == 1){
              $this->app->Tpl->Set('AKTIV', "checked");
            }
            if($input['keinportocheck'] == 1){
              $this->app->Tpl->Set('KEINPORTOCHECK', "checked");
            }
          $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Daten wurden erfolgreich gespeichert!</div>");
        }
      }
      else {
        $error = '';

        if(trim($input['bezeichnung']) == '')
        {
          $error = 'Bitte alle Pflichtfelder ausfüllen!';
          $this->app->Tpl->Set('MSGBEZEICHNUNG',' <span style="color:red">Pflichtfeld!</span>');
        }
        if(trim($input['typ']) == '')
        {
          $error = 'Bitte alle Pflichtfelder ausfüllen!';
          $this->app->Tpl->Set('MSGTYP',' <span style="color:red">Pflichtfeld!</span>');
        }

        if($error!=''){
          $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">$error</div>");
        }else {

          if(trim($input['projekt']) == ''){
            $projektid = 0;
          }else{
            $projektid = $this->app->DB->Select(
              sprintf(
                "SELECT `id` FROM `projekt` WHERE `abkuerzung` = '%s' LIMIT 1",
                $input['projekt']
              )
            );
          }

          while($this->app->DB->Select(sprintf("SELECT `id` FROM `versandarten` WHERE `type` = '%s' LIMIT 1", $input['typ'])))  {
            $typa = explode('_', $input['typ']);
            if(count($typa) == 1 || !is_numeric($typa[count($typa)-1]))
            {
              $input['typ'] .= '_1';
            }else{
              $counter = $typa[count($typa)-1]+1;
              unset($typa[count($typa)-1]);
              $input['typ'] = implode('_', $typa).'_'.$counter;
            }
          }
          
          $this->app->DB->Insert(
            sprintf(
              "INSERT INTO `versandarten` 
                (`bezeichnung`, `type`, `projekt`, `aktiv`, `keinportocheck`,
                `modul`,`export_drucker`, `paketmarke_drucker`,
               `ausprojekt`,`versandmail`, `geschaeftsbrief_vorlage`) 
             VALUES ('%s', '%s', %d, %d,%d,
                     '%s',%d,%d,
                     %d,%d,%d)",
              $input['bezeichnung'], $input['typ'],$projektid, $input['aktiv'], $input['keinportocheck'],
              $input['selmodul'],$input['export_drucker'],$input['paketmarke_drucker'],
              $input['ausprojekt'], $input['versandmail'], $input['geschaeftsbrief_vorlage']
            )
          );

          $newid = $this->app->DB->GetInsertID();
          $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Die Daten wurden erfolgreich gespeichert!</div>");
          $this->app->Location->execute("index.php?module=versandarten&action=edit&id=$newid&msg=$msg");
        }
      }      
    }
    $ausprojekt = 0;

    $daten = $this->app->DB->SelectRow(
      sprintf('SELECT * FROM `versandarten` WHERE `id` = %d LIMIT 1', $id)
    );
    if(!empty($daten)) {
      $this->app->erp->Headlines('', $daten['bezeichnung']);
      $this->app->Tpl->Set('AKTMODUL', $daten['modul']);
      /** @var Versanddienstleister $obj */
      $obj = $this->loadModule($daten['modul'], $daten['id']);
      $bezeichnung = $daten['bezeichnung'];
      $typ = $daten['type'];
      $projekt = $daten['projekt'];
      $aktiv = $daten['aktiv'];
      $keinportocheck = $daten['keinportocheck'];
      $ausprojekt = $daten['ausprojekt'];
      $projektname = $this->app->DB->Select(sprintf('SELECT `abkuerzung` FROM `projekt` WHERE `id` = %d', $projekt));
      if(!empty($obj) && method_exists($obj, 'Einstellungen')) {
        $obj->Einstellungen('JSON');
      }
      if(!empty($obj) && method_exists($obj, 'isEtikettenDrucker')) {
        $etikettendrucker = $obj->isEtikettenDrucker();
      }
    }
    else {
      $this->app->Tpl->Set('AKTMODUL','');
    }

    $drucker_export = $this->app->erp->GetDrucker();
    $this->app->Tpl->Set('EXPORT_DRUCKER','<option value="0"></option>');
    if(!empty($drucker_export)){
      foreach($drucker_export as  $k => $v) {
        $this->app->Tpl->Add(
          'EXPORT_DRUCKER',
          '<option value="'.$k.'"'
          .($k == (isset($daten['export_drucker'])?$daten['export_drucker']:'')?' selected="selected" ':'').'>'
          .$v.'</option>'
        );
      }
    }

    $drucker_paketmarke = $this->app->erp->GetDrucker();
    
    if($etikettendrucker) {
      $etikettendruckerarr = $this->app->erp->GetEtikettendrucker();
      if($etikettendruckerarr) {
        foreach($etikettendruckerarr as $k => $v) {
          $drucker_paketmarke[$k] = $v;
        }
      }
    }
    $this->app->Tpl->Set('PAKETMARKE_DRUCKER','<option value="0"></option>');
    if($drucker_paketmarke) {
      foreach($drucker_paketmarke as  $k => $v)  {
        $this->app->Tpl->Add(
          'PAKETMARKE_DRUCKER',
          '<option value="'.$k.'"'
          .($k == (isset($daten['paketmarke_drucker'])?$daten['paketmarke_drucker']:'')?' selected="selected" ':'').'>'
          .$v.'</option>'
        );
      }
    }
    $this->app->YUI->HideFormular('versandmail', array('0'=>'versandbetreff','1'=>'dummy'));
    $this->app->Tpl->Add(
      'SELVERSANDMAIL',
      '<option value="0">Standardverhalten</option>');
    $this->app->Tpl->Add(
      'SELVERSANDMAIL',
      '<option value="-1"'
      .(isset($daten['versandmail']) && $daten['versandmail'] == -1?' selected="selected"':'')
      .'>Keine Versandmail</option>'
    );
    $this->app->Tpl->Add(
      'SELVERSANDMAIL',
      '<option value="1"'
      .(isset($daten['versandmail']) && $daten['versandmail'] == 1?' selected="selected"':'')
      .'>Eigene Textvorlage</option>'
    );
    
    $geschaeftsbrief_vorlagen = $this->app->DB->SelectArr(
      "SELECT gv.id, gv.subjekt, p.abkuerzung 
      FROM `geschaeftsbrief_vorlagen` AS `gv` 
      LEFT JOIN `projekt` AS `p` ON gv.projekt = p.id 
      ORDER by gv.subjekt"
    );
    if($geschaeftsbrief_vorlagen) {
      foreach($geschaeftsbrief_vorlagen as $k => $v) {
        $this->app->Tpl->Add(
          'SELGESCHAEFTSBRIEF_VORLAGE',
          '<option value="'.$v['id'].'"'
          .($v['id'] == (isset($daten['geschaeftsbrief_vorlage'])
            ?$daten['geschaeftsbrief_vorlage']:'')
            ?' selected="selected" ':'').'>'
          .$v['subjekt'].($v['abkuerzung']?' - '.$v['abkuerzung']:'')
          .'</option>'
        );
      }
    }
    if($error === ''){
      $selectedModule = isset($daten['modul'])?$daten['modul']:'';
    }
    else {
      $selectedModule = $input['selmodul'];
    }
    $this->app->Tpl->Set('SELMODUL', $this->VersandartenSelModul($selectedModule));

    $this->app->Tpl->Set('BEZEICHNUNG', $error == ''?$bezeichnung:$input['bezeichnung']);
    $this->app->Tpl->Set('TYP', $error == ''?$typ:$input['typ']);
    $this->app->Tpl->Set('PROJEKT', $error == ''?$projektname:$input['projekt']);
    if(($error == '' && $aktiv == 1) || ($error != '' && $input['aktiv'])){
      $this->app->Tpl->Set('AKTIV', 'checked');
    }
    if(($error == '' && $keinportocheck == 1) || ($error != '' && $input['keinportocheck'])){
      $this->app->Tpl->Set('KEINPORTOCHECK', 'checked');
    }
    $this->app->YUI->AutoComplete('projekt', 'projektname', 1);
    if(!empty($daten['modul'])) {
      $beta = $this->getBetaShippingModules();
      if(in_array('versandarten_'.$daten['modul'], $beta)) {
        $this->app->Tpl->Add('MESSAGE','<div class="info">Dieses Modul ist noch im Beta Stadium.</div>');
        /** @var Appstore $appstore */
        $appstore = $this->app->erp->LoadModul('appstore');
        if($appstore !== null){
          $appstore->addBetaToHeadline();
        }
      }
    }
    $this->app->Tpl->Parse('PAGE', 'versandarten_edit.tpl');
  }

  /**
   * @param string $shippingModule
   *
   * @return array
   */
  public function getPrinterByModule($shippingModule = '')
  {
    /** @var Versanddienstleister $obj */
    $obj = empty($shippingModule)?null:$this->loadModule($shippingModule);
    $isLabelPrinter = $obj !== null
      && method_exists($obj, 'isEtikettenDrucker')
      && $obj->isEtikettenDrucker();
    $printer = $this->app->erp->GetDrucker();
    if(!is_array($printer)) {
      $printer = [];
    }
    if(!$isLabelPrinter) {
      return $printer;
    }

    $labelPrinter = $this->app->erp->GetEtikettendrucker();
    if(empty($labelPrinter)) {
      return $printer;
    }

    foreach($labelPrinter as $k => $v) {
      $printer[$k] = $v;
    }

    return $printer;
  }

  /**
   * @param string $shippingModule
   *
   * @return array
   */
  public function getVuePrinterOptions($shippingModule = '')
  {
    try{
      return VueUtil::keyValueArrayToVueOptions($this->getPrinterByModule($shippingModule));
    }
    catch(Exception $e) {
      return [];
    }
  }

  /**
   * @return array
   */
  public function getVueExportPrinterOptions()
  {
    $printer = $this->app->erp->GetDrucker();
    if(empty($printer)) {
      return [];
    }
    try{
      return VueUtil::keyValueArrayToVueOptions($printer);
    }
    catch(Exception $e) {
      return [];
    }
  }

  /**
   * @return array
   */
  public function getVueProjects(): array
  {
    $projects = array_merge(
      [ 0 => '', ],
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

    try{
      return VueUtil::keyValueArrayToVueOptions($projects);
    }
    catch(Exception $e) {
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
  public function getStep2Page($shippingModule, $shippingModuleName, $requiredForSubmit = null): JsonResponse
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

  /**
   * @return array
   */
  public function getFeatureForm($shippingModule): array
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

    return $ret;
  }

  public function VersandartenDelete(): void
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->DB->Delete(sprintf('DELETE FROM `versandarten` WHERE `id` = %d LIMIT 1', $id));
    $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Die Versandart wurde gelöscht!</div>");
    $this->app->Location->execute("index.php?module=versandarten&action=list&msg=$msg");
  }

  /**
   * @param string $val
   *
   * @return array|null
   */
  public function getApps($val = ''): ?array
  {
    $val = (string)$val;
    /** @var Appstore $appstore */
    $appstore = $this->app->loadModule('appstore');
    $module = $appstore->getAppsListWithPrefix('versandarten_');
    $modularr = $this->VersandartenSelModul('', true);
    if($module) {
      if(!empty($module['installiert'])) {
        foreach($module['installiert'] as $k => $v) {
          $module['installiert'][$k]['match'] = $appstore->match($v['Bezeichnung'], $val);
          $module['installiert'][$k]['md5'] = md5($v['Bezeichnung']);
          $found[] = $v['key'];
        }
      }
      if(!empty($module['kauf'])) {
        foreach($module['kauf'] as $k => $v) {
          $module['kauf'][$k]['match'] = $appstore->match($v['Bezeichnung'], $val);
          $module['kauf'][$k]['md5'] = md5($v['Bezeichnung']);
          $found[] = $v['key'];
        }
      }
    }
    if($modularr) {
      foreach($modularr as $k => $v)  {
        if(!isset($found) || !in_array('versandarten_'.$k,$found)) {
          $found[] = 'versandarten_'.$k;
          $module['installiert'][] = [
            'md5'=>md5($v),
            'Bezeichnung'=>$v,
            'key'=>'versandarten_'.$k,
            'match'=>$appstore->match($v, $val),
            'Icon'=>'Icons_dunkel_9.gif'
          ];
        }
      }
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


  /**
   * @var int $shippingMethodId
   *
   * @return JsonResponse
   */
  public function getVueShippingMethodSuccessPage($shippingMethodId): JsonResponse
  {
    $succespage = [
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
      ['page'=>$succespage]
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
      $form[count($form) - 1]['link'] = [
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
        if(strpos($shippingModuleName, 'versandarten_') === 0) {
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
        return new JsonResponse($createShippingResult, JsonResponse::HTTP_BAD_REQUEST);
      }
    }

    if((int)$shippingMethodId > 0) {

      return $this->getVueShippingMethodSuccessPage((int)$shippingMethodId);
    }

    return new JsonResponse($data, JsonResponse::HTTP_BAD_REQUEST);
  }


  /**
   * @param int        $shippingMethodId
   * @param null|array $post
   */
  public function saveCreateData($shippingMethodId, $post = null): void
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
      catch(Exception $e) {
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
          if($v['match'])
          {
            if($anzeigen !== '')
            {
              $anzeigen .= ';';
            }
            $anzeigen .= 'm'.md5($v['Bezeichnung']);
          }else{
            if($ausblenden !== '')
            {
              $ausblenden .= ';';
            }
            $ausblenden .= 'm'.md5($v['Bezeichnung']);
          }
        }
      }
      if(isset($module['kauf'])) {
        foreach($module['kauf'] as $k => $v) {
          if($v['match']) {
            if($anzeigen !== '')
            {
              $anzeigen .= ';';
            }
            $anzeigen .= 'm'.md5($v['Bezeichnung']);
          }
          else{
            if($ausblenden !== '')
            {
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
   * @param string $shippingMethodModule
   *
   * @return array
   */
  public function createShippingMethodFromModuleName($shippingMethodModule): array
  {
    if(!$this->app->erp->ModulVorhanden($shippingMethodModule)) {
      return [
        'success'=>false,
        'error'=>'Modul nicht vorhanden'
      ];
    }
    $modules = $this->getApps();
    $modul = substr($shippingMethodModule,13);
    $name = ucfirst($modul);
    if($modules['installiert']) {
      foreach($modules['installiert'] as $key => $installedModule) {
        if($installedModule['key'] === $shippingMethodModule && $installedModule['Bezeichnung'] != '') {
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
          "SELECT `id` FROM `versandarten` WHERE `type` = '%s' OR `bezeichnung` = '%s' LIMIT 1",
          $this->app->DB->real_escape_string($type), $this->app->DB->real_escape_string($name)
        )
      )
    ) {
      $i++;
      $type = $modul.'_'.$i;
      $name = $originalName.' '.$i;
    }
    $versandmail = 0;
    if($shippingMethodModule === 'versandarten_selbstabholer'){
      $versandmail = 1;
    }
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `versandarten` 
              (`bezeichnung`, `type`,`aktiv`, `geloescht`, `modul`,`ausprojekt`, `versandmail`, `einstellungen_json`) 
            VALUES ('%s','%s','1','0','%s','0', %d,'')",
        $name, $type, $modul, $versandmail
      )
    );
    $id = $this->app->DB->GetInsertID();
    $this->app->erp->RunHook('versandarten_create', 1, $id);

    return ['id'=>$id];
  }

  /**
   * @return JsonResponse|void
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

    if($cmd === 'suche') {
      return $this->HandleSearchAjaxAction();
    }
    $module = $this->getApps($this->app->Secure->GetPOST('val'));
    if($this->app->Secure->GetGET('auswahl')) {
      //$bezeichnung = $this->app->Secure->GetPOST('bezeichnung');
      $auswahlmodul = $this->app->Secure->GetGET('auswahl');
      if($auswahlmodul === 'custom') {
        $this->app->DB->Insert(
          "INSERT INTO `versandarten` 
            (`bezeichnung`, `type`,`aktiv`, `geloescht`, `modul`,`ausprojekt`,`einstellungen_json`) 
            VALUES ('','','1','0','','0','')"
        );
        $id = $this->app->DB->GetInsertID();
        $this->app->Location->execute('index.php?module=versandarten&action=edit&id='.$id);
      }
      if($this->app->erp->ModulVorhanden($auswahlmodul)) {
        $ret = $this->createShippingMethodFromModuleName($auswahlmodul);
        if(!empty($ret['id'])) {
          $this->app->Location->execute('index.php?module=versandarten&action=edit&id='.$ret['id']);
        }
        $this->app->Location->execute('index.php?module=versandarten&action=create');
      }
    }

    if($this->app->erp->isIoncube() && $this->app->Secure->GetPOST('testen')) {
      $modul = $this->app->Secure->GetPOST('modul');
      if($modul) {
        $testapp = $modul;
        if(is_file(dirname(__DIR__).'/update.php')) {
          $result = '';
          include_once(dirname(__DIR__).'/update.php');
          if($result === 'OK') {
            $this->app->Tpl->Add(
              'MESSAGE',
              '<div class="info">Das Modul wurde zum Testen angefragt. Bitte Updaten Sie xentral in fr&uuml;hestens 10 Minuten um das Modul zu laden</div>'
            );
          }
          else{
            $this->app->Tpl->Add(
              'MESSAGE',
              '<div class="error">Es ist ein Fehler beim Updaten aufgetreten: '.$result.'</div>'
            );
          }
        }
      }
    }
    elseif($this->app->erp->isIoncube()) {
      $get = $this->app->Secure->GetGET('get');
      if(!empty($get) && !empty($module) && !empty($module['kauf'])) {
        foreach($module['kauf'] as $k => $v) {
          if($v['md5'] == $get) {
            $mods = $this->app->erp->getAppList();
            foreach($mods as $k2 => $v2)  {
              if(md5($v2['Bezeichnung']) == $get) {
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
    /** @var Appstore $appstore */
    $appstore = $this->app->loadModule('appstore');
    $modullist = $this->GetApps();
    $appstore->AddModuleHtml(
      $modullist, 'versandarten_', 'index.php?module=versandarten&action=create&get=',
      [
        'title' => 'Custom',
        'link' => 'index.php?module=versandarten&action=create&auswahl=custom',
      ]
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
    $input['typ'] = $this->app->Secure->GetPOST('typ');
    $input['projekt'] = $this->app->Secure->GetPOST('projekt');
    $input['selmodul'] = $this->app->Secure->GetPOST('selmodul');
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
   * @param array $input
   */
  public function SetInput($input): void
  {
    $this->app->Tpl->Set('BEZEICHNUNG', $input['bezeichnung']);
    $this->app->Tpl->Set('TYP', $input['typ']);
    $this->app->Tpl->Set('PROJEKT', $input['projekt']);
    if($input['aktiv']==1){
      $this->app->Tpl->Set('AKTIV', 'checked');
    }
    if($input['keinportocheck']==1){
      $this->app->Tpl->Set('KEINPORTOCHECK', 'checked');
    }

    if($input['ausprojekt']==1){
      $this->app->Tpl->Set('AUSPROJEKT', 'checked');
    }
  }

}
