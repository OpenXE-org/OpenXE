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

class WidgetGenverbindlichkeit_position
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

  public function verbindlichkeit_positionDelete()
  {
    
    $this->form->Execute("verbindlichkeit_position","delete");

    $this->verbindlichkeit_positionList();
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
    $this->form = $this->app->FormHandler->CreateNew("verbindlichkeit_position");
    $this->form->UseTable("verbindlichkeit_position");
    $this->form->UseTemplate("verbindlichkeit_position.tpl",$this->parsetarget);

    $field = new HTMLInput("artikel","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);
    $this->form->AddMandatory("artikel","notempty","Pflichtfeld!","MSGARTIKEL");

    $field = new HTMLInput("bezeichnung","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnung","notempty","Pflichtfeld!","MSGBEZEICHNUNG");

    $field = new HTMLInput("nummer","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("beschreibung",5,30,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLInput("menge","text","","8","","","","","","","","0","","");
    $this->form->NewField($field);
    $this->form->AddMandatory("menge","notempty","Pflichtfeld!","MSGMENGE");

    $field = new HTMLInput("preis","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("waehrung","text","","15","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("umsatzsteuer",0,"umsatzsteuer","","","0");
    $field->AddOption('Standard','');
    $field->AddOption('Erm&auml;&szlig;igt','ermaessigt');
    $field->AddOption('Befreit','befreit');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("anderersteuersatz","","","","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz","text","","15","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("steuertext",3,50,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLInput("einheit","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("vpe","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kostenstelle","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferdatum","text","","15","","","","","","","","0","","");
    $this->form->NewField($field);


  }

}

?>