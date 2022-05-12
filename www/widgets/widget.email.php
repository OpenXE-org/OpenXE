<?php
include ("_gen/widget.gen.email.php");

class WidgetEmail extends WidgetGenEmail 
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
    if($this->app->Secure->GetGET("action")=="create")
    {
      $field = new HTMLInput("datum","text",date('d.m.Y'),"10");
      $this->form->NewField($field);
    }

    if($this->app->Secure->GetGET("action")=="edit")
    {
      $id = $this->app->Secure->GetGET("id");
      $this->form->locafterexe="index.php?module=email&action=artikel&id={$id}";
    }
  }

  function DatumErsetzen($wert)
  {
    return "neuerwerert";
  }

  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT a.datum, p.abkuerzung,if(a.typ='person',CONCAT(a.vorname,' ',a.name), a.name) as kunde, a.ort, a.email,
      a.id FROM email as a, projekt as p WHERE a.projekt = p.id order by a.datum, p.abkuerzung");
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
