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

class ObjGenArtikel
{

  private  $id;
  private  $typ;
  private  $nummer;
  private  $checksum;
  private  $projekt;
  private  $inaktiv;
  private  $ausverkauft;
  private  $warengruppe;
  private  $name_de;
  private  $name_en;
  private  $kurztext_de;
  private  $kurztext_en;
  private  $beschreibung_de;
  private  $beschreibung_en;
  private  $uebersicht_de;
  private  $uebersicht_en;
  private  $links_de;
  private  $links_en;
  private  $startseite_de;
  private  $startseite_en;
  private  $standardbild;
  private  $herstellerlink;
  private  $hersteller;
  private  $teilbar;
  private  $nteile;
  private  $seriennummern;
  private  $lager_platz;
  private  $lieferzeit;
  private  $lieferzeitmanuell;
  private  $sonstiges;
  private  $gewicht;
  private  $endmontage;
  private  $funktionstest;
  private  $artikelcheckliste;
  private  $stueckliste;
  private  $juststueckliste;
  private  $barcode;
  private  $hinzugefuegt;
  private  $pcbdecal;
  private  $lagerartikel;
  private  $porto;
  private  $chargenverwaltung;
  private  $provisionsartikel;
  private  $gesperrt;
  private  $sperrgrund;
  private  $geloescht;
  private  $gueltigbis;
  private  $umsatzsteuer;
  private  $klasse;
  private  $adresse;
  private  $shopartikel;
  private  $unishopartikel;
  private  $journalshopartikel;
  private  $shop;
  private  $katalog;
  private  $katalogtext_de;
  private  $katalogtext_en;
  private  $katalogbezeichnung_de;
  private  $katalogbezeichnung_en;
  private  $neu;
  private  $topseller;
  private  $startseite;
  private  $wichtig;
  private  $mindestlager;
  private  $mindestbestellung;
  private  $partnerprogramm_sperre;
  private  $internerkommentar;
  private  $intern_gesperrt;
  private  $intern_gesperrtuser;
  private  $intern_gesperrtgrund;
  private  $inbearbeitung;
  private  $inbearbeitunguser;
  private  $cache_lagerplatzinhaltmenge;
  private  $internkommentar;
  private  $firma;
  private  $logdatei;
  private  $anabregs_text;
  private  $autobestellung;
  private  $produktion;
  private  $herstellernummer;
  private  $restmenge;
  private  $mlmdirektpraemie;
  private  $keineeinzelartikelanzeigen;
  private  $mindesthaltbarkeitsdatum;
  private  $letzteseriennummer;
  private  $individualartikel;
  private  $keinrabatterlaubt;
  private  $rabatt;
  private  $rabatt_prozent;
  private  $geraet;
  private  $serviceartikel;
  private  $autoabgleicherlaubt;
  private  $pseudopreis;
  private  $freigabenotwendig;
  private  $freigaberegel;
  private  $nachbestellt;
  private  $ean;
  private  $mlmpunkte;
  private  $mlmbonuspunkte;
  private  $mlmkeinepunkteeigenkauf;
  private  $shop2;
  private  $shop3;
  private  $usereditid;
  private  $useredittimestamp;
  private  $freifeld1;
  private  $freifeld2;
  private  $freifeld3;
  private  $freifeld4;
  private  $freifeld5;
  private  $freifeld6;
  private  $einheit;
  private  $webid;
  private  $lieferzeitmanuell_en;
  private  $variante;
  private  $variante_von;
  private  $produktioninfo;
  private  $sonderaktion;
  private  $sonderaktion_en;
  private  $autolagerlampe;
  private  $leerfeld;
  private  $zolltarifnummer;
  private  $herkunftsland;
  private  $laenge;
  private  $breite;
  private  $hoehe;
  private  $gebuehr;
  private  $pseudolager;
  private  $downloadartikel;
  private  $matrixprodukt;
  private  $steuer_erloese_inland_normal;
  private  $steuer_aufwendung_inland_normal;
  private  $steuer_erloese_inland_ermaessigt;
  private  $steuer_aufwendung_inland_ermaessigt;
  private  $steuer_erloese_inland_steuerfrei;
  private  $steuer_aufwendung_inland_steuerfrei;
  private  $steuer_erloese_inland_innergemeinschaftlich;
  private  $steuer_aufwendung_inland_innergemeinschaftlich;
  private  $steuer_erloese_inland_eunormal;
  private  $steuer_erloese_inland_nichtsteuerbar;
  private  $steuer_erloese_inland_euermaessigt;
  private  $steuer_aufwendung_inland_nichtsteuerbar;
  private  $steuer_aufwendung_inland_eunormal;
  private  $steuer_aufwendung_inland_euermaessigt;
  private  $steuer_erloese_inland_export;
  private  $steuer_aufwendung_inland_import;
  private  $steuer_art_produkt;
  private  $steuer_art_produkt_download;
  private  $metadescription_de;
  private  $metadescription_en;
  private  $metakeywords_de;
  private  $metakeywords_en;
  private  $anabregs_text_en;
  private  $externeproduktion;
  private  $bildvorschau;
  private  $inventursperre;
  private  $variante_kopie;
  private  $unikat;
  private  $generierenummerbeioption;
  private  $allelieferanten;
  private  $tagespreise;
  private  $rohstoffe;
  private  $xvp;
  private  $ohnepreisimpdf;
  private  $provisionssperre;
  private  $dienstleistung;
  private  $inventurekaktiv;
  private  $inventurek;
  private  $hinweis_einfuegen;
  private  $etikettautodruck;
  private  $lagerkorrekturwert;
  private  $autodrucketikett;
  private  $abckategorie;
  private  $laststorage_changed;
  private  $laststorage_sync;
  private  $steuersatz;
  private  $steuertext_innergemeinschaftlich;
  private  $steuertext_export;
  private  $formelmenge;
  private  $formelpreis;
  private  $freifeld7;
  private  $freifeld8;
  private  $freifeld9;
  private  $freifeld10;
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
  private  $freifeld21;
  private  $freifeld22;
  private  $freifeld23;
  private  $freifeld24;
  private  $freifeld25;
  private  $freifeld26;
  private  $freifeld27;
  private  $freifeld28;
  private  $freifeld29;
  private  $freifeld30;
  private  $freifeld31;
  private  $freifeld32;
  private  $freifeld33;
  private  $freifeld34;
  private  $freifeld35;
  private  $freifeld36;
  private  $freifeld37;
  private  $freifeld38;
  private  $freifeld39;
  private  $freifeld40;
  private  $ursprungsregion;
  private  $bestandalternativartikel;
  private  $metatitle_de;
  private  $metatitle_en;
  private  $vkmeldungunterdruecken;
  private  $altersfreigabe;
  private  $unikatbeikopie;
  private  $steuergruppe;
  private  $kostenstelle;
  private  $artikelautokalkulation;
  private  $artikelabschliessenkalkulation;
  private  $artikelfifokalkulation;
  private  $keinskonto;
  private  $berechneterek;
  private  $verwendeberechneterek;
  private  $berechneterekwaehrung;
  private  $has_preproduced_partlist;
  private  $preproduced_partlist;
  private  $nettogewicht;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `artikel` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->typ=$result['typ'];
    $this->nummer=$result['nummer'];
    $this->checksum=$result['checksum'];
    $this->projekt=$result['projekt'];
    $this->inaktiv=$result['inaktiv'];
    $this->ausverkauft=$result['ausverkauft'];
    $this->warengruppe=$result['warengruppe'];
    $this->name_de=$result['name_de'];
    $this->name_en=$result['name_en'];
    $this->kurztext_de=$result['kurztext_de'];
    $this->kurztext_en=$result['kurztext_en'];
    $this->beschreibung_de=$result['beschreibung_de'];
    $this->beschreibung_en=$result['beschreibung_en'];
    $this->uebersicht_de=$result['uebersicht_de'];
    $this->uebersicht_en=$result['uebersicht_en'];
    $this->links_de=$result['links_de'];
    $this->links_en=$result['links_en'];
    $this->startseite_de=$result['startseite_de'];
    $this->startseite_en=$result['startseite_en'];
    $this->standardbild=$result['standardbild'];
    $this->herstellerlink=$result['herstellerlink'];
    $this->hersteller=$result['hersteller'];
    $this->teilbar=$result['teilbar'];
    $this->nteile=$result['nteile'];
    $this->seriennummern=$result['seriennummern'];
    $this->lager_platz=$result['lager_platz'];
    $this->lieferzeit=$result['lieferzeit'];
    $this->lieferzeitmanuell=$result['lieferzeitmanuell'];
    $this->sonstiges=$result['sonstiges'];
    $this->gewicht=$result['gewicht'];
    $this->endmontage=$result['endmontage'];
    $this->funktionstest=$result['funktionstest'];
    $this->artikelcheckliste=$result['artikelcheckliste'];
    $this->stueckliste=$result['stueckliste'];
    $this->juststueckliste=$result['juststueckliste'];
    $this->barcode=$result['barcode'];
    $this->hinzugefuegt=$result['hinzugefuegt'];
    $this->pcbdecal=$result['pcbdecal'];
    $this->lagerartikel=$result['lagerartikel'];
    $this->porto=$result['porto'];
    $this->chargenverwaltung=$result['chargenverwaltung'];
    $this->provisionsartikel=$result['provisionsartikel'];
    $this->gesperrt=$result['gesperrt'];
    $this->sperrgrund=$result['sperrgrund'];
    $this->geloescht=$result['geloescht'];
    $this->gueltigbis=$result['gueltigbis'];
    $this->umsatzsteuer=$result['umsatzsteuer'];
    $this->klasse=$result['klasse'];
    $this->adresse=$result['adresse'];
    $this->shopartikel=$result['shopartikel'];
    $this->unishopartikel=$result['unishopartikel'];
    $this->journalshopartikel=$result['journalshopartikel'];
    $this->shop=$result['shop'];
    $this->katalog=$result['katalog'];
    $this->katalogtext_de=$result['katalogtext_de'];
    $this->katalogtext_en=$result['katalogtext_en'];
    $this->katalogbezeichnung_de=$result['katalogbezeichnung_de'];
    $this->katalogbezeichnung_en=$result['katalogbezeichnung_en'];
    $this->neu=$result['neu'];
    $this->topseller=$result['topseller'];
    $this->startseite=$result['startseite'];
    $this->wichtig=$result['wichtig'];
    $this->mindestlager=$result['mindestlager'];
    $this->mindestbestellung=$result['mindestbestellung'];
    $this->partnerprogramm_sperre=$result['partnerprogramm_sperre'];
    $this->internerkommentar=$result['internerkommentar'];
    $this->intern_gesperrt=$result['intern_gesperrt'];
    $this->intern_gesperrtuser=$result['intern_gesperrtuser'];
    $this->intern_gesperrtgrund=$result['intern_gesperrtgrund'];
    $this->inbearbeitung=$result['inbearbeitung'];
    $this->inbearbeitunguser=$result['inbearbeitunguser'];
    $this->cache_lagerplatzinhaltmenge=$result['cache_lagerplatzinhaltmenge'];
    $this->internkommentar=$result['internkommentar'];
    $this->firma=$result['firma'];
    $this->logdatei=$result['logdatei'];
    $this->anabregs_text=$result['anabregs_text'];
    $this->autobestellung=$result['autobestellung'];
    $this->produktion=$result['produktion'];
    $this->herstellernummer=$result['herstellernummer'];
    $this->restmenge=$result['restmenge'];
    $this->mlmdirektpraemie=$result['mlmdirektpraemie'];
    $this->keineeinzelartikelanzeigen=$result['keineeinzelartikelanzeigen'];
    $this->mindesthaltbarkeitsdatum=$result['mindesthaltbarkeitsdatum'];
    $this->letzteseriennummer=$result['letzteseriennummer'];
    $this->individualartikel=$result['individualartikel'];
    $this->keinrabatterlaubt=$result['keinrabatterlaubt'];
    $this->rabatt=$result['rabatt'];
    $this->rabatt_prozent=$result['rabatt_prozent'];
    $this->geraet=$result['geraet'];
    $this->serviceartikel=$result['serviceartikel'];
    $this->autoabgleicherlaubt=$result['autoabgleicherlaubt'];
    $this->pseudopreis=$result['pseudopreis'];
    $this->freigabenotwendig=$result['freigabenotwendig'];
    $this->freigaberegel=$result['freigaberegel'];
    $this->nachbestellt=$result['nachbestellt'];
    $this->ean=$result['ean'];
    $this->mlmpunkte=$result['mlmpunkte'];
    $this->mlmbonuspunkte=$result['mlmbonuspunkte'];
    $this->mlmkeinepunkteeigenkauf=$result['mlmkeinepunkteeigenkauf'];
    $this->shop2=$result['shop2'];
    $this->shop3=$result['shop3'];
    $this->usereditid=$result['usereditid'];
    $this->useredittimestamp=$result['useredittimestamp'];
    $this->freifeld1=$result['freifeld1'];
    $this->freifeld2=$result['freifeld2'];
    $this->freifeld3=$result['freifeld3'];
    $this->freifeld4=$result['freifeld4'];
    $this->freifeld5=$result['freifeld5'];
    $this->freifeld6=$result['freifeld6'];
    $this->einheit=$result['einheit'];
    $this->webid=$result['webid'];
    $this->lieferzeitmanuell_en=$result['lieferzeitmanuell_en'];
    $this->variante=$result['variante'];
    $this->variante_von=$result['variante_von'];
    $this->produktioninfo=$result['produktioninfo'];
    $this->sonderaktion=$result['sonderaktion'];
    $this->sonderaktion_en=$result['sonderaktion_en'];
    $this->autolagerlampe=$result['autolagerlampe'];
    $this->leerfeld=$result['leerfeld'];
    $this->zolltarifnummer=$result['zolltarifnummer'];
    $this->herkunftsland=$result['herkunftsland'];
    $this->laenge=$result['laenge'];
    $this->breite=$result['breite'];
    $this->hoehe=$result['hoehe'];
    $this->gebuehr=$result['gebuehr'];
    $this->pseudolager=$result['pseudolager'];
    $this->downloadartikel=$result['downloadartikel'];
    $this->matrixprodukt=$result['matrixprodukt'];
    $this->steuer_erloese_inland_normal=$result['steuer_erloese_inland_normal'];
    $this->steuer_aufwendung_inland_normal=$result['steuer_aufwendung_inland_normal'];
    $this->steuer_erloese_inland_ermaessigt=$result['steuer_erloese_inland_ermaessigt'];
    $this->steuer_aufwendung_inland_ermaessigt=$result['steuer_aufwendung_inland_ermaessigt'];
    $this->steuer_erloese_inland_steuerfrei=$result['steuer_erloese_inland_steuerfrei'];
    $this->steuer_aufwendung_inland_steuerfrei=$result['steuer_aufwendung_inland_steuerfrei'];
    $this->steuer_erloese_inland_innergemeinschaftlich=$result['steuer_erloese_inland_innergemeinschaftlich'];
    $this->steuer_aufwendung_inland_innergemeinschaftlich=$result['steuer_aufwendung_inland_innergemeinschaftlich'];
    $this->steuer_erloese_inland_eunormal=$result['steuer_erloese_inland_eunormal'];
    $this->steuer_erloese_inland_nichtsteuerbar=$result['steuer_erloese_inland_nichtsteuerbar'];
    $this->steuer_erloese_inland_euermaessigt=$result['steuer_erloese_inland_euermaessigt'];
    $this->steuer_aufwendung_inland_nichtsteuerbar=$result['steuer_aufwendung_inland_nichtsteuerbar'];
    $this->steuer_aufwendung_inland_eunormal=$result['steuer_aufwendung_inland_eunormal'];
    $this->steuer_aufwendung_inland_euermaessigt=$result['steuer_aufwendung_inland_euermaessigt'];
    $this->steuer_erloese_inland_export=$result['steuer_erloese_inland_export'];
    $this->steuer_aufwendung_inland_import=$result['steuer_aufwendung_inland_import'];
    $this->steuer_art_produkt=$result['steuer_art_produkt'];
    $this->steuer_art_produkt_download=$result['steuer_art_produkt_download'];
    $this->metadescription_de=$result['metadescription_de'];
    $this->metadescription_en=$result['metadescription_en'];
    $this->metakeywords_de=$result['metakeywords_de'];
    $this->metakeywords_en=$result['metakeywords_en'];
    $this->anabregs_text_en=$result['anabregs_text_en'];
    $this->externeproduktion=$result['externeproduktion'];
    $this->bildvorschau=$result['bildvorschau'];
    $this->inventursperre=$result['inventursperre'];
    $this->variante_kopie=$result['variante_kopie'];
    $this->unikat=$result['unikat'];
    $this->generierenummerbeioption=$result['generierenummerbeioption'];
    $this->allelieferanten=$result['allelieferanten'];
    $this->tagespreise=$result['tagespreise'];
    $this->rohstoffe=$result['rohstoffe'];
    $this->xvp=$result['xvp'];
    $this->ohnepreisimpdf=$result['ohnepreisimpdf'];
    $this->provisionssperre=$result['provisionssperre'];
    $this->dienstleistung=$result['dienstleistung'];
    $this->inventurekaktiv=$result['inventurekaktiv'];
    $this->inventurek=$result['inventurek'];
    $this->hinweis_einfuegen=$result['hinweis_einfuegen'];
    $this->etikettautodruck=$result['etikettautodruck'];
    $this->lagerkorrekturwert=$result['lagerkorrekturwert'];
    $this->autodrucketikett=$result['autodrucketikett'];
    $this->abckategorie=$result['abckategorie'];
    $this->laststorage_changed=$result['laststorage_changed'];
    $this->laststorage_sync=$result['laststorage_sync'];
    $this->steuersatz=$result['steuersatz'];
    $this->steuertext_innergemeinschaftlich=$result['steuertext_innergemeinschaftlich'];
    $this->steuertext_export=$result['steuertext_export'];
    $this->formelmenge=$result['formelmenge'];
    $this->formelpreis=$result['formelpreis'];
    $this->freifeld7=$result['freifeld7'];
    $this->freifeld8=$result['freifeld8'];
    $this->freifeld9=$result['freifeld9'];
    $this->freifeld10=$result['freifeld10'];
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
    $this->freifeld21=$result['freifeld21'];
    $this->freifeld22=$result['freifeld22'];
    $this->freifeld23=$result['freifeld23'];
    $this->freifeld24=$result['freifeld24'];
    $this->freifeld25=$result['freifeld25'];
    $this->freifeld26=$result['freifeld26'];
    $this->freifeld27=$result['freifeld27'];
    $this->freifeld28=$result['freifeld28'];
    $this->freifeld29=$result['freifeld29'];
    $this->freifeld30=$result['freifeld30'];
    $this->freifeld31=$result['freifeld31'];
    $this->freifeld32=$result['freifeld32'];
    $this->freifeld33=$result['freifeld33'];
    $this->freifeld34=$result['freifeld34'];
    $this->freifeld35=$result['freifeld35'];
    $this->freifeld36=$result['freifeld36'];
    $this->freifeld37=$result['freifeld37'];
    $this->freifeld38=$result['freifeld38'];
    $this->freifeld39=$result['freifeld39'];
    $this->freifeld40=$result['freifeld40'];
    $this->ursprungsregion=$result['ursprungsregion'];
    $this->bestandalternativartikel=$result['bestandalternativartikel'];
    $this->metatitle_de=$result['metatitle_de'];
    $this->metatitle_en=$result['metatitle_en'];
    $this->vkmeldungunterdruecken=$result['vkmeldungunterdruecken'];
    $this->altersfreigabe=$result['altersfreigabe'];
    $this->unikatbeikopie=$result['unikatbeikopie'];
    $this->steuergruppe=$result['steuergruppe'];
    $this->kostenstelle=$result['kostenstelle'];
    $this->artikelautokalkulation=$result['artikelautokalkulation'];
    $this->artikelabschliessenkalkulation=$result['artikelabschliessenkalkulation'];
    $this->artikelfifokalkulation=$result['artikelfifokalkulation'];
    $this->keinskonto=$result['keinskonto'];
    $this->berechneterek=$result['berechneterek'];
    $this->verwendeberechneterek=$result['verwendeberechneterek'];
    $this->berechneterekwaehrung=$result['berechneterekwaehrung'];
    $this->has_preproduced_partlist=$result['has_preproduced_partlist'];
    $this->preproduced_partlist=$result['preproduced_partlist'];
    $this->nettogewicht=$result['nettogewicht'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `artikel` (`id`,`typ`,`nummer`,`checksum`,`projekt`,`inaktiv`,`ausverkauft`,`warengruppe`,`name_de`,`name_en`,`kurztext_de`,`kurztext_en`,`beschreibung_de`,`beschreibung_en`,`uebersicht_de`,`uebersicht_en`,`links_de`,`links_en`,`startseite_de`,`startseite_en`,`standardbild`,`herstellerlink`,`hersteller`,`teilbar`,`nteile`,`seriennummern`,`lager_platz`,`lieferzeit`,`lieferzeitmanuell`,`sonstiges`,`gewicht`,`endmontage`,`funktionstest`,`artikelcheckliste`,`stueckliste`,`juststueckliste`,`barcode`,`hinzugefuegt`,`pcbdecal`,`lagerartikel`,`porto`,`chargenverwaltung`,`provisionsartikel`,`gesperrt`,`sperrgrund`,`geloescht`,`gueltigbis`,`umsatzsteuer`,`klasse`,`adresse`,`shopartikel`,`unishopartikel`,`journalshopartikel`,`shop`,`katalog`,`katalogtext_de`,`katalogtext_en`,`katalogbezeichnung_de`,`katalogbezeichnung_en`,`neu`,`topseller`,`startseite`,`wichtig`,`mindestlager`,`mindestbestellung`,`partnerprogramm_sperre`,`internerkommentar`,`intern_gesperrt`,`intern_gesperrtuser`,`intern_gesperrtgrund`,`inbearbeitung`,`inbearbeitunguser`,`cache_lagerplatzinhaltmenge`,`internkommentar`,`firma`,`logdatei`,`anabregs_text`,`autobestellung`,`produktion`,`herstellernummer`,`restmenge`,`mlmdirektpraemie`,`keineeinzelartikelanzeigen`,`mindesthaltbarkeitsdatum`,`letzteseriennummer`,`individualartikel`,`keinrabatterlaubt`,`rabatt`,`rabatt_prozent`,`geraet`,`serviceartikel`,`autoabgleicherlaubt`,`pseudopreis`,`freigabenotwendig`,`freigaberegel`,`nachbestellt`,`ean`,`mlmpunkte`,`mlmbonuspunkte`,`mlmkeinepunkteeigenkauf`,`shop2`,`shop3`,`usereditid`,`useredittimestamp`,`freifeld1`,`freifeld2`,`freifeld3`,`freifeld4`,`freifeld5`,`freifeld6`,`einheit`,`webid`,`lieferzeitmanuell_en`,`variante`,`variante_von`,`produktioninfo`,`sonderaktion`,`sonderaktion_en`,`autolagerlampe`,`leerfeld`,`zolltarifnummer`,`herkunftsland`,`laenge`,`breite`,`hoehe`,`gebuehr`,`pseudolager`,`downloadartikel`,`matrixprodukt`,`steuer_erloese_inland_normal`,`steuer_aufwendung_inland_normal`,`steuer_erloese_inland_ermaessigt`,`steuer_aufwendung_inland_ermaessigt`,`steuer_erloese_inland_steuerfrei`,`steuer_aufwendung_inland_steuerfrei`,`steuer_erloese_inland_innergemeinschaftlich`,`steuer_aufwendung_inland_innergemeinschaftlich`,`steuer_erloese_inland_eunormal`,`steuer_erloese_inland_nichtsteuerbar`,`steuer_erloese_inland_euermaessigt`,`steuer_aufwendung_inland_nichtsteuerbar`,`steuer_aufwendung_inland_eunormal`,`steuer_aufwendung_inland_euermaessigt`,`steuer_erloese_inland_export`,`steuer_aufwendung_inland_import`,`steuer_art_produkt`,`steuer_art_produkt_download`,`metadescription_de`,`metadescription_en`,`metakeywords_de`,`metakeywords_en`,`anabregs_text_en`,`externeproduktion`,`bildvorschau`,`inventursperre`,`variante_kopie`,`unikat`,`generierenummerbeioption`,`allelieferanten`,`tagespreise`,`rohstoffe`,`xvp`,`ohnepreisimpdf`,`provisionssperre`,`dienstleistung`,`inventurekaktiv`,`inventurek`,`hinweis_einfuegen`,`etikettautodruck`,`lagerkorrekturwert`,`autodrucketikett`,`abckategorie`,`laststorage_changed`,`laststorage_sync`,`steuersatz`,`steuertext_innergemeinschaftlich`,`steuertext_export`,`formelmenge`,`formelpreis`,`freifeld7`,`freifeld8`,`freifeld9`,`freifeld10`,`freifeld11`,`freifeld12`,`freifeld13`,`freifeld14`,`freifeld15`,`freifeld16`,`freifeld17`,`freifeld18`,`freifeld19`,`freifeld20`,`freifeld21`,`freifeld22`,`freifeld23`,`freifeld24`,`freifeld25`,`freifeld26`,`freifeld27`,`freifeld28`,`freifeld29`,`freifeld30`,`freifeld31`,`freifeld32`,`freifeld33`,`freifeld34`,`freifeld35`,`freifeld36`,`freifeld37`,`freifeld38`,`freifeld39`,`freifeld40`,`ursprungsregion`,`bestandalternativartikel`,`metatitle_de`,`metatitle_en`,`vkmeldungunterdruecken`,`altersfreigabe`,`unikatbeikopie`,`steuergruppe`,`kostenstelle`,`artikelautokalkulation`,`artikelabschliessenkalkulation`,`artikelfifokalkulation`,`keinskonto`,`berechneterek`,`verwendeberechneterek`,`berechneterekwaehrung`,`has_preproduced_partlist`,`preproduced_partlist`,`nettogewicht`)
      VALUES(NULL,'{$this->typ}','{$this->nummer}','{$this->checksum}','{$this->projekt}','{$this->inaktiv}','{$this->ausverkauft}','{$this->warengruppe}','{$this->name_de}','{$this->name_en}','{$this->kurztext_de}','{$this->kurztext_en}','{$this->beschreibung_de}','{$this->beschreibung_en}','{$this->uebersicht_de}','{$this->uebersicht_en}','{$this->links_de}','{$this->links_en}','{$this->startseite_de}','{$this->startseite_en}','{$this->standardbild}','{$this->herstellerlink}','{$this->hersteller}','{$this->teilbar}','{$this->nteile}','{$this->seriennummern}','{$this->lager_platz}','{$this->lieferzeit}','{$this->lieferzeitmanuell}','{$this->sonstiges}','{$this->gewicht}','{$this->endmontage}','{$this->funktionstest}','{$this->artikelcheckliste}','{$this->stueckliste}','{$this->juststueckliste}','{$this->barcode}','{$this->hinzugefuegt}','{$this->pcbdecal}','{$this->lagerartikel}','{$this->porto}','{$this->chargenverwaltung}','{$this->provisionsartikel}','{$this->gesperrt}','{$this->sperrgrund}','{$this->geloescht}','{$this->gueltigbis}','{$this->umsatzsteuer}','{$this->klasse}','{$this->adresse}','{$this->shopartikel}','{$this->unishopartikel}','{$this->journalshopartikel}','{$this->shop}','{$this->katalog}','{$this->katalogtext_de}','{$this->katalogtext_en}','{$this->katalogbezeichnung_de}','{$this->katalogbezeichnung_en}','{$this->neu}','{$this->topseller}','{$this->startseite}','{$this->wichtig}','{$this->mindestlager}','{$this->mindestbestellung}','{$this->partnerprogramm_sperre}','{$this->internerkommentar}','{$this->intern_gesperrt}','{$this->intern_gesperrtuser}','{$this->intern_gesperrtgrund}','{$this->inbearbeitung}','{$this->inbearbeitunguser}','{$this->cache_lagerplatzinhaltmenge}','{$this->internkommentar}','{$this->firma}','{$this->logdatei}','{$this->anabregs_text}','{$this->autobestellung}','{$this->produktion}','{$this->herstellernummer}','{$this->restmenge}','{$this->mlmdirektpraemie}','{$this->keineeinzelartikelanzeigen}','{$this->mindesthaltbarkeitsdatum}','{$this->letzteseriennummer}','{$this->individualartikel}','{$this->keinrabatterlaubt}','{$this->rabatt}','{$this->rabatt_prozent}','{$this->geraet}','{$this->serviceartikel}','{$this->autoabgleicherlaubt}','{$this->pseudopreis}','{$this->freigabenotwendig}','{$this->freigaberegel}','{$this->nachbestellt}','{$this->ean}','{$this->mlmpunkte}','{$this->mlmbonuspunkte}','{$this->mlmkeinepunkteeigenkauf}','{$this->shop2}','{$this->shop3}','{$this->usereditid}','{$this->useredittimestamp}','{$this->freifeld1}','{$this->freifeld2}','{$this->freifeld3}','{$this->freifeld4}','{$this->freifeld5}','{$this->freifeld6}','{$this->einheit}','{$this->webid}','{$this->lieferzeitmanuell_en}','{$this->variante}','{$this->variante_von}','{$this->produktioninfo}','{$this->sonderaktion}','{$this->sonderaktion_en}','{$this->autolagerlampe}','{$this->leerfeld}','{$this->zolltarifnummer}','{$this->herkunftsland}','{$this->laenge}','{$this->breite}','{$this->hoehe}','{$this->gebuehr}','{$this->pseudolager}','{$this->downloadartikel}','{$this->matrixprodukt}','{$this->steuer_erloese_inland_normal}','{$this->steuer_aufwendung_inland_normal}','{$this->steuer_erloese_inland_ermaessigt}','{$this->steuer_aufwendung_inland_ermaessigt}','{$this->steuer_erloese_inland_steuerfrei}','{$this->steuer_aufwendung_inland_steuerfrei}','{$this->steuer_erloese_inland_innergemeinschaftlich}','{$this->steuer_aufwendung_inland_innergemeinschaftlich}','{$this->steuer_erloese_inland_eunormal}','{$this->steuer_erloese_inland_nichtsteuerbar}','{$this->steuer_erloese_inland_euermaessigt}','{$this->steuer_aufwendung_inland_nichtsteuerbar}','{$this->steuer_aufwendung_inland_eunormal}','{$this->steuer_aufwendung_inland_euermaessigt}','{$this->steuer_erloese_inland_export}','{$this->steuer_aufwendung_inland_import}','{$this->steuer_art_produkt}','{$this->steuer_art_produkt_download}','{$this->metadescription_de}','{$this->metadescription_en}','{$this->metakeywords_de}','{$this->metakeywords_en}','{$this->anabregs_text_en}','{$this->externeproduktion}','{$this->bildvorschau}','{$this->inventursperre}','{$this->variante_kopie}','{$this->unikat}','{$this->generierenummerbeioption}','{$this->allelieferanten}','{$this->tagespreise}','{$this->rohstoffe}','{$this->xvp}','{$this->ohnepreisimpdf}','{$this->provisionssperre}','{$this->dienstleistung}','{$this->inventurekaktiv}','{$this->inventurek}','{$this->hinweis_einfuegen}','{$this->etikettautodruck}','{$this->lagerkorrekturwert}','{$this->autodrucketikett}','{$this->abckategorie}','{$this->laststorage_changed}','{$this->laststorage_sync}','{$this->steuersatz}','{$this->steuertext_innergemeinschaftlich}','{$this->steuertext_export}','{$this->formelmenge}','{$this->formelpreis}','{$this->freifeld7}','{$this->freifeld8}','{$this->freifeld9}','{$this->freifeld10}','{$this->freifeld11}','{$this->freifeld12}','{$this->freifeld13}','{$this->freifeld14}','{$this->freifeld15}','{$this->freifeld16}','{$this->freifeld17}','{$this->freifeld18}','{$this->freifeld19}','{$this->freifeld20}','{$this->freifeld21}','{$this->freifeld22}','{$this->freifeld23}','{$this->freifeld24}','{$this->freifeld25}','{$this->freifeld26}','{$this->freifeld27}','{$this->freifeld28}','{$this->freifeld29}','{$this->freifeld30}','{$this->freifeld31}','{$this->freifeld32}','{$this->freifeld33}','{$this->freifeld34}','{$this->freifeld35}','{$this->freifeld36}','{$this->freifeld37}','{$this->freifeld38}','{$this->freifeld39}','{$this->freifeld40}','{$this->ursprungsregion}','{$this->bestandalternativartikel}','{$this->metatitle_de}','{$this->metatitle_en}','{$this->vkmeldungunterdruecken}','{$this->altersfreigabe}','{$this->unikatbeikopie}','{$this->steuergruppe}','{$this->kostenstelle}','{$this->artikelautokalkulation}','{$this->artikelabschliessenkalkulation}','{$this->artikelfifokalkulation}','{$this->keinskonto}','{$this->berechneterek}','{$this->verwendeberechneterek}','{$this->berechneterekwaehrung}','{$this->has_preproduced_partlist}','{$this->preproduced_partlist}','{$this->nettogewicht}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `artikel` SET
      `typ`='{$this->typ}',
      `nummer`='{$this->nummer}',
      `checksum`='{$this->checksum}',
      `projekt`='{$this->projekt}',
      `inaktiv`='{$this->inaktiv}',
      `ausverkauft`='{$this->ausverkauft}',
      `warengruppe`='{$this->warengruppe}',
      `name_de`='{$this->name_de}',
      `name_en`='{$this->name_en}',
      `kurztext_de`='{$this->kurztext_de}',
      `kurztext_en`='{$this->kurztext_en}',
      `beschreibung_de`='{$this->beschreibung_de}',
      `beschreibung_en`='{$this->beschreibung_en}',
      `uebersicht_de`='{$this->uebersicht_de}',
      `uebersicht_en`='{$this->uebersicht_en}',
      `links_de`='{$this->links_de}',
      `links_en`='{$this->links_en}',
      `startseite_de`='{$this->startseite_de}',
      `startseite_en`='{$this->startseite_en}',
      `standardbild`='{$this->standardbild}',
      `herstellerlink`='{$this->herstellerlink}',
      `hersteller`='{$this->hersteller}',
      `teilbar`='{$this->teilbar}',
      `nteile`='{$this->nteile}',
      `seriennummern`='{$this->seriennummern}',
      `lager_platz`='{$this->lager_platz}',
      `lieferzeit`='{$this->lieferzeit}',
      `lieferzeitmanuell`='{$this->lieferzeitmanuell}',
      `sonstiges`='{$this->sonstiges}',
      `gewicht`='{$this->gewicht}',
      `endmontage`='{$this->endmontage}',
      `funktionstest`='{$this->funktionstest}',
      `artikelcheckliste`='{$this->artikelcheckliste}',
      `stueckliste`='{$this->stueckliste}',
      `juststueckliste`='{$this->juststueckliste}',
      `barcode`='{$this->barcode}',
      `hinzugefuegt`='{$this->hinzugefuegt}',
      `pcbdecal`='{$this->pcbdecal}',
      `lagerartikel`='{$this->lagerartikel}',
      `porto`='{$this->porto}',
      `chargenverwaltung`='{$this->chargenverwaltung}',
      `provisionsartikel`='{$this->provisionsartikel}',
      `gesperrt`='{$this->gesperrt}',
      `sperrgrund`='{$this->sperrgrund}',
      `geloescht`='{$this->geloescht}',
      `gueltigbis`='{$this->gueltigbis}',
      `umsatzsteuer`='{$this->umsatzsteuer}',
      `klasse`='{$this->klasse}',
      `adresse`='{$this->adresse}',
      `shopartikel`='{$this->shopartikel}',
      `unishopartikel`='{$this->unishopartikel}',
      `journalshopartikel`='{$this->journalshopartikel}',
      `shop`='{$this->shop}',
      `katalog`='{$this->katalog}',
      `katalogtext_de`='{$this->katalogtext_de}',
      `katalogtext_en`='{$this->katalogtext_en}',
      `katalogbezeichnung_de`='{$this->katalogbezeichnung_de}',
      `katalogbezeichnung_en`='{$this->katalogbezeichnung_en}',
      `neu`='{$this->neu}',
      `topseller`='{$this->topseller}',
      `startseite`='{$this->startseite}',
      `wichtig`='{$this->wichtig}',
      `mindestlager`='{$this->mindestlager}',
      `mindestbestellung`='{$this->mindestbestellung}',
      `partnerprogramm_sperre`='{$this->partnerprogramm_sperre}',
      `internerkommentar`='{$this->internerkommentar}',
      `intern_gesperrt`='{$this->intern_gesperrt}',
      `intern_gesperrtuser`='{$this->intern_gesperrtuser}',
      `intern_gesperrtgrund`='{$this->intern_gesperrtgrund}',
      `inbearbeitung`='{$this->inbearbeitung}',
      `inbearbeitunguser`='{$this->inbearbeitunguser}',
      `cache_lagerplatzinhaltmenge`='{$this->cache_lagerplatzinhaltmenge}',
      `internkommentar`='{$this->internkommentar}',
      `firma`='{$this->firma}',
      `logdatei`='{$this->logdatei}',
      `anabregs_text`='{$this->anabregs_text}',
      `autobestellung`='{$this->autobestellung}',
      `produktion`='{$this->produktion}',
      `herstellernummer`='{$this->herstellernummer}',
      `restmenge`='{$this->restmenge}',
      `mlmdirektpraemie`='{$this->mlmdirektpraemie}',
      `keineeinzelartikelanzeigen`='{$this->keineeinzelartikelanzeigen}',
      `mindesthaltbarkeitsdatum`='{$this->mindesthaltbarkeitsdatum}',
      `letzteseriennummer`='{$this->letzteseriennummer}',
      `individualartikel`='{$this->individualartikel}',
      `keinrabatterlaubt`='{$this->keinrabatterlaubt}',
      `rabatt`='{$this->rabatt}',
      `rabatt_prozent`='{$this->rabatt_prozent}',
      `geraet`='{$this->geraet}',
      `serviceartikel`='{$this->serviceartikel}',
      `autoabgleicherlaubt`='{$this->autoabgleicherlaubt}',
      `pseudopreis`='{$this->pseudopreis}',
      `freigabenotwendig`='{$this->freigabenotwendig}',
      `freigaberegel`='{$this->freigaberegel}',
      `nachbestellt`='{$this->nachbestellt}',
      `ean`='{$this->ean}',
      `mlmpunkte`='{$this->mlmpunkte}',
      `mlmbonuspunkte`='{$this->mlmbonuspunkte}',
      `mlmkeinepunkteeigenkauf`='{$this->mlmkeinepunkteeigenkauf}',
      `shop2`='{$this->shop2}',
      `shop3`='{$this->shop3}',
      `usereditid`='{$this->usereditid}',
      `useredittimestamp`='{$this->useredittimestamp}',
      `freifeld1`='{$this->freifeld1}',
      `freifeld2`='{$this->freifeld2}',
      `freifeld3`='{$this->freifeld3}',
      `freifeld4`='{$this->freifeld4}',
      `freifeld5`='{$this->freifeld5}',
      `freifeld6`='{$this->freifeld6}',
      `einheit`='{$this->einheit}',
      `webid`='{$this->webid}',
      `lieferzeitmanuell_en`='{$this->lieferzeitmanuell_en}',
      `variante`='{$this->variante}',
      `variante_von`='{$this->variante_von}',
      `produktioninfo`='{$this->produktioninfo}',
      `sonderaktion`='{$this->sonderaktion}',
      `sonderaktion_en`='{$this->sonderaktion_en}',
      `autolagerlampe`='{$this->autolagerlampe}',
      `leerfeld`='{$this->leerfeld}',
      `zolltarifnummer`='{$this->zolltarifnummer}',
      `herkunftsland`='{$this->herkunftsland}',
      `laenge`='{$this->laenge}',
      `breite`='{$this->breite}',
      `hoehe`='{$this->hoehe}',
      `gebuehr`='{$this->gebuehr}',
      `pseudolager`='{$this->pseudolager}',
      `downloadartikel`='{$this->downloadartikel}',
      `matrixprodukt`='{$this->matrixprodukt}',
      `steuer_erloese_inland_normal`='{$this->steuer_erloese_inland_normal}',
      `steuer_aufwendung_inland_normal`='{$this->steuer_aufwendung_inland_normal}',
      `steuer_erloese_inland_ermaessigt`='{$this->steuer_erloese_inland_ermaessigt}',
      `steuer_aufwendung_inland_ermaessigt`='{$this->steuer_aufwendung_inland_ermaessigt}',
      `steuer_erloese_inland_steuerfrei`='{$this->steuer_erloese_inland_steuerfrei}',
      `steuer_aufwendung_inland_steuerfrei`='{$this->steuer_aufwendung_inland_steuerfrei}',
      `steuer_erloese_inland_innergemeinschaftlich`='{$this->steuer_erloese_inland_innergemeinschaftlich}',
      `steuer_aufwendung_inland_innergemeinschaftlich`='{$this->steuer_aufwendung_inland_innergemeinschaftlich}',
      `steuer_erloese_inland_eunormal`='{$this->steuer_erloese_inland_eunormal}',
      `steuer_erloese_inland_nichtsteuerbar`='{$this->steuer_erloese_inland_nichtsteuerbar}',
      `steuer_erloese_inland_euermaessigt`='{$this->steuer_erloese_inland_euermaessigt}',
      `steuer_aufwendung_inland_nichtsteuerbar`='{$this->steuer_aufwendung_inland_nichtsteuerbar}',
      `steuer_aufwendung_inland_eunormal`='{$this->steuer_aufwendung_inland_eunormal}',
      `steuer_aufwendung_inland_euermaessigt`='{$this->steuer_aufwendung_inland_euermaessigt}',
      `steuer_erloese_inland_export`='{$this->steuer_erloese_inland_export}',
      `steuer_aufwendung_inland_import`='{$this->steuer_aufwendung_inland_import}',
      `steuer_art_produkt`='{$this->steuer_art_produkt}',
      `steuer_art_produkt_download`='{$this->steuer_art_produkt_download}',
      `metadescription_de`='{$this->metadescription_de}',
      `metadescription_en`='{$this->metadescription_en}',
      `metakeywords_de`='{$this->metakeywords_de}',
      `metakeywords_en`='{$this->metakeywords_en}',
      `anabregs_text_en`='{$this->anabregs_text_en}',
      `externeproduktion`='{$this->externeproduktion}',
      `bildvorschau`='{$this->bildvorschau}',
      `inventursperre`='{$this->inventursperre}',
      `variante_kopie`='{$this->variante_kopie}',
      `unikat`='{$this->unikat}',
      `generierenummerbeioption`='{$this->generierenummerbeioption}',
      `allelieferanten`='{$this->allelieferanten}',
      `tagespreise`='{$this->tagespreise}',
      `rohstoffe`='{$this->rohstoffe}',
      `xvp`='{$this->xvp}',
      `ohnepreisimpdf`='{$this->ohnepreisimpdf}',
      `provisionssperre`='{$this->provisionssperre}',
      `dienstleistung`='{$this->dienstleistung}',
      `inventurekaktiv`='{$this->inventurekaktiv}',
      `inventurek`='{$this->inventurek}',
      `hinweis_einfuegen`='{$this->hinweis_einfuegen}',
      `etikettautodruck`='{$this->etikettautodruck}',
      `lagerkorrekturwert`='{$this->lagerkorrekturwert}',
      `autodrucketikett`='{$this->autodrucketikett}',
      `abckategorie`='{$this->abckategorie}',
      `laststorage_changed`='{$this->laststorage_changed}',
      `laststorage_sync`='{$this->laststorage_sync}',
      `steuersatz`='{$this->steuersatz}',
      `steuertext_innergemeinschaftlich`='{$this->steuertext_innergemeinschaftlich}',
      `steuertext_export`='{$this->steuertext_export}',
      `formelmenge`='{$this->formelmenge}',
      `formelpreis`='{$this->formelpreis}',
      `freifeld7`='{$this->freifeld7}',
      `freifeld8`='{$this->freifeld8}',
      `freifeld9`='{$this->freifeld9}',
      `freifeld10`='{$this->freifeld10}',
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
      `freifeld21`='{$this->freifeld21}',
      `freifeld22`='{$this->freifeld22}',
      `freifeld23`='{$this->freifeld23}',
      `freifeld24`='{$this->freifeld24}',
      `freifeld25`='{$this->freifeld25}',
      `freifeld26`='{$this->freifeld26}',
      `freifeld27`='{$this->freifeld27}',
      `freifeld28`='{$this->freifeld28}',
      `freifeld29`='{$this->freifeld29}',
      `freifeld30`='{$this->freifeld30}',
      `freifeld31`='{$this->freifeld31}',
      `freifeld32`='{$this->freifeld32}',
      `freifeld33`='{$this->freifeld33}',
      `freifeld34`='{$this->freifeld34}',
      `freifeld35`='{$this->freifeld35}',
      `freifeld36`='{$this->freifeld36}',
      `freifeld37`='{$this->freifeld37}',
      `freifeld38`='{$this->freifeld38}',
      `freifeld39`='{$this->freifeld39}',
      `freifeld40`='{$this->freifeld40}',
      `ursprungsregion`='{$this->ursprungsregion}',
      `bestandalternativartikel`='{$this->bestandalternativartikel}',
      `metatitle_de`='{$this->metatitle_de}',
      `metatitle_en`='{$this->metatitle_en}',
      `vkmeldungunterdruecken`='{$this->vkmeldungunterdruecken}',
      `altersfreigabe`='{$this->altersfreigabe}',
      `unikatbeikopie`='{$this->unikatbeikopie}',
      `steuergruppe`='{$this->steuergruppe}',
      `kostenstelle`='{$this->kostenstelle}',
      `artikelautokalkulation`='{$this->artikelautokalkulation}',
      `artikelabschliessenkalkulation`='{$this->artikelabschliessenkalkulation}',
      `artikelfifokalkulation`='{$this->artikelfifokalkulation}',
      `keinskonto`='{$this->keinskonto}',
      `berechneterek`='{$this->berechneterek}',
      `verwendeberechneterek`='{$this->verwendeberechneterek}',
      `berechneterekwaehrung`='{$this->berechneterekwaehrung}',
      `has_preproduced_partlist`='{$this->has_preproduced_partlist}',
      `preproduced_partlist`='{$this->preproduced_partlist}',
      `nettogewicht`='{$this->nettogewicht}'
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

    $sql = "DELETE FROM `artikel` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->typ='';
    $this->nummer='';
    $this->checksum='';
    $this->projekt='';
    $this->inaktiv='';
    $this->ausverkauft='';
    $this->warengruppe='';
    $this->name_de='';
    $this->name_en='';
    $this->kurztext_de='';
    $this->kurztext_en='';
    $this->beschreibung_de='';
    $this->beschreibung_en='';
    $this->uebersicht_de='';
    $this->uebersicht_en='';
    $this->links_de='';
    $this->links_en='';
    $this->startseite_de='';
    $this->startseite_en='';
    $this->standardbild='';
    $this->herstellerlink='';
    $this->hersteller='';
    $this->teilbar='';
    $this->nteile='';
    $this->seriennummern='';
    $this->lager_platz='';
    $this->lieferzeit='';
    $this->lieferzeitmanuell='';
    $this->sonstiges='';
    $this->gewicht='';
    $this->endmontage='';
    $this->funktionstest='';
    $this->artikelcheckliste='';
    $this->stueckliste='';
    $this->juststueckliste='';
    $this->barcode='';
    $this->hinzugefuegt='';
    $this->pcbdecal='';
    $this->lagerartikel='';
    $this->porto='';
    $this->chargenverwaltung='';
    $this->provisionsartikel='';
    $this->gesperrt='';
    $this->sperrgrund='';
    $this->geloescht='';
    $this->gueltigbis='';
    $this->umsatzsteuer='';
    $this->klasse='';
    $this->adresse='';
    $this->shopartikel='';
    $this->unishopartikel='';
    $this->journalshopartikel='';
    $this->shop='';
    $this->katalog='';
    $this->katalogtext_de='';
    $this->katalogtext_en='';
    $this->katalogbezeichnung_de='';
    $this->katalogbezeichnung_en='';
    $this->neu='';
    $this->topseller='';
    $this->startseite='';
    $this->wichtig='';
    $this->mindestlager='';
    $this->mindestbestellung='';
    $this->partnerprogramm_sperre='';
    $this->internerkommentar='';
    $this->intern_gesperrt='';
    $this->intern_gesperrtuser='';
    $this->intern_gesperrtgrund='';
    $this->inbearbeitung='';
    $this->inbearbeitunguser='';
    $this->cache_lagerplatzinhaltmenge='';
    $this->internkommentar='';
    $this->firma='';
    $this->logdatei='';
    $this->anabregs_text='';
    $this->autobestellung='';
    $this->produktion='';
    $this->herstellernummer='';
    $this->restmenge='';
    $this->mlmdirektpraemie='';
    $this->keineeinzelartikelanzeigen='';
    $this->mindesthaltbarkeitsdatum='';
    $this->letzteseriennummer='';
    $this->individualartikel='';
    $this->keinrabatterlaubt='';
    $this->rabatt='';
    $this->rabatt_prozent='';
    $this->geraet='';
    $this->serviceartikel='';
    $this->autoabgleicherlaubt='';
    $this->pseudopreis='';
    $this->freigabenotwendig='';
    $this->freigaberegel='';
    $this->nachbestellt='';
    $this->ean='';
    $this->mlmpunkte='';
    $this->mlmbonuspunkte='';
    $this->mlmkeinepunkteeigenkauf='';
    $this->shop2='';
    $this->shop3='';
    $this->usereditid='';
    $this->useredittimestamp='';
    $this->freifeld1='';
    $this->freifeld2='';
    $this->freifeld3='';
    $this->freifeld4='';
    $this->freifeld5='';
    $this->freifeld6='';
    $this->einheit='';
    $this->webid='';
    $this->lieferzeitmanuell_en='';
    $this->variante='';
    $this->variante_von='';
    $this->produktioninfo='';
    $this->sonderaktion='';
    $this->sonderaktion_en='';
    $this->autolagerlampe='';
    $this->leerfeld='';
    $this->zolltarifnummer='';
    $this->herkunftsland='';
    $this->laenge='';
    $this->breite='';
    $this->hoehe='';
    $this->gebuehr='';
    $this->pseudolager='';
    $this->downloadartikel='';
    $this->matrixprodukt='';
    $this->steuer_erloese_inland_normal='';
    $this->steuer_aufwendung_inland_normal='';
    $this->steuer_erloese_inland_ermaessigt='';
    $this->steuer_aufwendung_inland_ermaessigt='';
    $this->steuer_erloese_inland_steuerfrei='';
    $this->steuer_aufwendung_inland_steuerfrei='';
    $this->steuer_erloese_inland_innergemeinschaftlich='';
    $this->steuer_aufwendung_inland_innergemeinschaftlich='';
    $this->steuer_erloese_inland_eunormal='';
    $this->steuer_erloese_inland_nichtsteuerbar='';
    $this->steuer_erloese_inland_euermaessigt='';
    $this->steuer_aufwendung_inland_nichtsteuerbar='';
    $this->steuer_aufwendung_inland_eunormal='';
    $this->steuer_aufwendung_inland_euermaessigt='';
    $this->steuer_erloese_inland_export='';
    $this->steuer_aufwendung_inland_import='';
    $this->steuer_art_produkt='';
    $this->steuer_art_produkt_download='';
    $this->metadescription_de='';
    $this->metadescription_en='';
    $this->metakeywords_de='';
    $this->metakeywords_en='';
    $this->anabregs_text_en='';
    $this->externeproduktion='';
    $this->bildvorschau='';
    $this->inventursperre='';
    $this->variante_kopie='';
    $this->unikat='';
    $this->generierenummerbeioption='';
    $this->allelieferanten='';
    $this->tagespreise='';
    $this->rohstoffe='';
    $this->xvp='';
    $this->ohnepreisimpdf='';
    $this->provisionssperre='';
    $this->dienstleistung='';
    $this->inventurekaktiv='';
    $this->inventurek='';
    $this->hinweis_einfuegen='';
    $this->etikettautodruck='';
    $this->lagerkorrekturwert='';
    $this->autodrucketikett='';
    $this->abckategorie='';
    $this->laststorage_changed='';
    $this->laststorage_sync='';
    $this->steuersatz='';
    $this->steuertext_innergemeinschaftlich='';
    $this->steuertext_export='';
    $this->formelmenge='';
    $this->formelpreis='';
    $this->freifeld7='';
    $this->freifeld8='';
    $this->freifeld9='';
    $this->freifeld10='';
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
    $this->freifeld21='';
    $this->freifeld22='';
    $this->freifeld23='';
    $this->freifeld24='';
    $this->freifeld25='';
    $this->freifeld26='';
    $this->freifeld27='';
    $this->freifeld28='';
    $this->freifeld29='';
    $this->freifeld30='';
    $this->freifeld31='';
    $this->freifeld32='';
    $this->freifeld33='';
    $this->freifeld34='';
    $this->freifeld35='';
    $this->freifeld36='';
    $this->freifeld37='';
    $this->freifeld38='';
    $this->freifeld39='';
    $this->freifeld40='';
    $this->ursprungsregion='';
    $this->bestandalternativartikel='';
    $this->metatitle_de='';
    $this->metatitle_en='';
    $this->vkmeldungunterdruecken='';
    $this->altersfreigabe='';
    $this->unikatbeikopie='';
    $this->steuergruppe='';
    $this->kostenstelle='';
    $this->artikelautokalkulation='';
    $this->artikelabschliessenkalkulation='';
    $this->artikelfifokalkulation='';
    $this->keinskonto='';
    $this->berechneterek='';
    $this->verwendeberechneterek='';
    $this->berechneterekwaehrung='';
    $this->has_preproduced_partlist='';
    $this->preproduced_partlist='';
    $this->nettogewicht='';
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
  public function SetNummer($value) { $this->nummer=$value; }
  public function GetNummer() { return $this->nummer; }
  public function SetChecksum($value) { $this->checksum=$value; }
  public function GetChecksum() { return $this->checksum; }
  public function SetProjekt($value) { $this->projekt=$value; }
  public function GetProjekt() { return $this->projekt; }
  public function SetInaktiv($value) { $this->inaktiv=$value; }
  public function GetInaktiv() { return $this->inaktiv; }
  public function SetAusverkauft($value) { $this->ausverkauft=$value; }
  public function GetAusverkauft() { return $this->ausverkauft; }
  public function SetWarengruppe($value) { $this->warengruppe=$value; }
  public function GetWarengruppe() { return $this->warengruppe; }
  public function SetName_De($value) { $this->name_de=$value; }
  public function GetName_De() { return $this->name_de; }
  public function SetName_En($value) { $this->name_en=$value; }
  public function GetName_En() { return $this->name_en; }
  public function SetKurztext_De($value) { $this->kurztext_de=$value; }
  public function GetKurztext_De() { return $this->kurztext_de; }
  public function SetKurztext_En($value) { $this->kurztext_en=$value; }
  public function GetKurztext_En() { return $this->kurztext_en; }
  public function SetBeschreibung_De($value) { $this->beschreibung_de=$value; }
  public function GetBeschreibung_De() { return $this->beschreibung_de; }
  public function SetBeschreibung_En($value) { $this->beschreibung_en=$value; }
  public function GetBeschreibung_En() { return $this->beschreibung_en; }
  public function SetUebersicht_De($value) { $this->uebersicht_de=$value; }
  public function GetUebersicht_De() { return $this->uebersicht_de; }
  public function SetUebersicht_En($value) { $this->uebersicht_en=$value; }
  public function GetUebersicht_En() { return $this->uebersicht_en; }
  public function SetLinks_De($value) { $this->links_de=$value; }
  public function GetLinks_De() { return $this->links_de; }
  public function SetLinks_En($value) { $this->links_en=$value; }
  public function GetLinks_En() { return $this->links_en; }
  public function SetStartseite_De($value) { $this->startseite_de=$value; }
  public function GetStartseite_De() { return $this->startseite_de; }
  public function SetStartseite_En($value) { $this->startseite_en=$value; }
  public function GetStartseite_En() { return $this->startseite_en; }
  public function SetStandardbild($value) { $this->standardbild=$value; }
  public function GetStandardbild() { return $this->standardbild; }
  public function SetHerstellerlink($value) { $this->herstellerlink=$value; }
  public function GetHerstellerlink() { return $this->herstellerlink; }
  public function SetHersteller($value) { $this->hersteller=$value; }
  public function GetHersteller() { return $this->hersteller; }
  public function SetTeilbar($value) { $this->teilbar=$value; }
  public function GetTeilbar() { return $this->teilbar; }
  public function SetNteile($value) { $this->nteile=$value; }
  public function GetNteile() { return $this->nteile; }
  public function SetSeriennummern($value) { $this->seriennummern=$value; }
  public function GetSeriennummern() { return $this->seriennummern; }
  public function SetLager_Platz($value) { $this->lager_platz=$value; }
  public function GetLager_Platz() { return $this->lager_platz; }
  public function SetLieferzeit($value) { $this->lieferzeit=$value; }
  public function GetLieferzeit() { return $this->lieferzeit; }
  public function SetLieferzeitmanuell($value) { $this->lieferzeitmanuell=$value; }
  public function GetLieferzeitmanuell() { return $this->lieferzeitmanuell; }
  public function SetSonstiges($value) { $this->sonstiges=$value; }
  public function GetSonstiges() { return $this->sonstiges; }
  public function SetGewicht($value) { $this->gewicht=$value; }
  public function GetGewicht() { return $this->gewicht; }
  public function SetEndmontage($value) { $this->endmontage=$value; }
  public function GetEndmontage() { return $this->endmontage; }
  public function SetFunktionstest($value) { $this->funktionstest=$value; }
  public function GetFunktionstest() { return $this->funktionstest; }
  public function SetArtikelcheckliste($value) { $this->artikelcheckliste=$value; }
  public function GetArtikelcheckliste() { return $this->artikelcheckliste; }
  public function SetStueckliste($value) { $this->stueckliste=$value; }
  public function GetStueckliste() { return $this->stueckliste; }
  public function SetJuststueckliste($value) { $this->juststueckliste=$value; }
  public function GetJuststueckliste() { return $this->juststueckliste; }
  public function SetBarcode($value) { $this->barcode=$value; }
  public function GetBarcode() { return $this->barcode; }
  public function SetHinzugefuegt($value) { $this->hinzugefuegt=$value; }
  public function GetHinzugefuegt() { return $this->hinzugefuegt; }
  public function SetPcbdecal($value) { $this->pcbdecal=$value; }
  public function GetPcbdecal() { return $this->pcbdecal; }
  public function SetLagerartikel($value) { $this->lagerartikel=$value; }
  public function GetLagerartikel() { return $this->lagerartikel; }
  public function SetPorto($value) { $this->porto=$value; }
  public function GetPorto() { return $this->porto; }
  public function SetChargenverwaltung($value) { $this->chargenverwaltung=$value; }
  public function GetChargenverwaltung() { return $this->chargenverwaltung; }
  public function SetProvisionsartikel($value) { $this->provisionsartikel=$value; }
  public function GetProvisionsartikel() { return $this->provisionsartikel; }
  public function SetGesperrt($value) { $this->gesperrt=$value; }
  public function GetGesperrt() { return $this->gesperrt; }
  public function SetSperrgrund($value) { $this->sperrgrund=$value; }
  public function GetSperrgrund() { return $this->sperrgrund; }
  public function SetGeloescht($value) { $this->geloescht=$value; }
  public function GetGeloescht() { return $this->geloescht; }
  public function SetGueltigbis($value) { $this->gueltigbis=$value; }
  public function GetGueltigbis() { return $this->gueltigbis; }
  public function SetUmsatzsteuer($value) { $this->umsatzsteuer=$value; }
  public function GetUmsatzsteuer() { return $this->umsatzsteuer; }
  public function SetKlasse($value) { $this->klasse=$value; }
  public function GetKlasse() { return $this->klasse; }
  public function SetAdresse($value) { $this->adresse=$value; }
  public function GetAdresse() { return $this->adresse; }
  public function SetShopartikel($value) { $this->shopartikel=$value; }
  public function GetShopartikel() { return $this->shopartikel; }
  public function SetUnishopartikel($value) { $this->unishopartikel=$value; }
  public function GetUnishopartikel() { return $this->unishopartikel; }
  public function SetJournalshopartikel($value) { $this->journalshopartikel=$value; }
  public function GetJournalshopartikel() { return $this->journalshopartikel; }
  public function SetShop($value) { $this->shop=$value; }
  public function GetShop() { return $this->shop; }
  public function SetKatalog($value) { $this->katalog=$value; }
  public function GetKatalog() { return $this->katalog; }
  public function SetKatalogtext_De($value) { $this->katalogtext_de=$value; }
  public function GetKatalogtext_De() { return $this->katalogtext_de; }
  public function SetKatalogtext_En($value) { $this->katalogtext_en=$value; }
  public function GetKatalogtext_En() { return $this->katalogtext_en; }
  public function SetKatalogbezeichnung_De($value) { $this->katalogbezeichnung_de=$value; }
  public function GetKatalogbezeichnung_De() { return $this->katalogbezeichnung_de; }
  public function SetKatalogbezeichnung_En($value) { $this->katalogbezeichnung_en=$value; }
  public function GetKatalogbezeichnung_En() { return $this->katalogbezeichnung_en; }
  public function SetNeu($value) { $this->neu=$value; }
  public function GetNeu() { return $this->neu; }
  public function SetTopseller($value) { $this->topseller=$value; }
  public function GetTopseller() { return $this->topseller; }
  public function SetStartseite($value) { $this->startseite=$value; }
  public function GetStartseite() { return $this->startseite; }
  public function SetWichtig($value) { $this->wichtig=$value; }
  public function GetWichtig() { return $this->wichtig; }
  public function SetMindestlager($value) { $this->mindestlager=$value; }
  public function GetMindestlager() { return $this->mindestlager; }
  public function SetMindestbestellung($value) { $this->mindestbestellung=$value; }
  public function GetMindestbestellung() { return $this->mindestbestellung; }
  public function SetPartnerprogramm_Sperre($value) { $this->partnerprogramm_sperre=$value; }
  public function GetPartnerprogramm_Sperre() { return $this->partnerprogramm_sperre; }
  public function SetInternerkommentar($value) { $this->internerkommentar=$value; }
  public function GetInternerkommentar() { return $this->internerkommentar; }
  public function SetIntern_Gesperrt($value) { $this->intern_gesperrt=$value; }
  public function GetIntern_Gesperrt() { return $this->intern_gesperrt; }
  public function SetIntern_Gesperrtuser($value) { $this->intern_gesperrtuser=$value; }
  public function GetIntern_Gesperrtuser() { return $this->intern_gesperrtuser; }
  public function SetIntern_Gesperrtgrund($value) { $this->intern_gesperrtgrund=$value; }
  public function GetIntern_Gesperrtgrund() { return $this->intern_gesperrtgrund; }
  public function SetInbearbeitung($value) { $this->inbearbeitung=$value; }
  public function GetInbearbeitung() { return $this->inbearbeitung; }
  public function SetInbearbeitunguser($value) { $this->inbearbeitunguser=$value; }
  public function GetInbearbeitunguser() { return $this->inbearbeitunguser; }
  public function SetCache_Lagerplatzinhaltmenge($value) { $this->cache_lagerplatzinhaltmenge=$value; }
  public function GetCache_Lagerplatzinhaltmenge() { return $this->cache_lagerplatzinhaltmenge; }
  public function SetInternkommentar($value) { $this->internkommentar=$value; }
  public function GetInternkommentar() { return $this->internkommentar; }
  public function SetFirma($value) { $this->firma=$value; }
  public function GetFirma() { return $this->firma; }
  public function SetLogdatei($value) { $this->logdatei=$value; }
  public function GetLogdatei() { return $this->logdatei; }
  public function SetAnabregs_Text($value) { $this->anabregs_text=$value; }
  public function GetAnabregs_Text() { return $this->anabregs_text; }
  public function SetAutobestellung($value) { $this->autobestellung=$value; }
  public function GetAutobestellung() { return $this->autobestellung; }
  public function SetProduktion($value) { $this->produktion=$value; }
  public function GetProduktion() { return $this->produktion; }
  public function SetHerstellernummer($value) { $this->herstellernummer=$value; }
  public function GetHerstellernummer() { return $this->herstellernummer; }
  public function SetRestmenge($value) { $this->restmenge=$value; }
  public function GetRestmenge() { return $this->restmenge; }
  public function SetMlmdirektpraemie($value) { $this->mlmdirektpraemie=$value; }
  public function GetMlmdirektpraemie() { return $this->mlmdirektpraemie; }
  public function SetKeineeinzelartikelanzeigen($value) { $this->keineeinzelartikelanzeigen=$value; }
  public function GetKeineeinzelartikelanzeigen() { return $this->keineeinzelartikelanzeigen; }
  public function SetMindesthaltbarkeitsdatum($value) { $this->mindesthaltbarkeitsdatum=$value; }
  public function GetMindesthaltbarkeitsdatum() { return $this->mindesthaltbarkeitsdatum; }
  public function SetLetzteseriennummer($value) { $this->letzteseriennummer=$value; }
  public function GetLetzteseriennummer() { return $this->letzteseriennummer; }
  public function SetIndividualartikel($value) { $this->individualartikel=$value; }
  public function GetIndividualartikel() { return $this->individualartikel; }
  public function SetKeinrabatterlaubt($value) { $this->keinrabatterlaubt=$value; }
  public function GetKeinrabatterlaubt() { return $this->keinrabatterlaubt; }
  public function SetRabatt($value) { $this->rabatt=$value; }
  public function GetRabatt() { return $this->rabatt; }
  public function SetRabatt_Prozent($value) { $this->rabatt_prozent=$value; }
  public function GetRabatt_Prozent() { return $this->rabatt_prozent; }
  public function SetGeraet($value) { $this->geraet=$value; }
  public function GetGeraet() { return $this->geraet; }
  public function SetServiceartikel($value) { $this->serviceartikel=$value; }
  public function GetServiceartikel() { return $this->serviceartikel; }
  public function SetAutoabgleicherlaubt($value) { $this->autoabgleicherlaubt=$value; }
  public function GetAutoabgleicherlaubt() { return $this->autoabgleicherlaubt; }
  public function SetPseudopreis($value) { $this->pseudopreis=$value; }
  public function GetPseudopreis() { return $this->pseudopreis; }
  public function SetFreigabenotwendig($value) { $this->freigabenotwendig=$value; }
  public function GetFreigabenotwendig() { return $this->freigabenotwendig; }
  public function SetFreigaberegel($value) { $this->freigaberegel=$value; }
  public function GetFreigaberegel() { return $this->freigaberegel; }
  public function SetNachbestellt($value) { $this->nachbestellt=$value; }
  public function GetNachbestellt() { return $this->nachbestellt; }
  public function SetEan($value) { $this->ean=$value; }
  public function GetEan() { return $this->ean; }
  public function SetMlmpunkte($value) { $this->mlmpunkte=$value; }
  public function GetMlmpunkte() { return $this->mlmpunkte; }
  public function SetMlmbonuspunkte($value) { $this->mlmbonuspunkte=$value; }
  public function GetMlmbonuspunkte() { return $this->mlmbonuspunkte; }
  public function SetMlmkeinepunkteeigenkauf($value) { $this->mlmkeinepunkteeigenkauf=$value; }
  public function GetMlmkeinepunkteeigenkauf() { return $this->mlmkeinepunkteeigenkauf; }
  public function SetShop2($value) { $this->shop2=$value; }
  public function GetShop2() { return $this->shop2; }
  public function SetShop3($value) { $this->shop3=$value; }
  public function GetShop3() { return $this->shop3; }
  public function SetUsereditid($value) { $this->usereditid=$value; }
  public function GetUsereditid() { return $this->usereditid; }
  public function SetUseredittimestamp($value) { $this->useredittimestamp=$value; }
  public function GetUseredittimestamp() { return $this->useredittimestamp; }
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
  public function SetEinheit($value) { $this->einheit=$value; }
  public function GetEinheit() { return $this->einheit; }
  public function SetWebid($value) { $this->webid=$value; }
  public function GetWebid() { return $this->webid; }
  public function SetLieferzeitmanuell_En($value) { $this->lieferzeitmanuell_en=$value; }
  public function GetLieferzeitmanuell_En() { return $this->lieferzeitmanuell_en; }
  public function SetVariante($value) { $this->variante=$value; }
  public function GetVariante() { return $this->variante; }
  public function SetVariante_Von($value) { $this->variante_von=$value; }
  public function GetVariante_Von() { return $this->variante_von; }
  public function SetProduktioninfo($value) { $this->produktioninfo=$value; }
  public function GetProduktioninfo() { return $this->produktioninfo; }
  public function SetSonderaktion($value) { $this->sonderaktion=$value; }
  public function GetSonderaktion() { return $this->sonderaktion; }
  public function SetSonderaktion_En($value) { $this->sonderaktion_en=$value; }
  public function GetSonderaktion_En() { return $this->sonderaktion_en; }
  public function SetAutolagerlampe($value) { $this->autolagerlampe=$value; }
  public function GetAutolagerlampe() { return $this->autolagerlampe; }
  public function SetLeerfeld($value) { $this->leerfeld=$value; }
  public function GetLeerfeld() { return $this->leerfeld; }
  public function SetZolltarifnummer($value) { $this->zolltarifnummer=$value; }
  public function GetZolltarifnummer() { return $this->zolltarifnummer; }
  public function SetHerkunftsland($value) { $this->herkunftsland=$value; }
  public function GetHerkunftsland() { return $this->herkunftsland; }
  public function SetLaenge($value) { $this->laenge=$value; }
  public function GetLaenge() { return $this->laenge; }
  public function SetBreite($value) { $this->breite=$value; }
  public function GetBreite() { return $this->breite; }
  public function SetHoehe($value) { $this->hoehe=$value; }
  public function GetHoehe() { return $this->hoehe; }
  public function SetGebuehr($value) { $this->gebuehr=$value; }
  public function GetGebuehr() { return $this->gebuehr; }
  public function SetPseudolager($value) { $this->pseudolager=$value; }
  public function GetPseudolager() { return $this->pseudolager; }
  public function SetDownloadartikel($value) { $this->downloadartikel=$value; }
  public function GetDownloadartikel() { return $this->downloadartikel; }
  public function SetMatrixprodukt($value) { $this->matrixprodukt=$value; }
  public function GetMatrixprodukt() { return $this->matrixprodukt; }
  public function SetSteuer_Erloese_Inland_Normal($value) { $this->steuer_erloese_inland_normal=$value; }
  public function GetSteuer_Erloese_Inland_Normal() { return $this->steuer_erloese_inland_normal; }
  public function SetSteuer_Aufwendung_Inland_Normal($value) { $this->steuer_aufwendung_inland_normal=$value; }
  public function GetSteuer_Aufwendung_Inland_Normal() { return $this->steuer_aufwendung_inland_normal; }
  public function SetSteuer_Erloese_Inland_Ermaessigt($value) { $this->steuer_erloese_inland_ermaessigt=$value; }
  public function GetSteuer_Erloese_Inland_Ermaessigt() { return $this->steuer_erloese_inland_ermaessigt; }
  public function SetSteuer_Aufwendung_Inland_Ermaessigt($value) { $this->steuer_aufwendung_inland_ermaessigt=$value; }
  public function GetSteuer_Aufwendung_Inland_Ermaessigt() { return $this->steuer_aufwendung_inland_ermaessigt; }
  public function SetSteuer_Erloese_Inland_Steuerfrei($value) { $this->steuer_erloese_inland_steuerfrei=$value; }
  public function GetSteuer_Erloese_Inland_Steuerfrei() { return $this->steuer_erloese_inland_steuerfrei; }
  public function SetSteuer_Aufwendung_Inland_Steuerfrei($value) { $this->steuer_aufwendung_inland_steuerfrei=$value; }
  public function GetSteuer_Aufwendung_Inland_Steuerfrei() { return $this->steuer_aufwendung_inland_steuerfrei; }
  public function SetSteuer_Erloese_Inland_Innergemeinschaftlich($value) { $this->steuer_erloese_inland_innergemeinschaftlich=$value; }
  public function GetSteuer_Erloese_Inland_Innergemeinschaftlich() { return $this->steuer_erloese_inland_innergemeinschaftlich; }
  public function SetSteuer_Aufwendung_Inland_Innergemeinschaftlich($value) { $this->steuer_aufwendung_inland_innergemeinschaftlich=$value; }
  public function GetSteuer_Aufwendung_Inland_Innergemeinschaftlich() { return $this->steuer_aufwendung_inland_innergemeinschaftlich; }
  public function SetSteuer_Erloese_Inland_Eunormal($value) { $this->steuer_erloese_inland_eunormal=$value; }
  public function GetSteuer_Erloese_Inland_Eunormal() { return $this->steuer_erloese_inland_eunormal; }
  public function SetSteuer_Erloese_Inland_Nichtsteuerbar($value) { $this->steuer_erloese_inland_nichtsteuerbar=$value; }
  public function GetSteuer_Erloese_Inland_Nichtsteuerbar() { return $this->steuer_erloese_inland_nichtsteuerbar; }
  public function SetSteuer_Erloese_Inland_Euermaessigt($value) { $this->steuer_erloese_inland_euermaessigt=$value; }
  public function GetSteuer_Erloese_Inland_Euermaessigt() { return $this->steuer_erloese_inland_euermaessigt; }
  public function SetSteuer_Aufwendung_Inland_Nichtsteuerbar($value) { $this->steuer_aufwendung_inland_nichtsteuerbar=$value; }
  public function GetSteuer_Aufwendung_Inland_Nichtsteuerbar() { return $this->steuer_aufwendung_inland_nichtsteuerbar; }
  public function SetSteuer_Aufwendung_Inland_Eunormal($value) { $this->steuer_aufwendung_inland_eunormal=$value; }
  public function GetSteuer_Aufwendung_Inland_Eunormal() { return $this->steuer_aufwendung_inland_eunormal; }
  public function SetSteuer_Aufwendung_Inland_Euermaessigt($value) { $this->steuer_aufwendung_inland_euermaessigt=$value; }
  public function GetSteuer_Aufwendung_Inland_Euermaessigt() { return $this->steuer_aufwendung_inland_euermaessigt; }
  public function SetSteuer_Erloese_Inland_Export($value) { $this->steuer_erloese_inland_export=$value; }
  public function GetSteuer_Erloese_Inland_Export() { return $this->steuer_erloese_inland_export; }
  public function SetSteuer_Aufwendung_Inland_Import($value) { $this->steuer_aufwendung_inland_import=$value; }
  public function GetSteuer_Aufwendung_Inland_Import() { return $this->steuer_aufwendung_inland_import; }
  public function SetSteuer_Art_Produkt($value) { $this->steuer_art_produkt=$value; }
  public function GetSteuer_Art_Produkt() { return $this->steuer_art_produkt; }
  public function SetSteuer_Art_Produkt_Download($value) { $this->steuer_art_produkt_download=$value; }
  public function GetSteuer_Art_Produkt_Download() { return $this->steuer_art_produkt_download; }
  public function SetMetadescription_De($value) { $this->metadescription_de=$value; }
  public function GetMetadescription_De() { return $this->metadescription_de; }
  public function SetMetadescription_En($value) { $this->metadescription_en=$value; }
  public function GetMetadescription_En() { return $this->metadescription_en; }
  public function SetMetakeywords_De($value) { $this->metakeywords_de=$value; }
  public function GetMetakeywords_De() { return $this->metakeywords_de; }
  public function SetMetakeywords_En($value) { $this->metakeywords_en=$value; }
  public function GetMetakeywords_En() { return $this->metakeywords_en; }
  public function SetAnabregs_Text_En($value) { $this->anabregs_text_en=$value; }
  public function GetAnabregs_Text_En() { return $this->anabregs_text_en; }
  public function SetExterneproduktion($value) { $this->externeproduktion=$value; }
  public function GetExterneproduktion() { return $this->externeproduktion; }
  public function SetBildvorschau($value) { $this->bildvorschau=$value; }
  public function GetBildvorschau() { return $this->bildvorschau; }
  public function SetInventursperre($value) { $this->inventursperre=$value; }
  public function GetInventursperre() { return $this->inventursperre; }
  public function SetVariante_Kopie($value) { $this->variante_kopie=$value; }
  public function GetVariante_Kopie() { return $this->variante_kopie; }
  public function SetUnikat($value) { $this->unikat=$value; }
  public function GetUnikat() { return $this->unikat; }
  public function SetGenerierenummerbeioption($value) { $this->generierenummerbeioption=$value; }
  public function GetGenerierenummerbeioption() { return $this->generierenummerbeioption; }
  public function SetAllelieferanten($value) { $this->allelieferanten=$value; }
  public function GetAllelieferanten() { return $this->allelieferanten; }
  public function SetTagespreise($value) { $this->tagespreise=$value; }
  public function GetTagespreise() { return $this->tagespreise; }
  public function SetRohstoffe($value) { $this->rohstoffe=$value; }
  public function GetRohstoffe() { return $this->rohstoffe; }
  public function SetXvp($value) { $this->xvp=$value; }
  public function GetXvp() { return $this->xvp; }
  public function SetOhnepreisimpdf($value) { $this->ohnepreisimpdf=$value; }
  public function GetOhnepreisimpdf() { return $this->ohnepreisimpdf; }
  public function SetProvisionssperre($value) { $this->provisionssperre=$value; }
  public function GetProvisionssperre() { return $this->provisionssperre; }
  public function SetDienstleistung($value) { $this->dienstleistung=$value; }
  public function GetDienstleistung() { return $this->dienstleistung; }
  public function SetInventurekaktiv($value) { $this->inventurekaktiv=$value; }
  public function GetInventurekaktiv() { return $this->inventurekaktiv; }
  public function SetInventurek($value) { $this->inventurek=$value; }
  public function GetInventurek() { return $this->inventurek; }
  public function SetHinweis_Einfuegen($value) { $this->hinweis_einfuegen=$value; }
  public function GetHinweis_Einfuegen() { return $this->hinweis_einfuegen; }
  public function SetEtikettautodruck($value) { $this->etikettautodruck=$value; }
  public function GetEtikettautodruck() { return $this->etikettautodruck; }
  public function SetLagerkorrekturwert($value) { $this->lagerkorrekturwert=$value; }
  public function GetLagerkorrekturwert() { return $this->lagerkorrekturwert; }
  public function SetAutodrucketikett($value) { $this->autodrucketikett=$value; }
  public function GetAutodrucketikett() { return $this->autodrucketikett; }
  public function SetAbckategorie($value) { $this->abckategorie=$value; }
  public function GetAbckategorie() { return $this->abckategorie; }
  public function SetLaststorage_Changed($value) { $this->laststorage_changed=$value; }
  public function GetLaststorage_Changed() { return $this->laststorage_changed; }
  public function SetLaststorage_Sync($value) { $this->laststorage_sync=$value; }
  public function GetLaststorage_Sync() { return $this->laststorage_sync; }
  public function SetSteuersatz($value) { $this->steuersatz=$value; }
  public function GetSteuersatz() { return $this->steuersatz; }
  public function SetSteuertext_Innergemeinschaftlich($value) { $this->steuertext_innergemeinschaftlich=$value; }
  public function GetSteuertext_Innergemeinschaftlich() { return $this->steuertext_innergemeinschaftlich; }
  public function SetSteuertext_Export($value) { $this->steuertext_export=$value; }
  public function GetSteuertext_Export() { return $this->steuertext_export; }
  public function SetFormelmenge($value) { $this->formelmenge=$value; }
  public function GetFormelmenge() { return $this->formelmenge; }
  public function SetFormelpreis($value) { $this->formelpreis=$value; }
  public function GetFormelpreis() { return $this->formelpreis; }
  public function SetFreifeld7($value) { $this->freifeld7=$value; }
  public function GetFreifeld7() { return $this->freifeld7; }
  public function SetFreifeld8($value) { $this->freifeld8=$value; }
  public function GetFreifeld8() { return $this->freifeld8; }
  public function SetFreifeld9($value) { $this->freifeld9=$value; }
  public function GetFreifeld9() { return $this->freifeld9; }
  public function SetFreifeld10($value) { $this->freifeld10=$value; }
  public function GetFreifeld10() { return $this->freifeld10; }
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
  public function SetFreifeld21($value) { $this->freifeld21=$value; }
  public function GetFreifeld21() { return $this->freifeld21; }
  public function SetFreifeld22($value) { $this->freifeld22=$value; }
  public function GetFreifeld22() { return $this->freifeld22; }
  public function SetFreifeld23($value) { $this->freifeld23=$value; }
  public function GetFreifeld23() { return $this->freifeld23; }
  public function SetFreifeld24($value) { $this->freifeld24=$value; }
  public function GetFreifeld24() { return $this->freifeld24; }
  public function SetFreifeld25($value) { $this->freifeld25=$value; }
  public function GetFreifeld25() { return $this->freifeld25; }
  public function SetFreifeld26($value) { $this->freifeld26=$value; }
  public function GetFreifeld26() { return $this->freifeld26; }
  public function SetFreifeld27($value) { $this->freifeld27=$value; }
  public function GetFreifeld27() { return $this->freifeld27; }
  public function SetFreifeld28($value) { $this->freifeld28=$value; }
  public function GetFreifeld28() { return $this->freifeld28; }
  public function SetFreifeld29($value) { $this->freifeld29=$value; }
  public function GetFreifeld29() { return $this->freifeld29; }
  public function SetFreifeld30($value) { $this->freifeld30=$value; }
  public function GetFreifeld30() { return $this->freifeld30; }
  public function SetFreifeld31($value) { $this->freifeld31=$value; }
  public function GetFreifeld31() { return $this->freifeld31; }
  public function SetFreifeld32($value) { $this->freifeld32=$value; }
  public function GetFreifeld32() { return $this->freifeld32; }
  public function SetFreifeld33($value) { $this->freifeld33=$value; }
  public function GetFreifeld33() { return $this->freifeld33; }
  public function SetFreifeld34($value) { $this->freifeld34=$value; }
  public function GetFreifeld34() { return $this->freifeld34; }
  public function SetFreifeld35($value) { $this->freifeld35=$value; }
  public function GetFreifeld35() { return $this->freifeld35; }
  public function SetFreifeld36($value) { $this->freifeld36=$value; }
  public function GetFreifeld36() { return $this->freifeld36; }
  public function SetFreifeld37($value) { $this->freifeld37=$value; }
  public function GetFreifeld37() { return $this->freifeld37; }
  public function SetFreifeld38($value) { $this->freifeld38=$value; }
  public function GetFreifeld38() { return $this->freifeld38; }
  public function SetFreifeld39($value) { $this->freifeld39=$value; }
  public function GetFreifeld39() { return $this->freifeld39; }
  public function SetFreifeld40($value) { $this->freifeld40=$value; }
  public function GetFreifeld40() { return $this->freifeld40; }
  public function SetUrsprungsregion($value) { $this->ursprungsregion=$value; }
  public function GetUrsprungsregion() { return $this->ursprungsregion; }
  public function SetBestandalternativartikel($value) { $this->bestandalternativartikel=$value; }
  public function GetBestandalternativartikel() { return $this->bestandalternativartikel; }
  public function SetMetatitle_De($value) { $this->metatitle_de=$value; }
  public function GetMetatitle_De() { return $this->metatitle_de; }
  public function SetMetatitle_En($value) { $this->metatitle_en=$value; }
  public function GetMetatitle_En() { return $this->metatitle_en; }
  public function SetVkmeldungunterdruecken($value) { $this->vkmeldungunterdruecken=$value; }
  public function GetVkmeldungunterdruecken() { return $this->vkmeldungunterdruecken; }
  public function SetAltersfreigabe($value) { $this->altersfreigabe=$value; }
  public function GetAltersfreigabe() { return $this->altersfreigabe; }
  public function SetUnikatbeikopie($value) { $this->unikatbeikopie=$value; }
  public function GetUnikatbeikopie() { return $this->unikatbeikopie; }
  public function SetSteuergruppe($value) { $this->steuergruppe=$value; }
  public function GetSteuergruppe() { return $this->steuergruppe; }
  public function SetKostenstelle($value) { $this->kostenstelle=$value; }
  public function GetKostenstelle() { return $this->kostenstelle; }
  public function SetArtikelautokalkulation($value) { $this->artikelautokalkulation=$value; }
  public function GetArtikelautokalkulation() { return $this->artikelautokalkulation; }
  public function SetArtikelabschliessenkalkulation($value) { $this->artikelabschliessenkalkulation=$value; }
  public function GetArtikelabschliessenkalkulation() { return $this->artikelabschliessenkalkulation; }
  public function SetArtikelfifokalkulation($value) { $this->artikelfifokalkulation=$value; }
  public function GetArtikelfifokalkulation() { return $this->artikelfifokalkulation; }
  public function SetKeinskonto($value) { $this->keinskonto=$value; }
  public function GetKeinskonto() { return $this->keinskonto; }
  public function SetBerechneterek($value) { $this->berechneterek=$value; }
  public function GetBerechneterek() { return $this->berechneterek; }
  public function SetVerwendeberechneterek($value) { $this->verwendeberechneterek=$value; }
  public function GetVerwendeberechneterek() { return $this->verwendeberechneterek; }
  public function SetBerechneterekwaehrung($value) { $this->berechneterekwaehrung=$value; }
  public function GetBerechneterekwaehrung() { return $this->berechneterekwaehrung; }
  public function SetHas_Preproduced_Partlist($value) { $this->has_preproduced_partlist=$value; }
  public function GetHas_Preproduced_Partlist() { return $this->has_preproduced_partlist; }
  public function SetPreproduced_Partlist($value) { $this->preproduced_partlist=$value; }
  public function GetPreproduced_Partlist() { return $this->preproduced_partlist; }
  public function SetNettogewicht($value) { $this->nettogewicht=$value; }
  public function GetNettogewicht() { return $this->nettogewicht; }

}
