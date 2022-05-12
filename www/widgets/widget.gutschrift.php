<?php
include ("_gen/widget.gen.gutschrift.php");

class WidgetGutschrift extends WidgetGenGutschrift 
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
    if($id > 0 && $this->app->Secure->GetPOST('speichern')!='') {
      $before = $this->app->DB->SelectRow(
        sprintf('SELECT projekt, schreibschutz FROM gutschrift WHERE id = %d', $id)
      );
      $schreibschutzbefore = $before['schreibschutz'];
      $projektbevor = $before['projekt'];

      $projektabkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$projektbevor' LIMIT 1");
      $projektdanach = explode(' ',$this->app->Secure->GetPOST('projekt'));
      $projektdanach = reset($projektdanach);
      $projektdanachid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '$projektdanach' AND IFNULL(geloescht,0) = 0 LIMIT 1");
      if(!$schreibschutzbefore && $projektdanach != $projektabkuerzung){
        $this->app->erp->LoadSteuersaetze($id, 'gutschrift', $projektdanachid);
      }
      if(!$schreibschutzbefore && (bool)$this->app->Secure->GetPOST('schreibschutz')) {
        $this->app->DB->Update(
          sprintf(
            'UPDATE gutschrift SET zuarchivieren = 1 WHERE id = %d',
            $id
          )
        );
      }
    }
    $this->app->YUI->AutoComplete("adresse","kunde",1);
    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->app->YUI->AutoComplete("rechnungid","rechnung",1);
    $this->app->YUI->AutoComplete("aktion","aktionscode",1);
    $this->app->YUI->AutoComplete("lieferbedingung","lieferbedingungen");

    $this->app->YUI->AutoComplete("kostenstelle","kostenstelle",1);

    $this->form->ReplaceFunction("kurs",$this,"ReplaceBetrag");

    //$this->app->YUI->DatePicker("datum");
    $this->app->YUI->DatePicker("ustbrief_eingang_am");
    $this->app->YUI->DatePicker("lieferdatum");
    $this->app->YUI->DatePicker("datum");
    $this->app->YUI->DatePicker("manuell_vorabbezahlt");

    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("bodyzusatz","belege");
      $this->app->YUI->CkEditor("freitext","belege");
      $this->app->YUI->CkEditor("internebemerkung","internal");
    }


    if($this->app->Secure->GetGET("action")=="edit")
    { 
      $this->app->Tpl->Add('FURTHERTABS','<li><a href="index.php?module=gutschrift&action=minidetail&id=[ID]&frame=true#tabs-4">Protokoll</a></li>');
      $this->app->Tpl->Add('FURTHERTABSDIV','<div id="tabs-4"></div>');                
    }

    $id = $this->app->Secure->GetGET("id");
    $stornorechnung = $this->app->DB->Select("SELECT stornorechnung FROM gutschrift WHERE id='$id' LIMIT 1");
    if($stornorechnung)
      $this->app->Tpl->Set('BEZEICHNUNGTITEL',$this->app->erp->Firmendaten("bezeichnungstornorechnung"));
    else
      $this->app->Tpl->Set('BEZEICHNUNGTITEL','Gutschrift');

    $this->app->Tpl->Set('BEZEICHNUNGSTORNORECHNUNG',$this->app->erp->Firmendaten("bezeichnungstornorechnung"));


    $this->form->ReplaceFunction("datum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("lieferdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("manuell_vorabbezahlt",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("ustbrief_eingang_am",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->form->ReplaceFunction("adresse",$this,"ReplaceKunde");
    $this->form->ReplaceFunction("rechnungid",$this,"ReplaceRechnung");
    $this->form->ReplaceFunction("zahlungszielskonto",$this,"ReplaceBetrag");
    //    $this->form->ReplaceFunction("rechnung",&$this,"ReplaceRechnung");


    $typOptions = $this->app->erp->GetTypSelect();
    $field = new HTMLSelect("typ",0);
    //$field->onchange="onchange_typ(this.form.typ.options[this.form.typ.selectedIndex].value);";
    $field->AddOptionsSimpleArray($typOptions);
    $this->form->NewField($field);

    $zahlungsweise = $this->app->erp->GetZahlungsweiseGutschrift($id);

    $zahlungsstatus= $this->app->erp->GetZahlungsstatus();

    $zahlungsstatus= $this->app->DB->Select("SELECT zahlungsstatus FROM gutschrift WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set('ZAHLUNGSSTATUS',$zahlungsstatus);

    $status = $this->app->erp->GetStatusGutschrift();

    //$this->app->erp->GetSelect($versandart,$this->app->

    $field = new HTMLSelect("zahlungsweise",0);
    $field->onchange="aktion_buchen(this.form.zahlungsweise.options[this.form.zahlungsweise.selectedIndex].value);";
    $field->AddOptionsSimpleArray($zahlungsweise);
    $this->form->NewField($field);


    $field = new HTMLInput("land","hidden","");
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


    $field = new HTMLInput("datum","text","",10);
    $field->readonly="readonly";
    $this->form->NewField($field);

    $id = $this->app->Secure->GetGET("id");
    
    if($this->app->erp->ModulVorhanden('bundesstaaten'))
    {
      $typOptions = $this->app->erp->GetTypSelect();
      $field = new HTMLSelect("bundesstaat",0,"bundesstaat",false,false,"1");
      $this->form->NewField($field);
      if($id > 0){
        $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM gutschrift WHERE id = '$id' LIMIT 1");
      }else{
        $schreibschutz = 0;
      }
      $this->app->YUI->BundeslaenderSelect('EPROO_SELECT_BUNDESSTAAT', 'land', 'bundesstaat',$this->app->DB->Select("SELECT land FROM gutschrift WHERE id = '$id' LIMIT 1"),$this->app->DB->Select("SELECT bundesstaat FROM gutschrift WHERE id = '$id' LIMIT 1"),$schreibschutz?true:false);
    }else{
      $this->app->Tpl->Set('VORBUNDESSTAAT','<!--');
      $this->app->Tpl->Set('NACHBUNDESSTAAT','-->');
    }
    
    $rechnung = $this->app->DB->Select("SELECT rechnung FROM gutschrift WHERE id='$id' LIMIT 1");
    $adresse_gut = $this->app->DB->Select("SELECT adresse FROM gutschrift WHERE id='$id' LIMIT 1");
    /*
       $rechnungid = $this->app->DB->Select("SELECT id FROM rechnung WHERE belegnr='$rechnung' AND adresse='$adresse_gut' LIMIT 1");
       if($rechnungid > 0)
       $this->app->DB->Update("UPDATE gutschrift SET rechnungid='$rechnungid' WHERE id='$id' LIMIT 1");
       else
       $this->app->DB->Update("UPDATE gutschrift SET rechnungid='' WHERE id='$id' LIMIT 1");
     */
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }


  function ReplaceRechnung($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceRechnung($db,$value,$fromform);
  }

  function ReplaceKunde($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceKundennummer($db,$value,$fromform);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }

  function ReplaceBetrag($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceBetrag($db,$value,$fromform);
  }

  function ReplaceDecimal($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDecimal($db,$value,$fromform);
  }
}
