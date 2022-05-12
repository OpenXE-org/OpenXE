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

class ObjGenSpedition_Avi
{

  private  $id;
  private  $typ;
  private  $name;
  private  $ansprechpartner;
  private  $abteilung;
  private  $unterabteilung;
  private  $strasse;
  private  $plz;
  private  $ort;
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
  private  $spedition_aviohnepreis;
  private  $spedition_avi_bestaetigt;
  private  $bestaetigteslieferdatum;
  private  $spedition_avibestaetigtper;
  private  $spedition_avibestaetigtabnummer;
  private  $gewuenschteslieferdatum;
  private  $pdfarchiviert;
  private  $versendet;
  private  $datum;
  private  $adresse;
  private  $projekt;
  private  $email;
  private  $freitext;
  private  $status;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM spedition_avi WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->typ=$result['typ'];
    $this->name=$result['name'];
    $this->ansprechpartner=$result['ansprechpartner'];
    $this->abteilung=$result['abteilung'];
    $this->unterabteilung=$result['unterabteilung'];
    $this->strasse=$result['strasse'];
    $this->plz=$result['plz'];
    $this->ort=$result['ort'];
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
    $this->spedition_aviohnepreis=$result['spedition_aviohnepreis'];
    $this->spedition_avi_bestaetigt=$result['spedition_avi_bestaetigt'];
    $this->bestaetigteslieferdatum=$result['bestaetigteslieferdatum'];
    $this->spedition_avibestaetigtper=$result['spedition_avibestaetigtper'];
    $this->spedition_avibestaetigtabnummer=$result['spedition_avibestaetigtabnummer'];
    $this->gewuenschteslieferdatum=$result['gewuenschteslieferdatum'];
    $this->pdfarchiviert=$result['pdfarchiviert'];
    $this->versendet=$result['versendet'];
    $this->datum=$result['datum'];
    $this->adresse=$result['adresse'];
    $this->projekt=$result['projekt'];
    $this->email=$result['email'];
    $this->freitext=$result['freitext'];
    $this->status=$result['status'];
  }

  public function Create()
  {
    $sql = "INSERT INTO spedition_avi (id,typ,name,ansprechpartner,abteilung,unterabteilung,strasse,plz,ort,land,liefername,lieferansprechpartner,lieferabteilung,lieferunterabteilung,lieferstrasse,lieferplz,lieferort,lieferland,schreibschutz,bodyzusatz,sprache,internebezeichnung,angelegtam,ust_befreit,anschreiben,projektfiliale,usereditid,useredittimestamp,steuersatz_normal,steuersatz_zwischen,steuersatz_ermaessigt,steuersatz_starkermaessigt,steuersatz_dienstleistung,waehrung,spedition_aviohnepreis,spedition_avi_bestaetigt,bestaetigteslieferdatum,spedition_avibestaetigtper,spedition_avibestaetigtabnummer,gewuenschteslieferdatum,pdfarchiviert,versendet,datum,adresse,projekt,email,freitext,status)
      VALUES('','{$this->typ}','{$this->name}','{$this->ansprechpartner}','{$this->abteilung}','{$this->unterabteilung}','{$this->strasse}','{$this->plz}','{$this->ort}','{$this->land}','{$this->liefername}','{$this->lieferansprechpartner}','{$this->lieferabteilung}','{$this->lieferunterabteilung}','{$this->lieferstrasse}','{$this->lieferplz}','{$this->lieferort}','{$this->lieferland}','{$this->schreibschutz}','{$this->bodyzusatz}','{$this->sprache}','{$this->internebezeichnung}','{$this->angelegtam}','{$this->ust_befreit}','{$this->anschreiben}','{$this->projektfiliale}','{$this->usereditid}','{$this->useredittimestamp}','{$this->steuersatz_normal}','{$this->steuersatz_zwischen}','{$this->steuersatz_ermaessigt}','{$this->steuersatz_starkermaessigt}','{$this->steuersatz_dienstleistung}','{$this->waehrung}','{$this->spedition_aviohnepreis}','{$this->spedition_avi_bestaetigt}','{$this->bestaetigteslieferdatum}','{$this->spedition_avibestaetigtper}','{$this->spedition_avibestaetigtabnummer}','{$this->gewuenschteslieferdatum}','{$this->pdfarchiviert}','{$this->versendet}','{$this->datum}','{$this->adresse}','{$this->projekt}','{$this->email}','{$this->freitext}','{$this->status}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE spedition_avi SET
      typ='{$this->typ}',
      name='{$this->name}',
      ansprechpartner='{$this->ansprechpartner}',
      abteilung='{$this->abteilung}',
      unterabteilung='{$this->unterabteilung}',
      strasse='{$this->strasse}',
      plz='{$this->plz}',
      ort='{$this->ort}',
      land='{$this->land}',
      liefername='{$this->liefername}',
      lieferansprechpartner='{$this->lieferansprechpartner}',
      lieferabteilung='{$this->lieferabteilung}',
      lieferunterabteilung='{$this->lieferunterabteilung}',
      lieferstrasse='{$this->lieferstrasse}',
      lieferplz='{$this->lieferplz}',
      lieferort='{$this->lieferort}',
      lieferland='{$this->lieferland}',
      schreibschutz='{$this->schreibschutz}',
      bodyzusatz='{$this->bodyzusatz}',
      sprache='{$this->sprache}',
      internebezeichnung='{$this->internebezeichnung}',
      angelegtam='{$this->angelegtam}',
      ust_befreit='{$this->ust_befreit}',
      anschreiben='{$this->anschreiben}',
      projektfiliale='{$this->projektfiliale}',
      usereditid='{$this->usereditid}',
      useredittimestamp='{$this->useredittimestamp}',
      steuersatz_normal='{$this->steuersatz_normal}',
      steuersatz_zwischen='{$this->steuersatz_zwischen}',
      steuersatz_ermaessigt='{$this->steuersatz_ermaessigt}',
      steuersatz_starkermaessigt='{$this->steuersatz_starkermaessigt}',
      steuersatz_dienstleistung='{$this->steuersatz_dienstleistung}',
      waehrung='{$this->waehrung}',
      spedition_aviohnepreis='{$this->spedition_aviohnepreis}',
      spedition_avi_bestaetigt='{$this->spedition_avi_bestaetigt}',
      bestaetigteslieferdatum='{$this->bestaetigteslieferdatum}',
      spedition_avibestaetigtper='{$this->spedition_avibestaetigtper}',
      spedition_avibestaetigtabnummer='{$this->spedition_avibestaetigtabnummer}',
      gewuenschteslieferdatum='{$this->gewuenschteslieferdatum}',
      pdfarchiviert='{$this->pdfarchiviert}',
      versendet='{$this->versendet}',
      datum='{$this->datum}',
      adresse='{$this->adresse}',
      projekt='{$this->projekt}',
      email='{$this->email}',
      freitext='{$this->freitext}',
      status='{$this->status}'
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

    $sql = "DELETE FROM spedition_avi WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->typ="";
    $this->name="";
    $this->ansprechpartner="";
    $this->abteilung="";
    $this->unterabteilung="";
    $this->strasse="";
    $this->plz="";
    $this->ort="";
    $this->land="";
    $this->liefername="";
    $this->lieferansprechpartner="";
    $this->lieferabteilung="";
    $this->lieferunterabteilung="";
    $this->lieferstrasse="";
    $this->lieferplz="";
    $this->lieferort="";
    $this->lieferland="";
    $this->schreibschutz="";
    $this->bodyzusatz="";
    $this->sprache="";
    $this->internebezeichnung="";
    $this->angelegtam="";
    $this->ust_befreit="";
    $this->anschreiben="";
    $this->projektfiliale="";
    $this->usereditid="";
    $this->useredittimestamp="";
    $this->steuersatz_normal="";
    $this->steuersatz_zwischen="";
    $this->steuersatz_ermaessigt="";
    $this->steuersatz_starkermaessigt="";
    $this->steuersatz_dienstleistung="";
    $this->waehrung="";
    $this->spedition_aviohnepreis="";
    $this->spedition_avi_bestaetigt="";
    $this->bestaetigteslieferdatum="";
    $this->spedition_avibestaetigtper="";
    $this->spedition_avibestaetigtabnummer="";
    $this->gewuenschteslieferdatum="";
    $this->pdfarchiviert="";
    $this->versendet="";
    $this->datum="";
    $this->adresse="";
    $this->projekt="";
    $this->email="";
    $this->freitext="";
    $this->status="";
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
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  function GetAnsprechpartner() { return $this->ansprechpartner; }
  function SetAbteilung($value) { $this->abteilung=$value; }
  function GetAbteilung() { return $this->abteilung; }
  function SetUnterabteilung($value) { $this->unterabteilung=$value; }
  function GetUnterabteilung() { return $this->unterabteilung; }
  function SetStrasse($value) { $this->strasse=$value; }
  function GetStrasse() { return $this->strasse; }
  function SetPlz($value) { $this->plz=$value; }
  function GetPlz() { return $this->plz; }
  function SetOrt($value) { $this->ort=$value; }
  function GetOrt() { return $this->ort; }
  function SetLand($value) { $this->land=$value; }
  function GetLand() { return $this->land; }
  function SetLiefername($value) { $this->liefername=$value; }
  function GetLiefername() { return $this->liefername; }
  function SetLieferansprechpartner($value) { $this->lieferansprechpartner=$value; }
  function GetLieferansprechpartner() { return $this->lieferansprechpartner; }
  function SetLieferabteilung($value) { $this->lieferabteilung=$value; }
  function GetLieferabteilung() { return $this->lieferabteilung; }
  function SetLieferunterabteilung($value) { $this->lieferunterabteilung=$value; }
  function GetLieferunterabteilung() { return $this->lieferunterabteilung; }
  function SetLieferstrasse($value) { $this->lieferstrasse=$value; }
  function GetLieferstrasse() { return $this->lieferstrasse; }
  function SetLieferplz($value) { $this->lieferplz=$value; }
  function GetLieferplz() { return $this->lieferplz; }
  function SetLieferort($value) { $this->lieferort=$value; }
  function GetLieferort() { return $this->lieferort; }
  function SetLieferland($value) { $this->lieferland=$value; }
  function GetLieferland() { return $this->lieferland; }
  function SetSchreibschutz($value) { $this->schreibschutz=$value; }
  function GetSchreibschutz() { return $this->schreibschutz; }
  function SetBodyzusatz($value) { $this->bodyzusatz=$value; }
  function GetBodyzusatz() { return $this->bodyzusatz; }
  function SetSprache($value) { $this->sprache=$value; }
  function GetSprache() { return $this->sprache; }
  function SetInternebezeichnung($value) { $this->internebezeichnung=$value; }
  function GetInternebezeichnung() { return $this->internebezeichnung; }
  function SetAngelegtam($value) { $this->angelegtam=$value; }
  function GetAngelegtam() { return $this->angelegtam; }
  function SetUst_Befreit($value) { $this->ust_befreit=$value; }
  function GetUst_Befreit() { return $this->ust_befreit; }
  function SetAnschreiben($value) { $this->anschreiben=$value; }
  function GetAnschreiben() { return $this->anschreiben; }
  function SetProjektfiliale($value) { $this->projektfiliale=$value; }
  function GetProjektfiliale() { return $this->projektfiliale; }
  function SetUsereditid($value) { $this->usereditid=$value; }
  function GetUsereditid() { return $this->usereditid; }
  function SetUseredittimestamp($value) { $this->useredittimestamp=$value; }
  function GetUseredittimestamp() { return $this->useredittimestamp; }
  function SetSteuersatz_Normal($value) { $this->steuersatz_normal=$value; }
  function GetSteuersatz_Normal() { return $this->steuersatz_normal; }
  function SetSteuersatz_Zwischen($value) { $this->steuersatz_zwischen=$value; }
  function GetSteuersatz_Zwischen() { return $this->steuersatz_zwischen; }
  function SetSteuersatz_Ermaessigt($value) { $this->steuersatz_ermaessigt=$value; }
  function GetSteuersatz_Ermaessigt() { return $this->steuersatz_ermaessigt; }
  function SetSteuersatz_Starkermaessigt($value) { $this->steuersatz_starkermaessigt=$value; }
  function GetSteuersatz_Starkermaessigt() { return $this->steuersatz_starkermaessigt; }
  function SetSteuersatz_Dienstleistung($value) { $this->steuersatz_dienstleistung=$value; }
  function GetSteuersatz_Dienstleistung() { return $this->steuersatz_dienstleistung; }
  function SetWaehrung($value) { $this->waehrung=$value; }
  function GetWaehrung() { return $this->waehrung; }
  function SetSpedition_Aviohnepreis($value) { $this->spedition_aviohnepreis=$value; }
  function GetSpedition_Aviohnepreis() { return $this->spedition_aviohnepreis; }
  function SetSpedition_Avi_Bestaetigt($value) { $this->spedition_avi_bestaetigt=$value; }
  function GetSpedition_Avi_Bestaetigt() { return $this->spedition_avi_bestaetigt; }
  function SetBestaetigteslieferdatum($value) { $this->bestaetigteslieferdatum=$value; }
  function GetBestaetigteslieferdatum() { return $this->bestaetigteslieferdatum; }
  function SetSpedition_Avibestaetigtper($value) { $this->spedition_avibestaetigtper=$value; }
  function GetSpedition_Avibestaetigtper() { return $this->spedition_avibestaetigtper; }
  function SetSpedition_Avibestaetigtabnummer($value) { $this->spedition_avibestaetigtabnummer=$value; }
  function GetSpedition_Avibestaetigtabnummer() { return $this->spedition_avibestaetigtabnummer; }
  function SetGewuenschteslieferdatum($value) { $this->gewuenschteslieferdatum=$value; }
  function GetGewuenschteslieferdatum() { return $this->gewuenschteslieferdatum; }
  function SetPdfarchiviert($value) { $this->pdfarchiviert=$value; }
  function GetPdfarchiviert() { return $this->pdfarchiviert; }
  function SetVersendet($value) { $this->versendet=$value; }
  function GetVersendet() { return $this->versendet; }
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetEmail($value) { $this->email=$value; }
  function GetEmail() { return $this->email; }
  function SetFreitext($value) { $this->freitext=$value; }
  function GetFreitext() { return $this->freitext; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }

}

?>