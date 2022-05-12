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

class WidgetGenberichte
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

  public function berichteDelete()
  {
    
    $this->form->Execute("berichte","delete");

    $this->berichteList();
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
    $this->form = $this->app->FormHandler->CreateNew("berichte");
    $this->form->UseTable("berichte");
    $this->form->UseTemplate("berichte.tpl",$this->parsetarget);

    $field = new HTMLInput("name","text","","80","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("name","notempty","Pflichtfeld!","MSGNAME");

    $field = new HTMLInput("project","text","","80","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("beschreibung",5,80,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("variablen",5,80,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("struktur",5,80,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLInput("spaltennamen","text","","80","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("spaltenbreite","text","","80","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("spaltenausrichtung","text","","80","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("sumcols","text","","80","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("internebemerkung",5,80,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ftpuebertragung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ftppassivemode","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("typ",0,"typ","","","0");
    $field->AddOption('FTP','ftp');
    $field->AddOption('FTP mit SSL','ftpssl');
    $field->AddOption('SFTP','sftp');
    $this->form->NewField($field);

    $field = new HTMLInput("ftphost","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("ftpport","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("ftpuser","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("ftppassword","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("ftpuhrzeit","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("ftpnamealternativ","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("emailuebertragung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("emailempfaenger","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("emailbetreff","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("emailuhrzeit","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("emailnamealternativ","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("doctype_actionmenu","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("doctype",0,"doctype","","","0");
    $field->AddOption('Angebot','angebot');
    $field->AddOption('Auftrag','auftrag');
    $field->AddOption('Rechnung','rechnung');
    $field->AddOption('Gutschrift','gutschrift');
    $field->AddOption('Lieferschein','lieferschein');
    $field->AddOption('Bestellung','bestellung');
    $field->AddOption('Produktion','produktion');
    $this->form->NewField($field);

    $field = new HTMLSelect("doctype_actionmenufiletype",0,"doctype_actionmenufiletype","","","0");
    $field->AddOption('CSV','csv');
    $field->AddOption('PDF','pdf');
    $this->form->NewField($field);

    $field = new HTMLInput("doctype_actionmenuname","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);


  }

}

?>