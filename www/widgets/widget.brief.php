<?php
include ("_gen/widget.gen.brief.php");

class WidgetBrief extends WidgetGenBrief 
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
    $action = $this->app->Secure->GetGET("action");
    if($action=="brief")
    { 
      // liste zuweisen
      $pid = $this->app->Secure->GetGET("id");
      $this->app->Secure->POST["adresse"]=$pid;
      $field = new HTMLInput("adresse","hidden",$pid);
      $this->form->NewField($field);

      // liste zuweisen
      $this->app->Secure->POST["datum"]=date('Y-m-d');
      $field = new HTMLInput("datum","hidden",date('Y-m-d'));
      $this->form->NewField($field);

      // liste zuweisen
      $this->app->Secure->POST["bearbeiter"]=$this->app->User->GetName();
      $field = new HTMLInput("bearbeiter","hidden",$this->app->User->GetName());
      $this->form->NewField($field);

    }
  }


  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT a.datum, p.abkuerzung,if(a.typ='person',CONCAT(a.vorname,' ',a.name), a.name) as kunde, a.ort, a.email,
      a.id FROM brief as a, projekt as p WHERE a.projekt = p.id order by a.datum, p.abkuerzung");
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
