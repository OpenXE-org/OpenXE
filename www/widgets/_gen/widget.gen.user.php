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

class WidgetGenuser
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

  public function userDelete()
  {
    
    $this->form->Execute("user","delete");

    $this->userList();
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
    $this->form = $this->app->FormHandler->CreateNew("user");
    $this->form->UseTable("user");
    $this->form->UseTemplate("user.tpl",$this->parsetarget);

    $field = new HTMLInput("description","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("description","notempty","Pflichfeld!",MSGDESCRIPTION);

    $field = new HTMLSelect("select1",0,"select1");
    $this->form->NewField($field);

    $field = new HTMLInput("username","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("internebezeichnung","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("adresse","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("externlogin","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("activ","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("vorlage","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("startseite","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("fehllogins","text","","5","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("neuanmeldenlinkuebernehmen","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("password","password","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("repassword","password","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("hwtoken",0,"hwtoken");
    $field->AddOption('Benutzername + Passwort','0');
    $field->AddOption(' Benutzername + mOTP','1');
    $field->AddOption('Benutzername + FRED OTP','2');
    $this->form->NewField($field);

    $field = new HTMLInput("motppin","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("motpsecret","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("hwkey","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("hwcounter","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("standarddrucker","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("settings","text","","40","","","","","","","0");
    $this->form->NewField($field);


  }

}

?>