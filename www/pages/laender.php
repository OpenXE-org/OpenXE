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

class Laender {
  var $app;

  static function TableSearch(&$app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name)
    {
      case "laender_list":
      $allowed['laender'] = array('list');

      $heading = array('ISO', 'Bezeichnung DE', 'Bezeichnung EN','EU', 'Men&uuml;');
      $width = array('9%', '40%', '40%','10%','1%');

      $findcols = array('l.iso', 'l.bezeichnung_de', 'l.bezeichnung_en', "if(l.eu,'EU','')", 'l.id');
      $searchsql = array('l.iso', 'l.bezeichnung_de', 'l.bezeichnung_en');

      $defaultorder = 1;
      $defaultorderdesc = 0;

      $menu = "<a href=\"index.php?module=laender&action=edit&id=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=laender&action=delete&id=%value%\")><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";

      $where = " l.id > 0 ";

      $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, l.iso, l.bezeichnung_de, l.bezeichnung_en, if(l.eu,'EU',''), l.id FROM laender l";

      $count = "SELECT count(l.id) FROM laender l WHERE $where";
      break;

    }

    $erg = false;

    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v))$erg[$v] = $$v;
    }
    return $erg;
  }

  function __construct($app, $intern = false) {
    $this->app=$app;
    if($intern)return;
    $this->app->ActionHandlerInit($this);

    // ab hier alle Action Handler definieren die das Modul hat
    $this->app->ActionHandler("list", "LaenderList");
    $this->app->ActionHandler("create", "LaenderCreate");
    $this->app->ActionHandler("edit", "LaenderEdit");
    $this->app->ActionHandler("delete", "LaenderDelete");
    $this->app->ActionHandler('test', 'LaenderTest');

    $this->app->ActionHandlerListen($app);
  }

  function LaenderTest(){
      $countryModule = $this->app->Container->get('CountryMigrationService');
      if($countryModule->needsMigration()){
          $countryModule->doMigration();
      }
  }

  function LaenderMenu()
  {
    $this->app->erp->MenuEintrag("index.php?module=laender&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function LaenderList()
  {
    $this->app->erp->MenuEintrag("index.php?module=laender&action=create","Neues Land");
    $this->app->erp->MenuEintrag("index.php?module=laender&action=list","&Uuml;bersicht");

    $this->app->YUI->TableSearch('TAB1','laender_list', "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse("PAGE","laender_list.tpl");
  }

  function LaenderEdit()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $submit = $this->app->Secure->GetPOST('submitland');

    $this->app->Tpl->Add('KURZUEBERSCHRIFT2',$bezeichnung_de);



    $input = array();

    $this->app->erp->MenuEintrag("index.php?module=laender&action=edit&id=$id","Details");

    $this->app->erp->MenuEintrag("index.php?module=laender&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $input = $this->GetInput();

    if(is_numeric($id) && $submit!=''){

      $error = '';
      if($input['iso']=='' || strlen(trim($input['iso'])) > 2) $error .= 'Geben Sie bitte einen zweistelligen ISO-Code ein.<br>';
      if($input['bezeichnung_de'] == '') $error .= 'Geben Sie bitte eine deutsche L&auml;nderbezeichnung ein.<br>';
      if($input['bezeichnung_en'] =='') $error .= 'Geben Sie bitte eine englische L&auml;nderbezeichnung ein.<br>';

      if($error!=''){
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">$error</div>");
      }else{
        if($error == ""){
          $this->app->DB->Update("UPDATE laender SET iso='{$input['iso']}', bezeichnung_de='{$input['bezeichnung_de']}', bezeichnung_en='{$input['bezeichnung_en']}', eu='{$input['eu']}' WHERE id = '$id' LIMIT 1");
          if($input['eu']==1){
            $this->app->Tpl->Set('EU', "checked");
          }
          $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
        }
      }
    }
    $iso = $this->app->DB->Select("SELECT iso FROM laender WHERE id = '$id'");
    $bezeichnung_de = $this->app->DB->Select("SELECT bezeichnung_de FROM laender WHERE id = '$id'");
    $bezeichnung_en = $this->app->DB->Select("SELECT bezeichnung_en FROM laender WHERE id = '$id'");
    $eu = $this->app->DB->Select("SELECT eu FROM laender WHERE id = '$id'");


    $this->app->Tpl->Set('ISO', $iso);
    $this->app->Tpl->Set('BEZEICHNUNG_DE', $bezeichnung_de);
    $this->app->Tpl->Set('BEZEICHNUNG_EN', $bezeichnung_en);
    if($eu == 1){
      $this->app->Tpl->Set('EU', "checked");
    }


    //$this->SetInput($input);

    $this->app->Tpl->Parse('PAGE', "laender_edit.tpl");
  }

  function LaenderDelete()
  {
    $ref = $_SERVER['HTTP_REFERER'];
    $id = $this->app->Secure->GetGET("id");
    $this->app->DB->DELETE("DELETE FROM laender WHERE id = '$id' LIMIT 1");

    //$this->VorlageList();
    header("Location: $ref");
    exit;
  }

  function LaenderCreate(){
    //		$this->app->Tpl->Add(KURZUEBERSCHRIFT,"Benutzer");
    $this->app->erp->MenuEintrag("index.php?module=laender&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $input = $this->GetInput();
    $submit = $this->app->Secure->GetPOST('submitland');

    if($submit!='') {

      $error = '';

      if($input['iso']=='' || strlen(trim($input['iso'])) > 2) $error .= 'Geben Sie bitte einen zweistelligen ISO-Code ein.<br>';
      if($input['bezeichnung_de']=='') $error .= 'Geben Sie bitte eine deutsche L&auml;nderbezeichnung ein.<br>';
      if($input['bezeichnung_en']=='') $error .= 'Geben Sie bitte eine englische L&auml;nderbezeichnung ein.<br>';

      if($this->app->DB->Select("SELECT '1' FROM laender WHERE iso='{$input['iso']}' LIMIT 1")=='1')
        $error .= "Es existiert bereits ein ISO-Code mit diesem Namen.<br />";

      if($error!=''){
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">$error</div>");
      }else {
        $this->app->DB->Insert("INSERT INTO laender (iso, bezeichnung_de, bezeichnung_en, eu) VALUES ('{$input['iso']}', '{$input['bezeichnung_de']}', '{$input['bezeichnung_en']}', '{$input['eu']}')");
        $newid = $this->app->DB->GetInsertID();
        $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Der L&auml;ndereintrag wurde erfolgreich angelegt.</div>");
        header("Location: index.php?module=laender&action=edit&id=$newid&msg=$msg");
        exit;
      }
    }

    $this->SetInput($input);

    $this->app->Tpl->Parse('PAGE', "laender_edit.tpl");
  }


  function GetInput(){
    $input = array();
    $input['iso'] = $this->app->Secure->GetPOST('iso');
    $input['bezeichnung_de'] = $this->app->Secure->GetPOST('bezeichnung_de');
    $input['bezeichnung_en'] = $this->app->Secure->GetPOST('bezeichnung_en');
    $input['eu'] = (int)$this->app->Secure->GetPOST('eu');
    return $input;

  }

  function SetInput($input){
    $this->app->Tpl->Set('ISO', $input['iso']);
    $this->app->Tpl->Set('BEZEICHNUNG_DE', $input['bezeichnung_de']);
    $this->app->Tpl->Set('BEZEICHNUNG_EN', $input['bezeichnung_en']);
    if($input['eu']==1){
      $this->app->Tpl->Set('EU', "checked");
    }


  }

}
