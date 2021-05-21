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

class WidgetGenetiketten
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

  public function etikettenDelete()
  {
    
    $this->form->Execute("etiketten","delete");

    $this->etikettenList();
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
    $this->form = $this->app->FormHandler->CreateNew("etiketten");
    $this->form->UseTable("etiketten");
    $this->form->UseTemplate("etiketten.tpl",$this->parsetarget);

    $field = new HTMLInput("name","text","","80","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("name","notempty","Pflichfeld!","MSGNAME");

    $field = new HTMLTextarea("xml",10,80,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLTextarea("bemerkung",5,80,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLSelect("verwendenals",0,"verwendenals","","","0");
    $field->AddOption('','');
    $field->AddOption('Artikel klein','artikel_klein');
    $field->AddOption('Lager klein','lagerplatz_klein');
    $field->AddOption('Etikettendrucker 2-zeilig','etikettendrucker_einfach');
    $field->AddOption('Kommissionieraufkleber','kommissionieraufkleber');
    $field->AddOption('Seriennummer','seriennummer');
    $field->AddOption('Lieferschein Position','lieferschein_position');
    $field->AddOption('Multiorder Picking Artikel','multiorder_artikel');
    $field->AddOption('MultiOrder Picking Lieferschein','multiorder_lieferschein');
    $field->AddOption('MultiOrder Picking Trenner','multiorder_trenner');
    $this->form->NewField($field);

    $field = new HTMLSelect("format",0,"format","","","0");
    $field->AddOption('30x15 mm','30x15x3');
    $field->AddOption('50x18 mm','50x18x3');
    $field->AddOption('100x50 mm','100x50x5');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("manuell","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("labelbreite","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("labelhoehe","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("labelabstand","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("labeloffsetx","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("labeloffsety","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("anzahlprozeile","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);


  }

}

?>