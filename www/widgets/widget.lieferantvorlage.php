<?php
include ("_gen/widget.gen.lieferantvorlage.php");

class WidgetLieferantvorlage extends WidgetGenLieferantvorlage 
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
    $versandart = $this->app->erp->GetVersandartLieferant();
    $id = $this->app->Secure->GetGET("id");
    $zahlungsweise = $this->app->erp->GetZahlungsweise('lieferantenvorlage', $id);

    //$this->app->erp->GetSelect($versandart,$this->app->
    $field = new HTMLSelect("versandart",0);
    $field->AddOptionsSimpleArray($versandart);
    $this->form->NewField($field);

    $field = new HTMLSelect("zahlungsweise",0);
    $field->AddOptionsSimpleArray($zahlungsweise);
    $this->form->NewField($field);


  }

  function DatumErsetzen($wert)
  {
    return "neuerwerert";
  }

  public function Table()
  {
    //$table = new EasyTable($this->app);  
    //$table->Query("SELECT nummer, name_de as name,barcode, id FROM lieferantvorlage order by nummer");
    //$table->Display($this->parsetarget);
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
