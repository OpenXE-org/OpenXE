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

class ObjGenArbeitsnachweis_Position
{

  private  $id;
  private  $arbeitsnachweis;
  private  $artikel;
  private  $projekt;
  private  $bezeichnung;
  private  $beschreibung;
  private  $ort;
  private  $internerkommentar;
  private  $nummer;
  private  $verrechnungsart;
  private  $menge;
  private  $arbeitspaket;
  private  $datum;
  private  $von;
  private  $bis;
  private  $sort;
  private  $status;
  private  $bemerkung;
  private  $abgerechnet;
  private  $logdatei;
  private  $adresse;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM arbeitsnachweis_position WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->arbeitsnachweis=$result[arbeitsnachweis];
    $this->artikel=$result[artikel];
    $this->projekt=$result[projekt];
    $this->bezeichnung=$result[bezeichnung];
    $this->beschreibung=$result[beschreibung];
    $this->ort=$result[ort];
    $this->internerkommentar=$result[internerkommentar];
    $this->nummer=$result[nummer];
    $this->verrechnungsart=$result[verrechnungsart];
    $this->menge=$result[menge];
    $this->arbeitspaket=$result[arbeitspaket];
    $this->datum=$result[datum];
    $this->von=$result[von];
    $this->bis=$result[bis];
    $this->sort=$result[sort];
    $this->status=$result[status];
    $this->bemerkung=$result[bemerkung];
    $this->abgerechnet=$result[abgerechnet];
    $this->logdatei=$result[logdatei];
    $this->adresse=$result[adresse];
  }

  public function Create()
  {
    $sql = "INSERT INTO arbeitsnachweis_position (id,arbeitsnachweis,artikel,projekt,bezeichnung,beschreibung,ort,internerkommentar,nummer,verrechnungsart,menge,arbeitspaket,datum,von,bis,sort,status,bemerkung,abgerechnet,logdatei,adresse)
      VALUES('','{$this->arbeitsnachweis}','{$this->artikel}','{$this->projekt}','{$this->bezeichnung}','{$this->beschreibung}','{$this->ort}','{$this->internerkommentar}','{$this->nummer}','{$this->verrechnungsart}','{$this->menge}','{$this->arbeitspaket}','{$this->datum}','{$this->von}','{$this->bis}','{$this->sort}','{$this->status}','{$this->bemerkung}','{$this->abgerechnet}','{$this->logdatei}','{$this->adresse}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE arbeitsnachweis_position SET
      arbeitsnachweis='{$this->arbeitsnachweis}',
      artikel='{$this->artikel}',
      projekt='{$this->projekt}',
      bezeichnung='{$this->bezeichnung}',
      beschreibung='{$this->beschreibung}',
      ort='{$this->ort}',
      internerkommentar='{$this->internerkommentar}',
      nummer='{$this->nummer}',
      verrechnungsart='{$this->verrechnungsart}',
      menge='{$this->menge}',
      arbeitspaket='{$this->arbeitspaket}',
      datum='{$this->datum}',
      von='{$this->von}',
      bis='{$this->bis}',
      sort='{$this->sort}',
      status='{$this->status}',
      bemerkung='{$this->bemerkung}',
      abgerechnet='{$this->abgerechnet}',
      logdatei='{$this->logdatei}',
      adresse='{$this->adresse}'
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

    $sql = "DELETE FROM arbeitsnachweis_position WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->arbeitsnachweis="";
    $this->artikel="";
    $this->projekt="";
    $this->bezeichnung="";
    $this->beschreibung="";
    $this->ort="";
    $this->internerkommentar="";
    $this->nummer="";
    $this->verrechnungsart="";
    $this->menge="";
    $this->arbeitspaket="";
    $this->datum="";
    $this->von="";
    $this->bis="";
    $this->sort="";
    $this->status="";
    $this->bemerkung="";
    $this->abgerechnet="";
    $this->logdatei="";
    $this->adresse="";
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
  function SetArbeitsnachweis($value) { $this->arbeitsnachweis=$value; }
  function GetArbeitsnachweis() { return $this->arbeitsnachweis; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetOrt($value) { $this->ort=$value; }
  function GetOrt() { return $this->ort; }
  function SetInternerkommentar($value) { $this->internerkommentar=$value; }
  function GetInternerkommentar() { return $this->internerkommentar; }
  function SetNummer($value) { $this->nummer=$value; }
  function GetNummer() { return $this->nummer; }
  function SetVerrechnungsart($value) { $this->verrechnungsart=$value; }
  function GetVerrechnungsart() { return $this->verrechnungsart; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetArbeitspaket($value) { $this->arbeitspaket=$value; }
  function GetArbeitspaket() { return $this->arbeitspaket; }
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetVon($value) { $this->von=$value; }
  function GetVon() { return $this->von; }
  function SetBis($value) { $this->bis=$value; }
  function GetBis() { return $this->bis; }
  function SetSort($value) { $this->sort=$value; }
  function GetSort() { return $this->sort; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetAbgerechnet($value) { $this->abgerechnet=$value; }
  function GetAbgerechnet() { return $this->abgerechnet; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }

}

?>