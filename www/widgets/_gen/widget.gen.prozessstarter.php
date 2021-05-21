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

class WidgetGenprozessstarter
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

  public function prozessstarterDelete()
  {
    
    $this->form->Execute("prozessstarter","delete");

    $this->prozessstarterList();
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
    $this->form = $this->app->FormHandler->CreateNew("prozessstarter");
    $this->form->UseTable("prozessstarter");
    $this->form->UseTemplate("prozessstarter.tpl",$this->parsetarget);

    $field = new HTMLInput("bezeichnung","text","","40","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnung","notempty","Pflichfeld!","MSGBEZEICHNUNG");

    $field = new HTMLSelect("art",0,"art","","","0");
    $field->AddOption('{|Uhrzeit|}','uhrzeit');
    $field->AddOption('{|Periodisch|}','periodisch');
    $this->form->NewField($field);

    $field = new HTMLSelect("art_filter",0,"art_filter","","","0");
    $field->AddOption('{|Jeden Tag|}','');
    $field->AddOption('{|Montag|}','1');
    $field->AddOption('{|Dienstag|}','2');
    $field->AddOption('{|Mittwoch|}','3');
    $field->AddOption('{|Donnerstag|}','4');
    $field->AddOption('{|Freitag|}','5');
    $field->AddOption('{|Samstag|}','6');
    $field->AddOption('{|Sonntag|}','7');
    $this->form->NewField($field);

    $field = new HTMLInput("startzeit","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("letzteausfuerhung","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("periode","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("typ",0,"typ","","","0");
    $field->AddOption('{|Cronjob|}','cronjob');
    $field->AddOption('{|URL|}','url');
    $this->form->NewField($field);

    $field = new HTMLInput("parameter","text","","40","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("parameter","notempty","Pflichfeld!","MSGPARAMETER");

    $field = new HTMLCheckbox("aktiv","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("mutex","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("mutexcounter","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);


  }

}

?>