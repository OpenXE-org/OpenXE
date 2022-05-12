<?php
include ("_gen/widget.gen.inhalt.php");

class WidgetInhalt extends WidgetGenInhalt
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

    $this->app->YUI->DatePicker("datum");
    $this->app->YUI->DatePicker("sichtbarbis");

    $this->app->YUI->AutoComplete("shop","shopname");

    $this->form->ReplaceFunction("shop",$this,"ReplaceShopname");



    $this->form->ReplaceFunction("datum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("sichtbarbis",$this,"ReplaceDatum");


    //firma
    $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
    $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
    $this->form->NewField($field);


		$id = $this->app->Secure->GetGET("id");
		$shop = $this->app->DB->Select("SELECT shop FROM inhalt WHERE id='$id'");

    $navigation = $this->app->erp->GetNavigationSelect($shop);
    $field = new HTMLSelect("navigation",0);
    //$field->onchange="aktion_buchen(this.form.zahlungsweiselieferant.options[this.form.zahlungsweise.selectedIndex].value);";
    $field->AddOptionsAsocSimpleArray($navigation);
    $this->form->NewField($field);


    $this->app->YUI->AutoComplete(PROJEKTAUTO,"projekt",array('name','abkuerzung'),"abkuerzung");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");


    //$this->app->Tpl->Set(DATUM_BUCHUNG,
    //    "<img src=\"./themes/[THEME]/images/kalender.png\" onclick=\"displayCalendar(document.forms[0].datum,'dd.mm.yyyy',this)\">");

  }
  function ReplaceProjekt($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$id' LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$value' LIMIT 1");
    }

    // wenn ziel datenbank
    if($db)
    { 
      return $id;
    }
    // wenn ziel formular
    else
    { 
      return $abkuerzung;
    }
  }

  function ReplaceDecimal($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden

    return str_replace(",",".",$value);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }



  function ReplaceAdresse($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM adresse WHERE name='$value' AND geloescht=0 LIMIT 1");
    }

    // wenn ziel datenbank
    if($db)
    { 
      return $id;
    }
    // wenn ziel formular
    else
    { 
      return $abkuerzung;
    }
  }

  public function Table()
  {

    $table = new EasyTable($this->app);  
    $this->app->Tpl->Set(INHALT,"");
    $this->app->Tpl->Set(SUBSUBHEADING,"Inhalt");
    $table->Query("SELECT g.inhaltstyp, if(g.inhalt='',g.title,g.inhalt) as inhalt, s.bezeichnung as shop, g.sprache, if(g.aktiv,'online','') as aktiv, g.id FROM inhalt g, shopexport s WHERE s.firma='".$this->app->User->GetFirma()."'

    AND s.id=g.shop");
    $table->DisplayNew(INHALT, "<a href=\"index.php?module=inhalt&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.svg\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=inhalt&action=delete&id=%value%';\">
          <img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\"></a>
        ");

    /*$this->app->Tpl->Set(EXTEND,"<input type=\"button\" value=\"Inhalt exportieren\" onclick=\"window.location.href='index.php?module=inhalt&action=exportieren'\">");
    $this->app->Tpl->Parse($this->parsetarget,"rahmen70.tpl");

    $this->app->Tpl->Set(INHALT,"");
    $this->app->Tpl->Set(SUBSUBHEADING,"Inhalt exportiert");
    $table->Query("SELECT a.name, betrag, auswahl as typ, grund FROM inhalt, adresse a WHERE geschaeftsbrief_vorlagen.adresse = a.id AND geschaeftsbrief_vorlagen.exportiert=1");
    $table->DisplayNew(INHALT, "Grund","noAction");
    */
    $this->app->Tpl->Set(EXTEND,"");
    $this->app->Tpl->Parse($this->parsetarget,"rahmen70.tpl");

  }


	function ReplaceShopname($db,$value)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(is_numeric($value)) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT bezeichnung FROM shopexport WHERE id='$id' LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM shopexport WHERE bezeichnung='$value' LIMIT 1");
    }

    // wenn ziel datenbank
    if($db)
    {
      return $id;
    }
    // wenn ziel formular
    else
    {
      return $abkuerzung;
    }
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
