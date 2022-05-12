<?php
include ("_gen/widget.gen.zolltarifnummer.php");

class WidgetZolltarifnummer extends WidgetGenZolltarifnummer 
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
		$this->app->YUI->TableSearch($this->parsetarget,"zolltarifnummerlist");
  }



  public function Search()
  {
//    $this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }


}

