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

class WidgetGenservice
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

  public function serviceDelete()
  {
    
    $this->form->Execute("service","delete");

    $this->serviceList();
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
    $this->form = $this->app->FormHandler->CreateNew("service");
    $this->form->UseTable("service");
    $this->form->UseTemplate("service.tpl",$this->parsetarget);

    $field = new HTMLInput("lieferid","hidden","","","","","","","","","","0","","");
    $this->form->NewField($field);


    $field = new HTMLInput("adresse","text","","140","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("ansprechpartner",1,140,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLSelect("art",0,"art","","","0");
    $field->AddOption('Aufgabe','Aufgabe');
    $field->AddOption('Fehler','Fehler');
    $field->AddOption('Kundenwunsch','Kundenwunsch');
    $field->AddOption('Allgemeines Feature','Allgemeines Feature');
    $this->form->NewField($field);

    $field = new HTMLInput("erledigenbis","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("bezahlte_zusatzleistung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("bereich",0,"bereich","","","0");
    $field->AddOption('Vertrieb','vertrieb');
    $field->AddOption('Marketing','marketing');
    $field->AddOption('Support','support');
    $field->AddOption('Produktmanagement','produktmanagement');
    $field->AddOption('Entwicklung','entwicklung');
    $field->AddOption('Sonstiges','sonstiges');
    $this->form->NewField($field);

    $field = new HTMLInput("dauer_geplant","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("eingangart",0,"eingangart","","","0");
    $field->AddOption('Internet','internet');
    $field->AddOption('Telefon','telefon');
    $field->AddOption('E-Mail','email');
    $field->AddOption('Fax','fax');
    $field->AddOption('Brief','brief');
    $field->AddOption('Sonstiges','sonstiges');
    $this->form->NewField($field);

    $field = new HTMLSelect("prio",0,"prio","","","0");
    $field->AddOption('Niedrig','niedrig');
    $field->AddOption('Normal','normal');
    $field->AddOption('Hoch','hoch');
    $field->AddOption('Notfall','notfall');
    $this->form->NewField($field);

    $field = new HTMLSelect("status",0,"status","","","0");
    $field->AddOption('1. Angelegt','angelegt');
    $field->AddOption('2. Terminiert','terminiert');
    $field->AddOption('3. Besprochen','besprochen');
    $field->AddOption('4. Gestartet','gestartet');
    $field->AddOption('5. Testen','testen');
    $field->AddOption('6. Abgeschlossen','abgeschlossen');
    $field->AddOption('X. Feedback','feedback');
    $field->AddOption('X. Warten','warten');
    $this->form->NewField($field);

    $field = new HTMLInput("zuweisen","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("freigabe","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("betreff","text","","140","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("beschreibung_html",30,80,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLInput("artikel","text","","60","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("seriennummer","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("antwortankunden",20,80,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLCheckbox("antwortpermail","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld1","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld2","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld3","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld4","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("freifeld5","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);


  }

}

?>