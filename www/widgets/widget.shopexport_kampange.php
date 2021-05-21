<?php
include ("_gen/widget.gen.shopexport_kampange.php");

class WidgetShopexport_kampange extends WidgetGenShopexport_kampange 
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
    $this->app->Tpl->Set('DATUM_VON',
	"<img src=\"./themes/[THEME]/images/kalender.png\" onclick=\"displayCalendar(document.forms[0].von,'dd.mm.yyyy',this)\">");
     $this->app->Tpl->Set('DATUM_BIS',
	"<img src=\"./themes/[THEME]/images/kalender.png\" onclick=\"displayCalendar(document.forms[0].bis,'dd.mm.yyyy',this)\">");

    $this->form->ReplaceFunction("von",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("bis",$this,"ReplaceDatum");
    $this->app->YUI->AutoComplete('ARTIKELAUTO',"artikel",array('nummer','name_de','warengruppe'),"name_de");
    $this->form->ReplaceFunction("artikel",$this,"ReplaceArtikel");


    //BANNER
    function array_insert($array, $index, $value)
    {
      return array_merge(array_slice($array, 0, $index), $value, array_slice($array, $index));
    } 

   //$this->app->erp->GetSelect($versandart,$this->app->
    $banner = $this->app->DB->SelectArr("SELECT ds.datei as schluessel, d.titel as wert FROM datei_stichwoerter ds, datei d WHERE d.id=ds.datei AND subjekt='Banner' AND d.geloescht=0 AND d.firma='".$this->app->User->GetFirma()."'"); 
    $banner = array_insert($banner,0, array(""));
    $field = new HTMLSelect("banner",0);
    $field->AddOptionsDimensionalArray($banner);
    $this->form->NewField($field);


    $unterbanner = $this->app->DB->SelectArr("SELECT ds.datei as schluessel, d.titel as wert FROM datei_stichwoerter ds, datei d WHERE d.id=ds.datei AND subjekt='Unterbanner' AND d.geloescht=0 AND d.firma='".$this->app->User->GetFirma()."'"); 
    $unterbanner = array_insert($unterbanner,0, array(""));
    $field = new HTMLSelect("unterbanner",0);
    $field->AddOptionsDimensionalArray($unterbanner);
    $this->form->NewField($field);

/*

    $this->form->ReplaceFunction("adresse",$this,"ReplaceLieferant");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->form->ReplaceFunction("gueltig_bis",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("preis_anfrage_vom",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("datum_lagerlieferant",$this,"ReplaceDatum");

    $this->app->YUI->AutoComplete(LIEFERANTAUTO,"adresse",array('name'),"name","lieferant");
    $this->app->YUI->AutoComplete(PROJEKTAUTO,"projekt",array('name','abkuerzung'),"abkuerzung");

    $action = $this->app->Secure->GetGET("action");
    if($action=="einkauf")
    { 
      // liste zuweisen
      $pid = $this->app->Secure->GetGET("id");
      $this->app->Secure->POST["artikel"]=$pid;
      $field = new HTMLInput("artikel","hidden",$pid);
      $this->form->NewField($field);
    }

    $this->app->Tpl->Set(DATUM_PREISANFRAGE,
	"<img src=\"./themes/[THEME]/images/kalender.png\" onclick=\"displayCalendar(document.forms[1].preis_anfrage_vom,'dd.mm.yyyy',this)\">");
    
    $this->app->Tpl->Set(DATUM_GUELTIGBIS,
	"<img src=\"./themes/[THEME]/images/kalender.png\" onclick=\"displayCalendar(document.forms[1].gueltig_bis,'dd.mm.yyyy',this)\">");

    $this->app->Tpl->Set(DATUM_LAGER,
	"<img src=\"./themes/[THEME]/images/kalender.png\" onclick=\"displayCalendar(document.forms[1].datum_lagerlieferant,'dd.mm.yyyy',this)\">");

    $this->app->Tpl->Set(BRUTTOEINGABE,"Bruttoeingabe: <input type=\"text\" size=\"10\" name=\"brutto\" id=\"brutto\">&nbsp;
      <input type=\"button\" value=\"Brutto -> Netto\" 
      onclick=\"document.forms[1].preis.value=runde(document.forms[1].brutto.value/1.19,4)\">");
*/

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


  function ReplaceArtikel($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    // wenn es die db id ist
    $check =  $this->app->DB->Select("SELECT id FROM artikel WHERE name_de='$value' LIMIT 1");
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM artikel WHERE name_de='$value' LIMIT 1");
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

  function ReplaceLieferant($db,$value,$fromform)
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
    $table->Query("SELECT nummer, name_de as name,barcode, id FROM shopexport_kampange order by nummer");
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
