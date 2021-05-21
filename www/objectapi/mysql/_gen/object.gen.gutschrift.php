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

class ObjGenGutschrift
{

  private  $id;
  private  $datum;
  private  $projekt;
  private  $anlegeart;
  private  $belegnr;
  private  $rechnung;
  private  $rechnungid;
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
  private  $plz;
  private  $ort;
  private  $land;
  private  $ustid;
  private  $ustbrief;
  private  $ustbrief_eingang;
  private  $ustbrief_eingang_am;
  private  $ust_befreit;
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
  private  $zahlungszieltage;
  private  $zahlungszieltageskonto;
  private  $zahlungszielskonto;
  private  $gesamtsumme;
  private  $bank_inhaber;
  private  $bank_institut;
  private  $bank_blz;
  private  $bank_konto;
  private  $kreditkarte_typ;
  private  $kreditkarte_inhaber;
  private  $kreditkarte_nummer;
  private  $kreditkarte_pruefnummer;
  private  $kreditkarte_monat;
  private  $kreditkarte_jahr;
  private  $paypalaccount;
  private  $firma;
  private  $versendet;
  private  $versendet_am;
  private  $versendet_per;
  private  $versendet_durch;
  private  $inbearbeitung;
  private  $logdatei;
  private  $dta_datei_verband;
  private  $manuell_vorabbezahlt;
  private  $manuell_vorabbezahlt_hinweis;
  private  $nicht_umsatzmindernd;
  private  $dta_datei;
  private  $deckungsbeitragcalc;
  private  $deckungsbeitrag;
  private  $erloes_netto;
  private  $umsatz_netto;
  private  $vertriebid;
  private  $aktion;
  private  $vertrieb;
  private  $provision;
  private  $provision_summe;
  private  $gruppe;
  private  $ihrebestellnummer;
  private  $anschreiben;
  private  $usereditid;
  private  $useredittimestamp;
  private  $realrabatt;
  private  $rabatt;
  private  $rabatt1;
  private  $rabatt2;
  private  $rabatt3;
  private  $rabatt4;
  private  $rabatt5;
  private  $steuersatz_normal;
  private  $steuersatz_zwischen;
  private  $steuersatz_ermaessigt;
  private  $steuersatz_starkermaessigt;
  private  $steuersatz_dienstleistung;
  private  $waehrung;
  private  $keinsteuersatz;
  private  $stornorechnung;
  private  $schreibschutz;
  private  $pdfarchiviert;
  private  $pdfarchiviertversion;
  private  $typ;
  private  $ohne_briefpapier;
  private  $lieferid;
  private  $ansprechpartnerid;
  private  $projektfiliale;
  private  $zuarchivieren;
  private  $internebezeichnung;
  private  $angelegtam;
  private  $ansprechpartner;
  private  $sprache;
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
  private  $bundesstaat;
  private  $kundennummer_buchhaltung;
  private  $storage_country;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `gutschrift` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->datum=$result['datum'];
    $this->projekt=$result['projekt'];
    $this->anlegeart=$result['anlegeart'];
    $this->belegnr=$result['belegnr'];
    $this->rechnung=$result['rechnung'];
    $this->rechnungid=$result['rechnungid'];
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
    $this->plz=$result['plz'];
    $this->ort=$result['ort'];
    $this->land=$result['land'];
    $this->ustid=$result['ustid'];
    $this->ustbrief=$result['ustbrief'];
    $this->ustbrief_eingang=$result['ustbrief_eingang'];
    $this->ustbrief_eingang_am=$result['ustbrief_eingang_am'];
    $this->ust_befreit=$result['ust_befreit'];
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
    $this->zahlungszieltage=$result['zahlungszieltage'];
    $this->zahlungszieltageskonto=$result['zahlungszieltageskonto'];
    $this->zahlungszielskonto=$result['zahlungszielskonto'];
    $this->gesamtsumme=$result['gesamtsumme'];
    $this->bank_inhaber=$result['bank_inhaber'];
    $this->bank_institut=$result['bank_institut'];
    $this->bank_blz=$result['bank_blz'];
    $this->bank_konto=$result['bank_konto'];
    $this->kreditkarte_typ=$result['kreditkarte_typ'];
    $this->kreditkarte_inhaber=$result['kreditkarte_inhaber'];
    $this->kreditkarte_nummer=$result['kreditkarte_nummer'];
    $this->kreditkarte_pruefnummer=$result['kreditkarte_pruefnummer'];
    $this->kreditkarte_monat=$result['kreditkarte_monat'];
    $this->kreditkarte_jahr=$result['kreditkarte_jahr'];
    $this->paypalaccount=$result['paypalaccount'];
    $this->firma=$result['firma'];
    $this->versendet=$result['versendet'];
    $this->versendet_am=$result['versendet_am'];
    $this->versendet_per=$result['versendet_per'];
    $this->versendet_durch=$result['versendet_durch'];
    $this->inbearbeitung=$result['inbearbeitung'];
    $this->logdatei=$result['logdatei'];
    $this->dta_datei_verband=$result['dta_datei_verband'];
    $this->manuell_vorabbezahlt=$result['manuell_vorabbezahlt'];
    $this->manuell_vorabbezahlt_hinweis=$result['manuell_vorabbezahlt_hinweis'];
    $this->nicht_umsatzmindernd=$result['nicht_umsatzmindernd'];
    $this->dta_datei=$result['dta_datei'];
    $this->deckungsbeitragcalc=$result['deckungsbeitragcalc'];
    $this->deckungsbeitrag=$result['deckungsbeitrag'];
    $this->erloes_netto=$result['erloes_netto'];
    $this->umsatz_netto=$result['umsatz_netto'];
    $this->vertriebid=$result['vertriebid'];
    $this->aktion=$result['aktion'];
    $this->vertrieb=$result['vertrieb'];
    $this->provision=$result['provision'];
    $this->provision_summe=$result['provision_summe'];
    $this->gruppe=$result['gruppe'];
    $this->ihrebestellnummer=$result['ihrebestellnummer'];
    $this->anschreiben=$result['anschreiben'];
    $this->usereditid=$result['usereditid'];
    $this->useredittimestamp=$result['useredittimestamp'];
    $this->realrabatt=$result['realrabatt'];
    $this->rabatt=$result['rabatt'];
    $this->rabatt1=$result['rabatt1'];
    $this->rabatt2=$result['rabatt2'];
    $this->rabatt3=$result['rabatt3'];
    $this->rabatt4=$result['rabatt4'];
    $this->rabatt5=$result['rabatt5'];
    $this->steuersatz_normal=$result['steuersatz_normal'];
    $this->steuersatz_zwischen=$result['steuersatz_zwischen'];
    $this->steuersatz_ermaessigt=$result['steuersatz_ermaessigt'];
    $this->steuersatz_starkermaessigt=$result['steuersatz_starkermaessigt'];
    $this->steuersatz_dienstleistung=$result['steuersatz_dienstleistung'];
    $this->waehrung=$result['waehrung'];
    $this->keinsteuersatz=$result['keinsteuersatz'];
    $this->stornorechnung=$result['stornorechnung'];
    $this->schreibschutz=$result['schreibschutz'];
    $this->pdfarchiviert=$result['pdfarchiviert'];
    $this->pdfarchiviertversion=$result['pdfarchiviertversion'];
    $this->typ=$result['typ'];
    $this->ohne_briefpapier=$result['ohne_briefpapier'];
    $this->lieferid=$result['lieferid'];
    $this->ansprechpartnerid=$result['ansprechpartnerid'];
    $this->projektfiliale=$result['projektfiliale'];
    $this->zuarchivieren=$result['zuarchivieren'];
    $this->internebezeichnung=$result['internebezeichnung'];
    $this->angelegtam=$result['angelegtam'];
    $this->ansprechpartner=$result['ansprechpartner'];
    $this->sprache=$result['sprache'];
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
    $this->bundesstaat=$result['bundesstaat'];
    $this->kundennummer_buchhaltung=$result['kundennummer_buchhaltung'];
    $this->storage_country=$result['storage_country'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `gutschrift` (`id`,`datum`,`projekt`,`anlegeart`,`belegnr`,`rechnung`,`rechnungid`,`bearbeiter`,`freitext`,`internebemerkung`,`status`,`adresse`,`name`,`abteilung`,`unterabteilung`,`strasse`,`adresszusatz`,`plz`,`ort`,`land`,`ustid`,`ustbrief`,`ustbrief_eingang`,`ustbrief_eingang_am`,`ust_befreit`,`email`,`telefon`,`telefax`,`betreff`,`kundennummer`,`lieferschein`,`versandart`,`lieferdatum`,`buchhaltung`,`zahlungsweise`,`zahlungsstatus`,`ist`,`soll`,`zahlungszieltage`,`zahlungszieltageskonto`,`zahlungszielskonto`,`gesamtsumme`,`bank_inhaber`,`bank_institut`,`bank_blz`,`bank_konto`,`kreditkarte_typ`,`kreditkarte_inhaber`,`kreditkarte_nummer`,`kreditkarte_pruefnummer`,`kreditkarte_monat`,`kreditkarte_jahr`,`paypalaccount`,`firma`,`versendet`,`versendet_am`,`versendet_per`,`versendet_durch`,`inbearbeitung`,`logdatei`,`dta_datei_verband`,`manuell_vorabbezahlt`,`manuell_vorabbezahlt_hinweis`,`nicht_umsatzmindernd`,`dta_datei`,`deckungsbeitragcalc`,`deckungsbeitrag`,`erloes_netto`,`umsatz_netto`,`vertriebid`,`aktion`,`vertrieb`,`provision`,`provision_summe`,`gruppe`,`ihrebestellnummer`,`anschreiben`,`usereditid`,`useredittimestamp`,`realrabatt`,`rabatt`,`rabatt1`,`rabatt2`,`rabatt3`,`rabatt4`,`rabatt5`,`steuersatz_normal`,`steuersatz_zwischen`,`steuersatz_ermaessigt`,`steuersatz_starkermaessigt`,`steuersatz_dienstleistung`,`waehrung`,`keinsteuersatz`,`stornorechnung`,`schreibschutz`,`pdfarchiviert`,`pdfarchiviertversion`,`typ`,`ohne_briefpapier`,`lieferid`,`ansprechpartnerid`,`projektfiliale`,`zuarchivieren`,`internebezeichnung`,`angelegtam`,`ansprechpartner`,`sprache`,`gln`,`deliverythresholdvatid`,`bearbeiterid`,`kurs`,`ohne_artikeltext`,`anzeigesteuer`,`kostenstelle`,`bodyzusatz`,`lieferbedingung`,`titel`,`skontobetrag`,`skontoberechnet`,`extsoll`,`bundesstaat`,`kundennummer_buchhaltung`,`storage_country`)
      VALUES(NULL,'{$this->datum}','{$this->projekt}','{$this->anlegeart}','{$this->belegnr}','{$this->rechnung}','{$this->rechnungid}','{$this->bearbeiter}','{$this->freitext}','{$this->internebemerkung}','{$this->status}','{$this->adresse}','{$this->name}','{$this->abteilung}','{$this->unterabteilung}','{$this->strasse}','{$this->adresszusatz}','{$this->plz}','{$this->ort}','{$this->land}','{$this->ustid}','{$this->ustbrief}','{$this->ustbrief_eingang}','{$this->ustbrief_eingang_am}','{$this->ust_befreit}','{$this->email}','{$this->telefon}','{$this->telefax}','{$this->betreff}','{$this->kundennummer}','{$this->lieferschein}','{$this->versandart}','{$this->lieferdatum}','{$this->buchhaltung}','{$this->zahlungsweise}','{$this->zahlungsstatus}','{$this->ist}','{$this->soll}','{$this->zahlungszieltage}','{$this->zahlungszieltageskonto}','{$this->zahlungszielskonto}','{$this->gesamtsumme}','{$this->bank_inhaber}','{$this->bank_institut}','{$this->bank_blz}','{$this->bank_konto}','{$this->kreditkarte_typ}','{$this->kreditkarte_inhaber}','{$this->kreditkarte_nummer}','{$this->kreditkarte_pruefnummer}','{$this->kreditkarte_monat}','{$this->kreditkarte_jahr}','{$this->paypalaccount}','{$this->firma}','{$this->versendet}','{$this->versendet_am}','{$this->versendet_per}','{$this->versendet_durch}','{$this->inbearbeitung}','{$this->logdatei}','{$this->dta_datei_verband}','{$this->manuell_vorabbezahlt}','{$this->manuell_vorabbezahlt_hinweis}','{$this->nicht_umsatzmindernd}','{$this->dta_datei}','{$this->deckungsbeitragcalc}','{$this->deckungsbeitrag}','{$this->erloes_netto}','{$this->umsatz_netto}','{$this->vertriebid}','{$this->aktion}','{$this->vertrieb}','{$this->provision}','{$this->provision_summe}','{$this->gruppe}','{$this->ihrebestellnummer}','{$this->anschreiben}','{$this->usereditid}','{$this->useredittimestamp}','{$this->realrabatt}','{$this->rabatt}','{$this->rabatt1}','{$this->rabatt2}','{$this->rabatt3}','{$this->rabatt4}','{$this->rabatt5}','{$this->steuersatz_normal}','{$this->steuersatz_zwischen}','{$this->steuersatz_ermaessigt}','{$this->steuersatz_starkermaessigt}','{$this->steuersatz_dienstleistung}','{$this->waehrung}','{$this->keinsteuersatz}','{$this->stornorechnung}','{$this->schreibschutz}','{$this->pdfarchiviert}','{$this->pdfarchiviertversion}','{$this->typ}','{$this->ohne_briefpapier}','{$this->lieferid}','{$this->ansprechpartnerid}','{$this->projektfiliale}','{$this->zuarchivieren}','{$this->internebezeichnung}','{$this->angelegtam}','{$this->ansprechpartner}','{$this->sprache}','{$this->gln}','{$this->deliverythresholdvatid}','{$this->bearbeiterid}','{$this->kurs}','{$this->ohne_artikeltext}','{$this->anzeigesteuer}','{$this->kostenstelle}','{$this->bodyzusatz}','{$this->lieferbedingung}','{$this->titel}','{$this->skontobetrag}','{$this->skontoberechnet}','{$this->extsoll}','{$this->bundesstaat}','{$this->kundennummer_buchhaltung}','{$this->storage_country}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `gutschrift` SET
      `datum`='{$this->datum}',
      `projekt`='{$this->projekt}',
      `anlegeart`='{$this->anlegeart}',
      `belegnr`='{$this->belegnr}',
      `rechnung`='{$this->rechnung}',
      `rechnungid`='{$this->rechnungid}',
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
      `plz`='{$this->plz}',
      `ort`='{$this->ort}',
      `land`='{$this->land}',
      `ustid`='{$this->ustid}',
      `ustbrief`='{$this->ustbrief}',
      `ustbrief_eingang`='{$this->ustbrief_eingang}',
      `ustbrief_eingang_am`='{$this->ustbrief_eingang_am}',
      `ust_befreit`='{$this->ust_befreit}',
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
      `zahlungszieltage`='{$this->zahlungszieltage}',
      `zahlungszieltageskonto`='{$this->zahlungszieltageskonto}',
      `zahlungszielskonto`='{$this->zahlungszielskonto}',
      `gesamtsumme`='{$this->gesamtsumme}',
      `bank_inhaber`='{$this->bank_inhaber}',
      `bank_institut`='{$this->bank_institut}',
      `bank_blz`='{$this->bank_blz}',
      `bank_konto`='{$this->bank_konto}',
      `kreditkarte_typ`='{$this->kreditkarte_typ}',
      `kreditkarte_inhaber`='{$this->kreditkarte_inhaber}',
      `kreditkarte_nummer`='{$this->kreditkarte_nummer}',
      `kreditkarte_pruefnummer`='{$this->kreditkarte_pruefnummer}',
      `kreditkarte_monat`='{$this->kreditkarte_monat}',
      `kreditkarte_jahr`='{$this->kreditkarte_jahr}',
      `paypalaccount`='{$this->paypalaccount}',
      `firma`='{$this->firma}',
      `versendet`='{$this->versendet}',
      `versendet_am`='{$this->versendet_am}',
      `versendet_per`='{$this->versendet_per}',
      `versendet_durch`='{$this->versendet_durch}',
      `inbearbeitung`='{$this->inbearbeitung}',
      `logdatei`='{$this->logdatei}',
      `dta_datei_verband`='{$this->dta_datei_verband}',
      `manuell_vorabbezahlt`='{$this->manuell_vorabbezahlt}',
      `manuell_vorabbezahlt_hinweis`='{$this->manuell_vorabbezahlt_hinweis}',
      `nicht_umsatzmindernd`='{$this->nicht_umsatzmindernd}',
      `dta_datei`='{$this->dta_datei}',
      `deckungsbeitragcalc`='{$this->deckungsbeitragcalc}',
      `deckungsbeitrag`='{$this->deckungsbeitrag}',
      `erloes_netto`='{$this->erloes_netto}',
      `umsatz_netto`='{$this->umsatz_netto}',
      `vertriebid`='{$this->vertriebid}',
      `aktion`='{$this->aktion}',
      `vertrieb`='{$this->vertrieb}',
      `provision`='{$this->provision}',
      `provision_summe`='{$this->provision_summe}',
      `gruppe`='{$this->gruppe}',
      `ihrebestellnummer`='{$this->ihrebestellnummer}',
      `anschreiben`='{$this->anschreiben}',
      `usereditid`='{$this->usereditid}',
      `useredittimestamp`='{$this->useredittimestamp}',
      `realrabatt`='{$this->realrabatt}',
      `rabatt`='{$this->rabatt}',
      `rabatt1`='{$this->rabatt1}',
      `rabatt2`='{$this->rabatt2}',
      `rabatt3`='{$this->rabatt3}',
      `rabatt4`='{$this->rabatt4}',
      `rabatt5`='{$this->rabatt5}',
      `steuersatz_normal`='{$this->steuersatz_normal}',
      `steuersatz_zwischen`='{$this->steuersatz_zwischen}',
      `steuersatz_ermaessigt`='{$this->steuersatz_ermaessigt}',
      `steuersatz_starkermaessigt`='{$this->steuersatz_starkermaessigt}',
      `steuersatz_dienstleistung`='{$this->steuersatz_dienstleistung}',
      `waehrung`='{$this->waehrung}',
      `keinsteuersatz`='{$this->keinsteuersatz}',
      `stornorechnung`='{$this->stornorechnung}',
      `schreibschutz`='{$this->schreibschutz}',
      `pdfarchiviert`='{$this->pdfarchiviert}',
      `pdfarchiviertversion`='{$this->pdfarchiviertversion}',
      `typ`='{$this->typ}',
      `ohne_briefpapier`='{$this->ohne_briefpapier}',
      `lieferid`='{$this->lieferid}',
      `ansprechpartnerid`='{$this->ansprechpartnerid}',
      `projektfiliale`='{$this->projektfiliale}',
      `zuarchivieren`='{$this->zuarchivieren}',
      `internebezeichnung`='{$this->internebezeichnung}',
      `angelegtam`='{$this->angelegtam}',
      `ansprechpartner`='{$this->ansprechpartner}',
      `sprache`='{$this->sprache}',
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
      `bundesstaat`='{$this->bundesstaat}',
      `kundennummer_buchhaltung`='{$this->kundennummer_buchhaltung}',
      `storage_country`='{$this->storage_country}'
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

    $sql = "DELETE FROM `gutschrift` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->datum='';
    $this->projekt='';
    $this->anlegeart='';
    $this->belegnr='';
    $this->rechnung='';
    $this->rechnungid='';
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
    $this->plz='';
    $this->ort='';
    $this->land='';
    $this->ustid='';
    $this->ustbrief='';
    $this->ustbrief_eingang='';
    $this->ustbrief_eingang_am='';
    $this->ust_befreit='';
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
    $this->zahlungszieltage='';
    $this->zahlungszieltageskonto='';
    $this->zahlungszielskonto='';
    $this->gesamtsumme='';
    $this->bank_inhaber='';
    $this->bank_institut='';
    $this->bank_blz='';
    $this->bank_konto='';
    $this->kreditkarte_typ='';
    $this->kreditkarte_inhaber='';
    $this->kreditkarte_nummer='';
    $this->kreditkarte_pruefnummer='';
    $this->kreditkarte_monat='';
    $this->kreditkarte_jahr='';
    $this->paypalaccount='';
    $this->firma='';
    $this->versendet='';
    $this->versendet_am='';
    $this->versendet_per='';
    $this->versendet_durch='';
    $this->inbearbeitung='';
    $this->logdatei='';
    $this->dta_datei_verband='';
    $this->manuell_vorabbezahlt='';
    $this->manuell_vorabbezahlt_hinweis='';
    $this->nicht_umsatzmindernd='';
    $this->dta_datei='';
    $this->deckungsbeitragcalc='';
    $this->deckungsbeitrag='';
    $this->erloes_netto='';
    $this->umsatz_netto='';
    $this->vertriebid='';
    $this->aktion='';
    $this->vertrieb='';
    $this->provision='';
    $this->provision_summe='';
    $this->gruppe='';
    $this->ihrebestellnummer='';
    $this->anschreiben='';
    $this->usereditid='';
    $this->useredittimestamp='';
    $this->realrabatt='';
    $this->rabatt='';
    $this->rabatt1='';
    $this->rabatt2='';
    $this->rabatt3='';
    $this->rabatt4='';
    $this->rabatt5='';
    $this->steuersatz_normal='';
    $this->steuersatz_zwischen='';
    $this->steuersatz_ermaessigt='';
    $this->steuersatz_starkermaessigt='';
    $this->steuersatz_dienstleistung='';
    $this->waehrung='';
    $this->keinsteuersatz='';
    $this->stornorechnung='';
    $this->schreibschutz='';
    $this->pdfarchiviert='';
    $this->pdfarchiviertversion='';
    $this->typ='';
    $this->ohne_briefpapier='';
    $this->lieferid='';
    $this->ansprechpartnerid='';
    $this->projektfiliale='';
    $this->zuarchivieren='';
    $this->internebezeichnung='';
    $this->angelegtam='';
    $this->ansprechpartner='';
    $this->sprache='';
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
    $this->bundesstaat='';
    $this->kundennummer_buchhaltung='';
    $this->storage_country='';
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
  public function SetAnlegeart($value) { $this->anlegeart=$value; }
  public function GetAnlegeart() { return $this->anlegeart; }
  public function SetBelegnr($value) { $this->belegnr=$value; }
  public function GetBelegnr() { return $this->belegnr; }
  public function SetRechnung($value) { $this->rechnung=$value; }
  public function GetRechnung() { return $this->rechnung; }
  public function SetRechnungid($value) { $this->rechnungid=$value; }
  public function GetRechnungid() { return $this->rechnungid; }
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
  public function SetPlz($value) { $this->plz=$value; }
  public function GetPlz() { return $this->plz; }
  public function SetOrt($value) { $this->ort=$value; }
  public function GetOrt() { return $this->ort; }
  public function SetLand($value) { $this->land=$value; }
  public function GetLand() { return $this->land; }
  public function SetUstid($value) { $this->ustid=$value; }
  public function GetUstid() { return $this->ustid; }
  public function SetUstbrief($value) { $this->ustbrief=$value; }
  public function GetUstbrief() { return $this->ustbrief; }
  public function SetUstbrief_Eingang($value) { $this->ustbrief_eingang=$value; }
  public function GetUstbrief_Eingang() { return $this->ustbrief_eingang; }
  public function SetUstbrief_Eingang_Am($value) { $this->ustbrief_eingang_am=$value; }
  public function GetUstbrief_Eingang_Am() { return $this->ustbrief_eingang_am; }
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
  public function SetKreditkarte_Typ($value) { $this->kreditkarte_typ=$value; }
  public function GetKreditkarte_Typ() { return $this->kreditkarte_typ; }
  public function SetKreditkarte_Inhaber($value) { $this->kreditkarte_inhaber=$value; }
  public function GetKreditkarte_Inhaber() { return $this->kreditkarte_inhaber; }
  public function SetKreditkarte_Nummer($value) { $this->kreditkarte_nummer=$value; }
  public function GetKreditkarte_Nummer() { return $this->kreditkarte_nummer; }
  public function SetKreditkarte_Pruefnummer($value) { $this->kreditkarte_pruefnummer=$value; }
  public function GetKreditkarte_Pruefnummer() { return $this->kreditkarte_pruefnummer; }
  public function SetKreditkarte_Monat($value) { $this->kreditkarte_monat=$value; }
  public function GetKreditkarte_Monat() { return $this->kreditkarte_monat; }
  public function SetKreditkarte_Jahr($value) { $this->kreditkarte_jahr=$value; }
  public function GetKreditkarte_Jahr() { return $this->kreditkarte_jahr; }
  public function SetPaypalaccount($value) { $this->paypalaccount=$value; }
  public function GetPaypalaccount() { return $this->paypalaccount; }
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
  public function SetInbearbeitung($value) { $this->inbearbeitung=$value; }
  public function GetInbearbeitung() { return $this->inbearbeitung; }
  public function SetLogdatei($value) { $this->logdatei=$value; }
  public function GetLogdatei() { return $this->logdatei; }
  public function SetDta_Datei_Verband($value) { $this->dta_datei_verband=$value; }
  public function GetDta_Datei_Verband() { return $this->dta_datei_verband; }
  public function SetManuell_Vorabbezahlt($value) { $this->manuell_vorabbezahlt=$value; }
  public function GetManuell_Vorabbezahlt() { return $this->manuell_vorabbezahlt; }
  public function SetManuell_Vorabbezahlt_Hinweis($value) { $this->manuell_vorabbezahlt_hinweis=$value; }
  public function GetManuell_Vorabbezahlt_Hinweis() { return $this->manuell_vorabbezahlt_hinweis; }
  public function SetNicht_Umsatzmindernd($value) { $this->nicht_umsatzmindernd=$value; }
  public function GetNicht_Umsatzmindernd() { return $this->nicht_umsatzmindernd; }
  public function SetDta_Datei($value) { $this->dta_datei=$value; }
  public function GetDta_Datei() { return $this->dta_datei; }
  public function SetDeckungsbeitragcalc($value) { $this->deckungsbeitragcalc=$value; }
  public function GetDeckungsbeitragcalc() { return $this->deckungsbeitragcalc; }
  public function SetDeckungsbeitrag($value) { $this->deckungsbeitrag=$value; }
  public function GetDeckungsbeitrag() { return $this->deckungsbeitrag; }
  public function SetErloes_Netto($value) { $this->erloes_netto=$value; }
  public function GetErloes_Netto() { return $this->erloes_netto; }
  public function SetUmsatz_Netto($value) { $this->umsatz_netto=$value; }
  public function GetUmsatz_Netto() { return $this->umsatz_netto; }
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
  public function SetStornorechnung($value) { $this->stornorechnung=$value; }
  public function GetStornorechnung() { return $this->stornorechnung; }
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
  public function SetProjektfiliale($value) { $this->projektfiliale=$value; }
  public function GetProjektfiliale() { return $this->projektfiliale; }
  public function SetZuarchivieren($value) { $this->zuarchivieren=$value; }
  public function GetZuarchivieren() { return $this->zuarchivieren; }
  public function SetInternebezeichnung($value) { $this->internebezeichnung=$value; }
  public function GetInternebezeichnung() { return $this->internebezeichnung; }
  public function SetAngelegtam($value) { $this->angelegtam=$value; }
  public function GetAngelegtam() { return $this->angelegtam; }
  public function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  public function GetAnsprechpartner() { return $this->ansprechpartner; }
  public function SetSprache($value) { $this->sprache=$value; }
  public function GetSprache() { return $this->sprache; }
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
  public function SetBundesstaat($value) { $this->bundesstaat=$value; }
  public function GetBundesstaat() { return $this->bundesstaat; }
  public function SetKundennummer_Buchhaltung($value) { $this->kundennummer_buchhaltung=$value; }
  public function GetKundennummer_Buchhaltung() { return $this->kundennummer_buchhaltung; }
  public function SetStorage_Country($value) { $this->storage_country=$value; }
  public function GetStorage_Country() { return $this->storage_country; }

}
