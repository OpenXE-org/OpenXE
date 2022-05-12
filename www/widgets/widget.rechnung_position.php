<?php
include ("_gen/widget.gen.rechnung_position.php");

class WidgetRechnung_position extends WidgetGenRechnung_position 
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

    $id = (int)$this->app->Secure->GetGET('id');
    $projektabkuerzung = $this->app->DB->Select("SELECT p.abkuerzung FROM rechnung_position ap LEFT JOIN rechnung a ON a.id=ap.rechnung LEFT JOIN projekt p ON p.id=a.projekt WHERE ap.id='$id' LIMIT 1");
    $this->app->YUI->AutoComplete('erloese','sachkonto',1,"&sid=".$projektabkuerzung);


    $this->app->YUI->DatePicker("lieferdatum");
    $this->app->YUI->AutoComplete("einheit","artikeleinheit");
    $this->app->YUI->AutoComplete("zolltarifnummer","zolltarifnummer",1);
    $this->app->YUI->AutoComplete("steuersatz","steuersatz",1);
    $this->app->YUI->AutoComplete("kostenstelle","kostenstelle",1);
    //$this->app->YUI->AutoComplete(AUTO,"artikel",array('nummer','name_de','warengruppe'),"nummer");
    $this->form->ReplaceFunction("lieferdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("preis",$this,"ReplaceMengeBetrag");
    $this->form->ReplaceFunction("steuersatz",$this,"ReplaceSteuersatz");
    $this->form->ReplaceFunction("menge",$this,"ReplaceMenge");
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
    }elseif(!$this->app->erp->RechteVorhanden('rechnung', 'formeln'))
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
    $table->Query("SELECT rechnung, id FROM rechnung_position");
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
