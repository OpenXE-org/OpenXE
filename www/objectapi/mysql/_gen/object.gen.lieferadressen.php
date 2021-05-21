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

class ObjGenLieferadressen
{

  private  $id;
  private  $typ;
  private  $sprache;
  private  $name;
  private  $abteilung;
  private  $unterabteilung;
  private  $land;
  private  $strasse;
  private  $ort;
  private  $plz;
  private  $telefon;
  private  $telefax;
  private  $email;
  private  $sonstiges;
  private  $adresszusatz;
  private  $steuer;
  private  $adresse;
  private  $logdatei;
  private  $ansprechpartner;
  private  $standardlieferadresse;
  private  $gln;
  private  $ustid;
  private  $ust_befreit;
  private  $lieferbedingung;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM lieferadressen WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->typ=$result['typ'];
    $this->sprache=$result['sprache'];
    $this->name=$result['name'];
    $this->abteilung=$result['abteilung'];
    $this->unterabteilung=$result['unterabteilung'];
    $this->land=$result['land'];
    $this->strasse=$result['strasse'];
    $this->ort=$result['ort'];
    $this->plz=$result['plz'];
    $this->telefon=$result['telefon'];
    $this->telefax=$result['telefax'];
    $this->email=$result['email'];
    $this->sonstiges=$result['sonstiges'];
    $this->adresszusatz=$result['adresszusatz'];
    $this->steuer=$result['steuer'];
    $this->adresse=$result['adresse'];
    $this->logdatei=$result['logdatei'];
    $this->ansprechpartner=$result['ansprechpartner'];
    $this->standardlieferadresse=$result['standardlieferadresse'];
    $this->gln=$result['gln'];
    $this->ustid=$result['ustid'];
    $this->ust_befreit=$result['ust_befreit'];
    $this->lieferbedingung=$result['lieferbedingung'];
  }

  public function Create()
  {
    $sql = "INSERT INTO lieferadressen (id,typ,sprache,name,abteilung,unterabteilung,land,strasse,ort,plz,telefon,telefax,email,sonstiges,adresszusatz,steuer,adresse,logdatei,ansprechpartner,standardlieferadresse,gln,ustid,ust_befreit,lieferbedingung)
      VALUES('','{$this->typ}','{$this->sprache}','{$this->name}','{$this->abteilung}','{$this->unterabteilung}','{$this->land}','{$this->strasse}','{$this->ort}','{$this->plz}','{$this->telefon}','{$this->telefax}','{$this->email}','{$this->sonstiges}','{$this->adresszusatz}','{$this->steuer}','{$this->adresse}','{$this->logdatei}','{$this->ansprechpartner}','{$this->standardlieferadresse}','{$this->gln}','{$this->ustid}','{$this->ust_befreit}','{$this->lieferbedingung}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE lieferadressen SET
      typ='{$this->typ}',
      sprache='{$this->sprache}',
      name='{$this->name}',
      abteilung='{$this->abteilung}',
      unterabteilung='{$this->unterabteilung}',
      land='{$this->land}',
      strasse='{$this->strasse}',
      ort='{$this->ort}',
      plz='{$this->plz}',
      telefon='{$this->telefon}',
      telefax='{$this->telefax}',
      email='{$this->email}',
      sonstiges='{$this->sonstiges}',
      adresszusatz='{$this->adresszusatz}',
      steuer='{$this->steuer}',
      adresse='{$this->adresse}',
      logdatei='{$this->logdatei}',
      ansprechpartner='{$this->ansprechpartner}',
      standardlieferadresse='{$this->standardlieferadresse}',
      gln='{$this->gln}',
      ustid='{$this->ustid}',
      ust_befreit='{$this->ust_befreit}',
      lieferbedingung='{$this->lieferbedingung}'
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

    $sql = "DELETE FROM lieferadressen WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->typ="";
    $this->sprache="";
    $this->name="";
    $this->abteilung="";
    $this->unterabteilung="";
    $this->land="";
    $this->strasse="";
    $this->ort="";
    $this->plz="";
    $this->telefon="";
    $this->telefax="";
    $this->email="";
    $this->sonstiges="";
    $this->adresszusatz="";
    $this->steuer="";
    $this->adresse="";
    $this->logdatei="";
    $this->ansprechpartner="";
    $this->standardlieferadresse="";
    $this->gln="";
    $this->ustid="";
    $this->ust_befreit="";
    $this->lieferbedingung="";
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
  function SetTyp($value) { $this->typ=$value; }
  function GetTyp() { return $this->typ; }
  function SetSprache($value) { $this->sprache=$value; }
  function GetSprache() { return $this->sprache; }
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetAbteilung($value) { $this->abteilung=$value; }
  function GetAbteilung() { return $this->abteilung; }
  function SetUnterabteilung($value) { $this->unterabteilung=$value; }
  function GetUnterabteilung() { return $this->unterabteilung; }
  function SetLand($value) { $this->land=$value; }
  function GetLand() { return $this->land; }
  function SetStrasse($value) { $this->strasse=$value; }
  function GetStrasse() { return $this->strasse; }
  function SetOrt($value) { $this->ort=$value; }
  function GetOrt() { return $this->ort; }
  function SetPlz($value) { $this->plz=$value; }
  function GetPlz() { return $this->plz; }
  function SetTelefon($value) { $this->telefon=$value; }
  function GetTelefon() { return $this->telefon; }
  function SetTelefax($value) { $this->telefax=$value; }
  function GetTelefax() { return $this->telefax; }
  function SetEmail($value) { $this->email=$value; }
  function GetEmail() { return $this->email; }
  function SetSonstiges($value) { $this->sonstiges=$value; }
  function GetSonstiges() { return $this->sonstiges; }
  function SetAdresszusatz($value) { $this->adresszusatz=$value; }
  function GetAdresszusatz() { return $this->adresszusatz; }
  function SetSteuer($value) { $this->steuer=$value; }
  function GetSteuer() { return $this->steuer; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  function GetAnsprechpartner() { return $this->ansprechpartner; }
  function SetStandardlieferadresse($value) { $this->standardlieferadresse=$value; }
  function GetStandardlieferadresse() { return $this->standardlieferadresse; }
  function SetGln($value) { $this->gln=$value; }
  function GetGln() { return $this->gln; }
  function SetUstid($value) { $this->ustid=$value; }
  function GetUstid() { return $this->ustid; }
  function SetUst_Befreit($value) { $this->ust_befreit=$value; }
  function GetUst_Befreit() { return $this->ust_befreit; }
  function SetLieferbedingung($value) { $this->lieferbedingung=$value; }
  function GetLieferbedingung() { return $this->lieferbedingung; }

}

?>