<?php
include ("_gen/widget.gen.uservorlage.php");

class WidgetUservorlage extends WidgetGenUservorlage 
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
  }
  
  public function Table()
  {
    //$table->Query("SELECT nummer,beschreibung, id FROM uservorlage");
 		$table = new EasyTable($this->app);
    $this->app->Tpl->Set('INHALT',"");
    $table->Query("SELECT bezeichnung, id FROM uservorlage");
    $table->DisplayNew($this->parsetarget, "<a href=\"index.php?module=uservorlage&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.svg\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=uservorlage&action=delete&id=%value%';\">
          <img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\"></a>
        ");

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
