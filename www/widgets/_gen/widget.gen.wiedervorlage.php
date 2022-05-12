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

class WidgetGenwiedervorlage
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

  public function wiedervorlageDelete()
  {
    
    $this->form->Execute("wiedervorlage","delete");

    $this->wiedervorlageList();
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
    $this->form = $this->app->FormHandler->CreateNew("wiedervorlage");
    $this->form->UseTable("wiedervorlage");
    $this->form->UseTemplate("wiedervorlage.tpl",$this->parsetarget);

    $field = new HTMLInput("bezeichnung","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnung","notempty","Pflichtfeld!","MSGBEZEICHNUNG");

    $field = new HTMLInput("bearbeiter","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("adresse","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("beschreibung",5,60,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLInput("betrag","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("chance",0,"chance","","","0");
    $field->AddOption('0 %','0');
    $field->AddOption('10 %','10');
    $field->AddOption('20 %','20');
    $field->AddOption('30 %','30');
    $field->AddOption('40 %','40');
    $field->AddOption('50 %','50');
    $field->AddOption('60 %','60');
    $field->AddOption('70 %','70');
    $field->AddOption('80 %','80');
    $field->AddOption('90 %','90');
    $field->AddOption('100 %','100');
    $this->form->NewField($field);

    $field = new HTMLInput("stages","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("datum_erinnerung","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("datum_angelegt","hidden","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("zeit_angelegt","hidden","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("zeit_erinnerung","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("adresse_mitarbeiter","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("prio","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("abgeschlossen","","","1","0","0");
    $this->form->NewField($field);


  }

}

?>