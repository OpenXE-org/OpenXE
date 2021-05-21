<?php
include ("_gen/widget.gen.arbeitsfreietage.php");

class WidgetArbeitsfreietage extends WidgetGenArbeitsfreietage 
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

    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");

    $this->app->YUI->DatePicker("datum");
    $this->form->ReplaceFunction("datum",$this,"ReplaceDatum");

  }

  public function Table()
  {
    $this->app->YUI->TableSearch($this->parsetarget,"arbeitsfreietage_list");
  }

  public function Search()
  {

  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }
}

