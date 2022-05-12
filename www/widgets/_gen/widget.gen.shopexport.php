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

class WidgetGenshopexport
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

  public function shopexportDelete()
  {
    
    $this->form->Execute("shopexport","delete");

    $this->shopexportList();
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
    $this->form = $this->app->FormHandler->CreateNew("shopexport");
    $this->form->UseTable("shopexport");
    $this->form->UseTemplate("shopexport.tpl",$this->parsetarget);

    $field = new HTMLInput("pruefen","hidden","1","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("auftragabholen","hidden","1","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bezeichnung","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("aktiv","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("einzelsync","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("maxmanuell","text","","6","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("vondatum","text","","12","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("vonzeit","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("startdate","text","","12","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("ab_nummer","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("nummersyncstatusaendern","","","1","0","0");
    $this->form->NewField($field);




    $field = new HTMLCheckbox("nurfehler","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("auftraege","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("aenderungen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("zahlungsweisenmapping","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("versandartenmapping","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("vorabbezahltmarkieren_ohnevorkasse_bar","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("utf8codierung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("multiprojekt","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ust_ok","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("artikelporto","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("artikelportoermaessigt","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("portoartikelanlegen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("artikelnachnahme_extraartikel","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("artikelnachnahme","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("auftragabgleich","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("sendonlywithtracking","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("holeallestati","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("freitext",0,"freitext","","","0");
    $field->AddOption('','');
    $field->AddOption('{|in Feld Freitext laden|}','freitext');
    $field->AddOption('{|in Feld Interne Bemerkung laden|}','internebemerkung');
    $this->form->NewField($field);

    $field = new HTMLSelect("autoversandoption",0,"autoversandoption","","","0");
    $field->AddOption('{|Einstellung aus Schnittstelle übernehmen (Standard)|}','standard');
    $field->AddOption('{|Rechnung und Lieferschein erstellen|}','rechnungundlieferschein');
    $field->AddOption('{|nur Lieferschein erstellen|}','nurlieferschein');
    $field->AddOption('{|nur Rechnung erstellen|}','nurrechnung');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("angeboteanlegen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autoversandbeikommentardeaktivieren","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("stornoabgleich","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("steuerfreilieferlandexport","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("gesamtbetragfestsetzen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("gesamtbetragfestsetzendifferenz","text","","6","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("lastschriftdatenueberschreiben","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("artikelimport","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("rabatteportofestschreiben","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("artikeltexteuebernehmen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("artikelnummernummerkreis","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("artikelimporteinzeln","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("artikelnummeruebernehmen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("artikelbezeichnungauswawision","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("artikelbeschreibungauswawision","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("artikelbeschreibungenuebernehmen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("stuecklisteergaenzen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("positionsteuersaetzeerlauben","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("lagerexport","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("lagergrundlage",0,"lagergrundlage","","","0");
    $field->AddOption('{|Artikel verkaufbare|}','0');
    $field->AddOption('{|Lagerbestand minus Reservierungen|}','1');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ueberschreibe_lagerkorrekturwert","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("lagerkorrekturwert","text","1","6","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("artikelexport","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autosendarticle","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("shopbilderuebertragen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("eigenschaftenuebertragen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kategorienuebertragen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("variantenuebertragen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("crosssellingartikeluebertragen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("staffelpreiseuebertragen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("gutscheineuebertragen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("nurpreise","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("nurneueartikel","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("artikelnummerbeimanlegenausshop","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kundenurvonprojekt","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("adressennichtueberschreiben","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("adressupdate","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("vertrieb","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("artikelrabatt","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("artikelrabattsteuer","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("preisgruppe","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("url","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("passwort","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("token","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("datumvon","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("cronjobaktiv","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("demomodus","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("direktimport","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("holealle","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("anzgleichzeitig","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("modulename",0,"modulename","","","0");
    $field->AddOption('{|extern|}','');
    $this->form->NewField($field);


  }

}

?>