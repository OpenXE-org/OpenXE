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

class WidgetGenaktionscode_liste
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

  public function aktionscode_listeDelete()
  {
    
    $this->form->Execute("aktionscode_liste","delete");

    $this->aktionscode_listeList();
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
    $this->form = $this->app->FormHandler->CreateNew("aktionscode_liste");
    $this->form->UseTable("aktionscode_liste");
    $this->form->UseTemplate("aktionscode_liste.tpl",$this->parsetarget);

    $field = new HTMLInput("code","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("code","notempty","Pflichfeld!",MSGCODE);

    $field = new HTMLInput("beschriftung","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("beschriftung","notempty","Pflichfeld!",MSGBESCHRIFTUNG);

    $field = new HTMLCheckbox("ausblenden","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("bemerkung",5,50);   
    $this->form->NewField($field);


  }

}

?>