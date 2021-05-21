<?php
include '_gen/widget.gen.wiedervorlage.php';

class WidgetWiedervorlage extends WidgetGenWiedervorlage 
{
  private $app;
  public function __construct($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::__construct($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {

    $action = $this->app->Secure->GetGET("action");
    $submit = $this->app->Secure->POST['submit'];

    $this->app->YUI->DatePicker("datum_angelegt");
    $this->app->YUI->DatePicker("datum_erinnerung");
    $this->app->YUI->TimePicker("zeit_erinnerung");
    $this->app->YUI->TimePicker("zeit_angelegt");
    
    
    $this->app->YUI->AutoComplete("adresse","adresse");
    $this->app->YUI->AutoComplete("bearbeiter","mitarbeiter");
    $this->app->YUI->AutoComplete("adresse_mitarbeiter","mitarbeiter");
    $this->app->YUI->AutoComplete("stages","wiedervorlage_stages");
    $this->app->YUI->AutoComplete("projekt","projektname");
    $this->form->ReplaceFunction("datum_angelegt",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("datum_erinnerung",$this,"ReplaceDatum");    
    $this->form->ReplaceFunction("bearbeiter",$this,"ReplaceMitarbeiter");
    $this->form->ReplaceFunction("adresse",$this,"ReplaceAdresse");
    $this->form->ReplaceFunction("adresse_mitarbeiter",$this,"ReplaceMitarbeiter");
    $this->form->ReplaceFunction("stages",$this,"ReplaceWiedervorlageStages");
    $this->form->ReplaceFunction("betrag",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjektName");

    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("beschreibung","belege");
    }
    
    if($action=="create" || $action=="addwiedervorlage") 
    {
      //$this->app->Secure->POST["bearbeiter"]=$this->app->User->GetAdresse();
      $field = new HTMLInput("bearbeiter","text",$this->app->User->GetAdresse());
      $this->form->NewField($field);
      $field = new HTMLInput("datum_erinnerung","text",date("Y-m-d",strtotime ("+1 day")));
      $this->form->NewField($field);
      $field = new HTMLInput("datum_angelegt","hidden",date("Y-m-d"));
      $this->form->NewField($field);
      $field = new HTMLInput("zeit_angelegt","hidden",date("H:i"));
      $this->form->NewField($field);
      $field = new HTMLInput("adresse_mitarbeiter","text",$this->app->User->GetAdresse());
      $this->form->NewField($field);
      //$this->app->Tpl->Set(ADRESSE,$this->app->User->GetName());
    }
    
    if(($action=='create' || $action=='addwiedervorlage')&& $submit!='')
    { 

      $this->app->YUI->Message('info','Es wurde eine neue Wiedervorlage erstellt.');

      
    } 
    
    
    /*



    if($action=="create" || $action=="addwiedervorlage")
    {
      // liste zuweisen
      $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
      $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
      $this->form->NewField($field);

      if($this->app->Secure->POST["projekt"]=="")
      {
        $this->app->erp->LogFile("Standard Projekt laden");
        $projekt = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$this->app->User->GetFirma()."' LIMIT 1");

        $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
        if($projekt_bevorzugt=="1")
        { 
          $projekt = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
        }
        $field = new HTMLInput("projekt","text",$projekt);
        $field->value=$projekt;
        $this->form->NewField($field);
      }

      $zahlungsweise = $this->app->erp->GetZahlungsweise();
      $field = new HTMLSelect("zahlungsweise",0);
      if($this->app->Secure->POST["zahlungsweise"]=="")
        $field->value=$this->app->erp->StandardZahlungsweise($projekt);
      //$field->onchange="aktion_buchen(this.form.zahlungsweise.options[this.form.zahlungsweise.selectedIndex].value);";
      $field->AddOptionsSimpleArray($zahlungsweise);
      $this->form->NewField($field);
    }
    else {

      $zahlungsweise = $this->app->erp->GetZahlungsweise();
      $field = new HTMLSelect("zahlungsweise",0);
      //$field->onchange="aktion_buchen(this.form.zahlungsweise.options[this.form.zahlungsweise.selectedIndex].value);";
      $field->AddOptionsSimpleArray($zahlungsweise);
      $this->form->NewField($field);

    }
    $field = new HTMLInput("land","hidden","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_land","hidden","");
    $this->form->NewField($field);


    $versandart = $this->app->erp->GetVersandartAuftrag();
    $field = new HTMLSelect("versandart",0);
    $field->AddOptionsSimpleArray($versandart);
    $this->form->NewField($field);

    $verrechnungskontoreisekosten = $this->app->erp->GetVerrechnungskontenReisekosten();
    $field = new HTMLSelect("verrechnungskontoreisekosten",0);
    $field->AddOptionsAsocSimpleArray($verrechnungskontoreisekosten);
    $this->form->NewField($field);


    $field = new HTMLSelect("zahlungsweiselieferant",0);
    //$field->onchange="aktion_buchen(this.form.zahlungsweiselieferant.options[this.form.zahlungsweise.selectedIndex].value);";
    $field->AddOptionsSimpleArray($zahlungsweise);
    $this->form->NewField($field);

    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");

    $field = new HTMLCheckbox("abweichende_rechnungsadresse","","","1");
    $field->onclick="abweichend(this.form.abweichende_rechnungsadresse.value);";
    $this->form->NewField($field);



    $typOptions = $this->app->erp->GetTypSelect();
    $field = new HTMLSelect("typ",0);
    $field->onchange="onchange_typ(this.form.typ.options[this.form.typ.selectedIndex].value);";
    $field->AddOptionsSimpleArray($typOptions);
    $this->form->NewField($field);

    /*
       $vorname = $this->app->Secure->GetPOST('vorname');
       if($vorname!='') {
       $id = $this->app->Secure->GetGET('id');

       if(!($id!='' && is_numeric($id)))
       $id = $this->app->DB->Select('SELECT id FROM adresse ORDER BY id DESC LIMIT 1');

       if(is_numeric($id) && $id>0)
       $this->app->DB->Update("UPDATE adresse SET vorname='$vorname' WHERE id='$id' LIMIT 1");

       }
     */
/*
    $field = new HTMLInput("vorname","hidden","");
    $this->form->NewField($field);

      if($this->app->erp->Firmendaten("adressefreifeld1")!="")
        $this->app->Tpl->Set(FREIFELD1BEZEICHNUNG,$this->app->erp->Firmendaten("adressefreifeld1"));
      else
        $this->app->Tpl->Set(FREIFELD1BEZEICHNUNG,"Freifeld 1");
      if($this->app->erp->Firmendaten("adressefreifeld2")!="")
        $this->app->Tpl->Set(FREIFELD2BEZEICHNUNG,$this->app->erp->Firmendaten("adressefreifeld2"));
      else
        $this->app->Tpl->Set(FREIFELD2BEZEICHNUNG,"Freifeld 2");
      if($this->app->erp->Firmendaten("adressefreifeld3")!="")
        $this->app->Tpl->Set(FREIFELD3BEZEICHNUNG,$this->app->erp->Firmendaten("adressefreifeld3"));
      else
        $this->app->Tpl->Set(FREIFELD3BEZEICHNUNG,"Freifeld 3");
      if($this->app->erp->Firmendaten("adressefreifeld4")!="")
        $this->app->Tpl->Set(FREIFELD4BEZEICHNUNG,$this->app->erp->Firmendaten("adressefreifeld4"));
      else
        $this->app->Tpl->Set(FREIFELD4BEZEICHNUNG,"Freifeld 4");
      if($this->app->erp->Firmendaten("adressefreifeld5")!="")
        $this->app->Tpl->Set(FREIFELD5BEZEICHNUNG,$this->app->erp->Firmendaten("adressefreifeld5"));
      else
        $this->app->Tpl->Set(FREIFELD5BEZEICHNUNG,"Freifeld 5");
      if($this->app->erp->Firmendaten("adressefreifeld6")!="")
        $this->app->Tpl->Set(FREIFELD6BEZEICHNUNG,$this->app->erp->Firmendaten("adressefreifeld6"));
      else
        $this->app->Tpl->Set(FREIFELD6BEZEICHNUNG,"Freifeld 6");
      if($this->app->erp->Firmendaten("adressefreifeld7")!="")
        $this->app->Tpl->Set(FREIFELD7BEZEICHNUNG,$this->app->erp->Firmendaten("adressefreifeld7"));
      else
        $this->app->Tpl->Set(FREIFELD7BEZEICHNUNG,"Freifeld 7");
      if($this->app->erp->Firmendaten("adressefreifeld8")!="")
        $this->app->Tpl->Set(FREIFELD8BEZEICHNUNG,$this->app->erp->Firmendaten("adressefreifeld8"));
      else
        $this->app->Tpl->Set(FREIFELD8BEZEICHNUNG,"Freifeld 8");
      if($this->app->erp->Firmendaten("adressefreifeld9")!="")
        $this->app->Tpl->Set(FREIFELD9BEZEICHNUNG,$this->app->erp->Firmendaten("adressefreifeld9"));
      else
        $this->app->Tpl->Set(FREIFELD9BEZEICHNUNG,"Freifeld 9");
      if($this->app->erp->Firmendaten("adressefreifeld10")!="")
        $this->app->Tpl->Set(FREIFELD10BEZEICHNUNG,$this->app->erp->Firmendaten("adressefreifeld10"));
      else
        $this->app->Tpl->Set(FREIFELD10BEZEICHNUNG,"Freifeld 10");

*/




    /*
       $id = $this->app->Secure->GetGET('id');
       if(is_numeric($id) && $id>0) {
       $vorname = $this->app->DB->Select("SELECT vorname FROM adresse WHERE id='$id' LIMIT 1");
       $typ = $this->app->DB->Select("SELECT typ FROM adresse WHERE id='$id' LIMIT 1");
       $this->app->Tpl->Set('ADRESSEVORNAME', $vorname);
       $this->app->Tpl->Set('ADRESSETYP', $typ);
       }
     */
  }

  function ReplaceKunde($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceKunde($db,$value,$fromform);
  }


  function ReplaceProjektName($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjektName($db,$value,$fromform);
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }
  
  function ReplaceMitarbeiter($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceMitarbeiter($db, $value, $fromform);
  }

  function ReplaceWiedervorlageStages($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceWiedervorlageStages($db, $value, $fromform);
  }


  function ReplaceAdresse($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceAdresse($db, $value, $fromform);
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
