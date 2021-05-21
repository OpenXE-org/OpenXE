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

class WidgetGenartikel
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

  public function artikelDelete()
  {
    
    $this->form->Execute("artikel","delete");

    $this->artikelList();
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
    $this->form = $this->app->FormHandler->CreateNew("artikel");
    $this->form->UseTable("artikel");
    $this->form->UseTemplate("artikel.tpl",$this->parsetarget);

    $field = new HTMLInput("leerfeld","hidden","","","","","","","","","","0","","");
    $this->form->NewField($field);


    $field = new HTMLInput("name_de","text","","70","","","","","","","","0","","");
    $this->form->NewField($field);
    $this->form->AddMandatory("name_de","notempty","Pflichtfeld!","MSGNAME_DE");

    $field = new HTMLInput("nummer","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("typ",0,"typ","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("adresse","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("anabregs_text",5,70,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("kurztext_de",2,70,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("internerkommentar",4,70,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLInput("hersteller","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("herstellerlink","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("herstellernummer","text","","35","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("ean","text","","35","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("zolltarifnummer","text","","35","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("herkunftsland","text","","35","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("ursprungsregion",0,"ursprungsregion","","","0");
    $field->AddOption('{|Bitte wählen|}','');
    $field->AddOption('01 Schleswig-Holstein','01');
    $field->AddOption('02 Hamburg','02');
    $field->AddOption('03 Niedersachsen','03');
    $field->AddOption('04 Bremen','04');
    $field->AddOption('05 Nordrhein-Westfalen','05');
    $field->AddOption('06 Hessen','06');
    $field->AddOption('07 Rheinland-Pfalz','07');
    $field->AddOption('08 Baden-Württemberg','08');
    $field->AddOption('09 Bayern','09');
    $field->AddOption('10 Saarland','10');
    $field->AddOption('11 Berlin','11');
    $field->AddOption('12 Brandenburg','12');
    $field->AddOption('13 Mecklenburg-Vorpommern','13');
    $field->AddOption('14 Sachsen','14');
    $field->AddOption('15 Sachsen-Anhalt','15');
    $field->AddOption('16 Thüringen','16');
    $field->AddOption('99 ausländischer Ursprung','99');
    $this->form->NewField($field);

    $field = new HTMLInput("mindestlager","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("gewicht","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("mindestbestellung","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("nettogewicht","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("lager_platz","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("laenge","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("einheit","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("breite","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("xvp","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("hoehe","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("abckategorie",0,"abckategorie","","","0");
    $field->AddOption('{|keine|}','');
    $field->AddOption('A','A');
    $field->AddOption('B','B');
    $field->AddOption('C','C');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("lagerartikel","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("porto","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("rabatt","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt_prozent","text","","4","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("variante","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("variante_von","text","","35","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("matrixprodukt","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("tagespreise","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("umsatzsteuer",0,"umsatzsteuer","","","0");
    $field->AddOption('{|normal|}','normal');
    $field->AddOption('{|erm&auml;&szlig;igt|}','ermaessigt');
    $field->AddOption('{|befreit|}','befreit');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("anderersteuersatz","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz","text","","15","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("keinrabatterlaubt","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("provisionssperre","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("chargenverwaltung",0,"chargenverwaltung","","","0");
    $field->AddOption('{|nein|}','0');
    $field->AddOption('{|ja|}','1');
    $field->AddOption('{|originale nutzen|}','2');
    $this->form->NewField($field);

    $field = new HTMLSelect("seriennummern",0,"seriennummern","","","0");
    $field->AddOption('{|keine|}','keine');
    $field->AddOption('{|eigene erzeugen|}','eigene');
    $field->AddOption('{|originale nutzen|}','vomprodukt');
    $field->AddOption('{|originale einlagern + nutzen|}','vomprodukteinlagern');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("mindesthaltbarkeitsdatum","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("allelieferanten","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("inventursperre","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("inventurekaktiv","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("inventurek","text","","25","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("verwendeberechneterek","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("berechneterek","text","","7","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("berechneterekwaehrung","text","","6","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("vkmeldungunterdruecken","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("keinskonto","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("altersfreigabe",0,"altersfreigabe","","","0");
    $field->AddOption('','');
    $field->AddOption('{|ab 16|}','16');
    $field->AddOption('{|ab 18|}','18');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("stueckliste","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("juststueckliste","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("keineeinzelartikelanzeigen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("has_preproduced_partlist","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("preproduced_partlist","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("produktion","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("externeproduktion","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("rohstoffe","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("geraet","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("serviceartikel","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("gebuehr","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("dienstleistung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("unikat","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ohnepreisimpdf","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("formelmenge",2,50,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("formelpreis",2,50,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("intern_gesperrtgrund",4,50,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLCheckbox("intern_gesperrt","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("hinweis_einfuegen",4,50,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLCheckbox("freigabenotwendig","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("freigaberegel","text","","","","","","","","","","0","","");
    $this->form->NewField($field);


    $field = new HTMLInput("name_en","text","","70","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("kurztext_en",2,70,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("anabregs_text_en",5,70,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("uebersicht_de",2,25,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("uebersicht_en",2,25,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("beschreibung_de",3,25,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("beschreibung_en",3,25,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("links_de",2,25,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("links_en",2,25,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("startseite_de",2,25,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("startseite_en",2,25,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("metatitle_de",1,70,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("metatitle_en",1,70,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("metadescription_de",2,70,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("metadescription_en",2,70,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("metakeywords_de",2,70,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("metakeywords_en",2,70,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLCheckbox("katalog","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("katalogbezeichnung_de","text","","70","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("katalogbezeichnung_en","text","","70","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("katalogtext_de",6,70,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("katalogtext_en",6,70,"","","","","0");   
    $this->form->NewField($field);


    $field = new HTMLInput("freifeld1","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld2","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld3","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld4","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld5","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld6","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld7","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld8","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld9","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld10","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld11","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld12","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld13","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld14","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld15","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld16","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld17","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld18","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld19","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld20","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld21","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld22","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld23","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld24","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld25","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld26","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld27","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld28","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld29","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld30","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld31","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld32","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld33","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld34","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld35","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld36","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld37","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld38","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld39","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld40","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);


    $field = new HTMLInput("shop","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("shop2","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("shop3","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autolagerlampe","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("restmenge","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("pseudolager",3,20,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLInput("lieferzeitmanuell","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bestandalternativartikel","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("lagerkorrekturwert","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("partnerprogramm_sperre","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("neu","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("topseller","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("startseite","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("downloadartikel","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ausverkauft","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("inaktiv","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("pseudopreis","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("generierenummerbeioption","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("variante_kopie","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("unikatbeikopie","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autoabgleicherlaubt","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("steuer_art_produkt",0,"steuer_art_produkt","","","0");
    $field->AddOption('Physisches Produkt','1');
    $field->AddOption('Digitaler Inhalt','2');
    $field->AddOption('Dienstleistung','3');
    $this->form->NewField($field);

    $field = new HTMLSelect("steuer_art_produkt_download",0,"steuer_art_produkt_download","","","0");
    $field->AddOption('Download','1');
    $field->AddOption('eBook','2');
    $this->form->NewField($field);


    $field = new HTMLInput("steuersatz_erloese_normal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_normal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_normal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz_erloese_ermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_ermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_ermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_nichtsteuerbar","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_nichtsteuerbar","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_steuerfrei","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_steuerfrei","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz_erloese_innergemeinschaftlich","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_innergemeinschaftlich","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuertext_innergemeinschaftlich","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_innergemeinschaftlich","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz_erloese_eunormal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_eunormal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_eunormal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz_erloese_euermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_euermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_euermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz_erloese_export","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_export","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuertext_export","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_import","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuergruppe","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kostenstelle","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("onlinshopspopup_sid","hidden","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("onlinshopspopup_shop","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("onlinshopspopup_aktiv","","","","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("onlinshopspopup_ausartikel","","","","0","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("onlinshopspopup_pseudolager",0,0,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLCheckbox("onlinshopspopup_autolagerlampe","","","","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("onlinshopspopup_restmenge","","","","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("onlinshopspopup_lieferzeitmanuell","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("onlinshopspopup_pseudopreis","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("onlinshopspopup_generierenummerbeioption","","","","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("onlinshopspopup_variante_kopie","","","","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("onlinshopspopup_unikat","","","","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("onlinshopspopup_unikatbeikopie","","","","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("onlinshopspopup_lagerkorrekturwert","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("onlinshopspopup_autoabgeleicherlaubt","","","","0","0");
    $this->form->NewField($field);

  }

}

?>