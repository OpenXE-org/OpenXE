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

class ObjGenBestellung
{

  private  $id;
  private  $datum;
  private  $projekt;
  private  $bestellungsart;
  private  $belegnr;
  private  $bearbeiter;
  private  $angebot;
  private  $freitext;
  private  $internebemerkung;
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
  private  $land;
  private  $abweichendelieferadresse;
  private  $liefername;
  private  $lieferabteilung;
  private  $lieferunterabteilung;
  private  $lieferland;
  private  $lieferstrasse;
  private  $lieferort;
  private  $lieferplz;
  private  $lieferadresszusatz;
  private  $lieferansprechpartner;
  private  $ustid;
  private  $ust_befreit;
  private  $email;
  private  $telefon;
  private  $telefax;
  private  $betreff;
  private  $kundennummer;
  private  $lieferantennummer;
  private  $versandart;
  private  $lieferdatum;
  private  $einkaeufer;
  private  $keineartikelnummern;
  private  $zahlungsweise;
  private  $zahlungsstatus;
  private  $zahlungszieltage;
  private  $zahlungszieltageskonto;
  private  $zahlungszielskonto;
  private  $gesamtsumme;
  private  $bank_inhaber;
  private  $bank_institut;
  private  $bank_blz;
  private  $bank_konto;
  private  $paypalaccount;
  private  $bestellbestaetigung;
  private  $firma;
  private  $versendet;
  private  $versendet_am;
  private  $versendet_per;
  private  $versendet_durch;
  private  $logdatei;
  private  $artikelnummerninfotext;
  private  $ansprechpartner;
  private  $anschreiben;
  private  $usereditid;
  private  $useredittimestamp;
  private  $steuersatz_normal;
  private  $steuersatz_zwischen;
  private  $steuersatz_ermaessigt;
  private  $steuersatz_starkermaessigt;
  private  $steuersatz_dienstleistung;
  private  $waehrung;
  private  $bestellungohnepreis;
  private  $schreibschutz;
  private  $pdfarchiviert;
  private  $pdfarchiviertversion;
  private  $typ;
  private  $verbindlichkeiteninfo;
  private  $ohne_briefpapier;
  private  $projektfiliale;
  private  $bestellung_bestaetigt;
  private  $bestaetigteslieferdatum;
  private  $bestellungbestaetigtper;
  private  $bestellungbestaetigtabnummer;
  private  $gewuenschteslieferdatum;
  private  $zuarchivieren;
  private  $internebezeichnung;
  private  $angelegtam;
  private  $preisanfrageid;
  private  $sprache;
  private  $kundennummerlieferant;
  private  $ohne_artikeltext;
  private  $langeartikelnummern;
  private  $abweichendebezeichnung;
  private  $anzeigesteuer;
  private  $kostenstelle;
  private  $bodyzusatz;
  private  $lieferbedingung;
  private  $titel;
  private  $liefertitel;
  private  $skontobetrag;
  private  $skontoberechnet;
  private  $bundesstaat;
  private  $lieferbundesstaat;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `bestellung` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->datum=$result['datum'];
    $this->projekt=$result['projekt'];
    $this->bestellungsart=$result['bestellungsart'];
    $this->belegnr=$result['belegnr'];
    $this->bearbeiter=$result['bearbeiter'];
    $this->angebot=$result['angebot'];
    $this->freitext=$result['freitext'];
    $this->internebemerkung=$result['internebemerkung'];
    $this->status=$result['status'];
    $this->adresse=$result['adresse'];
    $this->name=$result['name'];
    $this->vorname=$result['vorname'];
    $this->abteilung=$result['abteilung'];
    $this->unterabteilung=$result['unterabteilung'];
    $this->strasse=$result['strasse'];
    $this->adresszusatz=$result['adresszusatz'];
    $this->plz=$result['plz'];
    $this->ort=$result['ort'];
    $this->land=$result['land'];
    $this->abweichendelieferadresse=$result['abweichendelieferadresse'];
    $this->liefername=$result['liefername'];
    $this->lieferabteilung=$result['lieferabteilung'];
    $this->lieferunterabteilung=$result['lieferunterabteilung'];
    $this->lieferland=$result['lieferland'];
    $this->lieferstrasse=$result['lieferstrasse'];
    $this->lieferort=$result['lieferort'];
    $this->lieferplz=$result['lieferplz'];
    $this->lieferadresszusatz=$result['lieferadresszusatz'];
    $this->lieferansprechpartner=$result['lieferansprechpartner'];
    $this->ustid=$result['ustid'];
    $this->ust_befreit=$result['ust_befreit'];
    $this->email=$result['email'];
    $this->telefon=$result['telefon'];
    $this->telefax=$result['telefax'];
    $this->betreff=$result['betreff'];
    $this->kundennummer=$result['kundennummer'];
    $this->lieferantennummer=$result['lieferantennummer'];
    $this->versandart=$result['versandart'];
    $this->lieferdatum=$result['lieferdatum'];
    $this->einkaeufer=$result['einkaeufer'];
    $this->keineartikelnummern=$result['keineartikelnummern'];
    $this->zahlungsweise=$result['zahlungsweise'];
    $this->zahlungsstatus=$result['zahlungsstatus'];
    $this->zahlungszieltage=$result['zahlungszieltage'];
    $this->zahlungszieltageskonto=$result['zahlungszieltageskonto'];
    $this->zahlungszielskonto=$result['zahlungszielskonto'];
    $this->gesamtsumme=$result['gesamtsumme'];
    $this->bank_inhaber=$result['bank_inhaber'];
    $this->bank_institut=$result['bank_institut'];
    $this->bank_blz=$result['bank_blz'];
    $this->bank_konto=$result['bank_konto'];
    $this->paypalaccount=$result['paypalaccount'];
    $this->bestellbestaetigung=$result['bestellbestaetigung'];
    $this->firma=$result['firma'];
    $this->versendet=$result['versendet'];
    $this->versendet_am=$result['versendet_am'];
    $this->versendet_per=$result['versendet_per'];
    $this->versendet_durch=$result['versendet_durch'];
    $this->logdatei=$result['logdatei'];
    $this->artikelnummerninfotext=$result['artikelnummerninfotext'];
    $this->ansprechpartner=$result['ansprechpartner'];
    $this->anschreiben=$result['anschreiben'];
    $this->usereditid=$result['usereditid'];
    $this->useredittimestamp=$result['useredittimestamp'];
    $this->steuersatz_normal=$result['steuersatz_normal'];
    $this->steuersatz_zwischen=$result['steuersatz_zwischen'];
    $this->steuersatz_ermaessigt=$result['steuersatz_ermaessigt'];
    $this->steuersatz_starkermaessigt=$result['steuersatz_starkermaessigt'];
    $this->steuersatz_dienstleistung=$result['steuersatz_dienstleistung'];
    $this->waehrung=$result['waehrung'];
    $this->bestellungohnepreis=$result['bestellungohnepreis'];
    $this->schreibschutz=$result['schreibschutz'];
    $this->pdfarchiviert=$result['pdfarchiviert'];
    $this->pdfarchiviertversion=$result['pdfarchiviertversion'];
    $this->typ=$result['typ'];
    $this->verbindlichkeiteninfo=$result['verbindlichkeiteninfo'];
    $this->ohne_briefpapier=$result['ohne_briefpapier'];
    $this->projektfiliale=$result['projektfiliale'];
    $this->bestellung_bestaetigt=$result['bestellung_bestaetigt'];
    $this->bestaetigteslieferdatum=$result['bestaetigteslieferdatum'];
    $this->bestellungbestaetigtper=$result['bestellungbestaetigtper'];
    $this->bestellungbestaetigtabnummer=$result['bestellungbestaetigtabnummer'];
    $this->gewuenschteslieferdatum=$result['gewuenschteslieferdatum'];
    $this->zuarchivieren=$result['zuarchivieren'];
    $this->internebezeichnung=$result['internebezeichnung'];
    $this->angelegtam=$result['angelegtam'];
    $this->preisanfrageid=$result['preisanfrageid'];
    $this->sprache=$result['sprache'];
    $this->kundennummerlieferant=$result['kundennummerlieferant'];
    $this->ohne_artikeltext=$result['ohne_artikeltext'];
    $this->langeartikelnummern=$result['langeartikelnummern'];
    $this->abweichendebezeichnung=$result['abweichendebezeichnung'];
    $this->anzeigesteuer=$result['anzeigesteuer'];
    $this->kostenstelle=$result['kostenstelle'];
    $this->bodyzusatz=$result['bodyzusatz'];
    $this->lieferbedingung=$result['lieferbedingung'];
    $this->titel=$result['titel'];
    $this->liefertitel=$result['liefertitel'];
    $this->skontobetrag=$result['skontobetrag'];
    $this->skontoberechnet=$result['skontoberechnet'];
    $this->bundesstaat=$result['bundesstaat'];
    $this->lieferbundesstaat=$result['lieferbundesstaat'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `bestellung` (`id`,`datum`,`projekt`,`bestellungsart`,`belegnr`,`bearbeiter`,`angebot`,`freitext`,`internebemerkung`,`status`,`adresse`,`name`,`vorname`,`abteilung`,`unterabteilung`,`strasse`,`adresszusatz`,`plz`,`ort`,`land`,`abweichendelieferadresse`,`liefername`,`lieferabteilung`,`lieferunterabteilung`,`lieferland`,`lieferstrasse`,`lieferort`,`lieferplz`,`lieferadresszusatz`,`lieferansprechpartner`,`ustid`,`ust_befreit`,`email`,`telefon`,`telefax`,`betreff`,`kundennummer`,`lieferantennummer`,`versandart`,`lieferdatum`,`einkaeufer`,`keineartikelnummern`,`zahlungsweise`,`zahlungsstatus`,`zahlungszieltage`,`zahlungszieltageskonto`,`zahlungszielskonto`,`gesamtsumme`,`bank_inhaber`,`bank_institut`,`bank_blz`,`bank_konto`,`paypalaccount`,`bestellbestaetigung`,`firma`,`versendet`,`versendet_am`,`versendet_per`,`versendet_durch`,`logdatei`,`artikelnummerninfotext`,`ansprechpartner`,`anschreiben`,`usereditid`,`useredittimestamp`,`steuersatz_normal`,`steuersatz_zwischen`,`steuersatz_ermaessigt`,`steuersatz_starkermaessigt`,`steuersatz_dienstleistung`,`waehrung`,`bestellungohnepreis`,`schreibschutz`,`pdfarchiviert`,`pdfarchiviertversion`,`typ`,`verbindlichkeiteninfo`,`ohne_briefpapier`,`projektfiliale`,`bestellung_bestaetigt`,`bestaetigteslieferdatum`,`bestellungbestaetigtper`,`bestellungbestaetigtabnummer`,`gewuenschteslieferdatum`,`zuarchivieren`,`internebezeichnung`,`angelegtam`,`preisanfrageid`,`sprache`,`kundennummerlieferant`,`ohne_artikeltext`,`langeartikelnummern`,`abweichendebezeichnung`,`anzeigesteuer`,`kostenstelle`,`bodyzusatz`,`lieferbedingung`,`titel`,`liefertitel`,`skontobetrag`,`skontoberechnet`,`bundesstaat`,`lieferbundesstaat`)
      VALUES(NULL,'{$this->datum}','{$this->projekt}','{$this->bestellungsart}','{$this->belegnr}','{$this->bearbeiter}','{$this->angebot}','{$this->freitext}','{$this->internebemerkung}','{$this->status}','{$this->adresse}','{$this->name}','{$this->vorname}','{$this->abteilung}','{$this->unterabteilung}','{$this->strasse}','{$this->adresszusatz}','{$this->plz}','{$this->ort}','{$this->land}','{$this->abweichendelieferadresse}','{$this->liefername}','{$this->lieferabteilung}','{$this->lieferunterabteilung}','{$this->lieferland}','{$this->lieferstrasse}','{$this->lieferort}','{$this->lieferplz}','{$this->lieferadresszusatz}','{$this->lieferansprechpartner}','{$this->ustid}','{$this->ust_befreit}','{$this->email}','{$this->telefon}','{$this->telefax}','{$this->betreff}','{$this->kundennummer}','{$this->lieferantennummer}','{$this->versandart}','{$this->lieferdatum}','{$this->einkaeufer}','{$this->keineartikelnummern}','{$this->zahlungsweise}','{$this->zahlungsstatus}','{$this->zahlungszieltage}','{$this->zahlungszieltageskonto}','{$this->zahlungszielskonto}','{$this->gesamtsumme}','{$this->bank_inhaber}','{$this->bank_institut}','{$this->bank_blz}','{$this->bank_konto}','{$this->paypalaccount}','{$this->bestellbestaetigung}','{$this->firma}','{$this->versendet}','{$this->versendet_am}','{$this->versendet_per}','{$this->versendet_durch}','{$this->logdatei}','{$this->artikelnummerninfotext}','{$this->ansprechpartner}','{$this->anschreiben}','{$this->usereditid}','{$this->useredittimestamp}','{$this->steuersatz_normal}','{$this->steuersatz_zwischen}','{$this->steuersatz_ermaessigt}','{$this->steuersatz_starkermaessigt}','{$this->steuersatz_dienstleistung}','{$this->waehrung}','{$this->bestellungohnepreis}','{$this->schreibschutz}','{$this->pdfarchiviert}','{$this->pdfarchiviertversion}','{$this->typ}','{$this->verbindlichkeiteninfo}','{$this->ohne_briefpapier}','{$this->projektfiliale}','{$this->bestellung_bestaetigt}','{$this->bestaetigteslieferdatum}','{$this->bestellungbestaetigtper}','{$this->bestellungbestaetigtabnummer}','{$this->gewuenschteslieferdatum}','{$this->zuarchivieren}','{$this->internebezeichnung}','{$this->angelegtam}','{$this->preisanfrageid}','{$this->sprache}','{$this->kundennummerlieferant}','{$this->ohne_artikeltext}','{$this->langeartikelnummern}','{$this->abweichendebezeichnung}','{$this->anzeigesteuer}','{$this->kostenstelle}','{$this->bodyzusatz}','{$this->lieferbedingung}','{$this->titel}','{$this->liefertitel}','{$this->skontobetrag}','{$this->skontoberechnet}','{$this->bundesstaat}','{$this->lieferbundesstaat}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `bestellung` SET
      `datum`='{$this->datum}',
      `projekt`='{$this->projekt}',
      `bestellungsart`='{$this->bestellungsart}',
      `belegnr`='{$this->belegnr}',
      `bearbeiter`='{$this->bearbeiter}',
      `angebot`='{$this->angebot}',
      `freitext`='{$this->freitext}',
      `internebemerkung`='{$this->internebemerkung}',
      `status`='{$this->status}',
      `adresse`='{$this->adresse}',
      `name`='{$this->name}',
      `vorname`='{$this->vorname}',
      `abteilung`='{$this->abteilung}',
      `unterabteilung`='{$this->unterabteilung}',
      `strasse`='{$this->strasse}',
      `adresszusatz`='{$this->adresszusatz}',
      `plz`='{$this->plz}',
      `ort`='{$this->ort}',
      `land`='{$this->land}',
      `abweichendelieferadresse`='{$this->abweichendelieferadresse}',
      `liefername`='{$this->liefername}',
      `lieferabteilung`='{$this->lieferabteilung}',
      `lieferunterabteilung`='{$this->lieferunterabteilung}',
      `lieferland`='{$this->lieferland}',
      `lieferstrasse`='{$this->lieferstrasse}',
      `lieferort`='{$this->lieferort}',
      `lieferplz`='{$this->lieferplz}',
      `lieferadresszusatz`='{$this->lieferadresszusatz}',
      `lieferansprechpartner`='{$this->lieferansprechpartner}',
      `ustid`='{$this->ustid}',
      `ust_befreit`='{$this->ust_befreit}',
      `email`='{$this->email}',
      `telefon`='{$this->telefon}',
      `telefax`='{$this->telefax}',
      `betreff`='{$this->betreff}',
      `kundennummer`='{$this->kundennummer}',
      `lieferantennummer`='{$this->lieferantennummer}',
      `versandart`='{$this->versandart}',
      `lieferdatum`='{$this->lieferdatum}',
      `einkaeufer`='{$this->einkaeufer}',
      `keineartikelnummern`='{$this->keineartikelnummern}',
      `zahlungsweise`='{$this->zahlungsweise}',
      `zahlungsstatus`='{$this->zahlungsstatus}',
      `zahlungszieltage`='{$this->zahlungszieltage}',
      `zahlungszieltageskonto`='{$this->zahlungszieltageskonto}',
      `zahlungszielskonto`='{$this->zahlungszielskonto}',
      `gesamtsumme`='{$this->gesamtsumme}',
      `bank_inhaber`='{$this->bank_inhaber}',
      `bank_institut`='{$this->bank_institut}',
      `bank_blz`='{$this->bank_blz}',
      `bank_konto`='{$this->bank_konto}',
      `paypalaccount`='{$this->paypalaccount}',
      `bestellbestaetigung`='{$this->bestellbestaetigung}',
      `firma`='{$this->firma}',
      `versendet`='{$this->versendet}',
      `versendet_am`='{$this->versendet_am}',
      `versendet_per`='{$this->versendet_per}',
      `versendet_durch`='{$this->versendet_durch}',
      `logdatei`='{$this->logdatei}',
      `artikelnummerninfotext`='{$this->artikelnummerninfotext}',
      `ansprechpartner`='{$this->ansprechpartner}',
      `anschreiben`='{$this->anschreiben}',
      `usereditid`='{$this->usereditid}',
      `useredittimestamp`='{$this->useredittimestamp}',
      `steuersatz_normal`='{$this->steuersatz_normal}',
      `steuersatz_zwischen`='{$this->steuersatz_zwischen}',
      `steuersatz_ermaessigt`='{$this->steuersatz_ermaessigt}',
      `steuersatz_starkermaessigt`='{$this->steuersatz_starkermaessigt}',
      `steuersatz_dienstleistung`='{$this->steuersatz_dienstleistung}',
      `waehrung`='{$this->waehrung}',
      `bestellungohnepreis`='{$this->bestellungohnepreis}',
      `schreibschutz`='{$this->schreibschutz}',
      `pdfarchiviert`='{$this->pdfarchiviert}',
      `pdfarchiviertversion`='{$this->pdfarchiviertversion}',
      `typ`='{$this->typ}',
      `verbindlichkeiteninfo`='{$this->verbindlichkeiteninfo}',
      `ohne_briefpapier`='{$this->ohne_briefpapier}',
      `projektfiliale`='{$this->projektfiliale}',
      `bestellung_bestaetigt`='{$this->bestellung_bestaetigt}',
      `bestaetigteslieferdatum`='{$this->bestaetigteslieferdatum}',
      `bestellungbestaetigtper`='{$this->bestellungbestaetigtper}',
      `bestellungbestaetigtabnummer`='{$this->bestellungbestaetigtabnummer}',
      `gewuenschteslieferdatum`='{$this->gewuenschteslieferdatum}',
      `zuarchivieren`='{$this->zuarchivieren}',
      `internebezeichnung`='{$this->internebezeichnung}',
      `angelegtam`='{$this->angelegtam}',
      `preisanfrageid`='{$this->preisanfrageid}',
      `sprache`='{$this->sprache}',
      `kundennummerlieferant`='{$this->kundennummerlieferant}',
      `ohne_artikeltext`='{$this->ohne_artikeltext}',
      `langeartikelnummern`='{$this->langeartikelnummern}',
      `abweichendebezeichnung`='{$this->abweichendebezeichnung}',
      `anzeigesteuer`='{$this->anzeigesteuer}',
      `kostenstelle`='{$this->kostenstelle}',
      `bodyzusatz`='{$this->bodyzusatz}',
      `lieferbedingung`='{$this->lieferbedingung}',
      `titel`='{$this->titel}',
      `liefertitel`='{$this->liefertitel}',
      `skontobetrag`='{$this->skontobetrag}',
      `skontoberechnet`='{$this->skontoberechnet}',
      `bundesstaat`='{$this->bundesstaat}',
      `lieferbundesstaat`='{$this->lieferbundesstaat}'
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

    $sql = "DELETE FROM `bestellung` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->datum='';
    $this->projekt='';
    $this->bestellungsart='';
    $this->belegnr='';
    $this->bearbeiter='';
    $this->angebot='';
    $this->freitext='';
    $this->internebemerkung='';
    $this->status='';
    $this->adresse='';
    $this->name='';
    $this->vorname='';
    $this->abteilung='';
    $this->unterabteilung='';
    $this->strasse='';
    $this->adresszusatz='';
    $this->plz='';
    $this->ort='';
    $this->land='';
    $this->abweichendelieferadresse='';
    $this->liefername='';
    $this->lieferabteilung='';
    $this->lieferunterabteilung='';
    $this->lieferland='';
    $this->lieferstrasse='';
    $this->lieferort='';
    $this->lieferplz='';
    $this->lieferadresszusatz='';
    $this->lieferansprechpartner='';
    $this->ustid='';
    $this->ust_befreit='';
    $this->email='';
    $this->telefon='';
    $this->telefax='';
    $this->betreff='';
    $this->kundennummer='';
    $this->lieferantennummer='';
    $this->versandart='';
    $this->lieferdatum='';
    $this->einkaeufer='';
    $this->keineartikelnummern='';
    $this->zahlungsweise='';
    $this->zahlungsstatus='';
    $this->zahlungszieltage='';
    $this->zahlungszieltageskonto='';
    $this->zahlungszielskonto='';
    $this->gesamtsumme='';
    $this->bank_inhaber='';
    $this->bank_institut='';
    $this->bank_blz='';
    $this->bank_konto='';
    $this->paypalaccount='';
    $this->bestellbestaetigung='';
    $this->firma='';
    $this->versendet='';
    $this->versendet_am='';
    $this->versendet_per='';
    $this->versendet_durch='';
    $this->logdatei='';
    $this->artikelnummerninfotext='';
    $this->ansprechpartner='';
    $this->anschreiben='';
    $this->usereditid='';
    $this->useredittimestamp='';
    $this->steuersatz_normal='';
    $this->steuersatz_zwischen='';
    $this->steuersatz_ermaessigt='';
    $this->steuersatz_starkermaessigt='';
    $this->steuersatz_dienstleistung='';
    $this->waehrung='';
    $this->bestellungohnepreis='';
    $this->schreibschutz='';
    $this->pdfarchiviert='';
    $this->pdfarchiviertversion='';
    $this->typ='';
    $this->verbindlichkeiteninfo='';
    $this->ohne_briefpapier='';
    $this->projektfiliale='';
    $this->bestellung_bestaetigt='';
    $this->bestaetigteslieferdatum='';
    $this->bestellungbestaetigtper='';
    $this->bestellungbestaetigtabnummer='';
    $this->gewuenschteslieferdatum='';
    $this->zuarchivieren='';
    $this->internebezeichnung='';
    $this->angelegtam='';
    $this->preisanfrageid='';
    $this->sprache='';
    $this->kundennummerlieferant='';
    $this->ohne_artikeltext='';
    $this->langeartikelnummern='';
    $this->abweichendebezeichnung='';
    $this->anzeigesteuer='';
    $this->kostenstelle='';
    $this->bodyzusatz='';
    $this->lieferbedingung='';
    $this->titel='';
    $this->liefertitel='';
    $this->skontobetrag='';
    $this->skontoberechnet='';
    $this->bundesstaat='';
    $this->lieferbundesstaat='';
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
  public function SetBestellungsart($value) { $this->bestellungsart=$value; }
  public function GetBestellungsart() { return $this->bestellungsart; }
  public function SetBelegnr($value) { $this->belegnr=$value; }
  public function GetBelegnr() { return $this->belegnr; }
  public function SetBearbeiter($value) { $this->bearbeiter=$value; }
  public function GetBearbeiter() { return $this->bearbeiter; }
  public function SetAngebot($value) { $this->angebot=$value; }
  public function GetAngebot() { return $this->angebot; }
  public function SetFreitext($value) { $this->freitext=$value; }
  public function GetFreitext() { return $this->freitext; }
  public function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  public function GetInternebemerkung() { return $this->internebemerkung; }
  public function SetStatus($value) { $this->status=$value; }
  public function GetStatus() { return $this->status; }
  public function SetAdresse($value) { $this->adresse=$value; }
  public function GetAdresse() { return $this->adresse; }
  public function SetName($value) { $this->name=$value; }
  public function GetName() { return $this->name; }
  public function SetVorname($value) { $this->vorname=$value; }
  public function GetVorname() { return $this->vorname; }
  public function SetAbteilung($value) { $this->abteilung=$value; }
  public function GetAbteilung() { return $this->abteilung; }
  public function SetUnterabteilung($value) { $this->unterabteilung=$value; }
  public function GetUnterabteilung() { return $this->unterabteilung; }
  public function SetStrasse($value) { $this->strasse=$value; }
  public function GetStrasse() { return $this->strasse; }
  public function SetAdresszusatz($value) { $this->adresszusatz=$value; }
  public function GetAdresszusatz() { return $this->adresszusatz; }
  public function SetPlz($value) { $this->plz=$value; }
  public function GetPlz() { return $this->plz; }
  public function SetOrt($value) { $this->ort=$value; }
  public function GetOrt() { return $this->ort; }
  public function SetLand($value) { $this->land=$value; }
  public function GetLand() { return $this->land; }
  public function SetAbweichendelieferadresse($value) { $this->abweichendelieferadresse=$value; }
  public function GetAbweichendelieferadresse() { return $this->abweichendelieferadresse; }
  public function SetLiefername($value) { $this->liefername=$value; }
  public function GetLiefername() { return $this->liefername; }
  public function SetLieferabteilung($value) { $this->lieferabteilung=$value; }
  public function GetLieferabteilung() { return $this->lieferabteilung; }
  public function SetLieferunterabteilung($value) { $this->lieferunterabteilung=$value; }
  public function GetLieferunterabteilung() { return $this->lieferunterabteilung; }
  public function SetLieferland($value) { $this->lieferland=$value; }
  public function GetLieferland() { return $this->lieferland; }
  public function SetLieferstrasse($value) { $this->lieferstrasse=$value; }
  public function GetLieferstrasse() { return $this->lieferstrasse; }
  public function SetLieferort($value) { $this->lieferort=$value; }
  public function GetLieferort() { return $this->lieferort; }
  public function SetLieferplz($value) { $this->lieferplz=$value; }
  public function GetLieferplz() { return $this->lieferplz; }
  public function SetLieferadresszusatz($value) { $this->lieferadresszusatz=$value; }
  public function GetLieferadresszusatz() { return $this->lieferadresszusatz; }
  public function SetLieferansprechpartner($value) { $this->lieferansprechpartner=$value; }
  public function GetLieferansprechpartner() { return $this->lieferansprechpartner; }
  public function SetUstid($value) { $this->ustid=$value; }
  public function GetUstid() { return $this->ustid; }
  public function SetUst_Befreit($value) { $this->ust_befreit=$value; }
  public function GetUst_Befreit() { return $this->ust_befreit; }
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
  public function SetLieferantennummer($value) { $this->lieferantennummer=$value; }
  public function GetLieferantennummer() { return $this->lieferantennummer; }
  public function SetVersandart($value) { $this->versandart=$value; }
  public function GetVersandart() { return $this->versandart; }
  public function SetLieferdatum($value) { $this->lieferdatum=$value; }
  public function GetLieferdatum() { return $this->lieferdatum; }
  public function SetEinkaeufer($value) { $this->einkaeufer=$value; }
  public function GetEinkaeufer() { return $this->einkaeufer; }
  public function SetKeineartikelnummern($value) { $this->keineartikelnummern=$value; }
  public function GetKeineartikelnummern() { return $this->keineartikelnummern; }
  public function SetZahlungsweise($value) { $this->zahlungsweise=$value; }
  public function GetZahlungsweise() { return $this->zahlungsweise; }
  public function SetZahlungsstatus($value) { $this->zahlungsstatus=$value; }
  public function GetZahlungsstatus() { return $this->zahlungsstatus; }
  public function SetZahlungszieltage($value) { $this->zahlungszieltage=$value; }
  public function GetZahlungszieltage() { return $this->zahlungszieltage; }
  public function SetZahlungszieltageskonto($value) { $this->zahlungszieltageskonto=$value; }
  public function GetZahlungszieltageskonto() { return $this->zahlungszieltageskonto; }
  public function SetZahlungszielskonto($value) { $this->zahlungszielskonto=$value; }
  public function GetZahlungszielskonto() { return $this->zahlungszielskonto; }
  public function SetGesamtsumme($value) { $this->gesamtsumme=$value; }
  public function GetGesamtsumme() { return $this->gesamtsumme; }
  public function SetBank_Inhaber($value) { $this->bank_inhaber=$value; }
  public function GetBank_Inhaber() { return $this->bank_inhaber; }
  public function SetBank_Institut($value) { $this->bank_institut=$value; }
  public function GetBank_Institut() { return $this->bank_institut; }
  public function SetBank_Blz($value) { $this->bank_blz=$value; }
  public function GetBank_Blz() { return $this->bank_blz; }
  public function SetBank_Konto($value) { $this->bank_konto=$value; }
  public function GetBank_Konto() { return $this->bank_konto; }
  public function SetPaypalaccount($value) { $this->paypalaccount=$value; }
  public function GetPaypalaccount() { return $this->paypalaccount; }
  public function SetBestellbestaetigung($value) { $this->bestellbestaetigung=$value; }
  public function GetBestellbestaetigung() { return $this->bestellbestaetigung; }
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
  public function SetLogdatei($value) { $this->logdatei=$value; }
  public function GetLogdatei() { return $this->logdatei; }
  public function SetArtikelnummerninfotext($value) { $this->artikelnummerninfotext=$value; }
  public function GetArtikelnummerninfotext() { return $this->artikelnummerninfotext; }
  public function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  public function GetAnsprechpartner() { return $this->ansprechpartner; }
  public function SetAnschreiben($value) { $this->anschreiben=$value; }
  public function GetAnschreiben() { return $this->anschreiben; }
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
  public function SetBestellungohnepreis($value) { $this->bestellungohnepreis=$value; }
  public function GetBestellungohnepreis() { return $this->bestellungohnepreis; }
  public function SetSchreibschutz($value) { $this->schreibschutz=$value; }
  public function GetSchreibschutz() { return $this->schreibschutz; }
  public function SetPdfarchiviert($value) { $this->pdfarchiviert=$value; }
  public function GetPdfarchiviert() { return $this->pdfarchiviert; }
  public function SetPdfarchiviertversion($value) { $this->pdfarchiviertversion=$value; }
  public function GetPdfarchiviertversion() { return $this->pdfarchiviertversion; }
  public function SetTyp($value) { $this->typ=$value; }
  public function GetTyp() { return $this->typ; }
  public function SetVerbindlichkeiteninfo($value) { $this->verbindlichkeiteninfo=$value; }
  public function GetVerbindlichkeiteninfo() { return $this->verbindlichkeiteninfo; }
  public function SetOhne_Briefpapier($value) { $this->ohne_briefpapier=$value; }
  public function GetOhne_Briefpapier() { return $this->ohne_briefpapier; }
  public function SetProjektfiliale($value) { $this->projektfiliale=$value; }
  public function GetProjektfiliale() { return $this->projektfiliale; }
  public function SetBestellung_Bestaetigt($value) { $this->bestellung_bestaetigt=$value; }
  public function GetBestellung_Bestaetigt() { return $this->bestellung_bestaetigt; }
  public function SetBestaetigteslieferdatum($value) { $this->bestaetigteslieferdatum=$value; }
  public function GetBestaetigteslieferdatum() { return $this->bestaetigteslieferdatum; }
  public function SetBestellungbestaetigtper($value) { $this->bestellungbestaetigtper=$value; }
  public function GetBestellungbestaetigtper() { return $this->bestellungbestaetigtper; }
  public function SetBestellungbestaetigtabnummer($value) { $this->bestellungbestaetigtabnummer=$value; }
  public function GetBestellungbestaetigtabnummer() { return $this->bestellungbestaetigtabnummer; }
  public function SetGewuenschteslieferdatum($value) { $this->gewuenschteslieferdatum=$value; }
  public function GetGewuenschteslieferdatum() { return $this->gewuenschteslieferdatum; }
  public function SetZuarchivieren($value) { $this->zuarchivieren=$value; }
  public function GetZuarchivieren() { return $this->zuarchivieren; }
  public function SetInternebezeichnung($value) { $this->internebezeichnung=$value; }
  public function GetInternebezeichnung() { return $this->internebezeichnung; }
  public function SetAngelegtam($value) { $this->angelegtam=$value; }
  public function GetAngelegtam() { return $this->angelegtam; }
  public function SetPreisanfrageid($value) { $this->preisanfrageid=$value; }
  public function GetPreisanfrageid() { return $this->preisanfrageid; }
  public function SetSprache($value) { $this->sprache=$value; }
  public function GetSprache() { return $this->sprache; }
  public function SetKundennummerlieferant($value) { $this->kundennummerlieferant=$value; }
  public function GetKundennummerlieferant() { return $this->kundennummerlieferant; }
  public function SetOhne_Artikeltext($value) { $this->ohne_artikeltext=$value; }
  public function GetOhne_Artikeltext() { return $this->ohne_artikeltext; }
  public function SetLangeartikelnummern($value) { $this->langeartikelnummern=$value; }
  public function GetLangeartikelnummern() { return $this->langeartikelnummern; }
  public function SetAbweichendebezeichnung($value) { $this->abweichendebezeichnung=$value; }
  public function GetAbweichendebezeichnung() { return $this->abweichendebezeichnung; }
  public function SetAnzeigesteuer($value) { $this->anzeigesteuer=$value; }
  public function GetAnzeigesteuer() { return $this->anzeigesteuer; }
  public function SetKostenstelle($value) { $this->kostenstelle=$value; }
  public function GetKostenstelle() { return $this->kostenstelle; }
  public function SetBodyzusatz($value) { $this->bodyzusatz=$value; }
  public function GetBodyzusatz() { return $this->bodyzusatz; }
  public function SetLieferbedingung($value) { $this->lieferbedingung=$value; }
  public function GetLieferbedingung() { return $this->lieferbedingung; }
  public function SetTitel($value) { $this->titel=$value; }
  public function GetTitel() { return $this->titel; }
  public function SetLiefertitel($value) { $this->liefertitel=$value; }
  public function GetLiefertitel() { return $this->liefertitel; }
  public function SetSkontobetrag($value) { $this->skontobetrag=$value; }
  public function GetSkontobetrag() { return $this->skontobetrag; }
  public function SetSkontoberechnet($value) { $this->skontoberechnet=$value; }
  public function GetSkontoberechnet() { return $this->skontoberechnet; }
  public function SetBundesstaat($value) { $this->bundesstaat=$value; }
  public function GetBundesstaat() { return $this->bundesstaat; }
  public function SetLieferbundesstaat($value) { $this->lieferbundesstaat=$value; }
  public function GetLieferbundesstaat() { return $this->lieferbundesstaat; }

}
