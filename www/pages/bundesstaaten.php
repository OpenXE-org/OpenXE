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

class Bundesstaaten {
  /** @var erpooSystem $app */
  var $app;

  static function TableSearch(&$app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name)
    {
      case "bundesstaaten_list":
      $allowed['bundesstaaten'] = array('list');

      $heading = array('Land', 'ISO', 'Bundesstaat', 'Aktiv', 'Men&uuml;');
      $width = array('20%', '10%', '60%', '10%', '1%');

      $findcols = array('l.bezeichnung_de', 'b.iso', 'b.bundesstaat', "if(b.aktiv, 'ja', '-')", 'b.id');
      $searchsql = array('l.bezeichnung_de', 'b.iso', 'b.bundesstaat');

      $defaultorder = 1;
      $defaultorderdesc = 0;

      $menu = "<table cellpadding=0 cellspacing=0>";
        $menu .= "<tr>";
          $menu .= "<td nowrap>";
            $menu .= '<a href="javascript:;" onclick="BundesstaatenEdit(%value%);">';
              $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
            $menu .= "</a>&nbsp;";
            $menu .= '<a href="javascript:;" onclick="BundesstaatenDelete(%value%);">';
              $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
            $menu .= "</a>";
          $menu .= "</td>";
        $menu .= "</tr>";
      $menu .= "</table>";

      $finaktiv = $app->YUI->TableSearchFilter($name, 5, 'inaktiv', '0', 0, 'checkbox');
      $subwhere = '';
      if($finaktiv == 0){
        $subwhere .= ' AND b.aktiv = 1';
      }

      $where = " b.id > 0".$subwhere;

      $sql = "SELECT SQL_CALC_FOUND_ROWS b.id, l.bezeichnung_de, b.iso, b.bundesstaat, if(b.aktiv, 'ja', '-') as aktiv, b.id FROM bundesstaaten b LEFT JOIN laender l ON b.land = l.iso";

      $count = "SELECT count(b.id) FROM bundesstaaten b WHERE $where";
      break;

    }

    $erg = [];

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
    $this->app->ActionHandler("list", "BundesstaatenList");
    $this->app->ActionHandler("edit", "BundesstaatenEdit");
    $this->app->ActionHandler("save", "BundesstaatenSave");
    $this->app->ActionHandler("delete", "BundesstaatenDelete");
    
    $this->app->ActionHandlerListen($app);

    $this->Install();

  }

  function Install(){
    $this->app->erp->CheckTable("bundesstaaten");
    $this->app->erp->CheckColumn("id", "int(11)", "bundesstaaten", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("land", "varchar(255)", "bundesstaaten", "NOT NULL");
    $this->app->erp->CheckColumn("iso", "varchar(255)", "bundesstaaten", "NOT NULL");
    $this->app->erp->CheckColumn("bundesstaat", "varchar(255)", "bundesstaaten", "NOT NULL");
    $this->app->erp->CheckColumn("aktiv", "int(11)", "bundesstaaten", "NOT NULL DEFAULT 0");
  }

  /**
   * @param $target
   * @param $landid
   * @param $bundeslandid
   * @param string $valueland
   * @param string $valuebundesland
   * @param bool $disabled
   */
  function BundeslaenderSelect($target, $landid, $bundeslandid, $valueland = '', $valuebundesland = '', $disabled = false)
  {
    if($valueland == '')$valueland = $this->app->erp->Firmendaten('land');
    $arr = $this->app->DB->SelectArr("SELECT * FROM bundesstaaten WHERE land <> '' AND iso <> '' AND ( aktiv = 1 OR (land = '$valueland' AND iso = '$valuebundesland'))  ORDER BY land, bundesstaat");
    $this->app->Tpl->Add($target,'<select id="'.$bundeslandid.'" name="'.$bundeslandid.'"'.($disabled?' disabled="disabled" ':'').'><option value=""></option>');
    if($arr)
    {
      foreach($arr as $i => $v)
      {
        if($v['land'] == $valueland)
        {
          $this->app->Tpl->Add($target,'<option value="'.$v['iso'].'"'.($v['iso'] == $valuebundesland?' selected="selected" ':'').'>'.htmlspecialchars($v['bundesstaat']).'</option>');
        }
      }
    }
    $this->app->Tpl->Add($target,'</select>');
    $this->app->Tpl->Add($target,'<script type="text/javascript">');
    if($arr)
    {
      $this->app->Tpl->Add($target,"\r\nvar bundeslaender$bundeslandid =  new Array();\r\n");
      $i = 0;
      foreach($arr as $i => $v)
      {    
        $this->app->Tpl->Add($target,"\r\n bundeslaender$bundeslandid"."[".$i."] = new Object();\r\n");
        $this->app->Tpl->Add($target,"\r\n bundeslaender$bundeslandid"."[".$i."].land = '".htmlspecialchars($v['land'])."';\r\n");
        $this->app->Tpl->Add($target,"\r\n bundeslaender$bundeslandid"."[".$i."].iso = '".htmlspecialchars($v['iso'])."';\r\n");
        $this->app->Tpl->Add($target,"\r\n bundeslaender$bundeslandid"."[".$i."].bundesstaat = '".htmlspecialchars($v['bundesstaat'])."';\r\n");
        $i++;
      }
    }
    
    $this->app->Tpl->Add($target,'
    $(document).ready(function() {
      $(\'select[name="'.$landid.'"]\').on(\'change\', function(){
        var landsel = $(this).val();
        var bundeslandel = $(\'#'.$bundeslandid.'\');
        var bundeslandval = $(bundeslandel).val();
        $(bundeslandel).html(\'<option value=""></option>\');
        $(bundeslaender'.$bundeslandid.').each(function(k,v){
          if(landsel == v.land)
          {
            $(bundeslandel).html($(bundeslandel).html()+\'<option value="\'+v.iso+\'">\'+v.bundesstaat+\'</option>\');
          }
        });
        $(bundeslaender'.$bundeslandid.').val(bundeslandval);
      });
    });
    </script>');
  }

  function BundesstaatenMenu()
  {

    $this->app->erp->MenuEintrag("index.php?module=bundesstaaten&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=bundesstaaten&action=list","Details");
    $this->app->erp->MenuEintrag("BundesstaatenEdit(0)","Neuer Eintrag");
  }

  /**
   * @param string $needle
   * @param $haystack
   * @param bool $strict
   * @return bool
   */
  function in_array_r($needle, $haystack, $strict = false){
    foreach($haystack as $item){
      if(($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))){
        return true;
      }
    }

    return false;
  } 

  function BundesstaatenList()
  {
    $this->BundesstaatenMenu();
    $this->app->Tpl->Set("KURZUEBERSCHRIFT","Bundesstaaten");

    $bundesstaatende = $this->app->erp->GetSelectStaatenliste('DE');   
    $bundesstaatendedb = $this->app->DB->SelectArr("SELECT bundesstaat, iso FROM bundesstaaten WHERE land = 'DE'");
    $fehlende = array();
    foreach($bundesstaatende as $key=>$value){
      if($this->in_array_r($value,$bundesstaatendedb)){
      }else{
        $fehlende[$key] = $value;
      }
    }

    $bundesstaatenat = $this->app->erp->GetSelectStaatenliste('AT');
    $bundesstaatenatdb = $this->app->DB->SelectArr("SELECT bundesstaat, iso FROM bundesstaaten WHERE land = 'AT'");
    $fehlenat = array();
    foreach($bundesstaatenat as $key=>$value){
      if($this->in_array_r($value,$bundesstaatenatdb)){
      }else{
        $fehlenat[$key] = $value;
      }
    }

    $bundesstaatench = $this->app->erp->GetSelectStaatenliste('CH');
    $bundesstaatenchdb = $this->app->DB->SelectArr("SELECT bundesstaat, iso FROM bundesstaaten WHERE land = 'CH'");
    $fehlench = array();
    foreach($bundesstaatench as $key=>$value){
      if($this->in_array_r($value,$bundesstaatenchdb)){
      }else{
        $fehlench[$key] = $value;
      }
    }

    $bundesstaatenus = $this->app->erp->GetSelectStaatenliste('US');
    $bundesstaatenusdb = $this->app->DB->SelectArr("SELECT bundesstaat, iso FROM bundesstaaten WHERE land = 'US'");
    $fehlenus = array();
    foreach($bundesstaatenus as $key=>$value){
      if($this->in_array_r($value,$bundesstaatenusdb)){
      }else{
        $fehlenus[$key] = $value;
      }
    }

    $bundesstaatenca = $this->app->erp->GetSelectStaatenliste('CA');
    $bundesstaatencadb = $this->app->DB->SelectArr("SELECT bundesstaat, iso FROM bundesstaaten WHERE land = 'CA'");
    $fehlenca = array();
    foreach($bundesstaatenca as $key=>$value){
      if($this->in_array_r($value,$bundesstaatencadb)){
      }else{
        $fehlenca[$key] = $value;
      }
    }

    $anzahlfehlen = count($fehlende) + count($fehlenat) + count($fehlench) + count($fehlenus) + count($fehlenca);


    if($anzahlfehlen > 0){
      if($anzahlfehlen == 1){
        $info = "Es steht ".$anzahlfehlen." neuer Bundesstaaten Eintrag in Xentral zur Verfügung.";
      }else{
        $info = "Es stehen ".$anzahlfehlen." neue Bundesstaaten Einträge in Xentral zur Verfügung.";
      }      
      $nachladebutton = "<input type='submit' name='nachladen' id='nachladen' value='Jetzt nachladen'>";
      $info .= $nachladebutton;
      $this->app->Tpl->Set('MESSAGE',"<div class=\"info\">$info</div>");
    }

    $nachladen = $this->app->Secure->GetPOST("nachladen");
    if($nachladen != ""){
      if(count($fehlende) > 0){
        foreach($fehlende as $key=>$value){
          $this->app->DB->Insert("INSERT INTO bundesstaaten (land, iso, bundesstaat, aktiv) VALUES ('DE', '$key', '$value', 1)");
        }
      }

      if(count($fehlenat) > 0){
        foreach($fehlenat as $key=>$value){
          $this->app->DB->Insert("INSERT INTO bundesstaaten (land, iso, bundesstaat, aktiv) VALUES ('AT', '$key', '$value', 1)");
        }
      }

      if(count($fehlench) > 0){
        foreach($fehlench as $key=>$value){
          $this->app->DB->Insert("INSERT INTO bundesstaaten (land, iso, bundesstaat, aktiv) VALUES ('CH', '$key', '$value', 1)");
        }
      }

      if(count($fehlenus) > 0){
        foreach($fehlenus as $key=>$value){
          $this->app->DB->Insert("INSERT INTO bundesstaaten (land, iso, bundesstaat, aktiv) VALUES ('US', '$key', '$value', 1)");
        }
      }

      if(count($fehlenca) > 0){
        foreach($fehlenca as $key=>$value){
          $this->app->DB->Insert("INSERT INTO bundesstaaten (land, iso, bundesstaat, aktiv) VALUES ('CA', '$key', '$value', 1)");
        }
      }

      header("Location:index.php?module=bundesstaaten&action=list");
      exit;
    }

    $laenderanzahl = $this->app->DB->Select("SELECT COUNT(id) FROM laender");
    //if($laenderanzahl <= 0){
    $select = '';
    $laender = $this->app->erp->GetSelectLaenderliste();
    foreach($laender as $key=>$value){
      if(trim($key) != "" && trim($key) != "0"){
        $select .= "<option value='".$key."'>".htmlspecialchars($value)."</option>";
      }
        
    }
    //}else{
      //$laender = $this->app->DB->SelectArr("SELECT iso, bezeichnung_de FROM laender");
      //foreach($laender as $key=>$value){
        //$select .= "<option value='".$value['iso']."'>".$value['bezeichnung_de']."</option>";
      //}

    $this->app->Tpl->Add("LAENDER", $select);
    //}

    $this->app->YUI->TableSearch('TAB1','bundesstaaten_list', "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse("PAGE","bundesstaaten_list.tpl");
  }

  function BundesstaatenEdit()
  {
    if($this->app->Secure->GetGET('cmd')=='get'){
      $id = (int)$this->app->Secure->GetPOST('id');
      
      $data = $this->app->DB->SelectArr("SELECT b.id, b.land, b.iso, b.bundesstaat, b.aktiv FROM bundesstaaten b WHERE b.id = '$id' LIMIT 1");
      
      if($data){
        $data = reset($data);

      }else{
        $data['id'] = 0;
        $data['land'] = '';
        $data['iso'] = '';
        $data['bundesstaat'] = '';
        $data['aktiv'] = 1;

      }
      echo json_encode($data);
      exit;
    }

    $this->app->Tpl->Parse('PAGE', "bundesstaaten_list.tpl");
  }

  function BundesstaatenSave()
  { 
    $id = (int)$this->app->Secure->GetPOST('id');
    $land = trim($this->app->Secure->GetPOST('land'));
    $iso = trim($this->app->Secure->GetPOST('iso'));
    $bundesstaat = trim($this->app->Secure->GetPOST('bundesstaat'));
    $aktiv = $this->app->Secure->GetPOST('aktiv');

    $error = "";

    if($land == ""){
      $error .= "Bitte Land ausfüllen\n";
    }

    if($iso == ""){
      $error .= "Bitte ISO-Code ausfüllen"."\n";
    }

    if(strlen($iso) > 2){
      $error .= "Bitte 2-stelligen ISO-Code für den Bundesstaat ausfüllen"."\n";
    }

    if($bundesstaat == ""){
      $error .= "Bitte Bundesstaat ausfüllen"."\n";
    }
       
    if($land != "" && $bundesstaat != ""){
      $isovorhanden = $this->app->DB->Select("SELECT id FROM bundesstaaten WHERE land = '$land' AND iso = '$iso' AND id != '$id' LIMIT 1");
      if($isovorhanden != ""){
        $error .= "Es gibt diesen ISO-Code bereits für dieses Land"."\n";
      }else{
        $schonvorhanden = $this->app->DB->Select("SELECT id FROM bundesstaaten WHERE land = '$land' AND iso = '$iso' AND bundesstaat = '$bundesstaat' AND id != '$id' LIMIT 1");
        if($schonvorhanden != ""){
          $error .= "Diesen Eintrag gibt es bereits"."\n";
        }
      }
      
    }

    if($error == ""){
      if($id){
        $this->app->DB->Update("UPDATE bundesstaaten SET land = '$land', iso = '$iso', bundesstaat = '$bundesstaat', aktiv = '$aktiv' WHERE id = '$id'");

        echo json_encode(array('status'=>1));
        exit;
      }else{
        $this->app->DB->Insert("INSERT INTO bundesstaaten (land, iso, bundesstaat, aktiv) VALUES ('$land', '$iso', '$bundesstaat', '$aktiv')");

        echo json_encode(array('status'=>1));
        exit;          
       
      } 
    }else{
      echo json_encode(array('status'=>0,'statusText'=>$error));
      exit;

    }
    
  }

  function BundesstaatenDelete()
  {    
    $id = (int) $this->app->Secure->GetPOST('id');
    if($id)
      $this->app->DB->Update("DELETE FROM bundesstaaten WHERE id = '$id'");
        
    echo json_encode(array('status'=>1));
    exit;    
  
  }

}
