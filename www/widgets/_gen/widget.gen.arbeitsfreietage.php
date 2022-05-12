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

class WidgetGenarbeitsfreietage
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

  public function arbeitsfreietageDelete()
  {
    
    $this->form->Execute("arbeitsfreietage","delete");

    $this->arbeitsfreietageList();
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
    $this->form = $this->app->FormHandler->CreateNew("arbeitsfreietage");
    $this->form->UseTable("arbeitsfreietage");
    $this->form->UseTemplate("arbeitsfreietage.tpl",$this->parsetarget);

    $field = new HTMLInput("bezeichnung","text","","40","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnung","notempty","Pflichfeld!","MSGBEZEICHNUNG");

    $field = new HTMLInput("datum","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("typ",0,"typ","","","0");
    $field->AddOption('Feiertag','feiertag');
    $field->AddOption('Br&uuml;ckentag','brueckentag');
    $field->AddOption('Betriebsferien','betriebsferien');
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);


  }

}

?>