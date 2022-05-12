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

class ObjGenKasse
{

  private  $id;
  private  $datum;
  private  $auswahl;
  private  $betrag;
  private  $adresse;
  private  $grund;
  private  $projekt;
  private  $bearbeiter;
  private  $steuergruppe;
  private  $exportiert;
  private  $exportiert_datum;
  private  $firma;
  private  $logdatei;
  private  $konto;
  private  $nummer;
  private  $wert;
  private  $steuersatz;
  private  $betrag_brutto_normal;
  private  $betrag_steuer_normal;
  private  $betrag_brutto_ermaessigt;
  private  $betrag_steuer_ermaessigt;
  private  $betrag_brutto_befreit;
  private  $betrag_steuer_befreit;
  private  $tagesabschluss;
  private  $storniert;
  private  $storniert_grund;
  private  $storniert_bearbeiter;
  private  $sachkonto;
  private  $bemerkung;
  private  $belegdatum;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM kasse WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->datum=$result['datum'];
    $this->auswahl=$result['auswahl'];
    $this->betrag=$result['betrag'];
    $this->adresse=$result['adresse'];
    $this->grund=$result['grund'];
    $this->projekt=$result['projekt'];
    $this->bearbeiter=$result['bearbeiter'];
    $this->steuergruppe=$result['steuergruppe'];
    $this->exportiert=$result['exportiert'];
    $this->exportiert_datum=$result['exportiert_datum'];
    $this->firma=$result['firma'];
    $this->logdatei=$result['logdatei'];
    $this->konto=$result['konto'];
    $this->nummer=$result['nummer'];
    $this->wert=$result['wert'];
    $this->steuersatz=$result['steuersatz'];
    $this->betrag_brutto_normal=$result['betrag_brutto_normal'];
    $this->betrag_steuer_normal=$result['betrag_steuer_normal'];
    $this->betrag_brutto_ermaessigt=$result['betrag_brutto_ermaessigt'];
    $this->betrag_steuer_ermaessigt=$result['betrag_steuer_ermaessigt'];
    $this->betrag_brutto_befreit=$result['betrag_brutto_befreit'];
    $this->betrag_steuer_befreit=$result['betrag_steuer_befreit'];
    $this->tagesabschluss=$result['tagesabschluss'];
    $this->storniert=$result['storniert'];
    $this->storniert_grund=$result['storniert_grund'];
    $this->storniert_bearbeiter=$result['storniert_bearbeiter'];
    $this->sachkonto=$result['sachkonto'];
    $this->bemerkung=$result['bemerkung'];
    $this->belegdatum=$result['belegdatum'];
  }

  public function Create()
  {
    $sql = "INSERT INTO kasse (id,datum,auswahl,betrag,adresse,grund,projekt,bearbeiter,steuergruppe,exportiert,exportiert_datum,firma,logdatei,konto,nummer,wert,steuersatz,betrag_brutto_normal,betrag_steuer_normal,betrag_brutto_ermaessigt,betrag_steuer_ermaessigt,betrag_brutto_befreit,betrag_steuer_befreit,tagesabschluss,storniert,storniert_grund,storniert_bearbeiter,sachkonto,bemerkung,belegdatum)
      VALUES('','{$this->datum}','{$this->auswahl}','{$this->betrag}','{$this->adresse}','{$this->grund}','{$this->projekt}','{$this->bearbeiter}','{$this->steuergruppe}','{$this->exportiert}','{$this->exportiert_datum}','{$this->firma}','{$this->logdatei}','{$this->konto}','{$this->nummer}','{$this->wert}','{$this->steuersatz}','{$this->betrag_brutto_normal}','{$this->betrag_steuer_normal}','{$this->betrag_brutto_ermaessigt}','{$this->betrag_steuer_ermaessigt}','{$this->betrag_brutto_befreit}','{$this->betrag_steuer_befreit}','{$this->tagesabschluss}','{$this->storniert}','{$this->storniert_grund}','{$this->storniert_bearbeiter}','{$this->sachkonto}','{$this->bemerkung}','{$this->belegdatum}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE kasse SET
      datum='{$this->datum}',
      auswahl='{$this->auswahl}',
      betrag='{$this->betrag}',
      adresse='{$this->adresse}',
      grund='{$this->grund}',
      projekt='{$this->projekt}',
      bearbeiter='{$this->bearbeiter}',
      steuergruppe='{$this->steuergruppe}',
      exportiert='{$this->exportiert}',
      exportiert_datum='{$this->exportiert_datum}',
      firma='{$this->firma}',
      logdatei='{$this->logdatei}',
      konto='{$this->konto}',
      nummer='{$this->nummer}',
      wert='{$this->wert}',
      steuersatz='{$this->steuersatz}',
      betrag_brutto_normal='{$this->betrag_brutto_normal}',
      betrag_steuer_normal='{$this->betrag_steuer_normal}',
      betrag_brutto_ermaessigt='{$this->betrag_brutto_ermaessigt}',
      betrag_steuer_ermaessigt='{$this->betrag_steuer_ermaessigt}',
      betrag_brutto_befreit='{$this->betrag_brutto_befreit}',
      betrag_steuer_befreit='{$this->betrag_steuer_befreit}',
      tagesabschluss='{$this->tagesabschluss}',
      storniert='{$this->storniert}',
      storniert_grund='{$this->storniert_grund}',
      storniert_bearbeiter='{$this->storniert_bearbeiter}',
      sachkonto='{$this->sachkonto}',
      bemerkung='{$this->bemerkung}',
      belegdatum='{$this->belegdatum}'
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

    $sql = "DELETE FROM kasse WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->datum="";
    $this->auswahl="";
    $this->betrag="";
    $this->adresse="";
    $this->grund="";
    $this->projekt="";
    $this->bearbeiter="";
    $this->steuergruppe="";
    $this->exportiert="";
    $this->exportiert_datum="";
    $this->firma="";
    $this->logdatei="";
    $this->konto="";
    $this->nummer="";
    $this->wert="";
    $this->steuersatz="";
    $this->betrag_brutto_normal="";
    $this->betrag_steuer_normal="";
    $this->betrag_brutto_ermaessigt="";
    $this->betrag_steuer_ermaessigt="";
    $this->betrag_brutto_befreit="";
    $this->betrag_steuer_befreit="";
    $this->tagesabschluss="";
    $this->storniert="";
    $this->storniert_grund="";
    $this->storniert_bearbeiter="";
    $this->sachkonto="";
    $this->bemerkung="";
    $this->belegdatum="";
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
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetAuswahl($value) { $this->auswahl=$value; }
  function GetAuswahl() { return $this->auswahl; }
  function SetBetrag($value) { $this->betrag=$value; }
  function GetBetrag() { return $this->betrag; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetGrund($value) { $this->grund=$value; }
  function GetGrund() { return $this->grund; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetSteuergruppe($value) { $this->steuergruppe=$value; }
  function GetSteuergruppe() { return $this->steuergruppe; }
  function SetExportiert($value) { $this->exportiert=$value; }
  function GetExportiert() { return $this->exportiert; }
  function SetExportiert_Datum($value) { $this->exportiert_datum=$value; }
  function GetExportiert_Datum() { return $this->exportiert_datum; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetKonto($value) { $this->konto=$value; }
  function GetKonto() { return $this->konto; }
  function SetNummer($value) { $this->nummer=$value; }
  function GetNummer() { return $this->nummer; }
  function SetWert($value) { $this->wert=$value; }
  function GetWert() { return $this->wert; }
  function SetSteuersatz($value) { $this->steuersatz=$value; }
  function GetSteuersatz() { return $this->steuersatz; }
  function SetBetrag_Brutto_Normal($value) { $this->betrag_brutto_normal=$value; }
  function GetBetrag_Brutto_Normal() { return $this->betrag_brutto_normal; }
  function SetBetrag_Steuer_Normal($value) { $this->betrag_steuer_normal=$value; }
  function GetBetrag_Steuer_Normal() { return $this->betrag_steuer_normal; }
  function SetBetrag_Brutto_Ermaessigt($value) { $this->betrag_brutto_ermaessigt=$value; }
  function GetBetrag_Brutto_Ermaessigt() { return $this->betrag_brutto_ermaessigt; }
  function SetBetrag_Steuer_Ermaessigt($value) { $this->betrag_steuer_ermaessigt=$value; }
  function GetBetrag_Steuer_Ermaessigt() { return $this->betrag_steuer_ermaessigt; }
  function SetBetrag_Brutto_Befreit($value) { $this->betrag_brutto_befreit=$value; }
  function GetBetrag_Brutto_Befreit() { return $this->betrag_brutto_befreit; }
  function SetBetrag_Steuer_Befreit($value) { $this->betrag_steuer_befreit=$value; }
  function GetBetrag_Steuer_Befreit() { return $this->betrag_steuer_befreit; }
  function SetTagesabschluss($value) { $this->tagesabschluss=$value; }
  function GetTagesabschluss() { return $this->tagesabschluss; }
  function SetStorniert($value) { $this->storniert=$value; }
  function GetStorniert() { return $this->storniert; }
  function SetStorniert_Grund($value) { $this->storniert_grund=$value; }
  function GetStorniert_Grund() { return $this->storniert_grund; }
  function SetStorniert_Bearbeiter($value) { $this->storniert_bearbeiter=$value; }
  function GetStorniert_Bearbeiter() { return $this->storniert_bearbeiter; }
  function SetSachkonto($value) { $this->sachkonto=$value; }
  function GetSachkonto() { return $this->sachkonto; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetBelegdatum($value) { $this->belegdatum=$value; }
  function GetBelegdatum() { return $this->belegdatum; }

}

?>