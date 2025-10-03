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
class Dateien {
  /** @var Application $app */
  var $app;
  /** @var int $id */
  var $id;

  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public static function TableSearch($app, $name, $erlaubtevars)
  {
    if(strpos($name, 'dateien_popup_') == 0)
    {
      $_objekt = str_replace('dateien_popup_','',$name);

      $id = $app->Secure->GetGET("id");
      $doctype_id = $app->Secure->GetGET("id");
      $sid = $app->Secure->GetGET("sid");
      if($sid > 0) $id = $sid;

      // alle artikel die ein Kunde kaufen kann mit preisen netto brutto
      $cmd = $app->Secure->GetGET("smodule");
      $cmd = strtolower($_objekt);

      // headings
      $heading = array('','','','Titel', 'Stichwort', 'Version','Gr&ouml;&szlig;e', 'Ersteller','Version','Datum','Men&uuml;');
      $width = array('1%','1%','10','40%', '15%', '5%','10%','15%', '10%', '10%','15%', '10%','5%');
      $findcols = array('open','d.id','d.id','d.titel', 's.subjekt', 'v.version','v.size', 'v.ersteller','v.bemerkung','v.datum', 's.id');
      $searchsql = array('d.titel', 's.subjekt', 'v.version','v.size', 'v.ersteller','v.bemerkung','v.dateiname');

      $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=dateien&action=send&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/download.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=dateien&action=removelink&doctype=".urlencode($_objekt)."&doctypeid=".$doctype_id."&id=%value%\")><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\" ></a></td></tr></table>";
      $menucol = 10;
      $alignright=array(6,7);

      if(!function_exists('imagejpeg'))
      {
        $img = "'<img src=./themes/{$app->Conf->WFconf['defaulttheme']}/images/icon_img_error.png title=\"Keine GD-Erweiterung installiert\" />'";
      }else{
        $img = "concat('<span style=\"width:100px;text-align:center;display:block;\"><a href=\"index.php?module=dateien&action=send&id=',d.id,'\"><img src=\"index.php?module=ajax&action=thumbnail&cmd=$cmd&id=',d.id,'\" style=\"border:0;max-width:100px;max-height:100px;\" /></a></span>')";
      }

      // SQL statement
      $sql = "SELECT SQL_CALC_FOUND_ROWS d.id,'<img src=./themes/{$app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,concat('<input type=\"checkbox\" id=\"auswahl_',d.id,'\" onchange=\"chauswahl();\" value=\"1\" />'),
      $img, 

      if(d.titel!='',CONCAT(d.titel,'<br><i style=color:#999>',v.dateiname,'</i>'),v.dateiname), s.subjekt, v.version, if(v.size!='',if(v.size > 1024*1024,CONCAT(ROUND(v.size/1024/1024,2),' MB'),CONCAT(ROUND(v.size/1024,2),' KB')),''), v.ersteller, v.bemerkung, DATE_FORMAT(v.datum, '%d.%m.%Y'), d.id FROM 
          datei d LEFT JOIN datei_stichwoerter s ON d.id=s.datei LEFT JOIN datei_version v ON v.datei=d.id ";

      parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $queries);
      switch($queries['module'])
      {
        case "adresse": $objekt="adressen"; break;
        default: $objekt=$queries['module'];
      }

      //if(!ctype_alpha($objekt))$objekt="";

      if(!preg_match('/[A-Za-z_]/', $objekt))$objekt="";



      $parameter=$id;
      $moreinfo = true;
      $moreinfomodule = 'dateien';
      // fester filter
      $where = "s.objekt LIKE '$_objekt' AND s.parameter='$parameter' AND d.geloescht=0";

      $count = "SELECT COUNT(d.id) FROM datei d LEFT JOIN datei_stichwoerter s ON d.id=s.datei
      LEFT JOIN datei_version v ON v.datei=d.id WHERE $where";
        
    }

   switch ($name) {
      case "dateien_list":
          $allowed['dateien_list'] = array('list');
          $heading = array('ID','Titel', 'Beschreibung', 'Verkn&uuml;pfung', 'Geloescht', 'Logdatei',  'Men&uuml;');
          $width = array('10%'); // Fill out manually later

          $findcols = array('d.id','d.titel', 'd.beschreibung', 'ds.objekt', 'd.geloescht', 'd.logdatei');
          $searchsql = array('d.titel', 'd.beschreibung', 'd.nummer', 'd.geloescht', 'd.logdatei','ds.objekt');

          $defaultorder = 1;
          $defaultorderdesc = 0;

          $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=dateien&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a></td></tr></table>";

          $sql = "SELECT SQL_CALC_FOUND_ROWS d.id, d.id, d.titel, d.beschreibung, GROUP_CONCAT(ds.objekt SEPARATOR ', ') as verknuepfung, d.geloescht, d.logdatei, d.id FROM datei d LEFT join datei_stichwoerter ds ON ds.datei = d.id";

          $where = "1";
          $count = "SELECT count(DISTINCT id) FROM datei WHERE $where";

          $groupby = " GROUP BY d.id ";

         break;
    }

    $erg = [];
   
    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v)) {
        $erg[$v] = $$v;
      }
    }
    return $erg;
  }
  
  function __construct($app, $intern = false) {
    //parent::GenDateien($app);
    $this->app=$app;
    if($intern) {
      return;
    }
    $this->id = $this->app->Secure->GetGET("id");

    $this->app->Tpl->Set('ID', $this->id );
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","DateienCreate");
    $this->app->ActionHandler("edit","DateienEdit");
    $this->app->ActionHandler("list","datei_list");
    $this->app->ActionHandler("archiv","DateienArchiv");
    $this->app->ActionHandler("artikel","DateienArtikel");
    $this->app->ActionHandler("send","DateienSend");
    $this->app->ActionHandler("delete","DateienDelete");
    $this->app->ActionHandler("removelink","DateienRemoveLink");
    $this->app->ActionHandler("zahlung","DateienZahlung");
    $this->app->ActionHandler("protokoll","DateienProtokoll");
    $this->app->ActionHandler("abschicken","DateienAbschicken");
    $this->app->ActionHandler("freigabe","DateienFreigabe");    
    $this->app->ActionHandler("listfreigegebene","DateienListFreigegebene");
    $this->app->ActionHandler("kundeuebernehmen","DateienKundeuebernehmen");
    $this->app->ActionHandler("versand","DateienVersand");
    $this->app->ActionHandler("lieferadresseneu","DateienLieferadresseNeu");
    $this->app->ActionHandler("download","DateienDownload");
    $this->app->ActionHandler("minidetail","DateienMinidetail");
    $this->app->ActionHandler("lieferadresseauswahl","DateienLieferadresseAuswahl");
    $this->app->ActionHandler("popup","DateienPopup");

    $this->app->ActionHandlerListen($app);
  }

  function DateienPopup()
  {
    $this->app->BuildNavigation = false;
    $this->app->PopupJS = true;
    $typ = $this->app->Secure->GetGET('typ');
    $typid = $this->app->Secure->GetGET('typid');
    if($this->app->Secure->GetGET('cmd')==='getanz')
    {
      echo json_encode(array('anz'=>$this->app->DB->Select("SELECT COUNT(d.id) FROM datei d LEFT JOIN datei_stichwoerter s ON d.id=s.datei
      LEFT JOIN datei_version v ON v.datei=d.id WHERE s.objekt LIKE '$typ' AND s.parameter='$typid' AND d.geloescht=0")));
      $this->app->ExitXentral();
    }
    $this->app->YUI->TableSearch('TAB1', "dateien_popup_".$typ, "show","","",basename(__FILE__), __CLASS__);
    $parameter = array('module'=>$typ,'action'=>'popup','popup'=>true);
    if(is_numeric($typid))
    {
      $id = $typid;
    }else{
      $id = '';
      $parameter['idelement'] = $typid;
    }
    $this->app->YUI->DateiUpload('PAGE',$typ,$id,$parameter);
  }

  function DateienMinidetail()
  {
    $id = (int)$this->app->Secure->GetGET('id');   
    $table = new EasyTable($this->app);
    $table->Query("SELECT version,dateiname,datum,ersteller,bemerkung,id FROM datei_version WHERE datei='$id'",0,"");
    $table->DisplayNew('VERSIONEN',"
      <a href=\"index.php?module=dateien&action=send&fid=%value%&id=$id\"><img src=\"./themes/new/images/download.svg\" border=\"0\"></a>
      ");
  
    $table = new EasyTable($this->app);
    $table->Query("SELECT subjekt,objekt,parameter FROM datei_stichwoerter WHERE datei='$id'",0,"");
    $table->DisplayNew('STICHWOERTER',"Parameter","noAction");
    $this->app->Tpl->Output('datei_minidetail.tpl');
    $this->app->ExitXentral();    
  }

  function DateienHauptMenu()
  {
    $this->app->erp->Headlines('Dateien');
  }

  function datei_list() {
    $this->app->erp->MenuEintrag("index.php?module=dateien&action=list", "&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=dateien&action=create", "Neu anlegen");

    $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

    $this->app->YUI->TableSearch('TAB1', 'dateien_list', "show", "", "", basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE', "dateien_list.tpl");
  }    

  function DateienDownload()
  {
    $typ = $this->app->Secure->GetGET('typ');
    $id = (int)$this->app->Secure->GetGET('id');
    $erlaubt = false;
    if($typ && $id)
    {
      switch($typ)
      {
        case "bestellung":
        case "angebot":
        case "lieferschein":
        case "rechnung":
        case "gutschrift":
        case "auftrag":
        case "arbeitsnachweis":
        case "brieffax": 
          if($this->app->erp->RechteVorhanden($typ,'edit'))$erlaubt = true;
        break;
        case "brieffax":
          if($this->app->erp->RechteVorhanden('adresse','brief'))$erlaubt = true;
          $typ = "dokument";
        break;
      }
      if($erlaubt)
      {
        $dateianhang = $this->app->DB->SelectArr("SELECT ds.id, ds.datei, d.titel FROM datei_stichwoerter ds INNER JOIN datei d on ds.datei = d.id where d.geloescht <> 1 AND ds.datei = '$id' AND objekt like '$typ' LIMIT 1");
        if($dateianhang)
        {
          $dateianhang = reset($dateianhang);
          $this->app->erp->SendDatei($id);
          $this->app->ExitXentral();
        }
      } else {
        echo "Fehlende Rechte";
        $this->app->ExitXentral();
      }
    }
    echo "Fehler";
    $this->app->ExitXentral();
  }

  
  function DateienMenu()
  {
    $id = $this->app->Secure->GetGET("id");

    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=dateien&action=edit&id=$id\">Dateien</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=dateien&action=artikel&id=$id\">Artikel</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=dateien&action=zahlung&id=$id\">Zahlungsinformation</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=dateien&action=versand&id=$id\">Versand</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=dateien&action=abschicken&id=$id\">Abschicken</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=dateien&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a>&nbsp;");
  }

  function DateienCreate()
  {
    $id = $this->id;
    $this->DateienHauptMenu();

 
    $speichern = $this->app->Secure->GetPOST("speichern");
    if($speichern !='')
    {
      $titel= $this->app->Secure->GetPOST("titel");
      $beschreibung= $this->app->Secure->GetPOST("beschreibung");
      $nummer= $this->app->Secure->GetPOST("nummer");
      $subjekt= $this->app->Secure->GetPOST("subjekt");
      $objekt= $this->app->Secure->GetPOST("objekt");

      $this->app->Tpl->Set('TITLE',$titel);
      $this->app->Tpl->Set('BESCHREIBUNG',$beschreibung);
      $this->app->Tpl->Set('NUMMER',$nummer);
      $this->app->Tpl->Set('SUBJEKT',$subjekt);
      $this->app->Tpl->Set('OBJEKT',$objekt);

      if($_FILES['upload']['tmp_name']=='')
      {
	      $this->app->Tpl->Set('ERROR',"<div class=\"info\">Bitte w&auml;hlen Sie eine Datei aus und laden Sie diese herauf!</div>");
      } else {
        // nach /tmp
        //move_uploaded_file($_FILES['upload']['tmp_name'],"//".$_FILES['upload']['name'])){
        $fileid = $this->app->erp->CreateDatei($_FILES['upload']['name'],$titel,$beschreibung,$nummer,$_FILES['upload']['tmp_name'],$this->app->User->GetName());

        // stichwoerter hinzufuegen
        $this->app->erp->AddDateiStichwort($fileid,$subjekt,$objekt,$nummer);
        $this->app->Location->execute('index.php?module=dateien&action=edit&id='.$fileid);
        //loeschen von /tmp	
      }

    }    
      //$this->DateienMenu();
    $this->app->Tpl->Set('HEADING',"Datei (Neu)");
    $this->app->Tpl->Parse('PAGE',"datei_neu.tpl");
  }

  function DateienEdit()
  {
    $id = $this->app->Secure->GetGET("id");

    $this->DateienHauptMenu();

    $geschuetzt = $this->app->DB->Select("SELECT geschuetzt FROM datei WHERE id = '".$id."' LIMIT 1");
    if (!$geschuetzt) {
        $this->DateiUploadNeuVersion('NEUEVERSION',$id);
        $speichern = $this->app->Secure->GetPOST("titel_beschreibung_speichern");
        if($titel_beschreibung_speichern !="")
        {
            $titel= $this->app->Secure->GetPOST("titel");
            $beschreibung= $this->app->Secure->GetPOST("beschreibung");
            $this->app->DB->Update("UPDATE datei SET titel='$titel', beschreibung='$beschreibung' WHERE id='$id' LIMIT 1");
        }
    }
    $this->app->Tpl->Set('STARTDISABLE', "<!--");
    $this->app->Tpl->Set('ENDEDISABLE', "-->");
    $this->app->Tpl->Parse('NEUEVERSION', "datei_neudirekt.tpl");

    $titel = $this->app->DB->Select("SELECT titel FROM datei WHERE id='$id' LIMIT 1");
    $beschreibung = $this->app->DB->Select("SELECT beschreibung FROM datei WHERE id='$id' LIMIT 1");
    $nummer= $this->app->DB->Select("SELECT nummer FROM datei WHERE id='$id' LIMIT 1");

    $this->app->Tpl->Set('TITEL',$titel);
    $this->app->Tpl->Set('KURZUEBERSCHRIFT2',$titel);
    $this->app->Tpl->Set('BESCHREIBUNG',$beschreibung);
    $this->app->Tpl->Set('NUMMER',$nummer);

    $table = new EasyTable($this->app);
    $table->Query("SELECT version,dateiname,datum,ersteller,bemerkung,id FROM datei_version WHERE datei='$id'",0,"");
/*    $table->DisplayNew('VERSIONEN',"
      <!--<a href=\"index.php?module=adresse&action=dateiversion&id=$id&lid=%value%\">edit</a>-->
      <a href=\"#\"onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=dateien&action=delete&fid=%value%&version=true&id=$id';\" ><img src=\"./themes/new/images/delete.svg\" border=\"0\"></a>
      <a href=\"index.php?module=dateien&action=send&fid=%value%&id=$id\"><img src=\"./themes/new/images/download.svg\" border=\"0\"></a>
      ",
      "<!--<a href=\"index.php?module=adresse&action=lieferadresseneu&id=$id\">Neue Version anlegen</a>-->");*/
     $table->DisplayNew('VERSIONEN',"
      <a href=\"index.php?module=dateien&action=send&fid=%value%&id=$id\"><img src=\"./themes/new/images/download.svg\" border=\"0\"></a>
      ");
  
    $table = new EasyTable($this->app);
    $table->Query("SELECT subjekt,objekt,parameter FROM datei_stichwoerter WHERE datei='$id'",0,"");
    $table->DisplayNew('STICHWOERTER',"Parameter","noAction");

    $this->app->YUI->AutoComplete("auftrag_zuordnen","auftrag");
    $this->app->YUI->AutoComplete("verbindlichkeit_zuordnen","verbindlichkeit");

    $this->app->Tpl->Set('HEADING',"Datei (Bearbeiten)");
    $this->app->Tpl->Parse('PAGE',"datei.tpl");
  }


  function DateienSend()
  {
    $fid = $this->app->Secure->GetGET('fid');
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->SendDatei($id,$fid);
    $this->app->ExitXentral();
  }

  function DateienDelete()
  {
    $id = $this->app->Secure->GetGET("id"); 
    $cmd = urldecode($this->app->Secure->GetGET("cmd"));

    $this->app->erp->RunHook('LayouttemplateAttachmentItemsDelete', 2, $id, $cmd);

    // Deletion of files removed, they only get marketd

    $this->app->DB->Update("UPDATE datei SET geloescht=1 WHERE id='$id' AND NOT geschuetzt");
    $refer = $_SERVER['HTTP_REFERER'];
    $this->app->Location->execute($refer);
  }

  function DateienRemoveLink() {
    $id = $this->app->Secure->GetGET("id");
    $doctype = $this->app->Secure->GetGET("doctype");
    $objekttype = $this->app->YUI->dateien_module_objekt_map($doctype);
    $doctype_id = $this->app->Secure->GetGET("doctypeid");
    $sql = "DELETE FROM datei_stichwoerter WHERE datei='$id' AND LOWER(objekt)=LOWER('$objekttype') AND parameter='$doctype_id'";
    $this->app->DB->Update($sql);
    $refer = $_SERVER['HTTP_REFERER'];
    $this->app->Location->execute($refer);
  }

  function DateienProtokoll()
  {
    $this->app->Tpl->Set('PROTOKOLL',"pro tabelle");

    $this->DateienMenu();
    $this->app->Tpl->Set('HEADING',"Dateien (Protokoll)");
    $this->app->Tpl->Parse('PAGE',"dateien_protokoll.tpl");
  }

  function DateiUploadNeuVersion($parsetarget, $datei) {
      $speichern = $this->app->Secure->GetPOST("speichern");
      $module = $this->app->Secure->GetGET("module");
      $action = $this->app->Secure->GetGET("action");
      $id = $this->app->Secure->GetGET("id");
      if($id)$this->app->Tpl->Set('ID',$id);

      // Get files here
      if ($speichern != "") {
        $retval = $this->app->YUI->FilesFromUploadtoDMS(null, null, $datei);
        if ($retval !== true) {
            $this->app->Tpl->Set('ERROR', implode(', ',$retval));
            $this->app->erp->EnableTab("tabs-2");
        } else {
//            header("Location: index.php?module=$module&action=$action&id=$id");
        }
      }
    }
}

