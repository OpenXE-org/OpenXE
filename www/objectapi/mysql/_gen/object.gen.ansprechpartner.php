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

class ObjGenAnsprechpartner
{

  private  $id;
  private  $typ;
  private  $sprache;
  private  $name;
  private  $bereich;
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
  private  $mobil;
  private  $titel;
  private  $anschreiben;
  private  $ansprechpartner_land;
  private  $vorname;
  private  $geburtstag;
  private  $geburtstagkalender;
  private  $geburtstagskarte;
  private  $geloescht;
  private  $marketingsperre;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM ansprechpartner WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->typ=$result['typ'];
    $this->sprache=$result['sprache'];
    $this->name=$result['name'];
    $this->bereich=$result['bereich'];
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
    $this->mobil=$result['mobil'];
    $this->titel=$result['titel'];
    $this->anschreiben=$result['anschreiben'];
    $this->ansprechpartner_land=$result['ansprechpartner_land'];
    $this->vorname=$result['vorname'];
    $this->geburtstag=$result['geburtstag'];
    $this->geburtstagkalender=$result['geburtstagkalender'];
    $this->geburtstagskarte=$result['geburtstagskarte'];
    $this->geloescht=$result['geloescht'];
    $this->marketingsperre=$result['marketingsperre'];
  }

  public function Create()
  {
    $sql = "INSERT INTO ansprechpartner (id,typ,sprache,name,bereich,abteilung,unterabteilung,land,strasse,ort,plz,telefon,telefax,email,sonstiges,adresszusatz,steuer,adresse,logdatei,mobil,titel,anschreiben,ansprechpartner_land,vorname,geburtstag,geburtstagkalender,geburtstagskarte,geloescht,marketingsperre)
      VALUES('','{$this->typ}','{$this->sprache}','{$this->name}','{$this->bereich}','{$this->abteilung}','{$this->unterabteilung}','{$this->land}','{$this->strasse}','{$this->ort}','{$this->plz}','{$this->telefon}','{$this->telefax}','{$this->email}','{$this->sonstiges}','{$this->adresszusatz}','{$this->steuer}','{$this->adresse}','{$this->logdatei}','{$this->mobil}','{$this->titel}','{$this->anschreiben}','{$this->ansprechpartner_land}','{$this->vorname}','{$this->geburtstag}','{$this->geburtstagkalender}','{$this->geburtstagskarte}','{$this->geloescht}','{$this->marketingsperre}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE ansprechpartner SET
      typ='{$this->typ}',
      sprache='{$this->sprache}',
      name='{$this->name}',
      bereich='{$this->bereich}',
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
      mobil='{$this->mobil}',
      titel='{$this->titel}',
      anschreiben='{$this->anschreiben}',
      ansprechpartner_land='{$this->ansprechpartner_land}',
      vorname='{$this->vorname}',
      geburtstag='{$this->geburtstag}',
      geburtstagkalender='{$this->geburtstagkalender}',
      geburtstagskarte='{$this->geburtstagskarte}',
      geloescht='{$this->geloescht}',
      marketingsperre='{$this->marketingsperre}'
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

    $sql = "DELETE FROM ansprechpartner WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->typ="";
    $this->sprache="";
    $this->name="";
    $this->bereich="";
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
    $this->mobil="";
    $this->titel="";
    $this->anschreiben="";
    $this->ansprechpartner_land="";
    $this->vorname="";
    $this->geburtstag="";
    $this->geburtstagkalender="";
    $this->geburtstagskarte="";
    $this->geloescht="";
    $this->marketingsperre="";
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
  function SetBereich($value) { $this->bereich=$value; }
  function GetBereich() { return $this->bereich; }
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
  function SetMobil($value) { $this->mobil=$value; }
  function GetMobil() { return $this->mobil; }
  function SetTitel($value) { $this->titel=$value; }
  function GetTitel() { return $this->titel; }
  function SetAnschreiben($value) { $this->anschreiben=$value; }
  function GetAnschreiben() { return $this->anschreiben; }
  function SetAnsprechpartner_Land($value) { $this->ansprechpartner_land=$value; }
  function GetAnsprechpartner_Land() { return $this->ansprechpartner_land; }
  function SetVorname($value) { $this->vorname=$value; }
  function GetVorname() { return $this->vorname; }
  function SetGeburtstag($value) { $this->geburtstag=$value; }
  function GetGeburtstag() { return $this->geburtstag; }
  function SetGeburtstagkalender($value) { $this->geburtstagkalender=$value; }
  function GetGeburtstagkalender() { return $this->geburtstagkalender; }
  function SetGeburtstagskarte($value) { $this->geburtstagskarte=$value; }
  function GetGeburtstagskarte() { return $this->geburtstagskarte; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }
  function SetMarketingsperre($value) { $this->marketingsperre=$value; }
  function GetMarketingsperre() { return $this->marketingsperre; }

}

?>