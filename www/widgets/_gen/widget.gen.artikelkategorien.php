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

class WidgetGenartikelkategorien
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

  public function artikelkategorienDelete()
  {
    
    $this->form->Execute("artikelkategorien","delete");

    $this->artikelkategorienList();
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
    $this->form = $this->app->FormHandler->CreateNew("artikelkategorien");
    $this->form->UseTable("artikelkategorien");
    $this->form->UseTemplate("artikelkategorien.tpl",$this->parsetarget);

    $field = new HTMLInput("bezeichnung","text","","40","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnung","notempty","Pflichfeld!","MSGBEZEICHNUNG");

    $field = new HTMLInput("next_nummer","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("externenummer","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz_erloese_normal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_normal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_normal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz_erloese_ermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_ermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_ermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_nichtsteuerbar","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_nichtsteuerbar","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_steuerfrei","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_steuerfrei","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz_erloese_innergemeinschaftlich","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_innergemeinschaftlich","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuertext_innergemeinschaftlich","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_innergemeinschaftlich","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz_erloese_eunormal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_eunormal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_eunormal","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz_erloese_euermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_euermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_euermaessigt","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz_erloese_export","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_erloese_inland_export","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuertext_export","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("steuer_aufwendung_inland_import","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);


  }

}

?>