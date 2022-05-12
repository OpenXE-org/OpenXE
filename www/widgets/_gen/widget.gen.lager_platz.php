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

class WidgetGenlager_platz
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

  public function lager_platzDelete()
  {
    
    $this->form->Execute("lager_platz","delete");

    $this->lager_platzList();
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
    $this->form = $this->app->FormHandler->CreateNew("lager_platz");
    $this->form->UseTable("lager_platz");
    $this->form->UseTemplate("lager_platz.tpl",$this->parsetarget);

    $field = new HTMLInput("kurzbezeichnung","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);
    $this->form->AddMandatory("kurzbezeichnung","notempty","Pflichtfeld!","MSGKURZBEZEICHNUNG");

    $field = new HTMLCheckbox("autolagersperre","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("verbrauchslager","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("sperrlager","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("adresse","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("laenge","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("breite","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("hoehe","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);


  }

}

?>