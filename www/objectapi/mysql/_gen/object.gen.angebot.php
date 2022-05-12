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

class ObjGenAngebot
{

  private  $id;
  private  $datum;
  private  $gueltigbis;
  private  $projekt;
  private  $belegnr;
  private  $bearbeiter;
  private  $anfrage;
  private  $auftrag;
  private  $freitext;
  private  $internebemerkung;
  private  $status;
  private  $adresse;
  private  $retyp;
  private  $rechnungname;
  private  $retelefon;
  private  $reansprechpartner;
  private  $retelefax;
  private  $reabteilung;
  private  $reemail;
  private  $reunterabteilung;
  private  $readresszusatz;
  private  $restrasse;
  private  $replz;
  private  $reort;
  private  $reland;
  private  $name;
  private  $abteilung;
  private  $unterabteilung;
  private  $strasse;
  private  $adresszusatz;
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
  private  $vertrieb;
  private  $zahlungsweise;
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
  private  $abweichendelieferadresse;
  private  $abweichenderechnungsadresse;
  private  $liefername;
  private  $lieferabteilung;
  private  $lieferunterabteilung;
  private  $lieferland;
  private  $lieferstrasse;
  private  $lieferort;
  private  $lieferplz;
  private  $lieferadresszusatz;
  private  $lieferansprechpartner;
  private  $liefertelefon;
  private  $liefertelefax;
  private  $liefermail;
  private  $autoversand;
  private  $keinporto;
  private  $gesamtsummeausblenden;
  private  $ust_befreit;
  private  $firma;
  private  $versendet;
  private  $versendet_am;
  private  $versendet_per;
  private  $versendet_durch;
  private  $inbearbeitung;
  private  $vermerk;
  private  $logdatei;
  private  $ansprechpartner;
  private  $deckungsbeitragcalc;
  private  $deckungsbeitrag;
  private  $erloes_netto;
  private  $umsatz_netto;
  private  $lieferdatum;
  private  $vertriebid;
  private  $aktion;
  private  $provision;
  private  $provision_summe;
  private  $keinsteuersatz;
  private  $anfrageid;
  private  $gruppe;
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
  private  $schreibschutz;
  private  $pdfarchiviert;
  private  $pdfarchiviertversion;
  private  $typ;
  private  $ohne_briefpapier;
  private  $auftragid;
  private  $lieferid;
  private  $ansprechpartnerid;
  private  $projektfiliale;
  private  $abweichendebezeichnung;
  private  $zuarchivieren;
  private  $internebezeichnung;
  private  $angelegtam;
  private  $kopievon;
  private  $kopienummer;
  private  $lieferdatumkw;
  private  $sprache;
  private  $liefergln;
  private  $lieferemail;
  private  $gln;
  private  $planedorderdate;
  private  $bearbeiterid;
  private  $kurs;
  private  $ohne_artikeltext;
  private  $anzeigesteuer;
  private  $kostenstelle;
  private  $bodyzusatz;
  private  $lieferbedingung;
  private  $titel;
  private  $liefertitel;
  private  $skontobetrag;
  private  $skontoberechnet;
  private  $shop;
  private  $internet;
  private  $transaktionsnummer;
  private  $packstation_inhaber;
  private  $packstation_station;
  private  $packstation_ident;
  private  $packstation_plz;
  private  $packstation_ort;
  private  $shopextid;
  private  $bundesstaat;
  private  $lieferbundesstaat;
  private  $standardlager;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `angebot` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->datum=$result['datum'];
    $this->gueltigbis=$result['gueltigbis'];
    $this->projekt=$result['projekt'];
    $this->belegnr=$result['belegnr'];
    $this->bearbeiter=$result['bearbeiter'];
    $this->anfrage=$result['anfrage'];
    $this->auftrag=$result['auftrag'];
    $this->freitext=$result['freitext'];
    $this->internebemerkung=$result['internebemerkung'];
    $this->status=$result['status'];
    $this->adresse=$result['adresse'];
    $this->retyp=$result['retyp'];
    $this->rechnungname=$result['rechnungname'];
    $this->retelefon=$result['retelefon'];
    $this->reansprechpartner=$result['reansprechpartner'];
    $this->retelefax=$result['retelefax'];
    $this->reabteilung=$result['reabteilung'];
    $this->reemail=$result['reemail'];
    $this->reunterabteilung=$result['reunterabteilung'];
    $this->readresszusatz=$result['readresszusatz'];
    $this->restrasse=$result['restrasse'];
    $this->replz=$result['replz'];
    $this->reort=$result['reort'];
    $this->reland=$result['reland'];
    $this->name=$result['name'];
    $this->abteilung=$result['abteilung'];
    $this->unterabteilung=$result['unterabteilung'];
    $this->strasse=$result['strasse'];
    $this->adresszusatz=$result['adresszusatz'];
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
    $this->vertrieb=$result['vertrieb'];
    $this->zahlungsweise=$result['zahlungsweise'];
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
    $this->abweichendelieferadresse=$result['abweichendelieferadresse'];
    $this->abweichenderechnungsadresse=$result['abweichenderechnungsadresse'];
    $this->liefername=$result['liefername'];
    $this->lieferabteilung=$result['lieferabteilung'];
    $this->lieferunterabteilung=$result['lieferunterabteilung'];
    $this->lieferland=$result['lieferland'];
    $this->lieferstrasse=$result['lieferstrasse'];
    $this->lieferort=$result['lieferort'];
    $this->lieferplz=$result['lieferplz'];
    $this->lieferadresszusatz=$result['lieferadresszusatz'];
    $this->lieferansprechpartner=$result['lieferansprechpartner'];
    $this->liefertelefon=$result['liefertelefon'];
    $this->liefertelefax=$result['liefertelefax'];
    $this->liefermail=$result['liefermail'];
    $this->autoversand=$result['autoversand'];
    $this->keinporto=$result['keinporto'];
    $this->gesamtsummeausblenden=$result['gesamtsummeausblenden'];
    $this->ust_befreit=$result['ust_befreit'];
    $this->firma=$result['firma'];
    $this->versendet=$result['versendet'];
    $this->versendet_am=$result['versendet_am'];
    $this->versendet_per=$result['versendet_per'];
    $this->versendet_durch=$result['versendet_durch'];
    $this->inbearbeitung=$result['inbearbeitung'];
    $this->vermerk=$result['vermerk'];
    $this->logdatei=$result['logdatei'];
    $this->ansprechpartner=$result['ansprechpartner'];
    $this->deckungsbeitragcalc=$result['deckungsbeitragcalc'];
    $this->deckungsbeitrag=$result['deckungsbeitrag'];
    $this->erloes_netto=$result['erloes_netto'];
    $this->umsatz_netto=$result['umsatz_netto'];
    $this->lieferdatum=$result['lieferdatum'];
    $this->vertriebid=$result['vertriebid'];
    $this->aktion=$result['aktion'];
    $this->provision=$result['provision'];
    $this->provision_summe=$result['provision_summe'];
    $this->keinsteuersatz=$result['keinsteuersatz'];
    $this->anfrageid=$result['anfrageid'];
    $this->gruppe=$result['gruppe'];
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
    $this->schreibschutz=$result['schreibschutz'];
    $this->pdfarchiviert=$result['pdfarchiviert'];
    $this->pdfarchiviertversion=$result['pdfarchiviertversion'];
    $this->typ=$result['typ'];
    $this->ohne_briefpapier=$result['ohne_briefpapier'];
    $this->auftragid=$result['auftragid'];
    $this->lieferid=$result['lieferid'];
    $this->ansprechpartnerid=$result['ansprechpartnerid'];
    $this->projektfiliale=$result['projektfiliale'];
    $this->abweichendebezeichnung=$result['abweichendebezeichnung'];
    $this->zuarchivieren=$result['zuarchivieren'];
    $this->internebezeichnung=$result['internebezeichnung'];
    $this->angelegtam=$result['angelegtam'];
    $this->kopievon=$result['kopievon'];
    $this->kopienummer=$result['kopienummer'];
    $this->lieferdatumkw=$result['lieferdatumkw'];
    $this->sprache=$result['sprache'];
    $this->liefergln=$result['liefergln'];
    $this->lieferemail=$result['lieferemail'];
    $this->gln=$result['gln'];
    $this->planedorderdate=$result['planedorderdate'];
    $this->bearbeiterid=$result['bearbeiterid'];
    $this->kurs=$result['kurs'];
    $this->ohne_artikeltext=$result['ohne_artikeltext'];
    $this->anzeigesteuer=$result['anzeigesteuer'];
    $this->kostenstelle=$result['kostenstelle'];
    $this->bodyzusatz=$result['bodyzusatz'];
    $this->lieferbedingung=$result['lieferbedingung'];
    $this->titel=$result['titel'];
    $this->liefertitel=$result['liefertitel'];
    $this->skontobetrag=$result['skontobetrag'];
    $this->skontoberechnet=$result['skontoberechnet'];
    $this->shop=$result['shop'];
    $this->internet=$result['internet'];
    $this->transaktionsnummer=$result['transaktionsnummer'];
    $this->packstation_inhaber=$result['packstation_inhaber'];
    $this->packstation_station=$result['packstation_station'];
    $this->packstation_ident=$result['packstation_ident'];
    $this->packstation_plz=$result['packstation_plz'];
    $this->packstation_ort=$result['packstation_ort'];
    $this->shopextid=$result['shopextid'];
    $this->bundesstaat=$result['bundesstaat'];
    $this->lieferbundesstaat=$result['lieferbundesstaat'];
    $this->standardlager=$result['standardlager'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `angebot` (`id`,`datum`,`gueltigbis`,`projekt`,`belegnr`,`bearbeiter`,`anfrage`,`auftrag`,`freitext`,`internebemerkung`,`status`,`adresse`,`retyp`,`rechnungname`,`retelefon`,`reansprechpartner`,`retelefax`,`reabteilung`,`reemail`,`reunterabteilung`,`readresszusatz`,`restrasse`,`replz`,`reort`,`reland`,`name`,`abteilung`,`unterabteilung`,`strasse`,`adresszusatz`,`plz`,`ort`,`land`,`ustid`,`email`,`telefon`,`telefax`,`betreff`,`kundennummer`,`versandart`,`vertrieb`,`zahlungsweise`,`zahlungszieltage`,`zahlungszieltageskonto`,`zahlungszielskonto`,`gesamtsumme`,`bank_inhaber`,`bank_institut`,`bank_blz`,`bank_konto`,`kreditkarte_typ`,`kreditkarte_inhaber`,`kreditkarte_nummer`,`kreditkarte_pruefnummer`,`kreditkarte_monat`,`kreditkarte_jahr`,`abweichendelieferadresse`,`abweichenderechnungsadresse`,`liefername`,`lieferabteilung`,`lieferunterabteilung`,`lieferland`,`lieferstrasse`,`lieferort`,`lieferplz`,`lieferadresszusatz`,`lieferansprechpartner`,`liefertelefon`,`liefertelefax`,`liefermail`,`autoversand`,`keinporto`,`gesamtsummeausblenden`,`ust_befreit`,`firma`,`versendet`,`versendet_am`,`versendet_per`,`versendet_durch`,`inbearbeitung`,`vermerk`,`logdatei`,`ansprechpartner`,`deckungsbeitragcalc`,`deckungsbeitrag`,`erloes_netto`,`umsatz_netto`,`lieferdatum`,`vertriebid`,`aktion`,`provision`,`provision_summe`,`keinsteuersatz`,`anfrageid`,`gruppe`,`anschreiben`,`usereditid`,`useredittimestamp`,`realrabatt`,`rabatt`,`rabatt1`,`rabatt2`,`rabatt3`,`rabatt4`,`rabatt5`,`steuersatz_normal`,`steuersatz_zwischen`,`steuersatz_ermaessigt`,`steuersatz_starkermaessigt`,`steuersatz_dienstleistung`,`waehrung`,`schreibschutz`,`pdfarchiviert`,`pdfarchiviertversion`,`typ`,`ohne_briefpapier`,`auftragid`,`lieferid`,`ansprechpartnerid`,`projektfiliale`,`abweichendebezeichnung`,`zuarchivieren`,`internebezeichnung`,`angelegtam`,`kopievon`,`kopienummer`,`lieferdatumkw`,`sprache`,`liefergln`,`lieferemail`,`gln`,`planedorderdate`,`bearbeiterid`,`kurs`,`ohne_artikeltext`,`anzeigesteuer`,`kostenstelle`,`bodyzusatz`,`lieferbedingung`,`titel`,`liefertitel`,`skontobetrag`,`skontoberechnet`,`shop`,`internet`,`transaktionsnummer`,`packstation_inhaber`,`packstation_station`,`packstation_ident`,`packstation_plz`,`packstation_ort`,`shopextid`,`bundesstaat`,`lieferbundesstaat`,`standardlager`)
      VALUES(NULL,'{$this->datum}','{$this->gueltigbis}','{$this->projekt}','{$this->belegnr}','{$this->bearbeiter}','{$this->anfrage}','{$this->auftrag}','{$this->freitext}','{$this->internebemerkung}','{$this->status}','{$this->adresse}','{$this->retyp}','{$this->rechnungname}','{$this->retelefon}','{$this->reansprechpartner}','{$this->retelefax}','{$this->reabteilung}','{$this->reemail}','{$this->reunterabteilung}','{$this->readresszusatz}','{$this->restrasse}','{$this->replz}','{$this->reort}','{$this->reland}','{$this->name}','{$this->abteilung}','{$this->unterabteilung}','{$this->strasse}','{$this->adresszusatz}','{$this->plz}','{$this->ort}','{$this->land}','{$this->ustid}','{$this->email}','{$this->telefon}','{$this->telefax}','{$this->betreff}','{$this->kundennummer}','{$this->versandart}','{$this->vertrieb}','{$this->zahlungsweise}','{$this->zahlungszieltage}','{$this->zahlungszieltageskonto}','{$this->zahlungszielskonto}','{$this->gesamtsumme}','{$this->bank_inhaber}','{$this->bank_institut}','{$this->bank_blz}','{$this->bank_konto}','{$this->kreditkarte_typ}','{$this->kreditkarte_inhaber}','{$this->kreditkarte_nummer}','{$this->kreditkarte_pruefnummer}','{$this->kreditkarte_monat}','{$this->kreditkarte_jahr}','{$this->abweichendelieferadresse}','{$this->abweichenderechnungsadresse}','{$this->liefername}','{$this->lieferabteilung}','{$this->lieferunterabteilung}','{$this->lieferland}','{$this->lieferstrasse}','{$this->lieferort}','{$this->lieferplz}','{$this->lieferadresszusatz}','{$this->lieferansprechpartner}','{$this->liefertelefon}','{$this->liefertelefax}','{$this->liefermail}','{$this->autoversand}','{$this->keinporto}','{$this->gesamtsummeausblenden}','{$this->ust_befreit}','{$this->firma}','{$this->versendet}','{$this->versendet_am}','{$this->versendet_per}','{$this->versendet_durch}','{$this->inbearbeitung}','{$this->vermerk}','{$this->logdatei}','{$this->ansprechpartner}','{$this->deckungsbeitragcalc}','{$this->deckungsbeitrag}','{$this->erloes_netto}','{$this->umsatz_netto}','{$this->lieferdatum}','{$this->vertriebid}','{$this->aktion}','{$this->provision}','{$this->provision_summe}','{$this->keinsteuersatz}','{$this->anfrageid}','{$this->gruppe}','{$this->anschreiben}','{$this->usereditid}','{$this->useredittimestamp}','{$this->realrabatt}','{$this->rabatt}','{$this->rabatt1}','{$this->rabatt2}','{$this->rabatt3}','{$this->rabatt4}','{$this->rabatt5}','{$this->steuersatz_normal}','{$this->steuersatz_zwischen}','{$this->steuersatz_ermaessigt}','{$this->steuersatz_starkermaessigt}','{$this->steuersatz_dienstleistung}','{$this->waehrung}','{$this->schreibschutz}','{$this->pdfarchiviert}','{$this->pdfarchiviertversion}','{$this->typ}','{$this->ohne_briefpapier}','{$this->auftragid}','{$this->lieferid}','{$this->ansprechpartnerid}','{$this->projektfiliale}','{$this->abweichendebezeichnung}','{$this->zuarchivieren}','{$this->internebezeichnung}','{$this->angelegtam}','{$this->kopievon}','{$this->kopienummer}','{$this->lieferdatumkw}','{$this->sprache}','{$this->liefergln}','{$this->lieferemail}','{$this->gln}','{$this->planedorderdate}','{$this->bearbeiterid}','{$this->kurs}','{$this->ohne_artikeltext}','{$this->anzeigesteuer}','{$this->kostenstelle}','{$this->bodyzusatz}','{$this->lieferbedingung}','{$this->titel}','{$this->liefertitel}','{$this->skontobetrag}','{$this->skontoberechnet}','{$this->shop}','{$this->internet}','{$this->transaktionsnummer}','{$this->packstation_inhaber}','{$this->packstation_station}','{$this->packstation_ident}','{$this->packstation_plz}','{$this->packstation_ort}','{$this->shopextid}','{$this->bundesstaat}','{$this->lieferbundesstaat}','{$this->standardlager}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `angebot` SET
      `datum`='{$this->datum}',
      `gueltigbis`='{$this->gueltigbis}',
      `projekt`='{$this->projekt}',
      `belegnr`='{$this->belegnr}',
      `bearbeiter`='{$this->bearbeiter}',
      `anfrage`='{$this->anfrage}',
      `auftrag`='{$this->auftrag}',
      `freitext`='{$this->freitext}',
      `internebemerkung`='{$this->internebemerkung}',
      `status`='{$this->status}',
      `adresse`='{$this->adresse}',
      `retyp`='{$this->retyp}',
      `rechnungname`='{$this->rechnungname}',
      `retelefon`='{$this->retelefon}',
      `reansprechpartner`='{$this->reansprechpartner}',
      `retelefax`='{$this->retelefax}',
      `reabteilung`='{$this->reabteilung}',
      `reemail`='{$this->reemail}',
      `reunterabteilung`='{$this->reunterabteilung}',
      `readresszusatz`='{$this->readresszusatz}',
      `restrasse`='{$this->restrasse}',
      `replz`='{$this->replz}',
      `reort`='{$this->reort}',
      `reland`='{$this->reland}',
      `name`='{$this->name}',
      `abteilung`='{$this->abteilung}',
      `unterabteilung`='{$this->unterabteilung}',
      `strasse`='{$this->strasse}',
      `adresszusatz`='{$this->adresszusatz}',
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
      `vertrieb`='{$this->vertrieb}',
      `zahlungsweise`='{$this->zahlungsweise}',
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
      `abweichendelieferadresse`='{$this->abweichendelieferadresse}',
      `abweichenderechnungsadresse`='{$this->abweichenderechnungsadresse}',
      `liefername`='{$this->liefername}',
      `lieferabteilung`='{$this->lieferabteilung}',
      `lieferunterabteilung`='{$this->lieferunterabteilung}',
      `lieferland`='{$this->lieferland}',
      `lieferstrasse`='{$this->lieferstrasse}',
      `lieferort`='{$this->lieferort}',
      `lieferplz`='{$this->lieferplz}',
      `lieferadresszusatz`='{$this->lieferadresszusatz}',
      `lieferansprechpartner`='{$this->lieferansprechpartner}',
      `liefertelefon`='{$this->liefertelefon}',
      `liefertelefax`='{$this->liefertelefax}',
      `liefermail`='{$this->liefermail}',
      `autoversand`='{$this->autoversand}',
      `keinporto`='{$this->keinporto}',
      `gesamtsummeausblenden`='{$this->gesamtsummeausblenden}',
      `ust_befreit`='{$this->ust_befreit}',
      `firma`='{$this->firma}',
      `versendet`='{$this->versendet}',
      `versendet_am`='{$this->versendet_am}',
      `versendet_per`='{$this->versendet_per}',
      `versendet_durch`='{$this->versendet_durch}',
      `inbearbeitung`='{$this->inbearbeitung}',
      `vermerk`='{$this->vermerk}',
      `logdatei`='{$this->logdatei}',
      `ansprechpartner`='{$this->ansprechpartner}',
      `deckungsbeitragcalc`='{$this->deckungsbeitragcalc}',
      `deckungsbeitrag`='{$this->deckungsbeitrag}',
      `erloes_netto`='{$this->erloes_netto}',
      `umsatz_netto`='{$this->umsatz_netto}',
      `lieferdatum`='{$this->lieferdatum}',
      `vertriebid`='{$this->vertriebid}',
      `aktion`='{$this->aktion}',
      `provision`='{$this->provision}',
      `provision_summe`='{$this->provision_summe}',
      `keinsteuersatz`='{$this->keinsteuersatz}',
      `anfrageid`='{$this->anfrageid}',
      `gruppe`='{$this->gruppe}',
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
      `schreibschutz`='{$this->schreibschutz}',
      `pdfarchiviert`='{$this->pdfarchiviert}',
      `pdfarchiviertversion`='{$this->pdfarchiviertversion}',
      `typ`='{$this->typ}',
      `ohne_briefpapier`='{$this->ohne_briefpapier}',
      `auftragid`='{$this->auftragid}',
      `lieferid`='{$this->lieferid}',
      `ansprechpartnerid`='{$this->ansprechpartnerid}',
      `projektfiliale`='{$this->projektfiliale}',
      `abweichendebezeichnung`='{$this->abweichendebezeichnung}',
      `zuarchivieren`='{$this->zuarchivieren}',
      `internebezeichnung`='{$this->internebezeichnung}',
      `angelegtam`='{$this->angelegtam}',
      `kopievon`='{$this->kopievon}',
      `kopienummer`='{$this->kopienummer}',
      `lieferdatumkw`='{$this->lieferdatumkw}',
      `sprache`='{$this->sprache}',
      `liefergln`='{$this->liefergln}',
      `lieferemail`='{$this->lieferemail}',
      `gln`='{$this->gln}',
      `planedorderdate`='{$this->planedorderdate}',
      `bearbeiterid`='{$this->bearbeiterid}',
      `kurs`='{$this->kurs}',
      `ohne_artikeltext`='{$this->ohne_artikeltext}',
      `anzeigesteuer`='{$this->anzeigesteuer}',
      `kostenstelle`='{$this->kostenstelle}',
      `bodyzusatz`='{$this->bodyzusatz}',
      `lieferbedingung`='{$this->lieferbedingung}',
      `titel`='{$this->titel}',
      `liefertitel`='{$this->liefertitel}',
      `skontobetrag`='{$this->skontobetrag}',
      `skontoberechnet`='{$this->skontoberechnet}',
      `shop`='{$this->shop}',
      `internet`='{$this->internet}',
      `transaktionsnummer`='{$this->transaktionsnummer}',
      `packstation_inhaber`='{$this->packstation_inhaber}',
      `packstation_station`='{$this->packstation_station}',
      `packstation_ident`='{$this->packstation_ident}',
      `packstation_plz`='{$this->packstation_plz}',
      `packstation_ort`='{$this->packstation_ort}',
      `shopextid`='{$this->shopextid}',
      `bundesstaat`='{$this->bundesstaat}',
      `lieferbundesstaat`='{$this->lieferbundesstaat}',
      `standardlager`='{$this->standardlager}'
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

    $sql = "DELETE FROM `angebot` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->datum='';
    $this->gueltigbis='';
    $this->projekt='';
    $this->belegnr='';
    $this->bearbeiter='';
    $this->anfrage='';
    $this->auftrag='';
    $this->freitext='';
    $this->internebemerkung='';
    $this->status='';
    $this->adresse='';
    $this->retyp='';
    $this->rechnungname='';
    $this->retelefon='';
    $this->reansprechpartner='';
    $this->retelefax='';
    $this->reabteilung='';
    $this->reemail='';
    $this->reunterabteilung='';
    $this->readresszusatz='';
    $this->restrasse='';
    $this->replz='';
    $this->reort='';
    $this->reland='';
    $this->name='';
    $this->abteilung='';
    $this->unterabteilung='';
    $this->strasse='';
    $this->adresszusatz='';
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
    $this->vertrieb='';
    $this->zahlungsweise='';
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
    $this->abweichendelieferadresse='';
    $this->abweichenderechnungsadresse='';
    $this->liefername='';
    $this->lieferabteilung='';
    $this->lieferunterabteilung='';
    $this->lieferland='';
    $this->lieferstrasse='';
    $this->lieferort='';
    $this->lieferplz='';
    $this->lieferadresszusatz='';
    $this->lieferansprechpartner='';
    $this->liefertelefon='';
    $this->liefertelefax='';
    $this->liefermail='';
    $this->autoversand='';
    $this->keinporto='';
    $this->gesamtsummeausblenden='';
    $this->ust_befreit='';
    $this->firma='';
    $this->versendet='';
    $this->versendet_am='';
    $this->versendet_per='';
    $this->versendet_durch='';
    $this->inbearbeitung='';
    $this->vermerk='';
    $this->logdatei='';
    $this->ansprechpartner='';
    $this->deckungsbeitragcalc='';
    $this->deckungsbeitrag='';
    $this->erloes_netto='';
    $this->umsatz_netto='';
    $this->lieferdatum='';
    $this->vertriebid='';
    $this->aktion='';
    $this->provision='';
    $this->provision_summe='';
    $this->keinsteuersatz='';
    $this->anfrageid='';
    $this->gruppe='';
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
    $this->schreibschutz='';
    $this->pdfarchiviert='';
    $this->pdfarchiviertversion='';
    $this->typ='';
    $this->ohne_briefpapier='';
    $this->auftragid='';
    $this->lieferid='';
    $this->ansprechpartnerid='';
    $this->projektfiliale='';
    $this->abweichendebezeichnung='';
    $this->zuarchivieren='';
    $this->internebezeichnung='';
    $this->angelegtam='';
    $this->kopievon='';
    $this->kopienummer='';
    $this->lieferdatumkw='';
    $this->sprache='';
    $this->liefergln='';
    $this->lieferemail='';
    $this->gln='';
    $this->planedorderdate='';
    $this->bearbeiterid='';
    $this->kurs='';
    $this->ohne_artikeltext='';
    $this->anzeigesteuer='';
    $this->kostenstelle='';
    $this->bodyzusatz='';
    $this->lieferbedingung='';
    $this->titel='';
    $this->liefertitel='';
    $this->skontobetrag='';
    $this->skontoberechnet='';
    $this->shop='';
    $this->internet='';
    $this->transaktionsnummer='';
    $this->packstation_inhaber='';
    $this->packstation_station='';
    $this->packstation_ident='';
    $this->packstation_plz='';
    $this->packstation_ort='';
    $this->shopextid='';
    $this->bundesstaat='';
    $this->lieferbundesstaat='';
    $this->standardlager='';
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
  public function SetGueltigbis($value) { $this->gueltigbis=$value; }
  public function GetGueltigbis() { return $this->gueltigbis; }
  public function SetProjekt($value) { $this->projekt=$value; }
  public function GetProjekt() { return $this->projekt; }
  public function SetBelegnr($value) { $this->belegnr=$value; }
  public function GetBelegnr() { return $this->belegnr; }
  public function SetBearbeiter($value) { $this->bearbeiter=$value; }
  public function GetBearbeiter() { return $this->bearbeiter; }
  public function SetAnfrage($value) { $this->anfrage=$value; }
  public function GetAnfrage() { return $this->anfrage; }
  public function SetAuftrag($value) { $this->auftrag=$value; }
  public function GetAuftrag() { return $this->auftrag; }
  public function SetFreitext($value) { $this->freitext=$value; }
  public function GetFreitext() { return $this->freitext; }
  public function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  public function GetInternebemerkung() { return $this->internebemerkung; }
  public function SetStatus($value) { $this->status=$value; }
  public function GetStatus() { return $this->status; }
  public function SetAdresse($value) { $this->adresse=$value; }
  public function GetAdresse() { return $this->adresse; }
  public function SetRetyp($value) { $this->retyp=$value; }
  public function GetRetyp() { return $this->retyp; }
  public function SetRechnungname($value) { $this->rechnungname=$value; }
  public function GetRechnungname() { return $this->rechnungname; }
  public function SetRetelefon($value) { $this->retelefon=$value; }
  public function GetRetelefon() { return $this->retelefon; }
  public function SetReansprechpartner($value) { $this->reansprechpartner=$value; }
  public function GetReansprechpartner() { return $this->reansprechpartner; }
  public function SetRetelefax($value) { $this->retelefax=$value; }
  public function GetRetelefax() { return $this->retelefax; }
  public function SetReabteilung($value) { $this->reabteilung=$value; }
  public function GetReabteilung() { return $this->reabteilung; }
  public function SetReemail($value) { $this->reemail=$value; }
  public function GetReemail() { return $this->reemail; }
  public function SetReunterabteilung($value) { $this->reunterabteilung=$value; }
  public function GetReunterabteilung() { return $this->reunterabteilung; }
  public function SetReadresszusatz($value) { $this->readresszusatz=$value; }
  public function GetReadresszusatz() { return $this->readresszusatz; }
  public function SetRestrasse($value) { $this->restrasse=$value; }
  public function GetRestrasse() { return $this->restrasse; }
  public function SetReplz($value) { $this->replz=$value; }
  public function GetReplz() { return $this->replz; }
  public function SetReort($value) { $this->reort=$value; }
  public function GetReort() { return $this->reort; }
  public function SetReland($value) { $this->reland=$value; }
  public function GetReland() { return $this->reland; }
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
  public function SetVertrieb($value) { $this->vertrieb=$value; }
  public function GetVertrieb() { return $this->vertrieb; }
  public function SetZahlungsweise($value) { $this->zahlungsweise=$value; }
  public function GetZahlungsweise() { return $this->zahlungsweise; }
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
  public function SetAbweichendelieferadresse($value) { $this->abweichendelieferadresse=$value; }
  public function GetAbweichendelieferadresse() { return $this->abweichendelieferadresse; }
  public function SetAbweichenderechnungsadresse($value) { $this->abweichenderechnungsadresse=$value; }
  public function GetAbweichenderechnungsadresse() { return $this->abweichenderechnungsadresse; }
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
  public function SetLiefertelefon($value) { $this->liefertelefon=$value; }
  public function GetLiefertelefon() { return $this->liefertelefon; }
  public function SetLiefertelefax($value) { $this->liefertelefax=$value; }
  public function GetLiefertelefax() { return $this->liefertelefax; }
  public function SetLiefermail($value) { $this->liefermail=$value; }
  public function GetLiefermail() { return $this->liefermail; }
  public function SetAutoversand($value) { $this->autoversand=$value; }
  public function GetAutoversand() { return $this->autoversand; }
  public function SetKeinporto($value) { $this->keinporto=$value; }
  public function GetKeinporto() { return $this->keinporto; }
  public function SetGesamtsummeausblenden($value) { $this->gesamtsummeausblenden=$value; }
  public function GetGesamtsummeausblenden() { return $this->gesamtsummeausblenden; }
  public function SetUst_Befreit($value) { $this->ust_befreit=$value; }
  public function GetUst_Befreit() { return $this->ust_befreit; }
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
  public function SetVermerk($value) { $this->vermerk=$value; }
  public function GetVermerk() { return $this->vermerk; }
  public function SetLogdatei($value) { $this->logdatei=$value; }
  public function GetLogdatei() { return $this->logdatei; }
  public function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  public function GetAnsprechpartner() { return $this->ansprechpartner; }
  public function SetDeckungsbeitragcalc($value) { $this->deckungsbeitragcalc=$value; }
  public function GetDeckungsbeitragcalc() { return $this->deckungsbeitragcalc; }
  public function SetDeckungsbeitrag($value) { $this->deckungsbeitrag=$value; }
  public function GetDeckungsbeitrag() { return $this->deckungsbeitrag; }
  public function SetErloes_Netto($value) { $this->erloes_netto=$value; }
  public function GetErloes_Netto() { return $this->erloes_netto; }
  public function SetUmsatz_Netto($value) { $this->umsatz_netto=$value; }
  public function GetUmsatz_Netto() { return $this->umsatz_netto; }
  public function SetLieferdatum($value) { $this->lieferdatum=$value; }
  public function GetLieferdatum() { return $this->lieferdatum; }
  public function SetVertriebid($value) { $this->vertriebid=$value; }
  public function GetVertriebid() { return $this->vertriebid; }
  public function SetAktion($value) { $this->aktion=$value; }
  public function GetAktion() { return $this->aktion; }
  public function SetProvision($value) { $this->provision=$value; }
  public function GetProvision() { return $this->provision; }
  public function SetProvision_Summe($value) { $this->provision_summe=$value; }
  public function GetProvision_Summe() { return $this->provision_summe; }
  public function SetKeinsteuersatz($value) { $this->keinsteuersatz=$value; }
  public function GetKeinsteuersatz() { return $this->keinsteuersatz; }
  public function SetAnfrageid($value) { $this->anfrageid=$value; }
  public function GetAnfrageid() { return $this->anfrageid; }
  public function SetGruppe($value) { $this->gruppe=$value; }
  public function GetGruppe() { return $this->gruppe; }
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
  public function SetAuftragid($value) { $this->auftragid=$value; }
  public function GetAuftragid() { return $this->auftragid; }
  public function SetLieferid($value) { $this->lieferid=$value; }
  public function GetLieferid() { return $this->lieferid; }
  public function SetAnsprechpartnerid($value) { $this->ansprechpartnerid=$value; }
  public function GetAnsprechpartnerid() { return $this->ansprechpartnerid; }
  public function SetProjektfiliale($value) { $this->projektfiliale=$value; }
  public function GetProjektfiliale() { return $this->projektfiliale; }
  public function SetAbweichendebezeichnung($value) { $this->abweichendebezeichnung=$value; }
  public function GetAbweichendebezeichnung() { return $this->abweichendebezeichnung; }
  public function SetZuarchivieren($value) { $this->zuarchivieren=$value; }
  public function GetZuarchivieren() { return $this->zuarchivieren; }
  public function SetInternebezeichnung($value) { $this->internebezeichnung=$value; }
  public function GetInternebezeichnung() { return $this->internebezeichnung; }
  public function SetAngelegtam($value) { $this->angelegtam=$value; }
  public function GetAngelegtam() { return $this->angelegtam; }
  public function SetKopievon($value) { $this->kopievon=$value; }
  public function GetKopievon() { return $this->kopievon; }
  public function SetKopienummer($value) { $this->kopienummer=$value; }
  public function GetKopienummer() { return $this->kopienummer; }
  public function SetLieferdatumkw($value) { $this->lieferdatumkw=$value; }
  public function GetLieferdatumkw() { return $this->lieferdatumkw; }
  public function SetSprache($value) { $this->sprache=$value; }
  public function GetSprache() { return $this->sprache; }
  public function SetLiefergln($value) { $this->liefergln=$value; }
  public function GetLiefergln() { return $this->liefergln; }
  public function SetLieferemail($value) { $this->lieferemail=$value; }
  public function GetLieferemail() { return $this->lieferemail; }
  public function SetGln($value) { $this->gln=$value; }
  public function GetGln() { return $this->gln; }
  public function SetPlanedorderdate($value) { $this->planedorderdate=$value; }
  public function GetPlanedorderdate() { return $this->planedorderdate; }
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
  public function SetLiefertitel($value) { $this->liefertitel=$value; }
  public function GetLiefertitel() { return $this->liefertitel; }
  public function SetSkontobetrag($value) { $this->skontobetrag=$value; }
  public function GetSkontobetrag() { return $this->skontobetrag; }
  public function SetSkontoberechnet($value) { $this->skontoberechnet=$value; }
  public function GetSkontoberechnet() { return $this->skontoberechnet; }
  public function SetShop($value) { $this->shop=$value; }
  public function GetShop() { return $this->shop; }
  public function SetInternet($value) { $this->internet=$value; }
  public function GetInternet() { return $this->internet; }
  public function SetTransaktionsnummer($value) { $this->transaktionsnummer=$value; }
  public function GetTransaktionsnummer() { return $this->transaktionsnummer; }
  public function SetPackstation_Inhaber($value) { $this->packstation_inhaber=$value; }
  public function GetPackstation_Inhaber() { return $this->packstation_inhaber; }
  public function SetPackstation_Station($value) { $this->packstation_station=$value; }
  public function GetPackstation_Station() { return $this->packstation_station; }
  public function SetPackstation_Ident($value) { $this->packstation_ident=$value; }
  public function GetPackstation_Ident() { return $this->packstation_ident; }
  public function SetPackstation_Plz($value) { $this->packstation_plz=$value; }
  public function GetPackstation_Plz() { return $this->packstation_plz; }
  public function SetPackstation_Ort($value) { $this->packstation_ort=$value; }
  public function GetPackstation_Ort() { return $this->packstation_ort; }
  public function SetShopextid($value) { $this->shopextid=$value; }
  public function GetShopextid() { return $this->shopextid; }
  public function SetBundesstaat($value) { $this->bundesstaat=$value; }
  public function GetBundesstaat() { return $this->bundesstaat; }
  public function SetLieferbundesstaat($value) { $this->lieferbundesstaat=$value; }
  public function GetLieferbundesstaat() { return $this->lieferbundesstaat; }
  public function SetStandardlager($value) { $this->standardlager=$value; }
  public function GetStandardlager() { return $this->standardlager; }

}
