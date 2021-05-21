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

class ObjGenZeiterfassung
{

  private  $id;
  private  $art;
  private  $adresse;
  private  $von;
  private  $bis;
  private  $aufgabe;
  private  $beschreibung;
  private  $arbeitspaket;
  private  $buchungsart;
  private  $kostenstelle;
  private  $projekt;
  private  $abgerechnet;
  private  $logdatei;
  private  $adresse_abrechnung;
  private  $abrechnen;
  private  $ist_abgerechnet;
  private  $gebucht_von_user;
  private  $ort;
  private  $abrechnung_dokument;
  private  $dokumentid;
  private  $status;
  private  $verrechnungsart;
  private  $arbeitsnachweis;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM zeiterfassung WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->art=$result[art];
    $this->adresse=$result[adresse];
    $this->von=$result[von];
    $this->bis=$result[bis];
    $this->aufgabe=$result[aufgabe];
    $this->beschreibung=$result[beschreibung];
    $this->arbeitspaket=$result[arbeitspaket];
    $this->buchungsart=$result[buchungsart];
    $this->kostenstelle=$result[kostenstelle];
    $this->projekt=$result[projekt];
    $this->abgerechnet=$result[abgerechnet];
    $this->logdatei=$result[logdatei];
    $this->adresse_abrechnung=$result[adresse_abrechnung];
    $this->abrechnen=$result[abrechnen];
    $this->ist_abgerechnet=$result[ist_abgerechnet];
    $this->gebucht_von_user=$result[gebucht_von_user];
    $this->ort=$result[ort];
    $this->abrechnung_dokument=$result[abrechnung_dokument];
    $this->dokumentid=$result[dokumentid];
    $this->status=$result[status];
    $this->verrechnungsart=$result[verrechnungsart];
    $this->arbeitsnachweis=$result[arbeitsnachweis];
  }

  public function Create()
  {
    $sql = "INSERT INTO zeiterfassung (id,art,adresse,von,bis,aufgabe,beschreibung,arbeitspaket,buchungsart,kostenstelle,projekt,abgerechnet,logdatei,adresse_abrechnung,abrechnen,ist_abgerechnet,gebucht_von_user,ort,abrechnung_dokument,dokumentid,status,verrechnungsart,arbeitsnachweis)
      VALUES('','{$this->art}','{$this->adresse}','{$this->von}','{$this->bis}','{$this->aufgabe}','{$this->beschreibung}','{$this->arbeitspaket}','{$this->buchungsart}','{$this->kostenstelle}','{$this->projekt}','{$this->abgerechnet}','{$this->logdatei}','{$this->adresse_abrechnung}','{$this->abrechnen}','{$this->ist_abgerechnet}','{$this->gebucht_von_user}','{$this->ort}','{$this->abrechnung_dokument}','{$this->dokumentid}','{$this->status}','{$this->verrechnungsart}','{$this->arbeitsnachweis}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE zeiterfassung SET
      art='{$this->art}',
      adresse='{$this->adresse}',
      von='{$this->von}',
      bis='{$this->bis}',
      aufgabe='{$this->aufgabe}',
      beschreibung='{$this->beschreibung}',
      arbeitspaket='{$this->arbeitspaket}',
      buchungsart='{$this->buchungsart}',
      kostenstelle='{$this->kostenstelle}',
      projekt='{$this->projekt}',
      abgerechnet='{$this->abgerechnet}',
      logdatei='{$this->logdatei}',
      adresse_abrechnung='{$this->adresse_abrechnung}',
      abrechnen='{$this->abrechnen}',
      ist_abgerechnet='{$this->ist_abgerechnet}',
      gebucht_von_user='{$this->gebucht_von_user}',
      ort='{$this->ort}',
      abrechnung_dokument='{$this->abrechnung_dokument}',
      dokumentid='{$this->dokumentid}',
      status='{$this->status}',
      verrechnungsart='{$this->verrechnungsart}',
      arbeitsnachweis='{$this->arbeitsnachweis}'
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

    $sql = "DELETE FROM zeiterfassung WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->art="";
    $this->adresse="";
    $this->von="";
    $this->bis="";
    $this->aufgabe="";
    $this->beschreibung="";
    $this->arbeitspaket="";
    $this->buchungsart="";
    $this->kostenstelle="";
    $this->projekt="";
    $this->abgerechnet="";
    $this->logdatei="";
    $this->adresse_abrechnung="";
    $this->abrechnen="";
    $this->ist_abgerechnet="";
    $this->gebucht_von_user="";
    $this->ort="";
    $this->abrechnung_dokument="";
    $this->dokumentid="";
    $this->status="";
    $this->verrechnungsart="";
    $this->arbeitsnachweis="";
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
  function SetArt($value) { $this->art=$value; }
  function GetArt() { return $this->art; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetVon($value) { $this->von=$value; }
  function GetVon() { return $this->von; }
  function SetBis($value) { $this->bis=$value; }
  function GetBis() { return $this->bis; }
  function SetAufgabe($value) { $this->aufgabe=$value; }
  function GetAufgabe() { return $this->aufgabe; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetArbeitspaket($value) { $this->arbeitspaket=$value; }
  function GetArbeitspaket() { return $this->arbeitspaket; }
  function SetBuchungsart($value) { $this->buchungsart=$value; }
  function GetBuchungsart() { return $this->buchungsart; }
  function SetKostenstelle($value) { $this->kostenstelle=$value; }
  function GetKostenstelle() { return $this->kostenstelle; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetAbgerechnet($value) { $this->abgerechnet=$value; }
  function GetAbgerechnet() { return $this->abgerechnet; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetAdresse_Abrechnung($value) { $this->adresse_abrechnung=$value; }
  function GetAdresse_Abrechnung() { return $this->adresse_abrechnung; }
  function SetAbrechnen($value) { $this->abrechnen=$value; }
  function GetAbrechnen() { return $this->abrechnen; }
  function SetIst_Abgerechnet($value) { $this->ist_abgerechnet=$value; }
  function GetIst_Abgerechnet() { return $this->ist_abgerechnet; }
  function SetGebucht_Von_User($value) { $this->gebucht_von_user=$value; }
  function GetGebucht_Von_User() { return $this->gebucht_von_user; }
  function SetOrt($value) { $this->ort=$value; }
  function GetOrt() { return $this->ort; }
  function SetAbrechnung_Dokument($value) { $this->abrechnung_dokument=$value; }
  function GetAbrechnung_Dokument() { return $this->abrechnung_dokument; }
  function SetDokumentid($value) { $this->dokumentid=$value; }
  function GetDokumentid() { return $this->dokumentid; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetVerrechnungsart($value) { $this->verrechnungsart=$value; }
  function GetVerrechnungsart() { return $this->verrechnungsart; }
  function SetArbeitsnachweis($value) { $this->arbeitsnachweis=$value; }
  function GetArbeitsnachweis() { return $this->arbeitsnachweis; }

}

?>