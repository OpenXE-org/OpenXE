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

class ObjGenProjekt
{

  private  $id;
  private  $name;
  private  $abkuerzung;
  private  $verantwortlicher;
  private  $beschreibung;
  private  $sonstiges;
  private  $aktiv;
  private  $farbe;
  private  $autoversand;
  private  $checkok;
  private  $portocheck;
  private  $automailrechnung;
  private  $checkname;
  private  $zahlungserinnerung;
  private  $zahlungsmailbedinungen;
  private  $folgebestaetigung;
  private  $stornomail;
  private  $kundenfreigabe_loeschen;
  private  $autobestellung;
  private  $speziallieferschein;
  private  $lieferscheinbriefpapier;
  private  $speziallieferscheinbeschriftung;
  private  $firma;
  private  $geloescht;
  private  $logdatei;
  private  $steuersatz_normal;
  private  $steuersatz_zwischen;
  private  $steuersatz_ermaessigt;
  private  $steuersatz_starkermaessigt;
  private  $steuersatz_dienstleistung;
  private  $waehrung;
  private  $eigenesteuer;
  private  $druckerlogistikstufe1;
  private  $druckerlogistikstufe2;
  private  $selbstabholermail;
  private  $eanherstellerscan;
  private  $reservierung;
  private  $verkaufszahlendiagram;
  private  $oeffentlich;
  private  $shopzwangsprojekt;
  private  $kunde;
  private  $dpdkundennr;
  private  $dhlkundennr;
  private  $dhlformat;
  private  $dpdformat;
  private  $paketmarke_einzeldatei;
  private  $dpdpfad;
  private  $dhlpfad;
  private  $upspfad;
  private  $dhlintodb;
  private  $intraship_enabled;
  private  $intraship_drucker;
  private  $intraship_testmode;
  private  $intraship_user;
  private  $intraship_signature;
  private  $intraship_ekp;
  private  $intraship_api_user;
  private  $intraship_api_password;
  private  $intraship_company_name;
  private  $intraship_street_name;
  private  $intraship_street_number;
  private  $intraship_zip;
  private  $intraship_country;
  private  $intraship_city;
  private  $intraship_email;
  private  $intraship_phone;
  private  $intraship_internet;
  private  $intraship_contact_person;
  private  $intraship_account_owner;
  private  $intraship_account_number;
  private  $intraship_bank_code;
  private  $intraship_bank_name;
  private  $intraship_iban;
  private  $intraship_bic;
  private  $intraship_WeightInKG;
  private  $intraship_LengthInCM;
  private  $intraship_WidthInCM;
  private  $intraship_HeightInCM;
  private  $intraship_PackageType;
  private  $abrechnungsart;
  private  $kommissionierverfahren;
  private  $wechselaufeinstufig;
  private  $projektuebergreifendkommisionieren;
  private  $absendeadresse;
  private  $absendename;
  private  $absendesignatur;
  private  $autodruckrechnung;
  private  $autodruckversandbestaetigung;
  private  $automailversandbestaetigung;
  private  $autodrucklieferschein;
  private  $automaillieferschein;
  private  $autodruckstorno;
  private  $autodruckanhang;
  private  $automailanhang;
  private  $autodruckerrechnung;
  private  $autodruckerlieferschein;
  private  $autodruckeranhang;
  private  $autodruckrechnungmenge;
  private  $autodrucklieferscheinmenge;
  private  $eigenernummernkreis;
  private  $next_angebot;
  private  $next_auftrag;
  private  $next_rechnung;
  private  $next_lieferschein;
  private  $next_arbeitsnachweis;
  private  $next_reisekosten;
  private  $next_bestellung;
  private  $next_gutschrift;
  private  $next_kundennummer;
  private  $next_lieferantennummer;
  private  $next_mitarbeiternummer;
  private  $next_waren;
  private  $next_produktion;
  private  $next_sonstiges;
  private  $next_anfrage;
  private  $next_artikelnummer;
  private  $gesamtstunden_max;
  private  $auftragid;
  private  $dhlzahlungmandant;
  private  $dhlretourenschein;
  private  $land;
  private  $etiketten_positionen;
  private  $etiketten_drucker;
  private  $etiketten_art;
  private  $seriennummernerfassen;
  private  $versandzweigeteilt;
  private  $nachnahmecheck;
  private  $kasse_lieferschein_anlegen;
  private  $kasse_lagerprozess;
  private  $kasse_belegausgabe;
  private  $kasse_preisgruppe;
  private  $kasse_text_bemerkung;
  private  $kasse_text_freitext;
  private  $kasse_drucker;
  private  $kasse_lieferschein;
  private  $kasse_rechnung;
  private  $kasse_lieferschein_doppel;
  private  $kasse_lager;
  private  $kasse_konto;
  private  $kasse_laufkundschaft;
  private  $kasse_rabatt_artikel;
  private  $kasse_zahlung_bar;
  private  $kasse_zahlung_ec;
  private  $kasse_zahlung_kreditkarte;
  private  $kasse_zahlung_ueberweisung;
  private  $kasse_zahlung_paypal;
  private  $kasse_extra_keinbeleg;
  private  $kasse_extra_rechnung;
  private  $kasse_extra_quittung;
  private  $kasse_extra_gutschein;
  private  $kasse_extra_rabatt_prozent;
  private  $kasse_extra_rabatt_euro;
  private  $kasse_adresse_erweitert;
  private  $kasse_zahlungsauswahl_zwang;
  private  $kasse_button_entnahme;
  private  $kasse_button_trinkgeld;
  private  $kasse_vorauswahl_anrede;
  private  $kasse_erweiterte_lagerabfrage;
  private  $filialadresse;
  private  $versandprojektfiliale;
  private  $differenz_auslieferung_tage;
  private  $autostuecklistenanpassung;
  private  $dpdendung;
  private  $dhlendung;
  private  $tracking_substr_start;
  private  $tracking_remove_kundennummer;
  private  $tracking_substr_length;
  private  $go_drucker;
  private  $go_apiurl_prefix;
  private  $go_apiurl_postfix;
  private  $go_apiurl_user;
  private  $go_username;
  private  $go_password;
  private  $go_ax4nr;
  private  $go_name1;
  private  $go_name2;
  private  $go_abteilung;
  private  $go_strasse1;
  private  $go_strasse2;
  private  $go_hausnummer;
  private  $go_plz;
  private  $go_ort;
  private  $go_land;
  private  $go_standardgewicht;
  private  $go_format;
  private  $go_ausgabe;
  private  $intraship_exportgrund;
  private  $billsafe_merchantId;
  private  $billsafe_merchantLicenseSandbox;
  private  $billsafe_merchantLicenseLive;
  private  $billsafe_applicationSignature;
  private  $billsafe_applicationVersion;
  private  $secupay_apikey;
  private  $secupay_url;
  private  $secupay_demo;
  private  $mahnwesen;
  private  $status;
  private  $kasse_bondrucker;
  private  $kasse_bondrucker_aktiv;
  private  $kasse_bondrucker_qrcode;
  private  $kasse_bon_zeile1;
  private  $kasse_bon_zeile2;
  private  $kasse_bon_zeile3;
  private  $kasse_zahlung_bar_bezahlt;
  private  $kasse_zahlung_ec_bezahlt;
  private  $kasse_zahlung_kreditkarte_bezahlt;
  private  $kasse_zahlung_ueberweisung_bezahlt;
  private  $kasse_zahlung_paypal_bezahlt;
  private  $kasse_quittung_rechnung;
  private  $kasse_print_qr;
  private  $kasse_button_einlage;
  private  $kasse_button_schublade;
  private  $produktionauftragautomatischfreigeben;
  private  $versandlagerplatzanzeigen;
  private  $versandartikelnameausstammdaten;
  private  $projektlager;
  private  $tracing_substr_length;
  private  $intraship_partnerid;
  private  $intraship_retourenlabel;
  private  $intraship_retourenaccount;
  private  $absendegrussformel;
  private  $autodruckrechnungdoppel;
  private  $intraship_partnerid_welt;
  private  $next_kalkulation;
  private  $next_preisanfrage;
  private  $next_proformarechnung;
  private  $next_verbindlichkeit;
  private  $freifeld1;
  private  $freifeld2;
  private  $freifeld3;
  private  $freifeld4;
  private  $freifeld5;
  private  $freifeld6;
  private  $freifeld7;
  private  $freifeld8;
  private  $freifeld9;
  private  $freifeld10;
  private  $mahnwesen_abweichender_versender;
  private  $lagerplatzlieferscheinausblenden;
  private  $etiketten_sort;
  private  $eanherstellerscanerlauben;
  private  $chargenerfassen;
  private  $mhderfassen;
  private  $autodruckrechnungstufe1;
  private  $autodruckrechnungstufe1menge;
  private  $autodruckrechnungstufe1mail;
  private  $autodruckkommissionierscheinstufe1;
  private  $autodruckkommissionierscheinstufe1menge;
  private  $kasse_bondrucker_freifeld;
  private  $kasse_bondrucker_anzahl;
  private  $kasse_rksv_aktiv;
  private  $kasse_rksv_tool;
  private  $kasse_rksv_kartenleser;
  private  $kasse_rksv_karteseriennummer;
  private  $kasse_rksv_kartepin;
  private  $kasse_rksv_aeskey;
  private  $kasse_rksv_publiczertifikat;
  private  $kasse_rksv_publiczertifikatkette;
  private  $kasse_rksv_kassenid;
  private  $kasse_gutschrift;
  private  $rechnungerzeugen;
  private  $pos_artikeltexteuebernehmen;
  private  $pos_anzeigenetto;
  private  $pos_zwischenspeichern;
  private  $kasse_button_belegladen;
  private  $kasse_button_storno;
  private  $pos_kundenalleprojekte;
  private  $pos_artikelnurausprojekt;
  private  $allechargenmhd;
  private  $anzeigesteuerbelege;
  private  $pos_grosseansicht;
  private  $preisberechnung;
  private  $steuernummer;
  private  $paketmarkeautodrucken;
  private  $orderpicking_sort;
  private  $deactivateautoshipping;
  private  $pos_sumarticles;
  private  $manualtracking;
  private  $zahlungsweise;
  private  $zahlungsweiselieferant;
  private  $versandart;
  private  $ups_api_user;
  private  $ups_api_password;
  private  $ups_api_key;
  private  $ups_accountnumber;
  private  $ups_company_name;
  private  $ups_street_name;
  private  $ups_street_number;
  private  $ups_zip;
  private  $ups_country;
  private  $ups_city;
  private  $ups_email;
  private  $ups_phone;
  private  $ups_internet;
  private  $ups_contact_person;
  private  $ups_WeightInKG;
  private  $ups_LengthInCM;
  private  $ups_WidthInCM;
  private  $ups_HeightInCM;
  private  $ups_drucker;
  private  $ups_ausgabe;
  private  $ups_package_code;
  private  $ups_package_description;
  private  $ups_service_code;
  private  $ups_service_description;
  private  $email_html_template;
  private  $druckanhang;
  private  $mailanhang;
  private  $next_retoure;
  private  $next_goodspostingdocument;
  private  $pos_disable_single_entries;
  private  $pos_disable_single_day;
  private  $pos_disable_counting_protocol;
  private  $pos_disable_signature;
  private  $steuer_erloese_inland_normal;
  private  $steuer_aufwendung_inland_normal;
  private  $steuer_erloese_inland_ermaessigt;
  private  $steuer_aufwendung_inland_ermaessigt;
  private  $steuer_erloese_inland_nichtsteuerbar;
  private  $steuer_aufwendung_inland_nichtsteuerbar;
  private  $steuer_erloese_inland_innergemeinschaftlich;
  private  $steuer_aufwendung_inland_innergemeinschaftlich;
  private  $steuer_erloese_inland_eunormal;
  private  $steuer_aufwendung_inland_eunormal;
  private  $steuer_erloese_inland_euermaessigt;
  private  $steuer_aufwendung_inland_euermaessigt;
  private  $steuer_erloese_inland_export;
  private  $steuer_aufwendung_inland_import;
  private  $create_proformainvoice;
  private  $print_proformainvoice;
  private  $proformainvoice_amount;
  private  $anzeigesteuerbelegebestellung;
  private  $autobestbeforebatch;
  private  $allwaysautobestbeforebatch;
  private  $kommissionierlauflieferschein;
  private  $intraship_exportdrucker;
  private  $multiorderpicking;
  private  $standardlager;
  private  $standardlagerproduktion;
  private  $klarna_merchantid;
  private  $klarna_sharedsecret;
  private  $nurlagerartikel;
  private  $paketmarkedrucken;
  private  $lieferscheinedrucken;
  private  $lieferscheinedruckenmenge;
  private  $auftragdrucken;
  private  $auftragdruckenmenge;
  private  $druckennachtracking;
  private  $exportdruckrechnungstufe1;
  private  $exportdruckrechnungstufe1menge;
  private  $exportdruckrechnung;
  private  $exportdruckrechnungmenge;
  private  $kommissionierlistestufe1;
  private  $kommissionierlistestufe1menge;
  private  $fremdnummerscanerlauben;
  private  $zvt100url;
  private  $zvt100port;
  private  $production_show_only_needed_storages;
  private  $produktion_extra_seiten;
  private  $kasse_button_trinkgeldeckredit;
  private  $kasse_autologout;
  private  $kasse_autologout_abschluss;
  private  $next_receiptdocument;
  private  $taxfromdoctypesettings;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `projekt` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->name=$result['name'];
    $this->abkuerzung=$result['abkuerzung'];
    $this->verantwortlicher=$result['verantwortlicher'];
    $this->beschreibung=$result['beschreibung'];
    $this->sonstiges=$result['sonstiges'];
    $this->aktiv=$result['aktiv'];
    $this->farbe=$result['farbe'];
    $this->autoversand=$result['autoversand'];
    $this->checkok=$result['checkok'];
    $this->portocheck=$result['portocheck'];
    $this->automailrechnung=$result['automailrechnung'];
    $this->checkname=$result['checkname'];
    $this->zahlungserinnerung=$result['zahlungserinnerung'];
    $this->zahlungsmailbedinungen=$result['zahlungsmailbedinungen'];
    $this->folgebestaetigung=$result['folgebestaetigung'];
    $this->stornomail=$result['stornomail'];
    $this->kundenfreigabe_loeschen=$result['kundenfreigabe_loeschen'];
    $this->autobestellung=$result['autobestellung'];
    $this->speziallieferschein=$result['speziallieferschein'];
    $this->lieferscheinbriefpapier=$result['lieferscheinbriefpapier'];
    $this->speziallieferscheinbeschriftung=$result['speziallieferscheinbeschriftung'];
    $this->firma=$result['firma'];
    $this->geloescht=$result['geloescht'];
    $this->logdatei=$result['logdatei'];
    $this->steuersatz_normal=$result['steuersatz_normal'];
    $this->steuersatz_zwischen=$result['steuersatz_zwischen'];
    $this->steuersatz_ermaessigt=$result['steuersatz_ermaessigt'];
    $this->steuersatz_starkermaessigt=$result['steuersatz_starkermaessigt'];
    $this->steuersatz_dienstleistung=$result['steuersatz_dienstleistung'];
    $this->waehrung=$result['waehrung'];
    $this->eigenesteuer=$result['eigenesteuer'];
    $this->druckerlogistikstufe1=$result['druckerlogistikstufe1'];
    $this->druckerlogistikstufe2=$result['druckerlogistikstufe2'];
    $this->selbstabholermail=$result['selbstabholermail'];
    $this->eanherstellerscan=$result['eanherstellerscan'];
    $this->reservierung=$result['reservierung'];
    $this->verkaufszahlendiagram=$result['verkaufszahlendiagram'];
    $this->oeffentlich=$result['oeffentlich'];
    $this->shopzwangsprojekt=$result['shopzwangsprojekt'];
    $this->kunde=$result['kunde'];
    $this->dpdkundennr=$result['dpdkundennr'];
    $this->dhlkundennr=$result['dhlkundennr'];
    $this->dhlformat=$result['dhlformat'];
    $this->dpdformat=$result['dpdformat'];
    $this->paketmarke_einzeldatei=$result['paketmarke_einzeldatei'];
    $this->dpdpfad=$result['dpdpfad'];
    $this->dhlpfad=$result['dhlpfad'];
    $this->upspfad=$result['upspfad'];
    $this->dhlintodb=$result['dhlintodb'];
    $this->intraship_enabled=$result['intraship_enabled'];
    $this->intraship_drucker=$result['intraship_drucker'];
    $this->intraship_testmode=$result['intraship_testmode'];
    $this->intraship_user=$result['intraship_user'];
    $this->intraship_signature=$result['intraship_signature'];
    $this->intraship_ekp=$result['intraship_ekp'];
    $this->intraship_api_user=$result['intraship_api_user'];
    $this->intraship_api_password=$result['intraship_api_password'];
    $this->intraship_company_name=$result['intraship_company_name'];
    $this->intraship_street_name=$result['intraship_street_name'];
    $this->intraship_street_number=$result['intraship_street_number'];
    $this->intraship_zip=$result['intraship_zip'];
    $this->intraship_country=$result['intraship_country'];
    $this->intraship_city=$result['intraship_city'];
    $this->intraship_email=$result['intraship_email'];
    $this->intraship_phone=$result['intraship_phone'];
    $this->intraship_internet=$result['intraship_internet'];
    $this->intraship_contact_person=$result['intraship_contact_person'];
    $this->intraship_account_owner=$result['intraship_account_owner'];
    $this->intraship_account_number=$result['intraship_account_number'];
    $this->intraship_bank_code=$result['intraship_bank_code'];
    $this->intraship_bank_name=$result['intraship_bank_name'];
    $this->intraship_iban=$result['intraship_iban'];
    $this->intraship_bic=$result['intraship_bic'];
    $this->intraship_WeightInKG=$result['intraship_WeightInKG'];
    $this->intraship_LengthInCM=$result['intraship_LengthInCM'];
    $this->intraship_WidthInCM=$result['intraship_WidthInCM'];
    $this->intraship_HeightInCM=$result['intraship_HeightInCM'];
    $this->intraship_PackageType=$result['intraship_PackageType'];
    $this->abrechnungsart=$result['abrechnungsart'];
    $this->kommissionierverfahren=$result['kommissionierverfahren'];
    $this->wechselaufeinstufig=$result['wechselaufeinstufig'];
    $this->projektuebergreifendkommisionieren=$result['projektuebergreifendkommisionieren'];
    $this->absendeadresse=$result['absendeadresse'];
    $this->absendename=$result['absendename'];
    $this->absendesignatur=$result['absendesignatur'];
    $this->autodruckrechnung=$result['autodruckrechnung'];
    $this->autodruckversandbestaetigung=$result['autodruckversandbestaetigung'];
    $this->automailversandbestaetigung=$result['automailversandbestaetigung'];
    $this->autodrucklieferschein=$result['autodrucklieferschein'];
    $this->automaillieferschein=$result['automaillieferschein'];
    $this->autodruckstorno=$result['autodruckstorno'];
    $this->autodruckanhang=$result['autodruckanhang'];
    $this->automailanhang=$result['automailanhang'];
    $this->autodruckerrechnung=$result['autodruckerrechnung'];
    $this->autodruckerlieferschein=$result['autodruckerlieferschein'];
    $this->autodruckeranhang=$result['autodruckeranhang'];
    $this->autodruckrechnungmenge=$result['autodruckrechnungmenge'];
    $this->autodrucklieferscheinmenge=$result['autodrucklieferscheinmenge'];
    $this->eigenernummernkreis=$result['eigenernummernkreis'];
    $this->next_angebot=$result['next_angebot'];
    $this->next_auftrag=$result['next_auftrag'];
    $this->next_rechnung=$result['next_rechnung'];
    $this->next_lieferschein=$result['next_lieferschein'];
    $this->next_arbeitsnachweis=$result['next_arbeitsnachweis'];
    $this->next_reisekosten=$result['next_reisekosten'];
    $this->next_bestellung=$result['next_bestellung'];
    $this->next_gutschrift=$result['next_gutschrift'];
    $this->next_kundennummer=$result['next_kundennummer'];
    $this->next_lieferantennummer=$result['next_lieferantennummer'];
    $this->next_mitarbeiternummer=$result['next_mitarbeiternummer'];
    $this->next_waren=$result['next_waren'];
    $this->next_produktion=$result['next_produktion'];
    $this->next_sonstiges=$result['next_sonstiges'];
    $this->next_anfrage=$result['next_anfrage'];
    $this->next_artikelnummer=$result['next_artikelnummer'];
    $this->gesamtstunden_max=$result['gesamtstunden_max'];
    $this->auftragid=$result['auftragid'];
    $this->dhlzahlungmandant=$result['dhlzahlungmandant'];
    $this->dhlretourenschein=$result['dhlretourenschein'];
    $this->land=$result['land'];
    $this->etiketten_positionen=$result['etiketten_positionen'];
    $this->etiketten_drucker=$result['etiketten_drucker'];
    $this->etiketten_art=$result['etiketten_art'];
    $this->seriennummernerfassen=$result['seriennummernerfassen'];
    $this->versandzweigeteilt=$result['versandzweigeteilt'];
    $this->nachnahmecheck=$result['nachnahmecheck'];
    $this->kasse_lieferschein_anlegen=$result['kasse_lieferschein_anlegen'];
    $this->kasse_lagerprozess=$result['kasse_lagerprozess'];
    $this->kasse_belegausgabe=$result['kasse_belegausgabe'];
    $this->kasse_preisgruppe=$result['kasse_preisgruppe'];
    $this->kasse_text_bemerkung=$result['kasse_text_bemerkung'];
    $this->kasse_text_freitext=$result['kasse_text_freitext'];
    $this->kasse_drucker=$result['kasse_drucker'];
    $this->kasse_lieferschein=$result['kasse_lieferschein'];
    $this->kasse_rechnung=$result['kasse_rechnung'];
    $this->kasse_lieferschein_doppel=$result['kasse_lieferschein_doppel'];
    $this->kasse_lager=$result['kasse_lager'];
    $this->kasse_konto=$result['kasse_konto'];
    $this->kasse_laufkundschaft=$result['kasse_laufkundschaft'];
    $this->kasse_rabatt_artikel=$result['kasse_rabatt_artikel'];
    $this->kasse_zahlung_bar=$result['kasse_zahlung_bar'];
    $this->kasse_zahlung_ec=$result['kasse_zahlung_ec'];
    $this->kasse_zahlung_kreditkarte=$result['kasse_zahlung_kreditkarte'];
    $this->kasse_zahlung_ueberweisung=$result['kasse_zahlung_ueberweisung'];
    $this->kasse_zahlung_paypal=$result['kasse_zahlung_paypal'];
    $this->kasse_extra_keinbeleg=$result['kasse_extra_keinbeleg'];
    $this->kasse_extra_rechnung=$result['kasse_extra_rechnung'];
    $this->kasse_extra_quittung=$result['kasse_extra_quittung'];
    $this->kasse_extra_gutschein=$result['kasse_extra_gutschein'];
    $this->kasse_extra_rabatt_prozent=$result['kasse_extra_rabatt_prozent'];
    $this->kasse_extra_rabatt_euro=$result['kasse_extra_rabatt_euro'];
    $this->kasse_adresse_erweitert=$result['kasse_adresse_erweitert'];
    $this->kasse_zahlungsauswahl_zwang=$result['kasse_zahlungsauswahl_zwang'];
    $this->kasse_button_entnahme=$result['kasse_button_entnahme'];
    $this->kasse_button_trinkgeld=$result['kasse_button_trinkgeld'];
    $this->kasse_vorauswahl_anrede=$result['kasse_vorauswahl_anrede'];
    $this->kasse_erweiterte_lagerabfrage=$result['kasse_erweiterte_lagerabfrage'];
    $this->filialadresse=$result['filialadresse'];
    $this->versandprojektfiliale=$result['versandprojektfiliale'];
    $this->differenz_auslieferung_tage=$result['differenz_auslieferung_tage'];
    $this->autostuecklistenanpassung=$result['autostuecklistenanpassung'];
    $this->dpdendung=$result['dpdendung'];
    $this->dhlendung=$result['dhlendung'];
    $this->tracking_substr_start=$result['tracking_substr_start'];
    $this->tracking_remove_kundennummer=$result['tracking_remove_kundennummer'];
    $this->tracking_substr_length=$result['tracking_substr_length'];
    $this->go_drucker=$result['go_drucker'];
    $this->go_apiurl_prefix=$result['go_apiurl_prefix'];
    $this->go_apiurl_postfix=$result['go_apiurl_postfix'];
    $this->go_apiurl_user=$result['go_apiurl_user'];
    $this->go_username=$result['go_username'];
    $this->go_password=$result['go_password'];
    $this->go_ax4nr=$result['go_ax4nr'];
    $this->go_name1=$result['go_name1'];
    $this->go_name2=$result['go_name2'];
    $this->go_abteilung=$result['go_abteilung'];
    $this->go_strasse1=$result['go_strasse1'];
    $this->go_strasse2=$result['go_strasse2'];
    $this->go_hausnummer=$result['go_hausnummer'];
    $this->go_plz=$result['go_plz'];
    $this->go_ort=$result['go_ort'];
    $this->go_land=$result['go_land'];
    $this->go_standardgewicht=$result['go_standardgewicht'];
    $this->go_format=$result['go_format'];
    $this->go_ausgabe=$result['go_ausgabe'];
    $this->intraship_exportgrund=$result['intraship_exportgrund'];
    $this->billsafe_merchantId=$result['billsafe_merchantId'];
    $this->billsafe_merchantLicenseSandbox=$result['billsafe_merchantLicenseSandbox'];
    $this->billsafe_merchantLicenseLive=$result['billsafe_merchantLicenseLive'];
    $this->billsafe_applicationSignature=$result['billsafe_applicationSignature'];
    $this->billsafe_applicationVersion=$result['billsafe_applicationVersion'];
    $this->secupay_apikey=$result['secupay_apikey'];
    $this->secupay_url=$result['secupay_url'];
    $this->secupay_demo=$result['secupay_demo'];
    $this->mahnwesen=$result['mahnwesen'];
    $this->status=$result['status'];
    $this->kasse_bondrucker=$result['kasse_bondrucker'];
    $this->kasse_bondrucker_aktiv=$result['kasse_bondrucker_aktiv'];
    $this->kasse_bondrucker_qrcode=$result['kasse_bondrucker_qrcode'];
    $this->kasse_bon_zeile1=$result['kasse_bon_zeile1'];
    $this->kasse_bon_zeile2=$result['kasse_bon_zeile2'];
    $this->kasse_bon_zeile3=$result['kasse_bon_zeile3'];
    $this->kasse_zahlung_bar_bezahlt=$result['kasse_zahlung_bar_bezahlt'];
    $this->kasse_zahlung_ec_bezahlt=$result['kasse_zahlung_ec_bezahlt'];
    $this->kasse_zahlung_kreditkarte_bezahlt=$result['kasse_zahlung_kreditkarte_bezahlt'];
    $this->kasse_zahlung_ueberweisung_bezahlt=$result['kasse_zahlung_ueberweisung_bezahlt'];
    $this->kasse_zahlung_paypal_bezahlt=$result['kasse_zahlung_paypal_bezahlt'];
    $this->kasse_quittung_rechnung=$result['kasse_quittung_rechnung'];
    $this->kasse_print_qr=$result['kasse_print_qr'];
    $this->kasse_button_einlage=$result['kasse_button_einlage'];
    $this->kasse_button_schublade=$result['kasse_button_schublade'];
    $this->produktionauftragautomatischfreigeben=$result['produktionauftragautomatischfreigeben'];
    $this->versandlagerplatzanzeigen=$result['versandlagerplatzanzeigen'];
    $this->versandartikelnameausstammdaten=$result['versandartikelnameausstammdaten'];
    $this->projektlager=$result['projektlager'];
    $this->tracing_substr_length=$result['tracing_substr_length'];
    $this->intraship_partnerid=$result['intraship_partnerid'];
    $this->intraship_retourenlabel=$result['intraship_retourenlabel'];
    $this->intraship_retourenaccount=$result['intraship_retourenaccount'];
    $this->absendegrussformel=$result['absendegrussformel'];
    $this->autodruckrechnungdoppel=$result['autodruckrechnungdoppel'];
    $this->intraship_partnerid_welt=$result['intraship_partnerid_welt'];
    $this->next_kalkulation=$result['next_kalkulation'];
    $this->next_preisanfrage=$result['next_preisanfrage'];
    $this->next_proformarechnung=$result['next_proformarechnung'];
    $this->next_verbindlichkeit=$result['next_verbindlichkeit'];
    $this->freifeld1=$result['freifeld1'];
    $this->freifeld2=$result['freifeld2'];
    $this->freifeld3=$result['freifeld3'];
    $this->freifeld4=$result['freifeld4'];
    $this->freifeld5=$result['freifeld5'];
    $this->freifeld6=$result['freifeld6'];
    $this->freifeld7=$result['freifeld7'];
    $this->freifeld8=$result['freifeld8'];
    $this->freifeld9=$result['freifeld9'];
    $this->freifeld10=$result['freifeld10'];
    $this->mahnwesen_abweichender_versender=$result['mahnwesen_abweichender_versender'];
    $this->lagerplatzlieferscheinausblenden=$result['lagerplatzlieferscheinausblenden'];
    $this->etiketten_sort=$result['etiketten_sort'];
    $this->eanherstellerscanerlauben=$result['eanherstellerscanerlauben'];
    $this->chargenerfassen=$result['chargenerfassen'];
    $this->mhderfassen=$result['mhderfassen'];
    $this->autodruckrechnungstufe1=$result['autodruckrechnungstufe1'];
    $this->autodruckrechnungstufe1menge=$result['autodruckrechnungstufe1menge'];
    $this->autodruckrechnungstufe1mail=$result['autodruckrechnungstufe1mail'];
    $this->autodruckkommissionierscheinstufe1=$result['autodruckkommissionierscheinstufe1'];
    $this->autodruckkommissionierscheinstufe1menge=$result['autodruckkommissionierscheinstufe1menge'];
    $this->kasse_bondrucker_freifeld=$result['kasse_bondrucker_freifeld'];
    $this->kasse_bondrucker_anzahl=$result['kasse_bondrucker_anzahl'];
    $this->kasse_rksv_aktiv=$result['kasse_rksv_aktiv'];
    $this->kasse_rksv_tool=$result['kasse_rksv_tool'];
    $this->kasse_rksv_kartenleser=$result['kasse_rksv_kartenleser'];
    $this->kasse_rksv_karteseriennummer=$result['kasse_rksv_karteseriennummer'];
    $this->kasse_rksv_kartepin=$result['kasse_rksv_kartepin'];
    $this->kasse_rksv_aeskey=$result['kasse_rksv_aeskey'];
    $this->kasse_rksv_publiczertifikat=$result['kasse_rksv_publiczertifikat'];
    $this->kasse_rksv_publiczertifikatkette=$result['kasse_rksv_publiczertifikatkette'];
    $this->kasse_rksv_kassenid=$result['kasse_rksv_kassenid'];
    $this->kasse_gutschrift=$result['kasse_gutschrift'];
    $this->rechnungerzeugen=$result['rechnungerzeugen'];
    $this->pos_artikeltexteuebernehmen=$result['pos_artikeltexteuebernehmen'];
    $this->pos_anzeigenetto=$result['pos_anzeigenetto'];
    $this->pos_zwischenspeichern=$result['pos_zwischenspeichern'];
    $this->kasse_button_belegladen=$result['kasse_button_belegladen'];
    $this->kasse_button_storno=$result['kasse_button_storno'];
    $this->pos_kundenalleprojekte=$result['pos_kundenalleprojekte'];
    $this->pos_artikelnurausprojekt=$result['pos_artikelnurausprojekt'];
    $this->allechargenmhd=$result['allechargenmhd'];
    $this->anzeigesteuerbelege=$result['anzeigesteuerbelege'];
    $this->pos_grosseansicht=$result['pos_grosseansicht'];
    $this->preisberechnung=$result['preisberechnung'];
    $this->steuernummer=$result['steuernummer'];
    $this->paketmarkeautodrucken=$result['paketmarkeautodrucken'];
    $this->orderpicking_sort=$result['orderpicking_sort'];
    $this->deactivateautoshipping=$result['deactivateautoshipping'];
    $this->pos_sumarticles=$result['pos_sumarticles'];
    $this->manualtracking=$result['manualtracking'];
    $this->zahlungsweise=$result['zahlungsweise'];
    $this->zahlungsweiselieferant=$result['zahlungsweiselieferant'];
    $this->versandart=$result['versandart'];
    $this->ups_api_user=$result['ups_api_user'];
    $this->ups_api_password=$result['ups_api_password'];
    $this->ups_api_key=$result['ups_api_key'];
    $this->ups_accountnumber=$result['ups_accountnumber'];
    $this->ups_company_name=$result['ups_company_name'];
    $this->ups_street_name=$result['ups_street_name'];
    $this->ups_street_number=$result['ups_street_number'];
    $this->ups_zip=$result['ups_zip'];
    $this->ups_country=$result['ups_country'];
    $this->ups_city=$result['ups_city'];
    $this->ups_email=$result['ups_email'];
    $this->ups_phone=$result['ups_phone'];
    $this->ups_internet=$result['ups_internet'];
    $this->ups_contact_person=$result['ups_contact_person'];
    $this->ups_WeightInKG=$result['ups_WeightInKG'];
    $this->ups_LengthInCM=$result['ups_LengthInCM'];
    $this->ups_WidthInCM=$result['ups_WidthInCM'];
    $this->ups_HeightInCM=$result['ups_HeightInCM'];
    $this->ups_drucker=$result['ups_drucker'];
    $this->ups_ausgabe=$result['ups_ausgabe'];
    $this->ups_package_code=$result['ups_package_code'];
    $this->ups_package_description=$result['ups_package_description'];
    $this->ups_service_code=$result['ups_service_code'];
    $this->ups_service_description=$result['ups_service_description'];
    $this->email_html_template=$result['email_html_template'];
    $this->druckanhang=$result['druckanhang'];
    $this->mailanhang=$result['mailanhang'];
    $this->next_retoure=$result['next_retoure'];
    $this->next_goodspostingdocument=$result['next_goodspostingdocument'];
    $this->pos_disable_single_entries=$result['pos_disable_single_entries'];
    $this->pos_disable_single_day=$result['pos_disable_single_day'];
    $this->pos_disable_counting_protocol=$result['pos_disable_counting_protocol'];
    $this->pos_disable_signature=$result['pos_disable_signature'];
    $this->steuer_erloese_inland_normal=$result['steuer_erloese_inland_normal'];
    $this->steuer_aufwendung_inland_normal=$result['steuer_aufwendung_inland_normal'];
    $this->steuer_erloese_inland_ermaessigt=$result['steuer_erloese_inland_ermaessigt'];
    $this->steuer_aufwendung_inland_ermaessigt=$result['steuer_aufwendung_inland_ermaessigt'];
    $this->steuer_erloese_inland_nichtsteuerbar=$result['steuer_erloese_inland_nichtsteuerbar'];
    $this->steuer_aufwendung_inland_nichtsteuerbar=$result['steuer_aufwendung_inland_nichtsteuerbar'];
    $this->steuer_erloese_inland_innergemeinschaftlich=$result['steuer_erloese_inland_innergemeinschaftlich'];
    $this->steuer_aufwendung_inland_innergemeinschaftlich=$result['steuer_aufwendung_inland_innergemeinschaftlich'];
    $this->steuer_erloese_inland_eunormal=$result['steuer_erloese_inland_eunormal'];
    $this->steuer_aufwendung_inland_eunormal=$result['steuer_aufwendung_inland_eunormal'];
    $this->steuer_erloese_inland_euermaessigt=$result['steuer_erloese_inland_euermaessigt'];
    $this->steuer_aufwendung_inland_euermaessigt=$result['steuer_aufwendung_inland_euermaessigt'];
    $this->steuer_erloese_inland_export=$result['steuer_erloese_inland_export'];
    $this->steuer_aufwendung_inland_import=$result['steuer_aufwendung_inland_import'];
    $this->create_proformainvoice=$result['create_proformainvoice'];
    $this->print_proformainvoice=$result['print_proformainvoice'];
    $this->proformainvoice_amount=$result['proformainvoice_amount'];
    $this->anzeigesteuerbelegebestellung=$result['anzeigesteuerbelegebestellung'];
    $this->autobestbeforebatch=$result['autobestbeforebatch'];
    $this->allwaysautobestbeforebatch=$result['allwaysautobestbeforebatch'];
    $this->kommissionierlauflieferschein=$result['kommissionierlauflieferschein'];
    $this->intraship_exportdrucker=$result['intraship_exportdrucker'];
    $this->multiorderpicking=$result['multiorderpicking'];
    $this->standardlager=$result['standardlager'];
    $this->standardlagerproduktion=$result['standardlagerproduktion'];
    $this->klarna_merchantid=$result['klarna_merchantid'];
    $this->klarna_sharedsecret=$result['klarna_sharedsecret'];
    $this->nurlagerartikel=$result['nurlagerartikel'];
    $this->paketmarkedrucken=$result['paketmarkedrucken'];
    $this->lieferscheinedrucken=$result['lieferscheinedrucken'];
    $this->lieferscheinedruckenmenge=$result['lieferscheinedruckenmenge'];
    $this->auftragdrucken=$result['auftragdrucken'];
    $this->auftragdruckenmenge=$result['auftragdruckenmenge'];
    $this->druckennachtracking=$result['druckennachtracking'];
    $this->exportdruckrechnungstufe1=$result['exportdruckrechnungstufe1'];
    $this->exportdruckrechnungstufe1menge=$result['exportdruckrechnungstufe1menge'];
    $this->exportdruckrechnung=$result['exportdruckrechnung'];
    $this->exportdruckrechnungmenge=$result['exportdruckrechnungmenge'];
    $this->kommissionierlistestufe1=$result['kommissionierlistestufe1'];
    $this->kommissionierlistestufe1menge=$result['kommissionierlistestufe1menge'];
    $this->fremdnummerscanerlauben=$result['fremdnummerscanerlauben'];
    $this->zvt100url=$result['zvt100url'];
    $this->zvt100port=$result['zvt100port'];
    $this->production_show_only_needed_storages=$result['production_show_only_needed_storages'];
    $this->produktion_extra_seiten=$result['produktion_extra_seiten'];
    $this->kasse_button_trinkgeldeckredit=$result['kasse_button_trinkgeldeckredit'];
    $this->kasse_autologout=$result['kasse_autologout'];
    $this->kasse_autologout_abschluss=$result['kasse_autologout_abschluss'];
    $this->next_receiptdocument=$result['next_receiptdocument'];
    $this->taxfromdoctypesettings=$result['taxfromdoctypesettings'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `projekt` (`id`,`name`,`abkuerzung`,`verantwortlicher`,`beschreibung`,`sonstiges`,`aktiv`,`farbe`,`autoversand`,`checkok`,`portocheck`,`automailrechnung`,`checkname`,`zahlungserinnerung`,`zahlungsmailbedinungen`,`folgebestaetigung`,`stornomail`,`kundenfreigabe_loeschen`,`autobestellung`,`speziallieferschein`,`lieferscheinbriefpapier`,`speziallieferscheinbeschriftung`,`firma`,`geloescht`,`logdatei`,`steuersatz_normal`,`steuersatz_zwischen`,`steuersatz_ermaessigt`,`steuersatz_starkermaessigt`,`steuersatz_dienstleistung`,`waehrung`,`eigenesteuer`,`druckerlogistikstufe1`,`druckerlogistikstufe2`,`selbstabholermail`,`eanherstellerscan`,`reservierung`,`verkaufszahlendiagram`,`oeffentlich`,`shopzwangsprojekt`,`kunde`,`dpdkundennr`,`dhlkundennr`,`dhlformat`,`dpdformat`,`paketmarke_einzeldatei`,`dpdpfad`,`dhlpfad`,`upspfad`,`dhlintodb`,`intraship_enabled`,`intraship_drucker`,`intraship_testmode`,`intraship_user`,`intraship_signature`,`intraship_ekp`,`intraship_api_user`,`intraship_api_password`,`intraship_company_name`,`intraship_street_name`,`intraship_street_number`,`intraship_zip`,`intraship_country`,`intraship_city`,`intraship_email`,`intraship_phone`,`intraship_internet`,`intraship_contact_person`,`intraship_account_owner`,`intraship_account_number`,`intraship_bank_code`,`intraship_bank_name`,`intraship_iban`,`intraship_bic`,`intraship_WeightInKG`,`intraship_LengthInCM`,`intraship_WidthInCM`,`intraship_HeightInCM`,`intraship_PackageType`,`abrechnungsart`,`kommissionierverfahren`,`wechselaufeinstufig`,`projektuebergreifendkommisionieren`,`absendeadresse`,`absendename`,`absendesignatur`,`autodruckrechnung`,`autodruckversandbestaetigung`,`automailversandbestaetigung`,`autodrucklieferschein`,`automaillieferschein`,`autodruckstorno`,`autodruckanhang`,`automailanhang`,`autodruckerrechnung`,`autodruckerlieferschein`,`autodruckeranhang`,`autodruckrechnungmenge`,`autodrucklieferscheinmenge`,`eigenernummernkreis`,`next_angebot`,`next_auftrag`,`next_rechnung`,`next_lieferschein`,`next_arbeitsnachweis`,`next_reisekosten`,`next_bestellung`,`next_gutschrift`,`next_kundennummer`,`next_lieferantennummer`,`next_mitarbeiternummer`,`next_waren`,`next_produktion`,`next_sonstiges`,`next_anfrage`,`next_artikelnummer`,`gesamtstunden_max`,`auftragid`,`dhlzahlungmandant`,`dhlretourenschein`,`land`,`etiketten_positionen`,`etiketten_drucker`,`etiketten_art`,`seriennummernerfassen`,`versandzweigeteilt`,`nachnahmecheck`,`kasse_lieferschein_anlegen`,`kasse_lagerprozess`,`kasse_belegausgabe`,`kasse_preisgruppe`,`kasse_text_bemerkung`,`kasse_text_freitext`,`kasse_drucker`,`kasse_lieferschein`,`kasse_rechnung`,`kasse_lieferschein_doppel`,`kasse_lager`,`kasse_konto`,`kasse_laufkundschaft`,`kasse_rabatt_artikel`,`kasse_zahlung_bar`,`kasse_zahlung_ec`,`kasse_zahlung_kreditkarte`,`kasse_zahlung_ueberweisung`,`kasse_zahlung_paypal`,`kasse_extra_keinbeleg`,`kasse_extra_rechnung`,`kasse_extra_quittung`,`kasse_extra_gutschein`,`kasse_extra_rabatt_prozent`,`kasse_extra_rabatt_euro`,`kasse_adresse_erweitert`,`kasse_zahlungsauswahl_zwang`,`kasse_button_entnahme`,`kasse_button_trinkgeld`,`kasse_vorauswahl_anrede`,`kasse_erweiterte_lagerabfrage`,`filialadresse`,`versandprojektfiliale`,`differenz_auslieferung_tage`,`autostuecklistenanpassung`,`dpdendung`,`dhlendung`,`tracking_substr_start`,`tracking_remove_kundennummer`,`tracking_substr_length`,`go_drucker`,`go_apiurl_prefix`,`go_apiurl_postfix`,`go_apiurl_user`,`go_username`,`go_password`,`go_ax4nr`,`go_name1`,`go_name2`,`go_abteilung`,`go_strasse1`,`go_strasse2`,`go_hausnummer`,`go_plz`,`go_ort`,`go_land`,`go_standardgewicht`,`go_format`,`go_ausgabe`,`intraship_exportgrund`,`billsafe_merchantId`,`billsafe_merchantLicenseSandbox`,`billsafe_merchantLicenseLive`,`billsafe_applicationSignature`,`billsafe_applicationVersion`,`secupay_apikey`,`secupay_url`,`secupay_demo`,`mahnwesen`,`status`,`kasse_bondrucker`,`kasse_bondrucker_aktiv`,`kasse_bondrucker_qrcode`,`kasse_bon_zeile1`,`kasse_bon_zeile2`,`kasse_bon_zeile3`,`kasse_zahlung_bar_bezahlt`,`kasse_zahlung_ec_bezahlt`,`kasse_zahlung_kreditkarte_bezahlt`,`kasse_zahlung_ueberweisung_bezahlt`,`kasse_zahlung_paypal_bezahlt`,`kasse_quittung_rechnung`,`kasse_print_qr`,`kasse_button_einlage`,`kasse_button_schublade`,`produktionauftragautomatischfreigeben`,`versandlagerplatzanzeigen`,`versandartikelnameausstammdaten`,`projektlager`,`tracing_substr_length`,`intraship_partnerid`,`intraship_retourenlabel`,`intraship_retourenaccount`,`absendegrussformel`,`autodruckrechnungdoppel`,`intraship_partnerid_welt`,`next_kalkulation`,`next_preisanfrage`,`next_proformarechnung`,`next_verbindlichkeit`,`freifeld1`,`freifeld2`,`freifeld3`,`freifeld4`,`freifeld5`,`freifeld6`,`freifeld7`,`freifeld8`,`freifeld9`,`freifeld10`,`mahnwesen_abweichender_versender`,`lagerplatzlieferscheinausblenden`,`etiketten_sort`,`eanherstellerscanerlauben`,`chargenerfassen`,`mhderfassen`,`autodruckrechnungstufe1`,`autodruckrechnungstufe1menge`,`autodruckrechnungstufe1mail`,`autodruckkommissionierscheinstufe1`,`autodruckkommissionierscheinstufe1menge`,`kasse_bondrucker_freifeld`,`kasse_bondrucker_anzahl`,`kasse_rksv_aktiv`,`kasse_rksv_tool`,`kasse_rksv_kartenleser`,`kasse_rksv_karteseriennummer`,`kasse_rksv_kartepin`,`kasse_rksv_aeskey`,`kasse_rksv_publiczertifikat`,`kasse_rksv_publiczertifikatkette`,`kasse_rksv_kassenid`,`kasse_gutschrift`,`rechnungerzeugen`,`pos_artikeltexteuebernehmen`,`pos_anzeigenetto`,`pos_zwischenspeichern`,`kasse_button_belegladen`,`kasse_button_storno`,`pos_kundenalleprojekte`,`pos_artikelnurausprojekt`,`allechargenmhd`,`anzeigesteuerbelege`,`pos_grosseansicht`,`preisberechnung`,`steuernummer`,`paketmarkeautodrucken`,`orderpicking_sort`,`deactivateautoshipping`,`pos_sumarticles`,`manualtracking`,`zahlungsweise`,`zahlungsweiselieferant`,`versandart`,`ups_api_user`,`ups_api_password`,`ups_api_key`,`ups_accountnumber`,`ups_company_name`,`ups_street_name`,`ups_street_number`,`ups_zip`,`ups_country`,`ups_city`,`ups_email`,`ups_phone`,`ups_internet`,`ups_contact_person`,`ups_WeightInKG`,`ups_LengthInCM`,`ups_WidthInCM`,`ups_HeightInCM`,`ups_drucker`,`ups_ausgabe`,`ups_package_code`,`ups_package_description`,`ups_service_code`,`ups_service_description`,`email_html_template`,`druckanhang`,`mailanhang`,`next_retoure`,`next_goodspostingdocument`,`pos_disable_single_entries`,`pos_disable_single_day`,`pos_disable_counting_protocol`,`pos_disable_signature`,`steuer_erloese_inland_normal`,`steuer_aufwendung_inland_normal`,`steuer_erloese_inland_ermaessigt`,`steuer_aufwendung_inland_ermaessigt`,`steuer_erloese_inland_nichtsteuerbar`,`steuer_aufwendung_inland_nichtsteuerbar`,`steuer_erloese_inland_innergemeinschaftlich`,`steuer_aufwendung_inland_innergemeinschaftlich`,`steuer_erloese_inland_eunormal`,`steuer_aufwendung_inland_eunormal`,`steuer_erloese_inland_euermaessigt`,`steuer_aufwendung_inland_euermaessigt`,`steuer_erloese_inland_export`,`steuer_aufwendung_inland_import`,`create_proformainvoice`,`print_proformainvoice`,`proformainvoice_amount`,`anzeigesteuerbelegebestellung`,`autobestbeforebatch`,`allwaysautobestbeforebatch`,`kommissionierlauflieferschein`,`intraship_exportdrucker`,`multiorderpicking`,`standardlager`,`standardlagerproduktion`,`klarna_merchantid`,`klarna_sharedsecret`,`nurlagerartikel`,`paketmarkedrucken`,`lieferscheinedrucken`,`lieferscheinedruckenmenge`,`auftragdrucken`,`auftragdruckenmenge`,`druckennachtracking`,`exportdruckrechnungstufe1`,`exportdruckrechnungstufe1menge`,`exportdruckrechnung`,`exportdruckrechnungmenge`,`kommissionierlistestufe1`,`kommissionierlistestufe1menge`,`fremdnummerscanerlauben`,`zvt100url`,`zvt100port`,`production_show_only_needed_storages`,`produktion_extra_seiten`,`kasse_button_trinkgeldeckredit`,`kasse_autologout`,`kasse_autologout_abschluss`,`next_receiptdocument`,`taxfromdoctypesettings`)
      VALUES(NULL,'{$this->name}','{$this->abkuerzung}','{$this->verantwortlicher}','{$this->beschreibung}','{$this->sonstiges}','{$this->aktiv}','{$this->farbe}','{$this->autoversand}','{$this->checkok}','{$this->portocheck}','{$this->automailrechnung}','{$this->checkname}','{$this->zahlungserinnerung}','{$this->zahlungsmailbedinungen}','{$this->folgebestaetigung}','{$this->stornomail}','{$this->kundenfreigabe_loeschen}','{$this->autobestellung}','{$this->speziallieferschein}','{$this->lieferscheinbriefpapier}','{$this->speziallieferscheinbeschriftung}','{$this->firma}','{$this->geloescht}','{$this->logdatei}','{$this->steuersatz_normal}','{$this->steuersatz_zwischen}','{$this->steuersatz_ermaessigt}','{$this->steuersatz_starkermaessigt}','{$this->steuersatz_dienstleistung}','{$this->waehrung}','{$this->eigenesteuer}','{$this->druckerlogistikstufe1}','{$this->druckerlogistikstufe2}','{$this->selbstabholermail}','{$this->eanherstellerscan}','{$this->reservierung}','{$this->verkaufszahlendiagram}','{$this->oeffentlich}','{$this->shopzwangsprojekt}','{$this->kunde}','{$this->dpdkundennr}','{$this->dhlkundennr}','{$this->dhlformat}','{$this->dpdformat}','{$this->paketmarke_einzeldatei}','{$this->dpdpfad}','{$this->dhlpfad}','{$this->upspfad}','{$this->dhlintodb}','{$this->intraship_enabled}','{$this->intraship_drucker}','{$this->intraship_testmode}','{$this->intraship_user}','{$this->intraship_signature}','{$this->intraship_ekp}','{$this->intraship_api_user}','{$this->intraship_api_password}','{$this->intraship_company_name}','{$this->intraship_street_name}','{$this->intraship_street_number}','{$this->intraship_zip}','{$this->intraship_country}','{$this->intraship_city}','{$this->intraship_email}','{$this->intraship_phone}','{$this->intraship_internet}','{$this->intraship_contact_person}','{$this->intraship_account_owner}','{$this->intraship_account_number}','{$this->intraship_bank_code}','{$this->intraship_bank_name}','{$this->intraship_iban}','{$this->intraship_bic}','{$this->intraship_WeightInKG}','{$this->intraship_LengthInCM}','{$this->intraship_WidthInCM}','{$this->intraship_HeightInCM}','{$this->intraship_PackageType}','{$this->abrechnungsart}','{$this->kommissionierverfahren}','{$this->wechselaufeinstufig}','{$this->projektuebergreifendkommisionieren}','{$this->absendeadresse}','{$this->absendename}','{$this->absendesignatur}','{$this->autodruckrechnung}','{$this->autodruckversandbestaetigung}','{$this->automailversandbestaetigung}','{$this->autodrucklieferschein}','{$this->automaillieferschein}','{$this->autodruckstorno}','{$this->autodruckanhang}','{$this->automailanhang}','{$this->autodruckerrechnung}','{$this->autodruckerlieferschein}','{$this->autodruckeranhang}','{$this->autodruckrechnungmenge}','{$this->autodrucklieferscheinmenge}','{$this->eigenernummernkreis}','{$this->next_angebot}','{$this->next_auftrag}','{$this->next_rechnung}','{$this->next_lieferschein}','{$this->next_arbeitsnachweis}','{$this->next_reisekosten}','{$this->next_bestellung}','{$this->next_gutschrift}','{$this->next_kundennummer}','{$this->next_lieferantennummer}','{$this->next_mitarbeiternummer}','{$this->next_waren}','{$this->next_produktion}','{$this->next_sonstiges}','{$this->next_anfrage}','{$this->next_artikelnummer}','{$this->gesamtstunden_max}','{$this->auftragid}','{$this->dhlzahlungmandant}','{$this->dhlretourenschein}','{$this->land}','{$this->etiketten_positionen}','{$this->etiketten_drucker}','{$this->etiketten_art}','{$this->seriennummernerfassen}','{$this->versandzweigeteilt}','{$this->nachnahmecheck}','{$this->kasse_lieferschein_anlegen}','{$this->kasse_lagerprozess}','{$this->kasse_belegausgabe}','{$this->kasse_preisgruppe}','{$this->kasse_text_bemerkung}','{$this->kasse_text_freitext}','{$this->kasse_drucker}','{$this->kasse_lieferschein}','{$this->kasse_rechnung}','{$this->kasse_lieferschein_doppel}','{$this->kasse_lager}','{$this->kasse_konto}','{$this->kasse_laufkundschaft}','{$this->kasse_rabatt_artikel}','{$this->kasse_zahlung_bar}','{$this->kasse_zahlung_ec}','{$this->kasse_zahlung_kreditkarte}','{$this->kasse_zahlung_ueberweisung}','{$this->kasse_zahlung_paypal}','{$this->kasse_extra_keinbeleg}','{$this->kasse_extra_rechnung}','{$this->kasse_extra_quittung}','{$this->kasse_extra_gutschein}','{$this->kasse_extra_rabatt_prozent}','{$this->kasse_extra_rabatt_euro}','{$this->kasse_adresse_erweitert}','{$this->kasse_zahlungsauswahl_zwang}','{$this->kasse_button_entnahme}','{$this->kasse_button_trinkgeld}','{$this->kasse_vorauswahl_anrede}','{$this->kasse_erweiterte_lagerabfrage}','{$this->filialadresse}','{$this->versandprojektfiliale}','{$this->differenz_auslieferung_tage}','{$this->autostuecklistenanpassung}','{$this->dpdendung}','{$this->dhlendung}','{$this->tracking_substr_start}','{$this->tracking_remove_kundennummer}','{$this->tracking_substr_length}','{$this->go_drucker}','{$this->go_apiurl_prefix}','{$this->go_apiurl_postfix}','{$this->go_apiurl_user}','{$this->go_username}','{$this->go_password}','{$this->go_ax4nr}','{$this->go_name1}','{$this->go_name2}','{$this->go_abteilung}','{$this->go_strasse1}','{$this->go_strasse2}','{$this->go_hausnummer}','{$this->go_plz}','{$this->go_ort}','{$this->go_land}','{$this->go_standardgewicht}','{$this->go_format}','{$this->go_ausgabe}','{$this->intraship_exportgrund}','{$this->billsafe_merchantId}','{$this->billsafe_merchantLicenseSandbox}','{$this->billsafe_merchantLicenseLive}','{$this->billsafe_applicationSignature}','{$this->billsafe_applicationVersion}','{$this->secupay_apikey}','{$this->secupay_url}','{$this->secupay_demo}','{$this->mahnwesen}','{$this->status}','{$this->kasse_bondrucker}','{$this->kasse_bondrucker_aktiv}','{$this->kasse_bondrucker_qrcode}','{$this->kasse_bon_zeile1}','{$this->kasse_bon_zeile2}','{$this->kasse_bon_zeile3}','{$this->kasse_zahlung_bar_bezahlt}','{$this->kasse_zahlung_ec_bezahlt}','{$this->kasse_zahlung_kreditkarte_bezahlt}','{$this->kasse_zahlung_ueberweisung_bezahlt}','{$this->kasse_zahlung_paypal_bezahlt}','{$this->kasse_quittung_rechnung}','{$this->kasse_print_qr}','{$this->kasse_button_einlage}','{$this->kasse_button_schublade}','{$this->produktionauftragautomatischfreigeben}','{$this->versandlagerplatzanzeigen}','{$this->versandartikelnameausstammdaten}','{$this->projektlager}','{$this->tracing_substr_length}','{$this->intraship_partnerid}','{$this->intraship_retourenlabel}','{$this->intraship_retourenaccount}','{$this->absendegrussformel}','{$this->autodruckrechnungdoppel}','{$this->intraship_partnerid_welt}','{$this->next_kalkulation}','{$this->next_preisanfrage}','{$this->next_proformarechnung}','{$this->next_verbindlichkeit}','{$this->freifeld1}','{$this->freifeld2}','{$this->freifeld3}','{$this->freifeld4}','{$this->freifeld5}','{$this->freifeld6}','{$this->freifeld7}','{$this->freifeld8}','{$this->freifeld9}','{$this->freifeld10}','{$this->mahnwesen_abweichender_versender}','{$this->lagerplatzlieferscheinausblenden}','{$this->etiketten_sort}','{$this->eanherstellerscanerlauben}','{$this->chargenerfassen}','{$this->mhderfassen}','{$this->autodruckrechnungstufe1}','{$this->autodruckrechnungstufe1menge}','{$this->autodruckrechnungstufe1mail}','{$this->autodruckkommissionierscheinstufe1}','{$this->autodruckkommissionierscheinstufe1menge}','{$this->kasse_bondrucker_freifeld}','{$this->kasse_bondrucker_anzahl}','{$this->kasse_rksv_aktiv}','{$this->kasse_rksv_tool}','{$this->kasse_rksv_kartenleser}','{$this->kasse_rksv_karteseriennummer}','{$this->kasse_rksv_kartepin}','{$this->kasse_rksv_aeskey}','{$this->kasse_rksv_publiczertifikat}','{$this->kasse_rksv_publiczertifikatkette}','{$this->kasse_rksv_kassenid}','{$this->kasse_gutschrift}','{$this->rechnungerzeugen}','{$this->pos_artikeltexteuebernehmen}','{$this->pos_anzeigenetto}','{$this->pos_zwischenspeichern}','{$this->kasse_button_belegladen}','{$this->kasse_button_storno}','{$this->pos_kundenalleprojekte}','{$this->pos_artikelnurausprojekt}','{$this->allechargenmhd}','{$this->anzeigesteuerbelege}','{$this->pos_grosseansicht}','{$this->preisberechnung}','{$this->steuernummer}','{$this->paketmarkeautodrucken}','{$this->orderpicking_sort}','{$this->deactivateautoshipping}','{$this->pos_sumarticles}','{$this->manualtracking}','{$this->zahlungsweise}','{$this->zahlungsweiselieferant}','{$this->versandart}','{$this->ups_api_user}','{$this->ups_api_password}','{$this->ups_api_key}','{$this->ups_accountnumber}','{$this->ups_company_name}','{$this->ups_street_name}','{$this->ups_street_number}','{$this->ups_zip}','{$this->ups_country}','{$this->ups_city}','{$this->ups_email}','{$this->ups_phone}','{$this->ups_internet}','{$this->ups_contact_person}','{$this->ups_WeightInKG}','{$this->ups_LengthInCM}','{$this->ups_WidthInCM}','{$this->ups_HeightInCM}','{$this->ups_drucker}','{$this->ups_ausgabe}','{$this->ups_package_code}','{$this->ups_package_description}','{$this->ups_service_code}','{$this->ups_service_description}','{$this->email_html_template}','{$this->druckanhang}','{$this->mailanhang}','{$this->next_retoure}','{$this->next_goodspostingdocument}','{$this->pos_disable_single_entries}','{$this->pos_disable_single_day}','{$this->pos_disable_counting_protocol}','{$this->pos_disable_signature}','{$this->steuer_erloese_inland_normal}','{$this->steuer_aufwendung_inland_normal}','{$this->steuer_erloese_inland_ermaessigt}','{$this->steuer_aufwendung_inland_ermaessigt}','{$this->steuer_erloese_inland_nichtsteuerbar}','{$this->steuer_aufwendung_inland_nichtsteuerbar}','{$this->steuer_erloese_inland_innergemeinschaftlich}','{$this->steuer_aufwendung_inland_innergemeinschaftlich}','{$this->steuer_erloese_inland_eunormal}','{$this->steuer_aufwendung_inland_eunormal}','{$this->steuer_erloese_inland_euermaessigt}','{$this->steuer_aufwendung_inland_euermaessigt}','{$this->steuer_erloese_inland_export}','{$this->steuer_aufwendung_inland_import}','{$this->create_proformainvoice}','{$this->print_proformainvoice}','{$this->proformainvoice_amount}','{$this->anzeigesteuerbelegebestellung}','{$this->autobestbeforebatch}','{$this->allwaysautobestbeforebatch}','{$this->kommissionierlauflieferschein}','{$this->intraship_exportdrucker}','{$this->multiorderpicking}','{$this->standardlager}','{$this->standardlagerproduktion}','{$this->klarna_merchantid}','{$this->klarna_sharedsecret}','{$this->nurlagerartikel}','{$this->paketmarkedrucken}','{$this->lieferscheinedrucken}','{$this->lieferscheinedruckenmenge}','{$this->auftragdrucken}','{$this->auftragdruckenmenge}','{$this->druckennachtracking}','{$this->exportdruckrechnungstufe1}','{$this->exportdruckrechnungstufe1menge}','{$this->exportdruckrechnung}','{$this->exportdruckrechnungmenge}','{$this->kommissionierlistestufe1}','{$this->kommissionierlistestufe1menge}','{$this->fremdnummerscanerlauben}','{$this->zvt100url}','{$this->zvt100port}','{$this->production_show_only_needed_storages}','{$this->produktion_extra_seiten}','{$this->kasse_button_trinkgeldeckredit}','{$this->kasse_autologout}','{$this->kasse_autologout_abschluss}','{$this->next_receiptdocument}','{$this->taxfromdoctypesettings}')";

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `projekt` SET
      `name`='{$this->name}',
      `abkuerzung`='{$this->abkuerzung}',
      `verantwortlicher`='{$this->verantwortlicher}',
      `beschreibung`='{$this->beschreibung}',
      `sonstiges`='{$this->sonstiges}',
      `aktiv`='{$this->aktiv}',
      `farbe`='{$this->farbe}',
      `autoversand`='{$this->autoversand}',
      `checkok`='{$this->checkok}',
      `portocheck`='{$this->portocheck}',
      `automailrechnung`='{$this->automailrechnung}',
      `checkname`='{$this->checkname}',
      `zahlungserinnerung`='{$this->zahlungserinnerung}',
      `zahlungsmailbedinungen`='{$this->zahlungsmailbedinungen}',
      `folgebestaetigung`='{$this->folgebestaetigung}',
      `stornomail`='{$this->stornomail}',
      `kundenfreigabe_loeschen`='{$this->kundenfreigabe_loeschen}',
      `autobestellung`='{$this->autobestellung}',
      `speziallieferschein`='{$this->speziallieferschein}',
      `lieferscheinbriefpapier`='{$this->lieferscheinbriefpapier}',
      `speziallieferscheinbeschriftung`='{$this->speziallieferscheinbeschriftung}',
      `firma`='{$this->firma}',
      `geloescht`='{$this->geloescht}',
      `logdatei`='{$this->logdatei}',
      `steuersatz_normal`='{$this->steuersatz_normal}',
      `steuersatz_zwischen`='{$this->steuersatz_zwischen}',
      `steuersatz_ermaessigt`='{$this->steuersatz_ermaessigt}',
      `steuersatz_starkermaessigt`='{$this->steuersatz_starkermaessigt}',
      `steuersatz_dienstleistung`='{$this->steuersatz_dienstleistung}',
      `waehrung`='{$this->waehrung}',
      `eigenesteuer`='{$this->eigenesteuer}',
      `druckerlogistikstufe1`='{$this->druckerlogistikstufe1}',
      `druckerlogistikstufe2`='{$this->druckerlogistikstufe2}',
      `selbstabholermail`='{$this->selbstabholermail}',
      `eanherstellerscan`='{$this->eanherstellerscan}',
      `reservierung`='{$this->reservierung}',
      `verkaufszahlendiagram`='{$this->verkaufszahlendiagram}',
      `oeffentlich`='{$this->oeffentlich}',
      `shopzwangsprojekt`='{$this->shopzwangsprojekt}',
      `kunde`='{$this->kunde}',
      `dpdkundennr`='{$this->dpdkundennr}',
      `dhlkundennr`='{$this->dhlkundennr}',
      `dhlformat`='{$this->dhlformat}',
      `dpdformat`='{$this->dpdformat}',
      `paketmarke_einzeldatei`='{$this->paketmarke_einzeldatei}',
      `dpdpfad`='{$this->dpdpfad}',
      `dhlpfad`='{$this->dhlpfad}',
      `upspfad`='{$this->upspfad}',
      `dhlintodb`='{$this->dhlintodb}',
      `intraship_enabled`='{$this->intraship_enabled}',
      `intraship_drucker`='{$this->intraship_drucker}',
      `intraship_testmode`='{$this->intraship_testmode}',
      `intraship_user`='{$this->intraship_user}',
      `intraship_signature`='{$this->intraship_signature}',
      `intraship_ekp`='{$this->intraship_ekp}',
      `intraship_api_user`='{$this->intraship_api_user}',
      `intraship_api_password`='{$this->intraship_api_password}',
      `intraship_company_name`='{$this->intraship_company_name}',
      `intraship_street_name`='{$this->intraship_street_name}',
      `intraship_street_number`='{$this->intraship_street_number}',
      `intraship_zip`='{$this->intraship_zip}',
      `intraship_country`='{$this->intraship_country}',
      `intraship_city`='{$this->intraship_city}',
      `intraship_email`='{$this->intraship_email}',
      `intraship_phone`='{$this->intraship_phone}',
      `intraship_internet`='{$this->intraship_internet}',
      `intraship_contact_person`='{$this->intraship_contact_person}',
      `intraship_account_owner`='{$this->intraship_account_owner}',
      `intraship_account_number`='{$this->intraship_account_number}',
      `intraship_bank_code`='{$this->intraship_bank_code}',
      `intraship_bank_name`='{$this->intraship_bank_name}',
      `intraship_iban`='{$this->intraship_iban}',
      `intraship_bic`='{$this->intraship_bic}',
      `intraship_WeightInKG`='{$this->intraship_WeightInKG}',
      `intraship_LengthInCM`='{$this->intraship_LengthInCM}',
      `intraship_WidthInCM`='{$this->intraship_WidthInCM}',
      `intraship_HeightInCM`='{$this->intraship_HeightInCM}',
      `intraship_PackageType`='{$this->intraship_PackageType}',
      `abrechnungsart`='{$this->abrechnungsart}',
      `kommissionierverfahren`='{$this->kommissionierverfahren}',
      `wechselaufeinstufig`='{$this->wechselaufeinstufig}',
      `projektuebergreifendkommisionieren`='{$this->projektuebergreifendkommisionieren}',
      `absendeadresse`='{$this->absendeadresse}',
      `absendename`='{$this->absendename}',
      `absendesignatur`='{$this->absendesignatur}',
      `autodruckrechnung`='{$this->autodruckrechnung}',
      `autodruckversandbestaetigung`='{$this->autodruckversandbestaetigung}',
      `automailversandbestaetigung`='{$this->automailversandbestaetigung}',
      `autodrucklieferschein`='{$this->autodrucklieferschein}',
      `automaillieferschein`='{$this->automaillieferschein}',
      `autodruckstorno`='{$this->autodruckstorno}',
      `autodruckanhang`='{$this->autodruckanhang}',
      `automailanhang`='{$this->automailanhang}',
      `autodruckerrechnung`='{$this->autodruckerrechnung}',
      `autodruckerlieferschein`='{$this->autodruckerlieferschein}',
      `autodruckeranhang`='{$this->autodruckeranhang}',
      `autodruckrechnungmenge`='{$this->autodruckrechnungmenge}',
      `autodrucklieferscheinmenge`='{$this->autodrucklieferscheinmenge}',
      `eigenernummernkreis`='{$this->eigenernummernkreis}',
      `next_angebot`='{$this->next_angebot}',
      `next_auftrag`='{$this->next_auftrag}',
      `next_rechnung`='{$this->next_rechnung}',
      `next_lieferschein`='{$this->next_lieferschein}',
      `next_arbeitsnachweis`='{$this->next_arbeitsnachweis}',
      `next_reisekosten`='{$this->next_reisekosten}',
      `next_bestellung`='{$this->next_bestellung}',
      `next_gutschrift`='{$this->next_gutschrift}',
      `next_kundennummer`='{$this->next_kundennummer}',
      `next_lieferantennummer`='{$this->next_lieferantennummer}',
      `next_mitarbeiternummer`='{$this->next_mitarbeiternummer}',
      `next_waren`='{$this->next_waren}',
      `next_produktion`='{$this->next_produktion}',
      `next_sonstiges`='{$this->next_sonstiges}',
      `next_anfrage`='{$this->next_anfrage}',
      `next_artikelnummer`='{$this->next_artikelnummer}',
      `gesamtstunden_max`='{$this->gesamtstunden_max}',
      `auftragid`='{$this->auftragid}',
      `dhlzahlungmandant`='{$this->dhlzahlungmandant}',
      `dhlretourenschein`='{$this->dhlretourenschein}',
      `land`='{$this->land}',
      `etiketten_positionen`='{$this->etiketten_positionen}',
      `etiketten_drucker`='{$this->etiketten_drucker}',
      `etiketten_art`='{$this->etiketten_art}',
      `seriennummernerfassen`='{$this->seriennummernerfassen}',
      `versandzweigeteilt`='{$this->versandzweigeteilt}',
      `nachnahmecheck`='{$this->nachnahmecheck}',
      `kasse_lieferschein_anlegen`='{$this->kasse_lieferschein_anlegen}',
      `kasse_lagerprozess`='{$this->kasse_lagerprozess}',
      `kasse_belegausgabe`='{$this->kasse_belegausgabe}',
      `kasse_preisgruppe`='{$this->kasse_preisgruppe}',
      `kasse_text_bemerkung`='{$this->kasse_text_bemerkung}',
      `kasse_text_freitext`='{$this->kasse_text_freitext}',
      `kasse_drucker`='{$this->kasse_drucker}',
      `kasse_lieferschein`='{$this->kasse_lieferschein}',
      `kasse_rechnung`='{$this->kasse_rechnung}',
      `kasse_lieferschein_doppel`='{$this->kasse_lieferschein_doppel}',
      `kasse_lager`='{$this->kasse_lager}',
      `kasse_konto`='{$this->kasse_konto}',
      `kasse_laufkundschaft`='{$this->kasse_laufkundschaft}',
      `kasse_rabatt_artikel`='{$this->kasse_rabatt_artikel}',
      `kasse_zahlung_bar`='{$this->kasse_zahlung_bar}',
      `kasse_zahlung_ec`='{$this->kasse_zahlung_ec}',
      `kasse_zahlung_kreditkarte`='{$this->kasse_zahlung_kreditkarte}',
      `kasse_zahlung_ueberweisung`='{$this->kasse_zahlung_ueberweisung}',
      `kasse_zahlung_paypal`='{$this->kasse_zahlung_paypal}',
      `kasse_extra_keinbeleg`='{$this->kasse_extra_keinbeleg}',
      `kasse_extra_rechnung`='{$this->kasse_extra_rechnung}',
      `kasse_extra_quittung`='{$this->kasse_extra_quittung}',
      `kasse_extra_gutschein`='{$this->kasse_extra_gutschein}',
      `kasse_extra_rabatt_prozent`='{$this->kasse_extra_rabatt_prozent}',
      `kasse_extra_rabatt_euro`='{$this->kasse_extra_rabatt_euro}',
      `kasse_adresse_erweitert`='{$this->kasse_adresse_erweitert}',
      `kasse_zahlungsauswahl_zwang`='{$this->kasse_zahlungsauswahl_zwang}',
      `kasse_button_entnahme`='{$this->kasse_button_entnahme}',
      `kasse_button_trinkgeld`='{$this->kasse_button_trinkgeld}',
      `kasse_vorauswahl_anrede`='{$this->kasse_vorauswahl_anrede}',
      `kasse_erweiterte_lagerabfrage`='{$this->kasse_erweiterte_lagerabfrage}',
      `filialadresse`='{$this->filialadresse}',
      `versandprojektfiliale`='{$this->versandprojektfiliale}',
      `differenz_auslieferung_tage`='{$this->differenz_auslieferung_tage}',
      `autostuecklistenanpassung`='{$this->autostuecklistenanpassung}',
      `dpdendung`='{$this->dpdendung}',
      `dhlendung`='{$this->dhlendung}',
      `tracking_substr_start`='{$this->tracking_substr_start}',
      `tracking_remove_kundennummer`='{$this->tracking_remove_kundennummer}',
      `tracking_substr_length`='{$this->tracking_substr_length}',
      `go_drucker`='{$this->go_drucker}',
      `go_apiurl_prefix`='{$this->go_apiurl_prefix}',
      `go_apiurl_postfix`='{$this->go_apiurl_postfix}',
      `go_apiurl_user`='{$this->go_apiurl_user}',
      `go_username`='{$this->go_username}',
      `go_password`='{$this->go_password}',
      `go_ax4nr`='{$this->go_ax4nr}',
      `go_name1`='{$this->go_name1}',
      `go_name2`='{$this->go_name2}',
      `go_abteilung`='{$this->go_abteilung}',
      `go_strasse1`='{$this->go_strasse1}',
      `go_strasse2`='{$this->go_strasse2}',
      `go_hausnummer`='{$this->go_hausnummer}',
      `go_plz`='{$this->go_plz}',
      `go_ort`='{$this->go_ort}',
      `go_land`='{$this->go_land}',
      `go_standardgewicht`='{$this->go_standardgewicht}',
      `go_format`='{$this->go_format}',
      `go_ausgabe`='{$this->go_ausgabe}',
      `intraship_exportgrund`='{$this->intraship_exportgrund}',
      `billsafe_merchantId`='{$this->billsafe_merchantId}',
      `billsafe_merchantLicenseSandbox`='{$this->billsafe_merchantLicenseSandbox}',
      `billsafe_merchantLicenseLive`='{$this->billsafe_merchantLicenseLive}',
      `billsafe_applicationSignature`='{$this->billsafe_applicationSignature}',
      `billsafe_applicationVersion`='{$this->billsafe_applicationVersion}',
      `secupay_apikey`='{$this->secupay_apikey}',
      `secupay_url`='{$this->secupay_url}',
      `secupay_demo`='{$this->secupay_demo}',
      `mahnwesen`='{$this->mahnwesen}',
      `status`='{$this->status}',
      `kasse_bondrucker`='{$this->kasse_bondrucker}',
      `kasse_bondrucker_aktiv`='{$this->kasse_bondrucker_aktiv}',
      `kasse_bondrucker_qrcode`='{$this->kasse_bondrucker_qrcode}',
      `kasse_bon_zeile1`='{$this->kasse_bon_zeile1}',
      `kasse_bon_zeile2`='{$this->kasse_bon_zeile2}',
      `kasse_bon_zeile3`='{$this->kasse_bon_zeile3}',
      `kasse_zahlung_bar_bezahlt`='{$this->kasse_zahlung_bar_bezahlt}',
      `kasse_zahlung_ec_bezahlt`='{$this->kasse_zahlung_ec_bezahlt}',
      `kasse_zahlung_kreditkarte_bezahlt`='{$this->kasse_zahlung_kreditkarte_bezahlt}',
      `kasse_zahlung_ueberweisung_bezahlt`='{$this->kasse_zahlung_ueberweisung_bezahlt}',
      `kasse_zahlung_paypal_bezahlt`='{$this->kasse_zahlung_paypal_bezahlt}',
      `kasse_quittung_rechnung`='{$this->kasse_quittung_rechnung}',
      `kasse_print_qr`='{$this->kasse_print_qr}',
      `kasse_button_einlage`='{$this->kasse_button_einlage}',
      `kasse_button_schublade`='{$this->kasse_button_schublade}',
      `produktionauftragautomatischfreigeben`='{$this->produktionauftragautomatischfreigeben}',
      `versandlagerplatzanzeigen`='{$this->versandlagerplatzanzeigen}',
      `versandartikelnameausstammdaten`='{$this->versandartikelnameausstammdaten}',
      `projektlager`='{$this->projektlager}',
      `tracing_substr_length`='{$this->tracing_substr_length}',
      `intraship_partnerid`='{$this->intraship_partnerid}',
      `intraship_retourenlabel`='{$this->intraship_retourenlabel}',
      `intraship_retourenaccount`='{$this->intraship_retourenaccount}',
      `absendegrussformel`='{$this->absendegrussformel}',
      `autodruckrechnungdoppel`='{$this->autodruckrechnungdoppel}',
      `intraship_partnerid_welt`='{$this->intraship_partnerid_welt}',
      `next_kalkulation`='{$this->next_kalkulation}',
      `next_preisanfrage`='{$this->next_preisanfrage}',
      `next_proformarechnung`='{$this->next_proformarechnung}',
      `next_verbindlichkeit`='{$this->next_verbindlichkeit}',
      `freifeld1`='{$this->freifeld1}',
      `freifeld2`='{$this->freifeld2}',
      `freifeld3`='{$this->freifeld3}',
      `freifeld4`='{$this->freifeld4}',
      `freifeld5`='{$this->freifeld5}',
      `freifeld6`='{$this->freifeld6}',
      `freifeld7`='{$this->freifeld7}',
      `freifeld8`='{$this->freifeld8}',
      `freifeld9`='{$this->freifeld9}',
      `freifeld10`='{$this->freifeld10}',
      `mahnwesen_abweichender_versender`='{$this->mahnwesen_abweichender_versender}',
      `lagerplatzlieferscheinausblenden`='{$this->lagerplatzlieferscheinausblenden}',
      `etiketten_sort`='{$this->etiketten_sort}',
      `eanherstellerscanerlauben`='{$this->eanherstellerscanerlauben}',
      `chargenerfassen`='{$this->chargenerfassen}',
      `mhderfassen`='{$this->mhderfassen}',
      `autodruckrechnungstufe1`='{$this->autodruckrechnungstufe1}',
      `autodruckrechnungstufe1menge`='{$this->autodruckrechnungstufe1menge}',
      `autodruckrechnungstufe1mail`='{$this->autodruckrechnungstufe1mail}',
      `autodruckkommissionierscheinstufe1`='{$this->autodruckkommissionierscheinstufe1}',
      `autodruckkommissionierscheinstufe1menge`='{$this->autodruckkommissionierscheinstufe1menge}',
      `kasse_bondrucker_freifeld`='{$this->kasse_bondrucker_freifeld}',
      `kasse_bondrucker_anzahl`='{$this->kasse_bondrucker_anzahl}',
      `kasse_rksv_aktiv`='{$this->kasse_rksv_aktiv}',
      `kasse_rksv_tool`='{$this->kasse_rksv_tool}',
      `kasse_rksv_kartenleser`='{$this->kasse_rksv_kartenleser}',
      `kasse_rksv_karteseriennummer`='{$this->kasse_rksv_karteseriennummer}',
      `kasse_rksv_kartepin`='{$this->kasse_rksv_kartepin}',
      `kasse_rksv_aeskey`='{$this->kasse_rksv_aeskey}',
      `kasse_rksv_publiczertifikat`='{$this->kasse_rksv_publiczertifikat}',
      `kasse_rksv_publiczertifikatkette`='{$this->kasse_rksv_publiczertifikatkette}',
      `kasse_rksv_kassenid`='{$this->kasse_rksv_kassenid}',
      `kasse_gutschrift`='{$this->kasse_gutschrift}',
      `rechnungerzeugen`='{$this->rechnungerzeugen}',
      `pos_artikeltexteuebernehmen`='{$this->pos_artikeltexteuebernehmen}',
      `pos_anzeigenetto`='{$this->pos_anzeigenetto}',
      `pos_zwischenspeichern`='{$this->pos_zwischenspeichern}',
      `kasse_button_belegladen`='{$this->kasse_button_belegladen}',
      `kasse_button_storno`='{$this->kasse_button_storno}',
      `pos_kundenalleprojekte`='{$this->pos_kundenalleprojekte}',
      `pos_artikelnurausprojekt`='{$this->pos_artikelnurausprojekt}',
      `allechargenmhd`='{$this->allechargenmhd}',
      `anzeigesteuerbelege`='{$this->anzeigesteuerbelege}',
      `pos_grosseansicht`='{$this->pos_grosseansicht}',
      `preisberechnung`='{$this->preisberechnung}',
      `steuernummer`='{$this->steuernummer}',
      `paketmarkeautodrucken`='{$this->paketmarkeautodrucken}',
      `orderpicking_sort`='{$this->orderpicking_sort}',
      `deactivateautoshipping`='{$this->deactivateautoshipping}',
      `pos_sumarticles`='{$this->pos_sumarticles}',
      `manualtracking`='{$this->manualtracking}',
      `zahlungsweise`='{$this->zahlungsweise}',
      `zahlungsweiselieferant`='{$this->zahlungsweiselieferant}',
      `versandart`='{$this->versandart}',
      `ups_api_user`='{$this->ups_api_user}',
      `ups_api_password`='{$this->ups_api_password}',
      `ups_api_key`='{$this->ups_api_key}',
      `ups_accountnumber`='{$this->ups_accountnumber}',
      `ups_company_name`='{$this->ups_company_name}',
      `ups_street_name`='{$this->ups_street_name}',
      `ups_street_number`='{$this->ups_street_number}',
      `ups_zip`='{$this->ups_zip}',
      `ups_country`='{$this->ups_country}',
      `ups_city`='{$this->ups_city}',
      `ups_email`='{$this->ups_email}',
      `ups_phone`='{$this->ups_phone}',
      `ups_internet`='{$this->ups_internet}',
      `ups_contact_person`='{$this->ups_contact_person}',
      `ups_WeightInKG`='{$this->ups_WeightInKG}',
      `ups_LengthInCM`='{$this->ups_LengthInCM}',
      `ups_WidthInCM`='{$this->ups_WidthInCM}',
      `ups_HeightInCM`='{$this->ups_HeightInCM}',
      `ups_drucker`='{$this->ups_drucker}',
      `ups_ausgabe`='{$this->ups_ausgabe}',
      `ups_package_code`='{$this->ups_package_code}',
      `ups_package_description`='{$this->ups_package_description}',
      `ups_service_code`='{$this->ups_service_code}',
      `ups_service_description`='{$this->ups_service_description}',
      `email_html_template`='{$this->email_html_template}',
      `druckanhang`='{$this->druckanhang}',
      `mailanhang`='{$this->mailanhang}',
      `next_retoure`='{$this->next_retoure}',
      `next_goodspostingdocument`='{$this->next_goodspostingdocument}',
      `pos_disable_single_entries`='{$this->pos_disable_single_entries}',
      `pos_disable_single_day`='{$this->pos_disable_single_day}',
      `pos_disable_counting_protocol`='{$this->pos_disable_counting_protocol}',
      `pos_disable_signature`='{$this->pos_disable_signature}',
      `steuer_erloese_inland_normal`='{$this->steuer_erloese_inland_normal}',
      `steuer_aufwendung_inland_normal`='{$this->steuer_aufwendung_inland_normal}',
      `steuer_erloese_inland_ermaessigt`='{$this->steuer_erloese_inland_ermaessigt}',
      `steuer_aufwendung_inland_ermaessigt`='{$this->steuer_aufwendung_inland_ermaessigt}',
      `steuer_erloese_inland_nichtsteuerbar`='{$this->steuer_erloese_inland_nichtsteuerbar}',
      `steuer_aufwendung_inland_nichtsteuerbar`='{$this->steuer_aufwendung_inland_nichtsteuerbar}',
      `steuer_erloese_inland_innergemeinschaftlich`='{$this->steuer_erloese_inland_innergemeinschaftlich}',
      `steuer_aufwendung_inland_innergemeinschaftlich`='{$this->steuer_aufwendung_inland_innergemeinschaftlich}',
      `steuer_erloese_inland_eunormal`='{$this->steuer_erloese_inland_eunormal}',
      `steuer_aufwendung_inland_eunormal`='{$this->steuer_aufwendung_inland_eunormal}',
      `steuer_erloese_inland_euermaessigt`='{$this->steuer_erloese_inland_euermaessigt}',
      `steuer_aufwendung_inland_euermaessigt`='{$this->steuer_aufwendung_inland_euermaessigt}',
      `steuer_erloese_inland_export`='{$this->steuer_erloese_inland_export}',
      `steuer_aufwendung_inland_import`='{$this->steuer_aufwendung_inland_import}',
      `create_proformainvoice`='{$this->create_proformainvoice}',
      `print_proformainvoice`='{$this->print_proformainvoice}',
      `proformainvoice_amount`='{$this->proformainvoice_amount}',
      `anzeigesteuerbelegebestellung`='{$this->anzeigesteuerbelegebestellung}',
      `autobestbeforebatch`='{$this->autobestbeforebatch}',
      `allwaysautobestbeforebatch`='{$this->allwaysautobestbeforebatch}',
      `kommissionierlauflieferschein`='{$this->kommissionierlauflieferschein}',
      `intraship_exportdrucker`='{$this->intraship_exportdrucker}',
      `multiorderpicking`='{$this->multiorderpicking}',
      `standardlager`='{$this->standardlager}',
      `standardlagerproduktion`='{$this->standardlagerproduktion}',
      `klarna_merchantid`='{$this->klarna_merchantid}',
      `klarna_sharedsecret`='{$this->klarna_sharedsecret}',
      `nurlagerartikel`='{$this->nurlagerartikel}',
      `paketmarkedrucken`='{$this->paketmarkedrucken}',
      `lieferscheinedrucken`='{$this->lieferscheinedrucken}',
      `lieferscheinedruckenmenge`='{$this->lieferscheinedruckenmenge}',
      `auftragdrucken`='{$this->auftragdrucken}',
      `auftragdruckenmenge`='{$this->auftragdruckenmenge}',
      `druckennachtracking`='{$this->druckennachtracking}',
      `exportdruckrechnungstufe1`='{$this->exportdruckrechnungstufe1}',
      `exportdruckrechnungstufe1menge`='{$this->exportdruckrechnungstufe1menge}',
      `exportdruckrechnung`='{$this->exportdruckrechnung}',
      `exportdruckrechnungmenge`='{$this->exportdruckrechnungmenge}',
      `kommissionierlistestufe1`='{$this->kommissionierlistestufe1}',
      `kommissionierlistestufe1menge`='{$this->kommissionierlistestufe1menge}',
      `fremdnummerscanerlauben`='{$this->fremdnummerscanerlauben}',
      `zvt100url`='{$this->zvt100url}',
      `zvt100port`='{$this->zvt100port}',
      `production_show_only_needed_storages`='{$this->production_show_only_needed_storages}',
      `produktion_extra_seiten`='{$this->produktion_extra_seiten}',
      `kasse_button_trinkgeldeckredit`='{$this->kasse_button_trinkgeldeckredit}',
      `kasse_autologout`='{$this->kasse_autologout}',
      `kasse_autologout_abschluss`='{$this->kasse_autologout_abschluss}',
      `next_receiptdocument`='{$this->next_receiptdocument}',
      `taxfromdoctypesettings`='{$this->taxfromdoctypesettings}'
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

    $sql = "DELETE FROM `projekt` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->name='';
    $this->abkuerzung='';
    $this->verantwortlicher='';
    $this->beschreibung='';
    $this->sonstiges='';
    $this->aktiv='';
    $this->farbe='';
    $this->autoversand='';
    $this->checkok='';
    $this->portocheck='';
    $this->automailrechnung='';
    $this->checkname='';
    $this->zahlungserinnerung='';
    $this->zahlungsmailbedinungen='';
    $this->folgebestaetigung='';
    $this->stornomail='';
    $this->kundenfreigabe_loeschen='';
    $this->autobestellung='';
    $this->speziallieferschein='';
    $this->lieferscheinbriefpapier='';
    $this->speziallieferscheinbeschriftung='';
    $this->firma='';
    $this->geloescht='';
    $this->logdatei='';
    $this->steuersatz_normal='';
    $this->steuersatz_zwischen='';
    $this->steuersatz_ermaessigt='';
    $this->steuersatz_starkermaessigt='';
    $this->steuersatz_dienstleistung='';
    $this->waehrung='';
    $this->eigenesteuer='';
    $this->druckerlogistikstufe1='';
    $this->druckerlogistikstufe2='';
    $this->selbstabholermail='';
    $this->eanherstellerscan='';
    $this->reservierung='';
    $this->verkaufszahlendiagram='';
    $this->oeffentlich='';
    $this->shopzwangsprojekt='';
    $this->kunde='';
    $this->dpdkundennr='';
    $this->dhlkundennr='';
    $this->dhlformat='';
    $this->dpdformat='';
    $this->paketmarke_einzeldatei='';
    $this->dpdpfad='';
    $this->dhlpfad='';
    $this->upspfad='';
    $this->dhlintodb='';
    $this->intraship_enabled='';
    $this->intraship_drucker='';
    $this->intraship_testmode='';
    $this->intraship_user='';
    $this->intraship_signature='';
    $this->intraship_ekp='';
    $this->intraship_api_user='';
    $this->intraship_api_password='';
    $this->intraship_company_name='';
    $this->intraship_street_name='';
    $this->intraship_street_number='';
    $this->intraship_zip='';
    $this->intraship_country='';
    $this->intraship_city='';
    $this->intraship_email='';
    $this->intraship_phone='';
    $this->intraship_internet='';
    $this->intraship_contact_person='';
    $this->intraship_account_owner='';
    $this->intraship_account_number='';
    $this->intraship_bank_code='';
    $this->intraship_bank_name='';
    $this->intraship_iban='';
    $this->intraship_bic='';
    $this->intraship_WeightInKG='';
    $this->intraship_LengthInCM='';
    $this->intraship_WidthInCM='';
    $this->intraship_HeightInCM='';
    $this->intraship_PackageType='';
    $this->abrechnungsart='';
    $this->kommissionierverfahren='';
    $this->wechselaufeinstufig='';
    $this->projektuebergreifendkommisionieren='';
    $this->absendeadresse='';
    $this->absendename='';
    $this->absendesignatur='';
    $this->autodruckrechnung='';
    $this->autodruckversandbestaetigung='';
    $this->automailversandbestaetigung='';
    $this->autodrucklieferschein='';
    $this->automaillieferschein='';
    $this->autodruckstorno='';
    $this->autodruckanhang='';
    $this->automailanhang='';
    $this->autodruckerrechnung='';
    $this->autodruckerlieferschein='';
    $this->autodruckeranhang='';
    $this->autodruckrechnungmenge='';
    $this->autodrucklieferscheinmenge='';
    $this->eigenernummernkreis='';
    $this->next_angebot='';
    $this->next_auftrag='';
    $this->next_rechnung='';
    $this->next_lieferschein='';
    $this->next_arbeitsnachweis='';
    $this->next_reisekosten='';
    $this->next_bestellung='';
    $this->next_gutschrift='';
    $this->next_kundennummer='';
    $this->next_lieferantennummer='';
    $this->next_mitarbeiternummer='';
    $this->next_waren='';
    $this->next_produktion='';
    $this->next_sonstiges='';
    $this->next_anfrage='';
    $this->next_artikelnummer='';
    $this->gesamtstunden_max='';
    $this->auftragid='';
    $this->dhlzahlungmandant='';
    $this->dhlretourenschein='';
    $this->land='';
    $this->etiketten_positionen='';
    $this->etiketten_drucker='';
    $this->etiketten_art='';
    $this->seriennummernerfassen='';
    $this->versandzweigeteilt='';
    $this->nachnahmecheck='';
    $this->kasse_lieferschein_anlegen='';
    $this->kasse_lagerprozess='';
    $this->kasse_belegausgabe='';
    $this->kasse_preisgruppe='';
    $this->kasse_text_bemerkung='';
    $this->kasse_text_freitext='';
    $this->kasse_drucker='';
    $this->kasse_lieferschein='';
    $this->kasse_rechnung='';
    $this->kasse_lieferschein_doppel='';
    $this->kasse_lager='';
    $this->kasse_konto='';
    $this->kasse_laufkundschaft='';
    $this->kasse_rabatt_artikel='';
    $this->kasse_zahlung_bar='';
    $this->kasse_zahlung_ec='';
    $this->kasse_zahlung_kreditkarte='';
    $this->kasse_zahlung_ueberweisung='';
    $this->kasse_zahlung_paypal='';
    $this->kasse_extra_keinbeleg='';
    $this->kasse_extra_rechnung='';
    $this->kasse_extra_quittung='';
    $this->kasse_extra_gutschein='';
    $this->kasse_extra_rabatt_prozent='';
    $this->kasse_extra_rabatt_euro='';
    $this->kasse_adresse_erweitert='';
    $this->kasse_zahlungsauswahl_zwang='';
    $this->kasse_button_entnahme='';
    $this->kasse_button_trinkgeld='';
    $this->kasse_vorauswahl_anrede='';
    $this->kasse_erweiterte_lagerabfrage='';
    $this->filialadresse='';
    $this->versandprojektfiliale='';
    $this->differenz_auslieferung_tage='';
    $this->autostuecklistenanpassung='';
    $this->dpdendung='';
    $this->dhlendung='';
    $this->tracking_substr_start='';
    $this->tracking_remove_kundennummer='';
    $this->tracking_substr_length='';
    $this->go_drucker='';
    $this->go_apiurl_prefix='';
    $this->go_apiurl_postfix='';
    $this->go_apiurl_user='';
    $this->go_username='';
    $this->go_password='';
    $this->go_ax4nr='';
    $this->go_name1='';
    $this->go_name2='';
    $this->go_abteilung='';
    $this->go_strasse1='';
    $this->go_strasse2='';
    $this->go_hausnummer='';
    $this->go_plz='';
    $this->go_ort='';
    $this->go_land='';
    $this->go_standardgewicht='';
    $this->go_format='';
    $this->go_ausgabe='';
    $this->intraship_exportgrund='';
    $this->billsafe_merchantId='';
    $this->billsafe_merchantLicenseSandbox='';
    $this->billsafe_merchantLicenseLive='';
    $this->billsafe_applicationSignature='';
    $this->billsafe_applicationVersion='';
    $this->secupay_apikey='';
    $this->secupay_url='';
    $this->secupay_demo='';
    $this->mahnwesen='';
    $this->status='';
    $this->kasse_bondrucker='';
    $this->kasse_bondrucker_aktiv='';
    $this->kasse_bondrucker_qrcode='';
    $this->kasse_bon_zeile1='';
    $this->kasse_bon_zeile2='';
    $this->kasse_bon_zeile3='';
    $this->kasse_zahlung_bar_bezahlt='';
    $this->kasse_zahlung_ec_bezahlt='';
    $this->kasse_zahlung_kreditkarte_bezahlt='';
    $this->kasse_zahlung_ueberweisung_bezahlt='';
    $this->kasse_zahlung_paypal_bezahlt='';
    $this->kasse_quittung_rechnung='';
    $this->kasse_print_qr='';
    $this->kasse_button_einlage='';
    $this->kasse_button_schublade='';
    $this->produktionauftragautomatischfreigeben='';
    $this->versandlagerplatzanzeigen='';
    $this->versandartikelnameausstammdaten='';
    $this->projektlager='';
    $this->tracing_substr_length='';
    $this->intraship_partnerid='';
    $this->intraship_retourenlabel='';
    $this->intraship_retourenaccount='';
    $this->absendegrussformel='';
    $this->autodruckrechnungdoppel='';
    $this->intraship_partnerid_welt='';
    $this->next_kalkulation='';
    $this->next_preisanfrage='';
    $this->next_proformarechnung='';
    $this->next_verbindlichkeit='';
    $this->freifeld1='';
    $this->freifeld2='';
    $this->freifeld3='';
    $this->freifeld4='';
    $this->freifeld5='';
    $this->freifeld6='';
    $this->freifeld7='';
    $this->freifeld8='';
    $this->freifeld9='';
    $this->freifeld10='';
    $this->mahnwesen_abweichender_versender='';
    $this->lagerplatzlieferscheinausblenden='';
    $this->etiketten_sort='';
    $this->eanherstellerscanerlauben='';
    $this->chargenerfassen='';
    $this->mhderfassen='';
    $this->autodruckrechnungstufe1='';
    $this->autodruckrechnungstufe1menge='';
    $this->autodruckrechnungstufe1mail='';
    $this->autodruckkommissionierscheinstufe1='';
    $this->autodruckkommissionierscheinstufe1menge='';
    $this->kasse_bondrucker_freifeld='';
    $this->kasse_bondrucker_anzahl='';
    $this->kasse_rksv_aktiv='';
    $this->kasse_rksv_tool='';
    $this->kasse_rksv_kartenleser='';
    $this->kasse_rksv_karteseriennummer='';
    $this->kasse_rksv_kartepin='';
    $this->kasse_rksv_aeskey='';
    $this->kasse_rksv_publiczertifikat='';
    $this->kasse_rksv_publiczertifikatkette='';
    $this->kasse_rksv_kassenid='';
    $this->kasse_gutschrift='';
    $this->rechnungerzeugen='';
    $this->pos_artikeltexteuebernehmen='';
    $this->pos_anzeigenetto='';
    $this->pos_zwischenspeichern='';
    $this->kasse_button_belegladen='';
    $this->kasse_button_storno='';
    $this->pos_kundenalleprojekte='';
    $this->pos_artikelnurausprojekt='';
    $this->allechargenmhd='';
    $this->anzeigesteuerbelege='';
    $this->pos_grosseansicht='';
    $this->preisberechnung='';
    $this->steuernummer='';
    $this->paketmarkeautodrucken='';
    $this->orderpicking_sort='';
    $this->deactivateautoshipping='';
    $this->pos_sumarticles='';
    $this->manualtracking='';
    $this->zahlungsweise='';
    $this->zahlungsweiselieferant='';
    $this->versandart='';
    $this->ups_api_user='';
    $this->ups_api_password='';
    $this->ups_api_key='';
    $this->ups_accountnumber='';
    $this->ups_company_name='';
    $this->ups_street_name='';
    $this->ups_street_number='';
    $this->ups_zip='';
    $this->ups_country='';
    $this->ups_city='';
    $this->ups_email='';
    $this->ups_phone='';
    $this->ups_internet='';
    $this->ups_contact_person='';
    $this->ups_WeightInKG='';
    $this->ups_LengthInCM='';
    $this->ups_WidthInCM='';
    $this->ups_HeightInCM='';
    $this->ups_drucker='';
    $this->ups_ausgabe='';
    $this->ups_package_code='';
    $this->ups_package_description='';
    $this->ups_service_code='';
    $this->ups_service_description='';
    $this->email_html_template='';
    $this->druckanhang='';
    $this->mailanhang='';
    $this->next_retoure='';
    $this->next_goodspostingdocument='';
    $this->pos_disable_single_entries='';
    $this->pos_disable_single_day='';
    $this->pos_disable_counting_protocol='';
    $this->pos_disable_signature='';
    $this->steuer_erloese_inland_normal='';
    $this->steuer_aufwendung_inland_normal='';
    $this->steuer_erloese_inland_ermaessigt='';
    $this->steuer_aufwendung_inland_ermaessigt='';
    $this->steuer_erloese_inland_nichtsteuerbar='';
    $this->steuer_aufwendung_inland_nichtsteuerbar='';
    $this->steuer_erloese_inland_innergemeinschaftlich='';
    $this->steuer_aufwendung_inland_innergemeinschaftlich='';
    $this->steuer_erloese_inland_eunormal='';
    $this->steuer_aufwendung_inland_eunormal='';
    $this->steuer_erloese_inland_euermaessigt='';
    $this->steuer_aufwendung_inland_euermaessigt='';
    $this->steuer_erloese_inland_export='';
    $this->steuer_aufwendung_inland_import='';
    $this->create_proformainvoice='';
    $this->print_proformainvoice='';
    $this->proformainvoice_amount='';
    $this->anzeigesteuerbelegebestellung='';
    $this->autobestbeforebatch='';
    $this->allwaysautobestbeforebatch='';
    $this->kommissionierlauflieferschein='';
    $this->intraship_exportdrucker='';
    $this->multiorderpicking='';
    $this->standardlager='';
    $this->standardlagerproduktion='';
    $this->klarna_merchantid='';
    $this->klarna_sharedsecret='';
    $this->nurlagerartikel='';
    $this->paketmarkedrucken='';
    $this->lieferscheinedrucken='';
    $this->lieferscheinedruckenmenge='';
    $this->auftragdrucken='';
    $this->auftragdruckenmenge='';
    $this->druckennachtracking='';
    $this->exportdruckrechnungstufe1='';
    $this->exportdruckrechnungstufe1menge='';
    $this->exportdruckrechnung='';
    $this->exportdruckrechnungmenge='';
    $this->kommissionierlistestufe1='';
    $this->kommissionierlistestufe1menge='';
    $this->fremdnummerscanerlauben='';
    $this->zvt100url='';
    $this->zvt100port='';
    $this->production_show_only_needed_storages='';
    $this->produktion_extra_seiten='';
    $this->kasse_button_trinkgeldeckredit='';
    $this->kasse_autologout='';
    $this->kasse_autologout_abschluss='';
    $this->next_receiptdocument='';
    $this->taxfromdoctypesettings='';
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
  public function SetName($value) { $this->name=$value; }
  public function GetName() { return $this->name; }
  public function SetAbkuerzung($value) { $this->abkuerzung=$value; }
  public function GetAbkuerzung() { return $this->abkuerzung; }
  public function SetVerantwortlicher($value) { $this->verantwortlicher=$value; }
  public function GetVerantwortlicher() { return $this->verantwortlicher; }
  public function SetBeschreibung($value) { $this->beschreibung=$value; }
  public function GetBeschreibung() { return $this->beschreibung; }
  public function SetSonstiges($value) { $this->sonstiges=$value; }
  public function GetSonstiges() { return $this->sonstiges; }
  public function SetAktiv($value) { $this->aktiv=$value; }
  public function GetAktiv() { return $this->aktiv; }
  public function SetFarbe($value) { $this->farbe=$value; }
  public function GetFarbe() { return $this->farbe; }
  public function SetAutoversand($value) { $this->autoversand=$value; }
  public function GetAutoversand() { return $this->autoversand; }
  public function SetCheckok($value) { $this->checkok=$value; }
  public function GetCheckok() { return $this->checkok; }
  public function SetPortocheck($value) { $this->portocheck=$value; }
  public function GetPortocheck() { return $this->portocheck; }
  public function SetAutomailrechnung($value) { $this->automailrechnung=$value; }
  public function GetAutomailrechnung() { return $this->automailrechnung; }
  public function SetCheckname($value) { $this->checkname=$value; }
  public function GetCheckname() { return $this->checkname; }
  public function SetZahlungserinnerung($value) { $this->zahlungserinnerung=$value; }
  public function GetZahlungserinnerung() { return $this->zahlungserinnerung; }
  public function SetZahlungsmailbedinungen($value) { $this->zahlungsmailbedinungen=$value; }
  public function GetZahlungsmailbedinungen() { return $this->zahlungsmailbedinungen; }
  public function SetFolgebestaetigung($value) { $this->folgebestaetigung=$value; }
  public function GetFolgebestaetigung() { return $this->folgebestaetigung; }
  public function SetStornomail($value) { $this->stornomail=$value; }
  public function GetStornomail() { return $this->stornomail; }
  public function SetKundenfreigabe_Loeschen($value) { $this->kundenfreigabe_loeschen=$value; }
  public function GetKundenfreigabe_Loeschen() { return $this->kundenfreigabe_loeschen; }
  public function SetAutobestellung($value) { $this->autobestellung=$value; }
  public function GetAutobestellung() { return $this->autobestellung; }
  public function SetSpeziallieferschein($value) { $this->speziallieferschein=$value; }
  public function GetSpeziallieferschein() { return $this->speziallieferschein; }
  public function SetLieferscheinbriefpapier($value) { $this->lieferscheinbriefpapier=$value; }
  public function GetLieferscheinbriefpapier() { return $this->lieferscheinbriefpapier; }
  public function SetSpeziallieferscheinbeschriftung($value) { $this->speziallieferscheinbeschriftung=$value; }
  public function GetSpeziallieferscheinbeschriftung() { return $this->speziallieferscheinbeschriftung; }
  public function SetFirma($value) { $this->firma=$value; }
  public function GetFirma() { return $this->firma; }
  public function SetGeloescht($value) { $this->geloescht=$value; }
  public function GetGeloescht() { return $this->geloescht; }
  public function SetLogdatei($value) { $this->logdatei=$value; }
  public function GetLogdatei() { return $this->logdatei; }
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
  public function SetEigenesteuer($value) { $this->eigenesteuer=$value; }
  public function GetEigenesteuer() { return $this->eigenesteuer; }
  public function SetDruckerlogistikstufe1($value) { $this->druckerlogistikstufe1=$value; }
  public function GetDruckerlogistikstufe1() { return $this->druckerlogistikstufe1; }
  public function SetDruckerlogistikstufe2($value) { $this->druckerlogistikstufe2=$value; }
  public function GetDruckerlogistikstufe2() { return $this->druckerlogistikstufe2; }
  public function SetSelbstabholermail($value) { $this->selbstabholermail=$value; }
  public function GetSelbstabholermail() { return $this->selbstabholermail; }
  public function SetEanherstellerscan($value) { $this->eanherstellerscan=$value; }
  public function GetEanherstellerscan() { return $this->eanherstellerscan; }
  public function SetReservierung($value) { $this->reservierung=$value; }
  public function GetReservierung() { return $this->reservierung; }
  public function SetVerkaufszahlendiagram($value) { $this->verkaufszahlendiagram=$value; }
  public function GetVerkaufszahlendiagram() { return $this->verkaufszahlendiagram; }
  public function SetOeffentlich($value) { $this->oeffentlich=$value; }
  public function GetOeffentlich() { return $this->oeffentlich; }
  public function SetShopzwangsprojekt($value) { $this->shopzwangsprojekt=$value; }
  public function GetShopzwangsprojekt() { return $this->shopzwangsprojekt; }
  public function SetKunde($value) { $this->kunde=$value; }
  public function GetKunde() { return $this->kunde; }
  public function SetDpdkundennr($value) { $this->dpdkundennr=$value; }
  public function GetDpdkundennr() { return $this->dpdkundennr; }
  public function SetDhlkundennr($value) { $this->dhlkundennr=$value; }
  public function GetDhlkundennr() { return $this->dhlkundennr; }
  public function SetDhlformat($value) { $this->dhlformat=$value; }
  public function GetDhlformat() { return $this->dhlformat; }
  public function SetDpdformat($value) { $this->dpdformat=$value; }
  public function GetDpdformat() { return $this->dpdformat; }
  public function SetPaketmarke_Einzeldatei($value) { $this->paketmarke_einzeldatei=$value; }
  public function GetPaketmarke_Einzeldatei() { return $this->paketmarke_einzeldatei; }
  public function SetDpdpfad($value) { $this->dpdpfad=$value; }
  public function GetDpdpfad() { return $this->dpdpfad; }
  public function SetDhlpfad($value) { $this->dhlpfad=$value; }
  public function GetDhlpfad() { return $this->dhlpfad; }
  public function SetUpspfad($value) { $this->upspfad=$value; }
  public function GetUpspfad() { return $this->upspfad; }
  public function SetDhlintodb($value) { $this->dhlintodb=$value; }
  public function GetDhlintodb() { return $this->dhlintodb; }
  public function SetIntraship_Enabled($value) { $this->intraship_enabled=$value; }
  public function GetIntraship_Enabled() { return $this->intraship_enabled; }
  public function SetIntraship_Drucker($value) { $this->intraship_drucker=$value; }
  public function GetIntraship_Drucker() { return $this->intraship_drucker; }
  public function SetIntraship_Testmode($value) { $this->intraship_testmode=$value; }
  public function GetIntraship_Testmode() { return $this->intraship_testmode; }
  public function SetIntraship_User($value) { $this->intraship_user=$value; }
  public function GetIntraship_User() { return $this->intraship_user; }
  public function SetIntraship_Signature($value) { $this->intraship_signature=$value; }
  public function GetIntraship_Signature() { return $this->intraship_signature; }
  public function SetIntraship_Ekp($value) { $this->intraship_ekp=$value; }
  public function GetIntraship_Ekp() { return $this->intraship_ekp; }
  public function SetIntraship_Api_User($value) { $this->intraship_api_user=$value; }
  public function GetIntraship_Api_User() { return $this->intraship_api_user; }
  public function SetIntraship_Api_Password($value) { $this->intraship_api_password=$value; }
  public function GetIntraship_Api_Password() { return $this->intraship_api_password; }
  public function SetIntraship_Company_Name($value) { $this->intraship_company_name=$value; }
  public function GetIntraship_Company_Name() { return $this->intraship_company_name; }
  public function SetIntraship_Street_Name($value) { $this->intraship_street_name=$value; }
  public function GetIntraship_Street_Name() { return $this->intraship_street_name; }
  public function SetIntraship_Street_Number($value) { $this->intraship_street_number=$value; }
  public function GetIntraship_Street_Number() { return $this->intraship_street_number; }
  public function SetIntraship_Zip($value) { $this->intraship_zip=$value; }
  public function GetIntraship_Zip() { return $this->intraship_zip; }
  public function SetIntraship_Country($value) { $this->intraship_country=$value; }
  public function GetIntraship_Country() { return $this->intraship_country; }
  public function SetIntraship_City($value) { $this->intraship_city=$value; }
  public function GetIntraship_City() { return $this->intraship_city; }
  public function SetIntraship_Email($value) { $this->intraship_email=$value; }
  public function GetIntraship_Email() { return $this->intraship_email; }
  public function SetIntraship_Phone($value) { $this->intraship_phone=$value; }
  public function GetIntraship_Phone() { return $this->intraship_phone; }
  public function SetIntraship_Internet($value) { $this->intraship_internet=$value; }
  public function GetIntraship_Internet() { return $this->intraship_internet; }
  public function SetIntraship_Contact_Person($value) { $this->intraship_contact_person=$value; }
  public function GetIntraship_Contact_Person() { return $this->intraship_contact_person; }
  public function SetIntraship_Account_Owner($value) { $this->intraship_account_owner=$value; }
  public function GetIntraship_Account_Owner() { return $this->intraship_account_owner; }
  public function SetIntraship_Account_Number($value) { $this->intraship_account_number=$value; }
  public function GetIntraship_Account_Number() { return $this->intraship_account_number; }
  public function SetIntraship_Bank_Code($value) { $this->intraship_bank_code=$value; }
  public function GetIntraship_Bank_Code() { return $this->intraship_bank_code; }
  public function SetIntraship_Bank_Name($value) { $this->intraship_bank_name=$value; }
  public function GetIntraship_Bank_Name() { return $this->intraship_bank_name; }
  public function SetIntraship_Iban($value) { $this->intraship_iban=$value; }
  public function GetIntraship_Iban() { return $this->intraship_iban; }
  public function SetIntraship_Bic($value) { $this->intraship_bic=$value; }
  public function GetIntraship_Bic() { return $this->intraship_bic; }
  public function SetIntraship_Weightinkg($value) { $this->intraship_WeightInKG=$value; }
  public function GetIntraship_Weightinkg() { return $this->intraship_WeightInKG; }
  public function SetIntraship_Lengthincm($value) { $this->intraship_LengthInCM=$value; }
  public function GetIntraship_Lengthincm() { return $this->intraship_LengthInCM; }
  public function SetIntraship_Widthincm($value) { $this->intraship_WidthInCM=$value; }
  public function GetIntraship_Widthincm() { return $this->intraship_WidthInCM; }
  public function SetIntraship_Heightincm($value) { $this->intraship_HeightInCM=$value; }
  public function GetIntraship_Heightincm() { return $this->intraship_HeightInCM; }
  public function SetIntraship_Packagetype($value) { $this->intraship_PackageType=$value; }
  public function GetIntraship_Packagetype() { return $this->intraship_PackageType; }
  public function SetAbrechnungsart($value) { $this->abrechnungsart=$value; }
  public function GetAbrechnungsart() { return $this->abrechnungsart; }
  public function SetKommissionierverfahren($value) { $this->kommissionierverfahren=$value; }
  public function GetKommissionierverfahren() { return $this->kommissionierverfahren; }
  public function SetWechselaufeinstufig($value) { $this->wechselaufeinstufig=$value; }
  public function GetWechselaufeinstufig() { return $this->wechselaufeinstufig; }
  public function SetProjektuebergreifendkommisionieren($value) { $this->projektuebergreifendkommisionieren=$value; }
  public function GetProjektuebergreifendkommisionieren() { return $this->projektuebergreifendkommisionieren; }
  public function SetAbsendeadresse($value) { $this->absendeadresse=$value; }
  public function GetAbsendeadresse() { return $this->absendeadresse; }
  public function SetAbsendename($value) { $this->absendename=$value; }
  public function GetAbsendename() { return $this->absendename; }
  public function SetAbsendesignatur($value) { $this->absendesignatur=$value; }
  public function GetAbsendesignatur() { return $this->absendesignatur; }
  public function SetAutodruckrechnung($value) { $this->autodruckrechnung=$value; }
  public function GetAutodruckrechnung() { return $this->autodruckrechnung; }
  public function SetAutodruckversandbestaetigung($value) { $this->autodruckversandbestaetigung=$value; }
  public function GetAutodruckversandbestaetigung() { return $this->autodruckversandbestaetigung; }
  public function SetAutomailversandbestaetigung($value) { $this->automailversandbestaetigung=$value; }
  public function GetAutomailversandbestaetigung() { return $this->automailversandbestaetigung; }
  public function SetAutodrucklieferschein($value) { $this->autodrucklieferschein=$value; }
  public function GetAutodrucklieferschein() { return $this->autodrucklieferschein; }
  public function SetAutomaillieferschein($value) { $this->automaillieferschein=$value; }
  public function GetAutomaillieferschein() { return $this->automaillieferschein; }
  public function SetAutodruckstorno($value) { $this->autodruckstorno=$value; }
  public function GetAutodruckstorno() { return $this->autodruckstorno; }
  public function SetAutodruckanhang($value) { $this->autodruckanhang=$value; }
  public function GetAutodruckanhang() { return $this->autodruckanhang; }
  public function SetAutomailanhang($value) { $this->automailanhang=$value; }
  public function GetAutomailanhang() { return $this->automailanhang; }
  public function SetAutodruckerrechnung($value) { $this->autodruckerrechnung=$value; }
  public function GetAutodruckerrechnung() { return $this->autodruckerrechnung; }
  public function SetAutodruckerlieferschein($value) { $this->autodruckerlieferschein=$value; }
  public function GetAutodruckerlieferschein() { return $this->autodruckerlieferschein; }
  public function SetAutodruckeranhang($value) { $this->autodruckeranhang=$value; }
  public function GetAutodruckeranhang() { return $this->autodruckeranhang; }
  public function SetAutodruckrechnungmenge($value) { $this->autodruckrechnungmenge=$value; }
  public function GetAutodruckrechnungmenge() { return $this->autodruckrechnungmenge; }
  public function SetAutodrucklieferscheinmenge($value) { $this->autodrucklieferscheinmenge=$value; }
  public function GetAutodrucklieferscheinmenge() { return $this->autodrucklieferscheinmenge; }
  public function SetEigenernummernkreis($value) { $this->eigenernummernkreis=$value; }
  public function GetEigenernummernkreis() { return $this->eigenernummernkreis; }
  public function SetNext_Angebot($value) { $this->next_angebot=$value; }
  public function GetNext_Angebot() { return $this->next_angebot; }
  public function SetNext_Auftrag($value) { $this->next_auftrag=$value; }
  public function GetNext_Auftrag() { return $this->next_auftrag; }
  public function SetNext_Rechnung($value) { $this->next_rechnung=$value; }
  public function GetNext_Rechnung() { return $this->next_rechnung; }
  public function SetNext_Lieferschein($value) { $this->next_lieferschein=$value; }
  public function GetNext_Lieferschein() { return $this->next_lieferschein; }
  public function SetNext_Arbeitsnachweis($value) { $this->next_arbeitsnachweis=$value; }
  public function GetNext_Arbeitsnachweis() { return $this->next_arbeitsnachweis; }
  public function SetNext_Reisekosten($value) { $this->next_reisekosten=$value; }
  public function GetNext_Reisekosten() { return $this->next_reisekosten; }
  public function SetNext_Bestellung($value) { $this->next_bestellung=$value; }
  public function GetNext_Bestellung() { return $this->next_bestellung; }
  public function SetNext_Gutschrift($value) { $this->next_gutschrift=$value; }
  public function GetNext_Gutschrift() { return $this->next_gutschrift; }
  public function SetNext_Kundennummer($value) { $this->next_kundennummer=$value; }
  public function GetNext_Kundennummer() { return $this->next_kundennummer; }
  public function SetNext_Lieferantennummer($value) { $this->next_lieferantennummer=$value; }
  public function GetNext_Lieferantennummer() { return $this->next_lieferantennummer; }
  public function SetNext_Mitarbeiternummer($value) { $this->next_mitarbeiternummer=$value; }
  public function GetNext_Mitarbeiternummer() { return $this->next_mitarbeiternummer; }
  public function SetNext_Waren($value) { $this->next_waren=$value; }
  public function GetNext_Waren() { return $this->next_waren; }
  public function SetNext_Produktion($value) { $this->next_produktion=$value; }
  public function GetNext_Produktion() { return $this->next_produktion; }
  public function SetNext_Sonstiges($value) { $this->next_sonstiges=$value; }
  public function GetNext_Sonstiges() { return $this->next_sonstiges; }
  public function SetNext_Anfrage($value) { $this->next_anfrage=$value; }
  public function GetNext_Anfrage() { return $this->next_anfrage; }
  public function SetNext_Artikelnummer($value) { $this->next_artikelnummer=$value; }
  public function GetNext_Artikelnummer() { return $this->next_artikelnummer; }
  public function SetGesamtstunden_Max($value) { $this->gesamtstunden_max=$value; }
  public function GetGesamtstunden_Max() { return $this->gesamtstunden_max; }
  public function SetAuftragid($value) { $this->auftragid=$value; }
  public function GetAuftragid() { return $this->auftragid; }
  public function SetDhlzahlungmandant($value) { $this->dhlzahlungmandant=$value; }
  public function GetDhlzahlungmandant() { return $this->dhlzahlungmandant; }
  public function SetDhlretourenschein($value) { $this->dhlretourenschein=$value; }
  public function GetDhlretourenschein() { return $this->dhlretourenschein; }
  public function SetLand($value) { $this->land=$value; }
  public function GetLand() { return $this->land; }
  public function SetEtiketten_Positionen($value) { $this->etiketten_positionen=$value; }
  public function GetEtiketten_Positionen() { return $this->etiketten_positionen; }
  public function SetEtiketten_Drucker($value) { $this->etiketten_drucker=$value; }
  public function GetEtiketten_Drucker() { return $this->etiketten_drucker; }
  public function SetEtiketten_Art($value) { $this->etiketten_art=$value; }
  public function GetEtiketten_Art() { return $this->etiketten_art; }
  public function SetSeriennummernerfassen($value) { $this->seriennummernerfassen=$value; }
  public function GetSeriennummernerfassen() { return $this->seriennummernerfassen; }
  public function SetVersandzweigeteilt($value) { $this->versandzweigeteilt=$value; }
  public function GetVersandzweigeteilt() { return $this->versandzweigeteilt; }
  public function SetNachnahmecheck($value) { $this->nachnahmecheck=$value; }
  public function GetNachnahmecheck() { return $this->nachnahmecheck; }
  public function SetKasse_Lieferschein_Anlegen($value) { $this->kasse_lieferschein_anlegen=$value; }
  public function GetKasse_Lieferschein_Anlegen() { return $this->kasse_lieferschein_anlegen; }
  public function SetKasse_Lagerprozess($value) { $this->kasse_lagerprozess=$value; }
  public function GetKasse_Lagerprozess() { return $this->kasse_lagerprozess; }
  public function SetKasse_Belegausgabe($value) { $this->kasse_belegausgabe=$value; }
  public function GetKasse_Belegausgabe() { return $this->kasse_belegausgabe; }
  public function SetKasse_Preisgruppe($value) { $this->kasse_preisgruppe=$value; }
  public function GetKasse_Preisgruppe() { return $this->kasse_preisgruppe; }
  public function SetKasse_Text_Bemerkung($value) { $this->kasse_text_bemerkung=$value; }
  public function GetKasse_Text_Bemerkung() { return $this->kasse_text_bemerkung; }
  public function SetKasse_Text_Freitext($value) { $this->kasse_text_freitext=$value; }
  public function GetKasse_Text_Freitext() { return $this->kasse_text_freitext; }
  public function SetKasse_Drucker($value) { $this->kasse_drucker=$value; }
  public function GetKasse_Drucker() { return $this->kasse_drucker; }
  public function SetKasse_Lieferschein($value) { $this->kasse_lieferschein=$value; }
  public function GetKasse_Lieferschein() { return $this->kasse_lieferschein; }
  public function SetKasse_Rechnung($value) { $this->kasse_rechnung=$value; }
  public function GetKasse_Rechnung() { return $this->kasse_rechnung; }
  public function SetKasse_Lieferschein_Doppel($value) { $this->kasse_lieferschein_doppel=$value; }
  public function GetKasse_Lieferschein_Doppel() { return $this->kasse_lieferschein_doppel; }
  public function SetKasse_Lager($value) { $this->kasse_lager=$value; }
  public function GetKasse_Lager() { return $this->kasse_lager; }
  public function SetKasse_Konto($value) { $this->kasse_konto=$value; }
  public function GetKasse_Konto() { return $this->kasse_konto; }
  public function SetKasse_Laufkundschaft($value) { $this->kasse_laufkundschaft=$value; }
  public function GetKasse_Laufkundschaft() { return $this->kasse_laufkundschaft; }
  public function SetKasse_Rabatt_Artikel($value) { $this->kasse_rabatt_artikel=$value; }
  public function GetKasse_Rabatt_Artikel() { return $this->kasse_rabatt_artikel; }
  public function SetKasse_Zahlung_Bar($value) { $this->kasse_zahlung_bar=$value; }
  public function GetKasse_Zahlung_Bar() { return $this->kasse_zahlung_bar; }
  public function SetKasse_Zahlung_Ec($value) { $this->kasse_zahlung_ec=$value; }
  public function GetKasse_Zahlung_Ec() { return $this->kasse_zahlung_ec; }
  public function SetKasse_Zahlung_Kreditkarte($value) { $this->kasse_zahlung_kreditkarte=$value; }
  public function GetKasse_Zahlung_Kreditkarte() { return $this->kasse_zahlung_kreditkarte; }
  public function SetKasse_Zahlung_Ueberweisung($value) { $this->kasse_zahlung_ueberweisung=$value; }
  public function GetKasse_Zahlung_Ueberweisung() { return $this->kasse_zahlung_ueberweisung; }
  public function SetKasse_Zahlung_Paypal($value) { $this->kasse_zahlung_paypal=$value; }
  public function GetKasse_Zahlung_Paypal() { return $this->kasse_zahlung_paypal; }
  public function SetKasse_Extra_Keinbeleg($value) { $this->kasse_extra_keinbeleg=$value; }
  public function GetKasse_Extra_Keinbeleg() { return $this->kasse_extra_keinbeleg; }
  public function SetKasse_Extra_Rechnung($value) { $this->kasse_extra_rechnung=$value; }
  public function GetKasse_Extra_Rechnung() { return $this->kasse_extra_rechnung; }
  public function SetKasse_Extra_Quittung($value) { $this->kasse_extra_quittung=$value; }
  public function GetKasse_Extra_Quittung() { return $this->kasse_extra_quittung; }
  public function SetKasse_Extra_Gutschein($value) { $this->kasse_extra_gutschein=$value; }
  public function GetKasse_Extra_Gutschein() { return $this->kasse_extra_gutschein; }
  public function SetKasse_Extra_Rabatt_Prozent($value) { $this->kasse_extra_rabatt_prozent=$value; }
  public function GetKasse_Extra_Rabatt_Prozent() { return $this->kasse_extra_rabatt_prozent; }
  public function SetKasse_Extra_Rabatt_Euro($value) { $this->kasse_extra_rabatt_euro=$value; }
  public function GetKasse_Extra_Rabatt_Euro() { return $this->kasse_extra_rabatt_euro; }
  public function SetKasse_Adresse_Erweitert($value) { $this->kasse_adresse_erweitert=$value; }
  public function GetKasse_Adresse_Erweitert() { return $this->kasse_adresse_erweitert; }
  public function SetKasse_Zahlungsauswahl_Zwang($value) { $this->kasse_zahlungsauswahl_zwang=$value; }
  public function GetKasse_Zahlungsauswahl_Zwang() { return $this->kasse_zahlungsauswahl_zwang; }
  public function SetKasse_Button_Entnahme($value) { $this->kasse_button_entnahme=$value; }
  public function GetKasse_Button_Entnahme() { return $this->kasse_button_entnahme; }
  public function SetKasse_Button_Trinkgeld($value) { $this->kasse_button_trinkgeld=$value; }
  public function GetKasse_Button_Trinkgeld() { return $this->kasse_button_trinkgeld; }
  public function SetKasse_Vorauswahl_Anrede($value) { $this->kasse_vorauswahl_anrede=$value; }
  public function GetKasse_Vorauswahl_Anrede() { return $this->kasse_vorauswahl_anrede; }
  public function SetKasse_Erweiterte_Lagerabfrage($value) { $this->kasse_erweiterte_lagerabfrage=$value; }
  public function GetKasse_Erweiterte_Lagerabfrage() { return $this->kasse_erweiterte_lagerabfrage; }
  public function SetFilialadresse($value) { $this->filialadresse=$value; }
  public function GetFilialadresse() { return $this->filialadresse; }
  public function SetVersandprojektfiliale($value) { $this->versandprojektfiliale=$value; }
  public function GetVersandprojektfiliale() { return $this->versandprojektfiliale; }
  public function SetDifferenz_Auslieferung_Tage($value) { $this->differenz_auslieferung_tage=$value; }
  public function GetDifferenz_Auslieferung_Tage() { return $this->differenz_auslieferung_tage; }
  public function SetAutostuecklistenanpassung($value) { $this->autostuecklistenanpassung=$value; }
  public function GetAutostuecklistenanpassung() { return $this->autostuecklistenanpassung; }
  public function SetDpdendung($value) { $this->dpdendung=$value; }
  public function GetDpdendung() { return $this->dpdendung; }
  public function SetDhlendung($value) { $this->dhlendung=$value; }
  public function GetDhlendung() { return $this->dhlendung; }
  public function SetTracking_Substr_Start($value) { $this->tracking_substr_start=$value; }
  public function GetTracking_Substr_Start() { return $this->tracking_substr_start; }
  public function SetTracking_Remove_Kundennummer($value) { $this->tracking_remove_kundennummer=$value; }
  public function GetTracking_Remove_Kundennummer() { return $this->tracking_remove_kundennummer; }
  public function SetTracking_Substr_Length($value) { $this->tracking_substr_length=$value; }
  public function GetTracking_Substr_Length() { return $this->tracking_substr_length; }
  public function SetGo_Drucker($value) { $this->go_drucker=$value; }
  public function GetGo_Drucker() { return $this->go_drucker; }
  public function SetGo_Apiurl_Prefix($value) { $this->go_apiurl_prefix=$value; }
  public function GetGo_Apiurl_Prefix() { return $this->go_apiurl_prefix; }
  public function SetGo_Apiurl_Postfix($value) { $this->go_apiurl_postfix=$value; }
  public function GetGo_Apiurl_Postfix() { return $this->go_apiurl_postfix; }
  public function SetGo_Apiurl_User($value) { $this->go_apiurl_user=$value; }
  public function GetGo_Apiurl_User() { return $this->go_apiurl_user; }
  public function SetGo_Username($value) { $this->go_username=$value; }
  public function GetGo_Username() { return $this->go_username; }
  public function SetGo_Password($value) { $this->go_password=$value; }
  public function GetGo_Password() { return $this->go_password; }
  public function SetGo_Ax4Nr($value) { $this->go_ax4nr=$value; }
  public function GetGo_Ax4Nr() { return $this->go_ax4nr; }
  public function SetGo_Name1($value) { $this->go_name1=$value; }
  public function GetGo_Name1() { return $this->go_name1; }
  public function SetGo_Name2($value) { $this->go_name2=$value; }
  public function GetGo_Name2() { return $this->go_name2; }
  public function SetGo_Abteilung($value) { $this->go_abteilung=$value; }
  public function GetGo_Abteilung() { return $this->go_abteilung; }
  public function SetGo_Strasse1($value) { $this->go_strasse1=$value; }
  public function GetGo_Strasse1() { return $this->go_strasse1; }
  public function SetGo_Strasse2($value) { $this->go_strasse2=$value; }
  public function GetGo_Strasse2() { return $this->go_strasse2; }
  public function SetGo_Hausnummer($value) { $this->go_hausnummer=$value; }
  public function GetGo_Hausnummer() { return $this->go_hausnummer; }
  public function SetGo_Plz($value) { $this->go_plz=$value; }
  public function GetGo_Plz() { return $this->go_plz; }
  public function SetGo_Ort($value) { $this->go_ort=$value; }
  public function GetGo_Ort() { return $this->go_ort; }
  public function SetGo_Land($value) { $this->go_land=$value; }
  public function GetGo_Land() { return $this->go_land; }
  public function SetGo_Standardgewicht($value) { $this->go_standardgewicht=$value; }
  public function GetGo_Standardgewicht() { return $this->go_standardgewicht; }
  public function SetGo_Format($value) { $this->go_format=$value; }
  public function GetGo_Format() { return $this->go_format; }
  public function SetGo_Ausgabe($value) { $this->go_ausgabe=$value; }
  public function GetGo_Ausgabe() { return $this->go_ausgabe; }
  public function SetIntraship_Exportgrund($value) { $this->intraship_exportgrund=$value; }
  public function GetIntraship_Exportgrund() { return $this->intraship_exportgrund; }
  public function SetBillsafe_Merchantid($value) { $this->billsafe_merchantId=$value; }
  public function GetBillsafe_Merchantid() { return $this->billsafe_merchantId; }
  public function SetBillsafe_Merchantlicensesandbox($value) { $this->billsafe_merchantLicenseSandbox=$value; }
  public function GetBillsafe_Merchantlicensesandbox() { return $this->billsafe_merchantLicenseSandbox; }
  public function SetBillsafe_Merchantlicenselive($value) { $this->billsafe_merchantLicenseLive=$value; }
  public function GetBillsafe_Merchantlicenselive() { return $this->billsafe_merchantLicenseLive; }
  public function SetBillsafe_Applicationsignature($value) { $this->billsafe_applicationSignature=$value; }
  public function GetBillsafe_Applicationsignature() { return $this->billsafe_applicationSignature; }
  public function SetBillsafe_Applicationversion($value) { $this->billsafe_applicationVersion=$value; }
  public function GetBillsafe_Applicationversion() { return $this->billsafe_applicationVersion; }
  public function SetSecupay_Apikey($value) { $this->secupay_apikey=$value; }
  public function GetSecupay_Apikey() { return $this->secupay_apikey; }
  public function SetSecupay_Url($value) { $this->secupay_url=$value; }
  public function GetSecupay_Url() { return $this->secupay_url; }
  public function SetSecupay_Demo($value) { $this->secupay_demo=$value; }
  public function GetSecupay_Demo() { return $this->secupay_demo; }
  public function SetMahnwesen($value) { $this->mahnwesen=$value; }
  public function GetMahnwesen() { return $this->mahnwesen; }
  public function SetStatus($value) { $this->status=$value; }
  public function GetStatus() { return $this->status; }
  public function SetKasse_Bondrucker($value) { $this->kasse_bondrucker=$value; }
  public function GetKasse_Bondrucker() { return $this->kasse_bondrucker; }
  public function SetKasse_Bondrucker_Aktiv($value) { $this->kasse_bondrucker_aktiv=$value; }
  public function GetKasse_Bondrucker_Aktiv() { return $this->kasse_bondrucker_aktiv; }
  public function SetKasse_Bondrucker_Qrcode($value) { $this->kasse_bondrucker_qrcode=$value; }
  public function GetKasse_Bondrucker_Qrcode() { return $this->kasse_bondrucker_qrcode; }
  public function SetKasse_Bon_Zeile1($value) { $this->kasse_bon_zeile1=$value; }
  public function GetKasse_Bon_Zeile1() { return $this->kasse_bon_zeile1; }
  public function SetKasse_Bon_Zeile2($value) { $this->kasse_bon_zeile2=$value; }
  public function GetKasse_Bon_Zeile2() { return $this->kasse_bon_zeile2; }
  public function SetKasse_Bon_Zeile3($value) { $this->kasse_bon_zeile3=$value; }
  public function GetKasse_Bon_Zeile3() { return $this->kasse_bon_zeile3; }
  public function SetKasse_Zahlung_Bar_Bezahlt($value) { $this->kasse_zahlung_bar_bezahlt=$value; }
  public function GetKasse_Zahlung_Bar_Bezahlt() { return $this->kasse_zahlung_bar_bezahlt; }
  public function SetKasse_Zahlung_Ec_Bezahlt($value) { $this->kasse_zahlung_ec_bezahlt=$value; }
  public function GetKasse_Zahlung_Ec_Bezahlt() { return $this->kasse_zahlung_ec_bezahlt; }
  public function SetKasse_Zahlung_Kreditkarte_Bezahlt($value) { $this->kasse_zahlung_kreditkarte_bezahlt=$value; }
  public function GetKasse_Zahlung_Kreditkarte_Bezahlt() { return $this->kasse_zahlung_kreditkarte_bezahlt; }
  public function SetKasse_Zahlung_Ueberweisung_Bezahlt($value) { $this->kasse_zahlung_ueberweisung_bezahlt=$value; }
  public function GetKasse_Zahlung_Ueberweisung_Bezahlt() { return $this->kasse_zahlung_ueberweisung_bezahlt; }
  public function SetKasse_Zahlung_Paypal_Bezahlt($value) { $this->kasse_zahlung_paypal_bezahlt=$value; }
  public function GetKasse_Zahlung_Paypal_Bezahlt() { return $this->kasse_zahlung_paypal_bezahlt; }
  public function SetKasse_Quittung_Rechnung($value) { $this->kasse_quittung_rechnung=$value; }
  public function GetKasse_Quittung_Rechnung() { return $this->kasse_quittung_rechnung; }
  public function SetKasse_Print_Qr($value) { $this->kasse_print_qr=$value; }
  public function GetKasse_Print_Qr() { return $this->kasse_print_qr; }
  public function SetKasse_Button_Einlage($value) { $this->kasse_button_einlage=$value; }
  public function GetKasse_Button_Einlage() { return $this->kasse_button_einlage; }
  public function SetKasse_Button_Schublade($value) { $this->kasse_button_schublade=$value; }
  public function GetKasse_Button_Schublade() { return $this->kasse_button_schublade; }
  public function SetProduktionauftragautomatischfreigeben($value) { $this->produktionauftragautomatischfreigeben=$value; }
  public function GetProduktionauftragautomatischfreigeben() { return $this->produktionauftragautomatischfreigeben; }
  public function SetVersandlagerplatzanzeigen($value) { $this->versandlagerplatzanzeigen=$value; }
  public function GetVersandlagerplatzanzeigen() { return $this->versandlagerplatzanzeigen; }
  public function SetVersandartikelnameausstammdaten($value) { $this->versandartikelnameausstammdaten=$value; }
  public function GetVersandartikelnameausstammdaten() { return $this->versandartikelnameausstammdaten; }
  public function SetProjektlager($value) { $this->projektlager=$value; }
  public function GetProjektlager() { return $this->projektlager; }
  public function SetTracing_Substr_Length($value) { $this->tracing_substr_length=$value; }
  public function GetTracing_Substr_Length() { return $this->tracing_substr_length; }
  public function SetIntraship_Partnerid($value) { $this->intraship_partnerid=$value; }
  public function GetIntraship_Partnerid() { return $this->intraship_partnerid; }
  public function SetIntraship_Retourenlabel($value) { $this->intraship_retourenlabel=$value; }
  public function GetIntraship_Retourenlabel() { return $this->intraship_retourenlabel; }
  public function SetIntraship_Retourenaccount($value) { $this->intraship_retourenaccount=$value; }
  public function GetIntraship_Retourenaccount() { return $this->intraship_retourenaccount; }
  public function SetAbsendegrussformel($value) { $this->absendegrussformel=$value; }
  public function GetAbsendegrussformel() { return $this->absendegrussformel; }
  public function SetAutodruckrechnungdoppel($value) { $this->autodruckrechnungdoppel=$value; }
  public function GetAutodruckrechnungdoppel() { return $this->autodruckrechnungdoppel; }
  public function SetIntraship_Partnerid_Welt($value) { $this->intraship_partnerid_welt=$value; }
  public function GetIntraship_Partnerid_Welt() { return $this->intraship_partnerid_welt; }
  public function SetNext_Kalkulation($value) { $this->next_kalkulation=$value; }
  public function GetNext_Kalkulation() { return $this->next_kalkulation; }
  public function SetNext_Preisanfrage($value) { $this->next_preisanfrage=$value; }
  public function GetNext_Preisanfrage() { return $this->next_preisanfrage; }
  public function SetNext_Proformarechnung($value) { $this->next_proformarechnung=$value; }
  public function GetNext_Proformarechnung() { return $this->next_proformarechnung; }
  public function SetNext_Verbindlichkeit($value) { $this->next_verbindlichkeit=$value; }
  public function GetNext_Verbindlichkeit() { return $this->next_verbindlichkeit; }
  public function SetFreifeld1($value) { $this->freifeld1=$value; }
  public function GetFreifeld1() { return $this->freifeld1; }
  public function SetFreifeld2($value) { $this->freifeld2=$value; }
  public function GetFreifeld2() { return $this->freifeld2; }
  public function SetFreifeld3($value) { $this->freifeld3=$value; }
  public function GetFreifeld3() { return $this->freifeld3; }
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
  public function SetMahnwesen_Abweichender_Versender($value) { $this->mahnwesen_abweichender_versender=$value; }
  public function GetMahnwesen_Abweichender_Versender() { return $this->mahnwesen_abweichender_versender; }
  public function SetLagerplatzlieferscheinausblenden($value) { $this->lagerplatzlieferscheinausblenden=$value; }
  public function GetLagerplatzlieferscheinausblenden() { return $this->lagerplatzlieferscheinausblenden; }
  public function SetEtiketten_Sort($value) { $this->etiketten_sort=$value; }
  public function GetEtiketten_Sort() { return $this->etiketten_sort; }
  public function SetEanherstellerscanerlauben($value) { $this->eanherstellerscanerlauben=$value; }
  public function GetEanherstellerscanerlauben() { return $this->eanherstellerscanerlauben; }
  public function SetChargenerfassen($value) { $this->chargenerfassen=$value; }
  public function GetChargenerfassen() { return $this->chargenerfassen; }
  public function SetMhderfassen($value) { $this->mhderfassen=$value; }
  public function GetMhderfassen() { return $this->mhderfassen; }
  public function SetAutodruckrechnungstufe1($value) { $this->autodruckrechnungstufe1=$value; }
  public function GetAutodruckrechnungstufe1() { return $this->autodruckrechnungstufe1; }
  public function SetAutodruckrechnungstufe1Menge($value) { $this->autodruckrechnungstufe1menge=$value; }
  public function GetAutodruckrechnungstufe1Menge() { return $this->autodruckrechnungstufe1menge; }
  public function SetAutodruckrechnungstufe1Mail($value) { $this->autodruckrechnungstufe1mail=$value; }
  public function GetAutodruckrechnungstufe1Mail() { return $this->autodruckrechnungstufe1mail; }
  public function SetAutodruckkommissionierscheinstufe1($value) { $this->autodruckkommissionierscheinstufe1=$value; }
  public function GetAutodruckkommissionierscheinstufe1() { return $this->autodruckkommissionierscheinstufe1; }
  public function SetAutodruckkommissionierscheinstufe1Menge($value) { $this->autodruckkommissionierscheinstufe1menge=$value; }
  public function GetAutodruckkommissionierscheinstufe1Menge() { return $this->autodruckkommissionierscheinstufe1menge; }
  public function SetKasse_Bondrucker_Freifeld($value) { $this->kasse_bondrucker_freifeld=$value; }
  public function GetKasse_Bondrucker_Freifeld() { return $this->kasse_bondrucker_freifeld; }
  public function SetKasse_Bondrucker_Anzahl($value) { $this->kasse_bondrucker_anzahl=$value; }
  public function GetKasse_Bondrucker_Anzahl() { return $this->kasse_bondrucker_anzahl; }
  public function SetKasse_Rksv_Aktiv($value) { $this->kasse_rksv_aktiv=$value; }
  public function GetKasse_Rksv_Aktiv() { return $this->kasse_rksv_aktiv; }
  public function SetKasse_Rksv_Tool($value) { $this->kasse_rksv_tool=$value; }
  public function GetKasse_Rksv_Tool() { return $this->kasse_rksv_tool; }
  public function SetKasse_Rksv_Kartenleser($value) { $this->kasse_rksv_kartenleser=$value; }
  public function GetKasse_Rksv_Kartenleser() { return $this->kasse_rksv_kartenleser; }
  public function SetKasse_Rksv_Karteseriennummer($value) { $this->kasse_rksv_karteseriennummer=$value; }
  public function GetKasse_Rksv_Karteseriennummer() { return $this->kasse_rksv_karteseriennummer; }
  public function SetKasse_Rksv_Kartepin($value) { $this->kasse_rksv_kartepin=$value; }
  public function GetKasse_Rksv_Kartepin() { return $this->kasse_rksv_kartepin; }
  public function SetKasse_Rksv_Aeskey($value) { $this->kasse_rksv_aeskey=$value; }
  public function GetKasse_Rksv_Aeskey() { return $this->kasse_rksv_aeskey; }
  public function SetKasse_Rksv_Publiczertifikat($value) { $this->kasse_rksv_publiczertifikat=$value; }
  public function GetKasse_Rksv_Publiczertifikat() { return $this->kasse_rksv_publiczertifikat; }
  public function SetKasse_Rksv_Publiczertifikatkette($value) { $this->kasse_rksv_publiczertifikatkette=$value; }
  public function GetKasse_Rksv_Publiczertifikatkette() { return $this->kasse_rksv_publiczertifikatkette; }
  public function SetKasse_Rksv_Kassenid($value) { $this->kasse_rksv_kassenid=$value; }
  public function GetKasse_Rksv_Kassenid() { return $this->kasse_rksv_kassenid; }
  public function SetKasse_Gutschrift($value) { $this->kasse_gutschrift=$value; }
  public function GetKasse_Gutschrift() { return $this->kasse_gutschrift; }
  public function SetRechnungerzeugen($value) { $this->rechnungerzeugen=$value; }
  public function GetRechnungerzeugen() { return $this->rechnungerzeugen; }
  public function SetPos_Artikeltexteuebernehmen($value) { $this->pos_artikeltexteuebernehmen=$value; }
  public function GetPos_Artikeltexteuebernehmen() { return $this->pos_artikeltexteuebernehmen; }
  public function SetPos_Anzeigenetto($value) { $this->pos_anzeigenetto=$value; }
  public function GetPos_Anzeigenetto() { return $this->pos_anzeigenetto; }
  public function SetPos_Zwischenspeichern($value) { $this->pos_zwischenspeichern=$value; }
  public function GetPos_Zwischenspeichern() { return $this->pos_zwischenspeichern; }
  public function SetKasse_Button_Belegladen($value) { $this->kasse_button_belegladen=$value; }
  public function GetKasse_Button_Belegladen() { return $this->kasse_button_belegladen; }
  public function SetKasse_Button_Storno($value) { $this->kasse_button_storno=$value; }
  public function GetKasse_Button_Storno() { return $this->kasse_button_storno; }
  public function SetPos_Kundenalleprojekte($value) { $this->pos_kundenalleprojekte=$value; }
  public function GetPos_Kundenalleprojekte() { return $this->pos_kundenalleprojekte; }
  public function SetPos_Artikelnurausprojekt($value) { $this->pos_artikelnurausprojekt=$value; }
  public function GetPos_Artikelnurausprojekt() { return $this->pos_artikelnurausprojekt; }
  public function SetAllechargenmhd($value) { $this->allechargenmhd=$value; }
  public function GetAllechargenmhd() { return $this->allechargenmhd; }
  public function SetAnzeigesteuerbelege($value) { $this->anzeigesteuerbelege=$value; }
  public function GetAnzeigesteuerbelege() { return $this->anzeigesteuerbelege; }
  public function SetPos_Grosseansicht($value) { $this->pos_grosseansicht=$value; }
  public function GetPos_Grosseansicht() { return $this->pos_grosseansicht; }
  public function SetPreisberechnung($value) { $this->preisberechnung=$value; }
  public function GetPreisberechnung() { return $this->preisberechnung; }
  public function SetSteuernummer($value) { $this->steuernummer=$value; }
  public function GetSteuernummer() { return $this->steuernummer; }
  public function SetPaketmarkeautodrucken($value) { $this->paketmarkeautodrucken=$value; }
  public function GetPaketmarkeautodrucken() { return $this->paketmarkeautodrucken; }
  public function SetOrderpicking_Sort($value) { $this->orderpicking_sort=$value; }
  public function GetOrderpicking_Sort() { return $this->orderpicking_sort; }
  public function SetDeactivateautoshipping($value) { $this->deactivateautoshipping=$value; }
  public function GetDeactivateautoshipping() { return $this->deactivateautoshipping; }
  public function SetPos_Sumarticles($value) { $this->pos_sumarticles=$value; }
  public function GetPos_Sumarticles() { return $this->pos_sumarticles; }
  public function SetManualtracking($value) { $this->manualtracking=$value; }
  public function GetManualtracking() { return $this->manualtracking; }
  public function SetZahlungsweise($value) { $this->zahlungsweise=$value; }
  public function GetZahlungsweise() { return $this->zahlungsweise; }
  public function SetZahlungsweiselieferant($value) { $this->zahlungsweiselieferant=$value; }
  public function GetZahlungsweiselieferant() { return $this->zahlungsweiselieferant; }
  public function SetVersandart($value) { $this->versandart=$value; }
  public function GetVersandart() { return $this->versandart; }
  public function SetUps_Api_User($value) { $this->ups_api_user=$value; }
  public function GetUps_Api_User() { return $this->ups_api_user; }
  public function SetUps_Api_Password($value) { $this->ups_api_password=$value; }
  public function GetUps_Api_Password() { return $this->ups_api_password; }
  public function SetUps_Api_Key($value) { $this->ups_api_key=$value; }
  public function GetUps_Api_Key() { return $this->ups_api_key; }
  public function SetUps_Accountnumber($value) { $this->ups_accountnumber=$value; }
  public function GetUps_Accountnumber() { return $this->ups_accountnumber; }
  public function SetUps_Company_Name($value) { $this->ups_company_name=$value; }
  public function GetUps_Company_Name() { return $this->ups_company_name; }
  public function SetUps_Street_Name($value) { $this->ups_street_name=$value; }
  public function GetUps_Street_Name() { return $this->ups_street_name; }
  public function SetUps_Street_Number($value) { $this->ups_street_number=$value; }
  public function GetUps_Street_Number() { return $this->ups_street_number; }
  public function SetUps_Zip($value) { $this->ups_zip=$value; }
  public function GetUps_Zip() { return $this->ups_zip; }
  public function SetUps_Country($value) { $this->ups_country=$value; }
  public function GetUps_Country() { return $this->ups_country; }
  public function SetUps_City($value) { $this->ups_city=$value; }
  public function GetUps_City() { return $this->ups_city; }
  public function SetUps_Email($value) { $this->ups_email=$value; }
  public function GetUps_Email() { return $this->ups_email; }
  public function SetUps_Phone($value) { $this->ups_phone=$value; }
  public function GetUps_Phone() { return $this->ups_phone; }
  public function SetUps_Internet($value) { $this->ups_internet=$value; }
  public function GetUps_Internet() { return $this->ups_internet; }
  public function SetUps_Contact_Person($value) { $this->ups_contact_person=$value; }
  public function GetUps_Contact_Person() { return $this->ups_contact_person; }
  public function SetUps_Weightinkg($value) { $this->ups_WeightInKG=$value; }
  public function GetUps_Weightinkg() { return $this->ups_WeightInKG; }
  public function SetUps_Lengthincm($value) { $this->ups_LengthInCM=$value; }
  public function GetUps_Lengthincm() { return $this->ups_LengthInCM; }
  public function SetUps_Widthincm($value) { $this->ups_WidthInCM=$value; }
  public function GetUps_Widthincm() { return $this->ups_WidthInCM; }
  public function SetUps_Heightincm($value) { $this->ups_HeightInCM=$value; }
  public function GetUps_Heightincm() { return $this->ups_HeightInCM; }
  public function SetUps_Drucker($value) { $this->ups_drucker=$value; }
  public function GetUps_Drucker() { return $this->ups_drucker; }
  public function SetUps_Ausgabe($value) { $this->ups_ausgabe=$value; }
  public function GetUps_Ausgabe() { return $this->ups_ausgabe; }
  public function SetUps_Package_Code($value) { $this->ups_package_code=$value; }
  public function GetUps_Package_Code() { return $this->ups_package_code; }
  public function SetUps_Package_Description($value) { $this->ups_package_description=$value; }
  public function GetUps_Package_Description() { return $this->ups_package_description; }
  public function SetUps_Service_Code($value) { $this->ups_service_code=$value; }
  public function GetUps_Service_Code() { return $this->ups_service_code; }
  public function SetUps_Service_Description($value) { $this->ups_service_description=$value; }
  public function GetUps_Service_Description() { return $this->ups_service_description; }
  public function SetEmail_Html_Template($value) { $this->email_html_template=$value; }
  public function GetEmail_Html_Template() { return $this->email_html_template; }
  public function SetDruckanhang($value) { $this->druckanhang=$value; }
  public function GetDruckanhang() { return $this->druckanhang; }
  public function SetMailanhang($value) { $this->mailanhang=$value; }
  public function GetMailanhang() { return $this->mailanhang; }
  public function SetNext_Retoure($value) { $this->next_retoure=$value; }
  public function GetNext_Retoure() { return $this->next_retoure; }
  public function SetNext_Goodspostingdocument($value) { $this->next_goodspostingdocument=$value; }
  public function GetNext_Goodspostingdocument() { return $this->next_goodspostingdocument; }
  public function SetPos_Disable_Single_Entries($value) { $this->pos_disable_single_entries=$value; }
  public function GetPos_Disable_Single_Entries() { return $this->pos_disable_single_entries; }
  public function SetPos_Disable_Single_Day($value) { $this->pos_disable_single_day=$value; }
  public function GetPos_Disable_Single_Day() { return $this->pos_disable_single_day; }
  public function SetPos_Disable_Counting_Protocol($value) { $this->pos_disable_counting_protocol=$value; }
  public function GetPos_Disable_Counting_Protocol() { return $this->pos_disable_counting_protocol; }
  public function SetPos_Disable_Signature($value) { $this->pos_disable_signature=$value; }
  public function GetPos_Disable_Signature() { return $this->pos_disable_signature; }
  public function SetSteuer_Erloese_Inland_Normal($value) { $this->steuer_erloese_inland_normal=$value; }
  public function GetSteuer_Erloese_Inland_Normal() { return $this->steuer_erloese_inland_normal; }
  public function SetSteuer_Aufwendung_Inland_Normal($value) { $this->steuer_aufwendung_inland_normal=$value; }
  public function GetSteuer_Aufwendung_Inland_Normal() { return $this->steuer_aufwendung_inland_normal; }
  public function SetSteuer_Erloese_Inland_Ermaessigt($value) { $this->steuer_erloese_inland_ermaessigt=$value; }
  public function GetSteuer_Erloese_Inland_Ermaessigt() { return $this->steuer_erloese_inland_ermaessigt; }
  public function SetSteuer_Aufwendung_Inland_Ermaessigt($value) { $this->steuer_aufwendung_inland_ermaessigt=$value; }
  public function GetSteuer_Aufwendung_Inland_Ermaessigt() { return $this->steuer_aufwendung_inland_ermaessigt; }
  public function SetSteuer_Erloese_Inland_Nichtsteuerbar($value) { $this->steuer_erloese_inland_nichtsteuerbar=$value; }
  public function GetSteuer_Erloese_Inland_Nichtsteuerbar() { return $this->steuer_erloese_inland_nichtsteuerbar; }
  public function SetSteuer_Aufwendung_Inland_Nichtsteuerbar($value) { $this->steuer_aufwendung_inland_nichtsteuerbar=$value; }
  public function GetSteuer_Aufwendung_Inland_Nichtsteuerbar() { return $this->steuer_aufwendung_inland_nichtsteuerbar; }
  public function SetSteuer_Erloese_Inland_Innergemeinschaftlich($value) { $this->steuer_erloese_inland_innergemeinschaftlich=$value; }
  public function GetSteuer_Erloese_Inland_Innergemeinschaftlich() { return $this->steuer_erloese_inland_innergemeinschaftlich; }
  public function SetSteuer_Aufwendung_Inland_Innergemeinschaftlich($value) { $this->steuer_aufwendung_inland_innergemeinschaftlich=$value; }
  public function GetSteuer_Aufwendung_Inland_Innergemeinschaftlich() { return $this->steuer_aufwendung_inland_innergemeinschaftlich; }
  public function SetSteuer_Erloese_Inland_Eunormal($value) { $this->steuer_erloese_inland_eunormal=$value; }
  public function GetSteuer_Erloese_Inland_Eunormal() { return $this->steuer_erloese_inland_eunormal; }
  public function SetSteuer_Aufwendung_Inland_Eunormal($value) { $this->steuer_aufwendung_inland_eunormal=$value; }
  public function GetSteuer_Aufwendung_Inland_Eunormal() { return $this->steuer_aufwendung_inland_eunormal; }
  public function SetSteuer_Erloese_Inland_Euermaessigt($value) { $this->steuer_erloese_inland_euermaessigt=$value; }
  public function GetSteuer_Erloese_Inland_Euermaessigt() { return $this->steuer_erloese_inland_euermaessigt; }
  public function SetSteuer_Aufwendung_Inland_Euermaessigt($value) { $this->steuer_aufwendung_inland_euermaessigt=$value; }
  public function GetSteuer_Aufwendung_Inland_Euermaessigt() { return $this->steuer_aufwendung_inland_euermaessigt; }
  public function SetSteuer_Erloese_Inland_Export($value) { $this->steuer_erloese_inland_export=$value; }
  public function GetSteuer_Erloese_Inland_Export() { return $this->steuer_erloese_inland_export; }
  public function SetSteuer_Aufwendung_Inland_Import($value) { $this->steuer_aufwendung_inland_import=$value; }
  public function GetSteuer_Aufwendung_Inland_Import() { return $this->steuer_aufwendung_inland_import; }
  public function SetCreate_Proformainvoice($value) { $this->create_proformainvoice=$value; }
  public function GetCreate_Proformainvoice() { return $this->create_proformainvoice; }
  public function SetPrint_Proformainvoice($value) { $this->print_proformainvoice=$value; }
  public function GetPrint_Proformainvoice() { return $this->print_proformainvoice; }
  public function SetProformainvoice_Amount($value) { $this->proformainvoice_amount=$value; }
  public function GetProformainvoice_Amount() { return $this->proformainvoice_amount; }
  public function SetAnzeigesteuerbelegebestellung($value) { $this->anzeigesteuerbelegebestellung=$value; }
  public function GetAnzeigesteuerbelegebestellung() { return $this->anzeigesteuerbelegebestellung; }
  public function SetAutobestbeforebatch($value) { $this->autobestbeforebatch=$value; }
  public function GetAutobestbeforebatch() { return $this->autobestbeforebatch; }
  public function SetAllwaysautobestbeforebatch($value) { $this->allwaysautobestbeforebatch=$value; }
  public function GetAllwaysautobestbeforebatch() { return $this->allwaysautobestbeforebatch; }
  public function SetKommissionierlauflieferschein($value) { $this->kommissionierlauflieferschein=$value; }
  public function GetKommissionierlauflieferschein() { return $this->kommissionierlauflieferschein; }
  public function SetIntraship_Exportdrucker($value) { $this->intraship_exportdrucker=$value; }
  public function GetIntraship_Exportdrucker() { return $this->intraship_exportdrucker; }
  public function SetMultiorderpicking($value) { $this->multiorderpicking=$value; }
  public function GetMultiorderpicking() { return $this->multiorderpicking; }
  public function SetStandardlager($value) { $this->standardlager=$value; }
  public function GetStandardlager() { return $this->standardlager; }
  public function SetStandardlagerproduktion($value) { $this->standardlagerproduktion=$value; }
  public function GetStandardlagerproduktion() { return $this->standardlagerproduktion; }
  public function SetKlarna_Merchantid($value) { $this->klarna_merchantid=$value; }
  public function GetKlarna_Merchantid() { return $this->klarna_merchantid; }
  public function SetKlarna_Sharedsecret($value) { $this->klarna_sharedsecret=$value; }
  public function GetKlarna_Sharedsecret() { return $this->klarna_sharedsecret; }
  public function SetNurlagerartikel($value) { $this->nurlagerartikel=$value; }
  public function GetNurlagerartikel() { return $this->nurlagerartikel; }
  public function SetPaketmarkedrucken($value) { $this->paketmarkedrucken=$value; }
  public function GetPaketmarkedrucken() { return $this->paketmarkedrucken; }
  public function SetLieferscheinedrucken($value) { $this->lieferscheinedrucken=$value; }
  public function GetLieferscheinedrucken() { return $this->lieferscheinedrucken; }
  public function SetLieferscheinedruckenmenge($value) { $this->lieferscheinedruckenmenge=$value; }
  public function GetLieferscheinedruckenmenge() { return $this->lieferscheinedruckenmenge; }
  public function SetAuftragdrucken($value) { $this->auftragdrucken=$value; }
  public function GetAuftragdrucken() { return $this->auftragdrucken; }
  public function SetAuftragdruckenmenge($value) { $this->auftragdruckenmenge=$value; }
  public function GetAuftragdruckenmenge() { return $this->auftragdruckenmenge; }
  public function SetDruckennachtracking($value) { $this->druckennachtracking=$value; }
  public function GetDruckennachtracking() { return $this->druckennachtracking; }
  public function SetExportdruckrechnungstufe1($value) { $this->exportdruckrechnungstufe1=$value; }
  public function GetExportdruckrechnungstufe1() { return $this->exportdruckrechnungstufe1; }
  public function SetExportdruckrechnungstufe1Menge($value) { $this->exportdruckrechnungstufe1menge=$value; }
  public function GetExportdruckrechnungstufe1Menge() { return $this->exportdruckrechnungstufe1menge; }
  public function SetExportdruckrechnung($value) { $this->exportdruckrechnung=$value; }
  public function GetExportdruckrechnung() { return $this->exportdruckrechnung; }
  public function SetExportdruckrechnungmenge($value) { $this->exportdruckrechnungmenge=$value; }
  public function GetExportdruckrechnungmenge() { return $this->exportdruckrechnungmenge; }
  public function SetKommissionierlistestufe1($value) { $this->kommissionierlistestufe1=$value; }
  public function GetKommissionierlistestufe1() { return $this->kommissionierlistestufe1; }
  public function SetKommissionierlistestufe1Menge($value) { $this->kommissionierlistestufe1menge=$value; }
  public function GetKommissionierlistestufe1Menge() { return $this->kommissionierlistestufe1menge; }
  public function SetFremdnummerscanerlauben($value) { $this->fremdnummerscanerlauben=$value; }
  public function GetFremdnummerscanerlauben() { return $this->fremdnummerscanerlauben; }
  public function SetZvt100Url($value) { $this->zvt100url=$value; }
  public function GetZvt100Url() { return $this->zvt100url; }
  public function SetZvt100Port($value) { $this->zvt100port=$value; }
  public function GetZvt100Port() { return $this->zvt100port; }
  public function SetProduction_Show_Only_Needed_Storages($value) { $this->production_show_only_needed_storages=$value; }
  public function GetProduction_Show_Only_Needed_Storages() { return $this->production_show_only_needed_storages; }
  public function SetProduktion_Extra_Seiten($value) { $this->produktion_extra_seiten=$value; }
  public function GetProduktion_Extra_Seiten() { return $this->produktion_extra_seiten; }
  public function SetKasse_Button_Trinkgeldeckredit($value) { $this->kasse_button_trinkgeldeckredit=$value; }
  public function GetKasse_Button_Trinkgeldeckredit() { return $this->kasse_button_trinkgeldeckredit; }
  public function SetKasse_Autologout($value) { $this->kasse_autologout=$value; }
  public function GetKasse_Autologout() { return $this->kasse_autologout; }
  public function SetKasse_Autologout_Abschluss($value) { $this->kasse_autologout_abschluss=$value; }
  public function GetKasse_Autologout_Abschluss() { return $this->kasse_autologout_abschluss; }
  public function SetNext_Receiptdocument($value) { $this->next_receiptdocument=$value; }
  public function GetNext_Receiptdocument() { return $this->next_receiptdocument; }
  public function SetTaxfromdoctypesettings($value) { $this->taxfromdoctypesettings=$value; }
  public function GetTaxfromdoctypesettings() { return $this->taxfromdoctypesettings; }

}
