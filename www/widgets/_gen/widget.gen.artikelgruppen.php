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

class WidgetGenartikelgruppen
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

  public function artikelgruppenDelete()
  {
    
    $this->form->Execute("artikelgruppen","delete");

    $this->artikelgruppenList();
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
    $this->form = $this->app->FormHandler->CreateNew("artikelgruppen");
    $this->form->UseTable("artikelgruppen");
    $this->form->UseTemplate("artikelgruppen.tpl",$this->parsetarget);

    $field = new HTMLInput("shop","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bezeichnung","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bezeichnung_en","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("beschreibung_de",10,40);   
    $this->form->NewField($field);

    $field = new HTMLTextarea("beschreibung_en",10,40);   
    $this->form->NewField($field);

    $field = new HTMLCheckbox("aktiv","","","1","0");
    $this->form->NewField($field);


  }

}

?>