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

class ObjGenWiedervorlage
{

  private  $id;
  private  $adresse;
  private  $projekt;
  private  $adresse_mitarbeier;
  private  $bezeichnung;
  private  $beschreibung;
  private  $ergebnis;
  private  $betrag;
  private  $erinnerung;
  private  $erinnerung_per_mail;
  private  $erinnerung_empfaenger;
  private  $link;
  private  $module;
  private  $action;
  private  $status;
  private  $bearbeiter;
  private  $adresse_mitarbeiter;
  private  $datum_angelegt;
  private  $zeit_angelegt;
  private  $datum_erinnerung;
  private  $zeit_erinnerung;
  private  $parameter;
  private  $oeffentlich;
  private  $abgeschlossen;
  private  $prio;
  private  $stages;
  private  $chance;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM wiedervorlage WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->adresse=$result['adresse'];
    $this->projekt=$result['projekt'];
    $this->adresse_mitarbeier=$result['adresse_mitarbeier'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->beschreibung=$result['beschreibung'];
    $this->ergebnis=$result['ergebnis'];
    $this->betrag=$result['betrag'];
    $this->erinnerung=$result['erinnerung'];
    $this->erinnerung_per_mail=$result['erinnerung_per_mail'];
    $this->erinnerung_empfaenger=$result['erinnerung_empfaenger'];
    $this->link=$result['link'];
    $this->module=$result['module'];
    $this->action=$result['action'];
    $this->status=$result['status'];
    $this->bearbeiter=$result['bearbeiter'];
    $this->adresse_mitarbeiter=$result['adresse_mitarbeiter'];
    $this->datum_angelegt=$result['datum_angelegt'];
    $this->zeit_angelegt=$result['zeit_angelegt'];
    $this->datum_erinnerung=$result['datum_erinnerung'];
    $this->zeit_erinnerung=$result['zeit_erinnerung'];
    $this->parameter=$result['parameter'];
    $this->oeffentlich=$result['oeffentlich'];
    $this->abgeschlossen=$result['abgeschlossen'];
    $this->prio=$result['prio'];
    $this->stages=$result['stages'];
    $this->chance=$result['chance'];
  }

  public function Create()
  {
    $sql = "INSERT INTO wiedervorlage (id,adresse,projekt,adresse_mitarbeier,bezeichnung,beschreibung,ergebnis,betrag,erinnerung,erinnerung_per_mail,erinnerung_empfaenger,link,module,action,status,bearbeiter,adresse_mitarbeiter,datum_angelegt,zeit_angelegt,datum_erinnerung,zeit_erinnerung,parameter,oeffentlich,abgeschlossen,prio,stages,chance)
      VALUES('','{$this->adresse}','{$this->projekt}','{$this->adresse_mitarbeier}','{$this->bezeichnung}','{$this->beschreibung}','{$this->ergebnis}','{$this->betrag}','{$this->erinnerung}','{$this->erinnerung_per_mail}','{$this->erinnerung_empfaenger}','{$this->link}','{$this->module}','{$this->action}','{$this->status}','{$this->bearbeiter}','{$this->adresse_mitarbeiter}','{$this->datum_angelegt}','{$this->zeit_angelegt}','{$this->datum_erinnerung}','{$this->zeit_erinnerung}','{$this->parameter}','{$this->oeffentlich}','{$this->abgeschlossen}','{$this->prio}','{$this->stages}','{$this->chance}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE wiedervorlage SET
      adresse='{$this->adresse}',
      projekt='{$this->projekt}',
      adresse_mitarbeier='{$this->adresse_mitarbeier}',
      bezeichnung='{$this->bezeichnung}',
      beschreibung='{$this->beschreibung}',
      ergebnis='{$this->ergebnis}',
      betrag='{$this->betrag}',
      erinnerung='{$this->erinnerung}',
      erinnerung_per_mail='{$this->erinnerung_per_mail}',
      erinnerung_empfaenger='{$this->erinnerung_empfaenger}',
      link='{$this->link}',
      module='{$this->module}',
      action='{$this->action}',
      status='{$this->status}',
      bearbeiter='{$this->bearbeiter}',
      adresse_mitarbeiter='{$this->adresse_mitarbeiter}',
      datum_angelegt='{$this->datum_angelegt}',
      zeit_angelegt='{$this->zeit_angelegt}',
      datum_erinnerung='{$this->datum_erinnerung}',
      zeit_erinnerung='{$this->zeit_erinnerung}',
      parameter='{$this->parameter}',
      oeffentlich='{$this->oeffentlich}',
      abgeschlossen='{$this->abgeschlossen}',
      prio='{$this->prio}',
      stages='{$this->stages}',
      chance='{$this->chance}'
      WHERE (id='{$this->id}')";

    $this->app->DB->Update($sql);
  }

  public function Delete($id="")
  {
    if(is_numeric($id))
    {
      $this->id=$id;
    }
    else
      return -1;

    $sql = "DELETE FROM wiedervorlage WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->projekt="";
    $this->adresse_mitarbeier="";
    $this->bezeichnung="";
    $this->beschreibung="";
    $this->ergebnis="";
    $this->betrag="";
    $this->erinnerung="";
    $this->erinnerung_per_mail="";
    $this->erinnerung_empfaenger="";
    $this->link="";
    $this->module="";
    $this->action="";
    $this->status="";
    $this->bearbeiter="";
    $this->adresse_mitarbeiter="";
    $this->datum_angelegt="";
    $this->zeit_angelegt="";
    $this->datum_erinnerung="";
    $this->zeit_erinnerung="";
    $this->parameter="";
    $this->oeffentlich="";
    $this->abgeschlossen="";
    $this->prio="";
    $this->stages="";
    $this->chance="";
  }

  public function Copy()
  {
    $this->id = "";
    $this->Create();
  }

 /** 
   Mit dieser Funktion kann man einen Datensatz suchen 
   dafuer muss man die Attribute setzen nach denen gesucht werden soll
   dann kriegt man als ergebnis den ersten Datensatz der auf die Suche uebereinstimmt
   zurueck. Mit Next() kann man sich alle weiteren Ergebnisse abholen
   **/ 

  public function Find()
  {
    //TODO Suche mit den werten machen
  }

  public function FindNext()
  {
    //TODO Suche mit den alten werten fortsetzen machen
  }

 /** Funktionen um durch die Tabelle iterieren zu koennen */ 

  public function Next()
  {
    //TODO: SQL Statement passt nach meiner Meinung nach noch nicht immer
  }

  public function First()
  {
    //TODO: SQL Statement passt nach meiner Meinung nach noch nicht immer
  }

 /** dank dieser funktionen kann man die tatsaechlichen werte einfach 
  ueberladen (in einem Objekt das mit seiner klasse ueber dieser steht)**/ 

  function SetId($value) { $this->id=$value; }
  function GetId() { return $this->id; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetAdresse_Mitarbeier($value) { $this->adresse_mitarbeier=$value; }
  function GetAdresse_Mitarbeier() { return $this->adresse_mitarbeier; }
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetErgebnis($value) { $this->ergebnis=$value; }
  function GetErgebnis() { return $this->ergebnis; }
  function SetBetrag($value) { $this->betrag=$value; }
  function GetBetrag() { return $this->betrag; }
  function SetErinnerung($value) { $this->erinnerung=$value; }
  function GetErinnerung() { return $this->erinnerung; }
  function SetErinnerung_Per_Mail($value) { $this->erinnerung_per_mail=$value; }
  function GetErinnerung_Per_Mail() { return $this->erinnerung_per_mail; }
  function SetErinnerung_Empfaenger($value) { $this->erinnerung_empfaenger=$value; }
  function GetErinnerung_Empfaenger() { return $this->erinnerung_empfaenger; }
  function SetLink($value) { $this->link=$value; }
  function GetLink() { return $this->link; }
  function SetModule($value) { $this->module=$value; }
  function GetModule() { return $this->module; }
  function SetAction($value) { $this->action=$value; }
  function GetAction() { return $this->action; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetAdresse_Mitarbeiter($value) { $this->adresse_mitarbeiter=$value; }
  function GetAdresse_Mitarbeiter() { return $this->adresse_mitarbeiter; }
  function SetDatum_Angelegt($value) { $this->datum_angelegt=$value; }
  function GetDatum_Angelegt() { return $this->datum_angelegt; }
  function SetZeit_Angelegt($value) { $this->zeit_angelegt=$value; }
  function GetZeit_Angelegt() { return $this->zeit_angelegt; }
  function SetDatum_Erinnerung($value) { $this->datum_erinnerung=$value; }
  function GetDatum_Erinnerung() { return $this->datum_erinnerung; }
  function SetZeit_Erinnerung($value) { $this->zeit_erinnerung=$value; }
  function GetZeit_Erinnerung() { return $this->zeit_erinnerung; }
  function SetParameter($value) { $this->parameter=$value; }
  function GetParameter() { return $this->parameter; }
  function SetOeffentlich($value) { $this->oeffentlich=$value; }
  function GetOeffentlich() { return $this->oeffentlich; }
  function SetAbgeschlossen($value) { $this->abgeschlossen=$value; }
  function GetAbgeschlossen() { return $this->abgeschlossen; }
  function SetPrio($value) { $this->prio=$value; }
  function GetPrio() { return $this->prio; }
  function SetStages($value) { $this->stages=$value; }
  function GetStages() { return $this->stages; }
  function SetChance($value) { $this->chance=$value; }
  function GetChance() { return $this->chance; }

}

?>