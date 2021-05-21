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

class WidgetGenauftrag_artikel
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

  public function auftrag_artikelDelete()
  {
    
    $this->form->Execute("auftrag_artikel","delete");

    $this->auftrag_artikelList();
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
    $this->form = $this->app->FormHandler->CreateNew("auftrag_artikel");
    $this->form->UseTable("auftrag_artikel");
    $this->form->UseTemplate("auftrag_artikel.tpl",$this->parsetarget);

    $field = new HTMLInput("artikel","text","","10","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("artikel","notempty","Pflichtfeld!",MSGARTIKEL);

    $field = new HTMLInput("bezeichnung","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("menge","text","","10","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("menge","notempty","Pflichtfeld!",MSGMENGE);

    $field = new HTMLInput("preis","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("bemerkung",5,40);   
    $this->form->NewField($field);

    $field = new HTMLCheckbox("wiederholend","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("startdatum","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("enddatum","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlzyklus","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("abgerechnetbis","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("bemerkung",5,40);   
    $this->form->NewField($field);



  }

}

?>