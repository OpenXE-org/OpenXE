<?php
include ("_gen/widget.gen.reisekosten_position.php");

class WidgetReisekosten_position extends WidgetGenReisekosten_position 
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
    $id = $this->app->Secure->GetGET("id");
    $reisekosten_id = $this->app->DB->Select("SELECT reisekosten FROM reisekosten_position WHERE id='$id' LIMIT 1");

    $this->app->YUI->AutoComplete("mitarbeiter","mitarbeiter");


    $this->form->ReplaceFunction("mitarbeiter",$this,"ReplaceMitarbeiter");

    //$this->app->YUI->AutoComplete(AUTO,"artikel",array('nummer','name_de','warengruppe'),"nummer");
    $this->app->YUI->DatePicker("datum");

    $reisekostenart = $this->app->erp->GetReisekostenartAssoc();
    $steuersatz = $this->app->erp->GetSteuersatzAssoc($reisekosten_id,"reisekosten");
    $bezahlt_wie = $this->app->erp->GetBezahltWieAssoc();

    $field = new HTMLSelect("reisekostenart",0);
    $field->AddOptionsAsocSimpleArray($reisekostenart);
    $this->form->NewField($field);

    $field = new HTMLSelect("uststeuersatz",0);
    $field->AddOptionsAsocSimpleArray($steuersatz);
    $this->form->NewField($field);

    $field = new HTMLSelect("bezahlt_wie",0);
    $field->AddOptionsAsocSimpleArray($bezahlt_wie);
    $this->form->NewField($field);

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
    $table->Query("SELECT reisekosten, id FROM reisekosten_position");
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
