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

class WidgetGeninventur_position
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

  public function inventur_positionDelete()
  {
    
    $this->form->Execute("inventur_position","delete");

    $this->inventur_positionList();
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
    $this->form = $this->app->FormHandler->CreateNew("inventur_position");
    $this->form->UseTable("inventur_position");
    $this->form->UseTemplate("inventur_position.tpl",$this->parsetarget);

    $field = new HTMLInput("nummer","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bezeichnung","text","","50","50","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnung","notempty","Pflichtfeld!","MSGBEZEICHNUNG");

    $field = new HTMLTextarea("beschreibung",8,48);   
    $this->form->NewField($field);

    $field = new HTMLInput("menge","text","","10","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("menge","notempty","Pflichtfeld!","MSGMENGE");

    $field = new HTMLInput("preis","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("internerkommentar",3,30);   
    $this->form->NewField($field);


  }

}

?>