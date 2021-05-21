<?php
include ("_gen/widget.gen.zahlungsweisen.php");

class WidgetZahlungsweisen extends WidgetGenZahlungsweisen 
{
  private $app;
  function __construct($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::__construct($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {
    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->app->YUI->CkEditor("freitext","belege");
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  public function Table()
  {
    $this->app->YUI->TableSearch('INHALT',"zahlungsweisenlist");
    $this->app->Tpl->Parse($this->parsetarget,"rahmen70.tpl");

  }

  public function Search()
  {
    //$this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }

}
?>
