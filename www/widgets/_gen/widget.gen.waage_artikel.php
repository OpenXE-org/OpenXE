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

class WidgetGenwaage_artikel
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

  public function waage_artikelDelete()
  {
    
    $this->form->Execute("waage_artikel","delete");

    $this->waage_artikelList();
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
    $this->form = $this->app->FormHandler->CreateNew("waage_artikel");
    $this->form->UseTable("waage_artikel");
    $this->form->UseTemplate("waage_artikel.tpl",$this->parsetarget);

    $field = new HTMLInput("beschriftung","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("beschriftung","notempty","Pflichtfeld!",MSGBESCHRIFTUNG);

    $field = new HTMLInput("artikel","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("artikel","notempty","Pflichtfeld!",MSGARTIKEL);

    $field = new HTMLInput("reihenfolge","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("mhddatum","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("etikettendrucker","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("etikett","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("etikettxml",10,120);   
    $this->form->NewField($field);

    $field = new HTMLInput("waage","text","","40","","","","","","","0");
    $this->form->NewField($field);


  }

}

?>