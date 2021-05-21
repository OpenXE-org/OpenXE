<?php
include ("_gen/widget.gen.kostenstellen.php");

class WidgetKostenstellen extends WidgetGenKostenstellen 
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
		$this->app->YUI->TableSearch($this->parsetarget,"kostenstellenlist");
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
