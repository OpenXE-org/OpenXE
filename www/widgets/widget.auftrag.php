<?php
include ("_gen/widget.gen.auftrag.php");

class WidgetAuftrag extends WidgetGenAuftrag 
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
    $this->form->ReplaceFunction("datum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("reservationdate",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("einzugsdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("lieferdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("tatsaechlicheslieferdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("gueltigbis",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->form->ReplaceFunction("adresse",$this,"ReplaceKunde");
    $this->form->ReplaceFunction("angebotid",$this,"ReplaceAngebot");
    $this->form->ReplaceFunction("gruppe",$this,"ReplaceGruppe");
    $this->form->ReplaceFunction("standardlager",$this,"ReplaceLager");
    $this->form->ReplaceFunction("zahlungszielskonto",$this,"ReplaceBetrag");


    $this->form->ReplaceFunction("kurs",$this,"ReplaceBetrag");
    $this->app->YUI->AutoComplete("kostenstelle","kostenstelle",1);
    
    if($this->app->Secure->GetPOST('speichern')!='') {
      $before = $this->app->DB->SelectRow(
        sprintf('SELECT projekt, schreibschutz FROM auftrag WHERE id = %d', $id)
      );
      $schreibschutzbefore = $before['schreibschutz'];
      $projektbevor = $before['projekt'];

      $projektabkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$projektbevor' LIMIT 1");
      $projektdanach = explode(' ',$this->app->Secure->GetPOST('projekt'));
      $projektdanach = reset($projektdanach);
      $overwriteZahlungsweise = null;
      $overwriteVersandart = null;
      if(!$schreibschutzbefore && $projektdanach != $projektabkuerzung){
        $projektdanach = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '$projektdanach' LIMIT 1");
        $this->app->erp->LoadSteuersaetze($id, 'auftrag', $projektdanach);
        if(!empty($projektdanach)){
          $standardlager = $this->app->DB->Select("SELECT l.id FROM projekt p INNER JOIN lager l ON p.standardlager = l.id WHERE p.id = '$projektdanach' LIMIT 1");
          if($standardlager && $this->form->CallbackAndMandatorycheck(true)){
            $this->form->HTMLList['standardlager']->htmlvalue = $this->app->DB->Select("SELECT bezeichnung FROM lager WHERE id = '$standardlager' LIMIT 1");
            $this->form->HTMLList['standardlager']->dbvalue = $standardlager;
          }
          $deactivateautoshipping = $this->app->erp->Projektdaten($projektdanach, 'deactivateautoshipping');
          if($deactivateautoshipping && $this->form->CallbackAndMandatorycheck(true)) {
            $this->form->HTMLList['autoversand']->htmlvalue = 0;
            $this->form->HTMLList['autoversand']->dbvalue = 0;
          }
          $query = sprintf("SELECT zahlungsweise, zahlungsweiselieferant, versandart FROM projekt WHERE id='%s'",
            $projektdanach);
          $projectDefaultData = $this->app->DB->SelectRow($query);
          if($this->form->HTMLList['lieferantenauftrag']->htmlvalue){
            if(!empty($projectDefaultData['zahlungsweiselieferant']) && $this->form->CallbackAndMandatorycheck(true)){
              $overwriteZahlungsweise = $projectDefaultData['zahlungsweiselieferant'];
            }
          }else{
            if(!empty($projectDefaultData['zahlungsweise']) && $this->form->CallbackAndMandatorycheck(true)){
              $overwriteZahlungsweise = $projectDefaultData['zahlungsweise'];
            }
          }
          if(!empty($projectDefaultData['versandart']) && $this->form->CallbackAndMandatorycheck(true)){
            $overwriteVersandart = $projectDefaultData['versandart'];
          }
        }
      }
      if(!$schreibschutzbefore && (bool)$this->app->Secure->GetPOST('schreibschutz')) {
        $this->app->DB->Update(
          sprintf(
            'UPDATE auftrag SET zuarchivieren = 1 WHERE id = %d',
            $id
          )
        );
      }
      if($this->form->CallbackAndMandatorycheck(true))
      {
        if($this->app->Secure->GetPOST('lieferantenauftrag'))
        {
          $this->app->DB->Update("UPDATE auftrag SET lieferantkdrnummer = '".reset(explode(' ',($this->app->Secure->GetPOST('lieferant'))))."' WHERE id = '$id' LIMIT 1");
        }else{
          $this->app->DB->Update("UPDATE auftrag SET lieferantkdrnummer = '".reset(explode(' ',($this->app->Secure->GetPOST('adresse'))))."' WHERE id = '$id' LIMIT 1");
        }
        $modus = 'speichern';
        $this->app->erp->RunHook('auftrageditdetail', 2, $id, $modus);
      }
    }
    $adresse = $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$id' LIMIT 1");

    $this->app->YUI->AutoComplete("adresse","kunde",1);
    $this->app->YUI->AutoComplete("lieferant","lieferant",1);
    $this->app->YUI->AutoComplete("angebotid","angebot",1);
    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->app->YUI->AutoComplete("gruppe","verband");
    $this->app->YUI->AutoComplete("aktion","aktionscode",1);
    $this->app->YUI->AutoComplete("standardlager","lager");
    $this->app->YUI->AutoComplete("lieferbedingung","lieferbedingungen");

    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("bodyzusatz","belege");
      $this->app->YUI->CkEditor("freitext","belege");
      $this->app->YUI->CkEditor("internebemerkung","internal");    
    }

    $this->form->ReplaceFunction("lieferant",$this,"ReplaceLieferant");

    $field = new HTMLCheckbox("lieferantenauftrag","","","1");
    $field->onclick="lieferantenauftraganzeige(this.form.lieferantenauftrag.value);";
    $this->form->NewField($field);

    $this->app->erp->AnzeigeAbweichendeBezeichnung("auftrag");

    if($adresse > 0)
    {
      $this->app->YUI->AutoComplete("ihrebestellnummer","ihrebestellnummer",0,"&adresse=$adresse");
    }

    if($this->app->Secure->GetGET("action")=="edit"){
      $this->app->Tpl->Add('FURTHERTABS','<li><a href="index.php?module=auftrag&action=minidetail&id=[ID]&frame=true#tabs-4">Protokoll</a></li>');
      $this->app->Tpl->Add('FURTHERTABSDIV','<div id="tabs-4"></div>');
    }


    $this->app->YUI->DatePicker("datum");
    $this->app->YUI->DatePicker("lieferdatum");
    $this->app->YUI->DatePicker("einzugsdatum");
    $this->app->YUI->DatePicker("reservationdate");
    $this->app->YUI->DatePicker("tatsaechlicheslieferdatum");

    $this->app->Tpl->Set('READONLY',"readonly");



    $typOptions = $this->app->erp->GetTypSelect();
    $field = new HTMLSelect("typ",0);
    //$field->onchange="onchange_typ(this.form.typ.options[this.form.typ.selectedIndex].value);";
    $field->AddOptionsSimpleArray($typOptions);
    $this->form->NewField($field);

    $versandart = $this->app->erp->GetVersandartAuftrag((int)$this->app->DB->Select("SELECT projekt FROM auftrag WHERE id = '$id' LIMIT 1"));
    $zahlungsweise = $this->app->erp->GetZahlungsweise('auftrag', $id);
    $zahlungsstatus= $this->app->erp->GetZahlungsstatus();
    $typ = $this->app->erp->GetKreditkarten();
    $status = $this->app->erp->GetStatusAuftrag();


    for($i=2009;$i<date('Y')+5;$i++)
    {
      $jahr[] = $i;
    }

    for($i=1;$i<13;$i++)
    {
      $monat[] = $i;
    }


    $this->app->Tpl->Set('VORWUNSCHLAGER','<!--');
    $this->app->Tpl->Set('NACHWUNSCHLAGER','-->');
    $projectRow =$this->app->DB->SelectRow(
      "SELECT p.standardlager, p.kommissionierverfahren, a.projekt
       FROM auftrag a 
       INNER JOIN projekt p ON a.projekt=p.id 
       WHERE a.id='$id'"
    );
    $projekt = $projectRow['projekt'];//$this->app->DB->Select("SELECT projekt FROM auftrag WHERE id = '$id' LIMIT 1");
    if($projekt) {
      if($this->app->DB->Select("SELECT count(id) FROM lager") > 1
      || $projectRow['standardlager'] > 0) {
        $kommissionierverfahren = $projectRow['kommissionierverfahren'];//$this->app->DB->Select("SELECT kommissionierverfahren FROM projekt WHERE id = '$projekt' LIMIT 1");
        if(($kommissionierverfahren==='lieferscheinlager'
          || $kommissionierverfahren==='lieferscheinlagerscan'
          || $kommissionierverfahren==='lieferscheinscan'
          || $kommissionierverfahren === 'lieferschein')) {
          $this->app->Tpl->Set('VORWUNSCHLAGER','');
          $this->app->Tpl->Set('NACHWUNSCHLAGER','');
        }
      }
    }
    
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


    $waehrungOptions = $this->app->erp->GetWaehrung();
    $field = new HTMLSelect("waehrung",0,"waehrung",false,false,"1");
    $field->AddOptionsSimpleArray($waehrungOptions);
    $this->form->NewField($field);

    $sprachenOptions = $this->app->erp->GetSprachenSelect($id?$this->app->DB->Select("SELECT sprache FROM adresse WHERE id = '$id' LIMIT 1"):null);

    $field = new HTMLSelect("sprache",0,"sprache",false,false,"1");
    //$field->onchange="onchange_typ(this.form.typ.options[this.form.typ.selectedIndex].value);";
    $field->AddOptionsSimpleArray($sprachenOptions);
    $this->form->NewField($field);

    //$this->app->erp->GetSelect($versandart,$this->app->
    $field = new HTMLSelect("versandart",0);
    //    $field->onchange="versand(this.form.versandart.options[this.form.versandart.selectedIndex].value);";
    $field->AddOptionsSimpleArray($versandart);
    $this->form->NewField($field);
    if(!empty($overwriteZahlungsweise)){
      $this->form->HTMLList['versandart']->htmlvalue = $overwriteVersandart;
      $this->form->HTMLList['versandart']->dbvalue = $overwriteVersandart;
    }

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

    $zahlungsweisenmodule = $this->app->DB->SelectArr("SELECT id, modul, type FROM zahlungsweisen WHERE verhalten = 'rechnung'");
    for($iz=0;$iz<count($zahlungsweisenmodule);$iz++) $cmdcheck[] = " cmd=='".$zahlungsweisenmodule[$iz]['type']."'";

    $field = new HTMLSelect("zahlungsweise",0);
    if(count($zahlungsweisenmodule) > 0)
      $field->onchange="var cmd = this.form.zahlungsweise.options[this.form.zahlungsweise.selectedIndex].value; if(".implode(' || ',$cmdcheck).") cmd='rechnung';  aktion_buchen(cmd);";
    else
      $field->onchange="var cmd = this.form.zahlungsweise.options[this.form.zahlungsweise.selectedIndex].value;  aktion_buchen(cmd);";
    $field->AddOptionsSimpleArray($zahlungsweise);
    $this->form->NewField($field);
    if(!empty($overwriteZahlungsweise)){
      $this->form->HTMLList['zahlungsweise']->htmlvalue = $overwriteZahlungsweise;
      $this->form->HTMLList['zahlungsweise']->dbvalue = $overwriteZahlungsweise;
   }

    $field = new HTMLSelect("zahlungsstatus",0);
    $field->AddOptionsSimpleArray($zahlungsstatus);
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
        $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM auftrag WHERE id = '$id' LIMIT 1");
      }else{
        $schreibschutz = 0;
      }
      $this->app->YUI->BundeslaenderSelect('EPROO_SELECT_BUNDESSTAAT', 'land', 'bundesstaat',$this->app->DB->Select("SELECT land FROM auftrag WHERE id = '$id' LIMIT 1"),$this->app->DB->Select("SELECT bundesstaat FROM auftrag WHERE id = '$id' LIMIT 1"),$schreibschutz?true:false);
      $this->app->YUI->BundeslaenderSelect('EPROO_SELECT_LIEFERBUNDESSTAAT', 'lieferland', 'lieferbundesstaat',$this->app->DB->Select("SELECT lieferland FROM auftrag WHERE id = '$id' LIMIT 1"),$this->app->DB->Select("SELECT lieferbundesstaat FROM auftrag WHERE id = '$id' LIMIT 1"),$schreibschutz?true:false);
    }else{
      $this->app->Tpl->Set('VORBUNDESSTAAT','<!--');
      $this->app->Tpl->Set('NACHBUNDESSTAAT','-->');
    }
    
    $modus = 'anzeigen';
    $this->app->erp->RunHook('auftrageditdetail', 2, $id, $modus);
  }

  function ReplaceGruppe($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceGruppe($db,$value,$fromform);
  }

  function ReplaceAngebot($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceAngebot($db,$value,$fromform);
  }


  function ReplaceLieferant($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceLieferantennummer($db,$value,$fromform);
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  function ReplaceKunde($db,$value,$fromform)
  {
    if($db && $this->app->Secure->GetPOST('lieferantenauftrag'))
    {
      return $this->app->erp->ReplaceLieferantennummer(1,$this->app->Secure->GetPOST('lieferant'),1);
    }
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
  
  function ReplaceBetrag($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceBetrag($db,$value,$fromform);
  }

  function ReplaceDecimal($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDecimal($db,$value,$fromform);
  }
}

