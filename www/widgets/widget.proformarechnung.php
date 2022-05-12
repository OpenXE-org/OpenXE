<?php
include ("_gen/widget.gen.proformarechnung.php");

class WidgetProformarechnung extends WidgetGenProformarechnung 
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
    $id = $this->app->Secure->GetGET('id');
    $this->app->YUI->AutoComplete("adresse","kunde",1);
    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->app->YUI->AutoComplete("lieferschein","lieferschein",1);
    $this->app->YUI->AutoComplete("auftragid","auftrag",1);
    $this->app->YUI->AutoComplete("gruppe","verband");
    $this->app->YUI->AutoComplete("aktion","aktionscode",1);
    $this->app->YUI->AutoComplete("lieferbedingung","lieferbedingungen");

    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("bodyzusatz","belege");
      $this->app->YUI->CkEditor("freitext","belege");
      $this->app->YUI->CkEditor("internebemerkung","internal");
      $this->app->YUI->CkEditor("verzollinformationen","belege");
    }

    if($this->app->Secure->GetGET("action")=="edit")
    {
      $this->app->Tpl->Add('FURTHERTABS','<li><a href="index.php?module=proformarechnung&action=minidetail&id=[ID]&frame=true#tabs-4">Protokoll</a></li>');
      $this->app->Tpl->Add('FURTHERTABSDIV','<div id="tabs-4"></div>');
    }

    $this->app->erp->AnzeigeAbweichendeBezeichnung("proformarechnung");

    //$this->app->YUI->DatePicker("datum");
    $this->app->YUI->DatePicker("ustbrief_eingang_am");
    $this->app->YUI->DatePicker("lieferdatum");
    $this->app->YUI->DatePicker("datum");
    $this->app->YUI->DatePicker("bezahlt_am");
    $this->app->YUI->DatePicker("einzugsdatum");
    $this->app->YUI->DatePicker("mahnwesen_datum");
    $this->app->YUI->HideFormular("verzollungadresse", array("unchecked"=>"verzollung","checked"=>"dummy"));

    if($auftragArr[0]['rma']==1)
      $this->app->YUI->ParserVarIf('RMA',1);
    else
      $this->app->YUI->ParserVarIf('RMA',0);

    $this->form->ReplaceFunction("adresse",$this,"ReplaceKundennummer");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");

    $this->form->ReplaceFunction("datum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("einzugsdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("mahnwesen_datum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("lieferdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("ustbrief_eingang_am",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("lieferschein",$this,"ReplaceLieferschein");
    $this->form->ReplaceFunction("gruppe",$this,"ReplaceGruppe");
    $this->form->ReplaceFunction("auftragid",$this,"ReplaceAuftrag");
    $this->form->ReplaceFunction("datum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("bezahlt_am",$this,"ReplaceDatum");

    $waehrungOptions = $this->app->erp->GetWaehrung();
    $field = new HTMLSelect("waehrung",0,"waehrung",false,false,"1");
    $field->AddOptionsSimpleArray($waehrungOptions);
    $this->form->NewField($field);

    $sprachenOptions = $this->app->erp->GetSprachenSelect($id?$this->app->DB->Select("SELECT sprache FROM adresse WHERE id = '$id' LIMIT 1"):null);

    $field = new HTMLSelect("sprache",0,"sprache",false,false,"1");
    //$field->onchange="onchange_typ(this.form.typ.options[this.form.typ.selectedIndex].value);";
    $field->AddOptionsSimpleArray($sprachenOptions);
    $this->form->NewField($field);

    $typOptions = $this->app->erp->GetTypSelect();
    $field = new HTMLSelect("typ",0);
    //$field->onchange="onchange_typ(this.form.typ.options[this.form.typ.selectedIndex].value);";
    $field->AddOptionsSimpleArray($typOptions);
    $this->form->NewField($field);

    $id = $this->app->Secure->GetGET("id");
    $zahlungsweise = $this->app->erp->GetZahlungsweise('proformarechnung', $id);
    $zahlungsstatus= $this->app->erp->GetZahlungsstatus();

    $zahlungsstatus= $this->app->DB->Select("SELECT zahlungsstatus FROM proformarechnung WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set('ZAHLUNGSSTATUS',$zahlungsstatus);

    $status = $this->app->erp->GetStatusRechnung();

    $field = new HTMLSelect("zahlungsweise",0);
    $field->onchange="aktion_buchen(this.form.zahlungsweise.options[this.form.zahlungsweise.selectedIndex].value);";
    $field->AddOptionsSimpleArray($zahlungsweise);
    $this->form->NewField($field);

    $field = new HTMLCheckbox("abweichendelieferadresse","","","1");
    $field->onclick="abweichend2();";
    $this->form->NewField($field);

    $field = new HTMLInput("land","hidden","");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferland","hidden","");
    $this->form->NewField($field);

    $field = new HTMLInput("verzollungland","hidden","");
    $this->form->NewField($field);



    $field = new HTMLCheckbox("mahnwesenfestsetzen","","","1");
    $field->onclick="mahnwesenfest(this.form.mahnwesenfestsetzen.value);";
    $this->form->NewField($field);


    for($i=date('Y');$i<date('Y')+20;$i++)
    {
      $jahr[] = $i;
    }

    for($i=1;$i<13;$i++)
    {
      $monat[] = $i;
    }
    $this->app->erp->AdresseAlsVerzollungsadresseButton($this->app->DB->Select("SELECT adresse FROM proformarechnung WHERE id = '$id' LIMIT 1"));

    $field = new HTMLSelect("kreditkarte_typ",0);
    $field->AddOptionsSimpleArray($typ);
    $this->form->NewField($field);

    $field = new HTMLSelect("kreditkarte_monat",0);
    $field->AddOptionsSimpleArray($monat);
    $this->form->NewField($field);

    $field = new HTMLSelect("kreditkarte_jahr",0);
    $field->AddOptionsSimpleArray($jahr);
    $this->form->NewField($field);

    $field = new HTMLInput("datum","text","",10);
    $field->readonly="readonly";
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
        $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM proformarechnung WHERE id = '$id' LIMIT 1");
      }else{
        $schreibschutz = 0;
      }
      $this->app->YUI->BundeslaenderSelect('EPROO_SELECT_BUNDESSTAAT', 'land', 'bundesstaat',$this->app->DB->Select("SELECT land FROM proformarechnung WHERE id = '$id' LIMIT 1"),$this->app->DB->Select("SELECT bundesstaat FROM proformarechnung WHERE id = '$id' LIMIT 1"),$schreibschutz?true:false);
      $this->app->YUI->BundeslaenderSelect('EPROO_SELECT_LIEFERBUNDESSTAAT', 'lieferland', 'lieferbundesstaat',$this->app->DB->Select("SELECT lieferland FROM proformarechnung WHERE id = '$id' LIMIT 1"),$this->app->DB->Select("SELECT lieferbundesstaat FROM proformarechnung WHERE id = '$id' LIMIT 1"),$schreibschutz?true:false);
    }else{
      $this->app->Tpl->Set('VORBUNDESSTAAT','<!--');
      $this->app->Tpl->Set('NACHBUNDESSTAAT','-->');
    }
  }

  function ReplaceGruppe($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceGruppe($db,$value,$fromform);
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  function ReplaceKundennummer($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceKundennummer($db,$value,$fromform);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }

  function ReplaceLieferschein($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceLieferschein($db,$value,$fromform);
  }

  function ReplaceAuftrag($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceAuftrag($db,$value,$fromform);
  }




}
?>
