<?php
include ("_gen/widget.gen.exportvorlage.php");

class WidgetExportvorlage extends WidgetGenExportvorlage 
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
    //$table->Query("SELECT nummer,beschreibung, id FROM exportvorlage");
 		$table = new EasyTable($this->app);
    $this->app->Tpl->Set(INHALT,"");
		$this->app->YUI->TableSearch($this->parsetarget,"exportvorlage");
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
