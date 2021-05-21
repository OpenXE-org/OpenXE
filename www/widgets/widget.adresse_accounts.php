<?php
include ("_gen/widget.gen.adresse_accounts.php");

class WidgetAdresse_accounts extends WidgetGenAdresse_accounts 
{
  private $app;
  function __construct($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;

    $id= $this->app->Secure->GetGET("id");
    $this->id = $id;
    $lid= $this->app->Secure->GetGET("lid");

   if($lid > 0)
    {
      $this->app->Secure->GET["id"]=$lid;
      $this->app->Secure->POST["id"]=$lid;
    }


    parent::__construct($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {
    $action = $this->app->Secure->GetGET("action");

    $this->app->YUI->AutoComplete("art","accountart");
    $this->app->YUI->DatePicker("gueltig_bis");
    $this->app->YUI->DatePicker("gueltig_ab");

    $this->form->ReplaceFunction("gueltig_bis",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("gueltig_ab",$this,"ReplaceDatum");

    if($action=="accounts")
    { 
      // liste zuweisen
      $pid = $this->id;//$this->app->Secure->GetGET("id");
      $this->app->Secure->POST["adresse"]=$pid;
      $field = new HTMLInput("adresse","hidden",$pid);
      $this->form->NewField($field);
    }

    if($action=="adresse_accountspopup")
    { 
      // liste zuweisen
      $pid = $this->id;//$this->app->Secure->GetGET("id");
      $this->app->Secure->POST["adresse"]=$pid;
      $field = new HTMLInput("adresse","hidden",$pid);
      $this->form->NewField($field);
    }


  }

  function DatumErsetzen($wert)
  {
    return "neuerwerert";
  }


  public function Search()
  {
    $this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }

   function __destruct() {
    $id = $this->id;
    $this->app->Secure->GET["id"]=$id;
    $this->app->Secure->POST["id"]=$id;
   }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }


}
?>
