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

class WidgetGenwawisioneinrichtung
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

  public function wawisioneinrichtungDelete()
  {
    
    $this->form->Execute("wawisioneinrichtung","delete");

    $this->wawisioneinrichtungList();
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
    $this->form = $this->app->FormHandler->CreateNew("wawisioneinrichtung");
    $this->form->UseTable("wawisioneinrichtung");
    $this->form->UseTemplate("wawisioneinrichtung.tpl",$this->parsetarget);

    $field = new HTMLInput("adresse","text","","40","","","","","","","","0","1","");
    $this->form->NewField($field);
    $this->form->AddMandatory("adresse","notempty","Pflichtfeld!","MSGADRESSE");


    $field = new HTMLInput("mitarbeiter","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("version","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("status",0,"status","","","0");
    $field->AddOption('geplant','geplant');
    $field->AddOption('gestartet','gestartet');
    $field->AddOption('abgeschlossen','abgeschlos');
    $field->AddOption('Inaktiv','inaktiv');
    $this->form->NewField($field);

    $field = new HTMLInput("intervall","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("startdatum","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("zeitgeplant","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("bemerkung",5,50,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLSelect("phase",0,"phase","","","0");
    $field->AddOption('Beginn','beginn');
    $field->AddOption('Mitte','mitte');
    $field->AddOption('kurz vor Abschluss','kurzdavor');
    $field->AddOption('&Uuml;bergabe','uebergabe');
    $this->form->NewField($field);


  }

}

?>