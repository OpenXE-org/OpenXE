<?php
include ("_gen/widget.gen.verbindlichkeit.php");

class WidgetVerbindlichkeit extends WidgetGenVerbindlichkeit 
{
  /** @var Application $pp */
  private $app;

  /**
   * WidgetVerbindlichkeit constructor.
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
    $id = $this->app->Secure->GetGET('id');
    if($id > 0 && $this->app->Secure->GetPOST('speichern')!=''){
      $before = $this->app->DB->SelectRow(
        sprintf('SELECT `schreibschutz` FROM `verbindlichkeit` WHERE `id` = %d', $id)
      );
      if(!empty($before['schreibschutz'])) {
        $freigabe = (int)$this->app->Secure->GetPOST('freigabe');
        $rechnungsfreigabe = (int)$this->app->Secure->GetPOST('rechnungsfreigabe');
        $klaerfall = (int)$this->app->Secure->GetPOST('klaerfall');
        $klaergrund = $this->app->Secure->GetPOST('klaergrund');
        $this->app->DB->Update(
          sprintf(
            "UPDATE `verbindlichkeit` 
            SET `freigabe` = %d, `rechnungsfreigabe` = %d, `klaerfall` = %d, `klaergrund` = '%s' 
            WHERE `id` = %d ",
            $freigabe, $rechnungsfreigabe, $klaerfall, $klaergrund, $id
          )
        );
        $this->app->Location->execute(
          'index.php?module=verbindlichkeit&action=edit&id='.$id
          .'&msg='.$this->app->erp->base64_url_encode('<div class="info">Die Daten wurden gespeichert!</div>'));
      }
    }

    $this->form->ReplaceFunction("adresse",$this,"ReplaceLieferant");
    $this->form->ReplaceFunction("zahlbarbis",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("rechnungsdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("eingangsdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("skontobis",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("bezahltam",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("betrag",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("skonto",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("frachtkosten",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("summenormal",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("summeermaessigt",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("bestellung",$this,"ReplaceBestellung");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->form->ReplaceFunction("auftrag",$this,"ReplaceAuftrag");

    $this->app->YUI->AutoComplete("waehrung","waehrung");

    $this->app->YUI->AutoSaveUserParameter('projekt','teilprojekt_filter');
    $this->app->YUI->AutoComplete('teilprojekt','arbeitspaket');

    $this->form->ReplaceFunction("teilprojekt",$this,"ReplaceArbeitspaket");

    for($i=1;$i<=15;$i++)
    {
      $this->form->ReplaceFunction("bestellung".$i,$this,"ReplaceBestellung");
      $this->app->YUI->AutoComplete("bestellung".$i,"bestellung",1);
      $this->form->ReplaceFunction("bestellung".$i."betrag",$this,"ReplaceDecimal");
      $this->app->YUI->AutoComplete("bestellung".$i."projekt","projektname",1);
      $this->form->ReplaceFunction("bestellung".$i."projekt",$this,"ReplaceProjekt");
      $this->app->YUI->AutoComplete("bestellung".$i."kostenstelle","kostenstelle",1);

      $this->form->ReplaceFunction("bestellung".$i."auftrag",$this,"ReplaceAuftrag");
      $this->app->YUI->AutoComplete("bestellung".$i."auftrag","auftragihrebestellnummer",1);
    }

    $this->app->YUI->AutoComplete("adresse","lieferant");
    $this->app->YUI->AutoComplete("kunde","kunde");
    $this->app->YUI->AutoComplete("mitarbeiter","mitarbeiter");
    $this->app->YUI->AutoComplete("sonstige","adresse");
    $this->app->YUI->AutoComplete("kostenstelle","kostenstelle",1);
    $this->app->YUI->AutoComplete("bestellung","bestellung",1);
    $this->app->YUI->AutoComplete("projekt","projektname",1);

    //$this->app->YUI->AutoComplete("sachkonto","sachkonto",1);

    $this->app->YUI->AutoComplete("buha_konto1","sachkonto",1);
    $this->app->YUI->AutoComplete("buha_konto2","sachkonto",1);
    $this->app->YUI->AutoComplete("buha_konto3","sachkonto",1);
    $this->app->YUI->AutoComplete("buha_konto4","sachkonto",1);
    $this->app->YUI->AutoComplete("buha_konto5","sachkonto",1);

    $this->form->ReplaceFunction("buha_betrag1",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("buha_betrag2",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("buha_betrag3",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("buha_betrag4",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("buha_betrag5",$this,"ReplaceDecimal");
    
    
    $this->form->ReplaceFunction("ustnormal",$this,"ReplaceSteuersatzNormal");
    $this->form->ReplaceFunction("ustermaessigt",$this,"ReplaceSteuersatzErmaessigt");
    $this->form->ReplaceFunction("uststuer3",$this,"ReplaceSteuersatz");
    $this->form->ReplaceFunction("uststuer4",$this,"ReplaceSteuersatz");
    $this->form->ReplaceFunction("summesatz3",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("summesatz4",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("betragbezahlt",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction('skonto_erhalten',$this,'ReplaceDecimal');

    $this->app->YUI->AutoComplete("auftrag","auftragihrebestellnummer",1);

    $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
    $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
    $this->form->NewField($field);

    $this->app->YUI->DatePicker("zahlbarbis");
    $this->app->YUI->DatePicker("skontobis");
    $this->app->YUI->DatePicker("rechnungsdatum");
    $this->app->YUI->DatePicker("eingangsdatum");
    $this->app->YUI->DatePicker("bezahltam");
    $this->app->YUI->CkEditor("internebemerkung","internal");


    $field = new HTMLSelect("art",0);
    $field->onchange="onchange_art(this.form.art.options[this.form.art.selectedIndex].value);";
    $field->AddOptionsSimpleArray(array('lieferant'=>'Lieferant','kunde'=>'Kunde','sonstige'=>'Sonstige'));
    $this->form->NewField($field);
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->RunHook('liability_widget',1, $id);
    $zahlungsweise = $this->app->erp->GetZahlungsweise('verbindlichkeit', $id);

    $field = new HTMLSelect("zahlungsweise",0);
    $field->onchange="aktion_buchen(this.form.zahlungsweise.options[this.form.zahlungsweise.selectedIndex].value);";
    $field->AddOptionsSimpleArray($zahlungsweise);
    $this->form->NewField($field);

    $weiteresteuer1 = $this->app->erp->GetKonfiguration('verbindlichkeit_steuertext1');
    if(!$weiteresteuer1)$weiteresteuer1 = 'Weitere Steuer';
    $weiteresteuer2 = $this->app->erp->GetKonfiguration('verbindlichkeit_steuertext2');
    if(!$weiteresteuer2)$weiteresteuer2 = 'Weitere Steuer';
    $this->app->Tpl->Set('WEITERESTEUER1',rtrim($weiteresteuer1,':').':');
    $this->app->Tpl->Set('WEITERESTEUER2',rtrim($weiteresteuer2,':').':');

    $action = $this->app->Secure->GetGET("action");
    $speichern = $this->app->Secure->GetPOST("speichern");
    if($action=="edit" && $speichern!="")
    {
        $this->app->erp->VerbindlichkeitProtokoll($id,"Verbindlichkeit gespeichert");
    }

    $this->app->Tpl->Parse("OCRDIALOGE","verbindlichkeit_ocrdialoge.tpl");
  }

  function ReplaceDecimal($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    return $this->app->erp->ReplaceMengeBetrag($db,$value,$fromform);
    return str_replace(",",".",$value);
  }

  function ReplaceAuftrag($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceAuftrag($db,$value,$fromform);
  }

  function ReplaceArbeitspaket($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceArbeitspaket($db,$value,$fromform);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(strpos($value,'-') > 0) $dbformat = 1;

    // wenn ziel datenbank
    if($db)
    { 
      if($dbformat) return $value;
      else return $this->app->String->Convert($value,"%1.%2.%3","%3-%2-%1");
    }
    // wenn ziel formular
    else
    { 
      if($dbformat) return $this->app->String->Convert($value,"%1-%2-%3","%3.%2.%1");
      else return $value;
    }
  }

  function ReplaceBestellung($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceBestellung($db,$value,$fromform);
  }


  function ReplaceLieferant($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceLieferant($db,$value,$fromform);
  }

  function ReplaceKunde($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceKunde($db,$value,$fromform);
  }


  function ReplaceMitarbeiter($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceMitarbeiter($db,$value,$fromform);
  }

  function ReplaceAdresse($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceAdresse($db,$value,$fromform);
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
      if($value < 0 || is_null($value))return "";
      return number_format($value,2,',','');
    }
  }

  function ReplaceSteuersatzNormal($db,$value,$fromform)
  {
    if($db)
    {
      if($value === "" || $value === null)return -1;
      return str_replace(',','.', $value);
    }else{
      if($value < 0 || is_null($value))return str_replace('.',',',$this->app->erp->GetKonfiguration('verbindlichkeit_steuersatznormal')?$this->app->erp->GetKonfiguration('verbindlichkeit_steuersatznormal'):$this->app->erp->Firmendaten('steuersatz_normal'));
      return number_format($value,2,',','');
    }
  }
  
  function ReplaceSteuersatzErmaessigt($db,$value,$fromform)
  {
    if($db)
    {
      if($value === "" || $value === null)return -1;
      return str_replace(',','.', $value);
    }else{
      if($value < 0 || is_null($value))return str_replace('.',',',$this->app->erp->GetKonfiguration('verbindlichkeit_steuersatzermaessigt')?$this->app->erp->GetKonfiguration('verbindlichkeit_steuersatzermaessigt'):$this->app->erp->Firmendaten('steuersatz_ermaessigt'));
      return number_format($value,2,',','');
    }
  }

  public function Table()
  {
    $table = new EasyTable($this->app);  
    $this->app->Tpl->Set($this->parsetarget,"<form action=\"\" method=\"post\">");
    $this->app->Tpl->Set('SUBSUBHEADING',"");
    //    $table->Query("SELECT a.name, verbindlichkeit.betrag, verbindlichkeit.rechnung, DATE_FORMAT(verbindlichkeit.skontobis,'%d.%m.%Y') as bis,verbindlichkeit.id FROM verbindlichkeit, adresse a WHERE verbindlichkeit.adresse = a.id AND verbindlichkeit.bezahlt!=1 AND verbindlichkeit.skontobis <= NOW() AND verbindlichkeit.status!='bezahlt' AND verbindlichkeit.skonto > 0 order by verbindlichkeit.skontobis");

    $table->Query("SELECT 
        CONCAT('<input type=\"checkbox\" ',if(verbindlichkeit.zahlbarbis<=NOW(),'checked',''),if(verbindlichkeit.skontobis>=NOW(),'checked','')
          ,' name=\"verbindlichkeit[]\" value=\"',verbindlichkeit.id,'\">') as auswahl, verbindlichkeit.id as 'nr.', a.name as lieferant, 
        if(a.swift='','fehlt - bitte nachtragen',a.swift) as BIC, 
        if(a.iban='','fehlt - bitte nachtragen',a.iban) as IBAN, 
        betrag,verbindlichkeit.betrag, verbindlichkeit.rechnung, 
        if(verbindlichkeit.skontobis='0000-00-00','-',if(verbindlichkeit.skontobis >=NOW(),
            CONCAT('<font color=red>',DATE_FORMAT(verbindlichkeit.skontobis,'%d.%m.%Y'),'</font>'),DATE_FORMAT(verbindlichkeit.skontobis,'%d.%m.%Y'))) as skonto_bis,
        if(verbindlichkeit.zahlbarbis='0000-00-00','-',DATE_FORMAT(verbindlichkeit.zahlbarbis,'%d.%m.%Y')) as zahlbar_bis,
        if(verbindlichkeit.skonto > 0,CONCAT(verbindlichkeit.skonto,' %'),'-') as skonto,	
        if(verbindlichkeit.status='','offen',verbindlichkeit.status) as status,
        verbindlichkeit.id FROM verbindlichkeit LEFT JOIN adresse a ON verbindlichkeit.adresse = a.id 
        WHERE verbindlichkeit.status!='bezahlt' Order by verbindlichkeit.skontobis, verbindlichkeit.zahlbarbis ");


    $table->DisplayNew('INHALT', "<a href=\"index.php?module=verbindlichkeit&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.svg\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=verbindlichkeit&action=delete&id=%value%';\">
        <img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\"></a>
        <a onclick=\"if(!confirm('Wirklich als bezahlt markieren?')) return false; else window.location.href='index.php?module=verbindlichkeit&action=bezahlt&id=%value%';\">
        <img src=\"./themes/[THEME]/images/ack.png\" border=\"0\"></a>
        ");


    $this->app->Tpl->Parse($this->parsetarget,"rahmen70_ohne_form.tpl");

    $this->app->Tpl->Set('SUBSUBHEADING',"");
    $this->app->Tpl->Set('INHALT',"<center>Auswahl Konto:&nbsp;
        <select name=\"konto\">".$this->app->erp->GetSelectBICKonto()."</select>&nbsp;<input type=\"submit\" name=\"submit\" value=\"Sammel&uuml;berweisung herunterladen und Verbindlichkeit als bezahlt markieren\"></center></form>");
    $this->app->Tpl->Parse($this->parsetarget,"rahmen70_ohne_form.tpl");
  }



  public function Search()
  {
    //$this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }


}
?>
