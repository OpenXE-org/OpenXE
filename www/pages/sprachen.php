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

class Sprachen {
  var $app;

  static function TableSearch(&$app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name)
    {
      case "sprachen_list":
      $allowed['sprachen'] = array('list');

      $heading = array('ISO', 'Bezeichnung DE', 'Bezeichnung EN', 'Aktiv', 'Men&uuml;');
      $width = array('9%', '30%', '30%','10%','1%');

      $findcols = array('iso', 'bezeichnung_de', 'bezeichnung_en', "if(aktiv=1,'ja','nein')", 'id');
      $searchsql = array('iso', 'bezeichnung_de', 'bezeichnung_en');

      $defaultorder = 1;
      $defaultorderdesc = 0;
      $auchinaktive = (int)$app->YUI->TableSearchFilter($name, 1, 'auchinaktive', 0,0,'checkbox');
      $menu = "<a href=\"index.php?module=sprachen&action=edit&id=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=sprachen&action=delete&id=%value%\")><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";

      $where = " id > 0 ".($auchinaktive?'':' AND aktiv = 1');

      $sql = "SELECT SQL_CALC_FOUND_ROWS id, iso, bezeichnung_de, bezeichnung_en, if(aktiv=1,'ja','nein'), id FROM sprachen";

      
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
    $this->app=&$app;
    if($intern)return;
    $this->app->ActionHandlerInit($this);

    // ab hier alle Action Handler definieren die das Modul hat
    $this->app->ActionHandler("list", "SprachenList");
    $this->app->ActionHandler("create", "SprachenCreate");
    $this->app->ActionHandler("edit", "SprachenEdit");
    $this->app->ActionHandler("delete", "SprachenDelete");

    $this->app->ActionHandlerListen($app);

    $this->Install();


  }


  function Install(){
    $this->app->erp->CheckTable("sprachen");
    $this->app->erp->CheckColumn("id", "int(11)", "sprachen", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("iso", "varchar(2)", "sprachen", "NOT NULL");
    $this->app->erp->CheckColumn("bezeichnung_de", "varchar(255)", "sprachen", "NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn("bezeichnung_en", "varchar(255)", "sprachen", "NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn("alias", "varchar(255)", "sprachen", "NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn("aktiv", "tinyint(1)", "sprachen", "NOT NULL DEFAULT '0'");
    $arr = array('DE'=>array('Deutsch','German','deutsch','1'),
    'EN'=>array('Englisch','English','englisch','1'),
    'FR'=>array('Französisch','French','franzoesisch','1'),
    'NL'=>array('Holländisch','Dutch','hollaendisch','1'),
    'IT'=>array('Italienisch','Italian','italienisch','1'),
    'ES'=>array('Spanisch','Spanish','spanisch','1')
    );
    foreach($arr as $k => $v)
    {
      $check = $this->app->DB->SelectArr("SELECT * FROM sprachen WHERE iso = '$k' LIMIT 1");
      if($check)
      {
        if($check[0]['alias'] != $v[2])$this->app->DB->Update("UPDATE sprachen SET alias = '".$v[2]."', aktiv = 1 WHERE id = '".$check[0]['id']."' LIMIT 1");
      }else{
        $this->app->DB->Insert("INSERT INTO sprachen (iso, bezeichnung_de,bezeichnung_en, alias, aktiv) VALUES ('".$k."','".$v[0]."','".$v[1]."','".$v[2]."','1')");
      }
    }
  }


  function SprachenMenu()
  {
    $this->app->erp->MenuEintrag("index.php?module=sprachen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function SprachenList()
  {
    $this->app->erp->MenuEintrag("index.php?module=sprachen&action=create","Neue Sprache");
    $this->app->erp->MenuEintrag("index.php?module=sprachen&action=list","&Uuml;bersicht");

    $this->app->YUI->TableSearch('TAB1','sprachen_list', "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse("PAGE","sprachen_list.tpl");
  }

  function SprachenEdit()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $submit = $this->app->Secure->GetPOST('submitsprache');

    $this->app->Tpl->Add('KURZUEBERSCHRIFT2',$bezeichnung_de);



    $input = array();

    $this->app->erp->MenuEintrag("index.php?module=sprachen&action=edit&id=$id","Details");

    $this->app->erp->MenuEintrag("index.php?module=sprachen&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $input = $this->GetInput();
    $error = '';
    if(is_numeric($id) && $submit!=''){
      if($input['iso']=='' || strlen(trim($input['iso'])) > 2) $error .= 'Geben Sie bitte einen zweistelligen ISO-Code ein.<br>';
      if($input['bezeichnung_de'] == '') $error .= 'Geben Sie bitte eine deutsche Sprachbezeichnung ein.<br>';
      if($input['bezeichnung_en'] =='') $error .= 'Geben Sie bitte eine englische Sprachbezeichnung ein.<br>';

      if($this->app->DB->Select("SELECT '1' FROM sprachen WHERE iso='{$input['iso']}' AND id <> '$id' LIMIT 1")=='1')
        $error .= "Es existiert bereits ein ISO-Code mit diesem Namen.<br />";

      if($error!=''){
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">$error</div>");
      }else{
        if($error == ""){
          $this->app->DB->Update("UPDATE sprachen SET iso='{$input['iso']}', bezeichnung_de='{$input['bezeichnung_de']}', bezeichnung_en='{$input['bezeichnung_en']}' , aktiv='{$input['aktiv']}' WHERE id = '$id' LIMIT 1");
          $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
        }
      }
    }
    $iso = $this->app->DB->Select("SELECT iso FROM sprachen WHERE id = '$id'");
    $bezeichnung_de = $this->app->DB->Select("SELECT bezeichnung_de FROM sprachen WHERE id = '$id'");
    $bezeichnung_en = $this->app->DB->Select("SELECT bezeichnung_en FROM sprachen WHERE id = '$id'");
    if($id)
    {
      $aktiv = $this->app->DB->Select("SELECT aktiv FROM sprachen WHERE id = '$id'");
    }else{
      $aktiv = 1;
    }
    $this->app->Tpl->Set('ISO', $iso);
    $this->app->Tpl->Set('BEZEICHNUNG_DE', $bezeichnung_de);
    $this->app->Tpl->Set('BEZEICHNUNG_EN', $bezeichnung_en);
    if($aktiv)$this->app->Tpl->Set('AKTIV', ' checked="checked" ');
    //$this->SetInput($input);

    $this->app->Tpl->Parse('PAGE', "sprachen_edit.tpl");
  }

  function SprachenDelete()
  {
    $ref = $_SERVER['HTTP_REFERER'];
    $id = $this->app->Secure->GetGET("id");
    $iso = $this->app->DB->Select("SELECT iso FROM sprachen WHERE id = '$id' LIMIT 1");
    if(in_array($iso,array('DE','EN')))
    {
      $this->app->DB->Update("UPDATE sprachen SET aktiv = 0 WHERE id = '$id' LIMIT 1");
    }else{
      $this->app->DB->Delete("DELETE FROM sprachen WHERE id = '$id' LIMIT 1");
    }
    //$this->VorlageList();
    header("Location: $ref");
    exit;
  }

  function SprachenCreate(){
    //		$this->app->Tpl->Add(KURZUEBERSCHRIFT,"Benutzer");
    $this->app->erp->MenuEintrag("index.php?module=sprachen&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $input = $this->GetInput();
    $submit = $this->app->Secure->GetPOST('submitsprache');

    if($submit!='') {

      $error = '';

      if($input['iso']=='' || strlen(trim($input['iso'])) > 2) $error .= 'Geben Sie bitte einen zweistelligen ISO-Code ein.<br>';
      if($input['bezeichnung_de']=='') $error .= 'Geben Sie bitte eine deutsche Sprachbezeichnung ein.<br>';
      if($input['bezeichnung_en']=='') $error .= 'Geben Sie bitte eine englische Sprachbezeichnung ein.<br>';

      if($this->app->DB->Select("SELECT '1' FROM sprachen WHERE iso='{$input['iso']}' LIMIT 1")=='1')
        $error .= "Es existiert bereits ein ISO-Code mit diesem Namen.<br />";

      if($error!=''){
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">$error</div>");
      }else {
        $this->app->DB->Insert("INSERT INTO sprachen (iso, bezeichnung_de, bezeichnung_en, aktiv) VALUES ('{$input['iso']}', '{$input['bezeichnung_de']}', '{$input['bezeichnung_en']}','{$input['aktiv']}')");
        $newid = $this->app->DB->GetInsertID();
        $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Der Spracheintrag wurde erfolgreich angelegt.</div>");
        header("Location: index.php?module=sprachen&action=edit&id=$newid&msg=$msg");
        exit;
      }
    }

    $this->SetInput($input);

    $this->app->Tpl->Parse('PAGE', "sprachen_edit.tpl");
  }


  function GetInput(){
    $input = array();
    $input['iso'] = $this->app->Secure->GetPOST('iso');
    $input['bezeichnung_de'] = $this->app->Secure->GetPOST('bezeichnung_de');
    $input['bezeichnung_en'] = $this->app->Secure->GetPOST('bezeichnung_en');
    $input['aktiv'] = (int)$this->app->Secure->GetPOST('aktiv');
    return $input;

  }

  function SetInput($input){
    $this->app->Tpl->Set('ISO', $input['iso']);
    $this->app->Tpl->Set('BEZEICHNUNG_DE', $input['bezeichnung_de']);
    $this->app->Tpl->Set('BEZEICHNUNG_EN', $input['bezeichnung_en']);
    if($input['aktiv'] || $this->app->Secure->GetGET('action')=='create')$this->app->Tpl->Set('AKTIV', ' checked="checked" ');    
  }

}
