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

class ObjGenAuftrag
{

  private  $id;
  private  $datum;
  private  $art;
  private  $projekt;
  private  $belegnr;
  private  $internet;
  private  $bearbeiter;
  private  $angebot;
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
  private  $ust_inner;
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
  private  $firma;
  private  $versendet;
  private  $versendet_am;
  private  $versendet_per;
  private  $versendet_durch;
  private  $autoversand;
  private  $keinporto;
  private  $keinestornomail;
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
  private  $packstation_inhaber;
  private  $packstation_station;
  private  $packstation_ident;
  private  $packstation_plz;
  private  $packstation_ort;
  private  $autofreigabe;
  private  $freigabe;
  private  $nachbesserung;
  private  $gesamtsumme;
  private  $inbearbeitung;
  private  $abgeschlossen;
  private  $nachlieferung;
  private  $lager_ok;
  private  $porto_ok;
  private  $ust_ok;
  private  $check_ok;
  private  $vorkasse_ok;
  private  $nachnahme_ok;
  private  $reserviert_ok;
  private  $partnerid;
  private  $folgebestaetigung;
  private  $zahlungsmail;
  private  $stornogrund;
  private  $stornosonstiges;
  private  $stornorueckzahlung;
  private  $stornobetrag;
  private  $stornobankinhaber;
  private  $stornobankkonto;
  private  $stornobankblz;
  private  $stornobankbank;
  private  $stornogutschrift;
  private  $stornogutschriftbeleg;
  private  $stornowareerhalten;
  private  $stornomanuellebearbeitung;
  private  $stornokommentar;
  private  $stornobezahlt;
  private  $stornobezahltam;
  private  $stornobezahltvon;
  private  $stornoabgeschlossen;
  private  $stornorueckzahlungper;
  private  $stornowareerhaltenretour;
  private  $partnerausgezahlt;
  private  $partnerausgezahltam;
  private  $kennen;
  private  $logdatei;
  private  $keinetrackingmail;
  private  $zahlungsmailcounter;
  private  $rma;
  private  $transaktionsnummer;
  private  $vorabbezahltmarkieren;
  private  $deckungsbeitragcalc;
  private  $deckungsbeitrag;
  private  $erloes_netto;
  private  $umsatz_netto;
  private  $lieferdatum;
  private  $tatsaechlicheslieferdatum;
  private  $liefertermin_ok;
  private  $teillieferung_moeglich;
  private  $kreditlimit_ok;
  private  $kreditlimit_freigabe;
  private  $liefersperre_ok;
  private  $teillieferungvon;
  private  $teillieferungnummer;
  private  $vertriebid;
  private  $aktion;
  private  $provision;
  private  $provision_summe;
  private  $anfrageid;
  private  $gruppe;
  private  $shopextid;
  private  $shopextstatus;
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
  private  $shop;
  private  $steuersatz_normal;
  private  $steuersatz_zwischen;
  private  $steuersatz_ermaessigt;
  private  $steuersatz_starkermaessigt;
  private  $steuersatz_dienstleistung;
  private  $waehrung;
  private  $keinsteuersatz;
  private  $angebotid;
  private  $schreibschutz;
  private  $pdfarchiviert;
  private  $pdfarchiviertversion;
  private  $typ;
  private  $ohne_briefpapier;
  private  $auftragseingangper;
  private  $lieferid;
  private  $ansprechpartnerid;
  private  $systemfreitext;
  private  $projektfiliale;
  private  $lieferungtrotzsperre;
  private  $zuarchivieren;
  private  $internebezeichnung;
  private  $angelegtam;
  private  $saldo;
  private  $saldogeprueft;
  private  $lieferantenauftrag;
  private  $lieferant;
  private  $lieferdatumkw;
  private  $abweichendebezeichnung;
  private  $rabatteportofestschreiben;
  private  $sprache;
  private  $bundesland;
  private  $gln;
  private  $liefergln;
  private  $lieferemail;
  private  $rechnungid;
  private  $deliverythresholdvatid;
  private  $fastlane;
  private  $bearbeiterid;
  private  $kurs;
  private  $lieferantennummer;
  private  $lieferantkdrnummer;
  private  $ohne_artikeltext;
  private  $webid;
  private  $anzeigesteuer;
  private  $cronjobkommissionierung;
  private  $kostenstelle;
  private  $bodyzusatz;
  private  $lieferbedingung;
  private  $titel;
  private  $liefertitel;
  private  $standardlager;
  private  $skontobetrag;
  private  $skontoberechnet;
  private  $kommissionskonsignationslager;
  private  $extsoll;
  private  $bundesstaat;
  private  $lieferbundesstaat;
  private  $reservationdate;
  private  $kundennummer_buchhaltung;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `auftrag` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->datum=$result['datum'];
    $this->art=$result['art'];
    $this->projekt=$result['projekt'];
    $this->belegnr=$result['belegnr'];
    $this->internet=$result['internet'];
    $this->bearbeiter=$result['bearbeiter'];
    $this->angebot=$result['angebot'];
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
    $this->ust_inner=$result['ust_inner'];
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
    $this->firma=$result['firma'];
    $this->versendet=$result['versendet'];
    $this->versendet_am=$result['versendet_am'];
    $this->versendet_per=$result['versendet_per'];
    $this->versendet_durch=$result['versendet_durch'];
    $this->autoversand=$result['autoversand'];
    $this->keinporto=$result['keinporto'];
    $this->keinestornomail=$result['keinestornomail'];
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
    $this->packstation_inhaber=$result['packstation_inhaber'];
    $this->packstation_station=$result['packstation_station'];
    $this->packstation_ident=$result['packstation_ident'];
    $this->packstation_plz=$result['packstation_plz'];
    $this->packstation_ort=$result['packstation_ort'];
    $this->autofreigabe=$result['autofreigabe'];
    $this->freigabe=$result['freigabe'];
    $this->nachbesserung=$result['nachbesserung'];
    $this->gesamtsumme=$result['gesamtsumme'];
    $this->inbearbeitung=$result['inbearbeitung'];
    $this->abgeschlossen=$result['abgeschlossen'];
    $this->nachlieferung=$result['nachlieferung'];
    $this->lager_ok=$result['lager_ok'];
    $this->porto_ok=$result['porto_ok'];
    $this->ust_ok=$result['ust_ok'];
    $this->check_ok=$result['check_ok'];
    $this->vorkasse_ok=$result['vorkasse_ok'];
    $this->nachnahme_ok=$result['nachnahme_ok'];
    $this->reserviert_ok=$result['reserviert_ok'];
    $this->partnerid=$result['partnerid'];
    $this->folgebestaetigung=$result['folgebestaetigung'];
    $this->zahlungsmail=$result['zahlungsmail'];
    $this->stornogrund=$result['stornogrund'];
    $this->stornosonstiges=$result['stornosonstiges'];
    $this->stornorueckzahlung=$result['stornorueckzahlung'];
    $this->stornobetrag=$result['stornobetrag'];
    $this->stornobankinhaber=$result['stornobankinhaber'];
    $this->stornobankkonto=$result['stornobankkonto'];
    $this->stornobankblz=$result['stornobankblz'];
    $this->stornobankbank=$result['stornobankbank'];
    $this->stornogutschrift=$result['stornogutschrift'];
    $this->stornogutschriftbeleg=$result['stornogutschriftbeleg'];
    $this->stornowareerhalten=$result['stornowareerhalten'];
    $this->stornomanuellebearbeitung=$result['stornomanuellebearbeitung'];
    $this->stornokommentar=$result['stornokommentar'];
    $this->stornobezahlt=$result['stornobezahlt'];
    $this->stornobezahltam=$result['stornobezahltam'];
    $this->stornobezahltvon=$result['stornobezahltvon'];
    $this->stornoabgeschlossen=$result['stornoabgeschlossen'];
    $this->stornorueckzahlungper=$result['stornorueckzahlungper'];
    $this->stornowareerhaltenretour=$result['stornowareerhaltenretour'];
    $this->partnerausgezahlt=$result['partnerausgezahlt'];
    $this->partnerausgezahltam=$result['partnerausgezahltam'];
    $this->kennen=$result['kennen'];
    $this->logdatei=$result['logdatei'];
    $this->keinetrackingmail=$result['keinetrackingmail'];
    $this->zahlungsmailcounter=$result['zahlungsmailcounter'];
    $this->rma=$result['rma'];
    $this->transaktionsnummer=$result['transaktionsnummer'];
    $this->vorabbezahltmarkieren=$result['vorabbezahltmarkieren'];
    $this->deckungsbeitragcalc=$result['deckungsbeitragcalc'];
    $this->deckungsbeitrag=$result['deckungsbeitrag'];
    $this->erloes_netto=$result['erloes_netto'];
    $this->umsatz_netto=$result['umsatz_netto'];
    $this->lieferdatum=$result['lieferdatum'];
    $this->tatsaechlicheslieferdatum=$result['tatsaechlicheslieferdatum'];
    $this->liefertermin_ok=$result['liefertermin_ok'];
    $this->teillieferung_moeglich=$result['teillieferung_moeglich'];
    $this->kreditlimit_ok=$result['kreditlimit_ok'];
    $this->kreditlimit_freigabe=$result['kreditlimit_freigabe'];
    $this->liefersperre_ok=$result['liefersperre_ok'];
    $this->teillieferungvon=$result['teillieferungvon'];
    $this->teillieferungnummer=$result['teillieferungnummer'];
    $this->vertriebid=$result['vertriebid'];
    $this->aktion=$result['aktion'];
    $this->provision=$result['provision'];
    $this->provision_summe=$result['provision_summe'];
    $this->anfrageid=$result['anfrageid'];
    $this->gruppe=$result['gruppe'];
    $this->shopextid=$result['shopextid'];
    $this->shopextstatus=$result['shopextstatus'];
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
    $this->shop=$result['shop'];
    $this->steuersatz_normal=$result['steuersatz_normal'];
    $this->steuersatz_zwischen=$result['steuersatz_zwischen'];
    $this->steuersatz_ermaessigt=$result['steuersatz_ermaessigt'];
    $this->steuersatz_starkermaessigt=$result['steuersatz_starkermaessigt'];
    $this->steuersatz_dienstleistung=$result['steuersatz_dienstleistung'];
    $this->waehrung=$result['waehrung'];
    $this->keinsteuersatz=$result['keinsteuersatz'];
    $this->angebotid=$result['angebotid'];
    $this->schreibschutz=$result['schreibschutz'];
    $this->pdfarchiviert=$result['pdfarchiviert'];
    $this->pdfarchiviertversion=$result['pdfarchiviertversion'];
    $this->typ=$result['typ'];
    $this->ohne_briefpapier=$result['ohne_briefpapier'];
    $this->auftragseingangper=$result['auftragseingangper'];
    $this->lieferid=$result['lieferid'];
    $this->ansprechpartnerid=$result['ansprechpartnerid'];
    $this->systemfreitext=$result['systemfreitext'];
    $this->projektfiliale=$result['projektfiliale'];
    $this->lieferungtrotzsperre=$result['lieferungtrotzsperre'];
    $this->zuarchivieren=$result['zuarchivieren'];
    $this->internebezeichnung=$result['internebezeichnung'];
    $this->angelegtam=$result['angelegtam'];
    $this->saldo=$result['saldo'];
    $this->saldogeprueft=$result['saldogeprueft'];
    $this->lieferantenauftrag=$result['lieferantenauftrag'];
    $this->lieferant=$result['lieferant'];
    $this->lieferdatumkw=$result['lieferdatumkw'];
    $this->abweichendebezeichnung=$result['abweichendebezeichnung'];
    $this->rabatteportofestschreiben=$result['rabatteportofestschreiben'];
    $this->sprache=$result['sprache'];
    $this->bundesland=$result['bundesland'];
    $this->gln=$result['gln'];
    $this->liefergln=$result['liefergln'];
    $this->lieferemail=$result['lieferemail'];
    $this->rechnungid=$result['rechnungid'];
    $this->deliverythresholdvatid=$result['deliverythresholdvatid'];
    $this->fastlane=$result['fastlane'];
    $this->bearbeiterid=$result['bearbeiterid'];
    $this->kurs=$result['kurs'];
    $this->lieferantennummer=$result['lieferantennummer'];
    $this->lieferantkdrnummer=$result['lieferantkdrnummer'];
    $this->ohne_artikeltext=$result['ohne_artikeltext'];
    $this->webid=$result['webid'];
    $this->anzeigesteuer=$result['anzeigesteuer'];
    $this->cronjobkommissionierung=$result['cronjobkommissionierung'];
    $this->kostenstelle=$result['kostenstelle'];
    $this->bodyzusatz=$result['bodyzusatz'];
    $this->lieferbedingung=$result['lieferbedingung'];
    $this->titel=$result['titel'];
    $this->liefertitel=$result['liefertitel'];
    $this->standardlager=$result['standardlager'];
    $this->skontobetrag=$result['skontobetrag'];
    $this->skontoberechnet=$result['skontoberechnet'];
    $this->kommissionskonsignationslager=$result['kommissionskonsignationslager'];
    $this->extsoll=$result['extsoll'];
    $this->bundesstaat=$result['bundesstaat'];
    $this->lieferbundesstaat=$result['lieferbundesstaat'];
    $this->reservationdate=$result['reservationdate'];
    $this->kundennummer_buchhaltung=$result['kundennummer_buchhaltung'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `auftrag` (`id`,`datum`,`art`,`projekt`,`belegnr`,`internet`,`bearbeiter`,`angebot`,`freitext`,`internebemerkung`,`status`,`adresse`,`name`,`abteilung`,`unterabteilung`,`strasse`,`adresszusatz`,`ansprechpartner`,`plz`,`ort`,`land`,`ustid`,`ust_befreit`,`ust_inner`,`email`,`telefon`,`telefax`,`betreff`,`kundennummer`,`versandart`,`vertrieb`,`zahlungsweise`,`zahlungszieltage`,`zahlungszieltageskonto`,`zahlungszielskonto`,`bank_inhaber`,`bank_institut`,`bank_blz`,`bank_konto`,`kreditkarte_typ`,`kreditkarte_inhaber`,`kreditkarte_nummer`,`kreditkarte_pruefnummer`,`kreditkarte_monat`,`kreditkarte_jahr`,`firma`,`versendet`,`versendet_am`,`versendet_per`,`versendet_durch`,`autoversand`,`keinporto`,`keinestornomail`,`abweichendelieferadresse`,`liefername`,`lieferabteilung`,`lieferunterabteilung`,`lieferland`,`lieferstrasse`,`lieferort`,`lieferplz`,`lieferadresszusatz`,`lieferansprechpartner`,`packstation_inhaber`,`packstation_station`,`packstation_ident`,`packstation_plz`,`packstation_ort`,`autofreigabe`,`freigabe`,`nachbesserung`,`gesamtsumme`,`inbearbeitung`,`abgeschlossen`,`nachlieferung`,`lager_ok`,`porto_ok`,`ust_ok`,`check_ok`,`vorkasse_ok`,`nachnahme_ok`,`reserviert_ok`,`partnerid`,`folgebestaetigung`,`zahlungsmail`,`stornogrund`,`stornosonstiges`,`stornorueckzahlung`,`stornobetrag`,`stornobankinhaber`,`stornobankkonto`,`stornobankblz`,`stornobankbank`,`stornogutschrift`,`stornogutschriftbeleg`,`stornowareerhalten`,`stornomanuellebearbeitung`,`stornokommentar`,`stornobezahlt`,`stornobezahltam`,`stornobezahltvon`,`stornoabgeschlossen`,`stornorueckzahlungper`,`stornowareerhaltenretour`,`partnerausgezahlt`,`partnerausgezahltam`,`kennen`,`logdatei`,`keinetrackingmail`,`zahlungsmailcounter`,`rma`,`transaktionsnummer`,`vorabbezahltmarkieren`,`deckungsbeitragcalc`,`deckungsbeitrag`,`erloes_netto`,`umsatz_netto`,`lieferdatum`,`tatsaechlicheslieferdatum`,`liefertermin_ok`,`teillieferung_moeglich`,`kreditlimit_ok`,`kreditlimit_freigabe`,`liefersperre_ok`,`teillieferungvon`,`teillieferungnummer`,`vertriebid`,`aktion`,`provision`,`provision_summe`,`anfrageid`,`gruppe`,`shopextid`,`shopextstatus`,`ihrebestellnummer`,`anschreiben`,`usereditid`,`useredittimestamp`,`realrabatt`,`rabatt`,`einzugsdatum`,`rabatt1`,`rabatt2`,`rabatt3`,`rabatt4`,`rabatt5`,`shop`,`steuersatz_normal`,`steuersatz_zwischen`,`steuersatz_ermaessigt`,`steuersatz_starkermaessigt`,`steuersatz_dienstleistung`,`waehrung`,`keinsteuersatz`,`angebotid`,`schreibschutz`,`pdfarchiviert`,`pdfarchiviertversion`,`typ`,`ohne_briefpapier`,`auftragseingangper`,`lieferid`,`ansprechpartnerid`,`systemfreitext`,`projektfiliale`,`lieferungtrotzsperre`,`zuarchivieren`,`internebezeichnung`,`angelegtam`,`saldo`,`saldogeprueft`,`lieferantenauftrag`,`lieferant`,`lieferdatumkw`,`abweichendebezeichnung`,`rabatteportofestschreiben`,`sprache`,`bundesland`,`gln`,`liefergln`,`lieferemail`,`rechnungid`,`deliverythresholdvatid`,`fastlane`,`bearbeiterid`,`kurs`,`lieferantennummer`,`lieferantkdrnummer`,`ohne_artikeltext`,`webid`,`anzeigesteuer`,`cronjobkommissionierung`,`kostenstelle`,`bodyzusatz`,`lieferbedingung`,`titel`,`liefertitel`,`standardlager`,`skontobetrag`,`skontoberechnet`,`kommissionskonsignationslager`,`extsoll`,`bundesstaat`,`lieferbundesstaat`,`reservationdate`,`kundennummer_buchhaltung`)
      VALUES(NULL,'{$this->datum}','{$this->art}','{$this->projekt}','{$this->belegnr}','{$this->internet}','{$this->bearbeiter}','{$this->angebot}','{$this->freitext}','{$this->internebemerkung}','{$this->status}','{$this->adresse}','{$this->name}','{$this->abteilung}','{$this->unterabteilung}','{$this->strasse}','{$this->adresszusatz}','{$this->ansprechpartner}','{$this->plz}','{$this->ort}','{$this->land}','{$this->ustid}','{$this->ust_befreit}','{$this->ust_inner}','{$this->email}','{$this->telefon}','{$this->telefax}','{$this->betreff}','{$this->kundennummer}','{$this->versandart}','{$this->vertrieb}','{$this->zahlungsweise}','{$this->zahlungszieltage}','{$this->zahlungszieltageskonto}','{$this->zahlungszielskonto}','{$this->bank_inhaber}','{$this->bank_institut}','{$this->bank_blz}','{$this->bank_konto}','{$this->kreditkarte_typ}','{$this->kreditkarte_inhaber}','{$this->kreditkarte_nummer}','{$this->kreditkarte_pruefnummer}','{$this->kreditkarte_monat}','{$this->kreditkarte_jahr}','{$this->firma}','{$this->versendet}','{$this->versendet_am}','{$this->versendet_per}','{$this->versendet_durch}','{$this->autoversand}','{$this->keinporto}','{$this->keinestornomail}','{$this->abweichendelieferadresse}','{$this->liefername}','{$this->lieferabteilung}','{$this->lieferunterabteilung}','{$this->lieferland}','{$this->lieferstrasse}','{$this->lieferort}','{$this->lieferplz}','{$this->lieferadresszusatz}','{$this->lieferansprechpartner}','{$this->packstation_inhaber}','{$this->packstation_station}','{$this->packstation_ident}','{$this->packstation_plz}','{$this->packstation_ort}','{$this->autofreigabe}','{$this->freigabe}','{$this->nachbesserung}','{$this->gesamtsumme}','{$this->inbearbeitung}','{$this->abgeschlossen}','{$this->nachlieferung}','{$this->lager_ok}','{$this->porto_ok}','{$this->ust_ok}','{$this->check_ok}','{$this->vorkasse_ok}','{$this->nachnahme_ok}','{$this->reserviert_ok}','{$this->partnerid}','{$this->folgebestaetigung}','{$this->zahlungsmail}','{$this->stornogrund}','{$this->stornosonstiges}','{$this->stornorueckzahlung}','{$this->stornobetrag}','{$this->stornobankinhaber}','{$this->stornobankkonto}','{$this->stornobankblz}','{$this->stornobankbank}','{$this->stornogutschrift}','{$this->stornogutschriftbeleg}','{$this->stornowareerhalten}','{$this->stornomanuellebearbeitung}','{$this->stornokommentar}','{$this->stornobezahlt}','{$this->stornobezahltam}','{$this->stornobezahltvon}','{$this->stornoabgeschlossen}','{$this->stornorueckzahlungper}','{$this->stornowareerhaltenretour}','{$this->partnerausgezahlt}','{$this->partnerausgezahltam}','{$this->kennen}','{$this->logdatei}','{$this->keinetrackingmail}','{$this->zahlungsmailcounter}','{$this->rma}','{$this->transaktionsnummer}','{$this->vorabbezahltmarkieren}','{$this->deckungsbeitragcalc}','{$this->deckungsbeitrag}','{$this->erloes_netto}','{$this->umsatz_netto}','{$this->lieferdatum}','{$this->tatsaechlicheslieferdatum}','{$this->liefertermin_ok}','{$this->teillieferung_moeglich}','{$this->kreditlimit_ok}','{$this->kreditlimit_freigabe}','{$this->liefersperre_ok}','{$this->teillieferungvon}','{$this->teillieferungnummer}','{$this->vertriebid}','{$this->aktion}','{$this->provision}','{$this->provision_summe}','{$this->anfrageid}','{$this->gruppe}','{$this->shopextid}','{$this->shopextstatus}','{$this->ihrebestellnummer}','{$this->anschreiben}','{$this->usereditid}','{$this->useredittimestamp}','{$this->realrabatt}','{$this->rabatt}','{$this->einzugsdatum}','{$this->rabatt1}','{$this->rabatt2}','{$this->rabatt3}','{$this->rabatt4}','{$this->rabatt5}','{$this->shop}','{$this->steuersatz_normal}','{$this->steuersatz_zwischen}','{$this->steuersatz_ermaessigt}','{$this->steuersatz_starkermaessigt}','{$this->steuersatz_dienstleistung}','{$this->waehrung}','{$this->keinsteuersatz}','{$this->angebotid}','{$this->schreibschutz}','{$this->pdfarchiviert}','{$this->pdfarchiviertversion}','{$this->typ}','{$this->ohne_briefpapier}','{$this->auftragseingangper}','{$this->lieferid}','{$this->ansprechpartnerid}','{$this->systemfreitext}','{$this->projektfiliale}','{$this->lieferungtrotzsperre}','{$this->zuarchivieren}','{$this->internebezeichnung}','{$this->angelegtam}','{$this->saldo}','{$this->saldogeprueft}','{$this->lieferantenauftrag}','{$this->lieferant}','{$this->lieferdatumkw}','{$this->abweichendebezeichnung}','{$this->rabatteportofestschreiben}','{$this->sprache}','{$this->bundesland}','{$this->gln}','{$this->liefergln}','{$this->lieferemail}','{$this->rechnungid}','{$this->deliverythresholdvatid}','{$this->fastlane}','{$this->bearbeiterid}','{$this->kurs}','{$this->lieferantennummer}','{$this->lieferantkdrnummer}','{$this->ohne_artikeltext}','{$this->webid}','{$this->anzeigesteuer}','{$this->cronjobkommissionierung}','{$this->kostenstelle}','{$this->bodyzusatz}','{$this->lieferbedingung}','{$this->titel}','{$this->liefertitel}','{$this->standardlager}','{$this->skontobetrag}','{$this->skontoberechnet}','{$this->kommissionskonsignationslager}','{$this->extsoll}','{$this->bundesstaat}','{$this->lieferbundesstaat}','{$this->reservationdate}','{$this->kundennummer_buchhaltung}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `auftrag` SET
      `datum`='{$this->datum}',
      `art`='{$this->art}',
      `projekt`='{$this->projekt}',
      `belegnr`='{$this->belegnr}',
      `internet`='{$this->internet}',
      `bearbeiter`='{$this->bearbeiter}',
      `angebot`='{$this->angebot}',
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
      `ust_inner`='{$this->ust_inner}',
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
      `firma`='{$this->firma}',
      `versendet`='{$this->versendet}',
      `versendet_am`='{$this->versendet_am}',
      `versendet_per`='{$this->versendet_per}',
      `versendet_durch`='{$this->versendet_durch}',
      `autoversand`='{$this->autoversand}',
      `keinporto`='{$this->keinporto}',
      `keinestornomail`='{$this->keinestornomail}',
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
      `packstation_inhaber`='{$this->packstation_inhaber}',
      `packstation_station`='{$this->packstation_station}',
      `packstation_ident`='{$this->packstation_ident}',
      `packstation_plz`='{$this->packstation_plz}',
      `packstation_ort`='{$this->packstation_ort}',
      `autofreigabe`='{$this->autofreigabe}',
      `freigabe`='{$this->freigabe}',
      `nachbesserung`='{$this->nachbesserung}',
      `gesamtsumme`='{$this->gesamtsumme}',
      `inbearbeitung`='{$this->inbearbeitung}',
      `abgeschlossen`='{$this->abgeschlossen}',
      `nachlieferung`='{$this->nachlieferung}',
      `lager_ok`='{$this->lager_ok}',
      `porto_ok`='{$this->porto_ok}',
      `ust_ok`='{$this->ust_ok}',
      `check_ok`='{$this->check_ok}',
      `vorkasse_ok`='{$this->vorkasse_ok}',
      `nachnahme_ok`='{$this->nachnahme_ok}',
      `reserviert_ok`='{$this->reserviert_ok}',
      `partnerid`='{$this->partnerid}',
      `folgebestaetigung`='{$this->folgebestaetigung}',
      `zahlungsmail`='{$this->zahlungsmail}',
      `stornogrund`='{$this->stornogrund}',
      `stornosonstiges`='{$this->stornosonstiges}',
      `stornorueckzahlung`='{$this->stornorueckzahlung}',
      `stornobetrag`='{$this->stornobetrag}',
      `stornobankinhaber`='{$this->stornobankinhaber}',
      `stornobankkonto`='{$this->stornobankkonto}',
      `stornobankblz`='{$this->stornobankblz}',
      `stornobankbank`='{$this->stornobankbank}',
      `stornogutschrift`='{$this->stornogutschrift}',
      `stornogutschriftbeleg`='{$this->stornogutschriftbeleg}',
      `stornowareerhalten`='{$this->stornowareerhalten}',
      `stornomanuellebearbeitung`='{$this->stornomanuellebearbeitung}',
      `stornokommentar`='{$this->stornokommentar}',
      `stornobezahlt`='{$this->stornobezahlt}',
      `stornobezahltam`='{$this->stornobezahltam}',
      `stornobezahltvon`='{$this->stornobezahltvon}',
      `stornoabgeschlossen`='{$this->stornoabgeschlossen}',
      `stornorueckzahlungper`='{$this->stornorueckzahlungper}',
      `stornowareerhaltenretour`='{$this->stornowareerhaltenretour}',
      `partnerausgezahlt`='{$this->partnerausgezahlt}',
      `partnerausgezahltam`='{$this->partnerausgezahltam}',
      `kennen`='{$this->kennen}',
      `logdatei`='{$this->logdatei}',
      `keinetrackingmail`='{$this->keinetrackingmail}',
      `zahlungsmailcounter`='{$this->zahlungsmailcounter}',
      `rma`='{$this->rma}',
      `transaktionsnummer`='{$this->transaktionsnummer}',
      `vorabbezahltmarkieren`='{$this->vorabbezahltmarkieren}',
      `deckungsbeitragcalc`='{$this->deckungsbeitragcalc}',
      `deckungsbeitrag`='{$this->deckungsbeitrag}',
      `erloes_netto`='{$this->erloes_netto}',
      `umsatz_netto`='{$this->umsatz_netto}',
      `lieferdatum`='{$this->lieferdatum}',
      `tatsaechlicheslieferdatum`='{$this->tatsaechlicheslieferdatum}',
      `liefertermin_ok`='{$this->liefertermin_ok}',
      `teillieferung_moeglich`='{$this->teillieferung_moeglich}',
      `kreditlimit_ok`='{$this->kreditlimit_ok}',
      `kreditlimit_freigabe`='{$this->kreditlimit_freigabe}',
      `liefersperre_ok`='{$this->liefersperre_ok}',
      `teillieferungvon`='{$this->teillieferungvon}',
      `teillieferungnummer`='{$this->teillieferungnummer}',
      `vertriebid`='{$this->vertriebid}',
      `aktion`='{$this->aktion}',
      `provision`='{$this->provision}',
      `provision_summe`='{$this->provision_summe}',
      `anfrageid`='{$this->anfrageid}',
      `gruppe`='{$this->gruppe}',
      `shopextid`='{$this->shopextid}',
      `shopextstatus`='{$this->shopextstatus}',
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
      `shop`='{$this->shop}',
      `steuersatz_normal`='{$this->steuersatz_normal}',
      `steuersatz_zwischen`='{$this->steuersatz_zwischen}',
      `steuersatz_ermaessigt`='{$this->steuersatz_ermaessigt}',
      `steuersatz_starkermaessigt`='{$this->steuersatz_starkermaessigt}',
      `steuersatz_dienstleistung`='{$this->steuersatz_dienstleistung}',
      `waehrung`='{$this->waehrung}',
      `keinsteuersatz`='{$this->keinsteuersatz}',
      `angebotid`='{$this->angebotid}',
      `schreibschutz`='{$this->schreibschutz}',
      `pdfarchiviert`='{$this->pdfarchiviert}',
      `pdfarchiviertversion`='{$this->pdfarchiviertversion}',
      `typ`='{$this->typ}',
      `ohne_briefpapier`='{$this->ohne_briefpapier}',
      `auftragseingangper`='{$this->auftragseingangper}',
      `lieferid`='{$this->lieferid}',
      `ansprechpartnerid`='{$this->ansprechpartnerid}',
      `systemfreitext`='{$this->systemfreitext}',
      `projektfiliale`='{$this->projektfiliale}',
      `lieferungtrotzsperre`='{$this->lieferungtrotzsperre}',
      `zuarchivieren`='{$this->zuarchivieren}',
      `internebezeichnung`='{$this->internebezeichnung}',
      `angelegtam`='{$this->angelegtam}',
      `saldo`='{$this->saldo}',
      `saldogeprueft`='{$this->saldogeprueft}',
      `lieferantenauftrag`='{$this->lieferantenauftrag}',
      `lieferant`='{$this->lieferant}',
      `lieferdatumkw`='{$this->lieferdatumkw}',
      `abweichendebezeichnung`='{$this->abweichendebezeichnung}',
      `rabatteportofestschreiben`='{$this->rabatteportofestschreiben}',
      `sprache`='{$this->sprache}',
      `bundesland`='{$this->bundesland}',
      `gln`='{$this->gln}',
      `liefergln`='{$this->liefergln}',
      `lieferemail`='{$this->lieferemail}',
      `rechnungid`='{$this->rechnungid}',
      `deliverythresholdvatid`='{$this->deliverythresholdvatid}',
      `fastlane`='{$this->fastlane}',
      `bearbeiterid`='{$this->bearbeiterid}',
      `kurs`='{$this->kurs}',
      `lieferantennummer`='{$this->lieferantennummer}',
      `lieferantkdrnummer`='{$this->lieferantkdrnummer}',
      `ohne_artikeltext`='{$this->ohne_artikeltext}',
      `webid`='{$this->webid}',
      `anzeigesteuer`='{$this->anzeigesteuer}',
      `cronjobkommissionierung`='{$this->cronjobkommissionierung}',
      `kostenstelle`='{$this->kostenstelle}',
      `bodyzusatz`='{$this->bodyzusatz}',
      `lieferbedingung`='{$this->lieferbedingung}',
      `titel`='{$this->titel}',
      `liefertitel`='{$this->liefertitel}',
      `standardlager`='{$this->standardlager}',
      `skontobetrag`='{$this->skontobetrag}',
      `skontoberechnet`='{$this->skontoberechnet}',
      `kommissionskonsignationslager`='{$this->kommissionskonsignationslager}',
      `extsoll`='{$this->extsoll}',
      `bundesstaat`='{$this->bundesstaat}',
      `lieferbundesstaat`='{$this->lieferbundesstaat}',
      `reservationdate`='{$this->reservationdate}',
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

    $sql = "DELETE FROM `auftrag` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->datum='';
    $this->art='';
    $this->projekt='';
    $this->belegnr='';
    $this->internet='';
    $this->bearbeiter='';
    $this->angebot='';
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
    $this->ust_inner='';
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
    $this->firma='';
    $this->versendet='';
    $this->versendet_am='';
    $this->versendet_per='';
    $this->versendet_durch='';
    $this->autoversand='';
    $this->keinporto='';
    $this->keinestornomail='';
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
    $this->packstation_inhaber='';
    $this->packstation_station='';
    $this->packstation_ident='';
    $this->packstation_plz='';
    $this->packstation_ort='';
    $this->autofreigabe='';
    $this->freigabe='';
    $this->nachbesserung='';
    $this->gesamtsumme='';
    $this->inbearbeitung='';
    $this->abgeschlossen='';
    $this->nachlieferung='';
    $this->lager_ok='';
    $this->porto_ok='';
    $this->ust_ok='';
    $this->check_ok='';
    $this->vorkasse_ok='';
    $this->nachnahme_ok='';
    $this->reserviert_ok='';
    $this->partnerid='';
    $this->folgebestaetigung='';
    $this->zahlungsmail='';
    $this->stornogrund='';
    $this->stornosonstiges='';
    $this->stornorueckzahlung='';
    $this->stornobetrag='';
    $this->stornobankinhaber='';
    $this->stornobankkonto='';
    $this->stornobankblz='';
    $this->stornobankbank='';
    $this->stornogutschrift='';
    $this->stornogutschriftbeleg='';
    $this->stornowareerhalten='';
    $this->stornomanuellebearbeitung='';
    $this->stornokommentar='';
    $this->stornobezahlt='';
    $this->stornobezahltam='';
    $this->stornobezahltvon='';
    $this->stornoabgeschlossen='';
    $this->stornorueckzahlungper='';
    $this->stornowareerhaltenretour='';
    $this->partnerausgezahlt='';
    $this->partnerausgezahltam='';
    $this->kennen='';
    $this->logdatei='';
    $this->keinetrackingmail='';
    $this->zahlungsmailcounter='';
    $this->rma='';
    $this->transaktionsnummer='';
    $this->vorabbezahltmarkieren='';
    $this->deckungsbeitragcalc='';
    $this->deckungsbeitrag='';
    $this->erloes_netto='';
    $this->umsatz_netto='';
    $this->lieferdatum='';
    $this->tatsaechlicheslieferdatum='';
    $this->liefertermin_ok='';
    $this->teillieferung_moeglich='';
    $this->kreditlimit_ok='';
    $this->kreditlimit_freigabe='';
    $this->liefersperre_ok='';
    $this->teillieferungvon='';
    $this->teillieferungnummer='';
    $this->vertriebid='';
    $this->aktion='';
    $this->provision='';
    $this->provision_summe='';
    $this->anfrageid='';
    $this->gruppe='';
    $this->shopextid='';
    $this->shopextstatus='';
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
    $this->shop='';
    $this->steuersatz_normal='';
    $this->steuersatz_zwischen='';
    $this->steuersatz_ermaessigt='';
    $this->steuersatz_starkermaessigt='';
    $this->steuersatz_dienstleistung='';
    $this->waehrung='';
    $this->keinsteuersatz='';
    $this->angebotid='';
    $this->schreibschutz='';
    $this->pdfarchiviert='';
    $this->pdfarchiviertversion='';
    $this->typ='';
    $this->ohne_briefpapier='';
    $this->auftragseingangper='';
    $this->lieferid='';
    $this->ansprechpartnerid='';
    $this->systemfreitext='';
    $this->projektfiliale='';
    $this->lieferungtrotzsperre='';
    $this->zuarchivieren='';
    $this->internebezeichnung='';
    $this->angelegtam='';
    $this->saldo='';
    $this->saldogeprueft='';
    $this->lieferantenauftrag='';
    $this->lieferant='';
    $this->lieferdatumkw='';
    $this->abweichendebezeichnung='';
    $this->rabatteportofestschreiben='';
    $this->sprache='';
    $this->bundesland='';
    $this->gln='';
    $this->liefergln='';
    $this->lieferemail='';
    $this->rechnungid='';
    $this->deliverythresholdvatid='';
    $this->fastlane='';
    $this->bearbeiterid='';
    $this->kurs='';
    $this->lieferantennummer='';
    $this->lieferantkdrnummer='';
    $this->ohne_artikeltext='';
    $this->webid='';
    $this->anzeigesteuer='';
    $this->cronjobkommissionierung='';
    $this->kostenstelle='';
    $this->bodyzusatz='';
    $this->lieferbedingung='';
    $this->titel='';
    $this->liefertitel='';
    $this->standardlager='';
    $this->skontobetrag='';
    $this->skontoberechnet='';
    $this->kommissionskonsignationslager='';
    $this->extsoll='';
    $this->bundesstaat='';
    $this->lieferbundesstaat='';
    $this->reservationdate='';
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
  public function SetArt($value) { $this->art=$value; }
  public function GetArt() { return $this->art; }
  public function SetProjekt($value) { $this->projekt=$value; }
  public function GetProjekt() { return $this->projekt; }
  public function SetBelegnr($value) { $this->belegnr=$value; }
  public function GetBelegnr() { return $this->belegnr; }
  public function SetInternet($value) { $this->internet=$value; }
  public function GetInternet() { return $this->internet; }
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
  public function SetUst_Inner($value) { $this->ust_inner=$value; }
  public function GetUst_Inner() { return $this->ust_inner; }
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
  public function SetAutoversand($value) { $this->autoversand=$value; }
  public function GetAutoversand() { return $this->autoversand; }
  public function SetKeinporto($value) { $this->keinporto=$value; }
  public function GetKeinporto() { return $this->keinporto; }
  public function SetKeinestornomail($value) { $this->keinestornomail=$value; }
  public function GetKeinestornomail() { return $this->keinestornomail; }
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
  public function SetAutofreigabe($value) { $this->autofreigabe=$value; }
  public function GetAutofreigabe() { return $this->autofreigabe; }
  public function SetFreigabe($value) { $this->freigabe=$value; }
  public function GetFreigabe() { return $this->freigabe; }
  public function SetNachbesserung($value) { $this->nachbesserung=$value; }
  public function GetNachbesserung() { return $this->nachbesserung; }
  public function SetGesamtsumme($value) { $this->gesamtsumme=$value; }
  public function GetGesamtsumme() { return $this->gesamtsumme; }
  public function SetInbearbeitung($value) { $this->inbearbeitung=$value; }
  public function GetInbearbeitung() { return $this->inbearbeitung; }
  public function SetAbgeschlossen($value) { $this->abgeschlossen=$value; }
  public function GetAbgeschlossen() { return $this->abgeschlossen; }
  public function SetNachlieferung($value) { $this->nachlieferung=$value; }
  public function GetNachlieferung() { return $this->nachlieferung; }
  public function SetLager_Ok($value) { $this->lager_ok=$value; }
  public function GetLager_Ok() { return $this->lager_ok; }
  public function SetPorto_Ok($value) { $this->porto_ok=$value; }
  public function GetPorto_Ok() { return $this->porto_ok; }
  public function SetUst_Ok($value) { $this->ust_ok=$value; }
  public function GetUst_Ok() { return $this->ust_ok; }
  public function SetCheck_Ok($value) { $this->check_ok=$value; }
  public function GetCheck_Ok() { return $this->check_ok; }
  public function SetVorkasse_Ok($value) { $this->vorkasse_ok=$value; }
  public function GetVorkasse_Ok() { return $this->vorkasse_ok; }
  public function SetNachnahme_Ok($value) { $this->nachnahme_ok=$value; }
  public function GetNachnahme_Ok() { return $this->nachnahme_ok; }
  public function SetReserviert_Ok($value) { $this->reserviert_ok=$value; }
  public function GetReserviert_Ok() { return $this->reserviert_ok; }
  public function SetPartnerid($value) { $this->partnerid=$value; }
  public function GetPartnerid() { return $this->partnerid; }
  public function SetFolgebestaetigung($value) { $this->folgebestaetigung=$value; }
  public function GetFolgebestaetigung() { return $this->folgebestaetigung; }
  public function SetZahlungsmail($value) { $this->zahlungsmail=$value; }
  public function GetZahlungsmail() { return $this->zahlungsmail; }
  public function SetStornogrund($value) { $this->stornogrund=$value; }
  public function GetStornogrund() { return $this->stornogrund; }
  public function SetStornosonstiges($value) { $this->stornosonstiges=$value; }
  public function GetStornosonstiges() { return $this->stornosonstiges; }
  public function SetStornorueckzahlung($value) { $this->stornorueckzahlung=$value; }
  public function GetStornorueckzahlung() { return $this->stornorueckzahlung; }
  public function SetStornobetrag($value) { $this->stornobetrag=$value; }
  public function GetStornobetrag() { return $this->stornobetrag; }
  public function SetStornobankinhaber($value) { $this->stornobankinhaber=$value; }
  public function GetStornobankinhaber() { return $this->stornobankinhaber; }
  public function SetStornobankkonto($value) { $this->stornobankkonto=$value; }
  public function GetStornobankkonto() { return $this->stornobankkonto; }
  public function SetStornobankblz($value) { $this->stornobankblz=$value; }
  public function GetStornobankblz() { return $this->stornobankblz; }
  public function SetStornobankbank($value) { $this->stornobankbank=$value; }
  public function GetStornobankbank() { return $this->stornobankbank; }
  public function SetStornogutschrift($value) { $this->stornogutschrift=$value; }
  public function GetStornogutschrift() { return $this->stornogutschrift; }
  public function SetStornogutschriftbeleg($value) { $this->stornogutschriftbeleg=$value; }
  public function GetStornogutschriftbeleg() { return $this->stornogutschriftbeleg; }
  public function SetStornowareerhalten($value) { $this->stornowareerhalten=$value; }
  public function GetStornowareerhalten() { return $this->stornowareerhalten; }
  public function SetStornomanuellebearbeitung($value) { $this->stornomanuellebearbeitung=$value; }
  public function GetStornomanuellebearbeitung() { return $this->stornomanuellebearbeitung; }
  public function SetStornokommentar($value) { $this->stornokommentar=$value; }
  public function GetStornokommentar() { return $this->stornokommentar; }
  public function SetStornobezahlt($value) { $this->stornobezahlt=$value; }
  public function GetStornobezahlt() { return $this->stornobezahlt; }
  public function SetStornobezahltam($value) { $this->stornobezahltam=$value; }
  public function GetStornobezahltam() { return $this->stornobezahltam; }
  public function SetStornobezahltvon($value) { $this->stornobezahltvon=$value; }
  public function GetStornobezahltvon() { return $this->stornobezahltvon; }
  public function SetStornoabgeschlossen($value) { $this->stornoabgeschlossen=$value; }
  public function GetStornoabgeschlossen() { return $this->stornoabgeschlossen; }
  public function SetStornorueckzahlungper($value) { $this->stornorueckzahlungper=$value; }
  public function GetStornorueckzahlungper() { return $this->stornorueckzahlungper; }
  public function SetStornowareerhaltenretour($value) { $this->stornowareerhaltenretour=$value; }
  public function GetStornowareerhaltenretour() { return $this->stornowareerhaltenretour; }
  public function SetPartnerausgezahlt($value) { $this->partnerausgezahlt=$value; }
  public function GetPartnerausgezahlt() { return $this->partnerausgezahlt; }
  public function SetPartnerausgezahltam($value) { $this->partnerausgezahltam=$value; }
  public function GetPartnerausgezahltam() { return $this->partnerausgezahltam; }
  public function SetKennen($value) { $this->kennen=$value; }
  public function GetKennen() { return $this->kennen; }
  public function SetLogdatei($value) { $this->logdatei=$value; }
  public function GetLogdatei() { return $this->logdatei; }
  public function SetKeinetrackingmail($value) { $this->keinetrackingmail=$value; }
  public function GetKeinetrackingmail() { return $this->keinetrackingmail; }
  public function SetZahlungsmailcounter($value) { $this->zahlungsmailcounter=$value; }
  public function GetZahlungsmailcounter() { return $this->zahlungsmailcounter; }
  public function SetRma($value) { $this->rma=$value; }
  public function GetRma() { return $this->rma; }
  public function SetTransaktionsnummer($value) { $this->transaktionsnummer=$value; }
  public function GetTransaktionsnummer() { return $this->transaktionsnummer; }
  public function SetVorabbezahltmarkieren($value) { $this->vorabbezahltmarkieren=$value; }
  public function GetVorabbezahltmarkieren() { return $this->vorabbezahltmarkieren; }
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
  public function SetTatsaechlicheslieferdatum($value) { $this->tatsaechlicheslieferdatum=$value; }
  public function GetTatsaechlicheslieferdatum() { return $this->tatsaechlicheslieferdatum; }
  public function SetLiefertermin_Ok($value) { $this->liefertermin_ok=$value; }
  public function GetLiefertermin_Ok() { return $this->liefertermin_ok; }
  public function SetTeillieferung_Moeglich($value) { $this->teillieferung_moeglich=$value; }
  public function GetTeillieferung_Moeglich() { return $this->teillieferung_moeglich; }
  public function SetKreditlimit_Ok($value) { $this->kreditlimit_ok=$value; }
  public function GetKreditlimit_Ok() { return $this->kreditlimit_ok; }
  public function SetKreditlimit_Freigabe($value) { $this->kreditlimit_freigabe=$value; }
  public function GetKreditlimit_Freigabe() { return $this->kreditlimit_freigabe; }
  public function SetLiefersperre_Ok($value) { $this->liefersperre_ok=$value; }
  public function GetLiefersperre_Ok() { return $this->liefersperre_ok; }
  public function SetTeillieferungvon($value) { $this->teillieferungvon=$value; }
  public function GetTeillieferungvon() { return $this->teillieferungvon; }
  public function SetTeillieferungnummer($value) { $this->teillieferungnummer=$value; }
  public function GetTeillieferungnummer() { return $this->teillieferungnummer; }
  public function SetVertriebid($value) { $this->vertriebid=$value; }
  public function GetVertriebid() { return $this->vertriebid; }
  public function SetAktion($value) { $this->aktion=$value; }
  public function GetAktion() { return $this->aktion; }
  public function SetProvision($value) { $this->provision=$value; }
  public function GetProvision() { return $this->provision; }
  public function SetProvision_Summe($value) { $this->provision_summe=$value; }
  public function GetProvision_Summe() { return $this->provision_summe; }
  public function SetAnfrageid($value) { $this->anfrageid=$value; }
  public function GetAnfrageid() { return $this->anfrageid; }
  public function SetGruppe($value) { $this->gruppe=$value; }
  public function GetGruppe() { return $this->gruppe; }
  public function SetShopextid($value) { $this->shopextid=$value; }
  public function GetShopextid() { return $this->shopextid; }
  public function SetShopextstatus($value) { $this->shopextstatus=$value; }
  public function GetShopextstatus() { return $this->shopextstatus; }
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
  public function SetShop($value) { $this->shop=$value; }
  public function GetShop() { return $this->shop; }
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
  public function SetAngebotid($value) { $this->angebotid=$value; }
  public function GetAngebotid() { return $this->angebotid; }
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
  public function SetAuftragseingangper($value) { $this->auftragseingangper=$value; }
  public function GetAuftragseingangper() { return $this->auftragseingangper; }
  public function SetLieferid($value) { $this->lieferid=$value; }
  public function GetLieferid() { return $this->lieferid; }
  public function SetAnsprechpartnerid($value) { $this->ansprechpartnerid=$value; }
  public function GetAnsprechpartnerid() { return $this->ansprechpartnerid; }
  public function SetSystemfreitext($value) { $this->systemfreitext=$value; }
  public function GetSystemfreitext() { return $this->systemfreitext; }
  public function SetProjektfiliale($value) { $this->projektfiliale=$value; }
  public function GetProjektfiliale() { return $this->projektfiliale; }
  public function SetLieferungtrotzsperre($value) { $this->lieferungtrotzsperre=$value; }
  public function GetLieferungtrotzsperre() { return $this->lieferungtrotzsperre; }
  public function SetZuarchivieren($value) { $this->zuarchivieren=$value; }
  public function GetZuarchivieren() { return $this->zuarchivieren; }
  public function SetInternebezeichnung($value) { $this->internebezeichnung=$value; }
  public function GetInternebezeichnung() { return $this->internebezeichnung; }
  public function SetAngelegtam($value) { $this->angelegtam=$value; }
  public function GetAngelegtam() { return $this->angelegtam; }
  public function SetSaldo($value) { $this->saldo=$value; }
  public function GetSaldo() { return $this->saldo; }
  public function SetSaldogeprueft($value) { $this->saldogeprueft=$value; }
  public function GetSaldogeprueft() { return $this->saldogeprueft; }
  public function SetLieferantenauftrag($value) { $this->lieferantenauftrag=$value; }
  public function GetLieferantenauftrag() { return $this->lieferantenauftrag; }
  public function SetLieferant($value) { $this->lieferant=$value; }
  public function GetLieferant() { return $this->lieferant; }
  public function SetLieferdatumkw($value) { $this->lieferdatumkw=$value; }
  public function GetLieferdatumkw() { return $this->lieferdatumkw; }
  public function SetAbweichendebezeichnung($value) { $this->abweichendebezeichnung=$value; }
  public function GetAbweichendebezeichnung() { return $this->abweichendebezeichnung; }
  public function SetRabatteportofestschreiben($value) { $this->rabatteportofestschreiben=$value; }
  public function GetRabatteportofestschreiben() { return $this->rabatteportofestschreiben; }
  public function SetSprache($value) { $this->sprache=$value; }
  public function GetSprache() { return $this->sprache; }
  public function SetBundesland($value) { $this->bundesland=$value; }
  public function GetBundesland() { return $this->bundesland; }
  public function SetGln($value) { $this->gln=$value; }
  public function GetGln() { return $this->gln; }
  public function SetLiefergln($value) { $this->liefergln=$value; }
  public function GetLiefergln() { return $this->liefergln; }
  public function SetLieferemail($value) { $this->lieferemail=$value; }
  public function GetLieferemail() { return $this->lieferemail; }
  public function SetRechnungid($value) { $this->rechnungid=$value; }
  public function GetRechnungid() { return $this->rechnungid; }
  public function SetDeliverythresholdvatid($value) { $this->deliverythresholdvatid=$value; }
  public function GetDeliverythresholdvatid() { return $this->deliverythresholdvatid; }
  public function SetFastlane($value) { $this->fastlane=$value; }
  public function GetFastlane() { return $this->fastlane; }
  public function SetBearbeiterid($value) { $this->bearbeiterid=$value; }
  public function GetBearbeiterid() { return $this->bearbeiterid; }
  public function SetKurs($value) { $this->kurs=$value; }
  public function GetKurs() { return $this->kurs; }
  public function SetLieferantennummer($value) { $this->lieferantennummer=$value; }
  public function GetLieferantennummer() { return $this->lieferantennummer; }
  public function SetLieferantkdrnummer($value) { $this->lieferantkdrnummer=$value; }
  public function GetLieferantkdrnummer() { return $this->lieferantkdrnummer; }
  public function SetOhne_Artikeltext($value) { $this->ohne_artikeltext=$value; }
  public function GetOhne_Artikeltext() { return $this->ohne_artikeltext; }
  public function SetWebid($value) { $this->webid=$value; }
  public function GetWebid() { return $this->webid; }
  public function SetAnzeigesteuer($value) { $this->anzeigesteuer=$value; }
  public function GetAnzeigesteuer() { return $this->anzeigesteuer; }
  public function SetCronjobkommissionierung($value) { $this->cronjobkommissionierung=$value; }
  public function GetCronjobkommissionierung() { return $this->cronjobkommissionierung; }
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
  public function SetStandardlager($value) { $this->standardlager=$value; }
  public function GetStandardlager() { return $this->standardlager; }
  public function SetSkontobetrag($value) { $this->skontobetrag=$value; }
  public function GetSkontobetrag() { return $this->skontobetrag; }
  public function SetSkontoberechnet($value) { $this->skontoberechnet=$value; }
  public function GetSkontoberechnet() { return $this->skontoberechnet; }
  public function SetKommissionskonsignationslager($value) { $this->kommissionskonsignationslager=$value; }
  public function GetKommissionskonsignationslager() { return $this->kommissionskonsignationslager; }
  public function SetExtsoll($value) { $this->extsoll=$value; }
  public function GetExtsoll() { return $this->extsoll; }
  public function SetBundesstaat($value) { $this->bundesstaat=$value; }
  public function GetBundesstaat() { return $this->bundesstaat; }
  public function SetLieferbundesstaat($value) { $this->lieferbundesstaat=$value; }
  public function GetLieferbundesstaat() { return $this->lieferbundesstaat; }
  public function SetReservationdate($value) { $this->reservationdate=$value; }
  public function GetReservationdate() { return $this->reservationdate; }
  public function SetKundennummer_Buchhaltung($value) { $this->kundennummer_buchhaltung=$value; }
  public function GetKundennummer_Buchhaltung() { return $this->kundennummer_buchhaltung; }

}
