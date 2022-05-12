<?php
include ("_gen/widget.gen.angebot_position.php");

class WidgetAngebot_position extends WidgetGenAngebot_position 
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
    $this->app->YUI->AutoComplete("einheit","artikeleinheit");

    $this->app->erp->AnzeigeFreifelderPositionen($this->form);
    $id = (int)$this->app->Secure->GetGET('id');
    if($this->app->DB->Select("SELECT explodiert_parent FROM angebot_position WHERE id = '$id' LIMIT 1"))
    {
      $this->app->Tpl->Set('VORPREISBERECHNEN','<!--');
      $this->app->Tpl->Set('NACHPREISBERECHNEN','-->');
    }
    //$this->app->YUI->AutoComplete(AUTO,"artikel",array('nummer','name_de','warengruppe'),"nummer");
    $this->form->ReplaceFunction("lieferdatum",$this,"ReplaceDatum");
    $this->app->YUI->AutoComplete("zolltarifnummer","zolltarifnummer",1);
    $this->app->YUI->DatePicker("lieferdatum");
    $this->app->YUI->AutoComplete("steuersatz","steuersatz",1);

    $projektabkuerzung = $this->app->DB->Select("SELECT p.abkuerzung FROM angebot_position ap LEFT JOIN angebot a ON a.id=ap.angebot LEFT JOIN projekt p ON p.id=a.projekt WHERE ap.id='$id' LIMIT 1");
    $this->app->YUI->AutoComplete('erloese','sachkonto',1,"&sid=".$projektabkuerzung);

    $this->app->YUI->AutoComplete("kostenstelle","kostenstelle",1);
    $this->app->YUI->AutoComplete("explodiert_parent","angebot_position",0,"&angebotposition=".$id."&angebot=".$this->app->DB->Select("SELECT angebot FROM angebot_position WHERE id = '$id' LIMIT 1"));
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
    
    $this->form->ReplaceFunction("explodiert_parent",$this,"ReplaceAngebotposition");

    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("beschreibung","belege");
      $this->app->YUI->CkEditor("bemerkung","basic");
    }
    
    if(!$this->app->erp->ModulVorhanden('formeln'))
    {
      $this->app->Tpl->Set('VORFORMELN',"<!--");
      $this->app->Tpl->Set('NACHFORMELN',"-->");
    }elseif(!$this->app->erp->RechteVorhanden('angebot', 'formeln'))
    {
      $this->app->Tpl->Set('FORMELNDISPLAY', ' style="display:none" ');
    }
    
    $this->app->erp->ArtikelFreifeldBezeichnungen();

    $this->form->ReplaceFunction("lieferdatum",$this,"ReplaceDatum");

    $field = new HTMLInput("nummer","text","",50);
    $field->readonly="readonly";

    $this->form->NewField($field);
    $this->app->YUI->WaehrungsumrechnungTabelle('WAEHRUNGSBUTTON','WAEHRUNGSTABELLE');
    $this->app->erp->RunHook('angebot_position_widget', 0);
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

  function ReplaceAngebotposition($db,$value,$fromform)
  {
    $id = $this->app->Secure->GetGET('id');
    $angebot = $this->app->DB->Select("SELECT angebot FROM angebot_position WHERE id = '$id' LIMIT 1");
    $value = (int)reset(explode(' ',$value));
    if(!$value)return '';
    if($db)
    {
      return $this->app->DB->Select("SELECT id FROM angebot_position WHERE angebot = '$angebot' AND sort = '$value' LIMIT 1");
    }
    return $this->app->DB->Select("SELECT concat(sort,' ',nummer) FROM angebot_position WHERE id = '$value' LIMIT 1");
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
    $table->Query("SELECT angebot, id FROM angebot_position");
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
