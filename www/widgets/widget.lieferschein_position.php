<?php
include ("_gen/widget.gen.lieferschein_position.php");

class WidgetLieferschein_position extends WidgetGenLieferschein_position 
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

    $this->app->erp->AnzeigeFreifelderPositionen($this->form);

    $this->app->YUI->AutoComplete("einheit","artikeleinheit");
    $this->app->YUI->AutoComplete("zolltarifnummer","zolltarifnummer",1);

    //$this->app->YUI->AutoComplete(AUTO,"artikel",array('nummer','name_de','warengruppe'),"nummer");
    $this->app->YUI->DatePicker("lieferdatum");

    $this->form->ReplaceFunction("lieferdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("menge",$this,"ReplaceMenge");

    $this->form->ReplaceFunction("zolleinzelwert",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("zollgesamtwert",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("zolleinzelgewicht",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("zollgesamtgewicht",$this,"ReplaceDecimal");

    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("beschreibung","belege");
      $this->app->YUI->CkEditor("bemerkung","basic");
    }

    $this->app->erp->ArtikelFreifeldBezeichnungen();

    $field = new HTMLInput("nummer","text","",50);
    $field->readonly="readonly";

    $this->form->NewField($field);
    $this->app->erp->RunHook("lieferschein_position_widget", 0);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }

  function ReplaceDecimal($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDecimal($db,$value,$fromform);
  }
  
  function ReplaceMenge($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceMenge($db,$value,$fromform);
  }  


  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT lieferschein, id FROM lieferschein_position");
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
