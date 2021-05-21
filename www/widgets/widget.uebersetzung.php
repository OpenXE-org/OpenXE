<?php
include ("_gen/widget.gen.uebersetzung.php");

class WidgetUebersetzung extends WidgetGenUebersetzung 
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
    if(!$id)
    {
       $field = new HTMLInput("sprache","text",'englisch');
      $this->form->NewField($field);
    }
    //$label = $this->app->DB->Select("SELECT label FROM uebersetzung WHERE id='$id' LIMIT 1");
    //$this->app->Tpl->Set('LABEL',$label);
/*
    $sprache = $this->app->DB->Select("SELECT sprache FROM uebersetzung WHERE id='$id' LIMIT 1");
    if(!$sprache)$sprache = 'englisch';
    $this->app->Tpl->Set('SPRACHE',$sprache);

    $original = $this->app->DB->Select("SELECT original FROM uebersetzung WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set('ORIGINAL',"<textarea rows=10 cols=100>$original</textarea>");
*/
  }

  public function Table()
  {
    $this->app->YUI->TableSearch($this->parsetarget,"uebersetzunglist");
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
