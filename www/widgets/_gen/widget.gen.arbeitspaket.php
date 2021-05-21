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

class WidgetGenarbeitspaket
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

  public function arbeitspaketDelete()
  {
    
    $this->form->Execute("arbeitspaket","delete");

    $this->arbeitspaketList();
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
    $this->form = $this->app->FormHandler->CreateNew("arbeitspaket");
    $this->form->UseTable("arbeitspaket");
    $this->form->UseTemplate("arbeitspaket.tpl",$this->parsetarget);

    $field = new HTMLSelect("art",0,"art");
    $field->AddOption('Teilprojekt','teilprojekt');
    $field->AddOption('Arbeitspaket','arbeitspaket');
    $field->AddOption('Meilenstein','meilenstein');
    $field->AddOption('Material','material');
    $this->form->NewField($field);

    $field = new HTMLInput("aufgabe","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("beschreibung",10,50);   
    $this->form->NewField($field);

    $field = new HTMLSelect("status",0,"status");
    $field->AddOption('offen','offen');
    $field->AddOption('aktiv','aktiv');
    $field->AddOption('abgeschlossen','abgeschlossen');
    $field->AddOption('abgerechnet','abgerechnet');
    $this->form->NewField($field);

    $field = new HTMLSelect("vorgaenger",0,"vorgaenger");
    $this->form->NewField($field);

    $field = new HTMLInput("abgabedatum","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("adresse","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("auftragid","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zeit_geplant","text","","5","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kosten_geplant","text","","5","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("artikel_geplant","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kostenstelle","text","","5","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("abgenommen","","","1","0");
    $this->form->NewField($field);


  }

}

?>