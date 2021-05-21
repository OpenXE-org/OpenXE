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

class WidgetGenwarteschlangen
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

  public function warteschlangenDelete()
  {
    
    $this->form->Execute("warteschlangen","delete");

    $this->warteschlangenList();
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
    $this->form = $this->app->FormHandler->CreateNew("warteschlangen");
    $this->form->UseTable("warteschlangen");
    $this->form->UseTemplate("warteschlangen.tpl",$this->parsetarget);

    $field = new HTMLInput("warteschlange","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("label","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);
    $this->form->AddMandatory("label","notempty","!","MSGLABEL");

    $field = new HTMLInput("adresse","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("wiedervorlage","","","1","0","0");
    $this->form->NewField($field);


  }

}

?>