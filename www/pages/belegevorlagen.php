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
class Belegevorlagen 
{
  static function TableSearch(&$app, $name, $erlaubtevars)
  {
    switch($name)
    {
      case "belegevorlagen_list":
        $heading = array('Bezeichnung','Belegtyp','Projekt','Men&uuml;');
        $width = array('40%','20%','29%','1%');
        $findcols = array('b.bezeichnung','b.belegtyp','pr.abkuerzung','b.id');
        $searchsql = array('b.bezeichnung','b.belegtyp','pr.abkuerzung');
        $menu = "<table><tr><td nowrap><a href=\"#\" onclick=\"deletevorlage(%value%);\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a></td></tr></table>";      
        $sql = "SELECT SQL_CALC_FOUND_ROWS b.id, b.bezeichnung,CONCAT(UCASE(LEFT(b.belegtyp, 1)), SUBSTRING(b.belegtyp, 2)), pr.abkuerzung, b.id  FROM belegevorlagen b LEFT JOIN projekt pr ON b.projekt = pr.id";
        $where = $app->erp->ProjektRechte('b.projekt');      
      break;
      case "belegevorlagen_list2":
        $belegtyp = $app->Secure->GetGET('smodule');
        $heading = array('Bezeichnung','Projekt','Men&uuml;');
        $width = array('50%','49%','1%');
        $findcols = array('b.bezeichnung','pr.abkuerzung','b.id');
        $searchsql = array('b.bezeichnung','pr.abkuerzung');
        $menu = "<table><tr><td nowrap><a href=\"#\" onclick=\"loadbelegvorlage(%value%);\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=\"deletevorlage(%value%);\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a></td></tr></table>";      
        $sql = "SELECT SQL_CALC_FOUND_ROWS b.id, b.bezeichnung, pr.abkuerzung, b.id  FROM belegevorlagen b LEFT JOIN projekt pr ON b.projekt = pr.id";
        $where = "belegtyp = '$belegtyp' ".$app->erp->ProjektRechte('b.projekt');
      break;
    }
    
    $erg = false;
    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v))$erg[$v] = $$v;
    }
    return $erg; 
  }
  function __construct(&$app, $intern = false)
  {
    $this->app=&$app;
    $this->artikel = $this->app->erp->GetKonfiguration('gesamtrabatt_artikel');
    if($intern)return;
    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler("list","BelegevorlagenList");
    $this->app->ActionHandler("einstellungen","BelegevorlagenEinstellungen");
    $this->app->DefaultActionHandler("list");
    $this->app->ActionHandlerListen($app);
  }
  
  function BelegevorlagenMenu(){
    $this->app->erp->MenuEintrag("index.php?module=belegevorlagen&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=belegevorlagen&action=einstellungen","Einstellungen");
    
  }

  function BelegevorlagenList()
  {
    if($this->app->Secure->GetGET('cmd') == 'delvorlage')
    {
      $id = (int)$this->app->Secure->GetPOST('lid');
      $this->app->DB->Delete("DELETE FROM belegevorlagen WHERE id = '$id' LIMIT 1");
      echo json_encode(array('status'=>1));
      exit;
    }
    $this->BelegevorlagenMenu();
    $this->app->YUI->TableSearch('TAB1', "belegevorlagen_list", "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE','belegevorlagen_list.tpl');
  }
  
  function BelegevorlagenEinstellungen()
  {
    $this->BelegevorlagenMenu();

    $this->app->Tpl->Set('PREISEAKTUALISIEREN',$this->app->erp->GetKonfiguration('belegevorlagen_preiseaktualisieren')=='on'?'checked':'');
    $this->app->YUI->AutoSaveKonfiguration('preiseaktualisieren','belegevorlagen_preiseaktualisieren');
      
    $this->app->Tpl->Parse('PAGE','belegevorlagen_einstellungen.tpl');
  }

  function Install()
  {
    $this->app->erp->CheckTable('belegevorlagen');
    $this->app->erp->CheckColumn("id","int(11)","belegevorlagen","DEFAULT '0' NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("belegtyp", "varchar(255)", "belegevorlagen", "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn("bezeichnung", "varchar(255)", "belegevorlagen", "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn("projekt", "int(11)", "belegevorlagen", "DEFAULT '0' NOT NULL");
    $this->app->erp->CheckColumn("json", "MEDIUMTEXT", "belegevorlagen", "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn("bearbeiter", "varchar(255)", "belegevorlagen", "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn("zeitstempel", "timestamp", "belegevorlagen","DEFAULT CURRENT_TIMESTAMP NOT NULL");
    
    $this->app->erp->RegisterHook('BelegPositionenButtons', 'belegevorlagen', 'BelegevorlagenBelegPositionenButtons');
    $this->app->erp->RegisterHook('AARLGPositionen_cmds_end', 'belegevorlagen', 'BelegevorlagenAARLGPositionen_cmds_end');
    $this->app->erp->RegisterHook('ajax_filter_hook1', 'belegevorlagen', 'Belegevorlagenajax_filter_hook1');
  }
  
  function Belegevorlagenajax_filter_hook1($filtername,&$newarr, $term, $term2, $term3)
  {
    if($filtername == 'belegvorlagen')
    {
      $arr = $this->app->DB->SelectArr("SELECT CONCAT(b.id,' ',b.bezeichnung) as bezeichnung FROM belegevorlagen  b
         WHERE (b.bezeichnung LIKE '%$term%')  ".$this->app->erp->ProjektRechte('b.projekt'));
      if($arr)
      {
        for($i=0;$i<count($arr);$i++)
          $newarr[] = $arr[$i]['bezeichnung'];
      }
    }
  }
  
  function BelegevorlagenBelegPositionenButtons($target, $module, $id)
  {
    if($module=="angebot" || $module=="auftrag" || $module=="rechnung" || $module=="lieferschein" || $module=="gutschrift" || $module=="proformarechnung")
    {
      $this->app->Tpl->Set('ID', $id);
      $this->app->Tpl->Set('MODULE', $module);
      $this->app->YUI->AutoComplete('bestehendevorlage','belegvorlagen');
      $this->app->YUI->TableSearch('BELEGEVORLAGENTABELLE', "belegevorlagen_list2", "show","","",basename(__FILE__), __CLASS__);
      $this->app->Tpl->Add($target, "<input type=\"button\" id=\"belegevorlagen\" value=\"Belegevorlagen\">&nbsp;".$this->app->Tpl->Parse($target,'belegevorlagen_widget.tpl'));
    }
  }
  
  function BelegevorlagenAARLGPositionen_cmds_end($id){
    $module = $this->app->Secure->GetGET('module');
    if(!$module)return;

    $projekt = $this->app->DB->Select("SELECT projekt FROM $module WHERE id='$id' LIMIT 1");
    if($projekt <=0) $projekt=0;

    if($this->app->Secure->GetGET('cmd') == 'deletebelegvorlage')
    {
      $status = 1;
      $lid = (int)$this->app->Secure->GetPOST('lid');
      $this->app->DB->Delete("DELETE FROM belegevorlagen WHERE id = '$lid' AND belegtyp = '$module' LIMIT 1");
      echo json_encode(array('status'=>$status));
      exit;
    }
    
    if($this->app->Secure->GetGET('cmd') == 'loadbelegvorlage')
    {
      $status = 0;
      $lid = (int)$this->app->Secure->GetPOST('lid');
      $json = (String)$this->app->DB->Select("SELECT json FROM belegevorlagen WHERE id = '$lid' AND belegtyp = '$module' LIMIT 1");
      if($json !== '')
      {
        $json = json_decode($json, true);
        $maxsort = (int)$this->app->DB->Select("SELECT max(sort) FROM $module"."_position WHERE $module = '$id' LIMIT 1");
        if(isset($json['positionen']))
        {
          foreach($json['positionen'] as $v)
          {
            $v[$module] = $id;
            if($this->app->erp->GetKonfiguration('belegevorlagen_preiseaktualisieren')=='on'){
              if($v['artikel'] != '0'){
                $v['preis'] = $this->app->erp->GetVerkaufspreis($v['artikel'],$v['menge']);
              }
            }
            $v['sort'] += $maxsort;
            $this->app->DB->Insert("INSERT INTO $module"."_position (id) VALUES ('')");
            $idnew = $this->app->DB->GetInsertID();
            $oldtonew[$v['id']] = $idnew;
            if($v['explodiert_parent'] && isset($oldtonew) && isset($oldtonew[$v['explodiert_parent']]))$v['explodiert_parent'] = $oldtonew[$v['explodiert_parent']];
            unset($v['id']);
            $this->app->DB->UpdateArr($module.'_position',$idnew,"id",$v, true);
            if(is_null($v['steuersatz']))$this->app->DB->Update("UPDATE ".$module."_position SET steuersatz = NULL WHERE id = '$idnew' LIMIT 1");
          }
        }
        if(isset($json['zwischenpositionen']))
        {
          $maxpos = $this->app->DB->SelectArr("SELECT id,sort FROM beleg_zwischenpositionen WHERE doctype = '$module' AND doctypeid = '$id' AND pos='$maxsort' ORDER BY sort DESC LIMIT 1");
          if($maxpos)
          {
            $sortoffset = 1 + $maxpos[0]['sort'];
          }else{
            $sortoffset = 0;
          }
          foreach($json['zwischenpositionen'] as $v)
          {
            if($v['pos'] == 0)$v['sort'] += $sortoffset;
            $v['doctypeid'] = $id;
            $v['pos'] += $maxsort;
            unset($v['id']);
            $this->app->DB->Insert("INSERT INTO beleg_zwischenpositionen (id) VALUES ('')");
            $idnew = $this->app->DB->GetInsertID();
            $this->app->DB->UpdateArr('beleg_zwischenpositionen',$idnew,"id",$v, true);
          }
        }
        $status = 1;
        $this->app->erp->ANABREGSNeuberechnen($id,$module);
      }
      echo json_encode(array('status'=>$status));
      exit;
    }
    if($this->app->Secure->GetGET('cmd') == 'savebelegevorlage')
    {
      $json = null;
      $status = 0;
      $bestehendevorlage = (int)reset(explode(' ',$this->app->Secure->GetPOST('bestehendevorlage')));
      $bezeichnung = (String)$this->app->Secure->GetPOST('bezeichnung');
      $vorlagetyp = $this->app->Secure->GetPOST('vorlagetyp');
      $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
      $lid = null;
      if($vorlagetyp == 'neu')
      {
        if($bezeichnung !== '')
        {
          $this->app->DB->Insert("INSERT INTO belegevorlagen (bezeichnung, belegtyp, bearbeiter, zeitstempel,projekt) VALUES ('$bezeichnung','$module','$bearbeiter',now(),'$projekt')");
          $lid = $this->app->DB->GetInsertID();
        }
      }else{
        $lid = $this->app->DB->Select("SELECT id FROM belegevorlagen WHERE id = '$bestehendevorlage' LIMIT 1");
        if($lid && $bezeichnung !== '')$this->app->DB->Update("UPDATE belegevorlagen set bezeichnung = '$bezeichnung' WHERE id = '$bestehendevorlage' LIMIT 1");
      }
      if($lid)
      {
        $json['positionen'] = $this->app->DB->SelectArr("SELECT * FROM $module"."_position WHERE $module = '$id' ORDER BY sort");
        $json['zwischenpositionen'] = $this->app->DB->SelectArr("SELECT * FROM beleg_zwischenpositionen WHERE doctype = '$module' AND doctypeid = '$id' ORDER BY pos, sort");
        $json = $this->app->DB->real_escape_string(json_encode($json));
        $this->app->DB->Update("UPDATE belegevorlagen set json = '$json', zeitstempel = now(), bearbeiter = '$bearbeiter' WHERE id = '$lid' LIMIT 1");
        $status = 1;
      }
      echo json_encode(array('status'=>$status));
      exit;
    }
  }
}
?>
