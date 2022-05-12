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

use Xentral\Components\Http\RedirectResponse;

class Shopexport
{
  /** @var Application $app */
  var $app;

  /**
   * Shopexport constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app,$intern = false)
  {
    $this->app=$app;
    if($intern) {
      return;
    }

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("login","ShopexportLogin");
    $this->app->ActionHandler("main","ShopexportMain");
    $this->app->ActionHandler("list","ShopexportList");
    $this->app->ActionHandler("export","ShopexportExport");
    $this->app->ActionHandler("artikeluebertragung","ShopexportArtikeluebertragung");
    $this->app->ActionHandler("adressuebertragung","ShopexportAdressuebertragung");
    $this->app->ActionHandler("artikeluebertragungdel","ShopexportArtikeluebertragungdel");
    $this->app->ActionHandler("adressuebertragungdel","ShopexportAdressuebertragungdel");
    $this->app->ActionHandler("besuchen","ShopexportBesuchen");
    $this->app->ActionHandler("navigation","ShopexportNavigation");
    $this->app->ActionHandler("logout","ShopexportLogout");
    $this->app->ActionHandler("navigationtab","ShopexportNavigationUebersicht");
    $this->app->ActionHandler("artikelgruppen","ShopexportArtikelgruppen");
    $this->app->ActionHandler("dateien","ShopexportDateien");
    $this->app->ActionHandler("live","ShopexportLive");

    $this->app->DefaultActionHandler("list");
    $this->app->ActionHandlerListen($app);
  }

  public function ShopexportList()
  {
    $this->app->Location->execute('index.php?module=importvorlage&action=uebersicht');
  }

  public function ShopexportLive()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->Headlines('Navigation');
    $this->ShopexportMenu();

    $url = $this->app->DB->Select("SELECT url FROM shopexport WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set('ID',$id);
    $this->app->Tpl->Set('URL',$url);

    $this->app->Tpl->Parse('PAGE','shopexport_live.tpl');
  } 

  public function Install()
  {
    $this->app->erp->CheckColumn('namenuebertragen','tinyint(1)','shopexport','DEFAULT -1');
    $this->app->erp->CheckColumn('beschreibunguebertragen','tinyint(1)','shopexport','DEFAULT -1');
    $this->app->erp->CheckColumn('verkaufspreis','tinyint(1)','shopexport','DEFAULT -1');
    $this->app->erp->CheckColumn('staffelpreiseuebertragen','tinyint(1)','shopexport','DEFAULT -1');
    $this->app->erp->CheckColumn('fremdsprachenuebertragen','tinyint(1)','shopexport','DEFAULT -1');
    $this->app->erp->CheckColumn('kategorienanlegen','tinyint(1)','shopexport','DEFAULT -1');
    $this->app->erp->CheckColumn('kategorienbaumuebertragen','tinyint(1)','shopexport','DEFAULT -1');
    $this->app->erp->CheckColumn('eanuebertragen','tinyint(1)','shopexport','DEFAULT -1');
    $this->app->erp->CheckColumn('zolltarifnummeruebertragen','tinyint(1)','shopexport','DEFAULT -1');
    $this->app->erp->CheckColumn('gewichtuebertragen','tinyint(1)','shopexport','DEFAULT -1');
    $this->app->erp->CheckColumn('herstelleruebertragen','tinyint(1)','shopexport','DEFAULT -1');
    $this->app->erp->CheckColumn('matrixeigenschaftenuebertragen','tinyint(1)','shopexport','DEFAULT -1');
  }

  protected function CheckEdit($id = null, $create = false)
  {
    if($id === null) {
      $id = $this->app->Secure->GetGET('id');
    }
    if(!is_numeric($id) || $id <= 0) {
      return;
    }
    $shopexportarr = $this->app->DB->SelectRow('SELECT * FROM shopexport WHERE id = '.$id.' LIMIT 1');
    if(empty($shopexportarr)) {
      return;
    }
    if($shopexportarr['shoptyp'] === '' || $shopexportarr['shoptyp'] === 'extern') {
      $obj = new ShopimporterBase();
    }
    elseif($shopexportarr['shoptyp'] === 'intern' && !empty($shopexportarr['modulename'])) {
      $obj = $this->app->erp->LoadModul($shopexportarr['modulename']);
    }
    if(empty($obj) || !method_exists($obj,'getArticleExportCapability')) {
      return;
    }
    $export = $obj->getArticleExportCapability();
    foreach($export as $k => $v) {
      if(isset($v['field']) && $shopexportarr[$v['field']] == -1) {
        if($create && isset($v['defaultoncreate'])) {
          $this->app->DB->Update("UPDATE shopexport SET '".$v['field']."' = ".(int)$v['defaultoncreate']." WHERE id = '$id' LIMIT 1");
        }
        elseif(isset($v['default'])) {
          if(is_numeric($v['default'])) {
            $this->app->DB->Update("UPDATE shopexport SET '".$v['field']."' = ".(int)$v['default']." WHERE id = '$id' LIMIT 1");
          }
          elseif(isset($shopexportarr[$v['default']])) {
            $this->app->DB->Update("UPDATE shopexport SET '".$v['field']."' = ".(int)$shopexportarr[$v['default']]." WHERE id = '$id' LIMIT 1");
          }
        }
        else{
          $this->app->DB->Update("UPDATE shopexport SET '".$v['field']."' = 0 WHERE id = '$id' LIMIT 1");
        }
      }
    }
  }

  protected function CheckCreate($id = null)
  {
    $this->CheckEdit($id, true);
  }

  public function ShopexportNavigationUebersicht()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->Headlines('Navigation');
    $this->ShopexportMenu();

    $navexport = $this->app->Secure->GetPOST('navexport');
    $this->app->Tpl->Set('ID',$id);
    $this->app->Tpl->Set('NAVEXPORT','<div class="info"> Navigation vom Shop &auml;ndern (interne Shop! kein XT & Co.).</div>');
    if(!empty($navexport)) {
      $this->app->remote->RemoteSendArtikelgruppen($id);
      $anzahl = $this->app->remote->RemoteSendNavigation($id);
      $this->app->erp->NewEvent('Navigations-Export Online-Shop Nr. '.$id,'onlineshop');
      $this->app->Tpl->Set('NAVEXPORT','<div class="error">Es wurden '.$anzahl.' Navigationen heraufgeladen.</div>');
    }

    $this->app->Tpl->Parse('PAGE','shopexport_navigation.tpl');
  } 

  public function ShopexportArtikelgruppen()
  {
    $this->app->erp->Headlines('Artikelgruppen');
    $this->ShopexportMenu();

    $shop = $this->app->Secure->GetGET('id');
    $edit = $this->app->Secure->GetGET('edit');
    $delete = $this->app->Secure->GetGET('delete');

    $bezeichnung = $this->app->Secure->GetPOST('bezeichnung');
    $bezeichnung_en = $this->app->Secure->GetPOST('bezeichnung_en');
    $beschreibung_de = $this->app->Secure->GetPOST('beschreibung_de');
    $beschreibung_en = $this->app->Secure->GetPOST('beschreibung_en');

    $aktiv = $this->app->Secure->GetPOST('aktiv');
    $submit = $this->app->Secure->GetPOST('anlegen');

    // Edit
    if(is_numeric($edit)) {
      if(!empty($submit) && is_numeric($shop)) {
        $this->app->DB->Update("UPDATE artikelgruppen SET bezeichnung='$bezeichnung', 
            bezeichnung_en='$bezeichnung_en', 
            beschreibung_de='$beschreibung_de', 
            beschreibung_en='$beschreibung_en', 
            shop='$shop', aktiv='$aktiv' WHERE id='$edit' LIMIT 1");
        $this->app->Location->execute('index.php?module=shopexport&action=artikelgruppen&id='.$shop.'#tabs-1');
      }

      $data = $this->app->DB->SelectArr("SELECT bezeichnung, bezeichnung_en, beschreibung_de, beschreibung_en, aktiv
          FROM artikelgruppen WHERE id='$edit' LIMIT 1");		
      if(is_array($data)) {
        $this->app->Tpl->Set('BEZEICHNUNG', $data[0]['bezeichnung']);
        $this->app->Tpl->Set('BESCHREIBUNG_DE', $data[0]['beschreibung_de']);
        $this->app->Tpl->Set('BESCHREIBUNG_EN', $data[0]['beschreibung_en']);
        $this->app->Tpl->Set('BEZEICHNUNGEN', $data[0]['bezeichnung_en']);
        $this->app->Tpl->Set('AKTIVCHECKED', (($data[0]['aktiv']=='1') ? 'checked' : ''));
      }
    }else{
      if(!empty($submit) && is_numeric($shop)) {
        $this->app->DB->Insert("INSERT INTO artikelgruppen (bezeichnung, bezeichnung_en, beschreibung_de,beschreibung_en, shop, aktiv) VALUES ('$bezeichnung', '$bezeichnung_en','$beschreibung_de','$beschreibung_en','$shop', '$aktiv')");
        $this->app->Location->execute('index.php?module=shopexport&action=artikelgruppen&id='.$shop.'#tabs-1');
      }
    }

    // Delete
    if(is_numeric($delete)) {
      $this->app->DB->Delete("DELETE FROM artikelgruppen WHERE id='$delete' LIMIT 1");
      $this->app->Location->execute('index.php?module=shopexport&action=artikelgruppen&id='.$shop);
    }

    // Table
    $table = new EasyTable($this->app);
    $this->app->Tpl->Set('INHALT','');
    $this->app->Tpl->Set('SUBSUBHEADING','Artikelgruppen');
    $table->Query("SELECT g.bezeichnung as artikelgruppe, g.id as gruppe, s.bezeichnung, if(g.aktiv,'online','') as aktiv, g.id FROM artikelgruppen g, 
        shopexport s WHERE s.firma='".$this->app->User->GetFirma()."' AND s.id=g.shop AND s.id=$shop");
    $table->DisplayNew('INHALT', "<a href=\"index.php?module=shopexport&action=artikelgruppen&edit=%value%&id=$shop#tabs-2\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.svg\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=shopexport&action=artikelgruppen&delete=%value%&id=$shop';\">
        <img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\"></a>");
    $this->app->Tpl->Set('EXTEND','');
    $this->app->Tpl->Parse('TABLE', 'rahmen70.tpl');

    $this->app->Tpl->Parse('PAGE','shopexport_artikelgruppen.tpl');
  }

  public function ShopexportDateien()
  {
    $this->app->erp->Headlines('Shopexport','Dateien');
    $this->ShopexportMenu();

    $id = $this->app->Secure->GetGET('id');
    $this->app->YUI->DateiUpload('PAGE','Banner',$id);
  }

  public function ShopexportMenu()
  {
    $id = $this->app->Secure->GetGET('id');

    $shopexportRow = $id <= 0?null: $this->app->DB->SelectRow(
      sprintf(
        'SELECT `bezeichnung`, `typ`, `shoptyp`, `modulename` FROM `shopexport` WHERE `id` = %d LIMIT 1',
        $id
      )
    );
    $name = empty($shopexportRow['bezeichnung'])?'': $shopexportRow['bezeichnung'];
    $this->app->erp->Headlines('',$name);
    /** @var Appstore $appstore */
    if(
      !empty($shopexportRow['shoptyp'])
      && $shopexportRow['shoptyp'] === 'intern'
      && ($appstore = $this->app->erp->LoadModul('appstore'))
      && !empty($shopexportRow['modulename'])
      && $appstore->isBeta($shopexportRow['modulename'])) {
      $appstore->addBetaToHeadline();
    }

    //$this->app->Tpl->Add('KURZUEBERSCHRIFT2',$name);

    $typ = empty($shopexportRow['typ'])?'':$shopexportRow['typ'];

    $this->app->erp->MenuEintrag('index.php?module=onlineshops&action=create','Neu');

    $this->app->erp->MenuEintrag("index.php?module=onlineshops&action=edit&id=$id",'Details');
    //$this->app->erp->MenuEintrag("index.php?module=shopexport&action=export&id=$id","Export");
    $this->app->erp->MenuEintrag("index.php?module=shopexport&action=artikeluebertragung&id=$id","Artikel &Uuml;bertragung");
    if($this->app->DB->Select("SELECT modulename FROM shopexport WHERE id = '$id'") === 'shopimporter_shopware'){
      //Soll nur in Shopware angezeigt werden, da nur in Shopware unterstüzt
      $this->app->erp->MenuEintrag("index.php?module=shopexport&action=adressuebertragung&id=$id","Adressen &Uuml;bertragung");
    }

    if($typ==='wawision') {
      $this->app->erp->MenuEintrag("index.php?module=shopexport&action=navigationtab&id=$id","Navigation");
      $this->app->erp->MenuEintrag("index.php?module=shopexport&action=artikelgruppen&id=$id","Artikelgruppen");
      $this->app->erp->MenuEintrag("index.php?module=shopexport&action=dateien&id=$id","Dateien");
      $this->app->erp->MenuEintrag("index.php?module=shopexport&action=live&id=$id","Live-Status");
      $this->app->erp->MenuEintrag("index.php?module=inhalt&action=listshop&id=$id","Inhalte / E-Mailvorlagen");
    }
    $this->app->erp->MenuEintrag("index.php?module=shopexport&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    
    $this->app->erp->RunMenuHook('shopexport');
  }


  public function ShopexportBesuchen()
  {
    $id = $this->app->Secure->GetGET('id');

    $url = $this->app->DB->Select("SELECT url FROM shopexport WHERE id='$id' LIMIT 1");
    $typ = $this->app->DB->Select("SELECT typ FROM shopexport WHERE id='$id' LIMIT 1");


    if($typ==='wawision') {
      $this->app->Location->execute($url);
    }	
  }


  public function ShopexportExport()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->ShopexportMenu();
    $this->app->erp->MenuEintrag('index.php?module=onlineshops&action=edit&id='.$id, "Zur&uuml;ck zur &Uuml;bersicht");

    $this->app->Tpl->Set('SCHRITT2',"disabled");
    $this->app->Tpl->Set('SCHRITT3',"disabled");

    $this->app->Tpl->Set('STATUS',"<div class=\"info\">Artikel Export: Bitte Verbindung pr&uuml;fen.</div>");

    $this->app->Tpl->Set('ID',$id);
    $this->app->Tpl->Set('COMMONEXPORT',"<div class=\"info\">Abgleich zu Kundencenter.</div>");

    if($this->app->Secure->GetPOST("commonexport")!=''){
      $anzahl = $this->app->remote->RemoteSendExportlink($id);
      //$anzahl = $this->app->remote->RemoteSendNavigation($id);
      //$this->app->erp->NewEvent("Navigations-Export Online-Shop Nr. $id","onlineshop");
      $this->app->Tpl->Set('COMMONEXPORT',"<div class=\"error\">Es wurden ".$anzahl." Datens&auml;tze heraufgeladen.</div>");
    }

    if($this->app->Secure->GetPOST("schritt1")!=''){
      
      $className = 'Remote';
      $methodName = 'RemoteConnection';
      $r = new ReflectionMethod($className, $methodName);
      $params = $r->getParameters();
      $anzargs = count($params);
      if($anzargs > 1) {
        $pageContents = $this->app->remote->RemoteConnection($id, true);
      }
      else{
        $pageContents = $this->app->remote->RemoteConnection($id);
      }
      if($pageContents==='success') {
        if($anzargs > 1) {
          $pageContents2 = $this->app->remote->RemoteConnection($id, 'info');
        }

        $this->app->Tpl->Set('SCHRITT2','');
        $this->app->Tpl->Set('HIDDENSCHRITT2',"<input type=\"hidden\" name=\"schritt1_check\" value=\"1\">");
        if($anzargs > 1 && is_array($pageContents2)) {
          $json = $this->app->DB->Select("SELECT json FROM shopexport WHERE id = '$id' LIMIT 1");
          if($json)
          {
            $json = array_merge(json_decode($json, true), $pageContents2);
          }else{
            $json = $pageContents2;
          }
          $this->app->DB->Update("UPDATE shopexport set json = '".$this->app->DB->real_escape_string(json_encode($json))."' WHERE id = '$id' LIMIT 1");
          unset($json);
          $this->app->Tpl->Set('STATUS',"<div class=\"info\">");
          if(isset($pageContents2['subshops']))
          {
            $this->app->Tpl->Set('STATUS',"Subshops: 
            <table><tr><td>Id</td><td>Name</td><td>Aktiv</td></tr>
            ");
            foreach($pageContents2['subshops'] as $subshop)
            {
              $this->app->Tpl->Add('STATUS',"<tr><td>".$subshop['id']."</td><td>".$subshop['name']."</td><td>".($subshop['aktiv']?'ja':'nein')."</td></tr>");
            }
            $this->app->Tpl->Add('STATUS',"</table>");
          }else{
            $this->app->Tpl->Add('STATUS',"Verbindung: success");
          }
          $this->app->Tpl->Add('STATUS',"</div>");
        }else{
          $this->app->Tpl->Set('STATUS',"<div class=\"info\">Verbindung: $pageContents</div>");
        }
      }
      else {
        $this->app->Tpl->Set('STATUS',"<div class=\"error\">Verbindungsproblem: Eventuell falsche Schl&uuml;ssel! ($pageContents)</div>");
      }
    }

    if($this->app->Secure->GetPOST('schritt2')!='' && $this->app->Secure->GetPOST('schritt1_check')=='1') {
      $projekt = $this->app->DB->Select("SELECT projekt FROM shopexport WHERE id='$id' LIMIT 1");

      $this->app->erp->UpdateChecksumShopartikel($projekt);

      $tmp  = $this->app->remote->RemoteGetUpdateArticleList($id);

      $this->app->Tpl->Set('SCHRITT2','');
      $this->app->Tpl->Set('SCHRITT3','');
      $this->app->Tpl->Set('HIDDENSCHRITT2',"<input type=\"hidden\" name=\"schritt1_check\" value=\"1\">");
      $this->app->Tpl->Set('HIDDENSCHRITT3',"<input type=\"hidden\" name=\"schritt2_check\" value=\"1\">");

      $html = "<table align=center>"; 
      $html = $html."<tr><td>Pos.</td><td></td><td>Artikel</td><td>Nummer</td><td>Letzte &Auml;nderung</td></tr>";

      if(is_array($tmp)) {
        foreach($tmp as $row) {
          $checkarray[$row['artikel']] = $row['checksum'];
        }
      }
      /*
         if($projekt=="1") $checkmasterarray = $this->app->DB->SelectArr("SELECT id,checksum FROM artikel WHERE shop='1' AND projekt='$projekt' AND geloescht='0'");
         else if ($projekt=="2") $checkmasterarray = $this->app->DB->SelectArr("SELECT id,checksum FROM artikel WHERE shop='2' AND projekt='$projekt' AND geloescht='0'");
         else if ($projekt=="4") $checkmasterarray = $this->app->DB->SelectArr("SELECT id,checksum FROM artikel WHERE shop='3' AND projekt='$projekt' AND geloescht='0'");
       */
      $checkmasterarray = $this->app->DB->SelectArr("SELECT id,checksum FROM artikel WHERE shop='$id' AND geloescht='0'");
      $html = $html."<tr><td>CMS</td><td><input type=\"checkbox\" name=\"cms\" value=\"1\" checked></td><td>Inhaltsseiten (keine Shop-Artikeltexte)</td>                              
        <td></td><td>(falls vorhanden)</td></tr>";                                                                                                                 

        $html = $html."<tr><td>Artikelgruppen</td><td><input type=\"checkbox\" name=\"artikelgruppen\" value=\"1\" checked></td><td>Artikelgruppen</td>       
        <td></td><td>(falls vorhanden)</td></tr>";                                     

        $html = $html."<tr><td>Dateien/Bilder</td><td><input type=\"checkbox\" name=\"dateienupdate\" value=\"1\" checked></td><td>Shopbilder (dauert bei vielen Bildern)</td>                                                        
        <td></td><td>(falls vorhanden)</td></tr>";
      $aenderungen = 0;
      $ccheckmasterarray = !empty($checkmasterarray)?count($checkmasterarray):0;
      for($i=0; $i<$ccheckmasterarray;$i++) {
        $artikel = $checkmasterarray[$i]['id'];
        $checksum= $checkmasterarray[$i]['checksum'];

        if(isset($checkarray) && isset($checkarray[$artikel]) && ($checkarray[$artikel]!=$checksum || $checkarray[$artikel]==""))
        {
          $aenderungen++;
          $tmp = $this->app->DB->SelectArr("SELECT a.name_de, a.nummer FROM artikel a WHERE a. id='$artikel' LIMIT 1");
          if($tmp)
          {
            $tmp[0]['logdatei'] = $this->app->DB->Select("SELECT logdatei FROM shopexport_artikel WHERE artikel='$artikel' AND shopexport='$id' LIMIT 1");

            if($tmp[0]['logdatei']=="") $tmp[0]['logdatei']="noch nicht vorhanden";


            $html = $html."<tr><td>$aenderungen</td><td><input type=\"checkbox\" name=\"artikel[]\" value=\"$artikel\" checked></td><td>{$tmp[0]['name_de']}</td>
              <td>{$tmp[0]['nummer']}</td><td>{$tmp[0]['logdatei']}</td></tr>";
          }
        } 
        if(isset($checkarray) && isset($checkarray[$artikel]))
        {
          unset($checkarray[$artikel]);
        }
      }

      // loesche alle artikel im shop die nicht mehr im ERP als shop artikel vorhanden sind!
      $ccheckarray = !empty($checkarray)?count($checkarray):0;
      for($j=0;$j < $ccheckarray; $j++) {
        if($ccheckarray > 0){
          foreach ($checkarray as $key_artikel => $value_checksum) {
            $pageContents = $this->app->remote->RemoteDeleteArticle($id, $key_artikel);
          }
          $this->app->Tpl->Set('STATUS', "<div class=\"error\">Es wurden " . $ccheckarray . " Artikel im Shop gel&ouml;scht (fehlende Attribute).</div>");
        }
      }

      $this->app->Tpl->Add('STATUS',"<div class=\"info\">&Auml;nderungen an $aenderungen Artikel gefunden.</div>");

      $html = $html ."</table>"; 

      $this->app->Tpl->Set('UPDATES',$html);

    }
    if($this->app->Secure->GetPOST("schritt3")!="" && $this->app->Secure->GetPOST("schritt1_check")=="1" && 
        $this->app->Secure->GetPOST("schritt2_check")=="1")
    {
      $artikel = $this->app->Secure->GetPOST("artikel");
      $this->app->erp->NewEvent("Artikel-Export Online-Shop Nr. $id","onlineshop");

      // artikelgruppen update

      if($this->app->Secure->GetPOST("cms")=="1") {
        $cms = $this->app->DB->Select("SELECT cms FROM shopexport WHERE id='$id' LIMIT 1");
        if($cms=='1'){
          $this->app->remote->RemoteSendInhalt($id);
        }
      }

      if($this->app->Secure->GetPOST("artikelgruppen")=="1") {
        $this->app->remote->RemoteSendArtikelgruppen($id);
        $this->app->remote->RemoteSendArtikelArtikelgruppen($id);
      } 


      // sende artikel liste      
      $tmp_anzahl  = $this->app->remote->RemoteSendArticleList($id,$artikel);

      // dateien update
      //$dateien = $this->app->DB->SelectArr("SELECT DISTINCT ds.datei FROM datei_stichwoerter ds, datei d WHERE d.id=ds.datei AND (ds.subjekt!='Druckbild') AND (ds.objekt='Artikel' OR ds.objekt='Kampangen') AND d.geloescht=0 AND d.firma='".$this->app->User->GetFirma()."'");

      // das sind zuviele bilder!!!! nur die bilder vom shop! TODO

      //$dateien = $this->app->DB->SelectArr("SELECT DISTINCT ds.datei FROM datei_stichwoerter ds, datei d WHERE d.id=ds.datei AND (ds.subjekt!='Druckbild') AND (ds.objekt='Artikel'  OR ds.objekt='Kampangen') AND d.firma='".$this->app->User->GetFirma()."'");

      if($this->app->Secure->GetPOST("dateienupdate")==1) {
        $dateien = $this->app->DB->SelectArr("SELECT DISTINCT ds.datei FROM datei_stichwoerter ds, datei d, artikel a WHERE d.id=ds.datei AND (ds.subjekt='Shopbild' OR ds.subjekt='Gruppenbild') AND ((ds.objekt='Artikel' AND ds.parameter=a.id)  OR (ds.objekt='Kampangen' AND ds.parameter='$id')) AND d.firma='".$this->app->User->GetFirma()."' AND a.shop='$id'");

        $tmp = $this->app->remote->RemoteGetFileList($id);

        if(is_array($tmp)) {
          foreach($tmp as $row) {
            $checkarray[$row['datei']] = $row['checksum'];
          }
        }
        $datei_updates = 0;
        $cdateien = !empty($dateien)?count($dateien):0;
        for($i=0;$i<$cdateien;$i++) {
          $fid = $dateien[$i]['datei'];
          $geloescht = $this->app->DB->Select("SELECT geloescht FROM datei WHERE id='$fid' LIMIT 1");

          if(isset($checkarray) && ($checkarray[$fid]!=md5($this->app->erp->GetDatei($fid))) && $geloescht==0) {
            $datei_updates++;
            $this->app->remote->RemoteSendFile($id,$fid);
            $this->app->remote->RemoteAddFileSubject($id,$fid);
            $checkarray[$fid]="update";
          }
          else {
            if($geloescht) {
              $this->app->remote->RemoteDeleteFile($id,$fid);
              $checkarray[$fid]='delete';
            } 
          }
          $checkarray[$fid]='mark';
          // wenn datei lokal geloescht loesche diese auch auf dem server
          //	if($geloescht)
        }
      }
      //print_r($checkarray);
      if(is_array($checkarray) && count($checkarray)>0) {
        $delete=0;
        foreach($checkarray as $key=>$value) {
          if($checkarray[$key]!=='mark' && $checkarray[$key]!=='delete' && $checkarray[$key]!=='update') {
            //echo "loesche $key<br>";
            $this->app->remote->RemoteDeleteFile($id,$key);
            $delete++;
          }
        }
      }
      //ENDE DATEIEN 
      // loesche nicht gebrauchte dateien

      if($datei_updates>0) {
        $this->app->Tpl->Set('STATUS',"<div class=\"info\">Datei-Updates: ".$datei_updates.".</div>");
      }
      if($delete>0) {
        $this->app->Tpl->Add('STATUS',"<div class=\"info\">Datei(en) gel&ouml;scht: ".$delete.".</div>");
      }
      // ende dateien update

      $this->app->DB->Insert("INSERT INTO shopexport_status (id, shopexport, bearbeiter,zeit, bemerkung,befehl)
          VALUES('','$id','".$this->app->User->GetName()."',NOW(),'','".serialize($artikel)."')");

      $this->app->Tpl->Add('STATUS',"<div class=\"info\">Erfolgreiche Updates an ".$tmp_anzahl." Artikeln durchgef&uuml;hrt.</div>");
    }


    $this->app->Tpl->Set('SUBHEADING','Starte Artikel Export');
    $this->app->Tpl->Parse('PAGE','shopexport_export.tpl');
  }


  public function ShopexportArtikeluebertragungdel()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $shop = (int)$this->app->Secure->GetGET('shop');;
    if($id > 0){
      $shop = $this->app->DB->Select("SELECT shop FROM shopexport_artikeluebertragen WHERE artikel='$id' AND shop = '$shop' LIMIT 1");
      $this->app->DB->Delete("DELETE FROM shopexport_artikeluebertragen WHERE artikel='$id' AND shop = '$shop' LIMIT 1");
      $this->app->DB->Delete("DELETE FROM shopexport_artikeluebertragen_check WHERE artikel='$id' AND shop = '$shop' LIMIT 1");
    }
    $msg = $this->app->erp->base64_url_encode('<div class="success">Der Artikel wurde aus der &Uuml;bertragung entfernt.</div>');
    $this->app->Location->execute('index.php?module=shopexport&action=artikeluebertragung&id='.$shop.'&msg='.$msg);
  }

  public function ShopexportAdressuebertragungdel()
  {

    $id = (int)$this->app->Secure->GetGET('id');
    $shop = 0;
    if($id > 0){
      $shop = $this->app->DB->Select("SELECT shop FROM shopexport_adressenuebertragen WHERE id='$id' LIMIT 1");
      $this->app->DB->Delete("DELETE FROM shopexport_adressenuebertragen WHERE id='$id' LIMIT 1");
    }
    $msg = $this->app->erp->base64_url_encode('<div class="success">Die Adresse wurde aus der &Uuml;bertragung entfernt.</div>');
    $this->app->Location->execute('index.php?module=shopexport&action=adressuebertragung&id='.$shop.'&msg='.$msg);
  }

  /**
   * @param int $shopId
   *
   * @return array
   */
  public function getChangedCount($shopId)
  {
    $checkedStarted = (int)$this->app->erp->GetKonfiguration('shopexport_artikeluebertragen_check_start_'.$shopId);
    $checked = (int)$this->app->erp->GetKonfiguration('shopexport_artikeluebertragen_check_checked_'.$shopId);
    $changed = (int)$this->app->erp->GetKonfiguration('shopexport_artikeluebertragen_check_changed_'.$shopId);
    $transfered = (int)$this->app->erp->GetKonfiguration('shopexport_artikeluebertragen_check_transfered_'.$shopId);
    $lastid = (int)$this->app->erp->GetKonfiguration('shopexport_artikeluebertragen_check_lastid_'.$shopId);
    return [$checkedStarted, $checked, $changed, $checked - $changed, $transfered, $lastid];
  }

  /**
   * @param int $shopId
   */
  public function resetChangedInfo($shopId) {
    $this->app->erp->SetKonfigurationValue('shopexport_artikeluebertragen_check_start_'.$shopId,'');
    $this->app->erp->SetKonfigurationValue('shopexport_artikeluebertragen_check_checked_'.$shopId,'');
    $this->app->erp->SetKonfigurationValue('shopexport_artikeluebertragen_check_changed_'.$shopId,'');
    $this->app->erp->SetKonfigurationValue('shopexport_artikeluebertragen_check_transfered_'.$shopId,'');
    $this->app->erp->SetKonfigurationValue('shopexport_artikeluebertragen_check_lastid_'.$shopId,'');
  }

  /**
   * @param int $ids
   */
  public function addChangedArticles($minutes = 15)
  {
    $ids = $this->app->DB->SelectFirstCols(
      sprintf(
        'SELECT `id` 
        FROM `shopexport` 
        WHERE `aktiv` = 1 AND `autosendarticle` = 1 AND `artikelexport` = 1
        AND (`autosendarticle_last` IS NULL OR DATE_ADD(`autosendarticle_last` INTERVAL %d MINUTE) <= NOW())',
        $minutes
      )
    );

    if(empty($ids)) {
      return;
    }
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `shopexport_artikeluebertragen_check` (`shop`, `artikel`) 
        SELECT s.id as shop, a.id 
        FROM `artikel` AS `a` 
        INNER JOIN `shopexport` AS `s` ON s.id IN (%s)
        LEFT JOIN (
            SELECT `artikel`, `shop` 
            FROM `artikel_onlineshops` 
            WHERE `shop` IN (%s) AND `aktiv` = 1 
            GROUP BY `artikel`, `shop`
        ) AS `oa` ON a.id = oa.artikel AND s.id = oa.shop
        LEFT JOIN `shopexport_artikeluebertragen_check` AS `sac` ON a.id = sac.artikel AND sac.shop = s.id
        LEFT JOIN `shopexport_artikeluebertragen` AS `sa` ON sa.shop = s.id AND sa.artikel = a.id
        WHERE (a.shop=s.id OR a.shop2=s.id OR a.shop3=s.id OR oa.artikel IS NOT NULL) 
          AND a.geloescht!=1 AND sa.id IS NULL
          AND sac.id IS NULL
        GROUP BY a.id, s.id",
        implode(',', $ids), implode(',', $ids)
      )
    );
  }

  /**
   * @return RedirectResponse
   */
  public function HandleResetArticleCacheForArticleWithZeroStock(): RedirectResponse
  {
    $anz = 0;
    $id = (int)$this->app->Secure->GetGET('id');
    if($id > 0) {
      $this->app->DB->Update(
        "UPDATE `artikel` AS `a` 
          INNER JOIN 
            `artikel_onlineshops` AS `oa` ON oa.shop = '$id' AND oa.aktiv = 1 AND a.id = oa.artikel
          SET oa.storage_cache = -999, oa.pseudostorage_cache = -999 
          WHERE (oa.storage_cache = 0 OR oa.storage_cache IS NULL) AND a.geloescht = 0"
      );
      $anz = $this->app->DB->affected_rows();
      $this->app->erp->LogFile("Lagerzahlencache zurückgesetzt für $anz Artikel mit 0-Mengen, shopid: $id");
    }
    $msg = $this->app->erp->base64_url_encode(
      "<div class=\"success\">Lagerzahlen Cache f&uuml;r ".$anz." Artikel zur&uuml;ckgesetzt.</div>"
    );
    return RedirectResponse::createFromUrl("index.php?module=shopexport&action=artikeluebertragung&id=$id&msg=$msg");
  }

  public function ShopexportArtikeluebertragung()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $alle = $this->app->Secure->GetPOST('alle');
    $allchanged = $this->app->Secure->GetPOST('allchanged');
    $bestaetigen = $this->app->Secure->GetPOST('bestaetigen');
    $abbrechen = $this->app->Secure->GetPOST('abbrechen');
    $artikelladen = $this->app->Secure->GetPOST('artikelladen');
    $artikel = $this->app->Secure->GetPOST('artikel');
    $kategorie = $this->app->Secure->GetPOST('kategorie');
    $unterartikel = $this->app->Secure->GetPOST('unterartikel');
    $unterartikelkategorie = $this->app->Secure->GetPOST('unterartikelkategorie');
    $kategorieladen = $this->app->Secure->GetPOST('kategorieladen');
    $delcache = $this->app->Secure->GetPOST('delcache');
    $delzerostockcache = $this->app->Secure->GetPOST('delzerostockcache');
    $delArticleCache = $this->app->Secure->GetPOST('delarticlecache');
    $artikelexporterlauben = $this->app->Secure->GetPOST('artikelexporterlauben');

    if(!empty($artikelexporterlauben)){
      $this->app->DB->Update("UPDATE shopexport SET artikelexport=1 WHERE id='$id' LIMIT 1");
    }

    if(!empty($delzerostockcache)) {
      $this->HandleResetArticleCacheForArticleWithZeroStock()->send();
      $this->app->ExitXentral();
    }
    if(!empty($delcache)) {
      $anz = 0;
      if($id > 0) {
        $this->app->DB->Update("UPDATE artikel a 
        LEFT JOIN (SELECT artikel FROM artikel_onlineshops WHERE shop = '$id' AND aktiv = 1 GROUP BY artikel) oa ON a.id = oa.artikel
        SET a.cache_lagerplatzinhaltmenge = -999 WHERE (a.shop = '$id' OR a.shop2 = '$id' OR a.shop3 = '$id' OR NOT ISNULL(oa.artikel)) AND a.geloescht = 0");
        $anz = $this->app->DB->affected_rows();
        $this->app->erp->LogFile("Lagerzahlencache zurückgesetzt für $anz Artikel, shopid: $id");
      }
      $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Lagerzahlen Cache f&uuml;r ".$anz." Artikel zur&uuml;ckgesetzt.</div>");
      $this->app->Location->execute("index.php?module=shopexport&action=artikeluebertragung&id=$id&msg=$msg");
    }

    if(!empty($delArticleCache)) {
      $anz = 0;
      if($id > 0){
        $this->app->DB->Update(
          sprintf(
            "UPDATE artikel_onlineshops SET last_article_hash = '', storage_cache=-999,pseudostorage_cache=-999 WHERE shop = %d",
            $id
          )
        );
        $anz = $this->app->DB->affected_rows();
        if($anz < 0) {
          $anz = 0;
        }
      }
      $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Artikel-Cache f&uuml;r ".$anz." Artikel zur&uuml;ckgesetzt.</div>");
      $this->app->Location->execute("index.php?module=shopexport&action=artikeluebertragung&id=$id&msg=$msg");
    }

    if(!empty($allchanged)) {
      if(!empty($bestaetigen)) {
        if($id > 0){
          /*$artikelarr = $this->app->DB->SelectArr("SELECT a.id FROM artikel a
        LEFT JOIN (SELECT artikel FROM artikel_onlineshops WHERE shop = '$id' AND aktiv = 1 GROUP BY artikel) oa ON a.id = oa.artikel
        WHERE (a.shop='$id' OR a.shop2='$id' OR a.shop3='$id' OR NOT ISNULL(oa.artikel)) AND a.geloescht!=1");
          $cartikelarr = !empty($artikelarr) ? count($artikelarr) : 0;
          for ($i = 0; $i < $cartikelarr; $i++) {
            $this->app->DB->Insert("INSERT INTO shopexport_artikeluebertragen_check (id,shop,artikel) VALUES ('','$id','" . $artikelarr[$i]['id'] . "')");
          }
        }*/
          $this->resetChangedInfo($id);
          $this->app->DB->Delete(
            sprintf(
              'DELETE FROM shopexport_artikeluebertragen_check WHERE shop = %d',
              $id
            )
          );
          $this->app->DB->Insert(
            sprintf(
              "INSERT INTO shopexport_artikeluebertragen_check (shop, artikel) 
                SELECT '%d' as shop, a.id FROM artikel a 
                LEFT JOIN (
                    SELECT artikel FROM artikel_onlineshops WHERE shop = %d AND aktiv = 1 GROUP BY artikel
                    ) oa ON a.id = oa.artikel
                LEFT JOIN shopexport_artikeluebertragen AS sa ON sa.shop = %d AND sa.artikel = a.id
                WHERE (a.shop=%d OR a.shop2=%d OR a.shop3=%d OR NOT ISNULL(oa.artikel)) AND a.geloescht!=1 AND ISNULL(sa.id)
                GROUP BY a.id",
              $id, $id, $id, $id, $id, $id
            )
          );
          echo $this->app->DB->error();
          $changeStart = $this->app->DB->affected_rows();
          $this->app->erp->SetKonfigurationValue('shopexport_artikeluebertragen_check_start_'.$id,
            $changeStart
          );
          $this->app->erp->SetKonfigurationValue('shopexport_artikeluebertragen_check_checked_'.$id,0);
          $this->app->erp->SetKonfigurationValue('shopexport_artikeluebertragen_check_changed_'.$id,0);
          $this->app->erp->SetKonfigurationValue(
            'shopexport_artikeluebertragen_check_lastid_'.$id,
            mt_rand(1,2000000000)
          );
        }

        $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Alle Artikel die mit dem Shop verkn&uuml;pft sind werden &uuml;berpr&uuml;ft.</div>");
        $this->app->Location->execute("index.php?module=shopexport&action=artikeluebertragung&id=$id&msg=$msg");
      }
      $this->app->Tpl->Add('MESSAGE','<div class="error">Bitte Best&auml;tigen Sie die &Uuml;bertragung!</div>');
    }

    if(!empty($alle)) {
      if(!empty($bestaetigen)) {
        if($id > 0){
          /*$artikelarr = $this->app->DB->SelectArr("SELECT a.id FROM artikel a
        LEFT JOIN (SELECT artikel FROM artikel_onlineshops WHERE shop = '$id' AND aktiv = 1 GROUP BY artikel) oa ON a.id = oa.artikel
        WHERE (a.shop='$id' OR a.shop2='$id' OR a.shop3='$id' OR NOT ISNULL(oa.artikel)) AND a.geloescht!=1");
          $cartikelarr = !empty($artikelarr) ? count($artikelarr) : 0;
          for ($i = 0; $i < $cartikelarr; $i++) {
            $this->app->DB->Insert("INSERT INTO shopexport_artikeluebertragen (id,shop,artikel) VALUES ('','$id','" . $artikelarr[$i]['id'] . "')");
          }
          */
          $this->resetChangedInfo($id);
          $this->app->DB->Insert(
            sprintf(
              "INSERT INTO shopexport_artikeluebertragen (shop, artikel)
                SELECT '%d' AS shop, a.id FROM artikel a 
                LEFT JOIN (
                    SELECT artikel FROM artikel_onlineshops WHERE shop = %d AND aktiv = 1 GROUP BY artikel
                ) AS oa ON a.id = oa.artikel
                WHERE (a.shop=%d OR a.shop2=%d OR a.shop3=%d OR NOT ISNULL(oa.artikel)) AND a.geloescht!=1",
              $id,$id, $id, $id, $id
            )
          );
          $this->app->erp->SetKonfigurationValue('shopexport_artikeluebertragen_start_'.$id,
            $this->app->DB->affected_rows()
          );
        }

        $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Alle Artikel die mit dem Shop verkn&uuml;pft sind werden &uuml;bertragen.</div>");
        $this->app->Location->execute("index.php?module=shopexport&action=artikeluebertragung&id=$id&msg=$msg");
      }
      $this->app->Tpl->Add('MESSAGE','<div class="error">Bitte Best&auml;tigen Sie die &Uuml;bertragung!</div>');
    }

    if(!empty($abbrechen)) {
      $this->app->DB->Delete("DELETE FROM shopexport_artikeluebertragen WHERE shop='$id'");
      $this->app->DB->Delete("DELETE FROM shopexport_artikeluebertragen_check WHERE shop='$id'");
      $this->resetChangedInfo($id);
      //$this->app->erp-> 'shopexport_artikeluebertragen_check_start_'.$id
      $msg = $this->app->erp->base64_url_encode('<div class="success">Alle aktuellen Artikel wurden aus der &Uuml;bertragung entfernt.</div>');
      $this->app->Location->execute('index.php?module=shopexport&action=artikeluebertragung&id='.$id.'&msg='.$msg);
    }

    if($kategorieladen != '') {
      if(!empty($bestaetigen)) {
        $typ = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE bezeichnung!='' AND bezeichnung='$kategorie' LIMIT 1");
        if($typ) {
          $typ .= '_kat';
        }
        else{
          $typ = $kategorie;
        }

        if((String)$typ !== '') {
          $this->app->DB->Query("INSERT INTO shopexport_artikeluebertragen (artikel, shop) SELECT id as artikel ,'$id' as shop FROM artikel WHERE (geloescht = 0 OR isnull(geloescht)) AND nummer <> 'DEL' AND nummer <> '' AND typ = '$typ'");
          $anz = (int)$this->app->DB->affected_rows();
          //$artikelarr = $this->app->DB->SelectArr("SELECT id FROM artikel WHERE (geloescht = 0 OR isnull(geloescht)) AND nummer <> 'DEL' AND nummer <> '' AND typ = '$typ'");
          if($anz > 0) {
            $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Die Artikelkategorie wurde der &Uuml;bertragung hinzugef&uuml;gt ($anz Artikel).</div>");
          }
          else{
            $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Keine Artikel in dieser Artikelkategorie gefunden.</div>");
          }
          $this->app->Location->execute("index.php?module=shopexport&action=artikeluebertragung&id=$id&msg=$msg");
        }
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Keine Artikelkategorie ausgew&auml;hlt</div>");
        $this->app->Location->execute("index.php?module=shopexport&action=artikeluebertragung&id=$id&msg=$msg");
      }
      $this->app->Tpl->Add('MESSAGE','<div class="error">Bitte Best&auml;tigen Sie die &Uuml;bertragung!</div>');
    }
    
    if($artikelladen!='') {
      if(!empty($bestaetigen)) {
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer!='' AND nummer='$artikel' AND (geloescht = 0 OR isnull(geloescht))  AND nummer <> 'DEL' LIMIT 1");

        if($artikelid > 0 && $id > 0)
        {
          $this->app->DB->Insert("INSERT INTO shopexport_artikeluebertragen (id,shop,artikel) VALUES ('','$id','$artikelid')");

          $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Der Artikel wurde der &Uuml;bertragung hinzugef&uuml;gt.</div>");
          $this->app->Location->execute("index.php?module=shopexport&action=artikeluebertragung&id=$id&msg=$msg");
        }
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Kein Artikel ausgew&auml;hlt</div>");
        $this->app->Location->execute("index.php?module=shopexport&action=artikeluebertragung&id=$id&msg=$msg");
      }
      $this->app->Tpl->Add('MESSAGE','<div class="error">Bitte Best&auml;tigen Sie die &Uuml;bertragung!</div>');
    }

    $this->app->Tpl->Set('VORMATRIXPRODUKT','<!--');
    $this->app->Tpl->Set('NACHMATRIXPRODUKT', '-->');

    $this->ShopexportMenu();

    $checking = (int)$this->app->DB->Select(
      sprintf(
        'SELECT COUNT(id) FROM shopexport_artikeluebertragen_check WHERE shop = %d',
        $id
      )
    );
    list($start,$checked, $changed, $notChanged, $transfered, $lastid) = $this->getChangedCount($id);
    if($checking || (int)$start > 0) {
      $inList = (int)$this->app->DB->Select(
        sprintf(
          'SELECT COUNT(id) FROM shopexport_artikeluebertragen WHERE shop = %d',
          $id
        )
      );

      if($inList === 0 && $checking === 0) {
        $this->app->Tpl->Add(
          'MESSAGE',
          '<div class="warning">' .$checked.' von '.$start.
        ' Artikel wurden überpr&uuml;ft. '.(!empty($transfered)?$transfered:$changed).' ver&auml;nderte Artikel wurden gefunden und an den Shop &uuml;bertragen.</div>');
      }
      else{
        $this->app->Tpl->Add(
          'MESSAGE',
          '<div class="warning">' .$checked.' von '.$start.' Artikel wurden &uuml;berpr&uuml;ft. '.$changed.
        ' ver&auml;nderte Artikel wurden gefunden und '.$transfered.' davon an den Shop &uuml;bertragen. <a href="index.php?module=shopexport&action=artikeluebertragung&id=' . $id . '"><input type="button" value="neu Laden" /></a></div>');
        /*if($start > 0) {
          $this->app->Tpl->Add(
            'MESSAGE',
            '<div class="warning">' .
            $checked . ' von ' . $start . ' Artikel gepr&uuml;ft (' . $changed . ' ge&auml;ndert / ' .
            $notChanged . ' nicht ge&auml;ndert) 
          <a href="index.php?module=shopexport&action=artikeluebertragung&id=' . $id . '"><input type="button" value="neu Laden" /></a></div></div>'
          );
        }
        else {
          $this->app->Tpl->Add(
            'MESSAGE',
            '<div class="warning">' . $inList . ' Artikel gefunden, noch zu pr&uuml;fen ' . $checking . ' Artikel <a href="index.php?module=shopexport&action=artikeluebertragung&id=' . $id . '"><input type="button" value="neu Laden" /></a></div></div>'
          );
        }*/
      }
    }


    $exportErlaubt = $this->app->DB->Select("SELECT artikelexport FROM shopexport WHERE id='$id' LIMIT 1");

    if(!$exportErlaubt){
      $this->app->Tpl->Set('IMPORTERINFO','<div class="info">');
      $this->app->Tpl->Add('IMPORTERINFO','Der Artikelexport für diesen Importer ist nicht aktiviert. 
        <input class="btnBlue" type="submit" value="Jetzt aktivieren" name="artikelexporterlauben">');
      $this->app->Tpl->Add('IMPORTERINFO','</div>');
    }
    $this->app->erp->checkActiveCronjob('artikeluebertragen', 'IMPORTERINFO2');




    $this->app->erp->MenuEintrag('index.php?module=onlineshops&action=edit&id='.$id, 'Zur&uuml;ck zur &Uuml;bersicht');
    $this->app->YUI->AutoComplete('artikel','artikelnummer',1);
    $this->app->YUI->AutoComplete('kategorie','artikelkategorien');
    $this->app->YUI->TableSearch('TAB1','shopexport_artikeluebertragung');
    $this->app->Tpl->Parse('PAGE','shopexport_artikeluebertragung.tpl');
  }

  public function ShopexportAdressuebertragung()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $bestaetigen = $this->app->Secure->GetPOST('bestaetigen');
    $alle = $this->app->Secure->GetPOST('alle');
    $abbrechen = $this->app->Secure->GetPOST('abbrechen');
    $adresseladen = $this->app->Secure->GetPOST('adresseladen');
    $adresse = $this->app->Secure->GetPOST('adresse');
    $gruppeladen = $this->app->Secure->GetPOST('gruppeladen');
    $gruppe = $this->app->Secure->GetPOST('gruppe');

    if(!empty($alle)) {
      if(!empty($bestaetigen)) {
        if($id > 0){
          $this->app->DB->Insert("INSERT INTO shopexport_adressenuebertragen (shop,adresse) SELECT $id, a.id FROM adresse a WHERE a.geloescht <> 1 AND a.kundennummer <> '' AND a.id NOT IN (SELECT adresse FROM shopexport_adressenuebertragen WHERE shop = $id)");
        }

        $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Alle Adressen die mit dem Shop verkn&uuml;pft sind werden &uuml;bertragen.</div>");
        $this->app->Location->execute("index.php?module=shopexport&action=adressuebertragung&id=$id&msg=$msg");
      }
      $this->app->Tpl->Add('MESSAGE','<div class="error">Bitte Best&auml;tigen Sie die &Uuml;bertragung!</div>');
    }

    if(!empty($abbrechen)) {
      $this->app->DB->Delete("DELETE FROM shopexport_adressenuebertragen WHERE shop='$id'");
      $msg = $this->app->erp->base64_url_encode('<div class="success">Alle Adressen wurden aus der &Uuml;bertragung entfernt.</div>');
      $this->app->Location->execute('index.php?module=shopexport&action=adressuebertragung&id='.$id.'&msg='.$msg);
    }

    if($gruppeladen!='') {
      if(!empty($bestaetigen) && $gruppe!='') {
        $gruppetmp = explode(' ', $gruppe);
        $gruppenid = $this->app->DB->Select("SELECT id FROM gruppen WHERE kennziffer='".$gruppetmp[0]."' LIMIT 1");
        if($gruppenid > 0 && $id > 0)
        {
          $this->app->DB->Insert("INSERT INTO shopexport_adressenuebertragen (shop,adresse) SELECT $id, a.id FROM adresse a JOIN adresse_rolle ar ON a.id = ar.adresse WHERE ar.subjekt='Mitglied' AND ar.objekt='Gruppe' AND ar.parameter='$gruppenid' AND a.geloescht <> 1 AND a.kundennummer <> '' AND a.id NOT IN (SELECT adresse FROM shopexport_adressenuebertragen WHERE shop = $id)");
          $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Die Adressen wurde der &Uuml;bertragung hinzugef&uuml;gt.</div>");
          $this->app->Location->execute("index.php?module=shopexport&action=adressuebertragung&id=$id&msg=$msg");
        }
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Keie Adresse ausgew&auml;hlt</div>");
        $this->app->Location->execute("index.php?module=shopexport&action=adressuebertragung&id=$id&msg=$msg");
      }
      $this->app->Tpl->Add('MESSAGE','<div class="error">Bitte Best&auml;tigen Sie die &Uuml;bertragung!</div>');
    }
    
    if($adresseladen!='')
    {
      if(!empty($bestaetigen) && $adresse!=''){
        $adressetmp = explode(' ', $adresse);
        $adressid = $adressetmp[0];
        $adressid = $this->app->DB->Select("SELECT id FROM adresse WHERE name!='' AND kundennummer!='' AND geloescht<>1 AND id='$adressid' LIMIT 1");
        if($adressid > 0 && $id > 0) {
          $this->app->DB->Insert("INSERT INTO shopexport_adressenuebertragen (id,shop,adresse) VALUES ('','$id','$adressid')");

          $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Die Adresse wurde der &Uuml;bertragung hinzugef&uuml;gt.</div>");
          $this->app->Location->execute("index.php?module=shopexport&action=adressuebertragung&id=$id&msg=$msg");
        }
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Keie Adresse ausgew&auml;hlt</div>");
        $this->app->Location->execute("index.php?module=shopexport&action=adressuebertragung&id=$id&msg=$msg");
      }
      $this->app->Tpl->Add('MESSAGE','<div class="error">Bitte Best&auml;tigen Sie die &Uuml;bertragung!</div>');
    }

    $this->ShopexportMenu();
    $this->app->erp->MenuEintrag('index.php?module=onlineshops&action=edit&id='.$id, 'Zur&uuml;ck zur &Uuml;bersicht');
    $this->app->YUI->AutoComplete('adresse','adresse');
    $this->app->YUI->AutoComplete('gruppe','gruppekennziffer');
    $this->app->YUI->TableSearch('TAB1','shopexport_adressuebertragung');
    $this->app->Tpl->Parse('PAGE','shopexport_adressuebertragung.tpl');
  }

  public function ShopexportNavigation()
  {
    $id = $this->app->Secure->GetGET('id');
    $tmp = new Navigation($this->app,$id);
    $this->app->Tpl->Set('ID',$id);
    $this->app->Tpl->Set('PAGE',$tmp->Get());
    $this->app->BuildNavigation=false;
  }


  /**
   * @param int       $articleId
   * @param int|array $shopId
   *
   * @return array
   */
  public function hasArticleHashChanged($articleId, $shopId)
  {
    if(is_array($shopId)) {
      $shop = $shopId;
      $shopId = $shop['id'];
    }/*
    else {
      $shop = $this->app->DB->SelectRow('SELECT * FROM shopexport WHERE id = %d', $shopId);
    }*/
    $hash = $this->getArticleHash($articleId, $shopId, true);
    $articleShopRow = $this->app->DB->SelectRow(
      sprintf(
        'SELECT last_article_hash, last_article_transfer FROM artikel_onlineshops WHERE artikel = %d AND shop = %d',
        $articleId, $shopId
      )
    );

    return [
      'changed' => empty($articleShopRow['last_article_hash']) || ($articleShopRow['last_article_hash'] !== $hash),
      'hash'    => $hash
    ];
  }

  /**
   * @param int       $articleId
   * @param int|array $shopId
   * @param bool      $withAttributes
   * @param bool      $widthCategorieTree
   *
   * @return string
   */
  public function getArticleHash($articleId, $shopId, $withAttributes = true, $widthCategorieTree = false)
  {
    if(is_array($shopId)) {
      $shop = $shopId;
      $shopId = $shop['id'];
    }/* else {
      $shop = $this->app->DB->SelectRow('SELECT * FROM shopexport WHERE id = %d', $shopId);
    }*/
    $art = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM artikel WHERE id = %d',
        $articleId
      )
    );
    if(empty($art)) {
      return '';
    }
    unset(
      $art['logdatei'],
      $art['cache_lagerplatzinhaltmenge'],
      $art['shop'],
      $art['shop2'],
      $art['shop3'],
      $art['usereditid'],
      $art['useredittimestamp'],
      $art['laststorage_sync']
    );
    if($withAttributes) {
      $art['attributes'] = $this->app->DB->SelectArr(
        sprintf(
          "SELECT DISTINCT e.name, ew.wert 
        FROM artikeleigenschaften e 
        INNER JOIN artikeleigenschaftenwerte ew ON e.id = ew.artikeleigenschaften
        WHERE ew.artikel = %d AND e.name <> '' AND ew.wert <> '' AND e.geloescht <> 1
        ORDER BY e.name, ew.wert",
          $articleId
        )
      );
    }
    if($widthCategorieTree) {
      $kategorienbaum = null;
      $this->app->erp->GetKategorienbaum($kategorienbaum, 0, 0, $articleId);
      if(!empty($kategorienbaum)) {
        $kategorien = $this->app->DB->SelectArr(
          sprintf(
            "SELECT ak.id, ak.bezeichnung 
            FROM `artikelbaum_artikel` aa 
            INNER JOIN `artikelkategorien` ak ON aa.kategorie = ak.id AND ak.geloescht <> 1 AND aa.artikel = %d 
            ORDER by ak.bezeichnung, ak.id",
            $articleId
          )
        );
        if($kategorien)
        {
          foreach($kategorien as $v)
          {
            $katid[$v['id']] = true;
          }
        }
        if(!empty($katid)) {
          foreach($kategorienbaum as $v) {
            if(isset($katid[$v['id']])) {
              $art['kategorien'][] = array('name'=>$v['bezeichnung'],'parent'=>$v['parent'],'id'=>$v['id'],'lvl'=>$v['lvl'],'extid'=>$v['extid']);
            }
          }
        }
      }
    }

    $art['shopexport'] = $this->app->DB->SelectArr(
      sprintf(
        "SELECT name, wert FROM shopexport_artikel WHERE artikel = %d AND shopid = %d ORDER BY id ",
        $articleId, $shopId
      )
    );

    return sha1(json_encode($art));
  }
}
