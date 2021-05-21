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

class WidgetGenseriennummern
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

  public function seriennummernDelete()
  {
    
    $this->form->Execute("seriennummern","delete");

    $this->seriennummernList();
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
    $this->form = $this->app->FormHandler->CreateNew("seriennummern");
    $this->form->UseTable("seriennummern");
    $this->form->UseTemplate("seriennummern.tpl",$this->parsetarget);

    $field = new HTMLInput("artikel","text","","40","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("artikel","notempty","Pflichfeld!","MSGARTIKEL");

    $field = new HTMLInput("seriennummer","text","","40","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("seriennummer","notempty","Pflichfeld!","MSGSERIENNUMMER");

    $field = new HTMLInput("adresse","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);
    $this->form->AddMandatory("adresse","notempty","Pflichtfeld!","MSGADRESSE");

    $field = new HTMLInput("lieferschein","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);
    $this->form->AddMandatory("lieferschein","notempty","Pflichtfeld!","MSGLIEFERSCHEIN");

    $field = new HTMLInput("lieferung","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("beschreibung",5,50,"","","","","0");   
    $this->form->NewField($field);


  }

}

?>