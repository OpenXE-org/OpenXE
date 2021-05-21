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

class WidgetGenprojekt
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

  public function projektDelete()
  {
    
    $this->form->Execute("projekt","delete");

    $this->projektList();
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
    $this->form = $this->app->FormHandler->CreateNew("projekt");
    $this->form->UseTable("projekt");
    $this->form->UseTemplate("projekt.tpl",$this->parsetarget);

    $field = new HTMLInput("farbe","text","","15","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("verkaufszahlendiagram","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("oeffentlich","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("zahlungserinnerung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungsmailbedinungen","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("stornomail","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("speziallieferschein","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("speziallieferscheinbeschriftung","","","1","0","0");
    $this->form->NewField($field);


    $field = new HTMLSelect("kommissionierverfahren",0,"kommissionierverfahren","","","0");
    $field->AddOption('{|Einfache Lagerbuchung ohne weiteren Prozess|}','lieferschein');
    $field->AddOption('{|Ohne Lagerbuchung|}','rechnungsmail');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("lagerplatzlieferscheinausblenden","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kommissionierlauflieferschein","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("multiorderpicking","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("orderpicking_sort",0,"orderpicking_sort","","","0");
    $field->AddOption('{|Lieferscheinpositionen|}','deliverynotesort');
    $field->AddOption('{|Lagerplatz Reihen|}','storagelocationrow');
    $field->AddOption('{|Lagerplatz Reihen Stücklistenunabh&auml;ngig|}','storagelocationrowpartlist');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("deactivateautoshipping","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autoversand","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("druckerlogistikstufe1",0,"druckerlogistikstufe1","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("druckerlogistikstufe2",0,"druckerlogistikstufe2","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("etiketten_positionen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("etiketten_drucker",0,"etiketten_drucker","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("etiketten_art",0,"etiketten_art","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("etiketten_sort",0,"etiketten_sort","","","0");
    $field->AddOption('{|Position in Lieferschein|}','0');
    $field->AddOption('{|Lagerplatz aufsteigend|}','1');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autodruckrechnungstufe1","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("autodruckrechnungstufe1menge","text","","3","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autodruckrechnungstufe1mail","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("exportdruckrechnungstufe1","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("exportdruckrechnungstufe1menge","text","","3","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autodruckkommissionierscheinstufe1","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("autodruckkommissionierscheinstufe1menge","text","","3","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kommissionierlistestufe1","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kommissionierlistestufe1menge","text","","3","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("rechnungerzeugen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("lieferscheinedrucken","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferscheinedruckenmenge","text","","3","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("druckanhang","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("mailanhang","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("auftragdrucken","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("auftragdruckenmenge","text","","3","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("paketmarkedrucken","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("automailversandbestaetigung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autodruckrechnung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("autodruckrechnungmenge","text","","3","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("automailrechnung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("exportdruckrechnung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("exportdruckrechnungmenge","text","","3","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("print_proformainvoice","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("proformainvoice_amount","text","","3","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autodrucklieferschein","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("autodrucklieferscheinmenge","text","","3","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("automaillieferschein","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autodruckanhang","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("automailanhang","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("paketmarkeautodrucken","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autodruckrechnungdoppel","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("druckennachtracking","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("wechselaufeinstufig","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("reservierung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("projektlager","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("standardlager","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("standardlagerproduktion","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("versandzweigeteilt","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("versandartikelnameausstammdaten","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("versandlagerplatzanzeigen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("manualtracking","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("chargenerfassen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("mhderfassen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("seriennummernerfassen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("allechargenmhd","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autobestbeforebatch","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("allwaysautobestbeforebatch","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("eanherstellerscanerlauben","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("eanherstellerscan","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("fremdnummerscanerlauben","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("portocheck","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("nachnahmecheck","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("folgebestaetigung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("selbstabholermail","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("differenz_auslieferung_tage","text","2","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("create_proformainvoice","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("checkok","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("checkname","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autostuecklistenanpassung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kundenfreigabe_loeschen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("nurlagerartikel","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("shopzwangsprojekt","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("production_show_only_needed_storages","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("produktion_extra_seiten","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("produktionauftragautomatischfreigeben","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("projektuebergreifendkommisionieren","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("veraltet","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("absendeadresse","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("absendename","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("absendesignatur",20,60,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("absendegrussformel",5,60,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("email_html_template",15,80,"","","","","0");   
    $this->form->NewField($field);


    $field = new HTMLCheckbox("eigenernummernkreis","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("next_angebot","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_auftrag","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_lieferschein","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_retoure","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_rechnung","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_gutschrift","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_bestellung","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_arbeitsnachweis","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_reisekosten","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_produktion","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_anfrage","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_proformarechnung","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_verbindlichkeit","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_goodspostingdocument","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_kundennummer","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_lieferantennummer","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_mitarbeiternummer","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("next_artikelnummer","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);


    $field = new HTMLInput("steuersatz_normal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz_ermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("taxfromdoctypesettings",0,"taxfromdoctypesettings","","","0");
    $field->AddOption('Einstellungen aus Firmendaten','0');
    $field->AddOption('Steuern aus Beleg zuvor (Empfehlung)','1');
    $field->AddOption('Steuern immer aus Einstellungen','2');
    $this->form->NewField($field);

    $field = new HTMLSelect("zahlungsweise",0,"zahlungsweise","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("zahlungsweiselieferant",0,"zahlungsweiselieferant","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("versandart",0,"versandart","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("waehrung","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuernummer","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("mahnwesen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("mahnwesen_abweichender_versender",0,"mahnwesen_abweichender_versender","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("eigenesteuer","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("anzeigesteuerbelege",0,"anzeigesteuerbelege","","","0");
    $field->AddOption('{|automatisch|}','0');
    $field->AddOption('{|immer netto|}','1');
    $field->AddOption('{|immer brutto|}','2');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("anzeigesteuerbelegebestellung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("preisberechnung",0,"preisberechnung","","","0");
    $field->AddOption('{|Runden nach addieren (Standard)|}','0');
    $field->AddOption('{|gerundete Positionen und Steuern addieren|}','1');
    $field->AddOption('{|gerundete Einzelpreise in Positionen und Steuern addieren|}','3');
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_normal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_ermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_nichtsteuerbar","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_innergemeinschaftlich","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_eunormal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_euermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_export","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_normal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_ermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_nichtsteuerbar","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_innergemeinschaftlich","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_eunormal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_euermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_import","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);


    $field = new HTMLSelect("kasse_lagerprozess",0,"kasse_lagerprozess","","","0");
    $field->AddOption('{|Keine Lagerbuchung erzeugen|}','kein');
    $field->AddOption('{|Aus beliebigem Lager entnehmen|}','auftrag');
    $field->AddOption('{|Aus eingestelltem POS Lager entnehmen|}','auftragpos');
    $this->form->NewField($field);

    $field = new HTMLSelect("kasse_bondrucker",0,"kasse_bondrucker","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_lager","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_preisgruppe","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_konto","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_laufkundschaft","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("pos_kundenalleprojekte","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("pos_artikelnurausprojekt","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("pos_sumarticles","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_rabatt_artikel","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_lieferschein_anlegen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_text_bemerkung","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_text_freitext","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("pos_artikeltexteuebernehmen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("pos_anzeigenetto","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("pos_zwischenspeichern","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("pos_grosseansicht","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("pos_disable_single_entries","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("pos_disable_single_day","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("pos_disable_counting_protocol","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("pos_disable_signature","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_zahlung_bar","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_zahlung_bar_bezahlt","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_zahlung_ec","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_zahlung_ec_bezahlt","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_zahlung_kreditkarte","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_zahlung_kreditkarte_bezahlt","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_zahlung_ueberweisung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_zahlung_ueberweisung_bezahlt","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_extra_rechnung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_extra_quittung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_extra_keinbeleg","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_extra_rabatt_prozent","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_extra_rabatt_euro","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_button_entnahme","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_button_einlage","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_button_trinkgeld","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_button_trinkgeldeckredit","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_button_schublade","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_button_belegladen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_button_storno","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_autologout","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_autologout_abschluss","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_adresse_erweitert","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_zahlungsauswahl_zwang","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("kasse_vorauswahl_anrede",0,"kasse_vorauswahl_anrede","","","0");
    $field->AddOption('Firma','firma');
    $field->AddOption('Herr','herr');
    $field->AddOption('Frau','frau');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_erweiterte_lagerabfrage","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("kasse_belegausgabe",0,"kasse_belegausgabe","","","0");
    $field->AddOption('Keine Ausgabe','kein');
    $field->AddOption('Drucker','drucker');
    $this->form->NewField($field);

    $field = new HTMLSelect("kasse_drucker",0,"kasse_drucker","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_lieferschein","text","1","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_rechnung","text","1","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_gutschrift","text","1","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_lieferschein_doppel","text","1","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_quittung_rechnung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_print_qr","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_rechnungpermail","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_bondrucker_aktiv","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_bondrucker_anzahl","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_bon_zeile1","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("kasse_bon_zeile2",5,40,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("kasse_bon_zeile3",5,40,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLSelect("kasse_bondrucker_freifeld",0,"kasse_bondrucker_freifeld","","","0");
    $field->AddOption('','0');
    $field->AddOption('Freifeld 1','1');
    $field->AddOption('Freifeld 2','2');
    $field->AddOption('Freifeld 3','3');
    $field->AddOption('Freifeld 4','4');
    $field->AddOption('Freifeld 5','5');
    $field->AddOption('Freifeld 6','6');
    $field->AddOption('Freifeld 7','7');
    $field->AddOption('Freifeld 8','8');
    $field->AddOption('Freifeld 9','9');
    $field->AddOption('Freifeld 10','10');
    $field->AddOption('Freifeld 11','11');
    $field->AddOption('Freifeld 12','12');
    $field->AddOption('Freifeld 13','13');
    $field->AddOption('Freifeld 14','14');
    $field->AddOption('Freifeld 15','15');
    $field->AddOption('Freifeld 16','16');
    $field->AddOption('Freifeld 17','17');
    $field->AddOption('Freifeld 18','18');
    $field->AddOption('Freifeld 19','19');
    $field->AddOption('Freifeld 20','20');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_bondrucker_qrcode","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zvt100url","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("zvt100port","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kasse_rksv_aktiv","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("kasse_rksv_tool",0,"kasse_rksv_tool","","","0");
    $field->AddOption('RKSVLight','rksvlight');
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_rksv_kartenleser","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_rksv_karteseriennummer","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_rksv_kartepin","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_rksv_kassenid","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kasse_rksv_aeskey","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);


    $field = new HTMLInput("filialadresse","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("versandprojektfiliale","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);


  }

}

?>
