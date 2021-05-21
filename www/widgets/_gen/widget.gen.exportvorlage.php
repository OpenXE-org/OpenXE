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

class WidgetGenexportvorlage
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

  public function exportvorlageDelete()
  {
    
    $this->form->Execute("exportvorlage","delete");

    $this->exportvorlageList();
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
    $this->form = $this->app->FormHandler->CreateNew("exportvorlage");
    $this->form->UseTable("exportvorlage");
    $this->form->UseTemplate("exportvorlage.tpl",$this->parsetarget);

    $field = new HTMLInput("bezeichnung","text","","50","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnung","notempty","Pflichtfeld!","MSGBEZEICHNUNG");

    $field = new HTMLSelect("ziel",0,"ziel","","","0");
    $field->AddOption('Adresse','adresse');
    $field->AddOption('Angebot','angebot');
    $field->AddOption('Auftrag','auftrag');
    $field->AddOption('Ansprechpartner','ansprechpartner');
    $field->AddOption('Artikel','artikel');
    $field->AddOption('Gutschrift','gutschrift');
    $field->AddOption('Rechnung','rechnung');
    $field->AddOption('Lieferschein','lieferschein');
    $field->AddOption('Bestellung','bestellung');
    $field->AddOption('Angebot Positionen','angebot_position');
    $field->AddOption('Auftrag Positionen','auftrag_position');
    $field->AddOption('Rechnung Positionen','rechnung_position');
    $field->AddOption('Gutschrift Positionen','gutschrift_position');
    $field->AddOption('Lieferschein Positionen','lieferschein_position');
    $field->AddOption('Bestellung Positionen','bestellung_position');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("exporterstezeilenummer","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("exporttrennzeichen",0,"exporttrennzeichen","","","0");
    $field->AddOption(';','semikolon');
    $field->AddOption(',','komma');
    $this->form->NewField($field);

    $field = new HTMLSelect("exportdatenmaskierung",0,"exportdatenmaskierung","","","0");
    $field->AddOption('keine','keine');
    $field->AddOption('&quot;','gaensefuesschen');
    $field->AddOption('&apos;','hochkomma');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("filterdatum","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("filterprojekt","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("apifreigabe","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("fields",15,60,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("fields_where",15,60,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("internebemerkung",5,50,"","","","","0");   
    $this->form->NewField($field);


  }

}

?>