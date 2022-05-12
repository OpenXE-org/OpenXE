<?php
include ("_gen/widget.gen.verbindlichkeit_position.php");

class WidgetVerbindlichkeit_position extends WidgetGenVerbindlichkeit_position 
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
    $id = $this->app->Secure->GetGET('id');

    
    $this->app->YUI->AutoComplete("einheit","artikeleinheit");
    //$this->app->YUI->AutoComplete("zolltarifnummer","zolltarifnummer",1);

    $this->app->erp->AnzeigeFreifelderPositionen($this->form);

    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->app->YUI->AutoComplete("kostenstelle","kostenstelle",1);

    $this->app->YUI->AutoComplete("artikel","artikelnummer");
    //$this->app->YUI->AutoComplete(AUTO,"artikel",array('nummer','name_de','warengruppe'),"nummer");
    $this->form->ReplaceFunction("lieferdatum",$this,"ReplaceDatum");
    $this->app->YUI->DatePicker("lieferdatum");
    $this->app->YUI->AutoComplete("steuersatz","steuersatz",1);
    $this->form->ReplaceFunction("preis",$this,"ReplaceMengeBetrag");
    $this->form->ReplaceFunction("steuersatz",$this,"ReplaceSteuersatz");
    $this->form->ReplaceFunction("artikel",$this,"ReplaceArtikel");
    $this->form->ReplaceFunction("menge",$this,"ReplaceMenge");
    //$this->form->ReplaceFunction("geliefert",$this,"ReplaceMenge");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");

    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("beschreibung","belege");
      $this->app->YUI->CkEditor("bemerkung","basic");
    }
    

      $this->app->Tpl->Set('VORSTEUER','<!--');
      $this->app->Tpl->Set('NACHSTEUER','-->');
    
  }

  function ReplaceArtikel($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceArtikel($db,$value,$fromform);
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
  
  function ReplaceMengeBetrag($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceMengeBetrag($db,$value,$fromform);
  }
  
  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  function ReplaceSteuersatz($db,$value,$fromform)
  {
    if($db)
    {
      if($value === "" || $value === null)return -1;
      return str_replace(',','.', $value);
    }else{
      if($value < 0)return "";
      return $value;
    }
  }

  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT bestellung, id FROM bestellung_position");
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
