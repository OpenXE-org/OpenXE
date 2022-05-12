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

class WidgetGenansprechpartner
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

  public function ansprechpartnerDelete()
  {
    
    $this->form->Execute("ansprechpartner","delete");

    $this->ansprechpartnerList();
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
    $this->form = $this->app->FormHandler->CreateNew("ansprechpartner");
    $this->form->UseTable("ansprechpartner");
    $this->form->UseTemplate("ansprechpartner.tpl",$this->parsetarget);

    $field = new HTMLSelect("typ",0,"typ","","","1");
    $field->AddOption('Herr','herr');
    $field->AddOption('Frau','frau');
    $field->AddOption('Firma / Filiale','firma');
    $this->form->NewField($field);

    $field = new HTMLInput("name","text","","30","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("titel","text","","20","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("bereich","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("abteilung","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("unterabteilung","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("adresszusatz","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("strasse","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("plz","text","","4","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("ort","text","","23","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("vorname","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("geburtstag","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("geburtstagkalender","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("geburtstagskarte","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("marketingsperre","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("telefon","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("telefax","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("mobil","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("anschreiben","text","","30","","","","","","","","0","","Sehr geehrte Frau Dr. Müller");
    $this->form->NewField($field);

    $field = new HTMLInput("email","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("sonstiges",10,50,"","","","","0");   
    $this->form->NewField($field);


  }

}

?>