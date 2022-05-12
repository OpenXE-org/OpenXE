<?php
include ("_gen/widget.gen.artikeloptionengruppe.php");

class WidgetArtikeloptionengruppe extends WidgetGenArtikeloptionengruppe
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
    $this->app->YUI->AutoComplete("artikel","artikelnummer",1);
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->form->ReplaceFunction("artikel",$this,"ReplaceArtikel");


  }

  public function Table()
  {
    $this->app->YUI->TableSearch($this->parsetarget,"artikeloptionengruppelist");//, "show","","",'artikeloptionengruppe', 'Artikeloptionengruppe');
  }



  public function Search()
  {
    //    $this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }
  function ReplaceArtikel($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceArtikel($db,$value,$fromform);
  }


}
?>
