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

class WidgetGengutschrift
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

  public function gutschriftDelete()
  {
    
    $this->form->Execute("gutschrift","delete");

    $this->gutschriftList();
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
    $this->form = $this->app->FormHandler->CreateNew("gutschrift");
    $this->form->UseTable("gutschrift");
    $this->form->UseTemplate("gutschrift.tpl",$this->parsetarget);

    $field = new HTMLInput("lieferid","hidden","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("ansprechpartnerid","hidden","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("adresse","text","","20","","","","","","","pflicht","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("aktion","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnungid","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kundennummer_buchhaltung","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("ihrebestellnummer","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("internebezeichnung","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferdatum","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("datum","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("stornorechnung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("schreibschutz","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("nicht_umsatzmindernd","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("manuell_vorabbezahlt","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("manuell_vorabbezahlt_hinweis",5,30,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLSelect("typ",0,"typ","","","0");
    $field->AddOption('Firma','firma');
    $field->AddOption('Herr','herr');
    $field->AddOption('Frau','frau');
    $this->form->NewField($field);

    $field = new HTMLInput("name","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);
    $this->form->AddMandatory("name","notempty","Pflichfeld!","MSGNAME");

    $field = new HTMLInput("titel","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("ansprechpartner","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("abteilung","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("unterabteilung","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("adresszusatz","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("strasse","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("plz","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("ort","text","","19","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("gln","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("telefon","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("telefax","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("email","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("anschreiben","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("freitext",5,110,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("bodyzusatz",5,110,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLSelect("zahlungsweise",0,"zahlungsweise","","","0");
    $field->AddOption('Kreditkarte','kreditkarte');
    $field->AddOption('&Uuml;berweisung','ueberweisung');
    $field->AddOption('Bar','bar');
    $field->AddOption('PayPal','paypal');
    $this->form->NewField($field);

    $field = new HTMLInput("lieferbedingung","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bearbeiter","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("vertrieb","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bearbeiter","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ohne_briefpapier","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ohne_artikeltext","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bank_inhaber","text","","35","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bank_institut","text","","35","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bank_blz","text","","35","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bank_konto","text","","35","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("paypalaccount","text","","35","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("kreditkarte_typ",0,"kreditkarte_typ","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kreditkarte_inhaber","text","","35","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kreditkarte_nummer","text","","35","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kreditkarte_pruefnummer","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("kreditkarte_monat",0,"kreditkarte_monat","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("kreditkarte_jahr",0,"kreditkarte_jahr","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszielskonto","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt","text","","4","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt1","text","","4","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt2","text","","4","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt3","text","","4","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt4","text","","4","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt5","text","","4","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("internebemerkung",2,110,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLInput("ustid","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("ust_befreit",0,"ust_befreit","","","0");
    $field->AddOption('{|Inland|}','0');
    $field->AddOption('{|EU-Lieferung / Lieferschwelle|}','1');
    $field->AddOption('{|Export|}','2');
    $field->AddOption('{|Steuerfrei Inland|}','3');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("keinsteuersatz","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ustbrief","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ustbrief_eingang","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("ustbrief_eingang_am","text","","35","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("anzeigesteuer",0,"anzeigesteuer","","","0");
    $field->AddOption('automatisch','0');
    $field->AddOption('netto','3');
    $field->AddOption('brutto','4');
    $this->form->NewField($field);

    $field = new HTMLInput("waehrung","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("sprache","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kurs","text","","15","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kostenstelle","text","","15","","","","","","","","0","","");
    $this->form->NewField($field);


  }

}

?>