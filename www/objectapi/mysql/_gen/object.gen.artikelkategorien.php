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

class ObjGenArtikelkategorien
{

  private  $id;
  private  $bezeichnung;
  private  $next_nummer;
  private  $projekt;
  private  $geloescht;
  private  $externenummer;
  private  $parent;
  private  $steuer_erloese_inland_normal;
  private  $steuer_aufwendung_inland_normal;
  private  $steuer_erloese_inland_ermaessigt;
  private  $steuer_aufwendung_inland_ermaessigt;
  private  $steuer_erloese_inland_steuerfrei;
  private  $steuer_aufwendung_inland_steuerfrei;
  private  $steuer_erloese_inland_innergemeinschaftlich;
  private  $steuer_aufwendung_inland_innergemeinschaftlich;
  private  $steuer_erloese_inland_eunormal;
  private  $steuer_erloese_inland_nichtsteuerbar;
  private  $steuer_erloese_inland_euermaessigt;
  private  $steuer_aufwendung_inland_nichtsteuerbar;
  private  $steuer_aufwendung_inland_eunormal;
  private  $steuer_aufwendung_inland_euermaessigt;
  private  $steuer_erloese_inland_export;
  private  $steuer_aufwendung_inland_import;
  private  $steuertext_innergemeinschaftlich;
  private  $steuertext_export;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM artikelkategorien WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->next_nummer=$result['next_nummer'];
    $this->projekt=$result['projekt'];
    $this->geloescht=$result['geloescht'];
    $this->externenummer=$result['externenummer'];
    $this->parent=$result['parent'];
    $this->steuer_erloese_inland_normal=$result['steuer_erloese_inland_normal'];
    $this->steuer_aufwendung_inland_normal=$result['steuer_aufwendung_inland_normal'];
    $this->steuer_erloese_inland_ermaessigt=$result['steuer_erloese_inland_ermaessigt'];
    $this->steuer_aufwendung_inland_ermaessigt=$result['steuer_aufwendung_inland_ermaessigt'];
    $this->steuer_erloese_inland_steuerfrei=$result['steuer_erloese_inland_steuerfrei'];
    $this->steuer_aufwendung_inland_steuerfrei=$result['steuer_aufwendung_inland_steuerfrei'];
    $this->steuer_erloese_inland_innergemeinschaftlich=$result['steuer_erloese_inland_innergemeinschaftlich'];
    $this->steuer_aufwendung_inland_innergemeinschaftlich=$result['steuer_aufwendung_inland_innergemeinschaftlich'];
    $this->steuer_erloese_inland_eunormal=$result['steuer_erloese_inland_eunormal'];
    $this->steuer_erloese_inland_nichtsteuerbar=$result['steuer_erloese_inland_nichtsteuerbar'];
    $this->steuer_erloese_inland_euermaessigt=$result['steuer_erloese_inland_euermaessigt'];
    $this->steuer_aufwendung_inland_nichtsteuerbar=$result['steuer_aufwendung_inland_nichtsteuerbar'];
    $this->steuer_aufwendung_inland_eunormal=$result['steuer_aufwendung_inland_eunormal'];
    $this->steuer_aufwendung_inland_euermaessigt=$result['steuer_aufwendung_inland_euermaessigt'];
    $this->steuer_erloese_inland_export=$result['steuer_erloese_inland_export'];
    $this->steuer_aufwendung_inland_import=$result['steuer_aufwendung_inland_import'];
    $this->steuertext_innergemeinschaftlich=$result['steuertext_innergemeinschaftlich'];
    $this->steuertext_export=$result['steuertext_export'];
  }

  public function Create()
  {
    $sql = "INSERT INTO artikelkategorien (id,bezeichnung,next_nummer,projekt,geloescht,externenummer,parent,steuer_erloese_inland_normal,steuer_aufwendung_inland_normal,steuer_erloese_inland_ermaessigt,steuer_aufwendung_inland_ermaessigt,steuer_erloese_inland_steuerfrei,steuer_aufwendung_inland_steuerfrei,steuer_erloese_inland_innergemeinschaftlich,steuer_aufwendung_inland_innergemeinschaftlich,steuer_erloese_inland_eunormal,steuer_erloese_inland_nichtsteuerbar,steuer_erloese_inland_euermaessigt,steuer_aufwendung_inland_nichtsteuerbar,steuer_aufwendung_inland_eunormal,steuer_aufwendung_inland_euermaessigt,steuer_erloese_inland_export,steuer_aufwendung_inland_import,steuertext_innergemeinschaftlich,steuertext_export)
      VALUES('','{$this->bezeichnung}','{$this->next_nummer}','{$this->projekt}','{$this->geloescht}','{$this->externenummer}','{$this->parent}','{$this->steuer_erloese_inland_normal}','{$this->steuer_aufwendung_inland_normal}','{$this->steuer_erloese_inland_ermaessigt}','{$this->steuer_aufwendung_inland_ermaessigt}','{$this->steuer_erloese_inland_steuerfrei}','{$this->steuer_aufwendung_inland_steuerfrei}','{$this->steuer_erloese_inland_innergemeinschaftlich}','{$this->steuer_aufwendung_inland_innergemeinschaftlich}','{$this->steuer_erloese_inland_eunormal}','{$this->steuer_erloese_inland_nichtsteuerbar}','{$this->steuer_erloese_inland_euermaessigt}','{$this->steuer_aufwendung_inland_nichtsteuerbar}','{$this->steuer_aufwendung_inland_eunormal}','{$this->steuer_aufwendung_inland_euermaessigt}','{$this->steuer_erloese_inland_export}','{$this->steuer_aufwendung_inland_import}','{$this->steuertext_innergemeinschaftlich}','{$this->steuertext_export}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE artikelkategorien SET
      bezeichnung='{$this->bezeichnung}',
      next_nummer='{$this->next_nummer}',
      projekt='{$this->projekt}',
      geloescht='{$this->geloescht}',
      externenummer='{$this->externenummer}',
      parent='{$this->parent}',
      steuer_erloese_inland_normal='{$this->steuer_erloese_inland_normal}',
      steuer_aufwendung_inland_normal='{$this->steuer_aufwendung_inland_normal}',
      steuer_erloese_inland_ermaessigt='{$this->steuer_erloese_inland_ermaessigt}',
      steuer_aufwendung_inland_ermaessigt='{$this->steuer_aufwendung_inland_ermaessigt}',
      steuer_erloese_inland_steuerfrei='{$this->steuer_erloese_inland_steuerfrei}',
      steuer_aufwendung_inland_steuerfrei='{$this->steuer_aufwendung_inland_steuerfrei}',
      steuer_erloese_inland_innergemeinschaftlich='{$this->steuer_erloese_inland_innergemeinschaftlich}',
      steuer_aufwendung_inland_innergemeinschaftlich='{$this->steuer_aufwendung_inland_innergemeinschaftlich}',
      steuer_erloese_inland_eunormal='{$this->steuer_erloese_inland_eunormal}',
      steuer_erloese_inland_nichtsteuerbar='{$this->steuer_erloese_inland_nichtsteuerbar}',
      steuer_erloese_inland_euermaessigt='{$this->steuer_erloese_inland_euermaessigt}',
      steuer_aufwendung_inland_nichtsteuerbar='{$this->steuer_aufwendung_inland_nichtsteuerbar}',
      steuer_aufwendung_inland_eunormal='{$this->steuer_aufwendung_inland_eunormal}',
      steuer_aufwendung_inland_euermaessigt='{$this->steuer_aufwendung_inland_euermaessigt}',
      steuer_erloese_inland_export='{$this->steuer_erloese_inland_export}',
      steuer_aufwendung_inland_import='{$this->steuer_aufwendung_inland_import}',
      steuertext_innergemeinschaftlich='{$this->steuertext_innergemeinschaftlich}',
      steuertext_export='{$this->steuertext_export}'
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

    $sql = "DELETE FROM artikelkategorien WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->bezeichnung="";
    $this->next_nummer="";
    $this->projekt="";
    $this->geloescht="";
    $this->externenummer="";
    $this->parent="";
    $this->steuer_erloese_inland_normal="";
    $this->steuer_aufwendung_inland_normal="";
    $this->steuer_erloese_inland_ermaessigt="";
    $this->steuer_aufwendung_inland_ermaessigt="";
    $this->steuer_erloese_inland_steuerfrei="";
    $this->steuer_aufwendung_inland_steuerfrei="";
    $this->steuer_erloese_inland_innergemeinschaftlich="";
    $this->steuer_aufwendung_inland_innergemeinschaftlich="";
    $this->steuer_erloese_inland_eunormal="";
    $this->steuer_erloese_inland_nichtsteuerbar="";
    $this->steuer_erloese_inland_euermaessigt="";
    $this->steuer_aufwendung_inland_nichtsteuerbar="";
    $this->steuer_aufwendung_inland_eunormal="";
    $this->steuer_aufwendung_inland_euermaessigt="";
    $this->steuer_erloese_inland_export="";
    $this->steuer_aufwendung_inland_import="";
    $this->steuertext_innergemeinschaftlich="";
    $this->steuertext_export="";
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
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetNext_Nummer($value) { $this->next_nummer=$value; }
  function GetNext_Nummer() { return $this->next_nummer; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }
  function SetExternenummer($value) { $this->externenummer=$value; }
  function GetExternenummer() { return $this->externenummer; }
  function SetParent($value) { $this->parent=$value; }
  function GetParent() { return $this->parent; }
  function SetSteuer_Erloese_Inland_Normal($value) { $this->steuer_erloese_inland_normal=$value; }
  function GetSteuer_Erloese_Inland_Normal() { return $this->steuer_erloese_inland_normal; }
  function SetSteuer_Aufwendung_Inland_Normal($value) { $this->steuer_aufwendung_inland_normal=$value; }
  function GetSteuer_Aufwendung_Inland_Normal() { return $this->steuer_aufwendung_inland_normal; }
  function SetSteuer_Erloese_Inland_Ermaessigt($value) { $this->steuer_erloese_inland_ermaessigt=$value; }
  function GetSteuer_Erloese_Inland_Ermaessigt() { return $this->steuer_erloese_inland_ermaessigt; }
  function SetSteuer_Aufwendung_Inland_Ermaessigt($value) { $this->steuer_aufwendung_inland_ermaessigt=$value; }
  function GetSteuer_Aufwendung_Inland_Ermaessigt() { return $this->steuer_aufwendung_inland_ermaessigt; }
  function SetSteuer_Erloese_Inland_Steuerfrei($value) { $this->steuer_erloese_inland_steuerfrei=$value; }
  function GetSteuer_Erloese_Inland_Steuerfrei() { return $this->steuer_erloese_inland_steuerfrei; }
  function SetSteuer_Aufwendung_Inland_Steuerfrei($value) { $this->steuer_aufwendung_inland_steuerfrei=$value; }
  function GetSteuer_Aufwendung_Inland_Steuerfrei() { return $this->steuer_aufwendung_inland_steuerfrei; }
  function SetSteuer_Erloese_Inland_Innergemeinschaftlich($value) { $this->steuer_erloese_inland_innergemeinschaftlich=$value; }
  function GetSteuer_Erloese_Inland_Innergemeinschaftlich() { return $this->steuer_erloese_inland_innergemeinschaftlich; }
  function SetSteuer_Aufwendung_Inland_Innergemeinschaftlich($value) { $this->steuer_aufwendung_inland_innergemeinschaftlich=$value; }
  function GetSteuer_Aufwendung_Inland_Innergemeinschaftlich() { return $this->steuer_aufwendung_inland_innergemeinschaftlich; }
  function SetSteuer_Erloese_Inland_Eunormal($value) { $this->steuer_erloese_inland_eunormal=$value; }
  function GetSteuer_Erloese_Inland_Eunormal() { return $this->steuer_erloese_inland_eunormal; }
  function SetSteuer_Erloese_Inland_Nichtsteuerbar($value) { $this->steuer_erloese_inland_nichtsteuerbar=$value; }
  function GetSteuer_Erloese_Inland_Nichtsteuerbar() { return $this->steuer_erloese_inland_nichtsteuerbar; }
  function SetSteuer_Erloese_Inland_Euermaessigt($value) { $this->steuer_erloese_inland_euermaessigt=$value; }
  function GetSteuer_Erloese_Inland_Euermaessigt() { return $this->steuer_erloese_inland_euermaessigt; }
  function SetSteuer_Aufwendung_Inland_Nichtsteuerbar($value) { $this->steuer_aufwendung_inland_nichtsteuerbar=$value; }
  function GetSteuer_Aufwendung_Inland_Nichtsteuerbar() { return $this->steuer_aufwendung_inland_nichtsteuerbar; }
  function SetSteuer_Aufwendung_Inland_Eunormal($value) { $this->steuer_aufwendung_inland_eunormal=$value; }
  function GetSteuer_Aufwendung_Inland_Eunormal() { return $this->steuer_aufwendung_inland_eunormal; }
  function SetSteuer_Aufwendung_Inland_Euermaessigt($value) { $this->steuer_aufwendung_inland_euermaessigt=$value; }
  function GetSteuer_Aufwendung_Inland_Euermaessigt() { return $this->steuer_aufwendung_inland_euermaessigt; }
  function SetSteuer_Erloese_Inland_Export($value) { $this->steuer_erloese_inland_export=$value; }
  function GetSteuer_Erloese_Inland_Export() { return $this->steuer_erloese_inland_export; }
  function SetSteuer_Aufwendung_Inland_Import($value) { $this->steuer_aufwendung_inland_import=$value; }
  function GetSteuer_Aufwendung_Inland_Import() { return $this->steuer_aufwendung_inland_import; }
  function SetSteuertext_Innergemeinschaftlich($value) { $this->steuertext_innergemeinschaftlich=$value; }
  function GetSteuertext_Innergemeinschaftlich() { return $this->steuertext_innergemeinschaftlich; }
  function SetSteuertext_Export($value) { $this->steuertext_export=$value; }
  function GetSteuertext_Export() { return $this->steuertext_export; }

}

?>