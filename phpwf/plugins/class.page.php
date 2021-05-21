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

/// central config board for the engine
class Page 
{
  var $engine;
  /** @var Application $app */

  /**
   * Page constructor.
   *
   * @param Application $app
   */
  function __construct($app)
  {
    $this->app = $app;
    //$this->engine = &$engine;
  }

  /// load a themeset set
  function LoadTheme($theme)
  {
    //$this->app->Tpl->ReadTemplatesFromPath("themes/$theme/templates/");
    $this->app->Tpl->ReadTemplatesFromPath(__DIR__."/../../www/themes/$theme/templates/");
  }

  /// show complete page
  function Show()
  {
    return $this->app->Tpl->FinalParse('page.tpl');
  }

  /**
   * @param array $menu
   *
   * @return array
   */
  public function removeDoubleMenuEntries($menu)
  {
    if(empty($menu)) {
      return $menu;
    }
    foreach($menu as $key=>$value) {
      if($value['first'][2] !== 'direktzugriff'){
        if(!empty($value['sec']) && count($value['sec']) > 0){
          $secKeys = [];
          foreach ($value['sec'] as $key2 => $secnav) {
            $secNavString = implode('|', $secnav);
            if(in_array($secNavString, $secKeys)) {
              unset($menu[$key]['sec'][$key2], $value['sec'][$key2]);
              continue;
            }
            $secKeys[] = $secNavString;
          }
        }
      }
    }

    return $menu;
  }

  /**
   * @param array  $menu
   * @param string $module
   * @param string $action
   *
   * @return array
   */
  public function getSelectionKeysByModuleAction($menu, $module, $action)
  {
    $moduleKey = -1;
    $actionKey = -1;
    $moduleKey3 = -1;
    $actionKey3 = -1;
    foreach($menu as $key => $value){
      if($value['first'][2]!=='direktzugriff') {
        if(!empty($value['sec']) && count($value['sec'])>0){
          foreach($value['sec'] as $key2 => $secnav){
            $isModuleSecNav = $module == $secnav[1];
            if($isModuleSecNav && $action == $secnav[2]) {
              return [$key, $key2];
            }
            if($isModuleSecNav && $secnav[2] === 'list' && $moduleKey3 === -1) {
              $actionKey3 = $key2;
              $moduleKey3 = $key;
            }
            elseif($isModuleSecNav && $moduleKey === -1) {
              $actionKey = $key2;
              $moduleKey = $key;
            }
          }
        }
      }
    }

    if($moduleKey3 != -1) {
      return [$moduleKey3, $actionKey3];
    }

    return [$moduleKey, $actionKey];
  }

  /**
   * @param array       $menu
   * @param bool        $returnJson
   * @param null|string $aktmodule
   * @param null|string $aktaction
   *
   * @return array|void
   */
  public function CreateNavigation($menu, $returnJson = false, $aktmodule = null, $aktaction = null)
  {
    if(method_exists($this->app->erp, 'NavigationHooks')) {
      $this->app->erp->NavigationHooks($menu);
    }

    $menu = $this->removeDoubleMenuEntries($menu);

    if(isset($menu) && count($menu)>0){
      if($aktmodule === null) {
        $aktmodule = (string)$this->app->Secure->GetGET('module');
      }
      if($aktaction === null) {
        $aktaction = (string)$this->app->Secure->GetGET('action');
      }
      $actKeys = $this->getSelectionKeysByModuleAction($menu, $aktmodule, $aktaction);
      $aktmodulekey = $actKeys[0];
      $aktactionkey = $actKeys[1];
      $jsonMenu = [];
      $breadCrumb= [];
      foreach($menu as $key=>$value){
        $main = [
          'active' => false,
          'sec' => [],
          'link' => null,
        ];
        if($value['first'][2]!=='direktzugriff') {
          if($aktmodulekey == $key) {
            $main['active'] = true;
          }
          if($value['first'][2]!='') {
            $main['title'] = $this->app->Tpl->pruefeuebersetzung($value['first'][0],'menu');
            $main['original_title'] = $value['first'][0];
          }
          else {
            if($aktmodule == $value['first'][1]) {
              $main['active'] = true;
            }
            $main['module'] = $value['first'][1];
            $main['link'] = 'index.php?module='.$value['first'][1].'&top='.base64_encode($value['first'][0]);
            $main['original_title'] = $value['first'][0];
            $main['title'] = $this->app->Tpl->pruefeuebersetzung($value['first'][0],'menu');
          }
        }
        else {
          if($value['first'][2]!='') {
            $main['original_title'] = $value['first'][0];
            $main['title'] = $this->app->Tpl->pruefeuebersetzung($value['first'][0],'menu');
          }
        }

        if(isset($value['sec']) && count($value['sec'])>0){
          foreach($value['sec'] as $key2 => $secnav){
            $sec = [
              'active' => false,
            ];
            if($secnav[2]!='') {
              $sec['module'] = $secnav[1];
              $sec['action'] = $secnav[2];
              $sec['link'] = 'index.php?module='.$secnav[1].'&action='.$secnav[2].'&top='.base64_encode($value['first'][0]);
              $sec['original_title'] = $secnav[0];
              $sec['title'] = $this->app->Tpl->pruefeuebersetzung($secnav[0],'menu');
              if($aktmodule == $secnav[1]) {
                $breadCrumb[] = [
                  'link' => 'index.php?module='.$secnav[1].'&action='.$secnav[2].'&top='.base64_encode($value['first'][0]),
                  'title' => $this->app->Tpl->pruefeuebersetzung($secnav[0],'menu'),
                ];
              }
            }
            else {
              $sec['module'] = $secnav[1];
              $sec['link'] = 'href="index.php?module='.$secnav[1].'&top='.base64_encode($value['first'][0]);
              $sec['original_title'] = $secnav[0];
              $sec['title'] = $this->app->Tpl->pruefeuebersetzung($secnav[0],'menu');
              $breadCrumb[] = [
                'link' => 'index.php?module='.$secnav[1].'&action='.$secnav[2].'&top='.base64_encode($value['first'][0]),
                'title' => $this->app->Tpl->pruefeuebersetzung($secnav[0],'menu'),
              ];
            }

            if($aktmodulekey == $key && $aktactionkey == $key2)
            {
              $sec['active'] = true;
            }
            if(!empty($sec)) {
              $main['sec'][] = $sec;
            }
          }
        }
        $jsonMenu[] = $main;
      }
      if($returnJson) {
        return $jsonMenu;
      }

      $this->drawMenu($menu, $aktmodulekey, $aktmodule, $aktactionkey);
      $this->app->Tpl->Add(
        'BODYENDE',
        '<script id="mainMenuJson" type="application/json">'.json_encode($jsonMenu).'</script>'
      );
      $this->app->Tpl->Add(
        'BODYENDE',
        '<script id="breadCrumbJson" type="application/json">'.json_encode($breadCrumb).'</script>'
      );
    }
  }

  /**
   * @param array  $menu
   * @param string $aktmodulekey
   * @param string $aktmodule
   * @param int    $aktactionkey
   */
  public function drawMenu($menu, $aktmodulekey, $aktmodule, $aktactionkey) {
    foreach($menu as $key=>$value){
      $aktiv = 0;
      if($value['first'][2]!=='direktzugriff') {
        if($aktmodulekey == $key) {
          $aktiv = 1;
        }

        if($value['first'][2]!='') {
          $this->app->Tpl->Set('FIRSTNAV',' href="#">'.$this->app->Tpl->pruefeuebersetzung($value['first'][0],'menu').'</a>');
          if($aktiv) {
            $this->app->Tpl->Set('FIRSTNAVCLASS','active');
          }
          else{
            $this->app->Tpl->Set('FIRSTNAVCLASS','');
          }
        }
        else {
          if($aktmodule == $value['first'][1]) {
            $this->app->Tpl->Set('FIRSTNAVCLASS','active');
          }
          else {
            $this->app->Tpl->Set('FIRSTNAVCLASS','');
          }
          $this->app->Tpl->Set('FIRSTNAV',' href="index.php?module='.$value['first'][1].'&top='.base64_encode($value['first'][0]).'" >'.$this->app->Tpl->pruefeuebersetzung($value['first'][0],'menu').'</a>');
        }
      }
      else {
        if($value['first'][2]!="") {
          $this->app->Tpl->Set('FIRSTNAVCLASS','navnichtdirekt');
          $this->app->Tpl->Set('FIRSTNAV','  href="#" >'.$this->app->Tpl->pruefeuebersetzung($value['first'][0],'menu').'</a>');
        }
      }

      $this->app->Tpl->Parse('NAV','firstnav.tpl');
      if(isset($value['sec']) && count($value['sec'])>0){
        $this->app->Tpl->Add('NAV','<ul class="submenu">');
        foreach($value['sec'] as $key2 => $secnav){
          if($secnav[2]!='') {
            $this->app->Tpl->Set('SECNAV','  href="index.php?module='.$secnav[1].'&action='.$secnav[2].'&top='.base64_encode($value['first'][0]).'">'.$this->app->Tpl->pruefeuebersetzung($secnav[0],'menu').'</a>');
            if($aktmodule == $secnav[1]) {
              $this->app->Tpl->Set('BREADCRUMB','<a href="index.php?module='.$secnav[1].'&action='.$secnav[2].'&top='.base64_encode($value['first'][0]).'">'.$this->app->Tpl->pruefeuebersetzung($secnav[0],'menu').'</a>&nbsp;&#9658;&nbsp;');
            }
          }
          else {
            $this->app->Tpl->Set('SECNAV',' href="index.php?module='.$secnav[1].'&top='.base64_encode($value['first'][0]).'">'.$this->app->Tpl->pruefeuebersetzung($secnav[0],'menu').'aa</a>');
            $this->app->Tpl->Set('BREADCRUMB','<a href="index.php?module='.$secnav[1].'&top='.base64_encode($value['first'][0]).'">'.$secnav[0].'aa</a>&nbsp;&#9658;&nbsp;');
          }

          if($aktmodulekey == $key && $aktactionkey == $key2)
          {
            $this->app->Tpl->Set('SECNAVCLASS','active');
          }
          else {
            $this->app->Tpl->Set('SECNAVCLASS','');
          }
          $this->app->Tpl->Parse('NAV','secnav.tpl');
        }
        $this->app->Tpl->Add('NAV','</ul></li>');
      }
    }
  }

}
