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

class WidgetGenkontorahmen
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

  public function kontorahmenDelete()
  {
    
    $this->form->Execute("kontorahmen","delete");

    $this->kontorahmenList();
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
    $this->form = $this->app->FormHandler->CreateNew("kontorahmen");
    $this->form->UseTable("kontorahmen");
    $this->form->UseTemplate("kontorahmen.tpl",$this->parsetarget);

    $field = new HTMLInput("sachkonto","text","","40","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("sachkonto","notempty","Pflichfeld!","MSGSACHKONTO");

    $field = new HTMLInput("beschriftung","text","","40","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("beschriftung","notempty","Pflichfeld!","MSGBESCHRIFTUNG");

    $field = new HTMLSelect("art",0,"art","","","0");
    $field->AddOption('Bitte w&auml;hlen','0');
    $field->AddOption('Aufwendungen','1');
    $field->AddOption('Erl&ouml;se','2');
    $field->AddOption('Geldtransit','3');
    $field->AddOption('Saldo','9');
    $this->form->NewField($field);

    $field = new HTMLTextarea("bemerkung",5,50,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ausblenden","","","1","0","0");
    $this->form->NewField($field);


  }

}

?>