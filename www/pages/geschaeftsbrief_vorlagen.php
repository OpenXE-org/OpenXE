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
include ("_gen/geschaeftsbrief_vorlagen.php");

class Geschaeftsbrief_vorlagen extends GenGeschaeftsbrief_vorlagen {
  /** @var Application $app */
  var $app;

  /**
   * Geschaeftsbrief_vorlagen constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    //parent::GenGeschaeftsbrief_vorlagen($app);
    $this->app=$app;
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Geschaeftsbrief_vorlagenCreate");
    $this->app->ActionHandler("edit","Geschaeftsbrief_vorlagenEdit");
    $this->app->ActionHandler("delete","Geschaeftsbrief_vorlagenDelete");
    $this->app->ActionHandler("list","Geschaeftsbrief_vorlagenList");
    $this->app->ActionHandler("dateien","Geschaeftsbrief_vorlagenDateien");
    //$this->app->ActionHandler("copy", "Geschaeftsbrief_vorlagenCopy");
    $this->app->ActionHandler("copyedit", "Geschaeftsbrief_vorlagenCopyEdit");
    $this->app->ActionHandler("copysave", "Geschaeftsbrief_vorlagenCopySave");

    $this->app->ActionHandlerListen($app);
  }

  function Geschaeftsbrief_vorlagenDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    if(is_numeric($id))
    {
      $this->app->DB->Delete("DELETE FROM geschaeftsbrief_vorlagen WHERE id='$id'");
    }

    $this->Geschaeftsbrief_vorlagenList();
  }


  function Geschaeftsbrief_vorlagenCreate()
  {
    $this->Geschaeftsbrief_vorlagenMenu();
    parent::Geschaeftsbrief_vorlagenCreate();
  }

  function Geschaeftsbrief_vorlagenList()
  {
    $this->Geschaeftsbrief_vorlagenMenu();
    //parent::Geschaeftsbrief_vorlagenList();
    $this->app->YUI->TableSearch("TAB1","geschaeftsbrief_vorlagenlist");


    $this->app->YUI->AutoComplete("editprojekt", "projektname", 1);
    //MIT EDITOR PASST FORMATIERUNG VOM TEXT NICHT MEHR
    $this->app->YUI->CkEditor("edittext","internal");
    $sprachenOptions = $this->app->erp->GetSprachenSelect();
    foreach($sprachenOptions as $k => $v)$this->app->Tpl->Add('SELSPRACHEN','<option value="'.$k.'">'.$v.'</option>');

    $this->app->YUI->AutoComplete("projekt", "projektname", 1);
    $this->app->YUI->CkEditor("text", "internal");
    $this->app->YUI->DateiPopup('DATEIENPOPUP','geschaeftsbrief_vorlagen','#e_id',array(
     'openbuttontarget'=>'DATEIBUTTON'
     ,'frompopup'=>'editGeschaeftsbriefvorlage','afteropen'=>'AFTERPOPUPOPEN'
    ));
    $this->app->Tpl->Parse('PAGE', "geschaeftsbrief_vorlagen_list.tpl");
  }

  function Geschaeftsbrief_vorlagenMenu()
  {

    $this->app->erp->Headlines('Dokumenten Vorlagen');
    $this->app->erp->MenuEintrag("index.php?module=geschaeftsbrief_vorlagen&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("Geschaeftsbrief_vorlagenEdit(0)","Neuer Eintrag");
    if($this->app->Secure->GetGET("action")==="list"){
      $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
    }
    else{
      $this->app->erp->MenuEintrag("index.php?module=geschaeftsbrief_vorlagen&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
    }
  }

  function Geschaeftsbrief_vorlagenPopup()
  {
  //  $this->app->Tpl->Parse("PAGE","geschaeftsbrief_vorlagen_popup.tpl");
  }


  /*function Geschaeftsbrief_vorlagenEdit()
  {
    $this->Geschaeftsbrief_vorlageMenu();
    parent::Geschaeftsbrief_vorlagenEdit();
  }*/

  function Geschaeftsbrief_vorlagenEdit()
  {
    $this->Geschaeftsbrief_vorlageMenu();


    if($this->app->Secure->GetGET('cmd')=='popupedit'){
      $eid = (int)$this->app->Secure->GetPOST('id');
      
      $data = $this->app->DB->SelectArr("SELECT id, sprache, betreff, text, subjekt, projekt FROM geschaeftsbrief_vorlagen WHERE id = '$eid' LIMIT 1");
      
      if($data){
        $data = reset($data);

        if($data['projekt'] > 0){
          $projektname = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '".$data['projekt']."' LIMIT 1");
          $data['projekt'] = $projektname;
        }else{
          $data['projekt'] = '';
        }
               
      }else{
        $data['id'] = '0';
        $data['sprache'] = 'deutsch';
        $data['betreff'] = '';
        $data['text'] = '';
        $data['subjekt'] = '';
        $data['projekt'] = ''; 
      }

      echo json_encode($data);
      $this->app->ExitXentral();

    }

    if($this->app->Secure->GetGET('cmd')==='popupsave'){
      $eid = $this->app->Secure->GetPOST('eid');
      $esprache = trim($this->app->Secure->GetPOST('esprache'));
      $ebetreff = trim($this->app->Secure->GetPOST('ebetreff'));
      $etext = trim($this->app->Secure->GetPOST('etext'));
      $esubjekt = trim($this->app->Secure->GetPOST('esubjekt'));
      $eprojekt = trim($this->app->Secure->GetPOST('eprojekt'));
      
      $error = "";

      if($esubjekt == ""){
        $error .= "Typf"."\n";
      }


      if($eprojekt != ""){
        $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '$eprojekt' LIMIT 1");

        if($projektid != ""){
          $eprojekt = $projektid;
        }else{
          $error .= "Kein gültiges Projekt"."\n";
        }
      }else{
        $eprojekt = 0;
      }

      if($error == ''){
        if($eid > 0){
          $this->app->DB->Update("UPDATE geschaeftsbrief_vorlagen SET sprache = '$esprache', betreff = '$ebetreff', text = '$etext', subjekt = '$esubjekt', projekt = '$eprojekt' WHERE id = '$eid'");
          echo json_encode(array('status'=>1));
          $this->app->ExitXentral();
        }
        $this->app->DB->Insert("INSERT INTO geschaeftsbrief_vorlagen (sprache, betreff, text, subjekt, projekt, firma) VALUES ('$esprache', '$ebetreff', '$etext', '$esubjekt', '$eprojekt', 1)");

        echo json_encode(array('status'=>1));
        $this->app->ExitXentral();
      }
      echo json_encode(array('status'=>0,'statusText'=>$error));
      $this->app->ExitXentral();
    }

    $this->app->Tpl->Parse('PAGE',"geschaeftsbrief_vorlagen_popup.tpl");
  }

  function Geschaeftsbrief_vorlageMenu() {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->MenuEintrag("index.php?module=geschaeftsbrief_vorlagen&action=edit&id=$id","Details");
    $anzahldateien = $this->app->erp->AnzahlDateien("geschaeftsbrief_vorlagen",$id);
    if($anzahldateien > 0) {
      $anzahldateien = ' ('.$anzahldateien.')';
    } else {
      $anzahldateien='';
    }

    $this->app->erp->MenuEintrag("index.php?module=geschaeftsbrief_vorlagen&action=dateien&id=$id","Dateien".$anzahldateien);
    $this->app->erp->MenuEintrag("index.php?module=geschaeftsbrief_vorlagen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }


  function Geschaeftsbrief_vorlagenDateien()
  {
    $id = $this->app->Secure->GetGET("id");
    $subjekt = $this->app->DB->Select("SELECT subjekt FROM geschaeftsbrief_vorlagen WHERE id='$id' LIMIT 1");
    $this->Geschaeftsbrief_vorlageMenu();
    $this->app->Tpl->Add('UEBERSCHRIFT'," (Dateien)");
    $this->app->YUI->DateiUpload('PAGE',"geschaeftsbrief_vorlagen",$id);
  }

  function Geschaeftsbrief_vorlagenCopyEdit()
  {
    //$id = (int)$this->app->Secure->GetGET('id');
    if($this->app->Secure->GetGET('cmd')==='get'){
      $id = (int)$this->app->Secure->GetPOST('id');
      $data = $this->app->DB->SelectArr("SELECT id, subjekt, sprache, betreff, text, projekt FROM geschaeftsbrief_vorlagen WHERE id = '$id' LIMIT 1");
      if($data){
        $data = reset($data);
        if($data['projekt'] > 0){
          $projektabkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '".$data['projekt']."' LIMIT 1");
          $data['projekt'] = $projektabkuerzung;
        }else{
          $data['projekt'] = "Standard Vorlage / ohne Projekt";
        }
        
      }

      if(!$this->app->erp->isHTML($data['text'])){
        $data['text'] = nl2br($data['text']);
      }

      echo json_encode($data);
      $this->app->ExitXentral();
    }
  }

  function Geschaeftsbrief_vorlagenCopySave(){
    $id = (int)$this->app->Secure->GetPOST('id');
    $subjekt = $this->app->Secure->GetPOST('subjekt');
    $sprache = $this->app->Secure->GetPOST('sprache');
    $projekt = $this->app->Secure->GetPOST('projekt');
    $betreff = $this->app->Secure->GetPOST('betreff');
    $text = $this->app->Secure->GetPOST('text');
    $dateien = $this->app->Secure->GetPOST('dateien');
    $error = '';
    
    if($id == '' || $id <= 0){
      $id = 0;
    }
    if($dateien == '' || $dateien <= 0){
      $dateien = 0;
    }
    
    if($subjekt == ''){
      $error .= "Typf"."\n";
    }

    if($error != ''){
      echo json_encode(array('status'=>0,'statusText'=>$error));
      $this->app->ExitXentral();
    }
    $data = array($id, $sprache, $projekt, $betreff, $text, $dateien, $subjekt);

    $this->Geschaeftsbrief_vorlagenCopy($data, true);
    echo json_encode(array('status'=>1));
    $this->app->ExitXentral();
  }


  function Geschaeftsbrief_vorlagenCopy($data = null, $return = false)
  {

    $id = $data[0];
    $sprache = $data[1];
    $projekt = $data[2];
    $betreff = $data[3];
    $text = $data[4];
    $dateien = $data[5];
    $subjekt = $data[6];
    
    $this->app->DB->MysqlCopyRow("geschaeftsbrief_vorlagen","id",$id);

    $idnew = $this->app->DB->GetInsertID();


    if($sprache == "DE"){
      $sprache = "deutsch";
    }elseif($sprache == "EN"){
      $sprache = "englisch";
    }

    if($projekt != ""){
      $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '$projekt' LIMIT 1");
    }else{
      $projektid = 0;
    }
    

    $this->app->DB->Update("UPDATE geschaeftsbrief_vorlagen SET sprache = '$sprache', betreff = '$betreff', text = '$text', subjekt = '$subjekt', projekt = '$projektid' WHERE id = '$idnew' LIMIT 1");

          
    if($dateien == 1){
      $dateien = $this->app->DB->SelectArr("SELECT DISTINCT datei FROM datei_stichwoerter WHERE parameter = '$id' AND objekt = 'geschaeftsbrief_vorlagen'");
      $datei_stichwoerter = $this->app->DB->SelectArr("SELECT id,datei FROM datei_stichwoerter WHERE parameter = '$id' AND objekt = 'geschaeftsbrief_vorlagen'");

      if($dateien){
        foreach($dateien as $datei){
          $titel = $this->app->DB->Select("SELECT titel FROM datei WHERE id='".$datei['datei']."' LIMIT 1");
          $beschreibung = $this->app->DB->Select("SELECT beschreibung FROM datei WHERE id='".$datei['datei']."' LIMIT 1");
          $nummer = $this->app->DB->Select("SELECT nummer FROM datei WHERE id='".$datei['datei']."' LIMIT 1");
          $name = $this->app->DB->Select("SELECT dateiname FROM datei_version WHERE datei='".$this->app->DB->real_escape_string($datei['datei'])."' ORDER by version DESC LIMIT 1");
          $ersteller = $this->app->User->GetName();
          $tmpnewdateiid = $this->app->erp->CreateDatei($name,$titel,$beschreibung,$nummer,$this->app->erp->GetDateiPfad($datei['datei']),$ersteller);
          $datei_mapping[$datei['datei']] = $tmpnewdateiid; 
        }
      }

      if($datei_stichwoerter){
        foreach($datei_stichwoerter as $datei){
          $neuesstichwort = $this->app->DB->MysqlCopyRow("datei_stichwoerter", "id", $datei['id']);
          $newdatei = $datei_mapping[$datei['datei']];
          $this->app->DB->Update("UPDATE datei_stichwoerter SET datei='$newdatei', parameter = '$idnew', objekt = 'geschaeftsbrief_vorlagen' WHERE id = '$neuesstichwort' LIMIT 1");
        }
      }
    }

    if($return) {
      return $idnew;
    }

    $msg = $this->app->erp->base64_url_encode("<div class=error>Sie befinden sich in der neuen Kopie der Vorlage.</div>"); 
    $this->app->Location->execute("index.php?module=geschaeftsbrief_vorlagen&action=edit&msg=$msg&id=".$idnew);
  }

}
