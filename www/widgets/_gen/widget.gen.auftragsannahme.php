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

class WidgetGenauftragsannahme
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

  public function auftragsannahmeDelete()
  {
    
    $this->form->Execute("auftragsannahme","delete");

    $this->auftragsannahmeList();
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
    $this->form = $this->app->FormHandler->CreateNew("auftragsannahme");
    $this->form->UseTable("auftragsannahme");
    $this->form->UseTemplate("auftragsannahme.tpl",$this->parsetarget);

    $field = new HTMLInput("vorgang","text","","40");
    $this->form->NewField($field);


    $field = new HTMLCheckbox("erledigt","","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("automatischerversand","","","");
    $this->form->NewField($field);

    $field = new HTMLInput("datum","text","","40");
    $this->form->NewField($field);


    $field = new HTMLInput("projekt","text","","40");
    $this->form->NewField($field);


    $field = new HTMLInput("projekt","text","","40");
    $this->form->NewField($field);


    $field = new HTMLSelect("dokument",0);
    $field->AddOption('Angebot AN','angebot an');
    $field->AddOption('Aufrtragsbest&auml;tigung AB','aufrtragsbest&auml;tigung ab');
    $field->AddOption('Aufrtragsbest&auml;tigung Stornierung ABS','aufrtragsbest&auml;tigung stornierung abs');
    $field->AddOption('Lieferschein LS','lieferschein ls');
    $field->AddOption('Rechnung RE','rechnung re');
    $field->AddOption('Gutschrift GS','gutschrift gs');
    $field->AddOption('Service SE','service se');
    $this->form->NewField($field);

    $field = new HTMLInput("kunde","text","","40");
    $this->form->NewField($field);


    $field = new HTMLInput("empfaenger","text","","40");
    $this->form->NewField($field);


    $field = new HTMLSelect("zahlungsweise",0);
    $field->AddOption('Rechnung','rechnung');
    $field->AddOption('Vorkasse','vorkasse');
    $field->AddOption('Paypal','paypal');
    $field->AddOption('Kreditkarte','kreditkarte');
    $field->AddOption('Nachnahme','nachnahme');
    $field->AddOption('Barzahlung','barzahlung');
    $this->form->NewField($field);

    $field = new HTMLSelect("versand",0);
    $field->AddOption('Versand','versand');
    $field->AddOption('Selbstabholer','selbstabholer');
    $this->form->NewField($field);



    $field = new HTMLSelect("weiterfuehren",0);
    $field->AddOption('Angebot AN','angebot an');
    $field->AddOption('Aufrtragsbest&auml;tigung AB','aufrtragsbest&auml;tigung ab');
    $field->AddOption('Aufrtragsbest&auml;tigung Stornierung ABS','aufrtragsbest&auml;tigung stornierung abs');
    $field->AddOption('Lieferschein LS','lieferschein ls');
    $field->AddOption('Rechnung RE','rechnung re');
    $field->AddOption('Gutschrift GS','gutschrift gs');
    $field->AddOption('Service SE','service se');
    $this->form->NewField($field);


  }

}

?>