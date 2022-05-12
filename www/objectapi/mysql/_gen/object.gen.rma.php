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

class ObjGenRma
{

  private  $id;
  private  $datum;
  private  $projekt;
  private  $belegnr;
  private  $bearbeiter;
  private  $freigabe;
  private  $status;
  private  $adresse;
  private  $name;
  private  $vorname;
  private  $abteilung;
  private  $unterabteilung;
  private  $strasse;
  private  $adresszusatz;
  private  $plz;
  private  $ort;
  private  $ustid;
  private  $email;
  private  $telefon;
  private  $telefax;
  private  $betreff;
  private  $kundennummer;
  private  $lieferantennummer;
  private  $zahlungsweise;
  private  $zahlungszieltage;
  private  $versandart;
  private  $bestellbestaetigung;
  private  $freitext;
  private  $zahlungszielskonto;
  private  $zahlungszieltageskonto;
  private  $bestellbestaetigungsdatum;
  private  $lieferdatum;
  private  $einkaeufer;
  private  $firma;
  private  $logdatei;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM rma WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->datum=$result[datum];
    $this->projekt=$result[projekt];
    $this->belegnr=$result[belegnr];
    $this->bearbeiter=$result[bearbeiter];
    $this->freigabe=$result[freigabe];
    $this->status=$result[status];
    $this->adresse=$result[adresse];
    $this->name=$result[name];
    $this->vorname=$result[vorname];
    $this->abteilung=$result[abteilung];
    $this->unterabteilung=$result[unterabteilung];
    $this->strasse=$result[strasse];
    $this->adresszusatz=$result[adresszusatz];
    $this->plz=$result[plz];
    $this->ort=$result[ort];
    $this->ustid=$result[ustid];
    $this->email=$result[email];
    $this->telefon=$result[telefon];
    $this->telefax=$result[telefax];
    $this->betreff=$result[betreff];
    $this->kundennummer=$result[kundennummer];
    $this->lieferantennummer=$result[lieferantennummer];
    $this->zahlungsweise=$result[zahlungsweise];
    $this->zahlungszieltage=$result[zahlungszieltage];
    $this->versandart=$result[versandart];
    $this->bestellbestaetigung=$result[bestellbestaetigung];
    $this->freitext=$result[freitext];
    $this->zahlungszielskonto=$result[zahlungszielskonto];
    $this->zahlungszieltageskonto=$result[zahlungszieltageskonto];
    $this->bestellbestaetigungsdatum=$result[bestellbestaetigungsdatum];
    $this->lieferdatum=$result[lieferdatum];
    $this->einkaeufer=$result[einkaeufer];
    $this->firma=$result[firma];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO rma (id,datum,projekt,belegnr,bearbeiter,freigabe,status,adresse,name,vorname,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,ustid,email,telefon,telefax,betreff,kundennummer,lieferantennummer,zahlungsweise,zahlungszieltage,versandart,bestellbestaetigung,freitext,zahlungszielskonto,zahlungszieltageskonto,bestellbestaetigungsdatum,lieferdatum,einkaeufer,firma,logdatei)
      VALUES('','{$this->datum}','{$this->projekt}','{$this->belegnr}','{$this->bearbeiter}','{$this->freigabe}','{$this->status}','{$this->adresse}','{$this->name}','{$this->vorname}','{$this->abteilung}','{$this->unterabteilung}','{$this->strasse}','{$this->adresszusatz}','{$this->plz}','{$this->ort}','{$this->ustid}','{$this->email}','{$this->telefon}','{$this->telefax}','{$this->betreff}','{$this->kundennummer}','{$this->lieferantennummer}','{$this->zahlungsweise}','{$this->zahlungszieltage}','{$this->versandart}','{$this->bestellbestaetigung}','{$this->freitext}','{$this->zahlungszielskonto}','{$this->zahlungszieltageskonto}','{$this->bestellbestaetigungsdatum}','{$this->lieferdatum}','{$this->einkaeufer}','{$this->firma}','{$this->logdatei}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE rma SET
      datum='{$this->datum}',
      projekt='{$this->projekt}',
      belegnr='{$this->belegnr}',
      bearbeiter='{$this->bearbeiter}',
      freigabe='{$this->freigabe}',
      status='{$this->status}',
      adresse='{$this->adresse}',
      name='{$this->name}',
      vorname='{$this->vorname}',
      abteilung='{$this->abteilung}',
      unterabteilung='{$this->unterabteilung}',
      strasse='{$this->strasse}',
      adresszusatz='{$this->adresszusatz}',
      plz='{$this->plz}',
      ort='{$this->ort}',
      ustid='{$this->ustid}',
      email='{$this->email}',
      telefon='{$this->telefon}',
      telefax='{$this->telefax}',
      betreff='{$this->betreff}',
      kundennummer='{$this->kundennummer}',
      lieferantennummer='{$this->lieferantennummer}',
      zahlungsweise='{$this->zahlungsweise}',
      zahlungszieltage='{$this->zahlungszieltage}',
      versandart='{$this->versandart}',
      bestellbestaetigung='{$this->bestellbestaetigung}',
      freitext='{$this->freitext}',
      zahlungszielskonto='{$this->zahlungszielskonto}',
      zahlungszieltageskonto='{$this->zahlungszieltageskonto}',
      bestellbestaetigungsdatum='{$this->bestellbestaetigungsdatum}',
      lieferdatum='{$this->lieferdatum}',
      einkaeufer='{$this->einkaeufer}',
      firma='{$this->firma}',
      logdatei='{$this->logdatei}'
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

    $sql = "DELETE FROM rma WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->datum="";
    $this->projekt="";
    $this->belegnr="";
    $this->bearbeiter="";
    $this->freigabe="";
    $this->status="";
    $this->adresse="";
    $this->name="";
    $this->vorname="";
    $this->abteilung="";
    $this->unterabteilung="";
    $this->strasse="";
    $this->adresszusatz="";
    $this->plz="";
    $this->ort="";
    $this->ustid="";
    $this->email="";
    $this->telefon="";
    $this->telefax="";
    $this->betreff="";
    $this->kundennummer="";
    $this->lieferantennummer="";
    $this->zahlungsweise="";
    $this->zahlungszieltage="";
    $this->versandart="";
    $this->bestellbestaetigung="";
    $this->freitext="";
    $this->zahlungszielskonto="";
    $this->zahlungszieltageskonto="";
    $this->bestellbestaetigungsdatum="";
    $this->lieferdatum="";
    $this->einkaeufer="";
    $this->firma="";
    $this->logdatei="";
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
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetBelegnr($value) { $this->belegnr=$value; }
  function GetBelegnr() { return $this->belegnr; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetFreigabe($value) { $this->freigabe=$value; }
  function GetFreigabe() { return $this->freigabe; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetVorname($value) { $this->vorname=$value; }
  function GetVorname() { return $this->vorname; }
  function SetAbteilung($value) { $this->abteilung=$value; }
  function GetAbteilung() { return $this->abteilung; }
  function SetUnterabteilung($value) { $this->unterabteilung=$value; }
  function GetUnterabteilung() { return $this->unterabteilung; }
  function SetStrasse($value) { $this->strasse=$value; }
  function GetStrasse() { return $this->strasse; }
  function SetAdresszusatz($value) { $this->adresszusatz=$value; }
  function GetAdresszusatz() { return $this->adresszusatz; }
  function SetPlz($value) { $this->plz=$value; }
  function GetPlz() { return $this->plz; }
  function SetOrt($value) { $this->ort=$value; }
  function GetOrt() { return $this->ort; }
  function SetUstid($value) { $this->ustid=$value; }
  function GetUstid() { return $this->ustid; }
  function SetEmail($value) { $this->email=$value; }
  function GetEmail() { return $this->email; }
  function SetTelefon($value) { $this->telefon=$value; }
  function GetTelefon() { return $this->telefon; }
  function SetTelefax($value) { $this->telefax=$value; }
  function GetTelefax() { return $this->telefax; }
  function SetBetreff($value) { $this->betreff=$value; }
  function GetBetreff() { return $this->betreff; }
  function SetKundennummer($value) { $this->kundennummer=$value; }
  function GetKundennummer() { return $this->kundennummer; }
  function SetLieferantennummer($value) { $this->lieferantennummer=$value; }
  function GetLieferantennummer() { return $this->lieferantennummer; }
  function SetZahlungsweise($value) { $this->zahlungsweise=$value; }
  function GetZahlungsweise() { return $this->zahlungsweise; }
  function SetZahlungszieltage($value) { $this->zahlungszieltage=$value; }
  function GetZahlungszieltage() { return $this->zahlungszieltage; }
  function SetVersandart($value) { $this->versandart=$value; }
  function GetVersandart() { return $this->versandart; }
  function SetBestellbestaetigung($value) { $this->bestellbestaetigung=$value; }
  function GetBestellbestaetigung() { return $this->bestellbestaetigung; }
  function SetFreitext($value) { $this->freitext=$value; }
  function GetFreitext() { return $this->freitext; }
  function SetZahlungszielskonto($value) { $this->zahlungszielskonto=$value; }
  function GetZahlungszielskonto() { return $this->zahlungszielskonto; }
  function SetZahlungszieltageskonto($value) { $this->zahlungszieltageskonto=$value; }
  function GetZahlungszieltageskonto() { return $this->zahlungszieltageskonto; }
  function SetBestellbestaetigungsdatum($value) { $this->bestellbestaetigungsdatum=$value; }
  function GetBestellbestaetigungsdatum() { return $this->bestellbestaetigungsdatum; }
  function SetLieferdatum($value) { $this->lieferdatum=$value; }
  function GetLieferdatum() { return $this->lieferdatum; }
  function SetEinkaeufer($value) { $this->einkaeufer=$value; }
  function GetEinkaeufer() { return $this->einkaeufer; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>