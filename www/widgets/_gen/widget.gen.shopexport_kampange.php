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

class WidgetGenshopexport_kampange
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

  public function shopexport_kampangeDelete()
  {
    
    $this->form->Execute("shopexport_kampange","delete");

    $this->shopexport_kampangeList();
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
    $this->form = $this->app->FormHandler->CreateNew("shopexport_kampange");
    $this->form->UseTable("shopexport_kampange");
    $this->form->UseTemplate("shopexport_kampange.tpl",$this->parsetarget);

    $field = new HTMLInput("name","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("name","notempty","Pflichtfeld!",MSGNAME);

    $field = new HTMLInput("link","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("aktiv","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("von","text","","8","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bis","text","","8","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("aktion",0);
    $field->AddOption('','');
    $field->AddOption('Versandkosten frei bei Artikel','versandkostenfreibeiartikel');
    $field->AddOption('Versandkosten frei','versandkostenfrei');
    $this->form->NewField($field);

    $field = new HTMLInput("artikel","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("banner",0);
    $this->form->NewField($field);

    $field = new HTMLSelect("unterbanner",0);
    $this->form->NewField($field);



  }

}

?>