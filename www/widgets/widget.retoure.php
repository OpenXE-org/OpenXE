<?php
include ("_gen/widget.gen.retoure.php");

class WidgetRetoure extends WidgetGenretoure 
{
  private $app;

  /**
   * WidgetRetoure constructor.
   *
   * @param Application $app
   * @param string      $parsetarget
   */
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
    if($this->app->Secure->GetPOST("speichern")!="")
    {
      $returnOrder = $this->app->DB->SelectRow("SELECT `projekt`, `schreibschutz` FROM `retoure` WHERE `id` = {$id}");
      $projektbevor = $returnOrder['projekt'];
      $writeProtected = $returnOrder['schreibschutz'] == '1';
      $projektabkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$projektbevor' LIMIT 1");
      $projektdanach = explode(' ',$this->app->Secure->GetPOST('projekt'));
      $projektdanach = reset($projektdanach);
      
      if(!$writeProtected && $projektdanach != $projektabkuerzung && $projektabkuerzung != '')
      {
        $projektdanach = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '$projektdanach' LIMIT 1");
        $standardlager = $this->app->DB->Select("SELECT standardlager FROM projekt WHERE id = '$projektdanach' LIMIT 1");
        
        if($standardlager && $this->form->CallbackAndMandatorycheck(true))
        {
          $this->form->HTMLList['standardlager']->htmlvalue = $this->app->DB->Select("SELECT bezeichnung FROM lager WHERE id = '$standardlager' LIMIT 1");
          $this->form->HTMLList['standardlager']->dbvalue = $standardlager;
        }
      }
    }
    if($this->app->Secure->GetGET("action")=="edit")
    {
      $this->app->Tpl->Add('FURTHERTABS','<li><a href="index.php?module=retoure&action=minidetail&id=[ID]&frame=true#tabs-4">Protokoll</a></li>');
      $this->app->Tpl->Add('FURTHERTABSDIV','<div id="tabs-4"></div>');
    }

    $this->app->YUI->AutoComplete('adresse','kunde',1);
    $this->app->YUI->AutoComplete('lieferant','lieferant',1);
    $this->app->YUI->AutoComplete('projekt','projektname',1);
    $this->app->YUI->AutoComplete('auftragid','auftrag',1);
    $this->app->YUI->AutoComplete('replacementorder_id','auftrag',1);
    $this->app->YUI->AutoComplete('gutschrift_id','gutschrift',1);
    $this->app->YUI->AutoComplete('lieferscheinid','lieferschein',1);
    $this->app->YUI->AutoComplete('aktion','aktionscode',1);
    $this->app->YUI->AutoComplete('standardlager','lager');

    $this->app->YUI->AutoComplete('lieferbedingung','lieferbedingungen');

    $this->app->erp->AnzeigeAbweichendeBezeichnung('retoure');

    $this->app->YUI->DatePicker('datum');

    $this->form->ReplaceFunction('datum',$this,'ReplaceDatum');
    $this->form->ReplaceFunction('projekt',$this,'ReplaceProjekt');
    $this->form->ReplaceFunction('adresse',$this,'ReplaceKunde');
    $this->form->ReplaceFunction('lieferant',$this,'ReplaceLieferant');
    $this->form->ReplaceFunction('lieferscheinid',$this,'ReplaceLieferschein');
    $this->form->ReplaceFunction('auftragid',$this,'ReplaceAuftrag');
    $this->form->ReplaceFunction('replacementorder_id',$this,'ReplaceAuftrag');
    $this->form->ReplaceFunction('gutschrift_id',$this,'ReplaceGutschrift');
    $this->form->ReplaceFunction('standardlager',$this,'ReplaceLager');

    if($this->app->erp->ModulVorhanden('kommissionskonsignationslager'))
    {
      $this->form->ReplaceFunction("kommissionskonsignationslager",$this,"ReplaceLagerPlatz");
      $this->app->YUI->AutoComplete("kommissionskonsignationslager","lagerplatz");
      $kommissionskonsignationslagerbezeichnung = $this->app->erp->Firmendaten('kommissionskonsignationslager');
      if((String)$kommissionskonsignationslagerbezeichnung === '')$kommissionskonsignationslagerbezeichnung = 'Kommissions-/Konsignationslager';
      $this->app->Tpl->Set('KOMMISSIONIERLAGER', $kommissionskonsignationslagerbezeichnung);
      
    }else{
      $this->app->Tpl->Set('VORKOMMISSIONSKONSIGNATIONSLAGER','<!--');
      $this->app->Tpl->Set('NACHKOMMISSIONSKONSIGNATIONSLAGER','-->');
    }
    
    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("bodyzusatz","belege");
      $this->app->YUI->CkEditor("freitext","belege");
      $this->app->YUI->CkEditor("internebemerkung","internal");
    }

    $typOptions = $this->app->erp->GetTypSelect();
    $field = new HTMLSelect("typ",0);
    //$field->onchange="onchange_typ(this.form.typ.options[this.form.typ.selectedIndex].value);";
    $field->AddOptionsSimpleArray($typOptions);
    $this->form->NewField($field);

    $versandart = $this->app->erp->GetVersandartAuftrag((int)$this->app->DB->Select("SELECT projekt FROM retoure WHERE id = '$id' LIMIT 1"));
    $field = new HTMLSelect("versandart",0);
    $field->onchange="versand(this.form.versandart.options[this.form.versandart.selectedIndex].value);";
    $field->AddOptionsSimpleArray($versandart);
    $this->form->NewField($field);

    $status = $this->app->erp->GetStatusLieferschein();

    $field = new HTMLInput("land","hidden","");
    $this->form->NewField($field);

    $sprachenOptions = $this->app->erp->GetSprachenSelect($id?$this->app->DB->Select("SELECT sprache FROM adresse WHERE id = '$id' LIMIT 1"):null);
    
    $field = new HTMLSelect("sprache",0,"sprache",false,false,"1");
    //$field->onchange="onchange_typ(this.form.typ.options[this.form.typ.selectedIndex].value);";
    $field->AddOptionsSimpleArray($sprachenOptions);
    $this->form->NewField($field);

    $field = new HTMLCheckbox("lieferantenretoure","","","1");
    $field->onclick="lieferantenretoureanzeige(this.form.lieferantenretoure.value);";
    $this->form->NewField($field);
    
    
    $this->app->Tpl->Set('VORWUNSCHLAGER','<!--');
    $this->app->Tpl->Set('NACHWUNSCHLAGER','-->');
    if($this->app->DB->Select("SELECT count(id) FROM lager") > 1 
      || $this->app->DB->Select("SELECT p.standardlager FROM retoure l LEFT JOIN projekt p ON l.projekt=p.id WHERE l.id='$id'") > 0)
    {
      $this->app->Tpl->Set('VORWUNSCHLAGER','');
      $this->app->Tpl->Set('NACHWUNSCHLAGER','');
    }
  
    if($this->app->erp->ModulVorhanden('bundesstaaten'))
    {
      $typOptions = $this->app->erp->GetTypSelect();
      $field = new HTMLSelect("bundesstaat",0,"bundesstaat",false,false,"1");
      $this->form->NewField($field);
      if($id > 0){
        $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM retoure WHERE id = '$id' LIMIT 1");
      }else{
        $schreibschutz = 0;
      }
      $this->app->YUI->BundeslaenderSelect('EPROO_SELECT_BUNDESSTAAT', 'land', 'bundesstaat',$this->app->DB->Select("SELECT land FROM retoure WHERE id = '$id' LIMIT 1"),$this->app->DB->Select("SELECT bundesstaat FROM retoure WHERE id = '$id' LIMIT 1"),$schreibschutz?true:false);
    }else{
      $this->app->Tpl->Set('VORBUNDESSTAAT','<!--');
      $this->app->Tpl->Set('NACHBUNDESSTAAT','-->');
    }
    
/*
    $field = new HTMLInput("datum","text","",10);
    $field->readonly="readonly";
    $this->form->NewField($field);
*/
    /*$field = new HTMLSelect('fortschritt',0,'fortschritt',false,false);
    $this->form->NewField($field);*/
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  function ReplaceAuftrag($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceAuftrag($db,$value,$fromform);
  }

  public function ReplaceGutschrift($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceANABRELSGSBE('gutschrift',$db,$value,$fromform);
  }
  
  function ReplaceLieferschein($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceLieferschein($db,$value,$fromform);
  }


  function ReplaceLieferant($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceLieferantennummer($db,$value,$fromform);
  }


  function ReplaceKunde($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceKundennummer($db,$value,$fromform);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }
  
  function ReplaceLager($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceLager($db,$value,$fromform);
  }
  
  function ReplaceLagerPlatz($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceLagerPlatz($db,$value,$fromform);
  }
}
