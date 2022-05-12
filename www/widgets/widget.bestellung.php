<?php
include ("_gen/widget.gen.bestellung.php");

class WidgetBestellung extends WidgetGenBestellung 
{
  private $app;
  function __construct(&$app,$parsetarget)
  {
    $this->app = &$app;
    $this->parsetarget = $parsetarget;
    parent::__construct($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->RunHook('suppliers_order_widget',1, $id);
    $this->app->YUI->AutoComplete("adresse","lieferant",1);
    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->app->YUI->AutoComplete("preisanfrageid","preisanfrage",1);

    $this->app->YUI->AutoComplete("kostenstelle","kostenstelle",1);

    $this->app->YUI->AutoComplete("lieferbedingung","lieferbedingungen");

    if($this->app->Secure->GetGET("action")=="edit"){
      $this->app->Tpl->Add('FURTHERTABS','<li><a href="index.php?module=bestellung&action=minidetail&id=[ID]&frame=true#tabs-4">Protokoll</a></li>');
      $this->app->Tpl->Add('FURTHERTABSDIV','<div id="tabs-4"></div>');
    }

    $this->app->YUI->DatePicker("datum");

    $this->app->erp->AnzeigeAbweichendeBezeichnung("bestellung");


    $this->app->YUI->DatePicker("bestaetigteslieferdatum");
    $this->app->YUI->DatePicker("gewuenschteslieferdatum");

    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("bodyzusatz","belege");
      $this->app->YUI->CkEditor("freitext","belege");
      $this->app->YUI->CkEditor("internebemerkung","internal");
    }


    $this->form->ReplaceFunction("datum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("gewuenschteslieferdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("bestaetigteslieferdatum",$this,"ReplaceDatum");
    //$this->form->ReplaceFunction("lieferdatum",&$this,"ReplaceDatum");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->form->ReplaceFunction("adresse",$this,"ReplaceLieferant");
    $this->form->ReplaceFunction("preisanfrageid",$this,"ReplacePreisanfrage");
    $this->form->ReplaceFunction("zahlungszielskonto",$this,"ReplaceBetrag");
  
    $versandart = $this->app->erp->GetVersandartAuftrag((int)$this->app->DB->Select("SELECT projekt FROM bestellung WHERE id = '$id' LIMIT 1"));
    $zahlungsweise = $this->app->erp->GetZahlungsweise('bestellung', $id);
    $zahlungsstatus= $this->app->erp->GetZahlungsstatus();
    $typ = $this->app->erp->GetKreditkarten();
    $status = $this->app->erp->GetStatusAuftrag();

        for($i=2009;$i<2020;$i++)
        {
          $jahr[] = $i;
        }

        for($i=1;$i<13;$i++)
        {
          $monat[] = $i;
        }

    //$this->app->erp->GetSelect($versandart,$this->app->
    $field = new HTMLSelect("versandart",0);
    $field->onchange="versand(this.form.versandart.options[this.form.versandart.selectedIndex].value);";
    $field->AddOptionsSimpleArray($versandart);
    $this->form->NewField($field);

    $field = new HTMLSelect("kreditkarte_typ",0);
    $field->AddOptionsSimpleArray($typ);
    $this->form->NewField($field);

    $field = new HTMLSelect("kreditkarte_monat",0);
    $field->AddOptionsSimpleArray($monat);
    $this->form->NewField($field);

    $field = new HTMLSelect("kreditkarte_jahr",0);
    $field->AddOptionsSimpleArray($jahr);
    $this->form->NewField($field);

//    $this->app->Tpl->Set(ONCHANGE_ZAHLUNGSART,"onchange=\"aktion_buchen(this.form.zahlungsweise.options[this.form.zahlungsweise.selectedIndex].value);\"");


    $field = new HTMLSelect("zahlungsweise",0);
    $field->onchange="aktion_buchen(this.form.zahlungsweise.options[this.form.zahlungsweise.selectedIndex].value);";
    $field->AddOptionsSimpleArray($zahlungsweise);
    $this->form->NewField($field);


    $field = new HTMLSelect("zahlungsstatus",0);
    $field->AddOptionsSimpleArray($zahlungsstatus);
    $this->form->NewField($field);

    $waehrungOptions = $this->app->erp->GetWaehrung();
    $field = new HTMLSelect("waehrung",0,"waehrung",false,false,"1");
    $field->AddOptionsSimpleArray($waehrungOptions);
    $this->form->NewField($field);

    $sprachenOptions = $this->app->erp->GetSprachenSelect($id?$this->app->DB->Select("SELECT sprache FROM adresse WHERE id = '$id' LIMIT 1"):null);

    $field = new HTMLSelect("sprache",0,"sprache",false,false,"1");
    //$field->onchange="onchange_typ(this.form.typ.options[this.form.typ.selectedIndex].value);";
    $field->AddOptionsSimpleArray($sprachenOptions);
    $this->form->NewField($field);


    $field = new HTMLCheckbox("abweichendelieferadresse","","","1");    
    $field->onclick="abweichend2();";    
    $this->form->NewField($field);

    $field = new HTMLInput("land","hidden","");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferland","hidden","");
    $this->form->NewField($field);

 		$field = new HTMLInput("datum","text","",10);
    //$field->readonly="readonly";
    $this->form->NewField($field);
    if($this->app->erp->ModulVorhanden('bundesstaaten'))
    {
      $typOptions = $this->app->erp->GetTypSelect();
      $field = new HTMLSelect("bundesstaat",0,"bundesstaat",false,false,"1");
      $this->form->NewField($field);
      $typOptions = $this->app->erp->GetTypSelect();
      $field = new HTMLSelect("lieferbundesstaat",0,"lieferbundesstaat",false,false,"1");
      $this->form->NewField($field);
      if($id > 0){
        $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM bestellung WHERE id = '$id' LIMIT 1");
      }else{
        $schreibschutz = 0;
      }
      $this->app->YUI->BundeslaenderSelect('EPROO_SELECT_BUNDESSTAAT', 'land', 'bundesstaat',$this->app->DB->Select("SELECT land FROM bestellung WHERE id = '$id' LIMIT 1"),$this->app->DB->Select("SELECT bundesstaat FROM bestellung WHERE id = '$id' LIMIT 1"),$schreibschutz?true:false);
      $this->app->YUI->BundeslaenderSelect('EPROO_SELECT_LIEFERBUNDESSTAAT', 'lieferland', 'lieferbundesstaat',$this->app->DB->Select("SELECT lieferland FROM bestellung WHERE id = '$id' LIMIT 1"),$this->app->DB->Select("SELECT lieferbundesstaat FROM bestellung WHERE id = '$id' LIMIT 1"),$schreibschutz?true:false);
    }else{
      $this->app->Tpl->Set('VORBUNDESSTAAT','<!--');
      $this->app->Tpl->Set('NACHBUNDESSTAAT','-->');
    }
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  function ReplacePreisanfrage($db,$value,$fromform)
  {
    return $this->app->erp->ReplacePreisanfrage($db,$value,$fromform);
  }

  function ReplaceLieferant($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceLieferantennummer($db,$value,$fromform);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }

  function ReplaceDecimal($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDecimal($db,$value,$fromform);
  }

  function ReplaceBetrag($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceBetrag($db,$value,$fromform);
  }


}
?>
