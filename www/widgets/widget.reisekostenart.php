<?php
include ("_gen/widget.gen.reisekostenart.php");

class WidgetReisekostenart extends WidgetGenReisekostenart 
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
    //$table->Query("SELECT nummer,beschreibung, id FROM reisekostenart");
		$this->app->YUI->TableSearch($this->parsetarget,"reisekostenartlist");
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
