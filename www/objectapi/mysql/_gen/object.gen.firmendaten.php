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

class ObjGenFirmendaten
{

  private  $id;
  private  $firma;
  private  $absender;
  private  $sichtbar;
  private  $barcode;
  private  $schriftgroesse;
  private  $betreffszeile;
  private  $dokumententext;
  private  $tabellenbeschriftung;
  private  $tabelleninhalt;
  private  $zeilenuntertext;
  private  $freitext;
  private  $infobox;
  private  $spaltenbreite;
  private  $footer_0_0;
  private  $footer_0_1;
  private  $footer_0_2;
  private  $footer_0_3;
  private  $footer_0_4;
  private  $footer_0_5;
  private  $footer_1_0;
  private  $footer_1_1;
  private  $footer_1_2;
  private  $footer_1_3;
  private  $footer_1_4;
  private  $footer_1_5;
  private  $footer_2_0;
  private  $footer_2_1;
  private  $footer_2_2;
  private  $footer_2_3;
  private  $footer_2_4;
  private  $footer_2_5;
  private  $footer_3_0;
  private  $footer_3_1;
  private  $footer_3_2;
  private  $footer_3_3;
  private  $footer_3_4;
  private  $footer_3_5;
  private  $footersichtbar;
  private  $hintergrund;
  private  $logo;
  private  $logo_type;
  private  $briefpapier;
  private  $briefpapier_type;
  private  $benutzername;
  private  $passwort;
  private  $host;
  private  $port;
  private  $mailssl;
  private  $signatur;
  private  $email;
  private  $absendername;
  private  $bcc1;
  private  $bcc2;
  private  $firmenfarbe;
  private  $name;
  private  $strasse;
  private  $plz;
  private  $ort;
  private  $steuernummer;
  private  $startseite_wiki;
  private  $datum;
  private  $projekt;
  private  $brieftext;
  private  $next_angebot;
  private  $next_auftrag;
  private  $next_gutschrift;
  private  $next_lieferschein;
  private  $next_bestellung;
  private  $next_rechnung;
  private  $next_kundennummer;
  private  $next_lieferantennummer;
  private  $next_mitarbeiternummer;
  private  $next_waren;
  private  $next_sonstiges;
  private  $next_produktion;
  private  $next_kundennumer;
  private  $next_produktionen;
  private  $wareneingang_kamera_waage;
  private  $layout_iconbar;
  private  $seite_von_ausrichtung;
  private  $seite_von_sichtbar;
  private  $rechnung_header;
  private  $lieferschein_header;
  private  $angebot_header;
  private  $auftrag_header;
  private  $gutschrift_header;
  private  $bestellung_header;
  private  $rechnung_footer;
  private  $lieferschein_footer;
  private  $angebot_footer;
  private  $auftrag_footer;
  private  $gutschrift_footer;
  private  $bestellung_footer;
  private  $eu_lieferung_vermerk;
  private  $rechnung_ohnebriefpapier;
  private  $lieferschein_ohnebriefpapier;
  private  $angebot_ohnebriefpapier;
  private  $auftrag_ohnebriefpapier;
  private  $gutschrift_ohnebriefpapier;
  private  $bestellung_ohnebriefpapier;
  private  $abstand_adresszeileoben;
  private  $abstand_boxrechtsoben;
  private  $abstand_betreffzeileoben;
  private  $abstand_artikeltabelleoben;
  private  $arbeitsnachweis_header;
  private  $arbeitsnachweis_footer;
  private  $arbeitsnachweis_ohnebriefpapier;
  private  $next_arbeitsnachweis;
  private  $parameterundfreifelder;
  private  $freifeld1;
  private  $freifeld2;
  private  $freifeld3;
  private  $freifeld4;
  private  $freifeld5;
  private  $freifeld6;
  private  $artikel_suche_kurztext;
  private  $externeinkauf;
  private  $schriftart;
  private  $externereinkauf;
  private  $next_reisekosten;
  private  $projektnummerimdokument;
  private  $mailanstellesmtp;
  private  $herstellernummerimdokument;
  private  $standardmarge;
  private  $zahlungsweise;
  private  $zahlungszieltage;
  private  $zahlungszielskonto;
  private  $zahlungszieltageskonto;
  private  $zahlung_rechnung;
  private  $zahlung_vorkasse;
  private  $zahlung_nachnahme;
  private  $zahlung_kreditkarte;
  private  $zahlung_paypal;
  private  $zahlung_bar;
  private  $zahlung_lastschrift;
  private  $zahlung_rechnung_sofort_de;
  private  $zahlung_rechnung_de;
  private  $knickfalz;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM firmendaten WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->firma=$result[firma];
    $this->absender=$result[absender];
    $this->sichtbar=$result[sichtbar];
    $this->barcode=$result[barcode];
    $this->schriftgroesse=$result[schriftgroesse];
    $this->betreffszeile=$result[betreffszeile];
    $this->dokumententext=$result[dokumententext];
    $this->tabellenbeschriftung=$result[tabellenbeschriftung];
    $this->tabelleninhalt=$result[tabelleninhalt];
    $this->zeilenuntertext=$result[zeilenuntertext];
    $this->freitext=$result[freitext];
    $this->infobox=$result[infobox];
    $this->spaltenbreite=$result[spaltenbreite];
    $this->footer_0_0=$result[footer_0_0];
    $this->footer_0_1=$result[footer_0_1];
    $this->footer_0_2=$result[footer_0_2];
    $this->footer_0_3=$result[footer_0_3];
    $this->footer_0_4=$result[footer_0_4];
    $this->footer_0_5=$result[footer_0_5];
    $this->footer_1_0=$result[footer_1_0];
    $this->footer_1_1=$result[footer_1_1];
    $this->footer_1_2=$result[footer_1_2];
    $this->footer_1_3=$result[footer_1_3];
    $this->footer_1_4=$result[footer_1_4];
    $this->footer_1_5=$result[footer_1_5];
    $this->footer_2_0=$result[footer_2_0];
    $this->footer_2_1=$result[footer_2_1];
    $this->footer_2_2=$result[footer_2_2];
    $this->footer_2_3=$result[footer_2_3];
    $this->footer_2_4=$result[footer_2_4];
    $this->footer_2_5=$result[footer_2_5];
    $this->footer_3_0=$result[footer_3_0];
    $this->footer_3_1=$result[footer_3_1];
    $this->footer_3_2=$result[footer_3_2];
    $this->footer_3_3=$result[footer_3_3];
    $this->footer_3_4=$result[footer_3_4];
    $this->footer_3_5=$result[footer_3_5];
    $this->footersichtbar=$result[footersichtbar];
    $this->hintergrund=$result[hintergrund];
    $this->logo=$result[logo];
    $this->logo_type=$result[logo_type];
    $this->briefpapier=$result[briefpapier];
    $this->briefpapier_type=$result[briefpapier_type];
    $this->benutzername=$result[benutzername];
    $this->passwort=$result[passwort];
    $this->host=$result[host];
    $this->port=$result[port];
    $this->mailssl=$result[mailssl];
    $this->signatur=$result[signatur];
    $this->email=$result[email];
    $this->absendername=$result[absendername];
    $this->bcc1=$result[bcc1];
    $this->bcc2=$result[bcc2];
    $this->firmenfarbe=$result[firmenfarbe];
    $this->name=$result[name];
    $this->strasse=$result[strasse];
    $this->plz=$result[plz];
    $this->ort=$result[ort];
    $this->steuernummer=$result[steuernummer];
    $this->startseite_wiki=$result[startseite_wiki];
    $this->datum=$result[datum];
    $this->projekt=$result[projekt];
    $this->brieftext=$result[brieftext];
    $this->next_angebot=$result[next_angebot];
    $this->next_auftrag=$result[next_auftrag];
    $this->next_gutschrift=$result[next_gutschrift];
    $this->next_lieferschein=$result[next_lieferschein];
    $this->next_bestellung=$result[next_bestellung];
    $this->next_rechnung=$result[next_rechnung];
    $this->next_kundennummer=$result[next_kundennummer];
    $this->next_lieferantennummer=$result[next_lieferantennummer];
    $this->next_mitarbeiternummer=$result[next_mitarbeiternummer];
    $this->next_waren=$result[next_waren];
    $this->next_sonstiges=$result[next_sonstiges];
    $this->next_produktion=$result[next_produktion];
    $this->next_kundennumer=$result[next_kundennumer];
    $this->next_produktionen=$result[next_produktionen];
    $this->wareneingang_kamera_waage=$result[wareneingang_kamera_waage];
    $this->layout_iconbar=$result[layout_iconbar];
    $this->seite_von_ausrichtung=$result[seite_von_ausrichtung];
    $this->seite_von_sichtbar=$result[seite_von_sichtbar];
    $this->rechnung_header=$result[rechnung_header];
    $this->lieferschein_header=$result[lieferschein_header];
    $this->angebot_header=$result[angebot_header];
    $this->auftrag_header=$result[auftrag_header];
    $this->gutschrift_header=$result[gutschrift_header];
    $this->bestellung_header=$result[bestellung_header];
    $this->rechnung_footer=$result[rechnung_footer];
    $this->lieferschein_footer=$result[lieferschein_footer];
    $this->angebot_footer=$result[angebot_footer];
    $this->auftrag_footer=$result[auftrag_footer];
    $this->gutschrift_footer=$result[gutschrift_footer];
    $this->bestellung_footer=$result[bestellung_footer];
    $this->eu_lieferung_vermerk=$result[eu_lieferung_vermerk];
    $this->rechnung_ohnebriefpapier=$result[rechnung_ohnebriefpapier];
    $this->lieferschein_ohnebriefpapier=$result[lieferschein_ohnebriefpapier];
    $this->angebot_ohnebriefpapier=$result[angebot_ohnebriefpapier];
    $this->auftrag_ohnebriefpapier=$result[auftrag_ohnebriefpapier];
    $this->gutschrift_ohnebriefpapier=$result[gutschrift_ohnebriefpapier];
    $this->bestellung_ohnebriefpapier=$result[bestellung_ohnebriefpapier];
    $this->abstand_adresszeileoben=$result[abstand_adresszeileoben];
    $this->abstand_boxrechtsoben=$result[abstand_boxrechtsoben];
    $this->abstand_betreffzeileoben=$result[abstand_betreffzeileoben];
    $this->abstand_artikeltabelleoben=$result[abstand_artikeltabelleoben];
    $this->arbeitsnachweis_header=$result[arbeitsnachweis_header];
    $this->arbeitsnachweis_footer=$result[arbeitsnachweis_footer];
    $this->arbeitsnachweis_ohnebriefpapier=$result[arbeitsnachweis_ohnebriefpapier];
    $this->next_arbeitsnachweis=$result[next_arbeitsnachweis];
    $this->parameterundfreifelder=$result[parameterundfreifelder];
    $this->freifeld1=$result[freifeld1];
    $this->freifeld2=$result[freifeld2];
    $this->freifeld3=$result[freifeld3];
    $this->freifeld4=$result[freifeld4];
    $this->freifeld5=$result[freifeld5];
    $this->freifeld6=$result[freifeld6];
    $this->artikel_suche_kurztext=$result[artikel_suche_kurztext];
    $this->externeinkauf=$result[externeinkauf];
    $this->schriftart=$result[schriftart];
    $this->externereinkauf=$result[externereinkauf];
    $this->next_reisekosten=$result[next_reisekosten];
    $this->projektnummerimdokument=$result[projektnummerimdokument];
    $this->mailanstellesmtp=$result[mailanstellesmtp];
    $this->herstellernummerimdokument=$result[herstellernummerimdokument];
    $this->standardmarge=$result[standardmarge];
    $this->zahlungsweise=$result[zahlungsweise];
    $this->zahlungszieltage=$result[zahlungszieltage];
    $this->zahlungszielskonto=$result[zahlungszielskonto];
    $this->zahlungszieltageskonto=$result[zahlungszieltageskonto];
    $this->zahlung_rechnung=$result[zahlung_rechnung];
    $this->zahlung_vorkasse=$result[zahlung_vorkasse];
    $this->zahlung_nachnahme=$result[zahlung_nachnahme];
    $this->zahlung_kreditkarte=$result[zahlung_kreditkarte];
    $this->zahlung_paypal=$result[zahlung_paypal];
    $this->zahlung_bar=$result[zahlung_bar];
    $this->zahlung_lastschrift=$result[zahlung_lastschrift];
    $this->zahlung_rechnung_sofort_de=$result[zahlung_rechnung_sofort_de];
    $this->zahlung_rechnung_de=$result[zahlung_rechnung_de];
    $this->knickfalz=$result[knickfalz];
  }

  public function Create()
  {
    $sql = "INSERT INTO firmendaten (id,firma,absender,sichtbar,barcode,schriftgroesse,betreffszeile,dokumententext,tabellenbeschriftung,tabelleninhalt,zeilenuntertext,freitext,infobox,spaltenbreite,footer_0_0,footer_0_1,footer_0_2,footer_0_3,footer_0_4,footer_0_5,footer_1_0,footer_1_1,footer_1_2,footer_1_3,footer_1_4,footer_1_5,footer_2_0,footer_2_1,footer_2_2,footer_2_3,footer_2_4,footer_2_5,footer_3_0,footer_3_1,footer_3_2,footer_3_3,footer_3_4,footer_3_5,footersichtbar,hintergrund,logo,logo_type,briefpapier,briefpapier_type,benutzername,passwort,host,port,mailssl,signatur,email,absendername,bcc1,bcc2,firmenfarbe,name,strasse,plz,ort,steuernummer,startseite_wiki,datum,projekt,brieftext,next_angebot,next_auftrag,next_gutschrift,next_lieferschein,next_bestellung,next_rechnung,next_kundennummer,next_lieferantennummer,next_mitarbeiternummer,next_waren,next_sonstiges,next_produktion,next_kundennumer,next_produktionen,wareneingang_kamera_waage,layout_iconbar,seite_von_ausrichtung,seite_von_sichtbar,rechnung_header,lieferschein_header,angebot_header,auftrag_header,gutschrift_header,bestellung_header,rechnung_footer,lieferschein_footer,angebot_footer,auftrag_footer,gutschrift_footer,bestellung_footer,eu_lieferung_vermerk,rechnung_ohnebriefpapier,lieferschein_ohnebriefpapier,angebot_ohnebriefpapier,auftrag_ohnebriefpapier,gutschrift_ohnebriefpapier,bestellung_ohnebriefpapier,abstand_adresszeileoben,abstand_boxrechtsoben,abstand_betreffzeileoben,abstand_artikeltabelleoben,arbeitsnachweis_header,arbeitsnachweis_footer,arbeitsnachweis_ohnebriefpapier,next_arbeitsnachweis,parameterundfreifelder,freifeld1,freifeld2,freifeld3,freifeld4,freifeld5,freifeld6,artikel_suche_kurztext,externeinkauf,schriftart,externereinkauf,next_reisekosten,projektnummerimdokument,mailanstellesmtp,herstellernummerimdokument,standardmarge,zahlungsweise,zahlungszieltage,zahlungszielskonto,zahlungszieltageskonto,zahlung_rechnung,zahlung_vorkasse,zahlung_nachnahme,zahlung_kreditkarte,zahlung_paypal,zahlung_bar,zahlung_lastschrift,zahlung_rechnung_sofort_de,zahlung_rechnung_de,knickfalz)
      VALUES('','{$this->firma}','{$this->absender}','{$this->sichtbar}','{$this->barcode}','{$this->schriftgroesse}','{$this->betreffszeile}','{$this->dokumententext}','{$this->tabellenbeschriftung}','{$this->tabelleninhalt}','{$this->zeilenuntertext}','{$this->freitext}','{$this->infobox}','{$this->spaltenbreite}','{$this->footer_0_0}','{$this->footer_0_1}','{$this->footer_0_2}','{$this->footer_0_3}','{$this->footer_0_4}','{$this->footer_0_5}','{$this->footer_1_0}','{$this->footer_1_1}','{$this->footer_1_2}','{$this->footer_1_3}','{$this->footer_1_4}','{$this->footer_1_5}','{$this->footer_2_0}','{$this->footer_2_1}','{$this->footer_2_2}','{$this->footer_2_3}','{$this->footer_2_4}','{$this->footer_2_5}','{$this->footer_3_0}','{$this->footer_3_1}','{$this->footer_3_2}','{$this->footer_3_3}','{$this->footer_3_4}','{$this->footer_3_5}','{$this->footersichtbar}','{$this->hintergrund}','{$this->logo}','{$this->logo_type}','{$this->briefpapier}','{$this->briefpapier_type}','{$this->benutzername}','{$this->passwort}','{$this->host}','{$this->port}','{$this->mailssl}','{$this->signatur}','{$this->email}','{$this->absendername}','{$this->bcc1}','{$this->bcc2}','{$this->firmenfarbe}','{$this->name}','{$this->strasse}','{$this->plz}','{$this->ort}','{$this->steuernummer}','{$this->startseite_wiki}','{$this->datum}','{$this->projekt}','{$this->brieftext}','{$this->next_angebot}','{$this->next_auftrag}','{$this->next_gutschrift}','{$this->next_lieferschein}','{$this->next_bestellung}','{$this->next_rechnung}','{$this->next_kundennummer}','{$this->next_lieferantennummer}','{$this->next_mitarbeiternummer}','{$this->next_waren}','{$this->next_sonstiges}','{$this->next_produktion}','{$this->next_kundennumer}','{$this->next_produktionen}','{$this->wareneingang_kamera_waage}','{$this->layout_iconbar}','{$this->seite_von_ausrichtung}','{$this->seite_von_sichtbar}','{$this->rechnung_header}','{$this->lieferschein_header}','{$this->angebot_header}','{$this->auftrag_header}','{$this->gutschrift_header}','{$this->bestellung_header}','{$this->rechnung_footer}','{$this->lieferschein_footer}','{$this->angebot_footer}','{$this->auftrag_footer}','{$this->gutschrift_footer}','{$this->bestellung_footer}','{$this->eu_lieferung_vermerk}','{$this->rechnung_ohnebriefpapier}','{$this->lieferschein_ohnebriefpapier}','{$this->angebot_ohnebriefpapier}','{$this->auftrag_ohnebriefpapier}','{$this->gutschrift_ohnebriefpapier}','{$this->bestellung_ohnebriefpapier}','{$this->abstand_adresszeileoben}','{$this->abstand_boxrechtsoben}','{$this->abstand_betreffzeileoben}','{$this->abstand_artikeltabelleoben}','{$this->arbeitsnachweis_header}','{$this->arbeitsnachweis_footer}','{$this->arbeitsnachweis_ohnebriefpapier}','{$this->next_arbeitsnachweis}','{$this->parameterundfreifelder}','{$this->freifeld1}','{$this->freifeld2}','{$this->freifeld3}','{$this->freifeld4}','{$this->freifeld5}','{$this->freifeld6}','{$this->artikel_suche_kurztext}','{$this->externeinkauf}','{$this->schriftart}','{$this->externereinkauf}','{$this->next_reisekosten}','{$this->projektnummerimdokument}','{$this->mailanstellesmtp}','{$this->herstellernummerimdokument}','{$this->standardmarge}','{$this->zahlungsweise}','{$this->zahlungszieltage}','{$this->zahlungszielskonto}','{$this->zahlungszieltageskonto}','{$this->zahlung_rechnung}','{$this->zahlung_vorkasse}','{$this->zahlung_nachnahme}','{$this->zahlung_kreditkarte}','{$this->zahlung_paypal}','{$this->zahlung_bar}','{$this->zahlung_lastschrift}','{$this->zahlung_rechnung_sofort_de}','{$this->zahlung_rechnung_de}','{$this->knickfalz}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE firmendaten SET
      firma='{$this->firma}',
      absender='{$this->absender}',
      sichtbar='{$this->sichtbar}',
      barcode='{$this->barcode}',
      schriftgroesse='{$this->schriftgroesse}',
      betreffszeile='{$this->betreffszeile}',
      dokumententext='{$this->dokumententext}',
      tabellenbeschriftung='{$this->tabellenbeschriftung}',
      tabelleninhalt='{$this->tabelleninhalt}',
      zeilenuntertext='{$this->zeilenuntertext}',
      freitext='{$this->freitext}',
      infobox='{$this->infobox}',
      spaltenbreite='{$this->spaltenbreite}',
      footer_0_0='{$this->footer_0_0}',
      footer_0_1='{$this->footer_0_1}',
      footer_0_2='{$this->footer_0_2}',
      footer_0_3='{$this->footer_0_3}',
      footer_0_4='{$this->footer_0_4}',
      footer_0_5='{$this->footer_0_5}',
      footer_1_0='{$this->footer_1_0}',
      footer_1_1='{$this->footer_1_1}',
      footer_1_2='{$this->footer_1_2}',
      footer_1_3='{$this->footer_1_3}',
      footer_1_4='{$this->footer_1_4}',
      footer_1_5='{$this->footer_1_5}',
      footer_2_0='{$this->footer_2_0}',
      footer_2_1='{$this->footer_2_1}',
      footer_2_2='{$this->footer_2_2}',
      footer_2_3='{$this->footer_2_3}',
      footer_2_4='{$this->footer_2_4}',
      footer_2_5='{$this->footer_2_5}',
      footer_3_0='{$this->footer_3_0}',
      footer_3_1='{$this->footer_3_1}',
      footer_3_2='{$this->footer_3_2}',
      footer_3_3='{$this->footer_3_3}',
      footer_3_4='{$this->footer_3_4}',
      footer_3_5='{$this->footer_3_5}',
      footersichtbar='{$this->footersichtbar}',
      hintergrund='{$this->hintergrund}',
      logo='{$this->logo}',
      logo_type='{$this->logo_type}',
      briefpapier='{$this->briefpapier}',
      briefpapier_type='{$this->briefpapier_type}',
      benutzername='{$this->benutzername}',
      passwort='{$this->passwort}',
      host='{$this->host}',
      port='{$this->port}',
      mailssl='{$this->mailssl}',
      signatur='{$this->signatur}',
      email='{$this->email}',
      absendername='{$this->absendername}',
      bcc1='{$this->bcc1}',
      bcc2='{$this->bcc2}',
      firmenfarbe='{$this->firmenfarbe}',
      name='{$this->name}',
      strasse='{$this->strasse}',
      plz='{$this->plz}',
      ort='{$this->ort}',
      steuernummer='{$this->steuernummer}',
      startseite_wiki='{$this->startseite_wiki}',
      datum='{$this->datum}',
      projekt='{$this->projekt}',
      brieftext='{$this->brieftext}',
      next_angebot='{$this->next_angebot}',
      next_auftrag='{$this->next_auftrag}',
      next_gutschrift='{$this->next_gutschrift}',
      next_lieferschein='{$this->next_lieferschein}',
      next_bestellung='{$this->next_bestellung}',
      next_rechnung='{$this->next_rechnung}',
      next_kundennummer='{$this->next_kundennummer}',
      next_lieferantennummer='{$this->next_lieferantennummer}',
      next_mitarbeiternummer='{$this->next_mitarbeiternummer}',
      next_waren='{$this->next_waren}',
      next_sonstiges='{$this->next_sonstiges}',
      next_produktion='{$this->next_produktion}',
      next_kundennumer='{$this->next_kundennumer}',
      next_produktionen='{$this->next_produktionen}',
      wareneingang_kamera_waage='{$this->wareneingang_kamera_waage}',
      layout_iconbar='{$this->layout_iconbar}',
      seite_von_ausrichtung='{$this->seite_von_ausrichtung}',
      seite_von_sichtbar='{$this->seite_von_sichtbar}',
      rechnung_header='{$this->rechnung_header}',
      lieferschein_header='{$this->lieferschein_header}',
      angebot_header='{$this->angebot_header}',
      auftrag_header='{$this->auftrag_header}',
      gutschrift_header='{$this->gutschrift_header}',
      bestellung_header='{$this->bestellung_header}',
      rechnung_footer='{$this->rechnung_footer}',
      lieferschein_footer='{$this->lieferschein_footer}',
      angebot_footer='{$this->angebot_footer}',
      auftrag_footer='{$this->auftrag_footer}',
      gutschrift_footer='{$this->gutschrift_footer}',
      bestellung_footer='{$this->bestellung_footer}',
      eu_lieferung_vermerk='{$this->eu_lieferung_vermerk}',
      rechnung_ohnebriefpapier='{$this->rechnung_ohnebriefpapier}',
      lieferschein_ohnebriefpapier='{$this->lieferschein_ohnebriefpapier}',
      angebot_ohnebriefpapier='{$this->angebot_ohnebriefpapier}',
      auftrag_ohnebriefpapier='{$this->auftrag_ohnebriefpapier}',
      gutschrift_ohnebriefpapier='{$this->gutschrift_ohnebriefpapier}',
      bestellung_ohnebriefpapier='{$this->bestellung_ohnebriefpapier}',
      abstand_adresszeileoben='{$this->abstand_adresszeileoben}',
      abstand_boxrechtsoben='{$this->abstand_boxrechtsoben}',
      abstand_betreffzeileoben='{$this->abstand_betreffzeileoben}',
      abstand_artikeltabelleoben='{$this->abstand_artikeltabelleoben}',
      arbeitsnachweis_header='{$this->arbeitsnachweis_header}',
      arbeitsnachweis_footer='{$this->arbeitsnachweis_footer}',
      arbeitsnachweis_ohnebriefpapier='{$this->arbeitsnachweis_ohnebriefpapier}',
      next_arbeitsnachweis='{$this->next_arbeitsnachweis}',
      parameterundfreifelder='{$this->parameterundfreifelder}',
      freifeld1='{$this->freifeld1}',
      freifeld2='{$this->freifeld2}',
      freifeld3='{$this->freifeld3}',
      freifeld4='{$this->freifeld4}',
      freifeld5='{$this->freifeld5}',
      freifeld6='{$this->freifeld6}',
      artikel_suche_kurztext='{$this->artikel_suche_kurztext}',
      externeinkauf='{$this->externeinkauf}',
      schriftart='{$this->schriftart}',
      externereinkauf='{$this->externereinkauf}',
      next_reisekosten='{$this->next_reisekosten}',
      projektnummerimdokument='{$this->projektnummerimdokument}',
      mailanstellesmtp='{$this->mailanstellesmtp}',
      herstellernummerimdokument='{$this->herstellernummerimdokument}',
      standardmarge='{$this->standardmarge}',
      zahlungsweise='{$this->zahlungsweise}',
      zahlungszieltage='{$this->zahlungszieltage}',
      zahlungszielskonto='{$this->zahlungszielskonto}',
      zahlungszieltageskonto='{$this->zahlungszieltageskonto}',
      zahlung_rechnung='{$this->zahlung_rechnung}',
      zahlung_vorkasse='{$this->zahlung_vorkasse}',
      zahlung_nachnahme='{$this->zahlung_nachnahme}',
      zahlung_kreditkarte='{$this->zahlung_kreditkarte}',
      zahlung_paypal='{$this->zahlung_paypal}',
      zahlung_bar='{$this->zahlung_bar}',
      zahlung_lastschrift='{$this->zahlung_lastschrift}',
      zahlung_rechnung_sofort_de='{$this->zahlung_rechnung_sofort_de}',
      zahlung_rechnung_de='{$this->zahlung_rechnung_de}',
      knickfalz='{$this->knickfalz}'
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

    $sql = "DELETE FROM firmendaten WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->firma="";
    $this->absender="";
    $this->sichtbar="";
    $this->barcode="";
    $this->schriftgroesse="";
    $this->betreffszeile="";
    $this->dokumententext="";
    $this->tabellenbeschriftung="";
    $this->tabelleninhalt="";
    $this->zeilenuntertext="";
    $this->freitext="";
    $this->infobox="";
    $this->spaltenbreite="";
    $this->footer_0_0="";
    $this->footer_0_1="";
    $this->footer_0_2="";
    $this->footer_0_3="";
    $this->footer_0_4="";
    $this->footer_0_5="";
    $this->footer_1_0="";
    $this->footer_1_1="";
    $this->footer_1_2="";
    $this->footer_1_3="";
    $this->footer_1_4="";
    $this->footer_1_5="";
    $this->footer_2_0="";
    $this->footer_2_1="";
    $this->footer_2_2="";
    $this->footer_2_3="";
    $this->footer_2_4="";
    $this->footer_2_5="";
    $this->footer_3_0="";
    $this->footer_3_1="";
    $this->footer_3_2="";
    $this->footer_3_3="";
    $this->footer_3_4="";
    $this->footer_3_5="";
    $this->footersichtbar="";
    $this->hintergrund="";
    $this->logo="";
    $this->logo_type="";
    $this->briefpapier="";
    $this->briefpapier_type="";
    $this->benutzername="";
    $this->passwort="";
    $this->host="";
    $this->port="";
    $this->mailssl="";
    $this->signatur="";
    $this->email="";
    $this->absendername="";
    $this->bcc1="";
    $this->bcc2="";
    $this->firmenfarbe="";
    $this->name="";
    $this->strasse="";
    $this->plz="";
    $this->ort="";
    $this->steuernummer="";
    $this->startseite_wiki="";
    $this->datum="";
    $this->projekt="";
    $this->brieftext="";
    $this->next_angebot="";
    $this->next_auftrag="";
    $this->next_gutschrift="";
    $this->next_lieferschein="";
    $this->next_bestellung="";
    $this->next_rechnung="";
    $this->next_kundennummer="";
    $this->next_lieferantennummer="";
    $this->next_mitarbeiternummer="";
    $this->next_waren="";
    $this->next_sonstiges="";
    $this->next_produktion="";
    $this->next_kundennumer="";
    $this->next_produktionen="";
    $this->wareneingang_kamera_waage="";
    $this->layout_iconbar="";
    $this->seite_von_ausrichtung="";
    $this->seite_von_sichtbar="";
    $this->rechnung_header="";
    $this->lieferschein_header="";
    $this->angebot_header="";
    $this->auftrag_header="";
    $this->gutschrift_header="";
    $this->bestellung_header="";
    $this->rechnung_footer="";
    $this->lieferschein_footer="";
    $this->angebot_footer="";
    $this->auftrag_footer="";
    $this->gutschrift_footer="";
    $this->bestellung_footer="";
    $this->eu_lieferung_vermerk="";
    $this->rechnung_ohnebriefpapier="";
    $this->lieferschein_ohnebriefpapier="";
    $this->angebot_ohnebriefpapier="";
    $this->auftrag_ohnebriefpapier="";
    $this->gutschrift_ohnebriefpapier="";
    $this->bestellung_ohnebriefpapier="";
    $this->abstand_adresszeileoben="";
    $this->abstand_boxrechtsoben="";
    $this->abstand_betreffzeileoben="";
    $this->abstand_artikeltabelleoben="";
    $this->arbeitsnachweis_header="";
    $this->arbeitsnachweis_footer="";
    $this->arbeitsnachweis_ohnebriefpapier="";
    $this->next_arbeitsnachweis="";
    $this->parameterundfreifelder="";
    $this->freifeld1="";
    $this->freifeld2="";
    $this->freifeld3="";
    $this->freifeld4="";
    $this->freifeld5="";
    $this->freifeld6="";
    $this->artikel_suche_kurztext="";
    $this->externeinkauf="";
    $this->schriftart="";
    $this->externereinkauf="";
    $this->next_reisekosten="";
    $this->projektnummerimdokument="";
    $this->mailanstellesmtp="";
    $this->herstellernummerimdokument="";
    $this->standardmarge="";
    $this->zahlungsweise="";
    $this->zahlungszieltage="";
    $this->zahlungszielskonto="";
    $this->zahlungszieltageskonto="";
    $this->zahlung_rechnung="";
    $this->zahlung_vorkasse="";
    $this->zahlung_nachnahme="";
    $this->zahlung_kreditkarte="";
    $this->zahlung_paypal="";
    $this->zahlung_bar="";
    $this->zahlung_lastschrift="";
    $this->zahlung_rechnung_sofort_de="";
    $this->zahlung_rechnung_de="";
    $this->knickfalz="";
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
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetAbsender($value) { $this->absender=$value; }
  function GetAbsender() { return $this->absender; }
  function SetSichtbar($value) { $this->sichtbar=$value; }
  function GetSichtbar() { return $this->sichtbar; }
  function SetBarcode($value) { $this->barcode=$value; }
  function GetBarcode() { return $this->barcode; }
  function SetSchriftgroesse($value) { $this->schriftgroesse=$value; }
  function GetSchriftgroesse() { return $this->schriftgroesse; }
  function SetBetreffszeile($value) { $this->betreffszeile=$value; }
  function GetBetreffszeile() { return $this->betreffszeile; }
  function SetDokumententext($value) { $this->dokumententext=$value; }
  function GetDokumententext() { return $this->dokumententext; }
  function SetTabellenbeschriftung($value) { $this->tabellenbeschriftung=$value; }
  function GetTabellenbeschriftung() { return $this->tabellenbeschriftung; }
  function SetTabelleninhalt($value) { $this->tabelleninhalt=$value; }
  function GetTabelleninhalt() { return $this->tabelleninhalt; }
  function SetZeilenuntertext($value) { $this->zeilenuntertext=$value; }
  function GetZeilenuntertext() { return $this->zeilenuntertext; }
  function SetFreitext($value) { $this->freitext=$value; }
  function GetFreitext() { return $this->freitext; }
  function SetInfobox($value) { $this->infobox=$value; }
  function GetInfobox() { return $this->infobox; }
  function SetSpaltenbreite($value) { $this->spaltenbreite=$value; }
  function GetSpaltenbreite() { return $this->spaltenbreite; }
  function SetFooter_0_0($value) { $this->footer_0_0=$value; }
  function GetFooter_0_0() { return $this->footer_0_0; }
  function SetFooter_0_1($value) { $this->footer_0_1=$value; }
  function GetFooter_0_1() { return $this->footer_0_1; }
  function SetFooter_0_2($value) { $this->footer_0_2=$value; }
  function GetFooter_0_2() { return $this->footer_0_2; }
  function SetFooter_0_3($value) { $this->footer_0_3=$value; }
  function GetFooter_0_3() { return $this->footer_0_3; }
  function SetFooter_0_4($value) { $this->footer_0_4=$value; }
  function GetFooter_0_4() { return $this->footer_0_4; }
  function SetFooter_0_5($value) { $this->footer_0_5=$value; }
  function GetFooter_0_5() { return $this->footer_0_5; }
  function SetFooter_1_0($value) { $this->footer_1_0=$value; }
  function GetFooter_1_0() { return $this->footer_1_0; }
  function SetFooter_1_1($value) { $this->footer_1_1=$value; }
  function GetFooter_1_1() { return $this->footer_1_1; }
  function SetFooter_1_2($value) { $this->footer_1_2=$value; }
  function GetFooter_1_2() { return $this->footer_1_2; }
  function SetFooter_1_3($value) { $this->footer_1_3=$value; }
  function GetFooter_1_3() { return $this->footer_1_3; }
  function SetFooter_1_4($value) { $this->footer_1_4=$value; }
  function GetFooter_1_4() { return $this->footer_1_4; }
  function SetFooter_1_5($value) { $this->footer_1_5=$value; }
  function GetFooter_1_5() { return $this->footer_1_5; }
  function SetFooter_2_0($value) { $this->footer_2_0=$value; }
  function GetFooter_2_0() { return $this->footer_2_0; }
  function SetFooter_2_1($value) { $this->footer_2_1=$value; }
  function GetFooter_2_1() { return $this->footer_2_1; }
  function SetFooter_2_2($value) { $this->footer_2_2=$value; }
  function GetFooter_2_2() { return $this->footer_2_2; }
  function SetFooter_2_3($value) { $this->footer_2_3=$value; }
  function GetFooter_2_3() { return $this->footer_2_3; }
  function SetFooter_2_4($value) { $this->footer_2_4=$value; }
  function GetFooter_2_4() { return $this->footer_2_4; }
  function SetFooter_2_5($value) { $this->footer_2_5=$value; }
  function GetFooter_2_5() { return $this->footer_2_5; }
  function SetFooter_3_0($value) { $this->footer_3_0=$value; }
  function GetFooter_3_0() { return $this->footer_3_0; }
  function SetFooter_3_1($value) { $this->footer_3_1=$value; }
  function GetFooter_3_1() { return $this->footer_3_1; }
  function SetFooter_3_2($value) { $this->footer_3_2=$value; }
  function GetFooter_3_2() { return $this->footer_3_2; }
  function SetFooter_3_3($value) { $this->footer_3_3=$value; }
  function GetFooter_3_3() { return $this->footer_3_3; }
  function SetFooter_3_4($value) { $this->footer_3_4=$value; }
  function GetFooter_3_4() { return $this->footer_3_4; }
  function SetFooter_3_5($value) { $this->footer_3_5=$value; }
  function GetFooter_3_5() { return $this->footer_3_5; }
  function SetFootersichtbar($value) { $this->footersichtbar=$value; }
  function GetFootersichtbar() { return $this->footersichtbar; }
  function SetHintergrund($value) { $this->hintergrund=$value; }
  function GetHintergrund() { return $this->hintergrund; }
  function SetLogo($value) { $this->logo=$value; }
  function GetLogo() { return $this->logo; }
  function SetLogo_Type($value) { $this->logo_type=$value; }
  function GetLogo_Type() { return $this->logo_type; }
  function SetBriefpapier($value) { $this->briefpapier=$value; }
  function GetBriefpapier() { return $this->briefpapier; }
  function SetBriefpapier_Type($value) { $this->briefpapier_type=$value; }
  function GetBriefpapier_Type() { return $this->briefpapier_type; }
  function SetBenutzername($value) { $this->benutzername=$value; }
  function GetBenutzername() { return $this->benutzername; }
  function SetPasswort($value) { $this->passwort=$value; }
  function GetPasswort() { return $this->passwort; }
  function SetHost($value) { $this->host=$value; }
  function GetHost() { return $this->host; }
  function SetPort($value) { $this->port=$value; }
  function GetPort() { return $this->port; }
  function SetMailssl($value) { $this->mailssl=$value; }
  function GetMailssl() { return $this->mailssl; }
  function SetSignatur($value) { $this->signatur=$value; }
  function GetSignatur() { return $this->signatur; }
  function SetEmail($value) { $this->email=$value; }
  function GetEmail() { return $this->email; }
  function SetAbsendername($value) { $this->absendername=$value; }
  function GetAbsendername() { return $this->absendername; }
  function SetBcc1($value) { $this->bcc1=$value; }
  function GetBcc1() { return $this->bcc1; }
  function SetBcc2($value) { $this->bcc2=$value; }
  function GetBcc2() { return $this->bcc2; }
  function SetFirmenfarbe($value) { $this->firmenfarbe=$value; }
  function GetFirmenfarbe() { return $this->firmenfarbe; }
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetStrasse($value) { $this->strasse=$value; }
  function GetStrasse() { return $this->strasse; }
  function SetPlz($value) { $this->plz=$value; }
  function GetPlz() { return $this->plz; }
  function SetOrt($value) { $this->ort=$value; }
  function GetOrt() { return $this->ort; }
  function SetSteuernummer($value) { $this->steuernummer=$value; }
  function GetSteuernummer() { return $this->steuernummer; }
  function SetStartseite_Wiki($value) { $this->startseite_wiki=$value; }
  function GetStartseite_Wiki() { return $this->startseite_wiki; }
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetBrieftext($value) { $this->brieftext=$value; }
  function GetBrieftext() { return $this->brieftext; }
  function SetNext_Angebot($value) { $this->next_angebot=$value; }
  function GetNext_Angebot() { return $this->next_angebot; }
  function SetNext_Auftrag($value) { $this->next_auftrag=$value; }
  function GetNext_Auftrag() { return $this->next_auftrag; }
  function SetNext_Gutschrift($value) { $this->next_gutschrift=$value; }
  function GetNext_Gutschrift() { return $this->next_gutschrift; }
  function SetNext_Lieferschein($value) { $this->next_lieferschein=$value; }
  function GetNext_Lieferschein() { return $this->next_lieferschein; }
  function SetNext_Bestellung($value) { $this->next_bestellung=$value; }
  function GetNext_Bestellung() { return $this->next_bestellung; }
  function SetNext_Rechnung($value) { $this->next_rechnung=$value; }
  function GetNext_Rechnung() { return $this->next_rechnung; }
  function SetNext_Kundennummer($value) { $this->next_kundennummer=$value; }
  function GetNext_Kundennummer() { return $this->next_kundennummer; }
  function SetNext_Lieferantennummer($value) { $this->next_lieferantennummer=$value; }
  function GetNext_Lieferantennummer() { return $this->next_lieferantennummer; }
  function SetNext_Mitarbeiternummer($value) { $this->next_mitarbeiternummer=$value; }
  function GetNext_Mitarbeiternummer() { return $this->next_mitarbeiternummer; }
  function SetNext_Waren($value) { $this->next_waren=$value; }
  function GetNext_Waren() { return $this->next_waren; }
  function SetNext_Sonstiges($value) { $this->next_sonstiges=$value; }
  function GetNext_Sonstiges() { return $this->next_sonstiges; }
  function SetNext_Produktion($value) { $this->next_produktion=$value; }
  function GetNext_Produktion() { return $this->next_produktion; }
  function SetNext_Kundennumer($value) { $this->next_kundennumer=$value; }
  function GetNext_Kundennumer() { return $this->next_kundennumer; }
  function SetNext_Produktionen($value) { $this->next_produktionen=$value; }
  function GetNext_Produktionen() { return $this->next_produktionen; }
  function SetWareneingang_Kamera_Waage($value) { $this->wareneingang_kamera_waage=$value; }
  function GetWareneingang_Kamera_Waage() { return $this->wareneingang_kamera_waage; }
  function SetLayout_Iconbar($value) { $this->layout_iconbar=$value; }
  function GetLayout_Iconbar() { return $this->layout_iconbar; }
  function SetSeite_Von_Ausrichtung($value) { $this->seite_von_ausrichtung=$value; }
  function GetSeite_Von_Ausrichtung() { return $this->seite_von_ausrichtung; }
  function SetSeite_Von_Sichtbar($value) { $this->seite_von_sichtbar=$value; }
  function GetSeite_Von_Sichtbar() { return $this->seite_von_sichtbar; }
  function SetRechnung_Header($value) { $this->rechnung_header=$value; }
  function GetRechnung_Header() { return $this->rechnung_header; }
  function SetLieferschein_Header($value) { $this->lieferschein_header=$value; }
  function GetLieferschein_Header() { return $this->lieferschein_header; }
  function SetAngebot_Header($value) { $this->angebot_header=$value; }
  function GetAngebot_Header() { return $this->angebot_header; }
  function SetAuftrag_Header($value) { $this->auftrag_header=$value; }
  function GetAuftrag_Header() { return $this->auftrag_header; }
  function SetGutschrift_Header($value) { $this->gutschrift_header=$value; }
  function GetGutschrift_Header() { return $this->gutschrift_header; }
  function SetBestellung_Header($value) { $this->bestellung_header=$value; }
  function GetBestellung_Header() { return $this->bestellung_header; }
  function SetRechnung_Footer($value) { $this->rechnung_footer=$value; }
  function GetRechnung_Footer() { return $this->rechnung_footer; }
  function SetLieferschein_Footer($value) { $this->lieferschein_footer=$value; }
  function GetLieferschein_Footer() { return $this->lieferschein_footer; }
  function SetAngebot_Footer($value) { $this->angebot_footer=$value; }
  function GetAngebot_Footer() { return $this->angebot_footer; }
  function SetAuftrag_Footer($value) { $this->auftrag_footer=$value; }
  function GetAuftrag_Footer() { return $this->auftrag_footer; }
  function SetGutschrift_Footer($value) { $this->gutschrift_footer=$value; }
  function GetGutschrift_Footer() { return $this->gutschrift_footer; }
  function SetBestellung_Footer($value) { $this->bestellung_footer=$value; }
  function GetBestellung_Footer() { return $this->bestellung_footer; }
  function SetEu_Lieferung_Vermerk($value) { $this->eu_lieferung_vermerk=$value; }
  function GetEu_Lieferung_Vermerk() { return $this->eu_lieferung_vermerk; }
  function SetRechnung_Ohnebriefpapier($value) { $this->rechnung_ohnebriefpapier=$value; }
  function GetRechnung_Ohnebriefpapier() { return $this->rechnung_ohnebriefpapier; }
  function SetLieferschein_Ohnebriefpapier($value) { $this->lieferschein_ohnebriefpapier=$value; }
  function GetLieferschein_Ohnebriefpapier() { return $this->lieferschein_ohnebriefpapier; }
  function SetAngebot_Ohnebriefpapier($value) { $this->angebot_ohnebriefpapier=$value; }
  function GetAngebot_Ohnebriefpapier() { return $this->angebot_ohnebriefpapier; }
  function SetAuftrag_Ohnebriefpapier($value) { $this->auftrag_ohnebriefpapier=$value; }
  function GetAuftrag_Ohnebriefpapier() { return $this->auftrag_ohnebriefpapier; }
  function SetGutschrift_Ohnebriefpapier($value) { $this->gutschrift_ohnebriefpapier=$value; }
  function GetGutschrift_Ohnebriefpapier() { return $this->gutschrift_ohnebriefpapier; }
  function SetBestellung_Ohnebriefpapier($value) { $this->bestellung_ohnebriefpapier=$value; }
  function GetBestellung_Ohnebriefpapier() { return $this->bestellung_ohnebriefpapier; }
  function SetAbstand_Adresszeileoben($value) { $this->abstand_adresszeileoben=$value; }
  function GetAbstand_Adresszeileoben() { return $this->abstand_adresszeileoben; }
  function SetAbstand_Boxrechtsoben($value) { $this->abstand_boxrechtsoben=$value; }
  function GetAbstand_Boxrechtsoben() { return $this->abstand_boxrechtsoben; }
  function SetAbstand_Betreffzeileoben($value) { $this->abstand_betreffzeileoben=$value; }
  function GetAbstand_Betreffzeileoben() { return $this->abstand_betreffzeileoben; }
  function SetAbstand_Artikeltabelleoben($value) { $this->abstand_artikeltabelleoben=$value; }
  function GetAbstand_Artikeltabelleoben() { return $this->abstand_artikeltabelleoben; }
  function SetArbeitsnachweis_Header($value) { $this->arbeitsnachweis_header=$value; }
  function GetArbeitsnachweis_Header() { return $this->arbeitsnachweis_header; }
  function SetArbeitsnachweis_Footer($value) { $this->arbeitsnachweis_footer=$value; }
  function GetArbeitsnachweis_Footer() { return $this->arbeitsnachweis_footer; }
  function SetArbeitsnachweis_Ohnebriefpapier($value) { $this->arbeitsnachweis_ohnebriefpapier=$value; }
  function GetArbeitsnachweis_Ohnebriefpapier() { return $this->arbeitsnachweis_ohnebriefpapier; }
  function SetNext_Arbeitsnachweis($value) { $this->next_arbeitsnachweis=$value; }
  function GetNext_Arbeitsnachweis() { return $this->next_arbeitsnachweis; }
  function SetParameterundfreifelder($value) { $this->parameterundfreifelder=$value; }
  function GetParameterundfreifelder() { return $this->parameterundfreifelder; }
  function SetFreifeld1($value) { $this->freifeld1=$value; }
  function GetFreifeld1() { return $this->freifeld1; }
  function SetFreifeld2($value) { $this->freifeld2=$value; }
  function GetFreifeld2() { return $this->freifeld2; }
  function SetFreifeld3($value) { $this->freifeld3=$value; }
  function GetFreifeld3() { return $this->freifeld3; }
  function SetFreifeld4($value) { $this->freifeld4=$value; }
  function GetFreifeld4() { return $this->freifeld4; }
  function SetFreifeld5($value) { $this->freifeld5=$value; }
  function GetFreifeld5() { return $this->freifeld5; }
  function SetFreifeld6($value) { $this->freifeld6=$value; }
  function GetFreifeld6() { return $this->freifeld6; }
  function SetArtikel_Suche_Kurztext($value) { $this->artikel_suche_kurztext=$value; }
  function GetArtikel_Suche_Kurztext() { return $this->artikel_suche_kurztext; }
  function SetExterneinkauf($value) { $this->externeinkauf=$value; }
  function GetExterneinkauf() { return $this->externeinkauf; }
  function SetSchriftart($value) { $this->schriftart=$value; }
  function GetSchriftart() { return $this->schriftart; }
  function SetExternereinkauf($value) { $this->externereinkauf=$value; }
  function GetExternereinkauf() { return $this->externereinkauf; }
  function SetNext_Reisekosten($value) { $this->next_reisekosten=$value; }
  function GetNext_Reisekosten() { return $this->next_reisekosten; }
  function SetProjektnummerimdokument($value) { $this->projektnummerimdokument=$value; }
  function GetProjektnummerimdokument() { return $this->projektnummerimdokument; }
  function SetMailanstellesmtp($value) { $this->mailanstellesmtp=$value; }
  function GetMailanstellesmtp() { return $this->mailanstellesmtp; }
  function SetHerstellernummerimdokument($value) { $this->herstellernummerimdokument=$value; }
  function GetHerstellernummerimdokument() { return $this->herstellernummerimdokument; }
  function SetStandardmarge($value) { $this->standardmarge=$value; }
  function GetStandardmarge() { return $this->standardmarge; }
  function SetZahlungsweise($value) { $this->zahlungsweise=$value; }
  function GetZahlungsweise() { return $this->zahlungsweise; }
  function SetZahlungszieltage($value) { $this->zahlungszieltage=$value; }
  function GetZahlungszieltage() { return $this->zahlungszieltage; }
  function SetZahlungszielskonto($value) { $this->zahlungszielskonto=$value; }
  function GetZahlungszielskonto() { return $this->zahlungszielskonto; }
  function SetZahlungszieltageskonto($value) { $this->zahlungszieltageskonto=$value; }
  function GetZahlungszieltageskonto() { return $this->zahlungszieltageskonto; }
  function SetZahlung_Rechnung($value) { $this->zahlung_rechnung=$value; }
  function GetZahlung_Rechnung() { return $this->zahlung_rechnung; }
  function SetZahlung_Vorkasse($value) { $this->zahlung_vorkasse=$value; }
  function GetZahlung_Vorkasse() { return $this->zahlung_vorkasse; }
  function SetZahlung_Nachnahme($value) { $this->zahlung_nachnahme=$value; }
  function GetZahlung_Nachnahme() { return $this->zahlung_nachnahme; }
  function SetZahlung_Kreditkarte($value) { $this->zahlung_kreditkarte=$value; }
  function GetZahlung_Kreditkarte() { return $this->zahlung_kreditkarte; }
  function SetZahlung_Paypal($value) { $this->zahlung_paypal=$value; }
  function GetZahlung_Paypal() { return $this->zahlung_paypal; }
  function SetZahlung_Bar($value) { $this->zahlung_bar=$value; }
  function GetZahlung_Bar() { return $this->zahlung_bar; }
  function SetZahlung_Lastschrift($value) { $this->zahlung_lastschrift=$value; }
  function GetZahlung_Lastschrift() { return $this->zahlung_lastschrift; }
  function SetZahlung_Rechnung_Sofort_De($value) { $this->zahlung_rechnung_sofort_de=$value; }
  function GetZahlung_Rechnung_Sofort_De() { return $this->zahlung_rechnung_sofort_de; }
  function SetZahlung_Rechnung_De($value) { $this->zahlung_rechnung_de=$value; }
  function GetZahlung_Rechnung_De() { return $this->zahlung_rechnung_de; }
  function SetKnickfalz($value) { $this->knickfalz=$value; }
  function GetKnickfalz() { return $this->knickfalz; }

}

?>