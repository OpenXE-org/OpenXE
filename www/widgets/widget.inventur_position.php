<?php
include ("_gen/widget.gen.inventur_position.php");

class WidgetInventur_position extends WidgetGenInventur_position 
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
    //$this->app->YUI->AutoComplete(AUTO,"artikel",array('nummer','name_de','warengruppe'),"nummer");
    $this->app->YUI->DatePicker("datum");

    $this->form->ReplaceFunction("preis",$this,"ReplaceDecimal");    
    $this->form->ReplaceFunction("menge",$this,"ReplaceDecimal");
/*
    //$inventurart = $this->app->erp->GetInventurartAssoc();
    $steuersatz = $this->app->erp->GetSteuersatzAssoc($id,"inventur");
    $bezahlt_wie = $this->app->erp->GetBezahltWieAssoc();

    $field = new HTMLSelect("inventurart",0);
    $field->AddOptionsAsocSimpleArray($inventurart);
    $this->form->NewField($field);

    $field = new HTMLSelect("uststeuersatz",0);
    $field->AddOptionsAsocSimpleArray($steuersatz);
    $this->form->NewField($field);
	
    $field = new HTMLSelect("bezahlt_wie",0);
    $field->AddOptionsAsocSimpleArray($bezahlt_wie);
    $this->form->NewField($field);
*/

    $this->form->ReplaceFunction("datum",$this,"ReplaceDatum");
  }

  function ReplaceMitarbeiter($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceMitarbeiter($db,$value,$fromform);
  }

  function ReplaceDecimal($db,$value,$fromform)  
  {    
    return $this->app->erp->ReplaceDecimal($db,$value,$fromform);  
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }

  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT inventur, id FROM inventur_position");
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
