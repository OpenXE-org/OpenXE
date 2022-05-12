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

class WidgetGenadresse_onchange
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

  public function adresse_onchangeDelete()
  {
    
    $this->form->Execute("adresse_onchange","delete");

    $this->adresse_onchangeList();
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
    $this->form = $this->app->FormHandler->CreateNew("adresse_onchange");
    $this->form->UseTable("adresse_onchange");
    $this->form->UseTemplate("adresse_onchange.tpl",$this->parsetarget);

    $field = new HTMLInput("vorname","hidden","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("typ",0);
    $field->AddOption('Firma','firma');
    $field->AddOption('Herr','herr');
    $field->AddOption('Frau','frau');
    $field->AddOption('Hochschule','hochschule');
    $field->AddOption('Ausbildungsbetrieb','ausbildungsbetrieb');
    $this->form->NewField($field);

    $field = new HTMLInput("name","text","","20","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("name","notempty","Pflichtfeld!",MSGNAME);

    $field = new HTMLInput("telefon","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("ansprechpartner","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("telefax","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("titel","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("anschreiben","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("abteilung","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("email","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("unterabteilung","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("mobil","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("adresszusatz","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("internetseite","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("strasse","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("kundenfreigabe",0);
    $field->AddOption('nein','0');
    $field->AddOption('ja','1');
    $this->form->NewField($field);

    $field = new HTMLInput("plz","text","","4","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("ort","text","","13","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("ustid","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("ust_befreit",0);
    $field->AddOption('Deutschland','0');
    $field->AddOption('EU-Lieferung','1');
    $field->AddOption('Export','2');
    $this->form->NewField($field);

    $field = new HTMLSelect("sprache",0);
    $field->AddOption('Deutsch','deutsch');
    $field->AddOption('Englisch','englisch');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("marketingsperre","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("trackingsperre","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("rechnungsadresse","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("passwort_gesendet","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("sonstiges",10,70);   
    $this->form->NewField($field);

    $field = new HTMLInput("kundennummer","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferantennummer","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("mitarbeiternummer","text","","","","","","","","","0");
    $this->form->NewField($field);



    $field = new HTMLSelect("zahlungsweise",0);
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltage","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltageskonto","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszielskonto","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("versandart",0);
    $this->form->NewField($field);

    $field = new HTMLInput("kundennummerlieferant","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("zahlungsweiselieferant",0);
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltagelieferant","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltageskontolieferant","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszielskontolieferant","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("versandartlieferant","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("inhaber","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bank","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("konto","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("blz","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("swift","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("iban","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("waehrung","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("paypalinhaber","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("paypal","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("paypalwaehrung","text","","20","","","","","","","0");
    $this->form->NewField($field);



  }

}

?>