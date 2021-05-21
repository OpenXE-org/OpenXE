<?php
include ("_gen/widget.gen.arbeitsnachweis_position.php");

class WidgetArbeitsnachweis_position extends WidgetGenArbeitsnachweis_position 
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

    //$this->app->YUI->AutoComplete(AUTO,"artikel",array('nummer','name_de','warengruppe'),"nummer");
    $this->app->YUI->TimePicker("von");
    $this->app->YUI->DatePicker("datum");

    $this->app->YUI->AutoComplete("adresse","mitarbeiter");

    $this->form->ReplaceFunction("adresse",$this,"ReplaceMitarbeiter");
    $this->form->ReplaceFunction("datum",$this,"ReplaceDatum");

  }

  function ReplaceMitarbeiter($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceMitarbeiter($db,$value,$fromform);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }

  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT arbeitsnachweis, id FROM arbeitsnachweis_position");
    $table->Display($this->parsetarget);
  }



  public function Search()
  {
    $this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }


}
?>
