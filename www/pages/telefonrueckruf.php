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

class Telefonrueckruf {
  var $app;


  static function TableSearch(&$app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name)
    {
      case "telefonrueckruf_list":
        $allowed['telefonrueckruf'] = array('list');


        $heading = array('', 'Datum', 'Zeit', 'Kunde', 'Angenommen von', 'R&uuml;ckruf von', 'Telefonnummer', 'Anzahl', 'Men&uuml;', '');
        $width = array('1%', '8%', '5%', '15%', '10%', '10%', '10%', '10%', '10%', '1%', '1%');

        $findcols = array('open', 't.datum', 't.zeit', "if(t.adresse != '', a.name, t.adressetext)", "if(t.angenommenvon != '', a2.name, '')", "if(t.rueckrufvon != '', a3.name, '')", 't.telefonnummer', "(SELECT COUNT(tv.id) FROM telefonrueckruf_versuche tv WHERE tv.telefonrueckruf = t.id)", 't.id', 'b.id');
        $searchsql = array("DATE_FORMAT(t.datum,'%d.%m.%Y')", 't.zeit', "if(t.adresse != '', a.name, t.adressetext)", "if(t.angenommenvon != '', a2.name, '')", "if(t.rueckrufvon != '', a3.name, '')", 't.telefonnummer', 't.kommentar', "(SELECT COUNT(tv.id) FROM telefonrueckruf_versuche tv WHERE tv.telefonrueckruf = t.id)");

        $menucol = 8;


        $defaultorder = 1;
        $defaultorderdesc = 0;

        $disablebuttons=true;

        $datecols = array(0);

        $alignright = array(9);

        $moreinfo = true;
        $moreinfoaction='rueckruf';


        $finaktiv = $app->YUI->TableSearchFilter($name, 5, 'inaktiv', '0', 0, 'checkbox');
        $fmeinerueckrufe = $app->YUI->TableSearchFilter($name, 5, 'meinerueckrufe', '0', 0, 'checkbox');
        $fmeinevergebenenrueckrufe = $app->YUI->TableSearchFilter($name, 6, 'meinevergebenenrueckrufe', '0', 0, 'checkbox');
        $fauchabgeschlossene = $app->YUI->TableSearchFilter($name, 7, 'auchabgeschlossene', '0', 0, 'checkbox');

        if($fmeinerueckrufe == 1){
          $subwhere .= " AND t.rueckrufvon = ".$app->User->GetAdresse();
        }else{
          $subwhere .= "";
        }

        if($fmeinevergebenenrueckrufe == 1){
          $subwhere .= " AND t.angenommenvon = ".$app->User->GetAdresse();
        }else{
          $subwhere .= "";
        }

        if($fauchabgeschlossene == 1){
          $subwhere .= "";
        }else{
          $subwhere .= " AND t.abgeschlossen = 0";
        }

        $where = " t.id > 0".$subwhere;


        $sql = "SELECT SQL_CALC_FOUND_ROWS t.id, '<img src=./themes/".$app->Conf->WFconf['defaulttheme']."/images/details_open.png class=details>' as open, DATE_FORMAT(t.datum,'%d.%m.%Y') as datum, t.zeit, if(t.adresse != '', a.name, t.adressetext), if(t.angenommenvon != '', a2.name, ''), if(t.rueckrufvon != '', a3.name, ''), t.telefonnummer, (SELECT COUNT(tv.id) FROM telefonrueckruf_versuche tv WHERE tv.telefonrueckruf = t.id) as anzahlversuche,  
        CONCAT('<table cellpadding=0 cellspacing=0><tr><td nowrap>',
        '<a href=\"javascript:;\" onclick=\"TelefonrueckrufEdit(',t.id,');\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;',
        '<a href=\"javascript:;\" onclick=\"TelefonrueckrufDelete(',t.id,');\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>&nbsp;',
        IF(t.abgeschlossen = 0, CONCAT('<a href=\"javascript:;\" onclick=\"TelefonrueckrufAbgeschlossen(',t.id,');\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/ack.png\" border=\"0\"></a>&nbsp;'),''),
        '</td></tr></table>') as x,
         t.id FROM telefonrueckruf t LEFT JOIN adresse a ON t.adresse = a.id LEFT JOIN adresse a2 ON t.angenommenvon = a2.id LEFT JOIN adresse a3 ON t.rueckrufvon = a3.id";

        $count = "SELECT count(t.id) FROM telefonrueckruf t WHERE $where";
      break;

      case "telefonrueckruf_versuche":
        $allowed['telefonrueckruf'] = array('edit');
        $heading = array('', 'Datum', 'Bearbeiter', 'Men&uuml;');
        $width = array('1%', '30%', '70%', '1%');

        $findcols = array('open', "CONCAT(tv.datum, ' ', tv.zeit)", "CONCAT(a.name, '<br />', LEFT(tv.beschreibung,50))", 'tv.id');
        $searchsql = array('tv.datum', 'tv.zeit', 'a.name', 'tv.beschreibung','tv.id');

        $defaultorder = 1;
        $defaultorderdesc = 0;

        $datecols = array(1);

        $disablebuttons=true;

        $moreinfo = true;
        $menucol = 4;


        $telefonrueckrufid = $app->Secure->GetGET("more_data1");


        $menu = "<table cellpadding=0 cellspacing=0>";
          $menu .= "<tr>";
            $menu .= "<td nowrap>";
              $menu .= '<a href="javascript:;" onclick="NeuerVersuchEdit(%value%);">';
                $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
              $menu .= "</a>&nbsp;";
              $menu .= '<a href="javascript:;" onclick="NeuerVersuchDelete(%value%);">';
                $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
              $menu .= "</a>";
            $menu .= "</td>";
          $menu .= "</tr>";
        $menu .= "</table>";

        $where = " tv.id > 0 AND tv.telefonrueckruf = '$telefonrueckrufid'";

        $sql = "SELECT SQL_CALC_FOUND_ROWS tv.id, '<img src=./themes/".$app->Conf->WFconf['defaulttheme']."/images/details_open.png class=details>' as open, CONCAT(DATE_FORMAT(tv.datum,'%d.%m.%Y'), ' ', tv.zeit), CONCAT('<i style=color:#999>',a.name,'</i><br />', LEFT(tv.beschreibung,50)), tv.id FROM telefonrueckruf_versuche tv LEFT JOIN adresse a ON tv.bearbeiter = a.id";

        $count = "SELECT count(tv.id) FROM telefonrueckruf_versuche tv WHERE $where";
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
    $this->app=&$app;
    if($intern)return;
    $this->app->ActionHandlerInit($this);

    // ab hier alle Action Handler definieren die das Modul hat
    $this->app->ActionHandler("list", "TelefonrueckrufList");
    $this->app->ActionHandler("edit", "TelefonrueckrufEdit");
    $this->app->ActionHandler("save", "TelefonrueckrufSave");
    $this->app->ActionHandler("delete", "TelefonrueckrufDelete");
    $this->app->ActionHandler("abgeschlossen", "TelefonrueckrufAbgeschlossen");
    $this->app->ActionHandler("editversuch", "TelefonrueckrufVersuchEdit");
    $this->app->ActionHandler("saveversuch", "TelefonrueckrufVersuchSave");
    $this->app->ActionHandler("deleteversuch", "TelefonrueckrufVersuchDelete");
    $this->app->ActionHandler("minidetail", "TelefonrueckrufVersucheMinidetail");
    $this->app->ActionHandler("minidetailrueckruf", "TelefonrueckrufMinidetail");
    $this->app->ActionHandlerListen($app);
  }

  function Install(){
    $this->app->erp->CheckTable("telefonrueckruf");
    $this->app->erp->CheckColumn("id", "int(11)", "telefonrueckruf", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("datum", "date", "telefonrueckruf", "NOT NULL");
    $this->app->erp->CheckColumn("zeit", "time", "telefonrueckruf", "NOT NULL");
    $this->app->erp->CheckColumn("adresse", "int(11)", "telefonrueckruf", "NOT NULL DEFAULT 0");
    $this->app->erp->CheckColumn("adressetext", "varchar(255)", "telefonrueckruf", "NOT NULL");
    $this->app->erp->CheckColumn("grund", "varchar(255)", "telefonrueckruf", "NOT NULL");
    $this->app->erp->CheckColumn("angenommenvon", "int(11)", "telefonrueckruf", "NOT NULL DEFAULT 0");
    $this->app->erp->CheckColumn("rueckrufvon", "int(11)", "telefonrueckruf", "NOT NULL DEFAULT 0");
    $this->app->erp->CheckColumn("telefonnummer", "varchar(255)", "telefonrueckruf", "NOT NULL");
    $this->app->erp->CheckColumn("kommentar", "text", "telefonrueckruf", "NOT NULL");
    $this->app->erp->CheckColumn("abgeschlossen", "tinyint", "telefonrueckruf", "NOT NULL DEFAULT 0");
    $this->app->erp->CheckIndex("telefonrueckruf", "rueckrufvon");

    $this->app->erp->CheckTable("telefonrueckruf_versuche");
    $this->app->erp->CheckColumn("id", "int(11)", "telefonrueckruf_versuche", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("telefonrueckruf", "int(11)", "telefonrueckruf_versuche", "NOT NULL DEFAULT 0");
    $this->app->erp->CheckColumn("datum", "date", "telefonrueckruf_versuche", "NOT NULL");
    $this->app->erp->CheckColumn("zeit", "time", "telefonrueckruf_versuche", "NOT NULL");
    $this->app->erp->CheckColumn("bearbeiter", "int(11)", "telefonrueckruf_versuche", "NOT NULL DEFAULT 0");
    $this->app->erp->CheckColumn("beschreibung", "text", "telefonrueckruf_versuche", "NOT NULL");
    $this->app->erp->CheckIndex("telefonrueckruf_versuche", "telefonrueckruf");

    $this->app->DB->Query("ALTER TABLE telefonrueckruf_versuche MODIFY beschreibung text");


    $this->app->erp->RegisterHook('eproosystem_ende','telefonrueckruf','TelefonrueckrufAnzahl');

  }
  
  function TelefonrueckrufAnzahl($tpl="")
  {

    $anzahl = $this->app->DB->Select("SELECT COUNT(id) FROM telefonrueckruf WHERE rueckrufvon='".$this->app->User->GetAdresse()."' AND abgeschlossen!=1");
    if($anzahl <=0) $anzahl="";
    $this->app->Tpl->Set("ANZAHLTELEFON",$anzahl);
  }

  function TelefonrueckrufMenu()
  {
    $this->app->erp->MenuEintrag("index.php?module=telefonrueckruf&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=telefonrueckruf&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("TelefonrueckrufEdit(0)","Neuer Eintrag");
  }

  function TelefonrueckrufList()
  {
    $this->TelefonrueckrufMenu();
    $this->app->Tpl->Set("KURZUEBERSCHRIFT","Telefonr&uuml;ckruf");

    $this->app->YUI->AutoComplete("evon", "adresse");
    $this->app->YUI->AutoComplete("emitarbeiter", "mitarbeiteraktuell");
    $this->app->YUI->DatePicker("edatum");
    $this->app->YUI->TimePicker("ezeit");
    $this->app->YUI->CkEditor("ebeschreibung", "basic", array("width"=>"300"));
    $this->app->YUI->CkEditor("enotiz", "basic", array("width"=>"400", "height"=>"100"));
    
    $this->app->YUI->TableSearch('TAB1','telefonrueckruf_list', "show","","",basename(__FILE__), __CLASS__);

    $this->app->YUI->TableSearch('VERSUCHE', 'telefonrueckruf_versuche', "show", "","",basename(__FILE__), __CLASS__);

    $this->app->Tpl->Parse("PAGE","telefonrueckruf_list.tpl");
  }

  function TelefonrueckrufEdit()
  {
    if($this->app->Secure->GetGET('cmd')=='get'){
      $id = (int)$this->app->Secure->GetPOST('id');

      $data = $this->app->DB->SelectArr("SELECT t.id, t.datum, t.zeit, t.adresse, t.adressetext, t.grund, t.rueckrufvon, t.telefonnummer, t.kommentar FROM telefonrueckruf t WHERE t.id = '$id' LIMIT 1");

      if($data){
        $data = reset($data);

        if($data['adresse'] != "" && $data['adresse'] > 0){
          $adressid = $this->app->DB->Select("SELECT id FROM adresse WHERE id = '".$data['adresse']."' LIMIT 1");
          if($adressid != ""){
            $adressname = $this->app->DB->Select("SELECT name FROM adresse WHERE id = '$adressid' LIMIT 1");
            if($adressname != ""){
              $kundennr = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$adressid' LIMIT 1");
              $lieferantennr = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id = '$adressid' LIMIT 1");

              $restadresse = "";
              $restadresse .= "(Kdr: ".$kundennr." ";

              if($lieferantennr != ""){
                $restadresse .= "Liefr: ".$lieferantennr;
              }

              $restadresse .= ")";

              $data['von'] = $adressid." ".$adressname." ".$restadresse;

            }
          }
        }else{
          $data['von'] = $data['adressetext'];
        }


        $data['datum'] = date('d.m.Y',strtotime($data['datum']));
        $data['zeit'] = date('H:i',strtotime($data['zeit']));

        if($data['rueckrufvon'] == 0){
          $data['rueckrufvon'] = '';
        }else{
          $mitarbeiternummer = $this->app->DB->Select("SELECT mitarbeiternummer FROM adresse WHERE id = '".$data['rueckrufvon']."' LIMIT 1");
          if($mitarbeiternummer != ""){
            $mitarbeitername = $this->app->DB->Select("SELECT name FROM adresse WHERE id = '".$data['rueckrufvon']."' LIMIT 1");
            $data['rueckrufvon'] = $mitarbeiternummer." ".$mitarbeitername;
          }
        }
      }else{
        $data['id'] = 0;
        $data['datum'] = '';
        $data['zeit'] = '';
        $data['von'] = '';
        $data['grund'] = '';
        $data['rueckrufvon'] = '';
        $data['telefonnummer'] = '';
        $data['beschreibung'] = '';
      }
      echo json_encode($data);
      exit;
    }

    $this->app->Tpl->Parse('PAGE', "telefonrueckruf_list.tpl");
  }

  function TelefonrueckrufSave()
  { 
    $id = (int)$this->app->Secure->GetPOST('id');
    $datum = trim($this->app->Secure->GetPOST('datum'));
    $zeit = trim($this->app->Secure->GetPOST('zeit'));
    $adresse = trim($this->app->Secure->GetPOST('von'));
    $grund = $this->app->Secure->GetPOST('grund');
    $kommentar = $this->app->Secure->GetPOST('beschreibung');
    $telefonnummer = $this->app->Secure->GetPOST('telefon');
    $rueckrufvon = $this->app->Secure->GetPOST('mitarbeiter');


    $error = "";

    if($datum != ""){
      $datum = date('Y-m-d',strtotime($datum));
    }else{
      $datum = date('Y-m-d');     
    }

    if($zeit != ""){
      $zeit = date('H:i:s',strtotime($zeit));
    }else{
      $zeit = date('H:i:s');      
    }

    if($adresse != ""){
      $adressetmp = explode(" ", $adresse);
      $adressid = $adressetmp[0];
      if(is_numeric($adressid)){
        $adressid = $this->app->DB->Select("SELECT id FROM adresse WHERE id = '$adressid' LIMIT 1");
        if($adressid != ""){
        }else{
          $adressetext = $adresse;
        }
      }else{
        $adressetext = $adresse;
      }
    }else{
      $error = "Bitte Von ausfüllen"."\n";
    }

    if($rueckrufvon != ""){
      $rueckrufvon = explode(" ", $rueckrufvon);
      $rueckrufvonnr = $rueckrufvon[0];
      $rueckrufvonid = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer = '$rueckrufvonnr' LIMIT 1");
      if($rueckrufvonid != ""){
      }else{
        $error .= "Bitte gültigen Mitarbeiter auswählen"."\n";
      }
    }


    if($error == ""){
      if($id){

        $this->app->DB->Update("UPDATE telefonrueckruf SET datum = '$datum', zeit = '$zeit', grund = '$grund', adresse = '$adressid', adressetext = '$adressetext', kommentar = '$kommentar', telefonnummer = '$telefonnummer', rueckrufvon = '$rueckrufvonid' WHERE id = '$id'");

        echo json_encode(array('status'=>1));
        exit;
      }else{
        $this->app->DB->Insert("INSERT INTO telefonrueckruf (datum, zeit, grund, adresse, adressetext, kommentar, telefonnummer, rueckrufvon, angenommenvon) VALUES ('$datum', '$zeit', '$grund', '$adressid', '$adressetext', '$kommentar', '$telefonnummer', '$rueckrufvonid', ".$this->app->User->GetAdresse().")");
        echo json_encode(array('status'=>1));
        exit;          
       
      } 
    }else{
      echo json_encode(array('status'=>0,'statusText'=>$error));
      exit;

    }
    
  }

  function TelefonrueckrufDelete()
  {    
    $id = (int) $this->app->Secure->GetPOST('id');
    $this->app->DB->Update("DELETE FROM telefonrueckruf WHERE id = '$id'");
        
    echo json_encode(array('status'=>1));
    exit;    
  
  }

  function TelefonrueckrufAbgeschlossen()
  {
    $id = (int) $this->app->Secure->GetPOST('id');
    $this->app->DB->Update("UPDATE telefonrueckruf SET abgeschlossen = 1 WHERE id = '$id'");

    echo json_encode(array('status'=>1));
    exit;
  }


  function TelefonrueckrufVersuchEdit(){

    if($this->app->Secure->GetGET('cmd')=='get'){
      $id = (int)$this->app->Secure->GetPOST('id');
     
      $data = $this->app->DB->SelectArr("SELECT tv.id, tv.beschreibung, tv.telefonrueckruf FROM telefonrueckruf_versuche tv WHERE tv.id = '$id' LIMIT 1");
      
      if($data){
        $data = reset($data);

      }else{
        $data['id'] = 0;
        $data['beschreibung'] = '';
      }

      echo json_encode($data);
      exit;
    }

    $this->app->Tpl->Parse('PAGE', "telefonrueckruf_list.tpl");
  }



  function TelefonrueckrufVersuchSave(){
    $id = (int)$this->app->Secure->GetPOST('id');
    $beschreibung = trim($this->app->Secure->GetPOST('beschreibung'));
    $telefonrueckruf = (int)$this->app->Secure->GetPOST('telefonrueckruf');

    $error = "";

    if($telefonrueckruf > 0){
      $telefonrueckrufcheck = $this->app->DB->Select("SELECT id FROM telefonrueckruf WHERE id = '$telefonrueckruf' LIMIT 1");
      if($telefonrueckrufcheck != ""){
      }else{
        $error .= "Kein gültiger Telefonrückruf ausgewählt"."\n";
      }
    }else{
      $error .= "Kein gültiger Telefonrückruf ausgewählt"."\n";
    }


    if($error == ""){
      if($id){
        $this->app->DB->Update("UPDATE telefonrueckruf_versuche SET beschreibung = '$beschreibung' WHERE id = '$id'");

        echo json_encode(array('status'=>1));
        exit;
      }else{

        $datum = date('Y-m-d');
        $zeit = date('H:i:s');

        $this->app->DB->Insert("INSERT INTO telefonrueckruf_versuche (telefonrueckruf, datum, zeit, bearbeiter, beschreibung) VALUES ('$telefonrueckruf', '$datum', '$zeit', ".$this->app->User->GetAdresse().", '$beschreibung')");

        echo json_encode(array('status'=>1));
        exit;          
       
      } 
    }else{
      echo json_encode(array('status'=>0,'statusText'=>$error));
      exit;

    }
  }

  function TelefonrueckrufVersuchDelete(){
    $id = (int) $this->app->Secure->GetPOST('id');
    $this->app->DB->Update("DELETE FROM telefonrueckruf_versuche WHERE id = '$id'");
        
    echo json_encode(array('status'=>1));
    exit;   
  }

  function TelefonrueckrufVersucheMinidetail(){
    $id = $this->app->Secure->GetGET('id');

    $table = new EasyTable($this->app);

    $table->Query("SELECT beschreibung FROM telefonrueckruf_versuche WHERE id = '$id' AND beschreibung != '' LIMIT 1");
    
    $table->DisplayNew("TABELLE","Notiz","noAction");

    $this->app->Tpl->Output("telefonrueckruf_versuch_minidetail.tpl");
    exit;
  }

  function TelefonrueckrufMinidetail(){
    $id = $this->app->Secure->GetGET('id');

    $table = new EasyTable($this->app);

    $table->Query("SELECT kommentar FROM telefonrueckruf WHERE id = '$id' LIMIT 1");
    
    $table->DisplayNew("TABELLE","Notiz","noAction");

    $table2 = new EasyTable($this->app);
    $table2->Query("SELECT DATE_FORMAT(tv.datum, '%d.%m.%Y') as datum, tv.zeit, a.name, tv.beschreibung FROM telefonrueckruf_versuche tv LEFT JOIN adresse a on tv.bearbeiter = a.id WHERE tv.id > 0 AND tv.telefonrueckruf = '$id' ORDER BY tv.datum DESC, tv.zeit DESC");
    $table2->DisplayNew("TABELLE2", "Notiz Versuche", "noAction");

    $this->app->Tpl->Output("telefonrueckruf_minidetail.tpl");
    exit;
  }
  

}
