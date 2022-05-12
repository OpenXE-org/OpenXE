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

class WidgetGenreisekosten_position
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

  public function reisekosten_positionDelete()
  {
    
    $this->form->Execute("reisekosten_position","delete");

    $this->reisekosten_positionList();
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
    $this->form = $this->app->FormHandler->CreateNew("reisekosten_position");
    $this->form->UseTable("reisekosten_position");
    $this->form->UseTemplate("reisekosten_position.tpl",$this->parsetarget);

    $field = new HTMLInput("datum","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("reisekostenart",0,"reisekostenart","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("betrag","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("abrechnen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("keineust","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("uststeuersatz",0,"uststeuersatz","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bezeichnung","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("bezahlt_wie",0,"bezahlt_wie","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("mitarbeiter","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);


  }

}

?>