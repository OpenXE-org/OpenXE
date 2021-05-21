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

class ObjGenService
{

  private  $id;
  private  $adresse;
  private  $zuweisen;
  private  $ansprechpartner;
  private  $nummer;
  private  $prio;
  private  $eingangart;
  private  $datum;
  private  $erledigenbis;
  private  $betreff;
  private  $beschreibung_html;
  private  $internebemerkung;
  private  $antwortankunden;
  private  $angelegtvonuser;
  private  $status;
  private  $artikel;
  private  $seriennummer;
  private  $antwortpermail;
  private  $antwortankundenempfaenger;
  private  $antwortankundenkopie;
  private  $antwortankundenblindkopie;
  private  $antwortankundenbetreff;
  private  $bezahlte_zusatzleistung;
  private  $freigabe;
  private  $freigabe_datum;
  private  $freigabe_bearbeiter;
  private  $dauer_geplant;
  private  $art;
  private  $bereich;
  private  $freifeld1;
  private  $freifeld2;
  private  $freifeld3;
  private  $freifeld4;
  private  $freifeld5;
  private  $version;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM service WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->adresse=$result['adresse'];
    $this->zuweisen=$result['zuweisen'];
    $this->ansprechpartner=$result['ansprechpartner'];
    $this->nummer=$result['nummer'];
    $this->prio=$result['prio'];
    $this->eingangart=$result['eingangart'];
    $this->datum=$result['datum'];
    $this->erledigenbis=$result['erledigenbis'];
    $this->betreff=$result['betreff'];
    $this->beschreibung_html=$result['beschreibung_html'];
    $this->internebemerkung=$result['internebemerkung'];
    $this->antwortankunden=$result['antwortankunden'];
    $this->angelegtvonuser=$result['angelegtvonuser'];
    $this->status=$result['status'];
    $this->artikel=$result['artikel'];
    $this->seriennummer=$result['seriennummer'];
    $this->antwortpermail=$result['antwortpermail'];
    $this->antwortankundenempfaenger=$result['antwortankundenempfaenger'];
    $this->antwortankundenkopie=$result['antwortankundenkopie'];
    $this->antwortankundenblindkopie=$result['antwortankundenblindkopie'];
    $this->antwortankundenbetreff=$result['antwortankundenbetreff'];
    $this->bezahlte_zusatzleistung=$result['bezahlte_zusatzleistung'];
    $this->freigabe=$result['freigabe'];
    $this->freigabe_datum=$result['freigabe_datum'];
    $this->freigabe_bearbeiter=$result['freigabe_bearbeiter'];
    $this->dauer_geplant=$result['dauer_geplant'];
    $this->art=$result['art'];
    $this->bereich=$result['bereich'];
    $this->freifeld1=$result['freifeld1'];
    $this->freifeld2=$result['freifeld2'];
    $this->freifeld3=$result['freifeld3'];
    $this->freifeld4=$result['freifeld4'];
    $this->freifeld5=$result['freifeld5'];
    $this->version=$result['version'];
  }

  public function Create()
  {
    $sql = "INSERT INTO service (id,adresse,zuweisen,ansprechpartner,nummer,prio,eingangart,datum,erledigenbis,betreff,beschreibung_html,internebemerkung,antwortankunden,angelegtvonuser,status,artikel,seriennummer,antwortpermail,antwortankundenempfaenger,antwortankundenkopie,antwortankundenblindkopie,antwortankundenbetreff,bezahlte_zusatzleistung,freigabe,freigabe_datum,freigabe_bearbeiter,dauer_geplant,art,bereich,freifeld1,freifeld2,freifeld3,freifeld4,freifeld5,version)
      VALUES('','{$this->adresse}','{$this->zuweisen}','{$this->ansprechpartner}','{$this->nummer}','{$this->prio}','{$this->eingangart}','{$this->datum}','{$this->erledigenbis}','{$this->betreff}','{$this->beschreibung_html}','{$this->internebemerkung}','{$this->antwortankunden}','{$this->angelegtvonuser}','{$this->status}','{$this->artikel}','{$this->seriennummer}','{$this->antwortpermail}','{$this->antwortankundenempfaenger}','{$this->antwortankundenkopie}','{$this->antwortankundenblindkopie}','{$this->antwortankundenbetreff}','{$this->bezahlte_zusatzleistung}','{$this->freigabe}','{$this->freigabe_datum}','{$this->freigabe_bearbeiter}','{$this->dauer_geplant}','{$this->art}','{$this->bereich}','{$this->freifeld1}','{$this->freifeld2}','{$this->freifeld3}','{$this->freifeld4}','{$this->freifeld5}','{$this->version}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE service SET
      adresse='{$this->adresse}',
      zuweisen='{$this->zuweisen}',
      ansprechpartner='{$this->ansprechpartner}',
      nummer='{$this->nummer}',
      prio='{$this->prio}',
      eingangart='{$this->eingangart}',
      datum='{$this->datum}',
      erledigenbis='{$this->erledigenbis}',
      betreff='{$this->betreff}',
      beschreibung_html='{$this->beschreibung_html}',
      internebemerkung='{$this->internebemerkung}',
      antwortankunden='{$this->antwortankunden}',
      angelegtvonuser='{$this->angelegtvonuser}',
      status='{$this->status}',
      artikel='{$this->artikel}',
      seriennummer='{$this->seriennummer}',
      antwortpermail='{$this->antwortpermail}',
      antwortankundenempfaenger='{$this->antwortankundenempfaenger}',
      antwortankundenkopie='{$this->antwortankundenkopie}',
      antwortankundenblindkopie='{$this->antwortankundenblindkopie}',
      antwortankundenbetreff='{$this->antwortankundenbetreff}',
      bezahlte_zusatzleistung='{$this->bezahlte_zusatzleistung}',
      freigabe='{$this->freigabe}',
      freigabe_datum='{$this->freigabe_datum}',
      freigabe_bearbeiter='{$this->freigabe_bearbeiter}',
      dauer_geplant='{$this->dauer_geplant}',
      art='{$this->art}',
      bereich='{$this->bereich}',
      freifeld1='{$this->freifeld1}',
      freifeld2='{$this->freifeld2}',
      freifeld3='{$this->freifeld3}',
      freifeld4='{$this->freifeld4}',
      freifeld5='{$this->freifeld5}',
      version='{$this->version}'
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

    $sql = "DELETE FROM service WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->zuweisen="";
    $this->ansprechpartner="";
    $this->nummer="";
    $this->prio="";
    $this->eingangart="";
    $this->datum="";
    $this->erledigenbis="";
    $this->betreff="";
    $this->beschreibung_html="";
    $this->internebemerkung="";
    $this->antwortankunden="";
    $this->angelegtvonuser="";
    $this->status="";
    $this->artikel="";
    $this->seriennummer="";
    $this->antwortpermail="";
    $this->antwortankundenempfaenger="";
    $this->antwortankundenkopie="";
    $this->antwortankundenblindkopie="";
    $this->antwortankundenbetreff="";
    $this->bezahlte_zusatzleistung="";
    $this->freigabe="";
    $this->freigabe_datum="";
    $this->freigabe_bearbeiter="";
    $this->dauer_geplant="";
    $this->art="";
    $this->bereich="";
    $this->freifeld1="";
    $this->freifeld2="";
    $this->freifeld3="";
    $this->freifeld4="";
    $this->freifeld5="";
    $this->version="";
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
  function SetZuweisen($value) { $this->zuweisen=$value; }
  function GetZuweisen() { return $this->zuweisen; }
  function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  function GetAnsprechpartner() { return $this->ansprechpartner; }
  function SetNummer($value) { $this->nummer=$value; }
  function GetNummer() { return $this->nummer; }
  function SetPrio($value) { $this->prio=$value; }
  function GetPrio() { return $this->prio; }
  function SetEingangart($value) { $this->eingangart=$value; }
  function GetEingangart() { return $this->eingangart; }
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetErledigenbis($value) { $this->erledigenbis=$value; }
  function GetErledigenbis() { return $this->erledigenbis; }
  function SetBetreff($value) { $this->betreff=$value; }
  function GetBetreff() { return $this->betreff; }
  function SetBeschreibung_Html($value) { $this->beschreibung_html=$value; }
  function GetBeschreibung_Html() { return $this->beschreibung_html; }
  function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  function GetInternebemerkung() { return $this->internebemerkung; }
  function SetAntwortankunden($value) { $this->antwortankunden=$value; }
  function GetAntwortankunden() { return $this->antwortankunden; }
  function SetAngelegtvonuser($value) { $this->angelegtvonuser=$value; }
  function GetAngelegtvonuser() { return $this->angelegtvonuser; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetSeriennummer($value) { $this->seriennummer=$value; }
  function GetSeriennummer() { return $this->seriennummer; }
  function SetAntwortpermail($value) { $this->antwortpermail=$value; }
  function GetAntwortpermail() { return $this->antwortpermail; }
  function SetAntwortankundenempfaenger($value) { $this->antwortankundenempfaenger=$value; }
  function GetAntwortankundenempfaenger() { return $this->antwortankundenempfaenger; }
  function SetAntwortankundenkopie($value) { $this->antwortankundenkopie=$value; }
  function GetAntwortankundenkopie() { return $this->antwortankundenkopie; }
  function SetAntwortankundenblindkopie($value) { $this->antwortankundenblindkopie=$value; }
  function GetAntwortankundenblindkopie() { return $this->antwortankundenblindkopie; }
  function SetAntwortankundenbetreff($value) { $this->antwortankundenbetreff=$value; }
  function GetAntwortankundenbetreff() { return $this->antwortankundenbetreff; }
  function SetBezahlte_Zusatzleistung($value) { $this->bezahlte_zusatzleistung=$value; }
  function GetBezahlte_Zusatzleistung() { return $this->bezahlte_zusatzleistung; }
  function SetFreigabe($value) { $this->freigabe=$value; }
  function GetFreigabe() { return $this->freigabe; }
  function SetFreigabe_Datum($value) { $this->freigabe_datum=$value; }
  function GetFreigabe_Datum() { return $this->freigabe_datum; }
  function SetFreigabe_Bearbeiter($value) { $this->freigabe_bearbeiter=$value; }
  function GetFreigabe_Bearbeiter() { return $this->freigabe_bearbeiter; }
  function SetDauer_Geplant($value) { $this->dauer_geplant=$value; }
  function GetDauer_Geplant() { return $this->dauer_geplant; }
  function SetArt($value) { $this->art=$value; }
  function GetArt() { return $this->art; }
  function SetBereich($value) { $this->bereich=$value; }
  function GetBereich() { return $this->bereich; }
  function SetFreifeld1($value) { $this->freifeld1=$value; }
  function GetFreifeld1() { return $this->freifeld1; }
  function SetFreifeld2($value) { $this->freifeld2=$value; }
  function GetFreifeld2() { return $this->freifeld2; }
  function SetFreifeld3($value) { $this->freifeld3=$value; }
  function GetFreifeld3() { return $this->freifeld3; }
  function SetFreifeld4($value) { $this->freifeld4=$value; }
  function GetFreifeld4() { return $this->freifeld4; }
  function SetFreifeld5($value) { $this->freifeld5=$value; }
  function GetFreifeld5() { return $this->freifeld5; }
  function SetVersion($value) { $this->version=$value; }
  function GetVersion() { return $this->version; }

}

?>