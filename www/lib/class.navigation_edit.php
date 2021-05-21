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

class Navigation {

  var $alert;
  var $FormDisabled;
  var $ArrNav;
  var $ArrDeep;
  var $actNavID;
  var $actBezeichnung;
  var $actBezeichnungEN;
  var $actPlugin;
  var $actSecure;
  var $actSecureUser;
  var $actSecurePass;
  var $actPageID;
  var $actExternal;
  var $actLink;
  var $actTitle;
  var $actMail;
  var $actTarget;
  var $actCSSLink;
  var $actCSSHover;
  var $actCSSactive;
  var $actCSSvisited;
  var $actImgLink;
  var $actImgHover;
  var $actImgactive;
  var $actImgvisited;
  var $actPos;
  var $actCompoint;
  var $NewMode;
  var $app;
  var $shop;
	

  function __construct($app,$shop)
  {
    $this->app = $app;


    
    $shop = $this->app->Secure->GetGET("shop");
    $this->shop=$shop;

    $form = $this->app->Secure->GetPOST("form");
    $formget = $this->app->Secure->GetGET("form");

    $this->actNavID = $form[actNavID];
    if(isset($form[btnSave])) $this->SaveNav();
    $this->SetVar();
    // Button methods
    if(isset($formget[btnNew])) $this->NewNav();
    if(isset($formget[btnDel])) $this->DelNav();
    if(isset($form[btnLeft])) $this->PosLeft();
    if(isset($form[btnRight])) $this->PosRight();
    if(isset($form[btnUp])) $this->PosUp();
    if(isset($form[btnDown])) $this->PosDown();
    $this->ArrNav = $this->GetArrNav();
    if(count($this->ArrNav)>0)if($this->actNavID=="") $this->actNavID = key($this->ArrNav);
    $this->SetVar();
    $this->ArrowButton(); 
  }

  function GetArrNav()
  {
    $form = $this->app->Secure->GetPOST("form");
    foreach($this->app->DB->SelectArr("SELECT id FROM shopnavigation WHERE parent='0' AND shop='".$this->shop."' ORDER BY position") as $compoint){
      $ArrNav[$compoint[id]]="";
      foreach($this->app->DB->SelectArr("SELECT id FROM shopnavigation WHERE parent='".$compoint[id]."' AND shop='".$this->shop."' ORDER BY position") as $firstnav){
        $ArrNav[$compoint[id]][$firstnav[id]]="";
        foreach($this->app->DB->SelectArr("SELECT id FROM shopnavigation WHERE parent='".$firstnav[id]."' AND shop='".$this->shop."' ORDER BY position") as $secnav){
          $ArrNav[$compoint[id]][$firstnav[id]][$secnav[id]]="";
          foreach($this->app->DB->SelectArr("SELECT id FROM shopnavigation WHERE parent='".$secnav[id]."' AND shop='".$this->shop."' ORDER BY position") as $thirdnav){
            $ArrNav[$compoint[id]][$firstnav[id]][$secnav[id]][$thirdnav[id]]="";
          }
        }
      }
    }
    return $ArrNav;
  }


  function GetSelectNav($id="",$mode="nav")
  {
    $form = $this->app->Secure->GetPOST("form");
    $return ="";
    $homenum=1;
    $firstnavnum=1;
    $secnavnum=1;
    $thirdnavnum=1;

    $ArrNav = $this->GetArrNav();

    if(is_array($ArrNav)){
      foreach(array_keys($ArrNav) as $home){
        $return .= $this->GetOptionNav($home,1,$id,$homenum,$mode);
        if(is_array($ArrNav[$home])){
          foreach(array_keys($ArrNav[$home]) as $firstnav){
            $return .= $this->GetOptionNav($firstnav,2,$id,$homenum.'.'.$firstnavnum,$mode);
            if(is_array($ArrNav[$home][$firstnav])){
              foreach(array_keys($ArrNav[$home][$firstnav]) as $secnav){
                $return .= $this->GetOptionNav($secnav,3,$id,$homenum.'.'.$firstnavnum.'.'.$secnavnum,$mode);
                if(is_array($ArrNav[$home][$firstnav][$secnav])){
                  foreach(array_keys($ArrNav[$home][$firstnav][$secnav]) as $thirdnav){
                    $return .=
                    $this->GetOptionNav($thirdnav,4,$id,$homenum.'.'.$firstnavnum.'.'.$secnavnum.'.'.$thirdnavnum,$mode);
                    $thirdnavnum++;
                  }
                }
                $secnavnum++;$thirdnavnum=1;
              }
            }
            $firstnavnum++;$secnavnum=1;$thirdnavnum=1;
          }
        }
        $homenum++;$firstnavnum=1;$secnavnum=1;$thirdnavnum=1;
      }
    }
    return $return;
  }

  function GetOptionNav($id,$deep,$actID,$dot="",$mode="nav")
  {
    $form = $this->app->Secure->GetPOST("form");
    switch($deep){
      case 1: $color="#FFCC99";
              break;
      case 2: $color="#fffacd";
              break;
      case 3: $color="#e6e6fa";
              break;
      case 4: $color="#b0c4de";
              break;
      default: $color="#FFFFFF";
    }


    //check if side is saved with pass
    //if($this->CheckNavSecure($id))$secure = "(Passwort Bereich)";

      if($mode=='page'){
         $pageid = $this->app->DB->Select("SELECT plugin FROM shopnavigation WHERE id='".$id."' AND shop='".$this->shop."' LIMIT 1");
         if($pageid==$actID) $selected = "selected";

         if(is_numeric($pageid)){
	    $pagedescription = $this->app->DB->Select("SELECT description FROM page WHERE id='".$pageid."' LIMIT 1");
            return "<option value=\"$pageid\" style=\"background-color:$color;\" $selected>$space$dot&nbsp;&nbsp;".
            $space.$pagedescription." ($pageid)";
         }

      } else {
        if($id==$actID) $selected = "selected";
        return "<option value=\"$id\" style=\"background-color:$color;\"
        $selected>$space$dot&nbsp;&nbsp;".$space.$this->GetNavValue($id)."&nbsp;$secure";
      }
  }

  function GetNavValue($id)
  {
    return $this->app->DB->Select("SELECT bezeichnung FROM shopnavigation WHERE id='".$id."' LIMIT 1");
  }


  function DelNav(){
    $form = $this->app->Secure->GetPOST("form");
    $formget = $this->app->Secure->GetGET("form");
    if(count($this->app->DB->SelectArr("SELECT id FROM shopnavigation WHERE parent='".$this->actNavID."' AND shop='".$this->shop."'"))==0){
      if(count($this->app->DB->Select("SELECT id FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position > '".($this->actPos)."' AND shop='".$this->shop."'"))>0){
	foreach($this->app->DB->SelectArr("SELECT id,position FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position >'".($this->actPos)."' AND shop='".$this->shop."' ORDER BY position") as $key => $id){
          $this->app->DB->Update("UPDATE shopnavigation SET position='".($id[position]-1)."' WHERE id='".$id[id]."' AND shop='".$this->shop."'");
      	}
      }

      // seiten verknuepfung anpassen
//      $this->app->DB->Update("UPDATE page SET nav_id='".$this->actCompoint."' WHERE nav='".$this->actNavID."'");
      $this->app->DB->Delete("DELETE FROM shopnavigation WHERE id='".$formget[btnDel]."' AND shop='".$this->shop."' LIMIT 1");
			
      $id = $this->app->DB->Select("SELECT id FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position ='".($this->actPos-1)."' AND shop='".$this->shop."'");
      if(is_numeric($id)){ $this->actNavID = $id;}
      else {
	if($this->actCompoint!=0){
	  $this->actNavID = $this->actCompoint;
	}
	else {
	  $this->actNavID = $this->app->DB->Select("SELECT id FROM shopnavigation WHERE AND position ='1' AND shop='".$this->shop."' LIMIT 1"); 
	}
      }
    }else {
      $this->Alert("Navigation ".$this->actBezeichnung." kann nicht gelöscht werden! Entfernen Sie zuerst alle Unternavigationen.");
    }	
  }


  function NewNav(){
    $form = $this->app->Secure->GetPOST("form");
    $formget = $this->app->Secure->GetGET("form");

    if(count($this->app->DB->Select("SELECT id FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position > '".($this->actPos)."' AND shop='".$this->shop."'"))>0){
      foreach($this->app->DB->SelectArr("SELECT id,position FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position >'".($this->actPos)."' AND shop='".$this->shop."' ORDER BY position") as $key => $id){
        $this->app->DB->Update("UPDATE shopnavigation SET position='".($id[position]+1)."' WHERE id='".$id[id]."' AND shop='".$this->shop."'");
      }	
    }
    
    // save plugin
    $this->app->DB->Insert("INSERT INTO shopnavigation (id,parent,position,bezeichnung,plugin,shop) VALUES ('','".$this->actCompoint."','".($this->actPos+1)."','".$formget[btnNew]."','".$newPage."',".$this->shop.")");
    $this->actNavID = $this->app->DB->GetInsertID();

    // change page description and nav description = desc  (page_id);
    //$this->app->DB->Update("UPDATE page SET description='".$form[btnNew]."', nav_id='".$this->actNavID."' WHERE id='".$newPage."' LIMIT 1");

  }

  function SaveNav(){
    $form = $this->app->Secure->GetPOST("form");
    $this->app->DB->Update("UPDATE shopnavigation SET bezeichnung='".$form[bezeichnung]."' WHERE id='".$this->actNavID."' AND shop='".$this->shop."'");
    $this->app->DB->Update("UPDATE shopnavigation SET bezeichnung_en='".$form[bezeichnung_en]."' WHERE id='".$this->actNavID."' AND shop='".$this->shop."'");
   
    $this->app->DB->Update("UPDATE shopnavigation SET plugin='".$form[plugin]."' WHERE id='".$this->actNavID."' AND shop='".$this->shop."'");

    if($form[plugin]=="artikel") 
      $this->app->DB->Update("UPDATE shopnavigation SET pluginparameter='".$form[parameter_artikel]."' WHERE id='".$this->actNavID."' AND shop='".$this->shop."'");
    if($form[plugin]=="gruppe") 
      $this->app->DB->Update("UPDATE shopnavigation SET pluginparameter='".$form[parameter_gruppe]."' WHERE id='".$this->actNavID."' AND shop='".$this->shop."'");
    if($form[plugin]=="PageID") 
      $this->app->DB->Update("UPDATE shopnavigation SET pluginparameter='".$form[parameter_inhalt]."' WHERE id='".$this->actNavID."' AND shop='".$this->shop."'");
		if($form[plugin]=="Link")
      $this->app->DB->Update("UPDATE shopnavigation SET pluginparameter='".$form['Link']."' WHERE id='".$this->actNavID."' AND shop='".$this->shop."'");
		if($form[plugin]=="External")
      $this->app->DB->Update("UPDATE shopnavigation SET pluginparameter='".$form['External']."' WHERE id='".$this->actNavID."' AND shop='".$this->shop."'");

  }

  function PosLeft(){
    $form = $this->app->Secure->GetPOST("form");
    if($this->actDeep > 1){
      $newcompoint = $this->app->DB->Select("SELECT parent FROM shopnavigation WHERE id='".$this->actCompoint."' AND shop='".$this->shop."' LIMIT 1");
      $newpos = $this->app->DB->Select("SELECT position FROM shopnavigation WHERE id='".$this->actCompoint."' AND shop='".$this->shop."' LIMIT 1")+1;
      if($newpos!="" && $newcompoint!=""){
	$tmppos = $newpos;	
    	if(count($this->app->DB->Select("SELECT id FROM shopnavigation WHERE parent='".$newcompoint."' AND position >='".$tmppos."' AND shop='".$this->shop."'"))>0){
          foreach($this->app->DB->SelectArr("SELECT id FROM shopnavigation WHERE parent='".$newcompoint."' AND position >='".$tmppos."' AND shop='".$this->shop."' ORDER BY position") as $key => $id){
            $tmppos++;
            //echo "danach (main) id $id[0] com = $newcompoint position ".$tmppos."<br>";
            $this->app->DB->Update("UPDATE shopnavigation SET position='".$tmppos."' WHERE id='".$id[id]."' AND shop='".$this->shop."'");
          }
        }
	//echo "new position $newpos  new com $newcompoint ".$this->actNavID."<br>";
	$sql = "UPDATE shopnavigation SET position='".$newpos."',parent='".$newcompoint."' WHERE id='".$this->actNavID."' AND shop='".$this->shop."'";
	$this->app->DB->Update($sql);
      }	
      $pos = 0;
      if(count($this->app->DB->Select("SELECT id FROM shopnavigation WHERE parent='".$this->actNavID."' AND shop='".$this->shop."'"))>0){
        foreach($this->app->DB->SelectArr("SELECT id FROM shopnavigation WHERE parent='".$this->actNavID."' AND shop='".$this->shop."' ORDER BY position") as $key => $id){
          $pos++;
	  //echo "unternav  id $id[0] position ".$pos."<br>";
          $this->app->DB->Update("UPDATE shopnavigation SET position='".$pos."' WHERE id='".$id[id]."' AND shop='".$this->shop."'");
        }
      }
      if(count($this->app->DB->Select("SELECT id FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position > '".$this->actPos."' AND shop='".$this->shop."'"))>0){
	foreach($this->app->DB->SelectArr("SELECT id,position FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position > '".$this->actPos."' AND shop='".$this->shop."' ORDER BY position") as $key => $id){ 
	  $pos++;
	  //echo "danach anpassen (intern) id $id[0] position ".$pos." com ".$this->actNavID."<br>";	
	  $this->app->DB->Update("UPDATE shopnavigation SET position='".$pos."', parent='".$this->actNavID."' AND shop='".$this->shop."' WHERE id='".$id[id]."'");
	}
      }
    }
  }

  function PosRight(){
    $form = $this->app->Secure->GetPOST("form");
    $newcompoint = $this->app->DB->Select("SELECT id FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position = '".($this->actPos-1)."' AND shop='".$this->shop."' LIMIT 1");
    //echo "com ".$this->actCompoint." position ".($this->actPos)."<br>"; 
    $newpos = $this->app->DB->Count("SELECT id FROM shopnavigation WHERE parent='".$newcompoint."' AND shop='".$this->shop."'") + 1;

    //echo "newcompoint $newcompoint new position $newpos<br>";

    if($newcompoint!=""){   
      if($newpos!="" && $newcompoint!=""){
	  $this->app->DB->Update("UPDATE shopnavigation SET position='".$newpos."', parent='".$newcompoint."' WHERE id='".$this->actNavID."'  AND shop='".$this->shop."'");
	  //echo("UPDATE shopnavigation SET position='".$newpos."', parent='".$newcompoint."' WHERE id='".$this->actNavID."'  AND shop='".$this->shop."'");
      }
      if(count($this->app->DB->Select("SELECT id FROM shopnavigation WHERE parent='".$this->actNavID."' AND shop='".$this->shop."'")) > 0){
	foreach($this->app->DB->SelectArr("SELECT id FROM shopnavigation WHERE parent='".$this->actNavID."' AND shop='".$this->shop."' ORDER BY position") as $key => $id){ 
	  //echo "id {$id[id]} position ".++$newpos." newcom $newcompoint<br>";
	  //$this->app->DB->Update("UPDATE shopnavigation SET position='".(++$newpos)."',parent='".$newcompoint."' WHERE id='".$id[id]."' AND shop='".$this->shop."'");
	}
      }
      if(count($this->app->DB->Select("SELECT id FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position > '".$this->actPos."' AND shop='".$this->shop."'")) > 0){
	$newpos = $this->actPos;
      	foreach($this->app->DB->SelectArr("SELECT id FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position > '".$this->actPos."' AND shop='".$this->shop."' ORDER BY position") as $key => $id){
          //echo "id {$id[id]} position ".$newpos++."<br>";
          $this->app->DB->Update("UPDATE shopnavigation SET position='".($newpos++)."' WHERE id='".$id[id]."' AND shop='".$this->shop."'");
          //echo("UPDATE shopnavigation SET position='".($newpos++)."' WHERE id='".$id[id]."' AND shop='".$this->shop."'");
      	}
      }
    }	
  }
  
  function PosUp(){
    $form = $this->app->Secure->GetPOST("form");
    // id where position > actPos
    $pos = $this->app->DB->Select("SELECT position FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position = '".($this->actPos-1)."' AND shop='".$this->shop."' LIMIT 1");
    $id = $this->app->DB->Select("SELECT id FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position = '".($this->actPos-1)."' AND shop='".$this->shop."' LIMIT 1"); 
    if(is_numeric($pos)){
      $this->app->DB->Update("UPDATE shopnavigation SET position='".$this->actPos."' WHERE parent='".$this->actCompoint."' AND id='".$id."' AND shop='".$this->shop."'"); 
      $this->app->DB->Update("UPDATE shopnavigation SET position='".$pos."' WHERE parent='".$this->actCompoint."' AND id='".$this->actNavID."' AND shop='".$this->shop."'"); 
    }	
  }
	
  function PosDown(){
    $form = $this->app->Secure->GetPOST("form");
    // id where position > actPos
    $pos = $this->app->DB->Select("SELECT position FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position = '".($this->actPos+1)."' AND shop='".$this->shop."' LIMIT 1"); 
    $id = $this->app->DB->Select("SELECT id FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position = '".($this->actPos+1)."' AND shop='".$this->shop."' LIMIT 1"); 
    //echo "new position ".$pos." alt position ".$this->actPos;	
    if(is_numeric($pos)){ 
      $this->app->DB->Update("UPDATE shopnavigation SET position='".$this->actPos."' WHERE parent='".$this->actCompoint."' AND id='".$id."' AND shop='".$this->shop."'"); 
      $this->app->DB->Update("UPDATE shopnavigation SET position='".$pos."' WHERE parent='".$this->actCompoint."' AND id='".$this->actNavID."' AND shop='".$this->shop."'"); 
    }	
  }

  function SetVar(){
    $form = $this->app->Secure->GetPOST("form");
    if($this->actNavID!=""){
      $this->actBezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM shopnavigation WHERE id='".$this->actNavID."' AND shop='".$this->shop."' LIMIT 1");	
      $this->actBezeichnungEN = $this->app->DB->Select("SELECT bezeichnung_en FROM shopnavigation WHERE id='".$this->actNavID."' AND shop='".$this->shop."' LIMIT 1");	
      $this->actPos = $this->app->DB->Select("SELECT position FROM shopnavigation WHERE id='".$this->actNavID."' AND shop='".$this->shop."' LIMIT 1");	
      $this->actCompoint = $this->app->DB->Select("SELECT parent FROM shopnavigation WHERE id='".$this->actNavID."' AND shop='".$this->shop."' LIMIT 1");	
      $compoint = $this->actNavID;
      $count = 0;

      while($compoint > 0){
	//sammel alle navigationen mit diesem compoint
	$count++;
	$compoint = $this->app->DB->Select("SELECT parent FROM shopnavigation WHERE id = '".$compoint."' AND shop='".$this->shop."'");
      }
      $this->actDeep = $count;
		
      $this->actPlugin = $this->app->DB->Select("SELECT plugin FROM shopnavigation WHERE id='".$this->actNavID."' AND shop='".$this->shop."' LIMIT 1");
      $this->actTarget = $this->app->DB->Select("SELECT target FROM shopnavigation WHERE id='".$this->actNavID."' AND shop='".$this->shop."' LIMIT 1");
      $this->actPluginparameter = $this->app->DB->Select("SELECT pluginparameter FROM shopnavigation WHERE id='".$this->actNavID."' AND shop='".$this->shop."' LIMIT 1");
    }

    // Link	
    if($this->actPlugin=="artikel"){
      $this->checkedArtikel="checked";
      $this->actPluginparameterArtikel = $this->actPluginparameter;
    } 
    elseif($this->actPlugin=="gruppe"){ 
      $this->actPluginparameterGruppe = $this->actPluginparameter;
      $this->checkedGruppe="checked";
    }
    elseif($this->actPlugin=="PageID"){ 
      $this->actPluginparameterInhalt= $this->actPluginparameter;
      $this->checkedInhalt="checked";
    }
		elseif($this->actPlugin=="Link"){
      $this->actLink= $this->actPluginparameter;
      $this->checkedLink="checked";
    }
		elseif($this->actPlugin=="External"){
      $this->actExternal= $this->actPluginparameter;
      $this->checkedExternal="checked";
    }

    else { 
      $this->checkedArtikel="checked";
    }
  }
  
  function Alert($alert){ $this->alert .='<script>window.setTimeout("alert(\''.$alert.'\')",1);</script>';}


  function ArrowButton(){
    $form = $this->app->Secure->GetPOST("form");
    $up=true;
    $down = true;
    $left = true;
    $right = true;
    //echo "pos ".$this->actPos." deep ".$this->actDeep;
    if($this->actNavID!=""){
      if($this->actDeep=="2" && $this->actPos=="1") $right=false;
      if($this->actDeep=="3" && $this->actPos=="1") $right=false;
      if($this->actDeep=="1" && $this->actPos=="1"){ $up = false; $right=false; $left=false; }
      if($this->actDeep=="1"){   $left=false; }
      if($this->actDeep=="4"){   $right=false; }
		
      if(!is_numeric($this->app->DB->Select("SELECT position FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position = '".($this->actPos+1)."' AND shop='".$this->shop."' LIMIT 1")))$down = false; 
      if(!is_numeric($this->app->DB->Select("SELECT position FROM shopnavigation WHERE parent='".$this->actCompoint."' AND position = '".($this->actPos-1)."' AND shop='".$this->shop."' LIMIT 1")))$up = false; 
    }else {
      $up=false;$down=false;$left=false;$right=false;
    }	
    if($up) $this->up = 'style="width:21;background: url(./themes/[THEME]/images/nav/up.png);background-repeat: no-repeat;"';	
    else $this->up = 'disabled style="width:21;background: url(./themes/[THEME]/images/nav/disup.png);background-repeat: no-repeat;"';
    if($down) $this->down = 'style="width:21;background: url(./themes/[THEME]/images/nav/down.png);background-repeat: no-repeat;"';
    else $this->down = 'disabled style="width:21;background: url(./themes/[THEME]/images/nav/disdown.png);background-repeat: no-repeat;"';
    if($left) $this->left = 'style="width:21;background: url(./themes/[THEME]/images/nav/left.png);background-repeat: no-repeat;"';
    else $this->left = 'disabled style="width:21;background: url(./themes/[THEME]/images/nav/disleft.png);background-repeat: no-repeat;"';
    if($right) $this->right = 'style="width:21;background: url(./themes/[THEME]/images/nav/right.png);background-repeat: no-repeat;"';
    else $this->right = 'disabled style="width:21;background: url(./themes/[THEME]/images/nav/disright.png);background-repeat: no-repeat;"';
  }	

  function Template(){
    $form = $this->app->Secure->GetPOST("form");
    if($this->actNavID=="")$this->FormDisabled="disabled";
    if($this->NewMode){
      $tpl .='<form name="scnavigation" action="index.php?module=shopexport&action=navigation&id=[ID]&shop='.$this->shop.'" method="post">';	
    } else {
      $tpl .='<form name="scnavigation" action="index.php?module=shopexport&action=navigation&id=[ID]&shop='.$this->shop.'" method="post">';	
    }
    $tpl .='
    <table border="0" cellpadding="0" cellspacing="5" width="650">
      <tr valign="top">
        <td width="180" align="center">
        <!-- left colum  -->
        <fieldset><legend><font >Navigations-Struktur</font></legend>
        <table border="0" cellpadding="0" cellspacing="0" height="450">
          <tr>
            <td align="center"> 
              <select size="30" style="width:300;"  name="form[actNavID]" onchange="submit();">
              '.$this->GetSelectNav($this->actNavID).'</select>';
     if(!$this->NewMode){
	  $tpl .='<table border="0" cellpadding="0" cellspacing="0">
	              <tr><td></td><td>&nbsp;</td><td></td></tr>
	  <tr><td></td>
	  <td align="center">
	  <input type="submit" '.$this->up.'  value="" name="form[btnUp]"></td>
	  <td></td></tr>
	  <tr><td>
	  <input type="submit" '.$this->left.'  value="" name="form[btnLeft]"></td>
	  </td>
	  <td></td>
	  <td align="center">
	  <input type="submit" '.$this->right.'  value="" name="form[btnRight]"></td>
	  </td>
	  </tr>
	  <tr><td></td>
	  <td align="center">
	  <input type="submit" '.$this->down.'  value="" name="form[btnDown]"></td>
	  </td>
	  <td></td></tr>
	  <tr><td></td><td>&nbsp;</td><td></td></tr>
	  </table>';
      }	
	$tpl .='</td></tr></table></fieldset>	
	  </td>
	  <td>
	  <!-- right colum -->
	  <!-- edit box -->
	  <fieldset><legend><font >Eigenschaften</font></legend>';
	$tpl .='<table border="0" cellpadding="0" cellspacing="5" valign="top">
	  <tr><td rowspan="7">&nbsp;</td><td width="80">
          <font >Name (DE): </font></td><td><input type="text"  value="'.$this->actBezeichnung.'" name="form[bezeichnung]">
          </td></tr>
	  <tr><td width="50">
          <font >Name (EN): </font></td><td><input type="text"  value="'.$this->actBezeichnungEN.'" name="form[bezeichnung_en]">
          </td></tr>

	  <tr valign="top">
	  <td><font >Link: </font></td>
	  <td>
	  <table border="0" cellpadding="0" cellspacing="0">

 	  <tr>
	  <td nowrap><input type="radio" name="form[plugin]" value="artikel"  '.$this->checkedArtikel.'> 
	  <font>Artikel</font></td>
	  <td nowrap><input type="text" name="form[parameter_artikel]" value="'.$this->actPluginparameterArtikel.'"></td>
	  </tr>
	  <tr>
	  <td nowrap><input type="radio" name="form[plugin]" value="gruppe"  '.$this->checkedGruppe.'> 
	  <font>Gruppe</font></td>
	  <td nowrap><input type="text" name="form[parameter_gruppe]" value="'.$this->actPluginparameterGruppe.'"></td>
	  </tr>

 	  <tr>
	  <td nowrap><input type="radio" name="form[plugin]" value="PageID"  '.$this->checkedInhalt.'> 
	  <font>Inhalt</font></td>
	  <td nowrap><input type="text" name="form[parameter_inhalt]" value="'.$this->actPluginparameterInhalt.'"></td>
	  </tr>

	  <tr>
	  <td nowrap><input type="radio" name="form[plugin]" value="Link"  '.$this->checkedLink.'> 
	  <font >Link</font>&nbsp;</td>
          <td><input type="text"  value="'.$this->actLink.'" name="form[Link]"></td>
	  </tr>

	  <tr>
	  <td nowrap><input type="radio" name="form[plugin]" value="External"  '.$this->checkedExternal.'> 
	  <font>externe Seite</font>&nbsp;</td>
          <td><input type="text"  value="'.$this->actExternal.'" name="form[External]"></td>
	  </tr>


	  </table>
	  </td>
	  </tr>';
	$tpl.='
	  <!--<tr valign="top"><td>
	  <font >Ziel: </font></td><td><input type="text"  name="form[actTarget]" size="10" value="'.$this->actTarget.'">
	  <select name="targetdefault" 
          onClick="document.scnavigation.elements[\'form[actTarget]\'].value=document.scnavigation.targetdefault.value;"
          onChange="document.scnavigation.elements[\'form[actTarget]\'].value=document.scnavigation.targetdefault.value;
	  document.scnavigation.elements[\'form[actTarget]\'].focus();">
	  <option value="self">gleiches Fenster</option>
	  <option value="_blank">neues Fenster &ouml;ffnen</option>
	  </select>
	  </td></tr>-->
	  
	  <tr><td colspan="2" height="50%">&nbsp;</td></tr> 
          <tr><td align="center" colspan="2" nowrap><br><br>
	  <input type="submit"  value="Speichern" name="form[btnSave]" '.$this->FormDisabled.'>
	  <input  type="button" onclick="str = prompt(\'Geben Sie einen Namen f&uuml;r die neue Navigation ein:\',\'\');if(str!=null & str!=\'\'){
          document.forms.scnavigation.action=\'index.php?module=shopexport&action=navigation&shop='.$this->shop.'&id=[ID]&form[btnNew]=\'+str;
          document.forms.scnavigation.submit();}" value="Neue Navigation einf&uuml;gen" name="form[btnNew]">
	  <input  type="button"
	  onclick="if(confirm(\'M&ouml;chten Sie die Navigation '.$this->actBezeichnung.' wirklich l&ouml;schen?\')){
	  document.forms.scnavigation.action=\'index.php?module=shopexport&action=navigation&shop='.$this->shop.'&id=[ID]&form[btnDel]='.$this->actNavID.'\';
	  document.forms.scnavigation.submit();}" value="Navigation L&ouml;schen" name="form[btnDel]" '.$this->FormDisabled.'>
	  </td></tr>
	  </table></td></tr></table></form>';

	$tpl .=$this->alert;	
      return $tpl;
  }


  function Get(){
    return $this->Template();
  }


}

