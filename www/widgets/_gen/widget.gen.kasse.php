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

class WidgetGenkasse
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

  public function kasseDelete()
  {
    
    $this->form->Execute("kasse","delete");

    $this->kasseList();
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
    $this->form = $this->app->FormHandler->CreateNew("kasse");
    $this->form->UseTable("kasse");
    $this->form->UseTemplate("kasse.tpl",$this->parsetarget);

    $field = new HTMLInput("datum","text","","10","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("datum","notempty","Pflichfeld!","MSGDATUM");

    $field = new HTMLSelect("auswahl",0,"auswahl","","","0");
    $field->AddOption('Ausgabe','ausgabe');
    $field->AddOption('Einnahme','einnahme');
    $this->form->NewField($field);

    $field = new HTMLInput("betrag","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);
    $this->form->AddMandatory("betrag","notempty","Pflichfeld!","MSGBETRAG");

    $field = new HTMLSelect("steuergruppe",0,"steuergruppe","","","0");
    $field->AddOption('Standard UST','0');
    $field->AddOption('Erm&auml;ssigte UST (Buch, Literatur, ...)','1');
    $field->AddOption('Ohne UST','2');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kundenbuchung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("adresse","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","40","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("projekt","notempty","Pflichfeld!","MSGPROJEKT");

    $field = new HTMLInput("grund","text","","40","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("grund","notempty","Pflichfeld!","MSGGRUND");

    $field = new HTMLInput("storniert_grund","text","","40","","","","","","","","0","2","");
    $this->form->NewField($field);


  }

}

?>