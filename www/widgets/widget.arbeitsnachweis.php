<?php
include ("_gen/widget.gen.arbeitsnachweis.php");

class WidgetArbeitsnachweis extends WidgetGenarbeitsnachweis 
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
    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->app->YUI->AutoComplete("auftragid","auftrag",1);


    $this->app->YUI->CkEditor("freitext","belege");
    $this->app->YUI->CkEditor("internebemerkung","internal");
    $this->app->YUI->DatePicker("datum");


    $this->form->ReplaceFunction("datum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->form->ReplaceFunction("adresse",$this,"ReplaceKunde");
    $this->form->ReplaceFunction("auftragid",$this,"ReplaceAuftrag");
  

    $versandart = $this->app->erp->GetVersandartAuftrag((int)$this->app->DB->Select("SELECT projekt FROM arbeitsnachweis WHERE id = '$id' LIMIT 1"));
    $field = new HTMLSelect("versandart",0);
    $field->onchange="versand(this.form.versandart.options[this.form.versandart.selectedIndex].value);";
    $field->AddOptionsSimpleArray($versandart);
    $this->form->NewField($field);

    $status = $this->app->erp->GetStatusArbeitsnachweis();

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
