<?php
include ("_gen/widget.gen.zeiterfassungvorlage.php");

class WidgetZeiterfassungvorlage extends WidgetGenZeiterfassungvorlage 
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
    $this->app->YUI->TableSearch($this->parsetarget,"zeiterfassungvorlagelist");
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
