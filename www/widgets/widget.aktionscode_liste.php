<?php
include ("_gen/widget.gen.aktionscode_liste.php");

class WidgetAktionscode_liste extends WidgetGenAktionscode_liste 
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

  }

  public function Table()
  {
    $this->app->YUI->TableSearch($this->parsetarget,"aktionscode_listelist");
  }



  public function Search()
  {
    //    $this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }


}
?>
