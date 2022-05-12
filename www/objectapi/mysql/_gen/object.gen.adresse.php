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

class ObjGenAdresse
{

  private  $id;
  private  $typ;
  private  $marketingsperre;
  private  $trackingsperre;
  private  $rechnungsadresse;
  private  $sprache;
  private  $name;
  private  $abteilung;
  private  $unterabteilung;
  private  $ansprechpartner;
  private  $land;
  private  $strasse;
  private  $ort;
  private  $plz;
  private  $telefon;
  private  $telefax;
  private  $mobil;
  private  $email;
  private  $ustid;
  private  $ust_befreit;
  private  $passwort_gesendet;
  private  $sonstiges;
  private  $adresszusatz;
  private  $kundenfreigabe;
  private  $steuer;
  private  $logdatei;
  private  $kundennummer;
  private  $lieferantennummer;
  private  $mitarbeiternummer;
  private  $konto;
  private  $blz;
  private  $bank;
  private  $inhaber;
  private  $swift;
  private  $iban;
  private  $waehrung;
  private  $paypal;
  private  $paypalinhaber;
  private  $paypalwaehrung;
  private  $projekt;
  private  $partner;
  private  $zahlungsweise;
  private  $zahlungszieltage;
  private  $zahlungszieltageskonto;
  private  $zahlungszielskonto;
  private  $versandart;
  private  $kundennummerlieferant;
  private  $zahlungsweiselieferant;
  private  $zahlungszieltagelieferant;
  private  $zahlungszieltageskontolieferant;
  private  $zahlungszielskontolieferant;
  private  $versandartlieferant;
  private  $geloescht;
  private  $firma;
  private  $webid;
  private  $vorname;
  private  $kennung;
  private  $sachkonto;
  private  $freifeld1;
  private  $freifeld2;
  private  $freifeld3;
  private  $filiale;
  private  $vertrieb;
  private  $innendienst;
  private  $verbandsnummer;
  private  $abweichendeemailab;
  private  $portofrei_aktiv;
  private  $portofreiab;
  private  $infoauftragserfassung;
  private  $mandatsreferenz;
  private  $mandatsreferenzdatum;
  private  $mandatsreferenzaenderung;
  private  $glaeubigeridentnr;
  private  $kreditlimit;
  private  $tour;
  private  $zahlungskonditionen_festschreiben;
  private  $rabatte_festschreiben;
  private  $mlmaktiv;
  private  $mlmvertragsbeginn;
  private  $mlmlizenzgebuehrbis;
  private  $mlmfestsetzenbis;
  private  $mlmfestsetzen;
  private  $mlmmindestpunkte;
  private  $mlmwartekonto;
  private  $abweichende_rechnungsadresse;
  private  $rechnung_vorname;
  private  $rechnung_name;
  private  $rechnung_titel;
  private  $rechnung_typ;
  private  $rechnung_strasse;
  private  $rechnung_ort;
  private  $rechnung_plz;
  private  $rechnung_ansprechpartner;
  private  $rechnung_land;
  private  $rechnung_abteilung;
  private  $rechnung_unterabteilung;
  private  $rechnung_adresszusatz;
  private  $rechnung_telefon;
  private  $rechnung_telefax;
  private  $rechnung_anschreiben;
  private  $rechnung_email;
  private  $geburtstag;
  private  $rolledatum;
  private  $liefersperre;
  private  $liefersperregrund;
  private  $mlmpositionierung;
  private  $steuernummer;
  private  $steuerbefreit;
  private  $mlmmitmwst;
  private  $mlmabrechnung;
  private  $mlmwaehrungauszahlung;
  private  $mlmauszahlungprojekt;
  private  $sponsor;
  private  $geworbenvon;
  private  $logfile;
  private  $kalender_aufgaben;
  private  $verrechnungskontoreisekosten;
  private  $usereditid;
  private  $useredittimestamp;
  private  $rabatt;
  private  $provision;
  private  $rabattinformation;
  private  $rabatt1;
  private  $rabatt2;
  private  $rabatt3;
  private  $rabatt4;
  private  $rabatt5;
  private  $internetseite;
  private  $bonus1;
  private  $bonus1_ab;
  private  $bonus2;
  private  $bonus2_ab;
  private  $bonus3;
  private  $bonus3_ab;
  private  $bonus4;
  private  $bonus4_ab;
  private  $bonus5;
  private  $bonus5_ab;
  private  $bonus6;
  private  $bonus6_ab;
  private  $bonus7;
  private  $bonus7_ab;
  private  $bonus8;
  private  $bonus8_ab;
  private  $bonus9;
  private  $bonus9_ab;
  private  $bonus10;
  private  $bonus10_ab;
  private  $rechnung_periode;
  private  $rechnung_anzahlpapier;
  private  $rechnung_permail;
  private  $titel;
  private  $anschreiben;
  private  $nachname;
  private  $arbeitszeitprowoche;
  private  $folgebestaetigungsperre;
  private  $lieferantennummerbeikunde;
  private  $verein_mitglied_seit;
  private  $verein_mitglied_bis;
  private  $verein_mitglied_aktiv;
  private  $verein_spendenbescheinigung;
  private  $freifeld4;
  private  $freifeld5;
  private  $freifeld6;
  private  $freifeld7;
  private  $freifeld8;
  private  $freifeld9;
  private  $freifeld10;
  private  $rechnung_papier;
  private  $angebot_cc;
  private  $auftrag_cc;
  private  $rechnung_cc;
  private  $gutschrift_cc;
  private  $lieferschein_cc;
  private  $bestellung_cc;
  private  $angebot_fax_cc;
  private  $auftrag_fax_cc;
  private  $rechnung_fax_cc;
  private  $gutschrift_fax_cc;
  private  $lieferschein_fax_cc;
  private  $bestellung_fax_cc;
  private  $abperfax;
  private  $abpermail;
  private  $kassiereraktiv;
  private  $kassierernummer;
  private  $kassiererprojekt;
  private  $portofreilieferant_aktiv;
  private  $portofreiablieferant;
  private  $mandatsreferenzart;
  private  $mandatsreferenzwdhart;
  private  $serienbrief;
  private  $kundennummer_buchhaltung;
  private  $lieferantennummer_buchhaltung;
  private  $lead;
  private  $zahlungsweiseabo;
  private  $bundesland;
  private  $mandatsreferenzhinweis;
  private  $geburtstagkalender;
  private  $geburtstagskarte;
  private  $liefersperredatum;
  private  $umsatzsteuer_lieferant;
  private  $lat;
  private  $lng;
  private  $art;
  private  $fromshop;
  private  $freifeld11;
  private  $freifeld12;
  private  $freifeld13;
  private  $freifeld14;
  private  $freifeld15;
  private  $freifeld16;
  private  $freifeld17;
  private  $freifeld18;
  private  $freifeld19;
  private  $freifeld20;
  private  $angebot_email;
  private  $auftrag_email;
  private  $rechnungs_email;
  private  $gutschrift_email;
  private  $lieferschein_email;
  private  $bestellung_email;
  private  $lieferschwellenichtanwenden;
  private  $hinweistextlieferant;
  private  $firmensepa;
  private  $hinweis_einfuegen;
  private  $anzeigesteuerbelege;
  private  $gln;
  private  $rechnung_gln;
  private  $keinealtersabfrage;
  private  $lieferbedingung;
  private  $mlmintranetgesamtestruktur;
  private  $kommissionskonsignationslager;
  private  $zollinformationen;
  private  $bundesstaat;
  private  $rechnung_bundesstaat;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `adresse` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->typ=$result['typ'];
    $this->marketingsperre=$result['marketingsperre'];
    $this->trackingsperre=$result['trackingsperre'];
    $this->rechnungsadresse=$result['rechnungsadresse'];
    $this->sprache=$result['sprache'];
    $this->name=$result['name'];
    $this->abteilung=$result['abteilung'];
    $this->unterabteilung=$result['unterabteilung'];
    $this->ansprechpartner=$result['ansprechpartner'];
    $this->land=$result['land'];
    $this->strasse=$result['strasse'];
    $this->ort=$result['ort'];
    $this->plz=$result['plz'];
    $this->telefon=$result['telefon'];
    $this->telefax=$result['telefax'];
    $this->mobil=$result['mobil'];
    $this->email=$result['email'];
    $this->ustid=$result['ustid'];
    $this->ust_befreit=$result['ust_befreit'];
    $this->passwort_gesendet=$result['passwort_gesendet'];
    $this->sonstiges=$result['sonstiges'];
    $this->adresszusatz=$result['adresszusatz'];
    $this->kundenfreigabe=$result['kundenfreigabe'];
    $this->steuer=$result['steuer'];
    $this->logdatei=$result['logdatei'];
    $this->kundennummer=$result['kundennummer'];
    $this->lieferantennummer=$result['lieferantennummer'];
    $this->mitarbeiternummer=$result['mitarbeiternummer'];
    $this->konto=$result['konto'];
    $this->blz=$result['blz'];
    $this->bank=$result['bank'];
    $this->inhaber=$result['inhaber'];
    $this->swift=$result['swift'];
    $this->iban=$result['iban'];
    $this->waehrung=$result['waehrung'];
    $this->paypal=$result['paypal'];
    $this->paypalinhaber=$result['paypalinhaber'];
    $this->paypalwaehrung=$result['paypalwaehrung'];
    $this->projekt=$result['projekt'];
    $this->partner=$result['partner'];
    $this->zahlungsweise=$result['zahlungsweise'];
    $this->zahlungszieltage=$result['zahlungszieltage'];
    $this->zahlungszieltageskonto=$result['zahlungszieltageskonto'];
    $this->zahlungszielskonto=$result['zahlungszielskonto'];
    $this->versandart=$result['versandart'];
    $this->kundennummerlieferant=$result['kundennummerlieferant'];
    $this->zahlungsweiselieferant=$result['zahlungsweiselieferant'];
    $this->zahlungszieltagelieferant=$result['zahlungszieltagelieferant'];
    $this->zahlungszieltageskontolieferant=$result['zahlungszieltageskontolieferant'];
    $this->zahlungszielskontolieferant=$result['zahlungszielskontolieferant'];
    $this->versandartlieferant=$result['versandartlieferant'];
    $this->geloescht=$result['geloescht'];
    $this->firma=$result['firma'];
    $this->webid=$result['webid'];
    $this->vorname=$result['vorname'];
    $this->kennung=$result['kennung'];
    $this->sachkonto=$result['sachkonto'];
    $this->freifeld1=$result['freifeld1'];
    $this->freifeld2=$result['freifeld2'];
    $this->freifeld3=$result['freifeld3'];
    $this->filiale=$result['filiale'];
    $this->vertrieb=$result['vertrieb'];
    $this->innendienst=$result['innendienst'];
    $this->verbandsnummer=$result['verbandsnummer'];
    $this->abweichendeemailab=$result['abweichendeemailab'];
    $this->portofrei_aktiv=$result['portofrei_aktiv'];
    $this->portofreiab=$result['portofreiab'];
    $this->infoauftragserfassung=$result['infoauftragserfassung'];
    $this->mandatsreferenz=$result['mandatsreferenz'];
    $this->mandatsreferenzdatum=$result['mandatsreferenzdatum'];
    $this->mandatsreferenzaenderung=$result['mandatsreferenzaenderung'];
    $this->glaeubigeridentnr=$result['glaeubigeridentnr'];
    $this->kreditlimit=$result['kreditlimit'];
    $this->tour=$result['tour'];
    $this->zahlungskonditionen_festschreiben=$result['zahlungskonditionen_festschreiben'];
    $this->rabatte_festschreiben=$result['rabatte_festschreiben'];
    $this->mlmaktiv=$result['mlmaktiv'];
    $this->mlmvertragsbeginn=$result['mlmvertragsbeginn'];
    $this->mlmlizenzgebuehrbis=$result['mlmlizenzgebuehrbis'];
    $this->mlmfestsetzenbis=$result['mlmfestsetzenbis'];
    $this->mlmfestsetzen=$result['mlmfestsetzen'];
    $this->mlmmindestpunkte=$result['mlmmindestpunkte'];
    $this->mlmwartekonto=$result['mlmwartekonto'];
    $this->abweichende_rechnungsadresse=$result['abweichende_rechnungsadresse'];
    $this->rechnung_vorname=$result['rechnung_vorname'];
    $this->rechnung_name=$result['rechnung_name'];
    $this->rechnung_titel=$result['rechnung_titel'];
    $this->rechnung_typ=$result['rechnung_typ'];
    $this->rechnung_strasse=$result['rechnung_strasse'];
    $this->rechnung_ort=$result['rechnung_ort'];
    $this->rechnung_plz=$result['rechnung_plz'];
    $this->rechnung_ansprechpartner=$result['rechnung_ansprechpartner'];
    $this->rechnung_land=$result['rechnung_land'];
    $this->rechnung_abteilung=$result['rechnung_abteilung'];
    $this->rechnung_unterabteilung=$result['rechnung_unterabteilung'];
    $this->rechnung_adresszusatz=$result['rechnung_adresszusatz'];
    $this->rechnung_telefon=$result['rechnung_telefon'];
    $this->rechnung_telefax=$result['rechnung_telefax'];
    $this->rechnung_anschreiben=$result['rechnung_anschreiben'];
    $this->rechnung_email=$result['rechnung_email'];
    $this->geburtstag=$result['geburtstag'];
    $this->rolledatum=$result['rolledatum'];
    $this->liefersperre=$result['liefersperre'];
    $this->liefersperregrund=$result['liefersperregrund'];
    $this->mlmpositionierung=$result['mlmpositionierung'];
    $this->steuernummer=$result['steuernummer'];
    $this->steuerbefreit=$result['steuerbefreit'];
    $this->mlmmitmwst=$result['mlmmitmwst'];
    $this->mlmabrechnung=$result['mlmabrechnung'];
    $this->mlmwaehrungauszahlung=$result['mlmwaehrungauszahlung'];
    $this->mlmauszahlungprojekt=$result['mlmauszahlungprojekt'];
    $this->sponsor=$result['sponsor'];
    $this->geworbenvon=$result['geworbenvon'];
    $this->logfile=$result['logfile'];
    $this->kalender_aufgaben=$result['kalender_aufgaben'];
    $this->verrechnungskontoreisekosten=$result['verrechnungskontoreisekosten'];
    $this->usereditid=$result['usereditid'];
    $this->useredittimestamp=$result['useredittimestamp'];
    $this->rabatt=$result['rabatt'];
    $this->provision=$result['provision'];
    $this->rabattinformation=$result['rabattinformation'];
    $this->rabatt1=$result['rabatt1'];
    $this->rabatt2=$result['rabatt2'];
    $this->rabatt3=$result['rabatt3'];
    $this->rabatt4=$result['rabatt4'];
    $this->rabatt5=$result['rabatt5'];
    $this->internetseite=$result['internetseite'];
    $this->bonus1=$result['bonus1'];
    $this->bonus1_ab=$result['bonus1_ab'];
    $this->bonus2=$result['bonus2'];
    $this->bonus2_ab=$result['bonus2_ab'];
    $this->bonus3=$result['bonus3'];
    $this->bonus3_ab=$result['bonus3_ab'];
    $this->bonus4=$result['bonus4'];
    $this->bonus4_ab=$result['bonus4_ab'];
    $this->bonus5=$result['bonus5'];
    $this->bonus5_ab=$result['bonus5_ab'];
    $this->bonus6=$result['bonus6'];
    $this->bonus6_ab=$result['bonus6_ab'];
    $this->bonus7=$result['bonus7'];
    $this->bonus7_ab=$result['bonus7_ab'];
    $this->bonus8=$result['bonus8'];
    $this->bonus8_ab=$result['bonus8_ab'];
    $this->bonus9=$result['bonus9'];
    $this->bonus9_ab=$result['bonus9_ab'];
    $this->bonus10=$result['bonus10'];
    $this->bonus10_ab=$result['bonus10_ab'];
    $this->rechnung_periode=$result['rechnung_periode'];
    $this->rechnung_anzahlpapier=$result['rechnung_anzahlpapier'];
    $this->rechnung_permail=$result['rechnung_permail'];
    $this->titel=$result['titel'];
    $this->anschreiben=$result['anschreiben'];
    $this->nachname=$result['nachname'];
    $this->arbeitszeitprowoche=$result['arbeitszeitprowoche'];
    $this->folgebestaetigungsperre=$result['folgebestaetigungsperre'];
    $this->lieferantennummerbeikunde=$result['lieferantennummerbeikunde'];
    $this->verein_mitglied_seit=$result['verein_mitglied_seit'];
    $this->verein_mitglied_bis=$result['verein_mitglied_bis'];
    $this->verein_mitglied_aktiv=$result['verein_mitglied_aktiv'];
    $this->verein_spendenbescheinigung=$result['verein_spendenbescheinigung'];
    $this->freifeld4=$result['freifeld4'];
    $this->freifeld5=$result['freifeld5'];
    $this->freifeld6=$result['freifeld6'];
    $this->freifeld7=$result['freifeld7'];
    $this->freifeld8=$result['freifeld8'];
    $this->freifeld9=$result['freifeld9'];
    $this->freifeld10=$result['freifeld10'];
    $this->rechnung_papier=$result['rechnung_papier'];
    $this->angebot_cc=$result['angebot_cc'];
    $this->auftrag_cc=$result['auftrag_cc'];
    $this->rechnung_cc=$result['rechnung_cc'];
    $this->gutschrift_cc=$result['gutschrift_cc'];
    $this->lieferschein_cc=$result['lieferschein_cc'];
    $this->bestellung_cc=$result['bestellung_cc'];
    $this->angebot_fax_cc=$result['angebot_fax_cc'];
    $this->auftrag_fax_cc=$result['auftrag_fax_cc'];
    $this->rechnung_fax_cc=$result['rechnung_fax_cc'];
    $this->gutschrift_fax_cc=$result['gutschrift_fax_cc'];
    $this->lieferschein_fax_cc=$result['lieferschein_fax_cc'];
    $this->bestellung_fax_cc=$result['bestellung_fax_cc'];
    $this->abperfax=$result['abperfax'];
    $this->abpermail=$result['abpermail'];
    $this->kassiereraktiv=$result['kassiereraktiv'];
    $this->kassierernummer=$result['kassierernummer'];
    $this->kassiererprojekt=$result['kassiererprojekt'];
    $this->portofreilieferant_aktiv=$result['portofreilieferant_aktiv'];
    $this->portofreiablieferant=$result['portofreiablieferant'];
    $this->mandatsreferenzart=$result['mandatsreferenzart'];
    $this->mandatsreferenzwdhart=$result['mandatsreferenzwdhart'];
    $this->serienbrief=$result['serienbrief'];
    $this->kundennummer_buchhaltung=$result['kundennummer_buchhaltung'];
    $this->lieferantennummer_buchhaltung=$result['lieferantennummer_buchhaltung'];
    $this->lead=$result['lead'];
    $this->zahlungsweiseabo=$result['zahlungsweiseabo'];
    $this->bundesland=$result['bundesland'];
    $this->mandatsreferenzhinweis=$result['mandatsreferenzhinweis'];
    $this->geburtstagkalender=$result['geburtstagkalender'];
    $this->geburtstagskarte=$result['geburtstagskarte'];
    $this->liefersperredatum=$result['liefersperredatum'];
    $this->umsatzsteuer_lieferant=$result['umsatzsteuer_lieferant'];
    $this->lat=$result['lat'];
    $this->lng=$result['lng'];
    $this->art=$result['art'];
    $this->fromshop=$result['fromshop'];
    $this->freifeld11=$result['freifeld11'];
    $this->freifeld12=$result['freifeld12'];
    $this->freifeld13=$result['freifeld13'];
    $this->freifeld14=$result['freifeld14'];
    $this->freifeld15=$result['freifeld15'];
    $this->freifeld16=$result['freifeld16'];
    $this->freifeld17=$result['freifeld17'];
    $this->freifeld18=$result['freifeld18'];
    $this->freifeld19=$result['freifeld19'];
    $this->freifeld20=$result['freifeld20'];
    $this->angebot_email=$result['angebot_email'];
    $this->auftrag_email=$result['auftrag_email'];
    $this->rechnungs_email=$result['rechnungs_email'];
    $this->gutschrift_email=$result['gutschrift_email'];
    $this->lieferschein_email=$result['lieferschein_email'];
    $this->bestellung_email=$result['bestellung_email'];
    $this->lieferschwellenichtanwenden=$result['lieferschwellenichtanwenden'];
    $this->hinweistextlieferant=$result['hinweistextlieferant'];
    $this->firmensepa=$result['firmensepa'];
    $this->hinweis_einfuegen=$result['hinweis_einfuegen'];
    $this->anzeigesteuerbelege=$result['anzeigesteuerbelege'];
    $this->gln=$result['gln'];
    $this->rechnung_gln=$result['rechnung_gln'];
    $this->keinealtersabfrage=$result['keinealtersabfrage'];
    $this->lieferbedingung=$result['lieferbedingung'];
    $this->mlmintranetgesamtestruktur=$result['mlmintranetgesamtestruktur'];
    $this->kommissionskonsignationslager=$result['kommissionskonsignationslager'];
    $this->zollinformationen=$result['zollinformationen'];
    $this->bundesstaat=$result['bundesstaat'];
    $this->rechnung_bundesstaat=$result['rechnung_bundesstaat'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `adresse` (`id`,`typ`,`marketingsperre`,`trackingsperre`,`rechnungsadresse`,`sprache`,`name`,`abteilung`,`unterabteilung`,`ansprechpartner`,`land`,`strasse`,`ort`,`plz`,`telefon`,`telefax`,`mobil`,`email`,`ustid`,`ust_befreit`,`passwort_gesendet`,`sonstiges`,`adresszusatz`,`kundenfreigabe`,`steuer`,`logdatei`,`kundennummer`,`lieferantennummer`,`mitarbeiternummer`,`konto`,`blz`,`bank`,`inhaber`,`swift`,`iban`,`waehrung`,`paypal`,`paypalinhaber`,`paypalwaehrung`,`projekt`,`partner`,`zahlungsweise`,`zahlungszieltage`,`zahlungszieltageskonto`,`zahlungszielskonto`,`versandart`,`kundennummerlieferant`,`zahlungsweiselieferant`,`zahlungszieltagelieferant`,`zahlungszieltageskontolieferant`,`zahlungszielskontolieferant`,`versandartlieferant`,`geloescht`,`firma`,`webid`,`vorname`,`kennung`,`sachkonto`,`freifeld1`,`freifeld2`,`freifeld3`,`filiale`,`vertrieb`,`innendienst`,`verbandsnummer`,`abweichendeemailab`,`portofrei_aktiv`,`portofreiab`,`infoauftragserfassung`,`mandatsreferenz`,`mandatsreferenzdatum`,`mandatsreferenzaenderung`,`glaeubigeridentnr`,`kreditlimit`,`tour`,`zahlungskonditionen_festschreiben`,`rabatte_festschreiben`,`mlmaktiv`,`mlmvertragsbeginn`,`mlmlizenzgebuehrbis`,`mlmfestsetzenbis`,`mlmfestsetzen`,`mlmmindestpunkte`,`mlmwartekonto`,`abweichende_rechnungsadresse`,`rechnung_vorname`,`rechnung_name`,`rechnung_titel`,`rechnung_typ`,`rechnung_strasse`,`rechnung_ort`,`rechnung_plz`,`rechnung_ansprechpartner`,`rechnung_land`,`rechnung_abteilung`,`rechnung_unterabteilung`,`rechnung_adresszusatz`,`rechnung_telefon`,`rechnung_telefax`,`rechnung_anschreiben`,`rechnung_email`,`geburtstag`,`rolledatum`,`liefersperre`,`liefersperregrund`,`mlmpositionierung`,`steuernummer`,`steuerbefreit`,`mlmmitmwst`,`mlmabrechnung`,`mlmwaehrungauszahlung`,`mlmauszahlungprojekt`,`sponsor`,`geworbenvon`,`logfile`,`kalender_aufgaben`,`verrechnungskontoreisekosten`,`usereditid`,`useredittimestamp`,`rabatt`,`provision`,`rabattinformation`,`rabatt1`,`rabatt2`,`rabatt3`,`rabatt4`,`rabatt5`,`internetseite`,`bonus1`,`bonus1_ab`,`bonus2`,`bonus2_ab`,`bonus3`,`bonus3_ab`,`bonus4`,`bonus4_ab`,`bonus5`,`bonus5_ab`,`bonus6`,`bonus6_ab`,`bonus7`,`bonus7_ab`,`bonus8`,`bonus8_ab`,`bonus9`,`bonus9_ab`,`bonus10`,`bonus10_ab`,`rechnung_periode`,`rechnung_anzahlpapier`,`rechnung_permail`,`titel`,`anschreiben`,`nachname`,`arbeitszeitprowoche`,`folgebestaetigungsperre`,`lieferantennummerbeikunde`,`verein_mitglied_seit`,`verein_mitglied_bis`,`verein_mitglied_aktiv`,`verein_spendenbescheinigung`,`freifeld4`,`freifeld5`,`freifeld6`,`freifeld7`,`freifeld8`,`freifeld9`,`freifeld10`,`rechnung_papier`,`angebot_cc`,`auftrag_cc`,`rechnung_cc`,`gutschrift_cc`,`lieferschein_cc`,`bestellung_cc`,`angebot_fax_cc`,`auftrag_fax_cc`,`rechnung_fax_cc`,`gutschrift_fax_cc`,`lieferschein_fax_cc`,`bestellung_fax_cc`,`abperfax`,`abpermail`,`kassiereraktiv`,`kassierernummer`,`kassiererprojekt`,`portofreilieferant_aktiv`,`portofreiablieferant`,`mandatsreferenzart`,`mandatsreferenzwdhart`,`serienbrief`,`kundennummer_buchhaltung`,`lieferantennummer_buchhaltung`,`lead`,`zahlungsweiseabo`,`bundesland`,`mandatsreferenzhinweis`,`geburtstagkalender`,`geburtstagskarte`,`liefersperredatum`,`umsatzsteuer_lieferant`,`lat`,`lng`,`art`,`fromshop`,`freifeld11`,`freifeld12`,`freifeld13`,`freifeld14`,`freifeld15`,`freifeld16`,`freifeld17`,`freifeld18`,`freifeld19`,`freifeld20`,`angebot_email`,`auftrag_email`,`rechnungs_email`,`gutschrift_email`,`lieferschein_email`,`bestellung_email`,`lieferschwellenichtanwenden`,`hinweistextlieferant`,`firmensepa`,`hinweis_einfuegen`,`anzeigesteuerbelege`,`gln`,`rechnung_gln`,`keinealtersabfrage`,`lieferbedingung`,`mlmintranetgesamtestruktur`,`kommissionskonsignationslager`,`zollinformationen`,`bundesstaat`,`rechnung_bundesstaat`)
      VALUES(NULL,'{$this->typ}','{$this->marketingsperre}','{$this->trackingsperre}','{$this->rechnungsadresse}','{$this->sprache}','{$this->name}','{$this->abteilung}','{$this->unterabteilung}','{$this->ansprechpartner}','{$this->land}','{$this->strasse}','{$this->ort}','{$this->plz}','{$this->telefon}','{$this->telefax}','{$this->mobil}','{$this->email}','{$this->ustid}','{$this->ust_befreit}','{$this->passwort_gesendet}','{$this->sonstiges}','{$this->adresszusatz}','{$this->kundenfreigabe}','{$this->steuer}','{$this->logdatei}','{$this->kundennummer}','{$this->lieferantennummer}','{$this->mitarbeiternummer}','{$this->konto}','{$this->blz}','{$this->bank}','{$this->inhaber}','{$this->swift}','{$this->iban}','{$this->waehrung}','{$this->paypal}','{$this->paypalinhaber}','{$this->paypalwaehrung}','{$this->projekt}','{$this->partner}','{$this->zahlungsweise}','{$this->zahlungszieltage}','{$this->zahlungszieltageskonto}','{$this->zahlungszielskonto}','{$this->versandart}','{$this->kundennummerlieferant}','{$this->zahlungsweiselieferant}','{$this->zahlungszieltagelieferant}','{$this->zahlungszieltageskontolieferant}','{$this->zahlungszielskontolieferant}','{$this->versandartlieferant}','{$this->geloescht}','{$this->firma}','{$this->webid}','{$this->vorname}','{$this->kennung}','{$this->sachkonto}','{$this->freifeld1}','{$this->freifeld2}','{$this->freifeld3}','{$this->filiale}','{$this->vertrieb}','{$this->innendienst}','{$this->verbandsnummer}','{$this->abweichendeemailab}','{$this->portofrei_aktiv}','{$this->portofreiab}','{$this->infoauftragserfassung}','{$this->mandatsreferenz}','{$this->mandatsreferenzdatum}','{$this->mandatsreferenzaenderung}','{$this->glaeubigeridentnr}','{$this->kreditlimit}','{$this->tour}','{$this->zahlungskonditionen_festschreiben}','{$this->rabatte_festschreiben}','{$this->mlmaktiv}','{$this->mlmvertragsbeginn}','{$this->mlmlizenzgebuehrbis}','{$this->mlmfestsetzenbis}','{$this->mlmfestsetzen}','{$this->mlmmindestpunkte}','{$this->mlmwartekonto}','{$this->abweichende_rechnungsadresse}','{$this->rechnung_vorname}','{$this->rechnung_name}','{$this->rechnung_titel}','{$this->rechnung_typ}','{$this->rechnung_strasse}','{$this->rechnung_ort}','{$this->rechnung_plz}','{$this->rechnung_ansprechpartner}','{$this->rechnung_land}','{$this->rechnung_abteilung}','{$this->rechnung_unterabteilung}','{$this->rechnung_adresszusatz}','{$this->rechnung_telefon}','{$this->rechnung_telefax}','{$this->rechnung_anschreiben}','{$this->rechnung_email}','{$this->geburtstag}','{$this->rolledatum}','{$this->liefersperre}','{$this->liefersperregrund}','{$this->mlmpositionierung}','{$this->steuernummer}','{$this->steuerbefreit}','{$this->mlmmitmwst}','{$this->mlmabrechnung}','{$this->mlmwaehrungauszahlung}','{$this->mlmauszahlungprojekt}','{$this->sponsor}','{$this->geworbenvon}','{$this->logfile}','{$this->kalender_aufgaben}','{$this->verrechnungskontoreisekosten}','{$this->usereditid}','{$this->useredittimestamp}','{$this->rabatt}','{$this->provision}','{$this->rabattinformation}','{$this->rabatt1}','{$this->rabatt2}','{$this->rabatt3}','{$this->rabatt4}','{$this->rabatt5}','{$this->internetseite}','{$this->bonus1}','{$this->bonus1_ab}','{$this->bonus2}','{$this->bonus2_ab}','{$this->bonus3}','{$this->bonus3_ab}','{$this->bonus4}','{$this->bonus4_ab}','{$this->bonus5}','{$this->bonus5_ab}','{$this->bonus6}','{$this->bonus6_ab}','{$this->bonus7}','{$this->bonus7_ab}','{$this->bonus8}','{$this->bonus8_ab}','{$this->bonus9}','{$this->bonus9_ab}','{$this->bonus10}','{$this->bonus10_ab}','{$this->rechnung_periode}','{$this->rechnung_anzahlpapier}','{$this->rechnung_permail}','{$this->titel}','{$this->anschreiben}','{$this->nachname}','{$this->arbeitszeitprowoche}','{$this->folgebestaetigungsperre}','{$this->lieferantennummerbeikunde}','{$this->verein_mitglied_seit}','{$this->verein_mitglied_bis}','{$this->verein_mitglied_aktiv}','{$this->verein_spendenbescheinigung}','{$this->freifeld4}','{$this->freifeld5}','{$this->freifeld6}','{$this->freifeld7}','{$this->freifeld8}','{$this->freifeld9}','{$this->freifeld10}','{$this->rechnung_papier}','{$this->angebot_cc}','{$this->auftrag_cc}','{$this->rechnung_cc}','{$this->gutschrift_cc}','{$this->lieferschein_cc}','{$this->bestellung_cc}','{$this->angebot_fax_cc}','{$this->auftrag_fax_cc}','{$this->rechnung_fax_cc}','{$this->gutschrift_fax_cc}','{$this->lieferschein_fax_cc}','{$this->bestellung_fax_cc}','{$this->abperfax}','{$this->abpermail}','{$this->kassiereraktiv}','{$this->kassierernummer}','{$this->kassiererprojekt}','{$this->portofreilieferant_aktiv}','{$this->portofreiablieferant}','{$this->mandatsreferenzart}','{$this->mandatsreferenzwdhart}','{$this->serienbrief}','{$this->kundennummer_buchhaltung}','{$this->lieferantennummer_buchhaltung}','{$this->lead}','{$this->zahlungsweiseabo}','{$this->bundesland}','{$this->mandatsreferenzhinweis}','{$this->geburtstagkalender}','{$this->geburtstagskarte}','{$this->liefersperredatum}','{$this->umsatzsteuer_lieferant}','{$this->lat}','{$this->lng}','{$this->art}','{$this->fromshop}','{$this->freifeld11}','{$this->freifeld12}','{$this->freifeld13}','{$this->freifeld14}','{$this->freifeld15}','{$this->freifeld16}','{$this->freifeld17}','{$this->freifeld18}','{$this->freifeld19}','{$this->freifeld20}','{$this->angebot_email}','{$this->auftrag_email}','{$this->rechnungs_email}','{$this->gutschrift_email}','{$this->lieferschein_email}','{$this->bestellung_email}','{$this->lieferschwellenichtanwenden}','{$this->hinweistextlieferant}','{$this->firmensepa}','{$this->hinweis_einfuegen}','{$this->anzeigesteuerbelege}','{$this->gln}','{$this->rechnung_gln}','{$this->keinealtersabfrage}','{$this->lieferbedingung}','{$this->mlmintranetgesamtestruktur}','{$this->kommissionskonsignationslager}','{$this->zollinformationen}','{$this->bundesstaat}','{$this->rechnung_bundesstaat}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `adresse` SET
      `typ`='{$this->typ}',
      `marketingsperre`='{$this->marketingsperre}',
      `trackingsperre`='{$this->trackingsperre}',
      `rechnungsadresse`='{$this->rechnungsadresse}',
      `sprache`='{$this->sprache}',
      `name`='{$this->name}',
      `abteilung`='{$this->abteilung}',
      `unterabteilung`='{$this->unterabteilung}',
      `ansprechpartner`='{$this->ansprechpartner}',
      `land`='{$this->land}',
      `strasse`='{$this->strasse}',
      `ort`='{$this->ort}',
      `plz`='{$this->plz}',
      `telefon`='{$this->telefon}',
      `telefax`='{$this->telefax}',
      `mobil`='{$this->mobil}',
      `email`='{$this->email}',
      `ustid`='{$this->ustid}',
      `ust_befreit`='{$this->ust_befreit}',
      `passwort_gesendet`='{$this->passwort_gesendet}',
      `sonstiges`='{$this->sonstiges}',
      `adresszusatz`='{$this->adresszusatz}',
      `kundenfreigabe`='{$this->kundenfreigabe}',
      `steuer`='{$this->steuer}',
      `logdatei`='{$this->logdatei}',
      `kundennummer`='{$this->kundennummer}',
      `lieferantennummer`='{$this->lieferantennummer}',
      `mitarbeiternummer`='{$this->mitarbeiternummer}',
      `konto`='{$this->konto}',
      `blz`='{$this->blz}',
      `bank`='{$this->bank}',
      `inhaber`='{$this->inhaber}',
      `swift`='{$this->swift}',
      `iban`='{$this->iban}',
      `waehrung`='{$this->waehrung}',
      `paypal`='{$this->paypal}',
      `paypalinhaber`='{$this->paypalinhaber}',
      `paypalwaehrung`='{$this->paypalwaehrung}',
      `projekt`='{$this->projekt}',
      `partner`='{$this->partner}',
      `zahlungsweise`='{$this->zahlungsweise}',
      `zahlungszieltage`='{$this->zahlungszieltage}',
      `zahlungszieltageskonto`='{$this->zahlungszieltageskonto}',
      `zahlungszielskonto`='{$this->zahlungszielskonto}',
      `versandart`='{$this->versandart}',
      `kundennummerlieferant`='{$this->kundennummerlieferant}',
      `zahlungsweiselieferant`='{$this->zahlungsweiselieferant}',
      `zahlungszieltagelieferant`='{$this->zahlungszieltagelieferant}',
      `zahlungszieltageskontolieferant`='{$this->zahlungszieltageskontolieferant}',
      `zahlungszielskontolieferant`='{$this->zahlungszielskontolieferant}',
      `versandartlieferant`='{$this->versandartlieferant}',
      `geloescht`='{$this->geloescht}',
      `firma`='{$this->firma}',
      `webid`='{$this->webid}',
      `vorname`='{$this->vorname}',
      `kennung`='{$this->kennung}',
      `sachkonto`='{$this->sachkonto}',
      `freifeld1`='{$this->freifeld1}',
      `freifeld2`='{$this->freifeld2}',
      `freifeld3`='{$this->freifeld3}',
      `filiale`='{$this->filiale}',
      `vertrieb`='{$this->vertrieb}',
      `innendienst`='{$this->innendienst}',
      `verbandsnummer`='{$this->verbandsnummer}',
      `abweichendeemailab`='{$this->abweichendeemailab}',
      `portofrei_aktiv`='{$this->portofrei_aktiv}',
      `portofreiab`='{$this->portofreiab}',
      `infoauftragserfassung`='{$this->infoauftragserfassung}',
      `mandatsreferenz`='{$this->mandatsreferenz}',
      `mandatsreferenzdatum`='{$this->mandatsreferenzdatum}',
      `mandatsreferenzaenderung`='{$this->mandatsreferenzaenderung}',
      `glaeubigeridentnr`='{$this->glaeubigeridentnr}',
      `kreditlimit`='{$this->kreditlimit}',
      `tour`='{$this->tour}',
      `zahlungskonditionen_festschreiben`='{$this->zahlungskonditionen_festschreiben}',
      `rabatte_festschreiben`='{$this->rabatte_festschreiben}',
      `mlmaktiv`='{$this->mlmaktiv}',
      `mlmvertragsbeginn`='{$this->mlmvertragsbeginn}',
      `mlmlizenzgebuehrbis`='{$this->mlmlizenzgebuehrbis}',
      `mlmfestsetzenbis`='{$this->mlmfestsetzenbis}',
      `mlmfestsetzen`='{$this->mlmfestsetzen}',
      `mlmmindestpunkte`='{$this->mlmmindestpunkte}',
      `mlmwartekonto`='{$this->mlmwartekonto}',
      `abweichende_rechnungsadresse`='{$this->abweichende_rechnungsadresse}',
      `rechnung_vorname`='{$this->rechnung_vorname}',
      `rechnung_name`='{$this->rechnung_name}',
      `rechnung_titel`='{$this->rechnung_titel}',
      `rechnung_typ`='{$this->rechnung_typ}',
      `rechnung_strasse`='{$this->rechnung_strasse}',
      `rechnung_ort`='{$this->rechnung_ort}',
      `rechnung_plz`='{$this->rechnung_plz}',
      `rechnung_ansprechpartner`='{$this->rechnung_ansprechpartner}',
      `rechnung_land`='{$this->rechnung_land}',
      `rechnung_abteilung`='{$this->rechnung_abteilung}',
      `rechnung_unterabteilung`='{$this->rechnung_unterabteilung}',
      `rechnung_adresszusatz`='{$this->rechnung_adresszusatz}',
      `rechnung_telefon`='{$this->rechnung_telefon}',
      `rechnung_telefax`='{$this->rechnung_telefax}',
      `rechnung_anschreiben`='{$this->rechnung_anschreiben}',
      `rechnung_email`='{$this->rechnung_email}',
      `geburtstag`='{$this->geburtstag}',
      `rolledatum`='{$this->rolledatum}',
      `liefersperre`='{$this->liefersperre}',
      `liefersperregrund`='{$this->liefersperregrund}',
      `mlmpositionierung`='{$this->mlmpositionierung}',
      `steuernummer`='{$this->steuernummer}',
      `steuerbefreit`='{$this->steuerbefreit}',
      `mlmmitmwst`='{$this->mlmmitmwst}',
      `mlmabrechnung`='{$this->mlmabrechnung}',
      `mlmwaehrungauszahlung`='{$this->mlmwaehrungauszahlung}',
      `mlmauszahlungprojekt`='{$this->mlmauszahlungprojekt}',
      `sponsor`='{$this->sponsor}',
      `geworbenvon`='{$this->geworbenvon}',
      `logfile`='{$this->logfile}',
      `kalender_aufgaben`='{$this->kalender_aufgaben}',
      `verrechnungskontoreisekosten`='{$this->verrechnungskontoreisekosten}',
      `usereditid`='{$this->usereditid}',
      `useredittimestamp`='{$this->useredittimestamp}',
      `rabatt`='{$this->rabatt}',
      `provision`='{$this->provision}',
      `rabattinformation`='{$this->rabattinformation}',
      `rabatt1`='{$this->rabatt1}',
      `rabatt2`='{$this->rabatt2}',
      `rabatt3`='{$this->rabatt3}',
      `rabatt4`='{$this->rabatt4}',
      `rabatt5`='{$this->rabatt5}',
      `internetseite`='{$this->internetseite}',
      `bonus1`='{$this->bonus1}',
      `bonus1_ab`='{$this->bonus1_ab}',
      `bonus2`='{$this->bonus2}',
      `bonus2_ab`='{$this->bonus2_ab}',
      `bonus3`='{$this->bonus3}',
      `bonus3_ab`='{$this->bonus3_ab}',
      `bonus4`='{$this->bonus4}',
      `bonus4_ab`='{$this->bonus4_ab}',
      `bonus5`='{$this->bonus5}',
      `bonus5_ab`='{$this->bonus5_ab}',
      `bonus6`='{$this->bonus6}',
      `bonus6_ab`='{$this->bonus6_ab}',
      `bonus7`='{$this->bonus7}',
      `bonus7_ab`='{$this->bonus7_ab}',
      `bonus8`='{$this->bonus8}',
      `bonus8_ab`='{$this->bonus8_ab}',
      `bonus9`='{$this->bonus9}',
      `bonus9_ab`='{$this->bonus9_ab}',
      `bonus10`='{$this->bonus10}',
      `bonus10_ab`='{$this->bonus10_ab}',
      `rechnung_periode`='{$this->rechnung_periode}',
      `rechnung_anzahlpapier`='{$this->rechnung_anzahlpapier}',
      `rechnung_permail`='{$this->rechnung_permail}',
      `titel`='{$this->titel}',
      `anschreiben`='{$this->anschreiben}',
      `nachname`='{$this->nachname}',
      `arbeitszeitprowoche`='{$this->arbeitszeitprowoche}',
      `folgebestaetigungsperre`='{$this->folgebestaetigungsperre}',
      `lieferantennummerbeikunde`='{$this->lieferantennummerbeikunde}',
      `verein_mitglied_seit`='{$this->verein_mitglied_seit}',
      `verein_mitglied_bis`='{$this->verein_mitglied_bis}',
      `verein_mitglied_aktiv`='{$this->verein_mitglied_aktiv}',
      `verein_spendenbescheinigung`='{$this->verein_spendenbescheinigung}',
      `freifeld4`='{$this->freifeld4}',
      `freifeld5`='{$this->freifeld5}',
      `freifeld6`='{$this->freifeld6}',
      `freifeld7`='{$this->freifeld7}',
      `freifeld8`='{$this->freifeld8}',
      `freifeld9`='{$this->freifeld9}',
      `freifeld10`='{$this->freifeld10}',
      `rechnung_papier`='{$this->rechnung_papier}',
      `angebot_cc`='{$this->angebot_cc}',
      `auftrag_cc`='{$this->auftrag_cc}',
      `rechnung_cc`='{$this->rechnung_cc}',
      `gutschrift_cc`='{$this->gutschrift_cc}',
      `lieferschein_cc`='{$this->lieferschein_cc}',
      `bestellung_cc`='{$this->bestellung_cc}',
      `angebot_fax_cc`='{$this->angebot_fax_cc}',
      `auftrag_fax_cc`='{$this->auftrag_fax_cc}',
      `rechnung_fax_cc`='{$this->rechnung_fax_cc}',
      `gutschrift_fax_cc`='{$this->gutschrift_fax_cc}',
      `lieferschein_fax_cc`='{$this->lieferschein_fax_cc}',
      `bestellung_fax_cc`='{$this->bestellung_fax_cc}',
      `abperfax`='{$this->abperfax}',
      `abpermail`='{$this->abpermail}',
      `kassiereraktiv`='{$this->kassiereraktiv}',
      `kassierernummer`='{$this->kassierernummer}',
      `kassiererprojekt`='{$this->kassiererprojekt}',
      `portofreilieferant_aktiv`='{$this->portofreilieferant_aktiv}',
      `portofreiablieferant`='{$this->portofreiablieferant}',
      `mandatsreferenzart`='{$this->mandatsreferenzart}',
      `mandatsreferenzwdhart`='{$this->mandatsreferenzwdhart}',
      `serienbrief`='{$this->serienbrief}',
      `kundennummer_buchhaltung`='{$this->kundennummer_buchhaltung}',
      `lieferantennummer_buchhaltung`='{$this->lieferantennummer_buchhaltung}',
      `lead`='{$this->lead}',
      `zahlungsweiseabo`='{$this->zahlungsweiseabo}',
      `bundesland`='{$this->bundesland}',
      `mandatsreferenzhinweis`='{$this->mandatsreferenzhinweis}',
      `geburtstagkalender`='{$this->geburtstagkalender}',
      `geburtstagskarte`='{$this->geburtstagskarte}',
      `liefersperredatum`='{$this->liefersperredatum}',
      `umsatzsteuer_lieferant`='{$this->umsatzsteuer_lieferant}',
      `lat`='{$this->lat}',
      `lng`='{$this->lng}',
      `art`='{$this->art}',
      `fromshop`='{$this->fromshop}',
      `freifeld11`='{$this->freifeld11}',
      `freifeld12`='{$this->freifeld12}',
      `freifeld13`='{$this->freifeld13}',
      `freifeld14`='{$this->freifeld14}',
      `freifeld15`='{$this->freifeld15}',
      `freifeld16`='{$this->freifeld16}',
      `freifeld17`='{$this->freifeld17}',
      `freifeld18`='{$this->freifeld18}',
      `freifeld19`='{$this->freifeld19}',
      `freifeld20`='{$this->freifeld20}',
      `angebot_email`='{$this->angebot_email}',
      `auftrag_email`='{$this->auftrag_email}',
      `rechnungs_email`='{$this->rechnungs_email}',
      `gutschrift_email`='{$this->gutschrift_email}',
      `lieferschein_email`='{$this->lieferschein_email}',
      `bestellung_email`='{$this->bestellung_email}',
      `lieferschwellenichtanwenden`='{$this->lieferschwellenichtanwenden}',
      `hinweistextlieferant`='{$this->hinweistextlieferant}',
      `firmensepa`='{$this->firmensepa}',
      `hinweis_einfuegen`='{$this->hinweis_einfuegen}',
      `anzeigesteuerbelege`='{$this->anzeigesteuerbelege}',
      `gln`='{$this->gln}',
      `rechnung_gln`='{$this->rechnung_gln}',
      `keinealtersabfrage`='{$this->keinealtersabfrage}',
      `lieferbedingung`='{$this->lieferbedingung}',
      `mlmintranetgesamtestruktur`='{$this->mlmintranetgesamtestruktur}',
      `kommissionskonsignationslager`='{$this->kommissionskonsignationslager}',
      `zollinformationen`='{$this->zollinformationen}',
      `bundesstaat`='{$this->bundesstaat}',
      `rechnung_bundesstaat`='{$this->rechnung_bundesstaat}'
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

    $sql = "DELETE FROM `adresse` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->typ='';
    $this->marketingsperre='';
    $this->trackingsperre='';
    $this->rechnungsadresse='';
    $this->sprache='';
    $this->name='';
    $this->abteilung='';
    $this->unterabteilung='';
    $this->ansprechpartner='';
    $this->land='';
    $this->strasse='';
    $this->ort='';
    $this->plz='';
    $this->telefon='';
    $this->telefax='';
    $this->mobil='';
    $this->email='';
    $this->ustid='';
    $this->ust_befreit='';
    $this->passwort_gesendet='';
    $this->sonstiges='';
    $this->adresszusatz='';
    $this->kundenfreigabe='';
    $this->steuer='';
    $this->logdatei='';
    $this->kundennummer='';
    $this->lieferantennummer='';
    $this->mitarbeiternummer='';
    $this->konto='';
    $this->blz='';
    $this->bank='';
    $this->inhaber='';
    $this->swift='';
    $this->iban='';
    $this->waehrung='';
    $this->paypal='';
    $this->paypalinhaber='';
    $this->paypalwaehrung='';
    $this->projekt='';
    $this->partner='';
    $this->zahlungsweise='';
    $this->zahlungszieltage='';
    $this->zahlungszieltageskonto='';
    $this->zahlungszielskonto='';
    $this->versandart='';
    $this->kundennummerlieferant='';
    $this->zahlungsweiselieferant='';
    $this->zahlungszieltagelieferant='';
    $this->zahlungszieltageskontolieferant='';
    $this->zahlungszielskontolieferant='';
    $this->versandartlieferant='';
    $this->geloescht='';
    $this->firma='';
    $this->webid='';
    $this->vorname='';
    $this->kennung='';
    $this->sachkonto='';
    $this->freifeld1='';
    $this->freifeld2='';
    $this->freifeld3='';
    $this->filiale='';
    $this->vertrieb='';
    $this->innendienst='';
    $this->verbandsnummer='';
    $this->abweichendeemailab='';
    $this->portofrei_aktiv='';
    $this->portofreiab='';
    $this->infoauftragserfassung='';
    $this->mandatsreferenz='';
    $this->mandatsreferenzdatum='';
    $this->mandatsreferenzaenderung='';
    $this->glaeubigeridentnr='';
    $this->kreditlimit='';
    $this->tour='';
    $this->zahlungskonditionen_festschreiben='';
    $this->rabatte_festschreiben='';
    $this->mlmaktiv='';
    $this->mlmvertragsbeginn='';
    $this->mlmlizenzgebuehrbis='';
    $this->mlmfestsetzenbis='';
    $this->mlmfestsetzen='';
    $this->mlmmindestpunkte='';
    $this->mlmwartekonto='';
    $this->abweichende_rechnungsadresse='';
    $this->rechnung_vorname='';
    $this->rechnung_name='';
    $this->rechnung_titel='';
    $this->rechnung_typ='';
    $this->rechnung_strasse='';
    $this->rechnung_ort='';
    $this->rechnung_plz='';
    $this->rechnung_ansprechpartner='';
    $this->rechnung_land='';
    $this->rechnung_abteilung='';
    $this->rechnung_unterabteilung='';
    $this->rechnung_adresszusatz='';
    $this->rechnung_telefon='';
    $this->rechnung_telefax='';
    $this->rechnung_anschreiben='';
    $this->rechnung_email='';
    $this->geburtstag='';
    $this->rolledatum='';
    $this->liefersperre='';
    $this->liefersperregrund='';
    $this->mlmpositionierung='';
    $this->steuernummer='';
    $this->steuerbefreit='';
    $this->mlmmitmwst='';
    $this->mlmabrechnung='';
    $this->mlmwaehrungauszahlung='';
    $this->mlmauszahlungprojekt='';
    $this->sponsor='';
    $this->geworbenvon='';
    $this->logfile='';
    $this->kalender_aufgaben='';
    $this->verrechnungskontoreisekosten='';
    $this->usereditid='';
    $this->useredittimestamp='';
    $this->rabatt='';
    $this->provision='';
    $this->rabattinformation='';
    $this->rabatt1='';
    $this->rabatt2='';
    $this->rabatt3='';
    $this->rabatt4='';
    $this->rabatt5='';
    $this->internetseite='';
    $this->bonus1='';
    $this->bonus1_ab='';
    $this->bonus2='';
    $this->bonus2_ab='';
    $this->bonus3='';
    $this->bonus3_ab='';
    $this->bonus4='';
    $this->bonus4_ab='';
    $this->bonus5='';
    $this->bonus5_ab='';
    $this->bonus6='';
    $this->bonus6_ab='';
    $this->bonus7='';
    $this->bonus7_ab='';
    $this->bonus8='';
    $this->bonus8_ab='';
    $this->bonus9='';
    $this->bonus9_ab='';
    $this->bonus10='';
    $this->bonus10_ab='';
    $this->rechnung_periode='';
    $this->rechnung_anzahlpapier='';
    $this->rechnung_permail='';
    $this->titel='';
    $this->anschreiben='';
    $this->nachname='';
    $this->arbeitszeitprowoche='';
    $this->folgebestaetigungsperre='';
    $this->lieferantennummerbeikunde='';
    $this->verein_mitglied_seit='';
    $this->verein_mitglied_bis='';
    $this->verein_mitglied_aktiv='';
    $this->verein_spendenbescheinigung='';
    $this->freifeld4='';
    $this->freifeld5='';
    $this->freifeld6='';
    $this->freifeld7='';
    $this->freifeld8='';
    $this->freifeld9='';
    $this->freifeld10='';
    $this->rechnung_papier='';
    $this->angebot_cc='';
    $this->auftrag_cc='';
    $this->rechnung_cc='';
    $this->gutschrift_cc='';
    $this->lieferschein_cc='';
    $this->bestellung_cc='';
    $this->angebot_fax_cc='';
    $this->auftrag_fax_cc='';
    $this->rechnung_fax_cc='';
    $this->gutschrift_fax_cc='';
    $this->lieferschein_fax_cc='';
    $this->bestellung_fax_cc='';
    $this->abperfax='';
    $this->abpermail='';
    $this->kassiereraktiv='';
    $this->kassierernummer='';
    $this->kassiererprojekt='';
    $this->portofreilieferant_aktiv='';
    $this->portofreiablieferant='';
    $this->mandatsreferenzart='';
    $this->mandatsreferenzwdhart='';
    $this->serienbrief='';
    $this->kundennummer_buchhaltung='';
    $this->lieferantennummer_buchhaltung='';
    $this->lead='';
    $this->zahlungsweiseabo='';
    $this->bundesland='';
    $this->mandatsreferenzhinweis='';
    $this->geburtstagkalender='';
    $this->geburtstagskarte='';
    $this->liefersperredatum='';
    $this->umsatzsteuer_lieferant='';
    $this->lat='';
    $this->lng='';
    $this->art='';
    $this->fromshop='';
    $this->freifeld11='';
    $this->freifeld12='';
    $this->freifeld13='';
    $this->freifeld14='';
    $this->freifeld15='';
    $this->freifeld16='';
    $this->freifeld17='';
    $this->freifeld18='';
    $this->freifeld19='';
    $this->freifeld20='';
    $this->angebot_email='';
    $this->auftrag_email='';
    $this->rechnungs_email='';
    $this->gutschrift_email='';
    $this->lieferschein_email='';
    $this->bestellung_email='';
    $this->lieferschwellenichtanwenden='';
    $this->hinweistextlieferant='';
    $this->firmensepa='';
    $this->hinweis_einfuegen='';
    $this->anzeigesteuerbelege='';
    $this->gln='';
    $this->rechnung_gln='';
    $this->keinealtersabfrage='';
    $this->lieferbedingung='';
    $this->mlmintranetgesamtestruktur='';
    $this->kommissionskonsignationslager='';
    $this->zollinformationen='';
    $this->bundesstaat='';
    $this->rechnung_bundesstaat='';
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
  public function SetTyp($value) { $this->typ=$value; }
  public function GetTyp() { return $this->typ; }
  public function SetMarketingsperre($value) { $this->marketingsperre=$value; }
  public function GetMarketingsperre() { return $this->marketingsperre; }
  public function SetTrackingsperre($value) { $this->trackingsperre=$value; }
  public function GetTrackingsperre() { return $this->trackingsperre; }
  public function SetRechnungsadresse($value) { $this->rechnungsadresse=$value; }
  public function GetRechnungsadresse() { return $this->rechnungsadresse; }
  public function SetSprache($value) { $this->sprache=$value; }
  public function GetSprache() { return $this->sprache; }
  public function SetName($value) { $this->name=$value; }
  public function GetName() { return $this->name; }
  public function SetAbteilung($value) { $this->abteilung=$value; }
  public function GetAbteilung() { return $this->abteilung; }
  public function SetUnterabteilung($value) { $this->unterabteilung=$value; }
  public function GetUnterabteilung() { return $this->unterabteilung; }
  public function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  public function GetAnsprechpartner() { return $this->ansprechpartner; }
  public function SetLand($value) { $this->land=$value; }
  public function GetLand() { return $this->land; }
  public function SetStrasse($value) { $this->strasse=$value; }
  public function GetStrasse() { return $this->strasse; }
  public function SetOrt($value) { $this->ort=$value; }
  public function GetOrt() { return $this->ort; }
  public function SetPlz($value) { $this->plz=$value; }
  public function GetPlz() { return $this->plz; }
  public function SetTelefon($value) { $this->telefon=$value; }
  public function GetTelefon() { return $this->telefon; }
  public function SetTelefax($value) { $this->telefax=$value; }
  public function GetTelefax() { return $this->telefax; }
  public function SetMobil($value) { $this->mobil=$value; }
  public function GetMobil() { return $this->mobil; }
  public function SetEmail($value) { $this->email=$value; }
  public function GetEmail() { return $this->email; }
  public function SetUstid($value) { $this->ustid=$value; }
  public function GetUstid() { return $this->ustid; }
  public function SetUst_Befreit($value) { $this->ust_befreit=$value; }
  public function GetUst_Befreit() { return $this->ust_befreit; }
  public function SetPasswort_Gesendet($value) { $this->passwort_gesendet=$value; }
  public function GetPasswort_Gesendet() { return $this->passwort_gesendet; }
  public function SetSonstiges($value) { $this->sonstiges=$value; }
  public function GetSonstiges() { return $this->sonstiges; }
  public function SetAdresszusatz($value) { $this->adresszusatz=$value; }
  public function GetAdresszusatz() { return $this->adresszusatz; }
  public function SetKundenfreigabe($value) { $this->kundenfreigabe=$value; }
  public function GetKundenfreigabe() { return $this->kundenfreigabe; }
  public function SetSteuer($value) { $this->steuer=$value; }
  public function GetSteuer() { return $this->steuer; }
  public function SetLogdatei($value) { $this->logdatei=$value; }
  public function GetLogdatei() { return $this->logdatei; }
  public function SetKundennummer($value) { $this->kundennummer=$value; }
  public function GetKundennummer() { return $this->kundennummer; }
  public function SetLieferantennummer($value) { $this->lieferantennummer=$value; }
  public function GetLieferantennummer() { return $this->lieferantennummer; }
  public function SetMitarbeiternummer($value) { $this->mitarbeiternummer=$value; }
  public function GetMitarbeiternummer() { return $this->mitarbeiternummer; }
  public function SetKonto($value) { $this->konto=$value; }
  public function GetKonto() { return $this->konto; }
  public function SetBlz($value) { $this->blz=$value; }
  public function GetBlz() { return $this->blz; }
  public function SetBank($value) { $this->bank=$value; }
  public function GetBank() { return $this->bank; }
  public function SetInhaber($value) { $this->inhaber=$value; }
  public function GetInhaber() { return $this->inhaber; }
  public function SetSwift($value) { $this->swift=$value; }
  public function GetSwift() { return $this->swift; }
  public function SetIban($value) { $this->iban=$value; }
  public function GetIban() { return $this->iban; }
  public function SetWaehrung($value) { $this->waehrung=$value; }
  public function GetWaehrung() { return $this->waehrung; }
  public function SetPaypal($value) { $this->paypal=$value; }
  public function GetPaypal() { return $this->paypal; }
  public function SetPaypalinhaber($value) { $this->paypalinhaber=$value; }
  public function GetPaypalinhaber() { return $this->paypalinhaber; }
  public function SetPaypalwaehrung($value) { $this->paypalwaehrung=$value; }
  public function GetPaypalwaehrung() { return $this->paypalwaehrung; }
  public function SetProjekt($value) { $this->projekt=$value; }
  public function GetProjekt() { return $this->projekt; }
  public function SetPartner($value) { $this->partner=$value; }
  public function GetPartner() { return $this->partner; }
  public function SetZahlungsweise($value) { $this->zahlungsweise=$value; }
  public function GetZahlungsweise() { return $this->zahlungsweise; }
  public function SetZahlungszieltage($value) { $this->zahlungszieltage=$value; }
  public function GetZahlungszieltage() { return $this->zahlungszieltage; }
  public function SetZahlungszieltageskonto($value) { $this->zahlungszieltageskonto=$value; }
  public function GetZahlungszieltageskonto() { return $this->zahlungszieltageskonto; }
  public function SetZahlungszielskonto($value) { $this->zahlungszielskonto=$value; }
  public function GetZahlungszielskonto() { return $this->zahlungszielskonto; }
  public function SetVersandart($value) { $this->versandart=$value; }
  public function GetVersandart() { return $this->versandart; }
  public function SetKundennummerlieferant($value) { $this->kundennummerlieferant=$value; }
  public function GetKundennummerlieferant() { return $this->kundennummerlieferant; }
  public function SetZahlungsweiselieferant($value) { $this->zahlungsweiselieferant=$value; }
  public function GetZahlungsweiselieferant() { return $this->zahlungsweiselieferant; }
  public function SetZahlungszieltagelieferant($value) { $this->zahlungszieltagelieferant=$value; }
  public function GetZahlungszieltagelieferant() { return $this->zahlungszieltagelieferant; }
  public function SetZahlungszieltageskontolieferant($value) { $this->zahlungszieltageskontolieferant=$value; }
  public function GetZahlungszieltageskontolieferant() { return $this->zahlungszieltageskontolieferant; }
  public function SetZahlungszielskontolieferant($value) { $this->zahlungszielskontolieferant=$value; }
  public function GetZahlungszielskontolieferant() { return $this->zahlungszielskontolieferant; }
  public function SetVersandartlieferant($value) { $this->versandartlieferant=$value; }
  public function GetVersandartlieferant() { return $this->versandartlieferant; }
  public function SetGeloescht($value) { $this->geloescht=$value; }
  public function GetGeloescht() { return $this->geloescht; }
  public function SetFirma($value) { $this->firma=$value; }
  public function GetFirma() { return $this->firma; }
  public function SetWebid($value) { $this->webid=$value; }
  public function GetWebid() { return $this->webid; }
  public function SetVorname($value) { $this->vorname=$value; }
  public function GetVorname() { return $this->vorname; }
  public function SetKennung($value) { $this->kennung=$value; }
  public function GetKennung() { return $this->kennung; }
  public function SetSachkonto($value) { $this->sachkonto=$value; }
  public function GetSachkonto() { return $this->sachkonto; }
  public function SetFreifeld1($value) { $this->freifeld1=$value; }
  public function GetFreifeld1() { return $this->freifeld1; }
  public function SetFreifeld2($value) { $this->freifeld2=$value; }
  public function GetFreifeld2() { return $this->freifeld2; }
  public function SetFreifeld3($value) { $this->freifeld3=$value; }
  public function GetFreifeld3() { return $this->freifeld3; }
  public function SetFiliale($value) { $this->filiale=$value; }
  public function GetFiliale() { return $this->filiale; }
  public function SetVertrieb($value) { $this->vertrieb=$value; }
  public function GetVertrieb() { return $this->vertrieb; }
  public function SetInnendienst($value) { $this->innendienst=$value; }
  public function GetInnendienst() { return $this->innendienst; }
  public function SetVerbandsnummer($value) { $this->verbandsnummer=$value; }
  public function GetVerbandsnummer() { return $this->verbandsnummer; }
  public function SetAbweichendeemailab($value) { $this->abweichendeemailab=$value; }
  public function GetAbweichendeemailab() { return $this->abweichendeemailab; }
  public function SetPortofrei_Aktiv($value) { $this->portofrei_aktiv=$value; }
  public function GetPortofrei_Aktiv() { return $this->portofrei_aktiv; }
  public function SetPortofreiab($value) { $this->portofreiab=$value; }
  public function GetPortofreiab() { return $this->portofreiab; }
  public function SetInfoauftragserfassung($value) { $this->infoauftragserfassung=$value; }
  public function GetInfoauftragserfassung() { return $this->infoauftragserfassung; }
  public function SetMandatsreferenz($value) { $this->mandatsreferenz=$value; }
  public function GetMandatsreferenz() { return $this->mandatsreferenz; }
  public function SetMandatsreferenzdatum($value) { $this->mandatsreferenzdatum=$value; }
  public function GetMandatsreferenzdatum() { return $this->mandatsreferenzdatum; }
  public function SetMandatsreferenzaenderung($value) { $this->mandatsreferenzaenderung=$value; }
  public function GetMandatsreferenzaenderung() { return $this->mandatsreferenzaenderung; }
  public function SetGlaeubigeridentnr($value) { $this->glaeubigeridentnr=$value; }
  public function GetGlaeubigeridentnr() { return $this->glaeubigeridentnr; }
  public function SetKreditlimit($value) { $this->kreditlimit=$value; }
  public function GetKreditlimit() { return $this->kreditlimit; }
  public function SetTour($value) { $this->tour=$value; }
  public function GetTour() { return $this->tour; }
  public function SetZahlungskonditionen_Festschreiben($value) { $this->zahlungskonditionen_festschreiben=$value; }
  public function GetZahlungskonditionen_Festschreiben() { return $this->zahlungskonditionen_festschreiben; }
  public function SetRabatte_Festschreiben($value) { $this->rabatte_festschreiben=$value; }
  public function GetRabatte_Festschreiben() { return $this->rabatte_festschreiben; }
  public function SetMlmaktiv($value) { $this->mlmaktiv=$value; }
  public function GetMlmaktiv() { return $this->mlmaktiv; }
  public function SetMlmvertragsbeginn($value) { $this->mlmvertragsbeginn=$value; }
  public function GetMlmvertragsbeginn() { return $this->mlmvertragsbeginn; }
  public function SetMlmlizenzgebuehrbis($value) { $this->mlmlizenzgebuehrbis=$value; }
  public function GetMlmlizenzgebuehrbis() { return $this->mlmlizenzgebuehrbis; }
  public function SetMlmfestsetzenbis($value) { $this->mlmfestsetzenbis=$value; }
  public function GetMlmfestsetzenbis() { return $this->mlmfestsetzenbis; }
  public function SetMlmfestsetzen($value) { $this->mlmfestsetzen=$value; }
  public function GetMlmfestsetzen() { return $this->mlmfestsetzen; }
  public function SetMlmmindestpunkte($value) { $this->mlmmindestpunkte=$value; }
  public function GetMlmmindestpunkte() { return $this->mlmmindestpunkte; }
  public function SetMlmwartekonto($value) { $this->mlmwartekonto=$value; }
  public function GetMlmwartekonto() { return $this->mlmwartekonto; }
  public function SetAbweichende_Rechnungsadresse($value) { $this->abweichende_rechnungsadresse=$value; }
  public function GetAbweichende_Rechnungsadresse() { return $this->abweichende_rechnungsadresse; }
  public function SetRechnung_Vorname($value) { $this->rechnung_vorname=$value; }
  public function GetRechnung_Vorname() { return $this->rechnung_vorname; }
  public function SetRechnung_Name($value) { $this->rechnung_name=$value; }
  public function GetRechnung_Name() { return $this->rechnung_name; }
  public function SetRechnung_Titel($value) { $this->rechnung_titel=$value; }
  public function GetRechnung_Titel() { return $this->rechnung_titel; }
  public function SetRechnung_Typ($value) { $this->rechnung_typ=$value; }
  public function GetRechnung_Typ() { return $this->rechnung_typ; }
  public function SetRechnung_Strasse($value) { $this->rechnung_strasse=$value; }
  public function GetRechnung_Strasse() { return $this->rechnung_strasse; }
  public function SetRechnung_Ort($value) { $this->rechnung_ort=$value; }
  public function GetRechnung_Ort() { return $this->rechnung_ort; }
  public function SetRechnung_Plz($value) { $this->rechnung_plz=$value; }
  public function GetRechnung_Plz() { return $this->rechnung_plz; }
  public function SetRechnung_Ansprechpartner($value) { $this->rechnung_ansprechpartner=$value; }
  public function GetRechnung_Ansprechpartner() { return $this->rechnung_ansprechpartner; }
  public function SetRechnung_Land($value) { $this->rechnung_land=$value; }
  public function GetRechnung_Land() { return $this->rechnung_land; }
  public function SetRechnung_Abteilung($value) { $this->rechnung_abteilung=$value; }
  public function GetRechnung_Abteilung() { return $this->rechnung_abteilung; }
  public function SetRechnung_Unterabteilung($value) { $this->rechnung_unterabteilung=$value; }
  public function GetRechnung_Unterabteilung() { return $this->rechnung_unterabteilung; }
  public function SetRechnung_Adresszusatz($value) { $this->rechnung_adresszusatz=$value; }
  public function GetRechnung_Adresszusatz() { return $this->rechnung_adresszusatz; }
  public function SetRechnung_Telefon($value) { $this->rechnung_telefon=$value; }
  public function GetRechnung_Telefon() { return $this->rechnung_telefon; }
  public function SetRechnung_Telefax($value) { $this->rechnung_telefax=$value; }
  public function GetRechnung_Telefax() { return $this->rechnung_telefax; }
  public function SetRechnung_Anschreiben($value) { $this->rechnung_anschreiben=$value; }
  public function GetRechnung_Anschreiben() { return $this->rechnung_anschreiben; }
  public function SetRechnung_Email($value) { $this->rechnung_email=$value; }
  public function GetRechnung_Email() { return $this->rechnung_email; }
  public function SetGeburtstag($value) { $this->geburtstag=$value; }
  public function GetGeburtstag() { return $this->geburtstag; }
  public function SetRolledatum($value) { $this->rolledatum=$value; }
  public function GetRolledatum() { return $this->rolledatum; }
  public function SetLiefersperre($value) { $this->liefersperre=$value; }
  public function GetLiefersperre() { return $this->liefersperre; }
  public function SetLiefersperregrund($value) { $this->liefersperregrund=$value; }
  public function GetLiefersperregrund() { return $this->liefersperregrund; }
  public function SetMlmpositionierung($value) { $this->mlmpositionierung=$value; }
  public function GetMlmpositionierung() { return $this->mlmpositionierung; }
  public function SetSteuernummer($value) { $this->steuernummer=$value; }
  public function GetSteuernummer() { return $this->steuernummer; }
  public function SetSteuerbefreit($value) { $this->steuerbefreit=$value; }
  public function GetSteuerbefreit() { return $this->steuerbefreit; }
  public function SetMlmmitmwst($value) { $this->mlmmitmwst=$value; }
  public function GetMlmmitmwst() { return $this->mlmmitmwst; }
  public function SetMlmabrechnung($value) { $this->mlmabrechnung=$value; }
  public function GetMlmabrechnung() { return $this->mlmabrechnung; }
  public function SetMlmwaehrungauszahlung($value) { $this->mlmwaehrungauszahlung=$value; }
  public function GetMlmwaehrungauszahlung() { return $this->mlmwaehrungauszahlung; }
  public function SetMlmauszahlungprojekt($value) { $this->mlmauszahlungprojekt=$value; }
  public function GetMlmauszahlungprojekt() { return $this->mlmauszahlungprojekt; }
  public function SetSponsor($value) { $this->sponsor=$value; }
  public function GetSponsor() { return $this->sponsor; }
  public function SetGeworbenvon($value) { $this->geworbenvon=$value; }
  public function GetGeworbenvon() { return $this->geworbenvon; }
  public function SetLogfile($value) { $this->logfile=$value; }
  public function GetLogfile() { return $this->logfile; }
  public function SetKalender_Aufgaben($value) { $this->kalender_aufgaben=$value; }
  public function GetKalender_Aufgaben() { return $this->kalender_aufgaben; }
  public function SetVerrechnungskontoreisekosten($value) { $this->verrechnungskontoreisekosten=$value; }
  public function GetVerrechnungskontoreisekosten() { return $this->verrechnungskontoreisekosten; }
  public function SetUsereditid($value) { $this->usereditid=$value; }
  public function GetUsereditid() { return $this->usereditid; }
  public function SetUseredittimestamp($value) { $this->useredittimestamp=$value; }
  public function GetUseredittimestamp() { return $this->useredittimestamp; }
  public function SetRabatt($value) { $this->rabatt=$value; }
  public function GetRabatt() { return $this->rabatt; }
  public function SetProvision($value) { $this->provision=$value; }
  public function GetProvision() { return $this->provision; }
  public function SetRabattinformation($value) { $this->rabattinformation=$value; }
  public function GetRabattinformation() { return $this->rabattinformation; }
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
  public function SetInternetseite($value) { $this->internetseite=$value; }
  public function GetInternetseite() { return $this->internetseite; }
  public function SetBonus1($value) { $this->bonus1=$value; }
  public function GetBonus1() { return $this->bonus1; }
  public function SetBonus1_Ab($value) { $this->bonus1_ab=$value; }
  public function GetBonus1_Ab() { return $this->bonus1_ab; }
  public function SetBonus2($value) { $this->bonus2=$value; }
  public function GetBonus2() { return $this->bonus2; }
  public function SetBonus2_Ab($value) { $this->bonus2_ab=$value; }
  public function GetBonus2_Ab() { return $this->bonus2_ab; }
  public function SetBonus3($value) { $this->bonus3=$value; }
  public function GetBonus3() { return $this->bonus3; }
  public function SetBonus3_Ab($value) { $this->bonus3_ab=$value; }
  public function GetBonus3_Ab() { return $this->bonus3_ab; }
  public function SetBonus4($value) { $this->bonus4=$value; }
  public function GetBonus4() { return $this->bonus4; }
  public function SetBonus4_Ab($value) { $this->bonus4_ab=$value; }
  public function GetBonus4_Ab() { return $this->bonus4_ab; }
  public function SetBonus5($value) { $this->bonus5=$value; }
  public function GetBonus5() { return $this->bonus5; }
  public function SetBonus5_Ab($value) { $this->bonus5_ab=$value; }
  public function GetBonus5_Ab() { return $this->bonus5_ab; }
  public function SetBonus6($value) { $this->bonus6=$value; }
  public function GetBonus6() { return $this->bonus6; }
  public function SetBonus6_Ab($value) { $this->bonus6_ab=$value; }
  public function GetBonus6_Ab() { return $this->bonus6_ab; }
  public function SetBonus7($value) { $this->bonus7=$value; }
  public function GetBonus7() { return $this->bonus7; }
  public function SetBonus7_Ab($value) { $this->bonus7_ab=$value; }
  public function GetBonus7_Ab() { return $this->bonus7_ab; }
  public function SetBonus8($value) { $this->bonus8=$value; }
  public function GetBonus8() { return $this->bonus8; }
  public function SetBonus8_Ab($value) { $this->bonus8_ab=$value; }
  public function GetBonus8_Ab() { return $this->bonus8_ab; }
  public function SetBonus9($value) { $this->bonus9=$value; }
  public function GetBonus9() { return $this->bonus9; }
  public function SetBonus9_Ab($value) { $this->bonus9_ab=$value; }
  public function GetBonus9_Ab() { return $this->bonus9_ab; }
  public function SetBonus10($value) { $this->bonus10=$value; }
  public function GetBonus10() { return $this->bonus10; }
  public function SetBonus10_Ab($value) { $this->bonus10_ab=$value; }
  public function GetBonus10_Ab() { return $this->bonus10_ab; }
  public function SetRechnung_Periode($value) { $this->rechnung_periode=$value; }
  public function GetRechnung_Periode() { return $this->rechnung_periode; }
  public function SetRechnung_Anzahlpapier($value) { $this->rechnung_anzahlpapier=$value; }
  public function GetRechnung_Anzahlpapier() { return $this->rechnung_anzahlpapier; }
  public function SetRechnung_Permail($value) { $this->rechnung_permail=$value; }
  public function GetRechnung_Permail() { return $this->rechnung_permail; }
  public function SetTitel($value) { $this->titel=$value; }
  public function GetTitel() { return $this->titel; }
  public function SetAnschreiben($value) { $this->anschreiben=$value; }
  public function GetAnschreiben() { return $this->anschreiben; }
  public function SetNachname($value) { $this->nachname=$value; }
  public function GetNachname() { return $this->nachname; }
  public function SetArbeitszeitprowoche($value) { $this->arbeitszeitprowoche=$value; }
  public function GetArbeitszeitprowoche() { return $this->arbeitszeitprowoche; }
  public function SetFolgebestaetigungsperre($value) { $this->folgebestaetigungsperre=$value; }
  public function GetFolgebestaetigungsperre() { return $this->folgebestaetigungsperre; }
  public function SetLieferantennummerbeikunde($value) { $this->lieferantennummerbeikunde=$value; }
  public function GetLieferantennummerbeikunde() { return $this->lieferantennummerbeikunde; }
  public function SetVerein_Mitglied_Seit($value) { $this->verein_mitglied_seit=$value; }
  public function GetVerein_Mitglied_Seit() { return $this->verein_mitglied_seit; }
  public function SetVerein_Mitglied_Bis($value) { $this->verein_mitglied_bis=$value; }
  public function GetVerein_Mitglied_Bis() { return $this->verein_mitglied_bis; }
  public function SetVerein_Mitglied_Aktiv($value) { $this->verein_mitglied_aktiv=$value; }
  public function GetVerein_Mitglied_Aktiv() { return $this->verein_mitglied_aktiv; }
  public function SetVerein_Spendenbescheinigung($value) { $this->verein_spendenbescheinigung=$value; }
  public function GetVerein_Spendenbescheinigung() { return $this->verein_spendenbescheinigung; }
  public function SetFreifeld4($value) { $this->freifeld4=$value; }
  public function GetFreifeld4() { return $this->freifeld4; }
  public function SetFreifeld5($value) { $this->freifeld5=$value; }
  public function GetFreifeld5() { return $this->freifeld5; }
  public function SetFreifeld6($value) { $this->freifeld6=$value; }
  public function GetFreifeld6() { return $this->freifeld6; }
  public function SetFreifeld7($value) { $this->freifeld7=$value; }
  public function GetFreifeld7() { return $this->freifeld7; }
  public function SetFreifeld8($value) { $this->freifeld8=$value; }
  public function GetFreifeld8() { return $this->freifeld8; }
  public function SetFreifeld9($value) { $this->freifeld9=$value; }
  public function GetFreifeld9() { return $this->freifeld9; }
  public function SetFreifeld10($value) { $this->freifeld10=$value; }
  public function GetFreifeld10() { return $this->freifeld10; }
  public function SetRechnung_Papier($value) { $this->rechnung_papier=$value; }
  public function GetRechnung_Papier() { return $this->rechnung_papier; }
  public function SetAngebot_Cc($value) { $this->angebot_cc=$value; }
  public function GetAngebot_Cc() { return $this->angebot_cc; }
  public function SetAuftrag_Cc($value) { $this->auftrag_cc=$value; }
  public function GetAuftrag_Cc() { return $this->auftrag_cc; }
  public function SetRechnung_Cc($value) { $this->rechnung_cc=$value; }
  public function GetRechnung_Cc() { return $this->rechnung_cc; }
  public function SetGutschrift_Cc($value) { $this->gutschrift_cc=$value; }
  public function GetGutschrift_Cc() { return $this->gutschrift_cc; }
  public function SetLieferschein_Cc($value) { $this->lieferschein_cc=$value; }
  public function GetLieferschein_Cc() { return $this->lieferschein_cc; }
  public function SetBestellung_Cc($value) { $this->bestellung_cc=$value; }
  public function GetBestellung_Cc() { return $this->bestellung_cc; }
  public function SetAngebot_Fax_Cc($value) { $this->angebot_fax_cc=$value; }
  public function GetAngebot_Fax_Cc() { return $this->angebot_fax_cc; }
  public function SetAuftrag_Fax_Cc($value) { $this->auftrag_fax_cc=$value; }
  public function GetAuftrag_Fax_Cc() { return $this->auftrag_fax_cc; }
  public function SetRechnung_Fax_Cc($value) { $this->rechnung_fax_cc=$value; }
  public function GetRechnung_Fax_Cc() { return $this->rechnung_fax_cc; }
  public function SetGutschrift_Fax_Cc($value) { $this->gutschrift_fax_cc=$value; }
  public function GetGutschrift_Fax_Cc() { return $this->gutschrift_fax_cc; }
  public function SetLieferschein_Fax_Cc($value) { $this->lieferschein_fax_cc=$value; }
  public function GetLieferschein_Fax_Cc() { return $this->lieferschein_fax_cc; }
  public function SetBestellung_Fax_Cc($value) { $this->bestellung_fax_cc=$value; }
  public function GetBestellung_Fax_Cc() { return $this->bestellung_fax_cc; }
  public function SetAbperfax($value) { $this->abperfax=$value; }
  public function GetAbperfax() { return $this->abperfax; }
  public function SetAbpermail($value) { $this->abpermail=$value; }
  public function GetAbpermail() { return $this->abpermail; }
  public function SetKassiereraktiv($value) { $this->kassiereraktiv=$value; }
  public function GetKassiereraktiv() { return $this->kassiereraktiv; }
  public function SetKassierernummer($value) { $this->kassierernummer=$value; }
  public function GetKassierernummer() { return $this->kassierernummer; }
  public function SetKassiererprojekt($value) { $this->kassiererprojekt=$value; }
  public function GetKassiererprojekt() { return $this->kassiererprojekt; }
  public function SetPortofreilieferant_Aktiv($value) { $this->portofreilieferant_aktiv=$value; }
  public function GetPortofreilieferant_Aktiv() { return $this->portofreilieferant_aktiv; }
  public function SetPortofreiablieferant($value) { $this->portofreiablieferant=$value; }
  public function GetPortofreiablieferant() { return $this->portofreiablieferant; }
  public function SetMandatsreferenzart($value) { $this->mandatsreferenzart=$value; }
  public function GetMandatsreferenzart() { return $this->mandatsreferenzart; }
  public function SetMandatsreferenzwdhart($value) { $this->mandatsreferenzwdhart=$value; }
  public function GetMandatsreferenzwdhart() { return $this->mandatsreferenzwdhart; }
  public function SetSerienbrief($value) { $this->serienbrief=$value; }
  public function GetSerienbrief() { return $this->serienbrief; }
  public function SetKundennummer_Buchhaltung($value) { $this->kundennummer_buchhaltung=$value; }
  public function GetKundennummer_Buchhaltung() { return $this->kundennummer_buchhaltung; }
  public function SetLieferantennummer_Buchhaltung($value) { $this->lieferantennummer_buchhaltung=$value; }
  public function GetLieferantennummer_Buchhaltung() { return $this->lieferantennummer_buchhaltung; }
  public function SetLead($value) { $this->lead=$value; }
  public function GetLead() { return $this->lead; }
  public function SetZahlungsweiseabo($value) { $this->zahlungsweiseabo=$value; }
  public function GetZahlungsweiseabo() { return $this->zahlungsweiseabo; }
  public function SetBundesland($value) { $this->bundesland=$value; }
  public function GetBundesland() { return $this->bundesland; }
  public function SetMandatsreferenzhinweis($value) { $this->mandatsreferenzhinweis=$value; }
  public function GetMandatsreferenzhinweis() { return $this->mandatsreferenzhinweis; }
  public function SetGeburtstagkalender($value) { $this->geburtstagkalender=$value; }
  public function GetGeburtstagkalender() { return $this->geburtstagkalender; }
  public function SetGeburtstagskarte($value) { $this->geburtstagskarte=$value; }
  public function GetGeburtstagskarte() { return $this->geburtstagskarte; }
  public function SetLiefersperredatum($value) { $this->liefersperredatum=$value; }
  public function GetLiefersperredatum() { return $this->liefersperredatum; }
  public function SetUmsatzsteuer_Lieferant($value) { $this->umsatzsteuer_lieferant=$value; }
  public function GetUmsatzsteuer_Lieferant() { return $this->umsatzsteuer_lieferant; }
  public function SetLat($value) { $this->lat=$value; }
  public function GetLat() { return $this->lat; }
  public function SetLng($value) { $this->lng=$value; }
  public function GetLng() { return $this->lng; }
  public function SetArt($value) { $this->art=$value; }
  public function GetArt() { return $this->art; }
  public function SetFromshop($value) { $this->fromshop=$value; }
  public function GetFromshop() { return $this->fromshop; }
  public function SetFreifeld11($value) { $this->freifeld11=$value; }
  public function GetFreifeld11() { return $this->freifeld11; }
  public function SetFreifeld12($value) { $this->freifeld12=$value; }
  public function GetFreifeld12() { return $this->freifeld12; }
  public function SetFreifeld13($value) { $this->freifeld13=$value; }
  public function GetFreifeld13() { return $this->freifeld13; }
  public function SetFreifeld14($value) { $this->freifeld14=$value; }
  public function GetFreifeld14() { return $this->freifeld14; }
  public function SetFreifeld15($value) { $this->freifeld15=$value; }
  public function GetFreifeld15() { return $this->freifeld15; }
  public function SetFreifeld16($value) { $this->freifeld16=$value; }
  public function GetFreifeld16() { return $this->freifeld16; }
  public function SetFreifeld17($value) { $this->freifeld17=$value; }
  public function GetFreifeld17() { return $this->freifeld17; }
  public function SetFreifeld18($value) { $this->freifeld18=$value; }
  public function GetFreifeld18() { return $this->freifeld18; }
  public function SetFreifeld19($value) { $this->freifeld19=$value; }
  public function GetFreifeld19() { return $this->freifeld19; }
  public function SetFreifeld20($value) { $this->freifeld20=$value; }
  public function GetFreifeld20() { return $this->freifeld20; }
  public function SetAngebot_Email($value) { $this->angebot_email=$value; }
  public function GetAngebot_Email() { return $this->angebot_email; }
  public function SetAuftrag_Email($value) { $this->auftrag_email=$value; }
  public function GetAuftrag_Email() { return $this->auftrag_email; }
  public function SetRechnungs_Email($value) { $this->rechnungs_email=$value; }
  public function GetRechnungs_Email() { return $this->rechnungs_email; }
  public function SetGutschrift_Email($value) { $this->gutschrift_email=$value; }
  public function GetGutschrift_Email() { return $this->gutschrift_email; }
  public function SetLieferschein_Email($value) { $this->lieferschein_email=$value; }
  public function GetLieferschein_Email() { return $this->lieferschein_email; }
  public function SetBestellung_Email($value) { $this->bestellung_email=$value; }
  public function GetBestellung_Email() { return $this->bestellung_email; }
  public function SetLieferschwellenichtanwenden($value) { $this->lieferschwellenichtanwenden=$value; }
  public function GetLieferschwellenichtanwenden() { return $this->lieferschwellenichtanwenden; }
  public function SetHinweistextlieferant($value) { $this->hinweistextlieferant=$value; }
  public function GetHinweistextlieferant() { return $this->hinweistextlieferant; }
  public function SetFirmensepa($value) { $this->firmensepa=$value; }
  public function GetFirmensepa() { return $this->firmensepa; }
  public function SetHinweis_Einfuegen($value) { $this->hinweis_einfuegen=$value; }
  public function GetHinweis_Einfuegen() { return $this->hinweis_einfuegen; }
  public function SetAnzeigesteuerbelege($value) { $this->anzeigesteuerbelege=$value; }
  public function GetAnzeigesteuerbelege() { return $this->anzeigesteuerbelege; }
  public function SetGln($value) { $this->gln=$value; }
  public function GetGln() { return $this->gln; }
  public function SetRechnung_Gln($value) { $this->rechnung_gln=$value; }
  public function GetRechnung_Gln() { return $this->rechnung_gln; }
  public function SetKeinealtersabfrage($value) { $this->keinealtersabfrage=$value; }
  public function GetKeinealtersabfrage() { return $this->keinealtersabfrage; }
  public function SetLieferbedingung($value) { $this->lieferbedingung=$value; }
  public function GetLieferbedingung() { return $this->lieferbedingung; }
  public function SetMlmintranetgesamtestruktur($value) { $this->mlmintranetgesamtestruktur=$value; }
  public function GetMlmintranetgesamtestruktur() { return $this->mlmintranetgesamtestruktur; }
  public function SetKommissionskonsignationslager($value) { $this->kommissionskonsignationslager=$value; }
  public function GetKommissionskonsignationslager() { return $this->kommissionskonsignationslager; }
  public function SetZollinformationen($value) { $this->zollinformationen=$value; }
  public function GetZollinformationen() { return $this->zollinformationen; }
  public function SetBundesstaat($value) { $this->bundesstaat=$value; }
  public function GetBundesstaat() { return $this->bundesstaat; }
  public function SetRechnung_Bundesstaat($value) { $this->rechnung_bundesstaat=$value; }
  public function GetRechnung_Bundesstaat() { return $this->rechnung_bundesstaat; }

}
