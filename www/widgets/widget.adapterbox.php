<?php
include ("_gen/widget.gen.adapterbox.php");

class WidgetAdapterbox extends WidgetGenAdapterbox 
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
    //firma

  }

  public function Table()
  {
    $this->app->YUI->TableSearch('INHALT',"adapterboxlist");
    $this->app->Tpl->Parse('PAGE',"rahmen70.tpl");
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
