<?php
include ("_gen/widget.gen.auftrag_position.php");

class WidgetAuftrag_position extends WidgetGenAuftrag_position 
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
    $id = (int)$this->app->Secure->GetGET('id');
    $this->app->YUI->AutoComplete("einheit","artikeleinheit");

    $this->app->erp->AnzeigeFreifelderPositionen($this->form);

    //$this->app->YUI->AutoComplete(AUTO,"artikel",array('nummer','name_de','warengruppe'),"nummer");
    $this->form->ReplaceFunction("lieferdatum",$this,"ReplaceDatum");
    $this->app->YUI->AutoComplete("zolltarifnummer","zolltarifnummer",1);
    $this->app->YUI->AutoComplete("steuersatz","steuersatz",1);
    $this->app->YUI->AutoComplete("kostenstelle","kostenstelle",1);

    $position = $this->app->DB->SelectRow(
      sprintf(
        'SELECT p.abkuerzung, ap.artikel
        FROM `auftrag_position` AS `ap` 
        LEFT JOIN `auftrag` AS `a` ON a.id=ap.auftrag 
        LEFT JOIN `projekt` AS `p` ON p.id=a.projekt 
        WHERE ap.id = %d 
        LIMIT 1',
        $id
      )
    );
    $projektabkuerzung = $position['abkuerzung'];
    $articleId = $position['artikel'];
    $this->app->YUI->AutoComplete('erloese','sachkonto',1,"&sid=".$projektabkuerzung);

    $this->app->YUI->DatePicker("lieferdatum");
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


    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("beschreibung","belege");
      $this->app->YUI->CkEditor("bemerkung","basic");
    }
    
    if(!$this->app->erp->RechteVorhanden('auftrag','steuer'))
    {
      $this->app->Tpl->Set('VORSTEUER','<!--');
      $this->app->Tpl->Set('NACHSTEUER','-->');
    }

    if(!$this->app->erp->ModulVorhanden('formeln'))
    {
      $this->app->Tpl->Set('VORFORMELN',"<!--");
      $this->app->Tpl->Set('NACHFORMELN',"-->");
    }elseif(!$this->app->erp->RechteVorhanden('auftrag', 'formeln'))
    {
      $this->app->Tpl->Set('FORMELNDISPLAY', ' style="display:none" ');
    }
    
    $this->app->erp->ArtikelFreifeldBezeichnungen();

    $field = new HTMLInput("nummer","text","",50);
    $field->readonly="readonly";

    $this->form->NewField($field);

    $this->app->YUI->WaehrungsumrechnungTabelle('WAEHRUNGSBUTTON','WAEHRUNGSTABELLE');
    $this->app->erp->RunHook("auftrag_position_widget", 0);
    if($this->app->Secure->GetPOST('speichern') != '') {
      $this->app->DB->Update(
        sprintf(
          'UPDATE `artikel` SET `laststorage_changed` = NOW() WHERE `id` = %d',
          $articleId
        )
      );
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
    $table->Query("SELECT auftrag, id FROM auftrag_position");
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
