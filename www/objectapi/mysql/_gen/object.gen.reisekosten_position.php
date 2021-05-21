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

class ObjGenReisekosten_Position
{

  private  $id;
  private  $reisekosten;
  private  $reisekostenart;
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
  private  $bezahlt_wie;
  private  $uststeuersatz;
  private  $keineust;
  private  $betrag;
  private  $abrechnen;
  private  $abgerechnet;
  private  $abgerechnet_objekt;
  private  $abgerechnet_parameter;
  private  $exportiert;
  private  $exportiert_am;
  private  $logdatei;
  private  $mitarbeiter;
  private  $teilprojekt;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM reisekosten_position WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->reisekosten=$result['reisekosten'];
    $this->reisekostenart=$result['reisekostenart'];
    $this->artikel=$result['artikel'];
    $this->projekt=$result['projekt'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->beschreibung=$result['beschreibung'];
    $this->ort=$result['ort'];
    $this->internerkommentar=$result['internerkommentar'];
    $this->nummer=$result['nummer'];
    $this->verrechnungsart=$result['verrechnungsart'];
    $this->menge=$result['menge'];
    $this->arbeitspaket=$result['arbeitspaket'];
    $this->datum=$result['datum'];
    $this->von=$result['von'];
    $this->bis=$result['bis'];
    $this->sort=$result['sort'];
    $this->status=$result['status'];
    $this->bemerkung=$result['bemerkung'];
    $this->bezahlt_wie=$result['bezahlt_wie'];
    $this->uststeuersatz=$result['uststeuersatz'];
    $this->keineust=$result['keineust'];
    $this->betrag=$result['betrag'];
    $this->abrechnen=$result['abrechnen'];
    $this->abgerechnet=$result['abgerechnet'];
    $this->abgerechnet_objekt=$result['abgerechnet_objekt'];
    $this->abgerechnet_parameter=$result['abgerechnet_parameter'];
    $this->exportiert=$result['exportiert'];
    $this->exportiert_am=$result['exportiert_am'];
    $this->logdatei=$result['logdatei'];
    $this->mitarbeiter=$result['mitarbeiter'];
    $this->teilprojekt=$result['teilprojekt'];
  }

  public function Create()
  {
    $sql = "INSERT INTO reisekosten_position (id,reisekosten,reisekostenart,artikel,projekt,bezeichnung,beschreibung,ort,internerkommentar,nummer,verrechnungsart,menge,arbeitspaket,datum,von,bis,sort,status,bemerkung,bezahlt_wie,uststeuersatz,keineust,betrag,abrechnen,abgerechnet,abgerechnet_objekt,abgerechnet_parameter,exportiert,exportiert_am,logdatei,mitarbeiter,teilprojekt)
      VALUES('','{$this->reisekosten}','{$this->reisekostenart}','{$this->artikel}','{$this->projekt}','{$this->bezeichnung}','{$this->beschreibung}','{$this->ort}','{$this->internerkommentar}','{$this->nummer}','{$this->verrechnungsart}','{$this->menge}','{$this->arbeitspaket}','{$this->datum}','{$this->von}','{$this->bis}','{$this->sort}','{$this->status}','{$this->bemerkung}','{$this->bezahlt_wie}','{$this->uststeuersatz}','{$this->keineust}','{$this->betrag}','{$this->abrechnen}','{$this->abgerechnet}','{$this->abgerechnet_objekt}','{$this->abgerechnet_parameter}','{$this->exportiert}','{$this->exportiert_am}','{$this->logdatei}','{$this->mitarbeiter}','{$this->teilprojekt}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE reisekosten_position SET
      reisekosten='{$this->reisekosten}',
      reisekostenart='{$this->reisekostenart}',
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
      bezahlt_wie='{$this->bezahlt_wie}',
      uststeuersatz='{$this->uststeuersatz}',
      keineust='{$this->keineust}',
      betrag='{$this->betrag}',
      abrechnen='{$this->abrechnen}',
      abgerechnet='{$this->abgerechnet}',
      abgerechnet_objekt='{$this->abgerechnet_objekt}',
      abgerechnet_parameter='{$this->abgerechnet_parameter}',
      exportiert='{$this->exportiert}',
      exportiert_am='{$this->exportiert_am}',
      logdatei='{$this->logdatei}',
      mitarbeiter='{$this->mitarbeiter}',
      teilprojekt='{$this->teilprojekt}'
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

    $sql = "DELETE FROM reisekosten_position WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->reisekosten="";
    $this->reisekostenart="";
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
    $this->bezahlt_wie="";
    $this->uststeuersatz="";
    $this->keineust="";
    $this->betrag="";
    $this->abrechnen="";
    $this->abgerechnet="";
    $this->abgerechnet_objekt="";
    $this->abgerechnet_parameter="";
    $this->exportiert="";
    $this->exportiert_am="";
    $this->logdatei="";
    $this->mitarbeiter="";
    $this->teilprojekt="";
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
  function SetReisekosten($value) { $this->reisekosten=$value; }
  function GetReisekosten() { return $this->reisekosten; }
  function SetReisekostenart($value) { $this->reisekostenart=$value; }
  function GetReisekostenart() { return $this->reisekostenart; }
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
  function SetBezahlt_Wie($value) { $this->bezahlt_wie=$value; }
  function GetBezahlt_Wie() { return $this->bezahlt_wie; }
  function SetUststeuersatz($value) { $this->uststeuersatz=$value; }
  function GetUststeuersatz() { return $this->uststeuersatz; }
  function SetKeineust($value) { $this->keineust=$value; }
  function GetKeineust() { return $this->keineust; }
  function SetBetrag($value) { $this->betrag=$value; }
  function GetBetrag() { return $this->betrag; }
  function SetAbrechnen($value) { $this->abrechnen=$value; }
  function GetAbrechnen() { return $this->abrechnen; }
  function SetAbgerechnet($value) { $this->abgerechnet=$value; }
  function GetAbgerechnet() { return $this->abgerechnet; }
  function SetAbgerechnet_Objekt($value) { $this->abgerechnet_objekt=$value; }
  function GetAbgerechnet_Objekt() { return $this->abgerechnet_objekt; }
  function SetAbgerechnet_Parameter($value) { $this->abgerechnet_parameter=$value; }
  function GetAbgerechnet_Parameter() { return $this->abgerechnet_parameter; }
  function SetExportiert($value) { $this->exportiert=$value; }
  function GetExportiert() { return $this->exportiert; }
  function SetExportiert_Am($value) { $this->exportiert_am=$value; }
  function GetExportiert_Am() { return $this->exportiert_am; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetMitarbeiter($value) { $this->mitarbeiter=$value; }
  function GetMitarbeiter() { return $this->mitarbeiter; }
  function SetTeilprojekt($value) { $this->teilprojekt=$value; }
  function GetTeilprojekt() { return $this->teilprojekt; }

}

?>