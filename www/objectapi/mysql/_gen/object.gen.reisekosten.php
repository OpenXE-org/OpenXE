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

class ObjGenReisekosten
{

  private  $id;
  private  $datum;
  private  $projekt;
  private  $teilprojekt;
  private  $prefix;
  private  $reisekostenart;
  private  $belegnr;
  private  $bearbeiter;
  private  $auftrag;
  private  $auftragid;
  private  $freitext;
  private  $status;
  private  $adresse;
  private  $mitarbeiter;
  private  $name;
  private  $abteilung;
  private  $unterabteilung;
  private  $strasse;
  private  $adresszusatz;
  private  $ansprechpartner;
  private  $plz;
  private  $ort;
  private  $land;
  private  $ustid;
  private  $email;
  private  $telefon;
  private  $telefax;
  private  $betreff;
  private  $kundennummer;
  private  $versandart;
  private  $versand;
  private  $firma;
  private  $versendet;
  private  $versendet_am;
  private  $versendet_per;
  private  $versendet_durch;
  private  $inbearbeitung_user;
  private  $logdatei;
  private  $ohne_briefpapier;
  private  $ust_befreit;
  private  $usereditid;
  private  $useredittimestamp;
  private  $steuersatz_normal;
  private  $steuersatz_zwischen;
  private  $steuersatz_ermaessigt;
  private  $steuersatz_starkermaessigt;
  private  $steuersatz_dienstleistung;
  private  $waehrung;
  private  $anlass;
  private  $internebemerkung;
  private  $von;
  private  $bis;
  private  $von_zeit;
  private  $bis_zeit;
  private  $schreibschutz;
  private  $pdfarchiviert;
  private  $pdfarchiviertversion;
  private  $typ;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `reisekosten` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->datum=$result['datum'];
    $this->projekt=$result['projekt'];
    $this->teilprojekt=$result['teilprojekt'];
    $this->prefix=$result['prefix'];
    $this->reisekostenart=$result['reisekostenart'];
    $this->belegnr=$result['belegnr'];
    $this->bearbeiter=$result['bearbeiter'];
    $this->auftrag=$result['auftrag'];
    $this->auftragid=$result['auftragid'];
    $this->freitext=$result['freitext'];
    $this->status=$result['status'];
    $this->adresse=$result['adresse'];
    $this->mitarbeiter=$result['mitarbeiter'];
    $this->name=$result['name'];
    $this->abteilung=$result['abteilung'];
    $this->unterabteilung=$result['unterabteilung'];
    $this->strasse=$result['strasse'];
    $this->adresszusatz=$result['adresszusatz'];
    $this->ansprechpartner=$result['ansprechpartner'];
    $this->plz=$result['plz'];
    $this->ort=$result['ort'];
    $this->land=$result['land'];
    $this->ustid=$result['ustid'];
    $this->email=$result['email'];
    $this->telefon=$result['telefon'];
    $this->telefax=$result['telefax'];
    $this->betreff=$result['betreff'];
    $this->kundennummer=$result['kundennummer'];
    $this->versandart=$result['versandart'];
    $this->versand=$result['versand'];
    $this->firma=$result['firma'];
    $this->versendet=$result['versendet'];
    $this->versendet_am=$result['versendet_am'];
    $this->versendet_per=$result['versendet_per'];
    $this->versendet_durch=$result['versendet_durch'];
    $this->inbearbeitung_user=$result['inbearbeitung_user'];
    $this->logdatei=$result['logdatei'];
    $this->ohne_briefpapier=$result['ohne_briefpapier'];
    $this->ust_befreit=$result['ust_befreit'];
    $this->usereditid=$result['usereditid'];
    $this->useredittimestamp=$result['useredittimestamp'];
    $this->steuersatz_normal=$result['steuersatz_normal'];
    $this->steuersatz_zwischen=$result['steuersatz_zwischen'];
    $this->steuersatz_ermaessigt=$result['steuersatz_ermaessigt'];
    $this->steuersatz_starkermaessigt=$result['steuersatz_starkermaessigt'];
    $this->steuersatz_dienstleistung=$result['steuersatz_dienstleistung'];
    $this->waehrung=$result['waehrung'];
    $this->anlass=$result['anlass'];
    $this->internebemerkung=$result['internebemerkung'];
    $this->von=$result['von'];
    $this->bis=$result['bis'];
    $this->von_zeit=$result['von_zeit'];
    $this->bis_zeit=$result['bis_zeit'];
    $this->schreibschutz=$result['schreibschutz'];
    $this->pdfarchiviert=$result['pdfarchiviert'];
    $this->pdfarchiviertversion=$result['pdfarchiviertversion'];
    $this->typ=$result['typ'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `reisekosten` (`id`,`datum`,`projekt`,`teilprojekt`,`prefix`,`reisekostenart`,`belegnr`,`bearbeiter`,`auftrag`,`auftragid`,`freitext`,`status`,`adresse`,`mitarbeiter`,`name`,`abteilung`,`unterabteilung`,`strasse`,`adresszusatz`,`ansprechpartner`,`plz`,`ort`,`land`,`ustid`,`email`,`telefon`,`telefax`,`betreff`,`kundennummer`,`versandart`,`versand`,`firma`,`versendet`,`versendet_am`,`versendet_per`,`versendet_durch`,`inbearbeitung_user`,`logdatei`,`ohne_briefpapier`,`ust_befreit`,`usereditid`,`useredittimestamp`,`steuersatz_normal`,`steuersatz_zwischen`,`steuersatz_ermaessigt`,`steuersatz_starkermaessigt`,`steuersatz_dienstleistung`,`waehrung`,`anlass`,`internebemerkung`,`von`,`bis`,`von_zeit`,`bis_zeit`,`schreibschutz`,`pdfarchiviert`,`pdfarchiviertversion`,`typ`)
      VALUES(NULL,'{$this->datum}','{$this->projekt}','{$this->teilprojekt}','{$this->prefix}','{$this->reisekostenart}','{$this->belegnr}','{$this->bearbeiter}','{$this->auftrag}','{$this->auftragid}','{$this->freitext}','{$this->status}','{$this->adresse}','{$this->mitarbeiter}','{$this->name}','{$this->abteilung}','{$this->unterabteilung}','{$this->strasse}','{$this->adresszusatz}','{$this->ansprechpartner}','{$this->plz}','{$this->ort}','{$this->land}','{$this->ustid}','{$this->email}','{$this->telefon}','{$this->telefax}','{$this->betreff}','{$this->kundennummer}','{$this->versandart}','{$this->versand}','{$this->firma}','{$this->versendet}','{$this->versendet_am}','{$this->versendet_per}','{$this->versendet_durch}','{$this->inbearbeitung_user}','{$this->logdatei}','{$this->ohne_briefpapier}','{$this->ust_befreit}','{$this->usereditid}','{$this->useredittimestamp}','{$this->steuersatz_normal}','{$this->steuersatz_zwischen}','{$this->steuersatz_ermaessigt}','{$this->steuersatz_starkermaessigt}','{$this->steuersatz_dienstleistung}','{$this->waehrung}','{$this->anlass}','{$this->internebemerkung}','{$this->von}','{$this->bis}','{$this->von_zeit}','{$this->bis_zeit}','{$this->schreibschutz}','{$this->pdfarchiviert}','{$this->pdfarchiviertversion}','{$this->typ}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `reisekosten` SET
      `datum`='{$this->datum}',
      `projekt`='{$this->projekt}',
      `teilprojekt`='{$this->teilprojekt}',
      `prefix`='{$this->prefix}',
      `reisekostenart`='{$this->reisekostenart}',
      `belegnr`='{$this->belegnr}',
      `bearbeiter`='{$this->bearbeiter}',
      `auftrag`='{$this->auftrag}',
      `auftragid`='{$this->auftragid}',
      `freitext`='{$this->freitext}',
      `status`='{$this->status}',
      `adresse`='{$this->adresse}',
      `mitarbeiter`='{$this->mitarbeiter}',
      `name`='{$this->name}',
      `abteilung`='{$this->abteilung}',
      `unterabteilung`='{$this->unterabteilung}',
      `strasse`='{$this->strasse}',
      `adresszusatz`='{$this->adresszusatz}',
      `ansprechpartner`='{$this->ansprechpartner}',
      `plz`='{$this->plz}',
      `ort`='{$this->ort}',
      `land`='{$this->land}',
      `ustid`='{$this->ustid}',
      `email`='{$this->email}',
      `telefon`='{$this->telefon}',
      `telefax`='{$this->telefax}',
      `betreff`='{$this->betreff}',
      `kundennummer`='{$this->kundennummer}',
      `versandart`='{$this->versandart}',
      `versand`='{$this->versand}',
      `firma`='{$this->firma}',
      `versendet`='{$this->versendet}',
      `versendet_am`='{$this->versendet_am}',
      `versendet_per`='{$this->versendet_per}',
      `versendet_durch`='{$this->versendet_durch}',
      `inbearbeitung_user`='{$this->inbearbeitung_user}',
      `logdatei`='{$this->logdatei}',
      `ohne_briefpapier`='{$this->ohne_briefpapier}',
      `ust_befreit`='{$this->ust_befreit}',
      `usereditid`='{$this->usereditid}',
      `useredittimestamp`='{$this->useredittimestamp}',
      `steuersatz_normal`='{$this->steuersatz_normal}',
      `steuersatz_zwischen`='{$this->steuersatz_zwischen}',
      `steuersatz_ermaessigt`='{$this->steuersatz_ermaessigt}',
      `steuersatz_starkermaessigt`='{$this->steuersatz_starkermaessigt}',
      `steuersatz_dienstleistung`='{$this->steuersatz_dienstleistung}',
      `waehrung`='{$this->waehrung}',
      `anlass`='{$this->anlass}',
      `internebemerkung`='{$this->internebemerkung}',
      `von`='{$this->von}',
      `bis`='{$this->bis}',
      `von_zeit`='{$this->von_zeit}',
      `bis_zeit`='{$this->bis_zeit}',
      `schreibschutz`='{$this->schreibschutz}',
      `pdfarchiviert`='{$this->pdfarchiviert}',
      `pdfarchiviertversion`='{$this->pdfarchiviertversion}',
      `typ`='{$this->typ}'
      WHERE (`id`='{$this->id}')";

    $this->app->DB->Update($sql);
  }

  public function Delete($id='')
  {
    if(is_numeric($id))
    {
      $this->id=$id;
    }
    else
      return -1;

    $sql = "DELETE FROM `reisekosten` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->datum='';
    $this->projekt='';
    $this->teilprojekt='';
    $this->prefix='';
    $this->reisekostenart='';
    $this->belegnr='';
    $this->bearbeiter='';
    $this->auftrag='';
    $this->auftragid='';
    $this->freitext='';
    $this->status='';
    $this->adresse='';
    $this->mitarbeiter='';
    $this->name='';
    $this->abteilung='';
    $this->unterabteilung='';
    $this->strasse='';
    $this->adresszusatz='';
    $this->ansprechpartner='';
    $this->plz='';
    $this->ort='';
    $this->land='';
    $this->ustid='';
    $this->email='';
    $this->telefon='';
    $this->telefax='';
    $this->betreff='';
    $this->kundennummer='';
    $this->versandart='';
    $this->versand='';
    $this->firma='';
    $this->versendet='';
    $this->versendet_am='';
    $this->versendet_per='';
    $this->versendet_durch='';
    $this->inbearbeitung_user='';
    $this->logdatei='';
    $this->ohne_briefpapier='';
    $this->ust_befreit='';
    $this->usereditid='';
    $this->useredittimestamp='';
    $this->steuersatz_normal='';
    $this->steuersatz_zwischen='';
    $this->steuersatz_ermaessigt='';
    $this->steuersatz_starkermaessigt='';
    $this->steuersatz_dienstleistung='';
    $this->waehrung='';
    $this->anlass='';
    $this->internebemerkung='';
    $this->von='';
    $this->bis='';
    $this->von_zeit='';
    $this->bis_zeit='';
    $this->schreibschutz='';
    $this->pdfarchiviert='';
    $this->pdfarchiviertversion='';
    $this->typ='';
  }

  public function Copy()
  {
    $this->id = '';
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

  public function SetId($value) { $this->id=$value; }
  public function GetId() { return $this->id; }
  public function SetDatum($value) { $this->datum=$value; }
  public function GetDatum() { return $this->datum; }
  public function SetProjekt($value) { $this->projekt=$value; }
  public function GetProjekt() { return $this->projekt; }
  public function SetTeilprojekt($value) { $this->teilprojekt=$value; }
  public function GetTeilprojekt() { return $this->teilprojekt; }
  public function SetPrefix($value) { $this->prefix=$value; }
  public function GetPrefix() { return $this->prefix; }
  public function SetReisekostenart($value) { $this->reisekostenart=$value; }
  public function GetReisekostenart() { return $this->reisekostenart; }
  public function SetBelegnr($value) { $this->belegnr=$value; }
  public function GetBelegnr() { return $this->belegnr; }
  public function SetBearbeiter($value) { $this->bearbeiter=$value; }
  public function GetBearbeiter() { return $this->bearbeiter; }
  public function SetAuftrag($value) { $this->auftrag=$value; }
  public function GetAuftrag() { return $this->auftrag; }
  public function SetAuftragid($value) { $this->auftragid=$value; }
  public function GetAuftragid() { return $this->auftragid; }
  public function SetFreitext($value) { $this->freitext=$value; }
  public function GetFreitext() { return $this->freitext; }
  public function SetStatus($value) { $this->status=$value; }
  public function GetStatus() { return $this->status; }
  public function SetAdresse($value) { $this->adresse=$value; }
  public function GetAdresse() { return $this->adresse; }
  public function SetMitarbeiter($value) { $this->mitarbeiter=$value; }
  public function GetMitarbeiter() { return $this->mitarbeiter; }
  public function SetName($value) { $this->name=$value; }
  public function GetName() { return $this->name; }
  public function SetAbteilung($value) { $this->abteilung=$value; }
  public function GetAbteilung() { return $this->abteilung; }
  public function SetUnterabteilung($value) { $this->unterabteilung=$value; }
  public function GetUnterabteilung() { return $this->unterabteilung; }
  public function SetStrasse($value) { $this->strasse=$value; }
  public function GetStrasse() { return $this->strasse; }
  public function SetAdresszusatz($value) { $this->adresszusatz=$value; }
  public function GetAdresszusatz() { return $this->adresszusatz; }
  public function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  public function GetAnsprechpartner() { return $this->ansprechpartner; }
  public function SetPlz($value) { $this->plz=$value; }
  public function GetPlz() { return $this->plz; }
  public function SetOrt($value) { $this->ort=$value; }
  public function GetOrt() { return $this->ort; }
  public function SetLand($value) { $this->land=$value; }
  public function GetLand() { return $this->land; }
  public function SetUstid($value) { $this->ustid=$value; }
  public function GetUstid() { return $this->ustid; }
  public function SetEmail($value) { $this->email=$value; }
  public function GetEmail() { return $this->email; }
  public function SetTelefon($value) { $this->telefon=$value; }
  public function GetTelefon() { return $this->telefon; }
  public function SetTelefax($value) { $this->telefax=$value; }
  public function GetTelefax() { return $this->telefax; }
  public function SetBetreff($value) { $this->betreff=$value; }
  public function GetBetreff() { return $this->betreff; }
  public function SetKundennummer($value) { $this->kundennummer=$value; }
  public function GetKundennummer() { return $this->kundennummer; }
  public function SetVersandart($value) { $this->versandart=$value; }
  public function GetVersandart() { return $this->versandart; }
  public function SetVersand($value) { $this->versand=$value; }
  public function GetVersand() { return $this->versand; }
  public function SetFirma($value) { $this->firma=$value; }
  public function GetFirma() { return $this->firma; }
  public function SetVersendet($value) { $this->versendet=$value; }
  public function GetVersendet() { return $this->versendet; }
  public function SetVersendet_Am($value) { $this->versendet_am=$value; }
  public function GetVersendet_Am() { return $this->versendet_am; }
  public function SetVersendet_Per($value) { $this->versendet_per=$value; }
  public function GetVersendet_Per() { return $this->versendet_per; }
  public function SetVersendet_Durch($value) { $this->versendet_durch=$value; }
  public function GetVersendet_Durch() { return $this->versendet_durch; }
  public function SetInbearbeitung_User($value) { $this->inbearbeitung_user=$value; }
  public function GetInbearbeitung_User() { return $this->inbearbeitung_user; }
  public function SetLogdatei($value) { $this->logdatei=$value; }
  public function GetLogdatei() { return $this->logdatei; }
  public function SetOhne_Briefpapier($value) { $this->ohne_briefpapier=$value; }
  public function GetOhne_Briefpapier() { return $this->ohne_briefpapier; }
  public function SetUst_Befreit($value) { $this->ust_befreit=$value; }
  public function GetUst_Befreit() { return $this->ust_befreit; }
  public function SetUsereditid($value) { $this->usereditid=$value; }
  public function GetUsereditid() { return $this->usereditid; }
  public function SetUseredittimestamp($value) { $this->useredittimestamp=$value; }
  public function GetUseredittimestamp() { return $this->useredittimestamp; }
  public function SetSteuersatz_Normal($value) { $this->steuersatz_normal=$value; }
  public function GetSteuersatz_Normal() { return $this->steuersatz_normal; }
  public function SetSteuersatz_Zwischen($value) { $this->steuersatz_zwischen=$value; }
  public function GetSteuersatz_Zwischen() { return $this->steuersatz_zwischen; }
  public function SetSteuersatz_Ermaessigt($value) { $this->steuersatz_ermaessigt=$value; }
  public function GetSteuersatz_Ermaessigt() { return $this->steuersatz_ermaessigt; }
  public function SetSteuersatz_Starkermaessigt($value) { $this->steuersatz_starkermaessigt=$value; }
  public function GetSteuersatz_Starkermaessigt() { return $this->steuersatz_starkermaessigt; }
  public function SetSteuersatz_Dienstleistung($value) { $this->steuersatz_dienstleistung=$value; }
  public function GetSteuersatz_Dienstleistung() { return $this->steuersatz_dienstleistung; }
  public function SetWaehrung($value) { $this->waehrung=$value; }
  public function GetWaehrung() { return $this->waehrung; }
  public function SetAnlass($value) { $this->anlass=$value; }
  public function GetAnlass() { return $this->anlass; }
  public function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  public function GetInternebemerkung() { return $this->internebemerkung; }
  public function SetVon($value) { $this->von=$value; }
  public function GetVon() { return $this->von; }
  public function SetBis($value) { $this->bis=$value; }
  public function GetBis() { return $this->bis; }
  public function SetVon_Zeit($value) { $this->von_zeit=$value; }
  public function GetVon_Zeit() { return $this->von_zeit; }
  public function SetBis_Zeit($value) { $this->bis_zeit=$value; }
  public function GetBis_Zeit() { return $this->bis_zeit; }
  public function SetSchreibschutz($value) { $this->schreibschutz=$value; }
  public function GetSchreibschutz() { return $this->schreibschutz; }
  public function SetPdfarchiviert($value) { $this->pdfarchiviert=$value; }
  public function GetPdfarchiviert() { return $this->pdfarchiviert; }
  public function SetPdfarchiviertversion($value) { $this->pdfarchiviertversion=$value; }
  public function GetPdfarchiviertversion() { return $this->pdfarchiviertversion; }
  public function SetTyp($value) { $this->typ=$value; }
  public function GetTyp() { return $this->typ; }

}
