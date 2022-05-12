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

class Datenbankbereinigen {
  var $app;

  static function TableSearch(&$app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name)
    {
      case "lager_list":
        $allowed['datenbankbereinigen'] = array('list');

        $heading = array('Artikelnummer', 'Artikelname', 'Menge', 'Men&uuml;');
        $width = array('9%', '40%', '10%','1%');

        $findcols = array('a.nummer', 'a.name_de', 'l.menge', 'l.id');
        $searchsql = array('a.nummer', 'a.name_de', 'l.menge');

        $defaultorder = 1;
        $defaultorderdesc = 0;

        $menu = "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=datenbankbereinigen&action=lagerdelete&id=%value%\")><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";

        $where = " l.id > 0 AND l.lager_platz NOT IN (SELECT id FROM lager_platz)";

        $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, a.nummer, a.name_de, l.menge, l.id FROM lager_platz_inhalt l JOIN artikel a on l.artikel = a.id";

        $count = "SELECT count(l.id) FROM lager_platz_inhalt l WHERE $where";
        break;

      case "artikel_list":
        $allowed['datenbankbereinigen'] = array('list');

        $heading = array('Artikelnummer', 'Artikelname', 'Men&uuml;');
        $width = array('20%', '40%', '1%');

        $findcols = array('a.nummer', 'a.name_de', 'a.id');
        $searchsql = array('a.nummer', 'a.name_de');

        $defaultorder = 1;
        $defaultorderdesc = 0;

        $menu = '<a href="javascript:;" onclick="ArtikelwiederherstellenEdit(%value%);">';
          $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\">";
        $menu .= "</a>";

        $where = " a.id > 0 AND a.geloescht = 1";

        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer, a.name_de, a.id FROM artikel a";
        
        $count = "SELECT count(a.id) FROM artikel a WHERE $where";
        break;

      case "adressen_list":
        $allowed['datenbankbereinigen'] = array('list');

        $heading = array('Kundennummer', 'Lieferantennummer', 'Name', 'Straße', 'PLZ', 'Ort', 'Land', 'Men&uuml;');
        $width = array('10%', '10%', '30%', '10%', '10%', '10%', '10%', '1%');

        $findcols = array('a.kundennummer', 'a.lieferantennummer', 'a.name', 'a.strasse', 'a.plz', 'a.ort', 'a.land', 'a.id');
        $searchsql = array('a.kundennummer', 'a.lieferantennummer', 'a.name', 'a.strasse', 'a.plz', 'a.ort', 'a.land');

        $defaultorder = 1;
        $defaultorderdesc = 0;

        $menu = '<a href="javascript:;" onclick="AdressewiederherstellenEdit(%value%);">';
          $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\">";
        $menu .= "</a>";

        $where = " a.id > 0 AND a.geloescht = 1";

        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.kundennummer, a.lieferantennummer, a.name, a.strasse, a.plz, a.ort, a.land, a.id FROM adresse a";

        $count = "SELECT count(a.id) FROM adresse a WHERE $where";
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
    $this->app->ActionHandler("list", "DatenbankbereinigenList");
    $this->app->ActionHandler("edit", "DatenbankbereinigenEdit");
    $this->app->ActionHandler("lagerdelete", "DatenbankbereinigenLagerDelete");
    $this->app->ActionHandler("lager", "DatenbankbereinigenLager");
    $this->app->ActionHandler("adresse", "DatenbankbereinigenAdressen");
    $this->app->ActionHandler("artikel", "DatenbankbereinigenArtikel");
    $this->app->ActionHandler("artikelwiederherstellen", "ArtikelwiederherstellenEdit");
    $this->app->ActionHandler("artikelwiederherstellensave", "ArtikelwiederherstellenSave");
    $this->app->ActionHandler("adressewiederherstellen", "AdressewiederherstellenEdit");
    $this->app->ActionHandler("adressewiederherstellensave", "AdressewiederherstellenSave");

    $this->app->ActionHandlerListen($app);


  }

  function DatenbankbereinigenMenu()
  {
    $this->app->erp->MenuEintrag("index.php?module=datenbankbereinigen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function DatenbankbereinigenList()
  {
    $this->app->erp->MenuEintrag("index.php?module=datenbankbereinigen&action=list","&Uuml;bersicht");

    $vorschau = $this->app->Secure->GetPOST("vorschau");
    
    if($vorschau != ""){
      $option = $this->app->Secure->GetPOST("optionen");
      //$this->app->erp->LogFile("OPTION".$option);
      if($option == "lager"){
        //$this->app->YUI->TableSearch('TAB1','lager_list', "show","","",basename(__FILE__), __CLASS__);
        //$this->app->Tpl->Set("LAGERSELECTED", "selected");
        header("Location: index.php?module=datenbankbereinigen&action=lager");
      }elseif($option == "adresse"){
        //$this->app->YUI->TableSearch('TAB1','adressen_list', "show","","",basename(__FILE__), __CLASS__);
        //$this->app->Tpl->Set("ADRESSESELECTED", "selected");
        header("Location: index.php?module=datenbankbereinigen&action=adresse");
      }elseif($option == "artikel"){
        //$this->app->YUI->TableSearch('TAB1','artikel_list', "show","","",basename(__FILE__), __CLASS__);
        //$this->app->Tpl->Set("ARTIKELSELECTED", "selected");
        header("Location: index.php?module=datenbankbereinigen&action=artikel");
      }
    } 
    

   
    $this->app->Tpl->Parse("PAGE","datenbankbereinigen_list.tpl");
  }

  function DatenbankbereinigenLager()
  {
    $vorschau = $this->app->Secure->GetPOST("vorschau");

    if($vorschau != ""){
      $this->auswerten();
    }

    $this->app->Tpl->Set("LAGERSELECTED", "selected");
    $this->app->YUI->TableSearch('TAB1','lager_list', "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse("PAGE","datenbankbereinigen_list.tpl");
  }

  function DatenbankbereinigenArtikel()
  {
    $vorschau = $this->app->Secure->GetPOST("vorschau");

    if($vorschau != ""){
      $this->auswerten();
    }

    $this->app->Tpl->Set("ARTIKELSELECTED", "selected");
    $this->app->YUI->TableSearch('TAB1','artikel_list', "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse("PAGE","datenbankbereinigen_list.tpl");
  }

  function DatenbankbereinigenAdressen()
  {
    $vorschau = $this->app->Secure->GetPOST("vorschau");

    if($vorschau != ""){
      $this->auswerten();
    }
    $this->app->Tpl->Set("ADRESSESELECTED", "selected");
    $this->app->YUI->TableSearch('TAB1','adressen_list', "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse("PAGE","datenbankbereinigen_list.tpl");
  }

  

  function DatenbankbereinigenLagerDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->DB->DELETE("DELETE FROM lager_platz_inhalt WHERE id = '$id' LIMIT 1");

    header("Location: index.php?module=datenbankbereinigen&action=lager");
    exit;
  }

  function ArtikelwiederherstellenEdit()
  {
    if($this->app->Secure->GetGET('cmd')=='get'){
      $id = (int)$this->app->Secure->GetPOST('id');

      $data = $this->app->DB->SelectArr("SELECT id FROM artikel WHERE id = '$id' LIMIT 1");
      
      if($data){
        $data = reset($data);

        $bisherigenummer = $this->app->DB->Select("SELECT nummer FROM angebot_position WHERE artikel = '$id' LIMIT 1");
        if($bisherigenummer == "" || $bisherigenummer <= 0){
          $bisherigenummer = $this->app->DB->Select("SELECT nummer FROM auftrag_position WHERE artikel = '$id' LIMIT 1");
          if($bisherigenummer == "" || $bisherigenummer <= 0){
            $bisherigenummer = $this->app->DB->Select("SELECT nummer FROM gutschrift_position WHERE artikel = '$id' LIMIT 1");
            if($bisherigenummer == "" || $bisherigenummer <= 0 ){
              $bisherigenummer = $this->app->DB->Select("SELECT nummer FROM lieferschein_position WHERE artikel = '$id' LIMIT 1");
              if($bisherigenummer == "" || $bisherigenummer <= 0){
                $bisherigenummer = $this->app->DB->Select("SELECT nummer FROM rechnung_position WHERE artikel = '$id' LIMIT 1");
              }
            }
          }
        }

        if($bisherigenummer != ""){
          $data['bisherigenummer'] = $bisherigenummer;
        }else{
          $data['bisherigenummer'] = "";
        }


      }
      echo json_encode($data);
      exit;
    }

    $this->app->Tpl->Parse('PAGE', "datenbankbereinigen_list.tpl");
  }

  function ArtikelwiederherstellenSave()
  {

    $id = (int)$this->app->Secure->GetPOST('id');
    $artikelid = $this->app->Secure->GetPOST('artikelid');
    $neuenummer = trim($this->app->Secure->GetPOST('neuenummer'));

    $error = "";

    if(trim($neuenummer) == ""){
      $error .= "Bitte neue Nummer ausfüllen\n";
    }

    if($id == "" || $id <= 0){
      $error .= "Kein Artikel gefunden\n";
    }

    $nummervergeben = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer = '$neuenummer' LIMIT 1");
    if($nummervergeben != ""){
      $error .= "Neue Nummer ist schon vergeben";
    }

        
    if($error == ""){
      $this->app->DB->Update("UPDATE artikel SET geloescht = 0, nummer = '$neuenummer' WHERE id = '$id'");

      echo json_encode(array('status'=>1));
      exit;
      

    }else{
      echo json_encode(array('status'=>0,'statusText'=>$error));
      exit;

    }    
  
  }


  function AdressewiederherstellenEdit()
  {
    if($this->app->Secure->GetGET('cmd')=='get'){
      $id = (int)$this->app->Secure->GetPOST('id');

      $data = $this->app->DB->SelectArr("SELECT id, kundennummer, lieferantennummer, mitarbeiternummer FROM adresse WHERE id = '$id' LIMIT 1");
      
      if($data){
        $data = reset($data);

        $kundennummer = substr($data['kundennummer'], 4);
        $lieferantennummer = substr($data['lieferantennummer'], 4);
        $mitarbeiternummer = substr($data['mitarbeiternummer'], 4);

        if($kundennummer == "" || $kundennummer <= 0){
          $belegkvorhanden = $this->app->DB->Select("SELECT kundennummer FROM angebot WHERE adresse = '$id' LIMIT 1");
          if($belegkvorhanden == "" || $belegkvorhanden <= 0){
            $belegkvorhanden = $this->app->DB->Select("SELECT kundennummer FROM auftrag WHERE adresse = '$id' LIMIT 1");
            if($belegkvorhanden == "" || $belegkvorhanden <= 0){
              $belegkvorhanden = $this->app->DB->Select("SELECT kundennummer FROM bestellung WHERE adresse = '$id' LIMIT 1");
              if($belegkvorhanden == "" || $belegkvorhanden <= 0){
                $belegkvorhanden = $this->app->DB->Select("SELECT kundennummer FROM gutschrift WHERE adresse = '$id' LIMIT 1");
                if($belegkvorhanden == "" || $belegkvorhanden <= 0){
                  $belegkvorhanden = $this->app->DB->Select("SELECT kundennummer FROM lieferschein WHERE adresse = '$id' LIMIT 1");
                  if($belegkvorhanden == "" || $belegkvorhanden <= 0){
                    $belegkvorhanden = $this->app->DB->Select("SELECT kundennummer FROM rechnung WHERE adresse = '$id' LIMIT 1");
                  }
                }
              }
            }
          }
        }

        if(($kundennummer == "" || $kundennummer <= 0) && ($belegkvorhanden == "" || $belegkvorhanden <= 0)){
          $data['kundennummer'] = "";
        }else{
          $data['kundennummer'] = $kundennummer;
        }

        if($lieferantennummer == "" || $lieferantennummer <= 0){
          $beleglvorhanden = $this->app->DB->Select("SELECT lieferantennummer FROM bestellung WHERE adresse = '$id' LIMIT 1");
        }

        if(($lieferantennummer == "" || $lieferantennummer <= 0) && ($beleglvorhanden == "" || $beleglvorhanden <= 0)){
          $data['lieferantennummer'] = "";
        }else{
          $data['lieferantennummer'] = $lieferantennummer;
        }        
        
        if($mitarbeiternummer == "" || $mitarbeiternummer <= 0){
          $data['mitarbeiternummer'] = "";
        }else{
          $data['mitarbeiternummer'] = $mitarbeiternummer;
        }

      }
      echo json_encode($data);
      exit;
    }

    $this->app->Tpl->Parse('PAGE', "datenbankbereinigen_list.tpl");
  }



  function AdressewiederherstellenSave()
  {
    $id = (int)$this->app->Secure->GetPOST('eid');
    //$this->app->erp->LogFile("ID:".$id);
    $neuenummerk = trim($this->app->Secure->GetPOST('neuenummerk'));
    $neuenummerl = trim($this->app->Secure->GetPOST('neuenummerl'));
    $neuenummerm = trim($this->app->Secure->GetPOST('neuenummerm'));

    //$this->app->erp->LogFile(print_r($_POST, true));

    $error = "";

    if(trim($neuenummerk) == "" && trim($neuenummerl) == "" && trim($neuenummerm)){
      $error .= "Mindestens eine neue Nummer muss vergeben werden\n";
    }

    $rollekvorhanden = $this->app->DB->Select("SELECT id FROM adresse_rolle WHERE subjekt = 'Kunde' AND adresse = '$id' LIMIT 1");
    $rollelvorhanden = $this->app->DB->Select("SELECT id FROM adresse_rolle WHERE subjekt = 'Lieferant' AND adresse = '$id' LIMIT 1");
    $rollemvorhanden = $this->app->DB->Select("SELECT id FROM adresse_rolle WHERE subjekt = 'Mitarbeiter' AND adresse = '$id' LIMIT 1");

    if($id == "" || $id <= 0){
      $error .= "Keine Adresse gefunden\n";
    }

    if($rollekvorhanden != "" && $neuenummerk == ""){
      $error .= "Adresse war Kunde. Bitte neue Kundennummer ausfüllen.\n";
    }

    if($rollelvorhanden != "" && $neuenummerl == ""){
      $error .= "Adresse war Lieferant. Bitte neue Lieferantennummer ausfüllen.\n";
    }

    if($rollemvorhanden != "" && $neuenummerm == ""){
      $error .= "Adresse war Mitarbeiter. Bitte neue Mitarbeiternummer ausfüllen.\n";
    }

    $knummervergeben = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer = '$neuenummerk' AND kundennummer != '' LIMIT 1");
    if($knummervergeben != ""){
      $error .= "Neue Kundennummer ist schon vergeben.\n";
    }

    $lnummervergeben = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer = '$neuenummerl' AND lieferantennummer != ''LIMIT 1");
    if($lnummervergeben != ""){
      $error .= "Neue Lieferantennummer ist schon vergeben.\n";
    }

    $mnummervergeben = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer = '$neuenummerm' AND mitarbeiternummer != '' LIMIT 1");
    if($mnummervergeben != ""){
      $error .= "Neue Mitarbeiternummer ist schon vergeben.\n";
    }

        
    if($error == ""){
      $this->app->DB->Update("UPDATE adresse SET kundennummer = $neuenummerk, lieferantennummer = '$neuenummerl', mitarbeiternummer = '$neuenummerm', geloescht = 0 WHERE id = '$id'");

      if(($rollekvorhanden == "" || $rollekvorhanden <= 0) && $neuenummerk != ""){
        $this->app->erp->AddRolleZuAdresse($id, "Kunde", "von", "Projekt", 0);
      }

      if(($rollelvorhanden == "" || $rollelvorhanden <= 0) && $neuenummerl != ""){
        $this->app->erp->AddRolleZuAdresse($id, "Lieferant", "von", "Projekt", 0);
      }

      if(($rollemvorhanden == "" || $rollemvorhanden <= 0) && $neuenummerm != ""){
        $this->app->erp->AddRolleZuAdresse($id, "Mitarbeiter", "von", "Projekt", 0);
      }



      echo json_encode(array('status'=>1));
      exit;
      
    }else{
      echo json_encode(array('status'=>0,'statusText'=>$error));
      exit;

    }    
  }



  
  function auswerten()
  {
    $option = $this->app->Secure->GetPOST("optionen");
    //$this->app->erp->LogFile("OPTION".$option);
    if($option == "lager"){
      //$this->app->YUI->TableSearch('TAB1','lager_list', "show","","",basename(__FILE__), __CLASS__);
      //$this->app->Tpl->Set("LAGERSELECTED", "selected");
      header("Location: index.php?module=datenbankbereinigen&action=lager");
    }elseif($option == "adresse"){
      //$this->app->YUI->TableSearch('TAB1','adressen_list', "show","","",basename(__FILE__), __CLASS__);
      //$this->app->Tpl->Set("ADRESSESELECTED", "selected");
      header("Location: index.php?module=datenbankbereinigen&action=adresse");
    }elseif($option == "artikel"){
      //$this->app->YUI->TableSearch('TAB1','artikel_list', "show","","",basename(__FILE__), __CLASS__);
      //$this->app->Tpl->Set("ARTIKELSELECTED", "selected");
      header("Location: index.php?module=datenbankbereinigen&action=artikel");
    }
  }

  

}
