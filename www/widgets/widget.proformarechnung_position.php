<?php
include ("_gen/widget.gen.proformarechnung_position.php");

class WidgetProformarechnung_position extends WidgetGenProformarechnung_position 
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
    $this->app->YUI->DatePicker("lieferdatum");
    $this->app->YUI->AutoComplete("einheit","artikeleinheit");
    $this->app->YUI->AutoComplete("zolltarifnummer","zolltarifnummer",1);

    //$this->app->YUI->AutoComplete(AUTO,"artikel",array('nummer','name_de','warengruppe'),"nummer");
    $this->form->ReplaceFunction("lieferdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("preis",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("steuersatz",$this,"ReplaceSteuersatz");
    $this->form->ReplaceFunction("menge",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("grundrabatt",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("rabatt",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("rabatt1",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("rabatt2",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("rabatt3",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("rabatt4",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("rabatt5",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("punkte",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("bonuspunkte",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("mlmdirektpraemie",$this,"ReplaceDecimal");
//    $this->app->Tpl->Set(DATUM_LIEFERDATUM,
//        "<img src=\"./themes/[THEME]/images/kalender.png\" onclick=\"displayCalendar(document.forms[0].lieferdatum,'dd.mm.yyyy',this)\">");

    if(!$this->app->erp->RechteVorhanden('rechnung','steuer'))
    {
      $this->app->Tpl->Set('VORSTEUER','<!--');
      $this->app->Tpl->Set('NACHSTEUER','-->');
    }
    
    if(!$this->app->erp->ModulVorhanden('formeln'))
    {
      $this->app->Tpl->Set('VORFORMELN',"<!--");
      $this->app->Tpl->Set('NACHFORMELN',"-->");
    }elseif(!$this->app->erp->RechteVorhanden('proformarechnung', 'formeln'))
    {
      $this->app->Tpl->Set('FORMELNDISPLAY', ' style="display:none" ');
    }
    
    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("beschreibung","belege");
      $this->app->YUI->CkEditor("bemerkung","basic");
    }

    $this->app->erp->ArtikelFreifeldBezeichnungen();

    $field = new HTMLInput("nummer","text","",50);
    $field->readonly="readonly";

    $this->form->NewField($field);
    $this->app->YUI->WaehrungsumrechnungTabelle('WAEHRUNGSBUTTON','WAEHRUNGSTABELLE');
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
  
  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }
  function ReplaceDecimal($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDecimal($db,$value,$fromform);
  }



  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT proformarechnung, id FROM proformarechnung_position");
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
