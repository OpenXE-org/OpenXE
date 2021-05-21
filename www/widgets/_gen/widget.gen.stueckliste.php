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

class WidgetGenstueckliste
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

  public function stuecklisteDelete()
  {
    
    $this->form->Execute("stueckliste","delete");

    $this->stuecklisteList();
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
    $this->form = $this->app->FormHandler->CreateNew("stueckliste");
    $this->form->UseTable("stueckliste");
    $this->form->UseTemplate("stueckliste.tpl",$this->parsetarget);

    $field = new HTMLInput("artikel","text","","70","","","","","","","","0","","");
    $this->form->NewField($field);
    $this->form->AddMandatory("artikel","notempty","Pflichfeld!","MSGARTIKEL");

    $field = new HTMLInput("menge","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);
    $this->form->AddMandatory("menge","notempty","Pflichfeld!","MSGMENGE");

    $field = new HTMLInput("sort","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("art",0,"art","","","0");
    $field->AddOption('Einkaufsteil','et');
    $field->AddOption('Informationsteil','it');
    $field->AddOption('Beistellung','bt');
    $this->form->NewField($field);

    $field = new HTMLInput("sort","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("alternative","text","","70","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("referenz",3,70,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLSelect("layer",0,"layer","","","0");
    $field->AddOption('TOP','Top');
    $field->AddOption('BOTTOM','Bottom');
    $this->form->NewField($field);

    $field = new HTMLInput("sort","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("place",0,"place","","","0");
    $field->AddOption('platzieren','DP');
    $field->AddOption('nicht platzieren','DNP');
    $this->form->NewField($field);

    $field = new HTMLInput("sort","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("wert","text","","70","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bauform","text","","70","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("zachse","text","","70","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("xpos","text","","70","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("ypos","text","","70","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("internerkommentar",3,70,"","","","","0");   
    $this->form->NewField($field);


  }

}

?>