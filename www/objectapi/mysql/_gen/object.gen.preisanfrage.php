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

class ObjGenPreisanfrage
{

  private  $id;
  private  $datum;
  private  $projekt;
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
  private  $lieferantennummer;
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
  private  $reservierart;
  private  $auslagerart;
  private  $projektfiliale;
  private  $datumauslieferung;
  private  $datumbereitstellung;
  private  $zuarchivieren;
  private  $internebezeichnung;
  private  $anschreiben;
  private  $usereditid;
  private  $useredittimestamp;
  private  $steuersatz_normal;
  private  $steuersatz_zwischen;
  private  $steuersatz_ermaessigt;
  private  $steuersatz_starkermaessigt;
  private  $steuersatz_dienstleistung;
  private  $waehrung;
  private  $typ;
  private  $bearbeiterid;
  private  $schreibschutz;
  private  $internebemerkung;
  private  $sprache;
  private  $bodyzusatz;
  private  $lieferbedingung;
  private  $titel;
  private  $bundesstaat;
  private  $zusammenfassen;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM preisanfrage WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->datum=$result['datum'];
    $this->projekt=$result['projekt'];
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
    $this->lieferantennummer=$result['lieferantennummer'];
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
    $this->reservierart=$result['reservierart'];
    $this->auslagerart=$result['auslagerart'];
    $this->projektfiliale=$result['projektfiliale'];
    $this->datumauslieferung=$result['datumauslieferung'];
    $this->datumbereitstellung=$result['datumbereitstellung'];
    $this->zuarchivieren=$result['zuarchivieren'];
    $this->internebezeichnung=$result['internebezeichnung'];
    $this->anschreiben=$result['anschreiben'];
    $this->usereditid=$result['usereditid'];
    $this->useredittimestamp=$result['useredittimestamp'];
    $this->steuersatz_normal=$result['steuersatz_normal'];
    $this->steuersatz_zwischen=$result['steuersatz_zwischen'];
    $this->steuersatz_ermaessigt=$result['steuersatz_ermaessigt'];
    $this->steuersatz_starkermaessigt=$result['steuersatz_starkermaessigt'];
    $this->steuersatz_dienstleistung=$result['steuersatz_dienstleistung'];
    $this->waehrung=$result['waehrung'];
    $this->typ=$result['typ'];
    $this->bearbeiterid=$result['bearbeiterid'];
    $this->schreibschutz=$result['schreibschutz'];
    $this->internebemerkung=$result['internebemerkung'];
    $this->sprache=$result['sprache'];
    $this->bodyzusatz=$result['bodyzusatz'];
    $this->lieferbedingung=$result['lieferbedingung'];
    $this->titel=$result['titel'];
    $this->bundesstaat=$result['bundesstaat'];
    $this->zusammenfassen=$result['zusammenfassen'];
  }

  public function Create()
  {
    $sql = "INSERT INTO preisanfrage (id,datum,projekt,belegnr,bearbeiter,auftrag,auftragid,freitext,status,adresse,mitarbeiter,name,abteilung,unterabteilung,strasse,adresszusatz,ansprechpartner,plz,ort,land,ustid,email,telefon,telefax,betreff,lieferantennummer,versandart,versand,firma,versendet,versendet_am,versendet_per,versendet_durch,inbearbeitung_user,logdatei,ohne_briefpapier,reservierart,auslagerart,projektfiliale,datumauslieferung,datumbereitstellung,zuarchivieren,internebezeichnung,anschreiben,usereditid,useredittimestamp,steuersatz_normal,steuersatz_zwischen,steuersatz_ermaessigt,steuersatz_starkermaessigt,steuersatz_dienstleistung,waehrung,typ,bearbeiterid,schreibschutz,internebemerkung,sprache,bodyzusatz,lieferbedingung,titel,bundesstaat,zusammenfassen)
      VALUES('','{$this->datum}','{$this->projekt}','{$this->belegnr}','{$this->bearbeiter}','{$this->auftrag}','{$this->auftragid}','{$this->freitext}','{$this->status}','{$this->adresse}','{$this->mitarbeiter}','{$this->name}','{$this->abteilung}','{$this->unterabteilung}','{$this->strasse}','{$this->adresszusatz}','{$this->ansprechpartner}','{$this->plz}','{$this->ort}','{$this->land}','{$this->ustid}','{$this->email}','{$this->telefon}','{$this->telefax}','{$this->betreff}','{$this->lieferantennummer}','{$this->versandart}','{$this->versand}','{$this->firma}','{$this->versendet}','{$this->versendet_am}','{$this->versendet_per}','{$this->versendet_durch}','{$this->inbearbeitung_user}','{$this->logdatei}','{$this->ohne_briefpapier}','{$this->reservierart}','{$this->auslagerart}','{$this->projektfiliale}','{$this->datumauslieferung}','{$this->datumbereitstellung}','{$this->zuarchivieren}','{$this->internebezeichnung}','{$this->anschreiben}','{$this->usereditid}','{$this->useredittimestamp}','{$this->steuersatz_normal}','{$this->steuersatz_zwischen}','{$this->steuersatz_ermaessigt}','{$this->steuersatz_starkermaessigt}','{$this->steuersatz_dienstleistung}','{$this->waehrung}','{$this->typ}','{$this->bearbeiterid}','{$this->schreibschutz}','{$this->internebemerkung}','{$this->sprache}','{$this->bodyzusatz}','{$this->lieferbedingung}','{$this->titel}','{$this->bundesstaat}','{$this->zusammenfassen}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE preisanfrage SET
      datum='{$this->datum}',
      projekt='{$this->projekt}',
      belegnr='{$this->belegnr}',
      bearbeiter='{$this->bearbeiter}',
      auftrag='{$this->auftrag}',
      auftragid='{$this->auftragid}',
      freitext='{$this->freitext}',
      status='{$this->status}',
      adresse='{$this->adresse}',
      mitarbeiter='{$this->mitarbeiter}',
      name='{$this->name}',
      abteilung='{$this->abteilung}',
      unterabteilung='{$this->unterabteilung}',
      strasse='{$this->strasse}',
      adresszusatz='{$this->adresszusatz}',
      ansprechpartner='{$this->ansprechpartner}',
      plz='{$this->plz}',
      ort='{$this->ort}',
      land='{$this->land}',
      ustid='{$this->ustid}',
      email='{$this->email}',
      telefon='{$this->telefon}',
      telefax='{$this->telefax}',
      betreff='{$this->betreff}',
      lieferantennummer='{$this->lieferantennummer}',
      versandart='{$this->versandart}',
      versand='{$this->versand}',
      firma='{$this->firma}',
      versendet='{$this->versendet}',
      versendet_am='{$this->versendet_am}',
      versendet_per='{$this->versendet_per}',
      versendet_durch='{$this->versendet_durch}',
      inbearbeitung_user='{$this->inbearbeitung_user}',
      logdatei='{$this->logdatei}',
      ohne_briefpapier='{$this->ohne_briefpapier}',
      reservierart='{$this->reservierart}',
      auslagerart='{$this->auslagerart}',
      projektfiliale='{$this->projektfiliale}',
      datumauslieferung='{$this->datumauslieferung}',
      datumbereitstellung='{$this->datumbereitstellung}',
      zuarchivieren='{$this->zuarchivieren}',
      internebezeichnung='{$this->internebezeichnung}',
      anschreiben='{$this->anschreiben}',
      usereditid='{$this->usereditid}',
      useredittimestamp='{$this->useredittimestamp}',
      steuersatz_normal='{$this->steuersatz_normal}',
      steuersatz_zwischen='{$this->steuersatz_zwischen}',
      steuersatz_ermaessigt='{$this->steuersatz_ermaessigt}',
      steuersatz_starkermaessigt='{$this->steuersatz_starkermaessigt}',
      steuersatz_dienstleistung='{$this->steuersatz_dienstleistung}',
      waehrung='{$this->waehrung}',
      typ='{$this->typ}',
      bearbeiterid='{$this->bearbeiterid}',
      schreibschutz='{$this->schreibschutz}',
      internebemerkung='{$this->internebemerkung}',
      sprache='{$this->sprache}',
      bodyzusatz='{$this->bodyzusatz}',
      lieferbedingung='{$this->lieferbedingung}',
      titel='{$this->titel}',
      bundesstaat='{$this->bundesstaat}',
      zusammenfassen='{$this->zusammenfassen}'
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

    $sql = "DELETE FROM preisanfrage WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->datum="";
    $this->projekt="";
    $this->belegnr="";
    $this->bearbeiter="";
    $this->auftrag="";
    $this->auftragid="";
    $this->freitext="";
    $this->status="";
    $this->adresse="";
    $this->mitarbeiter="";
    $this->name="";
    $this->abteilung="";
    $this->unterabteilung="";
    $this->strasse="";
    $this->adresszusatz="";
    $this->ansprechpartner="";
    $this->plz="";
    $this->ort="";
    $this->land="";
    $this->ustid="";
    $this->email="";
    $this->telefon="";
    $this->telefax="";
    $this->betreff="";
    $this->lieferantennummer="";
    $this->versandart="";
    $this->versand="";
    $this->firma="";
    $this->versendet="";
    $this->versendet_am="";
    $this->versendet_per="";
    $this->versendet_durch="";
    $this->inbearbeitung_user="";
    $this->logdatei="";
    $this->ohne_briefpapier="";
    $this->reservierart="";
    $this->auslagerart="";
    $this->projektfiliale="";
    $this->datumauslieferung="";
    $this->datumbereitstellung="";
    $this->zuarchivieren="";
    $this->internebezeichnung="";
    $this->anschreiben="";
    $this->usereditid="";
    $this->useredittimestamp="";
    $this->steuersatz_normal="";
    $this->steuersatz_zwischen="";
    $this->steuersatz_ermaessigt="";
    $this->steuersatz_starkermaessigt="";
    $this->steuersatz_dienstleistung="";
    $this->waehrung="";
    $this->typ="";
    $this->bearbeiterid="";
    $this->schreibschutz="";
    $this->internebemerkung="";
    $this->sprache="";
    $this->bodyzusatz="";
    $this->lieferbedingung="";
    $this->titel="";
    $this->bundesstaat="";
    $this->zusammenfassen="";
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
  function SetAuftrag($value) { $this->auftrag=$value; }
  function GetAuftrag() { return $this->auftrag; }
  function SetAuftragid($value) { $this->auftragid=$value; }
  function GetAuftragid() { return $this->auftragid; }
  function SetFreitext($value) { $this->freitext=$value; }
  function GetFreitext() { return $this->freitext; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetMitarbeiter($value) { $this->mitarbeiter=$value; }
  function GetMitarbeiter() { return $this->mitarbeiter; }
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetAbteilung($value) { $this->abteilung=$value; }
  function GetAbteilung() { return $this->abteilung; }
  function SetUnterabteilung($value) { $this->unterabteilung=$value; }
  function GetUnterabteilung() { return $this->unterabteilung; }
  function SetStrasse($value) { $this->strasse=$value; }
  function GetStrasse() { return $this->strasse; }
  function SetAdresszusatz($value) { $this->adresszusatz=$value; }
  function GetAdresszusatz() { return $this->adresszusatz; }
  function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  function GetAnsprechpartner() { return $this->ansprechpartner; }
  function SetPlz($value) { $this->plz=$value; }
  function GetPlz() { return $this->plz; }
  function SetOrt($value) { $this->ort=$value; }
  function GetOrt() { return $this->ort; }
  function SetLand($value) { $this->land=$value; }
  function GetLand() { return $this->land; }
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
  function SetLieferantennummer($value) { $this->lieferantennummer=$value; }
  function GetLieferantennummer() { return $this->lieferantennummer; }
  function SetVersandart($value) { $this->versandart=$value; }
  function GetVersandart() { return $this->versandart; }
  function SetVersand($value) { $this->versand=$value; }
  function GetVersand() { return $this->versand; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetVersendet($value) { $this->versendet=$value; }
  function GetVersendet() { return $this->versendet; }
  function SetVersendet_Am($value) { $this->versendet_am=$value; }
  function GetVersendet_Am() { return $this->versendet_am; }
  function SetVersendet_Per($value) { $this->versendet_per=$value; }
  function GetVersendet_Per() { return $this->versendet_per; }
  function SetVersendet_Durch($value) { $this->versendet_durch=$value; }
  function GetVersendet_Durch() { return $this->versendet_durch; }
  function SetInbearbeitung_User($value) { $this->inbearbeitung_user=$value; }
  function GetInbearbeitung_User() { return $this->inbearbeitung_user; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetOhne_Briefpapier($value) { $this->ohne_briefpapier=$value; }
  function GetOhne_Briefpapier() { return $this->ohne_briefpapier; }
  function SetReservierart($value) { $this->reservierart=$value; }
  function GetReservierart() { return $this->reservierart; }
  function SetAuslagerart($value) { $this->auslagerart=$value; }
  function GetAuslagerart() { return $this->auslagerart; }
  function SetProjektfiliale($value) { $this->projektfiliale=$value; }
  function GetProjektfiliale() { return $this->projektfiliale; }
  function SetDatumauslieferung($value) { $this->datumauslieferung=$value; }
  function GetDatumauslieferung() { return $this->datumauslieferung; }
  function SetDatumbereitstellung($value) { $this->datumbereitstellung=$value; }
  function GetDatumbereitstellung() { return $this->datumbereitstellung; }
  function SetZuarchivieren($value) { $this->zuarchivieren=$value; }
  function GetZuarchivieren() { return $this->zuarchivieren; }
  function SetInternebezeichnung($value) { $this->internebezeichnung=$value; }
  function GetInternebezeichnung() { return $this->internebezeichnung; }
  function SetAnschreiben($value) { $this->anschreiben=$value; }
  function GetAnschreiben() { return $this->anschreiben; }
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
  function SetTyp($value) { $this->typ=$value; }
  function GetTyp() { return $this->typ; }
  function SetBearbeiterid($value) { $this->bearbeiterid=$value; }
  function GetBearbeiterid() { return $this->bearbeiterid; }
  function SetSchreibschutz($value) { $this->schreibschutz=$value; }
  function GetSchreibschutz() { return $this->schreibschutz; }
  function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  function GetInternebemerkung() { return $this->internebemerkung; }
  function SetSprache($value) { $this->sprache=$value; }
  function GetSprache() { return $this->sprache; }
  function SetBodyzusatz($value) { $this->bodyzusatz=$value; }
  function GetBodyzusatz() { return $this->bodyzusatz; }
  function SetLieferbedingung($value) { $this->lieferbedingung=$value; }
  function GetLieferbedingung() { return $this->lieferbedingung; }
  function SetTitel($value) { $this->titel=$value; }
  function GetTitel() { return $this->titel; }
  function SetBundesstaat($value) { $this->bundesstaat=$value; }
  function GetBundesstaat() { return $this->bundesstaat; }
  function SetZusammenfassen($value) { $this->zusammenfassen=$value; }
  function GetZusammenfassen() { return $this->zusammenfassen; }

}

?>