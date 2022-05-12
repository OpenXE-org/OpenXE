<?php
include ("_gen/widget.gen.wissensdatenbank.php");

class WidgetWissensdatenbank extends WidgetGenWissensdatenbank
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

    $this->app->YUI->CkEditor("text","internal",array("height"=>"650"));
    $this->app->YUI->CkEditor("bemerkung","internal");

    //$this->form->ReplaceFunction("startdatum",$this,"ReplaceDatum");
    //$this->form->ReplaceFunction("mitarbeiter",$this,"ReplaceMitarbeiter");
    //$this->form->ReplaceFunction("adresse",$this,"ReplaceKunde");
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  function ReplaceKunde($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceKunde($db,$value,$fromform);
  }

  function ReplaceMitarbeiter($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceMitarbeiter($db,$value,$fromform);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }



}
?>
