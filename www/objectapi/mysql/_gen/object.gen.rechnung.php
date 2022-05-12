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

class ObjGenRechnung
{

  private  $id;
  private  $datum;
  private  $aborechnung;
  private  $projekt;
  private  $anlegeart;
  private  $belegnr;
  private  $auftrag;
  private  $auftragid;
  private  $bearbeiter;
  private  $freitext;
  private  $internebemerkung;
  private  $status;
  private  $adresse;
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
  private  $ust_befreit;
  private  $ustbrief;
  private  $ustbrief_eingang;
  private  $ustbrief_eingang_am;
  private  $email;
  private  $telefon;
  private  $telefax;
  private  $betreff;
  private  $kundennummer;
  private  $lieferschein;
  private  $versandart;
  private  $lieferdatum;
  private  $buchhaltung;
  private  $zahlungsweise;
  private  $zahlungsstatus;
  private  $ist;
  private  $soll;
  private  $skonto_gegeben;
  private  $zahlungszieltage;
  private  $zahlungszieltageskonto;
  private  $zahlungszielskonto;
  private  $firma;
  private  $versendet;
  private  $versendet_am;
  private  $versendet_per;
  private  $versendet_durch;
  private  $versendet_mahnwesen;
  private  $mahnwesen;
  private  $mahnwesen_datum;
  private  $mahnwesen_gesperrt;
  private  $mahnwesen_internebemerkung;
  private  $inbearbeitung;
  private  $datev_abgeschlossen;
  private  $logdatei;
  private  $doppel;
  private  $autodruck_rz;
  private  $autodruck_periode;
  private  $autodruck_done;
  private  $autodruck_anzahlverband;
  private  $autodruck_anzahlkunde;
  private  $autodruck_mailverband;
  private  $autodruck_mailkunde;
  private  $dta_datei_verband;
  private  $dta_datei;
  private  $deckungsbeitragcalc;
  private  $deckungsbeitrag;
  private  $umsatz_netto;
  private  $erloes_netto;
  private  $mahnwesenfestsetzen;
  private  $vertriebid;
  private  $aktion;
  private  $vertrieb;
  private  $provision;
  private  $provision_summe;
  private  $gruppe;
  private  $punkte;
  private  $bonuspunkte;
  private  $provdatum;
  private  $ihrebestellnummer;
  private  $anschreiben;
  private  $usereditid;
  private  $useredittimestamp;
  private  $realrabatt;
  private  $rabatt;
  private  $einzugsdatum;
  private  $rabatt1;
  private  $rabatt2;
  private  $rabatt3;
  private  $rabatt4;
  private  $rabatt5;
  private  $forderungsverlust_datum;
  private  $forderungsverlust_betrag;
  private  $steuersatz_normal;
  private  $steuersatz_zwischen;
  private  $steuersatz_ermaessigt;
  private  $steuersatz_starkermaessigt;
  private  $steuersatz_dienstleistung;
  private  $waehrung;
  private  $keinsteuersatz;
  private  $schreibschutz;
  private  $pdfarchiviert;
  private  $pdfarchiviertversion;
  private  $typ;
  private  $ohne_briefpapier;
  private  $lieferid;
  private  $ansprechpartnerid;
  private  $systemfreitext;
  private  $projektfiliale;
  private  $zuarchivieren;
  private  $internebezeichnung;
  private  $angelegtam;
  private  $abweichendebezeichnung;
  private  $bezahlt_am;
  private  $sprache;
  private  $bundesland;
  private  $gln;
  private  $deliverythresholdvatid;
  private  $bearbeiterid;
  private  $kurs;
  private  $ohne_artikeltext;
  private  $anzeigesteuer;
  private  $kostenstelle;
  private  $bodyzusatz;
  private  $lieferbedingung;
  private  $titel;
  private  $skontobetrag;
  private  $skontoberechnet;
  private  $extsoll;
  private  $teilstorno;
  private  $bundesstaat;
  private  $kundennummer_buchhaltung;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `rechnung` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->datum=$result['datum'];
    $this->aborechnung=$result['aborechnung'];
    $this->projekt=$result['projekt'];
    $this->anlegeart=$result['anlegeart'];
    $this->belegnr=$result['belegnr'];
    $this->auftrag=$result['auftrag'];
    $this->auftragid=$result['auftragid'];
    $this->bearbeiter=$result['bearbeiter'];
    $this->freitext=$result['freitext'];
    $this->internebemerkung=$result['internebemerkung'];
    $this->status=$result['status'];
    $this->adresse=$result['adresse'];
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
    $this->ust_befreit=$result['ust_befreit'];
    $this->ustbrief=$result['ustbrief'];
    $this->ustbrief_eingang=$result['ustbrief_eingang'];
    $this->ustbrief_eingang_am=$result['ustbrief_eingang_am'];
    $this->email=$result['email'];
    $this->telefon=$result['telefon'];
    $this->telefax=$result['telefax'];
    $this->betreff=$result['betreff'];
    $this->kundennummer=$result['kundennummer'];
    $this->lieferschein=$result['lieferschein'];
    $this->versandart=$result['versandart'];
    $this->lieferdatum=$result['lieferdatum'];
    $this->buchhaltung=$result['buchhaltung'];
    $this->zahlungsweise=$result['zahlungsweise'];
    $this->zahlungsstatus=$result['zahlungsstatus'];
    $this->ist=$result['ist'];
    $this->soll=$result['soll'];
    $this->skonto_gegeben=$result['skonto_gegeben'];
    $this->zahlungszieltage=$result['zahlungszieltage'];
    $this->zahlungszieltageskonto=$result['zahlungszieltageskonto'];
    $this->zahlungszielskonto=$result['zahlungszielskonto'];
    $this->firma=$result['firma'];
    $this->versendet=$result['versendet'];
    $this->versendet_am=$result['versendet_am'];
    $this->versendet_per=$result['versendet_per'];
    $this->versendet_durch=$result['versendet_durch'];
    $this->versendet_mahnwesen=$result['versendet_mahnwesen'];
    $this->mahnwesen=$result['mahnwesen'];
    $this->mahnwesen_datum=$result['mahnwesen_datum'];
    $this->mahnwesen_gesperrt=$result['mahnwesen_gesperrt'];
    $this->mahnwesen_internebemerkung=$result['mahnwesen_internebemerkung'];
    $this->inbearbeitung=$result['inbearbeitung'];
    $this->datev_abgeschlossen=$result['datev_abgeschlossen'];
    $this->logdatei=$result['logdatei'];
    $this->doppel=$result['doppel'];
    $this->autodruck_rz=$result['autodruck_rz'];
    $this->autodruck_periode=$result['autodruck_periode'];
    $this->autodruck_done=$result['autodruck_done'];
    $this->autodruck_anzahlverband=$result['autodruck_anzahlverband'];
    $this->autodruck_anzahlkunde=$result['autodruck_anzahlkunde'];
    $this->autodruck_mailverband=$result['autodruck_mailverband'];
    $this->autodruck_mailkunde=$result['autodruck_mailkunde'];
    $this->dta_datei_verband=$result['dta_datei_verband'];
    $this->dta_datei=$result['dta_datei'];
    $this->deckungsbeitragcalc=$result['deckungsbeitragcalc'];
    $this->deckungsbeitrag=$result['deckungsbeitrag'];
    $this->umsatz_netto=$result['umsatz_netto'];
    $this->erloes_netto=$result['erloes_netto'];
    $this->mahnwesenfestsetzen=$result['mahnwesenfestsetzen'];
    $this->vertriebid=$result['vertriebid'];
    $this->aktion=$result['aktion'];
    $this->vertrieb=$result['vertrieb'];
    $this->provision=$result['provision'];
    $this->provision_summe=$result['provision_summe'];
    $this->gruppe=$result['gruppe'];
    $this->punkte=$result['punkte'];
    $this->bonuspunkte=$result['bonuspunkte'];
    $this->provdatum=$result['provdatum'];
    $this->ihrebestellnummer=$result['ihrebestellnummer'];
    $this->anschreiben=$result['anschreiben'];
    $this->usereditid=$result['usereditid'];
    $this->useredittimestamp=$result['useredittimestamp'];
    $this->realrabatt=$result['realrabatt'];
    $this->rabatt=$result['rabatt'];
    $this->einzugsdatum=$result['einzugsdatum'];
    $this->rabatt1=$result['rabatt1'];
    $this->rabatt2=$result['rabatt2'];
    $this->rabatt3=$result['rabatt3'];
    $this->rabatt4=$result['rabatt4'];
    $this->rabatt5=$result['rabatt5'];
    $this->forderungsverlust_datum=$result['forderungsverlust_datum'];
    $this->forderungsverlust_betrag=$result['forderungsverlust_betrag'];
    $this->steuersatz_normal=$result['steuersatz_normal'];
    $this->steuersatz_zwischen=$result['steuersatz_zwischen'];
    $this->steuersatz_ermaessigt=$result['steuersatz_ermaessigt'];
    $this->steuersatz_starkermaessigt=$result['steuersatz_starkermaessigt'];
    $this->steuersatz_dienstleistung=$result['steuersatz_dienstleistung'];
    $this->waehrung=$result['waehrung'];
    $this->keinsteuersatz=$result['keinsteuersatz'];
    $this->schreibschutz=$result['schreibschutz'];
    $this->pdfarchiviert=$result['pdfarchiviert'];
    $this->pdfarchiviertversion=$result['pdfarchiviertversion'];
    $this->typ=$result['typ'];
    $this->ohne_briefpapier=$result['ohne_briefpapier'];
    $this->lieferid=$result['lieferid'];
    $this->ansprechpartnerid=$result['ansprechpartnerid'];
    $this->systemfreitext=$result['systemfreitext'];
    $this->projektfiliale=$result['projektfiliale'];
    $this->zuarchivieren=$result['zuarchivieren'];
    $this->internebezeichnung=$result['internebezeichnung'];
    $this->angelegtam=$result['angelegtam'];
    $this->abweichendebezeichnung=$result['abweichendebezeichnung'];
    $this->bezahlt_am=$result['bezahlt_am'];
    $this->sprache=$result['sprache'];
    $this->bundesland=$result['bundesland'];
    $this->gln=$result['gln'];
    $this->deliverythresholdvatid=$result['deliverythresholdvatid'];
    $this->bearbeiterid=$result['bearbeiterid'];
    $this->kurs=$result['kurs'];
    $this->ohne_artikeltext=$result['ohne_artikeltext'];
    $this->anzeigesteuer=$result['anzeigesteuer'];
    $this->kostenstelle=$result['kostenstelle'];
    $this->bodyzusatz=$result['bodyzusatz'];
    $this->lieferbedingung=$result['lieferbedingung'];
    $this->titel=$result['titel'];
    $this->skontobetrag=$result['skontobetrag'];
    $this->skontoberechnet=$result['skontoberechnet'];
    $this->extsoll=$result['extsoll'];
    $this->teilstorno=$result['teilstorno'];
    $this->bundesstaat=$result['bundesstaat'];
    $this->kundennummer_buchhaltung=$result['kundennummer_buchhaltung'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `rechnung` (`id`,`datum`,`aborechnung`,`projekt`,`anlegeart`,`belegnr`,`auftrag`,`auftragid`,`bearbeiter`,`freitext`,`internebemerkung`,`status`,`adresse`,`name`,`abteilung`,`unterabteilung`,`strasse`,`adresszusatz`,`ansprechpartner`,`plz`,`ort`,`land`,`ustid`,`ust_befreit`,`ustbrief`,`ustbrief_eingang`,`ustbrief_eingang_am`,`email`,`telefon`,`telefax`,`betreff`,`kundennummer`,`lieferschein`,`versandart`,`lieferdatum`,`buchhaltung`,`zahlungsweise`,`zahlungsstatus`,`ist`,`soll`,`skonto_gegeben`,`zahlungszieltage`,`zahlungszieltageskonto`,`zahlungszielskonto`,`firma`,`versendet`,`versendet_am`,`versendet_per`,`versendet_durch`,`versendet_mahnwesen`,`mahnwesen`,`mahnwesen_datum`,`mahnwesen_gesperrt`,`mahnwesen_internebemerkung`,`inbearbeitung`,`datev_abgeschlossen`,`logdatei`,`doppel`,`autodruck_rz`,`autodruck_periode`,`autodruck_done`,`autodruck_anzahlverband`,`autodruck_anzahlkunde`,`autodruck_mailverband`,`autodruck_mailkunde`,`dta_datei_verband`,`dta_datei`,`deckungsbeitragcalc`,`deckungsbeitrag`,`umsatz_netto`,`erloes_netto`,`mahnwesenfestsetzen`,`vertriebid`,`aktion`,`vertrieb`,`provision`,`provision_summe`,`gruppe`,`punkte`,`bonuspunkte`,`provdatum`,`ihrebestellnummer`,`anschreiben`,`usereditid`,`useredittimestamp`,`realrabatt`,`rabatt`,`einzugsdatum`,`rabatt1`,`rabatt2`,`rabatt3`,`rabatt4`,`rabatt5`,`forderungsverlust_datum`,`forderungsverlust_betrag`,`steuersatz_normal`,`steuersatz_zwischen`,`steuersatz_ermaessigt`,`steuersatz_starkermaessigt`,`steuersatz_dienstleistung`,`waehrung`,`keinsteuersatz`,`schreibschutz`,`pdfarchiviert`,`pdfarchiviertversion`,`typ`,`ohne_briefpapier`,`lieferid`,`ansprechpartnerid`,`systemfreitext`,`projektfiliale`,`zuarchivieren`,`internebezeichnung`,`angelegtam`,`abweichendebezeichnung`,`bezahlt_am`,`sprache`,`bundesland`,`gln`,`deliverythresholdvatid`,`bearbeiterid`,`kurs`,`ohne_artikeltext`,`anzeigesteuer`,`kostenstelle`,`bodyzusatz`,`lieferbedingung`,`titel`,`skontobetrag`,`skontoberechnet`,`extsoll`,`teilstorno`,`bundesstaat`,`kundennummer_buchhaltung`)
      VALUES(NULL,'{$this->datum}','{$this->aborechnung}','{$this->projekt}','{$this->anlegeart}','{$this->belegnr}','{$this->auftrag}','{$this->auftragid}','{$this->bearbeiter}','{$this->freitext}','{$this->internebemerkung}','{$this->status}','{$this->adresse}','{$this->name}','{$this->abteilung}','{$this->unterabteilung}','{$this->strasse}','{$this->adresszusatz}','{$this->ansprechpartner}','{$this->plz}','{$this->ort}','{$this->land}','{$this->ustid}','{$this->ust_befreit}','{$this->ustbrief}','{$this->ustbrief_eingang}','{$this->ustbrief_eingang_am}','{$this->email}','{$this->telefon}','{$this->telefax}','{$this->betreff}','{$this->kundennummer}','{$this->lieferschein}','{$this->versandart}','{$this->lieferdatum}','{$this->buchhaltung}','{$this->zahlungsweise}','{$this->zahlungsstatus}','{$this->ist}','{$this->soll}','{$this->skonto_gegeben}','{$this->zahlungszieltage}','{$this->zahlungszieltageskonto}','{$this->zahlungszielskonto}','{$this->firma}','{$this->versendet}','{$this->versendet_am}','{$this->versendet_per}','{$this->versendet_durch}','{$this->versendet_mahnwesen}','{$this->mahnwesen}','{$this->mahnwesen_datum}','{$this->mahnwesen_gesperrt}','{$this->mahnwesen_internebemerkung}','{$this->inbearbeitung}','{$this->datev_abgeschlossen}','{$this->logdatei}','{$this->doppel}','{$this->autodruck_rz}','{$this->autodruck_periode}','{$this->autodruck_done}','{$this->autodruck_anzahlverband}','{$this->autodruck_anzahlkunde}','{$this->autodruck_mailverband}','{$this->autodruck_mailkunde}','{$this->dta_datei_verband}','{$this->dta_datei}','{$this->deckungsbeitragcalc}','{$this->deckungsbeitrag}','{$this->umsatz_netto}','{$this->erloes_netto}','{$this->mahnwesenfestsetzen}','{$this->vertriebid}','{$this->aktion}','{$this->vertrieb}','{$this->provision}','{$this->provision_summe}','{$this->gruppe}','{$this->punkte}','{$this->bonuspunkte}','{$this->provdatum}','{$this->ihrebestellnummer}','{$this->anschreiben}','{$this->usereditid}','{$this->useredittimestamp}','{$this->realrabatt}','{$this->rabatt}','{$this->einzugsdatum}','{$this->rabatt1}','{$this->rabatt2}','{$this->rabatt3}','{$this->rabatt4}','{$this->rabatt5}','{$this->forderungsverlust_datum}','{$this->forderungsverlust_betrag}','{$this->steuersatz_normal}','{$this->steuersatz_zwischen}','{$this->steuersatz_ermaessigt}','{$this->steuersatz_starkermaessigt}','{$this->steuersatz_dienstleistung}','{$this->waehrung}','{$this->keinsteuersatz}','{$this->schreibschutz}','{$this->pdfarchiviert}','{$this->pdfarchiviertversion}','{$this->typ}','{$this->ohne_briefpapier}','{$this->lieferid}','{$this->ansprechpartnerid}','{$this->systemfreitext}','{$this->projektfiliale}','{$this->zuarchivieren}','{$this->internebezeichnung}','{$this->angelegtam}','{$this->abweichendebezeichnung}','{$this->bezahlt_am}','{$this->sprache}','{$this->bundesland}','{$this->gln}','{$this->deliverythresholdvatid}','{$this->bearbeiterid}','{$this->kurs}','{$this->ohne_artikeltext}','{$this->anzeigesteuer}','{$this->kostenstelle}','{$this->bodyzusatz}','{$this->lieferbedingung}','{$this->titel}','{$this->skontobetrag}','{$this->skontoberechnet}','{$this->extsoll}','{$this->teilstorno}','{$this->bundesstaat}','{$this->kundennummer_buchhaltung}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `rechnung` SET
      `datum`='{$this->datum}',
      `aborechnung`='{$this->aborechnung}',
      `projekt`='{$this->projekt}',
      `anlegeart`='{$this->anlegeart}',
      `belegnr`='{$this->belegnr}',
      `auftrag`='{$this->auftrag}',
      `auftragid`='{$this->auftragid}',
      `bearbeiter`='{$this->bearbeiter}',
      `freitext`='{$this->freitext}',
      `internebemerkung`='{$this->internebemerkung}',
      `status`='{$this->status}',
      `adresse`='{$this->adresse}',
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
      `ust_befreit`='{$this->ust_befreit}',
      `ustbrief`='{$this->ustbrief}',
      `ustbrief_eingang`='{$this->ustbrief_eingang}',
      `ustbrief_eingang_am`='{$this->ustbrief_eingang_am}',
      `email`='{$this->email}',
      `telefon`='{$this->telefon}',
      `telefax`='{$this->telefax}',
      `betreff`='{$this->betreff}',
      `kundennummer`='{$this->kundennummer}',
      `lieferschein`='{$this->lieferschein}',
      `versandart`='{$this->versandart}',
      `lieferdatum`='{$this->lieferdatum}',
      `buchhaltung`='{$this->buchhaltung}',
      `zahlungsweise`='{$this->zahlungsweise}',
      `zahlungsstatus`='{$this->zahlungsstatus}',
      `ist`='{$this->ist}',
      `soll`='{$this->soll}',
      `skonto_gegeben`='{$this->skonto_gegeben}',
      `zahlungszieltage`='{$this->zahlungszieltage}',
      `zahlungszieltageskonto`='{$this->zahlungszieltageskonto}',
      `zahlungszielskonto`='{$this->zahlungszielskonto}',
      `firma`='{$this->firma}',
      `versendet`='{$this->versendet}',
      `versendet_am`='{$this->versendet_am}',
      `versendet_per`='{$this->versendet_per}',
      `versendet_durch`='{$this->versendet_durch}',
      `versendet_mahnwesen`='{$this->versendet_mahnwesen}',
      `mahnwesen`='{$this->mahnwesen}',
      `mahnwesen_datum`='{$this->mahnwesen_datum}',
      `mahnwesen_gesperrt`='{$this->mahnwesen_gesperrt}',
      `mahnwesen_internebemerkung`='{$this->mahnwesen_internebemerkung}',
      `inbearbeitung`='{$this->inbearbeitung}',
      `datev_abgeschlossen`='{$this->datev_abgeschlossen}',
      `logdatei`='{$this->logdatei}',
      `doppel`='{$this->doppel}',
      `autodruck_rz`='{$this->autodruck_rz}',
      `autodruck_periode`='{$this->autodruck_periode}',
      `autodruck_done`='{$this->autodruck_done}',
      `autodruck_anzahlverband`='{$this->autodruck_anzahlverband}',
      `autodruck_anzahlkunde`='{$this->autodruck_anzahlkunde}',
      `autodruck_mailverband`='{$this->autodruck_mailverband}',
      `autodruck_mailkunde`='{$this->autodruck_mailkunde}',
      `dta_datei_verband`='{$this->dta_datei_verband}',
      `dta_datei`='{$this->dta_datei}',
      `deckungsbeitragcalc`='{$this->deckungsbeitragcalc}',
      `deckungsbeitrag`='{$this->deckungsbeitrag}',
      `umsatz_netto`='{$this->umsatz_netto}',
      `erloes_netto`='{$this->erloes_netto}',
      `mahnwesenfestsetzen`='{$this->mahnwesenfestsetzen}',
      `vertriebid`='{$this->vertriebid}',
      `aktion`='{$this->aktion}',
      `vertrieb`='{$this->vertrieb}',
      `provision`='{$this->provision}',
      `provision_summe`='{$this->provision_summe}',
      `gruppe`='{$this->gruppe}',
      `punkte`='{$this->punkte}',
      `bonuspunkte`='{$this->bonuspunkte}',
      `provdatum`='{$this->provdatum}',
      `ihrebestellnummer`='{$this->ihrebestellnummer}',
      `anschreiben`='{$this->anschreiben}',
      `usereditid`='{$this->usereditid}',
      `useredittimestamp`='{$this->useredittimestamp}',
      `realrabatt`='{$this->realrabatt}',
      `rabatt`='{$this->rabatt}',
      `einzugsdatum`='{$this->einzugsdatum}',
      `rabatt1`='{$this->rabatt1}',
      `rabatt2`='{$this->rabatt2}',
      `rabatt3`='{$this->rabatt3}',
      `rabatt4`='{$this->rabatt4}',
      `rabatt5`='{$this->rabatt5}',
      `forderungsverlust_datum`='{$this->forderungsverlust_datum}',
      `forderungsverlust_betrag`='{$this->forderungsverlust_betrag}',
      `steuersatz_normal`='{$this->steuersatz_normal}',
      `steuersatz_zwischen`='{$this->steuersatz_zwischen}',
      `steuersatz_ermaessigt`='{$this->steuersatz_ermaessigt}',
      `steuersatz_starkermaessigt`='{$this->steuersatz_starkermaessigt}',
      `steuersatz_dienstleistung`='{$this->steuersatz_dienstleistung}',
      `waehrung`='{$this->waehrung}',
      `keinsteuersatz`='{$this->keinsteuersatz}',
      `schreibschutz`='{$this->schreibschutz}',
      `pdfarchiviert`='{$this->pdfarchiviert}',
      `pdfarchiviertversion`='{$this->pdfarchiviertversion}',
      `typ`='{$this->typ}',
      `ohne_briefpapier`='{$this->ohne_briefpapier}',
      `lieferid`='{$this->lieferid}',
      `ansprechpartnerid`='{$this->ansprechpartnerid}',
      `systemfreitext`='{$this->systemfreitext}',
      `projektfiliale`='{$this->projektfiliale}',
      `zuarchivieren`='{$this->zuarchivieren}',
      `internebezeichnung`='{$this->internebezeichnung}',
      `angelegtam`='{$this->angelegtam}',
      `abweichendebezeichnung`='{$this->abweichendebezeichnung}',
      `bezahlt_am`='{$this->bezahlt_am}',
      `sprache`='{$this->sprache}',
      `bundesland`='{$this->bundesland}',
      `gln`='{$this->gln}',
      `deliverythresholdvatid`='{$this->deliverythresholdvatid}',
      `bearbeiterid`='{$this->bearbeiterid}',
      `kurs`='{$this->kurs}',
      `ohne_artikeltext`='{$this->ohne_artikeltext}',
      `anzeigesteuer`='{$this->anzeigesteuer}',
      `kostenstelle`='{$this->kostenstelle}',
      `bodyzusatz`='{$this->bodyzusatz}',
      `lieferbedingung`='{$this->lieferbedingung}',
      `titel`='{$this->titel}',
      `skontobetrag`='{$this->skontobetrag}',
      `skontoberechnet`='{$this->skontoberechnet}',
      `extsoll`='{$this->extsoll}',
      `teilstorno`='{$this->teilstorno}',
      `bundesstaat`='{$this->bundesstaat}',
      `kundennummer_buchhaltung`='{$this->kundennummer_buchhaltung}'
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

    $sql = "DELETE FROM `rechnung` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->datum='';
    $this->aborechnung='';
    $this->projekt='';
    $this->anlegeart='';
    $this->belegnr='';
    $this->auftrag='';
    $this->auftragid='';
    $this->bearbeiter='';
    $this->freitext='';
    $this->internebemerkung='';
    $this->status='';
    $this->adresse='';
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
    $this->ust_befreit='';
    $this->ustbrief='';
    $this->ustbrief_eingang='';
    $this->ustbrief_eingang_am='';
    $this->email='';
    $this->telefon='';
    $this->telefax='';
    $this->betreff='';
    $this->kundennummer='';
    $this->lieferschein='';
    $this->versandart='';
    $this->lieferdatum='';
    $this->buchhaltung='';
    $this->zahlungsweise='';
    $this->zahlungsstatus='';
    $this->ist='';
    $this->soll='';
    $this->skonto_gegeben='';
    $this->zahlungszieltage='';
    $this->zahlungszieltageskonto='';
    $this->zahlungszielskonto='';
    $this->firma='';
    $this->versendet='';
    $this->versendet_am='';
    $this->versendet_per='';
    $this->versendet_durch='';
    $this->versendet_mahnwesen='';
    $this->mahnwesen='';
    $this->mahnwesen_datum='';
    $this->mahnwesen_gesperrt='';
    $this->mahnwesen_internebemerkung='';
    $this->inbearbeitung='';
    $this->datev_abgeschlossen='';
    $this->logdatei='';
    $this->doppel='';
    $this->autodruck_rz='';
    $this->autodruck_periode='';
    $this->autodruck_done='';
    $this->autodruck_anzahlverband='';
    $this->autodruck_anzahlkunde='';
    $this->autodruck_mailverband='';
    $this->autodruck_mailkunde='';
    $this->dta_datei_verband='';
    $this->dta_datei='';
    $this->deckungsbeitragcalc='';
    $this->deckungsbeitrag='';
    $this->umsatz_netto='';
    $this->erloes_netto='';
    $this->mahnwesenfestsetzen='';
    $this->vertriebid='';
    $this->aktion='';
    $this->vertrieb='';
    $this->provision='';
    $this->provision_summe='';
    $this->gruppe='';
    $this->punkte='';
    $this->bonuspunkte='';
    $this->provdatum='';
    $this->ihrebestellnummer='';
    $this->anschreiben='';
    $this->usereditid='';
    $this->useredittimestamp='';
    $this->realrabatt='';
    $this->rabatt='';
    $this->einzugsdatum='';
    $this->rabatt1='';
    $this->rabatt2='';
    $this->rabatt3='';
    $this->rabatt4='';
    $this->rabatt5='';
    $this->forderungsverlust_datum='';
    $this->forderungsverlust_betrag='';
    $this->steuersatz_normal='';
    $this->steuersatz_zwischen='';
    $this->steuersatz_ermaessigt='';
    $this->steuersatz_starkermaessigt='';
    $this->steuersatz_dienstleistung='';
    $this->waehrung='';
    $this->keinsteuersatz='';
    $this->schreibschutz='';
    $this->pdfarchiviert='';
    $this->pdfarchiviertversion='';
    $this->typ='';
    $this->ohne_briefpapier='';
    $this->lieferid='';
    $this->ansprechpartnerid='';
    $this->systemfreitext='';
    $this->projektfiliale='';
    $this->zuarchivieren='';
    $this->internebezeichnung='';
    $this->angelegtam='';
    $this->abweichendebezeichnung='';
    $this->bezahlt_am='';
    $this->sprache='';
    $this->bundesland='';
    $this->gln='';
    $this->deliverythresholdvatid='';
    $this->bearbeiterid='';
    $this->kurs='';
    $this->ohne_artikeltext='';
    $this->anzeigesteuer='';
    $this->kostenstelle='';
    $this->bodyzusatz='';
    $this->lieferbedingung='';
    $this->titel='';
    $this->skontobetrag='';
    $this->skontoberechnet='';
    $this->extsoll='';
    $this->teilstorno='';
    $this->bundesstaat='';
    $this->kundennummer_buchhaltung='';
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
  public function SetAborechnung($value) { $this->aborechnung=$value; }
  public function GetAborechnung() { return $this->aborechnung; }
  public function SetProjekt($value) { $this->projekt=$value; }
  public function GetProjekt() { return $this->projekt; }
  public function SetAnlegeart($value) { $this->anlegeart=$value; }
  public function GetAnlegeart() { return $this->anlegeart; }
  public function SetBelegnr($value) { $this->belegnr=$value; }
  public function GetBelegnr() { return $this->belegnr; }
  public function SetAuftrag($value) { $this->auftrag=$value; }
  public function GetAuftrag() { return $this->auftrag; }
  public function SetAuftragid($value) { $this->auftragid=$value; }
  public function GetAuftragid() { return $this->auftragid; }
  public function SetBearbeiter($value) { $this->bearbeiter=$value; }
  public function GetBearbeiter() { return $this->bearbeiter; }
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
  public function SetUst_Befreit($value) { $this->ust_befreit=$value; }
  public function GetUst_Befreit() { return $this->ust_befreit; }
  public function SetUstbrief($value) { $this->ustbrief=$value; }
  public function GetUstbrief() { return $this->ustbrief; }
  public function SetUstbrief_Eingang($value) { $this->ustbrief_eingang=$value; }
  public function GetUstbrief_Eingang() { return $this->ustbrief_eingang; }
  public function SetUstbrief_Eingang_Am($value) { $this->ustbrief_eingang_am=$value; }
  public function GetUstbrief_Eingang_Am() { return $this->ustbrief_eingang_am; }
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
  public function SetLieferschein($value) { $this->lieferschein=$value; }
  public function GetLieferschein() { return $this->lieferschein; }
  public function SetVersandart($value) { $this->versandart=$value; }
  public function GetVersandart() { return $this->versandart; }
  public function SetLieferdatum($value) { $this->lieferdatum=$value; }
  public function GetLieferdatum() { return $this->lieferdatum; }
  public function SetBuchhaltung($value) { $this->buchhaltung=$value; }
  public function GetBuchhaltung() { return $this->buchhaltung; }
  public function SetZahlungsweise($value) { $this->zahlungsweise=$value; }
  public function GetZahlungsweise() { return $this->zahlungsweise; }
  public function SetZahlungsstatus($value) { $this->zahlungsstatus=$value; }
  public function GetZahlungsstatus() { return $this->zahlungsstatus; }
  public function SetIst($value) { $this->ist=$value; }
  public function GetIst() { return $this->ist; }
  public function SetSoll($value) { $this->soll=$value; }
  public function GetSoll() { return $this->soll; }
  public function SetSkonto_Gegeben($value) { $this->skonto_gegeben=$value; }
  public function GetSkonto_Gegeben() { return $this->skonto_gegeben; }
  public function SetZahlungszieltage($value) { $this->zahlungszieltage=$value; }
  public function GetZahlungszieltage() { return $this->zahlungszieltage; }
  public function SetZahlungszieltageskonto($value) { $this->zahlungszieltageskonto=$value; }
  public function GetZahlungszieltageskonto() { return $this->zahlungszieltageskonto; }
  public function SetZahlungszielskonto($value) { $this->zahlungszielskonto=$value; }
  public function GetZahlungszielskonto() { return $this->zahlungszielskonto; }
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
  public function SetVersendet_Mahnwesen($value) { $this->versendet_mahnwesen=$value; }
  public function GetVersendet_Mahnwesen() { return $this->versendet_mahnwesen; }
  public function SetMahnwesen($value) { $this->mahnwesen=$value; }
  public function GetMahnwesen() { return $this->mahnwesen; }
  public function SetMahnwesen_Datum($value) { $this->mahnwesen_datum=$value; }
  public function GetMahnwesen_Datum() { return $this->mahnwesen_datum; }
  public function SetMahnwesen_Gesperrt($value) { $this->mahnwesen_gesperrt=$value; }
  public function GetMahnwesen_Gesperrt() { return $this->mahnwesen_gesperrt; }
  public function SetMahnwesen_Internebemerkung($value) { $this->mahnwesen_internebemerkung=$value; }
  public function GetMahnwesen_Internebemerkung() { return $this->mahnwesen_internebemerkung; }
  public function SetInbearbeitung($value) { $this->inbearbeitung=$value; }
  public function GetInbearbeitung() { return $this->inbearbeitung; }
  public function SetDatev_Abgeschlossen($value) { $this->datev_abgeschlossen=$value; }
  public function GetDatev_Abgeschlossen() { return $this->datev_abgeschlossen; }
  public function SetLogdatei($value) { $this->logdatei=$value; }
  public function GetLogdatei() { return $this->logdatei; }
  public function SetDoppel($value) { $this->doppel=$value; }
  public function GetDoppel() { return $this->doppel; }
  public function SetAutodruck_Rz($value) { $this->autodruck_rz=$value; }
  public function GetAutodruck_Rz() { return $this->autodruck_rz; }
  public function SetAutodruck_Periode($value) { $this->autodruck_periode=$value; }
  public function GetAutodruck_Periode() { return $this->autodruck_periode; }
  public function SetAutodruck_Done($value) { $this->autodruck_done=$value; }
  public function GetAutodruck_Done() { return $this->autodruck_done; }
  public function SetAutodruck_Anzahlverband($value) { $this->autodruck_anzahlverband=$value; }
  public function GetAutodruck_Anzahlverband() { return $this->autodruck_anzahlverband; }
  public function SetAutodruck_Anzahlkunde($value) { $this->autodruck_anzahlkunde=$value; }
  public function GetAutodruck_Anzahlkunde() { return $this->autodruck_anzahlkunde; }
  public function SetAutodruck_Mailverband($value) { $this->autodruck_mailverband=$value; }
  public function GetAutodruck_Mailverband() { return $this->autodruck_mailverband; }
  public function SetAutodruck_Mailkunde($value) { $this->autodruck_mailkunde=$value; }
  public function GetAutodruck_Mailkunde() { return $this->autodruck_mailkunde; }
  public function SetDta_Datei_Verband($value) { $this->dta_datei_verband=$value; }
  public function GetDta_Datei_Verband() { return $this->dta_datei_verband; }
  public function SetDta_Datei($value) { $this->dta_datei=$value; }
  public function GetDta_Datei() { return $this->dta_datei; }
  public function SetDeckungsbeitragcalc($value) { $this->deckungsbeitragcalc=$value; }
  public function GetDeckungsbeitragcalc() { return $this->deckungsbeitragcalc; }
  public function SetDeckungsbeitrag($value) { $this->deckungsbeitrag=$value; }
  public function GetDeckungsbeitrag() { return $this->deckungsbeitrag; }
  public function SetUmsatz_Netto($value) { $this->umsatz_netto=$value; }
  public function GetUmsatz_Netto() { return $this->umsatz_netto; }
  public function SetErloes_Netto($value) { $this->erloes_netto=$value; }
  public function GetErloes_Netto() { return $this->erloes_netto; }
  public function SetMahnwesenfestsetzen($value) { $this->mahnwesenfestsetzen=$value; }
  public function GetMahnwesenfestsetzen() { return $this->mahnwesenfestsetzen; }
  public function SetVertriebid($value) { $this->vertriebid=$value; }
  public function GetVertriebid() { return $this->vertriebid; }
  public function SetAktion($value) { $this->aktion=$value; }
  public function GetAktion() { return $this->aktion; }
  public function SetVertrieb($value) { $this->vertrieb=$value; }
  public function GetVertrieb() { return $this->vertrieb; }
  public function SetProvision($value) { $this->provision=$value; }
  public function GetProvision() { return $this->provision; }
  public function SetProvision_Summe($value) { $this->provision_summe=$value; }
  public function GetProvision_Summe() { return $this->provision_summe; }
  public function SetGruppe($value) { $this->gruppe=$value; }
  public function GetGruppe() { return $this->gruppe; }
  public function SetPunkte($value) { $this->punkte=$value; }
  public function GetPunkte() { return $this->punkte; }
  public function SetBonuspunkte($value) { $this->bonuspunkte=$value; }
  public function GetBonuspunkte() { return $this->bonuspunkte; }
  public function SetProvdatum($value) { $this->provdatum=$value; }
  public function GetProvdatum() { return $this->provdatum; }
  public function SetIhrebestellnummer($value) { $this->ihrebestellnummer=$value; }
  public function GetIhrebestellnummer() { return $this->ihrebestellnummer; }
  public function SetAnschreiben($value) { $this->anschreiben=$value; }
  public function GetAnschreiben() { return $this->anschreiben; }
  public function SetUsereditid($value) { $this->usereditid=$value; }
  public function GetUsereditid() { return $this->usereditid; }
  public function SetUseredittimestamp($value) { $this->useredittimestamp=$value; }
  public function GetUseredittimestamp() { return $this->useredittimestamp; }
  public function SetRealrabatt($value) { $this->realrabatt=$value; }
  public function GetRealrabatt() { return $this->realrabatt; }
  public function SetRabatt($value) { $this->rabatt=$value; }
  public function GetRabatt() { return $this->rabatt; }
  public function SetEinzugsdatum($value) { $this->einzugsdatum=$value; }
  public function GetEinzugsdatum() { return $this->einzugsdatum; }
  public function SetRabatt1($value) { $this->rabatt1=$value; }
  public function GetRabatt1() { return $this->rabatt1; }
  public function SetRabatt2($value) { $this->rabatt2=$value; }
  public function GetRabatt2() { return $this->rabatt2; }
  public function SetRabatt3($value) { $this->rabatt3=$value; }
  public function GetRabatt3() { return $this->rabatt3; }
  public function SetRabatt4($value) { $this->rabatt4=$value; }
  public function GetRabatt4() { return $this->rabatt4; }
  public function SetRabatt5($value) { $this->rabatt5=$value; }
  public function GetRabatt5() { return $this->rabatt5; }
  public function SetForderungsverlust_Datum($value) { $this->forderungsverlust_datum=$value; }
  public function GetForderungsverlust_Datum() { return $this->forderungsverlust_datum; }
  public function SetForderungsverlust_Betrag($value) { $this->forderungsverlust_betrag=$value; }
  public function GetForderungsverlust_Betrag() { return $this->forderungsverlust_betrag; }
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
  public function SetKeinsteuersatz($value) { $this->keinsteuersatz=$value; }
  public function GetKeinsteuersatz() { return $this->keinsteuersatz; }
  public function SetSchreibschutz($value) { $this->schreibschutz=$value; }
  public function GetSchreibschutz() { return $this->schreibschutz; }
  public function SetPdfarchiviert($value) { $this->pdfarchiviert=$value; }
  public function GetPdfarchiviert() { return $this->pdfarchiviert; }
  public function SetPdfarchiviertversion($value) { $this->pdfarchiviertversion=$value; }
  public function GetPdfarchiviertversion() { return $this->pdfarchiviertversion; }
  public function SetTyp($value) { $this->typ=$value; }
  public function GetTyp() { return $this->typ; }
  public function SetOhne_Briefpapier($value) { $this->ohne_briefpapier=$value; }
  public function GetOhne_Briefpapier() { return $this->ohne_briefpapier; }
  public function SetLieferid($value) { $this->lieferid=$value; }
  public function GetLieferid() { return $this->lieferid; }
  public function SetAnsprechpartnerid($value) { $this->ansprechpartnerid=$value; }
  public function GetAnsprechpartnerid() { return $this->ansprechpartnerid; }
  public function SetSystemfreitext($value) { $this->systemfreitext=$value; }
  public function GetSystemfreitext() { return $this->systemfreitext; }
  public function SetProjektfiliale($value) { $this->projektfiliale=$value; }
  public function GetProjektfiliale() { return $this->projektfiliale; }
  public function SetZuarchivieren($value) { $this->zuarchivieren=$value; }
  public function GetZuarchivieren() { return $this->zuarchivieren; }
  public function SetInternebezeichnung($value) { $this->internebezeichnung=$value; }
  public function GetInternebezeichnung() { return $this->internebezeichnung; }
  public function SetAngelegtam($value) { $this->angelegtam=$value; }
  public function GetAngelegtam() { return $this->angelegtam; }
  public function SetAbweichendebezeichnung($value) { $this->abweichendebezeichnung=$value; }
  public function GetAbweichendebezeichnung() { return $this->abweichendebezeichnung; }
  public function SetBezahlt_Am($value) { $this->bezahlt_am=$value; }
  public function GetBezahlt_Am() { return $this->bezahlt_am; }
  public function SetSprache($value) { $this->sprache=$value; }
  public function GetSprache() { return $this->sprache; }
  public function SetBundesland($value) { $this->bundesland=$value; }
  public function GetBundesland() { return $this->bundesland; }
  public function SetGln($value) { $this->gln=$value; }
  public function GetGln() { return $this->gln; }
  public function SetDeliverythresholdvatid($value) { $this->deliverythresholdvatid=$value; }
  public function GetDeliverythresholdvatid() { return $this->deliverythresholdvatid; }
  public function SetBearbeiterid($value) { $this->bearbeiterid=$value; }
  public function GetBearbeiterid() { return $this->bearbeiterid; }
  public function SetKurs($value) { $this->kurs=$value; }
  public function GetKurs() { return $this->kurs; }
  public function SetOhne_Artikeltext($value) { $this->ohne_artikeltext=$value; }
  public function GetOhne_Artikeltext() { return $this->ohne_artikeltext; }
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
  public function SetSkontobetrag($value) { $this->skontobetrag=$value; }
  public function GetSkontobetrag() { return $this->skontobetrag; }
  public function SetSkontoberechnet($value) { $this->skontoberechnet=$value; }
  public function GetSkontoberechnet() { return $this->skontoberechnet; }
  public function SetExtsoll($value) { $this->extsoll=$value; }
  public function GetExtsoll() { return $this->extsoll; }
  public function SetTeilstorno($value) { $this->teilstorno=$value; }
  public function GetTeilstorno() { return $this->teilstorno; }
  public function SetBundesstaat($value) { $this->bundesstaat=$value; }
  public function GetBundesstaat() { return $this->bundesstaat; }
  public function SetKundennummer_Buchhaltung($value) { $this->kundennummer_buchhaltung=$value; }
  public function GetKundennummer_Buchhaltung() { return $this->kundennummer_buchhaltung; }

}
