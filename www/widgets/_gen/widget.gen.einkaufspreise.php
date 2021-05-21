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

class WidgetGeneinkaufspreise
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

  public function einkaufspreiseDelete()
  {
    
    $this->form->Execute("einkaufspreise","delete");

    $this->einkaufspreiseList();
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
    $this->form = $this->app->FormHandler->CreateNew("einkaufspreise");
    $this->form->UseTable("einkaufspreise");
    $this->form->UseTemplate("einkaufspreise.tpl",$this->parsetarget);

    $field = new HTMLCheckbox("standard","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("adresse","text","","45","","","","","","","","0","");
    $this->form->NewField($field);
    $this->form->AddMandatory("adresse","notempty","Pflichtfeld!","MSGADRESSE");

    $field = new HTMLInput("bezeichnunglieferant","text","","70","","","","","","","","0","");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellnummer","text","","70","","","","","","","","0","");
    $this->form->NewField($field);

    $field = new HTMLInput("ab_menge","text","","10","","","","","","","","0","");
    $this->form->NewField($field);
    $this->form->AddMandatory("ab_menge","notempty","Pflichfeld!","MSGAB_MENGE");

    $field = new HTMLInput("vpe","text","","10","","","","","","","","0","");
    $this->form->NewField($field);

    $field = new HTMLInput("preis","text","","10","","","","","","","","0","");
    $this->form->NewField($field);
    $this->form->AddMandatory("preis","notempty","Pflichfeld!","MSGPREIS");

    $field = new HTMLSelect("waehrung",0,"waehrung","","","0");
    $field->AddOption('EUR','EUR');
    $field->AddOption('USD','USD');
    $field->AddOption('CAD','CAD');
    $field->AddOption('CHF','CHF');
    $field->AddOption('GBP','GBP');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("nichtberechnet","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("preis_anfrage_vom","text","","10","","","","","","","","0","");
    $this->form->NewField($field);

    $field = new HTMLInput("gueltig_bis","text","","10","","","","","","","","0","");
    $this->form->NewField($field);

    $field = new HTMLInput("lager_lieferant","text","","5","","","","","","","","0","");
    $this->form->NewField($field);

    $field = new HTMLInput("datum_lagerlieferant","text","","10","","","","","","","","0","");
    $this->form->NewField($field);

    $field = new HTMLInput("sicherheitslager","text","","10","","","","","","","","0","");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferzeit_standard","text","","10","","","","","","","","0","");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferzeit_aktuell","text","","10","","","","","","","","0","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("rahmenvertrag","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("rahmenvertrag_menge","text","","10","","","","","","","","0","");
    $this->form->NewField($field);

    $field = new HTMLInput("rahmenvertrag_von","text","","10","","","","","","","","0","");
    $this->form->NewField($field);

    $field = new HTMLInput("rahmenvertrag_bis","text","","10","","","","","","","","0","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("bemerkung",3,70);   
    $this->form->NewField($field);


  }

}

?>