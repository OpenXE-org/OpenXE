<?php
include ("_gen/widget.gen.lieferadressen.php");

class WidgetLieferadressen extends WidgetGenLieferadressen 
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
    if($action=="lieferadresse")
    { 
      // liste zuweisen
      $pid = $this->id;//app->Secure->GetGET("id");
      $this->app->Secure->POST["adresse"]=$pid;
      $field = new HTMLInput("adresse","hidden",$pid);
      $this->form->NewField($field);
      if($this->app->Secure->POST["standardlieferadresse"]=="1")
      {
        $this->app->DB->Update("UPDATE lieferadressen SET standardlieferadresse=0 WHERE adresse='$pid'");
      }	
    }

    if($action=="lieferadressepopup")
    {
      // liste zuweisen
      $pid = $this->id;//$this->app->Secure->GetGET("id");
      $this->app->Secure->POST["adresse"]=$pid;
      $field = new HTMLInput("adresse","hidden",$pid);
      $this->form->NewField($field);
      if($this->app->Secure->POST["standardlieferadresse"]=="1")
      {
        $this->app->DB->Update("UPDATE lieferadressen SET standardlieferadresse=0 WHERE adresse='$pid'");
      }	

    }

    $this->app->YUI->AutoComplete("lieferbedingung","lieferbedingungen");

    $field = new HTMLInput("land","hidden","");
    $this->form->NewField($field);

    $typOptions = $this->app->erp->GetTypSelect();
    $field = new HTMLSelect("typ",0);
    //$field->onchange="onchange_typ(this.form.typ.options[this.form.typ.selectedIndex].value);";
    $field->AddOptionsSimpleArray($typOptions);
    $this->form->NewField($field);

  }

  function DatumErsetzen($wert)
  {
    return "neuerwerert";
  }

  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT nummer, name_de as name,barcode, id FROM lieferadressen order by nummer");
    $table->Display($this->parsetarget);
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


}
?>
