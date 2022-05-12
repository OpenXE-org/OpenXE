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

class ObjGenAbrechnungsartikel
{

  private  $id;
  private  $sort;
  private  $artikel;
  private  $bezeichnung;
  private  $nummer;
  private  $menge;
  private  $preis;
  private  $steuerklasse;
  private  $rabatt;
  private  $abgerechnet;
  private  $startdatum;
  private  $lieferdatum;
  private  $abgerechnetbis;
  private  $wiederholend;
  private  $zahlzyklus;
  private  $abgrechnetam;
  private  $rechnung;
  private  $projekt;
  private  $adresse;
  private  $status;
  private  $bemerkung;
  private  $logdatei;
  private  $beschreibung;
  private  $dokument;
  private  $preisart;
  private  $enddatum;
  private  $angelegtvon;
  private  $angelegtam;
  private  $experte;
  private  $waehrung;
  private  $beschreibungersetzten;
  private  $gruppe;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM abrechnungsartikel WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->sort=$result['sort'];
    $this->artikel=$result['artikel'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->nummer=$result['nummer'];
    $this->menge=$result['menge'];
    $this->preis=$result['preis'];
    $this->steuerklasse=$result['steuerklasse'];
    $this->rabatt=$result['rabatt'];
    $this->abgerechnet=$result['abgerechnet'];
    $this->startdatum=$result['startdatum'];
    $this->lieferdatum=$result['lieferdatum'];
    $this->abgerechnetbis=$result['abgerechnetbis'];
    $this->wiederholend=$result['wiederholend'];
    $this->zahlzyklus=$result['zahlzyklus'];
    $this->abgrechnetam=$result['abgrechnetam'];
    $this->rechnung=$result['rechnung'];
    $this->projekt=$result['projekt'];
    $this->adresse=$result['adresse'];
    $this->status=$result['status'];
    $this->bemerkung=$result['bemerkung'];
    $this->logdatei=$result['logdatei'];
    $this->beschreibung=$result['beschreibung'];
    $this->dokument=$result['dokument'];
    $this->preisart=$result['preisart'];
    $this->enddatum=$result['enddatum'];
    $this->angelegtvon=$result['angelegtvon'];
    $this->angelegtam=$result['angelegtam'];
    $this->experte=$result['experte'];
    $this->waehrung=$result['waehrung'];
    $this->beschreibungersetzten=$result['beschreibungersetzten'];
    $this->gruppe=$result['gruppe'];
  }

  public function Create()
  {
    $sql = "INSERT INTO abrechnungsartikel (id,sort,artikel,bezeichnung,nummer,menge,preis,steuerklasse,rabatt,abgerechnet,startdatum,lieferdatum,abgerechnetbis,wiederholend,zahlzyklus,abgrechnetam,rechnung,projekt,adresse,status,bemerkung,logdatei,beschreibung,dokument,preisart,enddatum,angelegtvon,angelegtam,experte,waehrung,beschreibungersetzten,gruppe)
      VALUES('','{$this->sort}','{$this->artikel}','{$this->bezeichnung}','{$this->nummer}','{$this->menge}','{$this->preis}','{$this->steuerklasse}','{$this->rabatt}','{$this->abgerechnet}','{$this->startdatum}','{$this->lieferdatum}','{$this->abgerechnetbis}','{$this->wiederholend}','{$this->zahlzyklus}','{$this->abgrechnetam}','{$this->rechnung}','{$this->projekt}','{$this->adresse}','{$this->status}','{$this->bemerkung}','{$this->logdatei}','{$this->beschreibung}','{$this->dokument}','{$this->preisart}','{$this->enddatum}','{$this->angelegtvon}','{$this->angelegtam}','{$this->experte}','{$this->waehrung}','{$this->beschreibungersetzten}','{$this->gruppe}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE abrechnungsartikel SET
      sort='{$this->sort}',
      artikel='{$this->artikel}',
      bezeichnung='{$this->bezeichnung}',
      nummer='{$this->nummer}',
      menge='{$this->menge}',
      preis='{$this->preis}',
      steuerklasse='{$this->steuerklasse}',
      rabatt='{$this->rabatt}',
      abgerechnet='{$this->abgerechnet}',
      startdatum='{$this->startdatum}',
      lieferdatum='{$this->lieferdatum}',
      abgerechnetbis='{$this->abgerechnetbis}',
      wiederholend='{$this->wiederholend}',
      zahlzyklus='{$this->zahlzyklus}',
      abgrechnetam='{$this->abgrechnetam}',
      rechnung='{$this->rechnung}',
      projekt='{$this->projekt}',
      adresse='{$this->adresse}',
      status='{$this->status}',
      bemerkung='{$this->bemerkung}',
      logdatei='{$this->logdatei}',
      beschreibung='{$this->beschreibung}',
      dokument='{$this->dokument}',
      preisart='{$this->preisart}',
      enddatum='{$this->enddatum}',
      angelegtvon='{$this->angelegtvon}',
      angelegtam='{$this->angelegtam}',
      experte='{$this->experte}',
      waehrung='{$this->waehrung}',
      beschreibungersetzten='{$this->beschreibungersetzten}',
      gruppe='{$this->gruppe}'
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

    $sql = "DELETE FROM abrechnungsartikel WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->sort="";
    $this->artikel="";
    $this->bezeichnung="";
    $this->nummer="";
    $this->menge="";
    $this->preis="";
    $this->steuerklasse="";
    $this->rabatt="";
    $this->abgerechnet="";
    $this->startdatum="";
    $this->lieferdatum="";
    $this->abgerechnetbis="";
    $this->wiederholend="";
    $this->zahlzyklus="";
    $this->abgrechnetam="";
    $this->rechnung="";
    $this->projekt="";
    $this->adresse="";
    $this->status="";
    $this->bemerkung="";
    $this->logdatei="";
    $this->beschreibung="";
    $this->dokument="";
    $this->preisart="";
    $this->enddatum="";
    $this->angelegtvon="";
    $this->angelegtam="";
    $this->experte="";
    $this->waehrung="";
    $this->beschreibungersetzten="";
    $this->gruppe="";
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
  function SetSort($value) { $this->sort=$value; }
  function GetSort() { return $this->sort; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetNummer($value) { $this->nummer=$value; }
  function GetNummer() { return $this->nummer; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetPreis($value) { $this->preis=$value; }
  function GetPreis() { return $this->preis; }
  function SetSteuerklasse($value) { $this->steuerklasse=$value; }
  function GetSteuerklasse() { return $this->steuerklasse; }
  function SetRabatt($value) { $this->rabatt=$value; }
  function GetRabatt() { return $this->rabatt; }
  function SetAbgerechnet($value) { $this->abgerechnet=$value; }
  function GetAbgerechnet() { return $this->abgerechnet; }
  function SetStartdatum($value) { $this->startdatum=$value; }
  function GetStartdatum() { return $this->startdatum; }
  function SetLieferdatum($value) { $this->lieferdatum=$value; }
  function GetLieferdatum() { return $this->lieferdatum; }
  function SetAbgerechnetbis($value) { $this->abgerechnetbis=$value; }
  function GetAbgerechnetbis() { return $this->abgerechnetbis; }
  function SetWiederholend($value) { $this->wiederholend=$value; }
  function GetWiederholend() { return $this->wiederholend; }
  function SetZahlzyklus($value) { $this->zahlzyklus=$value; }
  function GetZahlzyklus() { return $this->zahlzyklus; }
  function SetAbgrechnetam($value) { $this->abgrechnetam=$value; }
  function GetAbgrechnetam() { return $this->abgrechnetam; }
  function SetRechnung($value) { $this->rechnung=$value; }
  function GetRechnung() { return $this->rechnung; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetDokument($value) { $this->dokument=$value; }
  function GetDokument() { return $this->dokument; }
  function SetPreisart($value) { $this->preisart=$value; }
  function GetPreisart() { return $this->preisart; }
  function SetEnddatum($value) { $this->enddatum=$value; }
  function GetEnddatum() { return $this->enddatum; }
  function SetAngelegtvon($value) { $this->angelegtvon=$value; }
  function GetAngelegtvon() { return $this->angelegtvon; }
  function SetAngelegtam($value) { $this->angelegtam=$value; }
  function GetAngelegtam() { return $this->angelegtam; }
  function SetExperte($value) { $this->experte=$value; }
  function GetExperte() { return $this->experte; }
  function SetWaehrung($value) { $this->waehrung=$value; }
  function GetWaehrung() { return $this->waehrung; }
  function SetBeschreibungersetzten($value) { $this->beschreibungersetzten=$value; }
  function GetBeschreibungersetzten() { return $this->beschreibungersetzten; }
  function SetGruppe($value) { $this->gruppe=$value; }
  function GetGruppe() { return $this->gruppe; }

}

?>