<?php
include ("_gen/widget.gen.kalkulation.php");

class WidgetKalkulation extends WidgetGenkalkulation 
{
  private $app;
  function __construct(&$app,$parsetarget)
  {
    $this->app = &$app;
    $this->parsetarget = $parsetarget;
    parent::__construct($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {
    $this->app->YUI->AutoComplete("adresse","kunde",1);
    $this->app->YUI->AutoComplete("mitarbeiter","mitarbeiter");
    $this->app->YUI->AutoComplete("projekt","projektname",1);


    $this->app->YUI->DatePicker("datum");
    $this->app->YUI->DatePicker("von");
    $this->app->YUI->DatePicker("bis");

    $this->app->YUI->TimePicker("von_zeit");
    $this->app->YUI->TimePicker("bis_zeit");


    $this->form->ReplaceFunction("datum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("von",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("bis",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->form->ReplaceFunction("adresse",$this,"ReplaceKunde");
    $this->form->ReplaceFunction("mitarbeiter",$this,"ReplaceMitarbeiter");
  
/*
    $versandart = $this->app->erp->GetVersandartAuftrag();
    $field = new HTMLSelect("versandart",0);
    $field->onchange="versand(this.form.versandart.options[this.form.versandart.selectedIndex].value);";
    $field->AddOptionsSimpleArray($versandart);
    $this->form->NewField($field);

    $status = $this->app->erp->GetStatusKalkulation();
*/
    $field = new HTMLInput("land","hidden","");
    $this->form->NewField($field);
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }


  function ReplaceAuftrag($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceAuftrag($db,$value,$fromform);
  }

  function ReplaceMitarbeiter($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceMitarbeiter($db,$value,$fromform);
  }



  function ReplaceKunde($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceKundennummer($db,$value,$fromform);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }

}
?>
