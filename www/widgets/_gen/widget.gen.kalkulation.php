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

class WidgetGenkalkulation
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

  public function kalkulationDelete()
  {
    
    $this->form->Execute("kalkulation","delete");

    $this->kalkulationList();
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
    $this->form = $this->app->FormHandler->CreateNew("kalkulation");
    $this->form->UseTable("kalkulation");
    $this->form->UseTemplate("kalkulation.tpl",$this->parsetarget);

    $field = new HTMLInput("adresse","text","","10","","","","","","","pflicht","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("name","text","","30","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("name","notempty","Pflichfeld!","MSGNAME");

    $field = new HTMLInput("bezeichnung","text","","30","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("datum","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("schreibschutz","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("freitext",10,110,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLInput("bearbeiter","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ohne_briefpapier","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("internebemerkung",2,110,"","","","","0");   
    $this->form->NewField($field);


  }

}

?>