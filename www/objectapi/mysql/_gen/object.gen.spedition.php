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

class ObjGenSpedition
{

  private  $id;
  private  $adresse;
  private  $projekt;
  private  $datum;
  private  $typ;
  private  $name;
  private  $ansprechpartner;
  private  $abteilung;
  private  $unterabteilung;
  private  $strasse;
  private  $plz;
  private  $ort;
  private  $status;
  private  $land;
  private  $liefername;
  private  $lieferansprechpartner;
  private  $lieferabteilung;
  private  $lieferunterabteilung;
  private  $lieferstrasse;
  private  $lieferplz;
  private  $lieferort;
  private  $lieferland;
  private  $schreibschutz;
  private  $bodyzusatz;
  private  $sprache;
  private  $internebezeichnung;
  private  $email;
  private  $angelegtam;
  private  $ust_befreit;
  private  $anschreiben;
  private  $projektfiliale;
  private  $usereditid;
  private  $useredittimestamp;
  private  $steuersatz_normal;
  private  $steuersatz_zwischen;
  private  $steuersatz_ermaessigt;
  private  $steuersatz_starkermaessigt;
  private  $steuersatz_dienstleistung;
  private  $waehrung;
  private  $speditionohnepreis;
  private  $spedition_bestaetigt;
  private  $bestaetigteslieferdatum;
  private  $speditionbestaetigtper;
  private  $speditionbestaetigtabnummer;
  private  $gewuenschteslieferdatum;
  private  $pdfarchiviert;
  private  $versendet;
  private  $freitext;
  private  $adresszusatz;
  private  $ustid;
  private  $telefon;
  private  $telefax;
  private  $lieferbedingung;
  private  $titel;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `spedition` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->adresse=$result['adresse'];
    $this->projekt=$result['projekt'];
    $this->datum=$result['datum'];
    $this->typ=$result['typ'];
    $this->name=$result['name'];
    $this->ansprechpartner=$result['ansprechpartner'];
    $this->abteilung=$result['abteilung'];
    $this->unterabteilung=$result['unterabteilung'];
    $this->strasse=$result['strasse'];
    $this->plz=$result['plz'];
    $this->ort=$result['ort'];
    $this->status=$result['status'];
    $this->land=$result['land'];
    $this->liefername=$result['liefername'];
    $this->lieferansprechpartner=$result['lieferansprechpartner'];
    $this->lieferabteilung=$result['lieferabteilung'];
    $this->lieferunterabteilung=$result['lieferunterabteilung'];
    $this->lieferstrasse=$result['lieferstrasse'];
    $this->lieferplz=$result['lieferplz'];
    $this->lieferort=$result['lieferort'];
    $this->lieferland=$result['lieferland'];
    $this->schreibschutz=$result['schreibschutz'];
    $this->bodyzusatz=$result['bodyzusatz'];
    $this->sprache=$result['sprache'];
    $this->internebezeichnung=$result['internebezeichnung'];
    $this->email=$result['email'];
    $this->angelegtam=$result['angelegtam'];
    $this->ust_befreit=$result['ust_befreit'];
    $this->anschreiben=$result['anschreiben'];
    $this->projektfiliale=$result['projektfiliale'];
    $this->usereditid=$result['usereditid'];
    $this->useredittimestamp=$result['useredittimestamp'];
    $this->steuersatz_normal=$result['steuersatz_normal'];
    $this->steuersatz_zwischen=$result['steuersatz_zwischen'];
    $this->steuersatz_ermaessigt=$result['steuersatz_ermaessigt'];
    $this->steuersatz_starkermaessigt=$result['steuersatz_starkermaessigt'];
    $this->steuersatz_dienstleistung=$result['steuersatz_dienstleistung'];
    $this->waehrung=$result['waehrung'];
    $this->speditionohnepreis=$result['speditionohnepreis'];
    $this->spedition_bestaetigt=$result['spedition_bestaetigt'];
    $this->bestaetigteslieferdatum=$result['bestaetigteslieferdatum'];
    $this->speditionbestaetigtper=$result['speditionbestaetigtper'];
    $this->speditionbestaetigtabnummer=$result['speditionbestaetigtabnummer'];
    $this->gewuenschteslieferdatum=$result['gewuenschteslieferdatum'];
    $this->pdfarchiviert=$result['pdfarchiviert'];
    $this->versendet=$result['versendet'];
    $this->freitext=$result['freitext'];
    $this->adresszusatz=$result['adresszusatz'];
    $this->ustid=$result['ustid'];
    $this->telefon=$result['telefon'];
    $this->telefax=$result['telefax'];
    $this->lieferbedingung=$result['lieferbedingung'];
    $this->titel=$result['titel'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `spedition` (`id`,`adresse`,`projekt`,`datum`,`typ`,`name`,`ansprechpartner`,`abteilung`,`unterabteilung`,`strasse`,`plz`,`ort`,`status`,`land`,`liefername`,`lieferansprechpartner`,`lieferabteilung`,`lieferunterabteilung`,`lieferstrasse`,`lieferplz`,`lieferort`,`lieferland`,`schreibschutz`,`bodyzusatz`,`sprache`,`internebezeichnung`,`email`,`angelegtam`,`ust_befreit`,`anschreiben`,`projektfiliale`,`usereditid`,`useredittimestamp`,`steuersatz_normal`,`steuersatz_zwischen`,`steuersatz_ermaessigt`,`steuersatz_starkermaessigt`,`steuersatz_dienstleistung`,`waehrung`,`speditionohnepreis`,`spedition_bestaetigt`,`bestaetigteslieferdatum`,`speditionbestaetigtper`,`speditionbestaetigtabnummer`,`gewuenschteslieferdatum`,`pdfarchiviert`,`versendet`,`freitext`,`adresszusatz`,`ustid`,`telefon`,`telefax`,`lieferbedingung`,`titel`)
      VALUES(NULL,'{$this->adresse}','{$this->projekt}','{$this->datum}','{$this->typ}','{$this->name}','{$this->ansprechpartner}','{$this->abteilung}','{$this->unterabteilung}','{$this->strasse}','{$this->plz}','{$this->ort}','{$this->status}','{$this->land}','{$this->liefername}','{$this->lieferansprechpartner}','{$this->lieferabteilung}','{$this->lieferunterabteilung}','{$this->lieferstrasse}','{$this->lieferplz}','{$this->lieferort}','{$this->lieferland}','{$this->schreibschutz}','{$this->bodyzusatz}','{$this->sprache}','{$this->internebezeichnung}','{$this->email}','{$this->angelegtam}','{$this->ust_befreit}','{$this->anschreiben}','{$this->projektfiliale}','{$this->usereditid}','{$this->useredittimestamp}','{$this->steuersatz_normal}','{$this->steuersatz_zwischen}','{$this->steuersatz_ermaessigt}','{$this->steuersatz_starkermaessigt}','{$this->steuersatz_dienstleistung}','{$this->waehrung}','{$this->speditionohnepreis}','{$this->spedition_bestaetigt}','{$this->bestaetigteslieferdatum}','{$this->speditionbestaetigtper}','{$this->speditionbestaetigtabnummer}','{$this->gewuenschteslieferdatum}','{$this->pdfarchiviert}','{$this->versendet}','{$this->freitext}','{$this->adresszusatz}','{$this->ustid}','{$this->telefon}','{$this->telefax}','{$this->lieferbedingung}','{$this->titel}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `spedition` SET
      `adresse`='{$this->adresse}',
      `projekt`='{$this->projekt}',
      `datum`='{$this->datum}',
      `typ`='{$this->typ}',
      `name`='{$this->name}',
      `ansprechpartner`='{$this->ansprechpartner}',
      `abteilung`='{$this->abteilung}',
      `unterabteilung`='{$this->unterabteilung}',
      `strasse`='{$this->strasse}',
      `plz`='{$this->plz}',
      `ort`='{$this->ort}',
      `status`='{$this->status}',
      `land`='{$this->land}',
      `liefername`='{$this->liefername}',
      `lieferansprechpartner`='{$this->lieferansprechpartner}',
      `lieferabteilung`='{$this->lieferabteilung}',
      `lieferunterabteilung`='{$this->lieferunterabteilung}',
      `lieferstrasse`='{$this->lieferstrasse}',
      `lieferplz`='{$this->lieferplz}',
      `lieferort`='{$this->lieferort}',
      `lieferland`='{$this->lieferland}',
      `schreibschutz`='{$this->schreibschutz}',
      `bodyzusatz`='{$this->bodyzusatz}',
      `sprache`='{$this->sprache}',
      `internebezeichnung`='{$this->internebezeichnung}',
      `email`='{$this->email}',
      `angelegtam`='{$this->angelegtam}',
      `ust_befreit`='{$this->ust_befreit}',
      `anschreiben`='{$this->anschreiben}',
      `projektfiliale`='{$this->projektfiliale}',
      `usereditid`='{$this->usereditid}',
      `useredittimestamp`='{$this->useredittimestamp}',
      `steuersatz_normal`='{$this->steuersatz_normal}',
      `steuersatz_zwischen`='{$this->steuersatz_zwischen}',
      `steuersatz_ermaessigt`='{$this->steuersatz_ermaessigt}',
      `steuersatz_starkermaessigt`='{$this->steuersatz_starkermaessigt}',
      `steuersatz_dienstleistung`='{$this->steuersatz_dienstleistung}',
      `waehrung`='{$this->waehrung}',
      `speditionohnepreis`='{$this->speditionohnepreis}',
      `spedition_bestaetigt`='{$this->spedition_bestaetigt}',
      `bestaetigteslieferdatum`='{$this->bestaetigteslieferdatum}',
      `speditionbestaetigtper`='{$this->speditionbestaetigtper}',
      `speditionbestaetigtabnummer`='{$this->speditionbestaetigtabnummer}',
      `gewuenschteslieferdatum`='{$this->gewuenschteslieferdatum}',
      `pdfarchiviert`='{$this->pdfarchiviert}',
      `versendet`='{$this->versendet}',
      `freitext`='{$this->freitext}',
      `adresszusatz`='{$this->adresszusatz}',
      `ustid`='{$this->ustid}',
      `telefon`='{$this->telefon}',
      `telefax`='{$this->telefax}',
      `lieferbedingung`='{$this->lieferbedingung}',
      `titel`='{$this->titel}'
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

    $sql = "DELETE FROM `spedition` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->adresse='';
    $this->projekt='';
    $this->datum='';
    $this->typ='';
    $this->name='';
    $this->ansprechpartner='';
    $this->abteilung='';
    $this->unterabteilung='';
    $this->strasse='';
    $this->plz='';
    $this->ort='';
    $this->status='';
    $this->land='';
    $this->liefername='';
    $this->lieferansprechpartner='';
    $this->lieferabteilung='';
    $this->lieferunterabteilung='';
    $this->lieferstrasse='';
    $this->lieferplz='';
    $this->lieferort='';
    $this->lieferland='';
    $this->schreibschutz='';
    $this->bodyzusatz='';
    $this->sprache='';
    $this->internebezeichnung='';
    $this->email='';
    $this->angelegtam='';
    $this->ust_befreit='';
    $this->anschreiben='';
    $this->projektfiliale='';
    $this->usereditid='';
    $this->useredittimestamp='';
    $this->steuersatz_normal='';
    $this->steuersatz_zwischen='';
    $this->steuersatz_ermaessigt='';
    $this->steuersatz_starkermaessigt='';
    $this->steuersatz_dienstleistung='';
    $this->waehrung='';
    $this->speditionohnepreis='';
    $this->spedition_bestaetigt='';
    $this->bestaetigteslieferdatum='';
    $this->speditionbestaetigtper='';
    $this->speditionbestaetigtabnummer='';
    $this->gewuenschteslieferdatum='';
    $this->pdfarchiviert='';
    $this->versendet='';
    $this->freitext='';
    $this->adresszusatz='';
    $this->ustid='';
    $this->telefon='';
    $this->telefax='';
    $this->lieferbedingung='';
    $this->titel='';
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
  public function SetAdresse($value) { $this->adresse=$value; }
  public function GetAdresse() { return $this->adresse; }
  public function SetProjekt($value) { $this->projekt=$value; }
  public function GetProjekt() { return $this->projekt; }
  public function SetDatum($value) { $this->datum=$value; }
  public function GetDatum() { return $this->datum; }
  public function SetTyp($value) { $this->typ=$value; }
  public function GetTyp() { return $this->typ; }
  public function SetName($value) { $this->name=$value; }
  public function GetName() { return $this->name; }
  public function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  public function GetAnsprechpartner() { return $this->ansprechpartner; }
  public function SetAbteilung($value) { $this->abteilung=$value; }
  public function GetAbteilung() { return $this->abteilung; }
  public function SetUnterabteilung($value) { $this->unterabteilung=$value; }
  public function GetUnterabteilung() { return $this->unterabteilung; }
  public function SetStrasse($value) { $this->strasse=$value; }
  public function GetStrasse() { return $this->strasse; }
  public function SetPlz($value) { $this->plz=$value; }
  public function GetPlz() { return $this->plz; }
  public function SetOrt($value) { $this->ort=$value; }
  public function GetOrt() { return $this->ort; }
  public function SetStatus($value) { $this->status=$value; }
  public function GetStatus() { return $this->status; }
  public function SetLand($value) { $this->land=$value; }
  public function GetLand() { return $this->land; }
  public function SetLiefername($value) { $this->liefername=$value; }
  public function GetLiefername() { return $this->liefername; }
  public function SetLieferansprechpartner($value) { $this->lieferansprechpartner=$value; }
  public function GetLieferansprechpartner() { return $this->lieferansprechpartner; }
  public function SetLieferabteilung($value) { $this->lieferabteilung=$value; }
  public function GetLieferabteilung() { return $this->lieferabteilung; }
  public function SetLieferunterabteilung($value) { $this->lieferunterabteilung=$value; }
  public function GetLieferunterabteilung() { return $this->lieferunterabteilung; }
  public function SetLieferstrasse($value) { $this->lieferstrasse=$value; }
  public function GetLieferstrasse() { return $this->lieferstrasse; }
  public function SetLieferplz($value) { $this->lieferplz=$value; }
  public function GetLieferplz() { return $this->lieferplz; }
  public function SetLieferort($value) { $this->lieferort=$value; }
  public function GetLieferort() { return $this->lieferort; }
  public function SetLieferland($value) { $this->lieferland=$value; }
  public function GetLieferland() { return $this->lieferland; }
  public function SetSchreibschutz($value) { $this->schreibschutz=$value; }
  public function GetSchreibschutz() { return $this->schreibschutz; }
  public function SetBodyzusatz($value) { $this->bodyzusatz=$value; }
  public function GetBodyzusatz() { return $this->bodyzusatz; }
  public function SetSprache($value) { $this->sprache=$value; }
  public function GetSprache() { return $this->sprache; }
  public function SetInternebezeichnung($value) { $this->internebezeichnung=$value; }
  public function GetInternebezeichnung() { return $this->internebezeichnung; }
  public function SetEmail($value) { $this->email=$value; }
  public function GetEmail() { return $this->email; }
  public function SetAngelegtam($value) { $this->angelegtam=$value; }
  public function GetAngelegtam() { return $this->angelegtam; }
  public function SetUst_Befreit($value) { $this->ust_befreit=$value; }
  public function GetUst_Befreit() { return $this->ust_befreit; }
  public function SetAnschreiben($value) { $this->anschreiben=$value; }
  public function GetAnschreiben() { return $this->anschreiben; }
  public function SetProjektfiliale($value) { $this->projektfiliale=$value; }
  public function GetProjektfiliale() { return $this->projektfiliale; }
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
  public function SetSpeditionohnepreis($value) { $this->speditionohnepreis=$value; }
  public function GetSpeditionohnepreis() { return $this->speditionohnepreis; }
  public function SetSpedition_Bestaetigt($value) { $this->spedition_bestaetigt=$value; }
  public function GetSpedition_Bestaetigt() { return $this->spedition_bestaetigt; }
  public function SetBestaetigteslieferdatum($value) { $this->bestaetigteslieferdatum=$value; }
  public function GetBestaetigteslieferdatum() { return $this->bestaetigteslieferdatum; }
  public function SetSpeditionbestaetigtper($value) { $this->speditionbestaetigtper=$value; }
  public function GetSpeditionbestaetigtper() { return $this->speditionbestaetigtper; }
  public function SetSpeditionbestaetigtabnummer($value) { $this->speditionbestaetigtabnummer=$value; }
  public function GetSpeditionbestaetigtabnummer() { return $this->speditionbestaetigtabnummer; }
  public function SetGewuenschteslieferdatum($value) { $this->gewuenschteslieferdatum=$value; }
  public function GetGewuenschteslieferdatum() { return $this->gewuenschteslieferdatum; }
  public function SetPdfarchiviert($value) { $this->pdfarchiviert=$value; }
  public function GetPdfarchiviert() { return $this->pdfarchiviert; }
  public function SetVersendet($value) { $this->versendet=$value; }
  public function GetVersendet() { return $this->versendet; }
  public function SetFreitext($value) { $this->freitext=$value; }
  public function GetFreitext() { return $this->freitext; }
  public function SetAdresszusatz($value) { $this->adresszusatz=$value; }
  public function GetAdresszusatz() { return $this->adresszusatz; }
  public function SetUstid($value) { $this->ustid=$value; }
  public function GetUstid() { return $this->ustid; }
  public function SetTelefon($value) { $this->telefon=$value; }
  public function GetTelefon() { return $this->telefon; }
  public function SetTelefax($value) { $this->telefax=$value; }
  public function GetTelefax() { return $this->telefax; }
  public function SetLieferbedingung($value) { $this->lieferbedingung=$value; }
  public function GetLieferbedingung() { return $this->lieferbedingung; }
  public function SetTitel($value) { $this->titel=$value; }
  public function GetTitel() { return $this->titel; }

}
