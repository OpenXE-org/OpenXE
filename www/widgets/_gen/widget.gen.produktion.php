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

class WidgetGenproduktion
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

  public function produktionDelete()
  {
    
    $this->form->Execute("produktion","delete");

    $this->produktionList();
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
    $this->form = $this->app->FormHandler->CreateNew("produktion");
    $this->form->UseTable("produktion");
    $this->form->UseTemplate("produktion.tpl",$this->parsetarget);

    $field = new HTMLInput("adresse","text","","20","","","","","","","pflicht","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("auftragid","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("internebezeichnung","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("datum","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("standardlager","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("schreibschutz","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bezeichnung","text","","100","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("reservierart",0,"reservierart","","","0");
    $field->AddOption('bei Produktionsstart','abschluss');
    $field->AddOption('bei Freigabe','freigabe');
    $field->AddOption('immer auch im Entwurfsmodus ','sofort');
    $this->form->NewField($field);

    $field = new HTMLSelect("auslagerart",0,"auslagerart","","","0");
    $field->AddOption('Einzelentnahme','einzeln');
    $field->AddOption('Sammelentnahme','sammel');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("unterlistenexplodieren","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("funktionstest","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("arbeitsschrittetextanzeigen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("seriennummer_erstellen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("unterseriennummern_erfassen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("datumauslieferung","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("datumbereitstellung","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("datumproduktion","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("datumproduktionende","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("charge","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("mhd","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("freitext",6,100,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("internebemerkung",2,100,"","","","","0");   
    $this->form->NewField($field);


  }

}

?>