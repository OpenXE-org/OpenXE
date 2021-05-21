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

class WidgetGendatei_stichwortvorlagen
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

  public function datei_stichwortvorlagenDelete()
  {
    
    $this->form->Execute("datei_stichwortvorlagen","delete");

    $this->datei_stichwortvorlagenList();
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
    $this->form = $this->app->FormHandler->CreateNew("datei_stichwortvorlagen");
    $this->form->UseTable("datei_stichwortvorlagen");
    $this->form->UseTemplate("datei_stichwortvorlagen.tpl",$this->parsetarget);

    $field = new HTMLInput("beschriftung","text","","40","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("beschriftung","notempty","Pflichfeld!","MSGBESCHRIFTUNG");

    $field = new HTMLSelect("modul",0,"modul","","","0");
    $field->AddOption('','');
    $field->AddOption('Artikel','artikel');
    $field->AddOption('Adresse','adresse');
    $field->AddOption('Aufgabe','aufgabe');
    $field->AddOption('Kasse','kasse');
    $field->AddOption('Projekt','projekt');
    $field->AddOption('Verbindlichkeit','verbindlichkeit');
    $field->AddOption('Wiedervorlage','wiedervorlage');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ausblenden","","","1","0","0");
    $this->form->NewField($field);


  }

}

?>