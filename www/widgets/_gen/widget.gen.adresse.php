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

class WidgetGenadresse
{

  private $app;            //application object  
  public $form;            //store form object  
  protected $parsetarget;    //target for content

  public function __construct($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function adresseDelete()
  {
    
    $this->form->Execute("adresse","delete");

    $this->adresseList();
  }

  function Edit()
  {
    $this->form->Edit();
  }

  function Copy()
  {
    $this->form->Copy();
  }

  public function Create()
  {
    $this->form->Create();
  }

  public function Search()
  {
    $this->app->Tpl->Set($this->parsetarget,"SUUUCHEEE");
  }

  public function Summary()
  {
    $this->app->Tpl->Set($this->parsetarget,"grosse Tabelle");
  }

  function Form()
  {
    $this->form = $this->app->FormHandler->CreateNew("adresse");
    $this->form->UseTable("adresse");
    $this->form->UseTemplate("adresse.tpl",$this->parsetarget);


    $field = new HTMLInput("vorname","hidden","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("typ",0,"typ","","","1");
    $field->AddOption('{|Firma|}','firma');
    $field->AddOption('{|Herr|}','herr');
    $field->AddOption('{|Frau|}','frau');
    $this->form->NewField($field);

    $field = new HTMLInput("name","text","","30","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("name","notempty","Pflichtfeld!","MSGNAME");

    $field = new HTMLInput("titel","text","","20","","","","","","","","0","3","");
    $this->form->NewField($field);

    $field = new HTMLInput("ansprechpartner","text","","30","","","","","","","","0","4","");
    $this->form->NewField($field);

    $field = new HTMLInput("abteilung","text","","30","","","","","","","","0","5","");
    $this->form->NewField($field);

    $field = new HTMLInput("unterabteilung","text","","30","","","","","","","","0","6","");
    $this->form->NewField($field);

    $field = new HTMLInput("adresszusatz","text","","30","","","","","","","","0","7","");
    $this->form->NewField($field);

    $field = new HTMLInput("strasse","text","","30","","","","","","","","0","8","");
    $this->form->NewField($field);

    $field = new HTMLInput("plz","text","","5","","","","","","","","0","9","");
    $this->form->NewField($field);

    $field = new HTMLInput("ort","text","","23","","","","","","","","0","10","");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferbedingung","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("gln","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("abweichende_rechnungsadresse","","","1","0","12");
    $this->form->NewField($field);

    $field = new HTMLInput("vorname","hidden","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("telefon","text","","30","","","","","","","","0","50","");
    $this->form->NewField($field);

    $field = new HTMLInput("telefax","text","","30","","","","","","","","0","51","");
    $this->form->NewField($field);

    $field = new HTMLInput("mobil","text","","30","","","","","","","","0","52","");
    $this->form->NewField($field);

    $field = new HTMLInput("anschreiben","text","","30","","","","","","","","0","53","Sehr geehrte Frau Dr. Müller");
    $this->form->NewField($field);

    $field = new HTMLInput("email","text","","30","","","","","","","","0","54","");
    $this->form->NewField($field);

    $field = new HTMLInput("internetseite","text","","30","","","","","","","","0","55","");
    $this->form->NewField($field);

    $field = new HTMLInput("vorname","hidden","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("vertrieb","text","","30","","","","","","","","0","100","");
    $this->form->NewField($field);

    $field = new HTMLInput("innendienst","text","","30","","","","","","","","0","101","");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","30","","","","","","","","0","102","");
    $this->form->NewField($field);

    $field = new HTMLInput("fromshop","text","","30","","","","","","","","0","103","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("liefersperre","","","1","0","105");
    $this->form->NewField($field);

    $field = new HTMLInput("liefersperredatum","text","","10","","","","","","","","0","106","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("liefersperregrund",3,30,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLSelect("sprache",0,"sprache","","","103");
    $field->AddOption('{|Deutsch|}','deutsch');
    $field->AddOption('{|Englisch|}','englisch');
    $field->AddOption('{|Französisch|}','franzoesisch');
    $field->AddOption('{|Holländisch|}','hollaendisch');
    $field->AddOption('{|Italienisch|}','italienisch');
    $field->AddOption('{|Spanisch|}','spanisch');
    $field->AddOption('{|Tschechisch|}','tschiechisch');
    $field->AddOption('{|Dänisch|}','daenisch');
    $field->AddOption('{|Niederländisch|}','niederlaendisch');
    $field->AddOption('{|Slowakisch|}','slowkaisch');
    $field->AddOption('{|Schwedisch|}','schwedisch');
    $field->AddOption('{|Polnisch|}','polnisch');
    $this->form->NewField($field);

    $field = new HTMLSelect("kundenfreigabe",0,"kundenfreigabe","","","104");
    $field->AddOption('{|nein|}','0');
    $field->AddOption('{|ja|}','1');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("folgebestaetigungsperre","","","1","0","110");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("trackingsperre","","","1","0","111");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("marketingsperre","","","1","0","108");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("lead","","","1","0","109");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_vorname","hidden","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("rechnung_typ",0,"rechnung_typ","","","30");
    $field->AddOption('{|Firma|}','firma');
    $field->AddOption('{|Herr|}','herr');
    $field->AddOption('{|Frau|}','frau');
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_name","text","","30","","","","","","","","0","31","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_telefon","text","","30","","","","","","","","0","40","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_titel","text","","20","","","","","","","","0","33","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_telefax","text","","30","","","","","","","","0","41","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_ansprechpartner","text","","30","","","","","","","","0","32","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_anschreiben","text","","30","","","","","","","","0","42","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_abteilung","text","","30","","","","","","","","0","34","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_email","text","","30","","","","","","","","0","43","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_unterabteilung","text","","30","","","","","","","","0","35","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_adresszusatz","text","","30","","","","","","","","0","35","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_strasse","text","","30","","","","","","","","0","36","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_plz","text","","5","","","","","","","","0","37","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_ort","text","","23","","","","","","","","0","38","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_gln","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("infoauftragserfassung",10,120,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("sonstiges",20,120,"","","","","0");   
    $this->form->NewField($field);


    $field = new HTMLCheckbox("zahlungskonditionen_festschreiben","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("zahlungsweise",0,"zahlungsweise","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltage","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltageskonto","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszielskonto","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferantennummerbeikunde","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("zahlungsweiseabo",0,"zahlungsweiseabo","","","0");
    $field->AddOption('{|Standard aus Stammdaten|}','');
    $field->AddOption('{|Rechnung|}','rechnung');
    $field->AddOption('{|Lastschrift|}','lastschrift');
    $this->form->NewField($field);

    $field = new HTMLSelect("art",0,"art","","","0");
    $field->AddOption('{|Rechnung und Lieferschein erstellen|}','standardauftrag');
    $field->AddOption('{|nur Lieferschein erstellen|}','lieferung');
    $field->AddOption('{|nur Rechnung erstellen|}','rechnung');
    $this->form->NewField($field);

    $field = new HTMLInput("kommissionskonsignationslager","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("zahlungsweiselieferant",0,"zahlungsweiselieferant","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltagelieferant","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltageskontolieferant","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszielskontolieferant","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("versandartlieferant","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kundennummerlieferant","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("umsatzsteuer_lieferant",0,"umsatzsteuer_lieferant","","","0");
    $field->AddOption('Inland','inland');
    $field->AddOption('EU-Lieferung','eulieferung');
    $field->AddOption('Import','import');
    $this->form->NewField($field);

    $field = new HTMLTextarea("hinweistextlieferant",3,0,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLInput("ustid","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuernummer","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("ust_befreit",0,"ust_befreit","","","0");
    $field->AddOption('{|Inland|}','0');
    $field->AddOption('{|EU-Lieferung / Lieferschwelle|}','1');
    $field->AddOption('{|Export|}','2');
    $field->AddOption('{|Steuerfrei Inland|}','3');
    $this->form->NewField($field);

    $field = new HTMLInput("waehrung","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("lieferschwellenichtanwenden","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("anzeigesteuerbelege",0,"anzeigesteuerbelege","","","0");
    $field->AddOption('{|automatisch|}','0');
    $field->AddOption('{|immer netto|}','1');
    $field->AddOption('{|immer brutto|}','2');
    $this->form->NewField($field);

    $field = new HTMLTextarea("zollinformationen",5,110,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLInput("kundennummer","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kundennummer_buchhaltung","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferantennummer","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferantennummer_buchhaltung","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("mitarbeiternummer","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("verbandsnummer","text","","","","","","","","","","0","","");
    $this->form->NewField($field);


    $field = new HTMLInput("inhaber","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bank","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("konto","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("blz","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("swift","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("iban","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("mandatsreferenz","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("mandatsreferenzart",0,"mandatsreferenzart","","","0");
    $field->AddOption('Einmalig','einmalig');
    $field->AddOption('Wiederholend','wdh');
    $this->form->NewField($field);

    $field = new HTMLSelect("mandatsreferenzwdhart",0,"mandatsreferenzwdhart","","","0");
    $field->AddOption('Erste','erste');
    $field->AddOption('Folge','folge');
    $this->form->NewField($field);

    $field = new HTMLInput("mandatsreferenzdatum","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("mandatsreferenzaenderung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("firmensepa","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("mandatsreferenzhinweis",10,120,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLInput("paypalinhaber","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("paypal","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("paypalwaehrung","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);


    $field = new HTMLCheckbox("rechnung_papier","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_anzahlpapier","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("rechnung_permail","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("angebot_email","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("auftrag_email","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnungs_email","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("gutschrift_email","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferschein_email","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung_email","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("angebot_cc","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("auftrag_cc","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_cc","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("gutschrift_cc","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferschein_cc","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung_cc","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("abperfax","","","1","0","0");
    $this->form->NewField($field);


    $field = new HTMLInput("provision","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("portofrei_aktiv","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("portofreiab","text","","12","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("versandart",0,"versandart","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("keinealtersabfrage","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("tour",0,"tour","","","0");
    $field->AddOption('Keine feste Tour','0');
    $field->AddOption('Montag','1');
    $field->AddOption('Dienstag','2');
    $field->AddOption('Mittwoch','3');
    $field->AddOption('Donnerstag','4');
    $field->AddOption('Freitag','5');
    $field->AddOption('Samstag','6');
    $field->AddOption('Sonntag','7');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("portofreilieferant_aktiv","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("portofreiablieferant","text","","12","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt1","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus1","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus1_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus6","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus6_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt2","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus2","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus2_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus7","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus7_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt3","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus3","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus3_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus8","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus8_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt4","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus4","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus4_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus9","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus9_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt5","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus5","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus5_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus10","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus10_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("rabattinformation",10,120,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLInput("filiale","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("geburtstag","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("geburtstagkalender","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("geburtstagskarte","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("verrechnungskontoreisekosten",0,"verrechnungskontoreisekosten","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("sachkonto","text","","30","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("kreditlimit","text","","30","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("arbeitszeitprowoche","text","","30","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld1","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld2","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld3","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld4","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld5","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld6","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld7","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld8","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld9","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld10","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld11","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld12","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld13","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld14","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld15","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld16","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld17","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld18","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld19","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld20","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("lat","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("lng","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kennung","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

  }

}

?>