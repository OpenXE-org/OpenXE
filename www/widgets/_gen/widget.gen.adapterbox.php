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

class WidgetGenadapterbox
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

  public function adapterboxDelete()
  {
    
    $this->form->Execute("adapterbox","delete");

    $this->adapterboxList();
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
    $this->form = $this->app->FormHandler->CreateNew("adapterbox");
    $this->form->UseTable("adapterbox");
    $this->form->UseTemplate("adapterbox.tpl",$this->parsetarget);

    $field = new HTMLCheckbox("dhcp","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("ipadresse","text","","60","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("netmask","text","","60","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("gateway","text","","60","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("dns","text","","60","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("wlan","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("ssid","text","","60","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("passphrase","text","","60","","","","","","","","0","","");
    $this->form->NewField($field);


  }

}

?>